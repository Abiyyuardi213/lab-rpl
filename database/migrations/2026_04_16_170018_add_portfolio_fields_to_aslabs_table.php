<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('aslabs', function (Blueprint $table) {
            $table->string('profile_image')->nullable()->after('angkatan');
            $table->string('slug')->unique()->nullable()->after('profile_image');
            $table->text('bio')->nullable()->after('slug');
            $table->json('skills')->nullable()->after('bio');
            $table->string('instagram_link')->nullable()->after('skills');
            $table->string('github_link')->nullable()->after('instagram_link');
            $table->string('linkedin_link')->nullable()->after('github_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aslabs', function (Blueprint $table) {
            $table->dropColumn([
                'profile_image',
                'slug',
                'bio',
                'skills',
                'instagram_link',
                'github_link',
                'linkedin_link'
            ]);
        });
    }
};
