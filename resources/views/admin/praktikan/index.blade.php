@extends('layouts.admin')

@section('title', 'Manajemen Praktikan')

@section('content')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Daftar Praktikan</h1>
                <p class="text-sm text-zinc-500 mt-1">Kelola data praktikan laboratorium di sini.</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Praktikan</span>
            </div>
        </div>

        <!-- Table Container -->
        <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden">
            <div class="p-6 pb-4 flex items-center justify-between gap-4 border-b border-zinc-100">
                <div class="flex items-center gap-2 flex-1">
                    <div class="relative max-w-sm w-full">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-xs"></i>
                        <input type="text" id="customSearch" placeholder="Cari praktikan..."
                            class="flex h-9 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 pl-9 text-sm shadow-sm transition-colors placeholder:text-zinc-500 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <select id="customLength"
                        class="h-9 rounded-md border border-zinc-200 bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
                        <option value="10">10 data</option>
                        <option value="25">25 data</option>
                        <option value="50">50 data</option>
                        <option value="100">100 data</option>
                    </select>
                    <a href="{{ route('admin.praktikan.create') }}" data-spa
                        class="inline-flex h-9 items-center justify-center rounded-md bg-[#001f3f] px-4 py-2 text-sm font-medium text-white shadow hover:bg-[#002d5a] transition-colors">
                        <i class="fas fa-plus mr-2 text-xs"></i>
                        Tambah Praktikan
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="praktikanTable" class="w-full text-sm text-left">
                    <thead class="bg-zinc-50 border-b border-zinc-100 text-zinc-500 font-medium h-10">
                        <tr>
                            <th class="px-6 align-middle font-medium text-zinc-500 w-12 text-center">NO</th>
                            <th class="px-6 align-middle font-medium text-zinc-500">NPM</th>
                            <th class="px-6 align-middle font-medium text-zinc-500">NAMA PRAKTIKAN</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-center">STATUS</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 text-zinc-900">
                        @foreach ($praktikans as $index => $praktikan)
                            <tr class="hover:bg-zinc-50/50 transition-colors">
                                <td class="px-6 py-4 text-center text-zinc-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 font-medium">{{ $praktikan->npm }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-9 w-9 rounded-full bg-zinc-100 border border-zinc-200 flex items-center justify-center overflow-hidden flex-shrink-0">
                                            @if ($praktikan->profile_picture)
                                                <img src="{{ asset('storage/' . $praktikan->profile_picture) }}"
                                                    class="h-full w-full object-cover">
                                            @else
                                                <span class="text-xs font-medium text-zinc-500 uppercase">
                                                    {{ substr($praktikan->name, 0, 2) }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-medium text-zinc-900">{{ $praktikan->name }}</span>
                                            <span class="text-xs text-zinc-500">{{ $praktikan->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button onclick="togglePraktikanStatus('{{ $praktikan->id }}', this)"
                                        class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-950 focus-visible:ring-offset-2 {{ $praktikan->status ? 'bg-emerald-500' : 'bg-zinc-200' }}">
                                        <span
                                            class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $praktikan->status ? 'translate-x-4' : 'translate-x-1' }}"></span>
                                    </button>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.praktikan.show', $praktikan->id) }}" data-spa
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-md text-zinc-500 hover:text-[#001f3f] hover:bg-zinc-100 transition-colors">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        <a href="{{ route('admin.praktikan.edit', $praktikan->id) }}" data-spa
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-md text-zinc-500 hover:text-[#001f3f] hover:bg-zinc-100 transition-colors">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        <form action="{{ route('admin.praktikan.impersonate', $praktikan->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" title="Login Sebagai Praktikan"
                                                class="inline-flex items-center justify-center h-8 w-8 rounded-md text-zinc-500 hover:text-emerald-600 hover:bg-emerald-50 transition-colors">
                                                <i class="fas fa-sign-in-alt text-xs"></i>
                                            </button>
                                        </form>
                                        <form id="delete-form-{{ $praktikan->id }}"
                                            action="{{ route('admin.praktikan.destroy', $praktikan->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                onclick="confirmDelete('{{ $praktikan->id }}', '{{ $praktikan->name }}')"
                                                class="inline-flex items-center justify-center h-8 w-8 rounded-md text-zinc-500 hover:text-rose-600 hover:bg-rose-50 transition-colors">
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

        table.dataTable tbody td.dataTables_empty {
            padding: 0 !important;
            border: none !important;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            if ($('#praktikanTable').length > 0) {
                var table = $('#praktikanTable').DataTable({
                    stateSave: true,
                    dom: 't<"flex flex-col sm:flex-row items-center justify-between px-6 py-4 border-t border-zinc-100"ip>',
                    language: {
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        emptyTable: "<div class='py-20 flex flex-col items-center justify-center space-y-3'><div class='h-16 w-16 rounded-2xl bg-zinc-50 flex items-center justify-center'><i class='fas fa-user-slash text-2xl text-zinc-300'></i></div><div class='text-center'><p class='text-zinc-900 font-semibold'>Tidak ada data praktikan tersedia</p><p class='text-zinc-500 text-xs mt-1'>Silakan tambah data praktikan baru untuk melihat daftar di sini.</p></div></div>",
                        paginate: {
                            next: '<i class="fas fa-chevron-right text-[10px]"></i>',
                            previous: '<i class="fas fa-chevron-left text-[10px]"></i>'
                        }
                    },
                    columnDefs: [{
                        orderable: false,
                        targets: [4]
                    }]
                });

                // Jika ada parameter last_page di URL, paksa ke halaman terakhir
                // Namun karena stateSave aktif, kita harus berhati-hati agar tidak konflik
                @if (request('last_page'))
                    table.page('last').draw(false);
                @endif

                $('#customSearch').on('keyup', function() {
                    table.search(this.value).draw();
                });

                $('#customLength').on('change', function() {
                    table.page.len($(this).val()).draw();
                });
            }
        });

        function togglePraktikanStatus(id, btn) {
            const span = btn.querySelector('span');
            const isActive = btn.classList.contains('bg-emerald-500');

            // Optimistic Update: Langsung ubah tampilan
            if (isActive) {
                btn.classList.remove('bg-emerald-500');
                btn.classList.add('bg-zinc-200');
                span.classList.remove('translate-x-4');
                span.classList.add('translate-x-1');
            } else {
                btn.classList.remove('bg-zinc-200');
                btn.classList.add('bg-emerald-500');
                span.classList.remove('translate-x-1');
                span.classList.add('translate-x-4');
            }

            fetch(`/admin/praktikan/${id}/toggle-status`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Gagal memperbarui status');
                return data;
            })
            .then(data => {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
                Toast.fire({
                    icon: 'success',
                    title: data.message
                });
            })
            .catch(error => {
                // Kembalikan ke status semula jika gagal
                if (isActive) {
                    btn.classList.remove('bg-zinc-200');
                    btn.classList.add('bg-emerald-500');
                    span.classList.remove('translate-x-1');
                    span.classList.add('translate-x-4');
                } else {
                    btn.classList.remove('bg-emerald-500');
                    btn.classList.add('bg-zinc-200');
                    span.classList.remove('translate-x-4');
                    span.classList.add('translate-x-1');
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: error.message || 'Terjadi kesalahan saat memperbarui status.'
                });
            });
        }

        function confirmDelete(id, name) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data praktikan " + name + " akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
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
                    fetch(`/admin/praktikan/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(async response => {
                        const data = await response.json();
                        if (!response.ok) {
                            throw new Error(data.message || 'Terjadi kesalahan saat menghapus data.');
                        }
                        return data;
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            // Remove row from DataTable without reload and stay on current page
                            const table = $('#praktikanTable').DataTable();
                            const row = $(`#delete-form-${id}`).closest('tr');
                            table.row(row).remove().draw(false);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: error.message || 'Terjadi kesalahan saat menghapus data.'
                        });
                    });
                }
            })
        }
    </script>
@endsection
