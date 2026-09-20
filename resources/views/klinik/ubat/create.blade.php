@extends('layouts.app')

@section('title', 'Borang Permohonan Ubat & Farmasi Klinik Haiwan')
@section('page_title', 'Borang Permohonan Bekalan Ubat & Farmasi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('klinik.permohonan_ubat.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 bg-white px-3.5 py-2 rounded-xl border border-slate-200 transition">
            &larr; Kembali ke Senarai Permohonan Klinik
        </a>
        <span class="text-xs font-bold text-rose-700 bg-rose-50 px-3 py-1.5 rounded-xl border border-rose-200">
            <i class="fa-solid fa-pills mr-1"></i> Permohonan Bekalan Stor Farmasi
        </span>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs">
        <div class="pb-5 mb-6 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-lg bg-rose-100 text-rose-900 font-black text-xs uppercase tracking-wider">
                    <i class="fa-solid fa-prescription-bottle-medical mr-1"></i> Pesanan Farmasi Veterinar
                </span>
            </div>
            <h2 class="text-lg font-bold text-slate-900 mt-2">Borang Pesanan Bekalan Ubat, Vaksin &amp; Klinikal dari Klinik Haiwan</h2>
            <p class="text-xs text-slate-500 mt-0.5">Sila lengkapkan maklumat pesanan ubat-ubatan, cecair suntikan, antibiotik, vaksin atau bahan rawatan pembedahan untuk kegunaan klinik.</p>
        </div>

        <form action="{{ route('klinik.permohonan_ubat.store') }}" method="POST" class="space-y-6 text-xs">
            @csrf

            <!-- Maklumat Pemohon / Klinik -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">PEGAWAI KLINIK / PEMOHON</span>
                    <span class="font-bold text-slate-800 text-xs mt-0.5 block">{{ Auth::user()->name }}</span>
                    <span class="text-[10px] text-slate-500">{{ Auth::user()->role_label }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">NO. TELEFON / EMEL</span>
                    <span class="font-bold text-slate-800 text-xs mt-0.5 block">{{ Auth::user()->phone ?? '-' }}</span>
                    <span class="text-[10px] text-slate-500">{{ Auth::user()->email }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">LOKASI / JAJAHAN BERTUGAS</span>
                    <span class="font-bold text-slate-800 text-xs mt-0.5 block">{{ Auth::user()->jajahan ?? 'Ibu Pejabat Kota Bharu' }}</span>
                </div>
            </div>

            <!-- Butiran Ubat & Kuantiti -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-slate-900 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-capsules text-rose-600"></i>
                    <span>Maklumat Ubat / Vaksin Yang Diperlukan</span>
                </h3>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Pilih Ubat / Vaksin / Bekalan Rawatan Daripada Katalog Stor Farmasi <span class="text-rose-500">*</span></label>
                    <select name="inventori_item_id" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none font-semibold">
                        <option value="">-- Sila Pilih Ubat / Vaksin Daripada Stor Farmasi Pusat --</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" {{ old('inventori_item_id') == $item->id ? 'selected' : '' }}>
                                [{{ $item->kod_item }}] {{ $item->nama_item }} &bull; Baki Stok: {{ $item->kuantiti_semasa }} {{ $item->unit }} &bull; {{ $item->kategori }} &bull; Luput: {{ $item->tarikh_luput ? $item->tarikh_luput->format('d/m/Y') : '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Kuantiti Dimohon <span class="text-rose-500">*</span></label>
                        <input type="number" name="kuantiti_dimohon" min="1" value="{{ old('kuantiti_dimohon', 1) }}" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 font-bold focus:ring-2 focus:ring-rose-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Klinik Haiwan / Jajahan Pemohon <span class="text-rose-500">*</span></label>
                        <select name="klinik_jajahan" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 font-medium focus:ring-2 focus:ring-rose-500 focus:outline-none bg-slate-50 focus:bg-white">
                            @foreach($klinikList as $klinik)
                                <option value="{{ $klinik }}" {{ old('klinik_jajahan', (Auth::user()->jajahan ? 'Pusat Veterinar Jajahan ' . Auth::user()->jajahan : 'Klinik Haiwan Ibu Pejabat JPVNK Kota Bharu')) == $klinik ? 'selected' : '' }}>
                                    {{ $klinik }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Tarikh Diperlukan</label>
                        <input type="date" name="tarikh_diperlukan" value="{{ old('tarikh_diperlukan', date('Y-m-d')) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-rose-500 focus:outline-none font-semibold">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Tujuan Permohonan &amp; Keperluan Rawatan Klinikal <span class="text-rose-500">*</span></label>
                    <textarea name="tujuan_permohonan" rows="3" required placeholder="Nyatakan tujuan permohonan bekalan (cth: Keperluan rawatan harian pesakit kucing/anjing di klinik / Stok suntikan bius pembedahan kembiri / Vaksinasi berjadual penyakit ternakan di jajahan)..." class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-rose-500 focus:outline-none">{{ old('tujuan_permohonan') }}</textarea>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Catatan Tambahan (Pilihan)</label>
                    <input type="text" name="catatan_pemohon" value="{{ old('catatan_pemohon') }}" placeholder="cth: Diperlukan pembungkusan beg sejuk / dos rawatan kecemasan..." class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-rose-500 focus:outline-none">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('klinik.permohonan_ubat.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                    Batal
                </a>

                <button type="submit" class="px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold shadow-lg shadow-rose-700/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Hantar Permohonan ke Stor Farmasi</span>
                </button>
            </div>

        </form>
    </div>

</div>
@endsection