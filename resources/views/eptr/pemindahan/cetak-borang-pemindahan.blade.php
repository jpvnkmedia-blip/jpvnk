<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>BORANG PERMOHONAN PEMINDAHAN TERNAKAN - {{ $pemindahan->no_rujukan }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 15mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #f1f5f9;
        }
        .form-page {
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.3;
            font-size: 10.5pt;
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
                padding: 6mm 10mm !important;
                page-break-inside: avoid !important;
            }
        }
    </style>
</head>
<body class="p-4 sm:p-6 flex flex-col items-center">

    <!-- Screen-Only Actions -->
    <div class="no-print mb-6 flex flex-wrap gap-3">
        <button onclick="window.print()" class="px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-sm rounded-xl shadow-lg transition flex items-center gap-2">
            <i class="fa-solid fa-print"></i>
            <span>Cetak Borang Permohonan Pemindahan</span>
        </button>
        <a href="{{ route('eptr.pemindahan.show', $pemindahan->id) }}" class="px-4 py-2.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-sm rounded-xl transition">
            Kembali ke Butiran Permohonan
        </a>
    </div>

    @php
        $pejabat = $pemindahan->pejabat_info;
        $jajahan = $pejabat['jajahan'];
        $jajahanUpper = strtoupper($jajahan);
        $alamat1 = $pejabat['alamat_baris1'];
        $alamat2 = $pejabat['alamat_baris2'];
        $tarikhMohonStr = $pemindahan->tarikh_permohonan ? $pemindahan->tarikh_permohonan->format('d.m.Y') : date('d.m.Y');
        $tarikhPindahStr = $pemindahan->tarikh_jangka_pindah ? $pemindahan->tarikh_jangka_pindah->format('d.m.Y') : date('d.m.Y', strtotime('+7 days'));
        $j = (int) $pemindahan->bilangan_jantan;
        $b = (int) $pemindahan->bilangan_betina;
        $kuantitiText = '';
        if ($j > 0 && $b > 0) {
            $kuantitiText = "{$j} JANTAN dan {$b} BETINA";
        } elseif ($j > 0) {
            $kuantitiText = "{$j} JANTAN";
        } elseif ($b > 0) {
            $kuantitiText = "dan {$b} BETINA";
        }
    @endphp

    <!-- Form Container -->
    <div class="max-w-[210mm] w-full">
        <div class="form-page p-8 sm:p-12 shadow-2xl bg-white border border-slate-200">

            <!-- Rujukan & Tarikh (Aligned Right) -->
            <div class="text-right text-[10pt] mb-4 space-y-0.5">
                <div class="grid grid-cols-12">
                    <div class="col-span-6"></div>
                    <div class="col-span-6 text-left pl-6">
                        <div><span class="inline-block w-28">No Rujukan Permit</span> : <span class="font-mono font-bold">{{ $pemindahan->no_rujukan }}</span></div>
                        <div><span class="inline-block w-28">Tarikh</span> : <span>{{ $tarikhMohonStr }}</span></div>
                    </div>
                </div>
            </div>

            <!-- Kepada Pegawai -->
            <div class="text-[10pt] space-y-0.5 mb-4">
                <div>Kepada :</div>
                <div class="pt-1">YBrs Pegawai Perkhidmatan Veterinar</div>
                <div>Jajahan {{ $jajahanUpper }} ,</div>
                <div>{{ $alamat1 }},</div>
                <div class="font-bold">{{ $alamat2 }} .</div>
            </div>

            <div class="text-[10pt] mb-2 font-bold">
                Tuan/Puan,
            </div>

            <!-- Tajuk -->
            <div class="text-[11pt] font-bold uppercase tracking-wide mb-3 underline">
                PERMOHONAN PEMINDAHAN TERNAKAN / PRODUK
            </div>

            <!-- Paragraf Pembuka -->
            <div class="text-[10pt] text-justify leading-relaxed mb-3">
                Dengan segala hormatnya, saya ingin memohon kebenaran untuk memindah ternakan / produk bagi tujuan <strong class="uppercase">{{ $pemindahan->tujuan_pemindahan }}</strong> dan dijangka bertolak pada <strong class="underline">{{ $tarikhPindahStr }}</strong> ( Tarikh permohonan pemindahan mestilah sekurang-kurangnya tujuh ( 7 ) hari sebelum tarikh jangkaan pemindahan )
            </div>

            <div class="text-[10pt] mb-2">
                Maklumat adalah seperti berikut:-
            </div>

            <!-- 2-Column Table: MAKLUMAT PEMOHON vs MAKLUMAT PENERIMA -->
            <table class="w-full text-left text-[9.5pt] mb-6 border-collapse">
                <thead>
                    <tr class="font-bold uppercase text-center bg-slate-50">
                        <th class="py-1.5 px-3 w-1/2">MAKLUMAT PEMOHON /PENGHANTAR</th>
                        <th class="py-1.5 px-3 w-1/2">MAKLUMAT PENERIMA</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black align-top">
                    <!-- Row 1: Nama -->
                    <tr>
                        <td class="p-2">
                            <div class="text-[8.5pt] text-slate-600">Nama Penuh @syarikat</div>
                            <div class="font-bold uppercase pt-0.5">
                                {{ $pemindahan->pemohon_nama }}
                                @if($pemindahan->pemohon_id_premis)
                                    <span class="font-mono ml-1">{{ $pemindahan->pemohon_id_premis }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="p-2">
                            <div class="text-[8.5pt] text-slate-600">Nama Penuh</div>
                            <div class="font-bold uppercase pt-0.5">{{ $pemindahan->penerima_nama }}</div>
                        </td>
                    </tr>

                    <!-- Row 2: No IC -->
                    <tr>
                        <td class="p-2">
                            <div class="text-[8.5pt] text-slate-600">No.Ic</div>
                            <div class="font-mono pt-0.5">{{ $pemindahan->pemohon_ic ?: '-' }}</div>
                        </td>
                        <td class="p-2">
                            <div class="text-[8.5pt] text-slate-600">No IC</div>
                            <div class="font-mono pt-0.5">{{ $pemindahan->penerima_ic ?: '-' }}</div>
                        </td>
                    </tr>

                    <!-- Row 3: No Tel -->
                    <tr>
                        <td class="p-2">
                            <div class="text-[8.5pt] text-slate-600">No.Tel</div>
                            <div class="font-mono pt-0.5">{{ $pemindahan->pemohon_tel ?: '-' }}</div>
                        </td>
                        <td class="p-2">
                            <div class="text-[8.5pt] text-slate-600">No Tel</div>
                            <div class="font-mono pt-0.5">{{ $pemindahan->penerima_tel ?: '-' }}</div>
                        </td>
                    </tr>

                    <!-- Row 4: Jenis Ternakan & ID Premis -->
                    <tr>
                        <td class="p-2">
                            <div class="text-[8.5pt] text-slate-600">Jenis Ternakan/ Produk</div>
                            <div class="font-bold uppercase pt-0.5">{{ $pemindahan->jenis_ternakan ?? 'LEMBU' }}</div>
                        </td>
                        <td class="p-2">
                            <div class="text-[8.5pt] text-slate-600">ID Premis</div>
                            <div class="font-mono font-bold uppercase pt-0.5">{{ $pemindahan->penerima_id_premis ?: '-' }}</div>
                        </td>
                    </tr>

                    <!-- Row 5: Bilangan Ternakan & Alamat Penerima -->
                    <tr>
                        <td class="p-2">
                            <div class="text-[8.5pt] text-slate-600">Bilangan Ternakan/ Kuantiti Produk</div>
                            <div class="font-medium pt-0.5">
                                {{ $kuantitiText }}
                                <div class="text-[8pt] text-slate-700 italic pt-0.5">( Jika jantina berlainan sila Nyatakan bilangan utk J/B )</div>
                            </div>
                        </td>
                        <td class="p-2" rowspan="3">
                            <div class="text-[8.5pt] text-slate-600">Alamat Penuh Lokasi Destinasi</div>
                            <div class="uppercase pt-0.5 leading-tight">
                                {{ $pemindahan->penerima_alamat ?: '-' }}
                            </div>
                        </td>
                    </tr>

                    <!-- Row 6: Alamat Pemohon -->
                    <tr>
                        <td class="p-2">
                            <div class="text-[8.5pt] text-slate-600">Alamat Penuh Lokasi Ternakan/ produk</div>
                            <div class="uppercase pt-0.5 leading-tight">
                                {{ $pemindahan->pemohon_alamat ?: '-' }}
                            </div>
                        </td>
                    </tr>

                    <!-- Row 7: No Plat Kenderaan -->
                    <tr>
                        <td class="p-2">
                            <div class="text-[8.5pt] text-slate-600">No plat Kenderaan</div>
                            <div class="font-mono font-bold uppercase pt-0.5">{{ $pemindahan->no_kenderaan ?: '-' }}</div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Tandatangan Pemohon -->
            <div class="text-[10pt] pt-4 space-y-1">
                <div>Saya Yang Benar,</div>
                <div class="pt-8">
                    <div>.......................................................</div>
                    <div class="font-bold pt-1 uppercase">PEMOHON / PEMUNYA LADANG TERNAKAN</div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
