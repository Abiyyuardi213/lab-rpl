<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TugasAsistensi extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'pendaftaran_id',
        'aslab_id',
        'judul',
        'deskripsi',
        'file_tugas',
        'due_date',
        'status',
        'file_mahasiswa',
        'nilai',
        'catatan_aslab',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'due_date' => 'datetime',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(PendaftaranPraktikum::class, 'pendaftaran_id');
    }

    public function aslab()
    {
        return $this->belongsTo(Aslab::class, 'aslab_id');
    }
}
