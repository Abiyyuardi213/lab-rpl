@extends('layouts.admin')

@section('title', 'Soal Praktikum')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 uppercase">Soal Praktikum</h1>
                <p class="text-sm text-zinc-500 mt-1 italic">"Akses soal praktikum sesuai dengan jadwal sesi Anda."</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('praktikan.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Penugasan</span>
            </div>
        </div>

        {{-- Info Bar: Waktu & Hari saat ini --}}
        @php
            $now = \Carbon\Carbon::now('Asia/Jakarta');
        @endphp
        <div class="p-4 bg-zinc-900 rounded-xl border border-zinc-800 shadow-2xl relative overflow-hidden group">
            <div class="flex flex-wrap items-center gap-6 relative z-10">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-lg bg-zinc-800 flex items-center justify-center text-zinc-400">
                        <i class="fas fa-clock text-xs"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-zinc-500 uppercase tracking-widest leading-none">Waktu Sekarang</p>
                        <p id="realtime-clock" class="text-xs font-black text-white mt-1.5 tabular-nums leading-none">{{ $now->format('H:i:s') }}</p>
                    </div>
                </div>
                <div class="h-8 w-px bg-zinc-800"></div>
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-lg bg-zinc-800 flex items-center justify-center text-zinc-400">
                        <i class="fas fa-calendar-alt text-xs"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-zinc-500 uppercase tracking-widest leading-none">Hari Ini</p>
                        <p class="text-xs font-black text-white mt-1.5 uppercase leading-none">{{ $now->locale('id')->dayName }}</p>
                    </div>
                </div>
                <div class="h-8 w-px bg-zinc-800"></div>
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-lg bg-zinc-800 flex items-center justify-center text-zinc-400">
                        <i class="fas fa-users text-xs"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-zinc-500 uppercase tracking-widest leading-none">Sesi Terdaftar</p>
                        <p class="text-xs font-black text-white mt-1.5 uppercase leading-none">
                            {{ $pendaftarans->map(function($p) {
                                return $p->sesi->nama_sesi . ' (' . substr($p->sesi->jam_mulai, 0, 5) . ' - ' . substr($p->sesi->jam_selesai, 0, 5) . ')';
                            })->unique()->implode(', ') ?: 'Tidak Ada Sesi' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        @if (session('error'))
            <div class="bg-rose-50 border border-rose-200 p-4 rounded-xl shadow-sm animate-in slide-in-from-top duration-300">
                <div class="flex items-center gap-4">
                    <div class="h-8 w-8 rounded-lg bg-rose-500 flex items-center justify-center text-white shrink-0 shadow-lg shadow-rose-500/10">
                        <i class="fas fa-lock text-[10px]"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-rose-700 font-bold uppercase tracking-widest leading-none">Akses Ditolak</p>
                        <p class="text-[10px] text-rose-600 mt-1.5 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Penugasan Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($penugasans as $p)
                <div class="bg-white rounded-xl border border-zinc-200 shadow-sm overflow-hidden flex flex-col group transition-all hover:shadow-lg {{ !$p->is_accessible ? 'opacity-75 grayscale-[0.5]' : '' }}">
                    <!-- Card Header -->
                    <div class="h-28 bg-gradient-to-br {{ $p->is_accessible ? 'from-[#001f3f] to-[#002d5a]' : 'from-zinc-700 to-zinc-800' }} p-5 flex flex-col justify-between relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 pointer-events-none">
                            <i class="fas fa-file-invoice text-7xl text-white"></i>
                        </div>
                        <div class="flex items-start justify-between relative z-10">
                            <span class="bg-white/10 backdrop-blur-md text-white text-[9px] font-bold px-2 py-1 rounded border border-white/20 uppercase tracking-widest leading-none">
                                {{ $p->praktikum->kode_praktikum }}
                            </span>
                            @if ($p->is_accessible)
                                <span class="bg-emerald-500/20 backdrop-blur-md text-emerald-400 text-[9px] font-black px-2 py-1 rounded border border-emerald-500/30 uppercase tracking-widest leading-none flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    TERBUKA
                                </span>
                            @else
                                <span class="bg-rose-500/20 backdrop-blur-md text-rose-400 text-[9px] font-black px-2 py-1 rounded border border-rose-500/30 uppercase tracking-widest leading-none flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                                    TERKUNCI
                                </span>
                            @endif
                        </div>
                        <div class="relative z-10">
                            <h3 class="text-sm font-black text-white line-clamp-1 uppercase tracking-tight leading-none">{{ $p->judul }}</h3>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-5 flex-grow flex flex-col">
                        <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest leading-none mb-4 italic">{{ $p->praktikum->nama_praktikum }}</p>
                        
                        <div class="space-y-3 pt-4 border-t border-zinc-100 mb-6">
                            <div class="flex items-center gap-3">
                                <div class="h-7 w-7 rounded-md bg-zinc-50 flex items-center justify-center text-zinc-400 border border-zinc-100">
                                    <i class="fas fa-calendar-day text-[10px]"></i>
                                </div>
                                <div>
                                    <p class="text-[8px] font-bold text-zinc-400 uppercase tracking-widest leading-none">Hari Sesi</p>
                                    <p class="text-[10px] font-bold text-zinc-700 uppercase mt-1 leading-none">{{ $p->sesi->hari }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="h-7 w-7 rounded-md bg-zinc-50 flex items-center justify-center text-zinc-400 border border-zinc-100">
                                    <i class="fas fa-clock text-[10px]"></i>
                                </div>
                                <div>
                                    <p class="text-[8px] font-bold text-zinc-400 uppercase tracking-widest leading-none">Jam Akses</p>
                                    <p class="text-[10px] font-bold text-zinc-700 uppercase mt-1 leading-none tabular-nums">{{ substr($p->sesi->jam_mulai, 0, 5) }} - {{ substr($p->sesi->jam_selesai, 0, 5) }}</p>
                                </div>
                            </div>

                        </div>

                        <div class="mt-auto">
                            @if ($p->is_accessible)
                                <a href="{{ route('praktikan.penugasan.show', $p->id) }}" 
                                    class="w-full h-10 bg-[#001f3f] text-white text-[10px] font-bold uppercase tracking-widest rounded-lg shadow-lg shadow-[#001f3f]/10 hover:bg-[#002d5a] transition-all flex items-center justify-center gap-2 group-active:scale-95">
                                    Lihat Soal
                                    <i class="fas fa-arrow-right text-[10px]"></i>
                                </a>
                            @else
                                <button disabled 
                                    class="w-full h-10 bg-zinc-50 text-zinc-400 text-[10px] font-bold uppercase tracking-widest rounded-lg border border-zinc-200 cursor-not-allowed flex items-center justify-center gap-2">
                                    <i class="fas fa-lock text-[10px]"></i>
                                    Terkunci
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-24 bg-white rounded-xl border border-dashed border-zinc-200 flex flex-col items-center justify-center text-center">
                    <div class="h-16 w-16 rounded-xl bg-zinc-50 flex items-center justify-center mb-4 border border-zinc-100 shadow-inner overflow-hidden relative">
                        <i class="fas fa-file-invoice text-zinc-200 text-2xl relative z-10"></i>
                        <div class="absolute inset-0 bg-gradient-to-br from-transparent to-zinc-100/50"></div>
                    </div>
                    <h5 class="text-zinc-400 font-bold uppercase tracking-widest text-xs">Belum Ada Soal</h5>
                    <p class="text-[10px] text-zinc-400 italic mt-2 font-medium tracking-tight">Asisten lab belum membagikan soal untuk sesi Anda.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const clockElement = document.getElementById('realtime-clock');
        if (clockElement) {
            let timeParts = clockElement.textContent.trim().split(':');
            if (timeParts.length === 3) {
                let hours = parseInt(timeParts[0]);
                let minutes = parseInt(timeParts[1]);
                let seconds = parseInt(timeParts[2]);

                setInterval(function() {
                    seconds++;
                    if (seconds >= 60) {
                        seconds = 0;
                        minutes++;
                        if (minutes >= 60) {
                            minutes = 0;
                            hours++;
                            if (hours >= 24) {
                                hours = 0;
                            }
                        }
                    }
                    
                    const h = String(hours).padStart(2, '0');
                    const m = String(minutes).padStart(2, '0');
                    const s = String(seconds).padStart(2, '0');
                    clockElement.textContent = `${h}:${m}:${s}`;
                }, 1000);
            }
        }
    });
</script>
@endpush
