<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Aktiviti Pegawai (Action List) - JPVNK</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1.2cm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #111;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 15px;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 0.5px;
        }
        .header h2 {
            font-size: 13px;
            margin: 0 0 4px 0;
            font-weight: bold;
            color: #333;
        }
        .header p {
            margin: 2px 0;
            font-size: 10px;
            color: #555;
        }
        .meta-bar {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 10px;
            font-weight: bold;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        th, td {
            border: 1px solid #444;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            text-align: center;
        }
        .text-center { text-align: center; }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid #ccc;
        }
        .status-selesai { background: #e6f4ea; color: #137333; border-color: #ceead6; }
        .status-tindakan { background: #e8f0fe; color: #1a73e8; border-color: #d2e3fc; }
        .status-perancangan { background: #fef7e0; color: #b06000; border-color: #feefc3; }
        .footer {
            margin-top: 25px;
            font-size: 9px;
            color: #666;
            display: flex;
            justify-content: space-between;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }
        .no-print {
            position: fixed;
            top: 15px;
            right: 15px;
            background: #047857;
            color: #fff;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            font-size: 12px;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <a href="javascript:window.print()" class="no-print">🖨️ Cetak / Simpan PDF</a>

    <div class="header">
        <h1>Jabatan Perkhidmatan Veterinar Negeri Kelantan (JPVNK)</h1>
        <h2>LOG & DAIRI AKTIVITI ADMIN / PEGAWAI (ACTION LIST)</h2>
        <p>Laporan Rekod Aktiviti Pentadbiran, Penguatkuasaan & Perkhidmatan Veterinar</p>
    </div>

    <div class="meta-bar">
        <div>
            <span>JAJAHAN: {{ strtoupper($selectedJajahan) }}</span>
            @if($tarikhMula || $tarikhAkhir)
                <span style="margin-left: 15px;">TEMPOH: {{ $tarikhMula ? \Carbon\Carbon::parse($tarikhMula)->format('d/m/Y') : 'Awal' }} HINGGA {{ $tarikhAkhir ? \Carbon\Carbon::parse($tarikhAkhir)->format('d/m/Y') : 'Terkini' }}</span>
            @endif
        </div>
        <div>
            <span>JUMLAH REKOD: {{ $actionLists->count() }} AKTIVITI</span> |
            <span>TARIKH CETAKAN: {{ date('d/m/Y H:i A') }}</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">Bil</th>
                <th style="width: 8%;">No. Bil</th>
                <th style="width: 9%;">Tarikh & Masa</th>
                <th style="width: 10%;">Jajahan & Lokasi</th>
                <th style="width: 14%;">Kategori & Tajuk Aktiviti</th>
                <th style="width: 27%;">Perincian / Maklumat Aktiviti</th>
                <th style="width: 14%;">Pegawai Bertugas</th>
                <th style="width: 7%;">Status</th>
                <th style="width: 7%;">Keutamaan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($actionLists as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center" style="font-weight: bold;">{{ $item->no_bil }}</td>
                    <td>
                        <strong>{{ $item->tarikh ? \Carbon\Carbon::parse($item->tarikh)->format('d/m/Y') : '-' }}</strong>
                        @if($item->masa_mula || $item->masa_selesai)
                            <div style="font-size: 9px; color: #555;">{{ $item->masa_mula }} - {{ $item->masa_selesai }}</div>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $item->jajahan }}</strong>
                        @if($item->lokasi)
                            <div style="font-size: 9px; color: #444;">{{ $item->lokasi }}</div>
                        @endif
                    </td>
                    <td>
                        <span style="font-size: 9px; color: #047857; font-weight: bold; text-transform: uppercase;">[{{ $item->kategori_label }}]</span>
                        <div style="font-weight: bold; margin-top: 2px;">{{ $item->tajuk_aktiviti ?: '-' }}</div>
                    </td>
                    <td>
                        <div style="white-space: pre-line;">{{ $item->maklumat_aktiviti }}</div>
                        @if($item->tindakan_susulan)
                            <div style="margin-top: 4px; padding-top: 4px; border-top: 1px dashed #ccc; font-size: 9px; color: #b45309;">
                                <strong>Susulan:</strong> {{ $item->tindakan_susulan }}
                            </div>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $item->nama_pegawai ?: '-' }}</strong>
                        @if($item->creator)
                            <div style="font-size: 8px; color: #666;">Direkod: {{ $item->creator->name }}</div>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $item->status === 'Selesai' ? 'status-selesai' : ($item->status === 'Dalam Tindakan' ? 'status-tindakan' : 'status-perancangan') }}">
                            {{ $item->status }}
                        </span>
                    </td>
                    <td class="text-center">
                        <strong>{{ $item->keutamaan }}</strong>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px; color: #666;">
                        Tiada rekod aktiviti ditemui bagi kriteria pilihan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div>Sistem Pengurusan Jabatan Perkhidmatan Veterinar Negeri Kelantan (JPVNK) - Action List</div>
        <div>Muka Surat Cetakan Laporan Log Aktiviti Pentadbiran</div>
    </div>

</body>
</html>
