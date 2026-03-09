@extends('layouts.app')

@section('title', 'Daftar Pengumuman — Lab RPL ITATS')
@section('meta_description',
    'Kumpulan berita dan pengumuman terbaru seputar Laboratorium Rekayasa Perangkat Lunak
    ITATS.')


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
                    Update Terkini • Lab RPL
                </div>

                <h1
                    class="text-balance text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-[1.1] tracking-tight text-black">
                    Pusat <span class="text-[#1a4fa0]">Pengumuman & Berita</span>
                </h1>

                <p class="text-lg text-slate-600 max-w-prose leading-relaxed">
                    Ikuti perkembangan terbaru mengenai jadwal praktikum, kegiatan laboratorium, dan informasi penting
                    lainnya untuk seluruh praktikan Lab RPL ITATS.
                </p>

                <div class="flex flex-wrap items-center justify-center gap-4 mt-2">
                    <a href="#list-pengumuman"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-xl text-sm font-bold ring-offset-background transition-all hover:scale-105 bg-[#1a4fa0] text-white hover:bg-[#1a4fa0]/90 h-12 px-8 py-2 shadow-lg shadow-[#1a4fa0]/25">
                        Lihat Pengumuman
                    </a>
                    <a href="{{ route('login.praktikan') }}"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-xl text-sm font-semibold ring-offset-background transition-all hover:scale-105 hover:bg-slate-100 h-12 px-8 py-2 border-2 border-slate-200 text-slate-700 bg-white">
                        Portal Praktikan
                    </a>
                </div>
                <div class="w-16 h-1 bg-[#1a4fa0] mt-4 rounded-full"></div>
            </div>
        </section>

        {{-- List Pengumuman --}}
        <section id="list-pengumuman" class="py-24 bg-slate-50/50 relative">
            <div class="max-w-screen-xl mx-auto px-6">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-16">
                    <div>
                        <h4 class="text-blue-600 font-black tracking-[0.3em] uppercase text-xs mb-4">Warta Lab</h4>
                        <h2 class="text-4xl font-extrabold text-slate-900 tracking-tight">Daftar Pengumuman</h2>
                    </div>
                    <div class="hidden md:block">
                        <p class="text-slate-500 font-medium max-w-xs text-right italic">
                            Informasi disaring secara berkala oleh tim administrator.
                        </p>
                    </div>
                </div>

                @if ($pengumumans->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach ($pengumumans as $p)
                            @php
                                $catColors = [
                                    'umum' => 'bg-blue-50 text-blue-600 border-blue-100',
                                    'praktikum' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                    'kegiatan' => 'bg-purple-50 text-purple-600 border-purple-100',
                                ];
                                $catStyle = $catColors[$p->kategori] ?? 'bg-slate-50 text-slate-600 border-slate-100';
                            @endphp
                            <article
                                class="group bg-white rounded-[2rem] border border-slate-200/60 hover:border-blue-200/50 hover:shadow-2xl hover:shadow-blue-900/5 transition-all duration-500 overflow-hidden flex flex-col h-full">
                                {{-- Thumbnail Area --}}
                                <div class="relative h-56 overflow-hidden shrink-0">
                                    @if ($p->gambar)
                                        <img src="{{ asset('storage/' . $p->gambar) }}" alt="{{ $p->judul }}"
                                            class="absolute inset-0 w-full h-full object-cover transition duration-700 group-hover:scale-110">
                                    @else
                                        <div
                                            class="absolute inset-0 bg-slate-100 flex items-center justify-center text-slate-300">
                                            <i
                                                class="fas fa-newspaper text-6xl opacity-20 group-hover:scale-110 transition-transform duration-500"></i>
                                        </div>
                                    @endif
                                    <div class="absolute top-6 left-6">
                                        <span
                                            class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider border {{ $catStyle }} shadow-sm">
                                            {{ $p->kategori }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Content Area --}}
                                <div class="p-8 flex flex-col flex-1">
                                    <div
                                        class="flex items-center gap-2 mb-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                                        <i class="far fa-calendar-alt text-blue-500"></i>
                                        {{ $p->created_at->translatedFormat('d F Y') }}
                                    </div>

                                    <h3
                                        class="text-xl font-extrabold text-slate-900 mb-4 group-hover:text-blue-600 transition-colors leading-snug line-clamp-2">
                                        <a href="{{ route('pengumuman.show', $p->slug) }}">{{ $p->judul }}</a>
                                    </h3>

                                    <p
                                        class="text-slate-500 text-sm mb-8 leading-relaxed line-clamp-3 font-medium opacity-80">
                                        {{ strip_tags($p->konten) }}
                                    </p>

                                    <div class="mt-auto pt-6 border-t border-slate-50 flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-xs shadow-inner">
                                                <i class="fas fa-user-shield"></i>
                                            </div>
                                            <span
                                                class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ explode(' ', $p->user->name)[0] }}</span>
                                        </div>
                                        <a href="{{ route('pengumuman.show', $p->slug) }}"
                                            class="inline-flex items-center gap-2 text-[11px] font-black text-blue-600 uppercase tracking-widest group/btn py-1 px-3 rounded-lg hover:bg-blue-50 transition-all">
                                            DETAIL
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
                        {{ $pengumumans->links() }}
                    </div>
                @else
                    <div class="max-w-md mx-auto py-24 text-center">
                        <div
                            class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-8 shadow-inner">
                            <i class="fas fa-envelope-open text-4xl text-slate-300"></i>
                        </div>
                        <h3 class="text-2xl font-extrabold text-slate-900">Belum ada pengumuman</h3>
                        <p class="text-slate-500 mt-4 leading-relaxed font-medium">Nantikan informasi terbaru dari kami di
                            halaman ini atau melalui media sosial kami.</p>
                    </div>
                @endif
            </div>
        </section>

        {{-- Contact Section --}}
        <section class="py-24 max-w-screen-xl mx-auto px-6">
            <div
                class="relative rounded-[3rem] bg-slate-900 p-10 md:p-20 overflow-hidden group shadow-2xl shadow-blue-900/20">
                <div
                    class="absolute top-0 right-0 h-full w-1/2 bg-blue-600 rounded-l-[10rem] opacity-5 pointer-events-none transition-transform duration-1000 group-hover:scale-110">
                </div>
                <div class="absolute bottom-0 left-0 p-12 text-white/5 pointer-events-none">
                    <i class="fas fa-question-circle text-[15rem]"></i>
                </div>

                <div class="relative z-10 flex flex-col md:flex-row items-center gap-12">
                    <div class="flex-1 text-center md:text-left">
                        <h2 class="text-3xl md:text-5xl font-extrabold text-white mb-6 leading-tight">Butuh Bantuan?</h2>
                        <p class="text-slate-400 text-lg md:text-xl font-medium max-w-xl leading-relaxed">
                            Punya kendala teknis atau pertanyaan seputar praktikum? Tim helpdesk kami siap membantu Anda.
                        </p>
                    </div>
                    <div class="shrink-0 flex translate-y-2">
                        <a href="mailto:labrpl.itats@gmail.com"
                            class="inline-flex items-center gap-4 bg-white text-slate-900 px-10 py-5 rounded-2xl font-bold hover:bg-blue-50 transition-all hover:scale-105 shadow-xl">
                            Hubungi Helpdesk Lab
                            <i class="fas fa-external-link-alt text-xs opacity-40"></i>
                        </a>
                    </div>
                </div>
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
