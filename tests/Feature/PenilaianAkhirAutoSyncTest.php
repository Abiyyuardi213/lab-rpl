<?php

namespace Tests\Feature;

use App\Models\JadwalPraktikum;
use App\Models\PendaftaranPraktikum;
use App\Models\PenilaianAkhir;
use App\Models\PenilaianPraktikum;
use App\Models\Praktikan;
use App\Models\Praktikum;
use App\Models\Presensi;
use App\Models\SesiPraktikum;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PenilaianAkhirAutoSyncTest extends TestCase
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

    public function test_penilaian_akhir_auto_syncs_and_recalculates_when_module_score_is_added_later(): void
    {
        // 1. Setup Praktikum with 2 modules
        $praktikum = Praktikum::create([
            'nama_praktikum' => 'Praktikum Basis Data AutoSync '.uniqid(),
            'kode_praktikum' => 'PBD-'.strtoupper(bin2hex(random_bytes(4))),
            'periode_praktikum' => '2026/2027 Ganjil',
            'kuota_praktikan' => 50,
            'jumlah_modul' => 2,
            'ada_tugas_akhir' => false,
            'status_praktikum' => 'on_progress',
        ]);

        $sesi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi 1 '.uniqid(),
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'kuota' => 30,
        ]);

        $user = User::factory()->create([
            'name' => 'Dimas Test',
            'username' => 'dimas_'.uniqid(),
            'email' => 'dimas.'.uniqid().'@labrpl.com',
        ]);

        $praktikan = Praktikan::create([
            'user_id' => $user->id,
            'npm' => '06.'.rand(2020, 2026).'.1.'.rand(10000, 99999),
            'nama' => 'Dimas Test',
            'nomor_telepon' => '081234567890',
            'status' => 'aktif',
        ]);

        $pendaftaran = PendaftaranPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'praktikan_id' => $praktikan->id,
            'sesi_id' => $sesi->id,
            'no_hp' => '081234567890',
            'dosen_pengampu' => 'Dr. Bambang Agus Herlambang',
            'asal_kelas_mata_kuliah' => 'P',
            'status' => 'verified',
        ]);

        $jadwal1 = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul 1',
            'tanggal' => now()->toDateString(),
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '10:00',
            'ruangan' => 'Lab 1',
        ]);

        // 2. Pre-create PenilaianAkhir with total_praktikum = 0 (mimicking premature save before module grading)
        $penilaianAkhir = PenilaianAkhir::create([
            'pendaftaran_id' => $pendaftaran->id,
            'nilai_dosen' => [1 => 80, 2 => 80],
            'nilai_laporan' => 85,
            'nilai_tugas_akhir' => 0,
            'total_praktikum' => 0.00,
            'total_asistensi' => 0.00,
            'total_praktikum_asistensi' => 28.33,
            'total_dosen' => 80.00,
            'nilai_akhir' => 59.33,
            'nilai_huruf' => 'C',
            'status_kelulusan' => 'LULUS',
            'is_gugur' => false,
            'alasan_gugur' => null,
        ]);

        $this->assertEquals(0.00, $penilaianAkhir->fresh()->total_praktikum);

        // 3. Now grading happens later: Admin inputs score 80 for Modul 1
        $presensi = Presensi::create([
            'jadwal_id' => $jadwal1->id,
            'pendaftaran_id' => $pendaftaran->id,
            'status' => 'hadir',
            'jam_masuk' => now(),
        ]);

        PenilaianPraktikum::create([
            'presensi_id' => $presensi->id,
            'aslab_id' => null,
            'nilai' => 80,
            'catatan' => 'Bagus',
        ]);

        // 4. Trigger syncRegistration (simulating admin viewing Penilaian Akhir page or export)
        $pendaftaran->unsetRelation('presensis');
        $syncResult = PenilaianAkhir::syncRegistration($pendaftaran);

        // 5. Assert: total_praktikum was 0, now recalculated to 80 / 2 = 40.00
        $this->assertTrue($syncResult['is_db']);
        $this->assertEquals(40.00, $syncResult['data']['total_praktikum']);

        // Assert: Database record was automatically updated (Self-Healing)
        $updatedRecord = $penilaianAkhir->fresh();
        $this->assertEquals(40.00, $updatedRecord->total_praktikum);

        // Assert: Manual fields (nilai_dosen, nilai_laporan) are preserved intact
        $this->assertEquals([1 => 80, 2 => 80], $updatedRecord->nilai_dosen);
        $this->assertEquals(85, $updatedRecord->nilai_laporan);
    }

    public function test_admin_storing_module_grade_auto_syncs_existing_penilaian_akhir(): void
    {
        $adminRole = \App\Models\Role::firstOrCreate(['name' => 'Admin']);
        $adminUser = User::factory()->create([
            'role_id' => $adminRole->id,
            'username' => 'admin_'.uniqid(),
        ]);

        $praktikum = Praktikum::create([
            'nama_praktikum' => 'Praktikum Sync On Write '.uniqid(),
            'kode_praktikum' => 'PBD-'.strtoupper(bin2hex(random_bytes(4))),
            'periode_praktikum' => '2026/2027 Ganjil',
            'kuota_praktikan' => 50,
            'jumlah_modul' => 2,
            'ada_tugas_akhir' => false,
            'status_praktikum' => 'on_progress',
        ]);

        $sesi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi Sync '.uniqid(),
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'kuota' => 30,
        ]);

        $studentUser = User::factory()->create([
            'username' => 'mhs_'.uniqid(),
        ]);
        $praktikan = Praktikan::create([
            'user_id' => $studentUser->id,
            'npm' => '06.'.rand(2020, 2026).'.1.'.rand(10000, 99999),
            'nama' => 'Student Sync',
            'nomor_telepon' => '081234567890',
            'status' => 'aktif',
        ]);

        $pendaftaran = PendaftaranPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'praktikan_id' => $praktikan->id,
            'sesi_id' => $sesi->id,
            'no_hp' => '081234567890',
            'dosen_pengampu' => 'Dr. Bambang Agus Herlambang',
            'asal_kelas_mata_kuliah' => 'P',
            'status' => 'verified',
        ]);

        $jadwal1 = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul 1',
            'tanggal' => now()->toDateString(),
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '10:00',
            'ruangan' => 'Lab 1',
        ]);

        $penilaianAkhir = PenilaianAkhir::create([
            'pendaftaran_id' => $pendaftaran->id,
            'nilai_dosen' => [1 => 80, 2 => 80],
            'nilai_laporan' => 85,
            'nilai_tugas_akhir' => 0,
            'total_praktikum' => 0.00,
            'total_asistensi' => 0.00,
            'total_praktikum_asistensi' => 28.33,
            'total_dosen' => 80.00,
            'nilai_akhir' => 59.33,
            'nilai_huruf' => 'C',
            'status_kelulusan' => 'LULUS',
            'is_gugur' => false,
            'alasan_gugur' => null,
        ]);

        $presensi = Presensi::create([
            'jadwal_id' => $jadwal1->id,
            'pendaftaran_id' => $pendaftaran->id,
            'status' => 'hadir',
            'jam_masuk' => now(),
        ]);

        // Submit grade 90 via Admin controller endpoint
        $response = $this->actingAs($adminUser)->post('/administrator/penilaian', [
            'presensi_id' => $presensi->id,
            'nilai' => 90,
            'catatan' => 'Sangat Bagus',
        ]);

        $response->assertSessionHasNoErrors();

        // Assert: total_praktikum in database automatically recalculated to 90 / 2 = 45.00
        $this->assertEquals(45.00, $penilaianAkhir->fresh()->total_praktikum);
    }

    public function test_admin_update_override_creates_presensi_with_schedule_jam_masuk_when_student_has_no_prior_presensi(): void
    {
        $adminRole = \App\Models\Role::firstOrCreate(['name' => 'Admin']);
        $adminUser = User::factory()->create([
            'role_id' => $adminRole->id,
            'username' => 'admin_'.uniqid(),
        ]);

        $praktikum = Praktikum::create([
            'nama_praktikum' => 'Praktikum Override Test '.uniqid(),
            'kode_praktikum' => 'PBD-'.strtoupper(bin2hex(random_bytes(4))),
            'periode_praktikum' => '2026/2027 Ganjil',
            'kuota_praktikan' => 50,
            'jumlah_modul' => 2,
            'ada_tugas_akhir' => false,
            'status_praktikum' => 'on_progress',
        ]);

        $sesi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi Override '.uniqid(),
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'kuota' => 30,
        ]);

        $studentUser = User::factory()->create([
            'username' => 'mhs_override_'.uniqid(),
        ]);
        $praktikan = Praktikan::create([
            'user_id' => $studentUser->id,
            'npm' => '06.'.rand(2020, 2026).'.1.'.rand(10000, 99999),
            'nama' => 'Student Override',
            'nomor_telepon' => '081234567890',
            'status' => 'aktif',
        ]);

        $pendaftaran = PendaftaranPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'praktikan_id' => $praktikan->id,
            'sesi_id' => $sesi->id,
            'no_hp' => '081234567890',
            'dosen_pengampu' => 'Dr. Bambang Agus Herlambang',
            'asal_kelas_mata_kuliah' => 'P',
            'status' => 'verified',
        ]);

        $jadwalTanggal = '2026-11-05';
        $jadwalWaktuMulai = '08:00';
        $jadwal1 = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul 1',
            'tanggal' => $jadwalTanggal,
            'waktu_mulai' => $jadwalWaktuMulai,
            'waktu_selesai' => '10:00',
            'ruangan' => 'Lab 1',
        ]);

        // Student has NO prior presensi for Modul 1
        $this->assertFalse(Presensi::where('jadwal_id', $jadwal1->id)->where('pendaftaran_id', $pendaftaran->id)->exists());

        // Admin submits override score for Modul 1 via PUT /administrator/penilaian-akhir/{pendaftaran_id}
        $response = $this->actingAs($adminUser)->put("/administrator/penilaian-akhir/{$pendaftaran->id}", [
            'nilai_praktikum' => [1 => 85],
            'nilai_asistensi' => [1 => 80],
            'nilai_dosen' => [1 => 75, 2 => 75],
            'nilai_laporan' => 80,
            'nilai_tugas_akhir' => 0,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        // Verify presensi record was automatically created with status 'hadir' and jam_masuk matching the schedule
        $presensi = Presensi::where('jadwal_id', $jadwal1->id)
            ->where('pendaftaran_id', $pendaftaran->id)
            ->first();

        $this->assertNotNull($presensi);
        $this->assertEquals('hadir', $presensi->status);
        $this->assertNotNull($presensi->jam_masuk);
        $expectedJamMasuk = \Carbon\Carbon::parse($jadwalTanggal.' '.$jadwalWaktuMulai)->format('Y-m-d H:i');
        $this->assertEquals($expectedJamMasuk, \Carbon\Carbon::parse($presensi->jam_masuk)->format('Y-m-d H:i'));

        // Verify grade was saved
        $penilaian = PenilaianPraktikum::where('presensi_id', $presensi->id)->first();
        $this->assertNotNull($penilaian);
        $this->assertEquals(85, $penilaian->nilai);

        // Verify PenilaianAkhir is calculated
        $penilaianAkhir = PenilaianAkhir::where('pendaftaran_id', $pendaftaran->id)->first();
        $this->assertNotNull($penilaianAkhir);
        $this->assertEquals(42.50, $penilaianAkhir->total_praktikum);
    }
}
