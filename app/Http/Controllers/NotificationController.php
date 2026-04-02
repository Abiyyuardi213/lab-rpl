<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(15);
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return back();
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back();
    }

    public function fetch()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['count' => 0, 'notifications' => []]);
        }

        $count = $user->unreadNotifications->count();
        $notifications = $user->unreadNotifications->take(5)->map(function ($notif) {
            return [
                'id' => $notif->id,
                'title' => $notif->data['title'] ?? 'Info',
                'message' => $notif->data['message'] ?? '',
                'time' => $notif->created_at->diffForHumans()
            ];
        });

        // Get the latest unread notification ID to track changes
        $latestId = $user->unreadNotifications->first() ? $user->unreadNotifications->first()->id : null;

        return response()->json([
            'count' => $count,
            'notifications' => $notifications,
            'latest_id' => $latestId
        ]);
    }
}
