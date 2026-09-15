@extends('layouts.app')

@section('title', 'Maklumat Permit Sembelihan & SKV - ' . $permit->no_permit)
@section('page_title', 'Permit Sembelihan & Sijil Kesihatan Veterinar (SKV) Sembelih')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Navigation & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('eptr.borang-d.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-slate-900 bg-white px-3.5 py-2 rounded-xl border border-slate-200 transition shadow-2xs">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Senarai Borang D</span>
        </a>

        <div class="flex flex-wrap items-center gap-2">
            @if(Auth::user()->isStaff() && $permit->status_kelulusan === 'Menunggu')
                <!-- Butang Luluskan Permit -->
                <form action="{{ route('eptr.borang-d.lulus', $permit->id) }}" method="POST" class="inline" onsubmit="return confirm('Adakah anda pasti untuk MELULUSKAN permohonan Borang D & SKV Sembelih ini?');">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black shadow-lg shadow-emerald-700/20 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Luluskan Permit & SKV</span>
                    </button>
                </form>

                <!-- Butang Tolak Permit -->
                <form action="{{ route('eptr.borang-d.tolak', $permit->id) }}" method="POST" class="inline" onsubmit="return confirm('Tolak permohonan permit sembelihan ini?');">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-xmark"></i>
                        <span>Tolak</span>
                    </button>
                </form>
            @endif

            @if(Auth::user()->isStaff() && $permit->status_kelulusan === 'Diluluskan')
                <a href="{{ route('eptr.borang-d.cetak-skv', $permit->id) }}" target="_blank" class="px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                    <i class="fa-solid fa-file-shield"></i>
                    <span>Cetak SKV Sembelih (2 Halaman)</span>
                </a>
                <a href="{{ route('eptr.borang-d.cetak-lengkap', $permit->id) }}" target="_blank" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                    <i class="fa-solid fa-print text-amber-400"></i>
                    <span>Cetak Set Lengkap (Borang D + SKV)</span>
                </a>
                <a href="{{ route('eptr.borang-d.cetak', $permit->id) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-xs transition flex items-center gap-1.5">
                    <i class="fa-solid fa-file-invoice"></i>
                    <span>Borang D Sahaja</span>
                </a>
            @endif
        </div>
    </div>

    @php
        $pemunya = $permit->pemunya;
        $items = $permit->senarai_ternakan_list;
        $tarikhSembelihStr = $permit->tarikh_sembelih ? $permit->tarikh_sembelih->format('d/m/Y') : '-';
        $tarikhTamatStr = $permit->tarikh_tamat ? $permit->tarikh_tamat->format('d/m/Y') : ($permit->tarikh_sembelih ? \Carbon\Carbon::parse($permit->tarikh_sembelih)->addDays(6)->format('d/m/Y') : '-');
        $isExpired = $permit->isExpired();
    @endphp

    <!-- Permit & SKV Summary Container -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs text-xs space-y-6">
        
        <!-- Header with Status Badge -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-100 gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-700 flex items-center justify-center text-2xl font-bold">
                    <i class="fa-solid fa-file-invoice"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-mono font-black text-slate-900 text-base">{{ $permit->no_permit }}</span>
                        @if($permit->status_kelulusan === 'Diluluskan')
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <i class="fa-solid fa-circle-check text-emerald-500 mr-1"></i> Diluluskan
                            </span>
                        @elseif($permit->status_kelulusan === 'Ditolak')
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                <i class="fa-solid fa-circle-xmark text-rose-500 mr-1"></i> Ditolak
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                <i class="fa-solid fa-clock text-amber-500 mr-1"></i> Menunggu Kelulusan
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Ruj. SKV: <strong class="font-mono text-slate-700">{{ $permit->no_rujukan_skv ?: '-' }}</strong> &bull; Ruj. Karkas: <strong class="font-mono text-slate-700">{{ $permit->no_rujukan_karkas ?: '-' }}</strong></p>
                </div>
            </div>

            <!-- Tempoh Sah Laku 7 Hari Badge -->
            <div class="p-3 rounded-2xl {{ $isExpired ? 'bg-rose-50 border border-rose-200 text-rose-900' : 'bg-emerald-50 border border-emerald-200 text-emerald-900' }} flex items-center gap-3">
                <i class="fa-solid fa-calendar-check text-xl {{ $isExpired ? 'text-rose-600' : 'text-emerald-600' }}"></i>
                <div>
                    <div class="text-[10px] uppercase font-bold text-slate-500">Tempoh Sah Laku SKV (7 Hari)</div>
                    <div class="font-bold text-xs font-mono">{{ $tarikhSembelihStr }} hingga {{ $tarikhTamatStr }}</div>
                    @if($isExpired)
                        <span class="text-[10px] font-bold text-rose-600">Telah Tamat Tempoh</span>
                    @else
                        <span class="text-[10px] font-bold text-emerald-700">Masih Sah Berkuat Kuasa</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Details 2 Columns: Pemunya & Penyembelihan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            
            <!-- Maklumat Pemunya -->
            <div class="space-y-2.5 bg-slate-50 p-4 rounded-2xl border border-slate-200/80">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block border-b border-slate-200 pb-1">
                    MAKLUMAT PEMUNYA TERNAKAN
                </span>
                <div class="grid grid-cols-12 gap-1">
                    <div class="col-span-4 text-slate-500">Nama:</div>
                    <div class="col-span-8 font-bold text-slate-900 uppercase">{{ $pemunya->nama ?? '-' }}</div>
                </div>
                <div class="grid grid-cols-12 gap-1">
                    <div class="col-span-4 text-slate-500">No. KP:</div>
                    <div class="col-span-8 font-mono font-bold text-slate-800">{{ $pemunya->no_kp ?? '-' }}</div>
                </div>
                <div class="grid grid-cols-12 gap-1">
                    <div class="col-span-4 text-slate-500">No. Telefon:</div>
                    <div class="col-span-8 font-medium text-slate-800">{{ $pemunya->no_telefon ?? '-' }}</div>
                </div>
                <div class="grid grid-cols-12 gap-1">
                    <div class="col-span-4 text-slate-500">Jajahan & Alamat:</div>
                    <div class="col-span-8 text-slate-700">{{ $pemunya->alamat ?? '-' }}, {{ $pemunya->jajahan ?? '-' }}</div>
                </div>
            </div>

            <!-- Maklumat SKV & Permit -->
            <div class="space-y-2.5 bg-slate-50 p-4 rounded-2xl border border-slate-200/80">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block border-b border-slate-200 pb-1">
                    BUTIRAN PERMIT & KADAR BAYARAN
                </span>
                <div class="grid grid-cols-12 gap-1">
                    <div class="col-span-5 text-slate-500">Jenis Ternakan:</div>
                    <div class="col-span-7 font-bold text-slate-900 uppercase">{{ $permit->jenis_ternakan ?? 'Lembu' }}</div>
                </div>
                <div class="grid grid-cols-12 gap-1">
                    <div class="col-span-5 text-slate-500">Tujuan Sembelih:</div>
                    <div class="col-span-7 font-bold text-amber-900">{{ $permit->tujuan_sembelih }}</div>
                </div>
                <div class="grid grid-cols-12 gap-1">
                    <div class="col-span-5 text-slate-500">No. Kenderaan:</div>
                    <div class="col-span-7 font-mono font-bold text-slate-800 uppercase">{{ $permit->no_kenderaan ?: 'TIADA' }}</div>
                </div>
                @php
                    $isKecil = in_array(strtolower($permit->jenis_ternakan ?? 'lembu'), ['kambing', 'biri-biri', 'biri']);
                    $ratePerEkor = $isKecil ? 5.00 : 10.00;
                    $itemCount = count($items) > 0 ? count($items) : 1;
                    $fiSkv = 10.00;
                    
                    $freeCount = 0;
                    $paidCount = 0;
                    
                    if ($permit->is_musim_korban) {
                        $hariPercuma = $permit->hari_korban_percuma;
                        foreach ($items as $it) {
                            $hariItem = $it['hari_sembelihan_korban'] ?? null;
                            if (!empty($hariPercuma) && $hariItem === $hariPercuma) {
                                $freeCount++;
                            } else {
                                $paidCount++;
                            }
                        }
                    } else {
                        $paidCount = $itemCount;
                    }
                    
                    $fiPembatalan = $paidCount * $ratePerEkor;
                    $totalFiPermit = $fiSkv + $fiPembatalan;
                @endphp
                <div class="grid grid-cols-12 gap-1">
                    <div class="col-span-5 text-slate-500">Kadar Fi Sembelih &amp; SKV:</div>
                    <div class="col-span-7 font-bold text-emerald-700">
                        RM {{ number_format($totalFiPermit, 2) }} 
                        <span class="text-[10px] font-normal text-slate-500 block">
                            @if($permit->is_musim_korban)
                                (1 Sijil SKV: RM 10.00 + Pembatalan: {{ $paidCount }} ekor berbayar &times; RM {{ number_format($ratePerEkor, 2) }}{{ $freeCount > 0 ? ' • ' . $freeCount . ' ekor percuma ' . ($permit->hari_korban_percuma ?? 'Hari Raya Pertama') : '' }})
                            @else
                                (1 Sijil SKV: RM 10.00 + Pembatalan: {{ $itemCount }} ekor &times; RM {{ number_format($ratePerEkor, 2) }})
                            @endif
                        </span>
                    </div>
                </div>
                <div class="grid grid-cols-12 gap-1">
                    <div class="col-span-5 text-slate-500">Kategori Borang:</div>
                    <div class="col-span-7 font-medium text-slate-700">
                        @if($permit->is_musim_korban)
                            <span class="inline-flex items-center gap-1 text-red-700 font-bold">
                                <i class="fa-solid fa-moon"></i> Musim Hari Raya Korban (Had 10 Baris)
                            </span>
                            @if($permit->hari_korban_percuma)
                                <span class="text-[11px] text-emerald-700 block font-semibold">
                                    <i class="fa-solid fa-gift"></i> Pengecualian Percuma: {{ $permit->hari_korban_percuma }}
                                </span>
                            @else
                                <span class="text-[11px] text-amber-700 block font-semibold">
                                    <i class="fa-solid fa-receipt"></i> Sembelihan Hari Tasyrik (Tiada Pengecualian Percuma)
                                </span>
                            @endif
                        @else
                            <span>Hari Biasa (Had 7 Baris)</span>
                        @endif
                    </div>
                </div>
                <div class="grid grid-cols-12 gap-1 pt-1 border-t border-slate-200/60 items-center">
                    <div class="col-span-5 text-slate-500">Resit Pembayaran:</div>
                    <div class="col-span-7">
                        @if($permit->resit_pembayaran)
                            <a href="{{ asset('storage/' . $permit->resit_pembayaran) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold border border-emerald-300 text-[11px] transition shadow-xs">
                                <i class="fa-solid fa-receipt text-emerald-600"></i>
                                <span>Lihat Resit Pembayaran</span>
                            </a>
                        @else
                            <span class="inline-flex items-center gap-1 text-slate-400 italic text-[11px]">
                                <i class="fa-solid fa-circle-info"></i> Resit di Kaunter JPVNK
                            </span>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <!-- Jadual Ternakan SKV Sembelih -->
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <h4 class="font-bold text-slate-900 text-sm">Senarai Ternakan SKV Sembelih ({{ count($items) }} Ekor)</h4>
                <span class="text-xs text-slate-500 font-medium">Had Maksimum: {{ $permit->max_rows }} Baris</span>
            </div>

            <div class="overflow-x-auto border border-slate-200 rounded-2xl">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold uppercase text-[10px]">
                        <tr>
                            <th class="p-3 w-10 text-center">BIL</th>
                            <th class="p-3">Jantina</th>
                            <th class="p-3">No. ID Ternakan (Tag)</th>
                            <th class="p-3">No. Siri Kad Pendaftaran</th>
                            <th class="p-3">{{ $permit->is_musim_korban ? 'Tarikh / Hari Korban' : 'Tarikh Sembelihan' }}</th>
                            <th class="p-3">Tempat Sembelihan</th>
                            <th class="p-3">Kuantiti Karkas</th>
                            <th class="p-3">No Kn. Haiwan 16</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($items as $idx => $it)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="p-3 text-center font-bold text-slate-500">{{ $idx + 1 }}</td>
                                <td class="p-3 font-bold">{{ $it['jantina'] ?? '-' }}</td>
                                <td class="p-3 font-mono font-bold text-emerald-800">
                                    @if(!empty($it['ternakan_id']))
                                        <a href="{{ route('eptr.show', $it['ternakan_id']) }}" class="hover:underline flex items-center gap-1">
                                            <i class="fa-solid fa-tag text-emerald-600"></i>
                                            <span>{{ $it['no_id_ternakan'] }}</span>
                                        </a>
                                    @else
                                        <span>{{ $it['no_id_ternakan'] ?? '-' }}</span>
                                    @endif
                                </td>
                                <td class="p-3 font-mono text-slate-600">{{ $it['no_siri_kad_pendaftaran'] ?? '-' }}</td>
                                <td class="p-3">
                                    @if($permit->is_musim_korban)
                                        <div>
                                            <strong class="text-slate-900 block">{{ $it['hari_sembelihan_korban'] ?? ($permit->hari_korban_percuma ?: 'Hari Raya Korban') }}</strong>
                                            @php
                                                $isItemFree = !empty($permit->hari_korban_percuma) && ($it['hari_sembelihan_korban'] ?? '') === $permit->hari_korban_percuma;
                                            @endphp
                                            @if($isItemFree)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold text-[9px]">
                                                    <i class="fa-solid fa-gift"></i> PERCUMA (RM 0)
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-amber-100 text-amber-900 font-bold text-[9px]">
                                                    <i class="fa-solid fa-receipt"></i> BERBAYAR
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="font-mono">{{ !empty($it['tarikh_sembelihan']) ? \Carbon\Carbon::parse($it['tarikh_sembelihan'])->format('d/m/Y') : $tarikhSembelihStr }}</span>
                                    @endif
                                </td>
                                <td class="p-3 uppercase text-slate-700">{{ $it['tempat_sembelihan'] ?? '-' }}</td>
                                <td class="p-3 font-medium text-slate-700">{{ $it['kuantiti_karkas'] ?? '-' }}</td>
                                <td class="p-3 font-mono text-slate-500">{{ $it['no_kn_haiwan_16'] ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-6 text-center text-slate-400">Tiada butiran ternakan diisi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Maklumat Agihan Karkas (Alamat 1, 2, 3) -->
        @if($permit->alamat_1 || $permit->alamat_2 || $permit->alamat_3)
            <div class="space-y-2 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block border-b border-slate-200 pb-1">
                    MAKLUMAT PEMINDAHAN & AGIHAN KARKAS
                </span>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                    @if($permit->alamat_1)
                        <div class="p-2.5 rounded-xl bg-white border border-slate-200">
                            <strong class="text-slate-800 block mb-0.5">A. Alamat 1:</strong>
                            <div class="text-slate-600">{{ $permit->alamat_1 }}</div>
                            <div class="text-[11px] text-emerald-700 font-bold mt-1">Kuantiti: {{ $permit->kuantiti_karkas_1 ?: '-' }}</div>
                        </div>
                    @endif
                    @if($permit->alamat_2)
                        <div class="p-2.5 rounded-xl bg-white border border-slate-200">
                            <strong class="text-slate-800 block mb-0.5">B. Alamat 2:</strong>
                            <div class="text-slate-600">{{ $permit->alamat_2 }}</div>
                            <div class="text-[11px] text-emerald-700 font-bold mt-1">Kuantiti: {{ $permit->kuantiti_karkas_2 ?: '-' }}</div>
                        </div>
                    @endif
                    @if($permit->alamat_3)
                        <div class="p-2.5 rounded-xl bg-white border border-slate-200">
                            <strong class="text-slate-800 block mb-0.5">C. Alamat 3:</strong>
                            <div class="text-slate-600">{{ $permit->alamat_3 }}</div>
                            <div class="text-[11px] text-emerald-700 font-bold mt-1">Kuantiti: {{ $permit->kuantiti_karkas_3 ?: '-' }}</div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Maklumat Kelulusan & Catatan -->
        @if($permit->diluluskan_oleh || $permit->catatan)
            <div class="pt-2 border-t border-slate-100 flex flex-col sm:flex-row justify-between text-slate-500 text-[11px] gap-2">
                <div>
                    @if($permit->pelulus)
                        <span>Diproses oleh: <strong class="text-slate-800">{{ $permit->pelulus->nama ?? $permit->pelulus->name }}</strong></span>
                    @endif
                </div>
                @if($permit->catatan)
                    <div class="text-slate-600 italic">
                        Catatan: {{ $permit->catatan }}
                    </div>
                @endif
            </div>
        @endif

    </div>

</div>
@endsection
