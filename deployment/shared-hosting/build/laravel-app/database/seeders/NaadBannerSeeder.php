<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NaadBannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title' => 'Naad-e-Maan Resonance',
                'subtitle' => 'Stream premium artists, emotional soundscapes, and neon-lit late-night sessions.',
                'cta_label' => 'Start Listening',
                'cta_url' => '/',
            ],
            [
                'title' => 'Night Sessions Live',
                'subtitle' => 'Follow new videos, live cuts, and cinematic performances from rising artists.',
                'cta_label' => 'Watch Videos',
                'cta_url' => '/#videos',
            ],
            [
                'title' => 'Genre Worlds',
                'subtitle' => 'Move from rock stages and pop hooks to ambient drift and classical depth.',
                'cta_label' => 'Explore Genres',
                'cta_url' => '/#genres',
            ],
            [
                'title' => 'Creator Spotlight',
                'subtitle' => 'Featured creators shaping the next wave of releases on Naad-e-Maan.',
                'cta_label' => 'Meet Artists',
                'cta_url' => '/artists',
            ],
            [
                'title' => 'Calling All Creators',
                'subtitle' => 'Upload tracks, manage albums, and build your audience with a premium creator suite.',
                'cta_label' => 'Open Creator Studio',
                'cta_url' => '/creator/dashboard',
            ],
        ];

        foreach ($banners as $index => $banner) {
            Banner::updateOrCreate(
                ['slug' => Str::slug($banner['title'])],
                [
                    ...$banner,
                    'image_url' => $this->placeholderImage($banner['title'], '1600x900', '020617', $this->accentForIndex($index)),
                    'is_active' => true,
                    'sort_order' => $index + 1,
                    'status' => 'published',
                    'is_published' => true,
                    'is_featured' => $index < 2,
                    'published_at' => now()->subDays(14 - ($index * 2)),
                ]
            );
        }
    }

    private function placeholderImage(string $label, string $size, string $background, string $foreground): string
    {
        return sprintf(
            'https://placehold.co/%s/%s/%s?text=%s',
            $size,
            $background,
            $foreground,
            rawurlencode($label)
        );
    }

    private function accentForIndex(int $index): string
    {
        return ['22d3ee', 'ec4899', 'f59e0b', 'a3e635', '60a5fa'][$index % 5];
    }
}
