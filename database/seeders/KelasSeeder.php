<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $kelasList = [
            [
                'nama_kelas' => 'P',
                'is_active' => true,
            ],
            [
                'nama_kelas' => 'P1',
                'is_active' => true,
            ],
            [
                'nama_kelas' => 'V',
                'is_active' => true,
            ],
        ];

        foreach ($kelasList as $kelas) {
            Kelas::updateOrCreate(
                ['nama_kelas' => $kelas['nama_kelas']],
                $kelas
            );
        }
    }
}
