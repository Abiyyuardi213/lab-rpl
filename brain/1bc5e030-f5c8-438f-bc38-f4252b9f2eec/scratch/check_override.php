<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Penugasan;
use App\Models\PendaftaranPraktikum;

echo "=== OVERRIDE RECORDS ===\n";
$overrides = DB::table('penugasan_praktikan_overrides')->get();
foreach ($overrides as $o) {
    echo "pendaftaran_id: {$o->pendaftaran_id} | penugasan_id: {$o->penugasan_id} | jadwal_id: {$o->jadwal_praktikum_id}\n";
    
    // Cek penugasan yang di-override
    $p = Penugasan::find($o->penugasan_id);
    if ($p) {
        echo "  -> Penugasan: [{$p->kode_akhir_npm}] {$p->judul} | file: " . ($p->file_soal ?? 'NULL') . " | sesi_id: {$p->sesi_id} | jadwal_id: {$p->jadwal_praktikum_id}\n";
        if ($p->file_soal) {
            $exists = \Illuminate\Support\Facades\Storage::disk('public')->exists($p->file_soal);
            echo "  -> File exists: " . ($exists ? 'YES' : 'NO') . "\n";
        }
    }
    
    // Cek pendaftaran
    $pendaftaran = PendaftaranPraktikum::with(['praktikan.user', 'sesi'])->find($o->pendaftaran_id);
    if ($pendaftaran) {
        $npm = $pendaftaran->praktikan->npm ?? '?';
        $nama = $pendaftaran->praktikan->user->name ?? '?';
        $sesiNama = $pendaftaran->sesi->nama_sesi ?? '?';
        echo "  -> Praktikan: {$nama} (NPM: {$npm}) | Sesi: {$sesiNama}\n";
    }
    echo "\n";
}

echo "\n=== PENUGASAN A1 (kode_akhir_npm = A1) ===\n";
$a1s = Penugasan::where('kode_akhir_npm', 'A1')->get();
foreach ($a1s as $a) {
    echo "ID: {$a->id} | Judul: {$a->judul} | file: " . ($a->file_soal ?? 'NULL') . " | sesi_id: {$a->sesi_id} | jadwal_praktikum_id: {$a->jadwal_praktikum_id}\n";
    if ($a->file_soal) {
        $exists = \Illuminate\Support\Facades\Storage::disk('public')->exists($a->file_soal);
        echo "  -> File exists: " . ($exists ? 'YES' : 'NO') . "\n";
    }
}
