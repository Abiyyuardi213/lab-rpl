<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Penugasan extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'praktikum_id',
        'sesi_id',
        'jadwal_praktikum_id',
        'aslab_id',
        'kode_akhir_npm',
        'judul',
        'deskripsi',
        'file_soal',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function praktikum()
    {
        return $this->belongsTo(Praktikum::class);
    }

    public function sesi()
    {
        return $this->belongsTo(SesiPraktikum::class, 'sesi_id');
    }

    public function jadwalPraktikum()
    {
        return $this->belongsTo(JadwalPraktikum::class);
    }

    public function aslab()
    {
        return $this->belongsTo(Aslab::class);
    }
}
