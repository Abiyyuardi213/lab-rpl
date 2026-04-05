<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalPraktikum extends Model
{
    protected $fillable = [
        'praktikum_id',
        'judul_modul',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'ruangan',
        'token'
    ];

    public function praktikum()
    {
        return $this->belongsTo(Praktikum::class);
    }

    public function presensis()
    {
        return $this->hasMany(Presensi::class, 'jadwal_id');
    }
}
