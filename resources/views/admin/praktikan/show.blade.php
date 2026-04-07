@extends('layouts.admin', ['title' => 'Detail Praktikan'])

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header Section -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.praktikan.index') }}" data-spa
                    class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-zinc-200 bg-white text-zinc-500 shadow-sm hover:bg-zinc-100 hover:text-zinc-900 transition-colors">
                    <i class="fas fa-arrow-left text-xs"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Detail Praktikan</h1>
                    <p class="text-sm text-zinc-500 mt-1">Informasi lengkap mengenai profil praktikan.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.praktikan.edit', $praktikan->id) }}" data-spa
                    class="inline-flex h-9 items-center justify-center rounded-md border border-zinc-200 bg-white px-4 py-2 text-sm font-medium shadow-sm hover:bg-zinc-100 hover:text-zinc-900 transition-colors">
                    <i class="fas fa-edit mr-2 text-xs"></i>
                    Edit Profil
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Profile Card -->
            <div class="md:col-span-1 space-y-6">
                <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm text-center">
                    <div class="relative mx-auto h-32 w-32 mb-4">
                        <div
                            class="h-full w-full rounded-2xl bg-zinc-100 border-4 border-white shadow-md overflow-hidden flex items-center justify-center">
                            @if ($praktikan->profile_picture)
                                <img src="{{ asset('storage/' . $praktikan->profile_picture) }}"
                                    class="h-full w-full object-cover">
                            @else
                                <span
                                    class="text-3xl font-bold text-zinc-300 uppercase">{{ substr($praktikan->name, 0, 2) }}</span>
                            @endif
                        </div>
                        <div
                            class="absolute -bottom-1 -right-1 h-6 w-6 rounded-full border-2 border-white {{ $praktikan->status ? 'bg-emerald-500' : 'bg-zinc-300' }} shadow-sm">
                        </div>
                    </div>
                    <h2 class="text-xl font-bold text-zinc-900">{{ $praktikan->name }}</h2>
                    <p class="text-sm text-zinc-500 font-medium tracking-tight mb-4">{{ $praktikan->npm }}</p>

                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $praktikan->status ? 'bg-emerald-50 text-emerald-600' : 'bg-zinc-100 text-zinc-500' }}">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ $praktikan->status ? 'bg-emerald-500' : 'bg-zinc-400' }}"></span>
                        {{ $praktikan->status ? 'Aktif' : 'Non-Aktif' }}
                    </div>
                </div>

                <div class="rounded-2xl bg-[#001f3f] p-5 text-white shadow-lg shadow-[#001f3f]/20">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="h-8 w-8 rounded-lg bg-white/10 flex items-center justify-center">
                            <i class="fas fa-shield-halved text-xs"></i>
                        </div>
                        <h4 class="text-sm font-bold uppercase tracking-wider">Akses Sistem</h4>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <p class="text-[10px] text-white/50 font-bold uppercase">Role</p>
                            <p class="text-sm font-medium">{{ $praktikan->role->name }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-white/50 font-bold uppercase">Terdaftar Sejak</p>
                            <p class="text-sm font-medium">{{ $praktikan->created_at->translatedFormat('d F Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Information -->
            <div class="md:col-span-2 space-y-6">
                <div class="rounded-2xl border border-zinc-200 bg-white overflow-hidden shadow-sm">
                    <div class="border-b border-zinc-100 bg-zinc-50/50 px-6 py-4">
                        <h3 class="text-sm font-bold text-zinc-900 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-id-card-clip text-zinc-400"></i>
                            Informasi Personal
                        </h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-8">
                            <div>
                                <dt class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.15em] mb-1">Username
                                </dt>
                                <dd class="text-sm font-semibold text-zinc-900">
                                    <span
                                        class="bg-zinc-100 px-2 py-0.5 rounded text-zinc-600">@</span>{{ $praktikan->username }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.15em] mb-1">Email
                                    Address</dt>
                                <dd class="text-sm font-semibold text-zinc-900 flex items-center gap-2">
                                    <i class="fas fa-envelope text-zinc-300"></i>
                                    {{ $praktikan->email }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.15em] mb-1">NPM</dt>
                                <dd class="text-sm font-semibold text-zinc-900 flex items-center gap-2">
                                    <i class="fas fa-graduation-cap text-zinc-300"></i>
                                    {{ $praktikan->npm }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.15em] mb-1">Jurusan
                                </dt>
                                <dd class="text-sm font-semibold text-zinc-900 flex items-center gap-2">
                                    <i class="fas fa-building text-zinc-300"></i>
                                    {{ $praktikan->jurusan ?? '-' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.15em] mb-1">Angkatan
                                </dt>
                                <dd class="text-sm font-semibold text-zinc-900 flex items-center gap-2">
                                    <i class="fas fa-calendar-alt text-zinc-300"></i>
                                    {{ $praktikan->angkatan ?? '-' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.15em] mb-1">No. HP
                                </dt>
                                <dd class="text-sm font-semibold text-zinc-900 flex items-center gap-2">
                                    <i class="fas fa-phone text-zinc-300"></i>
                                    {{ $praktikan->no_hp ?? '-' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.15em] mb-1">Status
                                    Verifikasi</dt>
                                <dd class="text-sm font-semibold text-emerald-600 flex items-center gap-2">
                                    <i class="fas fa-circle-check"></i>
                                    Verified Student
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="rounded-2xl border border-zinc-200 bg-white overflow-hidden shadow-sm">
                    <div class="border-b border-zinc-100 bg-zinc-50/50 px-6 py-4 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-zinc-900 uppercase tracking-wider flex items-center gap-2">
                             <i class="fas fa-book-bookmark text-zinc-400"></i>
                             Praktikum yang Diikuti
                         </h3>
                     </div>
                     <div class="p-6">
                         @if ($praktikan->praktikan?->pendaftarans?->count() > 0)
                             <div class="space-y-4">
                                 @foreach ($praktikan->praktikan->pendaftarans as $pendaftaran)
                                     <div class="flex items-center justify-between p-4 rounded-xl border border-zinc-100 bg-zinc-50/30">
                                         <div class="flex items-center gap-4">
                                             <div class="h-10 w-10 rounded-lg bg-[#001f3f]/5 flex items-center justify-center text-[#001f3f]">
                                                 <i class="fas fa-flask text-xs"></i>
                                             </div>
                                             <div>
                                                 <h4 class="text-sm font-bold text-zinc-900">{{ $pendaftaran->praktikum->nama_praktikum }}</h4>
                                                 <p class="text-[10px] text-zinc-500 font-medium uppercase tracking-wider">
                                                     {{ $pendaftaran->sesi?->nama_sesi ?? 'Sesi belum ditentukan' }} 
                                                     @if($pendaftaran->sesi)
                                                         • {{ $pendaftaran->sesi->hari }}, {{ $pendaftaran->sesi->jam_mulai }} - {{ $pendaftaran->sesi->jam_selesai }}
                                                     @endif
                                                 </p>
                                             </div>
                                         </div>
                                         <span @class([
                                             'px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border',
                                             'bg-emerald-50 text-emerald-600 border-emerald-100' => $pendaftaran->status === 'approved',
                                             'bg-amber-50 text-amber-600 border-amber-100' => $pendaftaran->status === 'pending',
                                             'bg-rose-50 text-rose-600 border-rose-100' => $pendaftaran->status === 'rejected',
                                             'bg-zinc-50 text-zinc-500 border-zinc-100' => !in_array($pendaftaran->status, ['approved', 'pending', 'rejected']),
                                         ])>
                                             {{ $pendaftaran->status }}
                                         </span>
                                     </div>
                                 @endforeach
                             </div>
                         @else
                             <div class="flex flex-col items-center justify-center py-10 text-center space-y-3">
                                 <div
                                     class="h-12 w-12 rounded-full bg-zinc-50 flex items-center justify-center text-zinc-300 border border-zinc-100">
                                     <i class="fas fa-flask"></i>
                                 </div>
                                 <div>
                                     <p class="text-sm font-semibold text-zinc-900">Belum terdaftar di praktikum manapun</p>
                                     <p class="text-xs text-zinc-500 mt-1">Data pendaftaran praktikum akan muncul di sini.</p>
                                 </div>
                             </div>
                         @endif
                     </div>
                </div>
            </div>
        </div>
    </div>
@endsection
