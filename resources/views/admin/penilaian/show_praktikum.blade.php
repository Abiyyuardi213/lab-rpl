@extends('layouts.admin')

@section('title', 'Kelola Penilaian - ' . $praktikum->nama_praktikum)

@section('content')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 uppercase">{{ $praktikum->nama_praktikum }}</h1>
                <p class="text-sm text-zinc-500 mt-1 font-medium italic">
                    Kelola penilaian praktikan yang dikelompokkan berdasarkan sesi dan modul.
                </p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500 bg-white px-3 py-1.5 rounded-lg border border-zinc-200 shadow-sm w-fit">
                <a href="{{ route('admin.penilaian.index') }}" class="hover:text-zinc-900 transition-colors">Penilaian</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">{{ $praktikum->kode_praktikum }}</span>
            </div>
        </div>

        <!-- Sesi Filter Tabs / Pills -->
        @if($praktikum->sesis->count() > 0)
            <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-zinc-200">
                <button type="button" 
                        onclick="switchSesiTab('all')"
                        id="tab-btn-all"
                        class="sesi-tab-btn inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all bg-zinc-900 text-white shadow-sm">
                    <span>Semua Sesi</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-white/20 text-white">
                        {{ $praktikum->sesis->count() }}
                    </span>
                </button>

                @foreach($praktikum->sesis as $sesi)
                    <button type="button" 
                            onclick="switchSesiTab('sesi-{{ $sesi->id }}')"
                            id="tab-btn-sesi-{{ $sesi->id }}"
                            class="sesi-tab-btn inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all bg-white text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 border border-zinc-200">
                        <span>{{ $sesi->nama_sesi }}</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-zinc-100 text-zinc-600 border border-zinc-200">
                            {{ $sesi->pendaftarans_count }} Mhs
                        </span>
                    </button>
                @endforeach

                @if($jadwalsBySesi->has('umum'))
                    <button type="button" 
                            onclick="switchSesiTab('sesi-umum')"
                            id="tab-btn-sesi-umum"
                            class="sesi-tab-btn inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all bg-white text-zinc-600 hover:text-zinc-900 hover:bg-zinc-100 border border-zinc-200">
                        <span>Umum / Lintas Sesi</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-zinc-100 text-zinc-600 border border-zinc-200">
                            {{ $jadwalsBySesi->get('umum')->count() }} Modul
                        </span>
                    </button>
                @endif
            </div>
        @endif

        <!-- Sesi Containers -->
        <div class="space-y-8" id="sesiContainers">
            @forelse($praktikum->sesis as $sesi)
                @php
                    $jadwals = $jadwalsBySesi->get($sesi->id, collect());
                @endphp
                <div class="sesi-section space-y-4" data-sesi-target="sesi-{{ $sesi->id }}">
                    <!-- Session Card Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 bg-zinc-50 border border-zinc-200 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-zinc-900 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h2 class="text-base font-bold text-zinc-900 uppercase tracking-tight">{{ $sesi->nama_sesi }}</h2>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ $sesi->hari }}
                                    </span>
                                </div>
                                <p class="text-xs text-zinc-500 font-medium mt-0.5">
                                    {{ substr($sesi->jam_mulai, 0, 5) }} - {{ substr($sesi->jam_selesai, 0, 5) }} WIB • Terdaftar: <strong class="text-zinc-800">{{ $sesi->pendaftarans_count }}</strong> / Kuota: {{ $sesi->kuota }} Mahasiswa
                                </p>
                            </div>
                        </div>
                        <div class="text-xs text-zinc-500 flex items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-white border border-zinc-200 text-zinc-700 shadow-xs">
                                <i class="fas fa-book-open text-zinc-400 mr-1.5"></i>
                                {{ $jadwals->count() }} Modul Terjadwal
                            </span>
                        </div>
                    </div>

                    <!-- Modul Cards Grid -->
                    @if($jadwals->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($jadwals as $jadwal)
                                @php
                                    $presensiCount = $jadwal->presensis->count();
                                    $dinilaiCount = $jadwal->presensis->filter(fn($p) => $p->penilaian !== null)->count();
                                    $totalMhs = max($sesi->pendaftarans_count, 1);
                                    $progressPercent = min(100, round(($dinilaiCount / $totalMhs) * 100));
                                @endphp
                                <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm hover:border-zinc-300 hover:shadow transition-all flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-[10px] font-black uppercase tracking-wider text-zinc-400">
                                                Modul {{ $loop->iteration }}
                                            </span>
                                            <span class="inline-flex items-center gap-1 text-[11px] font-medium text-zinc-500">
                                                <i class="fas fa-door-open text-zinc-400"></i>
                                                {{ $jadwal->ruangan ?? 'Lab RPL' }}
                                            </span>
                                        </div>

                                        <h3 class="text-base font-bold text-zinc-900 uppercase leading-snug line-clamp-2">
                                            {{ $jadwal->judul_modul }}
                                        </h3>

                                        <p class="text-xs font-medium text-zinc-500 mt-2 flex items-center gap-2">
                                            <i class="far fa-calendar-alt text-zinc-400"></i>
                                            <span>{{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('l, d F Y') }}</span>
                                        </p>
                                        <p class="text-xs font-medium text-zinc-500 mt-1 flex items-center gap-2">
                                            <i class="far fa-clock text-zinc-400"></i>
                                            <span>{{ substr($jadwal->waktu_mulai, 0, 5) }} - {{ substr($jadwal->waktu_selesai, 0, 5) }} WIB</span>
                                        </p>

                                        <!-- Progress Bar Penilaian -->
                                        <div class="mt-4 pt-3 border-t border-zinc-100">
                                            <div class="flex items-center justify-between text-[11px] font-medium mb-1">
                                                <span class="text-zinc-500">Kemajuan Nilai:</span>
                                                <span class="font-bold text-zinc-900">{{ $dinilaiCount }}/{{ $sesi->pendaftarans_count }} Mhs</span>
                                            </div>
                                            <div class="w-full bg-zinc-100 rounded-full h-1.5 overflow-hidden">
                                                <div class="bg-zinc-900 h-1.5 rounded-full transition-all duration-300" style="width: {{ $progressPercent }}%"></div>
                                            </div>
                                            <div class="flex items-center justify-between text-[10px] text-zinc-400 mt-1.5">
                                                <span>Presensi Hadir: {{ $presensiCount }}</span>
                                                <span>{{ $progressPercent }}% Selesai</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-5">
                                        <a href="{{ route('admin.penilaian.jadwal', $jadwal->id) }}" 
                                           class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-zinc-900 px-4 py-2 text-xs font-semibold text-white shadow hover:bg-zinc-800 transition-colors">
                                            <span>Kelola Nilai Modul Ini</span>
                                            <i class="fas fa-arrow-right text-[10px]"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 text-center bg-white border border-dashed border-zinc-200 rounded-xl text-zinc-400">
                            <i class="fas fa-calendar-times text-2xl mb-2 opacity-30"></i>
                            <p class="text-xs font-medium italic">Belum ada modul yang dijadwalkan untuk sesi ini.</p>
                        </div>
                    @endif
                </div>
            @empty
                <!-- Fallback jika praktikum belum membuat sesi sama sekali -->
                <div class="rounded-xl border border-zinc-200 bg-white p-8 text-center text-zinc-400">
                    <p class="text-sm font-medium">Praktikum ini belum memiliki sesi praktikum.</p>
                </div>
            @endforelse

            <!-- Sesi Umum / Lintas Sesi jika ada -->
            @if($jadwalsBySesi->has('umum'))
                @php
                    $jadwalsUmum = $jadwalsBySesi->get('umum');
                @endphp
                <div class="sesi-section space-y-4" data-sesi-target="sesi-umum">
                    <div class="flex items-center justify-between p-4 bg-zinc-50 border border-zinc-200 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-zinc-700 text-white flex items-center justify-center font-bold text-sm">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-zinc-900 uppercase">Jadwal Umum / Lintas Sesi</h2>
                                <p class="text-xs text-zinc-500 font-medium">Jadwal modul yang tidak terikat ke sesi praktikum tertentu</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($jadwalsUmum as $jadwal)
                            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm hover:border-zinc-300 transition-all flex flex-col justify-between">
                                <div>
                                    <h3 class="text-base font-bold text-zinc-900 uppercase leading-snug">{{ $jadwal->judul_modul }}</h3>
                                    <p class="text-xs font-medium text-zinc-500 mt-2 flex items-center gap-2">
                                        <i class="far fa-calendar-alt text-zinc-400"></i>
                                        <span>{{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('l, d F Y') }}</span>
                                    </p>
                                    <p class="text-xs font-medium text-zinc-500 mt-1 flex items-center gap-2">
                                        <i class="far fa-clock text-zinc-400"></i>
                                        <span>{{ substr($jadwal->waktu_mulai, 0, 5) }} - {{ substr($jadwal->waktu_selesai, 0, 5) }} WIB</span>
                                    </p>
                                </div>
                                <div class="mt-5">
                                    <a href="{{ route('admin.penilaian.jadwal', $jadwal->id) }}" 
                                       class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-zinc-900 px-4 py-2 text-xs font-semibold text-white shadow hover:bg-zinc-800 transition-colors">
                                        <span>Kelola Nilai</span>
                                        <i class="fas fa-arrow-right text-[10px]"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function switchSesiTab(targetId) {
            // Update button styles
            const buttons = document.querySelectorAll('.sesi-tab-btn');
            buttons.forEach(btn => {
                btn.classList.remove('bg-zinc-900', 'text-white', 'shadow-sm');
                btn.classList.add('bg-white', 'text-zinc-600', 'hover:bg-zinc-100');
            });

            const activeBtn = document.getElementById('tab-btn-' + targetId);
            if (activeBtn) {
                activeBtn.classList.remove('bg-white', 'text-zinc-600', 'hover:bg-zinc-100');
                activeBtn.classList.add('bg-zinc-900', 'text-white', 'shadow-sm');
            }

            // Show/hide session sections
            const sections = document.querySelectorAll('.sesi-section');
            sections.forEach(section => {
                if (targetId === 'all') {
                    section.classList.remove('hidden');
                } else {
                    if (section.getAttribute('data-sesi-target') === targetId) {
                        section.classList.remove('hidden');
                    } else {
                        section.classList.add('hidden');
                    }
                }
            });
        }
    </script>
@endsection
