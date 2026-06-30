<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Praktikum;
use App\Models\PendaftaranPraktikum;
use App\Models\PenilaianAkhir;
use App\Imports\PenilaianAkhirImport;
use App\Traits\HasActivityLog;
use Maatwebsite\Excel\Facades\Excel;

class PenilaianAkhirController extends Controller
{
    use HasActivityLog;

    /**
     * Display a listing of the practical courses.
     */
    public function index()
    {
        $praktikums = Praktikum::withCount(['pendaftarans' => function ($q) {
            $q->where('status', 'verified');
        }])->get();

        return view('admin.penilaian_akhir.index', compact('praktikums'));
    }

    /**
     * Show the final grades for a specific practical course.
     */
    public function showPraktikum($praktikum_id)
    {
        $praktikum = Praktikum::findOrFail($praktikum_id);

        $pendaftarans = PendaftaranPraktikum::with(['praktikan.user', 'penilaianAkhir'])
            ->where('praktikum_id', $praktikum_id)
            ->where('status', 'verified')
            ->get();

        $grades = [];
        foreach ($pendaftarans as $pendaftaran) {
            if ($pendaftaran->penilaianAkhir) {
                $grades[] = [
                    'pendaftaran' => $pendaftaran,
                    'grades' => $pendaftaran->penilaianAkhir->toArray(),
                    'is_db' => true,
                ];
            } else {
                // Dynamically calculate grades with default zeros
                $nilaiDosen = [];
                $nilaiLaporan = 0;
                $nilaiTugasAkhir = 0;

                $calculated = PenilaianAkhir::calculateGrades(
                    $pendaftaran,
                    $nilaiDosen,
                    $nilaiLaporan,
                    $nilaiTugasAkhir,
                    false
                );

                $grades[] = [
                    'pendaftaran' => $pendaftaran,
                    'grades' => $calculated,
                    'is_db' => false,
                ];
            }
        }

        // Sort by user name alphabetically
        usort($grades, function ($a, $b) {
            $nameA = $a['pendaftaran']->praktikan->user->name ?? '';
            $nameB = $b['pendaftaran']->praktikan->user->name ?? '';
            return strcasecmp($nameA, $nameB);
        });

        return view('admin.penilaian_akhir.show_praktikum', compact('praktikum', 'grades'));
    }

    /**
     * Import final grades from an uploaded Excel file.
     */
    public function import(Request $request, $praktikum_id)
    {
        $request->validate([
            'file_excel' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        $praktikum = Praktikum::findOrFail($praktikum_id);

        Excel::import(new PenilaianAkhirImport($praktikum_id), $request->file('file_excel'));

        $this->logActivity(
            'Import Penilaian Akhir',
            'Admin mengimport nilai akhir praktikum: ' . $praktikum->nama_praktikum,
            ['praktikum_id' => $praktikum_id]
        );

        return back()->with('success', 'Nilai akhir praktikan berhasil diimport.');
    }

    /**
     * Update/override the final grade for a specific student registration.
     */
    public function update(Request $request, $pendaftaran_id)
    {
        $request->validate([
            'nilai_dosen' => 'nullable|array',
            'nilai_dosen.*' => 'nullable|integer|between:0,100',
            'nilai_laporan' => 'nullable|integer|between:0,100',
            'nilai_tugas_akhir' => 'nullable|integer|between:0,100',
            'is_gugur' => 'nullable|boolean',
            'alasan_gugur' => 'nullable|string',
        ]);

        $pendaftaran = PendaftaranPraktikum::with('praktikan.user')->findOrFail($pendaftaran_id);

        $nilaiDosen = $request->input('nilai_dosen', []);
        $nilaiLaporan = $request->input('nilai_laporan', 0);
        $nilaiTugasAkhir = $request->input('nilai_tugas_akhir', 0);
        $isGugur = (bool)$request->input('is_gugur', false);
        $alasanGugur = $request->input('alasan_gugur');

        $calculated = PenilaianAkhir::calculateGrades(
            $pendaftaran,
            $nilaiDosen,
            $nilaiLaporan,
            $nilaiTugasAkhir,
            $isGugur,
            $alasanGugur
        );

        $penilaianAkhir = PenilaianAkhir::updateOrCreate(
            ['pendaftaran_id' => $pendaftaran->id],
            $calculated
        );

        $this->logActivity(
            'Update Penilaian Akhir',
            'Admin memperbarui nilai akhir praktikan: ' . ($pendaftaran->praktikan->user->name ?? ''),
            ['penilaian_akhir_id' => $penilaianAkhir->id]
        );

        return back()->with('success', 'Nilai akhir praktikan berhasil diperbarui.');
    }
}
