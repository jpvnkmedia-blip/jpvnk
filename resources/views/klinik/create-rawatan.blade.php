@extends('layouts.app')

@section('title', 'Rekod Rawatan Klinikal Veterinar')
@section('page_title', 'Klinik: Rekod Rawatan Pesakit')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs">
    
    <div class="border-b border-slate-100 pb-4 mb-6">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-100 text-rose-900 text-xs font-bold uppercase mb-2">
            <i class="fa-solid fa-stethoscope"></i> Rekod Klinikal
        </div>
        <h3 class="text-base font-bold text-slate-900">Borang Rekod Pemeriksaan & Rawatan Pesakit</h3>
        <p class="text-xs text-slate-500 mt-0.5">Haiwan: <b>{{ $temujanji->jenis_haiwan }} @if($temujanji->nama_haiwan) ({{ $temujanji->nama_haiwan }}) @endif</b> &bull; Pemilik: {{ $temujanji->pemilik->name ?? '-' }}</p>
    </div>

    <form action="{{ route('klinik.rawatan.store', $temujanji->id) }}" method="POST" class="space-y-4 text-xs">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Berat Badan Haiwan (kg)</label>
                <input type="number" step="0.01" name="berat_badan_kg" placeholder="Contoh: 3.50" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Suhu Badan (°C)</label>
                <input type="number" step="0.1" name="suhu_celsius" placeholder="Contoh: 38.5" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
            </div>
        </div>

        <div>
            <label class="block font-bold text-slate-700 uppercase mb-1">Diagnosis Klinikal Pegawai</label>
            <textarea name="diagnosis" rows="2" required placeholder="Diagnosis penyakit atau status kesihatan haiwan" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none"></textarea>
        </div>

        <div>
            <label class="block font-bold text-slate-700 uppercase mb-1">Rawatan / Prosedur Yang Dijalankan</label>
            <textarea name="rawatan_diberikan" rows="2" required placeholder="Contoh: Suntikan antibiotik, pencucian luka, suntikan vaksin asas" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none"></textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Preskripsi Ubat-Ubatan</label>
                <input type="text" name="ubat_diberikan" placeholder="Contoh: Amoxicillin 50mg, Ubat Cacing" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Vaksinasi (Jika Diberikan)</label>
                <input type="text" name="vaksinasi" placeholder="Contoh: Felocell 3 / Rabies / FMD" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Temujanji Susulan (Jika Perlu)</label>
                <input type="date" name="tarikh_temujanji_susulan" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Caj / Kos Rawatan (RM)</label>
                <input type="number" step="0.01" name="kos_rawatan" value="30.00" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none font-bold">
            </div>
        </div>

        <div>
            <label class="block font-bold text-slate-700 uppercase mb-1">Nasihat Penjagaan Rumah</label>
            <textarea name="nasihat_veterinar" rows="2" placeholder="Nasihat kepada pemilik haiwan" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none"></textarea>
        </div>

        <div class="pt-4 flex items-center justify-end gap-3">
            <a href="{{ route('klinik.show', $temujanji->id) }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 font-bold text-slate-700 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold shadow-lg shadow-rose-700/30 transition flex items-center gap-2">
                <i class="fa-solid fa-check"></i>
                <span>Simpan Rekod & Selesaikan Rawatan</span>
            </button>
        </div>
    </form>
</div>
@endsection
