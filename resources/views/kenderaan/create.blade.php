@extends('layouts.app')

@section('title', 'Borang Permohonan Tempahan Kenderaan')
@section('page_title', 'Kenderaan: Permohonan Tempahan Kenderaan Rasmi')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs">
    
    <div class="border-b border-slate-100 pb-4 mb-6">
        <h3 class="text-base font-bold text-slate-900">Borang Permohonan Penggunaan Kenderaan Jabatan</h3>
        <p class="text-xs text-slate-500 mt-0.5">Sila lengkapkan maklumat destinasi, tarikh perjalanan dan tujuan rasmi.</p>
    </div>

    <form action="{{ route('kenderaan.store') }}" method="POST" class="space-y-4 text-xs">
        @csrf

        <div>
            <label class="block font-bold text-slate-700 uppercase mb-1">Pilih Kenderaan (Pilihan)</label>
            <select name="kenderaan_id" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none font-medium">
                <option value="">-- Pihak Pengurusan Tentukan Kenderaan Sesuai --</option>
                @foreach(($fleet ?? $availableKenderaan ?? []) as $k)
                    <option value="{{ $k->id }}">{{ $k->no_pendaftaran }} - {{ $k->model }} ({{ $k->jenis_kenderaan ?? $k->jenis ?? 'Kenderaan' }}) &bull; {{ $k->status }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block font-bold text-slate-700 uppercase mb-1">Destinasi Perjalanan</label>
            <input type="text" name="destinasi" value="{{ old('destinasi') }}" required placeholder="Contoh: Ladang Ternakan Pasir Mas & Pejabat Veterinar Jajahan Pasir Mas" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none">
        </div>

        <div>
            <label class="block font-bold text-slate-700 uppercase mb-1">Tujuan Rasmi Penggunaan Kenderaan</label>
            <textarea name="tujuan" rows="3" required placeholder="Contoh: Operasi pemasangan tag telinga EPTR, pemeriksaan tapak ladang unggas EPU, atau pemantauan program pawah" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none">{{ old('tujuan') }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh & Masa Bertolak</label>
                <input type="datetime-local" name="tarikh_keluar" value="{{ date('Y-m-d\TH:i') }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none">
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh & Masa Dijangka Pulang</label>
                <input type="datetime-local" name="tarikh_kembali" value="{{ date('Y-m-d\TH:i', strtotime('+8 hours')) }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Bilangan Penumpang (Orang)</label>
                <input type="number" name="bilangan_penumpang" value="{{ old('bilangan_penumpang', 2) }}" min="1" max="20" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none font-bold">
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Pemandu Dicadangkan (Pilihan)</label>
                <select name="nama_pemandu" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none font-medium">
                    <option value="{{ Auth::user()->name }} (Pemohon / Staf Sendiri)">Saya Sendiri ({{ Auth::user()->name }})</option>
                    <option value="Pemandu Khas Jabatan">Pihak Pengurusan Tentukan Pemandu Rasmi</option>
                    @foreach(($pemanduList ?? []) as $d)
                        <option value="{{ $d->nama }} ({{ $d->no_telefon }})">{{ $d->nama }} &bull; {{ $d->no_telefon }} (Lesen {{ $d->kelas_lesen }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="pt-4 flex items-center justify-end gap-3">
            <a href="{{ route('kenderaan.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 font-bold text-slate-700 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold shadow-lg shadow-teal-700/30 transition flex items-center gap-2">
                <i class="fa-solid fa-paper-plane"></i>
                <span>Hantar Permohonan Tempahan</span>
            </button>
        </div>
    </form>
</div>
@endsection
