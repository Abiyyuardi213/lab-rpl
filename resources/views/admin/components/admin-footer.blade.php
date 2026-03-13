<footer class="bg-white border-t border-slate-200 py-12 w-full">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <img src="{{ asset('image/logo-RPL.jpg') }}" alt="Logo" class="w-6 h-6 object-contain">
                <span class="text-xs font-bold text-slate-900 tracking-tight">Lab RPL Informatika ITATS</span>
            </div>

            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                &copy; {{ date('Y') }} AdminPanel. All rights reserved.
            </p>

            <div class="flex gap-6">
                <a href="{{ url('/') }}"
                    class="text-[11px] font-bold text-slate-400 uppercase tracking-wider hover:text-primary transition-colors">
                    Lihat Website
                </a>
                <span class="text-slate-200">|</span>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                    v1.0.0
                </span>
            </div>
        </div>
    </div>
</footer>
