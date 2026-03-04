@extends('layouts.admin')

@section('title', 'Tambah Praktikum')

@section('content')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Tambah Praktikum</h1>
                <p class="text-sm text-zinc-500 mt-1">Buat data praktikum baru untuk periode ini.</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                <span>/</span>
                <a href="{{ route('admin.praktikum.index') }}" class="hover:text-zinc-900 transition-colors">Praktikum</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Tambah</span>
            </div>
        </div>

        <div class="max-w-2xl">
            <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-zinc-100 bg-zinc-50/50">
                    <h3 class="font-bold text-zinc-900">Form Informasi Praktikum</h3>
                </div>
                <form action="{{ route('admin.praktikum.store') }}" method="POST" class="p-6 space-y-6">
                    @csrf

                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-zinc-700 uppercase tracking-tight">Nama Praktikum</label>
                            <input type="text" name="nama_praktikum" required
                                placeholder="e.g., Pemrograman Berorientasi Objek"
                                class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none @error('nama_praktikum') border-rose-500 @enderror">
                            @error('nama_praktikum')
                                <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-zinc-700 uppercase tracking-tight">Periode
                                    Praktikum</label>
                                <input type="text" name="periode_praktikum" required placeholder="e.g., Ganjil 2024/2025"
                                    class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none @error('periode_praktikum') border-rose-500 @enderror">
                                @error('periode_praktikum')
                                    <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-zinc-700 uppercase tracking-tight">Kuota
                                    Praktikan</label>
                                <input type="number" name="kuota_praktikan" required min="1" placeholder="Max orang"
                                    class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none @error('kuota_praktikan') border-rose-500 @enderror">
                                @error('kuota_praktikan')
                                    <p class="text-xs text-rose-500 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold text-zinc-700 uppercase tracking-tight">Status Awal</label>
                            <select name="status_praktikum" required
                                class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none">
                                <option value="open_registration">Buka Pendaftaran</option>
                                <option value="on_progress">Berlangsung</option>
                                <option value="finished">Berakhir</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-zinc-100 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.praktikum.index') }}"
                            class="px-4 py-2.5 rounded-lg border border-zinc-200 text-sm font-bold text-zinc-600 hover:bg-zinc-50 transition-colors active:scale-95">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-6 py-2.5 rounded-lg bg-[#001f3f] border border-[#001f3f] text-sm font-bold text-white hover:bg-[#002d5a] transition-all shadow-lg shadow-[#001f3f]/10 active:scale-95">
                            Simpan Praktikum
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
