<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borang Permohonan Ladang Bridlot NAIMbif - {{ $application->no_rujukan }}</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 12mm 10mm 12mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.35;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 190mm;
            margin: 0 auto;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .logo-box {
            width: 130px;
            text-align: left;
            vertical-align: middle;
        }

        .logo-badge {
            display: inline-block;
            background: #166534;
            color: #fff;
            padding: 6px 10px;
            border-radius: 6px;
            font-weight: 900;
            font-size: 14px;
            letter-spacing: 0.5px;
        }

        .logo-badge span {
            color: #ef4444;
            font-style: italic;
        }

        .header-title-box {
            text-align: left;
            vertical-align: middle;
            padding-left: 10px;
        }

        .form-title {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            margin-bottom: 4px;
        }

        .header-notes {
            font-size: 8.5px;
            line-height: 1.25;
            color: #222;
        }

        .header-notes ul {
            margin: 0;
            padding-left: 14px;
        }

        .section-header {
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            text-decoration: underline;
            margin-top: 10px;
            margin-bottom: 6px;
            letter-spacing: 0.3px;
        }

        .field-row {
            margin-bottom: 5px;
            display: flex;
            align-items: baseline;
            flex-wrap: wrap;
        }

        .underline-val {
            display: inline-block;
            border-bottom: 1px solid #000;
            padding: 0 4px;
            font-weight: bold;
            min-height: 15px;
        }

        .matrix-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            margin-bottom: 8px;
            font-size: 9.5px;
        }

        .matrix-table th, .matrix-table td {
            border: 1px solid #000;
            padding: 3px 4px;
            text-align: center;
        }

        .matrix-table th {
            background-color: #f1f5f9;
            font-weight: bold;
        }

        .matrix-table td.breed-name {
            text-align: left;
            font-weight: bold;
            padding-left: 6px;
        }

        .matrix-table tr.total-row {
            font-weight: bold;
            background-color: #f8fafc;
        }

        .bottom-grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .bottom-box {
            width: 50%;
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
            font-size: 9.5px;
        }

        .box-title {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            margin-bottom: 6px;
            font-size: 10px;
        }

        .declaration-box {
            margin-top: 8px;
            font-size: 10px;
        }

        .sig-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 4px;
            text-align: right;
        }

        .sig-box {
            width: 250px;
            text-align: left;
            font-size: 10px;
        }

        /* Non-print toolbar */
        .toolbar {
            background: #0f172a;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .btn-print {
            background: #10b981;
            color: #fff;
            border: none;
            padding: 8px 16px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
        }

        .btn-back {
            color: #cbd5e1;
            text-decoration: none;
            font-size: 12px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: none;
            }
            .container {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Toolbar for Screen Users -->
        <div class="toolbar no-print">
            <div style="font-weight: bold;">
                <i class="fas fa-file-pdf" style="color: #fbbf24; margin-right: 6px;"></i> Paparan Cetakan Rasmi A4: {{ $application->no_rujukan }}
            </div>
            <div>
                <a href="{{ route('naimbif.public.check_status', ['carian' => $application->no_rujukan]) }}" class="btn-back" style="margin-right: 15px;">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button onclick="window.print()" class="btn-print">
                    <i class="fas fa-print"></i> Cetak / Simpan PDF
                </button>
            </div>
        </div>

        <!-- ==================================================== -->
        <!-- HEADER BORANG                                        -->
        <!-- ==================================================== -->
        <table class="header-table">
            <tr>
                <td class="logo-box">
                    <img src="{{ asset('images/naimbif-logo.png') }}" alt="NAIMbif" style="max-height: 55px; width: auto; display: block; margin-bottom: 2px;">
                    <div style="font-size: 8px; font-weight: bold; color: #166534;">
                        JPV NEGERI KELANTAN
                    </div>
                </td>
                <td class="header-title-box">
                    <div class="form-title">BORANG PERMOHONAN PENYERTAAN LADANG BRIDLOT NAIMbif</div>
                    <div class="header-notes">
                        <ul>
                            <li>Borang diberi secara percuma dan salinan fotostat dibenarkan</li>
                            <li>Borang ini boleh dimuat turun dari laman web JPVNK / Portal NAIMbif</li>
                            <li>Sila kembalikan borang yang lengkap diisi kepada Pejabat Perkhidmatan Veterinar Jajahan terdekat atau Ibu Pejabat Jabatan Perkhidmatan Veterinar Negeri Kelantan</li>
                        </ul>
                    </div>
                </td>
            </tr>
        </table>

        <!-- ==================================================== -->
        <!-- MAKLUMAT PESERTA                                     -->
        <!-- ==================================================== -->
        <div class="section-header">MAKLUMAT PESERTA</div>

        <div class="field-row">
            <strong>1. NAMA:</strong>
            <span class="underline-val" style="flex: 1; margin-left: 6px;">{{ $application->nama }}</span>
        </div>

        <div class="field-row" style="margin-top: 3px;">
            <strong>2. NO KAD PENGENALAN:</strong>
            <span class="underline-val" style="width: 200px; margin-left: 6px; margin-right: 15px;">{{ $application->formatted_no_kp }}</span>
            <strong>3. NO TELEFON:</strong>
            <span class="underline-val" style="flex: 1; margin-left: 6px;">{{ $application->no_telefon }}</span>
        </div>

        <div class="field-row" style="margin-top: 3px;">
            <strong>4. ALAMAT TETAP:</strong>
            <span class="underline-val" style="flex: 1; margin-left: 6px;">{{ $application->alamat_tetap }}</span>
        </div>

        <div class="field-row" style="margin-top: 3px;">
            <span style="width: 250px;"></span>
            <strong>POSKOD :</strong>
            <span class="underline-val" style="width: 80px; margin-left: 6px; margin-right: 20px; text-align: center;">{{ $application->poskod }}</span>
            <strong>JAJAHAN :</strong>
            <span class="underline-val" style="flex: 1; margin-left: 6px;">{{ $application->jajahan }}</span>
        </div>

        <div class="field-row" style="margin-top: 3px;">
            <strong>5. PENGALAMAN MENTERNAK:</strong>
            <span class="underline-val" style="width: 60px; margin: 0 6px; text-align: center;">{{ $application->pengalaman_menternak }}</span>
            <strong style="margin-right: 15px;">TAHUN</strong>

            <strong>6. STATUS PENTERNAKAN:</strong>
            <span style="margin-left: 8px;">
                @if($application->status_penternakan === 'Sepenuh Masa')
                    <strong><u>SEPENUH MASA</u></strong> / SAMPINGAN
                @else
                    SEPENUH MASA / <strong><u>SAMPINGAN</u></strong>
                @endif
            </span>
        </div>

        <div class="field-row" style="margin-top: 3px;">
            <strong>7. PERNAH MENGIKUTI SEBARANG KURSUS BERKAITAN?</strong>
            <span style="margin-left: 6px; margin-right: 10px;">
                @if($application->pernah_kursus)
                    <strong><u>YA</u></strong> / TIDAK
                @else
                    YA / <strong><u>TIDAK</u></strong>
                @endif
            </span>
            <span>JIKA YA, NYATAKAN BUTIRAN KURSUS:</span>
        </div>

        <div class="field-row" style="margin-top: 2px;">
            <span style="margin-left: 15px;">NAMA KURSUS:</span>
            <span class="underline-val" style="width: 220px; margin-left: 6px; margin-right: 15px;">{{ $application->nama_kursus ?? '-' }}</span>
            <span>ANJURAN:</span>
            <span class="underline-val" style="flex: 1; margin-left: 6px;">{{ $application->anjuran_kursus ?? '-' }}</span>
        </div>

        <div class="field-row" style="margin-top: 2px;">
            <span style="margin-left: 15px;">JIKA TIDAK, ADAKAH ANDA BERMINAT UNTUK MENYERTAI KURSUS YANG DIANJURKAN JPVNK?</span>
            <span style="margin-left: 8px;">
                @if(!$application->pernah_kursus && $application->berminat_kursus_jpvnk)
                    <strong><u>YA</u></strong> / TIDAK
                @elseif(!$application->pernah_kursus && !$application->berminat_kursus_jpvnk)
                    YA / <strong><u>TIDAK</u></strong>
                @else
                    YA / TIDAK
                @endif
            </span>
        </div>

        <!-- ==================================================== -->
        <!-- MAKLUMAT ASAS LADANG                                 -->
        <!-- ==================================================== -->
        <div class="section-header">MAKLUMAT ASAS LADANG</div>

        <div class="field-row">
            <strong>8. ALAMAT LADANG:</strong>
            <span style="margin-left: 4px;">JIKA BERLAINAN DARI ALAMAT DI ATAS, NYATAKAN;</span>
            <span class="underline-val" style="flex: 1; margin-left: 6px;">{{ $application->alamat_ladang ?: $application->alamat_tetap }}</span>
        </div>

        <div class="field-row" style="margin-top: 3px;">
            <span style="width: 250px;"></span>
            <strong>POSKOD :</strong>
            <span class="underline-val" style="width: 80px; margin-left: 6px; margin-right: 20px; text-align: center;">{{ $application->poskod_ladang ?: $application->poskod }}</span>
            <strong>JAJAHAN :</strong>
            <span class="underline-val" style="flex: 1; margin-left: 6px;">{{ $application->jajahan_ladang ?: $application->jajahan }}</span>
        </div>

        <div class="field-row" style="margin-top: 3px;">
            <strong>9. LOKASI GPS:</strong>
            <span style="margin-left: 6px;">LONGITUD [E]</span>
            <span class="underline-val" style="width: 150px; margin-left: 6px; margin-right: 20px; text-align: center;">{{ $application->gps_longitud ?? '-' }}</span>
            <span>LATITUD [N]</span>
            <span class="underline-val" style="width: 150px; margin-left: 6px; text-align: center;">{{ $application->gps_latitud ?? '-' }}</span>
        </div>

        <div class="field-row" style="margin-top: 3px;">
            <strong>10. STATUS TANAH:</strong>
            <span style="margin-left: 6px; margin-right: 10px;">
                @php $st = $application->status_tanah; @endphp
                {{ $st == 'Sendiri' ? ' [X] SENDIRI ' : ' [ ] SENDIRI ' }} /
                {{ $st == 'Sewa' ? ' [X] SEWA ' : ' [ ] SEWA ' }} /
                {{ $st == 'Kerajaan' ? ' [X] KERAJAAN ' : ' [ ] KERAJAAN ' }} /
                {{ $st == 'Lain-lain' ? ' [X] LAIN-LAIN' : ' [ ] LAIN-LAIN' }}
            </span>
            @if($st == 'Lain-lain')
                <span>NYATAKAN:</span>
                <span class="underline-val" style="flex: 1; margin-left: 6px;">{{ $application->status_tanah_lain }}</span>
            @endif
        </div>

        <div class="field-row" style="margin-top: 3px;">
            <strong>11. KELUASAN TANAH:</strong>
            <span class="underline-val" style="width: 60px; margin: 0 6px; text-align: center;">{{ $application->keluasan_tanah }}</span>
            <strong style="margin-right: 20px;">EKAR</strong>

            <strong>12. PADANG RAGUT:</strong>
            <span style="margin: 0 20px 0 6px;">
                @if($application->padang_ragut === 'Ada')
                    <strong><u>ADA</u></strong> / TIADA
                @else
                    ADA / <strong><u>TIADA</u></strong>
                @endif
            </span>

            <strong>13. BILANGAN PEKERJA:</strong>
            <span class="underline-val" style="width: 50px; margin: 0 6px; text-align: center;">{{ $application->bilangan_pekerja }}</span>
            <strong>ORANG</strong>
        </div>

        <!-- ==================================================== -->
        <!-- MAKLUMAT ASAS TERNAKAN                               -->
        <!-- ==================================================== -->
        <div class="section-header">MAKLUMAT ASAS TERNAKAN</div>

        <div class="field-row">
            <strong>14. PUNCA TERNAKAN:</strong>
            <span style="margin-left: 6px; margin-right: 25px;">
                @php $pt = $application->punca_ternakan; @endphp
                {{ $pt == 'Beli' ? ' [X] BELI ' : ' [ ] BELI ' }} /
                {{ $pt == 'Pawah' ? ' [X] PAWAH ' : ' [ ] PAWAH ' }}
                @if($pt == 'Lain-lain')
                    / <u>{{ $application->punca_ternakan_lain }}</u>
                @endif
            </span>

            <strong>15. KAEDAH PEMBIAKAN:</strong>
            <span style="margin-left: 6px;">
                @if($application->kaedah_pembiakan === 'Asli')
                    <strong><u>ASLI</u></strong> / PERMANIAN BERADAS
                @else
                    ASLI / <strong><u>PERMANIAN BERADAS</u></strong>
                @endif
            </span>
        </div>

        <div style="margin-top: 3px;">
            <strong>16.</strong>
        </div>

        <!-- Matrix Table -->
        <table class="matrix-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 28%; text-align: left; padding-left: 6px;">STOK TERNAKAN</th>
                    <th colspan="3">BETINA</th>
                    <th colspan="2">JANTAN</th>
                    <th rowspan="2" style="width: 12%;">JUMLAH</th>
                </tr>
                <tr>
                    <th style="width: 12%;">ANAK</th>
                    <th style="width: 12%;">DARA</th>
                    <th style="width: 12%;">INDUK</th>
                    <th style="width: 12%;">ANAK JANTAN</th>
                    <th style="width: 12%;">PEJANTAN</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $standardBreeds = ['CHAROLAIS', 'BELGIAN BLUE', "BLONDE D'AQUITAINE", 'LIMOUSIN', 'KEDAH KELANTAN', 'LAIN-LAIN'];
                    $totBAnak = 0; $totBDara = 0; $totBInduk = 0; $totJAnak = 0; $totJPej = 0; $grand = 0;
                @endphp

                @foreach($standardBreeds as $b)
                    @php
                        $inv = $inventories[$b] ?? null;
                        $ba = $inv ? $inv->betina_anak : 0;
                        $bd = $inv ? $inv->betina_dara : 0;
                        $bi = $inv ? $inv->betina_induk : 0;
                        $ja = $inv ? $inv->jantan_anak : 0;
                        $jp = $inv ? $inv->jantan_pejantan : 0;
                        $rowTot = $ba + $bd + $bi + $ja + $jp;

                        $totBAnak += $ba; $totBDara += $bd; $totBInduk += $bi;
                        $totJAnak += $ja; $totJPej += $jp; $grand += $rowTot;
                    @endphp
                    <tr>
                        <td class="breed-name">
                            {{ $b }}
                            @if($b === 'LAIN-LAIN' && $inv && $inv->nama_baka_lain)
                                : <u>{{ $inv->nama_baka_lain }}</u>
                            @endif
                        </td>
                        <td>{{ $ba ?: '-' }}</td>
                        <td>{{ $bd ?: '-' }}</td>
                        <td>{{ $bi ?: '-' }}</td>
                        <td>{{ $ja ?: '-' }}</td>
                        <td>{{ $jp ?: '-' }}</td>
                        <td style="font-weight: bold;">{{ $rowTot ?: '-' }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td class="breed-name">JUMLAH</td>
                    <td>{{ $totBAnak }}</td>
                    <td>{{ $totBDara }}</td>
                    <td>{{ $totBInduk }}</td>
                    <td>{{ $totJAnak }}</td>
                    <td>{{ $totJPej }}</td>
                    <td style="font-weight: bold; background-color: #e2e8f0;">{{ $grand }}</td>
                </tr>
            </tbody>
        </table>

        <!-- ==================================================== -->
        <!-- PENGAKUAN                                            -->
        <!-- ==================================================== -->
        <div class="declaration-box">
            <div style="font-weight: bold; text-transform: uppercase;">PENGAKUAN</div>
            <div>SAYA MENGAKUI BAHAWA BUTIRAN DIATAS ADALAH BENAR.</div>

            <div class="sig-container">
                <div class="sig-box">
                    <div style="margin-bottom: 2px;">
                        TANDATANGAN :
                        @if($application->tandatangan)
                            <img src="{{ $application->tandatangan }}" alt="Tandatangan" style="max-height: 35px; vertical-align: middle; margin-left: 6px;">
                        @else
                            <span style="display: inline-block; width: 120px; border-bottom: 1px dotted #000; margin-left: 6px;">&nbsp;</span>
                        @endif
                    </div>
                    <div>
                        TARIKH : <span style="font-weight: bold; margin-left: 6px;">{{ $application->tarikh_permohonan ? $application->tarikh_permohonan->format('d/m/Y') : date('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================================================== -->
        <!-- UNTUK KEGUNAAN PEJABAT (JAJAHAN) & ULASAN NEGERI     -->
        <!-- ==================================================== -->
        <table class="bottom-grid">
            <tr>
                <!-- UNTUK KEGUNAAN PEJABAT (JAJAHAN) -->
                <td class="bottom-box" style="border-right: 1px solid #000;">
                    <div class="box-title">UNTUK KEGUNAAN PEJABAT (JAJAHAN)</div>
                    <div style="line-height: 1.45;">
                        <div>
                            <strong>ID premis :</strong>
                            <span class="underline-val" style="min-width: 140px;">{{ $application->id_premis ?? '' }}</span>
                        </div>
                        <div>
                            <strong>Status :</strong>
                            <span style="margin-left: 6px;">
                                {{ $application->status_kelengkapan == 'Lengkap' ? '[X] Lengkap' : '[ ] Lengkap' }} /
                                {{ $application->status_kelengkapan == 'Tidak Lengkap' ? '[X] Tidak Lengkap' : '[ ] Tidak Lengkap' }}
                            </span>
                        </div>
                        <div>
                            <strong>Permohonan :</strong>
                            <span style="margin-left: 6px;">
                                {{ $application->syor_permohonan == 'Disokong' ? '[X] Disokong' : '[ ] Disokong' }} /
                                {{ $application->syor_permohonan == 'Tidak disokong' ? '[X] Tidak disokong' : '[ ] Tidak disokong' }}
                            </span>
                        </div>
                        <div>
                            <strong>Pegawai Penyiasat :</strong>
                            <span class="underline-val" style="min-width: 130px;">{{ $application->pegawai_penyiasat ?? '' }}</span>
                        </div>
                    </div>
                </td>

                <!-- ULASAN NEGERI -->
                <td class="bottom-box">
                    <div class="box-title">ULASAN NEGERI</div>
                    <div style="line-height: 1.45;">
                        <div>
                            <strong>Status :</strong>
                            <span style="margin-left: 6px;">
                                {{ $application->status_negeri == 'Lulus' ? '[X] Lulus' : '[ ] Lulus' }} /
                                {{ $application->status_negeri == 'Gagal' ? '[X] Gagal' : '[ ] Gagal' }}
                            </span>
                        </div>
                        <div>
                            <strong>No Rujukan :</strong>
                            <span class="underline-val" style="min-width: 140px;">{{ $application->no_rujukan_negeri ?? '' }}</span>
                        </div>
                        <div>
                            <strong>Ulasan :</strong>
                            <span class="underline-val" style="min-width: 160px; display: block; margin-top: 2px;">{{ $application->ulasan_negeri ?? '' }}</span>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Security / Tracking Footer (Print Only) -->
        <div style="margin-top: 8px; font-size: 7.5px; color: #64748b; display: flex; justify-content: space-between;">
            <div>No. Rujukan Sistem: {{ $application->no_rujukan }}</div>
            <div>Dicetak secara digital pada: {{ date('d/m/Y H:i:s') }}</div>
            <div>Jabatan Perkhidmatan Veterinar Negeri Kelantan</div>
        </div>
    </div>

</body>
</html>
