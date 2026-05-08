<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Rating extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'pendaftaran_id',
        'rating_praktikum',
        'ulasan_praktikum',
        'rating_asisten',
        'ulasan_asisten',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(PendaftaranPraktikum::class, 'pendaftaran_id');
    }
}
