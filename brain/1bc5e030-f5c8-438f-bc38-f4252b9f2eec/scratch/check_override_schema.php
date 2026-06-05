<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== SHOW COLUMNS FROM penugasan_praktikan_overrides ===\n";
$columns = DB::select('SHOW COLUMNS FROM penugasan_praktikan_overrides');
foreach ($columns as $column) {
    echo "Field: {$column->Field}, Type: {$column->Type}, Key: {$column->Key}, Default: {$column->Default}\n";
}

echo "\n=== TOTAL OVERRIDES RECORDED ===\n";
$overrides = DB::table('penugasan_praktikan_overrides')->get();
foreach ($overrides as $o) {
    echo "ID: {$o->id}, Pendaftaran ID: {$o->pendaftaran_id}, Penugasan ID: {$o->penugasan_id}\n";
}
