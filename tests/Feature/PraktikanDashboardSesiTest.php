<?php

namespace Tests\Feature;

use App\Models\JadwalPraktikum;
use App\Models\PendaftaranPraktikum;
use App\Models\Praktikan;
use App\Models\Praktikum;
use App\Models\Role;
use App\Models\SesiPraktikum;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PraktikanDashboardSesiTest extends TestCase
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

    public function test_praktikan_dashboard_only_shows_schedule_for_registered_session(): void
    {
        $praktikanRole = Role::firstOrCreate(['name' => 'Praktikan']);
        $user = User::factory()->create([
            'role_id' => $praktikanRole->id,
            'username' => 'user_' . uniqid(),
        ]);
        $praktikan = Praktikan::create([
            'user_id' => $user->id,
            'npm' => '06.9999.1.' . rand(10000, 99999),
            'no_wa' => '081234567890',
            'jenis_kelamin' => 'L',
        ]);

        $praktikum = Praktikum::create([
            'nama_praktikum' => 'PBO Testing',
            'kode_praktikum' => 'PRK-PBO' . rand(100, 999),
            'periode_praktikum' => '2026/2027 Ganjil',
            'kuota_praktikan' => 100,
            'ada_tugas_akhir' => false,
            'tahun_ajaran' => '2026/2027',
            'semester' => 'Ganjil',
            'jumlah_modul' => 2,
            'status_praktikum' => 'on_progress',
        ]);

        $sesi1 = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi 1',
            'hari' => 'Senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'kuota' => 30,
        ]);

        $sesi2 = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi 2',
            'hari' => 'Senin',
            'jam_mulai' => '10:00:00',
            'jam_selesai' => '12:00:00',
            'kuota' => 30,
        ]);

        // Jadwal for Sesi 1 in RK-201
        $jadwalSesi1 = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi1->id,
            'judul_modul' => 'Modul 1: Pengenalan Sesi 1',
            'tanggal' => now()->addDay()->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'ruangan' => 'RK-201',
            'token' => 'TOKEN111',
        ]);

        // Jadwal for Sesi 2 in RK-202
        $jadwalSesi2 = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi2->id,
            'judul_modul' => 'Modul 1: Pengenalan Sesi 2',
            'tanggal' => now()->addDay()->toDateString(),
            'waktu_mulai' => '10:00:00',
            'waktu_selesai' => '12:00:00',
            'ruangan' => 'RK-202',
            'token' => 'TOKEN222',
        ]);

        // Register student to Sesi 2
        PendaftaranPraktikum::create([
            'praktikan_id' => $praktikan->id,
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi2->id,
            'no_hp' => '081234567890',
            'status' => 'verified',
            'dosen_pengampu' => 'Dosen Test',
            'asal_kelas_mata_kuliah' => 'Kelas A',
            'kelas' => 'pagi',
            'is_mengulang' => false,
        ]);

        // Verify SesiPraktikum ruangan accessor
        $this->assertEquals('RK-201', $sesi1->ruangan);
        $this->assertEquals('RK-202', $sesi2->ruangan);

        // Act: Visit praktikan dashboard
        $response = $this->actingAs($user)->get(route('praktikan.dashboard'));

        $response->assertOk();
        // Should see Sesi 2 and RK-202
        $response->assertSee('Modul 1: Pengenalan Sesi 2');
        $response->assertSee('RK-202');

        // Should NOT see Sesi 1 or RK-201
        $response->assertDontSee('Modul 1: Pengenalan Sesi 1');
        $response->assertDontSee('RK-201');
    }

    public function test_praktikan_cannot_generate_qr_for_other_session(): void
    {
        $praktikanRole = Role::firstOrCreate(['name' => 'Praktikan']);
        $user = User::factory()->create([
            'role_id' => $praktikanRole->id,
            'username' => 'user_' . uniqid(),
        ]);
        $praktikan = Praktikan::create([
            'user_id' => $user->id,
            'npm' => '06.9999.1.' . rand(10000, 99999),
            'no_wa' => '081234567891',
            'jenis_kelamin' => 'L',
        ]);

        $praktikum = Praktikum::create([
            'nama_praktikum' => 'Basis Data',
            'kode_praktikum' => 'PRK-BD' . rand(100, 999),
            'periode_praktikum' => '2026/2027 Ganjil',
            'kuota_praktikan' => 100,
            'ada_tugas_akhir' => false,
            'tahun_ajaran' => '2026/2027',
            'semester' => 'Ganjil',
            'jumlah_modul' => 2,
            'status_praktikum' => 'on_progress',
        ]);

        $sesi1 = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi 1',
            'hari' => 'Senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'kuota' => 30,
        ]);

        $sesi2 = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi 2',
            'hari' => 'Senin',
            'jam_mulai' => '10:00:00',
            'jam_selesai' => '12:00:00',
            'kuota' => 30,
        ]);

        $jadwalSesi1 = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi1->id,
            'judul_modul' => 'Modul 1: DDL',
            'tanggal' => now()->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'ruangan' => 'RK-201',
            'token' => 'BDTOKEN1',
        ]);

        // Student registered to Sesi 2
        PendaftaranPraktikum::create([
            'praktikan_id' => $praktikan->id,
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi2->id,
            'no_hp' => '081234567890',
            'status' => 'verified',
            'dosen_pengampu' => 'Dosen Test',
            'asal_kelas_mata_kuliah' => 'Kelas A',
            'kelas' => 'pagi',
            'is_mengulang' => false,
        ]);

        // Attempting to generate QR for Sesi 1 schedule
        $response = $this->actingAs($user)->get(route('praktikan.presensi.generate-qr', $jadwalSesi1->id));

        $response->assertSessionHas('error', 'Sesi Anda tidak sesuai dengan jadwal ini. Silakan presensi di sesi Anda masing-masing.');
    }
}
