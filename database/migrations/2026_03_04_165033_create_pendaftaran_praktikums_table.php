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
        Schema::create('pendaftaran_praktikums', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('praktikum_id')->constrained('praktikums')->onDelete('cascade');
            $table->foreignUuid('sesi_id')->constrained('sesi_praktikums')->onDelete('cascade');
            $table->string('no_hp');
            $table->string('dosen_pengampu');
            $table->enum('kelas', ['pagi', 'malam']);
            $table->string('asal_kelas_mata_kuliah');
            $table->string('bukti_krs');
            $table->string('bukti_pembayaran');
            $table->string('foto_almamater');
            $table->boolean('is_mengulang')->default(false);
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_praktikums');
    }
};
