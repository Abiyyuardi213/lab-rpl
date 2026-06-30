<?php

namespace App\Imports;

use App\Models\PendaftaranPraktikum;
use App\Models\PenilaianAkhir;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class GugurSheetImport implements ToCollection
{
    protected $praktikumId;

    public function __construct($praktikumId)
    {
        $this->praktikumId = $praktikumId;
    }

    public function collection(Collection $rows)
    {
        $pendaftarans = PendaftaranPraktikum::with('praktikan')
            ->where('praktikum_id', $this->praktikumId)
            ->where('status', 'verified')
            ->get();

        if ($pendaftarans->isEmpty()) {
            return;
        }

        // Skip the first 2 rows of headers (Row 1: headers, Row 2: empty, Row 3: data)
        $dataRows = $rows->slice(2);

        foreach ($dataRows as $row) {
            $npm = trim((string)($row[0] ?? ''));
            if (empty($npm)) {
                continue;
            }

            // Clean NPM formatting
            $npm = preg_replace('/\s+/', '', $npm);

            // Find matching pendaftaran
            $pendaftaran = $pendaftarans->first(function($item) use ($npm) {
                return preg_replace('/\s+/', '', trim((string)$item->praktikan->npm)) === $npm;
            });

            if (!$pendaftaran) {
                continue;
            }

            $alasan = trim((string)($row[3] ?? ''));

            // Load existing PenilaianAkhir or create new
            $penilaianAkhir = PenilaianAkhir::firstOrNew(['pendaftaran_id' => $pendaftaran->id]);
            
            $nilaiDosen = $penilaianAkhir->nilai_dosen ?? [];
            $nilaiLaporan = $penilaianAkhir->nilai_laporan ?? 0;
            $nilaiTugasAkhir = $penilaianAkhir->nilai_tugas_akhir ?? 0;

            // Mark as gugur
            $calculated = PenilaianAkhir::calculateGrades($pendaftaran, $nilaiDosen, $nilaiLaporan, $nilaiTugasAkhir, true, $alasan);

            $penilaianAkhir->fill($calculated);
            $penilaianAkhir->save();
        }
    }
}
