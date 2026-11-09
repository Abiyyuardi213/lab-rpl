<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesertaSusulan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'peserta_susulans';

    protected $fillable = [
        'jadwal_id',
        'pendaftaran_id',
        'status',
        'catatan',
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
}
