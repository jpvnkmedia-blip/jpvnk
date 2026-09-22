@extends('layouts.app')

@section('title', 'EPTR Borang A - Pendaftaran Ternakan Ruminan')
@section('page_title', 'EPTR Borang A: Permohonan Pendaftaran Ternakan Ruminan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    kelantanData: {{ json_encode($kelantanData) }},
    bakaData: {{ json_encode($bakaData) }},
    indukList: {{ json_encode($indukList ?? []) }},
    selectedJajahan: '{{ old('jajahan', $user->jajahan ?? 'Kota Bharu') }}',
    selectedDaerah: '{{ old('daerah', $user->pemunya->daerah ?? '') }}',
    selectedPoskod: '{{ old('poskod', $user->pemunya->poskod ?? '') }}',
    selectedJenis: '{{ strtolower(old('jenis_ternakan', 'lembu')) }}',
    selectedBaka: '{{ old('baka', 'kedah-kelantan') }}',
    selectedBakaPejantan: '{{ old('baka_pejantan', '') }}',
    selectedBakaInduk: '{{ old('baka_induk', '') }}',
    selectedTagInduk: '{{ old('no_tanda_pengenalan_induk', '') }}',
    matchedInduk: null,
    selectedTarikhLahir: '{{ old('tarikh_lahir', '') }}',
    calculatedUmur: '{{ old('umur', '') }}',
    kategoriDaftar: '{{ old('kategori_daftar', 'biasa') }}',
    receiptFileName: '',
    receiptFileSize: '',
    
    get isTempohPemutihan() {
        let now = new Date();
        let start = new Date('2026-09-20T00:00:00');
        let end = new Date('2026-12-31T23:59:59');
        return now >= start && now <= end;
    },
    get isRuminanBesar() {
        let key = this.selectedJenis.toLowerCase();
        return key.includes('lembu') || key.includes('kerbau');
    },
    get fiDaftar() {
        return 2.00;
    },
    get fiTagging() {
        return this.isRuminanBesar ? 8.00 : 5.00;
    },
    get fiDaftarLewat() {
        if (this.isTempohPemutihan) {
            return 0.00;
        }
        return this.isRuminanBesar ? 12.00 : 10.00;
    },
    get totalFi() {
        return this.kategoriDaftar === 'lewat' ? (this.fiDaftar + this.fiTagging + this.fiDaftarLewat) : (this.fiDaftar + this.fiTagging);
    },
    get daerahOptions() {
        return (this.kelantanData[this.selectedJajahan] && this.kelantanData[this.selectedJajahan].daerah) ? this.kelantanData[this.selectedJajahan].daerah : [];
    },
    get poskodOptions() {
        return (this.kelantanData[this.selectedJajahan] && this.kelantanData[this.selectedJajahan].poskod) ? this.kelantanData[this.selectedJajahan].poskod : [];
    },
    get bakaOptions() {
        let key = this.selectedJenis.toLowerCase();
        if (key.includes('lembu')) key = 'lembu';
        else if (key.includes('kerbau')) key = 'kerbau';
        else if (key.includes('biri')) key = 'biri-biri';
        else if (key.includes('kambing')) key = 'kambing';
        return this.bakaData[key] || [];
    },
    onJajahanChange() {
        let daerahs = this.daerahOptions;
        this.selectedDaerah = daerahs.length > 0 ? daerahs[0] : '';
        let poskods = this.poskodOptions;
        this.selectedPoskod = poskods.length > 0 ? poskods[0] : '';
    },
    onJenisChange() {
        let bakas = this.bakaOptions;
        this.selectedBaka = bakas.length > 0 ? bakas[0] : '';
    },
    onTagIndukInput() {
        const tag = (this.selectedTagInduk || '').trim().toUpperCase();
        if (!tag || tag === 'TIADA') {
            this.matchedInduk = null;
            return;
        }
        const found = this.indukList.find(i => (i.no_tag || '').toUpperCase() === tag);
        if (found) {
            this.matchedInduk = found;
            if (found.baka) {
                this.selectedBakaInduk = found.baka;
            }
        } else {
            this.matchedInduk = null;
        }
    },
    onTarikhLahirChange() {
        if (!this.selectedTarikhLahir) {
            this.calculatedUmur = '';
            return;
        }

        const birthDate = new Date(this.selectedTarikhLahir);
        const today = new Date();
        if (isNaN(birthDate.getTime())) return;

        // Kira perbezaan hari
        const diffTime = today.getTime() - birthDate.getTime();
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

        if (diffDays < 0) {
            this.calculatedUmur = '0 Hari (Tarikh Hadapan Tidak Sah)';
            return;
        }

        let years = today.getFullYear() - birthDate.getFullYear();
        let months = today.getMonth() - birthDate.getMonth();
        let days = today.getDate() - birthDate.getDate();

        if (days < 0) {
            months--;
            const prevMonth = new Date(today.getFullYear(), today.getMonth(), 0);
            days += prevMonth.getDate();
        }
        if (months < 0) {
            years--;
            months += 12;
        }

        let ageStr = '';
        if (years > 0) {
            ageStr = years + ' Tahun' + (months > 0 ? (' ' + months + ' Bulan') : '');
        } else if (months > 0) {
            ageStr = months + ' Bulan' + (days > 0 ? (' ' + days + ' Hari') : '');
        } else {
            ageStr = days + ' Hari';
        }

        this.calculatedUmur = ageStr;

        // FI STATUTORI Auto-Select berdasarkan Tarikh Lahir:
        // Enakmen EPTR: Ternakan melebihi 14 hari (> 2 minggu) dikira Pendaftaran Lewat (Seksyen 7)
        if (diffDays > 14) {
            this.kategoriDaftar = 'lewat';
        } else {
            this.kategoriDaftar = 'biasa';
        }
    },
    onFileSelected(event) {
        const file = event.target.files[0];
        if (file) {
            this.receiptFileName = file.name;
            this.receiptFileSize = (file.size / 1024).toFixed(1) + ' KB';
        } else {
            this.receiptFileName = '';
            this.receiptFileSize = '';
        }
    }
}" x-init="if(!selectedDaerah && daerahOptions.length > 0) selectedDaerah = daerahOptions[0]; if(!selectedPoskod && poskodOptions.length > 0) selectedPoskod = poskodOptions[0]; if(selectedTarikhLahir) onTarikhLahirChange(); if(selectedTagInduk) onTagIndukInput();">

    <!-- Form Header Card -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex items-center justify-between border border-slate-700">
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs font-bold uppercase tracking-wider mb-2">
                <i class="fa-solid fa-file-signature"></i> Borang A Rasmi EPTR
            </span>
            <h2 class="text-2xl font-black">Borang Permohonan Pendaftaran Ternakan Ruminan</h2>
            <p class="text-xs text-slate-300 mt-1">Enakmen Pendaftaran Ternakan Ruminan 2024 (Negeri Kelantan)</p>
        </div>
        <div class="hidden sm:block text-right">
            <span class="text-xs font-mono bg-white/10 px-3.5 py-2 rounded-xl border border-white/20 text-emerald-300 font-bold">JADUAL PERTAMA [BORANG A]</span>
        </div>
    </div>

    <!-- Main Registration Form -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs">
        <form action="{{ route('eptr.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 text-xs">
            @csrf

            <!-- Section 1: Maklumat Pemunya -->
            <div>
                <div class="flex items-center gap-2 pb-3 mb-4 border-b border-slate-100 text-slate-800 font-bold text-sm">
                    <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs font-black">1</span>
                    <span>Bahagian A: Maklumat Pemunya Ternakan</span>
                </div>

                @if($user->isStaff())
                    <div class="mb-4">
                        <label class="block font-bold text-slate-700 uppercase mb-1">Pilih Pemunya Berdaftar (Atau Masukkan Pemunya Baru di Bawah)</label>
                        <select name="pemunya_id" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            <option value="">-- Cipta / Masukkan Pemunya Baru --</option>
                            @foreach($pemunyaList as $pem)
                                <option value="{{ $pem->id }}">{{ $pem->nama }} (No KP: {{ $pem->no_kp }} &bull; {{ $pem->jajahan }})</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Nama Penuh Pemunya <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_pemunya" value="{{ old('nama_pemunya', $user->pemunya->nama ?? $user->name) }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">No. Kad Pengenalan Pemunya <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_kp_pemunya" value="{{ old('no_kp_pemunya', $user->pemunya->no_kp ?? $user->ic_number) }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">No. Telefon <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_tel_pemunya" value="{{ old('no_tel_pemunya', $user->pemunya->no_telefon ?? $user->phone) }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                    
                    <!-- Dynamic Jajahan Dropdown -->
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Jajahan <span class="text-rose-500">*</span></label>
                        <select name="jajahan" x-model="selectedJajahan" @change="onJajahanChange()" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none font-bold">
                            @foreach($jajahanList as $j)
                                <option value="{{ $j }}">{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Dynamic Daerah Dropdown -->
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Daerah <span class="text-rose-500">*</span></label>
                        <select name="daerah" x-model="selectedDaerah" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none font-bold">
                            <template x-for="d in daerahOptions" :key="d">
                                <option :value="d" x-text="d" :selected="d === selectedDaerah"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                    <!-- Dynamic Poskod Dropdown / Selection -->
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Poskod</label>
                        <select name="poskod" x-model="selectedPoskod" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none font-mono">
                            <template x-for="p in poskodOptions" :key="p">
                                <option :value="p" x-text="p" :selected="p === selectedPoskod"></option>
                            </template>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block font-bold text-slate-700 uppercase mb-1">Alamat Penuh Pemunya <span class="text-rose-500">*</span></label>
                        <input type="text" name="alamat_pemunya" value="{{ old('alamat_pemunya', $user->pemunya->alamat ?? $user->address) }}" required placeholder="No. Rumah, Jalan, Kampung" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Section 2: Maklumat Ternakan Ruminan -->
            <div class="pt-4 border-t border-slate-100">
                <div class="flex items-center gap-2 pb-3 mb-4 border-b border-slate-100 text-slate-800 font-bold text-sm">
                    <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs font-black">2</span>
                    <span>Bahagian B: Maklumat Butiran Ternakan Ruminan</span>
                </div>

                <!-- Notis Auto Generate No Tag Telinga -->
                <div class="mb-4 p-3.5 bg-emerald-50/80 rounded-2xl border border-emerald-200 flex items-start gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-700 text-white flex items-center justify-center shrink-0 mt-0.5 shadow-xs">
                        <i class="fa-solid fa-tag text-xs"></i>
                    </div>
                    <div class="text-xs">
                        <div class="font-bold text-emerald-950">No. Tag Telinga Rasmi (Auto-Generated)</div>
                        <p class="text-emerald-800 text-[11px] mt-0.5">
                            No. Tag Telinga Rasmi <b>tidak perlu diisi oleh pemohon</b>. Sistem akan menjana nombor tag secara automatik berdasarkan <b>kod singkatan Daerah</b> dan <b>nombor turutan</b> (contoh: <span class="font-mono font-bold bg-white px-1.5 py-0.5 rounded border border-emerald-300 text-emerald-900" x-text="((kelantanData[selectedJajahan] && kelantanData[selectedJajahan].kod && kelantanData[selectedJajahan].kod[selectedDaerah]) ? kelantanData[selectedJajahan].kod[selectedDaerah] : 'PRG') + '-XXXX'"></span>) sebaik sahaja diluluskan oleh Admin Jajahan EPTR.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Species Selection -->
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Spesies / Jenis Ternakan <span class="text-rose-500">*</span></label>
                        <select name="jenis_ternakan" x-model="selectedJenis" @change="onJenisChange()" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none font-bold">
                            <option value="lembu">Lembu</option>
                            <option value="kerbau">Kerbau</option>
                            <option value="kambing">Kambing</option>
                            <option value="biri-biri">Biri-biri</option>
                        </select>
                    </div>

                    <!-- Dynamic Breed (Baka) Dropdown -->
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Baka Ternakan <span class="text-rose-500">*</span></label>
                        <select name="baka" x-model="selectedBaka" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none capitalize font-semibold">
                            <template x-for="b in bakaOptions" :key="b">
                                <option :value="b" x-text="b" :selected="b === selectedBaka"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Baka Pejantan (Bapa)</label>
                        <select name="baka_pejantan" x-model="selectedBakaPejantan" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none capitalize">
                            <option value="">-- Baka Pejantan --</option>
                            <template x-for="b in bakaOptions" :key="'p_'+b">
                                <option :value="b" x-text="b" :selected="b === selectedBakaPejantan"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1 flex items-center justify-between">
                            <span>Baka Induk (Ibu)</span>
                            <span x-show="matchedInduk" class="text-[10px] text-emerald-700 font-bold bg-emerald-100 px-1.5 py-0.2 rounded flex items-center gap-1">
                                <i class="fa-solid fa-wand-magic-sparkles text-[9px]"></i> Auto-Padan
                            </span>
                        </label>
                        <select name="baka_induk" x-model="selectedBakaInduk" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none capitalize">
                            <option value="">-- Baka Induk --</option>
                            <template x-for="b in bakaOptions" :key="'i_'+b">
                                <option :value="b" x-text="b" :selected="b === selectedBakaInduk"></option>
                            </template>
                            <template x-if="selectedBakaInduk && !bakaOptions.includes(selectedBakaInduk)">
                                <option :value="selectedBakaInduk" x-text="selectedBakaInduk" selected></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1 flex items-center justify-between">
                            <span>No. Tag Telinga Induk</span>
                            <span class="text-[10px] text-slate-400 font-normal">Taip / Pilih</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   name="no_tanda_pengenalan_induk" 
                                   id="no_tanda_pengenalan_induk"
                                   list="senarai_induk_datalist"
                                   x-model="selectedTagInduk" 
                                   @input="onTagIndukInput()" 
                                   @change="onTagIndukInput()"
                                   placeholder="Taip No. Tag atau pilih / TIADA" 
                                   autocomplete="off"
                                   class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none font-mono font-bold uppercase text-slate-800">
                            
                            <datalist id="senarai_induk_datalist">
                                <option value="TIADA">TIADA (Induk luar / tidak bertag)</option>
                                <template x-for="ind in indukList" :key="ind.id">
                                    <option :value="ind.no_tag" x-text="ind.no_tag + ' &bull; ' + (ind.baka ? ind.baka.toUpperCase() : '') + ' &bull; ' + ind.pemunya + ' (' + ind.jajahan + ')'"></option>
                                </template>
                            </datalist>
                        </div>
                    </div>
                </div>

                <!-- Live Auto-Matched Induk Status Card -->
                <div x-show="matchedInduk" x-transition class="mt-3 p-3.5 bg-emerald-50/90 border border-emerald-300 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-xs">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-700 text-white flex items-center justify-center shrink-0 shadow-xs">
                            <i class="fa-solid fa-cow text-sm"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-emerald-950 text-xs">Induk Ditemui &amp; Dipautkan:</span>
                                <span class="font-mono font-black text-xs px-2 py-0.5 bg-white rounded border border-emerald-300 text-emerald-900" x-text="matchedInduk ? matchedInduk.no_tag : ''"></span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-200/70 text-emerald-900 uppercase" x-text="matchedInduk ? matchedInduk.baka : ''"></span>
                            </div>
                            <div class="text-[11px] text-emerald-800 mt-0.5">
                                Pemunya: <b x-text="matchedInduk ? matchedInduk.pemunya : ''"></b>
                                <span x-show="matchedInduk && matchedInduk.pemunya_kp" class="font-mono text-emerald-700" x-text="matchedInduk ? (' (' + matchedInduk.pemunya_kp + ')') : ''"></span>
                                &bull; Jajahan: <span x-text="matchedInduk ? matchedInduk.jajahan : ''"></span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 self-end sm:self-auto">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-emerald-600 text-white text-[10px] font-bold">
                            <i class="fa-solid fa-check"></i> Rekod Berdaftar
                        </span>
                        <button type="button" @click="selectedTagInduk = 'TIADA'; onTagIndukInput();" class="text-[10px] text-slate-500 hover:text-rose-600 underline">
                            Batal Pautan
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Lahir <span class="text-rose-500">*</span></label>
                        <input type="date" 
                               name="tarikh_lahir" 
                               id="tarikh_lahir"
                               x-model="selectedTarikhLahir" 
                               @change="onTarikhLahirChange()" 
                               @input="onTarikhLahirChange()"
                               max="{{ date('Y-m-d') }}"
                               value="{{ old('tarikh_lahir') }}" 
                               required 
                               class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none font-semibold">
                        <p class="text-[10px] text-slate-400 mt-1">Wajib diisi &bull; Menentukan anggaran umur &amp; kadar fi.</p>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1 flex items-center justify-between">
                            <span>Anggaran Umur</span>
                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-1.5 py-0.2 rounded flex items-center gap-1">
                                <i class="fa-solid fa-wand-magic-sparkles text-[9px]"></i> Auto-Kira
                            </span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   name="umur" 
                                   id="umur"
                                   x-model="calculatedUmur" 
                                   readonly
                                   value="{{ old('umur') }}" 
                                   placeholder="Auto-dikira berdasarkan Tarikh Lahir" 
                                   class="w-full px-3.5 py-2.5 text-sm bg-slate-100 border border-slate-200 rounded-xl text-slate-800 font-bold focus:outline-none cursor-not-allowed">
                            <div class="absolute right-3 top-2.5 text-emerald-600">
                                <i class="fa-solid fa-calculator text-xs"></i>
                            </div>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">Dikira secara automatik dari tarikh lahir.</p>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Jantina <span class="text-rose-500">*</span></label>
                        <select name="jantina" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            <option value="Betina" {{ old('jantina') === 'Betina' ? 'selected' : '' }}>Betina (Induk/Dara)</option>
                            <option value="Jantan" {{ old('jantina') === 'Jantan' ? 'selected' : '' }}>Jantan (Pejantan/Pedaging)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Warna Dominan / Ciri Warna</label>
                        <input type="text" name="warna" value="{{ old('warna', 'Coklat') }}" placeholder="Contoh: Coklat gelap, Putih kelabu, Hitam tompok" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Tanda Badan / Ciri Khas Fizikal</label>
                        <input type="text" name="tanda_badan" value="{{ old('tanda_badan') }}" placeholder="Contoh: Tompok putih di dahi, tanduk lurus pendek" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Tujuan Ternakan</label>
                        <select name="tujuan_ternakan" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            <option value="Pembiakan">Pembiakan / Baka</option>
                            <option value="Pedaging">Pedaging / Penggemukan (Fidlot)</option>
                            <option value="Tenusu">Tenusu (Susu)</option>
                            <option value="Lain-lain">Lain-lain</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Lokasi Kandang / Padang Ragut</label>
                        <input type="text" name="lokasi_kandang" value="{{ old('lokasi_kandang') }}" placeholder="Contoh: Kandang Utama Kg Padang Kala" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Section 3: Lampiran Resit Pembayaran Pendaftaran EPTR (Wajib bagi Penternak) -->
            <div class="pt-4 border-t border-slate-100">
                <div class="flex items-center justify-between pb-3 mb-2 border-b border-slate-100">
                    <div class="flex items-center gap-2 text-slate-800 font-bold text-sm">
                        <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs font-black">3</span>
                        <span>Bahagian C: Lampiran Resit Pembayaran Pendaftaran</span>
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

                <!-- Pengiraan Fi Statutori EPTR Mengikut Enakmen -->
                <div class="mb-4 bg-slate-50 rounded-2xl p-4 border border-slate-200 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-lg bg-emerald-100 text-emerald-800 font-bold text-[11px] font-mono">FI STATUTORI</span>
                            <span class="font-bold text-slate-800 text-xs">Kadar Bayaran Pendaftaran EPTR (Borang A)</span>
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-500 bg-slate-200/80 px-2 py-0.5 rounded-md">
                                <i class="fa-solid fa-lock text-[9px] text-slate-400"></i> Auto-Kunci
                            </span>
                        </div>
                        <a href="{{ route('eptr.jadual-fi') }}" target="_blank" class="text-[11px] font-bold text-emerald-700 hover:underline flex items-center gap-1">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Jadual Fi Rasmi
                        </a>
                    </div>

                    <!-- Hidden input to pass the locked auto-selected value -->
                    <input type="hidden" name="kategori_daftar" :value="kategoriDaftar">

                    <!-- Pilihan Kategori Pendaftaran (Biasa vs Lewat) - Auto-Pilih & Dikunci Berdasarkan Tarikh Lahir -->
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="flex items-center p-2.5 rounded-xl border transition relative select-none cursor-not-allowed"
                             :class="kategoriDaftar === 'biasa' ? 'bg-emerald-50/70 border-emerald-500 ring-2 ring-emerald-500/20 shadow-xs' : 'bg-slate-100/70 border-slate-200 text-slate-400 opacity-60'">
                            <input type="radio" value="biasa" x-model="kategoriDaftar" disabled class="text-emerald-600 mr-2 cursor-not-allowed">
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900" :class="kategoriDaftar === 'biasa' ? 'text-slate-900' : 'text-slate-500'">Pendaftaran Pertama</span>
                                    <span x-show="kategoriDaftar === 'biasa' && selectedTarikhLahir" class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-emerald-200 text-emerald-800 flex items-center gap-1">
                                        <i class="fa-solid fa-lock text-[8px]"></i> Auto (&le; 14 Hari)
                                    </span>
                                </div>
                                <div class="text-[10px]" :class="kategoriDaftar === 'biasa' ? 'text-slate-600' : 'text-slate-400'">Seksyen 5 (RM2.00) + Seksyen 6 Penandaan</div>
                            </div>
                        </div>
                        <div class="flex items-center p-2.5 rounded-xl border transition relative select-none cursor-not-allowed"
                             :class="kategoriDaftar === 'lewat' ? 'bg-amber-50/70 border-amber-500 ring-2 ring-amber-500/20 shadow-xs' : 'bg-slate-100/70 border-slate-200 text-slate-400 opacity-60'">
                            <input type="radio" value="lewat" x-model="kategoriDaftar" disabled class="text-amber-600 mr-2 cursor-not-allowed">
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900" :class="kategoriDaftar === 'lewat' ? 'text-slate-900' : 'text-slate-500'">Pendaftaran Lewat</span>
                                    <span x-show="kategoriDaftar === 'lewat' && selectedTarikhLahir" class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-amber-200 text-amber-800 flex items-center gap-1">
                                        <i class="fa-solid fa-lock text-[8px]"></i> Auto (&gt; 14 Hari)
                                    </span>
                                </div>
                                <div class="text-[10px]" :class="kategoriDaftar === 'lewat' ? 'text-slate-600' : 'text-slate-400'">Seksyen 7 (Daftar &amp; Tagging + Denda Lewat)</div>
                            </div>
                        </div>
                    </div>

                    <!-- Banner Tempoh Pemutihan EPTR -->
                    <template x-if="isTempohPemutihan">
                        <div class="p-3 bg-emerald-50 border border-emerald-300 rounded-xl text-emerald-900 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-md bg-emerald-600 text-white font-black text-[9px] uppercase tracking-wider">Pemutihan EPTR</span>
                                <span class="text-xs font-bold text-emerald-950">Tempoh Pemutihan Berlangsung (20 Sept &ndash; 31 Dis 2026): Denda lewat pendaftaran dikecualikan 100% (RM 0.00)!</span>
                            </div>
                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-lg border border-emerald-300">Denda Lewat: RM 0.00</span>
                        </div>
                    </template>

                    <!-- Ringkasan Pengiraan Fi -->
                    <div class="p-3.5 bg-gradient-to-r from-slate-900 to-emerald-950 text-white rounded-xl shadow-xs flex items-center justify-between">
                        <div class="space-y-0.5">
                            <div class="text-[11px] text-slate-300">
                                Kategori: <strong class="text-emerald-300" x-text="isRuminanBesar ? 'Ruminan Besar (Lembu / Kerbau)' : 'Ruminan Kecil (Kambing / Bebiri)'"></strong>
                            </div>
                            <div class="text-[10px] text-slate-400">
                                <span x-show="kategoriDaftar === 'biasa'">Pendaftaran Pertama: Daftar (RM2.00) + Penandaan Tag (<span x-text="'RM ' + fiTagging.toFixed(2)"></span>)</span>
                                <span x-show="kategoriDaftar === 'lewat'">
                                    <template x-if="isTempohPemutihan">
                                        <span>Pendaftaran Lewat: Daftar &amp; Tag (<span x-text="'RM ' + (fiDaftar + fiTagging).toFixed(2)"></span>) + <span class="text-emerald-300 font-bold">Denda Lewat Pemutihan (RM 0.00)</span></span>
                                    </template>
                                    <template x-if="!isTempohPemutihan">
                                        <span>Pendaftaran Lewat: Daftar &amp; Tag (<span x-text="'RM ' + (fiDaftar + fiTagging).toFixed(2)"></span>) + Denda Lewat Seksyen 7 (<span x-text="'RM ' + fiDaftarLewat.toFixed(2)"></span>)</span>
                                    </template>
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] text-slate-300 uppercase block font-semibold">Jumlah Fi Resit</span>
                            <span class="text-lg font-black text-amber-300 font-mono" x-text="'RM ' + totalFi.toFixed(2)"></span>
                        </div>
                    </div>
                </div>

                <div class="p-5 bg-slate-50 rounded-2xl border-2 border-dashed {{ $errors->has('resit_pembayaran') ? 'border-rose-400 bg-rose-50/20' : 'border-slate-300 hover:border-emerald-500' }} transition text-center relative">
                    <input type="file" name="resit_pembayaran" id="resit_pembayaran" @change="onFileSelected($event)" accept="image/jpeg,image/png,image/jpg,application/pdf" {{ !Auth::user()->isStaff() ? 'required' : '' }} class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    
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

            <!-- Section 4: Kolum PROGRAM (Pilihan - Pawah & Bantuan untuk Pegawai JPVNK) -->
            @if(Auth::user()->isStaff())
                <div class="pt-4 border-t border-slate-100 bg-slate-50/80 p-5 rounded-2xl border border-slate-200">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2 text-slate-900 font-bold text-sm">
                            <i class="fa-solid fa-handshake text-emerald-700"></i>
                            <span>Bahagian D: Kolum Program Bantuan (Pilihan)</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                            Akses Pegawai JPVNK
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-600 mb-3">
                        Pilih nama skim bantuan kerajaan sekiranya ternakan ini didaftarkan di bawah <b>Program Pawah Ternakan Negeri / DUN</b> atau program bantuan baka untuk pemantauan dan penyelesaian.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">Pilih / Namakan Program Bantuan</label>
                            <select name="program" class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none font-medium text-slate-800">
                                @foreach($programList as $prog)
                                    <option value="{{ $prog }}" {{ old('program') == $prog ? 'selected' : '' }}>{{ $prog }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">Catatan Tambahan Program</label>
                            <input type="text" name="catatan" value="{{ old('catatan') }}" placeholder="Contoh: Pawah fasa 1 peruntukan DUN" class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        </div>
                    </div>
                </div>
            @endif

            <!-- Submit Buttons -->
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('eptr.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-7 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-black text-sm rounded-xl shadow-lg shadow-emerald-700/20 transition flex items-center gap-2">
                    <i class="fa-solid fa-check"></i>
                    <span>Daftar Ternakan EPTR & Muat Naik Resit</span>
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
