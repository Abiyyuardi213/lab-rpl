<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Penugasan;
use App\Models\Praktikum;
use App\Models\SesiPraktikum;
use Illuminate\Support\Facades\Storage;

class PenugasanController extends Controller
{
    public function index()
    {
        $penugasans = Penugasan::with(['praktikum', 'sesi', 'aslab.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $praktikums = Praktikum::with('sesis')->get();

        return view('admin.penugasan.index', compact('penugasans', 'praktikums'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'praktikum_id' => 'required|exists:praktikums,id',
            'sesi_id' => 'required|exists:sesi_praktikums,id',
            'kode_akhir_npm' => 'required|integer|min:0|max:9',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required',
            'file_soal' => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:5120',
        ]);

        $filePath = null;
        if ($request->hasFile('file_soal')) {
            $filePath = $request->file('file_soal')->store('penugasan_soal', 'public');
        }

        Penugasan::create([
            'praktikum_id' => $request->praktikum_id,
            'sesi_id' => $request->sesi_id,
            'kode_akhir_npm' => $request->kode_akhir_npm,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'file_soal' => $filePath,
        ]);

        return redirect()->route('admin.penugasan.index')->with('success', 'Penugasan berhasil dibuat.');
    }

    public function update(Request $request, $id)
    {
        $penugasan = Penugasan::findOrFail($id);

        $request->validate([
            'kode_akhir_npm' => 'required|integer|min:0|max:9',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required',
            'file_soal' => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:5120',
        ]);

        $data = $request->only(['kode_akhir_npm', 'judul', 'deskripsi']);

        if ($request->hasFile('file_soal')) {
            if ($penugasan->file_soal) {
                Storage::disk('public')->delete($penugasan->file_soal);
            }
            $data['file_soal'] = $request->file('file_soal')->store('penugasan_soal', 'public');
        }

        $penugasan->update($data);

        return redirect()->route('admin.penugasan.index')->with('success', 'Penugasan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $penugasan = Penugasan::findOrFail($id);

        if ($penugasan->file_soal) {
            Storage::disk('public')->delete($penugasan->file_soal);
        }

        $penugasan->delete();

        return redirect()->route('admin.penugasan.index')->with('success', 'Penugasan berhasil dihapus.');
    }
}
