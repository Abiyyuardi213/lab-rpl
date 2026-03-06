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
        ];

        // Fetch real activities
        $recentRegistrations = \App\Models\PendaftaranPraktikum::with(['praktikan.user', 'praktikum'])
            ->latest()
            ->take(8)
            ->get()
            ->map(function ($item) {
                return (object)[
                    'type' => 'Registration',
                    'title' => 'Pendaftaran ' . ($item->praktikum->nama_praktikum ?? 'Praktikum'),
                    'user' => $item->praktikan->user->name ?? 'User',
                    'time' => $item->created_at,
                    'badge' => 'New Pendaftar',
                    'badge_color' => 'bg-blue-100 text-blue-700 border-blue-200',
                    'icon' => 'fas fa-id-card',
                    'icon_bg' => 'bg-blue-100 text-blue-600'
                ];
            });

        $recentSubmissions = \App\Models\TugasAsistensi::with(['pendaftaran.praktikan.user'])
            ->where('status', 'submitted')
            ->latest('updated_at')
            ->take(8)
            ->get()
            ->map(function ($item) {
                return (object)[
                    'type' => 'Submission',
                    'title' => 'Tugas "' . $item->judul . '" dikirim',
                    'user' => $item->pendaftaran->praktikan->user->name ?? 'Praktikan',
                    'time' => $item->updated_at,
                    'badge' => 'Submitted',
                    'badge_color' => 'bg-amber-100 text-amber-700 border-amber-200',
                    'icon' => 'fas fa-file-upload',
                    'icon_bg' => 'bg-amber-100 text-amber-600'
                ];
            });

        $recentUsers = User::latest()
            ->take(8)
            ->get()
            ->map(function ($item) {
                return (object)[
                    'type' => 'Account',
                    'title' => 'Akun baru dibuat: ' . $item->name,
                    'user' => 'System',
                    'time' => $item->created_at,
                    'badge' => 'New User',
                    'badge_color' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                    'icon' => 'fas fa-user-plus',
                    'icon_bg' => 'bg-emerald-100 text-emerald-600'
                ];
            });

        $activities = collect($recentRegistrations)
            ->concat($recentSubmissions)
            ->concat($recentUsers)
            ->sortByDesc('time')
            ->take(10);

        return view('admin.dashboard', compact('stats', 'activities'));
    }
}
