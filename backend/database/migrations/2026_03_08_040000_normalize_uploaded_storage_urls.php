<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach ($this->targets() as $table => $columns) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column) {
                if (!Schema::hasColumn($table, $column)) {
                    continue;
                }

                DB::table($table)
                    ->select(['id', $column])
                    ->orderBy('id')
                    ->get()
                    ->each(function (object $record) use ($table, $column): void {
                        $normalized = $this->normalizeStorageUrl($record->{$column});

                        if ($normalized !== $record->{$column}) {
                            DB::table($table)
                                ->where('id', $record->id)
                                ->update([$column => $normalized]);
                        }
                    });
            }
        }
    }

    public function down(): void
    {
        // Relative /storage URLs are portable across local environments.
    }

    private function targets(): array
    {
        return [
            'users' => ['avatar_url'],
            'artists' => ['image_url'],
            'artist_profiles' => ['avatar_url', 'cover_image_url'],
            'genres' => ['image_url'],
            'albums' => ['cover_image_url'],
            'songs' => ['audio_url', 'cover_image_url'],
            'tracks' => ['audio_url', 'cover_image_url'],
            'playlists' => ['cover_image_url'],
            'banners' => ['image_url'],
        ];
    }

    private function normalizeStorageUrl(mixed $value): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        $value = trim($value);

        if ($value === '') {
            return $value;
        }

        if (str_starts_with($value, '/storage/')) {
            return $value;
        }

        if (str_starts_with($value, 'storage/')) {
            return '/'.$value;
        }

        $path = parse_url($value, PHP_URL_PATH);

        if (is_string($path) && str_starts_with($path, '/storage/')) {
            return $path;
        }

        return $value;
    }
};
