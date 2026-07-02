@extends('layouts.admin')

@section('title', 'Master Kelas')

@section('content')
    <div class="space-y-6">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 uppercase">Master Kelas</h1>
                <p class="text-sm text-zinc-500 mt-1 italic">"Kelola opsi kelas yang tersedia untuk praktikum."</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Master Kelas</span>
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden">
            <div class="p-6 pb-4 flex items-center justify-between gap-4 border-b border-zinc-100">
                <div class="relative max-w-sm w-full">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-xs"></i>
                    <input type="text" id="customSearch" placeholder="Cari kelas..."
                        class="flex h-9 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 pl-9 text-sm shadow-sm transition-colors placeholder:text-zinc-500 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
                </div>
                <button onclick="toggleModal('modal-add-kelas')"
                    class="inline-flex h-9 items-center justify-center rounded-md bg-[#001f3f] px-4 py-2 text-sm font-medium text-white shadow hover:bg-[#002d5a] transition-colors whitespace-nowrap">
                    <i class="fas fa-plus mr-2 text-xs"></i>
                    Tambah Kelas
                </button>
            </div>

            <div class="overflow-x-auto">
                <table id="kelasTable" class="w-full text-sm text-left">
                    <thead class="bg-zinc-50 border-b border-zinc-100 text-zinc-500 font-medium h-10">
                        <tr>
                            <th class="px-6 align-middle font-medium text-zinc-500 w-12 text-center text-[10px] uppercase tracking-wider">No</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">Nama Kelas</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">Status</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-right text-[10px] uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 text-zinc-900">
                        @foreach ($kelas as $index => $k)
                            <tr class="hover:bg-zinc-50/50 transition-colors">
                                <td class="px-6 py-4 text-center text-zinc-500 font-medium">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 font-semibold text-zinc-900">{{ $k->nama_kelas }}</td>
                                <td class="px-6 py-4">
                                    <button type="button" onclick="toggleStatus('{{ $k->id }}', this)"
                                        class="inline-flex items-center rounded-md border px-2 py-1 text-[10px] font-bold uppercase tracking-wider {{ $k->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-zinc-50 text-zinc-500 border-zinc-200' }}">
                                        {{ $k->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button onclick='openEditModal(@json($k))'
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-md text-zinc-500 hover:text-amber-600 hover:bg-amber-50 transition-colors">
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>
                                        <form id="delete-form-{{ $k->id }}"
                                            action="{{ route('admin.kelas.destroy', $k->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete('{{ $k->id }}', '{{ $k->nama_kelas }}')"
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

    <!-- Modal Tambah -->
    <div id="modal-add-kelas" class="fixed inset-0 z-[60] hidden bg-zinc-900/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full max-w-md overflow-hidden shadow-2xl border border-zinc-200">
            <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between bg-zinc-50/50">
                <h3 class="font-bold text-zinc-900 uppercase tracking-tight">Tambah Kelas</h3>
                <button onclick="toggleModal('modal-add-kelas')" class="h-8 w-8 flex items-center justify-center rounded-lg hover:bg-zinc-100 text-zinc-400 transition-colors">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <form action="{{ route('admin.kelas.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Nama Kelas</label>
                    <input type="text" name="nama_kelas" required placeholder="Contoh: Kelas P atau 4IA01"
                        class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none">
                </div>
                <label class="flex items-center gap-2 text-xs font-bold text-zinc-600">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-zinc-300">
                    Aktifkan kelas ini
                </label>
                <div class="pt-4 flex items-center justify-end gap-3">
                    <button type="button" onclick="toggleModal('modal-add-kelas')"
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

    <!-- Modal Edit -->
    <div id="modal-edit-kelas" class="fixed inset-0 z-[60] hidden bg-zinc-900/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full max-w-md overflow-hidden shadow-2xl border border-zinc-200">
            <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between bg-zinc-50/50">
                <h3 class="font-bold text-zinc-900 uppercase tracking-tight">Edit Data Kelas</h3>
                <button onclick="toggleModal('modal-edit-kelas')" class="h-8 w-8 flex items-center justify-center rounded-lg hover:bg-zinc-100 text-zinc-400 transition-colors">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <form id="form-edit-kelas" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Nama Kelas</label>
                    <input type="text" name="nama_kelas" id="edit-nama_kelas" required
                        class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-amber-500/10 focus:border-amber-500 outline-none">
                </div>
                <label class="flex items-center gap-2 text-xs font-bold text-zinc-600">
                    <input type="checkbox" name="is_active" id="edit-is-active" value="1" class="rounded border-zinc-300">
                    Aktifkan kelas ini
                </label>
                <div class="pt-4 flex items-center justify-end gap-3">
                    <button type="button" onclick="toggleModal('modal-edit-kelas')"
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
            const table = $('#kelasTable').DataTable({
                dom: 't<"flex flex-col sm:flex-row items-center justify-between px-6 py-4 border-t border-zinc-100"ip>',
                language: {
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    emptyTable: "Belum ada data kelas",
                    paginate: {
                        next: '<i class="fas fa-chevron-right text-[10px]"></i>',
                        previous: '<i class="fas fa-chevron-left text-[10px]"></i>'
                    }
                },
                columnDefs: [{
                    orderable: false,
                    targets: [3]
                }]
            });

            $('#customSearch').on('keyup', function() {
                table.search(this.value).draw();
            });
        });

        function toggleModal(id) {
            document.getElementById(id).classList.toggle('hidden');
        }

        function openEditModal(kelas) {
            document.getElementById('form-edit-kelas').action = `/admin/kelas/${kelas.id}`;
            document.getElementById('edit-nama_kelas').value = kelas.nama_kelas;
            document.getElementById('edit-is-active').checked = kelas.is_active;
            toggleModal('modal-edit-kelas');
        }

        function toggleStatus(id, button) {
            fetch(`/admin/kelas/${id}/toggle-status`, {
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

        function confirmDelete(id, nama_kelas) {
            Swal.fire({
                title: 'Hapus Kelas?',
                text: nama_kelas + ' akan dihapus dari master data kelas.',
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
    </script>
@endsection
