@extends('layouts.app')

@section('title', 'Laporan Kegiatan — Lab RPL ITATS')
@section('meta_description', 'Arsip laporan dan dokumentasi kegiatan yang telah dilaksanakan oleh Laboratorium Rekayasa
    Perangkat Lunak ITATS.')


@section('content')
    <div class="bg-white min-h-screen relative font-sans overflow-hidden">
        {{-- Decorative background elements --}}
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-[600px] pointer-events-none opacity-40">
            <div class="absolute top-[-10%] left-[-20%] w-[50%] h-[70%] rounded-full bg-blue-100/40 blur-[130px]"></div>
            <div class="absolute bottom-0 right-[-10%] w-[40%] h-[60%] rounded-full bg-indigo-50/40 blur-[110px]"></div>
        </div>

        {{-- Hero Section --}}
        <section class="relative z-10 max-w-screen-2xl mx-auto px-6 md:px-10 pt-10 md:pt-16 pb-12">
            <div class="text-center max-w-4xl mx-auto flex flex-col items-center gap-6 relative z-10">
                <div
                    class="inline-flex w-fit items-center gap-2 rounded-full border border-slate-200 px-3 py-1 text-xs font-medium text-slate-500 mb-2">
                    <span class="h-2 w-2 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(37,99,235,0.4)]"></span>
                    Report Kegiatan • Lab RPL
                </div>

                <h1
                    class="text-balance text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-[1.1] tracking-tight text-black">
                    Laporan <span class="text-[#1a4fa0]">Kegiatan Lab</span>
                </h1>

                <p class="text-lg text-slate-600 max-w-prose leading-relaxed">
                    Dokumentasi dan arsip kegiatan yang telah dilaksanakan oleh Laboratorium Rekayasa Perangkat Lunak ITATS,
                    mulai dari workshop, lomba, hingga kunjungan industri.
                </p>

                <div class="flex flex-wrap items-center justify-center gap-4 mt-2">
                    <a href="#list-kegiatan"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-xl text-sm font-bold ring-offset-background transition-all hover:scale-105 bg-[#1a4fa0] text-white hover:bg-[#1a4fa0]/90 h-12 px-8 py-2 shadow-lg shadow-[#1a4fa0]/25">
                        Lihat Laporan
                    </a>
                </div>
                <div class="w-16 h-1 bg-[#1a4fa0] mt-4 rounded-full"></div>
            </div>
        </section>

        {{-- List Kegiatan --}}
        <section id="list-kegiatan" class="py-24 bg-slate-50/50 relative">
            <div class="max-w-screen-xl mx-auto px-6">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-16">
                    <div>
                        <h4 class="text-blue-600 font-black tracking-[0.3em] uppercase text-xs mb-4">Arsip Lab</h4>
                        <h2 class="text-4xl font-extrabold text-slate-900 tracking-tight">Daftar Kegiatan</h2>
                    </div>
                    <div class="hidden md:block">
                        <p class="text-slate-500 font-medium max-w-xs text-right italic">
                            Dokumentasi resmi setiap agenda laboratorium.
                        </p>
                    </div>
                </div>

                @if ($kegiatans->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach ($kegiatans as $k)
                            <article
                                class="group bg-white rounded-[2rem] border border-slate-200/60 hover:border-blue-200/50 hover:shadow-2xl hover:shadow-blue-900/5 transition-all duration-500 overflow-hidden flex flex-col h-full">
                                {{-- Thumbnail Area --}}
                                <div class="relative h-56 overflow-hidden shrink-0">
                                    @if ($k->gambar)
                                        <img src="{{ asset('storage/' . $k->gambar) }}" alt="{{ $k->judul }}"
                                            class="absolute inset-0 w-full h-full object-cover transition duration-700 group-hover:scale-110">
                                    @else
                                        <div
                                            class="absolute inset-0 bg-slate-100 flex items-center justify-center text-slate-300">
                                            <i
                                                class="fas fa-camera text-6xl opacity-20 group-hover:scale-110 transition-transform duration-500"></i>
                                        </div>
                                    @endif
                                    @if ($k->lokasi)
                                        <div class="absolute top-6 left-6">
                                            <span
                                                class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider border bg-white/90 backdrop-blur text-slate-900 border-white shadow-sm">
                                                <i class="fas fa-map-marker-alt text-blue-500 mr-1.5"></i>
                                                {{ $k->lokasi }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Content Area --}}
                                <div class="p-8 flex flex-col flex-1">
                                    <div
                                        class="flex items-center gap-2 mb-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                                        <i class="far fa-calendar-alt text-blue-500"></i>
                                        {{ $k->tanggal_kegiatan->translatedFormat('d F Y') }}
                                    </div>

                                    <h3
                                        class="text-xl font-extrabold text-slate-900 mb-4 group-hover:text-blue-600 transition-colors leading-snug line-clamp-2">
                                        <a href="{{ route('kegiatan.show', $k->slug) }}">{{ $k->judul }}</a>
                                    </h3>

                                    <p
                                        class="text-slate-500 text-sm mb-8 leading-relaxed line-clamp-3 font-medium opacity-80">
                                        {{ strip_tags($k->konten) }}
                                    </p>

                                    <div class="mt-auto pt-6 border-t border-slate-50 flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-xs shadow-inner">
                                                <i class="fas fa-user-shield"></i>
                                            </div>
                                            <span
                                                class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ explode(' ', $k->user->name)[0] }}</span>
                                        </div>
                                        <a href="{{ route('kegiatan.show', $k->slug) }}"
                                            class="inline-flex items-center gap-2 text-[11px] font-black text-blue-600 uppercase tracking-widest group/btn py-1 px-3 rounded-lg hover:bg-blue-50 transition-all">
                                            BACA REPORT
                                            <i
                                                class="fas fa-arrow-right text-[10px] transform group-hover/btn:translate-x-1 transition-transform"></i>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-20 flex justify-center">
                        {{ $kegiatans->links() }}
                    </div>
                @else
                    <div class="max-w-md mx-auto py-24 text-center">
                        <div
                            class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner">
                            <i class="fas fa-calendar-times text-4xl text-slate-300"></i>
                        </div>
                        <h3 class="text-2xl font-extrabold text-slate-900">Belum ada laporan kegiatan</h3>
                        <p class="text-slate-500 mt-4 leading-relaxed font-medium">Nantikan update laporan kegiatan terbaru
                            dari kami di
                            halaman ini.</p>
                    </div>
                @endif
            </div>
        </section>
    </div>

    <style>
        /* Pagination custom styling */
        .pagination {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .page-item .page-link {
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            font-weight: 800;
            font-size: 0.875rem;
            color: #64748b;
            background: white;
            border: 1px solid #e2e8f0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        }

        .page-item.active .page-link {
            background: #2563eb;
            border-color: #2563eb;
            color: white;
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.25);
            transform: translateY(-2px);
        }

        .page-item:hover .page-link:not(.active) {
            border-color: #cbd5e1;
            background: #f8fafc;
            color: #1e293b;
        }

        /* Smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }
    </style>
@endsection
