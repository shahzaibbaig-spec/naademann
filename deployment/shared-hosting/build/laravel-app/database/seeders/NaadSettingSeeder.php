<?php

namespace Database\Seeders;

use App\Models\PlatformSetting;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class NaadSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'logo_text', 'label' => 'Logo Text', 'value' => 'Naad-e-Maan', 'type' => 'text', 'group' => 'branding', 'is_public' => true],
            ['key' => 'platform_tagline', 'label' => 'Platform Tagline', 'value' => 'The Sound of the Soul', 'type' => 'text', 'group' => 'branding', 'is_public' => true],
            ['key' => 'homepage_video', 'label' => 'Homepage Video URL or ID', 'value' => 'ScMzIvxBSi4', 'type' => 'text', 'group' => 'media', 'is_public' => false],
            ['key' => 'artist_default_video', 'label' => 'Artist Default Video URL or ID', 'value' => 'ScMzIvxBSi4', 'type' => 'text', 'group' => 'media', 'is_public' => false],
            ['key' => 'footer_text', 'label' => 'Footer Text', 'value' => 'Naad-e-Maan is a premium streaming space for resonance, release culture, and creator-led sound journeys.', 'type' => 'textarea', 'group' => 'branding', 'is_public' => true],
            ['key' => 'support_email', 'label' => 'Support Email', 'value' => 'support@naademaan.test', 'type' => 'email', 'group' => 'support', 'is_public' => true],
            ['key' => 'social_instagram', 'label' => 'Instagram URL', 'value' => 'https://instagram.com/naademaan', 'type' => 'url', 'group' => 'social', 'is_public' => true],
            ['key' => 'social_youtube', 'label' => 'YouTube URL', 'value' => 'https://youtube.com/@naademaan', 'type' => 'url', 'group' => 'social', 'is_public' => true],
            ['key' => 'social_soundcloud', 'label' => 'SoundCloud URL', 'value' => 'https://soundcloud.com/naademaan', 'type' => 'url', 'group' => 'social', 'is_public' => true],
        ];

        foreach ($settings as $setting) {
            PlatformSetting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'label' => $setting['label'],
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                ]
            );

            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'label' => $setting['label'],
                    'value' => $setting['value'],
                    'group' => $setting['group'],
                    'type' => $setting['type'],
                    'is_public' => $setting['is_public'],
                    'status' => 'published',
                ]
            );
        }
    }
}
