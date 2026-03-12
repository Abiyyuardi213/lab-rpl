@extends('layouts.admin')

@section('title', 'Soal: ' . $penugasan->judul)

@section('content')
    <div class="space-y-6">
        <!-- Header & Breadcrumb -->
        <div class="flex items-start justify-between">
            <div>
                <a href="{{ route('praktikan.penugasan.index') }}" class="flex items-center gap-2 text-[10px] font-bold text-zinc-400 uppercase tracking-widest hover:text-[#001f3f] transition-colors group mb-2">
                    <i class="fas fa-arrow-left text-[8px] group-hover:-translate-x-1 transition-transform"></i> Kembali ke Daftar Soal
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 uppercase">{{ $penugasan->judul }}</h1>
                <p class="text-sm text-zinc-500 mt-1 italic">{{ $penugasan->praktikum->nama_praktikum }} - {{ $penugasan->sesi->nama_sesi }}</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500 pt-6">
                <a href="{{ route('praktikan.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                <span>/</span>
                <a href="{{ route('praktikan.penugasan.index') }}" class="hover:text-zinc-900 transition-colors">Penugasan</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold truncate max-w-[150px]">Detail</span>
            </div>
        </div>

        @if (session('error'))
            <div class="bg-rose-50 border border-rose-200 p-4 rounded-xl shadow-sm animate-in slide-in-from-top duration-300">
                <div class="flex items-center gap-4">
                    <div class="h-8 w-8 rounded-lg bg-rose-500 flex items-center justify-center text-white shrink-0 shadow-lg shadow-rose-500/10">
                        <i class="fas fa-exclamation-triangle text-[10px]"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-rose-700 font-bold uppercase tracking-widest leading-none">Terjadi Kesalahan</p>
                        <p class="text-[10px] text-rose-600 mt-1.5 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-xl shadow-sm animate-in slide-in-from-top duration-300">
                <div class="flex items-center gap-4">
                    <div class="h-8 w-8 rounded-lg bg-emerald-500 flex items-center justify-center text-white shrink-0 shadow-lg shadow-emerald-500/10">
                        <i class="fas fa-check text-[10px]"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-emerald-700 font-bold uppercase tracking-widest leading-none">Berhasil</p>
                        <p class="text-[10px] text-emerald-600 mt-1.5 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content: Description & Files -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl border border-zinc-200 shadow-sm overflow-hidden min-h-[400px]">
                    <div class="p-6 border-b border-zinc-100 bg-zinc-50/50 flex items-center gap-4">
                        <div class="h-9 w-9 rounded-lg bg-[#001f3f] flex items-center justify-center text-white shadow-lg shadow-[#001f3f]/10">
                            <i class="fas fa-file-alt text-xs"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-zinc-900 uppercase tracking-tight leading-none">Detail Soal</h3>
                            <p class="text-[9px] text-zinc-400 font-bold uppercase tracking-widest mt-1.5 leading-none">Baca instruksi pengerjaan dengan teliti</p>
                        </div>
                    </div>
                    <div class="p-8">
                        <div class="prose prose-zinc max-w-none text-zinc-700 font-medium">
                            {!! nl2br(e($penugasan->deskripsi)) !!}
                        </div>

                        @if ($penugasan->file_soal)
                            <div class="mt-12 p-5 bg-zinc-50 rounded-xl border border-zinc-200 flex items-center justify-between group hover:bg-white transition-all shadow-hover">
                                <div class="flex items-center gap-4">
                                    <div class="h-12 w-12 rounded-lg bg-white flex items-center justify-center text-rose-600 shadow-sm border border-zinc-200 group-hover:scale-105 transition-transform">
                                        <i class="fas fa-file-pdf text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-zinc-900 uppercase tracking-tight">Lampiran Soal</p>
                                        <p class="text-[9px] text-zinc-400 font-bold uppercase tracking-widest mt-1 leading-none italic">Download file pendukung</p>
                                    </div>
                                </div>
                                <a href="{{ route('praktikan.penugasan.download', $penugasan->id) }}"
                                    class="h-10 px-6 bg-[#001f3f] text-white text-[10px] font-bold rounded-lg uppercase tracking-widest shadow-lg shadow-[#001f3f]/10 hover:bg-[#002d5a] transition-all flex items-center gap-2">
                                    <i class="fas fa-download"></i> Unduh File
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar: Info & Deadline -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-xl border border-zinc-200 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-zinc-100 bg-zinc-50/50">
                        <h3 class="text-sm font-black text-zinc-900 uppercase tracking-tight">Informasi Sesi</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="space-y-3">
                            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest leading-none">Sumber Soal / Asisten</p>
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-500 font-bold text-xs border border-zinc-200 uppercase">
                                    {{ $penugasan->aslab ? substr($penugasan->aslab->user->name, 0, 1) : 'A' }}
                                </div>
                                <div>
                                    <p class="text-[11px] font-black text-zinc-900 uppercase tracking-tight leading-none">
                                        {{ $penugasan->aslab ? $penugasan->aslab->user->name : 'Administrator' }}
                                    </p>
                                    @if($penugasan->aslab)
                                        <p class="text-[9px] text-zinc-400 font-bold uppercase tracking-widest mt-1.5 leading-none">{{ $penugasan->aslab->npm }}</p>
                                    @else
                                        <p class="text-[9px] text-zinc-400 font-bold uppercase tracking-widest mt-1.5 leading-none italic">Sistem Lab RPL</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-zinc-50 space-y-4">
                            <div class="flex items-center justify-between p-4 bg-emerald-50 rounded-xl border border-emerald-100 shadow-sm">
                                <div>
                                    <p class="text-[9px] font-bold text-emerald-600 uppercase tracking-widest leading-none mb-2">Waktu Tersisa</p>
                                    <p class="text-xl font-black text-emerald-700 tracking-tighter tabular-nums leading-none" id="countdown">--:--:--</p>
                                </div>
                                <div class="h-9 w-9 rounded-lg bg-white flex items-center justify-center text-emerald-500 shadow-sm border border-emerald-200">
                                    <i class="fas fa-clock text-xs animate-pulse"></i>
                                </div>
                            </div>
                        </div>

                        <div class="pt-2 space-y-4">
                            <div class="flex items-start gap-4 p-4 bg-zinc-50 rounded-xl border border-zinc-100">
                                <i class="fas fa-info-circle text-amber-500 text-xs mt-0.5"></i>
                                <p class="text-[10px] text-zinc-500 font-medium italic leading-relaxed">
                                    Akses soal akan ditutup otomatis saat jam sesi berakhir. Pastikan Anda menyelesaikan tugas tepat waktu.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Countdown timer logic
        const endTimeStr = "{{ $penugasan->sesi->jam_selesai }}";
        const today = new Date().toISOString().split('T')[0];
        const endTime = new Date(today + 'T' + endTimeStr);

        function updateCountdown() {
            const now = new Date();
            const diff = endTime - now;

            if (diff <= 0) {
                document.getElementById('countdown').innerHTML = "00:00:00";
                window.location.reload(); // Refresh when time is up to trigger access restriction
                return;
            }

            const h = Math.floor(diff / 3600000).toString().padStart(2, '0');
            const m = Math.floor((diff % 3600000) / 60000).toString().padStart(2, '0');
            const s = Math.floor((diff % 60000) / 1000).toString().padStart(2, '0');

            document.getElementById('countdown').innerHTML = `${h}:${m}:${s}`;
        }

        setInterval(updateCountdown, 1000);
        updateCountdown();
    </script>
@endsection
