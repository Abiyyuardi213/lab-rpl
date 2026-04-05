@extends('layouts.admin')

@section('title', 'Jadwal Praktikum')

@section('content')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Jadwal Pelaksanaan Modul</h1>
                <p class="text-sm text-zinc-500 mt-1">Kelola seluruh jadwal praktikum untuk setiap modul di sini.</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Jadwal Praktikum</span>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Total Jadwal</p>
                    <p class="text-2xl font-black text-zinc-900 mt-1">{{ count($jadwals) }}</p>
                </div>
                <div class="h-10 w-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Mendatang</p>
                    <p class="text-2xl font-black text-zinc-900 mt-1">
                        {{ $jadwals->where('tanggal', '>=', date('Y-m-d'))->count() }}</p>
                </div>
                <div class="h-10 w-10 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Lokasi Berbeda</p>
                    <p class="text-2xl font-black text-zinc-900 mt-1">{{ $jadwals->unique('ruangan')->count() }}</p>
                </div>
                <div class="h-10 w-10 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600">
                    <i class="fas fa-door-open"></i>
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden">
            <div class="p-6 pb-4 flex items-center justify-between gap-4 border-b border-zinc-100">
                <div class="flex items-center gap-2 flex-1">
                    <div class="relative max-w-sm w-full">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-xs"></i>
                        <input type="text" id="customSearch" placeholder="Cari jadwal..."
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
                    <button onclick="openAddModal()"
                        class="inline-flex h-9 items-center justify-center rounded-md bg-[#001f3f] px-4 py-2 text-sm font-medium text-white shadow hover:bg-[#002d5a] transition-colors">
                        <i class="fas fa-plus mr-2 text-xs"></i>
                        Tambah Jadwal
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="jadwalTable" class="w-full text-sm text-left">
                    <thead class="bg-zinc-50 border-b border-zinc-100 text-zinc-500 font-medium h-10">
                        <tr>
                            <th class="px-6 align-middle font-medium text-zinc-500">PRAKTIKUM</th>
                            <th class="px-6 align-middle font-medium text-zinc-500">MODUL</th>
                            <th class="px-6 align-middle font-medium text-zinc-500">TANGGAL & WAKTU</th>
                            <th class="px-6 align-middle font-medium text-zinc-500">RUANGAN</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 text-zinc-900">
                        @foreach ($jadwals as $jadwal)
                            <tr class="hover:bg-zinc-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span
                                            class="font-bold text-zinc-900">{{ $jadwal->praktikum->nama_praktikum }}</span>
                                        <span
                                            class="text-[10px] font-mono text-zinc-400 uppercase tracking-tight">{{ $jadwal->praktikum->kode_praktikum }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-medium">
                                    <span
                                        class="px-2 py-0.5 rounded-full bg-zinc-100 text-zinc-600 text-[10px] font-black uppercase tracking-wider border border-zinc-200">
                                        {{ $jadwal->judul_modul }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span
                                            class="font-semibold text-zinc-800">{{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d M Y') }}</span>
                                        <span class="text-[10px] text-zinc-400 font-mono italic">
                                            {{ substr($jadwal->waktu_mulai, 0, 5) }} -
                                            {{ substr($jadwal->waktu_selesai, 0, 5) }} WIB
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-zinc-500 font-medium">
                                    {{ $jadwal->ruangan ?? 'Daring / TBA' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('presensi.generate-jadwal-qr', $jadwal->id) }}" 
                                           class="inline-flex items-center justify-center h-8 w-8 rounded-md text-emerald-500 hover:text-emerald-700 hover:bg-emerald-50 transition-colors"
                                           title="QR Presensi">
                                            <i class="fas fa-qrcode text-xs"></i>
                                        </a>
                                        <button onclick='openEditModal(@json($jadwal))'
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-md text-zinc-500 hover:text-[#001f3f] hover:bg-zinc-100 transition-colors">
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>
                                        <form action="{{ route('admin.jadwal-praktikum.destroy', $jadwal->id) }}"
                                            method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="confirmDelete('{{ $jadwal->id }}')"
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

    <!-- Shadcn-like Modal Structure -->
    <div id="jadwalModal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <!-- Background Backdrop -->
            <div class="fixed inset-0 bg-zinc-950/40 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>

            <div
                class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-zinc-200">
                <div class="px-6 py-6 sm:px-8">
                    <div class="flex items-center justify-between mb-6">
                        <div class="space-y-1">
                            <h3 class="text-xl font-bold tracking-tight text-zinc-900" id="modalTitle">Tambah Jadwal</h3>
                            <p class="text-xs text-zinc-500">Tentukan waktu dan lokasi praktikum ini akan diadakan.</p>
                        </div>
                        <button onclick="closeModal()"
                            class="rounded-md h-8 w-8 inline-flex items-center justify-center text-zinc-400 hover:text-zinc-600 hover:bg-zinc-50 transition-colors">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>

                    <form id="jadwalForm" action="{{ route('admin.jadwal-praktikum.store') }}" method="POST"
                        class="space-y-5">
                        @csrf
                        <input type="hidden" name="_method" id="formMethod" value="POST">

                        <div class="space-y-2">
                            <label class="text-xs font-bold text-zinc-700 uppercase tracking-tight">Pilih Praktikum</label>
                            <select name="praktikum_id" id="modal_praktikum_id" required
                                class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-2 text-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none font-medium">
                                <option value="">-- Pilih Praktikum --</option>
                                @foreach ($praktikums as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama_praktikum }}
                                        ({{ $p->kode_praktikum }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold text-zinc-700 uppercase tracking-tight">Judul Modul</label>
                            <input type="text" name="judul_modul" id="modal_judul_modul"
                                placeholder="Contoh: Modul 1: Pengenalan" required
                                class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-2 text-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none font-medium">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-zinc-700 uppercase tracking-tight">Tanggal</label>
                                <input type="date" name="tanggal" id="modal_tanggal" required
                                    class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-2 text-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none font-medium">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-zinc-700 uppercase tracking-tight">Ruangan</label>
                                <input type="text" name="ruangan" id="modal_ruangan" placeholder="Lab R 301 / Zoom"
                                    class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-2 text-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none font-medium">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-zinc-700 uppercase tracking-tight">Waktu Mulai</label>
                                <input type="time" name="waktu_mulai" id="modal_waktu_mulai" required
                                    class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-2 text-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none font-medium">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-zinc-700 uppercase tracking-tight">Waktu
                                    Selesai</label>
                                <input type="time" name="waktu_selesai" id="modal_waktu_selesai" required
                                    class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-2 text-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none font-medium">
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-100">
                            <button type="button" onclick="closeModal()"
                                class="inline-flex h-10 items-center justify-center rounded-lg border border-zinc-200 bg-white px-6 text-sm font-bold text-zinc-600 transition-all hover:bg-zinc-50 active:scale-95">
                                Batal
                            </button>
                            <button type="submit"
                                class="inline-flex h-10 items-center justify-center rounded-lg bg-[#001f3f] px-8 text-sm font-bold text-white shadow-lg shadow-[#001f3f]/20 transition-all hover:bg-[#002d5a] active:scale-95 uppercase tracking-widest">
                                Simpan Jadwal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- DataTables & Scripts -->
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
            var table = $('#jadwalTable').DataTable({
                dom: 't<"flex flex-col sm:flex-row items-center justify-between px-6 py-4 border-t border-zinc-100"ip>',
                language: {
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    emptyTable: "<div class='py-20 flex flex-col items-center justify-center space-y-3'><div class='h-16 w-16 rounded-2xl bg-zinc-50 flex items-center justify-center'><i class='fas fa-calendar-times text-2xl text-zinc-300'></i></div><div class='text-center'><p class='text-zinc-900 font-semibold'>Tidak ada jadwal tersedia</p><p class='text-zinc-500 text-xs mt-1'>Silakan tambah jadwal baru untuk melihat daftar di sini.</p></div></div>",
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
        });

        function openAddModal() {
            $('#modalTitle').text('Tambah Jadwal');
            $('#formMethod').val('POST');
            $('#jadwalForm').attr('action', "{{ route('admin.jadwal-praktikum.store') }}");
            $('#jadwalForm')[0].reset();
            $('#jadwalModal').removeClass('hidden');
        }

        function openEditModal(jadwal) {
            $('#modalTitle').text('Edit Jadwal');
            $('#formMethod').val('PATCH');
            $('#jadwalForm').attr('action', `/admin/jadwal-praktikum/${jadwal.id}`);

            $('#modal_praktikum_id').val(jadwal.praktikum_id);
            $('#modal_judul_modul').val(jadwal.judul_modul);
            $('#modal_tanggal').val(jadwal.tanggal);
            $('#modal_waktu_mulai').val(jadwal.waktu_mulai);
            $('#modal_waktu_selesai').val(jadwal.waktu_selesai);
            $('#modal_ruangan').val(jadwal.ruangan);

            $('#jadwalModal').removeClass('hidden');
        }

        function closeModal() {
            $('#jadwalModal').addClass('hidden');
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Jadwal yang dihapus tidak dapat dipulihkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#001f3f',
                cancelButtonColor: '#f4f4f5',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    cancelButton: 'text-zinc-600 border border-zinc-200',
                    confirmButton: 'bg-[#001f3f]'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/jadwal-praktikum/${id}`;
                    form.innerHTML = `
                        @csrf
                        @method('DELETE')
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            })
        }
    </script>
@endsection
