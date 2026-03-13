@extends('layouts.app')

@section('title', '403 - Akses Ditolak')

@section('content')
    <div class="max-w-screen-xl mx-auto px-6 py-24 md:py-32 flex flex-col items-center justify-center min-h-[70vh]">
        {{-- Icon Section --}}
        <div class="mb-12">
            <div class="relative">
                <div class="absolute inset-0 bg-amber-100 rounded-[2.5rem] blur-2xl opacity-50 scale-150"></div>
                <div class="w-24 h-24 md:w-32 md:h-32 bg-white border border-amber-50 rounded-[2.5rem] flex items-center justify-center shadow-2xl shadow-amber-900/5 relative">
                    <i class="fas fa-user-shield text-4xl md:text-5xl text-amber-500"></i>
                </div>
            </div>
        </div>

        {{-- Text Section --}}
        <div class="text-center space-y-6 max-w-2xl">
            <div class="space-y-4">
                <h1 class="text-6xl md:text-8xl font-black text-amber-50 leading-none select-none">403</h1>
                <h2 class="text-3xl md:text-5xl font-extrabold text-slate-900 tracking-tight">
                    Akses <span class="text-amber-500">Dibatasi</span>
                </h2>
            </div>
            
            <p class="text-lg text-slate-500 font-medium leading-relaxed max-w-lg mx-auto">
                Maaf, Anda tidak memiliki izin untuk mengakses halaman ini. Silakan hubungi admin laboratorium jika Anda merasa ini adalah kesalahan.
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
                Error Code: 403
            </div>
            <div class="px-4 py-2 bg-slate-100 rounded-lg text-[10px] font-bold text-slate-400 uppercase tracking-widest border border-slate-200">
                Status: Forbidden
            </div>
        </div>
    </div>
@endsection
