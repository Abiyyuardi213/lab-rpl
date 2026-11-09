<?php

namespace Tests\Feature;

use App\Models\JadwalPraktikum;
use App\Models\PendaftaranPraktikum;
use App\Models\PenilaianAkhir;
use App\Models\Penugasan;
use App\Models\PesertaSusulan;
use App\Models\Praktikan;
use App\Models\Praktikum;
use App\Models\Presensi;
use App\Models\Role;
use App\Models\SesiPraktikum;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PraktikanRoleWhiteboxTest extends TestCase
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

    private function createPraktikanUser(): array
    {
        $role = Role::firstOrCreate(['name' => 'Praktikan']);
        $uniqueId = rand(10000, 99999);
        $npm = '06.'.date('Y').'.1.'.$uniqueId;

        $user = User::factory()->create([
            'role_id' => $role->id,
            'username' => $npm,
            'name' => 'Mahasiswa Test '.$uniqueId,
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

    private function createPraktikum(string $status = 'open_registration'): Praktikum
    {
        return Praktikum::create([
            'nama_praktikum' => 'Praktikum Whitebox '.uniqid(),
            'kode_praktikum' => 'WB-'.bin2hex(random_bytes(4)),
            'periode_praktikum' => '2026/2027 Ganjil',
            'kuota_praktikan' => 50,
            'status_praktikum' => $status,
            'jumlah_modul' => 3,
            'ada_tugas_akhir' => false,
        ]);
    }

    private function createSesi(Praktikum $praktikum, int $kuota = 30): SesiPraktikum
    {
        return SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi Pagi '.uniqid(),
            'hari' => 'Senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
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

    public function test_praktikan_can_access_dashboard_and_receive_view_data(): void
    {
        [$user, $praktikan] = $this->createPraktikanUser();

        $response = $this->actingAs($user)->get('/praktikan/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('praktikan.dashboard');
        $response->assertViewHas(['praktikums', 'activePendaftarans', 'completedPendaftarans', 'upcomingSchedules']);
    }

    public function test_praktikan_dashboard_displays_gugur_penalty_alert_when_status_is_gugur(): void
    {
        [$user, $praktikan] = $this->createPraktikanUser();
        $praktikum = $this->createPraktikum('on_progress');
        $sesi = $this->createSesi($praktikum);
        $this->createPendaftaran($praktikan, $praktikum, $sesi, 'gugur');

        $response = $this->actingAs($user)->get('/praktikan/dashboard');

        $response->assertStatus(200);
        $response->assertSee('gugur', false);
    }

    public function test_praktikan_dashboard_displays_susulan_notification_when_enrolled(): void
    {
        [$user, $praktikan] = $this->createPraktikanUser();
        $praktikum = $this->createPraktikum('on_progress');
        $sesi = $this->createSesi($praktikum);
        $pendaftaran = $this->createPendaftaran($praktikan, $praktikum, $sesi, 'verified');

        $jadwalSusulan = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => null,
            'judul_modul' => 'Praktikum Susulan Modul 1 '.uniqid(),
            'tanggal' => now()->addDays(2)->toDateString(),
            'waktu_mulai' => '13:00:00',
            'waktu_selesai' => '15:00:00',
            'ruangan' => 'Lab RPL 1',
            'token' => 'SUSULAN-TOKEN-'.uniqid(),
            'is_susulan' => true,
        ]);

        PesertaSusulan::create([
            'jadwal_id' => $jadwalSusulan->id,
            'pendaftaran_id' => $pendaftaran->id,
            'status' => 'terdaftar',
        ]);

        $response = $this->actingAs($user)->get('/praktikan/dashboard');

        $response->assertStatus(200);
        $response->assertSee('susulan', false);
    }

    public function test_praktikan_can_view_registration_create_form_for_open_praktikum(): void
    {
        [$user, $praktikan] = $this->createPraktikanUser();
        $praktikum = $this->createPraktikum('open_registration');
        $this->createSesi($praktikum);

        $response = $this->actingAs($user)->get('/praktikan/daftar-praktikum/'.$praktikum->id);

        $response->assertStatus(200);
        $response->assertViewIs('praktikan.pendaftaran.create');
        $response->assertViewHas('praktikum');
    }

    public function test_praktikan_cannot_register_if_praktikum_registration_is_closed(): void
    {
        [$user, $praktikan] = $this->createPraktikanUser();
        $praktikum = $this->createPraktikum('finished');

        $response = $this->actingAs($user)->get('/praktikan/daftar-praktikum/'.$praktikum->id);

        $response->assertRedirect(route('praktikan.dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_praktikan_cannot_register_twice_for_same_praktikum(): void
    {
        [$user, $praktikan] = $this->createPraktikanUser();
        $praktikum = $this->createPraktikum('open_registration');
        $sesi = $this->createSesi($praktikum);
        $this->createPendaftaran($praktikan, $praktikum, $sesi, 'verified');

        $response = $this->actingAs($user)->get('/praktikan/daftar-praktikum/'.$praktikum->id);

        $response->assertRedirect(route('praktikan.dashboard'));
        $response->assertSessionHas('error', 'Anda sudah mendaftar pada praktikum ini.');
    }

    public function test_praktikan_cannot_register_when_sesi_quota_is_exhausted(): void
    {
        [$user, $praktikan] = $this->createPraktikanUser();
        $praktikum = $this->createPraktikum('open_registration');
        $sesi = $this->createSesi($praktikum, 0); // kuota = 0

        $response = $this->actingAs($user)->post('/praktikan/daftar-praktikum', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'dosen_pengampu' => 'Dosen A',
            'asal_kelas_mata_kuliah' => 'IF-A',
            'kelas' => 'pagi',
            'is_mengulang' => 0,
            'is_google_form' => 1,
        ]);

        $response->assertSessionHasErrors('sesi_id');
    }

    public function test_praktikan_can_submit_valid_registration(): void
    {
        [$user, $praktikan] = $this->createPraktikanUser();
        $praktikum = $this->createPraktikum('open_registration');
        $sesi = $this->createSesi($praktikum, 20);

        $response = $this->actingAs($user)->post('/praktikan/daftar-praktikum', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'dosen_pengampu' => 'Dosen Pengampu Pengujian',
            'asal_kelas_mata_kuliah' => 'IF-B',
            'kelas' => 'pagi',
            'is_mengulang' => 0,
            'is_google_form' => 1,
        ]);

        $response->assertRedirect(route('praktikan.dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pendaftaran_praktikums', [
            'praktikan_id' => $praktikan->id,
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'status' => 'pending',
        ]);
    }

    public function test_praktikan_can_view_riwayat_pendaftaran(): void
    {
        [$user, $praktikan] = $this->createPraktikanUser();
        $praktikum = $this->createPraktikum('on_progress');
        $sesi = $this->createSesi($praktikum);
        $this->createPendaftaran($praktikan, $praktikum, $sesi, 'verified');

        $response = $this->actingAs($user)->get('/praktikan/riwayat-pendaftaran');

        $response->assertStatus(200);
        $response->assertViewIs('praktikan.pendaftaran.index');
        $response->assertViewHas('pendaftarans');
    }

    public function test_praktikan_can_generate_qr_for_modul_1_when_verified(): void
    {
        [$user, $praktikan] = $this->createPraktikanUser();
        $praktikum = $this->createPraktikum('on_progress');
        $sesi = $this->createSesi($praktikum);
        $this->createPendaftaran($praktikan, $praktikum, $sesi, 'verified');

        $jadwal = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul 1: Pengenalan Environment',
            'tanggal' => now()->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'ruangan' => 'Lab RPL 1',
            'token' => 'TOKEN-MODUL-1-'.uniqid(),
            'is_susulan' => false,
        ]);

        $response = $this->actingAs($user)->get('/praktikan/presensi/generate-qr/'.$jadwal->id);

        $response->assertStatus(200);
        $response->assertViewIs('praktikan.presensi.qr');
        $response->assertViewHas(['token', 'jadwal', 'pendaftaran']);
    }

    public function test_praktikan_cannot_generate_qr_if_already_present(): void
    {
        [$user, $praktikan] = $this->createPraktikanUser();
        $praktikum = $this->createPraktikum('on_progress');
        $sesi = $this->createSesi($praktikum);
        $pendaftaran = $this->createPendaftaran($praktikan, $praktikum, $sesi, 'verified');

        $jadwal = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul 1: Pengenalan',
            'tanggal' => now()->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'ruangan' => 'Lab RPL',
            'token' => 'TOKEN-PRESENT-'.uniqid(),
            'is_susulan' => false,
        ]);

        Presensi::create([
            'jadwal_id' => $jadwal->id,
            'pendaftaran_id' => $pendaftaran->id,
            'jam_masuk' => now(),
            'status' => 'hadir',
        ]);

        $response = $this->actingAs($user)->get('/praktikan/presensi/generate-qr/'.$jadwal->id);

        $response->assertStatus(302);
        $response->assertSessionHas('info', 'Anda sudah melakukan presensi untuk jadwal ini.');
    }

    public function test_praktikan_cannot_generate_qr_if_pendaftaran_is_unverified_or_gugur(): void
    {
        [$user, $praktikan] = $this->createPraktikanUser();
        $praktikum = $this->createPraktikum('on_progress');
        $sesi = $this->createSesi($praktikum);
        $this->createPendaftaran($praktikan, $praktikum, $sesi, 'gugur');

        $jadwal = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul 1',
            'tanggal' => now()->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'ruangan' => 'Lab RPL',
            'token' => 'TOKEN-GUGUR-'.uniqid(),
        ]);

        $response = $this->actingAs($user)->get('/praktikan/presensi/generate-qr/'.$jadwal->id);

        $response->assertStatus(302);
        $response->assertSessionHas('error');
    }

    public function test_praktikan_can_check_presensi_status_asynchronously(): void
    {
        [$user, $praktikan] = $this->createPraktikanUser();
        $praktikum = $this->createPraktikum('on_progress');
        $sesi = $this->createSesi($praktikum);
        $this->createPendaftaran($praktikan, $praktikum, $sesi, 'verified');

        $jadwal = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul Check Status',
            'tanggal' => now()->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'ruangan' => 'Lab RPL',
            'token' => 'TOKEN-CHECK-'.uniqid(),
        ]);

        $response = $this->actingAs($user)->get('/praktikan/presensi/check-status/'.$jadwal->id);

        $response->assertStatus(200);
        $response->assertJson(['present' => false]);
    }

    public function test_praktikan_can_view_penugasan_index(): void
    {
        [$user, $praktikan] = $this->createPraktikanUser();
        $praktikum = $this->createPraktikum('on_progress');
        $sesi = $this->createSesi($praktikum);
        $this->createPendaftaran($praktikan, $praktikum, $sesi, 'verified');

        $response = $this->actingAs($user)->get('/praktikan/penugasan');

        $response->assertStatus(200);
        $response->assertViewIs('praktikan.penugasan.index');
        $response->assertViewHas(['penugasans', 'pendaftarans']);
    }

    public function test_praktikan_is_strictly_forbidden_from_penugasan_when_gugur(): void
    {
        [$user, $praktikan] = $this->createPraktikanUser();
        $praktikum = $this->createPraktikum('on_progress');
        $sesi = $this->createSesi($praktikum);
        $this->createPendaftaran($praktikan, $praktikum, $sesi, 'gugur');

        $penugasan = Penugasan::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul' => 'Soal Modul 1 Gugur',
            'deskripsi' => 'Instruksi',
            'tipe_distribusi' => 'by_sesi',
            'kode_akhir_npm' => '*',
        ]);

        $response = $this->actingAs($user)->get('/praktikan/penugasan/'.$penugasan->id);

        $response->assertStatus(403);
    }

    public function test_praktikan_is_forbidden_from_penugasan_of_unmatched_sesi(): void
    {
        [$user, $praktikan] = $this->createPraktikanUser();
        $praktikum = $this->createPraktikum('on_progress');
        $sesiUser = $this->createSesi($praktikum);
        $sesiLain = $this->createSesi($praktikum);
        $this->createPendaftaran($praktikan, $praktikum, $sesiUser, 'verified');

        $penugasanSesiLain = Penugasan::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesiLain->id,
            'judul' => 'Soal Sesi Berbeda',
            'deskripsi' => 'Instruksi Sesi Berbeda',
            'tipe_distribusi' => 'by_sesi',
            'kode_akhir_npm' => '*',
        ]);

        $response = $this->actingAs($user)->get('/praktikan/penugasan/'.$penugasanSesiLain->id);

        $response->assertStatus(403);
    }

    public function test_praktikan_is_forbidden_from_susulan_penugasan_if_not_enrolled(): void
    {
        [$user, $praktikan] = $this->createPraktikanUser();
        $praktikum = $this->createPraktikum('on_progress');
        $sesi = $this->createSesi($praktikum);
        $this->createPendaftaran($praktikan, $praktikum, $sesi, 'verified');

        $jadwalSusulan = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => null,
            'judul_modul' => 'Modul Susulan',
            'tanggal' => now()->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'ruangan' => 'Lab RPL',
            'token' => 'SUSULAN-SOAL-'.uniqid(),
            'is_susulan' => true,
        ]);

        $penugasanSusulan = Penugasan::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'jadwal_praktikum_id' => $jadwalSusulan->id,
            'judul' => 'Soal Khusus Susulan',
            'deskripsi' => 'Instruksi Susulan',
            'tipe_distribusi' => 'by_sesi',
            'kode_akhir_npm' => '*',
        ]);

        $response = $this->actingAs($user)->get('/praktikan/penugasan/'.$penugasanSusulan->id);

        $response->assertStatus(403);
    }

    public function test_praktikan_cannot_download_sertifikat_if_praktikum_not_finished(): void
    {
        [$user, $praktikan] = $this->createPraktikanUser();
        $praktikum = $this->createPraktikum('on_progress');
        $sesi = $this->createSesi($praktikum);
        $pendaftaran = $this->createPendaftaran($praktikan, $praktikum, $sesi, 'verified');

        $response = $this->actingAs($user)->get('/praktikan/pendaftaran/'.$pendaftaran->id.'/sertifikat');

        $response->assertStatus(302);
        $response->assertSessionHas('error');
    }

    public function test_praktikan_cannot_download_sertifikat_if_grade_is_tidak_lulus(): void
    {
        [$user, $praktikan] = $this->createPraktikanUser();
        $praktikum = $this->createPraktikum('finished');
        $sesi = $this->createSesi($praktikum);
        $pendaftaran = $this->createPendaftaran($praktikan, $praktikum, $sesi, 'verified');

        PenilaianAkhir::create([
            'pendaftaran_id' => $pendaftaran->id,
            'nilai_akhir' => 45.0,
            'nilai_huruf' => 'D',
            'status_kelulusan' => 'TIDAK LULUS',
        ]);

        $response = $this->actingAs($user)->get('/praktikan/pendaftaran/'.$pendaftaran->id.'/sertifikat');

        $response->assertStatus(302);
        $response->assertSessionHas('error');
    }

    public function test_praktikan_can_submit_rating_for_assigned_aslab(): void
    {
        [$user, $praktikan] = $this->createPraktikanUser();
        $praktikum = $this->createPraktikum('finished');
        $sesi = $this->createSesi($praktikum);
        $pendaftaran = $this->createPendaftaran($praktikan, $praktikum, $sesi, 'verified');

        $response = $this->actingAs($user)->post('/praktikan/pendaftaran/'.$pendaftaran->id.'/rating', [
            'rating_praktikum' => 5,
            'ulasan_praktikum' => 'Praktikum sangat bermanfaat dan terstruktur.',
            'rating_asisten' => 5,
            'ulasan_asisten' => 'Asisten lab sangat ramah dan membimbing dengan baik.',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('ratings', [
            'pendaftaran_id' => $pendaftaran->id,
            'rating_praktikum' => 5,
            'rating_asisten' => 5,
        ]);
    }

    public function test_praktikan_can_view_profile_edit_and_update_while_username_locked(): void
    {
        [$user, $praktikan] = $this->createPraktikanUser();
        $originalUsername = $user->username;

        $viewResponse = $this->actingAs($user)->get('/praktikan/profile/edit');
        $viewResponse->assertStatus(200);

        $updateResponse = $this->actingAs($user)->patch('/praktikan/profile/update', [
            'name' => 'Nama Praktikan Diperbarui',
            'email' => $user->email,
            'username' => '06.9999.1.99999', // Mencoba memanipulasi NPM/username
            'no_hp' => '089876543210',
            'jurusan' => 'Teknik Informatika',
            'angkatan' => '2024',
        ]);

        $updateResponse->assertRedirect();
        $updateResponse->assertSessionHas('success');

        $freshUser = $user->fresh();
        $this->assertEquals('Nama Praktikan Diperbarui', $freshUser->name);
        // Username terkunci untuk praktikan
        $this->assertEquals($originalUsername, $freshUser->username);
        $this->assertEquals('089876543210', $praktikan->fresh()->no_hp);
    }

    public function test_praktikan_can_fetch_and_mark_notifications(): void
    {
        [$user, $praktikan] = $this->createPraktikanUser();

        $fetchResponse = $this->actingAs($user)->get('/praktikan/notifications/fetch');
        $fetchResponse->assertStatus(200);

        $readAllResponse = $this->actingAs($user)->get('/praktikan/notifications/read-all');
        $readAllResponse->assertRedirect();
    }

    public function test_praktikan_is_prevented_from_accessing_admin_and_aslab_areas(): void
    {
        [$user, $praktikan] = $this->createPraktikanUser();

        $adminResponse = $this->actingAs($user)->get('/administrator/dashboard');
        $adminResponse->assertRedirect('/');

        $aslabResponse = $this->actingAs($user)->get('/aslab/dashboard');
        $aslabResponse->assertRedirect('/');
    }
}
