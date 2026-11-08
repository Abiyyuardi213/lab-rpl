<?php

namespace Tests\Feature;

use App\Models\JadwalPraktikum;
use App\Models\PendaftaranPraktikum;
use App\Models\Penugasan;
use App\Models\PesertaSusulan;
use App\Models\Praktikan;
use App\Models\Praktikum;
use App\Models\Role;
use App\Models\SesiPraktikum;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PraktikumSusulanExecutionTest extends TestCase
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
            'nama_praktikum' => 'Praktikum Eksekusi Susulan '.uniqid(),
            'kode_praktikum' => 'PRK-'.bin2hex(random_bytes(6)),
            'periode_praktikum' => '2026/2027 Ganjil',
            'kuota_praktikan' => 50,
            'status_praktikum' => 'on_progress',
            'jumlah_modul' => 3,
        ]);
    }

    private function createStudentUser(Praktikum $praktikum, SesiPraktikum $sesi, string $status = 'verified'): array
    {
        $role = Role::firstOrCreate(['name' => 'Praktikan']);
        $user = User::factory()->create([
            'role_id' => $role->id,
            'username' => 'user_'.uniqid(),
        ]);
        do {
            $npm = '06.'.date('Y').'.1.'.mt_rand(100000, 999999);
        } while (Praktikan::where('npm', $npm)->exists());

        $praktikan = Praktikan::create([
            'user_id' => $user->id,
            'npm' => $npm,
            'no_wa' => '081234567888',
            'jenis_kelamin' => 'L',
        ]);

        $pendaftaran = PendaftaranPraktikum::create([
            'praktikan_id' => $praktikan->id,
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'status' => $status,
            'no_hp' => '081234567888',
            'dosen_pengampu' => 'Dosen Test',
            'kelas' => 'pagi',
            'asal_kelas_mata_kuliah' => 'A',
        ]);

        return [$user, $pendaftaran];
    }

    public function test_enrolled_student_can_checkin_make_up_session(): void
    {
        $praktikum = $this->createPraktikum();
        $sesi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi 1 Reguler',
            'hari' => Carbon::now()->translatedFormat('l'),
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'kuota' => 30,
        ]);

        [$user, $pendaftaran] = $this->createStudentUser($praktikum, $sesi, 'verified');

        // Jadwal susulan sedang aktif saat ini
        $now = Carbon::now();
        $jadwalSusulan = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'judul_modul' => 'Modul 1 Susulan',
            'tanggal' => $now->toDateString(),
            'waktu_mulai' => $now->copy()->subMinutes(10)->format('H:i:s'),
            'waktu_selesai' => $now->copy()->addHour()->format('H:i:s'),
            'ruangan' => 'Lab RPL 1',
            'token' => 'token-susulan-'.uniqid(),
            'is_susulan' => true,
        ]);

        // Daftarkan sebagai peserta susulan
        $peserta = PesertaSusulan::create([
            'jadwal_id' => $jadwalSusulan->id,
            'pendaftaran_id' => $pendaftaran->id,
            'status' => 'terdaftar',
        ]);

        $response = $this->actingAs($user)->get(route('praktikan.presensi.scan-jadwal', $jadwalSusulan->token));

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('presensis', [
            'jadwal_id' => $jadwalSusulan->id,
            'pendaftaran_id' => $pendaftaran->id,
            'status' => 'hadir',
            'is_susulan' => 1,
        ]);

        $peserta->refresh();
        $this->assertEquals('hadir', $peserta->status);
    }

    public function test_unenrolled_student_cannot_checkin_make_up_session(): void
    {
        $praktikum = $this->createPraktikum();
        $sesi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi 1 Reguler',
            'hari' => Carbon::now()->translatedFormat('l'),
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'kuota' => 30,
        ]);

        [$user, $pendaftaran] = $this->createStudentUser($praktikum, $sesi, 'verified');

        $now = Carbon::now();
        $jadwalSusulan = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'judul_modul' => 'Modul 1 Susulan',
            'tanggal' => $now->toDateString(),
            'waktu_mulai' => $now->copy()->subMinutes(10)->format('H:i:s'),
            'waktu_selesai' => $now->copy()->addHour()->format('H:i:s'),
            'ruangan' => 'Lab RPL 1',
            'token' => 'token-susulan-'.uniqid(),
            'is_susulan' => true,
        ]);

        // Mahasiswa TIDAK didaftarkan ke peserta susulan
        $response = $this->actingAs($user)->get(route('praktikan.presensi.scan-jadwal', $jadwalSusulan->token));

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('presensis', [
            'jadwal_id' => $jadwalSusulan->id,
            'pendaftaran_id' => $pendaftaran->id,
        ]);
    }

    public function test_gugur_student_is_blocked_from_checkin(): void
    {
        $praktikum = $this->createPraktikum();
        $sesi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi 1 Reguler',
            'hari' => Carbon::now()->translatedFormat('l'),
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'kuota' => 30,
        ]);

        // Mahasiswa berstatus GUGUR
        [$user, $pendaftaran] = $this->createStudentUser($praktikum, $sesi, 'gugur');

        $now = Carbon::now();
        $jadwalReguler = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul 3 Reguler',
            'tanggal' => $now->toDateString(),
            'waktu_mulai' => $now->copy()->subMinutes(10)->format('H:i:s'),
            'waktu_selesai' => $now->copy()->addHour()->format('H:i:s'),
            'ruangan' => 'Lab RPL 1',
            'token' => 'token-reguler-'.uniqid(),
            'is_susulan' => false,
        ]);

        $response = $this->actingAs($user)->get(route('praktikan.presensi.scan-jadwal', $jadwalReguler->token));

        $response->assertSessionHas('error');
        $this->assertStringContainsString('gugur', strtolower(session('error') ?? ''));
    }

    public function test_gugur_student_is_blocked_from_penugasan(): void
    {
        $praktikum = $this->createPraktikum();
        $sesi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi 1 Reguler',
            'hari' => Carbon::now()->translatedFormat('l'),
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'kuota' => 30,
        ]);

        [$user, $pendaftaran] = $this->createStudentUser($praktikum, $sesi, 'gugur');

        $now = Carbon::now();
        $jadwal = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul 1',
            'tanggal' => $now->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'ruangan' => 'Lab RPL 1',
            'is_susulan' => false,
        ]);

        $penugasan = Penugasan::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'jadwal_praktikum_id' => $jadwal->id,
            'judul' => 'Tugas Modul 1',
            'deskripsi' => 'Kerjakan tugas',
            'tipe_distribusi' => 'by_sesi',
            'kode_akhir_npm' => '*',
            'status' => 'published',
        ]);

        $response = $this->actingAs($user)->get(route('praktikan.penugasan.show', $penugasan->id));
        $response->assertStatus(403);
    }
}
