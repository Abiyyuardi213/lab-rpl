<div data-aslab="{{ json_encode([
    'name' => $aslab->user->name,
    'npm' => $aslab->npm,
    'jurusan' => $aslab->jurusan,
    'angkatan' => $aslab->angkatan,
    'email' => $aslab->user->email,
    'no_hp' => $aslab->no_hp ?? '-',
    'jabatan' => $aslab->jabatan,
    'foto' => $aslab->user->profile_picture
        ? asset('storage/' . $aslab->user->profile_picture)
        : 'https://ui-avatars.com/api/?name=' . urlencode($aslab->user->name) . '&background=1a4fa0&color=fff&size=200',
]) }}" onclick="showAslabDetail(this)"
    class="group cursor-pointer relative bg-white rounded-3xl p-6 border border-slate-200 hover:border-[#1a4fa0]/30 hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 {{ $width ?? '' }}">
    <div class="relative w-24 h-24 mx-auto mb-6">
        <div
            class="absolute inset-0 bg-[#1a4fa0]/10 rounded-2xl group-hover:rotate-12 group-hover:scale-110 transition-transform duration-500">
        </div>
        <img src="{{ $aslab->user->profile_picture ? asset('storage/' . $aslab->user->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($aslab->user->name) . '&background=f1f5f9&color=64748b' }}"
            alt="{{ $aslab->user->name }}"
            class="relative w-full h-full rounded-2xl object-cover border-2 border-white shadow-sm transition-transform duration-500 group-hover:scale-105">
    </div>
    <div class="text-center">
        <h5 class="text-sm font-bold text-slate-900 leading-tight mb-1 group-hover:text-[#1a4fa0] transition-colors">
            {{ $aslab->user->name }}
        </h5>
        <p class="text-[10px] text-[#1a4fa0] font-bold uppercase tracking-wider mb-2">
            {{ $aslab->jabatan }}
        </p>
        <p class="text-xs text-slate-500 mb-4">{{ $aslab->jurusan }} • {{ $aslab->angkatan }}
        </p>
        <div class="flex items-center justify-center gap-2">
            <span
                class="text-[9px] font-bold text-slate-400 border border-slate-100 px-2 py-0.5 rounded shadow-sm group-hover:bg-slate-50 transition-colors">
                NPM: {{ $aslab->npm }}
            </span>
        </div>
    </div>
</div>
