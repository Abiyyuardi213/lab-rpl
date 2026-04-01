<div id="transfer-section" class="hidden">
    <div class="bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden flex flex-col min-h-[600px]">
        
        {{-- Header --}}
        <div class="px-4 sm:px-6 py-3.5 border-b border-zinc-100 bg-zinc-50/70 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#001f3f] flex items-center justify-center flex-shrink-0 text-white">
                    <i class="fas fa-columns text-xs"></i>
                </div>
                <div>
                    <h3 class="text-xs font-black text-zinc-800 uppercase tracking-wider leading-none">Penjajakan Aslab</h3>
                    <p class="text-[10px] text-zinc-400 font-medium mt-0.5">Bagi mahasiswa ke aslab bimbingan dengan menggeser kartu.</p>
                </div>
            </div>

            <div class="flex items-center gap-0.5 p-0.5 bg-white border border-zinc-200 rounded-lg shadow-sm self-start sm:self-auto">
                <button id="btnModeTable2" onclick="switchMode('table')"
                    class="view-mode-btn">
                    <i class="fas fa-list text-[9px]"></i> Tabel
                </button>
                <button id="btnModeKanban2" onclick="switchMode('kanban')"
                    class="view-mode-btn">
                    <i class="fas fa-columns text-[9px]"></i> Transfer
                </button>
            </div>
        </div>

        {{-- Columns --}}
        <div class="flex flex-col md:flex-row divide-y md:divide-y-0 md:divide-x divide-zinc-100 flex-1">
            
            {{-- Source --}}
            <div class="flex-1 flex flex-col kanban-height bg-zinc-50/20">
                <div class="p-4 bg-white/80 sticky top-0 z-20 border-b border-zinc-100">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[9px] font-extrabold text-zinc-400 uppercase tracking-widest">Sumber</span>
                        <div id="transfer-count-left" class="text-[10px] font-bold font-mono text-zinc-400 bg-zinc-100 px-2 py-0.5 rounded-full">0</div>
                    </div>
                    <select id="transfer-select-left" onchange="renderTransferPane('left')"
                        class="w-full bg-white border border-zinc-200 text-zinc-900 rounded-lg px-3 h-10 text-[11px] font-bold outline-none cursor-pointer">
                        <option value="unassigned">Belum Ada Aslab</option>
                        @foreach($praktikum->aslabs as $as)
                            <option value="{{ $as->id }}">{{ optional($as->user)->name ?? 'Aslab' }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="transfer-dropzone-left" class="flex-1 overflow-y-auto p-4 space-y-3" data-pane="left"></div>
            </div>

            {{-- Target --}}
            <div class="flex-1 flex flex-col kanban-height bg-zinc-50/20">
                <div class="p-4 bg-white/80 sticky top-0 z-20 border-b border-zinc-100">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[9px] font-extrabold text-zinc-400 uppercase tracking-widest">Tujuan</span>
                        <div id="transfer-count-right" class="text-[10px] font-bold font-mono text-zinc-400 bg-zinc-100 px-2 py-0.5 rounded-full">0</div>
                    </div>
                    <select id="transfer-select-right" onchange="renderTransferPane('right')"
                        class="w-full bg-white border border-zinc-200 text-zinc-900 rounded-lg px-3 h-10 text-[11px] font-bold outline-none cursor-pointer">
                        <option value="unassigned">Belum Ada Aslab</option>
                        @foreach($praktikum->aslabs as $as)
                            <option value="{{ $as->id }}">{{ optional($as->user)->name ?? 'Aslab' }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="transfer-dropzone-right" class="flex-1 overflow-y-auto p-4 space-y-3" data-pane="right"></div>
            </div>

        </div>
    </div>
</div>

<script>
    (function() {
        window.transferStudents = [
            @foreach($praktikum->pendaftarans as $p)
                {
                    id: @json($p->id),
                    aslab_id: @json($p->aslab_id),
                    name: @json(optional($p->praktikan->user)->name ?? 'N/A'),
                    npm: @json($p->praktikan->npm ?? '-'),
                    sesi: @json(optional($p->sesi)->nama_sesi ?? '-')
                },
            @endforeach
        ];

        window.transferAslabs = {
            "unassigned": { name: "Belum Ada", kuota: 0 },
            @foreach($praktikum->aslabs as $as)
                [@json($as->id)]: {
                    name: @json(optional($as->user)->name ?? 'Aslab'),
                    kuota: @json($as->pivot->kuota ?? 0)
                },
            @endforeach
        };
    })();
</script>
