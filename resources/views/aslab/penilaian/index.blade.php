@extends('layouts.admin')

@section('title', 'Penilaian Praktikum')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 uppercase">Penilaian Praktikum</h1>
                <p class="text-sm text-zinc-500 mt-1 italic">"Pilih jadwal untuk mulai memberikan penilaian live atau asistensi."</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('aslab.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold uppercase">Penilaian</span>
            </div>
        </div>

        <!-- Schedule List Container -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] flex items-center gap-2">
                    <i class="fas fa-calendar-alt"></i>
                    Daftar Jadwal Praktikum
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($jadwals as $jadwal)
                    <div class="group relative rounded-xl border border-zinc-200 bg-white p-6 shadow-sm hover:border-zinc-900 transition-all duration-300 flex flex-col justify-between overflow-hidden">
                        <!-- BG Decoration -->
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-zinc-50 rounded-full group-hover:bg-zinc-100 transition-colors -z-0"></div>
                        
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-bold bg-zinc-100 text-zinc-600 border border-zinc-200">
                                    {{ substr($jadwal->waktu_mulai, 0, 5) }} - {{ substr($jadwal->waktu_selesai, 0, 5) }}
                                </span>
                                <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">{{ $jadwal->tanggal }}</span>
                            </div>

                            <div class="space-y-1 mb-6">
                                <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest leading-none">{{ $jadwal->praktikum->nama_praktikum }}</p>
                                <h4 class="text-lg font-black text-zinc-900 tracking-tight leading-tight uppercase group-hover:text-zinc-800 transition-colors">
                                    {{ $jadwal->judul_modul }}
                                </h4>
                            </div>

                            <div class="flex items-center gap-4 py-3 border-y border-zinc-100 mb-6 group-hover:border-zinc-200 transition-colors">
                                <div class="flex flex-col">
                                    <span class="text-[8px] font-bold text-zinc-400 uppercase tracking-widest">Presensi</span>
                                    <span class="text-base font-black text-zinc-900">{{ $jadwal->presensis->count() }} <span class="text-[10px] font-bold text-zinc-400 uppercase ml-0.5">MHS</span></span>
                                </div>
                                <div class="w-px h-6 bg-zinc-100 group-hover:bg-zinc-200 transition-colors"></div>
                                <div class="flex flex-col">
                                    <span class="text-[8px] font-bold text-zinc-400 uppercase tracking-widest">Ruangan</span>
                                    <span class="text-[10px] font-black text-zinc-700 uppercase tracking-tight">{{ $jadwal->ruangan ?? 'LAB RPL' }}</span>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('aslab.penilaian.show', $jadwal->id) }}" 
                           class="relative z-10 w-full py-2.5 bg-zinc-900 text-white rounded-lg text-[10px] font-bold uppercase tracking-widest flex items-center justify-center gap-2 hover:bg-zinc-800 transition-all shadow-sm active:scale-[0.98]">
                            Mulai Penilaian
                            <i class="fas fa-chevron-right text-[8px]"></i>
                        </a>
                    </div>
                @empty
                    <div class="col-span-full py-16 rounded-xl border-2 border-dashed border-zinc-100 bg-zinc-50/30 flex flex-col items-center justify-center text-center">
                        <div class="w-12 h-12 rounded-full bg-white border border-zinc-100 flex items-center justify-center mb-4 shadow-sm">
                            <i class="fas fa-calendar-xmark text-zinc-300 text-lg"></i>
                        </div>
                        <h5 class="text-zinc-900 font-bold uppercase text-xs tracking-tight mb-1">Tidak Ada Jadwal</h5>
                        <p class="text-zinc-400 text-[11px] font-medium italic">Anda belum memiliki jadwal praktikum yang ditugaskan.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
