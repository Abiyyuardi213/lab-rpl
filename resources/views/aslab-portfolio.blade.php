@extends('layouts.app')

@section('title', 'Portfolio ' . $aslab->user->name . ' — Lab RPL ITATS')

@section('content')
<div class="bg-white min-h-screen">
    {{-- Hero/Header Portfolio --}}
    <section class="relative pt-12 pb-24 overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-48 bg-[#1a4fa0]"></div>
        
        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="flex flex-col md:flex-row gap-12 items-center md:items-end">
                {{-- Profile Image --}}
                <div class="relative group">
                    <div class="absolute -inset-1 bg-gradient-to-tr from-[#1a4fa0] to-blue-400 blur opacity-25 group-hover:opacity-50 transition duration-500"></div>
                    <div class="relative aspect-[4/5] w-48 sm:w-64 overflow-hidden bg-white shadow-2xl border-4 border-white">
                        <img src="{{ $aslab->profile_image ? asset('storage/' . $aslab->profile_image) : ($aslab->user->profile_picture ? asset('storage/' . $aslab->user->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($aslab->user->name) . '&background=1a4fa0&color=fff&size=500') }}" 
                             alt="{{ $aslab->user->name }}"
                             class="h-full w-full object-cover">
                    </div>
                </div>

                {{-- Basic Info --}}
                <div class="flex-1 text-center md:text-left pb-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-[#1a4fa0] text-[10px] font-bold uppercase tracking-widest mb-4">
                        <i class="fas fa-shield-alt"></i> Verified Lab Assistant
                    </div>
                    <h1 class="text-4xl sm:text-5xl font-black text-slate-900 leading-tight tracking-tighter">
                        {{ $aslab->user->name }}
                    </h1>
                    <p class="text-lg text-[#1a4fa0] font-bold mt-2 uppercase tracking-wide">
                        {{ $aslab->jabatan }} • {{ $aslab->angkatan }}
                    </p>
                    
                    <div class="flex flex-wrap justify-center md:justify-start gap-4 mt-6">
                        @if($aslab->instagram_link)
                            <a href="{{ $aslab->instagram_link }}" target="_blank" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-pink-50 hover:text-pink-600 transition-all shadow-sm">
                                <i class="fab fa-instagram"></i>
                            </a>
                        @endif
                        @if($aslab->github_link)
                            <a href="{{ $aslab->github_link }}" target="_blank" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-slate-900 hover:text-white transition-all shadow-sm">
                                <i class="fab fa-github"></i>
                            </a>
                        @endif
                        @if($aslab->linkedin_link)
                            <a href="{{ $aslab->linkedin_link }}" target="_blank" class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-blue-50 hover:text-blue-700 transition-all shadow-sm">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Content Section --}}
    <section class="pb-24 max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            {{-- About / Bio --}}
            <div class="lg:col-span-2 space-y-12">
                <div>
                    <h3 class="text-xl font-black text-slate-900 mb-6 flex items-center gap-3">
                        <span class="w-8 h-1 bg-[#1a4fa0] rounded-full"></span>
                        About Me
                    </h3>
                    <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed text-lg">
                        {!! $aslab->bio ? nl2br(e($aslab->bio)) : 'Asisten belum menambahkan biografi singkat.' !!}
                    </div>
                </div>

                {{-- Skills Section --}}
                <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm mb-12">
                    <h3 class="text-sm font-black text-zinc-400 uppercase tracking-[0.2em] mb-6 flex items-center gap-3">
                        <i class="fas fa-brain text-amber-500/80"></i>
                        Bidang Keahlian
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @forelse($aslab->skills ?? [] as $skill)
                            <span class="px-3 py-1.5 rounded-lg bg-zinc-50 border border-zinc-200 text-zinc-700 text-xs font-bold hover:border-zinc-300 transition-colors">
                                {{ $skill }}
                            </span>
                        @empty
                            <p class="text-zinc-400 italic text-sm">Belum ada data keahlian.</p>
                        @endforelse
                    </div>
                </div>

                @if($aslab->achievements && count($aslab->achievements) > 0)
                <div class="space-y-6">
                    <h3 class="text-xl font-bold text-zinc-900 flex items-center gap-3">
                        <span class="w-1.5 h-6 bg-zinc-900 rounded-full"></span>
                        Prestasi & Penghargaan
                    </h3>
                    <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-zinc-50 border-b border-zinc-100">
                                <tr>
                                    <th class="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest w-16">No</th>
                                    <th class="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Nama Prestasi</th>
                                    <th class="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest w-40 text-right">Tahun</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100">
                                @foreach($aslab->achievements as $index => $item)
                                <tr class="hover:bg-zinc-50/50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-medium text-zinc-400">{{ sprintf('%02d', $index + 1) }}</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-zinc-900">{{ $item->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-zinc-500 font-medium text-right">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border bg-zinc-50 text-zinc-600 border-zinc-200">
                                            @if($item->start_year && $item->end_year)
                                                {{ $item->start_year }} — {{ $item->end_year }}
                                            @elseif($item->start_year)
                                                {{ $item->start_year }}
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                @if($aslab->experiences && count($aslab->experiences) > 0)
                <div class="space-y-6 mt-12">
                    <h3 class="text-xl font-bold text-zinc-900 flex items-center gap-3">
                        <span class="w-1.5 h-6 bg-zinc-900 rounded-full"></span>
                        Pengalaman Organisasi
                    </h3>
                    <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-zinc-50 border-b border-zinc-100">
                                <tr>
                                    <th class="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest w-16">No</th>
                                    <th class="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Organisasi / Jabatan</th>
                                    <th class="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest w-40 text-right">Tahun</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100">
                                @foreach($aslab->experiences as $index => $item)
                                <tr class="hover:bg-zinc-50/50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-medium text-zinc-400">{{ sprintf('%02d', $index + 1) }}</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-zinc-900">{{ $item->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-zinc-500 font-medium text-right">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border bg-zinc-50 text-zinc-600 border-zinc-200">
                                            @if($item->start_year && $item->end_year)
                                                {{ $item->start_year }} — {{ $item->end_year }}
                                            @elseif($item->start_year)
                                                {{ $item->start_year }}
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                @if($aslab->activities && count($aslab->activities) > 0)
                <div class="space-y-6 mt-12">
                    <h3 class="text-xl font-bold text-zinc-900 flex items-center gap-3">
                        <span class="w-1.5 h-6 bg-zinc-900 rounded-full"></span>
                        Kegiatan Prodi & Kampus
                    </h3>
                    <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-zinc-50 border-b border-zinc-100">
                                <tr>
                                    <th class="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest w-16">No</th>
                                    <th class="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Nama Kegiatan</th>
                                    <th class="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest w-40 text-right">Tahun</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100">
                                @foreach($aslab->activities as $index => $item)
                                <tr class="hover:bg-zinc-50/50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-medium text-zinc-400">{{ sprintf('%02d', $index + 1) }}</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-zinc-900">{{ $item->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-zinc-500 font-medium text-right">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border bg-zinc-50 text-zinc-600 border-zinc-200">
                                            {{ $item->month }} {{ $item->year }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>

            {{-- Sidebar Info --}}
            <div class="lg:sticky lg:top-8 h-fit space-y-8">
                <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                    <h4 class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-6">Informasi Akademik</h4>
                    <ul class="space-y-6">
                        <li class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-zinc-50 border border-zinc-100 flex items-center justify-center text-zinc-900">
                                <i class="fas fa-id-card text-xs"></i>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-zinc-400 leading-none mb-1">NPM</p>
                                <p class="text-sm font-bold text-zinc-900">{{ $aslab->npm }}</p>
                            </div>
                        </li>
                        <li class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-zinc-50 border border-zinc-100 flex items-center justify-center text-zinc-900">
                                <i class="fas fa-graduation-cap text-xs"></i>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-zinc-400 leading-none mb-1">Jurusan</p>
                                <p class="text-sm font-bold text-zinc-900">{{ $aslab->jurusan }}</p>
                            </div>
                        </li>
                    </ul>

                    <div class="mt-8 pt-6 border-t border-zinc-100">
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $aslab->no_hp ?? '') }}" target="_blank" class="w-full inline-flex h-11 items-center justify-center gap-3 rounded-lg bg-zinc-900 text-white text-xs font-bold hover:bg-zinc-800 transition-all active:scale-95 shadow-lg shadow-zinc-200">
                            <i class="fab fa-whatsapp text-lg"></i> Hubungi WhatsApp
                        </a>
                    </div>
                </div>

                {{-- Social Media Card --}}
                <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
                    <h4 class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-6">Media Sosial</h4>
                    <div class="space-y-4">
                        @if($aslab->instagram_link)
                        <a href="{{ $aslab->instagram_link }}" target="_blank" class="flex items-center gap-4 group">
                            <div class="w-10 h-10 rounded-lg bg-zinc-50 border border-zinc-100 flex items-center justify-center text-zinc-400 group-hover:text-pink-600 group-hover:bg-pink-50 group-hover:border-pink-100 transition-all">
                                <i class="fab fa-instagram"></i>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-zinc-400 leading-none mb-1">Instagram</p>
                                <p class="text-sm font-bold text-zinc-900 truncate max-w-[150px]">@ {{ Str::afterLast(rtrim($aslab->instagram_link, '/'), '/') }}</p>
                            </div>
                        </a>
                        @endif
                        
                        @if($aslab->github_link)
                        <a href="{{ $aslab->github_link }}" target="_blank" class="flex items-center gap-4 group">
                            <div class="w-10 h-10 rounded-lg bg-zinc-50 border border-zinc-100 flex items-center justify-center text-zinc-400 group-hover:text-zinc-900 group-hover:bg-zinc-100 group-hover:border-zinc-200 transition-all">
                                <i class="fab fa-github"></i>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-zinc-400 leading-none mb-1">GitHub</p>
                                <p class="text-sm font-bold text-zinc-900 truncate max-w-[150px]">{{ Str::afterLast(rtrim($aslab->github_link, '/'), '/') }}</p>
                            </div>
                        </a>
                        @endif

                        @if($aslab->linkedin_link)
                        <a href="{{ $aslab->linkedin_link }}" target="_blank" class="flex items-center gap-4 group">
                            <div class="w-10 h-10 rounded-lg bg-zinc-50 border border-zinc-100 flex items-center justify-center text-zinc-400 group-hover:text-blue-600 group-hover:bg-blue-50 group-hover:border-blue-100 transition-all">
                                <i class="fab fa-linkedin"></i>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-zinc-400 leading-none mb-1">LinkedIn</p>
                                <p class="text-sm font-bold text-zinc-900 truncate max-w-[150px]">{{ Str::afterLast(rtrim($aslab->linkedin_link, '/'), '/') }}</p>
                            </div>
                        </a>
                        @endif

                        @if(!$aslab->instagram_link && !$aslab->github_link && !$aslab->linkedin_link)
                        <div class="flex flex-col items-center justify-center py-4 text-center">
                            <i class="fas fa-share-alt text-zinc-200 text-xl mb-2"></i>
                            <p class="text-[10px] text-zinc-400 italic">Media sosial belum ditambahkan</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
