<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Praktikum;
use App\Models\JadwalPraktikum;
use App\Models\PendaftaranPraktikum;
use App\Models\Presensi;
use App\Models\PenilaianPraktikum;
use App\Models\PenilaianAkhir;

class PenilaianTugasAkhirTest extends TestCase
{
    public function test_penilaian_tugas_akhir_automatically_populates_in_rekap_penilaian_akhir(): void
    {
        $praktikum = new Praktikum();
        $praktikum->id = 'test-id';
        $praktikum->jumlah_modul = 4;
        $praktikum->ada_tugas_akhir = false;

        $pendaftaran = new PendaftaranPraktikum();
        $pendaftaran->id = 'pend-id';
        $pendaftaran->setRelation('praktikum', $praktikum);

        $jadwalTA = new JadwalPraktikum();
        $jadwalTA->id = 'jdwl-ta';
        $jadwalTA->judul_modul = 'TUGAS AKHIR PRAKTIKUM STRUKTUR DATA XVI';

        $penilaian = new PenilaianPraktikum();
        $penilaian->nilai = 88;

        $presensi = new Presensi();
        $presensi->setRelation('jadwal', $jadwalTA);
        $presensi->setRelation('penilaian', $penilaian);

        $pendaftaran->setRelation('presensis', collect([$presensi]));
        $pendaftaran->setRelation('tugasAsistensis', collect());

        $calculated = PenilaianAkhir::calculateGrades($pendaftaran, [], 0, null, false, null, collect());

        $this->assertEquals(88, $calculated['nilai_tugas_akhir']);
    }
}
