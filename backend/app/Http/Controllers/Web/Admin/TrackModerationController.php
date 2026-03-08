<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Web\WebController;
use App\Models\Song;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TrackModerationController extends WebController
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Song::class);

        $status = trim($request->string('status')->toString());
        $allowedStatuses = ['draft', 'pending', 'approved', 'rejected'];

        return view('admin.moderation', [
            'tracks' => Song::query()
                ->with(['artist', 'album'])
                ->when(in_array($status, $allowedStatuses, true), fn ($query) => $query->where('moderation_status', $status))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'activeStatus' => in_array($status, $allowedStatuses, true) ? $status : '',
            'statusCounts' => [
                'all' => Song::query()->count(),
                'draft' => Song::query()->where('moderation_status', 'draft')->count(),
                'pending' => Song::query()->where('moderation_status', 'pending')->count(),
                'approved' => Song::query()->where('moderation_status', 'approved')->count(),
                'rejected' => Song::query()->where('moderation_status', 'rejected')->count(),
            ],
            ...$this->interactionState($request),
        ]);
    }

    public function update(Request $request, Song $song): RedirectResponse
    {
        $this->authorize('update', $song);

        $data = $request->validate([
            'moderation_status' => ['required', 'in:draft,pending,approved,rejected'],
        ]);

        $song->update([
            'moderation_status' => $data['moderation_status'],
            'approved_at' => $data['moderation_status'] === 'approved' ? now() : null,
            'published_at' => $data['moderation_status'] === 'approved' ? ($song->published_at ?? now()) : null,
        ]);

        return back()->with('status', 'Track moderation status updated.');
    }
}
