<div id="mahasiswa-section" class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden">
    <div class="p-6 pb-4 flex flex-col md:flex-row items-center justify-between gap-4 border-b border-zinc-100">
        <div class="flex items-center gap-2 flex-1 w-full md:w-auto">
            <div class="relative max-w-sm w-full">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-xs"></i>
                <input type="text" id="studentSearch" placeholder="Cari praktikan (Nama / NPM)..."
                    class="flex h-9 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 pl-9 text-sm shadow-sm transition-colors placeholder:text-zinc-500 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap w-full md:w-auto justify-end">
            <!-- Filter Sesi -->
            <select id="filterSesi"
                class="h-9 rounded-md border border-zinc-200 bg-white px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950 cursor-pointer font-medium text-zinc-700">
                <option value="">-- Semua Sesi --</option>
                @foreach ($praktikum->sesis as $s)
                    <option value="{{ $s->nama_sesi }}">{{ $s->nama_sesi }}</option>
                @endforeach
            </select>

            <!-- Filter Kelulusan -->
            <select id="filterKelulusan"
                class="h-9 rounded-md border border-zinc-200 bg-white px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950 cursor-pointer font-medium text-zinc-700">
                <option value="">-- Status Kelulusan --</option>
                <option value="LULUS">Lulus</option>
                <option value="TIDAK LULUS">Tidak Lulus</option>
                <option value="Belum Ditentukan">Belum Set</option>
            </select>

            <!-- Custom Length -->
            <select id="customLength"
                class="h-9 rounded-md border border-zinc-200 bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
                <option value="10">10 data</option>
                <option value="25">25 data</option>
                <option value="50">50 data</option>
                <option value="100">100 data</option>
            </select>

            <!-- Action Buttons -->
            <a href="{{ route('admin.praktikum.download-template', $praktikum->id) }}" title="Download Template Excel"
                class="inline-flex h-9 items-center justify-center rounded-md border border-sky-200 bg-sky-50 px-3 text-xs font-semibold text-sky-700 hover:bg-sky-100 transition-colors shadow-sm">
                <i class="fas fa-download mr-1.5 text-xs"></i> Template
            </a>

            <button type="button" onclick="document.getElementById('importFile').click()"
                class="inline-flex h-9 items-center justify-center rounded-md bg-[#001f3f] px-3.5 text-xs font-semibold text-white shadow hover:bg-[#002d5a] transition-colors">
                <i class="fas fa-file-import mr-1.5 text-xs"></i> Import
            </button>

            <a href="{{ route('admin.praktikum.export-students', $praktikum->id) }}" title="Export ke Excel"
                class="inline-flex h-9 items-center justify-center rounded-md bg-emerald-600 px-3 text-xs font-semibold text-white shadow hover:bg-emerald-700 transition-colors">
                <i class="fas fa-file-export mr-1.5 text-xs"></i> Export
            </a>
        </div>
    </div>

    <form action="{{ route('admin.praktikum.import-students', $praktikum->id) }}" method="POST" id="importForm" enctype="multipart/form-data" class="hidden">
        @csrf
        <input type="file" name="file" id="importFile" accept=".csv" onchange="previewImport(this)">
    </form>

    {{-- ── TABLE ─────────────────────────────────────────────────── --}}
    <div class="overflow-x-auto">
        <table id="studentTable" class="w-full text-sm text-left">
            <thead class="bg-zinc-50 border-b border-zinc-100 text-zinc-500 font-medium h-10">
                <tr>
                    <th class="w-12 px-6 align-middle"><input type="checkbox" id="selectAll" class="w-3.5 h-3.5 rounded border-zinc-300 text-[#001f3f] focus:ring-[#001f3f] cursor-pointer"></th>
                    <th class="px-6 align-middle font-medium text-zinc-500">MAHASISWA</th>
                    <th class="px-6 align-middle font-medium text-zinc-500">NPM</th>
                    <th class="px-6 align-middle font-medium text-zinc-500">STATUS PENDAFTARAN</th>
                    <th class="px-6 align-middle font-medium text-zinc-500">SESI PRAKTIKUM</th>
                    <th class="px-6 align-middle font-medium text-zinc-500">STATUS KELULUSAN</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-50">
                @foreach($praktikum->pendaftarans as $p)
                <tr class="hover:bg-slate-50/60 transition-colors">
                    <td class="px-4 py-3.5"><input type="checkbox" class="student-checkbox w-3.5 h-3.5 rounded border-zinc-300 text-[#001f3f] focus:ring-[#001f3f] cursor-pointer" value="{{ $p->id }}"></td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl overflow-hidden flex-shrink-0 border border-zinc-100 shadow-sm">
                                @if($p->praktikan->user->profile_picture)
                                    <img src="{{ asset('storage/' . $p->praktikan->user->profile_picture) }}" class="w-full h-full object-cover">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($p->praktikan->user->name) }}&background=001f3f&color=ffffff&bold=true" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="font-bold text-zinc-900 text-[13px] truncate leading-tight">{{ $p->praktikan->user->name }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-3.5 font-mono text-xs font-semibold text-zinc-700">
                        {{ $p->praktikan->npm }}
                    </td>
                    <td class="px-6 py-3.5" data-search="{{ $p->status }}">
                        @php
                            $sc = match($p->status) {
                                'verified' => ['c'=>'bg-emerald-50 text-emerald-700 border-emerald-100', 'd'=>'bg-emerald-400'],
                                'pending' => ['c'=>'bg-amber-50 text-amber-700 border-amber-100', 'd'=>'bg-amber-400'],
                                'rejected' => ['c'=>'bg-rose-50 text-rose-700 border-rose-100', 'd'=>'bg-rose-400'],
                                default => ['c'=>'bg-zinc-50 text-zinc-500 border-zinc-100', 'd'=>'bg-zinc-400']
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[9px] font-black border {{ $sc['c'] }} uppercase tracking-wider">
                            <span class="w-1.5 h-1.5 rounded-full {{ $sc['d'] }}"></span>
                            {{ $p->status }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5" data-search="{{ $p->sesi->nama_sesi }}">
                        <div class="relative group/sel max-w-[170px]">
                            <select name="sesi_id" onchange="updateAssignment(this, '{{ route('admin.praktikum.pendaftaran.change-session', $p->id) }}')" data-original-value="{{ $p->sesi_id }}" class="appearance-none w-full h-8 pl-2.5 pr-7 text-[11px] font-semibold text-zinc-700 bg-white border border-zinc-200 rounded-lg focus:border-[#001f3f] cursor-pointer outline-none shadow-sm transition-all">
                                @foreach ($praktikum->sesis as $s)
                                    <option value="{{ $s->id }}" {{ $p->sesi_id == $s->id ? 'selected' : '' }}>{{ $s->nama_sesi }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute right-2 top-1/2 -translate-y-1/2 text-zinc-300 text-[7px] pointer-events-none group-hover/sel:text-zinc-500"></i>
                        </div>
                        <p class="text-[10px] text-zinc-400 mt-1.5 flex items-center gap-1 font-medium italic">
                            <span class="w-1 h-1 rounded-full bg-sky-400"></span>{{ $p->sesi->hari }}, {{ substr($p->sesi->jam_mulai, 0, 5) }}
                        </p>
                    </td>
                    @if(false)
                        <td class="px-4 py-3.5" data-search="{{ $p->aslab ? $p->aslab->user->name : 'Pilih Aslab' }}">
                            <div class="relative group/sel max-w-[180px]">
                                <select name="aslab_id" onchange="updateAssignment(this, '{{ route('admin.praktikum.pendaftaran.assign-aslab', $p->id) }}')" data-original-value="{{ $p->aslab_id }}" class="appearance-none w-full h-8 pl-2.5 pr-7 text-[11px] font-semibold text-zinc-700 bg-white border border-zinc-200 rounded-lg focus:border-[#001f3f] cursor-pointer outline-none shadow-sm transition-all">
                                    <option value="">— Belum Ditugaskan —</option>
                                    @foreach ($praktikum->aslabs as $as)
                                        <option value="{{ $as->id }}" {{ $p->aslab_id == $as->id ? 'selected' : '' }}>{{ $as->user->name }}</option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down absolute right-2 top-1/2 -translate-y-1/2 text-zinc-300 text-[7px] pointer-events-none group-hover/sel:text-zinc-500"></i>
                            </div>
                        </td>
                    @endif
                    <td class="px-4 py-3.5" data-search="{{ $p->penilaianAkhir?->status_kelulusan ?? 'Belum Ditentukan' }}">
                        @php
                            $gradStatus = $p->penilaianAkhir?->status_kelulusan;
                            $gradClass = match($gradStatus) {
                                'LULUS' => 'border-emerald-300 bg-emerald-50 text-emerald-800 font-bold',
                                'TIDAK LULUS' => 'border-rose-300 bg-rose-50 text-rose-800 font-bold',
                                default => 'border-zinc-200 bg-white text-zinc-600'
                            };
                        @endphp
                        <div class="relative group/sel max-w-[170px]">
                            <select name="status_kelulusan" onchange="updateAssignment(this, '{{ route('admin.praktikum.pendaftaran.update-graduation-status', $p->id) }}')" data-original-value="{{ $gradStatus }}" class="appearance-none w-full h-8 pl-2.5 pr-7 text-[11px] rounded-lg focus:border-[#001f3f] cursor-pointer outline-none shadow-sm transition-all {{ $gradClass }}">
                                <option value="" class="bg-white text-zinc-700">— Belum Ditentukan —</option>
                                <option value="LULUS" class="bg-emerald-50 text-emerald-800 font-bold" {{ $gradStatus === 'LULUS' ? 'selected' : '' }}>LULUS</option>
                                <option value="TIDAK LULUS" class="bg-rose-50 text-rose-800 font-bold" {{ $gradStatus === 'TIDAK LULUS' ? 'selected' : '' }}>TIDAK LULUS</option>
                            </select>
                            <i class="fas fa-chevron-down absolute right-2 top-1/2 -translate-y-1/2 text-zinc-300 text-[7px] pointer-events-none group-hover/sel:text-zinc-500"></i>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>