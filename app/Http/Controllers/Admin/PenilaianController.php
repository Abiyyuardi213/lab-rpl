<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalPraktikum;
use App\Models\Presensi;
use App\Models\PenilaianPraktikum;
use App\Models\Praktikum;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasActivityLog;

class PenilaianController extends Controller
{
    use HasActivityLog;

    public function index()
    {
        // Admin can see all mata praktikum and their sessions
        $praktikums = Praktikum::withCount(['jadwals', 'pendaftarans' => function($q) {
            $q->where('status', 'verified');
        }])->get();
        
        return view('admin.penilaian.index', compact('praktikums'));
    }

    public function showPraktikum($praktikum_id)
    {
        $praktikum = Praktikum::with(['jadwals' => function($q) {
            $q->orderBy('tanggal', 'desc')->orderBy('waktu_mulai', 'desc')->with(['sesi', 'presensis' => function($pq) {
                $pq->where('status', 'hadir');
            }]);
        }])->findOrFail($praktikum_id);

        return view('admin.penilaian.show_praktikum', compact('praktikum'));
    }

    public function showJadwal($jadwal_id)
    {
        $jadwal = JadwalPraktikum::with(['praktikum', 'sesi'])->findOrFail($jadwal_id);
        
        // Admin can see students who had presence record.
        // User asked for "penilaian berdasarkan presensi" 
        // We show all presence records for this schedule (Hadir, Izin, Sakit, Alpa) because Admin might want to grade them after the fact.
        $presensis = Presensi::with(['pendaftaran.praktikan.user', 'penilaian'])
            ->where('jadwal_id', $jadwal_id)
            ->get();

        return view('admin.penilaian.show_jadwal', compact('jadwal', 'presensis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'presensi_id' => 'required|exists:presensis,id',
            'nilai' => 'required|integer|min:0|max:100',
            'catatan' => 'nullable|string',
        ]);

        $presensi = Presensi::with(['jadwal', 'pendaftaran.praktikan'])->findOrFail($request->presensi_id);

        // Admin has NO time limit or status limit as per user request ("bebas akses waktu")
        
        PenilaianPraktikum::updateOrCreate(
            ['presensi_id' => $request->presensi_id],
            [
                'aslab_id' => null, // Graded by Admin
                'nilai' => $request->nilai,
                'catatan' => $request->catatan,
            ]
        );

        $this->logActivity(
            'Penilaian Admin Praktikum',
            'Admin memberikan nilai ' . $request->nilai . ' kepada ' . $presensi->pendaftaran->praktikan->user->name . ' (Modul: ' . $presensi->jadwal->judul_modul . ')',
            ['presensi_id' => $request->presensi_id, 'nilai' => $request->nilai]
        );

        return back()->with('success', 'Nilai berhasil disimpan untuk ' . $presensi->pendaftaran->praktikan->user->name);
    }
}
