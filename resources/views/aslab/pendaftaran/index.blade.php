@extends('layouts.admin')

@section('title', 'Daftar Pendaftaran Mahasiswa')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Daftar Pendaftaran Mahasiswa</h1>
                <p class="text-sm text-slate-500">Kelola dan ambil mahasiswa bimbingan Anda di sini.</p>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
            @foreach ($myPraktikums as $item)
                @php
                    $usedCount = Auth::user()->assignedStudents()->where('praktikum_id', $item->id)->count();
                    $quotaValue = $item->pivot->kuota;
                    $percent = $quotaValue > 0 ? ($usedCount / $quotaValue) * 100 : 0;
                @endphp
                <div
                    class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-xl hover:shadow-slate-200/50 transition-all group">
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-[#001f3f] group-hover:bg-[#001f3f] group-hover:text-white transition-all duration-500 shadow-inner">
                            <i class="fas fa-microscope"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-0.5">
                                {{ $item->kode_praktikum }}</p>
                            <h4 class="text-sm font-black text-slate-800 truncate uppercase tracking-tight">
                                {{ $item->nama_praktikum }}</h4>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-end justify-between">
                            <div class="flex items-baseline gap-1">
                                <span class="text-3xl font-black text-slate-900 tracking-tighter">{{ $usedCount }}</span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">/
                                    {{ $quotaValue }} Quota</span>
                            </div>
                            <span
                                class="text-[10px] font-black {{ $percent >= 100 ? 'text-rose-500' : 'text-emerald-500' }} uppercase tracking-widest">
                                {{ round($percent) }}% Full
                            </span>
                        </div>
                        <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full {{ $percent >= 100 ? 'bg-rose-500' : 'bg-[#001f3f]' }} rounded-full transition-all duration-1000 shadow-[0_0_8px_rgba(0,31,63,0.3)]"
                                style="width: {{ min($percent, 100) }}%"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Student List Container -->
        <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 md:p-8 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-base font-black text-slate-900 uppercase tracking-tight">Daftar Mahasiswa Terdaftar</h3>
                <span
                    class="px-3 py-1 bg-white border border-slate-200 rounded-full text-[10px] font-black text-slate-400 uppercase tracking-widest">Total:
                    {{ $students->count() }}</span>
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50/50 border-b border-slate-200">
                        <tr>
                            <th class="px-8 py-5 font-black text-slate-400 uppercase tracking-widest text-[10px]">Mahasiswa
                            </th>
                            <th class="px-8 py-5 font-black text-slate-400 uppercase tracking-widest text-[10px]">Praktikum
                            </th>
                            <th class="px-8 py-5 font-black text-slate-400 uppercase tracking-widest text-[10px]">Sesi</th>
                            <th class="px-8 py-5 font-black text-slate-400 uppercase tracking-widest text-[10px]">Aslab
                                Bimbingan</th>
                            <th
                                class="px-8 py-5 font-black text-slate-400 uppercase tracking-widest text-[10px] text-right">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($students as $student)
                            <tr class="hover:bg-slate-50/80 transition-all group">
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span
                                            class="font-black text-slate-900 uppercase tracking-tight">{{ $student->praktikan->user->name }}</span>
                                        <span
                                            class="text-[10px] text-slate-400 font-bold font-mono tracking-widest">{{ $student->praktikan->npm }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span
                                            class="font-bold text-slate-700">{{ $student->praktikum->nama_praktikum }}</span>
                                        <span
                                            class="text-[10px] text-slate-400 font-black uppercase tracking-widest">{{ $student->praktikum->kode_praktikum }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span
                                        class="inline-flex items-center px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-slate-100 text-slate-600 border border-slate-200">
                                        {{ $student->sesi->nama_sesi }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 font-bold">
                                    @if ($student->aslab)
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 rounded-xl bg-[#001f3f]/5 flex items-center justify-center text-[10px] font-black text-[#001f3f] border border-[#001f3f]/10 shadow-sm">
                                                {{ substr($student->aslab->name, 0, 1) }}
                                            </div>
                                            <span
                                                class="text-xs {{ $student->aslab_id === Auth::id() ? 'text-[#001f3f]' : 'text-slate-400' }}">
                                                {{ $student->aslab_id === Auth::id() ? 'ANDA' : $student->aslab->name }}
                                            </span>
                                        </div>
                                    @else
                                        <span
                                            class="text-[10px] text-rose-400 font-black uppercase tracking-widest italic flex items-center gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-400 animate-pulse"></span>
                                            Belum ada aslab
                                        </span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 text-right">
                                    @if (!$student->aslab_id)
                                        <form action="{{ route('aslab.pendaftaran.assign', $student->id) }}"
                                            method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#001f3f] text-white text-[10px] font-black rounded-xl hover:bg-[#002d5a] transition-all active:scale-95 shadow-lg shadow-[#001f3f]/20 uppercase tracking-widest">
                                                <i class="fas fa-hand-pointer"></i>
                                                Klaim Mahasiswa
                                            </button>
                                        </form>
                                    @elseif($student->aslab_id === Auth::id())
                                        <span
                                            class="inline-flex px-4 py-1.5 bg-emerald-50 text-emerald-600 text-[10px] font-black rounded-full border border-emerald-100 uppercase tracking-widest">
                                            <i class="fas fa-check-circle mr-2"></i>
                                            Milik Anda
                                        </span>
                                    @else
                                        <span class="text-[10px] text-slate-300 font-black uppercase tracking-widest">Sudah
                                            Diambil</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-8 py-20 text-center text-slate-400 italic font-bold">
                                    Belum ada mahasiswa pendaftar yang terverifikasi di praktikum Anda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="md:hidden divide-y divide-slate-100">
                @forelse($students as $student)
                    <div class="p-6 space-y-4 hover:bg-slate-50 transition-all">
                        <div class="flex justify-between items-start">
                            <div class="flex flex-col gap-1">
                                <span
                                    class="font-black text-slate-900 uppercase text-sm tracking-tight">{{ $student->praktikan->user->name }}</span>
                                <span
                                    class="text-[10px] text-slate-400 font-bold font-mono tracking-widest">{{ $student->praktikan->npm }}</span>
                            </div>
                            <span
                                class="px-3 py-1 bg-slate-100 rounded-full text-[9px] font-black text-slate-500 uppercase">
                                {{ $student->sesi->nama_sesi }}
                            </span>
                        </div>

                        <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-100 flex flex-col gap-3">
                            <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-widest">
                                <span class="text-slate-400 italic">Praktikum:</span>
                                <span class="text-slate-700">{{ $student->praktikum->nama_praktikum }}</span>
                            </div>
                            <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-widest">
                                <span class="text-slate-400 italic">Aslab:</span>
                                @if ($student->aslab)
                                    <span
                                        class="text-[#001f3f]">{{ $student->aslab_id === Auth::id() ? 'ANDA' : $student->aslab->name }}</span>
                                @else
                                    <span class="text-rose-400">Belum Ada</span>
                                @endif
                            </div>
                        </div>

                        <div class="pt-2">
                            @if (!$student->aslab_id)
                                <form action="{{ route('aslab.pendaftaran.assign', $student->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        class="w-full py-4 bg-[#001f3f] text-white text-[10px] font-black rounded-2xl hover:bg-[#002d5a] transition-all active:scale-95 shadow-lg shadow-[#001f3f]/20 uppercase tracking-widest text-center flex items-center justify-center gap-3">
                                        <i class="fas fa-hand-pointer"></i>
                                        Klaim Mahasiswa Sekarang
                                    </button>
                                </form>
                            @elseif($student->aslab_id === Auth::id())
                                <div
                                    class="w-full py-4 bg-emerald-50 text-emerald-600 text-[10px] font-black rounded-2xl border border-emerald-100 uppercase tracking-widest text-center flex items-center justify-center gap-2">
                                    <i class="fas fa-check-circle"></i>
                                    Mahasiswa Bimbingan Anda
                                </div>
                            @else
                                <div
                                    class="w-full py-4 bg-slate-50 text-slate-300 text-[10px] font-black rounded-2xl border border-slate-100 uppercase tracking-widest text-center">
                                    Sudah Diambil Aslab Lain
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-slate-400 italic font-bold text-xs uppercase tracking-widest">
                        Belum ada mahasiswa pendaftar terverifikasi
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
