<?php

namespace Tests\Feature;

use App\Models\JadwalPraktikum;
use App\Models\PendaftaranPraktikum;
use App\Models\Praktikan;
use App\Models\Praktikum;
use App\Models\Presensi;
use App\Models\Role;
use App\Models\SesiPraktikum;
use App\Models\User;
use App\Services\PresensiAlfaService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PraktikanGugurConsecutiveAlfaTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'lab_rpl',
        ]);
    }

    private function createPraktikum(): Praktikum
    {
        return Praktikum::create([
            'nama_praktikum' => 'Praktikum Gugur Test '.uniqid(),
            'kode_praktikum' => 'PRK-'.bin2hex(random_bytes(6)),
            'periode_praktikum' => '2026/2027 Ganjil',
            'kuota_praktikan' => 50,
            'status_praktikum' => 'on_progress',
            'jumlah_modul' => 3,
        ]);
    }

    private function createStudent(Praktikum $praktikum, SesiPraktikum $sesi, string $name = 'Budi'): PendaftaranPraktikum
    {
        $role = Role::firstOrCreate(['name' => 'Praktikan']);
        $user = User::factory()->create([
            'role_id' => $role->id,
            'name' => $name,
            'username' => 'user_'.uniqid(),
        ]);
        $praktikan = Praktikan::create([
            'user_id' => $user->id,
            'npm' => '06.2024.1.'.rand(10000, 99999),
            'no_wa' => '081234567888',
            'jenis_kelamin' => 'L',
        ]);

        return PendaftaranPraktikum::create([
            'praktikan_id' => $praktikan->id,
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'status' => 'verified',
            'no_hp' => '081234567888',
            'dosen_pengampu' => 'Dosen Test',
            'kelas' => 'pagi',
            'asal_kelas_mata_kuliah' => 'A',
        ]);
    }

    public function test_student_becomes_gugur_when_alfa_twice_consecutively(): void
    {
        $praktikum = $this->createPraktikum();
        $sesi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi 1',
            'hari' => 'Senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'kuota' => 30,
        ]);

        $student = $this->createStudent($praktikum, $sesi, 'Agus');

        // Jadwal Modul 1 (kemarin selesai)
        $jadwal1 = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul 1 Dasar',
            'tanggal' => Carbon::yesterday()->subDay()->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'ruangan' => 'Lab RPL 1',
            'is_susulan' => false,
        ]);

        // Jadwal Modul 2 (kemarin selesai)
        $jadwal2 = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul 2 Lanjutan',
            'tanggal' => Carbon::yesterday()->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'ruangan' => 'Lab RPL 1',
            'is_susulan' => false,
        ]);

        $service = app(PresensiAlfaService::class);

        // Tandai selesai jadwal 1
        $service->markSchedule($jadwal1);
        $student->refresh();
        $this->assertEquals('verified', $student->status, 'Student should still be verified after 1st alfa');

        // Tandai selesai jadwal 2 (2x berturut-turut alfa)
        $service->markSchedule($jadwal2);
        $student->refresh();
        $this->assertEquals('gugur', $student->status, 'Student should be marked as gugur after 2 consecutive alfas');
    }

    public function test_student_does_not_become_gugur_when_alfa_is_not_consecutive(): void
    {
        $praktikum = $this->createPraktikum();
        $sesi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi 1',
            'hari' => 'Senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'kuota' => 30,
        ]);

        $student = $this->createStudent($praktikum, $sesi, 'Bambang');

        // Jadwal Modul 1 (kemarin lusa)
        $jadwal1 = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul 1 Dasar',
            'tanggal' => Carbon::yesterday()->subDays(2)->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'ruangan' => 'Lab RPL 1',
            'is_susulan' => false,
        ]);

        // Jadwal Modul 2 (kemarin) - Mahasiswa HADIR
        $jadwal2 = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul 2 Lanjutan',
            'tanggal' => Carbon::yesterday()->subDay()->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'ruangan' => 'Lab RPL 1',
            'is_susulan' => false,
        ]);
        Presensi::create([
            'jadwal_id' => $jadwal2->id,
            'pendaftaran_id' => $student->id,
            'jam_masuk' => Carbon::yesterday()->subDay()->setTime(8, 5),
            'status' => 'hadir',
        ]);

        // Jadwal Modul 3 (kemarin)
        $jadwal3 = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul 3 Mahir',
            'tanggal' => Carbon::yesterday()->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'ruangan' => 'Lab RPL 1',
            'is_susulan' => false,
        ]);

        $service = app(PresensiAlfaService::class);
        $service->markSchedule($jadwal1); // Alfa Modul 1
        $service->markSchedule($jadwal3); // Alfa Modul 3

        $student->refresh();
        $this->assertEquals('verified', $student->status, 'Student should remain verified because alfas are not consecutive');
    }

    public function test_student_does_not_become_gugur_when_previous_alfa_resolved_by_susulan(): void
    {
        $praktikum = $this->createPraktikum();
        $sesi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi 1',
            'hari' => 'Senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'kuota' => 30,
        ]);

        $student = $this->createStudent($praktikum, $sesi, 'Citra');

        // Jadwal Modul 1 Reguler (alfa)
        $jadwal1 = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul 1 Dasar',
            'tanggal' => Carbon::yesterday()->subDays(2)->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'ruangan' => 'Lab RPL 1',
            'is_susulan' => false,
        ]);

        // Jadwal Modul 1 SUSULAN (mahasiswa hadir di sesi susulan ini!)
        $jadwal1Susulan = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'judul_modul' => 'Modul 1 Dasar',
            'tanggal' => Carbon::yesterday()->subDay()->toDateString(),
            'waktu_mulai' => '13:00:00',
            'waktu_selesai' => '15:00:00',
            'ruangan' => 'Lab RPL 1',
            'is_susulan' => true,
        ]);
        Presensi::create([
            'jadwal_id' => $jadwal1Susulan->id,
            'pendaftaran_id' => $student->id,
            'jam_masuk' => Carbon::yesterday()->subDay()->setTime(13, 10),
            'status' => 'hadir',
            'is_susulan' => true,
        ]);

        // Jadwal Modul 2 Reguler (kemarin selesai, mahasiswa alfa di sini)
        $jadwal2 = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul 2 Lanjutan',
            'tanggal' => Carbon::yesterday()->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'ruangan' => 'Lab RPL 1',
            'is_susulan' => false,
        ]);

        $service = app(PresensiAlfaService::class);
        $service->markSchedule($jadwal1); // Reguler modul 1 selesai
        $service->markSchedule($jadwal2); // Reguler modul 2 selesai

        $student->refresh();
        $this->assertEquals('verified', $student->status, 'Student should remain verified because Modul 1 was resolved via susulan');
    }
}
