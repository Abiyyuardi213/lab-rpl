<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE pendaftaran_praktikums MODIFY COLUMN status ENUM('pending', 'verified', 'rejected', 'gugur') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE pendaftaran_praktikums MODIFY COLUMN status ENUM('pending', 'verified', 'rejected') NOT NULL DEFAULT 'pending'");
    }
};

