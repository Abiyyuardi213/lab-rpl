<footer class="bg-gray-50/50 py-8 w-full transition-all">
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <img src="{{ asset('image/logo-RPL.jpg') }}" alt="Logo" class="w-5 h-5 object-contain rounded">
                <span class="text-xs font-bold text-slate-700 tracking-tight">Lab RPL Informatika ITATS</span>
            </div>

            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                &copy; {{ date('Y') }}
                @php
                    $panelName = 'Admin Panel';
                    if (Auth::check() && Auth::user()->role) {
                        if (Auth::user()->role->name === 'Praktikan') {
                            $panelName = 'Portal Praktikan';
                        } elseif (Auth::user()->role->name === 'Aslab') {
                            $panelName = 'Portal Aslab';
                        }
                    }
                @endphp
                {{ $panelName }}. All rights reserved.
            </p>

            <div class="flex gap-6">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                    v2.0.0
                </span>
            </div>
        </div>
    </div>
</footer>
