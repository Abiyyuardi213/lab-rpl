<?php
chdir(__DIR__);
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Penugasan;

// Cek salah satu penugasan A1 - path file-nya
$p = Penugasan::find('019e9576-3ae6-7301-8c08-1a0967aed99d');
if ($p) {
    echo "file_soal value: " . $p->file_soal . "\n";
    $storagePath = storage_path('app/public/' . $p->file_soal);
    $publicPath = public_path('storage/' . $p->file_soal);
    echo "Storage path: " . $storagePath . "\n";
    echo "File exists at storage path: " . (file_exists($storagePath) ? 'YES' : 'NO') . "\n";
    echo "Public path: " . $publicPath . "\n";
    echo "File exists at public path: " . (file_exists($publicPath) ? 'YES' : 'NO') . "\n";
}

echo "\n=== Storage disk public path ===\n";
echo \Illuminate\Support\Facades\Storage::disk('public')->path('') . "\n";

echo "\n=== Cek folder penugasan_soal ===\n";
$dir = storage_path('app/public/penugasan_soal');
if (is_dir($dir)) {
    $files = scandir($dir);
    echo "Files in penugasan_soal: " . count($files) . "\n";
    foreach (array_slice($files, 0, 10) as $f) {
        echo "  " . $f . "\n";
    }
} else {
    echo "Folder penugasan_soal TIDAK ADA\n";
}
