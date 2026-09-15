@extends('layouts.app')

@section('title', 'Kad Kuning EPTR - Pasport & Perakuan Pendaftaran Ternakan')
@section('page_title', 'Kad Kuning EPTR: Pasport & Perakuan Pendaftaran Ternakan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <a href="{{ route('eptr.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-slate-900 bg-white px-3.5 py-2 rounded-xl border border-slate-200 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Senarai EPTR</span>
        </a>

        <div class="flex flex-wrap items-center gap-2">
            @if($ternakan->status_kelulusan === 'Menunggu' && Auth::user()->isStaff())
                <form action="{{ route('eptr.lulus', $ternakan->id) }}" method="POST" class="inline" onsubmit="return confirm('Luluskan permohonan pendaftaran ternakan ini dan jana No. Tag Telinga rasmi?');">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-black shadow-lg shadow-emerald-700/20 transition flex items-center gap-2">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Luluskan & Jana No. Tag Telinga</span>
                    </button>
                </form>
                <form action="{{ route('eptr.tolak', $ternakan->id) }}" method="POST" class="inline" onsubmit="return confirm('Tolak permohonan pendaftaran ternakan ini?');">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-xmark"></i>
                        <span>Tolak</span>
                    </button>
                </form>
            @endif

            @if(Auth::user()->isStaff() && $ternakan->status_kelulusan === 'Diluluskan' && !empty($ternakan->no_tag))
                <a href="{{ route('eptr.cetak-borang-a', $ternakan->id) }}" target="_blank" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-print text-emerald-400"></i>
                    <span>Cetak Borang A Asal</span>
                </a>
                <a href="{{ route('eptr.cetak-kad-kuning', $ternakan->id) }}" target="_blank" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-id-card text-amber-200"></i>
                    <span>Cetak Kad Kuning (Pasport)</span>
                </a>
            @endif
            @if($ternakan->no_tag && !$ternakan->isDibatalkanAtauMatiAtauSembelih())
                <a href="{{ route('eptr.borang-b.create', ['ternakan_id' => $ternakan->id]) }}" class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md transition flex items-center gap-1.5">
                    <i class="fa-solid fa-arrows-rotate"></i>
                    <span>Pindah Milik (Borang B)</span>
                </a>
                <a href="{{ route('eptr.borang-d.create', ['ternakan_id' => $ternakan->id]) }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-receipt text-slate-700"></i>
                    <span>Borang D</span>
                </a>
                <a href="{{ route('eptr.borang-c.create', ['ternakan_id' => $ternakan->id]) }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-ban text-rose-600"></i>
                    <span>Borang C</span>
                </a>
            @endif
        </div>
    </div>

    @if($ternakan->isDibatalkanAtauMatiAtauSembelih())
        <!-- Status Dibatalkan / Mati / Sembelih Alert Banner -->
        <div class="p-4 bg-rose-50 rounded-2xl border border-rose-300 text-rose-900 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center text-lg font-bold shrink-0 shadow-md">
                <i class="fa-solid fa-ban"></i>
            </div>
            <div>
                <div class="font-bold text-sm">Status Ternakan: {{ strtoupper($ternakan->status) }} (REKOD DITUTUP / TIDAK AKTIF)</div>
                <p class="text-xs text-rose-800 mt-0.5">
                    Ternakan ini telah direkodkan mengalami <b>pembatalan / kematian / penyembelihan</b>. Sebarang urusan rawatan klinikal, vaksinasi, pendaftaran kelahiran anak, permit sembelihan atau perubahan rekod tidak lagi dibenarkan bagi ternakan ini.
                </p>
            </div>
        </div>
    @elseif($ternakan->status_kelulusan === 'Menunggu')
        <!-- Status Alert Banner -->
        <div class="p-4 bg-amber-50 rounded-2xl border border-amber-300 text-amber-900 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center text-lg font-bold">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div>
                    <div class="font-bold text-sm">Status: Menunggu Kelulusan Admin Jajahan EPTR</div>
                    <p class="text-xs text-amber-800">
                        No. Tag Telinga Rasmi akan dijana secara automatik mengikut kod singkatan <b>Daerah {{ $ternakan->daerah }}</b> (contoh: <span class="font-mono font-bold">{{ config("kelantan.jajahan.{$ternakan->jajahan}.kod.{$ternakan->daerah}", 'PRG') }}-0001</span>) sebaik sahaja disahkan.
                    </p>
                </div>
            </div>
            @if(Auth::user()->isStaff())
                <form action="{{ route('eptr.lulus', $ternakan->id) }}" method="POST" class="shrink-0" onsubmit="return confirm('Luluskan permohonan pendaftaran ternakan ini dan jana No. Tag Telinga rasmi?');">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl shadow transition flex items-center gap-1.5">
                        <i class="fa-solid fa-check"></i> Lulus & Jana Tag
                    </button>
                </form>
            @endif
        </div>
    @endif

    <!-- Official Borang B Visual Container (Clean White Professional Card) -->
    <div class="bg-white text-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xl relative overflow-hidden">
        
        <!-- Official Header -->
        <div class="border-b border-slate-200 pb-6 mb-6 text-center relative z-10">
            <div class="flex items-center justify-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-xl bg-emerald-700 text-white flex items-center justify-center text-2xl shadow-md">
                    <i class="fa-solid fa-cow"></i>
                </div>
                <div>
                    <h3 class="text-xs font-black tracking-widest uppercase text-slate-600">KERAJAAN NEGERI KELANTAN</h3>
                    <h2 class="text-base sm:text-lg font-black tracking-tight text-slate-900">JABATAN PERKHIDMATAN VETERINAR NEGERI KELANTAN</h2>
                </div>
            </div>
            <div class="inline-block bg-slate-900 text-white font-mono text-xs font-bold px-4 py-1 rounded-full uppercase tracking-wider mt-1">
                ENAKMEN PENDAFTARAN TERNAKAN RUMINAN 2024 &bull; JADUAL KEDUA [BORANG B]
            </div>
            <p class="text-[11px] font-bold text-slate-700 mt-2">PERAKUAN PENDAFTARAN TERNAKAN RUMINAN</p>
        </div>

        <!-- Borang B Body Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative z-10">
            
            <!-- Left: No Tag & QR Code Box -->
            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200 flex flex-col items-center justify-center text-center">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-600 mb-1">NO. TAG TELINGA RASMI</span>
                <div class="text-xl font-black font-mono {{ $ternakan->no_tag ? 'text-slate-900' : 'text-amber-700 text-sm' }} bg-white px-3.5 py-1.5 rounded-xl border border-slate-300 shadow-2xs">
                    {{ $ternakan->no_tag ?? 'MENUNGGU KELULUSAN' }}
                </div>

                <div class="my-4 p-3 bg-white rounded-xl border border-slate-200 shadow-inner flex flex-col items-center">
                    <i class="fa-solid fa-qrcode text-6xl text-slate-900"></i>
                    <span class="text-[9px] font-mono text-slate-500 mt-1">{{ $ternakan->qr_code ?? 'QR-MENUNGGU' }}</span>
                </div>

                <div class="text-[11px] font-semibold text-slate-800">
                    No. Siri Pendaftaran: <span class="font-mono font-bold">{{ $ternakan->no_siri_kad_kuning ?? 'BELUM DIJANA' }}</span>
                </div>
                <div class="text-[10px] text-slate-600 mt-0.5">
                    Didaftarkan: <b>{{ $ternakan->tarikh_daftar ? $ternakan->tarikh_daftar->format('d/m/Y') : 'Menunggu Kelulusan' }}</b>
                </div>
            </div>

            <!-- Middle: Maklumat Ternakan -->
            <div class="md:col-span-2 bg-white rounded-2xl p-6 border border-slate-200 text-xs space-y-4">
                
                <!-- Pemunya -->
                <div class="pb-3 border-b border-slate-100">
                    <span class="text-[10px] font-black uppercase text-emerald-800">1. MAKLUMAT PEMUNYA BERDAFTAR</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-1.5">
                        <div>
                            <div class="text-[11px] text-slate-500">Nama Pemunya:</div>
                            <div class="font-bold text-slate-900 text-sm">{{ $ternakan->pemunya->nama ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <div class="text-[11px] text-slate-500">No. Kad Pengenalan:</div>
                            <div class="font-mono font-bold text-slate-900">{{ $ternakan->pemunya->no_kp ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
                        <div>
                            <div class="text-[11px] text-slate-500">No. Telefon:</div>
                            <div class="font-medium text-slate-800">{{ $ternakan->pemunya->no_telefon ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-[11px] text-slate-500">Jajahan / Daerah:</div>
                            <div class="font-bold text-slate-800">{{ $ternakan->jajahan }} ({{ $ternakan->daerah ?? ($ternakan->mukim ?? 'Semua Daerah') }}) &bull; {{ $ternakan->poskod ?? ($ternakan->pemunya->poskod ?? '') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Butiran Ternakan -->
                <div class="pb-3 border-b border-slate-100">
                    <span class="text-[10px] font-black uppercase text-emerald-800">2. BUTIRAN TERNAKAN RUMINAN</span>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mt-1.5">
                        <div>
                            <div class="text-[11px] text-slate-500">Jenis:</div>
                            <div class="font-bold text-slate-900 capitalize">{{ $ternakan->jenis_ternakan }}</div>
                        </div>
                        <div>
                            <div class="text-[11px] text-slate-500">Baka:</div>
                            <div class="font-bold text-slate-900 capitalize">{{ $ternakan->baka }}</div>
                        </div>
                        <div>
                            <div class="text-[11px] text-slate-500">Jantina:</div>
                            <div class="font-bold {{ $ternakan->jantina === 'Jantan' ? 'text-blue-700' : 'text-pink-700' }}">{{ $ternakan->jantina }}</div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mt-2">
                        <div>
                            <div class="text-[11px] text-slate-500">Warna:</div>
                            <div class="font-medium text-slate-800">{{ $ternakan->warna ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-[11px] text-slate-500">Umur / T.Lahir:</div>
                            <div class="font-medium text-slate-800">{{ $ternakan->umur ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-[11px] text-slate-500">Tujuan:</div>
                            <div class="font-medium text-slate-800">{{ $ternakan->tujuan_ternakan }}</div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="text-[11px] text-slate-500">Tanda Badan / Ciri Khas:</div>
                        <div class="font-medium text-slate-800">{{ $ternakan->tanda_badan ?? 'Tiada tanda khas' }}</div>
                    </div>
                </div>

                <!-- Program Bantuan (Pawah dll) & Resit Pembayaran -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <span class="text-[10px] font-black uppercase text-emerald-800">3. STATUS PROGRAM / BANTUAN</span>
                        <div class="mt-1.5">
                            <div class="font-bold text-slate-900 text-sm">
                                {{ $ternakan->program && $ternakan->program !== 'Tiada' ? $ternakan->program : 'Ternakan Persendirian' }}
                            </div>
                            <div class="mt-1">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                    Status: {{ $ternakan->status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <span class="text-[10px] font-black uppercase text-emerald-800">4. DOKUMEN RESIT PEMBAYARAN</span>
                        <div class="mt-1.5">
                            @if($ternakan->resit_pembayaran)
                                <a href="{{ asset('storage/' . $ternakan->resit_pembayaran) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold border border-emerald-300 transition">
                                    <i class="fa-solid fa-receipt text-emerald-600"></i>
                                    <span>Lihat Resit Pembayaran</span>
                                </a>
                            @else
                                <span class="inline-flex items-center gap-1 text-slate-400 italic text-xs">
                                    <i class="fa-solid fa-circle-info"></i> Resit di Kaunter JPVNK
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Official Footer Note -->
        <div class="mt-6 pt-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between text-[10px] font-semibold text-slate-600">
            <div>Perakuan ini dikeluarkan secara rasmi di bawah peruntukan Enakmen Pendaftaran Ternakan Ruminan 2024.</div>
            <div class="mt-1 sm:mt-0 font-mono">Pendaftar Ternakan JPVNK</div>
        </div>

    </div>

    <!-- Section: Sejarah Pertukaran Milikan (Borang B) -->
    @if($ternakan->pindahMilik && $ternakan->pindahMilik->count() > 0)
        <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5 text-slate-900 font-bold text-sm sm:text-base">
                    <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-arrows-rotate"></i>
                    </div>
                    <span>Sejarah Pertukaran Milikan (Borang B)</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold text-[11px] uppercase">
                            <th class="px-4 py-2.5">Tarikh Pindah</th>
                            <th class="px-4 py-2.5">Pemunya Asal</th>
                            <th class="px-4 py-2.5">Pemunya Baru</th>
                            <th class="px-4 py-2.5">Sebab Pindah</th>
                            <th class="px-4 py-2.5">Harga Jualan (RM)</th>
                            <th class="px-4 py-2.5">Status</th>
                            <th class="px-4 py-2.5 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($ternakan->pindahMilik as $pm)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-4 py-3 font-medium">{{ $pm->tarikh_pindah ? $pm->tarikh_pindah->format('d/m/Y') : '-' }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $pm->pemunyaAsal->nama ?? '-' }}</td>
                                <td class="px-4 py-3 font-bold text-emerald-800">{{ $pm->pemunyaBaru->nama ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $pm->sebab_pindah }}</td>
                                <td class="px-4 py-3 font-mono">{{ $pm->harga_jualan ? 'RM ' . number_format($pm->harga_jualan, 2) : '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $pm->status_kelulusan === 'Diluluskan' ? 'bg-emerald-100 text-emerald-800' : ($pm->status_kelulusan === 'Ditolak' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                        {{ $pm->status_kelulusan }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('eptr.borang-b.show', $pm->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition">
                                        <i class="fa-solid fa-eye text-blue-700"></i> Borang B
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Section: Sejarah Kelahiran Anak (Jika Induk Betina) -->
    @if($ternakan->jantina === 'Betina')
        <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5 text-slate-900 font-bold text-sm sm:text-base">
                    <div class="w-8 h-8 rounded-xl bg-pink-100 text-pink-700 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-baby"></i>
                    </div>
                    <span>Sejarah Kelahiran Anak Ternakan (Induk Tag: {{ $ternakan->no_tag ?? 'ID-'.$ternakan->id }})</span>
                </div>
                @if(!$ternakan->isDibatalkanAtauMatiAtauSembelih() && (!Auth::user()->isStaff() || Auth::user()->isSuperAdmin()))
                <a href="{{ route('eptr.daftar-anak', ['induk_id' => $ternakan->id]) }}" class="px-3.5 py-1.5 rounded-xl bg-pink-600 hover:bg-pink-700 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5">
                    <i class="fa-solid fa-plus"></i>
                    <span>Daftar Anak Baru</span>
                </a>
                @endif
            </div>

            @if($ternakan->kelahiranAnak && $ternakan->kelahiranAnak->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold text-[11px] uppercase">
                                <th class="px-4 py-2.5">Tarikh Lahir</th>
                                <th class="px-4 py-2.5">Tag Anak</th>
                                <th class="px-4 py-2.5">Jantina & Baka</th>
                                <th class="px-4 py-2.5">Berat Lahir</th>
                                <th class="px-4 py-2.5">Status & Keadaan</th>
                                <th class="px-4 py-2.5 text-right">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($ternakan->kelahiranAnak as $anak)
                                <tr class="hover:bg-slate-50/80">
                                    <td class="px-4 py-3 font-medium">{{ $anak->tarikh_kelahiran ? $anak->tarikh_kelahiran->format('d/m/Y') : '-' }}</td>
                                    <td class="px-4 py-3 font-mono font-bold text-emerald-800">
                                        @if($anak->anakTernakan)
                                            <a href="{{ route('eptr.show', $anak->anak_ternakan_id) }}" class="hover:underline">
                                                {{ $anak->anakTernakan->no_tag ?? 'Tag Belum Lulus' }}
                                            </a>
                                        @else
                                            <span class="text-slate-400 font-normal">{{ $anak->no_tag_sementara ?? '-' }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="font-bold {{ $anak->jantina_anak === 'Jantan' ? 'text-blue-700' : 'text-pink-700' }}">{{ $anak->jantina_anak }}</span> &bull; 
                                        <span class="capitalize">{{ $anak->baka_anak }}</span>
                                    </td>
                                    <td class="px-4 py-3 font-mono">{{ $anak->berat_lahir_kg ? $anak->berat_lahir_kg . ' KG' : '-' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $anak->status_kelahiran === 'Hidup' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                            {{ $anak->status_kelahiran }} ({{ $anak->keadaan_anak }})
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        @if($anak->anakTernakan)
                                            <a href="{{ route('eptr.show', $anak->anak_ternakan_id) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition">
                                                <i class="fa-solid fa-eye text-emerald-700"></i> Profil Anak
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-6 text-center text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                    <i class="fa-solid fa-baby text-2xl text-slate-300 mb-1 block"></i>
                    Belum ada rekod kelahiran anak bagi induk ini.
                </div>
            @endif
        </div>
    @endif

    <!-- Section: Rekod Program Kesihatan & Vaksinasi Ternakan -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-xs space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5 text-slate-900 font-bold text-sm sm:text-base">
                <div class="w-8 h-8 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-heart-pulse"></i>
                </div>
                <span>Rekod Program Kesihatan, Vaksinasi & Rawatan</span>
            </div>
            @if(!$ternakan->isDibatalkanAtauMatiAtauSembelih() && $ternakan->status_kelulusan === 'Diluluskan')
            <a href="{{ route('eptr.kesihatan.create', ['ternakan_id' => $ternakan->id]) }}" class="px-3.5 py-1.5 rounded-xl bg-teal-700 hover:bg-teal-800 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Rekod Kesihatan</span>
            </a>
            @else
            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl bg-slate-100 text-slate-500 font-semibold text-xs border border-slate-200">
                <i class="fa-solid fa-lock text-[10px]"></i> Rekod Ditutup
            </span>
            @endif
        </div>

        @if($ternakan->programKesihatan && $ternakan->programKesihatan->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold text-[11px] uppercase">
                            <th class="px-4 py-2.5">Tarikh Rawatan</th>
                            <th class="px-4 py-2.5">No. Rujukan</th>
                            <th class="px-4 py-2.5">Program & Ubat / Vaksin</th>
                            <th class="px-4 py-2.5">Dos / Berat</th>
                            <th class="px-4 py-2.5">Dos Ulangan (Booster)</th>
                            <th class="px-4 py-2.5">Pegawai Pemeriksa</th>
                            <th class="px-4 py-2.5 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($ternakan->programKesihatan as $pk)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-4 py-3 font-medium">{{ $pk->tarikh_rawatan ? $pk->tarikh_rawatan->format('d/m/Y') : '-' }}</td>
                                <td class="px-4 py-3 font-mono font-bold text-teal-800">
                                    <a href="{{ route('eptr.kesihatan.show', $pk->id) }}" class="hover:underline">
                                        {{ $pk->no_rujukan_kesihatan }}
                                    </a>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-900">{{ $pk->nama_vaksin_atau_ubat }}</div>
                                    <div class="text-[10px] text-slate-500">{{ $pk->jenis_program }}</div>
                                </td>
                                <td class="px-4 py-3 font-mono">
                                    <div>{{ $pk->dos_diberikan ?? '-' }}</div>
                                    <div class="text-[10px] text-slate-500">{{ $pk->berat_semasa_kg ? $pk->berat_semasa_kg . ' KG' : '' }}</div>
                                </td>
                                <td class="px-4 py-3 font-mono font-bold text-amber-800">
                                    {{ $pk->tarikh_ulangan_dos ? $pk->tarikh_ulangan_dos->format('d/m/Y') : 'Tiada' }}
                                </td>
                                <td class="px-4 py-3 text-slate-700">{{ $pk->pegawai_pemeriksa }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('eptr.kesihatan.show', $pk->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition">
                                        <i class="fa-solid fa-eye text-teal-700"></i> Butiran
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-6 text-center text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                <i class="fa-solid fa-heart-pulse text-2xl text-slate-300 mb-1 block"></i>
                Belum ada rekod vaksinasi atau rawatan kesihatan bagi ternakan ini.
            </div>
        @endif
    </div>

</div>
@endsection
