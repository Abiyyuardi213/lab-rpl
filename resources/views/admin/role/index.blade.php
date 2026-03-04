@extends('layouts.admin')

@section('title', 'Manajemen Role')

@section('content')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Manajemen Peran</h1>
                <p class="text-sm text-zinc-500 mt-1">Kelola peran dan hak akses pengguna dalam sistem.</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Role</span>
            </div>
        </div>

        <!-- Table Container -->
        <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden">
            <div class="p-6 pb-4 flex items-center justify-between gap-4 border-b border-zinc-100">
                <div class="flex items-center gap-2 flex-1">
                    <div class="relative max-w-sm w-full">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-xs"></i>
                        <input type="text" id="customSearch" placeholder="Cari role..."
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
                    <button onclick="toggleModal('modal-add-role')"
                        class="inline-flex h-9 items-center justify-center rounded-md bg-[#001f3f] px-4 py-2 text-sm font-medium text-white shadow hover:bg-[#002d5a] transition-colors">
                        <i class="fas fa-plus mr-2 text-xs"></i>
                        Tambah Role
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="roleTable" class="w-full text-sm text-left">
                    <thead class="bg-zinc-50 border-b border-zinc-100 text-zinc-500 font-medium h-10">
                        <tr>
                            <th class="px-6 align-middle font-medium text-zinc-500 w-12 text-center">ID</th>
                            <th class="px-6 align-middle font-medium text-zinc-500">NAMA ROLE</th>
                            <th class="px-6 align-middle font-medium text-zinc-500">DISPLAY NAME</th>
                            <th class="px-6 align-middle font-medium text-zinc-500">DESKRIPSI</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 text-zinc-900">
                        @foreach ($roles as $role)
                            <tr class="hover:bg-zinc-50/50 transition-colors">
                                <td class="px-6 py-4 text-center text-zinc-500">#{{ $role->id }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="bg-zinc-100 text-zinc-700 px-2 py-0.5 rounded text-[11px] font-bold font-mono border border-zinc-200">
                                        {{ $role->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-semibold text-zinc-900">{{ $role->display_name }}</td>
                                <td class="px-6 py-4 text-zinc-500 max-w-xs truncate">{{ $role->description ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <button onclick="editRole({{ json_encode($role) }})"
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-md text-zinc-500 hover:text-[#001f3f] hover:bg-zinc-100 transition-colors">
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>
                                        <form id="delete-form-{{ $role->id }}"
                                            action="{{ route('admin.role.destroy', $role->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                onclick="confirmDelete('{{ $role->id }}', '{{ $role->display_name }}')"
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

    <!-- Modal Add Role -->
    <div id="modal-add-role" class="fixed inset-0 z-[60] hidden">
        <div class="absolute inset-0 bg-zinc-900/40 backdrop-blur-sm" onclick="toggleModal('modal-add-role')"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg p-6">
            <div
                class="bg-white rounded-2xl shadow-2xl border border-zinc-100 overflow-hidden animate-in zoom-in duration-300">
                <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between bg-zinc-50/50">
                    <h3 class="font-bold text-zinc-900">Tambah Role Baru</h3>
                    <button onclick="toggleModal('modal-add-role')"
                        class="text-zinc-400 hover:text-zinc-600 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.role.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-zinc-700 uppercase tracking-tight">Nama Role (Unique)</label>
                        <input type="text" name="name" required placeholder="e.g., administrator"
                            class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-zinc-700 uppercase tracking-tight">Display Name</label>
                        <input type="text" name="display_name" required placeholder="e.g., Administrator"
                            class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-zinc-700 uppercase tracking-tight">Deskripsi</label>
                        <textarea name="description" rows="3" placeholder="Deskripsi mengenai role ini..."
                            class="flex w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-2 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none resize-none"></textarea>
                    </div>
                    <div class="pt-4 flex items-center gap-3">
                        <button type="button" onclick="toggleModal('modal-add-role')"
                            class="flex-1 px-4 py-2.5 rounded-lg border border-zinc-200 text-sm font-bold text-zinc-600 hover:bg-zinc-50 transition-colors active:scale-95">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2.5 rounded-lg bg-[#001f3f] border border-[#001f3f] text-sm font-bold text-white hover:bg-[#002d5a] transition-all shadow-lg shadow-[#001f3f]/10 active:scale-95">
                            Simpan Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Role -->
    <div id="modal-edit-role" class="fixed inset-0 z-[60] hidden">
        <div class="absolute inset-0 bg-zinc-900/40 backdrop-blur-sm" onclick="toggleModal('modal-edit-role')"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg p-6">
            <div
                class="bg-white rounded-2xl shadow-2xl border border-zinc-100 overflow-hidden animate-in zoom-in duration-300">
                <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between bg-zinc-50/50">
                    <h3 class="font-bold text-zinc-900">Edit Peran</h3>
                    <button onclick="toggleModal('modal-edit-role')"
                        class="text-zinc-400 hover:text-zinc-600 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="form-edit-role" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-zinc-700 uppercase tracking-tight">Nama Role (Unique)</label>
                        <input type="text" name="name" id="edit-name" required placeholder="e.g., administrator"
                            class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-zinc-700 uppercase tracking-tight">Display Name</label>
                        <input type="text" name="display_name" id="edit-display-name" required
                            placeholder="e.g., Administrator"
                            class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-zinc-700 uppercase tracking-tight">Deskripsi</label>
                        <textarea name="description" id="edit-description" rows="3" placeholder="Deskripsi mengenai role ini..."
                            class="flex w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-2 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none resize-none"></textarea>
                    </div>
                    <div class="pt-4 flex items-center gap-3">
                        <button type="button" onclick="toggleModal('modal-edit-role')"
                            class="flex-1 px-4 py-2.5 rounded-lg border border-zinc-200 text-sm font-bold text-zinc-600 hover:bg-zinc-50 transition-colors active:scale-95">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2.5 rounded-lg bg-[#001f3f] border border-[#001f3f] text-sm font-bold text-white hover:bg-[#002d5a] transition-all shadow-lg shadow-[#001f3f]/10 active:scale-95">
                            Perbarui Role
                        </button>
                    </div>
                </form>
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
            if ($('#roleTable').length > 0) {
                var table = $('#roleTable').DataTable({
                    dom: 't<"flex flex-col sm:flex-row items-center justify-between px-6 py-4 border-t border-zinc-100"ip>',
                    language: {
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        emptyTable: "<div class='py-20 flex flex-col items-center justify-center space-y-3'><div class='h-16 w-16 rounded-2xl bg-zinc-50 flex items-center justify-center'><i class='fas fa-shield-slash text-2xl text-zinc-300'></i></div><div class='text-center'><p class='text-zinc-900 font-semibold'>Tidak ada data role tersedia</p><p class='text-zinc-500 text-xs mt-1'>Silakan tambah data role baru untuk melihat daftar di sini.</p></div></div>",
                        paginate: {
                            next: '<i class="fas fa-chevron-right text-[10px]"></i>',
                            previous: '<i class="fas fa-chevron-left text-[10px]"></i>'
                        }
                    }
                });

                $('#customSearch').on('keyup', function() {
                    table.search(this.value).draw();
                });

                $('#customLength').on('change', function() {
                    table.page.len($(this).val()).draw();
                });
            }
        });

        function toggleModal(id) {
            const modal = document.getElementById(id);
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            } else {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        function editRole(role) {
            const form = document.getElementById('form-edit-role');
            form.action = `/admin/role/${role.id}`;
            document.getElementById('edit-name').value = role.name;
            document.getElementById('edit-display-name').value = role.display_name;
            document.getElementById('edit-description').value = role.description || '';
            toggleModal('modal-edit-role');
        }

        function confirmDelete(id, name) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Role " + name + " akan dihapus secara permanen!",
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
