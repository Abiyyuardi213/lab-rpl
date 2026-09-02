@extends('layouts.admin')

@section('title', 'Manajemen Sertifikat Praktikum')

@section('content')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Manajemen Sertifikat Praktikum</h1>
                <p class="text-sm text-zinc-500 mt-1">Kelola arsip template sertifikat, format penomoran, dan penandatangan resmi per praktikum dari masa ke masa.</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Sertifikat</span>
            </div>
        </div>

        <!-- Table Container -->
        <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden">
            <div class="p-6 pb-4 flex items-center justify-between gap-4 border-b border-zinc-100">
                <div class="flex items-center gap-2 flex-1">
                    <div class="relative max-w-sm w-full">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-xs"></i>
                        <input type="text" id="customSearch" placeholder="Cari sertifikat praktikum..."
                            class="flex h-9 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 pl-9 text-sm shadow-sm transition-colors placeholder:text-zinc-500 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <select id="customLength"
                        class="h-9 rounded-md border border-zinc-200 bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
                        <option value="10">10 data</option>
                        <option value="25">25 data</option>
                        <option value="50">50 data</option>
                    </select>
                    <button onclick="document.getElementById('modalAddCertificate').classList.remove('hidden')"
                        class="inline-flex h-9 items-center justify-center rounded-md bg-[#001f3f] px-4 py-2 text-sm font-medium text-white shadow hover:bg-[#002d5a] transition-colors">
                        <i class="fas fa-plus mr-2 text-xs"></i>
                        Terbitkan Sertifikat
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="certTable" class="w-full text-sm text-left">
                    <thead class="bg-zinc-50 border-b border-zinc-100 text-zinc-500 font-medium h-10">
                        <tr>
                            <th class="px-6 align-middle font-medium text-zinc-500 w-12 text-center">NO</th>
                            <th class="px-6 align-middle font-medium text-zinc-500">PRAKTIKUM & PERIODE</th>
                            <th class="px-6 align-middle font-medium text-zinc-500">PREFIX SURAT</th>
                            <th class="px-6 align-middle font-medium text-zinc-500">TEMPLATE BACKGROUND</th>
                            <th class="px-6 align-middle font-medium text-zinc-500">PENANDATANGAN</th>
                            <th class="px-6 align-middle font-medium text-zinc-500">TANGGAL RESMI</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 text-zinc-900">
                        @foreach ($certificates as $index => $cert)
                            <tr class="hover:bg-zinc-50/50 transition-colors">
                                <td class="px-6 py-4 text-center text-zinc-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-zinc-900 text-sm">{{ $cert->praktikum->nama_praktikum }}</span>
                                        <span class="text-xs text-zinc-500 font-mono">{{ $cert->praktikum->kode_praktikum }} • Periode {{ $cert->praktikum->periode_praktikum }}</span>
                                        <div class="mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 border border-emerald-100 text-emerald-700 uppercase tracking-wider">
                                                {{ $cert->praktikum->total_lulus ?? 0 }} Mahasiswa Lulus
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono font-bold bg-zinc-50 border border-zinc-200 text-[#001f3f]">
                                        {{ $cert->nomor_surat_prefix }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($cert->bg_template)
                                        <div class="h-10 w-16 rounded-md overflow-hidden border border-zinc-200 bg-zinc-100 flex items-center justify-center">
                                            <img src="{{ asset('storage/' . $cert->bg_template) }}" class="h-full w-full object-cover">
                                        </div>
                                    @else
                                        <span class="text-xs text-zinc-400 italic">Bawaan System (ITATS)</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 space-y-1">
                                    <div class="text-xs">
                                        <span class="font-bold text-zinc-700">Lab:</span> <span class="text-zinc-900">{{ $cert->nama_kepala_lab }}</span>
                                    </div>
                                    <div class="text-xs">
                                        <span class="font-bold text-zinc-700">Kaprodi:</span> <span class="text-zinc-900">{{ $cert->nama_kaprodi }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-medium text-zinc-800">
                                    {{ \Carbon\Carbon::parse($cert->tanggal_sertifikat)->isoFormat('D MMMM Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.certificate.edit', $cert->id) }}"
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-md text-zinc-500 hover:text-[#001f3f] hover:bg-zinc-100 transition-colors" title="Edit">
                                            <i class="fas fa-pencil-alt text-xs"></i>
                                        </a>
                                        <button onclick="confirmDelete('{{ $cert->id }}', '{{ $cert->praktikum->nama_praktikum }}')"
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-md text-zinc-500 hover:text-rose-600 hover:bg-zinc-100 transition-colors" title="Hapus">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                        <form id="delete-form-{{ $cert->id }}" action="{{ route('admin.certificate.destroy', $cert->id) }}" method="POST" class="hidden">
                                            @csrf
                                            @method('DELETE')
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

    <!-- Modal Add Certificate -->
    <div id="modalAddCertificate" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4 sm:p-6">
        <div class="bg-white rounded-2xl border border-zinc-200 max-w-2xl w-full max-h-[85vh] flex flex-col shadow-2xl overflow-hidden">
            <!-- Modal Header (Fixed) -->
            <div class="flex items-center justify-between p-5 border-b border-zinc-100 bg-white shrink-0">
                <div>
                    <h3 class="text-base font-bold text-zinc-900">Terbitkan Sertifikat Praktikum Baru</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Pilih mata praktikum dan konfigurasikan format suratnya.</p>
                </div>
                <button onclick="document.getElementById('modalAddCertificate').classList.add('hidden')" class="w-8 h-8 rounded-lg flex items-center justify-center text-zinc-400 hover:text-zinc-600 hover:bg-zinc-100 transition-colors text-lg">&times;</button>
            </div>

            <!-- Modal Body (Scrollable with custom smooth scrollbar) -->
            <form action="{{ route('admin.certificate.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <div class="p-6 space-y-4 overflow-y-auto flex-1 custom-scrollbar">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-zinc-700">Pilih Mata Praktikum <span class="text-rose-500">*</span></label>
                        <select name="praktikum_id" required class="flex h-9 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 text-xs shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
                            <option value="">— Pilih Praktikum —</option>
                            @foreach($praktikums as $prk)
                                <option value="{{ $prk->id }}">{{ $prk->nama_praktikum }} ({{ $prk->kode_praktikum }}) — Periode {{ $prk->periode_praktikum }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-zinc-700">Prefix Kode Nomor Sertifikat <span class="text-rose-500">*</span></label>
                            <input type="text" name="nomor_surat_prefix" value="{{ old('nomor_surat_prefix', '02/SERT/PSTF/ITATS') }}" required
                                class="flex h-9 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 text-xs shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-zinc-700">Tanggal Resmi Sertifikat <span class="text-rose-500">*</span></label>
                            <input type="date" name="tanggal_sertifikat" value="{{ date('Y-m-d') }}" required
                                class="flex h-9 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 text-xs shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
                        </div>
                    </div>

                    <div class="space-y-3 pt-3 border-t border-zinc-100">
                        <p class="text-xs font-bold text-zinc-900">Penandatangan 1: Kepala Laboratorium</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <input type="text" name="nama_kepala_lab" value="{{ old('nama_kepala_lab', $setting->nama_kepala_lab) }}" placeholder="Nama Kepala Lab" required class="flex h-9 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 text-xs shadow-sm">
                            <input type="text" name="nip_kepala_lab" value="{{ old('nip_kepala_lab', $setting->nip_kepala_lab) }}" placeholder="NIP Kepala Lab" class="flex h-9 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 text-xs shadow-sm">
                        </div>
                        <div>
                            <label class="text-[11px] font-medium text-zinc-500 block mb-1">Upload TTD Kepala Lab (PNG Transparan):</label>
                            <input type="file" name="ttd_kepala_lab" accept="image/png,image/jpeg" class="text-xs text-zinc-500 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border file:border-zinc-200 file:bg-zinc-50 file:text-xs">
                        </div>
                    </div>

                    <div class="space-y-3 pt-3 border-t border-zinc-100">
                        <p class="text-xs font-bold text-zinc-900">Penandatangan 2: Kepala Program Studi (Kaprodi)</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <input type="text" name="nama_kaprodi" value="{{ old('nama_kaprodi', $setting->nama_kaprodi) }}" placeholder="Nama Kaprodi" required class="flex h-9 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 text-xs shadow-sm">
                            <input type="text" name="nip_kaprodi" value="{{ old('nip_kaprodi', $setting->nip_kaprodi) }}" placeholder="NIP Kaprodi" class="flex h-9 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 text-xs shadow-sm">
                        </div>
                        <div>
                            <label class="text-[11px] font-medium text-zinc-500 block mb-1">Upload TTD Kaprodi (PNG Transparan):</label>
                            <input type="file" name="ttd_kaprodi" accept="image/png,image/jpeg" class="text-xs text-zinc-500 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border file:border-zinc-200 file:bg-zinc-50 file:text-xs">
                        </div>
                    </div>

                    <div class="space-y-1.5 pt-3 border-t border-zinc-100">
                        <label class="text-xs font-semibold text-zinc-700">Upload Template Background Kustom (PNG/JPG)</label>
                        <input type="file" name="bg_template" accept="image/png,image/jpeg" class="w-full text-xs text-zinc-500 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border file:border-zinc-200 file:bg-zinc-50 file:text-xs">
                        <p class="text-[10px] text-zinc-400 italic">Opsional. Kosongkan jika ingin menggunakan template background bawaan laboratorium.</p>
                    </div>
                </div>

                <!-- Modal Footer (Fixed) -->
                <div class="p-4 px-6 border-t border-zinc-100 bg-zinc-50/50 flex justify-end gap-2 shrink-0">
                    <button type="button" onclick="document.getElementById('modalAddCertificate').classList.add('hidden')" class="inline-flex h-9 items-center justify-center rounded-md border border-zinc-200 bg-white px-4 py-2 text-xs font-medium text-zinc-700 shadow-sm hover:bg-zinc-50 transition-colors">Batal</button>
                    <button type="submit" class="inline-flex h-9 items-center justify-center rounded-md bg-[#001f3f] px-4 py-2 text-xs font-medium text-white shadow hover:bg-[#002d5a] transition-colors">Simpan & Terbitkan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <style>
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            border: 1px solid #e4e4e7 !important;
            border-radius: 6px !important;
            padding: 4px 10px !important;
            font-size: 11px !important;
            font-weight: 500 !important;
            cursor: pointer !important;
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

        /* Custom Slim Scrollbar for Modal Inner Content */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f4f4f5;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #d4d4d8;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a1a1aa;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            if ($('#certTable').length > 0) {
                var table = $('#certTable').DataTable({
                    dom: 't<"flex flex-col sm:flex-row items-center justify-between px-6 py-4 border-t border-zinc-100"ip>',
                    language: {
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        emptyTable: "<div class='py-20 flex flex-col items-center justify-center space-y-3'><div class='h-16 w-16 rounded-2xl bg-zinc-50 flex items-center justify-center'><i class='fas fa-certificate text-2xl text-zinc-300'></i></div><div class='text-center'><p class='text-zinc-900 font-semibold'>Belum ada sertifikat diterbitkan</p><p class='text-zinc-500 text-xs mt-1'>Klik tombol Terbitkan Sertifikat untuk mengonfigurasikan sertifikat praktikum.</p></div></div>",
                        paginate: {
                            next: '<i class="fas fa-chevron-right text-[10px]"></i>',
                            previous: '<i class="fas fa-chevron-left text-[10px]"></i>'
                        }
                    },
                    columnDefs: [{
                        orderable: false,
                        targets: [0, 3, 6]
                    }]
                });

                $('#customSearch').on('keyup', function() {
                    table.search(this.value).draw();
                });

                $('#customLength').on('change', function() {
                    table.page.len($(this).val()).draw();
                });
            }
        });

        function confirmDelete(id, title) {
            Swal.fire({
                title: 'Hapus Arsip Sertifikat?',
                text: `Hapus konfigurasi sertifikat untuk praktikum "${title}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#71717a',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
@endpush
