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
                            class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col p-6 space-y-5">
                            <div class="flex items-start justify-between">
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
                                <div class="text-right">
                                    <span class="text-2xl font-black text-slate-900 tracking-tighter">{{ $ap->progress_percentage }}%</span>
                                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Progress</p>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="space-y-2">
                                <div class="h-3 w-full bg-slate-100 rounded-full overflow-hidden border border-slate-200/50 p-0.5">
                                    <div class="h-full bg-emerald-500 rounded-full transition-all duration-1000 ease-out shadow-sm shadow-emerald-500/20"
                                        style="width: {{ $ap->progress_percentage }}%">
                                    </div>
                                </div>
                                <div class="flex justify-between items-center text-[9px] font-bold uppercase tracking-widest text-slate-400">
                                    <span>Hadir: {{ $ap->presensis_count }} Sesi</span>
                                    <span>Target: {{ $ap->praktikum->jumlah_modul + ($ap->praktikum->ada_tugas_akhir ? 1 : 0) }} Sesi</span>
                                </div>
                            </div>

                            <hr class="border-slate-50">

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-hide flex-grow mr-4">
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
                                                <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 text-[10px] font-black {{ $statusColor }} transition-all"
                                                    title="{{ $t->status }}">
                                                    <i class="fas {{ $icon }}"></i>
                                                </div>
                                                <span
                                                    class="text-[8px] font-black text-slate-400 uppercase tracking-tighter truncate max-w-[60px]">{{ $t->judul }}</span>
                                            </div>
                                            @if (!$loop->last)
                                                <div
                                                    class="w-6 h-0.5 {{ $t->status == 'reviewed' ? 'bg-emerald-500' : 'bg-slate-100' }}">
                                                </div>
                                            @endif
                                        </div>
                                    @empty
                                        <p class="text-[9px] text-slate-400 font-bold uppercase italic tracking-widest">
                                            Menunggu tugas asistensi...</p>
                                    @endforelse
                                </div>
                                <a href="{{ route('praktikan.pendaftaran.progress', $ap->id) }}"
                                    class="shrink-0 h-10 px-4 bg-slate-50 text-slate-600 text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-[#001f3f] hover:text-white transition-all flex items-center justify-center border border-slate-100">
                                    Detail
                                </a>
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
                    // A task needs attention if it's pending OR submitted but not yet graded/reviewed
                    if ($t->status == 'pending' || ($t->status == 'submitted' && $t->nilai === null)) {
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

        <!-- Penugasan / Soal Praktikum Aktif -->
        @if(isset($penugasans) && $penugasans->isNotEmpty())
            <div class="mt-10">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-lg bg-[#001f3f] flex items-center justify-center text-white shadow-lg shadow-[#001f3f]/10">
                            <i class="fas fa-file-invoice text-[10px]"></i>
                        </div>
                        <h2 class="text-lg font-bold text-slate-900 uppercase tracking-tight">Soal Praktikum Aktif</h2>
                    </div>
                    <a href="{{ route('praktikan.penugasan.index') }}" class="text-[10px] font-bold text-[#001f3f] uppercase tracking-widest hover:underline flex items-center gap-1">
                        Lihat Semua Soal <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($penugasans->filter(fn($p) => $p->is_accessible)->take(3) as $p)
                        <div class="bg-white rounded-xl border border-zinc-200 shadow-sm overflow-hidden flex flex-col group transition-all hover:shadow-lg">
                            <!-- Card Header -->
                            <div class="h-28 bg-linear-to-br from-[#001f3f] to-[#002d5a] p-5 flex flex-col justify-between relative overflow-hidden">
                                <div class="absolute top-0 right-0 p-4 opacity-10 pointer-events-none">
                                    <i class="fas fa-file-invoice text-7xl text-white"></i>
                                </div>
                                <div class="flex items-start justify-between relative z-10">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span class="bg-white/10 backdrop-blur-md text-white text-[9px] font-bold px-2 py-1 rounded border border-white/20 uppercase tracking-widest leading-none" title="Kode Praktikum">
                                            {{ $p->praktikum->kode_praktikum }}
                                        </span>
                                        <span class="bg-[#1a4fa0]/80 backdrop-blur-md text-white text-[9px] font-bold px-2 py-1 rounded border border-blue-400/30 uppercase tracking-widest leading-none" title="Modul">
                                            {{ $p->praktikum->nama_praktikum }}
                                        </span>
                                    </div>
                                    <span class="bg-emerald-500/20 backdrop-blur-md text-emerald-400 text-[9px] font-black px-2 py-1 rounded border border-emerald-500/30 uppercase tracking-widest leading-none flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                        TERBUKA
                                    </span>
                                </div>
                                <div class="relative z-10">
                                    <h3 class="text-sm font-black text-white line-clamp-1 uppercase tracking-tight leading-none">{{ $p->judul }}</h3>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="p-5 grow flex flex-col">
                                <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest leading-none mb-4 italic">{{ $p->praktikum->nama_praktikum }}</p>
                                
                                <div class="space-y-3 pt-4 border-t border-zinc-100 mb-6">
                                    <div class="flex items-center gap-3">
                                        <div class="h-7 w-7 rounded-md bg-zinc-50 flex items-center justify-center text-zinc-400 border border-zinc-100">
                                            <i class="fas fa-calendar-day text-[10px]"></i>
                                        </div>
                                        <div>
                                            <p class="text-[8px] font-bold text-zinc-400 uppercase tracking-widest leading-none">Hari Sesi</p>
                                            <p class="text-[10px] font-bold text-zinc-700 uppercase mt-1 leading-none">{{ $p->sesi->hari }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="h-7 w-7 rounded-md bg-zinc-50 flex items-center justify-center text-zinc-400 border border-zinc-100">
                                            <i class="fas fa-clock text-[10px]"></i>
                                        </div>
                                        <div>
                                            <p class="text-[8px] font-bold text-zinc-400 uppercase tracking-widest leading-none">Jam Akses</p>
                                            <p class="text-[10px] font-bold text-zinc-700 uppercase mt-1 leading-none tabular-nums">{{ substr($p->sesi->jam_mulai, 0, 5) }} - {{ substr($p->sesi->jam_selesai, 0, 5) }}</p>
                                        </div>
                                    </div>

                                </div>

                                <div class="mt-auto">
                                    <a href="{{ route('praktikan.penugasan.show', $p->id) }}" 
                                        class="w-full h-10 bg-[#001f3f] text-white text-[10px] font-bold uppercase tracking-widest rounded-lg shadow-lg shadow-[#001f3f]/10 hover:bg-[#002d5a] transition-all flex items-center justify-center gap-2 group-active:scale-95">
                                        Lihat Soal
                                        <i class="fas fa-arrow-right text-[10px]"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
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
                        $studentPendaftaran = $activePendaftarans->firstWhere('praktikum_id', $jadwal->praktikum_id);
                        $studentSesi = $studentPendaftaran ? $studentPendaftaran->sesi : null;

                        // Use session times if available, otherwise fallback to schedule times
                        $waktuMulai = $studentSesi ? $studentSesi->jam_mulai : $jadwal->waktu_mulai;
                        $waktuSelesai = $studentSesi ? $studentSesi->jam_selesai : $jadwal->waktu_selesai;

                        $start = \Carbon\Carbon::parse($jadwal->tanggal . ' ' . $waktuMulai);
                        $end = \Carbon\Carbon::parse($jadwal->tanggal . ' ' . $waktuSelesai);

                        $isFinished = $now->greaterThan($end);
                        $isOngoing = $now->between($start, $end);
                        $isCorrectDay = true;

                        // Additional check: Does the session day match the schedule date?
                        if ($studentSesi && $studentSesi->hari) {
                            $daysMap = [
                                'Minggu' => 0,
                                'Senin' => 1,
                                'Selasa' => 2,
                                'Rabu' => 3,
                                'Kamis' => 4,
                                'Jumat' => 5,
                                'Sabtu' => 6,
                            ];
                            $sessionDayIndex = $daysMap[$studentSesi->hari] ?? null;
                            $jadwalDayIndex = \Carbon\Carbon::parse($jadwal->tanggal)->dayOfWeek;

                            if ($sessionDayIndex !== null && $sessionDayIndex !== $jadwalDayIndex) {
                                $isCorrectDay = false;
                            }
                        }

                        $statusLabel = 'Aktif';
                        $statusClass = 'bg-emerald-50 text-emerald-600 border-emerald-100';
                        $hasPresensi = $jadwal->presensis->isNotEmpty();

                        if ($hasPresensi) {
                            $statusLabel = 'Hadir';
                            $statusClass = 'bg-emerald-500 text-white border-emerald-400';
                        } elseif ($isFinished) {
                            $statusLabel = 'Selesai';
                            $statusClass = 'bg-slate-100 text-slate-500 border-slate-200';
                        } elseif ($isOngoing && $isCorrectDay) {
                            $statusLabel = 'Berlangsung';
                            $statusClass = 'bg-amber-50 text-amber-600 border-amber-100 animate-pulse';
                        } elseif (!$isCorrectDay) {
                            $statusLabel = 'Bukan Sesi Anda';
                            $statusClass = 'bg-rose-50 text-rose-600 border-rose-100';
                        }
                    @endphp
                    <div
                        class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm hover:border-emerald-500/50 transition-all group {{ $isFinished && !$hasPresensi ? 'opacity-70' : '' }} {{ !$isCorrectDay ? 'grayscale-[0.5]' : '' }}">
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
                                <span class="text-[10px] font-bold text-slate-700">
                                    @if ($studentSesi)
                                        {{ substr($studentSesi->jam_mulai, 0, 5) }} - {{ substr($studentSesi->jam_selesai, 0, 5) }} WIB
                                        <span class="text-[8px] opacity-50 ml-1">({{ $studentSesi->nama_sesi }})</span>
                                    @else
                                        {{ substr($jadwal->waktu_mulai, 0, 5) }} - {{ substr($jadwal->waktu_selesai, 0, 5) }} WIB
                                    @endif
                                </span>
                            </div>
                            @if ($isFinished && !$hasPresensi)
                                <div
                                    class="mt-2 text-[8px] font-black text-rose-500 uppercase tracking-widest border-t border-dashed border-rose-100 pt-2 animate-bounce">
                                    <i class="fas fa-info-circle mr-1"></i> Sesi Telah Berakhir
                                </div>
                            @elseif(!$isCorrectDay)
                                <div
                                    class="mt-2 text-[8px] font-black text-rose-500 uppercase tracking-widest border-t border-dashed border-rose-100 pt-2">
                                    <i class="fas fa-exclamation-circle mr-1"></i> Sesuaikan dengan {{ $studentSesi->hari }}
                                </div>
                            @else
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-location-dot text-[10px] text-slate-400"></i>
                                    <span
                                        class="text-[10px] font-bold text-slate-700">{{ $jadwal->ruangan ?? 'Laboratorium RPL' }}</span>
                                </div>
                            @endif
                        </div>

                        @if ($hasPresensi)
                            <div class="mt-4">
                                <div
                                    class="w-full py-2 bg-emerald-50 border border-emerald-200 text-emerald-600 text-[10px] font-black uppercase tracking-widest rounded-xl flex items-center justify-center gap-2">
                                    <i class="fas fa-check-circle"></i>
                                    Sudah Presensi
                                </div>
                            </div>
                        @elseif ($isOngoing && $isCorrectDay)
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
