<?php

namespace Database\Seeders;

use App\Models\Dosen;
use Illuminate\Database\Seeder;

class DosenSeeder extends Seeder
{
    public function run(): void
    {
        $dosens = [
            [
                'nama' => 'Dr. Bambang Agus Herlambang, S.Kom., M.Kom.',
                'nip' => '198104122005011002',
                'is_active' => true,
            ],
            [
                'nama' => 'Fajar Rohman Hariri, S.Kom., M.Kom.',
                'nip' => '198507202008121001',
                'is_active' => true,
            ],
            [
                'nama' => 'Triana Harmini, S.Si., M.Pd.',
                'nip' => '197909152003122001',
                'is_active' => true,
            ],
            [
                'nama' => 'Yusuf Amrozi, S.Kom., M.MT.',
                'nip' => '197602042002121003',
                'is_active' => true,
            ],
            [
                'nama' => 'Wahyu Suhariyanto, S.T., M.Kom.',
                'nip' => '198811102012011001',
                'is_active' => true,
            ],
        ];

        foreach ($dosens as $dosen) {
            Dosen::updateOrCreate(
                ['nip' => $dosen['nip']],
                $dosen
            );
        }
    }
}
