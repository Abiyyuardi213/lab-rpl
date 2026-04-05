<!DOCTYPE html>
<html>
<head>
    <title>QR Presensi - {{ $jadwal->judul_modul }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            margin: 0;
            padding: 20px; /* Reduced from 40px */
            text-align: center;
        }
        .logo-container {
            position: relative;
            width: 100%;
            height: 70px; /* Reduced from 100px */
            margin-bottom: 10px;
        }
        .logo-left {
            position: absolute;
            left: 0;
            top: 0;
            width: 100px; /* Reduced from 120px */
        }
        .logo-right {
            position: absolute;
            right: 0;
            top: 0;
            width: 60px; /* Reduced from 80px */
        }
        .logo-left img, .logo-right img {
            width: 100%;
            height: auto;
        }
        .header {
            margin-top: 20px; /* Reduced from 40px */
            margin-bottom: 15px; /* Reduced from 30px */
        }
        .praktikum-name {
            font-size: 20px; /* Reduced from 24px */
            font-weight: bold;
            color: #001f3f;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .modul-title {
            font-size: 16px; /* Reduced from 18px */
            color: #64748b;
            margin-bottom: 15px;
        }
        .info {
            font-size: 10px; /* Reduced from 12px */
            color: #94a3b8;
            margin-bottom: 20px; /* Reduced from 40px */
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .qr-container {
            margin: 10px auto;
            padding: 15px;
            border: 2px solid #e2e8f0;
            display: inline-block;
            border-radius: 20px;
        }
        .qr-image {
            width: 260px; /* Reduced from 300px */
            height: 260px;
        }
        .instruction-box {
            margin: 15px auto; /* Reduced from 40px */
            max-width: 480px;
            text-align: left;
            background-color: #f8fafc;
            border-radius: 12px;
            padding: 15px 20px; /* Reduced padding */
            border: 1px solid #e2e8f0;
        }
        .instruction-title {
            font-size: 12px; /* Reduced from 14px */
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            margin-bottom: 12px;
            letter-spacing: 1px;
        }
        .instruction-item {
            margin-bottom: 8px; /* Reduced from 15px */
            position: relative;
            padding-left: 35px;
            font-size: 11px; /* Reduced from 12px */
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
        }
        .step-number {
            position: absolute;
            left: 0;
            top: -2px;
            width: 20px; /* Reduced size */
            height: 20px;
            background-color: #001f3f;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 20px;
            font-size: 9px;
        }
        .footer {
            margin-top: 25px; /* Reduced from 50px */
            padding-top: 15px;
            border-top: 1px dashed #e2e8f0;
        }
        .instruction {
            font-size: 9px; /* Reduced from 10px */
            color: #64748b;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
    </style>
</head>
<body>
    <div class="logo-container">
        <div class="logo-left">
            @if($itatsLogo)
                <img src="data:image/jpeg;base64,{{ $itatsLogo }}">
            @endif
        </div>
        <div class="logo-right">
            @if($rplLogo)
                <img src="data:image/png;base64,{{ $rplLogo }}">
            @endif
        </div>
    </div>

    <div class="header">
        <div class="praktikum-name">{{ $jadwal->praktikum->nama_praktikum }}</div>
        <div class="modul-title">{{ $jadwal->judul_modul }}</div>
        <div class="info">
            {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('l, d F Y') }}
            <br>
            {{ substr($jadwal->waktu_mulai, 0, 5) }} - {{ substr($jadwal->waktu_selesai, 0, 5) }} WIB
            <br>
            Ruangan: {{ $jadwal->ruangan ?? 'Daring' }}
        </div>
    </div>

    <div class="qr-container">
        <img src="data:image/svg+xml;base64,{{ $qrCode }}" class="qr-image">
    </div>

    <div class="instruction-box">
        <div class="instruction-title">
            <span style="color: #001f3f;">i</span> &nbsp; INSTRUKSI PRAKTIKAN
        </div>
        <div class="instruction-item">
            <div class="step-number">1</div>
            BUKA APLIKASI LAB-RPL DAN LOGIN
        </div>
        <div class="instruction-item">
            <div class="step-number">2</div>
            GUNAKAN SCANNER PADA PONSEL ANDA
        </div>
        <div class="instruction-item">
            <div class="step-number">3</div>
            ARAHKAN KE QR CODE DI ATAS
        </div>
        <div class="instruction-item">
            <div class="step-number">4</div>
            PRESENSI AKAN TERCATAT SECARA OTOMATIS
        </div>
    </div>

    <div class="footer">
        <div class="instruction">Scan kode ini menggunakan aplikasi Lab-RPL untuk melakukan presensi</div>
        <div style="font-size: 8px; color: #cbd5e1; margin-top: 20px;">
            Generated by Lab-RPL ITATS Attendance System
        </div>
    </div>
</body>
</html>
