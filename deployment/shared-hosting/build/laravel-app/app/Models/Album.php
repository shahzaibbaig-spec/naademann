<?php

namespace App\Models;

use App\Models\Concerns\HasPublishingScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Album extends Model
{
    use HasPublishingScopes;

    protected $fillable = [
        'artist_id',
        'artist_profile_id',
        'title',
        'slug',
        'genre',
        'genre_id',
        'description',
        'cover_image_url',
        'release_date',
        'status',
        'is_published',
        'is_featured',
        'published_at',
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

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    public function artistProfile(): BelongsTo
    {
        return $this->belongsTo(ArtistProfile::class);
    }

    public function genreRecord(): BelongsTo
    {
        return $this->belongsTo(Genre::class, 'genre_id');
    }

    public function songs(): HasMany
    {
        return $this->hasMany(Song::class);
    }

    public function tracks(): HasMany
    {
        return $this->hasMany(Track::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
