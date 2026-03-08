<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Web\WebController;
use App\Models\Artist;
use App\Models\Banner;
use App\Models\Genre;
use App\Models\Song;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends WebController
{
    public function __invoke(Request $request)
    {
        $statusCounts = [
            'draft' => Song::query()->where('moderation_status', 'draft')->count(),
            'pending' => Song::query()->where('moderation_status', 'pending')->count(),
            'approved' => Song::query()->where('moderation_status', 'approved')->count(),
            'rejected' => Song::query()->where('moderation_status', 'rejected')->count(),
        ];

        return view('admin.dashboard', [
            'metrics' => [
                'users' => User::query()->count(),
                'creators' => User::query()->where('role', 'creator')->count(),
                'admins' => User::query()->where('role', 'admin')->count(),
                'artists' => Artist::query()->count(),
                'tracks' => Song::query()->count(),
                'pending_tracks' => $statusCounts['pending'],
                'published_tracks' => $statusCounts['approved'],
                'genres' => Genre::query()->count(),
                'active_genres' => Genre::query()->where('is_active', true)->count(),
                'banners' => Banner::query()->count(),
                'active_banners' => Banner::query()->where('is_active', true)->count(),
            ],
            'statusCounts' => $statusCounts,
            'pendingTracks' => Song::query()
                ->with(['artist', 'album'])
                ->where('moderation_status', 'pending')
                ->latest()
                ->take(6)
                ->get(),
            'recentUsers' => User::query()->latest()->take(5)->get(),
            'recentArtists' => Artist::query()->with('user')->latest()->take(4)->get(),
            ...$this->interactionState($request),
        ]);
    }
}
