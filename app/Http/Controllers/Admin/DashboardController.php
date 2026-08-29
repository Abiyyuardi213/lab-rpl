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
            'active_praktikums' => \App\Models\Praktikum::whereIn('status_praktikum', ['open_registration', 'on_progress'])->count(),
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

        $recentPresensis = \App\Models\Presensi::with(['pendaftaran.praktikan.user', 'jadwal'])
            ->latest()
            ->take(8)
            ->get()
            ->map(function ($item) {
                return (object)[
                    'type' => 'Attendance',
                    'title' => 'Presensi: ' . ($item->jadwal->judul_modul ?? 'Modul'),
                    'user' => $item->pendaftaran->praktikan->user->name ?? 'Praktikan',
                    'time' => $item->created_at,
                    'badge' => ucfirst($item->status),
                    'badge_color' => $item->status === 'hadir' ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-amber-100 text-amber-700 border-amber-200',
                    'icon' => 'fas fa-clipboard-check',
                    'icon_bg' => 'bg-indigo-100 text-indigo-600'
                ];
            });

        $activities = collect($recentRegistrations)
            ->concat($recentSubmissions)
            ->concat($recentUsers)
            ->concat($recentPresensis)
            ->sortByDesc('time')
            ->take(10);

        return view('admin.dashboard', compact('stats', 'activities'));
    }

    public function dashboard2(Request $request)
    {
        $selectedYear = $request->get('year', date('Y'));

        // Available years from pendaftaran
        $years = \App\Models\PendaftaranPraktikum::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (!in_array(date('Y'), $years)) {
            array_unshift($years, (int)date('Y'));
        }

        // Cumulative monthly registration counts for selected year (Up to current month)
        $monthlyCounts = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        $runningTotal = 0;
        $currentMonth = (int)date('n');
        $isCurrentYear = ($selectedYear == date('Y'));

        for ($m = 1; $m <= 12; $m++) {
            if ($isCurrentYear && $m > $currentMonth) {
                // Bulan di masa depan yang belum dilalui diisi null agar titik & garis grafik terhenti sampai bulan ini
                $monthlyCounts[] = null;
            } else {
                $monthCount = \App\Models\PendaftaranPraktikum::whereYear('created_at', $selectedYear)
                    ->whereMonth('created_at', $m)
                    ->count();
                $runningTotal += $monthCount;
                $monthlyCounts[] = $runningTotal;
            }
        }

        // Stats summary
        $totalRegistered = \App\Models\PendaftaranPraktikum::whereYear('created_at', $selectedYear)->count();
        $verifiedCount = \App\Models\PendaftaranPraktikum::whereYear('created_at', $selectedYear)->where('status', 'verified')->count();
        $pendingCount = \App\Models\PendaftaranPraktikum::whereYear('created_at', $selectedYear)->where('status', 'pending')->count();
        $rejectedCount = \App\Models\PendaftaranPraktikum::whereYear('created_at', $selectedYear)->where('status', 'rejected')->count();

        // Recent live tickers
        $liveActivities = \App\Models\PendaftaranPraktikum::with(['praktikan.user', 'praktikum'])
            ->latest()
            ->take(10)
            ->get();

        // Marquee Ticker Stats per Praktikum
        $praktikumStats = \App\Models\Praktikum::withCount(['pendaftarans' => function ($q) use ($selectedYear) {
            $q->whereYear('created_at', $selectedYear);
        }])->get()->map(function ($p) use ($totalRegistered) {
            $percentage = $totalRegistered > 0 ? round(($p->pendaftarans_count / $totalRegistered) * 100, 1) : 0;
            return (object)[
                'nama' => $p->nama_praktikum,
                'count' => $p->pendaftarans_count,
                'percentage' => $percentage
            ];
        });

        return view('admin.dashboard2', compact(
            'selectedYear',
            'years',
            'months',
            'monthlyCounts',
            'totalRegistered',
            'verifiedCount',
            'pendingCount',
            'rejectedCount',
            'liveActivities',
            'praktikumStats'
        ));
    }
}
