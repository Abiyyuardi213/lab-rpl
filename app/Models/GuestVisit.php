<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestVisit extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'visit_date',
        'started_at',
        'ended_at',
        'activity_purpose',
        'guest_name',
        'guest_count',
        'lab_condition',
        'additional_note',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'guest_count' => 'integer',
    ];

    public function getIsCheckedOutAttribute(): bool
    {
        return ! is_null($this->ended_at);
    }
}
