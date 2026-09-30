@extends('layouts.admin')

@section('title', 'Daftar Praktikan - ' . $praktikum->nama_praktikum)

@section('content')
    <div class="space-y-4">

        <!-- Header Section -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Manajemen Praktikan</h1>
                <p class="text-sm text-zinc-500 mt-1">
                    Praktikum: <span class="font-bold text-zinc-800">{{ $praktikum->nama_praktikum }}</span> 
                    <span class="font-mono text-xs px-2 py-0.5 rounded bg-zinc-100 border border-zinc-200 text-zinc-600 ml-1">{{ $praktikum->kode_praktikum }}</span>
                </p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                <span>/</span>
                <a href="{{ route('admin.praktikum.index') }}" class="hover:text-zinc-900 transition-colors">Praktikum</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Praktikan</span>
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