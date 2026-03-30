@extends('layouts.admin')

@section('title', 'Pendaftaran Praktikum')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-start justify-between">
            <div class="space-y-1">
                <a href="{{ route('praktikan.dashboard') }}"
                    class="inline-flex items-center gap-2 text-xs font-bold text-zinc-500 hover:text-zinc-900 transition-colors mb-2">
                    <i class="fas fa-arrow-left"></i>
                    Batal dan Kembali
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Form Pendaftaran Praktikum</h1>
                <p class="text-sm text-zinc-500">Silakan isi data diri Anda dengan benar untuk mendaftar praktikum
                    <strong>{{ $praktikum->nama_praktikum }}</strong>.
                </p>
            </div>
        </div>

        <form action="{{ route('praktikan.pendaftaran.store') }}" method="POST" enctype="multipart/form-data"
            class="space-y-6">
            @csrf
            <input type="hidden" name="praktikum_id" value="{{ $praktikum->id }}">

            <!-- Profile Info (Static) -->
            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-bold text-zinc-900 uppercase tracking-widest flex items-center gap-2 mb-6">
                    <i class="fas fa-user-circle text-[#001f3f]"></i>
                    Informasi Dasar
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Nama Lengkap</label>
                        <p
                            class="text-sm font-semibold text-zinc-900 bg-zinc-50 px-4 py-2.5 rounded-xl border border-zinc-100 italic">
                            {{ Auth::user()->name }}
                        </p>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">NPM</label>
                        <p
                            class="text-sm font-semibold text-zinc-900 bg-zinc-50 px-4 py-2.5 rounded-xl border border-zinc-100 italic">
                            {{ Auth::user()->npm }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Detailed Info -->
            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-bold text-zinc-900 uppercase tracking-widest flex items-center gap-2 mb-6">
                    <i class="fas fa-edit text-[#001f3f]"></i>
                    Data Perkuliahan
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Nomor Telepon
                            (WhatsApp)</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp') }}" placeholder="Contoh: 081234567890"
                            required
                            class="w-full rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm focus:outline-none focus:ring-4 focus:ring-[#001f3f]/5 focus:border-[#001f3f] transition-all">
                        @error('no_hp')
                            <p class="text-[10px] text-rose-500 font-bold mt-1 uppercase">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Asal Kelas
                            Kuliah (Pagi/Malam)</label>
                        <select name="kelas" required
                            class="w-full rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm focus:outline-none focus:ring-4 focus:ring-[#001f3f]/5 focus:border-[#001f3f] transition-all">
                            <option value="">Pilih Waktu Kelas</option>
                            <option value="pagi" {{ old('kelas') == 'pagi' ? 'selected' : '' }}>Pagi</option>
                            <option value="malam" {{ old('kelas') == 'malam' ? 'selected' : '' }}>Malam</option>
                        </select>
                        @error('kelas')
                            <p class="text-[10px] text-rose-500 font-bold mt-1 uppercase">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-4 md:col-span-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-zinc-100">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Dosen Pengampu
                                    MK</label>
                                <select name="dosen_pengampu" required
                                    class="w-full rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm focus:outline-none focus:ring-4 focus:ring-[#001f3f]/5 focus:border-[#001f3f] transition-all">
                                    <option value="">Pilih Dosen Pengampu</option>
                                    @foreach ($praktikum->daftar_dosen ?? [] as $dosen)
                                        <option value="{{ $dosen }}"
                                            {{ old('dosen_pengampu') == $dosen ? 'selected' : '' }}>{{ $dosen }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('dosen_pengampu')
                                    <p class="text-[10px] text-rose-500 font-bold mt-1 uppercase">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Asal Kelas Mata
                                    Kuliah</label>
                                <select name="asal_kelas_mata_kuliah" required
                                    class="w-full rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm focus:outline-none focus:ring-4 focus:ring-[#001f3f]/5 focus:border-[#001f3f] transition-all">
                                    <option value="">Pilih Kelas MK</option>
                                    @foreach ($praktikum->daftar_kelas_mk ?? [] as $kelas)
                                        <option value="{{ $kelas }}"
                                            {{ old('asal_kelas_mata_kuliah') == $kelas ? 'selected' : '' }}>
                                            {{ $kelas }}</option>
                                    @endforeach
                                </select>
                                @error('asal_kelas_mata_kuliah')
                                    <p class="text-[10px] text-rose-500 font-bold mt-1 uppercase">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest block pt-4">Pilih Sesi
                            Praktikum</label>
                        <p class="text-[9px] text-zinc-400 italic mb-2">Pilih jadwal sesi yang tersedia untuk dikuti.</p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-2">
                            @forelse($praktikum->sesis as $sesi)
                                @php $isFull = $sesi->pendaftarans()->count() >= $sesi->kuota; @endphp
                                <label
                                    class="relative flex flex-col p-4 rounded-xl border cursor-pointer transition-all {{ $isFull ? 'bg-zinc-50 border-zinc-100 opacity-60 cursor-not-allowed' : 'bg-white border-zinc-200 hover:border-[#001f3f] hover:bg-zinc-50' }}">
                                    <input type="radio" name="sesi_id" value="{{ $sesi->id }}"
                                        {{ $isFull ? 'disabled' : '' }} required
                                        {{ old('sesi_id') == $sesi->id ? 'checked' : '' }}
                                        class="absolute top-4 right-4 h-4 w-4 text-[#001f3f] focus:ring-[#001f3f] border-zinc-300">
                                    <span class="text-sm font-bold text-zinc-900">{{ $sesi->nama_sesi }}</span>
                                    <div class="mt-1 space-y-0.5">
                                        <div class="flex items-center gap-1.5 text-[10px] font-medium text-zinc-400">
                                            <i class="fas fa-clock text-[9px]"></i>
                                            <span>{{ $sesi->hari }}, {{ substr($sesi->jam_mulai, 0, 5) }} -
                                                {{ substr($sesi->jam_selesai, 0, 5) }} WIB</span>
                                        </div>
                                    </div>
                                    <div class="mt-2 flex items-center justify-between">
                                        <span
                                            class="text-[9px] font-black uppercase tracking-tighter {{ $isFull ? 'text-rose-500' : 'text-emerald-500' }}">
                                            {{ $isFull ? 'PENUH' : 'TERSEDIA' }}
                                        </span>
                                        <span class="text-[10px] font-bold text-zinc-400">
                                            {{ $sesi->pendaftarans()->count() }}/{{ $sesi->kuota }}
                                        </span>
                                    </div>
                                </label>
                            @empty
                                <div
                                    class="col-span-2 py-4 text-center text-xs text-rose-500 font-bold italic border border-rose-100 rounded-xl bg-rose-50">
                                    Maaf, belum ada sesi tersedia untuk praktikum ini.
                                </div>
                            @endforelse
                        </div>
                        @error('sesi_id')
                            <p class="text-[10px] text-rose-500 font-bold mt-1 uppercase">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-4 md:col-span-2">
                        <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Apakah Anda Mengulang
                            Praktikum Ini?</label>
                        <div class="flex items-center gap-6">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="is_mengulang" value="0" checked
                                    class="h-4 w-4 text-[#001f3f] focus:ring-[#001f3f] border-zinc-300">
                                <span class="text-sm font-semibold text-zinc-700">Tidak, Baru Pertama Kali</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="is_mengulang" value="1"
                                    class="h-4 w-4 text-[#001f3f] focus:ring-[#001f3f] border-zinc-300">
                                <span class="text-sm font-semibold text-zinc-700">Ya, Saya Mengulang</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Uploads -->
            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-sm font-bold text-zinc-900 uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-upload text-[#001f3f]"></i>
                        Upload Dokumen Persyaratan
                    </h3>
                    <label class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-zinc-100/50 border border-zinc-200 cursor-pointer hover:bg-zinc-100 transition-colors">
                        <input type="checkbox" name="is_google_form" value="1" id="toggle_google_form"
                            class="h-3.5 w-3.5 text-[#001f3f] focus:ring-[#001f3f] border-zinc-300 rounded">
                        <span class="text-[10px] font-bold text-zinc-600 uppercase">Sudah upload via google form</span>
                    </label>
                </div>
                <div id="upload_section" class="grid grid-cols-1 md:grid-cols-2 gap-8 transition-opacity duration-300">
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-zinc-600 uppercase">Bukti KRS (PDF/JPG)</label>
                        <div class="relative group">
                            <input type="file" name="bukti_krs" id="bukti_krs" required onchange="previewFile(this, 'preview_krs')"
                                class="w-full text-xs text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-[#001f3f] file:text-white hover:file:bg-[#002d5a] file:transition-all cursor-pointer border border-zinc-200 rounded-xl p-2 bg-zinc-50/50">
                        </div>
                        <p class="text-[9px] text-zinc-400 italic">Pastikan mata kuliah praktikum tertera di KRS.</p>
                        <div id="preview_krs" class="mt-2 hidden"></div>
                        @error('bukti_krs')
                            <p class="text-[10px] text-rose-500 font-bold mt-1 uppercase">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-zinc-600 uppercase">Bukti Bayar (PDF/JPG)</label>
                        <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" required onchange="previewFile(this, 'preview_pembayaran')"
                            class="w-full text-xs text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-[#001f3f] file:text-white hover:file:bg-[#002d5a] file:transition-all cursor-pointer border border-zinc-200 rounded-xl p-2 bg-zinc-50/50">
                        <p class="text-[9px] text-zinc-400 italic">Upload struk pembayaran resmi dari bank/aplikasi.</p>
                        <div id="preview_pembayaran" class="mt-2 hidden"></div>
                        @error('bukti_pembayaran')
                            <p class="text-[10px] text-rose-500 font-bold mt-1 uppercase">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <label class="text-[11px] font-bold text-zinc-600 uppercase">Foto Beralmamater (JPG/PNG)</label>
                        <input type="file" name="foto_almamater" id="foto_almamater" required onchange="previewFile(this, 'preview_foto')"
                            class="w-full text-xs text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-[#001f3f] file:text-white hover:file:bg-[#002d5a] file:transition-all cursor-pointer border border-zinc-200 rounded-xl p-2 bg-zinc-50/50">
                        <p class="text-[9px] text-zinc-400 italic">Gunakan foto terbaru dengan almamater rapi, background
                            polos.</p>
                        <div id="preview_foto" class="mt-2 hidden"></div>
                        @error('foto_almamater')
                            <p class="text-[10px] text-rose-500 font-bold mt-1 uppercase">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4 pt-4">
                <button type="submit" {{ $praktikum->sesis->count() == 0 ? 'disabled' : '' }}
                    class="flex-grow py-3.5 bg-[#001f3f] text-white rounded-2xl text-xs font-bold uppercase tracking-[0.2em] shadow-xl shadow-[#001f3f]/20 hover:bg-[#002d5a] transition-all hover:-translate-y-0.5 active:scale-[0.98] disabled:bg-zinc-300 disabled:shadow-none disabled:cursor-not-allowed">
                    SUBMIT PENDAFTARAN SEKARANG
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleGoogleForm = document.getElementById('toggle_google_form');
        const uploadSection = document.getElementById('upload_section');
        const fileInputs = uploadSection.querySelectorAll('input[type="file"]');

        toggleGoogleForm.addEventListener('change', function() {
            if (this.checked) {
                uploadSection.classList.add('opacity-50');
                fileInputs.forEach(input => {
                    input.required = false;
                    input.disabled = true;
                });
            } else {
                uploadSection.classList.remove('opacity-50');
                fileInputs.forEach(input => {
                    input.required = true;
                    input.disabled = false;
                });
            }
        });
    });

    function previewFile(input, previewId) {
        const previewContainer = document.getElementById(previewId);
        previewContainer.innerHTML = ''; // clear existing
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();
            // Cek apakah file adalah PDF dengan mengecek type atau extension
            const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');

            reader.onload = function(e) {
                previewContainer.classList.remove('hidden');
                if (isPdf) {
                    previewContainer.innerHTML = '<iframe src="' + e.target.result + '" class="w-full h-48 md:h-64 rounded-lg border border-zinc-200" frameborder="0"></iframe>';
                } else {
                    previewContainer.innerHTML = '<img src="' + e.target.result + '" class="w-full h-auto max-h-64 object-contain rounded-lg border border-zinc-200" alt="Preview">';
                }
            }
            reader.readAsDataURL(file);
        } else {
            previewContainer.classList.add('hidden');
        }
    }
</script>
@endpush
