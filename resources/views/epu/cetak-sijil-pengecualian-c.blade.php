<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borang C - Sijil Pengecualian Lesen Perladangan Unggas</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-veterinar.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 18mm 12mm 18mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background-color: #fff;
            line-height: 1.35;
        }
        .dotted-line {
            border-bottom: 1px dotted #222;
            display: inline-block;
            min-height: 1.2em;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0 !important;
                margin: 0 !important;
            }
            .page-container {
                page-break-inside: avoid;
                break-inside: avoid;
            }
        }
    </style>
</head>
<body class="p-4 sm:p-8 max-w-4xl mx-auto text-[13px]">

    <!-- Print Floating Buttons -->
    <div class="no-print fixed top-5 right-5 flex gap-2 z-50 bg-white/95 p-3 rounded-2xl shadow-xl border border-slate-200">
        <button onclick="window.print()" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-sans font-bold text-xs rounded-xl shadow transition flex items-center gap-1.5">
            <i class="fa-solid fa-print"></i> Cetak Sijil Pengecualian (Borang C)
        </button>
        <button onclick="window.close()" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-sans font-bold text-xs rounded-xl transition">
            Tutup
        </button>
    </div>

    <!-- Main Certificate Content (Fit 1 Single Page) -->
    <div class="page-container space-y-3.5">

        <!-- Title Block -->
        <div class="text-center space-y-0.5 mb-4">
            <p class="font-bold text-[14px] uppercase">BORANG C</p>
            <p class="font-bold text-[14px] uppercase tracking-wide">
                SIJIL PENGECUALIAN LESEN PERLADANGAN UNGGAS
            </p>
        </div>

        <!-- Opening Statement -->
        <div class="text-justify leading-normal">
            Adalah dengan ini dikeluarkan sijil pengecualian lesen untuk menjalankan aktiviti perladangan unggas kepada:
        </div>

        <!-- Section: Butir-Butir Pemegang -->
        <div class="space-y-2 pt-1">
            <div class="flex items-baseline">
                <span class="w-60 font-normal">Nama/Syarikat</span>
                <span class="mr-2">:</span>
                <span class="dotted-line flex-1 px-2 font-bold uppercase">
                    {{ $ladang->pemilik->nama_syarikat ?? $ladang->pemilik->name ?? $ladang->nama_pemohon_atau_syarikat }}
                </span>
            </div>

            <div class="flex items-baseline">
                <span class="w-60 font-normal">No. Kad Pengenalan</span>
                <span class="mr-2">:</span>
                <span class="dotted-line flex-1 px-2 font-mono">
                    {{ $ladang->pemilik->ic_number ?? '-' }}
                </span>
            </div>

            <div class="flex items-baseline">
                <span class="w-60 font-normal">No. Pendaftaran Premis / Perniagaan/Perakuan Perbadanan</span>
                <span class="mr-2">:</span>
                <span class="dotted-line flex-1 px-2 font-mono">
                    {{ $ladang->no_syarikat_atau_ssm ?? ($ladang->pemilik->no_ssm ?? '-') }}
                </span>
            </div>

            <div class="space-y-1">
                <div class="flex items-baseline">
                    <span class="w-60 font-normal">Alamat ladang/premis</span>
                    <span class="mr-2">:</span>
                    <span class="dotted-line flex-1 px-2">
                        {{ $ladang->alamat_ladang }}
                    </span>
                </div>
                <div class="dotted-line w-full min-h-[1.3em] px-2">
                    {{ $ladang->poskod }} {{ $ladang->jajahan }}, {{ $ladang->negeri ?? 'Kelantan' }}
                </div>
                <div class="flex items-baseline">
                    <span class="dotted-line flex-1 min-h-[1.3em]"></span>
                    <span class="font-normal mx-2 whitespace-nowrap">GPS Ladang :</span>
                    <span class="dotted-line w-64 px-2 font-mono text-[12px]">
                        @if($ladang->latitude && $ladang->longitude)
                            {{ $ladang->latitude }}, {{ $ladang->longitude }}
                        @else
                            -
                        @endif
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 items-baseline">
                <div class="flex items-baseline">
                    <span class="w-28 font-normal">No Telefon</span>
                    <span class="mr-2">:</span>
                    <span class="dotted-line flex-1 px-2 font-mono">
                        {{ $ladang->pemilik->phone ?? '-' }}
                    </span>
                </div>
                <div class="flex items-baseline">
                    <span class="w-20 font-normal">No. Faks</span>
                    <span class="mr-2">:</span>
                    <span class="dotted-line flex-1 px-2 font-mono">
                        {{ $ladang->pemilik->fax ?? '-' }}
                    </span>
                </div>
            </div>

            <div class="flex items-baseline">
                <span class="w-60 font-normal">Emel</span>
                <span class="mr-2">:</span>
                <span class="dotted-line flex-1 px-2">
                    {{ $ladang->pemilik->email ?? '-' }}
                </span>
            </div>
        </div>

        <!-- Capacity Statement -->
        <div class="pt-1 leading-normal">
            <div class="flex items-baseline flex-wrap">
                <span>Dengan bilangan unggas/keupayaan pengeluaran</span>
                <span class="dotted-line px-4 font-bold font-mono min-w-[160px] text-center">
                    {{ number_format($permohonan->kapasiti_ladang ?: $ladang->kapasiti_maksimum_unggas) }}
                </span>
                <span>ekor hanya di premis atau kawasan seperti di alamat tersebut sahaja.</span>
            </div>
        </div>

        <div class="text-justify leading-normal">
            Sijil ini tidak boleh dipindahmilik dan boleh dibatalkan sekiranya syarat-syarat khas tidak dipatuhi.
        </div>

        <!-- Section: Syarat-Syarat Khas -->
        <div class="space-y-1.5 pt-1">
            <p class="text-center font-bold text-[13px] uppercase tracking-wide">
                SYARAT-SYARAT KHAS
            </p>

            <ol class="space-y-1 text-[12px] leading-snug text-justify list-none pl-0">
                <li class="flex items-start">
                    <span class="w-5 flex-shrink-0 font-bold">1.</span>
                    <span>Jarak minimum ladang adalah sekurang-kurangnya <strong>200 meter</strong> garisan lurus dari mana-mana struktur atau bangunan awam sepertimana dinyatakan di bawah Seksyen 6(a)(iii) Enakmen ini.</span>
                </li>
                <li class="flex items-start">
                    <span class="w-5 flex-shrink-0 font-bold">2.</span>
                    <span>Ladang tidak boleh mendatangkan bahaya kepada kesihatan awam, memudaratkan harta benda awam, atau mendatangkan pencemaran kepada alam sekitar.</span>
                </li>
                <li class="flex items-start">
                    <span class="w-5 flex-shrink-0 font-bold">3.</span>
                    <span>Ladang tidak boleh melepaskan sisa unggas, atau sisa kumbahan unggas ke dalam sungai, anak sungai atau lain-lain.</span>
                </li>
                <li class="flex items-start">
                    <span class="w-5 flex-shrink-0 font-bold">4.</span>
                    <span>Ladang tidak boleh menyebabkan pelepasan bau busuk, debu dan habuk kepada kawasan sekitar.</span>
                </li>
                <li class="flex items-start">
                    <span class="w-5 flex-shrink-0 font-bold">5.</span>
                    <span>Ladang tidak boleh menyebabkan kawasannya menjadi kawasan pembiakan lalat, serangga dan makhluk perosak.</span>
                </li>
                <li class="flex items-baseline">
                    <span class="w-5 flex-shrink-0 font-bold">6.</span>
                    <span class="whitespace-nowrap mr-2">Syarat-syarat lain seperti berikut :</span>
                    <span class="dotted-line flex-1 px-2">
                        {{ $permohonan->syarat_khas_lesen ?? 'Tertakluk kepada pemantauan biosekuriti JPVNK dari semasa ke semasa.' }}
                    </span>
                </li>
            </ol>
        </div>

        <!-- Footer / Signature Block -->
        <div class="pt-6 flex justify-between items-end text-[13px]">
            <div class="w-1/2">
                <div class="flex items-baseline">
                    <span class="font-normal mr-2">Tarikh :</span>
                    <span class="dotted-line w-44 px-2 font-mono">
                        {{ $permohonan->tarikh_kelulusan ? \Carbon\Carbon::parse($permohonan->tarikh_kelulusan)->format('d/m/Y') : date('d/m/Y') }}
                    </span>
                </div>
            </div>

            <div class="w-1/2 text-center space-y-1">
                <div class="dotted-line w-56 mx-auto min-h-[1.4em]"></div>
                <p class="text-[12px] font-normal">Tandatangan Pengarah dan Cop Jabatan</p>
            </div>
        </div>

    </div>

</body>
</html>
