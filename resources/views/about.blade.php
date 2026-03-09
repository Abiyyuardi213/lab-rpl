@extends('layouts.app')

@section('content')
    <div class="bg-white min-h-screen">
        {{-- Hero Section --}}
        <section class="max-w-screen-2xl mx-auto px-6 md:px-10 pt-10 md:pt-16 pb-12 overflow-hidden">
            <div class="text-center max-w-4xl mx-auto flex flex-col items-center gap-6 relative z-10">
                <div
                    class="inline-flex w-fit items-center gap-2 rounded-full border border-slate-200 px-3 py-1 text-xs font-medium text-slate-500 mb-2">
                    <span class="h-2 w-2 rounded-full bg-orange-500 shadow-[0_0_8px_rgba(249,115,22,0.4)]"></span>
                    Profil Laboratorium • ITATS
                </div>
                <h1
                    class="text-balance text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-[1.1] tracking-tight text-black">
                    Mengenal <span class="text-[#1a4fa0]">Lab. Rekayasa Perangkat Lunak</span>
                </h1>
                <p class="text-lg text-slate-600 max-w-prose leading-relaxed">
                    Pusat keunggulan akademik yang berfokus pada riset desain sistem, pengembangan aplikasi modern, dan
                    implementasi metodologi perangkat lunak terkini. Kami berdedikasi untuk mencetak tenaga ahli
                    informatika yang kompeten.
                </p>
                <div class="flex flex-wrap items-center justify-center gap-4 mt-2">
                    <a href="#visi-misi"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-xl text-sm font-bold ring-offset-background transition-all hover:scale-105 bg-[#1a4fa0] text-white hover:bg-[#1a4fa0]/90 h-12 px-8 py-2 shadow-lg shadow-[#1a4fa0]/25">
                        Visi & Misi
                    </a>
                    <a href="{{ route('login.praktikan') }}"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-xl text-sm font-semibold ring-offset-background transition-all hover:scale-105 hover:bg-slate-100 h-12 px-8 py-2 border-2 border-slate-200 text-slate-700 bg-white">
                        Masuk Portal
                    </a>
                </div>
                <div class="w-16 h-1 bg-[#1a4fa0] mt-4 rounded-full"></div>
            </div>
        </section>

        {{-- Stats Grid --}}
        <section class="bg-slate-50 py-16 border-y border-slate-100">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                    <div
                        class="p-8 bg-white rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                        <div class="text-4xl font-black text-[#1a4fa0] mb-2">20+</div>
                        <div class="text-sm font-bold text-slate-400 uppercase tracking-widest">Penelitian/Project</div>
                    </div>
                    <div
                        class="p-8 bg-white rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                        <div class="text-4xl font-black text-[#1a4fa0] mb-2">5+</div>
                        <div class="text-sm font-bold text-slate-400 uppercase tracking-widest">Asisten Laboratorium</div>
                    </div>
                    <div
                        class="p-8 bg-white rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                        <div class="text-4xl font-black text-[#1a4fa0] mb-2">500+</div>
                        <div class="text-sm font-bold text-slate-400 uppercase tracking-widest">Alumni Praktikan</div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Visi & Misi --}}
        <section id="visi-misi" class="py-24 max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h4 class="text-[#1a4fa0] font-bold tracking-widest uppercase text-sm mb-4">Aspirasi & Tujuan</h4>
                <h2 class="text-4xl font-extrabold text-slate-900">Visi & Misi Laboratorium</h2>
                <div class="w-16 h-1 bg-[#1a4fa0] mx-auto mt-6"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                {{-- Visi --}}
                <div
                    class="relative group p-10 rounded-3xl border border-slate-200 bg-white hover:border-[#1a4fa0]/20 hover:shadow-2xl transition-all duration-500">
                    <div
                        class="w-14 h-14 rounded-2xl bg-blue-50 text-[#1a4fa0] flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                        <i class="fas fa-eye text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-4">Visi</h3>
                    <p class="text-slate-600 leading-relaxed text-lg italic">
                        "Menjadi pusat riset dan pembelajaran Rekayasa Perangkat Lunak yang inovatif, unggul, dan relevan
                        dengan kebutuhan industri teknologi global serta berkontribusi pada kemajuan masyarakat digital."
                    </p>
                </div>

                {{-- Misi --}}
                <div
                    class="relative group p-10 rounded-3xl border border-slate-200 bg-white hover:border-[#1a4fa0]/20 hover:shadow-2xl transition-all duration-500">
                    <div
                        class="w-14 h-14 rounded-2xl bg-indigo-50 text-[#1a4fa0] flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                        <i class="fas fa-bullseye text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-4">Misi</h3>
                    <ul class="space-y-4 text-slate-600">
                        <li class="flex gap-4">
                            <span
                                class="w-6 h-6 rounded-full bg-indigo-100 text-[#1a4fa0] text-xs flex-shrink-0 flex items-center justify-center font-bold mt-1">1</span>
                            <p>Menyelenggarakan kegiatan praktikum yang terstruktur dan berkualitas berbasis standar
                                industri.</p>
                        </li>
                        <li class="flex gap-4">
                            <span
                                class="w-6 h-6 rounded-full bg-indigo-100 text-[#1a4fa0] text-xs flex-shrink-0 flex items-center justify-center font-bold mt-1">2</span>
                            <p>Mendorong kolaborasi riset antara dosen dan mahasiswa dalam bidang pengembangan perangkat
                                lunak.</p>
                        </li>
                        <li class="flex gap-4">
                            <span
                                class="w-6 h-6 rounded-full bg-indigo-100 text-[#1a4fa0] text-xs flex-shrink-0 flex items-center justify-center font-bold mt-1">3</span>
                            <p>Membekali mahasiswa dengan kemampuan analisis, desain arsitektur, dan pengujian kualitas
                                sistem.</p>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        {{-- Fasilitas Section --}}
        <section class="bg-slate-900 py-24 text-white">
            <div class="max-w-7xl mx-auto px-6">
                <div class="flex flex-col lg:flex-row gap-16 items-center">
                    <div class="lg:w-1/2">
                        <h4 class="text-[#00c6ff] font-bold tracking-widest uppercase text-sm mb-4">Lingkungan Kerja</h4>
                        <h2 class="text-4xl font-extrabold mb-8 italic">Pusat Teknologi Terintegrasi</h2>
                        <div class="space-y-6 text-slate-400 leading-relaxed text-lg">
                            <p>Lab RPL ITATS menyediakan ruang kolaboratif yang dirancang untuk merangsang kreativitas dan
                                fokus dalam pengembangan kode program.</p>
                            <p>Dilengkapi dengan infrastruktur modern, workstation berperforma tinggi, dan akses ke berbagai
                                framework pengembangan terkini untuk mendukung eksplorasi teknologi tanpa batas.</p>
                        </div>
                        <div class="mt-12 flex items-center gap-6">
                            <div class="flex -space-x-4">
                                <div
                                    class="w-12 h-12 rounded-full border-4 border-slate-900 bg-white shadow-lg overflow-hidden flex items-center justify-center">
                                    <img src="{{ asset('image/logo-RPL.jpg') }}" class="w-8">
                                </div>
                                <div
                                    class="w-12 h-12 rounded-full border-4 border-slate-900 bg-slate-800 flex items-center justify-center text-xs font-bold text-white">
                                    ITATS</div>
                            </div>
                            <div class="text-sm font-medium text-slate-500">Membangun Sistem Hebat Sejak 2012</div>
                        </div>
                    </div>
                    <div class="lg:w-1/2 grid grid-cols-2 gap-4">
                        <div
                            class="aspect-square bg-slate-800 rounded-3xl p-8 flex flex-col justify-end hover:bg-[#1a4fa0]/20 transition-colors">
                            <i class="fas fa-server text-3xl text-blue-400 mb-4"></i>
                            <h5 class="font-bold">Server Deployment</h5>
                        </div>
                        <div
                            class="aspect-square bg-slate-800 rounded-3xl p-8 flex flex-col justify-end hover:bg-[#1a4fa0]/20 transition-colors mt-8">
                            <i class="fas fa-code-branch text-3xl text-indigo-400 mb-4"></i>
                            <h5 class="font-bold">DevSecOps Standard</h5>
                        </div>
                        <div
                            class="aspect-square bg-slate-800 rounded-3xl p-8 flex flex-col justify-end hover:bg-[#1a4fa0]/20 transition-colors -mt-8">
                            <i class="fas fa-laptop-code text-3xl text-amber-400 mb-4"></i>
                            <h5 class="font-bold">Modern Frameworks</h5>
                        </div>
                        <div
                            class="aspect-square bg-slate-800 rounded-3xl p-8 flex flex-col justify-end hover:bg-[#1a4fa0]/20 transition-colors">
                            <i class="fas fa-microchip text-3xl text-emerald-400 mb-4"></i>
                            <h5 class="font-bold">High-End Hardware</h5>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Contact CTA --}}
        <section class="py-20 bg-white">
            <div class="max-w-4xl mx-auto px-6 text-center space-y-8">
                <div
                    class="w-20 h-20 bg-blue-50 text-[#1a4fa0] rounded-full flex items-center justify-center mx-auto mb-4 animate-bounce">
                    <i class="fas fa-envelope-open-text text-3xl"></i>
                </div>
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">Ingin Tahu Lebih Banyak?</h2>
                <p class="text-slate-500 max-w-xl mx-auto text-lg leading-relaxed">
                    Kami selalu terbuka untuk pertanyaan, kolaborasi riset, atau kunjugan mahasiswa. Hubungi kami melalui
                    kanal resmi Laboratorium.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-6 pt-4">
                    <a href="https://instagram.com/hmif_itats" target="_blank"
                        class="group px-8 py-3 rounded-xl bg-slate-900 text-white font-bold hover:bg-black transition-all shadow-xl hover:-translate-y-1 flex items-center gap-2">
                        <i class="fab fa-instagram text-xl text-pink-500"></i>
                        Instagram HMIF
                    </a>
                    <a href="mailto:labrpl.itats@gmail.com"
                        class="px-8 py-3 rounded-xl bg-white text-slate-900 border-2 border-slate-200 font-bold hover:bg-slate-50 transition-all shadow-sm hover:-translate-y-1 flex items-center gap-2">
                        <i class="fas fa-envelope text-xl text-blue-500"></i>
                        Email Lab RPL
                    </a>
                </div>
            </div>
        </section>
    </div>
@endsection
