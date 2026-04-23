<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Penugasan;
use App\Models\Praktikum;
use App\Models\SesiPraktikum;

try {
    $s = SesiPraktikum::first();
    $p = Praktikum::find($s->praktikum_id);

    echo "Testing insert with empty string for jadwal_praktikum_id...\n";

    $penugasan = Penugasan::create([
        'praktikum_id' => $p->id,
        'sesi_id' => $s->id,
        'jadwal_praktikum_id' => '', // EMPTY STRING
        'kode_akhir_npm' => 0,
        'judul' => 'Test Empty String ' . time(),
        'deskripsi' => 'Test Description',
    ]);
    echo "Success: " . $penugasan->id . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
