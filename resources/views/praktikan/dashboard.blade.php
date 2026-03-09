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
                <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Praktikum Diikuti</h3>
                <p class="text-xl font-bold text-slate-900">
                    {{ Auth::user()->praktikan->pendaftarans()->where('status', 'verified')->count() }} Praktikum</p>
                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-2">
                    <p class="text-[10px] text-slate-500 font-medium tracking-tight">Terverifikasi di sistem</p>
                </div>
            </div>

            <div
                class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm shadow-slate-900/5 group transition-all hover:bg-zinc-50 hover:-translate-y-1">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="h-10 w-10 rounded-xl bg-purple-50 flex items-center justify-center border border-purple-100 text-purple-600">
                        <i class="fas fa-calendar-alt text-lg"></i>
                    </div>
                </div>
                <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Jadwal Mendatang</h3>
                <p class="text-xl font-bold text-slate-900">{{ $upcomingSchedules->count() }} Agenda</p>
                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-2 text-slate-500">
                    <p class="text-[10px] font-medium tracking-tight italic">Cek jadwal pelaksanaan praktikum</p>
                </div>
            </div>
        </div>

        <!-- Progress Asistensi Section -->
        @if ($activePendaftarans->isNotEmpty())
            <div class="mt-10">
                <div class="flex items-center gap-3 mb-6">
                    <div
                        class="h-8 w-8 rounded-lg bg-[#001f3f] flex items-center justify-center text-white shadow-lg shadow-[#001f3f]/10">
                        <i class="fas fa-chart-line text-[10px]"></i>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900 uppercase tracking-tight">Progress Asistensi & Modul</h2>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach ($activePendaftarans as $ap)
                        <div
                            class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col p-6 space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-50 pb-4">
                                <div class="flex items-center gap-3">
                                    <div class="bg-[#001f3f]/5 p-2 rounded-xl text-[#001f3f]">
                                        <i class="fas fa-book-reader"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-black text-slate-900 uppercase tracking-tight">
                                            {{ $ap->praktikum->nama_praktikum }}</h4>
                                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">
                                            Aslab: {{ $ap->aslab->user->name ?? 'Belum Ditugaskan' }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('praktikan.pendaftaran.progress', $ap->id) }}"
                                    class="text-[10px] font-black text-[#001f3f] uppercase hover:underline">Detail</a>
                            </div>

                            <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-hide">
                                @forelse($ap->tugasAsistensis as $idx => $t)
                                    <div class="flex items-center flex-none group">
                                        <div class="relative flex flex-col items-center gap-2 min-w-[70px]">
                                            @php
                                                $statusColor = match ($t->status) {
                                                    'reviewed' => 'bg-emerald-500 text-white border-emerald-500',
                                                    'submitted' => 'bg-amber-100 text-amber-600 border-amber-200',
                                                    default => 'bg-slate-50 text-slate-400 border-slate-200',
                                                };
                                                $icon = match ($t->status) {
                                                    'reviewed' => 'fa-check-double',
                                                    'submitted' => 'fa-arrow-up',
                                                    default => 'fa-hourglass-start',
                                                };
                                            @endphp
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 text-xs font-black {{ $statusColor }} transition-all"
                                                title="{{ $t->status }}">
                                                <i class="fas {{ $icon }}"></i>
                                            </div>
                                            <span
                                                class="text-[9px] font-black text-slate-400 uppercase tracking-tighter truncate max-w-[60px]">{{ $t->judul }}</span>
                                        </div>
                                        @if (!$loop->last)
                                            <div
                                                class="w-8 h-0.5 {{ $t->status == 'reviewed' ? 'bg-emerald-500' : 'bg-slate-100' }}">
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="py-4 w-full text-center">
                                        <p class="text-[10px] text-slate-400 font-bold uppercase italic tracking-widest">
                                            Belum ada tugas dari aslab</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Peringatan Asistensi Alert -->
        @php
            $pendingTasks = collect();
            foreach ($activePendaftarans as $ap) {
                foreach ($ap->tugasAsistensis as $t) {
                    if ($t->status == 'pending') {
                        $pendingTasks->push([
                            'praktikum' => $ap->praktikum->nama_praktikum,
                            'modul' => $t->judul,
                            'pendaftaran_id' => $ap->id,
                        ]);
                    }
                }
            }
        @endphp

        @if ($pendingTasks->isNotEmpty())
            <div class="mt-8 space-y-3">
                @foreach ($pendingTasks as $pt)
                    <div
                        class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-2xl shadow-sm animate-in slide-in-from-left duration-500">
                        <div class="flex items-center gap-4">
                            <div
                                class="h-10 w-10 rounded-full bg-rose-500 flex items-center justify-center text-white shrink-0 shadow-lg shadow-rose-500/20">
                                <i class="fas fa-exclamation-triangle text-sm"></i>
                            </div>
                            <div class="flex-grow">
                                <p class="text-xs sm:text-sm font-bold text-rose-900 uppercase tracking-tight">
                                    Peringatan Asistensi
                                </p>
                                <p class="text-[10px] sm:text-xs text-rose-700 mt-0.5 leading-relaxed">
                                    Anda belum bisa mengikuti praktikum <span
                                        class="font-black italic">"{{ $pt['praktikum'] }}"</span> pada bagian <span
                                        class="font-black italic underline">{{ $pt['modul'] }}</span> karena belum
                                    melaksanakan asistensi aslab.
                                </p>
                            </div>
                            <a href="{{ route('praktikan.pendaftaran.progress', $pt['pendaftaran_id']) }}"
                                class="shrink-0 px-4 py-2 bg-rose-600 text-white text-[9px] font-black rounded-xl uppercase tracking-widest hover:bg-rose-700 transition-all shadow-md shadow-rose-600/10">
                                Kerjakan
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Upcoming Schedules -->
        <div class="mt-10">
            <div class="flex items-center gap-3 mb-6">
                <div
                    class="h-8 w-8 rounded-lg bg-emerald-500 flex items-center justify-center text-white shadow-lg shadow-emerald-500/10">
                    <i class="fas fa-clock text-[10px]"></i>
                </div>
                <h2 class="text-lg font-bold text-slate-900 uppercase tracking-tight">Jadwal Praktikum Mendatang</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @forelse($upcomingSchedules as $jadwal)
                    @php
                        $now = \Carbon\Carbon::now();
                        $start = \Carbon\Carbon::parse($jadwal->tanggal . ' ' . $jadwal->waktu_mulai);
                        $end = \Carbon\Carbon::parse($jadwal->tanggal . ' ' . $jadwal->waktu_selesai);

                        $isFinished = $now->greaterThan($end);
                        $isOngoing = $now->between($start, $end);

                        $statusLabel = 'Aktif';
                        $statusClass = 'bg-emerald-50 text-emerald-600 border-emerald-100';

                        if ($isFinished) {
                            $statusLabel = 'Selesai';
                            $statusClass = 'bg-slate-100 text-slate-500 border-slate-200';
                        } elseif ($isOngoing) {
                            $statusLabel = 'Berlangsung';
                            $statusClass = 'bg-amber-50 text-amber-600 border-amber-100 animate-pulse';
                        }
                    @endphp
                    <div
                        class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm hover:border-emerald-500/50 transition-all group {{ $isFinished ? 'opacity-70' : '' }}">
                        <div class="flex justify-between items-start mb-3">
                            <span
                                class="text-[9px] font-black {{ $statusClass }} px-2 py-0.5 rounded uppercase tracking-widest border">
                                {{ $statusLabel }}
                            </span>
                            <div class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">
                                {{ $jadwal->praktikum->kode_praktikum }}</div>
                        </div>
                        <h4
                            class="text-sm font-bold text-slate-900 line-clamp-1 uppercase group-hover:text-emerald-600 transition-colors">
                            {{ $jadwal->judul_modul }}</h4>
                        <p class="text-[11px] font-medium text-slate-500 mb-3 line-clamp-1 italic">
                            {{ $jadwal->praktikum->nama_praktikum }}</p>

                        <div class="space-y-2 pt-3 border-t border-slate-50">
                            <div class="flex items-center gap-2">
                                <i class="far fa-calendar text-[10px] text-slate-400"></i>
                                <span
                                    class="text-[10px] font-bold text-slate-700">{{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('l, d M Y') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="far fa-clock text-[10px] text-slate-400"></i>
                                <span class="text-[10px] font-bold text-slate-700">{{ substr($jadwal->waktu_mulai, 0, 5) }}
                                    - {{ substr($jadwal->waktu_selesai, 0, 5) }} WIB</span>
                            </div>
                            @if ($isFinished)
                                <div
                                    class="mt-2 text-[8px] font-black text-rose-500 uppercase tracking-widest border-t border-dashed border-rose-100 pt-2 animate-bounce">
                                    <i class="fas fa-info-circle mr-1"></i> Menunggu Jadwal Modul Selanjutnya
                                </div>
                            @else
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-location-dot text-[10px] text-slate-400"></i>
                                    <span
                                        class="text-[10px] font-bold text-slate-700">{{ $jadwal->ruangan ?? 'Laboratorium RPL' }}</span>
                                </div>
                            @endif
                        </div>

                        @if ($isOngoing || (!$isFinished && $now->diffInMinutes($start) <= 60))
                            <div class="mt-4">
                                <a href="{{ route('praktikan.presensi.generate-qr', $jadwal->id) }}"
                                    class="w-full py-2 bg-emerald-600 border border-emerald-500 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-emerald-600/10 hover:bg-emerald-700 transition-all flex items-center justify-center gap-2">
                                    <i class="fas fa-qrcode"></i>
                                    Presensi Sekarang
                                </a>
                            </div>
                        @endif
                    </div>
                @empty
                    <div
                        class="col-span-1 md:col-span-2 lg:col-span-4 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200 p-8 text-center">
                        <p class="text-xs text-slate-400 font-medium italic">Tidak ada jadwal praktikum mendatang untuk
                            Anda.</p>
                    </div>
                @endforelse
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

                                @if ($p->pendaftarans->isNotEmpty())
                                    <div
                                        class="w-full py-2.5 rounded-xl bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-[0.2em] border border-emerald-100 flex items-center justify-center gap-2">
                                        Sudah Terdaftar
                                        <i class="fas fa-check-double text-[10px]"></i>
                                    </div>
                                @else
                                    <a href="{{ route('praktikan.pendaftaran.create', $p->id) }}"
                                        class="w-full py-2.5 rounded-xl bg-[#001f3f] text-white text-[10px] font-bold uppercase tracking-[0.2em] shadow-lg shadow-[#001f3f]/10 transition-all hover:bg-[#002d5a] active:scale-[0.98] flex items-center justify-center gap-2 group-hover:gap-3">
                                        Daftar Praktikum
                                        <i class="fas fa-arrow-right text-[10px]"></i>
                                    </a>
                                @endif
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
