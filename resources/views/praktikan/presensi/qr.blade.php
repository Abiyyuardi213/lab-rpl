@extends('layouts.admin')

@section('title', 'Presensi QR Code')

@section('content')
    <div class="flex flex-col items-center justify-center min-h-[70vh] space-y-8">
        <div class="text-center space-y-2">
            <h1 class="text-3xl font-black text-slate-900 uppercase tracking-tight">Presensi Praktikum</h1>
            <p class="text-slate-500 font-medium italic">{{ $jadwal->judul_modul }} -
                {{ $jadwal->praktikum->nama_praktikum }}</p>
        </div>

        <div class="relative group">
            <!-- Decorative background -->
            <div
                class="absolute -inset-1 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-3xl blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200">
            </div>

            <div
                class="relative bg-white p-8 rounded-3xl shadow-xl border border-slate-100 flex flex-col items-center space-y-6">
                <div id="qrcode" class="p-2 bg-white rounded-xl shadow-inner border border-slate-50"></div>

                <div class="text-center space-y-1">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Scan by Lab Assistant</p>
                    <p id="timer" class="text-xs font-bold text-rose-500">QR Kadaluarsa dalam 30:00</p>
                </div>
            </div>
        </div>

        <div class="max-w-xs text-center space-y-4">
            <div class="bg-blue-50 border border-blue-100 p-4 rounded-2xl">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                    <p class="text-[11px] text-blue-700 leading-relaxed text-left font-medium">
                        Tunjukkan QR Code ini kepada Asisten Laboratorium untuk melakukan absensi kehadiran. QR ini bersifat
                        sementara.
                    </p>
                </div>
            </div>

            <a href="{{ route('praktikan.dashboard') }}"
                class="inline-flex items-center gap-2 text-xs font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Dashboard
            </a>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const token = @json($token);

                // Generate QR
                new QRCode(document.getElementById("qrcode"), {
                    text: token,
                    width: 256,
                    height: 256,
                    colorDark: "#0f172a",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });

                // Countdown Timer
                let timeLeft = 30 * 60; // 30 minutes in seconds
                const timerElement = document.getElementById('timer');

                const countdown = setInterval(() => {
                    if (timeLeft <= 0) {
                        clearInterval(countdown);
                        timerElement.innerText = "QR Kadaluarsa";
                        document.getElementById('qrcode').classList.add('opacity-10');
                        const overlay = document.createElement('div');
                        overlay.className =
                            "absolute inset-0 flex items-center justify-center font-black text-rose-500 uppercase tracking-tighter text-xl";
                        overlay.innerText = "Kadaluarsa";
                        document.getElementById('qrcode').parentElement.appendChild(overlay);
                        return;
                    }

                    const minutes = Math.floor(timeLeft / 60);
                    const seconds = timeLeft % 60;
                    timerElement.innerText =
                        `QR Kadaluarsa dalam ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
                    timeLeft--;
                }, 1000);

                // Polling for manual check status
                const pollStatus = setInterval(() => {
                    fetch(`{{ route('praktikan.presensi.check-status', $jadwal->id) }}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.present) {
                                clearInterval(pollStatus);
                                clearInterval(countdown);

                                Swal.fire({
                                    title: 'Presensi Berhasil!',
                                    text: 'Terima kasih, kehadiran Anda telah dicatat oleh sistem.',
                                    icon: 'success',
                                    confirmButtonColor: '#059669',
                                    confirmButtonText: 'Kembali ke Dashboard',
                                    customClass: {
                                        popup: 'rounded-3xl',
                                        confirmButton: 'px-8 py-3 rounded-xl font-bold uppercase tracking-widest text-xs'
                                    }
                                }).then(() => {
                                    window.location.href = "{{ route('praktikan.dashboard') }}";
                                });
                            }
                        })
                        .catch(err => console.error('Polling error:', err));
                }, 5000); // Check every 5 seconds
            });
        </script>
    @endpush
@endsection
