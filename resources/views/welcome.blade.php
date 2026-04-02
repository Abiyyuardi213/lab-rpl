@extends('layouts.app')
@section('title', 'Beranda | Lab RPL ITATS — Rekayasa Perangkat Lunak')
@section('meta_description', 'Beranda resmi Laboratorium Rekayasa Perangkat Lunak (Lab RPL) ITATS. Akses pendaftaran praktikum, asisten lab, dan dokumentasi kegiatan Teknik Informatika ITATS.')
@section('meta')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@graph": [
    {
      "@@type": "WebSite",
      "@@id": "{{ url('/') }}/#website",
      "url": "{{ url('/') }}",
      "name": "Lab RPL ITATS",
      "description": "Laboratorium Rekayasa Perangkat Lunak Teknik Informatika ITATS",
      "publisher": {
        "@@id": "{{ url('/') }}/#organization"
      },
      "inLanguage": "id"
    },
    {
      "@@type": "EducationalOrganization",
      "@@id": "{{ url('/') }}/#organization",
      "name": "Lab Rekayasa Perangkat Lunak ITATS",
      "url": "{{ url('/') }}",
      "logo": {
        "@@type": "ImageObject",
        "url": "{{ asset('image/logo-RPL.png') }}"
      },
      "address": {
        "@@type": "PostalAddress",
        "streetAddress": "Jl. Arief Rahman Hakim No.100",
        "addressLocality": "Surabaya",
        "addressRegion": "Jawa Timur",
        "postalCode": "60117",
        "addressCountry": "ID"
      },
      "sameAs": [
        "https://www.instagram.com/hmif_itats/"
      ]
    }
  ]
}
</script>
@endsection
@section('content')
    {{-- Hero Bento --}}
    <section class="max-w-screen-2xl mx-auto px-6 md:px-10 pt-10 md:pt-16">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            {{-- Left: Headline + CTA --}}
            <div class="flex flex-col justify-center gap-6">
                <div
                    class="inline-flex w-fit items-center gap-2 rounded-full border border-slate-200 px-3 py-1 text-xs font-medium text-slate-500 mb-4">
                    <span class="h-2 w-2 rounded-full bg-orange-500 shadow-[0_0_8px_rgba(249,115,22,0.4)]"></span>
                    Inovatif • Terstandarisasi • Profesional
                </div>
                <h1 class="text-balance text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-[1.1] tracking-tight">
                    <span class="text-black">Lab. Rekayasa</span> <br>
                    <span class="text-[#1a4fa0]">Perangkat Lunak</span>
                </h1>
                <p class="text-lg text-slate-600 max-w-prose leading-relaxed">
                    Laboratorium yang mengampu praktikum Pemrograman Terstruktur, Struktur Data, dan Basis Data untuk
                    mencetak praktikan yang kompeten dalam pengembangan perangkat lunak di ITATS.
                </p>
                <div class="flex flex-wrap items-center gap-4 mt-2">
                    <a href="{{ route('login.praktikan') }}"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-xl text-sm font-bold ring-offset-background transition-all hover:scale-105 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-[#1a4fa0] text-white hover:bg-[#1a4fa0]/90 h-12 px-8 py-2 shadow-lg shadow-[#1a4fa0]/25">
                        <i class="fas fa-sign-in-alt mr-2"></i> Masuk Portal
                    </a>
                    <a href="#tentang"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-xl text-sm font-semibold ring-offset-background transition-all hover:scale-105 hover:bg-slate-100 h-12 px-8 py-2 border-2 border-slate-200 text-slate-700 bg-white">
                        Pelajari Lebih Lanjut
                    </a>
                </div>
            </div>
            {{-- Right: Bento Grid Stats & Highlights --}}
            <div class="grid grid-cols-2 gap-4">
                {{-- Stats Card --}}
                <div
                    class="col-span-2 rounded-2xl border border-slate-200 p-6 bg-gradient-to-br from-white to-slate-50 shadow-sm relative overflow-hidden group">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110">
                    </div>
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-6">Statistik Laboratorium</h3>
                    <div class="grid grid-cols-3 gap-4 relative z-10">
                        <div class="text-center">
                            <div class="text-3xl font-black text-slate-900">{{ $stats['praktikum'] ?? 0 }}</div>
                            <div class="text-[10px] font-bold text-slate-500 uppercase mt-1">Praktikum</div>
                        </div>
                        <div class="text-center border-x border-slate-200">
                            <div class="text-3xl font-black text-slate-900">{{ $stats['aslab'] ?? 0 }}</div>
                            <div class="text-[10px] font-bold text-slate-500 uppercase mt-1">Aslab</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-black text-slate-900">{{ $stats['praktikan'] ?? 0 }}</div>
                            <div class="text-[10px] font-bold text-slate-500 uppercase mt-1">Praktikan</div>
                        </div>
                    </div>
                </div>
                {{-- Activity Card --}}
                <div
                    class="rounded-2xl border border-slate-200 p-6 bg-white hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center mb-4">
                        <i class="fas fa-calendar-alt text-amber-600"></i>
                    </div>
                    <h3 class="font-bold text-slate-900">Agenda Sesi</h3>
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed italic">
                        "Terus pantau jadwal praktikum terbaru di portal."
                    </p>
                </div>
                {{-- Info Card --}}
                <div
                    class="rounded-2xl border border-slate-200 p-6 bg-white hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center mb-4">
                        <i class="fas fa-info-circle text-blue-600"></i>
                    </div>
                    <h3 class="font-bold text-slate-900">Info Terbaru</h3>
                    <div class="text-xs text-slate-600 mt-2 line-clamp-2">
                        @if ($latestPraktikum)
                            Pendaftaran <strong>{{ $latestPraktikum->nama_praktikum }}</strong> sedang berjalan.
                        @else
                            Periksa pengumuman untuk info pendaftaran praktikum.
                        @endif
                    </div>
                </div>
            </div>
        </div>
        {{-- Hero Visual Image --}}
        <div class="mt-12 rounded-3xl overflow-hidden border-8 border-white shadow-2xl relative group">
            <div
                class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent z-10 opacity-60 group-hover:opacity-40 transition-opacity">
            </div>
            <img src="{{ asset('image/praktikum1.jpg') }}" alt="Lab RPL ITATS Visual"
                class="w-full h-[300px] md:h-[450px] object-cover transition-transform duration-700 group-hover:scale-105">
            <div class="absolute bottom-10 left-10 z-20">
                <h4 class="text-white text-2xl font-bold">Lab RPL ITATS</h4>
                <p class="text-white/80 text-sm">Laboratorium untuk Masa Depan Digital Anda.</p>
            </div>
        </div>
    </section>
    {{-- Services/Quick Access --}}
    <section id="layanan" class="max-w-screen-2xl mx-auto px-6 md:px-10 mt-20 mb-20">
        <div class="flex flex-col md:flex-row justify-between items-end mb-10 gap-4 text-center md:text-left">
            <div>
                <h2 class="text-3xl font-extrabold text-slate-900">Akses Cepat</h2>
                <p class="text-slate-500 mt-2">Layanan utama Sistem Informasi Laboratorium RPL.</p>
            </div>
            <a href="{{ route('login.praktikan') }}" class="text-primary font-bold hover:underline flex items-center gap-2">
                Masuk ke Dashboard <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $services = [
                    [
                        'icon' => 'fa-book-open',
                        'color' => 'bg-indigo-500',
                        'title' => 'E-modul Praktikum',
                        'desc' => 'Unduh materi dan modul praktikum secara digital.',
                        'link' => '#',
                    ],
                    [
                        'icon' => 'fa-edit',
                        'color' => 'bg-emerald-500',
                        'title' => 'Pendaftaran MBKM',
                        'desc' => 'Daftar konversi praktikum jalur MBKM/Project.',
                        'link' => '#',
                    ],
                    [
                        'icon' => 'fa-users',
                        'color' => 'bg-amber-500',
                        'title' => 'Data Aslab',
                        'desc' => 'Kenali asisten laboratorium yang membimbing anda.',
                        'link' => route('aslab.public'),
                    ],
                    [
                        'icon' => 'fa-bullhorn',
                        'color' => 'bg-rose-500',
                        'title' => 'Pengumuman',
                        'desc' => 'Informasi rilis jadwal dan nilai praktikum.',
                        'link' => route('pengumuman.public'),
                    ],
                ];
            @endphp
            @foreach ($services as $s)
                <a href="{{ $s['link'] }}"
                    class="group relative rounded-2xl border border-slate-100 p-8 bg-white shadow-sm hover:shadow-xl hover:border-primary/20 transition-all duration-300">
                    <div
                        class="w-12 h-12 rounded-xl {{ $s['color'] }} text-white flex items-center justify-center mb-6 shadow-lg shadow-inherit/20 group-hover:scale-110 transition-transform">
                        <i class="fas {{ $s['icon'] }} text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">{{ $s['title'] }}</h3>
                    <p class="text-sm text-slate-500 mt-3 leading-relaxed">{{ $s['desc'] }}</p>
                    <div class="w-12 h-1 bg-slate-100 mt-6 group-hover:w-full group-hover:bg-primary transition-all"></div>
                </a>
            @endforeach
        </div>
    </section>
    {{-- Latest Activities --}}
    @if ($latestKegiatans->count() > 0)
        <section class="max-w-screen-2xl mx-auto px-6 md:px-10 py-24 border-t border-slate-100">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
                <div class="max-w-xl">
                    <h4 class="text-blue-600 font-black tracking-[0.3em] uppercase text-xs mb-4">Update Lab</h4>
                    <h2 class="text-3xl md:text-5xl font-extrabold text-slate-900 leading-[1.1] tracking-tight">Dokumentasi
                        <br> <span class="text-[#1a4fa0]">Kegiatan Terkini</span></h2>
                </div>
                <a href="{{ route('kegiatan.public') }}"
                    class="group flex items-center gap-3 bg-slate-50 hover:bg-[#1a4fa0] px-6 py-3 rounded-2xl transition-all duration-300">
                    <span class="text-sm font-bold text-slate-600 group-hover:text-white transition-colors">Lihat Semua
                        Report</span>
                    <i class="fas fa-arrow-right text-xs text-slate-400 group-hover:text-white transition-colors"></i>
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach ($latestKegiatans as $k)
                    <article
                        class="group relative bg-white rounded-[2rem] border border-slate-200 overflow-hidden hover:shadow-2xl hover:shadow-blue-900/10 transition-all duration-500 flex flex-col h-full">
                        <div class="relative h-48 overflow-hidden shrink-0">
                            @if ($k->gambar)
                                <img src="{{ asset('storage/' . $k->gambar) }}"
                                    class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                            @else
                                <div class="w-full h-full bg-slate-50 flex items-center justify-center text-slate-300">
                                    <i class="fas fa-camera text-4xl"></i>
                                </div>
                            @endif
                        </div>
                        <div class="p-8 flex flex-col flex-1">
                            <div
                                class="flex items-center gap-2 mb-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                <i class="far fa-calendar-alt text-blue-500"></i>
                                {{ $k->tanggal_kegiatan->translatedFormat('d M Y') }}
                            </div>
                            <h3
                                class="text-lg font-extrabold text-slate-900 mb-4 group-hover:text-[#1a4fa0] transition-colors line-clamp-2">
                                {{ $k->judul }}</h3>
                            <p class="text-sm text-slate-500 line-clamp-3 mb-6 leading-relaxed">
                                {{ strip_tags($k->konten) }}</p>
                            <div class="mt-auto border-t border-slate-50 pt-6">
                                <a href="{{ route('kegiatan.show', $k->slug) }}"
                                    class="inline-flex items-center gap-2 text-xs font-black text-[#1a4fa0] uppercase tracking-widest group/btn py-1 px-3 rounded-lg hover:bg-blue-50 transition-all">
                                    BACA REPORT <i
                                        class="fas fa-arrow-right text-[10px] transform group-hover/btn:translate-x-1 transition-transform"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
    {{-- About Section --}}
    <section id="tentang" class="bg-slate-50 py-20 px-6">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div class="order-2 lg:order-1">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-4 pt-10">
                        <div
                            class="aspect-square rounded-2xl bg-white p-4 shadow-sm flex items-center justify-center border border-slate-200">
                            <img src="{{ asset('image/logo-RPL.jpg') }}" alt="Laboratorium RPL"
                                class="w-24 h-24 object-contain grayscale opacity-50">
                        </div>
                        <div class="aspect-[3/4] rounded-2xl bg-primary/10 overflow-hidden">
                            <div
                                class="w-full h-full bg-primary flex items-center justify-center text-white text-5xl font-black">
                                RPL</div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="aspect-[3/4] rounded-2xl bg-slate-200 overflow-hidden">
                            <div
                                class="w-full h-full bg-slate-300 flex items-center justify-center text-slate-400 text-3xl font-bold italic">
                                ITATS</div>
                        </div>
                        <div
                            class="aspect-square rounded-2xl bg-white p-4 shadow-sm flex items-center justify-center border border-slate-200">
                            <i class="fas fa-terminal text-6xl text-slate-100"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="order-1 lg:order-2">
                <div class="text-primary font-bold tracking-widest uppercase text-sm mb-4">Tentang Laboratorium</div>
                <h2 class="text-4xl font-extrabold text-slate-900 mb-6 leading-tight">Membangun Fondasi <br> Perangkat
                    Lunak
                    Berkualitas</h2>
                <div class="space-y-4 text-slate-600 leading-relaxed">
                    <p>Laboratorium Rekayasa Perangkat Lunak (RPL) ITATS didedikasikan untuk mengampu mata kuliah praktikum
                        Pemrograman Terstruktur, Struktur Data, dan Basis Data demi membangun pondasi teknis yang kuat bagi mahasiswa.</p>
                    <p>Kami memfokuskan pada penguasaan logika pemrograman, efisiensi algoritma, dan manajemen basis data yang terintegrasi untuk menjamin kualitas pemahaman mahasiswa dalam rekayasa perangkat lunak.</p>
                </div>
                <div class="mt-10 grid grid-cols-2 gap-8 border-t border-slate-200 pt-10">
                    <div>
                        <div class="text-primary font-bold text-xl">Visi</div>
                        <p class="text-sm text-slate-500 mt-2">Menjadi laboratorium unggulan dalam pembentukan kemampuan fundamental rekayasa perangkat lunak melalui penguasaan algoritma dan basis data.</p>
                    </div>
                    <div>
                        <div class="text-primary font-bold text-xl">Misi</div>
                        <p class="text-sm text-slate-500 mt-2">Mencetak praktikan yang mahir dalam logika pemrograman, optimasi struktur data, dan manajemen basis data.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection