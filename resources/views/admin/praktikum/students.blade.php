@extends('layouts.admin')

@section('title', 'Daftar Praktikan - ' . $praktikum->nama_praktikum)

@section('content')
    <div class="space-y-4">

        {{-- ── PAGE HEADER ──────────────────────────────────────────────── --}}
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">

            {{-- Left: Breadcrumb + Title --}}
            <div class="min-w-0">
                <a href="{{ route('admin.praktikum.index') }}"
                    class="inline-flex items-center gap-1.5 text-[10px] font-extrabold text-zinc-400 hover:text-[#001f3f] transition-all mb-2 group">
                    <i class="fas fa-arrow-left text-[8px] group-hover:-translate-x-0.5 transition-transform"></i>
                    <span class="uppercase tracking-widest">Daftar Praktikum</span>
                </a>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-xl sm:text-2xl font-black tracking-tight text-zinc-900 leading-none">Manajemen Praktikan</h1>
                    <div class="flex items-center bg-[#001f3f] text-white px-2.5 py-1 rounded-lg text-[10px] font-black font-mono tracking-widest shadow-lg shadow-[#001f3f]/10">
                        {{ $praktikum->kode_praktikum }}
                    </div>
                </div>
                <div class="flex items-center gap-2 mt-2">
                    <div class="flex -space-x-2">
                        <div class="w-5 h-5 rounded-full bg-zinc-100 flex items-center justify-center border-2 border-white">
                            <i class="fas fa-flask text-[8px] text-zinc-400"></i>
                        </div>
                    </div>
                    <p class="text-xs text-zinc-500 font-bold tracking-tight truncate max-w-[250px] sm:max-w-md">
                        {{ $praktikum->nama_praktikum }}
                    </p>
                </div>
            </div>

            {{-- Right: Breadcrumb Trail (Hidden on small mobile) --}}
            <div class="hidden md:flex items-center gap-2 text-[10px] text-zinc-400 font-bold uppercase tracking-widest pb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-[#001f3f] transition-colors">Admin</a>
                <i class="fas fa-chevron-right text-[7px] opacity-30"></i>
                <a href="{{ route('admin.praktikum.index') }}" class="hover:text-[#001f3f] transition-colors">Praktikum</a>
                <i class="fas fa-chevron-right text-[7px] opacity-30"></i>
                <span class="text-zinc-600 font-black">Praktikan</span>
            </div>

        </div>

        @include('admin.praktikum.partials.student-table')
        @include('admin.praktikum.partials.student-kanban')

        {{-- ── IMPORT REVIEW MODAL ────────────────────────────────────── --}}
        <div id="importReviewModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-zinc-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeImportModal()"></div>

                {{-- Modal Panel --}}
                <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl border border-zinc-100">
                    <div id="importModalContent">
                        {{-- Content will be loaded here via AJAX --}}
                        <div class="p-12 flex flex-col items-center justify-center space-y-4">
                            <div class="w-16 h-16 border-4 border-[#001f3f]/10 border-t-[#001f3f] rounded-full animate-spin"></div>
                            <p class="text-xs font-black text-[#001f3f] uppercase tracking-widest">Memproses File CSV...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>{{-- End outer space-y-4 --}}

    @include('admin.praktikum.partials.student-scripts')
@endsection