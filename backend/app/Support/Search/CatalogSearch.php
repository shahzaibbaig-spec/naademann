<?php

namespace App\Support\Search;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Genre;
use App\Models\Song;

class CatalogSearch
{
    public function search(string $query, int $limit = 6): array
    {
        $query = trim($query);
        $limit = max(1, $limit);

        if ($query === '') {
            return [
                'query' => '',
                'tracks' => collect(),
                'artists' => collect(),
                'albums' => collect(),
                'genres' => collect(),
            ];
        }

        $tracks = Song::query()
            ->with(['artist', 'album'])
            ->where('moderation_status', 'approved')
            ->where(function ($builder) use ($query): void {
                $builder
                    ->where('title', 'like', "%{$query}%")
                    ->orWhere('genre', 'like', "%{$query}%")
                    ->orWhereHas('artist', fn ($artistQuery) => $artistQuery->where('name', 'like', "%{$query}%"))
                    ->orWhereHas('album', fn ($albumQuery) => $albumQuery->where('title', 'like', "%{$query}%"));
            })
            ->orderByDesc('is_featured')
            ->orderByDesc('approved_at')
            ->take($limit)
            ->get();

        $artists = Artist::query()
            ->where(function ($builder) use ($query): void {
                $builder
                    ->where('name', 'like', "%{$query}%")
                    ->orWhere('genre', 'like', "%{$query}%")
                    ->orWhere('bio', 'like', "%{$query}%");
            })
            ->orderByDesc('monthly_listeners')
            ->take($limit)
            ->get();

        $albums = Album::query()
            ->with('artist')
            ->where(function ($builder) use ($query): void {
                $builder
                    ->where('title', 'like', "%{$query}%")
                    ->orWhere('genre', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhereHas('artist', fn ($artistQuery) => $artistQuery->where('name', 'like', "%{$query}%"));
            })
            ->orderByDesc('release_date')
            ->take($limit)
            ->get();

        $genres = Genre::query()
            ->where('is_active', true)
            ->where(function ($builder) use ($query): void {
                $builder
                    ->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->orderBy('sort_order')
            ->take($limit)
            ->get();

        return [
            'query' => $query,
            'tracks' => $tracks,
            'artists' => $artists,
            'albums' => $albums,
            'genres' => $genres,
        ];
    }
}
