@extends('layouts.app')

@section('title', 'Borang Permohonan Alatan Tulis & Stor Pejabat')
@section('page_title', 'Permohonan Alatan Tulis & Peralatan Pejabat')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('inventori.permohonan.saya') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 bg-white px-3.5 py-2 rounded-xl border border-slate-200 transition">
            &larr; Kembali ke Permohonan Saya
        </a>
        <span class="text-xs font-bold text-indigo-700 bg-indigo-50 px-3 py-1.5 rounded-xl border border-indigo-200">
            <i class="fa-solid fa-boxes-stacked mr-1"></i> Borang Permohonan Stor Pejabat
        </span>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs">
        <div class="pb-5 mb-6 border-b border-slate-100">
            <h2 class="text-lg font-bold text-slate-900">Borang Pesanan / Permohonan Barangan Stor Pejabat</h2>
            <p class="text-xs text-slate-500 mt-0.5">Sila isi butiran barangan alat tulis, kertas, toner atau peralatan pejabat yang diperlukan untuk kegunaan bahagian / unit anda.</p>
        </div>

        <form action="{{ route('inventori.permohonan.pejabat.store') }}" method="POST" class="space-y-6 text-xs">
            @csrf

            <!-- Maklumat Pemohon -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">NAMA PEGAWAI / PEMOHON</span>
                    <span class="font-bold text-slate-800 text-xs mt-0.5 block">{{ Auth::user()->name }}</span>
                    <span class="text-[10px] text-slate-500">{{ Auth::user()->role_label }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">NO. TELEFON / EMEL</span>
                    <span class="font-bold text-slate-800 text-xs mt-0.5 block">{{ Auth::user()->phone ?? '-' }}</span>
                    <span class="text-[10px] text-slate-500">{{ Auth::user()->email }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">JAJAHAN / PENEMPATAN</span>
                    <span class="font-bold text-slate-800 text-xs mt-0.5 block">{{ Auth::user()->jajahan ?? 'Ibu Pejabat Kota Bharu' }}</span>
                </div>
            </div>

            <!-- Pilihan Barangan & Kuantiti -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-slate-900 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-indigo-600"></i>
                    <span>Butiran Barangan &amp; Keperluan</span>
                </h3>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Pilih Barangan Pejabat Yang Dimohon <span class="text-rose-500">*</span></label>
                    <select name="inventori_item_id" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none font-semibold">
                        <option value="">-- Sila Pilih Barangan Daripada Stor Pejabat --</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" {{ old('inventori_item_id') == $item->id ? 'selected' : '' }}>
                                [{{ $item->kod_item }}] {{ $item->nama_item }} &bull; (Baki Stok Semasa: {{ $item->kuantiti_semasa }} {{ $item->unit }}) &bull; {{ $item->kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Kuantiti Dimohon <span class="text-rose-500">*</span></label>
                        <input type="number" name="kuantiti_dimohon" min="1" value="{{ old('kuantiti_dimohon', 1) }}" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 font-bold focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Bahagian / Unit Staf <span class="text-rose-500">*</span></label>
                        <input type="text" name="unit_bahagian" value="{{ old('unit_bahagian', Auth::user()->role_label) }}" required placeholder="cth: Unit Pentadbiran / Bahagian EPTR / Klinik Haiwan" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 font-medium focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Tarikh Diperlukan</label>
                        <input type="date" name="tarikh_diperlukan" value="{{ old('tarikh_diperlukan', date('Y-m-d')) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:outline-none font-semibold">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Tujuan Permohonan &amp; Penggunaan <span class="text-rose-500">*</span></label>
                    <textarea name="tujuan_permohonan" rows="3" required placeholder="Nyatakan tujuan penggunaan (cth: Cetakan borang bancian penternak ruminan / Kegunaan harian pejabat jajahan / Kursus jabatan)..." class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:outline-none">{{ old('tujuan_permohonan') }}</textarea>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Catatan Tambahan (Pilihan)</label>
                    <input type="text" name="catatan_pemohon" value="{{ old('catatan_pemohon') }}" placeholder="Sebarang maklumat tambahan untuk perhatian Admin Pejabat..." class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('inventori.permohonan.saya') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                    Batal
                </a>

                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-lg shadow-indigo-700/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Hantar Permohonan Alatan Pejabat</span>
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
