<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PengumumanController extends Controller
{
    private function storeUploadedMedia(array $files): array
    {
        $paths = [];

        foreach ($files as $index => $file) {
            if (!$file) {
                continue;
            }

            $filename = Str::uuid()->toString() . '_' . $index . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
            $paths[] = $file->storeAs('pengumuman', $filename, 'public');
        }

        return $paths;
    }

    private function deleteStoredMediaPaths(array $mediaPaths): void
    {
        foreach ($mediaPaths as $path) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    private function buildMediaValidationRules(): array
    {
        return [
            'media' => 'nullable|array',
            'media.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ];
    }

    private function mediaValidationMessages(): array
    {
        return [
            'media.array' => 'Gambar harus dikirim sebagai daftar file.',
            'media.*.image' => 'Setiap file harus berupa gambar yang valid.',
            'media.*.mimes' => 'Format yang diizinkan hanya JPG, JPEG, PNG, dan WEBP.',
            'media.*.max' => 'Ukuran setiap gambar maksimal 5 MB.',
        ];
    }

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
            'waktu_unggah' => 'required|date_format:Y-m-d\TH:i',
            'deskripsi' => 'required|string',
        ] + $this->buildMediaValidationRules(), $this->mediaValidationMessages());

        // Auto-set guru dari session
        $validated['id_guru'] = session('id_guru');

        // Convert datetime-local to proper datetime format
        $validated['waktu_unggah'] = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['waktu_unggah']);

        $uploadedMedia = $this->storeUploadedMedia((array) $request->file('media', []));
        $validated['media'] = $uploadedMedia ? json_encode($uploadedMedia) : null;

        Pengumuman::create($validated);
        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil ditambahkan');
    }

    public function show(Pengumuman $pengumuman)
    {
        $mediaPaths = $pengumuman->mediaPaths();
        $mediaUrls = $pengumuman->mediaUrls();

        return view('pengumuman.show', compact('pengumuman', 'mediaPaths', 'mediaUrls'));
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
            'waktu_unggah' => 'required|date_format:Y-m-d\TH:i',
            'deskripsi' => 'required|string',
            'existing_media' => 'nullable|array',
            'existing_media.*' => 'nullable|string',
        ] + $this->buildMediaValidationRules(), $this->mediaValidationMessages());

        // Auto-set guru dari session
        $validated['id_guru'] = session('id_guru');

        // Convert datetime-local to proper datetime format
        $validated['waktu_unggah'] = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['waktu_unggah']);

        $oldMediaPaths = $pengumuman->mediaPaths();
        $keptMediaPaths = array_values(array_filter((array) $request->input('existing_media', [])));
        $removedMediaPaths = array_values(array_diff($oldMediaPaths, $keptMediaPaths));
        $uploadedMediaPaths = $this->storeUploadedMedia((array) $request->file('media', []));

        $this->deleteStoredMediaPaths($removedMediaPaths);

        $finalMediaPaths = array_values(array_filter(array_merge($keptMediaPaths, $uploadedMediaPaths)));
        $validated['media'] = $finalMediaPaths ? json_encode($finalMediaPaths) : null;

        $pengumuman->update($validated);
        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil diperbarui');
    }

    public function destroy(Pengumuman $pengumuman)
    {
        // Delete media file if exists
        $this->deleteStoredMediaPaths($pengumuman->mediaPaths());

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
            $this->deleteStoredMediaPaths($item->mediaPaths());
        }

        $deletedCount = Pengumuman::whereIn('id_pengumuman', $validated['selected_pengumuman'])->delete();
        return redirect()->route('pengumuman.index')->with('success', $deletedCount . ' pengumuman berhasil dihapus');
    }
}
