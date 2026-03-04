<?php

namespace App\Http\Controllers;

use App\Models\Praktikum;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PraktikumController extends Controller
{
    public function index()
    {
        $praktikums = Praktikum::orderBy('created_at', 'desc')->get();
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
        ]);

        return redirect()->route('admin.praktikum.index')->with('success', 'Praktikum berhasil ditambahkan.');
    }

    public function show($id)
    {
        $praktikum = Praktikum::findOrFail($id);
        return view('admin.praktikum.show', compact('praktikum'));
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
        ]);

        $praktikum->update($request->all());

        return redirect()->route('admin.praktikum.index')->with('success', 'Praktikum berhasil diperbarui.');
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
}
