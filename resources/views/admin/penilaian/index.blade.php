@extends('layouts.admin')

@section('title', 'Manajemen Penilaian')

@section('content')
    <div class="space-y-8">
        <!-- Header -->
        <div class="relative overflow-hidden bg-gradient-to-br from-slate-800 to-slate-900 rounded-3xl p-8 md:p-12 shadow-2xl">
            <div class="relative z-10">
                <h1 class="text-3xl md:text-5xl font-black text-white tracking-tight leading-tight uppercase mb-4">
                    Pusat Penilaian
                </h1>
                <p class="text-slate-400 max-w-2xl text-sm md:text-lg leading-relaxed font-medium">
                    Kelola dan tinjau seluruh nilai praktikum mahasiswa. Admin memiliki akses penuh untuk memberikan nilai kapan saja tanpa batasan waktu sesi.
                </p>
            </div>
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 bg-white/5 rounded-full blur-3xl"></div>
        </div>

        <!-- Praktikum Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($praktikums as $p)
                <div class="group bg-white p-6 rounded-[2.5rem] border border-slate-200 shadow-sm hover:border-slate-400 hover:shadow-2xl transition-all duration-500">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-800 group-hover:bg-slate-800 group-hover:text-white transition-all duration-500 shadow-inner">
                            <i class="fas fa-book-open text-lg"></i>
                        </div>
                        <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-[9px] font-black font-mono">
                            {{ $p->kode_praktikum }}
                        </span>
                    </div>

                    <div class="space-y-2 mb-8">
                        <h4 class="text-xl font-black text-slate-900 leading-tight uppercase">
                            {{ $p->nama_praktikum }}
                        </h4>
                        <div class="flex items-center gap-3">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $p->jadwals_count }} Modul</span>
                            <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $p->pendaftarans_count }} Praktikan</span>
                        </div>
                    </div>

                    <a href="{{ route('admin.penilaian.praktikum', $p->id) }}" 
                       class="w-full py-4 border-2 border-slate-800 rounded-2xl text-[11px] font-black text-slate-800 flex items-center justify-center gap-3 hover:bg-slate-800 hover:text-white transition-all">
                        PILIH PRAKTIKUM
                        <i class="fas fa-chevron-right text-[10px]"></i>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endsection
