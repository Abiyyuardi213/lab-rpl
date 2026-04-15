<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DigitNpm extends Model
{
    protected $fillable = [
        'digit',
        'label',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('digit');
    }
}
