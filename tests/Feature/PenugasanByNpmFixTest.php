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

class PenugasanByNpmFixTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'lab_rpl',
        ]);

        // Pastikan digit 0-9 aktif
        for ($i = 0; $i <= 9; $i++) {
            DigitNpm::firstOrCreate(
                ['digit' => (string) $i],
                ['label' => "Digit Akhir: $i", 'is_active' => true, 'sort_order' => $i]
            );
        }
    }

    public function test_admin_bulk_store_validates_active_digit_npm(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminUser = User::factory()->create([
            'role_id' => $adminRole->id,
            'username' => 'admin_'.uniqid(),
        ]);

        $praktikum = Praktikum::create([
            'nama_praktikum' => 'Praktikum Uji Validasi '.uniqid(),
            'kode_praktikum' => 'PRK-'.substr(uniqid(), -6),
            'periode_praktikum' => '2026/2027 Ganjil',
            'kuota_praktikan' => 50,
            'status_praktikum' => 'on_progress',
            'jumlah_modul' => 1,
        ]);

        $sesi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi Uji',
            'hari' => 'Senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'kuota' => 30,
        ]);

        Storage::fake('public');

        // Test with invalid digit '99'
        $responseInvalid = $this->actingAs($adminUser)->post(route('admin.penugasan.bulk-store'), [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_umum' => 'Soal Uji Validasi',
            'deskripsi_umum' => 'Instruksi uji validasi',
            'assignments' => [
                [
                    'kode_akhir_npm' => '99',
                    'file_soal' => UploadedFile::fake()->create('test-99.pdf', 100, 'application/pdf'),
                ],
            ],
        ]);

        $responseInvalid->assertSessionHasErrors(['assignments.0.kode_akhir_npm']);

        // Test with valid digit '2'
        $responseValid = $this->actingAs($adminUser)->post(route('admin.penugasan.bulk-store'), [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_umum' => 'Soal Uji Validasi',
            'deskripsi_umum' => 'Instruksi uji validasi',
            'assignments' => [
                [
                    'kode_akhir_npm' => '2',
                    'file_soal' => UploadedFile::fake()->create('test-2.pdf', 100, 'application/pdf'),
                ],
            ],
        ]);

        $responseValid->assertSessionHasNoErrors();
        $this->assertDatabaseHas('penugasans', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'kode_akhir_npm' => '2',
            'judul' => 'Soal Uji Validasi',
        ]);
    }

    public function test_praktikan_with_matching_digit_can_access_and_other_digit_is_forbidden(): void
    {
        $now = Carbon::now('Asia/Jakarta');
        $praktikanRole = Role::firstOrCreate(['name' => 'Praktikan']);
        $user = User::factory()->create([
            'role_id' => $praktikanRole->id,
            'username' => 'praktikan_'.uniqid(),
        ]);
        $praktikan = Praktikan::create([
            'user_id' => $user->id,
            'npm' => '06.9999.1.'.rand(1000, 8999).'2', // NPM berakhiran 2
        ]);

        $praktikum = Praktikum::create([
            'nama_praktikum' => 'Praktikum Uji '.uniqid(),
            'kode_praktikum' => 'PRK-'.substr(uniqid(), -6),
            'periode_praktikum' => '2026/2027 Ganjil',
            'kuota_praktikan' => 50,
            'status_praktikum' => 'on_progress',
            'jumlah_modul' => 1,
        ]);

        $sesi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi Uji',
            'hari' => $now->locale('id')->dayName,
            'jam_mulai' => '00:00:00',
            'jam_selesai' => '23:59:59',
            'kuota' => 30,
        ]);

        $jadwal = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul 1 Test',
            'tanggal' => $now->toDateString(),
            'waktu_mulai' => '00:00:00',
            'waktu_selesai' => '23:59:59',
            'ruangan' => 'RK-201',
            'token' => 'TOKEN_TEST',
        ]);

        $pendaftaran = PendaftaranPraktikum::create([
            'praktikan_id' => $praktikan->id,
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'no_hp' => '081234567890',
            'dosen_pengampu' => 'Dosen Pengampu',
            'kelas' => 'pagi',
            'asal_kelas_mata_kuliah' => 'IF-A',
            'status' => 'verified',
        ]);

        // Simulasikan presensi hadir pada jadwal
        Presensi::create([
            'pendaftaran_id' => $pendaftaran->id,
            'jadwal_id' => $jadwal->id,
            'status' => 'hadir',
            'jam_masuk' => now(),
        ]);

        Storage::fake('public');
        $filePath2 = UploadedFile::fake()->create('soal-2.pdf', 50, 'application/pdf')->store('penugasan_soal', 'public');
        $filePath1 = UploadedFile::fake()->create('soal-1.pdf', 50, 'application/pdf')->store('penugasan_soal', 'public');

        $soalDigit2 = Penugasan::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'jadwal_praktikum_id' => $jadwal->id,
            'kode_akhir_npm' => '2',
            'judul' => 'Soal Digit 2',
            'deskripsi' => 'Deskripsi soal 2',
            'file_soal' => $filePath2,
        ]);

        $soalDigit1 = Penugasan::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'jadwal_praktikum_id' => $jadwal->id,
            'kode_akhir_npm' => '1',
            'judul' => 'Soal Digit 1',
            'deskripsi' => 'Deskripsi soal 1',
            'file_soal' => $filePath1,
        ]);

        // Mahasiswa hanya boleh melihat soal digit 2 di halaman index
        $resIndex = $this->actingAs($user)->get(route('praktikan.penugasan.index'));
        $resIndex->assertOk();
        $resIndex->assertSee('Soal Digit 2');
        $resIndex->assertDontSee('Soal Digit 1');

        // Akses langsung ke soal digit 1 harus 403
        $resShowDigit1 = $this->actingAs($user)->get(route('praktikan.penugasan.show', $soalDigit1->id));
        $resShowDigit1->assertStatus(403);

        $resDownloadDigit1 = $this->actingAs($user)->get(route('praktikan.penugasan.download', $soalDigit1->id));
        $resDownloadDigit1->assertStatus(403);

        // Akses ke soal digit 2 harus 200
        $resShowDigit2 = $this->actingAs($user)->get(route('praktikan.penugasan.show', $soalDigit2->id));
        $resShowDigit2->assertOk();

        $resDownloadDigit2 = $this->actingAs($user)->get(route('praktikan.penugasan.download', $soalDigit2->id));
        $resDownloadDigit2->assertOk();
    }

    public function test_praktikan_does_not_get_false_403_when_pendaftaran_sesi_lookup_handles_praktikum_correctly(): void
    {
        $now = Carbon::now('Asia/Jakarta');
        $praktikanRole = Role::firstOrCreate(['name' => 'Praktikan']);
        $user = User::factory()->create([
            'role_id' => $praktikanRole->id,
            'username' => 'praktikan_flex_'.uniqid(),
        ]);
        $praktikan = Praktikan::create([
            'user_id' => $user->id,
            'npm' => '06.9999.1.'.rand(1000, 8999).'5', // NPM berakhiran 5
        ]);

        $praktikum = Praktikum::create([
            'nama_praktikum' => 'Praktikum Flex '.uniqid(),
            'kode_praktikum' => 'PRK-'.substr(uniqid(), -6),
            'periode_praktikum' => '2026/2027 Ganjil',
            'kuota_praktikan' => 50,
            'status_praktikum' => 'on_progress',
            'jumlah_modul' => 1,
        ]);

        $sesi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi Flex',
            'hari' => $now->locale('id')->dayName,
            'jam_mulai' => '00:00:00',
            'jam_selesai' => '23:59:59',
            'kuota' => 30,
        ]);

        $jadwal = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul Flex',
            'tanggal' => $now->toDateString(),
            'waktu_mulai' => '00:00:00',
            'waktu_selesai' => '23:59:59',
            'ruangan' => 'RK-201',
            'token' => 'TOKEN_FLEX',
        ]);

        $pendaftaran = PendaftaranPraktikum::create([
            'praktikan_id' => $praktikan->id,
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'no_hp' => '081234567890',
            'dosen_pengampu' => 'Dosen Pengampu',
            'kelas' => 'pagi',
            'asal_kelas_mata_kuliah' => 'IF-A',
            'status' => 'verified',
        ]);

        Presensi::create([
            'pendaftaran_id' => $pendaftaran->id,
            'jadwal_id' => $jadwal->id,
            'status' => 'hadir',
            'jam_masuk' => now(),
        ]);

        Storage::fake('public');
        $filePath = UploadedFile::fake()->create('soal-5.pdf', 50, 'application/pdf')->store('penugasan_soal', 'public');

        $soal = Penugasan::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'jadwal_praktikum_id' => $jadwal->id,
            'kode_akhir_npm' => '5',
            'judul' => 'Soal Modul Flex Digit 5',
            'deskripsi' => 'Deskripsi flex',
            'file_soal' => $filePath,
        ]);

        $resShow = $this->actingAs($user)->get(route('praktikan.penugasan.show', $soal->id));
        $resShow->assertOk();

        $resDownload = $this->actingAs($user)->get(route('praktikan.penugasan.download', $soal->id));
        $resDownload->assertOk();
    }
}
