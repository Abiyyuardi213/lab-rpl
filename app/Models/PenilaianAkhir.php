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

    public static function calculateGrades(PendaftaranPraktikum $pendaftaran, array $nilaiDosen = [], ?int $nilaiLaporan = null, ?int $nilaiTugasAkhir = null, bool $isGugur = false, ?string $alasanGugur = null, $schedules = null): array
    {
        $praktikum = $pendaftaran->praktikum;
        $jumlahModul = $praktikum->jumlah_modul;
        $adaTugasAkhir = $praktikum->ada_tugas_akhir;

        $presensis = $pendaftaran->relationLoaded('presensis') ? $pendaftaran->presensis : $pendaftaran->presensis()->with(['jadwal', 'penilaian'])->get();
        if (!$schedules) {
            $schedules = JadwalPraktikum::where('praktikum_id', $praktikum->id)
                ->orderBy('tanggal', 'asc')
                ->orderBy('waktu_mulai', 'asc')
                ->get();
        }

        // Auto-detect Tugas Akhir score from presensis if $nilaiTugasAkhir is null or 0
        $taPresensi = $presensis->first(function ($p) {
            if (!$p->jadwal) return false;
            $jTitle = strtolower($p->jadwal->judul_modul);
            return str_contains($jTitle, 'tugas akhir') || str_contains($jTitle, 'ta ') || str_contains($jTitle, 'akhir');
        });

        $autoTaScore = ($taPresensi && $taPresensi->penilaian) ? $taPresensi->penilaian->nilai : 0;

        if (($nilaiTugasAkhir === null || $nilaiTugasAkhir == 0) && $autoTaScore > 0) {
            $nilaiTugasAkhir = $autoTaScore;
        }

        $adaTugasAkhir = $praktikum->ada_tugas_akhir || $taPresensi !== null || ($nilaiTugasAkhir ?? 0) > 0;

        $prakScores = [];
        for ($i = 1; $i <= $jumlahModul; $i++) {
            $targetTitle = "Modul " . $i;
            $presensi = $presensis->first(function ($p) use ($targetTitle, $i) {
                if (!$p->jadwal) return false;
                $jTitle = $p->jadwal->judul_modul;
                return strcasecmp($jTitle, $targetTitle) === 0 
                    || str_contains(strtolower($jTitle), strtolower($targetTitle))
                    || str_contains(strtolower($jTitle), "modul " . $i);
            });
            $prakScores[$i] = ($presensi && $presensi->penilaian) ? $presensi->penilaian->nilai : 0;
        }

        $tugasList = $pendaftaran->relationLoaded('tugasAsistensis') ? $pendaftaran->tugasAsistensis : $pendaftaran->tugasAsistensis()->get();
        $astScores = [];
        for ($i = 1; $i <= $jumlahModul; $i++) {
            $targetTitle = "Modul " . $i;
            $tugas = $tugasList->first(function ($t) use ($targetTitle, $i) {
                $tTitle = $t->judul;
                return strcasecmp($tTitle, $targetTitle) === 0 
                    || str_contains(strtolower($tTitle), strtolower($targetTitle))
                    || str_contains(strtolower($tTitle), "modul " . $i);
            });
            $astScores[$i] = $tugas ? ($tugas->nilai ?? 0) : 0;
        }

        $dosScores = [];
        for ($i = 1; $i <= $jumlahModul; $i++) {
            $dosScores[$i] = isset($nilaiDosen[$i]) ? intval($nilaiDosen[$i]) : (isset($nilaiDosen['Modul ' . $i]) ? intval($nilaiDosen['Modul ' . $i]) : 0);
        }

        $sumPrak = array_sum($prakScores);
        if ($adaTugasAkhir) {
            $totalPrak = ($sumPrak + ($nilaiTugasAkhir ?? 0)) / ($jumlahModul + 1);
        } else {
            $totalPrak = $jumlahModul > 0 ? $sumPrak / $jumlahModul : 0;
        }

        $sumAst = array_sum($astScores);
        $totalAst = $jumlahModul > 0 ? $sumAst / $jumlahModul : 0;

        $totalPrakAst = (($nilaiLaporan ?? 0) + $totalPrak + $totalAst) / 3;

        $sumDos = array_sum($dosScores);
        $totalDos = $jumlahModul > 0 ? $sumDos / $jumlahModul : 0;

        $nilaiAkhir = ($totalPrakAst * 0.4) + ($totalDos * 0.6);

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
