<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'profile_picture',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function aslab()
    {
        return $this->hasOne(Aslab::class, 'user_id');
    }

    public function praktikan()
    {
        return $this->hasOne(Praktikan::class, 'user_id');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
        ];
    }

    public function pendaftarans()
    {
        return $this->hasManyThrough(PendaftaranPraktikum::class, Praktikan::class, 'user_id', 'praktikan_id', 'id', 'id');
    }

    public function assignedStudents()
    {
        return $this->hasManyThrough(PendaftaranPraktikum::class, Aslab::class, 'user_id', 'aslab_id', 'id', 'id');
    }

    public function aslabPraktikums()
    {
        return $this->aslab ? $this->aslab->aslabPraktikums() : collect();
    }

    public function getNpmAttribute()
    {
        return $this->praktikan?->npm ?? $this->aslab?->npm;
    }

    public function getJurusanAttribute()
    {
        return $this->praktikan?->jurusan ?? $this->aslab?->jurusan;
    }

    public function getAngkatanAttribute()
    {
        return $this->praktikan?->angkatan ?? $this->aslab?->angkatan;
    }

    public function getNoHpAttribute()
    {
        return $this->praktikan?->no_hp ?? $this->aslab?->no_hp;
    }
}
