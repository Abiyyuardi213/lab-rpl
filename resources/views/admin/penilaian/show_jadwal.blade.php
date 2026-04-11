@extends('layouts.admin')

@section('title', 'Detail Penilaian')

@section('content')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-start justify-between">
            <div class="space-y-1">
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 uppercase">{{ $jadwal->judul_modul }}</h1>
                <p class="text-sm text-zinc-500 font-medium italic">"{{ $jadwal->praktikum->nama_praktikum }} • {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d F Y') }}"</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('admin.penilaian.praktikum', $jadwal->praktikum_id) }}" class="hover:text-zinc-900 transition-colors">{{ $jadwal->praktikum->kode_praktikum }}</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Detail Nilai</span>
            </div>
        </div>

        @if (session('success'))
            <div
                class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm flex items-center gap-3 animate-in fade-in slide-in-from-top-2">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- Table Container in Shadcn Style -->
        <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden">
            <div class="p-6 pb-4 flex items-center justify-between gap-4 border-b border-zinc-100 bg-zinc-50/50">
                <div class="flex items-center gap-2 flex-1">
                    <div class="relative max-w-sm w-full">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 text-xs"></i>
                        <input type="text" id="adminSearch" placeholder="Cari praktikan..."
                            class="flex h-9 w-full rounded-md border border-zinc-200 bg-white px-3 py-1 pl-9 text-sm shadow-sm transition-colors placeholder:text-zinc-400 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
                    </div>
                </div>
                <div class="text-right">
                     <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Total Siswa: {{ $presensis->count() }}</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="adminTable" class="w-full text-sm text-left">
                    <thead class="bg-zinc-50 border-b border-zinc-100 text-zinc-500 font-medium h-10">
                        <tr>
                            <th class="px-6 align-middle font-medium text-zinc-500 uppercase text-[10px] tracking-wider">Praktikan</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 uppercase text-[10px] tracking-wider">Status Presensi</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 uppercase text-[10px] tracking-wider text-center">Skor</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 uppercase text-[10px] tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse($presensis as $presensi)
                            @php
                                $praktikan = $presensi->pendaftaran->praktikan;
                                $nilai = $presensi->penilaian;
                                $statusClass = match($presensi->status) {
                                    'hadir' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    'izin', 'sakit' => 'bg-amber-50 text-amber-700 border-amber-100',
                                    default => 'bg-zinc-100 text-zinc-600 border-zinc-200',
                                };
                            @endphp
                            <tr class="hover:bg-zinc-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded bg-zinc-900 text-white flex items-center justify-center font-bold text-xs">
                                            {{ substr($praktikan->nama, 0, 1) }}
                                        </div>
                                        <div>
                                            <span class="font-semibold text-zinc-900 block leading-tight">{{ $praktikan->nama }}</span>
                                            <span class="text-[10px] text-zinc-400 font-mono">{{ $praktikan->npm }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border {{ $statusClass }}">
                                        {{ $presensi->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($nilai)
                                        <div class="flex flex-col items-center">
                                            <span class="text-sm font-black text-zinc-900">{{ $nilai->nilai }}</span>
                                            <span class="text-[8px] font-bold text-zinc-400 uppercase tracking-tighter">
                                                {{ $nilai->aslab_id ? 'By Aslab' : 'By Admin' }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-xs text-zinc-300 italic">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button onclick="openAdminModal('{{ $presensi->id }}', '{{ $praktikan->nama }}', '{{ $nilai ? $nilai->nilai : '' }}', '{{ $nilai ? $nilai->catatan : '' }}')"
                                            class="inline-flex h-8 items-center justify-center rounded-md border border-zinc-200 bg-white px-3 text-xs font-medium shadow-sm hover:bg-zinc-50 transition-colors">
                                        <i class="fas fa-edit mr-2 text-[10px]"></i>
                                        Beri Nilai
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-20 text-center text-zinc-400">
                                    Belum ada data presensi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Admin Grading Modal (Shadcn Style) -->
    <div id="adminModal" class="fixed inset-0 z-[100] hidden overflow-y-auto outline-none">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeAdminModal()"></div>
            
            <div class="relative bg-white w-full max-w-lg rounded-xl shadow-2xl transition-all transform p-6">
                <div class="flex flex-col space-y-1.5 text-center sm:text-left mb-6">
                    <h3 class="text-lg font-semibold leading-none tracking-tight">Input Nilai Praktikum</h3>
                    <p class="text-sm text-zinc-500" id="adminStudentName"></p>
                </div>

                <form action="{{ route('admin.penilaian.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="presensi_id" id="adminPresensiId">
                    
                    <div class="space-y-2 text-left">
                        <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Skor (0-100)</label>
                        <input type="number" name="nilai" id="adminScoreInput" required min="0" max="100"
                               class="flex h-10 w-full rounded-md border border-zinc-200 bg-white px-3 py-2 text-sm ring-offset-white file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-zinc-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-950 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                    </div>

                    <div class="space-y-2 text-left">
                        <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Catatan Perubahan (Opsional)</label>
                        <textarea name="catatan" id="adminNotesInput" rows="3"
                                  class="flex min-h-[80px] w-full rounded-md border border-zinc-200 bg-white px-3 py-2 text-sm ring-offset-white placeholder:text-zinc-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-950 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"></textarea>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2 gap-2">
                        <button type="button" onclick="closeAdminModal()"
                                class="inline-flex h-10 items-center justify-center rounded-md border border-zinc-200 bg-white px-4 py-2 text-sm font-medium ring-offset-white transition-colors hover:bg-zinc-100 hover:text-zinc-900">
                            Batal
                        </button>
                        <button type="submit"
                                class="inline-flex h-10 items-center justify-center rounded-md bg-zinc-900 px-4 py-2 text-sm font-medium text-zinc-50 shadow hover:bg-zinc-900/90 transition-colors">
                            Simpan Nilai
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAdminModal(presensiId, studentName, currentNilai, currentCatatan) {
            document.getElementById('adminPresensiId').value = presensiId;
            document.getElementById('adminStudentName').textContent = studentName;
            document.getElementById('adminScoreInput').value = currentNilai;
            document.getElementById('adminNotesInput').value = currentCatatan;
            
            const modal = document.getElementById('adminModal');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeAdminModal() {
            const modal = document.getElementById('adminModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Search Filter
        document.getElementById('adminSearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#adminTable tbody tr');
            
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    </script>
@endsection
