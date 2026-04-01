<div id="mahasiswa-section" class="bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden anim-section-fade-in">

    {{-- Card Top Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-4 sm:px-6 py-3.5 border-b border-zinc-100 bg-zinc-50/70">
        {{-- Title + Count --}}
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-[#001f3f] flex items-center justify-center flex-shrink-0">
                <i class="fas fa-users text-white text-xs"></i>
            </div>
            <div>
                <p class="text-xs font-black text-zinc-800 uppercase tracking-wider leading-none">Manajemen Praktikan</p>
                <p class="text-[10px] text-zinc-400 font-medium mt-0.5">
                    Total: <span class="font-black text-[#001f3f]">{{ $praktikum->pendaftarans->count() }}</span> mahasiswa terdaftar
                </p>
            </div>
        </div>
        {{-- View Toggle --}}
        <div class="flex items-center gap-0.5 p-0.5 bg-white border border-zinc-200 rounded-lg shadow-sm self-start sm:self-auto">
            <button id="btnModeTable" onclick="switchMode('table')"
                class="view-mode-btn flex items-center gap-1.5 px-3 py-1.5 text-[10px] font-black uppercase tracking-wider rounded-md transition-all">
                <i class="fas fa-table-list text-[9px]"></i> Tabel
            </button>
            <button id="btnModeKanban" onclick="switchMode('kanban')"
                class="view-mode-btn flex items-center gap-1.5 px-3 py-1.5 text-[10px] font-black uppercase tracking-wider rounded-md transition-all">
                <i class="fas fa-exchange-alt text-[9px]"></i> Transfer
            </button>
        </div>
    </div>

    {{-- ── ACTION BAR ──────────────────────────────────────────── --}}
    <div class="px-4 sm:px-6 py-4 border-b border-zinc-100 bg-[#fafafa]/50 space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center gap-4">
            {{-- Search Area --}}
            <div class="relative group/search flex-1 min-w-0">
                <div class="absolute inset-y-1.5 left-1.5 w-7 rounded-lg bg-zinc-100 flex items-center justify-center pointer-events-none border border-zinc-200 transition-all group-focus-within/search:bg-[#001f3f]/5 group-focus-within/search:border-[#001f3f]/10">
                    <i class="fas fa-search text-[10px] text-zinc-400 group-focus-within/search:text-[#001f3f] transition-all"></i>
                </div>
                <input type="text" id="studentSearch" placeholder="Cari praktikan (Nama / NPM)..."
                    class="w-full h-10 pl-10 pr-4 border border-zinc-200 rounded-xl bg-white shadow-sm placeholder-zinc-400 text-[11px] font-black uppercase tracking-widest text-zinc-700 focus:outline-none focus:ring-4 focus:ring-[#001f3f]/5 focus:border-[#001f3f] transition-all">
                <div class="absolute right-3 top-1/2 -translate-y-1/2 hidden lg:flex items-center gap-1 px-1.5 py-0.5 rounded border border-zinc-200 bg-zinc-50 text-[9px] font-black text-zinc-400 tracking-tighter pointer-events-none">
                    <span class="opacity-70">CTRL</span>
                    <span>K</span>
                </div>
            </div>

            {{-- Filters + Buttons --}}
            <div class="flex flex-wrap items-center gap-2">
                <div class="relative flex-1 sm:flex-none">
                    <select id="filterSesi" class="appearance-none h-10 w-full sm:min-w-[130px] pl-3 pr-10 border border-zinc-200 rounded-xl bg-white text-[10px] font-black uppercase tracking-widest text-zinc-600 focus:outline-none focus:border-[#001f3f] transition-all cursor-pointer shadow-sm">
                        <option value="">Sesi</option>
                        @foreach ($praktikum->sesis as $s)
                            <option value="{{ $s->nama_sesi }}">{{ $s->nama_sesi }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-filter absolute right-4 top-1/2 -translate-y-1/2 text-zinc-400 text-[8px] pointer-events-none"></i>
                </div>

                <div class="relative flex-1 sm:flex-none">
                    <select id="filterAslab" class="appearance-none h-10 w-full sm:min-w-[130px] pl-3 pr-10 border border-zinc-200 rounded-xl bg-white text-[10px] font-black uppercase tracking-widest text-zinc-600 focus:outline-none focus:border-[#001f3f] transition-all cursor-pointer shadow-sm">
                        <option value="">Aslab</option>
                        <option value="Belum Ada">Belum Terbagi</option>
                        @foreach ($praktikum->aslabs as $as)
                            <option value="{{ $as->user->name }}">{{ $as->user->name }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-user-tie absolute right-4 top-1/2 -translate-y-1/2 text-zinc-400 text-[8px] pointer-events-none"></i>
                </div>

                <div class="hidden sm:block h-6 w-px bg-zinc-200 mx-1"></div>

                <a href="{{ route('admin.praktikum.download-template', $praktikum->id) }}" title="Download Template Excel"
                    class="h-10 px-4 rounded-xl border border-sky-200 bg-sky-50 text-sky-700 hover:bg-sky-100 shadow-sm transition-all flex items-center gap-2 text-[10px] font-black uppercase tracking-wider">
                    <i class="fas fa-download"></i> <span class="hidden lg:inline">Template</span>
                </a>

                <button type="button" onclick="document.getElementById('importFile').click()"
                    class="h-10 px-4 rounded-xl bg-[#001f3f] text-white hover:opacity-90 shadow-lg shadow-[#001f3f]/20 transition-all flex items-center gap-2 text-[10px] font-black uppercase tracking-wider">
                    <i class="fas fa-file-import"></i> <span class="hidden sm:inline">Import</span>
                </button>

                <a href="{{ route('admin.praktikum.export-students', $praktikum->id) }}" title="Export ke Excel"
                    class="h-10 px-4 rounded-xl border border-emerald-200 bg-emerald-600 text-white hover:bg-emerald-700 shadow-lg shadow-emerald-500/20 transition-all flex items-center gap-2 text-[10px] font-black uppercase tracking-wider">
                    <i class="fas fa-file-export"></i> <span class="hidden sm:inline">Export</span>
                </a>

                <div class="hidden sm:block h-6 w-px bg-zinc-200 mx-1"></div>

                <form action="{{ route('admin.praktikum.auto-assign-aslab', $praktikum->id) }}" method="POST" id="autoAssignForm">
                    @csrf
                    <button type="button" onclick="confirmAutoAssign()"
                        class="h-10 px-5 rounded-xl bg-[#001f3f] text-white flex items-center gap-2.5 text-[10px] font-black uppercase tracking-widest shadow-xl shadow-indigo-500/10 hover:shadow-indigo-500/20 active:scale-95 transition-all group overflow-hidden relative border border-zinc-800">
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent -translate-x-full group-hover:animate-[shimmer_2s_infinite]"></div>
                        <i class="fas fa-magic text-indigo-300 relative z-10"></i>
                        <span class="relative z-10 bg-gradient-to-r from-indigo-200 to-violet-200 bg-clip-text text-transparent group-hover:from-white group-hover:to-white transition-all font-black">
                            <span class="hidden sm:inline">Bagi Otomatis</span>
                            <span class="sm:hidden">Auto</span>
                        </span>
                    </button>
                </form>
            </div>
        </div>

        <form action="{{ route('admin.praktikum.import-students', $praktikum->id) }}" method="POST" id="importForm" enctype="multipart/form-data" class="hidden">
            @csrf
            <input type="file" name="file" id="importFile" accept=".csv" onchange="previewImport(this)">
        </form>

        {{-- Bulk Action Bar --}}
        <div id="bulkActionBar" class="hidden flex-col sm:flex-row items-center justify-between gap-3 px-4 sm:px-6 py-4 bg-[#001f3f] border border-white/5 rounded-2xl shadow-2xl">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center border border-white/10 text-emerald-400">
                    <i class="fas fa-check-double text-xs"></i>
                </div>
                <div>
                    <p class="text-[11px] font-black text-white leading-none uppercase tracking-widest">
                        <span id="selectedCount" class="text-emerald-400">0</span> praktikan terpilih
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <select id="bulkAslabSelect" class="h-10 rounded-xl bg-white/5 border border-white/10 text-white text-[10px] font-black uppercase px-4 outline-none cursor-pointer hover:bg-white/10 transition-all w-52">
                    <option value="" class="text-zinc-900">Pilih Aslab Tujuan</option>
                    @foreach ($praktikum->aslabs as $as)
                        <option value="{{ $as->id }}" class="text-zinc-900">{{ $as->user->name }}</option>
                    @endforeach
                </select>
                <button type="button" onclick="executeBulkAssign()" class="h-10 px-5 bg-emerald-500 hover:bg-emerald-400 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg shadow-emerald-500/20">Pindahkan</button>
                <button type="button" onclick="$('.student-checkbox').prop('checked',false); $('#selectAll').prop('checked',false); toggleBulkActionBar();" class="h-10 w-10 flex items-center justify-center bg-white/5 text-white/40 hover:text-white rounded-xl transition-all border border-white/5"><i class="fas fa-times text-xs"></i></button>
            </div>
        </div>
    </div>

    {{-- ── TABLE ─────────────────────────────────────────────────── --}}
    <div class="overflow-x-auto">
        <table id="studentTable" class="w-full text-sm">
            <thead>
                <tr class="bg-zinc-50/80 border-b border-zinc-100">
                    <th class="w-12 px-4 py-3"><input type="checkbox" id="selectAll" class="w-3.5 h-3.5 rounded border-zinc-300 text-[#001f3f] focus:ring-[#001f3f] cursor-pointer"></th>
                    <th class="px-4 py-3 text-left text-[10px] font-black text-zinc-400 uppercase tracking-[0.15em]">Mahasiswa & Status</th>
                    <th class="px-4 py-3 text-left text-[10px] font-black text-zinc-400 uppercase tracking-[0.15em]">Sesi Praktikum</th>
                    <th class="px-4 py-3 text-left text-[10px] font-black text-zinc-400 uppercase tracking-[0.15em]">Bimbingan Aslab</th>
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
                                <div class="flex items-center gap-2">
                                    <p class="font-bold text-zinc-900 text-[13px] truncate leading-tight">{{ $p->praktikan->user->name }}</p>
                                    @php
                                        $sc = match($p->status) {
                                            'verified' => ['c'=>'bg-emerald-50 text-emerald-700 border-emerald-100', 'd'=>'bg-emerald-400'],
                                            'pending' => ['c'=>'bg-amber-50 text-amber-700 border-amber-100', 'd'=>'bg-amber-400'],
                                            'rejected' => ['c'=>'bg-rose-50 text-rose-700 border-rose-100', 'd'=>'bg-rose-400'],
                                            default => ['c'=>'bg-zinc-50 text-zinc-500 border-zinc-100', 'd'=>'bg-zinc-400']
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[8px] font-black border {{ $sc['c'] }} uppercase tracking-tighter">
                                        <span class="w-1 h-1 rounded-full {{ $sc['d'] }}"></span>
                                        {{ $p->status }}
                                    </span>
                                </div>
                                <p class="text-[10px] text-zinc-400 font-mono mt-0.5">{{ $p->praktikan->npm }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3.5" data-search="{{ $p->sesi->nama_sesi }}">
                        <div class="relative group/sel max-w-[170px]">
                            <select onchange="updateAssignment(this, '{{ route('admin.praktikum.pendaftaran.change-session', $p->id) }}')" data-original-value="{{ $p->sesi_id }}" name="sesi_id" class="appearance-none w-full h-8 pl-2.5 pr-7 text-[11px] font-semibold text-zinc-700 bg-white border border-zinc-200 rounded-lg focus:border-[#001f3f] cursor-pointer outline-none shadow-sm transition-all">
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
                    <td class="px-4 py-3.5" data-search="{{ $p->aslab ? $p->aslab->user->name : 'Pilih Aslab' }}">
                        <div class="relative group/sel max-w-[180px]">
                            <select onchange="updateAssignment(this, '{{ route('admin.praktikum.pendaftaran.assign-aslab', $p->id) }}')" data-original-value="{{ $p->aslab_id }}" name="aslab_id" class="appearance-none w-full h-8 pl-2.5 pr-7 text-[11px] font-semibold text-zinc-700 bg-white border border-zinc-200 rounded-lg focus:border-[#001f3f] cursor-pointer outline-none shadow-sm transition-all">
                                <option value="">— Belum Ditugaskan —</option>
                                @foreach ($praktikum->aslabs as $as)
                                    <option value="{{ $as->id }}" {{ $p->aslab_id == $as->id ? 'selected' : '' }}>{{ $as->user->name }}</option>
                                @endforeach
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