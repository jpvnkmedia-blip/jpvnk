<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekod Aktiviti #{{ $actionList->no_bil }} - JPVNK</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.5cm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11.5px;
            color: #111;
            margin: 0;
            padding: 0;
            background: #fff;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 16px;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            font-weight: 800;
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
            color: #666;
        }
        .ref-box {
            display: flex;
            justify-content: space-between;
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 10px 14px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 11px;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            background: #e6f4ea;
            color: #137333;
            padding: 6px 10px;
            border-left: 4px solid #137333;
            margin: 18px 0 10px 0;
        }
        table.meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.meta-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }
        table.meta-table td.label {
            width: 25%;
            font-weight: bold;
            color: #555;
            text-transform: uppercase;
            font-size: 10px;
        }
        table.meta-table td.val {
            width: 75%;
            font-weight: 500;
        }
        .content-box {
            background: #fafafa;
            border: 1px solid #e0e0e0;
            padding: 14px;
            border-radius: 6px;
            white-space: pre-line;
            font-size: 12px;
            line-height: 1.6;
        }
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .sig-box {
            width: 45%;
            text-align: center;
        }
        .sig-line {
            border-bottom: 1px solid #000;
            margin-top: 60px;
            margin-bottom: 6px;
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
        <h2>BORANG LOG / DAIRI AKTIVITI PEGAWAI (ACTION LIST)</h2>
        <p>Pejabat Perkhidmatan Veterinar Jajahan {{ $actionList->jajahan }}</p>
    </div>

    <div class="ref-box">
        <div><strong>NO. RUJUKAN:</strong> {{ $actionList->no_bil }}</div>
        <div><strong>TARIKH:</strong> {{ $actionList->tarikh_formatted }}</div>
        <div><strong>STATUS:</strong> <span style="text-transform: uppercase;">{{ $actionList->status }}</span></div>
    </div>

    <div class="section-title">1. MAKLUMAT UTAMA AKTIVITI</div>
    <table class="meta-table">
        <tr>
            <td class="label">Tajuk Aktiviti</td>
            <td class="val"><strong>{{ $actionList->tajuk_aktiviti ?: '-' }}</strong></td>
        </tr>
        <tr>
            <td class="label">Kategori Aktiviti</td>
            <td class="val">{{ $actionList->kategori_label }}</td>
        </tr>
        <tr>
            <td class="label">Pejabat Jajahan</td>
            <td class="val">Jajahan {{ $actionList->jajahan }}</td>
        </tr>
        <tr>
            <td class="label">Tarikh & Waktu</td>
            <td class="val">{{ $actionList->tarikh_formatted }} @if($actionList->masa_mula || $actionList->masa_selesai) ({{ $actionList->masa_mula }} - {{ $actionList->masa_selesai }}) @endif</td>
        </tr>
        <tr>
            <td class="label">Lokasi / Premis</td>
            <td class="val">{{ $actionList->lokasi ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">Pegawai Bertugas</td>
            <td class="val">{{ $actionList->nama_pegawai ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tahap Keutamaan</td>
            <td class="val">{{ $actionList->keutamaan }}</td>
        </tr>
    </table>

    <div class="section-title">2. PERINCIAN & CATATAN AKTIVITI</div>
    <div class="content-box">
        {{ $actionList->maklumat_aktiviti }}
    </div>

    @if($actionList->tindakan_susulan)
        <div class="section-title" style="background: #fef7e0; color: #b06000; border-left-color: #b06000;">3. TINDAKAN SUSULAN</div>
        <div class="content-box" style="background: #fffdf5; border-color: #fde68a;">
            {{ $actionList->tindakan_susulan }}
        </div>
    @endif

    <div class="signature-section">
        <div class="sig-box">
            <p>Disediakan Oleh:</p>
            <div class="sig-line"></div>
            <p><strong>{{ $actionList->nama_pegawai ?: ($actionList->creator->name ?? 'Pegawai Bertugas') }}</strong><br>Pejabat Perkhidmatan Veterinar Jajahan {{ $actionList->jajahan }}</p>
        </div>
        <div class="sig-box">
            <p>Disahkan / Disemak Oleh:</p>
            <div class="sig-line"></div>
            <p><strong>( Pegawai Veterinar Jajahan / Ketua Seksyen )</strong><br>Jabatan Perkhidmatan Veterinar Negeri Kelantan</p>
        </div>
    </div>

</body>
</html>
