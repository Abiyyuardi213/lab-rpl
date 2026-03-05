@extends('layouts.admin')

@section('title', 'Riwayat Pendaftaran')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Riwayat Pendaftaran</h1>
                <p class="text-sm text-zinc-500">Cek status verifikasi pendaftaran praktikum Anda di sini.</p>
            </div>
        </div>

        @if ($pendaftarans->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($pendaftarans as $p)
                    <div
                        class="bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden flex flex-col group transition-all hover:shadow-md">
                        <!-- Card Header -->
                        <div class="p-5 border-b border-zinc-100 flex items-start justify-between bg-zinc-50/50">
                            <div class="space-y-1">
                                <h3 class="text-sm font-bold text-zinc-900">{{ $p->praktikum->nama_praktikum }}</h3>
                                <p class="text-[10px] font-bold text-[#001f3f] font-mono tracking-wider">
                                    {{ $p->praktikum->kode_praktikum }}</p>
                            </div>
                            @php
                                $statusConfig = [
                                    'pending' => [
                                        'label' => 'Menunggu',
                                        'class' => 'bg-amber-50 text-amber-700 border-amber-100',
                                    ],
                                    'verified' => [
                                        'label' => 'Terverifikasi',
                                        'class' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    ],
                                    'rejected' => [
                                        'label' => 'Ditolak',
                                        'class' => 'bg-rose-50 text-rose-700 border-rose-100',
                                    ],
                                ];
                                $st = $statusConfig[$p->status] ?? [
                                    'label' => $p->status,
                                    'class' => 'bg-zinc-50 text-zinc-500 border-zinc-100',
                                ];
                            @endphp
                            <span
                                class="px-2.5 py-1 rounded-full text-[9px] font-black uppercase border {{ $st['class'] }}">
                                {{ $st['label'] }}
                            </span>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 space-y-4 flex-grow">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Sesi Terpilih
                                    </p>
                                    <p class="text-xs font-bold text-zinc-700">{{ $p->sesi->nama_sesi }}</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Jadwal</p>
                                    <p class="text-xs font-bold text-zinc-700 capitalize">{{ $p->sesi->hari }}</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-4">
                                <div class="space-y-1">
                                    <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Waktu</p>
                                    <p class="text-xs font-bold text-zinc-700">{{ substr($p->sesi->jam_mulai, 0, 5) }} -
                                        {{ substr($p->sesi->jam_selesai, 0, 5) }} WIB</p>
                                </div>
                                <div class="space-y-1 pt-2 border-t border-zinc-50">
                                    <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Dosen & Kelas
                                    </p>
                                    <p class="text-xs font-bold text-[#001f3f]">{{ $p->dosen_pengampu }}</p>
                                    <p class="text-[10px] font-medium text-zinc-500 uppercase">
                                        {{ $p->asal_kelas_mata_kuliah }}</p>
                                </div>
                            </div>

                            @if ($p->status == 'rejected' && $p->catatan)
                                <div class="p-3 bg-rose-50 border border-rose-100 rounded-xl">
                                    <p class="text-[9px] font-black text-rose-600 uppercase mb-1">Catatan Penolakan:</p>
                                    <p class="text-xs text-rose-700 italic">"{{ $p->catatan }}"</p>
                                </div>
                            @endif

                            @if ($p->status == 'verified')
                                <a href="{{ route('praktikan.pendaftaran.progress', $p->id) }}"
                                    class="w-full flex items-center justify-center gap-2 py-3 bg-[#001f3f] text-white rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-[#002d5a] transition-all shadow-md active:scale-95 mt-2">
                                    <i class="fas fa-tasks"></i>
                                    Lihat Progress Praktikum
                                </a>
                            @endif
                        </div>

                        <!-- Card Footer -->
                        <div class="p-4 bg-zinc-50/30 border-t border-zinc-100 text-center">
                            <p class="text-[9px] font-bold text-zinc-400 italic">Daftar pada:
                                {{ $p->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-3xl border-2 border-dashed border-zinc-200 p-12 text-center">
                <div
                    class="h-16 w-16 bg-zinc-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-zinc-100">
                    <i class="fas fa-history text-2xl text-zinc-300"></i>
                </div>
                <h3 class="text-lg font-bold text-zinc-900">Belum Ada Riwayat Pendaftaran</h3>
                <p class="text-sm text-zinc-500 mt-1 italic">Anda belum pernah melakukan pendaftaran praktikum.</p>
                <a href="{{ route('praktikan.dashboard') }}"
                    class="inline-flex items-center gap-2 mt-6 px-6 py-2.5 bg-[#001f3f] text-white rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-[#002d5a] transition-all">
                    Lihat Praktikum Tersedia
                </a>
            </div>
        @endif
    </div>
@endsection
