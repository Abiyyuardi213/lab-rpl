@extends('layouts.welcome')

@section('title', 'Verifikasi QR Presensi')

@section('content')
<div class="min-h-screen flex items-center justify-center p-6 bg-slate-50">
    <div class="max-w-md w-full bg-white rounded-[2rem] shadow-2xl border border-slate-100 overflow-hidden">
        <div class="p-8 text-center space-y-6">
            @if($status === 'valid')
                <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto text-3xl animate-bounce">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="space-y-2">
                    <h1 class="text-2xl font-black text-slate-900 uppercase tracking-tight">{{ $message }}</h1>
                    <p class="text-slate-500 font-medium">QR Code ini resmi dari Sistem Informasi Lab RPL ITATS</p>
                </div>
                
                <div class="bg-slate-50 rounded-2xl p-6 text-left space-y-4 border border-slate-100">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Praktikan</p>
                        <p class="text-slate-900 font-bold">{{ $nama }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Mata Praktikum</p>
                        <p class="text-slate-900 font-bold">{{ $praktikum }}</p>
                    </div>
                </div>

                <p class="text-[11px] text-slate-400 leading-relaxed">
                    Silakan tunjukkan QR ini kepada Asisten Laboratorium yang bertugas untuk melakukan scan presensi.
                </p>
            @else
                <div class="w-20 h-20 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto text-3xl">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="space-y-2">
                    <h1 class="text-2xl font-black text-slate-900 uppercase tracking-tight">{{ $message }}</h1>
                    <p class="text-slate-500 font-medium">QR Code tidak ditemukan atau sudah melewati batas waktu.</p>
                </div>
            @endif

            <div class="pt-4">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-black text-emerald-600 uppercase tracking-widest hover:text-emerald-700 transition-colors">
                    <i class="fas fa-home"></i>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
        
        <div class="bg-slate-900 p-4 text-center">
            <p class="text-[10px] font-bold text-white/50 uppercase tracking-widest">Lab RPL ITATS &copy; {{ date('Y') }}</p>
        </div>
    </div>
</div>
@endsection
