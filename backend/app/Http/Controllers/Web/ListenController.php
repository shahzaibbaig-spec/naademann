<?php

namespace App\Http\Controllers\Web;

use App\Models\Album;
use App\Models\Genre;
use App\Models\Playlist;
use App\Models\Song;
use Illuminate\Http\Request;

class ListenController extends WebController
{
    public function __invoke(Request $request)
    {
        $activeGenre = trim($request->string('genre')->toString());
        $activeAlbumSlug = trim($request->string('album')->toString());
        $activeTrackSlug = trim($request->string('track')->toString());

        $activeAlbum = $activeAlbumSlug !== ''
            ? Album::query()->with('artist')->where('slug', $activeAlbumSlug)->first()
            : null;

        $activeTrack = $activeTrackSlug !== ''
            ? Song::query()
                ->with(['artist', 'album'])
                ->where('moderation_status', 'approved')
                ->where('slug', $activeTrackSlug)
                ->first()
            : null;

        $songs = Song::query()
            ->with(['artist', 'album'])
            ->where('moderation_status', 'approved')
            ->when($activeGenre !== '', fn ($query) => $query->where('genre', $activeGenre))
            ->when($activeAlbum, fn ($query) => $query->where('album_id', $activeAlbum->id))
            ->when($activeTrack, fn ($query) => $query->whereKey($activeTrack->id))
            ->orderByDesc('is_featured')
            ->orderByDesc('approved_at')
            ->get();

        $playlists = Playlist::query()
            ->with('user')
            ->when($request->user(), function ($query) use ($request): void {
                $query->where(function ($builder) use ($request): void {
                    $builder->where('is_public', true)->orWhere('user_id', $request->user()->id);
                });
            }, fn ($query) => $query->where('is_public', true))
            ->orderByDesc('created_at')
            ->get();

        $genres = Genre::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $playlistSongs = Song::query()
            ->with(['artist', 'album'])
            ->whereIn('id', $playlists->flatMap(fn (Playlist $playlist) => $playlist->song_ids ?? [])->unique()->values())
            ->where('moderation_status', 'approved')
            ->get()
            ->keyBy('id');

        $playlists->each(function (Playlist $playlist) use ($playlistSongs): void {
            $queue = collect($playlist->song_ids ?? [])
                ->map(fn (int $songId) => $playlistSongs->get($songId))
                ->filter()
                ->values();

            $playlist->setAttribute('queue_payload', $this->serializeQueueTracks($queue));
        });

        $activePlaylistSlug = $request->string('playlist')->toString();
        $activePlaylist = $activePlaylistSlug !== '' ? $playlists->firstWhere('slug', $activePlaylistSlug) : null;

        return view('pages.listen', [
            'songs' => $songs,
            'playlists' => $playlists,
            'genres' => $genres,
            'activeGenre' => $activeGenre,
            'activeAlbum' => $activeAlbum,
            'activeTrack' => $activeTrack,
            'activePlaylistSlug' => $activePlaylistSlug,
            'playerQueue' => $activePlaylist?->queue_payload ?? $this->serializeQueueTracks($songs),
            ...$this->interactionState($request),
        ]);
    }
}
