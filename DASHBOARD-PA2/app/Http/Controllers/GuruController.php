<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Kelas;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    public function index()
    {
        $guru = Guru::all();
        return view('guru.index', compact('guru'));
    }

    public function create()
    {
        $kelas = Kelas::all();
        return view('guru.create', compact('kelas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip_guru' => 'required|string|max:30|unique:guru',
            'nama_guru' => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string|max:255',
            'no_hp' => 'required|string|max:15',
            'email' => 'required|email|max:100|unique:guru',
            'jabatan' => 'required|in:Guru,Kepala Sekolah',
            'pendidikan_terakhir' => 'required|string|max:100',
            'jurusan' => 'required|string|max:100',
            'kelas_ampuan' => 'nullable|array',
            'kelas_ampuan.*' => 'exists:kelas,id_kelas',
        ]);

        // Pisahkan kelas_ampuan dari validated
        $kelasAmpuan = $validated['kelas_ampuan'] ?? [];
        unset($validated['kelas_ampuan']);

        // Create guru
        $guru = Guru::create($validated);

        // Attach kelas
        if (!empty($kelasAmpuan)) {
            $guru->kelasAmpuan()->attach($kelasAmpuan);
        }

        return redirect()->route('guru.index')->with('success', 'Guru berhasil ditambahkan');
    }

    public function show(Guru $guru)
    {
        return view('guru.show', compact('guru'));
    }

    public function edit(Guru $guru)
    {
        $kelas = Kelas::all();
        $kelasAmpuanIds = $guru->kelasAmpuan()->pluck('guru_kelas.id_kelas')->toArray();
        return view('guru.edit', compact('guru', 'kelas', 'kelasAmpuanIds'));
    }

    public function update(Request $request, Guru $guru)
    {
        $validated = $request->validate([
            'nip_guru' => 'required|string|max:30|unique:guru,nip_guru,' . $guru->id_guru . ',id_guru',
            'nama_guru' => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string|max:255',
            'no_hp' => 'required|string|max:15',
            'email' => 'required|email|max:100|unique:guru,email,' . $guru->id_guru . ',id_guru',
            'jabatan' => 'required|in:Guru,Kepala Sekolah',
            'pendidikan_terakhir' => 'required|string|max:100',
            'jurusan' => 'required|string|max:100',
            'kelas_ampuan' => 'nullable|array',
            'kelas_ampuan.*' => 'exists:kelas,id_kelas',
        ]);

        // Pisahkan kelas_ampuan dari validated
        $kelasAmpuan = $validated['kelas_ampuan'] ?? [];
        unset($validated['kelas_ampuan']);

        // Update guru
        $guru->update($validated);

        // Sync kelas
        $guru->kelasAmpuan()->sync($kelasAmpuan);

        return redirect()->route('guru.index')->with('success', 'Guru berhasil diperbarui');
    }

    public function destroy(Guru $guru)
    {
        $guru->delete();
        return redirect()->route('guru.index')->with('success', 'Guru berhasil dihapus');
    }
}
