<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $praktikums = DB::table('praktikums')->get();
        
        $now = now();
        
        foreach ($praktikums as $praktikum) {
            $dosens = json_decode($praktikum->daftar_dosen ?? '[]', true);
            if (is_array($dosens)) {
                foreach ($dosens as $dosenName) {
                    if (empty($dosenName)) continue;
                    
                    // Check if already exists in dosens table
                    $exists = DB::table('dosens')->where('nama', trim($dosenName))->exists();
                    if (!$exists) {
                        DB::table('dosens')->insert([
                            'id' => (string) Str::uuid(),
                            'nama' => trim($dosenName),
                            'nip' => null,
                            'is_active' => true,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }
                }
            }
            
            $kelasItems = json_decode($praktikum->daftar_kelas_mk ?? '[]', true);
            if (is_array($kelasItems)) {
                foreach ($kelasItems as $kelasName) {
                    if (empty($kelasName)) continue;
                    
                    // Check if already exists in kelas table
                    $exists = DB::table('kelas')->where('nama_kelas', trim($kelasName))->exists();
                    if (!$exists) {
                        DB::table('kelas')->insert([
                            'id' => (string) Str::uuid(),
                            'nama_kelas' => trim($kelasName),
                            'is_active' => true,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down migration logic needed for data transfer, 
        // as dropping the tables in previous migrations is sufficient.
    }
};
