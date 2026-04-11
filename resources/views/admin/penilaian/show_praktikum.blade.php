@extends('layouts.admin')

@section('title', 'Pilih Jadwal Modul')

@section('content')
    <div class="space-y-8">
        <!-- Breadcrumbs -->
        <div class="flex items-center gap-3 text-[10px] font-black uppercase tracking-widest text-slate-400">
            <a href="{{ route('admin.penilaian.index') }}" class="hover:text-slate-900 transition-colors">Pusat Penilaian</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <span class="text-slate-900">{{ $praktikum->nama_praktikum }}</span>
        </div>

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h1 class="text-3xl font-black text-slate-900 uppercase tracking-tight">{{ $praktikum->nama_praktikum }}</h1>
                <p class="text-slate-500 font-medium">Pilih modul/jadwal untuk melihat daftar presensi dan memberikan penilaian.</p>
            </div>
        </div>

        <!-- Jadwal List -->
        <div class="grid grid-cols-1 gap-4">
            @forelse($praktikum->jadwals as $jadwal)
                <a href="{{ route('admin.penilaian.jadwal', $jadwal->id) }}" 
                   class="group bg-white p-6 rounded-3xl border border-slate-200 hover:border-slate-800 hover:shadow-xl transition-all flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-6">
                        <div class="w-14 h-14 rounded-2xl bg-slate-50 text-slate-400 group-hover:bg-slate-900 group-hover:text-white flex items-center justify-center transition-all">
                            <i class="fas fa-calendar-alt text-xl"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">
                                Modul {{ $loop->remaining + 1 }} • {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d F Y') }}
                            </p>
                            <h3 class="text-xl font-black text-slate-900 group-hover:text-slate-900 transition-colors uppercase leading-none">
                                {{ $jadwal->judul_modul }}
                            </h3>
                        </div>
                    </div>

                    <div class="flex items-center gap-6">
                        <div class="text-right">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Presensi</p>
                            <div class="flex items-center gap-2 justify-end">
                                <span class="text-xl font-black text-slate-900">{{ $jadwal->presensis_count }}</span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Siswa</span>
                            </div>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-300 group-hover:translate-x-1 transition-transform">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </a>
            @empty
                <div class="py-20 text-center bg-white rounded-3xl border border-dashed border-slate-300">
                    <i class="fas fa-calendar-times text-4xl text-slate-200 mb-4"></i>
                    <p class="text-slate-400 font-bold uppercase tracking-widest text-xs">Belum Ada Jadwal Modul</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
