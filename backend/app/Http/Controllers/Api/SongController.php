<?php

namespace App\Http\Controllers\Api;

use App\Models\Song;
use App\Models\Stream;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SongController extends Controller
{
    public function index(Request $request)
    {
        $query = Song::query()->with(['artist', 'album']);

        if ($search = trim((string) $request->query('search'))) {
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('genre', 'like', "%{$search}%")
                    ->orWhereHas('artist', fn ($artistQuery) => $artistQuery->where('name', 'like', "%{$search}%"));
            });
        }

        if ($genre = trim((string) $request->query('genre'))) {
            $query->where('genre', $genre);
        }

        if ($artistSlug = trim((string) $request->query('artist'))) {
            $query->whereHas('artist', fn ($artistQuery) => $artistQuery->where('slug', $artistSlug));
        }

        $songs = $query
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Song $song) => $this->serializeSong($song))
            ->values();

        return response()->json([
            'data' => $songs,
            'meta' => [
                'genres' => Song::query()->select('genre')->distinct()->orderBy('genre')->pluck('genre')->values(),
                'search' => $request->query('search'),
                'genre' => $request->query('genre'),
                'artist' => $request->query('artist'),
            ],
        ]);
    }

    public function show(Song $song)
    {
        $song->load(['artist', 'album']);

        return response()->json([
            'data' => $this->serializeSong($song),
        ]);
    }

    public function genres()
    {
        return response()->json([
            'data' => Song::query()->select('genre')->distinct()->orderBy('genre')->pluck('genre')->values(),
        ]);
    }

    public function stream(Request $request, Song $song)
    {
        DB::transaction(function () use ($request, $song): void {
            Stream::create([
                'song_id' => $song->id,
                'user_id' => optional($this->resolveUserId($request))->id,
                'ip_address' => $request->ip(),
                'played_at' => now(),
            ]);

            $song->increment('streams_count');
        });

        $song->refresh()->load(['artist', 'album']);

        return response()->json([
            'message' => 'Stream recorded.',
            'data' => $this->serializeSong($song),
        ]);
    }

    private function serializeSong(Song $song): array
    {
        $releaseDate = optional($song->album?->release_date)->toDateString()
            ?? optional($song->created_at)->toDateString();

        return [
            'id' => $song->id,
            'title' => $song->title,
            'slug' => $song->slug,
            'genre' => $song->genre,
            'duration' => $song->duration,
            'release_date' => $releaseDate,
            'audio_url' => $song->audio_url,
            'cover_image_url' => $song->cover_image_url,
            'is_featured' => $song->is_featured,
            'streams_count' => $song->streams_count,
            'artist' => $song->artist ? [
                'id' => $song->artist->id,
                'name' => $song->artist->name,
                'slug' => $song->artist->slug,
                'genre' => $song->artist->genre,
                'image_url' => $song->artist->image_url,
            ] : null,
            'album' => $song->album ? [
                'id' => $song->album->id,
                'title' => $song->album->title,
                'slug' => $song->album->slug,
                'cover_image_url' => $song->album->cover_image_url,
                'release_date' => optional($song->album->release_date)->toDateString(),
            ] : null,
        ];
    }

    private function resolveUserId(Request $request)
    {
        $token = $request->bearerToken() ?: $request->input('token');

        if (!$token) {
            return null;
        }

        return \App\Models\User::query()->where('api_token', $token)->first();
    }
}
