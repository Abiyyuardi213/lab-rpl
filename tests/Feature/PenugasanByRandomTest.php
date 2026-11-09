<?php

namespace Tests\Feature;

use App\Models\Aslab;
use App\Models\AslabPraktikum;
use App\Models\DigitNpm;
use App\Models\JadwalPraktikum;
use App\Models\PendaftaranPraktikum;
use App\Models\Penugasan;
use App\Models\PenugasanPraktikanOverride;
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

class PenugasanByRandomTest extends TestCase
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

    private function generateUniqueNpm(): string
    {
        do {
            $npm = '06.'.date('Y').'.1.'.mt_rand(100000, 999999);
        } while (Praktikan::where('npm', $npm)->exists());

        return $npm;
    }

    public function test_admin_can_create_penugasan_by_random_pool(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminUser = User::factory()->create([
            'role_id' => $adminRole->id,
            'username' => 'admin_'.uniqid(),
        ]);

        $praktikum = $this->createPraktikum('Praktikum Admin Random '.uniqid());
        $sesi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi Pagi',
            'hari' => 'Senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'kuota' => 30,
        ]);

        Storage::fake('public');
        $file = UploadedFile::fake()->create('random-1.pdf', 100, 'application/pdf');

        $response = $this->actingAs($adminUser)->post(route('admin.penugasan.store'), [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'tipe_distribusi' => 'by_random',
            'judul' => 'Paket Soal Random A',
            'deskripsi' => 'Kerjakan paket soal A ini',
            'file_soal' => $file,
        ]);

        $response->assertRedirect(route('admin.penugasan.index'));
        $this->assertDatabaseHas('penugasans', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'tipe_distribusi' => 'by_random',
            'kode_akhir_npm' => 'random',
            'judul' => 'Paket Soal Random A',
        ]);
    }

    public function test_aslab_can_create_penugasan_by_random_pool(): void
    {
        $aslabRole = Role::firstOrCreate(['name' => 'Aslab']);
        $aslabUser = User::factory()->create([
            'role_id' => $aslabRole->id,
            'username' => 'aslab_'.uniqid(),
        ]);
        $aslab = Aslab::create([
            'user_id' => $aslabUser->id,
            'npm' => '06.2021.1.'.rand(10000, 99999),
            'no_hp' => '081234567899',
        ]);

        $praktikum = $this->createPraktikum('Praktikum Aslab Random '.uniqid());
        AslabPraktikum::create([
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
        $file = UploadedFile::fake()->create('random-2.pdf', 100, 'application/pdf');

        $response = $this->actingAs($aslabUser)->post(route('aslab.penugasan.store'), [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'tipe_distribusi' => 'by_random',
            'judul' => 'Paket Soal Random Aslab',
            'deskripsi' => 'Kerjakan paket variasi ini',
            'file_soal' => $file,
        ]);

        $response->assertRedirect(route('aslab.penugasan.index'));
        $this->assertDatabaseHas('penugasans', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'aslab_id' => $aslab->id,
            'tipe_distribusi' => 'by_random',
            'kode_akhir_npm' => 'random',
            'judul' => 'Paket Soal Random Aslab',
        ]);
    }

    public function test_praktikan_accessing_random_pool_gets_one_assignment_and_locks_permanently(): void
    {
        $praktikanRole = Role::firstOrCreate(['name' => 'Praktikan']);
        $praktikum = $this->createPraktikum('Praktikum Pool Random '.uniqid());

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
            'judul_modul' => 'Modul 1 Random',
            'tanggal' => Carbon::today()->toDateString(),
            'waktu_mulai' => '00:00:00',
            'waktu_selesai' => '23:59:59',
            'ruangan' => 'Lab RPL 1',
        ]);

        // Buat 3 soal dalam pool random
        Storage::fake('public');
        $file1 = 'penugasan_soal/random-1.pdf';
        $file2 = 'penugasan_soal/random-2.pdf';
        $file3 = 'penugasan_soal/random-3.pdf';
        Storage::disk('public')->put($file1, 'PDF 1');
        Storage::disk('public')->put($file2, 'PDF 2');
        Storage::disk('public')->put($file3, 'PDF 3');

        $soalA = Penugasan::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'jadwal_praktikum_id' => $jadwal->id,
            'tipe_distribusi' => 'by_random',
            'kode_akhir_npm' => 'random',
            'judul' => 'Soal Random Paket A',
            'deskripsi' => 'Deskripsi A',
            'file_soal' => $file1,
        ]);

        $soalB = Penugasan::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'jadwal_praktikum_id' => $jadwal->id,
            'tipe_distribusi' => 'by_random',
            'kode_akhir_npm' => 'random',
            'judul' => 'Soal Random Paket B',
            'deskripsi' => 'Deskripsi B',
            'file_soal' => $file2,
        ]);

        $soalC = Penugasan::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'jadwal_praktikum_id' => $jadwal->id,
            'tipe_distribusi' => 'by_random',
            'kode_akhir_npm' => 'random',
            'judul' => 'Soal Random Paket C',
            'deskripsi' => 'Deskripsi C',
            'file_soal' => $file3,
        ]);

        // Praktikan 1
        $user1 = User::factory()->create(['role_id' => $praktikanRole->id, 'username' => 'mhs1_'.uniqid()]);
        $praktikan1 = Praktikan::create([
            'user_id' => $user1->id,
            'npm' => $this->generateUniqueNpm(),
            'no_hp' => '081234567801',
            'jenis_kelamin' => 'L',
        ]);
        $pendaftaran1 = PendaftaranPraktikum::create([
            'praktikan_id' => $praktikan1->id,
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
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

        // 1. Akses index pertama kali
        $response1 = $this->actingAs($user1)->get(route('praktikan.penugasan.index'));
        $response1->assertStatus(200);

        // Harus tercatat di penugasan_praktikan_overrides
        $override = PenugasanPraktikanOverride::where('pendaftaran_id', $pendaftaran1->id)
            ->where('jadwal_praktikum_id', $jadwal->id)
            ->first();

        $this->assertNotNull($override, 'Praktikan harus otomatis terkunci di penugasan_praktikan_overrides.');
        $lockedPenugasanId = $override->penugasan_id;
        $this->assertTrue(in_array($lockedPenugasanId, [$soalA->id, $soalB->id, $soalC->id]), 'Soal terkunci harus salah satu dari pool A, B, atau C.');

        // 2. Akses index kedua kali (refresh browser) -> soal tetap terkunci pada ID yang sama
        $this->actingAs($user1)->get(route('praktikan.penugasan.index'));
        $overrideAfterRefresh = PenugasanPraktikanOverride::where('pendaftaran_id', $pendaftaran1->id)
            ->where('jadwal_praktikum_id', $jadwal->id)
            ->first();
        $this->assertEquals($lockedPenugasanId, $overrideAfterRefresh->penugasan_id, 'Penguncian soal harus persisten dan tidak berubah saat refresh.');

        // 3. Praktikan dapat membuka show dan download soal yang terkunci
        $showResponse = $this->actingAs($user1)->get(route('praktikan.penugasan.show', $lockedPenugasanId));
        $showResponse->assertStatus(200);

        $downloadResponse = $this->actingAs($user1)->get(route('praktikan.penugasan.download', $lockedPenugasanId));
        $downloadResponse->assertStatus(200);

        // 4. Praktikan DITOLAK (403) jika mencoba mengakses variasi soal lain dari pool yang bukan miliknya
        $otherSoalIds = array_diff([$soalA->id, $soalB->id, $soalC->id], [$lockedPenugasanId]);
        foreach ($otherSoalIds as $forbiddenId) {
            $forbiddenShow = $this->actingAs($user1)->get(route('praktikan.penugasan.show', $forbiddenId));
            $forbiddenShow->assertStatus(403);

            $forbiddenDown = $this->actingAs($user1)->get(route('praktikan.penugasan.download', $forbiddenId));
            $forbiddenDown->assertStatus(403);
        }
    }

    public function test_praktikan_in_different_session_cannot_access_random_assignment(): void
    {
        $praktikanRole = Role::firstOrCreate(['name' => 'Praktikan']);
        $praktikum = $this->createPraktikum('Praktikum Isolasi Random '.uniqid());

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

        // Student Sesi Malam
        $userMalam = User::factory()->create(['role_id' => $praktikanRole->id, 'username' => 'mhsmalam_'.uniqid()]);
        $praktikanMalam = Praktikan::create([
            'user_id' => $userMalam->id,
            'npm' => $this->generateUniqueNpm(),
            'no_hp' => '081234567809',
            'jenis_kelamin' => 'L',
        ]);
        PendaftaranPraktikum::create([
            'praktikan_id' => $praktikanMalam->id,
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesiMalam->id,
            'status' => 'verified',
            'no_hp' => '081234567809',
            'dosen_pengampu' => 'Dosen Test',
            'kelas' => 'malam',
            'asal_kelas_mata_kuliah' => 'V',
        ]);

        Storage::fake('public');
        $filePagi = 'penugasan_soal/random-pagi.pdf';
        Storage::disk('public')->put($filePagi, 'PDF Pagi');

        $soalRandomPagi = Penugasan::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesiPagi->id,
            'jadwal_praktikum_id' => $jadwalPagi->id,
            'tipe_distribusi' => 'by_random',
            'kode_akhir_npm' => 'random',
            'judul' => 'Soal Random Sesi Pagi',
            'deskripsi' => 'Kerjakan modul 1 pagi',
            'file_soal' => $filePagi,
        ]);

        // Student in Sesi Malam must get 403
        $resp = $this->actingAs($userMalam)->get(route('praktikan.penugasan.show', $soalRandomPagi->id));
        $resp->assertStatus(403);

        $down = $this->actingAs($userMalam)->get(route('praktikan.penugasan.download', $soalRandomPagi->id));
        $down->assertStatus(403);
    }

    public function test_admin_can_upload_multiple_files_up_to_10_in_by_random_mode(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminUser = User::factory()->create([
            'role_id' => $adminRole->id,
            'username' => 'admin_'.uniqid(),
        ]);

        $praktikum = $this->createPraktikum('Praktikum Admin Random Batch '.uniqid());
        $sesi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi Siang',
            'hari' => 'Selasa',
            'jam_mulai' => '13:00:00',
            'jam_selesai' => '15:00:00',
            'kuota' => 30,
        ]);

        Storage::fake('public');
        $files = [
            UploadedFile::fake()->create('paket-1.pdf', 100, 'application/pdf'),
            UploadedFile::fake()->create('paket-2.pdf', 100, 'application/pdf'),
            UploadedFile::fake()->create('paket-3.pdf', 100, 'application/pdf'),
        ];

        $response = $this->actingAs($adminUser)->post(route('admin.penugasan.store'), [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'tipe_distribusi' => 'by_random',
            'judul' => 'Tugas Modul Random',
            'deskripsi' => 'Instruksi pengerjaan variasi soal',
            'files_random' => $files,
        ]);

        $response->assertRedirect(route('admin.penugasan.index'));

        $this->assertDatabaseHas('penugasans', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'tipe_distribusi' => 'by_random',
            'kode_akhir_npm' => 'random',
            'judul' => 'Tugas Modul Random (Paket 1)',
        ]);
        $this->assertDatabaseHas('penugasans', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'tipe_distribusi' => 'by_random',
            'kode_akhir_npm' => 'random',
            'judul' => 'Tugas Modul Random (Paket 2)',
        ]);
        $this->assertDatabaseHas('penugasans', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'tipe_distribusi' => 'by_random',
            'kode_akhir_npm' => 'random',
            'judul' => 'Tugas Modul Random (Paket 3)',
        ]);

        $created = Penugasan::where('praktikum_id', $praktikum->id)->get();
        $this->assertCount(3, $created);
        foreach ($created as $p) {
            Storage::disk('public')->assertExists($p->file_soal);
        }
    }

    public function test_admin_multiple_upload_fails_if_exceeding_10_files(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminUser = User::factory()->create([
            'role_id' => $adminRole->id,
            'username' => 'admin_'.uniqid(),
        ]);

        $praktikum = $this->createPraktikum('Praktikum Admin Random Max '.uniqid());
        $sesi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi Sore',
            'hari' => 'Rabu',
            'jam_mulai' => '15:00:00',
            'jam_selesai' => '17:00:00',
            'kuota' => 30,
        ]);

        Storage::fake('public');
        $files = [];
        for ($i = 1; $i <= 11; $i++) {
            $files[] = UploadedFile::fake()->create("paket-{$i}.pdf", 100, 'application/pdf');
        }

        $response = $this->actingAs($adminUser)->post(route('admin.penugasan.store'), [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'tipe_distribusi' => 'by_random',
            'judul' => 'Tugas Modul Terlalu Banyak',
            'deskripsi' => 'Instruksi pengerjaan',
            'files_random' => $files,
        ]);

        $response->assertSessionHasErrors('files_random');
        $this->assertEquals(0, Penugasan::where('praktikum_id', $praktikum->id)->count());
    }
}
