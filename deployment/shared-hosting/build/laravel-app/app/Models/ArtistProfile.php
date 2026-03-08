<?php

namespace App\Models;

use App\Models\Concerns\HasPublishingScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Str;

class ArtistProfile extends Model
{
    use HasPublishingScopes;

    protected $fillable = [
        'user_id',
        'genre_id',
        'name',
        'slug',
        'bio',
        'avatar_url',
        'cover_image_url',
        'status',
        'is_published',
        'is_featured',
        'published_at',
        'monthly_listeners',
        'followers_count',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ArtistProfile $artistProfile): void {
            if (!filled($artistProfile->slug) && filled($artistProfile->name)) {
                $artistProfile->slug = static::uniqueSlug($artistProfile->name);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    public function albums(): HasMany
    {
        return $this->hasMany(Album::class);
    }

    public function tracks(): HasMany
    {
        return $this->hasMany(Track::class);
    }

    public function follows(): HasMany
    {
        return $this->hasMany(Follow::class);
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows')->withTimestamps();
    }

    public function trackViews(): HasManyThrough
    {
        return $this->hasManyThrough(TrackView::class, Track::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    private static function uniqueSlug(string $value): string
    {
        $base = Str::slug($value) ?: 'artist';
        $slug = $base;
        $index = 1;

        while (static::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$index;
            $index++;
        }

        return $slug;
    }
}
