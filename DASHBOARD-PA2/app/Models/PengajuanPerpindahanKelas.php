<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanPerpindahanKelas extends Model
{
    protected $table = 'pengajuan_perpindahan_kelas';
    protected $primaryKey = 'id_pengajuan';

    protected $fillable = [
        'nomor_induk_siswa',
        'id_kelas_asal',
        'id_kelas_tujuan',
        'id_guru_pengaju',
        'id_guru_pemroses',
        'alasan',
        'status',
        'alasan_penolakan',
        'tanggal_pengajuan',
        'tanggal_diproses',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'datetime',
        'tanggal_diproses' => 'datetime',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'nomor_induk_siswa', 'nomor_induk_siswa');
    }

    public function kelasAsal(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'id_kelas_asal', 'id_kelas');
    }

    public function kelasTujuan(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'id_kelas_tujuan', 'id_kelas');
    }

    public function guruPengaju(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'id_guru_pengaju', 'id_guru');
    }

    public function guruPemroses(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'id_guru_pemroses', 'id_guru');
    }
}
