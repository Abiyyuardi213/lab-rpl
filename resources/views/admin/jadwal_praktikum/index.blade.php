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
                <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                    <!-- Dropdown Filter Praktikum -->
                    <form method="GET" action="{{ route('admin.jadwal-praktikum.index') }}" class="flex items-center">
                        <select name="praktikum_id" onchange="this.form.submit()"
                            class="h-9 rounded-md border border-zinc-200 bg-white px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950 cursor-pointer font-medium text-zinc-700">
                            <option value="">-- Semua Mata Praktikum --</option>
                            @foreach ($praktikums as $p)
                                <option value="{{ $p->id }}" {{ isset($selectedPraktikum) && $selectedPraktikum == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_praktikum }} ({{ $p->kode_praktikum }})
                                </option>
                            @endforeach
                        </select>
                    </form>

                    <select id="customLength"
                        class="h-9 rounded-md border border-zinc-200 bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
                        <option value="10">10 data</option>
                        <option value="25">25 data</option>
                        <option value="50">50 data</option>
                        <option value="100">100 data</option>
                    </select>
                    <button onclick="openAddModal()"
                        class="inline-flex h-9 items-center justify-center rounded-md bg-[#001f3f] px-4 py-2 text-sm font-medium text-white shadow hover:bg-[#002d5a] transition-colors whitespace-nowrap">
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
                                    <div class="flex flex-col gap-1">
                                        <span
                                            class="px-2 py-0.5 rounded-full bg-zinc-100 text-zinc-600 text-[10px] font-black uppercase tracking-wider border border-zinc-200 w-fit">
                                            {{ $jadwal->judul_modul }}
                                        </span>
                                        @if($jadwal->is_susulan)
                                            <span class="px-2 py-0.5 rounded-full bg-purple-50 text-purple-700 text-[10px] font-bold uppercase tracking-wider border border-purple-200 w-fit">
                                                <i class="fas fa-user-clock mr-1"></i>Susulan ({{ $jadwal->pesertaSusulans->count() }} Peserta)
                                            </span>
                                        @elseif($jadwal->sesi)
                                            <span class="text-[10px] text-indigo-600 font-bold uppercase tracking-tight">
                                                <i class="fas fa-users mr-1"></i>{{ $jadwal->sesi->nama_sesi }}
                                            </span>
                                        @else
                                            <span class="text-[10px] text-zinc-400 font-medium uppercase tracking-tight" title="Data historis (Legacy)">
                                                <i class="fas fa-history mr-1"></i>Semua Sesi (Legacy)
                                            </span>
                                        @endif
                                    </div>
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
                                        @if($jadwal->is_susulan)
                                            <button onclick='openPesertaSusulanModal(@json($jadwal))'
                                                class="inline-flex items-center justify-center h-8 w-8 rounded-md text-purple-600 hover:text-purple-800 hover:bg-purple-50 transition-colors"
                                                title="Kelola Peserta Susulan">
                                                <i class="fas fa-users-cog text-xs"></i>
                                            </button>
                                        @endif
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
                class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-zinc-200">
                <div class="px-6 py-6 sm:px-8">
                    <div class="flex items-center justify-between mb-6">
                        <div class="space-y-1">
                            <h3 class="text-xl font-bold tracking-tight text-zinc-900" id="modalTitle">Tambah Jadwal Modul Multi-Sesi</h3>
                            <p class="text-xs text-zinc-500" id="modalSubtitle">Jadwalkan modul ke seluruh sesi praktikum sekaligus secara terstruktur.</p>
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

                        <!-- Field Praktikum, Judul Modul, dan Ruangan -->
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-zinc-700 uppercase tracking-tight">Mata Praktikum <span class="text-rose-500">*</span></label>
                                    <select name="praktikum_id" id="modal_praktikum_id" required onchange="handlePraktikumChange()"
                                        class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-2 text-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none font-medium text-zinc-900">
                                        <option value="">-- Pilih Mata Praktikum --</option>
                                        @foreach ($praktikums as $p)
                                            <option value="{{ $p->id }}">{{ $p->nama_praktikum }} ({{ $p->kode_praktikum }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="space-y-1.5" id="topRuanganContainer">
                                    <label class="text-xs font-bold text-zinc-700 uppercase tracking-tight" id="labelModalRuangan">Ruangan Default / Bersama (Opsional)</label>
                                    <div class="flex gap-2">
                                        <input type="text" name="ruangan" id="modal_ruangan" placeholder="Contoh: Lab RPL / Zoom"
                                            class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-2 text-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none font-medium">
                                        <button type="button" onclick="applyDefaultRoomToAll()" id="btnApplyRoom"
                                            class="h-10 px-3 py-2 text-xs font-semibold text-zinc-700 bg-zinc-100 hover:bg-zinc-200 border border-zinc-200 rounded-lg transition-colors whitespace-nowrap">
                                            <i class="fas fa-copy mr-1 text-[10px]"></i> Samakan
                                        </button>
                                    </div>
                                    <p class="text-[10px] text-zinc-400" id="hintModalRuangan">Atau tentukan ruangan berbeda secara spesifik di tiap sesi di bawah.</p>
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-zinc-700 uppercase tracking-tight">Judul Modul <span class="text-rose-500">*</span></label>
                                <input type="text" name="judul_modul" id="modal_judul_modul"
                                    placeholder="Contoh: Modul 1: Pengenalan Database Relasional" required
                                    class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-2 text-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none font-medium">
                            </div>

                            <div class="p-3 bg-purple-50/70 border border-purple-200 rounded-lg flex items-center justify-between" id="susulanToggleContainer">
                                <div class="flex items-center gap-2.5">
                                    <input type="checkbox" name="is_susulan" id="is_susulan_checkbox" value="1" onchange="toggleSusulanMode(this)"
                                        class="h-4 w-4 rounded border-purple-300 text-purple-600 focus:ring-purple-500 cursor-pointer">
                                    <div>
                                        <label for="is_susulan_checkbox" class="text-xs font-bold text-purple-900 cursor-pointer block leading-tight">
                                            Jadwal Khusus Susulan (Inval)
                                        </label>
                                        <p class="text-[10px] text-purple-600 font-medium">Jadwal pengganti bagi praktikan yang berhalangan hadir di sesi reguler.</p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-purple-200/60 text-purple-800">
                                    Khusus Inval
                                </span>
                            </div>
                        </div>

                        <!-- SECTION TAMBAH: Multi-Sesi Matrix -->
                        <div id="multiSessionSection" class="space-y-3 pt-2 border-t border-zinc-100">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                <div>
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-900 flex items-center gap-1.5">
                                        <i class="fas fa-users-cog text-indigo-600"></i> Penetapan Jadwal per Sesi
                                    </h4>
                                    <p class="text-[11px] text-zinc-500 mt-0.5">Tiap sesi mendapatkan waktu spesifik sesuai jam regulernya.</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="date" id="quickDateInput"
                                        class="h-8 rounded-md border border-zinc-200 px-2 py-1 text-xs bg-zinc-50 focus:bg-white outline-none">
                                    <button type="button" onclick="applyQuickDate()"
                                        class="h-8 inline-flex items-center px-2.5 py-1 text-xs font-semibold text-zinc-700 bg-zinc-100 hover:bg-zinc-200 border border-zinc-200 rounded-md transition-colors whitespace-nowrap">
                                        <i class="fas fa-copy mr-1 text-[10px]"></i> Samakan Tanggal
                                    </button>
                                </div>
                            </div>

                            <div id="sessionListContainer" class="space-y-2 max-h-64 overflow-y-auto pr-1">
                                <div class="p-4 text-center text-xs text-zinc-400 bg-zinc-50 rounded-lg border border-dashed border-zinc-200">
                                    Pilih mata praktikum di atas untuk memuat daftar sesi.
                                </div>
                            </div>
                        </div>

                        <!-- SECTION EDIT: Single Sesi Editor -->
                        <div id="singleSessionSection" class="space-y-4 pt-2 border-t border-zinc-100 hidden">
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-zinc-700 uppercase tracking-tight">Sesi Praktikum <span class="text-rose-500">*</span></label>
                                <select name="sesi_id" id="modal_edit_sesi_id"
                                    class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-2 text-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none font-medium text-zinc-900">
                                    <!-- Options populated dynamically -->
                                </select>
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-zinc-700 uppercase tracking-tight">Tanggal Pelaksanaan <span class="text-rose-500">*</span></label>
                                <input type="date" name="tanggal" id="modal_edit_tanggal"
                                    class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-2 text-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none font-medium">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-zinc-700 uppercase tracking-tight">Waktu Mulai <span class="text-rose-500">*</span></label>
                                    <input type="time" name="waktu_mulai" id="modal_edit_waktu_mulai"
                                        class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-2 text-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none font-medium">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-zinc-700 uppercase tracking-tight">Waktu Selesai <span class="text-rose-500">*</span></label>
                                    <input type="time" name="waktu_selesai" id="modal_edit_waktu_selesai"
                                        class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-2 text-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none font-medium">
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-100">
                            <button type="button" onclick="closeModal()"
                                class="inline-flex h-10 items-center justify-center rounded-lg border border-zinc-200 bg-white px-6 text-sm font-bold text-zinc-600 transition-all hover:bg-zinc-50 active:scale-95">
                                Batal
                            </button>
                            <button type="submit" id="btnSubmitModal"
                                class="inline-flex h-10 items-center justify-center rounded-lg bg-[#001f3f] px-8 text-sm font-bold text-white shadow-lg shadow-[#001f3f]/20 transition-all hover:bg-[#002d5a] active:scale-95 uppercase tracking-widest">
                                Simpan Jadwal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Kelola Peserta Susulan -->
    <div id="pesertaSusulanModal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="fixed inset-0 bg-zinc-950/40 backdrop-blur-sm transition-opacity" onclick="closePesertaSusulanModal()"></div>

            <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-zinc-200">
                <div class="px-6 py-6 sm:px-8 space-y-6">
                    <div class="flex items-center justify-between border-b border-zinc-100 pb-4">
                        <div class="space-y-1">
                            <h3 class="text-lg font-bold tracking-tight text-zinc-900" id="susulanModalTitle">Kelola Peserta Susulan</h3>
                            <p class="text-xs text-zinc-500" id="susulanModalSubtitle">Daftarkan mahasiswa yang berhalangan hadir ke jadwal susulan ini.</p>
                        </div>
                        <button onclick="closePesertaSusulanModal()"
                            class="rounded-md h-8 w-8 inline-flex items-center justify-center text-zinc-400 hover:text-zinc-600 hover:bg-zinc-50 transition-colors">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>

                    <!-- Form Tambah Peserta -->
                    <form id="formTambahPesertaSusulan" method="POST" class="space-y-4 p-4 rounded-xl bg-purple-50/50 border border-purple-100">
                        @csrf
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-zinc-700 uppercase tracking-tight">Pilih Praktikan Terdaftar <span class="text-rose-500">*</span></label>
                            <select name="pendaftaran_ids[]" id="susulanStudentSelect" multiple required
                                class="flex w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-xs transition-all focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 outline-none font-medium text-zinc-900 min-h-[90px]">
                                <!-- Options dynamically inserted -->
                            </select>
                            <p class="text-[10px] text-zinc-400">Tekan tombol Ctrl / Command untuk memilih beberapa mahasiswa sekaligus.</p>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-zinc-700 uppercase tracking-tight">Catatan Dispensasi / Izin (Opsional)</label>
                            <input type="text" name="catatan" id="susulanCatatanInput" placeholder="Contoh: Sakit surat dokter / dispensasi kampus"
                                class="flex h-9 w-full rounded-lg border border-zinc-200 bg-white px-3 py-1.5 text-xs focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600 outline-none font-medium">
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="inline-flex h-9 items-center justify-center rounded-lg bg-purple-700 px-4 text-xs font-bold text-white shadow-sm hover:bg-purple-800 transition-colors">
                                <i class="fas fa-user-plus mr-1.5 text-[10px]"></i>
                                Tambah ke Jadwal Susulan
                            </button>
                        </div>
                    </form>

                    <!-- Daftar Peserta Terdaftar -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-700 flex items-center justify-between">
                            <span>Daftar Peserta Terdaftar</span>
                            <span id="susulanCountBadge" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700">0 Peserta</span>
                        </h4>

                        <div class="border border-zinc-200 rounded-lg overflow-hidden max-h-56 overflow-y-auto">
                            <table class="w-full text-xs text-left">
                                <thead class="bg-zinc-50 border-b border-zinc-100 text-zinc-500 uppercase text-[9px] font-bold">
                                    <tr>
                                        <th class="px-4 py-2">NPM</th>
                                        <th class="px-4 py-2">Nama</th>
                                        <th class="px-4 py-2">Catatan</th>
                                        <th class="px-4 py-2 text-center">Status</th>
                                        <th class="px-4 py-2 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="susulanPesertaTableBody" class="divide-y divide-zinc-100">
                                    <!-- Populated via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
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

        const allPraktikums = @json($praktikums);
        let modalMode = 'add';

        function openAddModal() {
            modalMode = 'add';
            $('#modalTitle').text('Tambah Jadwal Modul Multi-Sesi');
            $('#modalSubtitle').text('Jadwalkan modul ke seluruh sesi praktikum sekaligus secara terstruktur.');
            $('#btnSubmitModal').text('Simpan Jadwal Multi-Sesi');
            $('#formMethod').val('POST');
            $('#jadwalForm').attr('action', "{{ route('admin.jadwal-praktikum.store') }}");
            $('#jadwalForm')[0].reset();
            $('#susulanToggleContainer').removeClass('hidden');
            $('#is_susulan_checkbox').prop('checked', false);
            $('#modal_edit_sesi_id').closest('.space-y-1.5').removeClass('hidden');

            $('#labelModalRuangan').text('Ruangan Default / Bersama (Opsional)');
            $('#btnApplyRoom').removeClass('hidden');
            $('#hintModalRuangan').removeClass('hidden');

            $('#multiSessionSection').removeClass('hidden');
            $('#singleSessionSection').addClass('hidden');

            enableMultiSessionInputs(true);

            $('#sessionListContainer').html(`
                <div class="p-4 text-center text-xs text-zinc-400 bg-zinc-50 rounded-lg border border-dashed border-zinc-200">
                    Pilih mata praktikum di atas untuk memuat daftar sesi.
                </div>
            `);

            $('#jadwalModal').removeClass('hidden');
        }

        function openEditModal(jadwal) {
            modalMode = 'edit';
            $('#modalTitle').text('Edit Jadwal Praktikum');
            $('#modalSubtitle').text('Perbarui informasi tanggal, jam, atau sesi untuk jadwal spesifik ini.');
            $('#btnSubmitModal').text('Perbarui Jadwal');
            $('#formMethod').val('PATCH');
            $('#jadwalForm').attr('action', `/administrator/jadwal-praktikum/${jadwal.id}`);
            $('#susulanToggleContainer').removeClass('hidden');
            $('#is_susulan_checkbox').prop('checked', Boolean(jadwal.is_susulan));

            $('#labelModalRuangan').text('Ruangan / Tempat');
            $('#btnApplyRoom').addClass('hidden');
            $('#hintModalRuangan').addClass('hidden');

            $('#modal_praktikum_id').val(jadwal.praktikum_id);
            $('#modal_judul_modul').val(jadwal.judul_modul);
            $('#modal_ruangan').val(jadwal.ruangan);

            $('#multiSessionSection').addClass('hidden');
            $('#singleSessionSection').removeClass('hidden');

            enableMultiSessionInputs(false);

            populateEditSessionSelect(jadwal.praktikum_id, jadwal.sesi_id);

            $('#modal_edit_tanggal').val(jadwal.tanggal);
            $('#modal_edit_waktu_mulai').val(jadwal.waktu_mulai ? jadwal.waktu_mulai.substring(0, 5) : '08:00');
            $('#modal_edit_waktu_selesai').val(jadwal.waktu_selesai ? jadwal.waktu_selesai.substring(0, 5) : '10:00');
            toggleSusulanMode(document.getElementById('is_susulan_checkbox'));

            $('#jadwalModal').removeClass('hidden');
        }

        function handlePraktikumChange() {
            const praktikumId = $('#modal_praktikum_id').val();
            if (modalMode === 'add') {
                renderAddSessionList(praktikumId);
            } else {
                populateEditSessionSelect(praktikumId, null);
            }
        }

        function renderAddSessionList(praktikumId) {
            const container = $('#sessionListContainer');
            if (!praktikumId) {
                container.html(`
                    <div class="p-4 text-center text-xs text-zinc-400 bg-zinc-50 rounded-lg border border-dashed border-zinc-200">
                        Pilih mata praktikum di atas untuk memuat daftar sesi.
                    </div>
                `);
                return;
            }

            const praktikum = allPraktikums.find(p => p.id == praktikumId);
            const sesis = (praktikum && praktikum.sesis) ? praktikum.sesis : [];

            if (sesis.length === 0) {
                container.html(`
                    <div class="p-4 text-center text-xs text-amber-700 bg-amber-50 rounded-lg border border-amber-200">
                        Praktikum ini belum memiliki sesi. Tambahkan sesi terlebih dahulu di menu <strong>Praktikum</strong>.
                    </div>
                `);
                return;
            }

            let html = '';
            sesis.forEach((sesi, idx) => {
                const startTime = (sesi.jam_mulai || '08:00').substring(0, 5);
                const endTime = (sesi.jam_selesai || '10:00').substring(0, 5);
                const hari = sesi.hari || 'Hari Bebas';

                html += `
                    <div class="p-3 rounded-lg border border-zinc-200 bg-zinc-50/70 hover:bg-zinc-50 transition-colors session-row" id="sessionRow_${idx}">
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="sessions[${idx}][enabled]" value="1" checked
                                    onchange="toggleSessionRow(${idx})"
                                    class="h-4 w-4 rounded border-zinc-300 text-[#001f3f] focus:ring-[#001f3f]">
                                <span class="text-xs font-bold text-zinc-800">${sesi.nama_sesi}</span>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-zinc-200 text-zinc-700">${hari}</span>
                            </label>
                            <span class="text-[10px] text-zinc-400 font-mono">Default: ${startTime} - ${endTime}</span>
                        </div>
                        <input type="hidden" name="sessions[${idx}][sesi_id]" value="${sesi.id}">
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-2" id="sessionInputs_${idx}">
                            <div>
                                <label class="text-[10px] font-bold text-zinc-500 uppercase">Tanggal</label>
                                <input type="date" name="sessions[${idx}][tanggal]" required
                                    class="session-date-input flex h-8 w-full rounded border border-zinc-200 bg-white px-2 py-1 text-xs focus:ring-1 focus:ring-[#001f3f] outline-none">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-zinc-500 uppercase">Waktu Mulai</label>
                                <input type="time" name="sessions[${idx}][waktu_mulai]" value="${startTime}" required
                                    class="flex h-8 w-full rounded border border-zinc-200 bg-white px-2 py-1 text-xs focus:ring-1 focus:ring-[#001f3f] outline-none">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-zinc-500 uppercase">Waktu Selesai</label>
                                <input type="time" name="sessions[${idx}][waktu_selesai]" value="${endTime}" required
                                    class="flex h-8 w-full rounded border border-zinc-200 bg-white px-2 py-1 text-xs focus:ring-1 focus:ring-[#001f3f] outline-none">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-zinc-500 uppercase">Ruangan Sesi</label>
                                <input type="text" name="sessions[${idx}][ruangan]" placeholder="Lab RPL / Zoom"
                                    class="session-room-input flex h-8 w-full rounded border border-zinc-200 bg-white px-2 py-1 text-xs focus:ring-1 focus:ring-[#001f3f] outline-none">
                            </div>
                        </div>
                    </div>
                `;
            });

            container.html(html);
        }

        function toggleSessionRow(idx) {
            const chk = $(`input[name="sessions[${idx}][enabled]"]`);
            const inputsContainer = $(`#sessionInputs_${idx}`);
            if (chk.is(':checked')) {
                inputsContainer.find('input').prop('disabled', false);
                $(`#sessionRow_${idx}`).removeClass('opacity-50');
            } else {
                inputsContainer.find('input').prop('disabled', true);
                $(`#sessionRow_${idx}`).addClass('opacity-50');
            }
        }

        function applyQuickDate() {
            const val = $('#quickDateInput').val();
            if (!val) {
                Swal.fire({
                    icon: 'info',
                    title: 'Pilih Tanggal',
                    text: 'Pilih tanggal pada input di samping tombol sebelum menerapkan ke semua sesi.',
                    confirmButtonColor: '#001f3f'
                });
                return;
            }
            $('.session-date-input').val(val);
        }

        function applyDefaultRoomToAll() {
            const val = $('#modal_ruangan').val();
            if (!val) {
                Swal.fire({
                    icon: 'info',
                    title: 'Isi Ruangan',
                    text: 'Ketik nama ruangan pada kolom Ruangan Default di atas sebelum menerapkan ke semua sesi.',
                    confirmButtonColor: '#001f3f'
                });
                return;
            }
            $('.session-room-input').val(val);
        }

        function populateEditSessionSelect(praktikumId, selectedSesiId) {
            const select = $('#modal_edit_sesi_id');
            select.empty();

            const praktikum = allPraktikums.find(p => p.id == praktikumId);
            const sesis = (praktikum && praktikum.sesis) ? praktikum.sesis : [];

            if (sesis.length === 0) {
                select.append('<option value="">-- Tidak ada sesi --</option>');
                return;
            }

            select.append('<option value="">-- Pilih Sesi --</option>');
            sesis.forEach(s => {
                const isSelected = (selectedSesiId && s.id == selectedSesiId) ? 'selected' : '';
                select.append(`<option value="${s.id}" ${isSelected}>${s.nama_sesi} (${s.hari} ${s.jam_mulai ? s.jam_mulai.substring(0, 5) : ''})</option>`);
            });
        }

        function enableMultiSessionInputs(isMulti) {
            if (isMulti) {
                $('#singleSessionSection').find('input, select').prop('disabled', true);
                $('#multiSessionSection').find('input').prop('disabled', false);
            } else {
                $('#multiSessionSection').find('input').prop('disabled', true);
                $('#singleSessionSection').find('input, select').prop('disabled', false);
            }
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
                    form.action = `/administrator/jadwal-praktikum/${id}`;
                    form.innerHTML = `
                        @csrf
                        @method('DELETE')
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            })
        }

        function toggleSusulanMode(chk) {
            const isChecked = $(chk).is(':checked');
            const isEdit = $('#formMethod').val() === 'PATCH';
            if (isChecked) {
                $('#multiSessionSection').addClass('hidden');
                $('#singleSessionSection').removeClass('hidden');
                $('#modal_edit_sesi_id').closest('.space-y-1.5').addClass('hidden');
                $('#modal_edit_sesi_id').prop('required', false);
                if (isEdit) {
                    $('#modal_edit_sesi_id').val('');
                }
                enableMultiSessionInputs(false);
                $('#btnSubmitModal').text(isEdit ? 'Perbarui Jadwal Susulan' : 'SIMPAN JADWAL SUSULAN');
            } else {
                $('#modal_edit_sesi_id').closest('.space-y-1.5').removeClass('hidden');
                $('#modal_edit_sesi_id').prop('required', true);
                if (!isEdit) {
                    $('#multiSessionSection').removeClass('hidden');
                    $('#singleSessionSection').addClass('hidden');
                    enableMultiSessionInputs(true);
                    $('#btnSubmitModal').text('Simpan Jadwal Multi-Sesi');
                } else {
                    $('#btnSubmitModal').text('Perbarui Jadwal');
                }
            }
        }

        let activeSusulanJadwal = null;

        function openPesertaSusulanModal(jadwal) {
            activeSusulanJadwal = jadwal;
            $('#susulanModalTitle').text(`Kelola Peserta Susulan: ${jadwal.judul_modul}`);
            const namaPraktikum = jadwal.praktikum ? jadwal.praktikum.nama_praktikum : 'Praktikum';
            $('#susulanModalSubtitle').text(`${namaPraktikum} • Ruang: ${jadwal.ruangan || 'TBA'} • ${jadwal.tanggal}`);

            const formAction = `/administrator/jadwal-praktikum/${jadwal.id}/peserta-susulan`;
            $('#formTambahPesertaSusulan').attr('action', formAction);

            const praktikum = allPraktikums.find(p => p.id == jadwal.praktikum_id);
            const pendaftarans = (praktikum && praktikum.pendaftarans) ? praktikum.pendaftarans : [];
            const existingPesertaPendaftaranIds = (jadwal.peserta_susulans || []).map(p => p.pendaftaran_id);

            const select = $('#susulanStudentSelect');
            select.empty();

            const eligibleStudents = pendaftarans.filter(p => !existingPesertaPendaftaranIds.includes(p.id));

            if (eligibleStudents.length === 0) {
                select.append('<option value="" disabled>-- Semua praktikan sudah terdaftar di susulan ini --</option>');
            } else {
                eligibleStudents.forEach(p => {
                    const npm = p.praktikan ? p.praktikan.npm : '-';
                    const name = (p.praktikan && p.praktikan.user) ? p.praktikan.user.name : 'Mahasiswa';
                    const sesi = p.sesi ? p.sesi.nama_sesi : 'Sesi Umum';
                    select.append(`<option value="${p.id}">[${npm}] ${name} (${sesi})</option>`);
                });
            }

            renderPesertaSusulanTable(jadwal);
            $('#pesertaSusulanModal').removeClass('hidden');
        }

        function closePesertaSusulanModal() {
            $('#pesertaSusulanModal').addClass('hidden');
        }

        function renderPesertaSusulanTable(jadwal) {
            const tbody = $('#susulanPesertaTableBody');
            tbody.empty();
            const list = jadwal.peserta_susulans || [];
            $('#susulanCountBadge').text(`${list.length} Peserta`);

            if (list.length === 0) {
                tbody.html(`
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-zinc-400 italic">
                            Belum ada praktikan yang didaftarkan ke jadwal susulan ini.
                        </td>
                    </tr>
                `);
                return;
            }

            list.forEach(p => {
                const praktikan = p.pendaftaran ? p.pendaftaran.praktikan : null;
                const user = praktikan ? praktikan.user : null;
                const npm = praktikan ? praktikan.npm : '-';
                const name = user ? user.name : 'Mahasiswa';
                const statusBadge = p.status === 'hadir'
                    ? '<span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">Hadir</span>'
                    : '<span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-amber-50 text-amber-700 border border-amber-200">Terdaftar</span>';

                const csrfToken = $('meta[name="csrf-token"]').attr('content');
                tbody.append(`
                    <tr class="hover:bg-zinc-50/50">
                        <td class="px-4 py-2.5 font-mono font-bold text-zinc-900">${npm}</td>
                        <td class="px-4 py-2.5 font-medium text-zinc-800">${name}</td>
                        <td class="px-4 py-2.5 text-zinc-500 italic">${p.catatan || '-'}</td>
                        <td class="px-4 py-2.5 text-center">${statusBadge}</td>
                        <td class="px-4 py-2.5 text-right">
                            <form action="/administrator/jadwal-praktikum/${jadwal.id}/peserta-susulan/${p.id}" method="POST" class="inline" onsubmit="return confirm('Hapus praktikan dari jadwal susulan ini?')">
                                <input type="hidden" name="_token" value="${csrfToken}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="text-rose-500 hover:text-rose-700 font-bold p-1" title="Hapus">
                                    <i class="fas fa-trash-alt text-[10px]"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                `);
            });
        }
    </script>
@endsection
