<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PerKelasTemplateSheet implements FromCollection, ShouldAutoSize, WithCustomStartCell, WithEvents
{
    protected $praktikum;
    protected $pendaftarans;
    protected $kelas;
    protected $dosenList;

    public function __construct($praktikum, Collection $pendaftarans, $kelas, array $dosenList)
    {
        $this->praktikum = $praktikum;
        $this->pendaftarans = $pendaftarans;
        $this->kelas = $kelas;
        $this->dosenList = $dosenList;
    }

    public function startCell(): string
    {
        return 'A3';
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

        return $headings;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $lastCol = $sheet->getHighestColumn();
                $lastRow = $sheet->getHighestRow();

                // Write headings to row 2
                $sheet->getDelegate()->fromArray($this->headings(), null, 'A2');

                $dosenText = !empty($this->dosenList) ? implode(', ', $this->dosenList) : '-';
                $infoText = $this->praktikum->nama_praktikum . ' (' . $this->praktikum->kode_praktikum . ')';
                $infoText .= ' | Kelas: ' . ($this->kelas ?: '-');
                $infoText .= ' | Dosen Pengampu: ' . $dosenText;

                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->getCell('A1')->setValue($infoText);

                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => '001F3F'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(30);

                $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 10,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => '1E40AF'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                $sheet->getRowDimension(2)->setRowHeight(35);

                $sheet->getStyle("A1:{$lastCol}{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D4D4D8'],
                        ],
                    ],
                ]);

                $sheet->getStyle("A3:{$lastCol}{$lastRow}")->applyFromArray([
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle("A3:A{$lastRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->getStyle("B3:B{$lastRow}")->applyFromArray([
                    'font' => ['size' => 10],
                ]);

                $scoreColStart = 3;
                $scoreColEnd = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($lastCol);
                for ($col = $scoreColStart; $col <= $scoreColEnd; $col++) {
                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                    $sheet->getStyle("{$colLetter}3:{$colLetter}{$lastRow}")->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                }
            },
        ];
    }
}
