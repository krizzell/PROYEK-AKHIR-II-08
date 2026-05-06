<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;

class TagihanController extends Controller
{
    public function index()
    {
        $idGuru = session('id_guru');
        $isSuperAdmin = session('is_super_admin', false);

        // Start query dengan eager loading DARI AWAL
        $query = Tagihan::with('siswa', 'siswa.kelas');

        // Filter untuk guru biasa: hanya lihat tagihan siswa dari kelas mereka
        if ($idGuru && !$isSuperAdmin) {
            $guruKelas = Kelas::where('id_guru', $idGuru)->pluck('id_kelas')->toArray();
            $query->whereHas('siswa', function ($q) use ($guruKelas) {
                $q->whereIn('id_kelas', $guruKelas);
            });
        }

        // Filter by NIS
        if (request('nis')) {
            $query->whereHas('siswa', function ($q) {
                $q->where('nomor_induk_siswa', 'like', '%' . request('nis') . '%');
            });
        }

        // Filter by Nama Siswa
        if (request('nama')) {
            $query->whereHas('siswa', function ($q) {
                $q->where('nama_siswa', 'like', '%' . request('nama') . '%');
            });
        }

        // Filter by Kelas
        if (request('kelas')) {
            $query->whereHas('siswa', function ($q) {
                $q->where('id_kelas', request('kelas'));
            });
        }

        // Filter by Periode
        if (request('periode')) {
            $query->where('periode', request('periode'));
        }

        // Filter by Status
        if (request('status')) {
            $query->where('status', request('status'));
        }

        // Execute query
        $tagihan = $query->orderBy('id_tagihan', 'desc')->get();
        
        // Get filter options - untuk guru biasa, hanya kelas mereka sendiri
        if ($idGuru && !$isSuperAdmin) {
            $guruKelas = Kelas::where('id_guru', $idGuru)->pluck('id_kelas')->toArray();
            $kelas = Kelas::whereIn('id_kelas', $guruKelas)->get();
        } else {
            $kelas = Kelas::all();
        }
        
        $periode = Tagihan::distinct()->pluck('periode');
        $statuses = ['belum_bayar' => 'Belum Bayar', 'lunas' => 'Lunas'];

        return view('tagihan.index', compact('tagihan', 'kelas', 'periode', 'statuses', 'isSuperAdmin'));
    }

    public function create()
    {
        $isSuperAdmin = session('is_super_admin', false);
        
        // Hanya super admin yang bisa membuat tagihan
        if (!$isSuperAdmin) {
            return redirect()->route('tagihan.index')->with('error', 'Anda tidak berwenang membuat tagihan. Hanya admin yang dapat membuat tagihan.');
        }
        
        $siswa = Siswa::all();
        
        return view('tagihan.create', compact('siswa'));
    }

    public function store(Request $request)
    {
        $isSuperAdmin = session('is_super_admin', false);
        
        // Hanya super admin yang bisa membuat tagihan
        if (!$isSuperAdmin) {
            return redirect()->route('tagihan.index')->with('error', 'Anda tidak berwenang membuat tagihan. Hanya admin yang dapat membuat tagihan.');
        }
        
        $validated = $request->validate([
            'nomor_induk_siswa' => 'required|exists:siswa,nomor_induk_siswa',
            'jumlah_tagihan' => 'required|numeric|min:0',
            'periode' => 'required|string|max:20',
        ]);

        // Set payment_status default ke 'belum_bayar'
        $validated['payment_status'] = 'belum_bayar';
        $validated['status'] = 'belum_bayar';
        
        Tagihan::create($validated);
        return redirect()->route('tagihan.index')->with('success', 'Tagihan berhasil ditambahkan');
    }

    public function show(Tagihan $tagihan)
    {
        $idGuru = session('id_guru');
        $isSuperAdmin = session('is_super_admin', false);
        
        // Guru biasa hanya bisa lihat tagihan siswa kelasnya
        if ($idGuru && !$isSuperAdmin) {
            $guruKelas = Kelas::where('id_guru', $idGuru)->pluck('id_kelas')->toArray();
            if (!in_array($tagihan->siswa->id_kelas, $guruKelas)) {
                return redirect()->route('tagihan.index')->with('error', 'Anda tidak berwenang melihat tagihan ini');
            }
        }
        
        return view('tagihan.show', compact('tagihan'));
    }

    public function edit(Tagihan $tagihan)
    {
        // Tagihan tidak boleh diedit oleh siapapun untuk menjaga integritas data pembayaran
        abort(403, 'Data tagihan tidak dapat diedit untuk keamanan data pembayaran.');
    }

    public function update(Request $request, Tagihan $tagihan)
    {
        // Tagihan tidak boleh diupdate oleh siapapun untuk menjaga integritas data pembayaran
        abort(403, 'Data tagihan tidak dapat diubah untuk keamanan data pembayaran.');
    }

    public function destroy(Tagihan $tagihan)
    {
        // Tagihan tidak boleh dihapus oleh siapapun untuk menjaga integritas data pembayaran
        abort(403, 'Data tagihan tidak dapat dihapus untuk keamanan data pembayaran.');
    }

