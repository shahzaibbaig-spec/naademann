<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Concerns\HandlesPublicUploads;
use App\Http\Controllers\Web\WebController;
use App\Models\Genre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GenreController extends WebController
{
    use HandlesPublicUploads;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Genre::class);

        return view('admin.genres', [
            'genres' => Genre::query()->orderBy('sort_order')->get(),
            'genreMetrics' => [
                'total' => Genre::query()->count(),
                'active' => Genre::query()->where('is_active', true)->count(),
            ],
            ...$this->interactionState($request),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Genre::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image_url' => ['nullable', 'url'],
            'image_file' => ['nullable', 'image', 'max:4096'],
            'color' => ['nullable', 'regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        Genre::create([
            'name' => $data['name'],
            'slug' => $this->uniqueSlug($data['name']),
            'description' => $data['description'] ?? null,
            'image_url' => $this->storePublicUpload($request->file('image_file'), 'genres', $data['image_url'] ?? null),
            'color' => $data['color'] ?? '#3bf2ff',
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        return back()->with('status', 'Genre created.');
    }

    public function update(Request $request, Genre $genre): RedirectResponse
    {
        $this->authorize('update', $genre);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image_url' => ['nullable', 'url'],
            'image_file' => ['nullable', 'image', 'max:4096'],
            'color' => ['nullable', 'regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $genre->update([
            'name' => $data['name'],
            'slug' => $this->uniqueSlug($data['name'], $genre),
            'description' => $data['description'] ?? null,
            'image_url' => $this->storePublicUpload($request->file('image_file'), 'genres', $data['image_url'] ?? $genre->image_url),
            'color' => $data['color'] ?? $genre->color ?? '#3bf2ff',
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('status', 'Genre updated.');
    }

    public function destroy(Genre $genre): RedirectResponse
    {
        $this->authorize('delete', $genre);

        $genre->delete();

        return back()->with('status', 'Genre deleted.');
    }

    private function uniqueSlug(string $name, ?Genre $genre = null): string
    {
        $base = Str::slug($name) ?: 'genre';
        $slug = $base;
        $index = 1;

        while (
            Genre::query()
                ->where('slug', $slug)
                ->when($genre, fn ($query) => $query->whereKeyNot($genre->id))
                ->exists()
        ) {
            $slug = $base.'-'.$index;
            $index++;
        }

        return $slug;
    }
}
