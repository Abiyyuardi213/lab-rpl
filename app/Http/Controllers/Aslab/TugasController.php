<?php

namespace App\Http\Controllers\Aslab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\TugasAsistensi;
use App\Models\PendaftaranPraktikum;
use Illuminate\Support\Facades\Auth;

class TugasController extends Controller
{
    public function index()
    {
        $aslab = Auth::user()->aslab;
        if (!$aslab) {
            return redirect()->back()->with('error', 'Data aslab tidak ditemukan.');
        }

        $aslabId = $aslab->id;
        $tugas = TugasAsistensi::with(['pendaftaran.praktikan.user', 'pendaftaran.praktikum'])
            ->where('aslab_id', $aslabId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get unique praktikums that this aslab is assisting
        $praktikums = $aslab->praktikums;

        return view('aslab.tugas.index', compact('tugas', 'praktikums'));
    }

    public function store(Request $request)
    {
        $aslab = Auth::user()->aslab;
        if (!$aslab) {
            return back()->with('error', 'Data aslab tidak ditemukan.');
        }

        $request->validate([
            'praktikum_id' => 'required|exists:praktikums,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'file_tugas' => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:5120',
            'due_date' => 'nullable|date',
        ]);

        $filePath = null;
        if ($request->hasFile('file_tugas')) {
            $filePath = $request->file('file_tugas')->store('tugas_soal', 'public');
        }

        $students = PendaftaranPraktikum::where('aslab_id', $aslab->id)
            ->where('praktikum_id', $request->praktikum_id)
            ->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'Anda belum memiliki mahasiswa bimbingan di praktikum ini.');
        }

        foreach ($students as $student) {
            TugasAsistensi::create([
                'pendaftaran_id' => $student->id,
                'aslab_id' => $aslab->id,
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'file_tugas' => $filePath,
                'due_date' => $request->due_date,
                'status' => 'pending'
            ]);
        }

        return back()->with('success', 'Tugas asistensi berhasil diberikan kepada ' . $students->count() . ' mahasiswa.');
    }

    public function update(Request $request, $id)
    {
        $aslab = Auth::user()->aslab;
        if (!$aslab) {
            return back()->with('error', 'Data aslab tidak ditemukan.');
        }

        $tugas = TugasAsistensi::findOrFail($id);
        if ($tugas->aslab_id !== $aslab->id) {
            abort(403);
        }

        $request->validate([
            'nilai' => 'nullable|integer|between:0,100',
            'catatan_aslab' => 'nullable|string',
            'status' => 'required|in:pending,submitted,reviewed',
        ]);

        $data = $request->only(['nilai', 'catatan_aslab', 'status']);

        // Auto-set status to reviewed if grade is provided and it's not currently reviewed
        if ($request->filled('nilai') && $data['status'] !== 'reviewed') {
            $data['status'] = 'reviewed';
        }

        $tugas->update($data);

        return back()->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $aslab = Auth::user()->aslab;
        if (!$aslab) {
            return back()->with('error', 'Data aslab tidak ditemukan.');
        }

        $tugas = TugasAsistensi::findOrFail($id);
        if ($tugas->aslab_id !== $aslab->id) {
            abort(403);
        }

        $tugas->delete();
        return back()->with('success', 'Tugas berhasil dihapus.');
    }
}
