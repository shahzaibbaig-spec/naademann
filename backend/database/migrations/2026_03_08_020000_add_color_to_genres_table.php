<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('genres', function (Blueprint $table): void {
            if (!Schema::hasColumn('genres', 'color')) {
                $table->string('color', 24)->nullable()->after('image_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('genres', function (Blueprint $table): void {
            if (Schema::hasColumn('genres', 'color')) {
                $table->dropColumn('color');
            }
        });
    }
};
