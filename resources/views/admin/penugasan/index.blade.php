@extends('layouts.admin')

@section('title', 'Manajemen Penugasan')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 uppercase">MANAJEMEN PENUGASAN</h1>
                <p class="text-sm text-zinc-500 mt-1 italic">"Kelola pembagian soal praktikum berdasarkan digit terakhir NPM praktikan."</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Penugasan</span>
            </div>
        </div>

        <!-- Penugasan Table Container -->
        <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden min-h-[500px]">
            <div class="p-6 pb-4 flex items-center justify-between gap-4 border-b border-zinc-100">
                    <div class="flex items-center gap-2 flex-1">
                        <div class="relative max-w-sm w-full">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-xs"></i>
                            <input type="text" id="customSearch" placeholder="Cari penugasan..."
                                class="flex h-9 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 pl-9 text-sm shadow-sm transition-colors placeholder:text-zinc-500 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
                        </div>
                        <select id="filterSesi"
                            class="h-9 rounded-md border border-zinc-200 bg-white px-3 py-1 text-[10px] font-bold uppercase tracking-wider shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
                            <option value="">Semua Sesi</option>
                            @php
                                $uniqueSesis = $praktikums->flatMap->sesis->pluck('nama_sesi')->unique()->sort();
                            @endphp
                            @foreach ($uniqueSesis as $sesi)
                                <option value="{{ $sesi }}">{{ $sesi }}</option>
                            @endforeach
                        </select>
                        <select id="filterNpm"
                            class="h-9 rounded-md border border-zinc-200 bg-white px-3 py-1 text-[10px] font-bold uppercase tracking-wider shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
                            <option value="">Semua NPM</option>
                            @foreach ($digitNpms as $digitNpm)
                                <option value="{{ $digitNpm->digit }}">{{ $digitNpm->label }} (Kode: {{ $digitNpm->digit }})</option>
                            @endforeach
                        </select>
                    </div>
                <div class="flex items-center gap-2">
                    <select id="customLength"
                        class="h-9 rounded-md border border-zinc-200 bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
                        <option value="10">10 data</option>
                        <option value="25">25 data</option>
                        <option value="50">50 data</option>
                    </select>
                    <button onclick="document.getElementById('modal-penugasan').classList.remove('hidden')"
                        class="inline-flex h-9 items-center justify-center rounded-md bg-[#001f3f] px-4 py-2 text-sm font-medium text-white shadow hover:bg-[#002d5a] transition-colors whitespace-nowrap">
                        <i class="fas fa-plus mr-2 text-xs"></i>
                        Tambah Soal
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table id="penugasanTable" class="w-full text-sm text-left">
                    <thead class="bg-zinc-50 border-b border-zinc-100 text-zinc-500 font-medium h-10">
                        <tr>
                            <th class="px-6 align-middle font-medium text-zinc-500 w-12 text-center text-[10px] uppercase tracking-wider">NO</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">Praktikum & Sesi</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">Kode NPM</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">Judul Soal</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">Jadwal Sesi</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">Praktikan Terdaftar</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-right text-[10px] uppercase tracking-wider">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 text-zinc-900">
                        @foreach ($penugasans as $index => $p)
                            @php
                                $registeredStudents = $p->sesi?->pendaftarans ?? collect();
                                $verifiedStudents = $registeredStudents->where('status', 'verified');
                            @endphp
                            <tr class="hover:bg-zinc-50/50 transition-colors">
                                <td class="px-6 py-4 text-center text-zinc-500 font-medium">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-zinc-900 uppercase tracking-tight">{{ $p->praktikum->nama_praktikum }}</span>
                                        <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider mt-0.5">{{ $p->sesi->nama_sesi }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-zinc-100 text-zinc-900 font-black text-xs border border-zinc-200">
                                        {{ $p->kode_akhir_npm }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="font-medium text-zinc-700 tracking-tight">{{ $p->judul }}</span>
                                        @if ($p->file_soal)
                                            <a href="{{ asset('storage/' . $p->file_soal) }}" target="_blank"
                                                class="w-7 h-7 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center hover:bg-[#001f3f] hover:text-white transition-all shadow-sm"
                                                title="Unduh Soal">
                                                <i class="fas fa-file-download text-[10px]"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-black text-[#001f3f] uppercase tracking-widest">{{ $p->sesi->hari }}</span>
                                        <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider">{{ $p->sesi->jam_mulai }} - {{ $p->sesi->jam_selesai }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex flex-col">
                                            <span class="font-black text-zinc-900">{{ $registeredStudents->count() }} Praktikan</span>
                                            <span class="text-[10px] text-emerald-600 font-bold uppercase tracking-wider">{{ $verifiedStudents->count() }} Terverifikasi</span>
                                        </div>
                                        <button type="button" onclick="openPraktikanModal('{{ $p->id }}')"
                                            class="inline-flex h-8 items-center justify-center rounded-md border border-zinc-200 bg-white px-3 text-[10px] font-bold uppercase tracking-wider text-[#001f3f] hover:bg-[#001f3f] hover:text-white transition-colors">
                                            Lihat
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button onclick="openEditModal('{{ $p->id }}', '{{ $p->kode_akhir_npm }}', '{{ addslashes($p->judul) }}', '{{ addslashes($p->deskripsi) }}')"
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-md text-zinc-500 hover:text-amber-600 hover:bg-amber-50 transition-colors">
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>
                                        <form id="delete-form-{{ $p->id }}"
                                            action="{{ route('admin.penugasan.destroy', $p->id) }}" method="POST"
                                            class="inline">
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

    @foreach ($penugasans as $p)
        @php
            $registeredStudents = $p->sesi?->pendaftarans ?? collect();
        @endphp
        <div id="modal-praktikan-{{ $p->id }}"
            class="fixed inset-0 z-[60] hidden bg-zinc-900/40 backdrop-blur-sm flex items-center justify-center p-4 transition-all duration-300">
            <div
                class="bg-white rounded-xl w-full max-w-6xl max-h-[90vh] overflow-hidden shadow-2xl border border-zinc-200 animate-in fade-in zoom-in duration-200 flex flex-col">
                <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between bg-zinc-50/50 shrink-0">
                    <div>
                        <h3 class="font-bold text-zinc-900 uppercase tracking-tight">Praktikan Terdaftar</h3>
                        <p class="text-xs text-zinc-500 mt-1">
                            {{ $p->praktikum->nama_praktikum }} - {{ $p->sesi->nama_sesi }}
                        </p>
                    </div>
                    <button type="button" onclick="closePraktikanModal('{{ $p->id }}')"
                        class="h-8 w-8 flex items-center justify-center rounded-lg hover:bg-zinc-100 text-zinc-400 transition-colors">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto">
                    @if ($registeredStudents->isEmpty())
                        <div class="py-14 text-center">
                            <div class="mx-auto h-14 w-14 rounded-xl bg-zinc-50 flex items-center justify-center mb-3">
                                <i class="fas fa-users text-xl text-zinc-300"></i>
                            </div>
                            <p class="font-semibold text-zinc-900">Belum ada praktikan di sesi ini</p>
                            <p class="text-xs text-zinc-500 mt-1">Praktikan yang mendaftar ke sesi ini akan muncul di sini.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto border border-zinc-100 rounded-xl">
                            <table class="w-full min-w-[1100px] text-sm text-left">
                                <thead class="bg-zinc-50 text-zinc-500 border-b border-zinc-100">
                                    <tr>
                                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider w-12 text-center">No</th>
                                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider">Nama</th>
                                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider">NPM</th>
                                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider">Dosen & Kelas</th>
                                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider">Aslab</th>
                                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider">Soal Diterima</th>
                                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider">Edit Soal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-100">
                                    @foreach ($registeredStudents->sortBy(fn($item) => $item->praktikan?->user?->name)->values() as $studentIndex => $student)
                                        @php
                                            $studentNpm = $student->praktikan?->npm ?? '';
                                            $studentLastDigit = is_numeric(substr($studentNpm, -1)) ? (int) substr($studentNpm, -1) : null;
                                            $defaultPenugasan = $studentLastDigit !== null
                                                ? $p->sesi->penugasans->firstWhere('kode_akhir_npm', $studentLastDigit)
                                                : null;
                                            $customPenugasan = $student->penugasanOverride?->penugasan;
                                            $currentPenugasan = $customPenugasan ?? $defaultPenugasan;
                                        @endphp
                                        <tr class="hover:bg-zinc-50/50">
                                            <td class="px-4 py-3 text-center text-zinc-500 font-medium">{{ $studentIndex + 1 }}</td>
                                            <td class="px-4 py-3">
                                                <div class="font-semibold text-zinc-900">{{ $student->praktikan?->user?->name ?? '-' }}</div>
                                                <div class="text-[10px] text-zinc-500">{{ $student->praktikan?->user?->email ?? '-' }}</div>
                                            </td>
                                            <td class="px-4 py-3 font-mono text-xs text-zinc-700">{{ $student->praktikan?->npm ?? '-' }}</td>
                                            <td class="px-4 py-3">
                                                <div class="font-medium text-zinc-700">{{ $student->dosen_pengampu ?? '-' }}</div>
                                                <div class="text-[10px] text-zinc-500 uppercase">{{ $student->kelas ?? '-' }} - {{ $student->asal_kelas_mata_kuliah ?? '-' }}</div>
                                            </td>
                                            <td class="px-4 py-3 text-zinc-700">{{ $student->aslab?->user?->name ?? 'Belum dibagi' }}</td>
                                            <td class="px-4 py-3">
                                                <div class="font-semibold text-zinc-900">{{ $currentPenugasan?->judul ?? 'Belum ada soal' }}</div>
                                                <div class="text-[10px] font-bold uppercase tracking-wider {{ $customPenugasan ? 'text-amber-600' : 'text-zinc-400' }}">
                                                    {{ $customPenugasan ? 'Soal khusus' : 'Default digit ' . ($studentLastDigit ?? '-') }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                @php
                                                    $statusClass = match ($student->status) {
                                                        'verified' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                                        'rejected' => 'bg-rose-50 text-rose-700 border-rose-100',
                                                        default => 'bg-amber-50 text-amber-700 border-amber-100',
                                                    };
                                                @endphp
                                                <span class="inline-flex items-center rounded-md border px-2 py-1 text-[10px] font-bold uppercase tracking-wider {{ $statusClass }}">
                                                    {{ $student->status }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <form action="{{ route('admin.penugasan.praktikan-soal.update', $student->id) }}" method="POST" class="flex items-center gap-2">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="penugasan_id"
                                                        class="h-9 w-52 rounded-md border border-zinc-200 bg-white px-3 text-xs shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
                                                        <option value="">
                                                            Default digit {{ $studentLastDigit ?? '-' }}{{ $defaultPenugasan ? ' - ' . $defaultPenugasan->judul : '' }}
                                                        </option>
                                                        @foreach ($p->sesi->penugasans->sortBy('kode_akhir_npm') as $availablePenugasan)
                                                            <option value="{{ $availablePenugasan->id }}"
                                                                @selected($customPenugasan?->id === $availablePenugasan->id)>
                                                                Kode {{ $availablePenugasan->kode_akhir_npm }} - {{ $availablePenugasan->judul }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <button type="submit"
                                                        class="inline-flex h-9 items-center justify-center rounded-md bg-[#001f3f] px-3 text-[10px] font-bold uppercase tracking-wider text-white hover:bg-[#002d5a] transition-colors">
                                                        Simpan
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach

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
                    <h3 class="font-bold text-zinc-900 uppercase tracking-tight">Tambah Soal Baru</h3>
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
                        <select name="praktikum_id" onchange="filterSessions(this.value)" required
                            class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none">
                            <option value="">-- Pilih Praktikum --</option>
                            @foreach ($praktikums as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_praktikum }}</option>
                            @endforeach
                        </select>
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
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Sesi</label>
                    <select name="sesi_id" id="select-sesi" required
                        class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none">
                        <option value="">-- Pilih Sesi --</option>
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
                    <div id="drop-zone" class="relative group border-2 border-dashed border-zinc-200 rounded-xl p-5 transition-all hover:border-[#001f3f] hover:bg-zinc-50/50 flex flex-col items-center justify-center gap-3 cursor-pointer bg-zinc-50/50 overflow-hidden">
                        <input type="file" name="file_soal" id="file_soal_tambah"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                        <div class="flex flex-col items-center gap-2 pointer-events-none text-center">
                            <div class="h-12 w-12 rounded-2xl bg-white border border-zinc-100 shadow-sm flex items-center justify-center group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                                <i class="fas fa-file-upload text-xl text-zinc-400 group-hover:text-[#001f3f]"></i>
                            </div>
                            <div class="space-y-0.5">
                                <p class="text-[11px] font-bold text-zinc-500 group-hover:text-zinc-900 transition-colors uppercase tracking-widest" id="file-label-tambah">Seret & Lepas File ke Sini</p>
                                <p class="text-[10px] text-zinc-400">Atau klik untuk pilih dari folder</p>
                            </div>
                            <p class="text-[9px] text-zinc-400 font-medium italic pt-1">PDF, DOCX, ZIP, RAR, atau Gambar (Maks 5MB)</p>
                        </div>
                        <!-- Progress indicator (hidden by default) -->
                        <div id="progress-tambah" class="absolute inset-0 bg-white/90 backdrop-blur-sm z-30 hidden items-center justify-center flex-col gap-3">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#001f3f]"></div>
                            <p class="text-[10px] font-bold text-[#001f3f] uppercase tracking-widest">Mengambil file...</p>
                        </div>
                    </div>
                    <input type="hidden" name="file_url" id="file_url_tambah">
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
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">File Soal (Opsional)</label>
                    <div id="drop-zone-edit" class="relative group border-2 border-dashed border-zinc-200 rounded-xl p-5 transition-all hover:border-amber-500 hover:bg-amber-50/50 flex flex-col items-center justify-center gap-3 cursor-pointer bg-zinc-50/50 overflow-hidden">
                        <input type="file" name="file_soal" id="file_soal_edit"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                        <div class="flex flex-col items-center gap-2 pointer-events-none text-center">
                            <div class="h-12 w-12 rounded-2xl bg-white border border-zinc-100 shadow-sm flex items-center justify-center group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                                <i class="fas fa-file-edit text-xl text-zinc-400 group-hover:text-amber-500"></i>
                            </div>
                            <div class="space-y-0.5">
                                <p class="text-[11px] font-bold text-zinc-500 group-hover:text-zinc-900 transition-colors uppercase tracking-widest" id="file-label-edit">Seret & Lepas File ke Sini</p>
                                <p class="text-[10px] text-zinc-400">Atau klik untuk pilih dari folder</p>
                            </div>
                            <p class="text-[9px] text-zinc-400 font-medium italic pt-1">Kosongkan jika tidak ingin mengubah file</p>
                        </div>
                        <!-- Progress indicator (hidden by default) -->
                        <div id="progress-edit" class="absolute inset-0 bg-white/90 backdrop-blur-sm z-30 hidden items-center justify-center flex-col gap-3">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-amber-500"></div>
                            <p class="text-[10px] font-bold text-amber-600 uppercase tracking-widest">Mengambil file...</p>
                        </div>
                    </div>
                    <input type="hidden" name="file_url" id="file_url_edit">
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

        function filterSessions(praktikumId) {
            const sesiSelect = document.getElementById('select-sesi');
            sesiSelect.innerHTML = '<option value="">-- Pilih Sesi --</option>';

            if (!praktikumId) return;

            const praktikum = praktikums.find(p => p.id === praktikumId);
            if (praktikum && praktikum.sesis) {
                praktikum.sesis.forEach(sesi => {
                    const option = document.createElement('option');
                    option.value = sesi.id;
                    option.textContent = `${sesi.nama_sesi} (${sesi.hari}, ${sesi.jam_mulai} - ${sesi.jam_selesai})`;
                    sesiSelect.appendChild(option);
                });
            }
        }

        $(document).ready(function() {
            if ($('#penugasanTable').length > 0) {
                var table = $('#penugasanTable').DataTable({
                    dom: 't<"flex flex-col sm:flex-row items-center justify-between px-6 py-4 border-t border-zinc-100"ip>',
                    language: {
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        emptyTable: "<div class='py-20 flex flex-col items-center justify-center space-y-3'><div class='h-16 w-16 rounded-2xl bg-zinc-50 flex items-center justify-center'><i class='fas fa-folder-open text-2xl text-zinc-300'></i></div><div class='text-center'><p class='text-zinc-900 font-semibold'>Tidak ada data penugasan tersedia</p><p class='text-zinc-500 text-xs mt-1'>Silakan tambah penugasan sesi baru untuk melihat daftar di sini.</p></div></div>",
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

                $('#filterSesi').on('change', function() {
                    table.column(1).search(this.value).draw();
                });

                $('#filterNpm').on('change', function() {
                    // Use exact match for NPM digit
                    table.column(2).search(this.value ? '^\\s*' + this.value + '\\s*$' : '', true, false).draw();
                });
            }

            // Setup drop zones
            setupDropZone('drop-zone', 'file_soal_tambah', 'file-label-tambah', 'progress-tambah');
            setupDropZone('drop-zone-edit', 'file_soal_edit', 'file-label-edit', 'progress-edit');
        });

        function setupDropZone(dropZoneId, inputId, labelId, progressId) {
            const dropZone = document.getElementById(dropZoneId);
            const input = document.getElementById(inputId);
            const label = document.getElementById(labelId);
            const progress = document.getElementById(progressId);
            const hiddenUrlInput = document.getElementById(inputId.replace('file_soal', 'file_url'));

            if (!dropZone || !input) return;

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, e => {
                    e.preventDefault();
                    e.stopPropagation();
                }, false);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => {
                    dropZone.classList.add('border-zinc-400', 'bg-zinc-100/80');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => {
                    dropZone.classList.remove('border-zinc-400', 'bg-zinc-100/80');
                }, false);
            });

            dropZone.addEventListener('drop', async (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;

                if (files && files.length > 0) {
                    input.files = files;
                    label.textContent = files[0].name;
                    if (hiddenUrlInput) hiddenUrlInput.value = '';
                } else {
                    // Try to handle dragging from external websites (URL)
                    const url = dt.getData('text/uri-list') || dt.getData('text/plain');
                    if (url && (url.startsWith('http://') || url.startsWith('https://'))) {
                        // Reset file input
                        input.value = '';
                        
                        if (hiddenUrlInput) {
                            hiddenUrlInput.value = url;
                            const fileName = url.split('/').pop().split('?')[0] || 'file-external';
                            label.textContent = "URL: " + fileName;
                        }

                        // Try to fetch for validation/better UI if CORS allows
                        try {
                            progress.style.display = 'flex';
                            const response = await fetch(url);
                            if (response.ok) {
                                const blob = await response.blob();
                                const fileName = url.split('/').pop().split('?')[0] || 'file-external';
                                const file = new File([blob], fileName, { type: blob.type || 'application/octet-stream' });
                                
                                const container = new DataTransfer();
                                container.items.add(file);
                                input.files = container.files;
                                label.textContent = file.name;
                                if (hiddenUrlInput) hiddenUrlInput.value = ''; // Use file instead
                            }
                        } catch (err) {
                            console.log("CORS blocked fetch, will use URL for backend download");
                        } finally {
                            progress.style.display = 'none';
                        }
                    }
                }
            }, false);

            input.addEventListener('change', () => {
                if (input.files.length > 0) {
                    label.textContent = input.files[0].name;
                    if (hiddenUrlInput) hiddenUrlInput.value = '';
                }
            });
        }

        function openEditModal(id, kodeNpm, judul, deskripsi) {
            const form = document.getElementById('form-edit-penugasan');
            form.action = `/admin/penugasan/${id}`;
            document.getElementById('edit-kode-npm').value = kodeNpm;
            document.getElementById('edit-judul').value = judul;
            document.getElementById('edit-deskripsi').value = deskripsi;
            
            // Reset file input, label, and hidden URL
            document.getElementById('file_soal_edit').value = '';
            document.getElementById('file_url_edit').value = '';
            document.getElementById('file-label-edit').textContent = "Seret & Lepas File ke Sini";
            
            document.getElementById('modal-edit-penugasan').classList.remove('hidden');
        }

        function openPraktikanModal(id) {
            document.getElementById('modal-praktikan-' + id).classList.remove('hidden');
        }

        function closePraktikanModal(id) {
            document.getElementById('modal-praktikan-' + id).classList.add('hidden');
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
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        @endif
        
        @if (session('error'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: '{{ session('error') }}',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        @endif

        // Close modal on click outside
        window.onclick = function(event) {
            if (event.target.id === 'modal-penugasan') document.getElementById('modal-penugasan').classList.add('hidden');
            if (event.target.id === 'modal-edit-penugasan') document.getElementById('modal-edit-penugasan').classList.add('hidden');
            if (event.target.id && event.target.id.startsWith('modal-praktikan-')) event.target.classList.add('hidden');
        }
    </script>
@endsection
