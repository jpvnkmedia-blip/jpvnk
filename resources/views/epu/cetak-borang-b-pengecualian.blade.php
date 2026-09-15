<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borang B - Permohonan Pengecualian Lesen Perladangan Unggas</title>
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
        .checkbox-box {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 20px;
            border: 1px solid #000;
            margin-left: 6px;
            font-size: 13px;
            font-weight: bold;
            vertical-align: middle;
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
            <i class="fa-solid fa-print"></i> Cetak Borang B Pengecualian
        </button>
        <button onclick="window.close()" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-sans font-bold text-xs rounded-xl transition">
            Tutup
        </button>
    </div>

    <!-- Main Form Content (Fit 1 Single Page) -->
    <div class="page-container space-y-4">

        <!-- Title Block -->
        <div class="text-center space-y-0.5 mb-5">
            <p class="font-bold text-[14px] uppercase">BORANG B</p>
            <p class="font-bold text-[14px] uppercase tracking-wide">
                BORANG PERMOHONAN PENGECUALIAN LESEN PERLADANGAN UNGGAS
            </p>
        </div>

        <!-- Opening Statement -->
        <div class="text-justify leading-normal">
            Saya dengan ini membuat permohonan kepada Jabatan Perkhidmatan Veterinar Negeri Kelantan dan memohon pengecualian lesen untuk menjalankan aktiviti perladangan unggas.
        </div>

        <!-- Section: Butir-Butir Pemohon -->
        <div class="space-y-2 pt-1">
            <p class="font-bold">Butir-Butir Pemohon:</p>

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

            <div class="flex items-baseline">
                <span class="w-60 font-normal">Kapasiti ladang</span>
                <span class="mr-2">:</span>
                <span class="dotted-line flex-1 px-2 font-mono font-bold">
                    {{ number_format($permohonan->kapasiti_ladang ?: $ladang->kapasiti_maksimum_unggas) }}
                </span>
                <span class="ml-2">ekor</span>
            </div>
        </div>

        <!-- Section: Sebab-sebab Pengecualian Lesen -->
        <div class="space-y-3 pt-2">
            <p class="font-normal">Sebab-sebab Pengecualian Lesen:</p>

            <div class="grid grid-cols-3 gap-3 items-center text-[12.5px]">
                <div class="flex items-center justify-between border border-transparent p-0.5">
                    <span>Projek D'Raja</span>
                    <span class="checkbox-box"></span>
                </div>
                <div class="flex items-center justify-between border border-transparent p-0.5">
                    <span class="leading-tight">Program Pembasmian<br>Rakyat Termiskin</span>
                    <span class="checkbox-box"></span>
                </div>
                <div class="flex items-center justify-between border border-transparent p-0.5">
                    <span>Amanah Ikhtiar Malaysia</span>
                    <span class="checkbox-box"></span>
                </div>
            </div>

            <div class="flex items-center gap-4 text-[12.5px]">
                <span>Lain-Lain</span>
                <span class="checkbox-box">
                    @php
                        $kapasiti = $permohonan->kapasiti_ladang ?: $ladang->kapasiti_maksimum_unggas;
                        $isSmallScale = ($kapasiti <= 500);
                    @endphp
                    {{ $isSmallScale ? '✓' : '' }}
                </span>
            </div>

            <div class="flex items-baseline text-[12.5px]">
                <span class="whitespace-nowrap mr-2">Sila Nyatakan:</span>
                <span class="dotted-line flex-1 px-2">
                    @if($isSmallScale)
                        Penternakan Skala Kecil (Bawah Kapasiti Pemegang Lesen Wajib: {{ number_format($kapasiti) }} ekor)
                    @endif
                </span>
            </div>
        </div>

        <!-- Footer / Signature Block -->
        <div class="pt-8 flex justify-between items-end text-[13px]">
            <div class="w-1/2">
                <div class="flex items-baseline">
                    <span class="font-normal mr-2">Tarikh :</span>
                    <span class="dotted-line w-44 px-2 font-mono">
                        {{ $permohonan->created_at ? $permohonan->created_at->format('d/m/Y') : date('d/m/Y') }}
                    </span>
                </div>
            </div>

            <div class="w-1/2 text-center space-y-1">
                <div class="dotted-line w-56 mx-auto min-h-[1.4em]"></div>
                <p class="text-[12px] font-normal">Tandatangan Pemohon dan Cop Syarikat</p>
            </div>
        </div>

    </div>

</body>
</html>
