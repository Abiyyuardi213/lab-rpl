<?php

namespace App\Http\Controllers\Praktikan;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranPraktikum;
use App\Models\Penugasan;
use App\Services\PenugasanDistributionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PenugasanController extends Controller
{
    protected PenugasanDistributionService $distributionService;

    public function __construct(PenugasanDistributionService $distributionService)
    {
        $this->distributionService = $distributionService;
    }

    public function index()
    {
        $user = Auth::user();
        $praktikan = $user ? $user->praktikan : null;

        if (! $praktikan) {
            return redirect()->route('praktikan.dashboard')
                ->with('error', 'Profil praktikan tidak ditemukan. Hubungi admin.');
        }

        $pendaftarans = PendaftaranPraktikum::with([
            'praktikum',
            'sesi.jadwalPraktikums',
            'penugasanOverrides.penugasan.praktikum',
            'penugasanOverrides.penugasan.sesi',
            'penugasanOverrides.penugasan.aslab.user',
        ])
            ->where('praktikan_id', $praktikan->id)
            ->where('status', 'verified')
            ->get();

        $filteredPenugasans = $this->distributionService->resolvePenugasansForStudent($praktikan, $pendaftarans);

        $penugasans = $filteredPenugasans
            ->sort(function ($a, $b) {
                if ($a->praktikum_id !== $b->praktikum_id) {
                    return $a->praktikum_id <=> $b->praktikum_id;
                }

                $dateA = $a->jadwalPraktikum ? $a->jadwalPraktikum->tanggal : $a->created_at->toDateString();
                $dateB = $b->jadwalPraktikum ? $b->jadwalPraktikum->tanggal : $b->created_at->toDateString();

                if ($dateA !== $dateB) {
                    return $dateB <=> $dateA;
                }

                return $b->created_at <=> $a->created_at;
            })
            ->values();

        foreach ($penugasans as $penugasan) {
            $isAccessible = false;
            $hasPresensi = false;
            $isWithinTime = false;

            $now = Carbon::now('Asia/Jakarta');
            $currentDay = $now->locale('id')->dayName;
            $currentTime = $now->format('H:i:s');
            $currentDate = $now->format('Y-m-d');

            $matchingPendaftaran = $pendaftarans->first(function ($p) use ($penugasan) {
                return $p->praktikum_id === $penugasan->praktikum_id
                    && (! $penugasan->sesi_id || $p->sesi_id === $penugasan->sesi_id);
            });

            if ($penugasan->jadwal_praktikum_id) {
                $jadwal = $penugasan->jadwalPraktikum;
                $isSameDate = $jadwal->tanggal === $currentDate;
                $isWithinTimeRange = ($currentTime >= $jadwal->waktu_mulai && $currentTime <= $jadwal->waktu_selesai);
                $isWithinTime = $this->shouldBypassWaktuForTesting() || ($isSameDate && $isWithinTimeRange);

                $hasPresensi = $this->shouldBypassPresensiForTesting() || ($matchingPendaftaran && \App\Models\Presensi::where('pendaftaran_id', $matchingPendaftaran->id)
                    ->where('jadwal_id', $penugasan->jadwal_praktikum_id)
                    ->whereIn('status', ['hadir', 'terlambat'])
                    ->exists());

                $isAccessible = $matchingPendaftaran !== null && $isWithinTime && $hasPresensi;
            } else {
                $sesi = $penugasan->sesi;
                if ($sesi) {
                    $isSameDay = strtolower($sesi->hari) === strtolower($currentDay);
                    $isWithinTimeRange = ($currentTime >= $sesi->jam_mulai && $currentTime <= $sesi->jam_selesai);
                    $isWithinTime = $this->shouldBypassWaktuForTesting() || ($isSameDay && $isWithinTimeRange);
                } else {
                    $isWithinTime = true;
                }

                $isRegistered = $matchingPendaftaran !== null;

                $hasPresensi = $this->shouldBypassPresensiForTesting() || ($matchingPendaftaran && \App\Models\Presensi::where('pendaftaran_id', $matchingPendaftaran->id)
                    ->whereIn('status', ['hadir', 'terlambat'])
                    ->exists());

                $isAccessible = $isRegistered && $isWithinTime && $hasPresensi;
            }

            $penugasan->is_accessible = $isAccessible;
            $penugasan->has_presensi = $hasPresensi;
            $penugasan->is_within_time = $isWithinTime;
        }

        return view('praktikan.penugasan.index', compact('penugasans', 'pendaftarans'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $praktikan = $user ? $user->praktikan : null;

        if (! $praktikan) {
            return redirect()->route('praktikan.dashboard')
                ->with('error', 'Profil praktikan tidak ditemukan. Hubungi admin.');
        }

        $penugasan = Penugasan::with(['praktikum', 'sesi', 'aslab.user', 'jadwalPraktikum'])->findOrFail($id);

        $pendaftaran = PendaftaranPraktikum::with(['penugasanOverrides' => function ($q) use ($penugasan) {
            $q->where('jadwal_praktikum_id', $penugasan->jadwal_praktikum_id);
        }])
            ->where('praktikan_id', $praktikan->id)
            ->where('praktikum_id', $penugasan->praktikum_id)
            ->when($penugasan->sesi_id && ! $penugasan->jadwalPraktikum?->is_susulan, function ($q, $sesiId) {
                $q->where('sesi_id', $sesiId);
            })
            ->first();

        if (! $pendaftaran) {
            abort(403, 'Anda tidak terdaftar dalam sesi praktikum ini.');
        }

        if ($pendaftaran->status === 'gugur') {
            abort(403, 'Anda telah dinyatakan gugur dalam praktikum ini.');
        }

        if ($pendaftaran->status !== 'verified') {
            abort(403, 'Status pendaftaran Anda belum terverifikasi.');
        }

        if ($penugasan->jadwalPraktikum?->is_susulan) {
            $isPesertaSusulan = \App\Models\PesertaSusulan::where('jadwal_id', $penugasan->jadwal_praktikum_id)
                ->where('pendaftaran_id', $pendaftaran->id)
                ->exists();

            if (! $isPesertaSusulan) {
                abort(403, 'Anda tidak terdaftar sebagai peserta pada jadwal susulan ini.');
            }
        }

        if (! $this->distributionService->canStudentAccessPenugasan($penugasan, $praktikan, $pendaftaran)) {
            abort(403, 'Soal ini tidak ditujukan untuk Anda.');
        }

        $now = Carbon::now('Asia/Jakarta');
        $currentDay = $now->locale('id')->dayName;
        $currentTime = $now->format('H:i:s');
        $currentDate = $now->format('Y-m-d');

        if ($penugasan->jadwal_praktikum_id) {
            $jadwal = $penugasan->jadwalPraktikum;
            $isSameDate = $jadwal->tanggal === $currentDate;
            $isWithinTimeRange = ($currentTime >= $jadwal->waktu_mulai && $currentTime <= $jadwal->waktu_selesai);
            $isWithinTime = $this->shouldBypassWaktuForTesting() || ($isSameDate && $isWithinTimeRange);

            if (! $isWithinTime) {
                return redirect()->route('praktikan.penugasan.index')
                    ->with('error', 'Soal hanya dapat diakses pada jadwal praktikum terkait.');
            }

            $hasPresensi = $this->shouldBypassPresensiForTesting() || \App\Models\Presensi::where('pendaftaran_id', $pendaftaran->id)
                ->where('jadwal_id', $penugasan->jadwal_praktikum_id)
                ->whereIn('status', ['hadir', 'terlambat'])
                ->exists();

            if (! $hasPresensi) {
                return redirect()->route('praktikan.penugasan.index')
                    ->with('error', 'Anda harus melakukan presensi QR terlebih dahulu.');
            }
        } else {
            $sesi = $penugasan->sesi;
            if ($sesi) {
                $isSameDay = strtolower($sesi->hari) === strtolower($currentDay);
                $isWithinTimeRange = ($currentTime >= $sesi->jam_mulai && $currentTime <= $sesi->jam_selesai);
                $isWithinTime = $this->shouldBypassWaktuForTesting() || ($isSameDay && $isWithinTimeRange);

                if (! $isWithinTime) {
                    return redirect()->route('praktikan.penugasan.index')
                        ->with('error', 'Soal hanya dapat diakses pada jam sesi praktikum Anda.');
                }
            }

            $hasPresensi = $this->shouldBypassPresensiForTesting() || \App\Models\Presensi::where('pendaftaran_id', $pendaftaran->id)
                ->whereIn('status', ['hadir', 'terlambat'])
                ->exists();

            if (! $hasPresensi) {
                return redirect()->route('praktikan.penugasan.index')
                    ->with('error', 'Silakan lakukan presensi QR terlebih dahulu.');
            }
        }

        return view('praktikan.penugasan.show', compact('penugasan'));
    }

    public function download($id)
    {
        $user = Auth::user();
        $praktikan = $user ? $user->praktikan : null;

        if (! $praktikan) {
            return redirect()->route('praktikan.dashboard')
                ->with('error', 'Profil praktikan tidak ditemukan. Hubungi admin.');
        }

        $penugasan = Penugasan::with(['sesi', 'jadwalPraktikum'])->findOrFail($id);

        $pendaftaran = PendaftaranPraktikum::with(['penugasanOverrides' => function ($q) use ($penugasan) {
            $q->where('jadwal_praktikum_id', $penugasan->jadwal_praktikum_id);
        }])
            ->where('praktikan_id', $praktikan->id)
            ->where('praktikum_id', $penugasan->praktikum_id)
            ->when($penugasan->sesi_id && ! $penugasan->jadwalPraktikum?->is_susulan, function ($q, $sesiId) {
                $q->where('sesi_id', $sesiId);
            })
            ->first();

        if (! $pendaftaran) {
            abort(403, 'Anda tidak terdaftar dalam sesi praktikum ini.');
        }

        if ($pendaftaran->status === 'gugur') {
            abort(403, 'Anda telah dinyatakan gugur dalam praktikum ini.');
        }

        if ($pendaftaran->status !== 'verified') {
            abort(403, 'Status pendaftaran Anda belum terverifikasi.');
        }

        if ($penugasan->jadwalPraktikum?->is_susulan) {
            $isPesertaSusulan = \App\Models\PesertaSusulan::where('jadwal_id', $penugasan->jadwal_praktikum_id)
                ->where('pendaftaran_id', $pendaftaran->id)
                ->exists();

            if (! $isPesertaSusulan) {
                abort(403, 'Anda tidak terdaftar sebagai peserta pada jadwal susulan ini.');
            }
        }

        if (! $this->distributionService->canStudentAccessPenugasan($penugasan, $praktikan, $pendaftaran)) {
            abort(403, 'Akses terbatas.');
        }

        $now = Carbon::now('Asia/Jakarta');
        if ($penugasan->jadwal_praktikum_id) {
            $jadwal = $penugasan->jadwalPraktikum;
            $isWithinTime = $this->shouldBypassWaktuForTesting() || ($jadwal->tanggal === $now->format('Y-m-d') && $now->format('H:i:s') >= $jadwal->waktu_mulai);
            if (! $isWithinTime) {
                abort(403, 'Belum waktunya.');
            }

            $hasPresensi = $this->shouldBypassPresensiForTesting() || \App\Models\Presensi::where('pendaftaran_id', $pendaftaran->id)
                ->where('jadwal_id', $penugasan->jadwal_praktikum_id)->exists();
            if (! $hasPresensi) {
                abort(403, 'Belum presensi.');
            }
        } else {
            $hasPresensi = $this->shouldBypassPresensiForTesting() || \App\Models\Presensi::where('pendaftaran_id', $pendaftaran->id)
                ->whereIn('status', ['hadir', 'terlambat'])->exists();
            if (! $hasPresensi) {
                abort(403, 'Belum presensi.');
            }
        }

        if (! $penugasan->file_soal || ! Storage::disk('public')->exists($penugasan->file_soal)) {
            return back()->with('error', 'File soal tidak ditemukan.');
        }

        $path = Storage::disk('public')->path($penugasan->file_soal);
        $fileName = $penugasan->judul.'.'.pathinfo($penugasan->file_soal, PATHINFO_EXTENSION);

        return response()->download($path, $fileName);
    }

    private function shouldBypassPresensiForTesting()
    {
        return app()->environment('local') && env('PENUGASAN_BYPASS_PRESENSI', false);
    }

    private function shouldBypassWaktuForTesting()
    {
        return app()->environment('local') && env('PENUGASAN_BYPASS_WAKTU', false);
    }
}
