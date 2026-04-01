@extends('layouts.admin')

@section('content')
<div class="h-full bg-[#f8fafc] p-4 lg:p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-black text-[#001f3f]">Review Import Praktikan</h1>
                <p class="text-sm text-zinc-500 mt-1">Silakan periksa perubahan data sebelum diterapkan ke database.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.praktikum.students', $praktikum->id) }}" 
                    class="px-5 py-2.5 bg-white border border-zinc-200 text-zinc-600 rounded-xl text-xs font-bold hover:bg-zinc-50 transition-all flex items-center gap-2">
                    <i class="fas fa-times"></i>
                    Batalkan
                </a>
                
                <form action="{{ route('admin.praktikum.import-confirm', $praktikum->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="data" value="{{ json_encode($previewData) }}">
                    <button type="submit" 
                        class="px-8 py-2.5 bg-[#001f3f] text-white rounded-xl text-xs font-black shadow-lg shadow-[#001f3f]/20 hover:bg-[#002d5a] hover:-translate-y-0.5 transition-all flex items-center gap-2">
                        <i class="fas fa-check-circle text-emerald-400"></i>
                        Konfirmasi & Terapkan
                    </button>
                </form>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl border border-zinc-100 shadow-sm flex items-center gap-4">
                <div class="h-12 w-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                    <i class="fas fa-users-cog text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Total Data</p>
                    <p class="text-xl font-black text-[#001f3f]">{{ count($previewData) }} Baris</p>
                </div>
            </div>
            <!-- More stats can be added here -->
        </div>

        <!-- Preview Table -->
        <div class="bg-white rounded-2xl shadow-xl shadow-zinc-200/50 border border-zinc-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#fafafa] border-b border-zinc-100">
                            <th class="px-6 py-4 text-[11px] font-black text-zinc-400 uppercase tracking-wider">Mahasiswa</th>
                            <th class="px-6 py-4 text-[11px] font-black text-zinc-400 uppercase tracking-wider">Dosen Pengampu</th>
                            <th class="px-6 py-4 text-[11px] font-black text-zinc-400 uppercase tracking-wider">Sesi Praktikum</th>
                            <th class="px-6 py-4 text-[11px] font-black text-zinc-400 uppercase tracking-wider">Aslab Bimbingan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-50">
                        @foreach ($previewData as $item)
                        <tr class="hover:bg-zinc-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-full bg-[#001f3f]/5 flex items-center justify-center text-[#001f3f] font-bold text-xs uppercase">
                                        {{ substr($item['nama'], 0, 2) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-zinc-900">{{ $item['nama'] }}</p>
                                        <p class="text-[11px] text-zinc-500">{{ $item['npm'] }}</p>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Dosen Column -->
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-[10px] text-zinc-400">Lama: {{ $item['old']['dosen'] }}</span>
                                    @if($item['new']['dosen_name'] && $item['new']['dosen_name'] !== $item['old']['dosen'])
                                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg w-fit transition-all hover:scale-105">
                                            <i class="fas fa-arrow-right text-[10px]"></i>
                                            {{ $item['new']['dosen_name'] }}
                                        </span>
                                    @else
                                        <span class="text-xs font-semibold text-zinc-400 italic">Tidak Berubah</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Sesi Column -->
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-[10px] text-zinc-400">Lama: {{ $item['old']['sesi'] }}</span>
                                    @if($item['new']['sesi_name'] && $item['new']['sesi_name'] !== $item['old']['sesi'])
                                        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg w-fit transition-all hover:scale-105">
                                            <i class="fas fa-arrow-right text-[10px]"></i>
                                            {{ $item['new']['sesi_name'] }}
                                        </span>
                                    @else
                                        <span class="text-xs font-semibold text-zinc-400 italic">Tidak Berubah</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Aslab Column -->
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-[10px] text-zinc-400">Lama: {{ $item['old']['aslab'] }}</span>
                                    @if($item['new']['aslab_name'] && $item['new']['aslab_name'] !== $item['old']['aslab'])
                                        <div class="flex flex-col gap-1">
                                            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-lg w-fit transition-all hover:scale-105">
                                                <i class="fas fa-arrow-right text-[10px]"></i>
                                                {{ $item['new']['aslab_name'] }}
                                            </span>
                                            @if($item['new']['link_grup'])
                                                <span class="text-[9px] text-emerald-600 flex items-center gap-1">
                                                    <i class="fab fa-whatsapp"></i> Update Link Grup
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs font-semibold text-zinc-400 italic">Tidak Berubah</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 bg-[#fafafa] border-t border-zinc-100 flex items-center justify-between">
                <p class="text-xs text-zinc-500 font-medium">Menampilkan {{ count($previewData) }} baris data yang akan diperbarui.</p>
                <div class="flex items-center gap-2">
                    <div class="h-2 w-2 rounded-full bg-emerald-500"></div>
                    <span class="text-[10px] font-bold text-zinc-600 uppercase tracking-wider">Semua Data Valid & Siap Diproses</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
