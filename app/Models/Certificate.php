<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Certificate extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'praktikum_id',
        'nomor_surat_prefix',
        'bg_template',
        'nama_kepala_lab',
        'nip_kepala_lab',
        'ttd_kepala_lab',
        'nama_kaprodi',
        'nip_kaprodi',
        'ttd_kaprodi',
        'tanggal_sertifikat',
        'catatan',
    ];

    protected $casts = [
        'tanggal_sertifikat' => 'date',
    ];

    public function praktikum()
    {
        return $this->belongsTo(Praktikum::class, 'praktikum_id');
    }
}
