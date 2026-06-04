<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PengumumanController extends Controller
{
    private function canManagePengumuman(): bool
    {
        return session('is_super_admin', false) || (bool) session('id_guru');
    }

    private function displayDurationOptions(): array
    {
        return [
            '1_hari' => '1 Hari',
            '3_hari' => '3 Hari',
            '7_hari' => '1 Minggu',
            '14_hari' => '2 Minggu',
            '30_hari' => '1 Bulan',
            '90_hari' => '3 Bulan',
        ];
    }

    private function calculateDisplayUntil(\Carbon\Carbon $publishedAt, string $duration): \Carbon\Carbon
    {
        $days = match ($duration) {
            '1_hari' => 1,
            '3_hari' => 3,
            '14_hari' => 14,
            '30_hari' => 30,
            '90_hari' => 90,
            default => 7,
        };

        return $publishedAt->copy()->addDays($days);
    }

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
        // Hanya SuperAdmin dan guru yang bisa membuat pengumuman
        // SuperAdmin: bisa membuat pengumuman untuk sekolah
        // Guru regular: harus punya id_guru di session

        if (!$this->canManagePengumuman()) {
            return redirect()->route('pengumuman.index')->with('error', 
                'Akun Anda tidak terhubung dengan data guru. ' .
                'Hubungi super admin untuk link akun Anda dengan guru.'
            );
        }
        
        $displayDurationOptions = $this->displayDurationOptions();

        return view('pengumuman.create', compact('displayDurationOptions'));
    }

    public function store(Request $request)
    {
        // SuperAdmin atau guru yang punya id_guru bisa membuat pengumuman
        if (!$this->canManagePengumuman()) {
            return redirect()->route('pengumuman.index')->with('error', 'Anda tidak berwenang membuat pengumuman. Hanya guru dan admin yang dapat membuat pengumuman.');
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:150',
            'waktu_unggah' => 'required|date_format:Y-m-d\TH:i',
            'durasi_tampil' => 'required|in:' . implode(',', array_keys($this->displayDurationOptions())),
            'deskripsi' => 'required|string',
        ] + $this->buildMediaValidationRules(), $this->mediaValidationMessages());

        // Auto-set guru dari session
        $validated['id_guru'] = session('id_guru');

        // Convert datetime-local to proper datetime format
        $validated['waktu_unggah'] = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['waktu_unggah']);
        $validated['tampil_sampai'] = $this->calculateDisplayUntil(
            $validated['waktu_unggah'],
            $validated['durasi_tampil']
        );

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
        $displayDurationOptions = $this->displayDurationOptions();

        return view('pengumuman.edit', compact('pengumuman', 'displayDurationOptions'));
    }

    public function update(Request $request, Pengumuman $pengumuman)
    {
        // SuperAdmin dan guru bisa mengedit semua pengumuman.
        $isSuperAdmin = session('is_super_admin', false);
        if (!$this->canManagePengumuman()) {
            return redirect()->route('pengumuman.index')->with('error', 'Anda tidak berwenang mengedit pengumuman ini.');
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:150',
            'waktu_unggah' => 'required|date_format:Y-m-d\TH:i',
            'durasi_tampil' => 'required|in:' . implode(',', array_keys($this->displayDurationOptions())),
            'deskripsi' => 'required|string',
            'existing_media' => 'nullable|array',
            'existing_media.*' => 'nullable|string',
        ] + $this->buildMediaValidationRules(), $this->mediaValidationMessages());

        // Keep id_guru jika user bukan SuperAdmin
        if (!$isSuperAdmin) {
            $validated['id_guru'] = session('id_guru');
        }

        // Convert datetime-local to proper datetime format
        $validated['waktu_unggah'] = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['waktu_unggah']);
        $validated['tampil_sampai'] = $this->calculateDisplayUntil(
            $validated['waktu_unggah'],
            $validated['durasi_tampil']
        );

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
        // SuperAdmin dan guru bisa menghapus semua pengumuman.
        if (!$this->canManagePengumuman()) {
            return redirect()->route('pengumuman.index')->with('error', 'Anda tidak berwenang menghapus pengumuman ini.');
        }
        
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

        // SuperAdmin dan guru bisa menghapus semua pengumuman.
        if (!$this->canManagePengumuman()) {
            return redirect()->route('pengumuman.index')->with('error', 'Anda tidak berwenang menghapus pengumuman.');
        }

        $query = Pengumuman::whereIn('id_pengumuman', $validated['selected_pengumuman']);

        $pengumumanToDelete = $query->get();
        
        foreach ($pengumumanToDelete as $item) {
            $this->deleteStoredMediaPaths($item->mediaPaths());
        }

        $deletedCount = $pengumumanToDelete->count();
        if ($deletedCount > 0) {
            Pengumuman::whereIn('id_pengumuman', $pengumumanToDelete->pluck('id_pengumuman'))->delete();
            return redirect()->route('pengumuman.index')->with('success', $deletedCount . ' pengumuman berhasil dihapus');
        }
        
        return redirect()->route('pengumuman.index')->with('error', 'Tidak ada pengumuman yang dapat dihapus.');
    }
}
