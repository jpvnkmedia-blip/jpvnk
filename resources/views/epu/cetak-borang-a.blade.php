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
            margin: 8mm 12mm 8mm 12mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background-color: #fff;
            line-height: 1.25;
        }
        .dotted-line {
            border-bottom: 1px dotted #333;
            display: inline-block;
            min-height: 1.15em;
        }
        .checkbox-box {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 15px;
            border: 1px solid #000;
            margin: 0 4px;
            font-size: 10px;
            font-weight: bold;
            vertical-align: middle;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            html, body {
                padding: 0 !important;
                margin: 0 !important;
                background: #fff !important;
                font-size: 11px !important;
                line-height: 1.25 !important;
            }
            .page-container {
                width: 100% !important;
                box-sizing: border-box !important;
            }
            .page-1 {
                page-break-after: always !important;
                break-after: page !important;
            }
            .page-2 {
                page-break-before: always !important;
                break-before: page !important;
                page-break-after: avoid !important;
                break-after: avoid !important;
            }
        }
    </style>
</head>
<body class="p-4 sm:p-6 max-w-4xl mx-auto text-[12px]">

    <!-- Print Floating Buttons -->
    <div class="no-print fixed top-5 right-5 flex gap-2 z-50 bg-white/95 p-3 rounded-2xl shadow-xl border border-slate-200">
        <button onclick="window.print()" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-sans font-bold text-xs rounded-xl shadow transition flex items-center gap-1.5">
            <i class="fa-solid fa-print"></i> Cetak Borang A
        </button>
        <button onclick="window.close()" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-sans font-bold text-xs rounded-xl transition">
            Tutup
        </button>
    </div>

    <!-- ================= PAGE 1 (Image 1) ================= -->
    <div class="page-container page-1 relative">
        <!-- Top Running Header -->
        <div class="flex justify-between items-start font-bold uppercase text-[11px] mb-2">
            <span>ENAKMEN PERLADANGAN UNGGAS 2005</span>
        </div>

        <!-- Title Block -->
        <div class="text-center space-y-0.5 mb-3">
            <p class="font-bold text-[12px] tracking-wider">JADUAL PERTAMA</p>
            <p class="font-bold text-[12px]">ENAKMEN PERLADANGAN UNGGAS 2005</p>
            <p class="text-[11px] italic">(Subseksyen 6(1) dan 6(2))</p>
            <p class="font-bold text-[13px] mt-1.5 uppercase">
                PERMOHONAN LESEN PERLADANGAN UNGGAS DAN<br>AKTIVITI-AKTIVITI BERKAITAN
            </p>
            <p class="font-bold text-[12px] mt-0.5">(Borang A)</p>
        </div>

        <!-- Section 1: Pemohon -->
        <div class="space-y-1.5">
            <div class="flex items-baseline">
                <span class="font-bold mr-1">1.</span>
                <span class="font-bold mr-2">Saya:</span>
                <span class="dotted-line flex-1 font-bold px-2 uppercase">{{ $ladang->pemilik->name ?? $ladang->nama_pemohon_atau_syarikat }}</span>
            </div>
            <div class="text-center text-[10px] italic text-slate-600 -mt-1 mb-1">
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
                <div class="dotted-line w-full mt-1 min-h-[1.2em] px-2">
                    {{ $ladang->pemilik->poskod ?? $ladang->poskod ?? '' }} {{ $ladang->pemilik->negeri ?? $ladang->negeri ?? '' }}
                </div>
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

            <div class="pt-0.5">
                <span class="font-bold block mb-1">Bentuk Perniagaan:</span>
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
        <div class="space-y-1 mt-2.5 pt-1">
            <p class="font-bold">2. Butir-butir Penternakan:</p>

            <div class="flex items-baseline pl-4">
                <span class="mr-2">Jenis Unggas:</span>
                <span class="dotted-line flex-1 font-bold px-2 uppercase">{{ $permohonan->jenis_unggas }}</span>
            </div>

            <div class="pl-4 pt-0.5">
                <span class="block mb-1">Jurusan dan aktiviti:</span>
                <div class="grid grid-cols-4 gap-2 text-[11px]">
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
                <div class="grid grid-cols-2 gap-2 mt-1.5 text-[11px]">
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
        <div class="space-y-1 mt-2.5 pt-1">
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
    <div class="page-container page-2 pt-1">
        
        <!-- Section 4: Aktiviti Berkaitan -->
        <div class="space-y-1.5">
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
            </div>

            <!-- Tarikh & Tandatangan Pemohon -->
            <div class="grid grid-cols-2 gap-8 pt-3 mt-1.5 items-end">
                <div class="flex items-baseline">
                    <span class="font-bold mr-2">Tarikh:</span>
                    <span class="dotted-line flex-1 font-mono px-2 font-bold">{{ $permohonan->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="text-center">
                    <div class="dotted-line w-full mb-1"></div>
                    <span class="italic text-[11px] font-bold block">Tandatangan Pemohon dan Cop Syarikat</span>
                </div>
            </div>
        </div>

        <!-- UNTUK KEGUNAAN RASMI PEJABAT -->
        <div class="mt-2.5 pt-2 border-t-2 border-black">
            <p class="text-center font-bold text-[12px] uppercase tracking-wider mb-2">
                UNTUK KEGUNAAN RASMI PEJABAT
            </p>

            <div class="space-y-1.5 text-[11.5px]">
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
                </div>

                @php
                    // Dapatkan Pegawai Pelesen / Pengarah dengan tandatangan digital berdaftar
                    $pegawaiPelesen = null;
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

                    $sigSrc = null;
                    if ($pegawaiPelesen && !empty($pegawaiPelesen->signature)) {
                        $diskPath = storage_path('app/public/' . $pegawaiPelesen->signature);
                        if (file_exists($diskPath)) {
                            $ext = pathinfo($diskPath, PATHINFO_EXTENSION) ?: 'png';
                            $sigSrc = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($diskPath));
                        } else {
                            $sigSrc = asset('storage/' . $pegawaiPelesen->signature);
                        }
                    }
                @endphp

                <div class="grid grid-cols-2 gap-8 pt-2 items-end">
                    <div class="flex items-baseline">
                        <span class="mr-2">Tarikh:</span>
                        <span class="dotted-line flex-1 font-mono px-2">{{ $permohonan->tarikh_kelulusan ? $permohonan->tarikh_kelulusan->format('d/m/Y') : '' }}</span>
                    </div>
                    <div class="text-center">
                        @if($sigSrc)
                            <div class="flex flex-col items-center justify-center -mb-2">
                                <img src="{{ $sigSrc }}" alt="Tandatangan Pegawai Pelesen" class="h-11 max-w-[150px] object-contain">
                            </div>
                        @endif
                        <div class="dotted-line w-full mb-0.5"></div>
                        <span class="italic text-[11px] font-bold block">Pegawai Pelesen</span>
                        @if($pegawaiPelesen)
                            <span class="text-[10.5px] font-semibold text-slate-800 block">({{ $pegawaiPelesen->name }})</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Syarat Lampiran (a) - (f) -->
        <div class="mt-2.5 pt-2 border-t border-slate-300 text-[10px] leading-tight">
            <p class="font-bold mb-1">Permohonan hendaklah dikemukakan berserta dengan butiran berikut:</p>
            <ol class="list-none space-y-1 pl-1">
                <li class="flex items-start">
                    <span class="italic mr-1.5 font-bold">(a)</span>
                    <div>
                        <span>satu cadangan pelan susun atur menunjukkan—</span>
                        <ul class="list-none space-y-0.5 pl-3 mt-0.5 text-[9.5px]">
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

