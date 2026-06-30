<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PenilaianAkhirExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $praktikum;
    protected $grades;

    public function __construct($praktikum, array $grades)
    {
        $this->praktikum = $praktikum;
        $this->grades = $grades;
    }

    public function collection()
    {
        return collect($this->grades);
    }

    public function headings(): array
    {
        $headings = ['NPM', 'Nama'];

        for ($i = 1; $i <= $this->praktikum->jumlah_modul; $i++) {
            $headings[] = "M{$i} Prak";
            $headings[] = "M{$i} Ast";
            $headings[] = "M{$i} Dos";
        }

        $headings[] = 'Lprn';

        if ($this->praktikum->ada_tugas_akhir) {
            $headings[] = 'Tugas Akhir';
        }

        $headings[] = 'Tot Prak';
        $headings[] = 'Tot Ast';
        $headings[] = 'Tot Prak+Ast';
        $headings[] = 'Tot Dos';
        $headings[] = 'Nilai Akhir';
        $headings[] = 'Huruf';
        $headings[] = 'Status';

        return $headings;
    }

    public function map($gradeData): array
    {
        $pendaftaran = $gradeData['pendaftaran'];
        $g = $gradeData['grades'];

        $row = [
            $pendaftaran->praktikan->npm,
            $pendaftaran->praktikan->user->name,
        ];

        $schedulesList = $pendaftaran->praktikum->jadwals()
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->get();

        for ($i = 1; $i <= $this->praktikum->jumlah_modul; $i++) {
            $sched = $schedulesList->get($i - 1);

            $prakScore = 0;
            if ($sched) {
                $pres = $pendaftaran->presensis->firstWhere('jadwal_id', $sched->id);
                $prakScore = ($pres && $pres->penilaian) ? $pres->penilaian->nilai : 0;
            }

            $astScore = 0;
            if ($sched) {
                $tugas = $pendaftaran->tugasAsistensis->firstWhere('judul', $sched->judul_modul);
                $astScore = $tugas ? ($tugas->nilai ?? 0) : 0;
            }

            $dosScore = $g['nilai_dosen'][$i] ?? 0;

            $row[] = $prakScore;
            $row[] = $astScore;
            $row[] = $dosScore;
        }

        $row[] = $g['nilai_laporan'] ?? 0;

        if ($this->praktikum->ada_tugas_akhir) {
            $row[] = $g['nilai_tugas_akhir'] ?? 0;
        }

        $row[] = number_format($g['total_praktikum'], 2);
        $row[] = number_format($g['total_asistensi'], 2);
        $row[] = number_format($g['total_praktikum_asistensi'], 2);
        $row[] = number_format($g['total_dosen'], 2);
        $row[] = number_format($g['nilai_akhir'], 2);
        $row[] = $g['nilai_huruf'];

        $isGugur = $g['is_gugur'] ?? false;
        if ($isGugur) {
            $row[] = 'GUGUR';
        } else {
            $row[] = $g['status_kelulusan'];
        }

        return $row;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => ['fillType' => 'solid', 'color' => ['rgb' => 'F4F4F5']],
                'borders' => [
                    'allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'D4D4D8']],
                ],
            ],
        ];
    }
}
