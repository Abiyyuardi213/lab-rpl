<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SesiPraktikum;

class SesiPraktikumController extends Controller
{
    public function store(Request $request, $praktikum_id)
    {
        $request->validate([
            'nama_sesi' => 'required|string|max:255',
            'dosen_pengampu' => 'required|string|max:255',
            'asal_kelas_mata_kuliah' => 'required|string|max:255',
            'hari' => 'required|string',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'kuota' => 'required|integer|min:1',
        ]);

        SesiPraktikum::create([
            'praktikum_id' => $praktikum_id,
            'nama_sesi' => $request->nama_sesi,
            'dosen_pengampu' => $request->dosen_pengampu,
            'asal_kelas_mata_kuliah' => $request->asal_kelas_mata_kuliah,
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'kuota' => $request->kuota,
        ]);

        return redirect()->back()->with('success', 'Sesi praktikum berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $sesi = SesiPraktikum::findOrFail($id);
        $sesi->delete();

        return redirect()->back()->with('success', 'Sesi praktikum berhasil dihapus.');
    }
}
