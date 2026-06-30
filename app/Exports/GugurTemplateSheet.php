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

class GugurTemplateSheet implements FromCollection, WithStyles, ShouldAutoSize, WithStartRow
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

        $rows = [];
        foreach ($pendaftarans as $p) {
            $rows[] = [
                $p->praktikan->npm,
                '',
                '',
                '',
            ];
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

                $headers = [
                    1 => 'NPM',
                    4 => 'Alasan Gugur',
                ];

                foreach ($headers as $colIndex => $value) {
                    $cell = $sheet->getCellByColumnAndRow($colIndex, 1);
                    $cell->setValue($value);
                }

                $sheet->getStyle('A1:D1')->apply([
                    'font' => new Font([
                        'bold' => true,
                        'size' => 10,
                        'color' => ['rgb' => '374151'],
                    ]),
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['rgb' => 'FEE2E2'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);

                $sheet->getColumnDimension('D')->setWidth(40);
                $sheet->getRowDimension(1)->setRowHeight(30);
            },
        ];
    }
}
