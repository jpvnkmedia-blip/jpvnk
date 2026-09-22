<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borang A - Permohonan Lesen Perladangan Unggas (Enakmen 2005)</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-veterinar.png') }}">
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
            line-height: 1.35;
        }
        .dotted-line {
            border-bottom: 1px dotted #222;
            display: inline-block;
            min-height: 1.25em;
        }
        .checkbox-box {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 16px;
            border: 1px solid #000;
            margin: 0 4px;
            font-size: 11px;
            font-weight: bold;
            vertical-align: middle;
        }
        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm 15mm;
            }
            .no-print {
                display: none !important;
            }
            html, body {
                padding: 0 !important;
                margin: 0 !important;
                background: #fff !important;
                font-size: 12.5px !important;
                line-height: 1.35 !important;
                width: 100% !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .sheet {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
                background: #fff !important;
                box-sizing: border-box !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            .page-1 {
                page-break-after: always !important;
                break-after: page !important;
                page-break-before: avoid !important;
                break-before: avoid !important;
            }
            .page-2 {
                page-break-before: auto !important;
                break-before: auto !important;
                page-break-after: avoid !important;
                break-after: avoid !important;
            }
        }
    </style>
</head>
<body class="p-4 sm:p-8 flex flex-col items-center text-[13px]">

    <!-- Print Floating Action Bar (Screen Only) -->
    <div class="no-print mb-6 flex gap-3 z-50">
        <button onclick="window.print()" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-sans font-bold text-sm rounded-xl shadow-lg transition flex items-center gap-2">
            <i class="fa-solid fa-print text-amber-400"></i>
            <span>Cetak Borang A (2 Muka Surat)</span>
        </button>
        <button onclick="window.close()" class="px-4 py-2.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-sans font-bold text-sm rounded-xl shadow-sm transition">
            Tutup
        </button>
    </div>

    <!-- ================= PAGE 1 (Image 1) ================= -->
    <div class="sheet page-1 max-w-[210mm] w-full bg-white p-8 sm:p-12 shadow-2xl mb-8 print:mb-0 print:p-0 print:shadow-none">
        <!-- Top Running Header -->
        <div class="flex justify-between items-start font-bold uppercase text-[12px] mb-3">
            <span>ENAKMEN PERLADANGAN UNGGAS 2005</span>
        </div>

        <!-- Title Block -->
        <div class="text-center space-y-1 mb-5">
            <p class="font-bold text-[13px] tracking-wider">JADUAL PERTAMA</p>
            <p class="font-bold text-[13px]">ENAKMEN PERLADANGAN UNGGAS 2005</p>
            <p class="text-[12px] italic">(Subseksyen 6(1) dan 6(2))</p>
            <p class="font-bold text-[14px] mt-2 uppercase">
                PERMOHONAN LESEN PERLADANGAN UNGGAS DAN<br>AKTIVITI-AKTIVITI BERKAITAN
            </p>
            <p class="font-bold text-[13px] mt-1">(Borang A)</p>
        </div>

        <!-- Section 1: Pemohon -->
        <div class="space-y-2.5 text-[13px]">
            <div class="flex items-baseline">
                <span class="font-bold mr-1">1.</span>
                <span class="font-bold mr-2">Saya:</span>
                <span class="dotted-line flex-1 font-bold px-2 uppercase">{{ $ladang->pemilik->name ?? $ladang->nama_pemohon_atau_syarikat }}</span>
            </div>
            <div class="text-center text-[11px] italic text-slate-600 -mt-1 mb-2">
                (Nama penuh ditulis dengan huruf besar)
            </div>

            <div class="flex items-baseline">
                <span class="font-bold mr-2">Nama Syarikat:</span>
                <span class="dotted-line flex-1 font-semibold px-2">{{ $ladang->pemilik->nama_syarikat ?? ($ladang->no_syarikat_atau_ssm ? $ladang->nama_pemohon_atau_syarikat : '') }}</span>
            </div>

            <div class="grid grid-cols-2 gap-4 items-baseline">
                <div class="flex items-baseline">
                    <span class="font-bold mr-2">No. K.P.:</span>
                    <span class="dotted-line flex-1 font-mono font-bold px-2">{{ $ladang->pemilik->ic_number ?? '-' }}</span>
                </div>
                <div class="flex items-baseline">
                    <span class="font-bold mr-2">No. Pendaftaran Syarikat:</span>
                    <span class="dotted-line flex-1 font-mono px-2">{{ $ladang->no_syarikat_atau_ssm ?? ($ladang->pemilik->no_ssm ?? '-') }}</span>
                </div>
            </div>

            <div>
                <div class="flex items-baseline">
                    <span class="font-bold mr-2 whitespace-nowrap">Alamat Surat Menyurat:</span>
                    <span class="dotted-line flex-1 px-2">{{ $ladang->pemilik->address ?? $ladang->alamat_ladang }}</span>
                </div>
                <div class="dotted-line w-full mt-1.5 min-h-[1.3em] px-2">
                    {{ $ladang->pemilik->poskod ?? $ladang->poskod ?? '' }} {{ $ladang->pemilik->negeri ?? $ladang->negeri ?? '' }}
                </div>
                <div class="dotted-line w-full mt-1.5 min-h-[1.3em]"></div>
            </div>

            <div class="grid grid-cols-2 gap-4 items-baseline">
                <div class="flex items-baseline">
                    <span class="font-bold mr-2">No. Tel.:</span>
                    <span class="dotted-line flex-1 font-mono px-2">{{ $ladang->pemilik->phone ?? '-' }}</span>
                </div>
                <div class="flex items-baseline">
                    <span class="font-bold mr-2">No. Fax.:</span>
                    <span class="dotted-line flex-1 font-mono px-2">{{ $ladang->pemilik->fax ?? '-' }}</span>
                </div>
            </div>

            <div class="flex items-baseline">
                <span class="font-bold mr-2">E-mail:</span>
                <span class="dotted-line flex-1 px-2">{{ $ladang->pemilik->email ?? '-' }}</span>
            </div>

            <div class="pt-1">
                <span class="font-bold block mb-1.5">Bentuk Perniagaan:</span>
                <div class="flex items-center gap-6 pl-4">
                    <div class="flex items-center">
                        <span>Milik Tunggal</span>
                        <span class="checkbox-box">{{ ($ladang->pemilik->bentuk_perniagaan ?? '') === 'Milikan Tunggal' ? '✓' : '' }}</span>
                    </div>
                    <div class="flex items-center">
                        <span>Perkongsian</span>
                        <span class="checkbox-box">{{ ($ladang->pemilik->bentuk_perniagaan ?? '') === 'Perkongsian' ? '✓' : '' }}</span>
                    </div>
                    <div class="flex items-center">
                        <span>Syarikat</span>
                        <span class="checkbox-box">{{ in_array(($ladang->pemilik->bentuk_perniagaan ?? ''), ['Sendirian Berhad', 'Awam Berhad', 'Syarikat', 'Koperasi', 'Perkongsian Liabiliti Terhad']) ? '✓' : '' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Butir-butir Penternakan -->
        <div class="space-y-2 mt-4 pt-2 text-[13px]">
            <p class="font-bold">2. Butir-butir Penternakan:</p>

            <div class="flex items-baseline pl-4">
                <span class="mr-2">Jenis Unggas:</span>
                <span class="dotted-line flex-1 font-bold px-2 uppercase">{{ $permohonan->jenis_unggas }}</span>
            </div>

            <div class="pl-4 pt-1">
                <span class="block mb-1.5">Jurusan dan aktiviti:</span>
                <div class="grid grid-cols-4 gap-2 text-[12px]">
                    <div class="flex items-center">
                        <span>Pedaging</span>
                        <span class="checkbox-box">{{ $permohonan->jurusan_aktiviti === 'Pedaging' ? '✓' : '' }}</span>
                    </div>
                    <div class="flex items-center">
                        <span>Penelur</span>
                        <span class="checkbox-box">{{ $permohonan->jurusan_aktiviti === 'Penelur' ? '✓' : '' }}</span>
                    </div>
                    <div class="flex items-center">
                        <span>Baka</span>
                        <span class="checkbox-box">{{ $permohonan->jurusan_aktiviti === 'Baka' ? '✓' : '' }}</span>
                    </div>
                    <div class="flex items-center">
                        <span>Haceri (Penetasan)</span>
                        <span class="checkbox-box">{{ $permohonan->jurusan_aktiviti === 'Haceri (Penetasan)' ? '✓' : '' }}</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2 mt-2 text-[12px]">
                    <div class="flex items-center">
                        <span>Loji Penyembelihan</span>
                        <span class="checkbox-box"></span>
                    </div>
                    <div class="flex items-center">
                        <span>Loji Pemprosesan</span>
                        <span class="checkbox-box"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Butir-butir Ladang/Loji -->
        <div class="space-y-2 mt-4 pt-2 text-[13px]">
            <p class="font-bold">3. Butir–butir Ladang/Loji:</p>

            <div class="flex items-baseline pl-4">
                <span class="mr-2">Lokasi:</span>
                <span class="dotted-line flex-1 px-2 font-semibold">{{ $ladang->alamat_ladang }}, {{ $ladang->jajahan }}</span>
            </div>

            <div class="flex items-baseline pl-4">
                <span class="mr-2">Daerah:</span>
                <span class="dotted-line flex-1 px-2">{{ $ladang->daerah ?: $ladang->mukim ?: $ladang->jajahan }}</span>
            </div>

            <div class="grid grid-cols-2 gap-4 items-baseline pl-4">
                <div class="flex items-baseline">
                    <span class="mr-2">Luas Kawasan:</span>
                    <span class="dotted-line flex-1 px-2 font-mono">{{ $ladang->luas_tanah_ekar ? number_format($ladang->luas_tanah_ekar * 0.404686, 2) : ($ladang->luas_kawasan_sqft ? number_format($ladang->luas_kawasan_sqft / 107639, 2) : '0.50') }} (Ha.)</span>
                </div>
                <div class="flex items-baseline">
                    <span class="mr-2">Kapasiti Ladang:</span>
                    <span class="dotted-line flex-1 font-bold font-mono px-2">{{ number_format($permohonan->kapasiti_ladang ?: $ladang->kapasiti_maksimum_unggas) }} ekor</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 items-baseline pl-4">
                <div class="flex items-baseline">
                    <span class="mr-2">No. Lot Tanah:</span>
                    <span class="dotted-line flex-1 font-mono px-2">{{ $ladang->no_lot ?? '-' }}</span>
                </div>
                <div class="flex items-baseline">
                    <span class="mr-2">Status Tanah:</span>
                    <span class="dotted-line flex-1 px-2">{{ $ladang->status_pemilikan_tanah ?? 'Milik Sendiri' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= PAGE 2 (Image 2) ================= -->
    <div class="sheet page-2 max-w-[210mm] w-full bg-white p-8 sm:p-12 shadow-2xl print:p-0 print:shadow-none">
        
        <!-- Section 4: Aktiviti Berkaitan -->
        <div class="space-y-2 text-[12.5px]">
            <p class="font-bold">4. Aktiviti berkaitan:</p>

            <div class="flex items-baseline pl-4">
                <span class="mr-2">Jenis pengeluaran:</span>
                <span class="dotted-line flex-1 px-2 font-semibold">{{ $permohonan->jenis_unggas }} ({{ $permohonan->jurusan_aktiviti }})</span>
            </div>

            <div class="flex items-baseline pl-4">
                <span class="mr-2">Keupayaan pengeluaran:</span>
                <span class="dotted-line flex-1 px-2 font-mono font-bold">{{ number_format($permohonan->kapasiti_ladang ?: $ladang->kapasiti_maksimum_unggas) }} ekor / pusingan</span>
            </div>

            <div class="pl-4">
                <div class="flex items-baseline">
                    <span class="mr-2 whitespace-nowrap">Alamat premis perniagaan:</span>
                    <span class="dotted-line flex-1 px-2">{{ $ladang->alamat_premis_perniagaan ?: $ladang->alamat_ladang }}</span>
                </div>
                <div class="dotted-line w-full mt-1 min-h-[1.2em]"></div>
            </div>

            <!-- Tarikh & Tandatangan Pemohon -->
            <div class="grid grid-cols-2 gap-8 pt-4 mt-2 items-end">
                <div class="flex items-baseline">
                    <span class="font-bold mr-2">Tarikh:</span>
                    <span class="dotted-line flex-1 font-mono px-2 font-bold">{{ $permohonan->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="text-center">
                    <div class="dotted-line w-full mb-1"></div>
                    <span class="italic text-[11.5px] font-bold block">Tandatangan Pemohon dan Cop Syarikat</span>
                </div>
            </div>
        </div>

        <!-- UNTUK KEGUNAAN RASMI PEJABAT -->
        <div class="mt-4 pt-3 border-t-2 border-black">
            <p class="text-center font-bold text-[12.5px] uppercase tracking-wider mb-2.5">
                UNTUK KEGUNAAN RASMI PEJABAT
            </p>

            <div class="space-y-2 text-[12px]">
                <div class="flex items-baseline">
                    <span class="mr-2 whitespace-nowrap">Tarikh permohonan diterima:</span>
                    <span class="dotted-line flex-1 font-mono font-bold px-2">{{ $permohonan->created_at->format('d/m/Y') }}</span>
                </div>

                <div class="flex items-center gap-6 pt-0.5">
                    <span class="mr-2">Keputusan permohonan:</span>
                    <div class="flex items-center">
                        <span>Dilulus</span>
                        <span class="checkbox-box">{{ $permohonan->status === 'Diluluskan' ? '✓' : '' }}</span>
                    </div>
                    <div class="flex items-center">
                        <span>Ditolak</span>
                        <span class="checkbox-box">{{ $permohonan->status === 'Ditolak' ? '✓' : '' }}</span>
                    </div>
                </div>

                <div>
                    <div class="flex items-baseline">
                        <span class="mr-2">Catatan:</span>
                        <span class="dotted-line flex-1 px-2">{{ $permohonan->catatan_pegawai ?? $permohonan->syarat_khas_lesen ?? '-' }}</span>
                    </div>
                    <div class="dotted-line w-full mt-1 min-h-[1.2em]"></div>
                </div>

                @php
                    // Dapatkan Pegawai Pelesen / Pengarah dengan tandatangan digital berdaftar
                    // HANYA dipaparkan sekiranya permohonan telah diluluskan sepenuhnya
                    $pegawaiPelesen = null;
                    $sigSrc = null;

                    if ($permohonan->status === 'Diluluskan') {
                        if ($permohonan->pelulus && !empty($permohonan->pelulus->signature)) {
                            $pegawaiPelesen = $permohonan->pelulus;
                        }
                        if (!$pegawaiPelesen) {
                            $pegawaiPelesen = \App\Models\User::where(function($q) {
                                    $q->where('role', 'pegawai_pelesen')->orWhereJsonContains('roles', 'pegawai_pelesen');
                                })
                                ->whereNotNull('signature')
                                ->where('signature', '!=', '')
                                ->first();
                        }
                        if (!$pegawaiPelesen) {
                            $pegawaiPelesen = \App\Models\User::where(function($q) {
                                    $q->where('role', 'pengarah')->orWhereJsonContains('roles', 'pengarah');
                                })
                                ->whereNotNull('signature')
                                ->where('signature', '!=', '')
                                ->first();
                        }
                        if (!$pegawaiPelesen) {
                            $pegawaiPelesen = \App\Models\User::whereNotNull('signature')->where('signature', '!=', '')->first()
                                ?? $permohonan->pelulus
                                ?? \App\Models\User::where('role', 'pegawai_pelesen')->orWhereJsonContains('roles', 'pegawai_pelesen')->first() 
                                ?? \App\Models\User::where('role', 'pengarah')->orWhereJsonContains('roles', 'pengarah')->first();
                        }

                        if ($pegawaiPelesen && !empty($pegawaiPelesen->signature)) {
                            $diskPath = storage_path('app/public/' . $pegawaiPelesen->signature);
                            if (file_exists($diskPath)) {
                                $ext = pathinfo($diskPath, PATHINFO_EXTENSION) ?: 'png';
                                $sigSrc = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($diskPath));
                            } else {
                                $sigSrc = asset('storage/' . $pegawaiPelesen->signature);
                            }
                        }
                    }
                @endphp

                <div class="grid grid-cols-2 gap-8 pt-3 items-end">
                    <div class="flex items-baseline">
                        <span class="mr-2">Tarikh:</span>
                        <span class="dotted-line flex-1 font-mono px-2">{{ $permohonan->status === 'Diluluskan' && $permohonan->tarikh_kelulusan ? $permohonan->tarikh_kelulusan->format('d/m/Y') : '' }}</span>
                    </div>
                    <div class="text-center">
                        <div class="min-h-[48px] flex flex-col items-center justify-end">
                            @if($sigSrc && $permohonan->status === 'Diluluskan')
                                <img src="{{ $sigSrc }}" alt="Tandatangan Pegawai Pelesen" class="h-12 max-w-[150px] object-contain -mb-1">
                            @endif
                        </div>
                        <div class="dotted-line w-full mb-0.5"></div>
                        <span class="italic text-[11.5px] font-bold block">Pegawai Pelesen</span>
                        @if($pegawaiPelesen && $permohonan->status === 'Diluluskan')
                            <span class="text-[11px] font-semibold text-slate-800 block">({{ $pegawaiPelesen->name }})</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Syarat Lampiran (a) - (f) -->
        <div class="mt-3 pt-2.5 border-t border-slate-300 text-[10.5px] leading-snug">
            <p class="font-bold mb-1">Permohonan hendaklah dikemukakan berserta dengan butiran berikut:</p>
            <ol class="list-none space-y-1 pl-1">
                <li class="flex items-start">
                    <span class="italic mr-1.5 font-bold">(a)</span>
                    <div>
                        <span>satu cadangan pelan susun atur menunjukkan—</span>
                        <ul class="list-none space-y-0.5 pl-3 mt-0.5 text-[10px]">
                            <li class="flex items-start">
                                <span class="italic mr-1.5">(i)</span>
                                <span>sempadan ladang dan sempadan tanah di mana ladang akan didirikan;</span>
                            </li>
                            <li class="flex items-start">
                                <span class="italic mr-1.5">(ii)</span>
                                <span>keluasan/dimensi dan rekabentuk bangunan ladang dan struktur ladang;</span>
                            </li>
                            <li class="flex items-start">
                                <span class="italic mr-1.5">(iii)</span>
                                <span>lokasi tempat yang menunjukkan jarak dengan garisan lurus mana-mana bangunan atau struktur rumah kediaman, klinik, sekolah atau tempat ibadat yang sedia ada semasa permohonan dibuat yang berada dalam lingkungan minimum dua ratus meter dari bangunan atau infrastruktur yang menjalankan aktiviti perladangan unggas di ladang yang dicadangkan.</span>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="flex items-start">
                    <span class="italic mr-1.5 font-bold">(b)</span>
                    <span>satu salinan hakmilik yang disahkan di mana ladang unggas dicadang akan didirikan.</span>
                </li>
                <li class="flex items-start">
                    <span class="italic mr-1.5 font-bold">(c)</span>
                    <span>jika tanah tersebut tidak dimiliki oleh pemohon, satu keizinan bertulis daripada tuan tanah berdaftar.</span>
                </li>
                <li class="flex items-start">
                    <span class="italic mr-1.5 font-bold">(d)</span>
                    <span>satu salinan sah pendaftaran perniagaan, jika ada.</span>
                </li>
                <li class="flex items-start">
                    <span class="italic mr-1.5 font-bold">(e)</span>
                    <span>satu salinan fotokopi Kad Pengenalan pemohon.</span>
                </li>
                <li class="flex items-start">
                    <span class="italic mr-1.5 font-bold">(f)</span>
                    <span>dua keping gambar pemohon ukuran pasport.</span>
                </li>
            </ol>
        </div>

    </div>

</body>
</html>


