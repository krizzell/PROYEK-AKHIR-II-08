<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Tagihan extends Model
{
    public const DENDA_KETERLAMBATAN = 20000;

    protected $table = 'tagihan';
    protected $primaryKey = 'id_tagihan';
    protected $fillable = [
        'nomor_induk_siswa', 
        'jumlah_tagihan', 
        'periode', 
        'status',
        'transaction_id',
        'payment_method',
        'payment_date',
        'payment_status'
    ];

    protected $casts = [
        'payment_date' => 'datetime',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'nomor_induk_siswa', 'nomor_induk_siswa');
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'id_tagihan', 'id_tagihan');
    }

    public function getDendaKeterlambatanAttribute(): int
    {
        if ($this->status === 'lunas') {
            return 0;
        }

        $dueDate = $this->getTanggalJatuhTempo();
        if (!$dueDate) {
            return 0;
        }

        return now()->greaterThan($dueDate) ? self::DENDA_KETERLAMBATAN : 0;
    }

    public function getTotalPembayaranAttribute(): int
    {
        return (int) $this->jumlah_tagihan + $this->denda_keterlambatan;
    }

    public function getTanggalJatuhTempo(): ?Carbon
    {
        $periode = strtolower(trim((string) $this->periode));
        $periode = preg_replace('/^spp\s+/', '', $periode);

        $bulanMap = [
            'januari' => 1, 'jan' => 1,
            'februari' => 2, 'feb' => 2,
            'maret' => 3, 'mar' => 3,
            'april' => 4, 'apr' => 4,
            'mei' => 5, 'may' => 5,
            'juni' => 6, 'jun' => 6,
            'juli' => 7, 'jul' => 7,
            'agustus' => 8, 'agu' => 8, 'aug' => 8,
            'september' => 9, 'sep' => 9,
            'oktober' => 10, 'okt' => 10, 'oct' => 10,
            'november' => 11, 'nov' => 11,
            'desember' => 12, 'des' => 12, 'dec' => 12,
        ];

        $month = null;
        foreach ($bulanMap as $name => $number) {
            if (str_contains($periode, $name)) {
                $month = $number;
                break;
            }
        }

        preg_match('/20\d{2}/', $periode, $matches);
        $year = isset($matches[0]) ? (int) $matches[0] : null;

        if (!$month || !$year) {
            return null;
        }

        return Carbon::create($year, $month, 10, 23, 59, 59);
    }
}
