@extends('layouts.admin')

@section('title', 'Detail Pendaftaran')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-start justify-between">
            <div class="space-y-1">
                <a href="{{ route('admin.pendaftaran.index') }}"
                    class="inline-flex items-center gap-2 text-xs font-bold text-zinc-500 hover:text-zinc-900 transition-colors mb-2">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Daftar
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Verifikasi Pendaftaran</h1>
                <p class="text-sm text-zinc-500">Periksa kelengkapan data dan validitas dokumen pendaftar.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Documents / Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Info Card -->
                <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm">
                    <h3 class="text-sm font-bold text-zinc-900 uppercase tracking-widest flex items-center gap-2 mb-6">
                        <i class="fas fa-id-card text-[#001f3f]"></i>
                        Identitas & Data Perkuliahan
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">
                        <div class="space-y-1">
                            <dt class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Nama Lengkap</dt>
                            <dd class="text-sm font-bold text-zinc-900">{{ $pendaftaran->praktikan->user->name }}</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">NPM</dt>
                            <dd class="text-sm font-bold text-[#001f3f] font-mono">{{ $pendaftaran->praktikan->npm }}</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Praktikum / Sesi</dt>
                            <dd class="text-sm font-bold text-zinc-900">{{ $pendaftaran->praktikum->nama_praktikum }}</dd>
                            <dd class="text-[11px] font-medium text-zinc-500 capitalize">{{ $pendaftaran->sesi->nama_sesi }}
                                ({{ $pendaftaran->sesi->hari }})</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">No. WhatsApp</dt>
                            <dd class="text-sm font-bold text-zinc-900">{{ $pendaftaran->no_hp }}</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Dosen Pengampu MK</dt>
                            <dd class="text-sm font-bold text-zinc-900">{{ $pendaftaran->dosen_pengampu }}</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Asal Kelas MK</dt>
                            <dd class="text-sm font-bold text-zinc-900 uppercase">{{ $pendaftaran->asal_kelas_mata_kuliah }}
                                ({{ $pendaftaran->kelas }})</dd>
                        </div>
                        <div class="space-y-1">
                            <dt class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Status Mengulang</dt>
                            <dd class="text-sm font-bold text-zinc-900">
                                @if ($pendaftaran->is_mengulang)
                                    <span class="text-amber-600">Ya, Mengulang</span>
                                @else
                                    <span class="text-emerald-600">Baru (Pertama Kali)</span>
                                @endif
                            </dd>
                        </div>
                    </div>
                </div>

                <!-- Documents Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="rounded-2xl border border-zinc-200 bg-white shadow-sm overflow-hidden flex flex-col">
                        <div class="px-5 py-4 border-b border-zinc-100 bg-zinc-50/50">
                            <h4
                                class="text-[10px] font-black text-zinc-900 uppercase tracking-[0.2em] flex items-center justify-between">
                                Bukti KRS
                                <a href="{{ asset('storage/' . $pendaftaran->bukti_krs) }}" target="_blank"
                                    class="text-[#001f3f] hover:underline transition-all">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </h4>
                        </div>
                        <div class="p-4 flex-grow aspect-[3/4] bg-zinc-100 group relative">
                            @if (Str::endsWith(strtolower($pendaftaran->bukti_krs), '.pdf'))
                                <iframe src="{{ asset('storage/' . $pendaftaran->bukti_krs) }}" class="w-full h-full rounded-lg" frameborder="0"></iframe>
                            @else
                                <img src="{{ asset('storage/' . $pendaftaran->bukti_krs) }}"
                                    class="w-full h-full object-contain">
                            @endif
                        </div>
                    </div>

                    <div class="rounded-2xl border border-zinc-200 bg-white shadow-sm overflow-hidden flex flex-col">
                        <div class="px-5 py-4 border-b border-zinc-100 bg-zinc-50/50">
                            <h4
                                class="text-[10px] font-black text-zinc-900 uppercase tracking-[0.2em] flex items-center justify-between">
                                Bukti Pembayaran
                                <a href="{{ asset('storage/' . $pendaftaran->bukti_pembayaran) }}" target="_blank"
                                    class="text-[#001f3f] hover:underline transition-all">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </h4>
                        </div>
                        <div class="p-4 flex-grow aspect-[3/4] bg-zinc-100 group relative">
                            @if (Str::endsWith(strtolower($pendaftaran->bukti_pembayaran), '.pdf'))
                                <iframe src="{{ asset('storage/' . $pendaftaran->bukti_pembayaran) }}" class="w-full h-full rounded-lg" frameborder="0"></iframe>
                            @else
                                <img src="{{ asset('storage/' . $pendaftaran->bukti_pembayaran) }}"
                                    class="w-full h-full object-contain">
                            @endif
                        </div>
                    </div>

                    <div
                        class="rounded-2xl border border-zinc-200 bg-white shadow-sm overflow-hidden flex flex-col md:col-span-2">
                        <div class="px-5 py-4 border-b border-zinc-100 bg-zinc-50/50">
                            <h4
                                class="text-[10px] font-black text-zinc-900 uppercase tracking-[0.2em] flex items-center justify-between">
                                Foto Beralmamater
                                <a href="{{ asset('storage/' . $pendaftaran->foto_almamater) }}" target="_blank"
                                    class="text-[#001f3f] hover:underline transition-all">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </h4>
                        </div>
                        <div class="p-8 bg-zinc-100 flex items-center justify-center">
                            <img src="{{ asset('storage/' . $pendaftaran->foto_almamater) }}"
                                class="max-h-96 rounded-xl shadow-lg">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Actions -->
            <div class="space-y-6">
                <!-- Current Status -->
                <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm">
                    <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-4">Status Verifikasi</h3>
                    @php
                        $ST_COLOR = [
                            'pending' => [
                                'label' => 'Menunggu Verifikasi',
                                'class' => 'bg-amber-50 text-amber-600 border-amber-100',
                            ],
                            'verified' => [
                                'label' => 'Terverifikasi',
                                'class' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                            ],
                            'rejected' => ['label' => 'Ditolak', 'class' => 'bg-rose-50 text-rose-600 border-rose-100'],
                        ];
                        $st = $ST_COLOR[$pendaftaran->status] ?? [
                            'label' => $pendaftaran->status,
                            'class' => 'bg-zinc-50 text-zinc-500 border-zinc-100',
                        ];
                    @endphp
                    <div class="px-4 py-3 rounded-xl border {{ $st['class'] }} flex items-center gap-3">
                        <i class="fas fa-info-circle"></i>
                        <span class="font-black text-xs uppercase tracking-[0.1em]">{{ $st['label'] }}</span>
                    </div>
                </div>

                <!-- Verification Form -->
                <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm overflow-hidden">
                    <h3 class="text-sm font-bold text-zinc-900 uppercase tracking-widest mb-6">Kelola Status</h3>
                    <form action="{{ route('admin.pendaftaran.update-status', $pendaftaran->id) }}" method="POST"
                        class="space-y-4">
                        @csrf
                        @method('PATCH')
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-zinc-500 uppercase">Input Hasil Verifikasi</label>
                            <select name="status" id="status-select" required onchange="toggleCatatan(this.value)"
                                class="w-full rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm focus:outline-none focus:ring-4 focus:ring-[#001f3f]/5 focus:border-[#001f3f] transition-all">
                                <option value="verified" {{ $pendaftaran->status == 'verified' ? 'selected' : '' }}>
                                    VERIFIED (Diterima)</option>
                                <option value="rejected" {{ $pendaftaran->status == 'rejected' ? 'selected' : '' }}>
                                    REJECTED (Tolak)</option>
                            </select>
                        </div>
                        <div id="catatan-field" class="{{ $pendaftaran->status == 'rejected' ? '' : 'hidden' }} space-y-1">
                            <label class="text-[10px] font-bold text-rose-500 uppercase">Alasan Penolakan</label>
                            <textarea name="catatan" rows="3" placeholder="Sebutkan alasan penolakan agar mahasiswa dapat memperbaikinya..."
                                class="w-full rounded-xl border border-rose-100 bg-rose-50/30 px-4 py-2.5 text-sm focus:outline-none focus:ring-4 focus:ring-rose-500/5 focus:border-rose-300 transition-all">{{ $pendaftaran->catatan }}</textarea>
                        </div>
                        <button type="submit"
                            class="w-full py-3 bg-[#001f3f] text-white rounded-xl text-xs font-black uppercase tracking-[0.2em] shadow-lg shadow-[#001f3f]/20 hover:bg-[#002d5a] transition-all active:scale-[0.98]">
                            SIMPAN PERUBAHAN
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleCatatan(status) {
            const field = document.getElementById('catatan-field');
            if (status === 'rejected') {
                field.classList.remove('hidden');
            } else {
                field.classList.add('hidden');
            }
        }
    </script>
@endsection
