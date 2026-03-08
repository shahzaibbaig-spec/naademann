<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Song extends Model
{
    protected $fillable = [
        'artist_id',
        'album_id',
        'title',
        'slug',
        'genre',
        'description',
        'lyrics',
        'duration',
        'audio_url',
        'cover_image_url',
        'release_date',
        'is_featured',
        'moderation_status',
        'approved_at',
        'published_at',
        'streams_count',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'release_date' => 'date',
            'approved_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Song $song): void {
            if (!filled($song->slug) && filled($song->title)) {
                $song->slug = static::uniqueSlug($song->title);
            }
        });
    }

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    public function streams(): HasMany
    {
        return $this->hasMany(Stream::class);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    private static function uniqueSlug(string $title): string
    {
        $base = Str::slug($title) ?: 'track';
        $slug = $base;
        $index = 1;

        while (static::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$index;
            $index++;
        }

        return $slug;
    }
}
