<?php

namespace App\Http\Controllers;

use App\Models\Praktikum;
use App\Models\Aslab;
use App\Models\Praktikan;
use App\Models\Pengumuman;
use App\Models\Kegiatan;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        // Get stats by role or table count
        $stats = [
            'praktikum' => \App\Models\Praktikum::count(),
            'aslab' => \App\Models\Aslab::count(),
            'praktikan' => \App\Models\Praktikan::count() ?: \App\Models\User::whereHas('role', function ($q) {
                $q->where('name', 'Praktikan');
            })->count(),
        ];

        // Get latest active/open praktikum
        $latestPraktikum = \App\Models\Praktikum::whereIn('status_praktikum', ['open_registration', 'on_progress'])->latest()->first();

        // Get latest activities
        $latestKegiatans = \App\Models\Kegiatan::where('is_active', true)->latest()->take(3)->get();

        return view('welcome', compact('stats', 'latestPraktikum', 'latestKegiatans'));
    }

    public function about()
    {
        return view('about');
    }

    public function praktikum()
    {
        $praktikums = \App\Models\Praktikum::latest()->get();
        return view('praktikum', compact('praktikums'));
    }

    public function organization()
    {
        $kepalaLab = config('lab-rpl.kepala_lab');

        $aslabs = \App\Models\Aslab::with('user')
            ->whereHas('user', function ($q) {
                $q->where('status', true);
            })
            ->get();

        return view('struktur-organisasi', compact('kepalaLab', 'aslabs'));
    }

    public function pengumuman()
    {
        $pengumumans = Pengumuman::with('user')
            ->where('is_active', true)
            ->latest()
            ->paginate(9);

        return view('pengumuman.index', compact('pengumumans'));
    }

    public function pengumumanDetail($slug)
    {
        $pengumuman = Pengumuman::with('user')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $recentPengumumans = Pengumuman::where('is_active', true)
            ->where('id', '!=', $pengumuman->id)
            ->latest()
            ->take(5)
            ->get();

        return view('pengumuman.show', compact('pengumuman', 'recentPengumumans'));
    }
    public function kegiatan()
    {
        $kegiatans = Kegiatan::with('user')
            ->where('is_active', true)
            ->latest()
            ->paginate(9);

        return view('kegiatan.index', compact('kegiatans'));
    }

    public function kegiatanDetail($slug)
    {
        $kegiatan = Kegiatan::with('user')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $recentKegiatans = Kegiatan::where('is_active', true)
            ->where('id', '!=', $kegiatan->id)
            ->latest()
            ->take(5)
            ->get();

        return view('kegiatan.show', compact('kegiatan', 'recentKegiatans'));
    }
}
