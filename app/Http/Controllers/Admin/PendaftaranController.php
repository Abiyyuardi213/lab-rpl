<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranPraktikum;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    public function index(Request $request)
    {
        $query = PendaftaranPraktikum::with(['praktikan.user', 'praktikum', 'sesi']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $pendaftarans = $query->orderBy('created_at', 'asc')->get();
        return view('admin.pendaftaran.index', compact('pendaftarans'));
    }

    public function show($id)
    {
        $pendaftaran = PendaftaranPraktikum::with(['praktikan.user', 'praktikum', 'sesi'])->findOrFail($id);
        return view('admin.pendaftaran.show', compact('pendaftaran'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:verified,rejected',
            'catatan' => 'required_if:status,rejected|nullable|string',
        ]);

        $pendaftaran = PendaftaranPraktikum::findOrFail($id);
        $pendaftaran->update([
            'status' => $request->status,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('admin.pendaftaran.index')->with('success', 'Status pendaftaran berhasil diperbarui.');
    }
}
