<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AslabActivity extends Model
{
    protected $fillable = ['aslab_id', 'name', 'year'];

    public function aslab()
    {
        return $this->belongsTo(Aslab::class);
    }
}
