<?php

namespace Tests\Feature;

use App\Models\Aslab;
use App\Models\DigitNpm;
use App\Models\Dosen;
use App\Models\JadwalPraktikum;
use App\Models\Kelas;
use App\Models\LabSetting;
use App\Models\PendaftaranPraktikum;
use App\Models\Penugasan;
use App\Models\Praktikan;
use App\Models\Praktikum;
use App\Models\Presensi;
use App\Models\Role;
use App\Models\SesiPraktikum;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminRoleWhiteboxTest extends TestCase
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

    private function createAdminUser(): User
    {
        $role = Role::firstOrCreate(['name' => 'Admin']);
        do {
            $uniqueId = rand(10000, 99999);
            $username = 'admin_'.$uniqueId.'_'.time();
        } while (User::where('username', $username)->exists());

        return User::factory()->create([
            'role_id' => $role->id,
            'username' => $username,
            'email' => 'admin_'.$uniqueId.'_'.time().'@example.com',
            'name' => 'Admin Test '.$uniqueId,
        ]);
    }

    private function createSuperAdminUser(): User
    {
        $role = Role::firstOrCreate(['name' => 'Super Admin']);
        do {
            $uniqueId = rand(10000, 99999);
            $username = 'superadmin_'.$uniqueId.'_'.time();
        } while (User::where('username', $username)->exists());

        return User::factory()->create([
            'role_id' => $role->id,
            'username' => $username,
            'email' => 'superadmin_'.$uniqueId.'_'.time().'@example.com',
            'name' => 'Super Admin Test '.$uniqueId,
        ]);
    }

    private function createAslabUser(): array
    {
        $role = Role::firstOrCreate(['name' => 'Aslab']);
        do {
            $uniqueId = rand(10000, 99999);
            $npm = '06.'.date('Y').'.1.'.$uniqueId;
        } while (User::where('username', $npm)->exists());

        $user = User::factory()->create([
            'role_id' => $role->id,
            'username' => $npm,
            'name' => 'Aslab Test '.$uniqueId,
        ]);

        $aslab = Aslab::create([
            'user_id' => $user->id,
            'npm' => $npm,
            'jabatan' => 'Anggota Laboratorium',
            'no_hp' => '081234567891',
            'jurusan' => 'Teknik Informatika',
            'angkatan' => date('Y'),
        ]);

        return [$user, $aslab];
    }

    private function createPraktikanUser(): array
    {
        $role = Role::firstOrCreate(['name' => 'Praktikan']);
        do {
            $uniqueId = rand(10000, 99999);
            $npm = '06.'.date('Y').'.1.'.$uniqueId;
        } while (User::where('username', $npm)->exists());

        $user = User::factory()->create([
            'role_id' => $role->id,
            'username' => $npm,
            'name' => 'Praktikan Test '.$uniqueId,
        ]);

        $praktikan = Praktikan::create([
            'user_id' => $user->id,
            'npm' => $npm,
            'no_hp' => '081234567890',
            'jurusan' => 'Teknik Informatika',
            'angkatan' => date('Y'),
        ]);

        return [$user, $praktikan];
    }

    private function createPraktikum(array $attributes = []): Praktikum
    {
        $unique = rand(1000, 9999);

        return Praktikum::create(array_merge([
            'kode_praktikum' => 'PRK-'.strtoupper(bin2hex(random_bytes(3))),
            'nama_praktikum' => 'Praktikum Uji Admin '.$unique,
            'periode_praktikum' => '2026/2027 Ganjil',
            'kuota_praktikan' => 40,
            'status_praktikum' => 'on_progress',
            'jumlah_modul' => 3,
            'ada_tugas_akhir' => false,
            'nomor_surat_prefix' => 'LAB-RPL/SERT',
        ], $attributes));
    }

    private function createSesi(Praktikum $praktikum, array $attributes = []): SesiPraktikum
    {
        return SesiPraktikum::create(array_merge([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi Pagi '.rand(10, 99),
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'kuota' => 20,
        ], $attributes));
    }

    private function createPendaftaran(Praktikan $praktikan, Praktikum $praktikum, SesiPraktikum $sesi, string $status = 'verified'): PendaftaranPraktikum
    {
        return PendaftaranPraktikum::create([
            'praktikan_id' => $praktikan->id,
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'status' => $status,
            'no_hp' => '081234567890',
            'kelas' => 'Pagi',
            'asal_kelas_mata_kuliah' => 'A',
            'dosen_pengampu' => 'Dosen Pengampu Uji',
        ]);
    }

    private function createJadwal(Praktikum $praktikum, SesiPraktikum $sesi, array $attributes = []): JadwalPraktikum
    {
        return JadwalPraktikum::create(array_merge([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul 1 Pengenalan',
            'tanggal' => Carbon::today()->toDateString(),
            'waktu_mulai' => Carbon::now()->subMinutes(10)->format('H:i:s'),
            'waktu_selesai' => Carbon::now()->addHours(2)->format('H:i:s'),
            'ruangan' => 'Lab RPL 1',
            'is_susulan' => false,
        ], $attributes));
    }

    // ==========================================
    // 1. DASHBOARD & RBAC ISOLATION TESTS
    // ==========================================

    public function test_admin_can_access_dashboard_and_receive_metrics(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get('/administrator/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
        $response->assertViewHas(['stats', 'activities']);
    }

    public function test_admin_can_access_dashboard_2_monitoring(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get('/administrator/dashboard-2');

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard2');
    }

    public function test_admin_is_forbidden_from_super_admin_role_and_user_management(): void
    {
        $admin = $this->createAdminUser();

        // Coba akses Role Management
        $roleResponse = $this->actingAs($admin)->get('/administrator/role');
        $roleResponse->assertRedirect(route('admin.dashboard'));
        $roleResponse->assertSessionHas('error', 'Anda tidak memiliki hak akses untuk fitur manajemen peran dan pengguna.');

        // Coba akses User Management
        $userResponse = $this->actingAs($admin)->get('/administrator/user');
        $userResponse->assertRedirect(route('admin.dashboard'));
        $userResponse->assertSessionHas('error', 'Anda tidak memiliki hak akses untuk fitur manajemen peran dan pengguna.');
    }

    public function test_admin_is_forbidden_from_praktikan_and_aslab_areas(): void
    {
        $admin = $this->createAdminUser();

        $praktikanResponse = $this->actingAs($admin)->get('/praktikan/dashboard');
        $praktikanResponse->assertRedirect(route('home'));

        $aslabResponse = $this->actingAs($admin)->get('/aslab/dashboard');
        $aslabResponse->assertRedirect(route('home'));
    }

    // ==========================================
    // 2. PRAKTIKUM & SESI MANAGEMENT TESTS
    // ==========================================

    public function test_admin_can_view_praktikum_index_and_create_form(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get('/administrator/praktikum');
        $response->assertStatus(200);
        $response->assertViewIs('admin.praktikum.index');
        $response->assertViewHas('praktikums');

        $createResponse = $this->actingAs($admin)->get('/administrator/praktikum/create');
        $createResponse->assertStatus(200);
        $createResponse->assertViewIs('admin.praktikum.create');
    }

    public function test_admin_can_store_new_praktikum_with_validation(): void
    {
        $admin = $this->createAdminUser();
        $namaPraktikum = 'Praktikum Cloud Computing '.rand(100, 999);

        $response = $this->actingAs($admin)->post('/administrator/praktikum', [
            'nama_praktikum' => $namaPraktikum,
            'periode_praktikum' => '2026/2027 Genap',
            'kuota_praktikan' => 60,
            'status_praktikum' => 'open_registration',
            'jumlah_modul' => 4,
            'ada_tugas_akhir' => 1,
            'nomor_surat_prefix' => 'LAB-RPL/CC',
            'daftar_dosen' => ['Dosen Pengampu A'],
            'daftar_kelas_mk' => ['A', 'B'],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('praktikums', [
            'nama_praktikum' => $namaPraktikum,
            'status_praktikum' => 'open_registration',
            'kuota_praktikan' => 60,
        ]);
    }

    public function test_admin_can_update_and_toggle_status_praktikum(): void
    {
        $admin = $this->createAdminUser();
        $praktikum = $this->createPraktikum(['status_praktikum' => 'open_registration']);

        // Toggle status
        $toggleResponse = $this->actingAs($admin)->patch("/administrator/praktikum/{$praktikum->id}/toggle-status", [
            'status' => 'on_progress',
        ]);
        $toggleResponse->assertStatus(200);
        $toggleResponse->assertJson(['success' => true]);
        $this->assertEquals('on_progress', $praktikum->fresh()->status_praktikum);

        // Update praktikum
        $updateResponse = $this->actingAs($admin)->put("/administrator/praktikum/{$praktikum->id}", [
            'nama_praktikum' => 'Praktikum Nama Diperbarui',
            'periode_praktikum' => $praktikum->periode_praktikum,
            'kuota_praktikan' => 80,
            'status_praktikum' => 'finished',
            'daftar_dosen' => ['Dosen Pengampu A'],
            'daftar_kelas_mk' => ['A', 'B'],
            'jumlah_modul' => 3,
            'ada_tugas_akhir' => 0,
            'nomor_surat_prefix' => 'LAB-RPL/UPD',
        ]);

        $updateResponse->assertRedirect();
        $this->assertDatabaseHas('praktikums', [
            'id' => $praktikum->id,
            'nama_praktikum' => 'Praktikum Nama Diperbarui',
            'kuota_praktikan' => 80,
            'status_praktikum' => 'finished',
        ]);
    }

    public function test_admin_can_manage_sesi_praktikum(): void
    {
        $admin = $this->createAdminUser();
        $praktikum = $this->createPraktikum();

        // Tambah Sesi
        $storeResponse = $this->actingAs($admin)->post("/administrator/praktikum/{$praktikum->id}/sesi", [
            'nama_sesi' => 'Sesi Sore Unggulan',
            'hari' => 'Rabu',
            'jam_mulai' => '15:30',
            'jam_selesai' => '17:30',
            'kuota' => 25,
        ]);

        $storeResponse->assertRedirect();
        $this->assertDatabaseHas('sesi_praktikums', [
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi Sore Unggulan',
            'kuota' => 25,
        ]);

        $sesi = SesiPraktikum::where('praktikum_id', $praktikum->id)->where('nama_sesi', 'Sesi Sore Unggulan')->first();

        // Update Sesi
        $updateResponse = $this->actingAs($admin)->patch("/administrator/praktikum/sesi/{$sesi->id}", [
            'nama_sesi' => 'Sesi Sore Diperbarui',
            'hari' => 'Kamis',
            'jam_mulai' => '16:00',
            'jam_selesai' => '18:00',
            'kuota' => 30,
        ]);

        $updateResponse->assertRedirect();
        $this->assertDatabaseHas('sesi_praktikums', [
            'id' => $sesi->id,
            'nama_sesi' => 'Sesi Sore Diperbarui',
            'hari' => 'Kamis',
            'kuota' => 30,
        ]);

        // Hapus Sesi
        $destroyResponse = $this->actingAs($admin)->delete("/administrator/praktikum/sesi/{$sesi->id}");
        $destroyResponse->assertRedirect();
        $this->assertDatabaseMissing('sesi_praktikums', ['id' => $sesi->id]);
    }

    public function test_admin_can_assign_and_destroy_aslab_for_praktikum(): void
    {
        $admin = $this->createAdminUser();
        $praktikum = $this->createPraktikum();
        [$aslabUser, $aslab] = $this->createAslabUser();

        // Assign aslab (aslab_id uses user_id, with kuota parameter)
        $assignResponse = $this->actingAs($admin)->post("/administrator/praktikum/{$praktikum->id}/aslab", [
            'aslab_id' => $aslabUser->id,
            'kuota' => 15,
        ]);

        $assignResponse->assertRedirect();
        $this->assertDatabaseHas('aslab_praktikums', [
            'praktikum_id' => $praktikum->id,
            'aslab_id' => $aslab->id,
        ]);

        // Destroy aslab from praktikum via pivot id
        $pivot = \App\Models\AslabPraktikum::where('praktikum_id', $praktikum->id)
            ->where('aslab_id', $aslab->id)
            ->first();

        $destroyResponse = $this->actingAs($admin)->delete("/administrator/praktikum/aslab/{$pivot->id}", [
            'praktikum_id' => $praktikum->id,
        ]);

        $destroyResponse->assertRedirect();
        $this->assertDatabaseMissing('aslab_praktikums', [
            'id' => $pivot->id,
        ]);
    }

    // ==========================================
    // 3. JADWAL PRAKTIKUM & SUSULAN TESTS
    // ==========================================

    public function test_admin_can_store_regular_jadwal_praktikum(): void
    {
        $admin = $this->createAdminUser();
        $praktikum = $this->createPraktikum();
        $sesi = $this->createSesi($praktikum);

        $response = $this->actingAs($admin)->post('/administrator/jadwal-praktikum', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul 2 Object-Oriented Programming',
            'tanggal' => Carbon::tomorrow()->toDateString(),
            'waktu_mulai' => '08:30:00',
            'waktu_selesai' => '10:30:00',
            'ruangan' => 'Laboratorium Rekayasa Lt. 2',
            'is_susulan' => '0',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('jadwal_praktikums', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul 2 Object-Oriented Programming',
            'is_susulan' => 0,
        ]);
    }

    public function test_admin_can_store_isolated_susulan_jadwal_praktikum(): void
    {
        $admin = $this->createAdminUser();
        $praktikum = $this->createPraktikum();
        $sesi = $this->createSesi($praktikum);

        $response = $this->actingAs($admin)->post('/administrator/jadwal-praktikum', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Susulan Modul 1',
            'tanggal' => Carbon::now()->addDays(3)->toDateString(),
            'waktu_mulai' => '13:00:00',
            'waktu_selesai' => '15:00:00',
            'ruangan' => 'Lab RPL Khusus',
            'is_susulan' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('jadwal_praktikums', [
            'praktikum_id' => $praktikum->id,
            'judul_modul' => 'Susulan Modul 1',
            'is_susulan' => 1,
        ]);
    }

    public function test_admin_can_enroll_alfa_student_to_susulan_schedule(): void
    {
        $admin = $this->createAdminUser();
        $praktikum = $this->createPraktikum();
        $sesi = $this->createSesi($praktikum);
        [$userPraktikan, $praktikan] = $this->createPraktikanUser();
        $pendaftaran = $this->createPendaftaran($praktikan, $praktikum, $sesi, 'verified');

        $jadwalSusulan = $this->createJadwal($praktikum, $sesi, [
            'judul_modul' => 'Susulan Terisolasi',
            'is_susulan' => true,
        ]);

        $response = $this->actingAs($admin)->post("/administrator/jadwal-praktikum/{$jadwalSusulan->id}/peserta-susulan", [
            'pendaftaran_ids' => [$pendaftaran->id],
            'catatan' => 'Izin sakit terverifikasi dokter',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('peserta_susulans', [
            'jadwal_id' => $jadwalSusulan->id,
            'pendaftaran_id' => $pendaftaran->id,
            'catatan' => 'Izin sakit terverifikasi dokter',
        ]);
    }

    public function test_admin_cannot_enroll_gugur_student_to_susulan_schedule(): void
    {
        $admin = $this->createAdminUser();
        $praktikum = $this->createPraktikum();
        $sesi = $this->createSesi($praktikum);
        [$userPraktikan, $praktikan] = $this->createPraktikanUser();
        $pendaftaranGugur = $this->createPendaftaran($praktikan, $praktikum, $sesi, 'gugur');

        $jadwalSusulan = $this->createJadwal($praktikum, $sesi, [
            'is_susulan' => true,
        ]);

        $response = $this->actingAs($admin)->post("/administrator/jadwal-praktikum/{$jadwalSusulan->id}/peserta-susulan", [
            'pendaftaran_ids' => [$pendaftaranGugur->id],
            'catatan' => 'Mencoba mendaftarkan mahasiswa gugur',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();

        $this->assertDatabaseMissing('peserta_susulans', [
            'jadwal_id' => $jadwalSusulan->id,
            'pendaftaran_id' => $pendaftaranGugur->id,
        ]);
    }

    // ==========================================
    // 4. PENDAFTARAN VERIFICATION & REACTIVATION
    // ==========================================

    public function test_admin_can_view_pendaftaran_index_and_filter_status(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get('/administrator/pendaftaran?status=pending');
        $response->assertStatus(200);
        $response->assertViewIs('admin.pendaftaran.index');
        $response->assertViewHas('pendaftarans');
    }

    public function test_admin_can_verify_and_reject_pendaftaran(): void
    {
        $admin = $this->createAdminUser();
        $praktikum = $this->createPraktikum();
        $sesi = $this->createSesi($praktikum);
        [$userPraktikan, $praktikan] = $this->createPraktikanUser();
        $pendaftaran = $this->createPendaftaran($praktikan, $praktikum, $sesi, 'pending');

        // Verifikasi pendaftaran
        $verifyResponse = $this->actingAs($admin)->patch("/administrator/pendaftaran/{$pendaftaran->id}/status", [
            'status' => 'verified',
        ]);
        $verifyResponse->assertRedirect(route('admin.pendaftaran.index'));
        $this->assertEquals('verified', $pendaftaran->fresh()->status);

        // Tolak pendaftaran dengan catatan
        $rejectResponse = $this->actingAs($admin)->patch("/administrator/pendaftaran/{$pendaftaran->id}/status", [
            'status' => 'rejected',
            'catatan' => 'KRS buram dan tidak terbaca',
        ]);
        $rejectResponse->assertRedirect(route('admin.pendaftaran.index'));
        $this->assertEquals('rejected', $pendaftaran->fresh()->status);
        $this->assertEquals('KRS buram dan tidak terbaca', $pendaftaran->fresh()->catatan);
    }

    public function test_admin_can_reactivate_gugur_student(): void
    {
        $admin = $this->createAdminUser();
        $praktikum = $this->createPraktikum();
        $sesi = $this->createSesi($praktikum);
        [$userPraktikan, $praktikan] = $this->createPraktikanUser();
        $pendaftaranGugur = $this->createPendaftaran($praktikan, $praktikum, $sesi, 'gugur');

        $response = $this->actingAs($admin)->patch("/administrator/pendaftaran/{$pendaftaranGugur->id}/reactivate", [
            'alasan' => 'Surat dispensasi dekanat disetujui',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $fresh = $pendaftaranGugur->fresh();
        $this->assertEquals('verified', $fresh->status);
        $this->assertStringContainsString('Surat dispensasi dekanat disetujui', $fresh->catatan);
    }

    // ==========================================
    // 5. BANK SOAL & PENUGASAN MANAGEMENT TESTS
    // ==========================================

    public function test_admin_can_store_penugasan_by_npm_mode_including_zero(): void
    {
        $admin = $this->createAdminUser();
        $praktikum = $this->createPraktikum();
        $sesi = $this->createSesi($praktikum);

        DigitNpm::firstOrCreate(
            ['digit' => '0'],
            ['label' => 'Digit 0', 'is_active' => true, 'sort_order' => 1]
        );

        $response = $this->actingAs($admin)->post('/administrator/penugasan', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'tipe_distribusi' => 'by_npm',
            'kode_akhir_npm' => '0',
            'judul' => 'Tugas Admin Digit 0',
            'deskripsi' => 'Kerjakan modul soal khusus digit nol',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Membuktikan bug falsy zero teratasi pada controller Admin
        $this->assertDatabaseHas('penugasans', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'tipe_distribusi' => 'by_npm',
            'kode_akhir_npm' => '0',
            'judul' => 'Tugas Admin Digit 0',
        ]);
    }

    public function test_admin_can_store_universal_penugasan_by_sesi(): void
    {
        $admin = $this->createAdminUser();
        $praktikum = $this->createPraktikum();
        $sesi = $this->createSesi($praktikum);

        $response = $this->actingAs($admin)->post('/administrator/penugasan', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'tipe_distribusi' => 'by_sesi',
            'judul' => 'Tugas Universal Sesi Pagi',
            'deskripsi' => 'Berlaku untuk seluruh mahasiswa sesi ini',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('penugasans', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'tipe_distribusi' => 'by_sesi',
            'kode_akhir_npm' => '*',
        ]);
    }

    public function test_admin_can_store_random_pool_penugasan(): void
    {
        $admin = $this->createAdminUser();
        $praktikum = $this->createPraktikum();
        $sesi = $this->createSesi($praktikum);

        $response = $this->actingAs($admin)->post('/administrator/penugasan', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'tipe_distribusi' => 'by_random',
            'judul' => 'Tugas Soal Acak Pool A',
            'deskripsi' => 'Pengundian acak otomatis',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('penugasans', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'tipe_distribusi' => 'by_random',
            'kode_akhir_npm' => 'random',
        ]);
    }

    public function test_admin_can_override_student_assignment(): void
    {
        $admin = $this->createAdminUser();
        $praktikum = $this->createPraktikum();
        $sesi = $this->createSesi($praktikum);
        $jadwal = $this->createJadwal($praktikum, $sesi);
        [$userPraktikan, $praktikan] = $this->createPraktikanUser();
        $pendaftaran = $this->createPendaftaran($praktikan, $praktikum, $sesi, 'verified');

        $penugasan = Penugasan::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'tipe_distribusi' => 'by_sesi',
            'kode_akhir_npm' => '*',
            'judul' => 'Soal Khusus Override',
            'deskripsi' => 'Instruksi khusus',
        ]);

        $response = $this->actingAs($admin)->patch("/administrator/penugasan/praktikan/{$pendaftaran->id}/soal", [
            'penugasan_id' => $penugasan->id,
            'jadwal_id' => $jadwal->id,
        ]);

        $response->assertRedirect(route('admin.penugasan.show', $jadwal->id));
        $this->assertDatabaseHas('penugasan_praktikan_overrides', [
            'pendaftaran_id' => $pendaftaran->id,
            'jadwal_praktikum_id' => $jadwal->id,
            'penugasan_id' => $penugasan->id,
        ]);
    }

    // ==========================================
    // 6. PRESENSI & LIVE SCORING AUTHORITY TESTS
    // ==========================================

    public function test_admin_can_generate_jadwal_qr_and_record_manual_presensi(): void
    {
        $admin = $this->createAdminUser();
        $praktikum = $this->createPraktikum();
        $sesi = $this->createSesi($praktikum);
        $jadwal = $this->createJadwal($praktikum, $sesi);
        [$userPraktikan, $praktikan] = $this->createPraktikanUser();
        $pendaftaran = $this->createPendaftaran($praktikan, $praktikum, $sesi, 'verified');

        // Generate QR
        $qrResponse = $this->actingAs($admin)->get("/presensi/generate-jadwal-qr/{$jadwal->id}");
        $qrResponse->assertStatus(200);

        // Store manual presensi
        $manualResponse = $this->actingAs($admin)->post('/presensi/manual', [
            'jadwal_id' => $jadwal->id,
            'pendaftaran_id' => $pendaftaran->id,
            'status' => 'hadir',
            'catatan' => 'Check-in manual via admin',
        ]);

        $manualResponse->assertStatus(200);
        $manualResponse->assertJson(['success' => true]);
        $this->assertDatabaseHas('presensis', [
            'jadwal_id' => $jadwal->id,
            'pendaftaran_id' => $pendaftaran->id,
            'status' => 'hadir',
        ]);
    }

    public function test_admin_can_store_penilaian_with_free_time_authority(): void
    {
        $admin = $this->createAdminUser();
        $praktikum = $this->createPraktikum();
        $sesi = $this->createSesi($praktikum);
        $jadwal = $this->createJadwal($praktikum, $sesi);
        [$userPraktikan, $praktikan] = $this->createPraktikanUser();
        $pendaftaran = $this->createPendaftaran($praktikan, $praktikum, $sesi, 'verified');

        // Penilaian langsung tanpa menunggu jadwal aktif (bebas batas waktu)
        $response = $this->actingAs($admin)->post('/administrator/penilaian', [
            'pendaftaran_id' => $pendaftaran->id,
            'jadwal_id' => $jadwal->id,
            'nilai' => 88,
            'catatan' => 'Penilaian otoritas admin tuntas',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Otomatis membentuk record presensi dan mencatat penilaian dengan aslab_id null (Admin)
        $this->assertDatabaseHas('penilaian_praktikums', [
            'aslab_id' => null,
            'nilai' => 88,
            'catatan' => 'Penilaian otoritas admin tuntas',
        ]);

        // Verifikasi Activity Log tercatat
        $this->assertDatabaseHas('activity_logs', [
            'activity' => 'Penilaian Admin Praktikum',
        ]);
    }

    public function test_admin_can_view_penilaian_akhir_and_summary(): void
    {
        $admin = $this->createAdminUser();
        $praktikum = $this->createPraktikum();

        $indexResponse = $this->actingAs($admin)->get('/administrator/penilaian-akhir');
        $indexResponse->assertStatus(200);
        $indexResponse->assertViewIs('admin.penilaian_akhir.index');

        $showResponse = $this->actingAs($admin)->get("/administrator/penilaian-akhir/praktikum/{$praktikum->id}");
        $showResponse->assertStatus(200);
        $showResponse->assertViewIs('admin.penilaian_akhir.show_praktikum');
    }

    // ==========================================
    // 7. MASTER DATA MANAGEMENT TESTS
    // ==========================================

    public function test_admin_can_manage_master_dosen(): void
    {
        $admin = $this->createAdminUser();
        do {
            $nip = 'NIP'.rand(100000, 999999).'_'.time();
        } while (Dosen::where('nip', $nip)->exists());

        // Store Dosen
        $storeResponse = $this->actingAs($admin)->post('/administrator/dosen', [
            'nama' => 'Dr. Dosen Penguji, M.Kom',
            'nip' => $nip,
            'is_active' => '1',
        ]);

        $storeResponse->assertRedirect(route('admin.dosen.index'));
        $this->assertDatabaseHas('dosens', [
            'nama' => 'Dr. Dosen Penguji, M.Kom',
            'nip' => $nip,
            'is_active' => true,
        ]);

        $dosen = Dosen::where('nip', $nip)->first();

        // Toggle status dosen
        $toggleResponse = $this->actingAs($admin)->patch("/administrator/dosen/{$dosen->id}/toggle-status");
        $toggleResponse->assertStatus(200);
        $toggleResponse->assertJson(['success' => true]);
        $this->assertEquals(false, $dosen->fresh()->is_active);
    }

    public function test_admin_can_manage_master_kelas(): void
    {
        $admin = $this->createAdminUser();
        do {
            $namaKelas = 'Kelas-'.rand(100, 999).'_'.time();
        } while (Kelas::where('nama_kelas', $namaKelas)->exists());

        // Store Kelas
        $storeResponse = $this->actingAs($admin)->post('/administrator/kelas', [
            'nama_kelas' => $namaKelas,
            'is_active' => '1',
        ]);

        $storeResponse->assertRedirect(route('admin.kelas.index'));
        $this->assertDatabaseHas('kelas', [
            'nama_kelas' => $namaKelas,
            'is_active' => true,
        ]);

        $kelas = Kelas::where('nama_kelas', $namaKelas)->first();

        // Toggle status kelas
        $toggleResponse = $this->actingAs($admin)->patch("/administrator/kelas/{$kelas->id}/toggle-status");
        $toggleResponse->assertStatus(200);
        $toggleResponse->assertJson(['success' => true]);
        $this->assertEquals(false, $kelas->fresh()->is_active);
    }

    public function test_admin_can_manage_digit_npm(): void
    {
        $admin = $this->createAdminUser();
        do {
            $digit = 'D'.rand(100, 999).'_'.time();
        } while (DigitNpm::where('digit', $digit)->exists());

        // Store Digit NPM
        $storeResponse = $this->actingAs($admin)->post('/administrator/digit-npm', [
            'digit' => $digit,
            'label' => 'Varian Digit '.$digit,
            'sort_order' => 50,
            'is_active' => '1',
        ]);

        $storeResponse->assertRedirect(route('admin.digit-npm.index'));
        $this->assertDatabaseHas('digit_npms', [
            'digit' => $digit,
            'label' => 'Varian Digit '.$digit,
        ]);

        $digitNpm = DigitNpm::where('digit', $digit)->first();

        // Toggle status
        $toggleResponse = $this->actingAs($admin)->patch("/administrator/digit-npm/{$digitNpm->id}/toggle-status");
        $toggleResponse->assertStatus(200);
        $toggleResponse->assertJson(['success' => true]);
        $this->assertEquals(false, $digitNpm->fresh()->is_active);
    }

    public function test_admin_can_manage_lab_settings(): void
    {
        $admin = $this->createAdminUser();

        $indexResponse = $this->actingAs($admin)->get('/administrator/laboratorium');
        $indexResponse->assertStatus(200);
        $indexResponse->assertViewIs('admin.setting.index');

        $updateResponse = $this->actingAs($admin)->put('/administrator/laboratorium', [
            'nama_kepala_lab' => 'Kepala Lab Baru, S.T., M.Kom.',
            'nip_kepala_lab' => '198501012010121001',
            'nama_kaprodi' => 'Kaprodi Baru, M.T.',
            'nip_kaprodi' => '198002022005011002',
            'nomor_surat_prefix' => 'LAB-RPL/SERT-NEW',
        ]);

        $updateResponse->assertRedirect();
        $setting = LabSetting::getSetting();
        $this->assertEquals('Kepala Lab Baru, S.T., M.Kom.', $setting->nama_kepala_lab);
        $this->assertEquals('LAB-RPL/SERT-NEW', $setting->nomor_surat_prefix);
    }

    // ==========================================
    // 8. NOTIFICATION & AUDIT LOGS TESTS
    // ==========================================

    public function test_admin_can_broadcast_notification(): void
    {
        $admin = $this->createAdminUser();
        [$aslabUser, $aslab] = $this->createAslabUser();
        [$praktikanUser, $praktikan] = $this->createPraktikanUser();

        $response = $this->actingAs($admin)->post('/administrator/notifications/send', [
            'title' => 'Pengumuman Penting Praktikum',
            'message' => 'Harap memperhatikan jadwal pelaksanaan modul praktikum.',
            'target' => 'all',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_admin_can_view_activity_logs_and_ratings(): void
    {
        $admin = $this->createAdminUser();

        $logsResponse = $this->actingAs($admin)->get('/administrator/logs');
        $logsResponse->assertStatus(200);
        $logsResponse->assertViewIs('admin.activity-log.index');

        $ratingsResponse = $this->actingAs($admin)->get('/administrator/ratings');
        $ratingsResponse->assertStatus(200);
        $ratingsResponse->assertViewIs('admin.rating.index');
    }

    public function test_admin_can_view_and_update_profile_info(): void
    {
        $admin = $this->createAdminUser();

        $viewResponse = $this->actingAs($admin)->get('/administrator/profile/edit');
        $viewResponse->assertStatus(200);

        $newUsername = 'admin_'.rand(10000, 99999).'_'.time();
        $updateResponse = $this->actingAs($admin)->patch('/administrator/profile/update', [
            'name' => 'Nama Admin Diperbarui',
            'email' => $admin->email,
            'username' => $newUsername,
        ]);

        $updateResponse->assertRedirect();
        $updateResponse->assertSessionHas('success');

        $this->assertEquals('Nama Admin Diperbarui', $admin->fresh()->name);
        $this->assertEquals($newUsername, $admin->fresh()->username);
    }
}
