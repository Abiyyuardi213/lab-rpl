@extends('layouts.admin')

@section('title', 'Manajemen Penilaian')

@section('content')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 uppercase">Pusat Penilaian</h1>
                <p class="text-sm text-zinc-500 mt-1 font-medium italic">"Kelola dan tinjau seluruh nilai praktikum mahasiswa."</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Penilaian</span>
            </div>
        </div>

        <div class="p-6 bg-zinc-900 rounded-xl border border-zinc-800 shadow-2xl relative overflow-hidden group">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <h3 class="text-lg font-black text-white uppercase tracking-tight">Admin Authority Mode</h3>
                    <p class="text-zinc-400 text-xs font-medium max-w-xl">Akses bebas waktu untuk memberikan atau mengubah nilai praktikum mahasiswa pada seluruh modul yang tersedia.</p>
                </div>
                <div class="flex items-center gap-4">
                     <div class="px-4 py-2 bg-zinc-800 rounded-lg border border-zinc-700">
                         <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Total Mata Praktikum</p>
                         <p class="text-xl font-black text-white leading-none mt-1">{{ $praktikums->count() }}</p>
                     </div>
                </div>
            </div>
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/5 rounded-full blur-3xl"></div>
        </div>

        <!-- Praktikum Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($praktikums as $p)
                <div class="group bg-white rounded-xl border border-zinc-200 shadow-sm overflow-hidden flex flex-col transition-all hover:shadow-lg">
                    <!-- Card Header -->
                    <div class="h-28 bg-gradient-to-br from-zinc-800 to-zinc-900 p-5 flex flex-col justify-between relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 pointer-events-none">
                            <i class="fas fa-book-open text-7xl text-white"></i>
                        </div>
                        <div class="flex items-start justify-between relative z-10">
                            <span class="bg-white/10 backdrop-blur-md text-white text-[9px] font-bold px-2 py-1 rounded border border-white/20 uppercase tracking-widest leading-none">
                                {{ $p->kode_praktikum }}
                            </span>
                        </div>
                        <div class="relative z-10">
                            <h3 class="text-sm font-black text-white line-clamp-1 uppercase tracking-tight leading-none">{{ $p->nama_praktikum }}</h3>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-5 flex-grow flex flex-col">
                        <div class="flex items-center justify-between mb-6">
                            <div class="space-y-1">
                                <p class="text-[8px] font-bold text-zinc-400 uppercase tracking-widest leading-none">Status Aktif</p>
                                <div class="flex items-center gap-1.5 mt-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span class="text-[10px] font-bold text-zinc-700 uppercase leading-none">{{ $p->status_praktikum }}</span>
                                </div>
                            </div>
                            <div class="text-right space-y-1">
                                <p class="text-[8px] font-bold text-zinc-400 uppercase tracking-widest leading-none">Pendaftar</p>
                                <p class="text-[10px] font-bold text-zinc-700 uppercase mt-1.5 leading-none">{{ $p->pendaftarans_count }} Orang</p>
                            </div>
                        </div>

                        <div class="space-y-3 pt-4 border-t border-zinc-100 mb-6">
                             <div class="flex items-center justify-between">
                                 <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest leading-none">Modul Praktikum</span>
                                 <span class="text-[10px] font-black text-zinc-900">{{ $p->jadwals_count }} Sesi</span>
                             </div>
                        </div>

                        <div class="mt-auto">
                            <a href="{{ route('admin.penilaian.praktikum', $p->id) }}" 
                               class="w-full h-10 bg-zinc-900 text-white text-[10px] font-bold uppercase tracking-widest rounded-lg shadow-lg hover:bg-zinc-800 transition-all flex items-center justify-center gap-2 group-active:scale-95">
                                Kelola Nilai
                                <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
