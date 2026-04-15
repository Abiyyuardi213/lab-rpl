@extends('layouts.admin')

@section('title', 'Digit NPM')

@section('content')
    <div class="space-y-6">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 uppercase">Digit NPM</h1>
                <p class="text-sm text-zinc-500 mt-1 italic">"Kelola opsi kode NPM untuk pembagian soal praktikum."</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Digit NPM</span>
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden">
            <div class="p-6 pb-4 flex items-center justify-between gap-4 border-b border-zinc-100">
                <div class="relative max-w-sm w-full">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-xs"></i>
                    <input type="text" id="customSearch" placeholder="Cari digit..."
                        class="flex h-9 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 pl-9 text-sm shadow-sm transition-colors placeholder:text-zinc-500 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
                </div>
                <button onclick="toggleModal('modal-add-digit')"
                    class="inline-flex h-9 items-center justify-center rounded-md bg-[#001f3f] px-4 py-2 text-sm font-medium text-white shadow hover:bg-[#002d5a] transition-colors whitespace-nowrap">
                    <i class="fas fa-plus mr-2 text-xs"></i>
                    Tambah Digit
                </button>
            </div>

            <div class="overflow-x-auto">
                <table id="digitTable" class="w-full text-sm text-left">
                    <thead class="bg-zinc-50 border-b border-zinc-100 text-zinc-500 font-medium h-10">
                        <tr>
                            <th class="px-6 align-middle font-medium text-zinc-500 w-12 text-center text-[10px] uppercase tracking-wider">No</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">Kode</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">Label</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">Urutan</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">Status</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-right text-[10px] uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 text-zinc-900">
                        @foreach ($digitNpms as $index => $digitNpm)
                            <tr class="hover:bg-zinc-50/50 transition-colors">
                                <td class="px-6 py-4 text-center text-zinc-500 font-medium">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-zinc-100 text-zinc-900 font-black text-xs border border-zinc-200">
                                        {{ $digitNpm->digit }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-semibold text-zinc-900">{{ $digitNpm->label }}</td>
                                <td class="px-6 py-4 text-zinc-500 font-medium">{{ $digitNpm->sort_order }}</td>
                                <td class="px-6 py-4">
                                    <button type="button" onclick="toggleStatus('{{ $digitNpm->id }}', this)"
                                        class="inline-flex items-center rounded-md border px-2 py-1 text-[10px] font-bold uppercase tracking-wider {{ $digitNpm->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-zinc-50 text-zinc-500 border-zinc-200' }}">
                                        {{ $digitNpm->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button onclick='openEditModal(@json($digitNpm))'
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-md text-zinc-500 hover:text-amber-600 hover:bg-amber-50 transition-colors">
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>
                                        <form id="delete-form-{{ $digitNpm->id }}"
                                            action="{{ route('admin.digit-npm.destroy', $digitNpm->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete('{{ $digitNpm->id }}', '{{ $digitNpm->label }}')"
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

    <div id="modal-add-digit" class="fixed inset-0 z-[60] hidden bg-zinc-900/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full max-w-md overflow-hidden shadow-2xl border border-zinc-200">
            <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between bg-zinc-50/50">
                <h3 class="font-bold text-zinc-900 uppercase tracking-tight">Tambah Kode NPM</h3>
                <button onclick="toggleModal('modal-add-digit')" class="h-8 w-8 flex items-center justify-center rounded-lg hover:bg-zinc-100 text-zinc-400 transition-colors">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <form action="{{ route('admin.digit-npm.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Kode</label>
                    <input type="text" name="digit" maxlength="20" required placeholder="Contoh: 4 atau A1"
                        class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Label</label>
                    <input type="text" name="label" required placeholder="Contoh: Calon Aslab"
                        class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Urutan</label>
                    <input type="number" name="sort_order" min="0" max="255" placeholder="Urutan tampilan, contoh: 10"
                        class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none">
                </div>
                <label class="flex items-center gap-2 text-xs font-bold text-zinc-600">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-zinc-300">
                    Aktifkan kode ini
                </label>
                <div class="pt-4 flex items-center justify-end gap-3">
                    <button type="button" onclick="toggleModal('modal-add-digit')"
                        class="inline-flex h-9 items-center justify-center rounded-md border border-zinc-200 bg-white px-4 text-xs font-bold text-zinc-600 transition-all hover:bg-zinc-50">
                        Batal
                    </button>
                    <button type="submit"
                        class="inline-flex h-9 items-center justify-center rounded-md bg-[#001f3f] px-6 text-xs font-bold text-white shadow-lg shadow-[#001f3f]/20 transition-all hover:bg-[#002d5a]">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="modal-edit-digit" class="fixed inset-0 z-[60] hidden bg-zinc-900/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full max-w-md overflow-hidden shadow-2xl border border-zinc-200">
            <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between bg-zinc-50/50">
                <h3 class="font-bold text-zinc-900 uppercase tracking-tight">Edit Kode NPM</h3>
                <button onclick="toggleModal('modal-edit-digit')" class="h-8 w-8 flex items-center justify-center rounded-lg hover:bg-zinc-100 text-zinc-400 transition-colors">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <form id="form-edit-digit" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Kode</label>
                    <input type="text" name="digit" id="edit-digit" maxlength="20" required
                        class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-amber-500/10 focus:border-amber-500 outline-none">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Label</label>
                    <input type="text" name="label" id="edit-label" required
                        class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-amber-500/10 focus:border-amber-500 outline-none">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Urutan</label>
                    <input type="number" name="sort_order" id="edit-sort-order" min="0" max="255"
                        class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-amber-500/10 focus:border-amber-500 outline-none">
                </div>
                <label class="flex items-center gap-2 text-xs font-bold text-zinc-600">
                    <input type="checkbox" name="is_active" id="edit-is-active" value="1" class="rounded border-zinc-300">
                    Aktifkan kode ini
                </label>
                <div class="pt-4 flex items-center justify-end gap-3">
                    <button type="button" onclick="toggleModal('modal-edit-digit')"
                        class="inline-flex h-9 items-center justify-center rounded-md border border-zinc-200 bg-white px-4 text-xs font-bold text-zinc-600 transition-all hover:bg-zinc-50">
                        Batal
                    </button>
                    <button type="submit"
                        class="inline-flex h-9 items-center justify-center rounded-md bg-amber-600 px-6 text-xs font-bold text-white shadow-lg shadow-amber-600/20 transition-all hover:bg-amber-700">
                        Simpan
                    </button>
                </div>
            </form>
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
    </style>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            const table = $('#digitTable').DataTable({
                dom: 't<"flex flex-col sm:flex-row items-center justify-between px-6 py-4 border-t border-zinc-100"ip>',
                language: {
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    emptyTable: "Belum ada digit NPM",
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
        });

        function toggleModal(id) {
            document.getElementById(id).classList.toggle('hidden');
        }

        function openEditModal(digitNpm) {
            document.getElementById('form-edit-digit').action = `/admin/digit-npm/${digitNpm.id}`;
            document.getElementById('edit-digit').value = digitNpm.digit;
            document.getElementById('edit-label').value = digitNpm.label;
            document.getElementById('edit-sort-order').value = digitNpm.sort_order;
            document.getElementById('edit-is-active').checked = digitNpm.is_active;
            toggleModal('modal-edit-digit');
        }

        function toggleStatus(id, button) {
            fetch(`/admin/digit-npm/${id}/toggle-status`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            }).then(response => response.json()).then(data => {
                if (!data.success) return;

                button.textContent = data.is_active ? 'Aktif' : 'Nonaktif';
                button.className = data.is_active
                    ? 'inline-flex items-center rounded-md border px-2 py-1 text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border-emerald-100'
                    : 'inline-flex items-center rounded-md border px-2 py-1 text-[10px] font-bold uppercase tracking-wider bg-zinc-50 text-zinc-500 border-zinc-200';
            });
        }

        function confirmDelete(id, label) {
            Swal.fire({
                title: 'Hapus digit?',
                text: label + ' akan dihapus dari master kode NPM.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#001f3f',
                cancelButtonColor: '#f4f4f5',
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }

        @if (session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        @if (session('error'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: '{{ session('error') }}',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        @endif
    </script>
@endsection
