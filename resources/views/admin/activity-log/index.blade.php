@extends('layouts.admin')

@section('title', 'Log Aktivitas')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Log Aktivitas</h1>
            <p class="text-sm text-zinc-500 mt-1">Monitoring riwayat aksi pengguna sistem secara real-time.</p>
        </div>
        <div class="flex items-center gap-2 text-xs font-medium text-zinc-500">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-zinc-900 transition-colors">Home</a>
            <span>/</span>
            <span class="text-zinc-900 font-semibold">Log Aktivitas</span>
        </div>
    </div>

    <!-- Table Container -->
    <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden">
        <div class="p-6 pb-4 flex flex-col md:flex-row items-center justify-between gap-4 border-b border-zinc-100">
            <form action="{{ route('admin.logs.index') }}" method="GET" class="flex flex-col md:flex-row items-center gap-3 w-full">
                <div class="relative w-full md:max-w-sm">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aktivitas atau user..." 
                        class="flex h-9 w-full rounded-md border border-zinc-200 bg-transparent px-3 py-1 pl-9 text-sm shadow-sm transition-colors placeholder:text-zinc-500 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
                </div>
                
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <select name="role" onchange="this.form.submit()" 
                        class="h-9 w-full md:w-40 rounded-md border border-zinc-200 bg-transparent px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
                        <option value="">Semua Role</option>
                        <option value="Admin" {{ request('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                        <option value="Aslab" {{ request('role') == 'Aslab' ? 'selected' : '' }}>Aslab</option>
                        <option value="Praktikan" {{ request('role') == 'Praktikan' ? 'selected' : '' }}>Praktikan</option>
                    </select>

                    <button type="submit" class="inline-flex h-9 items-center justify-center rounded-md bg-zinc-900 px-4 py-2 text-sm font-medium text-white shadow hover:bg-zinc-900/90 transition-colors">
                        Filter
                    </button>
                    
                    <a href="{{ route('admin.logs.index') }}" class="inline-flex h-9 w-10 items-center justify-center rounded-md border border-zinc-200 bg-white text-zinc-500 hover:bg-zinc-50 hover:text-zinc-900 transition-colors shadow-sm">
                        <i class="fas fa-sync-alt text-xs"></i>
                    </a>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-zinc-50 border-b border-zinc-100 text-zinc-500 font-medium h-10">
                    <tr>
                        <th class="px-6 align-middle font-medium text-zinc-500 w-32">WAKTU</th>
                        <th class="px-6 align-middle font-medium text-zinc-500">PENGGUNA</th>
                        <th class="px-6 align-middle font-medium text-zinc-500">AKTIVITAS</th>
                        <th class="px-6 align-middle font-medium text-zinc-500 text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 text-zinc-900">
                    @forelse($logs as $log)
                        <tr class="hover:bg-zinc-50/50 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-semibold text-zinc-900 block leading-tight">{{ $log->created_at->format('d M Y') }}</span>
                                <span class="text-[10px] text-zinc-400 uppercase tracking-tight">{{ $log->created_at->format('H:i:s') }} WIB</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-zinc-100 flex items-center justify-center text-zinc-500 text-[10px] border border-zinc-200">
                                        <i class="fas fa-{{ $log->role == 'Admin' ? 'user-shield' : ($log->role == 'Aslab' ? 'user-graduate' : 'user-edit') }}"></i>
                                    </div>
                                    <div>
                                        <span class="font-semibold text-zinc-900 block leading-tight">{{ $log->user ? $log->user->name : 'System' }}</span>
                                        <span class="text-[10px] text-zinc-400 uppercase tracking-tight">{{ $log->role }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-medium text-zinc-900 block leading-tight">{{ $log->activity }}</span>
                                <span class="text-xs text-zinc-500 line-clamp-1 max-w-xs">{{ $log->description }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button onclick="showLogDetail('{{ $log->id }}')" 
                                    class="inline-flex h-8 items-center justify-center rounded-md border border-zinc-200 bg-white px-3 text-xs font-medium text-zinc-900 shadow-sm hover:bg-zinc-50 transition-colors">
                                    Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center text-zinc-400">
                                    <div class="h-20 w-20 rounded-full bg-zinc-50 flex items-center justify-center mb-4 border border-zinc-100 shadow-inner">
                                        <i class="fas fa-history text-3xl opacity-20"></i>
                                    </div>
                                    <h3 class="text-sm font-black uppercase tracking-[0.2em] text-zinc-400">Data Kosong</h3>
                                    <p class="text-[10px] italic mt-1 font-medium tracking-tight">Belum ada aktivitas yang tercatat saat ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-zinc-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Log Detail Modal -->
<div id="logModal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-zinc-950/50 backdrop-blur-sm" onclick="closeLogModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl p-4">
        <div class="bg-white rounded-xl shadow-lg border border-zinc-200 overflow-hidden flex flex-col max-h-[90vh]">
            <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between">
                <div>
                    <h4 class="text-lg font-bold text-zinc-900 tracking-tight">Detail Aktivitas</h4>
                    <p class="text-xs text-zinc-500">Informasi teknis lengkap terkait tindakan pengguna.</p>
                </div>
                <button onclick="closeLogModal()" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-zinc-400 hover:text-zinc-900 hover:bg-zinc-100 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div id="logDetailContent" class="p-6 space-y-6 overflow-y-auto">
                <!-- Content will be injected by JS -->
                <div class="flex items-center justify-center py-10 text-zinc-400">
                    <i class="fas fa-circle-notch fa-spin text-xl"></i>
                </div>
            </div>

            <div class="px-6 py-4 bg-zinc-50 border-t border-zinc-100 flex justify-end">
                <button onclick="closeLogModal()" class="inline-flex h-9 items-center justify-center rounded-md border border-zinc-200 bg-white px-4 text-sm font-medium text-zinc-900 shadow-sm hover:bg-zinc-50 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        .pagination { @apply flex items-center justify-center gap-1; }
        .pagination li a, .pagination li span { 
            @apply inline-flex h-9 w-9 items-center justify-center rounded-md border border-zinc-200 bg-white text-xs font-semibold text-zinc-600 transition-colors hover:bg-zinc-50 hover:text-zinc-900 shadow-sm; 
        }
        .pagination li.active span { 
            @apply bg-zinc-900 border-zinc-900 text-white hover:bg-zinc-900; 
        }
        .pagination li.disabled span { 
            @apply opacity-50 cursor-not-allowed; 
        }
    </style>
@endpush

@push('scripts')
<script>
    function showLogDetail(id) {
        document.getElementById('logModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        const content = document.getElementById('logDetailContent');
        content.innerHTML = '<div class="flex items-center justify-center py-20 text-zinc-400"><i class="fas fa-circle-notch fa-spin text-xl"></i></div>';

        fetch(`{{ url('admin/logs') }}/${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const log = data.log;
                    let dataHtml = '';
                    
                    if (log.data) {
                        dataHtml = `
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Metadata / Parameters</label>
                                <div class="rounded-md bg-zinc-950 p-4">
                                    <pre class="text-[11px] font-mono leading-relaxed text-zinc-300 overflow-x-auto">${JSON.stringify(log.data, null, 4)}</pre>
                                </div>
                            </div>
                        `;
                    }

                    content.innerHTML = `
                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Pengguna</label>
                                <p class="text-sm font-bold text-zinc-900">${log.user_name}</p>
                                <p class="text-[10px] text-zinc-500 uppercase font-medium">${log.role}</p>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Waktu Kejadian</label>
                                <p class="text-sm font-bold text-zinc-900">${log.created_at}</p>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Aksi / Aktivitas</label>
                            <p class="text-sm font-bold text-zinc-900">${log.activity}</p>
                            <p class="text-xs text-zinc-500 leading-relaxed">${log.description || 'Tidak ada deskripsi'}</p>
                        </div>

                        ${dataHtml}

                        <hr class="border-zinc-100">

                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Alamat IP</label>
                                <p class="text-xs font-mono font-bold text-zinc-600">${log.ip_address}</p>
                            </div>
                            <div class="space-y-1 overflow-hidden">
                                <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">User Agent</label>
                                <p class="text-[10px] font-medium text-zinc-500 truncate" title="${log.user_agent}">${log.user_agent}</p>
                            </div>
                        </div>
                    `;
                }
            });
    }

    function closeLogModal() {
        document.getElementById('logModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
</script>
@endpush
@endsection
