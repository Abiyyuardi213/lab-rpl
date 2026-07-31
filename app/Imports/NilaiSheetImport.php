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
        $pendaftarans = PendaftaranPraktikum::with(['praktikan', 'praktikum', 'penilaianAkhir'])
            ->where('praktikum_id', $this->praktikumId)
            ->where('status', 'verified')
            ->get();

        if ($pendaftarans->isEmpty() || $rows->isEmpty()) {
            return;
        }

        $praktikum = $pendaftarans->first()->praktikum;
        $jumlahModul = $praktikum->jumlah_modul;

        // 1. Find Header Row & NPM Column
        $npmColIdx = 0;
        $headerRowIdx = null;

        foreach ($rows as $rIdx => $row) {
            foreach ($row as $cIdx => $cell) {
                $val = strtoupper(trim((string)$cell));
                if ($val === 'NPM' || str_contains($val, 'NPM') || $val === 'NIM') {
                    $npmColIdx = $cIdx;
                    $headerRowIdx = $rIdx;
                    break 2;
                }
            }
        }

        // Fallback: If no explicit NPM header string found, find first row where column 0 or 1 looks like NPM
        if ($headerRowIdx === null) {
            foreach ($rows as $rIdx => $row) {
                $c0 = preg_replace('/[^\d]/', '', (string)($row[0] ?? ''));
                $c1 = preg_replace('/[^\d]/', '', (string)($row[1] ?? ''));
                if (strlen($c0) >= 7) {
                    $npmColIdx = 0;
                    $headerRowIdx = max(0, $rIdx - 1);
                    break;
                } elseif (strlen($c1) >= 7) {
                    $npmColIdx = 1;
                    $headerRowIdx = max(0, $rIdx - 1);
                    break;
                }
            }
        }

        if ($headerRowIdx === null) {
            $headerRowIdx = 0;
        }

        // 2. Map Module Dosen Columns Dynamically
        $dosenColMap = [];

        for ($r = 0; $r <= min($headerRowIdx + 1, $rows->count() - 1); $r++) {
            $hRow = $rows[$r] ?? [];
            foreach ($hRow as $cIdx => $cellVal) {
                $cellStr = strtolower(trim((string)$cellVal));
                if ($cellStr === '') continue;

                for ($i = 1; $i <= $jumlahModul; $i++) {
                    if (
                        preg_match('/^m' . $i . '[\s\-\_\(\)]*(dos|dosen)?$/i', $cellStr) ||
                        preg_match('/(dosen|dos).*modul[\s\-_]*' . $i . '/i', $cellStr) ||
                        preg_match('/modul[\s\-_]*' . $i . '.*(dosen|dos)/i', $cellStr) ||
                        $cellStr === "nilai dosen modul {$i}" ||
                        $cellStr === "m{$i} dos" ||
                        $cellStr === "m{$i}dos" ||
                        $cellStr === "m{$i}" ||
                        $cellStr === "modul {$i}" ||
                        $cellStr === "modul{$i}"
                    ) {
                        if (!isset($dosenColMap[$i]) || str_contains($cellStr, 'dos') || str_contains($cellStr, 'dosen')) {
                            $dosenColMap[$i] = $cIdx;
                        }
                    }
                }
            }
        }

        // Fallback column map for any module not explicitly matched by header name
        for ($i = 1; $i <= $jumlahModul; $i++) {
            if (!isset($dosenColMap[$i])) {
                $matrixDosCol = ($npmColIdx + 2) + 2 + ($i - 1) * 3;
                $templateDosCol = ($npmColIdx + 2) + ($i - 1);

                $dataSample = $rows->slice($headerRowIdx + 1, 10);
                $hasMatrixData = $dataSample->pluck($matrixDosCol)->filter(fn($v) => is_numeric($v) && intval($v) > 0)->count() > 0;

                if ($hasMatrixData) {
                    $dosenColMap[$i] = $matrixDosCol;
                } else {
                    $dosenColMap[$i] = $templateDosCol;
                }
            }
        }

        // 3. Process Data Rows
        $dataRows = $rows->slice($headerRowIdx + 1);

        foreach ($dataRows as $row) {
            $rawNpm = trim((string)($row[$npmColIdx] ?? ''));
            if (empty($rawNpm)) {
                continue;
            }

            // Clean NPM formatting
            $cleanNpm = preg_replace('/[^\d]/', '', $rawNpm);
            if (empty($cleanNpm)) {
                continue;
            }

            // Find matching pendaftaran
            $pendaftaran = $pendaftarans->first(function($item) use ($cleanNpm) {
                $pNpm = preg_replace('/[^\d]/', '', trim((string)$item->praktikan->npm));
                return $pNpm === $cleanNpm;
            });

            if (!$pendaftaran) {
                continue;
            }

            $existingRecord = $pendaftaran->penilaianAkhir;
            $existingDosen = $existingRecord?->nilai_dosen ?? [];

            // Extract lecturer scores
            $nilaiDosen = [];
            for ($i = 1; $i <= $jumlahModul; $i++) {
                $colIdx = $dosenColMap[$i] ?? (($npmColIdx + 2) + ($i - 1));
                $rawVal = $row[$colIdx] ?? null;

                if ($rawVal !== null && trim((string)$rawVal) !== '') {
                    $strVal = str_replace(',', '.', trim((string)$rawVal));
                    if (is_numeric($strVal)) {
                        $nilaiDosen[$i] = (int) round((float) $strVal);
                    } else {
                        $nilaiDosen[$i] = $existingDosen[$i] ?? $existingDosen[(string)$i] ?? 0;
                    }
                } else {
                    $nilaiDosen[$i] = $existingDosen[$i] ?? $existingDosen[(string)$i] ?? 0;
                }
            }

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
