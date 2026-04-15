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
        Schema::table('digit_npms', function (Blueprint $table) {
            $table->string('digit', 20)->change();
        });

        Schema::table('penugasans', function (Blueprint $table) {
            $table->string('kode_akhir_npm', 20)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penugasans', function (Blueprint $table) {
            $table->tinyInteger('kode_akhir_npm')->nullable()->change();
        });

        Schema::table('digit_npms', function (Blueprint $table) {
            $table->tinyInteger('digit')->change();
        });
    }
};
