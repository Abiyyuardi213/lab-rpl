@extends('layouts.admin')

@section('title', 'QR Code Presensi: ' . $jadwal->judul_modul)

@section('content')
<div class="max-w-4xl mx-auto space-y-8 py-8 px-4">
    <!-- Header -->
    <div class="text-center space-y-4">
        <h1 class="text-3xl font-black text-slate-900 uppercase tracking-tighter sm:text-4xl">
            SCAN PRESENSI <br class="sm:hidden"> {{ $jadwal->judul_modul }}
        </h1>
        <div class="flex flex-col items-center gap-2">
            <span class="bg-[#001f3f] text-white text-[10px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest shadow-lg shadow-[#001f3f]/20">
                {{ $jadwal->praktikum->nama_praktikum }}
            </span>
            <p class="text-slate-500 font-bold uppercase tracking-widest text-[11px]">
                {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('l, d F Y') }}
                <span class="mx-2">|</span>
                {{ substr($jadwal->waktu_mulai, 0, 5) }} - {{ substr($jadwal->waktu_selesai, 0, 5) }} WIB
            </p>
        </div>
    </div>

    <!-- QR Code Section -->
    <div class="flex flex-col items-center">
        <div class="relative p-8 bg-white rounded-[2.5rem] shadow-2xl border border-slate-100 group transition-all hover:scale-[1.02] duration-500">
            <!-- Decorative Corners -->
            <div class="absolute top-0 left-0 w-12 h-12 border-t-4 border-l-4 border-[#001f3f] rounded-tl-[2.5rem]"></div>
            <div class="absolute top-0 right-0 w-12 h-12 border-t-4 border-r-4 border-[#001f3f] rounded-tr-[2.5rem]"></div>
            <div class="absolute bottom-0 left-0 w-12 h-12 border-b-4 border-l-4 border-[#001f3f] rounded-bl-[2.5rem]"></div>
            <div class="absolute bottom-0 right-0 w-12 h-12 border-b-4 border-r-4 border-[#001f3f] rounded-br-[2.5rem]"></div>
            
            <div class="bg-white p-2 rounded-2xl border border-slate-50">
                {!! $qrCode !!}
            </div>
            
            <div class="absolute -bottom-6 left-1/2 -translate-x-1/2 bg-white px-6 py-2 rounded-full shadow-lg border border-slate-100 whitespace-nowrap">
                <p class="text-xs font-black text-[#001f3f] uppercase tracking-[0.2em] flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Scan kode ini untuk presensi
                </p>
            </div>
        </div>
    </div>

    <!-- Instructions & Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-12 bg-slate-50 p-6 rounded-3xl border border-slate-200 border-dashed print:hidden">
        <div class="space-y-4">
            <h3 class="font-black text-slate-900 uppercase tracking-tight flex items-center gap-2">
                <i class="fas fa-info-circle text-[#001f3f]"></i>
                Instruksi Praktikan
            </h3>
            <ul class="space-y-2 text-xs font-bold text-slate-600 uppercase tracking-wide leading-relaxed">
                <li class="flex gap-3">
                    <span class="w-5 h-5 rounded-full bg-[#001f3f] text-white flex items-center justify-center text-[10px] flex-shrink-0">1</span>
                    Buka Aplikasi Lab-RPL dan Login
                </li>
                <li class="flex gap-3">
                    <span class="w-5 h-5 rounded-full bg-[#001f3f] text-white flex items-center justify-center text-[10px] flex-shrink-0">2</span>
                    Gunakan scanner pada ponsel Anda
                </li>
                <li class="flex gap-3">
                    <span class="w-5 h-5 rounded-full bg-[#001f3f] text-white flex items-center justify-center text-[10px] flex-shrink-0">3</span>
                    Arahkan ke QR Code di atas
                </li>
                <li class="flex gap-3">
                    <span class="w-5 h-5 rounded-full bg-[#001f3f] text-white flex items-center justify-center text-[10px] flex-shrink-0">4</span>
                    Presensi akan tercatat secara otomatis
                </li>
            </ul>
        </div>
        <div class="flex flex-col justify-center gap-3">
            <a href="{{ route('presensi.download-jadwal-pdf', $jadwal->id) }}" 
               class="w-full h-12 bg-emerald-600 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-emerald-700 transition-all flex items-center justify-center gap-3 shadow-lg shadow-emerald-600/10 translate-y-0 active:translate-y-1">
                <i class="fas fa-file-pdf"></i>
                Download PDF QR
            </a>
            <button onclick="window.print()" class="w-full h-12 bg-[#001f3f] text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-[#002d5a] transition-all flex items-center justify-center gap-3 shadow-lg shadow-[#001f3f]/10 translate-y-0 active:translate-y-1">
                <i class="fas fa-print"></i>
                Cetak Langsung
            </button>
            <a href="{{ route('aslab.dashboard') }}" class="w-full h-12 bg-white border border-slate-200 text-slate-600 text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-slate-100 transition-all flex items-center justify-center gap-3 translate-y-0 active:translate-y-1">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>

<style>
@media print {
    nav, aside, footer, header, .print-hidden, button, a {
        display: none !important;
    }
    body {
        background: white !important;
    }
    .min-h-screen {
        min-height: auto !important;
    }
    .shadow-2xl, .shadow-lg {
        shadow: none !important;
    }
}
</style>
@endsection
