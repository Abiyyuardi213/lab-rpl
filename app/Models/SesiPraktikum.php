<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SesiPraktikum extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'praktikum_id',
        'nama_sesi',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'kuota',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function praktikum()
    {
        return $this->belongsTo(Praktikum::class);
    }

    public function pendaftarans()
    {
        return $this->hasMany(PendaftaranPraktikum::class, 'sesi_id');
    }

    public function penugasans()
    {
        return $this->hasMany(Penugasan::class, 'sesi_id');
    }
}
