<?php

namespace Tests\Feature;

use App\Models\Aslab;
use App\Models\JadwalPraktikum;
use App\Models\PendaftaranPraktikum;
use App\Models\Praktikan;
use App\Models\Praktikum;
use App\Models\Presensi;
use App\Models\Role;
use App\Models\SesiPraktikum;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Tests\TestCase;

class PresensiDoubleScanTest extends TestCase
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

    public function test_double_scan_check_in_does_not_insert_duplicate_attendance(): void
    {
        $aslabRole = Role::firstOrCreate(['name' => 'Aslab']);
        $praktikanRole = Role::firstOrCreate(['name' => 'Praktikan']);

        $userAslab = User::factory()->create([
            'role_id' => $aslabRole->id,
            'username' => 'aslab_'.uniqid(),
        ]);
        $aslab = Aslab::create([
            'user_id' => $userAslab->id,
            'npm' => '06.2021.1.'.rand(10000, 99999),
            'no_hp' => '081234567891',
        ]);

        $userPraktikan = User::factory()->create([
            'role_id' => $praktikanRole->id,
            'username' => 'praktikan_'.uniqid(),
        ]);
        $praktikan = Praktikan::create([
            'user_id' => $userPraktikan->id,
            'npm' => '06.2024.1.'.rand(10000, 99999),
            'no_wa' => '081234567892',
            'jenis_kelamin' => 'L',
        ]);

        $praktikum = Praktikum::create([
            'nama_praktikum' => 'Test Presensi Double Scan',
            'kode_praktikum' => 'PRK-DSC'.bin2hex(random_bytes(6)),
            'periode_praktikum' => '2026/2027 Ganjil',
            'kuota_praktikan' => 50,
            'ada_tugas_akhir' => false,
            'tahun_ajaran' => '2026/2027',
            'semester' => 'Ganjil',
            'jumlah_modul' => 2,
            'status_praktikum' => 'open_registration',
        ]);

        $aslab->praktikums()->attach($praktikum->id, ['id' => (string) Str::uuid()]);

        $sesi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi Pagi',
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'kuota' => 25,
            'terisi' => 1,
        ]);

        $pendaftaran = PendaftaranPraktikum::create([
            'praktikan_id' => $praktikan->id,
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'aslab_id' => $aslab->id,
            'no_hp' => '081234567892',
            'dosen_pengampu' => 'Dosen Test',
            'kelas' => 'pagi',
            'asal_kelas_mata_kuliah' => 'A',
            'bukti_krs' => 'dummy_krs.jpg',
            'bukti_pembayaran' => 'dummy_pay.jpg',
            'foto_almamater' => 'dummy_alma.jpg',
            'status' => 'verified',
        ]);

        $jadwal = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul 1',
            'tanggal' => now()->toDateString(),
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '10:00',
            'ruangan' => 'Lab RPL 1',
            'token' => Str::random(32),
        ]);

        $token = Str::random(32);
        Cache::put("presensi_token_{$token}", [
            'pendaftaran_id' => $pendaftaran->id,
            'jadwal_id' => $jadwal->id,
            'user_id' => $userPraktikan->id,
        ], now()->addMinutes(30));

        // Scan 1 by Aslab
        $response1 = $this->actingAs($userAslab)->postJson(route('aslab.presensi.check-in'), [
            'token' => $token,
        ]);
        $response1->assertStatus(200);
        $response1->assertJson(['success' => true]);

        // Verify exactly 1 presensi was created
        $countAfterFirst = Presensi::where('jadwal_id', $jadwal->id)
            ->where('pendaftaran_id', $pendaftaran->id)
            ->count();
        $this->assertEquals(1, $countAfterFirst);

        // Scan 2 with the same token (immediate re-scan or duplicate camera callback)
        $response2 = $this->actingAs($userAslab)->postJson(route('aslab.presensi.check-in'), [
            'token' => $token,
        ]);

        // Scan 2 MUST NOT be success and MUST NOT create a second record
        $response2->assertStatus(400);

        $countAfterSecond = Presensi::where('jadwal_id', $jadwal->id)
            ->where('pendaftaran_id', $pendaftaran->id)
            ->count();
        $this->assertEquals(1, $countAfterSecond);
    }

    public function test_student_cannot_attend_same_modul_twice_across_different_schedules(): void
    {
        $aslabRole = Role::firstOrCreate(['name' => 'Aslab']);
        $praktikanRole = Role::firstOrCreate(['name' => 'Praktikan']);

        $userAslab = User::factory()->create([
            'role_id' => $aslabRole->id,
            'username' => 'aslab_'.uniqid(),
        ]);
        $aslab = Aslab::create([
            'user_id' => $userAslab->id,
            'npm' => '06.2021.1.'.rand(10000, 99999),
            'no_hp' => '081234567891',
        ]);

        $userPraktikan = User::factory()->create([
            'role_id' => $praktikanRole->id,
            'username' => 'praktikan_'.uniqid(),
        ]);
        $praktikan = Praktikan::create([
            'user_id' => $userPraktikan->id,
            'npm' => '06.2024.1.'.rand(10000, 99999),
            'no_wa' => '081234567892',
            'jenis_kelamin' => 'L',
        ]);

        $praktikum = Praktikum::create([
            'nama_praktikum' => 'Test Modul Duplicate Attendance',
            'kode_praktikum' => 'PRK-MDA'.bin2hex(random_bytes(6)),
            'periode_praktikum' => '2026/2027 Ganjil',
            'kuota_praktikan' => 50,
            'ada_tugas_akhir' => false,
            'tahun_ajaran' => '2026/2027',
            'semester' => 'Ganjil',
            'jumlah_modul' => 2,
            'status_praktikum' => 'open_registration',
        ]);

        $sesi1 = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi 1',
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'kuota' => 25,
            'terisi' => 1,
        ]);

        $sesi2 = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi 2',
            'hari' => 'Senin',
            'jam_mulai' => '10:00',
            'jam_selesai' => '12:00',
            'kuota' => 25,
            'terisi' => 0,
        ]);

        $pendaftaran = PendaftaranPraktikum::create([
            'praktikan_id' => $praktikan->id,
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi1->id,
            'aslab_id' => $aslab->id,
            'no_hp' => '081234567892',
            'dosen_pengampu' => 'Dosen Test',
            'kelas' => 'pagi',
            'asal_kelas_mata_kuliah' => 'A',
            'bukti_krs' => 'dummy_krs.jpg',
            'bukti_pembayaran' => 'dummy_pay.jpg',
            'foto_almamater' => 'dummy_alma.jpg',
            'status' => 'verified',
        ]);

        $jadwal1 = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi1->id,
            'judul_modul' => 'Modul 1',
            'tanggal' => now()->toDateString(),
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '10:00',
            'ruangan' => 'Lab 1',
            'token' => Str::random(32),
        ]);

        $jadwal2 = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi2->id,
            'judul_modul' => 'Modul 1',
            'tanggal' => now()->toDateString(),
            'waktu_mulai' => '10:00',
            'waktu_selesai' => '12:00',
            'ruangan' => 'Lab 2',
            'token' => Str::random(32),
        ]);

        // Student attended Modul 1 on Jadwal 1
        Presensi::create([
            'jadwal_id' => $jadwal1->id,
            'pendaftaran_id' => $pendaftaran->id,
            'jam_masuk' => now(),
            'status' => 'hadir',
        ]);

        // Try to scan Jadwal 2 (same Modul 1)
        $response = $this->actingAs($userPraktikan)->get(route('praktikan.presensi.scan-jadwal', $jadwal2->token));

        // It should redirect and NOT insert a new presensi for Modul 1
        $totalPresensiForModul1 = Presensi::where('pendaftaran_id', $pendaftaran->id)
            ->whereHas('jadwal', function ($q) use ($praktikum) {
                $q->where('praktikum_id', $praktikum->id)
                    ->where('judul_modul', 'Modul 1');
            })
            ->count();

        $this->assertEquals(1, $totalPresensiForModul1);
    }

    public function test_student_cannot_attend_same_modul_twice_if_multiple_schedules_exist_for_same_modul(): void
    {
        $praktikanRole = Role::firstOrCreate(['name' => 'Praktikan']);
        $userPraktikan = User::factory()->create(['role_id' => $praktikanRole->id, 'username' => 'praktikan_'.uniqid()]);
        $praktikan = Praktikan::create([
            'user_id' => $userPraktikan->id,
            'npm' => '06.2024.1.'.rand(10000, 99999),
            'no_wa' => '081234567892',
            'jenis_kelamin' => 'L',
        ]);

        $praktikum = Praktikum::create([
            'nama_praktikum' => 'Test Modul Null Sesi',
            'kode_praktikum' => 'PRK-MNS'.bin2hex(random_bytes(6)),
            'periode_praktikum' => '2026/2027 Ganjil',
            'kuota_praktikan' => 50,
            'ada_tugas_akhir' => false,
            'tahun_ajaran' => '2026/2027',
            'semester' => 'Ganjil',
            'jumlah_modul' => 2,
            'status_praktikum' => 'open_registration',
        ]);

        $sesi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi Bebas',
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'kuota' => 25,
            'terisi' => 1,
        ]);

        $pendaftaran = PendaftaranPraktikum::create([
            'praktikan_id' => $praktikan->id,
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'no_hp' => '081234567892',
            'dosen_pengampu' => 'Dosen Test',
            'kelas' => 'pagi',
            'asal_kelas_mata_kuliah' => 'A',
            'bukti_krs' => 'dummy_krs.jpg',
            'bukti_pembayaran' => 'dummy_pay.jpg',
            'foto_almamater' => 'dummy_alma.jpg',
            'status' => 'verified',
        ]);

        $jadwalA = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => null,
            'judul_modul' => 'Modul 1',
            'tanggal' => now()->toDateString(),
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '10:00',
            'ruangan' => 'Lab 1',
            'token' => Str::random(32),
        ]);

        $jadwalB = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => null,
            'judul_modul' => 'Modul 1',
            'tanggal' => now()->toDateString(),
            'waktu_mulai' => '10:00',
            'waktu_selesai' => '12:00',
            'ruangan' => 'Lab 2',
            'token' => Str::random(32),
        ]);

        // Student attended Modul 1 on Jadwal A
        Presensi::create([
            'jadwal_id' => $jadwalA->id,
            'pendaftaran_id' => $pendaftaran->id,
            'jam_masuk' => now(),
            'status' => 'hadir',
        ]);

        // Student scans Jadwal B (which is also Modul 1)
        $response = $this->actingAs($userPraktikan)->get(route('praktikan.presensi.scan-jadwal', $jadwalB->token));

        $totalPresensiForModul1 = Presensi::where('pendaftaran_id', $pendaftaran->id)
            ->whereHas('jadwal', function ($q) use ($praktikum) {
                $q->where('praktikum_id', $praktikum->id)
                    ->where('judul_modul', 'Modul 1');
            })
            ->count();

        $this->assertEquals(1, $totalPresensiForModul1);
    }
}
