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
        'jabatan',
        'no_hp',
        'jurusan',
        'angkatan',
        'profile_image',
        'slug',
        'bio',
        'skills',
        'instagram_link',
        'github_link',
        'linkedin_link',
    ];

    protected $casts = [
        'skills' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function achievements()
    {
        return $this->hasMany(AslabAchievement::class);
    }

    public function experiences()
    {
        return $this->hasMany(AslabExperience::class);
    }

    public function activities()
    {
        return $this->hasMany(AslabActivity::class);
    }

    public function aslabPraktikums()
    {
        return $this->hasMany(AslabPraktikum::class, 'aslab_id');
    }

    public function praktikums()
    {
        return $this->belongsToMany(Praktikum::class, 'aslab_praktikums', 'aslab_id', 'praktikum_id')
            ->withPivot('id', 'kuota')
            ->withTimestamps();
    }

    public function assignedStudents()
    {
        return $this->hasMany(PendaftaranPraktikum::class, 'aslab_id');
    }
}
