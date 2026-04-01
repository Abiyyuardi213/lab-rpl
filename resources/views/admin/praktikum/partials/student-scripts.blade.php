<style>
    .kanban-height {
        height: calc(100vh - 400px);
        min-height: 500px;
    }
    .sortable-ghost {
        opacity: 0.2;
        background: #f8fafc !important;
        border: 2px dashed #cbd5e1 !important;
    }
    .kanban-card-fade-in {
        animation: kanbanFadeIn 0.3s ease-out forwards;
    }
    @keyframes kanbanFadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* DataTables Custom Styling */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        display: inline-block !important;
        border: 1px solid #e4e4e7 !important;
        border-radius: 8px !important;
        padding: 6px 12px !important;
        margin-left: 6px !important;
        font-size: 10px !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        cursor: pointer !important;
        background: white !important;
        color: #71717a !important;
        transition: all 0.2s !important;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03) !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.current) {
        background: #f4f4f5 !important;
        color: #18181b !important;
        border-color: #d4d4d8 !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #001f3f !important;
        color: white !important;
        border-color: #001f3f !important;
        box-shadow: 0 4px 6px -1px rgba(0, 31, 63, 0.2) !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
        opacity: 0.4 !important;
        cursor: not-allowed !important;
        pointer-events: none !important;
    }

    .dataTables_wrapper .dataTables_info {
        font-size: 10px !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.1em !important;
        color: #a1a1aa !important;
    }
