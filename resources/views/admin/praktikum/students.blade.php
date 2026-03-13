@extends('layouts.admin')

@section('title', 'Daftar Praktikan - ' . $praktikum->nama_praktikum)

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
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Daftar Praktikan</h1>
                    <span
                        class="bg-zinc-100 text-zinc-700 px-2 py-0.5 rounded text-[11px] font-bold font-mono border border-zinc-200 uppercase tracking-wider">
                        {{ $praktikum->kode_praktikum }}
                    </span>
                </div>
                <p class="text-sm text-zinc-500 mt-1">{{ $praktikum->nama_praktikum }}</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                <span>/</span>
                <a href="{{ route('admin.praktikum.index') }}" class="hover:text-zinc-900 transition-colors">Praktikum</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Praktikan</span>
            </div>
        </div>

        <!-- Registered Students Card -->
        <div id="mahasiswa-section"
            class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden min-h-[500px]">
            <div class="p-6 border-b border-zinc-100 bg-zinc-50/50 flex items-center justify-between">
                <h3 class="font-bold text-zinc-900 flex items-center gap-2">
                    <i class="fas fa-user-graduate text-[#001f3f]"></i>
                    Manajemen Praktikan Terdaftar
                </h3>
                <span class="bg-[#001f3f] text-white px-3 py-1 rounded-full text-[10px] font-bold shadow-lg shadow-[#001f3f]/10">
                    Total: {{ $praktikum->pendaftarans->count() }} Orang
                </span>
            </div>
            
            <div class="p-6 pb-4 flex items-center justify-between gap-4 border-b border-zinc-50">
                <div class="relative max-w-sm w-full">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-xs shadow-sm"></i>
                    <input type="text" id="studentSearch" placeholder="Cari Nama atau NPM..."
                        class="flex h-9 w-full rounded-lg border border-zinc-200 bg-white px-3 py-1 pl-9 text-sm shadow-sm transition-all focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] placeholder:text-zinc-400">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="studentTable" class="w-full text-sm">
                    <thead class="bg-zinc-50/50 border-b border-zinc-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Mahasiswa</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Sesi Praktikum</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Aslab Bimbingan</th>
                            <th class="px-6 py-3 text-center text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse($praktikum->pendaftarans as $pendaftaran)
                            <tr class="hover:bg-zinc-50/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-zinc-900 leading-tight uppercase tracking-tight">{{ $pendaftaran->praktikan->user->name }}</div>
                                    <div class="text-[10px] text-zinc-400 font-mono font-bold mt-0.5">{{ $pendaftaran->praktikan->npm }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <form id="change-session-form-{{ $pendaftaran->id }}"
                                        action="{{ route('admin.praktikum.pendaftaran.change-session', $pendaftaran->id) }}#mahasiswa-section"
                                        method="POST">
                                        @csrf @method('PATCH')
                                        <select name="sesi_id" onchange="this.form.submit()"
                                            class="text-[11px] font-semibold bg-white border border-zinc-200 rounded-lg px-2.5 py-1.5 focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] w-full max-w-[160px] shadow-sm transition-all cursor-pointer">
                                            @foreach ($praktikum->sesis as $s)
                                                @php
                                                    $countReg = $s->pendaftarans_count ?? $s->pendaftarans()->count();
                                                    $isTargetFull = $countReg >= $s->kuota;
                                                @endphp
                                                <option value="{{ $s->id }}"
                                                    {{ $pendaftaran->sesi_id == $s->id ? 'selected' : '' }}
                                                    {{ $isTargetFull && $pendaftaran->sesi_id != $s->id ? 'disabled' : '' }}>
                                                    {{ $s->nama_sesi }} ({{ $countReg }}/{{ $s->kuota }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                    <div class="text-[9px] text-zinc-400 mt-1 uppercase tracking-tighter font-medium italic">
                                        {{ $pendaftaran->sesi->hari }}, {{ substr($pendaftaran->sesi->jam_mulai, 0, 5) }}-{{ substr($pendaftaran->sesi->jam_selesai, 0, 5) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <form id="assign-aslab-form-{{ $pendaftaran->id }}"
                                        action="{{ route('admin.praktikum.pendaftaran.assign-aslab', $pendaftaran->id) }}#mahasiswa-section"
                                        method="POST">
                                        @csrf @method('PATCH')
                                        <select name="aslab_id" onchange="this.form.submit()"
                                            class="text-[11px] font-semibold bg-white border border-zinc-200 rounded-lg px-2.5 py-1.5 focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] w-full max-w-[160px] shadow-sm transition-all cursor-pointer">
                                            <option value="">-- Pilih Aslab --</option>
                                            @foreach ($praktikum->aslabs as $as)
                                                @php
                                                    $curr = $as->assignedStudents()->where('praktikum_id', $praktikum->id)->count();
                                                    $max = $as->pivot->kuota;
                                                    $isFull = $curr >= $max;
                                                @endphp
                                                <option value="{{ $as->id }}"
                                                    {{ $pendaftaran->aslab_id == $as->id ? 'selected' : '' }}
                                                    {{ $isFull && $pendaftaran->aslab_id != $as->id ? 'disabled' : '' }}>
                                                    {{ $as->user->name }} ({{ $curr }}/{{ $max }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                    <div class="text-[9px] text-zinc-400 mt-1 uppercase tracking-tighter font-medium italic">
                                        Penanggung Jawab Bimbingan
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $statusBadge = [
                                            'pending' => 'bg-amber-50 text-amber-700 border-amber-100',
                                            'verified' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                            'rejected' => 'bg-rose-50 text-rose-700 border-rose-100',
                                        ];
                                        $st = $statusBadge[$pendaftaran->status] ?? 'bg-zinc-50 text-zinc-500 border-zinc-100';
                                    @endphp
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-[9px] font-black border {{ $st }} uppercase tracking-wider">
                                        {{ $pendaftaran->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <div class="h-16 w-16 rounded-2xl bg-zinc-50 flex items-center justify-center border border-zinc-100">
                                            <i class="fas fa-users-slash text-2xl text-zinc-200"></i>
                                        </div>
                                        <div>
                                            <p class="text-zinc-900 font-bold uppercase tracking-tight">Belum ada praktikan</p>
                                            <p class="text-zinc-500 text-xs italic mt-1">Mahasiswa belum mendaftar pada praktikum ini.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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
    </style>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            if ($('#studentTable').length > 0) {
                var table = $('#studentTable').DataTable({
                    dom: 't<"flex flex-col sm:flex-row items-center justify-between px-6 py-4 border-t border-zinc-100"ip>',
                    language: {
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ praktikan",
                        paginate: {
                            next: '<i class="fas fa-chevron-right text-[10px]"></i>',
                            previous: '<i class="fas fa-chevron-left text-[10px]"></i>'
                        }
                    },
                    columnDefs: [{
                        orderable: false,
                        targets: [1, 2, 3]
                    }]
                });

                $('#studentSearch').on('keyup', function() {
                    table.search(this.value).draw();
                });
            }
        });
    </script>
@endsection
