@extends('layouts.app')

@section('title', 'Daftar Item Bekalan Inventori')
@section('page_title', 'Inventori: Daftar Item / Bekalan Baharu')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs">
    
    <div class="border-b border-slate-100 pb-4 mb-6">
        <h3 class="text-base font-bold text-slate-900">Borang Pendaftaran Item & Stok Bekalan</h3>
        <p class="text-xs text-slate-500 mt-0.5">Sila lengkapkan maklumat item, kategori, unit ukuran dan paras stok minimum.</p>
    </div>

    <form action="{{ route('inventori.store') }}" method="POST" class="space-y-4 text-xs">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Kod Item Unik</label>
                <input type="text" name="kod_item" value="{{ old('kod_item', 'UBT-VET-' . rand(100, 999)) }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none font-mono font-bold">
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Kategori Bekalan</label>
                <select name="kategori" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none font-bold">
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat }}">{{ $kat }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block font-bold text-slate-700 uppercase mb-1">Nama Item / Bekalan</label>
            <input type="text" name="nama_item" value="{{ old('nama_item') }}" required placeholder="Contoh: Albendazole Oral Drench 1L / Tag Telinga EPTR" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none font-medium">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Unit Ukuran</label>
                <select name="unit" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="Botol">Botol</option>
                    <option value="Kotak">Kotak</option>
                    <option value="Keping">Keping</option>
                    <option value="Unit">Unit</option>
                    <option value="Pek">Pek</option>
                    <option value="Set">Set</option>
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Kuantiti Permulaan</label>
                <input type="number" name="kuantiti_semasa" value="{{ old('kuantiti_semasa', 50) }}" min="0" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none font-bold">
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Had Stok Minimum</label>
                <input type="number" name="kuantiti_minimum" value="{{ old('kuantiti_minimum', 10) }}" min="1" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none font-bold">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Harga Anggaran Seunit (RM)</label>
                <input type="number" step="0.01" name="harga_seunit" value="{{ old('harga_seunit', 25.00) }}" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Lokasi Rak / Stor Simpanan</label>
                <input type="text" name="lokasi_rak" value="{{ old('lokasi_rak', 'Stor Utama - Rak A1') }}" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>
        </div>

        <div>
            <label class="block font-bold text-slate-700 uppercase mb-1">Penerangan / Spesifikasi</label>
            <textarea name="deskripsi" rows="2" placeholder="Catatan kegunaan atau pembekal" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">{{ old('deskripsi') }}</textarea>
        </div>

        <div class="pt-4 flex items-center justify-end gap-3">
            <a href="{{ route('inventori.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 font-bold text-slate-700 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-lg shadow-indigo-700/30 transition flex items-center gap-2">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Daftar Item Inventori</span>
            </button>
        </div>
    </form>
</div>
@endsection
