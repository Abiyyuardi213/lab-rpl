@extends('layouts.admin')

@section('title', 'Tugas Asistensi')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Tugas Asistensi</h1>
                <p class="text-sm text-slate-500">Berikan dan pantau tugas untuk mahasiswa bimbingan Anda.</p>
            </div>
            <button onclick="document.getElementById('modal-tugas').classList.remove('hidden')"
                class="px-5 py-2.5 bg-[#001f3f] text-white text-sm font-bold rounded-xl hover:bg-[#002d5a] transition-all shadow-lg flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Tambah Tugas
            </button>
        </div>

        <!-- Task List Container -->
        <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden min-h-[400px]">
            <div class="p-6 md:p-8 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-base font-black text-slate-900 uppercase tracking-tight">Data Tugas Mahasiswa</h3>
                <span
                    class="px-3 py-1 bg-white border border-slate-200 rounded-full text-[10px] font-black text-slate-400 uppercase tracking-widest">Total:
                    {{ $tugas->count() }}</span>
            </div>

            <!-- Desktop View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50/50 border-b border-slate-200">
                        <tr>
                            <th class="px-8 py-5 font-black text-slate-400 uppercase tracking-widest text-[10px]">Mahasiswa
                            </th>
                            <th class="px-8 py-5 font-black text-slate-400 uppercase tracking-widest text-[10px]">Judul
                                Tugas</th>
                            <th class="px-8 py-5 font-black text-slate-400 uppercase tracking-widest text-[10px]">Deadline
                            </th>
                            <th class="px-8 py-5 font-black text-slate-400 uppercase tracking-widest text-[10px]">Status
                            </th>
                            <th
                                class="px-8 py-5 font-black text-slate-400 uppercase tracking-widest text-[10px] text-center">
                                Nilai</th>
                            <th
                                class="px-8 py-5 font-black text-slate-400 uppercase tracking-widest text-[10px] text-right">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($tugas as $t)
                            <tr class="hover:bg-slate-50/80 transition-all group">
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span
                                            class="font-black text-slate-900 uppercase tracking-tight">{{ $t->pendaftaran->user->name }}</span>
                                        <span
                                            class="text-[10px] text-slate-400 font-bold font-mono tracking-widest truncate max-w-[200px]">{{ $t->pendaftaran->praktikum->nama_praktikum }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 font-bold text-slate-700 uppercase text-xs">{{ $t->judul }}</td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-2 text-slate-500 font-medium whitespace-nowrap">
                                        <i class="far fa-calendar-alt text-slate-300"></i>
                                        {{ $t->due_date ? $t->due_date->format('d M Y') : '-' }}
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    @php
                                        $statusBase =
                                            'inline-flex items-center px-4 py-1.5 rounded-full text-[9px] font-black border uppercase tracking-widest ';
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
                                        class="text-lg font-black {{ $t->nilai >= 80 ? 'text-emerald-500' : ($t->nilai ? 'text-amber-500' : 'text-slate-200') }}">
                                        {{ $t->nilai ?? '??' }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if ($t->file_mahasiswa)
                                            <a href="{{ asset('storage/' . $t->file_mahasiswa) }}" target="_blank"
                                                class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all shadow-sm">
                                                <i class="fas fa-download text-[10px]"></i>
                                            </a>
                                        @endif
                                        <button
                                            onclick="openReviewModal('{{ $t->id }}', '{{ $t->nilai }}', '{{ $t->catatan_aslab }}', '{{ $t->status }}')"
                                            class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all shadow-sm">
                                            <i class="fas fa-marker text-[10px]"></i>
                                        </button>
                                        <form action="{{ route('aslab.tugas.destroy', $t->id) }}" method="POST"
                                            class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" onclick="return confirm('Hapus tugas ini?')"
                                                class="w-8 h-8 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all shadow-sm">
                                                <i class="fas fa-trash-alt text-[10px]"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-8 py-20 text-center text-slate-400 italic font-bold">
                                    Belum ada tugas yang diberikan kepada mahasiswa bimbingan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile View -->
            <div class="md:hidden divide-y divide-slate-100">
                @forelse($tugas as $t)
                    <div class="p-6 space-y-4 hover:bg-slate-50 transition-all">
                        <div class="flex justify-between items-start">
                            <div class="flex flex-col gap-1 pr-4">
                                <span
                                    class="font-black text-slate-900 uppercase text-sm tracking-tight leading-tight">{{ $t->pendaftaran->user->name }}</span>
                                <span
                                    class="text-[9px] text-slate-400 font-bold uppercase tracking-widest line-clamp-1 italic">{{ $t->pendaftaran->praktikum->nama_praktikum }}</span>
                            </div>
                            @php
                                $statusMcl = [
                                    'pending' => 'bg-slate-100 text-slate-400',
                                    'submitted' => 'bg-amber-100 text-amber-600',
                                    'reviewed' => 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/20',
                                ];
                            @endphp
                            <span
                                class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest whitespace-nowrap {{ $statusMcl[$t->status] ?? $statusMcl['pending'] }}">
                                {{ $t->status }}
                            </span>
                        </div>

                        <div class="bg-slate-50/80 p-5 rounded-2xl border border-slate-100 space-y-4">
                            <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-widest">
                                <span class="text-slate-400">Judul Tugas:</span>
                                <span class="text-slate-900">{{ $t->judul }}</span>
                            </div>
                            <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-widest">
                                <span class="text-slate-400">Deadline:</span>
                                <span
                                    class="text-slate-700 italic">{{ $t->due_date ? $t->due_date->format('d M Y') : '-' }}</span>
                            </div>
                            <div
                                class="pt-2 border-t border-slate-200/50 flex justify-between items-center text-[10px] font-black uppercase tracking-widest">
                                <span class="text-slate-400">Nilai Akhir:</span>
                                <span class="text-lg text-[#001f3f]">{{ $t->nilai ?? '??' }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-2">
                            @if ($t->file_mahasiswa)
                                <a href="{{ asset('storage/' . $t->file_mahasiswa) }}" target="_blank"
                                    class="flex-1 py-3 bg-indigo-50 text-indigo-600 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center justify-center gap-2 border border-indigo-100">
                                    <i class="fas fa-download"></i>
                                    Unduh
                                </a>
                            @endif
                            <button
                                onclick="openReviewModal('{{ $t->id }}', '{{ $t->nilai }}', '{{ $t->catatan_aslab }}', '{{ $t->status }}')"
                                class="flex-[2] py-3 bg-[#001f3f] text-white rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center justify-center gap-2 shadow-lg shadow-[#001f3f]/20">
                                <i class="fas fa-marker"></i>
                                Review & Nilai
                            </button>
                            <form action="{{ route('aslab.tugas.destroy', $t->id) }}" method="POST" class="flex-none">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Hapus tugas ini?')"
                                    class="w-12 py-3 bg-rose-50 text-rose-500 rounded-xl flex items-center justify-center border border-rose-100">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-slate-400 italic font-bold text-xs uppercase tracking-widest">
                        Belum ada data tugas yang diinputkan
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Modal: Tambah Tugas -->
    <div id="modal-tugas"
        class="fixed inset-0 z-[60] hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-900">Beri Tugas Baru</h3>
                <button onclick="document.getElementById('modal-tugas').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('aslab.tugas.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Pilih Mahasiswa</label>
                    <select name="pendaftaran_id" required
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] transition-all">
                        <option value="">-- Pilih Mahasiswa --</option>
                        @foreach ($students as $s)
                            <option value="{{ $s->id }}">{{ $s->user->name }} -
                                {{ $s->praktikum->nama_praktikum }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Judul Tugas</label>
                    <input type="text" name="judul" required placeholder="Judul Tugas (misal: Modul 1)"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] transition-all">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Deadline</label>
                    <input type="date" name="due_date"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] transition-all">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" placeholder="Instruksi pengerjaan..."
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] transition-all"></textarea>
                </div>
                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="document.getElementById('modal-tugas').classList.add('hidden')"
                        class="flex-1 px-4 py-3 border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-50 transition-all">Batal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-3 bg-[#001f3f] text-white rounded-xl text-xs font-bold hover:bg-[#002d5a] transition-all shadow-lg shadow-[#001f3f]/20">Kirim
                        Tugas</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Review Tugas -->
    <div id="modal-review"
        class="fixed inset-0 z-[60] hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl w-full max-w-lg overflow-hidden shadow-2xl">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-900">Review & Nilai Tugas</h3>
                <button onclick="document.getElementById('modal-review').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="form-review" method="POST" class="p-6 space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1 col-span-1">
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Nilai (0-100)</label>
                        <input type="number" id="review-nilai" name="nilai" min="0" max="100"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] transition-all">
                    </div>
                    <div class="space-y-1 col-span-1">
                        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Status</label>
                        <select id="review-status" name="status" required
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] transition-all">
                            <option value="pending">Pending</option>
                            <option value="submitted">Submitted</option>
                            <option value="reviewed">Reviewed (Done)</option>
                        </select>
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Catatan Aslab</label>
                    <textarea id="review-catatan" name="catatan_aslab" rows="3" placeholder="Berikan feedback atau revisi..."
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm focus:ring-2 focus:ring-[#001f3f]/10 focus:border-[#001f3f] transition-all"></textarea>
                </div>
                <div class="pt-4 flex gap-3">
                    <button type="submit"
                        class="w-full px-4 py-3 bg-emerald-600 text-white rounded-xl text-xs font-bold hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-600/20">Simpan
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
    </script>
@endsection
