@extends('layouts.app')

@section('title', 'Borang Permohonan Ubat & Farmasi Klinik Haiwan')
@section('page_title', 'Borang Permohonan Bekalan Ubat & Farmasi')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('klinik.permohonan_ubat.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 bg-white px-3.5 py-2 rounded-xl border border-slate-200 shadow-2xs hover:shadow-xs transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Senarai Permohonan</span>
        </a>
        <span class="text-xs font-bold text-rose-700 bg-rose-50 px-3 py-1.5 rounded-xl border border-rose-200">
            <i class="fa-solid fa-pills mr-1"></i> Stor Ubat &amp; Farmasi Pusat
        </span>
    </div>

    <!-- 1. Borang Permohonan Bekalan Ubat -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs">
        <div class="pb-5 mb-6 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-lg bg-rose-100 text-rose-900 font-black text-xs uppercase tracking-wider">
                    <i class="fa-solid fa-prescription-bottle-medical mr-1"></i> Pesanan Farmasi Veterinar
                </span>
                <span class="text-xs text-slate-400 font-semibold">&bull; {{ $klinikNama }}</span>
            </div>
            <h2 class="text-lg font-bold text-slate-900 mt-2">Borang Pesanan Bekalan Ubat, Vaksin &amp; Klinikal dari Klinik Haiwan</h2>
            <p class="text-xs text-slate-500 mt-0.5">Sila lengkapkan maklumat pesanan ubat-ubatan, cecair suntikan, antibiotik, vaksin atau bahan rawatan pembedahan untuk kegunaan klinik jajahan ini.</p>
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
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">KLINIK / JAJAHAN BERTUGAS</span>
                    <span class="font-bold text-emerald-800 text-xs mt-0.5 block flex items-center gap-1.5">
                        <i class="fa-solid fa-location-dot text-emerald-600"></i>
                        <span>{{ $klinikNama }}</span>
                    </span>
                    <span class="text-[10px] text-slate-500">Jajahan {{ $userJajahan }}</span>
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
                                [{{ $item->kod_item }}] {{ $item->nama_item }} &bull; Baki Stor Pusat: {{ $item->kuantiti_semasa }} {{ $item->unit }} &bull; {{ $item->kategori }} &bull; Luput: {{ $item->tarikh_luput ? $item->tarikh_luput->format('d/m/Y') : '-' }}
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
                        <div class="relative">
                            <input type="text" value="{{ $klinikNama }}" readonly class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 font-bold bg-slate-100/90 text-slate-700 cursor-not-allowed pr-10 focus:outline-none select-none shadow-2xs">
                            <i class="fa-solid fa-lock text-slate-400 absolute right-3.5 top-3 text-xs" title="Ditetapkan mengikut rekod jajahan pemohon (tidak boleh diubah)"></i>
                        </div>
                        <input type="hidden" name="klinik_jajahan" value="{{ $klinikNama }}">
                        <span class="text-[10px] text-slate-400 mt-1 block">Ditetapkan mengikut jajahan pemohon (tidak boleh diubah).</span>
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

    <!-- 2. Senarai Ubat Yang Telah Ada / Diterima di Klinik Haiwan Jajahan Tersebut -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-900 font-black text-xs uppercase tracking-wider">
                        <i class="fa-solid fa-boxes-stacked mr-1"></i> Stok Klinik
                    </span>
                    <span class="text-xs font-bold text-slate-700">{{ $klinikNama }}</span>
                </div>
                <h3 class="text-base font-bold text-slate-900 mt-1">Senarai Bekalan Ubat &amp; Vaksin di Klinik Haiwan Jajahan Ini</h3>
                <p class="text-xs text-slate-500">Rekod semua bekalan farmaseutikal haiwan yang telah diluluskan dan diserahkan bagi operasi rawatan di {{ $klinikNama }}.</p>
            </div>
            <div class="text-xs font-bold text-slate-600 bg-slate-50 px-3.5 py-2 rounded-xl border border-slate-200 whitespace-nowrap">
                Jumlah Sedia Ada: <span class="text-emerald-700 font-extrabold text-sm">{{ count($ringkasanUbatKlinik) }}</span> Jenis
            </div>
        </div>

        @if(count($ringkasanUbatKlinik) > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-bold text-[11px]">
                        <th class="px-4 py-3">Kod &amp; Nama Ubat / Vaksin</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3 text-center">Jumlah Diterima</th>
                        <th class="px-4 py-3">Tarikh Penerimaan Terkini</th>
                        <th class="px-4 py-3">Status Terkini</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($ringkasanUbatKlinik as $itemId => $data)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="px-4 py-3.5">
                            <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                <i class="fa-solid fa-pills text-rose-500"></i>
                                <span>{{ $data['item']->nama_item }}</span>
                            </div>
                            <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                [{{ $data['item']->kod_item }}] &bull; Luput: {{ $data['item']->tarikh_luput ? $data['item']->tarikh_luput->format('d/m/Y') : '-' }}
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-slate-600">
                            <span class="px-2.5 py-1 rounded-lg bg-slate-100 font-semibold text-[10px] text-slate-700">
                                {{ $data['item']->kategori }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="font-black text-slate-900 text-sm">
                                {{ $data['jumlah_diterima'] }}
                            </span>
                            <span class="text-[10px] text-slate-500 font-medium block">
                                {{ $data['item']->unit }} ({{ $data['bilangan_pesanan'] }} kali agihan)
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-slate-600 whitespace-nowrap">
                            <div class="font-bold text-[11px] text-slate-800">
                                {{ $data['tarikh_terakhir']->format('d/m/Y') }}
                            </div>
                            <div class="text-[10px] text-slate-400">
                                {{ $data['tarikh_terakhir']->diffForHumans() }}
                            </div>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold {{ $data['status_terkini'] === 'Telah Diambil / Diserahkan' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                                <i class="fa-solid fa-circle-check mr-1 text-[9px]"></i> {{ $data['status_terkini'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-8 text-center bg-slate-50 rounded-2xl border border-slate-100">
            <div class="w-12 h-12 rounded-full bg-slate-200/70 text-slate-400 flex items-center justify-center mx-auto mb-2">
                <i class="fa-solid fa-box-open text-base"></i>
            </div>
            <div class="font-bold text-slate-700 text-xs">Belum Ada Rekod Penerimaan Bekalan Ubat di {{ $klinikNama }}</div>
            <p class="text-[11px] text-slate-400 mt-1 max-w-md mx-auto">Klinik haiwan jajahan ini belum menerima serahan bekalan ubat dari Stor Farmasi Pusat. Sila buat pesanan melalui borang di atas.</p>
        </div>
        @endif
    </div>

</div>
@endsection