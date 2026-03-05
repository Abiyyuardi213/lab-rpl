@extends('layouts.admin')

@section('title', 'Aslab Dashboard')

@section('content')
    <div class="space-y-8">
        <!-- Welcome Section -->
        <div class="relative overflow-hidden bg-[#001f3f] rounded-[2.5rem] p-6 md:p-12 shadow-2xl shadow-[#001f3f]/20">
            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-8 md:gap-12">
                <div class="space-y-4 md:space-y-6">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 border border-white/20 backdrop-blur-md">
                        <span class="relative flex h-2 w-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span class="text-[9px] md:text-[10px] font-black text-emerald-400 uppercase tracking-widest">Aslab
                            Portal
                            Active</span>
                    </div>

                    <div class="space-y-2">
                        <h2 class="text-white/50 text-xs md:text-lg font-bold uppercase tracking-[0.2em]">Selamat Datang,
                        </h2>
                        <h1 class="text-2xl md:text-5xl font-black text-white tracking-tight leading-tight uppercase">
                            {{ Auth::user()->name }}
                        </h1>
                    </div>

                    <p class="text-white/40 max-w-sm text-xs md:text-base leading-relaxed font-medium">
                        Pantau perkembangan mahasiswa bimbingan Anda dan kelola tugas asistensi dengan efisien.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-3 md:gap-4 w-full lg:w-auto">
                    <div
                        class="bg-white/5 backdrop-blur-xl border border-white/10 p-4 md:p-6 rounded-3xl flex-1 lg:min-w-[160px] group hover:bg-white/10 transition-all">
                        <p
                            class="text-white/30 text-[9px] md:text-[10px] font-black uppercase tracking-widest mb-1 group-hover:text-emerald-400 transition-colors">
                            Mhs Bimbingan</p>
                        <div class="flex items-end gap-1">
                            <span
                                class="text-3xl md:text-5xl font-black text-white tracking-tighter">{{ $assignedCount }}</span>
                            <span class="text-white/20 text-[10px] font-bold mb-1 uppercase tracking-widest">Orang</span>
                        </div>
                    </div>
                    <div
                        class="bg-white/5 backdrop-blur-xl border border-white/10 p-4 md:p-6 rounded-3xl flex-1 lg:min-w-[160px] group hover:bg-white/10 transition-all">
                        <p
                            class="text-white/30 text-[9px] md:text-[10px] font-black uppercase tracking-widest mb-1 group-hover:text-amber-400 transition-colors">
                            Total Quota</p>
                        <div class="flex items-end gap-1">
                            <span
                                class="text-3xl md:text-5xl font-black text-white tracking-tighter">{{ $totalQuota }}</span>
                            <span class="text-white/20 text-[10px] font-bold mb-1 uppercase tracking-widest">Slot</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Decorative elements -->
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 bg-white/5 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl"></div>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Assigned Practicums -->
            <div class="lg:col-span-2 space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-slate-900 flex items-center gap-3">
                        <i class="fas fa-microscope text-[#001f3f]"></i>
                        Praktikum Saya
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    @forelse($myPraktikums as $praktikum)
                        <div
                            class="group bg-white p-5 md:p-8 rounded-[2rem] border border-slate-200 shadow-sm hover:border-[#001f3f] hover:shadow-2xl hover:shadow-[#001f3f]/5 transition-all duration-500">
                            <div class="flex justify-between items-start mb-6">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-[#001f3f] group-hover:bg-[#001f3f] group-hover:text-white transition-all duration-500 shadow-inner">
                                    <i class="fas fa-flask text-lg"></i>
                                </div>
                                <span
                                    class="bg-slate-100 text-slate-500 px-3 py-1 rounded-full text-[9px] font-black font-mono group-hover:bg-[#001f3f]/10 group-hover:text-[#001f3f] transition-colors">
                                    {{ $praktikum->kode_praktikum }}
                                </span>
                            </div>

                            <div class="space-y-1 mb-6">
                                <h4
                                    class="text-base md:text-xl font-black text-slate-900 leading-tight group-hover:text-[#001f3f] transition-colors uppercase">
                                    {{ $praktikum->nama_praktikum }}</h4>
                                <p class="text-[10px] md:text-xs text-slate-400 font-bold uppercase tracking-widest italic">
                                    {{ $praktikum->periode_praktikum }}
                                </p>
                            </div>

                            <div class="space-y-4 bg-slate-50/50 p-4 rounded-2xl border border-slate-100/50 mb-6">
                                <div
                                    class="flex items-center justify-between text-[10px] md:text-xs uppercase tracking-wider font-black">
                                    <span class="text-slate-400">Progess Bimbingan</span>
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="text-slate-900">{{ Auth::user()->assignedStudents()->where('praktikum_id', $praktikum->id)->count() }}</span>
                                        <span class="text-slate-300">/</span>
                                        <span class="text-slate-400">{{ $praktikum->pivot->kuota }}</span>
                                    </div>
                                </div>
                                <div class="w-full bg-slate-200/50 h-2 rounded-full overflow-hidden">
                                    @php
                                        $used = Auth::user()
                                            ->assignedStudents()
                                            ->where('praktikum_id', $praktikum->id)
                                            ->count();
                                        $total = $praktikum->pivot->kuota;
                                        $p = $total > 0 ? ($used / $total) * 100 : 0;
                                    @endphp
                                    <div class="h-full bg-[#001f3f] rounded-full transition-all duration-1000 shadow-[0_0_10px_rgba(0,31,63,0.3)]"
                                        style="width: {{ min($p, 100) }}%"></div>
                                </div>
                            </div>

                            <a href="{{ route('aslab.pendaftaran.index') }}?praktikum={{ $praktikum->id }}"
                                class="w-full py-4 bg-[#001f3f] rounded-2xl text-[10px] font-black text-white flex items-center justify-center gap-3 hover:bg-[#002d5a] transition-all shadow-lg shadow-[#001f3f]/20 active:scale-[0.98]">
                                <i class="fas fa-users-viewfinder"></i>
                                LIHAT PANEL BIMBINGAN
                            </a>
                        </div>
                    @empty
                        <div
                            class="col-span-2 py-12 bg-white border border-dashed border-slate-300 rounded-[32px] flex flex-col items-center justify-center text-center">
                            <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center mb-4">
                                <i class="fas fa-calendar-times text-slate-300 text-2xl"></i>
                            </div>
                            <p class="text-slate-400 font-medium italic">Anda belum ditugaskan di praktikum manapun.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Sidebar / Recent Actions -->
            <div class="space-y-8">
                <div class="flex items-center justify-between px-2">
                    <h3 class="text-xl font-black text-slate-900 flex items-center gap-3 lowercase tracking-tighter">
                        <i class="fas fa-bolt text-amber-500"></i>
                        aksi cepat
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 gap-4">
                    <a href="{{ route('aslab.pendaftaran.index') }}"
                        class="group block bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:border-emerald-500 hover:shadow-xl hover:shadow-emerald-500/5 transition-all duration-300">
                        <div class="flex items-center gap-5">
                            <div
                                class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-500 group-hover:text-white transition-all duration-500 shadow-inner">
                                <i class="fas fa-user-plus text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-black text-slate-900 uppercase">Ambil Bimbingan</p>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Klaim
                                    mahasiswa</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('aslab.tugas.index') }}"
                        class="group block bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:border-[#001f3f] hover:shadow-xl hover:shadow-[#001f3f]/5 transition-all duration-300">
                        <div class="flex items-center gap-5">
                            <div
                                class="w-12 h-12 rounded-2xl bg-[#001f3f]/5 text-[#001f3f] flex items-center justify-center group-hover:bg-[#001f3f] group-hover:text-white transition-all duration-500 shadow-inner">
                                <i class="fas fa-tasks text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-black text-slate-900 uppercase">Beri Tugas</p>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Input tugas
                                    baru</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- System Stats Card -->
                <div
                    class="bg-gradient-to-br from-[#001f3f] to-[#003366] p-8 rounded-[2.5rem] shadow-2xl shadow-[#001f3f]/30 text-white overflow-hidden relative group">
                    <div class="relative z-10">
                        <div
                            class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center mb-6 border border-white/10">
                            <i class="fas fa-shield-heart text-white/80"></i>
                        </div>
                        <h4 class="text-base font-black mb-3 uppercase tracking-tight">Informasi Lab RPL</h4>
                        <p class="text-xs text-white/50 leading-relaxed mb-8 font-medium">Pastikan seluruh berkas asistensi
                            diunggah
                            tepat waktu untuk proses penilaian akhir semester.</p>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="relative flex h-2 w-2">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-20"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-white/40"></span>
                                </span>
                                <span class="text-[9px] font-black uppercase tracking-[0.2em] text-white/40">Status:
                                    Aktif</span>
                            </div>
                            <span class="text-[9px] font-black uppercase tracking-[0.2em] text-white/20">Update: 5 Mar
                                '26</span>
                        </div>
                    </div>
                    <!-- Decorative blobs -->
                    <div
                        class="absolute -top-12 -right-12 w-32 h-32 bg-white/5 rounded-full blur-2xl group-hover:bg-white/10 transition-all duration-700">
                    </div>
                    <div
                        class="absolute -bottom-12 -left-12 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all duration-700">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
