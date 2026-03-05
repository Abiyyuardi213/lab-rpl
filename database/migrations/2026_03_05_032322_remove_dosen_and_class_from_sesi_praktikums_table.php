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
        Schema::table('sesi_praktikums', function (Blueprint $table) {
            $table->dropColumn(['dosen_pengampu', 'asal_kelas_mata_kuliah']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sesi_praktikums', function (Blueprint $table) {
            $table->string('dosen_pengampu')->nullable()->after('nama_sesi');
            $table->string('asal_kelas_mata_kuliah')->nullable()->after('dosen_pengampu');
        });
    }
};
