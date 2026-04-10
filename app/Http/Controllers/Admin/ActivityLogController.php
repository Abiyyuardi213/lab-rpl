<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user.role')->latest();

        // Filtering by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filtering by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('activity', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('admin.activity-log.index', compact('logs'));
    }

    public function show($id)
    {
        $log = ActivityLog::with('user.role')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'log' => [
                'id' => $log->id,
                'user_name' => $log->user ? $log->user->name : 'System/Guest',
                'role' => $log->role,
                'activity' => $log->activity,
                'description' => $log->description,
                'data' => $log->data,
                'ip_address' => $log->ip_address,
                'user_agent' => $log->user_agent,
                'created_at' => $log->created_at->format('d M Y H:i:s')
            ]
        ]);
    }
}
