@extends('layouts.admin')

@section('title', 'Scan Presensi QR')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight uppercase">Scan QR Presensi</h1>
                <p class="text-slate-500 text-sm mt-1">Arahkan kamera ke QR Code milik praktikan</p>
            </div>
            <div id="status-indicator" class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-slate-300"></span>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Kamera Off</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Scanner Area -->
            <div class="lg:col-span-8 flex flex-col gap-4">
                <div class="relative bg-black rounded-3xl overflow-hidden shadow-2xl group min-h-[400px]">
                    <div id="reader" class="w-full"></div>
                    <div id="scanner-overlay"
                        class="absolute inset-0 pointer-events-none border-2 border-emerald-500/30 m-12 rounded-2xl flex items-center justify-center">
                        <div
                            class="w-full h-0.5 bg-emerald-500 shadow-[0_0_15px_rgba(16,185,129,0.5)] animate-scanner-line">
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button id="start-btn"
                        class="px-6 py-3 bg-emerald-600 text-white text-xs font-black rounded-xl uppercase tracking-widest hover:bg-emerald-700 transition-all flex items-center gap-2 shadow-lg shadow-emerald-600/20">
                        <i class="fas fa-camera"></i> Mulai Scanner
                    </button>
                    <button id="stop-btn"
                        class="px-6 py-3 bg-rose-600 text-white text-xs font-black rounded-xl uppercase tracking-widest hover:bg-rose-700 transition-all flex items-center gap-2 shadow-lg shadow-rose-600/20 hidden">
                        <i class="fas fa-video-slash"></i> Hentikan
                    </button>
                </div>
            </div>

            <!-- Log Area -->
            <div class="lg:col-span-4 space-y-4">
                <div
                    class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col h-full min-h-[400px]">
                    <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                        <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest">Log Presensi Sesi Ini</h3>
                        <span id="scan-count" class="bg-slate-900 text-white text-[10px] px-2 py-0.5 rounded-full">0</span>
                    </div>

                    <div id="scan-logs" class="p-4 space-y-3 flex-grow overflow-y-auto max-h-[500px]">
                        <div
                            class="flex flex-col items-center justify-center h-full py-10 text-slate-400 space-y-3 grayscale opacity-50">
                            <i class="fas fa-history text-3xl"></i>
                            <p class="text-[10px] font-bold uppercase tracking-widest">Belum ada aktivitas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Processing Modal -->
    <div id="processing-modal"
        class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-[9999] hidden items-center justify-center p-6">
        <div class="bg-white rounded-3xl p-8 max-w-sm w-full shadow-2xl text-center space-y-6">
            <div id="modal-content">
                <div class="w-20 h-20 border-4 border-slate-100 border-t-emerald-500 rounded-full animate-spin mx-auto">
                </div>
                <p class="mt-6 font-bold text-slate-900 uppercase tracking-tight text-lg">Memproses Presensi...</p>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            @keyframes scanner-line {
                0% {
                    transform: translateY(-150px);
                }

                100% {
                    transform: translateY(150px);
                }
            }

            .animate-scanner-line {
                animation: scanner-line 2s ease-in-out infinite alternate;
            }

            #reader__scan_region video {
                object-fit: cover !important;
                border-radius: 1.5rem;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://unpkg.com/html5-qrcode"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const html5QrCode = new Html5Qrcode("reader");
                const startBtn = document.getElementById('start-btn');
                const stopBtn = document.getElementById('stop-btn');
                const statusIndicator = document.getElementById('status-indicator');
                const logContainer = document.getElementById('scan-logs');
                const scanCount = document.getElementById('scan-count');
                const processingModal = document.getElementById('processing-modal');
                const modalContent = document.getElementById('modal-content');

                let isScanning = false;
                let count = 0;

                const config = {
                    fps: 10,
                    qrbox: {
                        width: 250,
                        height: 250
                    }
                };

                startBtn.addEventListener('click', () => {
                    startBtn.disabled = true;
                    startBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyiapkan...';

                    html5QrCode.start({
                            facingMode: "environment"
                        },
                        config,
                        onScanSuccess
                    ).then(() => {
                        isScanning = true;
                        startBtn.classList.add('hidden');
                        stopBtn.classList.remove('hidden');
                        statusIndicator.querySelector('span:first-child').className =
                            'w-3 h-3 rounded-full bg-emerald-500 animate-pulse';
                        statusIndicator.querySelector('span:last-child').innerText = 'Kamera On';
                        statusIndicator.querySelector('span:last-child').className =
                            'text-xs font-bold text-emerald-600 uppercase tracking-widest';
                        startBtn.disabled = false;
                        startBtn.innerHTML = '<i class="fas fa-camera"></i> Mulai Scanner';
                    }).catch(err => {
                        alert('Gagal mengakses kamera: ' + err);
                        startBtn.disabled = false;
                        startBtn.innerHTML = '<i class="fas fa-camera"></i> Mulai Scanner';
                    });
                });

                stopBtn.addEventListener('click', () => {
                    html5QrCode.stop().then(() => {
                        isScanning = false;
                        startBtn.classList.remove('hidden');
                        stopBtn.classList.add('hidden');
                        statusIndicator.querySelector('span:first-child').className =
                            'w-3 h-3 rounded-full bg-slate-300';
                        statusIndicator.querySelector('span:last-child').innerText = 'Kamera Off';
                        statusIndicator.querySelector('span:last-child').className =
                            'text-xs font-bold text-slate-400 uppercase tracking-widest';
                    });
                });

                function onScanSuccess(decodedText, decodedResult) {
                    if (processingModal.classList.contains('flex')) return; // Already processing

                    showProcessing();

                    // Ajax call to backend
                    fetch('{{ route('aslab.presensi.check-in') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                token: decodedText
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                count++;
                                scanCount.innerText = count;
                                addLog(data.nama, data.sesi, data.status, 'success');
                                showResultModal(true, data.message, data.sesi);
                            } else {
                                showResultModal(false, data.message);
                            }
                        })
                        .catch(err => {
                            showResultModal(false, 'Terjadi kesalahan sistem.');
                        });
                }

                function addLog(name, sesi, status, type) {
                    if (count === 1) logContainer.innerHTML = ''; // Clear empty message

                    const time = new Date().toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    const statusClass = status === 'terlambat' ? 'bg-amber-100 text-amber-600 border-amber-200' :
                        'bg-emerald-100 text-emerald-600 border-emerald-200';

                    const logItem = `
                    <div class="p-3 bg-white border border-slate-100 rounded-2xl flex items-center justify-between shadow-sm animate-in slide-in-from-right duration-500">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center text-[10px] font-black">${name.charAt(0)}</div>
                            <div>
                                <p class="text-[11px] font-bold text-slate-900">${name}</p>
                                <p class="text-[9px] text-slate-500 font-medium">${sesi}</p>
                                <p class="text-[8px] text-slate-400 font-medium uppercase tracking-widest">${time} WIB</p>
                            </div>
                        </div>
                        <span class="text-[8px] font-black px-1.5 py-0.5 rounded uppercase tracking-tighter ${statusClass} border">${status}</span>
                    </div>
                `;
                    logContainer.insertAdjacentHTML('afterbegin', logItem);
                }

                function showProcessing() {
                    processingModal.classList.remove('hidden');
                    processingModal.classList.add('flex');
                }

                function showResultModal(success, message, sesi = '') {
                    const icon = success ? 'fa-check-circle text-emerald-500' : 'fa-times-circle text-rose-500';
                    const buttonClass = success ? 'bg-emerald-600' : 'bg-rose-600';

                    modalContent.innerHTML = `
                    <div class="animate-in zoom-in duration-300">
                        <i class="fas ${icon} text-6xl"></i>
                        <p class="mt-6 font-bold text-slate-900 uppercase tracking-tight text-lg">${success ? 'Berhasil!' : 'Gagal!'}</p>
                        <p class="text-sm text-slate-500 mt-2">${message}</p>
                        ${success ? `<p class="text-[10px] font-black text-emerald-600 bg-emerald-50 py-2 px-4 rounded-xl mt-3 uppercase tracking-widest border border-emerald-100">${sesi}</p>` : ''}
                        <button onclick="closeModal()" class="mt-8 w-full py-4 ${buttonClass} text-white font-black text-xs uppercase tracking-widest rounded-2xl shadow-lg transition-all active:scale-95">OK, Lanjut Scan</button>
                    </div>
                `;
                }

                window.closeModal = function() {
                    processingModal.classList.add('hidden');
                    processingModal.classList.remove('flex');
                    modalContent.innerHTML = `
                    <div class="w-20 h-20 border-4 border-slate-100 border-t-emerald-500 rounded-full animate-spin mx-auto"></div>
                    <p class="mt-6 font-bold text-slate-900 uppercase tracking-tight text-lg">Memproses Presensi...</p>
                `;
                }
            });
        </script>
    @endpush
@endsection
