<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>PENGESAHAN TARIKH SUNTIKAN FMD - {{ $pemindahan->no_rujukan }}</title>
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
            line-height: 1.25;
            font-size: 10.5pt;
            background-color: #ffffff;
            color: #000;
            page-break-inside: avoid;
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
        <button onclick="window.print()" class="px-6 py-2.5 bg-indigo-700 hover:bg-indigo-800 text-white font-bold text-sm rounded-xl shadow-lg transition flex items-center gap-2">
            <i class="fa-solid fa-print"></i>
            <span>Cetak Surat Pengesahan FMD (Kn. 156)</span>
        </button>
        <a href="{{ route('eptr.pemindahan.show', $pemindahan->id) }}" class="px-4 py-2.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-sm rounded-xl transition">
            Kembali ke Butiran Permohonan
        </a>
    </div>

    @php
        $pejabat = $pemindahan->pejabat_info;
        $jajahan = $pejabat['jajahan'];
        $jajahanUpper = strtoupper($jajahan);
        $jajahanJawi = $pejabat['jawi'];
        $alamat1 = $pejabat['alamat_baris1'];
        $alamat2 = $pejabat['alamat_baris2'];
        $tel = $pejabat['tel'];
        $faks = $pejabat['faks'];
        $tarikhMohonStr = $pemindahan->tarikh_permohonan ? $pemindahan->tarikh_permohonan->format('d.m.Y') : date('d.m.Y');
        $fmdP1 = $pemindahan->tarikh_fmd_p1 ? $pemindahan->tarikh_fmd_p1->format('d.m.Y') : '18.5.2026';
        $fmdP2 = $pemindahan->tarikh_fmd_p2 ? $pemindahan->tarikh_fmd_p2->format('d.m.Y') : '16.6.2026';
        $fmdBooster = $pemindahan->tarikh_fmd_booster ? $pemindahan->tarikh_fmd_booster->format('d.m.Y') : '';
        $lsd = $pemindahan->tarikh_lsd ? $pemindahan->tarikh_lsd->format('d.m.Y') : '';
        $spesies = strtoupper($pemindahan->jenis_ternakan ?? 'LEMBU');
        $j = (int) $pemindahan->bilangan_jantan;
        $b = (int) $pemindahan->bilangan_betina;
    @endphp

    <!-- Form Container -->
    <div class="max-w-[210mm] w-full">
        <div class="form-page p-8 sm:p-12 shadow-2xl bg-white border border-slate-200">
            
            <!-- Top Kn. 156 Indicator -->
            <div class="text-[9pt] font-sans font-medium text-slate-700 mb-1">
                Kn. 156
            </div>

            <!-- Official Letterhead -->
            <div class="border-b-2 border-slate-800 pb-2 mb-4">
                <div class="grid grid-cols-12 items-center">
                    <div class="col-span-2 flex justify-start">
                        <img src="{{ asset('images/jata-kelantan.png') }}" alt="Jata Kelantan" class="h-20 w-auto object-contain" onerror="this.src='{{ asset('images/logo.png') }}'">
                    </div>
                    <div class="col-span-8 text-center space-y-0.5">
                        <div class="text-[14pt] font-serif font-bold text-center leading-tight" style="direction: rtl;">
                            {{ $jajahanJawi }}
                        </div>
                        <div class="text-[11.5pt] font-bold uppercase tracking-tight">
                            {{ $pejabat['nama'] }}
                        </div>
                        <div class="text-[11pt] font-bold uppercase tracking-tight">
                            {{ $pejabat['jajahan_title'] }}
                        </div>
                        <div class="text-[9pt] uppercase">
                            {{ $alamat1 }}
                        </div>
                        <div class="text-[9pt] font-bold uppercase">
                            {{ $alamat2 }}
                        </div>
                    </div>
                    <div class="col-span-2 flex flex-col items-end justify-center">
                        <img src="{{ asset('images/dvs-logo.png') }}" alt="DVS Logo" class="h-14 w-auto object-contain mb-1" onerror="this.style.display='none'">
                        <div class="text-right text-[7.5pt] leading-tight text-slate-800 font-sans">
                            <div>Tel : {{ $tel }}</div>
                            <div>Faks : {{ $faks }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rujukan & Tarikh (Aligned Right) -->
            <div class="text-right text-[10pt] mb-4 space-y-0.5">
                <div class="grid grid-cols-12">
                    <div class="col-span-7"></div>
                    <div class="col-span-5 text-left pl-4">
                        <div><span class="inline-block w-24">Rujukan Kami</span> : <span class="font-mono font-bold">{{ $pemindahan->no_rujukan }}</span></div>
                        <div><span class="inline-block w-24">Rujukan Tuan</span> : </div>
                        <div><span class="inline-block w-24">Tarikh</span> : <span>{{ $tarikhMohonStr }}</span></div>
                    </div>
                </div>
            </div>

            <!-- Penerima Surat -->
            <div class="text-[10pt] space-y-0.5 mb-4">
                <div>Ke Majlis ,</div>
                <div class="font-bold pt-1">YABhg Tuan Pengarah</div>
                <div>Jabatan Perkhidmatan Veterinar Negeri Kelantan</div>
                <div>Kubang Kerian</div>
                <div>16150 Kota Bharu</div>
                <div class="font-bold">Kelantan.</div>
            </div>

            <div class="text-[10pt] mb-2 font-bold">
                Tuan,
            </div>

            <!-- Tajuk Surat -->
            <div class="text-[11pt] font-bold uppercase tracking-wide mb-3 underline">
                PENGESAHAN TARIKH SUNTIKAN FMD BAGI PEMINDAHAN TERNAKAN
            </div>

            <!-- Perenggan 1 -->
            <div class="text-[10pt] text-justify leading-relaxed mb-3">
                Dengan segala hormatnya saya merujuk kepada perkara di atas .
            </div>

            <!-- Perenggan 2 -->
            <div class="text-[10pt] text-justify leading-relaxed mb-3">
                <p>
                    2.&nbsp;&nbsp;&nbsp;&nbsp;Adalah dimaklumkan bahawa ternakan milik <strong class="uppercase">{{ $pemindahan->pemohon_nama }}</strong> sebanyak <strong>{{ $j > 0 ? $j : '' }}</strong> ekor <strong>{{ $spesies }}</strong> Jantan dan <strong>{{ $b > 0 ? $b : '' }}</strong> ekor <strong>{{ $spesies }}</strong> betina telah disuntik vaksin FMD dan didapati ternakan tersebut tiada tanda – tanda penyakit klinikal FMD &amp; LSD /penyakit berjangkit / sesuai untuk kegunaan manusia / tidak dari ternakan berpenyakit dan maklumatnya adalah seperti berikut:
                </p>
            </div>

            <!-- Maklumat Suntikan -->
            <div class="text-[10pt] space-y-1.5 mb-3 pl-6">
                <div>
                    <strong>Suntikan Penyakit Kuku Dan Mulut ( P1 ) :</strong> <span class="font-mono font-bold">{{ $fmdP1 }}</span> &nbsp;&nbsp;&nbsp;&nbsp;<strong>( P2 ) :</strong><span class="font-mono font-bold">{{ $fmdP2 }}</span> &nbsp;&nbsp;&nbsp;&nbsp;<strong>Booster:</strong> <span class="font-mono">{{ $fmdBooster ?: '-' }}</span>
                </div>
                <div>
                    <strong>Suntikan Penyakit LSD :</strong> <span class="font-mono">{{ $lsd ?: '-' }}</span>
                </div>
                <div class="pt-1">
                    <div><strong>Nama Penyuntik 1 :</strong> <span class="uppercase font-medium">{{ $pemindahan->nama_penyuntik_1 ?: '-' }}</span></div>
                    <div><strong>Nama Penyuntik 2 :</strong> <span class="uppercase font-medium">{{ $pemindahan->nama_penyuntik_2 ?: '-' }}</span></div>
                </div>
            </div>

            <!-- Perenggan 3 -->
            <div class="text-[10pt] text-justify leading-relaxed mb-4">
                3.&nbsp;&nbsp;&nbsp;&nbsp;Bersama-sama ini disertakan surat akuan suntikan dan nombor ternakan seperti di lampiran berkembar.
            </div>

            <div class="text-[10pt] mb-4 space-y-1">
                <div>Sekian untuk makluman dan tindakan pihak tuan selanjutnya .</div>
                <div class="font-bold pt-1">Terima Kasih .</div>
            </div>

            <!-- Slogan Rasmi -->
            <div class="text-[9.5pt] font-bold italic space-y-0.5 mb-4">
                <div>'ISLAM DIJUNJUNG,RAKYAT BERSATU,NEGERI BERKAT, RAJA BERDAULAT'</div>
                <div>'VETERINAR KOMPETEN, MASYARAKAT SEJAHTERA'</div>
            </div>

            <!-- Signature Section -->
            <div class="text-[10pt] space-y-1">
                <div>Saya Yang Menjalankan Amanah,</div>
                <div class="pt-6">
                    <div class="font-bold underline uppercase">{{ $pemindahan->pegawai_nama ?: 'PEGAWAI VETERINAR JAJAHAN' }}</div>
                    <div class="font-medium">Pegawai Perkhidmatan Veterinar Jajahan</div>
                    <div>Pejabat Perkhidmatan Veterinar Jajahan {{ $jajahan }}, Kelantan .</div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
