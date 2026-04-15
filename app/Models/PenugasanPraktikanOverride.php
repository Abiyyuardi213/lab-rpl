<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenugasanPraktikanOverride extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'pendaftaran_id',
        'penugasan_id',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function pendaftaran()
    {
        return $this->belongsTo(PendaftaranPraktikum::class, 'pendaftaran_id');
    }

    public function penugasan()
    {
        return $this->belongsTo(Penugasan::class, 'penugasan_id');
    }
}
