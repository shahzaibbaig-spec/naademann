<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class PlatformSetting extends Model
{
    protected $fillable = [
        'key',
        'label',
        'value',
        'type',
    ];

    public static function definitions(): array
    {
        return [
            'logo_text' => ['label' => 'Logo Text', 'type' => 'text', 'value' => 'Naad-e-Maan'],
            'platform_tagline' => ['label' => 'Platform Tagline', 'type' => 'text', 'value' => 'The Sound of the Soul'],
            'homepage_video' => ['label' => 'Homepage Video URL or ID', 'type' => 'text', 'value' => 'ScMzIvxBSi4'],
            'artist_default_video' => ['label' => 'Artist Default Video URL or ID', 'type' => 'text', 'value' => 'ScMzIvxBSi4'],
            'footer_text' => ['label' => 'Footer Text', 'type' => 'textarea', 'value' => 'Immersive music discovery, creator publishing, and admin-ready platform control.'],
            'support_email' => ['label' => 'Support Email', 'type' => 'email', 'value' => 'support@naademaan.test'],
            'social_instagram' => ['label' => 'Instagram URL', 'type' => 'url', 'value' => 'https://instagram.com/naademaan'],
            'social_youtube' => ['label' => 'YouTube URL', 'type' => 'url', 'value' => 'https://youtube.com/@naademaan'],
            'social_soundcloud' => ['label' => 'SoundCloud URL', 'type' => 'url', 'value' => 'https://soundcloud.com/naademaan'],
        ];
    }

    public static function youtubeVideoId(?string $value, string $fallback = 'ScMzIvxBSi4'): string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return $fallback;
        }

        if (preg_match('/^[A-Za-z0-9_-]{11}$/', $value) === 1) {
            return $value;
        }

        $query = parse_url($value, PHP_URL_QUERY);

        if (is_string($query)) {
            parse_str($query, $parameters);

            if (!empty($parameters['v']) && preg_match('/^[A-Za-z0-9_-]{11}$/', (string) $parameters['v']) === 1) {
                return (string) $parameters['v'];
            }
        }

        $path = trim((string) parse_url($value, PHP_URL_PATH), '/');

        foreach ($path === '' ? [] : explode('/', $path) as $segment) {
            if (preg_match('/^[A-Za-z0-9_-]{11}$/', $segment) === 1) {
                return $segment;
            }
        }

        return $fallback;
    }

    public static function syncDefaults(): Collection
    {
        $now = Carbon::now();

        static::query()->upsert(
            collect(static::definitions())->map(fn (array $definition, string $key) => [
                'key' => $key,
                'label' => $definition['label'],
                'value' => $definition['value'],
                'type' => $definition['type'],
                'created_at' => $now,
                'updated_at' => $now,
            ])->values()->all(),
            ['key'],
            ['label', 'type', 'updated_at']
        );

        return static::query()
            ->whereIn('key', array_keys(static::definitions()))
            ->orderBy('label')
            ->get()
            ->keyBy('key');
    }
}
