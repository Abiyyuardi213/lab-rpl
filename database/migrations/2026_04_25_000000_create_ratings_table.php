<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pendaftaran_id')->constrained('pendaftaran_praktikums')->onDelete('cascade');
            $table->integer('rating_praktikum')->nullable();
            $table->text('ulasan_praktikum')->nullable();
            $table->integer('rating_asisten')->nullable();
            $table->text('ulasan_asisten')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
