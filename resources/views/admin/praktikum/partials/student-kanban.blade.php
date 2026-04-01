        {{-- JavaScript State --}}
        <script>
            (function() {
                console.group('Kanban Data Init');
                window.transferStudents = [
                    @foreach($praktikum->pendaftarans as $p)
                        {
                            id: @json($p->id),
                            aslab_id: @json($p->aslab_id),
                            name: @json(optional(optional($p->praktikan)->user)->name),
                            npm: @json(optional($p->praktikan)->npm),
                            sesi: @json(optional($p->sesi)->nama_sesi)
                        },
                    @endforeach
                ];
                console.log('Students loaded:', window.transferStudents.length);

                window.transferAslabs = {
                    "unassigned": { name: "Belum Ada Aslab", kuota: 0, avatar: 'https://ui-avatars.com/api/?name=U&background=e4e4e7&color=52525b&bold=true' },
                    @foreach($praktikum->aslabs as $as)
                        [@json($as->id)]: {
                            name: @json($as->user->name),
                            kuota: @json($as->pivot->kuota ?? 0),
                            avatar: @json($as->user->profile_picture ? asset('storage/' . $as->user->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($as->user->name) . '&background=001f3f&color=ffffff&bold=true')
                        },
                    @endforeach
                };
                console.log('Aslabs loaded:', Object.keys(window.transferAslabs).length);
                console.groupEnd();
            })();
        </script>

        {{-- ── TRANSFER / KANBAN SECTION ───────────────────────────────────────────────── --}}
        <div id="transfer-section" class="hidden">
            {{-- Transfer Card --}}
            <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden">

                {{-- Transfer Top Bar --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-4 sm:px-6 py-3.5 border-b border-zinc-100 bg-zinc-50/70">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-[#001f3f] flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-exchange-alt text-white text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs font-black text-zinc-800 uppercase tracking-wider leading-none">Transfer Praktikan</p>
                            <p class="text-[10px] text-zinc-400 font-medium mt-0.5">Drag &amp; drop mahasiswa antar panel aslab</p>
                        </div>
                    </div>

                    {{-- View Toggle (Secondary set) --}}
                    <div class="flex items-center gap-0.5 p-0.5 bg-white border border-zinc-200 rounded-lg shadow-sm self-start sm:self-auto">
                        <button id="btnModeTable2" onclick="switchMode('table')"
                            class="view-mode-btn flex items-center gap-1.5 px-3 py-1.5 text-[10px] font-black uppercase tracking-wider rounded-md text-zinc-500 hover:bg-zinc-50 hover:text-zinc-800 transition-all">
                            <i class="fas fa-table-list text-[9px]"></i> Tabel
                        </button>
                        <button id="btnModeKanban2" onclick="switchMode('kanban')"
                            class="view-mode-btn flex items-center gap-1.5 px-3 py-1.5 text-[10px] font-black uppercase tracking-wider rounded-md bg-[#001f3f] text-white transition-all">
                            <i class="fas fa-exchange-alt text-[9px]"></i> Transfer
                        </button>
                    </div>
                </div>

                {{-- Two Panes --}}
                <div class="flex flex-col md:flex-row animate-stagger">

                    {{-- LEFT PANE --}}
                    <div class="flex-1 kanban-col flex flex-col kanban-height relative overflow-hidden border-b md:border-b-0 md:border-r border-zinc-100">
                        <div id="transfer-glow-left" class="absolute top-0 left-0 w-full h-0.5 bg-gradient-to-r from-zinc-300 to-zinc-400 transition-all duration-700"></div>
                        <div class="px-4 py-3 border-b border-zinc-100 bg-white/90 sticky top-0 z-10 backdrop-blur-xl">
                            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-2">Panel Sumber</p>
                            <div class="flex items-center gap-2">
                                <img id="transfer-avatar-left"
                                    src="https://ui-avatars.com/api/?name=U&amp;background=e4e4e7&amp;color=52525b&amp;bold=true"
                                    class="w-8 h-8 rounded-lg border border-zinc-100 shadow-sm object-cover flex-shrink-0">
                                <div class="flex-1 relative">
                                    <select id="transfer-select-left" onchange="renderTransferPane('left')"
                                        class="appearance-none w-full bg-zinc-50 border border-zinc-200 text-zinc-800 rounded-lg pl-3 pr-7 h-9 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-[#001f3f]/20 cursor-pointer shadow-sm transition-all">
                                        <option value="unassigned">Belum Ada Aslab</option>
                                        @foreach($praktikum->aslabs as $as)
                                            <option value="{{ $as->id }}">{{ $as->user->name }}</option>
                                        @endforeach
                                    </select>
                                    <i class="fas fa-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-zinc-300 text-[8px] pointer-events-none"></i>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-2 border-b border-zinc-100 bg-zinc-50/50 flex justify-between items-center">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Mahasiswa</span>
                            <span id="transfer-text-left" class="text-[11px] font-black font-mono text-zinc-600 transition-colors duration-500">0</span>
                        </div>
                        <div class="h-0.5 w-full bg-zinc-100 overflow-hidden">
                            <div id="transfer-progress-left" class="h-full bg-zinc-300 w-0 transition-all duration-700 ease-out"></div>
                        </div>
                        <div id="transfer-dropzone-left" class="p-3 overflow-y-auto flex-1 kanban-dropzone space-y-1.5" data-pane="left"></div>
                    </div>

                    {{-- RIGHT PANE --}}
                    <div class="flex-1 kanban-col flex flex-col kanban-height relative overflow-hidden">
                        <div id="transfer-glow-right" class="absolute top-0 left-0 w-full h-0.5 bg-gradient-to-r from-zinc-300 to-zinc-400 transition-all duration-700"></div>
                        <div class="px-4 py-3 border-b border-zinc-100 bg-white/90 sticky top-0 z-10 backdrop-blur-xl">
                            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-2">Panel Tujuan</p>
                            <div class="flex items-center gap-2">
                                <img id="transfer-avatar-right"
                                    src="https://ui-avatars.com/api/?name=U&amp;background=e4e4e7&amp;color=52525b&amp;bold=true"
                                    class="w-8 h-8 rounded-lg border border-zinc-100 shadow-sm object-cover flex-shrink-0">
                                <div class="flex-1 relative">
                                    <select id="transfer-select-right" onchange="renderTransferPane('right')"
                                        class="appearance-none w-full bg-zinc-50 border border-zinc-200 text-zinc-800 rounded-lg pl-3 pr-7 h-9 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-[#001f3f]/20 cursor-pointer shadow-sm transition-all">
                                        <option value="unassigned">Belum Ada Aslab</option>
                                        @foreach($praktikum->aslabs as $as)
                                            <option value="{{ $as->id }}">{{ $as->user->name }}</option>
                                        @endforeach
                                    </select>
                                    <i class="fas fa-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-zinc-300 text-[8px] pointer-events-none"></i>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-2 border-b border-zinc-100 bg-zinc-50/50 flex justify-between items-center">
                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Mahasiswa</span>
                            <span id="transfer-text-right" class="text-[11px] font-black font-mono text-zinc-600 transition-colors duration-500">0</span>
                        </div>
                        <div class="h-0.5 w-full bg-zinc-100 overflow-hidden">
                            <div id="transfer-progress-right" class="h-full bg-zinc-300 w-0 transition-all duration-700 ease-out"></div>
                        </div>
                        <div id="transfer-dropzone-right" class="p-3 overflow-y-auto flex-1 kanban-dropzone space-y-1.5" data-pane="right"></div>
                    </div>

                </div>{{-- End Two Panes --}}
            </div>{{-- End Transfer Card --}}

        </div>{{-- End #transfer-section --}}
