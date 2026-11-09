<?php

namespace Tests\Feature;

use App\Http\Controllers\JadwalPraktikumController;
use App\Models\JadwalPraktikum;
use App\Models\Praktikum;
use App\Models\SesiPraktikum;
use Illuminate\Http\Request;
use Tests\TestCase;

class JadwalPraktikumMultiSesiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'lab_rpl',
        ]);
    }

    public function test_multi_sesi_store_creates_records_for_all_enabled_sessions(): void
    {
        $praktikum = Praktikum::first();
        if (! $praktikum) {
            $this->markTestSkipped('Tidak ada data praktikum di database.');
        }

        $sesis = SesiPraktikum::where('praktikum_id', $praktikum->id)->take(2)->get();
        if ($sesis->count() < 2) {
            $this->markTestSkipped('Praktikum membutuhkan minimal 2 sesi untuk pengujian multi-sesi.');
        }

        $controller = new JadwalPraktikumController;
        $uniqueModul = 'Modul Test Multi-Sesi '.uniqid();

        $request = Request::create('/administrator/jadwal-praktikum', 'POST', [
            'praktikum_id' => $praktikum->id,
            'judul_modul' => $uniqueModul,
            'ruangan' => 'Lab Test 101',
            'sessions' => [
                [
                    'enabled' => '1',
                    'sesi_id' => $sesis[0]->id,
                    'tanggal' => date('Y-m-d', strtotime('+1 day')),
                    'waktu_mulai' => '08:00',
                    'waktu_selesai' => '10:00',
                ],
                [
                    'enabled' => '1',
                    'sesi_id' => $sesis[1]->id,
                    'tanggal' => date('Y-m-d', strtotime('+1 day')),
                    'waktu_mulai' => '13:00',
                    'waktu_selesai' => '15:00',
                ],
            ],
        ]);

        $controller->store($request);

        // Verifikasi dua record berhasil dibuat
        $created = JadwalPraktikum::where('judul_modul', $uniqueModul)->get();
        $this->assertCount(2, $created);

        // Verifikasi token unik dan sesi sesuai
        $this->assertNotEquals($created[0]->token, $created[1]->token);
        $this->assertEquals($sesis[0]->id, $created[0]->sesi_id);
        $this->assertEquals($sesis[1]->id, $created[1]->sesi_id);

        // Cleanup
        JadwalPraktikum::where('judul_modul', $uniqueModul)->delete();
    }

    public function test_multi_sesi_store_ignores_disabled_sessions(): void
    {
        $praktikum = Praktikum::first();
        if (! $praktikum) {
            $this->markTestSkipped('Tidak ada data praktikum di database.');
        }

        $sesis = SesiPraktikum::where('praktikum_id', $praktikum->id)->take(2)->get();
        if ($sesis->count() < 2) {
            $this->markTestSkipped('Praktikum membutuhkan minimal 2 sesi untuk pengujian.');
        }

        $controller = new JadwalPraktikumController;
        $uniqueModul = 'Modul Test Partial '.uniqid();

        $request = Request::create('/administrator/jadwal-praktikum', 'POST', [
            'praktikum_id' => $praktikum->id,
            'judul_modul' => $uniqueModul,
            'ruangan' => 'Lab Test 102',
            'sessions' => [
                [
                    'enabled' => '1',
                    'sesi_id' => $sesis[0]->id,
                    'tanggal' => date('Y-m-d', strtotime('+2 days')),
                    'waktu_mulai' => '09:00',
                    'waktu_selesai' => '11:00',
                ],
                [
                    // Tidak enabled
                    'sesi_id' => $sesis[1]->id,
                    'tanggal' => date('Y-m-d', strtotime('+2 days')),
                    'waktu_mulai' => '14:00',
                    'waktu_selesai' => '16:00',
                ],
            ],
        ]);

        $controller->store($request);

        // Hanya 1 record yang tersimpan
        $created = JadwalPraktikum::where('judul_modul', $uniqueModul)->get();
        $this->assertCount(1, $created);
        $this->assertEquals($sesis[0]->id, $created->first()->sesi_id);

        // Cleanup
        JadwalPraktikum::where('judul_modul', $uniqueModul)->delete();
    }

    public function test_single_sesi_store_fallback_still_works(): void
    {
        $praktikum = Praktikum::first();
        if (! $praktikum) {
            $this->markTestSkipped('Tidak ada data praktikum di database.');
        }

        $sesi = SesiPraktikum::where('praktikum_id', $praktikum->id)->first();
        if (! $sesi) {
            $this->markTestSkipped('Praktikum membutuhkan sesi.');
        }

        $controller = new JadwalPraktikumController;
        $uniqueModul = 'Modul Test Single Fallback '.uniqid();

        $request = Request::create('/administrator/jadwal-praktikum', 'POST', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => $uniqueModul,
            'tanggal' => date('Y-m-d', strtotime('+3 days')),
            'waktu_mulai' => '10:00',
            'waktu_selesai' => '12:00',
            'ruangan' => 'Lab Test Single',
        ]);

        $controller->store($request);

        $created = JadwalPraktikum::where('judul_modul', $uniqueModul)->first();
        $this->assertNotNull($created);
        $this->assertEquals($sesi->id, $created->sesi_id);

        // Cleanup
        $created->delete();
    }

    public function test_update_requires_valid_sesi(): void
    {
        $praktikum = Praktikum::first();
        if (! $praktikum) {
            $this->markTestSkipped('Tidak ada data praktikum di database.');
        }

        $sesi = SesiPraktikum::where('praktikum_id', $praktikum->id)->first();
        if (! $sesi) {
            $this->markTestSkipped('Praktikum membutuhkan sesi.');
        }

        $jadwal = JadwalPraktikum::create([
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul Edit Test '.uniqid(),
            'tanggal' => date('Y-m-d'),
            'waktu_mulai' => '07:00',
            'waktu_selesai' => '09:00',
            'ruangan' => 'R101',
            'token' => 'test-token-12345678901234567890',
        ]);

        $controller = new JadwalPraktikumController;

        $updateRequest = Request::create("/administrator/jadwal-praktikum/{$jadwal->id}", 'PATCH', [
            'praktikum_id' => $praktikum->id,
            'sesi_id' => $sesi->id,
            'judul_modul' => 'Modul Updated Successfully',
            'tanggal' => date('Y-m-d', strtotime('+5 days')),
            'waktu_mulai' => '08:30',
            'waktu_selesai' => '10:30',
            'ruangan' => 'R102 Updated',
        ]);

        $controller->update($updateRequest, $jadwal->id);

        $fresh = $jadwal->fresh();
        $this->assertEquals('Modul Updated Successfully', $fresh->judul_modul);
        $this->assertEquals('R102 Updated', $fresh->ruangan);
        $this->assertEquals('08:30', substr($fresh->waktu_mulai, 0, 5));

        // Cleanup
        $jadwal->delete();
    }

    public function test_multi_sesi_store_allows_different_rooms_per_session(): void
    {
        $praktikum = Praktikum::first();
        if (! $praktikum) {
            $this->markTestSkipped('Tidak ada data praktikum di database.');
        }

        $sesis = SesiPraktikum::where('praktikum_id', $praktikum->id)->take(2)->get();
        if ($sesis->count() < 2) {
            $this->markTestSkipped('Praktikum membutuhkan minimal 2 sesi untuk pengujian.');
        }

        $controller = new JadwalPraktikumController;
        $uniqueModul = 'Modul Test Different Rooms '.uniqid();

        $request = Request::create('/administrator/jadwal-praktikum', 'POST', [
            'praktikum_id' => $praktikum->id,
            'judul_modul' => $uniqueModul,
            'ruangan' => 'Lab Global Fallback',
            'sessions' => [
                [
                    'enabled' => '1',
                    'sesi_id' => $sesis[0]->id,
                    'tanggal' => date('Y-m-d', strtotime('+4 days')),
                    'waktu_mulai' => '08:00',
                    'waktu_selesai' => '10:00',
                    'ruangan' => 'Lab Basis Data G2',
                ],
                [
                    'enabled' => '1',
                    'sesi_id' => $sesis[1]->id,
                    'tanggal' => date('Y-m-d', strtotime('+4 days')),
                    'waktu_mulai' => '13:00',
                    'waktu_selesai' => '15:00',
                    'ruangan' => 'Lab RPL G3',
                ],
            ],
        ]);

        $controller->store($request);

        $schedules = JadwalPraktikum::where('judul_modul', $uniqueModul)->orderBy('waktu_mulai')->get();
        $this->assertCount(2, $schedules);
        $this->assertEquals('Lab Basis Data G2', $schedules[0]->ruangan);
        $this->assertEquals('Lab RPL G3', $schedules[1]->ruangan);

        // Cleanup
        JadwalPraktikum::where('judul_modul', $uniqueModul)->delete();
    }
}
