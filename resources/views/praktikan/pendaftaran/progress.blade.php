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
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="space-y-1 max-w-2xl">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-bold text-slate-900">{{ $t->judul }}</h4>
                                    @php
                                        $isOverdue = $t->due_date && now()->greaterThan($t->due_date->endOfDay());
                                    @endphp
                                    @if ($t->status === 'reviewed')
                                        <span
                                            class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-bold border border-emerald-100 flex items-center gap-1">
                                            <i class="fas fa-check-double"></i>
                                            DINILAI
                                        </span>
                                    @elseif($isOverdue && $t->status !== 'submitted')
                                        <span
                                            class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-600 text-[10px] font-bold border border-rose-100 uppercase tracking-tighter shadow-sm">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            Deadline Berakhir
                                        </span>
                                    @elseif($t->status === 'submitted')
                                        <span
                                            class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-600 text-[10px] font-bold border border-amber-100">TUNGGU
                                            REVIEW</span>
                                    @else
                                        <span
                                            class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[10px] font-bold">BELUM
                                            SELESAI</span>
                                    @endif
                                </div>
                                <p class="text-sm text-slate-600 leading-relaxed">{{ $t->deskripsi }}</p>
                                @if ($t->file_tugas)
                                    <div class="mt-3">
                                        <a href="{{ asset('storage/' . $t->file_tugas) }}" target="_blank"
                                            class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-bold border border-blue-100 hover:bg-blue-100 transition-all">
                                            <i class="fas fa-file-download"></i>
                                            UNDUH SOAL/MODUL
                                        </a>
                                    </div>
                                @endif
                                <div
                                    class="flex items-center gap-4 text-[10px] font-bold uppercase tracking-tight text-slate-400">
                                    <span class="flex items-center gap-1.5 {{ $isOverdue ? 'text-rose-500' : '' }}">
                                        <i class="far fa-calendar-alt"></i>
                                        Deadline: {{ $t->due_date ? $t->due_date->format('d M Y') : 'Tanpa batas' }}
                                    </span>
                                    @if ($t->nilai)
                                        <span
                                            class="flex items-center gap-1.5 text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">
                                            <i class="fas fa-star text-[8px]"></i>
                                            Skor: {{ $t->nilai }}/100
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-col items-end gap-3 min-w-[200px]">
                                @if ($t->status === 'reviewed')
                                    <div class="text-right">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">
                                            Feedback Aslab</p>
                                        <p
                                            class="text-[11px] text-slate-600 italic bg-amber-50 p-3 rounded-xl border border-amber-100">
                                            "{{ $t->catatan_aslab ?? 'Bagus sekali, pertahankan!' }}"</p>
                                    </div>
                                @elseif($isOverdue && $t->status !== 'submitted')
                                    <div class="text-right flex flex-col items-end">
                                        <div class="h-10 w-10 rounded-full bg-rose-50 flex items-center justify-center text-rose-500 mb-2 border border-rose-100 shadow-sm">
                                            <i class="fas fa-lock text-sm"></i>
                                        </div>
                                        <span class="text-[10px] font-black text-rose-500 uppercase tracking-widest">Akses Ditutup</span>
                                        <p class="text-[9px] text-slate-400 italic">Lewat batas pengumpulan</p>
                                    </div>
                                @else
                                    @if($isOverdue && $t->status === 'submitted')
                                        <div class="mb-1 text-right">
                                            <span class="text-[8px] font-black text-rose-500 uppercase px-2 py-0.5 bg-rose-50 rounded border border-rose-100">TERKUNCI (OVERDUE)</span>
                                        </div>
                                    @endif

                                    @if(!$isOverdue)
                                        <form action="{{ route('praktikan.pendaftaran.submit-tugas', $t->id) }}" method="POST"
                                            enctype="multipart/form-data" class="w-full flex flex-col gap-2">
                                            @csrf
                                            <div class="relative group">
                                                <input type="file" name="file_mahasiswa" required
                                                    onchange="this.form.submit()"
                                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                                <div
                                                    class="w-full px-4 py-2.5 border-2 border-dashed border-slate-200 rounded-xl text-xs font-bold text-slate-500 flex items-center justify-center gap-2 group-hover:border-[#001f3f] group-hover:text-[#001f3f] transition-all">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                    {{ $t->file_mahasiswa ? 'Update Tugas' : 'Unggah Tugas' }}
                                                </div>
                                            </div>
                                        </form>
                                    @endif

                                    @if ($t->file_mahasiswa)
                                        <div class="flex items-center justify-between gap-4 w-full px-2">
                                            <span class="text-[10px] text-emerald-500 font-bold whitespace-nowrap"><i
                                                    class="fas fa-check"></i> Tersimpan</span>
                                            <a href="{{ asset('storage/' . $t->file_mahasiswa) }}" target="_blank"
                                                class="text-[10px] font-black text-[#001f3f] hover:underline uppercase tracking-tight flex items-center gap-1">
                                                <i class="fas fa-external-link-alt text-[8px]"></i>
                                                Lihat File
                                            </a>
                                        </div>
                                    @endif
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
