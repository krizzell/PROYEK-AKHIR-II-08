<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengumuman extends Model
{
    protected $table = 'pengumuman';
    protected $primaryKey = 'id_pengumuman';
    protected $fillable = ['id_guru', 'judul', 'media', 'waktu_unggah', 'deskripsi'];

    protected $casts = [
        'waktu_unggah' => 'datetime',
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    public function mediaPaths(): array
    {
        return $this->normalizeMediaValue($this->getRawOriginal('media'));
    }

    public function mediaUrls(): array
    {
        return array_map(function (string $path) {
            return $this->resolveMediaUrl($path);
        }, $this->mediaPaths());
    }

    public function primaryMediaPath(): ?string
    {
        return $this->mediaPaths()[0] ?? null;
    }

    public function primaryMediaUrl(): ?string
    {
        $primaryPath = $this->primaryMediaPath();

        return $primaryPath ? $this->resolveMediaUrl($primaryPath) : null;
    }

    public function getMediaPathsAttribute(): array
    {
        return $this->mediaPaths();
    }

    public function getMediaUrlsAttribute(): array
    {
        return $this->mediaUrls();
    }

    public function getPrimaryMediaPathAttribute(): ?string
    {
        return $this->primaryMediaPath();
    }

    public function getPrimaryMediaUrlAttribute(): ?string
    {
        return $this->primaryMediaUrl();
    }

    protected function normalizeMediaValue(mixed $media): array
    {
        if (!is_string($media) || trim($media) === '') {
            return [];
        }

        $decoded = json_decode($media, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return array_values(array_filter($decoded));
        }

        return [$media];
    }

    protected function resolveMediaUrl(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset('storage/' . ltrim($path, '/'));
    }
}
