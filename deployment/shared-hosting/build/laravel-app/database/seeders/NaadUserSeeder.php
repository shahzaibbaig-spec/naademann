<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class NaadUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Naad Super Admin',
                'email' => 'superadmin@naademaan.test',
                'role' => 'super_admin',
                'headline' => 'Platform architect',
                'bio' => 'Controls system-wide curation, moderation, and release strategy for Naad-e-Maan.',
            ],
            [
                'name' => 'Naad Admin',
                'email' => 'admin@naademaan.test',
                'role' => 'admin',
                'headline' => 'Operations lead',
                'bio' => 'Coordinates homepage campaigns, user operations, and platform-quality reviews.',
            ],
            [
                'name' => 'Mira Skye',
                'email' => 'mira@naademaan.test',
                'role' => 'creator',
                'headline' => 'Neo-soul architect',
                'bio' => 'Builds intimate songs with luminous bass, warm vocals, and widescreen atmosphere.',
            ],
            [
                'name' => 'Rehan Pulse',
                'email' => 'rehan@naademaan.test',
                'role' => 'creator',
                'headline' => 'Electronic pressure builder',
                'bio' => 'Designs glowing club records and cinematic low-end for midnight listening sessions.',
            ],
            [
                'name' => 'Kian Drift',
                'email' => 'kian@naademaan.test',
                'role' => 'creator',
                'headline' => 'Hip hop storyteller',
                'bio' => 'Writes sharp verses around city detail, nocturnal textures, and melodic hooks.',
            ],
            [
                'name' => 'Aanya Raag',
                'email' => 'aanya@naademaan.test',
                'role' => 'creator',
                'headline' => 'Classical crossover composer',
                'bio' => 'Brings raga phrasing, chamber dynamics, and modern production into one stage identity.',
            ],
            [
                'name' => 'Leena Sol',
                'email' => 'leena@naademaan.test',
                'role' => 'creator',
                'headline' => 'Pop radiance writer',
                'bio' => 'Combines bright hooks, emotional toplines, and live-band energy for premium pop releases.',
            ],
        ];

        foreach ($users as $index => $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make('password'),
                    'role' => $user['role'],
                    'api_token' => null,
                    'avatar_url' => $this->placeholderImage($user['name'], '500x500', '020617', $this->accentForIndex($index)),
                    'headline' => $user['headline'],
                    'bio' => $user['bio'],
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
        return ['22d3ee', 'f472b6', 'f59e0b', '34d399', '60a5fa', 'f97316', 'e879f9'][$index % 7];
    }
}
