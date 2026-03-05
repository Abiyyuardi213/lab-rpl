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
        Schema::create('aslab_praktikums', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('aslab_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('praktikum_id')->constrained('praktikums')->onDelete('cascade');
            $table->integer('kuota')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aslab_praktikums');
    }
};
