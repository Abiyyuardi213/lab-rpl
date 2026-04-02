@extends('layouts.admin')

@section('title', 'Semua Notifikasi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Semua Notifikasi</h1>
            <p class="text-slate-500 mt-1 text-sm sm:text-base italic">"Pusat informasi dan pengumuman untuk Anda."</p>
        </div>
        <div class="flex items-center gap-3">
            @if(Auth::user()->unreadNotifications->count() > 0)
            <a href="{{ route('notifications.markAllAsRead') }}" class="px-4 py-2 bg-emerald-50 text-emerald-600 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-emerald-100 transition-all border border-emerald-100 shadow-sm flex items-center gap-2">
                <i class="fas fa-check-double"></i> Tandai Semua Dibaca
            </a>
            @endif
        </div>
    </div>

    <!-- Content -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-[#001f3f]/5">
            <h2 class="text-sm font-black text-slate-900 tracking-widest uppercase"><i class="fas fa-bell text-[#001f3f] mr-2"></i> Riwayat Notifikasi</h2>
        </div>
        
        <div class="divide-y divide-slate-100">
            @forelse($notifications as $notification)
                <div class="p-6 hover:bg-zinc-50 transition-colors {{ $notification->read_at ? 'opacity-70' : 'bg-[#001f3f]/[0.02]' }}">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-grow">
                            <div class="flex items-center gap-3 mb-1">
                                @if(!$notification->read_at)
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-500 shadow-sm shadow-rose-200 flex-shrink-0 animate-pulse"></span>
                                @endif
                                <h3 class="text-base font-bold text-slate-900">{{ $notification->data['title'] ?? 'Info Notifikasi' }}</h3>
                            </div>
                            <p class="text-sm text-slate-600 mt-1 whitespace-pre-wrap">{{ $notification->data['message'] ?? '' }}</p>
                            <div class="flex items-center gap-4 mt-3">
                                <span class="text-xs font-medium text-slate-400 capitalize bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200">
                                    <i class="far fa-clock mr-1"></i> {{ $notification->created_at->diffForHumans() }}
                                </span>
                                <span class="text-xs text-slate-400">
                                    {{ $notification->created_at->format('d M Y, H:i') }}
                                </span>
                            </div>
                        </div>
                        
                        @if(!$notification->read_at)
                        <div class="flex-shrink-0">
                            <a href="{{ route('notifications.markAsRead', $notification->id) }}" class="inline-flex items-center justify-center w-10 h-10 rounded-full border border-slate-200 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 hover:border-emerald-200 transition-all group" title="Tandai dibaca">
                                <i class="fas fa-check group-hover:scale-110 transition-transform"></i>
                            </a>
                        </div>
                        @else
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full text-emerald-500 bg-emerald-50/50" title="Sudah dibaca">
                                <i class="fas fa-check-double"></i>
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-16 text-center">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                        <i class="far fa-bell-slash text-3xl text-slate-300"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-1">Belum ada notifikasi</h3>
                    <p class="text-sm text-slate-500">Anda belum menerima notifikasi apapun dari sistem.</p>
                </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        @if($notifications->hasPages())
        <div class="p-6 border-t border-slate-100 bg-slate-50/50">
            {{ $notifications->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
