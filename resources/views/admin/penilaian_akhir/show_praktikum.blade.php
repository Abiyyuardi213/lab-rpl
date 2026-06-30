@extends('layouts.admin')

@section('title', 'Rekap Penilaian Akhir')

@section('content')
    <div class="space-y-6">
        <!-- Header & Breadcrumbs -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 uppercase">Rekap Penilaian Akhir</h1>
                <p class="text-sm text-zinc-500 font-medium italic mt-0.5">"{{ $praktikum->nama_praktikum }} ({{ $praktikum->kode_praktikum }})"</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('admin.penilaian-akhir.index') }}" class="hover:text-zinc-900 transition-colors">Penilaian Akhir</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">{{ $praktikum->kode_praktikum }}</span>
            </div>
        </div>

        <!-- Upper Options Cards: Course Info & Excel Import -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Practical Course Information -->
            <div class="bg-white rounded-xl border border-zinc-200 p-6 flex flex-col justify-between shadow-sm">
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest leading-none">Detail Praktikum</span>
                        <span class="bg-zinc-100 text-zinc-800 text-[9px] font-bold px-2 py-0.5 rounded border border-zinc-200 uppercase tracking-tight">
                            {{ $praktikum->status_praktikum }}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">Mata Kuliah</p>
                            <p class="text-sm font-black text-zinc-800 uppercase mt-1 leading-tight">{{ $praktikum->nama_praktikum }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">Periode</p>
                            <p class="text-sm font-black text-zinc-800 mt-1 leading-tight">{{ $praktikum->periode_praktikum }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">Sesi Modul</p>
                            <p class="text-sm font-black text-zinc-800 mt-1 leading-tight">{{ $praktikum->jumlah_modul }} Modul</p>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">Tugas Akhir (TA)</p>
                            <p class="text-sm font-black mt-1 leading-tight {{ $praktikum->ada_tugas_akhir ? 'text-emerald-600' : 'text-zinc-400' }}">
                                {{ $praktikum->ada_tugas_akhir ? 'Aktif' : 'Tidak Ada' }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="pt-6 border-t border-zinc-100 mt-4 flex items-center justify-between text-xs font-bold text-zinc-500">
                    <span>Total Praktikan Terdaftar:</span>
                    <span class="text-sm font-black text-zinc-900">{{ count($grades) }} Orang</span>
                </div>
            </div>

            <!-- Excel Import Form -->
            <div class="lg:col-span-2 bg-white rounded-xl border border-zinc-200 p-6 flex flex-col justify-between shadow-sm">
                <div>
                    <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                        <h3 class="text-xs font-black text-zinc-900 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-file-excel text-emerald-600 text-sm"></i>
                            Import Nilai Dosen & Dropout (Gugur)
                        </h3>
                        <span class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest">Excel / Spreadsheet</span>
                    </div>
                    <form action="{{ route('admin.penilaian-akhir.import', $praktikum->id) }}" method="POST" enctype="multipart/form-data" class="mt-4 flex flex-col sm:flex-row gap-4 items-end">
                        @csrf
                        <div class="flex-grow w-full space-y-1.5">
                            <label class="text-[9px] font-black text-zinc-400 uppercase tracking-widest pl-1">Pilih File Spreadsheet (.xlsx, .xls)</label>
                            <div class="flex items-center justify-center w-full">
                                <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-zinc-300 border-dashed rounded-lg cursor-pointer bg-zinc-50 hover:bg-zinc-100 transition-colors">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i class="fas fa-cloud-upload-alt text-zinc-400 text-2xl mb-2"></i>
                                        <p class="text-xs text-zinc-500" id="file-chosen-text"><span class="font-bold">Klik untuk unggah</span> atau seret file</p>
                                    </div>
                                    <input type="file" name="file_excel" id="file_excel_input" accept=".xlsx,.xls" required class="hidden" onchange="updateFileText(this)" />
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="w-full sm:w-auto h-12 px-6 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold uppercase tracking-widest rounded-lg shadow-lg shadow-emerald-600/20 transition-all flex items-center justify-center gap-2 flex-shrink-0">
                            <i class="fas fa-upload"></i>
                            Unggah & Rekap
                        </button>
                    </form>
                </div>
                <div class="bg-amber-50 border border-amber-200/60 rounded-lg p-3 text-[10px] text-amber-800 mt-4 leading-relaxed font-medium">
                    <span class="font-bold"><i class="fas fa-info-circle mr-1"></i> PANDUAN KOLOM EXCEL:</span><br>
                    • **Sheet 1 (NILAI)**: Baris 1 & 2 Header. Kolom **A**: NPM, Kolom **G, J, M, P**: Nilai Dosen Modul 1-4, Kolom **Q**: Laporan, Kolom **R**: Tugas Akhir (TA).<br>
                    • **Sheet 3 (GUGUR)**: Kolom **A**: NPM, Kolom **D**: Alasan Gugur (mengundurkan diri / tidak memenuhi syarat).
                </div>
            </div>
        </div>

        <!-- Grade Matrix Table -->
        <div class="rounded-xl border border-zinc-200 bg-white shadow-sm overflow-hidden">
            <div class="p-6 border-b border-zinc-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-zinc-50/20">
                <div>
                    <h3 class="text-sm font-bold text-zinc-800 uppercase tracking-wider leading-none">Matriks Penilaian Akhir</h3>
                    <p class="text-[10px] text-zinc-400 font-medium mt-1">Scroll ke samping untuk melihat detail nilai praktikum, asistensi, laporan, TA, dan dosen.</p>
                </div>
                <!-- Custom styling info -->
                <div class="flex gap-2">
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-zinc-500 uppercase px-2 py-1 rounded bg-zinc-100 border border-zinc-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Lulus
                    </span>
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-zinc-500 uppercase px-2 py-1 rounded bg-zinc-100 border border-zinc-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Tidak Lulus
                    </span>
                </div>
            </div>

            <!-- Sticky columns horizontally scrollable table container -->
            <div class="overflow-x-auto select-none scrollbar-thin scrollbar-thumb-zinc-200">
                <table class="w-full text-left border-collapse" style="min-width: 1600px;">
                    <thead>
                        <tr class="bg-zinc-50/80 border-b border-zinc-200 text-[10px] font-black text-zinc-500 uppercase tracking-widest">
                            <th class="sticky left-0 bg-zinc-50 z-20 px-6 py-4 min-w-[140px] max-w-[140px] w-[140px] border-r border-zinc-200 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">NPM</th>
                            <th class="sticky left-[140px] bg-zinc-50 z-20 px-6 py-4 min-w-[200px] max-w-[200px] w-[200px] border-r border-zinc-200 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">Nama</th>
                            
                            <!-- Dynamic Modul Headers -->
                            @for($i = 1; $i <= $praktikum->jumlah_modul; $i++)
                                <th class="px-3 py-4 text-center bg-zinc-100/50 border-r border-zinc-200 text-zinc-700">M{{ $i }} Prak</th>
                                <th class="px-3 py-4 text-center bg-zinc-100/50 border-r border-zinc-200 text-zinc-700">M{{ $i }} Ast</th>
                                <th class="px-3 py-4 text-center bg-zinc-100/50 border-r border-zinc-200 text-zinc-700 font-bold text-slate-800">M{{ $i }} Dos</th>
                            @endfor

                            <th class="px-4 py-4 text-center border-r border-zinc-200 bg-amber-50/50 text-amber-800 font-bold">Lprn</th>
                            @if($praktikum->ada_tugas_akhir)
                                <th class="px-4 py-4 text-center border-r border-zinc-200 bg-amber-50/50 text-amber-800 font-bold">Tugas Akhir</th>
                            @endif

                            <th class="px-4 py-4 text-center border-r border-zinc-200 bg-zinc-100 text-zinc-600">Tot Prak</th>
                            <th class="px-4 py-4 text-center border-r border-zinc-200 bg-zinc-100 text-zinc-600">Tot Ast</th>
                            <th class="px-4 py-4 text-center border-r border-zinc-200 bg-zinc-100 text-zinc-800 font-semibold">Tot Prak+Ast</th>
                            <th class="px-4 py-4 text-center border-r border-zinc-200 bg-zinc-100 text-zinc-600">Tot Dos</th>
                            <th class="px-6 py-4 text-center border-r border-zinc-200 bg-slate-900 text-white font-bold">Nilai Akhir</th>
                            <th class="px-4 py-4 text-center border-r border-zinc-200 bg-slate-850 text-white font-bold">Huruf</th>
                            <th class="px-6 py-4 text-center border-r border-zinc-200 bg-zinc-100 text-zinc-800 font-bold">Status</th>
                            <th class="px-6 py-4 text-center text-zinc-800 font-bold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 text-xs font-medium text-zinc-700">
                        @forelse($grades as $gradeData)
                            @php
                                $pendaftaran = $gradeData['pendaftaran'];
                                $g = $gradeData['grades'];
                                $isDb = $gradeData['is_db'];
                                $isGugur = $g['is_gugur'] ?? false;
                            @endphp
                            <tr class="hover:bg-zinc-50/30 transition-colors {{ $isGugur ? 'bg-zinc-50/60 opacity-60' : '' }}">
                                <!-- Sticky Columns -->
                                <td class="sticky left-0 bg-white z-10 px-6 py-4 font-bold text-zinc-900 border-r border-zinc-100 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">
                                    {{ $pendaftaran->praktikan->npm }}
                                </td>
                                <td class="sticky left-[140px] bg-white z-10 px-6 py-4 font-semibold text-zinc-700 border-r border-zinc-100 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] line-clamp-1 uppercase" title="{{ $pendaftaran->praktikan->user->name }}">
                                    {{ $pendaftaran->praktikan->user->name }}
                                </td>

                                <!-- Dynamic Modul Grades -->
                                @for($i = 1; $i <= $praktikum->jumlah_modul; $i++)
                                    @php
                                        // Retrieve practical score (Prak)
                                        // Match by schedule index
                                        $schedulesList = $pendaftaran->praktikum->jadwals()->orderBy('tanggal', 'asc')->orderBy('waktu_mulai', 'asc')->get();
                                        $sched = $schedulesList->get($i - 1);
                                        $prakScore = 0;
                                        if ($sched) {
                                            $pres = $pendaftaran->presensis->firstWhere('jadwal_id', $sched->id);
                                            $prakScore = ($pres && $pres->penilaian) ? $pres->penilaian->nilai : 0;
                                        }

                                        // Retrieve assistance score (Ast)
                                        $astScore = 0;
                                        if ($sched) {
                                            $tugas = $pendaftaran->tugasAsistensis->firstWhere('judul', $sched->judul_modul);
                                            $astScore = $tugas ? ($tugas->nilai ?? 0) : 0;
                                        }

                                        // Lecturer score
                                        $dosScore = $g['nilai_dosen'][$i] ?? 0;
                                    @endphp
                                    <td class="px-3 py-4 text-center border-r border-zinc-100 bg-zinc-50/10">{{ $prakScore }}</td>
                                    <td class="px-3 py-4 text-center border-r border-zinc-100 bg-zinc-50/10">{{ $astScore }}</td>
                                    <td class="px-3 py-4 text-center border-r border-zinc-100 bg-zinc-100/10 font-bold {{ $dosScore > 0 ? 'text-[#001f3f]' : 'text-zinc-300' }}">{{ $dosScore }}</td>
                                @endfor

                                <!-- Laporan -->
                                <td class="px-4 py-4 text-center border-r border-zinc-100 bg-amber-50/10 font-semibold {{ ($g['nilai_laporan'] ?? 0) > 0 ? 'text-amber-800' : 'text-zinc-300' }}">
                                    {{ $g['nilai_laporan'] ?? 0 }}
                                </td>

                                <!-- Tugas Akhir -->
                                @if($praktikum->ada_tugas_akhir)
                                    <td class="px-4 py-4 text-center border-r border-zinc-100 bg-amber-50/10 font-semibold {{ ($g['nilai_tugas_akhir'] ?? 0) > 0 ? 'text-amber-800' : 'text-zinc-300' }}">
                                        {{ $g['nilai_tugas_akhir'] ?? 0 }}
                                    </td>
                                @endif

                                <!-- Averages -->
                                <td class="px-4 py-4 text-center border-r border-zinc-100 bg-zinc-50/40 text-zinc-500 font-bold">{{ number_format($g['total_praktikum'], 2) }}</td>
                                <td class="px-4 py-4 text-center border-r border-zinc-100 bg-zinc-50/40 text-zinc-500 font-bold">{{ number_format($g['total_asistensi'], 2) }}</td>
                                <td class="px-4 py-4 text-center border-r border-zinc-100 bg-zinc-50/40 text-zinc-900 font-black">{{ number_format($g['total_praktikum_asistensi'], 2) }}</td>
                                <td class="px-4 py-4 text-center border-r border-zinc-100 bg-zinc-50/40 text-zinc-500 font-bold">{{ number_format($g['total_dosen'], 2) }}</td>

                                <!-- Final Output -->
                                <td class="px-6 py-4 text-center border-r border-zinc-100 bg-slate-900/5 text-slate-900 font-black text-sm">{{ number_format($g['nilai_akhir'], 2) }}</td>
                                <td class="px-4 py-4 text-center border-r border-zinc-100 bg-zinc-50/20 text-zinc-900 font-black text-sm">{{ $g['nilai_huruf'] }}</td>
                                
                                <td class="px-6 py-4 text-center border-r border-zinc-100">
                                    @if($isGugur)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-slate-100 text-slate-800 border border-slate-200 uppercase" title="Alasan: {{ $g['alasan_gugur'] }}">
                                            GUGUR
                                        </span>
                                    @elseif($g['status_kelulusan'] === 'LULUS')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">
                                            LULUS
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-rose-50 text-rose-700 border border-rose-200 uppercase">
                                            TIDAK LULUS
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <button onclick='openEditModal("{{ $pendaftaran->id }}", "{{ addslashes($pendaftaran->praktikan->user->name) }}", "{{ $pendaftaran->praktikan->npm }}", @json($g))'
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg hover:bg-[#001f3f]/5 text-[#001f3f] border border-zinc-200 transition-colors shadow-sm"
                                            title="Ubah & Override Nilai">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="20" class="px-6 py-12 text-center text-zinc-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-user-slash text-3xl opacity-20 mb-3"></i>
                                        <p class="font-black uppercase tracking-widest text-[10px]">Belum Ada Praktikan</p>
                                        <p class="text-[10px] italic font-medium mt-1 tracking-tight">Tidak ada praktikan terverifikasi untuk praktikum ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Manual Override Edit Modal -->
    <div id="modal-edit-nilai" class="fixed inset-0 z-[60] hidden bg-zinc-900/40 backdrop-blur-sm flex items-center justify-center p-4 transition-all duration-300">
        <div class="bg-white rounded-xl w-full max-w-lg overflow-hidden shadow-2xl border border-zinc-200 animate-in fade-in zoom-in duration-200">
            <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between bg-zinc-50/50">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-lg bg-[#001f3f] flex items-center justify-center text-white shadow-lg shadow-[#001f3f]/20">
                        <i class="fas fa-user-edit text-xs"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-zinc-900 uppercase tracking-tight leading-none">Override Nilai Akhir</h3>
                        <p class="text-[9px] text-zinc-400 font-bold uppercase mt-1 leading-none">Manual Entry Override</p>
                    </div>
                </div>
                <button onclick="closeEditModal()" class="h-8 w-8 flex items-center justify-center rounded-lg hover:bg-zinc-100 text-zinc-400 transition-colors">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            
            <form id="form-edit-nilai" method="POST" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto scrollbar-thin">
                @csrf
                @method('PUT')
                
                <!-- Student Card Display -->
                <div class="p-4 bg-zinc-50 border border-zinc-100 rounded-lg flex justify-between gap-4">
                    <div>
                        <p class="text-[8px] font-bold text-zinc-400 uppercase tracking-widest">NAMA PRAKTIKAN</p>
                        <p class="text-sm font-black text-zinc-800 uppercase mt-0.5" id="modal-student-name">-</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[8px] font-bold text-zinc-400 uppercase tracking-widest">NPM</p>
                        <p class="text-sm font-black text-zinc-800 mt-0.5" id="modal-student-npm">-</p>
                    </div>
                </div>

                <!-- Lecturer Grades Input Loop -->
                <div class="space-y-3">
                    <h4 class="text-[9px] font-black text-zinc-400 uppercase tracking-wider border-b border-zinc-100 pb-1 flex items-center gap-1.5">
                        <i class="fas fa-chalkboard-teacher"></i>
                        Nilai Dosen (Skor 0 - 100)
                    </h4>
                    <div class="grid grid-cols-2 gap-4">
                        @for($i = 1; $i <= $praktikum->jumlah_modul; $i++)
                            <div class="space-y-1.5">
                                <label class="text-[9px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Modul {{ $i }} Dosen</label>
                                <input type="number" name="nilai_dosen[{{ $i }}]" id="input-nilai-dosen-{{ $i }}" min="0" max="100" required
                                    class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none">
                            </div>
                        @endfor
                    </div>
                </div>

                <!-- Lprn & TA Inputs -->
                <div class="space-y-3 pt-2">
                    <h4 class="text-[9px] font-black text-zinc-400 uppercase tracking-wider border-b border-zinc-100 pb-1 flex items-center gap-1.5">
                        <i class="fas fa-book"></i>
                        Laporan & Tugas Akhir
                    </h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Nilai Laporan</label>
                            <input type="number" name="nilai_laporan" id="input-nilai-laporan" min="0" max="100" required
                                class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none">
                        </div>
                        @if($praktikum->ada_tugas_akhir)
                            <div class="space-y-1.5">
                                <label class="text-[9px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Nilai Tugas Akhir</label>
                                <input type="number" name="nilai_tugas_akhir" id="input-nilai-ta" min="0" max="100" required
                                    class="flex h-10 w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-1 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none">
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Gugur / Dropout inputs -->
                <div class="space-y-3 pt-2 border-t border-zinc-100">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_gugur" value="1" id="input-is-gugur" onchange="toggleGugurFields(this)"
                            class="h-4.5 w-4.5 rounded border-zinc-300 text-[#001f3f] focus:ring-[#001f3f]/20">
                        <label for="input-is-gugur" class="text-[10px] font-black text-rose-600 uppercase tracking-wider cursor-pointer select-none">Tandai Mahasiswa Gugur / Drop</label>
                    </div>

                    <div class="space-y-1.5 hidden transition-all duration-300" id="gugur-reason-container">
                        <label class="text-[9px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Alasan Gugur</label>
                        <textarea name="alasan_gugur" id="input-alasan-gugur" rows="2"
                            class="flex w-full rounded-lg border border-zinc-200 bg-zinc-50/50 px-3 py-2 text-sm shadow-sm transition-all focus:bg-white focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] outline-none"
                            placeholder="Tulis alasan praktikan gugur (pindah kampus, tidak ikut praktikum, dll)"></textarea>
                    </div>
                </div>

                <!-- Form Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-100">
                    <button type="button" onclick="closeEditModal()"
                        class="inline-flex h-9 items-center justify-center rounded-md border border-zinc-200 px-5 text-xs font-bold text-zinc-500 hover:bg-zinc-50 hover:text-zinc-700 transition-colors">
                        BATAL
                    </button>
                    <button type="submit"
                        class="inline-flex h-9 items-center justify-center rounded-md bg-[#001f3f] px-6 text-xs font-bold text-white shadow-lg shadow-[#001f3f]/20 transition-all hover:bg-[#002d5a]">
                        SIMPAN PERUBAHAN
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Javascript handlers -->
    <script>
        function updateFileText(input) {
            const label = document.getElementById('file-chosen-text');
            if (input.files && input.files.length > 0) {
                label.innerHTML = `<span class="font-bold text-emerald-600"><i class="fas fa-file-excel mr-1"></i> ${input.files[0].name}</span> selected`;
            } else {
                label.innerHTML = `<span class="font-bold">Klik untuk unggah</span> atau seret file`;
            }
        }

        function openEditModal(pendaftaranId, studentName, npm, grades) {
            const modal = document.getElementById('modal-edit-nilai');
            const form = document.getElementById('form-edit-nilai');
            
            // Set form action route
            form.action = `/admin/penilaian-akhir/${pendaftaranId}`;
            
            // Populate student details
            document.getElementById('modal-student-name').innerText = studentName;
            document.getElementById('modal-student-npm').innerText = npm;
            
            // Populate lecturer scores
            const jumlahModul = {{ $praktikum->jumlah_modul }};
            for (let i = 1; i <= jumlahModul; i++) {
                const input = document.getElementById('input-nilai-dosen-' + i);
                if (input) {
                    input.value = grades.nilai_dosen ? (grades.nilai_dosen[i] || 0) : 0;
                }
            }
            
            // Populate Lprn
            document.getElementById('input-nilai-laporan').value = grades.nilai_laporan || 0;
            
            // Populate TA
            const taInput = document.getElementById('input-nilai-ta');
            if (taInput) {
                taInput.value = grades.nilai_tugas_akhir || 0;
            }
            
            // Populate Gugur status
            const checkbox = document.getElementById('input-is-gugur');
            checkbox.checked = grades.is_gugur ? true : false;
            toggleGugurFields(checkbox);
            
            // Populate Gugur reason
            document.getElementById('input-alasan-gugur').value = grades.alasan_gugur || '';

            // Show modal
            modal.classList.remove('hidden');
        }

        function closeEditModal() {
            const modal = document.getElementById('modal-edit-nilai');
            modal.classList.add('hidden');
        }

        function toggleGugurFields(checkbox) {
            const reasonContainer = document.getElementById('gugur-reason-container');
            const reasonInput = document.getElementById('input-alasan-gugur');
            if (checkbox.checked) {
                reasonContainer.classList.remove('hidden');
                reasonInput.setAttribute('required', 'required');
            } else {
                reasonContainer.classList.add('hidden');
                reasonInput.removeAttribute('required');
            }
        }
    </script>
@endsection
