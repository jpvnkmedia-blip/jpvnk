@extends('layouts.app')

@section('title', 'Stor Peralatan & Aset Pejabat')
@section('page_title', 'Pengurusan Stor Peralatan & Aset Pejabat')

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-lg bg-indigo-100 text-indigo-900 font-black text-xs uppercase tracking-wider">
                    <i class="fa-solid fa-boxes-stacked mr-1"></i> Stor Pejabat
                </span>
                <span class="text-xs text-slate-500 font-semibold">Khusus Admin Pejabat &amp; Pentadbiran Am</span>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 mt-1">Pengurusan Inventori &amp; Aset Pejabat</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kawalan bekalan alat tulis, kertas, toner pencetak, perabot pejabat dan aset IT jabatan.</p>
        </div>
        @if(Auth::user()->canAccessStorPejabat())
            <a href="{{ route('inventori.pejabat.create') }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-lg shadow-indigo-700/30 transition flex items-center gap-2">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Daftar Barangan Pejabat Baharu</span>
            </a>
        @endif
    </div>

    <!-- KPI Summary Pills -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase">Jumlah Barangan Pejabat</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $totalItems }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Semua item berdaftar</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase">Nilai Keseluruhan Stok</div>
            <div class="text-2xl font-black text-indigo-700 mt-1">RM {{ number_format($totalNilaiStok, 2) }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Anggaran nilai inventori</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase">Amaran Stok Rendah</div>
            <div class="text-2xl font-black text-amber-600 mt-1">{{ $lowStockCount }}</div>
            <div class="text-[10px] text-amber-700 mt-0.5 font-semibold">Perlu pesanan semula</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase">Pinjaman Aset Aktif</div>
            <div class="text-2xl font-black text-indigo-900 mt-1">{{ $totalPinjamanAktif }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Sedang diguna staf</div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-3xl border border-slate-200 p-4 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex flex-wrap gap-2 text-xs">
            <a href="{{ route('inventori.pejabat.index') }}" class="px-3.5 py-1.5 rounded-full font-bold transition {{ !request('kategori') && !request('status') ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua Item
            </a>
            @foreach($kategoriList as $kat)
                <a href="{{ route('inventori.pejabat.index', ['kategori' => $kat]) }}" class="px-3.5 py-1.5 rounded-full font-bold transition {{ request('kategori') === $kat ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    {{ $kat }}
                </a>
            @endforeach
            <a href="{{ route('inventori.pejabat.index', ['status' => 'Stok Rendah']) }}" class="px-3.5 py-1.5 rounded-full font-bold transition {{ request('status') === 'Stok Rendah' ? 'bg-amber-600 text-white shadow-xs' : 'bg-amber-50 text-amber-800 hover:bg-amber-100 border border-amber-200' }}">
                ⚠️ Stok Rendah
            </a>
        </div>

        <form action="{{ route('inventori.pejabat.index') }}" method="GET" class="flex items-center gap-2">
            @if(request('kategori'))
                <input type="hidden" name="kategori" value="{{ request('kategori') }}">
            @endif
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / kod / rak..." class="rounded-xl border border-slate-200 pl-8 pr-3 py-1.5 text-xs bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none w-48 sm:w-64">
                <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-2.5 top-2.5 text-xs"></i>
            </div>
            <button type="submit" class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">
                Cari
            </button>
        </form>
    </div>

    <!-- Inventory Table -->
    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Senarai Stok Barangan &amp; Aset Stor Pejabat</h3>
            <span class="text-xs text-slate-500">Jumlah: <b>{{ $items->total() }}</b> rekod</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-bold text-[11px]">
                        <th class="px-5 py-3.5">Kod Item / SKU</th>
                        <th class="px-4 py-3.5">Nama Barangan Pejabat</th>
                        <th class="px-4 py-3.5">Kategori</th>
                        <th class="px-4 py-3.5">Baki Stok Semasa</th>
                        <th class="px-4 py-3.5">Lokasi Rak / Bilik</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($items as $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 font-mono font-bold text-indigo-900">
                                <a href="{{ route('inventori.show', $item->id) }}" class="hover:underline">
                                    {{ $item->kod_item }}
                                </a>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-slate-900">{{ $item->nama_item }}</div>
                                <div class="text-[11px] text-slate-500">Harga: <b>RM {{ number_format($item->harga_seunit, 2) }}</b> / {{ $item->unit }} &bull; {{ $item->pembekal_utama ?? 'Pembekal Am' }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-800">
                                    {{ $item->kategori }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="text-sm font-black {{ $item->kuantiti_semasa <= $item->kuantiti_minimum ? 'text-amber-600' : 'text-slate-900' }}">
                                    {{ number_format($item->kuantiti_semasa) }} {{ $item->unit }}
                                </div>
                                <div class="text-[10px] text-slate-400">Paras Minima: {{ $item->kuantiti_minimum }} {{ $item->unit }}</div>
                            </td>
                            <td class="px-4 py-3.5 font-medium text-slate-700">
                                <i class="fa-solid fa-map-pin text-slate-400 mr-1"></i>{{ $item->lokasi_rak ?? 'Stor Pejabat' }}
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $item->status === 'Mencukupi' ? 'bg-emerald-100 text-emerald-800' : ($item->status === 'Stok Rendah' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('inventori.show', $item->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-900 font-bold transition">
                                    <i class="fa-solid fa-arrow-right-arrow-left"></i> Stok Masuk / Keluar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-box-open text-3xl mb-2 text-slate-300"></i>
                                <div class="font-bold text-slate-600">Tiada barangan dijumpai dalam Stor Peralatan Pejabat</div>
                                <div class="text-xs text-slate-400 mt-0.5">Sila semak carian anda atau daftarkan barangan baharu.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $items->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
