<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Lesen Perladangan Unggas - EPU Borang B ({{ $permohonan->no_lesen_epu }})</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 15mm;
        }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; margin: 0 !important; padding: 0 !important; font-size: 10.5pt; }
            .license-container { page-break-inside: avoid; break-inside: avoid; box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-slate-100 p-8 flex flex-col items-center">

    <div class="no-print mb-6 flex gap-3">
        <button onclick="window.print()" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-sm rounded-xl shadow-lg transition flex items-center gap-2">
            <i class="fa-solid fa-print"></i> Cetak Lesen EPU (PDF/Print)
        </button>
        <button onclick="window.close()" class="px-4 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold text-sm rounded-xl transition">
            Tutup
        </button>
    </div>

    <!-- Official License Certificate Container (Borang B) -->
    <div class="license-container max-w-3xl w-full bg-white border-4 border-amber-600 p-8 sm:p-10 rounded-3xl shadow-2xl text-slate-900 text-xs leading-relaxed space-y-3 relative overflow-hidden">
        
        <!-- Header -->
        <div class="text-center border-b-2 border-amber-600 pb-4 mb-4">
            <img src="{{ asset('images/logo-veterinar.png') }}" alt="Logo Veterinar" class="w-16 h-16 object-contain mx-auto mb-2">
            <h3 class="text-xs font-black uppercase tracking-widest text-slate-600">KERAJAAN NEGERI KELANTAN</h3>
            <h2 class="text-base font-black uppercase text-slate-900 mt-0.5">JABATAN PERKHIDMATAN VETERINAR NEGERI KELANTAN</h2>
            <div class="text-xs font-black uppercase text-amber-900 mt-2">ENAKMEN PERLADANGAN UNGGAS 2005 (SEKSYEN 7)</div>
            <div class="text-sm font-black underline uppercase text-slate-900 mt-0.5">BORANG B &bull; PERAKUAN LESEN PERLADANGAN UNGGAS</div>
            <div class="font-mono font-black mt-2 text-base text-amber-900">NO. LESEN: {{ $permohonan->no_lesen_epu }}</div>
        </div>

        <p class="text-center italic">
            Adalah diperakui bahawa premis/ladang penternakan unggas seperti yang dinyatakan di bawah telah dilesenkan di bawah Seksyen 7 Enakmen Perladangan Unggas 2005:
        </p>

        <div class="border border-slate-300 rounded-2xl p-5 bg-slate-50 space-y-2">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <span class="text-slate-500 text-[11px] uppercase font-bold block">Nama Pemegang Lesen:</span>
                    <span class="font-bold text-slate-900 text-sm">{{ $permohonan->ladang->nama_pemohon_atau_syarikat }}</span>
                </div>
                <div>
                    <span class="text-slate-500 text-[11px] uppercase font-bold block">No. Syarikat / SSM:</span>
                    <span class="font-mono font-bold">{{ $permohonan->ladang->no_syarikat_atau_ssm ?? '-' }}</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-200">
                <div>
                    <span class="text-slate-500 text-[11px] uppercase font-bold block">Nama Ladang:</span>
                    <span class="font-bold text-slate-900">{{ $permohonan->ladang->nama_ladang }}</span>
                </div>
                <div>
                    <span class="text-slate-500 text-[11px] uppercase font-bold block">Lokasi Ladang / Jajahan:</span>
                    <span>{{ $permohonan->ladang->alamat_ladang }}, Jajahan {{ $permohonan->ladang->jajahan }}</span>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3 pt-2 border-t border-slate-200">
                <div>
                    <span class="text-slate-500 text-[11px] uppercase font-bold block">Jenis Unggas:</span>
                    <span class="font-bold text-amber-900">{{ $permohonan->jenis_unggas }}</span>
                </div>
                <div>
                    <span class="text-slate-500 text-[11px] uppercase font-bold block">Sistem Reban:</span>
                    <span class="font-bold">{{ $permohonan->ladang->sistem_reban }}</span>
                </div>
                <div>
                    <span class="text-slate-500 text-[11px] uppercase font-bold block">Kapasiti Dilesenkan:</span>
                    <span class="font-bold">{{ number_format($permohonan->ladang->kapasiti_maksimum_unggas) }} Ekor</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-200">
                <div>
                    <span class="text-slate-500 text-[11px] uppercase font-bold block">Tempoh Sah Lesen:</span>
                    <span class="font-bold text-emerald-800">{{ $permohonan->tarikh_mula_lesen ? $permohonan->tarikh_mula_lesen->format('d/m/Y') : '-' }} hingga {{ $permohonan->tarikh_tamat_lesen ? $permohonan->tarikh_tamat_lesen->format('d/m/Y') : '-' }}</span>
                </div>
                <div>
                    <span class="text-slate-500 text-[11px] uppercase font-bold block">No. Resit Bayaran:</span>
                    <span class="font-mono font-bold">{{ $permohonan->no_resit_bayaran }} (RM {{ number_format($permohonan->yuran_lesen, 2) }})</span>
                </div>
            </div>
        </div>

        <div>
            <span class="font-bold text-slate-800 uppercase block mb-1 text-[11px]">Syarat-Syarat Khas Lesen:</span>
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-[11px] text-amber-950 whitespace-pre-line">
                {{ $permohonan->syarat_khas_lesen }}
            </div>
        </div>

        <!-- Footer / Signature -->
        <div class="mt-8 pt-8 flex justify-between items-end">
            <div class="text-[10px] text-slate-500">
                <div>Tarikh Dikeluarkan: {{ $permohonan->tarikh_kelulusan ? $permohonan->tarikh_kelulusan->format('d/m/Y') : date('d/m/Y') }}</div>
                <div>Pangkalan Data Bersepadu JPVNK</div>
            </div>
            <div class="text-center w-64">
                <div class="h-16 border-b border-slate-400"></div>
                <div class="font-bold mt-1">Pengarah Perkhidmatan Veterinar</div>
                <div class="text-[10px]">Negeri Kelantan Darul Naim</div>
            </div>
        </div>

    </div>

</body>
</html>
