<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Daftar Tamu Lab RPL</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            color: #1a1a2e;
            margin: 15px 20px;
        }
        .header {
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 3px solid #001f3f;
            padding-bottom: 10px;
        }
        .header-table {
            width: 100%;
        }
        .header-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }
        .header-logo-left {
            width: 18%;
            text-align: left;
        }
        .header-logo-left img {
            width: 70px;
        }
        .header-center {
            width: 64%;
            text-align: center;
        }
        .header-center h1 {
            font-size: 14px;
            font-weight: bold;
            color: #001f3f;
            margin: 0 0 3px 0;
            text-transform: uppercase;
        }
        .header-center p {
            margin: 1px 0;
            color: #555;
            font-size: 8px;
        }
        .header-logo-right {
            width: 18%;
            text-align: right;
        }
        .header-logo-right img {
            width: 60px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        th {
            background-color: #001f3f;
            color: white;
            font-weight: bold;
            font-size: 7px;
            text-transform: uppercase;
            padding: 7px 5px;
            text-align: left;
        }
        td {
            padding: 5px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 7.5px;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .status-active {
            color: #059669;
            font-weight: bold;
        }
        .status-completed {
            color: #6b7280;
        }
        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
            font-size: 7px;
            color: #999;
        }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td class="header-logo-left">
                <img src="{{ public_path('image/logo-itats-biru.jpg') }}" alt="ITATS">
            </td>
            <td class="header-center">
                <h1>Laporan Daftar Tamu</h1>
                <p>Laboratorium Rekayasa Perangkat Lunak - ITATS</p>
                <p>Periode: {{ $periode }}</p>
                <p>Dicetak: {{ now()->translatedFormat('d M Y H:i') }} WIB</p>
            </td>
            <td class="header-logo-right">
                <img src="{{ public_path('image/logo-RPL.png') }}" alt="Lab RPL">
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Tamu</th>
                <th class="text-center">Jumlah</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Jam Keluar</th>
                <th>Tujuan Aktivitas</th>
                <th>Kondisi Lab</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($guestVisits as $index => $visit)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $visit->guest_name }}</td>
                    <td class="text-center">{{ $visit->guest_count }} org</td>
                    <td>{{ \Carbon\Carbon::parse($visit->visit_date)->translatedFormat('d M Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($visit->started_at)->format('H:i') }}</td>
                    <td>{{ $visit->ended_at ? \Carbon\Carbon::parse($visit->ended_at)->format('H:i') : '-' }}</td>
                    <td>{{ $visit->activity_purpose }}</td>
                    <td>{{ $visit->lab_condition }}</td>
                    <td class="text-center {{ $visit->ended_at ? 'status-completed' : 'status-active' }}">
                        {{ $visit->ended_at ? 'Selesai' : 'Aktif' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 30px; color: #999;">
                        Tidak ada data tamu untuk periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Laporan Daftar Tamu Lab RPL - ITATS | Dicetak {{ now()->translatedFormat('d M Y H:i') }} WIB
    </div>
</body>
</html>
