<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>SET LENGKAP BORANG D & SKV SEMBELIH ({{ $permit->no_permit }})</title>
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
            line-height: 1.25;
            font-size: 10.5pt;
            background-color: #ffffff;
            color: #000;
        }
        table, th, td {
            border: 1px solid #000;
        }
        .page-break {
            page-break-before: always;
            break-before: page;
        }
        @media print {
            .no-print { display: none !important; }
            html, body { width: 210mm !important; background: white !important; margin: 0 auto !important; padding: 0 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .form-page { width: 100% !important; max-width: 210mm !important; box-shadow: none !important; border: none !important; margin: 0 auto !important; padding: 5mm 10mm !important; }
            .page-break { page-break-before: always !important; break-before: page !important; }
        }
    </style>
</head>
<body class="p-4 sm:p-6 flex flex-col items-center">

    <!-- Screen-Only Actions -->
    <div class="no-print mb-6 flex flex-wrap gap-3">

    @php
        $pemunya = $permit->pemunya;
        $jajahan = $pemunya ? $pemunya->jajahan : 'Kota Bharu';
        $jajahanUpper = strtoupper($jajahan);
        $jajahanJawiMap = [
            'Kota Bharu' => 'كوتا بهارو',
            'Pasir Mas' => 'ڤاسير مس',
            'Tumpat' => 'تومڤت',
            'Bachok' => 'باچوق',
            'Pasir Puteh' => 'ڤاسير ڤوتيه',
            'Machang' => 'ماچڠ',
            'Tanah Merah' => 'تانه ميره',
            'Jeli' => 'جيلي',
            'Kuala Krai' => 'كوالا كراي',
            'Gua Musang' => 'ڬوا موسڠ',
        ];
        $jajahanJawi = $jajahanJawiMap[$jajahan] ?? 'كوتا بهارو';
        $totalRows = $permit->is_musim_korban ? 10 : 7;
        $items = $permit->senarai_ternakan_list;
        $tarikhSembelihStr = $permit->tarikh_sembelih ? $permit->tarikh_sembelih->format('d/m/Y') : date('d/m/Y');
        $tarikhTamatStr = $permit->tarikh_tamat ? $permit->tarikh_tamat->format('d/m/Y') : \Carbon\Carbon::parse($permit->tarikh_sembelih)->addDays(6)->format('d/m/Y');
        $firstItem = $items->first();
        $ternakanObj = $permit->ternakan;
        
        $spesies = strtoupper($permit->jenis_ternakan ?? ($ternakanObj ? $ternakanObj->jenis_ternakan : 'LEMBU'));
        $baka = strtoupper($ternakanObj ? ($ternakanObj->baka ?? 'KACUKAN') : ($firstItem['baka'] ?? 'KACUKAN'));
        $jantinaRaw = $ternakanObj ? $ternakanObj->jantina : ($firstItem['jantina'] ?? 'Jantan');
        $jantina = in_array(strtoupper($jantinaRaw), ['J', 'JANTAN']) ? 'JANTAN' : 'BETINA';
        $noPerakuan = $ternakanObj ? ($ternakanObj->no_siri_kad_kuning ?? 'DB' . date('mY') . rand(10, 99)) : ($firstItem['no_siri_kad_pendaftaran'] ?? ('DB' . date('mY') . rand(10, 99)));
        $noTag = $ternakanObj ? ($ternakanObj->no_tag ?? '-') : ($firstItem['no_id_ternakan'] ?? '-');
    @endphp

    <div class="max-w-[210mm] w-full space-y-6">

        <!-- ========================================== -->
        <!-- BAHAGIAN 1: BORANG D (JADUAL KEEMPAT - FORMAT WARTA) -->
        <!-- ========================================== -->
        <div class="form-page p-8 sm:p-14 shadow-2xl bg-white border border-slate-200">
            <!-- Top Reference (Aligned Right) -->
            <div class="text-right mb-6 text-[11pt]">
                <span>NO. RUJUKAN: </span>
                <span class="font-mono font-bold">{{ $permit->no_permit }}</span>
            </div>

            <!-- Header -->
            <div class="text-center mb-6 space-y-1">
                <div class="font-bold text-[12pt] tracking-wide uppercase">ENAKMEN PENDAFTARAN TERNAKAN RUMINAN 2024</div>
                <div class="font-bold text-[11pt] uppercase">JADUAL KEEMPAT</div>
                <div class="font-bold text-[11pt] uppercase">BORANG D</div>
                <div class="text-[10.5pt]">[subseksyen 11(1) (b)]</div>
                <div class="font-bold text-[11.5pt] uppercase tracking-wide pt-3">PEMBATALAN PENDAFTARAN TERNAKAN RUMINAN AKIBAT SEMBELIHAN</div>
                <div class="text-[10.5pt]">(Penamatan Daftar Ternakan Ruminan)</div>
            </div>

            <div class="mb-5 text-justify leading-relaxed">
                <span>Adalah dengan ini dibenarkan menyembelih ternakan ruminan yang butirannya sebagaimana berikut:</span>
            </div>

            <!-- Section 1: Maklumat Pemunya -->
            <div class="mb-5">
                <div class="font-bold uppercase tracking-wide mb-1.5">MAKLUMAT PEMUNYA</div>
                <div class="space-y-1">
                    <div class="grid grid-cols-12">
                        <div class="col-span-4">Nama</div>
                        <div class="col-span-8">: <span class="uppercase font-medium">{{ $pemunya->nama ?? '-' }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-4">No. Kad Pengenalan</div>
                        <div class="col-span-8">: <span class="font-mono">{{ $pemunya->no_kp ?? '-' }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-4">Alamat</div>
                        <div class="col-span-8">: <span class="uppercase">{{ $pemunya->alamat ?? '-' }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-4">No Telefon</div>
                        <div class="col-span-8">: <span>{{ $pemunya->no_telefon ?? '-' }}</span></div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Maklumat Ternakan -->
            <div class="mb-5">
                <div class="font-bold uppercase tracking-wide mb-1.5">MAKLUMAT TERNAKAN</div>
                <div class="space-y-1">
                    <div class="grid grid-cols-12">
                        <div class="col-span-4">Spesies</div>
                        <div class="col-span-8">: <span class="uppercase font-medium">{{ $spesies }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-4">Baka</div>
                        <div class="col-span-8">: <span class="uppercase font-medium">{{ $baka }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-4">Jantina</div>
                        <div class="col-span-8">: <span class="uppercase font-medium">{{ $jantina }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-4">No. Perakuan Pendaftaran</div>
                        <div class="col-span-8">: <span class="font-mono">{{ $noPerakuan }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-4">No. Tanda Pengenalan</div>
                        <div class="col-span-8">: <span class="font-mono font-bold">{{ $noTag }}</span></div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Maklumat Penyembelihan -->
            <div class="mb-5">
                <div class="font-bold uppercase tracking-wide mb-1.5">MAKLUMAT PENYEMBELIHAN</div>
                <div class="space-y-1">
                    <div class="grid grid-cols-12">
                        <div class="col-span-4">Nama Premis Sembelihan</div>
                        <div class="col-span-8">: <span class="uppercase">{{ $permit->nama_premis_sembelih ?? ($permit->lokasi_sembelih ?? '') }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-4">Alamat Premis Sembelihan</div>
                        <div class="col-span-8">: <span class="uppercase">{{ $permit->alamat_premis_sembelih ?? ($permit->lokasi_sembelih ?? '') }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-4">Tarikh Sembelihan</div>
                        <div class="col-span-8">: <span>{{ $tarikhSembelihStr }}</span></div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Penamatan & Signatures -->
            <div class="mb-6">
                <div class="font-bold uppercase tracking-wide mb-1.5">PENDAFTARAN TELAH DITAMATKAN</div>
                <div class="mb-8">
                    <div>Tarikh : <span>{{ $tarikhSembelihStr }}</span></div>
                </div>

                <div class="grid grid-cols-2 gap-8 pt-4">
                    <div class="text-left">
                        <div>......................………………………….</div>
                        <div class="mt-1 text-[10.5pt]">Tandatangan</div>
                        <div class="text-[10.5pt]">Pemunya Ternakan Ruminan</div>
                    </div>
                    <div class="text-left">
                        <div>.......................................................</div>
                        <div class="mt-1 text-[10.5pt]">Tandatangan dan</div>
                        <div class="text-[10.5pt]">cop Penolong Pendaftar</div>
                    </div>
                </div>
            </div>

            <!-- Statutory Notice (Exact Gazette Wording & Numbering) -->
            <div class="text-[9.5pt] text-justify space-y-2 pt-4 border-t border-black/30 leading-relaxed">
                <div class="font-bold">Perhatian:</div>
                
                <div class="flex gap-2.5 items-start">
                    <span class="shrink-0 font-medium">1.</span>
                    <div>Penyembelihan ternakan ruminan hendaklah dilakukan di rumah penyembelihan yang diluluskan atau rumah penyembelihan yang dilesenkan dan hendaklah mematuhi segala syarat-syarat yang ditetapkan di bawah Akta Binatang 1953 atau mana-mana perundangan subsidiarinya.</div>
                </div>
                
                <div class="flex gap-2.5 items-start">
                    <span class="shrink-0 font-medium">2.</span>
                    <div>
                        Adalah menjadi kesalahan jika penyembelihan tidak mengikut Akta Binatang 1953 atau perundangan subsidiarinya , jika disabitkan boleh didenda sebanyak RM10,000.<br>
                        <span>[Kaedah 17 Kaedah-Kaedah Binatang (Kawalan Penyembelihan) (Pindaan) 2018].</span>
                    </div>
                </div>
                
                <div class="flex gap-2.5 items-start">
                    <span class="shrink-0 font-medium">3.</span>
                    <div>Sila bawa kad Perakuan Pendaftaran Ternakan dan Kad Pengenalan Diri apabila ingin mendapatkan kebenaran menyembelih.</div>
                </div>
            </div>
        </div>


        <!-- ========================================== -->
        <!-- BAHAGIAN 2: SKV SEMBELIH - HALAMAN 1 (FORMAT WARTA) -->
        <!-- ========================================== -->
        <div class="form-page page-break p-8 sm:p-12 shadow-2xl bg-white border border-slate-200">
            
            <!-- Official Letterhead -->
            <div class="border-b-2 border-blue-900 pb-2 mb-4">
                <div class="grid grid-cols-12 items-center">
                    <div class="col-span-2 flex justify-start">
                        <img src="{{ asset('images/jata-kelantan.png') }}" alt="Jata Kelantan" class="h-22 w-auto object-contain" onerror="this.src='{{ asset('images/logo.png') }}'">
                    </div>
                    <div class="col-span-8 text-center space-y-0.5">
                        <div class="text-[15pt] font-serif font-bold text-center leading-tight" style="direction: rtl;">
                            ڤجابت ڤرخدمتن ۏيترينر ججاهن {{ $jajahanJawi }}
                        </div>
                        <div class="text-[12pt] font-bold uppercase tracking-tight">
                            PEJABAT PERKHIDMATAN VETERINAR
                        </div>
                        <div class="text-[11.5pt] font-bold uppercase tracking-tight">
                            JAJAHAN {{ $jajahanUpper }},
                        </div>
                        <div class="text-[10pt] uppercase">
                            JALAN TELIPOT, 15150 KOTA BHARU
                        </div>
                        <div class="text-[10pt] font-bold uppercase">
                            KELANTAN DARUL NAIM.
                        </div>
                    </div>
                    <div class="col-span-2 flex flex-col items-end justify-center">
                        <img src="{{ asset('images/dvs-logo.png') }}" alt="DVS Logo" class="h-16 w-auto object-contain mb-1" onerror="this.style.display='none'">
                        <div class="text-right text-[8.5pt] leading-tight text-slate-800">
                            <div>Tel : 097440341</div>
                            <div>Faks : 097440341</div>
                            <div class="text-[7.5pt]">Email : ppvjkb@yahoo.com</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rujukan & Tarikh (Aligned Right) -->
            <div class="text-right text-[10.5pt] mb-3 space-y-0.5">
                <div>
                    <span>Ruj. Kami : </span>
                    <span class="font-mono font-bold">{{ $permit->no_rujukan_skv ?: 'PPVJ' . strtoupper(substr($jajahan,0,2)) . '.600-3(' . rand(10,99) . ')' }}</span>
                </div>
                <div>
                    <span>Tarikh : </span>
                    <span class="font-medium">{{ $tarikhSembelihStr }}</span>
                </div>
            </div>

            <!-- Title & Statutory Clause -->
            <div class="mb-3 space-y-1.5">
                <div class="font-bold text-[12pt] uppercase tracking-wide">KEPADA SESIAPA YANG BERKENAAN</div>
                <p class="text-justify text-[10.5pt] leading-relaxed">
                    <strong>Kebenaran bertulis penyembelihan binatang dan Sijil Kesihatan Veterinar (SKV) penyembelihan binatang</strong> ini diberikan mengikut Perintah Menteri Besar Bagi Kawalan Penyakit Kuku dan Mulut Negeri Kelantan ( Kn. P.U. 16 ) bertarikh 29hb September 2005 di bawah subseksyen 36 (1) Akta Binatang 1953.
                </p>
                <div class="text-[10.5pt] pt-1">Kepada penama di bawah bagi tujuan menyembelih binatang / ternakan seperti berikut :</div>
            </div>

            <!-- Maklumat Pemunya -->
            <div class="text-[10.5pt] space-y-1 mb-3">
                <div class="grid grid-cols-12">
                    <div class="col-span-3">Pemunya</div>
                    <div class="col-span-9">: <span class="uppercase font-medium">{{ $pemunya->nama ?? '-' }}</span></div>
                </div>
                <div class="grid grid-cols-12">
                    <div class="col-span-3">No. Kad pengenalan</div>
                    <div class="col-span-9">: <span class="font-mono">{{ $pemunya->no_kp ?? '-' }}</span></div>
                </div>
                <div class="grid grid-cols-12">
                    <div class="col-span-3">Alamat</div>
                    <div class="col-span-9">: <span class="uppercase">{{ $pemunya->alamat ?? '-' }}</span></div>
                </div>
                <div class="grid grid-cols-12">
                    <div class="col-span-3">No Telefon</div>
                    <div class="col-span-9">: <span>{{ $pemunya->no_telefon ?? '-' }}</span></div>
                </div>
            </div>

            <!-- Ternakan Details Header -->
            <div class="text-[10.5pt] mb-2 space-y-0.5">
                <div>Bagi menyembelih ternakan :-</div>
                <div>
                    <span>Jenis ternakan : </span>
                    <span class="uppercase font-bold">{{ $permit->jenis_ternakan ?? 'LEMBU' }}</span>
                </div>
            </div>

            <!-- Table 1: 7 Rows (or 10 if Musim Korban) -->
            <table class="w-full text-center text-[9.5pt] mb-3 border-collapse">
                <thead>
                    <tr class="font-bold text-[9.5pt]">
                        <th class="py-1 px-1 w-8">BIL</th>
                        <th class="py-1 px-1 w-16">JANTINA<br>( J / B )</th>
                        <th class="py-1 px-2 w-28">NO. ID<br>TERNAKAN</th>
                        <th class="py-1 px-2 w-32">NO. SIRI KAD<br>PENDAFTARAN</th>
                        <th class="py-1 px-2 w-24">TARIKH<br>SEMBELIHAN</th>
                        <th class="py-1 px-2">TEMPAT<br>SEMBELIHAN</th>
                        <th class="py-1 px-2 w-20">No Kn.<br>Haiwan<br>16</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 1; $i <= $totalRows; $i++)
                        @php $row = $items->get($i - 1); @endphp
                        <tr class="h-7">
                            <td class="py-1 px-1">{{ $i }}.</td>
                            <td class="py-1 px-1 font-bold">{{ $row['jantina'] ?? '' }}</td>
                            <td class="py-1 px-1 font-mono font-bold">{{ $row['no_id_ternakan'] ?? '' }}</td>
                            <td class="py-1 px-1 font-mono">{{ $row['no_siri_kad_pendaftaran'] ?? '' }}</td>
                            <td class="py-1 px-1">{{ !empty($row['tarikh_sembelihan']) ? \Carbon\Carbon::parse($row['tarikh_sembelihan'])->format('d/m/Y') : '' }}</td>
                            <td class="py-1 px-1 text-left uppercase text-[9pt]">{{ $row['tempat_sembelihan'] ?? '' }}</td>
                            <td class="py-1 px-1 font-mono">{{ $row['no_kn_haiwan_16'] ?? '' }}</td>
                        </tr>
                    @endfor
                </tbody>
            </table>

            <!-- Tempoh Sah Laku 7 Hari & No Kenderaan -->
            <div class="text-[10pt] mb-3 leading-relaxed">
                <div>
                    *Tarikh kebenaran penyembelihan dan SKV penyembelihan binatang ini adalah bermula pada tarikh sembelihan dinyatakan dan berakhir pada tarikh <strong class="underline">{{ $tarikhTamatStr }}</strong> . No. Kenderaan : <strong class="font-mono uppercase">{{ $permit->no_kenderaan ?: '-' }}</strong>
                </div>
                <div class="mt-1.5">
                    <span>Tujuan sembelih</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <strong class="uppercase">{{ $permit->tujuan_sembelih ?? 'Jualan' }}</strong>
                </div>
            </div>

            <!-- Alamat Agihan Karkas 1, 2, 3 -->
            <div class="text-[10pt] space-y-1 mb-3">
                <div>
                    <div>A. Alamat 1 : <span>{{ $permit->alamat_1 ?: '' }}</span></div>
                    <div class="pl-4 text-[9.5pt]">Bahagian / kuantiti karkas : <span>{{ $permit->kuantiti_karkas_1 ?: '' }}</span></div>
                </div>
                <div>
                    <div>B. Alamat 2 : <span>{{ $permit->alamat_2 ?: '' }}</span></div>
                    <div class="pl-4 text-[9.5pt]">Bahagian / kuantiti karkas : <span>{{ $permit->kuantiti_karkas_2 ?: '' }}</span></div>
                </div>
                <div>
                    <div>C. Alamat 3 : <span>{{ $permit->alamat_3 ?: '' }}</span></div>
                    <div class="pl-4 text-[9.5pt]">Bahagian / kuantiti karkas : <span>{{ $permit->kuantiti_karkas_3 ?: '' }}</span></div>
                </div>
            </div>

            <!-- Legal Warning Notice -->
            <div class="text-[9pt] text-justify leading-relaxed mb-2">
                <strong>Kebenaran menyembelih dan SKV ini hendaklah dipamerkan di tempat atau di premis di mana karkas tersebut dipamer untuk jualan atau dijual</strong> mengikut Perintah Pengarah Jabatan Perkhidmatan Veterinar Bagi Kawalan dan Pembasmian Penyakit Kuku dan Mulut bagi Negeri Kelantan (Kn. P.U. 10 ) bertarikh 23hb April 2009 di bawah subseksyen 36 (2) Akta Binatang 1953.
            </div>

            <!-- Signature & Cop Jabatan -->
            <div class="text-[10.5pt] space-y-1 pt-0">
                <div>Saya yang menjalankan amanah</div>
                <div class="grid grid-cols-2 gap-8 items-end pt-3">
                    <div>
                        <div>.......................................................</div>
                        <div class="font-bold pt-1">Pegawai Perkhidmatan Veterinar</div>
                        <div>Jajahan {{ $jajahan }}.</div>
                    </div>
                    <div class="text-center font-bold text-black tracking-wider">
                        COP JABATAN
                    </div>
                </div>
            </div>

        </div>


        <!-- ========================================== -->
        <!-- BAHAGIAN 3: SKV SEMBELIH - HALAMAN 2 (FORMAT WARTA) -->
        <!-- ========================================== -->
        <div class="form-page page-break p-8 sm:p-12 shadow-2xl bg-white border border-slate-200">
            
            <!-- Official Letterhead -->
            <div class="border-b-2 border-blue-900 pb-2 mb-4">
                <div class="grid grid-cols-12 items-center">
                    <div class="col-span-2 flex justify-start">
                        <img src="{{ asset('images/jata-kelantan.png') }}" alt="Jata Kelantan" class="h-22 w-auto object-contain" onerror="this.src='{{ asset('images/logo.png') }}'">
                    </div>
                    <div class="col-span-8 text-center space-y-0.5">
                        <div class="text-[15pt] font-serif font-bold text-center leading-tight" style="direction: rtl;">
                            ڤجابت ڤرخدمتن ۏيترينر ججاهن {{ $jajahanJawi }}
                        </div>
                        <div class="text-[12pt] font-bold uppercase tracking-tight">
                            PEJABAT PERKHIDMATAN VETERINAR
                        </div>
                        <div class="text-[11.5pt] font-bold uppercase tracking-tight">
                            JAJAHAN {{ $jajahanUpper }},
                        </div>
                        <div class="text-[10pt] uppercase">
                            JALAN TELIPOT, 15150 KOTA BHARU
                        </div>
                        <div class="text-[10pt] font-bold uppercase">
                            KELANTAN DARUL NAIM.
                        </div>
                    </div>
                    <div class="col-span-2 flex flex-col items-end justify-center">
                        <img src="{{ asset('images/dvs-logo.png') }}" alt="DVS Logo" class="h-16 w-auto object-contain mb-1" onerror="this.style.display='none'">
                        <div class="text-right text-[8.5pt] leading-tight text-slate-800">
                            <div>Tel : 097440341</div>
                            <div>Faks : 097440341</div>
                            <div class="text-[7.5pt]">Email : ppvjkb@yahoo.com</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rujukan & Tarikh (Aligned Right) -->
            <div class="text-right text-[10.5pt] mb-3 space-y-0.5">
                <div>
                    <span>Ruj. Kami : </span>
                    <span class="font-mono font-bold">{{ $permit->no_rujukan_karkas ?: 'PPVJ' . strtoupper(substr($jajahan,0,2)) . '.600-3/1/1/H(' . rand(10,99) . ')' }}</span>
                </div>
                <div>
                    <span>Tarikh : </span>
                    <span class="font-medium">{{ $tarikhSembelihStr }}</span>
                </div>
            </div>

            <!-- Title & Statutory Clause -->
            <div class="mb-3 space-y-1.5">
                <div class="font-bold text-[12pt] uppercase tracking-wide">KEPADA SESIAPA YANG BERKENAAN</div>
                <p class="text-justify text-[10.5pt] leading-relaxed">
                    <strong>Kebenaran bertulis pemindahan karkas dan Sijil Kesihatan Veterinar (SKV) karkas setelah sembelihan</strong> diberikan mengikut Perintah Menteri Besar Bagi Kawalan Penyakit Kuku dan Mulut Negeri Kelantan ( Kn. P.U. 16 ) bertarikh 29hb September 2005 di bawah subseksyen 36 (1) Akta Binatang 1953.
                </p>
                <div class="text-[10.5pt] pt-1">Kepada penama di bawah bagi tujuan pemindahan karkas dan Sijil Kesihatan Veterinar Karkas seperti berikut :</div>
            </div>

            <!-- Maklumat Pemunya -->
            <div class="text-[10.5pt] space-y-1 mb-3">
                <div class="grid grid-cols-12">
                    <div class="col-span-3">Pemunya</div>
                    <div class="col-span-9">: <span class="uppercase font-medium">{{ $pemunya->nama ?? '-' }}</span></div>
                </div>
                <div class="grid grid-cols-12">
                    <div class="col-span-3">No. Kad pengenalan</div>
                    <div class="col-span-9">: <span class="font-mono">{{ $pemunya->no_kp ?? '-' }}</span></div>
                </div>
                <div class="grid grid-cols-12">
                    <div class="col-span-3">Alamat</div>
                    <div class="col-span-9">: <span class="uppercase">{{ $pemunya->alamat ?? '-' }}</span></div>
                </div>
                <div class="grid grid-cols-12">
                    <div class="col-span-3">No Telefon</div>
                    <div class="col-span-9">: <span>{{ $pemunya->no_telefon ?? '-' }}</span></div>
                </div>
            </div>

            <!-- Ternakan Details Header -->
            <div class="text-[10.5pt] mb-2 space-y-0.5">
                <div>Bagi menyembelih ternakan :-</div>
                <div>
                    <span>Jenis ternakan : </span>
                    <span class="uppercase font-bold">{{ $permit->jenis_ternakan ?? 'LEMBU' }}</span>
                </div>
            </div>

            <!-- Table 2: 4 Columns (JUMLAH BAHAGIAN / KUANTITI KARKAS) -->
            <table class="w-full text-center text-[10pt] mb-3 border-collapse">
                <thead>
                    <tr class="font-bold text-[10pt]">
                        <th class="py-1.5 px-1 w-12">BIL</th>
                        <th class="py-1.5 px-2 w-56">JUMLAH BAHAGIAN /<br>KUANTITI KARKAS</th>
                        <th class="py-1.5 px-2 w-40">TARIKH<br>SEMBELIHAN<br>TERNAKAN</th>
                        <th class="py-1.5 px-2">TEMPAT<br>SEMBELIHAN</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 1; $i <= $totalRows; $i++)
                        @php $row = $items->get($i - 1); @endphp
                        <tr class="h-8">
                            <td class="py-1 px-1">{{ $i }}.</td>
                            <td class="py-1 px-2 font-medium">{{ $row['kuantiti_karkas'] ?? '' }}</td>
                            <td class="py-1 px-2">{{ !empty($row['tarikh_sembelihan']) ? \Carbon\Carbon::parse($row['tarikh_sembelihan'])->format('d/m/Y') : '' }}</td>
                            <td class="py-1 px-2 text-left uppercase text-[9pt]">{{ $row['tempat_sembelihan'] ?? '' }}</td>
                        </tr>
                    @endfor
                </tbody>
            </table>

            <!-- Tempoh Sah Laku 7 Hari & No Kenderaan -->
            <div class="text-[10pt] mb-3 leading-relaxed">
                <div>
                    *Tarikh kebenaran pemindahan karkas dan SKV karkas ini adalah bermula pada tarikh sembelihan ternakan dinyatakan dan berakhir pada tarikh <strong class="underline">{{ $tarikhTamatStr }}</strong> . No. Kenderaan : <strong class="font-mono uppercase">{{ $permit->no_kenderaan ?: '-' }}</strong>
                </div>
                <div class="mt-1.5">
                    <span>Tujuan sembelih</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <strong class="uppercase">{{ $permit->tujuan_sembelih ?? 'Jualan' }}</strong>
                </div>
            </div>

            <!-- Alamat Agihan Karkas 1, 2, 3 -->
            <div class="text-[10pt] space-y-1 mb-3">
                <div>
                    <div>A. Alamat 1 : <span>{{ $permit->alamat_1 ?: '' }}</span></div>
                    <div class="pl-4 text-[9.5pt]">Bahagian / kuantiti karkas : <span>{{ $permit->kuantiti_karkas_1 ?: '' }}</span></div>
                </div>
                <div>
                    <div>B. Alamat 2 : <span>{{ $permit->alamat_2 ?: '' }}</span></div>
                    <div class="pl-4 text-[9.5pt]">Bahagian / kuantiti karkas : <span>{{ $permit->kuantiti_karkas_2 ?: '' }}</span></div>
                </div>
                <div>
                    <div>C. Alamat 3 : <span>{{ $permit->alamat_3 ?: '' }}</span></div>
                    <div class="pl-4 text-[9.5pt]">Bahagian / kuantiti karkas : <span>{{ $permit->kuantiti_karkas_3 ?: '' }}</span></div>
                </div>
            </div>

            <!-- Legal Warning Notice -->
            <div class="text-[9pt] text-justify leading-relaxed mb-2">
                <strong>Kebenaran bertulis pemindahan karkas dan Sijil Kesihatan Veterinar karkas ini hendaklah dipamerkan di tempat atau di premis di mana karkas tersebut dipamer untuk jualan atau dijual</strong> mengikut Perintah Pengarah Jabatan Perkhidmatan Veterinar Bagi Kawalan dan Pembasmian Penyakit Kuku dan Mulut bagi Negeri Kelantan (Kn. P.U. 10 ) bertarikh 23hb April 2009 di bawah subseksyen 36 (2) Akta Binatang 1953.
            </div>

            <!-- Signature & Cop Jabatan -->
            <div class="text-[10.5pt] space-y-1 pt-0">
                <div>Saya yang menjalankan amanah</div>
                <div class="grid grid-cols-2 gap-8 items-end pt-3">
                    <div>
                        <div>.......................................................</div>
                        <div class="font-bold pt-1">Pegawai Perkhidmatan Veterinar</div>
                        <div>Jajahan {{ $jajahan }}.</div>
                    </div>
                    <div class="text-center font-bold text-black tracking-wider">
                        COP JABATAN
                    </div>
                </div>
            </div>

        </div>

    </div>

</body>
</html>
