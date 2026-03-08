<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('songs', function (Blueprint $table): void {
            if (!Schema::hasColumn('songs', 'description')) {
                $table->text('description')->nullable()->after('genre');
            }

            if (!Schema::hasColumn('songs', 'lyrics')) {
                $table->longText('lyrics')->nullable()->after('description');
            }

            if (!Schema::hasColumn('songs', 'release_date')) {
                $table->date('release_date')->nullable()->after('cover_image_url');
            }

            if (!Schema::hasColumn('songs', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('approved_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table): void {
            $columns = collect(['description', 'lyrics', 'release_date', 'published_at'])
                ->filter(fn (string $column) => Schema::hasColumn('songs', $column))
                ->values()
                ->all();

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
