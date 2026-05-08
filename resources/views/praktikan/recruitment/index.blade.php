@extends('layouts.admin')

@section('title', 'Rekrutmen Asisten Laboratorium')

@section('content')
    <div class="space-y-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Rekrutmen Aslab</h1>
                <p class="text-slate-500 mt-1 text-sm">Bergabunglah menjadi bagian dari tim asisten Laboratorium RPL.</p>
            </div>
        </div>

        <!-- Active Oprec Periods -->
        <div class="space-y-4">
            <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-bullhorn text-blue-600"></i>
                Lowongan Tersedia
            </h2>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @forelse($activePeriods as $period)
                    <div
                        class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col md:flex-row transition-all hover:shadow-md group">
                        <div
                            class="md:w-1/3 bg-gradient-to-br from-blue-600 to-indigo-700 p-8 flex flex-col items-center justify-center text-white text-center relative overflow-hidden">
                            <div class="absolute inset-0 opacity-10">
                                <i class="fas fa-users text-[120px] -rotate-12 transform translate-x-4"></i>
                            </div>
                            <div class="relative z-10">
                                <div
                                    class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center mb-4 mx-auto border border-white/20">
                                    <i class="fas fa-user-plus text-2xl"></i>
                                </div>
                                <p class="text-[10px] font-bold uppercase tracking-widest opacity-80 mb-1">Pendaftaran
                                    Hingga</p>
                                <p class="text-lg font-black">{{ $period->end_date->format('d M Y') }}</p>
                            </div>
                        </div>
                        <div class="flex-grow p-8">
                            <div class="flex items-start justify-between mb-2">
                                <h3 class="text-xl font-bold text-slate-900 group-hover:text-blue-600 transition-colors">
                                    {{ $period->title }}</h3>
                            </div>
                            <div class="relative">
                                <div class="description-container relative overflow-hidden transition-all duration-500 max-h-24 mb-2" id="description-{{ $period->id }}">
                                    <p class="text-slate-500 text-sm leading-relaxed whitespace-pre-line">
                                        {{ $period->description ?? 'Mari kembangkan skill Anda dengan menjadi asisten laboratorium.' }}
                                    </p>
                                    <div class="description-fade absolute bottom-0 left-0 w-full h-12 bg-gradient-to-t from-white to-transparent pointer-events-none transition-opacity duration-300" id="fade-{{ $period->id }}"></div>
                                </div>
                                <button onclick="toggleDescription('{{ $period->id }}')" 
                                    class="description-toggle-btn hidden text-blue-600 text-[11px] font-bold mb-6 hover:text-blue-800 transition-colors flex items-center gap-1 group" 
                                    id="btn-{{ $period->id }}">
                                    <span class="btn-text">Baca Selengkapnya</span>
                                    <i class="fas fa-chevron-down text-[10px] group-[.active]:rotate-180 transition-transform"></i>
                                </button>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-8">
                                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Min. IPK
                                    </p>
                                    <p class="text-sm font-bold text-slate-800">{{ $period->min_ipk }}</p>
                                </div>
                                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Min.
                                        Semester</p>
                                    <p class="text-sm font-bold text-slate-800">{{ $period->min_semester }}</p>
                                </div>
                            </div>

                            @php
                                $alreadyApplied = $myApplications
                                    ->where('recruitment_period_id', $period->id)
                                    ->isNotEmpty();
                            @endphp

                            @if ($alreadyApplied)
                                <div
                                    class="w-full py-3 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-xl font-bold text-sm flex items-center justify-center gap-2">
                                    <i class="fas fa-check-circle"></i>
                                    Sudah Mendaftar
                                </div>
                            @else
                                <button onclick="openApplyModal('{{ $period->id }}', '{{ $period->title }}')"
                                    class="w-full py-3 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/20 flex items-center justify-center gap-2">
                                    Daftar Sekarang
                                    <i class="fas fa-arrow-right text-xs"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-full py-16 text-center bg-white rounded-3xl border-2 border-dashed border-slate-200">
                        <div
                            class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300 text-2xl">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3 class="text-slate-900 font-bold">Belum Ada Rekrutmen</h3>
                        <p class="text-slate-500 text-sm mt-1">Saat ini belum ada periode rekrutmen aslab yang dibuka.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- My Applications Status -->
        <div class="space-y-4">
            <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-history text-slate-400"></i>
                Riwayat Lamaran Saya
            </h2>

            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-6 py-4 font-bold text-[10px] uppercase tracking-widest text-slate-500">Periode</th>
                                <th class="px-6 py-4 font-bold text-[10px] uppercase tracking-widest text-slate-500">Tgl Daftar</th>
                                <th class="px-6 py-4 font-bold text-[10px] uppercase tracking-widest text-slate-500 text-center">Status</th>
                                <th class="px-6 py-4 font-bold text-[10px] uppercase tracking-widest text-slate-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 text-zinc-900">
                            @forelse($myApplications as $app)
                                <tr class="hover:bg-zinc-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-zinc-900">{{ $app->period->title }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-zinc-500 font-medium">
                                        {{ $app->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $statusClasses = [
                                                'pending' => 'bg-amber-50 text-amber-600 border-amber-100',
                                                'shortlisted' => 'bg-blue-50 text-blue-600 border-blue-100',
                                                'rejected' => 'bg-rose-50 text-rose-600 border-rose-100',
                                                'accepted' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                            ];
                                            $statusLabels = [
                                                'pending' => 'Pending',
                                                'shortlisted' => 'Shortlist',
                                                'rejected' => 'Ditolak',
                                                'accepted' => 'Diterima',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $statusClasses[$app->status] }}">
                                            {{ $statusLabels[$app->status] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('praktikan.recruitment.show', $app->id) }}" class="inline-flex items-center justify-center h-8 px-4 rounded-xl bg-zinc-100 text-zinc-600 text-[10px] font-black uppercase tracking-widest hover:bg-[#1a4fa0] hover:text-white transition-all shadow-sm">
                                            Lihat Progress
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-zinc-400 text-[11px] italic">Anda belum memiliki riwayat pendaftaran.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Apply Modal -->
    <div id="applyModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeApplyModal()"></div>
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-xl relative z-10 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900">Formulir Pendaftaran</h3>
                    <p id="modalPeriodTitle" class="text-[10px] text-blue-600 font-bold uppercase tracking-widest mt-0.5">
                    </p>
                </div>
                <button onclick="closeApplyModal()" class="text-slate-400 hover:text-slate-900 transition-colors"><i
                        class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('praktikan.recruitment.store') }}" method="POST" enctype="multipart/form-data"
                class="p-8 space-y-6">
                @csrf
                <input type="hidden" name="recruitment_period_id" id="modalPeriodId">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700">Unggah CV (PDF)</label>
                        <input type="file" name="cv" accept=".pdf"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-4 focus:ring-blue-100 transition-all outline-none"
                            required>
                        <p class="text-[10px] text-slate-400">Maksimal 2MB, format PDF.</p>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700">Unggah KHS Terakhir (PDF)</label>
                        <input type="file" name="khs" accept=".pdf"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-4 focus:ring-blue-100 transition-all outline-none"
                            required>
                        <p class="text-[10px] text-slate-400">Maksimal 2MB, format PDF.</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700">Link Portofolio (Opsional)</label>
                    <div class="relative group">
                        <span
                            class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-600 transition-colors">
                            <i class="fas fa-link text-xs"></i>
                        </span>
                        <input type="url" name="portfolio_url"
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:border-blue-600 transition-all outline-none text-sm"
                            placeholder="https://github.com/username atau LinkedIn">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700">Surat Motivasi / Alasan Mendaftar</label>
                    <textarea name="motivation_letter" rows="4"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-600 transition-all outline-none text-sm"
                        placeholder="Ceritakan mengapa Anda tertarik menjadi asisten lab..."></textarea>
                </div>

                <button type="submit"
                    class="w-full py-4 bg-[#001f3f] text-white rounded-2xl font-bold hover:bg-blue-900 transition-all shadow-xl shadow-blue-900/20">
                    Kirim Pendaftaran
                </button>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function openApplyModal(id, title) {
                document.getElementById('modalPeriodId').value = id;
                document.getElementById('modalPeriodTitle').innerText = title;
                document.getElementById('applyModal').classList.remove('hidden');
            }

            function closeApplyModal() {
                document.getElementById('applyModal').classList.add('hidden');
            }

            function toggleDescription(id) {
                const container = document.getElementById('description-' + id);
                const fade = document.getElementById('fade-' + id);
                const btn = document.getElementById('btn-' + id);
                const btnText = btn.querySelector('.btn-text');
                const btnIcon = btn.querySelector('i');

                if (container.classList.contains('max-h-24')) {
                    // Expand
                    container.classList.remove('max-h-24');
                    container.classList.add('max-h-[1000px]');
                    fade.classList.add('opacity-0');
                    btnText.innerText = 'Sembunyikan';
                    btn.classList.add('active');
                } else {
                    // Collapse
                    container.classList.add('max-h-24');
                    container.classList.remove('max-h-[1000px]');
                    fade.classList.remove('opacity-0');
                    btnText.innerText = 'Baca Selengkapnya';
                    btn.classList.remove('active');
                }
            }

            // Initialize descriptions: hide button if text doesn't overflow
            document.addEventListener('DOMContentLoaded', function() {
                const containers = document.querySelectorAll('[id^="description-"]');
                containers.forEach(container => {
                    const id = container.id.split('-')[1];
                    const btn = document.getElementById('btn-' + id);
                    const fade = document.getElementById('fade-' + id);
                    
                    // Check if content overflows (scrollHeight > offsetHeight)
                    if (container.scrollHeight > container.offsetHeight) {
                        btn.classList.remove('hidden');
                    } else {
                        fade.classList.add('hidden');
                    }
                });
            });
        </script>
    @endpush
@endsection
