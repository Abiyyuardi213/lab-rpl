<?php

namespace App\Http\Controllers\Aslab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalPraktikum;
use App\Models\Presensi;
use App\Models\PenilaianPraktikum;
use App\Models\TugasAsistensi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Traits\HasActivityLog;

class PenilaianController extends Controller
{
    use HasActivityLog;

    public function index()
    {
        $aslab = Auth::user()->aslab;
        if (!$aslab) {
            return redirect()->back()->with('error', 'Data aslab tidak ditemukan.');
        }

        // Get all practicums assigned to this aslab
        $praktikumIds = $aslab->praktikums->pluck('id');

        // Get all schedules for these practicums
        $jadwals = JadwalPraktikum::with(['praktikum', 'sesi', 'presensis' => function($q) {
            $q->where('status', 'hadir');
        }])
            ->whereIn('praktikum_id', $praktikumIds)
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu_mulai', 'desc')
            ->get();

        return view('aslab.penilaian.index', compact('jadwals'));
    }

    public function show($jadwal_id)
    {
        $jadwal = JadwalPraktikum::with(['praktikum', 'sesi'])->findOrFail($jadwal_id);
        
        // Only show students who have checked in (status = hadir)
        // Load pendaftaran.praktikan.user to show names
        $presensis = Presensi::with(['pendaftaran.praktikan.user', 'pendaftaran.tugasAsistensis', 'penilaian'])
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
            'nilai_asistensi' => 'nullable|integer|min:0|max:100',
            'catatan' => 'nullable|string',
        ]);

        $user = Auth::user();
        $aslab = $user->aslab;
        
        if (!$aslab) {
            return back()->with('error', 'Anda bukan aslab yang terdaftar.');
        }

        $presensi = Presensi::with(['jadwal', 'pendaftaran.praktikan'])->findOrFail($request->presensi_id);
        $pendaftaran = $presensi->pendaftaran;
        $judulModul = $presensi->jadwal->judul_modul;

        // Security Check 1: Status must be 'hadir'
        if ($presensi->status !== 'hadir') {
            return back()->with('error', 'Kecurangan terdeteksi: Praktikan belum tercatat hadir.');
        }

        // --- REMOVED: Restriction that grading must be today ---
        // Penilaian asistensi & praktikum sekarang dapat diakses kapan saja setelah praktikum selesai/berlangsung.

        // 1. Save Nilai Praktikum (Live)
        PenilaianPraktikum::updateOrCreate(
            ['presensi_id' => $request->presensi_id],
            [
                'aslab_id' => $aslab->id,
                'nilai' => $request->nilai,
                'catatan' => $request->catatan,
            ]
        );

        // 2. Save Nilai Asistensi to TugasAsistensi table (Live Assessment)
        if ($request->filled('nilai_asistensi')) {
            TugasAsistensi::updateOrCreate(
                [
                    'pendaftaran_id' => $pendaftaran->id,
                    'judul' => $judulModul
                ],
                [
                    'aslab_id' => $aslab->id,
                    'nilai' => $request->nilai_asistensi,
                    'status' => 'reviewed',
                    'deskripsi' => 'Penilaian asistensi langsung (Live Assessment)'
                ]
            );
        }

        $this->logActivity(
            'Penilaian Live Praktikum',
            'Aslab memberikan nilai praktikum: ' . $request->nilai . ' & asistensi: ' . ($request->nilai_asistensi ?? '0') . ' kepada ' . $presensi->pendaftaran->praktikan->nama,
            ['presensi_id' => $request->presensi_id, 'nilai' => $request->nilai, 'nilai_asistensi' => $request->nilai_asistensi]
        );

        return back()->with('success', 'Nilai berhasil disimpan untuk ' . $presensi->pendaftaran->praktikan->nama);
    }
}
