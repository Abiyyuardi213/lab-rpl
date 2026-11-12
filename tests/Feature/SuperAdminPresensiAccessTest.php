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

class SuperAdminPresensiAccessTest extends TestCase
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
            'kode_praktikum' => 'PRK-'.substr(uniqid(), -6),
            'periode_praktikum' => '2026/2027 Ganjil',
            'kuota_praktikan' => 50,
            'status_praktikum' => 'on_progress',
            'jumlah_modul' => 1,
        ]);
    }

    public function test_super_admin_can_generate_jadwal_qr_code(): void
    {
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin'], ['display_name' => 'Super Administrator']);
        $superAdmin = User::factory()->create([
            'role_id' => $superAdminRole->id,
            'username' => 'superadmin_'.uniqid(),
        ]);

        $praktikum = $this->createPraktikum('Praktikum SuperAdmin Test '.uniqid());

        $jadwal = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'judul_modul' => 'Modul 1 Test Super Admin',
            'tanggal' => Carbon::today()->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'ruangan' => 'Lab RPL 1',
        ]);

        $response = $this->actingAs($superAdmin)
            ->get(route('presensi.generate-jadwal-qr', $jadwal->id));

        $response->assertStatus(200);
        $response->assertViewIs('aslab.presensi.jadwal-qr');
        $response->assertViewHas('qrCode');
        $response->assertViewHas('jadwal');
        $this->assertNotNull($jadwal->fresh()->token);
    }

    public function test_super_admin_can_store_manual_attendance_and_destroy_it(): void
    {
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin'], ['display_name' => 'Super Administrator']);
        $superAdmin = User::factory()->create([
            'role_id' => $superAdminRole->id,
            'username' => 'superadmin_'.uniqid(),
        ]);

        $praktikanRole = Role::firstOrCreate(['name' => 'Praktikan'], ['display_name' => 'Praktikan']);
        $praktikanUser = User::factory()->create([
            'role_id' => $praktikanRole->id,
            'username' => 'praktikan_'.uniqid(),
        ]);
        $praktikan = Praktikan::create([
            'user_id' => $praktikanUser->id,
            'npm' => '06.2024.1.'.rand(10000, 99999),
            'no_hp' => '081234567890',
            'jenis_kelamin' => 'L',
        ]);

        $praktikum = $this->createPraktikum('Praktikum SuperAdmin Manual '.uniqid());

        $sesi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi Pagi',
            'hari' => 'Senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'kuota' => 30,
        ]);

        $pendaftaran = PendaftaranPraktikum::create([
            'praktikan_id' => $praktikan->id,
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'status' => 'verified',
            'no_hp' => '081234567890',
            'dosen_pengampu' => 'Dosen Test',
            'kelas' => 'pagi',
            'asal_kelas_mata_kuliah' => 'P',
        ]);

        $jadwal = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul Manual Test',
            'tanggal' => Carbon::today()->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'ruangan' => 'Lab RPL 1',
        ]);

        // Super Admin submits manual attendance
        $postResponse = $this->actingAs($superAdmin)
            ->postJson(route('presensi.manual'), [
                'jadwal_id' => $jadwal->id,
                'pendaftaran_id' => $pendaftaran->id,
                'status' => 'hadir',
            ]);

        $postResponse->assertStatus(200);
        $postResponse->assertJson(['success' => true]);

        $presensi = Presensi::where('jadwal_id', $jadwal->id)
            ->where('pendaftaran_id', $pendaftaran->id)
            ->first();
        $this->assertNotNull($presensi);

        // Super Admin cancels/destroys attendance
        $deleteResponse = $this->actingAs($superAdmin)
            ->deleteJson(route('presensi.destroy', $presensi->id));

        $deleteResponse->assertStatus(200);
        $deleteResponse->assertJson(['success' => true]);
        $this->assertFalse(Presensi::where('id', $presensi->id)->exists());
    }

    public function test_super_admin_can_download_jadwal_qr_pdf(): void
    {
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin'], ['display_name' => 'Super Administrator']);
        $superAdmin = User::factory()->create([
            'role_id' => $superAdminRole->id,
            'username' => 'superadmin_'.uniqid(),
        ]);

        $praktikum = $this->createPraktikum('Praktikum SuperAdmin PDF '.uniqid());

        $jadwal = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'judul_modul' => 'Modul 1 Test PDF',
            'tanggal' => Carbon::today()->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'ruangan' => 'Lab RPL 1',
        ]);

        $response = $this->actingAs($superAdmin)
            ->get(route('presensi.download-jadwal-pdf', $jadwal->id));

        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-disposition') ?? '', 'attachment'));
    }

    public function test_generate_jadwal_qr_filters_not_present_students_by_sesi(): void
    {
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin'], ['display_name' => 'Super Administrator']);
        $superAdmin = User::factory()->create([
            'role_id' => $superAdminRole->id,
            'username' => 'superadmin_'.uniqid(),
        ]);

        $praktikanRole = Role::firstOrCreate(['name' => 'Praktikan'], ['display_name' => 'Praktikan']);

        $praktikum = $this->createPraktikum('Praktikum Multi Sesi '.uniqid());

        $sesiPagi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi Pagi',
            'hari' => 'Senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'kuota' => 30,
        ]);

        $sesiMalam = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi Malam',
            'hari' => 'Senin',
            'jam_mulai' => '18:00:00',
            'jam_selesai' => '20:00:00',
            'kuota' => 30,
        ]);

        // Student A -> Sesi Pagi
        $userA = User::factory()->create(['role_id' => $praktikanRole->id, 'username' => 'user_a_'.uniqid()]);
        $praktikanA = Praktikan::create([
            'user_id' => $userA->id,
            'npm' => '06.2024.1.'.rand(10000, 99999),
            'no_hp' => '081234567891',
            'jenis_kelamin' => 'L',
        ]);
        $pendaftaranA = PendaftaranPraktikum::create([
            'praktikan_id' => $praktikanA->id,
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesiPagi->id,
            'status' => 'verified',
            'no_hp' => '081234567891',
            'dosen_pengampu' => 'Dosen Test',
            'kelas' => 'pagi',
            'asal_kelas_mata_kuliah' => 'P',
        ]);

        // Student B -> Sesi Malam
        $userB = User::factory()->create(['role_id' => $praktikanRole->id, 'username' => 'user_b_'.uniqid()]);
        $praktikanB = Praktikan::create([
            'user_id' => $userB->id,
            'npm' => '06.2024.1.'.rand(10000, 99999),
            'no_hp' => '081234567892',
            'jenis_kelamin' => 'P',
        ]);
        $pendaftaranB = PendaftaranPraktikum::create([
            'praktikan_id' => $praktikanB->id,
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesiMalam->id,
            'status' => 'verified',
            'no_hp' => '081234567892',
            'dosen_pengampu' => 'Dosen Test',
            'kelas' => 'malam',
            'asal_kelas_mata_kuliah' => 'V',
        ]);

        // Jadwal for Sesi Pagi
        $jadwalPagi = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesiPagi->id,
            'judul_modul' => 'Modul Sesi Pagi',
            'tanggal' => Carbon::today()->toDateString(),
            'waktu_mulai' => '00:00:00',
            'waktu_selesai' => '23:59:59',
            'ruangan' => 'Lab RPL 1',
        ]);

        $response = $this->actingAs($superAdmin)
            ->get(route('presensi.generate-jadwal-qr', $jadwalPagi->id));

        $response->assertStatus(200);
        $notPresent = $response->viewData('notPresentStudents');

        // Only Student A should be present in notPresentStudents list
        $this->assertTrue($notPresent->contains('id', $pendaftaranA->id));
        $this->assertFalse($notPresent->contains('id', $pendaftaranB->id));

        // Attempt storeManual for Student B (different session) should fail
        $failResponse = $this->actingAs($superAdmin)
            ->postJson(route('presensi.manual'), [
                'jadwal_id' => $jadwalPagi->id,
                'pendaftaran_id' => $pendaftaranB->id,
                'status' => 'hadir',
            ]);

        $failResponse->assertStatus(400);
        $failResponse->assertJson(['success' => false]);
        $this->assertStringContainsString('tidak sesuai dengan jadwal ini', $failResponse->json('message'));
    }

    public function test_unauthorized_user_is_rejected_from_generate_jadwal_qr(): void
    {
        $praktikanRole = Role::firstOrCreate(['name' => 'Praktikan'], ['display_name' => 'Praktikan']);
        $praktikanUser = User::factory()->create([
            'role_id' => $praktikanRole->id,
            'username' => 'praktikan_'.uniqid(),
        ]);

        $praktikum = $this->createPraktikum('Praktikum Rejection Test '.uniqid());

        $jadwal = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'judul_modul' => 'Modul Rejection Test',
            'tanggal' => Carbon::today()->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'ruangan' => 'Lab RPL 1',
        ]);

        $response = $this->actingAs($praktikanUser)
            ->get(route('presensi.generate-jadwal-qr', $jadwal->id));

        $response->assertSessionHas('error', 'Akses ditolak.');
    }

    public function test_generate_jadwal_qr_lists_alfa_student_and_store_manual_updates_alfa_to_hadir(): void
    {
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin'], ['display_name' => 'Super Administrator']);
        $superAdmin = User::factory()->create([
            'role_id' => $superAdminRole->id,
            'username' => 'superadmin_'.uniqid(),
        ]);

        $praktikanRole = Role::firstOrCreate(['name' => 'Praktikan'], ['display_name' => 'Praktikan']);
        $praktikanUser = User::factory()->create([
            'role_id' => $praktikanRole->id,
            'username' => 'praktikan_'.uniqid(),
        ]);
        $praktikan = Praktikan::create([
            'user_id' => $praktikanUser->id,
            'npm' => '06.2024.1.'.rand(10000, 99999),
            'no_hp' => '081234567890',
            'jenis_kelamin' => 'L',
        ]);

        $praktikum = $this->createPraktikum('Praktikum Manual Alfa '.uniqid());
        $sesi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi Pagi',
            'hari' => 'Kamis',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'kuota' => 30,
        ]);

        $pendaftaran = PendaftaranPraktikum::create([
            'praktikan_id' => $praktikan->id,
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'status' => 'verified',
            'no_hp' => '081234567890',
            'dosen_pengampu' => 'Dosen Test',
            'kelas' => 'pagi',
            'asal_kelas_mata_kuliah' => 'P',
        ]);

        $jadwal = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul 3 Alfa Fix',
            'tanggal' => Carbon::yesterday()->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'ruangan' => 'Lab RPL',
        ]);

        $alfaPresensi = Presensi::create([
            'jadwal_id' => $jadwal->id,
            'pendaftaran_id' => $pendaftaran->id,
            'jam_masuk' => Carbon::yesterday()->setTime(10, 0),
            'status' => 'alfa',
        ]);

        $qrResponse = $this->actingAs($superAdmin)
            ->get(route('presensi.generate-jadwal-qr', $jadwal->id));

        $qrResponse->assertStatus(200);
        $this->assertTrue($qrResponse->viewData('notPresentStudents')->contains('id', $pendaftaran->id));

        $manualResponse = $this->actingAs($superAdmin)
            ->postJson(route('presensi.manual'), [
                'jadwal_id' => $jadwal->id,
                'pendaftaran_id' => $pendaftaran->id,
                'status' => 'hadir',
            ]);

        $manualResponse->assertStatus(200)->assertJson(['success' => true]);
        $this->assertEquals('hadir', $alfaPresensi->fresh()->status);
    }

    public function test_admin_can_update_schedule_to_susulan_without_sesi_id(): void
    {
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin'], ['display_name' => 'Super Administrator']);
        $superAdmin = User::factory()->create([
            'role_id' => $superAdminRole->id,
            'username' => 'superadmin_'.uniqid(),
        ]);

        $praktikum = $this->createPraktikum('Praktikum Edit Susulan '.uniqid());

        $jadwal = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => null,
            'judul_modul' => 'Susulan Modul 1&2',
            'tanggal' => Carbon::yesterday()->toDateString(),
            'waktu_mulai' => '09:00:00',
            'waktu_selesai' => '11:20:00',
            'ruangan' => 'Lab LCSE',
            'is_susulan' => false,
        ]);

        $response = $this->actingAs($superAdmin)
            ->patch(route('admin.jadwal-praktikum.update', $jadwal->id), [
                'praktikum_id' => $praktikum->id,
                'judul_modul' => 'Susulan Modul 1&2',
                'tanggal' => Carbon::yesterday()->toDateString(),
                'waktu_mulai' => '09:00',
                'waktu_selesai' => '11:20',
                'ruangan' => 'Lab LCSE',
                'is_susulan' => '1',
            ]);

        $response->assertSessionHas('success');
        $jadwal->refresh();
        $this->assertTrue($jadwal->is_susulan);
        $this->assertNull($jadwal->sesi_id);
    }
}
