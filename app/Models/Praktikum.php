<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Praktikum extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'kode_praktikum',
        'nama_praktikum',
        'periode_praktikum',
        'kuota_praktikan',
        'status_praktikum',
    ];

    public function sesis()
    {
        return $this->hasMany(SesiPraktikum::class, 'praktikum_id');
    }

    public function pendaftarans()
    {
        return $this->hasMany(PendaftaranPraktikum::class, 'praktikum_id');
    }
}
