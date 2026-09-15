<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>DEKLARASI STATUS HAIWAN (RUMINAN) - {{ $pemindahan->no_rujukan }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @page {
            size: A4 portrait;
            margin: 6mm 10mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #f1f5f9;
        }
        .form-page {
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.2;
            font-size: 8.5pt;
            background-color: #ffffff;
            color: #000;
            page-break-inside: avoid;
        }
        table, th, td {
            border: 1px solid #000;
        }
        @media print {
            .no-print { display: none !important; }
            html, body {
                width: 210mm !important;
                background: white !important;
                margin: 0 !important;
                padding: 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .form-page {
                width: 100% !important;
                max-width: 210mm !important;
                box-shadow: none !important;
                border: none !important;
                margin: 0 auto !important;
                padding: 3mm 6mm !important;
                page-break-inside: avoid !important;
            }
        }
    </style>
</head>
<body class="p-4 sm:p-6 flex flex-col items-center">

    <!-- Screen-Only Actions -->
    <div class="no-print mb-6 flex flex-wrap gap-3">
        <button onclick="window.print()" class="px-6 py-2.5 bg-cyan-700 hover:bg-cyan-800 text-white font-bold text-sm rounded-xl shadow-lg transition flex items-center gap-2">
            <i class="fa-solid fa-print"></i>
            <span>Cetak Deklarasi Status Haiwan (DVS/DSHR)</span>
        </button>
        <a href="{{ route('eptr.pemindahan.show', $pemindahan->id) }}" class="px-4 py-2.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-sm rounded-xl transition">
            Kembali ke Butiran Permohonan
        </a>
    </div>

    @php
        $tarikhMohonStr = $pemindahan->tarikh_permohonan ? $pemindahan->tarikh_permohonan->format('d.m.Y') : date('d.m.Y');
        $tarikhPindahStr = $pemindahan->tarikh_jangka_pindah ? $pemindahan->tarikh_jangka_pindah->format('d.m.Y') : date('d.m.Y', strtotime('+7 days'));
        $fmdP1 = $pemindahan->tarikh_fmd_p1 ? $pemindahan->tarikh_fmd_p1->format('d.m.Y') : '18.5.2026';
        $fmdP2 = $pemindahan->tarikh_fmd_p2 ? $pemindahan->tarikh_fmd_p2->format('d.m.Y') : '16.6.2026';
        $fmdBooster = $pemindahan->tarikh_fmd_booster ? $pemindahan->tarikh_fmd_booster->format('d.m.Y') : '';
        $lsd = $pemindahan->tarikh_lsd ? $pemindahan->tarikh_lsd->format('d.m.Y') : '';
        $spesies = strtoupper($pemindahan->jenis_ternakan ?? 'LEMBU');
        $isSembelihan = strtoupper($pemindahan->tujuan_pemindahan) === 'SEMBELIHAN';
    @endphp

    <!-- Form Container -->
    <div class="max-w-[210mm] w-full">
        <div class="form-page p-6 sm:p-8 shadow-2xl bg-white border border-slate-200">

            <!-- Top Header & Form Ref -->
            <div class="flex justify-between items-start mb-1">
                <div></div>
                <div class="font-sans font-bold text-[8pt] text-right">
                    DVS/DSHR/0117/9/2021
                </div>
            </div>

            <!-- Title Box -->
            <div class="border border-black text-center py-1 font-bold text-[10pt] uppercase tracking-wide mb-2 bg-slate-50">
                DEKLARASI STATUS HAIWAN (RUMINAN)
            </div>

            <!-- 3-Column Top Info Table -->
            <table class="w-full text-left text-[8pt] mb-2 border-collapse">
                <tbody>
                    <tr class="align-top">
                        <!-- Box 1: Doktor Veterinar -->
                        <td class="p-1.5 w-1/3">
                            <div class="font-bold uppercase text-[7.5pt] mb-1">MAKLUMAT DOKTOR VETERINAR LADANG ATAU SWASTA</div>
                            <div class="space-y-0.5">
                                <div>Nama Doktor Veterinar : <span>{{ $pemindahan->nama_doktor_veterinar ?: '' }}</span></div>
                                <div>No. Kad Pengenalan : <span>{{ $pemindahan->ic_doktor_veterinar ?: '' }}</span></div>
                                <div>Telefon : <span>{{ $pemindahan->tel_doktor_veterinar ?: '' }}</span></div>
                                <div>No. Pendaftaran Akta Doktor Veterinar 1974 : <span>{{ $pemindahan->no_pendaftaran_doktor ?: '' }}</span></div>
                            </div>
                        </td>

                        <!-- Box 2: Pemilik Ladang -->
                        <td class="p-1.5 w-1/3">
                            <div class="font-bold uppercase text-[7.5pt] mb-1">MAKLUMAT PEMILIK TERNAKAN/LADANG</div>
                            <div class="space-y-0.5">
                                <div>Nama Tuan Punya : <strong class="uppercase">{{ $pemindahan->pemohon_nama }}</strong></div>
                                <div>Alamat Ladang : <span class="uppercase">{{ $pemindahan->pemohon_alamat }}</span></div>
                                <div>Tel : <span>{{ $pemindahan->pemohon_tel ?: '-' }}</span> &nbsp;&nbsp;No Faks : </div>
                                <div>Tarikh pemindahan : <strong>{{ $tarikhPindahStr }}</strong></div>
                                <div>Alamat tempat pemindahan : <span class="uppercase">{{ $pemindahan->penerima_nama }}</span></div>
                            </div>
                        </td>

                        <!-- Box 3: Maklumat Rujukan -->
                        <td class="p-1.5 w-1/3">
                            <div class="space-y-1">
                                <div><strong>NO. RUJUKAN:</strong> <span class="font-mono font-bold">{{ $pemindahan->no_rujukan }}</span></div>
                                <div><strong>ID Premis:</strong> <span class="font-mono font-bold uppercase">{{ $pemindahan->pemohon_id_premis ?: '-' }}</span></div>
                                <div><strong>No. MyGAP:</strong> <span>{{ $pemindahan->no_mygap ?: '' }}</span></div>
                                <div class="pt-1"><strong>Tujuan Pemindahan :</strong> <strong class="uppercase">{{ $pemindahan->tujuan_pemindahan }}</strong></div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Spesies, Bilangan, No Lori Table -->
            <table class="w-full text-left text-[8pt] mb-2 border-collapse font-bold uppercase">
                <tbody>
                    <tr>
                        <td class="p-1.5 w-1/3">Spesis : <span class="font-normal">{{ $spesies }}</span></td>
                        <td class="p-1.5 w-1/3">Bilangan ternakan per lori : <span class="font-normal">{{ $pemindahan->format_ringkas_jantina }}</span></td>
                        <td class="p-1.5 w-1/3">No. Lori: <span class="font-mono">{{ $pemindahan->no_kenderaan ?: '-' }}</span></td>
                    </tr>
                </tbody>
            </table>

            <!-- Seksyen Kegunaan Sembelihan -->
            <table class="w-full text-left text-[8pt] mb-2 border-collapse">
                <thead>
                    <tr class="bg-slate-50 font-bold text-center">
                        <th colspan="2" class="py-0.5">UNTUK KEGUNAAN SEMBELIHAN</th>
                    </tr>
                </thead>
                <tbody class="align-top">
                    <tr>
                        <td class="p-1.5 w-1/2">
                            <div>Nama &amp; Alamat Rumah sembelih : <span>{{ $isSembelihan ? $pemindahan->nama_rumah_sembelih : '' }}</span></div>
                        </td>
                        <td class="p-1.5 w-1/2 space-y-0.5">
                            <div>Tarikh dan masa ternakan keluar dari ladang : <span>{{ $isSembelihan && $pemindahan->tarikh_keluar_ladang ? $pemindahan->tarikh_keluar_ladang->format('d.m.Y H:i') : $tarikhPindahStr }}</span></div>
                            <div>Tarikh ternakan akan disembelih : <span>{{ $isSembelihan && $pemindahan->tarikh_sembelih ? $pemindahan->tarikh_sembelih->format('d.m.Y') : '' }}</span></div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- BAHAGIAN A: STATUS PENYAKIT -->
            <div class="bg-slate-700 text-white font-bold px-2 py-0.5 text-[8pt] tracking-wider uppercase mb-1">
                BAHAGIAN A: STATUS PENYAKIT
            </div>

            <div class="border border-black p-2 text-[7.5pt] mb-2 space-y-1 leading-tight">
                <div class="font-bold underline">Deklarasi:</div>
                <div class="flex gap-2 items-start">
                    <span>1)</span>
                    <div>Tiada tanda klinikal dan pengesanan jangkitan <em>Foot and Mouth Disease</em> (FMD), <em>Brucellosis</em>, <em>Tuberculosis</em> (TB) dan <em>Lumpy Skin Disease</em> (LSD) bagi ruminan besar.</div>
                </div>
                <div class="flex gap-2 items-start">
                    <span>2)</span>
                    <div>Tiada tanda klinikal dan pengesanan jangkitan <em>Foot and Mouth Disease</em> (FMD), <em>Brucellosis</em>, <em>Tuberculosis</em> (TB), <em>Peste Des Petits Ruminants</em> (PPR) dan <em>Caseous Lymphadenitis</em> (CLA) bagi ruminan kecil.</div>
                </div>
                <div class="flex gap-2 items-start">
                    <span>3)</span>
                    <div class="space-y-0.5 w-full">
                        <div>Ternakan telah diberi vaksin:</div>
                        <div class="pl-4 space-y-0.5">
                            <div class="flex items-center gap-1.5">
                                <span class="font-mono font-bold">[✓]</span>
                                <div>FMD Tarikh suntikan : (P1) <strong class="font-mono">{{ $fmdP1 }}</strong> &nbsp;&nbsp;(P2) <strong class="font-mono">{{ $fmdP2 }}</strong> &nbsp;&nbsp;(Booster) <strong class="font-mono">{{ $fmdBooster }}</strong></div>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="font-mono font-bold">[{{ $lsd ? '✓' : ' ' }}]</span>
                                <div>LSD Tarikh suntikan : <strong class="font-mono">{{ $lsd ?: '..........................................' }}</strong></div>
                            </div>
                            <div class="pt-0.5">
                                Lain-lain vaksin (nyatakan): <span class="border-b border-dotted border-black inline-block w-48">{{ $pemindahan->lain_vaksin_nama }}</span> &nbsp;Tarikh suntikan: <span class="border-b border-dotted border-black inline-block w-28">{{ $pemindahan->lain_vaksin_tarikh ? $pemindahan->lain_vaksin_tarikh->format('d.m.Y') : '' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BAHAGIAN B: TEMPOH PENARIKAN BALIK UBATAN VETERINAR -->
            <div class="bg-slate-700 text-white font-bold px-2 py-0.5 text-[8pt] tracking-wider uppercase mb-1">
                BAHAGIAN B: TEMPOH PENARIKAN BALIK UBATAN VETERINAR
            </div>

            <div class="border border-black p-2 text-[7.5pt] mb-2 space-y-1 leading-tight">
                <div class="font-bold underline">Deklarasi:</div>
                <div>Semua ternakan ini telah melepasi tempoh penarikan balik ubatan veterinar seperti yang disarankan oleh pengeluar.</div>
                <div class="pt-0.5">
                    Tarikh rawatan terakhir : <span class="border-b border-dotted border-black inline-block w-36">{{ $pemindahan->tarikh_rawatan_terakhir ? $pemindahan->tarikh_rawatan_terakhir->format('d.m.Y') : '...................................' }}</span> &nbsp;&nbsp;&nbsp;&nbsp;Nama/ Jenis Ubatan yang digunakan : <span class="border-b border-dotted border-black inline-block w-52">{{ $pemindahan->nama_ubat_terakhir ?: '................................................' }}</span>
                </div>
                <div>
                    Cara rawatan : Suntikan/ Minuman / Makanan/ Lain-lain................................
                </div>
            </div>

            <!-- BAHAGIAN C / PENGESAHAN -->
            <div class="border border-black p-2 text-[8pt] mb-2">
                <div class="font-bold underline mb-1 text-[7.5pt]">Deklarasi:</div>
                <div class="text-center font-bold text-[8pt] mb-2">
                    Saya dengan ini mengesahkan kenyataan di atas adalah benar dan tepat
                </div>

                <div class="grid grid-cols-2 gap-8 items-end pt-2">
                    <div>
                        <div class="pt-4">.......................................................</div>
                        <div class="font-bold uppercase pt-0.5">{{ $pemindahan->pemohon_nama }}</div>
                        <div class="text-[7pt] leading-tight text-slate-700">
                            **Tandatangan Veterinawan Ladang/<br>
                            Tuan Punya Ternakan/Ladang (**tandakan<br>
                            Cop rasmi:
                        </div>
                    </div>

                    <div class="space-y-1 text-[7.5pt]">
                        <div>No. Telefon/Emel: <span class="font-bold">{{ $pemindahan->pemohon_tel ?: '-' }}</span></div>
                        <div>Tarikh: <span>{{ $tarikhMohonStr }}</span></div>
                    </div>
                </div>
            </div>

            <!-- Footer Notes -->
            <div class="text-[7.5pt] italic text-slate-800 space-y-0.5">
                <div>Borang ini hendaklah ditandatangani oleh Veterinawan Ladang atau Tuan Punya Ternakan/Ladang</div>
                <div class="font-bold text-center uppercase tracking-wide pt-1">
                    Sah Untuk Satu Perjalanan Sahaja
                </div>
            </div>

        </div>
    </div>

</body>
</html>
