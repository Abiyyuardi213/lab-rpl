<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Praktikum;
use App\Models\PendaftaranPraktikum;
use App\Models\PenilaianAkhir;
use App\Models\Presensi;
use App\Models\PenilaianPraktikum;
use App\Models\TugasAsistensi;
use App\Models\Aslab;
use App\Exports\PenilaianAkhirExport;
use App\Exports\PenilaianAkhirTemplate;
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

        $schedules = $praktikum->jadwals()
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->get();

        $pendaftarans = PendaftaranPraktikum::with([
            'praktikan.user',
            'penilaianAkhir',
            'presensis.penilaian',
            'tugasAsistensis'
        ])
            ->where('praktikum_id', $praktikum_id)
            ->where('status', 'verified')
            ->get();

        $grades = [];
        foreach ($pendaftarans as $pendaftaran) {
            // Compute module scores ahead of time so Blade doesn't execute queries in loops
            $prakScores = [];
            $astScores = [];

            foreach ($schedules as $index => $sched) {
                $modulNum = $index + 1;
                if ($modulNum > $praktikum->jumlah_modul) break;

                $pres = $pendaftaran->presensis->firstWhere('jadwal_id', $sched->id);
                $prakScores[$modulNum] = ($pres && $pres->penilaian) ? $pres->penilaian->nilai : 0;

                $tugas = $pendaftaran->tugasAsistensis->firstWhere('judul', $sched->judul_modul);
                $astScores[$modulNum] = $tugas ? ($tugas->nilai ?? 0) : 0;
            }

            for ($i = 1; $i <= $praktikum->jumlah_modul; $i++) {
                if (!isset($prakScores[$i])) $prakScores[$i] = 0;
                if (!isset($astScores[$i])) $astScores[$i] = 0;
            }

            if ($pendaftaran->penilaianAkhir) {
                $grades[] = [
                    'pendaftaran' => $pendaftaran,
                    'grades' => $pendaftaran->penilaianAkhir->toArray(),
                    'is_db' => true,
                    'prak_scores' => $prakScores,
                    'ast_scores' => $astScores,
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
                    false,
                    null,
                    $schedules
                );

                $grades[] = [
                    'pendaftaran' => $pendaftaran,
                    'grades' => $calculated,
                    'is_db' => false,
                    'prak_scores' => $prakScores,
                    'ast_scores' => $astScores,
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

        Excel::import(new PenilaianAkhirImport($praktikum_id, $request->file('file_excel')), $request->file('file_excel'));

        $this->logActivity(
            'Import Penilaian Akhir',
            'Admin mengimport nilai akhir praktikum: ' . $praktikum->nama_praktikum,
            ['praktikum_id' => $praktikum_id]
        );

        return back()->with('success', 'Nilai akhir praktikan berhasil diimport.');
    }

    /**
     * Download import template for final grades.
     */
    public function downloadTemplate($praktikum_id)
    {
        $praktikum = Praktikum::findOrFail($praktikum_id);

        return Excel::download(
            new PenilaianAkhirTemplate($praktikum),
            'template-penilaian-akhir-' . $praktikum->kode_praktikum . '.xlsx'
        );
    }

    /**
     * Export final grades matrix to Excel.
     */
    public function export($praktikum_id)
    {
        $praktikum = Praktikum::findOrFail($praktikum_id);

        $pendaftarans = PendaftaranPraktikum::with(['praktikan.user', 'penilaianAkhir', 'presensis.penilaian', 'tugasAsistensis', 'praktikum.jadwals'])
            ->where('praktikum_id', $praktikum_id)
            ->where('status', 'verified')
            ->get();

        $grades = [];
        foreach ($pendaftarans as $pendaftaran) {
            if ($pendaftaran->penilaianAkhir) {
                $grades[] = [
                    'pendaftaran' => $pendaftaran,
                    'grades' => $pendaftaran->penilaianAkhir->toArray(),
                ];
            } else {
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
                ];
            }
        }

        usort($grades, function ($a, $b) {
            $nameA = $a['pendaftaran']->praktikan->user->name ?? '';
            $nameB = $b['pendaftaran']->praktikan->user->name ?? '';
            return strcasecmp($nameA, $nameB);
        });

        $this->logActivity(
            'Export Penilaian Akhir',
            'Admin mengexport matriks penilaian akhir praktikum: ' . $praktikum->nama_praktikum,
            ['praktikum_id' => $praktikum_id]
        );

        return Excel::download(
            new PenilaianAkhirExport($praktikum, $grades),
            'matriks-penilaian-akhir-' . $praktikum->kode_praktikum . '.xlsx'
        );
    }

    /**
     * Update/override the final grade for a specific student registration.
     */
    public function update(Request $request, $pendaftaran_id)
    {
        $request->validate([
            'nilai_praktikum' => 'nullable|array',
            'nilai_praktikum.*' => 'nullable|integer|between:0,100',
            'nilai_asistensi' => 'nullable|array',
            'nilai_asistensi.*' => 'nullable|integer|between:0,100',
            'nilai_dosen' => 'nullable|array',
            'nilai_dosen.*' => 'nullable|integer|between:0,100',
            'nilai_laporan' => 'nullable|integer|between:0,100',
            'nilai_tugas_akhir' => 'nullable|integer|between:0,100',
            'is_gugur' => 'nullable|boolean',
            'alasan_gugur' => 'nullable|string',
        ]);

        $pendaftaran = PendaftaranPraktikum::with(['praktikan.user', 'praktikum'])->findOrFail($pendaftaran_id);
        $praktikum = $pendaftaran->praktikum;

        $schedules = $praktikum->jadwals()
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->get();

        $nilaiPraktikum = $request->input('nilai_praktikum', []);
        $nilaiAsistensi = $request->input('nilai_asistensi', []);
        $nilaiDosen = $request->input('nilai_dosen', []);
        $nilaiLaporan = $request->input('nilai_laporan', 0);
        $nilaiTugasAkhir = $request->input('nilai_tugas_akhir', 0);
        $isGugur = (bool)$request->input('is_gugur', false);
        $alasanGugur = $request->input('alasan_gugur');

        $aslabId = $pendaftaran->aslab_id ?? Aslab::first()?->id;

        // 1. Update Presensi & PenilaianPraktikum (Nilai Prak)
        foreach ($nilaiPraktikum as $modulIndex => $val) {
            $modulNum = (int)$modulIndex;
            if ($modulNum < 1 || $modulNum > $praktikum->jumlah_modul) continue;

            $sched = $schedules->get($modulNum - 1);
            if ($sched) {
                $presensi = Presensi::firstOrCreate(
                    [
                        'pendaftaran_id' => $pendaftaran->id,
                        'jadwal_id' => $sched->id,
                    ],
                    [
                        'status' => 'hadir',
                    ]
                );

                PenilaianPraktikum::updateOrCreate(
                    ['presensi_id' => $presensi->id],
                    [
                        'aslab_id' => $aslabId,
                        'nilai' => (int)$val,
                    ]
                );
            }
        }

        // 2. Update TugasAsistensi (Nilai Ast)
        foreach ($nilaiAsistensi as $modulIndex => $val) {
            $modulNum = (int)$modulIndex;
            if ($modulNum < 1 || $modulNum > $praktikum->jumlah_modul) continue;

            $sched = $schedules->get($modulNum - 1);
            if ($sched) {
                TugasAsistensi::updateOrCreate(
                    [
                        'pendaftaran_id' => $pendaftaran->id,
                        'judul' => $sched->judul_modul,
                    ],
                    [
                        'aslab_id' => $aslabId,
                        'nilai' => (int)$val,
                        'status' => 'reviewed',
                    ]
                );
            }
        }

        // Reset loaded relations so calculateGrades reads newly updated scores from DB
        $pendaftaran->unsetRelation('presensis');
        $pendaftaran->unsetRelation('tugasAsistensis');

        $calculated = PenilaianAkhir::calculateGrades(
            $pendaftaran,
            $nilaiDosen,
            $nilaiLaporan,
            $nilaiTugasAkhir,
            $isGugur,
            $alasanGugur,
            $schedules
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

    /**
     * Reset/delete the overridden final grade for a specific student registration.
     */
    public function destroy($pendaftaran_id)
    {
        $pendaftaran = PendaftaranPraktikum::with('praktikan.user')->findOrFail($pendaftaran_id);

        PenilaianAkhir::where('pendaftaran_id', $pendaftaran_id)->delete();

        $this->logActivity(
            'Delete/Reset Penilaian Akhir',
            'Admin menghapus/reset override nilai akhir praktikan: ' . ($pendaftaran->praktikan->user->name ?? ''),
            ['pendaftaran_id' => $pendaftaran_id]
        );

        return back()->with('success', 'Nilai akhir praktikan berhasil dihapus dan di-reset.');
    }
}
