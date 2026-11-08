<?php

namespace App\Http\Controllers\Praktikan;

use App\Http\Controllers\Controller;
use App\Models\Praktikum;
use App\Services\PresensiAlfaService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $praktikan = $user?->praktikan;

        // Praktikums availble for registration (not finished)
        $praktikums = Praktikum::withCount('pendaftarans')
            ->with(['pendaftarans' => function ($query) use ($praktikan) {
                if ($praktikan) {
                    $query->where('praktikan_id', $praktikan->id)
                        ->whereIn('status', ['pending', 'verified']);
                } else {
                    $query->whereRaw('1 = 0'); // Empty result if no praktikan info
                }
            }])
            ->where('status_praktikum', '!=', 'finished')
            ->orderBy('created_at', 'desc')
            ->get();

        // Upcoming schedules for the praktikan
        $upcomingSchedules = collect();
        $activePendaftarans = collect();
        $completedPendaftarans = collect();
        $gugurPendaftarans = collect();
        $susulanSchedules = collect();

        if ($praktikan) {
            app(PresensiAlfaService::class)->markFinishedSchedules();

            $gugurPendaftarans = $praktikan->pendaftarans()
                ->where('status', 'gugur')
                ->with('praktikum')
                ->get();

            $susulanSchedules = \App\Models\PesertaSusulan::with(['jadwal.praktikum'])
                ->whereHas('pendaftaran', function ($q) use ($praktikan) {
                    $q->where('praktikan_id', $praktikan->id);
                })
                ->where('status', 'terdaftar')
                ->get()
                ->pluck('jadwal')
                ->filter(fn ($j) => $j !== null);

            $allVerifiedPendaftarans = $praktikan->pendaftarans()
                ->where('status', 'verified')
                ->with(['praktikum', 'sesi.jadwalPraktikums', 'presensis', 'penilaianAkhir'])
                ->withCount('presensis')
                ->get()
                ->map(function ($pendaftaran) {
                    $praktikum = $pendaftaran->praktikum;
                    $totalModules = $praktikum->jumlah_modul;
                    $hasFinalProject = $praktikum->ada_tugas_akhir;

                    $totalSteps = $totalModules + ($hasFinalProject ? 1 : 0);
                    $completedSteps = $pendaftaran->presensis_count;

                    $pendaftaran->progress_percentage = $totalSteps > 0
                        ? min(100, round(($completedSteps / $totalSteps) * 100))
                        : 0;

                    return $pendaftaran;
                });

            // Praktikum yang masih berjalan/aktif (status_praktikum != finished)
            $activePendaftarans = $allVerifiedPendaftarans->filter(function ($p) {
                return $p->praktikum && $p->praktikum->status_praktikum !== 'finished';
            })->values();

            // Praktikum yang telah selesai/berakhir (status_praktikum == finished)
            $completedPendaftarans = $allVerifiedPendaftarans->filter(function ($p) {
                return $p->praktikum && $p->praktikum->status_praktikum === 'finished';
            })->values();

            $upcomingSchedules = collect();
            if ($activePendaftarans->isNotEmpty()) {
                $upcomingSchedules = \App\Models\JadwalPraktikum::with(['praktikum', 'presensis' => function ($q) use ($praktikan) {
                    $q->whereHas('pendaftaran', function ($pq) use ($praktikan) {
                        $pq->where('praktikan_id', $praktikan->id);
                    });
                }])
                    ->where(function ($query) use ($activePendaftarans) {
                        foreach ($activePendaftarans as $p) {
                            $query->orWhere(function ($q) use ($p) {
                                $q->where('praktikum_id', $p->praktikum_id);
                                if ($p->sesi_id) {
                                    $q->where(function ($sq) use ($p) {
                                        $sq->where('sesi_id', $p->sesi_id)
                                            ->orWhereNull('sesi_id');
                                    });
                                }
                            });
                        }
                    })
                    ->where('tanggal', '>=', now()->subDay()->toDateString())
                    ->orderBy('tanggal', 'asc')
                    ->orderBy('waktu_mulai', 'asc')
                    ->get();
            }
        }

        $penugasans = collect();
        if ($praktikan) {
            $npm = $praktikan->npm;
            $lastDigit = intval(substr($npm, -1));
            $pendaftarans = \App\Models\PendaftaranPraktikum::with([
                'praktikum',
                'sesi.jadwalPraktikums',
                'penugasanOverrides.penugasan.praktikum',
                'penugasanOverrides.penugasan.sesi',
                'penugasanOverrides.penugasan.aslab.user',
            ])
                ->where('praktikan_id', $praktikan->id)
                ->where('status', 'verified')
                ->get();

            $distributionService = app(\App\Services\PenugasanDistributionService::class);
            $penugasans = $distributionService->resolvePenugasansForStudent($praktikan, $pendaftarans)
                ->sortByDesc('created_at')
                ->values();

            $now = \Carbon\Carbon::now('Asia/Jakarta');
            $currentDay = $now->locale('id')->dayName;
            $currentTime = $now->format('H:i:s');
            $currentDate = $now->format('Y-m-d');

            foreach ($penugasans as $penugasan) {
                $isAccessible = false;
                $hasPresensi = false;
                $isWithinTime = false;

                if ($penugasan->jadwal_praktikum_id) {
                    $jadwal = $penugasan->jadwalPraktikum;
                    $isSameDate = $jadwal->tanggal === $currentDate;
                    $isWithinTimeRange = ($currentTime >= $jadwal->waktu_mulai && $currentTime <= $jadwal->waktu_selesai);
                    $isWithinTime = env('APP_ENV') !== 'production' && env('PENUGASAN_BYPASS_WAKTU', false) ? true : ($isSameDate && $isWithinTimeRange);

                    $hasPresensi = env('APP_ENV') !== 'production' && env('PENUGASAN_BYPASS_PRESENSI', false) ? true : \App\Models\Presensi::where('pendaftaran_id', $pendaftarans->where('sesi_id', $penugasan->sesi_id)->first()?->id)
                        ->where('jadwal_id', $penugasan->jadwal_praktikum_id)
                        ->whereIn('status', ['hadir', 'terlambat'])
                        ->exists();

                    $isAccessible = $isWithinTime && $hasPresensi;
                } else {
                    $sesi = $penugasan->sesi;
                    $isSameDay = strtolower($sesi->hari) === strtolower($currentDay);
                    $isWithinTimeRange = ($currentTime >= $sesi->jam_mulai && $currentTime <= $sesi->jam_selesai);
                    $isWithinTime = env('APP_ENV') !== 'production' && env('PENUGASAN_BYPASS_WAKTU', false) ? true : ($isSameDay && $isWithinTimeRange);

                    $isRegistered = $pendaftarans->where('sesi_id', $penugasan->sesi_id)
                        ->where('praktikum_id', $penugasan->praktikum_id)
                        ->isNotEmpty();

                    $hasPresensi = env('APP_ENV') !== 'production' && env('PENUGASAN_BYPASS_PRESENSI', false) ? true : \App\Models\Presensi::whereHas('pendaftaran', function ($query) use ($praktikan, $penugasan) {
                        $query->where('praktikan_id', $praktikan->id)
                            ->where('praktikum_id', $penugasan->praktikum_id);
                    })->whereIn('status', ['hadir', 'terlambat'])->exists();

                    $isAccessible = $isRegistered && $isWithinTime && $hasPresensi;
                }

                $penugasan->is_accessible = $isAccessible;
                $penugasan->has_presensi = $hasPresensi;
                $penugasan->is_within_time = $isWithinTime;
            }
        }

        $recruitmentSchedules = collect();
        if ($user) {
            $recruitmentSchedules = \App\Models\RecruitmentSchedule::whereHas('applications', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
                ->where('date', '>=', now()->toDateString())
                ->orderBy('date', 'asc')
                ->orderBy('start_time', 'asc')
                ->get();
        }

        return view('praktikan.dashboard', compact('praktikums', 'upcomingSchedules', 'activePendaftarans', 'completedPendaftarans', 'penugasans', 'recruitmentSchedules', 'gugurPendaftarans', 'susulanSchedules'));
    }
}
