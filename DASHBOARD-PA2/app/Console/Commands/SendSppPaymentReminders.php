<?php

namespace App\Console\Commands;

use App\Models\NotificationLog;
use App\Models\Tagihan;
use App\Services\FcmNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class SendSppPaymentReminders extends Command
{
    protected $signature = 'spp:send-reminders {--force : Kirim meskipun hari ini bukan tanggal 7}';

    protected $description = 'Kirim reminder pembayaran SPP pada tanggal 7 setiap bulan';

    public function handle(FcmNotificationService $fcm): int
    {
        $today = now();

        if (!$this->option('force') && (int) $today->format('j') !== 7) {
            $this->info('Reminder SPP hanya dikirim pada tanggal 7.');
            return self::SUCCESS;
        }

        $period = $this->periodeLabel($today);
        $deadline = $today->copy()->day(10);

        $tagihanList = Tagihan::query()
            ->with('siswa.akun')
            ->whereIn('periode', [$period, str_replace('SPP ', '', $period)])
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', '!=', 'lunas');
            })
            ->where(function ($query) {
                $query->whereNull('payment_status')
                    ->orWhere('payment_status', '!=', 'lunas');
            })
            ->get();

        $sentCount = 0;
        $skippedCount = 0;

        foreach ($tagihanList as $tagihan) {
            $akunList = ($tagihan->siswa?->akun ?? collect())
                ->where('role', 'orangtua')
                ->filter(fn ($akun) => filled($akun->fcm_token))
                ->unique(fn ($akun) => trim((string) $akun->fcm_token))
                ->values();

            if ($akunList->isEmpty()) {
                $skippedCount++;
                continue;
            }

            foreach ($akunList as $akun) {
                try {
                    $log = NotificationLog::firstOrCreate([
                        'type' => 'spp_payment_reminder',
                        'reference_type' => 'tagihan',
                        'reference_id' => $tagihan->id_tagihan,
                        'target_type' => 'akun',
                        'target_id' => $akun->id_akun,
                        'period' => $period,
                    ]);

                    if ($log->sent_at) {
                        $skippedCount++;
                        continue;
                    }

                    $title = 'Pengingat Pembayaran SPP';
                    $body = sprintf(
                        'Batas pembayaran %s tinggal 3 hari. Mohon lakukan pembayaran sebelum %s.',
                        $period,
                        $this->dateLabel($deadline)
                    );

                    $sent = $fcm->sendToToken(
                        $akun->fcm_token,
                        $title,
                        $body,
                        'payment_reminder',
                        [
                            'id_tagihan' => $tagihan->id_tagihan,
                            'periode' => $period,
                            'deadline' => $deadline->toDateString(),
                        ]
                    );

                    $log->update([
                        'sent_at' => $sent ? now() : null,
                        'error_message' => $sent ? null : 'FCM send failed',
                    ]);

                    $sent ? $sentCount++ : $skippedCount++;
                } catch (\Throwable $e) {
                    $skippedCount++;
                    Log::warning('Failed to send SPP reminder', [
                        'id_tagihan' => $tagihan->id_tagihan,
                        'id_akun' => $akun->id_akun,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        $this->info("Reminder SPP selesai. Terkirim: {$sentCount}, dilewati/gagal: {$skippedCount}.");
        return self::SUCCESS;
    }

    private function periodeLabel(Carbon $date): string
    {
        return 'SPP ' . $this->monthName((int) $date->format('n')) . ' ' . $date->format('Y');
    }

    private function dateLabel(Carbon $date): string
    {
        return $date->format('d') . ' ' . $this->monthName((int) $date->format('n')) . ' ' . $date->format('Y');
    }

    private function monthName(int $month): string
    {
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return $months[$month] ?? '';
    }
}
