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
            $table->uuid('aslab_id')->nullable()->change();
            $table->tinyInteger('kode_akhir_npm')->nullable()->after('sesi_id')->comment('Digit terakhir NPM yang berhak mengakses soal ini');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penugasans', function (Blueprint $table) {
            $table->uuid('aslab_id')->nullable(false)->change();
            $table->dropColumn('kode_akhir_npm');
        });
    }
};
