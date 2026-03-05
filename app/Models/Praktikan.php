<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Praktikan extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'npm',
        'no_hp',
        'jurusan',
        'angkatan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pendaftarans()
    {
        return $this->hasMany(PendaftaranPraktikum::class, 'praktikan_id');
    }
}
