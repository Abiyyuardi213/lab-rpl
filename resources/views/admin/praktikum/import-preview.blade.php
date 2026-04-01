<!-- Header -->
<div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between bg-zinc-50/50">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-[#001f3f] flex items-center justify-center text-white shadow-lg shadow-[#001f3f]/20">
            <i class="fas fa-file-import text-sm text-emerald-400"></i>
        </div>
        <div>
            <h3 class="text-base font-black text-[#001f3f] leading-none">Review Import Praktikan</h3>
            <p class="text-[10px] text-zinc-400 font-medium mt-1">Konfirmasi perubahan data sebelum disimpan.</p>
        </div>
    </div>
    <button type="button" onclick="closeImportModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-zinc-100 text-zinc-400 transition-colors">
        <i class="fas fa-times"></i>
    </button>
</div>

<div class="p-6">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-blue-50/50 p-4 rounded-2xl border border-blue-100 flex items-center gap-4">
            <div class="h-10 w-10 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-md shadow-blue-600/20">
                <i class="fas fa-users-cog text-sm"></i>
            </div>
            <div>
                <p class="text-[9px] font-black text-blue-400 uppercase tracking-widest">Total Baris</p>
                <p class="text-lg font-black text-[#001f3f]">{{ count($previewData) }} Data</p>
            </div>
        </div>
        <div class="bg-emerald-50/50 p-4 rounded-2xl border border-emerald-100 flex items-center gap-4">
            <div class="h-10 w-10 rounded-xl bg-emerald-600 flex items-center justify-center text-white shadow-md shadow-emerald-600/20">
                <i class="fas fa-check-double text-sm"></i>
            </div>
            <div>
                <p class="text-[9px] font-black text-emerald-400 uppercase tracking-widest">Status</p>
                <p class="text-lg font-black text-[#001f3f]">Siap Import</p>
            </div>
        </div>
    </div>

    <!-- Preview Table -->
    <div class="bg-white rounded-2xl border border-zinc-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto max-h-[400px] overflow-y-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead class="sticky top-0 bg-[#fafafa] z-10 border-b border-zinc-100">
                    <tr>
                        <th class="px-5 py-3 text-[10px] font-black text-zinc-400 uppercase tracking-wider">Mahasiswa</th>
                        <th class="px-5 py-3 text-[10px] font-black text-zinc-400 uppercase tracking-wider">Dosen Pengampu</th>
                        <th class="px-5 py-3 text-[10px] font-black text-zinc-400 uppercase tracking-wider">Sesi Praktikum</th>
                        <th class="px-5 py-3 text-[10px] font-black text-zinc-400 uppercase tracking-wider">Aslab Bimbingan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-50">
                    @foreach ($previewData as $item)
                    <tr class="hover:bg-zinc-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-lg bg-[#001f3f]/5 flex items-center justify-center text-[#001f3f] font-black text-[10px] uppercase border border-[#001f3f]/10">
                                    {{ substr($item['nama'], 0, 2) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-zinc-900 truncate max-w-[150px]">{{ $item['nama'] }}</p>
                                    <p class="text-[9px] text-[#001f3f] font-black font-mono tracking-wider">{{ $item['npm'] }}</p>
                                </div>
                            </div>
                        </td>
                        
                        <!-- Dosen Column -->
                        <td class="px-5 py-4">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-1">
                                    <span class="text-[9px] font-bold text-zinc-300 uppercase tracking-tight">Old:</span>
                                    <span class="text-[9px] text-zinc-400 truncate max-w-[100px]">{{ $item['old']['dosen'] }}</span>
                                </div>
                                @if($item['new']['dosen_name'] && $item['new']['dosen_name'] !== $item['old']['dosen'])
                                    <div class="flex flex-col gap-0.5 mt-0.5">
                                        <div class="flex items-center gap-1">
                                            <i class="fas fa-chevron-down text-[7px] text-emerald-400 ml-1"></i>
                                            <span class="text-[9px] font-bold text-emerald-500 uppercase tracking-tight">New:</span>
                                        </div>
                                        <span class="inline-flex items-center text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100 transition-all">
                                            {{ $item['new']['dosen_name'] }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-[10px] font-medium text-zinc-300 italic">—</span>
                                @endif
                            </div>
                        </td>

                        <!-- Sesi Column -->
                        <td class="px-5 py-4">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-1">
                                    <span class="text-[9px] font-bold text-zinc-300 uppercase tracking-tight">Old:</span>
                                    <span class="text-[9px] text-zinc-400 truncate max-w-[100px]">{{ $item['old']['sesi'] }}</span>
                                </div>
                                @if($item['new']['sesi_name'] && $item['new']['sesi_name'] !== $item['old']['sesi'])
                                    <div class="flex flex-col gap-0.5 mt-0.5">
                                        <div class="flex items-center gap-1">
                                            <i class="fas fa-chevron-down text-[7px] text-blue-400 ml-1"></i>
                                            <span class="text-[9px] font-bold text-blue-500 uppercase tracking-tight">New:</span>
                                        </div>
                                        <span class="inline-flex items-center text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-100 transition-all">
                                            {{ $item['new']['sesi_name'] }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-[10px] font-medium text-zinc-300 italic">—</span>
                                @endif
                            </div>
                        </td>

                        <!-- Aslab Column -->
                        <td class="px-5 py-4">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-1">
                                    <span class="text-[9px] font-bold text-zinc-300 uppercase tracking-tight">Old:</span>
                                    <span class="text-[9px] text-zinc-400 truncate max-w-[100px]">{{ $item['old']['aslab'] }}</span>
                                </div>
                                @if($item['new']['aslab_name'] && $item['new']['aslab_name'] !== $item['old']['aslab'])
                                    <div class="flex flex-col gap-0.5 mt-0.5">
                                        <div class="flex items-center gap-1">
                                            <i class="fas fa-chevron-down text-[7px] text-amber-400 ml-1"></i>
                                            <span class="text-[9px] font-bold text-amber-500 uppercase tracking-tight">New:</span>
                                        </div>
                                        <div class="flex flex-col gap-1">
                                            <span class="inline-flex items-center text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-100 transition-all">
                                                {{ $item['new']['aslab_name'] }}
                                            </span>
                                            @if($item['new']['link_grup'])
                                                <span class="text-[8px] text-emerald-600 font-bold flex items-center gap-1 pl-1">
                                                    <i class="fab fa-whatsapp text-[10px]"></i> + Link Grup
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="text-[10px] font-medium text-zinc-300 italic">—</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="px-6 py-4 bg-zinc-50 border-t border-zinc-100 flex items-center justify-between">
    <div class="flex items-center gap-2">
        <div class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></div>
        <span class="text-[10px] font-black text-zinc-600 uppercase tracking-wider">Validasi Berhasil</span>
    </div>
    
    <div class="flex items-center gap-3">
        <button type="button" onclick="closeImportModal()" 
            class="px-4 py-2 text-zinc-500 text-[10px] font-black uppercase tracking-widest hover:text-zinc-700 transition-colors">
            Batalkan
        </button>
        <form action="{{ route('admin.praktikum.import-confirm', $praktikum->id) }}" method="POST" id="confirmImportForm" onsubmit="this.querySelector('button[type=submit]').disabled = true; this.querySelector('button[type=submit]').innerHTML = '<i class=\'fas fa-spinner fa-spin mr-2\'></i> Memproses...';">
            @csrf
            <input type="hidden" name="data" value="{{ json_encode($previewData) }}">
            <button type="submit" 
                class="px-6 py-2.5 bg-[#001f3f] text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-[#001f3f]/20 hover:bg-[#002d5a] hover:-translate-y-0.5 transition-all flex items-center gap-2">
                <i class="fas fa-check-circle text-emerald-400"></i>
                Terapkan Sekarang
            </button>
        </form>
    </div>
</div>

<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 5px;
    height: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #cbd5e1;
}
</style>
