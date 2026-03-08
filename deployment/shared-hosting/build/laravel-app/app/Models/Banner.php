<?php

namespace App\Models;

use App\Models\Concerns\HasPublishingScopes;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasPublishingScopes;

    protected $fillable = [
        'title',
        'slug',
        'subtitle',
        'cta_label',
        'cta_url',
        'image_url',
        'is_active',
        'sort_order',
        'status',
        'is_published',
        'is_featured',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
