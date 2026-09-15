<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>LAMPIRAN SENARAI PENGENALAN TERNAKAN - {{ $pemindahan->no_rujukan }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 12mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #f1f5f9;
        }
        .form-page {
            font-family: 'Times New Roman', Times, serif;
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
                padding: 4mm 8mm !important;
                page-break-inside: avoid !important;
            }
        }
    </style>
</head>
<body class="p-4 sm:p-6 flex flex-col items-center">

    <!-- Screen-Only Actions -->
    <div class="no-print mb-6 flex flex-wrap gap-3">
        <button onclick="window.print()" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-sm rounded-xl shadow-lg transition flex items-center gap-2">
            <i class="fa-solid fa-print"></i>
            <span>Cetak Lampiran Senarai No. Tag</span>
        </button>
        <a href="{{ route('eptr.pemindahan.show', $pemindahan->id) }}" class="px-4 py-2.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-sm rounded-xl transition">
            Kembali ke Butiran Permohonan
        </a>
    </div>

    @php
        $pejabat = $pemindahan->pejabat_info;
        $jajahan = $pejabat['jajahan'];
        $tarikhMohonStr = $pemindahan->tarikh_permohonan ? $pemindahan->tarikh_permohonan->format('d.m.Y') : date('d.m.Y');
        $fmdP1 = $pemindahan->tarikh_fmd_p1 ? $pemindahan->tarikh_fmd_p1->format('d.m.Y') : '18.5.2026';
        $fmdP2 = $pemindahan->tarikh_fmd_p2 ? $pemindahan->tarikh_fmd_p2->format('d.m.Y') : '16.6.2026';
        $fmdBooster = $pemindahan->tarikh_fmd_booster ? $pemindahan->tarikh_fmd_booster->format('d.m.Y') : '';
        $lsd = $pemindahan->tarikh_lsd ? $pemindahan->tarikh_lsd->format('d.m.Y') : '';
        $tagList = $pemindahan->senarai_tag_list;
    @endphp

    <!-- Form Container -->
    <div class="max-w-[210mm] w-full">
        <div class="form-page p-8 sm:p-10 shadow-2xl bg-white border border-slate-200 text-[9.5pt] leading-normal">

            <!-- Top Header -->
            <div class="flex justify-between items-start mb-2">
                <div class="font-bold text-[11pt] uppercase tracking-wide">
                    LAMPIRAN
                </div>
                <div class="font-mono font-bold text-[10pt]">
                    NO. RUJUKAN : {{ $pemindahan->no_rujukan }}
                </div>
            </div>

            <!-- Title -->
            <div class="text-[12pt] font-bold uppercase tracking-wide mb-2 underline text-left">
                SENARAI PENGENALAN TERNAKAN
            </div>

            <!-- Ternakan Summary Info -->
            <div class="text-[9.5pt] font-bold uppercase space-y-1 mb-3">
                <div class="grid grid-cols-12">
                    <div class="col-span-2">TERNAKAN</div>
                    <div class="col-span-10">: {{ $pemindahan->jenis_ternakan ?? 'LEMBU' }}</div>
                </div>
                <div class="grid grid-cols-12">
                    <div class="col-span-2">BIL</div>
                    <div class="col-span-10">: {{ $pemindahan->format_ringkas_jantina }}</div>
                </div>
                <div class="grid grid-cols-12">
                    <div class="col-span-2">TUJUAN</div>
                    <div class="col-span-10">: {{ $pemindahan->tujuan_pemindahan }}</div>
                </div>
            </div>

            <!-- 50 Rows Tag Table (2 Columns x 25 Rows) -->
            <div class="grid grid-cols-2 gap-4 mb-3">
                <!-- Kolum 1: 1 - 25 -->
                <table class="w-full text-center text-[8.5pt] border-collapse">
                    <thead>
                        <tr class="font-bold uppercase bg-slate-50">
                            <th class="py-1 px-1 w-12">BIL</th>
                            <th class="py-1 px-3">NO TAG</th>
                        </tr>
                    </thead>
                    <tbody class="font-mono">
                        @for($i = 1; $i <= 25; $i++)
                            @php $t = $tagList->get($i - 1); @endphp
                            <tr class="h-5">
                                <td class="py-0.5 px-1 text-slate-500 font-sans">{{ $i }}</td>
                                <td class="py-0.5 px-3 font-bold">{{ $t['no_tag'] ?? '' }}</td>
                            </tr>
                        @endfor
                    </tbody>
                </table>

                <!-- Kolum 2: 26 - 50 -->
                <table class="w-full text-center text-[8.5pt] border-collapse">
                    <thead>
                        <tr class="font-bold uppercase bg-slate-50">
                            <th class="py-1 px-1 w-12">BIL</th>
                            <th class="py-1 px-3">NO TAG</th>
                        </tr>
                    </thead>
                    <tbody class="font-mono">
                        @for($i = 26; $i <= 50; $i++)
                            @php $t = $tagList->get($i - 1); @endphp
                            <tr class="h-5">
                                <td class="py-0.5 px-1 text-slate-500 font-sans">{{ $i }}</td>
                                <td class="py-0.5 px-3 font-bold">{{ $t['no_tag'] ?? '' }}</td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            <!-- Bottom Summary Info Block -->
            <div class="text-[9pt] font-bold uppercase space-y-1 mb-3 leading-tight">
                <div>
                    {{ $pemindahan->pemohon_nama }} 
                    @if($pemindahan->pemohon_id_premis)
                        <span class="font-mono">({{ $pemindahan->pemohon_id_premis }})</span>
                    @endif
                </div>
                <div class="font-mono">
                    P1 : {{ $fmdP1 }} &nbsp;&nbsp;P2 : {{ $fmdP2 }} &nbsp;&nbsp;Booster : {{ $fmdBooster ?: '-' }} &nbsp;&nbsp;&nbsp;&nbsp;LSD : {{ $lsd ?: '-' }}
                </div>
                <div>{{ $pemindahan->penerima_nama }}</div>
                <div>
                    {{ $pemindahan->penerima_alamat }}
                    @if($pemindahan->penerima_id_premis)
                        - <span class="font-mono">({{ $pemindahan->penerima_id_premis }})</span>
                    @endif
                </div>
                <div class="font-mono">{{ $pemindahan->no_kenderaan }}</div>
            </div>

            <!-- Pegawai Verification & Signature -->
            <div class="text-[9pt] space-y-1 pt-1">
                <div>Saya dengan ini mengesahkan maklumat di atas adalah benar</div>
                <div class="pt-5">
                    <div>.......................................................</div>
                    <div class="font-bold uppercase pt-0.5">{{ $pemindahan->pegawai_nama ?: 'PEGAWAI VETERINAR JAJAHAN' }}</div>
                    <div class="font-medium">Pegawai Perkhidmatan Veterinar Jajahan</div>
                    <div>Pejabat Perkhidmatan Veterinar Jajahan {{ $jajahan }}, Kelantan</div>
                    <div>Tarikh : <span>{{ $tarikhMohonStr }}</span></div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
