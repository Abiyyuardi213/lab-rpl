<?php

namespace App\Http\Controllers\Aslab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PendaftaranPraktikum;
use App\Models\AslabPraktikum;
use Illuminate\Support\Facades\Auth;

class PendaftaranController extends Controller
{
    public function index()
    {
        $aslab = Auth::user()->aslab;
        if (!$aslab) {
            return redirect()->back()->with('error', 'Data aslab tidak ditemukan.');
        }

        $myPraktikums = $aslab->aslabPraktikums;
        $praktikumIds = $myPraktikums->pluck('id');

        $students = PendaftaranPraktikum::with(['praktikan.user', 'praktikum', 'sesi', 'aslab'])
            ->whereIn('praktikum_id', $praktikumIds)
            ->where('status', 'verified')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('aslab.pendaftaran.index', compact('students', 'myPraktikums'));
    }

    public function assign(Request $request, $id)
    {
        $pendaftaran = PendaftaranPraktikum::findOrFail($id);
        $aslab = Auth::user()->aslab;

        if (!$aslab) {
            return back()->with('error', 'Data aslab tidak ditemukan.');
        }

        // Check if aslab is in this practicum
        $aslabPraktikum = AslabPraktikum::where('aslab_id', $aslab->id)
            ->where('praktikum_id', $pendaftaran->praktikum_id)
            ->first();

        if (!$aslabPraktikum) {
            return back()->with('error', 'Anda tidak ditugaskan pada praktikum ini.');
        }

        // Check quota
        $currentCount = PendaftaranPraktikum::where('aslab_id', $aslab->id)
            ->where('praktikum_id', $pendaftaran->praktikum_id)
            ->count();

        if ($currentCount >= $aslabPraktikum->kuota) {
            return back()->with('error', 'Kuota bimbingan Anda untuk praktikum ini sudah penuh.');
        }

        $pendaftaran->aslab_id = $aslab->id;
        $pendaftaran->save();

        return back()->with('success', 'Mahasiswa berhasil Anda ambil sebagai bimbingan.');
    }
}
