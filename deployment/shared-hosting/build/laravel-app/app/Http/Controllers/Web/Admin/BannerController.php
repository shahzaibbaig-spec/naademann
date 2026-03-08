<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Concerns\HandlesPublicUploads;
use App\Http\Controllers\Web\WebController;
use App\Models\Banner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BannerController extends WebController
{
    use HandlesPublicUploads;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Banner::class);

        return view('admin.banners', [
            'banners' => Banner::query()->orderBy('sort_order')->get(),
            'bannerMetrics' => [
                'total' => Banner::query()->count(),
                'active' => Banner::query()->where('is_active', true)->count(),
            ],
            ...$this->interactionState($request),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Banner::class);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'cta_label' => ['nullable', 'string', 'max:255'],
            'cta_url' => ['nullable', 'string', 'max:255'],
            'image_url' => ['nullable', 'url'],
            'image_file' => ['nullable', 'image', 'max:4096'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        Banner::create([
            'title' => $data['title'],
            'subtitle' => $data['subtitle'] ?? null,
            'cta_label' => $data['cta_label'] ?? null,
            'cta_url' => $data['cta_url'] ?? null,
            'image_url' => $this->storePublicUpload($request->file('image_file'), 'banners', $data['image_url'] ?? null),
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        return back()->with('status', 'Banner created.');
    }

    public function update(Request $request, Banner $banner): RedirectResponse
    {
        $this->authorize('update', $banner);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'cta_label' => ['nullable', 'string', 'max:255'],
            'cta_url' => ['nullable', 'string', 'max:255'],
            'image_url' => ['nullable', 'url'],
            'image_file' => ['nullable', 'image', 'max:4096'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $banner->update([
            'title' => $data['title'],
            'subtitle' => $data['subtitle'] ?? null,
            'cta_label' => $data['cta_label'] ?? null,
            'cta_url' => $data['cta_url'] ?? null,
            'image_url' => $this->storePublicUpload($request->file('image_file'), 'banners', $data['image_url'] ?? $banner->image_url),
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('status', 'Banner updated.');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        $this->authorize('delete', $banner);

        $banner->delete();

        return back()->with('status', 'Banner deleted.');
    }
}
