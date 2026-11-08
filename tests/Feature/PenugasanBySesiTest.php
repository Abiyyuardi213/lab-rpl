<?php

namespace Tests\Feature;

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
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PenugasanBySesiTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'lab_rpl',
        ]);

        for ($i = 0; $i <= 9; $i++) {
            DigitNpm::firstOrCreate(
                ['digit' => (string) $i],
                ['label' => "Digit Akhir: $i", 'is_active' => true, 'sort_order' => $i]
            );
        }
    }

    private function createPraktikum(string $name): Praktikum
    {
        return Praktikum::create([
            'nama_praktikum' => $name,
            'kode_praktikum' => 'PRK-'.bin2hex(random_bytes(6)),
            'periode_praktikum' => '2026/2027 Ganjil',
            'kuota_praktikan' => 50,
            'status_praktikum' => 'on_progress',
            'jumlah_modul' => 1,
        ]);
    }

    public function test_admin_can_create_penugasan_by_sesi_without_digit_npm_requirement(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminUser = User::factory()->create([
            'role_id' => $adminRole->id,
            'username' => 'admin_'.uniqid(),
        ]);

        $praktikum = $this->createPraktikum('Praktikum By Sesi Test '.uniqid());

        $sesi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi Pagi',
            'hari' => 'Senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'kuota' => 30,
        ]);

        $jadwal = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul 1 Sesi',
            'tanggal' => Carbon::today()->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'ruangan' => 'Lab RPL 1',
        ]);

        Storage::fake('public');
        $file = UploadedFile::fake()->create('sesi-pagi.pdf', 100, 'application/pdf');

        $response = $this->actingAs($adminUser)->post(route('admin.penugasan.store'), [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'jadwal_praktikum_id' => $jadwal->id,
            'tipe_distribusi' => 'by_sesi',
            'judul' => 'Tugas Sesi Pagi Modul 1',
            'deskripsi' => 'Kerjakan seluruh tugas sesi ini secara seragam.',
            'file_soal' => $file,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('penugasans', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'jadwal_praktikum_id' => $jadwal->id,
            'tipe_distribusi' => 'by_sesi',
            'kode_akhir_npm' => '*',
            'judul' => 'Tugas Sesi Pagi Modul 1',
        ]);
    }

    private function generateUniqueNpm(string $endingDigit = ''): string
    {
        do {
            $prefix = '06.'.date('Y').'.1.'.mt_rand(10000, 99999);
            $npm = $endingDigit !== '' ? substr($prefix, 0, -1) . $endingDigit : $prefix;
        } while (Praktikan::where('npm', $npm)->exists());

        return $npm;
    }

    public function test_praktikan_in_same_session_with_any_digit_can_access_by_sesi_penugasan(): void
    {
        $praktikanRole = Role::firstOrCreate(['name' => 'Praktikan']);
        $praktikum = $this->createPraktikum('Praktikum Akses By Sesi '.uniqid());

        $sesiPagi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi Pagi',
            'hari' => 'Senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'kuota' => 30,
        ]);

        $jadwal = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesiPagi->id,
            'judul_modul' => 'Modul 1 Sesi Pagi',
            'tanggal' => Carbon::today()->toDateString(),
            'waktu_mulai' => '00:00:00',
            'waktu_selesai' => '23:59:59',
            'ruangan' => 'Lab RPL 1',
        ]);

        // Student 1 (NPM ending in 3)
        $user1 = User::factory()->create(['role_id' => $praktikanRole->id, 'username' => 'mhs1_'.uniqid()]);
        $praktikan1 = Praktikan::create([
            'user_id' => $user1->id,
            'npm' => $this->generateUniqueNpm('3'),
            'no_hp' => '081234567801',
            'jenis_kelamin' => 'L',
        ]);
        $pendaftaran1 = PendaftaranPraktikum::create([
            'praktikan_id' => $praktikan1->id,
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesiPagi->id,
            'status' => 'verified',
            'no_hp' => '081234567801',
            'dosen_pengampu' => 'Dosen Test',
            'kelas' => 'pagi',
            'asal_kelas_mata_kuliah' => 'P',
        ]);
        Presensi::create([
            'pendaftaran_id' => $pendaftaran1->id,
            'jadwal_id' => $jadwal->id,
            'jam_masuk' => now(),
            'status' => 'hadir',
        ]);

        // Student 2 (NPM ending in 8)
        $user2 = User::factory()->create(['role_id' => $praktikanRole->id, 'username' => 'mhs2_'.uniqid()]);
        $praktikan2 = Praktikan::create([
            'user_id' => $user2->id,
            'npm' => $this->generateUniqueNpm('8'),
            'no_hp' => '081234567802',
            'jenis_kelamin' => 'P',
        ]);
        $pendaftaran2 = PendaftaranPraktikum::create([
            'praktikan_id' => $praktikan2->id,
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesiPagi->id,
            'status' => 'verified',
            'no_hp' => '081234567802',
            'dosen_pengampu' => 'Dosen Test',
            'kelas' => 'pagi',
            'asal_kelas_mata_kuliah' => 'P',
        ]);
        Presensi::create([
            'pendaftaran_id' => $pendaftaran2->id,
            'jadwal_id' => $jadwal->id,
            'jam_masuk' => now(),
            'status' => 'hadir',
        ]);

        Storage::fake('public');
        $filePath = 'penugasan_soal/test-sesi-pagi.pdf';
        Storage::disk('public')->put($filePath, 'Fake PDF content for sesi pagi');

        $penugasan = Penugasan::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesiPagi->id,
            'jadwal_praktikum_id' => $jadwal->id,
            'tipe_distribusi' => 'by_sesi',
            'kode_akhir_npm' => '*',
            'judul' => 'Soal Sesi Pagi',
            'deskripsi' => 'Kerjakan modul 1 sesi pagi',
            'file_soal' => $filePath,
        ]);

        // Student 1 (NPM digit 3) can access
        $resp1 = $this->actingAs($user1)->get(route('praktikan.penugasan.show', $penugasan->id));
        $resp1->assertStatus(200);
        $down1 = $this->actingAs($user1)->get(route('praktikan.penugasan.download', $penugasan->id));
        $down1->assertStatus(200);

        // Student 2 (NPM digit 8) can access
        $resp2 = $this->actingAs($user2)->get(route('praktikan.penugasan.show', $penugasan->id));
        $resp2->assertStatus(200);
        $down2 = $this->actingAs($user2)->get(route('praktikan.penugasan.download', $penugasan->id));
        $down2->assertStatus(200);
    }

    public function test_aslab_can_create_penugasan_by_sesi(): void
    {
        $aslabRole = Role::firstOrCreate(['name' => 'Aslab']);
        $aslabUser = User::factory()->create([
            'role_id' => $aslabRole->id,
            'username' => 'aslab_'.uniqid(),
        ]);
        $aslab = \App\Models\Aslab::create([
            'user_id' => $aslabUser->id,
            'npm' => '06.2021.1.'.rand(10000, 99999),
            'no_hp' => '081234567899',
        ]);

        $praktikum = $this->createPraktikum('Praktikum Aslab By Sesi '.uniqid());
        \App\Models\AslabPraktikum::create([
            'aslab_id' => $aslab->id,
            'praktikum_id' => $praktikum->id,
        ]);

        $sesi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi Aslab',
            'hari' => 'Selasa',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'kuota' => 30,
        ]);

        Storage::fake('public');
        $file = UploadedFile::fake()->create('sesi-aslab.pdf', 100, 'application/pdf');

        $response = $this->actingAs($aslabUser)->post(route('aslab.penugasan.store'), [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'tipe_distribusi' => 'by_sesi',
            'judul' => 'Tugas Aslab Sesi',
            'deskripsi' => 'Kerjakan tugas sesi ini',
            'file_soal' => $file,
        ]);

        $response->assertRedirect(route('aslab.penugasan.index'));
        $this->assertDatabaseHas('penugasans', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'aslab_id' => $aslab->id,
            'tipe_distribusi' => 'by_sesi',
            'kode_akhir_npm' => '*',
            'judul' => 'Tugas Aslab Sesi',
        ]);
    }

    public function test_praktikan_in_different_session_cannot_access_by_sesi_penugasan(): void
    {
        $praktikanRole = Role::firstOrCreate(['name' => 'Praktikan']);
        $praktikum = $this->createPraktikum('Praktikum Isolasi Sesi '.uniqid());

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

        $jadwalPagi = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesiPagi->id,
            'judul_modul' => 'Modul 1 Sesi Pagi',
            'tanggal' => Carbon::today()->toDateString(),
            'waktu_mulai' => '00:00:00',
            'waktu_selesai' => '23:59:59',
            'ruangan' => 'Lab RPL 1',
        ]);

        // Student 3 in Sesi Malam
        $user3 = User::factory()->create(['role_id' => $praktikanRole->id, 'username' => 'mhs3_'.uniqid()]);
        $praktikan3 = Praktikan::create([
            'user_id' => $user3->id,
            'npm' => $this->generateUniqueNpm('5'),
            'no_hp' => '081234567803',
            'jenis_kelamin' => 'L',
        ]);
        $pendaftaran3 = PendaftaranPraktikum::create([
            'praktikan_id' => $praktikan3->id,
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesiMalam->id,
            'status' => 'verified',
            'no_hp' => '081234567803',
            'dosen_pengampu' => 'Dosen Test',
            'kelas' => 'malam',
            'asal_kelas_mata_kuliah' => 'V',
        ]);

        Storage::fake('public');
        $filePath = 'penugasan_soal/test-sesi-pagi.pdf';
        Storage::disk('public')->put($filePath, 'Fake PDF content');

        $penugasanPagi = Penugasan::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesiPagi->id,
            'jadwal_praktikum_id' => $jadwalPagi->id,
            'tipe_distribusi' => 'by_sesi',
            'kode_akhir_npm' => '*',
            'judul' => 'Soal Sesi Pagi Khusus',
            'deskripsi' => 'Kerjakan modul 1',
            'file_soal' => $filePath,
        ]);

        // Student in Sesi Malam must get 403
        $resp = $this->actingAs($user3)->get(route('praktikan.penugasan.show', $penugasanPagi->id));
        $resp->assertStatus(403);

        $down = $this->actingAs($user3)->get(route('praktikan.penugasan.download', $penugasanPagi->id));
        $down->assertStatus(403);
    }
}
