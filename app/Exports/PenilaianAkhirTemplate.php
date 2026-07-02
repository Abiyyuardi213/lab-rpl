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
        $pendaftarans = PendaftaranPraktikum::with('praktikan')
            ->where('praktikum_id', $this->praktikum->id)
            ->where('status', 'verified')
            ->get()
            ->groupBy('asal_kelas_mata_kuliah')
            ->sortKeys();

        $sheets = [];
        foreach ($pendaftarans as $kelas => $group) {
            $dosenList = $group->pluck('dosen_pengampu')->filter()->unique()->values()->toArray();

            $sheetName = 'Kelas ' . $kelas;
            if (strlen($sheetName) > 31) {
                $sheetName = substr($sheetName, 0, 31);
            }
            $sheets[$sheetName] = new PerKelasTemplateSheet($this->praktikum, $group, $kelas, $dosenList);
        }

        if (empty($sheets)) {
            $sheets['Data'] = new PerKelasTemplateSheet($this->praktikum, collect([]), '', []);
        }

        return $sheets;
    }
}
