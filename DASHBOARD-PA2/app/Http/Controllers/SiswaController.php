<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Siswa::with('kelas');

        // Filter berdasarkan NIS
        if ($request->filled('nis')) {
            $query->where('nomor_induk_siswa', 'like', '%' . $request->nis . '%');
        }

        // Filter berdasarkan Nama Siswa
        if ($request->filled('nama')) {
            $query->where('nama_siswa', 'like', '%' . $request->nama . '%');
        }

        // Filter berdasarkan Kelas
        if ($request->filled('kelas')) {
            $query->where('id_kelas', $request->kelas);
        }

        // Filter berdasarkan Jenis Kelamin
        if ($request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        $siswa = $query->get();
        $kelas = Kelas::all();

        return view('siswa.index', compact('siswa', 'kelas'));
    }

    public function create()
    {
        // Tampilkan SEMUA kelas yang tersedia
        $kelas = Kelas::all();
        return view('siswa.create', compact('kelas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_induk_siswa' => 'required|numeric|unique:siswa,nomor_induk_siswa',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'nama_siswa' => 'required|string|max:150',
            'nama_orgtua' => 'required|string|max:150',
            'tgl_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string',
        ]);

        Siswa::create($validated);
        return redirect()->route('siswa.index')->with('success', 'Siswa berhasil ditambahkan');
    }

    public function show($nomor_induk_siswa)
    {
        $siswa = Siswa::where('nomor_induk_siswa', $nomor_induk_siswa)->firstOrFail();
        return view('siswa.show', compact('siswa'));
    }

    public function edit($nomor_induk_siswa)
    {
        $siswa = Siswa::where('nomor_induk_siswa', $nomor_induk_siswa)->firstOrFail();
        // Tampilkan SEMUA kelas yang tersedia
        $kelas = Kelas::all();
        return view('siswa.edit', compact('siswa', 'kelas'));
    }

    public function update(Request $request, $nomor_induk_siswa)
    {
        $siswa = Siswa::where('nomor_induk_siswa', $nomor_induk_siswa)->firstOrFail();
        
        $validated = $request->validate([
            'nomor_induk_siswa' => 'required|numeric|unique:siswa,nomor_induk_siswa,' . $siswa->nomor_induk_siswa . ',nomor_induk_siswa',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'nama_siswa' => 'required|string|max:150',
            'nama_orgtua' => 'required|string|max:150',
            'tgl_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string',
        ]);

        $siswa->update($validated);
        return redirect()->route('siswa.index')->with('success', 'Siswa berhasil diperbarui');
    }

    public function destroy($nomor_induk_siswa)
    {
        $siswa = Siswa::where('nomor_induk_siswa', $nomor_induk_siswa)->firstOrFail();
        $siswa->delete();
        return redirect()->route('siswa.index')->with('success', 'Siswa berhasil dihapus');
    }
}
