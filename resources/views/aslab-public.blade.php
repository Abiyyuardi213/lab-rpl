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
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
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

@section('scripts')
    <script>
        function showAslabDetail(el) {
            const data = JSON.parse(el.getAttribute('data-aslab'));
            Swal.fire({
                html: `
                        <div class="text-left p-2">
                            <div class="flex flex-col md:flex-row gap-8 items-center md:items-start">
                                <div class="shrink-0 relative group">
                                    <div class="absolute -inset-1 bg-gradient-to-tr from-[#1a4fa0] to-blue-300 rounded-[2rem] blur opacity-25"></div>
                                    <img src="${data.foto}" 
                                         onerror="this.src='https://ui-avatars.com/api/?name='+encodeURIComponent(data.name)+'&background=1a4fa0&color=fff&size=200'"
                                         alt="${data.name}" 
                                         class="relative w-40 h-40 object-cover rounded-[2rem] border-4 border-white shadow-xl">
                                </div>
                                <div class="flex-1 text-center md:text-left pt-2">
                                    <h3 class="text-2xl font-black text-slate-900 leading-tight">${data.name}</h3>
                                    <p class="text-[#1a4fa0] font-bold uppercase tracking-wider text-xs mb-6 mt-1">${data.jabatan}</p>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="space-y-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-[#1a4fa0]">
                                                    <i class="fas fa-id-badge text-sm"></i>
                                                </div>
                                                <div>
                                                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-widest leading-none mb-1">NPM</p>
                                                    <p class="text-sm font-bold text-slate-700">${data.npm}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-[#1a4fa0]">
                                                    <i class="fas fa-graduation-cap text-sm"></i>
                                                </div>
                                                <div>
                                                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-widest leading-none mb-1">Jurusan</p>
                                                    <p class="text-sm font-bold text-slate-700">${data.jurusan}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="space-y-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-[#1a4fa0]">
                                                    <i class="fas fa-calendar-check text-sm"></i>
                                                </div>
                                                <div>
                                                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-widest leading-none mb-1">Angkatan</p>
                                                    <p class="text-sm font-bold text-slate-700">${data.angkatan}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-[#1a4fa0]">
                                                    <i class="fas fa-envelope text-sm"></i>
                                                </div>
                                                <div>
                                                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-widest leading-none mb-1">Email</p>
                                                    <p class="text-sm font-bold text-slate-700 truncate w-40" title="${data.email}">${data.email}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-8 pt-6 border-t border-slate-100 flex items-center gap-4">
                                        <a href="https://wa.me/${data.no_hp.replace(/\D/g,'')}" target="_blank" class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-[#1a4fa0] transition-colors shadow-lg">
                                            <i class="fab fa-whatsapp"></i> Hubungi Aslab
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `,
                width: 'auto',
                maxWidth: '100%',
                padding: '1.5rem',
                showConfirmButton: false,
                showCloseButton: true,
                customClass: {
                    popup: 'rounded-[2.5rem] overflow-hidden border-2 border-slate-50 shadow-2xl',
                    closeButton: 'hover:text-[#1a4fa0] transition-colors'
                }
            });
        }
    </script>
@endsection
@endsection
