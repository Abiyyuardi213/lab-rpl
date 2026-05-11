<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecruitmentSchedule extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'recruitment_period_id',
        'name',
        'date',
        'start_time',
        'end_time',
        'location',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function period()
    {
        return $this->belongsTo(RecruitmentPeriod::class, 'recruitment_period_id');
    }

    public function applications()
    {
        return $this->belongsToMany(AslabApplication::class, 'aslab_application_schedule');
    }
}
