@extends('layouts.app')

@section('content')
    <div class="bg-white min-h-screen">
        {{-- Hero Section --}}
        <section class="max-w-screen-2xl mx-auto px-6 md:px-10 pt-10 md:pt-16 pb-12 overflow-hidden">
            <div class="text-center max-w-3xl mx-auto flex flex-col items-center gap-6 relative z-10">
                <div
                    class="inline-flex w-fit items-center gap-2 rounded-full border border-slate-200 px-3 py-1 text-xs font-medium text-slate-500 mb-2">
                    <span class="h-2 w-2 rounded-full bg-orange-500 shadow-[0_0_8px_rgba(249,115,22,0.4)]"></span>
                    Profil Kepengurusan • Laboratorium
                </div>
                <h1
                    class="text-balance text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-[1.1] tracking-tight text-black">
                    Struktur <span class="text-[#1a4fa0]">Organisasi</span>
                </h1>
                <p class="text-lg text-slate-600 max-w-prose leading-relaxed">
                    Sinergi antara tenaga pendidik dan mahasiswa dalam mengelola operasional Laboratorium Rekayasa
                    Perangkat Lunak demi terciptanya lingkungan belajar yang inovatif.
                </p>
                <div class="w-16 h-1 bg-[#1a4fa0] mt-4 rounded-full"></div>
            </div>
        </section>

        {{-- Organization Chart Section --}}
        <section class="py-16 bg-slate-50 border-y border-slate-100">
            <div class="max-w-7xl mx-auto px-6">
                {{-- Kepala Laboratorium (Top Tier) --}}
                <div class="flex flex-col items-center mb-24">
                    <h4 class="text-[#1a4fa0] font-bold tracking-widest uppercase text-xs mb-10">Kepala Laboratorium</h4>
                    <div class="relative group">
                        {{-- Connector Line --}}
                        <div class="absolute top-full left-1/2 w-0.5 h-16 bg-slate-300 -translate-x-1/2 hidden md:block">
                        </div>

                        <div
                            class="relative w-72 bg-white rounded-[2.5rem] border border-slate-200 p-6 text-center shadow-xl hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 group-hover:border-[#1a4fa0]/30 overflow-hidden">
                            <div
                                class="absolute top-0 right-0 w-24 h-24 bg-[#1a4fa0]/5 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110">
                            </div>

                            <img src="{{ asset($kepalaLab['foto']) }}"
                                onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($kepalaLab['nama']) }}&background=1a4fa0&color=fff&size=200'"
                                alt="{{ $kepalaLab['nama'] }}"
                                class="w-32 h-32 rounded-3xl object-cover mx-auto mb-6 border-4 border-slate-50 shadow-md">

                            <h3 class="text-lg font-bold text-slate-900 leading-tight mb-1">{{ $kepalaLab['nama'] }}</h3>
                            <p class="text-[#1a4fa0] text-sm font-semibold mb-3">{{ $kepalaLab['jabatan'] }}</p>
                            <div
                                class="inline-flex items-center gap-2 bg-slate-50 px-3 py-1 rounded-full border border-slate-100">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">NIP.
                                    {{ $kepalaLab['nip'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Asisten Laboratorium (Second Tier) --}}
                <div class="mt-8 border-t border-slate-200 pt-16 relative">
                    {{-- Horizontal Connecting Line for Desktop --}}
                    <div class="absolute top-0 left-0 w-0.5 h-10 bg-slate-300 hidden md:block" style="left: 50%;"></div>

                    <div class="text-center mb-16">
                        <h4 class="text-[#1a4fa0] font-bold tracking-widest uppercase text-xs">Asisten Laboratorium</h4>
                        <p class="text-slate-500 text-sm mt-2">Dukungan teknis dan bimbingan praktikum mahasiswa.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                        @forelse($aslabs as $aslab)
                            <div
                                class="group relative bg-white rounded-3xl p-6 border border-slate-200 hover:border-[#1a4fa0]/30 hover:shadow-xl transition-all duration-500">
                                <div class="relative w-24 h-24 mx-auto mb-6">
                                    <div
                                        class="absolute inset-0 bg-[#1a4fa0]/10 rounded-2xl group-hover:scale-110 transition-transform">
                                    </div>
                                    <img src="{{ $aslab->user->profile_picture ? asset('storage/' . $aslab->user->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($aslab->user->name) . '&background=f1f5f9&color=64748b' }}"
                                        alt="{{ $aslab->user->name }}"
                                        class="relative w-full h-full rounded-2xl object-cover border-2 border-white shadow-sm">
                                </div>
                                <div class="text-center">
                                    <h5
                                        class="text-sm font-bold text-slate-900 leading-tight mb-1 group-hover:text-[#1a4fa0] transition-colors">
                                        {{ $aslab->user->name }}
                                    </h5>
                                    <p class="text-xs text-slate-500 mb-4">{{ $aslab->jurusan }} • {{ $aslab->angkatan }}
                                    </p>
                                    <div class="flex items-center justify-center gap-2">
                                        <span
                                            class="text-[9px] font-bold text-slate-400 border border-slate-100 px-2 py-0.5 rounded shadow-sm">
                                            NPM: {{ $aslab->npm }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-12 text-center text-slate-400">
                                <i class="fas fa-users-slash text-4xl mb-4 opacity-20"></i>
                                <p>Data asisten belum dipublikasikan.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        {{-- Culture Section --}}
        <section class="py-24 max-w-7xl mx-auto px-6">
            <div class="bg-[#1a4fa0] rounded-[3rem] p-12 md:p-20 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -mr-32 -mt-32 blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-400/10 rounded-full -ml-20 -mb-20 blur-2xl"></div>

                <div class="flex flex-col lg:flex-row gap-16 items-center relative z-10">
                    <div class="lg:w-1/2 text-left">
                        <h2 class="text-3xl md:text-4xl font-extrabold mb-8 italic">Membangun Budaya Kolaboratif</h2>
                        <div class="space-y-6 text-white/80 leading-relaxed text-lg">
                            <p>Di Laboratorium RPL, struktur organisasi bukan sekadar hierarki, melainkan sebuah tim kerja
                                yang saling mendukung.</p>
                            <p>Kami percaya bahwa inovasi lahir dari kolaborasi yang kuat antara pembimbing akademik dan
                                semangat belajar rekan-rekan mahasiswa.</p>
                        </div>
                    </div>
                    <div class="lg:w-1/2 grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div
                            class="bg-white/10 backdrop-blur-md p-6 rounded-2xl border border-white/10 hover:bg-white/20 transition-colors">
                            <i class="fas fa-check-circle text-2xl text-blue-300 mb-4"></i>
                            <h6 class="font-bold">Dedikasi Tinggi</h6>
                            <p class="text-xs text-white/60 mt-1 italic">Komitmen penuh pada edukasi.</p>
                        </div>
                        <div
                            class="bg-white/10 backdrop-blur-md p-6 rounded-2xl border border-white/10 hover:bg-white/20 transition-colors">
                            <i class="fas fa-heart text-2xl text-pink-300 mb-4"></i>
                            <h6 class="font-bold">Sharing Culture</h6>
                            <p class="text-xs text-white/60 mt-1 italic">Berbagi ilmu tanpa batas.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
