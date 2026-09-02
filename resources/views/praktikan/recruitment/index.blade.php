@extends('layouts.admin')

@section('title', 'Rekrutmen Asisten Laboratorium')

@section('content')
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Rekrutmen Asisten Laboratorium</h1>
                <p class="text-slate-500 mt-1 text-sm sm:text-base">Bergabunglah menjadi bagian dari tim asisten Laboratorium RPL.</p>
            </div>
            <div class="flex items-center gap-3">
                <span
                    class="inline-flex items-center px-3 py-1.5 rounded-full bg-blue-50 text-[#001f3f] text-xs font-bold border border-blue-100 shadow-sm">
                    <i class="fas fa-user-plus mr-2 text-blue-600"></i>
                    Open Recruitment
                </span>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div
                class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5 transition-all hover:shadow-md hover:border-blue-200 cursor-default">
                <div class="h-14 w-14 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-bullhorn text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Lowongan Ditawarkan</h3>
                    <p class="text-xl font-bold text-slate-900 mb-1">
                        {{ $allPeriods->where('is_active', true)->where('end_date', '>=', now())->count() }} <span class="text-sm font-medium text-slate-500 normal-case">Periode Aktif</span>
                    </p>
                    <p class="text-[10px] text-slate-500 font-medium">Kesempatan terbuka untuk mahasiwa aktif</p>
                </div>
            </div>

            <div
                class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5 transition-all hover:shadow-md hover:border-purple-200 cursor-default">
                <div class="h-14 w-14 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-history text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Lamaran Dikirim</h3>
                    <p class="text-xl font-bold text-slate-900 mb-1">
                        {{ $myApplications->count() }} <span class="text-sm font-medium text-slate-500 normal-case">Pendaftaran</span>
                    </p>
                    <p class="text-[10px] text-slate-500 font-medium">Riwayat pengajuan berkas aslab Anda</p>
                </div>
            </div>
        </div>

        <!-- All Oprec Periods -->
        <div class="space-y-4">
            <div class="flex items-center gap-3 mb-2">
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">Lowongan Rekrutmen</h2>
                <span class="px-2 py-1 bg-slate-100 text-slate-600 text-[10px] font-bold rounded-md">{{ $allPeriods->count() }} Total Periode</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @forelse($allPeriods as $period)
                    @php
                        $isOpen = $period->is_active && $period->end_date >= now();
                        $alreadyApplied = $myApplications
                            ->where('recruitment_period_id', $period->id)
                            ->isNotEmpty();
                    @endphp
                    <div
                        class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col md:flex-row transition-all hover:shadow-md hover:border-blue-200 group {{ !$isOpen ? 'opacity-85' : '' }}">
                        <div
                            class="md:w-1/3 {{ $isOpen ? 'bg-gradient-to-br from-[#001f3f] to-[#003366]' : 'bg-gradient-to-br from-slate-700 to-slate-800' }} p-6 flex flex-col items-center justify-center text-white text-center relative overflow-hidden">
                            <div class="absolute inset-0 opacity-10">
                                <i class="fas fa-users text-[120px] -rotate-12 transform translate-x-4"></i>
                            </div>
                            <div class="relative z-10">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center mb-3 mx-auto border border-white/20 shadow-sm">
                                    <i class="fas {{ $isOpen ? 'fa-user-plus' : 'fa-clock-rotate-left' }} text-xl text-white"></i>
                                </div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-200 mb-1">
                                    {{ $isOpen ? 'Batas Pendaftaran' : 'Berakhir Pada' }}
                                </p>
                                <p class="text-base font-bold text-white">{{ $period->end_date->format('d M Y') }}</p>
                                @if(!$isOpen)
                                    <span class="inline-block mt-2 px-2 py-0.5 rounded bg-rose-500/30 text-rose-200 border border-rose-400/30 text-[9px] font-bold uppercase tracking-wider">Tutup</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex-grow p-6 flex flex-col justify-between">
                            <div>
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <h3 class="text-lg font-bold text-slate-900 group-hover:text-blue-600 transition-colors">
                                        {{ $period->title }}
                                    </h3>
                                </div>
                                
                                <div class="relative mb-4">
                                    <div class="description-container relative overflow-hidden transition-all duration-500 max-h-20" id="description-{{ $period->id }}">
                                        <div class="trix-content prose prose-sm max-w-none text-slate-500 text-xs leading-relaxed">
                                            {!! $period->description ?? 'Mari kembangkan skill Anda dengan menjadi asisten laboratorium.' !!}
                                        </div>
                                        <div class="description-fade absolute bottom-0 left-0 w-full h-8 bg-gradient-to-t from-white to-transparent pointer-events-none transition-opacity duration-300" id="fade-{{ $period->id }}"></div>
                                    </div>
                                    <button onclick="toggleDescription('{{ $period->id }}')" 
                                        class="description-toggle-btn hidden text-blue-600 text-[11px] font-bold mt-1 hover:text-blue-800 transition-colors flex items-center gap-1 group" 
                                        id="btn-{{ $period->id }}">
                                        <span class="btn-text">Baca Selengkapnya</span>
                                        <i class="fas fa-chevron-down text-[10px] group-[.active]:rotate-180 transition-transform"></i>
                                    </button>
                                </div>

                                <div class="grid grid-cols-2 gap-3 mb-6">
                                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Min. IPK</p>
                                        <p class="text-sm font-bold text-slate-800">{{ $period->min_ipk }}</p>
                                    </div>
                                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Min. Semester</p>
                                        <p class="text-sm font-bold text-slate-800">Semester {{ $period->min_semester }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-3 pt-2 border-t border-slate-100">
                                <button 
                                    data-id="{{ $period->id }}"
                                    data-title="{{ $period->title }}"
                                    data-description="{{ $period->description }}"
                                    data-ipk="{{ $period->min_ipk }}"
                                    data-semester="{{ $period->min_semester }}"
                                    data-start="{{ $period->start_date->format('d M Y') }}"
                                    data-end="{{ $period->end_date->format('d M Y') }}"
                                    data-wa="{{ $period->whatsapp_link }}"
                                    onclick="openDetailModal(this)"
                                    class="flex-1 py-2.5 bg-slate-100 text-slate-700 rounded-xl font-bold text-xs hover:bg-slate-200 transition-all flex items-center justify-center gap-2">
                                    <i class="fas fa-info-circle text-xs"></i>
                                    Detail Info
                                </button>

                                @if ($alreadyApplied)
                                    <div
                                        class="flex-1 py-2.5 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-xl font-bold text-xs flex items-center justify-center gap-2">
                                        <i class="fas fa-check-circle"></i>
                                        Sudah Mendaftar
                                    </div>
                                @elseif(!$isOpen)
                                    <button disabled
                                        class="flex-1 py-2.5 bg-slate-100 text-slate-400 border border-slate-200 rounded-xl font-bold text-xs cursor-not-allowed flex items-center justify-center gap-2">
                                        <i class="fas fa-lock text-xs"></i>
                                        Pendaftaran Ditutup
                                    </button>
                                @else
                                    <button 
                                        data-id="{{ $period->id }}"
                                        data-title="{{ $period->title }}"
                                        onclick="openApplyModal(this)"
                                        class="flex-1 py-2.5 bg-[#001f3f] text-white rounded-xl font-bold text-xs hover:bg-slate-800 transition-all shadow-md flex items-center justify-center gap-2">
                                        Daftar Sekarang
                                        <i class="fas fa-arrow-right text-xs"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-full py-12 text-center bg-white rounded-2xl border border-dashed border-slate-200 shadow-sm">
                        <div
                            class="w-14 h-14 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300 text-xl border border-slate-100">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3 class="text-slate-900 font-bold text-sm">Belum Ada Lowongan Rekrutmen</h3>
                        <p class="text-slate-500 text-xs mt-1">Saat ini belum ada periode rekrutmen asisten laboratorium yang dibuka.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- My Applications Status -->
        <div class="space-y-4">
            <div class="flex items-center gap-3 mb-2">
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">Riwayat Lamaran Saya</h2>
                <span class="px-2 py-1 bg-slate-100 text-slate-600 text-[10px] font-bold rounded-md">{{ $myApplications->count() }} Total</span>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100 text-slate-500">
                                <th class="px-6 py-4 font-bold text-[10px] uppercase tracking-wider">Periode Rekrutmen</th>
                                <th class="px-6 py-4 font-bold text-[10px] uppercase tracking-wider">Tanggal Daftar</th>
                                <th class="px-6 py-4 font-bold text-[10px] uppercase tracking-wider text-center">Grup WA</th>
                                <th class="px-6 py-4 font-bold text-[10px] uppercase tracking-wider">Jadwal Tes</th>
                                <th class="px-6 py-4 font-bold text-[10px] uppercase tracking-wider text-center">Status</th>
                                <th class="px-6 py-4 font-bold text-[10px] uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-900">
                            @forelse($myApplications as $app)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-slate-900 leading-tight">{{ $app->period->title }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-500 font-medium">
                                        {{ $app->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($app->period->whatsapp_link)
                                            <a href="{{ $app->period->whatsapp_link }}" target="_blank" class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 hover:bg-emerald-500 hover:text-white transition-all shadow-sm" title="Gabung Grup WhatsApp">
                                                <i class="fab fa-whatsapp text-base"></i>
                                            </a>
                                        @else
                                            <span class="text-[10px] text-slate-400 italic">Belum tersedia</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($app->schedules->isNotEmpty())
                                            <div class="space-y-2">
                                                @foreach($app->schedules as $schedule)
                                                    <div class="p-2.5 rounded-xl bg-blue-50/70 border border-blue-100 flex items-center justify-between group">
                                                        <div class="flex items-center gap-2.5">
                                                            <div class="h-7 w-7 rounded-lg bg-[#001f3f] text-white flex flex-col items-center justify-center">
                                                                <span class="text-[6px] font-black uppercase leading-none">{{ $schedule->date->format('M') }}</span>
                                                                <span class="text-[10px] font-black leading-none">{{ $schedule->date->format('d') }}</span>
                                                            </div>
                                                            <div>
                                                                <p class="text-[10px] font-bold text-blue-950 leading-tight">{{ $schedule->name }}</p>
                                                                <p class="text-[8px] text-blue-700 mt-0.5">
                                                                    <i class="far fa-clock mr-1"></i> {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                                                                    <span class="mx-1">|</span>
                                                                    <i class="fas fa-map-marker-alt mr-1 text-rose-500"></i> {{ $schedule->location }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                        @if($schedule->notes)
                                                            <button onclick="showScheduleNotes(this)"
                                                                data-name="{{ $schedule->name }}"
                                                                data-notes="{{ $schedule->notes }}"
                                                                class="h-6 w-6 rounded-md bg-white text-blue-600 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity border border-blue-100 shadow-sm" title="Lihat Catatan">
                                                                <i class="fas fa-info text-[8px]"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-[10px] text-slate-400 italic">Belum ada jadwal tes.</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $statusClasses = [
                                                'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                'shortlisted' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                                                'accepted' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            ];
                                            $statusLabels = [
                                                'pending' => 'Menunggu Verifikasi',
                                                'shortlisted' => 'Shortlist (Lolos)',
                                                'rejected' => 'Tidak Lolos',
                                                'accepted' => 'Diterima Resmi',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border {{ $statusClasses[$app->status] }}">
                                            {{ $statusLabels[$app->status] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('praktikan.recruitment.show', $app->id) }}" class="inline-flex items-center justify-center h-8 px-4 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold whitespace-nowrap hover:bg-[#001f3f] hover:text-white transition-all shadow-sm">
                                            Detail Progress
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-slate-400 text-xs italic">Anda belum memiliki riwayat pendaftaran asisten laboratorium.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div id="detailModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/40" onclick="closeDetailModal()"></div>
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl relative z-10 overflow-hidden max-h-[90vh] flex flex-col">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between shrink-0">
                <div>
                    <h3 class="font-bold text-slate-900">Informasi Detail Rekrutmen</h3>
                    <p class="text-[10px] text-blue-600 font-bold uppercase tracking-widest mt-0.5">Persyaratan & Deskripsi</p>
                </div>
                <button onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-900 transition-colors"><i class="fas fa-times"></i></button>
            </div>
            <div class="p-8 overflow-y-auto custom-scrollbar space-y-8">
                <div>
                    <h4 id="detailTitle" class="text-2xl font-black text-slate-900 leading-tight mb-4"></h4>
                    <div class="flex flex-wrap gap-3">
                        <div class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-[10px] font-bold uppercase tracking-wider border border-blue-100 flex items-center gap-2">
                            <i class="fas fa-calendar-alt"></i>
                            <span id="detailDates"></span>
                        </div>
                        <div class="px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-lg text-[10px] font-bold uppercase tracking-wider border border-emerald-100 flex items-center gap-2">
                            <i class="fas fa-graduation-cap"></i>
                            Min. IPK: <span id="detailIPK"></span>
                        </div>
                        <div class="px-3 py-1.5 bg-amber-50 text-amber-700 rounded-lg text-[10px] font-bold uppercase tracking-wider border border-amber-100 flex items-center gap-2">
                            <i class="fas fa-layer-group"></i>
                            Min. Semester: <span id="detailSemester"></span>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <h5 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400 flex items-center gap-2">
                        <span class="w-8 h-px bg-slate-200"></span>
                        Deskripsi Lengkap
                    </h5>
                    <div id="detailDescription" class="trix-content prose prose-sm max-w-none text-slate-600 leading-relaxed">
                    </div>
                </div>

                <div id="waLinkContainer" class="hidden pt-6 border-t border-slate-100">
                    <h5 class="text-xs font-black uppercase tracking-[0.2em] text-emerald-500 flex items-center gap-2 mb-4">
                        <span class="w-8 h-px bg-emerald-200"></span>
                        Grup Koordinasi
                    </h5>
                    <div class="p-5 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-between group">
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 rounded-xl bg-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-900/20">
                                <i class="fab fa-whatsapp text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-emerald-900">Gabung Grup Resmi</h4>
                                <p class="text-[11px] text-emerald-600 font-medium">Klik tombol untuk masuk ke grup WhatsApp.</p>
                            </div>
                        </div>
                        <a id="detailWA" href="#" target="_blank" class="px-5 py-2.5 bg-emerald-600 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-md">
                            Join Now
                        </a>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-slate-100 bg-slate-50/50 flex justify-end shrink-0">
                <button onclick="closeDetailModal()" class="px-6 py-2.5 bg-slate-200 text-slate-700 rounded-xl font-bold text-sm hover:bg-slate-300 transition-all">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Apply Modal -->
    <div id="applyModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/40" onclick="closeApplyModal()"></div>
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-xl relative z-10 overflow-hidden max-h-[90vh] flex flex-col">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between shrink-0">
                <div>
                    <h3 class="font-bold text-slate-900">Formulir Pendaftaran</h3>
                    <p id="modalPeriodTitle" class="text-[10px] text-blue-600 font-bold uppercase tracking-widest mt-0.5">
                    </p>
                </div>
                <button onclick="closeApplyModal()" class="text-slate-400 hover:text-slate-900 transition-colors"><i
                        class="fas fa-times"></i></button>
            </div>
            <form id="applyForm" action="{{ route('praktikan.recruitment.store') }}" method="POST" enctype="multipart/form-data"
                class="flex flex-col overflow-hidden"
                onsubmit="event.preventDefault(); Swal.fire({
                    title: 'Kirim Pendaftaran?',
                    text: 'Pastikan semua data (IPK & Berkas) sudah benar. Anda tidak dapat mengubah data setelah dikirim.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#1a4fa0',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Kirim Sekarang!',
                    cancelButtonText: 'Cek Kembali'
                }).then((result) => { if (result.isConfirmed) { this.submit(); } })">
                @csrf
                <input type="hidden" name="recruitment_period_id" id="modalPeriodId">

                <div class="p-8 space-y-6 overflow-y-auto custom-scrollbar">
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
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700">Riwayat Studi (Transkrip PDF)</label>
                            <input type="file" name="transcript" accept=".pdf"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-4 focus:ring-blue-100 transition-all outline-none"
                                required>
                            <p class="text-[10px] text-slate-400">Keseluruhan nilai, format PDF.</p>
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
                        <label class="text-sm font-bold text-slate-700">IPK Total Saat Ini</label>
                        <div class="relative group">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-600 transition-colors">
                                <i class="fas fa-graduation-cap text-xs"></i>
                            </span>
                            <input type="number" step="0.01" name="ipk" 
                                class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:border-blue-600 transition-all outline-none text-sm"
                                placeholder="Contoh: 3.50" required>
                        </div>
                        <div class="p-3 rounded-xl bg-blue-50 border border-blue-100 space-y-1">
                            <p class="text-[10px] text-blue-700 font-medium leading-relaxed">
                                <i class="fas fa-info-circle mr-1"></i>
                                IPK total bisa di cek di <a href="https://sim.itats.ac.id/krs/nilai/riwayat" target="_blank" class="font-bold underline">sim.itats.ac.id/krs/nilai/riwayat</a>
                            </p>
                            <p class="text-[10px] text-blue-700 font-medium leading-relaxed">
                                <i class="fas fa-print mr-1"></i>
                                Di cetak dan di upload sebagai bukti kesesuaian IPK.
                            </p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700">Surat Motivasi / Alasan Mendaftar</label>
                        <textarea name="motivation_letter" rows="4"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-600 transition-all outline-none text-sm"
                            placeholder="Ceritakan mengapa Anda tertarik menjadi asisten lab..."></textarea>
                    </div>
                </div>

                <div class="p-6 border-t border-slate-100 bg-slate-50/50 shrink-0">
                    <button type="submit"
                        class="w-full py-4 bg-[#001f3f] text-white rounded-2xl font-bold hover:bg-blue-900 transition-all shadow-xl shadow-blue-900/20">
                        Kirim Pendaftaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('styles')
        <style>
            .custom-scrollbar {
                scrollbar-width: thin;
                scrollbar-color: #cbd5e1 #f1f5f9;
                overscroll-behavior: contain;
                -webkit-overflow-scrolling: touch;
                transform: translateZ(0);
                backface-visibility: hidden;
                will-change: scroll-position;
            }
            .custom-scrollbar::-webkit-scrollbar {
                width: 6px;
            }
            .custom-scrollbar::-webkit-scrollbar-track {
                background: #f1f5f9;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 10px;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function showScheduleNotes(button) {
                const name = button.getAttribute('data-name');
                const notes = button.getAttribute('data-notes');
                Swal.fire({
                    title: name,
                    text: notes,
                    icon: 'info',
                    confirmButtonText: 'Tutup',
                    customClass: {
                        confirmButton: 'bg-blue-600 rounded-xl px-6 py-2 text-white font-bold'
                    }
                });
            }

            function openApplyModal(el) {
                const data = el.dataset;
                document.getElementById('modalPeriodId').value = data.id;
                document.getElementById('modalPeriodTitle').innerText = data.title;
                document.getElementById('applyModal').classList.remove('hidden');
            }

            function closeApplyModal() {
                document.getElementById('applyModal').classList.add('hidden');
            }

            function openDetailModal(el) {
                const data = el.dataset;
                document.getElementById('detailTitle').innerText = data.title;
                document.getElementById('detailDescription').innerHTML = data.description || 'Tidak ada deskripsi tambahan.';
                document.getElementById('detailIPK').innerText = data.ipk;
                document.getElementById('detailSemester').innerText = data.semester;
                document.getElementById('detailDates').innerText = data.start + ' - ' + data.end;
                
                const waContainer = document.getElementById('waLinkContainer');
                const waLink = document.getElementById('detailWA');
                
                if (data.wa && data.wa !== '') {
                    waContainer.classList.remove('hidden');
                    waLink.href = data.wa;
                } else {
                    waContainer.classList.add('hidden');
                }

                document.getElementById('detailModal').classList.remove('hidden');
            }

            function closeDetailModal() {
                document.getElementById('detailModal').classList.add('hidden');
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
                    if (container.scrollHeight <= container.offsetHeight) {
                        btn.style.display = 'none';
                        fade.style.display = 'none';
                    }
                });
            });

            @if(session('success'))
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: 'success',
                    title: 'Pendaftaran Berhasil!',
                    text: "{{ session('success') }}",
                    customClass: {
                        popup: 'rounded-2xl shadow-2xl border border-emerald-50 bg-white p-4',
                        title: 'text-sm font-black text-slate-800',
                        htmlContainer: 'text-[11px] text-slate-500 mt-1 font-medium',
                    }
                });
            @endif
        </script>
    @endpush
@endsection
