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
            'is_featured' => ['nullable', 'boolean'],
        ]);

        $updates = [
            'moderation_status' => $data['moderation_status'],
            'approved_at' => $data['moderation_status'] === 'approved' ? now() : null,
            'published_at' => $data['moderation_status'] === 'approved' ? ($song->published_at ?? now()) : null,
        ];

        if ($request->has('is_featured')) {
            $updates['is_featured'] = $request->boolean('is_featured');
        }

        $song->update($updates);

        return back()->with('status', 'Track moderation status updated.');
    }

    public function bulk(Request $request): RedirectResponse
    {
        $this->authorize('viewAny', Song::class);

        $data = $request->validate([
            'track_ids' => ['required', 'array', 'min:1'],
            'track_ids.*' => ['integer', 'distinct', 'exists:songs,id'],
            'bulk_action' => ['required', 'in:publish,draft,delete'],
        ], [
            'track_ids.required' => 'Select at least one track before applying a bulk action.',
            'track_ids.min' => 'Select at least one track before applying a bulk action.',
            'bulk_action.required' => 'Choose a bulk action to continue.',
        ]);

        $songs = Song::query()
            ->whereIn('id', $data['track_ids'])
            ->get();

        if ($songs->isEmpty()) {
            return back()->withErrors([
                'track_ids' => 'No valid tracks were selected.',
            ]);
        }

        if ($data['bulk_action'] === 'delete') {
            foreach ($songs as $song) {
                $this->authorize('delete', $song);
            }

            $deletedCount = Song::query()
                ->whereIn('id', $songs->pluck('id'))
                ->delete();

            return back()->with('status', $deletedCount.' tracks deleted successfully.');
        }

        foreach ($songs as $song) {
            $this->authorize('update', $song);
        }

        $now = now();

        if ($data['bulk_action'] === 'publish') {
            foreach ($songs as $song) {
                $song->update([
                    'moderation_status' => 'approved',
                    'approved_at' => $now,
                    'published_at' => $song->published_at ?: $now,
                ]);
            }

            return back()->with('status', $songs->count().' tracks published successfully.');
        }

        foreach ($songs as $song) {
            $song->update([
                'moderation_status' => 'draft',
                'approved_at' => null,
                'published_at' => null,
            ]);
        }

        return back()->with('status', $songs->count().' tracks moved to draft successfully.');
    }
}
