@extends('layouts.app')

@section('title', 'Portal Tamu | Lab RPL ITATS')
@section('meta_description', 'Portal tamu Lab RPL ITATS untuk mencatat check-in dan checkout pengunjung laboratorium.')

@section('content')
    @php
        $isCheckoutMode = request('mode') === 'checkout' || request()->filled('q');
    @endphp

    @unless ($isCheckoutMode)
        <section class="max-w-screen-2xl mx-auto px-4 md:px-8 py-6 lg:py-8">
            <div class="grid min-h-[calc(100vh-15rem)] grid-cols-1 lg:grid-cols-[0.72fr_1.28fr] gap-5 items-stretch">
                <aside class="rounded-2xl bg-[#0f7f4a] p-6 text-white shadow-xl shadow-emerald-900/10 overflow-hidden relative">
                    <div class="absolute right-0 top-0 h-24 w-2/3 rounded-bl-[2rem] bg-white/10"></div>
                    <div class="relative z-10 flex h-full flex-col justify-between gap-6">
                        <div>
                            <div class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-black uppercase tracking-widest">
                                <span class="h-2 w-2 rounded-full bg-emerald-200"></span>
                                Buku Tamu Digital
                            </div>
                            <h1 class="mt-5 text-4xl font-black leading-tight tracking-tight">
                                Portal Tamu<br>Lab RPL
                            </h1>
                            <p class="mt-3 text-sm leading-relaxed text-emerald-50">
                                Silakan check-in saat masuk. Untuk keluar, buka daftar check-in aktif lalu pilih record kunjungan Anda.
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-2xl border border-white/15 bg-white/10 p-4">
                                <div class="text-[10px] font-black uppercase tracking-widest text-emerald-100">Tanggal</div>
                                <div class="mt-1 text-lg font-black">{{ now()->translatedFormat('d M Y') }}</div>
                            </div>
                            <div class="rounded-2xl border border-white/15 bg-white/10 p-4">
                                <div class="text-[10px] font-black uppercase tracking-widest text-emerald-100">Jam</div>
                                <div class="mt-1 text-lg font-black">{{ now()->format('H:i') }} WIB</div>
                            </div>
                        </div>

                        <a href="{{ route('portal-tamu.index', ['mode' => 'checkout']) }}"
                            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-black text-[#0f7f4a] shadow-sm transition hover:bg-emerald-50">
                            <i class="fas fa-address-card"></i>
                            Lihat Daftar Check-in Aktif
                            <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-black">{{ $activeVisits->count() }}</span>
                        </a>
                    </div>
                </aside>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 lg:p-6 shadow-sm">
                    <div class="mb-4 flex items-center justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-extrabold text-slate-900">Form Check-in</h2>
                            <p class="text-xs text-slate-500 mt-1">Tanggal dan jam mulai dicatat otomatis.</p>
                        </div>
                        <div class="hidden sm:flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-[#1a4fa0]">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>

                    <form action="{{ route('portal-tamu.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @csrf

                        <div>
                            <label for="guest_name" class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">Nama Tamu</label>
                            <input type="text" name="guest_name" id="guest_name" value="{{ old('guest_name') }}"
                                class="h-11 w-full rounded-xl border border-slate-200 px-4 text-sm focus:border-[#1a4fa0] focus:outline-none focus:ring-4 focus:ring-blue-100"
                                placeholder="Nama tamu atau perwakilan" required>
                            @error('guest_name')
                                <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="guest_count" class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">Jumlah Tamu</label>
                            <input type="number" name="guest_count" id="guest_count" min="1" max="500"
                                value="{{ old('guest_count', 1) }}"
                                class="h-11 w-full rounded-xl border border-slate-200 px-4 text-sm focus:border-[#1a4fa0] focus:outline-none focus:ring-4 focus:ring-blue-100"
                                required>
                            @error('guest_count')
                                <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="activity_purpose" class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">Tujuan Aktivitas</label>
                            <textarea name="activity_purpose" id="activity_purpose" rows="2"
                                class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-[#1a4fa0] focus:outline-none focus:ring-4 focus:ring-blue-100"
                                placeholder="Contoh: kunjungan, diskusi, rapat, pengerjaan project, atau aktivitas lain" required>{{ old('activity_purpose') }}</textarea>
                            @error('activity_purpose')
                                <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="lab_condition" class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">Kondisi Lab</label>
                            <select name="lab_condition" id="lab_condition"
                                class="h-11 w-full rounded-xl border border-slate-200 px-4 text-sm focus:border-[#1a4fa0] focus:outline-none focus:ring-4 focus:ring-blue-100"
                                required>
                                <option value="">Pilih kondisi lab</option>
                                @foreach (['Baik', 'Cukup Baik', 'Perlu Perhatian'] as $condition)
                                    <option value="{{ $condition }}" @selected(old('lab_condition') === $condition)>{{ $condition }}</option>
                                @endforeach
                            </select>
                            @error('lab_condition')
                                <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="additional_note" class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">Keterangan Tambahan</label>
                            <textarea name="additional_note" id="additional_note" rows="1"
                                class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-[#1a4fa0] focus:outline-none focus:ring-4 focus:ring-blue-100"
                                placeholder="Opsional">{{ old('additional_note') }}</textarea>
                            @error('additional_note')
                                <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                            class="md:col-span-2 inline-flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-[#1a4fa0] px-6 text-sm font-extrabold text-white shadow-lg shadow-[#1a4fa0]/20 transition hover:bg-[#1a4fa0]/90 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-blue-100">
                            <i class="fas fa-right-to-bracket"></i>
                            Check In
                        </button>
                    </form>
                </div>
            </div>
        </section>
    @else
        <section class="max-w-screen-2xl mx-auto px-4 md:px-6 py-5">
            <div class="mb-5 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full border border-emerald-100 bg-emerald-50 px-3 py-1 text-xs font-black uppercase tracking-widest text-emerald-700">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        {{ $activeVisits->count() }} Check-in Aktif
                    </div>
                    <h1 class="mt-3 text-3xl font-black tracking-tight text-slate-950">Daftar Check-in Aktif</h1>
                    <p class="mt-1 text-sm text-slate-500">Pilih card kunjungan Anda, lalu klik checkout.</p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('portal-tamu.index') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50">
                        <i class="fas fa-arrow-left"></i>
                        Form Check-in
                    </a>
                    <form action="{{ route('portal-tamu.index') }}" method="GET" class="flex min-w-0 gap-2">
                        <input type="hidden" name="mode" value="checkout">
                        <input type="search" name="q" value="{{ $search }}"
                            class="min-w-0 w-full sm:w-80 rounded-xl border border-slate-200 px-4 py-3 text-sm focus:border-[#1a4fa0] focus:outline-none focus:ring-4 focus:ring-blue-100"
                            placeholder="Cari nama, tujuan, atau tanggal">
                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-3 text-sm font-bold text-white transition hover:bg-slate-800">
                            <i class="fas fa-magnifying-glass"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6">
                @forelse ($activeVisits as $visit)
                    <article class="relative flex min-h-[305px] flex-col overflow-hidden rounded-2xl bg-gradient-to-b from-[#168947] to-[#20bd5b] p-3.5 text-white shadow-lg shadow-emerald-900/10">
                        <div class="absolute right-0 top-0 h-9 w-3/5 rounded-bl-2xl bg-white/10"></div>
                        <div class="relative z-10 flex flex-1 flex-col">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="text-[9px] font-black uppercase tracking-widest text-emerald-50">Check-in Aktif</div>
                                    <h2 class="mt-2 break-words text-xl font-black leading-none">{{ $visit->guest_name }}</h2>
                                </div>
                                <span class="shrink-0 rounded-full border border-white/15 bg-white/15 px-2 py-1 text-[9px] font-black">
                                    {{ $visit->guest_count }} Tamu
                                </span>
                            </div>

                            <p class="mt-2 min-h-[38px] text-xs font-medium leading-snug text-emerald-50 line-clamp-2">
                                {{ $visit->activity_purpose }}
                            </p>

                            <div class="mt-3 flex flex-wrap gap-1.5">
                                <span class="inline-flex items-center gap-1 rounded-full border border-white/15 bg-emerald-900/25 px-2 py-1 text-[9px] font-black">
                                    <i class="fas fa-calendar-day"></i>
                                    {{ $visit->visit_date->translatedFormat('d M Y') }}
                                </span>
                                <span class="inline-flex items-center gap-1 rounded-full bg-white/20 px-2 py-1 text-[9px] font-black">
                                    {{ $visit->lab_condition }}
                                </span>
                            </div>

                            <div class="my-3 border-t border-white/20"></div>

                            <dl class="space-y-1.5 text-[11px] font-black">
                                <div class="flex items-center justify-between gap-2">
                                    <dt class="text-emerald-50">Nama:</dt>
                                    <dd class="truncate text-right">{{ $visit->guest_name }}</dd>
                                </div>
                                <div class="flex items-center justify-between gap-2">
                                    <dt class="text-emerald-50">Waktu Masuk:</dt>
                                    <dd>{{ $visit->started_at->format('H:i') }} WIB</dd>
                                </div>
                                <div class="flex items-center justify-between gap-2">
                                    <dt class="text-emerald-50">Waktu Keluar:</dt>
                                    <dd>-</dd>
                                </div>
                            </dl>

                            @if ($visit->additional_note)
                                <p class="mt-2 rounded-xl bg-white/10 px-3 py-2 text-[10px] font-semibold text-emerald-50 line-clamp-2">
                                    {{ $visit->additional_note }}
                                </p>
                            @endif

                            <form action="{{ route('portal-tamu.checkout', $visit) }}" method="POST" class="mt-auto pt-3">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl bg-slate-950 px-4 text-xs font-black text-white shadow-sm transition hover:bg-slate-800">
                                    <i class="fas fa-right-from-bracket"></i>
                                    Check Out
                                </button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-slate-200 bg-white p-10 text-center">
                        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-slate-50 text-slate-400">
                            <i class="fas fa-inbox text-xl"></i>
                        </div>
                        <h3 class="font-extrabold text-slate-900">Tidak ada check-in aktif</h3>
                        <p class="text-sm text-slate-500 mt-1">Record yang belum checkout akan tampil di sini.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-8 border-t border-slate-200 pt-6">
                <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-full border border-rose-100 bg-rose-50 px-3 py-1 text-xs font-black uppercase tracking-widest text-rose-700">
                            <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                            {{ $completedVisits->count() }} Tidak Aktif
                        </div>
                        <h2 class="mt-3 text-2xl font-black tracking-tight text-slate-950">Sudah Checkout</h2>
                    </div>
                    <p class="text-sm text-slate-500">Record yang selesai otomatis berpindah ke section ini.</p>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6">
                    @forelse ($completedVisits as $visit)
                        <article class="relative flex min-h-[285px] flex-col overflow-hidden rounded-2xl bg-gradient-to-b from-[#991b1b] to-[#ef4444] p-3.5 text-white shadow-lg shadow-rose-900/10">
                            <div class="absolute right-0 top-0 h-9 w-3/5 rounded-bl-2xl bg-white/10"></div>
                            <div class="relative z-10 flex flex-1 flex-col">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="text-[9px] font-black uppercase tracking-widest text-rose-50">Tidak Aktif</div>
                                        <h3 class="mt-2 break-words text-xl font-black leading-none">{{ $visit->guest_name }}</h3>
                                    </div>
                                    <span class="shrink-0 rounded-full border border-white/15 bg-white/15 px-2 py-1 text-[9px] font-black">
                                        {{ $visit->guest_count }} Tamu
                                    </span>
                                </div>

                                <p class="mt-2 min-h-[38px] text-xs font-medium leading-snug text-rose-50 line-clamp-2">
                                    {{ $visit->activity_purpose }}
                                </p>

                                <div class="mt-3 flex flex-wrap gap-1.5">
                                    <span class="inline-flex items-center gap-1 rounded-full border border-white/15 bg-rose-950/25 px-2 py-1 text-[9px] font-black">
                                        <i class="fas fa-calendar-check"></i>
                                        {{ $visit->visit_date->translatedFormat('d M Y') }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-white/20 px-2 py-1 text-[9px] font-black">
                                        {{ $visit->lab_condition }}
                                    </span>
                                </div>

                                <div class="my-3 border-t border-white/20"></div>

                                <dl class="space-y-1.5 text-[11px] font-black">
                                    <div class="flex items-center justify-between gap-2">
                                        <dt class="text-rose-50">Nama:</dt>
                                        <dd class="truncate text-right">{{ $visit->guest_name }}</dd>
                                    </div>
                                    <div class="flex items-center justify-between gap-2">
                                        <dt class="text-rose-50">Waktu Masuk:</dt>
                                        <dd>{{ $visit->started_at->format('H:i') }} WIB</dd>
                                    </div>
                                    <div class="flex items-center justify-between gap-2">
                                        <dt class="text-rose-50">Waktu Keluar:</dt>
                                        <dd>{{ $visit->ended_at->format('H:i') }} WIB</dd>
                                    </div>
                                </dl>

                                @if ($visit->additional_note)
                                    <p class="mt-2 rounded-xl bg-white/10 px-3 py-2 text-[10px] font-semibold text-rose-50 line-clamp-2">
                                        {{ $visit->additional_note }}
                                    </p>
                                @endif

                                <div class="mt-auto pt-3">
                                    <div class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl bg-white/95 px-4 text-xs font-black text-rose-700">
                                        <i class="fas fa-circle-check"></i>
                                        Selesai Checkout
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full rounded-2xl border border-dashed border-rose-100 bg-rose-50/50 p-8 text-center">
                            <h3 class="font-extrabold text-slate-900">Belum ada record tidak aktif</h3>
                            <p class="text-sm text-slate-500 mt-1">Card merah akan muncul setelah tamu melakukan checkout.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    @endunless
@endsection

@section('scripts')
    @if (session('success') || session('info'))
        <script>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: '{{ session('success') ? 'success' : 'info' }}',
                title: '{{ session('success') ?? session('info') }}',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                customClass: {
                    popup: 'rounded-2xl shadow-xl border border-slate-100',
                    title: 'text-sm font-bold text-slate-800'
                }
            });
        </script>
    @endif
@endsection
