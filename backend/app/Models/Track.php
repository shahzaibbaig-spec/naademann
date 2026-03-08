<?php

namespace App\Models;

use App\Models\Concerns\HasPublishingScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Track extends Model
{
    use HasPublishingScopes;

    protected $fillable = [
        'artist_profile_id',
        'genre_id',
        'album_id',
        'title',
        'slug',
        'description',
        'lyrics',
        'duration',
        'audio_url',
        'cover_image_url',
        'release_date',
        'status',
        'is_published',
        'is_featured',
        'published_at',
        'views_count',
    ];

    protected function casts(): array
    {
        return [
            'release_date' => 'date',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Track $track): void {
            if (!filled($track->slug) && filled($track->title)) {
                $track->slug = static::uniqueSlug($track->title);
            }
        });
    }

    public function artistProfile(): BelongsTo
    {
        return $this->belongsTo(ArtistProfile::class);
    }

    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    public function trackViews(): HasMany
    {
        return $this->hasMany(TrackView::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function playlists(): BelongsToMany
    {
        return $this->belongsToMany(Playlist::class, 'playlist_track')
            ->withPivot('position')
            ->withTimestamps()
            ->orderByPivot('position');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    private static function uniqueSlug(string $value): string
    {
        $base = Str::slug($value) ?: 'track';
        $slug = $base;
        $index = 1;

        while (static::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$index;
            $index++;
        }

        return $slug;
    }
}
