<?php

namespace App\Http\Controllers\Aslab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalPraktikum;
use App\Models\Presensi;
use App\Models\PenilaianPraktikum;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Traits\HasActivityLog;

class PenilaianController extends Controller
{
    use HasActivityLog;

    public function index()
    {
        $now = Carbon::now('Asia/Jakarta');
        $today = $now->toDateString();

        // Get schedules for today
        $jadwals = JadwalPraktikum::with(['praktikum', 'sesi', 'presensis' => function($q) {
            $q->where('status', 'hadir');
        }])
            ->where('tanggal', $today)
            ->get();

        return view('aslab.penilaian.index', compact('jadwals'));
    }

    public function show($jadwal_id)
    {
        $jadwal = JadwalPraktikum::with(['praktikum', 'sesi'])->findOrFail($jadwal_id);
        
        // Only show students who have checked in (status = hadir)
        $presensis = Presensi::with(['pendaftaran.praktikan', 'penilaian'])
            ->where('jadwal_id', $jadwal_id)
            ->where('status', 'hadir')
            ->get();

        return view('aslab.penilaian.show', compact('jadwal', 'presensis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'presensi_id' => 'required|exists:presensis,id',
            'nilai' => 'required|integer|min:0|max:100',
            'catatan' => 'nullable|string',
        ]);

        $user = Auth::user();
        $aslab = $user->aslab;
        
        if (!$aslab) {
            return back()->with('error', 'Anda bukan aslab yang terdaftar.');
        }

        $presensi = Presensi::with('jadwal', 'pendaftaran.praktikan')->findOrFail($request->presensi_id);

        // Security Check 1: Status must be 'hadir'
        if ($presensi->status !== 'hadir') {
            return back()->with('error', 'Kecurangan terdeteksi: Praktikan belum tercatat hadir.');
        }

        // Security Check 2: Schedule must be today (prevent grading past/future sessions unless authorized)
        $today = Carbon::now('Asia/Jakarta')->toDateString();
        if ($presensi->jadwal->tanggal !== $today) {
             return back()->with('error', 'Penilaian hanya dapat dilakukan pada hari jadwal praktikum berlangsung.');
        }

        // Security Check 3: Check-in time (ensure they actually checked in during the session)
        // (Optional: can be added if needed)

        PenilaianPraktikum::updateOrCreate(
            ['presensi_id' => $request->presensi_id],
            [
                'aslab_id' => $aslab->id,
                'nilai' => $request->nilai,
                'catatan' => $request->catatan,
            ]
        );

        $this->logActivity(
            'Penilaian Live Praktikum',
            'Aslab memberikan nilai ' . $request->nilai . ' kepada ' . $presensi->pendaftaran->praktikan->nama,
            ['presensi_id' => $request->presensi_id, 'nilai' => $request->nilai]
        );

        return back()->with('success', 'Nilai berhasil disimpan untuk ' . $presensi->pendaftaran->praktikan->nama);
    }
}
