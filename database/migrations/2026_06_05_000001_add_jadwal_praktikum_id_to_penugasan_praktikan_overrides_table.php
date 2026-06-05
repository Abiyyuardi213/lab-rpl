<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Helper: cek apakah foreign key constraint ada di tabel.
     */
    private function foreignKeyExists(string $table, string $constraintName): bool
    {
        $result = DB::selectOne(
            "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND CONSTRAINT_NAME = ?
               AND CONSTRAINT_TYPE = 'FOREIGN KEY'",
            [$table, $constraintName]
        );
        return $result !== null;
    }

    /**
     * Helper: cek apakah index/unique key ada di tabel.
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $result = DB::selectOne(
            "SELECT INDEX_NAME FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND INDEX_NAME = ?",
            [$table, $indexName]
        );
        return $result !== null;
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tbl = 'penugasan_praktikan_overrides';

        // --- Langkah 1: Drop foreign key pendaftaran_id jika ada ---
        if ($this->foreignKeyExists($tbl, 'penugasan_praktikan_overrides_pendaftaran_id_foreign')) {
            DB::statement("ALTER TABLE `{$tbl}` DROP FOREIGN KEY `penugasan_praktikan_overrides_pendaftaran_id_foreign`");
        }

        // --- Langkah 2: Drop unique index pendaftaran_id jika ada ---
        if ($this->indexExists($tbl, 'penugasan_praktikan_overrides_pendaftaran_id_unique')) {
            DB::statement("ALTER TABLE `{$tbl}` DROP INDEX `penugasan_praktikan_overrides_pendaftaran_id_unique`");
        }

        // --- Langkah 3: Drop composite unique index jika sudah ada sebelumnya ---
        if ($this->indexExists($tbl, 'pendaftaran_jadwal_unique')) {
            DB::statement("ALTER TABLE `{$tbl}` DROP INDEX `pendaftaran_jadwal_unique`");
        }

        // --- Langkah 4: Drop foreign key jadwal_praktikum_id jika ada ---
        if ($this->foreignKeyExists($tbl, 'penugasan_praktikan_overrides_jadwal_praktikum_id_foreign')) {
            DB::statement("ALTER TABLE `{$tbl}` DROP FOREIGN KEY `penugasan_praktikan_overrides_jadwal_praktikum_id_foreign`");
        }

        // --- Langkah 5: Tangani kolom jadwal_praktikum_id ---
        if (Schema::hasColumn($tbl, 'jadwal_praktikum_id')) {
            // Cek tipe data kolom — jika bukan bigint, drop dan buat ulang
            $col = DB::selectOne(
                "SELECT DATA_TYPE FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = ?
                   AND COLUMN_NAME = 'jadwal_praktikum_id'",
                [$tbl]
            );
            if ($col && $col->DATA_TYPE !== 'bigint') {
                // Drop dulu index yang mungkin memakai kolom ini
                if ($this->indexExists($tbl, 'penugasan_praktikan_overrides_jadwal_praktikum_id_index')) {
                    DB::statement("ALTER TABLE `{$tbl}` DROP INDEX `penugasan_praktikan_overrides_jadwal_praktikum_id_index`");
                }
                Schema::table($tbl, function (Blueprint $table) {
                    $table->dropColumn('jadwal_praktikum_id');
                });
            }
        }

        // --- Langkah 6: Tambahkan kolom jadwal_praktikum_id jika belum ada ---
        if (!Schema::hasColumn($tbl, 'jadwal_praktikum_id')) {
            Schema::table($tbl, function (Blueprint $table) {
                $table->unsignedBigInteger('jadwal_praktikum_id')
                    ->nullable()
                    ->after('pendaftaran_id');
            });
        }

        // --- Langkah 7: Tambahkan foreign key ke jadwal_praktikums ---
        if (!$this->foreignKeyExists($tbl, 'penugasan_praktikan_overrides_jadwal_praktikum_id_foreign')) {
            DB::statement(
                "ALTER TABLE `{$tbl}` ADD CONSTRAINT `penugasan_praktikan_overrides_jadwal_praktikum_id_foreign`
                 FOREIGN KEY (`jadwal_praktikum_id`) REFERENCES `jadwal_praktikums` (`id`) ON DELETE CASCADE"
            );
        }

        // --- Langkah 8: Tambahkan kembali foreign key pendaftaran_id ---
        if (!$this->foreignKeyExists($tbl, 'penugasan_praktikan_overrides_pendaftaran_id_foreign')) {
            DB::statement(
                "ALTER TABLE `{$tbl}` ADD CONSTRAINT `penugasan_praktikan_overrides_pendaftaran_id_foreign`
                 FOREIGN KEY (`pendaftaran_id`) REFERENCES `pendaftaran_praktikums` (`id`) ON DELETE CASCADE"
            );
        }

        // --- Langkah 9: Tambahkan composite unique index ---
        if (!$this->indexExists($tbl, 'pendaftaran_jadwal_unique')) {
            DB::statement(
                "ALTER TABLE `{$tbl}` ADD UNIQUE KEY `pendaftaran_jadwal_unique` (`pendaftaran_id`, `jadwal_praktikum_id`)"
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tbl = 'penugasan_praktikan_overrides';

        // Drop composite unique index
        if ($this->indexExists($tbl, 'pendaftaran_jadwal_unique')) {
            DB::statement("ALTER TABLE `{$tbl}` DROP INDEX `pendaftaran_jadwal_unique`");
        }

        // Drop foreign key pendaftaran_id
        if ($this->foreignKeyExists($tbl, 'penugasan_praktikan_overrides_pendaftaran_id_foreign')) {
            DB::statement("ALTER TABLE `{$tbl}` DROP FOREIGN KEY `penugasan_praktikan_overrides_pendaftaran_id_foreign`");
        }

        // Drop foreign key jadwal_praktikum_id
        if ($this->foreignKeyExists($tbl, 'penugasan_praktikan_overrides_jadwal_praktikum_id_foreign')) {
            DB::statement("ALTER TABLE `{$tbl}` DROP FOREIGN KEY `penugasan_praktikan_overrides_jadwal_praktikum_id_foreign`");
        }

        // Drop kolom jadwal_praktikum_id
        if (Schema::hasColumn($tbl, 'jadwal_praktikum_id')) {
            Schema::table($tbl, function (Blueprint $table) {
                $table->dropColumn('jadwal_praktikum_id');
            });
        }

        // Kembalikan unique index pendaftaran_id
        if (!$this->indexExists($tbl, 'penugasan_praktikan_overrides_pendaftaran_id_unique')) {
            DB::statement(
                "ALTER TABLE `{$tbl}` ADD UNIQUE KEY `penugasan_praktikan_overrides_pendaftaran_id_unique` (`pendaftaran_id`)"
            );
        }

        // Kembalikan foreign key pendaftaran_id
        if (!$this->foreignKeyExists($tbl, 'penugasan_praktikan_overrides_pendaftaran_id_foreign')) {
            DB::statement(
                "ALTER TABLE `{$tbl}` ADD CONSTRAINT `penugasan_praktikan_overrides_pendaftaran_id_foreign`
                 FOREIGN KEY (`pendaftaran_id`) REFERENCES `pendaftaran_praktikums` (`id`) ON DELETE CASCADE"
            );
        }
    }
};
