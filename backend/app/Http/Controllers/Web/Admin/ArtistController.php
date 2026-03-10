<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Concerns\HandlesPublicUploads;
use App\Http\Controllers\Web\WebController;
use App\Models\Artist;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ArtistController extends WebController
{
    use HandlesPublicUploads;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Artist::class);

        return view('admin.artists', [
            'artists' => Artist::query()
                ->with('user')
                ->withCount('songs')
                ->latest()
                ->paginate(12)
                ->withQueryString(),
            'creatorUsers' => User::query()
                ->whereIn('role', ['creator', 'admin', 'super_admin'])
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'role']),
            'artistMetrics' => [
                'total' => Artist::query()->count(),
                'creator_owned' => Artist::query()->whereNotNull('user_id')->count(),
                'guest_profiles' => Artist::query()->whereNull('user_id')->count(),
            ],
            ...$this->interactionState($request),
        ]);
    }

    public function update(Request $request, Artist $artist): RedirectResponse
    {
        $this->authorize('update', $artist);

        $data = $this->validateArtistPayload($request, $artist);

        $artist->update([
            'user_id' => $data['user_id'] ?? null,
            'name' => $data['name'],
            'slug' => $this->uniqueSlug($data['name'], $artist),
            'genre' => $data['genre'],
            'bio' => $data['bio'] ?? null,
            'monthly_listeners' => $data['monthly_listeners'],
            'followers' => $data['followers'],
            'image_url' => $this->storePublicUpload($request->file('image_file'), 'artists', $data['image_url'] ?? $artist->image_url),
        ]);

        return back()->with('status', 'Artist profile updated.');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Artist::class);

        $data = $this->validateArtistPayload($request);

        Artist::query()->create([
            'user_id' => $data['user_id'] ?? null,
            'name' => $data['name'],
            'slug' => $this->uniqueSlug($data['name']),
            'genre' => $data['genre'],
            'bio' => $data['bio'] ?? null,
            'monthly_listeners' => $data['monthly_listeners'],
            'followers' => $data['followers'],
            'image_url' => $this->storePublicUpload($request->file('image_file'), 'artists', $data['image_url'] ?? null),
        ]);

        return back()->with('status', 'Artist profile created.');
    }

    public function destroy(Artist $artist): RedirectResponse
    {
        $this->authorize('delete', $artist);

        $artist->delete();

        return back()->with('status', 'Artist profile removed.');
    }

    private function validateArtistPayload(Request $request, ?Artist $artist = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'genre' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'monthly_listeners' => ['required', 'integer', 'min:0'],
            'followers' => ['required', 'integer', 'min:0'],
            'user_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query->whereIn('role', ['creator', 'admin', 'super_admin'])),
            ],
            'image_url' => [
                'nullable',
                'string',
                'max:2048',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (!is_string($value) || $value === '') {
                        return;
                    }

                    if (filter_var($value, FILTER_VALIDATE_URL)) {
                        return;
                    }

                    if (Str::startsWith($value, ['/storage/', 'storage/'])) {
                        return;
                    }

                    $fail('The profile image must be a full URL or a /storage/... path.');
                },
            ],
            'image_file' => ['nullable', 'image', 'max:4096'],
        ]);
    }

    private function uniqueSlug(string $name, ?Artist $artist = null): string
    {
        $base = Str::slug($name) ?: 'artist';
        $slug = $base;
        $index = 1;

        $query = Artist::query()->where('slug', $slug);

        if ($artist) {
            $query->whereKeyNot($artist->id);
        }

        while ($query->exists()) {
            $slug = $base.'-'.$index;
            $index++;
            $query = Artist::query()->where('slug', $slug);

            if ($artist) {
                $query->whereKeyNot($artist->id);
            }
        }

        return $slug;
    }
}
