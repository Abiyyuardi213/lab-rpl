<?php

use App\Models\Penugasan;
use App\Models\PendaftaranPraktikum;
use App\Models\PenugasanPraktikanOverride;

require __DIR__ . '/../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== INSPECTING PENUGASAN 'A1' ===\n";
$a1Penugasans = Penugasan::where('kode_akhir_npm', 'A1')->with('sesi', 'praktikum')->get();
foreach ($a1Penugasans as $p) {
    echo "ID: {$p->id}, Judul: {$p->judul}, Sesi: " . ($p->sesi->nama_sesi ?? 'N/A') . " (ID: {$p->sesi_id}), Praktikum: {$p->praktikum->nama_praktikum}\n";
}

echo "\n=== INSPECTING OVERRIDES IN THE SYSTEM ===\n";
$overrides = PenugasanPraktikanOverride::with(['pendaftaran.praktikan.user', 'penugasan'])->get();
foreach ($overrides as $o) {
    echo "Override ID: {$o->id}\n";
    echo "  - Student: {$o->pendaftaran->praktikan->user->name} (NPM: {$o->pendaftaran->praktikan->npm})\n";
    echo "  - Student Sesi: " . ($o->pendaftaran->sesi->nama_sesi ?? 'N/A') . " (ID: {$o->pendaftaran->sesi_id})\n";
    echo "  - Assigned Penugasan: {$o->penugasan->judul} (Kode NPM: {$o->penugasan->kode_akhir_npm})\n";
    echo "  - Penugasan Sesi: " . ($o->penugasan->sesi->nama_sesi ?? 'N/A') . " (ID: {$o->penugasan->sesi_id})\n";
}

echo "\n=== STUDENTS IN THE SYSTEM WITH NPM ENDING IN '3' ===\n";
$students = PendaftaranPraktikum::whereHas('praktikan', function($q) {
    $q->where('npm', 'like', '%3');
})->with('praktikan.user', 'sesi', 'penugasanOverride.penugasan')->get();

foreach ($students as $s) {
    echo "Nama: {$s->praktikan->user->name}, NPM: {$s->praktikan->npm}, Sesi: {$s->sesi->nama_sesi}\n";
    if ($s->penugasanOverride) {
        echo "  - Override Penugasan: {$s->penugasanOverride->penugasan->judul} (Kode: {$s->penugasanOverride->penugasan->kode_akhir_npm})\n";
    } else {
        echo "  - No Override\n";
    }
}
