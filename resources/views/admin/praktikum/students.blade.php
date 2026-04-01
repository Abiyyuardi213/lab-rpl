@extends('layouts.admin')

@section('title', 'Daftar Praktikan - ' . $praktikum->nama_praktikum)

@section('content')
    <div class="space-y-4">

        {{-- ── PAGE HEADER ──────────────────────────────────────────────── --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2">

            {{-- Left: Breadcrumb + Title --}}
            <div>
                <a href="{{ route('admin.praktikum.index') }}"
                    class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-zinc-400 hover:text-[#001f3f] transition-colors mb-1.5">
                    <i class="fas fa-arrow-left text-[9px]"></i>
                    Kembali ke Praktikum
                </a>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-lg sm:text-xl font-black tracking-tight text-zinc-900 leading-tight">Daftar Praktikan</h1>
                    <span class="inline-flex items-center bg-[#001f3f] text-white px-2 py-0.5 rounded text-[10px] font-black font-mono tracking-widest">
                        {{ $praktikum->kode_praktikum }}
                    </span>
                </div>
                <p class="text-xs text-zinc-400 font-medium mt-0.5">
                    <i class="fas fa-flask text-[9px] mr-1 text-zinc-300"></i>
                    {{ $praktikum->nama_praktikum }}
                </p>
            </div>

            {{-- Right: Nav trail --}}
            <div class="hidden sm:flex items-center gap-1.5 text-[11px] text-zinc-400 font-medium pb-1">
                <i class="fas fa-home text-[9px]"></i>
                <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-700 transition-colors">Dashboard</a>
                <span class="text-zinc-200">/</span>
                <a href="{{ route('admin.praktikum.index') }}" class="hover:text-zinc-700 transition-colors">Praktikum</a>
                <span class="text-zinc-200">/</span>
                <span class="text-zinc-600 font-bold">Praktikan</span>
            </div>

        </div>

        @include('admin.praktikum.partials.student-table')
        @include('admin.praktikum.partials.student-kanban')
    </div>{{-- End outer space-y-4 --}}

    @include('admin.praktikum.partials.student-scripts')
@endsection