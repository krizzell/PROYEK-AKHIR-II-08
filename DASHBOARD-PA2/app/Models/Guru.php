<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Guru extends Model
{
    protected $table = 'guru';
    protected $primaryKey = 'id_guru';
    protected $keyType = 'int';
    public $incrementing = true;
    protected $fillable = [
        'foto_guru',
        'nip_guru',
        'nama_guru',
        'jenis_kelamin',
        'tanggal_lahir',
        'alamat',
        'no_hp',
        'email',
        'jabatan',
        'pendidikan_terakhir',
        'jurusan'
    ];

    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class, 'id_guru', 'id_guru');
    }

    // Relasi many-to-many untuk kelas yang diampu
    public function kelasAmpuan(): BelongsToMany
    {
        return $this->belongsToMany(Kelas::class, 'guru_kelas', 'id_guru', 'id_kelas');
    }

    public function akun(): HasMany
    {
        return $this->hasMany(Akun::class, 'id_guru', 'id_guru');
    }

    public function pengumuman(): HasMany
    {
        return $this->hasMany(Pengumuman::class, 'id_guru', 'id_guru');
    }

    public function perkembangan(): HasMany
    {
        return $this->hasMany(Perkembangan::class, 'id_guru', 'id_guru');
    }
}
