@extends('layouts.admin')

@section('title', 'Detail Penilaian')

@section('content')
    <div class="space-y-8">
        <!-- Breadcrumbs -->
        <div class="flex items-center gap-3 text-[10px] font-black uppercase tracking-widest text-slate-400">
            <a href="{{ route('admin.penilaian.index') }}" class="hover:text-slate-900 transition-colors">Pusat Penilaian</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <a href="{{ route('admin.penilaian.praktikum', $jadwal->praktikum_id) }}" class="hover:text-slate-900 transition-colors">{{ $jadwal->praktikum->nama_praktikum }}</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <span class="text-slate-900">{{ $jadwal->judul_modul }}</span>
        </div>

        <!-- Info Header -->
        <div class="bg-white rounded-[2.5rem] p-6 md:p-10 border border-slate-200 shadow-xl shadow-slate-200/50">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-3 mb-2">
                             <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-[9px] font-black uppercase">Admin Authority</span>
                        </div>
                        <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight leading-none uppercase">
                            {{ $jadwal->judul_modul }}
                        </h1>
                        <p class="text-[11px] md:text-xs text-slate-400 font-bold uppercase tracking-[0.2em]">
                             {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d F Y') }} • {{ $jadwal->praktikum->nama_praktikum }}
                        </p>
                    </div>
                </div>

                <div class="bg-slate-50 border border-slate-100 px-8 py-4 rounded-3xl text-right">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Entri Presensi</p>
                    <div class="flex items-center gap-2 justify-end">
                        <span class="text-3xl font-black text-slate-900">{{ $presensis->count() }}</span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Siswa</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student List -->
        <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-8 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/10">
                <div>
                    <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Daftar Praktikan</h3>
                    <p class="text-xs text-slate-400 font-medium italic">Klik tombol nilai untuk menginput/mengubah nilai meskipun sesi telah berakhir.</p>
                </div>
                <div class="relative group">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-slate-900 transition-colors"></i>
                    <input type="text" id="adminSearch" placeholder="Cari Nama atau NPM..." 
                           class="pl-11 pr-6 py-3 bg-white border border-slate-200 rounded-2xl text-sm font-medium w-full md:w-80 focus:ring-4 focus:ring-slate-100 focus:border-slate-800 transition-all outline-none">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full" id="adminTable">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-10 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Praktikan</th>
                            <th class="px-10 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Sesi / Status Presensi</th>
                            <th class="px-10 py-5 text-center text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Skor Saat Ini</th>
                            <th class="px-10 py-5 text-center text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($presensis as $presensi)
                            @php
                                $praktikan = $presensi->pendaftaran->praktikan;
                                $nilai = $presensi->penilaian;
                                $statusColor = match($presensi->status) {
                                    'hadir' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                    'izin', 'sakit' => 'bg-amber-50 text-amber-600 border-amber-100',
                                    default => 'bg-rose-50 text-rose-600 border-rose-100',
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-10 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-black text-lg">
                                            {{ substr($praktikan->nama, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-slate-900">{{ $praktikan->nama }}</p>
                                            <p class="text-[11px] font-mono text-slate-400">{{ $praktikan->npm }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-10 py-6">
                                    <div class="flex flex-col gap-2">
                                        <span class="text-[10px] font-bold text-slate-600 uppercase">{{ $presensi->pendaftaran->sesi->nama_sesi }}</span>
                                        <span class="inline-flex items-center justify-center px-2 py-0.5 rounded border {{ $statusColor }} text-[8px] font-black uppercase w-fit">
                                            {{ $presensi->status }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-10 py-6 text-center">
                                    @if($nilai)
                                        <div class="flex flex-col items-center gap-1">
                                            <span class="text-xl font-black text-slate-900">{{ $nilai->nilai }}</span>
                                            @if($nilai->aslab_id)
                                                <span class="text-[8px] font-bold text-slate-400 uppercase">Dinilai oleh Aslab</span>
                                            @else
                                                <span class="text-[8px] font-bold text-emerald-500 uppercase italic">Dinilai oleh Admin</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs font-bold text-slate-300 italic uppercase">N/A</span>
                                    @endif
                                </td>
                                <td class="px-10 py-6 text-center">
                                    <button type="button" 
                                            onclick="openAdminModal('{{ $presensi->id }}', '{{ $praktikan->nama }}', '{{ $nilai ? $nilai->nilai : '' }}', '{{ $nilai ? $nilai->catatan : '' }}')"
                                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-slate-900 text-white text-[11px] font-black rounded-xl hover:bg-slate-800 transition-all shadow-lg group-hover:scale-105">
                                        <i class="fas fa-edit"></i>
                                        BERI NILAI
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-10 py-20 text-center">
                                     <p class="text-sm font-medium text-slate-300 uppercase tracking-widest">Tidak ada data presensi</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Admin Grading Modal -->
    <div id="adminModal" class="fixed inset-0 z-[100] hidden overflow-y-auto outline-none">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity" onclick="closeAdminModal()"></div>
            
            <div class="relative bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl transition-all transform py-10 px-8">
                <div class="text-center space-y-4 mb-10">
                    <div class="w-20 h-20 bg-slate-900 text-white rounded-3xl flex items-center justify-center text-3xl font-black mx-auto shadow-2xl mb-6">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-slate-900 uppercase">Input Nilai Admin</h2>
                        <p class="text-slate-400 font-medium" id="adminStudentName"></p>
                    </div>
                </div>

                <form action="{{ route('admin.penilaian.store') }}" method="POST" class="space-y-8">
                    @csrf
                    <input type="hidden" name="presensi_id" id="adminPresensiId">
                    
                    <div class="space-y-6">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Skor Akhir (0-100)</label>
                            <input type="number" name="nilai" id="adminScoreInput" required min="0" max="100" placeholder="Skor"
                                   class="w-full px-8 py-5 bg-slate-50 border border-slate-200 rounded-3xl text-2xl font-black focus:ring-4 focus:ring-slate-50 focus:border-slate-800 outline-none transition-all">
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Catatan Perubahan</label>
                            <textarea name="catatan" id="adminNotesInput" rows="3" placeholder="Alasan pemberian/perubahan nilai..."
                                      class="w-full p-6 bg-slate-50 border border-slate-200 rounded-3xl text-sm font-medium focus:ring-4 focus:ring-slate-50 focus:border-slate-800 outline-none transition-all"></textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <button type="button" onclick="closeAdminModal()"
                                class="py-5 bg-slate-100 text-slate-500 rounded-3xl text-[11px] font-black uppercase hover:bg-slate-200 transition-all">
                            Batal
                        </button>
                        <button type="submit"
                                class="py-5 bg-slate-900 text-white rounded-3xl text-[11px] font-black uppercase hover:bg-slate-800 transition-all shadow-xl">
                            Simpan Perubahan
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
