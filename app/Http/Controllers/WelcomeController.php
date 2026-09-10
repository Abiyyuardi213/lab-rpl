<?php

namespace App\Http\Controllers;

use App\Models\Praktikum;
use App\Models\Aslab;
use App\Models\Praktikan;
use App\Models\Pengumuman;
use App\Models\Kegiatan;
use App\Models\PendaftaranPraktikum;
use App\Models\User;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        // Get stats by role or table count
        $stats = [
            'praktikum' => Praktikum::count(),
            'aslab' => Aslab::count(),
            'praktikan' => Praktikan::count() ?: User::whereHas('role', function ($q) {
                $q->where('name', 'Praktikan');
            })->count(),
        ];

        // Get latest active/open praktikum
        $latestPraktikum = Praktikum::whereIn('status_praktikum', ['open_registration', 'on_progress'])->latest()->first();

        // Get latest activities
        $latestKegiatans = Kegiatan::where('is_active', true)->latest()->take(3)->get();

        return view('welcome', compact('stats', 'latestPraktikum', 'latestKegiatans'));
    }

    public function liveMode(Request $request)
    {
        $selectedYear = $request->get('year', date('Y'));
        $years = PendaftaranPraktikum::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (!in_array(date('Y'), $years)) {
            array_unshift($years, (int)date('Y'));
        }

        $monthlyCounts = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $runningTotal = 0;
        $currentMonth = (int)date('n');
        $isCurrentYear = ($selectedYear == date('Y'));

        for ($m = 1; $m <= 12; $m++) {
            if ($isCurrentYear && $m > $currentMonth) {
                $monthlyCounts[] = null;
            } else {
                $monthCount = PendaftaranPraktikum::whereYear('created_at', $selectedYear)
                    ->whereMonth('created_at', $m)
                    ->count();
                $runningTotal += $monthCount;
                $monthlyCounts[] = $runningTotal;
            }
        }

        $totalRegistered = PendaftaranPraktikum::whereYear('created_at', $selectedYear)->count();
        $verifiedCount = PendaftaranPraktikum::whereYear('created_at', $selectedYear)->where('status', 'verified')->count();
        $pendingCount = PendaftaranPraktikum::whereYear('created_at', $selectedYear)->where('status', 'pending')->count();
        $rejectedCount = PendaftaranPraktikum::whereYear('created_at', $selectedYear)->where('status', 'rejected')->count();

        $liveActivities = PendaftaranPraktikum::with(['praktikan.user', 'praktikum'])
            ->latest()
            ->take(10)
            ->get();

        $praktikumStats = Praktikum::withCount(['pendaftarans' => function ($q) use ($selectedYear) {
            $q->whereYear('created_at', $selectedYear);
        }])->get()->map(function ($p) use ($totalRegistered) {
            $percentage = $totalRegistered > 0 ? round(($p->pendaftarans_count / $totalRegistered) * 100, 1) : 0;
            return (object)[
                'nama' => $p->nama_praktikum,
                'count' => $p->pendaftarans_count,
                'percentage' => $percentage
            ];
        });

        // Rekap Praktikum dari Masa ke Masa
        $rekapPraktikums = Praktikum::withCount([
            'pendaftarans as total_peserta',
            'pendaftarans as total_lulus' => function ($q) {
                $q->where('status', 'verified')->whereHas('penilaianAkhir', function ($sq) {
                    $sq->where('status_kelulusan', 'LULUS');
                });
            }
        ])
        ->orderBy('created_at', 'desc')
        ->get();

        // Daftar Seluruh Praktikan Terdaftar pada Semua Praktikum
        $registeredPraktikans = PendaftaranPraktikum::with(['praktikan.user', 'praktikum'])
            ->latest()
            ->get();

        return view('live-mode-public', compact(
            'selectedYear',
            'years',
            'months',
            'monthlyCounts',
            'totalRegistered',
            'verifiedCount',
            'pendingCount',
            'rejectedCount',
            'liveActivities',
            'praktikumStats',
            'rekapPraktikums',
            'registeredPraktikans'
        ));
    }

    public function about()
    {
        return view('about');
    }

    public function praktikum()
    {
        $praktikums = Praktikum::latest()->get();
        return view('praktikum', compact('praktikums'));
    }

    public function aslab()
    {
        $allAslabs = Aslab::with('user')
            ->whereHas('user', function ($q) {
                $q->where('status', true);
            })
            ->get();

        $hierarchy = [
            'Koordinator Laboratorium',
            'Kepala Project Laboratorium',
            'Laboran',
            'Asisten Laboran',
            'Tim Kajian Bidang',
            'PUK Pembangunan Berkelanjutan (SDGs)',
            'PUK Teknologi Tepat Guna',
            'PUI Industri dan Digital',
            'Rekayasa Perangkat Lunak',
            'Game Edukasi',
            'Web dan Mobile Programming',
            'Koordinator Praktikum Pemrograman Terstruktur',
            'Koordinator Praktikum Struktur Data',
            'Koordinator Praktikum Basis Data',
            'Sekretaris',
            'Bendahara',
            'Admin',
            'Anggota',
            'Anggota Laboratorium'
        ];

        $aslabs = $allAslabs->sortBy(function ($aslab) use ($hierarchy) {
            $index = array_search($aslab->jabatan, $hierarchy);
            return $index === false ? 99 : $index;
        });

        return view('aslab-public', compact('aslabs'));
    }

    public function organization()
    {
        return view('struktur-organisasi');
    }

    public function pengumuman()
    {
        $pengumumans = Pengumuman::where('is_active', true)->latest()->paginate(9);
        return view('pengumuman.index', compact('pengumumans'));
    }

    public function pengumumanDetail($slug)
    {
        $pengumuman = Pengumuman::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $recentPengumumans = Pengumuman::where('is_active', true)
            ->where('id', '!=', $pengumuman->id)
            ->latest()
            ->take(4)
            ->get();

        return view('pengumuman.show', compact('pengumuman', 'recentPengumumans'));
    }

    public function kegiatan()
    {
        $kegiatans = Kegiatan::where('is_active', true)->latest()->paginate(9);
        return view('kegiatan.index', compact('kegiatans'));
    }

    public function kegiatanDetail($slug)
    {
        $kegiatan = Kegiatan::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $recentKegiatans = Kegiatan::where('is_active', true)
            ->where('id', '!=', $kegiatan->id)
            ->latest()
            ->take(4)
            ->get();

        return view('kegiatan.show', compact('kegiatan', 'recentKegiatans'));
    }
}
