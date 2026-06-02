<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\PengajuanPerpindahanKelas;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferSiswaController extends Controller
{
    private const MIN_SISWA = 20;
    private const MAX_SISWA = 30;
    private const CLASS_ORDER = ['Tulip', 'Melati', 'Anggrek', 'Ros', 'Rose', 'Sakura', 'Mawar'];

    private function isSuperAdmin(): bool
    {
        return (bool) session('is_super_admin');
    }

    private function guruKelasIds(): array
    {
        if (!session('id_guru')) {
            return [];
        }

        $guru = Guru::with('kelasAmpuan')->find(session('id_guru'));
        $kelasPivot = $guru?->kelasAmpuan?->pluck('id_kelas')->toArray() ?? [];
        $kelasUtama = Kelas::where('id_guru', session('id_guru'))->pluck('id_kelas')->toArray();

        return array_values(array_unique(array_merge($kelasPivot, $kelasUtama)));
    }

    private function orderedKelasQuery()
    {
        $case = collect(self::CLASS_ORDER)
            ->map(fn ($name, $index) => "WHEN nama_kelas = '{$name}' THEN " . ($index + 1))
            ->implode(' ');

        return Kelas::orderByRaw("CASE {$case} ELSE 99 END")->orderBy('nama_kelas');
    }

    private function transferValidationMessage(Siswa $siswa, Kelas $kelasTujuan): ?string
    {
        $kelasAsal = $siswa->kelas;

        if (!$kelasAsal) {
            return 'Siswa belum memiliki kelas asal yang valid.';
        }

        if ((int) $siswa->id_kelas === (int) $kelasTujuan->id_kelas) {
            return 'Kelas tujuan tidak boleh sama dengan kelas asal.';
        }

        $jumlahKelasAsal = Siswa::where('id_kelas', $siswa->id_kelas)->count();
        if ($jumlahKelasAsal - 1 < self::MIN_SISWA) {
            return "Pengajuan tidak dapat diproses karena kelas {$kelasAsal->nama_kelas} akan kurang dari " . self::MIN_SISWA . ' siswa.';
        }

        $jumlahKelasTujuan = Siswa::where('id_kelas', $kelasTujuan->id_kelas)->count();
        if ($jumlahKelasTujuan >= self::MAX_SISWA) {
            return "Pengajuan tidak dapat diproses karena kelas {$kelasTujuan->nama_kelas} sudah mencapai batas " . self::MAX_SISWA . ' siswa.';
        }

        return null;
    }

    private function canGuruAccessPengajuan(PengajuanPerpindahanKelas $pengajuan): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $kelasGuru = $this->guruKelasIds();

        return (int) $pengajuan->id_guru_pengaju === (int) session('id_guru')
            || in_array($pengajuan->id_kelas_asal, $kelasGuru)
            || in_array($pengajuan->id_kelas_tujuan, $kelasGuru);
    }

    public function index()
    {
        $relations = ['siswa', 'kelasAsal', 'kelasTujuan', 'guruPengaju', 'guruPemroses'];
        $isSuperAdmin = $this->isSuperAdmin();
        $pendingPengajuan = collect();

        if ($isSuperAdmin) {
            $pendingPengajuan = PengajuanPerpindahanKelas::with($relations)
                ->where('status', 'menunggu')
                ->latest('tanggal_pengajuan')
                ->get();

            $kelas = $this->orderedKelasQuery()
                ->withCount('siswa')
                ->get();
        } else {
            $kelasGuru = $this->guruKelasIds();

            $kelas = $this->orderedKelasQuery()
                ->whereIn('id_kelas', $kelasGuru)
                ->with(['siswa' => function ($query) {
                    $query->select('nomor_induk_siswa', 'nama_siswa', 'jenis_kelamin', 'id_kelas')
                        ->orderBy('nama_siswa');
                }])
                ->withCount('siswa')
                ->get();
        }

        $riwayatQuery = PengajuanPerpindahanKelas::with($relations)->latest('tanggal_pengajuan');

        if (!$isSuperAdmin) {
            $kelasGuru = $this->guruKelasIds();
            $riwayatQuery->where(function ($query) use ($kelasGuru) {
                $query->where('id_guru_pengaju', session('id_guru'))
                    ->orWhereIn('id_kelas_asal', $kelasGuru)
                    ->orWhereIn('id_kelas_tujuan', $kelasGuru);
            });
        }

        $riwayatPengajuan = $riwayatQuery->get();

        return view('transfer-siswa.index', compact('kelas', 'pendingPengajuan', 'riwayatPengajuan', 'isSuperAdmin'));
    }

    public function transfer($nomor_induk_siswa)
    {
        if ($this->isSuperAdmin()) {
            return redirect()->route('transfer-siswa.index')
                ->with('error', 'Kepala sekolah / super admin memproses pengajuan, bukan membuat pengajuan perpindahan.');
        }

        $siswa = Siswa::with('kelas')->findOrFail($nomor_induk_siswa);
        $kelasGuru = $this->guruKelasIds();

        if (!in_array($siswa->id_kelas, $kelasGuru)) {
            return redirect()->route('transfer-siswa.index')
                ->with('error', 'Anda hanya bisa mengajukan perpindahan untuk siswa di kelas yang Anda pegang.');
        }

        $kelasSekarang = $siswa->kelas;
        $kelasAyo = $this->orderedKelasQuery()
            ->where('id_kelas', '!=', $siswa->id_kelas)
            ->withCount('siswa')
            ->get();

        $pengajuanAktif = PengajuanPerpindahanKelas::where('nomor_induk_siswa', $siswa->nomor_induk_siswa)
            ->where('status', 'menunggu')
            ->first();

        return view('transfer-siswa.transfer', compact('siswa', 'kelasSekarang', 'kelasAyo', 'pengajuanAktif'));
    }

    public function proses(Request $request, $nomor_induk_siswa)
    {
        if ($this->isSuperAdmin()) {
            return redirect()->route('transfer-siswa.index')->with('error', 'Super admin tidak membuat pengajuan perpindahan.');
        }

        $validated = $request->validate([
            'id_kelas_tujuan' => 'required|exists:kelas,id_kelas',
            'alasan' => 'required|string|min:10|max:1000',
        ]);

        $siswa = Siswa::with('kelas')->findOrFail($nomor_induk_siswa);
        $kelasGuru = $this->guruKelasIds();

        if (!in_array($siswa->id_kelas, $kelasGuru)) {
            return back()->with('error', 'Siswa harus berasal dari kelas yang Anda pegang.')->withInput();
        }

        $pengajuanAktif = PengajuanPerpindahanKelas::where('nomor_induk_siswa', $siswa->nomor_induk_siswa)
            ->where('status', 'menunggu')
            ->exists();

        if ($pengajuanAktif) {
            return back()->with('error', 'Siswa ini masih memiliki pengajuan aktif berstatus menunggu.')->withInput();
        }

        $kelasTujuan = Kelas::findOrFail($validated['id_kelas_tujuan']);
        $validationMessage = $this->transferValidationMessage($siswa, $kelasTujuan);

        if ($validationMessage) {
            return back()->with('error', $validationMessage)->withInput();
        }

        PengajuanPerpindahanKelas::create([
            'nomor_induk_siswa' => $siswa->nomor_induk_siswa,
            'id_kelas_asal' => $siswa->id_kelas,
            'id_kelas_tujuan' => $kelasTujuan->id_kelas,
            'id_guru_pengaju' => session('id_guru'),
            'alasan' => $validated['alasan'],
            'status' => 'menunggu',
            'tanggal_pengajuan' => now(),
        ]);

        return redirect()->route('transfer-siswa.index')
            ->with('success', "Pengajuan perpindahan {$siswa->nama_siswa} berhasil dikirim dan menunggu persetujuan.");
    }

    public function show($id_pengajuan)
    {
        $pengajuan = PengajuanPerpindahanKelas::with(['siswa', 'kelasAsal', 'kelasTujuan', 'guruPengaju', 'guruPemroses'])
            ->findOrFail($id_pengajuan);

        if (!$this->canGuruAccessPengajuan($pengajuan)) {
            return redirect()->route('transfer-siswa.index')->with('error', 'Anda tidak memiliki akses ke pengajuan ini.');
        }

        $jumlahKelasAsal = Siswa::where('id_kelas', $pengajuan->id_kelas_asal)->count();
        $jumlahKelasTujuan = Siswa::where('id_kelas', $pengajuan->id_kelas_tujuan)->count();
        $isSuperAdmin = $this->isSuperAdmin();

        return view('transfer-siswa.show', compact('pengajuan', 'jumlahKelasAsal', 'jumlahKelasTujuan', 'isSuperAdmin'));
    }

    public function approve($id_pengajuan)
    {
        if (!$this->isSuperAdmin()) {
            return redirect()->route('transfer-siswa.index')->with('error', 'Hanya kepala sekolah / super admin yang dapat menyetujui pengajuan.');
        }

        $pengajuan = PengajuanPerpindahanKelas::with(['siswa', 'kelasAsal', 'kelasTujuan'])->findOrFail($id_pengajuan);

        if ($pengajuan->status !== 'menunggu') {
            return back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        return DB::transaction(function () use ($pengajuan) {
            $siswa = Siswa::where('nomor_induk_siswa', $pengajuan->nomor_induk_siswa)->lockForUpdate()->firstOrFail();
            $kelasTujuan = Kelas::findOrFail($pengajuan->id_kelas_tujuan);

            if ((int) $siswa->id_kelas !== (int) $pengajuan->id_kelas_asal) {
                return back()->with('error', 'Kelas asal siswa sudah berubah. Pengajuan tidak dapat disetujui.');
            }

            $validationMessage = $this->transferValidationMessage($siswa, $kelasTujuan);
            if ($validationMessage) {
                return back()->with('error', $validationMessage);
            }

            $siswa->update(['id_kelas' => $kelasTujuan->id_kelas]);
            $pengajuan->update([
                'status' => 'disetujui',
                'id_guru_pemroses' => session('id_guru'),
                'tanggal_diproses' => now(),
            ]);

            return redirect()->route('transfer-siswa.index')
                ->with('success', "Pengajuan disetujui. {$siswa->nama_siswa} otomatis dipindahkan ke kelas {$kelasTujuan->nama_kelas}.");
        });
    }

    public function reject(Request $request, $id_pengajuan)
    {
        if (!$this->isSuperAdmin()) {
            return redirect()->route('transfer-siswa.index')->with('error', 'Hanya kepala sekolah / super admin yang dapat menolak pengajuan.');
        }

        $validated = $request->validate([
            'alasan_penolakan' => 'required|string|min:5|max:1000',
        ]);

        $pengajuan = PengajuanPerpindahanKelas::findOrFail($id_pengajuan);

        if ($pengajuan->status !== 'menunggu') {
            return back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        $pengajuan->update([
            'status' => 'ditolak',
            'alasan_penolakan' => $validated['alasan_penolakan'],
            'id_guru_pemroses' => session('id_guru'),
            'tanggal_diproses' => now(),
        ]);

        return redirect()->route('transfer-siswa.index')->with('success', 'Pengajuan perpindahan kelas berhasil ditolak dan tersimpan di riwayat.');
    }
}
