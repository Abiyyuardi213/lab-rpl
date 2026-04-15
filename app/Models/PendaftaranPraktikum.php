<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PendaftaranPraktikum extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'praktikan_id',
        'praktikum_id',
        'sesi_id',
        'aslab_id',
        'no_hp',
        'dosen_pengampu',
        'kelas',
        'asal_kelas_mata_kuliah',
        'bukti_krs',
        'bukti_pembayaran',
        'foto_almamater',
        'is_mengulang',
        'is_google_form',
        'status',
        'catatan',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function praktikan()
    {
        return $this->belongsTo(Praktikan::class, 'praktikan_id');
    }

    public function aslab()
    {
        return $this->belongsTo(Aslab::class, 'aslab_id');
    }

    public function praktikum()
    {
        return $this->belongsTo(Praktikum::class);
    }

    public function sesi()
    {
        return $this->belongsTo(SesiPraktikum::class, 'sesi_id');
    }

    public function tugasAsistensis()
    {
        return $this->hasMany(TugasAsistensi::class, 'pendaftaran_id');
    }

    public function presensis()
    {
        return $this->hasMany(Presensi::class, 'pendaftaran_id');
    }

    public function penugasanOverride()
    {
        return $this->hasOne(PenugasanPraktikanOverride::class, 'pendaftaran_id');
    }
}
