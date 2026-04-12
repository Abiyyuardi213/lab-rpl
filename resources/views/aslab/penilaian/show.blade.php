@extends('layouts.admin')

@section('title', 'Penilaian Praktikan')

@section('content')
    <div class="space-y-8">
        <!-- Sticky Header & Info -->
        <div class="bg-white rounded-[2.5rem] p-6 md:p-10 border border-slate-200 shadow-xl shadow-slate-200/50">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-4">
                    <a href="{{ route('aslab.penilaian.index') }}" class="inline-flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-[#001f3f] transition-colors group">
                        <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                        Kembali ke Jadwal
                    </a>
                    <div class="space-y-1">
                        <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight leading-none uppercase">
                            {{ $jadwal->judul_modul }}
                        </h1>
                        <p class="text-[11px] md:text-xs text-slate-400 font-bold uppercase tracking-[0.2em]">
                            {{ $jadwal->praktikum->nama_praktikum }} • Room: {{ $jadwal->ruangan ?? 'LAB RPL' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="bg-slate-50 border border-slate-100 px-6 py-4 rounded-3xl text-right">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Status Kehadiran</p>
                        <div class="flex items-center gap-2 justify-end">
                            <span class="text-2xl font-black text-[#001f3f]">{{ $presensis->count() }}</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Hadir</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student List -->
        <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-8 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/30">
                <div>
                    <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Daftar Praktikan Hadir</h3>
                    <p class="text-xs text-slate-400 font-medium">Hanya praktikan yang sudah presensi yang muncul di daftar ini.</p>
                </div>
                <div class="relative group">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-[#001f3f] transition-colors"></i>
                    <input type="text" id="studentSearch" placeholder="Cari Nama atau NPM..." 
                           class="pl-11 pr-6 py-3 bg-white border border-slate-200 rounded-2xl text-sm font-medium w-full md:w-80 focus:ring-4 focus:ring-[#001f3f]/5 focus:border-[#001f3f] transition-all outline-none">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full" id="studentTable">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Praktikan</th>
                            <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Sesi / NPM Digits</th>
                            <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Nilai Live</th>
                            <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Nilai Asistensi</th>
                            <th class="px-8 py-5 text-center text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($presensis as $presensi)
                            @php
                                $praktikan = $presensi->pendaftaran->praktikan;
                                $nilai = $presensi->penilaian;
                                // Find assistance grade from TugasAsistensi that matches this module's title
                                $tugasAsistensi = $presensi->pendaftaran->tugasAsistensis
                                    ->where('judul', $jadwal->judul_modul)
                                    ->first();
                                $nilaiAsistensi = $tugasAsistensi ? $tugasAsistensi->nilai : null;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#001f3f] to-[#003366] text-white flex items-center justify-center font-black text-lg shadow-lg shadow-[#001f3f]/20">
                                            {{ substr($praktikan->nama, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-slate-900 group-hover:text-[#001f3f] transition-colors">{{ $praktikan->nama }}</p>
                                            <p class="text-[11px] font-mono text-slate-400">{{ $praktikan->npm }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-[10px] font-bold text-slate-600 uppercase">{{ $presensi->pendaftaran->sesi->nama_sesi }}</span>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[9px] font-black text-slate-400 border border-slate-200 px-1.5 py-0.5 rounded">NPM Akhir: {{ substr($praktikan->npm, -1) }}</span>
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" title="Terverifikasi Hadir"></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    @if($nilai)
                                        <div class="flex items-center gap-3">
                                            <span class="px-3 py-1.5 bg-emerald-50 text-emerald-600 rounded-xl text-[11px] font-black">
                                                LIVE: {{ $nilai->nilai }}
                                            </span>
                                            @if($nilai->catatan)
                                                <i class="fas fa-comment-dots text-slate-300 hover:text-[#001f3f] transition-colors cursor-help" title="{{ $nilai->catatan }}"></i>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-[10px] text-slate-300 font-bold uppercase">Kosong</span>
                                    @endif
                                </td>
                                <td class="px-8 py-6">
                                    @if($nilaiAsistensi !== null)
                                        <span class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-xl text-[11px] font-black">
                                            ASISTENSI: {{ $nilaiAsistensi }}
                                        </span>
                                    @else
                                        <span class="text-[10px] text-slate-300 font-bold uppercase">Kosong</span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <button type="button" 
                                            onclick="openGradingModal('{{ $presensi->id }}', '{{ $praktikan->nama }}', '{{ $nilai ? $nilai->nilai : '' }}', '{{ $nilaiAsistensi ?? '' }}', '{{ $nilai ? $nilai->catatan : '' }}')"
                                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#001f3f] text-white text-[11px] font-black rounded-xl hover:bg-[#002d5a] transition-all shadow-lg shadow-[#001f3f]/10 group-hover:scale-105 active:scale-95">
                                        <i class="fas fa-edit"></i>
                                        {{ $nilai ? 'UPDATE NILAI' : 'INPUT NILAI' }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center grayscale opacity-50">
                                        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                            <i class="fas fa-user-slash text-2xl text-slate-300"></i>
                                        </div>
                                        <p class="text-sm font-medium text-slate-400 uppercase tracking-[0.2em]">Belum Ada Praktikan Hadir</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Grading Modal -->
    <div id="gradingModal" class="fixed inset-0 z-[100] hidden overflow-y-auto outline-none">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeGradingModal()"></div>
            
            <div class="relative bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl transition-all transform py-10 px-8">
                <div class="text-center space-y-4 mb-10">
                    <div class="w-20 h-20 bg-[#001f3f] text-white rounded-3xl flex items-center justify-center text-3xl font-black mx-auto shadow-2xl shadow-[#001f3f]/20 mb-6">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-slate-900 uppercase">Input Nilai Live</h2>
                        <p class="text-slate-400 font-medium" id="modalStudentName"></p>
                    </div>
                </div>

                <form action="{{ route('aslab.penilaian.store') }}" method="POST" class="space-y-8">
                    @csrf
                    <input type="hidden" name="presensi_id" id="modalPresensiId">
                    
                    <div class="space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Skor Praktikum (0-100)</label>
                                <div class="relative group">
                                    <i class="fas fa-star absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-[#001f3f] transition-colors"></i>
                                    <input type="number" name="nilai" id="modalScoreInput" required min="0" max="100" placeholder="Skor"
                                           class="w-full pl-14 pr-6 py-5 bg-slate-50 border border-slate-200 rounded-3xl text-xl font-black focus:ring-4 focus:ring-[#001f3f]/5 focus:border-[#001f3f] outline-none transition-all placeholder:font-normal placeholder:text-slate-300">
                                </div>
                            </div>
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Asistensi Live (0-100)</label>
                                <div class="relative group">
                                    <i class="fas fa-handshake absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-blue-600 transition-colors"></i>
                                    <input type="number" name="nilai_asistensi" id="modalAsistensiInput" min="0" max="100" placeholder="Skor"
                                           class="w-full pl-14 pr-6 py-5 bg-slate-50 border border-slate-200 rounded-3xl text-xl font-black focus:ring-4 focus:ring-blue-600/5 focus:border-blue-600 outline-none transition-all placeholder:font-normal placeholder:text-slate-300">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Catatan Aslab (Opsional)</label>
                            <div class="relative group">
                                <textarea name="catatan" id="modalNotesInput" rows="3" placeholder="Tuliskan catatan kemajuan praktikan..."
                                          class="w-full p-6 bg-slate-50 border border-slate-200 rounded-3xl text-sm font-medium focus:ring-4 focus:ring-[#001f3f]/5 focus:border-[#001f3f] outline-none transition-all placeholder:text-slate-300"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <button type="button" onclick="closeGradingModal()"
                                class="py-5 bg-slate-100 text-slate-500 rounded-3xl text-[11px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all">
                            Batal
                        </button>
                        <button type="submit"
                                class="py-5 bg-[#001f3f] text-white rounded-3xl text-[11px] font-black uppercase tracking-widest hover:bg-[#002d5a] transition-all shadow-xl shadow-[#001f3f]/10">
                            Simpan Nilai
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openGradingModal(presensiId, studentName, currentNilai, currentAsistensi, currentCatatan) {
            document.getElementById('modalPresensiId').value = presensiId;
            document.getElementById('modalStudentName').textContent = studentName;
            document.getElementById('modalScoreInput').value = currentNilai;
            document.getElementById('modalAsistensiInput').value = currentAsistensi;
            document.getElementById('modalNotesInput').value = currentCatatan;
            
            const modal = document.getElementById('gradingModal');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeGradingModal() {
            const modal = document.getElementById('gradingModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Search Filter
        document.getElementById('studentSearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#studentTable tbody tr');
            
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    </script>
@endsection
