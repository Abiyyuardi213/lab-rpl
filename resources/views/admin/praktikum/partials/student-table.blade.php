        <div id="mahasiswa-section"
            class="bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden"
            style="display:block">

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
                        class="view-mode-btn flex items-center gap-1.5 px-3 py-1.5 text-[10px] font-black uppercase tracking-wider rounded-md bg-[#001f3f] text-white transition-all">
                        <i class="fas fa-table-list text-[9px]"></i> Tabel
                    </button>
                    <button id="btnModeKanban" onclick="switchMode('kanban')"
                        class="view-mode-btn flex items-center gap-1.5 px-3 py-1.5 text-[10px] font-black uppercase tracking-wider rounded-md text-zinc-500 hover:bg-zinc-50 hover:text-zinc-800 transition-all">
                        <i class="fas fa-exchange-alt text-[9px]"></i> Transfer
                    </button>
                </div>
            </div>

            {{-- ── ACTION BAR ──────────────────────────────────────────── --}}
            <div class="px-4 sm:px-6 py-5 border-b border-zinc-100 bg-[#fafafa]/50 space-y-4">
                {{-- Unified Toolbar: Search + Filters + Actions --}}
                <div class="flex flex-col sm:flex-row sm:items-center gap-2">

                    {{-- Search --}}
                    <div class="relative group flex-1">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-300 group-focus-within:text-[#001f3f] transition-colors text-[10px] pointer-events-none"></i>
                        <input type="text" id="studentSearch" placeholder="Cari nama atau NPM..."
                            class="w-full h-9 pl-8 pr-3 border border-zinc-200 rounded-lg bg-zinc-50 placeholder-zinc-300 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] focus:bg-white transition-all">
                    </div>

                    {{-- Filters + Buttons --}}
                    <div class="flex flex-wrap items-center gap-2">

                        {{-- Filter Sesi --}}
                        <div class="relative">
                            <select id="filterSesi"
                                class="appearance-none h-9 pl-3 pr-7 border border-zinc-200 rounded-lg bg-zinc-50 text-[10px] font-bold text-zinc-600 focus:outline-none focus:ring-2 focus:ring-[#001f3f]/10 cursor-pointer transition-all">
                                <option value="">Semua Sesi</option>
                                @foreach ($praktikum->sesis as $s)
                                    <option value="{{ $s->nama_sesi }}">{{ $s->nama_sesi }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute right-2 top-1/2 -translate-y-1/2 text-zinc-300 text-[8px] pointer-events-none"></i>
                        </div>

                        {{-- Filter Aslab --}}
                        <div class="relative">
                            <select id="filterAslab"
                                class="appearance-none h-9 pl-3 pr-7 border border-zinc-200 rounded-lg bg-zinc-50 text-[10px] font-bold text-zinc-600 focus:outline-none focus:ring-2 focus:ring-[#001f3f]/10 cursor-pointer transition-all">
                                <option value="">Semua Aslab</option>
                                <option value="Belum Ada">Belum Terbagi</option>
                                @foreach ($praktikum->aslabs as $as)
                                    <option value="{{ $as->user->name }}">{{ $as->user->name }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute right-2 top-1/2 -translate-y-1/2 text-zinc-300 text-[8px] pointer-events-none"></i>
                        </div>

                        <div class="hidden sm:block h-5 w-px bg-zinc-200"></div>

                        {{-- Template --}}
                        <a href="{{ route('admin.praktikum.download-template', $praktikum->id) }}"
                           title="Download Template Excel"
                           class="h-9 px-3 rounded-lg border border-zinc-200 bg-white text-zinc-500 hover:text-emerald-700 hover:border-emerald-200 hover:bg-emerald-50 transition-all flex items-center gap-1.5 text-[10px] font-bold">
                            <i class="fas fa-file-excel text-emerald-600"></i>
                            <span class="hidden lg:inline">Template</span>
                        </a>

                        {{-- Import --}}
                        <button type="button" onclick="document.getElementById('importFile').click()"
                            class="h-9 px-3 rounded-lg bg-[#001f3f] text-white hover:bg-[#002d5a] active:scale-95 transition-all flex items-center gap-1.5 text-[10px] font-bold shadow-sm">
                            <i class="fas fa-file-import text-emerald-400"></i>
                            Import
                        </button>

                        {{-- Export --}}
                        <a href="{{ route('admin.praktikum.export-students', $praktikum->id) }}"
                           title="Export ke Excel"
                           class="h-9 px-3 rounded-lg border border-zinc-200 bg-white text-zinc-600 hover:text-emerald-700 hover:border-emerald-200 hover:bg-emerald-50 transition-all flex items-center gap-1.5 text-[10px] font-bold">
                            <i class="fas fa-file-export text-emerald-600"></i>
                            <span class="hidden lg:inline">Export</span>
                        </a>

                        <div class="hidden sm:block h-5 w-px bg-zinc-200"></div>

                        {{-- Bagi Otomatis --}}
                        <form action="{{ route('admin.praktikum.auto-assign-aslab', $praktikum->id) }}" method="POST" id="autoAssignForm">
                            @csrf
                            <button type="button" onclick="confirmAutoAssign()"
                                class="h-9 px-3.5 rounded-lg bg-gradient-to-r from-[#001f3f] to-[#003366] text-white flex items-center gap-1.5 text-[10px] font-black uppercase tracking-wider shadow-sm hover:shadow-md active:scale-95 transition-all group">
                                <i class="fas fa-wand-magic-sparkles text-emerald-400 text-[10px] group-hover:rotate-12 transition-transform"></i>
                                Bagi Otomatis
                            </button>
                        </form>

                    </div>
                </div>

            {{-- Hidden import form --}}
            <form action="{{ route('admin.praktikum.import-students', $praktikum->id) }}" method="POST" id="importForm"
                enctype="multipart/form-data" class="hidden">
                @csrf
                <input type="file" name="file" id="importFile" accept=".csv"
                    onchange="document.getElementById('importForm').submit()">
            </form>

            {{-- ── BULK ACTION BAR ──────────────────────────────────────────────────── --}}
            <div id="bulkActionBar"
                class="hidden flex-col sm:flex-row items-start sm:items-center justify-between gap-3 px-4 sm:px-6 py-3 bg-[#001f3f] border-b border-[#001f3f]/20">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check-square text-white text-xs"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-black text-white leading-none">
                            <span id="selectedCount" class="text-emerald-400">0</span> mahasiswa dipilih
                        </p>
                        <p class="text-[9px] text-white/40 mt-0.5 font-medium">Pilih aslab tujuan untuk pemindahan massal</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <div class="relative">
                        <select id="bulkAslabSelect"
                            class="appearance-none h-9 rounded-lg bg-white/10 border border-white/20 text-white text-[10px] font-bold pl-3 pr-8 focus:ring-2 focus:ring-white/20 outline-none cursor-pointer w-48">
                            <option value="" class="text-zinc-900">— Pilih Aslab Tujuan —</option>
                            @foreach ($praktikum->aslabs as $as)
                                @php $cCount = $as->assignedStudents()->where('praktikum_id', $praktikum->id)->count(); @endphp
                                <option value="{{ $as->id }}" class="text-zinc-900">{{ $as->user->name }} ({{ $cCount }}/{{ $as->pivot->kuota }})</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-white/40 text-[8px] pointer-events-none"></i>
                    </div>
                    <button type="button" onclick="executeBulkAssign()" id="bulkAssignBtn"
                        class="h-9 px-4 bg-emerald-500 hover:bg-emerald-400 text-white rounded-lg text-[10px] font-black uppercase tracking-wider transition-all active:scale-95 shadow-lg shadow-emerald-500/20">
                        Pindahkan
                    </button>
                    <button type="button" onclick="$('.student-checkbox').prop('checked',false); $('#selectAll').prop('checked',false); toggleBulkActionBar();"
                        class="h-9 w-9 flex items-center justify-center bg-white/10 hover:bg-white/20 text-white rounded-lg transition-all">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
            </div>

            {{-- ── TABLE ─────────────────────────────────────────────────── --}}
            <div class="overflow-x-auto">
                <table id="studentTable" class="w-full text-sm min-w-[660px]">
                    <thead>
                        <tr class="bg-zinc-50/80 border-b border-zinc-100">
                            <th class="w-12 px-4 py-3 sticky left-0 bg-zinc-50/80 z-20">
                                <input type="checkbox" id="selectAll"
                                    class="w-3.5 h-3.5 rounded border-zinc-300 text-[#001f3f] focus:ring-[#001f3f] cursor-pointer">
                            </th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-zinc-400 uppercase tracking-[0.15em] sticky left-12 bg-zinc-50/80 z-20">
                                Mahasiswa
                            </th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-zinc-400 uppercase tracking-[0.15em]">
                                Sesi
                            </th>
                            <th class="px-4 py-3 text-left text-[10px] font-black text-zinc-400 uppercase tracking-[0.15em]">
                                Aslab Bimbingan
                            </th>
                            <th class="px-4 py-3 text-center text-[10px] font-black text-zinc-400 uppercase tracking-[0.15em]">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-50">
                        @foreach($praktikum->pendaftarans as $pendaftaran)
                            <tr class="group/row hover:bg-slate-50/60 transition-colors duration-150">
                                {{-- Checkbox --}}
                                <td class="px-4 py-3.5 sticky left-0 bg-white group-hover/row:bg-slate-50/60 z-10 transition-colors">
                                    <input type="checkbox"
                                        class="student-checkbox w-3.5 h-3.5 rounded border-zinc-300 text-[#001f3f] focus:ring-[#001f3f] cursor-pointer"
                                        value="{{ $pendaftaran->id }}">
                                </td>

                                {{-- Student Info --}}
                                <td class="px-4 py-3.5 sticky left-12 bg-white group-hover/row:bg-slate-50/60 z-10 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl overflow-hidden flex-shrink-0 border border-zinc-100 shadow-sm">
                                            @if($pendaftaran->praktikan->user->profile_picture)
                                                <img src="{{ asset('storage/' . $pendaftaran->praktikan->user->profile_picture) }}" class="w-full h-full object-cover">
                                            @else
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($pendaftaran->praktikan->user->name) }}&background=001f3f&color=ffffff&bold=true&size=64" class="w-full h-full object-cover">
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-zinc-900 text-[13px] truncate max-w-[190px] leading-tight">{{ $pendaftaran->praktikan->user->name }}</p>
                                            <p class="text-[10px] text-zinc-400 font-mono mt-0.5">{{ $pendaftaran->praktikan->npm }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Sesi --}}
                                <td class="px-4 py-3.5" data-search="{{ $pendaftaran->sesi->nama_sesi }}">
                                    <form id="change-session-form-{{ $pendaftaran->id }}">
                                        @csrf
                                        <div class="hidden sesi-hidden-data">{{ $pendaftaran->sesi->nama_sesi }}</div>
                                        <div class="relative group/sel">
                                            <select name="sesi_id"
                                                onchange="updateAssignment(this, '{{ route('admin.praktikum.pendaftaran.change-session', $pendaftaran->id) }}')"
                                                data-original-value="{{ $pendaftaran->sesi_id }}"
                                                class="appearance-none w-full max-w-[170px] h-8 pl-2.5 pr-7 text-[11px] font-semibold text-zinc-700 bg-white border border-zinc-200 rounded-lg focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] cursor-pointer outline-none hover:border-zinc-400 transition-all shadow-sm">
                                                @foreach ($praktikum->sesis as $s)
                                                    @php $countReg = $s->pendaftarans_count ?? $s->pendaftarans()->count(); $isTargetFull = $countReg >= $s->kuota; @endphp
                                                    <option value="{{ $s->id }}" {{ $pendaftaran->sesi_id == $s->id ? 'selected' : '' }} {{ $isTargetFull && $pendaftaran->sesi_id != $s->id ? 'disabled' : '' }}>
                                                        {{ $s->nama_sesi }} ({{ $countReg }}/{{ $s->kuota }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <i class="fas fa-chevron-down absolute right-2 top-1/2 -translate-y-1/2 text-zinc-300 text-[7px] pointer-events-none group-hover/sel:text-zinc-500"></i>
                                        </div>
                                    </form>
                                    <p class="text-[10px] text-zinc-400 mt-1.5 flex items-center gap-1 font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-sky-400 inline-block"></span>
                                        {{ $pendaftaran->sesi->hari }}, {{ substr($pendaftaran->sesi->jam_mulai, 0, 5) }}
                                    </p>
                                </td>


                                {{-- Aslab --}}
                                <td class="px-4 py-3.5" data-search="{{ $pendaftaran->aslab ? $pendaftaran->aslab->user->name : 'Pilih Aslab' }}">
                                    <form id="assign-aslab-form-{{ $pendaftaran->id }}">
                                        @csrf
                                        <div class="hidden aslab-hidden-data">{{ $pendaftaran->aslab ? $pendaftaran->aslab->user->name : 'Pilih Aslab' }}</div>
                                        <div class="relative group/sel">
                                            <select name="aslab_id"
                                                onchange="updateAssignment(this, '{{ route('admin.praktikum.pendaftaran.assign-aslab', $pendaftaran->id) }}')"
                                                data-original-value="{{ $pendaftaran->aslab_id }}"
                                                class="appearance-none w-full max-w-[170px] h-8 pl-2.5 pr-7 text-[11px] font-semibold text-zinc-700 bg-white border border-zinc-200 rounded-lg focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] cursor-pointer outline-none hover:border-zinc-400 transition-all shadow-sm">
                                                <option value="">— Belum Ditugaskan —</option>
                                                @foreach ($praktikum->aslabs as $as)
                                                    @php $curr = $as->assignedStudents()->where('praktikum_id', $praktikum->id)->count(); $max = $as->pivot->kuota; $isFull = $curr >= $max; @endphp
                                                    <option value="{{ $as->id }}" {{ $pendaftaran->aslab_id == $as->id ? 'selected' : '' }} {{ $isFull && $pendaftaran->aslab_id != $as->id ? 'disabled' : '' }}>
                                                        {{ $as->user->name }} ({{ $curr }}/{{ $max }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <i class="fas fa-chevron-down absolute right-2 top-1/2 -translate-y-1/2 text-zinc-300 text-[7px] pointer-events-none group-hover/sel:text-zinc-500"></i>
                                        </div>
                                    </form>
                                    @php
                                        $link = null;
                                        if ($pendaftaran->aslab_id) {
                                            $aslabPrak = \App\Models\AslabPraktikum::where('aslab_id', $pendaftaran->aslab_id)->where('praktikum_id', $praktikum->id)->first();
                                            $link = $aslabPrak?->link_grup;
                                        }
                                    @endphp
                                    @if ($link)
                                        <a href="{{ $link }}" target="_blank"
                                            class="mt-1.5 inline-flex items-center gap-1 px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 hover:bg-emerald-100 text-[9px] font-bold uppercase tracking-wide transition-all">
                                            <i class="fab fa-whatsapp"></i> Grup WA
                                        </a>
                                    @else
                                        <p class="text-[10px] text-zinc-300 mt-1.5">Belum ada grup</p>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="px-4 py-3.5 text-center">
                                    @php
                                        $sc = match($pendaftaran->status) {
                                            'verified' => ['cls' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'dot' => 'bg-emerald-400'],
                                            'pending'  => ['cls' => 'bg-amber-50 text-amber-700 border-amber-200', 'dot' => 'bg-amber-400'],
                                            'rejected' => ['cls' => 'bg-rose-50 text-rose-700 border-rose-200', 'dot' => 'bg-rose-400'],
                                            default    => ['cls' => 'bg-zinc-50 text-zinc-500 border-zinc-200', 'dot' => 'bg-zinc-400'],
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black border {{ $sc['cls'] }} uppercase tracking-wide whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $sc['dot'] }} flex-shrink-0"></span>
                                        {{ $pendaftaran->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>{{-- End #mahasiswa-section --}}
