@extends('layouts.admin')

@section('title', 'Detail Praktikum')

@section('content')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-start justify-between">
            <div class="space-y-1">
                <a href="{{ route('admin.praktikum.index') }}"
                    class="inline-flex items-center gap-2 text-xs font-bold text-zinc-500 hover:text-zinc-900 transition-colors mb-2">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Daftar
                </a>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900">{{ $praktikum->nama_praktikum }}</h1>
                    <span
                        class="bg-zinc-100 text-zinc-700 px-2 py-0.5 rounded text-[11px] font-bold font-mono border border-zinc-200 uppercase tracking-wider">
                        {{ $praktikum->kode_praktikum }}
                    </span>
                </div>
                <p class="text-sm text-zinc-500 mt-1">Informasi lengkap praktikum dan manajemen sesi pendaftaran.</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                <span>/</span>
                <a href="{{ route('admin.praktikum.index') }}" class="hover:text-zinc-900 transition-colors">Praktikum</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Detail</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            <!-- Sidebar (Left - Info & Sessions) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Detail Information Card -->
                <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-zinc-100 bg-zinc-50/50 flex items-center justify-between">
                        <h3 class="font-bold text-zinc-900 flex items-center gap-2">
                            <i class="fas fa-info-circle text-[#001f3f]"></i>
                            Detail Informasi
                        </h3>
                        <a href="{{ route('admin.praktikum.edit', $praktikum->id) }}"
                            class="text-xs font-bold text-[#001f3f] hover:underline transition-all">
                            Ubah Data
                        </a>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                            <div class="space-y-1">
                                <dt class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Kode Praktikum
                                </dt>
                                <dd class="text-sm font-semibold text-zinc-900">{{ $praktikum->kode_praktikum }}</dd>
                            </div>
                            <div class="space-y-1">
                                <dt class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Periode</dt>
                                <dd class="text-sm font-semibold text-zinc-900">{{ $praktikum->periode_praktikum }}</dd>
                            </div>
                            <div class="space-y-1">
                                <dt class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Kuota Maksimal
                                </dt>
                                <dd class="text-sm font-semibold text-zinc-900">{{ $praktikum->kuota_praktikan }} Mahasiswa
                                </dd>
                            </div>
                            <div class="space-y-1">
                                <dt class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Tanggal Dibuat
                                </dt>
                                <dd class="text-sm font-semibold text-zinc-900">
                                    {{ $praktikum->created_at->format('d M Y, H:i') }}</dd>
                            </div>
                            <div class="sm:col-span-2 space-y-1">
                                <dt class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Status Saat Ini
                                </dt>
                                <dd>
                                    @php
                                        $statusConfig = [
                                            'open_registration' => [
                                                'label' => 'Buka Pendaftaran',
                                                'class' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                            ],
                                            'on_progress' => [
                                                'label' => 'Berlangsung',
                                                'class' => 'bg-amber-50 text-amber-700 border-amber-100',
                                            ],
                                            'finished' => [
                                                'label' => 'Berakhir',
                                                'class' => 'bg-rose-50 text-rose-700 border-rose-100',
                                            ],
                                        ];
                                        $currentStatus = $statusConfig[$praktikum->status_praktikum] ?? [
                                            'label' => $praktikum->status_praktikum,
                                            'class' => 'bg-zinc-50 text-zinc-700 border-zinc-100',
                                        ];
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold border {{ $currentStatus['class'] }}">
                                        <i class="fas fa-circle text-[6px] mr-2"></i>
                                        {{ $currentStatus['label'] }}
                                    </span>
                                </dd>
                            </div>
                            <div class="space-y-1">
                                <dt class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Kurikulum
                                    Praktikum</dt>
                                <dd class="text-sm font-semibold text-zinc-900">
                                    {{ $praktikum->jumlah_modul }} Modul
                                    @if ($praktikum->ada_tugas_akhir)
                                        + 1 Tugas Akhir
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Jadwal Praktikum Management Card -->
                <div id="jadwal-section"
                    class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden mt-6">
                    <div class="p-6 border-b border-zinc-100 bg-zinc-50/50 flex items-center justify-between">
                        <h3 class="font-bold text-zinc-900 flex items-center gap-2">
                            <i class="fas fa-calendar-check text-[#001f3f]"></i>
                            Jadwal Pelaksanaan (Modul)
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-zinc-50/50 border-b border-zinc-100">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                                        Modul / Pertemuan</th>
                                    <th
                                        class="px-6 py-3 text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                                        Tanggal & Waktu</th>
                                    <th
                                        class="px-6 py-3 text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                                        Ruangan</th>
                                    <th
                                        class="px-6 py-3 text-right text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100">
                                @forelse($praktikum->jadwals as $jadwal)
                                    <tr class="hover:bg-zinc-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-zinc-900">{{ $jadwal->judul_modul }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-zinc-600 font-medium capitalize">
                                                {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d F Y') }}
                                            </div>
                                            <div class="text-[10px] text-zinc-400 font-mono italic">
                                                {{ substr($jadwal->waktu_mulai, 0, 5) }} -
                                                {{ substr($jadwal->waktu_selesai, 0, 5) }} WIB
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-zinc-600">{{ $jadwal->ruangan ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <form
                                                action="{{ route('admin.praktikum.jadwal.destroy', $jadwal->id) }}#jadwal-section"
                                                method="POST" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" onclick="return confirm('Hapus jadwal ini?')"
                                                    class="h-8 w-8 inline-flex items-center justify-center rounded-lg border border-rose-100 bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors">
                                                    <i class="fas fa-trash-alt text-[10px]"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-zinc-400 italic font-medium">
                                            Belum ada jadwal pelaksanaan yang ditambahkan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Add Jadwal Form Section -->
                <div id="add-jadwal-section"
                    class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden p-6 mt-6">
                    <h4
                        class="text-xs font-bold text-zinc-900 uppercase tracking-widest flex items-center gap-2 mb-6 border-b border-zinc-100 pb-2">
                        <i class="fas fa-plus text-[#001f3f]"></i>
                        Tambah Jadwal Pelaksanaan (Modul)
                    </h4>
                    <form action="{{ route('admin.praktikum.jadwal.store', $praktikum->id) }}#jadwal-section"
                        method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            <div class="space-y-1 lg:col-span-2">
                                <label class="text-[10px] font-bold text-zinc-500 uppercase">Judul Modul</label>
                                @if (count($availableModules) > 0)
                                    <select name="judul_modul" required
                                        class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f]">
                                        <option value="">-- Pilih Modul --</option>
                                        @foreach ($availableModules as $modul)
                                            <option value="{{ $modul }}"
                                                {{ in_array($modul, $scheduledModules) ? 'disabled' : '' }}>
                                                {{ $modul }}
                                                {{ in_array($modul, $scheduledModules) ? '(Sudah Dijadwalkan)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="text" name="judul_modul" placeholder="Modul 1: Pengenalan" required
                                        class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f]">
                                @endif
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-zinc-500 uppercase">Tanggal</label>
                                <input type="date" name="tanggal" required
                                    class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f]">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-zinc-500 uppercase">Waktu Mulai</label>
                                <input type="time" name="waktu_mulai" required
                                    class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f]">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-zinc-500 uppercase">Waktu Selesai</label>
                                <input type="time" name="waktu_selesai" required
                                    class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f]">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="space-y-1 lg:col-span-2">
                                <label class="text-[10px] font-bold text-zinc-500 uppercase">Ruangan (Opsional)</label>
                                <input type="text" name="ruangan" placeholder="Lab R 301 / Daring"
                                    class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f]">
                            </div>
                        </div>
                        <button type="submit"
                            class="w-full py-2.5 bg-[#001f3f] text-white rounded-lg text-xs font-bold hover:bg-[#002d5a] transition-all active:scale-[0.98] shadow-sm">
                            SIMPAN JADWAL
                        </button>
                    </form>
                </div>

                <!-- Session Management Card -->
                <div id="sesi-section"
                    class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-zinc-100 bg-zinc-50/50 flex items-center justify-between">
                        <h3 class="font-bold text-zinc-900 flex items-center gap-2">
                            <i class="fas fa-calendar-alt text-[#001f3f]"></i>
                            Manajemen Sesi Praktikum
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-zinc-50/50 border-b border-zinc-100">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                                        Sesi</th>
                                    <th
                                        class="px-6 py-3 text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                                        Waktu</th>
                                    <th
                                        class="px-6 py-3 text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                                        Kuota Terisi</th>
                                    <th
                                        class="px-6 py-3 text-right text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100">
                                @forelse($praktikum->sesis as $sesi)
                                    <tr class="hover:bg-zinc-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-zinc-900">{{ $sesi->nama_sesi }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-zinc-600 font-medium capitalize">{{ $sesi->hari }}</div>
                                            <div class="text-[10px] text-zinc-400 font-mono italic">
                                                {{ substr($sesi->jam_mulai, 0, 5) }} -
                                                {{ substr($sesi->jam_selesai, 0, 5) }} WIB
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-zinc-600 font-bold">
                                                {{ $sesi->pendaftarans_count ?? $sesi->pendaftarans()->count() }}/{{ $sesi->kuota }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-1 text-center">
                                                <button type="button" onclick="editSesi({{ json_encode($sesi) }})"
                                                    class="h-8 w-8 inline-flex items-center justify-center rounded-lg border border-zinc-100 bg-zinc-50 text-zinc-600 hover:bg-zinc-100 transition-colors">
                                                    <i class="fas fa-edit text-[10px]"></i>
                                                </button>
                                                <form
                                                    action="{{ route('admin.praktikum.sesi.destroy', $sesi->id) }}#sesi-section"
                                                    method="POST" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" onclick="return confirm('Hapus sesi ini?')"
                                                        class="h-8 w-8 inline-flex items-center justify-center rounded-lg border border-rose-100 bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors">
                                                        <i class="fas fa-trash-alt text-[10px]"></i>
                                                    </button>
                                                </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5"
                                            class="px-6 py-10 text-center text-zinc-400 italic font-medium">
                                            Belum ada sesi yang dibuat</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Add Session Section -->
                <div id="add-sesi-section"
                    class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden p-6">
                    <h4
                        class="text-xs font-bold text-zinc-900 uppercase tracking-widest flex items-center gap-2 mb-6 border-b border-zinc-100 pb-2">
                        <i class="fas fa-plus text-[#001f3f]"></i>
                        Tambah Sesi Baru
                    </h4>
                    <form action="{{ route('admin.praktikum.sesi.store', $praktikum->id) }}#sesi-section" method="POST"
                        class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-zinc-500 uppercase">Nama Sesi</label>
                                <input type="text" name="nama_sesi" placeholder="Sesi 1" required
                                    class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f]">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-zinc-500 uppercase">Hari</label>
                                <select name="hari" required
                                    class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f]">
                                    <option value="Senin">Senin</option>
                                    <option value="Selasa">Selasa</option>
                                    <option value="Rabu">Rabu</option>
                                    <option value="Kamis">Kamis</option>
                                    <option value="Jumat">Jumat</option>
                                    <option value="Sabtu">Sabtu</option>
                                    <option value="Minggu">Minggu</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-zinc-500 uppercase">Jam Mulai</label>
                                <input type="time" name="jam_mulai" required
                                    class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f]">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-zinc-500 uppercase">Jam Selesai</label>
                                <input type="time" name="jam_selesai" required
                                    class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f]">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-zinc-500 uppercase">Kuota Mhs</label>
                                <input type="number" name="kuota" placeholder="30" required min="1"
                                    class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f]">
                            </div>
                        </div>
                        <button type="submit"
                            class="w-full py-2.5 bg-[#001f3f] text-white rounded-lg text-xs font-bold hover:bg-[#002d5a] transition-all active:scale-[0.98] shadow-sm">
                            SIMPAN SESI BARU
                        </button>
                    </form>
                </div>

                <!-- Aslab Management Card -->
                <div id="aslab-section"
                    class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-zinc-100 bg-zinc-50/50 flex items-center justify-between">
                        <h3 class="font-bold text-zinc-900 flex items-center gap-2">
                            <i class="fas fa-users-cog text-[#001f3f]"></i>
                            Manajemen Aslab Praktikum
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-zinc-50/50 border-b border-zinc-100">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                                        Nama Aslab</th>
                                    <th
                                        class="px-6 py-3 text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                                        Email</th>
                                    <th
                                        class="px-6 py-3 text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                                        Kuota</th>
                                    <th
                                        class="px-6 py-3 text-right text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100">
                                @forelse($praktikum->aslabs as $aslab)
                                    <tr class="hover:bg-zinc-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-zinc-900">{{ $aslab->user->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-zinc-500">{{ $aslab->user->email }}</td>
                                        <td class="px-6 py-4">
                                            <div class="text-zinc-600 font-bold">{{ $aslab->pivot->kuota }} Mhs</div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <form
                                                action="{{ route('admin.praktikum.aslab.destroy', $aslab->pivot->id) }}#aslab-section"
                                                method="POST" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    onclick="return confirm('Hapus penugasan aslab ini?')"
                                                    class="h-8 w-8 inline-flex items-center justify-center rounded-lg border border-rose-100 bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors">
                                                    <i class="fas fa-user-minus text-[10px]"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4"
                                            class="px-6 py-10 text-center text-zinc-400 italic font-medium">Belum ada aslab
                                            yang ditugaskan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Add Aslab Form -->
                    <div class="p-6 border-t border-zinc-100 bg-zinc-50/30">
                        <h4 class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-4">Tugaskan Aslab Baru
                        </h4>
                        <form action="{{ route('admin.praktikum.aslab.store', $praktikum->id) }}#aslab-section"
                            method="POST" class="flex flex-wrap gap-4 items-end">
                            @csrf
                            <div class="flex-1 min-w-[200px] space-y-1">
                                <label class="text-[10px] font-bold text-zinc-500 uppercase">Pilih Aslab</label>
                                <select name="aslab_id" required
                                    class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f]">
                                    <option value="">-- Pilih Aslab --</option>
                                    @foreach ($allAslabs as $aslab)
                                        <option value="{{ $aslab->id }}">{{ $aslab->npm }} | {{ $aslab->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-32 space-y-1">
                                <label class="text-[10px] font-bold text-zinc-500 uppercase">Kuota</label>
                                <input type="number" name="kuota" required min="1" placeholder="Maks"
                                    class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f]">
                            </div>
                            <button type="submit"
                                class="px-6 py-2 bg-[#001f3f] text-white rounded-lg text-xs font-bold hover:bg-[#002d5a] transition-all active:scale-[0.98]">
                                TUGASKAN
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Registered Students Card -->
                <div id="mahasiswa-section"
                    class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden mt-6">
                    <div class="p-6 border-b border-zinc-100 bg-zinc-50/50 flex items-center justify-between">
                        <h3 class="font-bold text-zinc-900 flex items-center gap-2">
                            <i class="fas fa-user-graduate text-[#001f3f]"></i>
                            Daftar Praktikan Terdaftar
                        </h3>
                        <span class="bg-zinc-100 text-zinc-600 px-2 py-0.5 rounded text-[10px] font-bold">
                            Total: {{ $praktikum->pendaftarans->count() }} Orang
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-zinc-50/50 border-b border-zinc-100">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                                        Mahasiswa</th>
                                    <th
                                        class="px-6 py-3 text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                                        Sesi</th>
                                    <th
                                        class="px-6 py-3 text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                                        Aslab Bimbingan</th>
                                    <th
                                        class="px-6 py-3 text-center text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100">
                                @forelse($praktikum->pendaftarans as $pendaftaran)
                                    <tr class="hover:bg-zinc-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-zinc-900">{{ $pendaftaran->user->name }}</div>
                                            <div class="text-[10px] text-zinc-400 font-mono">{{ $pendaftaran->user->npm }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-zinc-600 font-semibold">{{ $pendaftaran->sesi->nama_sesi }}
                                            </div>
                                            <div class="text-[10px] text-zinc-400">{{ $pendaftaran->sesi->hari }},
                                                {{ substr($pendaftaran->sesi->jam_mulai, 0, 5) }}-{{ substr($pendaftaran->sesi->jam_selesai, 0, 5) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <form id="assign-aslab-form-{{ $pendaftaran->id }}"
                                                action="{{ route('admin.praktikum.pendaftaran.assign-aslab', $pendaftaran->id) }}#mahasiswa-section"
                                                method="POST">
                                                @csrf @method('PATCH')
                                                <select name="aslab_id" onchange="this.form.submit()"
                                                    class="text-[11px] font-semibold bg-zinc-50 border border-zinc-200 rounded-lg px-2 py-1 focus:ring-1 focus:ring-[#001f3f] focus:border-[#001f3f] w-full max-w-[150px]">
                                                    <option value="">-- Pilih Aslab --</option>
                                                    @foreach ($praktikum->aslabs as $as)
                                                        @php
                                                            // Optional: check quota here?
                                                            $curr = $as
                                                                ->assignedStudents()
                                                                ->where('praktikum_id', $praktikum->id)
                                                                ->count();
                                                            $max = $as->pivot->kuota;
                                                            $isFull = $curr >= $max;
                                                        @endphp
                                                        <option value="{{ $as->id }}"
                                                            {{ $pendaftaran->aslab_id == $as->id ? 'selected' : '' }}
                                                            {{ $isFull && $pendaftaran->aslab_id != $as->id ? 'disabled' : '' }}>
                                                            {{ $as->user->name }}
                                                            ({{ $curr }}/{{ $max }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </form>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php
                                                $statusBadge = [
                                                    'pending' => 'bg-amber-50 text-amber-700 border-amber-100',
                                                    'verified' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                                    'rejected' => 'bg-rose-50 text-rose-700 border-rose-100',
                                                ];
                                                $st =
                                                    $statusBadge[$pendaftaran->status] ??
                                                    'bg-zinc-50 text-zinc-500 border-zinc-100';
                                            @endphp
                                            <span
                                                class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $st }} uppercase">
                                                {{ $pendaftaran->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4"
                                            class="px-6 py-10 text-center text-zinc-400 italic font-medium">
                                            Belum ada praktikan yang mendaftar</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sidebar (Right - Status & Danger Zone) -->
            <div class="space-y-6">
                <!-- Status Management Card -->
                <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-zinc-100 bg-zinc-50/50">
                        <h3 class="font-bold text-zinc-900 text-sm">Update Status</h3>
                    </div>
                    <div class="p-4 space-y-2">
                        @php
                            $buttons = [
                                'open_registration' => [
                                    'label' => 'Buka Pendaftaran',
                                    'active' => 'bg-emerald-50 border-emerald-200 text-emerald-700',
                                ],
                                'on_progress' => [
                                    'label' => 'Sedang Berlangsung',
                                    'active' => 'bg-amber-50 border-amber-200 text-amber-700',
                                ],
                                'finished' => [
                                    'label' => 'Telah Berakhir',
                                    'active' => 'bg-rose-50 border-rose-200 text-rose-700',
                                ],
                            ];
                        @endphp

                        @foreach ($buttons as $key => $btn)
                            <button onclick="updateStatus('{{ $key }}')"
                                class="w-full flex items-center justify-between p-3 rounded-lg border text-[11px] font-bold uppercase tracking-tight
                                {{ $praktikum->status_praktikum == $key ? $btn['active'] : 'bg-white border-zinc-100 text-zinc-600 hover:bg-zinc-50' }} transition-all">
                                <span>{{ $btn['label'] }}</span>
                                @if ($praktikum->status_praktikum == $key)
                                    <i class="fas fa-check-circle"></i>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Danger Zone Card -->
                <div
                    class="rounded-xl border border-rose-100 bg-rose-50/30 text-zinc-950 p-6 flex flex-col items-center text-center space-y-3">
                    <div class="h-10 w-10 rounded-full bg-rose-100 flex items-center justify-center">
                        <i class="fas fa-trash-alt text-rose-600 text-sm"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-zinc-900">Hapus Praktikum</h4>
                        <p class="text-[10px] text-zinc-500 mt-1 italic leading-relaxed">Menghapus data ini akan
                            menghilangkan seluruh relasi pendaftar & sesi.</p>
                    </div>
                    <button onclick="confirmDelete()"
                        class="w-full px-4 py-2 rounded-lg bg-rose-600 text-white text-[11px] font-bold uppercase tracking-widest hover:bg-rose-700 transition-colors shadow-sm active:scale-95">
                        Hapus Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Session Modal -->
    <div id="editSesiModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-zinc-900/50 backdrop-blur-sm transition-opacity"></div>
            <div class="relative bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden">
                <div class="p-6 border-b border-zinc-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-zinc-900">Ubah Sesi Praktikum</h3>
                    <button type="button" onclick="closeEditSesi()" class="text-zinc-400 hover:text-zinc-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="editSesiForm" method="POST">
                    @csrf @method('PATCH')
                    <div class="p-6 space-y-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-zinc-500 uppercase">Nama Sesi</label>
                            <input type="text" name="nama_sesi" id="edit_nama_sesi" required
                                class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f]">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-zinc-500 uppercase">Hari</label>
                                <select name="hari" id="edit_hari" required
                                    class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f]">
                                    <option value="Senin">Senin</option>
                                    <option value="Selasa">Selasa</option>
                                    <option value="Rabu">Rabu</option>
                                    <option value="Kamis">Kamis</option>
                                    <option value="Jumat">Jumat</option>
                                    <option value="Sabtu">Sabtu</option>
                                    <option value="Minggu">Minggu</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-zinc-500 uppercase">Kuota Mhs</label>
                                <input type="number" name="kuota" id="edit_kuota" required min="1"
                                    class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f]">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-zinc-500 uppercase">Jam Mulai</label>
                                <input type="time" name="jam_mulai" id="edit_jam_mulai" required
                                    class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f]">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-zinc-500 uppercase">Jam Selesai</label>
                                <input type="time" name="jam_selesai" id="edit_jam_selesai" required
                                    class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f]">
                            </div>
                        </div>
                    </div>
                    <div class="p-6 bg-zinc-50 flex items-center justify-end gap-3">
                        <button type="button" onclick="closeEditSesi()"
                            class="px-4 py-2 text-xs font-bold text-zinc-500 hover:text-zinc-700">BATAL</button>
                        <button type="submit"
                            class="px-6 py-2 bg-[#001f3f] text-white rounded-lg text-xs font-bold hover:bg-[#002d5a] transition-all">
                            SIMPAN PERUBAHAN
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form id="delete-form" action="{{ route('admin.praktikum.destroy', $praktikum->id) }}" method="POST"
        class="hidden">
        @csrf @method('DELETE')
    </form>

    <script>
        function editSesi(sesi) {
            const form = document.getElementById('editSesiForm');
            form.action = `/admin/praktikum/sesi/${sesi.id}#sesi-section`;

            document.getElementById('edit_nama_sesi').value = sesi.nama_sesi;
            document.getElementById('edit_hari').value = sesi.hari;
            document.getElementById('edit_kuota').value = sesi.kuota;
            document.getElementById('edit_jam_mulai').value = sesi.jam_mulai.substring(0, 5);
            document.getElementById('edit_jam_selesai').value = sesi.jam_selesai.substring(0, 5);

            document.getElementById('editSesiModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeEditSesi() {
            document.getElementById('editSesiModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function updateStatus(newStatus) {
            Swal.fire({
                title: 'Update Status?',
                text: "Anda akan mengubah status praktikum ini.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#001f3f',
                cancelButtonColor: '#f4f4f5',
                confirmButtonText: 'Ya, Update',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'bg-[#001f3f]'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`{{ route('admin.praktikum.toggle-status', $praktikum->id) }}`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            status: newStatus
                        })
                    }).then(response => response.json()).then(data => {
                        if (data.success) {
                            Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: data.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                })
                                .then(() => window.location.reload());
                        }
                    });
                }
            })
        }

        function confirmDelete() {
            Swal.fire({
                title: 'Hapus Praktikum?',
                text: "Tindakan ini tidak dapat dibatalkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#f4f4f5',
                confirmButtonText: 'Ya, Hapus Permanen',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form').submit();
                }
            })
        }
    </script>
@endsection
