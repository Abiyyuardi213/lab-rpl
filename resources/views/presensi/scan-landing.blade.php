@extends('layouts.guest_presensi')

@section('title', 'Sesi Praktikum Terdeteksi')

@section('content')
<div class="min-h-[80vh] flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-[2.5rem] shadow-2xl border border-slate-100 relative overflow-hidden group">
        <!-- Decorative Background -->
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-32 h-32 bg-[#001f3f]/5 rounded-full blur-3xl transform group-hover:scale-150 transition-transform duration-1000"></div>
        <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-32 h-32 bg-blue-500/5 rounded-full blur-3xl transform group-hover:scale-150 transition-transform duration-1000"></div>

        <div class="relative">
            <div class="text-center">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-[#001f3f] shadow-lg shadow-[#001f3f]/20 pulse-animation">
                    <i class="fas fa-qrcode text-3xl text-white"></i>
                </div>
                <h2 class="mt-8 text-3xl font-black text-slate-900 uppercase tracking-tighter">SESI TERDETEKSI</h2>
                <p class="mt-2 text-xs font-bold text-slate-400 uppercase tracking-[0.2em] italic">Lab RPL ITATS Attendance System</p>
            </div>

            <div class="mt-10 bg-slate-50 border border-slate-100 rounded-2xl p-6 space-y-1 text-center">
                <p class="text-[10px] font-black text-[#001f3f] uppercase tracking-widest leading-none mb-2">Mata Praktikum:</p>
                <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight">{{ $jadwal->praktikum->nama_praktikum }}</h3>
                <div class="w-12 h-1 bg-slate-200 mx-auto my-4 rounded-full"></div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Modul / Sesi:</p>
                <p class="text-sm font-bold text-slate-600 uppercase">{{ $jadwal->judul_modul }}</p>
                
                <div class="mt-6 flex flex-wrap items-center justify-center gap-3 text-[10px] font-bold text-slate-400 uppercase">
                    <span class="flex items-center gap-1.5 bg-white px-3 py-1.5 rounded-lg border border-slate-100 shadow-sm">
                        <i class="far fa-calendar-alt text-blue-500"></i>
                        {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d M Y') }}
                    </span>
                    <span class="flex items-center gap-1.5 bg-white px-3 py-1.5 rounded-lg border border-slate-100 shadow-sm">
                        <i class="far fa-clock text-blue-500"></i>
                        {{ substr($jadwal->waktu_mulai, 0, 5) }} WIB
                    </span>
                </div>
            </div>

            <div class="mt-10 space-y-4">
                <div class="flex items-center gap-3 bg-blue-50/50 p-4 rounded-2xl border border-blue-50 animate-in fade-in slide-in-from-bottom duration-500">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-info-circle text-blue-600 text-xs"></i>
                    </div>
                    <p class="text-[11px] font-bold text-blue-800 leading-normal uppercase tracking-tight">
                        Klik tombol di bawah untuk mengonfirmasi kehadiran Anda pada sistem.
                    </p>
                </div>

                <a href="{{ route('praktikan.presensi.scan-jadwal', $jadwal->token) }}" 
                   class="flex w-full items-center justify-center gap-3 rounded-2xl bg-[#001f3f] px-8 py-4 text-xs font-black text-white uppercase tracking-[0.2em] shadow-xl shadow-[#001f3f]/20 hover:bg-[#002d5a] focus:outline-none transition-all active:scale-95 group">
                    Konfirmasi Kehadiran
                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
                
                <div class="text-center">
                    <a href="{{ route('home') }}" class="text-[10px] font-bold text-slate-400 hover:text-slate-600 uppercase tracking-widest transition-colors">
                        Batal
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @keyframes pulse {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(0, 31, 63, 0.4); }
        70% { transform: scale(1.05); box-shadow: 0 0 0 15px rgba(0, 31, 63, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(0, 31, 63, 0); }
    }
    .pulse-animation {
        animation: pulse 2s infinite;
    }
</style>
@endpush
@endsection
