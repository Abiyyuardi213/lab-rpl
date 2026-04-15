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
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PenugasanController extends Controller
{
    public function index()
    {
        $penugasans = Penugasan::with([
            'praktikum',
            'sesi.pendaftarans.praktikan.user',
            'sesi.pendaftarans.aslab.user',
            'sesi.pendaftarans.penugasanOverride.penugasan',
            'sesi.penugasans',
            'aslab.user',
        ])
            ->orderBy('created_at', 'desc')
            ->get();

        $praktikums = Praktikum::with('sesis')->get();
        $digitNpms = DigitNpm::active()->ordered()->get();

        return view('admin.penugasan.index', compact('penugasans', 'praktikums', 'digitNpms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'praktikum_id' => 'required|exists:praktikums,id',
            'sesi_id' => 'required|exists:sesi_praktikums,id',
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

        Penugasan::create([
            'praktikum_id' => $request->praktikum_id,
            'sesi_id' => $request->sesi_id,
            'kode_akhir_npm' => $request->kode_akhir_npm,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'file_soal' => $filePath,
        ]);

        return redirect()->route('admin.penugasan.index')->with('success', 'Penugasan berhasil dibuat.');
    }

    public function update(Request $request, $id)
    {
        $penugasan = Penugasan::findOrFail($id);

        $request->validate([
            'kode_akhir_npm' => ['required', 'string', 'max:20', Rule::exists('digit_npms', 'digit')->where('is_active', true)],
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required',
            'file_soal' => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:5120',
        ]);

        $data = $request->only(['kode_akhir_npm', 'judul', 'deskripsi']);

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

        return redirect()->route('admin.penugasan.index')->with('success', 'Penugasan berhasil diperbarui.');
    }

    public function updateStudentAssignment(Request $request, PendaftaranPraktikum $pendaftaran)
    {
        $request->validate([
            'penugasan_id' => 'nullable|exists:penugasans,id',
        ]);

        if (!$request->filled('penugasan_id')) {
            PenugasanPraktikanOverride::where('pendaftaran_id', $pendaftaran->id)->delete();

            return redirect()->route('admin.penugasan.index')
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

        return redirect()->route('admin.penugasan.index')
            ->with('success', 'Soal khusus praktikan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $penugasan = Penugasan::findOrFail($id);

        if ($penugasan->file_soal) {
            Storage::disk('public')->delete($penugasan->file_soal);
        }

        $penugasan->delete();

        return redirect()->route('admin.penugasan.index')->with('success', 'Penugasan berhasil dihapus.');
    }
}
