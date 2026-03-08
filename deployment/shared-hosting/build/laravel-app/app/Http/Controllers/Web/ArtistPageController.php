<?php

namespace App\Http\Controllers\Web;

use App\Models\Artist;
use App\Models\PlatformSetting;
use Illuminate\Http\Request;

class ArtistPageController extends WebController
{
    public function index(Request $request)
    {
        $artists = Artist::query()
            ->withCount(['songs' => fn ($query) => $query->where('moderation_status', 'approved')])
            ->orderByDesc('monthly_listeners')
            ->paginate(12);

        return view('pages.artists.index', [
            'artists' => $artists,
            ...$this->interactionState($request),
        ]);
    }

    public function show(Request $request, Artist $artist)
    {
        $interactionState = $this->interactionState($request);
        $platformSettings = $interactionState['platformSettings'];

        $artist->load([
            'albums' => fn ($query) => $query->orderByDesc('release_date'),
            'songs' => fn ($query) => $query->with('album')->orderByDesc('approved_at'),
        ]);

        $approvedSongs = $artist->songs->where('moderation_status', 'approved')->values();
        $topTracks = $approvedSongs->sortByDesc('streams_count')->take(5)->values();
        $collaborators = Artist::query()
            ->whereKeyNot($artist->id)
            ->when($artist->genre, fn ($query) => $query->where('genre', $artist->genre))
            ->orderByDesc('monthly_listeners')
            ->take(8)
            ->get();
        $collaboratorIds = $collaborators->pluck('id');
        $relatedArtists = Artist::query()
            ->whereKeyNot($artist->id)
            ->when($collaboratorIds->isNotEmpty(), fn ($query) => $query->whereNotIn('id', $collaboratorIds))
            ->orderByDesc('monthly_listeners')
            ->take(6)
            ->get();

        $coverImage = $artist->albums->first()?->cover_image_url
            ?? $approvedSongs->first()?->cover_image_url
            ?? $artist->image_url;

        $stats = [
            'listeners' => $artist->monthly_listeners,
            'followers' => $artist->followers,
            'streams' => $approvedSongs->sum('streams_count'),
        ];

        return view('pages.artists.show', [
            'artist' => $artist,
            'songs' => $approvedSongs,
            'topTracks' => $topTracks,
            'albums' => $artist->albums,
            'coverImage' => $coverImage,
            'collaborators' => $collaborators,
            'relatedArtists' => $relatedArtists,
            'stats' => $stats,
            'featuredVideoId' => PlatformSetting::youtubeVideoId($platformSettings['artist_default_video'] ?? null),
            'playerQueue' => $this->serializeTracks($approvedSongs),
            ...$interactionState,
        ]);
    }
}
