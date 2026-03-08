<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $this->ensureGenresTable();
        $this->ensureArtistProfilesTable();
        $this->ensureAlbumsTable();
        $this->ensureTracksTable();
        $this->ensureTrackViewsTable();
        $this->ensurePlaylistsTable();
        $this->ensurePlaylistTrackTable();
        $this->ensureFavoritesTable();
        $this->ensureFollowsTable();
        $this->ensureBannersTable();
        $this->ensureSiteSettingsTable();
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('follows');

        if (Schema::hasTable('favorites')) {
            Schema::table('favorites', function (Blueprint $table): void {
                if (Schema::hasColumn('favorites', 'track_id')) {
                    $table->dropConstrainedForeignId('track_id');
                }
            });
        }

        Schema::dropIfExists('playlist_track');

        if (Schema::hasTable('playlists')) {
            Schema::table('playlists', function (Blueprint $table): void {
                $columns = collect(['status', 'is_published', 'is_featured', 'published_at'])
                    ->filter(fn (string $column) => Schema::hasColumn('playlists', $column))
                    ->values()
                    ->all();

                if ($columns !== []) {
                    $table->dropColumn($columns);
                }
            });
        }

        Schema::dropIfExists('track_views');
        Schema::dropIfExists('tracks');

        if (Schema::hasTable('albums')) {
            Schema::table('albums', function (Blueprint $table): void {
                if (Schema::hasColumn('albums', 'artist_profile_id')) {
                    $table->dropConstrainedForeignId('artist_profile_id');
                }

                if (Schema::hasColumn('albums', 'genre_id')) {
                    $table->dropConstrainedForeignId('genre_id');
                }

                $columns = collect(['status', 'is_published', 'is_featured', 'published_at'])
                    ->filter(fn (string $column) => Schema::hasColumn('albums', $column))
                    ->values()
                    ->all();

                if ($columns !== []) {
                    $table->dropColumn($columns);
                }
            });
        }

        Schema::dropIfExists('artist_profiles');

        if (Schema::hasTable('genres')) {
            Schema::table('genres', function (Blueprint $table): void {
                $columns = collect(['status', 'is_published', 'is_featured', 'published_at'])
                    ->filter(fn (string $column) => Schema::hasColumn('genres', $column))
                    ->values()
                    ->all();

                if ($columns !== []) {
                    $table->dropColumn($columns);
                }
            });
        }

        if (Schema::hasTable('banners')) {
            Schema::table('banners', function (Blueprint $table): void {
                $columns = collect(['slug', 'status', 'is_published', 'is_featured', 'published_at'])
                    ->filter(fn (string $column) => Schema::hasColumn('banners', $column))
                    ->values()
                    ->all();

                if ($columns !== []) {
                    $table->dropColumn($columns);
                }
            });
        }
    }

    private function ensureGenresTable(): void
    {
        if (!Schema::hasTable('genres')) {
            return;
        }

        Schema::table('genres', function (Blueprint $table): void {
            if (!Schema::hasColumn('genres', 'status')) {
                $table->string('status')->default('published')->after('color');
            }

            if (!Schema::hasColumn('genres', 'is_published')) {
                $table->boolean('is_published')->default(true)->after('status');
            }

            if (!Schema::hasColumn('genres', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('is_published');
            }

            if (!Schema::hasColumn('genres', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('is_featured');
            }
        });

        DB::table('genres')
            ->get(['id', 'is_active', 'status', 'is_published', 'published_at', 'created_at'])
            ->each(function (object $genre): void {
                DB::table('genres')
                    ->where('id', $genre->id)
                    ->update([
                        'status' => $genre->status ?: ($genre->is_active ? 'published' : 'draft'),
                        'is_published' => $genre->is_published ?? (bool) $genre->is_active,
                        'published_at' => $genre->published_at ?: ($genre->is_active ? ($genre->created_at ?: now()) : null),
                    ]);
            });
    }

    private function ensureArtistProfilesTable(): void
    {
        if (!Schema::hasTable('artist_profiles')) {
            Schema::create('artist_profiles', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('genre_id')->nullable()->constrained('genres')->nullOnDelete();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('bio')->nullable();
                $table->string('avatar_url')->nullable();
                $table->string('cover_image_url')->nullable();
                $table->string('status')->default('draft');
                $table->boolean('is_published')->default(false);
                $table->boolean('is_featured')->default(false);
                $table->timestamp('published_at')->nullable();
                $table->unsignedBigInteger('monthly_listeners')->default(0);
                $table->unsignedBigInteger('followers_count')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('artists')) {
            return;
        }

        $genreMap = $this->genreMap();
        $records = DB::table('artists')
            ->get()
            ->map(function (object $artist) use ($genreMap): array {
                return [
                    'id' => $artist->id,
                    'user_id' => $artist->user_id,
                    'genre_id' => $genreMap[Str::lower(trim((string) $artist->genre))] ?? null,
                    'name' => $artist->name,
                    'slug' => $artist->slug ?: ('artist-'.$artist->id),
                    'bio' => $artist->bio,
                    'avatar_url' => $artist->image_url,
                    'cover_image_url' => $artist->image_url,
                    'status' => 'published',
                    'is_published' => true,
                    'is_featured' => false,
                    'published_at' => $artist->created_at ?: now(),
                    'monthly_listeners' => $artist->monthly_listeners ?? 0,
                    'followers_count' => $artist->followers ?? 0,
                    'created_at' => $artist->created_at ?: now(),
                    'updated_at' => $artist->updated_at ?: now(),
                ];
            })
            ->all();

        if ($records !== []) {
            DB::table('artist_profiles')->upsert(
                $records,
                ['id'],
                ['user_id', 'genre_id', 'name', 'slug', 'bio', 'avatar_url', 'cover_image_url', 'status', 'is_published', 'is_featured', 'published_at', 'monthly_listeners', 'followers_count', 'updated_at']
            );
        }
    }

    private function ensureAlbumsTable(): void
    {
        if (!Schema::hasTable('albums')) {
            return;
        }

        Schema::table('albums', function (Blueprint $table): void {
            if (!Schema::hasColumn('albums', 'artist_profile_id')) {
                $table->foreignId('artist_profile_id')->nullable()->constrained('artist_profiles')->nullOnDelete()->after('artist_id');
            }

            if (!Schema::hasColumn('albums', 'genre_id')) {
                $table->foreignId('genre_id')->nullable()->constrained('genres')->nullOnDelete()->after('genre');
            }

            if (!Schema::hasColumn('albums', 'status')) {
                $table->string('status')->default('draft')->after('release_date');
            }

            if (!Schema::hasColumn('albums', 'is_published')) {
                $table->boolean('is_published')->default(false)->after('status');
            }

            if (!Schema::hasColumn('albums', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('is_published');
            }

            if (!Schema::hasColumn('albums', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('is_featured');
            }
        });

        $genreMap = $this->genreMap();
        DB::table('albums')
            ->get(['id', 'artist_id', 'genre', 'release_date', 'created_at', 'artist_profile_id', 'genre_id', 'status', 'is_published', 'published_at'])
            ->each(function (object $album) use ($genreMap): void {
                DB::table('albums')
                    ->where('id', $album->id)
                    ->update([
                        'artist_profile_id' => $album->artist_profile_id ?: $album->artist_id,
                        'genre_id' => $album->genre_id ?: ($genreMap[Str::lower(trim((string) $album->genre))] ?? null),
                        'status' => $album->status ?: 'published',
                        'is_published' => $album->is_published ?? true,
                        'published_at' => $album->published_at ?: ($album->release_date ?: ($album->created_at ?: now())),
                    ]);
            });
    }

    private function ensureTracksTable(): void
    {
        if (!Schema::hasTable('tracks')) {
            Schema::create('tracks', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('artist_profile_id')->constrained('artist_profiles')->cascadeOnDelete();
                $table->foreignId('genre_id')->nullable()->constrained('genres')->nullOnDelete();
                $table->foreignId('album_id')->nullable()->constrained()->nullOnDelete();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->longText('lyrics')->nullable();
                $table->unsignedInteger('duration')->default(0);
                $table->string('audio_url');
                $table->string('cover_image_url')->nullable();
                $table->date('release_date')->nullable();
                $table->string('status')->default('draft');
                $table->boolean('is_published')->default(false);
                $table->boolean('is_featured')->default(false);
                $table->timestamp('published_at')->nullable();
                $table->unsignedBigInteger('views_count')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('songs')) {
            return;
        }

        $genreMap = $this->genreMap();
        $records = DB::table('songs')
            ->get()
            ->map(function (object $song) use ($genreMap): array {
                $legacyStatus = (string) ($song->moderation_status ?? 'draft');
                $status = match ($legacyStatus) {
                    'approved' => 'published',
                    'pending' => 'pending',
                    'rejected' => 'rejected',
                    default => 'draft',
                };

                $publishedAt = $song->published_at ?? $song->approved_at ?? null;

                return [
                    'id' => $song->id,
                    'artist_profile_id' => $song->artist_id,
                    'genre_id' => $genreMap[Str::lower(trim((string) $song->genre))] ?? null,
                    'album_id' => $song->album_id,
                    'title' => $song->title,
                    'slug' => $song->slug ?: ('track-'.$song->id),
                    'description' => $song->description ?? null,
                    'lyrics' => $song->lyrics ?? null,
                    'duration' => $song->duration ?? 0,
                    'audio_url' => $song->audio_url,
                    'cover_image_url' => $song->cover_image_url,
                    'release_date' => $song->release_date ?? null,
                    'status' => $status,
                    'is_published' => $legacyStatus === 'approved',
                    'is_featured' => (bool) ($song->is_featured ?? false),
                    'published_at' => $legacyStatus === 'approved' ? ($publishedAt ?: ($song->created_at ?: now())) : null,
                    'views_count' => $song->streams_count ?? 0,
                    'created_at' => $song->created_at ?: now(),
                    'updated_at' => $song->updated_at ?: now(),
                ];
            })
            ->all();

        if ($records !== []) {
            DB::table('tracks')->upsert(
                $records,
                ['id'],
                ['artist_profile_id', 'genre_id', 'album_id', 'title', 'slug', 'description', 'lyrics', 'duration', 'audio_url', 'cover_image_url', 'release_date', 'status', 'is_published', 'is_featured', 'published_at', 'views_count', 'updated_at']
            );
        }
    }

    private function ensureTrackViewsTable(): void
    {
        if (!Schema::hasTable('track_views')) {
            Schema::create('track_views', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('track_id')->constrained('tracks')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('ip_address', 45)->nullable();
                $table->timestamp('viewed_at')->useCurrent();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('streams')) {
            return;
        }

        $records = DB::table('streams')
            ->get()
            ->map(fn (object $stream): array => [
                'id' => $stream->id,
                'track_id' => $stream->song_id,
                'user_id' => $stream->user_id,
                'ip_address' => $stream->ip_address,
                'viewed_at' => $stream->played_at ?: ($stream->created_at ?: now()),
                'created_at' => $stream->created_at ?: now(),
                'updated_at' => $stream->updated_at ?: now(),
            ])
            ->all();

        if ($records !== []) {
            DB::table('track_views')->upsert(
                $records,
                ['id'],
                ['track_id', 'user_id', 'ip_address', 'viewed_at', 'updated_at']
            );
        }
    }

    private function ensurePlaylistsTable(): void
    {
        if (!Schema::hasTable('playlists')) {
            return;
        }

        Schema::table('playlists', function (Blueprint $table): void {
            if (!Schema::hasColumn('playlists', 'status')) {
                $table->string('status')->default('draft')->after('is_public');
            }

            if (!Schema::hasColumn('playlists', 'is_published')) {
                $table->boolean('is_published')->default(false)->after('status');
            }

            if (!Schema::hasColumn('playlists', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('is_published');
            }

            if (!Schema::hasColumn('playlists', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('is_featured');
            }
        });

        DB::table('playlists')
            ->get(['id', 'is_public', 'created_at', 'status', 'is_published', 'published_at'])
            ->each(function (object $playlist): void {
                DB::table('playlists')
                    ->where('id', $playlist->id)
                    ->update([
                        'status' => $playlist->status ?: ($playlist->is_public ? 'published' : 'draft'),
                        'is_published' => $playlist->is_published ?? (bool) $playlist->is_public,
                        'published_at' => $playlist->published_at ?: ($playlist->is_public ? ($playlist->created_at ?: now()) : null),
                    ]);
            });
    }

    private function ensurePlaylistTrackTable(): void
    {
        if (!Schema::hasTable('playlist_track')) {
            Schema::create('playlist_track', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('playlist_id')->constrained()->cascadeOnDelete();
                $table->foreignId('track_id')->constrained('tracks')->cascadeOnDelete();
                $table->unsignedInteger('position')->default(0);
                $table->timestamps();
                $table->unique(['playlist_id', 'track_id']);
            });
        }

        if (!Schema::hasTable('playlists') || !Schema::hasColumn('playlists', 'song_ids')) {
            return;
        }

        $records = [];

        DB::table('playlists')->get(['id', 'song_ids', 'created_at', 'updated_at'])->each(function (object $playlist) use (&$records): void {
            $songIds = is_array($playlist->song_ids)
                ? $playlist->song_ids
                : json_decode((string) $playlist->song_ids, true);

            foreach (array_values($songIds ?: []) as $position => $trackId) {
                $records[] = [
                    'playlist_id' => $playlist->id,
                    'track_id' => $trackId,
                    'position' => $position + 1,
                    'created_at' => $playlist->created_at ?: now(),
                    'updated_at' => $playlist->updated_at ?: now(),
                ];
            }
        });

        if ($records !== []) {
            DB::table('playlist_track')->upsert(
                $records,
                ['playlist_id', 'track_id'],
                ['position', 'updated_at']
            );
        }
    }

    private function ensureFavoritesTable(): void
    {
        if (!Schema::hasTable('favorites')) {
            return;
        }

        Schema::table('favorites', function (Blueprint $table): void {
            if (!Schema::hasColumn('favorites', 'track_id')) {
                $table->foreignId('track_id')->nullable()->constrained('tracks')->nullOnDelete()->after('song_id');
                $table->unique(['user_id', 'track_id']);
            }
        });

        DB::table('favorites')
            ->whereNull('track_id')
            ->update([
                'track_id' => DB::raw('song_id'),
            ]);
    }

    private function ensureFollowsTable(): void
    {
        if (!Schema::hasTable('follows')) {
            Schema::create('follows', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('artist_profile_id')->constrained('artist_profiles')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['user_id', 'artist_profile_id']);
            });
        }

        if (!Schema::hasTable('artist_follows')) {
            return;
        }

        $records = DB::table('artist_follows')
            ->get()
            ->map(fn (object $follow): array => [
                'id' => $follow->id,
                'user_id' => $follow->user_id,
                'artist_profile_id' => $follow->artist_id,
                'created_at' => $follow->created_at ?: now(),
                'updated_at' => $follow->updated_at ?: now(),
            ])
            ->all();

        if ($records !== []) {
            DB::table('follows')->upsert(
                $records,
                ['id'],
                ['user_id', 'artist_profile_id', 'updated_at']
            );
        }
    }

    private function ensureBannersTable(): void
    {
        if (!Schema::hasTable('banners')) {
            return;
        }

        Schema::table('banners', function (Blueprint $table): void {
            if (!Schema::hasColumn('banners', 'slug')) {
                $table->string('slug')->nullable()->after('title');
                $table->unique('slug');
            }

            if (!Schema::hasColumn('banners', 'status')) {
                $table->string('status')->default('draft')->after('sort_order');
            }

            if (!Schema::hasColumn('banners', 'is_published')) {
                $table->boolean('is_published')->default(false)->after('status');
            }

            if (!Schema::hasColumn('banners', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('is_published');
            }

            if (!Schema::hasColumn('banners', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('is_featured');
            }
        });

        $usedSlugs = [];

        DB::table('banners')
            ->orderBy('id')
            ->get(['id', 'title', 'slug', 'is_active', 'created_at', 'status', 'is_published', 'published_at'])
            ->each(function (object $banner) use (&$usedSlugs): void {
                $slug = $banner->slug ?: $this->uniqueSlug((string) $banner->title, $usedSlugs, 'banner');
                $usedSlugs[] = $slug;

                DB::table('banners')
                    ->where('id', $banner->id)
                    ->update([
                        'slug' => $slug,
                        'status' => $banner->status ?: ($banner->is_active ? 'published' : 'draft'),
                        'is_published' => $banner->is_published ?? (bool) $banner->is_active,
                        'published_at' => $banner->published_at ?: ($banner->is_active ? ($banner->created_at ?: now()) : null),
                    ]);
            });
    }

    private function ensureSiteSettingsTable(): void
    {
        if (!Schema::hasTable('site_settings')) {
            Schema::create('site_settings', function (Blueprint $table): void {
                $table->id();
                $table->string('key')->unique();
                $table->string('label')->nullable();
                $table->text('value')->nullable();
                $table->string('group')->default('general');
                $table->string('type')->default('text');
                $table->boolean('is_public')->default(false);
                $table->string('status')->default('published');
                $table->timestamps();
            });
        }

        $records = collect();

        if (Schema::hasTable('platform_settings')) {
            $records = DB::table('platform_settings')
                ->get()
                ->map(fn (object $setting): array => [
                    'key' => $setting->key,
                    'label' => $setting->label ?? Str::headline(str_replace('_', ' ', (string) $setting->key)),
                    'value' => $setting->value,
                    'group' => str_starts_with((string) $setting->key, 'social_') ? 'social' : 'branding',
                    'type' => $setting->type ?? 'text',
                    'is_public' => true,
                    'status' => 'published',
                    'created_at' => $setting->created_at ?: now(),
                    'updated_at' => $setting->updated_at ?: now(),
                ]);
        }

        if ($records->isEmpty()) {
            $records = collect([
                [
                    'key' => 'logo_text',
                    'label' => 'Logo Text',
                    'value' => 'Naad-e-Maan',
                    'group' => 'branding',
                    'type' => 'text',
                    'is_public' => true,
                    'status' => 'published',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'platform_tagline',
                    'label' => 'Platform Tagline',
                    'value' => 'The Sound of the Soul',
                    'group' => 'branding',
                    'type' => 'text',
                    'is_public' => true,
                    'status' => 'published',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        DB::table('site_settings')->upsert(
            $records->all(),
            ['key'],
            ['label', 'value', 'group', 'type', 'is_public', 'status', 'updated_at']
        );
    }

    private function genreMap(): array
    {
        if (!Schema::hasTable('genres')) {
            return [];
        }

        return DB::table('genres')
            ->get(['id', 'name'])
            ->mapWithKeys(fn (object $genre): array => [Str::lower(trim((string) $genre->name)) => $genre->id])
            ->all();
    }

    private function uniqueSlug(string $value, array $usedSlugs, string $fallback): string
    {
        $base = Str::slug($value) ?: $fallback;
        $slug = $base;
        $index = 1;

        while (in_array($slug, $usedSlugs, true) || DB::table('banners')->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$index;
            $index++;
        }

        return $slug;
    }
};
