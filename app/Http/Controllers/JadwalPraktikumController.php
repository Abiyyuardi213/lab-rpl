<?php

namespace App\Http\Controllers;

use App\Models\JadwalPraktikum;
use App\Models\Praktikum;
use Illuminate\Http\Request;

class JadwalPraktikumController extends Controller
{
    public function index(Request $request)
    {
        $selectedPraktikum = $request->get('praktikum_id');

        $query = JadwalPraktikum::with(['praktikum', 'sesi'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu_mulai', 'desc');

        if ($selectedPraktikum) {
            $query->where('praktikum_id', $selectedPraktikum);
        }

        $jadwals = $query->get();
        $praktikums = Praktikum::orderBy('nama_praktikum', 'asc')->get();
        $sesis = \App\Models\SesiPraktikum::orderBy('nama_sesi')->get();

        return view('admin.jadwal_praktikum.index', compact('jadwals', 'praktikums', 'sesis', 'selectedPraktikum'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'praktikum_id' => 'required|exists:praktikums,id',
            'sesi_id' => 'nullable|exists:sesi_praktikums,id',
            'judul_modul' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required|after:waktu_mulai',
            'ruangan' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        $data['token'] = (string) \Illuminate\Support\Str::random(32);
        JadwalPraktikum::create($data);

        $jTitle = strtolower($request->judul_modul);
        if (str_contains($jTitle, 'tugas akhir') || str_contains($jTitle, 'ta ') || str_contains($jTitle, 'akhir')) {
            $praktikum = Praktikum::find($request->praktikum_id);
            if ($praktikum && !$praktikum->ada_tugas_akhir) {
                $praktikum->update(['ada_tugas_akhir' => true]);
            }
        }

        return back()->with('success', 'Jadwal praktikum berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $jadwal = JadwalPraktikum::findOrFail($id);

        $request->validate([
            'praktikum_id' => 'required|exists:praktikums,id',
            'sesi_id' => 'nullable|exists:sesi_praktikums,id',
            'judul_modul' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required|after:waktu_mulai',
            'ruangan' => 'nullable|string|max:255',
        ]);

        $jadwal->update($request->all());

        $jTitle = strtolower($request->judul_modul);
        if (str_contains($jTitle, 'tugas akhir') || str_contains($jTitle, 'ta ') || str_contains($jTitle, 'akhir')) {
            $praktikum = Praktikum::find($request->praktikum_id);
            if ($praktikum && !$praktikum->ada_tugas_akhir) {
                $praktikum->update(['ada_tugas_akhir' => true]);
            }
        }

        return back()->with('success', 'Jadwal praktikum berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jadwal = JadwalPraktikum::findOrFail($id);
        $jadwal->delete();

        return back()->with('success', 'Jadwal praktikum berhasil dihapus.');
    }
}
