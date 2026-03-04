<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'roles' => Role::count(),
            'active_users' => User::where('status', true)->count(),
            'praktikums' => \App\Models\Praktikum::count(),
            'active_praktikums' => \App\Models\Praktikum::where('status_praktikum', '!=', 'berakhir')->count(),
            'praktikan' => User::whereHas('role', function ($q) {
                $q->where('name', 'Praktikan');
            })->count(),
            // Mocking other stats
            'announcements' => 0,
            'publishedAnnouncements' => 0,
            'workPrograms' => 0,
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
