<?php

namespace App\Http\Controllers\Api;

use App\Models\Playlist;
use App\Models\Song;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlaylistController extends Controller
{
    public function index(Request $request)
    {
        $user = $this->resolveUser($request);

        $playlists = Playlist::query()
            ->with('user')
            ->when($user, function ($query) use ($user): void {
                $query->where(function ($builder) use ($user): void {
                    $builder->where('is_public', true)->orWhere('user_id', $user->id);
                });
            }, fn ($query) => $query->where('is_public', true))
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Playlist $playlist) => $this->serializePlaylistSummary($playlist))
            ->values();

        return response()->json([
            'data' => $playlists,
        ]);
    }

    public function store(Request $request)
    {
        $user = $this->resolveUser($request);

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'cover_image_url' => ['nullable', 'url'],
            'song_ids' => ['required', 'array', 'min:1'],
            'song_ids.*' => ['integer', 'exists:songs,id'],
            'is_public' => ['sometimes', 'boolean'],
        ]);

        $playlist = Playlist::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'slug' => Str::slug($data['name']).'-'.Str::lower(Str::random(6)),
            'description' => $data['description'] ?? null,
            'cover_image_url' => $data['cover_image_url'] ?? null,
            'song_ids' => array_values($data['song_ids']),
            'is_public' => $data['is_public'] ?? true,
        ]);

        return response()->json([
            'message' => 'Playlist created.',
            'data' => $this->serializePlaylist($playlist->fresh()),
        ], 201);
    }

    public function show(Playlist $playlist, Request $request)
    {
        $user = $this->resolveUser($request);

        if (!$playlist->is_public && (!$user || $playlist->user_id !== $user->id)) {
            return response()->json(['message' => 'Playlist not found.'], 404);
        }

        return response()->json([
            'data' => $this->serializePlaylist($playlist),
        ]);
    }

    private function serializePlaylistSummary(Playlist $playlist): array
    {
        $firstSongId = $playlist->song_ids[0] ?? null;
        $previewSong = $firstSongId ? Song::query()->find($firstSongId) : null;

        return [
            'id' => $playlist->id,
            'name' => $playlist->name,
            'slug' => $playlist->slug,
            'description' => $playlist->description,
            'cover_image_url' => $playlist->cover_image_url ?: $previewSong?->cover_image_url,
            'songs_count' => count($playlist->song_ids ?? []),
            'is_public' => $playlist->is_public,
            'owner' => $playlist->user ? [
                'id' => $playlist->user->id,
                'name' => $playlist->user->name,
                'avatar_url' => $playlist->user->avatar_url,
            ] : null,
        ];
    }

    private function serializePlaylist(Playlist $playlist): array
    {
        $playlist->loadMissing('user');
        $songIds = $playlist->song_ids ?? [];
        $songs = Song::query()
            ->with(['artist', 'album'])
            ->whereIn('id', $songIds)
            ->get()
            ->sortBy(fn (Song $song) => array_search($song->id, $songIds, true))
            ->values()
            ->map(fn (Song $song) => [
                'id' => $song->id,
                'title' => $song->title,
                'slug' => $song->slug,
                'genre' => $song->genre,
                'duration' => $song->duration,
                'audio_url' => $song->audio_url,
                'cover_image_url' => $song->cover_image_url,
                'artist' => $song->artist ? [
                    'name' => $song->artist->name,
                    'slug' => $song->artist->slug,
                ] : null,
                'album' => $song->album ? [
                    'title' => $song->album->title,
                    'slug' => $song->album->slug,
                ] : null,
            ]);

        return [
            'id' => $playlist->id,
            'name' => $playlist->name,
            'slug' => $playlist->slug,
            'description' => $playlist->description,
            'cover_image_url' => $playlist->cover_image_url,
            'is_public' => $playlist->is_public,
            'owner' => $playlist->user ? [
                'id' => $playlist->user->id,
                'name' => $playlist->user->name,
                'avatar_url' => $playlist->user->avatar_url,
            ] : null,
            'songs' => $songs,
        ];
    }

    private function resolveUser(Request $request): ?User
    {
        $token = $request->bearerToken() ?: $request->input('token');

        if (!$token) {
            return null;
        }

        return User::query()->where('api_token', $token)->first();
    }
}
