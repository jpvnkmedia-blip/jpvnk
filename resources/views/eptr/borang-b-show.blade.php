@extends('layouts.app')

@section('title', 'EPTR Borang B - Perakuan Pertukaran Milikan (' . ($pindahMilik->ternakan->no_tag ?? 'ID-'.$pindahMilik->id) . ')')
@section('page_title', 'EPTR Borang B: Notis Pertukaran Milikan Ternakan Ruminan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <a href="{{ route('eptr.borang-b.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-slate-900 bg-white px-3.5 py-2 rounded-xl border border-slate-200 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Senarai Borang B</span>
        </a>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('eptr.show', $pindahMilik->ternakan_id) }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition flex items-center gap-1.5">
                <i class="fa-solid fa-cow text-emerald-600"></i>
                <span>Kad Kuning Ternakan</span>
            </a>

            @if(Auth::user()->isStaff())
                @if($pindahMilik->status_kelulusan === 'Menunggu')
                    <form action="{{ route('eptr.borang-b.lulus', $pindahMilik->id) }}" method="POST" class="inline" onsubmit="return confirm('Luluskan permohonan pindah milik ini dan tukar hak milik ternakan secara rasmi?');">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-black shadow-lg shadow-emerald-700/20 transition flex items-center gap-2">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>Luluskan Pindah Milik</span>
                        </button>
                    </form>
                    <form action="{{ route('eptr.borang-b.tolak', $pindahMilik->id) }}" method="POST" class="inline" onsubmit="return confirm('Tolak permohonan pindah milik ini?');">
                        @csrf
                        <button type="submit" class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold transition flex items-center gap-1.5">
                            <i class="fa-solid fa-xmark"></i>
                            <span>Tolak</span>
                        </button>
                    </form>
                @endif

                <a href="{{ route('eptr.borang-b.cetak', $pindahMilik->id) }}" target="_blank" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-print"></i>
                    <span>Cetak Borang B Asal</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Status Alert Banner -->
    @if($pindahMilik->status_kelulusan === 'Diluluskan')
        <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-300 text-emerald-900 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-lg font-bold">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <div class="font-bold text-sm">Status: Pindah Milik Telah Diluluskan & Hak Milik Sah Dipindahkan</div>
                    <p class="text-xs text-emerald-800">
                        Diluluskan oleh <b>{{ $pindahMilik->pelulus->nama ?? 'Pegawai JPVNK' }}</b> pada {{ $pindahMilik->updated_at->format('d/m/Y H:i') }}.
                    </p>
                </div>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-200 text-emerald-900">DILULUSKAN</span>
        </div>
    @elseif($pindahMilik->status_kelulusan === 'Ditolak')
        <div class="p-4 bg-rose-50 rounded-2xl border border-rose-300 text-rose-900 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center text-lg font-bold">
                    <i class="fa-solid fa-ban"></i>
                </div>
                <div>
                    <div class="font-bold text-sm">Status: Permohonan Pindah Milik Ditolak</div>
                    <p class="text-xs text-rose-800">{{ $pindahMilik->catatan ?? 'Permohonan tidak memenuhi syarat peruntukan EPTR.' }}</p>
                </div>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-black bg-rose-200 text-rose-900">DITOLAK</span>
        </div>
    @else
        <div class="p-4 bg-amber-50 rounded-2xl border border-amber-300 text-amber-900 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center text-lg font-bold">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div>
                    <div class="font-bold text-sm">Status: Menunggu Pengesahan / Kelulusan Admin Jajahan EPTR</div>
                    <p class="text-xs text-amber-800">Permohonan pindah milik ternakan sedang dalam proses semakan oleh pentadbir jajahan.</p>
                </div>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-black bg-amber-200 text-amber-900">MENUNGGU</span>
        </div>
    @endif

    <!-- Official Visual Card Container -->
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
            <div class="inline-block bg-blue-700 text-white font-mono text-xs font-bold px-4 py-1 rounded-full uppercase tracking-wider mt-1">
                ENAKMEN PENDAFTARAN TERNAKAN RUMINAN 2024 &bull; JADUAL KEDUA [BORANG B]
            </div>
            <p class="text-[12px] font-bold text-slate-800 mt-2">NOTIS PERTUKARAN / PEMINDAHAN MILIKAN TERNAKAN RUMINAN</p>
        </div>

        <!-- Content Body Grid -->
        <div class="space-y-6 relative z-10 text-xs">
            
            <!-- 1. Maklumat Ternakan -->
            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200">
                <span class="text-[10px] font-black uppercase text-blue-900 tracking-wider">1. BUTIRAN TERNAKAN RUMINAN</span>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-3">
                    <div>
                        <div class="text-[11px] text-slate-500">No. Tag Telinga:</div>
                        <div class="font-mono font-bold text-slate-900 text-sm">{{ $pindahMilik->ternakan->no_tag ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="text-[11px] text-slate-500">Spesies / Jenis:</div>
                        <div class="font-bold text-slate-900 capitalize">{{ $pindahMilik->ternakan->jenis_ternakan ?? 'Lembu' }}</div>
                    </div>
                    <div>
                        <div class="text-[11px] text-slate-500">Baka & Jantina:</div>
                        <div class="font-bold text-slate-900 capitalize">{{ $pindahMilik->ternakan->baka }} ({{ $pindahMilik->ternakan->jantina }})</div>
                    </div>
                    <div>
                        <div class="text-[11px] text-slate-500">Warna / Tanda Badan:</div>
                        <div class="font-medium text-slate-800">{{ $pindahMilik->ternakan->warna ?? '-' }} ({{ $pindahMilik->ternakan->tanda_badan ?? 'Tiada tanda khas' }})</div>
                    </div>
                </div>
            </div>

            <!-- 2. Perbandingan Pemilik Asal vs Pemilik Baru -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <!-- Pemilik Asal (Penjual) -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200 space-y-2">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <span class="text-[10px] font-black uppercase text-slate-600">2. PEMUNYA ASAL (PENJUAL / PEMBERI)</span>
                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[10px] font-bold">Pemilik Terdahulu</span>
                    </div>
                    <div>
                        <div class="text-[11px] text-slate-500">Nama Pemunya:</div>
                        <div class="font-bold text-slate-900 text-sm">{{ $pindahMilik->pemunyaAsal->nama ?? '-' }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <div class="text-[11px] text-slate-500">No. Kad Pengenalan:</div>
                            <div class="font-mono font-bold text-slate-800">{{ $pindahMilik->pemunyaAsal->no_kp ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-[11px] text-slate-500">No. Telefon:</div>
                            <div class="font-medium text-slate-800">{{ $pindahMilik->pemunyaAsal->no_telefon ?? '-' }}</div>
                        </div>
                    </div>
                    <div>
                        <div class="text-[11px] text-slate-500">Alamat & Jajahan:</div>
                        <div class="text-slate-800">{{ $pindahMilik->pemunyaAsal->alamat ?? '-' }}, Jajahan {{ $pindahMilik->pemunyaAsal->jajahan ?? '-' }}</div>
                    </div>
                </div>

                <!-- Pemilik Baru (Pembeli) -->
                <div class="bg-blue-50/40 rounded-2xl p-5 border border-blue-200 space-y-2">
                    <div class="flex items-center justify-between border-b border-blue-100 pb-2">
                        <span class="text-[10px] font-black uppercase text-blue-800">3. PEMUNYA BAHARU (PEMBELI / PENERIMA)</span>
                        <span class="px-2 py-0.5 rounded-md bg-blue-100 text-blue-800 text-[10px] font-bold">Pemilik Sah Baharu</span>
                    </div>
                    <div>
                        <div class="text-[11px] text-slate-500">Nama Pemunya:</div>
                        <div class="font-bold text-blue-950 text-sm">{{ $pindahMilik->pemunyaBaru->nama ?? '-' }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <div class="text-[11px] text-slate-500">No. Kad Pengenalan:</div>
                            <div class="font-mono font-bold text-blue-950">{{ $pindahMilik->pemunyaBaru->no_kp ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-[11px] text-slate-500">No. Telefon:</div>
                            <div class="font-medium text-slate-800">{{ $pindahMilik->pemunyaBaru->no_telefon ?? '-' }}</div>
                        </div>
                    </div>
                    <div>
                        <div class="text-[11px] text-slate-500">Alamat & Jajahan:</div>
                        <div class="text-slate-800">{{ $pindahMilik->pemunyaBaru->alamat ?? '-' }}, Jajahan {{ $pindahMilik->pemunyaBaru->jajahan ?? '-' }}</div>
                    </div>
                </div>

            </div>

            <!-- 3. Butiran Transaksi -->
            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200">
                <span class="text-[10px] font-black uppercase text-blue-900 tracking-wider">4. BUTIRAN TRANSAKSI & SYARAT PEMINDAHAN</span>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-3">
                    <div>
                        <div class="text-[11px] text-slate-500">Tarikh Pindah Milik:</div>
                        <div class="font-bold text-slate-900">{{ $pindahMilik->tarikh_pindah ? $pindahMilik->tarikh_pindah->format('d/m/Y') : '-' }}</div>
                    </div>
                    <div>
                        <div class="text-[11px] text-slate-500">Sebab Pindah:</div>
                        <div class="font-bold text-slate-900">{{ $pindahMilik->sebab_pindah }}</div>
                    </div>
                    <div>
                        <div class="text-[11px] text-slate-500">Harga Jualan:</div>
                        <div class="font-mono font-bold text-slate-900">{{ $pindahMilik->harga_jualan ? 'RM ' . number_format($pindahMilik->harga_jualan, 2) : 'Tiada / Bukan Jualan' }}</div>
                    </div>
                    <div>
                        <div class="text-[11px] text-slate-500">Status Kelulusan:</div>
                        <div class="font-bold {{ $pindahMilik->status_kelulusan === 'Diluluskan' ? 'text-emerald-700' : ($pindahMilik->status_kelulusan === 'Ditolak' ? 'text-rose-700' : 'text-amber-700') }}">
                            {{ $pindahMilik->status_kelulusan }}
                        </div>
                    </div>
                </div>

                <div class="mt-3 pt-3 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] text-slate-500 font-bold uppercase">Resit Pembayaran Fi:</span>
                        @if($pindahMilik->resit_pembayaran)
                            <a href="{{ asset('storage/' . $pindahMilik->resit_pembayaran) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-800 font-bold border border-blue-300 text-[11px] transition shadow-xs">
                                <i class="fa-solid fa-receipt text-blue-600"></i>
                                <span>Lihat Resit Pembayaran</span>
                            </a>
                        @else
                            <span class="inline-flex items-center gap-1 text-slate-400 italic text-[11px]">
                                <i class="fa-solid fa-circle-info"></i> Tiada Salinan Resit / Kaunter
                            </span>
                        @endif
                    </div>
                </div>

                @if($pindahMilik->catatan)
                    <div class="mt-3 pt-3 border-t border-slate-200">
                        <div class="text-[11px] text-slate-500">Catatan / Keterangan:</div>
                        <div class="text-slate-800">{{ $pindahMilik->catatan }}</div>
                    </div>
                @endif
            </div>

        </div>

        <!-- Official Footer Note -->
        <div class="mt-6 pt-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between text-[10px] font-semibold text-slate-600">
            <div>Borang B dikeluarkan di bawah Enakmen Pendaftaran Ternakan Ruminan 2024.</div>
            <div class="mt-1 sm:mt-0 font-mono">Pendaftar Ternakan JPVNK</div>
        </div>

    </div>

</div>
@endsection
