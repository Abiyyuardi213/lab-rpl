<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\JadwalPraktikum;
use App\Models\PendaftaranPraktikum;
use App\Models\Presensi;
use App\Traits\HasActivityLog;
use Carbon\Carbon;

class PresensiAlfaService
{
    use HasActivityLog;

    public function markFinishedSchedules(?int $praktikumId = null, string|int|null $praktikanId = null): void
    {
        $now = now();

        $query = JadwalPraktikum::query()
            ->where('is_susulan', false)
            ->whereRaw("CONCAT(tanggal, ' ', waktu_selesai) < ?", [$now->format('Y-m-d H:i:s')]);

        if ($praktikumId) {
            $query->where('praktikum_id', $praktikumId);
        }

        $query->chunkById(50, function ($jadwals) use ($praktikanId) {
            foreach ($jadwals as $jadwal) {
                $this->markSchedule($jadwal, $praktikanId);
            }
        });
    }

    public function markSchedule(JadwalPraktikum $jadwal, string|int|null $praktikanId = null): void
    {
        if ($jadwal->is_susulan) {
            return;
        }

        $finishedAt = Carbon::parse($jadwal->tanggal.' '.$jadwal->waktu_selesai);

        if ($finishedAt->greaterThanOrEqualTo(now())) {
            return;
        }

        $presentPendaftaranIds = Presensi::where('jadwal_id', $jadwal->id)
            ->orWhere(function ($query) use ($jadwal) {
                $query->whereHas('jadwal', function ($q) use ($jadwal) {
                    $q->where('praktikum_id', $jadwal->praktikum_id)
                        ->where('judul_modul', $jadwal->judul_modul);
                });
            })
            ->pluck('pendaftaran_id');

        $pendaftaranQuery = PendaftaranPraktikum::where('praktikum_id', $jadwal->praktikum_id)
            ->where('status', 'verified')
            ->whereNotIn('id', $presentPendaftaranIds);

        if ($praktikanId) {
            $pendaftaranQuery->where('praktikan_id', $praktikanId);
        }

        if ($jadwal->sesi_id) {
            $pendaftaranQuery->where('sesi_id', $jadwal->sesi_id);
        }

        $pendaftaranQuery->chunkById(100, function ($pendaftarans) use ($jadwal, $finishedAt) {
            foreach ($pendaftarans as $pendaftaran) {
                Presensi::firstOrCreate(
                    [
                        'jadwal_id' => $jadwal->id,
                        'pendaftaran_id' => $pendaftaran->id,
                    ],
                    [
                        'jam_masuk' => $finishedAt,
                        'status' => 'alfa',
                    ]
                );

                $this->evaluateDropOutCondition($pendaftaran, $jadwal);
            }
        });
    }

    public function evaluateDropOutCondition(PendaftaranPraktikum $pendaftaran, JadwalPraktikum $currentJadwal): void
    {
        if ($pendaftaran->status === 'gugur') {
            return;
        }

        $lastReactivatedAt = ActivityLog::where('activity', 'Pulihkan Praktikan')
            ->where('data->pendaftaran_id', $pendaftaran->id)
            ->latest('id')
            ->first()?->created_at;

        $finishedRegularSchedulesQuery = JadwalPraktikum::where('praktikum_id', $pendaftaran->praktikum_id)
            ->where('is_susulan', false)
            ->where(function ($q) use ($pendaftaran) {
                $q->where('sesi_id', $pendaftaran->sesi_id)
                    ->orWhereNull('sesi_id');
            })
            ->whereRaw("CONCAT(tanggal, ' ', waktu_selesai) <= ?", [
                $currentJadwal->tanggal.' '.$currentJadwal->waktu_selesai,
            ]);

        if ($lastReactivatedAt) {
            // Reset sejarah alfa lama sebelum Admin memulihkan praktikan agar tidak langsung gugur ulang
            $finishedRegularSchedulesQuery->whereRaw(
                "CONCAT(tanggal, ' ', waktu_selesai) > ?",
                [$lastReactivatedAt->format('Y-m-d H:i:s')]
            );
        }

        $finishedRegularSchedules = $finishedRegularSchedulesQuery
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu_mulai', 'asc')
            ->get();

        $consecutiveAlfa = 0;

        foreach ($finishedRegularSchedules as $schedule) {
            $isResolved = Presensi::where('pendaftaran_id', $pendaftaran->id)
                ->where(function ($query) use ($schedule) {
                    $query->where('jadwal_id', $schedule->id)
                        ->orWhereHas('jadwal', function ($q) use ($schedule) {
                            $q->where('praktikum_id', $schedule->praktikum_id)
                                ->where('judul_modul', $schedule->judul_modul);
                        });
                })
                ->whereIn('status', ['hadir', 'terlambat'])
                ->exists();

            if ($isResolved) {
                $consecutiveAlfa = 0;
            } else {
                $consecutiveAlfa++;
                if ($consecutiveAlfa >= 2) {
                    $pendaftaran->update(['status' => 'gugur']);
                    $this->logActivity(
                        'Praktikan Gugur',
                        'Praktikan dinyatakan gugur karena tidak hadir 2 pertemuan berturut-turut pada praktikum ini.',
                        ['pendaftaran_id' => $pendaftaran->id, 'praktikum_id' => $pendaftaran->praktikum_id]
                    );
                    break;
                }
            }
        }
    }
}
