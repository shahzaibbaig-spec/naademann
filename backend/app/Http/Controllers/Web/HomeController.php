<?php

namespace App\Http\Controllers\Web;

use App\Models\Artist;
use App\Models\Banner;
use App\Models\Genre;
use App\Models\Playlist;
use App\Models\PlatformSetting;
use App\Models\Song;
use Illuminate\Http\Request;

class HomeController extends WebController
{
    public function __invoke(Request $request)
    {
        $interactionState = $this->interactionState($request);
        $platformSettings = $interactionState['platformSettings'];

        $featuredSongs = Song::query()
            ->with(['artist', 'album'])
            ->where('moderation_status', 'approved')
            ->where('is_featured', true)
            ->orderByDesc('is_featured')
            ->orderByDesc('approved_at')
            ->take(8)
            ->get();

        $latestSongs = Song::query()
            ->with(['artist', 'album'])
            ->where('moderation_status', 'approved')
            ->orderByDesc('approved_at')
            ->orderByDesc('created_at')
            ->take(12)
            ->get();

        $artists = Artist::query()
            ->withCount(['songs' => fn ($query) => $query->where('moderation_status', 'approved')])
            ->orderByDesc('monthly_listeners')
            ->take(8)
            ->get();

        $genres = Genre::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $playlists = Playlist::query()
            ->with('user')
            ->where('is_public', true)
            ->orderByDesc('created_at')
            ->take(4)
            ->get();

        $banners = Banner::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $stats = [
            'listeners' => Artist::query()->sum('monthly_listeners'),
            'artists' => Artist::query()->count(),
            'tracks' => Song::query()->where('moderation_status', 'approved')->count(),
            'creators' => Artist::query()->whereNotNull('user_id')->count(),
        ];

        return view('pages.home', [
            'banners' => $banners,
            'featuredSongs' => $featuredSongs,
            'latestSongs' => $latestSongs,
            'artists' => $artists,
            'genres' => $genres,
            'playlists' => $playlists,
            'stats' => $stats,
            'homepageVideoId' => PlatformSetting::youtubeVideoId($platformSettings['homepage_video'] ?? null),
            'playerQueue' => $this->serializeQueueTracks($featuredSongs->isNotEmpty() ? $featuredSongs : $latestSongs),
            ...$interactionState,
        ]);
    }
}
