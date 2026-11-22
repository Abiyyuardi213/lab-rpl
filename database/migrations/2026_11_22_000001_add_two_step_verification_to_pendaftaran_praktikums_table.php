<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pendaftaran_praktikums', function (Blueprint $table) {
            $table->enum('status_lembar_kuning', ['pending', 'verified', 'rejected'])
                ->default('pending')
                ->after('is_google_form');
            $table->text('catatan_lembar_kuning')
                ->nullable()
                ->after('status_lembar_kuning');
            $table->enum('status_berkas', ['pending', 'verified', 'rejected'])
                ->default('pending')
                ->after('catatan_lembar_kuning');
        });

        // Backfill data yang sudah ada agar konsisten dengan status saat ini:
        DB::table('pendaftaran_praktikums')
            ->where('status', 'verified')
            ->update([
                'status_lembar_kuning' => 'verified',
                'status_berkas' => 'verified',
            ]);

        DB::table('pendaftaran_praktikums')
            ->where('status', 'rejected')
            ->update([
                'status_berkas' => 'rejected',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran_praktikums', function (Blueprint $table) {
            $table->dropColumn(['status_lembar_kuning', 'catatan_lembar_kuning', 'status_berkas']);
        });
    }
};
