<?php

use App\Models\PendaftaranPraktikum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$columns = DB::select('SHOW COLUMNS FROM pendaftaran_praktikums');
print_r($columns);
