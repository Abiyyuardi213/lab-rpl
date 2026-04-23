@extends('layouts.admin')

@section('title', 'Manajemen Penugasan')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 uppercase">MANAJEMEN PENUGASAN</h1>
                <p class="text-sm text-zinc-500 mt-1 italic">"Kelola soal penugasan berdasarkan jadwal praktikum atau secara umum."</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                    <span>/</span>
                    <span class="text-zinc-900 font-semibold">Penugasan</span>
                </div>
                <button onclick="document.getElementById('modal-penugasan').classList.remove('hidden')"
                    class="inline-flex h-9 items-center justify-center rounded-md bg-[#001f3f] px-4 py-2 text-sm font-medium text-white shadow hover:bg-[#002d5a] transition-colors whitespace-nowrap">
                    <i class="fas fa-plus mr-2 text-xs"></i>
                    Tambah Soal
                </button>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex items-center gap-1 p-1 bg-zinc-100 rounded-lg w-fit">
            <button onclick="switchTab('jadwal')" id="tab-jadwal"
                class="px-4 py-1.5 text-xs font-bold uppercase tracking-wider rounded-md transition-all bg-white text-zinc-900 shadow-sm border border-zinc-200">
                Berdasarkan Jadwal
            </button>
            <button onclick="switchTab('umum')" id="tab-umum"
                class="px-4 py-1.5 text-xs font-bold uppercase tracking-wider rounded-md transition-all text-zinc-500 hover:text-zinc-700">
                Soal Tanpa Jadwal ({{ $penugasansTanpaJadwal->count() }})
            </button>
        </div>

        <!-- Jadwal Table Container -->
        <div id="content-jadwal" class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden min-h-[500px]">
            <div class="p-6 pb-4 flex items-center justify-between gap-4 border-b border-zinc-100">
                <div class="flex items-center gap-2 flex-1">
                    <div class="relative max-w-sm w-full">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-xs"></i>
                        <input type="text" id="searchJadwal" placeholder="Cari jadwal atau praktikum..."
                            class="flex h-9 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 pl-9 text-sm shadow-sm transition-colors placeholder:text-zinc-500 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
                    </div>
                </div>
            </div>

            <!-- Table Jadwal -->
            <div class="overflow-x-auto">
                <table id="jadwalTable" class="w-full text-sm text-left">
                    <thead class="bg-zinc-50 border-b border-zinc-100 text-zinc-500 font-medium h-10">
                        <tr>
                            <th class="px-6 align-middle font-medium text-zinc-500 w-12 text-center text-[10px] uppercase tracking-wider">NO</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">Praktikum & Sesi</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">Modul</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">Waktu & Ruangan</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">Jumlah Soal</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-right text-[10px] uppercase tracking-wider">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 text-zinc-900">
                        @foreach ($jadwalPraktikums as $index => $j)
                            <tr class="hover:bg-zinc-50/50 transition-colors">
                                <td class="px-6 py-4 text-center text-zinc-500 font-medium">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-zinc-900 uppercase tracking-tight">{{ $j->praktikum->nama_praktikum ?? 'N/A' }}</span>
                                        <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider mt-0.5">{{ $j->sesi->nama_sesi ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-medium text-zinc-700 tracking-tight">{{ $j->judul_modul }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-black text-[#001f3f] uppercase tracking-widest">{{ \Carbon\Carbon::parse($j->tanggal)->format('d M Y') }}</span>
                                        <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider">{{ $j->waktu_mulai }} - {{ $j->waktu_selesai }} | {{ $j->ruangan ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full bg-zinc-100 text-zinc-900 font-bold text-[10px] border border-zinc-200">
                                            {{ $j->penugasans->count() }} Soal
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.penugasan.show', $j->id) }}"
                                        class="inline-flex h-8 items-center justify-center rounded-md bg-[#001f3f] px-4 text-[10px] font-bold uppercase tracking-wider text-white hover:bg-[#002d5a] transition-colors shadow-sm">
                                        Kelola Soal
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Umum Table Container -->
        <div id="content-umum" class="hidden rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden min-h-[500px]">
            <div class="p-6 pb-4 flex items-center justify-between gap-4 border-b border-zinc-100">
                <div class="flex items-center gap-2 flex-1">
                    <div class="relative max-w-sm w-full">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-xs"></i>
                        <input type="text" id="searchUmum" placeholder="Cari soal umum..."
                            class="flex h-9 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 pl-9 text-sm shadow-sm transition-colors placeholder:text-zinc-500 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
                    </div>
                </div>
            </div>

            <!-- Table Umum -->
            <div class="overflow-x-auto">
                <table id="umumTable" class="w-full text-sm text-left">
                    <thead class="bg-zinc-50 border-b border-zinc-100 text-zinc-500 font-medium h-10">
                        <tr>
                            <th class="px-6 align-middle font-medium text-zinc-500 w-12 text-center text-[10px] uppercase tracking-wider">NO</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">Kode NPM</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">Judul & Deskripsi</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">Praktikum & Sesi</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-right text-[10px] uppercase tracking-wider">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 text-zinc-900">
                        @foreach ($penugasansTanpaJadwal as $index => $p)
                            <tr class="hover:bg-zinc-50/50 transition-colors">
                                <td class="px-6 py-4 text-center text-zinc-500 font-medium">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-zinc-100 text-zinc-900 font-black text-xs border border-zinc-200">
                                        {{ $p->kode_akhir_npm }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col max-w-xs">
                                        <span class="font-bold text-zinc-900 tracking-tight">{{ $p->judul }}</span>
                                        <span class="text-[10px] text-zinc-500 line-clamp-1 mt-0.5">{{ $p->deskripsi }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-zinc-700 tracking-tight uppercase text-[11px]">{{ $p->praktikum->nama_praktikum ?? 'N/A' }}</span>
                                        <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider mt-0.5">{{ $p->sesi->nama_sesi ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button onclick="openEditModal('{{ $p->id }}', '{{ $p->praktikum_id }}', '{{ $p->sesi_id }}', '{{ $p->jadwal_praktikum_id }}', '{{ $p->kode_akhir_npm }}', '{{ addslashes($p->judul) }}', '{{ addslashes($p->deskripsi) }}')"
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-md text-zinc-500 hover:text-amber-600 hover:bg-amber-50 transition-colors">
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>
                                        <form id="delete-form-{{ $p->id }}" action="{{ route('admin.penugasan.destroy', $p->id) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="confirmDelete('{{ $p->id }}')"
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

    <!-- Modal: Tambah Penugasan -->
    <div id="modal-penugasan"
        class="fixed inset-0 z-[60] hidden bg-zinc-900/40 backdrop-blur-sm flex items-center justify-center p-4 transition-all duration-300">
        <div
            class="bg-white rounded-xl w-full max-w-lg overflow-hidden shadow-2xl border border-zinc-200 animate-in fade-in zoom-in duration-200">
            <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between bg-zinc-50/50">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-lg bg-[#001f3f] flex items-center justify-center text-white shadow-lg shadow-[#001f3f]/20">
                        <i class="fas fa-plus text-xs"></i>
                    </div>
                    <h3 class="font-bold text-zinc-900 uppercase tracking-tight">Tambah Soal</h3>
                </div>
                <button onclick="document.getElementById('modal-penugasan').classList.add('hidden')"
                    class="h-8 w-8 flex items-center justify-center rounded-lg hover:bg-zinc-100 text-zinc-400 transition-colors">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <form action="{{ route('admin.penugasan.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Praktikum</label>
                        <select name="praktikum_id" id="praktikum_id" required onchange="filterSessions(this.value, 'sesi_id', 'jadwal_praktikum_id')"
                            class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none">
                            <option value="">Pilih Praktikum</option>
                            @foreach ($praktikums as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_praktikum }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Sesi</label>
                        <select name="sesi_id" id="sesi_id" required onchange="filterJadwals(this.value, 'jadwal_praktikum_id')"
                            class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none">
                            <option value="">Pilih Sesi</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Jadwal Praktikum (Opsional)</label>
                    <select name="jadwal_praktikum_id" id="jadwal_praktikum_id"
                        class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none">
                        <option value="">Umum (Tanpa Jadwal)</option>
                    </select>
                    <p class="text-[10px] text-zinc-400 italic mt-1">* Kosongkan jika soal berlaku untuk seluruh praktikan di sesi tersebut tanpa melihat modul.</p>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Kode NPM</label>
                    <select name="kode_akhir_npm" required
                        class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none">
                        @foreach ($digitNpms as $digitNpm)
                            <option value="{{ $digitNpm->digit }}">{{ $digitNpm->label }} (Kode: {{ $digitNpm->digit }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Judul Soal</label>
                    <input type="text" name="judul" required placeholder="Contoh: Soal Praktikum 1"
                        class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none">
                </div>
                
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Instruksi</label>
                    <textarea name="deskripsi" rows="3" required placeholder="Tuliskan instruksi di sini..."
                        class="flex w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-2 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none"></textarea>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">File Soal (Opsional)</label>
                    <div class="relative group border-2 border-dashed border-zinc-200 rounded-xl p-5 transition-all hover:border-[#001f3f] hover:bg-zinc-50/50 flex flex-col items-center justify-center gap-3 cursor-pointer bg-zinc-50/50">
                        <input type="file" name="file_soal" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                        <i class="fas fa-file-upload text-xl text-zinc-400"></i>
                        <p class="text-[10px] text-zinc-400">Seret & Lepas atau Klik untuk pilih file</p>
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3">
                    <button type="button" onclick="document.getElementById('modal-penugasan').classList.add('hidden')"
                        class="inline-flex h-9 items-center justify-center rounded-md border border-zinc-200 bg-white px-4 text-xs font-bold text-zinc-600 transition-all hover:bg-zinc-50">
                        BATAL
                    </button>
                    <button type="submit"
                        class="inline-flex h-9 items-center justify-center rounded-md bg-[#001f3f] px-6 text-xs font-bold text-white shadow-lg shadow-[#001f3f]/20 transition-all hover:bg-[#002d5a]">
                        SIMPAN SOAL
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Edit Penugasan -->
    <div id="modal-edit-penugasan"
        class="fixed inset-0 z-[60] hidden bg-zinc-900/40 backdrop-blur-sm flex items-center justify-center p-4 transition-all duration-300">
        <div
            class="bg-white rounded-xl w-full max-w-lg overflow-hidden shadow-2xl border border-zinc-200 animate-in fade-in zoom-in duration-200">
            <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between bg-zinc-50/50">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-lg bg-amber-500 flex items-center justify-center text-white shadow-lg shadow-amber-500/20">
                        <i class="fas fa-edit text-xs"></i>
                    </div>
                    <h3 class="font-bold text-zinc-900 uppercase tracking-tight">Edit Penugasan</h3>
                </div>
                <button onclick="document.getElementById('modal-edit-penugasan').classList.add('hidden')"
                    class="h-8 w-8 flex items-center justify-center rounded-lg hover:bg-zinc-100 text-zinc-400 transition-colors">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <form id="form-edit-penugasan" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Jadwal Praktikum (Opsional)</label>
                    <select name="jadwal_praktikum_id" id="edit-jadwal-praktikum"
                        class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-amber-500/10 focus:border-amber-500 outline-none">
                        <option value="">Umum (Tanpa Jadwal)</option>
                        @foreach ($allJadwalPraktikums as $aj)
                            <option value="{{ $aj->id }}">{{ $aj->praktikum->nama_praktikum ?? 'N/A' }} - {{ $aj->judul_modul }} ({{ $aj->sesi->nama_sesi ?? 'N/A' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Kode NPM</label>
                    <select name="kode_akhir_npm" id="edit-kode-npm" required
                        class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-amber-500/10 focus:border-amber-500 outline-none">
                        @foreach ($digitNpms as $digitNpm)
                            <option value="{{ $digitNpm->digit }}">{{ $digitNpm->label }} (Kode: {{ $digitNpm->digit }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Judul Soal</label>
                    <input type="text" name="judul" id="edit-judul" required
                        class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-amber-500/10 focus:border-amber-500 outline-none">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Instruksi</label>
                    <textarea name="deskripsi" id="edit-deskripsi" rows="3" required
                        class="flex w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-2 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-amber-500/10 focus:border-amber-500 outline-none"></textarea>
                </div>
                
                <div class="pt-4 flex items-center justify-end gap-3">
                    <button type="button" onclick="document.getElementById('modal-edit-penugasan').classList.add('hidden')"
                        class="inline-flex h-9 items-center justify-center rounded-md border border-zinc-200 bg-white px-4 text-xs font-bold text-zinc-600 transition-all hover:bg-zinc-50">
                        BATAL
                    </button>
                    <button type="submit"
                        class="inline-flex h-9 items-center justify-center rounded-md bg-amber-600 px-6 text-xs font-bold text-white shadow-lg shadow-amber-600/20 transition-all hover:bg-amber-700">
                        SIMPAN PERUBAHAN
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
        const praktikums = @json($praktikums);
        const jadwalPraktikums = @json($allJadwalPraktikums);

        $(document).ready(function() {
            initTable('#jadwalTable', '#searchJadwal');
            initTable('#umumTable', '#searchUmum');
        });

        function initTable(tableId, searchId) {
            if ($(tableId).length > 0) {
                var table = $(tableId).DataTable({
                    dom: 't<"flex flex-col sm:flex-row items-center justify-between px-6 py-4 border-t border-zinc-100"ip>',
                    language: {
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        emptyTable: "<div class='py-20 flex flex-col items-center justify-center space-y-3'><div class='h-16 w-16 rounded-2xl bg-zinc-50 flex items-center justify-center'><i class='fas fa-folder-open text-2xl text-zinc-300'></i></div><div class='text-center'><p class='text-zinc-900 font-semibold'>Tidak ada data penugasan</p></div></div>",
                        paginate: {
                            next: '<i class="fas fa-chevron-right text-[10px]"></i>',
                            previous: '<i class="fas fa-chevron-left text-[10px]"></i>'
                        }
                    }
                });

                $(searchId).on('keyup', function() {
                    table.search(this.value).draw();
                });
            }
        }

        function switchTab(type) {
            const tabJadwal = document.getElementById('tab-jadwal');
            const tabUmum = document.getElementById('tab-umum');
            const contentJadwal = document.getElementById('content-jadwal');
            const contentUmum = document.getElementById('content-umum');

            if (type === 'jadwal') {
                tabJadwal.classList.add('bg-white', 'text-zinc-900', 'shadow-sm', 'border', 'border-zinc-200');
                tabJadwal.classList.remove('text-zinc-500');
                tabUmum.classList.remove('bg-white', 'text-zinc-900', 'shadow-sm', 'border', 'border-zinc-200');
                tabUmum.classList.add('text-zinc-500');
                contentJadwal.classList.remove('hidden');
                contentUmum.classList.add('hidden');
            } else {
                tabUmum.classList.add('bg-white', 'text-zinc-900', 'shadow-sm', 'border', 'border-zinc-200');
                tabUmum.classList.remove('text-zinc-500');
                tabJadwal.classList.remove('bg-white', 'text-zinc-900', 'shadow-sm', 'border', 'border-zinc-200');
                tabJadwal.classList.add('text-zinc-500');
                contentUmum.classList.remove('hidden');
                contentJadwal.classList.add('hidden');
            }
        }

        function filterSessions(praktikumId, sesiSelectId, jadwalSelectId) {
            const sesiSelect = document.getElementById(sesiSelectId);
            const jadwalSelect = document.getElementById(jadwalSelectId);
            
            sesiSelect.innerHTML = '<option value="">Pilih Sesi</option>';
            jadwalSelect.innerHTML = '<option value="">Umum (Tanpa Jadwal)</option>';

            if (praktikumId) {
                const praktikum = praktikums.find(p => p.id == praktikumId);
                if (praktikum && praktikum.sesis) {
                    praktikum.sesis.forEach(sesi => {
                        sesiSelect.innerHTML += `<option value="${sesi.id}">${sesi.nama_sesi}</option>`;
                    });
                }
            }
        }

        function filterJadwals(sesiId, jadwalSelectId) {
            const modal = document.querySelector(jadwalSelectId.includes('edit') ? '#modal-edit-penugasan' : '#modal-penugasan');
            const praktikumId = modal.querySelector('[name="praktikum_id"]').value;
            
            const jadwalSelect = document.getElementById(jadwalSelectId);
            jadwalSelect.innerHTML = '<option value="">Umum (Tanpa Jadwal)</option>';

            if (sesiId && praktikumId) {
                // Tampilkan jadwal yang spesifik untuk sesi ini ATAU jadwal yang berlaku untuk semua sesi di praktikum ini
                const filteredJadwals = jadwalPraktikums.filter(j => 
                    j.sesi_id == sesiId || (j.sesi_id == null && j.praktikum_id == praktikumId)
                );
                filteredJadwals.forEach(j => {
                    const infoSesi = j.sesi_id ? '' : ' [Semua Sesi]';
                    jadwalSelect.innerHTML += `<option value="${j.id}">${j.judul_modul}${infoSesi} (${j.tanggal})</option>`;
                });
            }
        }

        function openEditModal(id, praktikumId, sesiId, jadwalId, kodeNpm, judul, deskripsi) {
            const form = document.getElementById('form-edit-penugasan');
            form.action = `/admin/penugasan/${id}`;
            document.getElementById('edit-jadwal-praktikum').value = jadwalId || '';
            document.getElementById('edit-kode-npm').value = kodeNpm;
            document.getElementById('edit-judul').value = judul;
            document.getElementById('edit-deskripsi').value = deskripsi;
            
            document.getElementById('modal-edit-penugasan').classList.remove('hidden');
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Soal penugasan ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#001f3f',
                cancelButtonColor: '#f4f4f5',
                confirmButtonText: 'Ya, hapus!',
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
