<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;

class TransferSiswaController extends Controller
{
    // Kelas yang terkunci untuk transfer
    private const LOCKED_CLASS = 'TK A';
    
    // Minimum dan maksimum siswa per kelas
    private const MIN_SISWA = 20;
    private const MAX_SISWA = 30;

    public function index()
    {
        // Ambil semua kelas dengan siswa yang ada di dalamnya
        $kelas = Kelas::with(['siswa' => function ($query) {
            $query->select('nomor_induk_siswa', 'nama_siswa', 'jenis_kelamin', 'id_kelas')
                  ->orderBy('nama_siswa');
        }])
        ->withCount('siswa')
        ->get();

        // Pass constant ke view untuk checking
        return view('transfer-siswa.index', compact('kelas'))->with('lockedClass', self::LOCKED_CLASS);
    }

    public function transfer($nomor_induk_siswa)
    {
        // Ambil data siswa dengan kelas saat ini
        $siswa = Siswa::findOrFail($nomor_induk_siswa);
        $kelasSekarang = $siswa->kelas;

        // Ambil semua kelas KECUALI:
        // 1. Kelas siswa saat ini
        // 2. TK A (kelas terkunci)
        $kelasAyo = Kelas::where('id_kelas', '!=', $siswa->id_kelas)
                         ->where('nama_kelas', '!=', self::LOCKED_CLASS)
                         ->orderBy('nama_kelas')
                         ->get();

        return view('transfer-siswa.transfer', compact('siswa', 'kelasSekarang', 'kelasAyo'))->with('lockedClass', self::LOCKED_CLASS);
    }

    public function proses(Request $request, $nomor_induk_siswa)
    {
        $request->validate([
            'id_kelas_tujuan' => 'required|exists:kelas,id_kelas',
        ]);

        $siswa = Siswa::findOrFail($nomor_induk_siswa);
        $kelasLama = $siswa->kelas;
        $kelasBaru = Kelas::findOrFail($request->id_kelas_tujuan);
        $kelasLamaObj = Kelas::findOrFail($siswa->id_kelas);

        // Validasi 1: Cek apakah kelas saat ini adalah TK A (terkunci)
        if ($kelasLamaObj->nama_kelas === self::LOCKED_CLASS) {
            return back()->with('error', self::LOCKED_CLASS . ' adalah kelas terkunci dan tidak dapat melakukan perpindahan siswa.');
        }

        // Validasi 2: Cek apakah kelas tujuan adalah TK A (terkunci)
        if ($kelasBaru->nama_kelas === self::LOCKED_CLASS) {
            return back()->with('error', 'Siswa tidak dapat dipindahkan ke kelas ' . self::LOCKED_CLASS . '.');
        }

        // Validasi 3: Jangan bisa transfer ke kelas yang sama
        if ($siswa->id_kelas == $request->id_kelas_tujuan) {
            return back()->with('error', 'Siswa sudah berada di kelas ini!');
        }

        // Validasi 4: Cek jumlah siswa di kelas lama tidak akan kurang dari minimum
        $siswaKelasLama = Siswa::where('id_kelas', $siswa->id_kelas)->count();
        if ($siswaKelasLama - 1 < self::MIN_SISWA) {
            return back()->with('error', "Tidak bisa melakukan perpindahan! Kelas {$kelasLamaObj->nama_kelas} akan memiliki kurang dari " . self::MIN_SISWA . " siswa.");
        }

        // Validasi 5: Cek jumlah siswa di kelas tujuan tidak akan melebihi maksimum
        $siswaKelasBaru = Siswa::where('id_kelas', $request->id_kelas_tujuan)->count();
        if ($siswaKelasBaru + 1 > self::MAX_SISWA) {
            return back()->with('error', "Tidak bisa melakukan perpindahan! Kelas {$kelasBaru->nama_kelas} akan memiliki lebih dari " . self::MAX_SISWA . " siswa.");
        }

        // Update kelas siswa
        $siswa->update(['id_kelas' => $request->id_kelas_tujuan]);

        return redirect()->route('transfer-siswa.index')
                        ->with('success', "Siswa {$siswa->nama_siswa} berhasil dipindahkan dari {$kelasLamaObj->nama_kelas} ke {$kelasBaru->nama_kelas}!");
    }
}
