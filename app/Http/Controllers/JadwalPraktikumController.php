<?php

namespace App\Http\Controllers;

use App\Models\JadwalPraktikum;
use App\Models\PendaftaranPraktikum;
use App\Models\PesertaSusulan;
use App\Models\Praktikum;
use App\Models\SesiPraktikum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JadwalPraktikumController extends Controller
{
    public function index(Request $request)
    {
        $selectedPraktikum = $request->get('praktikum_id');

        $query = JadwalPraktikum::with(['praktikum', 'sesi', 'pesertaSusulans.pendaftaran.praktikan.user'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu_mulai', 'desc');

        if ($selectedPraktikum) {
            $query->where('praktikum_id', $selectedPraktikum);
        }

        $jadwals = $query->get();
        // Eager load sesis dan pendaftarans agar data mahasiswa terverifikasi siap diakses modal susulan
        $praktikums = Praktikum::with([
            'sesis',
            'pendaftarans' => function ($q) {
                $q->where('status', 'verified')->with(['praktikan.user', 'sesi']);
            },
        ])->orderBy('nama_praktikum', 'asc')->get();
        $sesis = SesiPraktikum::orderBy('nama_sesi')->get();

        return view('admin.jadwal_praktikum.index', compact('jadwals', 'praktikums', 'sesis', 'selectedPraktikum'));
    }

    public function store(Request $request)
    {
        // 1. Cek jika request datang dari form multi-sesi
        if ($request->has('sessions') && is_array($request->sessions)) {
            $activeSessions = collect($request->input('sessions', []))->filter(function ($item) {
                return ! empty($item['enabled']) && ! empty($item['sesi_id']);
            })->values()->all();

            if (empty($activeSessions)) {
                return back()->withErrors(['sessions' => 'Pilih dan aktifkan minimal satu sesi untuk dijadwalkan.'])->withInput();
            }

            $request->merge(['active_sessions' => $activeSessions]);

            $request->validate([
                'praktikum_id' => 'required|exists:praktikums,id',
                'judul_modul' => 'required|string|max:255',
                'ruangan' => 'nullable|string|max:255',
                'active_sessions' => 'required|array|min:1',
                'active_sessions.*.sesi_id' => 'required|exists:sesi_praktikums,id',
                'active_sessions.*.tanggal' => 'required|date',
                'active_sessions.*.waktu_mulai' => 'required',
                'active_sessions.*.waktu_selesai' => 'required',
                'active_sessions.*.ruangan' => 'nullable|string|max:255',
            ]);

            foreach ($activeSessions as $s) {
                if ($s['waktu_selesai'] <= $s['waktu_mulai']) {
                    return back()->withErrors(['sessions' => 'Waktu selesai harus lebih besar dari waktu mulai untuk semua sesi yang dipilih.'])->withInput();
                }
            }

            DB::transaction(function () use ($request, $activeSessions) {
                foreach ($activeSessions as $sessionData) {
                    $room = ! empty(trim($sessionData['ruangan'] ?? ''))
                        ? trim($sessionData['ruangan'])
                        : $request->ruangan;

                    JadwalPraktikum::create([
                        'praktikum_id' => $request->praktikum_id,
                        'sesi_id' => $sessionData['sesi_id'],
                        'judul_modul' => $request->judul_modul,
                        'tanggal' => $sessionData['tanggal'],
                        'waktu_mulai' => $sessionData['waktu_mulai'],
                        'waktu_selesai' => $sessionData['waktu_selesai'],
                        'ruangan' => $room,
                        'token' => (string) Str::random(32),
                    ]);
                }
            });

            $count = count($activeSessions);
            $message = "Jadwal praktikum berhasil ditambahkan untuk {$count} sesi.";
        } else {
            // 2. Fallback untuk backward compatibility (single store)
            $isSusulan = $request->boolean('is_susulan');
            $rules = [
                'praktikum_id' => 'required|exists:praktikums,id',
                'judul_modul' => 'required|string|max:255',
                'tanggal' => 'required|date',
                'waktu_mulai' => 'required',
                'waktu_selesai' => 'required',
                'ruangan' => 'nullable|string|max:255',
                'is_susulan' => 'nullable|boolean',
            ];

            if (! $isSusulan) {
                $rules['sesi_id'] = 'required|exists:sesi_praktikums,id';
            } else {
                $rules['sesi_id'] = 'nullable|exists:sesi_praktikums,id';
            }

            $request->validate($rules);

            if ($request->waktu_selesai <= $request->waktu_mulai) {
                return back()->withErrors(['waktu_selesai' => 'Waktu selesai harus setelah waktu mulai.'])->withInput();
            }

            $data = $request->all();
            $data['token'] = (string) Str::random(32);
            $data['is_susulan'] = $isSusulan;
            JadwalPraktikum::create($data);

            $message = 'Jadwal praktikum berhasil ditambahkan.';
        }

        $jTitle = strtolower($request->judul_modul);
        if (str_contains($jTitle, 'tugas akhir') || str_contains($jTitle, 'ta ') || str_contains($jTitle, 'akhir')) {
            $praktikum = Praktikum::find($request->praktikum_id);
            if ($praktikum && ! $praktikum->ada_tugas_akhir) {
                $praktikum->update(['ada_tugas_akhir' => true]);
            }
        }

        return back()->with('success', $message);
    }

    public function update(Request $request, $id)
    {
        $jadwal = JadwalPraktikum::findOrFail($id);

        $request->validate([
            'praktikum_id' => 'required|exists:praktikums,id',
            'sesi_id' => 'required|exists:sesi_praktikums,id',
            'judul_modul' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            'ruangan' => 'nullable|string|max:255',
        ]);

        if ($request->waktu_selesai <= $request->waktu_mulai) {
            return back()->withErrors(['waktu_selesai' => 'Waktu selesai harus setelah waktu mulai.'])->withInput();
        }

        $jadwal->update($request->only([
            'praktikum_id',
            'sesi_id',
            'judul_modul',
            'tanggal',
            'waktu_mulai',
            'waktu_selesai',
            'ruangan',
        ]));

        $jTitle = strtolower($request->judul_modul);
        if (str_contains($jTitle, 'tugas akhir') || str_contains($jTitle, 'ta ') || str_contains($jTitle, 'akhir')) {
            $praktikum = Praktikum::find($request->praktikum_id);
            if ($praktikum && ! $praktikum->ada_tugas_akhir) {
                $praktikum->update(['ada_tugas_akhir' => true]);
            }
        }

        return back()->with('success', 'Jadwal praktikum berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jadwal = JadwalPraktikum::findOrFail($id);
        $jadwal->delete();

        return back()->with('success', 'Jadwal praktikum berhasil dihapus.');
    }

    public function storePesertaSusulan(Request $request, $id)
    {
        $jadwal = JadwalPraktikum::findOrFail($id);

        $request->validate([
            'pendaftaran_ids' => 'required|array|min:1',
            'pendaftaran_ids.*' => [
                'required',
                'exists:pendaftaran_praktikums,id',
                function ($attribute, $value, $fail) use ($jadwal) {
                    $pendaftaran = PendaftaranPraktikum::find($value);
                    if (! $pendaftaran || $pendaftaran->praktikum_id !== $jadwal->praktikum_id) {
                        $fail('Praktikan tidak terdaftar di praktikum ini.');

                        return;
                    }
                    if ($pendaftaran->status === 'gugur') {
                        $fail('Praktikan yang telah dinyatakan gugur tidak dapat didaftarkan ke jadwal susulan.');

                        return;
                    }
                    if ($pendaftaran->status !== 'verified') {
                        $fail('Hanya praktikan terverifikasi yang dapat didaftarkan ke jadwal susulan.');
                    }
                },
            ],
            'catatan' => 'nullable|string|max:500',
        ]);

        foreach ($request->pendaftaran_ids as $pendaftaranId) {
            PesertaSusulan::firstOrCreate(
                [
                    'jadwal_id' => $jadwal->id,
                    'pendaftaran_id' => $pendaftaranId,
                ],
                [
                    'status' => 'terdaftar',
                    'catatan' => $request->catatan,
                ]
            );
        }

        return back()->with('success', count($request->pendaftaran_ids).' praktikan berhasil didaftarkan ke jadwal susulan.');
    }

    public function destroyPesertaSusulan($id, $peserta_id)
    {
        $peserta = PesertaSusulan::where('jadwal_id', $id)->findOrFail($peserta_id);
        $peserta->delete();

        return back()->with('success', 'Praktikan berhasil dihapus dari jadwal susulan.');
    }
}
