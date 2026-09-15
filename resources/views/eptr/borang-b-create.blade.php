@extends('layouts.app')

@section('title', 'Borang B - Permohonan Pindah Milik Ternakan')
@section('page_title', 'EPTR Borang B: Notis Pertukaran / Pemindahan Milikan Ternakan Ruminan')

@section('content')
@php
    $pemunyaJsonData = $pemunyaList->map(function($p) {
        return [
            'id' => $p->id,
            'nama' => $p->nama,
            'no_kp' => $p->no_kp,
            'no_kp_clean' => preg_replace('/[^0-9]/', '', $p->no_kp ?? ''),
            'no_telefon' => $p->no_telefon ?? '',
            'jajahan' => $p->jajahan ?? '',
            'daerah' => $p->daerah ?? '',
            'poskod' => $p->poskod ?? '',
            'alamat' => $p->alamat ?? '',
            'lokasi_kandang' => $p->lokasi_kandang ?? '',
        ];
    });
@endphp

<div class="max-w-4xl mx-auto space-y-6" x-data="{
    pemunyaList: {{ json_encode($pemunyaJsonData) }},
    jenisPemunya: '{{ old('jenis_pemunya_baru', 'sedia_ada') }}',
    sebabPindah: '{{ old('sebab_pindah', 'Jualan') }}',
    jajahanSelected: '{{ old('jajahan_pemunya_baru', '') }}',
    daerahSelected: '{{ old('daerah_pemunya_baru', '') }}',
    jajahanMap: {{ json_encode($kelantanData) }},
    mohonSalinanPendua: false,
    selectedTernakanIds: {{ json_encode(array_map('intval', old('ternakan_ids', (old('ternakan_id') ? [old('ternakan_id')] : (isset($selectedTernakan) ? [$selectedTernakan->id] : []))))) }},
    allTernakanIds: {{ json_encode($ternakanList->pluck('id')) }},
    ternakanSearch: '',
    receiptFileName: '',
    receiptFileSize: '',
    
    // Semakan No. Kad Pengenalan
    icInput: '{{ old('no_kp_semakan', old('no_kp_pemunya_baru', '')) }}',
    statusSemakan: '{{ old('pemunya_baru_id') ? 'found' : (old('jenis_pemunya_baru') === 'baru' ? 'not_found' : (old('no_kp_pemunya_baru') ? 'not_found' : 'idle')) }}',
    matchedPemunya: null,
    pemunyaBaruId: '{{ old('pemunya_baru_id', '') }}',
    namaPemunyaBaru: '{{ old('nama_pemunya_baru', '') }}',
    noKpPemunyaBaru: '{{ old('no_kp_pemunya_baru', '') }}',
    noTelPemunyaBaru: '{{ old('no_tel_pemunya_baru', '') }}',
    poskodPemunyaBaru: '{{ old('poskod_pemunya_baru', '') }}',
    alamatPemunyaBaru: '{{ old('alamat_pemunya_baru', '') }}',
    lokasiKandangBaru: '{{ old('lokasi_kandang_baru', '') }}',
    showManualSelect: false,

    init() {
        if (this.pemunyaBaruId) {
            const found = this.pemunyaList.find(p => p.id == this.pemunyaBaruId);
            if (found) {
                this.matchedPemunya = found;
                this.statusSemakan = 'found';
                this.jenisPemunya = 'sedia_ada';
                if (!this.icInput) this.icInput = found.no_kp;
            }
        } else if (this.icInput) {
            this.semakIC(this.icInput);
        }
    },
    cleanIC(val) {
        return (val || '').toString().replace(/\D/g, '');
    },
    semakIC(customIc = null) {
        const raw = (customIc !== null ? customIc : this.icInput) || '';
        const cleaned = this.cleanIC(raw);
        if (!cleaned || cleaned.length < 3) {
            this.statusSemakan = 'idle';
            this.matchedPemunya = null;
            return;
        }

        const match = this.pemunyaList.find(p => {
            return (p.no_kp_clean && p.no_kp_clean === cleaned) || 
                   (p.no_kp && p.no_kp.trim().toLowerCase() === raw.trim().toLowerCase());
        });

        if (match) {
            this.matchedPemunya = match;
            this.statusSemakan = 'found';
            this.jenisPemunya = 'sedia_ada';
            this.pemunyaBaruId = match.id;
            this.namaPemunyaBaru = match.nama;
            this.noKpPemunyaBaru = match.no_kp;
            this.noTelPemunyaBaru = match.no_telefon || '';
            this.jajahanSelected = match.jajahan || '';
            this.daerahSelected = match.daerah || '';
            this.poskodPemunyaBaru = match.poskod || '';
            this.alamatPemunyaBaru = match.alamat || '';
            this.lokasiKandangBaru = match.lokasi_kandang || '';
        } else {
            this.matchedPemunya = null;
            this.statusSemakan = 'not_found';
            this.jenisPemunya = 'baru';
            this.pemunyaBaruId = '';
            this.noKpPemunyaBaru = raw;
        }
    },
    pilihPemunyaDropdown(pemId) {
        if (!pemId) {
            this.resetSemakan();
            return;
        }
        const match = this.pemunyaList.find(p => p.id == pemId);
        if (match) {
            this.icInput = match.no_kp;
            this.semakIC(match.no_kp);
        }
    },
    resetSemakan() {
        this.icInput = '';
        this.statusSemakan = 'idle';
        this.matchedPemunya = null;
        this.pemunyaBaruId = '';
        this.namaPemunyaBaru = '';
        this.noKpPemunyaBaru = '';
        this.noTelPemunyaBaru = '';
        this.jajahanSelected = '';
        this.daerahSelected = '';
        this.poskodPemunyaBaru = '';
        this.alamatPemunyaBaru = '';
        this.lokasiKandangBaru = '';
    },
    onFileSelected(e) {
        const file = e.target.files[0];
        if (file) {
            this.receiptFileName = file.name;
            const sizeInKB = (file.size / 1024).toFixed(1);
            this.receiptFileSize = sizeInKB > 1024 ? (sizeInKB / 1024).toFixed(2) + ' MB' : sizeInKB + ' KB';
        } else {
            this.receiptFileName = '';
            this.receiptFileSize = '';
        }
    },
    toggleSelectAll() {
        if (this.selectedTernakanIds.length === this.allTernakanIds.length) {
            this.selectedTernakanIds = [];
        } else {
            this.selectedTernakanIds = [...this.allTernakanIds];
        }
    },
    get selectedCount() {
        return this.selectedTernakanIds.length;
    },
    get totalFi() {
        const count = this.selectedCount > 0 ? this.selectedCount : 0;
        const baseFee = count * 2.00;
        const penduaFee = this.mohonSalinanPendua ? (count * 10.00) : 0.00;
        return baseFee + penduaFee;
    },
    get daerahList() {
        if (!this.jajahanSelected || !this.jajahanMap[this.jajahanSelected]) return [];
        return this.jajahanMap[this.jajahanSelected].daerah || [];
    }
}">

    <!-- Header Card -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-100 pb-5">
            <div>
                <div class="inline-block bg-blue-100 text-blue-800 text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded-full mb-2">
                    JADUAL KEDUA &bull; BORANG B [SEKSYEN 7]
                </div>
                <h2 class="text-xl font-black text-slate-900">Borang Notis Pertukaran Milikan Ternakan Ruminan</h2>
                <p class="text-xs text-slate-500 mt-1">Lengkapkan maklumat pertukaran atau pemindahan hak milik ternakan ruminan berdaftar (boleh pilih satu atau banyak ekor sekali gus) kepada pemilik / pembeli baharu.</p>
            </div>
            <a href="{{ route('eptr.borang-b.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 px-3.5 py-2 rounded-xl border border-slate-200 transition">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Senarai</span>
            </a>
        </div>

        @if($errors->any())
            <div class="mt-4 p-4 bg-rose-50 rounded-2xl border border-rose-200 text-rose-800 text-xs space-y-1">
                <div class="font-bold flex items-center gap-1.5"><i class="fa-solid fa-triangle-exclamation"></i> Sila semak ralat berikut:</div>
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('eptr.borang-b.store') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-6 text-xs">
            @csrf

            <!-- 1. PILIH TERNAKAN (BOLEH BANYAK EKOR SEKALI GUS) -->
            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-3">
                    <div class="flex items-center gap-2 text-slate-900 font-bold text-sm">
                        <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs">1</span>
                        <span>PILIHAN TERNAKAN RUMINAN (BOLEH BANYAK EKOR SEKALI GUS)</span>
                        <span class="text-rose-500">*</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-black bg-blue-100 text-blue-800 border border-blue-200">
                            <i class="fa-solid fa-cow mr-1"></i>
                            <span x-text="selectedCount"></span> / {{ count($ternakanList) }} Ekor Dipilih
                        </span>
                        @if(count($ternakanList) > 1)
                            <button type="button" @click="toggleSelectAll()" class="px-3 py-1 rounded-xl bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 font-bold text-xs transition">
                                <span x-show="selectedTernakanIds.length < allTernakanIds.length">Pilih Semua</span>
                                <span x-show="selectedTernakanIds.length === allTernakanIds.length">Nyahpilih Semua</span>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Carian Pantas Ternakan -->
                @if(count($ternakanList) > 4)
                    <div class="relative">
                        <input type="text" x-model="ternakanSearch" placeholder="Cari No. Tag / Baka / Jantina dalam senarai..." class="w-full pl-9 pr-3.5 py-2 text-xs bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <i class="fa-solid fa-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                    </div>
                @endif

                <!-- Senarai Pilihan Ternakan Berdaftar -->
                <div class="space-y-2 max-h-80 overflow-y-auto pr-1">
                    @forelse($ternakanList as $t)
                        @php
                            $searchKey = strtolower($t->no_tag . ' ' . $t->baka . ' ' . $t->jantina . ' ' . ($t->warna ?? ''));
                        @endphp
                        <label class="flex items-center justify-between p-3.5 rounded-xl border transition cursor-pointer select-none"
                               x-show="!ternakanSearch || '{{ $searchKey }}'.includes(ternakanSearch.toLowerCase())"
                                :class="selectedTernakanIds.includes({{ $t->id }}) ? 'bg-blue-50/80 border-blue-500 ring-2 ring-blue-500/20' : 'bg-white border-slate-200 hover:bg-slate-50'">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" name="ternakan_ids[]" value="{{ $t->id }}" x-model.number="selectedTernakanIds" class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-bold text-slate-900 text-sm flex items-center gap-1">
                                            <i class="fa-solid fa-tag text-blue-600 text-xs"></i>
                                            <span>{{ $t->no_tag }}</span>
                                        </span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $t->jantina === 'Jantan' ? 'bg-indigo-100 text-indigo-800' : 'bg-rose-100 text-rose-800' }}">
                                            {{ $t->jantina }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700 capitalize">
                                            {{ $t->baka }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        Umur: <strong class="text-slate-700">{{ $t->umur ?? '-' }}</strong> &bull; 
                                        Warna: {{ $t->warna ?: 'Tiada' }} &bull; 
                                        Jajahan: <span class="uppercase font-semibold text-slate-700">{{ $t->jajahan }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right hidden sm:block">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    {{ $t->status }} ({{ $t->status_kelulusan }})
                                </span>
                            </div>
                        </label>
                    @empty
                        <div class="p-6 text-center text-slate-400 bg-white rounded-xl border border-slate-200">
                            <i class="fa-solid fa-cow text-2xl mb-2 text-slate-300"></i>
                            <p class="font-semibold text-xs text-slate-600">Tiada ternakan aktif yang tersedia untuk dipindah milik.</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Hanya ternakan berdaftar berstatus Aktif / Pawah yang telah diluluskan boleh dipindah milik.</p>
                        </div>
                    @endforelse
                </div>
                <p class="text-[11px] text-slate-500">Tandakan semua ternakan yang hendak dipindahkan kepada pemilik baharu dalam satu permohonan Borang B.</p>
            </div>

            <!-- 2. MAKLUMAT PEMUNYA / PENTERNAK BAHARU (PENERIMA / PEMBELI) -->
            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200 pb-3">
                    <div class="flex items-center gap-2 text-slate-900 font-bold text-sm">
                        <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs">2</span>
                        <span>MAKLUMAT PEMUNYA / PENTERNAK BAHARU (PENERIMA / PEMBELI)</span>
                        <span class="text-rose-500">*</span>
                    </div>
                    <div class="text-[11px] text-slate-500 font-medium">
                        Semakan automatik No. Kad Pengenalan penternak lama / baharu
                    </div>
                </div>

                <!-- Hidden inputs to submit values -->
                <input type="hidden" name="jenis_pemunya_baru" :value="jenisPemunya">
                <input type="hidden" name="pemunya_baru_id" :value="pemunyaBaruId">

                <!-- Kotak Carian / Semakan No. Kad Pengenalan -->
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-xs space-y-3">
                    <label class="block font-bold text-slate-800 uppercase text-xs">
                        Masukkan No. Kad Pengenalan (MyKad) Penerima / Pembeli
                        <span class="text-rose-500">*</span>
                    </label>

                    <div class="flex flex-col sm:flex-row gap-2">
                        <div class="relative flex-1">
                            <input type="text"
                                   name="no_kp_semakan"
                                   x-model="icInput"
                                   @input.debounce.300ms="semakIC()"
                                   @change="semakIC()"
                                   @keydown.enter.prevent="semakIC()"
                                   placeholder="Contoh: 880512035541 atau 880512-03-5541"
                                   list="senarai_ic_pemunya_datalist"
                                   class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 focus:bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none font-mono">
                            <i class="fa-solid fa-id-card absolute left-3.5 top-3.5 text-slate-400"></i>

                            <datalist id="senarai_ic_pemunya_datalist">
                                <template x-for="p in pemunyaList" :key="p.id">
                                    <option :value="p.no_kp" x-text="p.no_kp + ' - ' + p.nama + ' (' + p.jajahan + ')'"></option>
                                </template>
                            </datalist>
                        </div>

                        <button type="button"
                                @click="semakIC()"
                                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <span>Semak No. KP</span>
                        </button>

                        <button type="button"
                                x-show="icInput || statusSemakan !== 'idle'"
                                @click="resetSemakan()"
                                class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition flex items-center justify-center gap-1">
                            <i class="fa-solid fa-xmark"></i>
                            <span>Padam</span>
                        </button>
                    </div>

                    <!-- Pilihan Dropdown Pantas & Panduan -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 text-[11px] text-slate-500 pt-1">
                        <span>Sistem akan mengesahkan secara automatik sama ada penternak lama atau baharu.</span>
                        <button type="button" @click="showManualSelect = !showManualSelect" class="text-blue-600 hover:underline font-semibold flex items-center gap-1">
                            <i class="fa-solid fa-list-ul text-[10px]"></i>
                            <span x-text="showManualSelect ? 'Sembunyi Pilihan Dropdown' : 'Pilih dari Dropdown Penternak Berdaftar'"></span>
                        </button>
                    </div>

                    <!-- Dropdown Tambahan untuk pilih nama secara terus -->
                    <div x-show="showManualSelect" x-transition class="pt-2">
                        <select @change="pilihPemunyaDropdown($event.target.value)" class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">-- Pilih Daripada Senarai Penternak Berdaftar --</option>
                            @foreach($pemunyaList as $pem)
                                <option value="{{ $pem->id }}">
                                    {{ $pem->nama }} &bull; No. KP: {{ $pem->no_kp }} &bull; Jajahan {{ $pem->jajahan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- HASIL SEMAKAN A: PENTERNAK SEDIA ADA (LAMA) BERDAFTAR -->
                <div x-show="statusSemakan === 'found' && matchedPemunya" x-transition class="bg-emerald-50/90 rounded-2xl p-5 border border-emerald-300 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2 border-b border-emerald-200">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>
                            <div>
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-200/80 text-emerald-900 font-black text-[10px] uppercase tracking-wider">
                                    <i class="fa-solid fa-badge-check text-emerald-700"></i> Penternak Sedia Ada Berdaftar (EPTR)
                                </div>
                                <div class="font-bold text-slate-900 text-sm mt-0.5" x-text="matchedPemunya?.nama"></div>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-white text-emerald-800 border border-emerald-200">
                                Status: Berdaftar Aktif
                            </span>
                        </div>
                    </div>

                    <!-- Kad Maklumat Profil Penternak Sedia Ada -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 bg-white p-4 rounded-xl border border-emerald-200 text-xs">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">No. Kad Pengenalan</span>
                            <span class="font-mono font-bold text-slate-800 text-sm" x-text="matchedPemunya?.no_kp"></span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">No. Telefon</span>
                            <span class="font-semibold text-slate-800" x-text="matchedPemunya?.no_telefon || 'Tiada Rekod'"></span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Jajahan / Daerah</span>
                            <span class="font-semibold text-slate-800 uppercase" x-text="(matchedPemunya?.jajahan || '-') + (matchedPemunya?.daerah ? ' (' + matchedPemunya?.daerah + ')' : '')"></span>
                        </div>
                        <div class="sm:col-span-2 md:col-span-3">
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Alamat Berdaftar</span>
                            <span class="text-slate-700" x-text="matchedPemunya?.alamat || 'Tiada Alamat Lengkap'"></span>
                        </div>
                    </div>

                    <p class="text-[11px] text-emerald-800 flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>Maklumat penerima ini telah dipautkan secara automatik ke rekod penternak sedia ada dalam sistem EPTR.</span>
                    </p>
                </div>

                <!-- HASIL SEMAKAN B: PENTERNAK BAHARU (BELUM BERDAFTAR) -->
                <div x-show="statusSemakan === 'not_found'" x-transition class="bg-blue-50/80 rounded-2xl p-5 border border-blue-300 space-y-4">
                    <div class="flex items-center gap-2 pb-2 border-b border-blue-200">
                        <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                            <i class="fa-solid fa-user-plus"></i>
                        </div>
                        <div>
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-200/80 text-blue-900 font-black text-[10px] uppercase tracking-wider">
                                <i class="fa-solid fa-sparkles text-blue-700"></i> Penternak Baharu (Belum Berdaftar)
                            </div>
                            <div class="text-xs text-blue-800 mt-0.5">
                                No. Kad Pengenalan <strong class="font-mono" x-text="icInput"></strong> belum didaftarkan di dalam sistem EPTR. Sila lengkapkan maklumat penternak baharu di bawah:
                            </div>
                        </div>
                    </div>

                    <!-- Form fields for New Owner -->
                    <div class="space-y-4 pt-1">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-bold text-slate-700 uppercase mb-1">Nama Penuh Pemunya Baharu <span class="text-rose-500">*</span></label>
                                <input type="text"
                                       name="nama_pemunya_baru"
                                       x-model="namaPemunyaBaru"
                                       placeholder="Contoh: AHMAD BIN ISMAIL"
                                       class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none uppercase">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 uppercase mb-1">No. Kad Pengenalan (MyKad) <span class="text-rose-500">*</span></label>
                                <input type="text"
                                       name="no_kp_pemunya_baru"
                                       x-model="noKpPemunyaBaru"
                                       placeholder="Contoh: 880512035541"
                                       class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none font-mono">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-bold text-slate-700 uppercase mb-1">No. Telefon Bimbit <span class="text-rose-500">*</span></label>
                                <input type="text"
                                       name="no_tel_pemunya_baru"
                                       x-model="noTelPemunyaBaru"
                                       placeholder="Contoh: 019-1234567"
                                       class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 uppercase mb-1">Jajahan <span class="text-rose-500">*</span></label>
                                <select name="jajahan_pemunya_baru"
                                        x-model="jajahanSelected"
                                        class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    <option value="">-- Pilih Jajahan --</option>
                                    @foreach(array_keys($kelantanData) as $jajahan)
                                        <option value="{{ $jajahan }}">{{ $jajahan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-bold text-slate-700 uppercase mb-1">Daerah</label>
                                <select name="daerah_pemunya_baru"
                                        x-model="daerahSelected"
                                        class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    <option value="">-- Pilih Daerah --</option>
                                    <template x-for="daerah in daerahList" :key="daerah">
                                        <option :value="daerah" x-text="daerah"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 uppercase mb-1">Poskod</label>
                                <input type="text"
                                       name="poskod_pemunya_baru"
                                       x-model="poskodPemunyaBaru"
                                       placeholder="Contoh: 15000"
                                       class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none font-mono">
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">Alamat Surat-Menyurat / Kediaman <span class="text-rose-500">*</span></label>
                            <textarea name="alamat_pemunya_baru"
                                      x-model="alamatPemunyaBaru"
                                      rows="2"
                                      placeholder="Alamat penuh pemilik baharu"
                                      class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">Lokasi Kandang / Tempat Ternakan Ditempatkan</label>
                            <input type="text"
                                   name="lokasi_kandang_baru"
                                   x-model="lokasiKandangBaru"
                                   placeholder="Contoh: Kandang Kg. Tok Uban, Pasir Mas"
                                   class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- HASIL SEMAKAN C: STATUS IDLE (BELUM MASUKKAN NO. KP) -->
                <div x-show="statusSemakan === 'idle'" class="p-4 bg-white rounded-xl border border-dashed border-slate-300 text-center text-slate-500 text-xs">
                    <i class="fa-solid fa-id-card-clip text-2xl text-slate-300 mb-1 block"></i>
                    <span>Sila masukkan <b>No. Kad Pengenalan (MyKad)</b> penerima / pembeli pada ruangan di atas untuk membuat semakan status pendaftaran (Penternak Sedia Ada atau Penternak Baharu).</span>
                </div>
            </div>

            <!-- 3. BUTIRAN PINDAH MILIK -->
            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200 space-y-4">
                <div class="flex items-center gap-2 text-slate-900 font-bold text-sm border-b border-slate-200 pb-2">
                    <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs">3</span>
                    <span>BUTIRAN TRANSAKSI & SEBAB PEMINDAHAN MILIK</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Pemindahan / Akad <span class="text-rose-500">*</span></label>
                        <input type="date" name="tarikh_pindah" value="{{ old('tarikh_pindah', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Sebab Pertukaran Milikan <span class="text-rose-500">*</span></label>
                        <select name="sebab_pindah" x-model="sebabPindah" required class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="Jualan">Jualan / Urus Niaga</option>
                            <option value="Hibah / Hadiah">Hibah / Hadiah</option>
                            <option value="Pewarisan / Pusaka">Pewarisan / Harta Pusaka</option>
                            <option value="Pajakan / Kongsi">Pajakan / Kongsi Ternak</option>
                            <option value="Lain-lain">Lain-lain Sebab</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div x-show="sebabPindah === 'Jualan'" x-transition>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Harga Jualan Ternakan (RM)</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-2.5 font-bold text-slate-400">RM</span>
                            <input type="number" step="0.01" min="0" name="harga_jualan" value="{{ old('harga_jualan') }}" placeholder="Contoh: 3500.00" class="w-full pl-11 pr-3.5 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none font-mono">
                        </div>
                    </div>

                    <div :class="sebabPindah === 'Jualan' ? '' : 'sm:col-span-2'">
                        <label class="block font-bold text-slate-700 uppercase mb-1">Catatan Tambahan</label>
                        <input type="text" name="catatan" value="{{ old('catatan') }}" placeholder="Contoh: Resit jualan No. 12345 / Diserahkan bersama anak" class="w-full px-3.5 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- 4. PENGIRAAN FI STATUTORI BORANG B -->
            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200 space-y-3">
                <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                    <div class="flex items-center gap-2 text-slate-900 font-bold text-sm">
                        <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs">4</span>
                        <span>PENGIRAAN FI STATUTORI PINDAH MILIK</span>
                    </div>
                    <a href="{{ route('eptr.jadual-fi') }}" target="_blank" class="text-[11px] font-bold text-blue-700 hover:underline flex items-center gap-1">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i> Jadual Fi Rasmi
                    </a>
                </div>

                <!-- Opsyen Salinan Pendua -->
                <div class="bg-white p-3.5 rounded-xl border border-slate-200">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="mohon_salinan_pendua" value="1" x-model="mohonSalinanPendua" class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500 mt-0.5">
                        <div>
                            <span class="font-bold text-slate-800 text-xs">Permohonan Salinan Pendua Borang B (+ RM 10.00)</span>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                Tandakan jika pemohon memerlukan salinan pendua rasmi Borang B mengikut <strong>Subseksyen 44(3)</strong>.
                            </p>
                        </div>
                    </label>
                </div>

                <!-- Ringkasan Jumlah Fi -->
                <div class="p-3.5 bg-gradient-to-r from-slate-900 via-slate-800 to-blue-950 text-white rounded-xl shadow-xs flex items-center justify-between">
                    <div class="space-y-0.5">
                        <div class="text-[11px] text-slate-300">
                            Fi Pindah Milik (Seksyen 8): <strong class="text-blue-300 font-mono">RM 2.00 &times; <span x-text="selectedCount"></span> ekor</strong> = <strong class="text-white font-mono" x-text="'RM ' + (selectedCount * 2.00).toFixed(2)"></strong>
                        </div>
                        <div class="text-[10px] text-slate-400">
                            <span x-show="mohonSalinanPendua">+ Fi Salinan Pendua Borang B Subseksyen 44(3) (RM 10.00 &times; <span x-text="selectedCount"></span> salinan = <span x-text="'RM ' + (selectedCount * 10.00).toFixed(2)"></span>)</span>
                            <span x-show="!mohonSalinanPendua">Kadar statutori notis pertukaran milikan EPTR</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-slate-300 uppercase block font-semibold">Jumlah Fi Bayaran</span>
                        <span class="text-lg font-black text-amber-300 font-mono" x-text="'RM ' + totalFi.toFixed(2)"></span>
                    </div>
                </div>
            </div>

            <!-- 5. MUAT NAIK SALINAN RESIT PEMBAYARAN -->
            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200 space-y-3">
                <div class="flex items-center gap-2 text-slate-900 font-bold text-sm border-b border-slate-200 pb-2">
                    <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs">5</span>
                    <span>SALINAN RESIT PEMBAYARAN FI PINDAH MILIK</span>
                </div>

                <div class="space-y-2">
                    <label class="block font-bold text-slate-800 uppercase text-xs">
                        Muat Naik Resit Bayaran Fi Pindah Milik
                        @if(!Auth::user()->isStaff())
                            <span class="text-rose-500 font-black">* (Wajib Dimuat Naik)</span>
                        @else
                            <span class="text-slate-400 font-normal">(Pilihan bagi Staf)</span>
                        @endif
                    </label>

                    <div class="p-5 bg-white rounded-2xl border-2 border-dashed {{ $errors->has('resit_pembayaran') ? 'border-rose-400 bg-rose-50/20' : 'border-slate-300 hover:border-blue-500' }} transition text-center relative">
                        <input type="file" name="resit_pembayaran" id="resit_pembayaran_borang_b" @change="onFileSelected($event)" accept="image/jpeg,image/png,image/jpg,application/pdf" {{ !Auth::user()->isStaff() ? 'required' : '' }} class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        
                        <div class="space-y-2 pointer-events-none">
                            <div class="w-12 h-12 mx-auto rounded-full {{ $errors->has('resit_pembayaran') ? 'bg-rose-100 text-rose-700' : 'bg-blue-100 text-blue-700' }} flex items-center justify-center text-xl shadow-xs">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </div>
                            <div class="font-bold text-slate-800 text-sm">
                                <span x-show="!receiptFileName">Klik atau Seret Resit Pembayaran di Sini {{ !Auth::user()->isStaff() ? '(Wajib)' : '' }}</span>
                                <span x-show="receiptFileName" class="text-blue-700 font-mono" x-text="receiptFileName"></span>
                            </div>
                            <p class="text-[11px] text-slate-500" x-show="!receiptFileName">
                                Format yang disokong: <b>PDF, JPG, PNG</b> (Maksimum 2MB)
                            </p>
                            <p class="text-[11px] text-blue-600 font-semibold" x-show="receiptFileSize">
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
            </div>

            <!-- 6. PERAKUAN INTEGRITI -->
            <div class="bg-blue-50/60 rounded-2xl p-5 border border-blue-200">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="perakuan" value="1" {{ old('perakuan') ? 'checked' : '' }} required class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500 mt-0.5">
                    <div class="text-slate-800">
                        <span class="font-bold">Perakuan Kebenaran & Pemindahan Milik:</span>
                        <p class="text-[11px] text-slate-600 mt-0.5">
                            Saya mengaku dan memperakui bahawa permohonan pertukaran milikan ternakan ruminan ini adalah atas persetujuan bersama kedua-dua pihak yang sah. Segala maklumat yang diberikan adalah benar dan tertakluk kepada peruntukan <b>Enakmen Pendaftaran Ternakan Ruminan Negeri Kelantan 2024</b>.
                        </p>
                    </div>
                </label>
            </div>

            <!-- Submit Button Bar -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('eptr.borang-b.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-lg shadow-blue-600/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Hantar Borang B</span>
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
