@extends('layouts.admin')

@section('title', 'Daftar Praktikan - ' . $praktikum->nama_praktikum)

@section('content')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-start justify-between">
            <div class="space-y-1">
                <a href="{{ route('admin.praktikum.index') }}"
                    class="inline-flex items-center gap-2 text-xs font-bold text-zinc-500 hover:text-zinc-900 transition-colors mb-2">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Daftar
                </a>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Daftar Praktikan</h1>
                    <span
                        class="bg-zinc-100 text-zinc-700 px-2 py-0.5 rounded text-[11px] font-bold font-mono border border-zinc-200 uppercase tracking-wider">
                        {{ $praktikum->kode_praktikum }}
                    </span>
                </div>
                <p class="text-sm text-zinc-500 mt-1">{{ $praktikum->nama_praktikum }}</p>
            </div>
            <div class="flex flex-col items-end gap-3">
                <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                    <span>/</span>
                    <a href="{{ route('admin.praktikum.index') }}" class="hover:text-zinc-900 transition-colors">Praktikum</a>
                    <span>/</span>
                    <span class="text-zinc-900 font-semibold">Praktikan</span>
                </div>
                
                <!-- View Mode Toggles -->
                <div class="flex p-1 bg-zinc-100 rounded-lg border border-zinc-200 shadow-inner">
                    <button id="btnModeTable" onclick="switchMode('table')" class="view-mode-btn active px-4 py-1.5 text-xs font-bold rounded-md bg-white text-zinc-900 shadow-sm transition-all flex items-center gap-1.5">
                        <i class="fas fa-list"></i> Mode Tabel
                    </button>
                    <button id="btnModeKanban" onclick="switchMode('kanban')" class="view-mode-btn px-4 py-1.5 text-xs font-bold rounded-md text-zinc-500 hover:text-zinc-900 transition-all flex items-center gap-1.5">
                        <i class="fas fa-exchange-alt"></i> Mode Transfer
                    </button>
                </div>
            </div>
        </div>

        <!-- Registered Students Card -->
        <div id="mahasiswa-section"
            class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden min-h-[500px]">
            <div class="p-6 border-b border-zinc-100 bg-zinc-50/50 flex items-center justify-between">
                <h3 class="font-bold text-zinc-900 flex items-center gap-2">
                    <i class="fas fa-user-graduate text-[#001f3f]"></i>
                    Manajemen Praktikan Terdaftar
                </h3>
                <span class="bg-[#001f3f] text-white px-3 py-1 rounded-full text-[10px] font-bold shadow-lg shadow-[#001f3f]/10">
                    Total: {{ $praktikum->pendaftarans->count() }} Orang
                </span>
            </div>
            
            <div class="p-6 pb-4 flex flex-col sm:flex-row items-center justify-between gap-4 border-b border-zinc-50">
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                    <div class="relative max-w-sm w-full sm:w-64">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-xs shadow-sm"></i>
                        <input type="text" id="studentSearch" placeholder="Cari Nama / NPM..."
                            class="flex h-9 w-full rounded-lg border border-zinc-200 bg-white px-3 py-1 pl-9 text-sm shadow-sm transition-all focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] placeholder:text-zinc-400">
                    </div>
                    
                    <!-- Filters -->
                    <select id="filterSesi" class="h-9 w-full sm:w-auto rounded-lg border border-zinc-200 bg-white px-3 py-1 text-xs font-semibold text-zinc-600 shadow-sm focus:ring-2 focus:ring-[#001f3f]/10 outline-none">
                        <option value="">Semua Sesi</option>
                        @foreach ($praktikum->sesis as $s)
                            <option value="{{ $s->nama_sesi }}">{{ $s->nama_sesi }}</option>
                        @endforeach
                    </select>
                    
                    <select id="filterAslab" class="h-9 w-full sm:w-auto rounded-lg border border-zinc-200 bg-white px-3 py-1 text-xs font-semibold text-zinc-600 shadow-sm focus:ring-2 focus:ring-[#001f3f]/10 outline-none">
                        <option value="">Semua Aslab</option>
                        <option value="Belum Ada">Belum Dapat Aslab</option>
                        @foreach ($praktikum->aslabs as $as)
                            <option value="{{ $as->user->name }}">{{ $as->user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Auto Distribute Button -->
                <form action="{{ route('admin.praktikum.auto-assign-aslab', $praktikum->id) }}" method="POST" id="autoAssignForm" class="w-full sm:w-auto">
                    @csrf
                    <button type="button" onclick="confirmAutoAssign()" class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2 bg-[#001f3f] text-white rounded-lg text-xs font-bold shadow-md hover:bg-[#002d5a] transition-all active:scale-95">
                        <i class="fas fa-magic"></i>
                        Distribusi Otomatis
                    </button>
                </form>
            </div>

            <!-- Bulk Action Bar -->
            <div id="bulkActionBar" class="hidden px-6 py-3 bg-zinc-100 border-b border-zinc-200 flex items-center justify-between transition-all">
                <div class="text-xs font-bold text-zinc-700 flex items-center gap-2">
                    <span id="selectedCount" class="bg-zinc-800 text-white px-2 py-0.5 rounded-full text-[10px]">0</span> 
                    Mahasiswa Terpilih
                </div>
                <div class="flex items-center gap-3">
                    <select id="bulkAslabSelect" class="h-8 rounded-lg border border-zinc-300 bg-white px-3 py-1 text-xs font-semibold text-zinc-600 shadow-sm focus:ring-2 focus:ring-[#001f3f]/10 outline-none w-48 lg:w-64">
                        <option value="">Pilih Aslab Tujuan...</option>
                        @foreach ($praktikum->aslabs as $as)
                            @php
                                $cCount = $as->assignedStudents()->where('praktikum_id', $praktikum->id)->count();
                            @endphp
                            <option value="{{ $as->id }}">{{ $as->user->name }} ({{ $cCount }}/{{ $as->pivot->kuota }})</option>
                        @endforeach
                    </select>
                    <button type="button" onclick="executeBulkAssign()" id="bulkAssignBtn" class="h-8 px-4 bg-emerald-600 text-white rounded-lg text-xs font-bold shadow-sm hover:bg-emerald-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-exchange-alt mr-1"></i> Pindahkan
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="studentTable" class="w-full text-sm">
                    <thead class="bg-zinc-50/50 border-b border-zinc-100">
                        <tr>
                            <th class="px-6 py-3 text-left w-10">
                                <input type="checkbox" id="selectAll" class="rounded border-zinc-300 text-[#001f3f] shadow-sm focus:ring-[#001f3f] cursor-pointer">
                            </th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Mahasiswa</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Sesi Praktikum</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Aslab Bimbingan</th>
                            <th class="px-6 py-3 text-center text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach($praktikum->pendaftarans as $pendaftaran)
                            <tr class="hover:bg-zinc-50/30 transition-colors">
                                <td class="px-6 py-4">
                                    <input type="checkbox" class="student-checkbox rounded border-zinc-300 text-[#001f3f] shadow-sm focus:ring-[#001f3f] cursor-pointer" value="{{ $pendaftaran->id }}">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-zinc-900 leading-tight uppercase tracking-tight">{{ $pendaftaran->praktikan->user->name }}</div>
                                    <div class="text-[10px] text-zinc-400 font-mono font-bold mt-0.5">{{ $pendaftaran->praktikan->npm }}</div>
                                </td>
                                <td class="px-6 py-4" data-search="{{ $pendaftaran->sesi->nama_sesi }}">
                                    <form id="change-session-form-{{ $pendaftaran->id }}">
                                        @csrf
                                        <div class="hidden sesi-hidden-data">{{ $pendaftaran->sesi->nama_sesi }}</div>
                                        <select name="sesi_id" onchange="updateAssignment(this, '{{ route('admin.praktikum.pendaftaran.change-session', $pendaftaran->id) }}')" data-original-value="{{ $pendaftaran->sesi_id }}"
                                            class="text-[11px] font-semibold bg-white border border-zinc-200 rounded-lg px-2.5 py-1.5 focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] w-full max-w-[160px] shadow-sm transition-all cursor-pointer outline-none hover:border-[#001f3f]/30">
                                            @foreach ($praktikum->sesis as $s)
                                                @php
                                                    $countReg = $s->pendaftarans_count ?? $s->pendaftarans()->count();
                                                    $isTargetFull = $countReg >= $s->kuota;
                                                @endphp
                                                <option value="{{ $s->id }}"
                                                    {{ $pendaftaran->sesi_id == $s->id ? 'selected' : '' }}
                                                    {{ $isTargetFull && $pendaftaran->sesi_id != $s->id ? 'disabled' : '' }}>
                                                    {{ $s->nama_sesi }} ({{ $countReg }}/{{ $s->kuota }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                    <div class="text-[9px] text-zinc-400 mt-1 uppercase tracking-tighter font-medium italic">
                                        {{ $pendaftaran->sesi->hari }}, {{ substr($pendaftaran->sesi->jam_mulai, 0, 5) }}-{{ substr($pendaftaran->sesi->jam_selesai, 0, 5) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4" data-search="{{ $pendaftaran->aslab ? $pendaftaran->aslab->user->name : 'Pilih Aslab' }}">
                                    <form id="assign-aslab-form-{{ $pendaftaran->id }}">
                                        @csrf
                                        <div class="hidden aslab-hidden-data">{{ $pendaftaran->aslab ? $pendaftaran->aslab->user->name : 'Pilih Aslab' }}</div>
                                        <select name="aslab_id" onchange="updateAssignment(this, '{{ route('admin.praktikum.pendaftaran.assign-aslab', $pendaftaran->id) }}')" data-original-value="{{ $pendaftaran->aslab_id }}"
                                            class="text-[11px] font-semibold bg-white border border-zinc-200 rounded-lg px-2.5 py-1.5 focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] w-full max-w-[160px] shadow-sm transition-all cursor-pointer outline-none hover:border-[#001f3f]/30">
                                            <option value="">-- Pilih Aslab --</option>
                                            @foreach ($praktikum->aslabs as $as)
                                                @php
                                                    $curr = $as->assignedStudents()->where('praktikum_id', $praktikum->id)->count();
                                                    $max = $as->pivot->kuota;
                                                    $isFull = $curr >= $max;
                                                @endphp
                                                <option value="{{ $as->id }}"
                                                    {{ $pendaftaran->aslab_id == $as->id ? 'selected' : '' }}
                                                    {{ $isFull && $pendaftaran->aslab_id != $as->id ? 'disabled' : '' }}>
                                                    {{ $as->user->name }} ({{ $curr }}/{{ $max }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                    <div class="text-[9px] text-zinc-400 mt-1 uppercase tracking-tighter font-medium italic">
                                        Penanggung Jawab Bimbingan
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $statusBadge = [
                                            'pending' => 'bg-amber-50 text-amber-700 border-amber-100',
                                            'verified' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                            'rejected' => 'bg-rose-50 text-rose-700 border-rose-100',
                                        ];
                                        $st = $statusBadge[$pendaftaran->status] ?? 'bg-zinc-50 text-zinc-500 border-zinc-100';
                                    @endphp
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-[9px] font-black border {{ $st }} uppercase tracking-wider">
                                        {{ $pendaftaran->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div><!-- End of mahasiswa-section -->

        <!-- Transfer List Section (Split-View Drag & Drop) -->
        <div id="transfer-section" class="hidden flex flex-col md:flex-row gap-6 px-4 py-8 min-h-[500px] w-full animate-stagger max-w-6xl mx-auto">
            
            <!-- JavaScript State Initialization -->
            <script>
                // We store the master list of students here for the Transfer List
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
                    "unassigned": { name: "Belum Ada Aslab", kuota: 0, avatar: 'https://ui-avatars.com/api/?name=Unassigned&background=e4e4e7&color=52525b&bold=true' },
                    @foreach($praktikum->aslabs as $as)
                    [@json($as->id)]: { 
                        name: @json($as->user->name), 
                        kuota: @json($as->pivot->kuota),
                        avatar: @json($as->user->profile_picture ? asset('storage/' . $as->user->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($as->user->name) . '&background=001f3f&color=ffffff&bold=true')
                    },
                    @endforeach
                };
            </script>

            <!-- LEFT PANE -->
            <div class="flex-1 kanban-col rounded-2xl flex flex-col h-[65vh] relative overflow-hidden group shadow-[0_8px_30px_rgb(0,0,0,0.04)] bg-zinc-50/50 border border-zinc-200">
                <!-- Top Decorative Glow -->
                <div id="transfer-glow-left" class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-zinc-300 to-zinc-400 opacity-60 transition-all duration-700"></div>
                
                <div class="p-5 border-b border-[#001f3f]/10 bg-white/80 sticky top-0 z-10 backdrop-blur-xl">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-[10px] font-extrabold text-[#001f3f]/50 uppercase tracking-widest">Panel Sumber (Kiri)</label>
                        <i class="fas fa-chevron-circle-down text-zinc-300 text-sm"></i>
                    </div>
                    <div class="flex items-center gap-3">
                        <img id="transfer-avatar-left" src="https://ui-avatars.com/api/?name=U&background=e4e4e7&color=52525b&bold=true" class="w-10 h-10 rounded-full border-2 border-white shadow-sm object-cover" alt="Avatar">
                        <div class="flex-1 relative">
                            <select id="transfer-select-left" onchange="renderTransferPane('left')" class="appearance-none w-full bg-zinc-50 border border-zinc-200 text-[#001f3f] rounded-lg pl-3 pr-8 py-2 text-sm font-bold shadow-inner focus:outline-none focus:ring-2 focus:ring-[#001f3f]/20 transition-all cursor-pointer">
                                <option value="unassigned">Belum Ada Aslab</option>
                                @foreach($praktikum->aslabs as $as)
                                    <option value="{{ $as->id }}">{{ $as->user->name }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-zinc-400">
                                <i class="fas fa-caret-down"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="px-5 py-3 border-b border-zinc-100/50 bg-zinc-50/50 flex justify-between items-center z-10">
                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Total Mahasiswa</span>
                    <div class="flex items-center gap-2">
                        <span id="transfer-text-left" class="text-xs font-bold font-mono transition-colors duration-500">0</span>
                    </div>
                </div>
                <!-- Progress Bar Container -->
                <div class="h-1.5 w-full bg-zinc-100 overflow-hidden z-10 relative">
                    <div id="transfer-progress-left" class="h-full bg-zinc-300 w-0 transition-all duration-700 ease-out"></div>
                </div>

                <div id="transfer-dropzone-left" class="p-3 overflow-y-auto flex-1 kanban-dropzone space-y-3" data-pane="left">
                    <!-- Cards injected by JS -->
                </div>
            </div>

            <!-- Transfer Icon Divider -->
            <div class="hidden md:flex flex-col justify-center items-center">
                <div class="w-10 h-10 rounded-full bg-white border border-zinc-200 shadow-sm flex items-center justify-center text-zinc-400">
                    <i class="fas fa-exchange-alt"></i>
                </div>
            </div>

            <!-- RIGHT PANE -->
            <div class="flex-1 kanban-col rounded-2xl flex flex-col h-[65vh] relative overflow-hidden group shadow-[0_8px_30px_rgb(0,0,0,0.04)] bg-zinc-50/50 border border-zinc-200">
                <!-- Top Decorative Glow -->
                <div id="transfer-glow-right" class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-zinc-300 to-zinc-400 opacity-60 transition-all duration-700"></div>
                
                <div class="p-5 border-b border-[#001f3f]/10 bg-white/80 sticky top-0 z-10 backdrop-blur-xl">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-[10px] font-extrabold text-[#001f3f]/50 uppercase tracking-widest">Panel Tujuan (Kanan)</label>
                        <i class="fas fa-chevron-circle-down text-zinc-300 text-sm"></i>
                    </div>
                    <div class="flex items-center gap-3">
                        <img id="transfer-avatar-right" src="https://ui-avatars.com/api/?name=U&background=e4e4e7&color=52525b&bold=true" class="w-10 h-10 rounded-full border-2 border-white shadow-sm object-cover" alt="Avatar">
                        <div class="flex-1 relative">
                            <select id="transfer-select-right" onchange="renderTransferPane('right')" class="appearance-none w-full bg-zinc-50 border border-zinc-200 text-[#001f3f] rounded-lg pl-3 pr-8 py-2 text-sm font-bold shadow-inner focus:outline-none focus:ring-2 focus:ring-[#001f3f]/20 transition-all cursor-pointer">
                                <option value="unassigned">Belum Ada Aslab</option>
                                @foreach($praktikum->aslabs as $as)
                                    <option value="{{ $as->id }}">{{ $as->user->name }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-zinc-400">
                                <i class="fas fa-caret-down"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="px-5 py-3 border-b border-zinc-100/50 bg-zinc-50/50 flex justify-between items-center z-10">
                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Total Mahasiswa</span>
                    <div class="flex items-center gap-2">
                        <span id="transfer-text-right" class="text-xs font-bold font-mono transition-colors duration-500">0</span>
                    </div>
                </div>
                <!-- Progress Bar Container -->
                <div class="h-1.5 w-full bg-zinc-100 overflow-hidden z-10 relative">
                    <div id="transfer-progress-right" class="h-full bg-zinc-300 w-0 transition-all duration-700 ease-out"></div>
                </div>

                <div id="transfer-dropzone-right" class="p-3 overflow-y-auto flex-1 kanban-dropzone space-y-3" data-pane="right">
                    <!-- Cards injected by JS -->
                </div>
            </div>

        </div>
    </div>

    <style>
        /* Frontend Aesthetics Update */
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

        /* Applying fonts generically where needed */
        .outfit-font { font-family: 'Outfit', sans-serif; }
        .mono-font { font-family: 'JetBrains Mono', monospace; }

        /* Glassmorphism & Soft UI */
        .kanban-col {
            background: rgba(250, 250, 252, 0.6);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.9);
            box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.05);
        }

        .kanban-card {
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 2px 8px -2px rgba(17, 24, 39, 0.03), 0 0 0 1px rgba(17, 24, 39, 0.04);
        }

        .kanban-card:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 12px 24px -6px rgba(17, 24, 39, 0.06), 0 0 0 1px rgba(17, 24, 39, 0.08);
            border-color: rgba(99, 102, 241, 0.2);
        }

        .kanban-card:active {
            transform: translateY(0) scale(0.99);
        }

        /* Custom Gradients */
        .accent-gradient {
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Animations */
        @keyframes slideUpFade {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        .animate-stagger > div {
            animation: slideUpFade 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }
        .animate-stagger > div:nth-child(1) { animation-delay: 0.05s; }
        .animate-stagger > div:nth-child(2) { animation-delay: 0.1s; }
        .animate-stagger > div:nth-child(3) { animation-delay: 0.15s; }
        .animate-stagger > div:nth-child(4) { animation-delay: 0.2s; }
        .animate-stagger > div:nth-child(5) { animation-delay: 0.25s; }
        .animate-stagger > div:nth-child(6) { animation-delay: 0.3s; }
        .animate-stagger > div:nth-child(7) { animation-delay: 0.35s; }

        /* Custom Scrollbar */
        .kanban-dropzone::-webkit-scrollbar {
            width: 4px;
        }
        .kanban-dropzone::-webkit-scrollbar-track {
            background: transparent;
        }
        .kanban-dropzone::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .kanban-dropzone:hover::-webkit-scrollbar-thumb {
            background: #94a3b8;
        }

        /* Ghost class for Sortable */
        .sortable-ghost {
            opacity: 0.5;
            background: #f8fafc !important;
            border: 2px dashed #94a3b8 !important;
            box-shadow: none !important;
            transform: scale(0.98);
        }
    </style>

    <style>
        .dataTables_wrapper .dataTables_info {
            font-size: 11px;
            color: #a1a1aa;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border: 1px solid #e4e4e7 !important;
            border-radius: 8px !important;
            padding: 6px 14px !important;
            font-size: 12px !important;
            font-weight: 700 !important;
            margin-left: 6px !important;
            background: white !important;
            color: #71717a !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #001f3f !important;
            border-color: #001f3f !important;
            color: white !important;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <!-- SortableJS for Drag and Drop -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        // View Toggle Logic
        function switchMode(mode) {
            const tableSection = document.getElementById('mahasiswa-section');
            const transferSection = document.getElementById('transfer-section');
            const btnTable = document.getElementById('btnModeTable');
            const btnTransfer = document.getElementById('btnModeKanban');

            if (mode === 'table') {
                tableSection.classList.remove('hidden');
                transferSection.classList.add('hidden');
                
                btnTable.classList.remove('text-zinc-500', 'bg-transparent');
                btnTable.classList.add('text-zinc-900', 'bg-white', 'shadow-sm', 'active');
                
                btnTransfer.classList.remove('text-zinc-900', 'bg-white', 'shadow-sm', 'active');
                btnTransfer.classList.add('text-zinc-500', 'bg-transparent');
            } else {
                tableSection.classList.add('hidden');
                transferSection.classList.remove('hidden');
                // Initial render for panes
                renderTransferPane('left');
                renderTransferPane('right');
                
                btnTransfer.classList.remove('text-zinc-500', 'bg-transparent');
                btnTransfer.classList.add('text-zinc-900', 'bg-white', 'shadow-sm', 'active');
                
                btnTable.classList.remove('text-zinc-900', 'bg-white', 'shadow-sm', 'active');
                btnTable.classList.add('text-zinc-500', 'bg-transparent');
            }
        }

        // Dedicated render function for Split-View Transfer Panes
        function renderTransferPane(pane) {
            const selectVal = document.getElementById(`transfer-select-${pane}`).value;
            const dropzone = document.getElementById(`transfer-dropzone-${pane}`);
            
            // Filter students
            const targetAslabId = selectVal === 'unassigned' ? null : selectVal;
            const list = window.transferStudents.filter(s => s.aslab_id == targetAslabId);
            
            // Build HTML cards
            let html = '';
            list.forEach(s => {
                html += `
                <div class="kanban-card bg-white p-4 rounded-xl cursor-grab active:cursor-grabbing group/card relative overflow-hidden border border-zinc-200/60 shadow-sm hover:shadow-md hover:border-[#001f3f]/20 transition-all" data-pendaftaran-id="${s.id}">
                    <div class="flex items-start justify-between">
                        <div class="outfit-font font-bold text-[13px] text-[#001f3f] group-hover/card:text-blue-700 transition-colors">${s.name}</div>
                        <div class="text-zinc-300 opacity-0 group-hover/card:opacity-100 transition-opacity">
                            <i class="fas fa-grip-lines text-[#001f3f]/30"></i>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 mt-3">
                        <div class="mono-font text-[10px] text-zinc-500 bg-zinc-50 px-2.5 py-1 rounded-md border border-zinc-100">${s.npm}</div>
                        <div class="text-[9px] text-[#001f3f]/80 bg-[#001f3f]/5 border border-[#001f3f]/10 px-2.5 py-1 rounded-md font-bold truncate max-w-[120px] uppercase tracking-wider" title="${s.sesi}">
                            <i class="fas fa-clock mr-1 text-[#001f3f]/40"></i> ${s.sesi}
                        </div>
                    </div>
                </div>
                `;
            });
            dropzone.innerHTML = html;
            dropzone.setAttribute('data-aslab-id', selectVal);
            
            // Update Headers & Progress
            const aslabData = window.transferAslabs[selectVal];
            const textEl = document.getElementById(`transfer-text-${pane}`);
            const progressEl = document.getElementById(`transfer-progress-${pane}`);
            const glowEl = document.getElementById(`transfer-glow-${pane}`);
            
            const count = list.length;
            const kuota = aslabData.kuota;
            
            if (selectVal === 'unassigned') {
                textEl.textContent = `${count} Orang`;
                textEl.className = 'text-zinc-600 transition-colors duration-500';
                progressEl.style.width = '0%';
                if (glowEl) glowEl.className = 'absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-zinc-300 to-zinc-400 opacity-60 transition-all duration-700';
            } else {
                textEl.textContent = `${count} / ${kuota} (Kapasitas Maks)`;
                const isFull = count >= kuota;
                const pct = Math.min(100, (count / kuota) * 100);
                progressEl.style.width = `${pct}%`;
                
                if (isFull) {
                    textEl.className = 'text-rose-600 font-bold transition-colors duration-500';
                    progressEl.className = 'h-full rounded-full bg-rose-500 transition-all duration-700 ease-out';
                    if(glowEl) glowEl.className = 'absolute top-0 left-0 w-full h-1.5 bg-rose-500 opacity-80 transition-all duration-700';
                } else {
                    textEl.className = 'text-[#001f3f] font-bold transition-colors duration-500';
                    progressEl.className = 'h-full rounded-full bg-[#001f3f] transition-all duration-700 ease-out';
                    if(glowEl) glowEl.className = 'absolute top-0 left-0 w-full h-1.5 bg-[#001f3f] opacity-80 transition-all duration-700';
                }
            }
            
            // Update Avatar
            const avatarEl = document.getElementById(`transfer-avatar-${pane}`);
            if (avatarEl && aslabData.avatar) {
                avatarEl.src = aslabData.avatar;
            }
        }

        $(document).ready(function() {
            // Check LocalStorage for preferred view mode or default to table
            const preferredMode = localStorage.getItem('praktikumViewMode') || 'table';
            switchMode(preferredMode);
            if ($('#studentTable').length > 0) {
                // Initialize aslab data mapping for frontend modals
                window.aslabData = {
                    @foreach($praktikum->aslabs as $as)
                    "{{ $as->id }}": {
                        name: "{{ $as->user->name }}",
                        kuota: {{ $as->pivot->kuota }},
                        students: [
                            @foreach($as->assignedStudents()->where('praktikum_id', $praktikum->id)->get() as $assigned)
                                "{{ addslashes($assigned->praktikan->user->name) }}",
                            @endforeach
                        ]
                    },
                    @endforeach
                };

                var table = $('#studentTable').DataTable({
                    dom: 't<"flex flex-col sm:flex-row items-center justify-between px-6 py-4 border-t border-zinc-100"ip>',
                    language: {
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ praktikan",
                        emptyTable: "<div class='py-20 flex flex-col items-center justify-center space-y-3'><div class='h-16 w-16 rounded-2xl bg-zinc-50 flex items-center justify-center border border-zinc-100'><i class='fas fa-users-slash text-2xl text-zinc-200'></i></div><div><p class='text-zinc-900 font-bold uppercase tracking-tight'>Belum ada praktikan</p><p class='text-zinc-500 text-xs italic mt-1'>Mahasiswa belum mendaftar pada praktikum ini.</p></div></div>",
                        paginate: {
                            next: '<i class="fas fa-chevron-right text-[10px]"></i>',
                            previous: '<i class="fas fa-chevron-left text-[10px]"></i>'
                        }
                    },
                    columnDefs: [{
                        orderable: false,
                        targets: [0, 2, 3, 4]
                    }]
                });

                // Checkbox Logic
                $('#selectAll').on('change', function() {
                    $('.student-checkbox').prop('checked', $(this).prop('checked'));
                    toggleBulkActionBar();
                });

                $('.student-checkbox').on('change', function() {
                    // Update "Select All" state
                    if($('.student-checkbox:checked').length === $('.student-checkbox').length) {
                        $('#selectAll').prop('checked', true);
                    } else {
                        $('#selectAll').prop('checked', false);
                    }
                    toggleBulkActionBar();
                });
                
                // Re-bind checkboxes after DataTable pagination/search happens
                table.on('draw', function() {
                    $('#selectAll').prop('checked', false);
                    toggleBulkActionBar();
                });

                $('#studentSearch').on('keyup', function() {
                    table.search(this.value).draw();
                });

                $('#filterSesi').on('change', function() {
                    let val = $.fn.dataTable.util.escapeRegex($(this).val());
                    table.column(2).search(val ? $.fn.dataTable.util.escapeRegex(val) : '', true, false).draw();
                });

                $('#filterAslab').on('change', function() {
                    let val = $(this).val();
                    if(val === 'Belum Ada') {
                        // Regex to search explicitly for "Pilih Aslab"
                        table.column(3).search('Pilih Aslab', false, false).draw();
                    } else if (val) {
                        table.column(3).search($.fn.dataTable.util.escapeRegex(val), true, false).draw();
                    } else {
                        table.column(3).search('', true, false).draw();
                    }
                });

                // Initialize Sortable for Transfer List
                const dropzones = document.querySelectorAll('.kanban-dropzone');
                dropzones.forEach(zone => {
                    new Sortable(zone, {
                        group: 'aslab-assignments', // set both lists to same group
                        animation: 150,
                        ghostClass: 'sortable-ghost', // Custom styled ghost
                        dragClass: 'ring-2',
                        forceFallback: true,      // Bypass HTML5 DnD for better control
                        onEnd: async function (evt) {
                            const itemEl = evt.item;  // dragged HTMLElement
                            const toList = evt.to;    // target list
                            const fromList = evt.from;
                            
                            if (toList === fromList) return; // Order changed without moving to another bucket
                            
                            const newAslabIdRaw = toList.getAttribute('data-aslab-id');
                            const oldAslabIdRaw = fromList.getAttribute('data-aslab-id');
                            
                            if (newAslabIdRaw === oldAslabIdRaw) {
                                // Revert visual drag-and-drop safely
                                setTimeout(() => {
                                    renderTransferPane('left');
                                    renderTransferPane('right');
                                }, 10);
                                return;
                            }
                            
                            const newAslabId = newAslabIdRaw === 'unassigned' ? null : newAslabIdRaw;
                            
                            const pendaftaranId = itemEl.getAttribute('data-pendaftaran-id');
                            const token = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '{{ csrf_token() }}';

                            // API Call to Update Data
                            try {
                                const url = `{{ url('admin/praktikum/pendaftaran') }}/${pendaftaranId}/assign-aslab`;
                                const response = await fetch(url, {
                                    method: 'PATCH',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': token
                                    },
                                    body: JSON.stringify({ aslab_id: newAslabId })
                                });

                                const data = await response.json();

                                if (response.ok && data.success) {
                                    Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 1500 })
                                        .fire({ icon: 'success', title: 'Berhasil dipindahkan' });
                                    
                                    // Update global state array
                                    const studentIndex = window.transferStudents.findIndex(s => s.id == pendaftaranId);
                                    if(studentIndex > -1){
                                        window.transferStudents[studentIndex].aslab_id = newAslabId;
                                    }
                                    
                                    // Re-render both panes to recalculate counts and update logic cleanly
                                    setTimeout(() => {
                                        renderTransferPane('left');
                                        renderTransferPane('right');
                                    }, 10);
                                    
                                    // Update DataTable Select if it exists
                                    const selectElement = document.querySelector(`#assign-aslab-form-${pendaftaranId} select`);
                                    if(selectElement) {
                                        selectElement.value = newAslabId || ""; 
                                        selectElement.setAttribute('data-original-value', newAslabId || "");
                                        let hiddenDiv = selectElement.closest('form').querySelector('.aslab-hidden-data');
                                        if(hiddenDiv) {
                                            hiddenDiv.textContent = newAslabId ? window.transferAslabs[newAslabId].name : 'Pilih Aslab';
                                        }
                                    }
                                } else {
                                    throw new Error(data.message || 'Terjadi kesalahan sistem');
                                }
                            } catch (error) {
                                // Important: Let Sortable keep the physical DOM where it is natively, BUT when we call renderTransferPane it completely resets HTML based on accurate State!
                                setTimeout(() => {
                                    renderTransferPane('left');
                                    renderTransferPane('right');
                                }, 10);
                                
                                Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 })
                                    .fire({ icon: 'error', title: error.message });
                            }
                        }
                    });
                });
            }
        });

        // Add event listener to View Buttons to save to LocalStorage
        $('.view-mode-btn').on('click', function() {
            const isTransfer = $(this).attr('id') === 'btnModeKanban';
            localStorage.setItem('praktikumViewMode', isTransfer ? 'kanban' : 'table');
        });

        function toggleBulkActionBar() {
            const selectedCount = $('.student-checkbox:checked').length;
            $('#selectedCount').text(selectedCount);
            
            if (selectedCount > 0) {
                $('#bulkActionBar').removeClass('hidden');
            } else {
                $('#bulkActionBar').addClass('hidden');
            }
        }

        async function executeBulkAssign() {
            const selectedIds = $('.student-checkbox:checked').map(function() { return $(this).val(); }).get();
            const aslabId = $('#bulkAslabSelect').val();
            const token = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '{{ csrf_token() }}';

            if (!aslabId) {
                Swal.fire({icon: 'warning', title: 'Oops...', text: 'Pilih Aslab tujuan terlebih dahulu!'});
                return;
            }

            const targetAslab = window.aslabData[aslabId];
            let studentListHtml = '';
            if(targetAslab.students.length > 0) {
                studentListHtml = `<div class="max-h-32 overflow-y-auto text-left text-xs bg-zinc-50 p-3 rounded border border-zinc-200 mt-3 mb-4 shadow-inner">
                    <strong class="text-zinc-700 block mb-1">Daftar Praktikan Saat Ini (${targetAslab.students.length}/${targetAslab.kuota}):</strong>
                    <ul class="list-disc pl-5 space-y-0.5 text-zinc-600">`;
                targetAslab.students.forEach(s => {
                    studentListHtml += `<li>${s}</li>`;
                });
                studentListHtml += `</ul></div>`;
            } else {
                studentListHtml = `<div class="text-xs bg-zinc-50 p-3 rounded border border-zinc-200 mt-3 mb-4 text-left text-zinc-500 italic">Aslab ini belum memiliki praktikan (0/${targetAslab.kuota}).</div>`;
            }

            Swal.fire({
                title: 'Konfirmasi Pindah Aslab',
                html: `Anda akan memindahkan <strong>${selectedIds.length}</strong> mahasiswa ke <strong>${targetAslab.name}</strong>.<br>` + studentListHtml + `<span class="text-sm font-semibold">Lanjutkan pemindahan?</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#001f3f',
                cancelButtonColor: '#f4f4f5',
                confirmButtonText: 'Ya, Pindahkan!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: { confirmButton: 'bg-[#001f3f]' }
            }).then(async (result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                    
                    try {
                        const response = await fetch('{{ route('admin.praktikum.bulk-assign-aslab', $praktikum->id) }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
                            body: JSON.stringify({ pendaftaran_ids: selectedIds, aslab_id: aslabId })
                        });

                        const data = await response.json();

                        if (response.ok && data.success) {
                            Swal.fire('Berhasil!', data.message, 'success').then(() => window.location.reload());
                        } else {
                            throw new Error(data.message || 'Terjadi kesalahan sistem');
                        }
                    } catch (error) {
                        Swal.fire('Gagal!', error.message, 'error');
                    }
                }
            });
        }


        function confirmAutoAssign() {
            Swal.fire({
                title: 'Distribusi Otomatis?',
                text: "Mahasiswa yang BELUM memiliki Aslab akan dibagikan secara merata ke Aslab yang masih memiliki kuota kosong. Lanjutkan?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#001f3f',
                cancelButtonColor: '#f4f4f5',
                confirmButtonText: 'Ya, Eksekusi!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: { confirmButton: 'bg-[#001f3f]' }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    document.getElementById('autoAssignForm').submit();
                }
            });
        }

        async function updateAssignment(selectElement, url) {
            const originalValue = selectElement.getAttribute('data-original-value');
            const form = selectElement.closest('form');
            const token = form.querySelector('input[name="_token"]').value;
            const paramName = selectElement.name;
            const paramValue = selectElement.value;

            // Optional update hidden div text dynamically so searching keeps working properly
            let textValue = selectElement.options[selectElement.selectedIndex].text;
            let hiddenDiv = form.querySelector('.sesi-hidden-data, .aslab-hidden-data');
            
            selectElement.disabled = true;
            selectElement.classList.add('opacity-50');
            
            try {
                const response = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({ [paramName]: paramValue })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    selectElement.setAttribute('data-original-value', paramValue);
                    if(hiddenDiv) {
                        // Strip out " (curr/max)" from textValue for reliable searching
                        textValue = textValue.replace(/\s\(\d+\/\d+\)$/, '');
                        hiddenDiv.textContent = textValue;
                    }
                    
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                    });
                    Toast.fire({ icon: 'success', title: data.message });
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan sistem');
                }
            } catch (error) {
                // Revert
                selectElement.value = originalValue;
                
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                });
                Toast.fire({ icon: 'error', title: error.message });
            } finally {
                selectElement.disabled = false;
                selectElement.classList.remove('opacity-50');
            }
        }
    </script>
@endsection
