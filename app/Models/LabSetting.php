<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LabSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kepala_lab',
        'nip_kepala_lab',
        'ttd_kepala_lab',
        'nama_kaprodi',
        'nip_kaprodi',
        'ttd_kaprodi',
        'nomor_surat_prefix',
        'bg_sertifikat_template',
    ];

    public static function getSetting()
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'nama_kepala_lab' => 'Nama Kepala Lab',
                'nip_kepala_lab' => '-',
                'nama_kaprodi' => 'Nama Kaprodi',
                'nip_kaprodi' => '-',
                'nomor_surat_prefix' => 'LAB-RPL/SERT',
            ]
        );
    }
}
