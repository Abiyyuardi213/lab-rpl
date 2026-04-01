<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AslabPraktikum extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'aslab_id',
        'praktikum_id',
        'kuota',
        'link_grup',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function aslab()
    {
        return $this->belongsTo(Aslab::class, 'aslab_id');
    }

    public function praktikum()
    {
        return $this->belongsTo(Praktikum::class, 'praktikum_id');
    }
}
