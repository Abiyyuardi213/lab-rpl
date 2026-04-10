@extends('layouts.admin')

@section('title', 'QR Code Presensi: ' . $jadwal->judul_modul)

@section('content')
<div class="max-w-4xl mx-auto space-y-8 py-8 px-4">
    <!-- Header -->
    <div class="text-center space-y-4">
        <h1 class="text-3xl font-black text-slate-900 uppercase tracking-tighter sm:text-4xl">
            SCAN PRESENSI <br class="sm:hidden"> {{ $jadwal->judul_modul }}
        </h1>
        <div class="flex flex-col items-center gap-2">
            <span class="bg-[#001f3f] text-white text-[10px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest shadow-lg shadow-[#001f3f]/20">
                {{ $jadwal->praktikum->nama_praktikum }}
            </span>
            <p class="text-slate-500 font-bold uppercase tracking-widest text-[11px]">
                {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('l, d F Y') }}
                <span class="mx-2">|</span>
                {{ substr($jadwal->waktu_mulai, 0, 5) }} - {{ substr($jadwal->waktu_selesai, 0, 5) }} WIB
            </p>
        </div>
    </div>

    <!-- QR Code Section -->
    <div class="flex flex-col items-center">
        <div class="relative p-8 bg-white rounded-[2.5rem] shadow-2xl border border-slate-100 group transition-all hover:scale-[1.02] duration-500">
            <!-- Decorative Corners -->
            <div class="absolute top-0 left-0 w-12 h-12 border-t-4 border-l-4 border-[#001f3f] rounded-tl-[2.5rem]"></div>
            <div class="absolute top-0 right-0 w-12 h-12 border-t-4 border-r-4 border-[#001f3f] rounded-tr-[2.5rem]"></div>
            <div class="absolute bottom-0 left-0 w-12 h-12 border-b-4 border-l-4 border-[#001f3f] rounded-bl-[2.5rem]"></div>
            <div class="absolute bottom-0 right-0 w-12 h-12 border-b-4 border-r-4 border-[#001f3f] rounded-br-[2.5rem]"></div>
            
            <div class="bg-white p-2 rounded-2xl border border-slate-50">
                {!! $qrCode !!}
            </div>
            
            <div class="absolute -bottom-6 left-1/2 -translate-x-1/2 bg-white px-6 py-2 rounded-full shadow-lg border border-slate-100 whitespace-nowrap">
                <p class="text-xs font-black text-[#001f3f] uppercase tracking-[0.2em] flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Scan kode ini untuk presensi
                </p>
            </div>
        </div>
    </div>

    <!-- Instructions & Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-12 bg-slate-50 p-6 rounded-3xl border border-slate-200 border-dashed print:hidden">
        <div class="space-y-4">
            <h3 class="font-black text-slate-900 uppercase tracking-tight flex items-center gap-2">
                <i class="fas fa-info-circle text-[#001f3f]"></i>
                Instruksi Praktikan
            </h3>
            <ul class="space-y-2 text-xs font-bold text-slate-600 uppercase tracking-wide leading-relaxed">
                <li class="flex gap-3">
                    <span class="w-5 h-5 rounded-full bg-[#001f3f] text-white flex items-center justify-center text-[10px] flex-shrink-0">1</span>
                    Buka Aplikasi Lab-RPL dan Login
                </li>
                <li class="flex gap-3">
                    <span class="w-5 h-5 rounded-full bg-[#001f3f] text-white flex items-center justify-center text-[10px] flex-shrink-0">2</span>
                    Gunakan scanner pada ponsel Anda
                </li>
                <li class="flex gap-3">
                    <span class="w-5 h-5 rounded-full bg-[#001f3f] text-white flex items-center justify-center text-[10px] flex-shrink-0">3</span>
                    Arahkan ke QR Code di atas
                </li>
                <li class="flex gap-3">
                    <span class="w-5 h-5 rounded-full bg-[#001f3f] text-white flex items-center justify-center text-[10px] flex-shrink-0">4</span>
                    Presensi akan tercatat secara otomatis
                </li>
            </ul>
        </div>
        <div class="flex flex-col justify-center gap-3">
            <a href="{{ route('presensi.download-jadwal-pdf', $jadwal->id) }}" 
               class="w-full h-12 bg-emerald-600 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-emerald-700 transition-all flex items-center justify-center gap-3 shadow-lg shadow-emerald-600/10 translate-y-0 active:translate-y-1">
                <i class="fas fa-file-pdf"></i>
                Download PDF QR
            </a>
            <button onclick="window.print()" class="w-full h-12 bg-[#001f3f] text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-[#002d5a] transition-all flex items-center justify-center gap-3 shadow-lg shadow-[#001f3f]/10 translate-y-0 active:translate-y-1">
                <i class="fas fa-print"></i>
                Cetak Langsung
            </button>
            <a href="{{ Auth::user()->role->name === 'Admin' ? route('admin.praktikum.show', $jadwal->praktikum_id) : route('aslab.dashboard') }}" class="w-full h-12 bg-white border border-slate-200 text-slate-600 text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-slate-100 transition-all flex items-center justify-center gap-3 translate-y-0 active:translate-y-1">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>
    <!-- Attendance List -->
    <div class="mt-12 space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="font-black text-slate-900 uppercase tracking-tight flex items-center gap-3">
                <i class="fas fa-user-check text-emerald-600"></i>
                Daftar Kehadiran ({{ $jadwal->presensis->count() }})
            </h3>
            <div class="flex items-center gap-3">
                <span id="refresh-timer" class="text-[9px] font-bold text-slate-400 uppercase tracking-widest hidden sm:inline-block">Auto-refresh in 30s</span>
                <button onclick="openManualModal()" class="text-[10px] font-black uppercase tracking-widest text-emerald-600 bg-emerald-50 border border-emerald-100 px-4 py-2 rounded-xl hover:bg-emerald-100 transition-all flex items-center gap-2">
                    <i class="fas fa-plus"></i> Input Manual
                </button>
                <button onclick="window.location.reload()" class="text-[10px] font-black uppercase tracking-widest text-[#001f3f] bg-white border border-slate-200 px-4 py-2 rounded-xl hover:bg-slate-50 transition-all flex items-center gap-2">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </div>

        <!-- Manual Attendance Modal -->
        <div id="manualModal" class="fixed inset-0 z-[60] hidden">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeManualModal()"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg p-4">
                <div class="bg-white rounded-[2.5rem] shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[80vh]">
                    <div class="p-8 border-b border-slate-50 flex items-center justify-between bg-slate-50/50">
                        <div>
                            <h4 class="text-xl font-black text-slate-900 uppercase tracking-tight">Input Manual</h4>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pilih praktikan untuk presensi</p>
                        </div>
                        <button onclick="closeManualModal()" class="w-10 h-10 rounded-full bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-all">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="p-6">
                        <div class="relative">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                            <input type="text" id="studentSearch" onkeyup="filterStudents()" placeholder="Cari Nama atau NPM..." 
                                class="w-full pl-12 pr-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold placeholder:text-slate-300 focus:ring-2 focus:ring-[#001f3f]/10 transition-all">
                        </div>
                    </div>

                    <div class="flex-grow overflow-y-auto px-6 pb-8">
                        <div id="studentList" class="space-y-2">
                            @forelse($notPresentStudents as $pendaftaran)
                                <div class="student-item group flex items-center justify-between p-4 rounded-3xl border border-slate-50 hover:border-emerald-100 hover:bg-emerald-50/30 transition-all cursor-pointer"
                                     data-name="{{ strtolower($pendaftaran->praktikan->user->name) }}"
                                     data-npm="{{ $pendaftaran->praktikan->npm }}">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-2xl bg-slate-100 group-hover:bg-emerald-100 flex items-center justify-center text-[#001f3f] font-black text-xs transition-colors">
                                            {{ substr($pendaftaran->praktikan->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ $pendaftaran->praktikan->user->name }}</p>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $pendaftaran->praktikan->npm }}</p>
                                        </div>
                                    </div>
                                    <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button onclick="submitManual('{{ $pendaftaran->id }}', 'hadir')" class="px-4 py-2 bg-emerald-600 text-white text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-emerald-700 shadow-lg shadow-emerald-600/20">
                                            Hadir
                                        </button>
                                        <button onclick="submitManual('{{ $pendaftaran->id }}', 'terlambat')" class="px-4 py-2 bg-amber-500 text-white text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-amber-600 shadow-lg shadow-amber-500/20">
                                            Telat
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="py-12 text-center">
                                    <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest text-[10px]">Semua praktikan sudah presensi</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($jadwal->presensis->count() > 0)
            <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">#</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">Praktikan</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">NPM</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">Waktu Scan</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">Status</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($jadwal->presensis->sortByDesc('created_at') as $index => $presensi)
                                <tr class="hover:bg-slate-50/50 transition-colors group">
                                    <td class="px-6 py-4 text-xs font-bold text-slate-400">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-[#001f3f] font-black text-[10px] border border-slate-200">
                                                {{ substr($presensi->pendaftaran->praktikan->user->name, 0, 1) }}
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-xs font-black text-slate-900 uppercase tracking-tight">{{ $presensi->pendaftaran->praktikan->user->name }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">
                                        {{ $presensi->pendaftaran->praktikan->npm }}
                                    </td>
                                    <td class="px-6 py-4 text-xs font-bold text-slate-500">
                                        {{ \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i:s') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($presensi->status == 'hadir')
                                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[9px] font-black uppercase tracking-widest rounded-full border border-emerald-100">
                                                Tepat Waktu
                                            </span>
                                        @elseif($presensi->status == 'terlambat')
                                            <span class="px-3 py-1 bg-amber-50 text-amber-600 text-[9px] font-black uppercase tracking-widest rounded-full border border-amber-100">
                                                Terlambat
                                            </span>
                                        @else
                                            <span class="px-3 py-1 bg-slate-50 text-slate-600 text-[9px] font-black uppercase tracking-widest rounded-full border border-slate-100">
                                                {{ $presensi->status }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button onclick="deletePresensi('{{ $presensi->id }}', '{{ $presensi->pendaftaran->praktikan->user->name }}')" 
                                                class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 transition-all flex items-center justify-center opacity-0 group-hover:opacity-100">
                                            <i class="fas fa-trash-alt text-[10px]"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-200 p-12 text-center">
                <div class="w-16 h-16 bg-white rounded-2xl shadow-sm border border-slate-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-clock text-slate-300 text-2xl"></i>
                </div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Belum ada praktikan yang melakukan presensi</p>
            </div>
        @endif
    </div>
</div>

<style>
@media print {
    nav, aside, footer, header, .print-hidden, button, a {
        display: none !important;
    }
    body {
        background: white !important;
    }
    .min-h-screen {
        min-height: auto !important;
    }
    .shadow-2xl, .shadow-lg {
        shadow: none !important;
    }
}
</style>
@push('scripts')
<script>
    // Auto refresh every 30 seconds to update attendance list
    let seconds = 30;
    const updateTimer = () => {
        seconds--;
        const timerEl = document.getElementById('refresh-timer');
        if (timerEl) timerEl.innerText = `Auto-refresh in ${seconds}s`;
        
        if (seconds <= 0) {
            window.location.reload();
        }
    }
    setInterval(updateTimer, 1000);

    function openManualModal() {
        document.getElementById('manualModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeManualModal() {
        document.getElementById('manualModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function filterStudents() {
        const query = document.getElementById('studentSearch').value.toLowerCase();
        const items = document.querySelectorAll('.student-item');
        
        items.forEach(item => {
            const name = item.getAttribute('data-name');
            const npm = item.getAttribute('data-npm').toLowerCase();
            
            if (name.includes(query) || npm.includes(query)) {
                item.classList.remove('hidden');
                item.classList.add('flex');
            } else {
                item.classList.add('hidden');
                item.classList.remove('flex');
            }
        });
    }

    function submitManual(pendaftaranId, status) {
        Swal.fire({
            title: 'Konfirmasi Presensi',
            text: `Tandai praktikan ini sebagai ${status}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: status === 'hadir' ? '#059669' : '#d97706',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Tandai!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route("presensi.manual") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        jadwal_id: '{{ $jadwal->id }}',
                        pendaftaran_id: pendaftaranId,
                        status: status
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Gagal', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                });
            }
        });
    }

    function deletePresensi(presensiId, name) {
        Swal.fire({
            title: 'Batalkan Presensi?',
            text: `Apakah Anda yakin ingin membatalkan kehadiran ${name}? Tindakan ini tidak dapat dibatalkan.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Kembali'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`{{ url('presensi') }}/${presensiId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Dibatalkan',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Gagal', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                });
            }
        });
    }
</script>
@endpush
@endsection
