<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Artist;

class ArtistController extends Controller
{
    public function index()
    {
        $artists = Artist::query()
            ->withCount('songs')
            ->orderByDesc('monthly_listeners')
            ->get()
            ->map(fn (Artist $artist) => $this->serializeArtistSummary($artist))
            ->values();

        return response()->json([
            'data' => $artists,
        ]);
    }

    public function show(Artist $artist)
    {
        $artist->load([
            'albums' => fn ($query) => $query->orderByDesc('release_date'),
            'songs' => fn ($query) => $query->with('album')->orderByDesc('is_featured')->orderBy('title'),
        ]);

        return response()->json([
            'data' => [
                'id' => $artist->id,
                'name' => $artist->name,
                'slug' => $artist->slug,
                'genre' => $artist->genre,
                'bio' => $artist->bio,
                'image_url' => $artist->image_url,
                'monthly_listeners' => $artist->monthly_listeners,
                'followers' => $artist->followers,
                'albums' => $artist->albums->map(fn ($album) => [
                    'id' => $album->id,
                    'title' => $album->title,
                    'slug' => $album->slug,
                    'genre' => $album->genre,
                    'cover_image_url' => $album->cover_image_url,
                    'release_date' => optional($album->release_date)->toDateString(),
                ])->values(),
                'songs' => $artist->songs->map(fn ($song) => [
                    'id' => $song->id,
                    'title' => $song->title,
                    'slug' => $song->slug,
                    'genre' => $song->genre,
                    'duration' => $song->duration,
                    'audio_url' => $song->audio_url,
                    'cover_image_url' => $song->cover_image_url,
                    'streams_count' => $song->streams_count,
                    'is_featured' => $song->is_featured,
                    'album' => $song->album ? [
                        'title' => $song->album->title,
                        'slug' => $song->album->slug,
                    ] : null,
                ])->values(),
            ],
        ]);
    }

    private function serializeArtistSummary(Artist $artist): array
    {
        return [
            'id' => $artist->id,
            'name' => $artist->name,
            'slug' => $artist->slug,
            'genre' => $artist->genre,
            'image_url' => $artist->image_url,
            'monthly_listeners' => $artist->monthly_listeners,
            'followers' => $artist->followers,
            'songs_count' => $artist->songs_count ?? 0,
        ];
    }
}
