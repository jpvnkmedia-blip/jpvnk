<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>SET LENGKAP PERMIT PEMINDAHAN TERNAKAN - {{ $pemindahan->no_rujukan }}</title>
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
        .page-break {
            page-break-before: always;
            break-before: page;
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
            .page-break {
                page-break-before: always !important;
                break-before: page !important;
            }
        }
    </style>
</head>
<body class="p-2 sm:p-4 flex flex-col items-center">

    <!-- Screen-Only Actions -->
    <div class="no-print mb-6 flex flex-wrap gap-3">
        <button onclick="window.print()" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm rounded-xl shadow-lg transition flex items-center gap-2">
            <i class="fa-solid fa-print text-amber-400"></i>
            <span>Cetak 1 Set Lengkap (4 Halaman Penuh)</span>
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
        $tarikhPindahStr = $pemindahan->tarikh_jangka_pindah ? $pemindahan->tarikh_jangka_pindah->format('d.m.Y') : date('d.m.Y', strtotime('+7 days'));
        $fmdP1 = $pemindahan->tarikh_fmd_p1 ? $pemindahan->tarikh_fmd_p1->format('d.m.Y') : '18.5.2026';
        $fmdP2 = $pemindahan->tarikh_fmd_p2 ? $pemindahan->tarikh_fmd_p2->format('d.m.Y') : '16.6.2026';
        $fmdBooster = $pemindahan->tarikh_fmd_booster ? $pemindahan->tarikh_fmd_booster->format('d.m.Y') : '';
        $lsd = $pemindahan->tarikh_lsd ? $pemindahan->tarikh_lsd->format('d.m.Y') : '';
        $spesies = strtoupper($pemindahan->jenis_ternakan ?? 'LEMBU');
        $j = (int) $pemindahan->bilangan_jantan;
        $b = (int) $pemindahan->bilangan_betina;
        $kuantitiText = '';
        if ($j > 0 && $b > 0) {
            $kuantitiText = "{$j} JANTAN dan {$b} BETINA";
        } elseif ($j > 0) {
            $kuantitiText = "{$j} JANTAN";
        } elseif ($b > 0) {
            $kuantitiText = "{$b} BETINA";
        }
        $isSembelihan = strtoupper($pemindahan->tujuan_pemindahan) === 'SEMBELIHAN';
        $tagList = $pemindahan->senarai_tag_list;
    @endphp

    <div class="max-w-[210mm] w-full space-y-4">

        <!-- ==================================================== -->
        <!-- HALAMAN 1: SURAT PENGESAHAN TARIKH SUNTIKAN FMD (Kn. 156) -->
        <!-- ==================================================== -->
        <div class="form-page p-8 sm:p-12 shadow-2xl bg-white border border-slate-200 text-[10.5pt] leading-normal">
            <!-- Top Kn. 156 Indicator -->
            <div class="text-[9.5pt] font-sans font-medium text-slate-700 mb-1">
                Kn. 156
            </div>

            <!-- Official Letterhead -->
            <div class="border-b-2 border-slate-800 pb-2 mb-3">
                <div class="grid grid-cols-12 items-center">
                    <div class="col-span-2 flex justify-start">
                        <img src="{{ asset('images/jata-kelantan.png') }}" alt="Jata Kelantan" class="h-20 w-auto object-contain" onerror="this.src='{{ asset('images/logo.png') }}'">
                    </div>
                    <div class="col-span-8 text-center space-y-0.5">
                        <div class="text-[14pt] font-serif font-bold text-center leading-tight" style="direction: rtl;">
                            {{ $jajahanJawi }}
                        </div>
                        <div class="text-[12pt] font-bold uppercase tracking-tight">
                            {{ $pejabat['nama'] }}
                        </div>
                        <div class="text-[11.5pt] font-bold uppercase tracking-tight">
                            {{ $pejabat['jajahan_title'] }}
                        </div>
                        <div class="text-[9.5pt] uppercase">
                            {{ $alamat1 }}
                        </div>
                        <div class="text-[9.5pt] font-bold uppercase">
                            {{ $alamat2 }}
                        </div>
                    </div>
                    <div class="col-span-2 flex flex-col items-end justify-center">
                        <img src="{{ asset('images/dvs-logo.png') }}" alt="DVS Logo" class="h-14 w-auto object-contain mb-1" onerror="this.style.display='none'">
                        <div class="text-right text-[8pt] leading-tight text-slate-800 font-sans">
                            <div>Tel : {{ $tel }}</div>
                            <div>Faks : {{ $faks }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rujukan & Tarikh (Aligned Right) -->
            <div class="text-right text-[10.5pt] mb-3 space-y-0.5">
                <div class="grid grid-cols-12">
                    <div class="col-span-6"></div>
                    <div class="col-span-6 text-left pl-6">
                        <div><span class="inline-block w-28">Rujukan Kami</span> : <span class="font-mono font-bold">{{ $pemindahan->no_rujukan }}</span></div>
                        <div><span class="inline-block w-28">Rujukan Tuan</span> : </div>
                        <div><span class="inline-block w-28">Tarikh</span> : <span>{{ $tarikhMohonStr }}</span></div>
                    </div>
                </div>
            </div>

            <!-- Penerima Surat -->
            <div class="text-[10.5pt] space-y-0.5 mb-3">
                <div>Ke Majlis ,</div>
                <div class="font-bold pt-0.5">YABhg Tuan Pengarah</div>
                <div>Jabatan Perkhidmatan Veterinar Negeri Kelantan</div>
                <div>Kubang Kerian</div>
                <div>16150 Kota Bharu</div>
                <div class="font-bold">Kelantan.</div>
            </div>

            <div class="text-[10.5pt] mb-2 font-bold">
                Tuan,
            </div>

            <!-- Tajuk Surat -->
            <div class="text-[11.5pt] font-bold uppercase tracking-wide mb-3 underline">
                PENGESAHAN TARIKH SUNTIKAN FMD BAGI PEMINDAHAN TERNAKAN
            </div>

            <!-- Perenggan 1 -->
            <div class="text-[10.5pt] text-justify leading-relaxed mb-3">
                Dengan segala hormatnya saya merujuk kepada perkara di atas .
            </div>

            <!-- Perenggan 2 -->
            <div class="text-[10.5pt] text-justify leading-relaxed mb-3">
                <p>
                    2.&nbsp;&nbsp;&nbsp;&nbsp;Adalah dimaklumkan bahawa ternakan milik <strong class="uppercase">{{ $pemindahan->pemohon_nama }}</strong> sebanyak <strong>{{ $j > 0 ? $j : '' }}</strong> ekor <strong>{{ $spesies }}</strong> Jantan dan <strong>{{ $b > 0 ? $b : '' }}</strong> ekor <strong>{{ $spesies }}</strong> betina telah disuntik vaksin FMD dan didapati ternakan tersebut tiada tanda – tanda penyakit klinikal FMD &amp; LSD /penyakit berjangkit / sesuai untuk kegunaan manusia / tidak dari ternakan berpenyakit dan maklumatnya adalah seperti berikut:
                </p>
            </div>

            <!-- Maklumat Suntikan -->
            <div class="text-[10.5pt] space-y-1.5 mb-3 pl-6">
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
            <div class="text-[10.5pt] text-justify leading-relaxed mb-3">
                3.&nbsp;&nbsp;&nbsp;&nbsp;Bersama-sama ini disertakan surat akuan suntikan dan nombor ternakan seperti di lampiran berkembar.
            </div>

            <div class="text-[10.5pt] mb-3 space-y-0.5">
                <div>Sekian untuk makluman dan tindakan pihak tuan selanjutnya .</div>
                <div class="font-bold pt-0.5">Terima Kasih .</div>
            </div>

            <!-- Slogan Rasmi -->
            <div class="text-[10pt] font-bold italic space-y-0.5 mb-3">
                <div>'ISLAM DIJUNJUNG,RAKYAT BERSATU,NEGERI BERKAT, RAJA BERDAULAT'</div>
                <div>'VETERINAR KOMPETEN, MASYARAKAT SEJAHTERA'</div>
            </div>

            <!-- Signature Section -->
            <div class="text-[10.5pt] space-y-0.5 pt-1">
                <div>Saya Yang Menjalankan Amanah,</div>
                <div class="pt-6">
                    <div class="font-bold underline uppercase">{{ $pemindahan->pegawai_nama ?: 'PEGAWAI VETERINAR JAJAHAN' }}</div>
                    <div class="font-medium">Pegawai Perkhidmatan Veterinar Jajahan</div>
                    <div>Pejabat Perkhidmatan Veterinar Jajahan {{ $jajahan }}, Kelantan .</div>
                </div>
            </div>
        </div>


        <!-- ==================================================== -->
        <!-- HALAMAN 2: BORANG PERMOHONAN PEMINDAHAN TERNAKAN / PRODUK -->
        <!-- ==================================================== -->
        <div class="form-page page-break p-8 sm:p-12 shadow-2xl bg-white border border-slate-200 text-[10.5pt] leading-normal">
            <!-- Rujukan & Tarikh (Aligned Right) -->
            <div class="text-right text-[10.5pt] mb-3 space-y-0.5">
                <div class="grid grid-cols-12">
                    <div class="col-span-6"></div>
                    <div class="col-span-6 text-left pl-6">
                        <div><span class="inline-block w-32">No Rujukan Permit</span> : <span class="font-mono font-bold">{{ $pemindahan->no_rujukan }}</span></div>
                        <div><span class="inline-block w-32">Tarikh</span> : <span>{{ $tarikhMohonStr }}</span></div>
                    </div>
                </div>
            </div>

            <!-- Kepada Pegawai -->
            <div class="text-[10.5pt] space-y-0.5 mb-3">
                <div>Kepada :</div>
                <div class="pt-0.5">YBrs Pegawai Perkhidmatan Veterinar</div>
                <div>Jajahan {{ $jajahanUpper }} ,</div>
                <div>{{ $alamat1 }},</div>
                <div class="font-bold">{{ $alamat2 }} .</div>
            </div>

            <div class="text-[10.5pt] mb-2 font-bold">
                Tuan/Puan,
            </div>

            <!-- Tajuk -->
            <div class="text-[12pt] font-bold uppercase tracking-wide mb-3 underline text-center">
                PERMOHONAN PEMINDAHAN TERNAKAN / PRODUK
            </div>

            <!-- Paragraf Pembuka -->
            <div class="text-[10.5pt] text-justify leading-relaxed mb-3">
                Dengan segala hormatnya, saya ingin memohon kebenaran untuk memindah ternakan / produk bagi tujuan <strong class="uppercase">{{ $pemindahan->tujuan_pemindahan }}</strong> dan dijangka bertolak pada <strong class="underline">{{ $tarikhPindahStr }}</strong> ( Tarikh permohonan pemindahan mestilah sekurang-kurangnya tujuh ( 7 ) hari sebelum tarikh jangkaan pemindahan )
            </div>

            <div class="text-[10.5pt] mb-2">
                Maklumat adalah seperti berikut:-
            </div>

            <!-- 2-Column Table: MAKLUMAT PEMOHON vs MAKLUMAT PENERIMA -->
            <table class="w-full text-left text-[10pt] mb-6 border-collapse">
                <thead>
                    <tr class="font-bold uppercase text-center bg-slate-50">
                        <th class="py-2 px-3 w-1/2">MAKLUMAT PEMOHON /PENGHANTAR</th>
                        <th class="py-2 px-3 w-1/2">MAKLUMAT PENERIMA</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black align-top">
                    <!-- Row 1: Nama -->
                    <tr>
                        <td class="p-2.5">
                            <div class="text-[9pt] text-slate-600">Nama Penuh @syarikat</div>
                            <div class="font-bold uppercase pt-0.5">
                                {{ $pemindahan->pemohon_nama }}
                                @if($pemindahan->pemohon_id_premis)
                                    <span class="font-mono ml-1">({{ $pemindahan->pemohon_id_premis }})</span>
                                @endif
                            </div>
                        </td>
                        <td class="p-2.5">
                            <div class="text-[9pt] text-slate-600">Nama Penuh</div>
                            <div class="font-bold uppercase pt-0.5">{{ $pemindahan->penerima_nama }}</div>
                        </td>
                    </tr>

                    <!-- Row 2: No IC -->
                    <tr>
                        <td class="p-2.5">
                            <div class="text-[9pt] text-slate-600">No.Ic</div>
                            <div class="font-mono pt-0.5">{{ $pemindahan->pemohon_ic ?: '-' }}</div>
                        </td>
                        <td class="p-2.5">
                            <div class="text-[9pt] text-slate-600">No IC</div>
                            <div class="font-mono pt-0.5">{{ $pemindahan->penerima_ic ?: '-' }}</div>
                        </td>
                    </tr>

                    <!-- Row 3: No Tel -->
                    <tr>
                        <td class="p-2.5">
                            <div class="text-[9pt] text-slate-600">No.Tel</div>
                            <div class="font-mono pt-0.5">{{ $pemindahan->pemohon_tel ?: '-' }}</div>
                        </td>
                        <td class="p-2.5">
                            <div class="text-[9pt] text-slate-600">No Tel</div>
                            <div class="font-mono pt-0.5">{{ $pemindahan->penerima_tel ?: '-' }}</div>
                        </td>
                    </tr>

                    <!-- Row 4: Jenis Ternakan & ID Premis -->
                    <tr>
                        <td class="p-2.5">
                            <div class="text-[9pt] text-slate-600">Jenis Ternakan/ Produk</div>
                            <div class="font-bold uppercase pt-0.5">{{ $pemindahan->jenis_ternakan ?? 'LEMBU' }}</div>
                        </td>
                        <td class="p-2.5">
                            <div class="text-[9pt] text-slate-600">ID Premis</div>
                            <div class="font-mono font-bold uppercase pt-0.5">{{ $pemindahan->penerima_id_premis ?: '-' }}</div>
                        </td>
                    </tr>

                    <!-- Row 5: Bilangan Ternakan & Alamat Penerima -->
                    <tr>
                        <td class="p-2.5">
                            <div class="text-[9pt] text-slate-600">Bilangan Ternakan/ Kuantiti Produk</div>
                            <div class="font-medium pt-0.5">
                                {{ $kuantitiText }}
                                <div class="text-[8.5pt] text-slate-700 italic pt-0.5">( Jika jantina berlainan sila Nyatakan bilangan utk J/B )</div>
                            </div>
                        </td>
                        <td class="p-2.5" rowspan="3">
                            <div class="text-[9pt] text-slate-600">Alamat Penuh Lokasi Destinasi</div>
                            <div class="uppercase pt-0.5 leading-tight">
                                {{ $pemindahan->penerima_alamat ?: '-' }}
                            </div>
                        </td>
                    </tr>

                    <!-- Row 6: Alamat Pemohon -->
                    <tr>
                        <td class="p-2.5">
                            <div class="text-[9pt] text-slate-600">Alamat Penuh Lokasi Ternakan/ produk</div>
                            <div class="uppercase pt-0.5 leading-tight">
                                {{ $pemindahan->pemohon_alamat ?: '-' }}
                            </div>
                        </td>
                    </tr>

                    <!-- Row 7: No Plat Kenderaan -->
                    <tr>
                        <td class="p-2.5">
                            <div class="text-[9pt] text-slate-600">No plat Kenderaan</div>
                            <div class="font-mono font-bold uppercase pt-0.5">{{ $pemindahan->no_kenderaan ?: '-' }}</div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Tandatangan Pemohon -->
            <div class="text-[10.5pt] pt-4 space-y-1">
                <div>Saya Yang Benar,</div>
                <div class="pt-8">
                    <div>.......................................................</div>
                    <div class="font-bold pt-1 uppercase">PEMOHON / PEMUNYA LADANG TERNAKAN</div>
                </div>
            </div>
        </div>


        <!-- ==================================================== -->
        <!-- HALAMAN 3: DEKLARASI STATUS HAIWAN (RUMINAN) -->
        <!-- ==================================================== -->
        <div class="form-page page-break p-6 sm:p-8 shadow-2xl bg-white border border-slate-200 text-[9pt] leading-tight">
            <!-- Top Header & Form Ref -->
            <div class="flex justify-between items-start mb-1">
                <div></div>
                <div class="font-sans font-bold text-[8.5pt] text-right">
                    DVS/DSHR/0117/9/2021
                </div>
            </div>

            <!-- Title Box -->
            <div class="border border-black text-center py-1 font-bold text-[10.5pt] uppercase tracking-wide mb-2 bg-slate-50">
                DEKLARASI STATUS HAIWAN (RUMINAN)
            </div>

            <!-- 3-Column Top Info Table -->
            <table class="w-full text-left text-[8.5pt] mb-2 border-collapse">
                <tbody>
                    <tr class="align-top">
                        <!-- Box 1: Doktor Veterinar -->
                        <td class="p-2 w-1/3">
                            <div class="font-bold uppercase text-[8pt] mb-1">MAKLUMAT DOKTOR VETERINAR LADANG ATAU SWASTA</div>
                            <div class="space-y-0.5">
                                <div>Nama Doktor : <span>{{ $pemindahan->nama_doktor_veterinar ?: '-' }}</span></div>
                                <div>No. Kad Pengenalan : <span>{{ $pemindahan->ic_doktor_veterinar ?: '-' }}</span></div>
                                <div>Telefon : <span>{{ $pemindahan->tel_doktor_veterinar ?: '-' }}</span></div>
                                <div>No. Pendaftaran Akta 1974 : <span>{{ $pemindahan->no_pendaftaran_doktor ?: '-' }}</span></div>
                            </div>
                        </td>

                        <!-- Box 2: Pemilik Ladang -->
                        <td class="p-2 w-1/3">
                            <div class="font-bold uppercase text-[8pt] mb-1">MAKLUMAT PEMILIK TERNAKAN/LADANG</div>
                            <div class="space-y-0.5">
                                <div>Nama Tuan Punya : <strong class="uppercase">{{ $pemindahan->pemohon_nama }}</strong></div>
                                <div>Alamat Ladang : <span class="uppercase">{{ $pemindahan->pemohon_alamat }}</span></div>
                                <div>Tel : <span>{{ $pemindahan->pemohon_tel ?: '-' }}</span></div>
                                <div>Tarikh pemindahan : <strong>{{ $tarikhPindahStr }}</strong></div>
                                <div>Destinasi : <span class="uppercase">{{ $pemindahan->penerima_nama }}</span></div>
                            </div>
                        </td>

                        <!-- Box 3: Maklumat Rujukan -->
                        <td class="p-2 w-1/3">
                            <div class="space-y-1">
                                <div><strong>NO. RUJUKAN:</strong> <span class="font-mono font-bold">{{ $pemindahan->no_rujukan }}</span></div>
                                <div><strong>ID Premis:</strong> <span class="font-mono font-bold uppercase">{{ $pemindahan->pemohon_id_premis ?: '-' }}</span></div>
                                <div><strong>No. MyGAP:</strong> <span>{{ $pemindahan->no_mygap ?: '-' }}</span></div>
                                <div class="pt-0.5"><strong>Tujuan :</strong> <strong class="uppercase">{{ $pemindahan->tujuan_pemindahan }}</strong></div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Spesies, Bilangan, No Lori Table -->
            <table class="w-full text-left text-[8.5pt] mb-2 border-collapse font-bold uppercase">
                <tbody>
                    <tr>
                        <td class="p-1.5 w-1/3">Spesis : <span class="font-normal">{{ $spesies }}</span></td>
                        <td class="p-1.5 w-1/3">Bilangan per lori : <span class="font-normal">{{ $pemindahan->format_ringkas_jantina }}</span></td>
                        <td class="p-1.5 w-1/3">No. Lori: <span class="font-mono">{{ $pemindahan->no_kenderaan ?: '-' }}</span></td>
                    </tr>
                </tbody>
            </table>

            <!-- Seksyen Kegunaan Sembelihan -->
            <table class="w-full text-left text-[8.5pt] mb-2 border-collapse">
                <thead>
                    <tr class="bg-slate-50 font-bold text-center">
                        <th colspan="2" class="py-1">UNTUK KEGUNAAN SEMBELIHAN</th>
                    </tr>
                </thead>
                <tbody class="align-top">
                    <tr>
                        <td class="p-1.5 w-1/2">
                            <div>Nama &amp; Alamat Rumah sembelih : <span>{{ $isSembelihan ? $pemindahan->nama_rumah_sembelih : '-' }}</span></div>
                        </td>
                        <td class="p-1.5 w-1/2 space-y-0.5">
                            <div>Tarikh keluar dari ladang : <span>{{ $isSembelihan && $pemindahan->tarikh_keluar_ladang ? $pemindahan->tarikh_keluar_ladang->format('d.m.Y H:i') : $tarikhPindahStr }}</span></div>
                            <div>Tarikh ternakan disembelih : <span>{{ $isSembelihan && $pemindahan->tarikh_sembelih ? $pemindahan->tarikh_sembelih->format('d.m.Y') : '-' }}</span></div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- BAHAGIAN A: STATUS PENYAKIT -->
            <div class="bg-slate-700 text-white font-bold px-2 py-1 text-[8.5pt] tracking-wider uppercase mb-1">
                BAHAGIAN A: STATUS PENYAKIT
            </div>

            <div class="border border-black p-2 text-[8pt] mb-2 space-y-1 leading-tight">
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
                        <div class="pl-4 space-y-1">
                            <div class="flex items-center gap-1.5">
                                <span class="font-mono font-bold">[✓]</span>
                                <div>FMD Tarikh suntikan : (P1) <strong class="font-mono">{{ $fmdP1 }}</strong> &nbsp;&nbsp;(P2) <strong class="font-mono">{{ $fmdP2 }}</strong> &nbsp;&nbsp;(Booster) <strong class="font-mono">{{ $fmdBooster }}</strong></div>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="font-mono font-bold">[{{ $lsd ? '✓' : ' ' }}]</span>
                                <div>LSD Tarikh suntikan : <strong class="font-mono">{{ $lsd ?: '-' }}</strong></div>
                            </div>
                            <div class="pt-0.5">
                                Lain-lain vaksin (nyatakan): <span class="border-b border-dotted border-black inline-block w-44">{{ $pemindahan->lain_vaksin_nama }}</span> &nbsp;Tarikh suntikan: <span class="border-b border-dotted border-black inline-block w-28">{{ $pemindahan->lain_vaksin_tarikh ? $pemindahan->lain_vaksin_tarikh->format('d.m.Y') : '' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BAHAGIAN B: TEMPOH PENARIKAN BALIK UBATAN VETERINAR -->
            <div class="bg-slate-700 text-white font-bold px-2 py-1 text-[8.5pt] tracking-wider uppercase mb-1">
                BAHAGIAN B: TEMPOH PENARIKAN BALIK UBATAN VETERINAR
            </div>

            <div class="border border-black p-2 text-[8pt] mb-2 space-y-1 leading-tight">
                <div class="font-bold underline">Deklarasi:</div>
                <div>Semua ternakan ini telah melepasi tempoh penarikan balik ubatan veterinar seperti yang disarankan oleh pengeluar.</div>
                <div class="pt-0.5">
                    Tarikh rawatan terakhir : <span class="border-b border-dotted border-black inline-block w-36">{{ $pemindahan->tarikh_rawatan_terakhir ? $pemindahan->tarikh_rawatan_terakhir->format('d.m.Y') : '-' }}</span> &nbsp;&nbsp;&nbsp;&nbsp;Nama/ Jenis Ubatan : <span class="border-b border-dotted border-black inline-block w-52">{{ $pemindahan->nama_ubat_terakhir ?: '-' }}</span>
                </div>
                <div>
                    Cara rawatan : Suntikan/ Minuman / Makanan/ Lain-lain................................
                </div>
            </div>

            <!-- BAHAGIAN C / PENGESAHAN -->
            <div class="border border-black p-2 text-[8.5pt] mb-2">
                <div class="font-bold underline mb-1 text-[8pt]">Deklarasi:</div>
                <div class="text-center font-bold text-[8.5pt] mb-1.5">
                    Saya dengan ini mengesahkan kenyataan di atas adalah benar dan tepat
                </div>

                <div class="grid grid-cols-2 gap-8 items-end pt-1">
                    <div>
                        <div class="pt-4">.......................................................</div>
                        <div class="font-bold uppercase pt-0.5">{{ $pemindahan->pemohon_nama }}</div>
                        <div class="text-[7.5pt] leading-tight text-slate-700">
                            **Tandatangan Veterinawan Ladang/<br>
                            Tuan Punya Ternakan/Ladang (**tandakan<br>
                            Cop rasmi:
                        </div>
                    </div>

                    <div class="space-y-1 text-[8pt]">
                        <div>No. Telefon/Emel: <span class="font-bold">{{ $pemindahan->pemohon_tel ?: '-' }}</span></div>
                        <div>Tarikh: <span>{{ $tarikhMohonStr }}</span></div>
                    </div>
                </div>
            </div>

            <!-- Footer Notes -->
            <div class="text-[8pt] italic text-slate-800 space-y-0.5">
                <div>Borang ini hendaklah ditandatangani oleh Veterinawan Ladang atau Tuan Punya Ternakan/Ladang</div>
                <div class="font-bold text-center uppercase tracking-wide pt-0.5">
                    Sah Untuk Satu Perjalanan Sahaja
                </div>
            </div>
        </div>


        <!-- ==================================================== -->
        <!-- HALAMAN 4: LAMPIRAN: SENARAI PENGENALAN TERNAKAN (50 TAG) -->
        <!-- ==================================================== -->
        <div class="form-page page-break p-8 sm:p-10 shadow-2xl bg-white border border-slate-200 text-[9.5pt] leading-normal">
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
