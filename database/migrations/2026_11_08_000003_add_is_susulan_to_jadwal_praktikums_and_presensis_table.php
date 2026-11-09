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
        Schema::table('jadwal_praktikums', function (Blueprint $table) {
            $table->boolean('is_susulan')->default(false)->after('ruangan');
        });

        Schema::table('presensis', function (Blueprint $table) {
            $table->boolean('is_susulan')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_praktikums', function (Blueprint $table) {
            $table->dropColumn('is_susulan');
        });

        Schema::table('presensis', function (Blueprint $table) {
            $table->dropColumn('is_susulan');
        });
    }
};

