@extends('layouts.admin')

@section('title', 'Riwayat Presensi')

@section('content')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Riwayat Presensi</h1>
                <p class="text-sm text-zinc-500 mt-1">Lihat dan kelola seluruh riwayat kehadiran praktikan.</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Presensi</span>
            </div>
        </div>

        <!-- Filters & Search Section -->
        <div class="bg-white rounded-xl border border-zinc-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-zinc-100 bg-zinc-50/30">
                <form action="{{ route('admin.presensi.index') }}" method="GET" id="filterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Praktikum Filter -->
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-zinc-400 uppercase tracking-widest ml-1">Pilih Praktikum</label>
                        <select name="praktikum_id" onchange="this.form.submit()" 
                            class="flex h-10 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-zinc-950/5">
                            <option value="">Semua Praktikum</option>
                            @foreach($praktikums as $p)
                                <option value="{{ $p->id }}" {{ request('praktikum_id') == $p->id ? 'selected' : '' }}>{{ $p->nama_praktikum }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sesi Filter -->
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-zinc-400 uppercase tracking-widest ml-1">Pilih Sesi</label>
                        <select name="sesi_id" onchange="this.form.submit()" {{ $sesis->isEmpty() ? 'disabled' : '' }}
                            class="flex h-10 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-zinc-950/5 disabled:opacity-50">
                            <option value="">Semua Sesi</option>
                            @foreach($sesis as $s)
                                <option value="{{ $s->id }}" {{ request('sesi_id') == $s->id ? 'selected' : '' }}>
                                    {{ $s->nama_sesi }} ({{ $s->hari }}, {{ \Carbon\Carbon::parse($s->jam_mulai)->format('H:i') }}-{{ \Carbon\Carbon::parse($s->jam_selesai)->format('H:i') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Jadwal Filter -->
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-zinc-400 uppercase tracking-widest ml-1">Jadwal / Modul</label>
                        <select name="jadwal_id" onchange="this.form.submit()" {{ $jadwals->isEmpty() ? 'disabled' : '' }}
                            class="flex h-10 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 text-sm shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-zinc-950/5 disabled:opacity-50">
                            <option value="">Semua Modul</option>
                            @foreach($jadwals as $j)
                                <option value="{{ $j->id }}" {{ request('jadwal_id') == $j->id ? 'selected' : '' }}>{{ $j->judul_modul }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Search -->
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black text-zinc-400 uppercase tracking-widest ml-1">Cari Spesifik</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 text-xs"></i>
                            <input type="text" id="customSearch" placeholder="Nama / NPM..."
                                class="flex h-10 w-full rounded-xl border border-zinc-200 bg-white px-3 py-2 pl-9 text-sm shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-zinc-950/5">
                        </div>
                    </div>
                </form>
            </div>

            <div class="px-6 py-3 bg-zinc-50/50 border-b border-zinc-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-tight">Tampilkan</span>
                    <select id="customLength"
                        class="h-7 rounded-lg border border-zinc-200 bg-white px-2 text-[11px] font-bold shadow-sm focus:outline-none active:scale-95 transition-all">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                    </select>
                    <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-tight">Data</span>
                </div>
                @if(request()->anyFilled(['praktikum_id', 'sesi_id', 'jadwal_id']))
                    <a href="{{ route('admin.presensi.index') }}" class="text-[10px] font-black text-rose-600 uppercase tracking-widest hover:text-rose-700 transition-colors flex items-center gap-1.5">
                        <i class="fas fa-times-circle"></i> Reset Filter
                    </a>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table id="presensiTable" class="w-full text-sm text-left">
                    <thead class="bg-zinc-50 border-b border-zinc-100 text-zinc-500 font-medium h-10">
                        <tr>
                            <th class="px-6 align-middle font-medium text-zinc-500 w-12 text-center">NO</th>
                            <th class="px-6 align-middle font-medium text-zinc-500">PRAKTIKAN</th>
                            <th class="px-6 align-middle font-medium text-zinc-500">PRAKTIKUM & SESI</th>
                            <th class="px-6 align-middle font-medium text-zinc-500">MODUL</th>
                            <th class="px-6 align-middle font-medium text-zinc-500">WAKTU MASUK</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-center">STATUS</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 text-zinc-900">
                        @foreach ($presensis as $index => $presensi)
                            <tr class="hover:bg-zinc-50/50 transition-colors">
                                <td class="px-6 py-4 text-center text-zinc-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-zinc-900">{{ $presensi->pendaftaran->praktikan->user->name }}</span>
                                        <span class="text-[10px] text-zinc-500 font-mono tracking-tighter uppercase">{{ $presensi->pendaftaran->praktikan->npm }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-zinc-700 text-xs">{{ $presensi->pendaftaran->praktikum->nama_praktikum }}</span>
                                        <span class="text-[10px] text-zinc-400 font-bold uppercase tracking-tighter">{{ $presensi->pendaftaran->sesi->nama_sesi ?? 'Umum' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-[11px] text-zinc-600 font-medium px-2 py-1 bg-zinc-50 border border-zinc-100 rounded-lg italic">
                                        {{ $presensi->jadwal->judul_modul }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-zinc-900 font-medium text-xs">{{ \Carbon\Carbon::parse($presensi->jam_masuk)->translatedFormat('d M Y') }}</span>
                                        <span class="text-[10px] text-zinc-500 font-bold uppercase">{{ \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i:s') }} WIB</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $statusClass = $presensi->status === 'hadir' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 
                                                       ($presensi->status === 'terlambat' ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-rose-50 text-rose-700 border-rose-100');
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black border uppercase tracking-wider {{ $statusClass }}">
                                        {{ $presensi->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form id="delete-form-{{ $presensi->id }}" action="{{ route('admin.presensi.destroy', $presensi->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete('{{ $presensi->id }}', '{{ $presensi->pendaftaran->praktikan->user->name }}')"
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-md text-zinc-400 hover:text-rose-600 hover:bg-rose-50 transition-colors">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($presensis->hasPages())
                <div class="px-6 py-4 border-t border-zinc-100 italic text-[10px] text-zinc-400">
                    {{ $presensis->links() }}
                </div>
            @endif
        </div>
    </div>

    <style>
        .dataTables_wrapper .dataTables_info {
            font-size: 11px;
            color: #a1a1aa;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border: 1px solid #e4e4e7 !important;
            border-radius: 8px !important;
            padding: 2px 10px !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            margin-left: 4px !important;
            background: white !important;
            color: #71717a !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #0f172a !important;
            border-color: #0f172a !important;
            color: white !important;
        }

        table.dataTable tbody td.dataTables_empty {
            padding: 0 !important;
            border: none !important;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            if ($('#presensiTable').length > 0) {
                var table = $('#presensiTable').DataTable({
                    dom: 't<"flex flex-col sm:flex-row items-center justify-between px-6 py-4 border-t border-zinc-100"ip>',
                    language: {
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        emptyTable: "<div class='py-20 flex flex-col items-center justify-center space-y-3'><div class='h-16 w-16 rounded-2xl bg-zinc-50 flex items-center justify-center'><i class='fas fa-clipboard-check text-2xl text-zinc-300'></i></div><div class='text-center'><p class='text-zinc-900 font-semibold'>Belum ada data presensi</p><p class='text-zinc-500 text-xs mt-1'>Data presensi akan muncul setelah praktikan melakukan scan QR.</p></div></div>",
                        paginate: {
                            next: '<i class="fas fa-chevron-right text-[10px]"></i>',
                            previous: '<i class="fas fa-chevron-left text-[10px]"></i>'
                        }
                    },
                    columnDefs: [{
                        orderable: false,
                        targets: [5]
                    }],
                    pageLength: 25
                });

                $('#customSearch').on('keyup', function() {
                    table.search(this.value).draw();
                });

                $('#customLength').on('change', function() {
                    table.page.len($(this).val()).draw();
                });
            }
        });

        function confirmDelete(id, name) {
            Swal.fire({
                title: 'Hapus data presensi?',
                text: "Kehadiran praktikan " + name + " akan dihapus dari record.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#f4f4f5',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    cancelButton: 'text-zinc-600 border border-zinc-200',
                    confirmButton: 'bg-rose-600'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }
    </script>
@endsection
