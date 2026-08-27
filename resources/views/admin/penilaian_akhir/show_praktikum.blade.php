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
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.penilaian-akhir.export', array_merge([$praktikum->id], request()->query())) }}"
                    class="inline-flex items-center gap-2 h-9 px-4 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-bold uppercase tracking-widest rounded-lg shadow-lg shadow-emerald-600/20 transition-all">
                    <i class="fas fa-file-excel text-xs"></i>
                    Export Excel
                </a>
                <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                    <a href="{{ route('admin.penilaian-akhir.index') }}" class="hover:text-zinc-900 transition-colors">Penilaian Akhir</a>
                    <span>/</span>
                    <span class="text-zinc-900 font-semibold">{{ $praktikum->kode_praktikum }}</span>
                </div>
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
                            Import Nilai Dosen
                        </h3>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.penilaian-akhir.template', $praktikum->id) }}"
                                class="inline-flex items-center gap-1.5 h-7 px-3 bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-[9px] font-bold uppercase tracking-widest rounded-md border border-zinc-200 transition-colors">
                                <i class="fas fa-download text-[8px]"></i>
                                Download Template
                            </a>
                            <span class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest">Excel / Spreadsheet</span>
                        </div>
                    </div>
                    <form action="{{ route('admin.penilaian-akhir.import', $praktikum->id) }}" method="POST" enctype="multipart/form-data" class="mt-4 flex flex-col sm:flex-row gap-4 items-end">
                        @csrf
                        <div class="flex-grow w-full space-y-1.5">
                            <label class="text-[9px] font-black text-zinc-400 uppercase tracking-widest pl-1">Pilih File Spreadsheet (.xlsx, .xls)</label>
                            <div class="flex items-center justify-center w-full">
                                <label id="excel-dropzone" class="flex flex-col items-center justify-center w-full h-24 border-2 border-zinc-300 border-dashed rounded-lg cursor-pointer bg-zinc-50 hover:bg-zinc-100 transition-all duration-200">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i class="fas fa-cloud-upload-alt text-zinc-400 text-2xl mb-2 transition-transform duration-200" id="upload-icon"></i>
                                        <p class="text-xs text-zinc-500" id="file-chosen-text"><span class="font-bold">Klik untuk unggah</span> atau seret file ke sini</p>
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
                    <span class="font-bold"><i class="fas fa-info-circle mr-1"></i> PANDUAN:</span><br>
                    • <span class="font-bold">Download Template</span> → File Excel terpisah per kelas (P, Q, V) dengan NPM & Nama sudah terisi. Dosen tinggal mengisi nilai.<br>
                    • <span class="font-bold">Format Import</span> → Sheet <b>NILAI</b>: Kolom <b>A</b> NPM, Kolom <b>B</b> Nama, lalu kolom berikutnya untuk Nilai Dosen per modul sesuai jumlah modul.
                </div>
            </div>
        </div>

        <!-- Grade Matrix Table Card -->
        <div class="mt-6 rounded-xl border border-zinc-200 bg-white shadow-sm overflow-hidden">
            <!-- Table Header & Search/Filter Controls -->
            <div class="p-5 border-b border-zinc-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-zinc-50/40">
                <div>
                    <h3 class="text-sm font-bold text-zinc-800 uppercase tracking-wider leading-none">Matriks Penilaian Akhir</h3>
                    <p class="text-[10px] text-zinc-400 font-medium mt-1">Scroll ke samping untuk melihat detail nilai praktikum, asistensi, laporan, TA, dan dosen.</p>
                </div>

                <!-- Search & Aslab Filter Form -->
                <form id="filter-form" method="GET" action="{{ route('admin.penilaian-akhir.praktikum', $praktikum->id) }}" class="flex flex-col sm:flex-row items-center gap-3">
                    <!-- Search Input -->
                    <div class="relative w-full sm:w-64">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 text-xs"></i>
                        <input type="text" id="search-input" name="search" value="{{ request('search') }}" placeholder="Cari NPM atau Nama..."
                            autocomplete="off"
                            class="w-full h-9 pl-9 pr-8 text-xs bg-white border border-zinc-200 rounded-lg shadow-sm focus:outline-none focus:border-[#001f3f] focus:ring-1 focus:ring-[#001f3f] transition-all">
                        @if(request('search'))
                            <a href="{{ route('admin.penilaian-akhir.praktikum', array_merge([$praktikum->id], request()->except('search'))) }}" 
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-600 text-xs" title="Hapus pencarian">
                                <i class="fas fa-times-circle"></i>
                            </a>
                        @endif
                    </div>

                    <!-- Aslab Filter Select -->
                    <div class="relative w-full sm:w-52">
                        <select name="aslab_id" onchange="this.form.submit()"
                            class="w-full h-9 px-3 text-xs bg-white border border-zinc-200 rounded-lg shadow-sm focus:outline-none focus:border-[#001f3f] focus:ring-1 focus:ring-[#001f3f] font-medium text-zinc-700 transition-all cursor-pointer">
                            <option value="">-- Semua Aslab --</option>
                            <option value="none" {{ request('aslab_id') === 'none' ? 'selected' : '' }}>-- Tanpa Aslab --</option>
                            @foreach($aslabs as $aslab)
                                <option value="{{ $aslab->id }}" {{ request('aslab_id') == $aslab->id ? 'selected' : '' }}>
                                    Aslab: {{ $aslab->user->name ?? 'Aslab' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Dosen Pembimbing Filter Select -->
                    <div class="relative w-full sm:w-56">
                        <select name="dosen_pengampu" onchange="this.form.submit()"
                            class="w-full h-9 px-3 text-xs bg-white border border-zinc-200 rounded-lg shadow-sm focus:outline-none focus:border-[#001f3f] focus:ring-1 focus:ring-[#001f3f] font-medium text-zinc-700 transition-all cursor-pointer">
                            <option value="">-- Semua Dosen Pembimbing --</option>
                            <option value="none" {{ request('dosen_pengampu') === 'none' ? 'selected' : '' }}>-- Tanpa Dosen --</option>
                            @foreach($dosensList as $dosenName)
                                <option value="{{ $dosenName }}" {{ request('dosen_pengampu') == $dosenName ? 'selected' : '' }}>
                                    Dosen: {{ $dosenName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Submit & Reset Buttons -->
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="submit" class="h-9 px-4 bg-[#001f3f] hover:bg-[#002d5a] text-white text-xs font-bold uppercase tracking-wider rounded-lg transition-all shadow-sm">
                            <i class="fas fa-filter text-[10px] mr-1"></i> Filter
                        </button>
                        @if(request('search') || request('aslab_id') || request('dosen_pengampu'))
                            <a href="{{ route('admin.penilaian-akhir.praktikum', $praktikum->id) }}" 
                                class="h-9 px-3 bg-zinc-100 hover:bg-zinc-200 text-zinc-600 text-xs font-bold rounded-lg transition-all flex items-center justify-center border border-zinc-200"
                                title="Reset Filter">
                                <i class="fas fa-undo text-[10px]"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Sticky columns horizontally scrollable table container -->
            <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-zinc-200">
                <table class="w-full text-left border-collapse" style="min-width: 1600px;">
                    <thead>
                        <tr class="bg-zinc-50/80 border-b border-zinc-200 text-[10px] font-black text-zinc-500 uppercase tracking-widest">
                            <th class="sticky left-0 bg-zinc-50 z-20 px-6 py-4 min-w-[140px] max-w-[140px] w-[140px] border-r border-zinc-200 [will-change:transform]">NPM</th>
                            <th class="sticky left-[140px] bg-zinc-50 z-20 px-6 py-4 min-w-[200px] max-w-[200px] w-[200px] border-r border-zinc-200 [will-change:transform]">Nama</th>
                            <th class="sticky left-[340px] bg-zinc-50 z-20 px-4 py-4 text-center min-w-[80px] max-w-[80px] w-[80px] border-r-2 border-zinc-200 shadow-[3px_0_6px_-2px_rgba(0,0,0,0.06)] text-zinc-800 font-bold [will-change:transform]">Aksi</th>
                            
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
                            <th class="px-6 py-4 text-center bg-zinc-100 text-zinc-800 font-bold">Status</th>
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
                            <tr class="hover:bg-zinc-50/30 transition-colors {{ $isGugur ? 'bg-zinc-50/60 opacity-60' : '' }} grade-row"
                                data-npm="{{ strtolower($pendaftaran->praktikan->npm) }}"
                                data-name="{{ strtolower($pendaftaran->praktikan->user->name) }}"
                                data-dosen="{{ strtolower($pendaftaran->dosen_pengampu ?? '') }}">
                                <!-- Sticky Columns -->
                                <td class="sticky left-0 bg-white z-10 px-6 py-4 font-bold text-zinc-900 border-r border-zinc-100 [will-change:transform]">
                                    {{ $pendaftaran->praktikan->npm }}
                                </td>
                                <td class="sticky left-[140px] bg-white z-10 px-6 py-4 font-semibold text-zinc-700 border-r border-zinc-100 uppercase [will-change:transform]" title="{{ $pendaftaran->praktikan->user->name }}">
                                    <div class="font-bold text-zinc-900 truncate max-w-[180px]">{{ $pendaftaran->praktikan->user->name }}</div>
                                    @if($pendaftaran->aslab && $pendaftaran->aslab->user)
                                        <span class="text-[9px] font-semibold text-indigo-600 tracking-tight block capitalize normal-case truncate max-w-[180px] mt-0.5">
                                            <i class="fas fa-user-tie text-[8px] mr-0.5 text-indigo-400"></i> {{ $pendaftaran->aslab->user->name }}
                                        </span>
                                    @else
                                        <span class="text-[9px] font-medium text-zinc-300 block leading-tight mt-0.5">Tanpa Aslab</span>
                                    @endif
                                    @if($pendaftaran->dosen_pengampu)
                                        <span class="text-[9px] font-semibold text-emerald-700 tracking-tight block capitalize normal-case truncate max-w-[180px] mt-0.5" title="Dosen Pembimbing/Pengampu">
                                            <i class="fas fa-user-graduate text-[8px] mr-0.5 text-emerald-500"></i> Dosbing: {{ $pendaftaran->dosen_pengampu }}
                                        </span>
                                    @endif
                                </td>
                                <td class="sticky left-[340px] bg-white z-10 px-4 py-4 text-center min-w-[80px] max-w-[80px] w-[80px] border-r-2 border-zinc-200 shadow-[3px_0_6px_-2px_rgba(0,0,0,0.06)] [will-change:transform]">
                                    <button onclick='openEditModal("{{ $pendaftaran->id }}", "{{ addslashes($pendaftaran->praktikan->user->name) }}", "{{ $pendaftaran->praktikan->npm }}", @json($g), @json($gradeData["prak_scores"]), @json($gradeData["ast_scores"]))'
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg hover:bg-[#001f3f]/5 text-[#001f3f] border border-zinc-200 transition-colors shadow-sm"
                                            title="Ubah & Override Nilai">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>
                                </td>

                                @for($i = 1; $i <= $praktikum->jumlah_modul; $i++)
                                    @php
                                        $prakScore = $gradeData['prak_scores'][$i] ?? 0;
                                        $astScore = $gradeData['ast_scores'][$i] ?? 0;
                                        $dosArr = $g['nilai_dosen'] ?? [];
                                        if (is_array($dosArr)) {
                                            if (isset($dosArr[$i])) {
                                                $dosScore = $dosArr[$i];
                                            } elseif (isset($dosArr[(string)$i])) {
                                                $dosScore = $dosArr[(string)$i];
                                            } elseif (isset($dosArr['Modul ' . $i])) {
                                                $dosScore = $dosArr['Modul ' . $i];
                                            } else {
                                                $dosScore = 0;
                                            }
                                        } else {
                                            $dosScore = 0;
                                        }
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
                                
                                <td class="px-6 py-4 text-center">
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
                        <tr id="no-search-results-row" style="display: none;">
                            <td colspan="25" class="px-6 py-12 text-center text-zinc-400">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-search text-3xl opacity-20 mb-3"></i>
                                    <p class="font-black uppercase tracking-widest text-[10px]">Data Tidak Ditemukan</p>
                                    <p class="text-[10px] italic font-medium mt-1 tracking-tight">Tidak ada praktikan yang sesuai dengan kata kunci pencarian.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Manual Override Edit Modal -->
    <div id="modal-edit-nilai" class="fixed inset-0 z-[60] hidden bg-zinc-900/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full max-w-lg overflow-hidden border border-zinc-200 shadow-xl">
            <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between bg-zinc-50/50">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-lg bg-[#001f3f] flex items-center justify-center text-white shadow-md shadow-[#001f3f]/20">
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
            
            <form id="form-edit-nilai" method="POST" class="p-6 space-y-4 max-h-[75vh] overflow-y-auto overscroll-contain scrollbar-thin [will-change:transform]">
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

                <!-- Module Grades Input Loop (Prak, Ast, Dosen) -->
                <div class="space-y-3">
                    <h4 class="text-[9px] font-black text-zinc-400 uppercase tracking-wider border-b border-zinc-100 pb-1 flex items-center gap-1.5">
                        <i class="fas fa-list-ol text-[#001f3f]"></i>
                        Nilai Per Modul (Praktikum, Asistensi, Dosen)
                    </h4>
                    <div class="space-y-2.5">
                        @for($i = 1; $i <= $praktikum->jumlah_modul; $i++)
                            <div class="p-3 bg-zinc-50/80 border border-zinc-200/80 rounded-xl space-y-2">
                                <span class="text-[10px] font-black text-zinc-700 uppercase tracking-wider flex items-center gap-1.5">
                                    <i class="fas fa-cube text-[#001f3f]"></i> Modul {{ $i }}
                                </span>
                                <div class="grid grid-cols-3 gap-2">
                                    <div>
                                        <label class="text-[8px] font-bold text-zinc-500 uppercase tracking-tight block mb-1">Nilai Prak</label>
                                        <input type="number" name="nilai_praktikum[{{ $i }}]" id="input-nilai-prak-{{ $i }}" min="0" max="100" required
                                            class="flex h-9 w-full rounded-lg border border-zinc-200 bg-white px-2.5 py-1 text-xs font-semibold focus:border-[#001f3f] focus:ring-1 focus:ring-[#001f3f] outline-none">
                                    </div>
                                    <div>
                                        <label class="text-[8px] font-bold text-zinc-500 uppercase tracking-tight block mb-1">Nilai Ast</label>
                                        <input type="number" name="nilai_asistensi[{{ $i }}]" id="input-nilai-ast-{{ $i }}" min="0" max="100" required
                                            class="flex h-9 w-full rounded-lg border border-zinc-200 bg-white px-2.5 py-1 text-xs font-semibold focus:border-[#001f3f] focus:ring-1 focus:ring-[#001f3f] outline-none">
                                    </div>
                                    <div>
                                        <label class="text-[8px] font-bold text-zinc-500 uppercase tracking-tight block mb-1">Nilai Dosen</label>
                                        <input type="number" name="nilai_dosen[{{ $i }}]" id="input-nilai-dosen-{{ $i }}" min="0" max="100" required
                                            class="flex h-9 w-full rounded-lg border border-zinc-200 bg-white px-2.5 py-1 text-xs font-semibold focus:border-[#001f3f] focus:ring-1 focus:ring-[#001f3f] outline-none">
                                    </div>
                                </div>
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
                                class="flex h-10 w-full rounded-lg border border-zinc-200 bg-white px-3 py-1 text-sm font-semibold focus:border-[#001f3f] focus:ring-1 focus:ring-[#001f3f] outline-none">
                        </div>
                        @if($praktikum->ada_tugas_akhir)
                            <div class="space-y-1.5">
                                <label class="text-[9px] font-bold text-zinc-500 uppercase tracking-widest pl-1">Nilai Tugas Akhir</label>
                                <input type="number" name="nilai_tugas_akhir" id="input-nilai-ta" min="0" max="100" required
                                    class="flex h-10 w-full rounded-lg border border-zinc-200 bg-white px-3 py-1 text-sm font-semibold focus:border-[#001f3f] focus:ring-1 focus:ring-[#001f3f] outline-none">
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
                <div class="flex items-center justify-between gap-3 pt-4 border-t border-zinc-100">
                    <button type="button" id="btn-delete-nilai" onclick="confirmDeleteNilai()"
                        class="inline-flex h-9 items-center justify-center rounded-md bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 px-4 text-xs font-bold transition-colors gap-1.5">
                        <i class="fas fa-trash-alt text-xs"></i>
                        Hapus / Reset
                    </button>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="closeEditModal()"
                            class="inline-flex h-9 items-center justify-center rounded-md border border-zinc-200 px-5 text-xs font-bold text-zinc-500 hover:bg-zinc-50 hover:text-zinc-700 transition-colors">
                            BATAL
                        </button>
                        <button type="submit"
                            class="inline-flex h-9 items-center justify-center rounded-md bg-[#001f3f] px-6 text-xs font-bold text-white shadow-lg shadow-[#001f3f]/20 transition-all hover:bg-[#002d5a]">
                            SIMPAN PERUBAHAN
                        </button>
                    </div>
                </div>
            </form>

            <!-- Hidden Delete Form -->
            <form id="form-delete-nilai" method="POST" class="hidden">
                @csrf
                @method('DELETE')
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

        function openEditModal(pendaftaranId, studentName, npm, grades, prakScores, astScores) {
            const modal = document.getElementById('modal-edit-nilai');
            const form = document.getElementById('form-edit-nilai');
            
            // Set form action route
            form.action = `/administrator/penilaian-akhir/${pendaftaranId}`;

            // Set delete form action route & show/hide delete button
            const deleteForm = document.getElementById('form-delete-nilai');
            const deleteBtn = document.getElementById('btn-delete-nilai');
            if (deleteForm) {
                deleteForm.action = `/administrator/penilaian-akhir/${pendaftaranId}`;
            }
            if (deleteBtn) {
                if (grades && grades.id) {
                    deleteBtn.classList.remove('hidden');
                } else {
                    deleteBtn.classList.add('hidden');
                }
            }
            
            // Populate student details
            document.getElementById('modal-student-name').innerText = studentName;
            document.getElementById('modal-student-npm').innerText = npm;
            
            // Populate module scores (Prak, Ast, Dosen)
            const jumlahModul = {{ $praktikum->jumlah_modul }};
            for (let i = 1; i <= jumlahModul; i++) {
                const inputPrak = document.getElementById('input-nilai-prak-' + i);
                if (inputPrak) {
                    inputPrak.value = (prakScores && prakScores[i] !== undefined) ? prakScores[i] : 0;
                }

                const inputAst = document.getElementById('input-nilai-ast-' + i);
                if (inputAst) {
                    inputAst.value = (astScores && astScores[i] !== undefined) ? astScores[i] : 0;
                }

                const inputDos = document.getElementById('input-nilai-dosen-' + i);
                if (inputDos) {
                    let dosVal = 0;
                    if (grades && grades.nilai_dosen) {
                        const nd = grades.nilai_dosen;
                        if (nd[i] !== undefined && nd[i] !== null) {
                            dosVal = nd[i];
                        } else if (nd[String(i)] !== undefined && nd[String(i)] !== null) {
                            dosVal = nd[String(i)];
                        } else if (nd['Modul ' + i] !== undefined && nd['Modul ' + i] !== null) {
                            dosVal = nd['Modul ' + i];
                        }
                    }
                    inputDos.value = dosVal;
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

        function confirmDeleteNilai() {
            Swal.fire({
                title: 'Hapus / Reset Nilai?',
                text: 'Nilai override praktikan akan dihapus dan dikembalikan ke perhitungan otomatis.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-delete-nilai').submit();
                }
            });
        }

        // --- Live Search & Drag Drop Functionality ---
        document.addEventListener('DOMContentLoaded', function () {
            // Drag & Drop File Upload
            const dropzone = document.getElementById('excel-dropzone');
            const fileInput = document.getElementById('file_excel_input');
            const uploadIcon = document.getElementById('upload-icon');

            if (dropzone && fileInput) {
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropzone.addEventListener(eventName, function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                    }, false);
                });

                ['dragenter', 'dragover'].forEach(eventName => {
                    dropzone.addEventListener(eventName, function () {
                        dropzone.classList.add('border-emerald-500', 'bg-emerald-50/60', 'ring-2', 'ring-emerald-500/20');
                        if (uploadIcon) uploadIcon.classList.add('scale-125', 'text-emerald-600');
                    }, false);
                });

                ['dragleave', 'dragend', 'drop'].forEach(eventName => {
                    dropzone.addEventListener(eventName, function () {
                        dropzone.classList.remove('border-emerald-500', 'bg-emerald-50/60', 'ring-2', 'ring-emerald-500/20');
                        if (uploadIcon) uploadIcon.classList.remove('scale-125', 'text-emerald-600');
                    }, false);
                });

                dropzone.addEventListener('drop', function (e) {
                    const dt = e.dataTransfer;
                    const files = dt.files;

                    if (files && files.length > 0) {
                        const file = files[0];
                        const ext = file.name.split('.').pop().toLowerCase();
                        if (ext === 'xlsx' || ext === 'xls') {
                            fileInput.files = files;
                            updateFileText(fileInput);
                        } else {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Format File Salah',
                                    text: 'Silakan unggah file dengan format .xlsx atau .xls',
                                    confirmButtonColor: '#001f3f'
                                });
                            } else {
                                alert('Silakan unggah file dengan format .xlsx atau .xls');
                            }
                        }
                    }
                }, false);
            }

            const searchInput = document.getElementById('search-input');
            const filterForm = document.getElementById('filter-form');

            if (searchInput && filterForm) {
                // Restore cursor focus & position if search query exists
                if (searchInput.value) {
                    const len = searchInput.value.length;
                    searchInput.focus();
                    searchInput.setSelectionRange(len, len);
                }

                let searchTimeout = null;

                searchInput.addEventListener('input', function () {
                    const query = this.value.toLowerCase().trim();

                    // 1. Instant Client-Side Row Filtering (Zero latency)
                    const rows = document.querySelectorAll('tbody tr.grade-row');
                    let visibleCount = 0;

                    rows.forEach(function (row) {
                        const npm = (row.getAttribute('data-npm') || '').toLowerCase();
                        const name = (row.getAttribute('data-name') || '').toLowerCase();
                        const dosen = (row.getAttribute('data-dosen') || '').toLowerCase();

                        if (query === '' || npm.includes(query) || name.includes(query) || dosen.includes(query)) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    const noDataRow = document.getElementById('no-search-results-row');
                    if (noDataRow) {
                        noDataRow.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
                    }

                    // 2. Debounced Form Submission (Auto submit after 400ms typing pause)
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function () {
                        filterForm.submit();
                    }, 400);
                });
            }
        });
    </script>
@endsection
