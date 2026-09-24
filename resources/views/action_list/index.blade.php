@extends('layouts.app')

@section('title', 'Borang Action List (PK-RK-61) - JPVNK')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gradient-to-r from-emerald-900 via-teal-900 to-slate-900 p-6 rounded-3xl text-white shadow-xl">
        <div class="space-y-1">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-bold border border-emerald-500/30">
                <i class="fa-solid fa-clipboard-list"></i> KOD DOKUMEN: PK-RK-61
            </div>
            <h1 class="text-2xl font-black tracking-tight">Action List Pejabat Perkhidmatan Veterinar Jajahan</h1>
            <p class="text-xs text-slate-300">
                Pengurusan rekod perkhidmatan lapangan, rawatan klinik jajahan, pemantauan projek pawah & pengakuan pelanggan mengikut jajahan.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('action-list.create') }}" class="px-5 py-2.5 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-sm shadow-lg transition flex items-center gap-2">
                <i class="fa-solid fa-plus-circle"></i> Isi Borang Action List (PK-RK-61)
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase">Jumlah Borang</span>
                <span class="p-2 bg-emerald-50 text-emerald-600 rounded-xl text-sm"><i class="fa-solid fa-file-lines"></i></span>
            </div>
            <div class="text-2xl font-black text-slate-800 mt-2">{{ number_format($totalRecords) }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Keseluruhan rekod PK-RK-61</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase">Jumlah Kutipan</span>
                <span class="p-2 bg-amber-50 text-amber-600 rounded-xl text-sm"><i class="fa-solid fa-money-bill-wave"></i></span>
            </div>
            <div class="text-2xl font-black text-amber-600 mt-2">RM {{ number_format($totalKutipan, 2) }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Hasil perkhidmatan & ubat</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase">Rawatan Lapangan</span>
                <span class="p-2 bg-blue-50 text-blue-600 rounded-xl text-sm"><i class="fa-solid fa-truck-medical"></i></span>
            </div>
            <div class="text-2xl font-black text-blue-600 mt-2">{{ number_format($rawatanLapanganCount) }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Lawatan luar & ladang</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase">Rawatan Klinik</span>
                <span class="p-2 bg-purple-50 text-purple-600 rounded-xl text-sm"><i class="fa-solid fa-house-medical"></i></span>
            </div>
            <div class="text-2xl font-black text-purple-600 mt-2">{{ number_format($rawatanKlinikCount) }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Rawatan & surgeri klinik</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase">Pemantauan Projek</span>
                <span class="p-2 bg-teal-50 text-teal-600 rounded-xl text-sm"><i class="fa-solid fa-handshake-angle"></i></span>
            </div>
            <div class="text-2xl font-black text-teal-600 mt-2">{{ number_format($pemantauanPawahCount + $pemantauanProjekCount) }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Pawah, TRUST & berjadual</div>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
        <form action="{{ route('action-list.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Carian Teks -->
                <div class="lg:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Carian Pantas</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="No. Bil, Nama Pelanggan, No. K/P, Telefon..." class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-emerald-500">
                        <i class="fa-solid fa-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                    </div>
                </div>

                <!-- Penapis Jajahan -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Jajahan</label>
                    <select name="jajahan" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-200 focus:outline-emerald-500 font-semibold text-slate-700">
                        <option value="Semua" {{ ($selectedJajahan === 'Semua' || !$selectedJajahan) ? 'selected' : '' }}>Semua Jajahan</option>
                        @foreach($jajahanList as $jjh)
                            <option value="{{ $jjh }}" {{ $selectedJajahan === $jjh ? 'selected' : '' }}>Jajahan {{ $jjh }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Kategori Pelanggan -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Kategori</label>
                    <select name="kategori" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-200 focus:outline-emerald-500 font-semibold text-slate-700">
                        <option value="">Semua Kategori</option>
                        <option value="Individu" {{ request('kategori') === 'Individu' ? 'selected' : '' }}>Individu</option>
                        <option value="Syarikat" {{ request('kategori') === 'Syarikat' ? 'selected' : '' }}>Syarikat</option>
                    </select>
                </div>

                <!-- Butang Tapis -->
                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full py-2 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-filter"></i> Tapis
                    </button>
                    <a href="{{ route('action-list.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition" title="Reset">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Table of Action Lists -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-sm text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-list-check text-emerald-600"></i> Rekod Borang Action List (PK-RK-61)
            </h3>
            <span class="text-xs text-slate-400">Memaparkan {{ $actionLists->count() }} daripada {{ $actionLists->total() }} rekod</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-4">No. Bil / Siri</th>
                        <th class="py-3 px-4">Tarikh & Masa</th>
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4">Jajahan / Mukim</th>
                        <th class="py-3 px-4">Perkhidmatan</th>
                        <th class="py-3 px-4">Ternakan</th>
                        <th class="py-3 px-4">Bayaran</th>
                        <th class="py-3 px-4 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($actionLists as $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- No. Bil -->
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                <div class="text-emerald-700 font-black">{{ $item->no_bil ?: ('BIL-' . $item->id) }}</div>
                                <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-mono bg-slate-100 text-slate-600 border border-slate-200">
                                    {{ $item->kod_dokumen ?? 'PK-RK-61' }}
                                </span>
                            </td>

                            <!-- Tarikh -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-bold text-slate-800">{{ $item->tarikh ? $item->tarikh->format('d/m/Y') : '-' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $item->masa_pendaftaran ?: '-' }}</div>
                            </td>

                            <!-- Pelanggan -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900">{{ $item->nama_pelanggan }}</div>
                                <div class="text-[11px] font-mono text-slate-500">K/P: {{ $item->no_kp ?: '-' }}</div>
                                <div class="text-[10px] text-slate-400">Tel: {{ $item->telefon ?: '-' }}</div>
                            </td>

                            <!-- Jajahan / Mukim -->
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    {{ $item->jajahan }}
                                </span>
                                @if($item->mukim)
                                    <div class="text-[11px] text-slate-500 mt-1">Mukim {{ $item->mukim }}</div>
                                @endif
                            </td>

                            <!-- Perkhidmatan -->
                            <td class="py-3.5 px-4">
                                @php
                                    $srv = is_array($item->perkhidmatan_diberi) ? $item->perkhidmatan_diberi : [];
                                    $activeServices = [];
                                    if (!empty($srv['rawatan_lapangan'])) $activeServices[] = 'Lapangan';
                                    if (!empty($srv['rawatan_klinik'])) $activeServices[] = 'Klinik';
                                    if (!empty($srv['pembedahan'])) $activeServices[] = 'Pembedahan';
                                    if (!empty($srv['pemantauan_pawah'])) $activeServices[] = 'Pawah';
                                    if (!empty($srv['pemantauan_projek'])) $activeServices[] = 'Projek';
                                    if (!empty($srv['pemantauan_trust'])) $activeServices[] = 'TRUST';
                                    if (!empty($srv['lawatan_terancang'])) $activeServices[] = 'Lawatan';
                                    if (!empty($srv['perkhidmatan_lain'])) $activeServices[] = 'Lain-lain';
                                @endphp
                                <div class="flex flex-wrap gap-1">
                                    @forelse($activeServices as $s)
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">{{ $s }}</span>
                                    @empty
                                        <span class="text-[11px] text-slate-400 italic">-</span>
                                    @endforelse
                                </div>
                            </td>

                            <!-- Ternakan -->
                            <td class="py-3.5 px-4">
                                @php
                                    $animals = is_array($item->jenis_ternakan) ? $item->jenis_ternakan : [];
                                @endphp
                                <div class="font-semibold text-slate-800">
                                    {{ !empty($animals) ? implode(', ', $animals) : '-' }}
                                </div>
                                @if($item->bil_ternakan)
                                    <div class="text-[11px] text-slate-500">Bil: {{ $item->bil_ternakan }} ekor</div>
                                @endif
                            </td>

                            <!-- Bayaran -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-bold {{ $item->bayaran > 0 ? 'text-amber-600' : 'text-slate-500' }}">
                                    RM {{ number_format($item->bayaran, 2) }}
                                </div>
                                @if($item->no_resit)
                                    <div class="text-[10px] font-mono text-slate-400">Resit: {{ $item->no_resit }}</div>
                                @endif
                            </td>

                            <!-- Tindakan -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('action-list.show', $item->id) }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition" title="Lihat Butiran">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('action-list.cetak', $item->id) }}" target="_blank" class="p-1.5 rounded-lg bg-amber-100 hover:bg-amber-200 text-amber-900 transition font-bold" title="Cetak Borang Rasmi PK-RK-61">
                                        <i class="fa-solid fa-print text-xs"></i>
                                    </a>
                                    <a href="{{ route('action-list.edit', $item->id) }}" class="p-1.5 rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-900 transition" title="Kemaskini">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </a>
                                    @if(Auth::user()->isSuperAdmin() || Auth::user()->isAdminJajahan())
                                        <form action="{{ route('action-list.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Adakah anda pasti mahu memadam rekod Borang Action List ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg bg-rose-100 hover:bg-rose-200 text-rose-700 transition" title="Padam Rekod">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-slate-400">
                                <i class="fa-solid fa-clipboard-question text-4xl mb-3 block text-slate-300"></i>
                                <p class="text-sm font-semibold">Tiada rekod Borang Action List dijumpai.</p>
                                <a href="{{ route('action-list.create') }}" class="mt-3 inline-block px-4 py-2 rounded-xl bg-emerald-600 text-white font-bold text-xs shadow-xs hover:bg-emerald-700 transition">
                                    + Isi Borang Pertama
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($actionLists->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $actionLists->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
