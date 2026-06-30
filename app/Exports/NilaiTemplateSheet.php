<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use App\Models\PendaftaranPraktikum;

class NilaiTemplateSheet implements FromCollection, WithStyles, ShouldAutoSize, WithStartRow
{
    protected $praktikum;

    public function __construct($praktikum)
    {
        $this->praktikum = $praktikum;
    }

    public function startRow(): int
    {
        return 3;
    }

    public function collection()
    {
        $pendaftarans = PendaftaranPraktikum::with('praktikan')
            ->where('praktikum_id', $this->praktikum->id)
            ->where('status', 'verified')
            ->get();

        $jumlahModul = $this->praktikum->jumlah_modul;
        $totalCols = 6 + 3 * $jumlahModul + 2;

        $rows = [];
        foreach ($pendaftarans as $p) {
            $row = array_fill(0, $totalCols, '');

            $row[0] = $p->praktikan->npm;

            for ($i = 1; $i <= $jumlahModul; $i++) {
                $row[6 + 3 * ($i - 1)] = 0;
            }

            $row[4 + 3 * $jumlahModul] = 0;
            $row[5 + 3 * $jumlahModul] = 0;

            $rows[] = $row;
        }

        return collect($rows);
    }

    public function styles(Worksheet $sheet): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $jumlahModul = $this->praktikum->jumlah_modul;
                $totalCols = 6 + 3 * $jumlahModul + 2;

                $headers = array_fill(0, $totalCols, '');
                $headers[0] = 'NPM';

                for ($i = 1; $i <= $jumlahModul; $i++) {
                    $colIndex = 6 + 3 * ($i - 1);
                    $headers[$colIndex] = 'Nilai Dosen Modul ' . $i;
                }

                $headers[4 + 3 * $jumlahModul] = 'Laporan';
                $headers[5 + 3 * $jumlahModul] = 'Tugas Akhir';

                foreach ($headers as $colIndex => $value) {
                    if (!empty($value)) {
                        $cell = $sheet->getCellByColumnAndRow($colIndex + 1, 1);
                        $cell->setValue($value);
                    }
                }

                $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnLetter($totalCols);

                $sheet->getStyle("A1:{$lastColLetter}1")->apply([
                    'font' => new Font([
                        'bold' => true,
                        'size' => 10,
                        'color' => ['rgb' => '374151'],
                    ]),
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['rgb' => 'D1FAE5'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                $sheet->getRowDimension(1)->setRowHeight(30);
            },
        ];
    }
}
