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
            <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-10">
                <div class="mb-10 text-center">
                    <h4 class="text-[#1a4fa0] font-bold tracking-widest uppercase text-xs">Bagan Organisasi</h4>
                    <p class="text-slate-500 text-sm mt-2">Struktur organisasi Laboratorium Rekayasa Perangkat Lunak.</p>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white p-3 sm:p-6 shadow-sm">
                    <img src="{{ asset('image/struktur.jpg') }}" alt="Struktur Organisasi Laboratorium RPL"
                        class="mx-auto h-auto w-full min-w-[760px] max-w-6xl object-contain">
                </div>
            </div>
        </section>

        {{-- Culture Section --}}
        <section class="py-24 max-w-7xl mx-auto px-6">
            <div
                class="bg-[#1a4fa0] rounded-[3rem] p-12 md:p-20 text-white relative overflow-hidden shadow-2xl shadow-blue-900/10">
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
