<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Concerns\HandlesPublicUploads;
use App\Http\Controllers\Web\WebController;
use App\Models\Artist;
use App\Models\Genre;
use App\Models\Song;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UploadController extends WebController
{
    use HandlesPublicUploads;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Song::class);

        return view('admin.uploads', [
            'artists' => Artist::query()
                ->with(['albums' => fn ($query) => $query->orderBy('title')])
                ->orderBy('name')
                ->get(),
            'genres' => Genre::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
            'recentUploads' => Song::query()
                ->with(['artist', 'album'])
                ->latest()
                ->take(8)
                ->get(),
            ...$this->interactionState($request),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Song::class);

        $data = $request->validate([
            'artist_id' => ['required', 'integer', 'exists:artists,id'],
            'title' => ['required', 'string', 'max:255'],
            'genre' => ['required', 'string', 'max:255'],
            'album_id' => ['nullable', 'integer'],
            'description' => ['nullable', 'string', 'max:4000'],
            'lyrics' => ['nullable', 'string'],
            'release_date' => ['nullable', 'date'],
            'publish_now' => ['nullable', 'boolean'],
            'audio_file' => ['required', 'file', 'mimes:mp3', 'max:20480'],
            'cover_image' => ['nullable', 'image', 'max:4096'],
        ]);

        $artist = Artist::query()->findOrFail($data['artist_id']);
        $album = !empty($data['album_id']) ? $artist->albums()->findOrFail($data['album_id']) : null;
        $publishNow = $request->boolean('publish_now');
        $audioUrl = $this->storePublicUpload($request->file('audio_file'), 'songs');

        if (!$audioUrl) {
            return back()
                ->withErrors(['audio_file' => 'The track audio could not be stored. Please try again.'])
                ->withInput();
        }

        Song::create([
            'artist_id' => $artist->id,
            'album_id' => $album?->id,
            'title' => $data['title'],
            'genre' => $data['genre'],
            'description' => $data['description'] ?? null,
            'lyrics' => $data['lyrics'] ?? null,
            'duration' => 0,
            'audio_url' => $audioUrl,
            'cover_image_url' => $this->storePublicUpload($request->file('cover_image'), 'covers', $album?->cover_image_url ?: $artist->image_url),
            'release_date' => $data['release_date'] ?? now()->toDateString(),
            'is_featured' => false,
            'moderation_status' => $publishNow ? 'approved' : 'draft',
            'approved_at' => $publishNow ? now() : null,
            'published_at' => $publishNow ? now() : null,
            'streams_count' => 0,
        ]);

        return redirect()
            ->route('admin.uploads.index')
            ->with('status', $publishNow ? 'Track uploaded and published from the admin panel.' : 'Track uploaded to drafts from the admin panel.');
    }
}
