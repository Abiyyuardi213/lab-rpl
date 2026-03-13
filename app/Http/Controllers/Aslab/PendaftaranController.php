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

        $myPraktikums = $aslab->praktikums;
        $praktikumIds = $myPraktikums->pluck('id');

        // Only show students already assigned to this specific aslab
        $students = PendaftaranPraktikum::with(['praktikan.user', 'praktikum', 'sesi', 'aslab'])
            ->whereIn('praktikum_id', $praktikumIds)
            ->where('aslab_id', $aslab->id) // Restricted to self
            ->where('status', 'verified')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('aslab.pendaftaran.index', compact('students', 'myPraktikums'));
    }
}
