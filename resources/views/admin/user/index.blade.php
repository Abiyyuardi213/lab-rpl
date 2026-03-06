@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')

@section('content')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Daftar Pengguna</h1>
                <p class="text-sm text-zinc-500 mt-1">Kelola data admin, aslab, dan praktikan laboratorium di sini.</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Pengguna</span>
            </div>
        </div>



        <!-- Table Container -->
        <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden">
            <div class="p-6 pb-4 flex items-center justify-between gap-4 border-b border-zinc-100">
                <div class="flex items-center gap-2 flex-1">
                    <div class="relative max-w-sm w-full">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-xs"></i>
                        <input type="text" id="customSearch" placeholder="Cari pengguna..."
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
                    <a href="{{ route('admin.user.create') }}" data-spa
                        class="inline-flex h-9 items-center justify-center rounded-md bg-zinc-900 px-4 py-2 text-sm font-medium text-zinc-50 shadow hover:bg-zinc-900/90 transition-colors">
                        <i class="fas fa-plus mr-2 text-xs"></i>
                        Tambah Pengguna
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="userTable" class="w-full text-sm text-left">
                    <thead class="bg-zinc-50 border-b border-zinc-100 text-zinc-500 font-medium h-10">
                        <tr>
                            <th class="px-6 align-middle font-medium text-zinc-500 w-12 text-center">NO</th>
                            <th class="px-6 align-middle font-medium text-zinc-500">NAMA PENGGUNA</th>
                            <th class="px-6 align-middle font-medium text-zinc-500">EMAIL</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-center">PERAN</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 text-zinc-900">
                        @forelse($users as $index => $user)
                            <tr class="hover:bg-zinc-50/50 transition-colors">
                                <td class="px-6 py-4 text-center text-zinc-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-9 w-9 rounded-full bg-zinc-100 border border-zinc-200 flex items-center justify-center overflow-hidden flex-shrink-0">
                                            @if ($user->profile_picture)
                                                <img src="{{ asset('storage/' . $user->profile_picture) }}"
                                                    class="h-full w-full object-cover">
                                            @else
                                                <span class="text-xs font-medium text-zinc-500 uppercase">
                                                    {{ substr($user->name, 0, 2) }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-medium text-zinc-900">{{ $user->name }}</span>
                                            <span class="text-xs text-zinc-500">{{ $user->username }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-zinc-500">
                                    {{ $user->email }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex items-center rounded-full border border-zinc-200 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-zinc-900 bg-zinc-50">
                                        {{ $user->role->display_name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <button
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-md text-zinc-500 hover:text-zinc-900 hover:bg-zinc-100 transition-colors">
                                            <i class="fas fa-eye text-xs"></i>
                                        </button>
                                        <a href="{{ route('admin.user.edit', $user->id) }}" data-spa
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-md text-zinc-500 hover:text-zinc-900 hover:bg-zinc-100 transition-colors">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        <form id="delete-form-{{ $user->id }}"
                                            action="{{ route('admin.user.destroy', $user->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                onclick="confirmDelete('{{ $user->id }}', '{{ $user->name }}')"
                                                class="inline-flex items-center justify-center h-8 w-8 rounded-md text-zinc-500 hover:text-rose-600 hover:bg-rose-50 transition-colors">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-zinc-500 text-sm">
                                    Belum ada data pengguna.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        /* Refined DataTables Styling to match image perfectly */
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
            background: #18181b !important;
            border-color: #18181b !important;
            color: white !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.current) {
            background: #fafafa !important;
            border-color: #d4d4d8 !important;
            color: #18181b !important;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#userTable')) {
                $('#userTable').DataTable().destroy();
            }
            var table = $('#userTable').DataTable({
                dom: 't<"flex flex-col sm:flex-row items-center justify-between px-6 py-4 border-t border-zinc-100"ip>',
                language: {
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    emptyTable: "<div class='py-20 flex flex-col items-center justify-center space-y-3'><div class='h-16 w-16 rounded-2xl bg-zinc-50 flex items-center justify-center'><i class='fas fa-user-slash text-2xl text-zinc-300'></i></div><div class='text-center'><p class='text-zinc-900 font-semibold'>Tidak ada data pengguna tersedia</p><p class='text-zinc-500 text-xs mt-1'>Silakan tambah data pengguna baru untuk melihat daftar di sini.</p></div></div>",
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

            $('#customSearch').on('keyup', function() {
                table.search(this.value).draw();
            });

            $('#customLength').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            @if (request('last_page'))
                table.page('last').draw(false);
            @endif
        });

        function confirmDelete(id, name) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data pengguna " + name + " akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#001f3f',
                cancelButtonColor: '#f4f4f5',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    cancelButton: 'text-zinc-600 border border-zinc-200',
                    confirmButton: 'bg-[#001f3f]'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }
    </script>
@endsection
