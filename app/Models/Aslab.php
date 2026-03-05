<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Aslab extends Model
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

    public function aslabPraktikums()
    {
        return $this->hasMany(AslabPraktikum::class, 'aslab_id');
    }

    public function assignedStudents()
    {
        return $this->hasMany(PendaftaranPraktikum::class, 'aslab_id');
    }
}
