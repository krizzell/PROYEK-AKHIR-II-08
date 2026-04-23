<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class PembayaranApiController extends Controller
{
    public function __construct()
    {
        // Set Midtrans Config
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$clientKey = config('services.midtrans.client_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Create payment transaction & get Snap token
     * POST /api/pembayaran/create
     */
    public function create(Request $request)
    {
        try {
            $idTagihan = $request->input('invoice_id')
                ?? $request->input('id_tagihan')
                ?? $request->input('id');
            $userId = $request->input('user_id');

            if (!$idTagihan) {
                $raw = json_decode($request->getContent(), true);
                if (is_array($raw)) {
                    $idTagihan = $raw['invoice_id'] ?? $raw['id_tagihan'] ?? $raw['id'] ?? null;
                    $userId = $raw['user_id'] ?? $userId;
                }
            }

            if (!$idTagihan || !Tagihan::where('id_tagihan', $idTagihan)->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'invoice_id tidak valid',
                ], 422);
            }

            $tagihan = Tagihan::with('siswa')->findOrFail($idTagihan);

            // Optional security check: user_id harus cocok dengan pemilik tagihan
            if ($userId) {
                $akun = Akun::find($userId);
                if (!$akun || $akun->nomor_induk_siswa !== $tagihan->nomor_induk_siswa) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'User tidak berhak membayar tagihan ini',
                    ], 403);
                }
            }

            // Check if already paid
            $currentStatus = $tagihan->payment_status ?: $tagihan->status;
            if ($currentStatus === 'lunas') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tagihan sudah lunas',
                ], 400);
            }

            // Generate transaction ID (unique)
            $transactionId = 'INV-' . $tagihan->id_tagihan . '-' . time();

            // Prepare payment details
            $transaction_details = array(
                'order_id' => $transactionId,
                'gross_amount' => (int)$tagihan->jumlah_tagihan,
            );

            $customer_details = array(
                'first_name' => $tagihan->siswa->nama_siswa,
                'email' => 'orangtua@school.com',
                'phone' => '6200000000000',
            );

            $payload = array(
                'transaction_details' => $transaction_details,
                'customer_details' => $customer_details,
                'item_details' => [
                    [
                        'id' => 'tagihan-' . $tagihan->id_tagihan,
                        'price' => (int)$tagihan->jumlah_tagihan,
                        'quantity' => 1,
                        'name' => 'SPP ' . $tagihan->periode . ' - ' . $tagihan->siswa->nama_siswa,
                    ]
                ],
            );

            // Create transaction and get token + redirect_url
            $midtransTransaction = Snap::createTransaction($payload);
            $snapToken = $midtransTransaction->token ?? null;
            $redirectUrl = $midtransTransaction->redirect_url ?? null;

            if (!$snapToken || !$redirectUrl) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal membuat transaksi Midtrans',
                ], 500);
            }

            // Store transaction_id temporarily in tagihan
            $tagihan->update([
                'transaction_id' => $transactionId,
                'payment_method' => $request->input('payment_method', 'snap'),
                'status' => 'belum_bayar',
                'payment_status' => 'belum_bayar',
            ]);

            // Sandbox automation: allow backend-only payment flow for mobile testing/demo.
            // This will mark payment as paid immediately without opening Midtrans page.
            $isProduction = (bool) config('services.midtrans.is_production', false);
            $autoSuccess = !$isProduction && $request->boolean('auto_success', true);

            if ($autoSuccess) {
                $tagihan->update([
                    'status' => 'lunas',
                    'payment_status' => 'lunas',
                    'payment_date' => now(),
                ]);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'invoice_id' => $tagihan->id_tagihan,
                    'snap_token' => $snapToken,
                    'redirect_url' => $redirectUrl,
                    'transaction_id' => $transactionId,
                    'order_id' => $transactionId,
                    'gross_amount' => $tagihan->jumlah_tagihan,
                    'payment_status' => $autoSuccess ? 'lunas' : 'belum_bayar',
                    'auto_success' => $autoSuccess,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Check payment status
     * GET /api/pembayaran/{transaction_id}/status
     */
    public function status($transaction_id)
    {
        try {
            // Find tagihan by transaction_id dulu
            $tagihan = Tagihan::where('transaction_id', $transaction_id)->firstOrFail();

            // Jika payment_status sudah "lunas" di database, langsung return tanpa query Midtrans
            if ($tagihan->payment_status === 'lunas') {
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'transaction_id' => $transaction_id,
                        'payment_status' => 'lunas',
                        'payment_method' => $tagihan->payment_method ?? 'unknown',
                        'transaction_status' => 'settlement',
                        'gross_amount' => $tagihan->jumlah_tagihan,
                        'currency' => 'IDR',
                    ],
                ], 200);
            }

            // Get status from Midtrans untuk status pending
            try {
                $status = Transaction::status($transaction_id);
                $paymentStatus = $this->mapMidtransStatus($status->transaction_status);

                // Update tagihan status jika berubah
                if ($tagihan->payment_status !== $paymentStatus) {
                    $tagihan->update([
                        'payment_status' => $paymentStatus,
                        'status' => $paymentStatus,
                        'payment_method' => $status->payment_type ?? null,
                    ]);

                    // If payment success, update payment_date
                    if ($paymentStatus === 'lunas') {
                        $tagihan->update([
                            'payment_date' => now(),
                        ]);
                    }
                }

                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'transaction_id' => $transaction_id,
                        'payment_status' => $paymentStatus,
                        'payment_method' => $status->payment_type ?? 'unknown',
                        'transaction_status' => $status->transaction_status,
                        'gross_amount' => $status->gross_amount,
                        'currency' => $status->currency ?? 'IDR',
                    ],
                ], 200);
            } catch (\Exception $midtransError) {
                // Jika Midtrans error (transaction belum ada di Midtrans), kembalikan status dari database
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'transaction_id' => $transaction_id,
                        'payment_status' => $tagihan->payment_status ?? 'belum_bayar',
                        'payment_method' => $tagihan->payment_method ?? 'unknown',
                        'transaction_status' => $tagihan->payment_status ?? 'belum_bayar',
                        'gross_amount' => $tagihan->jumlah_tagihan,
                        'currency' => 'IDR',
                    ],
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Status tidak ditemukan: ' . $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Webhook handler from Midtrans
     * POST /webhook/midtrans
     * No authentication required - Midtrans verifies signature
     */
    public function webhook(Request $request)
    {
        try {
            $notif = json_decode($request->getContent());
            $transactionId = $notif->order_id ?? null;
            $statusCode = $notif->status_code ?? null;
            $grossAmount = $notif->gross_amount ?? null;
            $signatureKey = $notif->signature_key ?? null;

            if (!$transactionId) {
                return response()->json(['status' => 'error'], 400);
            }

            // Verify Midtrans signature for security
            if ($statusCode && $grossAmount && $signatureKey) {
                $serverKey = config('services.midtrans.server_key');
                $expectedSignature = hash('sha512', $transactionId . $statusCode . $grossAmount . $serverKey);
                if (!hash_equals($expectedSignature, $signatureKey)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid signature',
                    ], 403);
                }
            }

            // Get status from Midtrans
            $status = Transaction::status($transactionId);
            $paymentStatus = $this->mapMidtransStatus($status->transaction_status);

            // Find tagihan
            $tagihan = Tagihan::where('transaction_id', $transactionId)->first();

            if (!$tagihan) {
                \Log::warning("Webhook received for unknown transaction: $transactionId");
                return response()->json(['status' => 'warning'], 200);
            }

            // Update tagihan dengan status terbaru
            $updateData = [
                'payment_status' => $paymentStatus,
                'status' => $paymentStatus,
                'payment_method' => $status->payment_type ?? null,
            ];

            // Set payment_date jika lunas
            if ($paymentStatus === 'lunas') {
                $updateData['payment_date'] = now();
            }

            $tagihan->update($updateData);

            // Log webhook
            \Log::info("Webhook processed - Transaction: $transactionId, Status: $paymentStatus");

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            \Log::error("Webhook error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Map Midtrans transaction status to application payment_status
     * Status values: belum_bayar, lunas only (no pending/gagal for user display)
     */
    private function mapMidtransStatus($transactionStatus)
    {
        switch ($transactionStatus) {
            case 'capture':
            case 'settlement':
                return 'lunas';
            case 'pending':
            case 'expire':
            case 'cancel':
            case 'deny':
                return 'belum_bayar'; // Failed or expired → back to unpaid
            default:
                return 'belum_bayar';
        }
    }
}
