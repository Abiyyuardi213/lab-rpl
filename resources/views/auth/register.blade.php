<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - LabRPL</title>
    <!-- Font Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- PWA & Apple Mobile Web Support -->
    <meta name="theme-color" content="#001f3f">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="{{ asset('image/logo-RPL.jpg') }}">
    <link rel="apple-touch-startup-image" href="{{ asset('image/logo-RPL.jpg') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            z-index: 10;
        }

        .step-line {
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #f4f4f5;
            z-index: 0;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade {
            animation: fadeIn 0.4s ease-out forwards;
        }

        .input-group-custom i {
            pointer-events: none;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentStep = 1;
            const totalSteps = 3;

            const updateUI = (step) => {
                document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));
                const currentEl = document.getElementById(`step-${step}`);
                currentEl.classList.remove('hidden');
                currentEl.classList.add('animate-fade');

                document.getElementById('stage-text').textContent = `Langkah Ke-${step}`;

                for (let i = 1; i <= totalSteps; i++) {
                    const circle = document.getElementById(`circle-${i}`);
                    const label = document.getElementById(`label-${i}`);
                    
                    if (i < step) {
                        circle.innerHTML = '<i class="fas fa-check"></i>';
                        circle.className = 'step-circle bg-emerald-500 text-white shadow-lg shadow-emerald-500/20';
                        label.className = 'text-[10px] font-black text-zinc-900 mt-2 text-center uppercase tracking-tighter';
                    } else if (i === step) {
                        circle.innerHTML = i;
                        circle.className = 'step-circle bg-[#001f3f] text-white shadow-lg shadow-[#001f3f]/20 scale-110';
                        label.className = 'text-[10px] font-black text-[#001f3f] mt-2 text-center uppercase tracking-tighter';
                    } else {
                        circle.innerHTML = i;
                        circle.className = 'step-circle bg-white border border-zinc-200 text-zinc-400';
                        label.className = 'text-[10px] font-bold text-zinc-400 mt-2 text-center uppercase tracking-tighter';
                    }
                }

                if (step === 3) {
                    document.getElementById('sum-npm').textContent = document.getElementById('npm').value || '-';
                    document.getElementById('sum-name').textContent = document.getElementById('name').value || '-';
                    document.getElementById('sum-email').textContent = document.getElementById('email').value || '-';
                }

                currentStep = step;
            };

            window.nextStep = (e) => {
                if (e) e.preventDefault();
                const currentInputs = document.getElementById(`step-${currentStep}`).querySelectorAll('input[required]');
                let valid = true;
                currentInputs.forEach(input => {
                    if (!input.checkValidity()) {
                        input.reportValidity();
                        valid = false;
                    }
                });

                if (currentStep === 2) {
                    const pass = document.getElementById('password').value;
                    const conf = document.getElementById('password_confirmation').value;
                    if (pass.length < 6) {
                        valid = false;
                        Swal.fire({
                            icon: 'warning',
                            title: 'Password Terlalu Pendek',
                            text: 'Password minimal 6 karakter',
                            confirmButtonColor: '#001f3f'
                        });
                    } else if (pass !== conf) {
                        valid = false;
                        Swal.fire({
                            icon: 'error',
                            title: 'Konfirmasi Salah',
                            text: 'Konfirmasi password tidak cocok',
                            confirmButtonColor: '#001f3f'
                        });
                    }
                }

                if (valid && currentStep < totalSteps) {
                    updateUI(currentStep + 1);
                }
            };

            window.prevStep = () => {
                if (currentStep > 1) updateUI(currentStep - 1);
            };

            // Global Form Handler
            const form = document.getElementById('register-form');
            form.addEventListener('submit', function(e) {
                if (currentStep < totalSteps) {
                    e.preventDefault();
                    nextStep(e);
                }
            });

            // Handle 'Enter' key on all inputs
            form.querySelectorAll('input').forEach(input => {
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        nextStep(e);
                    }
                });
            });

            updateUI(1);

            @if($errors->has('npm'))
                updateUI(1);
            @elseif($errors->any())
                updateUI(2);
            @endif
        });
    </script>
</head>

