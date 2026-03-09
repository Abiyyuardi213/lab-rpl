<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PengumumanController extends Controller
{
    public function index()
    {
        $pengumumans = Pengumuman::with('user')->latest()->get();
        return view('admin.pengumuman.index', compact('pengumumans'));
    }

    public function create()
    {
        return view('admin.pengumuman.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required',
            'kategori' => 'required|in:umum,praktikum,kegiatan',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('pengumuman', 'public');
        }

        Pengumuman::create($data);

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function edit(Pengumuman $pengumuman)
    {
        return view('admin.pengumuman.edit', compact('pengumuman'));
    }

    public function update(Request $request, Pengumuman $pengumuman)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required',
            'kategori' => 'required|in:umum,praktikum,kegiatan',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('gambar')) {
            if ($pengumuman->gambar) {
                Storage::disk('public')->delete($pengumuman->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('pengumuman', 'public');
        }

        if ($pengumuman->judul !== $request->judul) {
            $data['slug'] = Str::slug($request->judul);
        }

        $pengumuman->update($data);

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Pengumuman $pengumuman)
    {
        if ($pengumuman->gambar) {
            Storage::disk('public')->delete($pengumuman->gambar);
        }
        $pengumuman->delete();

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil dihapus.');
    }

    public function toggleStatus(Pengumuman $pengumuman)
    {
        $pengumuman->update(['is_active' => !$pengumuman->is_active]);
        return response()->json(['success' => true]);
    }
}
