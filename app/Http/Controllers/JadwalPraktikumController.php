<?php

namespace App\Http\Controllers;

use App\Models\JadwalPraktikum;
use App\Models\Praktikum;
use Illuminate\Http\Request;

class JadwalPraktikumController extends Controller
{
    public function index()
    {
        $jadwals = JadwalPraktikum::with('praktikum')
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu_mulai', 'desc')
            ->get();
        $praktikums = Praktikum::where('status_praktikum', '!=', 'finished')->get();

        return view('admin.jadwal_praktikum.index', compact('jadwals', 'praktikums'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'praktikum_id' => 'required|exists:praktikums,id',
            'judul_modul' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required|after:waktu_mulai',
            'ruangan' => 'nullable|string|max:255',
        ]);

        JadwalPraktikum::create($request->all());

        return back()->with('success', 'Jadwal praktikum berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $jadwal = JadwalPraktikum::findOrFail($id);

        $request->validate([
            'praktikum_id' => 'required|exists:praktikums,id',
            'judul_modul' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required|after:waktu_mulai',
            'ruangan' => 'nullable|string|max:255',
        ]);

        $jadwal->update($request->all());

        return back()->with('success', 'Jadwal praktikum berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jadwal = JadwalPraktikum::findOrFail($id);
        $jadwal->delete();

        return back()->with('success', 'Jadwal praktikum berhasil dihapus.');
    }
}
