@extends('layouts.app')

@section('title', 'Maklumat Permohonan Pemindahan: ' . $pemindahan->no_rujukan)
@section('page_title', 'Butiran Pemindahan Ternakan')

@section('content')
<div class="space-y-6">

    <!-- Top Action & Navigation Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <a href="{{ route('eptr.pemindahan.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-indigo-600 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Senarai Permohonan</span>
        </a>

        <!-- Print Action Buttons Dropdown / Row -->
        @if(Auth::user()->isStaff())
            @if($pemindahan->status === 'Diluluskan')
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('eptr.pemindahan.cetak-set-lengkap', $pemindahan->id) }}" target="_blank" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-lg transition flex items-center gap-2 border border-slate-700">
                    <i class="fa-solid fa-print text-amber-400"></i>
                    <span>Cetak 1 Set Lengkap (4 Halaman)</span>
                </a>

                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 font-bold text-xs rounded-xl hover:bg-slate-50 transition flex items-center gap-2">
                        <i class="fa-solid fa-file-pdf text-indigo-500"></i>
                        <span>Cetak Dokumen Individu</span>
                        <i class="fa-solid fa-chevron-down text-[10px]"></i>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-cloak class="absolute right-0 mt-2 w-64 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 py-2 z-50 text-xs font-semibold">
                        <a href="{{ route('eptr.pemindahan.cetak-surat-fmd', $pemindahan->id) }}" target="_blank" class="flex items-center gap-2.5 px-4 py-2 hover:bg-slate-50 dark:hover:bg-slate-700/60 text-slate-700 dark:text-slate-200">
                            <i class="fa-solid fa-file-lines text-indigo-500 w-4"></i>
                            <span>1. Surat Pengesahan FMD (Kn. 156)</span>
                        </a>
                        <a href="{{ route('eptr.pemindahan.cetak-borang', $pemindahan->id) }}" target="_blank" class="flex items-center gap-2.5 px-4 py-2 hover:bg-slate-50 dark:hover:bg-slate-700/60 text-slate-700 dark:text-slate-200">
                            <i class="fa-solid fa-file-contract text-emerald-500 w-4"></i>
                            <span>2. Borang Permohonan Pemindahan</span>
                        </a>
                        <a href="{{ route('eptr.pemindahan.cetak-deklarasi', $pemindahan->id) }}" target="_blank" class="flex items-center gap-2.5 px-4 py-2 hover:bg-slate-50 dark:hover:bg-slate-700/60 text-slate-700 dark:text-slate-200">
                            <i class="fa-solid fa-shield-halved text-cyan-500 w-4"></i>
                            <span>3. Deklarasi Kesihatan (DVS/DSHR)</span>
                        </a>
                        <a href="{{ route('eptr.pemindahan.cetak-lampiran-tag', $pemindahan->id) }}" target="_blank" class="flex items-center gap-2.5 px-4 py-2 hover:bg-slate-50 dark:hover:bg-slate-700/60 text-slate-700 dark:text-slate-200">
                            <i class="fa-solid fa-tags text-amber-500 w-4"></i>
                            <span>4. Lampiran Senarai No. Tag</span>
                        </a>
                    </div>
                </div>
            </div>
            @else
            <div class="px-4 py-2.5 rounded-xl bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 text-amber-700 dark:text-amber-300 text-xs font-semibold flex items-center gap-2">
                <i class="fa-solid fa-lock"></i>
                <span>Cetakan dokumen permit hanya dibenarkan selepas permohonan diluluskan oleh Pegawai.</span>
            </div>
            @endif
        @endif
    </div>

    <!-- Status & Overview Banner -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 sm:p-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-slate-200 dark:border-slate-700">
            <div>
                <div class="flex items-center gap-3">
                    <span class="font-mono text-xl font-black text-indigo-600 dark:text-indigo-400">{{ $pemindahan->no_rujukan }}</span>
                    @if($pemindahan->status === 'Diluluskan')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                            <i class="fa-solid fa-circle-check"></i> Diluluskan
                        </span>
                    @elseif($pemindahan->status === 'Ditolak')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
                            <i class="fa-solid fa-circle-xmark"></i> Ditolak
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                            <i class="fa-solid fa-clock"></i> Menunggu Kelulusan
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5">
                    Permohonan didaftarkan pada <strong>{{ $pemindahan->tarikh_permohonan ? $pemindahan->tarikh_permohonan->format('d/m/Y') : date('d/m/Y') }}</strong> di bawah <strong>Pejabat Perkhidmatan Veterinar Jajahan {{ $pemindahan->jajahan_asal }}</strong>.
                </p>
            </div>

            <!-- Approval Actions (For Staff/Admins) -->
            @if(Auth::user()->isStaff() || Auth::user()->isSuperAdmin())
                @if($pemindahan->status === 'Menunggu Kelulusan')
                <div class="flex flex-wrap items-center gap-2.5" x-data="{ showModal: false }">
                    <button @click="showModal = true" type="button" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-900/40 transition flex items-center gap-2">
                        <i class="fa-solid fa-check"></i>
                        <span>Luluskan Permohonan</span>
                    </button>
                    <form action="{{ route('eptr.pemindahan.tolak', $pemindahan->id) }}" method="POST" onsubmit="return confirm('Adakah anda pasti ingin MENOLAK permohonan ini?');">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs rounded-xl shadow-lg transition flex items-center gap-2">
                            <i class="fa-solid fa-xmark"></i>
                            <span>Tolak</span>
                        </button>
                    </form>

                    <!-- Modal Kelulusan Pegawai & Pemilihan Jajahan Letterhead -->
                    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
                        <div @click.outside="showModal = false" class="bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200 dark:border-slate-700 text-left space-y-4">
                            <div class="flex items-center justify-between border-b pb-3 dark:border-slate-700">
                                <h3 class="text-base font-black text-slate-800 dark:text-white flex items-center gap-2">
                                    <i class="fa-solid fa-stamp text-emerald-600"></i>
                                    <span>Kelulusan Permit Pemindahan</span>
                                </h3>
                                <button @click="showModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
                            </div>
                            <form action="{{ route('eptr.pemindahan.lulus', $pemindahan->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">Pejabat Veterinar Jajahan Kelulusan (Kepala Surat / Letterhead)</label>
                                    <select name="pegawai_jajahan" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white font-bold">
                                        @foreach(array_keys(config('kelantan.pejabat', [])) as $j)
                                            <option value="{{ $j }}" {{ ($pemindahan->pegawai_jajahan ?: (Auth::user()->jajahan ?: $pemindahan->jajahan_asal)) === $j ? 'selected' : '' }}>
                                                Pejabat Perkhidmatan Veterinar Jajahan {{ $j }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="text-[11px] text-slate-400 mt-1">Kepala surat (*letterhead*), alamat, nombor telefon dan faks pada dokumen permit cetakan akan dijana secara automatik mengikut pejabat jajahan ini.</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">Nama Pegawai Yang Meluluskan</label>
                                    <input type="text" name="pegawai_nama" value="{{ Auth::user()->name }}" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white font-bold uppercase">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">Catatan Kelulusan (Pilihan)</label>
                                    <textarea name="catatan_kelulusan" rows="2" placeholder="Catatan tambahan pegawai (jika ada)..." class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white"></textarea>
                                </div>
                                <div class="flex justify-end gap-2 pt-2 border-t dark:border-slate-700">
                                    <button type="button" @click="showModal = false" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl">Batal</button>
                                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-900/30">Sahkan &amp; Luluskan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
            @endif
        </div>

        <!-- 3-Column Info Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-6 text-sm">
            <!-- Col 1: Maklumat Pemohon -->
            <div class="space-y-3 bg-slate-50 dark:bg-slate-900/30 p-4 rounded-2xl border border-slate-100 dark:border-slate-700/60">
                <div class="flex items-center gap-2 text-xs font-black uppercase text-indigo-600 tracking-wider">
                    <i class="fa-solid fa-user"></i>
                    <span>Maklumat Pemohon / Penghantar</span>
                </div>
                <div class="space-y-1 text-xs">
                    <div><span class="text-slate-400">Nama:</span> <strong class="text-slate-800 dark:text-white uppercase">{{ $pemindahan->pemohon_nama }}</strong></div>
                    <div><span class="text-slate-400">No. IC:</span> <span class="font-mono font-bold">{{ $pemindahan->pemohon_ic ?: '-' }}</span></div>
                    <div><span class="text-slate-400">No. Tel:</span> <span>{{ $pemindahan->pemohon_tel ?: '-' }}</span></div>
                    <div><span class="text-slate-400">ID Premis:</span> <span class="font-mono font-bold text-indigo-600">{{ $pemindahan->pemohon_id_premis ?: '-' }}</span></div>
                    <div><span class="text-slate-400">Alamat:</span> <span class="uppercase">{{ $pemindahan->pemohon_alamat }}</span></div>
                </div>
            </div>

            <!-- Col 2: Maklumat Penerima & Destinasi -->
            <div class="space-y-3 bg-slate-50 dark:bg-slate-900/30 p-4 rounded-2xl border border-slate-100 dark:border-slate-700/60">
                <div class="flex items-center gap-2 text-xs font-black uppercase text-cyan-600 tracking-wider">
                    <i class="fa-solid fa-location-dot"></i>
                    <span>Maklumat Penerima &amp; Destinasi</span>
                </div>
                <div class="space-y-1 text-xs">
                    <div><span class="text-slate-400">Nama Penerima:</span> <strong class="text-slate-800 dark:text-white uppercase">{{ $pemindahan->penerima_nama }}</strong></div>
                    <div><span class="text-slate-400">ID Premis:</span> <span class="font-mono font-bold text-cyan-600">{{ $pemindahan->penerima_id_premis ?: '-' }}</span></div>
                    <div><span class="text-slate-400">No. IC / Tel:</span> <span>{{ $pemindahan->penerima_ic ?: '-' }} / {{ $pemindahan->penerima_tel ?: '-' }}</span></div>
                    <div><span class="text-slate-400">Lokasi:</span> <span class="uppercase">{{ $pemindahan->penerima_alamat }}</span></div>
                    <div><span class="text-slate-400">Jajahan / Negeri:</span> <span class="font-bold">{{ $pemindahan->penerima_jajahan ?: '-' }}, {{ $pemindahan->penerima_negeri }}</span></div>
                </div>
            </div>

            <!-- Col 3: Ternakan, Lori & Tarikh -->
            <div class="space-y-3 bg-slate-50 dark:bg-slate-900/30 p-4 rounded-2xl border border-slate-100 dark:border-slate-700/60">
                <div class="flex items-center gap-2 text-xs font-black uppercase text-emerald-600 tracking-wider">
                    <i class="fa-solid fa-truck"></i>
                    <span>Ternakan, Lori &amp; Perjalanan</span>
                </div>
                <div class="space-y-1 text-xs">
                    <div><span class="text-slate-400">Tujuan:</span> <strong class="uppercase text-indigo-600 font-black">{{ $pemindahan->tujuan_pemindahan }}</strong></div>
                    <div><span class="text-slate-400">Spesies:</span> <span class="font-bold">{{ $pemindahan->jenis_ternakan }}</span></div>
                    <div><span class="text-slate-400">Kuantiti:</span> <strong class="text-emerald-700 dark:text-emerald-400">{{ $pemindahan->format_kuantiti_jantina }}</strong></div>
                    <div><span class="text-slate-400">No. Plat Kenderaan:</span> <span class="font-mono font-bold uppercase">{{ $pemindahan->no_kenderaan ?: '-' }}</span></div>
                    <div><span class="text-slate-400">Tarikh Bertolak:</span> <strong class="text-slate-800 dark:text-white">{{ $pemindahan->tarikh_jangka_pindah ? $pemindahan->tarikh_jangka_pindah->format('d/m/Y') : '-' }}</strong></div>
                </div>
            </div>
        </div>

        <!-- Suntikan FMD & LSD Bar -->
        <div class="mt-6 p-4 rounded-2xl bg-indigo-50/50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 text-xs">
            <div class="font-bold text-indigo-900 dark:text-indigo-300 uppercase mb-2 flex items-center gap-2">
                <i class="fa-solid fa-syringe"></i>
                <span>Maklumat Suntikan Vaksinasi &amp; Pegawai Penyuntik</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-slate-700 dark:text-slate-300">
                <div>
                    <span class="text-slate-400">FMD (P1):</span>
                    <strong class="font-mono ml-1">{{ $pemindahan->tarikh_fmd_p1 ? $pemindahan->tarikh_fmd_p1->format('d/m/Y') : '-' }}</strong>
                </div>
                <div>
                    <span class="text-slate-400">FMD (P2):</span>
                    <strong class="font-mono ml-1">{{ $pemindahan->tarikh_fmd_p2 ? $pemindahan->tarikh_fmd_p2->format('d/m/Y') : '-' }}</strong>
                </div>
                <div>
                    <span class="text-slate-400">FMD Booster:</span>
                    <strong class="font-mono ml-1">{{ $pemindahan->tarikh_fmd_booster ? $pemindahan->tarikh_fmd_booster->format('d/m/Y') : '-' }}</strong>
                </div>
                <div>
                    <span class="text-slate-400">Suntikan LSD:</span>
                    <strong class="font-mono ml-1">{{ $pemindahan->tarikh_lsd ? $pemindahan->tarikh_lsd->format('d/m/Y') : '-' }}</strong>
                </div>
            </div>
            <div class="mt-2 pt-2 border-t border-indigo-200/60 dark:border-indigo-800/60 flex flex-wrap gap-4 text-[11px] text-slate-600 dark:text-slate-400">
                <span>Penyuntik 1: <strong class="uppercase">{{ $pemindahan->nama_penyuntik_1 ?: '-' }}</strong></span>
                <span>Penyuntik 2: <strong class="uppercase">{{ $pemindahan->nama_penyuntik_2 ?: '-' }}</strong></span>
            </div>
        </div>
    </div>

    <!-- Table 50 Tags Section -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 sm:p-8 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-base font-black text-slate-800 dark:text-white">Lampiran: Senarai Pengenalan Ternakan (50 Tag)</h3>
                <p class="text-xs text-slate-500">Nombor tag ternakan yang didaftarkan dalam permit pemindahan ini</p>
            </div>
            <div class="px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-700 text-xs font-bold font-mono">
                {{ count($pemindahan->senarai_tag ?? []) }} / 50 Baris Diisi
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
            <!-- Kolum 1 (1 - 25) -->
            <div class="border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-700 font-bold uppercase text-slate-500">
                        <tr>
                            <th class="py-2 px-3 w-12 text-center">BIL</th>
                            <th class="py-2 px-3">NO TAG</th>
                            <th class="py-2 px-3 w-24 text-center">JANTINA</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 font-mono">
                        @for($i = 1; $i <= 25; $i++)
                            @php $t = ($pemindahan->senarai_tag ?? [])[$i - 1] ?? null; @endphp
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-1.5 px-3 text-center text-slate-400">{{ $i }}.</td>
                                <td class="py-1.5 px-3 font-bold text-slate-800 dark:text-slate-200">{{ $t['no_tag'] ?? ($t ?? '') }}</td>
                                <td class="py-1.5 px-3 text-center font-sans">{{ $t['jantina'] ?? '' }}</td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            <!-- Kolum 2 (26 - 50) -->
            <div class="border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-700 font-bold uppercase text-slate-500">
                        <tr>
                            <th class="py-2 px-3 w-12 text-center">BIL</th>
                            <th class="py-2 px-3">NO TAG</th>
                            <th class="py-2 px-3 w-24 text-center">JANTINA</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 font-mono">
                        @for($i = 26; $i <= 50; $i++)
                            @php $t = ($pemindahan->senarai_tag ?? [])[$i - 1] ?? null; @endphp
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-1.5 px-3 text-center text-slate-400">{{ $i }}.</td>
                                <td class="py-1.5 px-3 font-bold text-slate-800 dark:text-slate-200">{{ $t['no_tag'] ?? ($t ?? '') }}</td>
                                <td class="py-1.5 px-3 text-center font-sans">{{ $t['jantina'] ?? '' }}</td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
