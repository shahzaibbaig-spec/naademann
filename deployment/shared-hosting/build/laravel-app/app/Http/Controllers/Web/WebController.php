<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

abstract class WebController extends Controller
{
    protected function interactionState(Request $request): array
    {
        $user = $request->user();

        return [
            'favoriteSongIds' => $user ? $user->favoriteSongs()->pluck('songs.id')->all() : [],
            'followedArtistIds' => $user ? $user->followedArtists()->pluck('artists.id')->all() : [],
            'userPlaylists' => $user ? $user->playlists()->orderBy('name')->get(['id', 'name', 'slug']) : collect(),
            'platformSettings' => PlatformSetting::query()->pluck('value', 'key'),
        ];
    }

    protected function serializeTracks(Collection $songs): array
    {
        return $songs->map(fn (Song $song) => [
            'id' => $song->id,
            'title' => $song->title,
            'slug' => $song->slug,
            'genre' => $song->genre,
            'duration' => $song->duration,
            'audio_url' => $song->audio_url,
            'cover_image_url' => $song->cover_image_url,
            'streams_count' => $song->streams_count,
            'artist' => $song->artist ? [
                'id' => $song->artist->id,
                'name' => $song->artist->name,
                'slug' => $song->artist->slug,
            ] : null,
            'album' => $song->album ? [
                'id' => $song->album->id,
                'title' => $song->album->title,
                'slug' => $song->album->slug,
            ] : null,
        ])->values()->all();
    }
}
