<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

class NaadGenreSeeder extends Seeder
{
    public function run(): void
    {
        $genres = [
            ['name' => 'Rock', 'slug' => 'rock', 'description' => 'Guitars, stage energy, and arena-scale emotion.', 'color' => '#f97316'],
            ['name' => 'Pop', 'slug' => 'pop', 'description' => 'Immediate hooks, polished production, and vocal shine.', 'color' => '#fb7185'],
            ['name' => 'Hip Hop', 'slug' => 'hip-hop', 'description' => 'Rhythmic storytelling, heavy low-end, and sharp detail.', 'color' => '#22d3ee'],
            ['name' => 'Classical', 'slug' => 'classical', 'description' => 'Orchestral depth, chamber nuance, and timeless structure.', 'color' => '#c084fc'],
            ['name' => 'Jazz', 'slug' => 'jazz', 'description' => 'Improvised warmth, smoky harmony, and late-night swing.', 'color' => '#f59e0b'],
            ['name' => 'Electronic', 'slug' => 'electronic', 'description' => 'Synthetic motion, club pressure, and neon detail.', 'color' => '#38bdf8'],
            ['name' => 'Neo-Soul', 'slug' => 'neo-soul', 'description' => 'Intimate vocals, velvet chords, and glowing rhythm beds.', 'color' => '#ec4899'],
            ['name' => 'Ambient', 'slug' => 'ambient', 'description' => 'Slow-building atmosphere and cinematic texture fields.', 'color' => '#2dd4bf'],
            ['name' => 'Indie', 'slug' => 'indie', 'description' => 'Personal writing, live-band character, and underground polish.', 'color' => '#a3e635'],
            ['name' => 'R&B', 'slug' => 'r-b', 'description' => 'Smooth grooves, expressive vocals, and modern soul movement.', 'color' => '#60a5fa'],
        ];

        foreach ($genres as $index => $genre) {
            Genre::updateOrCreate(
                ['slug' => $genre['slug']],
                [
                    ...$genre,
                    'image_url' => $this->placeholderImage($genre['name'], '1200x1600', '020617', ltrim($genre['color'], '#')),
                    'status' => 'published',
                    'is_published' => true,
                    'is_featured' => $index < 5,
                    'published_at' => now()->subDays(60 - ($index * 3)),
                    'is_active' => true,
                    'sort_order' => $index + 1,
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
}
