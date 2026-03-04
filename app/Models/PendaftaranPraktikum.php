<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PendaftaranPraktikum extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'praktikum_id',
        'sesi_id',
        'no_hp',
        'dosen_pengampu',
        'kelas',
        'asal_kelas_mata_kuliah',
        'bukti_krs',
        'bukti_pembayaran',
        'foto_almamater',
        'is_mengulang',
        'status',
        'catatan',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function praktikum()
    {
        return $this->belongsTo(Praktikum::class);
    }

    public function sesi()
    {
        return $this->belongsTo(SesiPraktikum::class, 'sesi_id');
    }
}
