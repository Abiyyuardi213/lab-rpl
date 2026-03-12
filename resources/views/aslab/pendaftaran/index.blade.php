@extends('layouts.admin')

@section('title', 'Pendaftaran Mahasiswa')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 uppercase">Pendaftaran Mahasiswa</h1>
                <p class="text-sm text-zinc-500 mt-1 italic">"Kelola dan ambil mahasiswa bimbingan Anda di sini."</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('aslab.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Pendaftaran</span>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($myPraktikums as $item)
                @php
                    $usedCount = Auth::user()->assignedStudents()->where('praktikum_id', $item->id)->count();
                    $quotaValue = $item->pivot->kuota;
                    $percent = $quotaValue > 0 ? ($usedCount / $quotaValue) * 100 : 0;
                @endphp
                <div class="bg-white p-5 rounded-xl border border-zinc-200 shadow-sm transition-all group overflow-hidden relative">
                    <div class="absolute top-0 right-0 p-4 opacity-[0.03] group-hover:opacity-[0.08] transition-opacity pointer-events-none">
                        <i class="fas fa-microscope text-6xl text-zinc-900"></i>
                    </div>
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-10 h-10 rounded-lg bg-zinc-50 flex items-center justify-center text-[#001f3f] border border-zinc-100 group-hover:bg-[#001f3f] group-hover:text-white transition-all duration-300">
                            <i class="fas fa-microscope text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest leading-none">{{ $item->kode_praktikum }}</p>
                            <h4 class="text-sm font-black text-zinc-800 truncate uppercase mt-1 tracking-tight">{{ $item->nama_praktikum }}</h4>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-end justify-between">
                            <div class="flex items-baseline gap-1">
                                <span class="text-2xl font-black text-zinc-900 tabular-nums tracking-tighter">{{ $usedCount }}</span>
                                <span class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest">/ {{ $quotaValue }} KUOTA</span>
                            </div>
                            <span class="text-[10px] font-bold {{ $percent >= 100 ? 'text-rose-600' : 'text-emerald-600' }} uppercase tracking-widest">
                                {{ round($percent) }}% TERISI
                            </span>
                        </div>
                        <div class="h-1.5 w-full bg-zinc-100 rounded-full overflow-hidden border border-zinc-200/50">
                            <div class="h-full {{ $percent >= 100 ? 'bg-rose-500' : 'bg-[#001f3f]' }} rounded-full transition-all duration-700 shadow-sm"
                                style="width: {{ min($percent, 100) }}%"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Student List Container -->
        <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden min-h-[500px]">
            <div class="p-6 pb-4 flex items-center justify-between gap-4 border-b border-zinc-100">
                <div class="flex items-center gap-2 flex-1">
                    <div class="relative max-w-sm w-full">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-xs"></i>
                        <input type="text" id="customSearch" placeholder="Cari data mahasiswa..."
                            class="flex h-9 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 pl-9 text-sm shadow-sm transition-colors placeholder:text-zinc-500 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <select id="customLength"
                        class="h-9 rounded-md border border-zinc-200 bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
                        <option value="10">10 data</option>
                        <option value="25">25 data</option>
                        <option value="50">50 data</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table id="pendaftaranTable" class="w-full text-sm text-left">
                    <thead class="bg-zinc-50 border-b border-zinc-100 text-zinc-500 font-medium h-10">
                        <tr>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider w-12 text-center text-[10px] uppercase tracking-wider">NO</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">Mahasiswa</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">Praktikum</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">Sesi</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">Aslab Bimbingan</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-right text-[10px] uppercase tracking-wider">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 text-zinc-900">
                        @foreach ($students as $index => $student)
                            <tr class="hover:bg-zinc-50/50 transition-colors">
                                <td class="px-6 py-4 text-center text-zinc-500 font-medium whitespace-nowrap">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-zinc-900 uppercase tracking-tight">{{ $student->praktikan->user->name }}</span>
                                        <span class="text-[10px] text-zinc-500 font-bold font-mono tracking-widest">{{ $student->praktikan->npm }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-zinc-700 uppercase tracking-tight">{{ $student->praktikum->nama_praktikum }}</span>
                                        <span class="text-[10px] text-zinc-400 font-black uppercase tracking-widest leading-none mt-0.5">{{ $student->praktikum->kode_praktikum }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-bold uppercase tracking-widest bg-zinc-100 text-zinc-600 border border-zinc-200">
                                        {{ $student->sesi->nama_sesi }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($student->aslab)
                                        <div class="flex items-center gap-2">
                                            @if($student->aslab_id === Auth::user()->aslab->id)
                                                <div class="w-6 h-6 rounded-md bg-[#001f3f] flex items-center justify-center text-[10px] text-white shadow-sm border border-[#001f3f]/10">
                                                    <i class="fas fa-user-check"></i>
                                                </div>
                                                <span class="text-[10px] font-black text-[#001f3f] uppercase tracking-widest">MILIK ANDA</span>
                                            @else
                                                <div class="w-6 h-6 rounded-md bg-zinc-100 flex items-center justify-center text-[10px] font-black text-zinc-400 border border-zinc-200 shadow-sm overflow-hidden">
                                                    {{ substr($student->aslab->user->name, 0, 1) }}
                                                </div>
                                                <span class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest truncate max-w-[120px]">
                                                    {{ $student->aslab->user->name }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-[10px] text-rose-500 font-black uppercase tracking-widest flex items-center gap-1.5 italic">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                            BELUM ADA ASLAB
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    @if (!$student->aslab_id)
                                        <form action="{{ route('aslab.pendaftaran.assign', $student->id) }}" method="POST" id="assign-form-{{ $student->id }}">
                                            @csrf @method('PATCH')
                                            <button type="button" onclick="confirmAssign('{{ $student->id }}', '{{ addslashes($student->praktikan->user->name) }}')"
                                                class="inline-flex h-8 items-center justify-center rounded-md bg-[#001f3f] px-3 py-1.5 text-[10px] font-bold text-white shadow-lg shadow-[#001f3f]/10 hover:bg-[#002d5a] transition-all uppercase tracking-widest">
                                                KLAIM
                                            </button>
                                        </form>
                                    @elseif($student->aslab_id === Auth::user()->aslab->id)
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-black text-emerald-600 uppercase tracking-widest">
                                            <i class="fas fa-check-double"></i>
                                            VERIFIED
                                        </span>
                                    @else
                                        <span class="text-[10px] text-zinc-300 font-bold uppercase tracking-widest italic">DIAMBIL</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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
            padding: 6px 14px !important;
            font-size: 12px !important;
            font-weight: 700 !important;
            margin-left: 6px !important;
            background: white !important;
            color: #71717a !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #001f3f !important;
            border-color: #001f3f !important;
            color: white !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.current) {
            background: #fafafa !important;
            border-color: #d4d4d8 !important;
            color: #001f3f !important;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            if ($('#pendaftaranTable').length > 0) {
                var table = $('#pendaftaranTable').DataTable({
                    dom: 't<"flex flex-col sm:flex-row items-center justify-between px-6 py-4 border-t border-zinc-100"ip>',
                    language: {
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        emptyTable: "<div class='py-20 flex flex-col items-center justify-center space-y-3'><div class='h-16 w-16 rounded-2xl bg-zinc-50 flex items-center justify-center'><i class='fas fa-user-friends text-2xl text-zinc-300'></i></div><div class='text-center'><p class='text-zinc-900 font-semibold'>Tidak ada data pendaftaran</p><p class='text-zinc-500 text-xs mt-1'>Belum ada mahasiswa pendaftar yang terverifikasi di praktikum Anda.</p></div></div>",
                        paginate: {
                            next: '<i class="fas fa-chevron-right text-[10px]"></i>',
                            previous: '<i class="fas fa-chevron-left text-[10px]"></i>'
                        }
                    },
                    columnDefs: [{
                        orderable: false,
                        targets: [5]
                    }]
                });

                $('#customSearch').on('keyup', function() {
                    table.search(this.value).draw();
                });

                $('#customLength').on('change', function() {
                    table.page.len($(this).val()).draw();
                });
            }
        });

        function confirmAssign(id, name) {
            Swal.fire({
                title: 'Klaim Mahasiswa?',
                html: `Apakah Anda ingin mengambil <b class="text-[#001f3f]">${name}</b> sebagai mahasiswa bimbingan Anda?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#001f3f',
                cancelButtonColor: '#f4f4f5',
                confirmButtonText: 'Ya, Klaim!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    cancelButton: 'text-zinc-600 border border-zinc-200',
                    confirmButton: 'bg-[#001f3f]'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('assign-form-' + id).submit();
                }
            });
        }

        @if (session('success'))
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });

            Toast.fire({
                icon: 'success',
                title: '{{ session('success') }}'
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#001f3f',
                customClass: {
                    confirmButton: 'bg-[#001f3f]'
                }
            });
        @endif
    </script>
@endsection
