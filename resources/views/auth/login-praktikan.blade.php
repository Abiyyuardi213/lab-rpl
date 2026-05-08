<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Praktikan - LabRPL AdminPanel</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Cloudflare Turnstile -->
    @if(!app()->environment('local'))
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit&onload=onloadTurnstileCallback" defer></script>
    @endif

    <!-- PWA & Apple Mobile Web Support -->
    <meta name="theme-color" content="#2563eb">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="{{ asset('image/rplmini.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at 0% 0%, #f0f4f8 0%, #e2e8f0 50%, #d7e3f1 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .main-card {
            display: flex;
            width: 100%;
            max-width: 820px;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.08);
            min-height: 480px;
        }

        .left-panel {
            flex: 1;
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
            padding: 2rem;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        @media (max-width: 768px) {
            .main-card {
                flex-direction: column;
            }
            .left-panel {
                display: none;
            }
        }

        /* Pattern Decorations */
        .pattern-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: radial-gradient(circle at 50% 50%, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 40px 40px;
            opacity: 0.4;
        }

        .circle-decoration {
            position: absolute;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .circle-1 { width: 240px; height: 240px; top: 50%; left: 50%; transform: translate(-50%, -50%); }
        .circle-2 { width: 380px; height: 380px; top: 50%; left: 50%; transform: translate(-50%, -50%); }

        .floating-icon {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .right-panel {
            flex: 1.1;
            padding: 2rem;
            display: flex;
            flex-direction: column;
        }

        .badge-ai {
            background: #eff6ff;
            color: #2563eb;
            padding: 5px 12px;
            border-radius: 100px;
            font-size: 0.7rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid #dbeafe;
            width: fit-content;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 0.5rem;
        }

        .input-wrapper {
            position: relative;
            margin-bottom: 1rem;
        }

        .input-field {
            width: 100%;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 14px 10px 44px;
            font-size: 0.9rem;
            transition: all 0.2s;
            color: #1e293b;
        }

        .input-field:focus {
            background: white;
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1rem;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
            font-size: 0.9rem;
        }

        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .stat-badge {
            text-align: center;
        }

        .stat-num {
            display: block;
            font-size: 1.1rem;
            font-weight: 800;
        }

        .stat-txt {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.8;
        }

        .footer-line {
            margin-top: auto;
            padding-top: 1.25rem;
            border-top: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            font-size: 0.65rem;
            color: #94a3b8;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .error-msg {
            font-size: 0.75rem;
            color: #ef4444;
            margin-top: -1rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }
    </style>
</head>

<body>

    <div class="flex flex-col items-center w-full">
        <div class="main-card">
            <!-- Left Side: Branding & Info -->
            <div class="left-panel">
                <div class="pattern-overlay"></div>
                <div class="circle-decoration circle-1"></div>
                <div class="circle-decoration circle-2"></div>



                <div class="flex-grow flex flex-col items-center justify-center text-center relative z-10">
                    <div class="mb-5 relative">
                        <div class="absolute inset-0 bg-blue-400/20 blur-3xl rounded-full"></div>
                        <img src="{{ asset('image/rplmini.png') }}" class="h-24 w-auto relative z-10" alt="Logo">
                    </div>
                    <h2 class="text-2xl font-extrabold mb-1 tracking-tight">Laboratorium RPL</h2>
                    <p class="text-blue-100/70 text-[10px] font-semibold uppercase tracking-[0.2em] mb-6">Sistem Informasi Rekayasa Perangkat Lunak</p>
                    
                    <div class="max-w-[280px]">
                        <p class="text-sm italic font-light leading-relaxed opacity-90">
                            "Pendidikan adalah passport untuk masa depan, karena hari esok adalah milik mereka yang mempersiapkannya hari ini."
                        </p>
                    </div>
                </div>

                <div class="relative z-10 flex justify-between items-end">
                    <div class="flex gap-8">
                        <div class="stat-badge">
                            <span class="stat-num">99%</span>
                            <span class="stat-txt">Akurasi Data</span>
                        </div>
                        <div class="stat-badge">
                            <span class="stat-num">24/7</span>
                            <span class="stat-txt">Akses Sistem</span>
                        </div>
                        <div class="stat-badge">
                            <span class="stat-num">IF</span>
                            <span class="stat-txt">Informatika</span>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Right Side: Login Form -->
            <div class="right-panel">
                <div class="mb-6">
                    <img src="{{ asset('image/logo-itats-biru.jpg') }}" class="h-8 w-auto mb-4" alt="ITATS">
                    
                    <div class="badge-ai">
                        <i class="fas fa-terminal text-[10px]"></i>
                        Pusat Praktikum Informatika
                    </div>

                    <h1 class="text-2xl font-bold text-slate-900 mb-1">Login Praktikan</h1>
                    <p class="text-slate-500 text-xs leading-relaxed">Sistem Informasi Laboratorium Rekayasa Perangkat Lunak - ITATS</p>
                </div>

                <form action="{{ route('login.praktikan.post') }}" method="POST" class="flex-grow">
                    @csrf

                    <label for="npm" class="form-label">Nomor Pokok Mahasiswa (NPM)</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="far fa-id-card"></i></span>
                        <input type="text" name="npm" id="npm" value="{{ old('npm') }}" class="input-field" placeholder="Contoh: 06.2024.1.XXXXX" required autofocus>
                    </div>
                    @error('npm')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror

                    <label for="password" class="form-label">Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="fas fa-shield-halved"></i></span>
                        <input type="password" name="password" id="password" class="input-field" placeholder="••••••••" required>
                        <button type="button" onclick="togglePassword('password', this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-blue-600 transition-colors">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>

                    <div class="flex items-center justify-between mb-5">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" name="remember" value="1" @checked(old('remember')) class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 transition-all cursor-pointer">
                            <span class="text-xs text-slate-600 group-hover:text-slate-900 transition-colors">Ingat saya</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition-colors">Lupa Password?</a>
                    </div>

                    @if(!app()->environment('local'))
                    <div class="mb-6 flex justify-center">
                        <div id="turnstile-container"></div>
                    </div>
                    @endif

                    <button type="submit" class="btn-primary">
                        <span>Masuk sebagai Praktikan</span>
                        <i class="fas fa-arrow-right-long text-xs"></i>
                    </button>
                    
                    <div class="mt-4 text-center">
                        <p class="text-xs text-slate-500">
                            Belum punya akun? 
                            <a href="{{ route('register') }}" class="text-blue-600 font-bold hover:underline">Daftar Sekarang</a>
                        </p>
                    </div>

                    <div class="mt-3 p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-start gap-3">
                        <i class="fas fa-circle-info text-blue-500 mt-0.5 text-xs"></i>
                        <p class="text-[10px] text-slate-500 font-medium leading-relaxed italic">
                            Pastikan NPM yang Anda masukkan sudah terdaftar sebagai Praktikan oleh Laboratorium.
                        </p>
                    </div>
                </form>

                <div class="footer-line">
                    <span class="flex items-center gap-2"><i class="fas fa-lock"></i> Sistem Terenkripsi</span>
                    <span class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Server Online</span>
                </div>
            </div>
        </div>

        <!-- External Footer Info -->
        <div class="mt-8 text-center space-y-4">
             <div class="flex items-center justify-center gap-4 text-xs font-bold text-slate-400 uppercase tracking-widest">
                 <a href="/" class="hover:text-slate-600 transition-colors">Halaman Utama</a>
                 <span>•</span>
                 <span>Pusat Bantuan</span>
             </div>
             <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em]">
                 &copy; {{ date('Y') }} LabRPL TEKNIK INFORMATIKA ITATS
             </p>
        </div>
    </div>

    @if(!app()->environment('local'))
    <script>
        window.onloadTurnstileCallback = function() {
            const container = document.getElementById('turnstile-container');
            if (!container) return;
            container.innerHTML = '';
            if (window.widgetId) {
                try { turnstile.remove(window.widgetId); } catch (e) {}
            }
            window.widgetId = turnstile.render("#turnstile-container", {
                sitekey: "{{ config('services.turnstile.key') }}",
                callback: function(token) { console.log("Success:", token); },
            });
        };
    </script>
    @endif

    @if (session('logout_success') || session('success'))
        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            })
            Toast.fire({
                icon: 'success',
                title: '{{ session('logout_success') ?? session('success') }}'
            })
        </script>
    @endif

    <script>
        function togglePassword(id, btn) {
            const input = document.getElementById(id);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>

</html>
