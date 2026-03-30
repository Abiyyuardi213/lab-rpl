<?php

namespace App\Http\Controllers;

use App\Models\Praktikum;
use App\Models\TugasAsistensi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PraktikumController extends Controller
{
    public function index()
    {
        $praktikums = Praktikum::orderBy('created_at', 'asc')->get();
        return view('admin.praktikum.index', compact('praktikums'));
    }

    public function create()
    {
        return view('admin.praktikum.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_praktikum' => 'required|string|max:255',
            'periode_praktikum' => 'required|string|max:255',
            'kuota_praktikan' => 'required|integer|min:1',
            'status_praktikum' => 'required|in:open_registration,on_progress,finished',
            'daftar_dosen' => 'required|array|min:1',
            'daftar_dosen.*' => 'required|string|max:255',
            'daftar_kelas_mk' => 'required|array|min:1',
            'daftar_kelas_mk.*' => 'required|string|max:255',
            'jumlah_modul' => 'required|integer|min:0',
            'ada_tugas_akhir' => 'required|boolean',
        ]);

        $kode = 'PRK-' . strtoupper(Str::random(6));
        while (Praktikum::where('kode_praktikum', $kode)->exists()) {
            $kode = 'PRK-' . strtoupper(Str::random(6));
        }

        Praktikum::create([
            'kode_praktikum' => $kode,
            'nama_praktikum' => $request->nama_praktikum,
            'periode_praktikum' => $request->periode_praktikum,
            'kuota_praktikan' => $request->kuota_praktikan,
            'status_praktikum' => $request->status_praktikum,
            'daftar_dosen' => $request->daftar_dosen,
            'daftar_kelas_mk' => $request->daftar_kelas_mk,
            'jumlah_modul' => $request->jumlah_modul,
            'ada_tugas_akhir' => $request->ada_tugas_akhir,
        ]);

        return redirect()->route('admin.praktikum.index', ['last_page' => '1'])->with('success', 'Praktikum berhasil ditambahkan.');
    }

    public function show($id)
    {
        $praktikum = Praktikum::with([
            'sesis' => function ($q) {
                $q->withCount('pendaftarans');
            },
            'jadwals' => function ($q) {
                $q->orderBy('tanggal', 'asc')->orderBy('waktu_mulai', 'asc');
            },
            'aslabs',
            'pendaftarans.praktikan.user',
            'pendaftarans.sesi',
            'pendaftarans.aslab'
        ])->findOrFail($id);

        $aslabRole = \App\Models\Role::where('name', 'Aslab')->first();
        $allAslabs = $aslabRole ? \App\Models\User::where('role_id', $aslabRole->id)->get() : collect();

        // Calculate available modules for scheduling
        $scheduledModules = $praktikum->jadwals->pluck('judul_modul')->toArray();
        $availableModules = [];
        for ($i = 1; $i <= $praktikum->jumlah_modul; $i++) {
            $availableModules[] = "Modul $i";
        }
        if ($praktikum->ada_tugas_akhir) {
            $availableModules[] = "Tugas Akhir";
        }

        return view('admin.praktikum.show', compact('praktikum', 'allAslabs', 'availableModules', 'scheduledModules'));
    }

    public function students($id)
    {
        $praktikum = Praktikum::with([
            'aslabs',
            'sesis',
            'pendaftarans.praktikan.user',
            'pendaftarans.sesi',
            'pendaftarans.aslab'
        ])->findOrFail($id);

        return view('admin.praktikum.students', compact('praktikum'));
    }

    public function edit($id)
    {
        $praktikum = Praktikum::findOrFail($id);
        return view('admin.praktikum.edit', compact('praktikum'));
    }

    public function update(Request $request, $id)
    {
        $praktikum = Praktikum::findOrFail($id);

        $request->validate([
            'nama_praktikum' => 'required|string|max:255',
            'periode_praktikum' => 'required|string|max:255',
            'kuota_praktikan' => 'required|integer|min:1',
            'status_praktikum' => 'required|in:open_registration,on_progress,finished',
            'daftar_dosen' => 'required|array|min:1',
            'daftar_dosen.*' => 'required|string|max:255',
            'daftar_kelas_mk' => 'required|array|min:1',
            'daftar_kelas_mk.*' => 'required|string|max:255',
            'jumlah_modul' => 'required|integer|min:0',
            'ada_tugas_akhir' => 'required|boolean',
        ]);

        $praktikum->update($request->all());

        return redirect()->route('admin.praktikum.index', ['last_page' => '1'])->with('success', 'Praktikum berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $praktikum = Praktikum::findOrFail($id);
        $praktikum->delete();

        return redirect()->route('admin.praktikum.index')->with('success', 'Praktikum berhasil dihapus.');
    }

    public function toggleStatus(Request $request, $id)
    {
        $praktikum = Praktikum::findOrFail($id);
        $praktikum->status_praktikum = $request->status;
        $praktikum->save();

        return response()->json([
            'success' => true,
            'message' => 'Status praktikum berhasil diperbarui.'
        ]);
    }
    public function storeAslab(Request $request, $praktikum_id)
    {
        $request->validate([
            'aslab_id' => 'required|exists:users,id',
            'kuota' => 'required|integer|min:1',
        ]);

        $praktikum = Praktikum::findOrFail($praktikum_id);
        $user = \App\Models\User::with('aslab')->findOrFail($request->aslab_id);

        if (!$user->aslab) {
            return back()->with('error', 'User ini bukan Asisten Laboratorium yang valid.');
        }

        $aslab_model_id = $user->aslab->id;

        if ($praktikum->aslabs()->where('aslab_id', $aslab_model_id)->exists()) {
            return back()->with('error', 'Aslab ini sudah ditugaskan pada praktikum ini.');
        }

        $praktikum->aslabs()->attach($aslab_model_id, [
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'kuota' => $request->kuota
        ]);

        return back()->with('success', 'Aslab berhasil ditugaskan.');
    }

    public function destroyAslab($id)
    {
        $pivot = \App\Models\AslabPraktikum::findOrFail($id);
        $pivot->delete();

        return back()->with('success', 'Penugasan aslab berhasil dihapus.');
    }

    public function assignStudentToAslab(Request $request, $pendaftaran_id)
    {
        $request->validate([
            'aslab_id' => 'nullable|exists:aslabs,id',
        ]);

        $pendaftaran = \App\Models\PendaftaranPraktikum::findOrFail($pendaftaran_id);

        if ($request->aslab_id) {
            // Verify aslab is assigned to this praktikum
            $aslabPivot = \App\Models\AslabPraktikum::where('aslab_id', $request->aslab_id)
                ->where('praktikum_id', $pendaftaran->praktikum_id)
                ->first();

            if (!$aslabPivot) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Aslab tidak terdaftar pada praktikum ini.'], 400);
                }
                return back()->with('error', 'Aslab tidak terdaftar pada praktikum ini.');
            }

            // Verify quota if changing to a new aslab
            if ($pendaftaran->aslab_id != $request->aslab_id) {
                $currentStudentsCount = \App\Models\PendaftaranPraktikum::where('praktikum_id', $pendaftaran->praktikum_id)
                    ->where('aslab_id', $request->aslab_id)
                    ->count();

                if ($currentStudentsCount >= $aslabPivot->kuota) {
                    if ($request->wantsJson()) {
                        return response()->json(['success' => false, 'message' => 'Aslab tujuan sudah penuh.'], 400);
                    }
                    return back()->with('error', 'Aslab tujuan sudah penuh.');
                }
            }
        }

        // Jika aslab berubah (unassign atau ganti ke aslab lain), hapus tugas
        // yang belum ada submission mahasiswanya agar tidak orphaned.
        $oldAslabId = $pendaftaran->aslab_id;
        $newAslabId = $request->aslab_id; // null = unassign, atau ID aslab baru

        if ($oldAslabId !== null && $oldAslabId !== $newAslabId) {
            TugasAsistensi::where('pendaftaran_id', $pendaftaran->id)
                ->where('aslab_id', $oldAslabId)
                ->whereNull('file_mahasiswa') // Jaga tugas yang sudah ada file mahasiswa
                ->delete();
        }

        $pendaftaran->aslab_id = $newAslabId;
        $pendaftaran->save();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Status bimbingan praktikan berhasil diperbarui.']);
        }
        return back()->with('success', 'Status bimbingan praktikan berhasil diperbarui.');
    }

    public function changeStudentSession(Request $request, $pendaftaran_id)
    {
        $request->validate([
            'sesi_id' => 'required|exists:sesi_praktikums,id',
        ]);

        $pendaftaran = \App\Models\PendaftaranPraktikum::findOrFail($pendaftaran_id);
        $newSesi = \App\Models\SesiPraktikum::findOrFail($request->sesi_id);

        // Verify session belongs to the same practicum
        if ($newSesi->praktikum_id != $pendaftaran->praktikum_id) {
            return back()->with('error', 'Sesi tidak valid untuk praktikum ini.');
        }

        // Check if new session is full
        $currentPendaftarCount = \App\Models\PendaftaranPraktikum::where('sesi_id', $newSesi->id)->count();
        if ($currentPendaftarCount >= $newSesi->kuota) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Sesi tujuan sudah penuh (Kuota: ' . $newSesi->kuota . ').'], 400);
            }
            return back()->with('error', 'Sesi tujuan sudah penuh (Kuota: ' . $newSesi->kuota . ').');
        }

        $pendaftaran->sesi_id = $request->sesi_id;
        $pendaftaran->save();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Sesi mahasiswa berhasil dipindahkan.']);
        }
        return back()->with('success', 'Sesi mahasiswa berhasil dipindahkan.');
    }

    public function storeJadwal(Request $request, $praktikum_id)
    {
        $request->validate([
            'judul_modul' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required|after:waktu_mulai',
            'ruangan' => 'nullable|string|max:255',
        ]);

        $praktikum = Praktikum::findOrFail($praktikum_id);

        $praktikum->jadwals()->create([
            'judul_modul' => $request->judul_modul,
            'tanggal' => $request->tanggal,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'ruangan' => $request->ruangan,
        ]);

        return back()->with('success', 'Jadwal Modul Praktikum berhasil ditambahkan.');
    }

    public function destroyJadwal($id)
    {
        $jadwal = \App\Models\JadwalPraktikum::findOrFail($id);
        $jadwal->delete();

        return back()->with('success', 'Jadwal Modul Praktikum berhasil dihapus.');
    }

    public function autoAssignAslab(Request $request, $praktikum_id)
    {
        $praktikum = Praktikum::with(['aslabs' => function ($q) use ($praktikum_id) {
            // Load assigned students count manually or just get relation
            // We will count dynamically
        }])->findOrFail($praktikum_id);

        $unassignedStudents = \App\Models\PendaftaranPraktikum::where('praktikum_id', $praktikum_id)
            ->whereNull('aslab_id')
            ->get();

        if ($unassignedStudents->isEmpty()) {
            return back()->with('info', 'Semua praktikan sudah memiliki Aslab bimbingan.');
        }

        $aslabs = $praktikum->aslabs;
        if ($aslabs->isEmpty()) {
            return back()->with('error', 'Belum ada Aslab yang ditugaskan di Praktikum ini.');
        }

        $assignedCount = 0;

        foreach ($unassignedStudents as $student) {
            // Find aslab with available quota and minimum current students
            $selectedAslab = null;
            $minStudents = PHP_INT_MAX;

            foreach ($aslabs as $aslab) {
                // Get current assigned students for this aslab in this praktikum
                $currentStudentsCount = \App\Models\PendaftaranPraktikum::where('praktikum_id', $praktikum_id)
                    ->where('aslab_id', $aslab->id)
                    ->count();

                $maxKuota = $aslab->pivot->kuota;

                if ($currentStudentsCount < $maxKuota) {
                    if ($currentStudentsCount < $minStudents) {
                        $minStudents = $currentStudentsCount;
                        $selectedAslab = $aslab;
                    }
                }
            }

            if ($selectedAslab) {
                $student->aslab_id = $selectedAslab->id;
                $student->save();
                $assignedCount++;
            }
        }

        if ($assignedCount > 0) {
            return back()->with('success', "Berhasil mendistribusikan $assignedCount praktikan ke Aslab.");
        } else {
            return back()->with('error', 'Gagal mendistribusikan. Semua Aslab mungkin sudah penuh kuotanya.');
        }
    }

    public function bulkAssignAslab(Request $request, $praktikum_id)
    {
        $request->validate([
            'pendaftaran_ids' => 'required|array|min:1',
            'pendaftaran_ids.*' => 'exists:pendaftaran_praktikums,id',
            'aslab_id' => 'required|exists:aslabs,id',
        ]);

        $praktikum = Praktikum::findOrFail($praktikum_id);
        
        // Cek Aslab terhubung dengan Praktikum ini
        $aslabPivot = \App\Models\AslabPraktikum::where('aslab_id', $request->aslab_id)
            ->where('praktikum_id', $praktikum_id)
            ->first();

        if (!$aslabPivot) {
            return response()->json(['success' => false, 'message' => 'Aslab tidak terdaftar pada praktikum ini.'], 400);
        }

        $pendaftaran_ids = $request->pendaftaran_ids;
        $countRequested = count($pendaftaran_ids);

        // Cek kuota Aslab
        $currentStudentsCount = \App\Models\PendaftaranPraktikum::where('praktikum_id', $praktikum_id)
            ->where('aslab_id', $request->aslab_id)
            ->count();
            
        // Kami membiarkan mahasiswa yang SUDAH dibimbing oleh aslab tersebut untuk diabaikan kuotanya
        // karena me-reassign ke aslab yang sama tidak membuang/menambah kuota nyata.
        $alreadyAssignedToThisAslabCount = \App\Models\PendaftaranPraktikum::whereIn('id', $pendaftaran_ids)
            ->where('aslab_id', $request->aslab_id)
            ->count();
            
        $netIncrease = $countRequested - $alreadyAssignedToThisAslabCount;
        
        if ($netIncrease === 0) {
            return response()->json(['success' => true, 'message' => "Semua praktikan yang dipilih sudah berada di Aslab tujuan ini."]);
        }

        if ($currentStudentsCount + $netIncrease > $aslabPivot->kuota) {
            return response()->json([
                'success' => false, 
                'message' => 'Kuota Aslab tidak mencukupi untuk menerima jumlah mahasiswa ini. (Sisa Kuota: ' . ($aslabPivot->kuota - $currentStudentsCount) . ')'
            ], 400);
        }

        // Hapus tugas dari aslab lama untuk mahasiswa yang dipindah ke aslab berbeda
        // (hanya yang belum ada file submission mahasiswanya)
        $pendaftaranYangBerpindah = \App\Models\PendaftaranPraktikum::whereIn('id', $pendaftaran_ids)
            ->where('praktikum_id', $praktikum_id)
            ->whereNotNull('aslab_id')
            ->where('aslab_id', '!=', $request->aslab_id)
            ->pluck('aslab_id', 'id'); // [pendaftaran_id => aslab_id_lama]

        foreach ($pendaftaranYangBerpindah as $pendaftaranId => $oldAslabId) {
            TugasAsistensi::where('pendaftaran_id', $pendaftaranId)
                ->where('aslab_id', $oldAslabId)
                ->whereNull('file_mahasiswa')
                ->delete();
        }

        \App\Models\PendaftaranPraktikum::whereIn('id', $pendaftaran_ids)
            ->where('praktikum_id', $praktikum_id)
            ->update(['aslab_id' => $request->aslab_id]);

        return response()->json(['success' => true, 'message' => "Berhasil memindahkan $netIncrease praktikan ke Aslab ini."]);
    }
}