<body class="bg-zinc-50 font-sans text-zinc-900 antialiased min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-6xl grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <!-- Left Side: Branding -->
        <div class="text-center lg:text-left space-y-8 animate-fade h-full flex flex-col justify-center">
            <div class="flex justify-center lg:justify-start">
                <div class="p-4 bg-white rounded-2xl border border-zinc-200 shadow-sm inline-block">
                    <img src="{{ asset('image/logo-RPL.jpg') }}" class="h-24 w-auto object-contain" alt="Logo">
                </div>
            </div>
            <div class="space-y-4">
                <h1 class="text-3xl lg:text-5xl font-bold tracking-tight text-zinc-900 leading-tight">
                    Laboratorium <br class="hidden lg:block"> Rekayasa Perangkat Lunak
                </h1>
                <p class="text-sm text-zinc-500 mt-2 max-w-sm mx-auto lg:mx-0">
                    Sistem Informasi Terintegrasi Laboratorium Rekayasa Perangkat Lunak - ITATS
                </p>
                
                <div class="hidden lg:flex items-center gap-4 pt-8">
                    <div class="flex -space-x-3">
                        <div class="w-10 h-10 rounded-full border-2 border-white bg-zinc-200"></div>
                        <div class="w-10 h-10 rounded-full border-2 border-white bg-zinc-300"></div>
                        <div class="w-10 h-10 rounded-full border-2 border-white bg-zinc-400"></div>
                    </div>
                    <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Gabung bersama kami</p>
                </div>
            </div>

            <!-- Back to Public (Desktop) -->
            <div class="hidden lg:block pt-8">
                <a href="/" class="inline-flex items-center gap-2 text-sm font-medium text-zinc-500 hover:text-zinc-900 transition-colors group">
                    <i class="fas fa-arrow-left text-xs group-hover:-translate-x-1 transition-transform"></i>
                    <span>KEMBALI KE HALAMAN UTAMA</span>
                </a>
            </div>
        </div>

        <!-- Right Side: Form Card -->
        <div class="flex flex-col items-center">
            <div class="w-full max-w-lg bg-white rounded-2xl border border-zinc-200 shadow-sm overflow-hidden mb-6">
                <div class="p-8 lg:p-10">
                    <div class="flex items-center gap-3 mb-10">
                        <div class="h-10 w-10 rounded-xl bg-[#001f3f] flex items-center justify-center text-white shadow-lg shadow-[#001f3f]/20">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h2 class="text-lg font-bold text-zinc-900 tracking-tight">Pendaftaran Akun</h2>
                    </div>

                    <!-- Step Indicator -->
                    <div class="relative mb-12 px-4 text-center">
                        <div class="step-line"></div>
                        <div class="flex justify-between relative">
                            <div class="flex flex-col items-center flex-1">
                                <div id="circle-1" class="step-circle">1</div>
                                <div id="label-1" class="text-[10px] font-black mt-2">NPM</div>
                            </div>
                            <div class="flex flex-col items-center flex-1">
                                <div id="circle-2" class="step-circle">2</div>
                                <div id="label-2" class="text-[10px] font-black mt-2 text-zinc-400">DETAIL</div>
                            </div>
                            <div class="flex flex-col items-center flex-1">
                                <div id="circle-3" class="step-circle">3</div>
                                <div id="label-3" class="text-[10px] font-black mt-2 text-zinc-400">KONFIRMASI</div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mb-10">
                        <span id="stage-text" class="px-4 py-1.5 rounded-full bg-zinc-100 border border-zinc-200 text-[#001f3f] text-[10px] font-black uppercase tracking-widest shadow-sm">Langkah Ke-1</span>
                    </div>

                    <form action="{{ route('register.post') }}" method="POST" id="register-form">
                        @csrf

                        <!-- Step 1: NPM -->
                        <div id="step-1" class="step-content space-y-5">
                            <div class="space-y-2">
                                <label for="npm" class="block text-sm font-bold text-zinc-700 uppercase tracking-tight">Nomor Pokok Mahasiswa (NPM)</label>
                                <div class="relative group input-group-custom">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 flex items-center justify-center text-zinc-400 group-focus-within:text-[#001f3f] transition-colors z-10">
                                        <i class="fas fa-id-card text-sm"></i>
                                    </span>
                                    <input type="text" name="npm" id="npm" value="{{ old('npm') }}" 
                                        class="block w-full rounded-lg border border-zinc-300 pl-10 pr-3 py-2.5 text-sm transition-all focus:border-[#001f3f] focus:outline-none focus:ring-1 focus:ring-[#001f3f] placeholder:text-zinc-400 font-medium bg-zinc-50/30 focus:bg-white" 
                                        placeholder="Masukan NPM Kamu" required autofocus>
                                </div>
                            </div>
                            <button type="button" onclick="nextStep(event)" 
                                class="w-full flex justify-center items-center gap-2 rounded-lg bg-[#001f3f] px-4 py-3 text-sm font-bold text-white hover:bg-[#002d5a] transition-all active:scale-[0.98] shadow-lg shadow-[#001f3f]/10 mt-2">
                                <span>Lanjutkan</span>
                                <i class="fas fa-arrow-right text-xs"></i>
                            </button>
                        </div>

                        <!-- Step 2: Account Details -->
                        <div id="step-2" class="step-content space-y-5 hidden">
                            <div class="space-y-2">
                                <label for="name" class="block text-sm font-bold text-zinc-700 uppercase tracking-tight">Nama Mahasiswa</label>
                                <div class="relative group input-group-custom">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 flex items-center justify-center text-zinc-400 group-focus-within:text-[#001f3f] transition-colors z-10">
                                        <i class="fas fa-user-circle text-sm"></i>
                                    </span>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" 
                                        class="block w-full rounded-lg border border-zinc-300 pl-10 pr-3 py-2.5 text-sm transition-all focus:border-[#001f3f] focus:outline-none focus:ring-1 focus:ring-[#001f3f] placeholder:text-zinc-400 font-medium bg-zinc-50/30 focus:bg-white" 
                                        placeholder="Masukan Nama Lengkap" required>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label for="email" class="block text-sm font-bold text-zinc-700 uppercase tracking-tight">Email Kampus</label>
                                <div class="relative group input-group-custom">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 flex items-center justify-center text-zinc-400 group-focus-within:text-[#001f3f] transition-colors z-10">
                                        <i class="fas fa-envelope text-sm"></i>
                                    </span>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" 
                                        class="block w-full rounded-lg border border-zinc-300 pl-10 pr-3 py-2.5 text-sm transition-all focus:border-[#001f3f] focus:outline-none focus:ring-1 focus:ring-[#001f3f] placeholder:text-zinc-400 font-medium bg-zinc-50/30 focus:bg-white" 
                                        placeholder="user@itatsu.student.ac.id" required>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label for="password" class="block text-sm font-bold text-zinc-700 uppercase tracking-tight">Password</label>
                                    <div class="relative group input-group-custom">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 flex items-center justify-center text-zinc-400 group-focus-within:text-[#001f3f] transition-colors z-10">
                                            <i class="fas fa-lock text-sm"></i>
                                        </span>
                                        <input type="password" name="password" id="password" 
                                            class="block w-full rounded-lg border border-zinc-300 pl-10 pr-3 py-2.5 text-sm transition-all focus:border-[#001f3f] focus:outline-none focus:ring-1 focus:ring-[#001f3f] placeholder:text-zinc-400 font-medium bg-zinc-50/30 focus:bg-white" 
                                            placeholder="••••••" required>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label for="password_confirmation" class="block text-sm font-bold text-zinc-700 uppercase tracking-tight">Konfirmasi</label>
                                    <div class="relative group input-group-custom">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 flex items-center justify-center text-zinc-400 group-focus-within:text-[#001f3f] transition-colors z-10">
                                            <i class="fas fa-check-double text-sm"></i>
                                        </span>
                                        <input type="password" name="password_confirmation" id="password_confirmation" 
                                            class="block w-full rounded-lg border border-zinc-300 pl-10 pr-3 py-2.5 text-sm transition-all focus:border-[#001f3f] focus:outline-none focus:ring-1 focus:ring-[#001f3f] placeholder:text-zinc-400 font-medium bg-zinc-50/30 focus:bg-white" 
                                            placeholder="••••••" required>
                                    </div>
                                </div>
                            </div>
                            
                            <p class="text-[11px] font-bold text-rose-500 uppercase tracking-widest leading-tight">*Password minimal 6 karakter</p>

                            <div class="grid grid-cols-2 gap-3 mt-4">
                                <button type="button" onclick="prevStep()" 
                                    class="flex justify-center items-center gap-2 rounded-lg border border-zinc-200 bg-white px-4 py-3 text-sm font-bold text-zinc-600 hover:bg-zinc-50 transition-all active:scale-[0.98]">
                                    <i class="fas fa-arrow-left text-xs"></i>
                                    <span>Kembali</span>
                                </button>
                                <button type="button" onclick="nextStep(event)" 
                                    class="flex justify-center items-center gap-2 rounded-lg bg-[#001f3f] px-4 py-3 text-sm font-bold text-white hover:bg-[#002d5a] transition-all active:scale-[0.98] shadow-lg shadow-[#001f3f]/10">
                                    <span>Lanjutkan</span>
                                    <i class="fas fa-arrow-right text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 3: Confirmation -->
                        <div id="step-3" class="step-content space-y-6 hidden">
                            <div class="bg-zinc-50 rounded-xl p-5 border border-zinc-200 space-y-4 shadow-inner">
                                <div class="flex justify-between items-center group">
                                    <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">NPM</span>
                                    <span id="sum-npm" class="text-sm font-bold text-[#001f3f] group-hover:translate-x-1 transition-transform">-</span>
                                </div>
                                <div class="h-[1px] bg-zinc-200/50"></div>
                                <div class="flex justify-between items-center group">
                                    <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Nama</span>
                                    <span id="sum-name" class="text-sm font-bold text-[#001f3f] group-hover:translate-x-1 transition-transform">-</span>
                                </div>
                                <div class="h-[1px] bg-zinc-200/50"></div>
                                <div class="flex justify-between items-center group">
                                    <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Email</span>
                                    <span id="sum-email" class="text-sm font-bold text-[#001f3f] group-hover:translate-x-1 transition-transform">-</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 p-4 bg-zinc-50 border border-zinc-100 rounded-lg">
                                <div class="w-10 h-10 rounded-lg bg-white border border-zinc-200 flex items-center justify-center text-[#001f3f] flex-shrink-0 shadow-sm">
                                    <i class="fas fa-info text-xs"></i>
                                </div>
                                <p class="text-xs text-zinc-500 font-medium leading-tight italic">Pastikan data sudah benar. Setelah mendaftar, anda dapat langsung masuk ke sistem.</p>
                            </div>

                            <div class="grid grid-cols-2 gap-3 mt-4">
                                <button type="button" onclick="prevStep()" class="flex justify-center items-center gap-2 rounded-lg border border-zinc-200 bg-white px-4 py-3 text-sm font-bold text-zinc-600 hover:bg-zinc-50 transition-all active:scale-[0.98]">
                                    <i class="fas fa-arrow-left text-xs"></i>
                                    <span>Kembali</span>
                                </button>
                                <button type="submit" class="flex justify-center items-center gap-2 rounded-lg bg-emerald-600 px-4 py-3 text-sm font-bold text-white hover:bg-emerald-700 transition-all active:scale-[0.98] shadow-lg shadow-emerald-600/10">
                                    <span>Daftar Sekarang</span>
                                    <i class="fas fa-check-circle text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Footer Action -->
                <div class="px-8 py-5 bg-zinc-50 border-t border-zinc-100 text-center">
                    <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-3">Sudah memiliki akses?</p>
                    <a href="{{ route('login.praktikan') }}" class="inline-flex items-center gap-2 justify-center w-full py-2.5 rounded-xl border border-zinc-200 bg-white text-xs font-black text-zinc-900 hover:bg-zinc-50 transition-all active:scale-[0.98] uppercase tracking-widest shadow-sm">
                        <span>Masuk ke Akun</span>
                        <i class="fas fa-sign-in-alt text-[10px]"></i>
                    </a>
                </div>
            </div>

            <!-- Mobile Footer Info -->
            <div class="lg:hidden text-center space-y-4 pb-8">
                <a href="/" class="mt-8 inline-flex items-center gap-2 text-sm font-medium text-zinc-500 hover:text-zinc-900 transition-colors group">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Halaman Utama</span>
                </a>
                <p class="text-center text-[11px] font-bold text-zinc-300 uppercase tracking-[0.2em] mt-12 mb-4">
                    &copy; {{ date('Y') }} LabRPL TEKNIK INFORMATIKA ITATS
                </p>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: '{{ $errors->first() }}',
                confirmButtonColor: '#001f3f'
            });
        </script>
    @endif
</body>

</html>
