<?php

namespace App\Services;

use App\Models\PendaftaranPraktikum;
use App\Models\Penugasan;
use App\Models\PenugasanPraktikanOverride;
use App\Models\Praktikan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PenugasanDistributionService
{
    /**
     * Menyelesaikan daftar penugasan unik per modul yang tepat untuk seorang praktikan.
     * Mengunci soal pool random secara permanen ke penugasan_praktikan_overrides bila belum terkunci.
     *
     * @param  Collection<int, PendaftaranPraktikum>  $pendaftarans
     * @return Collection<int, Penugasan>
     */
    public function resolvePenugasansForStudent(Praktikan $praktikan, Collection $pendaftarans): Collection
    {
        $npm = (string) ($praktikan->npm ?? '');
        $lastChar = substr($npm, -1);
        $lastDigit = is_numeric($lastChar) ? (string) $lastChar : null;

        $praktikumIds = $pendaftarans->pluck('praktikum_id')->unique();
        $allPenugasans = Penugasan::with(['praktikum', 'sesi.jadwalPraktikums', 'aslab.user', 'jadwalPraktikum'])
            ->whereIn('praktikum_id', $praktikumIds)
            ->get();

        // Filter penugasan yang sesinya sesuai dengan pendaftaran mahasiswa
        $matchedPenugasans = $allPenugasans->filter(function ($penugasan) use ($pendaftarans) {
            return $pendaftarans->contains(function ($p) use ($penugasan) {
                return $p->praktikum_id === $penugasan->praktikum_id
                    && (! $penugasan->sesi_id || $p->sesi_id === $penugasan->sesi_id);
            });
        });

        // Kelompokkan penugasan per unit modul: praktikum_id + sesi_id + jadwal_praktikum_id
        $grouped = $matchedPenugasans->groupBy(function ($item) {
            return $item->praktikum_id.'_'.($item->sesi_id ?? 'all').'_'.($item->jadwal_praktikum_id ?? 'no_jadwal');
        });

        $resolved = collect();

        foreach ($grouped as $groupKey => $groupPenugasans) {
            $first = $groupPenugasans->first();
            $pendaftaran = $pendaftarans->first(function ($p) use ($first) {
                return $p->praktikum_id === $first->praktikum_id
                    && (! $first->sesi_id || $p->sesi_id === $first->sesi_id);
            });

            if (! $pendaftaran) {
                continue;
            }

            // 1. Prioritas Override / Kunci Permanen Database
            $override = PenugasanPraktikanOverride::where('pendaftaran_id', $pendaftaran->id)
                ->where('jadwal_praktikum_id', $first->jadwal_praktikum_id)
                ->first();

            if ($override) {
                $lockedPenugasan = $groupPenugasans->firstWhere('id', $override->penugasan_id)
                    ?: Penugasan::with(['praktikum', 'sesi.jadwalPraktikums', 'aslab.user', 'jadwalPraktikum'])->find($override->penugasan_id);

                if ($lockedPenugasan) {
                    $resolved->push($lockedPenugasan);

                    continue;
                }
            }

            // 2. Prioritas By Sesi (Universal satu sesi)
            $bySesi = $groupPenugasans->first(function ($p) {
                return $p->tipe_distribusi === 'by_sesi' || (string) $p->kode_akhir_npm === '*';
            });

            if ($bySesi) {
                $resolved->push($bySesi);

                continue;
            }

            // 3. Prioritas By Random Pool (Acak Terkunci)
            $randomPool = $groupPenugasans->filter(function ($p) {
                return $p->tipe_distribusi === 'by_random';
            });

            if ($randomPool->isNotEmpty()) {
                // Pilih 1 acak dari pool dan kunci permanen secara atomik
                $selected = $randomPool->random();

                $lockedOverride = DB::transaction(function () use ($pendaftaran, $first, $selected) {
                    return PenugasanPraktikanOverride::firstOrCreate(
                        [
                            'pendaftaran_id' => $pendaftaran->id,
                            'jadwal_praktikum_id' => $first->jadwal_praktikum_id,
                        ],
                        [
                            'penugasan_id' => $selected->id,
                        ]
                    );
                });

                $lockedPenugasan = $groupPenugasans->firstWhere('id', $lockedOverride->penugasan_id)
                    ?: Penugasan::with(['praktikum', 'sesi.jadwalPraktikums', 'aslab.user', 'jadwalPraktikum'])->find($lockedOverride->penugasan_id);

                if ($lockedPenugasan) {
                    $resolved->push($lockedPenugasan);

                    continue;
                }
            }

            // 4. Prioritas Fallback: By NPM (Deterministik digit)
            if ($lastDigit !== null) {
                $byNpm = $groupPenugasans->first(function ($p) use ($lastDigit) {
                    return (string) $p->kode_akhir_npm === $lastDigit;
                });

                if ($byNpm) {
                    $resolved->push($byNpm);
                }
            }
        }

        return $resolved;
    }

    /**
     * Memeriksa apakah mahasiswa memiliki hak akses ke suatu file penugasan.
     */
    public function canStudentAccessPenugasan(Penugasan $penugasan, Praktikan $praktikan, PendaftaranPraktikum $pendaftaran): bool
    {
        // Guard praktikum dan sesi
        if ($pendaftaran->praktikum_id !== $penugasan->praktikum_id) {
            return false;
        }

        if ($penugasan->sesi_id && $pendaftaran->sesi_id !== $penugasan->sesi_id) {
            return false;
        }

        $npm = (string) ($praktikan->npm ?? '');
        $lastChar = substr($npm, -1);
        $lastDigit = is_numeric($lastChar) ? (string) $lastChar : null;
        $kodeSoal = (string) $penugasan->kode_akhir_npm;

        // Cek apakah ada override / kunci permanen
        $override = PenugasanPraktikanOverride::where('pendaftaran_id', $pendaftaran->id)
            ->where('jadwal_praktikum_id', $penugasan->jadwal_praktikum_id)
            ->first();

        if ($override) {
            return $override->penugasan_id === $penugasan->id;
        }

        // Jika mode By Sesi
        if ($penugasan->tipe_distribusi === 'by_sesi' || $kodeSoal === '*') {
            return true;
        }

        // Jika mode By Random tapi belum ada override:
        if ($penugasan->tipe_distribusi === 'by_random') {
            // Kunci mahasiswa ke soal ini jika belum ada override
            PenugasanPraktikanOverride::firstOrCreate(
                [
                    'pendaftaran_id' => $pendaftaran->id,
                    'jadwal_praktikum_id' => $penugasan->jadwal_praktikum_id,
                ],
                [
                    'penugasan_id' => $penugasan->id,
                ]
            );

            return true;
        }

        // Jika mode By NPM
        return $lastDigit !== null && $kodeSoal === $lastDigit;
    }
}
