<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalPraktikum;
use App\Models\PendaftaranPraktikum;
use App\Models\TugasAsistensi;
use App\Models\Presensi;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode; // We might need to install this or use JS

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

        // Generate QR Data
        $data = [
            'pendaftaran_id' => $pendaftaran->id,
            'jadwal_id' => $jadwal->id,
            'expires' => now()->addMinutes(30)->timestamp,
        ];

        $token = encrypt(json_encode($data));

        return view('praktikan.presensi.qr', compact('token', 'jadwal', 'pendaftaran'));
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
            $json = decrypt($request->token);
            $data = json_decode($json, true);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'QR Code tidak valid atau sudah kadaluarsa.'], 400);
        }

        if ($data['expires'] < now()->timestamp) {
            return response()->json(['success' => false, 'message' => 'QR Code sudah kadaluarsa.'], 400);
        }

        $pendaftaran = PendaftaranPraktikum::findOrFail($data['pendaftaran_id']);
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

        return response()->json([
            'success' => true,
            'message' => 'Presensi berhasil: ' . $pendaftaran->praktikan->user->name,
            'nama' => $pendaftaran->praktikan->user->name,
            'status' => $status
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
