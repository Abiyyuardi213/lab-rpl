@extends('layouts.admin')

@section('title', 'Tugas Asistensi')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 uppercase">Tugas Asistensi</h1>
                <p class="text-sm text-zinc-500 mt-1 italic">"Berikan dan pantau tugas untuk mahasiswa bimbingan Anda."</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('aslab.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Tugas</span>
            </div>
        </div>

        <!-- Tugas Table Container -->
        <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden min-h-[500px]">
            <div class="p-6 pb-4 flex items-center justify-between gap-4 border-b border-zinc-100">
                <div class="flex items-center gap-2 flex-1">
                    <div class="relative max-w-sm w-full">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-xs"></i>
                        <input type="text" id="customSearch" placeholder="Cari data tugas..."
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
                    <button onclick="document.getElementById('modal-tugas').classList.remove('hidden')"
                        class="inline-flex h-9 items-center justify-center rounded-md bg-[#001f3f] px-4 py-2 text-sm font-medium text-white shadow hover:bg-[#002d5a] transition-colors whitespace-nowrap">
                        <i class="fas fa-plus mr-2 text-xs"></i>
                        Tambah Tugas
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table id="tugasTable" class="w-full text-sm text-left">
                    <thead class="bg-zinc-50 border-b border-zinc-100 text-zinc-500 font-medium h-10">
                        <tr>
                            <th class="px-6 align-middle font-medium text-zinc-500 w-12 text-center text-[10px] uppercase tracking-wider">NO</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">Mahasiswa & Praktikum</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">Tugas & Berkas</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">Batas Waktu</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-center text-[10px] uppercase tracking-wider">Status</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-center text-[10px] uppercase tracking-wider">Skor</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-right text-[10px] uppercase tracking-wider">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 text-zinc-900">
                        @foreach ($tugas as $index => $t)
                            <tr class="hover:bg-zinc-50/50 transition-colors">
                                <td class="px-6 py-4 text-center text-zinc-500 font-medium">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-zinc-900 uppercase tracking-tight">{{ $t->pendaftaran->praktikan->user->name ?? 'Mahasiswa' }}</span>
                                        <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider mt-0.5">{{ $t->pendaftaran->praktikum->nama_praktikum }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="font-medium text-zinc-700 tracking-tight">{{ $t->judul }}</span>
                                        @if ($t->file_tugas)
                                            <a href="{{ asset('storage/' . $t->file_tugas) }}" target="_blank"
                                                class="w-7 h-7 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center hover:bg-[#001f3f] hover:text-white transition-all shadow-sm"
                                                title="Unduh Soal">
                                                <i class="fas fa-file-download text-[10px]"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2 text-zinc-500 text-[10px] font-bold uppercase tracking-widest whitespace-nowrap">
                                        <i class="far fa-calendar-alt text-zinc-300"></i>
                                        {{ $t->due_date ? $t->due_date->format('d M Y') : '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $statusBase = 'inline-flex items-center px-3 py-1 rounded-full text-[9px] font-bold border uppercase tracking-wider leading-none ';
                                        $statusMap = [
                                            'pending' => $statusBase . 'bg-zinc-50 text-zinc-400 border-zinc-200',
                                            'submitted' => $statusBase . 'bg-amber-50 text-amber-600 border-amber-100',
                                            'reviewed' => $statusBase . 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                        ];
                                    @endphp
                                    <span class="{{ $statusMap[$t->status] ?? $statusMap['pending'] }}">
                                        {{ $t->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-base font-black tabular-nums tracking-tighter {{ $t->nilai >= 80 ? 'text-emerald-600' : ($t->nilai ? 'text-amber-600' : 'text-zinc-200') }}">
                                        {{ $t->nilai ?? '??' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        @if ($t->file_mahasiswa)
                                            <a href="{{ asset('storage/' . $t->file_mahasiswa) }}" target="_blank"
                                                class="w-8 h-8 rounded-md text-zinc-500 hover:text-indigo-600 hover:bg-indigo-50 flex items-center justify-center transition-colors"
                                                title="Download Jawaban">
                                                <i class="fas fa-download text-xs"></i>
                                            </a>
                                        @endif
                                        <button onclick="openReviewModal('{{ $t->id }}', '{{ $t->nilai }}', '{{ $t->catatan_aslab }}', '{{ $t->status }}')"
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-md text-zinc-500 hover:text-emerald-600 hover:bg-emerald-50 transition-colors"
                                            title="Beri Nilai">
                                            <i class="fas fa-marker text-xs"></i>
                                        </button>
                                        <form id="delete-form-{{ $t->id }}"
                                            action="{{ route('aslab.tugas.destroy', $t->id) }}" method="POST"
                                            class="inline">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="confirmDelete('{{ $t->id }}')"
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

    <!-- Modal: Tambah Tugas -->
    <div id="modal-tugas"
        class="fixed inset-0 z-[60] hidden bg-zinc-900/40 backdrop-blur-sm flex items-center justify-center p-4 transition-all duration-300">
        <div
            class="bg-white rounded-xl w-full max-w-lg overflow-hidden shadow-2xl border border-zinc-200 animate-in fade-in zoom-in duration-200">
            <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between bg-zinc-50/50">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-lg bg-[#001f3f] flex items-center justify-center text-white shadow-lg shadow-[#001f3f]/20">
                        <i class="fas fa-plus text-xs"></i>
                    </div>
                    <h3 class="font-bold text-zinc-900 uppercase tracking-tight">Beri Tugas Baru</h3>
                </div>
                <button onclick="document.getElementById('modal-tugas').classList.add('hidden')"
                    class="h-8 w-8 flex items-center justify-center rounded-lg hover:bg-zinc-100 text-zinc-400 transition-colors">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <form action="{{ route('aslab.tugas.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Mata Praktikum</label>
                    <select name="praktikum_id" required
                        class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none">
                        <option value="">-- Pilih Praktikum --</option>
                        @foreach ($praktikums as $p)
                            <option value="{{ $p->id }}">{{ $p->nama_praktikum }} ({{ $p->kode_praktikum }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5 col-span-2 sm:col-span-1">
                        <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Judul Tugas</label>
                        <input type="text" name="judul" required placeholder="Contoh: Modul 1"
                            class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none">
                    </div>
                    <div class="space-y-1.5 col-span-2 sm:col-span-1">
                        <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Deadline</label>
                        <input type="date" name="due_date"
                            class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Instruksi</label>
                    <textarea name="deskripsi" rows="3" placeholder="Instruksi pengerjaan..."
                        class="flex w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-2 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none"></textarea>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">File Soal (Opsional)</label>
                    <input type="file" name="file_tugas"
                        class="flex w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1.5 text-xs shadow-sm transition-all file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-[#001f3f] file:text-white hover:file:bg-[#002d5a]">
                </div>
                <div class="pt-4 flex items-center justify-end gap-3">
                    <button type="button" onclick="document.getElementById('modal-tugas').classList.add('hidden')"
                        class="inline-flex h-9 items-center justify-center rounded-md border border-zinc-200 bg-white px-4 text-xs font-bold text-zinc-600 transition-all hover:bg-zinc-50">
                        BATAL
                    </button>
                    <button type="submit"
                        class="inline-flex h-9 items-center justify-center rounded-md bg-[#001f3f] px-6 text-xs font-bold text-white shadow-lg shadow-[#001f3f]/20 transition-all hover:bg-[#002d5a]">
                        KIRIM TUGAS
                    </button>
                </div>
            </form>
        </div>
    </div>
        </div>
    </div>

    <!-- Modal: Review Tugas -->
    <div id="modal-review"
        class="fixed inset-0 z-[60] hidden bg-zinc-900/40 backdrop-blur-sm flex items-center justify-center p-4 transition-all duration-300">
        <div
            class="bg-white rounded-xl w-full max-w-lg overflow-hidden shadow-2xl border border-zinc-200 animate-in fade-in zoom-in duration-200">
            <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between bg-zinc-50/50">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-lg bg-emerald-500 flex items-center justify-center text-white shadow-lg shadow-emerald-500/20">
                        <i class="fas fa-marker text-xs"></i>
                    </div>
                    <h3 class="font-bold text-zinc-900 uppercase tracking-tight">Review & Nilai</h3>
                </div>
                <button onclick="document.getElementById('modal-review').classList.add('hidden')"
                    class="h-8 w-8 flex items-center justify-center rounded-lg hover:bg-zinc-100 text-zinc-400 transition-colors">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <form id="form-review" method="POST" class="p-6 space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5 col-span-1">
                        <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Skor (0-100)</label>
                        <input type="number" id="review-nilai" name="nilai" min="0" max="100"
                            class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none tabular-nums">
                    </div>
                    <div class="space-y-1.5 col-span-1">
                        <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Status</label>
                        <select id="review-status" name="status" required
                            class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none uppercase tracking-widest font-bold">
                            <option value="pending">Pending</option>
                            <option value="submitted">Submitted</option>
                            <option value="reviewed">Reviewed</option>
                        </select>
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Catatan Aslab</label>
                    <textarea id="review-catatan" name="catatan_aslab" rows="3"
                        placeholder="Berikan feedback atau instruksi revisi..."
                        class="flex w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-2 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none"></textarea>
                </div>
                <div class="pt-4">
                    <button type="submit"
                        class="w-full h-10 bg-emerald-600 text-white rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-500/20 active:scale-[0.98]">
                        Simpan Review
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

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.current) {
            background: #fafafa !important;
            border-color: #d4d4d8 !important;
            color: #001f3f !important;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
        const praktikums = @json($praktikums);

        $(document).ready(function() {
            if ($('#tugasTable').length > 0) {
                var table = $('#tugasTable').DataTable({
                    dom: 't<"flex flex-col sm:flex-row items-center justify-between px-6 py-4 border-t border-zinc-100"ip>',
                    language: {
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        emptyTable: "<div class='py-20 flex flex-col items-center justify-center space-y-3'><div class='h-16 w-16 rounded-2xl bg-zinc-50 flex items-center justify-center'><i class='fas fa-folder-open text-2xl text-zinc-300'></i></div><div class='text-center'><p class='text-zinc-900 font-semibold'>Tidak ada data tugas tersedia</p><p class='text-zinc-500 text-xs mt-1'>Silakan klik tombol 'Tambah Tugas' untuk memberikan penugasan baru.</p></div></div>",
                        paginate: {
                            next: '<i class="fas fa-chevron-right text-[10px]"></i>',
                            previous: '<i class="fas fa-chevron-left text-[10px]"></i>'
                        }
                    },
                    columnDefs: [{
                        orderable: false,
                        targets: [6]
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

        function openReviewModal(id, nilai, catatan, status) {
            const form = document.getElementById('form-review');
            form.action = `/aslab/tugas/${id}`;

            document.getElementById('review-nilai').value = nilai !== 'null' ? nilai : '';
            document.getElementById('review-catatan').value = catatan !== 'null' ? catatan : '';
            document.getElementById('review-status').value = status;

            document.getElementById('modal-review').classList.remove('hidden');
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Tugas ini akan dihapus secara permanen dari sistem!",
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
            });
        }

        @if (session('success'))
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });

            Toast.fire({
                icon: 'success',
                title: '{{ session('success') }}'
            });
        @endif

        // Close modal on click outside
        window.onclick = function(event) {
            const mTugas = document.getElementById('modal-tugas');
            const mReview = document.getElementById('modal-review');
            if (event.target == mTugas) mTugas.classList.add('hidden');
            if (event.target == mReview) mReview.classList.add('hidden');
        }
    </script>
@endsection
