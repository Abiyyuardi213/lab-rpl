<?php

namespace App\Services;

use App\Models\JadwalPraktikum;
use App\Models\PendaftaranPraktikum;
use App\Models\Presensi;
use Carbon\Carbon;

class PresensiAlfaService
{
    public function markFinishedSchedules(?int $praktikumId = null): void
    {
        $now = now();

        $query = JadwalPraktikum::query()
            ->whereRaw("CONCAT(tanggal, ' ', waktu_selesai) < ?", [$now->format('Y-m-d H:i:s')]);

        if ($praktikumId) {
            $query->where('praktikum_id', $praktikumId);
        }

        $query->chunkById(50, function ($jadwals) {
            foreach ($jadwals as $jadwal) {
                $this->markSchedule($jadwal);
            }
        });
    }

    public function markSchedule(JadwalPraktikum $jadwal): void
    {
        $finishedAt = Carbon::parse($jadwal->tanggal . ' ' . $jadwal->waktu_selesai);

        if ($finishedAt->greaterThanOrEqualTo(now())) {
            return;
        }

        $presentPendaftaranIds = Presensi::where('jadwal_id', $jadwal->id)
            ->pluck('pendaftaran_id');

        $pendaftaranQuery = PendaftaranPraktikum::where('praktikum_id', $jadwal->praktikum_id)
            ->where('status', 'verified')
            ->whereNotIn('id', $presentPendaftaranIds);

        if ($jadwal->sesi_id) {
            $pendaftaranQuery->where('sesi_id', $jadwal->sesi_id);
        }

        $pendaftaranQuery->chunkById(100, function ($pendaftarans) use ($jadwal, $finishedAt) {
            foreach ($pendaftarans as $pendaftaran) {
                Presensi::create([
                    'jadwal_id' => $jadwal->id,
                    'pendaftaran_id' => $pendaftaran->id,
                    'jam_masuk' => $finishedAt,
                    'status' => 'alfa',
                ]);
            }
        });
    }
}
