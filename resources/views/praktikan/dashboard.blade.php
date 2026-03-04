@extends('layouts.admin')

@section('title', 'Dashboard Praktikan')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Dashboard Mahasiswa</h1>
                <p class="text-slate-500 mt-1 text-sm sm:text-base italic">"Belajarlah hari ini demi masa depan yang lebih
                    cerah."</p>
            </div>
            <div class="flex items-center gap-3">
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-100 shadow-sm shadow-emerald-600/5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                    Akun Aktif
                </span>
            </div>
        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div
                class="bg-[#001f3f] rounded-2xl p-6 text-white shadow-lg shadow-[#001f3f]/20 group transition-all hover:-translate-y-1">
                <div class="flex items-center justify-between mb-4">
                    <div class="h-10 w-10 rounded-xl bg-white/10 flex items-center justify-center border border-white/20">
                        <i class="fas fa-graduation-cap text-lg"></i>
                    </div>
                </div>
                <h3 class="text-xs font-bold uppercase tracking-widest text-white/60 mb-1">Status Mahasiswa</h3>
                <p class="text-xl font-bold">Terdaftar Aktif</p>
                <div class="mt-4 pt-4 border-t border-white/10 flex items-center gap-2">
                    <span
                        class="text-[10px] font-medium bg-emerald-500/20 text-emerald-400 px-2 py-0.5 rounded border border-emerald-500/30 uppercase tracking-tighter">Verified
                        Student</span>
                </div>
            </div>

            <div
                class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm shadow-slate-900/5 group transition-all hover:bg-zinc-50 hover:-translate-y-1">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="h-10 w-10 rounded-xl bg-blue-50 flex items-center justify-center border border-blue-100 text-blue-600">
                        <i class="fas fa-flask text-lg"></i>
                    </div>
                </div>
                <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Praktikum Berlangsung</h3>
                <p class="text-xl font-bold text-slate-900">0 Praktikum</p>
                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-2">
                    <p class="text-[10px] text-slate-500 font-medium">Belum terdaftar di kelas manapun</p>
                </div>
            </div>

            <div
                class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm shadow-slate-900/5 group transition-all hover:bg-zinc-50 hover:-translate-y-1">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="h-10 w-10 rounded-xl bg-purple-50 flex items-center justify-center border border-purple-100 text-purple-600">
                        <i class="fas fa-calendar-check text-lg"></i>
                    </div>
                </div>
                <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Total Kehadiran</h3>
                <p class="text-xl font-bold text-slate-900">N/A</p>
                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-2 text-slate-500">
                    <i class="fas fa-exclamation-circle text-[10px]"></i>
                    <p class="text-[10px] font-medium italic">Data segera hadir</p>
                </div>
            </div>
        </div>

        <!-- Available Practicum Section -->
        <div class="mt-8">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div
                        class="h-8 w-8 rounded-lg bg-[#001f3f] flex items-center justify-center text-white shadow-lg shadow-[#001f3f]/10">
                        <i class="fas fa-list text-[10px]"></i>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900 uppercase tracking-tight">Pendaftaran Praktikum Tersedia
                    </h2>
                </div>
                <a href="#" class="text-xs font-bold text-[#001f3f] hover:underline uppercase tracking-widest">Semua
                    Katalog</a>
            </div>

            @if ($praktikums->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($praktikums as $p)
                        <div
                            class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col group transition-all hover:shadow-md hover:border-[#001f3f]/30">
                            <!-- Card Header Image/Color -->
                            <div
                                class="h-24 bg-gradient-to-br from-[#001f3f] to-[#003366] p-4 flex items-start justify-between">
                                <span
                                    class="bg-white/20 backdrop-blur-md text-white text-[9px] font-black px-2 py-1 rounded uppercase tracking-[0.2em] border border-white/10">
                                    {{ $p->kode_praktikum }}
                                </span>
                                @php
                                    $statusConfig = [
                                        'open_registration' => [
                                            'label' => 'Buka Pendaftaran',
                                            'class' => 'bg-emerald-500',
                                        ],
                                        'on_progress' => [
                                            'label' => 'Berlangsung',
                                            'class' => 'bg-amber-500',
                                        ],
                                        'finished' => [
                                            'label' => 'Berakhir',
                                            'class' => 'bg-rose-500',
                                        ],
                                    ];
                                    $currentStatus = $statusConfig[$p->status_praktikum] ?? [
                                        'label' => $p->status_praktikum,
                                        'class' => 'bg-slate-500',
                                    ];
                                @endphp
                                <span
                                    class="{{ $currentStatus['class'] }} text-white text-[9px] font-black px-2 py-1 rounded uppercase tracking-[0.2em] border border-white/20 animate-pulse">
                                    {{ $currentStatus['label'] }}
                                </span>
                            </div>

                            <!-- Card Body -->
                            <div class="p-5 flex-grow">
                                <h3 class="text-base font-bold text-slate-900 line-clamp-1 mb-1">{{ $p->nama_praktikum }}
                                </h3>
                                <p class="text-xs font-medium text-slate-500 mb-4">{{ $p->periode_praktikum }}</p>

                                <div class="grid grid-cols-2 gap-4 mb-5">
                                    <div class="space-y-1">
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Kuota</p>
                                        <p class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                            <i class="fas fa-users text-slate-300"></i>
                                            {{ $p->kuota_praktikan }} Mahasiswa
                                        </p>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Terisi</p>
                                        @php
                                            $percent =
                                                $p->kuota_praktikan > 0
                                                    ? ($p->pendaftarans_count / $p->kuota_praktikan) * 100
                                                    : 0;
                                            $percent = min($percent, 100);
                                        @endphp
                                        <div class="flex items-center gap-2">
                                            <div class="h-1.5 flex-grow bg-slate-100 rounded-full overflow-hidden">
                                                <div class="h-full bg-emerald-500 rounded-full transition-all duration-500"
                                                    style="width: {{ $percent }}%"></div>
                                            </div>
                                            <p class="text-xs font-bold text-emerald-600">{{ round($percent) }}%</p>
                                        </div>
                                    </div>
                                </div>

                                <a href="{{ route('praktikan.pendaftaran.create', $p->id) }}"
                                    class="w-full py-2.5 rounded-xl bg-[#001f3f] text-white text-[10px] font-bold uppercase tracking-[0.2em] shadow-lg shadow-[#001f3f]/10 transition-all hover:bg-[#002d5a] active:scale-[0.98] flex items-center justify-center gap-2 group-hover:gap-3">
                                    Daftar Praktikum
                                    <i class="fas fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-3xl border border-slate-200 border-dashed p-12 text-center">
                    <div
                        class="h-20 w-20 rounded-full bg-slate-50 mx-auto flex items-center justify-center mb-4 border border-slate-100 shadow-inner">
                        <i class="fas fa-calendar-times text-2xl text-slate-300"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Belum Ada Praktikum Dibuka</h3>
                    <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto italic">Saat ini belum ada jadwal praktikum yang
                        tersedia untuk pendaftaran. Silakan periksa kembali nanti.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
