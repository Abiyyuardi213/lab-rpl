<?php

namespace App\Http\Controllers;

use App\Models\Praktikum;
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
            'aslab_id' => 'required|exists:aslabs,id',
        ]);

        $pendaftaran = \App\Models\PendaftaranPraktikum::findOrFail($pendaftaran_id);

        // Verify aslab is assigned to this practicum
        $isAssigned = \App\Models\AslabPraktikum::where('aslab_id', $request->aslab_id)
            ->where('praktikum_id', $pendaftaran->praktikum_id)
            ->exists();

        if (!$isAssigned) {
            return back()->with('error', 'Aslab tidak terdaftar pada praktikum ini.');
        }

        $pendaftaran->aslab_id = $request->aslab_id;
        $pendaftaran->save();

        return back()->with('success', 'Aslab berhasil disematkan ke mahasiswa.');
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
}
