@extends('layouts.app')

@section('title', '503 - Sistem Sedang Maintenance')

@section('content')
    <div class="max-w-screen-xl mx-auto px-6 py-24 md:py-32 flex flex-col items-center justify-center min-h-[70vh]">
        {{-- Icon Section --}}
        <div class="mb-12">
            <div class="relative">
                <div class="absolute inset-0 bg-blue-100 rounded-[2.5rem] blur-2xl opacity-50 scale-150"></div>
                <div class="w-24 h-24 md:w-32 md:h-32 bg-white border border-blue-50 rounded-[2.5rem] flex items-center justify-center shadow-2xl shadow-blue-900/5 relative">
                    <i class="fas fa-tools text-4xl md:text-5xl text-[#1a4fa0]"></i>
                </div>
            </div>
        </div>

        {{-- Text Section --}}
        <div class="text-center space-y-6 max-w-2xl">
            <div class="space-y-4">
                <h1 class="text-6xl md:text-8xl font-black text-blue-50 leading-none select-none">503</h1>
                <h2 class="text-3xl md:text-5xl font-extrabold text-slate-900 tracking-tight">
                    Sistem Sedang <span class="text-[#1a4fa0]">Pemeliharaan</span>
                </h2>
            </div>
            
            <p class="text-lg text-slate-500 font-medium leading-relaxed max-w-lg mx-auto">
                Layanan Lab RPL sedang diperbarui untuk meningkatkan kualitas praktikum. Kami akan kembali online dalam beberapa saat lagi. Terima kasih atas kesabaran Anda.
            </p>
        </div>

        {{-- Meta Info --}}
        <div class="mt-20 flex flex-col items-center gap-6">
            <div class="flex gap-4 opacity-50">
                <div class="px-4 py-2 bg-slate-100 rounded-lg text-[10px] font-bold text-slate-400 uppercase tracking-widest border border-slate-200">
                    Error Code: 503
                </div>
                <div class="px-4 py-2 bg-slate-100 rounded-lg text-[10px] font-bold text-slate-400 uppercase tracking-widest border border-slate-200">
                    Status: Service Unavailable
                </div>
            </div>
            
            <p class="text-xs text-slate-400 italic">"Membangun Masa Depan Inovasi Teknik Informatika ITATS"</p>
        </div>
    </div>
@endsection
