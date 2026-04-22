@extends('layouts.admin')

@section('title', 'Detail Penugasan')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('aslab.tugas.index') }}" 
                    class="h-10 w-10 flex items-center justify-center rounded-xl bg-white border border-zinc-200 text-zinc-400 hover:text-zinc-900 hover:border-zinc-300 transition-all shadow-sm">
                    <i class="fas fa-chevron-left text-xs"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 uppercase">{{ $representative->judul }}</h1>
                    <p class="text-sm text-zinc-500 mt-1 italic">{{ $representative->pendaftaran->praktikum->nama_praktikum }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('aslab.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                <span>/</span>
                <a href="{{ route('aslab.tugas.index') }}" class="hover:text-zinc-900 transition-colors">Tugas</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Detail</span>
            </div>
        </div>

        <!-- Assignment Info Card -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2 rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#001f3f]"></span>
                        Informasi Penugasan
                    </h3>
                    <button onclick="openEditModal('{{ $representative->id }}', '{{ $representative->judul }}', '{{ $representative->due_date ? $representative->due_date->format('Y-m-d') : '' }}', '{{ addslashes($representative->deskripsi) }}')"
                        class="h-7 px-3 rounded-lg bg-zinc-50 border border-zinc-200 text-[10px] font-bold text-zinc-600 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-edit text-[9px]"></i>
                        Edit Penugasan
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest block mb-1">Deskripsi / Instruksi</label>
                        <p class="text-sm text-zinc-700 leading-relaxed">{{ $representative->deskripsi ?? 'Tidak ada deskripsi' }}</p>
                    </div>
                    <div class="flex flex-wrap gap-6">
                        @if($representative->due_date)
                        <div>
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest block mb-1">Batas Waktu</label>
                            <div class="flex items-center gap-2 text-zinc-900 font-bold text-sm">
                                <i class="far fa-calendar-alt text-[#001f3f]"></i>
                                {{ $representative->due_date->format('d F Y') }}
                            </div>
                        </div>
                        @endif
                        @if($representative->file_tugas)
                        <div>
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest block mb-1">Berkas Soal</label>
                            <a href="{{ asset('storage/' . $representative->file_tugas) }}" target="_blank"
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 hover:bg-[#001f3f] hover:text-white transition-all text-xs font-bold uppercase tracking-wider">
                                <i class="fas fa-file-download"></i>
                                Lihat Soal
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Stats Card -->
            <div class="rounded-xl border border-zinc-200 bg-zinc-900 p-6 text-white shadow-lg overflow-hidden relative group">
                <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:scale-110 transition-transform duration-500">
                    <i class="fas fa-users text-8xl"></i>
                </div>
                <h3 class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-6 relative">Statistik Pengumpulan</h3>
                <div class="grid grid-cols-2 gap-4 relative">
                    <div class="space-y-1">
                        <span class="text-3xl font-black tabular-nums">{{ $tugas->count() }}</span>
                        <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Total Mhs</p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-3xl font-black tabular-nums text-emerald-400">{{ $tugas->where('status', 'reviewed')->count() }}</span>
                        <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Dinilai</p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-3xl font-black tabular-nums text-amber-400">{{ $tugas->where('status', 'submitted')->count() }}</span>
                        <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Menunggu</p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-3xl font-black tabular-nums text-zinc-500">{{ $tugas->where('status', 'pending')->count() }}</span>
                        <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Belum Kumpul</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student List Table -->
        <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden min-h-[400px] flex flex-col">
            <div class="p-6 pb-4 flex items-center justify-between gap-4 border-b border-zinc-100">
                <h2 class="text-sm font-bold text-zinc-900 uppercase tracking-widest flex items-center gap-2">
                    <i class="fas fa-list text-[#001f3f]"></i>
                    Daftar Mahasiswa & Penilaian
                </h2>
                <div class="relative max-w-xs w-full">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-xs"></i>
                    <input type="text" id="customSearch" placeholder="Cari nama mahasiswa..."
                        class="flex h-9 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 pl-9 text-sm shadow-sm transition-colors placeholder:text-zinc-500 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
                </div>
            </div>

            <div class="overflow-x-auto flex-grow h-full">
                <table id="studentTable" class="w-full text-sm text-left">
                    <thead class="bg-zinc-50 border-b border-zinc-100 text-zinc-500 font-medium h-10">
                        <tr>
                            <th class="px-6 align-middle font-medium text-zinc-500 w-12 text-center text-[10px] uppercase tracking-wider">NO</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">Mahasiswa</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">Tugas Mahasiswa</th>
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
                                        <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider mt-0.5">{{ $t->pendaftaran->praktikan->npm }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($t->file_mahasiswa)
                                        <div class="flex items-center gap-2">
                                            <a href="{{ asset('storage/' . $t->file_mahasiswa) }}" target="_blank"
                                                class="inline-flex items-center gap-2 px-2 py-1 rounded bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all text-[10px] font-bold uppercase tracking-wider">
                                                <i class="fas fa-download"></i>
                                                Download Jawaban
                                            </a>
                                            <span class="text-[10px] text-zinc-400 italic">Dikumpul: {{ $t->updated_at->format('d M Y') }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-zinc-400 italic">Belum mengumpulkan</span>
                                    @endif
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
                                    <button onclick="openReviewModal('{{ $t->id }}', '{{ $t->nilai }}', '{{ $t->catatan_aslab }}', '{{ $t->status }}')"
                                        class="inline-flex items-center justify-center h-8 px-3 rounded-md bg-white border border-zinc-200 text-zinc-600 hover:text-emerald-600 hover:border-emerald-200 hover:bg-emerald-50 transition-all text-[10px] font-bold uppercase tracking-wider gap-2">
                                        <i class="fas fa-marker"></i>
                                        Beri Nilai
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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

    <!-- Modal: Edit Tugas (Bulk) -->
    <div id="modal-edit-tugas"
        class="fixed inset-0 z-[60] hidden bg-zinc-900/40 backdrop-blur-sm flex items-center justify-center p-4 transition-all duration-300">
        <div
            class="bg-white rounded-xl w-full max-w-lg overflow-hidden shadow-2xl border border-zinc-200 animate-in fade-in zoom-in duration-200">
            <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between bg-zinc-50/50">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-lg bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-600/20">
                        <i class="fas fa-edit text-xs"></i>
                    </div>
                    <h3 class="font-bold text-zinc-900 uppercase tracking-tight">Edit Penugasan</h3>
                </div>
                <button onclick="document.getElementById('modal-edit-tugas').classList.add('hidden')"
                    class="h-8 w-8 flex items-center justify-center rounded-lg hover:bg-zinc-100 text-zinc-400 transition-colors">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <form id="form-edit-tugas" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf @method('PUT')
                <input type="hidden" name="is_bulk" value="1">
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5 col-span-2 sm:col-span-1">
                        <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Judul Tugas</label>
                        <input type="text" id="edit-judul" name="judul" required
                            class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-blue-600/10 focus:border-blue-600 outline-none">
                    </div>
                    <div class="space-y-1.5 col-span-2 sm:col-span-1">
                        <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Deadline</label>
                        <input type="date" id="edit-due-date" name="due_date"
                            class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-blue-600/10 focus:border-blue-600 outline-none">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Instruksi</label>
                    <textarea id="edit-deskripsi" name="deskripsi" rows="3"
                        class="flex w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-2 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-blue-600/10 focus:border-blue-600 outline-none"></textarea>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Update File Soal (Opsional)</label>
                    <input type="file" name="file_tugas"
                        class="flex w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1.5 text-xs shadow-sm transition-all file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                    <p class="text-[9px] text-zinc-400 italic">Kosongkan jika tidak ingin mengubah file soal.</p>
                </div>
                
                <div class="pt-4 flex items-center justify-end gap-3">
                    <button type="button" onclick="document.getElementById('modal-edit-tugas').classList.add('hidden')"
                        class="inline-flex h-9 items-center justify-center rounded-md border border-zinc-200 bg-white px-4 text-xs font-bold text-zinc-600 transition-all hover:bg-zinc-50">
                        BATAL
                    </button>
                    <button type="submit"
                        class="inline-flex h-9 items-center justify-center rounded-md bg-blue-600 px-6 text-xs font-bold text-white shadow-lg shadow-blue-600/20 transition-all hover:bg-blue-700">
                        SIMPAN PERUBAHAN
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#studentTable').DataTable({
                dom: 't<"mt-auto flex flex-col sm:flex-row items-center justify-between px-6 py-4 border-t border-zinc-100 bg-white"ip>',
                language: {
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ mahasiswa",
                    paginate: {
                        next: '<i class="fas fa-chevron-right text-[10px]"></i>',
                        previous: '<i class="fas fa-chevron-left text-[10px]"></i>'
                    }
                }
            });

            $('#customSearch').on('keyup', function() {
                table.search(this.value).draw();
            });
        });

            document.getElementById('modal-review').classList.remove('hidden');
        }

        function openEditModal(id, judul, dueDate, deskripsi) {
            const form = document.getElementById('form-edit-tugas');
            form.action = `/aslab/tugas/${id}`;
            document.getElementById('edit-judul').value = judul;
            document.getElementById('edit-due-date').value = dueDate;
            document.getElementById('edit-deskripsi').value = deskripsi;
            document.getElementById('modal-edit-tugas').classList.remove('hidden');
        }

        // Close modal on click outside
        window.onclick = function(event) {
            const mReview = document.getElementById('modal-review');
            const mEdit = document.getElementById('modal-edit-tugas');
            if (event.target == mReview) mReview.classList.add('hidden');
            if (event.target == mEdit) mEdit.classList.add('hidden');
        }
    </script>

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
@endsection
