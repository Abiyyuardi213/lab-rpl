<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AslabAchievement extends Model
{
    protected $fillable = ['aslab_id', 'name', 'year', 'start_year', 'end_year'];

    public function aslab()
    {
        return $this->belongsTo(Aslab::class);
    }
}
