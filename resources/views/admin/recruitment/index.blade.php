@extends('layouts.admin')

@section('title', 'Manajemen Rekrutmen')

@section('content')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Manajemen Rekrutmen Aslab</h1>
                <p class="text-sm text-zinc-500 mt-1">Kelola periode pembukaan asisten laboratorium baru.</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Rekrutmen</span>
            </div>
        </div>

        @if (session('success'))
            <div
                class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm flex items-center gap-3">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- Table Container -->
        <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden">
            <div class="p-6 pb-4 flex items-center justify-between gap-4 border-b border-zinc-100">
                <div class="flex items-center gap-2 flex-1">
                    <div class="relative max-w-sm w-full">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-xs"></i>
                        <input type="text" id="customSearch" placeholder="Cari periode..."
                            class="flex h-9 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 pl-9 text-sm shadow-sm transition-colors placeholder:text-zinc-500 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.recruitment.create') }}"
                        class="inline-flex h-9 items-center justify-center rounded-md bg-[#1a4fa0] px-4 py-2 text-sm font-medium text-white shadow hover:bg-[#1a4fa0]/90 transition-colors">
                        <i class="fas fa-plus mr-2 text-xs"></i>
                        Tambah Periode
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="recruitmentTable" class="w-full text-sm text-left">
                    <thead class="bg-zinc-50 border-b border-zinc-100 text-zinc-500 font-medium h-10">
                        <tr>
                            <th class="px-6 align-middle font-medium text-zinc-500 w-12 text-center">NO</th>
                            <th class="px-6 align-middle font-medium text-zinc-500">JUDUL PERIODE</th>
                            <th class="px-6 align-middle font-medium text-zinc-500">MASA PENDAFTARAN</th>
                            <th class="px-6 align-middle font-medium text-zinc-500">MIN. PERSYARATAN</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-center">PENDAFTAR</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-center">STATUS</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 text-zinc-900">
                        @foreach ($periods as $index => $period)
                            <tr class="hover:bg-zinc-50/50 transition-colors">
                                <td class="px-6 py-4 text-center text-zinc-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-[#1a4fa0]">
                                            <i class="fas fa-users-gear text-xs"></i>
                                        </div>
                                        <div>
                                            <span class="font-semibold text-zinc-900 block leading-tight">{{ $period->title }}</span>
                                            <span class="text-[10px] text-zinc-400 uppercase tracking-tight">UUID: {{ substr($period->id, 0, 8) }}...</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-zinc-600 text-xs font-medium">
                                    <div class="flex flex-col">
                                        <span>{{ $period->start_date->format('d M Y') }}</span>
                                        <span class="text-zinc-400 text-[10px]">s/d {{ $period->end_date->format('d M Y') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-zinc-500 text-xs">
                                    <div class="flex flex-col gap-1">
                                        <span class="flex items-center gap-1.5"><i class="fas fa-graduation-cap w-3"></i> IPK: {{ $period->min_ipk }}</span>
                                        <span class="flex items-center gap-1.5"><i class="fas fa-layer-group w-3"></i> Sem: {{ $period->min_semester }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 text-zinc-700">
                                        {{ $period->applications_count }} Orang
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center">
                                        @if($period->is_active)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">
                                                <span class="w-1 h-1 rounded-full bg-emerald-500"></span> Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 text-zinc-500">
                                                <span class="w-1 h-1 rounded-full bg-zinc-400"></span> Non-Aktif
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.recruitment.show', $period->id) }}"
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-md text-zinc-400 hover:text-[#1a4fa0] hover:bg-zinc-100 transition-colors" title="Lihat Pelamar">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        <form id="delete-form-{{ $period->id }}"
                                            action="{{ route('admin.recruitment.destroy', $period->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                onclick="confirmDelete('{{ $period->id }}', '{{ $period->title }}')"
                                                class="inline-flex items-center justify-center h-8 w-8 rounded-md text-zinc-400 hover:text-rose-600 hover:bg-rose-50 transition-colors">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .dataTables_empty {
                padding: 0 !important;
                background-color: transparent !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        <script>
            $(document).ready(function() {
                var table = $('#recruitmentTable').DataTable({
                    dom: 't<"flex flex-col sm:flex-row items-center justify-between px-6 py-4 border-t border-zinc-100"ip>',
                    language: {
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                        infoFiltered: "(disaring dari _MAX_ total data)",
                        lengthMenu: "Tampilkan _MENU_ data",
                        loadingRecords: "Memuat...",
                        processing: "Sedang memproses...",
                        search: "Cari:",
                        zeroRecords: "Periode tidak ditemukan",
                        emptyTable: `
                            <div class="flex flex-col items-center justify-center py-20 text-zinc-400">
                                <div class="h-20 w-20 rounded-full bg-zinc-50 flex items-center justify-center mb-4 border border-zinc-100 shadow-inner">
                                    <i class="fas fa-users-gear text-3xl opacity-20"></i>
                                </div>
                                <h3 class="text-sm font-black uppercase tracking-[0.2em] text-zinc-400">Data Kosong</h3>
                                <p class="text-[10px] italic mt-1 font-medium tracking-tight">Belum ada periode rekrutmen yang dibuat saat ini.</p>
                            </div>
                        `,
                        paginate: {
                            next: '<i class="fas fa-chevron-right text-[10px]"></i>',
                            previous: '<i class="fas fa-chevron-left text-[10px]"></i>'
                        }
                    }
                });

                $('#customSearch').on('keyup', function() {
                    table.search(this.value).draw();
                });
            });

            function confirmDelete(id, name) {
                Swal.fire({
                    title: 'Hapus periode rekrutmen?',
                    text: name + " akan dihapus selamanya beserta seluruh data pendaftar didalamnya.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e11d48',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + id).submit();
                    }
                });
            }
        </script>
    @endpush
@endsection

