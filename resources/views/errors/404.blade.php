@extends('layouts.app')

@section('title', '404 - Halaman Tidak Ditemukan')

@section('content')
    <div class="max-w-screen-xl mx-auto px-6 py-24 md:py-32 flex flex-col items-center justify-center min-h-[70vh]">
        {{-- Icon Section --}}
        <div class="mb-12">
            <div class="w-24 h-24 md:w-32 md:h-32 bg-slate-50 border border-slate-100 rounded-[2.5rem] flex items-center justify-center shadow-2xl shadow-blue-900/5 rotate-3">
                <i class="fas fa-search text-4xl md:text-5xl text-[#1a4fa0]"></i>
            </div>
        </div>

        {{-- Text Section --}}
        <div class="text-center space-y-6 max-w-2xl">
            <div class="space-y-2">
                <h1 class="text-6xl md:text-8xl font-black text-slate-100 leading-none select-none">404</h1>
                <h2 class="text-3xl md:text-5xl font-extrabold text-slate-900 tracking-tight">
                    Halaman Tidak <span class="text-[#1a4fa0]">Ditemukan</span>
                </h2>
            </div>
            
            <p class="text-lg text-slate-500 font-medium leading-relaxed max-w-lg mx-auto">
                Maaf, halaman yang Anda cari tidak tersedia. Mungkin sudah dipindahkan atau sedang dalam pemeliharaan.
            </p>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-12 flex flex-wrap items-center justify-center gap-4">
            <a href="{{ url('/') }}"
                class="inline-flex items-center justify-center rounded-xl bg-[#1a4fa0] text-white px-10 h-14 text-sm font-black uppercase tracking-widest transition-all hover:scale-105 active:scale-95 shadow-xl shadow-blue-900/20">
                <i class="fas fa-home mr-2"></i> Kembali ke Beranda
            </a>
            <button onclick="window.history.back()"
                class="inline-flex items-center justify-center rounded-xl bg-white border-2 border-slate-200 text-slate-700 px-10 h-14 text-sm font-bold transition-all hover:bg-slate-50 hover:scale-105 active:scale-95">
                <i class="fas fa-arrow-left mr-2"></i> Sebelumnya
            </button>
        </div>

        {{-- Meta Info --}}
        <div class="mt-20 flex gap-4 opacity-50">
            <div class="px-4 py-2 bg-slate-100 rounded-lg text-[10px] font-bold text-slate-400 uppercase tracking-widest border border-slate-200">
                Error Code: 404
            </div>
            <div class="px-4 py-2 bg-slate-100 rounded-lg text-[10px] font-bold text-slate-400 uppercase tracking-widest border border-slate-200">
                Status: Not Found
            </div>
        </div>
    </div>
@endsection
