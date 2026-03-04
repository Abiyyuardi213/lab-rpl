@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')

@section('content')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Daftar Pengguna</h1>
                <p class="mt-1 text-sm text-zinc-500">Kelola data admin, aslab, dan praktikan laboratorium di sini.</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-400">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-600 transition-colors">Home</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Pengguna</span>
            </div>
        </div>

        <!-- Action Button -->
        <div class="flex justify-end">
            <a href="{{ route('admin.user.create') }}" data-spa
                class="inline-flex items-center gap-2 bg-[#09090b] px-4 py-2 rounded-lg text-xs font-bold text-white hover:bg-zinc-800 transition-all active:scale-95 shadow-sm">
                <i class="fas fa-plus text-[10px]"></i>
                <span>Tambah Pengguna</span>
            </a>
        </div>

        @if (session('success'))
            <div
                class="rounded-xl border border-emerald-100 bg-emerald-50/50 p-4 text-emerald-800 flex items-center gap-3 animate-in fade-in slide-in-from-top-2 duration-300">
                <i class="fas fa-circle-check text-emerald-500"></i>
                <p class="text-sm font-bold">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Table Container -->
        <div class="rounded-xl border border-zinc-200 bg-white shadow-sm overflow-hidden mt-4">
            <div class="overflow-x-auto">
                <table id="userTable" class="w-full text-sm text-left">
                    <thead class="text-xs text-zinc-500 uppercase bg-zinc-50 border-b border-zinc-100">
                        <tr>
                            <th class="px-6 py-3 font-medium text-center w-12">NO</th>
                            <th class="px-6 py-3 font-medium">NAMA PENGGUNA</th>
                            <th class="px-6 py-3 font-medium">EMAIL</th>
                            <th class="px-6 py-3 font-medium text-center">PERAN</th>
                            <th class="px-6 py-3 font-medium text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse($users as $index => $user)
                            <tr class="hover:bg-zinc-50/50 transition-colors">
                                <td class="px-6 py-4 text-center">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-10 h-10 rounded-full border border-zinc-100 overflow-hidden flex-shrink-0 bg-zinc-50 flex items-center justify-center">
                                            @if ($user->profile_picture)
                                                <img src="{{ asset('storage/' . $user->profile_picture) }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <span class="text-[10px] font-bold text-zinc-400 uppercase">
                                                    {{ substr($user->name, 0, 2) }}
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-bold text-zinc-800 text-sm tracking-tight leading-none mb-1">
                                                {{ $user->name }}
                                            </p>
                                            <p class="text-[11px] font-medium text-zinc-400">
                                                {{ $user->username }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-zinc-500 font-medium">
                                    {{ $user->email }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex items-center justify-center px-2.5 py-1 text-xs font-semibold rounded-full bg-indigo-50 text-indigo-600 border border-indigo-200">
                                        {{ $user->role->role_name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- View Button --}}
                                        <button type="button"
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-md text-zinc-400 hover:text-zinc-600 hover:bg-zinc-100 transition-colors">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        {{-- Edit Button --}}
                                        <a href="{{ route('admin.user.edit', $user->id) }}" data-spa
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-md text-zinc-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        {{-- Delete Button --}}
                                        <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST"
                                            onsubmit="return confirm('Hapus pengguna ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center justify-center h-8 w-8 rounded-md text-zinc-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                                <i class="fas fa-trash"></i>
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

    <script>
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#userTable')) {
                $('#userTable').DataTable().destroy();
            }
            var table = $('#userTable').DataTable({
                dom: 't<"flex flex-col sm:flex-row items-center justify-between px-6 py-4 border-t border-zinc-100"ip>',
                language: {
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
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
        });
    </script>
@endsection
