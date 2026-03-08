<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role')->default('listener')->after('password');
            $table->string('headline')->nullable()->after('avatar_url');
            $table->text('bio')->nullable()->after('headline');
        });

        Schema::table('songs', function (Blueprint $table): void {
            $table->string('moderation_status')->default('approved')->after('is_featured');
            $table->timestamp('approved_at')->nullable()->after('moderation_status');
        });
    }

    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table): void {
            $table->dropColumn(['moderation_status', 'approved_at']);
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['role', 'headline', 'bio']);
        });
    }
};
