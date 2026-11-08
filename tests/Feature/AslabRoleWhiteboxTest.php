<?php

namespace Tests\Feature;

use App\Models\Aslab;
use App\Models\AslabPraktikum;
use App\Models\DigitNpm;
use App\Models\JadwalPraktikum;
use App\Models\PendaftaranPraktikum;
use App\Models\Penugasan;
use App\Models\Praktikan;
use App\Models\Praktikum;
use App\Models\Presensi;
use App\Models\Role;
use App\Models\SesiPraktikum;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AslabRoleWhiteboxTest extends TestCase
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

    private function createPraktikum(string $status = 'on_progress'): Praktikum
    {
        return Praktikum::create([
            'nama_praktikum' => 'Praktikum Aslab Test '.uniqid(),
            'kode_praktikum' => 'AS-'.bin2hex(random_bytes(4)),
            'periode_praktikum' => '2026/2027 Ganjil',
            'kuota_praktikan' => 40,
            'status_praktikum' => $status,
            'jumlah_modul' => 3,
            'ada_tugas_akhir' => false,
        ]);
    }

    private function createSesi(Praktikum $praktikum): SesiPraktikum
    {
        return SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi Aslab '.uniqid(),
            'hari' => 'Senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'kuota' => 25,
        ]);
    }

    private function assignAslabToPraktikum(Aslab $aslab, Praktikum $praktikum, int $kuota = 20): AslabPraktikum
    {
        return AslabPraktikum::create([
            'aslab_id' => $aslab->id,
            'praktikum_id' => $praktikum->id,
            'kuota' => $kuota,
        ]);
    }

    private function createPendaftaran(Praktikan $praktikan, Praktikum $praktikum, SesiPraktikum $sesi, string $status = 'verified'): PendaftaranPraktikum
    {
        return PendaftaranPraktikum::create([
            'praktikan_id' => $praktikan->id,
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'no_hp' => '081234567890',
            'dosen_pengampu' => 'Dosen Pengampu M.Kom',
            'kelas' => 'pagi',
            'asal_kelas_mata_kuliah' => 'IF-A',
            'is_mengulang' => false,
            'status' => $status,
        ]);
    }

    public function test_aslab_can_access_dashboard_and_receive_metrics(): void
    {
        [$user, $aslab] = $this->createAslabUser();
        $praktikum = $this->createPraktikum();
        $this->assignAslabToPraktikum($aslab, $praktikum, 25);

        $response = $this->actingAs($user)->get('/aslab/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('aslab.dashboard.index');
        $response->assertViewHas(['assignedCount', 'totalQuota', 'myPraktikums']);
    }

    public function test_aslab_can_view_penugasan_index_filtered_by_assigned_praktikums(): void
    {
        [$user, $aslab] = $this->createAslabUser();
        $praktikum = $this->createPraktikum();
        $this->assignAslabToPraktikum($aslab, $praktikum, 20);

        $response = $this->actingAs($user)->get('/aslab/penugasan');

        $response->assertStatus(200);
        $response->assertViewIs('aslab.penugasan.index');
        $response->assertViewHas(['penugasans', 'praktikums', 'digitNpms']);
    }

    public function test_aslab_can_create_penugasan_by_npm_mode(): void
    {
        [$user, $aslab] = $this->createAslabUser();
        $praktikum = $this->createPraktikum();
        $sesi = $this->createSesi($praktikum);
        $this->assignAslabToPraktikum($aslab, $praktikum);

        DigitNpm::firstOrCreate(
            ['digit' => '0'],
            ['label' => 'Digit 0', 'is_active' => true, 'sort_order' => 1]
        );

        $response = $this->actingAs($user)->post('/aslab/penugasan', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'tipe_distribusi' => 'by_npm',
            'kode_akhir_npm' => '0',
            'judul' => 'Tugas Modul 1 NPM 0',
            'deskripsi' => 'Instruksi pengerjaan studi kasus untuk digit 0',
        ]);

        $response->assertRedirect(route('aslab.penugasan.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('penugasans', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'aslab_id' => $aslab->id,
            'tipe_distribusi' => 'by_npm',
            'kode_akhir_npm' => '0',
            'judul' => 'Tugas Modul 1 NPM 0',
        ]);
    }

    public function test_aslab_can_create_universal_penugasan_by_sesi_mode(): void
    {
        [$user, $aslab] = $this->createAslabUser();
        $praktikum = $this->createPraktikum();
        $sesi = $this->createSesi($praktikum);
        $this->assignAslabToPraktikum($aslab, $praktikum);

        $response = $this->actingAs($user)->post('/aslab/penugasan', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'tipe_distribusi' => 'by_sesi',
            'judul' => 'Tugas Universal Sesi Pagi',
            'deskripsi' => 'Semua praktikan pada sesi ini mengerjakan studi kasus yang seragam',
        ]);

        $response->assertRedirect(route('aslab.penugasan.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('penugasans', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'aslab_id' => $aslab->id,
            'tipe_distribusi' => 'by_sesi',
            'kode_akhir_npm' => '*',
        ]);
    }

    public function test_aslab_can_create_random_pool_penugasan(): void
    {
        [$user, $aslab] = $this->createAslabUser();
        $praktikum = $this->createPraktikum();
        $sesi = $this->createSesi($praktikum);
        $this->assignAslabToPraktikum($aslab, $praktikum);

        $response = $this->actingAs($user)->post('/aslab/penugasan', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'tipe_distribusi' => 'by_random',
            'judul' => 'Tugas Paket Random A',
            'deskripsi' => 'Variasi soal yang akan dibagikan secara acak terkunci ke mahasiswa',
        ]);

        $response->assertRedirect(route('aslab.penugasan.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('penugasans', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'aslab_id' => $aslab->id,
            'tipe_distribusi' => 'by_random',
            'kode_akhir_npm' => 'random',
        ]);
    }

    public function test_aslab_cannot_create_penugasan_by_npm_without_kode_akhir_npm(): void
    {
        [$user, $aslab] = $this->createAslabUser();
        $praktikum = $this->createPraktikum();
        $sesi = $this->createSesi($praktikum);
        $this->assignAslabToPraktikum($aslab, $praktikum);

        $response = $this->actingAs($user)->post('/aslab/penugasan', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'tipe_distribusi' => 'by_npm',
            'judul' => 'Tugas Tanpa NPM',
            'deskripsi' => 'Harus gagal validasi',
        ]);

        $response->assertSessionHasErrors('kode_akhir_npm');
    }

    public function test_aslab_cannot_update_or_delete_penugasan_owned_by_another_aslab(): void
    {
        [$userA, $aslabA] = $this->createAslabUser();
        [$userB, $aslabB] = $this->createAslabUser();

        $praktikum = $this->createPraktikum();
        $sesi = $this->createSesi($praktikum);

        $penugasanA = Penugasan::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'aslab_id' => $aslabA->id,
            'tipe_distribusi' => 'by_sesi',
            'kode_akhir_npm' => '*',
            'judul' => 'Tugas Milik Aslab A',
            'deskripsi' => 'Deskripsi A',
        ]);

        $updateResponse = $this->actingAs($userB)->put('/aslab/penugasan/'.$penugasanA->id, [
            'judul' => 'Judul Dibajak Aslab B',
            'deskripsi' => 'Deskripsi Bajakan',
        ]);
        $updateResponse->assertStatus(403);

        $deleteResponse = $this->actingAs($userB)->delete('/aslab/penugasan/'.$penugasanA->id);
        $deleteResponse->assertStatus(403);
    }

    public function test_aslab_can_update_and_delete_their_own_penugasan(): void
    {
        [$user, $aslab] = $this->createAslabUser();
        $praktikum = $this->createPraktikum();
        $sesi = $this->createSesi($praktikum);

        $penugasan = Penugasan::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'aslab_id' => $aslab->id,
            'tipe_distribusi' => 'by_sesi',
            'kode_akhir_npm' => '*',
            'judul' => 'Judul Asli Aslab',
            'deskripsi' => 'Deskripsi Asli',
        ]);

        $updateResponse = $this->actingAs($user)->put('/aslab/penugasan/'.$penugasan->id, [
            'judul' => 'Judul Telah Direvisi',
            'deskripsi' => 'Deskripsi Telah Direvisi',
        ]);
        $updateResponse->assertRedirect(route('aslab.penugasan.index'));
        $this->assertEquals('Judul Telah Direvisi', $penugasan->fresh()->judul);

        $deleteResponse = $this->actingAs($user)->delete('/aslab/penugasan/'.$penugasan->id);
        $deleteResponse->assertRedirect(route('aslab.penugasan.index'));
        $this->assertDatabaseMissing('penugasans', ['id' => $penugasan->id]);
    }

    public function test_aslab_can_view_penilaian_index_listing_active_schedules(): void
    {
        [$user, $aslab] = $this->createAslabUser();
        $praktikum = $this->createPraktikum();
        $this->assignAslabToPraktikum($aslab, $praktikum);

        $response = $this->actingAs($user)->get('/aslab/penilaian');

        $response->assertStatus(200);
        $response->assertViewIs('aslab.penilaian.index');
        $response->assertViewHas('jadwals');
    }

    public function test_aslab_can_view_penilaian_show_for_specific_jadwal(): void
    {
        [$user, $aslab] = $this->createAslabUser();
        $praktikum = $this->createPraktikum();
        $sesi = $this->createSesi($praktikum);
        $this->assignAslabToPraktikum($aslab, $praktikum);

        $jadwal = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul Penilaian Live',
            'tanggal' => now()->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'ruangan' => 'Lab RPL',
            'token' => 'TOKEN-JADWAL-'.uniqid(),
            'is_susulan' => false,
        ]);

        $response = $this->actingAs($user)->get('/aslab/penilaian/'.$jadwal->id);

        $response->assertStatus(200);
        $response->assertViewIs('aslab.penilaian.show');
        $response->assertViewHas(['jadwal', 'pendaftarans']);
    }

    public function test_aslab_can_submit_live_grading_score_for_present_student(): void
    {
        [$userAslab, $aslab] = $this->createAslabUser();
        [$userMhs, $praktikan] = $this->createPraktikanUser();

        $praktikum = $this->createPraktikum();
        $sesi = $this->createSesi($praktikum);
        $this->assignAslabToPraktikum($aslab, $praktikum);
        $pendaftaran = $this->createPendaftaran($praktikan, $praktikum, $sesi, 'verified');

        $jadwal = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul Penilaian Praktikum',
            'tanggal' => now()->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'ruangan' => 'Lab RPL',
            'token' => 'TOKEN-NILAI-'.uniqid(),
            'is_susulan' => false,
        ]);

        $presensi = Presensi::create([
            'jadwal_id' => $jadwal->id,
            'pendaftaran_id' => $pendaftaran->id,
            'jam_masuk' => now(),
            'status' => 'hadir',
        ]);

        $response = $this->actingAs($userAslab)->post('/aslab/penilaian', [
            'presensi_id' => $presensi->id,
            'nilai' => 88,
            'catatan' => 'Implementasi algoritma sangat efisien.',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('penilaian_praktikums', [
            'presensi_id' => $presensi->id,
            'aslab_id' => $aslab->id,
            'nilai' => 88,
        ]);
    }

    public function test_aslab_cannot_submit_invalid_grade_scores(): void
    {
        [$userAslab, $aslab] = $this->createAslabUser();

        $response = $this->actingAs($userAslab)->post('/aslab/penilaian', [
            'presensi_id' => 99999,
            'nilai' => 150, // Melebihi skor maksimal 100
        ]);

        $response->assertSessionHasErrors(['presensi_id', 'nilai']);
    }

    public function test_aslab_can_view_presensi_scanner_page(): void
    {
        [$user, $aslab] = $this->createAslabUser();
        $praktikum = $this->createPraktikum();
        $this->assignAslabToPraktikum($aslab, $praktikum);

        $response = $this->actingAs($user)->get('/aslab/presensi/scan');

        $response->assertStatus(200);
        $response->assertViewIs('aslab.presensi.scan');
        $response->assertViewHas(['activeSchedules', 'finishedSchedules']);
    }

    public function test_aslab_can_checkin_student_with_valid_cached_token(): void
    {
        [$userAslab, $aslab] = $this->createAslabUser();
        [$userMhs, $praktikan] = $this->createPraktikanUser();

        $praktikum = $this->createPraktikum();
        $sesi = $this->createSesi($praktikum);
        $this->assignAslabToPraktikum($aslab, $praktikum);
        $pendaftaran = $this->createPendaftaran($praktikan, $praktikum, $sesi, 'verified');

        $jadwal = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul Scan QR Checkin',
            'tanggal' => now()->toDateString(),
            'waktu_mulai' => now()->subMinutes(5)->format('H:i:s'),
            'waktu_selesai' => now()->addHour()->format('H:i:s'),
            'ruangan' => 'Lab RPL',
            'token' => 'TOKEN-JADWAL-CHECKIN-'.uniqid(),
            'is_susulan' => false,
        ]);

        $token = 'TOKEN-MAHASISWA-'.uniqid();
        Cache::put("presensi_token_{$token}", [
            'pendaftaran_id' => $pendaftaran->id,
            'jadwal_id' => $jadwal->id,
            'expires' => now()->addMinutes(5)->timestamp,
        ], 300);

        $response = $this->actingAs($userAslab)->postJson('/aslab/presensi/check-in', [
            'token' => $token,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('presensis', [
            'jadwal_id' => $jadwal->id,
            'pendaftaran_id' => $pendaftaran->id,
            'status' => 'hadir',
        ]);

        // Token telah dihapus dari Cache (Anti-double scan)
        $this->assertNull(Cache::get("presensi_token_{$token}"));
    }

    public function test_aslab_checkin_fails_with_expired_or_invalid_token(): void
    {
        [$user, $aslab] = $this->createAslabUser();

        $response = $this->actingAs($user)->postJson('/aslab/presensi/check-in', [
            'token' => 'TOKEN-KADALUARSA-'.uniqid(),
        ]);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
    }

    public function test_aslab_checkin_strictly_rejects_gugur_student(): void
    {
        [$userAslab, $aslab] = $this->createAslabUser();
        [$userMhs, $praktikan] = $this->createPraktikanUser();

        $praktikum = $this->createPraktikum();
        $sesi = $this->createSesi($praktikum);
        $this->assignAslabToPraktikum($aslab, $praktikum);
        $pendaftaran = $this->createPendaftaran($praktikan, $praktikum, $sesi, 'gugur');

        $jadwal = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul GUGUR Checkin',
            'tanggal' => now()->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'ruangan' => 'Lab RPL',
            'token' => 'TOKEN-JADWAL-GUGUR-'.uniqid(),
            'is_susulan' => false,
        ]);

        $token = 'TOKEN-GUGUR-'.uniqid();
        Cache::put("presensi_token_{$token}", [
            'pendaftaran_id' => $pendaftaran->id,
            'jadwal_id' => $jadwal->id,
            'expires' => now()->addMinutes(5)->timestamp,
        ], 300);

        $response = $this->actingAs($userAslab)->postJson('/aslab/presensi/check-in', [
            'token' => $token,
        ]);

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);
    }

    public function test_aslab_can_view_portfolio_edit_form(): void
    {
        [$user, $aslab] = $this->createAslabUser();

        $response = $this->actingAs($user)->get('/aslab/portfolio/edit');

        $response->assertStatus(200);
        $response->assertViewIs('aslab.portfolio.edit');
        $response->assertViewHas('aslab');
    }

    public function test_aslab_can_update_portfolio_details_and_links(): void
    {
        [$user, $aslab] = $this->createAslabUser();

        $response = $this->actingAs($user)->patch('/aslab/portfolio/update', [
            'bio' => 'Asisten Laboratorium bidang Rekayasa Perangkat Lunak dan Cloud Computing.',
            'skills' => ['Laravel', 'Docker', 'MySQL', 'Tailwind CSS'],
            'github_link' => 'https://github.com/aslab-whitebox-test',
            'linkedin_link' => 'https://linkedin.com/in/aslab-whitebox-test',
        ]);

        $response->assertRedirect();
        $freshAslab = $aslab->fresh();
        $this->assertEquals('https://github.com/aslab-whitebox-test', $freshAslab->github_link);
        $this->assertContains('Laravel', $freshAslab->skills);
    }

    public function test_aslab_portfolio_validates_social_media_urls(): void
    {
        [$user, $aslab] = $this->createAslabUser();

        $response = $this->actingAs($user)->patch('/aslab/portfolio/update', [
            'github_link' => 'bukan-url-valid-http',
        ]);

        $response->assertSessionHasErrors('github_link');
    }

    public function test_aslab_can_view_ratings_and_average_statistics(): void
    {
        [$user, $aslab] = $this->createAslabUser();

        $response = $this->actingAs($user)->get('/aslab/ratings');

        $response->assertStatus(200);
        $response->assertViewIs('aslab.rating.index');
        $response->assertViewHas(['ratings', 'totalRatings', 'avgRatingAslab', 'avgRatingPraktikum']);
    }

    public function test_aslab_can_view_and_update_profile_info(): void
    {
        [$user, $aslab] = $this->createAslabUser();

        $viewResponse = $this->actingAs($user)->get('/aslab/profile/edit');
        $viewResponse->assertStatus(200);

        $newUsername = 'aslab_'.rand(10000, 99999).'_'.time();
        $updateResponse = $this->actingAs($user)->patch('/aslab/profile/update', [
            'name' => 'Nama Aslab Diperbarui',
            'email' => $user->email,
            'username' => $newUsername,
            'no_hp' => '081299998888',
            'jurusan' => 'Teknik Informatika',
            'angkatan' => '2023',
        ]);

        $updateResponse->assertRedirect();
        $updateResponse->assertSessionHas('success');

        $this->assertEquals('Nama Aslab Diperbarui', $user->fresh()->name);
        $this->assertEquals('081299998888', $aslab->fresh()->no_hp);
    }

    public function test_aslab_can_fetch_and_mark_notifications_as_read(): void
    {
        [$user, $aslab] = $this->createAslabUser();

        $fetchResponse = $this->actingAs($user)->get('/aslab/notifications/fetch');
        $fetchResponse->assertStatus(200);

        $readAllResponse = $this->actingAs($user)->get('/aslab/notifications/read-all');
        $readAllResponse->assertRedirect();
    }

    public function test_aslab_is_forbidden_from_admin_and_praktikan_areas(): void
    {
        [$user, $aslab] = $this->createAslabUser();

        $adminResponse = $this->actingAs($user)->get('/administrator/dashboard');
        $adminResponse->assertRedirect('/');

        $praktikanResponse = $this->actingAs($user)->get('/praktikan/dashboard');
        $praktikanResponse->assertRedirect('/');
    }
}
