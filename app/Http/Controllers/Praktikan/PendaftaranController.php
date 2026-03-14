<?php

namespace App\Http\Controllers\Praktikan;

use App\Http\Controllers\Controller;
use App\Models\Praktikum;
use App\Models\PendaftaranPraktikum;
use App\Models\SesiPraktikum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PendaftaranController extends Controller
{
    public function index()
    {
        $pendaftarans = PendaftaranPraktikum::with(['praktikum', 'sesi'])
            ->where('praktikan_id', Auth::user()->praktikan->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('praktikan.pendaftaran.index', compact('pendaftarans'));
    }

    public function create($praktikum_id)
    {
        $praktikum = Praktikum::with('sesis')->findOrFail($praktikum_id);

        // Cek apakah sudah pernah mendaftar
        $existing = PendaftaranPraktikum::where('praktikan_id', Auth::user()->praktikan->id)
            ->where('praktikum_id', $praktikum_id)
            ->whereIn('status', ['pending', 'verified'])
            ->first();

        if ($existing) {
            return redirect()->route('praktikan.dashboard')->with('error', 'Anda sudah mendaftar pada praktikum ini.');
        }

        if ($praktikum->status_praktikum !== 'open_registration') {
            return redirect()->route('praktikan.dashboard')->with('error', 'Pendaftaran untuk praktikum ini sudah ditutup.');
        }

        return view('praktikan.pendaftaran.create', compact('praktikum'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'praktikum_id' => 'required|exists:praktikums,id',
            'sesi_id' => 'required|exists:sesi_praktikums,id',
            'no_hp' => 'nullable|string|max:15',
            'dosen_pengampu' => 'required|string|max:255',
            'asal_kelas_mata_kuliah' => 'required|string|max:50',
            'kelas' => 'required|in:pagi,malam',
            'bukti_krs' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'bukti_pembayaran' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'foto_almamater' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'is_mengulang' => 'required|boolean',
        ]);

        // Pastikan tidak ada pendaftaran ganda yang aktif
        $existing = PendaftaranPraktikum::where('praktikan_id', Auth::user()->praktikan->id)
            ->where('praktikum_id', $request->praktikum_id)
            ->whereIn('status', ['pending', 'verified'])
            ->first();

        if ($existing) {
            return redirect()->route('praktikan.dashboard')->with('error', 'Anda sudah mendaftar pada praktikum ini.');
        }

        $praktikum = Praktikum::findOrFail($request->praktikum_id);
        $sesi = SesiPraktikum::findOrFail($request->sesi_id);

        // Cek kuota sesi
        if ($sesi->pendaftarans()->count() >= $sesi->kuota) {
            return back()->withErrors(['sesi_id' => 'Sesi yang Anda pilih sudah penuh.'])->withInput();
        }

        // Upload files
        $pathKrs = $request->file('bukti_krs')->store('pendaftaran/krs', 'public');
        $pathPembayaran = $request->file('bukti_pembayaran')->store('pendaftaran/pembayaran', 'public');
        $pathFoto = $request->file('foto_almamater')->store('pendaftaran/foto', 'public');

        PendaftaranPraktikum::create([
            'praktikan_id' => Auth::user()->praktikan->id,
            'praktikum_id' => $request->praktikum_id,
            'sesi_id' => $request->sesi_id,
            'no_hp' => $request->no_hp ?: Auth::user()->praktikan->no_hp,
            'dosen_pengampu' => $request->dosen_pengampu,
            'kelas' => $request->kelas,
            'asal_kelas_mata_kuliah' => $request->asal_kelas_mata_kuliah,
            'bukti_krs' => $pathKrs,
            'bukti_pembayaran' => $pathPembayaran,
            'foto_almamater' => $pathFoto,
            'is_mengulang' => $request->is_mengulang,
            'status' => 'pending',
        ]);
        return redirect()->route('praktikan.dashboard')->with('success', 'Pendaftaran praktikum berhasil dikirim. Silakan tunggu verifikasi admin.');
    }

    public function progress($id)
    {
        $pendaftaran = PendaftaranPraktikum::with(['praktikum', 'aslab.user', 'tugasAsistensis' => function ($q) {
            $q->orderBy('created_at', 'desc');
        }])
            ->where('id', $id)
            ->where('praktikan_id', Auth::user()->praktikan->id)
            ->firstOrFail();

        return view('praktikan.pendaftaran.progress', compact('pendaftaran'));
    }

    public function submitTugas(Request $request, $tugas_id)
    {
        $tugas = \App\Models\TugasAsistensi::findOrFail($tugas_id);

        // Ensure this task belongs to the user
        $pendaftaran = $tugas->pendaftaran;
        if ($pendaftaran->praktikan_id !== Auth::user()->praktikan->id) {
            abort(403);
        }

        $request->validate([
            'file_mahasiswa' => 'required|file|mimes:pdf,zip,rar,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('file_mahasiswa')) {
            if ($tugas->file_mahasiswa) {
                Storage::delete('public/' . $tugas->file_mahasiswa);
            }
            $tugas->file_mahasiswa = $request->file('file_mahasiswa')->store('tugas/mahasiswa', 'public');
            $tugas->status = 'submitted';
            $tugas->save();
        }

        return back()->with('success', 'Tugas berhasil diunggah.');
    }
}
