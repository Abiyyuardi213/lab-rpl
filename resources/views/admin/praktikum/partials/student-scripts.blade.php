    {{-- ── STYLES ──────────────────────────────────────────────────────── --}}
    <style>

        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap');

        /* Breakpoint helper */
        @media (min-width: 480px) {
            .xs\:flex-row {
                flex-direction: row;
            }

            .xs\:items-center {
                align-items: center;
            }

            .xs\:flex-none {
                flex: none;
            }

            .xs\:w-52 {
                width: 13rem;
            }

            .xs\:inline {
                display: inline;
            }

            .xs\:w-auto {
                width: auto;
            }
        }

        /* Kanban column height — full on desktop, fixed on mobile */
        .kanban-height {
            height: 60vh;
            min-height: 360px;
        }

        @media (max-width: 767px) {
            .kanban-height {
                height: 50vh;
                min-height: 320px;
            }
        }

        /* Kanban column glass effect */
        .kanban-col {
            background: rgba(250, 250, 252, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        /* Kanban cards */
        .kanban-card {
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 1px 4px -1px rgba(0, 0, 0, 0.04), 0 0 0 1px rgba(0, 0, 0, 0.04);
        }

        .kanban-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(0, 31, 63, 0.08);
        }

        .kanban-card:active {
            transform: translateY(0) scale(0.99);
        }

        /* Sortable ghost */
        .sortable-ghost {
            opacity: 0.4;
            background: #f1f5f9 !important;
            border: 2px dashed #cbd5e1 !important;
            box-shadow: none !important;
        }

        /* Scrollbar */
        .kanban-dropzone::-webkit-scrollbar {
            width: 3px;
        }

        .kanban-dropzone::-webkit-scrollbar-track {
            background: transparent;
        }

        .kanban-dropzone::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 99px;
        }

        .kanban-dropzone:hover::-webkit-scrollbar-thumb {
            background: #cbd5e1;
        }

        /* Table scroll hint on mobile */
        @media (max-width: 640px) {
            .overflow-x-auto::after {
                content: '';
                position: absolute;
                right: 0;
                top: 0;
                bottom: 0;
                width: 32px;
                background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.9));
                pointer-events: none;
            }
        }

        /* Stagger animation for Transfer section */
        @keyframes slideUpFade {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-stagger>div {
            animation: slideUpFade 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }

        .animate-stagger>div:nth-child(1) {
            animation-delay: 0.04s;
        }

        .animate-stagger>div:nth-child(2) {
            animation-delay: 0.08s;
        }

        .animate-stagger>div:nth-child(3) {
            animation-delay: 0.12s;
        }

        /* DataTables overrides */
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
            padding: 5px 12px !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            margin-left: 4px !important;
            background: white !important;
            color: #71717a !important;
            transition: all 0.15s !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #f4f4f5 !important;
            color: #18181b !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #001f3f !important;
            border-color: #001f3f !important;
            color: white !important;
        }

        .dataTables_wrapper .dataTables_paginate {
            padding-top: 2px;
        }
    </style>

    {{-- ── SCRIPTS ──────────────────────────────────────────────────────── --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    <script>
        /* ─── Global State & View Toggle ─── */
        window.switchMode = function(mode) {
            console.log('Switching to mode:', mode);
            const ts = document.getElementById('transfer-section');
            const ms = document.getElementById('mahasiswa-section');
            const btns = {
                table: [document.getElementById('btnModeTable'), document.getElementById('btnModeTable2')],
                kanban: [document.getElementById('btnModeKanban'), document.getElementById('btnModeKanban2')]
            };

            const active = ['bg-[#001f3f]', 'text-white'];
            const inactive = ['text-zinc-500', 'hover:bg-zinc-50', 'hover:text-zinc-800'];

            if (mode === 'kanban') {
                if (ms) ms.classList.add('hidden');
                if (ts) ts.classList.remove('hidden');
                btns.kanban.forEach(b => b && (b.classList.add(...active), b.classList.remove(...inactive)));
                btns.table.forEach(b => b && (b.classList.remove(...active), b.classList.add(...inactive)));
                renderTransferPane('left');
                renderTransferPane('right');
            } else {
                if (ts) ts.classList.add('hidden');
                if (ms) ms.classList.remove('hidden');
                btns.table.forEach(b => b && (b.classList.add(...active), b.classList.remove(...inactive)));
                btns.kanban.forEach(b => b && (b.classList.remove(...active), b.classList.add(...inactive)));
            }
            localStorage.setItem('praktikumViewMode', mode);
        };

        window.renderTransferPane = function(pane) {
            const selectEl = document.getElementById(`transfer-select-${pane}`);
            const dropzone = document.getElementById(`transfer-dropzone-${pane}`);
            if (!selectEl || !dropzone) return;

            const selectVal = selectEl.value;
            const targetId = selectVal === 'unassigned' ? null : selectVal;
            
            if (!window.transferStudents) return;

            const list = window.transferStudents.filter(s => {
                const sAid = s.aslab_id === null ? null : String(s.aslab_id);
                const tId = targetId === null ? null : String(targetId);
                return sAid === tId;
            });
            
            const aslabData = window.transferAslabs ? window.transferAslabs[selectVal] : null;
            if (!aslabData) return;

            // Render Cards
            let html = '';
            list.forEach(s => {
                html += `
                    <div class="kanban-card bg-white px-3.5 py-3 rounded-xl cursor-grab active:cursor-grabbing group/card border border-zinc-100 hover:border-[#001f3f]/15 transition-all" data-pendaftaran-id="${s.id}">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-bold text-[12px] text-[#001f3f] leading-tight truncate">${s.name || 'N/A'}</span>
                            <i class="fas fa-grip-dots-vertical text-zinc-200 group-hover/card:text-zinc-300 transition-colors flex-shrink-0 text-[10px]"></i>
                        </div>
                        <div class="flex items-center gap-1.5 mt-2 flex-wrap">
                            <span class="font-mono text-[10px] text-zinc-500 bg-zinc-50 px-2 py-0.5 rounded-md border border-zinc-100">${s.npm || '-'}</span>
                            <span class="text-[9px] text-[#001f3f]/70 bg-[#001f3f]/5 border border-[#001f3f]/8 px-2 py-0.5 rounded-md font-bold uppercase tracking-wide flex items-center gap-1">
                                <i class="fas fa-clock text-[#001f3f]/30 text-[8px]"></i> ${s.sesi || '-'}
                            </span>
                        </div>
                    </div>`;
            });
            dropzone.innerHTML = html || `<div class="flex flex-col items-center justify-center h-full py-10 text-center"><i class="fas fa-inbox text-zinc-200 text-3xl mb-3"></i><p class="text-xs text-zinc-400 font-medium">Tidak ada mahasiswa</p></div>`;
            dropzone.setAttribute('data-aslab-id', selectVal);

            // Update Stats
            const textEl = document.getElementById(`transfer-text-${pane}`);
            const progressEl = document.getElementById(`transfer-progress-${pane}`);
            const glowEl = document.getElementById(`transfer-glow-${pane}`);
            const avatarEl = document.getElementById(`transfer-avatar-${pane}`);
            
            if (avatarEl && aslabData.avatar) avatarEl.src = aslabData.avatar;

            if (textEl && progressEl) {
                const count = list.length;
                const kuota = aslabData.kuota || 0;
                if (selectVal === 'unassigned') {
                    textEl.textContent = `${count}`;
                    progressEl.style.width = '0%';
                } else {
                    const isFull = count >= kuota && kuota > 0;
                    const pct = kuota > 0 ? Math.min(100, (count / kuota) * 100) : 0;
                    textEl.textContent = `${count} / ${kuota}`;
                    progressEl.style.width = `${pct}%`;
                    if (isFull) {
                        textEl.className = 'text-xs text-rose-600 font-bold font-mono';
                        progressEl.className = 'h-full bg-rose-500';
                    } else {
                        textEl.className = 'text-xs text-[#001f3f] font-bold font-mono';
                        progressEl.className = 'h-full bg-[#001f3f]';
                    }
                }
            }

            // Init Sortable (Destroy if exists)
            if (dropzone._sortable) dropzone._sortable.destroy();
            dropzone._sortable = new Sortable(dropzone, {
                group: 'students',
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: async function (evt) {
                    const studentId = evt.item.getAttribute('data-pendaftaran-id');
                    const newAslabIdRaw = evt.to.getAttribute('data-aslab-id');
                    const newAslabId = newAslabIdRaw === 'unassigned' ? null : newAslabIdRaw;
                    
                    if (!studentId) return;
                    
                    // Old state for potential revert
                    const std = window.transferStudents.find(s => String(s.id) === String(studentId));
                    if (!std) return;
                    const oldAslabId = std.aslab_id;

                    // Optimistic Update
                    std.aslab_id = newAslabId;
                    renderTransferPane('left');
                    renderTransferPane('right');

                    const token = document.querySelector('meta[name="csrf-token"]')?.content;
                    try {
                        const baseUrl = "{{ url('admin/praktikum/pendaftaran') }}";
                        const res = await fetch(`${baseUrl}/${studentId}/assign-aslab`, {
                            method: 'PATCH',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
                            body: JSON.stringify({ aslab_id: newAslabId })
                        });
                        const data = await res.json();
                        if (!res.ok || !data.success) throw new Error(data.message || 'Gagal');

                        // Sync Table Form if exists
                        const sel = document.querySelector(`#assign-aslab-form-${studentId} select`);
                        if (sel) {
                            sel.value = newAslabId || '';
                            sel.setAttribute('data-original-value', newAslabId || '');
                            const hd = sel.closest('form').querySelector('.aslab-hidden-data');
                            if (hd) hd.textContent = newAslabId ? (window.transferAslabs[newAslabId]?.name || 'Terpilih') : 'Pilih Aslab';
                        }
                    } catch (err) {
                        // Revert
                        std.aslab_id = oldAslabId;
                        renderTransferPane('left');
                        renderTransferPane('right');
                        Swal.fire({ icon: 'error', title: 'Gagal', text: err.message, toast: true, position: 'top-end', timer: 3000, showConfirmButton: false });
                    }
                }
            });
        };

        $(document).ready(function () {
            // Data for bulk actions
            try {
                window.aslabData = {
                    @foreach($praktikum->aslabs as $as)
                        [@json($as->id)]: {
                            name: @json($as->user->name),
                            kuota: @json($as->pivot->kuota ?? 0),
                            students: [
                                @foreach($as->assignedStudents()->where('praktikum_id', $praktikum->id)->get() as $assigned)
                                    @json($assigned->praktikan->user->name),
                                @endforeach
                            ]
                        },
                    @endforeach
                };
            } catch(e) { console.warn('Aslab data init error:', e); }

            // DataTable Init
            var table = $('#studentTable').DataTable({
                dom: 't<"flex flex-col sm:flex-row items-center justify-between px-4 sm:px-6 py-4 border-t border-zinc-100 gap-3"ip>',
                language: {
                    info: "Menampilkan _START_–_END_ dari _TOTAL_ praktikan",
                    paginate: { next: '<i class="fas fa-chevron-right text-[9px]"></i>', previous: '<i class="fas fa-chevron-left text-[9px]"></i>' }
                },
                columnDefs: [{ orderable: false, targets: [0, 4] }]
            });

            // Filters
            $('#studentSearch').on('keyup', function () { table.search(this.value).draw(); });
            $('#filterSesi').on('change', function () { table.column(2).search(this.value).draw(); });
            $('#filterAslab').on('change', function () {
                const val = $(this).val();
                if (val === 'Belum Ada') table.column(3).search('Pilih Aslab').draw();
                else table.column(3).search(val || '').draw();
            });

            // Initial view
            const preferredMode = localStorage.getItem('praktikumViewMode') || 'table';
            window.switchMode(preferredMode);

            // Bulk actions bindings
            $('#selectAll').on('change', function () { $('.student-checkbox').prop('checked', this.checked); toggleBulkActionBar(); });
            $(document).on('change', '.student-checkbox', toggleBulkActionBar);
            table.on('draw', () => { $('#selectAll').prop('checked', false); toggleBulkActionBar(); });
        });

        /* --- Helper Functions --- */
        function toggleBulkActionBar() {
            const n = $('.student-checkbox:checked').length;
            $('#selectedCount').text(n);
            $('#bulkActionBar').toggleClass('hidden', n === 0);
        }

        async function executeBulkAssign() {
            const selectedIds = $('.student-checkbox:checked').map(function () { return $(this).val(); }).get();
            const aslabId = $('#bulkAslabSelect').val();
            const token = document.querySelector('meta[name="csrf-token"]')?.content;

            if (!aslabId) { Swal.fire('Pilih Aslab!', '', 'warning'); return; }
            const targetAslab = window.aslabData[aslabId];
            
            Swal.fire({
                title: 'Pindahkan Praktikan?',
                text: `Pindahkan ${selectedIds.length} mahasiswa ke ${targetAslab.name}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#001f3f',
                confirmButtonText: 'Ya, Pindahkan!'
            }).then(async (res) => {
                if (!res.isConfirmed) return;
                Swal.showLoading();
                try {
                    const r = await fetch('{{ route('admin.praktikum.bulk-assign-aslab', $praktikum->id) }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                        body: JSON.stringify({ pendaftaran_ids: selectedIds, aslab_id: aslabId })
                    });
                    const d = await r.json();
                    if (r.ok && d.success) window.location.reload();
                    else throw new Error(d.message);
                } catch(e) { Swal.fire('Gagal', e.message, 'error'); }
            });
        }

        function confirmAutoAssign() {
             Swal.fire({
                title: 'Distribusi Otomatis?',
                text: 'Bagikan mahasiswa tanpa aslab secara merata?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#001f3f',
                confirmButtonText: 'Ya!'
            }).then(r => r.isConfirmed && document.getElementById('autoAssignForm').submit());
        }

        async function updateAssignment(el, url) {
            const form = el.closest('form');
            const token = form.querySelector('input[name="_token"]').value;
            const original = el.getAttribute('data-original-value');
            el.disabled = true;
            try {
                const r = await fetch(url, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                    body: JSON.stringify({ [el.name]: el.value })
                });
                const d = await r.json();
                if (r.ok && d.success) {
                    el.setAttribute('data-original-value', el.value);
                    Swal.fire({ icon: 'success', title: 'Berhasil', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false });
                } else throw new Error(d.message);
            } catch(e) { 
                el.value = original;
                Swal.fire({ icon: 'error', title: e.message, toast: true, position: 'top-end', timer: 3000, showConfirmButton: false });
            } finally { el.disabled = false; }
        }
    </script>
