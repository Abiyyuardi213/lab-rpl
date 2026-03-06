@extends('layouts.admin')

@section('title', 'Edit Praktikum')

@section('content')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Edit Praktikum</h1>
                <p class="text-sm text-zinc-500 mt-1">Perbarui data praktikum {{ $praktikum->kode_praktikum }}.</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                <span>/</span>
                <a href="{{ route('admin.praktikum.index') }}" class="hover:text-zinc-900 transition-colors">Praktikum</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Edit</span>
            </div>
        </div>

        <form action="{{ route('admin.praktikum.update', $praktikum->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Side Kiri: Informasi Dasar -->
                <div class="lg:col-span-2 space-y-6">
                    <div
                        class="rounded-2xl border border-zinc-200 bg-white shadow-sm overflow-hidden transition-all hover:shadow-md">
                        <div class="p-6 border-b border-zinc-100 bg-zinc-50/50">
                            <h3 class="font-bold text-zinc-900 flex items-center gap-2">
                                <i class="fas fa-edit text-[#001f3f]"></i>
                                Pembaruan Data Praktikum
                            </h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Nama
                                    Praktikum</label>
                                <input type="text" name="nama_praktikum"
                                    value="{{ old('nama_praktikum', $praktikum->nama_praktikum) }}" required
                                    placeholder="e.g., Pemrograman Berorientasi Objek"
                                    class="flex h-12 w-full rounded-xl border border-zinc-200 bg-zinc-50/30 px-4 py-2 text-sm shadow-sm transition-all focus:bg-white focus:ring-4 focus:ring-[#001f3f]/5 focus:border-[#001f3f] outline-none @error('nama_praktikum') border-rose-500 @enderror">
                                @error('nama_praktikum')
                                    <p class="text-[10px] text-rose-500 font-bold mt-1 uppercase">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Periode
                                        Praktikum</label>
                                    <input type="text" name="periode_praktikum"
                                        value="{{ old('periode_praktikum', $praktikum->periode_praktikum) }}" required
                                        placeholder="e.g., Ganjil 2024/2025"
                                        class="flex h-12 w-full rounded-xl border border-zinc-200 bg-zinc-50/30 px-4 py-2 text-sm shadow-sm transition-all focus:bg-white focus:ring-4 focus:ring-[#001f3f]/5 focus:border-[#001f3f] outline-none @error('periode_praktikum') border-rose-500 @enderror">
                                    @error('periode_praktikum')
                                        <p class="text-[10px] text-rose-500 font-bold mt-1 uppercase">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Kuota
                                        Praktikan</label>
                                    <input type="number" name="kuota_praktikan"
                                        value="{{ old('kuota_praktikan', $praktikum->kuota_praktikan) }}" required
                                        min="1" placeholder="Maks. Mahasiswa"
                                        class="flex h-12 w-full rounded-xl border border-zinc-200 bg-zinc-50/30 px-4 py-2 text-sm shadow-sm transition-all focus:bg-white focus:ring-4 focus:ring-[#001f3f]/5 focus:border-[#001f3f] outline-none @error('kuota_praktikan') border-rose-500 @enderror">
                                    @error('kuota_praktikan')
                                        <p class="text-[10px] text-rose-500 font-bold mt-1 uppercase">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Jumlah
                                        Modul</label>
                                    <input type="number" name="jumlah_modul" required min="0"
                                        value="{{ old('jumlah_modul', $praktikum->jumlah_modul) }}" placeholder="e.g., 4"
                                        class="flex h-12 w-full rounded-xl border border-zinc-200 bg-zinc-50/30 px-4 py-2 text-sm shadow-sm transition-all focus:bg-white focus:ring-4 focus:ring-[#001f3f]/5 focus:border-[#001f3f] outline-none @error('jumlah_modul') border-rose-500 @enderror">
                                    <p class="text-[9px] text-zinc-400 font-medium italic mt-1">Sistem akan menyesuaikan
                                        opsi jadwal berdasarkan jumlah modul.</p>
                                    @error('jumlah_modul')
                                        <p class="text-[10px] text-rose-500 font-bold mt-1 uppercase">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Tugas
                                        Akhir</label>
                                    <select name="ada_tugas_akhir" required
                                        class="flex h-12 w-full rounded-xl border border-zinc-200 bg-zinc-50/30 px-4 py-2 text-sm shadow-sm transition-all focus:bg-white focus:ring-4 focus:ring-[#001f3f]/5 focus:border-[#001f3f] outline-none @error('ada_tugas_akhir') border-rose-500 @enderror">
                                        <option value="0" {{ $praktikum->ada_tugas_akhir == 0 ? 'selected' : '' }}>
                                            Tidak Ada Tugas Akhir</option>
                                        <option value="1" {{ $praktikum->ada_tugas_akhir == 1 ? 'selected' : '' }}>Ada
                                            Tugas Akhir</option>
                                    </select>
                                    @error('ada_tugas_akhir')
                                        <p class="text-[10px] text-rose-500 font-bold mt-1 uppercase">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="rounded-2xl border border-zinc-200 bg-white shadow-sm overflow-hidden transition-all hover:shadow-md">
                        <div class="p-6 border-b border-zinc-100 bg-zinc-50/50">
                            <h3 class="font-bold text-zinc-900 flex items-center gap-2">
                                <i class="fas fa-toggle-on text-[#001f3f]"></i>
                                Status Sistem
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Pilih Status
                                    Praktikum</label>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <label
                                        class="relative flex flex-col p-4 border border-zinc-100 rounded-xl cursor-pointer transition-all hover:bg-zinc-50 group">
                                        <input type="radio" name="status_praktikum" value="open_registration"
                                            class="sr-only peer"
                                            {{ $praktikum->status_praktikum == 'open_registration' ? 'checked' : '' }}>
                                        <div
                                            class="absolute top-3 right-3 opacity-0 peer-checked:opacity-100 transition-opacity">
                                            <i class="fas fa-check-circle text-emerald-500 text-sm"></i>
                                        </div>
                                        <span
                                            class="text-[9px] font-black uppercase text-zinc-400 peer-checked:text-emerald-700 transition-colors">Registration</span>
                                        <span class="text-[11px] font-bold text-zinc-900 mt-1">Buka Pendaftaran</span>
                                        <div
                                            class="absolute inset-0 border-2 border-transparent peer-checked:border-emerald-500 rounded-xl pointer-events-none transition-all">
                                        </div>
                                    </label>

                                    <label
                                        class="relative flex flex-col p-4 border border-zinc-100 rounded-xl cursor-pointer transition-all hover:bg-zinc-50 group">
                                        <input type="radio" name="status_praktikum" value="on_progress"
                                            class="sr-only peer"
                                            {{ $praktikum->status_praktikum == 'on_progress' ? 'checked' : '' }}>
                                        <div
                                            class="absolute top-3 right-3 opacity-0 peer-checked:opacity-100 transition-opacity">
                                            <i class="fas fa-check-circle text-amber-500 text-sm"></i>
                                        </div>
                                        <span
                                            class="text-[9px] font-black uppercase text-zinc-400 peer-checked:text-amber-700 transition-colors">Process</span>
                                        <span class="text-[11px] font-bold text-zinc-900 mt-1">Berlangsung</span>
                                        <div
                                            class="absolute inset-0 border-2 border-transparent peer-checked:border-amber-500 rounded-xl pointer-events-none transition-all">
                                        </div>
                                    </label>

                                    <label
                                        class="relative flex flex-col p-4 border border-zinc-100 rounded-xl cursor-pointer transition-all hover:bg-zinc-50 group">
                                        <input type="radio" name="status_praktikum" value="finished" class="sr-only peer"
                                            {{ $praktikum->status_praktikum == 'finished' ? 'checked' : '' }}>
                                        <div
                                            class="absolute top-3 right-3 opacity-0 peer-checked:opacity-100 transition-opacity">
                                            <i class="fas fa-check-circle text-rose-500 text-sm"></i>
                                        </div>
                                        <span
                                            class="text-[9px] font-black uppercase text-zinc-400 peer-checked:text-rose-700 transition-colors">Ended</span>
                                        <span class="text-[11px] font-bold text-zinc-900 mt-1">Telah Berakhir</span>
                                        <div
                                            class="absolute inset-0 border-2 border-transparent peer-checked:border-rose-500 rounded-xl pointer-events-none transition-all">
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Side Kanan: Dynamic Options & Action -->
                <div class="space-y-6">
                    <div
                        class="rounded-2xl border border-zinc-200 bg-white shadow-sm overflow-hidden transition-all hover:shadow-md">
                        <div class="p-6 border-b border-zinc-100 bg-zinc-50/50 flex items-center justify-between">
                            <h3 class="font-bold text-zinc-900 text-sm">Opsi Dosen</h3>
                            <button type="button" onclick="addItem('dosen-list', 'daftar_dosen[]')"
                                class="h-8 px-3 rounded-lg bg-[#001f3f] text-white text-[10px] font-bold uppercase transition-all hover:bg-[#002d5a] active:scale-95">
                                + Tambah
                            </button>
                        </div>
                        <div class="p-6 space-y-4">
                            <div id="dosen-list" class="space-y-2">
                                @foreach ($praktikum->daftar_dosen ?? [] as $dosen)
                                    <div class="flex gap-2 group">
                                        <input type="text" name="daftar_dosen[]" value="{{ $dosen }}" required
                                            placeholder="Nama Dosen"
                                            class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-1 text-sm outline-none focus:bg-white focus:border-[#001f3f] transition-all">
                                        <button type="button" onclick="this.parentElement.remove()"
                                            class="h-10 w-10 flex items-center justify-center rounded-lg border border-rose-100 bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors">
                                            <i class="fas fa-trash-alt text-[10px]"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div
                        class="rounded-2xl border border-zinc-200 bg-white shadow-sm overflow-hidden transition-all hover:shadow-md">
                        <div class="p-6 border-b border-zinc-100 bg-zinc-50/50 flex items-center justify-between">
                            <h3 class="font-bold text-zinc-900 text-sm">Opsi Kelas</h3>
                            <button type="button" onclick="addItem('kelas-list', 'daftar_kelas_mk[]')"
                                class="h-8 px-3 rounded-lg bg-[#001f3f] text-white text-[10px] font-bold uppercase transition-all hover:bg-[#002d5a] active:scale-95">
                                + Tambah
                            </button>
                        </div>
                        <div class="p-6 space-y-4">
                            <div id="kelas-list" class="space-y-2">
                                @foreach ($praktikum->daftar_kelas_mk ?? [] as $kelas)
                                    <div class="flex gap-2 group">
                                        <input type="text" name="daftar_kelas_mk[]" value="{{ $kelas }}"
                                            required placeholder="4IAxx / 2KAxx"
                                            class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-1 text-sm outline-none focus:bg-white focus:border-[#001f3f] transition-all">
                                        <button type="button" onclick="this.parentElement.remove()"
                                            class="h-10 w-10 flex items-center justify-center rounded-lg border border-rose-100 bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors">
                                            <i class="fas fa-trash-alt text-[10px]"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="bg-zinc-50/50 rounded-2xl border border-zinc-100 p-4 space-y-3">
                        <button type="submit"
                            class="w-full py-4 rounded-xl bg-[#001f3f] text-white text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-[#001f3f]/20 hover:bg-[#002d5a] transition-all hover:-translate-y-0.5 active:scale-95">
                            PERBARUI DATA
                        </button>
                        <a href="{{ route('admin.praktikum.index') }}"
                            class="block w-full py-3 text-center text-[10px] font-bold text-zinc-400 uppercase tracking-widest hover:text-rose-500 transition-colors">
                            Batal dan Kembali
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <script>
            function addItem(containerId, inputName) {
                const container = document.getElementById(containerId);
                const div = document.createElement('div');
                div.className = 'flex gap-2 animate-in fade-in slide-in-from-top-1 duration-200';
                div.innerHTML = `
                    <input type="text" name="${inputName}" required placeholder="${containerId === 'dosen-list' ? 'Nama Dosen' : 'Contoh: 4IA01'}"
                        class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:border-[#001f3f] outline-none">
                    <button type="button" onclick="this.parentElement.remove()" class="h-10 w-10 flex items-center justify-center rounded-lg border border-rose-100 bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors">
                        <i class="fas fa-trash-alt text-[10px]"></i>
                    </button>
                `;
                container.appendChild(div);
            }
        </script>
    </div>
@endsection
