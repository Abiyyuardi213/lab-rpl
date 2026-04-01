@extends('layouts.admin')

@section('title', 'Manajemen Pendaftaran')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Pendaftaran Praktikum</h1>
                <p class="text-sm text-zinc-500">Verifikasi dokumen dan kelola pendaftaran mahasiswa.</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="flex bg-white border border-zinc-200 rounded-xl p-1 shadow-sm">
                    <a href="{{ route('admin.pendaftaran.index') }}"
                        class="px-4 py-1.5 text-[10px] font-bold uppercase rounded-lg {{ !request('status') ? 'bg-[#001f3f] text-white' : 'text-zinc-500 hover:text-zinc-900' }} transition-all">Semua</a>
                    <a href="{{ route('admin.pendaftaran.index', ['status' => 'pending']) }}"
                        class="px-4 py-1.5 text-[10px] font-bold uppercase rounded-lg {{ request('status') == 'pending' ? 'bg-amber-500 text-white' : 'text-zinc-500 hover:text-zinc-900' }} transition-all">Pending</a>
                    <a href="{{ route('admin.pendaftaran.index', ['status' => 'verified']) }}"
                        class="px-4 py-1.5 text-[10px] font-bold uppercase rounded-lg {{ request('status') == 'verified' ? 'bg-emerald-500 text-white' : 'text-zinc-500 hover:text-zinc-900' }} transition-all">Verified</a>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="rounded-2xl border border-zinc-200 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4 border-b border-zinc-100 bg-zinc-50/30">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-tight">Tampilkan</span>
                    <select id="customLength"
                        class="h-7 rounded-lg border border-zinc-200 bg-white px-2 text-[11px] font-bold shadow-sm focus:outline-none active:scale-95 transition-all">
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-tight">Data</span>
                </div>
                <div class="relative w-full sm:max-w-xs">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 text-xs"></i>
                    <input type="text" id="customSearch" placeholder="Cari mahasiswa, NPM, atau praktikum..."
                        class="flex h-9 w-full rounded-xl border border-zinc-200 bg-white px-3 py-1 pl-9 text-xs shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#001f3f]/5 placeholder:text-zinc-400">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="pendaftaranTable" class="w-full text-sm text-left">
                    <thead class="bg-zinc-50 border-b border-zinc-100 text-zinc-500 font-medium h-10">
                        <tr>
                            <th class="px-6 align-middle font-black text-[10px] text-zinc-400 uppercase tracking-widest w-12 text-center">NO</th>
                            <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest">Mahasiswa
                            </th>
                            <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest">Praktikum /
                                Sesi</th>
                            <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest">Tanggal
                                Daftar</th>
                            <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest">Status</th>
                            <th
                                class="px-6 py-4 text-center text-[10px] font-black text-zinc-400 uppercase tracking-widest">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse($pendaftarans as $index => $p)
                            <tr class="hover:bg-zinc-50/30 transition-colors">
                                <td class="px-6 py-4 text-center text-zinc-500 font-medium">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-9 w-9 rounded-full bg-zinc-100 flex items-center justify-center border border-zinc-200 overflow-hidden">
                                            <img src="{{ $p->foto_almamater ? asset('storage/' . $p->foto_almamater) : 'https://ui-avatars.com/api/?name=' . urlencode($p->praktikan->user->name) }}"
                                                class="h-full w-full object-cover">
                                        </div>
                                        <div>
                                            <div class="font-bold text-zinc-900">{{ $p->praktikan->user->name }}</div>
                                            <div class="text-[10px] font-bold text-[#001f3f] font-mono uppercase">
                                                {{ $p->praktikan->npm }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-zinc-900">{{ $p->praktikum->nama_praktikum }}</div>
                                    <div class="text-[10px] text-zinc-500 font-medium capitalize">{{ $p->sesi->nama_sesi }}
                                        - {{ $p->sesi->hari }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-zinc-600 font-medium">{{ $p->created_at->format('d M Y') }}</div>
                                    <div class="text-[10px] text-zinc-400 font-mono uppercase">
                                        {{ $p->created_at->format('H:i') }} WIB</div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusConfig = [
                                            'pending' => [
                                                'label' => 'Pending',
                                                'class' => 'bg-amber-50 text-amber-700 border-amber-100',
                                            ],
                                            'verified' => [
                                                'label' => 'Verified',
                                                'class' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                            ],
                                            'rejected' => [
                                                'label' => 'Rejected',
                                                'class' => 'bg-rose-50 text-rose-700 border-rose-100',
                                            ],
                                        ];
                                        $st = $statusConfig[$p->status] ?? [
                                            'label' => $p->status,
                                            'class' => 'bg-zinc-50 text-zinc-500 border-zinc-100',
                                        ];
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-[9px] font-black uppercase border {{ $st['class'] }}">
                                        {{ $st['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.pendaftaran.show', $p->id) }}"
                                            class="h-9 w-9 inline-flex items-center justify-center rounded-xl border border-zinc-200 bg-white text-zinc-600 hover:bg-[#001f3f] hover:text-white transition-all shadow-sm active:scale-95 group"
                                            title="Detail Pendaftaran">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>

                                        @if ($p->status === 'pending')
                                            <button onclick="updateStatus('{{ $p->id }}', 'verified')"
                                                class="h-9 w-9 inline-flex items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white transition-all shadow-sm active:scale-95"
                                                title="Verifikasi Mahasiswa">
                                                <i class="fas fa-check text-xs"></i>
                                            </button>
                                            <button onclick="updateStatus('{{ $p->id }}', 'rejected')"
                                                class="h-9 w-9 inline-flex items-center justify-center rounded-xl border border-rose-200 bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white transition-all shadow-sm active:scale-95"
                                                title="Tolak Pendaftaran">
                                                <i class="fas fa-times text-xs"></i>
                                            </button>
                                        @endif

                                        @if ($p->status === 'rejected')
                                            <button onclick="deletePendaftaran('{{ $p->id }}', '{{ $p->praktikan->user->name }}')"
                                                class="h-9 w-9 inline-flex items-center justify-center rounded-xl border border-rose-200 bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white transition-all shadow-sm active:scale-95"
                                                title="Hapus Permanen">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-2 text-zinc-400 italic">
                                        <i class="fas fa-inbox text-3xl opacity-20"></i>
                                        <span>Tidak ada data pendaftaran</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
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
            padding: 2px 10px !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            margin-left: 4px !important;
            background: white !important;
            color: #71717a !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #001f3f !important;
            border-color: #001f3f !important;
            color: white !important;
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
            if ($('#pendaftaranTable').length > 0) {
                var table = $('#pendaftaranTable').DataTable({
                    dom: 't<"flex flex-col sm:flex-row items-center justify-between px-6 py-4 border-t border-zinc-100"ip>',
                    language: {
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        emptyTable: "<div class='py-20 flex flex-col items-center justify-center space-y-3'><div class='h-16 w-16 rounded-2xl bg-zinc-50 flex items-center justify-center'><i class='fas fa-inbox text-2xl text-zinc-300'></i></div><div class='text-center'><p class='text-zinc-900 font-semibold'>Tidak ada data pendaftaran</p><p class='text-zinc-500 text-xs mt-1'>Mahasiswa belum ada yang mendaftar atau filter tidak menemukan hasil.</p></div></div>",
                        paginate: {
                            next: '<i class="fas fa-chevron-right text-[10px]"></i>',
                            previous: '<i class="fas fa-chevron-left text-[10px]"></i>'
                        }
                    },
                    columnDefs: [{
                        orderable: false,
                        targets: [0, 5]
                    }],
                    pageLength: 10,
                    order: []
                });

                $('#customSearch').on('keyup', function() {
                    table.search(this.value).draw();
                });

                $('#customLength').on('change', function() {
                    table.page.len($(this).val()).draw();
                });
            }
        });

        function updateStatus(id, status) {
            let url = "{{ route('admin.pendaftaran.update-status', ':id') }}".replace(':id', id);

            if (status === 'verified') {
                Swal.fire({
                    title: 'Verifikasi Pendaftaran?',
                    text: "Konfirmasi bahwa data mahasiswa sudah sesuai.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#f1f5f9',
                    confirmButtonText: 'Ya, Verifikasi',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-[1.5rem]',
                        confirmButton: 'px-6 py-2.5 rounded-xl font-bold text-xs',
                        cancelButton: 'px-6 py-2.5 rounded-xl font-bold text-xs text-zinc-600'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitStatusForm(url, status);
                    }
                });
            } else {
                Swal.fire({
                    title: 'Tolak Pendaftaran?',
                    text: "Berikan alasan penolakan untuk mahasiswa.",
                    icon: 'warning',
                    input: 'textarea',
                    inputPlaceholder: 'Tulis alasan penolakan di sini...',
                    inputAttributes: {
                        'aria-label': 'Tulis alasan penolakan di sini'
                    },
                    showCancelButton: true,
                    confirmButtonColor: '#f43f5e',
                    cancelButtonColor: '#f1f5f9',
                    confirmButtonText: 'Ya, Tolak',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Anda harus memberikan alasan penolakan!'
                        }
                    },
                    customClass: {
                        popup: 'rounded-[1.5rem]',
                        confirmButton: 'px-6 py-2.5 rounded-xl font-bold text-xs',
                        cancelButton: 'px-6 py-2.5 rounded-xl font-bold text-xs text-zinc-600',
                        input: 'rounded-xl text-sm border-zinc-200'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitStatusForm(url, status, result.value);
                    }
                });
            }
        }

        function submitStatusForm(url, status, catatan = '') {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;

            const csrfToken = '{{ csrf_token() }}';

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PATCH';
            form.appendChild(methodInput);

            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = status;
            form.appendChild(statusInput);

            if (catatan) {
                const catatanInput = document.createElement('input');
                catatanInput.type = 'hidden';
                catatanInput.name = 'catatan';
                catatanInput.value = catatan;
                form.appendChild(catatanInput);
            }

            document.body.appendChild(form);
            form.submit();
        }

        function deletePendaftaran(id, name) {
            Swal.fire({
                title: 'Hapus pendaftaran?',
                text: "Pendaftaran atas nama " + name + " akan dihapus secara permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#f4f4f5',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-[1.5rem]',
                    confirmButton: 'px-6 py-2.5 rounded-xl font-bold text-xs',
                    cancelButton: 'px-6 py-2.5 rounded-xl font-bold text-xs text-zinc-600'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ route('admin.pendaftaran.destroy', ':id') }}".replace(':id', id);

                    const csrfToken = '{{ csrf_token() }}';
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    form.appendChild(csrfInput);

                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    form.appendChild(methodInput);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@endsection
