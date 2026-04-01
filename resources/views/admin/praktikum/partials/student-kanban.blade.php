        {{-- JavaScript State --}}
        <script>
            (function() {
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

                window.transferAslabs = {
                    "unassigned": { 
                        name: "Belum Ada Aslab", 
                        kuota: 0, 
                        avatar: 'https://ui-avatars.com/api/?name=%3F&background=f4f4f5&color=a1a1aa&bold=true' 
                    },
                    @foreach($praktikum->aslabs as $as)
                        [@json($as->id)]: {
                            name: @json($as->user->name),
                            kuota: @json($as->pivot->kuota ?? 0),
                            avatar: @json($as->user->profile_picture ? asset('storage/' . $as->user->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($as->user->name) . '&background=001f3f&color=ffffff&bold=true')
                        },
                    @endforeach
                };
            })();
        </script>

        {{-- ── KANBAN SECTION ───────────────────────────────────────────────────────── --}}
        <div id="transfer-section" class="hidden">
            <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden flex flex-col">
                
                {{-- Header / Top Bar --}}
                <div class="px-6 py-4 border-b border-zinc-100 bg-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#001f3f]/5 border border-[#001f3f]/10 flex items-center justify-center text-[#001f3f]">
                            <i class="fas fa-columns text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-zinc-900 leading-tight">Manajemen Penempatan</h3>
                            <p class="text-[11px] text-zinc-500 font-medium">Geser kartu mahasiswa untuk memindahkan antar aslab.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-1 p-1 bg-zinc-100/50 rounded-xl border border-zinc-200/50 self-start sm:self-auto">
                        <button onclick="switchMode('table')"
                            class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold rounded-lg text-zinc-500 hover:text-zinc-900 transition-all">
                            <i class="fas fa-list text-[10px]"></i> Tabel
                        </button>
                        <button class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold rounded-lg bg-white text-[#001f3f] shadow-sm border border-zinc-200 transition-all">
                            <i class="fas fa-columns text-[10px]"></i> Kanban
                        </button>
                    </div>
                </div>

                {{-- Columns Wrapper --}}
                <div class="flex flex-col md:flex-row divide-y md:divide-y-0 md:divide-x divide-zinc-100">
                    
                    {{-- LEFT COLUMN --}}
                    <div class="flex-1 flex flex-col kanban-height bg-zinc-50/30">
                        {{-- Pane Header --}}
                        <div class="p-4 bg-white/80 backdrop-blur-md sticky top-0 z-20 border-b border-zinc-100">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-[10px] font-extrabold text-[#001f3f]/40 uppercase tracking-[0.2em]">Panel Sumber</span>
                                <div id="transfer-text-left" class="text-[10px] font-bold font-mono text-zinc-400 bg-zinc-100 px-2 py-0.5 rounded-full">0</div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="relative flex-shrink-0">
                                    <img id="transfer-avatar-left" 
                                         src="https://ui-avatars.com/api/?name=%3F&background=f4f4f5&color=a1a1aa&bold=true"
                                         class="w-10 h-10 rounded-xl border border-zinc-200 shadow-sm object-cover bg-white">
                                    <div id="transfer-glow-left" class="absolute -bottom-1 -right-1 w-3 h-3 rounded-full bg-zinc-300 border-2 border-white ring-1 ring-zinc-200"></div>
                                </div>
                                <div class="flex-1 relative group">
                                    <select id="transfer-select-left" onchange="renderTransferPane('left')"
                                        class="appearance-none w-full bg-white border border-zinc-200 text-zinc-900 rounded-xl pl-3 pr-10 h-10 text-xs font-bold focus:outline-none focus:ring-4 focus:ring-[#001f3f]/5 focus:border-[#001f3f]/20 cursor-pointer shadow-sm transition-all group-hover:border-zinc-300">
                                        <option value="unassigned">Belum Ada Aslab</option>
                                        @foreach($praktikum->aslabs as $as)
                                            <option value="{{ $as->id }}">{{ $as->user->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-zinc-400 group-hover:text-zinc-600 transition-colors">
                                        <i class="fas fa-chevron-down text-[10px]"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Progress Bar --}}
                        <div class="h-1 w-full bg-zinc-100 overflow-hidden">
                            <div id="transfer-progress-left" class="h-full bg-[#001f3f] w-0 transition-all duration-700 ease-out shadow-[0_0_8px_rgba(0,31,63,0.3)]"></div>
                        </div>

                        {{-- Dropzone --}}
                        <div id="transfer-dropzone-left" class="flex-1 overflow-y-auto p-4 space-y-3 kanban-dropzone active:bg-[#001f3f]/5 transition-colors duration-300" data-pane="left"></div>
                    </div>

                    {{-- RIGHT COLUMN --}}
                    <div class="flex-1 flex flex-col kanban-height bg-zinc-50/30">
                        {{-- Pane Header --}}
                        <div class="p-4 bg-white/80 backdrop-blur-md sticky top-0 z-20 border-b border-zinc-100">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-[10px] font-extrabold text-[#001f3f]/40 uppercase tracking-[0.2em]">Panel Tujuan</span>
                                <div id="transfer-text-right" class="text-[10px] font-bold font-mono text-zinc-400 bg-zinc-100 px-2 py-0.5 rounded-full">0</div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="relative flex-shrink-0">
                                    <img id="transfer-avatar-right" 
                                         src="https://ui-avatars.com/api/?name=%3F&background=f4f4f5&color=a1a1aa&bold=true"
                                         class="w-10 h-10 rounded-xl border border-zinc-200 shadow-sm object-cover bg-white">
                                    <div id="transfer-glow-right" class="absolute -bottom-1 -right-1 w-3 h-3 rounded-full bg-zinc-300 border-2 border-white ring-1 ring-zinc-200"></div>
                                </div>
                                <div class="flex-1 relative group">
                                    <select id="transfer-select-right" onchange="renderTransferPane('right')"
                                        class="appearance-none w-full bg-white border border-zinc-200 text-zinc-900 rounded-xl pl-3 pr-10 h-10 text-xs font-bold focus:outline-none focus:ring-4 focus:ring-[#001f3f]/5 focus:border-[#001f3f]/20 cursor-pointer shadow-sm transition-all group-hover:border-zinc-300">
                                        <option value="unassigned">Belum Ada Aslab</option>
                                        @foreach($praktikum->aslabs as $as)
                                            <option value="{{ $as->id }}">{{ $as->user->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-zinc-400 group-hover:text-zinc-600 transition-colors">
                                        <i class="fas fa-chevron-down text-[10px]"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Progress Bar --}}
                        <div class="h-1 w-full bg-zinc-100 overflow-hidden">
                            <div id="transfer-progress-right" class="h-full bg-[#001f3f] w-0 transition-all duration-700 ease-out shadow-[0_0_8px_rgba(0,31,63,0.3)]"></div>
                        </div>

                        {{-- Dropzone --}}
                        <div id="transfer-dropzone-right" class="flex-1 overflow-y-auto p-4 space-y-3 kanban-dropzone active:bg-[#001f3f]/5 transition-colors duration-300" data-pane="right"></div>
                    </div>

                </div>{{-- End Columns Wrapper --}}
            </div>
        </div>{{-- End #transfer-section --}}

