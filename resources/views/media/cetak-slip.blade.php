<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Pengesahan Tempahan Media - {{ $tempahan->no_rujukan }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; margin: 0 !important; padding: 0 !important; font-size: 11pt; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .print-container { border: 1.5px solid #0f172a !important; box-shadow: none !important; width: 100% !important; max-width: 100% !important; border-radius: 0 !important; }
        }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    </style>
</head>
<body class="bg-slate-100 p-6 flex flex-col items-center min-h-screen text-slate-800">

    <!-- Print Action Buttons (Hidden on Print) -->
    <div class="no-print mb-6 flex gap-3">
        <button onclick="window.print()" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-lg transition flex items-center gap-2">
            <i class="fa-solid fa-print"></i> Cetak Slip Pengesahan (PDF/Print)
        </button>
        <button onclick="window.close()" class="px-4 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-sm rounded-xl transition">
            Tutup
        </button>
    </div>

    <!-- Official Slip Container -->
    <div class="print-container max-w-3xl w-full bg-white border-2 border-slate-900 p-8 rounded-2xl shadow-xl text-slate-900 text-xs space-y-4">
        
        <!-- Header -->
        <div class="flex items-center justify-between border-b-2 border-slate-900 pb-4">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo-veterinar.png') }}" alt="Logo JPVNK" class="w-16 h-16 object-contain" onerror="this.src='https://via.placeholder.com/64?text=JPVNK'">
                <div>
                    <h3 class="text-[11px] font-bold uppercase tracking-wider text-slate-700">KERAJAAN NEGERI KELANTAN</h3>
                    <h2 class="text-sm font-black uppercase text-slate-900 leading-snug">JABATAN PERKHIDMATAN VETERINAR NEGERI KELANTAN</h2>
                    <p class="text-[11px] font-semibold text-indigo-900">UNIT MEDIA, KOMUNIKASI KORPORAT & SIARAN</p>
                </div>
            </div>
            <div class="text-right">
                <div class="inline-block px-3 py-1 bg-slate-900 text-white font-mono font-bold text-[10px] rounded tracking-wider uppercase">
                    SLIP PENGESAHAN TEMPAHAN
                </div>
                <div class="text-[11px] font-bold font-mono text-indigo-950 mt-1">NO: {{ $tempahan->no_rujukan }}</div>
                <div class="text-[10px] text-slate-500">Tarikh Jana: {{ now()->format('d/m/Y h:i A') }}</div>
            </div>
        </div>

        <!-- Status Banner -->
        <div class="flex items-center justify-between p-3 rounded-lg border {{ $tempahan->status == 'Diluluskan' || $tempahan->status == 'Selesai' ? 'bg-emerald-50 border-emerald-300 text-emerald-950' : ($tempahan->status == 'Ditolak' ? 'bg-rose-50 border-rose-300 text-rose-950' : 'bg-amber-50 border-amber-300 text-amber-950') }}">
            <div class="flex items-center gap-2">
                <span class="font-bold uppercase text-[11px]">STATUS TEMPAHAN:</span>
                <span class="font-black px-2.5 py-0.5 rounded text-xs {{ $tempahan->status == 'Diluluskan' || $tempahan->status == 'Selesai' ? 'bg-emerald-600 text-white' : ($tempahan->status == 'Ditolak' ? 'bg-rose-600 text-white' : 'bg-amber-500 text-white') }}">
                    {{ strtoupper($tempahan->status) }}
                </span>
            </div>
            <div class="text-right text-[11px]">
                <span class="font-bold">Keutamaan:</span> 
                <span class="font-bold uppercase {{ $tempahan->keutamaan == 'Sangat Segera' ? 'text-rose-600' : ($tempahan->keutamaan == 'Segera' ? 'text-amber-600' : 'text-slate-700') }}">
                    {{ $tempahan->keutamaan }}
                </span>
            </div>
        </div>

        <!-- Section 1: Maklumat Pemohon & Bahagian -->
        <div>
            <h4 class="font-black text-slate-900 uppercase text-[11px] bg-slate-100 px-2 py-1 border-l-4 border-indigo-600 mb-2">
                1. MAKLUMAT PEMOHON
            </h4>
            <table class="w-full border-collapse border border-slate-300 text-[11px]">
                <tr>
                    <td class="w-1/4 border border-slate-300 bg-slate-50 p-2 font-bold text-slate-700">Nama Pemohon</td>
                    <td class="w-1/4 border border-slate-300 p-2 font-semibold">{{ $tempahan->nama_pemohon }}</td>
                    <td class="w-1/4 border border-slate-300 bg-slate-50 p-2 font-bold text-slate-700">Jawatan</td>
                    <td class="w-1/4 border border-slate-300 p-2">{{ $tempahan->jawatan_pemohon }}</td>
                </tr>
                <tr>
                    <td class="border border-slate-300 bg-slate-50 p-2 font-bold text-slate-700">Bahagian / Unit / PPVJ</td>
                    <td class="border border-slate-300 p-2">{{ $tempahan->bahagian_unit }}</td>
                    <td class="border border-slate-300 bg-slate-50 p-2 font-bold text-slate-700">No. Telefon & Emel</td>
                    <td class="border border-slate-300 p-2">{{ $tempahan->no_telefon }} | {{ $tempahan->emel }}</td>
                </tr>
            </table>
        </div>

        <!-- Section 2: Maklumat Program -->
        <div>
            <h4 class="font-black text-slate-900 uppercase text-[11px] bg-slate-100 px-2 py-1 border-l-4 border-indigo-600 mb-2">
                2. MAKLUMAT PROGRAM / AKTIVITI
            </h4>
            <table class="w-full border-collapse border border-slate-300 text-[11px]">
                <tr>
                    <td class="w-1/4 border border-slate-300 bg-slate-50 p-2 font-bold text-slate-700">Nama Program</td>
                    <td colspan="3" class="border border-slate-300 p-2 font-black text-indigo-950">{{ $tempahan->nama_program }}</td>
                </tr>
                <tr>
                    <td class="border border-slate-300 bg-slate-50 p-2 font-bold text-slate-700">Tarikh & Masa</td>
                    <td class="border border-slate-300 p-2 font-semibold">
                        {{ $tempahan->tarikh_program ? $tempahan->tarikh_program->format('d/m/Y (l)') : '-' }}<br>
                        <span class="text-slate-600">{{ $tempahan->masa_mula }} - {{ $tempahan->masa_tamat }}</span>
                    </td>
                    <td class="border border-slate-300 bg-slate-50 p-2 font-bold text-slate-700">Lokasi / Tempat</td>
                    <td class="border border-slate-300 p-2 font-semibold">{{ $tempahan->lokasi }}</td>
                </tr>
                <tr>
                    <td class="border border-slate-300 bg-slate-50 p-2 font-bold text-slate-700">Penganjur / Urus Setia</td>
                    <td class="border border-slate-300 p-2">{{ $tempahan->penganjur }}</td>
                    <td class="border border-slate-300 bg-slate-50 p-2 font-bold text-slate-700">Pegawai Dihubungi & Peserta</td>
                    <td class="border border-slate-300 p-2">{{ $tempahan->pegawai_bertanggungjawab }} (Anggaran: {{ $tempahan->anggaran_peserta ?? '-' }} orang)</td>
                </tr>
            </table>
        </div>

        <!-- Section 3 & 4: Perkhidmatan Media & Keperluan -->
        <div>
            <h4 class="font-black text-slate-900 uppercase text-[11px] bg-slate-100 px-2 py-1 border-l-4 border-indigo-600 mb-2">
                3. JENIS PERKHIDMATAN MEDIA &amp; BUTIRAN KEPERLUAN
            </h4>
            <div class="border border-slate-300 p-3 rounded bg-slate-50 space-y-2">
                <div>
                    <span class="font-bold text-slate-700">Jenis Perkhidmatan Dipohon:</span>
                    <div class="flex flex-wrap gap-1.5 mt-1">
                        @if($tempahan->jenis_permohonan && is_array($tempahan->jenis_permohonan))
                            @foreach($tempahan->jenis_permohonan as $jenis)
                                <span class="px-2 py-0.5 bg-indigo-100 text-indigo-900 font-bold text-[10px] rounded border border-indigo-200">
                                    ✓ {{ $jenis }}
                                </span>
                            @endforeach
                        @else
                            <span class="text-slate-500 italic">Tiada pilihan</span>
                        @endif
                    </div>
                </div>

                @if(!empty($tempahan->keperluan_fotografi))
                    <div class="border-t border-slate-200 pt-1.5 text-[11px]">
                        <span class="font-bold text-slate-700">Fokus Fotografi:</span>
                        <span class="text-slate-900">{{ implode(', ', (array)$tempahan->keperluan_fotografi) }}</span>
                    </div>
                @endif

                @if(!empty($tempahan->keperluan_poster))
                    <div class="border-t border-slate-200 pt-1.5 text-[11px]">
                        <span class="font-bold text-slate-700">Butiran Poster/Grafik:</span>
                        <span class="text-slate-900">
                            Tajuk: {{ $tempahan->keperluan_poster['tajuk'] ?? '-' }} | 
                            Saiz: {{ $tempahan->keperluan_poster['saiz'] ?? '-' }} | 
                            Konsep: {{ $tempahan->keperluan_poster['konsep'] ?? '-' }}
                        </span>
                    </div>
                @endif

                @if(!empty($tempahan->keperluan_video))
                    <div class="border-t border-slate-200 pt-1.5 text-[11px]">
                        <span class="font-bold text-slate-700">Butiran Video:</span>
                        <span class="text-slate-900">
                            Format: {{ $tempahan->keperluan_video['format'] ?? '-' }} | 
                            Tempoh: {{ $tempahan->keperluan_video['durasi'] ?? '-' }}
                        </span>
                    </div>
                @endif

                @if(!empty($tempahan->catatan_keperluan))
                    <div class="border-t border-slate-200 pt-1.5 text-[11px]">
                        <span class="font-bold text-slate-700">Catatan Tambahan Pemohon:</span>
                        <p class="text-slate-800 italic mt-0.5">{{ $tempahan->catatan_keperluan }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Section 5: Kelulusan & Penugasan Krew Media -->
        <div>
            <h4 class="font-black text-slate-900 uppercase text-[11px] bg-slate-100 px-2 py-1 border-l-4 border-indigo-600 mb-2">
                4. PENGESAHAN &amp; PENUGASAN UNIT MEDIA
            </h4>
            <table class="w-full border-collapse border border-slate-300 text-[11px]">
                <tr>
                    <td class="w-1/4 border border-slate-300 bg-slate-50 p-2 font-bold text-slate-700">Pegawai Pelulus</td>
                    <td class="w-1/4 border border-slate-300 p-2 font-semibold">{{ $tempahan->pelulus->name ?? 'Belum Ditugaskan' }}</td>
                    <td class="w-1/4 border border-slate-300 bg-slate-50 p-2 font-bold text-slate-700">Tarikh Keputusan</td>
                    <td class="w-1/4 border border-slate-300 p-2">{{ $tempahan->tarikh_kelulusan ? $tempahan->tarikh_kelulusan->format('d/m/Y h:i A') : '-' }}</td>
                </tr>
                <tr>
                    <td class="border border-slate-300 bg-slate-50 p-2 font-bold text-slate-700">Krew / Pegawai Ditugaskan</td>
                    <td class="border border-slate-300 p-2 font-bold text-indigo-900">
                        {{ $tempahan->pegawai_media_bertugas ?? $tempahan->pegawaiBertugas->name ?? '-' }}
                    </td>
                    <td class="border border-slate-300 bg-slate-50 p-2 font-bold text-slate-700">Peralatan Diperuntukkan</td>
                    <td class="border border-slate-300 p-2">{{ $tempahan->peralatan_disediakan ?? '-' }}</td>
                </tr>
                @if($tempahan->catatan_admin)
                <tr>
                    <td class="border border-slate-300 bg-slate-50 p-2 font-bold text-slate-700">Ulasan / Arahan Unit Media</td>
                    <td colspan="3" class="border border-slate-300 p-2 text-slate-900">{{ $tempahan->catatan_admin }}</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Signature Blocks -->
        <div class="pt-4 grid grid-cols-2 gap-8 text-[11px]">
            <div class="border border-dashed border-slate-300 p-4 rounded text-center">
                <p class="font-bold text-slate-700">DIPOHON OLEH:</p>
                <div class="h-16 flex items-center justify-center">
                    <span class="font-mono text-xs text-slate-400 italic">[ Tandatangan Pemohon ]</span>
                </div>
                <div class="border-t border-slate-300 pt-1 font-bold">{{ $tempahan->nama_pemohon }}</div>
                <div class="text-[10px] text-slate-500">{{ $tempahan->jawatan_pemohon }}</div>
                <div class="text-[10px] text-slate-500">Tarikh: {{ $tempahan->created_at ? $tempahan->created_at->format('d/m/Y') : '-' }}</div>
            </div>

            <div class="border border-dashed border-slate-300 p-4 rounded text-center">
                <p class="font-bold text-slate-700">DISAHKAN / DILULUSKAN OLEH:</p>
                <div class="h-16 flex items-center justify-center">
                    @if($tempahan->pelulus && $tempahan->pelulus->signature && \Illuminate\Support\Facades\Storage::disk('public')->exists($tempahan->pelulus->signature))
                        <img src="{{ asset('storage/' . $tempahan->pelulus->signature) }}" alt="Tandatangan" class="max-h-14 max-w-full object-contain">
                    @elseif($tempahan->status == 'Diluluskan' || $tempahan->status == 'Selesai')
                        <span class="font-mono text-xs text-emerald-600 font-bold">[ DISAHKAN SECARA DIGITAL ]</span>
                    @else
                        <span class="font-mono text-xs text-slate-400 italic">[ Menunggu Kelulusan ]</span>
                    @endif
                </div>
                <div class="border-t border-slate-300 pt-1 font-bold">{{ $tempahan->pelulus->name ?? 'Pegawai Unit Media' }}</div>
                <div class="text-[10px] text-slate-500">Unit Media, Komunikasi Korporat &amp; Siaran JPVNK</div>
                <div class="text-[10px] text-slate-500">Tarikh: {{ $tempahan->tarikh_kelulusan ? $tempahan->tarikh_kelulusan->format('d/m/Y') : '-' }}</div>
            </div>
        </div>

        <!-- Footer / Barcode -->
        <div class="border-t border-slate-300 pt-2 flex items-center justify-between text-[10px] text-slate-500">
            <div>
                * Cetakan komputer ini adalah sah dan dijana secara automatik oleh Sistem Bersepadu JPVNK.
            </div>
            <div class="font-mono font-bold text-slate-700">
                #{{ $tempahan->no_rujukan }}
            </div>
        </div>

    </div>

</body>
</html>
