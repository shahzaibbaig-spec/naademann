<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MusicPlatformSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            NaadUserSeeder::class,
            NaadGenreSeeder::class,
            NaadCatalogSeeder::class,
            NaadBannerSeeder::class,
            NaadSettingSeeder::class,
        ]);
    }
}
