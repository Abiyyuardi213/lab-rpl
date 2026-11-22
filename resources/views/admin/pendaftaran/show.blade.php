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
                            <dd class="text-sm font-bold text-zinc-900 flex items-center gap-2">
                                <span>{{ $pendaftaran->no_hp }}</span>
                                @if ($pendaftaran->no_hp)
                                    @php
                                        $cleanWaPendaftaran = preg_replace('/[^0-9]/', '', $pendaftaran->no_hp);
                                        if (str_starts_with($cleanWaPendaftaran, '0')) {
                                            $cleanWaPendaftaran = '62' . substr($cleanWaPendaftaran, 1);
                                        }
                                        $waTextPendaftaran = "Halo " . ($pendaftaran->praktikan->user->name ?? 'Praktikan') . ", mengenai pendaftaran " . ($pendaftaran->praktikum->nama_praktikum ?? 'Praktikum') . " Lab RPL ITATS...";
                                        $waUrlPendaftaran = 'https://wa.me/' . $cleanWaPendaftaran . '?text=' . urlencode($waTextPendaftaran);
                                    @endphp
                                    <a href="{{ $waUrlPendaftaran }}" target="_blank" rel="noopener noreferrer"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-sm">
                                        <i class="fab fa-whatsapp text-sm"></i> Hubungi WA
                                    </a>
                                @endif
                            </dd>
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
                        <div class="space-y-1">
                            <dt class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Metode Pendaftaran</dt>
                            <dd class="text-sm font-bold text-zinc-900">
                                @if ($pendaftaran->is_google_form)
                                    <span class="px-2 py-0.5 rounded text-[10px] bg-indigo-50 text-indigo-600 border border-indigo-100 uppercase font-black tracking-tighter">
                                        <i class="fab fa-google mr-1"></i> Google Form
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] bg-zinc-50 text-zinc-500 border border-zinc-100 uppercase font-black tracking-tighter">
                                        Manual Upload
                                    </span>
                                @endif
                            </dd>
                        </div>
                    </div>
                </div>

                <!-- Documents Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($pendaftaran->bukti_krs)
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
                    @endif

                    @if($pendaftaran->bukti_pembayaran)
                    <div class="rounded-2xl border border-zinc-200 bg-white shadow-sm overflow-hidden flex flex-col">
                        <div class="px-5 py-4 border-b border-zinc-100 bg-amber-50/30">
                            <h4
                                class="text-[10px] font-black text-amber-900 uppercase tracking-[0.2em] flex items-center justify-between">
                                <span><i class="fas fa-receipt mr-1 text-amber-600"></i> Bukti Pembayaran (Lembar Kuning)</span>
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
                    @endif

                    @if($pendaftaran->foto_almamater)
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
                    @endif

                    @if($pendaftaran->is_google_form && !$pendaftaran->bukti_krs)
                        <div class="md:col-span-2 p-12 text-center bg-indigo-50/50 border border-indigo-100 rounded-2xl border-dashed">
                            <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fab fa-google text-indigo-600 text-2xl"></i>
                            </div>
                            <h4 class="text-sm font-bold text-indigo-900 uppercase tracking-widest mb-2">Terdaftar via Google Form</h4>
                            <p class="text-[11px] text-indigo-600 max-w-sm mx-auto leading-relaxed">Pendaftar menyatakan sudah mengunggah berkas persyaratan melalui Google Form. Silakan verifikasi data melalui dashboard Google Form laboratorium.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right: Actions -->
            <div class="space-y-6">
                <!-- Current Status -->
                <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm">
                    <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-4">Status Verifikasi Pendaftaran</h3>
                    @php
                        $ST_COLOR = [
                            'pending' => [
                                'label' => 'Menunggu Kelengkapan Verifikasi',
                                'class' => 'bg-amber-50 text-amber-700 border-amber-200',
                            ],
                            'verified' => [
                                'label' => 'Terverifikasi Penuh (Aktif)',
                                'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            ],
                            'rejected' => [
                                'label' => 'Ditolak',
                                'class' => 'bg-rose-50 text-rose-700 border-rose-200',
                            ],
                            'gugur' => [
                                'label' => 'Gugur (Diskualifikasi)',
                                'class' => 'bg-slate-100 text-slate-800 border-slate-300',
                            ],
                        ];
                        $st = $ST_COLOR[$pendaftaran->status] ?? [
                            'label' => $pendaftaran->status,
                            'class' => 'bg-zinc-50 text-zinc-500 border-zinc-200',
                        ];

                        $SUB_COLOR = [
                            'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'verified' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                        ];
                        $stLkClass = $SUB_COLOR[$pendaftaran->status_lembar_kuning ?? 'pending'] ?? 'bg-zinc-50 text-zinc-500 border-zinc-200';
                        $stBerkasClass = $SUB_COLOR[$pendaftaran->status_berkas ?? 'pending'] ?? 'bg-zinc-50 text-zinc-500 border-zinc-200';
                    @endphp
                    <div class="px-4 py-3 rounded-xl border {{ $st['class'] }} flex items-center gap-3 mb-4">
                        <i class="fas fa-shield-alt text-base"></i>
                        <div>
                            <span class="font-black text-xs uppercase tracking-wider block">{{ $st['label'] }}</span>
                            <span class="text-[10px] opacity-80 font-medium">Praktikan {{ $pendaftaran->status === 'verified' ? 'berhak mengikuti praktikum' : 'belum diizinkan mengikuti praktikum' }}</span>
                        </div>
                    </div>

                    <!-- Step Status Pills -->
                    <div class="grid grid-cols-1 gap-2 pt-3 border-t border-zinc-100">
                        <div class="flex items-center justify-between text-xs p-2.5 rounded-lg border {{ $stLkClass }}">
                            <span class="font-bold flex items-center gap-1.5">
                                <i class="fas fa-receipt text-[11px]"></i> 1. Lembar Kuning
                            </span>
                            <span class="text-[10px] font-black uppercase">{{ $pendaftaran->status_lembar_kuning ?? 'pending' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs p-2.5 rounded-lg border {{ $stBerkasClass }}">
                            <span class="font-bold flex items-center gap-1.5">
                                <i class="fas fa-file-alt text-[11px]"></i> 2. Berkas Akademik
                            </span>
                            <span class="text-[10px] font-black uppercase">{{ $pendaftaran->status_berkas ?? 'pending' }}</span>
                        </div>
                    </div>
                </div>

                <!-- 2-Step Verification Form -->
                <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm overflow-hidden">
                    <h3 class="text-sm font-bold text-zinc-900 uppercase tracking-widest mb-1">Kelola 2-Step Verifikasi</h3>
                    <p class="text-[11px] text-zinc-500 mb-6">Kedua tahap wajib diverifikasi sebelum mahasiswa dapat berstatus aktif.</p>

                    <form action="{{ route('admin.pendaftaran.update-status', $pendaftaran->id) }}" method="POST"
                        class="space-y-5">
                        @csrf
                        @method('PATCH')

                        <!-- Step 1: Lembar Kuning -->
                        <div class="p-4 rounded-xl border border-amber-200/70 bg-amber-50/20 space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="text-[11px] font-black text-amber-950 uppercase tracking-wide flex items-center gap-1.5">
                                    <i class="fas fa-receipt text-amber-600"></i> Step 1: Verifikasi Lembar Kuning
                                </label>
                                <span class="text-[9px] font-bold text-amber-700 bg-amber-100 px-1.5 py-0.5 rounded">Bukti Bayar</span>
                            </div>
                            <select name="status_lembar_kuning" id="status-lk-select" required onchange="toggleCatatanLK(this.value)"
                                class="w-full rounded-xl border border-zinc-200 bg-white px-3.5 py-2.5 text-xs font-bold text-zinc-800 focus:outline-none focus:ring-4 focus:ring-[#001f3f]/5 focus:border-[#001f3f] transition-all">
                                <option value="pending" {{ ($pendaftaran->status_lembar_kuning ?? 'pending') == 'pending' ? 'selected' : '' }}>
                                    PENDING (Menunggu Verifikasi Pembayaran)
                                </option>
                                <option value="verified" {{ ($pendaftaran->status_lembar_kuning ?? 'pending') == 'verified' ? 'selected' : '' }}>
                                    VERIFIED (Lembar Kuning Sah / Lolos)
                                </option>
                                <option value="rejected" {{ ($pendaftaran->status_lembar_kuning ?? 'pending') == 'rejected' ? 'selected' : '' }}>
                                    REJECTED (Kwitansi Tidak Valid / Ditolak)
                                </option>
                            </select>
                            <div id="catatan-lk-field" class="{{ ($pendaftaran->status_lembar_kuning ?? '') == 'rejected' ? '' : 'hidden' }} space-y-1">
                                <label class="text-[10px] font-bold text-rose-600 uppercase">Alasan Penolakan Lembar Kuning</label>
                                <textarea name="catatan_lembar_kuning" rows="2" placeholder="Contoh: Bukti transfer buram, nominal pembayaran tidak sesuai..."
                                    class="w-full rounded-xl border border-rose-200 bg-rose-50/30 p-2.5 text-xs focus:outline-none focus:ring-4 focus:ring-rose-500/5 focus:border-rose-400 transition-all">{{ $pendaftaran->catatan_lembar_kuning }}</textarea>
                            </div>
                        </div>

                        <!-- Step 2: Berkas Akademik -->
                        <div class="p-4 rounded-xl border border-indigo-200/70 bg-indigo-50/20 space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="text-[11px] font-black text-indigo-950 uppercase tracking-wide flex items-center gap-1.5">
                                    <i class="fas fa-file-contract text-indigo-600"></i> Step 2: Input Hasil Verifikasi (Berkas)
                                </label>
                                <span class="text-[9px] font-bold text-indigo-700 bg-indigo-100 px-1.5 py-0.5 rounded">KRS & Biodata</span>
                            </div>
                            <select name="status_berkas" id="status-berkas-select" required onchange="toggleCatatanBerkas(this.value)"
                                class="w-full rounded-xl border border-zinc-200 bg-white px-3.5 py-2.5 text-xs font-bold text-zinc-800 focus:outline-none focus:ring-4 focus:ring-[#001f3f]/5 focus:border-[#001f3f] transition-all">
                                <option value="pending" {{ ($pendaftaran->status_berkas ?? 'pending') == 'pending' ? 'selected' : '' }}>
                                    PENDING (Menunggu Pemeriksaan Berkas)
                                </option>
                                <option value="verified" {{ ($pendaftaran->status_berkas ?? 'pending') == 'verified' ? 'selected' : '' }}>
                                    VERIFIED (Berkas Lengkap & Valid)
                                </option>
                                <option value="rejected" {{ ($pendaftaran->status_berkas ?? 'pending') == 'rejected' ? 'selected' : '' }}>
                                    REJECTED (Berkas Ditolak)
                                </option>
                            </select>
                            <div id="catatan-berkas-field" class="{{ ($pendaftaran->status_berkas ?? '') == 'rejected' || ($pendaftaran->status == 'rejected' && !($pendaftaran->status_lembar_kuning == 'rejected')) ? '' : 'hidden' }} space-y-1">
                                <label class="text-[10px] font-bold text-rose-600 uppercase">Alasan Penolakan Berkas</label>
                                <textarea name="catatan" rows="2" placeholder="Contoh: KRS semester ini belum tertera mata kuliah praktikum..."
                                    class="w-full rounded-xl border border-rose-200 bg-rose-50/30 p-2.5 text-xs focus:outline-none focus:ring-4 focus:ring-rose-500/5 focus:border-rose-400 transition-all">{{ $pendaftaran->catatan }}</textarea>
                            </div>
                        </div>

                        <div class="p-3 bg-zinc-50 border border-zinc-200/80 rounded-xl flex items-start gap-2.5">
                            <i class="fas fa-info-circle text-xs text-[#001f3f] mt-0.5"></i>
                            <p class="text-[10px] text-zinc-600 leading-relaxed">
                                <strong>Ketentuan:</strong> Mahasiswa otomatis diizinkan praktikum jika <strong>Step 1 & Step 2 sama-sama VERIFIED</strong>. Jika salah satu REJECTED, status pendaftaran menjadi Ditolak.
                            </p>
                        </div>

                        <button type="submit"
                            class="w-full py-3 bg-[#001f3f] text-white rounded-xl text-xs font-black uppercase tracking-[0.15em] shadow-lg shadow-[#001f3f]/20 hover:bg-[#002d5a] transition-all active:scale-[0.98]">
                            SIMPAN HASIL VERIFIKASI
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleCatatanLK(status) {
            const field = document.getElementById('catatan-lk-field');
            if (field) {
                if (status === 'rejected') {
                    field.classList.remove('hidden');
                } else {
                    field.classList.add('hidden');
                }
            }
        }

        function toggleCatatanBerkas(status) {
            const field = document.getElementById('catatan-berkas-field');
            if (field) {
                if (status === 'rejected') {
                    field.classList.remove('hidden');
                } else {
                    field.classList.add('hidden');
                }
            }
        }
    </script>
@endsection
