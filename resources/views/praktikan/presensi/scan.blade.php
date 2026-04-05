@extends('layouts.admin')

@section('title', 'Scanner Presensi')

@section('content')
<div class="max-w-xl mx-auto space-y-8 py-4 sm:py-8 px-4">
    <!-- Header -->
    <div class="text-center space-y-3">
        <h1 class="text-2xl font-black text-slate-900 uppercase tracking-tighter sm:text-3xl">
            PEMINDAI PRESENSI
        </h1>
        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest italic">
            Scan QR Code yang ditampilkan oleh Aslab Anda
        </p>
    </div>

    <!-- Scanner Area -->
    <div class="relative group">
        <div class="absolute -inset-2 bg-gradient-to-r from-[#001f3f] to-blue-500 rounded-[2.5rem] blur opacity-20 transition duration-1000"></div>
        <div class="relative bg-zinc-950 rounded-[2rem] overflow-hidden shadow-2xl border border-zinc-800 min-h-[300px] sm:min-h-[400px]">
             <div id="reader" class="w-full"></div>
             <!-- Overlay for scanning effect -->
             <div id="scanner-overlay" class="absolute inset-0 pointer-events-none border-2 border-[#001f3f]/30 m-6 sm:m-12 rounded-2xl flex items-center justify-center overflow-hidden">
                <div class="w-full h-1 bg-[#001f3f] shadow-[0_0_20px_rgba(0,31,63,0.8)] animate-scanner-line"></div>
             </div>
        </div>
    </div>

    <!-- Actions & Status -->
    <div class="space-y-4">
        <div id="status-display" class="flex items-center justify-center gap-3 bg-white border border-slate-100 p-4 rounded-2xl shadow-sm">
            <span class="relative flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75 mr-off"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500 mr-off"></span>
            </span>
            <span class="text-xs font-black text-slate-400 uppercase tracking-widest" id="status-text">Kamera Mati</span>
        </div>

        <div class="grid grid-cols-1 gap-3">
            <button id="start-btn" 
                class="h-14 bg-[#001f3f] text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-[#002d5a] transition-all flex items-center justify-center gap-3 shadow-lg shadow-[#001f3f]/20 active:scale-95">
                <i class="fas fa-camera"></i>
                Aktifkan Kamera
            </button>
            <button id="stop-btn" 
                class="h-14 bg-white border-2 border-rose-100 text-rose-500 text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-rose-50 transition-all flex items-center justify-center gap-3 hidden active:scale-95">
                <i class="fas fa-stop-circle"></i>
                Hentikan Pemindai
            </button>
            <a href="{{ url()->previous() }}" 
               class="h-12 flex items-center justify-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] hover:text-slate-600 transition-colors">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>
</div>

<!-- Modal Processing -->
<div id="loading-modal" class="fixed inset-0 z-[60] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
        <div class="relative bg-white rounded-[2rem] p-8 w-full max-w-sm shadow-2xl text-center space-y-6">
            <div class="w-20 h-20 border-4 border-slate-100 border-t-[#001f3f] rounded-full animate-spin mx-auto flex items-center justify-center">
                <i class="fas fa-qrcode text-[#001f3f] animate-pulse"></i>
            </div>
            <div>
                <h3 class="text-xl font-black text-slate-900 uppercase">Processing...</h3>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-2">Sedang memvalidasi kehadiran Anda</p>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @keyframes scanner-line {
        0% { transform: translateY(-150px); opacity: 0; }
        50% { opacity: 1; }
        100% { transform: translateY(150px); opacity: 0; }
    }
    .animate-scanner-line { animation: scanner-line 3s ease-in-out infinite; }
    #reader video { 
        object-fit: cover !important; 
        border-radius: 1.5rem; 
        width: 100% !important;
        height: 100% !important;
        min-height: 300px;
    }
    .mr-off { background-color: #94a3b8 !important; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const scannerIndicator = document.querySelector('#status-display .relative');
        const statusText = document.getElementById('status-text');
        const startBtn = document.getElementById('start-btn');
        const stopBtn = document.getElementById('stop-btn');
        const loadingModal = document.getElementById('loading-modal');
        
        let html5QrCode = new Html5Qrcode("reader");

        const onScanSuccess = (decodedText) => {
            // Decoded text expected to be the URL: http://.../praktikan/presensi/scan-jadwal/{token}
            html5QrCode.stop();
            loadingModal.classList.remove('hidden');
            
            // Redirect to the scanned URL
            window.location.href = decodedText;
        };

        const config = { fps: 15, qrbox: { width: 250, height: 250 } };

        startBtn.addEventListener('click', () => {
            startBtn.disabled = true;
            startBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memulai...';
            
            html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess)
                .then(() => {
                    startBtn.classList.add('hidden');
                    stopBtn.classList.remove('hidden');
                    statusText.innerText = 'Pemindai Aktif';
                    statusText.className = 'text-xs font-black text-emerald-500 uppercase tracking-widest';
                    scannerIndicator.querySelectorAll('span').forEach(s => {
                        s.classList.remove('mr-off');
                        s.classList.add('bg-emerald-500');
                    });
                })
                .catch(err => {
                    alert('Gagal mengakses kamera: ' + err);
                    startBtn.disabled = false;
                    startBtn.innerHTML = '<i class="fas fa-camera"></i> Aktifkan Kamera';
                });
        });

        stopBtn.addEventListener('click', () => {
            html5QrCode.stop().then(() => {
                startBtn.classList.remove('hidden');
                stopBtn.classList.add('hidden');
                statusText.innerText = 'Kamera Mati';
                statusText.className = 'text-xs font-black text-slate-400 uppercase tracking-widest';
                scannerIndicator.querySelectorAll('span').forEach(s => {
                    s.classList.add('mr-off');
                    s.classList.remove('bg-emerald-500');
                    s.classList.remove('bg-rose-500');
                });
                startBtn.disabled = false;
                startBtn.innerHTML = '<i class="fas fa-camera"></i> Aktifkan Kamera';
            });
        });
    });
</script>
@endpush
@endsection
