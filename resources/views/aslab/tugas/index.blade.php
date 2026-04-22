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
        <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden min-h-[500px] flex flex-col">
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
                        class="inline-flex h-9 items-center justify-center rounded-md border border-zinc-200 bg-white px-4 py-2 text-sm font-medium text-zinc-900 shadow-sm hover:bg-zinc-50 transition-colors whitespace-nowrap">
                        <i class="fas fa-plus mr-2 text-xs"></i>
                        Tambah Tugas
                    </button>
                    <button onclick="document.getElementById('modal-langsung').classList.remove('hidden')"
                        class="inline-flex h-9 items-center justify-center rounded-md bg-[#001f3f] px-4 py-2 text-sm font-medium text-white shadow hover:bg-[#002d5a] transition-colors whitespace-nowrap">
                        <i class="fas fa-marker mr-2 text-xs"></i>
                        Penilaian Langsung
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto flex-grow h-full">
                <table id="tugasTable" class="w-full text-sm text-left">
                    <thead class="bg-zinc-50 border-b border-zinc-100 text-zinc-500 font-medium h-10">
                        <tr>
                            <th class="px-6 align-middle font-medium text-zinc-500 w-12 text-center text-[10px] uppercase tracking-wider">NO</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">Mata Praktikum</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">Penugasan & Berkas</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">Batas Waktu</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-center text-[10px] uppercase tracking-wider">Progress Penilaian</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-right text-[10px] uppercase tracking-wider">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 text-zinc-900">
                        @foreach ($tugas as $index => $t)
                            <tr class="hover:bg-zinc-50/50 transition-colors">
                                <td class="px-6 py-4 text-center text-zinc-500 font-medium">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-zinc-900 uppercase tracking-tight">{{ $t->pendaftaran->praktikum->nama_praktikum }}</span>
                                        <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider mt-0.5">{{ $t->pendaftaran->praktikum->kode_praktikum }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex flex-col">
                                            <span class="font-medium text-zinc-700 tracking-tight">{{ $t->judul }}</span>
                                            @if($t->deskripsi)
                                                <span class="text-[10px] text-zinc-400 truncate max-w-[200px]">{{ $t->deskripsi }}</span>
                                            @endif
                                        </div>
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
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1.5 container">
                                        <div class="flex items-center justify-between text-[9px] font-black uppercase tracking-tighter">
                                            <span class="text-emerald-600">{{ $t->total_reviewed }} Dinilai</span>
                                            <span class="text-zinc-400">{{ $t->total_mahasiswa }} Total</span>
                                        </div>
                                        <div class="w-full h-1.5 bg-zinc-100 rounded-full overflow-hidden flex">
                                            <div class="h-full bg-emerald-500 transition-all duration-500" style="width: {{ ($t->total_reviewed / $t->total_mahasiswa) * 100 }}%"></div>
                                            <div class="h-full bg-amber-400 transition-all duration-500" style="width: {{ ($t->total_submitted / $t->total_mahasiswa) * 100 }}%"></div>
                                        </div>
                                        <div class="flex items-center gap-2 text-[8px] font-bold text-zinc-400 uppercase tracking-widest">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                            {{ $t->total_submitted }} Menunggu
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button onclick="openEditModal('{{ $t->id }}', '{{ $t->judul }}', '{{ $t->due_date ? $t->due_date->format('Y-m-d') : '' }}', '{{ addslashes($t->deskripsi) }}')"
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-md text-zinc-500 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                                            title="Edit Penugasan">
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>
                                        <a href="{{ route('aslab.tugas.show', $t->id) }}"
                                            class="inline-flex items-center justify-center h-8 px-3 rounded-md text-[10px] font-bold uppercase tracking-wider text-zinc-600 hover:text-[#001f3f] hover:bg-zinc-100 transition-colors gap-2"
                                            title="Lihat Detail & Nilai Mahasiswa">
                                            <i class="fas fa-users text-xs"></i>
                                            Detail
                                        </a>
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

    <!-- Modal: Penilaian Langsung (Tanpa Tugas) -->
    <div id="modal-langsung"
        class="fixed inset-0 z-[60] hidden bg-zinc-900/40 backdrop-blur-sm flex items-center justify-center p-4 transition-all duration-300">
        <div
            class="bg-white rounded-xl w-full max-w-lg overflow-hidden shadow-2xl border border-zinc-200 animate-in fade-in zoom-in duration-200">
            <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between bg-zinc-50/50">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-lg bg-[#001f3f] flex items-center justify-center text-white shadow-lg shadow-[#001f3f]/20">
                        <i class="fas fa-bolt text-xs"></i>
                    </div>
                    <h3 class="font-bold text-zinc-900 uppercase tracking-tight">Penilaian Langsung</h3>
                </div>
                <button onclick="document.getElementById('modal-langsung').classList.add('hidden')"
                    class="h-8 w-8 flex items-center justify-center rounded-lg hover:bg-zinc-100 text-zinc-400 transition-colors">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <form action="{{ route('aslab.tugas.store-direct') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Pilih Mahasiswa</label>
                    <select name="pendaftaran_id" required
                        class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none">
                        <option value="">-- Pilih Mahasiswa --</option>
                        @foreach($students->groupBy('praktikum_id') as $praktikumId => $pendaftarans)
                            <optgroup label="{{ $pendaftarans->first()->praktikum->nama_praktikum }}">
                                @foreach($pendaftarans as $p)
                                    <option value="{{ $p->id }}">{{ $p->praktikan->user->name }} ({{ $p->praktikan->npm }})</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5 col-span-2 sm:col-span-1">
                        <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Modul / Materi</label>
                        <input type="text" name="judul" required placeholder="Contoh: Modul 1"
                            class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none">
                    </div>
                    <div class="space-y-1.5 col-span-2 sm:col-span-1">
                        <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Skor (0-100)</label>
                        <input type="number" name="nilai" required min="0" max="100" placeholder="0-100"
                            class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none font-black text-center text-lg tabular-nums text-[#001f3f]">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Catatan Aslab (Opsional)</label>
                    <textarea name="catatan_aslab" rows="3" placeholder="Feedback untuk mahasiswa..."
                        class="flex w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-2 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none"></textarea>
                </div>
                <p class="text-[10px] text-zinc-400 italic">
                    * Penilaian ini akan langsung tersimpan dengan status <span class="font-bold text-emerald-600">Reviewed</span> tanpa perlu pengumpulan file dari mahasiswa.
                </p>
                <div class="pt-4 flex items-center justify-end gap-3">
                    <button type="button" onclick="document.getElementById('modal-langsung').classList.add('hidden')"
                        class="inline-flex h-9 items-center justify-center rounded-md border border-zinc-200 bg-white px-4 text-xs font-bold text-zinc-600 transition-all hover:bg-zinc-50">
                        BATAL
                    </button>
                    <button type="submit"
                        class="inline-flex h-9 items-center justify-center rounded-md bg-[#002d5a] px-6 text-xs font-bold text-white shadow-lg shadow-zinc-200 transition-all hover:bg-[#001f3f]">
                        SIMPAN NILAI
                    </button>
                </div>
            </form>
        </div>
    </div>
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
    </style>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
        const praktikums = @json($praktikums);

        $(document).ready(function() {
            if ($('#tugasTable').length > 0) {
                var table = $('#tugasTable').DataTable({
                    dom: 't<"mt-auto flex flex-col sm:flex-row items-center justify-between px-6 py-4 border-t border-zinc-100 bg-white"ip>',
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
                        targets: [5]
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

        function openEditModal(id, judul, dueDate, deskripsi) {
            const form = document.getElementById('form-edit-tugas');
            form.action = `/aslab/tugas/${id}`;
            document.getElementById('edit-judul').value = judul;
            document.getElementById('edit-due-date').value = dueDate;
            document.getElementById('edit-deskripsi').value = deskripsi;
            document.getElementById('modal-edit-tugas').classList.remove('hidden');
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
            const mLangsung = document.getElementById('modal-langsung');
            const mEdit = document.getElementById('modal-edit-tugas');
            if (event.target == mTugas) mTugas.classList.add('hidden');
            if (event.target == mLangsung) mLangsung.classList.add('hidden');
            if (event.target == mEdit) mEdit.classList.add('hidden');
        }
    </script>
@endsection
