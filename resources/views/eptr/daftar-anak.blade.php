@extends('layouts.app')

@section('title', 'EPTR - Pendaftaran Kelahiran Anak Ternakan')
@section('page_title', 'EPTR: Pendaftaran Kelahiran Anak Ternakan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    indukList: {{ json_encode($indukList) }},
    bakaData: {{ json_encode($bakaData) }},
    selectedIndukId: '{{ old('induk_id', $selectedInduk->id ?? '') }}',
    selectedJenis: 'lembu',
    selectedBaka: '{{ old('baka_anak', 'kedah-kelantan') }}',
    selectedTarikhLahir: '{{ old('tarikh_kelahiran', date('Y-m-d')) }}',
    kategoriDaftar: '{{ old('kategori_daftar', 'biasa') }}',
    statusKelahiran: '{{ old('status_kelahiran', 'Hidup') }}',
    receiptFileName: '',
    receiptFileSize: '',
    
    get isRuminanBesar() {
        let key = (this.selectedJenis || 'lembu').toLowerCase();
        return key.includes('lembu') || key.includes('kerbau');
    },
    get fiDaftar() { return 2.00; },
    get fiTagging() { return this.isRuminanBesar ? 8.00 : 5.00; },
    get fiDaftarLewat() { return this.isRuminanBesar ? 12.00 : 10.00; },
    get totalFi() {
        return this.kategoriDaftar === 'lewat' 
            ? (this.fiDaftar + this.fiTagging + this.fiDaftarLewat) 
            : (this.fiDaftar + this.fiTagging);
    },
    
    get selectedInduk() {
        return this.indukList.find(i => i.id == this.selectedIndukId) || null;
    },
    get bakaOptions() {
        let key = this.selectedJenis.toLowerCase();
        if (key.includes('lembu')) key = 'lembu';
        else if (key.includes('kerbau')) key = 'kerbau';
        else if (key.includes('biri')) key = 'biri-biri';
        else if (key.includes('kambing')) key = 'kambing';
        return this.bakaData[key] || [];
    },
    onIndukChange() {
        if (this.selectedInduk) {
            this.selectedJenis = this.selectedInduk.jenis_ternakan.toLowerCase();
            this.selectedBaka = this.selectedInduk.baka;
        }
    },
    onTarikhKelahiranChange() {
        if (!this.selectedTarikhLahir) {
            this.kategoriDaftar = 'biasa';
            return;
        }

        const birthDate = new Date(this.selectedTarikhLahir);
        const today = new Date();
        if (isNaN(birthDate.getTime())) return;

        // Kira perbezaan hari
        const diffTime = today.getTime() - birthDate.getTime();
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

        // Enakmen EPTR: Ternakan melebihi 14 hari (> 2 minggu) dikira Pendaftaran Lewat (Seksyen 7)
        if (diffDays > 14) {
            this.kategoriDaftar = 'lewat';
        } else {
            this.kategoriDaftar = 'biasa';
        }
    },
    onFileSelected(e) {
        const file = e.target.files[0];
        if (file) {
            this.receiptFileName = file.name;
            const sizeInKb = (file.size / 1024).toFixed(1);
            this.receiptFileSize = sizeInKb > 1024 ? (sizeInKb / 1024).toFixed(2) + ' MB' : sizeInKb + ' KB';
        } else {
            this.receiptFileName = '';
            this.receiptFileSize = '';
        }
    }
}" x-init="if(selectedInduk) { selectedJenis = selectedInduk.jenis_ternakan.toLowerCase(); } onTarikhKelahiranChange();">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex items-center justify-between border border-slate-700">
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs font-bold uppercase tracking-wider mb-2">
                <i class="fa-solid fa-baby"></i> Rekod Kelahiran Ternakan
            </span>
            <h2 class="text-2xl font-black">Pendaftaran Kelahiran Anak Ternakan</h2>
            <p class="text-xs text-slate-300 mt-1">Daftarkan anak ternakan baru yang dilahirkan dan pautkan secara automatik ke profil Induk & Pemunya</p>
        </div>
        <div class="hidden sm:block text-right">
            <a href="{{ route('eptr.index') }}" class="text-xs font-bold bg-white/10 hover:bg-white/20 text-emerald-300 px-3.5 py-2 rounded-xl border border-white/20 transition inline-flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left"></i> Senarai EPTR
            </a>
        </div>
    </div>

    <!-- Registration Form -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs">
        <form action="{{ route('eptr.store-anak') }}" method="POST" enctype="multipart/form-data" class="space-y-6 text-xs">
            @csrf

            <!-- Section 1: Pemilihan Induk & Pejantan -->
            <div>
                <div class="flex items-center gap-2 pb-3 mb-4 border-b border-slate-100 text-slate-800 font-bold text-sm">
                    <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs font-black">1</span>
                    <span>Bahagian A: Maklumat Induk (Ibu) & Pejantan (Bapa)</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Pilih Induk -->
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Pilih Induk (Ibu yang Melahirkan) <span class="text-rose-500">*</span></label>
                        <select name="induk_id" x-model="selectedIndukId" @change="onIndukChange()" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none font-bold">
                            <option value="">-- Pilih Induk Berdaftar --</option>
                            @foreach($indukList as $ind)
                                <option value="{{ $ind->id }}" {{ (old('induk_id', $selectedInduk->id ?? '') == $ind->id) ? 'selected' : '' }}>
                                    Tag: {{ $ind->no_tag ?? 'ID-'.$ind->id }} &bull; {{ $ind->jenis_ternakan }} ({{ $ind->baka }}) &bull; {{ $ind->pemunya->nama ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Pilih Pejantan -->
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Pilih Baka Pejantan (Bapa - Pilihan)</label>
                        <select name="pejantan_id" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            <option value="">-- Pejantan Tidak Diketahui / Permanian Beradas (AI) --</option>
                            @foreach($pejantanList as $pej)
                                <option value="{{ $pej->id }}" {{ old('pejantan_id') == $pej->id ? 'selected' : '' }}>
                                    Tag: {{ $pej->no_tag ?? 'ID-'.$pej->id }} &bull; {{ $pej->jenis_ternakan }} ({{ $pej->baka }}) &bull; {{ $pej->jajahan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Live Induk Details Preview Box -->
                <template x-if="selectedInduk">
                    <div class="mt-4 p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div>
                            <div class="text-[10px] font-bold text-slate-500 uppercase">Maklumat Induk Terpilih:</div>
                            <div class="font-bold text-slate-900 text-sm mt-0.5">
                                Tag: <span class="font-mono text-emerald-800" x-text="selectedInduk.no_tag || 'Tiada Tag'"></span> &bull; 
                                Spesies: <span class="capitalize" x-text="selectedInduk.jenis_ternakan"></span> &bull; 
                                Baka: <span class="capitalize" x-text="selectedInduk.baka"></span>
                            </div>
                            <div class="text-[11px] text-slate-600 mt-0.5">
                                Pemunya: <b x-text="selectedInduk.pemunya ? selectedInduk.pemunya.nama : '-'"></b> &bull; 
                                Jajahan/Daerah: <span x-text="selectedInduk.jajahan + ' (' + (selectedInduk.daerah || '-') + ')'"></span>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                            Status: <span x-text="selectedInduk.status"></span>
                        </span>
                    </div>
                </template>
            </div>

            <!-- Section 2: Butiran Anak Ternakan -->
            <div class="pt-4 border-t border-slate-100">
                <div class="flex items-center gap-2 pb-3 mb-4 border-b border-slate-100 text-slate-800 font-bold text-sm">
                    <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs font-black">2</span>
                    <span>Bahagian B: Butiran Anak Ternakan yang Dilahirkan</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Kelahiran <span class="text-rose-500">*</span></label>
                        <input type="date" 
                               name="tarikh_kelahiran" 
                               x-model="selectedTarikhLahir"
                               @change="onTarikhKelahiranChange()"
                               @input="onTarikhKelahiranChange()"
                               value="{{ old('tarikh_kelahiran', date('Y-m-d')) }}" 
                               max="{{ date('Y-m-d') }}" 
                               required 
                               class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none font-bold">
                        <div x-show="kategoriDaftar === 'lewat'" x-transition class="mt-1.5 p-2 rounded-xl bg-amber-50 border border-amber-300 text-amber-900 text-[11px] font-semibold flex items-center gap-1.5">
                            <i class="fa-solid fa-triangle-exclamation text-amber-600"></i>
                            <span>Kelahiran melebihi 14 hari: Dikenakan fi Pendaftaran Lewat (Seksyen 7).</span>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Jantina Anak <span class="text-rose-500">*</span></label>
                        <select name="jantina_anak" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none font-bold">
                            <option value="Betina" {{ old('jantina_anak') === 'Betina' ? 'selected' : '' }}>Betina</option>
                            <option value="Jantan" {{ old('jantina_anak') === 'Jantan' ? 'selected' : '' }}>Jantan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Status Kelahiran <span class="text-rose-500">*</span></label>
                        <select name="status_kelahiran" x-model="statusKelahiran" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none font-bold">
                            <option value="Hidup">Hidup (Cergas / Sedia Didaftar)</option>
                            <option value="Mati Semasa Lahir">Mati Semasa Lahir (Stillbirth)</option>
                            <option value="Gugur">Gugur (Abortion)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Baka Anak <span class="text-rose-500">*</span></label>
                        <select name="baka_anak" x-model="selectedBaka" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none capitalize font-semibold">
                            <template x-for="b in bakaOptions" :key="b">
                                <option :value="b" x-text="b" :selected="b === selectedBaka"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Berat Lahir (KG)</label>
                        <input type="number" step="0.1" name="berat_lahir_kg" value="{{ old('berat_lahir_kg', '25.0') }}" placeholder="Contoh: 25.5" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Keadaan / Kesihatan Anak <span class="text-rose-500">*</span></label>
                        <select name="keadaan_anak" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            <option value="Cergas">Cergas & Menyusu Kuat</option>
                            <option value="Sederhana">Sederhana</option>
                            <option value="Lemah">Lemah / Memerlukan Rawatan</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Warna Anak</label>
                        <input type="text" name="warna_anak" value="{{ old('warna_anak', 'Coklat') }}" placeholder="Contoh: Coklat kemerahan, Putih kelabu" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Tanda Badan / Ciri Fizikal</label>
                        <input type="text" name="tanda_badan_anak" value="{{ old('tanda_badan_anak') }}" placeholder="Contoh: Bintang putih di dahi, 4 kaki stoking putih" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Muat Naik Gambar Anak (Pilihan)</label>
                        <input type="file" name="gambar_anak" accept="image/*" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Catatan Tambahan Kelahiran</label>
                        <input type="text" name="catatan" value="{{ old('catatan') }}" placeholder="Contoh: Kelahiran normal tanpa bantuan" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
                    </div>
                </div>
            </div>

            <!-- Section 3: Lampiran Resit Pembayaran Pendaftaran Anak (Wajib bagi Penternak) -->
            <div class="pt-4 border-t border-slate-100" x-show="statusKelahiran === 'Hidup'">
                <div class="flex items-center justify-between pb-3 mb-2 border-b border-slate-100">
                    <div class="flex items-center gap-2 text-slate-800 font-bold text-sm">
                        <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs font-black">3</span>
                        <span>Bahagian C: Lampiran Resit Pembayaran Pendaftaran Anak</span>
                    </div>
                    @if(!Auth::user()->isStaff())
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-700 border border-rose-300 flex items-center gap-1">
                            <i class="fa-solid fa-asterisk text-[9px]"></i> Wajib Dilampirkan
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                            Pilihan (Kakitangan)
                        </span>
                    @endif
                </div>

                <!-- Pengiraan Fi Statutori EPTR Anak -->
                <div class="mb-4 bg-slate-50 rounded-2xl p-4 border border-slate-200 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-lg bg-emerald-100 text-emerald-800 font-bold text-[11px] font-mono">FI STATUTORI</span>
                            <span class="font-bold text-slate-800 text-xs">Kadar Bayaran Pendaftaran & Tagging Anak</span>
                        </div>
                        <a href="{{ route('eptr.jadual-fi') }}" target="_blank" class="text-[11px] font-bold text-emerald-700 hover:underline flex items-center gap-1">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Jadual Fi Rasmi
                        </a>
                    </div>

                    <!-- Pilihan Status Pendaftaran (Auto mengikut Tarikh Kelahiran) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="flex items-center p-2.5 rounded-xl border transition relative select-none"
                             :class="kategoriDaftar === 'biasa' ? 'bg-emerald-50/70 border-emerald-500 ring-2 ring-emerald-500/20 shadow-xs' : 'bg-slate-100/70 border-slate-200 text-slate-400 opacity-60'">
                            <input type="radio" value="biasa" x-model="kategoriDaftar" disabled class="text-emerald-600 mr-2 cursor-not-allowed">
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900" :class="kategoriDaftar === 'biasa' ? 'text-slate-900' : 'text-slate-500'">Pendaftaran Pertama (Biasa)</span>
                                    <span x-show="kategoriDaftar === 'biasa'" class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-emerald-200 text-emerald-900">Dalam 14 Hari</span>
                                </div>
                                <div class="text-[10px]" :class="kategoriDaftar === 'biasa' ? 'text-slate-600' : 'text-slate-400'">Seksyen 5 & 6 (Daftar &amp; Tagging)</div>
                            </div>
                        </div>

                        <div class="flex items-center p-2.5 rounded-xl border transition relative select-none"
                             :class="kategoriDaftar === 'lewat' ? 'bg-amber-50/70 border-amber-500 ring-2 ring-amber-500/20 shadow-xs' : 'bg-slate-100/70 border-slate-200 text-slate-400 opacity-60'">
                            <input type="radio" value="lewat" x-model="kategoriDaftar" disabled class="text-amber-600 mr-2 cursor-not-allowed">
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900" :class="kategoriDaftar === 'lewat' ? 'text-slate-900' : 'text-slate-500'">Pendaftaran Lewat</span>
                                    <span x-show="kategoriDaftar === 'lewat'" class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-amber-200 text-amber-900 flex items-center gap-1">
                                        <i class="fa-solid fa-lock text-[8px]"></i> Auto (&gt; 14 Hari)
                                    </span>
                                </div>
                                <div class="text-[10px]" :class="kategoriDaftar === 'lewat' ? 'text-slate-600' : 'text-slate-400'">Seksyen 7 (Daftar &amp; Tagging + Denda Lewat)</div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden input to submit kategori_daftar -->
                    <input type="hidden" name="kategori_daftar" :value="kategoriDaftar">

                    <!-- Ringkasan Pengiraan Fi -->
                    <div class="p-3.5 bg-gradient-to-r from-slate-900 to-emerald-950 text-white rounded-xl shadow-xs flex items-center justify-between">
                        <div class="space-y-0.5">
                            <div class="text-[11px] text-slate-300">
                                Kategori Anak: <strong class="text-emerald-300" x-text="isRuminanBesar ? 'Ruminan Besar (Lembu / Kerbau)' : 'Ruminan Kecil (Kambing / Bebiri)'"></strong>
                            </div>
                            <div class="text-[10px] text-slate-400">
                                <span x-show="kategoriDaftar === 'biasa'">Pendaftaran Pertama: Daftar (RM2.00) + Penandaan Tag (<span x-text="'RM ' + fiTagging.toFixed(2)"></span>)</span>
                                <span x-show="kategoriDaftar === 'lewat'">Pendaftaran Lewat: Daftar &amp; Tag (<span x-text="'RM ' + (fiDaftar + fiTagging).toFixed(2)"></span>) + Denda Lewat Seksyen 7 (<span x-text="'RM ' + fiDaftarLewat.toFixed(2)"></span>)</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] text-slate-300 uppercase block font-semibold">Jumlah Fi Resit</span>
                            <span class="text-lg font-black text-amber-300 font-mono" x-text="'RM ' + totalFi.toFixed(2)"></span>
                        </div>
                    </div>
                </div>

                <div class="p-5 bg-slate-50 rounded-2xl border-2 border-dashed {{ $errors->has('resit_pembayaran') ? 'border-rose-400 bg-rose-50/20' : 'border-slate-300 hover:border-emerald-500' }} transition text-center relative">
                    <input type="file" name="resit_pembayaran" id="resit_pembayaran_anak" @change="onFileSelected($event)" accept="image/jpeg,image/png,image/jpg,application/pdf" {{ !Auth::user()->isStaff() ? 'required' : '' }} class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    
                    <div class="space-y-2 pointer-events-none">
                        <div class="w-12 h-12 mx-auto rounded-full {{ $errors->has('resit_pembayaran') ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }} flex items-center justify-center text-xl shadow-xs">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>
                        <div class="font-bold text-slate-800 text-sm">
                            <span x-show="!receiptFileName">Klik atau Seret Resit Pembayaran di Sini {{ !Auth::user()->isStaff() ? '(Wajib)' : '' }}</span>
                            <span x-show="receiptFileName" class="text-emerald-700 font-mono" x-text="receiptFileName"></span>
                        </div>
                        <p class="text-[11px] text-slate-500" x-show="!receiptFileName">
                            Format yang disokong: <b>PDF, JPG, PNG</b> (Maksimum 2MB)
                        </p>
                        <p class="text-[11px] text-emerald-600 font-semibold" x-show="receiptFileSize">
                            Saiz Fail: <span x-text="receiptFileSize"></span>
                        </p>
                    </div>
                </div>
                @error('resit_pembayaran')
                    <p class="text-xs font-bold text-rose-600 mt-2 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span>{{ $message }}</span>
                    </p>
                @enderror
            </div>

            <!-- Auto Registration Notice -->
            <div x-show="statusKelahiran === 'Hidup'" class="p-4 bg-emerald-50 rounded-2xl border border-emerald-200 text-emerald-900 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-700 text-white flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-circle-check text-sm"></i>
                </div>
                <div class="text-[11px]">
                    <div class="font-bold text-emerald-950">Pendaftaran Automatik Profil EPTR Anak</div>
                    <p class="text-emerald-800 mt-0.5">
                        Bagi anak yang dilahirkan hidup, sistem akan <b>mewujudkan profil EPTR baharu untuk anak ini</b> secara automatik, mewarisi pemunya induk dan merekodkan salasilah induk secara rasmi.
                    </p>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('eptr.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-7 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-black text-sm rounded-xl shadow-lg shadow-emerald-700/20 transition flex items-center gap-2">
                    <i class="fa-solid fa-save"></i>
                    <span>Simpan Rekod Kelahiran & Daftar Anak</span>
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
