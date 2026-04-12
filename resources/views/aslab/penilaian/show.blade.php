@extends('layouts.admin')

@section('title', 'Penilaian Praktikan')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-start justify-between">
            <div>
                <a href="{{ route('aslab.penilaian.index') }}" class="inline-flex items-center gap-2 text-[10px] font-black text-zinc-400 uppercase tracking-widest hover:text-zinc-900 transition-colors group mb-2">
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                    Kembali ke Jadwal
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 uppercase">{{ $jadwal->judul_modul }}</h1>
                <p class="text-sm text-zinc-500 mt-1 italic">
                    {{ $jadwal->praktikum->nama_praktikum }} • Ruangan: {{ $jadwal->ruangan ?? 'LAB RPL' }}
                </p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <div class="bg-zinc-50 border border-zinc-200 px-4 py-2 rounded-lg text-right">
                    <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">Hadir Di Sesi Ini</p>
                    <p class="text-lg font-black text-zinc-900 leading-none">{{ $jadwal->presensis()->where('jadwal_id', $jadwal->id)->where('status', 'hadir')->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Student List Container -->
        <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden min-h-[400px]">
            <div class="p-6 pb-4 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-zinc-100 bg-zinc-50/30">
                <div>
                    <h3 class="text-sm font-bold text-zinc-900 uppercase tracking-tight">Daftar Praktikan Terdaftar</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Seluruh mahasiswa yang terdaftar di praktikum/sesi ini.</p>
                </div>
                <div class="relative group">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 text-xs"></i>
                    <input type="text" id="studentSearch" placeholder="Cari Nama atau NPM..." 
                           class="h-9 w-full md:w-64 rounded-md border border-zinc-200 bg-white px-9 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left" id="studentTable">
                    <thead class="bg-zinc-50 border-b border-zinc-100 text-zinc-500 font-medium h-10">
                        <tr>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider w-12 text-center">NO</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">NPM</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">Nama Praktikan</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-[10px] uppercase tracking-wider">Sesi</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-center text-[10px] uppercase tracking-wider">Nilai Live</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-center text-[10px] uppercase tracking-wider">Nilai Asistensi</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 text-right text-[10px] uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 text-zinc-900">
                        @forelse($pendaftarans as $index => $pendaftaran)
                            @php
                                $praktikan = $pendaftaran->praktikan;
                                // Get presensi for this student in this specific schedule
                                $presensi = $pendaftaran->presensis->first();
                                $nilai = $presensi ? $presensi->penilaian : null;
                                
                                // Improved matching logic: Find by module number if exact title doesn't match
                                $currentModulNumber = null;
                                if (preg_match('/Modul\s+(\d+)/i', $jadwal->judul_modul, $matches)) {
                                    $currentModulNumber = (int)$matches[1];
                                }

                                $tugasAsistensi = $pendaftaran->tugasAsistensis->first(function($t) use ($jadwal, $currentModulNumber) {
                                    // 1. Try exact match
                                    if (strtolower($t->judul) === strtolower($jadwal->judul_modul)) return true;
                                    
                                    // 2. Try matching by module number
                                    if ($currentModulNumber && preg_match('/Modul\s+(\d+)/i', $t->judul, $m)) {
                                        return (int)$m[1] === $currentModulNumber;
                                    }
                                    
                                    return false;
                                });

                                $nilaiAsistensi = $tugasAsistensi ? $tugasAsistensi->nilai : null;
                            @endphp
                            <tr class="hover:bg-zinc-50/50 transition-colors">
                                <td class="px-6 py-4 text-center text-zinc-400 font-medium">{{ $pendaftarans->firstItem() + $index }}</td>
                                <td class="px-6 py-4">
                                    <span class="text-[11px] font-mono font-bold tracking-tight text-zinc-600">{{ $praktikan->npm }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-bold text-zinc-900 uppercase tracking-tight">{{ $praktikan->user->name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-bold text-zinc-600 uppercase">{{ $pendaftaran->sesi->nama_sesi }}</span>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            @if($presensi)
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500" title="Mahasiswa Hadir"></span>
                                                <span class="text-[8px] font-black text-emerald-600 uppercase">Hadir</span>
                                            @else
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500" title="Mahasiswa Tidak Hadir"></span>
                                                <span class="text-[8px] font-black text-rose-600 uppercase">Alpha</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($nilai)
                                        <div class="flex items-center justify-center gap-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-black bg-emerald-50 text-emerald-600 border border-emerald-100">
                                                {{ $nilai->nilai }}
                                            </span>
                                            @if($nilai->catatan)
                                                <i class="fas fa-info-circle text-zinc-300 cursor-help" title="{{ $nilai->catatan }}"></i>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-[10px] text-zinc-300 font-bold uppercase tracking-widest">---</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($nilaiAsistensi !== null)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-black bg-blue-50 text-blue-600 border border-blue-100">
                                            {{ $nilaiAsistensi }}
                                        </span>
                                    @else
                                        <span class="text-[10px] text-zinc-300 font-bold uppercase tracking-widest">---</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($presensi)
                                        <button type="button" 
                                                onclick="openGradingModal('{{ $presensi->id }}', '{{ $praktikan->user->name }}', '{{ $nilai ? $nilai->nilai : '' }}', '{{ $nilaiAsistensi ?? '' }}', '{{ $nilai ? $nilai->catatan : '' }}')"
                                                class="inline-flex items-center gap-2 px-3 py-1.5 bg-zinc-900 text-white text-[10px] font-bold rounded-md hover:bg-zinc-800 transition-all shadow-sm active:scale-95">
                                            <i class="fas fa-marker mr-1"></i>
                                            NILAI
                                        </button>
                                    @else
                                        <button type="button" disabled
                                                class="inline-flex items-center gap-2 px-3 py-1.5 bg-zinc-100 text-zinc-400 text-[10px] font-bold rounded-md cursor-not-allowed">
                                            <i class="fas fa-lock mr-1"></i>
                                            ABSEN
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center opacity-40">
                                        <i class="fas fa-user-slash text-3xl text-zinc-300 mb-4"></i>
                                        <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.2em]">Belum Ada Praktikan Terdaftar</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-zinc-100 bg-zinc-50/10">
                {{ $pendaftarans->links() }}
            </div>
        </div>
    </div>

    <!-- Grading Modal -->
    <div id="gradingModal" class="fixed inset-0 z-[100] hidden overflow-y-auto outline-none transition-all duration-300">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-zinc-900/40 backdrop-blur-sm transition-opacity" onclick="closeGradingModal()"></div>
            
            <div class="relative bg-white w-full max-w-md rounded-xl shadow-2xl border border-zinc-200 animate-in fade-in zoom-in duration-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between bg-zinc-50/50">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-lg bg-zinc-900 flex items-center justify-center text-white shadow-lg">
                            <i class="fas fa-marker text-xs"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-zinc-900 uppercase tracking-tight text-sm">Input Nilai Live</h3>
                            <p class="text-[10px] text-zinc-500 font-medium truncate max-w-[200px]" id="modalStudentName"></p>
                        </div>
                    </div>
                    <button onclick="closeGradingModal()" class="h-8 w-8 flex items-center justify-center rounded-lg hover:bg-zinc-100 text-zinc-400 transition-colors">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>

                <form action="{{ route('aslab.penilaian.store') }}" method="POST" class="p-6 space-y-5">
                    @csrf
                    <input type="hidden" name="presensi_id" id="modalPresensiId">
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Skor Praktikum</label>
                            <input type="number" name="nilai" id="modalScoreInput" required min="0" max="100" placeholder="0-100"
                                   class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-1 focus:ring-zinc-950 focus:border-zinc-950 outline-none font-black text-center text-lg">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Skor Asistensi</label>
                            <input type="number" name="nilai_asistensi" id="modalAsistensiInput" min="0" max="100" placeholder="0-100"
                                   class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-1 focus:ring-zinc-950 focus:border-zinc-950 outline-none font-black text-center text-lg">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Catatan Aslab (Opsional)</label>
                        <textarea name="catatan" id="modalNotesInput" rows="3" placeholder="Tuliskan feedback..."
                                  class="flex w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-2 text-sm shadow-sm transition-all focus:bg-white focus:ring-1 focus:ring-zinc-950 focus:border-zinc-950 outline-none"></textarea>
                    </div>

                    <div class="pt-2 flex items-center gap-3">
                        <button type="button" onclick="closeGradingModal()"
                                class="flex-1 h-10 bg-zinc-100 text-zinc-600 rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-zinc-200 transition-all">
                            Batal
                        </button>
                        <button type="submit"
                                class="flex-1 h-10 bg-zinc-900 text-white rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-zinc-800 transition-all shadow-lg active:scale-[0.98]">
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
                if (row.querySelector('td[colspan]')) return;
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    </script>
@endsection
