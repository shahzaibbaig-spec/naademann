<?php

namespace App\Http\Controllers\Web\Creator;

use App\Http\Controllers\Concerns\HandlesPublicUploads;
use App\Http\Controllers\Web\WebController;
use App\Models\Album;
use App\Models\Favorite;
use App\Models\Genre;
use App\Models\Song;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardController extends WebController
{
    use HandlesPublicUploads;

    public function index(Request $request)
    {
        $artist = $request->user()->artists()->with(['albums', 'songs.album'])->firstOrFail();
        $songIds = $artist->songs->pluck('id');
        $applicationAudioLimitKilobytes = 20480;
        $effectiveAudioLimitKilobytes = $this->effectiveUploadLimitKilobytes($applicationAudioLimitKilobytes);

        $analytics = [
            'monthly_listeners' => $artist->monthly_listeners,
            'followers' => $artist->followers,
            'tracks' => $artist->songs->count(),
            'albums' => $artist->albums->count(),
            'streams' => $artist->songs->sum('streams_count'),
            'favorites' => Favorite::query()->whereIn('song_id', $songIds)->count(),
        ];

        return view('creator.dashboard', [
            'artist' => $artist,
            'analytics' => $analytics,
            'tracks' => $artist->songs->sortByDesc('created_at'),
            'albums' => $artist->albums->sortByDesc('release_date'),
            'genres' => Genre::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'effectiveAudioUploadLimitLabel' => $this->formatKilobytes($effectiveAudioLimitKilobytes),
            'targetAudioUploadLimitLabel' => $this->formatKilobytes($applicationAudioLimitKilobytes),
            'audioUploadLimitConstrained' => $effectiveAudioLimitKilobytes < $applicationAudioLimitKilobytes,
            ...$this->interactionState($request),
        ]);
    }

    public function storeTrack(Request $request): RedirectResponse
    {
        $artist = $request->user()->artists()->firstOrFail();
        $applicationAudioLimitKilobytes = 20480;
        $effectiveAudioLimitKilobytes = $this->effectiveUploadLimitKilobytes($applicationAudioLimitKilobytes);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'genre' => ['required', 'string', 'max:255'],
            'album_id' => ['nullable', 'integer'],
            'description' => ['nullable', 'string', 'max:4000'],
            'lyrics' => ['nullable', 'string'],
            'release_date' => ['nullable', 'date'],
            'publish_now' => ['nullable', 'boolean'],
            'audio_file' => ['required', 'file', 'mimes:mp3', "max:{$effectiveAudioLimitKilobytes}"],
            'cover_image' => ['nullable', 'image', 'max:4096'],
        ], [
            'audio_file.uploaded' => $this->uploadFailedValidationMessage('audio file', $effectiveAudioLimitKilobytes),
            'audio_file.max' => 'The audio file may not be greater than '.$this->formatKilobytes($effectiveAudioLimitKilobytes).'.',
        ]);

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

        return back()->with('status', $publishNow ? 'Track published and added to your catalog.' : 'Track saved as draft.');
    }

    public function destroyTrack(Request $request, Song $song): RedirectResponse
    {
        $artistIds = $request->user()->artists()->pluck('id');
        abort_unless($artistIds->contains($song->artist_id), 403);
        $song->delete();

        return back()->with('status', 'Track removed.');
    }

    public function storeAlbum(Request $request): RedirectResponse
    {
        $artist = $request->user()->artists()->firstOrFail();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'genre' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'release_date' => ['nullable', 'date'],
            'cover_image' => ['nullable', 'image', 'max:4096'],
        ]);

        Album::create([
            'artist_id' => $artist->id,
            'title' => $data['title'],
            'slug' => $this->uniqueSlugForModel($data['title'], Album::class),
            'genre' => $data['genre'],
            'description' => $data['description'] ?? null,
            'cover_image_url' => $this->storePublicUpload($request->file('cover_image'), 'albums', $artist->image_url),
            'release_date' => $data['release_date'] ?? now()->toDateString(),
        ]);

        return back()->with('status', 'Album created.');
    }

    public function destroyAlbum(Request $request, Album $album): RedirectResponse
    {
        $artistIds = $request->user()->artists()->pluck('id');
        abort_unless($artistIds->contains($album->artist_id), 403);
        $album->delete();

        return back()->with('status', 'Album removed.');
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $artist = $request->user()->artists()->firstOrFail();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$request->user()->id],
            'headline' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'genre' => ['required', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'max:4096'],
        ]);

        $avatarPath = $this->storePublicUpload($request->file('avatar'), 'avatars', $request->user()->avatar_url);

        $request->user()->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'headline' => $data['headline'] ?? null,
            'bio' => $data['bio'] ?? null,
            'avatar_url' => $avatarPath,
        ]);

        $artist->update([
            'name' => $data['name'],
            'genre' => $data['genre'],
            'bio' => $data['bio'] ?? $artist->bio,
            'image_url' => $avatarPath,
        ]);

        return back()->with('status', 'Profile updated.');
    }

    private function uniqueSlugForModel(string $value, string $modelClass): string
    {
        $base = Str::slug($value) ?: 'release';
        $slug = $base;
        $index = 1;

        while ($modelClass::query()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$index}";
            $index++;
        }

        return $slug;
    }
}
