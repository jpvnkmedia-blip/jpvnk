@extends('layouts.app')

@section('title', 'Maklumat Perjanjian Pawah - ' . $perjanjian->no_perjanjian)
@section('page_title', 'Perjanjian Lembu Pawah: ' . $perjanjian->no_perjanjian)

@section('content')
<div class="space-y-6" x-data="{ modalKelahiran: false, modalKesihatan: false, modalPenyelesaian: false, modalLulus: false, modalTolak: false, modalPautLembu: false }">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <a href="{{ route('pawah.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 bg-white px-3.5 py-2 rounded-xl border border-slate-200 transition inline-flex items-center gap-1.5 w-fit">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Senarai Pawah</span>
        </a>
        <div class="flex flex-wrap items-center gap-2">
            @if($perjanjian->status === 'Menunggu Kelulusan' && Auth::user()->isStaff())
                <button @click="modalLulus = true" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                    <i class="fa-solid fa-check-circle"></i> Luluskan Permohonan &amp; Pautkan Lembu
                </button>
                <button @click="modalTolak = true" class="px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                    <i class="fa-solid fa-ban"></i> Tolak Permohonan
                </button>
            @endif

            @if($perjanjian->status === 'Aktif' && Auth::user()->isStaff())
                <button @click="modalPautLembu = true" class="px-3.5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                    <i class="fa-solid fa-link"></i> Pautkan Induk EPTR
                </button>
                <button @click="modalKelahiran = true" class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs shadow-md transition flex items-center gap-1.5">
                    <i class="fa-solid fa-plus-circle"></i> Tambah Kelahiran Anak
                </button>
                <button @click="modalKesihatan = true" class="px-3.5 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-700 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                    <i class="fa-solid fa-stethoscope"></i> Rekod Rawatan/Kesihatan
                </button>
                <button @click="modalPenyelesaian = true" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                    <i class="fa-solid fa-check-double"></i> Sahkan Penyelesaian
                </button>
            @endif

            @if($perjanjian->status === 'Aktif' || $perjanjian->status === 'Selesai')
            <a href="{{ route('pawah.cetak-perjanjian', $perjanjian->id) }}" target="_blank" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                <i class="fa-solid fa-print text-amber-400"></i> Cetak Surat Perjanjian
            </a>
            @endif
        </div>
    </div>

    @if($perjanjian->status === 'Menunggu Kelulusan')
    <div class="p-4 sm:p-5 rounded-3xl bg-amber-50 border-2 border-amber-300 text-amber-950 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-xs">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-2xl bg-amber-200 text-amber-800 flex items-center justify-center text-lg shrink-0">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <h4 class="font-bold text-sm text-amber-900">Permohonan Program Pawah Sedang Disemak</h4>
                <p class="text-xs text-amber-800/90 mt-0.5">
                    @if(Auth::user()->isStaff())
                        Permohonan ini dihantar oleh pemohon dan sedang menunggu semakan, lawatan tapak dan kelulusan daripada Pegawai Pawah JPVNK.
                    @else
                        Permohonan anda telah berjaya dihantar ke sistem JPVNK. Pegawai Veterinar Jajahan akan membuat semakan dan menghubungi anda untuk lawatan tapak kandang sebelum kelulusan diberikan.
                    @endif
                </p>
            </div>
        </div>
        @if(Auth::user()->isStaff())
        <div class="flex items-center gap-2 shrink-0">
            <button @click="modalLulus = true" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition">
                Luluskan Sekarang
            </button>
        </div>
        @endif
    </div>
    @elseif($perjanjian->status === 'Ditolak')
    <div class="p-4 sm:p-5 rounded-3xl bg-rose-50 border-2 border-rose-300 text-rose-950 flex items-start gap-3 shadow-xs">
        <div class="w-10 h-10 rounded-2xl bg-rose-200 text-rose-800 flex items-center justify-center text-lg shrink-0">
            <i class="fa-solid fa-circle-xmark"></i>
        </div>
        <div>
            <h4 class="font-bold text-sm text-rose-900">Permohonan Program Pawah Telah Ditolak</h4>
            <p class="text-xs text-rose-800/90 mt-0.5">{{ $perjanjian->catatan ?? 'Permohonan tidak memenuhi kriteria tapak/syarat Program Pawah.' }}</p>
        </div>
    </div>
    @endif

    <!-- Contract Summary Card -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 mb-6 border-b border-slate-100 gap-4">
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="font-mono text-lg font-black text-emerald-800">{{ $perjanjian->no_perjanjian }}</span>
                    <span class="px-3 py-0.5 rounded-full text-xs font-bold {{ $perjanjian->status === 'Aktif' ? 'bg-emerald-100 text-emerald-800' : ($perjanjian->status === 'Menunggu Kelulusan' ? 'bg-amber-100 text-amber-900 border border-amber-300' : ($perjanjian->status === 'Ditolak' ? 'bg-rose-100 text-rose-800' : 'bg-blue-100 text-blue-800')) }}">
                        {{ $perjanjian->status }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-teal-100 text-teal-800 border border-teal-200">
                        <i class="fa-solid fa-tag"></i> {{ $perjanjian->jenis_pawah ?? 'Lembu Hibrid' }}
                    </span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-900 mt-1">{{ $perjanjian->nama_program }}</h2>
                <p class="text-xs text-slate-500">Jajahan: {{ $perjanjian->jajahan }} &bull; Penyelia: {{ $perjanjian->pegawai_penyelia ?? '-' }} &bull; Ternakan Sedia Ada: <span class="font-bold text-slate-700">{{ $perjanjian->bilangan_ternakan_sedia_ada ?? 0 }} Ekor {{ $perjanjian->jenis_ternakan_sedia_ada ? '(' . $perjanjian->jenis_ternakan_sedia_ada . ')' : '' }}</span> &bull; Rekod EPTR: <span class="font-bold text-emerald-700">{{ $ternakanPemohonEptr->count() }} Ekor</span></p>
            </div>
            <div class="text-right sm:text-right text-xs">
                <div class="text-slate-400 font-medium">Tempoh Kontrak:</div>
                <div class="font-bold text-slate-900 text-sm">{{ $perjanjian->tarikh_mula ? $perjanjian->tarikh_mula->format('d/m/Y') : '-' }} &rarr; {{ $perjanjian->tarikh_tamat ? $perjanjian->tarikh_tamat->format('d/m/Y') : '-' }}</div>
                <div class="text-emerald-700 font-semibold mt-0.5">Tempoh: {{ $perjanjian->tempoh_tahun }} Tahun</div>
            </div>
        </div>

        <!-- Participants & Cattle Details -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 text-xs">
            
            <!-- Peserta -->
            <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 space-y-3">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">MAKLUMAT PESERTA PENERIMA</span>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <div class="text-slate-400 text-[11px]">Nama Peserta:</div>
                        <div class="font-bold text-slate-900 text-sm">{{ $perjanjian->peserta->name ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-slate-400 text-[11px]">No. Kad Pengenalan:</div>
                        <div class="font-mono font-bold text-slate-900">{{ $perjanjian->peserta->ic_number ?? '-' }}</div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <div class="text-slate-400 text-[11px]">No. Telefon:</div>
                        <div class="font-medium text-slate-800">{{ $perjanjian->peserta->phone ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-slate-400 text-[11px]">Emel:</div>
                        <div class="font-medium text-slate-800">{{ $perjanjian->peserta->email ?? '-' }}</div>
                    </div>
                </div>
                <div>
                    <div class="text-slate-400 text-[11px]">Alamat Peserta:</div>
                    <div class="font-medium text-slate-800">{{ $perjanjian->peserta->address ?? '-' }}</div>
                </div>
            </div>

            <!-- Syarat Pemulangan -->
            <div class="bg-emerald-50/50 p-5 rounded-2xl border border-emerald-200/70 space-y-3">
                <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">SYARAT & TERMA PEMULANGAN ANAK</span>
                <p class="text-slate-700 leading-relaxed text-xs">
                    {{ $perjanjian->syarat_pemulangan }}
                </p>
                @php
                    $butiran = $perjanjian->butiran_permohonan;
                    $hasTernakanInfo = $butiran['pengalaman'] || $butiran['jenis_ternakan_sedia_ada'] || ($butiran['bilangan_ternakan_sedia_ada'] > 0) || $butiran['keluasan_ragut'] || $butiran['jenis_kandang'] || $butiran['sumber_makanan'];
                @endphp
                @if(!$hasTernakanInfo && !empty($butiran['catatan_tambahan']))
                    <div class="pt-2 border-t border-emerald-200/50 text-[11px] text-slate-600">
                        <b>Catatan Tambahan:</b> {{ $butiran['catatan_tambahan'] }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- Maklumat Permohonan, Pengalaman Menternak & Fasiliti Tapak -->
    @if($hasTernakanInfo || $butiran['is_permohonan_awam'])
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-wheat-awn"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Maklumat Permohonan: Ternakan Sedia Ada & Fasiliti Tapak</h3>
                    <p class="text-xs text-slate-500">Profil pengalaman menternak dan kesediaan fasiliti tapak projek pemohon</p>
                </div>
            </div>
            @if($butiran['pengalaman'])
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-900 border border-emerald-300 self-start sm:self-auto">
                    <i class="fa-solid fa-award text-emerald-700"></i>
                    Pengalaman Menternak: {{ $butiran['pengalaman'] }}
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200 self-start sm:self-auto">
                    <i class="fa-solid fa-circle-info text-slate-500"></i>
                    Penternak Baharu / Tiada Pengalaman Lepas
                </span>
            @endif
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Ternakan Sedia Ada -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col justify-between space-y-2">
                <div class="flex items-center justify-between text-slate-400">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Ternakan Sedia Ada</span>
                    <i class="fa-solid fa-cow text-emerald-600 text-sm"></i>
                </div>
                <div class="text-sm font-extrabold text-slate-900">
                    @if($butiran['bilangan_ternakan_sedia_ada'] > 0 || $butiran['jenis_ternakan_sedia_ada'])
                        <div class="text-emerald-900 text-base font-black">{{ $butiran['bilangan_ternakan_sedia_ada'] }} Ekor</div>
                        <div class="text-xs font-medium text-slate-600 mt-0.5">{{ $butiran['jenis_ternakan_sedia_ada'] ?? 'Ternakan Am' }}</div>
                    @else
                        <span class="text-xs font-semibold text-slate-500">Tiada Ternakan Sedia Ada</span>
                    @endif
                </div>
            </div>

            <!-- Keluasan Padang Ragut -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col justify-between space-y-2">
                <div class="flex items-center justify-between text-slate-400">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Padang Ragut / Rumput</span>
                    <i class="fa-solid fa-mountain-sun text-emerald-600 text-sm"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-900 leading-snug">
                        {{ $butiran['keluasan_ragut'] ?? 'Tiada Maklumat Padang Ragut' }}
                    </div>
                </div>
            </div>

            <!-- Jenis & Keadaan Kandang -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col justify-between space-y-2">
                <div class="flex items-center justify-between text-slate-400">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Jenis & Keadaan Kandang</span>
                    <i class="fa-solid fa-warehouse text-emerald-600 text-sm"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-900 leading-snug">
                        {{ $butiran['jenis_kandang'] ?? 'Tiada Maklumat Kandang' }}
                    </div>
                </div>
            </div>

            <!-- Sumber Makanan & Air -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col justify-between space-y-2">
                <div class="flex items-center justify-between text-slate-400">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Sumber Makanan & Air</span>
                    <i class="fa-solid fa-faucet-drip text-emerald-600 text-sm"></i>
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-900 leading-snug">
                        {{ $butiran['sumber_makanan'] ?? 'Tiada Maklumat Bekalan Air' }}
                    </div>
                </div>
            </div>
        </div>

        @if($butiran['catatan_tambahan'])
        <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200 text-xs">
            <div class="font-bold text-amber-900 flex items-center gap-1.5 mb-1">
                <i class="fa-solid fa-comment-dots text-amber-700"></i>
                Catatan / Justifikasi Tambahan Pemohon:
            </div>
            <div class="text-slate-800 font-medium leading-relaxed">
                {{ $butiran['catatan_tambahan'] }}
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- 1. Lembu Induk Skim Pawah Dipautkan Dari EPTR -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-cow"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Senarai Lembu Induk Skim Pawah Dipautkan Dari EPTR</h3>
                    <p class="text-xs text-slate-500">Ternakan induk skim pawah jabatan yang diserahkan dan dipautkan di bawah surat perjanjian ini</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold {{ $perjanjian->ternakanList->count() > 0 ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-slate-100 text-slate-700 border border-slate-300' }} px-3.5 py-1.5 rounded-full">
                    {{ $perjanjian->ternakanList->count() }} Ekor Induk Pawah
                </span>
                @if(Auth::user()->isStaff() && $perjanjian->status === 'Aktif')
                    <button @click="modalPautLembu = true" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-xs flex items-center gap-1.5">
                        <i class="fa-solid fa-plus-circle"></i> Pautkan Induk EPTR
                    </button>
                @endif
            </div>
        </div>

        @if($perjanjian->ternakanList->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($perjanjian->ternakanList as $lembu)
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-3">
                        <div class="space-y-1">
                            <div class="font-mono font-black text-sm text-emerald-900 flex items-center gap-2">
                                <span>{{ $lembu->no_tag }}</span>
                                <span class="text-[10px] font-sans font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">{{ $lembu->baka }}</span>
                            </div>
                            <div class="text-xs text-slate-600">
                                Jantina: <b>{{ $lembu->jantina }}</b> &bull; Warna: {{ $lembu->warna ?? '-' }} &bull; Umur: {{ $lembu->umur ?? '-' }}
                            </div>
                            <div class="text-[11px] text-slate-500">
                                Pemunya: {{ $lembu->pemunya->nama ?? '-' }} &bull; Lokasi: {{ $lembu->lokasi_kandang ?? $lembu->jajahan }}
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <a href="{{ route('eptr.show', $lembu->id) }}" class="p-2 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:text-emerald-700 shadow-2xs transition inline-flex items-center gap-1" title="Buka Kad Kuning EPTR">
                                <i class="fa-solid fa-id-card"></i> <span class="hidden sm:inline">Kad Kuning</span>
                            </a>
                            @if(Auth::user()->isStaff() && $perjanjian->status === 'Aktif')
                                <form action="{{ route('pawah.padam-pautan-ternakan', ['id' => $perjanjian->id, 'ternakanId' => $lembu->id]) }}" method="POST" onsubmit="return confirm('Adakah anda pasti untuk batalkan pautan induk ini?');" class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-xl bg-white border border-rose-200 text-rose-600 hover:bg-rose-50 text-xs font-bold shadow-2xs transition" title="Batalkan Pautan">
                                        <i class="fa-solid fa-unlink"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-5 rounded-2xl bg-amber-50/60 border border-amber-200 text-xs space-y-2">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-sm shrink-0 mt-0.5">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div class="space-y-1">
                        <h4 class="font-bold text-amber-950 text-sm">Belum Mempunyai Induk Skim Pawah Dipautkan (0 Ekor)</h4>
                        @if($perjanjian->status === 'Menunggu Kelulusan')
                            <p class="text-slate-700 leading-relaxed">
                                Permohonan ini masih dalam status <b>Menunggu Kelulusan</b>. Induk lembu pawah daripada simpanan jabatan belum diagihkan secara rasmi kepada pemohon.
                            </p>
                            @if(Auth::user()->isStaff())
                                <div class="pt-2">
                                    <button @click="modalLulus = true" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-check-circle"></i> Luluskan Permohonan &amp; Pilih Lembu Induk EPTR
                                    </button>
                                </div>
                            @endif
                        @else
                            <p class="text-slate-700 leading-relaxed">
                                Tiada lembu induk dipautkan ke dalam surat perjanjian ini. Pegawai boleh memautkan lembu induk betina aktif daripada sistem EPTR.
                            </p>
                            @if(Auth::user()->isStaff())
                                <div class="pt-2">
                                    <button @click="modalPautLembu = true" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-plus-circle"></i> Pautkan Lembu Induk Daripada EPTR Sekarang
                                    </button>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- 2. Ternakan Sedia Ada Pemohon Di Bawah EPTR (Kad Kuning) -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-id-card-clip"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Ternakan Sedia Ada Pemohon Di Bawah EPTR (Kad Kuning)</h3>
                    <p class="text-xs text-slate-500">Senarai keseluruhan rekod ternakan milik pemohon yang didaftarkan di dalam sistem EPTR</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold {{ $ternakanPemohonEptr->count() > 0 ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-slate-100 text-slate-700 border border-slate-300' }} px-3.5 py-1.5 rounded-full">
                    {{ $ternakanPemohonEptr->count() }} Ekor Didaftarkan EPTR
                </span>
            </div>
        </div>

        @if($ternakanPemohonEptr->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($ternakanPemohonEptr as $lembuEptr)
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col justify-between space-y-3">
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-mono font-black text-sm text-slate-900">{{ $lembuEptr->no_tag }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $lembuEptr->status === 'Aktif' ? 'bg-emerald-100 text-emerald-800' : ($lembuEptr->status === 'Pawah' ? 'bg-teal-100 text-teal-800' : ($lembuEptr->status === 'Sembelih' ? 'bg-rose-100 text-rose-800' : 'bg-slate-200 text-slate-700')) }}">
                                    {{ $lembuEptr->status }}
                                </span>
                            </div>
                            <div class="text-xs text-slate-700">
                                <b>{{ $lembuEptr->baka }}</b> &bull; Jantina: <b>{{ $lembuEptr->jantina }}</b>
                            </div>
                            <div class="text-[11px] text-slate-500">
                                Warna: {{ $lembuEptr->warna ?? '-' }} &bull; Umur: {{ $lembuEptr->umur ?? '-' }} &bull; Jajahan: {{ $lembuEptr->jajahan ?? '-' }}
                            </div>
                            <div class="text-[11px] text-slate-500">
                                Lokasi: {{ $lembuEptr->lokasi_kandang ?? $lembuEptr->jajahan }}
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-slate-200 text-xs">
                            <a href="{{ route('eptr.show', $lembuEptr->id) }}" class="inline-flex items-center gap-1.5 text-emerald-800 font-bold hover:text-emerald-950 text-[11px]">
                                <i class="fa-solid fa-id-card"></i> Kad Kuning (EPTR)
                            </a>
                            @if(Auth::user()->isStaff() && $perjanjian->status === 'Aktif' && !$perjanjian->ternakanList->contains('id', $lembuEptr->id))
                                <form action="{{ route('pawah.paut-ternakan', $perjanjian->id) }}" method="POST" class="m-0">
                                    @csrf
                                    <input type="hidden" name="ternakan_ids[]" value="{{ $lembuEptr->id }}">
                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] transition shadow-2xs inline-flex items-center gap-1" title="Pautkan sebagai Induk Skim Pawah">
                                        <i class="fa-solid fa-link"></i> Pautkan
                                    </button>
                                </form>
                            @elseif($perjanjian->ternakanList->contains('id', $lembuEptr->id))
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                    <i class="fa-solid fa-check"></i> Induk Skim Ini
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-6 rounded-2xl bg-slate-50 border border-dashed border-slate-300 text-center text-xs text-slate-500 space-y-1">
                <i class="fa-solid fa-circle-info text-slate-400 text-base mb-1 block"></i>
                <div class="font-bold text-slate-700">Tiada Rekod Ternakan Ditemui Di Bawah EPTR</div>
                <p>Pemohon ini belum mempunyai pendaftaran ternakan (Kad Kuning) di bawah nama atau No. KP beliau di dalam modul EPTR.</p>
            </div>
        @endif
    </div>

    <!-- Kelahiran & Pemantauan Tabs -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Left: Rekod Kelahiran Anak Pawah -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-baby text-amber-500 text-base"></i>
                    <h3 class="text-sm font-bold text-slate-900">Rekod Kelahiran Anak Pawah</h3>
                </div>
                <span class="text-xs font-bold text-amber-800">{{ $perjanjian->rekodKelahiran->count() }} Kelahiran</span>
            </div>

            <div class="space-y-3">
                @forelse($perjanjian->rekodKelahiran as $anak)
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-mono font-bold text-amber-900">{{ $anak->no_tag_anak }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900">{{ $anak->status_anak }}</span>
                        </div>
                        <div class="text-slate-700">
                            Jantina: <b>{{ $anak->jantina_anak }}</b> &bull; Tarikh Lahir: {{ $anak->tarikh_kelahiran ? $anak->tarikh_kelahiran->format('d/m/Y') : '-' }} &bull; Berat: {{ $anak->berat_lahir_kg ? $anak->berat_lahir_kg . ' kg' : '-' }}
                        </div>
                        <div class="text-[11px] text-slate-500">
                            Baka Bapa: {{ $anak->baka_bapa ?? '-' }} &bull; Induk: {{ $anak->induk->no_tag ?? 'Induk EPTR' }}
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center text-slate-400 text-xs">
                        Belum ada rekod kelahiran anak pawah.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right: Rekod Pemantauan Kesihatan Veterinar -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-stethoscope text-cyan-600 text-base"></i>
                    <h3 class="text-sm font-bold text-slate-900">Pemantauan Kesihatan Berkala</h3>
                </div>
                <span class="text-xs font-bold text-cyan-800">{{ $perjanjian->rekodKesihatan->count() }} Lawatan</span>
            </div>

            <div class="space-y-3">
                @forelse($perjanjian->rekodKesihatan as $kes)
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-900">Tarikh: {{ $kes->tarikh_lawatan ? $kes->tarikh_lawatan->format('d/m/Y') : '-' }}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $kes->status_fizikal === 'Baik' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                Fizikal: {{ $kes->status_fizikal }}
                            </span>
                        </div>
                        <div class="text-slate-700 font-medium">{{ $kes->diagnosis ?? 'Pemeriksaan rutin' }}</div>
                        <div class="text-[11px] text-slate-500">
                            Rawatan: {{ $kes->rawatan_diberikan ?? 'Tiada' }} &bull; Pegawai: {{ $kes->pegawai_pemeriksa }}
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center text-slate-400 text-xs">
                        Belum ada rekod pemantauan kesihatan.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Settlement Status if completed -->
    @if($perjanjian->penyelesaian)
        <div class="bg-blue-50 border-2 border-blue-300 rounded-3xl p-6 text-xs text-blue-950 space-y-2">
            <div class="flex items-center gap-2 font-bold text-sm text-blue-900">
                <i class="fa-solid fa-certificate text-blue-600"></i>
                <span>Program Telah Selesai (Rekod Penyelesaian)</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                <div>
                    <span class="text-slate-500 text-[11px]">Tarikh Penyelesaian:</span>
                    <div class="font-bold">{{ $perjanjian->penyelesaian->tarikh_penyelesaian ? $perjanjian->penyelesaian->tarikh_penyelesaian->format('d/m/Y') : '-' }}</div>
                </div>
                <div>
                    <span class="text-slate-500 text-[11px]">Anak Dipulangkan:</span>
                    <div class="font-bold">{{ $perjanjian->penyelesaian->bilangan_anak_dipulangkan }} Ekor</div>
                </div>
                <div>
                    <span class="text-slate-500 text-[11px]">Pegawai Pengesah:</span>
                    <div class="font-bold">{{ $perjanjian->penyelesaian->pegawai_pengesah }}</div>
                </div>
            <p class="pt-2 text-slate-700 italic border-t border-blue-200">"{{ $perjanjian->penyelesaian->perakuan }}"</p>
            @if($perjanjian->penyelesaian->resit_pembayaran)
                <div class="pt-3 flex items-center justify-between border-t border-blue-200">
                    <div class="flex items-center gap-2 text-blue-900 font-bold">
                        <i class="fa-solid fa-receipt text-blue-600 text-sm"></i>
                        <span>Resit Bayaran Penyelesaian Disertakan:</span>
                    </div>
                    <a href="{{ asset('storage/' . $perjanjian->penyelesaian->resit_pembayaran) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-sm text-xs transition">
                        <i class="fa-solid fa-file-invoice"></i>
                        <span>Lihat / Muat Turun Resit</span>
                    </a>
                </div>
            @endif
        </div>
    @endif

    <!-- MODAL 1: Tambah Kelahiran Anak Pawah -->
    <div x-show="modalKelahiran" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl text-xs space-y-4" @click.outside="modalKelahiran = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-900">Daftar Kelahiran Anak Lembu Pawah</h3>
                <button @click="modalKelahiran = false" class="text-slate-400 hover:text-slate-600 text-base">&times;</button>
            </div>

            <form action="{{ route('pawah.kelahiran.store', $perjanjian->id) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Pilih Induk Lembu EPTR</label>
                    <select name="ternakan_induk_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                        @foreach($perjanjian->ternakanList as $t)
                            <option value="{{ $t->id }}">{{ $t->no_tag }} ({{ $t->baka }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Jantina Anak</label>
                        <select name="jantina_anak" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                            <option value="Betina">Betina (Anak Pawah)</option>
                            <option value="Jantan">Jantan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Kelahiran</label>
                        <input type="date" name="tarikh_kelahiran" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Berat Lahir (kg)</label>
                        <input type="number" step="0.1" name="berat_lahir_kg" placeholder="Contoh: 25.5" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Baka Bapa</label>
                        <input type="text" name="baka_bapa" placeholder="Contoh: Charolais / KK" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Warna & Tanda Khas Anak</label>
                    <input type="text" name="warna" placeholder="Contoh: Coklat tompok putih" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                </div>

                <div class="pt-3 flex justify-end gap-2">
                    <button type="button" @click="modalKelahiran = false" class="px-4 py-2 rounded-xl bg-slate-100 font-bold text-slate-600">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold shadow-md">Simpan Kelahiran</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: Rekod Kesihatan & Rawatan -->
    <div x-show="modalKesihatan" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl text-xs space-y-4" @click.outside="modalKesihatan = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-900">Rekod Pemeriksaan Kesihatan Lembu Pawah</h3>
                <button @click="modalKesihatan = false" class="text-slate-400 hover:text-slate-600 text-base">&times;</button>
            </div>

            <form action="{{ route('pawah.kesihatan.store', $perjanjian->id) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Pilih Ternakan</label>
                    <select name="ternakan_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                        @foreach($perjanjian->ternakanList as $t)
                            <option value="{{ $t->id }}">{{ $t->no_tag }} ({{ $t->baka }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Lawatan</label>
                        <input type="date" name="tarikh_lawatan" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Keadaan Fizikal</label>
                        <select name="status_fizikal" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                            <option value="Baik">Baik & Sihat</option>
                            <option value="Sederhana">Sederhana</option>
                            <option value="Kurus">Kurus</option>
                            <option value="Sakit">Sakit</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Status Bunting</label>
                    <select name="status_bunting" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                        <option value="0">Tidak Bunting / Selepas Beranak</option>
                        <option value="1">Bunting Disahkan</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Diagnosis & Penemuan</label>
                    <textarea name="diagnosis" rows="2" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl" placeholder="Catatan kesihatan"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Rawatan Diberikan</label>
                    <input type="text" name="rawatan_diberikan" placeholder="Cth: Suntikan vitamin & ubat cacing" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                </div>

                <div class="pt-3 flex justify-end gap-2">
                    <button type="button" @click="modalKesihatan = false" class="px-4 py-2 rounded-xl bg-slate-100 font-bold text-slate-600">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-700 text-white font-bold shadow-md">Simpan Rekod Kesihatan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: Pengesahan Penyelesaian Program Pawah -->
    <div x-show="modalPenyelesaian" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl text-xs space-y-4" @click.outside="modalPenyelesaian = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-900">Pengesahan Penyelesaian Kontrak Pawah</h3>
                <button @click="modalPenyelesaian = false" class="text-slate-400 hover:text-slate-600 text-base">&times;</button>
            </div>

            <form action="{{ route('pawah.penyelesaian.store', $perjanjian->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Penyelesaian Kontrak</label>
                    <input type="date" name="tarikh_penyelesaian" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Bil. Anak Dipulangkan</label>
                        <input type="number" name="bilangan_anak_dipulangkan" value="1" min="0" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Status Penyelesaian</label>
                        <select name="status_penyelesaian" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                            <option value="Selesai Penuh">Selesai Penuh</option>
                            <option value="Tebus Guna Tunai">Tebus Guna Tunai</option>
                            <option value="Ganti Rugi">Ganti Rugi</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Jumlah Bayaran Tebus Guna / Gantirugi (RM) <span class="text-slate-400 font-normal">(Jika ada)</span></label>
                    <input type="number" step="0.01" min="0" name="jumlah_bayaran_tebus_guna" value="0.00" placeholder="0.00" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">
                        Salinan Resit Bayaran <span class="text-rose-500 font-bold">*</span>
                        <span class="text-slate-400 font-normal lowercase">(Wajib: PDF, PNG, JPG - Maks 5MB)</span>
                    </label>
                    <input type="file" name="resit_pembayaran" required accept=".pdf,.png,.jpg,.jpeg" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Perakuan Pengesahan Pegawai</label>
                    <textarea name="perakuan" rows="3" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">Disahkan bahawa peserta telah memenuhi segala terma perjanjian pawah dengan memulangkan anak pawah yang ditetapkan. Induk lembu pawah kini menjadi hak milik mutlak peserta.</textarea>
                </div>

                <div class="pt-3 flex justify-end gap-2">
                    <button type="button" @click="modalPenyelesaian = false" class="px-4 py-2 rounded-xl bg-slate-100 font-bold text-slate-600">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-md">Sahkan & Tamatkan Kontrak</button>
                </div>
            </form>
        </div>
    </div>

    @if(Auth::user()->isStaff() && $perjanjian->status === 'Menunggu Kelulusan')
    <!-- MODAL 4: Kelulusan Permohonan & Pemilihan Lembu EPTR -->
    <div x-show="modalLulus" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-2xl w-full shadow-2xl text-xs space-y-4 max-h-[90vh] overflow-y-auto" @click.outside="modalLulus = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        <span>Kelulusan Permohonan Program Pawah</span>
                    </h3>
                    <p class="text-[11px] text-slate-500">Pilih lembu induk betina daripada sistem EPTR untuk diagihkan kepada peserta</p>
                </div>
                <button @click="modalLulus = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form action="{{ route('pawah.lulus', $perjanjian->id) }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Mula Serahan</label>
                        <input type="date" name="tarikh_mula" value="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Tempoh Kontrak (Tahun)</label>
                        <input type="number" name="tempoh_tahun" min="1" max="10" value="{{ $perjanjian->tempoh_tahun ?? 3 }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 font-bold">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Pegawai Penyelia Bertugas</label>
                    <input type="text" name="pegawai_penyelia" value="{{ Auth::user()->name }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Pilih Lembu Induk EPTR Untuk Diserahkan (Minimum 1 Ekor)</label>
                    <div class="max-h-56 overflow-y-auto border border-slate-200 rounded-2xl divide-y divide-slate-100 bg-slate-50/60 p-2">
                        @forelse($availableTernakan ?? [] as $ternakan)
                            <label class="p-3 rounded-xl hover:bg-emerald-50 transition flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="ternakan_ids[]" value="{{ $ternakan->id }}" class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                                <div class="flex-1">
                                    <div class="font-bold text-slate-900 font-mono text-sm flex items-center gap-2">
                                        <span>{{ $ternakan->no_tag }}</span>
                                        <span class="text-xs font-sans font-semibold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">{{ $ternakan->baka }}</span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        Pemunya Asal: {{ $ternakan->pemunya->nama ?? '-' }} &bull; Umur: {{ $ternakan->umur ?? '-' }} &bull; Lokasi: {{ $ternakan->jajahan }}
                                    </div>
                                </div>
                            </label>
                        @empty
                            <div class="p-6 text-center text-slate-400 text-xs">
                                Tiada lembu betina aktif dijumpai dalam sistem EPTR. Sila daftar ternakan lembu di modul EPTR terlebih dahulu.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Catatan Kelulusan Pegawai</label>
                    <textarea name="catatan_kelulusan" rows="2" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl" placeholder="Contoh: Lawatan tapak kandang telah dibuat pada {{ date('d/m/Y') }} dan didapati memenuhi syarat biosekuriti dan kapasiti ragutan."></textarea>
                </div>

                <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                    <button type="button" @click="modalLulus = false" class="px-5 py-2.5 rounded-xl bg-slate-100 font-bold text-slate-700 hover:bg-slate-200 transition">Batal</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-md shadow-emerald-700/30 transition flex items-center gap-2">
                        <i class="fa-solid fa-check"></i>
                        <span>Sahkan Kelulusan &amp; Pautkan Lembu</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 5: Penolakan Permohonan Pawah -->
    <div x-show="modalTolak" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl text-xs space-y-4" @click.outside="modalTolak = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-900 text-rose-900 flex items-center gap-2">
                    <i class="fa-solid fa-ban text-rose-600"></i>
                    <span>Tolak Permohonan Program Pawah</span>
                </h3>
                <button @click="modalTolak = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
            </div>

            <form action="{{ route('pawah.tolak', $perjanjian->id) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Sebab Penolakan Permohonan</label>
                    <textarea name="sebab_tolak" rows="4" required class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500" placeholder="Nyatakan sebab penolakan secara terperinci (cth: Keluasan padang ragut tidak mencukupi, kandang tidak selamat, ketiadaan stok induk)."></textarea>
                </div>

                <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                    <button type="button" @click="modalTolak = false" class="px-4 py-2 rounded-xl bg-slate-100 font-bold text-slate-700 hover:bg-slate-200">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold shadow-md shadow-rose-700/30">Sahkan Tolak</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if(Auth::user()->isStaff())
    <!-- MODAL 6: Pautkan Lembu Induk Dari EPTR -->
    <div x-show="modalPautLembu" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-2xl w-full shadow-2xl text-xs space-y-4 max-h-[90vh] overflow-y-auto" @click.outside="modalPautLembu = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-link text-emerald-600"></i>
                        <span>Pautkan Lembu Induk Daripada EPTR</span>
                    </h3>
                    <p class="text-[11px] text-slate-500">Pilih lembu induk betina aktif daripada sistem EPTR untuk dipautkan ke bawah perjanjian pawah {{ $perjanjian->no_perjanjian }}</p>
                </div>
                <button @click="modalPautLembu = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form action="{{ route('pawah.paut-ternakan', $perjanjian->id) }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-2">Pilih Lembu Induk (Boleh Pilih Lebih Daripada 1 Ekor)</label>
                    <div class="max-h-60 overflow-y-auto space-y-2 border border-slate-200 rounded-2xl p-3 bg-slate-50">
                        @forelse($availableTernakan ?? [] as $ternakan)
                            @if(!$perjanjian->ternakanList->contains('id', $ternakan->id))
                            <label class="flex items-center gap-3 p-3 rounded-xl bg-white border border-slate-200 hover:border-emerald-400 cursor-pointer transition">
                                <input type="checkbox" name="ternakan_ids[]" value="{{ $ternakan->id }}" class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                                <div class="flex-1">
                                    <div class="font-mono font-bold text-slate-900 text-xs flex items-center gap-2">
                                        <span>{{ $ternakan->no_tag }}</span>
                                        <span class="text-[10px] font-sans font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">{{ $ternakan->baka }}</span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        Pemunya Semasa: {{ $ternakan->pemunya->nama ?? '-' }} &bull; Umur: {{ $ternakan->umur ?? '-' }} &bull; Jajahan: {{ $ternakan->jajahan }}
                                    </div>
                                </div>
                            </label>
                            @endif
                        @empty
                            <div class="text-center py-6 text-slate-400 text-xs">
                                Tiada lembu betina aktif dijumpai di dalam EPTR untuk dipautkan.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                    <button type="button" @click="modalPautLembu = false" class="px-5 py-2.5 rounded-xl bg-slate-100 font-bold text-slate-700 hover:bg-slate-200 transition">Batal</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-md shadow-emerald-700/30 transition flex items-center gap-2">
                        <i class="fa-solid fa-link"></i>
                        <span>Simpan &amp; Pautkan Lembu</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>
@endsection
