<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DigitNpm;
use App\Models\JadwalPraktikum;
use App\Models\PendaftaranPraktikum;
use App\Models\Penugasan;
use App\Models\PenugasanPraktikanOverride;
use App\Models\Praktikum;
use Illuminate\Http\Request;
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
                'sesi.pendaftarans.penugasanOverrides' => function ($query) use ($id) {
                    $query->where('jadwal_praktikum_id', $id);
                },
                'sesi.pendaftarans.penugasanOverrides.penugasan',
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
        $tipeDistribusi = $request->input('tipe_distribusi', 'by_npm');

        $rules = [
            'praktikum_id' => 'required|exists:praktikums,id',
            'sesi_id' => 'required|exists:sesi_praktikums,id',
            'jadwal_praktikum_id' => 'nullable|exists:jadwal_praktikums,id',
            'tipe_distribusi' => 'nullable|in:by_npm,by_sesi,by_random',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required',
            'file_soal' => 'nullable|file|mimes:pdf,doc,docx,zip,rar,jpg,png,jpeg|max:10240',
            'files_random' => 'nullable|array|max:10',
            'files_random.*' => 'nullable|file|mimes:pdf,doc,docx,zip,rar,jpg,png,jpeg|max:10240',
        ];

        $messages = [
            'files_random.max' => 'Maksimal 10 file soal yang dapat diunggah sekaligus dalam mode random.',
        ];

        if ($tipeDistribusi === 'by_npm') {
            $rules['kode_akhir_npm'] = ['required', 'string', 'max:20', Rule::exists('digit_npms', 'digit')->where('is_active', true)];
        } else {
            $rules['kode_akhir_npm'] = ['nullable', 'string', 'max:20'];
        }

        $request->validate($rules, $messages);

        $targetJadwalId = $request->jadwal_praktikum_id ?: $request->input('redirect_to_jadwal');
        $route = $targetJadwalId
            ? redirect()->route('admin.penugasan.show', $targetJadwalId)
            : redirect()->route('admin.penugasan.index');

        // Penanganan Multiple Upload untuk Mode By Random (Maksimal 10 file)
        $randomFiles = $request->file('files_random', []);
        if ($tipeDistribusi === 'by_random' && is_array($randomFiles) && count($randomFiles) > 1) {
            $createdCount = 0;
            foreach ($randomFiles as $index => $rFile) {
                if ($rFile && $rFile->isValid()) {
                    $rFilePath = $rFile->store('penugasan_soal', 'public');
                    $packetNumber = $index + 1;
                    Penugasan::create([
                        'praktikum_id' => $request->praktikum_id,
                        'sesi_id' => $request->sesi_id,
                        'jadwal_praktikum_id' => $request->jadwal_praktikum_id ?: null,
                        'tipe_distribusi' => 'by_random',
                        'kode_akhir_npm' => 'random',
                        'judul' => $request->judul." (Paket {$packetNumber})",
                        'deskripsi' => $request->deskripsi,
                        'file_soal' => $rFilePath,
                    ]);
                    $createdCount++;
                }
            }

            return $route->with('success', "{$createdCount} variasi soal acak berhasil dibuat dalam pool.");
        }

        $filePath = null;
        if ($tipeDistribusi === 'by_random' && is_array($randomFiles) && count($randomFiles) === 1) {
            $rFile = $randomFiles[0];
            if ($rFile && $rFile->isValid()) {
                $filePath = $rFile->store('penugasan_soal', 'public');
            }
        } elseif ($request->hasFile('file_soal')) {
            $filePath = $request->file('file_soal')->store('penugasan_soal', 'public');
        } elseif ($request->filled('file_url')) {
            try {
                $url = $request->file_url;
                $contents = file_get_contents($url);
                if ($contents !== false) {
                    $name = basename(parse_url($url, PHP_URL_PATH)) ?: 'downloaded_file';
                    $extension = pathinfo($name, PATHINFO_EXTENSION);
                    if (! $extension) {
                        // Try to guess from content type? For now just keep it.
                    }
                    $filePath = 'penugasan_soal/'.uniqid().'_'.$name;
                    Storage::disk('public')->put($filePath, $contents);
                }
            } catch (\Exception $e) {
                // Ignore or log error
            }
        }

        $data = $request->only(['praktikum_id', 'sesi_id', 'jadwal_praktikum_id', 'judul', 'deskripsi']);
        $data['tipe_distribusi'] = $tipeDistribusi;
        $data['kode_akhir_npm'] = $tipeDistribusi === 'by_sesi' ? '*' : ($request->filled('kode_akhir_npm') ? $request->kode_akhir_npm : ($tipeDistribusi === 'by_random' ? 'random' : null));
        $data['file_soal'] = $filePath;

        // Ensure empty string is converted to null for foreign key
        if (empty($data['jadwal_praktikum_id'])) {
            unset($data['jadwal_praktikum_id']);
        }

        Penugasan::create($data);

        return $route->with('success', 'Penugasan berhasil dibuat.');
    }

    public function bulkStore(Request $request)
    {
        if (! $request->filled('judul_umum') && $request->filled('judul')) {
            $request->merge(['judul_umum' => $request->input('judul')]);
        }
        if (! $request->filled('deskripsi_umum') && $request->filled('deskripsi')) {
            $request->merge(['deskripsi_umum' => $request->input('deskripsi')]);
        }

        $request->validate([
            'praktikum_id' => 'required|exists:praktikums,id',
            'sesi_id' => 'nullable|exists:sesi_praktikums,id',
            'jadwal_praktikum_id' => 'nullable|exists:jadwal_praktikums,id',
            'judul_umum' => 'required|string|max:255',
            'deskripsi_umum' => 'required',
            'assignments' => 'required|array',
            'assignments.*.kode_akhir_npm' => [
                'required',
                'string',
                'max:20',
                Rule::exists('digit_npms', 'digit')->where('is_active', true),
            ],
            'assignments.*.file_soal' => 'nullable|file|mimes:pdf,doc,docx,zip,rar,jpg,png,jpeg|max:10240',
        ]);

        // Fallback for sesi_id if not provided in request (e.g. from hidden input in show page)
        $sesiId = $request->sesi_id;
        if (! $sesiId && $request->jadwal_praktikum_id) {
            $jadwal = JadwalPraktikum::find($request->jadwal_praktikum_id);
            $sesiId = $jadwal ? $jadwal->sesi_id : null;
        }

        // If sesi_id is still missing and it's required by the system logic
        if (! $sesiId) {
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
        $tipeDistribusi = $request->input('tipe_distribusi', $penugasan->tipe_distribusi ?? 'by_npm');

        $rules = [
            'jadwal_praktikum_id' => 'nullable|exists:jadwal_praktikums,id',
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

        $data = $request->only(['jadwal_praktikum_id', 'judul', 'deskripsi']);
        $data['tipe_distribusi'] = $tipeDistribusi;
        $data['kode_akhir_npm'] = $tipeDistribusi === 'by_sesi' ? '*' : ($tipeDistribusi === 'by_random' ? 'random' : ($request->filled('kode_akhir_npm') ? $request->kode_akhir_npm : $penugasan->kode_akhir_npm));

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
                    $data['file_soal'] = 'penugasan_soal/'.uniqid().'_'.$name;
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
            // jadwal_id wajib ada agar override tidak pernah tersimpan tanpa konteks modul
            'jadwal_id' => 'required|exists:jadwal_praktikums,id',
        ]);

        $jadwalId = $request->jadwal_id;

        if (! $request->filled('penugasan_id')) {
            PenugasanPraktikanOverride::where('pendaftaran_id', $pendaftaran->id)
                ->where('jadwal_praktikum_id', $jadwalId)
                ->delete();

            return redirect()->route('admin.penugasan.show', $jadwalId)
                ->with('success', 'Soal praktikan dikembalikan ke aturan digit akhir NPM.');
        }

        $penugasan = Penugasan::findOrFail($request->penugasan_id);

        // Guard: jadwal_id selalu dari request (sudah divalidasi), bukan dari penugasan
        // Ini mencegah override tersimpan dengan jadwal_praktikum_id = NULL
        if (! $jadwalId) {
            return redirect()->route('admin.penugasan.show', $jadwalId)
                ->with('error', 'Gagal menyimpan: Jadwal praktikum tidak teridentifikasi.');
        }

        if ($penugasan->praktikum_id !== $pendaftaran->praktikum_id) {
            return redirect()->route('admin.penugasan.show', $jadwalId)
                ->with('error', 'Soal yang dipilih harus berasal dari praktikum yang sama.');
        }

        // Cek digit akhir NPM mahasiswa secara aman
        $studentNpm = $pendaftaran->praktikan->npm ?? '';
        $lastChar = substr($studentNpm, -1);
        $studentLastDigit = is_numeric($lastChar) ? $lastChar : null;

        // Jika kode soal SAMA dengan digit NPM mahasiswa → hapus override (kembalikan ke default)
        if ($studentLastDigit !== null && (string) $penugasan->kode_akhir_npm === (string) $studentLastDigit) {
            PenugasanPraktikanOverride::where('pendaftaran_id', $pendaftaran->id)
                ->where('jadwal_praktikum_id', $jadwalId)
                ->delete();

            return redirect()->route('admin.penugasan.show', $jadwalId)
                ->with('success', 'Soal sesuai digit NPM mahasiswa. Dikembalikan ke default (digit '.$studentLastDigit.').');
        }

        // Kode tidak cocok dengan digit NPM → soal khusus, selalu terikat ke jadwalId
        PenugasanPraktikanOverride::updateOrCreate(
            [
                'pendaftaran_id' => $pendaftaran->id,
                'jadwal_praktikum_id' => $jadwalId,
            ],
            ['penugasan_id' => $penugasan->id]
        );

        return redirect()->route('admin.penugasan.show', $jadwalId)
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
