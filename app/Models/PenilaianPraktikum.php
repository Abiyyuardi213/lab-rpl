<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PenilaianPraktikum extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'presensi_id',
        'aslab_id',
        'nilai',
        'catatan',
    ];

    public function presensi()
    {
        return $this->belongsTo(Presensi::class);
    }

    public function aslab()
    {
        return $this->belongsTo(Aslab::class);
    }
}
