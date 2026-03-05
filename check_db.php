<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$columns = DB::select('SHOW COLUMNS FROM pendaftaran_praktikums');
foreach ($columns as $column) {
    echo "Field: {$column->Field}, Type: {$column->Type}\n";
}
