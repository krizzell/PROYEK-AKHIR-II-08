<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Kelas extends Model
{
    protected $table = 'kelas';
    protected $primaryKey = 'id_kelas';
    protected $keyType = 'int';
    public $incrementing = true;
    protected $fillable = ['id_guru', 'nama_kelas'];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    public function siswa(): HasMany
    {
        return $this->hasMany(Siswa::class, 'id_kelas', 'id_kelas');
    }

    // Relasi many-to-many untuk guru yang mengampu kelas
    public function guruAmpuan(): BelongsToMany
    {
        return $this->belongsToMany(Guru::class, 'guru_kelas', 'id_kelas', 'id_guru');
    }
}
