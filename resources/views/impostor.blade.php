<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAYANG SEKALI !</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
    <style>
        body { 
            background-color: #000000; 
            font-family: 'VT323', monospace;
            overflow: hidden; 
            color: #ff0000;
        }
        .impostor-text {
            text-shadow: 0 0 10px rgba(255, 0, 0, 0.8);
            letter-spacing: 0.1em;
        }
        .btn-earth {
            font-family: 'VT323', monospace;
            background: #ffffff;
            color: #000000;
            transition: all 0.3s ease;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.2);
        }
        .btn-earth:hover {
            transform: scale(1.1);
            box-shadow: 0 0 30px rgba(255, 255, 255, 0.4);
            background: #ff0000;
            color: #000000;
        }
        .scanline {
            width: 100%;
            height: 100px;
            z-index: 10;
            background: linear-gradient(0deg, rgba(0,0,0,0) 0%, rgba(255,255,255,0.02) 50%, rgba(0,0,0,0) 100%);
            opacity: 0.1;
            position: absolute;
            bottom: 100%;
            animation: scanline 10s linear infinite;
        }
        @keyframes scanline {
            0% { bottom: 100%; }
            100% { bottom: -100px; }
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="scanline"></div>
    
    <div class="text-center p-8 max-w-2xl relative z-20">
        <!-- Glow behind GIF -->
        <div class="absolute inset-0 flex items-center justify-center -z-10">
            <div class="w-96 h-64 bg-red-900/10 blur-[100px] border border-red-500/5 rounded-full"></div>
        </div>

        <div class="mb-4">
            <img src="{{ asset('image/impostor.gif') }}" alt="IMPOSTOR ALERT" 
                 class="w-full max-w-md mx-auto pointer-events-none">
        </div>

        <h1 class="text-6xl font-normal text-red-600 mb-6 impostor-text uppercase">
            KAU BUKAN ADMIN !
        </h1>

        <div class="space-y-6">
            <p class="text-2xl text-red-500/80 leading-tight">
                TINGGALKAN SISTEM SEKARANG, <br>
                ATAU KAU AKAN DI-EJECT!
            </p>

            <div class="flex flex-wrap justify-center gap-4 pt-4">
                <a href="{{ url('/') }}" 
                   class="btn-earth px-10 py-2 text-2xl font-bold uppercase rounded-sm flex items-center">
                    <i class="fas fa-caret-left mr-3"></i> KEMBALI KE BUMI
                </a>
            </div>

            <p class="text-sm font-mono text-red-900 mt-12 opacity-50 uppercase tracking-[0.5em]">
                STATUS: UNAUTHORIZED_ACCESS_DETECTED
            </p>
        </div>
    </div>

    <!-- Noise Effect -->
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none z-50 opacity-[0.03] contrast-150 brightness-150">
        <div class="bg-[url('https://www.transparenttextures.com/patterns/60-lines.png')] w-full h-full"></div>
    </div>
</body>
</html>
