@extends('layouts.admin')

@section('title', 'Rating & Ulasan Praktikan')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-[#001f3f]">Rating & Ulasan Praktikan</h1>
            <p class="text-sm text-slate-500 mt-1">Feedback dari mahasiswa bimbingan Anda terkait pelaksanaan praktikum.</p>
        </div>
        <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
            <a href="{{ route('aslab.dashboard') }}" class="hover:text-[#001f3f] transition-colors">Home</a>
            <span>/</span>
            <span class="text-[#001f3f] font-semibold">Ratings</span>
        </div>
    </div>

    <!-- Summary Stats (Optional - simple cards) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-6 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Rating Diterima</p>
                <p class="text-3xl font-black text-[#001f3f]">{{ $totalRatings }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl">
                <i class="fas fa-comment-dots"></i>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-6 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Rata-rata Rating Anda</p>
                <p class="text-3xl font-black text-[#001f3f]">{{ number_format($avgRatingAslab ?? 0, 1) }}<span class="text-lg text-slate-400 font-medium">/5</span></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-[#001f3f]/5 text-[#001f3f] flex items-center justify-center text-xl">
                <i class="fas fa-star"></i>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-6 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Rata-rata Rating Praktikum</p>
                <p class="text-3xl font-black text-[#001f3f]">{{ number_format($avgRatingPraktikum ?? 0, 1) }}<span class="text-lg text-slate-400 font-medium">/5</span></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center text-xl">
                <i class="fas fa-flask"></i>
            </div>
        </div>
    </div>

    <!-- Rating List -->
    <div class="grid grid-cols-1 gap-6">
        @forelse($ratings as $rating)
            <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden group hover:border-[#001f3f] transition-all duration-300">
                <div class="p-6 md:p-8 flex flex-col md:flex-row gap-8">
                    <!-- Identity Side -->
                    <div class="md:w-1/3 border-b md:border-b-0 md:border-r border-slate-100 pb-6 md:pb-0 md:pr-8">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 leading-tight">{{ $rating->pendaftaran->praktikan->nama_lengkap }}</h3>
                                <p class="text-[10px] font-mono text-slate-500 mt-0.5">{{ $rating->pendaftaran->praktikan->npm }}</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="inline-flex px-3 py-1 bg-slate-50 rounded-lg text-xs font-medium text-slate-600 border border-slate-100">
                                {{ $rating->pendaftaran->praktikum->nama_praktikum }}
                            </div>
                            <p class="text-[10px] text-slate-400">Dikirim pada: {{ $rating->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>

                    <!-- Reviews Side -->
                    <div class="md:w-2/3 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Rating Praktikum -->
                        <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100">
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Rating Praktikum</h4>
                            <div class="flex text-amber-400 text-sm gap-1 mb-3">
                                @for($i=1; $i<=5; $i++)
                                    <i class="fas fa-star {{ $i <= $rating->rating_praktikum ? 'text-amber-400' : 'text-slate-200' }}"></i>
                                @endfor
                            </div>
                            <p class="text-sm text-slate-600 italic">"{{ $rating->ulasan_praktikum ?: 'Tidak ada ulasan tertulis.' }}"</p>
                        </div>

                        <!-- Rating Asisten -->
                        <div class="bg-[#001f3f]/5 p-5 rounded-2xl border border-[#001f3f]/10">
                            <h4 class="text-[10px] font-black text-[#001f3f]/60 uppercase tracking-widest mb-3">Rating Untuk Anda</h4>
                            <div class="flex text-amber-400 text-sm gap-1 mb-3">
                                @for($i=1; $i<=5; $i++)
                                    <i class="fas fa-star {{ $i <= $rating->rating_asisten ? 'text-amber-400' : 'text-slate-200' }}"></i>
                                @endfor
                            </div>
                            <p class="text-sm text-slate-700 italic">"{{ $rating->ulasan_asisten ?: 'Tidak ada ulasan tertulis.' }}"</p>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="py-20 bg-white border border-dashed border-slate-300 rounded-[32px] flex flex-col items-center justify-center text-center">
                <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center mb-4">
                    <i class="fas fa-comment-slash text-slate-300 text-2xl"></i>
                </div>
                <p class="text-slate-400 font-medium italic">Belum ada praktikan yang mengirimkan rating/ulasan.</p>
            </div>
        @endforelse

        @if($ratings->hasPages())
            <div class="mt-4">
                {{ $ratings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
