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
        // Re-create praktikans table properly with UUID and relations
        Schema::dropIfExists('praktikans');
        Schema::create('praktikans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string('npm')->unique();
            $table->string('no_hp')->nullable();
            $table->string('jurusan')->nullable();
            $table->string('angkatan')->nullable();
            $table->timestamps();
        });

        // Create aslabs table
        Schema::create('aslabs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->string('npm')->unique();
            $table->string('no_hp')->nullable();
            $table->string('jurusan')->nullable();
            $table->string('angkatan')->nullable();
            $table->timestamps();
        });

        // Remove npm from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('npm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('npm')->nullable()->after('username');
        });

        Schema::dropIfExists('aslabs');
        Schema::dropIfExists('praktikans');

        // Restore simple praktikans table
        Schema::create('praktikans', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }
};
