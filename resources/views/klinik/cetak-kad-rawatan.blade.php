<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Kad Rawatan Haiwan - {{ $temujanji->rawatan->no_rekod_rawatan ?? $temujanji->no_temujanji }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white; margin: 0; padding: 0; font-size: 11pt; }
        }
    </style>
</head>
<body class="bg-slate-100 p-8 flex flex-col items-center">

    <div class="no-print mb-6 flex gap-3">
        <button onclick="window.print()" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm rounded-xl shadow-lg transition flex items-center gap-2">
            <i class="fa-solid fa-print"></i> Cetak Kad Rawatan (PDF/Print)
        </button>
        <button onclick="window.close()" class="px-4 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold text-sm rounded-xl transition">
            Tutup
        </button>
    </div>

    <div class="max-w-2xl w-full bg-white border-2 border-slate-900 p-8 rounded-2xl shadow-xl text-slate-900 text-xs space-y-4">
        
        <!-- Header -->
        <div class="text-center border-b-2 border-slate-900 pb-3 mb-3">
            <img src="{{ asset('images/logo-veterinar.png') }}" alt="Logo Veterinar" class="w-14 h-14 object-contain mx-auto mb-1">
            <h3 class="text-xs font-black uppercase tracking-wider">KERAJAAN NEGERI KELANTAN</h3>
            <h2 class="text-base font-black uppercase">JABATAN PERKHIDMATAN VETERINAR NEGERI KELANTAN</h2>
            <div class="text-xs font-bold uppercase underline mt-1">KAD REKOD RAWATAN & KESIHATAN KLINIK VETERINAR</div>
            <div class="font-mono font-bold text-rose-900 mt-1">NO. REKOD: {{ $temujanji->rawatan->no_rekod_rawatan ?? '-' }}</div>
        </div>

        <table class="w-full border-collapse border border-slate-400 text-xs">
            <tr>
                <td class="border border-slate-400 p-2 font-bold bg-slate-50 w-1/3">Nama Pemilik:</td>
                <td class="border border-slate-400 p-2">{{ $temujanji->pemilik->name ?? '-' }} (Tel: {{ $temujanji->pemilik->phone ?? '-' }})</td>
            </tr>
            <tr>
                <td class="border border-slate-400 p-2 font-bold bg-slate-50">Jenis & Nama Haiwan:</td>
                <td class="border border-slate-400 p-2 font-bold text-rose-900">{{ $temujanji->jenis_haiwan }} @if($temujanji->nama_haiwan) ({{ $temujanji->nama_haiwan }}) @endif</td>
            </tr>
            <tr>
                <td class="border border-slate-400 p-2 font-bold bg-slate-50">Baka & Jantina:</td>
                <td class="border border-slate-400 p-2">{{ $temujanji->baka ?? '-' }} &bull; {{ $temujanji->jantina_haiwan }} (Umur: {{ $temujanji->umur_haiwan ?? '-' }})</td>
            </tr>
            <tr>
                <td class="border border-slate-400 p-2 font-bold bg-slate-50">Tarikh Rawatan:</td>
                <td class="border border-slate-400 p-2">{{ $temujanji->rawatan->tarikh_rawatan ? $temujanji->rawatan->tarikh_rawatan->format('d/m/Y') : date('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="border border-slate-400 p-2 font-bold bg-slate-50">Berat & Suhu:</td>
                <td class="border border-slate-400 p-2">{{ $temujanji->rawatan->berat_badan_kg ?? '-' }} kg &bull; {{ $temujanji->rawatan->suhu_celsius ?? '-' }} °C</td>
            </tr>
            <tr>
                <td class="border border-slate-400 p-2 font-bold bg-slate-50">Diagnosis:</td>
                <td class="border border-slate-400 p-2">{{ $temujanji->rawatan->diagnosis ?? '-' }}</td>
            </tr>
            <tr>
                <td class="border border-slate-400 p-2 font-bold bg-slate-50">Rawatan Diberikan:</td>
                <td class="border border-slate-400 p-2">{{ $temujanji->rawatan->rawatan_diberikan ?? '-' }}</td>
            </tr>
            <tr>
                <td class="border border-slate-400 p-2 font-bold bg-slate-50">Ubat & Vaksinasi:</td>
                <td class="border border-slate-400 p-2 font-mono">{{ $temujanji->rawatan->ubat_diberikan ?? 'Tiada' }} @if($temujanji->rawatan->vaksinasi) &bull; Vaksin: {{ $temujanji->rawatan->vaksinasi }} @endif</td>
            </tr>
            <tr>
                <td class="border border-slate-400 p-2 font-bold bg-slate-50">Caj / Kos Rawatan:</td>
                <td class="border border-slate-400 p-2 font-bold">RM {{ number_format($temujanji->rawatan->kos_rawatan ?? 0, 2) }} (Status: Selesai Bayar)</td>
            </tr>
            @if($temujanji->rawatan->tarikh_temujanji_susulan)
                <tr>
                    <td class="border border-slate-400 p-2 font-bold bg-slate-50">Temujanji Susulan:</td>
                    <td class="border border-slate-400 p-2 font-bold text-amber-800">{{ $temujanji->rawatan->tarikh_temujanji_susulan->format('d/m/Y') }}</td>
                </tr>
            @endif
        </table>

        <div class="mt-8 pt-6 flex justify-between items-end">
            <div class="text-[10px] text-slate-500">
                <div>Klinik: {{ $temujanji->klinik_jajahan }}</div>
                <div>Sistem Veterinar Bersepadu JPVNK</div>
            </div>
            <div class="text-center w-56">
                <div class="h-12 border-b border-slate-400"></div>
                <div class="font-bold mt-1">{{ $temujanji->rawatan->pegawai_veterinar ?? 'Pegawai Veterinar' }}</div>
                <div class="text-[10px]">Pegawai Perubatan Veterinar Bertugas</div>
            </div>
        </div>

    </div>

</body>
</html>
