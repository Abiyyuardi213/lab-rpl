@extends('layouts.admin')

@section('title', 'Dashboard Praktikan')

@section('content')
    @php
        $pendaftarans = Auth::user()->praktikan->pendaftarans;
        $hasPendaftaran = $pendaftarans->isNotEmpty();
        $hasVerified = $pendaftarans->where('status', 'verified')->isNotEmpty();
        $isFirstTimer = !$hasVerified;
    @endphp

    <div class="space-y-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Dashboard Mahasiswa</h1>
                <p class="text-slate-500 mt-1 text-sm sm:text-base">Selamat datang kembali, pantau aktivitas praktikum Anda di sini.</p>
            </div>
            <div class="flex items-center gap-3">
                <span
                    class="inline-flex items-center px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-100 shadow-sm" title="Status akun Anda aktif dan siap digunakan">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                    Akun Aktif
                </span>
            </div>
        </div>

        @if($isFirstTimer)
            @if(!$hasPendaftaran)
                <!-- Phase 1: Onboarding Banner (Belum Daftar) -->
                <div id="tour-step-1" class="bg-gradient-to-br from-[#001f3f] to-[#003366] rounded-3xl p-6 md:p-8 text-white shadow-xl relative overflow-hidden group">
                    <div class="absolute right-0 top-0 opacity-10 transform translate-x-1/4 -translate-y-1/4 transition-transform group-hover:scale-110 duration-700 pointer-events-none">
                        <i class="fas fa-rocket text-9xl"></i>
                    </div>
                    <div class="relative z-10">
                        <div class="inline-flex items-center px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-[10px] font-bold tracking-widest uppercase mb-4 border border-white/20 shadow-sm">
                            <i class="fas fa-info-circle mr-2"></i> Panduan Pengguna Baru
                        </div>
                        <h2 class="text-2xl md:text-3xl font-bold mb-2">Langkah Pertama Anda 👋</h2>
                        <p class="text-blue-100 mb-6 max-w-2xl text-sm md:text-base leading-relaxed">Tampaknya Anda belum terdaftar di praktikum manapun. Ikuti 3 langkah mudah berikut untuk memulai perjalanan akademis Anda bersama kami.</p>
                        
                        <!-- Progress Tracker Gamifikasi -->
                        <div class="flex items-center gap-2 mb-8 overflow-x-auto pb-2 scrollbar-hide">
                            <span class="px-3 py-1.5 bg-emerald-500/20 border border-emerald-400/30 rounded-full text-[10px] md:text-xs font-bold whitespace-nowrap"><i class="fas fa-check-circle text-emerald-400 mr-1"></i> 1. Akun Dibuat</span>
                            <div class="w-4 h-px bg-white/30 shrink-0"></div>
                            <span class="px-3 py-1.5 bg-white/5 border border-white/10 rounded-full text-[10px] md:text-xs font-bold text-white/60 whitespace-nowrap"><i class="far fa-circle mr-1"></i> 2. Pilih Praktikum</span>
                            <div class="w-4 h-px bg-white/30 shrink-0"></div>
                            <span class="px-3 py-1.5 bg-white/5 border border-white/10 rounded-full text-[10px] md:text-xs font-bold text-white/60 whitespace-nowrap"><i class="far fa-circle mr-1"></i> 3. Verifikasi Admin</span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-5 border border-white/10 hover:bg-white/10 transition-colors">
                                <div class="w-10 h-10 rounded-xl bg-white text-[#001f3f] flex items-center justify-center font-black text-lg mb-4 shadow-lg">1</div>
                                <h3 class="font-bold text-lg mb-2">Eksplorasi</h3>
                                <p class="text-xs text-blue-100/80 leading-relaxed">Cari dan pilih praktikum yang sesuai dengan mata kuliah dan semester Anda saat ini di area Katalog.</p>
                            </div>
                            <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-5 border border-white/10 hover:bg-white/10 transition-colors">
                                <div class="w-10 h-10 rounded-xl bg-white text-[#001f3f] flex items-center justify-center font-black text-lg mb-4 shadow-lg">2</div>
                                <h3 class="font-bold text-lg mb-2">Mendaftar</h3>
                                <p class="text-xs text-blue-100/80 leading-relaxed">Klik tombol daftar dan lengkapi berkas persyaratan yang diminta oleh sistem sebelum kuota penuh.</p>
                            </div>
                            <div class="bg-white/5 backdrop-blur-sm rounded-2xl p-5 border border-white/10 hover:bg-white/10 transition-colors">
                                <div class="w-10 h-10 rounded-xl bg-white text-[#001f3f] flex items-center justify-center font-black text-lg mb-4 shadow-lg">3</div>
                                <h3 class="font-bold text-lg mb-2">Verifikasi</h3>
                                <p class="text-xs text-blue-100/80 leading-relaxed">Tunggu proses verifikasi dari Admin. Setelah disetujui, jadwal dan akses soal akan otomatis muncul.</p>
                            </div>
                        </div>
                        
                        <button onclick="document.getElementById('katalog-praktikum').scrollIntoView({behavior: 'smooth'})" class="inline-flex items-center gap-3 bg-white text-[#001f3f] px-6 py-3 rounded-xl font-black text-sm hover:bg-blue-50 transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                            Lihat Katalog Praktikum
                            <i class="fas fa-arrow-down animate-bounce"></i>
                        </button>
                    </div>
                </div>
            @else
                <!-- Phase 2: Onboarding Banner (Sudah Daftar, Menunggu Verifikasi) -->
                <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-3xl p-6 md:p-8 text-white shadow-xl relative overflow-hidden group">
                    <div class="absolute right-0 top-0 opacity-10 transform translate-x-1/4 -translate-y-1/4 transition-transform group-hover:scale-110 duration-700 pointer-events-none">
                        <i class="fas fa-hourglass-half text-9xl"></i>
                    </div>
                    <div class="relative z-10">
                        <div class="inline-flex items-center px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-[10px] font-bold tracking-widest uppercase mb-4 border border-white/20 shadow-sm">
                            <i class="fas fa-clock mr-2"></i> Sedang Diproses
                        </div>
                        <h2 class="text-2xl md:text-3xl font-bold mb-2">Pendaftaran Berhasil Diterima! 🎉</h2>
                        <p class="text-amber-50 mb-6 max-w-2xl text-sm md:text-base leading-relaxed">Pendaftaran praktikum Anda telah masuk antrean. Mohon tunggu proses verifikasi oleh Admin Laboratorium. Fitur Jadwal dan Soal Anda akan otomatis terbuka setelah disetujui.</p>

                        <!-- Progress Tracker Gamifikasi -->
                        <div class="flex items-center gap-2 mb-2 overflow-x-auto pb-2 scrollbar-hide">
                            <span class="px-3 py-1.5 bg-white/20 border border-white/30 rounded-full text-[10px] md:text-xs font-bold whitespace-nowrap"><i class="fas fa-check-circle text-emerald-200 mr-1"></i> 1. Akun Dibuat</span>
                            <div class="w-4 h-px bg-white/30 shrink-0"></div>
                            <span class="px-3 py-1.5 bg-white/20 border border-white/30 rounded-full text-[10px] md:text-xs font-bold whitespace-nowrap"><i class="fas fa-check-circle text-emerald-200 mr-1"></i> 2. Praktikum Dipilih</span>
                            <div class="w-4 h-px bg-white/30 shrink-0"></div>
                            <span class="px-3 py-1.5 bg-white/20 border border-white/30 rounded-full text-[10px] md:text-xs font-bold whitespace-nowrap animate-pulse shadow-[0_0_15px_rgba(255,255,255,0.3)]"><i class="fas fa-spinner fa-spin mr-1"></i> 3. Menunggu Verifikasi</span>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        <!-- Peringatan Asistensi Alert -->
        @php
            $pendingTasks = collect();
            if (isset($activePendaftarans)) {
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
            }
        @endphp

        @if ($pendingTasks->isNotEmpty())
            <div class="space-y-3">
                @foreach ($pendingTasks as $pt)
                    <div
                        class="bg-rose-50 border border-rose-200 border-l-4 border-l-rose-500 p-4 rounded-r-2xl rounded-l-md shadow-sm">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            <div
                                class="h-10 w-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-exclamation-circle text-lg"></i>
                            </div>
                            <div class="flex-grow">
                                <p class="text-sm font-bold text-rose-900">
                                    Aksi Diperlukan: Asistensi Tertunda
                                </p>
                                <p class="text-xs text-rose-700 mt-1 leading-relaxed">
                                    Silakan selesaikan tugas asistensi <span class="font-bold">"{{ $pt['modul'] }}"</span> untuk praktikum <span class="font-bold">{{ $pt['praktikum'] }}</span> agar Anda dapat mengikuti sesi berikutnya.
                                </p>
                            </div>
                            <a href="{{ route('praktikan.pendaftaran.progress', $pt['pendaftaran_id']) }}"
                                class="shrink-0 px-5 py-2.5 bg-rose-600 text-white text-xs font-bold rounded-xl hover:bg-rose-700 transition-colors shadow-sm text-center">
                                Kerjakan Sekarang
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div
                class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5 transition-all hover:shadow-md hover:border-blue-200 cursor-default">
                <div class="h-14 w-14 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-id-badge text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Status Mahasiswa</h3>
                    <p class="text-xl font-bold text-slate-900 mb-1">Terdaftar Aktif</p>
                    <span class="text-[10px] font-bold bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded uppercase tracking-widest border border-emerald-200" title="Akun Anda terverifikasi oleh sistem">Verified</span>
                </div>
            </div>

            <div
                class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5 transition-all hover:shadow-md hover:border-purple-200 cursor-default">
                <div class="h-14 w-14 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-microscope text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Praktikum Diikuti</h3>
                    <p class="text-xl font-bold text-slate-900 mb-1">
                        {{ Auth::user()->praktikan->pendaftarans()->where('status', 'verified')->count() }} <span class="text-sm font-medium text-slate-500 normal-case">Mata Kuliah</span>
                    </p>
                    <p class="text-[10px] text-slate-500 font-medium">Praktikum yang telah terverifikasi</p>
                </div>
            </div>

            <div
                class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5 transition-all hover:shadow-md hover:border-orange-200 cursor-default">
                <div class="h-14 w-14 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center shrink-0">
                    <i class="far fa-calendar-check text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Jadwal Mendatang</h3>
                    <p class="text-xl font-bold text-slate-900 mb-1">{{ isset($upcomingSchedules) ? $upcomingSchedules->count() : 0 }} <span class="text-sm font-medium text-slate-500 normal-case">Sesi</span></p>
                    <p class="text-[10px] text-slate-500 font-medium">Jangan lewatkan agenda Anda</p>
                </div>
            </div>
        </div>

        <!-- Progress Asistensi Section -->
        <div id="tour-step-3">
            <div class="flex items-center gap-3 mb-4">
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">Progress Asistensi Anda</h2>
                <span class="px-2 py-1 bg-slate-100 text-slate-600 text-[10px] font-bold rounded-md" title="Total praktikum aktif saat ini">{{ isset($activePendaftarans) ? $activePendaftarans->count() : 0 }} Aktif</span>
            </div>

            @if (isset($activePendaftarans) && $activePendaftarans->isNotEmpty())
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach ($activePendaftarans as $ap)
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col transition-all hover:shadow-md">
                            <div class="p-5 border-b border-slate-100">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h4 class="text-base font-bold text-slate-900 line-clamp-1">{{ $ap->praktikum->nama_praktikum }}</h4>
                                        <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5" title="Asisten Laboratorium Anda untuk praktikum ini">
                                            <i class="fas fa-user-tie text-slate-400"></i> Aslab: <span class="font-semibold text-slate-700">{{ $ap->aslab->user->name ?? 'Belum Ditugaskan' }}</span>
                                        </p>
                                    </div>
                                    <div class="text-right pl-3">
                                        <span class="text-2xl font-black {{ $ap->progress_percentage == 100 ? 'text-emerald-500' : 'text-[#001f3f]' }} tracking-tighter">{{ $ap->progress_percentage }}%</span>
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <div class="space-y-2 mb-2">
                                    <div class="h-2.5 w-full bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full {{ $ap->progress_percentage == 100 ? 'bg-emerald-500' : 'bg-[#001f3f]' }} rounded-full transition-all duration-1000 ease-out relative"
                                            style="width: {{ $ap->progress_percentage }}%">
                                            @if($ap->progress_percentage > 0 && $ap->progress_percentage < 100)
                                                <div class="absolute top-0 right-0 bottom-0 left-0 bg-white/20 animate-pulse"></div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center text-[10px] font-medium text-slate-500">
                                        <span>Kehadiran: <span class="font-bold text-slate-700">{{ $ap->presensis_count }} Sesi</span></span>
                                        <span>Target: <span class="font-bold text-slate-700">{{ $ap->praktikum->jumlah_modul + ($ap->praktikum->ada_tugas_akhir ? 1 : 0) }} Sesi</span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-5 bg-slate-50/50 flex-grow">
                                <p class="text-xs font-bold text-slate-700 mb-3">Timeline Tugas Asistensi</p>
                                
                                <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-hide">
                                    @forelse($ap->tugasAsistensis as $idx => $t)
                                        <div class="flex items-center flex-none group" title="{{ $t->judul }} - {{ ucfirst($t->status) }}">
                                            <div class="relative flex flex-col items-center gap-2 min-w-[60px]">
                                                @php
                                                    $statusColor = match ($t->status) {
                                                        'reviewed' => 'bg-emerald-100 text-emerald-600 border-emerald-200',
                                                        'submitted' => 'bg-amber-100 text-amber-600 border-amber-200',
                                                        default => 'bg-white text-slate-400 border-slate-200',
                                                    };
                                                    $icon = match ($t->status) {
                                                        'reviewed' => 'fa-check',
                                                        'submitted' => 'fa-arrow-up',
                                                        default => 'fa-clock',
                                                    };
                                                @endphp
                                                <div class="w-8 h-8 rounded-full flex items-center justify-center border {{ $statusColor }} transition-all shadow-sm">
                                                    <i class="fas {{ $icon }} text-xs"></i>
                                                </div>
                                                <span class="text-[9px] font-medium text-slate-500 truncate max-w-[60px]">{{ $t->judul }}</span>
                                            </div>
                                            @if (!$loop->last)
                                                <div class="w-8 h-px {{ $t->status == 'reviewed' ? 'bg-emerald-300' : 'bg-slate-200' }} -mt-4"></div>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="flex flex-col items-center justify-center py-4 px-2 w-full text-center border-2 border-dashed border-slate-200 rounded-xl bg-white">
                                            <div class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center text-blue-400 mb-2">
                                                <i class="fas fa-file-signature"></i>
                                            </div>
                                            <p class="text-xs font-bold text-slate-600">Belum Ada Tugas</p>
                                            <p class="text-[10px] text-slate-500 mt-1 max-w-[250px] leading-relaxed">Asisten laboratorium akan memberikan penugasan (asistensi) setelah sesi praktikum Anda dimulai.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                            
                            <div class="p-4 border-t border-slate-100 bg-white">
                                <a href="{{ route('praktikan.pendaftaran.progress', $ap->id) }}"
                                    class="w-full py-2.5 bg-white border border-slate-200 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-colors flex items-center justify-center gap-2">
                                    Lihat Detail Asistensi
                                    <i class="fas fa-chevron-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State Progress -->
                <div class="bg-white rounded-2xl border border-slate-200 border-dashed shadow-sm p-8 md:p-12 flex flex-col items-center justify-center text-center">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-5 border border-slate-100">
                        <i class="fas fa-tasks text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800">Belum Ada Progress</h3>
                    <p class="text-slate-500 mt-2 max-w-md text-sm leading-relaxed">Pantau perkembangan tugas asistensi Anda di sini. Progress akan otomatis muncul setelah pendaftaran praktikum diverifikasi oleh admin.</p>
                </div>
            @endif
        </div>

        <!-- Upcoming Schedules -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden" id="tour-step-4">
            <div id="toggle-jadwal-btn" class="p-5 border-b border-slate-100 flex items-center justify-between cursor-pointer hover:bg-slate-50 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 tracking-tight">Jadwal Praktikum Mendatang</h2>
                        <p class="text-xs text-slate-500">Lihat sesi praktikum yang harus Anda hadiri</p>
                    </div>
                </div>
                <button id="jadwal-chevron" class="w-8 h-8 rounded-full hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-transform duration-300">
                    <i class="fas fa-chevron-up"></i>
                </button>
            </div>

            <div id="jadwal-content" class="transition-all duration-300 origin-top">
                <div class="p-6 bg-slate-50/50">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                        @forelse(isset($upcomingSchedules) ? $upcomingSchedules : [] as $jadwal)
                            @php
                                $now = \Carbon\Carbon::now();
                                $studentPendaftaran = isset($activePendaftarans) ? $activePendaftarans->firstWhere('praktikum_id', $jadwal->praktikum_id) : null;
                                $studentSesi = $studentPendaftaran ? $studentPendaftaran->sesi : null;

                                $waktuMulai = $studentSesi ? $studentSesi->jam_mulai : $jadwal->waktu_mulai;
                                $waktuSelesai = $studentSesi ? $studentSesi->jam_selesai : $jadwal->waktu_selesai;

                                $start = \Carbon\Carbon::parse($jadwal->tanggal . ' ' . $waktuMulai);
                                $end = \Carbon\Carbon::parse($jadwal->tanggal . ' ' . $waktuSelesai);

                                $isFinished = $now->greaterThan($end);
                                $isOngoing = $now->between($start, $end);
                                $isCorrectDay = true;

                                if ($studentSesi && $studentSesi->hari) {
                                    $daysMap = ['Minggu'=>0,'Senin'=>1,'Selasa'=>2,'Rabu'=>3,'Kamis'=>4,'Jumat'=>5,'Sabtu'=>6];
                                    $sessionDayIndex = $daysMap[$studentSesi->hari] ?? null;
                                    $jadwalDayIndex = \Carbon\Carbon::parse($jadwal->tanggal)->dayOfWeek;
                                    if ($sessionDayIndex !== null && $sessionDayIndex !== $jadwalDayIndex) {
                                        $isCorrectDay = false;
                                    }
                                }

                                $statusLabel = 'Akan Datang';
                                $statusClass = 'bg-blue-100 text-blue-700';
                                $hasPresensi = $jadwal->presensis->isNotEmpty();

                                if ($hasPresensi) {
                                    $statusLabel = 'Hadir';
                                    $statusClass = 'bg-emerald-100 text-emerald-700';
                                } elseif ($isFinished) {
                                    $statusLabel = 'Selesai';
                                    $statusClass = 'bg-slate-200 text-slate-600';
                                } elseif ($isOngoing && $isCorrectDay) {
                                    $statusLabel = 'Berlangsung Sekarang';
                                    $statusClass = 'bg-rose-100 text-rose-700 font-bold animate-pulse';
                                } elseif (!$isCorrectDay) {
                                    $statusLabel = 'Beda Hari Sesi';
                                    $statusClass = 'bg-amber-100 text-amber-700';
                                }
                            @endphp
                            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all relative overflow-hidden group {{ $isFinished && !$hasPresensi ? 'opacity-70' : '' }}">
                                @if($isOngoing && $isCorrectDay && !$hasPresensi)
                                    <div class="absolute top-0 left-0 w-1 h-full bg-rose-500"></div>
                                @endif
                                
                                <div class="flex justify-between items-start mb-3">
                                    <span class="text-[10px] font-bold {{ $statusClass }} px-2 py-1 rounded-md">
                                        {{ $statusLabel }}
                                    </span>
                                    <span class="text-xs font-bold text-slate-400" title="Kode Praktikum">{{ $jadwal->praktikum->kode_praktikum }}</span>
                                </div>
                                
                                <h4 class="text-base font-bold text-slate-900 line-clamp-2 mb-1 group-hover:text-emerald-600 transition-colors">{{ $jadwal->judul_modul }}</h4>
                                <p class="text-xs font-medium text-slate-500 mb-4 line-clamp-1">{{ $jadwal->praktikum->nama_praktikum }}</p>

                                <div class="space-y-2.5">
                                    <div class="flex items-center gap-3 text-sm">
                                        <div class="w-6 h-6 rounded bg-slate-50 flex items-center justify-center text-slate-400">
                                            <i class="far fa-calendar"></i>
                                        </div>
                                        <span class="font-medium text-slate-700">{{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('l, d M Y') }}</span>
                                    </div>
                                    <div class="flex items-center gap-3 text-sm">
                                        <div class="w-6 h-6 rounded bg-slate-50 flex items-center justify-center text-slate-400">
                                            <i class="far fa-clock"></i>
                                        </div>
                                        <span class="font-medium text-slate-700 tabular-nums">
                                            {{ substr($waktuMulai, 0, 5) }} - {{ substr($waktuSelesai, 0, 5) }} WIB
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-3 text-sm">
                                        <div class="w-6 h-6 rounded bg-slate-50 flex items-center justify-center text-slate-400">
                                            <i class="fas fa-location-dot"></i>
                                        </div>
                                        <span class="font-medium text-slate-700">{{ $jadwal->ruangan ?? 'Lab. Komputer' }}</span>
                                    </div>
                                </div>

                                <div class="mt-5 pt-4 border-t border-slate-100">
                                    @if ($hasPresensi)
                                        <div class="w-full py-2.5 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-xl flex items-center justify-center gap-2 border border-emerald-200">
                                            <i class="fas fa-check-circle"></i> Kehadiran Tercatat
                                        </div>
                                    @elseif ($isOngoing && $isCorrectDay)
                                        <a href="{{ route('praktikan.presensi.generate-qr', $jadwal->id) }}" class="w-full py-2.5 bg-emerald-600 text-white text-xs font-bold rounded-xl shadow-md hover:bg-emerald-700 hover:shadow-lg transition-all flex items-center justify-center gap-2">
                                            <i class="fas fa-qrcode"></i> Isi Presensi Sekarang
                                        </a>
                                    @elseif ($isFinished && !$hasPresensi)
                                        <p class="text-xs text-rose-500 font-medium text-center"><i class="fas fa-times-circle mr-1"></i> Tidak Hadir / Sesi Berakhir</p>
                                    @else
                                        <p class="text-xs text-slate-500 font-medium text-center">Tombol presensi akan muncul saat sesi dimulai</p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-10 text-center bg-white border border-slate-200 border-dashed rounded-2xl">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mx-auto mb-4 shadow-sm border border-slate-100">
                                    <i class="far fa-calendar-check text-3xl"></i>
                                </div>
                                <h3 class="font-bold text-lg text-slate-700">Jadwal Kosong</h3>
                                <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">Saat ini belum ada sesi praktikum yang harus Anda hadiri. Jadwal akan otomatis tampil di sini ketika Admin sudah mengatur jadwal untuk Anda.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Penugasan / Soal Praktikum Aktif -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden" id="tour-step-5">
            <div id="toggle-soal-btn" class="p-5 border-b border-slate-100 flex items-center justify-between cursor-pointer hover:bg-slate-50 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center">
                        <i class="fas fa-file-invoice text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 tracking-tight">Soal Praktikum Aktif</h2>
                        <p class="text-xs text-slate-500">Kerjakan soal yang telah dibuka untuk sesi Anda</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('praktikan.penugasan.index') }}" class="text-sm font-bold text-blue-600 hover:text-blue-800">Lihat Semua</a>
                    <button id="soal-chevron" class="w-8 h-8 rounded-full hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-transform duration-300">
                        <i class="fas fa-chevron-up"></i>
                    </button>
                </div>
            </div>

            <div id="soal-content" class="transition-all duration-300 origin-top">
                <div class="p-6 bg-slate-50/50">
                    @if(isset($penugasans) && $penugasans->isNotEmpty())
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($penugasans->filter(fn($p) => $p->is_accessible)->take(3) as $p)
                                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col group hover:shadow-md transition-all">
                                    <div class="p-5 border-b border-slate-100 flex items-start justify-between bg-gradient-to-br from-slate-50 to-white">
                                        <div>
                                            <span class="inline-block px-2.5 py-1 bg-blue-100 text-blue-700 text-[10px] font-bold rounded-md mb-2">
                                                {{ $p->praktikum->kode_praktikum }}
                                            </span>
                                            <h3 class="font-bold text-slate-900 line-clamp-2 leading-snug">{{ $p->judul }}</h3>
                                        </div>
                                        <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center shrink-0">
                                            <i class="fas fa-pen text-sm"></i>
                                        </div>
                                    </div>
                                    <div class="p-5 flex-grow space-y-3">
                                        <p class="text-xs font-medium text-slate-600 line-clamp-1"><i class="fas fa-book mr-2 text-slate-400"></i>{{ $p->praktikum->nama_praktikum }}</p>
                                        <p class="text-xs font-medium text-slate-600"><i class="fas fa-calendar-day mr-2 text-slate-400"></i>Hari Sesi: <span class="font-bold">{{ $p->sesi->hari }}</span></p>
                                        <p class="text-xs font-medium text-slate-600"><i class="fas fa-clock mr-2 text-slate-400"></i>Akses: <span class="font-bold tabular-nums">{{ substr($p->sesi->jam_mulai, 0, 5) }} - {{ substr($p->sesi->jam_selesai, 0, 5) }}</span></p>
                                    </div>
                                    <div class="p-4 border-t border-slate-100">
                                        <a href="{{ route('praktikan.penugasan.show', $p->id) }}" class="w-full py-2 bg-[#001f3f] text-white text-xs font-bold rounded-xl hover:bg-blue-900 transition-colors flex items-center justify-center gap-2">
                                            Buka Soal <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- Empty State Soal -->
                        <div class="py-10 flex flex-col items-center justify-center text-center bg-white border border-slate-200 border-dashed rounded-2xl">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-4 shadow-sm border border-slate-100">
                                <i class="fas fa-file-invoice text-3xl"></i>
                            </div>
                            <h3 class="font-bold text-lg text-slate-700">Tidak Ada Soal Aktif</h3>
                            <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">Belum ada penugasan atau soal ujian praktikum yang sedang dibuka untuk sesi Anda.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Available Practicum Section -->
        <div id="katalog-praktikum" class="pt-4 pb-10">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Katalog Praktikum</h2>
                    <p class="text-sm text-slate-500 mt-1">Daftar praktikum yang tersedia untuk Anda ikuti.</p>
                </div>
                <!-- Search Filter -->
                <div class="relative max-w-sm w-full sm:w-auto">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                    <input type="text" id="search-katalog-input" placeholder="Cari nama praktikum..." class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all shadow-sm">
                </div>
            </div>

            @if (isset($praktikums) && $praktikums->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="katalog-container">
                    @foreach ($praktikums as $p)
                        <div class="katalog-card bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col group transition-all hover:shadow-lg hover:-translate-y-1" data-name="{{ strtolower($p->nama_praktikum) }}">
                            
                            <div class="p-5 border-b border-slate-100 relative">
                                @php
                                    $statusConfig = [
                                        'open_registration' => ['label' => 'Buka Pendaftaran', 'class' => 'bg-emerald-100 text-emerald-700', 'icon' => 'fa-door-open'],
                                        'on_progress' => ['label' => 'Sedang Berlangsung', 'class' => 'bg-blue-100 text-blue-700', 'icon' => 'fa-running'],
                                        'finished' => ['label' => 'Selesai', 'class' => 'bg-slate-100 text-slate-600', 'icon' => 'fa-check-circle'],
                                    ];
                                    $currentStatus = $statusConfig[$p->status_praktikum] ?? ['label' => $p->status_praktikum, 'class' => 'bg-slate-100 text-slate-700', 'icon' => 'fa-info-circle'];
                                @endphp
                                
                                <div class="flex items-center justify-between mb-3">
                                    <span class="px-2 py-1 bg-slate-100 text-slate-600 text-[10px] font-bold rounded-md uppercase tracking-wider" title="Kode Praktikum">
                                        {{ $p->kode_praktikum }}
                                    </span>
                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-md flex items-center gap-1.5 {{ $currentStatus['class'] }}">
                                        <i class="fas {{ $currentStatus['icon'] }}"></i> {{ $currentStatus['label'] }}
                                    </span>
                                </div>
                                
                                <h3 class="font-bold text-slate-900 text-base leading-snug line-clamp-2 mb-1 group-hover:text-blue-600 transition-colors">{{ $p->nama_praktikum }}</h3>
                                <p class="text-xs text-slate-500"><i class="far fa-calendar-alt mr-1"></i> {{ $p->periode_praktikum }}</p>
                            </div>

                            <div class="p-5 flex-grow space-y-4">
                                <div class="flex justify-between items-end">
                                    <div>
                                        <p class="text-[10px] text-slate-500 uppercase tracking-wider font-bold mb-1" title="Kapasitas maksimum mahasiswa">Kuota Pendaftar</p>
                                        <p class="text-sm font-bold text-slate-800"><i class="fas fa-users text-slate-400 mr-1.5"></i> {{ $p->pendaftarans_count }} / {{ $p->kuota_praktikan }}</p>
                                    </div>
                                    @php
                                        $percent = $p->kuota_praktikan > 0 ? ($p->pendaftarans_count / $p->kuota_praktikan) * 100 : 0;
                                        $percent = min($percent, 100);
                                        $colorClass = $percent >= 100 ? 'bg-rose-500' : ($percent >= 80 ? 'bg-amber-500' : 'bg-emerald-500');
                                    @endphp
                                    <div class="text-right">
                                        <p class="text-[10px] text-slate-500 uppercase tracking-wider font-bold mb-1">Terisi</p>
                                        <p class="text-sm font-bold {{ str_replace('bg-', 'text-', $colorClass) }}">{{ round($percent) }}%</p>
                                    </div>
                                </div>
                                
                                <!-- Mini progress bar -->
                                <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full {{ $colorClass }} rounded-full" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                            
                            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                                @if ($p->pendaftarans->isNotEmpty())
                                    <div class="w-full py-2.5 bg-white border border-slate-200 text-slate-500 text-xs font-bold rounded-xl text-center flex items-center justify-center gap-2" title="Anda telah terdaftar di praktikum ini">
                                        <i class="fas fa-check-circle text-emerald-500"></i> Anda Terdaftar
                                    </div>
                                @elseif($percent >= 100)
                                    <div class="w-full py-2.5 bg-rose-50 border border-rose-100 text-rose-600 text-xs font-bold rounded-xl text-center flex items-center justify-center gap-2 cursor-not-allowed" title="Kuota pendaftar telah penuh">
                                        <i class="fas fa-lock text-rose-400"></i> Kuota Penuh
                                    </div>
                                @elseif($p->status_praktikum == 'open_registration')
                                    <a href="{{ route('praktikan.pendaftaran.create', $p->id) }}" class="w-full py-2.5 bg-[#001f3f] text-white text-xs font-bold rounded-xl text-center shadow-md hover:bg-blue-900 transition-colors flex items-center justify-center gap-2 group-hover:shadow-lg">
                                        Daftar Praktikum <i class="fas fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                                    </a>
                                @else
                                    <div class="w-full py-2.5 bg-slate-200 text-slate-500 text-xs font-bold rounded-xl text-center flex items-center justify-center gap-2 cursor-not-allowed" title="Pendaftaran telah ditutup oleh admin">
                                        Pendaftaran Ditutup
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <!-- Empty state for search -->
                <div id="search-empty-state" class="hidden py-12 bg-white rounded-2xl border border-slate-200 border-dashed flex flex-col items-center justify-center text-center">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-4 border border-slate-100">
                        <i class="fas fa-search text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Praktikum Tidak Ditemukan</h3>
                    <p class="text-slate-500 mt-2 max-w-sm text-sm">Coba gunakan kata kunci lain untuk mencari praktikum.</p>
                </div>
            @else
                <div class="py-12 bg-white rounded-2xl border border-slate-200 border-dashed flex flex-col items-center justify-center text-center">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-4 border border-slate-100">
                        <i class="fas fa-calendar-times text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Belum Ada Praktikum</h3>
                    <p class="text-slate-500 mt-2 max-w-sm text-sm">Saat ini belum ada jadwal praktikum yang dibuka. Kami akan menginformasikan jika pendaftaran telah dibuka kembali.</p>
                </div>
            @endif
        </div>
    </div>

@push('scripts')
<!-- Tambahkan pustaka Intro.js via CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/introjs.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/intro.min.js"></script>

<style>
/* Custom style untuk Intro.js agar terlihat lebih modern */
.introjs-tooltip {
    border-radius: 16px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
    font-family: inherit;
}
.introjs-tooltipbuttons {
    display: flex;
    gap: 8px;
    justify-content: flex-end;
}
.introjs-button {
    border-radius: 8px;
    font-weight: bold;
    padding: 8px 16px;
    text-shadow: none;
    background-image: none;
}
.introjs-nextbutton {
    background-color: #001f3f !important;
    color: white !important;
    border: none;
}
.introjs-prevbutton {
    background-color: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ------------------------------------
    // TOGGLE SECTIONS
    // ------------------------------------
    const toggleJadwalBtn = document.getElementById('toggle-jadwal-btn');
    const jadwalContent = document.getElementById('jadwal-content');
    const jadwalChevron = document.getElementById('jadwal-chevron');

    if (toggleJadwalBtn && jadwalContent) {
        toggleJadwalBtn.addEventListener('click', function() {
            jadwalContent.classList.toggle('hidden');
            if (jadwalChevron) {
                jadwalChevron.classList.toggle('rotate-180');
            }
        });
    }

    const toggleSoalBtn = document.getElementById('toggle-soal-btn');
    const soalContent = document.getElementById('soal-content');
    const soalChevron = document.getElementById('soal-chevron');

    if (toggleSoalBtn && soalContent) {
        toggleSoalBtn.addEventListener('click', function(e) {
            if (e.target.tagName.toLowerCase() === 'a') return;
            soalContent.classList.toggle('hidden');
            if (soalChevron) {
                soalChevron.classList.toggle('rotate-180');
            }
        });
    }

    // ------------------------------------
    // SEARCH KATALOG
    // ------------------------------------
    const searchInput = document.getElementById('search-katalog-input');
    const cards = document.querySelectorAll('.katalog-card');
    const emptyState = document.getElementById('search-empty-state');

    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            let hasVisibleCard = false;

            cards.forEach(card => {
                const name = card.getAttribute('data-name');
                if (name.includes(searchTerm)) {
                    card.style.display = 'flex';
                    hasVisibleCard = true;
                } else {
                    card.style.display = 'none';
                }
            });

            if (emptyState) {
                if (!hasVisibleCard && searchTerm !== '') {
                    emptyState.classList.remove('hidden');
                } else {
                    emptyState.classList.add('hidden');
                }
            }
        });
    }

    // ------------------------------------
    // INTERACTIVE PRODUCT TOUR (INTRO.JS)
    // ------------------------------------
    const isFirstTimer = {{ $isFirstTimer ? 'true' : 'false' }};
    const hasPendaftaran = {{ $hasPendaftaran ? 'true' : 'false' }};
    
    // Hanya jalankan tour jika user benar-benar baru (belum daftar) dan belum pernah menyelesaikan tour
    if (isFirstTimer && !hasPendaftaran && !localStorage.getItem('tourCompleted')) {
        setTimeout(() => {
            const tour = introJs();
            tour.setOptions({
                steps: [
                    {
                        title: '✨ Selamat Datang!',
                        intro: 'Mari kita mulai perjalanan akademis Anda di Laboratorium RPL. Ini adalah panduan singkat dashboard Anda.'
                    },
                    {
                        element: document.querySelector('#tour-step-1'),
                        title: 'Mulai Dari Sini',
                        intro: 'Anda dapat melihat status Anda saat ini dan panduan apa yang harus dilakukan selanjutnya.'
                    },
                    {
                        element: document.querySelector('#katalog-praktikum'),
                        title: 'Katalog Praktikum',
                        intro: 'Langkah terpenting: Cari dan daftar praktikum yang sesuai dengan mata kuliah Anda di bagian ini.'
                    },
                    {
                        element: document.querySelector('#tour-step-3'),
                        title: 'Pantau Tugas',
                        intro: 'Setelah Anda disetujui, semua progress nilai dan tugas asistensi Anda akan terekap di sini.'
                    },
                    {
                        element: document.querySelector('#tour-step-4'),
                        title: 'Jadwal Otomatis',
                        intro: 'Dan tidak perlu khawatir tertinggal! Jadwal yang harus Anda hadiri akan muncul otomatis di sini.',
                        position: 'top'
                    }
                ],
                dontShowAgain: true,
                dontShowAgainCookie: 'tourCompleted',
                showProgress: true,
                showBullets: false,
                nextLabel: 'Lanjut',
                prevLabel: 'Kembali',
                doneLabel: 'Selesai & Mulai',
                exitOnOverlayClick: false
            });

            tour.oncomplete(function() {
                localStorage.setItem('tourCompleted', 'true');
            });
            
            tour.onexit(function() {
                localStorage.setItem('tourCompleted', 'true');
            });

            tour.start();
        }, 1000); // Beri jeda 1 detik agar halaman ter-render sempurna
    }
});
</script>
@endpush
@endsection
