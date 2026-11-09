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
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class SuperAdminRoleWhiteboxTest extends TestCase
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
            'email' => 'aslab_'.$uniqueId.'_'.time().'@example.com',
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
            'email' => 'praktikan_'.$uniqueId.'_'.time().'@example.com',
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
            'nama_praktikum' => 'Praktikum Uji Super Admin '.$unique,
            'periode_praktikum' => '2026/2027 Ganjil',
            'kuota_praktikan' => 50,
            'status_praktikum' => 'on_progress',
            'jumlah_modul' => 3,
            'ada_tugas_akhir' => false,
            'nomor_surat_prefix' => 'LAB-RPL/SERT',
            'daftar_dosen' => ['Dosen Pengampu Utama'],
            'daftar_kelas_mk' => ['A', 'B'],
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
            'kuota' => 25,
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
            'dosen_pengampu' => 'Dosen Pengampu Utama',
        ]);
    }

    private function createJadwal(Praktikum $praktikum, SesiPraktikum $sesi, array $attributes = []): JadwalPraktikum
    {
        return JadwalPraktikum::create(array_merge([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul 1 Pengantar',
            'tanggal' => Carbon::today()->toDateString(),
            'waktu_mulai' => Carbon::now()->subMinutes(10)->format('H:i:s'),
            'waktu_selesai' => Carbon::now()->addHours(2)->format('H:i:s'),
            'ruangan' => 'Lab RPL 1',
            'is_susulan' => false,
        ], $attributes));
    }

    // ==========================================
    // 1. DASHBOARD, REDIRECT, & RBAC BARRIERS
    // ==========================================

    public function test_super_admin_can_access_dashboard_and_receive_metrics(): void
    {
        $superAdmin = $this->createSuperAdminUser();

        $response = $this->actingAs($superAdmin)->get('/administrator/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
        $response->assertViewHas(['stats', 'activities']);
    }

    public function test_super_admin_can_access_dashboard_2_monitoring(): void
    {
        $superAdmin = $this->createSuperAdminUser();

        $response = $this->actingAs($superAdmin)->get('/administrator/dashboard-2');

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard2');
    }

    public function test_super_admin_home_redirects_to_administrator_dashboard(): void
    {
        $superAdmin = $this->createSuperAdminUser();

        $response = $this->actingAs($superAdmin)->get('/home');

        $response->assertRedirect('/administrator/dashboard');
    }

    public function test_super_admin_is_forbidden_from_direct_praktikan_and_aslab_portals(): void
    {
        $superAdmin = $this->createSuperAdminUser();

        $praktikanResponse = $this->actingAs($superAdmin)->get('/praktikan/dashboard');
        $praktikanResponse->assertRedirect(route('home'));

        $aslabResponse = $this->actingAs($superAdmin)->get('/aslab/dashboard');
        $aslabResponse->assertRedirect(route('home'));
    }

    // ==========================================
    // 2. EXCLUSIVE ROLE MANAGEMENT (SUPER ADMIN ONLY)
    // ==========================================

    public function test_super_admin_has_exclusive_access_to_role_index(): void
    {
        $superAdmin = $this->createSuperAdminUser();

        $response = $this->actingAs($superAdmin)->get('/administrator/role');

        $response->assertStatus(200);
        $response->assertViewIs('admin.role.index');
        $response->assertViewHas('roles');
    }

    public function test_super_admin_can_create_new_role(): void
    {
        $superAdmin = $this->createSuperAdminUser();
        $roleName = 'Observer_'.rand(100, 999).'_'.time();

        $response = $this->actingAs($superAdmin)->post('/administrator/role', [
            'name' => $roleName,
            'display_name' => 'Auditor Akademik Eksternal',
            'description' => 'Role pengamat proses perkuliahan',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('roles', [
            'name' => $roleName,
            'display_name' => 'Auditor Akademik Eksternal',
        ]);
    }

    public function test_super_admin_can_update_role_details(): void
    {
        $superAdmin = $this->createSuperAdminUser();
        $role = Role::create([
            'name' => 'RoleTest_'.rand(100, 999).'_'.time(),
            'display_name' => 'Nama Role Awal',
        ]);

        $response = $this->actingAs($superAdmin)->put("/administrator/role/{$role->id}", [
            'name' => $role->name,
            'display_name' => 'Nama Role Diperbarui',
            'description' => 'Deskripsi peran berhasil dimutasi',
        ]);

        $response->assertRedirect();
        $this->assertEquals('Nama Role Diperbarui', $role->fresh()->display_name);
    }

    public function test_super_admin_can_toggle_role_status(): void
    {
        $superAdmin = $this->createSuperAdminUser();
        $role = Role::create([
            'name' => 'ToggleRole_'.rand(100, 999).'_'.time(),
            'display_name' => 'Role Uji Toggle',
            'status' => true,
        ]);

        $response = $this->actingAs($superAdmin)->patch("/administrator/role/{$role->id}/toggle-status");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertEquals(false, $role->fresh()->status);
    }

    public function test_super_admin_can_destroy_custom_role(): void
    {
        $superAdmin = $this->createSuperAdminUser();
        $role = Role::create([
            'name' => 'DeleteRole_'.rand(100, 999).'_'.time(),
            'display_name' => 'Role Siap Hapus',
        ]);

        $response = $this->actingAs($superAdmin)->delete("/administrator/role/{$role->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    // ==========================================
    // 3. EXCLUSIVE USER MANAGEMENT (SUPER ADMIN ONLY)
    // ==========================================

    public function test_super_admin_has_exclusive_access_to_user_index(): void
    {
        $superAdmin = $this->createSuperAdminUser();

        $response = $this->actingAs($superAdmin)->get('/administrator/user');

        $response->assertStatus(200);
        $response->assertViewIs('admin.user.index');
        $response->assertViewHas('users');

        $createResponse = $this->actingAs($superAdmin)->get('/administrator/user/create');
        $createResponse->assertStatus(200);
        $createResponse->assertViewIs('admin.user.create');
    }

    public function test_super_admin_can_create_internal_user(): void
    {
        $superAdmin = $this->createSuperAdminUser();
        $roleAdmin = Role::firstOrCreate(['name' => 'Admin']);
        $uniqueId = rand(1000, 9999).'_'.time();

        $response = $this->actingAs($superAdmin)->post('/administrator/user', [
            'username' => 'newadmin_'.$uniqueId,
            'name' => 'Operator Baru '.$uniqueId,
            'email' => 'newadmin_'.$uniqueId.'@example.com',
            'password' => 'SecurePass123!',
            'role_id' => $roleAdmin->id,
        ]);

        $response->assertRedirect(route('admin.user.index', ['last_page' => '1']));
        $this->assertDatabaseHas('users', [
            'username' => 'newadmin_'.$uniqueId,
            'email' => 'newadmin_'.$uniqueId.'@example.com',
            'role_id' => $roleAdmin->id,
        ]);
    }

    public function test_super_admin_can_update_user_and_auto_sync_aslab_record(): void
    {
        $superAdmin = $this->createSuperAdminUser();
        $roleAdmin = Role::firstOrCreate(['name' => 'Admin']);
        $roleAslab = Role::firstOrCreate(['name' => 'Aslab']);

        $user = User::factory()->create([
            'role_id' => $roleAdmin->id,
            'username' => 'staff_'.rand(100, 999).'_'.time(),
            'email' => 'staff_'.rand(100, 999).'_'.time().'@example.com',
        ]);

        // Mutasi role user dari Admin menjadi Aslab
        $staffName = 'Staff Naik Aslab '.rand(1000, 9999).'_'.time();
        $response = $this->actingAs($superAdmin)->put("/administrator/user/{$user->id}", [
            'name' => $staffName,
            'username' => $user->username,
            'email' => $user->email,
            'role_id' => $roleAslab->id,
        ]);

        $response->assertRedirect(route('admin.user.index'));
        $this->assertEquals($roleAslab->id, $user->fresh()->role_id);

        // Membuktikan auto-sync: record pada tabel aslabs otomatis terbuat via firstOrCreate
        $this->assertDatabaseHas('aslabs', [
            'user_id' => $user->id,
            'npm' => $user->username,
        ]);
    }

    public function test_super_admin_can_toggle_user_status(): void
    {
        $superAdmin = $this->createSuperAdminUser();
        $admin = $this->createAdminUser();

        $response = $this->actingAs($superAdmin)->patch("/administrator/user/{$admin->id}/toggle-status");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertEquals(false, $admin->fresh()->status);
    }

    public function test_super_admin_can_destroy_user(): void
    {
        $superAdmin = $this->createSuperAdminUser();
        $admin = $this->createAdminUser();

        $response = $this->actingAs($superAdmin)->delete("/administrator/user/{$admin->id}");

        $response->assertRedirect(route('admin.user.index'));
        $this->assertDatabaseMissing('users', ['id' => $admin->id]);
    }

    // ==========================================
    // 4. USER IMPERSONATION CAPABILITIES
    // ==========================================

    public function test_super_admin_can_impersonate_aslab_user(): void
    {
        $superAdmin = $this->createSuperAdminUser();
        [$aslabUser, $aslab] = $this->createAslabUser();

        $response = $this->actingAs($superAdmin)->post("/administrator/aslab/{$aslabUser->id}/impersonate");

        $response->assertRedirect(route('aslab.dashboard'));
        $this->assertEquals($aslabUser->id, Auth::id());
        $this->assertEquals($superAdmin->id, session('impersonated_by'));
    }

    public function test_super_admin_can_impersonate_praktikan_user(): void
    {
        $superAdmin = $this->createSuperAdminUser();
        [$praktikanUser, $praktikan] = $this->createPraktikanUser();

        $response = $this->actingAs($superAdmin)->post("/administrator/praktikan/{$praktikanUser->id}/impersonate");

        $response->assertRedirect(route('dashboard.redirect'));
        $this->assertEquals($praktikanUser->id, Auth::id());
        $this->assertEquals($superAdmin->id, session('impersonated_by'));
    }

    // ==========================================
    // 5. PRAKTIKUM, SESI, & JADWAL SUSULAN
    // ==========================================

    public function test_super_admin_can_manage_praktikum_and_toggle_status(): void
    {
        $superAdmin = $this->createSuperAdminUser();
        $namaPraktikum = 'Praktikum Super Architecture '.rand(100, 999);

        // Store Praktikum
        $storeResponse = $this->actingAs($superAdmin)->post('/administrator/praktikum', [
            'nama_praktikum' => $namaPraktikum,
            'periode_praktikum' => '2026/2027 Ganjil',
            'kuota_praktikan' => 70,
            'status_praktikum' => 'open_registration',
            'jumlah_modul' => 4,
            'ada_tugas_akhir' => 1,
            'daftar_dosen' => ['Dosen Utama A'],
            'daftar_kelas_mk' => ['A', 'B'],
        ]);

        $storeResponse->assertRedirect();
        $this->assertDatabaseHas('praktikums', ['nama_praktikum' => $namaPraktikum]);

        $praktikum = Praktikum::where('nama_praktikum', $namaPraktikum)->first();

        // Toggle Status
        $toggleResponse = $this->actingAs($superAdmin)->patch("/administrator/praktikum/{$praktikum->id}/toggle-status", [
            'status' => 'on_progress',
        ]);
        $toggleResponse->assertStatus(200);
        $toggleResponse->assertJson(['success' => true]);
        $this->assertEquals('on_progress', $praktikum->fresh()->status_praktikum);
    }

    public function test_super_admin_can_manage_sesi_and_assign_aslab(): void
    {
        $superAdmin = $this->createSuperAdminUser();
        $praktikum = $this->createPraktikum();
        [$aslabUser, $aslab] = $this->createAslabUser();

        // Tambah Sesi
        $sesiResponse = $this->actingAs($superAdmin)->post("/administrator/praktikum/{$praktikum->id}/sesi", [
            'nama_sesi' => 'Sesi Super Siang',
            'hari' => 'Selasa',
            'jam_mulai' => '13:00',
            'jam_selesai' => '15:00',
            'kuota' => 20,
        ]);
        $sesiResponse->assertRedirect();
        $this->assertDatabaseHas('sesi_praktikums', ['nama_sesi' => 'Sesi Super Siang']);

        // Assign Aslab ke Praktikum
        $assignResponse = $this->actingAs($superAdmin)->post("/administrator/praktikum/{$praktikum->id}/aslab", [
            'aslab_id' => $aslabUser->id,
            'kuota' => 12,
        ]);
        $assignResponse->assertRedirect();
        $this->assertDatabaseHas('aslab_praktikums', [
            'praktikum_id' => $praktikum->id,
            'aslab_id' => $aslab->id,
        ]);
    }

    public function test_super_admin_can_create_regular_and_susulan_schedules(): void
    {
        $superAdmin = $this->createSuperAdminUser();
        $praktikum = $this->createPraktikum();
        $sesi = $this->createSesi($praktikum);

        // Jadwal Reguler
        $regularResponse = $this->actingAs($superAdmin)->post('/administrator/jadwal-praktikum', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul 2 Advanced Logic',
            'tanggal' => Carbon::tomorrow()->toDateString(),
            'waktu_mulai' => '09:00:00',
            'waktu_selesai' => '11:00:00',
            'ruangan' => 'Lab 1',
            'is_susulan' => '0',
        ]);
        $regularResponse->assertRedirect();
        $this->assertDatabaseHas('jadwal_praktikums', ['judul_modul' => 'Modul 2 Advanced Logic', 'is_susulan' => 0]);

        // Jadwal Susulan
        $susulanResponse = $this->actingAs($superAdmin)->post('/administrator/jadwal-praktikum', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Susulan Modul 2',
            'tanggal' => Carbon::now()->addDays(2)->toDateString(),
            'waktu_mulai' => '15:00:00',
            'waktu_selesai' => '17:00:00',
            'ruangan' => 'Lab Khusus',
            'is_susulan' => '1',
        ]);
        $susulanResponse->assertRedirect();
        $this->assertDatabaseHas('jadwal_praktikums', ['judul_modul' => 'Susulan Modul 2', 'is_susulan' => 1]);
    }

    public function test_super_admin_can_enroll_susulan_participant_and_reject_gugur(): void
    {
        $superAdmin = $this->createSuperAdminUser();
        $praktikum = $this->createPraktikum();
        $sesi = $this->createSesi($praktikum);
        [$userA, $praktikanA] = $this->createPraktikanUser();
        [$userB, $praktikanB] = $this->createPraktikanUser();

        $pendaftaranAktif = $this->createPendaftaran($praktikanA, $praktikum, $sesi, 'verified');
        $pendaftaranGugur = $this->createPendaftaran($praktikanB, $praktikum, $sesi, 'gugur');

        $jadwalSusulan = $this->createJadwal($praktikum, $sesi, ['is_susulan' => true]);

        // Berhasil enroll mahasiswa aktif
        $successResponse = $this->actingAs($superAdmin)->post("/administrator/jadwal-praktikum/{$jadwalSusulan->id}/peserta-susulan", [
            'pendaftaran_ids' => [$pendaftaranAktif->id],
            'catatan' => 'Disetujui Super Admin',
        ]);
        $successResponse->assertRedirect();
        $successResponse->assertSessionHas('success');
        $this->assertDatabaseHas('peserta_susulans', [
            'jadwal_id' => $jadwalSusulan->id,
            'pendaftaran_id' => $pendaftaranAktif->id,
        ]);

        // Ditolak saat mendaftarkan mahasiswa gugur
        $rejectResponse = $this->actingAs($superAdmin)->post("/administrator/jadwal-praktikum/{$jadwalSusulan->id}/peserta-susulan", [
            'pendaftaran_ids' => [$pendaftaranGugur->id],
        ]);
        $rejectResponse->assertRedirect();
        $rejectResponse->assertSessionHasErrors();
        $this->assertDatabaseMissing('peserta_susulans', [
            'jadwal_id' => $jadwalSusulan->id,
            'pendaftaran_id' => $pendaftaranGugur->id,
        ]);
    }

    // ==========================================
    // 6. PENDAFTARAN & PEMULIHAN GUGUR
    // ==========================================

    public function test_super_admin_can_verify_pendaftaran_and_reactivate_gugur(): void
    {
        $superAdmin = $this->createSuperAdminUser();
        $praktikum = $this->createPraktikum();
        $sesi = $this->createSesi($praktikum);
        [$userPraktikan, $praktikan] = $this->createPraktikanUser();
        $pendaftaran = $this->createPendaftaran($praktikan, $praktikum, $sesi, 'pending');

        // Verifikasi
        $verifyResponse = $this->actingAs($superAdmin)->patch("/administrator/pendaftaran/{$pendaftaran->id}/status", [
            'status' => 'verified',
        ]);
        $verifyResponse->assertRedirect(route('admin.pendaftaran.index'));
        $this->assertEquals('verified', $pendaftaran->fresh()->status);

        // Simulasi status menjadi gugur
        $pendaftaran->update(['status' => 'gugur']);

        // Pemulihan status oleh Super Admin
        $reactivateResponse = $this->actingAs($superAdmin)->patch("/administrator/pendaftaran/{$pendaftaran->id}/reactivate", [
            'alasan' => 'Dispensasi rektorat disetujui penuh',
        ]);
        $reactivateResponse->assertRedirect();
        $reactivateResponse->assertSessionHas('success');
        $this->assertEquals('verified', $pendaftaran->fresh()->status);
    }

    // ==========================================
    // 7. PENUGASAN 3 MODE & OVERRIDE
    // ==========================================

    public function test_super_admin_can_store_penugasan_in_all_three_modes(): void
    {
        $superAdmin = $this->createSuperAdminUser();
        $praktikum = $this->createPraktikum();
        $sesi = $this->createSesi($praktikum);

        DigitNpm::firstOrCreate(
            ['digit' => '0'],
            ['label' => 'Digit 0', 'is_active' => true, 'sort_order' => 1]
        );

        // Mode By NPM (termasuk verifikasi digit '0')
        $npmResponse = $this->actingAs($superAdmin)->post('/administrator/penugasan', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'tipe_distribusi' => 'by_npm',
            'kode_akhir_npm' => '0',
            'judul' => 'Tugas Super Admin NPM 0',
            'deskripsi' => 'Instruksi khusus',
        ]);
        $npmResponse->assertRedirect();
        $this->assertDatabaseHas('penugasans', [
            'tipe_distribusi' => 'by_npm',
            'kode_akhir_npm' => '0',
        ]);

        // Mode By Sesi (universal *)
        $sesiResponse = $this->actingAs($superAdmin)->post('/administrator/penugasan', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'tipe_distribusi' => 'by_sesi',
            'judul' => 'Tugas Super Admin Universal',
            'deskripsi' => 'Berlaku semua',
        ]);
        $sesiResponse->assertRedirect();
        $this->assertDatabaseHas('penugasans', [
            'tipe_distribusi' => 'by_sesi',
            'kode_akhir_npm' => '*',
        ]);

        // Mode By Random
        $randomResponse = $this->actingAs($superAdmin)->post('/administrator/penugasan', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'tipe_distribusi' => 'by_random',
            'judul' => 'Tugas Super Admin Random',
            'deskripsi' => 'Pool acak',
        ]);
        $randomResponse->assertRedirect();
        $this->assertDatabaseHas('penugasans', [
            'tipe_distribusi' => 'by_random',
            'kode_akhir_npm' => 'random',
        ]);
    }

    public function test_super_admin_can_override_student_assignment(): void
    {
        $superAdmin = $this->createSuperAdminUser();
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
            'judul' => 'Soal Override Super Admin',
            'deskripsi' => 'Deskripsi',
        ]);

        $response = $this->actingAs($superAdmin)->patch("/administrator/penugasan/praktikan/{$pendaftaran->id}/soal", [
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
    // 8. PRESENSI & FREE-TIME SCORING AUTHORITY
    // ==========================================

    public function test_super_admin_can_generate_qr_and_record_manual_presensi(): void
    {
        $superAdmin = $this->createSuperAdminUser();
        $praktikum = $this->createPraktikum();
        $sesi = $this->createSesi($praktikum);
        $jadwal = $this->createJadwal($praktikum, $sesi);
        [$userPraktikan, $praktikan] = $this->createPraktikanUser();
        $pendaftaran = $this->createPendaftaran($praktikan, $praktikum, $sesi, 'verified');

        $qrResponse = $this->actingAs($superAdmin)->get("/presensi/generate-jadwal-qr/{$jadwal->id}");
        $qrResponse->assertStatus(200);

        $manualResponse = $this->actingAs($superAdmin)->post('/presensi/manual', [
            'jadwal_id' => $jadwal->id,
            'pendaftaran_id' => $pendaftaran->id,
            'status' => 'hadir',
        ]);

        $manualResponse->assertStatus(200);
        $manualResponse->assertJson(['success' => true]);
        $this->assertDatabaseHas('presensis', [
            'jadwal_id' => $jadwal->id,
            'pendaftaran_id' => $pendaftaran->id,
            'status' => 'hadir',
        ]);
    }

    public function test_super_admin_can_store_live_grading_with_free_time_authority(): void
    {
        $superAdmin = $this->createSuperAdminUser();
        $praktikum = $this->createPraktikum();
        $sesi = $this->createSesi($praktikum);
        $jadwal = $this->createJadwal($praktikum, $sesi);
        [$userPraktikan, $praktikan] = $this->createPraktikanUser();
        $pendaftaran = $this->createPendaftaran($praktikan, $praktikum, $sesi, 'verified');

        $response = $this->actingAs($superAdmin)->post('/administrator/penilaian', [
            'pendaftaran_id' => $pendaftaran->id,
            'jadwal_id' => $jadwal->id,
            'nilai' => 95,
            'catatan' => 'Nilai istimewa dari Super Admin',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('penilaian_praktikums', [
            'aslab_id' => null,
            'nilai' => 95,
            'catatan' => 'Nilai istimewa dari Super Admin',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'activity' => 'Penilaian Admin Praktikum',
        ]);
    }

    public function test_super_admin_can_view_penilaian_akhir_matrix(): void
    {
        $superAdmin = $this->createSuperAdminUser();
        $praktikum = $this->createPraktikum();

        $response = $this->actingAs($superAdmin)->get("/administrator/penilaian-akhir/praktikum/{$praktikum->id}");
        $response->assertStatus(200);
        $response->assertViewIs('admin.penilaian_akhir.show_praktikum');
    }

    // ==========================================
    // 9. MASTER DATA & SETTINGS
    // ==========================================

    public function test_super_admin_can_manage_master_dosen_kelas_and_digit_npm(): void
    {
        $superAdmin = $this->createSuperAdminUser();

        // Master Dosen
        do {
            $nip = 'NIP'.rand(100000, 999999).'_'.time();
        } while (Dosen::where('nip', $nip)->exists());

        $dosenResponse = $this->actingAs($superAdmin)->post('/administrator/dosen', [
            'nama' => 'Prof. Super Dosen, Ph.D',
            'nip' => $nip,
            'is_active' => '1',
        ]);
        $dosenResponse->assertRedirect(route('admin.dosen.index'));
        $this->assertDatabaseHas('dosens', ['nip' => $nip]);

        // Master Kelas
        do {
            $namaKelas = 'SuperKelas-'.rand(100, 999).'_'.time();
        } while (Kelas::where('nama_kelas', $namaKelas)->exists());

        $kelasResponse = $this->actingAs($superAdmin)->post('/administrator/kelas', [
            'nama_kelas' => $namaKelas,
            'is_active' => '1',
        ]);
        $kelasResponse->assertRedirect(route('admin.kelas.index'));
        $this->assertDatabaseHas('kelas', ['nama_kelas' => $namaKelas]);

        // Digit NPM
        do {
            $digit = 'S'.rand(100, 999).'_'.time();
        } while (DigitNpm::where('digit', $digit)->exists());

        $digitResponse = $this->actingAs($superAdmin)->post('/administrator/digit-npm', [
            'digit' => $digit,
            'label' => 'Varian Super '.$digit,
            'sort_order' => 99,
            'is_active' => '1',
        ]);
        $digitResponse->assertRedirect(route('admin.digit-npm.index'));
        $this->assertDatabaseHas('digit_npms', ['digit' => $digit]);
    }

    public function test_super_admin_can_manage_lab_settings_and_certificates(): void
    {
        $superAdmin = $this->createSuperAdminUser();

        $updateResponse = $this->actingAs($superAdmin)->put('/administrator/laboratorium', [
            'nama_kepala_lab' => 'Prof. Dr. Kepala Lab, S.T., M.T.',
            'nip_kepala_lab' => '197501012000121001',
            'nama_kaprodi' => 'Dr. Kaprodi Utama, M.Kom.',
            'nip_kaprodi' => '197802022003011002',
            'nomor_surat_prefix' => 'LAB-RPL/SUPER-SERT',
        ]);

        $updateResponse->assertRedirect();
        $setting = LabSetting::getSetting();
        $this->assertEquals('Prof. Dr. Kepala Lab, S.T., M.T.', $setting->nama_kepala_lab);
        $this->assertEquals('LAB-RPL/SUPER-SERT', $setting->nomor_surat_prefix);
    }

    // ==========================================
    // 10. NOTIFICATIONS, AUDIT LOGS & PROFILE
    // ==========================================

    public function test_super_admin_can_broadcast_notification_and_view_audit_logs(): void
    {
        $superAdmin = $this->createSuperAdminUser();
        [$aslabUser, $aslab] = $this->createAslabUser();
        [$praktikanUser, $praktikan] = $this->createPraktikanUser();

        $broadcastResponse = $this->actingAs($superAdmin)->post('/administrator/notifications/send', [
            'title' => 'Peringatan Pemeliharaan Sistem',
            'message' => 'Server laboratorium akan menjalani pemeliharaan berkala.',
            'target' => 'all',
        ]);
        $broadcastResponse->assertRedirect();
        $broadcastResponse->assertSessionHas('success');

        $logsResponse = $this->actingAs($superAdmin)->get('/administrator/logs');
        $logsResponse->assertStatus(200);
        $logsResponse->assertViewIs('admin.activity-log.index');

        $ratingsResponse = $this->actingAs($superAdmin)->get('/administrator/ratings');
        $ratingsResponse->assertStatus(200);
        $ratingsResponse->assertViewIs('admin.rating.index');
    }

    public function test_super_admin_can_view_and_update_profile(): void
    {
        $superAdmin = $this->createSuperAdminUser();

        $viewResponse = $this->actingAs($superAdmin)->get('/administrator/profile/edit');
        $viewResponse->assertStatus(200);

        $newUsername = 'superadmin_'.rand(10000, 99999).'_'.time();
        $updateResponse = $this->actingAs($superAdmin)->patch('/administrator/profile/update', [
            'name' => 'Nama Super Admin Baru',
            'email' => $superAdmin->email,
            'username' => $newUsername,
        ]);

        $updateResponse->assertRedirect();
        $updateResponse->assertSessionHas('success');

        $this->assertEquals('Nama Super Admin Baru', $superAdmin->fresh()->name);
        $this->assertEquals($newUsername, $superAdmin->fresh()->username);
    }
}
