<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>CETAK PUKAL BORANG B ({{ count($pindahMilikList) }} Rekod Pindah Milik)</title>
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
            background-color: #ffffff !important;
            line-height: 1.4;
            font-size: 11pt;
            border: none;
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
        <button onclick="window.print()" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm rounded-xl shadow-lg transition flex items-center gap-2">
            <i class="fa-solid fa-print text-amber-400"></i>
            <span>Cetak Semua Borang B ({{ count($pindahMilikList) }} Rekod)</span>
        </button>
        <span class="text-xs text-slate-500 font-semibold px-2">
            Jumlah Borang B: <span class="text-blue-700 font-bold font-mono">{{ count($pindahMilikList) }}</span>
        </span>
        <a href="{{ route('eptr.borang-b.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-xl transition ml-auto">
            Kembali ke Senarai Borang B
        </a>
    </div>

    <!-- Loop Through Each Borang B Record -->
    @foreach($pindahMilikList as $index => $pm)
        @php
            $ternakan = $pm->ternakan;
            $pemunyaAsal = $ternakan ? $ternakan->pemunya_asal : $pm->pemunyaAsal;
            $senaraiPindah = $ternakan ? $ternakan->senarai_pindah_milik_terkini : collect([$pm]);
            if ($senaraiPindah->isEmpty()) {
                $senaraiPindah = collect([$pm]);
            }
            $row1 = $senaraiPindah->get(0);
            $row2 = $senaraiPindah->get(1);
        @endphp
        <div class="form-container max-w-[210mm] w-full p-8 sm:p-12 shadow-2xl text-black bg-white mb-8 {{ !$loop->last ? 'page-break' : '' }}">
            
            <!-- Header -->
            <div class="text-center mb-6 leading-relaxed">
                <div class="font-bold text-[12pt] tracking-wide uppercase">ENAKMEN PENDAFTARAN TERNAKAN RUMINAN 2024</div>
                <div class="font-bold text-[11pt] uppercase mt-1">JADUAL KEDUA</div>
                <div class="font-bold text-[11pt] uppercase mt-0.5">BORANG B</div>
                <div class="text-[10pt] mt-0.5">[subseksyen 5 (4)]</div>
                <div class="font-bold text-[11.5pt] uppercase tracking-wide mt-2">PERAKUAN DAFTAR TERNAKAN RUMINAN</div>
            </div>

            <!-- Jajahan -->
            <div class="mb-4 text-[11pt]">
                <div class="grid grid-cols-12">
                    <div class="col-span-3">Jajahan</div>
                    <div class="col-span-9">: <span class="font-bold uppercase">{{ $pemunyaAsal->jajahan ?? ($ternakan->jajahan ?? '-') }}</span></div>
                </div>
            </div>

            <!-- Section 1: Maklumat Pemunya (Pemunya Asal) -->
            <div class="mb-4">
                <div class="font-bold uppercase tracking-wide mb-1 text-[11pt]">MAKLUMAT PEMUNYA</div>
                <div class="space-y-1 text-[11pt]">
                    <div class="grid grid-cols-12">
                        <div class="col-span-4">Nama</div>
                        <div class="col-span-8">: <span class="font-bold uppercase">{{ $pemunyaAsal->nama ?? '-' }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-4">No. Kad Pengenenalan</div>
                        <div class="col-span-8">: <span class="font-mono font-bold">{{ $pemunyaAsal->no_kp ?? '-' }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-4">Alamat</div>
                        <div class="col-span-8">: <span class="uppercase">{{ $pemunyaAsal->alamat ?? '-' }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-4">No. Telefon</div>
                        <div class="col-span-8">: <span>{{ $pemunyaAsal->no_telefon ?? '-' }}</span></div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Maklumat Ternakan -->
            <div class="mb-6">
                <div class="font-bold uppercase tracking-wide mb-1 text-[11pt]">MAKLUMAT TERNAKAN</div>
                <div class="space-y-1 text-[11pt]">
                    <div class="grid grid-cols-12">
                        <div class="col-span-5">Spesies</div>
                        <div class="col-span-7">: 
                            <span>
                                @php
                                    $jenisLower = strtolower($ternakan->jenis_ternakan ?? 'lembu');
                                @endphp
                                @if(str_contains($jenisLower, 'kerbau'))
                                    Lembu (L) / <b>Kerbau (K)</b> / Kambing (C) / Biri-Biri (B)
                                @elseif(str_contains($jenisLower, 'kambing'))
                                    Lembu (L) / Kerbau (K) / <b>Kambing (C)</b> / Biri-Biri (B)
                                @elseif(str_contains($jenisLower, 'biri'))
                                    Lembu (L) / Kerbau (K) / Kambing (C) / <b>Biri-Biri (B)</b>
                                @else
                                    <b>Lembu (L)</b> / Kerbau (K) / Kambing (C) / Biri-Biri (B)
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-5">Baka Bapa</div>
                        <div class="col-span-7">: <span class="uppercase">{{ $ternakan->baka_pejantan ?? ($ternakan->baka ?? 'KACUKAN') }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-5">Baka Induk</div>
                        <div class="col-span-7">: <span class="uppercase">{{ $ternakan->baka_induk ?? ($ternakan->baka ?? 'KACUKAN') }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-5">No Tanda Pengenalan Induk</div>
                        <div class="col-span-7">: <span class="uppercase font-mono">{{ $ternakan->no_tanda_pengenalan_induk ?? 'TIADA' }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-5">Baka</div>
                        <div class="col-span-7">: <span class="uppercase font-bold">{{ $ternakan->baka ?? 'KACUKAN' }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-5">Jantina</div>
                        <div class="col-span-7">: <span class="uppercase font-bold">{{ $ternakan->jantina ?? '-' }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-5">Tarikh Lahir</div>
                        <div class="col-span-7">: <span>{{ ($ternakan && $ternakan->tarikh_lahir) ? $ternakan->tarikh_lahir->format('d/m/Y') : '' }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-5">Tarikh Daftar</div>
                        <div class="col-span-7">: <span>{{ ($ternakan && $ternakan->tarikh_daftar) ? $ternakan->tarikh_daftar->format('d.m.Y') : ($ternakan && $ternakan->created_at ? $ternakan->created_at->format('d.m.Y') : '') }}</span></div>
                    </div>
                    <div class="grid grid-cols-12">
                        <div class="col-span-5">No Tanda Pengenalan</div>
                        <div class="col-span-7">: <span class="font-mono font-bold">{{ $ternakan->no_tag ?? '-' }}</span></div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Pertukaran Milikan Table (Pemunya Terbaru di Atas Sekali) -->
            <table class="w-full border-collapse text-[10.5pt] mb-4 bg-transparent">
                <thead>
                    <tr>
                        <th class="p-2 font-bold w-[60%] text-center uppercase bg-slate-50 border border-black">PERTUKARAN MILIKAN</th>
                        <th class="p-2 font-bold w-[40%] text-center uppercase bg-slate-50 border border-black">TANDATANGAN DAN COP PENOLONG PENDAFTAR</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Row 1: Pemunya Terbaru di Atas Sekali -->
                    <tr class="h-28">
                        <td class="p-3 align-top leading-relaxed border border-black">
                            <div class="grid grid-cols-12">
                                <div class="col-span-5">Nama</div>
                                <div class="col-span-7">: <span class="font-bold uppercase">{{ $row1->pemunyaBaru->nama ?? '' }}</span></div>
                            </div>
                            <div class="grid grid-cols-12">
                                <div class="col-span-5">No Kad Pengenalan</div>
                                <div class="col-span-7">: <span class="font-mono">{{ $row1->pemunyaBaru->no_kp ?? '' }}</span></div>
                            </div>
                            <div class="grid grid-cols-12">
                                <div class="col-span-5">Alamat</div>
                                <div class="col-span-7">: <span class="uppercase">{{ $row1->pemunyaBaru->alamat ?? '' }}</span></div>
                            </div>
                            <div class="grid grid-cols-12">
                                <div class="col-span-5">No Telefon</div>
                                <div class="col-span-7">: <span>{{ $row1->pemunyaBaru->no_telefon ?? '' }}</span></div>
                            </div>
                        </td>
                        <td class="p-3 align-bottom text-center border border-black">
                            @if($row1)
                                <div class="text-[9pt] italic">Disahkan oleh Penolong Pendaftar</div>
                                <div class="font-mono text-[8pt]">{{ $row1->tarikh_pindah ? $row1->tarikh_pindah->format('d/m/Y') : '' }}</div>
                            @endif
                        </td>
                    </tr>

                    <!-- Row 2: Pertukaran Terdahulu / Ruang Kosong -->
                    <tr class="h-28">
                        <td class="p-3 align-top leading-relaxed border border-black">
                            <div class="grid grid-cols-12">
                                <div class="col-span-5">Nama</div>
                                <div class="col-span-7">: <span class="font-bold uppercase">{{ $row2->pemunyaBaru->nama ?? '' }}</span></div>
                            </div>
                            <div class="grid grid-cols-12">
                                <div class="col-span-5">No Kad Pengenalan</div>
                                <div class="col-span-7">: <span class="font-mono">{{ $row2->pemunyaBaru->no_kp ?? '' }}</span></div>
                            </div>
                            <div class="grid grid-cols-12">
                                <div class="col-span-5">Alamat</div>
                                <div class="col-span-7">: <span class="uppercase">{{ $row2->pemunyaBaru->alamat ?? '' }}</span></div>
                            </div>
                            <div class="grid grid-cols-12">
                                <div class="col-span-5">No Telefon</div>
                                <div class="col-span-7">: <span>{{ $row2->pemunyaBaru->no_telefon ?? '' }}</span></div>
                            </div>
                        </td>
                        <td class="p-3 align-bottom text-center border border-black">
                            @if($row2)
                                <div class="text-[9pt] italic">Disahkan oleh Penolong Pendaftar</div>
                                <div class="font-mono text-[8pt]">{{ $row2->tarikh_pindah ? $row2->tarikh_pindah->format('d/m/Y') : '' }}</div>
                            @endif
                        </td>
                    </tr>

                    @if($senaraiPindah->count() > 2)
                        @foreach($senaraiPindah->slice(2) as $rowN)
                            <tr class="h-28">
                                <td class="p-3 align-top leading-relaxed border border-black">
                                    <div class="grid grid-cols-12">
                                        <div class="col-span-5">Nama</div>
                                        <div class="col-span-7">: <span class="font-bold uppercase">{{ $rowN->pemunyaBaru->nama ?? '' }}</span></div>
                                    </div>
                                    <div class="grid grid-cols-12">
                                        <div class="col-span-5">No Kad Pengenalan</div>
                                        <div class="col-span-7">: <span class="font-mono">{{ $rowN->pemunyaBaru->no_kp ?? '' }}</span></div>
                                    </div>
                                    <div class="grid grid-cols-12">
                                        <div class="col-span-5">Alamat</div>
                                        <div class="col-span-7">: <span class="uppercase">{{ $rowN->pemunyaBaru->alamat ?? '' }}</span></div>
                                    </div>
                                    <div class="grid grid-cols-12">
                                        <div class="col-span-5">No Telefon</div>
                                        <div class="col-span-7">: <span>{{ $rowN->pemunyaBaru->no_telefon ?? '' }}</span></div>
                                    </div>
                                </td>
                                <td class="p-3 align-bottom text-center border border-black">
                                    <div class="text-[9pt] italic">Disahkan oleh Penolong Pendaftar</div>
                                    <div class="font-mono text-[8pt]">{{ $rowN->tarikh_pindah ? $rowN->tarikh_pindah->format('d/m/Y') : '' }}</div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>

        </div>
    @endforeach

</body>
</html>
