<?php

namespace App\Models;

use App\Models\Concerns\HasPublishingScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Genre extends Model
{
    use HasPublishingScopes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_url',
        'color',
        'status',
        'is_published',
        'is_featured',
        'published_at',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function artistProfiles(): HasMany
    {
        return $this->hasMany(ArtistProfile::class);
    }

    public function albums(): HasMany
    {
        return $this->hasMany(Album::class);
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
