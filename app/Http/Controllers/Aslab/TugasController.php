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

        $students = PendaftaranPraktikum::with('praktikan.user', 'praktikum')
            ->where('aslab_id', $aslabId)
            ->get();

        return view('aslab.tugas.index', compact('tugas', 'students'));
    }

    public function store(Request $request)
    {
        $aslab = Auth::user()->aslab;
        if (!$aslab) {
            return back()->with('error', 'Data aslab tidak ditemukan.');
        }

        $request->validate([
            'pendaftaran_id' => 'required|exists:pendaftaran_praktikums,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $pendaftaran = PendaftaranPraktikum::findOrFail($request->pendaftaran_id);
        if ($pendaftaran->aslab_id !== $aslab->id) {
            return back()->with('error', 'Mahasiswa ini bukan bimbingan Anda.');
        }

        TugasAsistensi::create([
            'pendaftaran_id' => $request->pendaftaran_id,
            'aslab_id' => $aslab->id,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'due_date' => $request->due_date,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Tugas asistensi berhasil diberikan.');
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
            'status' => 'required|in:pending,submitted,reviewed'
        ]);

        $tugas->update($request->only(['nilai', 'catatan_aslab', 'status']));

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
