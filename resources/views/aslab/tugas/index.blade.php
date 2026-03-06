@extends('layouts.admin')

@section('title', 'Tugas Asistensi')

@section('content')
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight uppercase">Tugas Asistensi</h1>
                <p class="text-slate-500 mt-1 text-sm sm:text-base italic">"Berikan dan pantau tugas untuk mahasiswa
                    bimbingan Anda."</p>
            </div>
            <button onclick="document.getElementById('modal-tugas').classList.remove('hidden')"
                class="inline-flex items-center px-6 py-3.5 rounded-2xl bg-[#001f3f] text-white text-[10px] font-black border border-[#001f3f] shadow-xl shadow-[#001f3f]/20 uppercase tracking-widest hover:bg-[#002d5a] transition-all active:scale-[0.98] gap-3 w-fit self-end sm:self-auto">
                <i class="fas fa-plus-circle text-sm text-white/50"></i>
                Tambah Tugas
            </button>
        </div>

        <!-- Task List Container -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm shadow-slate-900/5 overflow-hidden min-h-[500px]">
            <div class="p-8 border-b border-slate-100 flex items-center justify-between bg-slate-50/30">
                <div class="flex items-center gap-4">
                    <div
                        class="h-10 w-10 rounded-xl bg-[#001f3f] flex items-center justify-center text-white shadow-lg shadow-[#001f3f]/20">
                        <i class="fas fa-tasks text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black text-slate-900 uppercase tracking-tight">Data Tugas Mahasiswa</h3>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Daftar seluruh
                            penugasan asistensi</p>
                    </div>
                </div>
                <div class="flex flex-col items-end">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Total Entri</span>
                    <span
                        class="px-3 py-1 bg-white border border-slate-200 rounded-full text-[10px] font-black text-[#001f3f] uppercase tracking-widest shadow-sm">
                        {{ $tugas->count() }} Data
                    </span>
                </div>
            </div>

            <!-- Desktop View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50/20 border-b border-slate-100">
                        <tr>
                            <th class="px-8 py-5 font-black text-slate-400 uppercase tracking-[0.2em] text-[10px]">Mahasiswa
                            </th>
                            <th class="px-8 py-5 font-black text-slate-400 uppercase tracking-[0.2em] text-[10px]">Tugas &
                                Berkas</th>
                            <th class="px-8 py-5 font-black text-slate-400 uppercase tracking-[0.2em] text-[10px]">Batas
                                Waktu</th>
                            <th class="px-8 py-5 font-black text-slate-400 uppercase tracking-[0.2em] text-[10px]">Status
                            </th>
                            <th
                                class="px-8 py-5 font-black text-slate-400 uppercase tracking-[0.2em] text-[10px] text-center">
                                Skor</th>
                            <th
                                class="px-8 py-5 font-black text-slate-400 uppercase tracking-[0.2em] text-[10px] text-right">
                                Kelola</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($tugas as $t)
                            <tr class="hover:bg-slate-50/50 transition-all group border-b border-slate-50 last:border-0">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="h-10 w-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-[#001f3f] group-hover:text-white transition-all duration-300">
                                            <i class="fas fa-user text-xs"></i>
                                        </div>
                                        <div class="flex flex-col">
                                            <span
                                                class="font-black text-slate-900 uppercase tracking-tight group-hover:text-[#001f3f] transition-colors">{{ $t->pendaftaran->praktikan->user->name ?? 'Mahasiswa' }}</span>
                                            <span
                                                class="text-[9px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-0.5 whitespace-nowrap overflow-hidden text-ellipsis max-w-[180px]">{{ $t->pendaftaran->praktikum->nama_praktikum }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="font-bold text-slate-700 uppercase text-xs tracking-tight">{{ $t->judul }}</span>
                                        @if ($t->file_tugas)
                                            <a href="{{ asset('storage/' . $t->file_tugas) }}" target="_blank"
                                                class="w-7 h-7 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center hover:bg-[#001f3f] hover:text-white transition-all shadow-sm group/btn"
                                                title="Unduh Soal">
                                                <i class="fas fa-file-download text-[10px]"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div
                                        class="flex items-center gap-2.5 text-slate-500 font-bold text-[10px] uppercase tracking-widest whitespace-nowrap">
                                        <i class="far fa-calendar-alt text-slate-300"></i>
                                        {{ $t->due_date ? $t->due_date->format('d M Y') : '-' }}
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    @php
                                        $statusBase =
                                            'inline-flex items-center px-4 py-1.5 rounded-full text-[9px] font-black border uppercase tracking-widest leading-none ';
                                        $statusMap = [
                                            'pending' => $statusBase . 'bg-slate-50 text-slate-400 border-slate-200',
                                            'submitted' => $statusBase . 'bg-amber-50 text-amber-500 border-amber-100',
                                            'reviewed' =>
                                                $statusBase . 'bg-emerald-50 text-emerald-500 border-emerald-100',
                                        ];
                                    @endphp
                                    <span class="{{ $statusMap[$t->status] ?? $statusMap['pending'] }}">
                                        {{ $t->status }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <span
                                        class="text-xl font-black tabular-nums tracking-tighter {{ $t->nilai >= 80 ? 'text-emerald-500' : ($t->nilai ? 'text-amber-500' : 'text-slate-200') }}">
                                        {{ $t->nilai ?? '??' }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div
                                        class="flex items-center justify-end gap-2.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                        @if ($t->file_mahasiswa)
                                            <a href="{{ asset('storage/' . $t->file_mahasiswa) }}" target="_blank"
                                                class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all shadow-sm border border-indigo-100">
                                                <i class="fas fa-download text-[10px]"></i>
                                            </a>
                                        @endif
                                        <button
                                            onclick="openReviewModal('{{ $t->id }}', '{{ $t->nilai }}', '{{ $t->catatan_aslab }}', '{{ $t->status }}')"
                                            class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all shadow-sm border border-emerald-100">
                                            <i class="fas fa-marker text-[10px]"></i>
                                        </button>
                                        <form action="{{ route('aslab.tugas.destroy', $t->id) }}" method="POST"
                                            class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" onclick="return confirm('Hapus tugas ini?')"
                                                class="w-9 h-9 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all shadow-sm border border-rose-100">
                                                <i class="fas fa-trash-alt text-[10px]"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-8 py-24 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div
                                            class="h-16 w-16 rounded-full bg-slate-50 flex items-center justify-center mb-4 border border-slate-100">
                                            <i class="fas fa-folder-open text-slate-200 text-2xl"></i>
                                        </div>
                                        <h5 class="text-slate-400 font-black uppercase tracking-widest text-xs">Data Kosong
                                        </h5>
                                        <p class="text-[10px] text-slate-400 italic mt-1 font-medium tracking-tight">Belum
                                            ada tugas yang diberikan kepada mahasiswa bimbingan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile View -->
            <div class="md:hidden divide-y divide-slate-100">
                @forelse($tugas as $t)
                    <div class="p-6 space-y-4 hover:bg-slate-50/50 transition-all active:bg-slate-50">
                        <div class="flex justify-between items-start">
                            <div class="flex flex-col gap-1 pr-4">
                                <span
                                    class="font-black text-slate-900 uppercase text-xs tracking-tight leading-tight">{{ $t->pendaftaran->praktikan->user->name ?? 'Mahasiswa' }}</span>
                                <span
                                    class="text-[9px] text-slate-400 font-bold uppercase tracking-widest line-clamp-1 italic">{{ $t->pendaftaran->praktikum->nama_praktikum }}</span>
                            </div>
                            @php
                                $statusMcl = [
                                    'pending' => 'bg-slate-100 text-slate-400',
                                    'submitted' => 'bg-amber-100 text-amber-600',
                                    'reviewed' => 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/10',
                                ];
                            @endphp
                            <span
                                class="px-2.5 py-1 rounded-full text-[8px] font-black uppercase tracking-widest whitespace-nowrap {{ $statusMcl[$t->status] ?? $statusMcl['pending'] }}">
                                {{ $t->status }}
                            </span>
                        </div>

                        <div
                            class="bg-white p-4 rounded-2xl border border-slate-100 space-y-3 shadow-sm shadow-slate-900/[0.02]">
                            <div class="flex justify-between items-center text-[9px] font-black uppercase tracking-widest">
                                <span class="text-slate-400">Judul Tugas:</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-slate-900">{{ $t->judul }}</span>
                                    @if ($t->file_tugas)
                                        <a href="{{ asset('storage/' . $t->file_tugas) }}" target="_blank"
                                            class="text-[#001f3f]">
                                            <i class="fas fa-file-download text-xs"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="flex justify-between items-center text-[9px] font-black uppercase tracking-widest">
                                <span class="text-slate-400">Deadline:</span>
                                <span
                                    class="text-slate-700">{{ $t->due_date ? $t->due_date->format('d M Y') : '-' }}</span>
                            </div>
                            <div
                                class="pt-2 border-t border-slate-100 flex justify-between items-center text-[9px] font-black uppercase tracking-widest">
                                <span class="text-slate-400">Skor Akhir:</span>
                                <span
                                    class="text-base text-[#001f3f] tracking-tighter tabular-nums">{{ $t->nilai ?? '??' }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-2">
                            @if ($t->file_mahasiswa)
                                <a href="{{ asset('storage/' . $t->file_mahasiswa) }}" target="_blank"
                                    class="py-3 bg-zinc-50 text-slate-600 rounded-xl text-[9px] font-black uppercase tracking-widest flex items-center justify-center gap-2 border border-slate-200">
                                    <i class="fas fa-download"></i>
                                    Unduh
                                </a>
                            @endif
                            <button
                                onclick="openReviewModal('{{ $t->id }}', '{{ $t->nilai }}', '{{ $t->catatan_aslab }}', '{{ $t->status }}')"
                                class="col-span-1 py-3 bg-[#001f3f] text-white rounded-xl text-[9px] font-black uppercase tracking-widest flex items-center justify-center gap-2 shadow-lg shadow-[#001f3f]/10">
                                <i class="fas fa-marker"></i>
                                Review
                            </button>
                            <form action="{{ route('aslab.tugas.destroy', $t->id) }}" method="POST" class="col-span-1">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Hapus tugas ini?')"
                                    class="w-full py-3 bg-rose-50 text-rose-500 rounded-xl flex items-center justify-center border border-rose-100">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <p class="text-slate-300 font-black text-[10px] uppercase tracking-widest italic">Belum ada data
                            tugas</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Modal: Tambah Tugas -->
    <div id="modal-tugas"
        class="fixed inset-0 z-[60] hidden bg-slate-900/40 backdrop-blur-md flex items-center justify-center p-4 transition-all duration-300">
        <div
            class="bg-white rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl border border-slate-200 animate-in fade-in zoom-in duration-200">
            <div class="p-8 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-lg bg-[#001f3f] flex items-center justify-center text-white">
                        <i class="fas fa-plus text-xs"></i>
                    </div>
                    <h3 class="font-black text-slate-900 uppercase tracking-tight">Beri Tugas Baru</h3>
                </div>
                <button onclick="document.getElementById('modal-tugas').classList.add('hidden')"
                    class="h-8 w-8 flex items-center justify-center rounded-xl hover:bg-slate-100 text-slate-400 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('aslab.tugas.store') }}" method="POST" enctype="multipart/form-data"
                class="p-8 space-y-5">
                @csrf
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Pilih Mata
                        Praktikum</label>
                    <select name="praktikum_id" required
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-5 py-3.5 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-[#001f3f]/5 focus:border-[#001f3f] outline-none transition-all">
                        <option value="">-- Pilih Praktikum --</option>
                        @foreach ($praktikums as $p)
                            <option value="{{ $p->id }}">{{ $p->nama_praktikum }} ({{ $p->kode_praktikum }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Judul Tugas</label>
                    <input type="text" name="judul" required placeholder="Contoh: Modul 1 - Logic & Loops"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-5 py-3.5 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-[#001f3f]/5 focus:border-[#001f3f] outline-none transition-all">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Deadline</label>
                    <input type="date" name="due_date"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-5 py-3.5 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-[#001f3f]/5 focus:border-[#001f3f] outline-none transition-all">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Deskripsi
                        (Opsional)</label>
                    <textarea name="deskripsi" rows="3" placeholder="Instruksi pengerjaan tugas..."
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-5 py-3.5 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-[#001f3f]/5 focus:border-[#001f3f] outline-none transition-all"></textarea>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Upload Soal
                        (PDF/Doc/Zip)</label>
                    <div class="relative">
                        <input type="file" name="file_tugas"
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-5 py-2.5 text-xs text-slate-500 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-[#001f3f] file:text-white hover:file:bg-[#002d5a]">
                    </div>
                </div>
                <div class="pt-4 flex gap-4">
                    <button type="button" onclick="document.getElementById('modal-tugas').classList.add('hidden')"
                        class="flex-1 px-6 py-4 border border-slate-200 text-slate-600 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all">Batal</button>
                    <button type="submit"
                        class="flex-1 px-6 py-4 bg-[#001f3f] text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-[#002d5a] transition-all shadow-xl shadow-[#001f3f]/10 active:scale-[0.98]">Kirim
                        Tugas</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Review Tugas -->
    <div id="modal-review"
        class="fixed inset-0 z-[60] hidden bg-slate-900/40 backdrop-blur-md flex items-center justify-center p-4 transition-all duration-300">
        <div
            class="bg-white rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl border border-slate-200 animate-in fade-in zoom-in duration-200">
            <div class="p-8 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-lg bg-emerald-500 flex items-center justify-center text-white">
                        <i class="fas fa-marker text-xs"></i>
                    </div>
                    <h3 class="font-black text-slate-900 uppercase tracking-tight">Review & Nilai</h3>
                </div>
                <button onclick="document.getElementById('modal-review').classList.add('hidden')"
                    class="h-8 w-8 flex items-center justify-center rounded-xl hover:bg-slate-100 text-slate-400 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="form-review" method="POST" class="p-8 space-y-6">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5 col-span-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Skor
                            (0-100)</label>
                        <input type="number" id="review-nilai" name="nilai" min="0" max="100"
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-5 py-3.5 text-sm font-bold text-slate-900 focus:ring-4 focus:ring-emerald-500/5 focus:border-emerald-500 outline-none transition-all tabular-nums">
                    </div>
                    <div class="space-y-1.5 col-span-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Status</label>
                        <select id="review-status" name="status" required
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-5 py-3.5 text-sm font-bold text-slate-900 focus:ring-4 focus:ring-emerald-500/5 focus:border-emerald-500 outline-none transition-all uppercase tracking-widest">
                            <option value="pending">Pending</option>
                            <option value="submitted">Submitted</option>
                            <option value="reviewed">Reviewed</option>
                        </select>
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Catatan
                        Aslab</label>
                    <textarea id="review-catatan" name="catatan_aslab" rows="3"
                        placeholder="Berikan feedback atau instruksi revisi..."
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-5 py-3.5 text-sm font-bold text-slate-900 focus:ring-4 focus:ring-emerald-500/5 focus:border-emerald-500 outline-none transition-all"></textarea>
                </div>
                <div class="pt-4">
                    <button type="submit"
                        class="w-full px-6 py-4 bg-emerald-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-500/20 active:scale-[0.98]">Simpan
                        Review</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openReviewModal(id, nilai, catatan, status) {
            const form = document.getElementById('form-review');
            form.action = `/aslab/tugas/${id}`;

            document.getElementById('review-nilai').value = nilai !== 'null' ? nilai : '';
            document.getElementById('review-catatan').value = catatan !== 'null' ? catatan : '';
            document.getElementById('review-status').value = status;

            document.getElementById('modal-review').classList.remove('hidden');
        }

        // Close modal on click outside
        window.onclick = function(event) {
            const mTugas = document.getElementById('modal-tugas');
            const mReview = document.getElementById('modal-review');
            if (event.target == mTugas) mTugas.classList.add('hidden');
            if (event.target == mReview) mReview.classList.add('hidden');
        }
    </script>
@endsection
