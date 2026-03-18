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

        $pendaftarans = PendaftaranPraktikum::with(['praktikum', 'sesi'])
            ->where('praktikan_id', $praktikan->id)
            ->where('status', 'verified')
            ->get();

        $sesiIds = $pendaftarans->pluck('sesi_id')->toArray();
        $npm = $praktikan->npm;
        $lastDigit = intval(substr($npm, -1));

        $penugasans = Penugasan::with(['praktikum', 'sesi', 'aslab.user'])
            ->whereIn('sesi_id', $sesiIds)
            ->where('kode_akhir_npm', $lastDigit)
            ->orderBy('created_at', 'desc')
            ->get();

        $now = Carbon::now('Asia/Jakarta');
        $currentDay = $now->locale('id')->dayName;
        $currentTime = $now->format('H:i:s');

        foreach ($penugasans as $penugasan) {
            $sesi = $penugasan->sesi;
            $isAccessible = false;

            $dayMatch = (strtolower($sesi->hari) === strtolower($currentDay));
            $timeMatch = ($currentTime >= $sesi->jam_mulai && $currentTime <= $sesi->jam_selesai);

            if ($dayMatch && $timeMatch) {
                $isAccessible = true;
            }

            $penugasan->is_accessible = $isAccessible;
        }

        return view('praktikan.penugasan.index', compact('penugasans'));
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
        $isRegistered = PendaftaranPraktikum::where('praktikan_id', $praktikan->id)
            ->where('sesi_id', $penugasan->sesi_id)
            ->where('status', 'verified')
            ->exists();

        if (!$isRegistered) {
            abort(403, 'Anda tidak terdaftar dalam sesi praktikum ini.');
        }

        $npm = $praktikan->npm;
        $lastDigit = intval(substr($npm, -1));

        if ($penugasan->kode_akhir_npm !== null && (int)$penugasan->kode_akhir_npm !== $lastDigit) {
            abort(403, 'Soal ini tidak ditujukan untuk NPM Anda.');
        }

        // Check time access
        $now = Carbon::now('Asia/Jakarta');
        $currentDay = $now->locale('id')->dayName;
        $currentTime = $now->format('H:i:s');

        $sesi = $penugasan->sesi;
        if (strtolower($sesi->hari) !== strtolower($currentDay) || $currentTime < $sesi->jam_mulai || $currentTime > $sesi->jam_selesai) {
            return redirect()->route('praktikan.penugasan.index')
                ->with('error', 'Soal hanya dapat diakses pada jam sesi praktikum (' . $sesi->hari . ', ' . $sesi->jam_mulai . ' - ' . $sesi->jam_selesai . ').');
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
        $isRegistered = PendaftaranPraktikum::where('praktikan_id', $praktikan->id)
            ->where('sesi_id', $penugasan->sesi_id)
            ->where('status', 'verified')
            ->exists();

        if (!$isRegistered) {
            abort(403, 'Anda tidak terdaftar dalam sesi praktikum ini.');
        }

        // 2. Check NPM Digit
        $npm = $praktikan->npm;
        $lastDigit = intval(substr($npm, -1));
        if ($penugasan->kode_akhir_npm !== null && (int)$penugasan->kode_akhir_npm !== $lastDigit) {
            abort(403, 'Soal ini tidak ditujukan untuk NPM Anda.');
        }

        // 3. Check time access
        $now = Carbon::now('Asia/Jakarta');
        $currentDay = $now->locale('id')->dayName;
        $currentTime = $now->format('H:i:s');
        $sesi = $penugasan->sesi;

        if (strtolower($sesi->hari) !== strtolower($currentDay) || $currentTime < $sesi->jam_mulai || $currentTime > $sesi->jam_selesai) {
            return back()->with('error', 'File hanya dapat diunduh pada jam sesi praktikum.');
        }

        // 4. Check file existence
        if (!$penugasan->file_soal || !Storage::disk('public')->exists($penugasan->file_soal)) {
            return back()->with('error', 'File soal tidak ditemukan di server.');
        }

        $path = Storage::disk('public')->path($penugasan->file_soal);
        $fileName = $penugasan->judul . '.' . pathinfo($penugasan->file_soal, PATHINFO_EXTENSION);

        return response()->download($path, $fileName);
    }


}