    public function bulkCreate()
    {
        $isSuperAdmin = session('is_super_admin', false);
        
        // Hanya super admin yang bisa membuat bulk tagihan
        if (!$isSuperAdmin) {
            return redirect()->route('tagihan.index')->with('error', 'Anda tidak berwenang membuat bulk tagihan. Hanya admin yang dapat membuat tagihan.');
        }
        
        return view('tagihan.bulk-create');
    }

    public function bulkCreateStore(Request $request)
    {
        $isSuperAdmin = session('is_super_admin', false);
        
        // Hanya super admin yang bisa membuat bulk tagihan
        if (!$isSuperAdmin) {
            return redirect()->route('tagihan.index')->with('error', 'Anda tidak berwenang membuat bulk tagihan. Hanya admin yang dapat membuat tagihan.');
        }
        
        $validated = $request->validate([
            'jumlah_tagihan' => 'required|numeric|min:1',
        ], [
            'jumlah_tagihan.required' => 'Jumlah tagihan wajib diisi',
            'jumlah_tagihan.numeric' => 'Jumlah tagihan harus berupa angka',
            'jumlah_tagihan.min' => 'Jumlah tagihan minimal Rp 1',
        ]);

        // Gunakan current date untuk bulan dan tahun
        $now = now();
        $bulanNama = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $periode = 'SPP ' . $bulanNama[$now->month] . ' ' . $now->year;

        // Apply all untuk semua siswa
        $query = Siswa::query();
        
        $siswaList = $query->get();
        $countCreated = 0;

        // Buat tagihan untuk masing-masing siswa
        foreach ($siswaList as $siswa) {
            // Cek apakah sudah ada tagihan dengan periode yang sama
            $existingTagihan = Tagihan::where('nomor_induk_siswa', $siswa->nomor_induk_siswa)
                                     ->where('periode', $periode)
                                     ->exists();

            if (!$existingTagihan) {
                Tagihan::create([
                    'nomor_induk_siswa' => $siswa->nomor_induk_siswa,
                    'jumlah_tagihan' => $validated['jumlah_tagihan'],
                    'periode' => $periode,
                    'status' => 'belum_bayar',
                    'payment_status' => 'belum_bayar'
                ]);
                $countCreated++;
            }
        }

        $message = "Tagihan berhasil dibuat untuk {$countCreated} siswa";
        if ($countCreated < count($siswaList)) {
            $skipped = count($siswaList) - $countCreated;
            $message .= " ({$skipped} siswa sudah memiliki tagihan untuk periode ini)";
        }

        return redirect()->route('tagihan.index')->with('success', $message);
    }

    public function bulkUpdateStatus()
    {
        $idGuru = session('id_guru');
        $isSuperAdmin = session('is_super_admin', false);
        
        // Guru biasa hanya bisa update tagihan untuk kelasnya
        if ($idGuru && !$isSuperAdmin) {
            $guruKelas = Kelas::where('id_guru', $idGuru)->pluck('id_kelas');
            $kelas = Kelas::where('id_guru', $idGuru)->get();
            $periode = Tagihan::whereHas('siswa', function($q) use ($guruKelas) {
                $q->whereIn('id_kelas', $guruKelas);
            })->distinct()->pluck('periode');
        } else {
            $kelas = Kelas::all();
            $periode = Tagihan::distinct()->pluck('periode');
        }
        
        $statuses = ['belum_bayar' => 'Belum Bayar', 'lunas' => 'Lunas'];
        
        return view('tagihan.bulk-update-status', compact('kelas', 'periode', 'statuses'));
    }

    public function bulkUpdateStatusStore(Request $request)
    {
        $idGuru = session('id_guru');
        $isSuperAdmin = session('is_super_admin', false);
        
        $validated = $request->validate([
            'filter_id_kelas' => 'nullable|exists:kelas,id_kelas',
            'filter_periode' => 'nullable|string',
            'filter_status' => 'nullable|in:belum_bayar,lunas',
            'new_status' => 'required|in:belum_bayar,lunas',
        ], [
            'new_status.required' => 'Status baru wajib dipilih',
        ]);

        // Build query with filters
        $query = Tagihan::query();

        // Guru biasa hanya bisa update tagihan kelasnya
        if ($idGuru && !$isSuperAdmin) {
            $guruKelas = Kelas::where('id_guru', $idGuru)->pluck('id_kelas')->toArray();
            $query->whereHas('siswa', function ($q) use ($guruKelas) {
                $q->whereIn('id_kelas', $guruKelas);
            });
        }

        if ($validated['filter_id_kelas']) {
            $query->whereHas('siswa', function ($q) use ($validated) {
                $q->where('id_kelas', $validated['filter_id_kelas']);
            });
        }

        if ($validated['filter_periode']) {
            $query->where('periode', $validated['filter_periode']);
        }

        if ($validated['filter_status']) {
            $query->where('status', $validated['filter_status']);
        }

        // Execute update
        $count = $query->update([
            'status' => $validated['new_status'],
            'payment_status' => $validated['new_status'],
        ]);

        $statusLabels = ['belum_bayar' => 'Belum Bayar', 'lunas' => 'Lunas'];
        $newStatusLabel = $statusLabels[$validated['new_status']];

        return redirect()->route('tagihan.index')->with('success', "Status {$count} tagihan berhasil diubah menjadi '{$newStatusLabel}'");
    }
}
