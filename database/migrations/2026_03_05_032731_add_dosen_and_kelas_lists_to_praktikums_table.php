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
        Schema::table('praktikums', function (Blueprint $table) {
            $table->json('daftar_dosen')->nullable()->after('nama_praktikum');
            $table->json('daftar_kelas_mk')->nullable()->after('daftar_dosen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('praktikums', function (Blueprint $table) {
            $table->dropColumn(['daftar_dosen', 'daftar_kelas_mk']);
        });
    }
};
