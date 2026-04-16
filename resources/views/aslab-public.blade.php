@extends('layouts.app')

@section('content')
    <div class="bg-white min-h-screen">
        {{-- Hero Section --}}
        <section class="max-w-screen-2xl mx-auto px-6 md:px-10 pt-10 md:pt-16 pb-12 overflow-hidden">
            <div class="text-center max-w-3xl mx-auto flex flex-col items-center gap-6 relative z-10">
                <div
                    class="inline-flex w-fit items-center gap-2 rounded-full border border-slate-200 px-3 py-1 text-xs font-medium text-slate-500 mb-2">
                    <span class="h-2 w-2 rounded-full bg-[#1a4fa0] shadow-[0_0_8px_rgba(26,79,160,0.4)]"></span>
                    Profil Asisten • Laboratorium
                </div>
                <h1
                    class="text-balance text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-[1.1] tracking-tight text-black">
                    Asisten <span class="text-[#1a4fa0]">Laboratorium</span>
                </h1>
                <p class="text-lg text-slate-600 max-w-prose leading-relaxed">
                    Kenali tim asisten laboratorium yang akan mendampingi dan membimbing anda selama kegiatan praktikum di Laboratorium Rekayasa Perangkat Lunak.
                </p>
                <div class="w-16 h-1 bg-[#1a4fa0] mt-4 rounded-full"></div>
            </div>
        </section>

        {{-- Aslab List Section --}}
        <section class="py-16 bg-slate-50 border-y border-slate-100">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-16">
                    <h4 class="text-[#1a4fa0] font-bold tracking-widest uppercase text-xs">Daftar Asisten Aktif</h4>
                    <p class="text-slate-500 text-sm mt-2">Gunakan informasi ini untuk menghubungi pembimbing akademik anda</p>
                </div>

                @if ($aslabs->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                        @foreach ($aslabs as $aslab)
                            @include('partials.aslab-card', ['aslab' => $aslab])
                        @endforeach
                    </div>
                @else
                    <div class="py-12 text-center text-slate-400">
                        <i class="fas fa-users-slash text-4xl mb-4 opacity-20"></i>
                        <p>Data asisten belum tersedia atau belum dipublikasikan.</p>
                    </div>
                @endif
            </div>
        </section>

        {{-- Support Section --}}
        <section class="py-24 max-w-7xl mx-auto px-6">
            <div
                class="bg-[#1a4fa0] rounded-[3rem] p-12 md:p-20 text-white relative overflow-hidden shadow-2xl shadow-blue-900/10">
                <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -mr-32 -mt-32 blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-400/10 rounded-full -ml-20 -mb-20 blur-2xl"></div>

                <div class="flex flex-col lg:flex-row gap-16 items-center relative z-10">
                    <div class="lg:w-1/2 text-left">
                        <h2 class="text-3xl md:text-4xl font-extrabold mb-8 italic">Butuh Bantuan Lebih Lanjut?</h2>
                        <div class="space-y-6 text-white/80 leading-relaxed text-lg">
                            <p>Jika anda memiliki kendala terkait praktikum atau membutuhkan informasi spesifik mengenai bimbingan, jangan ragu untuk menghubungi asisten yang bersangkutan.</p>
                            <p>Asisten laboratorium kami siap membantu anda mencapai kompetensi terbaik dalam perkuliahan praktikum.</p>
                        </div>
                    </div>
                    <div class="lg:w-1/2 grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div
                            class="bg-white/10 backdrop-blur-md p-6 rounded-2xl border border-white/10 hover:bg-white/20 transition-colors">
                            <i class="fas fa-comments text-2xl text-blue-300 mb-4"></i>
                            <h6 class="font-bold">Komunikasi Aktif</h6>
                            <p class="text-xs text-white/60 mt-1 italic">Koordinasi lancar bersama aslab.</p>
                        </div>
                        <div
                            class="bg-white/10 backdrop-blur-md p-6 rounded-2xl border border-white/10 hover:bg-white/20 transition-colors">
                            <i class="fas fa-user-graduate text-2xl text-emerald-300 mb-4"></i>
                            <h6 class="font-bold">Bimbingan Intensif</h6>
                            <p class="text-xs text-white/60 mt-1 italic">Tingkatkan pemahaman teknis anda.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection

