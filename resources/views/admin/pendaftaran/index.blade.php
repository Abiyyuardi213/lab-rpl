@extends('layouts.admin')

@section('title', 'Manajemen Pendaftaran')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Pendaftaran Praktikum</h1>
                <p class="text-sm text-zinc-500">Verifikasi dokumen dan kelola pendaftaran mahasiswa.</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="flex bg-white border border-zinc-200 rounded-xl p-1 shadow-sm">
                    <a href="{{ route('admin.pendaftaran.index') }}"
                        class="px-4 py-1.5 text-[10px] font-bold uppercase rounded-lg {{ !request('status') ? 'bg-[#001f3f] text-white' : 'text-zinc-500 hover:text-zinc-900' }} transition-all">Semua</a>
                    <a href="{{ route('admin.pendaftaran.index', ['status' => 'pending']) }}"
                        class="px-4 py-1.5 text-[10px] font-bold uppercase rounded-lg {{ request('status') == 'pending' ? 'bg-amber-500 text-white' : 'text-zinc-500 hover:text-zinc-900' }} transition-all">Pending</a>
                    <a href="{{ route('admin.pendaftaran.index', ['status' => 'verified']) }}"
                        class="px-4 py-1.5 text-[10px] font-bold uppercase rounded-lg {{ request('status') == 'verified' ? 'bg-emerald-500 text-white' : 'text-zinc-500 hover:text-zinc-900' }} transition-all">Verified</a>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="rounded-2xl border border-zinc-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-zinc-50/50 border-b border-zinc-100">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest">Mahasiswa
                            </th>
                            <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest">Praktikum /
                                Sesi</th>
                            <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest">Tanggal
                                Daftar</th>
                            <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest">Status</th>
                            <th
                                class="px-6 py-4 text-center text-[10px] font-black text-zinc-400 uppercase tracking-widest">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse($pendaftarans as $p)
                            <tr class="hover:bg-zinc-50/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-9 w-9 rounded-full bg-zinc-100 flex items-center justify-center border border-zinc-200 overflow-hidden">
                                            <img src="{{ $p->foto_almamater ? asset('storage/' . $p->foto_almamater) : 'https://ui-avatars.com/api/?name=' . urlencode($p->user->name) }}"
                                                class="h-full w-full object-cover">
                                        </div>
                                        <div>
                                            <div class="font-bold text-zinc-900">{{ $p->user->name }}</div>
                                            <div class="text-[10px] font-bold text-[#001f3f] font-mono uppercase">
                                                {{ $p->user->npm }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-zinc-900">{{ $p->praktikum->nama_praktikum }}</div>
                                    <div class="text-[10px] text-zinc-500 font-medium capitalize">{{ $p->sesi->nama_sesi }}
                                        - {{ $p->sesi->hari }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-zinc-600 font-medium">{{ $p->created_at->format('d M Y') }}</div>
                                    <div class="text-[10px] text-zinc-400 font-mono uppercase">
                                        {{ $p->created_at->format('H:i') }} WIB</div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusConfig = [
                                            'pending' => [
                                                'label' => 'Pending',
                                                'class' => 'bg-amber-50 text-amber-700 border-amber-100',
                                            ],
                                            'verified' => [
                                                'label' => 'Verified',
                                                'class' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                            ],
                                            'rejected' => [
                                                'label' => 'Rejected',
                                                'class' => 'bg-rose-50 text-rose-700 border-rose-100',
                                            ],
                                        ];
                                        $st = $statusConfig[$p->status] ?? [
                                            'label' => $p->status,
                                            'class' => 'bg-zinc-50 text-zinc-500 border-zinc-100',
                                        ];
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-[9px] font-black uppercase border {{ $st['class'] }}">
                                        {{ $st['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.pendaftaran.show', $p->id) }}"
                                            class="h-9 w-9 inline-flex items-center justify-center rounded-xl border border-zinc-200 bg-white text-zinc-600 hover:bg-[#001f3f] hover:text-white transition-all shadow-sm active:scale-95 group">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-2 text-zinc-400 italic">
                                        <i class="fas fa-inbox text-3xl opacity-20"></i>
                                        <span>Tidak ada data pendaftaran</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
