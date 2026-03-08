<?php

namespace App\Http\Controllers\Web\Ajax;

use App\Http\Controllers\Web\WebController;
use App\Models\Artist;
use App\Models\Playlist;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InteractionController extends WebController
{
    public function playlists(Request $request)
    {
        return response()->json([
            'playlists' => $request->user()
                ->playlists()
                ->orderBy('name')
                ->get(['id', 'name', 'slug']),
        ]);
    }

    public function toggleFavorite(Request $request, Song $song)
    {
        $favorites = $request->user()->favoriteSongs();
        $isFavorite = $favorites->where('songs.id', $song->id)->exists();

        if ($isFavorite) {
            $favorites->detach($song->id);
        } else {
            $favorites->attach($song->id);
        }

        return response()->json([
            'favorited' => !$isFavorite,
            'song_id' => $song->id,
        ]);
    }

    public function toggleFollow(Request $request, Artist $artist)
    {
        $follows = $request->user()->followedArtists();
        $isFollowing = $follows->where('artists.id', $artist->id)->exists();

        if ($isFollowing) {
            $follows->detach($artist->id);
            $artist->decrement('followers');
        } else {
            $follows->attach($artist->id);
            $artist->increment('followers');
        }

        return response()->json([
            'following' => !$isFollowing,
            'artist_id' => $artist->id,
        ]);
    }

    public function addToPlaylist(Request $request, Song $song)
    {
        $data = $request->validate([
            'playlist_id' => ['nullable', 'integer'],
            'playlist_name' => ['nullable', 'string', 'max:255'],
        ]);

        $playlist = null;

        if (!empty($data['playlist_id'])) {
            $playlist = $request->user()->playlists()->findOrFail($data['playlist_id']);
        }

        if (!$playlist && !empty($data['playlist_name'])) {
            $playlist = Playlist::create([
                'user_id' => $request->user()->id,
                'name' => $data['playlist_name'],
                'slug' => Str::slug($data['playlist_name']).'-'.Str::lower(Str::random(5)),
                'description' => 'Created from the Naad-e-Maan web player.',
                'song_ids' => [],
                'is_public' => true,
            ]);
        }

        abort_unless($playlist, 422, 'Choose a playlist or enter a new playlist name.');

        $songIds = collect($playlist->song_ids ?? [])
            ->push($song->id)
            ->unique()
            ->values()
            ->all();

        $playlist->update([
            'song_ids' => $songIds,
        ]);

        return response()->json([
            'message' => 'Song added to playlist.',
            'playlist' => [
                'id' => $playlist->id,
                'name' => $playlist->name,
                'slug' => $playlist->slug,
            ],
        ]);
    }
}
