<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Akun extends Model
{
    protected $table = 'akun';
    protected $primaryKey = 'id_akun';
    protected $fillable = ['id_guru', 'nomor_induk_siswa', 'username', 'password', 'role', 'is_super_admin', 'fcm_token'];
    protected $hidden = ['password'];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'nomor_induk_siswa', 'nomor_induk_siswa');
    }
}
