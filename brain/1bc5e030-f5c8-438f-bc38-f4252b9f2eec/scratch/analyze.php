<?php

use App\Models\JadwalPraktikum;
use App\Models\Penugasan;
use App\Models\PendaftaranPraktikum;

require __DIR__ . '/../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$id = 11;
$jadwal = JadwalPraktikum::with(['praktikum', 'sesi.jadwalPraktikums', 'penugasans.aslab.user'])->findOrFail($id);

$penugasans = Penugasan::where('jadwal_praktikum_id', $id)
    ->with([
        'praktikum',
        'sesi.pendaftarans.praktikan.user',
        'sesi.pendaftarans.aslab.user',
        'sesi.pendaftarans.penugasanOverride.penugasan',
        'sesi.penugasans',
        'aslab.user',
        'jadwalPraktikum',
    ])
    ->orderBy('created_at', 'desc')
    ->get();

echo "=== TESTING FIX IN ANALYSIS SCRIPT ===\n";

foreach ($penugasans as $index => $p) {
    $registeredStudents = $p->sesi?->pendaftarans ?? collect();
    $filteredStudents = $registeredStudents->filter(function($student) use ($p, $penugasans) {
        $studentNpm = $student->praktikan?->npm ?? '';
        $studentLastDigit = is_numeric(substr($studentNpm, -1)) ? (int) substr($studentNpm, -1) : null;
        
        // FIX applied here: filter by student's sesi_id when matching the default penugasan
        $defaultPenugasan = $studentLastDigit !== null
            ? $penugasans->where('sesi_id', $student->sesi_id)->firstWhere('kode_akhir_npm', $studentLastDigit)
            : null;
        $customPenugasan = $student->penugasanOverride?->penugasan;
        $currentPenugasan = $customPenugasan ?? $defaultPenugasan;
        
        return $currentPenugasan && $currentPenugasan->id === $p->id;
    });
    $verifiedStudents = $filteredStudents->where('status', 'verified');
    
    echo "Penugasan ID: " . substr($p->id, 0, 8) . "..., NPM: {$p->kode_akhir_npm}, Sesi: " . ($p->sesi ? $p->sesi->nama_sesi : 'N/A') . "\n";
    echo "  - Registered in Sesi: " . $registeredStudents->count() . "\n";
    echo "  - Filtered: " . $filteredStudents->count() . "\n";
    echo "  - Verified: " . $verifiedStudents->count() . "\n";
}
