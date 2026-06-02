<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Akun;
use App\Models\Pengumuman;
use App\Models\Perkembangan;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Main statistics
        $siswa_count = Siswa::count();
        $perkembangan_recent = Perkembangan::count();
        $pembayaran_pending = Tagihan::where('status', 'belum_bayar')->count();
        $pengumuman_active = Pengumuman::count();

        // Recent activities (combined perkembangan dan pembayaran)
        $activities = collect();
        
        // Get recent perkembangan updates
        $perkembangan_updates = Perkembangan::with('siswa.kelas', 'guru')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->siswa->nama_siswa ?? 'Unknown',
                    'action' => 'Memperbarui data perkembangan',
                    'class' => $item->siswa->kelas->nama_kelas ?? '-',
                    'time' => $item->updated_at,
                    'type' => 'perkembangan'
                ];
            });

        // Get recent pembayaran verifications
        $pembayaran_updates = Pembayaran::with('tagihan.siswa.kelas')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->tagihan->siswa->nama_siswa ?? 'Unknown',
                    'action' => 'Memverifikasi pembayaran SPP',
                    'class' => $item->tagihan->siswa->kelas->nama_kelas ?? '-',
                    'time' => $item->updated_at,
                    'type' => 'pembayaran'
                ];
            });

        // Merge and sort by time
        $activities = $perkembangan_updates->merge($pembayaran_updates)
            ->sortByDesc('time')
            ->take(10);

        return view('dashboard.index', compact(
            'siswa_count',
            'perkembangan_recent',
            'pembayaran_pending',
            'pengumuman_active',
            'activities'
        ));
    }
}
