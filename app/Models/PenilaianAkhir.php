<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PenilaianAkhir extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'pendaftaran_id',
        'nilai_dosen',
        'nilai_laporan',
        'nilai_tugas_akhir',
        'total_praktikum',
        'total_asistensi',
        'total_praktikum_asistensi',
        'total_dosen',
        'nilai_akhir',
        'nilai_huruf',
        'status_kelulusan',
        'is_gugur',
        'alasan_gugur',
    ];

    protected $casts = [
        'nilai_dosen' => 'array',
        'is_gugur' => 'boolean',
        'nilai_laporan' => 'integer',
        'nilai_tugas_akhir' => 'integer',
        'total_praktikum' => 'float',
        'total_asistensi' => 'float',
        'total_praktikum_asistensi' => 'float',
        'total_dosen' => 'float',
        'nilai_akhir' => 'float',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(PendaftaranPraktikum::class, 'pendaftaran_id');
    }

    /**
     * Calculate and return grades for a student's practical course registration.
     */
    public static function calculateGrades(PendaftaranPraktikum $pendaftaran, array $nilaiDosen = [], ?int $nilaiLaporan = null, ?int $nilaiTugasAkhir = null, bool $isGugur = false, ?string $alasanGugur = null): array
    {
        $praktikum = $pendaftaran->praktikum;
        $jumlahModul = $praktikum->jumlah_modul;
        $adaTugasAkhir = $praktikum->ada_tugas_akhir;

        // 1. Get Practical Scores (Prak) from penilaian_praktikums
        $presensis = $pendaftaran->presensis()->with('penilaian')->get();
        $schedules = JadwalPraktikum::where('praktikum_id', $praktikum->id)
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->get();

        $prakScores = [];
        foreach ($schedules as $index => $schedule) {
            $modulNum = $index + 1;
            if ($modulNum > $jumlahModul) {
                break;
            }

            $presensi = $presensis->firstWhere('jadwal_id', $schedule->id);
            $score = ($presensi && $presensi->penilaian) ? $presensi->penilaian->nilai : 0;
            $prakScores[$modulNum] = $score;
        }

        // Fill remaining modules with 0 if schedules are fewer than jumlah_modul
        for ($i = 1; $i <= $jumlahModul; $i++) {
            if (!isset($prakScores[$i])) {
                $prakScores[$i] = 0;
            }
        }

        // 2. Get Assistant Scores (Ast) from tugas_asistensis
        $tugasList = $pendaftaran->tugasAsistensis()->get();
        $astScores = [];
        foreach ($schedules as $index => $schedule) {
            $modulNum = $index + 1;
            if ($modulNum > $jumlahModul) {
                break;
            }

            // Find tugas by matching the title exactly with schedule's module title
            $tugas = $tugasList->firstWhere('judul', $schedule->judul_modul);
            $astScores[$modulNum] = $tugas ? ($tugas->nilai ?? 0) : 0;
        }

        // Fill remaining modules with 0
        for ($i = 1; $i <= $jumlahModul; $i++) {
            if (!isset($astScores[$i])) {
                $astScores[$i] = 0;
            }
        }

        // 3. Lecturer Scores (Dos)
        $dosScores = [];
        for ($i = 1; $i <= $jumlahModul; $i++) {
            $dosScores[$i] = isset($nilaiDosen[$i]) ? intval($nilaiDosen[$i]) : (isset($nilaiDosen['Modul ' . $i]) ? intval($nilaiDosen['Modul ' . $i]) : 0);
        }

        // 4. Calculate Total Prak
        $sumPrak = array_sum($prakScores);
        if ($adaTugasAkhir) {
            $totalPrak = ($sumPrak + ($nilaiTugasAkhir ?? 0)) / ($jumlahModul + 1);
        } else {
            $totalPrak = $jumlahModul > 0 ? $sumPrak / $jumlahModul : 0;
        }

        // 5. Calculate Total Ast
        $sumAst = array_sum($astScores);
        $totalAst = $jumlahModul > 0 ? $sumAst / $jumlahModul : 0;

        // 6. Calculate Total Prak + Ast
        $totalPrakAst = (($nilaiLaporan ?? 0) + $totalPrak + $totalAst) / 3;

        // 7. Calculate Total Dos
        $sumDos = array_sum($dosScores);
        $totalDos = $jumlahModul > 0 ? $sumDos / $jumlahModul : 0;

        // 8. Calculate Nilai Akhir
        $nilaiAkhir = ($totalPrakAst * 0.4) + ($totalDos * 0.6);

        // 9. Grade Letter (Huruf)
        $nilaiHuruf = 'E';
        if ($nilaiAkhir >= 91) {
            $nilaiHuruf = 'A+';
        } elseif ($nilaiAkhir >= 86) {
            $nilaiHuruf = 'A';
        } elseif ($nilaiAkhir >= 80) {
            $nilaiHuruf = 'A-';
        } elseif ($nilaiAkhir >= 76) {
            $nilaiHuruf = 'B+';
        } elseif ($nilaiAkhir >= 73) {
            $nilaiHuruf = 'B';
        } elseif ($nilaiAkhir >= 66) {
            $nilaiHuruf = 'B-';
        } elseif ($nilaiAkhir >= 61) {
            $nilaiHuruf = 'C+';
        } elseif ($nilaiAkhir >= 51) {
            $nilaiHuruf = 'C';
        } elseif ($nilaiAkhir >= 41) {
            $nilaiHuruf = 'D';
        } else {
            $nilaiHuruf = 'E';
        }

        // 10. Keterangan (Status Kelulusan)
        $statusKelulusan = in_array($nilaiHuruf, ['D', 'E'], true) ? 'TIDAK LULUS' : 'LULUS';

        return [
            'nilai_dosen' => $dosScores,
            'nilai_laporan' => $nilaiLaporan,
            'nilai_tugas_akhir' => $nilaiTugasAkhir,
            'total_praktikum' => round($totalPrak, 2),
            'total_asistensi' => round($totalAst, 2),
            'total_praktikum_asistensi' => round($totalPrakAst, 2),
            'total_dosen' => round($totalDos, 2),
            'nilai_akhir' => round($nilaiAkhir, 2),
            'nilai_huruf' => $nilaiHuruf,
            'status_kelulusan' => $isGugur ? 'TIDAK LULUS' : $statusKelulusan,
            'is_gugur' => $isGugur,
            'alasan_gugur' => $alasanGugur,
        ];
    }
}
