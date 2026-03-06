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
        Schema::table('praktikums', function (Blueprint $table) {
            $table->integer('jumlah_modul')->default(0)->after('kuota_praktikan');
            $table->boolean('ada_tugas_akhir')->default(false)->after('jumlah_modul');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('praktikums', function (Blueprint $table) {
            $table->dropColumn(['jumlah_modul', 'ada_tugas_akhir']);
        });
    }
};
