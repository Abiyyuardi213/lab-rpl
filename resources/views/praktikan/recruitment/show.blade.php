@extends('layouts.admin')

@section('title', 'Progress Rekrutmen')

@section('content')
    <div class="space-y-8">
        <!-- Header Section -->
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('praktikan.recruitment.index') }}"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-zinc-200 bg-white text-zinc-400 hover:text-zinc-900 transition-colors shadow-sm">
                    <i class="fas fa-chevron-left text-xs"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Detail Progress Lamaran</h1>
                    <p class="text-sm text-zinc-500 mt-1">Pantau perkembangan seleksi asisten laboratorium Anda.</p>
                </div>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('praktikan.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                <span>/</span>
                <a href="{{ route('praktikan.recruitment.index') }}" class="hover:text-zinc-900 transition-colors">Rekrutmen</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Progress</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Status Timeline -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-3xl border border-zinc-200 shadow-sm p-8 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8 opacity-[0.03] pointer-events-none">
                        <i class="fas fa-tasks text-9xl"></i>
                    </div>

                    <h2 class="text-sm font-black uppercase tracking-[0.2em] text-zinc-400 mb-8 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-[#1a4fa0]"></i>
                        Status Perjalanan Anda
                    </h2>

                    <div class="relative space-y-12">
                        <!-- Vertical Line -->
                        <div class="absolute left-4 top-2 bottom-2 w-0.5 bg-zinc-100"></div>

                        @php
                            $steps = [
                                ['id' => 'pending', 'title' => 'Pendaftaran Terkirim', 'desc' => 'Berkas Anda telah diterima oleh sistem dan sedang menunggu antrean review admin.'],
                                ['id' => 'shortlisted', 'title' => 'Lolos Administrasi', 'desc' => 'Selamat! Berkas Anda memenuhi kriteria awal dan masuk dalam daftar shortlist.'],
                                ['id' => 'accepted', 'title' => 'Diterima Sebagai Aslab', 'desc' => 'Selamat bergabung! Anda telah resmi terpilih sebagai bagian dari Asisten Laboratorium RPL.'],
                            ];

                            $currentStatus = $application->status;
                            $isRejected = $currentStatus === 'rejected';
                        @endphp

                        @foreach($steps as $index => $step)
                            @php
                                $isCompleted = false;
                                $isActive = false;
                                
                                if ($currentStatus === 'accepted') {
                                    $isCompleted = true;
                                } elseif ($currentStatus === 'shortlisted') {
                                    if ($step['id'] === 'pending') $isCompleted = true;
                                    if ($step['id'] === 'shortlisted') $isActive = true;
                                } elseif ($currentStatus === 'pending') {
                                    if ($step['id'] === 'pending') $isActive = true;
                                }

                                if ($isRejected && $step['id'] === 'pending') $isCompleted = true;
                            @endphp

                            <div class="relative pl-12 group">
                                <!-- Step Circle -->
                                <div @class([
                                    'absolute left-0 top-0 w-8 h-8 rounded-full border-4 flex items-center justify-center transition-all z-10',
                                    'bg-emerald-500 border-emerald-100 text-white' => $isCompleted,
                                    'bg-[#1a4fa0] border-blue-100 text-white animate-pulse' => $isActive,
                                    'bg-white border-zinc-100 text-zinc-300' => !$isCompleted && !$isActive,
                                ])>
                                    @if($isCompleted)
                                        <i class="fas fa-check text-[10px]"></i>
                                    @else
                                        <span class="text-[10px] font-black">{{ $index + 1 }}</span>
                                    @endif
                                </div>

                                <div>
                                    <h3 @class([
                                        'text-base font-bold transition-colors',
                                        'text-emerald-600' => $isCompleted,
                                        'text-[#1a4fa0]' => $isActive,
                                        'text-zinc-400' => !$isCompleted && !$isActive,
                                    ])>{{ $step['title'] }}</h3>
                                    <p class="text-sm text-zinc-500 mt-1 leading-relaxed">{{ $step['desc'] }}</p>
                                    
                                    @if($isActive)
                                        <div class="mt-3 inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-[#1a4fa0] text-[10px] font-black uppercase tracking-widest border border-blue-100">
                                            <span class="w-1 h-1 rounded-full bg-[#1a4fa0] animate-ping"></span>
                                            Sedang Berjalan
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        @if($isRejected)
                            <div class="relative pl-12 group">
                                <div class="absolute left-0 top-0 w-8 h-8 rounded-full bg-rose-500 border-4 border-rose-100 text-white flex items-center justify-center z-10">
                                    <i class="fas fa-times text-[10px]"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-rose-600">Pendaftaran Ditolak</h3>
                                    <p class="text-sm text-zinc-500 mt-1 leading-relaxed">Mohon maaf, pendaftaran Anda belum dapat dilanjutkan ke tahap berikutnya pada periode ini.</p>
                                    
                                    <div class="mt-4 p-4 rounded-2xl bg-rose-50 border border-rose-100">
                                        <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest mb-1">Catatan Admin:</p>
                                        <p class="text-sm text-rose-700 italic font-medium leading-relaxed">"{{ $application->admin_notes ?? 'Tidak ada catatan tambahan.' }}"</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                @if($currentStatus === 'accepted')
                    <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-3xl p-8 text-white shadow-xl shadow-emerald-500/20 relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-8 opacity-10 pointer-events-none rotate-12">
                            <i class="fas fa-award text-9xl"></i>
                        </div>
                        <div class="relative z-10">
                            <h3 class="text-2xl font-black mb-2">Selamat, Asisten Baru!</h3>
                            <p class="text-emerald-50 opacity-90 leading-relaxed mb-6">Pendaftaran Anda telah diterima. Sekarang Anda dapat mengakses fitur-fitur khusus Asisten Laboratorium pada menu dashboard.</p>
                            <a href="{{ route('aslab.dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-emerald-600 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-emerald-50 transition-all shadow-lg">
                                Masuk ke Dashboard Aslab
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column: Info & Summary -->
            <div class="space-y-6">
                @if($application->schedules->isNotEmpty())
                    <div class="bg-white rounded-3xl border border-zinc-200 shadow-sm p-8">
                        <h2 class="text-sm font-black uppercase tracking-[0.2em] text-[#1a4fa0] mb-6 flex items-center gap-2">
                            <i class="fas fa-calendar-alt"></i>
                            Jadwal Tes Lanjutan
                        </h2>
                        
                        <div class="space-y-4">
                            @foreach($application->schedules as $schedule)
                                <div class="p-5 rounded-2xl bg-blue-50/50 border border-blue-100 flex items-start gap-4 group">
                                    <div class="h-14 w-14 rounded-2xl bg-[#1a4fa0] text-white flex flex-col items-center justify-center shadow-lg shadow-blue-900/20">
                                        <span class="text-[10px] font-black uppercase leading-none">{{ $schedule->date->format('M') }}</span>
                                        <span class="text-xl font-black leading-none">{{ $schedule->date->format('d') }}</span>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-bold text-zinc-900 leading-tight">{{ $schedule->name }}</h4>
                                        <div class="flex flex-col gap-y-1 mt-2 text-[11px] font-medium text-zinc-500">
                                            <span class="flex items-center gap-1.5"><i class="far fa-clock text-blue-500"></i> {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</span>
                                            <span class="flex items-center gap-1.5"><i class="fas fa-location-dot text-rose-500"></i> {{ $schedule->location }}</span>
                                        </div>
                                        @if($schedule->notes)
                                            <div class="mt-4 p-3 rounded-xl bg-white/60 border border-blue-100 text-[11px] text-blue-800 italic leading-relaxed">
                                                <span class="font-black uppercase text-[9px] not-italic text-blue-400 block mb-1">Instruksi:</span>
                                                {{ $schedule->notes }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="bg-white rounded-3xl border border-zinc-200 shadow-sm p-8">
                    <h2 class="text-sm font-black uppercase tracking-[0.2em] text-zinc-400 mb-6">Ringkasan Lamaran</h2>
                    
                    <div class="space-y-6">
                        <div>
                            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-2">Periode Rekrutmen</p>
                            <p class="text-sm font-bold text-zinc-900 leading-tight">{{ $application->period->title }}</p>
                        </div>

                        <div>
                            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-2">Tanggal Submit</p>
                            <p class="text-sm font-bold text-zinc-900">{{ $application->created_at->translatedFormat('d F Y, H:i') }}</p>
                        </div>

                        @if($application->period->whatsapp_link)
                            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-between group">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center shadow-md">
                                        <i class="fab fa-whatsapp text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-[11px] font-black text-emerald-900 uppercase tracking-tight">Grup Koordinasi</h4>
                                        <p class="text-[10px] text-emerald-600 font-medium leading-none">Klik untuk bergabung</p>
                                    </div>
                                </div>
                                <a href="{{ $application->period->whatsapp_link }}" target="_blank" class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg font-black text-[9px] uppercase tracking-widest hover:bg-emerald-700 transition-all">
                                    Join
                                </a>
                            </div>
                        @endif

                        <div class="pt-4 border-t border-zinc-100">
                            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-3">Dokumen Terlampir</p>
                            <div class="flex flex-col gap-2">
                                <a href="{{ Storage::url($application->cv_path) }}" target="_blank" class="flex items-center justify-between p-3 rounded-xl bg-zinc-50 border border-zinc-100 hover:bg-zinc-100 transition-colors group">
                                    <div class="flex items-center gap-3">
                                        <i class="far fa-file-pdf text-rose-500"></i>
                                        <span class="text-xs font-bold text-zinc-700">Curriculum Vitae</span>
                                    </div>
                                    <i class="fas fa-external-link-alt text-[10px] text-zinc-300 group-hover:text-zinc-500"></i>
                                </a>
                                <a href="{{ Storage::url($application->khs_path) }}" target="_blank" class="flex items-center justify-between p-3 rounded-xl bg-zinc-50 border border-zinc-100 hover:bg-zinc-100 transition-colors group">
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-file-invoice text-blue-500"></i>
                                        <span class="text-xs font-bold text-zinc-700">KHS Terakhir</span>
                                    </div>
                                    <i class="fas fa-external-link-alt text-[10px] text-zinc-300 group-hover:text-zinc-500"></i>
                                </a>
                            </div>
                        </div>

                        @if($application->admin_notes && !$isRejected)
                            <div class="p-4 rounded-2xl bg-zinc-50 border border-zinc-100">
                                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1">Catatan Admin:</p>
                                <p class="text-[11px] text-zinc-600 italic leading-relaxed">"{{ $application->admin_notes }}"</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
