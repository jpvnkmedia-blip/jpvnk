@extends('layouts.app')

@section('title', 'Profil Penternak: ' . $pemunya->nama . ' - EPTR')
@section('page_title', 'Profil Penternak & Pemunya Ternakan')

@section('content')
<div class="space-y-6">

    <!-- Top Navigation & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('eptr.penternak.index') }}" class="p-2.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition shadow-2xs" title="Kembali ke Senarai Penternak">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-extrabold text-slate-900">{{ $pemunya->nama }}</h2>
                    @if($pemunya->status === 'Aktif')
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Aktif
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                            {{ $pemunya->status ?: 'Tidak Aktif' }}
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 font-mono mt-0.5">No. KP: {{ $pemunya->no_kp }} &bull; Jajahan: {{ $pemunya->jajahan }}</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('eptr.index', ['pemunya_id' => $pemunya->id]) }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-1.5 shadow-2xs">
                <i class="fa-solid fa-cow text-amber-600"></i>
                <span>Ternakan di EPTR</span>
            </a>
            @if(!Auth::user()->isStaff() || Auth::user()->isSuperAdmin())
            <a href="{{ route('eptr.create', ['pemunya_id' => $pemunya->id]) }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-lg shadow-emerald-700/30 transition flex items-center gap-1.5">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Daftar Ternakan Baharu</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Farmer Details Header Card & Livestock Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Maklumat Pemunya -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200 p-5 sm:p-6 shadow-xs">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-sky-100 text-sky-800 font-black flex items-center justify-center text-lg shadow-inner">
                        {{ strtoupper(substr($pemunya->nama, 0, 2)) }}
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">{{ $pemunya->nama }}</h3>
                        <p class="text-xs text-slate-500 font-mono">Didaftarkan dalam sistem: {{ $pemunya->created_at ? $pemunya->created_at->format('d/m/Y') : '-' }}</p>
                    </div>
                </div>
                @if($pemunya->user)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-medium">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        <span>Akaun Aktif</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-50 text-slate-600 border border-slate-200 text-xs font-medium">
                        <i class="fa-solid fa-user-clock text-slate-400"></i>
                        <span>Pengguna Luar Talian</span>
                    </span>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="space-y-1">
                    <span class="text-slate-400 font-medium block">No. Kad Pengenalan:</span>
                    <span class="font-bold text-slate-800 font-mono text-sm">{{ $pemunya->no_kp }}</span>
                </div>
                <div class="space-y-1">
                    <span class="text-slate-400 font-medium block">No. Telefon / WhatsApp:</span>
                    <span class="font-bold text-slate-800">
                        @if($pemunya->no_telefon)
                            <a href="tel:{{ $pemunya->no_telefon }}" class="text-sky-600 hover:underline flex items-center gap-1">
                                <i class="fa-solid fa-phone text-xs"></i>
                                <span>{{ $pemunya->no_telefon }}</span>
                            </a>
                        @else
                            <span class="text-slate-400 italic">Tiada no. telefon</span>
                        @endif
                    </span>
                </div>
                <div class="space-y-1 sm:col-span-2">
                    <span class="text-slate-400 font-medium block">Alamat Kediaman / Surat-Menyurat:</span>
                    <span class="font-medium text-slate-800">{{ $pemunya->alamat ?: 'Alamat belum dikemaskini' }}</span>
                </div>
                <div class="space-y-1">
                    <span class="text-slate-400 font-medium block">Jajahan:</span>
                    <span class="font-bold text-slate-800">{{ $pemunya->jajahan }}</span>
                </div>
                <div class="space-y-1">
                    <span class="text-slate-400 font-medium block">Daerah / Mukim & Poskod:</span>
                    <span class="font-medium text-slate-800">{{ $pemunya->daerah ?: '-' }} {{ $pemunya->poskod ? "({$pemunya->poskod})" : '' }}</span>
                </div>
                @if(!empty($pemunya->lokasi_kandang))
                <div class="space-y-1 sm:col-span-2">
                    <span class="text-slate-400 font-medium block">Lokasi Kandang / Tapak Ternakan:</span>
                    <span class="font-medium text-slate-800">{{ $pemunya->lokasi_kandang }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Inventori Ternakan Stat Cards -->
        <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-5 sm:p-6 text-white flex flex-col justify-between shadow-md">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-700">
                    <span class="text-xs font-bold text-slate-300 uppercase tracking-wider">Inventori Ternakan</span>
                    <span class="px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-black">
                        {{ $pemunya->ternakan_aktif_count }} Ekor Aktif
                    </span>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <div class="p-3 rounded-2xl bg-white/5 border border-white/10 flex items-center gap-3">
                        <span class="text-2xl">🐮</span>
                        <div>
                            <div class="text-lg font-black text-white">{{ $pemunya->lembu_count }}</div>
                            <div class="text-[10px] text-slate-300">Lembu</div>
                        </div>
                    </div>
                    <div class="p-3 rounded-2xl bg-white/5 border border-white/10 flex items-center gap-3">
                        <span class="text-2xl">🐐</span>
                        <div>
                            <div class="text-lg font-black text-white">{{ $pemunya->kambing_count }}</div>
                            <div class="text-[10px] text-slate-300">Kambing</div>
                        </div>
                    </div>
                    <div class="p-3 rounded-2xl bg-white/5 border border-white/10 flex items-center gap-3">
                        <span class="text-2xl">🦬</span>
                        <div>
                            <div class="text-lg font-black text-white">{{ $pemunya->kerbau_count }}</div>
                            <div class="text-[10px] text-slate-300">Kerbau</div>
                        </div>
                    </div>
                    <div class="p-3 rounded-2xl bg-white/5 border border-white/10 flex items-center gap-3">
                        <span class="text-2xl">🐑</span>
                        <div>
                            <div class="text-lg font-black text-white">{{ $pemunya->biri_count }}</div>
                            <div class="text-[10px] text-slate-300">Biri-biri</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 pt-3 border-t border-slate-700/60 flex items-center justify-between text-[11px] text-slate-400">
                <span>Jumlah Keseluruhan Rekod:</span>
                <span class="font-bold text-white">{{ $pemunya->ternakan_count }} Ekor</span>
            </div>
        </div>
    </div>

    <!-- Tabs Container (Alpine.js) -->
    <div x-data="{ activeTab: 'ternakan' }" class="space-y-4">
        
        <!-- Tab Navigation Buttons -->
        <div class="flex items-center gap-2 border-b border-slate-200 pb-2">
            <button @click="activeTab = 'ternakan'" :class="activeTab === 'ternakan' ? 'bg-emerald-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-100'" class="px-4 py-2 rounded-xl font-bold text-xs transition flex items-center gap-2">
                <i class="fa-solid fa-cow"></i>
                <span>Senarai Ternakan ({{ $pemunya->ternakan->count() }})</span>
            </button>
            <button @click="activeTab = 'pindah_milik'" :class="activeTab === 'pindah_milik' ? 'bg-emerald-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-100'" class="px-4 py-2 rounded-xl font-bold text-xs transition flex items-center gap-2">
                <i class="fa-solid fa-arrows-rotate"></i>
                <span>Sejarah Pindah Milik (Borang B) ({{ $pindahMilikList->count() }})</span>
            </button>
            <button @click="activeTab = 'permit'" :class="activeTab === 'permit' ? 'bg-emerald-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-100'" class="px-4 py-2 rounded-xl font-bold text-xs transition flex items-center gap-2">
                <i class="fa-solid fa-file-invoice"></i>
                <span>Permit Sembelihan (Borang D) ({{ $pemunya->permitSembelihan->count() }})</span>
            </button>
        </div>

        <!-- TAB 1: SENARAI TERNAKAN -->
        <div x-show="activeTab === 'ternakan'" x-cloak class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xs">
            <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Senarai Ternakan Berdaftar Milik Penternak</h3>
                    <p class="text-xs text-slate-500">Rekod pendaftaran haiwan ruminan, nombor tag telinga rasmi dan kad kuning</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-bold text-[11px]">
                            <th class="px-4 py-3.5">No. Tag Telinga</th>
                            <th class="px-4 py-3.5">Jenis & Baka</th>
                            <th class="px-4 py-3.5">Jantina & Umur</th>
                            <th class="px-4 py-3.5">Lokasi Kandang</th>
                            <th class="px-4 py-3.5">Program / Skim</th>
                            <th class="px-4 py-3.5">Status</th>
                            <th class="px-5 py-3.5 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pemunya->ternakan as $t)
                            <tr class="hover:bg-slate-50/80 transition">
                                <!-- No Tag -->
                                <td class="px-4 py-3.5 font-mono font-bold text-emerald-800">
                                    @if($t->no_tag)
                                        <a href="{{ route('eptr.show', $t->id) }}" class="hover:underline flex items-center gap-1.5">
                                            <i class="fa-solid fa-tag text-emerald-600"></i>
                                            <span>{{ $t->no_tag }}</span>
                                        </a>
                                        @if($t->no_siri_kad_kuning)
                                            <span class="text-[10px] text-slate-400 font-mono block mt-0.5">{{ $t->no_siri_kad_kuning }}</span>
                                        @endif
                                    @else
                                        <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700 font-sans text-[11px] font-bold border border-amber-200">
                                            Menunggu Tag Rasmi
                                        </span>
                                    @endif
                                </td>

                                <!-- Jenis & Baka -->
                                <td class="px-4 py-3.5">
                                    <div class="font-bold text-slate-800">{{ $t->jenis_ternakan }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $t->baka }}</div>
                                </td>

                                <!-- Jantina & Umur -->
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-1.5 font-medium">
                                        @if($t->jantina === 'Jantan')
                                            <span class="text-blue-600 font-bold">♂ Jantan</span>
                                        @else
                                            <span class="text-rose-600 font-bold">♀ Betina</span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-slate-500">{{ $t->umur ?: '-' }}</div>
                                </td>

                                <!-- Lokasi Kandang -->
                                <td class="px-4 py-3.5">
                                    <div class="text-slate-700">{{ $t->lokasi_kandang ?: $t->jajahan }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $t->daerah ?: $t->jajahan }}</div>
                                </td>

                                <!-- Program / Skim -->
                                <td class="px-4 py-3.5">
                                    @if($t->program && $t->program !== 'Tiada')
                                        <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-800 border border-amber-200 font-medium text-[11px]">
                                            {{ $t->program }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 italic">Persendirian</span>
                                    @endif
                                </td>

                                <!-- Status -->
                                <td class="px-4 py-3.5">
                                    @if($t->status === 'Aktif')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Aktif
                                        </span>
                                    @elseif($t->status === 'Pawah')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                            Pawah
                                        </span>
                                    @elseif($t->isDibatalkanAtauMatiAtauSembelih())
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            {{ $t->status }}
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                            {{ $t->status }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Tindakan -->
                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('eptr.show', $t->id) }}" class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold transition flex items-center gap-1" title="Buka Kad Kuning Rasmi">
                                            <i class="fa-solid fa-id-card"></i>
                                            <span>Kad Kuning</span>
                                        </a>
                                        @if(!$t->isDibatalkanAtauMatiAtauSembelih())
                                            <a href="{{ route('eptr.borang-b.create', ['ternakan_id' => $t->id]) }}" class="px-2.5 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold transition" title="Pindah Milik Ternakan Ini">
                                                <i class="fa-solid fa-arrows-rotate"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                                    <i class="fa-solid fa-cow text-2xl mb-2 block"></i>
                                    <span>Penternak ini belum mempunyai sebarang pendaftaran ternakan.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 2: SEJARAH PINDAH MILIK (BORANG B) -->
        <div x-show="activeTab === 'pindah_milik'" x-cloak class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xs">
            <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Sejarah Pertukaran & Pemindahan Milikan Ternakan (Borang B)</h3>
                    <p class="text-xs text-slate-500">Rekod pemindahan pemilikan sama ada sebagai penjual (pemunya asal) atau pembeli (pemunya baru)</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-bold text-[11px]">
                            <th class="px-4 py-3.5">Peranan & Tarikh</th>
                            <th class="px-4 py-3.5">No. Tag Ternakan</th>
                            <th class="px-4 py-3.5">Pemunya Asal (Penjual)</th>
                            <th class="px-4 py-3.5">Pemunya Baharu (Pembeli)</th>
                            <th class="px-4 py-3.5">Sebab Pindah</th>
                            <th class="px-4 py-3.5">Status Kelulusan</th>
                            <th class="px-5 py-3.5 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pindahMilikList as $pm)
                            <tr class="hover:bg-slate-50/80 transition">
                                <!-- Peranan & Tarikh -->
                                <td class="px-4 py-3.5">
                                    @if($pm->pemunya_asal_id == $pemunya->id)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            Pindah Keluar (Penjual)
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Pindah Masuk (Pembeli)
                                        </span>
                                    @endif
                                    <div class="text-[11px] text-slate-500 font-mono mt-1">
                                        {{ $pm->tarikh_pindah ? $pm->tarikh_pindah->format('d/m/Y') : '-' }}
                                    </div>
                                </td>

                                <!-- No Tag Ternakan -->
                                <td class="px-4 py-3.5 font-mono font-bold text-slate-800">
                                    @if($pm->ternakan)
                                        <a href="{{ route('eptr.show', $pm->ternakan->id) }}" class="text-emerald-700 hover:underline">
                                            {{ $pm->ternakan->no_tag ?: 'ID #' . $pm->ternakan->id }}
                                        </a>
                                        <div class="text-[10px] text-slate-400 font-sans">{{ $pm->ternakan->jenis_ternakan }} ({{ $pm->ternakan->baka }})</div>
                                    @else
                                        <span class="text-slate-400 italic">Rekod Ternakan Dipadam</span>
                                    @endif
                                </td>

                                <!-- Pemunya Asal -->
                                <td class="px-4 py-3.5">
                                    <div class="font-bold text-slate-800">{{ $pm->pemunyaAsal ? $pm->pemunyaAsal->nama : '-' }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $pm->pemunyaAsal ? $pm->pemunyaAsal->no_kp : '' }}</div>
                                </td>

                                <!-- Pemunya Baharu -->
                                <td class="px-4 py-3.5">
                                    <div class="font-bold text-slate-800">{{ $pm->pemunyaBaru ? $pm->pemunyaBaru->nama : '-' }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $pm->pemunyaBaru ? $pm->pemunyaBaru->no_kp : '' }}</div>
                                </td>

                                <!-- Sebab Pindah -->
                                <td class="px-4 py-3.5">
                                    <div class="font-medium text-slate-700">{{ $pm->sebab_pindah }}</div>
                                    @if($pm->harga_jualan)
                                        <div class="text-[11px] text-slate-500">RM {{ number_format($pm->harga_jualan, 2) }}</div>
                                    @endif
                                </td>

                                <!-- Status Kelulusan -->
                                <td class="px-4 py-3.5">
                                    @if($pm->status_kelulusan === 'Diluluskan')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Diluluskan
                                        </span>
                                    @elseif($pm->status_kelulusan === 'Ditolak')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            Menunggu Kelulusan
                                        </span>
                                    @endif
                                </td>

                                <!-- Tindakan -->
                                <td class="px-5 py-3.5 text-right">
                                    <a href="{{ route('eptr.borang-b.show', $pm->id) }}" class="px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold transition inline-flex items-center gap-1">
                                        <i class="fa-solid fa-file-lines"></i>
                                        <span>Borang B</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                                    <i class="fa-solid fa-arrows-rotate text-2xl mb-2 block"></i>
                                    <span>Tiada rekod pindah milik atau pertukaran milikan bagi penternak ini.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 3: PERMIT SEMBELIHAN (BORANG D) -->
        <div x-show="activeTab === 'permit'" x-cloak class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xs">
            <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Rekod Permit Sembelihan Ternakan (Borang D)</h3>
                    <p class="text-xs text-slate-500">Permit sembelihan ruminan yang didaftarkan atas nama penternak ini</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-bold text-[11px]">
                            <th class="px-4 py-3.5">No. Permit</th>
                            <th class="px-4 py-3.5">Ternakan</th>
                            <th class="px-4 py-3.5">Tarikh Sembelih</th>
                            <th class="px-4 py-3.5">Tempat / Rumah Sembelih</th>
                            <th class="px-4 py-3.5">Tujuan Sembelihan</th>
                            <th class="px-4 py-3.5">Status Kelulusan</th>
                            <th class="px-5 py-3.5 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pemunya->permitSembelihan as $permit)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3.5 font-mono font-bold text-amber-800">
                                    {{ $permit->no_permit }}
                                </td>
                                <td class="px-4 py-3.5 font-mono font-bold text-slate-800">
                                    @if($permit->ternakan)
                                        <a href="{{ route('eptr.show', $permit->ternakan->id) }}" class="text-emerald-700 hover:underline">
                                            {{ $permit->ternakan->no_tag ?: 'ID #' . $permit->ternakan->id }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 font-mono text-slate-600">
                                    {{ $permit->tarikh_sembelih ? \Carbon\Carbon::parse($permit->tarikh_sembelih)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-4 py-3.5 text-slate-700">
                                    {{ $permit->tempat_sembelih ?: '-' }}
                                </td>
                                <td class="px-4 py-3.5 text-slate-700">
                                    {{ $permit->tujuan_sembelihan ?: '-' }}
                                </td>
                                <td class="px-4 py-3.5">
                                    @if($permit->status_kelulusan === 'Diluluskan')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Diluluskan
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            {{ $permit->status_kelulusan }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a href="{{ route('eptr.borang-d.show', $permit->id) }}" class="px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold transition inline-flex items-center gap-1">
                                        <i class="fa-solid fa-file-invoice"></i>
                                        <span>Borang D</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                                    <i class="fa-solid fa-file-invoice text-2xl mb-2 block"></i>
                                    <span>Tiada rekod permit sembelihan bagi penternak ini.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
