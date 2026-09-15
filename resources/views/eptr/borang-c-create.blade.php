@extends('layouts.app')

@section('title', 'Hantar Notis EPTR Borang C')
@section('page_title', 'EPTR Borang C: Notis Pembatalan / Kematian / Pindah Keluar')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs" x-data="{
    selectedTernakanId: '{{ old('ternakan_id', $selectedTernakan->id ?? '') }}',
    jenisBatal: '{{ old('jenis_batal', 'Mati') }}',
    fileName: '',
    ternakanList: {{ json_encode($ternakanList->map(fn($t) => [
        'id' => $t->id,
        'no_tag' => $t->no_tag,
        'jenis' => $t->jenis_ternakan,
        'baka' => $t->baka
    ])) }},
    get selectedTernakan() {
        return this.ternakanList.find(t => t.id == this.selectedTernakanId) || null;
    },
    get isRuminanBesar() {
        if (!this.selectedTernakan) return true;
        let j = (this.selectedTernakan.jenis || 'lembu').toLowerCase();
        return j.includes('lembu') || j.includes('kerbau');
    },
    get fiPembatalan() {
        if (this.jenisBatal === 'Pindah Keluar') {
            return this.isRuminanBesar ? 20.00 : 10.00;
        } else if (this.jenisBatal === 'Sembelihan') {
            return this.isRuminanBesar ? 10.00 : 5.00;
        }
        return 0.00;
    },
    get peruntukanEnakmen() {
        if (this.jenisBatal === 'Pindah Keluar') return 'Perenggan 11(1)(c) - Pindah Keluar Negeri';
        if (this.jenisBatal === 'Sembelihan') return 'Perenggan 11(1)(b) - Sembelihan';
        return 'Tiada fi berkanun bagi kematian/kecurian/pelupusan';
    },
    get dokumenLabel() {
        if (this.jenisBatal === 'Mati') return 'Surat Pengesahan Kematian Veterinar / Gambar Bangkai / Laporan Post-Mortem';
        if (this.jenisBatal === 'Kecurian') return 'Salinan Laporan Polis / Dokumen Berkaitan';
        if (this.jenisBatal === 'Pindah Keluar') return 'Resit Bayaran Fi Statutori Pembatalan (RM 20 / RM 10) / Salinan Permit Pemindahan';
        if (this.jenisBatal === 'Sembelihan') return 'Resit Bayaran Fi Pembatalan (RM 10 / RM 5) / Salinan Surat Kebenaran';
        return 'Dokumen Sokongan / Gambar / Surat Akuan Berkaitan';
    }
}">
    
    <div class="border-b border-slate-100 pb-4 mb-6">
        <div class="inline-block bg-rose-100 text-rose-800 text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded-full mb-2">
            JADUAL KETIGA &bull; BORANG C
        </div>
        <h3 class="text-base font-bold text-slate-900">Borang Pemberitahuan Peristiwa Ternakan (Borang C)</h3>
        <p class="text-xs text-slate-500 mt-0.5">Sila lengkapkan maklumat bagi melaporkan kematian, kecurian, pelupusan atau pemindahan keluar ternakan.</p>
    </div>

    <form action="{{ route('eptr.borang-c.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
        @csrf

        <div>
            <label class="block font-bold text-slate-700 uppercase mb-1">Pilih Ternakan Ruminan <span class="text-rose-500">*</span></label>
            <select name="ternakan_id" x-model="selectedTernakanId" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none font-bold">
                <option value="">-- Pilih Lembu / Ternakan --</option>
                @foreach($ternakanList as $t)
                    <option value="{{ $t->id }}" {{ (old('ternakan_id') == $t->id || (isset($selectedTernakan) && $selectedTernakan->id == $t->id)) ? 'selected' : '' }}>
                        {{ $t->no_tag }} &bull; {{ $t->jenis_ternakan }} ({{ $t->baka }}) - {{ $t->pemunya->nama ?? 'Pemunya' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Jenis Peristiwa / Pembatalan <span class="text-rose-500">*</span></label>
                <select name="jenis_batal" x-model="jenisBatal" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none font-bold">
                    <option value="Mati">Kematian Ternakan (Tiada Fi)</option>
                    <option value="Kecurian">Kecurian Ternakan (Tiada Fi)</option>
                    <option value="Pindah Keluar">Pindah Keluar Negeri Kelantan (Fi Statutori)</option>
                    <option value="Sembelihan">Sembelihan Ternakan (Fi Statutori)</option>
                    <option value="Pelupusan">Pelupusan / Lupus (Tiada Fi)</option>
                    <option value="Lain-lain">Lain-lain</option>
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Peristiwa Berlaku <span class="text-rose-500">*</span></label>
                <input type="date" name="tarikh_peristiwa" value="{{ old('tarikh_peristiwa', date('Y-m-d')) }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>
        </div>

        <div>
            <label class="block font-bold text-slate-700 uppercase mb-1">Sebab / Kronologi Kejadian <span class="text-rose-500">*</span></label>
            <textarea name="sebab" rows="3" required placeholder="Nyatakan punca kematian (cth: penyakit kembung / kemalangan) atau butiran kejadian" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">{{ old('sebab') }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">No. Laporan Polis (Jika Kecurian)</label>
                <input type="text" name="no_laporan_polis" value="{{ old('no_laporan_polis') }}" placeholder="Contoh: RPT/KB/2026/0122" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none font-mono">
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Destinasi (Jika Pindah Keluar Negeri)</label>
                <input type="text" name="destinasi_pindah_keluar" value="{{ old('destinasi_pindah_keluar') }}" placeholder="Contoh: Ladang Besut, Terengganu" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>
        </div>

        <!-- Muat Naik Dokumen Berkaitan / Dokumen Sokongan -->
        <div class="p-4 rounded-2xl bg-rose-50/50 border border-rose-200 space-y-2">
            <label class="block font-bold text-slate-800 uppercase">
                <div class="flex items-center justify-between">
                    <span>Muat Naik Dokumen Berkaitan</span>
                    <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-rose-100 text-rose-800" x-text="jenisBatal"></span>
                </div>
                <span class="text-slate-500 font-normal normal-case block text-[11px] mt-1" x-text="'Disyorkan: ' + dokumenLabel"></span>
            </label>
            <div class="mt-1 flex justify-center px-4 pt-4 pb-4 border-2 border-dashed border-rose-300 hover:border-rose-500 bg-white rounded-xl transition">
                <div class="space-y-1.5 text-center">
                    <i class="fa-solid fa-cloud-arrow-up text-2xl text-rose-500"></i>
                    <div class="flex text-xs text-slate-600 justify-center items-center gap-1">
                        <label for="dokumen_sokongan" class="relative cursor-pointer font-bold text-rose-600 hover:text-rose-700 focus-within:outline-none underline">
                            <span>Pilih Fail Dokumen</span>
                            <input id="dokumen_sokongan" name="dokumen_sokongan" type="file" accept=".pdf,.jpg,.jpeg,.png" class="sr-only" @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                        </label>
                        <span>atau seret fail ke sini</span>
                    </div>
                    <p class="text-[10px] text-slate-400">Disokong: PDF, JPG, JPEG, PNG (Maksimum 5MB)</p>
                    <div x-show="fileName" class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200 mt-2 flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        <span x-text="fileName"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pengiraan Fi Statutori Pembatalan -->
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2.5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-1.5 font-bold text-slate-800 text-xs">
                    <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-800 text-[10px] font-mono">FI STATUTORI</span>
                    <span>Kadar Fi Pembatalan EPTR</span>
                </div>
                <a href="{{ route('eptr.jadual-fi') }}" target="_blank" class="text-[11px] font-bold text-rose-700 hover:underline flex items-center gap-1">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Jadual Fi
                </a>
            </div>

            <div class="p-3 bg-gradient-to-r from-slate-900 to-rose-950 text-white rounded-xl flex items-center justify-between">
                <div class="space-y-0.5">
                    <div class="text-[11px] text-slate-300 font-medium" x-text="peruntukanEnakmen"></div>
                    <div class="text-[10px] text-slate-400">
                        Kategori: <span class="text-rose-300 font-bold" x-text="isRuminanBesar ? 'Ruminan Besar' : 'Ruminan Kecil'"></span>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-[10px] text-slate-400 uppercase block">Jumlah Fi</span>
                    <span class="text-base font-black text-amber-300 font-mono" x-text="'RM ' + fiPembatalan.toFixed(2)"></span>
                </div>
            </div>
        </div>

        <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
            <a href="{{ route('eptr.borang-c.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 font-bold text-slate-700 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold shadow-lg shadow-rose-700/30 transition flex items-center gap-2">
                <i class="fa-solid fa-paper-plane"></i>
                <span>Hantar Notis Borang C</span>
            </button>
        </div>
    </form>
</div>
@endsection
