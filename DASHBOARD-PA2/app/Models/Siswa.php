<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    protected $table = 'siswa';
    protected $primaryKey = 'nomor_induk_siswa';
    protected $fillable = ['nomor_induk_siswa', 'id_kelas', 'nama_siswa', 'foto_siswa', 'nama_orgtua', 'tgl_lahir', 'jenis_kelamin', 'alamat'];
    public $incrementing = false;

    protected $casts = [
        'tgl_lahir' => 'date',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function akun(): HasMany
    {
        return $this->hasMany(Akun::class, 'nomor_induk_siswa', 'nomor_induk_siswa');
    }

    public function perkembangan(): HasMany
    {
        return $this->hasMany(Perkembangan::class, 'nomor_induk_siswa', 'nomor_induk_siswa');
    }

    public function tagihan(): HasMany
    {
        return $this->hasMany(Tagihan::class, 'nomor_induk_siswa', 'nomor_induk_siswa');
    }

    public function pengajuanPerpindahan(): HasMany
    {
        return $this->hasMany(PengajuanPerpindahanKelas::class, 'nomor_induk_siswa', 'nomor_induk_siswa');
    }
}
