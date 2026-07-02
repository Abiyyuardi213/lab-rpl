<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Praktikum;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();
        return view('admin.kelas.index', compact('kelas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_kelas' => 'required|string|max:100|unique:kelas,nama_kelas',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        Kelas::create($data);

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function update(Request $request, Kelas $kelas)
    {
        $data = $request->validate([
            'nama_kelas' => [
                'required',
                'string',
                'max:100',
                Rule::unique('kelas', 'nama_kelas')->ignore($kelas->id),
            ],
            'is_active' => 'nullable|boolean',
        ]);

        $oldNama = $kelas->nama_kelas;
        $data['is_active'] = $request->boolean('is_active');

        $kelas->update($data);

        if ($oldNama !== $data['nama_kelas']) {
            $praktikums = Praktikum::whereJsonContains('daftar_kelas_mk', $oldNama)->get();
            foreach ($praktikums as $p) {
                $list = $p->daftar_kelas_mk ?? [];
                if (($key = array_search($oldNama, $list)) !== false) {
                    $list[$key] = $data['nama_kelas'];
                    $p->update(['daftar_kelas_mk' => array_values($list)]);
                }
            }
        }

        return redirect()->route('admin.kelas.index')->with('success', 'Data Kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas)
    {
        if (Praktikum::whereJsonContains('daftar_kelas_mk', $kelas->nama_kelas)->exists()) {
            return redirect()->route('admin.kelas.index')
                ->with('error', 'Kelas tidak dapat dihapus karena sudah dipakai pada praktikum.');
        }

        $kelas->delete();

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil dihapus.');
    }

    public function toggleStatus(Kelas $kelas)
    {
        $kelas->update([
            'is_active' => !$kelas->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status kelas berhasil diperbarui.',
            'is_active' => $kelas->is_active,
        ]);
    }
}
