<?php

namespace App\Http\Controllers\Aslab;

use App\Http\Controllers\Controller;
use App\Models\DigitNpm;
use App\Models\Penugasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PenugasanController extends Controller
{
    public function index()
    {
        $aslab = Auth::user()->aslab;
        if (! $aslab) {
            return redirect()->back()->with('error', 'Data aslab tidak ditemukan.');
        }

        $praktikumIds = $aslab->praktikums->pluck('id');

        $penugasans = Penugasan::with(['praktikum', 'sesi'])
            ->whereIn('praktikum_id', $praktikumIds)
            ->where(function ($query) use ($aslab) {
                $query->where('aslab_id', $aslab->id)
                    ->orWhereNull('aslab_id');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $praktikums = $aslab->praktikums()->with('sesis')->get();
        $digitNpms = DigitNpm::active()->ordered()->get();

        return view('aslab.penugasan.index', compact('penugasans', 'praktikums', 'digitNpms'));
    }

    public function store(Request $request)
    {
        $aslab = Auth::user()->aslab;
        if (! $aslab) {
            return back()->with('error', 'Data aslab tidak ditemukan.');
        }

        $tipeDistribusi = $request->input('tipe_distribusi', 'by_npm');

        $rules = [
            'praktikum_id' => 'required|exists:praktikums,id',
            'sesi_id' => 'required|exists:sesi_praktikums,id',
            'tipe_distribusi' => 'nullable|in:by_npm,by_sesi,by_random',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required',
            'file_soal' => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:5120',
        ];

        if ($tipeDistribusi === 'by_npm') {
            $rules['kode_akhir_npm'] = ['required', 'string', 'max:20', Rule::exists('digit_npms', 'digit')->where('is_active', true)];
        } else {
            $rules['kode_akhir_npm'] = ['nullable', 'string', 'max:20'];
        }

        $request->validate($rules);

        $filePath = null;
        if ($request->hasFile('file_soal')) {
            $filePath = $request->file('file_soal')->store('penugasan_soal', 'public');
        }

        Penugasan::create([
            'praktikum_id' => $request->praktikum_id,
            'sesi_id' => $request->sesi_id,
            'aslab_id' => $aslab->id,
            'tipe_distribusi' => $tipeDistribusi,
            'kode_akhir_npm' => $tipeDistribusi === 'by_sesi' ? '*' : ($request->filled('kode_akhir_npm') ? $request->kode_akhir_npm : ($tipeDistribusi === 'by_random' ? 'random' : null)),
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'file_soal' => $filePath,
        ]);

        return redirect()->route('aslab.penugasan.index')->with('success', 'Penugasan berhasil dibuat.');
    }

    public function update(Request $request, $id)
    {
        $aslab = Auth::user()->aslab;
        if (! $aslab) {
            return back()->with('error', 'Data aslab tidak ditemukan.');
        }

        $penugasan = Penugasan::findOrFail($id);
        if ($penugasan->aslab_id !== $aslab->id) {
            abort(403);
        }

        $tipeDistribusi = $request->input('tipe_distribusi', $penugasan->tipe_distribusi ?? 'by_npm');

        $rules = [
            'tipe_distribusi' => 'nullable|in:by_npm,by_sesi,by_random',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required',
            'file_soal' => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:5120',
        ];

        if ($tipeDistribusi === 'by_npm') {
            $rules['kode_akhir_npm'] = ['required', 'string', 'max:20', Rule::exists('digit_npms', 'digit')->where('is_active', true)];
        } else {
            $rules['kode_akhir_npm'] = ['nullable', 'string', 'max:20'];
        }

        $request->validate($rules);

        $data = $request->only(['judul', 'deskripsi']);
        $data['tipe_distribusi'] = $tipeDistribusi;
        $data['kode_akhir_npm'] = $tipeDistribusi === 'by_sesi' ? '*' : ($tipeDistribusi === 'by_random' ? 'random' : ($request->filled('kode_akhir_npm') ? $request->kode_akhir_npm : $penugasan->kode_akhir_npm));

        if ($request->hasFile('file_soal')) {
            if ($penugasan->file_soal) {
                Storage::disk('public')->delete($penugasan->file_soal);
            }
            $data['file_soal'] = $request->file('file_soal')->store('penugasan_soal', 'public');
        }

        $penugasan->update($data);

        return redirect()->route('aslab.penugasan.index')->with('success', 'Penugasan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $aslab = Auth::user()->aslab;
        if (! $aslab) {
            return back()->with('error', 'Data aslab tidak ditemukan.');
        }

        $penugasan = Penugasan::findOrFail($id);
        if ($penugasan->aslab_id !== $aslab->id) {
            abort(403);
        }

        if ($penugasan->file_soal) {
            Storage::disk('public')->delete($penugasan->file_soal);
        }

        $penugasan->delete();

        return redirect()->route('aslab.penugasan.index')->with('success', 'Penugasan berhasil dihapus.');
    }
}
