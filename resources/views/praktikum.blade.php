@extends('layouts.app')

@section('content')
    <div class="bg-white min-h-screen">
        {{-- Hero Section --}}
        <section class="max-w-screen-2xl mx-auto px-6 md:px-10 pt-10 md:pt-16 pb-12 overflow-hidden">
            <div class="text-center max-w-4xl mx-auto flex flex-col items-center gap-6 relative z-10">
                <div
                    class="inline-flex w-fit items-center gap-2 rounded-full border border-slate-200 px-3 py-1 text-xs font-medium text-slate-500 mb-2">
                    <span class="h-2 w-2 rounded-full bg-orange-500 shadow-[0_0_8px_rgba(249,115,22,0.4)]"></span>
                    Program Akademik • Lab RPL
                </div>
                <h1
                    class="text-balance text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-[1.1] tracking-tight text-black">
                    Daftar <span class="text-[#1a4fa0]">Praktikum & Pelatihan</span>
                </h1>
                <p class="text-lg text-slate-600 max-w-prose leading-relaxed">
                    Temukan berbagai modul praktikum yang dirancang untuk mengasah kemampuan teknis Anda dalam rekayasa
                    perangkat lunak, mulai dari dasar pemrograman hingga arsitektur sistem kompleks.
                </p>
                <div class="flex flex-wrap items-center justify-center gap-4 mt-2">
                    <a href="#list-praktikum"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-xl text-sm font-bold ring-offset-background transition-all hover:scale-105 bg-[#1a4fa0] text-white hover:bg-[#1a4fa0]/90 h-12 px-8 py-2 shadow-lg shadow-[#1a4fa0]/25">
                        Lihat Praktikum
                    </a>
                    <a href="{{ route('login.praktikan') }}"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-xl text-sm font-semibold ring-offset-background transition-all hover:scale-105 hover:bg-slate-100 h-12 px-8 py-2 border-2 border-slate-200 text-slate-700 bg-white">
                        Daftar Sekarang
                    </a>
                </div>
                <div class="w-16 h-1 bg-[#1a4fa0] mt-4 rounded-full"></div>
            </div>
        </section>

        {{-- List Praktikum --}}
        <section id="list-praktikum" class="py-24 bg-slate-50 border-y border-slate-100">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-16 px-6">
                    <h4 class="text-[#1a4fa0] font-bold tracking-widest uppercase text-sm mb-4">Materi Tersedia</h4>
                    <h2 class="text-4xl font-extrabold text-slate-900">Program Praktikum Aktif</h2>
                    <div class="w-16 h-1 bg-[#1a4fa0] mx-auto mt-6"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @forelse($praktikums as $p)
                        <div
                            class="group relative bg-white rounded-3xl p-8 border border-slate-200 hover:border-[#1a4fa0]/30 hover:shadow-2xl transition-all duration-500 flex flex-col h-full">
                            <div class="flex justify-between items-start mb-6">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-blue-50 text-[#1a4fa0] flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="fas fa-code text-xl"></i>
                                </div>
                                <span @class([
                                    'px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider',
                                    'bg-emerald-50 text-emerald-600' =>
                                        $p->status_praktikum === 'open_registration',
                                    'bg-amber-50 text-amber-600' => $p->status_praktikum === 'on_progress',
                                    'bg-rose-50 text-rose-600' => $p->status_praktikum === 'finished',
                                    'bg-slate-100 text-slate-500' => $p->status_praktikum === 'closed',
                                ])>
                                    @if ($p->status_praktikum === 'finished')
                                        Selesai / Berakhir
                                    @else
                                        {{ str_replace('_', ' ', $p->status_praktikum) }}
                                    @endif
                                </span>
                            </div>

                            <h3
                                class="text-xl font-bold text-slate-900 mb-3 group-hover:text-[#1a4fa0] transition-colors leading-snug">
                                {{ $p->nama_praktikum }}
                            </h3>

                            <p class="text-slate-500 text-sm mb-6 flex-grow">
                                Kode: <span
                                    class="font-mono text-slate-700 bg-slate-50 px-1.5 py-0.5 rounded">{{ $p->kode_praktikum }}</span><br>
                                Periode: {{ $p->periode_praktikum }}
                            </p>

                            <div class="space-y-3 mb-8">
                                <div class="flex items-center gap-3 text-sm text-slate-600">
                                    <i class="fas fa-book-open w-5 text-[#1a4fa0]/60"></i>
                                    <span>{{ $p->jumlah_modul ?? 0 }} Modul Pembelajaran</span>
                                </div>
                                <div class="flex items-center gap-3 text-sm text-slate-600">
                                    <i class="fas fa-users w-5 text-[#1a4fa0]/60"></i>
                                    <span>Kuota: {{ $p->kuota_praktikan }} Mahasiswa</span>
                                </div>
                                <div class="flex items-center gap-3 text-sm text-slate-600">
                                    <i
                                        class="fas @if ($p->ada_tugas_akhir) fa-check-circle text-emerald-500 @else fa-times-circle text-slate-300 @endif w-5"></i>
                                    <span>Proyek Akhir Lab</span>
                                </div>
                            </div>

                            @if ($p->status_praktikum === 'open_registration')
                                <a href="{{ route('login.praktikan') }}"
                                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-slate-50 text-[#1a4fa0] font-bold hover:bg-[#1a4fa0] hover:text-white transition-all duration-300 group/btn shadow-sm">
                                    Daftar Praktikum
                                    <i
                                        class="fas fa-arrow-right text-xs transform group-hover/btn:translate-x-1 transition-transform"></i>
                                </a>
                            @elseif($p->status_praktikum === 'on_progress')
                                <button disabled
                                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-slate-100 text-slate-400 font-bold cursor-not-allowed">
                                    <i class="fas fa-spinner fa-spin text-xs"></i>
                                    Sedang Berjalan
                                </button>
                            @else
                                <button disabled
                                    class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-slate-100 text-slate-400 font-bold cursor-not-allowed">
                                    <i class="fas fa-lock text-xs"></i>
                                    Pendaftaran Ditutup
                                </button>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-full py-20 text-center">
                            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-layer-group text-3xl text-slate-300"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900">Belum ada praktikum</h3>
                            <p class="text-slate-500 mt-2">Jadwal praktikum akan segera diumumkan.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- Faq/Notice --}}
        <section class="py-24 max-w-4xl mx-auto px-6">
            <div class="bg-white rounded-3xl border border-slate-200 p-10 md:p-12 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 p-8 opacity-5">
                    <i class="fas fa-question-circle text-9xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 mb-6">Informasi Pendaftaran</h3>
                <div class="space-y-6">
                    <div class="flex gap-4">
                        <div
                            class="w-10 h-10 rounded-full bg-blue-50 text-[#1a4fa0] flex-shrink-0 flex items-center justify-center">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div>
                            <h5 class="font-bold text-slate-900 mb-1">Akun Terverifikasi</h5>
                            <p class="text-slate-500 text-sm italic">Mahasiswa wajib menggunakan akun praktikan yang sudah
                                diverifikasi oleh admin untuk mendaftar praktikum.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div
                            class="w-10 h-10 rounded-full bg-indigo-50 text-[#1a4fa0] flex-shrink-0 flex items-center justify-center">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h5 class="font-bold text-slate-900 mb-1">Batas Waktu</h5>
                            <p class="text-slate-500 text-sm italic">Perhatikan status 'Open Registration'. Pendaftaran akan
                                ditutup otomatis jika kuota telah terpenuhi.</p>
                        </div>
                    </div>
                </div>
                <div
                    class="mt-10 pt-8 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-slate-500">Punya kendala pendaftaran?</p>
                    <a href="mailto:labrpl.itats@gmail.com" class="text-[#1a4fa0] font-bold hover:underline">Hubungi
                        Helpdesk Lab &rarr;</a>
                </div>
            </div>
        </section>
    </div>
@endsection
