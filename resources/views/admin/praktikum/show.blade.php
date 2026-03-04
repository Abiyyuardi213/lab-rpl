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
                        </dl>
                    </div>
                </div>

                <!-- Session Management Card -->
                <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden">
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
                                        Sesi / Dosen</th>
                                    <th
                                        class="px-6 py-3 text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                                        Asal Kelas MK</th>
                                    <th
                                        class="px-6 py-3 text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                                        Waktu</th>
                                    <th
                                        class="px-6 py-3 text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                                        Kuota</th>
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
                                            <div class="text-[10px] text-zinc-500 font-medium">
                                                {{ $sesi->dosen_pengampu ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-xs font-semibold text-zinc-700 uppercase">
                                                {{ $sesi->asal_kelas_mata_kuliah ?? '-' }}</div>
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
                                            <form action="{{ route('admin.praktikum.sesi.destroy', $sesi->id) }}"
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
                                        <td colspan="5" class="px-6 py-10 text-center text-zinc-400 italic font-medium">
                                            Belum ada sesi yang dibuat</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Add Session Section -->
                <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden p-6">
                    <h4
                        class="text-xs font-bold text-zinc-900 uppercase tracking-widest flex items-center gap-2 mb-6 border-b border-zinc-100 pb-2">
                        <i class="fas fa-plus text-[#001f3f]"></i>
                        Tambah Sesi Baru
                    </h4>
                    <form action="{{ route('admin.praktikum.sesi.store', $praktikum->id) }}" method="POST"
                        class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-zinc-500 uppercase">Nama Sesi</label>
                                <input type="text" name="nama_sesi" placeholder="Sesi 1" required
                                    class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f]">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-zinc-500 uppercase">Dosen Pengampu</label>
                                <input type="text" name="dosen_pengampu" placeholder="Nama Dosen" required
                                    class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f]">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-zinc-500 uppercase">Asal Kelas MK</label>
                                <input type="text" name="asal_kelas_mata_kuliah" placeholder="Contoh: 4IA01" required
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
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-zinc-500 uppercase">Kuota Mhs</label>
                                <input type="number" name="kuota" placeholder="30" required min="1"
                                    class="w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f]">
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
                        <button type="submit"
                            class="w-full py-2.5 bg-[#001f3f] text-white rounded-lg text-xs font-bold hover:bg-[#002d5a] transition-all active:scale-[0.98] shadow-sm">
                            SIMPAN SESI BARU
                        </button>
                    </form>
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

    <form id="delete-form" action="{{ route('admin.praktikum.destroy', $praktikum->id) }}" method="POST"
        class="hidden">
        @csrf @method('DELETE')
    </form>

    <script>
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
