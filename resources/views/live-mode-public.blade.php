@extends('layouts.app')

@section('title', 'Live Monitoring & Rekap Praktikum Masa ke Masa — Lab RPL ITATS')

@section('meta')
    <!-- ApexCharts CDN -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endsection

@section('content')
    <!-- Marquee Running Text Tepat di Bawah Navbar (Tanpa Container Pembatas) -->
    <div class="bg-[#0b0e14]/90 border-b border-slate-800/80 py-2.5 px-4 overflow-hidden backdrop-blur-md">
        <div
            class="max-w-screen-2xl mx-auto flex items-center gap-3 overflow-hidden text-xs font-semibold whitespace-nowrap">
            <div
                class="flex items-center gap-1.5 shrink-0 bg-emerald-500/20 text-emerald-400 px-3 py-1 rounded-full border border-emerald-500/30 text-[11px] font-extrabold uppercase tracking-wider">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                <i class="fas fa-users mr-1"></i> PRAKTIKAN TERDAFTAR ({{ $registeredPraktikans->count() }} MAHASISWA):
            </div>

            <div class="overflow-hidden relative w-full">
                <div
                    class="inline-flex items-center gap-8 animate-[marquee_400s_linear_infinite] hover:[animation-play-state:paused] min-w-full">
                    @for ($loopCount = 0; $loopCount < 2; $loopCount++)
                        @forelse($registeredPraktikans as $rp)
                            <div class="inline-flex items-center gap-2 text-slate-300 shrink-0">
                                <span
                                    class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-[10px] font-black">
                                    {{ strtoupper(substr($rp->praktikan->user->name ?? 'P', 0, 1)) }}
                                </span>
                                <span
                                    class="font-extrabold text-white">{{ $rp->praktikan->user->name ?? 'Mahasiswa' }}</span>
                                <span
                                    class="text-slate-400 text-[11px]">({{ $rp->praktikum->nama_praktikum ?? 'Praktikum' }})</span>
                                <span
                                    class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase @if ($rp->status === 'verified') bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 @elseif($rp->status === 'rejected') bg-rose-500/10 text-rose-400 border border-rose-500/20 @else bg-amber-500/10 text-amber-400 border border-amber-500/20 @endif">
                                    {{ $rp->status }}
                                </span>
                                <span class="text-slate-700 ml-3">|</span>
                            </div>
                        @empty
                            @if ($loopCount == 0)
                                <span class="text-slate-500 italic text-xs">Belum ada data praktikan terdaftar.</span>
                            @endif
                        @endforelse
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes marquee {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }
    </style>

    <div class="bg-[#07090e] min-h-screen text-slate-100 py-8 px-4 sm:px-6 lg:px-10">
        <div class="max-w-screen-2xl mx-auto space-y-6">

            <!-- Header Controls & Title Bar -->
            <div
                class="bg-[#0b0e14] border border-slate-800 rounded-2xl p-6 shadow-2xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 font-bold text-xl shadow-lg shadow-emerald-500/10">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white">Live Monitoring &
                                Rekapitulasi</h1>
                            <span
                                class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-[10px] font-extrabold border border-emerald-500/30 uppercase tracking-widest flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                LIVE REALTIME
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Metrik pendaftaran praktikan, grafik tren, serta rekapitulasi
                            data praktikum dari masa ke masa di Lab RPL ITATS</p>
                    </div>
                </div>

                <!-- Back to Home & Filter Tahun Form -->
                <div class="flex items-center gap-3 w-full md:w-auto justify-between md:justify-end">
                    <a href="{{ route('home') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-xs font-bold text-slate-300 hover:text-white hover:bg-slate-700 transition-all">
                        <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                    </a>

                    <form method="GET" action="{{ route('live-mode.public') }}" class="flex items-center gap-2">
                        <label for="year"
                            class="text-xs font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap">
                            <i class="fas fa-calendar-alt text-emerald-400 mr-1"></i> Tahun:
                        </label>
                        <select name="year" id="year" onchange="this.form.submit()"
                            class="bg-[#181c24] border border-slate-700 text-white text-xs font-bold rounded-xl px-4 py-2.5 outline-none focus:border-emerald-500 transition-all cursor-pointer shadow-md">
                            @foreach ($years as $y)
                                <option value="{{ $y }}" @if ($selectedYear == $y) selected @endif>
                                    {{ $y }} @if ($y == date('Y'))
                                        (Tahun Ini)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>

            <!-- Stat Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Card 1: Total Pendaftar -->
                <div
                    class="bg-[#0b0e14] border border-slate-800 rounded-xl p-5 shadow-xl relative overflow-hidden group hover:border-slate-700 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Pendaftar
                            ({{ $selectedYear }})</span>
                        <span
                            class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400 flex items-center justify-center text-xs border border-blue-500/20">
                            <i class="fas fa-users"></i>
                        </span>
                    </div>
                    <div class="mt-3 flex items-baseline justify-between">
                        <span
                            class="text-3xl font-black text-white tracking-tight">{{ number_format($totalRegistered) }}</span>
                        <span class="text-xs font-bold text-emerald-400 flex items-center gap-1">
                            <i class="fas fa-caret-up"></i> 100% Data {{ $selectedYear }}
                        </span>
                    </div>
                    <div class="w-full bg-slate-800/60 h-1.5 rounded-full mt-3 overflow-hidden">
                        <div class="bg-blue-500 h-full rounded-full" style="width: 100%"></div>
                    </div>
                </div>

                <!-- Card 2: Terverifikasi -->
                <div
                    class="bg-[#0b0e14] border border-slate-800 rounded-xl p-5 shadow-xl relative overflow-hidden group hover:border-slate-700 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Terverifikasi</span>
                        <span
                            class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xs border border-emerald-500/20">
                            <i class="fas fa-check-circle"></i>
                        </span>
                    </div>
                    <div class="mt-3 flex items-baseline justify-between">
                        <span
                            class="text-3xl font-black text-emerald-400 tracking-tight">{{ number_format($verifiedCount) }}</span>
                        @php $verifiedRate = $totalRegistered > 0 ? round(($verifiedCount / $totalRegistered) * 100) : 0; @endphp
                        <span class="text-xs font-bold text-emerald-400 flex items-center gap-1">
                            <i class="fas fa-caret-up"></i> {{ $verifiedRate }}% Ratio
                        </span>
                    </div>
                    <div class="w-full bg-slate-800/60 h-1.5 rounded-full mt-3 overflow-hidden">
                        <div class="bg-emerald-500 h-full rounded-full" style="width: {{ $verifiedRate }}%"></div>
                    </div>
                </div>

                <!-- Card 3: Pending -->
                <div
                    class="bg-[#0b0e14] border border-slate-800 rounded-xl p-5 shadow-xl relative overflow-hidden group hover:border-slate-700 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Menunggu
                            Verifikasi</span>
                        <span
                            class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center text-xs border border-amber-500/20">
                            <i class="fas fa-clock"></i>
                        </span>
                    </div>
                    <div class="mt-3 flex items-baseline justify-between">
                        <span
                            class="text-3xl font-black text-amber-400 tracking-tight">{{ number_format($pendingCount) }}</span>
                        @php $pendingRate = $totalRegistered > 0 ? round(($pendingCount / $totalRegistered) * 100) : 0; @endphp
                        <span class="text-xs font-bold text-amber-400">
                            {{ $pendingRate }}% Pending
                        </span>
                    </div>
                    <div class="w-full bg-slate-800/60 h-1.5 rounded-full mt-3 overflow-hidden">
                        <div class="bg-amber-500 h-full rounded-full" style="width: {{ $pendingRate }}%"></div>
                    </div>
                </div>

                <!-- Card 4: Ditolak -->
                <div
                    class="bg-[#0b0e14] border border-slate-800 rounded-xl p-5 shadow-xl relative overflow-hidden group hover:border-slate-700 transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Ditolak / Perlu
                            Revisi</span>
                        <span
                            class="w-8 h-8 rounded-lg bg-rose-500/10 text-rose-400 flex items-center justify-center text-xs border border-rose-500/20">
                            <i class="fas fa-times-circle"></i>
                        </span>
                    </div>
                    <div class="mt-3 flex items-baseline justify-between">
                        <span
                            class="text-3xl font-black text-rose-400 tracking-tight">{{ number_format($rejectedCount) }}</span>
                        @php $rejectedRate = $totalRegistered > 0 ? round(($rejectedCount / $totalRegistered) * 100) : 0; @endphp
                        <span class="text-xs font-bold text-rose-400">
                            {{ $rejectedRate }}% Ditolak
                        </span>
                    </div>
                    <div class="w-full bg-slate-800/60 h-1.5 rounded-full mt-3 overflow-hidden">
                        <div class="bg-rose-500 h-full rounded-full" style="width: {{ $rejectedRate }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Main Chart Section -->
            <div class="bg-[#0b0e14] border border-slate-800 rounded-xl p-6 shadow-2xl space-y-4">
                <div
                    class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800/80 pb-4">
                    <div>
                        <h2 class="text-lg font-extrabold text-white tracking-tight flex items-center gap-2">
                            <i class="fas fa-chart-area text-emerald-400"></i>
                            Grafik Tren Pendaftaran Praktikan Tahun {{ $selectedYear }}
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">Monitoring per bulan dari Januari {{ $selectedYear }}
                            hingga Desember {{ $selectedYear }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-[#181c24] border border-slate-700 text-xs font-bold text-emerald-400">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 shadow-sm shadow-emerald-400"></span>
                            Jumlah Mahasiswa Pendaftar
                        </span>
                    </div>
                </div>

                <div class="w-full min-h-[380px]">
                    <div id="livePendaftaranChart" class="w-full h-[380px]"></div>
                </div>
            </div>

            <!-- REKAPITULASI PRAKTIKUM DARI MASA KE MASA (DARK THEME ELEGANT) -->
            <div class="bg-[#0b0e14] border border-slate-800 rounded-xl p-6 shadow-2xl space-y-4">
                <div
                    class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800/80 pb-4">
                    <div>
                        <h2 class="text-lg font-extrabold text-white tracking-tight flex items-center gap-2">
                            <i class="fas fa-history text-sky-400"></i>
                            Rekapitulasi Praktikum Dari Masa ke Masa
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">Daftar historis seluruh mata praktikum, total peserta
                            terdaftar, dan tingkat kelulusan</p>
                    </div>
                    <span
                        class="px-3 py-1 bg-sky-500/10 text-sky-400 rounded-xl border border-sky-500/20 text-xs font-extrabold">
                        Total {{ $rekapPraktikums->count() }} Praktikum
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr
                                class="bg-[#141822] border-b border-slate-800 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                                <th class="py-3.5 px-4 rounded-l-xl">Nama & Kode Praktikum</th>
                                <th class="py-3.5 px-4">Periode</th>
                                <th class="py-3.5 px-4 text-center">Status</th>
                                <th class="py-3.5 px-4 text-center">Total Peserta</th>
                                <th class="py-3.5 px-4 text-center">Peserta Lulus</th>
                                <th class="py-3.5 px-4 text-center rounded-r-xl">Persentase Kelulusan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 text-slate-200 font-medium">
                            @forelse($rekapPraktikums as $rk)
                                <tr class="hover:bg-[#141822] transition-colors">
                                    <td class="py-4 px-4">
                                        <div class="font-extrabold text-white text-sm">{{ $rk->nama_praktikum }}</div>
                                        <div class="text-[11px] font-mono text-slate-400 mt-0.5">{{ $rk->kode_praktikum }}
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 font-bold text-slate-300">
                                        {{ $rk->periode_praktikum }}
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        @if ($rk->status_praktikum === 'finished')
                                            <span
                                                class="px-2.5 py-1 rounded-full bg-slate-800 text-slate-300 font-extrabold text-[10px] uppercase border border-slate-700">
                                                Telah Berakhir
                                            </span>
                                        @elseif($rk->status_praktikum === 'on_progress')
                                            <span
                                                class="px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-400 font-extrabold text-[10px] uppercase border border-amber-500/20">
                                                Berjalan
                                            </span>
                                        @else
                                            <span
                                                class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 font-extrabold text-[10px] uppercase border border-emerald-500/20">
                                                Pendaftaran Buka
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-center font-black text-white text-sm">
                                        {{ number_format($rk->total_peserta) }} <span
                                            class="text-xs font-normal text-slate-500">mhs</span>
                                    </td>
                                    <td class="py-4 px-4 text-center font-black text-emerald-400 text-sm">
                                        {{ number_format($rk->total_lulus) }} <span
                                            class="text-xs font-normal text-slate-500">lulus</span>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        @php
                                            $passRate =
                                                $rk->total_peserta > 0
                                                    ? round(($rk->total_lulus / $rk->total_peserta) * 100, 1)
                                                    : 0;
                                        @endphp
                                        <div class="inline-flex items-center gap-2">
                                            <div class="w-20 bg-slate-800 h-2 rounded-full overflow-hidden">
                                                <div class="bg-emerald-500 h-full rounded-full"
                                                    style="width: {{ $passRate }}%"></div>
                                            </div>
                                            <span class="font-extrabold text-xs text-white">{{ $passRate }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-slate-500 italic">
                                        Belum ada riwayat praktikum dari masa ke masa.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Real-Time Activity Live Ticker Feed -->
            <div class="bg-[#0b0e14] border border-slate-800 rounded-xl p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="w-3 h-3 rounded-full bg-emerald-400 animate-ping"></span>
                        <h2 class="text-base font-bold text-white tracking-tight">Live Feed Pendaftaran Terkini</h2>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr
                                class="border-b border-slate-800 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                                <th class="py-3 px-4">Praktikan</th>
                                <th class="py-3 px-4">Praktikum yang Didaftar</th>
                                <th class="py-3 px-4">Waktu Pendaftaran</th>
                                <th class="py-3 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 text-slate-200">
                            @forelse($liveActivities as $act)
                                <tr class="hover:bg-[#141822] transition-colors">
                                    <td class="py-3.5 px-4 font-bold text-white flex items-center gap-2.5">
                                        <div
                                            class="w-7 h-7 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs font-black">
                                            {{ strtoupper(substr($act->praktikan->user->name ?? 'P', 0, 1)) }}
                                        </div>
                                        <span>{{ $act->praktikan->user->name ?? 'Mahasiswa' }}</span>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-300 font-medium">
                                        {{ $act->praktikum->nama_praktikum ?? 'Praktikum' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-400 font-mono text-[11px]">
                                        <i class="far fa-clock text-slate-500 mr-1"></i>
                                        {{ $act->created_at->diffForHumans() }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if ($act->status === 'verified')
                                            <span
                                                class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-extrabold border border-emerald-500/20 uppercase">
                                                Verified
                                            </span>
                                        @elseif($act->status === 'rejected')
                                            <span
                                                class="px-2.5 py-1 rounded-full bg-rose-500/10 text-rose-400 text-[10px] font-extrabold border border-rose-500/20 uppercase">
                                                Rejected
                                            </span>
                                        @else
                                            <span
                                                class="px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-400 text-[10px] font-extrabold border border-amber-500/20 uppercase">
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-slate-500 italic">
                                        Belum ada aktivitas pendaftaran terkini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- ApexChart Logic for Dark Theme Live Chart -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const chartElem = document.querySelector("#livePendaftaranChart");
            if (!chartElem) return;

            const months = @json($months);
            const counts = @json($monthlyCounts);

            const options = {
                series: [{
                    name: 'Total Pendaftaran (Akumulasi)',
                    data: counts
                }],
                chart: {
                    type: 'bar',
                    height: 380,
                    fontFamily: 'Inter, sans-serif',
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    },
                    background: 'transparent',
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '45%',
                        borderRadius: 8,
                        borderRadiusApplication: 'end',
                        dataLabels: {
                            position: 'top'
                        }
                    }
                },
                colors: ['#10b981'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'dark',
                        type: 'vertical',
                        shadeIntensity: 0.5,
                        gradientToColors: ['#34d399'],
                        inverseColors: false,
                        opacityFrom: 0.95,
                        opacityTo: 0.75,
                        stops: [0, 100]
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val) {
                        return val !== null && val > 0 ? val : '';
                    },
                    offsetY: -22,
                    style: {
                        fontSize: '11px',
                        fontWeight: 700,
                        colors: ['#34d399']
                    }
                },
                grid: {
                    borderColor: '#1e293b',
                    strokeDashArray: 4
                },
                xaxis: {
                    categories: months,
                    labels: {
                        style: {
                            colors: '#94a3b8',
                            fontSize: '12px',
                            fontWeight: 600
                        }
                    },
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#94a3b8',
                            fontSize: '11px',
                            fontWeight: 600
                        },
                        formatter: function(val) {
                            return val !== null ? Math.round(val) : '';
                        }
                    }
                },
                tooltip: {
                    theme: 'dark',
                    y: {
                        formatter: function(val) {
                            return val !== null ? val + " Mahasiswa" : "Belum berjalan";
                        }
                    }
                }
            };

            const chart = new ApexCharts(chartElem, options);
            chart.render();
        });
    </script>
@endsection
