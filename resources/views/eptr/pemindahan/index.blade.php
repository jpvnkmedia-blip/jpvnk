@extends('layouts.app')

@section('title', 'Senarai Permohonan Pemindahan Ternakan')
@section('page_title', 'Modul Pemindahan Ternakan')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Banner -->
    <div class="rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 text-xs font-semibold">
                    <i class="fa-solid fa-truck-moving"></i>
                    <span>Pengurusan &amp; Kebenaran Pemindahan Ternakan</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">
                    Permohonan Pemindahan Ternakan
                </h1>
                <p class="text-sm text-slate-300 max-w-2xl leading-relaxed">
                    Uruskan permohonan kebenaran memindah ternakan ruminan antara jajahan/negeri berserta pengesahan vaksinasi FMD/LSD, Deklarasi Kesihatan (DVS/DSHR), dan Lampiran Senarai Tag Ternakan.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('eptr.pemindahan.create') }}" class="inline-flex items-center gap-2.5 px-5 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm shadow-lg shadow-indigo-900/50 transition transform hover:-translate-y-0.5">
                    <i class="fa-solid fa-plus-circle text-base"></i>
                    <span>Mohon Pemindahan Baharu</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jumlah Permohonan</p>
                    <h3 class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($stats['total']) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-clipboard-list"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">Menunggu Kelulusan</p>
                    <h3 class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ number_format($stats['menunggu']) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Diluluskan</p>
                    <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($stats['diluluskan']) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider">Ditolak</p>
                    <h3 class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">{{ number_format($stats['ditolak']) }}</h3>
                </div>
                <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
        <form action="{{ route('eptr.pemindahan.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <div class="lg:col-span-2">
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">Carian Permohonan</label>
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="No Rujukan, Pemohon, Penerima, No Plat..." class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Semua Status --</option>
                    <option value="Menunggu Kelulusan" {{ request('status') === 'Menunggu Kelulusan' ? 'selected' : '' }}>Menunggu Kelulusan</option>
                    <option value="Diluluskan" {{ request('status') === 'Diluluskan' ? 'selected' : '' }}>Diluluskan</option>
                    <option value="Ditolak" {{ request('status') === 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase mb-1">Jajahan Asal</label>
                <select name="jajahan" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Semua Jajahan --</option>
                    @foreach(['Bachok', 'Kota Bharu', 'Pasir Mas', 'Tumpat', 'Pasir Puteh', 'Machang', 'Tanah Merah', 'Jeli', 'Kuala Krai', 'Gua Musang'] as $j)
                        <option value="{{ $j }}" {{ request('jajahan') === $j ? 'selected' : '' }}>{{ $j }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm rounded-xl transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-filter"></i>
                    <span>Tapis</span>
                </button>
                @if(request()->hasAny(['search', 'status', 'jajahan', 'tujuan']))
                <a href="{{ route('eptr.pemindahan.index') }}" class="px-3 py-2.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 text-slate-700 dark:text-slate-200 text-sm font-semibold rounded-xl transition flex items-center justify-center" title="Set Semula">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">Bil</th>
                        <th class="py-3.5 px-4">No. Rujukan Permit</th>
                        <th class="py-3.5 px-4">Maklumat Pemohon &amp; Jajahan</th>
                        <th class="py-3.5 px-4">Destinasi / Penerima</th>
                        <th class="py-3.5 px-4 text-center">Ternakan &amp; Kuantiti</th>
                        <th class="py-3.5 px-4">Tarikh Pindah</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 font-medium">
                    @forelse($pemindahanList as $index => $item)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/40 transition">
                        <td class="py-3 px-4 text-slate-400 text-xs font-mono">
                            {{ $pemindahanList->firstItem() + $index }}.
                        </td>
                        <td class="py-3 px-4">
                            <a href="{{ route('eptr.pemindahan.show', $item->id) }}" class="font-mono font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                                {{ $item->no_rujukan }}
                            </a>
                            <div class="text-[11px] text-slate-400 mt-0.5">
                                <i class="fa-solid fa-calendar-day mr-1"></i> Mohon: {{ $item->tarikh_permohonan ? $item->tarikh_permohonan->format('d/m/Y') : '-' }}
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <div class="font-bold text-slate-800 dark:text-white uppercase">{{ $item->pemohon_nama }}</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-2 mt-0.5">
                                <span><i class="fa-solid fa-id-card text-[11px]"></i> {{ $item->pemohon_ic ?: '-' }}</span>
                                @if($item->pemohon_id_premis)
                                <span class="px-1.5 py-0.2 rounded bg-slate-100 dark:bg-slate-700 text-[10px] font-mono font-bold">{{ $item->pemohon_id_premis }}</span>
                                @endif
                            </div>
                            <div class="text-[11px] text-indigo-600 dark:text-indigo-400 font-semibold mt-0.5">
                                <i class="fa-solid fa-location-dot mr-1"></i> {{ $item->jajahan_asal }}
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <div class="font-bold text-slate-800 dark:text-white uppercase">{{ $item->penerima_nama }}</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 truncate max-w-xs" title="{{ $item->penerima_alamat }}">
                                {{ $item->penerima_alamat ?: ($item->penerima_jajahan ?? '-') }}
                            </div>
                            @if($item->no_kenderaan)
                            <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                                <i class="fa-solid fa-truck text-[10px]"></i> {{ $item->no_kenderaan }}
                            </div>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 font-bold text-xs">
                                {{ $item->jenis_ternakan }}
                            </span>
                            <div class="text-xs font-semibold text-slate-600 dark:text-slate-300 mt-1">
                                {{ $item->format_ringkas_jantina }}
                            </div>
                            <div class="text-[11px] text-slate-400">
                                ({{ count($item->senarai_tag ?? []) }} No. Tag)
                            </div>
                        </td>
                        <td class="py-3 px-4 text-xs font-medium text-slate-700 dark:text-slate-300">
                            @if($item->tarikh_jangka_pindah)
                                <div class="font-bold text-slate-900 dark:text-white">{{ $item->tarikh_jangka_pindah->format('d/m/Y') }}</div>
                                <div class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold">{{ $item->tujuan_pemindahan }}</div>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            @if($item->status === 'Diluluskan')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                                    <i class="fa-solid fa-circle-check"></i> Diluluskan
                                </span>
                            @elseif($item->status === 'Ditolak')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300">
                                    <i class="fa-solid fa-circle-xmark"></i> Ditolak
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                    <i class="fa-solid fa-clock"></i> Menunggu
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('eptr.pemindahan.show', $item->id) }}" class="p-2 bg-slate-100 dark:bg-slate-700 hover:bg-indigo-50 dark:hover:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 rounded-xl transition" title="Lihat Butiran">
                                    <i class="fa-solid fa-eye text-sm"></i>
                                </a>
                                @if(Auth::user()->isStaff() && $item->status === 'Diluluskan')
                                <a href="{{ route('eptr.pemindahan.cetak-set-lengkap', $item->id) }}" target="_blank" class="p-2 bg-slate-100 dark:bg-slate-700 hover:bg-emerald-50 dark:hover:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 rounded-xl transition" title="Cetak Set Lengkap (4 Halaman)">
                                    <i class="fa-solid fa-print text-sm"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fa-solid fa-truck-moving text-4xl mb-3 text-slate-300 dark:text-slate-600"></i>
                                <p class="text-base font-bold text-slate-700 dark:text-slate-300">Tiada Rekod Permohonan Pemindahan</p>
                                <p class="text-xs text-slate-400 mt-1">Klik butang "Mohon Pemindahan Baharu" di atas untuk memulakan permohonan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($pemindahanList->hasPages())
        <div class="p-4 border-t border-slate-200 dark:border-slate-700">
            {{ $pemindahanList->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
