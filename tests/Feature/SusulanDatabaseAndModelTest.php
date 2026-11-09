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

class SusulanDatabaseAndModelTest extends TestCase
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
            'nama_praktikum' => 'Praktikum Migrasi Test '.uniqid(),
            'kode_praktikum' => 'PRK-'.bin2hex(random_bytes(6)),
            'periode_praktikum' => '2026/2027 Ganjil',
            'kuota_praktikan' => 50,
            'status_praktikum' => 'on_progress',
            'jumlah_modul' => 2,
        ]);
    }

    private function createPraktikan(): Praktikan
    {
        $role = Role::firstOrCreate(['name' => 'Praktikan']);
        $user = User::factory()->create([
            'role_id' => $role->id,
            'username' => 'user_'.uniqid(),
        ]);
        do {
            $npm = '06.'.date('Y').'.1.'.mt_rand(100000, 999999);
        } while (Praktikan::where('npm', $npm)->exists());

        return Praktikan::create([
            'user_id' => $user->id,
            'npm' => $npm,
            'no_wa' => '081234567888',
            'jenis_kelamin' => 'L',
        ]);
    }

    public function test_pendaftaran_supports_gugur_status(): void
    {
        $praktikan = $this->createPraktikan();

        $praktikum = $this->createPraktikum();
        $sesi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi Test',
            'hari' => 'Senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'kuota' => 30,
        ]);

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

        $this->assertDatabaseHas('pendaftaran_praktikums', [
            'id' => $pendaftaran->id,
            'status' => 'gugur',
        ]);
    }

    public function test_jadwal_and_presensi_support_is_susulan(): void
    {
        $praktikum = $this->createPraktikum();
        $jadwal = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'judul_modul' => 'Modul Susulan Test',
            'tanggal' => Carbon::today()->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'ruangan' => 'Lab RPL 1',
            'is_susulan' => true,
        ]);

        $this->assertTrue($jadwal->is_susulan);
        $this->assertDatabaseHas('jadwal_praktikums', [
            'id' => $jadwal->id,
            'is_susulan' => 1,
        ]);
    }

    public function test_peserta_susulan_relationship(): void
    {
        $praktikan = $this->createPraktikan();

        $praktikum = $this->createPraktikum();
        $sesi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi Test',
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
            'judul_modul' => 'Modul 1 Susulan',
            'tanggal' => Carbon::today()->toDateString(),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'ruangan' => 'Lab RPL 1',
            'is_susulan' => true,
        ]);

        $peserta = PesertaSusulan::create([
            'jadwal_id' => $jadwalSusulan->id,
            'pendaftaran_id' => $pendaftaran->id,
            'status' => 'terdaftar',
            'catatan' => 'Sakit surat dokter',
        ]);

        $this->assertDatabaseHas('peserta_susulans', [
            'id' => $peserta->id,
            'jadwal_id' => $jadwalSusulan->id,
            'pendaftaran_id' => $pendaftaran->id,
            'status' => 'terdaftar',
        ]);

        $this->assertEquals($jadwalSusulan->id, $peserta->jadwal->id);
        $this->assertEquals($pendaftaran->id, $peserta->pendaftaran->id);
        $this->assertTrue($jadwalSusulan->pesertaSusulans->contains($peserta));
        $this->assertTrue($pendaftaran->pesertaSusulans->contains($peserta));
    }
}
