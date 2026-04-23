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
        
        // Get ALL filter options
        $kelas = Kelas::all();
        $periode = Tagihan::distinct()->pluck('periode');
        $statuses = ['belum_bayar' => 'Belum Bayar', 'lunas' => 'Lunas'];

        return view('tagihan.index', compact('tagihan', 'kelas', 'periode', 'statuses'));
    }

    public function create()
    {
        $idGuru = session('id_guru');
        $isSuperAdmin = session('is_super_admin', false);
        
        // Guru biasa hanya bisa buat tagihan untuk siswa kelasnya
        if ($idGuru && !$isSuperAdmin) {
            // Get kelas milik guru ini
            $guruKelas = Kelas::where('id_guru', $idGuru)->pluck('id_kelas');
            $siswa = Siswa::whereIn('id_kelas', $guruKelas)->get();
        } else {
            $siswa = Siswa::all();
        }
        
        return view('tagihan.create', compact('siswa'));
    }

    public function store(Request $request)
    {
        $idGuru = session('id_guru');
        $isSuperAdmin = session('is_super_admin', false);
        
        $validated = $request->validate([
            'nomor_induk_siswa' => 'required|exists:siswa,nomor_induk_siswa',
            'jumlah_tagihan' => 'required|numeric|min:0',
            'periode' => 'required|string|max:20',
        ]);

        // Guru biasa hanya bisa buat tagihan untuk siswa kelasnya
        if ($idGuru && !$isSuperAdmin) {
            $siswa = Siswa::findOrFail($validated['nomor_induk_siswa']);
            $guruKelas = Kelas::where('id_guru', $idGuru)->pluck('id_kelas')->toArray();
            if (!in_array($siswa->id_kelas, $guruKelas)) {
                return redirect()->route('tagihan.index')->with('error', 'Anda tidak berwenang membuat tagihan untuk siswa ini');
            }
        }

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
        $idGuru = session('id_guru');
        $isSuperAdmin = session('is_super_admin', false);
        
        // Guru biasa hanya bisa edit tagihan siswa kelasnya
        if ($idGuru && !$isSuperAdmin) {
            $guruKelas = Kelas::where('id_guru', $idGuru)->pluck('id_kelas')->toArray();
            if (!in_array($tagihan->siswa->id_kelas, $guruKelas)) {
                return redirect()->route('tagihan.index')->with('error', 'Anda tidak berwenang mengedit tagihan ini');
            }
            $siswa = Siswa::whereIn('id_kelas', $guruKelas)->get();
        } else {
            $siswa = Siswa::all();
        }
        
        return view('tagihan.edit', compact('tagihan', 'siswa'));
    }

    public function update(Request $request, Tagihan $tagihan)
    {
        $idGuru = session('id_guru');
        $isSuperAdmin = session('is_super_admin', false);
        
        // Guru biasa hanya bisa update tagihan siswa kelasnya
        if ($idGuru && !$isSuperAdmin) {
            $guruKelas = Kelas::where('id_guru', $idGuru)->pluck('id_kelas')->toArray();
            if (!in_array($tagihan->siswa->id_kelas, $guruKelas)) {
                return redirect()->route('tagihan.index')->with('error', 'Anda tidak berwenang mengupdate tagihan ini');
            }
        }
        
        $validated = $request->validate([
            'nomor_induk_siswa' => 'required|exists:siswa,nomor_induk_siswa',
            'jumlah_tagihan' => 'required|numeric|min:0',
            'periode' => 'required|string|max:20',
        ]);

        // Status TIDAK bisa diedit manual - hanya berubah via payment gateway
        $tagihan->update($validated);
        return redirect()->route('tagihan.index')->with('success', 'Tagihan berhasil diperbarui');
    }

    public function destroy(Tagihan $tagihan)
    {
        $idGuru = session('id_guru');
        $isSuperAdmin = session('is_super_admin', false);
        
        // Guru biasa hanya bisa delete tagihan siswa kelasnya
        if ($idGuru && !$isSuperAdmin) {
            $guruKelas = Kelas::where('id_guru', $idGuru)->pluck('id_kelas')->toArray();
            if (!in_array($tagihan->siswa->id_kelas, $guruKelas)) {
                return redirect()->route('tagihan.index')->with('error', 'Anda tidak berwenang menghapus tagihan ini');
            }
        }
        
        $tagihan->delete();
        return redirect()->route('tagihan.index')->with('success', 'Tagihan berhasil dihapus');
    }

    public function bulkCreate()
    {
        $idGuru = session('id_guru');
        $isSuperAdmin = session('is_super_admin', false);
        
        // Guru biasa hanya bisa buat tagihan untuk kelasnya
        if ($idGuru && !$isSuperAdmin) {
            $kelas = Kelas::where('id_guru', $idGuru)->get();
        } else {
            $kelas = Kelas::all();
        }
        
        return view('tagihan.bulk-create', compact('kelas'));
    }

    public function bulkCreateStore(Request $request)
    {
        $idGuru = session('id_guru');
        $isSuperAdmin = session('is_super_admin', false);
        
        $validated = $request->validate([
            'tipe_target' => 'required|in:semua_siswa,per_kelas',
            'id_kelas' => 'required_if:tipe_target,per_kelas|nullable|exists:kelas,id_kelas',
            'jumlah_tagihan' => 'required|numeric|min:1',
            'periode' => 'required|string|max:20',
        ], [
            'id_kelas.required_if' => 'Kelas wajib dipilih jika target per kelas',
        ]);

        // Validate permission - guru biasa hanya bisa untuk kelasnya
        if ($idGuru && !$isSuperAdmin) {
            $guruKelas = Kelas::where('id_guru', $idGuru)->pluck('id_kelas')->toArray();
            
            if ($validated['tipe_target'] === 'per_kelas' && !in_array($validated['id_kelas'], $guruKelas)) {
                return redirect()->route('tagihan.index')->with('error', 'Anda tidak berwenang membuat tagihan untuk kelas ini');
            } elseif ($validated['tipe_target'] === 'semua_siswa') {
                // Semua siswa berarti hanya kelasnya saja
                $validated['tipe_target'] = 'per_kelas';
                $validated['id_kelas'] = $guruKelas[0] ?? null;
                if (!$validated['id_kelas']) {
                    return redirect()->route('tagihan.index')->with('error', 'Anda tidak memiliki kelas');
                }
            }
        }

        // Tentukan siswa target
        $query = Siswa::query();
        
        if ($validated['tipe_target'] === 'per_kelas') {
            $query->where('id_kelas', $validated['id_kelas']);
        }
        
        $siswaList = $query->get();
        $countCreated = 0;

        // Buat tagihan untuk masing-masing siswa
        foreach ($siswaList as $siswa) {
            // Cek apakah sudah ada tagihan dengan periode yang sama
            $existingTagihan = Tagihan::where('nomor_induk_siswa', $siswa->nomor_induk_siswa)
                                     ->where('periode', $validated['periode'])
                                     ->exists();

            if (!$existingTagihan) {
                Tagihan::create([
                    'nomor_induk_siswa' => $siswa->nomor_induk_siswa,
                    'jumlah_tagihan' => $validated['jumlah_tagihan'],
                    'periode' => $validated['periode'],
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
