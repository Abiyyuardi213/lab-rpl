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
        Schema::table('pendaftaran_praktikums', function (Blueprint $table) {
            $table->string('bukti_krs')->nullable()->change();
            $table->string('bukti_pembayaran')->nullable()->change();
            $table->string('foto_almamater')->nullable()->change();
            $table->boolean('is_google_form')->default(false)->after('is_mengulang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran_praktikums', function (Blueprint $table) {
            $table->string('bukti_krs')->nullable(false)->change();
            $table->string('bukti_pembayaran')->nullable(false)->change();
            $table->string('foto_almamater')->nullable(false)->change();
            $table->dropColumn('is_google_form');
        });
    }
};
