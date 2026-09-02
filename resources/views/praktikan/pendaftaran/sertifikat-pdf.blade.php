<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sertifikat Kelulusan Praktikum</title>
    <style>
        @page {
            margin: 0;
            size: a4 landscape;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            color: #1e293b;
        }
        
        .bg-template-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 297mm;
            height: 210mm;
            z-index: -1;
        }

        .overlay-content {
            position: relative;
            z-index: 10;
            text-align: center;
            box-sizing: border-box;
            padding-top: 135px;
            width: 100%;
        }

        .main-title {
            font-size: 32px;
            font-weight: 900;
            color: #1e293b;
            letter-spacing: 4px;
            margin: 0 0 6px 0;
            text-transform: uppercase;
        }
        .cert-number {
            font-size: 15px;
            font-weight: bold;
            color: #0284c7; /* Sky blue matching ITATS theme */
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin-bottom: 14px;
        }
        .given-to {
            font-size: 14px;
            font-weight: bold;
            color: #334155;
            margin-bottom: 8px;
        }
        .student-name {
            font-size: 34px;
            font-weight: bold;
            color: #0284c7;
            border-bottom: 3px solid #334155;
            display: inline-block;
            padding-bottom: 4px;
            margin-bottom: 8px;
            padding-left: 24px;
            padding-right: 24px;
        }
        .student-npm {
            font-size: 15px;
            font-weight: bold;
            color: #334155;
            margin-bottom: 12px;
        }
        .as-label {
            font-size: 14px;
            font-weight: bold;
            color: #334155;
            margin-bottom: 6px;
        }
        .status-badge {
            font-size: 26px;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 6px;
        }
        .course-title {
            font-size: 18px;
            font-weight: bold;
            color: #334155;
            margin-bottom: 4px;
        }
        .course-sub {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 12px;
        }
        .location-date {
            font-size: 13px;
            font-weight: bold;
            color: #475569;
            margin-bottom: 4px;
        }
        .mengetahui-text {
            font-size: 13px;
            color: #475569;
            margin-bottom: 15px;
        }

        .signatures-table {
            width: 86%;
            margin: 0 auto;
            border-collapse: collapse;
        }
        .signatures-table td {
            width: 50%;
            vertical-align: top;
            text-align: center;
        }
        .sig-title {
            font-size: 13px;
            color: #1e293b;
            font-weight: bold;
            line-height: 1.3;
        }
        .sig-image-container {
            height: 70px;
            margin: 2px 0;
        }
        .sig-image {
            max-height: 70px;
            max-width: 160px;
        }
        .sig-name {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            text-decoration: underline;
        }
        .sig-nip {
            font-size: 12px;
            font-weight: bold;
            color: #334155;
            margin-top: 2px;
        }

        /* Standard Built-in Header (If admin hasn't uploaded background) */
        .default-header-logos {
            width: 88%;
            margin: 0 auto 10px auto;
            border-collapse: collapse;
        }
        .default-header-logos td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
    @php
        $certRecord = $pendaftaran->praktikum->certificate;

        $bgTemplate = $certRecord?->bg_template ?: ($pendaftaran->praktikum->bg_sertifikat_template ?: $setting->bg_sertifikat_template);
        $prefixNomor = $certRecord?->nomor_surat_prefix ?: ($pendaftaran->praktikum->nomor_surat_prefix ?: ($setting->nomor_surat_prefix ?: 'SERT/LAB-RPL/ITATS'));
        
        $namaKepalaLab = $certRecord?->nama_kepala_lab ?: ($setting->nama_kepala_lab ?? 'Nama Kepala Lab');
        $nipKepalaLab = $certRecord?->nip_kepala_lab ?: ($setting->nip_kepala_lab ?? '-');
        $ttdKepalaLab = $certRecord?->ttd_kepala_lab ?: $setting->ttd_kepala_lab;

        $namaKaprodi = $certRecord?->nama_kaprodi ?: ($setting->nama_kaprodi ?? 'Nama Kaprodi');
        $nipKaprodi = $certRecord?->nip_kaprodi ?: ($setting->nip_kaprodi ?? '-');
        $ttdKaprodi = $certRecord?->ttd_kaprodi ?: $setting->ttd_kaprodi;

        $bgBase64 = null;
        if ($bgTemplate && file_exists(storage_path('app/public/' . $bgTemplate))) {
            $type = pathinfo(storage_path('app/public/' . $bgTemplate), PATHINFO_EXTENSION);
            $data = file_get_contents(storage_path('app/public/' . $bgTemplate));
            $bgBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        $ttdKaprodiBase64 = null;
        if ($ttdKaprodi && file_exists(storage_path('app/public/' . $ttdKaprodi))) {
            $type = pathinfo(storage_path('app/public/' . $ttdKaprodi), PATHINFO_EXTENSION);
            $data = file_get_contents(storage_path('app/public/' . $ttdKaprodi));
            $ttdKaprodiBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        $ttdKepalaLabBase64 = null;
        if ($ttdKepalaLab && file_exists(storage_path('app/public/' . $ttdKepalaLab))) {
            $type = pathinfo(storage_path('app/public/' . $ttdKepalaLab), PATHINFO_EXTENSION);
            $data = file_get_contents(storage_path('app/public/' . $ttdKepalaLab));
            $ttdKepalaLabBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        $endDate = $certRecord?->tanggal_sertifikat ?: ($pendaftaran->praktikum->updated_at ?: \Carbon\Carbon::now());
    @endphp

    <div style="position: relative; width: 100%; height: 100%;">
        @if($bgBase64)
            {{-- Mode Template Kustom Gambar --}}
            <img src="{{ $bgBase64 }}" class="bg-template-img">
        @endif

        <div class="overlay-content" style="{{ !$bgBase64 ? 'padding-top: 35px;' : '' }}">
            
            @if(!$bgBase64)
                {{-- Header bawaan bergaya ITATS jika admin belum upload template --}}
                <table class="default-header-logos">
                    <tr>
                        <td style="text-align: left; width: 30%;">
                            <span style="font-weight: 900; font-size: 20px; color: #001f3f;">ITATS</span><br>
                            <span style="font-size: 8px; color: #64748b; font-weight: bold;">INSTITUT TEKNOLOGI ADHI TAMA SURABAYA</span>
                        </td>
                        <td style="text-align: center; width: 40%;">
                            <span style="font-weight: 900; font-size: 14px; color: #0284c7;">Teknik Informatika</span><br>
                            <span style="font-size: 9px; color: #475569;">Laboratorium Rekayasa Perangkat Lunak</span>
                        </td>
                        <td style="text-align: right; width: 30%;">
                            <span style="font-size: 10px; font-weight: bold; color: #001f3f;">LAB-RPL</span>
                        </td>
                    </tr>
                </table>
            @endif

            <div class="main-title">SERTIFIKAT KELULUSAN</div>
            <div class="cert-number">Nomor: {{ $prefixNomor }}/{{ $endDate->format('m/Y') }}</div>

            <div class="given-to">Diberikan kepada:</div>
            <div class="student-name">{{ $pendaftaran->praktikan->user->name }}</div>
            <div class="student-npm">NPM: {{ $pendaftaran->praktikan->npm }}</div>

            <div class="as-label">Sebagai:</div>
            <div class="status-badge">PESERTA LULUS</div>
            <div class="course-title">Praktikum {{ $pendaftaran->praktikum->nama_praktikum }}</div>
            <div class="course-sub">Periode {{ $pendaftaran->praktikum->periode_praktikum }}</div>

            <div class="location-date">Surabaya, {{ $endDate->isoFormat('D MMMM Y') }}</div>
            <div class="mengetahui-text">mengetahui:</div>

            <table class="signatures-table">
                <tr>
                    <td>
                        <div class="sig-title">Kepala Program Studi<br>Teknik Informatika</div>
                        <div class="sig-image-container">
                            @if($ttdKaprodiBase64)
                                <img src="{{ $ttdKaprodiBase64 }}" class="sig-image">
                            @else
                                <div style="height: 50px;"></div>
                            @endif
                        </div>
                        <div class="sig-name">{{ $namaKaprodi }}</div>
                        <div class="sig-nip">NIP. {{ $nipKaprodi }}</div>
                    </td>
                    <td>
                        <div class="sig-title">Kepala Laboratorium<br>Rekayasa Perangkat Lunak</div>
                        <div class="sig-image-container">
                            @if($ttdKepalaLabBase64)
                                <img src="{{ $ttdKepalaLabBase64 }}" class="sig-image">
                            @else
                                <div style="height: 50px;"></div>
                            @endif
                        </div>
                        <div class="sig-name">{{ $namaKepalaLab }}</div>
                        <div class="sig-nip">NIP. {{ $nipKepalaLab }}</div>
                    </td>
                </tr>
            </table>

        </div>
    </div>
</body>
</html>
