<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalPraktikum;
use App\Models\PendaftaranPraktikum;
use App\Models\TugasAsistensi;
use App\Models\Presensi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PresensiController extends Controller
{
    public function generateQR($jadwal_id)
    {
        $user = Auth::user();
        $praktikan = $user->praktikan;

        if (!$praktikan) {
            return back()->with('error', 'Hanya praktikan yang dapat generate QR.');
        }

        $jadwal = JadwalPraktikum::with('praktikum')->findOrFail($jadwal_id);

        // Find pendaftaran for this praktikum
        $pendaftaran = PendaftaranPraktikum::where('praktikan_id', $praktikan->id)
            ->where('praktikum_id', $jadwal->praktikum_id)
            ->where('status', 'verified')
            ->first();

        if (!$pendaftaran) {
            return back()->with('error', 'Anda tidak terdaftar di praktikum ini atau pendaftaran belum terverifikasi.');
        }

        // Check already present
        $alreadyPresent = Presensi::where('jadwal_id', $jadwal->id)
            ->where('pendaftaran_id', $pendaftaran->id)
            ->exists();

        if ($alreadyPresent) {
            return back()->with('info', 'Anda sudah melakukan presensi untuk jadwal ini.');
        }

        // --- RULE CHECK: Asistensi Above Modul 1 ---
        $modulNumber = $this->parseModulNumber($jadwal->judul_modul);

        if ($modulNumber > 1) {
            // Check if ALL previous modules have at least one reviewed assistance
            // Simplified: Check if there is a 'reviewed' task for (modulNumber - 1)
            // Or just check if total 'reviewed' tasks >= ($modulNumber - 1)

            $reviewedTasksCount = TugasAsistensi::where('pendaftaran_id', $pendaftaran->id)
                ->where('status', 'reviewed')
                ->count();

            // This is a heuristic. In a more robust system, we'd check specifically per module.
            // But usually, tasks are sequential.
            if ($reviewedTasksCount < ($modulNumber - 1)) {
                return back()->with('error', 'Anda belum menyelesaikan asistensi untuk modul sebelumnya. Silakan selesaikan asistensi terlebih dahulu.');
            }
        }

        // Generate a short, unique token for the QR code
        $token = Str::random(32);
        
        // Store the attendance data in cache for 30 minutes
        Cache::put("presensi_token_{$token}", [
            'pendaftaran_id' => $pendaftaran->id,
            'jadwal_id' => $jadwal->id,
            'user_id' => $user->id,
        ], now()->addMinutes(30));

        // Use a full URL for the QR code so it's informative when scanned by generic apps
        $qrUrl = route('home') . '/p/' . $token;

        // Generate the SVG QR code using the new library
        // We use a lower correction level (L) and larger size to make it scan faster
        $qrCode = QrCode::size(300)
            ->gradient(15, 23, 42, 30, 64, 175, 'diagonal') // slate-900 to a blue-ish
            ->margin(1)
            ->errorCorrection('M')
            ->generate($qrUrl);

        return view('praktikan.presensi.qr', compact('qrCode', 'qrUrl', 'jadwal', 'pendaftaran', 'token'));
    }

    public function scan()
    {
        return view('aslab.presensi.scan');
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'token' => 'required'
        ]);

        try {
            // Check if it's a full URL and extract the token
            $token = $request->token;
            if (filter_var($token, FILTER_VALIDATE_URL)) {
                $path = parse_url($token, PHP_URL_PATH);
                $segments = explode('/', trim($path, '/'));
                $token = end($segments);
            }

            // Retrieve data from cache
            $data = Cache::get("presensi_token_{$token}");
            
            if (!$data) {
                // Try fallback to encrypted token for backward compatibility during transition if needed
                // But since we are changing it now, we can just return error
                try {
                    $json = decrypt($request->token);
                    $data = json_decode($json, true);
                    if ($data['expires'] < now()->timestamp) {
                        return response()->json(['success' => false, 'message' => 'QR Code sudah kadaluarsa.'], 400);
                    }
                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'message' => 'QR Code tidak valid atau sudah kadaluarsa.'], 400);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat validasi QR.'], 400);
        }

        $pendaftaran = PendaftaranPraktikum::with('sesi')->findOrFail($data['pendaftaran_id']);
        $jadwal = JadwalPraktikum::findOrFail($data['jadwal_id']);

        // Final validation 
        $alreadyPresent = Presensi::where('jadwal_id', $jadwal->id)
            ->where('pendaftaran_id', $pendaftaran->id)
            ->exists();

        if ($alreadyPresent) {
            return response()->json(['success' => false, 'message' => 'Praktikan sudah melakukan presensi.'], 400);
        }

        // Check-in logic
        $status = 'hadir';
        $waktuMulai = \Carbon\Carbon::parse($jadwal->tanggal . ' ' . $jadwal->waktu_mulai);
        if (now()->gt($waktuMulai->addMinutes(15))) {
            $status = 'terlambat';
        }

        Presensi::create([
            'jadwal_id' => $jadwal->id,
            'pendaftaran_id' => $pendaftaran->id,
            'jam_masuk' => now(),
            'status' => $status
        ]);

        $sesiInfo = $pendaftaran->sesi ? 
            "{$pendaftaran->sesi->nama_sesi} ({$pendaftaran->sesi->hari}, {$pendaftaran->sesi->jam_mulai}-{$pendaftaran->sesi->jam_selesai})" : 
            "Tidak Terdaftar Sesi";

        return response()->json([
            'success' => true,
            'message' => 'Presensi berhasil: ' . $pendaftaran->praktikan->user->name,
            'nama' => $pendaftaran->praktikan->user->name,
            'sesi' => $sesiInfo,
            'status' => $status
        ]);
    }

    public function checkStatus($jadwal_id)
    {
        $user = Auth::user();
        $praktikan = $user->praktikan;

        if (!$praktikan) {
            return response()->json(['present' => false]);
        }

        $jadwal = JadwalPraktikum::findOrFail($jadwal_id);

        $pendaftaran = PendaftaranPraktikum::where('praktikan_id', $praktikan->id)
            ->where('praktikum_id', $jadwal->praktikum_id)
            ->where('status', 'verified')
            ->first();

        if (!$pendaftaran) {
            return response()->json(['present' => false]);
        }

        $present = Presensi::where('jadwal_id', $jadwal->id)
            ->where('pendaftaran_id', $pendaftaran->id)
            ->exists();

        return response()->json(['present' => $present]);
    }

    public function publicVerify($token)
    {
        $data = Cache::get("presensi_token_{$token}");

        if (!$data) {
            return view('presensi.public-verify', [
                'status' => 'expired',
                'message' => 'QR Code ini sudah tidak berlaku atau sudah kadaluarsa.'
            ]);
        }

        $pendaftaran = PendaftaranPraktikum::with(['praktikan.user', 'praktikum'])->find($data['pendaftaran_id']);

        return view('presensi.public-verify', [
            'status' => 'valid',
            'message' => 'QR Code Valid',
            'nama' => $pendaftaran->praktikan->user->name,
            'praktikum' => $pendaftaran->praktikum->nama_praktikum
        ]);
    }

    private function parseModulNumber($judul)
    {
        // Extract number from "Modul X"
        if (preg_match('/Modul\s+(\d+)/i', $judul, $matches)) {
            return (int)$matches[1];
        }
        return 1; // Default
    }
}
