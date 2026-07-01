@extends('layouts.admin')

@section('title', 'Daftar Tamu')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Daftar Tamu Lab</h1>
                <p class="text-sm text-zinc-500 mt-1">Pantau seluruh record check-in dan checkout pengunjung Lab RPL.</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Daftar Tamu</span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
                <div class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Record</div>
                <div class="mt-2 text-2xl font-black text-zinc-900">{{ $summary['records'] }}</div>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
                <div class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Total Tamu</div>
                <div class="mt-2 text-2xl font-black text-zinc-900">{{ $summary['guests'] }}</div>
            </div>
            <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-5 shadow-sm">
                <div class="text-[10px] font-black uppercase tracking-widest text-emerald-700">Masih Aktif</div>
                <div class="mt-2 text-2xl font-black text-emerald-700">{{ $summary['active'] }}</div>
            </div>
            <div class="rounded-xl border border-blue-100 bg-blue-50 p-5 shadow-sm">
                <div class="text-[10px] font-black uppercase tracking-widest text-blue-700">Selesai</div>
                <div class="mt-2 text-2xl font-black text-blue-700">{{ $summary['completed'] }}</div>
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-zinc-100 bg-zinc-50/30 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-lg font-black text-zinc-900">Import Rekap Excel Manual</h2>
                    <p class="text-sm text-zinc-500 mt-1">Unduh template, isi data rekap lama, lalu upload untuk review sebelum disetujui.</p>
                </div>
                <a href="{{ route('admin.guest-visits.template') }}"
                    class="inline-flex h-10 items-center justify-center rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-2 text-sm font-black text-emerald-700 hover:bg-emerald-100 transition-colors">
                    <i class="fas fa-file-excel mr-2"></i>
                    Unduh Template Excel
                </a>
            </div>

            <div class="p-6">
                <form action="{{ route('admin.guest-visits.import-preview') }}" method="POST" enctype="multipart/form-data"
                    class="grid grid-cols-1 gap-4 lg:grid-cols-[1fr_auto] lg:items-start">
                    @csrf
                    <div>
                        <label for="file_excel" class="text-[10px] font-black text-zinc-400 uppercase tracking-widest ml-1">Upload File Excel</label>
                        <label for="file_excel" id="guestVisitDropzone"
                            class="mt-1 flex min-h-14 cursor-pointer items-center justify-between gap-4 rounded-xl border border-dashed border-zinc-300 bg-white px-4 py-3 text-sm shadow-sm transition hover:border-[#1a4fa0] hover:bg-blue-50/30">
                            <span class="flex min-w-0 items-center gap-3">
                                <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-zinc-900 text-white">
                                    <i class="fas fa-file-arrow-up text-xs"></i>
                                </span>
                                <span class="min-w-0">
                                    <span id="guestVisitFileName" class="block truncate font-bold text-zinc-900">Drag & drop file Excel di sini atau klik untuk memilih</span>
                                    <span class="block text-xs text-zinc-500">Mendukung .xlsx, .xls, dan .csv</span>
                                </span>
                            </span>
                            <span class="hidden rounded-lg bg-zinc-100 px-3 py-1 text-xs font-black text-zinc-600 sm:inline-flex">Pilih File</span>
                        </label>
                        <input type="file" id="file_excel" name="file_excel" accept=".xlsx,.xls,.csv" class="sr-only" required>
                        <p class="mt-2 text-xs text-zinc-500">Kolom wajib: tanggal, jam_mulai, tujuan_aktivitas, nama_tamu, jumlah_tamu, kondisi_lab.</p>
                    </div>
                    <button type="submit"
                        class="inline-flex h-14 items-center justify-center rounded-xl bg-[#1a4fa0] px-5 py-2 text-sm font-black text-white shadow hover:bg-[#1a4fa0]/90 transition-colors lg:mt-[21px]">
                        <i class="fas fa-eye mr-2"></i>
                        Review Import
                    </button>
                </form>
            </div>

            @isset($previewRows)
                @php
                    $validPreviewCount = collect($previewRows)->where('is_valid', true)->count();
                    $invalidPreviewCount = collect($previewRows)->where('is_valid', false)->count();
                @endphp

                <div class="border-t border-zinc-100 bg-white">
                    <div class="p-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <h3 class="text-base font-black text-zinc-900">Review Data Import</h3>
                            <p class="text-sm text-zinc-500 mt-1">
                                {{ $validPreviewCount }} baris siap import, {{ $invalidPreviewCount }} baris perlu diperbaiki.
                            </p>
                        </div>
                        <div class="flex flex-col gap-2 sm:flex-row">
                            <form action="{{ route('admin.guest-visits.import-cancel') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="inline-flex h-10 items-center justify-center rounded-xl border border-rose-100 bg-rose-50 px-5 py-2 text-sm font-black text-rose-700 hover:bg-rose-100 transition-colors">
                                    <i class="fas fa-times-circle mr-2"></i>
                                    Batal Import
                                </button>
                            </form>
                            @if ($validPreviewCount > 0)
                                <form action="{{ route('admin.guest-visits.import-confirm') }}" method="POST" id="confirmGuestVisitImportForm">
                                    @csrf
                                    <button type="button"
                                        onclick="openGuestVisitImportModal()"
                                        class="inline-flex h-10 items-center justify-center rounded-xl bg-emerald-600 px-5 py-2 text-sm font-black text-white shadow hover:bg-emerald-700 transition-colors">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Setujui Import
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    @if (!empty($previewErrors))
                        <div class="mx-6 mb-4 rounded-xl border border-rose-100 bg-rose-50 p-4">
                            <div class="text-xs font-black uppercase tracking-widest text-rose-700 mb-2">Catatan Error</div>
                            <ul class="space-y-1 text-xs font-semibold text-rose-700">
                                @foreach ($previewErrors as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="overflow-x-auto border-t border-zinc-100">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-zinc-50 border-b border-zinc-100 text-zinc-500 font-medium h-10">
                                <tr>
                                    <th class="px-6 align-middle font-medium text-zinc-500">BARIS</th>
                                    <th class="px-6 align-middle font-medium text-zinc-500">TAMU</th>
                                    <th class="px-6 align-middle font-medium text-zinc-500">TANGGAL</th>
                                    <th class="px-6 align-middle font-medium text-zinc-500">WAKTU</th>
                                    <th class="px-6 align-middle font-medium text-zinc-500">TUJUAN</th>
                                    <th class="px-6 align-middle font-medium text-zinc-500 text-center">STATUS</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100">
                                @forelse ($previewRows as $row)
                                    <tr @class(['bg-rose-50/40' => !$row['is_valid']])>
                                        <td class="px-6 py-4 text-zinc-500">{{ $row['row_number'] }}</td>
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-zinc-900">{{ $row['data']['guest_name'] ?: '-' }}</div>
                                            <div class="text-[10px] font-bold uppercase text-zinc-500">{{ $row['data']['guest_count'] ?: '-' }} orang</div>
                                        </td>
                                        <td class="px-6 py-4 text-xs font-semibold text-zinc-700">{{ $row['data']['visit_date'] ?: '-' }}</td>
                                        <td class="px-6 py-4 text-xs font-semibold text-zinc-700">
                                            {{ $row['data']['started_at'] ? $row['data']['started_at']->format('H:i') : '-' }}
                                            -
                                            {{ $row['data']['ended_at'] ? $row['data']['ended_at']->format('H:i') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 max-w-sm">
                                            <p class="text-xs text-zinc-700 line-clamp-2">{{ $row['data']['activity_purpose'] ?: '-' }}</p>
                                            @if (!$row['is_valid'])
                                                <p class="mt-1 text-[10px] font-bold text-rose-600">{{ implode(' ', $row['errors']) }}</p>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if ($row['is_valid'])
                                                <span class="inline-flex rounded-full border border-emerald-100 bg-emerald-50 px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider text-emerald-700">Siap</span>
                                            @else
                                                <span class="inline-flex rounded-full border border-rose-100 bg-rose-50 px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider text-rose-700">Error</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-10 text-center text-sm text-zinc-500">Tidak ada data preview.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($validPreviewCount > 0)
                    <div id="guestVisitImportModal" class="fixed inset-0 z-[100] hidden">
                        <div class="absolute inset-0 bg-zinc-950/60 backdrop-blur-sm" onclick="closeGuestVisitImportModal()"></div>
                        <div class="relative mx-auto flex min-h-screen w-full max-w-md items-center px-4">
                            <div class="w-full rounded-2xl border border-zinc-200 bg-white shadow-2xl">
                                <div class="p-6 border-b border-zinc-100">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <div class="mb-3 inline-flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                                                <i class="fas fa-file-import"></i>
                                            </div>
                                            <h3 class="text-lg font-black text-zinc-900">Setujui Import Data?</h3>
                                            <p class="mt-1 text-sm text-zinc-500">Data valid akan disimpan ke daftar tamu lab.</p>
                                        </div>
                                        <button type="button" onclick="closeGuestVisitImportModal()"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-zinc-400 hover:bg-zinc-100 hover:text-zinc-700">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="p-6">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4">
                                            <div class="text-[10px] font-black uppercase tracking-widest text-emerald-700">Siap Import</div>
                                            <div class="mt-1 text-2xl font-black text-emerald-700">{{ $validPreviewCount }}</div>
                                        </div>
                                        <div class="rounded-xl border border-rose-100 bg-rose-50 p-4">
                                            <div class="text-[10px] font-black uppercase tracking-widest text-rose-700">Dilewati</div>
                                            <div class="mt-1 text-2xl font-black text-rose-700">{{ $invalidPreviewCount }}</div>
                                        </div>
                                    </div>
                                    <p class="mt-4 text-sm leading-relaxed text-zinc-500">
                                        Baris yang error tidak akan disimpan. Pastikan hasil review sudah sesuai sebelum melanjutkan.
                                    </p>
                                </div>

                                <div class="flex flex-col-reverse gap-3 border-t border-zinc-100 p-6 sm:flex-row sm:justify-end">
                                    <form action="{{ route('admin.guest-visits.import-cancel') }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex h-10 w-full items-center justify-center rounded-xl border border-rose-100 bg-rose-50 px-5 text-sm font-black text-rose-700 hover:bg-rose-100 sm:w-auto">
                                            <i class="fas fa-ban mr-2"></i>
                                            Batal Import
                                        </button>
                                    </form>
                                    <button type="button" onclick="closeGuestVisitImportModal()"
                                        class="inline-flex h-10 items-center justify-center rounded-xl border border-zinc-200 bg-white px-5 text-sm font-bold text-zinc-600 hover:bg-zinc-50">
                                        Kembali Review
                                    </button>
                                    <button type="button" onclick="submitGuestVisitImport()"
                                        class="inline-flex h-10 items-center justify-center rounded-xl bg-emerald-600 px-5 text-sm font-black text-white shadow hover:bg-emerald-700">
                                        <i class="fas fa-check mr-2"></i>
                                        Ya, Import
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endisset
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-zinc-100 bg-zinc-50/30">
                <form action="{{ route('admin.guest-visits.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="space-y-1.5">
                        <label for="start_date" class="text-[10px] font-black text-zinc-400 uppercase tracking-widest ml-1">Dari Tanggal</label>
                        <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                            class="flex h-10 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-zinc-950/5">
                    </div>
                    <div class="space-y-1.5">
                        <label for="end_date" class="text-[10px] font-black text-zinc-400 uppercase tracking-widest ml-1">Sampai Tanggal</label>
                        <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                            class="flex h-10 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-zinc-950/5">
                    </div>
                    <div class="space-y-1.5">
                        <label for="q" class="text-[10px] font-black text-zinc-400 uppercase tracking-widest ml-1">Cari</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 text-xs"></i>
                            <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Nama / tujuan / kondisi..."
                                class="flex h-10 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 pl-9 text-sm shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-zinc-950/5">
                        </div>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit"
                            class="inline-flex h-10 flex-1 items-center justify-center rounded-xl bg-[#1a4fa0] px-4 py-2 text-sm font-bold text-white shadow hover:bg-[#1a4fa0]/90 transition-colors">
                            <i class="fas fa-filter mr-2 text-xs"></i>
                            Filter
                        </button>
                        @if (request()->anyFilled(['start_date', 'end_date', 'q']))
                            <a href="{{ route('admin.guest-visits.index') }}"
                                class="inline-flex h-10 items-center justify-center rounded-xl border border-zinc-200 bg-white px-4 py-2 text-sm font-bold text-zinc-600 hover:bg-zinc-50 transition-colors">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-zinc-50 border-b border-zinc-100 text-zinc-500 font-medium h-10">
                        <tr>
                            <th class="px-6 align-middle font-medium text-zinc-500 w-12 text-center">NO</th>
                            <th class="px-6 align-middle font-medium text-zinc-500">TAMU</th>
                            <th class="px-6 align-middle font-medium text-zinc-500">TANGGAL</th>
                            <th class="px-6 align-middle font-medium text-zinc-500">WAKTU</th>
                            <th class="px-6 align-middle font-medium text-zinc-500">TUJUAN</th>
                            <th class="px-6 align-middle font-medium text-zinc-500">KONDISI</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-center">STATUS</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 text-zinc-900">
                        @forelse ($guestVisits as $index => $visit)
                            <tr class="hover:bg-zinc-50/50 transition-colors">
                                <td class="px-6 py-4 text-center text-zinc-500">
                                    {{ $guestVisits->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-zinc-900">{{ $visit->guest_name }}</span>
                                        <span class="text-[10px] text-zinc-500 font-bold uppercase">{{ $visit->guest_count }} orang</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs font-semibold text-zinc-700">{{ $visit->visit_date->translatedFormat('d M Y') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-zinc-900">Masuk {{ $visit->started_at->format('H:i') }} WIB</span>
                                        <span class="text-[10px] font-bold text-zinc-500">
                                            Keluar {{ $visit->ended_at ? $visit->ended_at->format('H:i') . ' WIB' : '-' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 max-w-sm">
                                    <p class="text-xs font-medium text-zinc-700 line-clamp-2">{{ $visit->activity_purpose }}</p>
                                    @if ($visit->additional_note)
                                        <p class="text-[10px] text-zinc-400 mt-1 line-clamp-1">{{ $visit->additional_note }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="rounded-lg border border-zinc-100 bg-zinc-50 px-2 py-1 text-[11px] font-bold text-zinc-600">
                                        {{ $visit->lab_condition }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($visit->ended_at)
                                        <span class="inline-flex items-center rounded-full border border-blue-100 bg-blue-50 px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider text-blue-700">
                                            Checkout
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full border border-emerald-100 bg-emerald-50 px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider text-emerald-700">
                                            Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @php
                                        $editPayload = [
                                            'id' => $visit->id,
                                            'visit_date' => $visit->visit_date->format('Y-m-d'),
                                            'started_time' => $visit->started_at->format('H:i'),
                                            'ended_time' => $visit->ended_at ? $visit->ended_at->format('H:i') : '',
                                            'guest_name' => $visit->guest_name,
                                            'guest_count' => $visit->guest_count,
                                            'activity_purpose' => $visit->activity_purpose,
                                            'lab_condition' => $visit->lab_condition,
                                            'additional_note' => $visit->additional_note,
                                        ];
                                    @endphp
                                    <button type="button"
                                        onclick='openEditGuestVisitModal(@json($editPayload))'
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-md text-zinc-400 transition-colors hover:bg-blue-50 hover:text-[#1a4fa0]">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-zinc-400">
                                        <div class="h-16 w-16 rounded-2xl bg-zinc-50 flex items-center justify-center mb-4 border border-zinc-100">
                                            <i class="fas fa-address-book text-2xl opacity-30"></i>
                                        </div>
                                        <h3 class="text-sm font-black uppercase tracking-[0.2em]">Data Kosong</h3>
                                        <p class="text-xs mt-1">Belum ada tamu pada filter yang dipilih.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($guestVisits->hasPages())
                <div class="px-6 py-4 border-t border-zinc-100">
                    {{ $guestVisits->links() }}
                </div>
            @endif
        </div>
    </div>

    <div id="editGuestVisitModal" class="fixed inset-0 z-[100] hidden">
        <div class="absolute inset-0 bg-zinc-950/60" onclick="closeEditGuestVisitModal()"></div>
        <div class="relative flex min-h-screen w-full items-center justify-center px-4 py-6">
            <div class="flex max-h-[calc(100vh-5rem)] w-full max-w-3xl flex-col overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-xl">
                <div class="shrink-0 flex items-start justify-between gap-4 border-b border-zinc-100 p-6">
                    <div>
                        <div class="mb-3 inline-flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-[#1a4fa0]">
                            <i class="fas fa-user-pen"></i>
                        </div>
                        <h3 class="text-lg font-black text-zinc-900">Edit Data Tamu</h3>
                        <p class="mt-1 text-sm text-zinc-500">Perbarui record kunjungan tamu lab.</p>
                    </div>
                    <button type="button" onclick="closeEditGuestVisitModal()"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-zinc-400 hover:bg-zinc-100 hover:text-zinc-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="editGuestVisitForm" method="POST" class="flex min-h-0 flex-1 flex-col">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                    <input type="hidden" name="q" value="{{ request('q') }}">
                    <input type="hidden" name="page" value="{{ request('page') }}">

                    <div class="grid min-h-0 grid-cols-1 gap-4 overflow-y-auto overscroll-contain p-6 md:grid-cols-2">
                        <div>
                            <label for="edit_visit_date" class="text-[10px] font-black text-zinc-400 uppercase tracking-widest ml-1">Tanggal</label>
                            <input type="date" id="edit_visit_date" name="visit_date" required
                                class="mt-1 h-10 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-zinc-950/5">
                        </div>
                        <div>
                            <label for="edit_guest_name" class="text-[10px] font-black text-zinc-400 uppercase tracking-widest ml-1">Nama Tamu</label>
                            <input type="text" id="edit_guest_name" name="guest_name" required
                                class="mt-1 h-10 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-zinc-950/5">
                        </div>
                        <div>
                            <label for="edit_started_time" class="text-[10px] font-black text-zinc-400 uppercase tracking-widest ml-1">Jam Mulai</label>
                            <input type="time" id="edit_started_time" name="started_time" required
                                class="mt-1 h-10 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-zinc-950/5">
                        </div>
                        <div>
                            <label for="edit_ended_time" class="text-[10px] font-black text-zinc-400 uppercase tracking-widest ml-1">Jam Keluar</label>
                            <input type="time" id="edit_ended_time" name="ended_time"
                                class="mt-1 h-10 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-zinc-950/5">
                        </div>
                        <div>
                            <label for="edit_guest_count" class="text-[10px] font-black text-zinc-400 uppercase tracking-widest ml-1">Jumlah Tamu</label>
                            <input type="number" id="edit_guest_count" name="guest_count" min="1" max="500" required
                                class="mt-1 h-10 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-zinc-950/5">
                        </div>
                        <div>
                            <label for="edit_lab_condition" class="text-[10px] font-black text-zinc-400 uppercase tracking-widest ml-1">Kondisi Lab</label>
                            <select id="edit_lab_condition" name="lab_condition" required
                                class="mt-1 h-10 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-zinc-950/5">
                                <option value="Baik">Baik</option>
                                <option value="Cukup Baik">Cukup Baik</option>
                                <option value="Perlu Perhatian">Perlu Perhatian</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label for="edit_activity_purpose" class="text-[10px] font-black text-zinc-400 uppercase tracking-widest ml-1">Tujuan Aktivitas</label>
                            <textarea id="edit_activity_purpose" name="activity_purpose" rows="3" required
                                class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-zinc-950/5"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label for="edit_additional_note" class="text-[10px] font-black text-zinc-400 uppercase tracking-widest ml-1">Keterangan Tambahan</label>
                            <textarea id="edit_additional_note" name="additional_note" rows="2"
                                class="mt-1 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-zinc-950/5"></textarea>
                        </div>
                    </div>

                    <div class="shrink-0 flex flex-col-reverse gap-3 border-t border-zinc-100 bg-white p-4 sm:flex-row sm:justify-end">
                        <button type="button" onclick="closeEditGuestVisitModal()"
                            class="inline-flex h-10 items-center justify-center rounded-xl border border-zinc-200 bg-white px-5 text-sm font-bold text-zinc-600 hover:bg-zinc-50">
                            Batal
                        </button>
                        <button type="submit"
                            class="inline-flex h-10 items-center justify-center rounded-xl bg-[#1a4fa0] px-5 text-sm font-black text-white shadow hover:bg-[#1a4fa0]/90">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @if (session('import_success'))
        <script>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '{{ session('import_success') }}',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                customClass: {
                    popup: 'rounded-xl shadow-xl border border-emerald-100',
                    title: 'text-sm font-bold text-slate-800'
                }
            });
        </script>
    @endif

    <script>
        const guestVisitUpdateUrlTemplate = '{{ route('admin.guest-visits.update', '__ID__') }}';
        const guestVisitDropzone = document.getElementById('guestVisitDropzone');
        const guestVisitFileInput = document.getElementById('file_excel');
        const guestVisitFileName = document.getElementById('guestVisitFileName');

        function setGuestVisitFile(file) {
            if (!file || !guestVisitFileInput) {
                return;
            }

            const transfer = new DataTransfer();
            transfer.items.add(file);
            guestVisitFileInput.files = transfer.files;
            guestVisitFileName.textContent = file.name;
        }

        if (guestVisitDropzone && guestVisitFileInput) {
            ['dragenter', 'dragover'].forEach((eventName) => {
                guestVisitDropzone.addEventListener(eventName, (event) => {
                    event.preventDefault();
                    guestVisitDropzone.classList.add('border-[#1a4fa0]', 'bg-blue-50');
                });
            });

            ['dragleave', 'drop'].forEach((eventName) => {
                guestVisitDropzone.addEventListener(eventName, (event) => {
                    event.preventDefault();
                    guestVisitDropzone.classList.remove('border-[#1a4fa0]', 'bg-blue-50');
                });
            });

            guestVisitDropzone.addEventListener('drop', (event) => {
                const file = event.dataTransfer.files[0];
                setGuestVisitFile(file);
            });

            guestVisitFileInput.addEventListener('change', (event) => {
                const file = event.target.files[0];
                if (file) {
                    guestVisitFileName.textContent = file.name;
                }
            });
        }

        function openGuestVisitImportModal() {
            document.getElementById('guestVisitImportModal')?.classList.remove('hidden');
        }

        function closeGuestVisitImportModal() {
            document.getElementById('guestVisitImportModal')?.classList.add('hidden');
        }

        function submitGuestVisitImport() {
            const form = document.getElementById('confirmGuestVisitImportForm');
            const modal = document.getElementById('guestVisitImportModal');
            const button = modal?.querySelector('button[onclick="submitGuestVisitImport()"]');

            if (button) {
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Mengimport...';
            }

            form?.submit();
        }

        function openEditGuestVisitModal(visit) {
            const modal = document.getElementById('editGuestVisitModal');
            const form = document.getElementById('editGuestVisitForm');
            const conditionSelect = document.getElementById('edit_lab_condition');

            form.action = guestVisitUpdateUrlTemplate.replace('__ID__', visit.id);
            document.getElementById('edit_visit_date').value = visit.visit_date ?? '';
            document.getElementById('edit_started_time').value = visit.started_time ?? '';
            document.getElementById('edit_ended_time').value = visit.ended_time ?? '';
            document.getElementById('edit_guest_name').value = visit.guest_name ?? '';
            document.getElementById('edit_guest_count').value = visit.guest_count ?? 1;
            document.getElementById('edit_activity_purpose').value = visit.activity_purpose ?? '';
            document.getElementById('edit_additional_note').value = visit.additional_note ?? '';

            if (visit.lab_condition && !Array.from(conditionSelect.options).some((option) => option.value === visit.lab_condition)) {
                const option = document.createElement('option');
                option.value = visit.lab_condition;
                option.textContent = visit.lab_condition;
                conditionSelect.appendChild(option);
            }
            conditionSelect.value = visit.lab_condition ?? 'Baik';

            modal?.classList.remove('hidden');
        }

        function closeEditGuestVisitModal() {
            document.getElementById('editGuestVisitModal')?.classList.add('hidden');
        }
    </script>
@endpush
