<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Web\WebController;
use App\Models\PlatformSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PlatformSettingController extends WebController
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', PlatformSetting::class);

        return view('admin.settings', [
            'settings' => PlatformSetting::syncDefaults(),
            ...$this->interactionState($request),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorize('viewAny', PlatformSetting::class);

        $data = $request->validate([
            'settings' => ['required', 'array'],
            'settings.logo_text' => ['required', 'string', 'max:255'],
            'settings.platform_tagline' => ['required', 'string', 'max:255'],
            'settings.homepage_video' => ['nullable', 'string', 'max:255'],
            'settings.artist_default_video' => ['nullable', 'string', 'max:255'],
            'settings.footer_text' => ['nullable', 'string', 'max:2000'],
            'settings.support_email' => ['nullable', 'email', 'max:255'],
            'settings.social_instagram' => ['nullable', 'url', 'max:255'],
            'settings.social_youtube' => ['nullable', 'url', 'max:255'],
            'settings.social_soundcloud' => ['nullable', 'url', 'max:255'],
        ]);

        $settings = PlatformSetting::syncDefaults();

        foreach ($data['settings'] as $key => $value) {
            if (!$settings->has($key)) {
                continue;
            }

            $settings[$key]->update([
                'value' => $value,
            ]);
        }

        return back()->with('status', 'Platform settings saved.');
    }
}
