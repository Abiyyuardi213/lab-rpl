@extends('layouts.app')

@section('title', $pengumuman->judul . ' — Lab RPL ITATS')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($pengumuman->konten), 160))


@php
    $categoryColors = [
        'umum' => 'bg-blue-50 text-blue-700 ring-blue-700/10',
        'praktikum' => 'bg-emerald-50 text-emerald-700 ring-emerald-700/10',
        'kegiatan' => 'bg-purple-50 text-purple-700 ring-purple-700/10',
    ];
    $categoryBadge = $categoryColors[$pengumuman->kategori] ?? 'bg-slate-50 text-slate-700 ring-slate-700/10';
@endphp

@section('content')
    <div class="relative bg-white font-sans overflow-hidden">
        {{-- Decorative background elements --}}
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-[500px] pointer-events-none opacity-40">
            <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[60%] rounded-full bg-blue-100/50 blur-[120px]"></div>
            <div class="absolute bottom-0 right-[-5%] w-[30%] h-[50%] rounded-full bg-indigo-50/50 blur-[100px]"></div>
        </div>

        {{-- Breadcrumbs --}}
        <nav class="relative z-10 max-w-screen-xl mx-auto px-6 pt-12 pb-8">
            <ol class="flex items-center space-x-2 text-sm text-slate-500 font-medium">
                <li><a href="/" class="hover:text-blue-600 transition-colors">Beranda</a></li>
                <li class="flex items-center space-x-2">
                    <svg class="h-5 w-5 text-slate-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                    <a href="{{ route('pengumuman.public') }}" class="hover:text-blue-600 transition-colors">Pengumuman</a>
                </li>
                <li class="flex items-center space-x-2">
                    <svg class="h-5 w-5 text-slate-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="text-slate-900 truncate max-w-[200px] sm:max-w-xs">{{ $pengumuman->judul }}</span>
                </li>
            </ol>
        </nav>

        {{-- Article Header --}}
        <header class="relative z-10 max-w-screen-xl mx-auto px-6 pb-12">
            <div class="max-w-4xl">
                <div
                    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset mb-6 {{ $categoryBadge }}">
                    {{ ucfirst($pengumuman->kategori) }}
                </div>
                <h1
                    class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-[1.1] mb-8">
                    {{ $pengumuman->judul }}
                </h1>

                <div class="flex flex-wrap items-center gap-6 text-slate-500">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-blue-600">
                            <i class="fas fa-user-circle text-xl"></i>
                        </div>
                        <div class="text-sm">
                            <p class="font-bold text-slate-900 leading-none mb-1">{{ $pengumuman->user->name }}</p>
                            <p class="text-xs uppercase tracking-wider font-semibold opacity-70">Administrator Lab</p>
                        </div>
                    </div>
                    <div class="w-px h-6 bg-slate-200 hidden sm:block"></div>
                    <div class="flex items-center gap-2 text-sm">
                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <time datetime="{{ $pengumuman->created_at->toIso8601String() }}">
                            {{ $pengumuman->created_at->translatedFormat('d F Y') }}
                        </time>
                    </div>
                </div>
            </div>
        </header>

        {{-- Main Content Grid --}}
        <div class="relative z-10 max-w-screen-xl mx-auto px-6 pb-24">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

                {{-- Content Column --}}
                <main class="lg:col-span-8">
                    @if ($pengumuman->gambar)
                        <div class="relative mb-12 group">
                            <div
                                class="absolute -inset-2 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-[2rem] blur opacity-10 group-hover:opacity-20 transition duration-1000">
                            </div>
                            <div
                                class="relative rounded-[1.5rem] overflow-hidden bg-slate-100 shadow-2xl shadow-blue-900/10 border border-slate-200">
                                <img src="{{ asset('storage/' . $pengumuman->gambar) }}" alt="{{ $pengumuman->judul }}"
                                    class="w-full h-auto object-cover transition duration-700 group-hover:scale-[1.02]">
                                <a href="{{ asset('storage/' . $pengumuman->gambar) }}" target="_blank"
                                    class="absolute bottom-6 right-6 inline-flex items-center gap-2 px-4 py-2 bg-white/90 backdrop-blur-md rounded-full text-xs font-bold text-slate-900 shadow-lg hover:bg-white transition-all scale-0 group-hover:scale-100 origin-bottom-right">
                                    <i class="fas fa-expand-alt"></i> Lihat Resolusi Penuh
                                </a>
                            </div>
                        </div>
                    @endif

                    <article
                        class="prose prose-slate prose-lg max-w-none 
                        prose-headings:font-extrabold prose-headings:tracking-tight prose-headings:text-slate-900
                        prose-p:leading-relaxed prose-p:text-slate-600
                        prose-a:text-blue-600 prose-a:font-bold prose-a:no-underline hover:prose-a:underline
                        prose-strong:text-slate-900 prose-strong:font-extrabold
                        prose-img:rounded-2xl prose-img:shadow-lg
                        trix-content">
                        {!! $pengumuman->konten !!}
                    </article>

                    {{-- Share & Actions --}}
                    <div
                        class="mt-16 pt-8 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-bold text-slate-900 uppercase tracking-widest">Bagikan:</span>
                            <div class="flex gap-2">
                                <a href="https://wa.me/?text={{ urlencode($pengumuman->judul . ' - ' . route('pengumuman.show', $pengumuman->slug)) }}"
                                    target="_blank"
                                    class="w-10 h-10 rounded-full bg-[#25D366]/10 text-[#25D366] flex items-center justify-center hover:bg-[#25D366] hover:text-white transition-all transform hover:-translate-y-1">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('pengumuman.show', $pengumuman->slug)) }}"
                                    target="_blank"
                                    class="w-10 h-10 rounded-full bg-[#1877F2]/10 text-[#1877F2] flex items-center justify-center hover:bg-[#1877F2] hover:text-white transition-all transform hover:-translate-y-1">
                                    <i class="fab fa-facebook-f text-xs"></i>
                                </a>
                                <button
                                    onclick="navigator.clipboard.writeText('{{ route('pengumuman.show', $pengumuman->slug) }}')"
                                    class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-slate-900 hover:text-white transition-all transform hover:-translate-y-1">
                                    <i class="fas fa-link text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <a href="{{ route('pengumuman.public') }}"
                            class="inline-flex items-center gap-2 text-sm font-bold text-blue-600 hover:text-blue-700 transition-colors group">
                            <i class="fas fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
                            Daftar Pengumuman Lainnya
                        </a>
                    </div>
                </main>

                {{-- Sidebar Column --}}
                <aside class="lg:col-span-4 space-y-12">
                    {{-- Help Card --}}
                    <div class="sticky top-28">
                        <div
                            class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-[2rem] p-8 text-white shadow-2xl shadow-blue-900/20 relative overflow-hidden group">
                            <div
                                class="absolute top-0 right-0 -translate-y-4 translate-x-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700">
                            </div>
                            <div class="relative z-10">
                                <h3 class="text-xl font-bold mb-4 leading-tight text-white">Butuh bantuan atau ingin
                                    mendaftar?</h3>
                                <p class="text-blue-100 text-sm mb-8 leading-relaxed">
                                    Akses Portal Praktikan untuk melakukan pendaftaran praktikum dan manajemen tugas Anda.
                                </p>
                                <a href="{{ route('login.praktikan') }}"
                                    class="block w-full py-4 px-6 bg-white text-blue-600 rounded-xl text-center text-sm font-bold hover:bg-blue-50 transition-colors shadow-lg shadow-black/10">
                                    Masuk Portal Praktikan
                                </a>
                            </div>
                        </div>

                        {{-- Recent Announcements --}}
                        @if ($recentPengumumans->count() > 0)
                            <div class="mt-8">
                                <h3
                                    class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-6 flex items-center gap-3">
                                    <span class="w-8 h-px bg-slate-200"></span>
                                    Terbaru Lainnya
                                </h3>
                                <div class="space-y-6">
                                    @foreach ($recentPengumumans as $recent)
                                        <a href="{{ route('pengumuman.show', $recent->slug) }}"
                                            class="group flex gap-4 pr-4">
                                            @if ($recent->gambar)
                                                <div
                                                    class="h-16 w-16 rounded-xl overflow-hidden shrink-0 border border-slate-100">
                                                    <img src="{{ asset('storage/' . $recent->gambar) }}"
                                                        class="h-full w-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500">
                                                </div>
                                            @else
                                                <div
                                                    class="h-16 w-16 rounded-xl bg-slate-50 flex items-center justify-center shrink-0 border border-slate-100 text-blue-200 shadow-inner">
                                                    <i class="fas fa-newspaper text-xl"></i>
                                                </div>
                                            @endif
                                            <div class="flex-1 min-w-0">
                                                <h4
                                                    class="text-sm font-bold text-slate-900 line-clamp-2 leading-snug group-hover:text-blue-600 transition-colors">
                                                    {{ $recent->judul }}
                                                </h4>
                                                <p
                                                    class="text-[11px] text-slate-400 font-semibold mt-1 uppercase tracking-wider flex items-center gap-2">
                                                    <i class="far fa-calendar-alt text-[10px]"></i>
                                                    {{ $recent->created_at->translatedFormat('d M Y') }}
                                                </p>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Contact Widget --}}
                        <div class="mt-12 p-6 rounded-2xl bg-slate-50 border border-slate-100 shadow-sm">
                            <h4 class="text-sm font-bold text-slate-900 mb-2">Pusat Bantuan</h4>
                            <p class="text-[13px] text-slate-500 mb-4">Punya pertanyaan seputar pengumuman ini?</p>
                            <a href="mailto:labrpl.itats@gmail.com"
                                class="text-sm font-bold text-blue-600 hover:underline flex items-center gap-2">
                                <i class="far fa-envelope"></i> labrpl.itats@gmail.com
                            </a>
                        </div>
                    </div>
                </aside>

            </div>
        </div>
    </div>

    <style>
        /* Trix content specific overrides for premium readable feel */
        .trix-content {
            font-size: 1.125rem;
            line-height: 1.85;
            color: #334155;
        }

        .trix-content strong {
            color: #0f172a;
            font-weight: 800;
        }

        .trix-content a {
            color: #2563eb;
            font-weight: 700;
            text-decoration: none;
            border-bottom: 2px solid rgba(37, 99, 235, 0.1);
            transition: all 0.2s;
        }

        .trix-content a:hover {
            border-bottom-color: rgba(37, 99, 235, 0.8);
            background: rgba(37, 99, 235, 0.05);
        }

        .trix-content ul,
        .trix-content ol {
            padding-left: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .trix-content ul {
            list-style-type: disc;
        }

        .trix-content ol {
            list-style-type: decimal;
        }

        .trix-content li::marker {
            color: #2563eb;
            font-weight: bold;
        }

        .trix-content h1,
        .trix-content h2,
        .trix-content h3 {
            margin-top: 2.5rem;
            margin-bottom: 1rem;
            color: #0f172a;
            letter-spacing: -0.025em;
            line-height: 1.2;
        }

        .trix-content h2 {
            font-size: 1.875rem;
            font-weight: 800;
        }

        .trix-content h3 {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .trix-content img {
            border-radius: 1.25rem;
            margin: 2.5rem 0;
            box-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.15);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* Smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }
    </style>
@endsection
