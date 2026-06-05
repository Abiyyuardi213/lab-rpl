<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== FOREIGN KEYS ON penugasan_praktikan_overrides ===\n";
$results = DB::select("
    SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'penugasan_praktikan_overrides'
      AND REFERENCED_TABLE_NAME IS NOT NULL
");

foreach ($results as $fk) {
    echo "Constraint: {$fk->CONSTRAINT_NAME}, Column: {$fk->COLUMN_NAME} -> {$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}\n";
}

echo "\n=== ALL INDEXES ON penugasan_praktikan_overrides ===\n";
$indexes = DB::select("SHOW INDEX FROM penugasan_praktikan_overrides");
foreach ($indexes as $index) {
    echo "Keyname: {$index->Key_name}, Unique: {$index->Non_unique}, Column: {$index->Column_name}\n";
}
