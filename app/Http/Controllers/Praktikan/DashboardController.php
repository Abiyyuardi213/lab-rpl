<?php

namespace App\Http\Controllers\Praktikan;

use App\Http\Controllers\Controller;
use App\Models\Praktikum;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $praktikan = $user->praktikan;

        // Praktikums availble for registration (not finished)
        $praktikums = Praktikum::withCount('pendaftarans')
            ->with(['pendaftarans' => function ($query) use ($praktikan) {
                if ($praktikan) {
                    $query->where('praktikan_id', $praktikan->id);
                } else {
                    $query->whereRaw('1 = 0'); // Empty result if no praktikan info
                }
            }])
            ->where('status_praktikum', '!=', 'finished')
            ->orderBy('created_at', 'desc')
            ->get();

        // Upcoming schedules for the praktikan
        $upcomingSchedules = collect();
        if ($praktikan) {
            $pendaftaranIds = $praktikan->pendaftarans()
                ->where('status', 'verified')
                ->pluck('praktikum_id');

            $upcomingSchedules = \App\Models\JadwalPraktikum::with('praktikum')
                ->whereIn('praktikum_id', $pendaftaranIds)
                ->where('tanggal', '>=', now()->toDateString())
                ->orderBy('tanggal', 'asc')
                ->orderBy('waktu_mulai', 'asc')
                ->get();
        }

        return view('praktikan.dashboard', compact('praktikums', 'upcomingSchedules'));
    }
}
