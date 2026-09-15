@extends('layouts.app')

@section('title', 'Rekod Transaksi Stok - ' . $item->nama_item)
@section('page_title', 'Inventori: Rekod Stok Masuk / Stok Keluar')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs">
    
    <div class="border-b border-slate-100 pb-4 mb-6">
        <h3 class="text-base font-bold text-slate-900">Borang Pergerakan Stok Inventori</h3>
        <p class="text-xs text-slate-500 mt-0.5">Item: <b>{{ $item->nama_item }} ({{ $item->kod_item }})</b> &bull; Baki Semasa: <span class="font-bold text-indigo-900">{{ $item->kuantiti_semasa }} {{ $item->unit }}</span></p>
    </div>

    <form action="{{ route('inventori.transaksi.store', $item->id) }}" method="POST" class="space-y-4 text-xs">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Jenis Pergerakan Stok</label>
                <select name="jenis_transaksi" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none font-bold">
                    <option value="Stok Keluar">Stok Keluar (Pengeluaran / Agihan)</option>
                    <option value="Stok Masuk">Stok Masuk (Penerimaan / Pembelian Baharu)</option>
                    <option value="Pelupusan">Pelupusan (Rosak / Luput)</option>
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Kuantiti ({{ $item->unit }})</label>
                <input type="number" name="kuantiti" value="10" min="1" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none font-bold">
            </div>
        </div>

        <div>
            <label class="block font-bold text-slate-700 uppercase mb-1">Nama Penerima / Pembekal</label>
            <input type="text" name="penerima_atau_pembekal" value="{{ old('penerima_atau_pembekal', 'Klinik Haiwan Kota Bharu') }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">No. Rujukan Dokumen / Pesanan</label>
                <input type="text" name="rujukan_dokumen" placeholder="Contoh: PO-2026-081 / NOTA-KLR-02" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Catatan Tambahan</label>
                <input type="text" name="catatan" placeholder="Catatan transaksi" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>
        </div>

        <div class="pt-4 flex items-center justify-end gap-3">
            <a href="{{ route('inventori.show', $item->id) }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 font-bold text-slate-700 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-lg shadow-indigo-700/30 transition flex items-center gap-2">
                <i class="fa-solid fa-check"></i>
                <span>Simpan Transaksi Stok</span>
            </button>
        </div>
    </form>
</div>
@endsection
