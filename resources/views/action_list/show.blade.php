@extends('layouts.app')

@section('title', 'Butiran Action List ' . ($actionList->no_bil ?: ('BIL-' . $actionList->id)) . ' - JPVNK')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gradient-to-r from-emerald-900 via-teal-900 to-slate-900 p-6 rounded-3xl text-white shadow-xl">
        <div class="space-y-1">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold border border-amber-500/30">
                <i class="fa-solid fa-file-lines"></i> KOD DOKUMEN: {{ $actionList->kod_dokumen ?? 'PK-RK-61' }}
            </div>
            <h1 class="text-2xl font-black font-mono tracking-tight">{{ $actionList->no_bil ?: ('BIL-' . $actionList->id) }}</h1>
            <p class="text-xs text-slate-300">
                Pejabat Perkhidmatan Veterinar Jajahan {{ $actionList->jajahan }} | Tarikh: {{ $actionList->tarikh ? $actionList->tarikh->format('d F Y') : '-' }} ({{ $actionList->masa_pendaftaran ?: '-' }})
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('action-list.cetak', $actionList->id) }}" target="_blank" class="px-5 py-2.5 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs shadow-lg transition flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Cetak Borang Rasmi PK-RK-61 (PDF)
            </a>
            <a href="{{ route('action-list.edit', $actionList->id) }}" class="px-4 py-2.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition flex items-center gap-1.5 border border-white/20">
                <i class="fa-solid fa-pen"></i> Kemaskini
            </a>
            <a href="{{ route('action-list.index') }}" class="px-4 py-2.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition flex items-center gap-1.5 border border-white/20">
                <i class="fa-solid fa-arrow-left"></i> Senarai
            </a>
        </div>
    </div>

    <!-- Status & Overview Bar -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-4 text-xs">
        <div class="flex items-center gap-3">
            <span class="text-slate-500 font-bold uppercase text-[11px]">Status Dokumen:</span>
            <span class="px-3 py-1 rounded-full font-bold text-xs bg-emerald-100 text-emerald-800 border border-emerald-300">
                <i class="fa-solid fa-circle-check"></i> {{ $actionList->status ?? 'Selesai' }}
            </span>
            <span class="px-3 py-1 rounded-full font-bold text-xs bg-slate-100 text-slate-700 border border-slate-200">
                Kategori: {{ $actionList->kategori_pelanggan }}
            </span>
        </div>

        <div class="flex items-center gap-4 font-semibold text-slate-700">
            <div>
                <span class="text-slate-400 text-[11px]">Pegawai Bertugas:</span>
                <span class="font-bold text-slate-900 ml-1">{{ $actionList->nama_pegawai ?? ($actionList->pegawai->name ?? '-') }}</span>
            </div>
            <div>
                <span class="text-slate-400 text-[11px]">Jumlah Bayaran:</span>
                <span class="font-black text-amber-600 ml-1">RM {{ number_format($actionList->bayaran, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- A. MAKLUMAT PELANGGAN -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-black text-slate-900 text-sm flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">A</span>
                MAKLUMAT PELANGGAN
            </h3>
            <span class="text-xs font-mono text-slate-400">[ MASA : {{ $actionList->masa_pendaftaran ?: '-' }} ]</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 text-xs">
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase">1. Nama Pelanggan / Syarikat</div>
                <div class="text-sm font-black text-slate-900 mt-0.5">{{ $actionList->nama_pelanggan }}</div>
            </div>

            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase">2. No. K/P (Baru) / SSM</div>
                <div class="font-mono font-bold text-slate-800 mt-0.5">{{ $actionList->no_kp ?: '-' }}</div>
            </div>

            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase">6. No. Telefon</div>
                <div class="font-bold text-slate-800 mt-0.5">{{ $actionList->telefon ?: '-' }}</div>
            </div>

            <div class="lg:col-span-2">
                <div class="text-[11px] font-bold text-slate-400 uppercase">3. Alamat Lengkap</div>
                <div class="text-slate-700 mt-0.5 leading-relaxed">{{ $actionList->alamat ?: '-' }}</div>
            </div>

            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase">20. No. Rujukan Fail</div>
                <div class="font-mono font-bold text-slate-800 mt-0.5">{{ $actionList->no_rujukan ?: '-' }}</div>
            </div>

            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase">4. Mukim</div>
                <div class="font-semibold text-slate-800 mt-0.5">{{ $actionList->mukim ?: '-' }}</div>
            </div>

            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase">Poskod</div>
                <div class="font-mono font-semibold text-slate-800 mt-0.5">{{ $actionList->poskod ?: '-' }}</div>
            </div>

            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase">5. Daerah / Jajahan</div>
                <div class="font-semibold text-slate-800 mt-0.5">{{ $actionList->daerah ?: ('Jajahan ' . $actionList->jajahan . ', Kelantan') }}</div>
            </div>
        </div>
    </div>

    <!-- B. BUTIR-BUTIR PERKHIDMATAN -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-3">
        <h3 class="font-black text-slate-900 text-sm flex items-center gap-2 border-b border-slate-100 pb-3">
            <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">B</span>
            BUTIR-BUTIR PERKHIDMATAN
        </h3>
        <div class="text-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase mb-1">7. Catatan Ringkas Perkhidmatan Yang Dipohon:</div>
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-800 leading-relaxed font-medium">
                {{ $actionList->catatan_perkhidmatan_dipohon ?: 'Tiada catatan perkhidmatan dipohon.' }}
            </div>
        </div>
    </div>

    <!-- C. MAKLUMAT TEMUJANJI -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
        <h3 class="font-black text-slate-900 text-sm flex items-center gap-2 border-b border-slate-100 pb-3">
            <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">C</span>
            MAKLUMAT TEMUJANJI
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase">8. Nama Pegawai</div>
                <div class="font-bold text-slate-900 mt-0.5">{{ $actionList->nama_pegawai ?: '-' }}</div>
                <div class="text-[11px] text-slate-500">[ Masa : {{ $actionList->masa_pegawai ?: '-' }} ]</div>
            </div>

            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase">9. Masa Temujanji</div>
                <div class="font-bold text-slate-800 mt-0.5">
                    Mula: <span class="font-mono">{{ $actionList->masa_temujanji_mula ?: '-' }}</span>
                </div>
                <div class="font-bold text-slate-800">
                    Hingga: <span class="font-mono">{{ $actionList->masa_temujanji_hingga ?: '-' }}</span>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="text-[11px] font-bold text-slate-400 uppercase">10. Maklumat Pelanggan (jika berlainan / wakil)</div>
                <div class="text-slate-800 mt-0.5">{{ $actionList->maklumat_pelanggan_berlainan ?: '-' }}</div>
            </div>

            <div class="lg:col-span-4">
                <div class="text-[11px] font-bold text-slate-400 uppercase">11. Maklumat Tambahan / Catatan Lokasi</div>
                <div class="text-slate-800 mt-0.5">{{ $actionList->maklumat_tambahan ?: '-' }}</div>
            </div>

            @if($actionList->lampiran_peta)
                <div class="lg:col-span-4 pt-2">
                    <div class="text-[11px] font-bold text-slate-400 uppercase mb-2">Lakaran Peta Lokasi / Lampiran:</div>
                    <div class="max-w-md rounded-2xl overflow-hidden border border-slate-200 shadow-xs">
                        <img src="{{ asset('storage/' . $actionList->lampiran_peta) }}" alt="Lakaran Peta" class="w-full h-auto object-cover">
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- D. MAKLUMAT PERKHIDMATAN YANG DIBERI -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
        <h3 class="font-black text-slate-900 text-sm flex items-center gap-2 border-b border-slate-100 pb-3">
            <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">D</span>
            MAKLUMAT PERKHIDMATAN YANG DIBERI
        </h3>

        @php
            $srv = is_array($actionList->perkhidmatan_diberi) ? $actionList->perkhidmatan_diberi : [];
        @endphp

        <!-- 12. Senarai Perkhidmatan -->
        <div>
            <div class="text-[11px] font-bold text-slate-400 uppercase mb-2">12. Jenis Perkhidmatan Dilaksanakan:</div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                <div class="p-2.5 rounded-xl border {{ !empty($srv['rawatan_lapangan']) ? 'bg-emerald-50 border-emerald-300 text-emerald-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
                    <i class="fa-solid {{ !empty($srv['rawatan_lapangan']) ? 'fa-square-check text-emerald-600' : 'fa-square text-slate-300' }} mr-1.5"></i>
                    Rawatan Di Lapangan
                </div>

                <div class="p-2.5 rounded-xl border {{ !empty($srv['rawatan_klinik']) ? 'bg-emerald-50 border-emerald-300 text-emerald-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
                    <i class="fa-solid {{ !empty($srv['rawatan_klinik']) ? 'fa-square-check text-emerald-600' : 'fa-square text-slate-300' }} mr-1.5"></i>
                    Rawatan Di Klinik
                </div>

                <div class="p-2.5 rounded-xl border {{ !empty($srv['pembedahan']) ? 'bg-emerald-50 border-emerald-300 text-emerald-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
                    <i class="fa-solid {{ !empty($srv['pembedahan']) ? 'fa-square-check text-emerald-600' : 'fa-square text-slate-300' }} mr-1.5"></i>
                    Pembedahan {{ $actionList->keterangan_pembedahan ? "({$actionList->keterangan_pembedahan})" : '' }}
                </div>

                <div class="p-2.5 rounded-xl border {{ !empty($srv['pemantauan_pawah']) ? 'bg-emerald-50 border-emerald-300 text-emerald-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
                    <i class="fa-solid {{ !empty($srv['pemantauan_pawah']) ? 'fa-square-check text-emerald-600' : 'fa-square text-slate-300' }} mr-1.5"></i>
                    Pemantauan Pawah Negeri
                </div>

                <div class="p-2.5 rounded-xl border {{ !empty($srv['pemantauan_projek']) ? 'bg-emerald-50 border-emerald-300 text-emerald-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
                    <i class="fa-solid {{ !empty($srv['pemantauan_projek']) ? 'fa-square-check text-emerald-600' : 'fa-square text-slate-300' }} mr-1.5"></i>
                    Pemantauan Projek {{ $actionList->keterangan_projek ? "({$actionList->keterangan_projek})" : '' }}
                </div>

                <div class="p-2.5 rounded-xl border {{ !empty($srv['pemantauan_trust']) ? 'bg-emerald-50 border-emerald-300 text-emerald-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
                    <i class="fa-solid {{ !empty($srv['pemantauan_trust']) ? 'fa-square-check text-emerald-600' : 'fa-square text-slate-300' }} mr-1.5"></i>
                    Pemantauan TRUST
                </div>

                <div class="p-2.5 rounded-xl border {{ !empty($srv['lawatan_terancang']) ? 'bg-emerald-50 border-emerald-300 text-emerald-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
                    <i class="fa-solid {{ !empty($srv['lawatan_terancang']) ? 'fa-square-check text-emerald-600' : 'fa-square text-slate-300' }} mr-1.5"></i>
                    Lawatan Terancang
                </div>

                <div class="p-2.5 rounded-xl border {{ !empty($srv['perkhidmatan_lain']) ? 'bg-emerald-50 border-emerald-300 text-emerald-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
                    <i class="fa-solid {{ !empty($srv['perkhidmatan_lain']) ? 'fa-square-check text-emerald-600' : 'fa-square text-slate-300' }} mr-1.5"></i>
                    Lain-lain {{ $actionList->keterangan_lain ? "({$actionList->keterangan_lain})" : '' }}
                </div>
            </div>
        </div>

        <!-- 13. Catatan Ringkas: Ternakan -->
        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase">13. 1. Jenis Ternakan</div>
                @php
                    $animals = is_array($actionList->jenis_ternakan) ? $actionList->jenis_ternakan : [];
                @endphp
                <div class="font-bold text-slate-900 mt-0.5">
                    {{ !empty($animals) ? implode(', ', $animals) : '-' }}
                    @if($actionList->jenis_ternakan_lain)
                        <span class="text-slate-500 font-normal">({{ $actionList->jenis_ternakan_lain }})</span>
                    @endif
                </div>
            </div>

            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase">2. Bil. Ternakan Dirawat</div>
                <div class="font-mono font-black text-emerald-700 text-sm mt-0.5">{{ $actionList->bil_ternakan ?? 0 }} ekor</div>
            </div>

            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase">3. Bil. Yang Ada di Ladang/Premis</div>
                <div class="font-mono font-black text-slate-800 text-sm mt-0.5">{{ $actionList->bil_yang_ada ?? 0 }} ekor</div>
            </div>
        </div>

        <!-- 17. Laporan & 21. Penggunaan Ubat -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 text-xs">
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase mb-1">17. Laporan Lengkap Rawatan / Tindakan:</div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-800 leading-relaxed font-medium min-h-[90px] whitespace-pre-line">
                    {{ $actionList->laporan ?: 'Tiada laporan direkodkan.' }}
                </div>
            </div>

            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase mb-1">21. Penggunaan Ubat & Vaksin:</div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-slate-800 leading-relaxed font-medium min-h-[90px] whitespace-pre-line">
                    {{ $actionList->penggunaan_ubat ?: 'Tiada ubat direkodkan.' }}
                </div>
            </div>
        </div>
    </div>

    <!-- E. PENGAKUAN PELANGGAN & PENGESAHAN PEGAWAI -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
        <h3 class="font-black text-slate-900 text-sm flex items-center gap-2 border-b border-slate-100 pb-3">
            <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">E</span>
            PENGAKUAN PELANGGAN & PENGESAHAN PEGAWAI
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 text-xs">
            <!-- 15. Tahap Kepuasan -->
            <div class="lg:col-span-3 bg-amber-50/70 p-4 rounded-2xl border border-amber-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <div class="text-[11px] font-bold text-amber-900 uppercase">15. Pengakuan Tahap Kepuasan Pelanggan:</div>
                    <div class="text-sm font-black mt-0.5 {{ $actionList->kepuasan_pelanggan === 'Puashati' ? 'text-emerald-800' : ($actionList->kepuasan_pelanggan === 'Tidak puashati' ? 'text-rose-800' : 'text-amber-800') }}">
                        <i class="fa-solid {{ $actionList->kepuasan_pelanggan === 'Puashati' ? 'fa-face-smile text-emerald-600' : ($actionList->kepuasan_pelanggan === 'Tidak puashati' ? 'fa-face-frown text-rose-600' : 'fa-face-meh text-amber-600') }} mr-1.5"></i>
                        {{ $actionList->kepuasan_pelanggan ?: 'Puashati' }}
                    </div>
                </div>

                @if($actionList->cadangan_pelanggan)
                    <div class="text-xs text-amber-950 font-medium">
                        <span class="font-bold">16. Cadangan:</span> "{{ $actionList->cadangan_pelanggan }}"
                    </div>
                @endif
            </div>

            <!-- 14. Tandatangan -->
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase">14. Tandatangan & Nama Pelanggan</div>
                <div class="font-bold text-slate-900 mt-0.5">{{ $actionList->tandatangan_pelanggan_nama ?: ($actionList->nama_pelanggan ?: '-') }}</div>
                <div class="text-[11px] text-slate-500">
                    Tarikh: {{ $actionList->tandatangan_pelanggan_tarikh ? $actionList->tandatangan_pelanggan_tarikh->format('d/m/Y') : '-' }} ({{ $actionList->tandatangan_pelanggan_masa ?: '-' }})
                </div>
            </div>

            <!-- 18. Bayaran -->
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase">18. Bayaran Perkhidmatan (RM)</div>
                <div class="text-base font-black text-amber-600 mt-0.5">RM {{ number_format($actionList->bayaran, 2) }}</div>
                <div class="text-[11px] font-mono text-slate-500">No. Resit: {{ $actionList->no_resit ?: 'Tiada Resit / Dikecualikan' }}</div>
            </div>

            <!-- 19. Pengesahan dan Ulasan Pegawai -->
            <div class="lg:col-span-3">
                <div class="text-[11px] font-bold text-slate-400 uppercase mb-1">19. Pengesahan dan Ulasan Pegawai Projek:</div>
                <div class="p-3.5 rounded-2xl bg-emerald-50/50 border border-emerald-200 text-slate-800 leading-relaxed font-medium">
                    {{ $actionList->pengesahan_ulasan_pegawai ?: 'Perkhidmatan veterinar telah disahkan dan direkodkan dengan sempurna mengikut tatacara jabatan.' }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
