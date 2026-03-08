<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Artist;
use App\Models\ArtistProfile;
use App\Models\Genre;
use App\Models\Song;
use App\Models\Track;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class NaadCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $genreIds = Genre::query()
            ->get(['id', 'name', 'color'])
            ->mapWithKeys(fn (Genre $genre): array => [mb_strtolower($genre->name) => ['id' => $genre->id, 'color' => ltrim($genre->color, '#')]])
            ->all();

        $artists = $this->artistBlueprints();
        $artistMap = [];
        $artistProfileMap = [];

        foreach ($artists as $artist) {
            $user = User::query()->where('email', $artist['email'])->firstOrFail();
            $genreMeta = $genreIds[mb_strtolower($artist['genre'])] ?? ['id' => null, 'color' => '22d3ee'];

            $legacyArtist = Artist::updateOrCreate(
                ['slug' => $artist['slug']],
                [
                    'user_id' => $user->id,
                    'name' => $artist['name'],
                    'genre' => $artist['genre'],
                    'bio' => $artist['bio'],
                    'image_url' => $this->placeholderImage($artist['name'], '900x900', '020617', $genreMeta['color']),
                    'monthly_listeners' => $artist['monthly_listeners'],
                    'followers' => $artist['followers_count'],
                ]
            );

            $artistProfile = ArtistProfile::updateOrCreate(
                ['slug' => $artist['slug']],
                [
                    'user_id' => $user->id,
                    'genre_id' => $genreMeta['id'],
                    'name' => $artist['name'],
                    'bio' => $artist['bio'],
                    'avatar_url' => $this->placeholderImage($artist['name'], '900x900', '020617', $genreMeta['color']),
                    'cover_image_url' => $this->placeholderImage($artist['name'].' Live', '1600x900', '020617', $genreMeta['color']),
                    'status' => 'published',
                    'is_published' => true,
                    'is_featured' => $artist['is_featured'],
                    'published_at' => now()->subDays($artist['published_days_ago']),
                    'monthly_listeners' => $artist['monthly_listeners'],
                    'followers_count' => $artist['followers_count'],
                ]
            );

            $artistMap[$artist['slug']] = $legacyArtist;
            $artistProfileMap[$artist['slug']] = $artistProfile;
        }

        $albums = [];

        foreach ($this->albumBlueprints() as $index => $album) {
            $genreMeta = $genreIds[mb_strtolower($album['genre'])] ?? ['id' => null, 'color' => '22d3ee'];
            $releaseDate = Carbon::parse($album['release_date']);

            $albums[$album['slug']] = Album::updateOrCreate(
                ['slug' => $album['slug']],
                [
                    'artist_id' => $artistMap[$album['artist_slug']]->id,
                    'artist_profile_id' => $artistProfileMap[$album['artist_slug']]->id,
                    'title' => $album['title'],
                    'genre' => $album['genre'],
                    'genre_id' => $genreMeta['id'],
                    'description' => $album['description'],
                    'cover_image_url' => $this->placeholderImage($album['title'], '1200x1200', '0f172a', $genreMeta['color']),
                    'release_date' => $releaseDate->toDateString(),
                    'status' => 'published',
                    'is_published' => true,
                    'is_featured' => $index < 4,
                    'published_at' => $releaseDate->copy()->setTime(20, 0),
                ]
            );
        }

        foreach ($this->trackBlueprints() as $index => $track) {
            $album = $albums[$track['album_slug']];
            $artistSlug = $track['artist_slug'];
            $genreMeta = $genreIds[mb_strtolower($track['genre'])] ?? ['id' => null, 'color' => '22d3ee'];
            $publishedAt = Carbon::parse($track['release_date'])->setTime(21, 0);
            $description = sprintf(
                '%s from %s by %s, carrying the %s energy of Naad-e-Maan.',
                $track['title'],
                $album->title,
                $artistMap[$artistSlug]->name,
                $track['genre']
            );

            $lyrics = implode(PHP_EOL, [
                $track['title'].' in the afterglow.',
                'The waveform bends and the room keeps breathing.',
                'Naad-e-Maan holds the pulse until the lights fade.',
            ]);

            Song::updateOrCreate(
                ['slug' => $track['slug']],
                [
                    'artist_id' => $artistMap[$artistSlug]->id,
                    'album_id' => $album->id,
                    'title' => $track['title'],
                    'genre' => $track['genre'],
                    'description' => $description,
                    'lyrics' => $lyrics,
                    'duration' => $track['duration'],
                    'audio_url' => $this->audioSampleUrl($index + 1),
                    'cover_image_url' => $album->cover_image_url,
                    'release_date' => $track['release_date'],
                    'is_featured' => $track['is_featured'],
                    'moderation_status' => 'approved',
                    'approved_at' => $publishedAt,
                    'published_at' => $publishedAt,
                    'streams_count' => 450000 + (($index + 1) * 187500),
                ]
            );

            Track::updateOrCreate(
                ['slug' => $track['slug']],
                [
                    'artist_profile_id' => $artistProfileMap[$artistSlug]->id,
                    'genre_id' => $genreMeta['id'],
                    'album_id' => $album->id,
                    'title' => $track['title'],
                    'description' => $description,
                    'lyrics' => $lyrics,
                    'duration' => $track['duration'],
                    'audio_url' => $this->audioSampleUrl($index + 1),
                    'cover_image_url' => $album->cover_image_url,
                    'release_date' => $track['release_date'],
                    'status' => 'published',
                    'is_published' => true,
                    'is_featured' => $track['is_featured'],
                    'published_at' => $publishedAt,
                    'views_count' => 320000 + (($index + 1) * 145000),
                ]
            );
        }
    }

    private function artistBlueprints(): array
    {
        return [
            [
                'email' => 'mira@naademaan.test',
                'name' => 'Mira Skye',
                'slug' => 'mira-skye',
                'genre' => 'Neo-Soul',
                'bio' => 'A premium neo-soul voice balancing intimacy, warm harmony, and cinematic low-end.',
                'monthly_listeners' => 8600000,
                'followers_count' => 980000,
                'is_featured' => true,
                'published_days_ago' => 420,
            ],
            [
                'email' => 'rehan@naademaan.test',
                'name' => 'Rehan Pulse',
                'slug' => 'rehan-pulse',
                'genre' => 'Electronic',
                'bio' => 'An electronic producer focused on bass architecture, glassy synth motion, and club momentum.',
                'monthly_listeners' => 6400000,
                'followers_count' => 720000,
                'is_featured' => true,
                'published_days_ago' => 390,
            ],
            [
                'email' => 'kian@naademaan.test',
                'name' => 'Kian Drift',
                'slug' => 'kian-drift',
                'genre' => 'Hip Hop',
                'bio' => 'A detail-heavy rapper with nocturnal hooks, cinematic ad-libs, and restless city writing.',
                'monthly_listeners' => 7100000,
                'followers_count' => 805000,
                'is_featured' => true,
                'published_days_ago' => 365,
            ],
            [
                'email' => 'aanya@naademaan.test',
                'name' => 'Aanya Raag',
                'slug' => 'aanya-raag',
                'genre' => 'Classical',
                'bio' => 'A classically trained composer bending raga phrasing and chamber space into modern releases.',
                'monthly_listeners' => 2900000,
                'followers_count' => 310000,
                'is_featured' => false,
                'published_days_ago' => 340,
            ],
            [
                'email' => 'leena@naademaan.test',
                'name' => 'Leena Sol',
                'slug' => 'leena-sol',
                'genre' => 'Pop',
                'bio' => 'A bright pop songwriter with polished toplines, festival-ready choruses, and live-band energy.',
                'monthly_listeners' => 5300000,
                'followers_count' => 560000,
                'is_featured' => true,
                'published_days_ago' => 330,
            ],
        ];
    }

    private function albumBlueprints(): array
    {
        return [
            ['artist_slug' => 'mira-skye', 'title' => 'Aurora Veil', 'slug' => 'aurora-veil', 'genre' => 'Neo-Soul', 'description' => 'Warm vocals, velvet keys, and night-drive bass pressure.', 'release_date' => '2026-02-21'],
            ['artist_slug' => 'mira-skye', 'title' => 'Afterglow Letters', 'slug' => 'afterglow-letters', 'genre' => 'R&B', 'description' => 'Late-night letters folded into polished rhythm and neon romance.', 'release_date' => '2025-11-08'],
            ['artist_slug' => 'rehan-pulse', 'title' => 'Velvet Pulse', 'slug' => 'velvet-pulse', 'genre' => 'Electronic', 'description' => 'Club-ready percussion and luminous synth pressure.', 'release_date' => '2026-01-14'],
            ['artist_slug' => 'rehan-pulse', 'title' => 'Silent Horizons', 'slug' => 'silent-horizons', 'genre' => 'Ambient', 'description' => 'Wide, drifting atmospheres shaped for headphones after midnight.', 'release_date' => '2025-10-02'],
            ['artist_slug' => 'kian-drift', 'title' => 'Rhythm Theory', 'slug' => 'rhythm-theory', 'genre' => 'Hip Hop', 'description' => 'Punchy drums, modern rap cadence, and city-night focus.', 'release_date' => '2025-11-30'],
            ['artist_slug' => 'kian-drift', 'title' => 'Sidewalk Cinema', 'slug' => 'sidewalk-cinema', 'genre' => 'Indie', 'description' => 'Street poetry, alt-leaning hooks, and neon-film storytelling.', 'release_date' => '2025-08-18'],
            ['artist_slug' => 'aanya-raag', 'title' => 'Midnight Raga', 'slug' => 'midnight-raga', 'genre' => 'Classical', 'description' => 'Contemporary classical themes with raga-inspired melodic movement.', 'release_date' => '2025-12-05'],
            ['artist_slug' => 'aanya-raag', 'title' => 'Velvet Chamber', 'slug' => 'velvet-chamber', 'genre' => 'Jazz', 'description' => 'Strings, upright warmth, and a chamber-room glow.', 'release_date' => '2025-07-11'],
            ['artist_slug' => 'leena-sol', 'title' => 'Electric Bloom', 'slug' => 'electric-bloom', 'genre' => 'Pop', 'description' => 'Hooks, handclaps, and a polished summer-night charge.', 'release_date' => '2026-01-26'],
            ['artist_slug' => 'leena-sol', 'title' => 'North of Noise', 'slug' => 'north-of-noise', 'genre' => 'Rock', 'description' => 'Guitar lift, anthem choruses, and rain-soaked stage lights.', 'release_date' => '2025-09-27'],
        ];
    }

    private function trackBlueprints(): array
    {
        return [
            ['artist_slug' => 'mira-skye', 'album_slug' => 'aurora-veil', 'genre' => 'Neo-Soul', 'title' => 'Echoes in Blue', 'slug' => 'echoes-in-blue', 'duration' => 280, 'release_date' => '2026-02-21', 'is_featured' => true],
            ['artist_slug' => 'mira-skye', 'album_slug' => 'aurora-veil', 'genre' => 'Neo-Soul', 'title' => 'Midnight Bloom', 'slug' => 'midnight-bloom', 'duration' => 216, 'release_date' => '2026-02-21', 'is_featured' => false],
            ['artist_slug' => 'mira-skye', 'album_slug' => 'aurora-veil', 'genre' => 'Neo-Soul', 'title' => 'Silver Thread', 'slug' => 'silver-thread', 'duration' => 248, 'release_date' => '2026-02-21', 'is_featured' => false],
            ['artist_slug' => 'mira-skye', 'album_slug' => 'afterglow-letters', 'genre' => 'R&B', 'title' => 'Satin Static', 'slug' => 'satin-static', 'duration' => 234, 'release_date' => '2025-11-08', 'is_featured' => true],
            ['artist_slug' => 'mira-skye', 'album_slug' => 'afterglow-letters', 'genre' => 'R&B', 'title' => 'Love on Delay', 'slug' => 'love-on-delay', 'duration' => 227, 'release_date' => '2025-11-08', 'is_featured' => false],
            ['artist_slug' => 'mira-skye', 'album_slug' => 'afterglow-letters', 'genre' => 'R&B', 'title' => 'Gold Frequency', 'slug' => 'gold-frequency', 'duration' => 241, 'release_date' => '2025-11-08', 'is_featured' => false],
            ['artist_slug' => 'rehan-pulse', 'album_slug' => 'velvet-pulse', 'genre' => 'Electronic', 'title' => 'Velvet Pulse', 'slug' => 'velvet-pulse-track', 'duration' => 242, 'release_date' => '2026-01-14', 'is_featured' => true],
            ['artist_slug' => 'rehan-pulse', 'album_slug' => 'velvet-pulse', 'genre' => 'Electronic', 'title' => 'Neon Dust', 'slug' => 'neon-dust', 'duration' => 250, 'release_date' => '2026-01-14', 'is_featured' => false],
            ['artist_slug' => 'rehan-pulse', 'album_slug' => 'velvet-pulse', 'genre' => 'Electronic', 'title' => 'Glass Voltage', 'slug' => 'glass-voltage', 'duration' => 238, 'release_date' => '2026-01-14', 'is_featured' => false],
            ['artist_slug' => 'rehan-pulse', 'album_slug' => 'silent-horizons', 'genre' => 'Ambient', 'title' => 'Low Tide Light', 'slug' => 'low-tide-light', 'duration' => 264, 'release_date' => '2025-10-02', 'is_featured' => true],
            ['artist_slug' => 'rehan-pulse', 'album_slug' => 'silent-horizons', 'genre' => 'Ambient', 'title' => 'Drift Signal', 'slug' => 'drift-signal', 'duration' => 272, 'release_date' => '2025-10-02', 'is_featured' => false],
            ['artist_slug' => 'rehan-pulse', 'album_slug' => 'silent-horizons', 'genre' => 'Ambient', 'title' => 'Skyline Hush', 'slug' => 'skyline-hush', 'duration' => 289, 'release_date' => '2025-10-02', 'is_featured' => false],
            ['artist_slug' => 'kian-drift', 'album_slug' => 'rhythm-theory', 'genre' => 'Hip Hop', 'title' => 'Rhythm Theory', 'slug' => 'rhythm-theory-track', 'duration' => 236, 'release_date' => '2025-11-30', 'is_featured' => true],
            ['artist_slug' => 'kian-drift', 'album_slug' => 'rhythm-theory', 'genre' => 'Hip Hop', 'title' => 'City Haze', 'slug' => 'city-haze', 'duration' => 224, 'release_date' => '2025-11-30', 'is_featured' => false],
            ['artist_slug' => 'kian-drift', 'album_slug' => 'rhythm-theory', 'genre' => 'Hip Hop', 'title' => 'Corner Cipher', 'slug' => 'corner-cipher', 'duration' => 219, 'release_date' => '2025-11-30', 'is_featured' => false],
            ['artist_slug' => 'kian-drift', 'album_slug' => 'sidewalk-cinema', 'genre' => 'Indie', 'title' => 'Backseat Monologue', 'slug' => 'backseat-monologue', 'duration' => 230, 'release_date' => '2025-08-18', 'is_featured' => true],
            ['artist_slug' => 'kian-drift', 'album_slug' => 'sidewalk-cinema', 'genre' => 'Indie', 'title' => 'Alley Chorus', 'slug' => 'alley-chorus', 'duration' => 213, 'release_date' => '2025-08-18', 'is_featured' => false],
            ['artist_slug' => 'kian-drift', 'album_slug' => 'sidewalk-cinema', 'genre' => 'Indie', 'title' => 'Night Window', 'slug' => 'night-window', 'duration' => 221, 'release_date' => '2025-08-18', 'is_featured' => false],
            ['artist_slug' => 'aanya-raag', 'album_slug' => 'midnight-raga', 'genre' => 'Classical', 'title' => 'Moonlit Alaap', 'slug' => 'moonlit-alaap', 'duration' => 301, 'release_date' => '2025-12-05', 'is_featured' => true],
            ['artist_slug' => 'aanya-raag', 'album_slug' => 'midnight-raga', 'genre' => 'Classical', 'title' => 'Saffron Echo', 'slug' => 'saffron-echo', 'duration' => 288, 'release_date' => '2025-12-05', 'is_featured' => false],
            ['artist_slug' => 'aanya-raag', 'album_slug' => 'midnight-raga', 'genre' => 'Classical', 'title' => 'Dawn Taal', 'slug' => 'dawn-taal', 'duration' => 276, 'release_date' => '2025-12-05', 'is_featured' => false],
            ['artist_slug' => 'aanya-raag', 'album_slug' => 'velvet-chamber', 'genre' => 'Jazz', 'title' => 'Blue Smoke Quartet', 'slug' => 'blue-smoke-quartet', 'duration' => 254, 'release_date' => '2025-07-11', 'is_featured' => true],
            ['artist_slug' => 'aanya-raag', 'album_slug' => 'velvet-chamber', 'genre' => 'Jazz', 'title' => 'Paper Lanterns', 'slug' => 'paper-lanterns', 'duration' => 247, 'release_date' => '2025-07-11', 'is_featured' => false],
            ['artist_slug' => 'aanya-raag', 'album_slug' => 'velvet-chamber', 'genre' => 'Jazz', 'title' => 'River in 7/8', 'slug' => 'river-in-7-8', 'duration' => 262, 'release_date' => '2025-07-11', 'is_featured' => false],
            ['artist_slug' => 'leena-sol', 'album_slug' => 'electric-bloom', 'genre' => 'Pop', 'title' => 'Cherry Neon', 'slug' => 'cherry-neon', 'duration' => 210, 'release_date' => '2026-01-26', 'is_featured' => true],
            ['artist_slug' => 'leena-sol', 'album_slug' => 'electric-bloom', 'genre' => 'Pop', 'title' => 'Heartline', 'slug' => 'heartline', 'duration' => 205, 'release_date' => '2026-01-26', 'is_featured' => false],
            ['artist_slug' => 'leena-sol', 'album_slug' => 'electric-bloom', 'genre' => 'Pop', 'title' => 'Summer on Repeat', 'slug' => 'summer-on-repeat', 'duration' => 218, 'release_date' => '2026-01-26', 'is_featured' => false],
            ['artist_slug' => 'leena-sol', 'album_slug' => 'north-of-noise', 'genre' => 'Rock', 'title' => 'Chrome Guitars', 'slug' => 'chrome-guitars', 'duration' => 244, 'release_date' => '2025-09-27', 'is_featured' => true],
            ['artist_slug' => 'leena-sol', 'album_slug' => 'north-of-noise', 'genre' => 'Rock', 'title' => 'Stadium Rain', 'slug' => 'stadium-rain', 'duration' => 252, 'release_date' => '2025-09-27', 'is_featured' => false],
            ['artist_slug' => 'leena-sol', 'album_slug' => 'north-of-noise', 'genre' => 'Rock', 'title' => 'Fire Exit', 'slug' => 'fire-exit', 'duration' => 239, 'release_date' => '2025-09-27', 'is_featured' => false],
        ];
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

    private function audioSampleUrl(int $index): string
    {
        $sample = (($index - 1) % 16) + 1;

        return "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-{$sample}.mp3";
    }
}
