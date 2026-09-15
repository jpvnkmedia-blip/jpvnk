@extends('layouts.app')

@section('title', 'Inventori Pejabat & Bekalan Veterinar')
@section('page_title', 'Pengurusan Inventori Pejabat & Bekalan Veterinar')

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">Pengurusan Stok, Tag EPTR & Aset Pejabat</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kawalan bekalan ubat-ubatan, vaksin ternakan, tag telinga EPTR bersiri, aplikator dan perkakasan pejabat</p>
        </div>
        @if(Auth::user()->isStaff())
            <a href="{{ route('inventori.create') }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-lg shadow-indigo-700/30 transition flex items-center gap-2">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Daftar Item / Bekalan Baharu</span>
            </a>
        @endif
    </div>

    <!-- KPI Summary Pills -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase">Jumlah Item / Kod</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $totalItems }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Semua kategori bekalan</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase">Amaran Stok Rendah</div>
            <div class="text-2xl font-black text-amber-600 mt-1">{{ $lowStockCount }}</div>
            <div class="text-[10px] text-amber-700 mt-0.5 font-semibold">Perlu pesanan semula</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase">Habis Stok</div>
            <div class="text-2xl font-black text-rose-600 mt-1">{{ $outOfStockCount }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Kuantiti sifar</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase">Pinjaman Peralatan Aktif</div>
            <div class="text-2xl font-black text-indigo-700 mt-1">{{ $totalPinjamanAktif }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Sedang dipinjam staf</div>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-bold text-[11px]">
                        <th class="px-5 py-3.5">Kod Item</th>
                        <th class="px-4 py-3.5">Nama Item / Bekalan</th>
                        <th class="px-4 py-3.5">Kategori</th>
                        <th class="px-4 py-3.5">Baki Stok Semasa</th>
                        <th class="px-4 py-3.5">Lokasi Simpanan</th>
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
                                <div class="text-[11px] text-slate-500">Harga: RM {{ number_format($item->harga_seunit, 2) }} / {{ $item->unit }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-800">
                                    {{ $item->kategori }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="text-sm font-black {{ $item->kuantiti_semasa <= $item->kuantiti_minimum ? 'text-amber-600 font-black' : 'text-slate-900' }}">
                                    {{ number_format($item->kuantiti_semasa) }} {{ $item->unit }}
                                </div>
                                <div class="text-[10px] text-slate-400">Minima: {{ $item->kuantiti_minimum }} {{ $item->unit }}</div>
                            </td>
                            <td class="px-4 py-3.5 font-medium text-slate-700">
                                {{ $item->lokasi_rak ?? '-' }}
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $item->status === 'Mencukupi' ? 'bg-emerald-100 text-emerald-800' : ($item->status === 'Stok Rendah' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1">
                                <a href="{{ route('inventori.show', $item->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-indigo-100 text-slate-700 hover:text-indigo-800 font-bold transition">
                                    <i class="fa-solid fa-eye"></i> Perincian
                                </a>
                                @if(Auth::user()->isStaff())
                                    <a href="{{ route('inventori.transaksi.create', $item->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-indigo-100 hover:bg-indigo-200 text-indigo-900 font-bold transition">
                                        <i class="fa-solid fa-arrow-right-arrow-left"></i> Transaksi
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                Tiada item inventori dijumpai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $items->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
