<?php

namespace Tests\Feature;

use App\Models\JadwalPraktikum;
use App\Models\PendaftaranPraktikum;
use App\Models\PesertaSusulan;
use App\Models\Praktikan;
use App\Models\Praktikum;
use App\Models\Role;
use App\Models\SesiPraktikum;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PraktikanDashboardGugurAndSusulanTest extends TestCase
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
            'nama_praktikum' => 'Praktikum Dashboard Test '.uniqid(),
            'kode_praktikum' => 'PRK-'.bin2hex(random_bytes(6)),
            'periode_praktikum' => '2026/2027 Ganjil',
            'kuota_praktikan' => 50,
            'status_praktikum' => 'on_progress',
            'jumlah_modul' => 3,
        ]);
    }

    private function createPraktikanUser(): array
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

        return [$user, $praktikan];
    }

    public function test_gugur_student_sees_alert_on_dashboard(): void
    {
        [$user, $praktikan] = $this->createPraktikanUser();

        $praktikum = $this->createPraktikum();
        $sesi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi Pagi',
            'hari' => 'Senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'kuota' => 30,
        ]);

        PendaftaranPraktikum::create([
            'praktikan_id' => $praktikan->id,
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'status' => 'gugur',
            'no_hp' => '081234567888',
            'dosen_pengampu' => 'Dosen Test',
            'kelas' => 'pagi',
            'asal_kelas_mata_kuliah' => 'A',
        ]);

        $response = $this->actingAs($user)->get(route('praktikan.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('GUGUR');
    }

    public function test_susulan_student_sees_make_up_schedule_notification(): void
    {
        [$user, $praktikan] = $this->createPraktikanUser();

        $praktikum = $this->createPraktikum();
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
            'no_hp' => '081234567888',
            'dosen_pengampu' => 'Dosen Test',
            'kelas' => 'pagi',
            'asal_kelas_mata_kuliah' => 'A',
        ]);

        $jadwalSusulan = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'judul_modul' => 'Modul 2 Spesial Susulan',
            'tanggal' => Carbon::tomorrow()->toDateString(),
            'waktu_mulai' => '13:00:00',
            'waktu_selesai' => '15:00:00',
            'ruangan' => 'Lab RPL 1',
            'is_susulan' => true,
        ]);

        PesertaSusulan::create([
            'jadwal_id' => $jadwalSusulan->id,
            'pendaftaran_id' => $pendaftaran->id,
            'status' => 'terdaftar',
        ]);

        $response = $this->actingAs($user)->get(route('praktikan.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Modul 2 Spesial Susulan');
    }

    public function test_admin_can_reactivate_gugur_student(): void
    {
        $admin = $this->createAdmin();
        $praktikum = $this->createPraktikum();
        $sesi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi Pagi',
            'hari' => 'Senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'kuota' => 30,
        ]);

        [$user, $praktikan] = $this->createPraktikanUser();

        $pendaftaran = PendaftaranPraktikum::create([
            'praktikan_id' => $praktikan->id,
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'status' => 'gugur',
            'no_hp' => '081234567888',
            'dosen_pengampu' => 'Dosen Test',
            'kelas' => 'pagi',
            'asal_kelas_mata_kuliah' => 'A',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.pendaftaran.reactivate', $pendaftaran->id), [
            'alasan' => 'Dispensasi sakit opname disetujui Kaprodi',
        ]);

        $response->assertSessionHas('success');
        $pendaftaran->refresh();
        $this->assertEquals('verified', $pendaftaran->status);
    }
}
