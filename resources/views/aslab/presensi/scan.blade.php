@extends('layouts.admin')

@section('title', 'Scan Presensi QR')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 uppercase">Presensi QR Scanner</h1>
                <p class="text-sm text-zinc-500 mt-1 italic">"Kelola kehadiran praktikan sesi hari ini secara real-time."</p>
            </div>
            <div class="flex flex-col items-end gap-2">
                <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                    <a href="{{ route('aslab.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                    <span>/</span>
                    <span class="text-zinc-900 font-semibold">Presensi</span>
                </div>
                <div id="status-indicator" class="flex items-center gap-2 bg-zinc-100 px-3 py-1 rounded-full border border-zinc-200">
                    <span class="w-2 h-2 rounded-full bg-zinc-300"></span>
                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest leading-none">Kamera Off</span>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <!-- Navigation Tabs -->
        <div class="flex border-b border-zinc-200 gap-8 overflow-x-auto no-scrollbar">
            <button onclick="switchTab('scanner')" id="tab-scanner"
                class="pb-4 text-xs font-bold uppercase tracking-widest text-[#001f3f] border-b-2 border-[#001f3f] transition-all whitespace-nowrap active-tab">
                <i class="fas fa-camera mr-2"></i> Scanner & Aktif
            </button>
            <button onclick="switchTab('history')" id="tab-history"
                class="pb-4 text-xs font-bold uppercase tracking-widest text-zinc-400 hover:text-zinc-600 transition-all whitespace-nowrap">
                <i class="fas fa-history mr-2 text-amber-500"></i> Sesi Berakhir
            </button>
        </div>

        <div id="scanner-section" class="grid grid-cols-1 lg:grid-cols-12 gap-6 animate-in fade-in duration-500">
            <!-- Scanner Area -->
            <div class="lg:col-span-12 xl:col-span-8 flex flex-col gap-4">
                <div class="relative bg-zinc-950 rounded-xl overflow-hidden shadow-2xl group min-h-[300px] md:min-h-[450px] border border-zinc-800">
                    <div id="reader" class="w-full"></div>
                    <div id="scanner-overlay"
                        class="absolute inset-0 pointer-events-none border-2 border-emerald-500/20 m-8 md:m-16 rounded-2xl flex items-center justify-center">
                        <div class="w-full h-0.5 bg-emerald-500 shadow-[0_0_15px_rgba(16,185,129,0.5)] animate-scanner-line"></div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button id="start-btn"
                        class="px-8 py-3.5 bg-emerald-600 text-white text-[10px] font-bold rounded-lg uppercase tracking-widest hover:bg-emerald-700 transition-all flex items-center gap-3 shadow-lg shadow-emerald-600/10 active:scale-95">
                        <i class="fas fa-camera text-xs"></i> Mulai Scanner
                    </button>
                    <button id="stop-btn"
                        class="px-8 py-3.5 bg-rose-600 text-white text-[10px] font-bold rounded-lg uppercase tracking-widest hover:bg-rose-700 transition-all flex items-center gap-3 shadow-lg shadow-rose-600/10 hidden active:scale-95">
                        <i class="fas fa-video-slash text-xs"></i> Hentikan
                    </button>
                </div>
            </div>

            <!-- Active Schedules Log -->
            <div class="lg:col-span-12 xl:col-span-4 space-y-4">
                <div class="bg-white rounded-xl border border-zinc-200 shadow-sm overflow-hidden flex flex-col h-full min-h-[450px]">
                    <div class="p-6 border-b border-zinc-100 bg-zinc-50/50 flex items-center justify-between">
                        <div>
                            <h3 class="text-[10px] font-bold text-zinc-900 uppercase tracking-widest">Sesi Aktif Sekarang</h3>
                            <p class="text-[9px] text-zinc-400 font-bold uppercase mt-1">Real-time Tracker</p>
                        </div>
                        <span id="scan-count" class="bg-[#001f3f] text-white text-[10px] font-bold px-3 py-1 rounded-full shadow-lg shadow-[#001f3f]/10 tabular-nums">0</span>
                    </div>

                    <div id="scan-logs" class="p-5 space-y-4 flex-grow overflow-y-auto max-h-[600px] no-scrollbar">
                        @forelse($activeSchedules as $jadwal)
                            <div class="mb-6 last:mb-0 space-y-3">
                                <div class="flex items-center justify-between px-1">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-bold text-[#001f3f] uppercase tracking-wider">{{ $jadwal->praktikum->nama_praktikum }}</span>
                                        <span class="text-[9px] text-zinc-400 font-bold italic">{{ $jadwal->judul_modul }}</span>
                                    </div>
                                    <span class="text-[8px] font-bold px-2 py-0.5 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded-full">ACTIVE</span>
                                </div>
                                <div class="space-y-2">
                                    @forelse($jadwal->presensis as $p)
                                        <div class="p-3 bg-white border border-zinc-100 rounded-xl flex items-center justify-between shadow-sm hover:border-[#001f3f]/20 transition-all group">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-zinc-900 text-white flex items-center justify-center text-[10px] font-bold group-hover:scale-105 transition-transform">
                                                    {{ substr($p->pendaftaran->praktikan->user->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="text-[11px] font-bold text-zinc-900 uppercase tracking-tight">{{ $p->pendaftaran->praktikan->user->name }}</p>
                                                    <p class="text-[9px] text-zinc-400 font-bold uppercase tracking-widest">{{ \Carbon\Carbon::parse($p->jam_masuk)->format('H:i') }} WIB</p>
                                                </div>
                                            </div>
                                            <span class="text-[8px] font-bold px-1.5 py-0.5 rounded-md border {{ $p->status === 'hadir' ? 'bg-emerald-50 text-emerald-500 border-emerald-100' : 'bg-amber-50 text-amber-500 border-amber-100' }} uppercase tracking-tighter">{{ $p->status }}</span>
                                        </div>
                                    @empty
                                        <div class="py-10 text-center space-y-2 grayscale opacity-40">
                                            <i class="fas fa-qrcode text-2xl text-zinc-300"></i>
                                            <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">Belum ada scan masuk</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center h-full py-20 text-zinc-400 space-y-4 grayscale opacity-50">
                                <i class="fas fa-calendar-times text-4xl text-zinc-200"></i>
                                <div class="text-center">
                                    <p class="text-xs font-bold uppercase tracking-widest text-zinc-900">Tidak ada sesi aktif</p>
                                    <p class="text-[10px] font-medium mt-1">Silakan periksa menu berakhir untuk riwayat hari ini</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div id="history-section" class="hidden space-y-6 animate-in slide-in-from-bottom duration-500">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($finishedSchedules as $jadwal)
                    <div class="bg-white rounded-xl border border-zinc-200 shadow-sm overflow-hidden flex flex-col group hover:shadow-xl transition-all border-b-2 border-b-amber-400/30">
                        <div class="p-6 border-b border-zinc-50 bg-zinc-50/30">
                            <div class="flex items-center justify-between mb-4">
                                <span class="bg-amber-100 text-amber-600 text-[8px] font-bold px-2 py-0.5 rounded-md uppercase tracking-widest">Selesai</span>
                                <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">{{ $jadwal->waktu_mulai }}-{{ $jadwal->waktu_selesai }}</span>
                            </div>
                            <h4 class="text-xs font-bold text-zinc-900 uppercase tracking-tight">{{ $jadwal->praktikum->nama_praktikum }}</h4>
                            <p class="text-[10px] text-zinc-400 font-bold uppercase mt-1 italic leading-none">{{ $jadwal->judul_modul }}</p>
                        </div>
                        <div class="p-5 flex-grow max-h-[300px] overflow-y-auto no-scrollbar space-y-3">
                            <h5 class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest ml-1 mb-2 leading-none">Record Kehadiran:</h5>
                            @forelse($jadwal->presensis as $p)
                                <div class="flex items-center justify-between p-3 bg-zinc-50/50 rounded-xl border border-zinc-100">
                                    <div class="flex items-center gap-3">
                                        <div class="w-7 h-7 rounded-md bg-zinc-200 text-zinc-500 flex items-center justify-center text-[9px] font-bold">
                                            {{ substr($p->pendaftaran->praktikan->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold text-zinc-700 uppercase tracking-tight leading-none">{{ $p->pendaftaran->praktikan->user->name }}</p>
                                            <p class="text-[8px] text-zinc-400 font-bold uppercase tracking-widest italic mt-1 leading-none">{{ $p->status }} pada {{ \Carbon\Carbon::parse($p->jam_masuk)->format('H:i') }}</p>
                                        </div>
                                    </div>
                                    <i class="fas fa-check-circle text-emerald-500 text-xs"></i>
                                </div>
                            @empty
                                <div class="py-10 text-center grayscale opacity-30">
                                    <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest italic">Tidak ada kehadiran tercatat</p>
                                </div>
                            @endforelse
                        </div>
                        <div class="px-5 py-3.5 bg-zinc-50/50 border-t border-zinc-100">
                             <div class="flex items-center justify-between">
                                <span class="text-[9px] font-bold text-zinc-400 uppercase">Total:</span>
                                <span class="text-xs font-black text-[#001f3f] tracking-tighter">{{ $jadwal->presensis->count() }} Mhs</span>
                             </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 flex flex-col items-center justify-center text-zinc-300 space-y-4">
                        <i class="fas fa-box-open text-5xl opacity-20"></i>
                        <p class="text-xs font-bold uppercase tracking-widest opacity-50">Belum ada sesi yang berakhir hari ini</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Processing Modal -->
    <div id="processing-modal"
        class="fixed inset-0 bg-zinc-900/40 backdrop-blur-sm z-[9999] hidden items-center justify-center p-6 transition-all duration-300">
        <div class="bg-white rounded-xl p-8 max-w-sm w-full shadow-2xl border border-zinc-200 text-center space-y-6 animate-in zoom-in duration-200">
            <div id="modal-content">
                <div class="relative w-24 h-24 mx-auto mb-8">
                    <div class="absolute inset-0 border-4 border-zinc-100 rounded-full"></div>
                    <div class="absolute inset-0 border-4 border-[#001f3f] border-t-transparent rounded-full animate-spin"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-qrcode text-2xl text-[#001f3f] animate-pulse"></i>
                    </div>
                </div>
                <p class="font-bold text-zinc-900 uppercase tracking-tight text-xl mb-1 leading-none">Memvalidasi QR...</p>
                <p class="text-xs text-zinc-400 font-bold uppercase tracking-widest leading-none mt-3">Sistem Lab RPL ITATS</p>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            @keyframes scanner-line {
                0% { transform: translateY(-120px); opacity: 0; }
                50% { opacity: 1; }
                100% { transform: translateY(120px); opacity: 0; }
            }
            .animate-scanner-line { animation: scanner-line 2.5s ease-in-out infinite; }
            #reader__scan_region video { object-fit: cover !important; border-radius: 0.75rem; }
            .no-scrollbar::-webkit-scrollbar { display: none; }
            .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
            .active-tab { filter: drop-shadow(0 0 8px rgba(0,31,63,0.1)); }
        </style>
    @endpush

    @push('scripts')
        <script src="https://unpkg.com/html5-qrcode"></script>
        <script>
            function switchTab(tab) {
                const scannerSec = document.getElementById('scanner-section');
                const historySec = document.getElementById('history-section');
                const btnScanner = document.getElementById('tab-scanner');
                const btnHistory = document.getElementById('tab-history');

                if (tab === 'scanner') {
                    scannerSec.classList.remove('hidden');
                    historySec.classList.add('hidden');
                    btnScanner.className = 'pb-4 text-xs font-bold uppercase tracking-widest text-[#001f3f] border-b-2 border-[#001f3f] transition-all whitespace-nowrap active-tab';
                    btnHistory.className = 'pb-4 text-xs font-bold uppercase tracking-widest text-zinc-400 hover:text-zinc-600 transition-all whitespace-nowrap';
                } else {
                    scannerSec.classList.add('hidden');
                    historySec.classList.remove('hidden');
                    btnScanner.className = 'pb-4 text-xs font-bold uppercase tracking-widest text-zinc-400 hover:text-zinc-600 transition-all whitespace-nowrap';
                    btnHistory.className = 'pb-4 text-xs font-bold uppercase tracking-widest text-[#001f3f] border-b-2 border-[#001f3f] transition-all whitespace-nowrap active-tab';
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                const html5QrCode = new Html5Qrcode("reader");
                const startBtn = document.getElementById('start-btn');
                const stopBtn = document.getElementById('stop-btn');
                const statusIndicator = document.getElementById('status-indicator');
                const scanCount = document.getElementById('scan-count');
                const processingModal = document.getElementById('processing-modal');
                const modalContent = document.getElementById('modal-content');

                let count = {{ $activeSchedules->sum(fn($j) => $j->presensis->count()) }};
                scanCount.innerText = count;

                const config = { fps: 15, qrbox: { width: 250, height: 250 } };

                startBtn.addEventListener('click', () => {
                    startBtn.disabled = true;
                    startBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
                    html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess)
                        .then(() => {
                            startBtn.classList.add('hidden');
                            stopBtn.classList.remove('hidden');
                            statusIndicator.querySelector('span:first-child').className = 'w-3 h-3 rounded-full bg-emerald-500 animate-pulse';
                            statusIndicator.querySelector('span:last-child').innerText = 'Scanner Ready';
                            statusIndicator.querySelector('span:last-child').className = 'text-[10px] font-bold text-emerald-600 uppercase tracking-widest leading-none';
                        }).catch(err => {
                            alert('Kamera tidak terdeteksi!');
                            startBtn.disabled = false;
                            startBtn.innerHTML = '<i class="fas fa-camera text-xs"></i> Mulai Scanner';
                        });
                });

                stopBtn.addEventListener('click', () => {
                    html5QrCode.stop().then(() => {
                        startBtn.classList.remove('hidden');
                        stopBtn.classList.add('hidden');
                        statusIndicator.querySelector('span:first-child').className = 'w-2 h-2 rounded-full bg-zinc-300';
                        statusIndicator.querySelector('span:last-child').innerText = 'Kamera Off';
                        statusIndicator.querySelector('span:last-child').className = 'text-[10px] font-bold text-zinc-400 uppercase tracking-widest leading-none';
                        startBtn.disabled = false;
                        startBtn.innerHTML = '<i class="fas fa-camera"></i> Mulai Scanner';
                    });
                });

                function onScanSuccess(decodedText) {
                    if (processingModal.classList.contains('flex')) return;
                    processingModal.classList.remove('hidden');
                    processingModal.classList.add('flex');

                    fetch('{{ route('aslab.presensi.check-in') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ token: decodedText })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            count++;
                            scanCount.innerText = count;
                            showResultModal(true, data.message, data.sesi);
                            // Refresh page after a short delay to update the persistent lists
                            setTimeout(() => { location.reload(); }, 2000);
                        } else {
                            showResultModal(false, data.message);
                        }
                    })
                    .catch(() => showResultModal(false, 'Masalah koneksi jaringan.'));
                }

                function showResultModal(success, message, sesi = '') {
                    const icon = success ? 'fa-check-circle text-emerald-500' : 'fa-times-circle text-rose-500';
                    modalContent.innerHTML = `
                        <div class="animate-in zoom-in duration-200">
                            <i class="fas ${icon} text-7xl mb-6"></i>
                            <h4 class="font-bold text-zinc-900 uppercase tracking-tight text-xl mb-2">${success ? 'Presensi Berhasil' : 'Presensi Gagal'}</h4>
                            <p class="text-[11px] text-zinc-500 font-bold uppercase tracking-widest leading-relaxed mb-6 px-4">${message}</p>
                            ${success ? `<div class="bg-zinc-50 border border-zinc-100 p-4 rounded-xl mb-8"><p class="text-[9px] font-black text-[#001f3f] uppercase tracking-widest mb-1 leading-none">Mata Kuliah / Sesi:</p><p class="text-[10px] font-bold text-zinc-500 italic uppercase mt-2">${sesi}</p></div>` : ''}
                            <button onclick="closeModal()" class="w-full py-3.5 bg-zinc-900 text-white font-bold text-[10px] uppercase tracking-widest rounded-lg shadow-xl active:scale-95 transition-all">OK, LANJUTKAN</button>
                        </div>
                    `;
                }

                window.closeModal = function() {
                    processingModal.classList.add('hidden');
                    processingModal.classList.remove('flex');
                    modalContent.innerHTML = `
                        <div class="relative w-24 h-24 mx-auto mb-8">
                            <div class="absolute inset-0 border-4 border-slate-100 rounded-full"></div>
                            <div class="absolute inset-0 border-4 border-[#001f3f] border-t-transparent rounded-full animate-spin"></div>
                            <div class="absolute inset-0 flex items-center justify-center"><i class="fas fa-qrcode text-2xl text-[#001f3f] animate-pulse"></i></div>
                        </div>
                        <p class="font-black text-slate-900 uppercase tracking-tight text-xl mb-2">Memvalidasi QR...</p>
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Sistem Lab RPL ITATS</p>
                    `;
                }
            });
        </script>
    @endpush
@endsection
