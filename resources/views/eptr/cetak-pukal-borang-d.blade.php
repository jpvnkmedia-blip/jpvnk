<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>CETAK PUKAL BORANG D ({{ count($permitList) }} Permit Sembelihan)</title>
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
            <i class="fa-solid fa-layer-group text-amber-600 mr-1"></i> Cetakan Pukal: <b>{{ count($permitList) }}</b> Dokumen Permit Borang D
        </div>
        <button onclick="window.print()" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl shadow-lg transition flex items-center gap-2">
            <i class="fa-solid fa-print"></i>
            <span>Cetak Semua ({{ count($permitList) }} Dokumen)</span>
        </button>
        <a href="{{ route('eptr.borang-d.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
            Kembali ke Senarai Borang D
        </a>
    </div>

    <!-- Iterate Over Each Selected Permit with Full-Page Form -->
    @foreach($permitList as $index => $permit)
        @php
            $pemunya = $permit->pemunya;
            $firstItem = !empty($permit->senarai_ternakan_list) ? $permit->senarai_ternakan_list->first() : null;
            $ternakanObj = $permit->ternakan;
            
            $spesies = strtoupper($permit->jenis_ternakan ?? ($ternakanObj ? $ternakanObj->jenis_ternakan : 'LEMBU'));
            $baka = strtoupper($ternakanObj ? ($ternakanObj->baka ?? 'KACUKAN') : ($firstItem['baka'] ?? 'KACUKAN'));
            $jantinaRaw = $ternakanObj ? $ternakanObj->jantina : ($firstItem['jantina'] ?? 'Jantan');
            $jantina = in_array(strtoupper($jantinaRaw), ['J', 'JANTAN']) ? 'JANTAN' : 'BETINA';
            $noPerakuan = $ternakanObj ? ($ternakanObj->no_siri_kad_kuning ?? 'DB' . date('mY') . rand(10, 99)) : ($firstItem['no_siri_kad_pendaftaran'] ?? ('DB' . date('mY') . rand(10, 99)));
            $noTag = $ternakanObj ? ($ternakanObj->no_tag ?? '-') : ($firstItem['no_id_ternakan'] ?? '-');
            $tarikhSembelihFormatted = $permit->tarikh_sembelih ? $permit->tarikh_sembelih->format('d/m/Y') : date('d/m/Y');
        @endphp

        <div class="form-container max-w-[210mm] w-full bg-white p-10 sm:p-14 shadow-2xl text-black mb-8 {{ !$loop->last ? 'page-break' : '' }}">
            
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
                        <div class="col-span-8">: <span>{{ $tarikhSembelihFormatted }}</span></div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Penamatan & Signatures -->
            <div class="mb-6">
                <div class="font-bold uppercase tracking-wide mb-1.5">PENDAFTARAN TELAH DITAMATKAN</div>
                <div class="mb-8">
                    <div>Tarikh : <span>{{ $tarikhSembelihFormatted }}</span></div>
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

            <!-- Footer -->
            <div class="text-[9pt] text-slate-500 flex justify-between items-center pt-4 border-t border-slate-200 mt-6">
                <div>Dokumen {{ $index + 1 }} daripada {{ count($permitList) }} &bull; Tarikh Cetakan: {{ date('d/m/Y H:i:s') }}</div>
                <div class="font-mono">JPVNK-EPTR-PUKAL-BORANG-D-{{ $permit->no_permit }}</div>
            </div>

        </div>
    @endforeach

</body>
</html>
