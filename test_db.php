<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Penugasan;
use App\Models\Praktikum;
use App\Models\SesiPraktikum;
use App\Models\JadwalPraktikum;

try {
    $s = SesiPraktikum::first();
    $p = Praktikum::find($s->praktikum_id);
    $j = JadwalPraktikum::find(5);

    echo "Praktikum: " . ($p ? $p->id : 'NOT FOUND') . "\n";
    echo "Sesi: " . ($s ? $s->id : 'NOT FOUND') . "\n";
    echo "Jadwal: " . ($j ? $j->id : 'NOT FOUND') . "\n";

    if ($p && $s && $j) {
        $penugasan = Penugasan::create([
            'praktikum_id' => $p->id,
            'sesi_id' => $s->id,
            'jadwal_praktikum_id' => 5,
            'kode_akhir_npm' => 0,
            'judul' => 'Test Script ' . time(),
            'deskripsi' => 'Test Description',
        ]);
        echo "Success: " . $penugasan->id . "\n";
    } else {
        echo "Missing data to perform test.\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
