    {{-- ── STYLES ──────────────────────────────────────────────────────── --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap');

        /* Kanban column height */
        .kanban-height {
            height: calc(100vh - 320px);
            min-height: 500px;
        }

        @media (max-width: 767px) {
            .kanban-height {
                height: 50vh;
                min-height: 400px;
            }
        }

        /* Custom Scrollbar for Kanban */
        .kanban-dropzone::-webkit-scrollbar {
            width: 4px;
        }

        .kanban-dropzone::-webkit-scrollbar-track {
            background: transparent;
        }

        .kanban-dropzone::-webkit-scrollbar-thumb {
            background: #e4e4e7;
            border-radius: 10px;
        }

        .kanban-dropzone:hover::-webkit-scrollbar-thumb {
            background: #d4d4d8;
        }

        /* Sortable ghost */
        .sortable-ghost {
            opacity: 0.2;
            background: #f8fafc !important;
            border: 2px dashed #cbd5e1 !important;
            box-shadow: none !important;
        }

        .sortable-drag {
            opacity: 1 !important;
            transform: rotate(2deg) scale(1.02);
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1) !important;
            cursor: grabbing !important;
        }

        /* Animation */
        @keyframes kanbanFadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .kanban-animate-in {
            animation: kanbanFadeIn 0.3s ease-out forwards;
        }

        /* DataTables overrides */
        .dataTables_wrapper .dataTables_info {
            font-size: 11px;
            color: #71717a;
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

        /* Shimmer Animation */
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        /* Focus Ring Customization */
        .focus-ring-blue {
            @apply outline-none ring-4 ring-[#001f3f]/5 border-[#001f3f];
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
            list.forEach((s, idx) => {
                const delay = Math.min(idx * 30, 300);
                html += `
                    <div class="kanban-animate-in group/card relative bg-white border border-zinc-200 p-3 rounded-xl shadow-sm hover:shadow-md hover:border-zinc-300 transition-all cursor-grab active:cursor-grabbing" 
                         style="animation-delay: ${delay}ms"
                         data-pendaftaran-id="${s.id}">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-bold text-zinc-900 truncate group-hover/card:text-[#001f3f] transition-colors leading-tight mb-2">${s.name || 'N/A'}</h4>
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span class="inline-flex items-center font-mono text-[10px] text-zinc-500 bg-zinc-50 px-1.5 py-0.5 rounded border border-zinc-200/60 font-medium">
                                        ${s.npm || '-'}
                                    </span>
                                    <span class="inline-flex items-center text-[9px] font-extrabold uppercase tracking-wider text-[#001f3f]/80 bg-[#001f3f]/5 px-1.5 py-0.5 rounded border border-[#001f3f]/10">
                                        <i class="fas fa-clock mr-1 text-[8px] opacity-40"></i>
                                        ${s.sesi || '-'}
                                    </span>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="w-7 h-7 flex items-center justify-center rounded-lg bg-zinc-50 group-hover/card:bg-zinc-100 text-zinc-300 group-hover/card:text-zinc-500 transition-all border border-transparent group-hover/card:border-zinc-200">
                                    <i class="fas fa-grip-vertical text-[10px]"></i>
                                </div>
                            </div>
                        </div>
                    </div>`;
            });
            
            if (!html) {
                html = `
                    <div class="flex flex-col items-center justify-center h-full py-12 px-6 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-zinc-50 flex items-center justify-center mb-4 border border-zinc-100">
                            <i class="fas fa-inbox text-2xl text-zinc-200"></i>
                        </div>
                        <h5 class="text-sm font-bold text-zinc-900 mb-1">Kosong</h5>
                        <p class="text-xs text-zinc-400 font-medium max-w-[180px]">Tidak ada mahasiswa yang terdaftar di aslab ini.</p>
                    </div>`;
            }
            
            dropzone.innerHTML = html;
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
                    textEl.textContent = `${count} Praktikan`;
                    textEl.className = 'text-[10px] font-bold font-mono text-zinc-500 bg-zinc-100 px-2 py-0.5 rounded-full';
                    progressEl.style.width = '0%';
                    if (glowEl) glowEl.className = 'absolute -bottom-1 -right-1 w-3 h-3 rounded-full bg-zinc-300 border-2 border-white ring-1 ring-zinc-200';
                } else {
                    const isFull = count >= kuota && kuota > 0;
                    const pct = kuota > 0 ? Math.min(100, (count / kuota) * 100) : 0;
                    
                    textEl.textContent = `${count} / ${kuota}`;
                    progressEl.style.width = `${pct}%`;
                    
                    if (isFull) {
                        textEl.className = 'text-[10px] font-bold font-mono text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-100';
                        progressEl.className = 'h-full bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.3)]';
                        if (glowEl) glowEl.className = 'absolute -bottom-1 -right-1 w-3 h-3 rounded-full bg-rose-500 border-2 border-white ring-1 ring-rose-200 animate-pulse';
                    } else {
                        textEl.className = 'text-[10px] font-bold font-mono text-[#001f3f] bg-[#001f3f]/5 px-2 py-0.5 rounded-full border border-[#001f3f]/10';
                        progressEl.className = 'h-full bg-[#001f3f] shadow-[0_0_8px_rgba(0,31,63,0.3)]';
                        if (glowEl) glowEl.className = 'absolute -bottom-1 -right-1 w-3 h-3 rounded-full bg-emerald-500 border-2 border-white ring-1 ring-emerald-200';
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
                columnDefs: [{ orderable: false, targets: [0, 3] }]
            });

            // Filters
            $('#studentSearch').on('keyup', function () { table.search(this.value).draw(); });

            // Ctrl + K shortcut
            $(document).on('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    $('#studentSearch').focus();
                }
            });

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
        async function previewImport(input) {
            if (!input.files || !input.files[0]) return;
            
            openImportModal();
            const formData = new FormData();
            formData.append('file', input.files[0]);
            formData.append('_token', '{{ csrf_token() }}');

            try {
                const response = await fetch('{{ route('admin.praktikum.import-students', $praktikum->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                if (!response.ok) throw new Error('Gagal mengunggah file.');

                const html = await response.text();
                document.getElementById('importModalContent').innerHTML = html;
            } catch (error) {
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: error.message
                });
                closeImportModal();
            } finally {
                input.value = ''; // Reset input
            }
        }

        function openImportModal() {
            const modal = document.getElementById('importReviewModal');
            document.getElementById('importModalContent').innerHTML = `
                <div class="p-12 flex flex-col items-center justify-center space-y-4">
                    <div class="w-16 h-16 border-4 border-[#001f3f]/10 border-t-[#001f3f] rounded-full animate-spin"></div>
                    <p class="text-xs font-black text-[#001f3f] uppercase tracking-widest text-center">Menganalisis File CSV...<br><span class="text-[9px] text-zinc-400 font-medium normal-case">Mohon tunggu sebentar</span></p>
                </div>
            `;
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeImportModal() {
            const modal = document.getElementById('importReviewModal');
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    </script>
