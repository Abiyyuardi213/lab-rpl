<?php

namespace App\Http\Controllers\Praktikan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Penugasan;
use App\Models\PendaftaranPraktikum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PenugasanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $praktikan = $user->praktikan;

        if (!$praktikan) {
            return redirect()->route('praktikan.dashboard')
                ->with('error', 'Profil praktikan tidak ditemukan. Hubungi admin.');
        }

        $pendaftarans = PendaftaranPraktikum::with([
            'praktikum',
            'sesi',
            'penugasanOverride.penugasan.praktikum',
            'penugasanOverride.penugasan.sesi',
            'penugasanOverride.penugasan.aslab.user',
        ])
            ->where('praktikan_id', $praktikan->id)
            ->where('status', 'verified')
            ->get();

        $npm = $praktikan->npm;
        $lastDigit = intval(substr($npm, -1));
        $overridePenugasanIds = $pendaftarans->pluck('penugasanOverride.penugasan_id')->filter()->values();
        $overrideSesiIds = $pendaftarans->filter(fn($pendaftaran) => $pendaftaran->penugasanOverride)->pluck('sesi_id');
        $defaultSesiIds = $pendaftarans->whereNotIn('sesi_id', $overrideSesiIds)->pluck('sesi_id')->values();

        $defaultPenugasans = Penugasan::with(['praktikum', 'sesi', 'aslab.user'])
            ->whereIn('sesi_id', $defaultSesiIds)
            ->where('kode_akhir_npm', (string)$lastDigit)
            ->orderBy('created_at', 'desc')
            ->get();

        $overridePenugasans = Penugasan::with(['praktikum', 'sesi', 'aslab.user'])
            ->whereIn('id', $overridePenugasanIds)
            ->get();

        $penugasans = $defaultPenugasans
            ->merge($overridePenugasans)
            ->unique('id')
            ->sortByDesc('created_at')
            ->values();

        $now = Carbon::now('Asia/Jakarta');
        $currentDay = $now->locale('id')->dayName;
        $currentTime = $now->format('H:i:s');

        foreach ($penugasans as $penugasan) {
            $isAccessible = false;
            $hasPresensi = false;
            $isWithinTime = false;

            $now = Carbon::now('Asia/Jakarta');
            $currentDay = $now->locale('id')->dayName;
            $currentTime = $now->format('H:i:s');
            $currentDate = $now->format('Y-m-d');

            if ($penugasan->jadwal_praktikum_id) {
                $jadwal = $penugasan->jadwalPraktikum;
                
                // 1. Check Time Access
                $isSameDate = $jadwal->tanggal === $currentDate;
                $isWithinTimeRange = ($currentTime >= $jadwal->waktu_mulai && $currentTime <= $jadwal->waktu_selesai);
                $isWithinTime = $this->shouldBypassWaktuForTesting() || ($isSameDate && $isWithinTimeRange);

                // 2. Check Presence
                $hasPresensi = $this->shouldBypassPresensiForTesting() || \App\Models\Presensi::where('pendaftaran_id', $pendaftarans->where('sesi_id', $penugasan->sesi_id)->first()?->id)
                    ->where('jadwal_id', $penugasan->jadwal_praktikum_id)
                    ->whereIn('status', ['hadir', 'terlambat'])
                    ->exists();

                // Accessible ONLY if within time AND has presence
                $isAccessible = $isWithinTime && $hasPresensi;
            } else {
                // General Assignment
                $sesi = $penugasan->sesi;
                $isSameDay = strtolower($sesi->hari) === strtolower($currentDay);
                $isWithinTimeRange = ($currentTime >= $sesi->jam_mulai && $currentTime <= $sesi->jam_selesai);
                $isWithinTime = $this->shouldBypassWaktuForTesting() || ($isSameDay && $isWithinTimeRange);

                // For general assignments, we might check if they have registered
                $isRegistered = $pendaftarans->where('sesi_id', $penugasan->sesi_id)
                                          ->where('praktikum_id', $penugasan->praktikum_id)
                                          ->isNotEmpty();
                
                // For general tasks, let's say they still need to have marked attendance in AT LEAST ONE session of this praktikum today?
                // Or maybe just presence in the session.
                $hasPresensi = $this->shouldBypassPresensiForTesting() || \App\Models\Presensi::whereHas('pendaftaran', function($query) use ($praktikan, $penugasan) {
                    $query->where('praktikan_id', $praktikan->id)
                          ->where('praktikum_id', $penugasan->praktikum_id);
                })->whereIn('status', ['hadir', 'terlambat'])->exists();

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
        $praktikan = $user->praktikan;

        if (!$praktikan) {
            return redirect()->route('praktikan.dashboard')
                ->with('error', 'Profil praktikan tidak ditemukan. Hubungi admin.');
        }

        $penugasan = Penugasan::with(['praktikum', 'sesi', 'aslab.user'])->findOrFail($id);

        // Check if student is registered in this session
        $pendaftaran = PendaftaranPraktikum::with('penugasanOverride')
            ->where('praktikan_id', $praktikan->id)
            ->where('sesi_id', $penugasan->sesi_id)
            ->where('status', 'verified')
            ->first();

        if (!$pendaftaran) {
            abort(403, 'Anda tidak terdaftar dalam sesi praktikum ini.');
        }

        $npm = $praktikan->npm;
        $lastDigit = intval(substr($npm, -1));
        $hasCustomAssignment = $pendaftaran->penugasanOverride?->penugasan_id === $penugasan->id;

        if ($pendaftaran->penugasanOverride && !$hasCustomAssignment) {
            abort(403, 'Soal ini sudah diganti khusus oleh admin.');
        }

        if (!$hasCustomAssignment && $penugasan->kode_akhir_npm !== null && (string)$penugasan->kode_akhir_npm !== (string)$lastDigit) {
            abort(403, 'Soal ini tidak ditujukan untuk NPM Anda.');
        }

        $now = Carbon::now('Asia/Jakarta');
        $currentDay = $now->locale('id')->dayName;
        $currentTime = $now->format('H:i:s');
        $currentDate = $now->format('Y-m-d');

        // 2. Schedule Check (If the assignment is linked to a specific schedule)
        if ($penugasan->jadwal_praktikum_id) {
            $jadwal = $penugasan->jadwalPraktikum;
            
            // Check Time
            $isSameDate = $jadwal->tanggal === $currentDate;
            $isWithinTimeRange = ($currentTime >= $jadwal->waktu_mulai && $currentTime <= $jadwal->waktu_selesai);
            $isWithinTime = $this->shouldBypassWaktuForTesting() || ($isSameDate && $isWithinTimeRange);

            if (!$isWithinTime) {
                return redirect()->route('praktikan.penugasan.index')
                    ->with('error', 'Soal hanya dapat diakses pada jadwal praktikum terkait (' . Carbon::parse($jadwal->tanggal)->format('d M Y') . ', ' . $jadwal->waktu_mulai . ' - ' . $jadwal->waktu_selesai . ').');
            }

            // Check Presence
            $hasPresensi = $this->shouldBypassPresensiForTesting() || \App\Models\Presensi::where('pendaftaran_id', $pendaftaran->id)
                ->where('jadwal_id', $penugasan->jadwal_praktikum_id)
                ->whereIn('status', ['hadir', 'terlambat'])
                ->exists();

            if (!$hasPresensi) {
                return redirect()->route('praktikan.penugasan.index')
                    ->with('error', 'Anda harus melakukan presensi QR terlebih dahulu untuk melihat soal ini.');
            }
        } else {
            // General Assignment
            $sesi = $penugasan->sesi;
            $isSameDay = strtolower($sesi->hari) === strtolower($currentDay);
            $isWithinTimeRange = ($currentTime >= $sesi->jam_mulai && $currentTime <= $sesi->jam_selesai);
            $isWithinTime = $this->shouldBypassWaktuForTesting() || ($isSameDay && $isWithinTimeRange);

            if (!$isWithinTime) {
                return redirect()->route('praktikan.penugasan.index')
                    ->with('error', 'Soal hanya dapat diakses pada hari dan jam sesi praktikum Anda (' . $sesi->hari . ', ' . $sesi->jam_mulai . ' - ' . $sesi->jam_selesai . ').');
            }

            $hasPresensi = $this->shouldBypassPresensiForTesting() || \App\Models\Presensi::whereHas('pendaftaran', function($query) use ($praktikan, $penugasan) {
                $query->where('praktikan_id', $praktikan->id)
                      ->where('praktikum_id', $penugasan->praktikum_id);
            })->whereIn('status', ['hadir', 'terlambat'])->exists();

            if (!$hasPresensi) {
                return redirect()->route('praktikan.penugasan.index')
                    ->with('error', 'Silakan lakukan presensi QR terlebih dahulu pada jadwal modul terkait untuk membuka akses.');
            }
        }

        return view('praktikan.penugasan.show', compact('penugasan'));
    }

    public function download($id)
    {
        $user = Auth::user();
        $praktikan = $user->praktikan;

        if (!$praktikan) {
            return redirect()->route('praktikan.dashboard')
                ->with('error', 'Profil praktikan tidak ditemukan. Hubungi admin.');
        }

        $penugasan = Penugasan::with('sesi')->findOrFail($id);

        // 1. Check registration
        $pendaftaran = PendaftaranPraktikum::with('penugasanOverride')
            ->where('praktikan_id', $praktikan->id)
            ->where('sesi_id', $penugasan->sesi_id)
            ->where('status', 'verified')
            ->first();

        if (!$pendaftaran) {
            abort(403, 'Anda tidak terdaftar dalam sesi praktikum ini.');
        }

        // 2. Schedule Check (If the assignment is linked to a specific schedule)
        if ($penugasan->jadwal_praktikum_id) {
            $hasPresensi = $this->shouldBypassPresensiForTesting() || \App\Models\Presensi::where('pendaftaran_id', $pendaftaran->id)
                ->where('jadwal_id', $penugasan->jadwal_praktikum_id)
                ->whereIn('status', ['hadir', 'terlambat'])
                ->exists();

            if (!$hasPresensi) {
                return abort(403, 'Anda harus hadir pada jadwal praktikum terkait untuk mengunduh soal ini.');
            }
        }

        // 2. Check NPM Digit
        $npm = $praktikan->npm;
        $lastDigit = intval(substr($npm, -1));
        $hasCustomAssignment = $pendaftaran->penugasanOverride?->penugasan_id === $penugasan->id;

        if ($pendaftaran->penugasanOverride && !$hasCustomAssignment) {
            abort(403, 'Soal ini sudah diganti khusus oleh admin.');
        }

        if (!$hasCustomAssignment && $penugasan->kode_akhir_npm !== null && (string)$penugasan->kode_akhir_npm !== (string)$lastDigit) {
            abort(403, 'Soal ini tidak ditujukan untuk NPM Anda.');
        }

        $now = Carbon::now('Asia/Jakarta');
        $currentDay = $now->locale('id')->dayName;
        $currentTime = $now->format('H:i:s');
        $currentDate = $now->format('Y-m-d');

        if ($penugasan->jadwal_praktikum_id) {
            $jadwal = $penugasan->jadwalPraktikum;
            
            // Check Time
            $isSameDate = $jadwal->tanggal === $currentDate;
            $isWithinTimeRange = ($currentTime >= $jadwal->waktu_mulai && $currentTime <= $jadwal->waktu_selesai);
            $isWithinTime = $this->shouldBypassWaktuForTesting() || ($isSameDate && $isWithinTimeRange);

            if (!$isWithinTime) {
                abort(403, 'File hanya dapat diunduh pada jadwal praktikum terkait.');
            }

            // Check Presence
            $hasPresensi = $this->shouldBypassPresensiForTesting() || \App\Models\Presensi::where('pendaftaran_id', $pendaftaran->id)
                ->where('jadwal_id', $penugasan->jadwal_praktikum_id)
                ->whereIn('status', ['hadir', 'terlambat'])
                ->exists();

            if (!$hasPresensi) {
                abort(403, 'Gagal mengunduh. Anda belum tercatat hadir di sesi ini.');
            }
        } else {
            // General Assignment
            $sesi = $penugasan->sesi;
            $isSameDay = strtolower($sesi->hari) === strtolower($currentDay);
            $isWithinTimeRange = ($currentTime >= $sesi->jam_mulai && $currentTime <= $sesi->jam_selesai);
            $isWithinTime = $this->shouldBypassWaktuForTesting() || ($isSameDay && $isWithinTimeRange);

            if (!$isWithinTime) {
                abort(403, 'File hanya dapat diunduh pada jam sesi praktikum Anda.');
            }

            $hasPresensi = $this->shouldBypassPresensiForTesting() || \App\Models\Presensi::where('pendaftaran_id', $pendaftaran->id)
                ->whereIn('status', ['hadir', 'terlambat'])
                ->exists();

            if (!$hasPresensi) {
                abort(403, 'Gagal mengunduh. Anda belum tercatat hadir di sesi ini.');
            }
        }

        // 4. Check file existence
        if (!$penugasan->file_soal || !Storage::disk('public')->exists($penugasan->file_soal)) {
            return back()->with('error', 'File soal tidak ditemukan di server.');
        }

        $path = Storage::disk('public')->path($penugasan->file_soal);
        $fileName = $penugasan->judul . '.' . pathinfo($penugasan->file_soal, PATHINFO_EXTENSION);

        return response()->download($path, $fileName);
    }

    private function shouldBypassPresensiForTesting(): bool
    {
        return app()->environment('local')
            && filter_var(env('PENUGASAN_BYPASS_PRESENSI', false), FILTER_VALIDATE_BOOLEAN);
    }

    private function shouldBypassWaktuForTesting(): bool
    {
        return app()->environment('local')
            && filter_var(env('PENUGASAN_BYPASS_WAKTU', false), FILTER_VALIDATE_BOOLEAN);
    }

}
