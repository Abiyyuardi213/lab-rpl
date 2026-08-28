@extends('layouts.admin')

@section('title', 'Live Monitoring Dashboard - Lab RPL')

@section('content')
    <!-- ApexCharts CDN -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <div class="space-y-6">
        <!-- Top Bar: CoinMarketCap Style Header & Year Filter -->
        <div class="bg-[#0b0e14] border border-slate-800 rounded-xl p-5 shadow-2xl text-slate-100 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 font-bold text-xl shadow-lg shadow-emerald-500/10">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white">Live Monitoring Pendaftaran</h1>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 text-[10px] font-bold border border-emerald-500/30 uppercase tracking-widest flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            LIVE REALTIME
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-1">Metrik & Statistik Pendaftaran Praktikan Laboratorium RPL ITATS</p>
                </div>
            </div>

            <!-- Filter Tahun Form -->
            <form method="GET" action="{{ route('admin.dashboard2') }}" class="flex items-center gap-3 w-full md:w-auto">
                <label for="year" class="text-xs font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap">
                    <i class="fas fa-calendar-alt text-emerald-400 mr-1.5"></i> Tahun:
                </label>
                <select name="year" id="year" onchange="this.form.submit()"
                    class="bg-[#181c24] border border-slate-700 text-white text-xs font-bold rounded-lg px-4 py-2.5 outline-none focus:border-emerald-500 transition-all cursor-pointer shadow-md">
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>
                            {{ $y }} {{ $y == date('Y') ? '(Tahun Ini)' : '' }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <!-- CoinMarketCap Ticker Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Card 1: Total Pendaftar -->
            <div class="bg-[#0b0e14] border border-slate-800 rounded-xl p-5 shadow-xl relative overflow-hidden group hover:border-slate-700 transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Pendaftar ({{ $selectedYear }})</span>
                    <span class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-400 flex items-center justify-center text-xs border border-blue-500/20">
                        <i class="fas fa-users"></i>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="text-3xl font-black text-white tracking-tight">{{ number_format($totalRegistered) }}</span>
                    <span class="text-xs font-bold text-emerald-400 flex items-center gap-1">
                        <i class="fas fa-caret-up"></i> 100% Data {{ $selectedYear }}
                    </span>
                </div>
                <div class="w-full bg-slate-800/60 h-1.5 rounded-full mt-3 overflow-hidden">
                    <div class="bg-blue-500 h-full rounded-full" style="width: 100%"></div>
                </div>
            </div>

            <!-- Card 2: Terverifikasi -->
            <div class="bg-[#0b0e14] border border-slate-800 rounded-xl p-5 shadow-xl relative overflow-hidden group hover:border-slate-700 transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Terverifikasi</span>
                    <span class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xs border border-emerald-500/20">
                        <i class="fas fa-check-circle"></i>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="text-3xl font-black text-emerald-400 tracking-tight">{{ number_format($verifiedCount) }}</span>
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
            <div class="bg-[#0b0e14] border border-slate-800 rounded-xl p-5 shadow-xl relative overflow-hidden group hover:border-slate-700 transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Menunggu Verifikasi</span>
                    <span class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center text-xs border border-amber-500/20">
                        <i class="fas fa-clock"></i>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="text-3xl font-black text-amber-400 tracking-tight">{{ number_format($pendingCount) }}</span>
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
            <div class="bg-[#0b0e14] border border-slate-800 rounded-xl p-5 shadow-xl relative overflow-hidden group hover:border-slate-700 transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Ditolak / Perlu Revisi</span>
                    <span class="w-8 h-8 rounded-lg bg-rose-500/10 text-rose-400 flex items-center justify-center text-xs border border-rose-500/20">
                        <i class="fas fa-times-circle"></i>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="text-3xl font-black text-rose-400 tracking-tight">{{ number_format($rejectedCount) }}</span>
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

        <!-- Main Chart Section (CoinMarketCap Dark Style ApexChart) -->
        <div class="bg-[#0b0e14] border border-slate-800 rounded-xl p-6 shadow-2xl space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800/80 pb-4">
                <div>
                    <h2 class="text-lg font-extrabold text-white tracking-tight flex items-center gap-2">
                        <i class="fas fa-chart-area text-emerald-400"></i>
                        Grafik Tren Pendaftaran Praktikan Tahun {{ $selectedYear }}
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Monitoring per bulan dari Januari {{ $selectedYear }} hingga Desember {{ $selectedYear }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-[#181c24] border border-slate-700 text-xs font-bold text-emerald-400">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 shadow-sm shadow-emerald-400"></span>
                        Jumlah Mahasiswa Pendaftar
                    </span>
                </div>
            </div>

            <!-- Container Canvas Chart -->
            <div class="w-full min-h-[380px]">
                <div id="livePendaftaranChart" class="w-full h-[380px]"></div>
            </div>
        </div>

        <!-- Real-Time Activity Live Ticker Feed (CoinMarketCap Transactions Table Style) -->
        <div class="bg-[#0b0e14] border border-slate-800 rounded-xl p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-4">
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-emerald-400 animate-ping"></span>
                    <h2 class="text-base font-bold text-white tracking-tight">Live Broadcast Feed Pendaftaran</h2>
                </div>
                <a href="{{ route('admin.pendaftaran.index') }}" class="text-xs font-bold text-emerald-400 hover:underline uppercase tracking-wider">
                    Lihat Semua Pendaftaran <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
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
                                    <div class="w-7 h-7 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs font-black">
                                        {{ strtoupper(substr($act->praktikan->user->name ?? 'P', 0, 1)) }}
                                    </div>
                                    <span>{{ $act->praktikan->user->name ?? 'Mahasiswa' }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-300 font-medium">
                                    {{ $act->praktikum->nama_praktikum ?? 'Praktikum' }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-400 font-mono text-[11px]">
                                    <i class="far fa-clock text-slate-500 mr-1"></i> {{ $act->created_at->diffForHumans() }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($act->status === 'verified')
                                        <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-extrabold border border-emerald-500/20 uppercase">
                                            Verified
                                        </span>
                                    @elseif($act->status === 'rejected')
                                        <span class="px-2.5 py-1 rounded-full bg-rose-500/10 text-rose-400 text-[10px] font-extrabold border border-rose-500/20 uppercase">
                                            Rejected
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-400 text-[10px] font-extrabold border border-amber-500/20 uppercase">
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

    <!-- ApexChart Initializer Script (CoinMarketCap Dark Neon Theme) -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const monthsData = @json($months);
            const countsData = @json($monthlyCounts);

            const options = {
                series: [{
                    name: 'Jumlah Pendaftaran',
                    data: countsData
                }],
                chart: {
                    type: 'area',
                    height: 380,
                    background: 'transparent',
                    toolbar: {
                        show: false
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800,
                        animateGradually: {
                            enabled: true,
                            delay: 150
                        },
                        dynamicAnimation: {
                            enabled: true,
                            speed: 350
                        }
                    }
                },
                theme: {
                    mode: 'dark'
                },
                colors: ['#10b981'],
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.55,
                        opacityTo: 0.05,
                        stops: [0, 90, 100]
                    }
                },
                dataLabels: {
                    enabled: false
                },
                grid: {
                    borderColor: '#1e293b',
                    strokeDashArray: 4,
                    xaxis: {
                        lines: {
                            show: true
                        }
                    }
                },
                xaxis: {
                    categories: monthsData,
                    labels: {
                        style: {
                            colors: '#94a3b8',
                            fontSize: '11px',
                            fontWeight: 600
                        }
                    },
                    axisBorder: {
                        color: '#334155'
                    },
                    axisTicks: {
                        color: '#334155'
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#94a3b8',
                            fontSize: '11px',
                            fontWeight: 600
                        },
                        formatter: function (val) {
                            return Math.floor(val) + " mhs";
                        }
                    }
                },
                tooltip: {
                    theme: 'dark',
                    x: {
                        show: true
                    },
                    y: {
                        formatter: function (val) {
                            return val + " Mahasiswa Terdaftar";
                        }
                    }
                },
                markers: {
                    size: 5,
                    colors: ['#10b981'],
                    strokeColors: '#0b0e14',
                    strokeWidth: 2,
                    hover: {
                        size: 8
                    }
                }
            };

            const chart = new ApexCharts(document.querySelector("#livePendaftaranChart"), options);
            chart.render();
        });
    </script>

    <!-- Script Dynamic Full Dark Mode Body Background khusus Halaman Dashboard 2 (CoinMarketCap Style) -->
    <script>
        (function() {
            const body = document.body;
            const header = document.getElementById('admin-sticky-header');
            const footer = document.getElementById('admin-main-footer') || document.querySelector('footer');
            const footerLogoTitle = document.getElementById('footer-logo-title');
            const pageTitle = document.querySelector('#admin-sticky-header h2');

            if (body) {
                body.style.backgroundColor = '#0b0e14';
                body.style.color = '#f8fafc';
            }
            if (header) {
                header.style.backgroundColor = 'rgba(11, 14, 20, 0.85)';
                header.style.borderColor = 'rgba(30, 41, 59, 0.8)';
            }
            if (pageTitle) {
                pageTitle.style.color = '#ffffff';
            }
            if (footer) {
                footer.style.backgroundColor = '#0b0e14';
                footer.style.borderTop = '1px solid rgba(30, 41, 59, 0.8)';
            }
            if (footerLogoTitle) {
                footerLogoTitle.style.color = '#e2e8f0';
            }
        })();
    </script>
@endsection 
