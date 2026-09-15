@extends('layouts.app')

@section('title', 'Daftar Barangan Stor Pejabat Baharu')
@section('page_title', 'Daftar Barangan Stor Peralatan Pejabat')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('inventori.pejabat.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 bg-white px-3.5 py-2 rounded-xl border border-slate-200 transition">
            &larr; Kembali ke Stor Pejabat
        </a>
        <span class="text-xs font-bold text-indigo-700 bg-indigo-50 px-3 py-1.5 rounded-xl border border-indigo-200">
            <i class="fa-solid fa-boxes-stacked mr-1"></i> Pendaftaran Stor Peralatan Pejabat
        </span>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs">
        <form action="{{ route('inventori.pejabat.store') }}" method="POST" class="space-y-6 text-xs">
            @csrf

            <!-- Section 1: Maklumat Asas Barangan -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-slate-900 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-tag text-indigo-600"></i>
                    <span>1. Maklumat Asas Barangan Pejabat</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Kod Item / SKU Barangan <span class="text-rose-500">*</span></label>
                        <input type="text" name="kod_item" value="{{ old('kod_item', 'PJB-'.strtoupper(substr(uniqid(), -6))) }}" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 font-mono font-bold focus:ring-2 focus:ring-indigo-500 focus:outline-none uppercase">
                        <span class="text-[10px] text-slate-400 mt-0.5 block">Kod unik pengenalan item stor pejabat</span>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Nama Penuh Barangan <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_item" value="{{ old('nama_item') }}" required placeholder="cth: Kertas A4 IK Yellow 70gsm / Toner HP 76A" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Kategori Barangan Pejabat <span class="text-rose-500">*</span></label>
                        <select name="kategori" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none font-semibold">
                            @foreach($kategoriList as $kat)
                                <option value="{{ $kat }}" {{ old('kategori') === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Unit Ukuran <span class="text-rose-500">*</span></label>
                        <input type="text" name="unit" value="{{ old('unit', 'Kotak') }}" required placeholder="cth: Kotak, Rim, Unit, Set, Keping, Pek" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Section 2: Kuantiti & Harga -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <h3 class="text-sm font-bold text-slate-900 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-calculator text-indigo-600"></i>
                    <span>2. Kuantiti Permulaan, Paras Minima &amp; Harga</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Kuantiti Permulaan Masuk <span class="text-rose-500">*</span></label>
                        <input type="number" name="kuantiti_semasa" min="0" value="{{ old('kuantiti_semasa', 10) }}" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 font-bold focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <span class="text-[10px] text-slate-400 mt-0.5 block">Akan direkodkan sebagai Stok Masuk awal</span>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Paras Stok Minimum (Amaran) <span class="text-rose-500">*</span></label>
                        <input type="number" name="kuantiti_minimum" min="1" value="{{ old('kuantiti_minimum', 5) }}" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 font-bold text-amber-600 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <span class="text-[10px] text-slate-400 mt-0.5 block">Sistem beri amaran bila stok di bawah ini</span>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Harga Anggaran Seunit (RM)</label>
                        <input type="number" step="0.01" min="0" name="harga_seunit" value="{{ old('harga_seunit', '0.00') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 font-bold focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Section 3: Pembekal & Lokasi Rak -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <h3 class="text-sm font-bold text-slate-900 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-truck-ramp-box text-indigo-600"></i>
                    <span>3. Pembekal &amp; Lokasi Penyimpanan</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Syarikat Pembekal Utama</label>
                        <input type="text" name="pembekal_utama" value="{{ old('pembekal_utama') }}" placeholder="cth: Percetakan & Alat Tulis Kelantan Sdn Bhd" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Lokasi Rak / Bilik Simpanan</label>
                        <input type="text" name="lokasi_rak" value="{{ old('lokasi_rak', 'Stor Pejabat Tingkat 1 - Rak A1') }}" placeholder="cth: Stor Pejabat - Rak A1" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Catatan / Deskripsi Barangan</label>
                    <textarea name="deskripsi" rows="3" placeholder="Spesifikasi barangan, peruntukan pembekalan pejabat atau catatan penggunaan..." class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:outline-none">{{ old('deskripsi') }}</textarea>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('inventori.pejabat.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                    Batal
                </a>

                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-lg shadow-indigo-700/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-save"></i>
                    <span>Simpan &amp; Daftar Barangan Pejabat</span>
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
