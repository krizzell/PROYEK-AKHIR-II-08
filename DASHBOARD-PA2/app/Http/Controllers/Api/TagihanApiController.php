<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use Illuminate\Http\Request;

class TagihanApiController extends Controller
{
    /**
     * Get list of tagihan for authenticated user (orangtua)
     * Mobile app akan send nomor_induk_siswa via token
     */
    public function index(Request $request)
    {
        try {
            // Support both query parameter dan request body
            $nomor_induk_siswa = $request->query('nomor_induk_siswa') ?? $request->input('nomor_induk_siswa');
            
            if (!$nomor_induk_siswa) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'nomor_induk_siswa diperlukan'
                ], 400);
            }

            $tagihan = Tagihan::where('nomor_induk_siswa', $nomor_induk_siswa)
                ->with('siswa', 'siswa.kelas')
                ->orderBy('id_tagihan', 'desc')
                ->get()
                ->map(function ($item) {
                    $normalizedStatus = $item->payment_status ?: ($item->status ?: 'belum_bayar');

                    return [
                        'id_tagihan' => $item->id_tagihan,
                        'nomor_induk_siswa' => $item->nomor_induk_siswa,
                        'nama_siswa' => $item->siswa?->nama_siswa ?? '-',
                        'kelas' => $item->siswa?->kelas?->nama_kelas ?? '-',
                        'jumlah_tagihan' => $item->jumlah_tagihan,
                        'periode' => $item->periode,
                        'status' => $normalizedStatus,
                        'payment_status' => $normalizedStatus,
                        'transaction_id' => $item->transaction_id,
                        'payment_method' => $item->payment_method,
                        'payment_date' => optional($item->payment_date)->format('Y-m-d H:i:s'),
                        'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $tagihan,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get single tagihan detail
     */
    public function show($id)
    {
        try {
            $tagihan = Tagihan::with('siswa', 'siswa.kelas')->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id_tagihan' => $tagihan->id_tagihan,
                    'nomor_induk_siswa' => $tagihan->nomor_induk_siswa,
                    'nama_siswa' => $tagihan->siswa?->nama_siswa ?? '-',
                    'kelas' => $tagihan->siswa?->kelas?->nama_kelas ?? '-',
                    'jumlah_tagihan' => $tagihan->jumlah_tagihan,
                    'periode' => $tagihan->periode,
                    'payment_status' => $tagihan->payment_status ?: ($tagihan->status ?: 'belum_bayar'),
                    'transaction_id' => $tagihan->transaction_id,
                    'payment_method' => $tagihan->payment_method,
                    'payment_date' => optional($tagihan->payment_date)->format('Y-m-d H:i:s'),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tagihan tidak ditemukan',
            ], 404);
        }
    }
}
