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
            $table->foreignUuid('sesi_id')->nullable()->after('praktikum_id')->constrained('sesi_praktikums')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_praktikums', function (Blueprint $table) {
            $table->dropForeign(['sesi_id']);
            $table->dropColumn('sesi_id');
        });
    }
};
