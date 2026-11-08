@extends('layouts.admin')

@section('title', 'Detail Penilaian - ' . $jadwal->judul_modul)

@section('content')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 uppercase">{{ $jadwal->judul_modul }}</h1>
                    @if($jadwal->sesi)
                        <span class="px-2 py-0.5 rounded text-xs font-bold uppercase bg-zinc-900 text-white shadow-xs">
                            {{ $jadwal->sesi->nama_sesi }}
                        </span>
                    @endif
                </div>
                <p class="text-sm text-zinc-500 font-medium">
                    {{ $jadwal->praktikum->nama_praktikum }} • 
                    <span class="text-zinc-700">{{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('l, d F Y') }}</span> • 
                    <span>{{ substr($jadwal->waktu_mulai, 0, 5) }} - {{ substr($jadwal->waktu_selesai, 0, 5) }} WIB</span>
                    @if($jadwal->ruangan)
                        • <span>Ruang: {{ $jadwal->ruangan }}</span>
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-zinc-500 bg-white px-3 py-1.5 rounded-lg border border-zinc-200 shadow-sm w-fit">
                <a href="{{ route('admin.penilaian.index') }}" class="hover:text-zinc-900 transition-colors">Penilaian</a>
                <span>/</span>
                <a href="{{ route('admin.penilaian.praktikum', $jadwal->praktikum_id) }}" class="hover:text-zinc-900 transition-colors">{{ $jadwal->praktikum->kode_praktikum }}</a>
                <span>/</span>
                <span class="text-zinc-900 font-semibold">Detail Modul</span>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm flex items-center gap-3">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Summary Cards -->
        @php
            $totalMhs = $pendaftarans->count();
            $totalHadir = $pendaftarans->filter(fn($p) => $p->presensis->isNotEmpty())->count();
            $totalDinilai = $pendaftarans->filter(fn($p) => $p->presensis->first()?->penilaian !== null)->count();
            $persenDinilai = $totalMhs > 0 ? round(($totalDinilai / $totalMhs) * 100) : 0;
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-4 bg-white border border-zinc-200 rounded-xl shadow-xs">
                <p class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Total Praktikan Terdaftar</p>
                <p class="text-2xl font-black text-zinc-900 mt-1">{{ $totalMhs }} <span class="text-xs font-normal text-zinc-400">Mahasiswa</span></p>
            </div>
            <div class="p-4 bg-white border border-zinc-200 rounded-xl shadow-xs">
                <p class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Presensi Kehadiran</p>
                <p class="text-2xl font-black text-zinc-900 mt-1">{{ $totalHadir }} <span class="text-xs font-normal text-zinc-400">/ {{ $totalMhs }} Hadir</span></p>
            </div>
            <div class="p-4 bg-white border border-zinc-200 rounded-xl shadow-xs">
                <p class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Sudah Dinilai</p>
                <div class="flex items-center justify-between mt-1">
                    <p class="text-2xl font-black text-zinc-900">{{ $totalDinilai }} <span class="text-xs font-normal text-zinc-400">({{ $persenDinilai }}%)</span></p>
                    <div class="w-16 bg-zinc-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-emerald-600 h-2 rounded-full" style="width: {{ $persenDinilai }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <div class="rounded-xl border border-zinc-200 bg-white text-zinc-950 shadow-sm overflow-hidden">
            <div class="p-4 sm:p-6 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-zinc-100 bg-zinc-50/50">
                <div class="flex items-center gap-2 flex-1">
                    <div class="relative max-w-sm w-full">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 text-xs"></i>
                        <input type="text" id="adminSearch" placeholder="Cari nama atau NPM praktikan..."
                            class="flex h-9 w-full rounded-md border border-zinc-200 bg-white px-3 py-1 pl-9 text-sm shadow-sm transition-colors placeholder:text-zinc-400 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-zinc-950">
                    </div>
                </div>
                <div class="text-xs font-semibold text-zinc-500">
                    Menampilkan seluruh praktikan yang terdaftar pada sesi ini.
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="adminTable" class="w-full text-sm text-left">
                    <thead class="bg-zinc-50 border-b border-zinc-100 text-zinc-500 font-medium h-10">
                        <tr>
                            <th class="px-6 align-middle font-medium text-zinc-500 uppercase text-[10px] tracking-wider text-center w-16">Foto</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 uppercase text-[10px] tracking-wider">NPM</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 uppercase text-[10px] tracking-wider">Nama Praktikan</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 uppercase text-[10px] tracking-wider">Sesi Terdaftar</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 uppercase text-[10px] tracking-wider text-center">Status Presensi</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 uppercase text-[10px] tracking-wider text-center">Nilai</th>
                            <th class="px-6 align-middle font-medium text-zinc-500 uppercase text-[10px] tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse($pendaftarans as $pendaftaran)
                            @php
                                $praktikan = $pendaftaran->praktikan;
                                $user = $praktikan?->user;
                                $sesi = $pendaftaran->sesi;
                                $presensi = $pendaftaran->presensis->first();
                                $nilai = $presensi?->penilaian;

                                if ($presensi) {
                                    $statusClass = match($presensi->status) {
                                        'hadir' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'terlambat' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'alfa' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        default => 'bg-zinc-100 text-zinc-600 border-zinc-200',
                                    };
                                    $statusLabel = $presensi->status === 'alfa' ? 'Tidak Hadir' : ucfirst($presensi->status);
                                    if ($presensi->jam_masuk) {
                                        $statusSub = \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i') . ' WIB';
                                    } else {
                                        $statusSub = null;
                                    }
                                } else {
                                    $statusClass = 'bg-zinc-100 text-zinc-500 border-zinc-200';
                                    $statusLabel = 'Belum Presensi';
                                    $statusSub = 'Belum scan QR';
                                }
                            @endphp
                            <tr class="hover:bg-zinc-50/50 transition-colors">
                                <td class="px-6 py-4 text-center">
                                    <img src="{{ $user?->profile_picture ? asset('storage/' . $user->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($user?->name ?? 'User') . '&background=random' }}" 
                                         alt="Avatar"
                                         class="w-10 h-10 rounded-lg object-cover border border-zinc-200 shadow-sm mx-auto">
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs text-zinc-900 font-mono font-bold">{{ $praktikan?->npm ?? '-' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-zinc-900 block leading-tight">{{ $user?->name ?? 'Mahasiswa' }}</span>
                                    @if($pendaftaran->asal_kelas_mata_kuliah)
                                        <span class="text-[10px] text-zinc-400 font-medium">Kelas: {{ $pendaftaran->asal_kelas_mata_kuliah }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-[11px] font-bold text-zinc-800 leading-none">{{ $sesi?->nama_sesi ?? 'Lintas Sesi' }}</span>
                                        @if($sesi)
                                            <span class="text-[10px] text-zinc-400 font-medium mt-1">{{ substr($sesi->jam_mulai, 0, 5) }} - {{ substr($sesi->jam_selesai, 0, 5) }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex flex-col items-center">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border {{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                        @if($statusSub)
                                            <span class="text-[9px] text-zinc-400 font-medium mt-0.5">{{ $statusSub }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($nilai)
                                        <div class="flex flex-col items-center">
                                            <span class="text-base font-black text-zinc-900">{{ $nilai->nilai }}</span>
                                            <span class="text-[8px] font-bold uppercase tracking-tight text-zinc-400">
                                                {{ $nilai->aslab_id ? 'Oleh Aslab' : 'Oleh Admin' }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-xs text-zinc-300 italic font-medium">Belum Dinilai</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button type="button" 
                                            data-presensi-id="{{ $presensi?->id ?? '' }}"
                                            data-pendaftaran-id="{{ $pendaftaran->id }}"
                                            data-jadwal-id="{{ $jadwal->id }}"
                                            data-name="{{ $user?->name ?? 'Mahasiswa' }}"
                                            data-npm="{{ $praktikan?->npm ?? '-' }}"
                                            data-nilai="{{ $nilai?->nilai ?? '' }}"
                                            data-catatan="{{ $nilai?->catatan ?? '' }}"
                                            onclick="initGrading(this)"
                                            class="inline-flex h-8 items-center justify-center rounded-md border border-zinc-200 bg-white px-3 text-xs font-medium shadow-xs hover:bg-zinc-50 hover:text-zinc-900 transition-colors">
                                        <i class="fas fa-edit mr-1.5 text-[10px]"></i>
                                        {{ $nilai ? 'Ubah Nilai' : 'Beri Nilai' }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center text-zinc-400 font-medium italic">
                                    Tidak ada praktikan terdaftar pada sesi jadwal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Admin Grading Modal -->
    <div id="adminModal" class="fixed inset-0 z-[100] hidden overflow-y-auto outline-none">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeAdminModal()"></div>
            
            <div class="relative bg-white w-full max-w-lg rounded-xl shadow-2xl transition-all transform p-6">
                <div class="flex flex-col space-y-1.5 text-left mb-6">
                    <h3 class="text-lg font-semibold leading-none tracking-tight text-zinc-900">Input Nilai Praktikum</h3>
                    <p class="text-xs text-zinc-500" id="adminStudentInfo"></p>
                </div>

                <form action="{{ route('admin.penilaian.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="presensi_id" id="adminPresensiId">
                    <input type="hidden" name="pendaftaran_id" id="adminPendaftaranId">
                    <input type="hidden" name="jadwal_id" id="adminJadwalId">
                    
                    <div class="space-y-2 text-left">
                        <label class="text-sm font-medium leading-none text-zinc-700">Skor (0-100)</label>
                        <input type="number" name="nilai" id="adminScoreInput" required min="0" max="100"
                               placeholder="Contoh: 85"
                               class="flex h-10 w-full rounded-md border border-zinc-200 bg-white px-3 py-2 text-sm placeholder:text-zinc-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-950 focus-visible:ring-offset-2">
                    </div>

                    <div class="space-y-2 text-left">
                        <label class="text-sm font-medium leading-none text-zinc-700">Catatan Penilaian (Opsional)</label>
                        <textarea name="catatan" id="adminNotesInput" rows="3"
                                  placeholder="Tambahkan feedback atau catatan tugas praktikan..."
                                  class="flex min-h-[80px] w-full rounded-md border border-zinc-200 bg-white px-3 py-2 text-sm placeholder:text-zinc-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-950 focus-visible:ring-offset-2"></textarea>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2 gap-2 pt-2">
                        <button type="button" onclick="closeAdminModal()"
                                class="inline-flex h-10 items-center justify-center rounded-md border border-zinc-200 bg-white px-4 py-2 text-sm font-medium transition-colors hover:bg-zinc-100 hover:text-zinc-900">
                            Batal
                        </button>
                        <button type="submit"
                                class="inline-flex h-10 items-center justify-center rounded-md bg-zinc-900 px-4 py-2 text-sm font-medium text-zinc-50 shadow hover:bg-zinc-800 transition-colors">
                            Simpan Nilai
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function initGrading(btn) {
            const presensiId = btn.getAttribute('data-presensi-id') || '';
            const pendaftaranId = btn.getAttribute('data-pendaftaran-id') || '';
            const jadwalId = btn.getAttribute('data-jadwal-id') || '';
            const name = btn.getAttribute('data-name');
            const npm = btn.getAttribute('data-npm') || '';
            const nilai = btn.getAttribute('data-nilai') || '';
            const catatan = btn.getAttribute('data-catatan') || '';

            document.getElementById('adminPresensiId').value = presensiId;
            document.getElementById('adminPendaftaranId').value = pendaftaranId;
            document.getElementById('adminJadwalId').value = jadwalId;
            document.getElementById('adminStudentInfo').textContent = name + ' (' + npm + ')';
            document.getElementById('adminScoreInput').value = nilai;
            document.getElementById('adminNotesInput').value = catatan;
            
            const modal = document.getElementById('adminModal');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeAdminModal() {
            const modal = document.getElementById('adminModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Search Filter by NPM and Nama
        document.getElementById('adminSearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#adminTable tbody tr');
            
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    </script>
@endsection
