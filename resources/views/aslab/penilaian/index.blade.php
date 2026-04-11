@extends('layouts.admin')

@section('title', 'Penilaian Live')

@section('content')
    <div class="space-y-8">
        <!-- Header -->
        <div class="relative overflow-hidden bg-gradient-to-br from-[#001f3f] to-[#003366] rounded-3xl p-8 md:p-12 shadow-2xl shadow-[#001f3f]/20">
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 border border-white/20 backdrop-blur-md mb-6">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-[10px] font-black text-emerald-400 uppercase tracking-widest">Live Assessment Mode</span>
                </div>
                <h1 class="text-3xl md:text-5xl font-black text-white tracking-tight leading-tight uppercase mb-4">
                    Penilaian Praktikum
                </h1>
                <p class="text-white/60 max-w-2xl text-sm md:text-lg leading-relaxed font-medium">
                    Pilih jadwal praktikum yang sedang berlangsung hari ini untuk mulai memberikan penilaian kepada praktikan secara langsung.
                </p>
            </div>
            
            <!-- Decorative elements -->
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 bg-white/5 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl"></div>
        </div>

        <!-- Today's Schedules -->
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-slate-900 flex items-center gap-3">
                    <i class="fas fa-calendar-day text-[#001f3f]"></i>
                    Jadwal Hari Ini ({{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y') }})
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse($jadwals as $jadwal)
                    <div class="group bg-white p-6 rounded-[2.5rem] border border-slate-200 shadow-sm hover:border-[#001f3f] hover:shadow-2xl hover:shadow-[#001f3f]/5 transition-all duration-500">
                        <div class="flex justify-between items-start mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-[#001f3f] group-hover:bg-[#001f3f] group-hover:text-white transition-all duration-500 shadow-inner">
                                <i class="fas fa-clock text-lg"></i>
                            </div>
                            <span class="bg-emerald-100 text-emerald-600 px-3 py-1 rounded-full text-[9px] font-black font-mono">
                                {{ substr($jadwal->waktu_mulai, 0, 5) }} - {{ substr($jadwal->waktu_selesai, 0, 5) }}
                            </span>
                        </div>

                        <div class="space-y-2 mb-6">
                            <p class="text-[10px] text-[#001f3f] font-black uppercase tracking-[0.2em] opacity-50">{{ $jadwal->praktikum->nama_praktikum }}</p>
                            <h4 class="text-xl font-black text-slate-900 leading-tight group-hover:text-[#001f3f] transition-colors uppercase">
                                {{ $jadwal->judul_modul }}
                            </h4>
                        </div>

                        <div class="flex items-center gap-4 py-4 border-y border-slate-100 mb-6">
                            <div class="flex-1">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Sudah Presensi</p>
                                <div class="flex items-center gap-2">
                                    <span class="text-xl font-black text-slate-900">{{ $jadwal->presensis_count }}</span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase">Praktikan</span>
                                </div>
                            </div>
                            <div class="w-px h-8 bg-slate-100"></div>
                            <div class="flex-1">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Ruangan</p>
                                <p class="text-xs font-bold text-slate-700 uppercase tracking-tight">{{ $jadwal->ruangan ?? 'LAB RPL' }}</p>
                            </div>
                        </div>

                        <a href="{{ route('aslab.penilaian.show', $jadwal->id) }}" 
                           class="w-full py-4 bg-[#001f3f] rounded-2xl text-[11px] font-black text-white flex items-center justify-center gap-3 hover:bg-[#002d5a] transition-all shadow-lg shadow-[#001f3f]/20 group-hover:translate-y-[-2px]">
                            MULAI PENILAIAN LIVE
                            <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                @empty
                    <div class="col-span-full py-20 bg-white border border-dashed border-slate-300 rounded-[3rem] flex flex-col items-center justify-center text-center">
                        <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center mb-6">
                            <i class="fas fa-calendar-xmark text-slate-300 text-2xl"></i>
                        </div>
                        <h5 class="text-slate-900 font-black uppercase text-sm tracking-widest mb-2">Tidak Ada Jadwal</h5>
                        <p class="text-slate-400 text-sm font-medium italic">Tidak ada jadwal praktikum yang ditemukan untuk hari ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
