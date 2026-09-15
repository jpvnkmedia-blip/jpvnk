<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>CETAK PUKAL BORANG A ({{ count($ternakanList) }} Ekor Ternakan)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @page {
            size: A4 portrait !important;
            margin: 12mm 15mm;
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
            <i class="fa-solid fa-layer-group text-emerald-600 mr-1"></i> Cetakan Pukal: <b>{{ count($ternakanList) }}</b> Dokumen Borang A
        </div>
        <button onclick="window.print()" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-lg transition flex items-center gap-2">
            <i class="fa-solid fa-print text-amber-400"></i>
            <span>Cetak Semua ({{ count($ternakanList) }} Dokumen)</span>
        </button>
        <a href="{{ route('eptr.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
            Kembali ke Senarai EPTR
        </a>
    </div>

    <!-- Iterate Over Each Selected Animal with Full-Page Form -->
    @foreach($ternakanList as $index => $ternakan)
        <div class="form-container max-w-[210mm] w-full bg-white p-10 sm:p-12 shadow-2xl text-black mb-8 {{ !$loop->last ? 'page-break' : '' }}">
            
            <!-- Header -->
            <div class="text-center mb-6">
                <div class="font-bold text-base tracking-wide uppercase">ENAKMEN PENDAFTARAN TERNAKAN RUMINAN 2024</div>
                <div class="font-bold text-sm uppercase mt-0.5">JADUAL PERTAMA</div>
                <div class="font-bold text-sm uppercase mt-0.5">BORANG A</div>
                <div class="text-sm mt-0.5">[subseksyen 5 (4)]</div>
                <div class="font-bold text-sm uppercase tracking-wide mt-1">DAFTAR TERNAKAN RUMINAN</div>
            </div>

            <!-- Jajahan & No. Perakuan Pendaftaran -->
            <div class="flex justify-between items-center mb-4 text-[11pt]">
                <div>
                    <span>Jajahan : </span>
                    <span class="font-bold uppercase">{{ $ternakan->jajahan }}</span>
                </div>
                <div>
                    <span>No. Perakuan Pendaftaran : </span>
                    <span class="font-bold font-mono">{{ $ternakan->no_siri_kad_kuning ?? 'DB' . date('mY') . rand(10, 99) }}</span>
                </div>
            </div>

            <!-- Section 1: Maklumat Pemunya -->
            <div class="mb-4">
                <div class="font-bold uppercase tracking-wide mb-1">MAKLUMAT PEMUNYA</div>
                <div class="space-y-1 pl-2">
                    <div class="grid grid-cols-12">
                        <div class="col-span-4">Nama</div>
                        <div class="col-span-8">: <span class="font-bold uppercase">{{ $ternakan->pemunya->nama ?? '-' }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-4">No. Kad Pengenalan</div>
                        <div class="col-span-8">: <span class="font-mono font-bold">{{ $ternakan->pemunya->no_kp ?? '-' }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-4">Alamat</div>
                        <div class="col-span-8">: <span class="uppercase">{{ $ternakan->pemunya->alamat ?? '-' }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-4">No Telefon</div>
                        <div class="col-span-8">: <span>{{ $ternakan->pemunya->no_telefon ?? '-' }}</span></div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Maklumat Ternakan -->
            <div class="mb-6">
                <div class="font-bold uppercase tracking-wide mb-1">MAKLUMAT TERNAKAN</div>
                <div class="space-y-1 pl-2">
                    <div class="grid grid-cols-12">
                        <div class="col-span-5">Spesies</div>
                        <div class="col-span-7">: 
                            <span>
                                @if(str_contains(strtolower($ternakan->jenis_ternakan), 'lembu'))
                                    <b>Lembu (L)</b> / Kerbau (K) / Kambing (C) / Biri-Biri (B)
                                @elseif(str_contains(strtolower($ternakan->jenis_ternakan), 'kerbau'))
                                    Lembu (L) / <b>Kerbau (K)</b> / Kambing (C) / Biri-Biri (B)
                                @elseif(str_contains(strtolower($ternakan->jenis_ternakan), 'kambing'))
                                    Lembu (L) / Kerbau (K) / <b>Kambing (C)</b> / Biri-Biri (B)
                                @else
                                    Lembu (L) / Kerbau (K) / Kambing (C) / <b>Biri-Biri (B)</b>
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-5">Baka Pejantan</div>
                        <div class="col-span-7">: <span class="uppercase">{{ $ternakan->baka_pejantan ?? ($ternakan->baka ?? 'KACUKAN') }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-5">Baka Induk</div>
                        <div class="col-span-7">: <span class="uppercase">{{ $ternakan->baka_induk ?? ($ternakan->baka ?? 'KACUKAN') }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-5">No. Tanda Pengenalan Induk</div>
                        <div class="col-span-7">: <span class="uppercase font-mono">{{ $ternakan->no_tanda_pengenalan_induk ?? 'TIADA' }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-5">Baka</div>
                        <div class="col-span-7">: <span class="uppercase font-bold">{{ $ternakan->baka }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-5">Jantina</div>
                        <div class="col-span-7">: <span class="uppercase font-bold">{{ $ternakan->jantina }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-5">Tarikh Lahir</div>
                        <div class="col-span-7">: <span>{{ $ternakan->tarikh_lahir ? $ternakan->tarikh_lahir->format('d/m/Y') : ($ternakan->umur ?? '-') }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-5">Tarikh Daftar</div>
                        <div class="col-span-7">: <span class="font-bold">{{ $ternakan->tarikh_daftar ? $ternakan->tarikh_daftar->format('d/m/Y') : date('d/m/Y') }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-5">No Tanda Pengenalan</div>
                        <div class="col-span-7">: <span class="font-mono font-bold">{{ $ternakan->no_tag }}</span></div>
                    </div>
                    @if($ternakan->program && $ternakan->program !== 'Tiada')
                        <div class="grid grid-cols-12 text-slate-700">
                            <div class="col-span-5">Program (Bantuan/Pawah)</div>
                            <div class="col-span-7">: <span class="font-bold uppercase">{{ $ternakan->program }}</span></div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Section 3: Official History Table -->
            <table class="w-full border-collapse text-[10pt] text-center mb-6">
                <thead>
                    <tr>
                        <th rowspan="3" class="p-2 font-bold w-24 bg-slate-50">TARIKH</th>
                        <th colspan="4" class="p-2 font-bold bg-slate-50">PERKARA (Sila Tanda (/)</th>
                        <th rowspan="3" class="p-2 font-bold w-48 bg-slate-50">CATATAN</th>
                    </tr>
                    <tr>
                        <th rowspan="2" class="p-2 font-bold w-24 bg-slate-50">Kelahiran<br>Anak</th>
                        <th rowspan="2" class="p-2 font-bold w-24 bg-slate-50">Pindah Milik</th>
                        <th colspan="2" class="p-1 font-bold bg-slate-50">Pembatalan Pendaftaran</th>
                    </tr>
                    <tr>
                        <th class="p-1 font-bold w-20 bg-slate-50">Sembelih</th>
                        <th class="p-1 font-bold w-20 bg-slate-50">Mati</th>
                        <th class="p-1 font-bold w-28 bg-slate-50">Pindah Keluar /<br>Eksport</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $hasHistory = false;
                    @endphp

                    @foreach($ternakan->kelahiranAnak as $ka)
                        @php $hasHistory = true; @endphp
                        <tr class="h-10">
                            <td class="p-1 font-mono">{{ $ka->tarikh_kelahiran ? $ka->tarikh_kelahiran->format('d/m/Y') : '-' }}</td>
                            <td class="p-1 font-bold">/</td>
                            <td class="p-1"></td>
                            <td class="p-1"></td>
                            <td class="p-1"></td>
                            <td class="p-1"></td>
                            <td class="p-1 text-left text-[9pt]">Kelahiran Anak: {{ $ka->jantina_anak }} (Baka: {{ $ka->baka_anak }}) - {{ $ka->anakTernakan->no_tag ?? ($ka->no_tag_sementara ?? 'Tag Menunggu') }}</td>
                        </tr>
                    @endforeach

                    @foreach($ternakan->pindahMilik as $pm)
                        @php $hasHistory = true; @endphp
                        <tr class="h-10">
                            <td class="p-1 font-mono">{{ $pm->tarikh_pindah ? $pm->tarikh_pindah->format('d/m/Y') : '-' }}</td>
                            <td class="p-1"></td>
                            <td class="p-1 font-bold">/</td>
                            <td class="p-1"></td>
                            <td class="p-1"></td>
                            <td class="p-1"></td>
                            <td class="p-1 text-left text-[9pt]">Pindah milik kepada {{ $pm->pemunyaBaru->nama ?? 'Pemunya Baru' }}</td>
                        </tr>
                    @endforeach

                    @foreach($ternakan->pembatalanTernakan as $pb)
                        @php $hasHistory = true; @endphp
                        <tr class="h-10">
                            <td class="p-1 font-mono">{{ $pb->tarikh_peristiwa ? $pb->tarikh_peristiwa->format('d/m/Y') : '-' }}</td>
                            <td class="p-1"></td>
                            <td class="p-1"></td>
                            <td class="p-1"></td>
                            <td class="p-1 font-bold">{{ $pb->jenis_batal === 'Mati' ? '/' : '' }}</td>
                            <td class="p-1 font-bold">{{ $pb->jenis_batal === 'Pindah Keluar' ? '/' : '' }}</td>
                            <td class="p-1 text-left text-[9pt]">{{ $pb->sebab }}</td>
                        </tr>
                    @endforeach

                    @foreach($ternakan->permitSembelihan as $ps)
                        @php $hasHistory = true; @endphp
                        <tr class="h-10">
                            <td class="p-1 font-mono">{{ $ps->tarikh_sembelih ? $ps->tarikh_sembelih->format('d/m/Y') : '-' }}</td>
                            <td class="p-1"></td>
                            <td class="p-1"></td>
                            <td class="p-1 font-bold">/</td>
                            <td class="p-1"></td>
                            <td class="p-1"></td>
                            <td class="p-1 text-left text-[9pt]">Permit: {{ $ps->no_permit }} ({{ $ps->tujuan_sembelih }})</td>
                        </tr>
                    @endforeach

                    @if(!$hasHistory)
                        <tr class="h-10">
                            <td class="p-1"></td>
                            <td class="p-1"></td>
                            <td class="p-1"></td>
                            <td class="p-1"></td>
                            <td class="p-1"></td>
                            <td class="p-1"></td>
                            <td class="p-1"></td>
                        </tr>
                        <tr class="h-10">
                            <td class="p-1"></td>
                            <td class="p-1"></td>
                            <td class="p-1"></td>
                            <td class="p-1"></td>
                            <td class="p-1"></td>
                            <td class="p-1"></td>
                            <td class="p-1"></td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <!-- Bottom verification code / date -->
            <div class="text-[9pt] text-slate-500 flex justify-between items-center pt-2">
                <div>Dokumen {{ $index + 1 }} daripada {{ count($ternakanList) }} &bull; Tarikh Cetakan: {{ date('d/m/Y H:i:s') }}</div>
                <div class="font-mono">JPVNK-EPTR-PUKAL-BORANG-A-{{ $ternakan->no_tag }}</div>
            </div>

        </div>
    @endforeach

</body>
</html>
