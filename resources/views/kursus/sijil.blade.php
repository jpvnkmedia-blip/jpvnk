<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Sijil Penyertaan Kursus - {{ $application->certificate_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white; margin: 0; padding: 0; }
            .certificate-border { border: 8px double #d97706 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 p-8 flex flex-col items-center min-h-screen">

    <div class="no-print mb-6 flex gap-3">
        <button onclick="window.print()" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-sm rounded-xl shadow-lg transition flex items-center gap-2">
            <i class="fa-solid fa-print"></i> Cetak Sijil Penyertaan Digital (PDF/Print)
        </button>
        <button onclick="window.close()" class="px-4 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold text-sm rounded-xl transition">
            Tutup
        </button>
    </div>

    <!-- Printable Certificate Box -->
    <div class="certificate-border max-w-3xl w-full bg-white border-8 border-double border-amber-600 p-12 rounded-3xl shadow-2xl text-center text-slate-900 relative overflow-hidden">
        
        <!-- Watermark -->
        <div class="absolute inset-0 flex items-center justify-center opacity-5 pointer-events-none">
            <i class="fa-solid fa-award text-[360px] text-amber-600"></i>
        </div>

        <div class="relative z-10 space-y-6">
            
            <!-- Emblem / Crest -->
            <div class="space-y-1">
                <img src="{{ asset('images/logo-veterinar.png') }}" alt="Logo Jabatan Perkhidmatan Veterinar" class="w-20 h-20 object-contain mx-auto mb-2">
                <h3 class="text-xs font-black tracking-widest uppercase text-slate-500">KERAJAAN NEGERI KELANTAN</h3>
                <h2 class="text-base font-black uppercase text-slate-900">JABATAN PERKHIDMATAN VETERINAR NEGERI KELANTAN</h2>
                <div class="text-[10px] font-mono text-slate-400">NO. SIJIL: {{ $application->certificate_number }}</div>
            </div>

            <!-- Title -->
            <div>
                <h1 class="text-3xl font-black text-amber-900 font-serif tracking-tight uppercase">SIJIL PENYERTAAN</h1>
                <p class="text-xs text-slate-500 mt-1 italic">Dengan ini diperakui bahawa</p>
            </div>

            <!-- Recipient Name -->
            <div class="py-2 border-b-2 border-amber-400/60 max-w-lg mx-auto">
                <div class="text-2xl font-black text-slate-900 uppercase font-serif">{{ $application->user->name ?? 'Peserta Kursus' }}</div>
                <div class="text-xs font-mono font-bold text-slate-600 mt-0.5">NO. KAD PENGENALAN: {{ $application->user->ic_number ?? '-' }}</div>
            </div>

            <!-- Course Title -->
            <div class="space-y-1 max-w-xl mx-auto text-xs leading-relaxed text-slate-700">
                <p>Telah berjaya menyertai dan menyempurnakan program latihan:</p>
                <div class="text-base font-black text-slate-900 uppercase font-serif text-cyan-950">
                    "{{ $application->course->title }}"
                </div>
                <p class="text-[11px] text-slate-500 pt-1">
                    Anjuran Bahagian Latihan & Pusat Latihan Veterinar Kelantan pada <b>{{ $application->course->start_date ? $application->course->start_date->format('d F Y') : '-' }}</b> bertempat di <b>{{ $application->course->location }}</b>.
                </p>
            </div>

            <!-- Signature & Date -->
            <div class="pt-8 grid grid-cols-2 gap-8 text-xs">
                <div class="text-center">
                    <div class="text-[11px] text-slate-500">Tarikh Dikeluarkan:</div>
                    <div class="font-bold text-slate-900">{{ $application->certificate_issued_at ? $application->certificate_issued_at->format('d/m/Y') : date('d/m/Y') }}</div>
                </div>
                <div class="text-center">
                    <div class="h-12 border-b border-slate-400 max-w-xs mx-auto"></div>
                    <div class="font-bold mt-1">Pengarah Perkhidmatan Veterinar</div>
                    <div class="text-[10px] text-slate-500">Negeri Kelantan Darul Naim</div>
                </div>
            </div>

        </div>

    </div>

</body>
</html>
