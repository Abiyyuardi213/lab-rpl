@extends('layouts.admin')

@section('title', 'Detail Praktikum')

@section('content')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-start justify-between">
            <div class="space-y-1">
                <a href="{{ route('admin.praktikum.index') }}" data-spa
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
                <p class="text-sm text-zinc-500 mt-1">Informasi lengkap praktikum dan statistik saat ini.</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                <span>/</span>
                <a href="{{ route('admin.praktikum.index') }}" class="hover:text-zinc-900 transition-colors">Praktikum</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Detail</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Information Card -->
            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-zinc-100 bg-zinc-50/50 flex items-center justify-between">
                        <h3 class="font-bold text-zinc-900 flex items-center gap-2">
                            <i class="fas fa-info-circle text-[#001f3f]"></i>
                            Detail Informasi
                        </h3>
                        <a href="{{ route('admin.praktikum.edit', $praktikum->id) }}" data-spa
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
                                <dd class="text-sm font-semibold text-zinc-900 flex items-center gap-1.5">
                                    {{ $praktikum->kuota_praktikan }} Mahasiswa
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
            </div>

            <!-- Quick Stats/Actions -->
            <div class="space-y-6">
                <!-- Status Management -->
                <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-zinc-100">
                        <h3 class="font-bold text-zinc-900 text-sm">Update Status</h3>
                    </div>
                    <div class="p-4 space-y-2">
                        <button onclick="updateStatus('open_registration')"
                            class="w-full flex items-center justify-between p-3 rounded-lg border {{ $praktikum->status_praktikum == 'open_registration' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-white border-zinc-100 text-zinc-600 hover:bg-zinc-50' }} transition-all group">
                            <span class="text-xs font-bold uppercase tracking-tight">Buka Pendaftaran</span>
                            @if ($praktikum->status_praktikum == 'open_registration')
                                <i class="fas fa-check-circle"></i>
                            @endif
                        </button>
                        <button onclick="updateStatus('on_progress')"
                            class="w-full flex items-center justify-between p-3 rounded-lg border {{ $praktikum->status_praktikum == 'on_progress' ? 'bg-amber-50 border-amber-200 text-amber-700' : 'bg-white border-zinc-100 text-zinc-600 hover:bg-zinc-50' }} transition-all group">
                            <span class="text-xs font-bold uppercase tracking-tight">Sedang Berlangsung</span>
                            @if ($praktikum->status_praktikum == 'on_progress')
                                <i class="fas fa-check-circle"></i>
                            @endif
                        </button>
                        <button onclick="updateStatus('finished')"
                            class="w-full flex items-center justify-between p-3 rounded-lg border {{ $praktikum->status_praktikum == 'finished' ? 'bg-rose-50 border-rose-200 text-rose-700' : 'bg-white border-zinc-100 text-zinc-600 hover:bg-zinc-50' }} transition-all group">
                            <span class="text-xs font-bold uppercase tracking-tight">Telah Berakhir</span>
                            @if ($praktikum->status_praktikum == 'finished')
                                <i class="fas fa-check-circle"></i>
                            @endif
                        </button>
                    </div>
                </div>

                <!-- Danger Zone -->
                <div
                    class="rounded-xl border border-rose-100 bg-rose-50/30 text-zinc-950 p-6 flex flex-col items-center text-center space-y-3">
                    <div class="h-10 w-10 rounded-full bg-rose-100 flex items-center justify-center">
                        <i class="fas fa-trash-alt text-rose-600"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-zinc-900">Hapus Praktikum</h4>
                        <p class="text-xs text-zinc-500 mt-1">Menghapus praktikum akan menghapus seluruh keterkaitan data.
                        </p>
                    </div>
                    <button onclick="confirmDelete()"
                        class="w-full px-4 py-2 rounded-lg bg-rose-600 text-white text-xs font-bold hover:bg-rose-700 transition-colors shadow-sm active:scale-95">
                        Hapus Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <form id="delete-form" action="{{ route('admin.praktikum.destroy', $praktikum->id) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
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
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                            Toast.fire({
                                icon: 'success',
                                title: data.message
                            }).then(() => {
                                window.location.reload();
                            });
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
                reverseButtons: true,
                customClass: {
                    cancelButton: 'text-zinc-600 border border-zinc-200'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form').submit();
                }
            })
        }
    </script>
@endsection
