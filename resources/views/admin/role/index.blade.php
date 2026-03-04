@extends('layouts.admin')

@section('title', 'Manajemen Peran')

@section('content')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 tracking-tight">Manajemen Peran</h1>
                <p class="text-sm text-zinc-500 mt-1">Kelola data peran (role) pengguna dan hak akses sistem.</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-400">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-600 transition-colors">Home</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Peran</span>
            </div>
        </div>

        <!-- Action Button -->
        <div class="flex justify-end">
            <a href="{{ route('admin.role.create') }}" data-spa
                class="inline-flex items-center gap-2 bg-[#09090b] px-4 py-2 rounded-lg text-xs font-bold text-white hover:bg-zinc-800 transition-all active:scale-95 shadow-sm">
                <i class="fas fa-plus text-[10px]"></i>
                <span>Tambah Peran</span>
            </a>
        </div>

        <!-- Table Container -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden mt-4">
            <div class="p-8">
                <table id="roleTable" class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-zinc-50">
                            <th
                                class="px-4 py-4 text-[11px] font-black text-zinc-400 uppercase tracking-widest w-12 text-center">
                                NO</th>
                            <th class="px-4 py-4 text-[11px] font-black text-zinc-400 uppercase tracking-widest w-32">ID
                            </th>
                            <th class="px-4 py-4 text-[11px] font-black text-zinc-400 uppercase tracking-widest">NAMA PERAN
                            </th>
                            <th class="px-4 py-4 text-[11px] font-black text-zinc-400 uppercase tracking-widest">DESKRIPSI
                            </th>
                            <th
                                class="px-4 py-4 text-[11px] font-black text-zinc-400 uppercase tracking-widest text-center">
                                STATUS</th>
                            <th class="px-4 py-4 text-[11px] font-black text-zinc-400 uppercase tracking-widest text-right">
                                AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-50">
                        @forelse($roles as $index => $role)
                            <tr class="hover:bg-zinc-50/50 transition-colors group">
                                <td class="px-4 py-6 text-sm font-bold text-zinc-900 text-center">{{ $index + 1 }}</td>
                                <td class="px-4 py-6">
                                    <div class="w-28 text-[11px] font-medium text-zinc-400 leading-relaxed break-all">
                                        {{ $role->id }}
                                    </div>
                                </td>
                                <td class="px-4 py-6">
                                    <span
                                        class="text-sm font-black text-zinc-800 lowercase tracking-tight">{{ $role->role_name }}</span>
                                </td>
                                <td class="px-4 py-6 text-sm text-zinc-400 font-medium">
                                    {{ $role->role_description ?? '-' }}
                                </td>
                                <td class="px-4 py-6">
                                    <div class="flex justify-center">
                                        <button type="button" onclick="toggleRoleStatus('{{ $role->id }}')"
                                            id="btn-status-{{ $role->id }}"
                                            class="relative w-11 h-6 rounded-full transition-colors duration-200 focus:outline-none {{ $role->role_status ? 'bg-[#18181b]' : 'bg-zinc-200' }}">
                                            <div
                                                class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition-transform duration-200 shadow-sm {{ $role->role_status ? 'translate-x-5' : 'translate-x-0' }}">
                                            </div>
                                        </button>
                                    </div>
                                </td>
                                <td class="px-4 py-6">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.role.edit', $role->id) }}" data-spa
                                            class="w-9 h-9 flex items-center justify-center rounded-lg bg-white border border-zinc-200 text-zinc-400 hover:text-zinc-600 hover:border-zinc-300 transition-all shadow-sm">
                                            <i class="fas fa-pencil text-[10px]"></i>
                                        </a>
                                        <form action="{{ route('admin.role.destroy', $role->id) }}" method="POST"
                                            onsubmit="return confirm('Hapus peran ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="w-9 h-9 flex items-center justify-center rounded-lg bg-white border border-zinc-200 text-rose-500 hover:bg-rose-50 transition-all shadow-sm">
                                                <i class="fas fa-trash text-[10px]"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6"
                                    class="px-6 py-12 text-center text-zinc-400 text-sm font-bold uppercase tracking-widest">
                                    Belum ada data peran.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        /* DataTables Custom Styling precisely matching image */
        .dataTables_wrapper .dataTables_length {
            margin-bottom: 24px;
            font-size: 14px;
            color: #18181b;
            font-weight: 500;
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #e4e4e7 !important;
            border-radius: 8px !important;
            padding: 4px 24px 4px 12px !important;
            margin: 0 8px !important;
            outline: none !important;
            background-color: transparent !important;
            font-weight: 600 !important;
        }

        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 24px;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #e4e4e7 !important;
            border-radius: 8px !important;
            padding: 8px 16px !important;
            width: 280px !important;
            font-size: 13px !important;
            outline: none !important;
            color: #71717a !important;
        }

        .dataTables_wrapper .dataTables_filter input::placeholder {
            color: #a1a1aa !important;
        }

        .dataTables_wrapper .dataTables_info {
            padding-top: 24px;
            font-size: 12px;
            color: #a1a1aa;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .dataTables_wrapper .dataTables_paginate {
            padding-top: 24px;
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
            transition: all 0.2s ease !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.current) {
            background: #fafafa !important;
            color: #18181b !important;
            border-color: #d4d4d8 !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #18181b !important;
            color: white !important;
            border-color: #18181b !important;
        }
    </style>

    <script>
        function toggleRoleStatus(id) {
            const btn = document.getElementById(`btn-status-${id}`);
            const dot = btn.querySelector('div');

            fetch(`/admin/role/${id}/toggle-status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (btn.classList.contains('bg-[#18181b]')) {
                            btn.classList.remove('bg-[#18181b]');
                            btn.classList.add('bg-zinc-200');
                            dot.classList.remove('translate-x-5');
                            dot.classList.add('translate-x-0');
                        } else {
                            btn.classList.remove('bg-zinc-200');
                            btn.classList.add('bg-[#18181b]');
                            dot.classList.remove('translate-x-0');
                            dot.classList.add('translate-x-5');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal memperbarui status');
                });
        }

        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#roleTable')) {
                $('#roleTable').DataTable().destroy();
            }
            $('#roleTable').DataTable({
                language: {
                    search: "",
                    searchPlaceholder: "Cari peran...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    paginate: {
                        next: '<i class="fas fa-chevron-right text-[10px]"></i>',
                        previous: '<i class="fas fa-chevron-left text-[10px]"></i>'
                    }
                },
                columnDefs: [{
                    orderable: false,
                    targets: [4, 5]
                }]
            });
        });
    </script>
@endsection
