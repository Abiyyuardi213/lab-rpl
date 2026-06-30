<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PenilaianAkhirImport implements WithMultipleSheets
{
    protected $praktikumId;

    public function __construct($praktikumId)
    {
        $this->praktikumId = $praktikumId;
    }

    public function sheets(): array
    {
        return [
            'NILAI' => new NilaiSheetImport($this->praktikumId),
            'GUGUR' => new GugurSheetImport($this->praktikumId),
        ];
    }
}
