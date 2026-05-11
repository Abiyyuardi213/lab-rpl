<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AslabApplication extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'recruitment_period_id',
        'user_id',
        'cv_path',
        'khs_path',
        'transcript_path',
        'portfolio_url',
        'motivation_letter',
        'ipk',
        'status',
        'admin_notes',
    ];

    public function period()
    {
        return $this->belongsTo(RecruitmentPeriod::class, 'recruitment_period_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedules()
    {
        return $this->belongsToMany(RecruitmentSchedule::class, 'aslab_application_schedule');
    }
}
