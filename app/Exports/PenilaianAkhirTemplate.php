<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\Praktikum;
use App\Models\PendaftaranPraktikum;

class PenilaianAkhirTemplate implements WithMultipleSheets
{
    protected $praktikum;

    public function __construct(Praktikum $praktikum)
    {
        $this->praktikum = $praktikum;
    }

    public function sheets(): array
    {
        return [
            'NILAI' => new NilaiTemplateSheet($this->praktikum),
            'GUGUR' => new GugurTemplateSheet($this->praktikum),
        ];
    }
}
