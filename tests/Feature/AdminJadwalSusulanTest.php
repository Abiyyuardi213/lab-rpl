<?php

namespace Tests\Feature;

use App\Models\JadwalPraktikum;
use App\Models\PendaftaranPraktikum;
use App\Models\Praktikan;
use App\Models\Praktikum;
use App\Models\Role;
use App\Models\SesiPraktikum;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminJadwalSusulanTest extends TestCase
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

    private function createAdmin(): User
    {
        $role = Role::firstOrCreate(['name' => 'Admin']);

        return User::factory()->create([
            'role_id' => $role->id,
            'username' => 'admin_'.uniqid(),
        ]);
    }

    private function createPraktikum(): Praktikum
    {
        return Praktikum::create([
            'nama_praktikum' => 'Praktikum Susulan Admin '.uniqid(),
            'kode_praktikum' => 'PRK-'.bin2hex(random_bytes(6)),
            'periode_praktikum' => '2026/2027 Ganjil',
            'kuota_praktikan' => 50,
            'status_praktikum' => 'on_progress',
            'jumlah_modul' => 3,
        ]);
    }

    private function createStudent(Praktikum $praktikum, SesiPraktikum $sesi, string $status = 'verified'): PendaftaranPraktikum
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

        return PendaftaranPraktikum::create([
            'praktikan_id' => $praktikan->id,
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'status' => $status,
            'no_hp' => '081234567888',
            'dosen_pengampu' => 'Dosen Test',
            'kelas' => 'pagi',
            'asal_kelas_mata_kuliah' => 'A',
        ]);
    }

    public function test_admin_can_create_susulan_schedule(): void
    {
        $admin = $this->createAdmin();
        $praktikum = $this->createPraktikum();

        $response = $this->actingAs($admin)->post(route('admin.jadwal-praktikum.store'), [
            'praktikum_id' => $praktikum->id,
            'judul_modul' => 'Modul 1 Susulan',
            'tanggal' => Carbon::tomorrow()->toDateString(),
            'waktu_mulai' => '13:00',
            'waktu_selesai' => '15:00',
            'ruangan' => 'Lab RPL 2',
            'is_susulan' => 1,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('jadwal_praktikums', [
            'praktikum_id' => $praktikum->id,
            'judul_modul' => 'Modul 1 Susulan',
            'is_susulan' => 1,
        ]);
    }

    public function test_admin_can_enroll_student_to_susulan_schedule(): void
    {
        $admin = $this->createAdmin();
        $praktikum = $this->createPraktikum();
        $sesi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi Reguler',
            'hari' => 'Senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'kuota' => 30,
        ]);

        $student = $this->createStudent($praktikum, $sesi, 'verified');

        $jadwalSusulan = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'judul_modul' => 'Modul 1 Dasar',
            'tanggal' => Carbon::tomorrow()->toDateString(),
            'waktu_mulai' => '13:00:00',
            'waktu_selesai' => '15:00:00',
            'ruangan' => 'Lab RPL 2',
            'is_susulan' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.jadwal-praktikum.peserta-susulan.store', $jadwalSusulan->id), [
            'pendaftaran_ids' => [$student->id],
            'catatan' => 'Sakit berhalangan di sesi reguler',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('peserta_susulans', [
            'jadwal_id' => $jadwalSusulan->id,
            'pendaftaran_id' => $student->id,
            'status' => 'terdaftar',
        ]);
    }

    public function test_admin_cannot_enroll_gugur_student_to_susulan(): void
    {
        $admin = $this->createAdmin();
        $praktikum = $this->createPraktikum();
        $sesi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi Reguler',
            'hari' => 'Senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'kuota' => 30,
        ]);

        $studentGugur = $this->createStudent($praktikum, $sesi, 'gugur');

        $jadwalSusulan = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'judul_modul' => 'Modul 1 Dasar',
            'tanggal' => Carbon::tomorrow()->toDateString(),
            'waktu_mulai' => '13:00:00',
            'waktu_selesai' => '15:00:00',
            'ruangan' => 'Lab RPL 2',
            'is_susulan' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.jadwal-praktikum.peserta-susulan.store', $jadwalSusulan->id), [
            'pendaftaran_ids' => [$studentGugur->id],
        ]);

        $response->assertSessionHasErrors();
        $this->assertDatabaseMissing('peserta_susulans', [
            'jadwal_id' => $jadwalSusulan->id,
            'pendaftaran_id' => $studentGugur->id,
        ]);
    }
}
