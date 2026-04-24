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
        $praktikan = $user ? $user->praktikan : null;

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

        $npm = (string) ($praktikan->npm ?? '');
        $lastChar = substr($npm, -1);
        $lastDigit = is_numeric($lastChar) ? (string)$lastChar : null;

        $overridePenugasanIds = $pendaftarans->map(fn($p) => $p->penugasanOverride?->penugasan_id)->filter()->values();
        
        $overrideSesiIds = $pendaftarans->filter(fn($p) => $p->penugasanOverride)->pluck('sesi_id');
        
        $defaultSesiIds = $pendaftarans->whereNotIn('sesi_id', $overrideSesiIds)->pluck('sesi_id')->values();

        $allPotentialPenugasans = Penugasan::with(['praktikum', 'sesi', 'aslab.user', 'jadwalPraktikum'])
            ->whereIn('sesi_id', $defaultSesiIds)
            ->get();

        $defaultPenugasans = $allPotentialPenugasans->filter(function ($penugasan) use ($lastDigit) {
            $kode = (string) $penugasan->kode_akhir_npm;
            if ($kode === '*') return true;
            if ($lastDigit !== null && $kode === $lastDigit) return true;
            return false;
        });

        $overridePenugasans = Penugasan::with(['praktikum', 'sesi', 'aslab.user'])
            ->whereIn('id', $overridePenugasanIds)
            ->get();

        $penugasans = $defaultPenugasans
            ->merge($overridePenugasans)
            ->unique('id')
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

            if ($penugasan->jadwal_praktikum_id) {
                $jadwal = $penugasan->jadwalPraktikum;
                $isSameDate = $jadwal->tanggal === $currentDate;
                $isWithinTimeRange = ($currentTime >= $jadwal->waktu_mulai && $currentTime <= $jadwal->waktu_selesai);
                $isWithinTime = $this->shouldBypassWaktuForTesting() || ($isSameDate && $isWithinTimeRange);

                $hasPresensi = $this->shouldBypassPresensiForTesting() || \App\Models\Presensi::where('pendaftaran_id', $pendaftarans->where('sesi_id', $penugasan->sesi_id)->first()?->id)
                    ->where('jadwal_id', $penugasan->jadwal_praktikum_id)
                    ->whereIn('status', ['hadir', 'terlambat'])
                    ->exists();

                $isAccessible = $isWithinTime && $hasPresensi;
            } else {
                $sesi = $penugasan->sesi;
                $isSameDay = strtolower($sesi->hari) === strtolower($currentDay);
                $isWithinTimeRange = ($currentTime >= $sesi->jam_mulai && $currentTime <= $sesi->jam_selesai);
                $isWithinTime = $this->shouldBypassWaktuForTesting() || ($isSameDay && $isWithinTimeRange);

                $isRegistered = $pendaftarans->where('sesi_id', $penugasan->sesi_id)
                                          ->where('praktikum_id', $penugasan->praktikum_id)
                                          ->isNotEmpty();
                
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
        $praktikan = $user ? $user->praktikan : null;

        if (!$praktikan) {
            return redirect()->route('praktikan.dashboard')
                ->with('error', 'Profil praktikan tidak ditemukan. Hubungi admin.');
        }

        $penugasan = Penugasan::with(['praktikum', 'sesi', 'aslab.user', 'jadwalPraktikum'])->findOrFail($id);

        $pendaftaran = PendaftaranPraktikum::with('penugasanOverride')
            ->where('praktikan_id', $praktikan->id)
            ->where('sesi_id', $penugasan->sesi_id)
            ->where('status', 'verified')
            ->first();

        if (!$pendaftaran) {
            abort(403, 'Anda tidak terdaftar dalam sesi praktikum ini.');
        }

        $npm = (string) ($praktikan->npm ?? '');
        $lastChar = substr($npm, -1);
        $lastDigit = is_numeric($lastChar) ? (string)$lastChar : null;
        $kodeSoal = (string) $penugasan->kode_akhir_npm;
        
        $hasCustomAssignment = $pendaftaran->penugasanOverride?->penugasan_id === $penugasan->id;

        if ($pendaftaran->penugasanOverride && !$hasCustomAssignment) {
            abort(403, 'Soal ini sudah diganti khusus oleh admin.');
        }

        $isUniversal = $kodeSoal === '*';
        $isMatchDigit = ($lastDigit !== null && $kodeSoal === $lastDigit);

        if (!$hasCustomAssignment && !$isUniversal && !$isMatchDigit) {
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

            if (!$isWithinTime) {
                return redirect()->route('praktikan.penugasan.index')
                    ->with('error', 'Soal hanya dapat diakses pada jadwal praktikum terkait.');
            }

            $hasPresensi = $this->shouldBypassPresensiForTesting() || \App\Models\Presensi::where('pendaftaran_id', $pendaftaran->id)
                ->where('jadwal_id', $penugasan->jadwal_praktikum_id)
                ->whereIn('status', ['hadir', 'terlambat'])
                ->exists();

            if (!$hasPresensi) {
                return redirect()->route('praktikan.penugasan.index')
                    ->with('error', 'Anda harus melakukan presensi QR terlebih dahulu.');
            }
        } else {
            $sesi = $penugasan->sesi;
            $isSameDay = strtolower($sesi->hari) === strtolower($currentDay);
            $isWithinTimeRange = ($currentTime >= $sesi->jam_mulai && $currentTime <= $sesi->jam_selesai);
            $isWithinTime = $this->shouldBypassWaktuForTesting() || ($isSameDay && $isWithinTimeRange);

            if (!$isWithinTime) {
                return redirect()->route('praktikan.penugasan.index')
                    ->with('error', 'Soal hanya dapat diakses pada jam sesi praktikum Anda.');
            }

            $hasPresensi = $this->shouldBypassPresensiForTesting() || \App\Models\Presensi::whereHas('pendaftaran', function($query) use ($praktikan, $penugasan) {
                $query->where('praktikan_id', $praktikan->id)
                      ->where('praktikum_id', $penugasan->praktikum_id);
            })->whereIn('status', ['hadir', 'terlambat'])->exists();

            if (!$hasPresensi) {
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

        if (!$praktikan) {
            return redirect()->route('praktikan.dashboard')
                ->with('error', 'Profil praktikan tidak ditemukan. Hubungi admin.');
        }

        $penugasan = Penugasan::with(['sesi', 'jadwalPraktikum'])->findOrFail($id);

        $pendaftaran = PendaftaranPraktikum::with('penugasanOverride')
            ->where('praktikan_id', $praktikan->id)
            ->where('sesi_id', $penugasan->sesi_id)
            ->where('status', 'verified')
            ->first();

        if (!$pendaftaran) {
            abort(403, 'Anda tidak terdaftar dalam sesi praktikum ini.');
        }

        $npm = (string) ($praktikan->npm ?? '');
        $lastChar = substr($npm, -1);
        $lastDigit = is_numeric($lastChar) ? (string)$lastChar : null;
        $kodeSoal = (string) $penugasan->kode_akhir_npm;
        
        $hasCustomAssignment = $pendaftaran->penugasanOverride?->penugasan_id === $penugasan->id;

        $isUniversal = $kodeSoal === '*';
        $isMatchDigit = ($lastDigit !== null && $kodeSoal === $lastDigit);

        if (!$hasCustomAssignment && !$isUniversal && !$isMatchDigit) {
            abort(403, 'Akses terbatas.');
        }

        $now = Carbon::now('Asia/Jakarta');
        if ($penugasan->jadwal_praktikum_id) {
            $jadwal = $penugasan->jadwalPraktikum;
            $isWithinTime = $this->shouldBypassWaktuForTesting() || ($jadwal->tanggal === $now->format('Y-m-d') && $now->format('H:i:s') >= $jadwal->waktu_mulai);
            if (!$isWithinTime) abort(403, 'Belum waktunya.');
            
            $hasPresensi = $this->shouldBypassPresensiForTesting() || \App\Models\Presensi::where('pendaftaran_id', $pendaftaran->id)
                ->where('jadwal_id', $penugasan->jadwal_praktikum_id)->exists();
            if (!$hasPresensi) abort(403, 'Belum presensi.');
        }

        if (!$penugasan->file_soal || !Storage::disk('public')->exists($penugasan->file_soal)) {
            return back()->with('error', 'File soal tidak ditemukan.');
        }

        $path = Storage::disk('public')->path($penugasan->file_soal);
        $fileName = $penugasan->judul . '.' . pathinfo($penugasan->file_soal, PATHINFO_EXTENSION);

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
