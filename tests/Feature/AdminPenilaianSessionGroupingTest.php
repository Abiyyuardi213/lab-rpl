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
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminPenilaianSessionGroupingTest extends TestCase
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

    private function createPraktikum(string $name): Praktikum
    {
        return Praktikum::create([
            'nama_praktikum' => $name,
            'kode_praktikum' => 'PRK-'.bin2hex(random_bytes(6)),
            'periode_praktikum' => '2026/2027 Ganjil',
            'kuota_praktikan' => 50,
            'status_praktikum' => 'on_progress',
            'jumlah_modul' => 2,
        ]);
    }

    public function test_admin_can_view_praktikum_penilaian_grouped_by_session(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminUser = User::factory()->create([
            'role_id' => $adminRole->id,
            'username' => 'admin_'.uniqid(),
        ]);

        $praktikum = $this->createPraktikum('Praktikum Web '.uniqid());
        $sesi1 = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi Pagi',
            'hari' => 'Senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'kuota' => 30,
        ]);
        $sesi2 = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi Siang',
            'hari' => 'Senin',
            'jam_mulai' => '10:00:00',
            'jam_selesai' => '12:00:00',
            'kuota' => 30,
        ]);

        $jadwal1 = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi1->id,
            'judul_modul' => 'Modul 1 HTML',
            'tanggal' => Carbon::today()->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'ruangan' => 'Lab RPL 1',
        ]);

        $jadwal2 = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi2->id,
            'judul_modul' => 'Modul 1 HTML Siang',
            'tanggal' => Carbon::today()->toDateString(),
            'waktu_mulai' => '10:00:00',
            'waktu_selesai' => '12:00:00',
            'ruangan' => 'Lab RPL 2',
        ]);

        $response = $this->actingAs($adminUser)->get(route('admin.penilaian.praktikum', $praktikum->id));

        $response->assertStatus(200);
        $response->assertSee('Sesi Pagi');
        $response->assertSee('Sesi Siang');
        $response->assertSee('Modul 1 HTML');
        $response->assertSee('Modul 1 HTML Siang');
    }

    public function test_admin_can_view_jadwal_penilaian_with_all_students_in_session(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminUser = User::factory()->create([
            'role_id' => $adminRole->id,
            'username' => 'admin_'.uniqid(),
        ]);
        $praktikanRole = Role::firstOrCreate(['name' => 'Praktikan']);

        $praktikum = $this->createPraktikum('Praktikum Web Detail '.uniqid());
        $sesi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi Utama',
            'hari' => 'Rabu',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'kuota' => 30,
        ]);

        $jadwal = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul 2 CSS',
            'tanggal' => Carbon::today()->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'ruangan' => 'Lab RPL 1',
        ]);

        // Student registered in this session but has NOT scanned presence
        $studentUser = User::factory()->create([
            'role_id' => $praktikanRole->id,
            'name' => 'Budi Santoso',
            'username' => 'budi_'.uniqid(),
        ]);
        $studentPraktikan = Praktikan::create([
            'user_id' => $studentUser->id,
            'npm' => '06.2024.1.'.rand(10000, 99999),
            'no_wa' => '081234567888',
            'jenis_kelamin' => 'L',
        ]);
        $pendaftaran = PendaftaranPraktikum::create([
            'praktikan_id' => $studentPraktikan->id,
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'status' => 'verified',
            'no_hp' => '081234567888',
            'dosen_pengampu' => 'Dosen Test',
            'kelas' => 'pagi',
            'asal_kelas_mata_kuliah' => 'A',
        ]);

        $response = $this->actingAs($adminUser)->get(route('admin.penilaian.jadwal', $jadwal->id));

        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');
        $response->assertSee($studentPraktikan->npm);
    }

    public function test_admin_can_grade_student_even_without_prior_presensi(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminUser = User::factory()->create([
            'role_id' => $adminRole->id,
            'username' => 'admin_'.uniqid(),
        ]);
        $praktikanRole = Role::firstOrCreate(['name' => 'Praktikan']);

        $praktikum = $this->createPraktikum('Praktikum Web Grade '.uniqid());
        $sesi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi Sore',
            'hari' => 'Kamis',
            'jam_mulai' => '15:00:00',
            'jam_selesai' => '17:00:00',
            'kuota' => 30,
        ]);

        $jadwal = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul 3 JavaScript',
            'tanggal' => Carbon::today()->toDateString(),
            'waktu_mulai' => '15:00:00',
            'waktu_selesai' => '17:00:00',
            'ruangan' => 'Lab RPL 1',
        ]);

        $studentUser = User::factory()->create([
            'role_id' => $praktikanRole->id,
            'name' => 'Citra Lestari',
            'username' => 'citra_'.uniqid(),
        ]);
        $studentPraktikan = Praktikan::create([
            'user_id' => $studentUser->id,
            'npm' => '06.2024.1.'.rand(10000, 99999),
            'no_wa' => '081234567889',
            'jenis_kelamin' => 'P',
        ]);
        $pendaftaran = PendaftaranPraktikum::create([
            'praktikan_id' => $studentPraktikan->id,
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'status' => 'verified',
            'no_hp' => '081234567889',
            'dosen_pengampu' => 'Dosen Test',
            'kelas' => 'malam',
            'asal_kelas_mata_kuliah' => 'B',
        ]);

        // Admin grades with pendaftaran_id and jadwal_id without prior presensi
        $response = $this->actingAs($adminUser)->post(route('admin.penilaian.store'), [
            'pendaftaran_id' => $pendaftaran->id,
            'jadwal_id' => $jadwal->id,
            'nilai' => 95,
            'catatan' => 'Sangat bagus pengerjaan tugasnya',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('presensis', [
            'jadwal_id' => $jadwal->id,
            'pendaftaran_id' => $pendaftaran->id,
            'status' => 'hadir',
        ]);

        $presensi = Presensi::where('jadwal_id', $jadwal->id)->where('pendaftaran_id', $pendaftaran->id)->first();
        $this->assertNotNull($presensi);

        $this->assertDatabaseHas('penilaian_praktikums', [
            'presensi_id' => $presensi->id,
            'nilai' => 95,
            'catatan' => 'Sangat bagus pengerjaan tugasnya',
        ]);
    }
}
