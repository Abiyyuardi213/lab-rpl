@extends('layouts.admin')

@section('title', 'Tambah Praktikum')

@section('content')
    <div class="space-y-4 sm:space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Tambah Praktikum</h1>
                <p class="text-slate-500 mt-1 text-sm sm:text-base">Buat data praktikum baru untuk periode ini.</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-900 transition-colors">Home</a>
                <span>/</span>
                <a href="{{ route('admin.praktikum.index') }}" class="hover:text-slate-900 transition-colors">Praktikum</a>
                <span>/</span>
                <span class="text-slate-900 font-semibold">Tambah</span>
            </div>
        </div>

        <form action="{{ route('admin.praktikum.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Side Kiri: Informasi Dasar -->
                <div class="lg:col-span-2 space-y-6">
                    <div
                        class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden transition-all hover:shadow-md">
                        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                            <h3 class="font-bold text-slate-900 flex items-center gap-2">
                                <i class="fas fa-info-circle text-[#001f3f]"></i>
                                Informasi Utama Praktikum
                            </h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Nama
                                    Praktikum</label>
                                <input type="text" name="nama_praktikum" required
                                    placeholder="e.g., Pemrograman Berorientasi Objek"
                                    class="flex h-12 w-full rounded-lg border border-slate-200 bg-slate-50/30 px-4 py-2 text-sm shadow-sm transition-all focus:bg-white focus:ring-4 focus:ring-[#001f3f]/5 focus:border-[#001f3f] outline-none @error('nama_praktikum') border-rose-500 @enderror">
                                @error('nama_praktikum')
                                    <p class="text-[10px] text-rose-500 font-bold mt-1 uppercase">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Periode
                                        Praktikum</label>
                                    <input type="text" name="periode_praktikum" required
                                        placeholder="e.g., Ganjil 2024/2025"
                                        class="flex h-12 w-full rounded-lg border border-slate-200 bg-slate-50/30 px-4 py-2 text-sm shadow-sm transition-all focus:bg-white focus:ring-4 focus:ring-[#001f3f]/5 focus:border-[#001f3f] outline-none @error('periode_praktikum') border-rose-500 @enderror">
                                    @error('periode_praktikum')
                                        <p class="text-[10px] text-rose-500 font-bold mt-1 uppercase">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Kuota
                                        Praktikan</label>
                                    <input type="number" name="kuota_praktikan" required min="1"
                                        placeholder="Maks. Mahasiswa"
                                        class="flex h-12 w-full rounded-lg border border-slate-200 bg-slate-50/30 px-4 py-2 text-sm shadow-sm transition-all focus:bg-white focus:ring-4 focus:ring-[#001f3f]/5 focus:border-[#001f3f] outline-none @error('kuota_praktikan') border-rose-500 @enderror">
                                    @error('kuota_praktikan')
                                        <p class="text-[10px] text-rose-500 font-bold mt-1 uppercase">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Jumlah
                                        Modul</label>
                                    <input type="number" name="jumlah_modul" required min="0" value="0"
                                        placeholder="e.g., 4"
                                        class="flex h-12 w-full rounded-lg border border-slate-200 bg-slate-50/30 px-4 py-2 text-sm shadow-sm transition-all focus:bg-white focus:ring-4 focus:ring-[#001f3f]/5 focus:border-[#001f3f] outline-none @error('jumlah_modul') border-rose-500 @enderror">
                                    <p class="text-[9px] text-slate-400 font-medium italic mt-1">Sistem akan menyesuaikan
                                        opsi jadwal berdasarkan jumlah modul.</p>
                                    @error('jumlah_modul')
                                        <p class="text-[10px] text-rose-500 font-bold mt-1 uppercase">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Tugas
                                        Akhir</label>
                                    <select name="ada_tugas_akhir" required
                                        class="flex h-12 w-full rounded-lg border border-slate-200 bg-slate-50/30 px-4 py-2 text-sm shadow-sm transition-all focus:bg-white focus:ring-4 focus:ring-[#001f3f]/5 focus:border-[#001f3f] outline-none @error('ada_tugas_akhir') border-rose-500 @enderror">
                                        <option value="0">Tidak Ada Tugas Akhir</option>
                                        <option value="1">Ada Tugas Akhir</option>
                                    </select>
                                    @error('ada_tugas_akhir')
                                        <p class="text-[10px] text-rose-500 font-bold mt-1 uppercase">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden transition-all hover:shadow-md">
                        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                            <h3 class="font-bold text-slate-900 flex items-center gap-2">
                                <i class="fas fa-toggle-on text-[#001f3f]"></i>
                                Status Sistem
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Pilih Status
                                    Awal</label>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <label
                                        class="relative flex flex-col p-4 border border-slate-200 rounded-lg cursor-pointer transition-all hover:bg-slate-50 group">
                                        <input type="radio" name="status_praktikum" value="open_registration"
                                            class="sr-only peer" checked>
                                        <div
                                            class="absolute top-3 right-3 opacity-0 peer-checked:opacity-100 transition-opacity">
                                            <i class="fas fa-check-circle text-emerald-500 text-sm"></i>
                                        </div>
                                        <span
                                            class="text-[9px] font-black uppercase text-slate-400 peer-checked:text-emerald-700 transition-colors">Registration</span>
                                        <span class="text-[11px] font-bold text-slate-900 mt-1">Buka Pendaftaran</span>
                                        <div
                                            class="absolute inset-0 border-2 border-transparent peer-checked:border-emerald-500 rounded-lg pointer-events-none transition-all">
                                        </div>
                                    </label>

                                    <label
                                        class="relative flex flex-col p-4 border border-slate-200 rounded-lg cursor-pointer transition-all hover:bg-slate-50 group">
                                        <input type="radio" name="status_praktikum" value="on_progress"
                                            class="sr-only peer">
                                        <div
                                            class="absolute top-3 right-3 opacity-0 peer-checked:opacity-100 transition-opacity">
                                            <i class="fas fa-check-circle text-amber-500 text-sm"></i>
                                        </div>
                                        <span
                                            class="text-[9px] font-black uppercase text-slate-400 peer-checked:text-amber-700 transition-colors">Process</span>
                                        <span class="text-[11px] font-bold text-slate-900 mt-1">Berlangsung</span>
                                        <div
                                            class="absolute inset-0 border-2 border-transparent peer-checked:border-amber-500 rounded-lg pointer-events-none transition-all">
                                        </div>
                                    </label>

                                    <label
                                        class="relative flex flex-col p-4 border border-slate-200 rounded-lg cursor-pointer transition-all hover:bg-slate-50 group">
                                        <input type="radio" name="status_praktikum" value="finished" class="sr-only peer">
                                        <div
                                            class="absolute top-3 right-3 opacity-0 peer-checked:opacity-100 transition-opacity">
                                            <i class="fas fa-check-circle text-rose-500 text-sm"></i>
                                        </div>
                                        <span
                                            class="text-[9px] font-black uppercase text-slate-400 peer-checked:text-rose-700 transition-colors">Ended</span>
                                        <span class="text-[11px] font-bold text-slate-900 mt-1">Telah Berakhir</span>
                                        <div
                                            class="absolute inset-0 border-2 border-transparent peer-checked:border-rose-500 rounded-lg pointer-events-none transition-all">
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Section Sertifikat Spesifik Praktikum -->
                            <div class="pt-4 border-t border-slate-100 space-y-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-7 h-7 rounded-lg bg-sky-50 text-sky-700 flex items-center justify-center font-bold text-xs">
                                        <i class="fas fa-certificate"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-900 text-xs">Pengaturan Sertifikat Praktikum Ini (Opsional)</h3>
                                        <p class="text-[10px] text-slate-500">Kosongkan jika ingin memakai desain & prefix bawaan laboratorium.</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-1">
                                        <label class="text-xs font-bold text-slate-700">Prefix Kode Surat Praktikum Ini</label>
                                        <input type="text" name="nomor_surat_prefix" value="{{ old('nomor_surat_prefix') }}" placeholder="e.g. SERT/PSTF/ITATS"
                                            class="w-full px-3.5 py-2 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:border-[#001f3f]">
                                    </div>

                                    <div class="space-y-1">
                                        <label class="text-xs font-bold text-slate-700">Upload Desain Template Kustom</label>
                                        <input type="file" name="bg_sertifikat_template" accept="image/png,image/jpeg"
                                            class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Side Kanan: Dynamic Options & Action -->
                <div class="space-y-6">
                    <div
                        class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden transition-all hover:shadow-md">
                        <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                            <h3 class="font-bold text-slate-900 text-sm">Opsi Dosen</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="max-h-48 overflow-y-auto border border-slate-200 rounded-lg p-3 space-y-2 bg-slate-50/50">
                                @forelse($dosens as $dosen)
                                    <label class="flex items-start gap-2.5 text-xs font-medium text-slate-700 cursor-pointer hover:text-slate-900 transition-colors">
                                        <input type="checkbox" name="daftar_dosen[]" value="{{ $dosen->nama }}"
                                            class="rounded border-slate-300 text-[#001f3f] focus:ring-[#001f3f]/10 mt-0.5"
                                            {{ is_array(old('daftar_dosen')) && in_array($dosen->nama, old('daftar_dosen')) ? 'checked' : '' }}>
                                        <div class="flex flex-col">
                                            <span class="font-bold">{{ $dosen->nama }}</span>
                                            @if($dosen->nip)
                                                <span class="text-[9px] text-slate-400">NIP: {{ $dosen->nip }}</span>
                                            @endif
                                        </div>
                                    </label>
                                @empty
                                    <div class="text-center py-4 text-xs text-slate-400">
                                        Belum ada dosen aktif.
                                    </div>
                                @endforelse
                            </div>
                            @error('daftar_dosen')
                                <p class="text-[10px] text-rose-500 font-bold mt-1 uppercase">{{ $message }}</p>
                            @enderror
                            <div class="mt-2 text-right">
                                <a href="{{ route('admin.dosen.index') }}" target="_blank" class="text-[10px] font-bold text-[#001f3f] hover:underline uppercase tracking-wide">
                                    <i class="fas fa-cog mr-1"></i> Kelola Dosen
                                </a>
                            </div>
                        </div>
                    </div>

                    <div
                        class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden transition-all hover:shadow-md">
                        <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                            <h3 class="font-bold text-slate-900 text-sm">Opsi Kelas</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="max-h-48 overflow-y-auto border border-slate-200 rounded-lg p-3 space-y-2 bg-slate-50/50">
                                @forelse($kelas as $k)
                                    <label class="flex items-center gap-2.5 text-xs font-semibold text-slate-700 cursor-pointer hover:text-slate-900 transition-colors">
                                        <input type="checkbox" name="daftar_kelas_mk[]" value="{{ $k->nama_kelas }}"
                                            class="rounded border-slate-300 text-[#001f3f] focus:ring-[#001f3f]/10"
                                            {{ is_array(old('daftar_kelas_mk')) && in_array($k->nama_kelas, old('daftar_kelas_mk')) ? 'checked' : '' }}>
                                        <span>{{ $k->nama_kelas }}</span>
                                    </label>
                                @empty
                                    <div class="text-center py-4 text-xs text-slate-400">
                                        Belum ada kelas aktif.
                                    </div>
                                @endforelse
                            </div>
                            @error('daftar_kelas_mk')
                                <p class="text-[10px] text-rose-500 font-bold mt-1 uppercase">{{ $message }}</p>
                            @enderror
                            <div class="mt-2 text-right">
                                <a href="{{ route('admin.kelas.index') }}" target="_blank" class="text-[10px] font-bold text-[#001f3f] hover:underline uppercase tracking-wide">
                                    <i class="fas fa-cog mr-1"></i> Kelola Kelas
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50/50 rounded-xl border border-slate-200 p-4 space-y-3">
                        <button type="submit"
                            class="w-full py-4 rounded-lg bg-[#001f3f] text-white text-xs font-black uppercase tracking-[0.2em] shadow-xl shadow-[#001f3f]/20 hover:bg-[#002d5a] transition-all hover:-translate-y-0.5 active:scale-95">
                            SIMPAN PRAKTIKUM
                        </button>
                        <a href="{{ route('admin.praktikum.index') }}"
                            class="block w-full py-3 text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest hover:text-rose-500 transition-colors">
                            Batal dan Kembali
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
