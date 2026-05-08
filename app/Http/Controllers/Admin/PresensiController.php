<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Services\PresensiAlfaService;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        app(PresensiAlfaService::class)->markFinishedSchedules(
            $request->filled('praktikum_id') ? (int) $request->praktikum_id : null
        );

        $query = Presensi::with(['pendaftaran.praktikan.user', 'pendaftaran.praktikum', 'pendaftaran.sesi', 'jadwal']);

        // Filtering logic
        if ($request->filled('praktikum_id')) {
            $query->whereHas('pendaftaran', function ($q) use ($request) {
                $q->where('praktikum_id', $request->praktikum_id);
            });
        }

        if ($request->filled('sesi_id')) {
            $query->whereHas('pendaftaran', function ($q) use ($request) {
                $q->where('sesi_id', $request->sesi_id);
            });
        }

        if ($request->filled('jadwal_id')) {
            $query->where('jadwal_id', $request->jadwal_id);
        }

        $presensis = $query->latest()->paginate(25)->withQueryString();

        // Data for filters
        $praktikums = \App\Models\Praktikum::orderBy('nama_praktikum')->get();
        
        $sesis = collect();
        $jadwals = collect();

        if ($request->filled('praktikum_id')) {
            $sesis = \App\Models\SesiPraktikum::where('praktikum_id', $request->praktikum_id)->get();
            $jadwals = \App\Models\JadwalPraktikum::where('praktikum_id', $request->praktikum_id)->get();
        }

        return view('admin.presensi.index', compact('presensis', 'praktikums', 'sesis', 'jadwals'));
    }

    public function destroy($id)
    {
        $presensi = Presensi::findOrFail($id);
        $presensi->delete();

        return back()->with('success', 'Data presensi berhasil dihapus.');
    }
}
