<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Praktikum;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DosenController extends Controller
{
    public function index()
    {
        $dosens = Dosen::orderBy('nama')->get();
        return view('admin.dosen.index', compact('dosens'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50|unique:dosens,nip',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        Dosen::create($data);

        return redirect()->route('admin.dosen.index')->with('success', 'Dosen berhasil ditambahkan.');
    }

    public function update(Request $request, Dosen $dosen)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('dosens', 'nip')->ignore($dosen->id),
            ],
            'is_active' => 'nullable|boolean',
        ]);

        // If nama is updated, check if the old name is used in any Praktikum, and if so, update those too (optional but nice)
        $oldNama = $dosen->nama;
        $data['is_active'] = $request->boolean('is_active');

        $dosen->update($data);

        if ($oldNama !== $data['nama']) {
            $praktikums = Praktikum::whereJsonContains('daftar_dosen', $oldNama)->get();
            foreach ($praktikums as $p) {
                $list = $p->daftar_dosen ?? [];
                if (($key = array_search($oldNama, $list)) !== false) {
                    $list[$key] = $data['nama'];
                    $p->update(['daftar_dosen' => array_values($list)]);
                }
            }
        }

        return redirect()->route('admin.dosen.index')->with('success', 'Data Dosen berhasil diperbarui.');
    }

    public function destroy(Dosen $dosen)
    {
        if (Praktikum::whereJsonContains('daftar_dosen', $dosen->nama)->exists()) {
            return redirect()->route('admin.dosen.index')
                ->with('error', 'Dosen tidak dapat dihapus karena sudah dipakai pada praktikum.');
        }

        $dosen->delete();

        return redirect()->route('admin.dosen.index')->with('success', 'Dosen berhasil dihapus.');
    }

    public function toggleStatus(Dosen $dosen)
    {
        $dosen->update([
            'is_active' => !$dosen->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status dosen berhasil diperbarui.',
            'is_active' => $dosen->is_active,
        ]);
    }
}
