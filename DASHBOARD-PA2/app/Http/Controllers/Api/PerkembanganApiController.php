<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Perkembangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerkembanganApiController extends Controller
{
    /**
     * Get perkembangan for specific siswa
     */
    public function index(Request $request)
    {
        try {
            $nomor_induk_siswa = $request->query('nomor_induk_siswa') ?? $request->input('nomor_induk_siswa');
            
            if (!$nomor_induk_siswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'nomor_induk_siswa diperlukan'
                ], 400);
            }

            $perkembangan = Perkembangan::where('nomor_induk_siswa', $nomor_induk_siswa)
                ->with('siswa.kelas', 'guru', 'kategoriDetails')
                ->orderBy('tahun', 'desc')
                ->orderBy('bulan', 'desc')
                ->get()
                ->map(function ($item) {
                    $rataRataKelas = $this->getRataRataKelas($item);

                    return [
                        'id_perkembangan' => $item->id_perkembangan,
                        'id_guru' => $item->id_guru,
                        'nomor_induk_siswa' => $item->nomor_induk_siswa,
                        'nama_siswa' => $item->siswa?->nama_siswa ?? 'N/A',
                        'nama_guru' => $item->guru?->nama_guru ?? 'N/A',
                        'kelas' => $item->siswa?->kelas?->nama_kelas ?? '',
                        'bulan' => $item->bulan,
                        'tahun' => $item->tahun,
                        'kategori' => $item->kategori,
                        'deskripsi' => $item->deskripsi,
                        'status_utama' => $item->status_utama,
                        'rata_rata_kelas' => $rataRataKelas,
                        'template_deskripsi' => $item->template_deskripsi,
                        'kategori_details' => $item->kategoriDetails->map(function ($detail) {
                            return [
                                'id_perkembangan_kategori' => $detail->id_perkembangan_kategori,
                                'id_perkembangan' => $detail->id_perkembangan,
                                'nama_kategori' => $detail->nama_kategori,
                                'nilai' => $detail->nilai,
                                'status_utama' => $detail->status_utama,
                                'deskripsi' => $detail->deskripsi,
                                'created_at' => $detail->created_at?->format('Y-m-d H:i:s'),
                                'updated_at' => $detail->updated_at?->format('Y-m-d H:i:s'),
                            ];
                        })->values(),
                        'created_at' => $item->created_at?->format('Y-m-d H:i:s'),
                        'updated_at' => $item->updated_at?->format('Y-m-d H:i:s'),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $perkembangan,
                'count' => $perkembangan->count(),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single perkembangan detail
     */
    public function show($id)
    {
        try {
            $perkembangan = Perkembangan::with('siswa.kelas', 'guru', 'kategoriDetails')->find($id);

            if (!$perkembangan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Perkembangan tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id_perkembangan' => $perkembangan->id_perkembangan,
                    'id_guru' => $perkembangan->id_guru,
                    'nomor_induk_siswa' => $perkembangan->nomor_induk_siswa,
                    'nama_siswa' => $perkembangan->siswa?->nama_siswa,
                    'nama_guru' => $perkembangan->guru?->nama_guru,
                    'kelas' => $perkembangan->siswa?->kelas?->nama_kelas ?? '',
                    'bulan' => $perkembangan->bulan,
                    'tahun' => $perkembangan->tahun,
                    'kategori' => $perkembangan->kategori,
                    'deskripsi' => $perkembangan->deskripsi,
                    'status_utama' => $perkembangan->status_utama,
                    'rata_rata_kelas' => $this->getRataRataKelas($perkembangan),
                    'template_deskripsi' => $perkembangan->template_deskripsi,
                    'kategori_details' => $perkembangan->kategoriDetails->map(function ($detail) {
                        return [
                            'id_perkembangan_kategori' => $detail->id_perkembangan_kategori,
                            'id_perkembangan' => $detail->id_perkembangan,
                            'nama_kategori' => $detail->nama_kategori,
                            'nilai' => $detail->nilai,
                            'status_utama' => $detail->status_utama,
                            'deskripsi' => $detail->deskripsi,
                            'created_at' => $detail->created_at?->format('Y-m-d H:i:s'),
                            'updated_at' => $detail->updated_at?->format('Y-m-d H:i:s'),
                        ];
                    })->values(),
                    'created_at' => $perkembangan->created_at?->format('Y-m-d H:i:s'),
                    'updated_at' => $perkembangan->updated_at?->format('Y-m-d H:i:s'),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getRataRataKelas(Perkembangan $perkembangan): float
    {
        $idKelas = $perkembangan->siswa?->id_kelas;

        if (!$idKelas || !$perkembangan->bulan || !$perkembangan->tahun) {
            return 0;
        }

        return (float) Perkembangan::query()
            ->join('siswa', 'perkembangan.nomor_induk_siswa', '=', 'siswa.nomor_induk_siswa')
            ->where('siswa.id_kelas', $idKelas)
            ->where('perkembangan.bulan', $perkembangan->bulan)
            ->where('perkembangan.tahun', $perkembangan->tahun)
            ->avg(DB::raw("
                CASE COALESCE(perkembangan.status_utama, 'BSH')
                    WHEN 'BB' THEN 1
                    WHEN 'MB' THEN 2
                    WHEN 'BSH' THEN 3
                    WHEN 'BSB' THEN 4
                    ELSE 0
                END
            "));
    }
}
