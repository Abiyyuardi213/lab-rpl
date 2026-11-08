<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'jadwal_id',
        'pendaftaran_id',
        'jam_masuk',
        'status',
        'is_susulan',
    ];

    protected $casts = [
        'is_susulan' => 'boolean',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    public function jadwal()
    {
        return $this->belongsTo(JadwalPraktikum::class, 'jadwal_id');
    }

    public function pendaftaran()
    {
        return $this->belongsTo(PendaftaranPraktikum::class, 'pendaftaran_id');
    }

    public function penilaian()
    {
        return $this->hasOne(PenilaianPraktikum::class, 'presensi_id');
    }
}
