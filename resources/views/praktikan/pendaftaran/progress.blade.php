@extends('layouts.admin')

@section('title', 'Progress Praktikum')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Progress Praktikum</h1>
                <p class="text-sm text-slate-500">Pantau tugas dan bimbingan aslab Anda untuk
                    {{ $pendaftaran->praktikum->nama_praktikum }}</p>
            </div>
            <a href="{{ route('praktikan.pendaftaran.index') }}"
                class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-50 transition-all">
                Kembali
            </a>
        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Aslab Info -->
            <div
                class="lg:col-span-1 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col items-center text-center">
                <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Asisten Lab</h4>
                @if ($pendaftaran->aslab)
                    <div
                        class="w-16 h-16 rounded-full bg-[#001f3f]/10 border-2 border-[#001f3f]/20 flex items-center justify-center mb-3">
                        @if ($pendaftaran->aslab->user->profile_picture)
                            <img src="{{ asset('storage/' . $pendaftaran->aslab->user->profile_picture) }}"
                                class="w-full h-full rounded-full object-cover" alt="Profile Photo"
                                onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode($pendaftaran->aslab->user->name) }}&background=001f3f&color=fff&bold=true';">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($pendaftaran->aslab->user->name) }}&background=001f3f&color=fff&bold=true"
                                class="w-full h-full rounded-full" alt="Avatar">
                        @endif
                    </div>
                    <h3 class="font-bold text-slate-900 leading-tight">{{ $pendaftaran->aslab->user->name }}</h3>
                    <p class="text-[10px] text-slate-500 font-medium mt-1 uppercase">{{ $pendaftaran->aslab->npm }}</p>
                    <div class="mt-4 pt-4 border-t border-slate-50 w-full">
                        <a href="mailto:{{ $pendaftaran->aslab->user->email }}"
                            class="text-xs font-bold text-[#001f3f] hover:underline flex items-center justify-center gap-2">
                            <i class="fas fa-envelope"></i>
                            Hubungi Aslab
                        </a>
                    </div>
                @else
                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-3">
                        <i class="fas fa-user-clock text-slate-400 text-xl"></i>
                    </div>
                    <span class="text-xs text-slate-400 italic">Menunggu pembagian aslab</span>
                @endif
            </div>

            <!-- Progress Stats -->
            <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-6">
                @php
                    $totalTugas = $pendaftaran->tugasAsistensis->count();
                    $submittedTugas = $pendaftaran->tugasAsistensis->where('status', 'submitted')->count();
                    $reviewedTugas = $pendaftaran->tugasAsistensis->where('status', 'reviewed')->count();
                    $avgNilai = $pendaftaran->tugasAsistensis->where('status', 'reviewed')->avg('nilai');
                @endphp
                <div
                    class="bg-[#001f3f] p-6 rounded-2xl shadow-lg shadow-[#001f3f]/20 text-white flex flex-col justify-between">
                    <span class="text-[10px] font-bold opacity-60 uppercase tracking-widest">Total Tugas</span>
                    <div class="flex items-end gap-2 mt-2">
                        <span class="text-4xl font-bold">{{ $totalTugas }}</span>
                        <span class="text-sm opacity-60 mb-1">Modul</span>
                    </div>
                    <div class="mt-4 text-[10px] bg-white/20 px-3 py-1 rounded-full w-fit">
                        Status: {{ $submittedTugas + $reviewedTugas }}/{{ $totalTugas }} Selesai
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Rata-rata Nilai</span>
                    <div class="flex items-end gap-2 mt-2">
                        <span class="text-4xl font-bold text-slate-900">{{ number_format($avgNilai ?: 0, 1) }}</span>
                        <i class="fas fa-star text-amber-400 mb-2"></i>
                    </div>
                    <div class="mt-4 text-[10px] font-bold text-emerald-600 flex items-center gap-1">
                        <i class="fas fa-chart-line"></i>
                        Berdasarkan {{ $reviewedTugas }} tugas dinilai
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Persentase</span>
                    <div class="flex items-end gap-2 mt-2">
                        <span
                            class="text-4xl font-bold text-slate-900">{{ $totalTugas > 0 ? round(($reviewedTugas / $totalTugas) * 100) : 0 }}%</span>
                    </div>
                    <div class="mt-4 w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                        <div class="bg-emerald-500 h-full"
                            style="width: {{ $totalTugas > 0 ? ($reviewedTugas / $totalTugas) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tasks List -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h3 class="font-bold text-slate-900">Daftar Tugas & Asistensi</h3>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($pendaftaran->tugasAsistensis as $t)
                    <div class="p-6 hover:bg-slate-50/50 transition-colors">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
                            <!-- Left: Content & Assignment Info -->
                            <div class="lg:col-span-7 space-y-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="text-base font-bold text-slate-900">{{ $t->judul }}</h4>
                                    @php
                                        $isOverdue = $t->due_date && now()->greaterThan($t->due_date->endOfDay());
                                    @endphp
                                    @if ($t->status === 'reviewed')
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-bold border border-emerald-100 flex items-center gap-1 shadow-sm">
                                            <i class="fas fa-check-double text-[8px]"></i>
                                            DINILAI
                                        </span>
                                        @if(!$isOverdue)
                                            <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-600 text-[10px] font-bold border border-amber-100 flex items-center gap-1 shadow-sm">
                                                <i class="fas fa-pencil-alt text-[8px]"></i>
                                                BISA REVISI
                                            </span>
                                        @endif
                                    @elseif($isOverdue && $t->status !== 'submitted')
                                        <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-600 text-[10px] font-bold border border-rose-100 flex items-center gap-1 shadow-sm">
                                            <i class="fas fa-exclamation-circle text-[8px]"></i>
                                            AKSES DITUTUP
                                        </span>
                                    @elseif($t->status === 'submitted')
                                        <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-600 text-[10px] font-bold border border-amber-100 shadow-sm">DIPROSES</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[10px] font-bold border border-slate-200">PENDING</span>
                                    @endif
                                </div>

                                <p class="text-sm text-slate-600 leading-relaxed font-medium">{{ $t->deskripsi }}</p>

                                <div class="flex flex-wrap items-center gap-4 pt-2">
                                    @if ($t->file_tugas)
                                        <a href="{{ asset('storage/' . $t->file_tugas) }}" target="_blank"
                                            class="inline-flex items-center gap-2 px-3 py-1.5 bg-[#001f3f] text-white rounded-lg text-[10px] font-bold hover:bg-[#002f5f] transition-all shadow-md shadow-[#001f3f]/10">
                                            <i class="fas fa-file-download"></i>
                                            UNDUH SOAL
                                        </a>
                                    @endif

                                    <div class="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest {{ $isOverdue ? 'text-rose-500' : 'text-slate-400' }}">
                                        <i class="far fa-calendar-alt"></i>
                                        DL: {{ $t->due_date ? $t->due_date->format('d/m/Y') : '-' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Interaction & Score -->
                            <div class="lg:col-span-5 flex flex-col gap-4">
                                @if ($t->status === 'reviewed')
                                    <!-- Dynamic Score Card -->
                                    <div class="w-full space-y-3">
                                        <div class="bg-white border-2 border-emerald-500/20 p-4 rounded-2xl shadow-sm relative overflow-hidden group">
                                            <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:scale-110 transition-transform duration-700">
                                                <i class="fas fa-star text-8xl text-emerald-600"></i>
                                            </div>
                                            <div class="flex items-center justify-between relative">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-12 h-12 rounded-xl bg-emerald-500 flex items-center justify-center text-white shadow-xl shadow-emerald-500/20">
                                                        <i class="fas fa-star text-lg"></i>
                                                    </div>
                                                    <div>
                                                        <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.2em] leading-none mb-1.5">Skor Akhir</p>
                                                        <h4 class="text-3xl font-black text-slate-900 leading-none">
                                                            {{ $t->nilai }}<span class="text-xs text-slate-400 font-bold ml-1.5 tracking-normal">/ 100</span>
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="space-y-2">
                                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1 flex items-center gap-2">
                                                <span class="w-4 h-[2px] bg-amber-200"></span>
                                                Feedback Aslab
                                            </p>
                                            <div class="text-xs text-slate-600 bg-amber-50/50 p-4 rounded-2xl border border-amber-100/50 relative">
                                                <i class="fas fa-quote-left text-amber-200 text-sm absolute -top-1 -left-1"></i>
                                                <span class="italic leading-relaxed relative z-10 block pl-2">{{ $t->catatan_aslab ?? 'Bagus sekali, pertahankan performa Anda!' }}</span>
                                            </div>
                                        </div>

                                    {{-- Re-upload: tampilkan jika reviewed tapi deadline belum lewat --}}
                                    @if(!$isOverdue)
                                        <div class="w-full border border-rose-200 rounded-2xl p-3 bg-rose-50/60 space-y-2">
                                            <p class="text-[9px] font-black text-rose-600 uppercase tracking-widest flex items-center gap-1.5">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                Ganti Jawaban (Nilai Akan Direset)
                                            </p>
                                            <p class="text-[9px] text-rose-500 leading-relaxed">Jika Anda mengunggah ulang, nilai dan catatan aslab akan dihapus. Aslab perlu menilai kembali.</p>
                                            <form action="{{ route('praktikan.pendaftaran.submit-tugas', $t->id) }}" method="POST" enctype="multipart/form-data" class="w-full">
                                                @csrf
                                                <label class="relative group block cursor-pointer">
                                                    <input type="file" name="file_mahasiswa" required onchange="this.closest('form').requestSubmit()" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                                    <span class="w-full px-4 py-3 border-2 border-dashed border-rose-300 bg-white rounded-xl text-[10px] font-bold text-rose-500 flex items-center justify-center gap-2 group-hover:border-rose-500 group-hover:bg-rose-50 transition-all duration-300">
                                                        <i class="fas fa-cloud-upload-alt"></i>
                                                        <span>Pilih File Baru untuk Mengganti</span>
                                                    </span>
                                                    <span class="mt-2 block text-center text-[9px] font-semibold text-rose-500">
                                                        Ukuran max file yang dapat di upload adalah 7 MB
                                                    </span>
                                                </label>
                                            </form>
                                        </div>
                                    @endif
                                    </div>{{-- end reviewed --}}
                                @elseif($isOverdue && $t->status !== 'submitted')
                                    {{-- Deadline lewat dan belum pernah submit sama sekali --}}
                                    <div class="h-full flex flex-col items-center lg:items-end justify-center py-4">
                                        <div class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-500 mb-3 border border-rose-100/50 shadow-inner">
                                            <i class="fas fa-lock text-lg"></i>
                                        </div>
                                        <span class="text-[10px] font-black text-rose-500 uppercase tracking-[0.2em]">Akses Ditutup</span>
                                        <p class="text-[11px] text-slate-400 italic mt-1">Melewati batas pengumpulan</p>
                                    </div>
                                @else
                                    {{-- Status pending/submitted, deadline masih berlaku --}}
                                    <div class="w-full space-y-4">

                                        {{-- Tombol upload: muncul selama deadline belum lewat, apapun statusnya (pending/submitted) --}}
                                        @if(!$isOverdue)
                                            <form action="{{ route('praktikan.pendaftaran.submit-tugas', $t->id) }}" method="POST"
                                                enctype="multipart/form-data" class="w-full">
                                                @csrf
                                                <div class="relative group">
                                                    <input type="file" name="file_mahasiswa" required onchange="this.closest('form').requestSubmit()"
                                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                                    <div class="w-full px-6 py-4 border-2 border-dashed {{ $t->file_mahasiswa ? 'border-amber-300 bg-amber-50/50' : 'border-slate-200' }} rounded-2xl text-xs font-bold text-slate-500 flex flex-col items-center justify-center gap-2 group-hover:border-[#001f3f] group-hover:text-[#001f3f] group-hover:bg-slate-50 transition-all duration-300">
                                                        <i class="fas fa-cloud-upload-alt text-2xl mb-1"></i>
                                                        <span>{{ $t->file_mahasiswa ? 'Ganti / Update Jawaban' : 'Unggah Tugas Anda' }}</span>
                                                        @if($t->file_mahasiswa)
                                                            <span class="text-[9px] text-amber-600 font-medium">File lama akan diganti</span>
                                                        @endif
                                                        <span class="text-[9px] text-slate-400 font-medium">Ukuran max file yang dapat di upload adalah 7 MB</span>
                                                    </div>
                                                </div>
                                            </form>
                                        @else
                                            {{-- Status submitted tapi deadline sudah lewat — menunggu penilaian --}}
                                            <div class="flex items-center justify-center lg:justify-end">
                                                <span class="text-[9px] font-black text-amber-600 uppercase px-3 py-1 bg-amber-50 border border-amber-200 rounded-lg tracking-widest">
                                                    <i class="fas fa-clock-rotate-left mr-1"></i>MENUNGGU PENILAIAN
                                                </span>
                                            </div>
                                        @endif

                                        @if ($t->file_mahasiswa)
                                            <div class="flex items-center justify-between gap-4 w-full bg-slate-50/80 p-3 rounded-xl border border-slate-200/50">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                                                        <i class="fas fa-file-check"></i>
                                                    </div>
                                                    <div>
                                                        <p class="text-[9px] font-black text-emerald-600 uppercase tracking-widest leading-none mb-1">Status</p>
                                                        <p class="text-[10px] text-slate-500 font-bold">Jawaban Tersimpan</p>
                                                    </div>
                                                </div>
                                                <a href="{{ asset('storage/' . $t->file_mahasiswa) }}" target="_blank"
                                                    class="h-8 px-4 rounded-lg bg-white border border-slate-200 text-[10px] font-bold text-slate-600 hover:text-[#001f3f] hover:border-[#001f3f] transition-all flex items-center gap-2 shadow-sm">
                                                    <i class="fas fa-external-link-alt text-[8px]"></i>
                                                    LIHAT
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center text-slate-400 italic font-medium">
                        Belum ada tugas asistensi yang diberikan oleh aslab.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
