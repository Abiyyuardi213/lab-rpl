<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalPraktikum;
use App\Models\PendaftaranPraktikum;
use App\Models\PenilaianAkhir;
use App\Models\PenilaianPraktikum;
use App\Models\Praktikum;
use App\Models\Presensi;
use App\Traits\HasActivityLog;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    use HasActivityLog;

    public function index()
    {
        // Admin can see all mata praktikum and their sessions
        $praktikums = Praktikum::withCount(['jadwals', 'pendaftarans' => function ($q) {
            $q->where('status', 'verified');
        }])->get();

        return view('admin.penilaian.index', compact('praktikums'));
    }

    public function showPraktikum($praktikum_id)
    {
        $praktikum = Praktikum::with([
            'sesis' => function ($q) {
                $q->orderBy('hari')->orderBy('jam_mulai')->withCount([
                    'pendaftarans' => function ($pq) {
                        $pq->where('status', 'verified');
                    },
                ]);
            },
            'jadwals' => function ($q) {
                $q->orderBy('tanggal', 'asc')
                    ->orderBy('waktu_mulai', 'asc')
                    ->with([
                        'sesi',
                        'presensis.penilaian',
                    ]);
            },
        ])->withCount([
            'pendaftarans' => function ($q) {
                $q->where('status', 'verified');
            },
        ])->findOrFail($praktikum_id);

        $jadwalsBySesi = $praktikum->jadwals->groupBy(function ($j) {
            return $j->sesi_id ?: 'umum';
        });

        return view('admin.penilaian.show_praktikum', compact('praktikum', 'jadwalsBySesi'));
    }

    public function showJadwal($jadwal_id)
    {
        $jadwal = JadwalPraktikum::with(['praktikum.sesis', 'sesi'])->findOrFail($jadwal_id);

        // Ambil seluruh mahasiswa terdaftar di praktikum & sesi ini
        $query = PendaftaranPraktikum::with([
            'praktikan.user',
            'sesi',
            'presensis' => function ($q) use ($jadwal_id) {
                $q->where('jadwal_id', $jadwal_id)->with('penilaian');
            },
        ])
            ->where('praktikum_id', $jadwal->praktikum_id)
            ->where('status', 'verified');

        if ($jadwal->sesi_id) {
            $query->where('sesi_id', $jadwal->sesi_id);
        }

        $pendaftarans = $query->get()->sortBy(function ($p) {
            return $p->praktikan->npm ?? '';
        });

        return view('admin.penilaian.show_jadwal', compact('jadwal', 'pendaftarans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'presensi_id' => 'nullable|exists:presensis,id',
            'pendaftaran_id' => 'required_without:presensi_id|exists:pendaftaran_praktikums,id',
            'jadwal_id' => 'required_without:presensi_id|exists:jadwal_praktikums,id',
            'nilai' => 'required|integer|min:0|max:100',
            'catatan' => 'nullable|string',
        ]);

        $presensi = null;
        if ($request->filled('presensi_id')) {
            $presensi = Presensi::with(['jadwal', 'pendaftaran.praktikan.user'])->findOrFail($request->presensi_id);
        } elseif ($request->filled('pendaftaran_id') && $request->filled('jadwal_id')) {
            $presensi = Presensi::firstOrCreate(
                [
                    'jadwal_id' => $request->jadwal_id,
                    'pendaftaran_id' => $request->pendaftaran_id,
                ],
                [
                    'status' => 'hadir',
                    'jam_masuk' => now(),
                ]
            );
            $presensi->load(['jadwal', 'pendaftaran.praktikan.user']);
        }

        // Admin has NO time limit or status limit as per user request ("bebas akses waktu")
        PenilaianPraktikum::updateOrCreate(
            ['presensi_id' => $presensi->id],
            [
                'aslab_id' => null, // Graded by Admin
                'nilai' => $request->nilai,
                'catatan' => $request->catatan,
            ]
        );

        // Auto-sync PenilaianAkhir if record exists for this registration
        if ($presensi->pendaftaran) {
            PenilaianAkhir::syncRegistration($presensi->pendaftaran);
        }

        $studentName = $presensi->pendaftaran?->praktikan?->user?->name ?? 'Mahasiswa';
        $modulTitle = $presensi->jadwal?->judul_modul ?? 'Modul';

        $this->logActivity(
            'Penilaian Admin Praktikum',
            'Admin memberikan nilai '.$request->nilai.' kepada '.$studentName.' (Modul: '.$modulTitle.')',
            ['presensi_id' => $presensi->id, 'nilai' => $request->nilai]
        );

        return back()->with('success', 'Nilai berhasil disimpan untuk '.$studentName);
    }
}
