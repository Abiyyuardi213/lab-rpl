<?php

namespace App\Imports;

use App\Models\PendaftaranPraktikum;
use App\Models\PenilaianAkhir;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class NilaiSheetImport implements ToCollection
{
    protected $praktikumId;

    public function __construct($praktikumId)
    {
        $this->praktikumId = $praktikumId;
    }

    public function collection(Collection $rows)
    {
        $pendaftarans = PendaftaranPraktikum::with(['praktikan', 'praktikum'])
            ->where('praktikum_id', $this->praktikumId)
            ->where('status', 'verified')
            ->get();

        if ($pendaftarans->isEmpty()) {
            return;
        }

        $praktikum = $pendaftarans->first()->praktikum;
        $jumlahModul = $praktikum->jumlah_modul;

        // Skip the first 2 rows of headers
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

            // Extract lecturer scores
            $nilaiDosen = [];
            for ($i = 1; $i <= $jumlahModul; $i++) {
                $colIndex = 2 + ($i - 1);
                $score = $row[$colIndex] ?? 0;
                $nilaiDosen[$i] = is_numeric($score) ? intval($score) : 0;
            }

            $existingRecord = $pendaftaran->penilaianAkhir;
            $nilaiLaporan = $existingRecord?->nilai_laporan ?? 0;
            $nilaiTugasAkhir = $existingRecord?->nilai_tugas_akhir ?? 0;
            $isGugur = $existingRecord?->is_gugur ?? false;
            $alasanGugur = $existingRecord?->alasan_gugur ?? null;

            // Calculate final grade with updated lecturer scores
            $calculated = PenilaianAkhir::calculateGrades($pendaftaran, $nilaiDosen, $nilaiLaporan, $nilaiTugasAkhir, $isGugur, $alasanGugur);

            // Save or update PenilaianAkhir
            PenilaianAkhir::updateOrCreate(
                ['pendaftaran_id' => $pendaftaran->id],
                $calculated
            );
        }
    }
}
