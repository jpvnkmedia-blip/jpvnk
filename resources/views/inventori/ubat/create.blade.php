@extends('layouts.app')

@section('title', 'Daftar Ubat / Vaksin Veterinar Baharu')
@section('page_title', 'Daftar Ubat, Vaksin & Farmaseutikal Veterinar')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('inventori.ubat.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 bg-white px-3.5 py-2 rounded-xl border border-slate-200 transition">
            &larr; Kembali ke Stor Ubat &amp; Farmasi
        </a>
        <span class="text-xs font-bold text-rose-700 bg-rose-50 px-3 py-1.5 rounded-xl border border-rose-200">
            <i class="fa-solid fa-pills mr-1"></i> Pendaftaran Stor Ubat Veterinar
        </span>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs">
        <form action="{{ route('inventori.ubat.store') }}" method="POST" class="space-y-6 text-xs">
            @csrf

            <!-- Section 1: Maklumat Asas Ubat / Vaksin -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-slate-900 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-prescription-bottle-medical text-rose-600"></i>
                    <span>1. Maklumat Formula &amp; Kategori Ubat / Vaksin</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Kod Item / SKU Farmasi <span class="text-rose-500">*</span></label>
                        <input type="text" name="kod_item" value="{{ old('kod_item', 'UBT-'.strtoupper(substr(uniqid(), -6))) }}" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 font-mono font-bold focus:ring-2 focus:ring-rose-500 focus:outline-none uppercase">
                        <span class="text-[10px] text-slate-400 mt-0.5 block">Kod unik inventori stor ubat &amp; vaksin</span>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Nama Penuh Ubat / Vaksin / Reagen <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_item" value="{{ old('nama_item') }}" required placeholder="cth: Ivomec Super 500ml / Vaksin FMD O/A/Asia1" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-rose-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Kategori Farmaseutikal <span class="text-rose-500">*</span></label>
                        <select name="kategori" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none font-semibold">
                            @foreach($kategoriList as $kat)
                                <option value="{{ $kat }}" {{ old('kategori') === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Unit Bungkusan / Dos <span class="text-rose-500">*</span></label>
                        <input type="text" name="unit" value="{{ old('unit', 'Botol') }}" required placeholder="cth: Botol, Vial, Kotak, Tiub, Keping, Pek, Set" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-rose-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Section 2: Kawalan Batch, Tarikh Luput & Suhu Simpanan -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <h3 class="text-sm font-bold text-slate-900 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-temperature-half text-rose-600"></i>
                    <span>2. No. Kelompok (Batch), Tarikh Luput &amp; Kawalan Suhu</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">No. Batch / Kelompok Pengeluar</label>
                        <input type="text" name="no_batch" value="{{ old('no_batch') }}" placeholder="cth: IVM-2026-089 / FMD-MY-26" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 font-mono font-bold focus:ring-2 focus:ring-rose-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Tarikh Luput Ubat (Expiry Date)</label>
                        <input type="date" name="tarikh_luput" value="{{ old('tarikh_luput') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-rose-500 focus:outline-none font-semibold">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Suhu Simpanan Yang Disyorkan <span class="text-rose-500">*</span></label>
                        <select name="suhu_simpanan" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none font-semibold">
                            <option value="Suhu Bilik Kering (< 25°C)" {{ old('suhu_simpanan') === 'Suhu Bilik Kering (< 25°C)' ? 'selected' : '' }}>🌡️ Suhu Bilik Kering (&lt; 25°C)</option>
                            <option value="Rangkaian Sejuk / Chiller (2°C - 8°C)" {{ old('suhu_simpanan') === 'Rangkaian Sejuk / Chiller (2°C - 8°C)' ? 'selected' : '' }}>❄️ Rangkaian Sejuk / Chiller (2°C - 8°C)</option>
                            <option value="Peti Beku / Freezer (-20°C)" {{ old('suhu_simpanan') === 'Peti Beku / Freezer (-20°C)' ? 'selected' : '' }}>🧊 Peti Beku / Freezer (-20°C)</option>
                            <option value="Tempat Gelap & Terlindung Cahaya" {{ old('suhu_simpanan') === 'Tempat Gelap & Terlindung Cahaya' ? 'selected' : '' }}>🌑 Terlindung Cahaya Matahari</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 3: Kuantiti & Harga -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <h3 class="text-sm font-bold text-slate-900 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-calculator text-rose-600"></i>
                    <span>3. Kuantiti Permulaan, Paras Minima &amp; Harga</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Kuantiti Permulaan Masuk <span class="text-rose-500">*</span></label>
                        <input type="number" name="kuantiti_semasa" min="0" value="{{ old('kuantiti_semasa', 10) }}" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 font-bold focus:ring-2 focus:ring-rose-500 focus:outline-none">
                        <span class="text-[10px] text-slate-400 mt-0.5 block">Direkodkan sebagai Stok Masuk awal</span>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Paras Minimum (Amaran Pesanan) <span class="text-rose-500">*</span></label>
                        <input type="number" name="kuantiti_minimum" min="1" value="{{ old('kuantiti_minimum', 5) }}" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 font-bold text-amber-600 focus:ring-2 focus:ring-rose-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Harga Kos Seunit (RM)</label>
                        <input type="number" step="0.01" min="0" name="harga_seunit" value="{{ old('harga_seunit', '0.00') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 font-bold focus:ring-2 focus:ring-rose-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Section 4: Pembekal & Lokasi -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <h3 class="text-sm font-bold text-slate-900 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-truck-medical text-rose-600"></i>
                    <span>4. Pembekal Farmaseutikal &amp; Lokasi Simpanan</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Pengeluar / Pembekal Farmasi</label>
                        <input type="text" name="pembekal_utama" value="{{ old('pembekal_utama') }}" placeholder="cth: Zoetis Malaysia / Boehringer Ingelheim / Pharmaniaga" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-rose-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Lokasi Rak / Peti Sejuk</label>
                        <input type="text" name="lokasi_rak" value="{{ old('lokasi_rak', 'Bilik Farmasi - Rak A2') }}" placeholder="cth: Peti Sejuk Vaksin No. 1 / Rak Ubat A2" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-rose-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Indikasi Rawatan / Catatan Penggunaan</label>
                    <textarea name="deskripsi" rows="3" placeholder="Indikasi rawatan, spesies sasaran (lembu/kambing/ayam), dos lazim atau syarat penggunaan..." class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-rose-500 focus:outline-none">{{ old('deskripsi') }}</textarea>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('inventori.ubat.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                    Batal
                </a>

                <button type="submit" class="px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold shadow-lg shadow-rose-700/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-save"></i>
                    <span>Simpan &amp; Daftar Ubat / Vaksin</span>
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
