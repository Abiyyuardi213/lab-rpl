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
        Schema::create('penilaian_praktikums', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('presensi_id')->constrained('presensis')->onDelete('cascade');
            $table->foreignUuid('aslab_id')->constrained('aslabs')->onDelete('cascade');
            $table->integer('nilai');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_praktikums');
    }
};
