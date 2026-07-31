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
    public function showPraktikum(Request $request, $praktikum_id)
    {
        $praktikum = Praktikum::with('aslabs.user')->findOrFail($praktikum_id);

        $schedules = $praktikum->jadwals()
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->get();

        $search = trim((string) $request->query('search', ''));
        $aslabFilter = $request->query('aslab_id', '');
        $dosenFilter = $request->query('dosen_pengampu', '');

        $query = PendaftaranPraktikum::with([
            'praktikan.user',
            'aslab.user',
            'penilaianAkhir',
            'presensis.penilaian',
            'tugasAsistensis'
        ])
            ->where('praktikum_id', $praktikum_id)
            ->where('status', 'verified');

        // Apply Search Filter (NPM, Name, or Dosen Pembimbing)
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('praktikan', function ($pq) use ($search) {
                    $pq->where('npm', 'like', '%' . $search . '%')
                      ->orWhereHas('user', function ($u) use ($search) {
                          $u->where('name', 'like', '%' . $search . '%');
                      });
                })->orWhere('dosen_pengampu', 'like', '%' . $search . '%');
            });
        }

        // Apply Aslab Filter
        if ($aslabFilter !== '') {
            if ($aslabFilter === 'none') {
                $query->whereNull('aslab_id');
            } else {
                $query->where('aslab_id', $aslabFilter);
            }
        }

        // Apply Dosen Pembimbing Filter
        if ($dosenFilter !== '') {
            if ($dosenFilter === 'none') {
                $query->where(function ($q) {
                    $q->whereNull('dosen_pengampu')->orWhere('dosen_pengampu', '');
                });
            } else {
                $query->where('dosen_pengampu', $dosenFilter);
            }
        }

        $pendaftarans = $query->get();

        // Get aslabs for filter dropdown
        $aslabs = $praktikum->aslabs;
        if ($aslabs->isEmpty()) {
            $aslabs = Aslab::with('user')->get();
        }

        // Get dosens for filter dropdown
        $dosensList = PendaftaranPraktikum::where('praktikum_id', $praktikum_id)
            ->whereNotNull('dosen_pengampu')
            ->where('dosen_pengampu', '!=', '')
            ->distinct()
            ->pluck('dosen_pengampu')
            ->toArray();

        if (!empty($praktikum->daftar_dosen) && is_array($praktikum->daftar_dosen)) {
            $dosensList = array_values(array_unique(array_merge($praktikum->daftar_dosen, $dosensList)));
        }

        if (empty($dosensList)) {
            $dosensList = \App\Models\Dosen::active()->pluck('nama')->toArray();
        }
        sort($dosensList);

        $grades = [];
        foreach ($pendaftarans as $pendaftaran) {
            // Compute module scores ahead of time by module number (1..jumlah_modul)
            $prakScores = [];
            $astScores = [];

            for ($i = 1; $i <= $praktikum->jumlah_modul; $i++) {
                $targetTitle = "Modul " . $i;

                // 1. Practical Score (Prak)
                $pres = $pendaftaran->presensis->first(function ($p) use ($targetTitle, $i) {
                    if (!$p->jadwal) return false;
                    $jTitle = $p->jadwal->judul_modul;
                    return strcasecmp($jTitle, $targetTitle) === 0 
                        || str_contains(strtolower($jTitle), strtolower($targetTitle))
                        || str_contains(strtolower($jTitle), "modul " . $i);
                });
                $prakScores[$i] = ($pres && $pres->penilaian) ? $pres->penilaian->nilai : 0;

                // 2. Assistance Score (Ast)
                $tugas = $pendaftaran->tugasAsistensis->first(function ($t) use ($targetTitle, $i) {
                    $tTitle = $t->judul;
                    return strcasecmp($tTitle, $targetTitle) === 0 
                        || str_contains(strtolower($tTitle), strtolower($targetTitle))
                        || str_contains(strtolower($tTitle), "modul " . $i);
                });
                $astScores[$i] = $tugas ? ($tugas->nilai ?? 0) : 0;
            }

            // Auto-detect Tugas Akhir score from presensis if a schedule exists with title containing Tugas Akhir / TA / Akhir
            $taPresensi = $pendaftaran->presensis->first(function ($p) {
                if (!$p->jadwal) return false;
                $jTitle = strtolower($p->jadwal->judul_modul);
                return str_contains($jTitle, 'tugas akhir') || str_contains($jTitle, 'ta ') || str_contains($jTitle, 'akhir');
            });
            $autoTaScore = ($taPresensi && $taPresensi->penilaian) ? $taPresensi->penilaian->nilai : 0;

            if ($pendaftaran->penilaianAkhir) {
                $gData = $pendaftaran->penilaianAkhir->toArray();

                // If DB value for nilai_tugas_akhir is 0/null, fallback to auto-detected TA score from presensi
                if ((empty($gData['nilai_tugas_akhir']) || $gData['nilai_tugas_akhir'] == 0) && $autoTaScore > 0) {
                    $gData['nilai_tugas_akhir'] = $autoTaScore;
                    $recalculated = PenilaianAkhir::calculateGrades(
                        $pendaftaran,
                        $gData['nilai_dosen'] ?? [],
                        $gData['nilai_laporan'] ?? 0,
                        $autoTaScore,
                        $gData['is_gugur'] ?? false,
                        $gData['alasan_gugur'] ?? null,
                        $schedules
                    );
                    $gData = array_merge($gData, $recalculated);
                }

                $grades[] = [
                    'pendaftaran' => $pendaftaran,
                    'grades' => $gData,
                    'is_db' => true,
                    'prak_scores' => $prakScores,
                    'ast_scores' => $astScores,
                ];
            } else {
                // Dynamically calculate grades with default zeros
                $nilaiDosen = [];
                $nilaiLaporan = 0;
                $nilaiTugasAkhir = $autoTaScore;

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

        return view('admin.penilaian_akhir.show_praktikum', compact('praktikum', 'grades', 'aslabs', 'dosensList'));
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
    public function export(Request $request, $praktikum_id)
    {
        $praktikum = Praktikum::findOrFail($praktikum_id);

        $search = trim((string) $request->query('search', ''));
        $aslabFilter = $request->query('aslab_id', '');
        $dosenFilter = $request->query('dosen_pengampu', '');

        $query = PendaftaranPraktikum::with(['praktikan.user', 'aslab.user', 'penilaianAkhir', 'presensis.penilaian', 'tugasAsistensis', 'praktikum.jadwals'])
            ->where('praktikum_id', $praktikum_id)
            ->where('status', 'verified');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('praktikan', function ($pq) use ($search) {
                    $pq->where('npm', 'like', '%' . $search . '%')
                      ->orWhereHas('user', function ($u) use ($search) {
                          $u->where('name', 'like', '%' . $search . '%');
                      });
                })->orWhere('dosen_pengampu', 'like', '%' . $search . '%');
            });
        }

        if ($aslabFilter !== '') {
            if ($aslabFilter === 'none') {
                $query->whereNull('aslab_id');
            } else {
                $query->where('aslab_id', $aslabFilter);
            }
        }

        if ($dosenFilter !== '') {
            if ($dosenFilter === 'none') {
                $query->where(function ($q) {
                    $q->whereNull('dosen_pengampu')->orWhere('dosen_pengampu', '');
                });
            } else {
                $query->where('dosen_pengampu', $dosenFilter);
            }
        }

        $pendaftarans = $query->get();

        $grades = [];
        foreach ($pendaftarans as $pendaftaran) {
            $taPres = $pendaftaran->presensis->first(function ($p) {
                if (!$p->jadwal) return false;
                $jTitle = strtolower($p->jadwal->judul_modul);
                return str_contains($jTitle, 'tugas akhir') || str_contains($jTitle, 'ta ') || str_contains($jTitle, 'akhir');
            });
            $autoTaScore = ($taPres && $taPres->penilaian) ? $taPres->penilaian->nilai : 0;

            if ($pendaftaran->penilaianAkhir) {
                $gData = $pendaftaran->penilaianAkhir->toArray();
                if ((empty($gData['nilai_tugas_akhir']) || $gData['nilai_tugas_akhir'] == 0) && $autoTaScore > 0) {
                    $gData['nilai_tugas_akhir'] = $autoTaScore;
                    $recalculated = PenilaianAkhir::calculateGrades(
                        $pendaftaran,
                        $gData['nilai_dosen'] ?? [],
                        $gData['nilai_laporan'] ?? 0,
                        $autoTaScore,
                        $gData['is_gugur'] ?? false,
                        $gData['alasan_gugur'] ?? null
                    );
                    $gData = array_merge($gData, $recalculated);
                }
                $grades[] = [
                    'pendaftaran' => $pendaftaran,
                    'grades' => $gData,
                ];
            } else {
                $nilaiDosen = [];
                $nilaiLaporan = 0;
                $nilaiTugasAkhir = $autoTaScore;

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
        $nilaiDosenInput = $request->input('nilai_dosen', []);
        $nilaiLaporan = $request->input('nilai_laporan', 0);
        $nilaiTugasAkhir = $request->input('nilai_tugas_akhir', 0);
        $isGugur = (bool)$request->input('is_gugur', false);
        $alasanGugur = $request->input('alasan_gugur');

        $nilaiDosen = [];
        for ($i = 1; $i <= $praktikum->jumlah_modul; $i++) {
            if (isset($nilaiDosenInput[$i])) {
                $nilaiDosen[$i] = (int)$nilaiDosenInput[$i];
            } elseif (isset($nilaiDosenInput[(string)$i])) {
                $nilaiDosen[$i] = (int)$nilaiDosenInput[(string)$i];
            } elseif (isset($nilaiDosenInput[$i - 1])) {
                $nilaiDosen[$i] = (int)$nilaiDosenInput[$i - 1];
            } else {
                $nilaiDosen[$i] = 0;
            }
        }

        $aslabId = $pendaftaran->aslab_id ?? Aslab::first()?->id;

        // 1. Update Presensi & PenilaianPraktikum (Nilai Prak)
        foreach ($nilaiPraktikum as $modulIndex => $val) {
            $modulNum = (int)$modulIndex;
            if ($modulNum < 1 || $modulNum > $praktikum->jumlah_modul) continue;

            $targetTitle = "Modul " . $modulNum;
            $sched = $schedules->first(function ($s) use ($targetTitle, $modulNum, $pendaftaran) {
                $match = strcasecmp($s->judul_modul, $targetTitle) === 0 || str_contains(strtolower($s->judul_modul), "modul " . $modulNum);
                if ($pendaftaran->sesi_id) {
                    return $match && $s->sesi_id == $pendaftaran->sesi_id;
                }
                return $match;
            }) ?? $schedules->first(function ($s) use ($targetTitle, $modulNum) {
                return strcasecmp($s->judul_modul, $targetTitle) === 0 || str_contains(strtolower($s->judul_modul), "modul " . $modulNum);
            });

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

            $targetTitle = "Modul " . $modulNum;

            // Check if student already has a tugas_asistensi matching "Modul X"
            $existingTugas = $pendaftaran->tugasAsistensis->first(function ($t) use ($targetTitle, $modulNum) {
                return strcasecmp($t->judul, $targetTitle) === 0 || str_contains(strtolower($t->judul), "modul " . $modulNum);
            });

            $judulModul = $existingTugas ? $existingTugas->judul : $targetTitle;

            TugasAsistensi::updateOrCreate(
                [
                    'pendaftaran_id' => $pendaftaran->id,
                    'judul' => $judulModul,
                ],
                [
                    'aslab_id' => $aslabId,
                    'nilai' => (int)$val,
                    'status' => 'reviewed',
                ]
            );
        }

        // 3. Sync Tugas Akhir to Presensi & PenilaianPraktikum if a TA schedule exists
        $taSched = $schedules->first(function ($s) {
            $jTitle = strtolower($s->judul_modul);
            return str_contains($jTitle, 'tugas akhir') || str_contains($jTitle, 'ta ') || str_contains($jTitle, 'akhir');
        });
        if ($taSched && $nilaiTugasAkhir !== null) {
            $presensiTA = Presensi::firstOrCreate(
                [
                    'pendaftaran_id' => $pendaftaran->id,
                    'jadwal_id' => $taSched->id,
                ],
                [
                    'status' => 'hadir',
                ]
            );

            PenilaianPraktikum::updateOrCreate(
                ['presensi_id' => $presensiTA->id],
                [
                    'aslab_id' => $aslabId,
                    'nilai' => (int)$nilaiTugasAkhir,
                ]
            );
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
