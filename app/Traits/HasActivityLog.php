<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait HasActivityLog
{
    /**
     * Log an activity.
     *
     * @param string $activity
     * @param string|null $description
     * @param array|null $data
     * @return void
     */
    public function logActivity($activity, $description = null, $data = null)
    {
        $user = Auth::user();

        ActivityLog::create([
            'user_id' => $user ? $user->id : null,
            'activity' => $activity,
            'description' => $description,
            'data' => $data,
            'role' => $user ? $user->role->name : 'Guest',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
