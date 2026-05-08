@extends('layouts.admin')

@section('title', 'Pilih Jadwal Modul')

@section('content')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 uppercase">{{ $praktikum->nama_praktikum }}</h1>
                <p class="text-sm text-zinc-500 mt-1 font-medium italic">"Pilih modul praktikum untuk mengelola penilaian."</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('admin.penilaian.index') }}" class="hover:text-zinc-900 transition-colors">Penilaian</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">{{ $praktikum->kode_praktikum }}</span>
            </div>
        </div>

        <!-- Jadwal Table / List in Shadcn Style -->
        <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden">
            <div class="p-6 pb-4 border-b border-zinc-100 flex items-center justify-between">
                 <h3 class="text-sm font-bold text-zinc-500 uppercase tracking-widest leading-none">Daftar Modul / Jadwal Pelaksanaan</h3>
            </div>

            <div class="divide-y divide-zinc-100">
                @forelse($praktikum->jadwals as $jadwal)
                    <a href="{{ route('admin.penilaian.jadwal', $jadwal->id) }}" class="flex items-center justify-between p-6 hover:bg-zinc-50/50 transition-colors group">
                        <div class="flex items-center gap-6">
                            <div class="w-12 h-12 rounded-xl bg-zinc-100 text-zinc-400 group-hover:bg-zinc-900 group-hover:text-white flex items-center justify-center transition-all shadow-inner border border-zinc-200">
                                <i class="fas fa-calendar-day text-sm"></i>
                            </div>
                            <div>
                                <h4 class="text-base font-bold text-zinc-900 group-hover:text-zinc-900 transition-colors uppercase leading-tight">{{ $jadwal->judul_modul }}</h4>
                                <p class="text-[11px] font-medium text-zinc-400 mt-1 uppercase tracking-tight">
                                    {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('l, d F Y') }} • {{ substr($jadwal->waktu_mulai, 0, 5) }} WIB
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-6">
                            <div class="text-right hidden sm:block">
                                <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Presensi Valid</p>
                                <div class="flex items-center gap-2 justify-end">
                                    <span class="text-lg font-black text-zinc-900">{{ $jadwal->presensis->count() }}</span>
                                    <span class="text-[9px] font-bold text-zinc-300 uppercase">Orang</span>
                                </div>
                            </div>
                            <div class="w-8 h-8 rounded-lg bg-zinc-50 flex items-center justify-center text-zinc-300 group-hover:translate-x-1 group-hover:text-zinc-900 transition-all border border-zinc-100">
                                <i class="fas fa-arrow-right text-[10px]"></i>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="flex flex-col items-center justify-center py-24 text-zinc-400">
                        <div class="h-20 w-20 rounded-full bg-zinc-50 flex items-center justify-center mb-4 border border-zinc-100 shadow-inner">
                            <i class="fas fa-calendar-alt text-3xl opacity-20"></i>
                        </div>
                        <h3 class="text-sm font-black uppercase tracking-[0.2em] text-zinc-400">Jadwal Kosong</h3>
                        <p class="text-[10px] italic mt-1 font-medium tracking-tight">Belum ada jadwal pelaksanaan untuk praktikum ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
