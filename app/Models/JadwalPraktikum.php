<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalPraktikum extends Model
{
    protected $fillable = [
        'praktikum_id',
        'sesi_id',
        'judul_modul',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'ruangan',
        'token',
        'is_susulan',
    ];

    protected $casts = [
        'is_susulan' => 'boolean',
    ];

    public function praktikum()
    {
        return $this->belongsTo(Praktikum::class);
    }

    public function sesi()
    {
        return $this->belongsTo(SesiPraktikum::class, 'sesi_id');
    }

    public function presensis()
    {
        return $this->hasMany(Presensi::class, 'jadwal_id');
    }

    public function penugasans()
    {
        return $this->hasMany(Penugasan::class, 'jadwal_praktikum_id');
    }

    public function pesertaSusulans()
    {
        return $this->hasMany(PesertaSusulan::class, 'jadwal_id');
    }
}
