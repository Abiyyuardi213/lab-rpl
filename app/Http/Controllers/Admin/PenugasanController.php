<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DigitNpm;
use App\Models\Penugasan;
use App\Models\PendaftaranPraktikum;
use App\Models\PenugasanPraktikanOverride;
use App\Models\Praktikum;
use App\Models\SesiPraktikum;
use App\Models\JadwalPraktikum;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PenugasanController extends Controller
{
    public function index()
    {
        $jadwalPraktikums = JadwalPraktikum::with(['praktikum', 'sesi', 'penugasans'])
            ->orderBy('tanggal', 'desc')
            ->get();

        $penugasansTanpaJadwal = Penugasan::whereNull('jadwal_praktikum_id')
            ->with(['praktikum', 'sesi', 'aslab.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $praktikums = Praktikum::with('sesis')->get();
        $digitNpms = DigitNpm::active()->ordered()->get();
        $allJadwalPraktikums = JadwalPraktikum::with(['praktikum', 'sesi'])->get(); // For the modal

        return view('admin.penugasan.index', compact('jadwalPraktikums', 'penugasansTanpaJadwal', 'praktikums', 'digitNpms', 'allJadwalPraktikums'));
    }

    public function show($id)
    {
        $jadwal = JadwalPraktikum::with(['praktikum', 'sesi.jadwalPraktikums', 'penugasans.aslab.user'])->findOrFail($id);
        
        $penugasans = Penugasan::where('jadwal_praktikum_id', $id)
            ->with([
                'praktikum',
                'sesi.pendaftarans.praktikan.user',
                'sesi.pendaftarans.aslab.user',
                'sesi.pendaftarans.penugasanOverride.penugasan',
                'sesi.penugasans',
                'aslab.user',
                'jadwalPraktikum',
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        $digitNpms = DigitNpm::active()->ordered()->get();
        $praktikums = Praktikum::with('sesis')->get();
        $allJadwalPraktikums = JadwalPraktikum::with(['praktikum', 'sesi'])->get();

        return view('admin.penugasan.show', compact('jadwal', 'penugasans', 'digitNpms', 'praktikums', 'allJadwalPraktikums'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'praktikum_id' => 'required|exists:praktikums,id',
            'sesi_id' => 'required|exists:sesi_praktikums,id',
            'jadwal_praktikum_id' => 'nullable|exists:jadwal_praktikums,id',
            'kode_akhir_npm' => ['required', 'string', 'max:20', Rule::exists('digit_npms', 'digit')->where('is_active', true)],
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required',
            'file_soal' => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:5120',
        ]);

        $filePath = null;
        if ($request->hasFile('file_soal')) {
            $filePath = $request->file('file_soal')->store('penugasan_soal', 'public');
        } elseif ($request->filled('file_url')) {
            try {
                $url = $request->file_url;
                $contents = file_get_contents($url);
                if ($contents !== false) {
                    $name = basename(parse_url($url, PHP_URL_PATH)) ?: 'downloaded_file';
                    $extension = pathinfo($name, PATHINFO_EXTENSION);
                    if (!$extension) {
                        // Try to guess from content type? For now just keep it.
                    }
                    $filePath = 'penugasan_soal/' . uniqid() . '_' . $name;
                    Storage::disk('public')->put($filePath, $contents);
                }
            } catch (\Exception $e) {
                // Ignore or log error
            }
        }

        $data = $request->only(['praktikum_id', 'sesi_id', 'jadwal_praktikum_id', 'kode_akhir_npm', 'judul', 'deskripsi']);
        $data['file_soal'] = $filePath;
        
        // Ensure empty string is converted to null for foreign key
        if (empty($data['jadwal_praktikum_id'])) {
            unset($data['jadwal_praktikum_id']);
        }

        Penugasan::create($data);

        $route = $request->jadwal_praktikum_id 
            ? redirect()->route('admin.penugasan.show', $request->jadwal_praktikum_id)
            : redirect()->route('admin.penugasan.index');

        return $route->with('success', 'Penugasan berhasil dibuat.');
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'praktikum_id' => 'required|exists:praktikums,id',
            'sesi_id' => 'nullable|exists:sesi_praktikums,id',
            'jadwal_praktikum_id' => 'nullable|exists:jadwal_praktikums,id',
            'judul_umum' => 'required|string|max:255',
            'deskripsi_umum' => 'required',
            'assignments' => 'required|array',
            'assignments.*.kode_akhir_npm' => ['required', 'string', 'max:20'],
            'assignments.*.file_soal' => 'nullable|file|mimes:pdf,doc,docx,zip,rar,jpg,png,jpeg|max:10240',
        ]);

        // Fallback for sesi_id if not provided in request (e.g. from hidden input in show page)
        $sesiId = $request->sesi_id;
        if (!$sesiId && $request->jadwal_praktikum_id) {
            $jadwal = JadwalPraktikum::find($request->jadwal_praktikum_id);
            $sesiId = $jadwal ? $jadwal->sesi_id : null;
        }

        // If sesi_id is still missing and it's required by the system logic
        if (!$sesiId) {
            return back()->withErrors(['sesi_id' => 'ID Sesi wajib diisi atau jadwal tidak memiliki sesi yang valid.'])->withInput();
        }

        $commonData = [
            'praktikum_id' => $request->praktikum_id,
            'sesi_id' => $sesiId,
            'jadwal_praktikum_id' => $request->jadwal_praktikum_id ?: null,
            'judul' => $request->judul_umum,
            'deskripsi' => $request->deskripsi_umum,
        ];

        $createdCount = 0;
        foreach ($request->assignments as $index => $assignmentData) {
            $filePath = null;
            if (isset($assignmentData['file_soal']) && $assignmentData['file_soal']->isValid()) {
                $filePath = $assignmentData['file_soal']->store('penugasan_soal', 'public');
            }

            if ($filePath) {
                Penugasan::create(array_merge($commonData, [
                    'kode_akhir_npm' => $assignmentData['kode_akhir_npm'],
                    'file_soal' => $filePath,
                ]));
                $createdCount++;
            }
        }

        if ($createdCount === 0) {
            return back()->with('error', 'Tidak ada file yang diunggah. Silakan pilih setidaknya satu file untuk digit NPM.')->withInput();
        }

        $route = $request->jadwal_praktikum_id 
            ? redirect()->route('admin.penugasan.show', $request->jadwal_praktikum_id)
            : redirect()->route('admin.penugasan.index');

        return $route->with('success', "$createdCount penugasan berhasil dibuat secara batch.");
    }

    public function update(Request $request, $id)
    {
        $penugasan = Penugasan::findOrFail($id);

        $request->validate([
            'jadwal_praktikum_id' => 'nullable|exists:jadwal_praktikums,id',
            'kode_akhir_npm' => ['required', 'string', 'max:20', Rule::exists('digit_npms', 'digit')->where('is_active', true)],
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required',
            'file_soal' => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:5120',
        ]);

        $data = $request->only(['jadwal_praktikum_id', 'kode_akhir_npm', 'judul', 'deskripsi']);
        
        // Ensure empty string is converted to null for foreign key
        if (empty($data['jadwal_praktikum_id'])) {
            $data['jadwal_praktikum_id'] = null;
        }

        if ($request->hasFile('file_soal')) {
            if ($penugasan->file_soal) {
                Storage::disk('public')->delete($penugasan->file_soal);
            }
            $data['file_soal'] = $request->file('file_soal')->store('penugasan_soal', 'public');
        } elseif ($request->filled('file_url')) {
            try {
                $url = $request->file_url;
                $contents = file_get_contents($url);
                if ($contents !== false) {
                    if ($penugasan->file_soal) {
                        Storage::disk('public')->delete($penugasan->file_soal);
                    }
                    $name = basename(parse_url($url, PHP_URL_PATH)) ?: 'downloaded_file';
                    $data['file_soal'] = 'penugasan_soal/' . uniqid() . '_' . $name;
                    Storage::disk('public')->put($data['file_soal'], $contents);
                }
            } catch (\Exception $e) {
                // Ignore or log error
            }
        }

        $penugasan->update($data);

        $route = $penugasan->jadwal_praktikum_id 
            ? redirect()->route('admin.penugasan.show', $penugasan->jadwal_praktikum_id)
            : redirect()->route('admin.penugasan.index');

        return $route->with('success', 'Penugasan berhasil diperbarui.');
    }

    public function updateStudentAssignment(Request $request, PendaftaranPraktikum $pendaftaran)
    {
        $request->validate([
            'penugasan_id' => 'nullable|exists:penugasans,id',
        ]);

        if (!$request->filled('penugasan_id')) {
            PenugasanPraktikanOverride::where('pendaftaran_id', $pendaftaran->id)->delete();

            return redirect()->route('admin.penugasan.show', $request->jadwal_id ?? $pendaftaran->sesi_id) // Still potentially wrong if sesi_id is not a jadwal_id
                ->with('success', 'Soal praktikan dikembalikan ke aturan digit akhir NPM.');
        }

        $penugasan = Penugasan::findOrFail($request->penugasan_id);

        if ($penugasan->praktikum_id !== $pendaftaran->praktikum_id || $penugasan->sesi_id !== $pendaftaran->sesi_id) {
            return redirect()->route('admin.penugasan.index')
                ->with('error', 'Soal yang dipilih harus berasal dari praktikum dan sesi yang sama.');
        }

        PenugasanPraktikanOverride::updateOrCreate(
            ['pendaftaran_id' => $pendaftaran->id],
            ['penugasan_id' => $penugasan->id]
        );

        return redirect()->route('admin.penugasan.show', $request->jadwal_id ?? $penugasan->jadwal_praktikum_id)
            ->with('success', 'Soal khusus praktikan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $penugasan = Penugasan::findOrFail($id);
        $jadwalId = $penugasan->jadwal_praktikum_id;

        if ($penugasan->file_soal) {
            Storage::disk('public')->delete($penugasan->file_soal);
        }

        $penugasan->delete();

        $route = $jadwalId 
            ? redirect()->route('admin.penugasan.show', $jadwalId)
            : redirect()->route('admin.penugasan.index');

        return $route->with('success', 'Penugasan berhasil dihapus.');
    }
}
