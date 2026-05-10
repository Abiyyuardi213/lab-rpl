@extends('layouts.admin')

@section('title', 'Detail Pelamar - ' . $recruitment->title)

@section('content')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.recruitment.index') }}"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-zinc-200 bg-white text-zinc-400 hover:text-zinc-900 transition-colors shadow-sm">
                    <i class="fas fa-chevron-left text-xs"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900">{{ $recruitment->title }}</h1>
                    <p class="text-sm text-zinc-500 mt-1">Tinjau berkas dan seleksi mahasiswa yang mendaftar.</p>
                </div>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                <span>/</span>
                <a href="{{ route('admin.recruitment.index') }}" class="hover:text-zinc-900 transition-colors">Rekrutmen</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Detail Pelamar</span>
            </div>
        </div>

        <!-- Table Container -->
        <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden">
            <div class="p-6 pb-4 flex items-center justify-between gap-4 border-b border-zinc-100">
                <div class="flex items-center gap-2 flex-1">
                    <div class="relative max-w-sm w-full">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-xs"></i>
                        <input type="text" id="customSearch" placeholder="Cari pelamar (nama/npm)..."
                            class="flex h-9 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 pl-9 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950 placeholder:text-zinc-500">
                    </div>
                    <button type="button" onclick="openValidationModal()" class="inline-flex items-center justify-center rounded-md text-xs font-bold ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-[#001f3f] text-white hover:bg-blue-900 h-9 px-4 py-2 gap-2 shadow-sm">
                        <i class="fas fa-check-double text-[10px]"></i>
                        Validasi IPK
                    </button>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        <span class="text-xs font-bold text-zinc-600 tracking-tight uppercase">{{ $recruitment->applications->count() }} Pendaftar</span>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="applicantTable" class="w-full text-sm text-left">
                    <thead class="bg-zinc-50 border-b border-zinc-100 text-zinc-500 font-medium h-10">
                        <tr>
                            <th class="px-6 align-middle font-medium text-zinc-500 uppercase text-[10px] tracking-wider">MAHASISWA</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 uppercase text-[10px] tracking-wider">PERSYARATAN</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 uppercase text-[10px] tracking-wider">DOKUMEN</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 uppercase text-[10px] tracking-wider text-center">STATUS</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 uppercase text-[10px] tracking-wider text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 text-zinc-900">
                        @foreach ($recruitment->applications->where('status', '!=', 'rejected') as $app)
                            <tr class="hover:bg-zinc-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-full bg-zinc-100 flex items-center justify-center border border-zinc-200 text-zinc-400">
                                            @if($app->user->profile_picture)
                                                <img src="{{ asset('storage/' . $app->user->profile_picture) }}" class="w-full h-full object-cover rounded-full">
                                            @else
                                                <i class="fas fa-user text-xs"></i>
                                            @endif
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-semibold text-zinc-900 block leading-tight">{{ $app->user->name }}</span>
                                            <span class="text-[10px] text-zinc-400 uppercase font-bold tracking-wider">{{ $app->user->praktikan->npm ?? 'N/A' }} | {{ $app->user->praktikan->angkatan ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-0.5">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[11px] font-bold text-zinc-600">SMT {{ $app->user->praktikan->semester ?? '-' }}</span>
                                            <span class="text-[10px] text-zinc-300">|</span>
                                            <span class="text-[11px] font-bold text-blue-600">IPK {{ number_format($app->ipk, 2) }}</span>
                                        </div>
                                        <span class="text-[10px] text-zinc-400">{{ $app->user->praktikan->jurusan ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ Storage::url($app->cv_path) }}" target="_blank"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors shadow-sm" title="Buka CV">
                                            <i class="far fa-file-pdf text-xs"></i>
                                        </a>
                                        <a href="{{ Storage::url($app->khs_path) }}" target="_blank"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors shadow-sm" title="Buka KHS">
                                            <i class="fas fa-file-invoice text-xs"></i>
                                        </a>
                                        @if($app->transcript_path)
                                            <a href="{{ Storage::url($app->transcript_path) }}" target="_blank"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-colors shadow-sm" title="Buka Transkrip Riwayat Studi">
                                                <i class="fas fa-file-medical text-xs"></i>
                                            </a>
                                        @endif
                                        @if($app->portfolio_url)
                                            <a href="{{ $app->portfolio_url }}" target="_blank"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors shadow-sm" title="Portofolio">
                                                <i class="fas fa-external-link-alt text-[10px]"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-amber-50 text-amber-600 border-amber-100',
                                            'shortlisted' => 'bg-blue-50 text-blue-600 border-blue-100',
                                            'accepted' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                        ];
                                        $statusLabels = [
                                            'pending' => 'Pending',
                                            'shortlisted' => 'Shortlist',
                                            'accepted' => 'Diterima',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $statusClasses[$app->status] }}">
                                        {{ $statusLabels[$app->status] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <button onclick="openStatusModal('{{ $app->id }}', '{{ $app->status }}', '{{ addslashes($app->admin_notes) }}')"
                                            class="inline-flex items-center justify-center h-8 px-3 rounded-md bg-zinc-100 text-zinc-600 text-[10px] font-bold hover:bg-zinc-200 transition-colors">
                                            Update Status
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Rejected Table Section -->
        <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden mt-8">
            <div class="p-6 pb-4 border-b border-zinc-100 bg-rose-50/30">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-[0.2em] text-rose-600 flex items-center gap-2">
                            <i class="fas fa-user-xmark"></i>
                            Peserta Tidak Lolos
                        </h3>
                        <p class="text-[10px] text-rose-500 font-medium mt-1">Daftar pelamar yang ditolak atau tidak memenuhi syarat IPK.</p>
                    </div>
                    <span class="px-2.5 py-1 bg-rose-100 text-rose-700 rounded-lg text-[10px] font-black tracking-widest">
                        {{ $recruitment->applications->where('status', 'rejected')->count() }} PESERTA
                    </span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table id="rejectedTable" class="w-full text-sm text-left">
                    <thead class="bg-zinc-50 border-b border-zinc-100 text-zinc-500 font-medium h-10">
                        <tr>
                            <th class="px-6 align-middle font-medium text-zinc-500 uppercase text-[10px] tracking-wider">MAHASISWA</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 uppercase text-[10px] tracking-wider">PERSYARATAN</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 uppercase text-[10px] tracking-wider">KETERANGAN PENOLAKAN</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 uppercase text-[10px] tracking-wider text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 text-zinc-900">
                        @foreach ($recruitment->applications->where('status', 'rejected') as $app)
                            <tr class="hover:bg-zinc-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3 opacity-70">
                                        <div class="h-8 w-8 rounded-full bg-zinc-100 flex items-center justify-center border border-zinc-200 text-zinc-400">
                                            <i class="fas fa-user text-xs"></i>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-semibold text-zinc-900 block leading-tight">{{ $app->user->name }}</span>
                                            <span class="text-[10px] text-zinc-400 uppercase font-bold tracking-wider">{{ $app->user->praktikan->npm ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-0.5 opacity-70">
                                        <span class="text-[11px] font-bold text-zinc-600">IPK {{ number_format($app->ipk, 2) }}</span>
                                        <span class="text-[10px] text-zinc-400">{{ $app->user->praktikan->jurusan ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="p-2 rounded-lg bg-rose-50/50 border border-rose-100 text-[10px] text-rose-600 leading-relaxed italic">
                                        {{ $app->admin_notes ?: 'Tidak ada catatan penolakan.' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end">
                                        <button onclick="openStatusModal('{{ $app->id }}', '{{ $app->status }}', '{{ addslashes($app->admin_notes) }}')"
                                            class="inline-flex items-center justify-center h-8 px-3 rounded-md bg-zinc-100 text-zinc-600 text-[10px] font-bold hover:bg-zinc-200 transition-colors">
                                            Pulihkan
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Validation Modal -->
    <div id="validationModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-zinc-950/60 backdrop-blur-sm" onclick="closeValidationModal()"></div>
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md relative z-10 overflow-hidden border border-zinc-200">
            <div class="p-8 text-center space-y-6">
                <div class="h-20 w-20 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center mx-auto text-blue-600 shadow-inner">
                    <i class="fas fa-check-double text-3xl"></i>
                </div>
                <div class="space-y-2">
                    <h3 class="text-xl font-black text-zinc-900 tracking-tight">Konfirmasi Validasi IPK</h3>
                    <p class="text-xs text-zinc-500 leading-relaxed">
                        Sistem akan memeriksa semua pendaftar dengan status <span class="font-bold text-amber-600">Pending</span> dan membandingkannya dengan syarat minimum IPK (<span class="font-bold text-blue-600">{{ number_format($recruitment->min_ipk, 2) }}</span>).
                    </p>
                    <p class="text-[10px] text-zinc-400 italic">Peserta yang tidak memenuhi syarat akan otomatis ditolak.</p>
                </div>
                <div class="flex flex-col gap-2 pt-2">
                    <form action="{{ route('admin.recruitment.validate-ipk', $recruitment->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full inline-flex h-11 items-center justify-center rounded-xl bg-[#001f3f] px-8 py-2 text-sm font-bold text-white shadow-lg shadow-blue-900/25 hover:bg-blue-900 transition-all">
                            Ya, Jalankan Validasi
                        </button>
                    </form>
                    <button type="button" onclick="closeValidationModal()" class="inline-flex h-11 items-center justify-center rounded-xl bg-zinc-100 px-8 py-2 text-sm font-bold text-zinc-600 hover:bg-zinc-200 transition-all">
                        Batalkan
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div id="statusModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-zinc-950/60 backdrop-blur-sm" onclick="closeStatusModal()"></div>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative z-10 overflow-hidden border border-zinc-200">
            <div class="p-6 border-b border-zinc-100 flex items-center justify-between">
                <h3 class="font-bold text-zinc-900 tracking-tight text-lg">Update Status Seleksi</h3>
                <button onclick="closeStatusModal()" class="text-zinc-400 hover:text-zinc-900 transition-colors"><i class="fas fa-times"></i></button>
            </div>
            <form id="statusForm" method="POST" class="p-6 space-y-5">
                @csrf
                @method('PATCH')
                
                <div class="space-y-2">
                    <label class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Pilih Keputusan</label>
                    <select name="status" id="modalStatus" class="flex h-11 w-full rounded-xl border border-zinc-200 bg-white px-4 py-2 text-sm shadow-sm transition-colors focus:outline-none focus:ring-1 focus:ring-zinc-950">
                        <option value="pending">Menunggu (Pending)</option>
                        <option value="shortlisted">Lolos Administrasi (Shortlist)</option>
                        <option value="rejected">Ditolak (Rejected)</option>
                        <option value="accepted">Diterima Sebagai Aslab (Accepted)</option>
                    </select>
                    
                    <div id="acceptedWarning" class="hidden mt-3 p-3 rounded-xl bg-amber-50 border border-amber-200 flex items-start gap-3">
                        <i class="fas fa-triangle-exclamation text-amber-500 mt-0.5"></i>
                        <p class="text-[10px] text-amber-700 font-bold leading-relaxed uppercase">
                            Warning: Konfirmasi pendaftaran ini akan otomatis menjadikan user sebagai Aslab.
                        </p>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Catatan Tambahan (Opsional)</label>
                    <textarea name="admin_notes" id="modalNotes" rows="4" class="flex w-full rounded-xl border border-zinc-200 bg-white px-4 py-3 text-sm shadow-sm transition-colors focus:outline-none focus:ring-1 focus:ring-zinc-950 placeholder:text-zinc-400" placeholder="Berikan catatan perbaikan atau alasan penolakan..."></textarea>
                </div>

                <div class="flex flex-col gap-2 pt-2">
                    <button type="submit" class="inline-flex h-11 items-center justify-center rounded-xl bg-[#1a4fa0] px-8 py-2 text-sm font-bold text-white shadow-lg shadow-[#1a4fa0]/25 hover:bg-[#1a4fa0]/90 transition-all">
                        Simpan Perubahan
                    </button>
                    <button type="button" onclick="closeStatusModal()" class="inline-flex h-11 items-center justify-center rounded-xl bg-zinc-100 px-8 py-2 text-sm font-bold text-zinc-600 hover:bg-zinc-200 transition-all">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('styles')
        <style>
            .dataTables_empty {
                padding: 0 !important;
                background-color: transparent !important;
            }

            /* Pagination Styling */
            .dataTables_paginate {
                display: flex !important;
                align-items: center !important;
                gap: 0.25rem !important;
            }

            .paginate_button {
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                height: 2rem !important;
                min-width: 2rem !important;
                padding: 0 0.5rem !important;
                font-size: 0.75rem !important;
                font-weight: 600 !important;
                border-radius: 0.5rem !important;
                border: 1px solid #e4e4e7 !important;
                background: white !important;
                color: #71717a !important;
                cursor: pointer !important;
                transition: all 0.2s !important;
            }

            .paginate_button:hover:not(.disabled):not(.current) {
                background: #f4f4f5 !important;
                color: #18181b !important;
                border-color: #d4d4d8 !important;
            }

            .paginate_button.current {
                background: #1a4fa0 !important;
                color: white !important;
                border-color: #1a4fa0 !important;
                box-shadow: 0 4px 6px -1px rgb(26 79 160 / 0.1), 0 2px 4px -2px rgb(26 79 160 / 0.1) !important;
            }

            .paginate_button.disabled {
                opacity: 0.5 !important;
                cursor: not-allowed !important;
                background: #fafafa !important;
            }

            .paginate_button.previous, 
            .paginate_button.next {
                padding: 0 0.75rem !important;
            }

            .dataTables_info {
                font-size: 0.75rem !important;
                color: #71717a !important;
                font-weight: 500 !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        <script>
            $(document).ready(function() {
                var table = $('#applicantTable').DataTable({
                    dom: 't<"flex flex-col sm:flex-row items-center justify-between px-6 py-4 border-t border-zinc-100"ip>',
                    language: {
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                        infoFiltered: "(disaring dari _MAX_ total data)",
                        lengthMenu: "Tampilkan _MENU_ data",
                        loadingRecords: "Memuat...",
                        processing: "Sedang memproses...",
                        search: "Cari:",
                        zeroRecords: `
                            <div class="flex flex-col items-center justify-center py-20 text-zinc-400">
                                <div class="h-20 w-20 rounded-full bg-zinc-50 flex items-center justify-center mb-4 border border-zinc-100 shadow-inner">
                                    <i class="fas fa-search text-3xl opacity-20"></i>
                                </div>
                                <h3 class="text-sm font-black uppercase tracking-[0.2em] text-zinc-400">Pencarian Tidak Ditemukan</h3>
                                <p class="text-[10px] italic mt-1 font-medium tracking-tight">Coba gunakan kata kunci pencarian yang lain.</p>
                            </div>
                        `,
                        emptyTable: `
                            <div class="flex flex-col items-center justify-center py-20 text-zinc-400">
                                <div class="h-20 w-20 rounded-full bg-zinc-50 flex items-center justify-center mb-4 border border-zinc-100 shadow-inner">
                                    <i class="fas fa-user-slash text-3xl opacity-20"></i>
                                </div>
                                <h3 class="text-sm font-black uppercase tracking-[0.2em] text-zinc-400">Data Kosong</h3>
                                <p class="text-[10px] italic mt-1 font-medium tracking-tight">Belum ada mahasiswa yang mendaftar pada periode ini.</p>
                            </div>
                        `,
                        paginate: {
                            next: '<i class="fas fa-chevron-right text-[10px]"></i>',
                            previous: '<i class="fas fa-chevron-left text-[10px]"></i>'
                        }
                    }
                });

                var rejectedTable = $('#rejectedTable').DataTable({
                    dom: 't<"flex flex-col sm:flex-row items-center justify-between px-6 py-4 border-t border-zinc-100"ip>',
                    language: {
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                        infoFiltered: "(disaring dari _MAX_ total data)",
                        lengthMenu: "Tampilkan _MENU_ data",
                        loadingRecords: "Memuat...",
                        processing: "Sedang memproses...",
                        search: "Cari:",
                        zeroRecords: `
                            <div class="flex flex-col items-center justify-center py-20 text-zinc-400">
                                <div class="h-20 w-20 rounded-full bg-zinc-50 flex items-center justify-center mb-4 border border-zinc-100 shadow-inner">
                                    <i class="fas fa-search text-3xl opacity-20"></i>
                                </div>
                                <h3 class="text-sm font-black uppercase tracking-[0.2em] text-zinc-400">Pencarian Tidak Ditemukan</h3>
                                <p class="text-[10px] italic mt-1 font-medium tracking-tight">Coba gunakan kata kunci pencarian yang lain.</p>
                            </div>
                        `,
                        emptyTable: `
                            <div class="flex flex-col items-center justify-center py-20 text-zinc-400">
                                <div class="h-20 w-20 rounded-full bg-zinc-50 flex items-center justify-center mb-4 border border-zinc-100 shadow-inner">
                                    <i class="fas fa-user-xmark text-3xl opacity-20"></i>
                                </div>
                                <h3 class="text-sm font-black uppercase tracking-[0.2em] text-zinc-400">Tidak Ada Peserta</h3>
                                <p class="text-[10px] italic mt-1 font-medium tracking-tight">Belum ada peserta yang dinyatakan tidak lolos.</p>
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
                    rejectedTable.search(this.value).draw();
                });
            });

            function openStatusModal(id, status, notes) {
                const modal = document.getElementById('statusModal');
                const form = document.getElementById('statusForm');
                const statusSelect = document.getElementById('modalStatus');
                const notesText = document.getElementById('modalNotes');
                const warning = document.getElementById('acceptedWarning');

                form.action = `{{ url('admin/recruitment/application') }}/${id}/status`;
                statusSelect.value = status;
                notesText.value = notes === 'null' ? '' : notes;
                
                modal.classList.remove('hidden');
                
                if(status === 'accepted') warning.classList.remove('hidden');

                statusSelect.addEventListener('change', function() {
                    if (this.value === 'accepted') {
                        warning.classList.remove('hidden');
                    } else {
                        warning.classList.add('hidden');
                    }
                });
            }

            function closeStatusModal() {
                document.getElementById('statusModal').classList.add('hidden');
            }

            function openValidationModal() {
                document.getElementById('validationModal').classList.remove('hidden');
            }

            function closeValidationModal() {
                document.getElementById('validationModal').classList.add('hidden');
            }
        </script>
    @endpush
@endsection

