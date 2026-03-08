<footer class="bg-white text-slate-600 py-20 border-t border-slate-100 text-sm">
    <div class="max-w-[1600px] mx-auto px-6 sm:px-10 lg:px-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
            <!-- Branding -->
            <div class="col-span-1 md:col-span-1">
                <div class="flex items-center gap-3 mb-6">
                    <img src="{{ asset('image/logo-RPL.jpg') }}" alt="LabRPL Logo" class="w-12 h-12 object-contain">
                    <div class="flex flex-col">
                        <h3 class="font-extrabold text-slate-900 leading-tight">LAB RPL ITATS</h3>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Informatika</span>
                    </div>
                </div>
                <p class="leading-relaxed text-slate-500 font-medium italic">
                    Pusat riset dan pembelajaran Rekayasa Perangkat Lunak - Institut Teknologi Adhi Tama Surabaya.
                </p>
            </div>

            <!-- Navigasi -->
            <div>
                <h3 class="font-bold text-slate-900 mb-6 uppercase tracking-widest text-xs">Navigasi</h3>
                <ul class="space-y-3.5">
                    <li><a href="#"
                            class="font-semibold text-slate-500 hover:text-primary transition-colors">Struktur
                            Organisasi</a></li>
                    <li><a href="#"
                            class="font-semibold text-slate-500 hover:text-primary transition-colors">Divisi</a></li>
                    <li><a href="#"
                            class="font-semibold text-slate-500 hover:text-primary transition-colors">Program Kerja</a>
                    </li>
                    <li><a href="#"
                            class="font-semibold text-slate-500 hover:text-primary transition-colors">Kegiatan</a></li>
                    <li><a href="#"
                            class="font-semibold text-slate-500 hover:text-primary transition-colors">Pengumuman &
                            Berita</a></li>
                </ul>
            </div>

            <!-- Kontak -->
            <div>
                <h3 class="font-bold text-slate-900 mb-6 uppercase tracking-widest text-xs">Kontak</h3>
                <ul class="space-y-5">
                    <li class="flex flex-col gap-1">
                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Email</span>
                        <span class="font-semibold text-slate-700">hmifitats1991@gmail.com</span>
                    </li>
                    <li class="flex flex-col gap-1">
                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Alamat</span>
                        <span class="font-semibold text-slate-700 leading-relaxed">
                            Jl. Arief Rahman Hakim No.100, <br>
                            Klampis Ngasem, Kec. Sukolilo, <br>
                            Surabaya, Jawa Timur 60117
                        </span>
                    </li>
                </ul>
            </div>

            <!-- Ikuti Kami -->
            <div>
                <h3 class="font-bold text-slate-900 mb-6 uppercase tracking-widest text-xs">Ikuti Kami</h3>
                <div class="flex gap-4">
                    <a href="#"
                        class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center border border-slate-200 text-slate-400 hover:bg-primary hover:text-white hover:border-primary transition-all duration-300">
                        <span class="text-[10px] font-bold">IG</span>
                    </a>
                    <a href="#"
                        class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center border border-slate-200 text-slate-400 hover:bg-primary hover:text-white hover:border-primary transition-all duration-300">
                        <span class="text-[10px] font-bold">LI</span>
                    </a>
                    <a href="#"
                        class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center border border-slate-200 text-slate-400 hover:bg-primary hover:text-white hover:border-primary transition-all duration-300">
                        <span class="text-[10px] font-bold">YT</span>
                    </a>
                </div>
            </div>
        </div>

        <div
            class="border-t border-slate-100 pt-10 flex flex-col md:flex-row justify-between items-center gap-6 text-[11px] font-bold text-slate-400 tracking-wider">
            <p>&copy; {{ date('Y') }} LAB RPL ITATS. ALL RIGHTS RESERVED.</p>
            <div class="flex gap-6 uppercase">
                <a href="#" class="hover:text-primary transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-primary transition-colors">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>
