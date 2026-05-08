@extends('layouts.admin')

@section('title', 'Rekapitulasi Rating & Ulasan')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Rating & Ulasan Praktikum</h1>
            <p class="text-sm text-zinc-500 mt-1">Pantau seluruh feedback dan penilaian dari praktikan.</p>
        </div>
        <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
            <span>/</span>
            <span class="text-zinc-900 font-semibold">Ratings</span>
        </div>
    </div>
    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-zinc-200 p-6 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-1">Total Rating Diterima</p>
                <p class="text-3xl font-black text-zinc-900">{{ $totalRatings }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-zinc-50 text-amber-500 flex items-center justify-center text-xl">
                <i class="fas fa-comment-dots"></i>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-zinc-200 p-6 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-1">Rata-rata Rating Aslab</p>
                <p class="text-3xl font-black text-zinc-900">{{ number_format($avgRatingAslab ?? 0, 1) }}<span class="text-lg text-zinc-400 font-medium">/5</span></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-zinc-50 text-zinc-400 flex items-center justify-center text-xl">
                <i class="fas fa-star"></i>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-zinc-200 p-6 flex items-center justify-between shadow-sm">
            <div>
                <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-1">Rata-rata Rating Praktikum</p>
                <p class="text-3xl font-black text-zinc-900">{{ number_format($avgRatingPraktikum ?? 0, 1) }}<span class="text-lg text-zinc-400 font-medium">/5</span></p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-zinc-50 text-zinc-400 flex items-center justify-center text-xl">
                <i class="fas fa-flask"></i>
            </div>
        </div>
    </div>

    <!-- Table Container -->
    <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-zinc-50 border-b border-zinc-100 text-zinc-500 font-medium h-10">
                    <tr>
                        <th class="px-6 align-middle font-medium text-zinc-500 w-32">WAKTU</th>
                        <th class="px-6 align-middle font-medium text-zinc-500">PRAKTIKAN</th>
                        <th class="px-6 align-middle font-medium text-zinc-500">PRAKTIKUM</th>
                        <th class="px-6 align-middle font-medium text-zinc-500">ASISTEN LAB</th>
                        <th class="px-6 align-middle font-medium text-zinc-500 text-center">RATING PRAKTIKUM</th>
                        <th class="px-6 align-middle font-medium text-zinc-500 text-center">RATING ASLAB</th>
                        <th class="px-6 align-middle font-medium text-zinc-500">ULASAN</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 text-zinc-900">
                    @forelse($ratings as $rating)
                        <tr class="hover:bg-zinc-50/50 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-semibold text-zinc-900 block leading-tight">{{ $rating->created_at->format('d M Y') }}</span>
                                <span class="text-[10px] text-zinc-400 uppercase tracking-tight">{{ $rating->created_at->format('H:i') }} WIB</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-medium text-zinc-900 block leading-tight">{{ $rating->pendaftaran->praktikan->nama_lengkap }}</span>
                                <span class="text-[10px] text-zinc-500 uppercase tracking-widest">{{ $rating->pendaftaran->praktikan->npm }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-medium text-zinc-900 block leading-tight">{{ $rating->pendaftaran->praktikum->nama_praktikum }}</span>
                                <span class="text-[10px] text-zinc-500 uppercase tracking-widest">{{ $rating->pendaftaran->praktikum->periode_praktikum }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-medium text-zinc-900 block leading-tight">{{ $rating->pendaftaran->aslab->nama_lengkap ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center text-amber-400 text-xs gap-0.5">
                                    @for($i=1; $i<=5; $i++)
                                        <i class="fas fa-star {{ $i <= $rating->rating_praktikum ? 'text-amber-400' : 'text-zinc-200' }}"></i>
                                    @endfor
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center text-amber-400 text-xs gap-0.5">
                                    @for($i=1; $i<=5; $i++)
                                        <i class="fas fa-star {{ $i <= $rating->rating_asisten ? 'text-amber-400' : 'text-zinc-200' }}"></i>
                                    @endfor
                                </div>
                            </td>
                            <td class="px-6 py-4 max-w-xs">
                                <div class="text-xs text-zinc-600 mb-1 line-clamp-2" title="{{ $rating->ulasan_praktikum }}">
                                    <span class="font-semibold text-[10px] uppercase text-zinc-400">Prak:</span> {{ $rating->ulasan_praktikum ?: '-' }}
                                </div>
                                <div class="text-xs text-zinc-600 line-clamp-2" title="{{ $rating->ulasan_asisten }}">
                                    <span class="font-semibold text-[10px] uppercase text-zinc-400">Aslab:</span> {{ $rating->ulasan_asisten ?: '-' }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center text-zinc-400">
                                    <div class="h-20 w-20 rounded-full bg-zinc-50 flex items-center justify-center mb-4 border border-zinc-100 shadow-inner">
                                        <i class="fas fa-star-half-alt text-3xl opacity-20"></i>
                                    </div>
                                    <h3 class="text-sm font-black uppercase tracking-[0.2em] text-zinc-400">Belum Ada Rating</h3>
                                    <p class="text-[10px] italic mt-1 font-medium tracking-tight">Belum ada praktikan yang mengirimkan ulasan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($ratings->hasPages())
            <div class="px-6 py-4 border-t border-zinc-100">
                {{ $ratings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
