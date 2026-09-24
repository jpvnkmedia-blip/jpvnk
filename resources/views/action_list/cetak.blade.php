<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>PK-RK-61 ACTION LIST - {{ $actionList->no_bil ?: ('BIL-' . $actionList->id) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white; margin: 0; padding: 0; font-size: 11px; }
            .print-page { padding: 0 !important; max-width: 100% !important; box-shadow: none !important; border: none !important; }
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
            background-color: #f1f5f9;
        }
        .form-border {
            border: 1.5px solid #000;
        }
        .form-border-t { border-top: 1.5px solid #000; }
        .form-border-b { border-bottom: 1.5px solid #000; }
        .form-border-l { border-left: 1.5px solid #000; }
        .form-border-r { border-right: 1.5px solid #000; }
        .box-char {
            display: inline-block;
            width: 18px;
            height: 22px;
            line-height: 22px;
            text-align: center;
            border-right: 1px solid #000;
            font-weight: bold;
            font-family: monospace;
            font-size: 12px;
        }
        .box-char:last-child {
            border-right: none;
        }
    </style>
</head>
<body class="p-6 flex flex-col items-center">

    <!-- Action Bar (No Print) -->
    <div class="no-print mb-6 max-w-4xl w-full flex items-center justify-between gap-3 bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-lg text-xs font-bold font-mono">PK-RK-61</span>
            <span class="text-xs font-bold text-slate-700">Format Cetakan Rasmi Borang Action List</span>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
            </button>
            <button onclick="window.close()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                Tutup
            </button>
        </div>
    </div>

    <!-- Printable Form Sheet (A4 Proportion) -->
    <div class="print-page max-w-4xl w-full bg-white p-8 border border-slate-300 shadow-xl text-slate-950 text-xs">
        
        <!-- Top Header -->
        <div class="relative mb-3">
            <div class="absolute top-0 right-0 text-right">
                <div class="font-bold text-[11px] tracking-wide mb-1 font-mono">PK-RK-61</div>
                <div class="border border-black border-dashed px-3 py-1 text-[11px] font-bold font-mono">
                    BIL: <span class="font-black text-slate-900">{{ $actionList->no_bil ?: ('BIL-' . $actionList->id) }}</span>
                </div>
            </div>

            <div class="text-center pt-2">
                <h1 class="text-sm font-black tracking-wider uppercase underline">ACTION LIST</h1>
                <h2 class="text-xs font-bold uppercase mt-0.5">PEJABAT PERKHIDMATAN VETERINAR</h2>
                <h2 class="text-xs font-bold uppercase">JAJAHAN <span class="border-b border-black px-4">{{ strtoupper($actionList->jajahan) }}</span></h2>
            </div>

            <div class="text-right mt-2 font-bold text-[11px]">
                Tarikh: <span class="border-b border-black px-3">{{ $actionList->tarikh ? $actionList->tarikh->format('d/m/Y') : date('d/m/Y') }}</span>
            </div>
        </div>

        @php
            $srv = is_array($actionList->perkhidmatan_diberi) ? $actionList->perkhidmatan_diberi : [];
            $animals = is_array($actionList->jenis_ternakan) ? $actionList->jenis_ternakan : [];
        @endphp

        <!-- MAIN TABLE BORDER -->
        <div class="form-border">
            
            <!-- SECTION A: MAKLUMAT PELANGGAN -->
            <div class="bg-slate-50/50 p-1.5 font-bold text-[11px] flex justify-between items-center form-border-b">
                <div>A. MAKLUMAT PELANGGAN <span class="font-normal ml-3">[ MASA : <span class="font-bold font-mono">{{ $actionList->masa_pendaftaran ?: '         ' }}</span> ]</span></div>
                <div class="flex gap-4 pr-2 font-bold text-[11px]">
                    <span class="inline-flex items-center gap-1">
                        <span class="w-3.5 h-3.5 border border-black inline-flex items-center justify-center font-bold text-[10px]">{{ $actionList->kategori_pelanggan === 'Individu' ? '✓' : '' }}</span> INDIVIDU
                    </span>
                    <span class="inline-flex items-center gap-1">
                        <span class="w-3.5 h-3.5 border border-black inline-flex items-center justify-center font-bold text-[10px]">{{ $actionList->kategori_pelanggan === 'Syarikat' ? '✓' : '' }}</span> SYARIKAT
                    </span>
                </div>
            </div>

            <div class="divide-y divide-black text-[11px]">
                <!-- 1 Nama -->
                <div class="flex">
                    <div class="w-24 p-1.5 font-semibold form-border-r flex-shrink-0">1 &nbsp; Nama</div>
                    <div class="w-4 p-1.5 font-bold text-center flex-shrink-0">:</div>
                    <div class="p-1.5 flex-1 font-bold uppercase">{{ $actionList->nama_pelanggan }}</div>
                </div>

                <!-- 2 No. K/P -->
                <div class="flex items-center">
                    <div class="w-24 p-1.5 font-semibold form-border-r flex-shrink-0">2 &nbsp; No. K/P</div>
                    <div class="w-4 p-1.5 font-bold text-center flex-shrink-0">:</div>
                    <div class="p-1.5 flex-1 font-mono font-bold tracking-wider">
                        {{ $actionList->no_kp ?: '-' }} <span class="font-normal font-sans ml-2">(BARU)</span>
                    </div>
                </div>

                <!-- 3 Alamat -->
                <div class="flex">
                    <div class="w-24 p-1.5 font-semibold form-border-r flex-shrink-0">3 &nbsp; Alamat</div>
                    <div class="w-4 p-1.5 font-bold text-center flex-shrink-0">:</div>
                    <div class="p-1.5 flex-1">{{ $actionList->alamat ?: '-' }}</div>
                </div>

                <!-- 4 Mukim & Poskod -->
                <div class="flex items-center">
                    <div class="w-24 p-1.5 font-semibold form-border-r flex-shrink-0">4 &nbsp; Mukim</div>
                    <div class="w-4 p-1.5 font-bold text-center flex-shrink-0">:</div>
                    <div class="p-1.5 flex-1">{{ $actionList->mukim ?: '-' }}</div>
                    <div class="p-1.5 font-semibold form-border-l flex items-center gap-1">
                        <span>P O S K O D</span>
                        <div class="border border-black inline-flex ml-2">
                            @php
                                $poskodStr = str_pad($actionList->poskod ?: '16800', 5, ' ', STR_PAD_RIGHT);
                            @endphp
                            @foreach(str_split(substr($poskodStr, 0, 5)) as $char)
                                <span class="box-char">{{ $char }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- 5 Daerah -->
                <div class="flex">
                    <div class="w-24 p-1.5 font-semibold form-border-r flex-shrink-0">5 &nbsp; Daerah</div>
                    <div class="w-4 p-1.5 font-bold text-center flex-shrink-0">:</div>
                    <div class="p-1.5 flex-1">{{ $actionList->daerah ?: ('Jajahan ' . $actionList->jajahan . ', Kelantan.') }}</div>
                </div>

                <!-- 6 Telefon & 20 Rujukan -->
                <div class="flex">
                    <div class="w-24 p-1.5 font-semibold form-border-r flex-shrink-0">6 &nbsp; Telefon</div>
                    <div class="w-4 p-1.5 font-bold text-center flex-shrink-0">:</div>
                    <div class="p-1.5 flex-1 font-mono font-semibold">{{ $actionList->telefon ?: '-' }}</div>
                    <div class="p-1.5 flex-1 form-border-l font-semibold">
                        20. Rujukan : <span class="font-mono font-bold">{{ $actionList->no_rujukan ?: '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- SECTION B: BUTIR-BUTIR PERKHIDMATAN -->
            <div class="bg-slate-50/50 p-1.5 font-bold text-[11px] form-border-t form-border-b">
                B. BUTIR-BUTIR PERKHIDMATAN
            </div>
            <div class="p-2 min-h-[45px] text-[11px] leading-relaxed">
                <span class="font-semibold">7. Catatan ringkas perkhidmatan yang dipohon :</span>
                <div class="mt-1 pl-4">{{ $actionList->catatan_perkhidmatan_dipohon ?: '-' }}</div>
            </div>

            <!-- SECTION C: MAKLUMAT TEMUJANJI -->
            <div class="bg-slate-50/50 p-1.5 font-bold text-[11px] form-border-t form-border-b">
                C. MAKLUMAT TEMUJANJI
            </div>
            <div class="divide-y divide-black text-[11px]">
                <div class="flex items-center">
                    <div class="w-36 p-1.5 font-semibold form-border-r flex-shrink-0">8 &nbsp; Nama Pegawai</div>
                    <div class="w-4 p-1.5 font-bold text-center flex-shrink-0">:</div>
                    <div class="p-1.5 flex-1 font-bold">{{ $actionList->nama_pegawai ?: '-' }}</div>
                    <div class="p-1.5 pr-4 text-right">[ MASA : <span class="font-mono font-bold">{{ $actionList->masa_pegawai ?: '-' }}</span> ]</div>
                </div>

                <div class="flex items-center">
                    <div class="w-36 p-1.5 font-semibold form-border-r flex-shrink-0">9 &nbsp; Masa Temujanji</div>
                    <div class="w-4 p-1.5 font-bold text-center flex-shrink-0">:</div>
                    <div class="p-1.5 flex-1 flex gap-8">
                        <span>MULA: <b class="font-mono">{{ $actionList->masa_temujanji_mula ?: '-' }}</b></span>
                        <span>HINGGA: <b class="font-mono">{{ $actionList->masa_temujanji_hingga ?: '-' }}</b></span>
                    </div>
                </div>

                <div class="flex">
                    <div class="w-36 p-1.5 font-semibold form-border-r flex-shrink-0 leading-tight">
                        10 Maklumat Pelanggan<br><span class="text-[10px] font-normal">(jika berlainan)</span>
                    </div>
                    <div class="w-4 p-1.5 font-bold text-center flex-shrink-0">:</div>
                    <div class="p-1.5 flex-1">{{ $actionList->maklumat_pelanggan_berlainan ?: '-' }}</div>
                </div>

                <div class="flex">
                    <div class="w-36 p-1.5 font-semibold form-border-r flex-shrink-0 leading-tight">
                        11 Maklumat tambahan<br><span class="text-[9px] font-normal">(peta lokasi sila lukis di belakang dan lain-lain)</span>
                    </div>
                    <div class="w-4 p-1.5 font-bold text-center flex-shrink-0">:</div>
                    <div class="p-1.5 flex-1">{{ $actionList->maklumat_tambahan ?: '-' }}</div>
                </div>
            </div>

            <!-- SECTION D: MAKLUMAT PERKHIDMATAN YANG DIBERI -->
            <div class="bg-slate-50/50 p-1.5 font-bold text-[11px] form-border-t form-border-b">
                D. MAKLUMAT PERKHIDMATAN YANG DIBERI
            </div>
            
            <div class="grid grid-cols-12 divide-x divide-black text-[11px]">
                
                <!-- Left Sub-column (12 Checklist) -->
                <div class="col-span-5 p-2 space-y-1.5 leading-tight">
                    <div class="font-semibold mb-1">12 &nbsp; Perkhidmatan:</div>
                    
                    <div class="flex items-start gap-1.5">
                        <span class="w-3.5 h-3.5 border border-black inline-flex items-center justify-center font-bold text-[10px] flex-shrink-0 mt-0.5">{{ !empty($srv['rawatan_lapangan']) ? '✓' : '' }}</span>
                        <span>Rawatan Di Lapangan</span>
                    </div>

                    <div class="flex items-start gap-1.5">
                        <span class="w-3.5 h-3.5 border border-black inline-flex items-center justify-center font-bold text-[10px] flex-shrink-0 mt-0.5">{{ !empty($srv['rawatan_klinik']) ? '✓' : '' }}</span>
                        <span>Rawatan Di Klinik</span>
                    </div>

                    <div class="flex items-start gap-1.5">
                        <span class="w-3.5 h-3.5 border border-black inline-flex items-center justify-center font-bold text-[10px] flex-shrink-0 mt-0.5">{{ !empty($srv['pembedahan']) ? '✓' : '' }}</span>
                        <span>Pembedahan <span class="border-b border-black font-semibold">{{ $actionList->keterangan_pembedahan ?: '_________________' }}</span></span>
                    </div>

                    <div class="flex items-start gap-1.5">
                        <span class="w-3.5 h-3.5 border border-black inline-flex items-center justify-center font-bold text-[10px] flex-shrink-0 mt-0.5">{{ !empty($srv['pemantauan_pawah']) ? '✓' : '' }}</span>
                        <span>Pemantauan Pawah Negeri</span>
                    </div>

                    <div class="flex items-start gap-1.5">
                        <span class="w-3.5 h-3.5 border border-black inline-flex items-center justify-center font-bold text-[10px] flex-shrink-0 mt-0.5">{{ !empty($srv['pemantauan_projek']) ? '✓' : '' }}</span>
                        <span>Pemantauan Projek <span class="border-b border-black font-semibold">{{ $actionList->keterangan_projek ?: '_________' }}</span></span>
                    </div>

                    <div class="flex items-start gap-1.5">
                        <span class="w-3.5 h-3.5 border border-black inline-flex items-center justify-center font-bold text-[10px] flex-shrink-0 mt-0.5">{{ !empty($srv['pemantauan_trust']) ? '✓' : '' }}</span>
                        <span>Pemantauan TRUST</span>
                    </div>

                    <div class="flex items-start gap-1.5">
                        <span class="w-3.5 h-3.5 border border-black inline-flex items-center justify-center font-bold text-[10px] flex-shrink-0 mt-0.5">{{ !empty($srv['lawatan_terancang']) ? '✓' : '' }}</span>
                        <span>Lawatan Terancang & berJadual</span>
                    </div>

                    <div class="flex items-start gap-1.5">
                        <span class="w-3.5 h-3.5 border border-black inline-flex items-center justify-center font-bold text-[10px] flex-shrink-0 mt-0.5">{{ !empty($srv['perkhidmatan_lain']) ? '✓' : '' }}</span>
                        <span>Lain-lain : <span class="border-b border-black font-semibold">{{ $actionList->keterangan_lain ?: '_____________' }}</span></span>
                    </div>
                </div>

                <!-- Right Sub-column (13, 17, 21) -->
                <div class="col-span-7 divide-y divide-black">
                    <!-- 13 Catatan ringkas & Jenis Ternakan -->
                    <div class="p-2 space-y-1">
                        <div class="font-semibold">13 &nbsp; Catatan ringkas :</div>
                        <div class="pl-3 space-y-1">
                            <div>1.Jenis Ternakan:</div>
                            <div class="grid grid-cols-2 gap-x-2 gap-y-1 text-[10px]">
                                <span class="flex items-center gap-1"><span class="w-3 h-3 border border-black inline-flex items-center justify-center font-bold text-[9px]">{{ in_array('Lembu', $animals) ? '✓' : '' }}</span> Lembu</span>
                                <span class="flex items-center gap-1"><span class="w-3 h-3 border border-black inline-flex items-center justify-center font-bold text-[9px]">{{ in_array('Kerbau', $animals) ? '✓' : '' }}</span> Kerbau</span>
                                <span class="flex items-center gap-1"><span class="w-3 h-3 border border-black inline-flex items-center justify-center font-bold text-[9px]">{{ in_array('Kambing', $animals) ? '✓' : '' }}</span> Kambing</span>
                                <span class="flex items-center gap-1"><span class="w-3 h-3 border border-black inline-flex items-center justify-center font-bold text-[9px]">{{ in_array('Biri-biri', $animals) ? '✓' : '' }}</span> Biri-biri</span>
                                <span class="flex items-center gap-1"><span class="w-3 h-3 border border-black inline-flex items-center justify-center font-bold text-[9px]">{{ in_array('Kuda', $animals) ? '✓' : '' }}</span> Kuda</span>
                                <span class="flex items-center gap-1"><span class="w-3 h-3 border border-black inline-flex items-center justify-center font-bold text-[9px]">{{ in_array('Ayam Pedaging', $animals) ? '✓' : '' }}</span> Ayam Pedaging</span>
                                <span class="flex items-center gap-1"><span class="w-3 h-3 border border-black inline-flex items-center justify-center font-bold text-[9px]">{{ in_array('Kucing', $animals) ? '✓' : '' }}</span> Kucing</span>
                                <span class="flex items-center gap-1"><span class="w-3 h-3 border border-black inline-flex items-center justify-center font-bold text-[9px]">{{ in_array('Anjing', $animals) ? '✓' : '' }}</span> Anjing</span>
                                <span class="flex items-center gap-1"><span class="w-3 h-3 border border-black inline-flex items-center justify-center font-bold text-[9px]">{{ in_array('Arnab', $animals) ? '✓' : '' }}</span> Arnab</span>
                                <span class="flex items-center gap-1"><span class="w-3 h-3 border border-black inline-flex items-center justify-center font-bold text-[9px]">{{ in_array('Lain-lain', $animals) ? '✓' : '' }}</span> Lain-lain {{ $actionList->jenis_ternakan_lain ? "({$actionList->jenis_ternakan_lain})" : '' }}</span>
                            </div>

                            <div class="pt-1 text-[11px] space-y-0.5">
                                <div>2.Bil. Ternakan <span class="border-b border-black font-bold font-mono px-3">{{ $actionList->bil_ternakan ?? '____' }}</span> ekor</div>
                                <div>3.Bil. yang ada <span class="border-b border-black font-bold font-mono px-3">{{ $actionList->bil_yang_ada ?? '____' }}</span> ekor</div>
                            </div>
                        </div>
                    </div>

                    <!-- 17 Laporan -->
                    <div class="p-2 min-h-[60px]">
                        <div class="font-semibold">17 &nbsp; Laporan :</div>
                        <div class="pl-3 pt-1 text-[11px] leading-relaxed whitespace-pre-line">{{ $actionList->laporan ?: '-' }}</div>
                    </div>

                    <!-- 21 Penggunaan Ubat -->
                    <div class="p-2 min-h-[50px]">
                        <div class="font-semibold">21 &nbsp; Penggunaan Ubat :</div>
                        <div class="pl-3 pt-1 text-[11px] leading-relaxed whitespace-pre-line">{{ $actionList->penggunaan_ubat ?: '-' }}</div>
                    </div>
                </div>

            </div>

            <!-- SECTION E: PENGAKUAN PELANGGAN -->
            <div class="bg-slate-50/50 p-1.5 font-bold text-[11px] form-border-t form-border-b">
                E. PENGAKUAN PELANGGAN
            </div>

            <div class="grid grid-cols-12 divide-x divide-black text-[11px]">
                <!-- 14 Tandatangan (Left) -->
                <div class="col-span-5 p-2 space-y-2">
                    <div class="font-semibold">14 &nbsp; Tandatangan :</div>
                    <div class="h-10 border-b border-black/50 max-w-[180px]"></div>
                    <div>Nama : <b class="uppercase">{{ $actionList->tandatangan_pelanggan_nama ?: ($actionList->nama_pelanggan ?: '-') }}</b></div>
                    <div>Tarikh : {{ $actionList->tandatangan_pelanggan_tarikh ? $actionList->tandatangan_pelanggan_tarikh->format('d/m/Y') : ($actionList->tarikh ? $actionList->tarikh->format('d/m/Y') : '-') }}</div>
                    <div>[ MASA : <span class="font-mono">{{ $actionList->tandatangan_pelanggan_masa ?: '-' }}</span> ]</div>
                </div>

                <!-- 15, 16 (Right) -->
                <div class="col-span-7 p-2 space-y-2 divide-y divide-black">
                    <div class="space-y-1">
                        <div class="font-semibold">15 &nbsp; Saya,</div>
                        <div class="pl-6 space-y-1 text-[11px]">
                            <div class="flex items-center gap-1.5">
                                <span class="w-3.5 h-3.5 border border-black inline-flex items-center justify-center font-bold text-[10px]">{{ $actionList->kepuasan_pelanggan === 'Puashati' ? '✓' : '' }}</span> Puashati
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-3.5 h-3.5 border border-black inline-flex items-center justify-center font-bold text-[10px]">{{ $actionList->kepuasan_pelanggan === 'Tidak puashati' ? '✓' : '' }}</span> Tidak puashati
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-3.5 h-3.5 border border-black inline-flex items-center justify-center font-bold text-[10px]">{{ $actionList->kepuasan_pelanggan === 'Boleh dipertimbangkan' ? '✓' : '' }}</span> Boleh dipertimbangkan
                            </div>
                        </div>
                    </div>

                    <div class="pt-2">
                        <div class="font-semibold">16 &nbsp; Cadangan :</div>
                        <div class="pl-6 pt-0.5">{{ $actionList->cadangan_pelanggan ?: '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- Bottom Row: 18 Bayaran & 19 Pengesahan Ulasan -->
            <div class="grid grid-cols-12 divide-x divide-black form-border-t text-[11px]">
                <div class="col-span-5 p-2 space-y-1">
                    <div>18 &nbsp; Bayaran: <b class="font-mono">RM {{ number_format($actionList->bayaran, 2) }}</b></div>
                    <div>&nbsp; &nbsp; &nbsp; No. Resit: <b class="font-mono">{{ $actionList->no_resit ?: '-' }}</b></div>
                </div>

                <div class="col-span-7 p-2">
                    <div class="font-semibold">19 &nbsp; Pengesahan dan Ulasan :</div>
                    <div class="pl-3 pt-1 text-[11px] leading-relaxed">{{ $actionList->pengesahan_ulasan_pegawai ?: 'Disahkan perkhidmatan telah diselesaikan.' }}</div>
                </div>
            </div>

        </div>

        <!-- Footer Instructions -->
        <div class="mt-3 text-[10px] text-slate-700 italic space-y-0.5">
            <div>* Setiap dokumen hendaklah disahkan oleh pegawai projek</div>
            <div>* Hanya satu borang untuk setiap kes/pelanggan</div>
        </div>

    </div>

</body>
</html>