</style>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<script>
    /* ─── GLOBAL SWITCH MODE ─── */
    window.switchMode = function(mode) {
        console.log('Switching view to:', mode);
        const ts = document.getElementById('transfer-section');
        const ms = document.getElementById('mahasiswa-section');
        
        if (mode === 'kanban') {
            if (ms) ms.classList.add('hidden');
            if (ts) ts.classList.remove('hidden');
            renderTransferPane('left');
            renderTransferPane('right');
        } else {
            if (ts) ts.classList.add('hidden');
            if (ms) ms.classList.remove('hidden');
        }

        const activeCls = "flex items-center gap-1.5 px-4 py-1.5 text-[10px] font-black uppercase tracking-wider rounded-md bg-[#001f3f] text-white shadow-lg shadow-[#001f3f]/20 transition-all active:scale-95";
        const inactiveCls = "flex items-center gap-1.5 px-4 py-1.5 text-[10px] font-black uppercase tracking-wider rounded-md text-zinc-500 hover:bg-zinc-50 hover:text-zinc-800 transition-all";

        [document.getElementById('btnModeTable'), document.getElementById('btnModeTable2')].forEach(btn => {
            if (btn) btn.className = (mode === 'table' ? activeCls : inactiveCls);
        });

        [document.getElementById('btnModeKanban'), document.getElementById('btnModeKanban2')].forEach(btn => {
            if (btn) btn.className = (mode === 'kanban' ? activeCls : inactiveCls);
        });

        localStorage.setItem('praktikumViewMode', mode);
    };

    /* ─── KANBAN RENDER ─── */
    window.renderTransferPane = function(pane) {
        const select = document.getElementById(`transfer-select-${pane}`);
        const dropzone = document.getElementById(`transfer-dropzone-${pane}`);
        if (!select || !dropzone) return;

        const aslabId = select.value === 'unassigned' ? null : select.value;
        const students = window.transferStudents.filter(s => {
            const sAid = s.aslab_id ? String(s.aslab_id) : null;
            const tId = aslabId ? String(aslabId) : null;
            return sAid === tId;
        });

        // Add sorting
        let html = '';
        students.forEach((s, i) => {
            html += `
                <div class="kanban-card-fade-in bg-white border border-zinc-200 p-3 rounded-xl shadow-sm hover:border-zinc-300 transition-all cursor-grab active:cursor-grabbing" 
                     data-pendaftaran-id="${s.id}" style="animation-delay: ${Math.min(i*30, 300)}ms">
                    <div class="flex flex-col gap-1.5">
                        <h4 class="text-[11px] font-black text-zinc-900 uppercase leading-tight truncate">${s.name}</h4>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="text-[9px] font-mono text-zinc-500 bg-zinc-50 border border-zinc-100 px-1 rounded">${s.npm}</span>
                            <span class="text-[8px] font-black uppercase text-[#001f3f]/60 bg-[#001f3f]/5 px-1 rounded">${s.sesi}</span>
                        </div>
                    </div>
                </div>`;
        });

        if (!html) {
            html = `<div class="p-8 text-center"><p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest">Kosong</p></div>`;
        }

        dropzone.innerHTML = html;
        dropzone.setAttribute('data-aslab-id', select.value);
        
        // Stats
        const countEl = document.getElementById(`transfer-count-${pane}`);
        if (countEl) countEl.innerText = students.length;

        // Init Sortable
        if (dropzone._sortable) dropzone._sortable.destroy();
        dropzone._sortable = new Sortable(dropzone, {
            group: 'students',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: async function(evt) {
                const sid = evt.item.getAttribute('data-pendaftaran-id');
                const aid = evt.to.getAttribute('data-aslab-id');
                const realAid = aid === 'unassigned' ? null : aid;

                // Optimistic Local
                const std = window.transferStudents.find(s => String(s.id) === String(sid));
                if (std) std.aslab_id = realAid;
                renderTransferPane('left');
                renderTransferPane('right');

                // Server Sync
                const token = document.querySelector('meta[name="csrf-token"]')?.content;
                try {
                    const r = await fetch(`{{ url('admin/praktikum/pendaftaran') }}/${sid}/assign-aslab`, {
                        method: 'PATCH',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                        body: JSON.stringify({ aslab_id: realAid })
                    });
                    if (!r.ok) throw new Error('Gagal sync ke server');
                } catch(e) { console.error(e); }
            }
        });
    };

    /* ─── INITIALIZATION ─── */
    $(document).ready(function() {
        const table = $('#studentTable').DataTable({
            dom: 't<"flex items-center justify-between px-6 py-4 border-t border-zinc-100"ip>',
            language: { info: '<span class="text-[10px] font-bold uppercase text-zinc-400">_TOTAL_ Praktikan</span>' },
            columnDefs: [{ orderable: false, targets: [0, 2, 3] }]
        });

        $('#studentSearch').on('keyup', function() { table.search(this.value).draw(); });
        $('#filterSesi').on('change', function() { table.column(2).search(this.value).draw(); });
        $('#filterAslab').on('change', function() {
            const val = $(this).val();
            if (val === 'Belum Ada') table.column(3).search('— Pilih Aslab —').draw();
            else table.column(3).search(val).draw();
        });

        // Persistent View Mode
        const mode = localStorage.getItem('praktikumViewMode') || 'table';
        window.switchMode(mode);

        // Bulk
        $('#selectAll').on('change', function() { $('.student-checkbox').prop('checked', this.checked); toggleBulkActionBar(); });
        $(document).on('change', '.student-checkbox', toggleBulkActionBar);
    });

    function toggleBulkActionBar() {
        const n = $('.student-checkbox:checked').length;
        $('#selectedCount').text(n);
        $('#bulkActionBar').toggleClass('hidden', n === 0);
    }

    async function executeBulkAssign() {
        const ids = $('.student-checkbox:checked').map(function() { return $(this).val(); }).get();
        const aid = $('#bulkAslabSelect').val();
        if (!aid) return;

        Swal.fire({ title: 'Pindahkan?', text: `Pindahkan ${ids.length} praktikan?`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#001f3f' }).then(async r => {
            if (!r.isConfirmed) return;
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            try {
                const res = await fetch(`{{ route('admin.praktikum.bulk-assign-aslab', $praktikum->id) }}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                    body: JSON.stringify({ pendaftaran_ids: ids, aslab_id: aid })
                });
                if (res.ok) window.location.reload();
            } catch(e) { Swal.fire('Error', e.message, 'error'); }
        });
    }

    async function updateAssignment(el, url) {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        const oldVal = el.getAttribute('data-original-value');
        const payload = { [el.name || 'aslab_id']: el.value };
        
        console.log('--- Updating Assignment ---');
        console.log('URL:', url);
        console.log('Payload:', payload);

        el.disabled = true;
        try {
            const r = await fetch(url, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                body: JSON.stringify(payload)
            });

            const result = await r.json().catch(() => ({}));
            console.log('Status:', r.status);
            console.log('Response:', result);

            if (r.ok) {
                el.setAttribute('data-original-value', el.value);
                Swal.fire({ icon: 'success', title: 'Berhasil', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
            } else {
                throw new Error(result.message || 'Gagal memperbarui data');
            }
        } catch(e) {
            console.error('Update Error:', e);
            el.value = oldVal;
            Swal.fire('Gagal', e.message, 'error');
        }
        el.disabled = false;
    }

    async function previewImport(input) {
        if (!input.files || !input.files[0]) return;
        const formData = new FormData();
        formData.append('file', input.files[0]);
        formData.append('_token', '{{ csrf_token() }}');
        try {
            const r = await fetch('{{ route('admin.praktikum.import-students', $praktikum->id) }}', { method: 'POST', body: formData });
            if (r.ok) { const html = await r.text(); document.getElementById('importReviewModal').classList.remove('hidden'); document.getElementById('importModalContent').innerHTML = html; }
        } catch(e) { Swal.fire('Error', 'Gagal membac file', 'error'); }
    }
</script>
