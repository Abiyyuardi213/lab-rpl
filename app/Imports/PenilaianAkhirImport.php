<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PenilaianAkhirImport implements WithMultipleSheets
{
    protected $praktikumId;
    protected $file;

    public function __construct($praktikumId, $file = null)
    {
        $this->praktikumId = $praktikumId;
        $this->file = $file;
    }

    public function sheets(): array
    {
        if ($this->file && file_exists($this->file->getRealPath())) {
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($this->file->getRealPath());
                $sheetNames = $spreadsheet->getSheetNames();
                
                $sheets = [];
                foreach ($sheetNames as $name) {
                    if ($name === 'GUGUR') {
                        $sheets[$name] = new GugurSheetImport($this->praktikumId);
                    } else {
                        $sheets[$name] = new NilaiSheetImport($this->praktikumId);
                    }
                }
                return $sheets;
            } catch (\Exception $e) {
                // Fallback if parsing fails
            }
        }

        return [
            'NILAI' => new NilaiSheetImport($this->praktikumId),
            'GUGUR' => new GugurSheetImport($this->praktikumId),
        ];
    }
}
