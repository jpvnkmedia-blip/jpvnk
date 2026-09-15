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

    <!-- Official Gazetted Forms Printable Hub (Enakmen 2005) -->
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

    <!-- History of EPU Licenses & Inspections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Left: Senarai Lesen Unggas (Borang B) -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-certificate text-amber-500 text-base"></i>
                    <h3 class="text-sm font-bold text-slate-900">Sejarah Lesen Perladangan Unggas</h3>
                </div>
                <span class="text-xs font-bold text-amber-800">{{ $ladang->permohonanList->count() }} Lesen</span>
            </div>

            <div class="space-y-3">
                @forelse($ladang->permohonanList as $permohonan)
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-mono font-bold text-amber-900">{{ $permohonan->no_lesen_epu ?? $permohonan->no_rujukan_permohonan }}</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $permohonan->status === 'Diluluskan' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $permohonan->status }}
                            </span>
                        </div>
                        <div class="text-slate-700">
                            Jenis: <b>{{ $permohonan->jenis_unggas }}</b> &bull; Bilangan: <b>{{ number_format($permohonan->bilangan_semasa_unggas) }} Ekor</b>
                        </div>
                        <div class="text-[11px] text-slate-500 flex justify-between items-center pt-1 border-t border-slate-200">
                            <span>Tempoh: {{ $permohonan->tarikh_mula_lesen ? $permohonan->tarikh_mula_lesen->format('d/m/Y') : '-' }} &rarr; {{ $permohonan->tarikh_tamat_lesen ? $permohonan->tarikh_tamat_lesen->format('d/m/Y') : '-' }}</span>
                            @if($permohonan->status === 'Diluluskan')
                                <a href="{{ route('epu.cetak-lesen', $permohonan->id) }}" target="_blank" class="font-bold text-amber-700 hover:underline flex items-center gap-1">
                                    <i class="fa-solid fa-print"></i> Cetak Lesen Borang B
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center text-slate-400 text-xs">
                        Tiada rekod lesen.
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
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">
                                Skor: {{ $pem->skor_kebersihan_peratus }}% &bull; {{ $pem->status_keputusan }}
                            </span>
                        </div>
                        <p class="text-slate-700">{{ $pem->penemuan_pemeriksaan }}</p>
                        <div class="text-[11px] text-slate-500 pt-1 border-t border-slate-200">
                            Syor Pegawai: <b>{{ $pem->syor_dan_arahan }}</b> (Pegawai: {{ $pem->pegawai->name ?? '-' }})
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
@endsection
