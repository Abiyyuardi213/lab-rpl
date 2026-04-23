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
            ->where('kode_akhir_npm', $lastDigit)
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
            $sesi = $penugasan->sesi;
            $isAccessible = false;

            $bypassWaktu = $this->shouldBypassWaktuForTesting();
            
            // For unscheduled assignments, check if student is in the correct session
            if (!$penugasan->jadwal_praktikum_id) {
                $isAccessible = $pendaftarans->where('sesi_id', $penugasan->sesi_id)
                                          ->where('praktikum_id', $penugasan->praktikum_id)
                                          ->isNotEmpty();
                $hasPresensi = $isAccessible; // Consider presence valid if they are registered for general tasks
            } else {
                // Check Presence Status (Has the student ever attended this specific module/jadwal?)
                $hasPresensi = $this->shouldBypassPresensiForTesting() || \App\Models\Presensi::whereHas('pendaftaran', function($query) use ($praktikan, $penugasan) {
                    $query->where('praktikan_id', $praktikan->id)
                          ->where('praktikum_id', $penugasan->praktikum_id);
                })->where('jadwal_id', $penugasan->jadwal_praktikum_id)
                  ->where('status', 'hadir')->exists();

                if ($hasPresensi) {
                    $isAccessible = true;
                }
            }

            $penugasan->is_accessible = $isAccessible;
            $penugasan->has_presensi = $hasPresensi;
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

        if (!$hasCustomAssignment && $penugasan->kode_akhir_npm !== null && (int)$penugasan->kode_akhir_npm !== $lastDigit) {
            abort(403, 'Soal ini tidak ditujukan untuk NPM Anda.');
        }

        // 2. Schedule Check (If the assignment is linked to a specific schedule)
        if ($penugasan->jadwal_praktikum_id) {
            $hasPresensi = $this->shouldBypassPresensiForTesting() || \App\Models\Presensi::where('pendaftaran_id', $pendaftaran->id)
                ->where('jadwal_id', $penugasan->jadwal_praktikum_id)
                ->where('status', 'hadir')
                ->exists();

            if (!$hasPresensi) {
                return redirect()->route('praktikan.penugasan.index')
                    ->with('error', 'Anda harus hadir pada jadwal praktikum terkait untuk melihat soal ini.');
            }
        } else {
            // Check Presence Status (Has the student ever attended this specific module/jadwal?)
            $hasPresensi = $this->shouldBypassPresensiForTesting() || \App\Models\Presensi::whereHas('pendaftaran', function($query) use ($praktikan, $penugasan) {
                $query->where('praktikan_id', $praktikan->id)
                      ->where('praktikum_id', $penugasan->praktikum_id);
            })->where('status', 'hadir')->exists();
        }

        // Check time access only if student hasn't attended yet
        if (!$hasPresensi) {
            $now = Carbon::now('Asia/Jakarta');
            $currentDay = $now->locale('id')->dayName;
            $currentTime = $now->format('H:i:s');
            $sesi = $penugasan->sesi;

            if (!$this->shouldBypassWaktuForTesting()) {
                if (strtolower($sesi->hari) !== strtolower($currentDay) || $currentTime < $sesi->jam_mulai || $currentTime > $sesi->jam_selesai) {
                    return redirect()->route('praktikan.penugasan.index')
                        ->with('error', 'Soal hanya dapat diakses pada jam sesi praktikum (' . $sesi->hari . ', ' . $sesi->jam_mulai . ' - ' . $sesi->jam_selesai . '). Lakukan presensi untuk membuka akses permanen.');
                }
            }

            return redirect()->route('praktikan.penugasan.index')
                ->with('error', 'Soal terkunci. Silahkan lakukan presensi QR terlebih dahulu untuk membuka akses.');
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
                ->where('status', 'hadir')
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

        if (!$hasCustomAssignment && $penugasan->kode_akhir_npm !== null && (int)$penugasan->kode_akhir_npm !== $lastDigit) {
            abort(403, 'Soal ini tidak ditujukan untuk NPM Anda.');
        }

        // Control access based on presence OR current session time
        $hasPresensiDownload = false;
        if ($penugasan->jadwal_praktikum_id) {
            $hasPresensiDownload = $this->shouldBypassPresensiForTesting() || \App\Models\Presensi::where('pendaftaran_id', $pendaftaran->id)
                ->where('jadwal_id', $penugasan->jadwal_praktikum_id)
                ->where('status', 'hadir')
                ->exists();
        } else {
            $hasPresensiDownload = $this->shouldBypassPresensiForTesting() || \App\Models\Presensi::where('pendaftaran_id', $pendaftaran->id)
                ->where('status', 'hadir')
                ->exists();
        }

        if (!$hasPresensiDownload) {
            $now = Carbon::now('Asia/Jakarta');
            $currentDay = $now->locale('id')->dayName;
            $currentTime = $now->format('H:i:s');
            $sesi = $penugasan->sesi;

            if (!$this->shouldBypassWaktuForTesting()) {
                if (strtolower($sesi->hari) !== strtolower($currentDay) || $currentTime < $sesi->jam_mulai || $currentTime > $sesi->jam_selesai) {
                    return back()->with('error', 'File hanya dapat diunduh pada jam sesi praktikum. Lakukan presensi untuk akses permanen.');
                }
            }

            return back()->with('error', 'Gagal mengunduh. Anda belum tercatat hadir di sesi ini.');
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
