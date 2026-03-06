@extends('layouts.admin')

@section('title', 'Edit Profil')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header Section -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                @php
                    $dashboardRoute = 'praktikan.dashboard';
                    $updateRoute = 'praktikan.profile.update';
                @endphp
                <a href="{{ route($dashboardRoute) }}"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-zinc-200 bg-white text-zinc-500 shadow-sm hover:bg-zinc-100 hover:text-zinc-900 transition-colors">
                    <i class="fas fa-arrow-left text-xs"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900">Pengaturan Profil</h1>
                    <p class="text-sm text-zinc-500 mt-1">Kelola informasi pribadi dan keamanan akun Anda.</p>
                </div>
            </div>
        </div>

        <form action="{{ route($updateRoute) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Left: Avatar Card -->
                <div class="md:col-span-1 space-y-6">
                    <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm text-center">
                        <div class="relative mx-auto h-32 w-32 mb-6 group">
                            @if ($user->profile_picture)
                                <img src="{{ asset('storage/' . $user->profile_picture) }}" id="preview"
                                    class="h-full w-full rounded-2xl object-cover border-4 border-white shadow-md transition-transform group-hover:scale-[1.02]">
                            @else
                                <div id="preview-placeholder"
                                    class="h-full w-full rounded-2xl bg-zinc-50 border-2 border-dashed border-zinc-200 flex flex-col items-center justify-center text-zinc-400 group-hover:border-zinc-300 transition-colors">
                                    <i class="fas fa-camera text-2xl mb-2"></i>
                                    <span class="text-[9px] font-bold uppercase tracking-widest">Update</span>
                                </div>
                                <img id="preview"
                                    class="h-full w-full rounded-2xl object-cover border-4 border-white shadow-md hidden">
                            @endif

                            <label for="profile_picture"
                                class="absolute -bottom-2 -right-2 h-8 w-8 bg-zinc-900 text-white rounded-lg shadow-lg flex items-center justify-center cursor-pointer hover:bg-zinc-800 transition-all hover:scale-110">
                                <i class="fas fa-pen text-[10px]"></i>
                                <input type="file" name="profile_picture" id="profile_picture" class="hidden"
                                    onchange="previewImage(event)">
                            </label>
                        </div>

                        <h2 class="text-lg font-bold text-zinc-900 leading-tight">{{ $user->name }}</h2>
                        <p class="text-xs font-medium text-zinc-400 uppercase tracking-widest mt-1">
                            {{ $user->role->display_name }}</p>

                        <div class="mt-6 pt-6 border-t border-zinc-100 flex flex-col gap-3">
                            <div
                                class="flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-zinc-400">
                                <span>Status Akun</span>
                                <span class="text-emerald-600">Terverifikasi</span>
                            </div>
                            <div
                                class="flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-zinc-400">
                                <span>Member Sejak</span>
                                <span class="text-zinc-900">{{ $user->created_at->format('M Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Identity & Security -->
                <div class="md:col-span-2 space-y-6">
                    <!-- Data Pribadi -->
                    <div class="rounded-2xl border border-zinc-200 bg-white overflow-hidden shadow-sm">
                        <div class="border-b border-zinc-100 bg-zinc-50/50 px-6 py-4">
                            <h3 class="text-sm font-bold text-zinc-900 uppercase tracking-wider flex items-center gap-2">
                                <i class="fas fa-id-card-clip text-zinc-400"></i>
                                Informasi Personal
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label
                                        class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.15em] ml-1">Nama
                                        Lengkap</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                        class="flex h-10 w-full rounded-md border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/5 transition-all">
                                    @error('name')
                                        <p class="text-[10px] font-bold text-rose-500 uppercase tracking-wider mt-1">
                                            {{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.15em] ml-1">NPM
                                        (ID Mahasiswa)</label>
                                    <input type="text" value="{{ $user->praktikan->npm }}" disabled
                                        class="flex h-10 w-full rounded-md border border-zinc-200 bg-zinc-100 px-3 py-2 text-sm font-bold text-zinc-500 cursor-not-allowed">
                                    <p class="text-[9px] text-zinc-400 italic">NPM adalah identitas tetap dan tidak dapat
                                        diubah.</p>
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.15em] ml-1">Username
                                        (Login ID)</label>
                                    <input type="text" name="username" value="{{ old('username', $user->username) }}"
                                        class="flex h-10 w-full rounded-md border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/5 transition-all">
                                    @error('username')
                                        <p class="text-[10px] font-bold text-rose-500 uppercase tracking-wider mt-1">
                                            {{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.15em] ml-1">Email
                                        Address</label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                        class="flex h-10 w-full rounded-md border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/5 transition-all">
                                    @error('email')
                                        <p class="text-[10px] font-bold text-rose-500 uppercase tracking-wider mt-1">
                                            {{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.15em] ml-1">No.
                                        WhatsApp</label>
                                    <input type="text" name="no_hp"
                                        value="{{ old('no_hp', $user->praktikan->no_hp) }}" placeholder="e.g., 0812..."
                                        class="flex h-10 w-full rounded-md border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/5 transition-all">
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.15em] ml-1">Angkatan</label>
                                    <input type="text" name="angkatan"
                                        value="{{ old('angkatan', $user->praktikan->angkatan) }}" placeholder="e.g., 2023"
                                        class="flex h-10 w-full rounded-md border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/5 transition-all">
                                </div>
                                <div class="sm:col-span-2 space-y-2">
                                    <label
                                        class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.15em] ml-1">Jurusan</label>
                                    <input type="text" name="jurusan"
                                        value="{{ old('jurusan', $user->praktikan->jurusan) }}"
                                        placeholder="e.g., Teknik Informatika"
                                        class="flex h-10 w-full rounded-md border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/5 transition-all">
                                </div>

                                <div class="space-y-2">
                                    <label
                                        class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.15em] ml-1">Password
                                        Baru (Opsional)</label>
                                    <input type="password" name="password"
                                        class="flex h-10 w-full rounded-md border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/5 transition-all">
                                    @error('password')
                                        <p class="text-[10px] font-bold text-rose-500 uppercase tracking-wider mt-1">
                                            {{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.15em] ml-1">Konfirmasi
                                        Password</label>
                                    <input type="password" name="password_confirmation"
                                        class="flex h-10 w-full rounded-md border border-zinc-200 bg-zinc-50 px-3 py-2 text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/5 transition-all">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-end gap-3 px-6 py-4 bg-zinc-50 border-t border-zinc-100 rounded-b-2xl">
                        <button type="submit"
                            class="px-8 py-2.5 rounded-xl bg-zinc-900 text-white text-xs font-black uppercase tracking-widest shadow-lg shadow-zinc-900/10 hover:bg-zinc-800 transition-all hover:-translate-y-0.5 active:scale-95">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('preview');
            const placeholder = document.getElementById('preview-placeholder');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    if (placeholder) placeholder.classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
