<?php

namespace App\Models;

use App\Models\Concerns\HasPublishingScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Playlist extends Model
{
    use HasPublishingScopes;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'cover_image_url',
        'song_ids',
        'is_public',
        'status',
        'is_published',
        'is_featured',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'song_ids' => 'array',
            'is_public' => 'boolean',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tracks(): BelongsToMany
    {
        return $this->belongsToMany(Track::class, 'playlist_track')
            ->withPivot('position')
            ->withTimestamps()
            ->orderByPivot('position');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
