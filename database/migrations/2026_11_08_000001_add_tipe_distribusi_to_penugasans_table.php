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
        Schema::table('penugasans', function (Blueprint $table) {
            $table->enum('tipe_distribusi', ['by_npm', 'by_sesi', 'by_random'])
                ->default('by_npm')
                ->after('jadwal_praktikum_id')
                ->comment('Mode distribusi penugasan soal: by_npm, by_sesi, by_random');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penugasans', function (Blueprint $table) {
            $table->dropColumn('tipe_distribusi');
        });
    }
};
