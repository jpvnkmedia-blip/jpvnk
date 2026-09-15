@extends('layouts.app')

@section('title', 'EPU Borang C - Pembaharuan Lesen Unggas')
@section('page_title', 'EPU Borang C: Permohonan Pembaharuan Lesen Ladang Unggas')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs">
    
    <div class="border-b border-slate-100 pb-4 mb-6">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100 text-amber-900 text-xs font-bold uppercase mb-2">
            <i class="fa-solid fa-arrows-rotate"></i> EPU Borang C
        </div>
        <h3 class="text-base font-bold text-slate-900">Permohonan Pembaharuan Lesen Ladang Unggas</h3>
        <p class="text-xs text-slate-500 mt-0.5">Ladang: <b>{{ $ladang->nama_ladang }}</b> (Jajahan {{ $ladang->jajahan }})</p>
    </div>

    <form action="{{ route('epu.borang-c.store', $ladang->id) }}" method="POST" class="space-y-4 text-xs">
        @csrf

        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
            <div class="text-[11px] text-slate-500 font-bold uppercase">Lesen Sedia Ada:</div>
            <div>No. Lesen: <span class="font-mono font-bold text-slate-800">{{ $ladang->permohonanTerkini->no_lesen_epu ?? 'Lesen Pertama' }}</span></div>
            <div>Sistem Reban: <b>{{ $ladang->sistem_reban }}</b> &bull; Kapasiti Maksimum: <b>{{ number_format($ladang->kapasiti_maksimum_unggas) }} Ekor</b></div>
        </div>

        <div>
            <label class="block font-bold text-slate-700 uppercase mb-1">Bilangan Semasa Unggas (Ekor)</label>
            <input type="number" name="bilangan_semasa_unggas" value="{{ old('bilangan_semasa_unggas', $ladang->permohonanTerkini->bilangan_semasa_unggas ?? 5000) }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none font-bold">
        </div>

        <div>
            <label class="block font-bold text-slate-700 uppercase mb-1">Kadar Yuran Pembaharuan Lesen (Tahunan)</label>
            <input type="text" readonly value="RM 200.00" class="w-full px-3.5 py-2.5 text-sm bg-slate-100 border border-slate-200 rounded-xl font-bold text-slate-700">
        </div>

        <div>
            <label class="block font-bold text-slate-700 uppercase mb-1">Sebarang Perubahan Struktur / Kapasiti (Jika Ada)</label>
            <textarea name="perubahan_maklumat" rows="3" placeholder="Nyatakan jika terdapat penambahan reban, kipas pengudaraan atau perubahan jenis unggas" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none"></textarea>
        </div>

        <div class="pt-4 flex items-center justify-end gap-3">
            <a href="{{ route('epu.show', $ladang->id) }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 font-bold text-slate-700 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold shadow-lg shadow-amber-700/30 transition flex items-center gap-2">
                <i class="fa-solid fa-arrows-rotate"></i>
                <span>Hantar Permohonan Pembaharuan (Borang C)</span>
            </button>
        </div>
    </form>
</div>
@endsection
