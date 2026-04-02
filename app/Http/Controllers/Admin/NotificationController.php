<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Notifications\BroadcastNotification;
use Illuminate\Support\Facades\Notification;

class NotificationController extends Controller
{
    public function create()
    {
        return view('admin.notifications.create');
    }

    public function send(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target' => 'required|in:all,praktikan,aslab',
        ]);

        $query = User::query();

        if ($request->target === 'praktikan') {
            $query->whereHas('role', function ($q) {
                $q->where('name', 'Praktikan');
            });
        } elseif ($request->target === 'aslab') {
            $query->whereHas('role', function ($q) {
                $q->where('name', 'Aslab');
            });
        } else {
            // all practical targets (praktikan and aslab)
            $query->whereHas('role', function ($q) {
                $q->whereIn('name', ['Praktikan', 'Aslab']);
            });
        }

        $users = $query->get();

        if ($users->isEmpty()) {
            return back()->with('error', 'Tidak ada penerima yang ditemukan untuk target ini.');
        }

        Notification::send($users, new BroadcastNotification($request->title, $request->message));

        return back()->with('success', 'Notifikasi berhasil dikirim ke ' . $users->count() . ' pengguna.');
    }
}
