<?php

namespace Tests\Feature;

use App\Models\PendaftaranPraktikum;
use App\Models\Praktikan;
use App\Models\Praktikum;
use App\Models\Role;
use App\Models\SesiPraktikum;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PendaftaranTwoStepVerificationTest extends TestCase
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
        $unique = rand(10000, 99999).'_'.time();

        return User::factory()->create([
            'role_id' => $role->id,
            'username' => 'admin_test_'.$unique,
            'email' => 'admin_test_'.$unique.'@example.com',
            'name' => 'Admin 2Step Test',
        ]);
    }

    private function createPraktikan(): array
    {
        $role = Role::firstOrCreate(['name' => 'Praktikan']);
        $unique = rand(10000, 99999).'_'.time();

        $user = User::factory()->create([
            'role_id' => $role->id,
            'username' => 'praktikan_'.$unique,
            'email' => 'praktikan_'.$unique.'@example.com',
            'name' => 'Praktikan 2Step '.$unique,
        ]);

        $praktikan = Praktikan::create([
            'user_id' => $user->id,
            'npm' => '06.'.rand(2020, 2026).'.1.'.rand(10000, 99999),
        ]);

        return [$user, $praktikan];
    }

    private function createPraktikumDanSesi(): array
    {
        $praktikum = Praktikum::create([
            'nama_praktikum' => 'Praktikum Uji 2-Step '.rand(100, 999),
            'kode_praktikum' => 'P2S'.rand(10, 99),
            'periode_praktikum' => '2026/2027 Ganjil',
            'kuota_praktikan' => 50,
            'jumlah_modul' => 3,
            'ada_tugas_akhir' => false,
            'status_praktikum' => 'on_progress',
        ]);

        $sesi = SesiPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'nama_sesi' => 'Sesi Uji '.rand(10, 99),
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'kuota' => 20,
        ]);

        return [$praktikum, $sesi];
    }

    private function createPendaftaran(Praktikan $praktikan, Praktikum $praktikum, SesiPraktikum $sesi): PendaftaranPraktikum
    {
        return PendaftaranPraktikum::create([
            'praktikan_id' => $praktikan->id,
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'status' => 'pending',
            'status_lembar_kuning' => 'pending',
            'status_berkas' => 'pending',
            'no_hp' => '081234567890',
            'kelas' => 'Pagi',
            'asal_kelas_mata_kuliah' => 'A',
            'dosen_pengampu' => 'Dosen Pengampu Uji',
        ]);
    }

    public function test_pendaftaran_requires_both_lembar_kuning_and_berkas_to_be_active(): void
    {
        $admin = $this->createAdmin();
        [$praktikum, $sesi] = $this->createPraktikumDanSesi();
        [$user, $praktikan] = $this->createPraktikan();
        $pendaftaran = $this->createPendaftaran($praktikan, $praktikum, $sesi);

        // Kasus 1: Hanya Lembar Kuning verified, Berkas masih pending
        $response1 = $this->actingAs($admin)->patch("/administrator/pendaftaran/{$pendaftaran->id}/status", [
            'status_lembar_kuning' => 'verified',
            'status_berkas' => 'pending',
        ]);
        $response1->assertRedirect(route('admin.pendaftaran.index'));

        $p = $pendaftaran->fresh();
        $this->assertEquals('verified', $p->status_lembar_kuning);
        $this->assertEquals('pending', $p->status_berkas);
        $this->assertEquals('pending', $p->status, 'Master status harus tetap pending jika berkas belum verified');
        $this->assertFalse($p->isFullyVerified());

        // Kasus 2: Hanya Berkas verified, Lembar Kuning pending
        $response2 = $this->actingAs($admin)->patch("/administrator/pendaftaran/{$pendaftaran->id}/status", [
            'status_lembar_kuning' => 'pending',
            'status_berkas' => 'verified',
        ]);
        $response2->assertRedirect(route('admin.pendaftaran.index'));

        $p = $pendaftaran->fresh();
        $this->assertEquals('pending', $p->status_lembar_kuning);
        $this->assertEquals('verified', $p->status_berkas);
        $this->assertEquals('pending', $p->status, 'Master status harus tetap pending jika lembar kuning belum verified');
        $this->assertFalse($p->isFullyVerified());

        // Kasus 3: KEDUA verifikasi verified
        $response3 = $this->actingAs($admin)->patch("/administrator/pendaftaran/{$pendaftaran->id}/status", [
            'status_lembar_kuning' => 'verified',
            'status_berkas' => 'verified',
        ]);
        $response3->assertRedirect(route('admin.pendaftaran.index'));

        $p = $pendaftaran->fresh();
        $this->assertEquals('verified', $p->status_lembar_kuning);
        $this->assertEquals('verified', $p->status_berkas);
        $this->assertEquals('verified', $p->status, 'Master status harus verified jika kedua tahap verified');
        $this->assertTrue($p->isFullyVerified());
    }

    public function test_pendaftaran_is_rejected_if_either_step_is_rejected(): void
    {
        $admin = $this->createAdmin();
        [$praktikum, $sesi] = $this->createPraktikumDanSesi();
        [$user, $praktikan] = $this->createPraktikan();
        $pendaftaran = $this->createPendaftaran($praktikan, $praktikum, $sesi);

        // Lembar kuning ditolak (misal bukti transfer buram)
        $response = $this->actingAs($admin)->patch("/administrator/pendaftaran/{$pendaftaran->id}/status", [
            'status_lembar_kuning' => 'rejected',
            'catatan_lembar_kuning' => 'Bukti kwitansi buram',
            'status_berkas' => 'verified',
        ]);
        $response->assertRedirect(route('admin.pendaftaran.index'));

        $p = $pendaftaran->fresh();
        $this->assertEquals('rejected', $p->status_lembar_kuning);
        $this->assertEquals('Bukti kwitansi buram', $p->catatan_lembar_kuning);
        $this->assertEquals('rejected', $p->status, 'Master status wajib rejected jika salah satu verifikasi ditolak');
    }

    public function test_legacy_quick_verify_sets_both_steps_to_verified(): void
    {
        $admin = $this->createAdmin();
        [$praktikum, $sesi] = $this->createPraktikumDanSesi();
        [$user, $praktikan] = $this->createPraktikan();
        $pendaftaran = $this->createPendaftaran($praktikan, $praktikum, $sesi);

        // Panggilan legacy dengan status = verified langsung
        $response = $this->actingAs($admin)->patch("/administrator/pendaftaran/{$pendaftaran->id}/status", [
            'status' => 'verified',
        ]);
        $response->assertRedirect(route('admin.pendaftaran.index'));

        $p = $pendaftaran->fresh();
        $this->assertEquals('verified', $p->status);
        $this->assertEquals('verified', $p->status_lembar_kuning);
        $this->assertEquals('verified', $p->status_berkas);
        $this->assertTrue($p->isFullyVerified());
    }
}
