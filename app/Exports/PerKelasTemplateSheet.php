<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

class PerKelasTemplateSheet implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithEvents
{
    protected $praktikum;
    protected $pendaftarans;

    public function __construct($praktikum, Collection $pendaftarans)
    {
        $this->praktikum = $praktikum;
        $this->pendaftarans = $pendaftarans;
    }

    public function collection()
    {
        $rows = [];
        foreach ($this->pendaftarans as $p) {
            $row = [
                $p->praktikan->npm,
                $p->praktikan->user->name ?? '',
            ];

            for ($i = 1; $i <= $this->praktikum->jumlah_modul; $i++) {
                $row[] = 0;
            }

            $row[] = 0;
            $row[] = 0;
            $row[] = '';

            $rows[] = $row;
        }

        return collect($rows);
    }

    public function headings(): array
    {
        $headings = ['NPM', 'Nama'];

        for ($i = 1; $i <= $this->praktikum->jumlah_modul; $i++) {
            $headings[] = "Nilai Dosen Modul {$i}";
        }

        $headings[] = 'Laporan';
        $headings[] = 'Tugas Akhir';
        $headings[] = 'Alasan Gugur';

        return $headings;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $lastCol = $sheet->getHighestColumn();
                $lastRow = $sheet->getHighestRow();

                $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 10,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => '001F3F'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                $sheet->getRowDimension(1)->setRowHeight(35);

                $sheet->getStyle("A1:{$lastCol}{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D4D4D8'],
                        ],
                    ],
                ]);

                $sheet->getStyle("A2:{$lastCol}{$lastRow}")->applyFromArray([
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle("A2:A{$lastRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 10,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                $sheet->getStyle("B2:B{$lastRow}")->applyFromArray([
                    'font' => [
                        'size' => 10,
                    ],
                ]);

                $scoreColStart = 3;
                $scoreColEnd = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($lastCol);
                for ($col = $scoreColStart; $col <= $scoreColEnd; $col++) {
                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                    $sheet->getStyle("{$colLetter}2:{$colLetter}{$lastRow}")->applyFromArray([
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ],
                    ]);
                }

                $lastColLetter = $lastCol;
                $sheet->getStyle("{$lastColLetter}2:{$lastColLetter}{$lastRow}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'FEE2E2'],
                    ],
                ]);

                $taCol = 3 + $this->praktikum->jumlah_modul;
                $taLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($taCol);
                $sheet->getStyle("{$taLetter}2:{$taLetter}{$lastRow}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'FEF3C7'],
                    ],
                ]);

                $lprnCol = 4 + $this->praktikum->jumlah_modul;
                $lprnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lprnCol);
                $sheet->getStyle("{$lprnLetter}2:{$lprnLetter}{$lastRow}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => 'FEF3C7'],
                    ],
                ]);
            },
        ];
    }
}
