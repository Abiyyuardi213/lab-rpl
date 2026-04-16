<a href="{{ route('aslab.portfolio', $aslab->slug ?? 'not-set') }}" 
   class="group block relative perspective-1000">
    <div class="relative aspect-[4/5] overflow-hidden bg-slate-200 shadow-lg transition-all duration-500 group-hover:shadow-2xl group-hover:-translate-y-2">
        {{-- Background Pattern/Gradient if no image --}}
        <div class="absolute inset-0 bg-gradient-to-br from-[#1a4fa0] to-blue-500 opacity-10"></div>
        
        @if($aslab->profile_image)
            <img src="{{ asset('storage/' . $aslab->profile_image) }}" 
                 alt="{{ $aslab->user->name }}"
                 class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110">
        @else
            <div class="flex h-full w-full flex-col items-center justify-center p-6 text-center">
                <img src="{{ $aslab->user->profile_picture ? asset('storage/' . $aslab->user->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($aslab->user->name) . '&background=1a4fa0&color=fff&size=200' }}"
                     alt="{{ $aslab->user->name }}"
                     class="w-20 h-20 rounded-full object-cover border-4 border-white shadow-md mb-3">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Lab Assistant</span>
            </div>
        @endif

        {{-- Hover Overlay --}}
        <div class="absolute inset-0 bg-black/40 opacity-0 transition-opacity duration-300 group-hover:opacity-100 flex items-center justify-center">
            <span class="px-4 py-2 bg-white/20 backdrop-blur-md rounded-full text-white text-[10px] font-bold uppercase tracking-widest border border-white/30">Lihat Portfolio</span>
        </div>
    </div>

    <div class="mt-4 text-center">
        <h5 class="text-xs sm:text-sm font-black text-slate-900 group-hover:text-[#1a4fa0] transition-colors leading-tight uppercase tracking-tight">
            {{ $aslab->user->name }}
        </h5>
        <p class="text-[9px] sm:text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-[0.1em]">
            {{ $aslab->jabatan }}
        </p>
    </div>
</a>
