<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecruitmentPeriod extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'whatsapp_link',
        'min_ipk',
        'min_semester',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'min_ipk' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function applications()
    {
        return $this->hasMany(AslabApplication::class);
    }

    public function schedules()
    {
        return $this->hasMany(RecruitmentSchedule::class);
    }
}
