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
        
        // Group by assignment details
        $allTugas = TugasAsistensi::with(['pendaftaran.praktikum'])
            ->where('aslab_id', $aslabId)
            ->orderBy('created_at', 'desc')
            ->get();

        $tugas = $allTugas->groupBy(function($item) {
            // Using a unique key for grouping: judul, due_date, and deskripsi
            $date = $item->due_date ? $item->due_date->format('Y-m-d') : 'no-date';
            return $item->judul . '|' . $date . '|' . $item->deskripsi;
        })->map(function($group) {
            $representative = $group->first();
            $representative->total_mahasiswa = $group->count();
            $representative->total_reviewed = $group->where('status', 'reviewed')->count();
            $representative->total_submitted = $group->where('status', 'submitted')->count();
            $representative->total_pending = $group->where('status', 'pending')->count();
            return $representative;
        })->values();

        // Get unique praktikums that this aslab is assisting
        $praktikums = $aslab->praktikums;

        // Get all students under this aslab for direct grading
        $students = PendaftaranPraktikum::with('praktikan.user', 'praktikum')
            ->where('aslab_id', $aslabId)
            ->where('status', 'verified')
            ->get();

        return view('aslab.tugas.index', compact('tugas', 'praktikums', 'students'));
    }

    public function show($id)
    {
        $aslab = Auth::user()->aslab;
        if (!$aslab) {
            return redirect()->back()->with('error', 'Data aslab tidak ditemukan.');
        }
        
        $representative = TugasAsistensi::findOrFail($id);
        if ($representative->aslab_id !== $aslab->id) {
            abort(403);
        }

        $tugas = TugasAsistensi::with(['pendaftaran.praktikan.user', 'pendaftaran.praktikum'])
            ->where('aslab_id', $aslab->id)
            ->where('judul', $representative->judul)
            ->where('deskripsi', $representative->deskripsi)
            ->where(function($query) use ($representative) {
                if ($representative->due_date) {
                    $query->whereDate('due_date', $representative->due_date);
                } else {
                    $query->whereNull('due_date');
                }
            })
            ->get();

        return view('aslab.tugas.show', compact('representative', 'tugas'));
    }

    public function storeDirect(Request $request)
    {
        $aslab = Auth::user()->aslab;
        if (!$aslab) {
            return back()->with('error', 'Data aslab tidak ditemukan.');
        }

        $request->validate([
            'pendaftaran_id' => 'required|exists:pendaftaran_praktikums,id',
            'judul' => 'required|string|max:255',
            'nilai' => 'required|integer|between:0,100',
            'catatan_aslab' => 'nullable|string',
        ]);

        $student = PendaftaranPraktikum::where('id', $request->pendaftaran_id)
            ->where('aslab_id', $aslab->id)
            ->firstOrFail();

        TugasAsistensi::create([
            'pendaftaran_id' => $student->id,
            'aslab_id' => $aslab->id,
            'judul' => $request->judul,
            'nilai' => $request->nilai,
            'catatan_aslab' => $request->catatan_aslab,
            'status' => 'reviewed',
            'deskripsi' => 'Penilaian asistensi langsung (Tanpa Tugas)'
        ]);

        return back()->with('success', 'Nilai asistensi berhasil diberikan kepada ' . $student->praktikan->user->name);
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

        $representative = TugasAsistensi::findOrFail($id);
        if ($representative->aslab_id !== $aslab->id) {
            abort(403);
        }

        // Handle Bulk Update (Group Penugasan)
        if ($request->has('is_bulk')) {
            $request->validate([
                'judul' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
                'due_date' => 'nullable|date',
                'file_tugas' => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:5120',
            ]);

            $updateData = $request->only(['judul', 'deskripsi', 'due_date']);
            
            if ($request->hasFile('file_tugas')) {
                $updateData['file_tugas'] = $request->file('file_tugas')->store('tugas_soal', 'public');
            }

            // Update all tasks in the same group based on the ORIGINAL representative's state
            TugasAsistensi::where('aslab_id', $aslab->id)
                ->where('judul', $representative->judul)
                ->where('deskripsi', $representative->deskripsi)
                ->where(function($query) use ($representative) {
                    if ($representative->due_date) {
                        $query->whereDate('due_date', $representative->due_date);
                    } else {
                        $query->whereNull('due_date');
                    }
                })
                ->update($updateData);

            return back()->with('success', 'Data penugasan berhasil diperbarui untuk seluruh mahasiswa.');
        }

        // Handle Individual Student Update (Grading)
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

        $representative->update($data);

        return back()->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $aslab = Auth::user()->aslab;
        if (!$aslab) {
            return back()->with('error', 'Data aslab tidak ditemukan.');
        }

        $representative = TugasAsistensi::findOrFail($id);
        if ($representative->aslab_id !== $aslab->id) {
            abort(403);
        }

        // Delete all tasks in the same group
        TugasAsistensi::where('aslab_id', $aslab->id)
            ->where('judul', $representative->judul)
            ->where('deskripsi', $representative->deskripsi)
            ->where(function($query) use ($representative) {
                if ($representative->due_date) {
                    $query->whereDate('due_date', $representative->due_date);
                } else {
                    $query->whereNull('due_date');
                }
            })
            ->delete();

        return back()->with('success', 'Penugasan berhasil dihapus.');
    }
}
