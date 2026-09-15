<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>CETAK PUKAL BORANG C ({{ count($pembatalanList) }} Rekod Pembatalan)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @page {
            size: A4 portrait !important;
            margin: 15mm 20mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #f1f5f9;
        }
        .form-container {
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.4;
            font-size: 11pt;
        }
        table, th, td {
            border: 1px solid #000;
        }
        .page-break {
            page-break-after: always !important;
            break-after: page !important;
        }
        @media print {
            @page {
                size: A4 portrait !important;
            }
            .no-print { display: none !important; }
            html, body {
                width: 210mm !important;
                background: white !important;
                margin: 0 auto !important;
                padding: 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .form-container {
                width: 100% !important;
                max-width: 210mm !important;
                min-height: 270mm !important;
                box-shadow: none !important;
                border: none !important;
                padding: 10mm 15mm !important;
                margin: 0 auto !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }
    </style>
</head>
<body class="p-4 sm:p-8 flex flex-col items-center">

    <!-- Top Action Bar (Screen Only) -->
    <div class="no-print mb-6 flex flex-wrap items-center gap-3 bg-white p-4 rounded-2xl shadow-md border border-slate-200">
        <div class="text-xs font-bold text-slate-800 mr-2">
            <i class="fa-solid fa-layer-group text-rose-600 mr-1"></i> Cetakan Pukal: <b>{{ count($pembatalanList) }}</b> Dokumen Borang C
        </div>
        <button onclick="window.print()" class="px-6 py-2.5 bg-rose-700 hover:bg-rose-800 text-white font-bold text-xs rounded-xl shadow-lg transition flex items-center gap-2">
            <i class="fa-solid fa-print"></i>
            <span>Cetak Semua ({{ count($pembatalanList) }} Dokumen)</span>
        </button>
        <a href="{{ route('eptr.borang-c.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
            Kembali ke Senarai Borang C
        </a>
    </div>

    <!-- Iterate Over Each Selected Cancellation with Full-Page Form -->
    @foreach($pembatalanList as $index => $pembatalan)
        <div class="form-container max-w-[210mm] w-full bg-white p-10 sm:p-12 shadow-2xl text-black mb-8 {{ !$loop->last ? 'page-break' : '' }}">
            
            <!-- Top Reference -->
            <div class="mb-4 text-[11pt]">
                <span>NO. RUJUKAN: </span>
                <span class="font-mono font-bold">{{ $pembatalan->no_laporan_polis ?? 'BATAL/' . date('Ymd') . '/' . $pembatalan->id }}</span>
            </div>

            <!-- Header -->
            <div class="text-center mb-6">
                <div class="font-bold text-base tracking-wide uppercase">ENAKMEN PENDAFTARAN TERNAKAN RUMINAN 2024</div>
                <div class="font-bold text-sm uppercase mt-0.5">JADUAL KETIGA</div>
                <div class="font-bold text-sm uppercase mt-0.5">BORANG C</div>
                <div class="text-sm mt-0.5">[subseksyen 11 (2)]</div>
                <div class="font-bold text-sm uppercase tracking-wide mt-1">PEMBATALAN PENDAFTARAN TERNAKAN RUMINAN</div>
            </div>

            <div class="mb-4 text-justify">
                <span>Ternakan ruminan yang butirannya seperti di bawah adalah </span>
                <span class="font-bold uppercase">DIBATALKAN </span>
                <span>pendaftarannya :</span>
            </div>

            <!-- Section 1: Maklumat Pemunya -->
            <div class="mb-4">
                <div class="font-bold uppercase tracking-wide mb-1">MAKLUMAT PEMUNYA</div>
                <div class="space-y-1 pl-2">
                    <div class="grid grid-cols-12">
                        <div class="col-span-4">Nama</div>
                        <div class="col-span-8">: <span class="font-bold uppercase">{{ $pembatalan->ternakan->pemunya->nama ?? '-' }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-4">No. Kad Pengenenalan</div>
                        <div class="col-span-8">: <span class="font-mono font-bold">{{ $pembatalan->ternakan->pemunya->no_kp ?? '-' }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-4">Alamat</div>
                        <div class="col-span-8">: <span class="uppercase">{{ $pembatalan->ternakan->pemunya->alamat ?? '-' }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-4">No Telefon</div>
                        <div class="col-span-8">: <span>{{ $pembatalan->ternakan->pemunya->no_telefon ?? '-' }}</span></div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Maklumat Ternakan -->
            <div class="mb-4">
                <div class="font-bold uppercase tracking-wide mb-1">MAKLUMAT TERNAKAN</div>
                <div class="space-y-1 pl-2">
                    <div class="grid grid-cols-12">
                        <div class="col-span-4">Spesies</div>
                        <div class="col-span-8">: <span class="font-bold uppercase">{{ $pembatalan->ternakan->jenis_ternakan ?? 'LEMBU' }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-4">Baka</div>
                        <div class="col-span-8">: <span class="uppercase">{{ $pembatalan->ternakan->baka ?? '-' }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-4">Jantina</div>
                        <div class="col-span-8">: <span class="uppercase">{{ $pembatalan->ternakan->jantina ?? '-' }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-4">No. Perakuan Pendaftran</div>
                        <div class="col-span-8">: <span class="font-mono font-bold">{{ $pembatalan->ternakan->no_siri_kad_kuning ?? 'DB' . date('mY') . rand(10, 99) }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-4">No. Tanda Pengenalan</div>
                        <div class="col-span-8">: <span class="font-mono font-bold text-base">{{ $pembatalan->ternakan->no_tag ?? '-' }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-4">Sebab Pembatalan</div>
                        <div class="col-span-8">: <span class="font-bold uppercase">{{ $pembatalan->jenis_batal }} ({{ $pembatalan->sebab }})</span></div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Sebab Pembatalan Table (Exact 3-Row Official Box) -->
            <table class="w-full border-collapse text-[10pt] mb-6">
                <tbody>
                    <!-- Row 1: Kematian -->
                    <tr class="h-12">
                        <td class="w-8 text-center font-bold text-sm">
                            {{ $pembatalan->jenis_batal === 'Mati' ? '[ / ]' : '[   ]' }}
                        </td>
                        <td class="p-2.5">
                            <div class="flex justify-between items-center">
                                <span class="font-bold">Kematian</span>
                                <span>Dokumen Sokongan : <b>{{ $pembatalan->dokumen_sokongan ? 'Ada' : 'Tiada' }}</b></span>
                                <span>Tarikh : <b>{{ $pembatalan->jenis_batal === 'Mati' && $pembatalan->tarikh_peristiwa ? $pembatalan->tarikh_peristiwa->format('d/m/Y') : '........................' }}</b></span>
                            </div>
                        </td>
                    </tr>
                    <!-- Row 2: Pindah Keluar -->
                    <tr class="h-12">
                        <td class="w-8 text-center font-bold text-sm">
                            {{ $pembatalan->jenis_batal === 'Pindah Keluar' ? '[ / ]' : '[   ]' }}
                        </td>
                        <td class="p-2.5">
                            <div class="flex justify-between items-center">
                                <span class="font-bold">Pindah Keluar</span>
                                <span>Tarikh : <b>{{ $pembatalan->jenis_batal === 'Pindah Keluar' && $pembatalan->tarikh_peristiwa ? $pembatalan->tarikh_peristiwa->format('d/m/Y') : '........................' }}</b></span>
                                <span>Destinasi : <b>{{ $pembatalan->destinasi_pindah_keluar ?? '....................................' }}</b></span>
                            </div>
                        </td>
                    </tr>
                    <!-- Row 3: Lain-Lain / Kecurian / Pelupusan -->
                    <tr class="h-12">
                        <td class="w-8 text-center font-bold text-sm">
                            {{ in_array($pembatalan->jenis_batal, ['Kecurian', 'Pelupusan', 'Lain-lain']) ? '[ / ]' : '[   ]' }}
                        </td>
                        <td class="p-2.5">
                            <div class="flex justify-between items-center">
                                <span class="font-bold">Lain-Lain ({{ $pembatalan->jenis_batal }})</span>
                                <span>Dokumen Sokongan : <b>{{ $pembatalan->no_laporan_polis ? 'Ada (Polis)' : 'Tiada' }}</b></span>
                                <span>Tarikh : <b>{{ in_array($pembatalan->jenis_batal, ['Kecurian', 'Pelupusan', 'Lain-lain']) && $pembatalan->tarikh_peristiwa ? $pembatalan->tarikh_peristiwa->format('d/m/Y') : '........................' }}</b></span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Date & Declaration -->
            <div class="space-y-2 mb-8">
                <div>Tarikh : <b>{{ $pembatalan->tarikh_peristiwa ? $pembatalan->tarikh_peristiwa->format('d/m/Y') : date('d/m/Y') }}</b></div>
                <div class="italic text-[10.5pt]">* Saya dengan ini mengaku bahawa butiran dan maklumat di atas adalah benar.</div>
            </div>

            <!-- Signatures (Exact Dotted Layout) -->
            <div class="grid grid-cols-2 gap-8 pt-4">
                <div class="text-left">
                    <div>…………………………................</div>
                    <div class="mt-1 font-semibold text-[10.5pt]">Tandatangan Pemunya Ternakan</div>
                    <div class="text-[10.5pt]">Ruminan</div>
                </div>
                <div class="text-left">
                    <div>....................................................................</div>
                    <div class="mt-1 font-semibold text-[10.5pt]">Tandatangan dan cop Penolong Pendaftar</div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-[9pt] text-slate-500 flex justify-between items-center pt-4 border-t border-slate-200 mt-6">
                <div>Dokumen {{ $index + 1 }} daripada {{ count($pembatalanList) }} &bull; Tarikh Cetakan: {{ date('d/m/Y H:i:s') }}</div>
                <div class="font-mono">JPVNK-EPTR-PUKAL-BORANG-C-{{ $pembatalan->id }}</div>
            </div>

        </div>
    @endforeach

</body>
</html>
