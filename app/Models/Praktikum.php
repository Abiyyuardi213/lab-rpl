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
        'daftar_dosen',
        'daftar_kelas_mk',
        'periode_praktikum',
        'kuota_praktikan',
        'status_praktikum',
        'jumlah_modul',
        'ada_tugas_akhir',
    ];

    protected $casts = [
        'daftar_dosen' => 'array',
        'daftar_kelas_mk' => 'array',
        'ada_tugas_akhir' => 'boolean',
    ];

    public function sesis()
    {
        return $this->hasMany(SesiPraktikum::class, 'praktikum_id');
    }

    public function pendaftarans()
    {
        return $this->hasMany(PendaftaranPraktikum::class, 'praktikum_id');
    }

    public function aslabs()
    {
        return $this->belongsToMany(Aslab::class, 'aslab_praktikums', 'praktikum_id', 'aslab_id')
            ->withPivot('id', 'kuota')
            ->withTimestamps();
    }

    public function jadwals()
    {
        return $this->hasMany(JadwalPraktikum::class, 'praktikum_id');
    }
}
