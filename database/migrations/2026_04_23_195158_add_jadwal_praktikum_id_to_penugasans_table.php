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
            $table->foreignId('jadwal_praktikum_id')->nullable()->after('sesi_id')->constrained('jadwal_praktikums')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penugasans', function (Blueprint $table) {
            $table->dropForeign(['jadwal_praktikum_id']);
            $table->dropColumn('jadwal_praktikum_id');
        });
    }
};
