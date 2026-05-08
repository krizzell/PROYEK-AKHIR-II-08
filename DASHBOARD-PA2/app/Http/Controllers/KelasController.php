<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Guru;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::with('guru')->withCount('siswa')->get();
        return view('kelas.index', compact('kelas'));
    }

    public function create()
    {
        $guru = Guru::all();
        return view('kelas.create', compact('guru'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_guru' => 'required|exists:guru,id_guru',
            'nama_kelas' => 'required|string|max:100',
        ]);

        Kelas::create($validated);
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil ditambahkan');
    }

    public function show($id_kelas)
    {
        $kelas = Kelas::with(['guru', 'siswa'])->findOrFail($id_kelas);
        return view('kelas.show', compact('kelas'));
    }

    public function edit($id_kelas)
    {
        $kelas = Kelas::findOrFail($id_kelas);
        $guru = Guru::all();
        return view('kelas.edit', compact('kelas', 'guru'));
    }

    public function update(Request $request, $id_kelas)
    {
        $kelas = Kelas::findOrFail($id_kelas);
        
        $validated = $request->validate([
            'id_guru' => 'required|exists:guru,id_guru',
            'nama_kelas' => 'required|string|max:100',
        ]);

        $kelas->update($validated);
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil diperbarui');
    }

    public function destroy($id_kelas)
    {
        $kelas = Kelas::findOrFail($id_kelas);
        $kelas->delete();
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dihapus');
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'selected_kelas' => 'required|array|min:1',
            'selected_kelas.*' => 'required|integer|exists:kelas,id_kelas',
        ]);

        $deletedCount = Kelas::whereIn('id_kelas', $validated['selected_kelas'])->delete();

        return redirect()->route('kelas.index')->with('success', $deletedCount . ' data kelas berhasil dihapus');
    }
}
