<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengumumanController extends Controller
{
    public function index()
    {
        $pengumuman = Pengumuman::with('guru')->latest('waktu_unggah')->get();
        return view('pengumuman.index', compact('pengumuman'));
    }

    public function create()
    {
        // Debug: Cek apakah guru_id ada di session
        if (!session('id_guru')) {
            return redirect()->route('pengumuman.index')->with('error', 
                'Akun Anda tidak terhubung dengan data guru. ' .
                'Hubungi super admin untuk link akun Anda dengan guru. ' 
                // '(Super Admin: Kelola Akun → Edit akun Anda → Pilih Guru)'
            );
        }
        
        return view('pengumuman.create');
    }

    public function store(Request $request)
    {
        // Pastikan user adalah guru dan punya id_guru
        if (!session('id_guru')) {
            return redirect()->route('pengumuman.index')->with('error', 'Anda tidak berwenang membuat pengumuman. Hanya guru yang dapat membuat pengumuman.');
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:150',
            'media' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'waktu_unggah' => 'required|date_format:Y-m-d\TH:i',
            'deskripsi' => 'required|string',
        ]);

        // Auto-set guru dari session
        $validated['id_guru'] = session('id_guru');

        // Convert datetime-local to proper datetime format
        $validated['waktu_unggah'] = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['waktu_unggah']);

        // Handle file upload
        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('pengumuman', $filename, 'public');
            $validated['media'] = $path;
        }

        Pengumuman::create($validated);
        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil ditambahkan');
    }

    public function show(Pengumuman $pengumuman)
    {
        return view('pengumuman.show', compact('pengumuman'));
    }

    public function edit(Pengumuman $pengumuman)
    {
        return view('pengumuman.edit', compact('pengumuman'));
    }

    public function update(Request $request, Pengumuman $pengumuman)
    {
        // Pastikan user adalah guru dan punya id_guru
        if (!session('id_guru')) {
            return redirect()->route('pengumuman.index')->with('error', 'Anda tidak berwenang mengedit pengumuman.');
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:150',
            'media' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'waktu_unggah' => 'required|date_format:Y-m-d\TH:i',
            'deskripsi' => 'required|string',
        ]);

        // Auto-set guru dari session
        $validated['id_guru'] = session('id_guru');

        // Convert datetime-local to proper datetime format
        $validated['waktu_unggah'] = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['waktu_unggah']);

        // Handle file upload
        if ($request->hasFile('media')) {
            // Delete old file if exists
            if ($pengumuman->media && Storage::disk('public')->exists($pengumuman->media)) {
                Storage::disk('public')->delete($pengumuman->media);
            }

            $file = $request->file('media');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('pengumuman', $filename, 'public');
            $validated['media'] = $path;
        }

        $pengumuman->update($validated);
        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil diperbarui');
    }

    public function destroy(Pengumuman $pengumuman)
    {
        // Delete media file if exists
        if ($pengumuman->media && Storage::disk('public')->exists($pengumuman->media)) {
            Storage::disk('public')->delete($pengumuman->media);
        }

        $pengumuman->delete();
        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil dihapus');
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'selected_pengumuman' => 'required|array|min:1',
            'selected_pengumuman.*' => 'required|integer|exists:pengumuman,id_pengumuman',
        ]);

        $pengumumanToDelete = Pengumuman::whereIn('id_pengumuman', $validated['selected_pengumuman'])->get();
        
        foreach ($pengumumanToDelete as $item) {
            if ($item->media && Storage::disk('public')->exists($item->media)) {
                Storage::disk('public')->delete($item->media);
            }
        }

        $deletedCount = Pengumuman::whereIn('id_pengumuman', $validated['selected_pengumuman'])->delete();
        return redirect()->route('pengumuman.index')->with('success', $deletedCount . ' pengumuman berhasil dihapus');
    }
}
