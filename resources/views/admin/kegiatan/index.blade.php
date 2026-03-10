@extends('layouts.admin')

@section('title', 'Manajemen Kegiatan')

@section('content')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Laporan Kegiatan Lab</h1>
                <p class="text-sm text-zinc-500 mt-1">Kelola artikel report kegiatan yang telah dilaksanakan oleh Lab RPL.
                </p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Kegiatan</span>
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
                        <input type="text" id="customSearch" placeholder="Cari kegiatan..."
                            class="flex h-9 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 pl-9 text-sm shadow-sm transition-colors placeholder:text-zinc-500 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.kegiatan.create') }}"
                        class="inline-flex h-9 items-center justify-center rounded-md bg-[#1a4fa0] px-4 py-2 text-sm font-medium text-white shadow hover:bg-[#1a4fa0]/90 transition-colors">
                        <i class="fas fa-plus mr-2 text-xs"></i>
                        Tambah Kegiatan
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="kegiatanTable" class="w-full text-sm text-left">
                    <thead class="bg-zinc-50 border-b border-zinc-100 text-zinc-500 font-medium h-10">
                        <tr>
                            <th class="px-6 align-middle font-medium text-zinc-500 w-12 text-center">NO</th>
                            <th class="px-6 align-middle font-medium text-zinc-500">JUDUL</th>
                            <th class="px-6 align-middle font-medium text-zinc-500">TANGGAL KEGIATAN</th>
                            <th class="px-6 align-middle font-medium text-zinc-500">LOKASI</th>
                            <th class="px-6 align-middle font-medium text-zinc-500">PENULIS</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-center">STATUS</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 text-zinc-900">
                        @foreach ($kegiatans as $index => $k)
                            <tr class="hover:bg-zinc-50/50 transition-colors">
                                <td class="px-6 py-4 text-center text-zinc-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if ($k->gambar)
                                            <img src="{{ asset('storage/' . $k->gambar) }}"
                                                class="w-10 h-10 rounded-lg object-cover">
                                        @else
                                            <div class="w-10 h-10 rounded-lg bg-zinc-100 flex items-center justify-center">
                                                <i class="fas fa-calendar-alt text-zinc-400 text-xs"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <span
                                                class="font-semibold text-zinc-900 block leading-tight">{{ $k->judul }}</span>
                                            <span
                                                class="text-[10px] text-zinc-400 uppercase tracking-tight">{{ $k->slug }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-zinc-600 text-xs font-medium">
                                    {{ $k->tanggal_kegiatan->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-zinc-500 text-xs">
                                    {{ $k->lokasi ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-zinc-600 text-xs font-medium">{{ $k->user->name }}</td>
                                <td class="px-6 py-4 text-center">
                                    <button onclick="toggleStatus('{{ $k->id }}')" @class([
                                        'relative inline-flex h-5 w-9 shrink-0 cursor-pointer items-center rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-[#1a4fa0] focus:ring-offset-2',
                                        'bg-[#1a4fa0]' => $k->is_active,
                                        'bg-zinc-200' => !$k->is_active,
                                    ])>
                                        <span @class([
                                            'pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                            'translate-x-4' => $k->is_active,
                                            'translate-x-0' => !$k->is_active,
                                        ])></span>
                                    </button>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.kegiatan.edit', $k->id) }}"
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-md text-zinc-400 hover:text-[#1a4fa0] hover:bg-zinc-100 transition-colors">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        <form id="delete-form-{{ $k->id }}"
                                            action="{{ route('admin.kegiatan.destroy', $k->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                onclick="confirmDelete('{{ $k->id }}', '{{ $k->judul }}')"
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
                var table = $('#kegiatanTable').DataTable({
                    dom: 't<"flex flex-col sm:flex-row items-center justify-between px-6 py-4 border-t border-zinc-100"ip>',
                    language: {
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                        infoFiltered: "(disaring dari _MAX_ total data)",
                        lengthMenu: "Tampilkan _MENU_ data",
                        loadingRecords: "Memuat...",
                        processing: "Sedang memproses...",
                        search: "Cari:",
                        zeroRecords: "Kegiatan tidak ditemukan",
                        emptyTable: `
                            <div class="flex flex-col items-center justify-center py-20 text-zinc-400">
                                <div class="h-20 w-20 rounded-full bg-zinc-50 flex items-center justify-center mb-4 border border-zinc-100 shadow-inner">
                                    <i class="fas fa-calendar-alt text-3xl opacity-20"></i>
                                </div>
                                <h3 class="text-sm font-black uppercase tracking-[0.2em] text-zinc-400">Data Kosong</h3>
                                <p class="text-[10px] italic mt-1 font-medium tracking-tight">Belum ada laporan kegiatan yang dipublikasikan saat ini.</p>
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

            function toggleStatus(id) {
                fetch(`{{ url('admin/kegiatan') }}/${id}/toggle-status`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    }).then(response => response.json())
                    .then(data => {
                        if (data.success) location.reload();
                    });
            }

            function confirmDelete(id, name) {
                Swal.fire({
                    title: 'Hapus kegiatan?',
                    text: name + " akan dihapus selamanya.",
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
