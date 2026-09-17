@extends('layouts.app')

@section('title', 'Maklumat Ladang Unggas - ' . $ladang->nama_ladang)
@section('page_title', 'EPU: ' . $ladang->nama_ladang)

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="flex items-center justify-between">
        <a href="{{ route('epu.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 bg-white px-3.5 py-2 rounded-xl border border-slate-200 transition">
            &larr; Kembali ke Senarai EPU
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('epu.borang-c.create', $ladang->id) }}" class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs shadow-md transition flex items-center gap-1.5">
                <i class="fa-solid fa-arrows-rotate"></i> Mohon Pembaharuan (Borang C)
            </a>
            @if(Auth::user()->isStaff())
                <a href="{{ route('epu.borang-d.create', $ladang->id) }}" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                    <i class="fa-solid fa-clipboard-check text-amber-400"></i> Laporan Pemeriksaan (Borang D)
                </a>
            @endif
        </div>
    </div>

    @php
        $p = $permohonanUtama ?? $ladang->permohonanList()->latest()->first();
        $user = Auth::user();
        $isStaff = $user->isStaff();
        $isOwner = $ladang->user_id === $user->id;
    @endphp

    <!-- Flowchart Workflow Progress Tracker (Enakmen Perladangan Unggas 2005) -->
    @if($p)
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-100">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 uppercase tracking-wider">
                            <i class="fa-solid fa-diagram-project mr-1"></i> Carta Alir Permohonan Lesen EPU
                        </span>
                        <span class="text-xs font-mono font-bold text-slate-500">No. Rujukan: {{ $p->no_rujukan_permohonan }}</span>
                    </div>
                    <h3 class="text-sm font-black text-slate-900 mt-1">Status Aliran Kerja Terkini</h3>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs px-3 py-1 rounded-xl font-bold
                        @if($p->status === 'Diluluskan') bg-emerald-100 text-emerald-800 border border-emerald-300
                        @elseif($p->status === 'Ditolak') bg-rose-100 text-rose-800 border border-rose-300
                        @elseif($p->status_verifikasi === 'Tidak Patuh') bg-amber-100 text-amber-800 border border-amber-300
                        @elseif($p->status_verifikasi === 'Tidak Lengkap') bg-orange-100 text-orange-800 border border-orange-300
                        @else bg-blue-100 text-blue-800 border border-blue-300 @endif">
                        Status: {{ $p->status }}
                    </span>
                </div>
            </div>

            <!-- Stepper Steps -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-2 text-center text-xs">
                
                <!-- Step 1: Daftar Pemohon -->
                <div class="p-3 rounded-2xl border bg-emerald-50 border-emerald-200 text-emerald-950 flex flex-col justify-between">
                    <div class="flex items-center justify-center gap-1.5 text-emerald-600 mb-1">
                        <i class="fa-solid fa-circle-check text-sm"></i>
                        <span class="font-bold text-[11px]">Langkah 1</span>
                    </div>
                    <div class="font-bold text-[11px]">Daftar Pengguna</div>
                    <div class="text-[10px] text-emerald-700 mt-1">Selesai Berdaftar</div>
                </div>

                <!-- Step 2: Permohonan Lesen -->
                <div class="p-3 rounded-2xl border bg-emerald-50 border-emerald-200 text-emerald-950 flex flex-col justify-between">
                    <div class="flex items-center justify-center gap-1.5 text-emerald-600 mb-1">
                        <i class="fa-solid fa-circle-check text-sm"></i>
                        <span class="font-bold text-[11px]">Langkah 2</span>
                    </div>
                    <div class="font-bold text-[11px]">Borang A Lesen</div>
                    <div class="text-[10px] text-emerald-700 mt-1">{{ $p->jenis_permohonan }} ({{ $p->jenis_unggas }})</div>
                </div>

                <!-- Step 3: Verifikasi Jajahan PPVJ -->
                <div class="p-3 rounded-2xl border flex flex-col justify-between
                    @if(in_array($p->status_verifikasi, ['Patuh', 'Lengkap']) || $p->status === 'Diluluskan') bg-emerald-50 border-emerald-200 text-emerald-950
                    @elseif($p->status_verifikasi === 'Tidak Lengkap') bg-orange-50 border-orange-200 text-orange-950
                    @elseif($p->status_verifikasi === 'Tidak Patuh') bg-amber-50 border-amber-200 text-amber-950
                    @else bg-blue-50 border-blue-200 text-blue-950 @endif">
                    <div class="flex items-center justify-center gap-1.5 mb-1
                        @if(in_array($p->status_verifikasi, ['Patuh', 'Lengkap']) || $p->status === 'Diluluskan') text-emerald-600
                        @elseif($p->status_verifikasi === 'Tidak Lengkap') text-orange-600
                        @elseif($p->status_verifikasi === 'Tidak Patuh') text-amber-600
                        @else text-blue-600 @endif">
                        <i class="fa-solid @if(in_array($p->status_verifikasi, ['Patuh', 'Lengkap']) || $p->status === 'Diluluskan') fa-circle-check @else fa-spinner fa-spin @endif text-sm"></i>
                        <span class="font-bold text-[11px]">Langkah 3</span>
                    </div>
                    <div class="font-bold text-[11px]">Semakan PPVJ</div>
                    <div class="text-[10px] mt-1 font-semibold">
                        {{ $p->status_verifikasi ?? 'Menunggu Semakan' }}
                    </div>
                </div>

                <!-- Step 4: Pemeriksaan Tapak (Borang D) -->
                <div class="p-3 rounded-2xl border flex flex-col justify-between
                    @if($p->status_verifikasi === 'Patuh' || $p->status_penilaian_ladang === 'Dihantar ke Pegawai Pelesen' || $p->status === 'Diluluskan') bg-emerald-50 border-emerald-200 text-emerald-950
                    @elseif($p->status_verifikasi === 'Tidak Patuh') bg-amber-50 border-amber-200 text-amber-950
                    @else bg-slate-50 border-slate-200 text-slate-700 @endif">
                    <div class="flex items-center justify-center gap-1.5 mb-1
                        @if($p->status_verifikasi === 'Patuh' || $p->status_penilaian_ladang === 'Dihantar ke Pegawai Pelesen' || $p->status === 'Diluluskan') text-emerald-600
                        @elseif($p->status_verifikasi === 'Tidak Patuh') text-amber-600
                        @else text-slate-400 @endif">
                        <i class="fa-solid @if($p->status_verifikasi === 'Patuh' || $p->status === 'Diluluskan') fa-circle-check @else fa-clipboard-check @endif text-sm"></i>
                        <span class="font-bold text-[11px]">Langkah 4</span>
                    </div>
                    <div class="font-bold text-[11px]">Verifikasi Tapak</div>
                    <div class="text-[10px] mt-1">
                        @if($p->status_verifikasi === 'Patuh') Patuh Piawaian
                        @elseif($p->status_verifikasi === 'Tidak Patuh') Perlu Penambahbaikan
                        @else {{ $ladang->pemeriksaanList->count() }} Laporan Tapak @endif
                    </div>
                </div>

                <!-- Step 5: Keputusan Pelesen / Pengarah -->
                <div class="p-3 rounded-2xl border flex flex-col justify-between
                    @if($p->status === 'Diluluskan') bg-emerald-50 border-emerald-200 text-emerald-950
                    @elseif($p->status === 'Ditolak') bg-rose-50 border-rose-200 text-rose-950
                    @elseif($p->status_penilaian_ladang === 'Dihantar ke Pegawai Pelesen') bg-blue-50 border-blue-200 text-blue-950
                    @else bg-slate-50 border-slate-200 text-slate-700 @endif">
                    <div class="flex items-center justify-center gap-1.5 mb-1
                        @if($p->status === 'Diluluskan') text-emerald-600
                        @elseif($p->status === 'Ditolak') text-rose-600
                        @elseif($p->status_penilaian_ladang === 'Dihantar ke Pegawai Pelesen') text-blue-600
                        @else text-slate-400 @endif">
                        <i class="fa-solid @if($p->status === 'Diluluskan') fa-circle-check @elseif($p->status === 'Ditolak') fa-circle-xmark @else fa-stamp @endif text-sm"></i>
                        <span class="font-bold text-[11px]">Langkah 5</span>
                    </div>
                    <div class="font-bold text-[11px]">Pegawai Pelesen</div>
                    <div class="text-[10px] mt-1 font-semibold">
                        @if($p->status === 'Diluluskan') Lulus Lesen
                        @elseif($p->status === 'Ditolak') Gagal / Rayuan
                        @elseif($p->status_penilaian_ladang === 'Dihantar ke Pegawai Pelesen') Semakan Pengarah
                        @else Menunggu Penilaian @endif
                    </div>
                </div>

                <!-- Step 6: Fi & Pencetakan Lesen -->
                <div class="p-3 rounded-2xl border flex flex-col justify-between
                    @if($p->status === 'Diluluskan' && ($p->status_bayaran_fi === 'Selesai Bayar' || $p->mohon_pengecualian || $p->yuran_lesen <= 0)) bg-emerald-100 border-emerald-300 text-emerald-950
                    @elseif($p->status === 'Diluluskan') bg-amber-50 border-amber-200 text-amber-950
                    @else bg-slate-50 border-slate-200 text-slate-700 @endif">
                    <div class="flex items-center justify-center gap-1.5 mb-1
                        @if($p->status === 'Diluluskan' && ($p->status_bayaran_fi === 'Selesai Bayar' || $p->mohon_pengecualian || $p->yuran_lesen <= 0)) text-emerald-600
                        @elseif($p->status === 'Diluluskan') text-amber-600
                        @else text-slate-400 @endif">
                        <i class="fa-solid @if($p->status === 'Diluluskan' && ($p->status_bayaran_fi === 'Selesai Bayar' || $p->mohon_pengecualian || $p->yuran_lesen <= 0)) fa-award @else fa-receipt @endif text-sm"></i>
                        <span class="font-bold text-[11px]">Langkah 6</span>
                    </div>
                    <div class="font-bold text-[11px]">Bayaran & Cetak</div>
                    <div class="text-[10px] mt-1 font-semibold">
                        @if($p->status === 'Diluluskan')
                            @if($p->status_bayaran_fi === 'Selesai Bayar' || $p->mohon_pengecualian || $p->yuran_lesen <= 0)
                                Sedia Dicetak
                            @else
                                Sila Bayar Fi
                            @endif
                        @else
                            -
                        @endif
                    </div>
                </div>

            </div>

            <!-- Dynamic Alert Banners for Applicant & Officials -->
            @if($p->status_verifikasi === 'Tidak Lengkap')
                <div class="p-4 rounded-2xl bg-orange-50 border border-orange-200 text-orange-950 text-xs flex items-start gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-orange-600 text-base mt-0.5"></i>
                    <div>
                        <div class="font-bold">Permohonan Tidak Lengkap (Tindakan Diperlukan)</div>
                        <p class="mt-0.5 text-orange-800">{{ $p->catatan_verifikasi ?? 'Sila lengkapkan dokumen sokongan atau maklumat tanah perladangan yang diperlukan.' }}</p>
                    </div>
                </div>
            @endif

            @if($p->status_verifikasi === 'Tidak Patuh')
                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-300 text-amber-950 text-xs flex items-start gap-3">
                    <i class="fa-solid fa-clipboard-question text-amber-600 text-base mt-0.5"></i>
                    <div class="flex-1">
                        <div class="font-bold">Makluman Ketidakpatuhan Ladang & Tindakan Penambahbaikan</div>
                        <p class="mt-0.5 text-amber-900"><b>Arahan Penambahbaikan:</b> {{ $p->tindakan_penambahbaikan ?? 'Sila patuhi zon penampan dan tingkatkan sistem kawalan bau/lalat sebelum pemeriksaan ulangan.' }}</p>
                        @if($p->catatan_verifikasi)
                            <p class="mt-1 text-amber-800 text-[11px]"><b>Catatan Pegawai:</b> {{ $p->catatan_verifikasi }}</p>
                        @endif
                    </div>
                </div>
            @endif

            @if($p->status === 'Ditolak' || $p->status_kelulusan_pelesen === 'Gagal')
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-950 text-xs space-y-2">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-circle-xmark text-rose-600 text-base mt-0.5"></i>
                        <div class="flex-1">
                            <div class="font-bold text-rose-900">Makluman Kegagalan Permohonan Lesen EPU</div>
                            <p class="mt-0.5 text-rose-800">Alasan Penolakan: {{ $p->catatan_pegawai ?? 'Tidak memenuhi kriteria kelulusan Enakmen Perladangan Unggas.' }}</p>
                            <p class="mt-1 text-rose-700 text-[11px]">Mengikut Enakmen, anda berhak mengemukakan <b>Rayuan kepada Pengarah Jabatan Perkhidmatan Veterinar Negeri Kelantan</b>. Pengarah akan meneliti dan memanjangkan rayuan kepada Pihak Berkuasa Negeri.</p>
                        </div>
                    </div>

                    @if($p->status_rayuan === 'Tiada')
                        <div class="pt-2 border-t border-rose-200 flex justify-end">
                            <button type="button" onclick="document.getElementById('modalRayuan').classList.remove('hidden')" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-xs transition flex items-center gap-1.5">
                                <i class="fa-solid fa-scale-balanced"></i> Kemukakan Rayuan kepada Pengarah
                            </button>
                        </div>
                    @else
                        <div class="p-2.5 rounded-xl bg-white/80 border border-rose-200 flex items-center justify-between text-[11px]">
                            <span>Status Rayuan: <b class="text-rose-900">{{ $p->status_rayuan }}</b> (Tarikh: {{ $p->tarikh_rayuan ? $p->tarikh_rayuan->format('d/m/Y') : '-' }})</span>
                            @if($p->catatan_keputusan_rayuan)
                                <span class="text-slate-600">Catatan: {{ $p->catatan_keputusan_rayuan }}</span>
                            @endif
                        </div>
                    @endif
                </div>
            @endif

            @if($p->status === 'Diluluskan')
                @if($p->status_bayaran_fi === 'Belum Bayar' && !$p->mohon_pengecualian && $p->yuran_lesen > 0)
                    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-300 text-amber-950 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-receipt text-amber-600 text-base mt-0.5"></i>
                            <div>
                                <div class="font-bold">Makluman Kelulusan Lesen &amp; Status Bayaran Fi</div>
                                @if(!$isStaff || $isOwner)
                                    <p class="text-amber-900 mt-0.5">Permohonan lesen anda telah diluluskan oleh Pegawai Pelesen / Pengarah DVS! Sila jelaskan fi lesen sebanyak <b>RM {{ number_format($p->yuran_lesen, 2) }}</b> dan muat naik bukti pembayaran untuk membolehkan pencetakan Lesen Borang B rasmi.</p>
                                @else
                                    <p class="text-amber-900 mt-0.5">Permohonan lesen bagi penternak ini telah diluluskan. Menunggu pemohon menjelaskan bayaran fi lesen sebanyak <b>RM {{ number_format($p->yuran_lesen, 2) }}</b> dan memuat naik resit bayaran.</p>
                                @endif
                            </div>
                        </div>
                        @if(!$isStaff || $isOwner)
                            <button type="button" onclick="document.getElementById('modalBayarFi').classList.remove('hidden')" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl shadow-xs transition flex items-center gap-1.5 whitespace-nowrap self-start sm:self-auto">
                                <i class="fa-solid fa-upload"></i> Bayar &amp; Muat Naik Resit
                            </button>
                        @else
                            <div class="px-3.5 py-2 bg-amber-100 text-amber-900 font-bold rounded-xl text-xs border border-amber-300 whitespace-nowrap self-start sm:self-auto flex items-center gap-1.5">
                                <i class="fa-solid fa-hourglass-half text-amber-600"></i> Menunggu Bayaran Pemohon
                            </div>
                        @endif
                    </div>
                @elseif($p->status_bayaran_fi === 'Menunggu Pengesahan')
                    <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-blue-50 via-indigo-50/50 to-blue-50 border border-blue-200 text-blue-950 text-xs flex flex-col lg:flex-row lg:items-center justify-between gap-4 shadow-xs">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center text-lg shrink-0 shadow-xs">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <div class="space-y-1">
                                <div class="font-black text-sm text-blue-950 flex items-center gap-2">
                                    <span>Resit Pembayaran Fi Sedang Disemak</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-200/80 text-blue-900 border border-blue-300">Menunggu Pengesahan Pegawai</span>
                                </div>
                                <p class="text-blue-900 text-xs leading-relaxed">
                                    Bukti bayaran (No. Resit: <b class="font-mono text-blue-950 bg-white/80 px-1.5 py-0.5 rounded border border-blue-200">{{ $p->no_resit_bayaran ?? '-' }}</b> &bull; Jumlah: <b>RM {{ number_format($p->yuran_lesen, 2) }}</b>) telah dihantar pada {{ $p->tarikh_bayaran_fi ? $p->tarikh_bayaran_fi->format('d/m/Y') : date('d/m/Y') }}.
                                </p>
                                @if($p->resit_bayaran_fi)
                                    <div class="pt-0.5">
                                        <a href="{{ asset('storage/' . $p->resit_bayaran_fi) }}" target="_blank" class="inline-flex items-center gap-1.5 text-blue-700 hover:text-blue-900 font-bold hover:underline text-[11px]">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Pautan Terus Fail: {{ basename($p->resit_bayaran_fi) }}
                                        </a>
                                    </div>
                                @else
                                    <div class="pt-0.5 text-amber-700 text-[11px] font-medium flex items-center gap-1">
                                        <i class="fa-solid fa-circle-info"></i> Fail imbasan resit belum dimuat naik oleh pemohon.
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 self-start lg:self-auto shrink-0">
                            @if($p->resit_bayaran_fi)
                                <a href="{{ asset('storage/' . $p->resit_bayaran_fi) }}" target="_blank" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5 hover:scale-102">
                                    <i class="fa-solid fa-file-invoice-dollar text-sm"></i> Buka &amp; Lihat Fail Resit
                                </a>
                            @else
                                <button type="button" onclick="document.getElementById('modalLihatResit').classList.remove('hidden')" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5">
                                    <i class="fa-solid fa-receipt text-sm"></i> Lihat Butiran Resit
                                </button>
                            @endif

                            @if(!$isStaff || $isOwner)
                                <button type="button" onclick="document.getElementById('modalBayarFi').classList.remove('hidden')" class="px-3 py-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-xs rounded-xl shadow-2xs transition flex items-center gap-1.5">
                                    <i class="fa-solid fa-file-arrow-up text-blue-600"></i> Kemaskini / Muat Naik Resit
                                </button>
                            @endif

                            @if($isStaff)
                                <form action="{{ route('epu.sahkan-bayaran', $p->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5 hover:scale-102">
                                        <i class="fa-solid fa-check-double"></i> Sahkan Bayaran Fi
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-950 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-award text-emerald-600 text-lg mt-0.5"></i>
                            <div>
                                <div class="font-bold text-emerald-900">Lesen Ladang Unggas (Borang B) Sah & Aktif</div>
                                <p class="text-emerald-800 mt-0.5">No. Lesen: <b class="font-mono">{{ $p->no_lesen_epu }}</b> &bull; Sah laku: {{ $p->tarikh_mula_lesen ? $p->tarikh_mula_lesen->format('d/m/Y') : '-' }} sehingga {{ $p->tarikh_tamat_lesen ? $p->tarikh_tamat_lesen->format('d/m/Y') : '-' }}</p>
                                @if($p->resit_bayaran_fi)
                                    <div class="mt-2">
                                        <a href="{{ asset('storage/' . $p->resit_bayaran_fi) }}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-900 font-bold text-[11px] border border-emerald-300/80 transition">
                                            <i class="fa-solid fa-receipt text-emerald-700"></i>
                                            <span>Papar Fail Resit Pembayaran @if($p->no_resit_bayaran) (No: {{ $p->no_resit_bayaran }}) @endif</span>
                                            <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @if(Auth::user()->canCetakBorangEpu())
                            <a href="{{ route('epu.cetak-lesen', $p->id) }}" target="_blank" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-xs transition flex items-center gap-1.5 self-start sm:self-auto">
                                <i class="fa-solid fa-print"></i> Cetak Lesen (Borang B)
                            </a>
                        @else
                            <span class="px-3 py-1.5 rounded-xl bg-emerald-100 text-emerald-800 text-xs font-semibold self-start sm:self-auto">
                                <i class="fa-solid fa-circle-check text-emerald-600 mr-1"></i> Lesen Sah (Cetakan oleh Pegawai PPVJ / Admin Negeri)
                            </span>
                        @endif
                    </div>
                @endif
            @endif

        </div>
    @endif

    <!-- Official Actions Center (Berasaskan Peranan & Fasa Carta Alir) -->
    @if($isStaff && $p)
        @php
            $canVerifikasi = $user->canPerformVerifikasi($ladang->jajahan);
            $canPelesen = $user->canPerformKeputusanPelesen();
        @endphp
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 text-xs">
            
            <!-- Panel 1: Tindakan Pegawai Verifikasi PPVJ (Jajahan) -->
            <div class="bg-white rounded-3xl border {{ $canVerifikasi ? 'border-blue-400 ring-2 ring-blue-100' : 'border-slate-200 bg-slate-50/30' }} p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-clipboard-user text-blue-600 text-base"></i>
                        <div>
                            <h3 class="font-black text-slate-900">Pegawai Verifikasi Jajahan (PPVJ {{ $ladang->jajahan }})</h3>
                            <p class="text-[10px] text-slate-500">Semakan kelengkapan dokumen & verifikasi kepatuhan ladang di tapak</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">PPVJ {{ $ladang->jajahan }}</span>
                </div>

                @if($p->pegawaiVerifikasi)
                    <div class="p-2.5 bg-blue-50/70 border border-blue-200 rounded-xl text-[11px] text-blue-900 flex items-center justify-between">
                        <span><i class="fa-solid fa-user-check text-blue-600 mr-1"></i> Pegawai Bertanggungjawab: <b>{{ $p->pegawaiVerifikasi->name }}</b></span>
                        <span class="text-[10px] text-slate-500 font-mono">{{ $p->tarikh_verifikasi ? $p->tarikh_verifikasi->format('d/m/Y') : '-' }}</span>
                    </div>
                @endif

                @if($canVerifikasi)
                    <!-- Active Form for Pegawai Verifikasi PPVJ -->
                    <form action="{{ route('epu.verifikasi', $p->id) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Status Verifikasi Ladang</label>
                            <select name="status_verifikasi" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500 focus:bg-white font-medium">
                                <option value="Lengkap" {{ $p->status_verifikasi === 'Lengkap' ? 'selected' : '' }}>Lengkap (Sedia untuk lawatan tapak)</option>
                                <option value="Tidak Lengkap" {{ $p->status_verifikasi === 'Tidak Lengkap' ? 'selected' : '' }}>Tidak Lengkap (Perlu pembetulan dokumen)</option>
                                <option value="Patuh" {{ $p->status_verifikasi === 'Patuh' ? 'selected' : '' }}>Patuh Piawaian (Sedia dimajukan ke Pegawai Pelesen)</option>
                                <option value="Tidak Patuh" {{ $p->status_verifikasi === 'Tidak Patuh' ? 'selected' : '' }}>Tidak Patuh (Keluarkan Makluman & Penambahbaikan)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Catatan Verifikasi</label>
                            <textarea name="catatan_verifikasi" rows="2" placeholder="Catatan semakan dokumen atau penemuan..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500 focus:bg-white">{{ old('catatan_verifikasi', $p->catatan_verifikasi) }}</textarea>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Tindakan Penambahbaikan (Jika Tidak Patuh)</label>
                            <textarea name="tindakan_penambahbaikan" rows="2" placeholder="Nyatakan tindakan yang perlu diambil oleh pemohon ladang..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500 focus:bg-white">{{ old('tindakan_penambahbaikan', $p->tindakan_penambahbaikan) }}</textarea>
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-xs transition">
                                <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Status Verifikasi
                            </button>
                        </div>
                    </form>

                    <!-- Sub-action: Hantar Penilaian ke Pegawai Pelesen -->
                    @if($p->status_verifikasi === 'Patuh' && $p->status_penilaian_ladang !== 'Dihantar ke Pegawai Pelesen')
                        <div class="mt-4 pt-4 border-t border-slate-100">
                            <form action="{{ route('epu.hantar-penilaian', $p->id) }}" method="POST" class="p-3 bg-emerald-50 border border-emerald-200 rounded-2xl space-y-2">
                                @csrf
                                <div class="font-bold text-emerald-950 flex items-center gap-1.5">
                                    <i class="fa-solid fa-paper-plane text-emerald-600"></i> Hantar Penilaian Ladang ke Pegawai Pelesen
                                </div>
                                <p class="text-[11px] text-emerald-800">Verifikasi tapak disahkan patuh. Klik butang di bawah untuk memanjangkan syor penilaian kepada Pegawai Pelesen / Pengarah.</p>
                                <input type="text" name="catatan_penilaian_ladang" placeholder="Syor perakuan kelulusan lesen..." class="w-full px-3 py-1.5 bg-white border border-emerald-300 rounded-xl text-xs focus:outline-none">
                                <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-xs transition">
                                    Hantar Penilaian ke Pegawai Pelesen
                                </button>
                            </form>
                        </div>
                    @endif
                @else
                    <!-- Read-Only View for Pegawai Pelesen / Pengarah / Non-PPVJ Officers -->
                    <div class="p-3 bg-slate-100 border border-slate-200 rounded-2xl text-slate-700 text-xs space-y-2">
                        <div class="font-bold flex items-center gap-1.5 text-slate-800">
                            <i class="fa-solid fa-lock text-slate-500"></i> Mod Paparan Sahaja (Bidang Kuasa PPVJ)
                        </div>
                        <p class="text-[11px] text-slate-600">Semakan kelengkapan dan verifikasi kepatuhan tapak dikendalikan khusus oleh Pegawai Verifikasi PPVJ {{ $ladang->jajahan }}. Pegawai Pelesen / Pengarah tidak boleh mengubah suai bahagian ini.</p>
                        
                        <div class="pt-2 border-t border-slate-200 space-y-1.5 text-[11px]">
                            <div>Status Semakan PPVJ: <span class="font-bold px-2 py-0.5 rounded bg-white border border-slate-200 text-slate-800">{{ $p->status_verifikasi ?? 'Belum Disemak' }}</span></div>
                            @if($p->catatan_verifikasi)
                                <div>Catatan Verifikasi: <span class="text-slate-900 font-semibold">{{ $p->catatan_verifikasi }}</span></div>
                            @endif
                            @if($p->tindakan_penambahbaikan)
                                <div class="text-amber-800">Tindakan Penambahbaikan: <b>{{ $p->tindakan_penambahbaikan }}</b></div>
                            @endif
                            <div>Status Penilaian: <span class="font-bold {{ $p->status_penilaian_ladang === 'Dihantar ke Pegawai Pelesen' ? 'text-emerald-700' : 'text-slate-600' }}">{{ $p->status_penilaian_ladang }}</span></div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Panel 2: Tindakan Pegawai Pelesen / Pengarah & Proses Rayuan -->
            <div class="bg-white rounded-3xl border {{ $canPelesen ? 'border-amber-400 ring-2 ring-amber-100' : 'border-slate-200 bg-slate-50/30' }} p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-stamp text-amber-600 text-base"></i>
                        <div>
                            <h3 class="font-black text-slate-900">Pegawai Pelesen / Pengarah DVS</h3>
                            <p class="text-[10px] text-slate-500">Semakan penilaian tapak, kelulusan lesen Borang B & pemprosesan rayuan</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900">HQ / Pelesen</span>
                </div>

                @if($p->status_penilaian_ladang === 'Dihantar ke Pegawai Pelesen')
                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-2xl text-xs space-y-1">
                        <div class="font-bold text-amber-950 flex items-center gap-1.5">
                            <i class="fa-solid fa-bell text-amber-600"></i> Penilaian Ladang Diterima dari PPVJ
                        </div>
                        <p class="text-amber-900 text-[11px]"><b>Syor PPVJ:</b> {{ $p->catatan_penilaian_ladang ?? 'Laporan verifikasi dan penilaian diperakukan untuk kelulusan.' }} (Tarikh: {{ $p->tarikh_hantar_penilaian ? $p->tarikh_hantar_penilaian->format('d/m/Y') : '-' }})</p>
                    </div>
                @endif

                @if($p->pelulus)
                    <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-xl text-[11px] text-slate-700 flex items-center justify-between">
                        <span><i class="fa-solid fa-signature text-slate-500 mr-1"></i> Keputusan Direkodkan Oleh: <b>{{ $p->pelulus->name }}</b></span>
                        <span class="text-[10px] text-slate-400 font-mono">{{ $p->tarikh_kelulusan ? $p->tarikh_kelulusan->format('d/m/Y') : '-' }}</span>
                    </div>
                @endif

                @if($canPelesen)
                    <!-- Active Decision Form for Pegawai Pelesen / Pengarah -->
                    <form action="{{ route('epu.keputusan-pelesen', $p->id) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Keputusan Permohonan Lesen</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="flex items-center gap-2 p-2.5 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-emerald-50 transition">
                                    <input type="radio" name="keputusan" value="Lulus" class="text-emerald-600 focus:ring-emerald-500" {{ $p->status === 'Diluluskan' ? 'checked' : '' }}>
                                    <span class="font-bold text-slate-900"><i class="fa-solid fa-check text-emerald-600 mr-1"></i> Luluskan Lesen</span>
                                </label>
                                <label class="flex items-center gap-2 p-2.5 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-rose-50 transition">
                                    <input type="radio" name="keputusan" value="Gagal" class="text-rose-600 focus:ring-rose-500" {{ $p->status === 'Ditolak' ? 'checked' : '' }}>
                                    <span class="font-bold text-slate-900"><i class="fa-solid fa-xmark text-rose-600 mr-1"></i> Tolak / Gagal</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Syarat Khas Lesen (Jika Lulus)</label>
                            <textarea name="syarat_khas_lesen" rows="2" placeholder="1. Kawalan lalat dan bau secara berkala..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-amber-500 focus:bg-white">{{ old('syarat_khas_lesen', $p->syarat_khas_lesen) }}</textarea>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Catatan Keputusan / Alasan Penolakan</label>
                            <textarea name="catatan_pegawai" rows="2" placeholder="Catatan rasmi pegawai pelesen..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-amber-500 focus:bg-white">{{ old('catatan_pegawai', $p->catatan_pegawai) }}</textarea>
                        </div>

                        <button type="submit" class="w-full py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl shadow-xs transition">
                            Rekod Keputusan Pegawai Pelesen
                        </button>
                    </form>

                    <!-- Processing Appeal (If Appeal Submitted) -->
                    @if($p->status_rayuan !== 'Tiada')
                        <div class="mt-4 pt-4 border-t border-slate-100">
                            <form action="{{ route('epu.rayuan.proses', $p->id) }}" method="POST" class="p-3 bg-purple-50 border border-purple-200 rounded-2xl space-y-2">
                                @csrf
                                <div class="font-bold text-purple-950 flex items-center justify-between">
                                    <span><i class="fa-solid fa-scale-balanced text-purple-600 mr-1"></i> Proses Rayuan Pemohon</span>
                                    <span class="text-[10px] px-2 py-0.5 bg-white rounded-full font-mono">{{ $p->status_rayuan }}</span>
                                </div>
                                <div class="text-[11px] text-purple-900">
                                    <b>Alasan Rayuan:</b> {{ $p->alasan_rayuan }}
                                </div>
                                @if($p->dokumen_rayuan)
                                    <a href="{{ asset('storage/' . $p->dokumen_rayuan) }}" target="_blank" class="inline-block text-[11px] text-purple-700 font-bold hover:underline">
                                        <i class="fa-solid fa-file-pdf"></i> Lihat Lampiran Rayuan
                                    </a>
                                @endif

                                <div class="space-y-1.5 pt-1">
                                    <label class="block font-bold text-purple-950 text-[11px]">Tindakan Pengarah DVS</label>
                                    <select name="tindakan_rayuan" class="w-full px-3 py-1.5 bg-white border border-purple-300 rounded-xl text-xs font-medium">
                                        <option value="Panjangkan ke PBN">Panjangkan ke Pihak Berkuasa Negeri (PBN)</option>
                                        <option value="Lulus Rayuan">Luluskan Rayuan (Keluarkan Lesen)</option>
                                        <option value="Tolak Rayuan">Tolak Rayuan Muktamad</option>
                                    </select>
                                </div>

                                <input type="text" name="catatan_keputusan_rayuan" placeholder="Catatan keputusan rayuan..." class="w-full px-3 py-1.5 bg-white border border-purple-300 rounded-xl text-xs focus:outline-none">

                                <button type="submit" class="w-full py-2 bg-purple-700 hover:bg-purple-800 text-white font-bold rounded-xl shadow-xs transition">
                                    Rekod Keputusan Rayuan
                                </button>
                            </form>
                        </div>
                    @endif
                @else
                    <!-- Read-Only View for Pegawai Verifikasi PPVJ / Non-Licensing Officers -->
                    <div class="p-3 bg-slate-100 border border-slate-200 rounded-2xl text-slate-700 text-xs space-y-2">
                        <div class="font-bold flex items-center gap-1.5 text-slate-800">
                            <i class="fa-solid fa-lock text-slate-500"></i> Mod Paparan Sahaja (Bidang Kuasa Pegawai Pelesen / Pengarah)
                        </div>
                        <p class="text-[11px] text-slate-600">Keputusan kelulusan lesen Borang B dan pemprosesan rayuan dikhaskan untuk Pegawai Pelesen / Pengarah DVS. Pegawai Verifikasi Jajahan tidak mempunyai akses untuk membuat keputusan pelesenan.</p>

                        <div class="pt-2 border-t border-slate-200 space-y-1.5 text-[11px]">
                            <div>Status Kelulusan Lesen: <span class="font-bold px-2 py-0.5 rounded bg-white border border-slate-200 {{ $p->status === 'Diluluskan' ? 'text-emerald-700' : ($p->status === 'Ditolak' ? 'text-rose-700' : 'text-slate-800') }}">{{ $p->status }}</span></div>
                            @if($p->catatan_pegawai)
                                <div>Catatan Pegawai Pelesen: <span class="text-slate-900 font-semibold">{{ $p->catatan_pegawai }}</span></div>
                            @endif
                            @if($p->syarat_khas_lesen)
                                <div>Syarat Khas: <span class="text-slate-700">{{ Str::limit($p->syarat_khas_lesen, 60) }}</span></div>
                            @endif
                            @if($p->status_rayuan !== 'Tiada')
                                <div class="text-purple-900">Status Rayuan: <b>{{ $p->status_rayuan }}</b></div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

        </div>
    @endif

    <!-- Farm Overview Card -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 mb-6 border-b border-slate-100 gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-0.5 rounded-full text-xs font-bold {{ $ladang->sistem_reban === 'Tertutup' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800' }}">
                        Reban {{ $ladang->sistem_reban }}
                    </span>
                    <span class="text-xs font-mono text-slate-400">Lot: {{ $ladang->no_lot ?? '-' }} &bull; {{ $ladang->luas_tanah_ekar ?? '-' }} Ekar</span>
                </div>
                <h2 class="text-2xl font-black text-slate-900 mt-1">{{ $ladang->nama_ladang }}</h2>
                <p class="text-xs text-slate-500">{{ $ladang->nama_pemohon_atau_syarikat }} ({{ $ladang->no_syarikat_atau_ssm ?? 'Individu' }})</p>
            </div>
            <div class="text-right text-xs">
                <div class="text-slate-400 font-medium">Kapasiti Ladang:</div>
                <div class="font-black text-slate-900 text-lg">{{ number_format($ladang->kapasiti_maksimum_unggas) }} Ekor</div>
                <div class="text-emerald-700 font-semibold">Jajahan {{ $ladang->jajahan }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 text-xs">
            
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">LOKASI & TANAH</span>
                <div>ID Premis: <span class="font-mono font-bold text-amber-900">{{ $ladang->id_premis ?? '-' }}</span></div>
                <div>Alamat: <b>{{ $ladang->alamat_ladang }}</b></div>
                <div>Jajahan & Mukim: <b>{{ $ladang->jajahan }} &bull; {{ $ladang->daerah ?: $ladang->mukim ?: '-' }}</b></div>
                @if($ladang->latitude && $ladang->longitude)
                    <div>Koordinat: <span class="font-mono text-slate-600 font-bold">{{ $ladang->latitude }}, {{ $ladang->longitude }}</span></div>
                @endif
                <div>No. Geran / Lot: <span class="font-mono font-bold">{{ $ladang->no_geran_tanah ?? '-' }} / Lot {{ $ladang->no_lot ?? '-' }}</span></div>
                <div>Pemilikan: <b>{{ $ladang->status_pemilikan_tanah }}</b></div>
            </div>

            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">BIOSEKURITI & REBAN</span>
                <div>Sistem Reban: <b>Reban {{ $ladang->sistem_reban }}</b></div>
                @if($ladang->luas_kawasan_sqft)
                    <div>Keluasan Tapak: <b>{{ number_format($ladang->luas_kawasan_sqft) }} ft²</b></div>
                @endif
                <div>Jarak Kediaman: <b>{{ $ladang->jarak_kediaman_terdekat_meter ?? '200' }} Meter</b></div>
                <div>Kawalan Lalat: <span class="text-slate-600">{{ Str::limit($ladang->kaedah_kawalan_lalat_bau ?? 'Semburan EM', 40) }}</span></div>
                <div>Pelupusan Tinja: <span class="text-slate-600">{{ Str::limit($ladang->kaedah_pelupusan_tinja ?? 'Baja organik', 40) }}</span></div>
            </div>

            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">PENGUSAHA / PEMILIK</span>
                <div>Nama: <b>{{ $ladang->pemilik->name ?? $ladang->nama_pemohon_atau_syarikat }}</b></div>
                @if($ladang->pemilik && $ladang->pemilik->nama_syarikat)
                    <div>Syarikat: <b>{{ $ladang->pemilik->nama_syarikat }}</b></div>
                @endif
                <div>No KP / SSM: <span class="font-mono font-bold">{{ $ladang->pemilik->ic_number ?? $ladang->no_syarikat_atau_ssm ?? '-' }}</span></div>
                <div>No Tel: <b>{{ $ladang->pemilik->phone ?? '-' }}</b></div>
            </div>

        </div>
    </div>

    <!-- Uploaded Attachments & Verification Hub -->
    @if($p)
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-2">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900">Dokumen &amp; Lampiran Dimuat Naik Pemohon</h3>
                    <p class="text-xs text-slate-500">Semak dan buka fail lampiran dokumen tanah, pelan tapak, SSM dan bukti pembayaran fi</p>
                </div>
            </div>
            @if(!$isStaff || $isOwner)
                <button type="button" onclick="document.getElementById('modalBayarFi').classList.remove('hidden')" class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5 self-start sm:self-auto">
                    <i class="fa-solid fa-upload text-blue-600"></i> Muat Naik / Kemaskini Fail
                </button>
            @else
                <span class="px-3 py-1 rounded-xl bg-slate-100 text-slate-500 font-bold text-xs flex items-center gap-1.5 self-start sm:self-auto">
                    <i class="fa-solid fa-eye text-blue-600"></i> Semakan Dokumen Rasmi
                </span>
            @endif
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 text-xs">
            <!-- 1. Resit Bayaran Fi Lesen -->
            <div class="p-4 rounded-2xl border {{ $p->resit_bayaran_fi ? 'bg-emerald-50/60 border-emerald-200' : 'bg-slate-50 border-slate-200' }} flex flex-col justify-between space-y-3">
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="font-bold text-slate-900 flex items-center gap-1.5">
                            <i class="fa-solid fa-receipt text-emerald-600"></i> Bukti Bayaran Fi Lesen
                        </span>
                        @if($p->resit_bayaran_fi)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Tersedia</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Belum Ada Fail</span>
                        @endif
                    </div>
                    <p class="text-[11px] text-slate-500">No. Resit: <b class="font-mono text-slate-800">{{ $p->no_resit_bayaran ?? 'Belum Dijana' }}</b> (RM {{ number_format($p->yuran_lesen, 2) }})</p>
                </div>
                <div>
                    @if($p->resit_bayaran_fi)
                        @if(!$isStaff || $isOwner)
                            <div class="flex items-center gap-1.5">
                                <a href="{{ asset('storage/' . $p->resit_bayaran_fi) }}" target="_blank" class="flex-1 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-xs transition flex items-center justify-center gap-1.5 text-xs">
                                    <i class="fa-solid fa-file-invoice-dollar"></i> Buka Resit (PDF/Imej)
                                </a>
                                <button type="button" onclick="document.getElementById('modalBayarFi').classList.remove('hidden')" class="px-2.5 py-2 bg-white hover:bg-slate-100 text-slate-700 font-bold border border-slate-200 rounded-xl transition text-xs" title="Muat Naik Semula / Tukar Fail">
                                    <i class="fa-solid fa-upload"></i>
                                </button>
                            </div>
                        @else
                            <a href="{{ asset('storage/' . $p->resit_bayaran_fi) }}" target="_blank" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-xs transition flex items-center justify-center gap-1.5 text-xs">
                                <i class="fa-solid fa-file-invoice-dollar"></i> Buka Resit (PDF/Imej)
                            </a>
                        @endif
                    @else
                        @if(!$isStaff || $isOwner)
                            <button type="button" onclick="document.getElementById('modalBayarFi').classList.remove('hidden')" class="w-full py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl shadow-xs transition flex items-center justify-center gap-1.5 text-xs">
                                <i class="fa-solid fa-upload"></i> Muat Naik Fail Resit
                            </button>
                        @else
                            <span class="w-full py-2 bg-slate-100 text-slate-500 font-semibold rounded-xl flex items-center justify-center text-xs gap-1.5">
                                <i class="fa-solid fa-hourglass-half text-amber-500"></i> Belum Dimuat Naik oleh Pemohon
                            </span>
                        @endif
                    @endif
                </div>
            </div>

            <!-- 2. Pelan Susunatur Tapak -->
            <div class="p-4 rounded-2xl border {{ $p->dokumen_pelan ? 'bg-blue-50/60 border-blue-200' : 'bg-slate-50 border-slate-200' }} flex flex-col justify-between space-y-3">
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="font-bold text-slate-900 flex items-center gap-1.5">
                            <i class="fa-solid fa-map-location-dot text-blue-600"></i> Pelan Susunatur Tapak
                        </span>
                        @if($p->dokumen_pelan)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">Tersedia</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-700">Tiada Lampiran</span>
                        @endif
                    </div>
                    <p class="text-[11px] text-slate-500">Lakaran pelan reban, jarak kediaman &amp; zon penampan</p>
                </div>
                <div>
                    @if($p->dokumen_pelan)
                        <a href="{{ asset('storage/' . $p->dokumen_pelan) }}" target="_blank" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-xs transition flex items-center justify-center gap-1.5 text-xs">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Pelan Tapak
                        </a>
                    @else
                        <span class="w-full py-2 bg-slate-100 text-slate-400 font-semibold rounded-xl flex items-center justify-center text-xs">
                            Tidak Dilampirkan
                        </span>
                    @endif
                </div>
            </div>

            <!-- 3. Geran Tanah / Perjanjian Pajakan -->
            <div class="p-4 rounded-2xl border {{ $p->dokumen_tanah ? 'bg-indigo-50/60 border-indigo-200' : 'bg-slate-50 border-slate-200' }} flex flex-col justify-between space-y-3">
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="font-bold text-slate-900 flex items-center gap-1.5">
                            <i class="fa-solid fa-file-contract text-indigo-600"></i> Geran / Surat Tanah
                        </span>
                        @if($p->dokumen_tanah)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-800">Tersedia</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-700">Tiada Lampiran</span>
                        @endif
                    </div>
                    <p class="text-[11px] text-slate-500">Pemilikan tanah / Perjanjian sewaan pajakan tapak</p>
                </div>
                <div>
                    @if($p->dokumen_tanah)
                        <a href="{{ asset('storage/' . $p->dokumen_tanah) }}" target="_blank" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-xs transition flex items-center justify-center gap-1.5 text-xs">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Dokumen Tanah
                        </a>
                    @else
                        <span class="w-full py-2 bg-slate-100 text-slate-400 font-semibold rounded-xl flex items-center justify-center text-xs">
                            Tidak Dilampirkan
                        </span>
                    @endif
                </div>
            </div>

            <!-- 4. Sijil SSM / Pendaftaran Syarikat -->
            <div class="p-4 rounded-2xl border {{ $p->dokumen_ssm ? 'bg-purple-50/60 border-purple-200' : 'bg-slate-50 border-slate-200' }} flex flex-col justify-between space-y-3">
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="font-bold text-slate-900 flex items-center gap-1.5">
                            <i class="fa-solid fa-building-shield text-purple-600"></i> Sijil SSM / Syarikat
                        </span>
                        @if($p->dokumen_ssm)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800">Tersedia</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-700">Tiada Lampiran</span>
                        @endif
                    </div>
                    <p class="text-[11px] text-slate-500">Pendaftaran SSM entiti perniagaan / syarikat</p>
                </div>
                <div>
                    @if($p->dokumen_ssm)
                        <a href="{{ asset('storage/' . $p->dokumen_ssm) }}" target="_blank" class="w-full py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl shadow-xs transition flex items-center justify-center gap-1.5 text-xs">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Sijil SSM
                        </a>
                    @else
                        <span class="w-full py-2 bg-slate-100 text-slate-400 font-semibold rounded-xl flex items-center justify-center text-xs">
                            Tidak Dilampirkan
                        </span>
                    @endif
                </div>
            </div>

            <!-- 5. Surat Sokongan / PBT -->
            <div class="p-4 rounded-2xl border {{ $p->dokumen_pbt ? 'bg-teal-50/60 border-teal-200' : 'bg-slate-50 border-slate-200' }} flex flex-col justify-between space-y-3">
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="font-bold text-slate-900 flex items-center gap-1.5">
                            <i class="fa-solid fa-city text-teal-600"></i> Surat Kebenaran PBT
                        </span>
                        @if($p->dokumen_pbt)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800">Tersedia</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-700">Tiada Lampiran</span>
                        @endif
                    </div>
                    <p class="text-[11px] text-slate-500">Kebenaran Majlis Daerah / Pihak Berkuasa Tempatan</p>
                </div>
                <div>
                    @if($p->dokumen_pbt)
                        <a href="{{ asset('storage/' . $p->dokumen_pbt) }}" target="_blank" class="w-full py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl shadow-xs transition flex items-center justify-center gap-1.5 text-xs">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Surat PBT
                        </a>
                    @else
                        <span class="w-full py-2 bg-slate-100 text-slate-400 font-semibold rounded-xl flex items-center justify-center text-xs">
                            Tidak Dilampirkan
                        </span>
                    @endif
                </div>
            </div>

            <!-- 6. Lampiran Rayuan / Pengecualian (jika ada) -->
            @if($p->dokumen_rayuan || $p->lampiran_pengecualian)
                <div class="p-4 rounded-2xl border bg-amber-50/60 border-amber-200 flex flex-col justify-between space-y-3">
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="font-bold text-slate-900 flex items-center gap-1.5">
                                <i class="fa-solid fa-file-lines text-amber-600"></i> Dokumen Rayuan / Pengecualian
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Tersedia</span>
                        </div>
                        <p class="text-[11px] text-slate-500">Lampiran sokongan khas permohonan / rayuan</p>
                    </div>
                    <div>
                        @if($p->dokumen_rayuan)
                            <a href="{{ asset('storage/' . $p->dokumen_rayuan) }}" target="_blank" class="w-full py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl shadow-xs transition flex items-center justify-center gap-1.5 text-xs">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Fail Rayuan
                            </a>
                        @elseif($p->lampiran_pengecualian)
                            <a href="{{ asset('storage/' . $p->lampiran_pengecualian) }}" target="_blank" class="w-full py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl shadow-xs transition flex items-center justify-center gap-1.5 text-xs">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Lampiran Pengecualian
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Official Gazetted Forms Printable Hub (Enakmen 2005) -->
    @if(Auth::user()->canCetakBorangEpu())
    <div class="bg-gradient-to-br from-amber-50 to-orange-50/40 rounded-3xl border border-amber-200/80 p-6 sm:p-7 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-4 border-b border-amber-200/60 gap-2">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-slate-950 uppercase tracking-wider">Format Rasmi Diwartakan</span>
                    <h3 class="text-base font-black text-slate-900">Cetak Dokumen & Borang Rasmi EPU</h3>
                </div>
                <p class="text-xs text-slate-600 mt-0.5">Enakmen Perladangan Unggas 2005 & Peraturan-Peraturan Berkaitan Jabatan Perkhidmatan Veterinar Negeri Kelantan</p>
            </div>
            <span class="text-xs font-mono font-bold text-amber-900 bg-white/80 px-3 py-1.5 rounded-xl border border-amber-200">
                <i class="fa-solid fa-stamp text-amber-600 mr-1"></i> Warta Rasmi
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
            <!-- 1. Borang A Permohonan Lesen -->
            <a href="{{ route('epu.cetak-borang-a', $ladang->id) }}" target="_blank" class="group bg-white p-4 rounded-2xl border border-amber-200 hover:border-amber-400 shadow-xs hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-700">Jadual 1</span>
                        <i class="fa-solid fa-file-signature text-amber-600 group-hover:scale-110 transition-transform"></i>
                    </div>
                    <div class="font-bold text-xs text-slate-900 group-hover:text-amber-700 transition">Borang A: Permohonan Lesen</div>
                    <p class="text-[11px] text-slate-500 mt-1">Permohonan lesen & aktiviti perladangan unggas (2 Halaman).</p>
                </div>
                <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-amber-800">
                    <span><i class="fa-solid fa-print mr-1"></i> Cetak Borang</span>
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                </div>
            </a>

            <!-- 2. Borang B Permohonan Pengecualian -->
            <a href="{{ route('epu.cetak-borang-b-pengecualian', $ladang->id) }}" target="_blank" class="group bg-white p-4 rounded-2xl border border-amber-200 hover:border-amber-400 shadow-xs hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-700">Borang B</span>
                        <i class="fa-solid fa-file-shield text-amber-600 group-hover:scale-110 transition-transform"></i>
                    </div>
                    <div class="font-bold text-xs text-slate-900 group-hover:text-amber-700 transition">Borang B: Pengecualian Lesen</div>
                    <p class="text-[11px] text-slate-500 mt-1">Permohonan pengecualian lesen (Projek D'Raja, PPRT, Skala Kecil).</p>
                </div>
                <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-amber-800">
                    <span><i class="fa-solid fa-print mr-1"></i> Cetak Borang</span>
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                </div>
            </a>

            <!-- 3. Borang C Sijil Pengecualian -->
            <a href="{{ route('epu.cetak-sijil-pengecualian-c', $ladang->id) }}" target="_blank" class="group bg-white p-4 rounded-2xl border border-amber-200 hover:border-amber-400 shadow-xs hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-700">Borang C</span>
                        <i class="fa-solid fa-award text-amber-600 group-hover:scale-110 transition-transform"></i>
                    </div>
                    <div class="font-bold text-xs text-slate-900 group-hover:text-amber-700 transition">Borang C: Sijil Pengecualian</div>
                    <p class="text-[11px] text-slate-500 mt-1">Sijil pelepasan lesen beserta 6 syarat khas biosekuriti.</p>
                </div>
                <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-amber-800">
                    <span><i class="fa-solid fa-print mr-1"></i> Cetak Sijil</span>
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                </div>
            </a>

            <!-- 4. Borang A Salinan Pendua -->
            <a href="{{ route('epu.cetak-salinan-pendua', $ladang->id) }}" target="_blank" class="group bg-white p-4 rounded-2xl border border-amber-200 hover:border-amber-400 shadow-xs hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-700">Borang A</span>
                        <i class="fa-solid fa-clone text-amber-600 group-hover:scale-110 transition-transform"></i>
                    </div>
                    <div class="font-bold text-xs text-slate-900 group-hover:text-amber-700 transition">Salinan Pendua Lesen</div>
                    <p class="text-[11px] text-slate-500 mt-1">Permohonan salinan gantian lesen hilang, cacat atau musnah.</p>
                </div>
                <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-amber-800">
                    <span><i class="fa-solid fa-print mr-1"></i> Cetak Borang</span>
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                </div>
            </a>
        </div>
    </div>
    @endif

    <!-- History of EPU Licenses & Inspections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Left: Senarai Lesen Unggas (Borang B) -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-certificate text-amber-500 text-base"></i>
                    <h3 class="text-sm font-bold text-slate-900">Sejarah Permohonan & Lesen Unggas</h3>
                </div>
                <span class="text-xs font-bold text-amber-800">{{ $ladang->permohonanList->count() }} Rekod</span>
            </div>

            <div class="space-y-3">
                @forelse($ladang->permohonanList as $permohonan)
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="font-mono font-bold text-amber-900 block">{{ $permohonan->no_lesen_epu ?? $permohonan->no_rujukan_permohonan }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">Ruj: {{ $permohonan->no_rujukan_permohonan }}</span>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold
                                @if($permohonan->status === 'Diluluskan') bg-emerald-100 text-emerald-800
                                @elseif($permohonan->status === 'Ditolak') bg-rose-100 text-rose-800
                                @else bg-amber-100 text-amber-800 @endif">
                                {{ $permohonan->status }}
                            </span>
                        </div>

                        <div class="text-slate-700">
                            Jenis: <b>{{ $permohonan->jenis_unggas }}</b> &bull; Bilangan: <b>{{ number_format($permohonan->bilangan_semasa_unggas) }} Ekor</b>
                            @if($permohonan->pegawaiVerifikasi)
                                &bull; <span class="text-blue-700 font-semibold">PPVJ: {{ $permohonan->pegawaiVerifikasi->name }}</span>
                            @endif
                        </div>

                        <div class="text-[11px] text-slate-500 flex flex-wrap justify-between items-center gap-2 pt-1 border-t border-slate-200">
                            <span>Tempoh: {{ $permohonan->tarikh_mula_lesen ? $permohonan->tarikh_mula_lesen->format('d/m/Y') : '-' }} &rarr; {{ $permohonan->tarikh_tamat_lesen ? $permohonan->tarikh_tamat_lesen->format('d/m/Y') : '-' }}</span>
                            <div class="flex items-center gap-2.5">
                                @if($permohonan->resit_bayaran_fi)
                                    <a href="{{ asset('storage/' . $permohonan->resit_bayaran_fi) }}" target="_blank" class="font-bold text-blue-700 hover:underline flex items-center gap-1">
                                        <i class="fa-solid fa-receipt"></i> Resit ({{ $permohonan->no_resit_bayaran ?? 'Lihat' }})
                                    </a>
                                @endif
                                @if($permohonan->status === 'Diluluskan' && Auth::user()->canCetakBorangEpu())
                                    <a href="{{ route('epu.cetak-lesen', $permohonan->id) }}" target="_blank" class="font-bold text-amber-700 hover:underline flex items-center gap-1">
                                        <i class="fa-solid fa-print"></i> Cetak Lesen (Borang B)
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center text-slate-400 text-xs">
                        Tiada rekod permohonan lesen.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right: Laporan Pemeriksaan Lapangan (Borang D) -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-check text-blue-600 text-base"></i>
                    <h3 class="text-sm font-bold text-slate-900">Laporan Pemeriksaan & Penguatkuasaan (Borang D)</h3>
                </div>
                <span class="text-xs font-bold text-blue-800">{{ $ladang->pemeriksaanList->count() }} Laporan</span>
            </div>

            <div class="space-y-3">
                @forelse($ladang->pemeriksaanList as $pem)
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-900">Tarikh: {{ $pem->tarikh_pemeriksaan ? $pem->tarikh_pemeriksaan->format('d/m/Y') : '-' }}</span>
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-bold {{ $pem->status_keputusan === 'Lulus' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                Skor: {{ $pem->skor_kebersihan_peratus }}% &bull; {{ $pem->status_keputusan }}
                            </span>
                        </div>
                        <p class="text-slate-700">{{ $pem->penemuan_pemeriksaan }}</p>
                        <div class="text-[11px] text-slate-500 pt-1 border-t border-slate-200 flex items-center justify-between">
                            <span>Syor: <b>{{ $pem->syor_dan_arahan }}</b></span>
                            <span class="text-slate-400">Pegawai: {{ $pem->pegawai->name ?? '-' }}</span>
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center text-slate-400 text-xs">
                        Belum ada laporan pemeriksaan tapak direkodkan.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>

<!-- Modal 1: Kemukakan Rayuan kepada Pengarah (Pemohon) -->
@if($p)
<div id="modalRayuan" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-xl border border-slate-100">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-scale-balanced text-rose-600 text-base"></i>
                <h3 class="text-sm font-bold text-slate-900">Borang Rayuan kepada Pengarah DVS</h3>
            </div>
            <button type="button" onclick="document.getElementById('modalRayuan').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>

        <form action="{{ route('epu.rayuan.store', $p->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 mb-1">Alasan & Justifikasi Rayuan <span class="text-rose-500">*</span></label>
                <textarea name="alasan_rayuan" rows="4" required placeholder="Nyatakan tindakan penambahbaikan dan justifikasi kukuh bagi permohonan semula lesen..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500"></textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Lampiran Dokumen Sokongan Rayuan (PDF / Gambar)</label>
                <input type="file" name="dokumen_rayuan" accept=".pdf,.jpg,.jpeg,.png" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                <span class="text-[10px] text-slate-400 mt-1 block">Contoh: Bukti gambar pembaikan reban, kelulusan jiran, atau surat sokongan rasmi.</span>
            </div>

            <div class="pt-2 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalRayuan').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-xs transition">Hantar Rayuan ke Pengarah</button>
            </div>
        </form>
    </div>
</div>

@if(!$isStaff || $isOwner)
<!-- Modal 2: Pembayaran Fi Lesen & Muat Naik Resit -->
<div id="modalBayarFi" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-xl border border-slate-100">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-receipt text-amber-600 text-base"></i>
                <h3 class="text-sm font-bold text-slate-900">Pembayaran Fi Lesen EPU</h3>
            </div>
            <button type="button" onclick="document.getElementById('modalBayarFi').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>

        <div class="p-4 bg-gradient-to-br from-amber-50 to-orange-50/60 rounded-2xl border border-amber-200 text-xs space-y-2.5">
            <div class="flex justify-between items-center font-bold text-amber-950">
                <span class="text-xs">Jumlah Fi Ditetapkan:</span>
                <span class="text-lg font-black text-amber-900 bg-white px-3 py-0.5 rounded-xl border border-amber-200">RM {{ number_format($p->yuran_lesen, 2) }}</span>
            </div>
            
            <div class="p-3 bg-white/90 rounded-xl border border-amber-200/80 text-[11px] text-amber-950 space-y-1">
                <div class="font-bold flex items-center gap-1.5 text-amber-900">
                    <i class="fa-solid fa-building-columns text-amber-600"></i> Akaun Bank Rasmi JPVNK:
                </div>
                <div><b>Nama Akaun:</b> Jabatan Perkhidmatan Veterinar Negeri Kelantan</div>
                <div><b>Bank:</b> Bank Islam Malaysia Berhad (BIMB)</div>
                <div><b>No. Akaun:</b> <span class="font-mono font-bold select-all bg-amber-100/70 px-1.5 py-0.5 rounded">03018010000000</span></div>
                <div class="text-[10px] text-slate-500 pt-0.5">* Sila masukkan No. Rujukan <b>{{ $p->no_rujukan_permohonan }}</b> sebagai rujukan semasa pembayaran. Bayaran juga boleh dibuat terus di kaunter PPVJ {{ $ladang->jajahan }}.</div>
            </div>
        </div>

        <form action="{{ route('epu.bayar-fi', $p->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 mb-1">No. Resit / No. Rujukan Transaksi Bank <span class="text-rose-500">*</span></label>
                <input type="text" name="no_resit_bayaran" required value="{{ old('no_resit_bayaran', $p->no_resit_bayaran) }}" placeholder="Contoh: RES-2026-9901 atau Ref Transaksi Online" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 font-mono">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Muat Naik Resit / Slip Bayaran (PDF / Gambar) <span class="text-rose-500">*</span></label>
                <input type="file" name="resit_bayaran_fi" required accept=".pdf,.jpg,.jpeg,.png" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                <span class="text-[10px] text-slate-400 mt-1 block">Format diterima: PDF, JPG, PNG (Maksimum 10MB)</span>
            </div>

            <div class="pt-2 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalBayarFi').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl shadow-xs transition flex items-center gap-1.5">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Hantar Bukti Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- Modal 3: Paparan / Semakan Butiran Resit Bayaran Fi -->
<div id="modalLihatResit" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-xl border border-slate-100">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-file-invoice-dollar text-blue-600 text-base"></i>
                <h3 class="text-sm font-bold text-slate-900">Maklumat Resit Pembayaran Fi Lesen EPU</h3>
            </div>
            <button type="button" onclick="document.getElementById('modalLihatResit').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>

        <div class="space-y-3 text-xs">
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 font-medium">No. Resit / Rujukan:</span>
                    <span class="font-mono font-bold text-slate-900 bg-white px-2 py-0.5 rounded border border-slate-200">{{ $p->no_resit_bayaran ?? '-' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 font-medium">Jumlah Fi Lesen:</span>
                    <span class="font-bold text-emerald-700 text-sm">RM {{ number_format($p->yuran_lesen, 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 font-medium">Tarikh Bayaran:</span>
                    <span class="font-semibold text-slate-800">{{ $p->tarikh_bayaran_fi ? $p->tarikh_bayaran_fi->format('d/m/Y') : '-' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 font-medium">Status Bayaran:</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $p->status_bayaran_fi === 'Selesai Bayar' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ $p->status_bayaran_fi }}
                    </span>
                </div>
                <div class="flex justify-between items-center pt-1 border-t border-slate-200">
                    <span class="text-slate-500 font-medium">Pembayar / Ladang:</span>
                    <span class="font-bold text-slate-800 text-right">{{ $ladang->nama_ladang }} ({{ $ladang->nama_pemohon_atau_syarikat }})</span>
                </div>
            </div>

            <!-- Fail Imbasan Resit -->
            <div class="p-4 rounded-2xl border {{ $p->resit_bayaran_fi ? 'bg-emerald-50/50 border-emerald-200' : 'bg-amber-50/50 border-amber-200' }}">
                <div class="font-bold text-slate-800 mb-2 flex items-center justify-between">
                    <span>Fail Imbasan Resit / Bukti Bayaran:</span>
                    @if($p->resit_bayaran_fi)
                        <span class="text-[10px] text-emerald-700 font-bold bg-emerald-100 px-2 py-0.5 rounded">Tersedia</span>
                    @else
                        <span class="text-[10px] text-amber-700 font-bold bg-amber-100 px-2 py-0.5 rounded">Belum Dimuat Naik</span>
                    @endif
                </div>

                @if($p->resit_bayaran_fi)
                    <div class="space-y-2">
                        <p class="text-[11px] text-slate-600">Fail resit yang dimuat naik oleh pemohon:</p>
                        <a href="{{ asset('storage/' . $p->resit_bayaran_fi) }}" target="_blank" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-xs transition flex items-center justify-center gap-2 text-xs">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Fail Resit (Tab Baharu)
                        </a>
                    </div>
                @else
                    <div class="space-y-2">
                        @if(!$isStaff || $isOwner)
                            <p class="text-[11px] text-amber-900">
                                Tiada fail lampiran resit fizikal/PDF disimpan dalam rekod ini. Sila muat naik fail resit rasmi atau slip transaksi pembayaran anda.
                            </p>
                            <button type="button" onclick="document.getElementById('modalLihatResit').classList.add('hidden'); document.getElementById('modalBayarFi').classList.remove('hidden');" class="w-full py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl shadow-xs transition flex items-center justify-center gap-1.5 text-xs">
                                <i class="fa-solid fa-upload"></i> Muat Naik / Lampirkan Fail Resit
                            </button>
                        @else
                            <p class="text-[11px] text-amber-900">
                                Tiada fail lampiran resit fizikal/PDF disimpan dalam rekod ini. Menunggu pemohon memuat naik bukti resit bayaran.
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="pt-2 flex justify-end">
            <button type="button" onclick="document.getElementById('modalLihatResit').classList.add('hidden')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition">Tutup</button>
        </div>
    </div>
</div>
@endif

@endsection
