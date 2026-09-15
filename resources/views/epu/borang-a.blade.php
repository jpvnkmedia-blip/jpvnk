@extends('layouts.app')

@section('title', 'Permohonan Lesen Penternakan Unggas (EPU Borang A)')
@section('page_title', 'Permohonan Lesen Penternakan Unggas (e-Unggas)')

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    #map { height: 320px; width: 100%; border-radius: 1rem; z-index: 10; }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="{
    currentStep: 1,
    
    // Step 1: Maklumat Pemohon
    namaPemohon: '{{ old('nama_pemohon_atau_syarikat', $user->nama_syarikat ?: $user->name) }}',
    noKp: '{{ old('no_syarikat_atau_ssm', $user->ic_number) }}',
    alamatPemohon1: '{{ old('alamat_pemohon_1', $user->address) }}',
    alamatPemohon2: '{{ old('alamat_pemohon_2', '') }}',
    poskodPemohon: '{{ old('poskod_pemohon', $user->poskod ?: '15000') }}',
    negeriPemohon: '{{ old('negeri_pemohon', $user->negeri ?: 'Kelantan') }}',
    emailPemohon: '{{ old('email_pemohon', $user->email) }}',
    phonePemohon: '{{ old('phone_pemohon', $user->phone) }}',
    faxPemohon: '{{ old('fax_pemohon', $user->fax) }}',

    // Step 2: Maklumat Penternakan / Ladang
    jenisUnggas: '{{ old('jenis_unggas', 'Ayam') }}',
    jurusanAktiviti: '{{ old('jurusan_aktiviti', 'Pedaging') }}',
    namaLadang: '{{ old('nama_ladang', 'Ladang Unggas Berkat') }}',
    idPremis: '{{ old('id_premis', 'PRM-' . strtoupper(substr($user->jajahan ?: 'KB', 0, 2)) . '-' . rand(100, 999)) }}',
    latitude: {{ old('latitude', '6.1254') }},
    longitude: {{ old('longitude', '102.2381') }},
    alamatLadang1: '{{ old('alamat_ladang', $user->address) }}',
    alamatLadang2: '',
    poskodLadang: '{{ old('poskod', $user->poskod ?: '15000') }}',
    negeriLadang: '{{ old('negeri', 'Kelantan') }}',
    jajahanLadang: '{{ old('jajahan', $user->jajahan ?: 'Kota Bharu') }}',
    daerahLadang: '{{ old('daerah', 'Kemumin') }}',
    luasKawasan: '{{ old('luas_kawasan_sqft', '10000') }}',
    bilanganSemasa: {{ old('bilangan_semasa_unggas', 2000) }},

    // Reban Ayam
    rebanAyam: [
        { id: 1, lebar: 40, panjang: 200, tingkat: 1 }
    ],
    // Reban Itik / Puyuh
    rebanLain: [
        { id: 1, lebar: 30, panjang: 100 }
    ],

    tambahRebanAyam() {
        this.rebanAyam.push({
            id: Date.now(),
            lebar: 40,
            panjang: 200,
            tingkat: 1
        });
    },
    padamRebanAyam(index) {
        if (this.rebanAyam.length > 1) {
            this.rebanAyam.splice(index, 1);
        }
    },

    tambahRebanLain() {
        this.rebanLain.push({
            id: Date.now(),
            lebar: 30,
            panjang: 100
        });
    },
    padamRebanLain(index) {
        if (this.rebanLain.length > 1) {
            this.rebanLain.splice(index, 1);
        }
    },

    // Dynamic Calculations
    get totalKeluasanAyam() {
        return this.rebanAyam.reduce((acc, r) => acc + (parseFloat(r.lebar || 0) * parseFloat(r.panjang || 0) * parseFloat(r.tingkat || 1)), 0);
    },
    get totalKeluasanLain() {
        return this.rebanLain.reduce((acc, r) => acc + (parseFloat(r.lebar || 0) * parseFloat(r.panjang || 0)), 0);
    },
    get computedKapasiti() {
        if (this.jenisUnggas === 'Ayam') {
            // Format: lebar (ft) x panjang (ft) x tingkat / 1
            return Math.floor(this.totalKeluasanAyam / 1);
        } else if (this.jenisUnggas === 'Itik') {
            // Format: lebar (ft) x panjang (ft) / 6
            return Math.floor(this.totalKeluasanLain / 6);
        } else if (this.jenisUnggas === 'Puyuh' || this.jenisUnggas === 'Merpati') {
            // Format: lebar (ft) x panjang (ft) / 0.2
            return Math.floor(this.totalKeluasanLain / 0.2);
        } else {
            return Math.floor(this.totalKeluasanLain / 1);
        }
    },
    get formulaLabel() {
        if (this.jenisUnggas === 'Ayam') {
            return 'Format: Lebar (ft) × Panjang (ft) × Tingkat ÷ 1 (≤500 ekor Percuma)';
        } else if (this.jenisUnggas === 'Itik') {
            return 'Format: Lebar (ft) × Panjang (ft) ÷ 6 (≤500 ekor Percuma)';
        } else if (this.jenisUnggas === 'Puyuh') {
            return 'Format: Lebar (ft) × Panjang (ft) ÷ 0.2 (≤1,000 ekor Percuma)';
        } else if (this.jenisUnggas === 'Merpati') {
            return 'Format: Lebar (ft) × Panjang (ft) ÷ 0.2 (≤1,000 ekor Percuma)';
        }
        return '';
    },
    get freeTierLabel() {
        if (this.jenisUnggas === 'Puyuh' || this.jenisUnggas === 'Merpati') {
            return 'Kadar Fi Lesen Statutori (≤1,000 Ekor Percuma)';
        }
        return 'Kadar Fi Lesen Statutori (≤500 Ekor Percuma)';
    },
    get computedFi() {
        const cap = this.computedKapasiti;
        if (cap <= 0) return 0.00;
        // Ayam & Itik: <= 500 percuma
        if ((this.jenisUnggas === 'Ayam' || this.jenisUnggas === 'Itik') && cap <= 500) {
            return 0.00;
        }
        // Puyuh & Merpati: <= 1000 percuma
        if ((this.jenisUnggas === 'Puyuh' || this.jenisUnggas === 'Merpati') && cap <= 1000) {
            return 0.00;
        }
        if (cap <= 5000) return 100.00;
        if (cap <= 20000) return 200.00;
        return 500.00;
    },

    // Pengecualian Lesen
    mohonPengecualian: 'Tidak',
    sebabPengecualian: 'Penternakan Tradisional / Skala Kecil Isi Rumah',
    sebabLain: '',

    // Alamat Premis Perniagaan (jika ada)
    adaPremisPerniagaan: false,
    alamatPerniagaan: '',
    poskodPerniagaan: '',
    negeriPerniagaan: 'Kelantan',

    // Declaration
    perakuan: false,

    get rebanDataJson() {
        return JSON.stringify({
            jenis: this.jenisUnggas,
            reban_ayam: this.rebanAyam,
            reban_lain: this.rebanLain,
            total_keluasan: this.jenisUnggas === 'Ayam' ? this.totalKeluasanAyam : this.totalKeluasanLain,
            kapasiti: this.computedKapasiti,
            fi: this.computedFi
        });
    }
}" x-init="
    $nextTick(() => {
        if (typeof L !== 'undefined') {
            var map = L.map('map').setView([latitude, longitude], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            var marker = L.marker([latitude, longitude], { draggable: true }).addTo(map);

            marker.on('dragend', function(e) {
                var pos = e.target.getLatLng();
                latitude = pos.lat.toFixed(6);
                longitude = pos.lng.toFixed(6);
            });

            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                latitude = e.latlng.lat.toFixed(6);
                longitude = e.latlng.lng.toFixed(6);
            });

            window.epuMap = map;
            window.epuMarker = marker;
        }
    });
">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-amber-900 via-amber-800 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 border border-amber-500/40 text-amber-300 text-xs font-bold uppercase mb-2">
                <i class="fa-solid fa-feather-pointed"></i> Sistem Pengurusan Lesen Penternakan Unggas (e-Unggas)
            </div>
            <h2 class="text-2xl sm:text-3xl font-black">Permohonan Lesen Penternakan Unggas</h2>
            <p class="text-xs sm:text-sm text-amber-100/80 mt-1">Borang Rasmi (Borang A - Seksyen 4) Enakmen Perladangan Unggas 2005</p>
        </div>
        <div class="text-left sm:text-right">
            <span class="text-xs font-mono bg-white/10 px-3 py-1.5 rounded-xl border border-white/20 inline-block">
                NEGERI KELANTAN
            </span>
        </div>
    </div>

    <!-- Multi-Step Wizard Indicator -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
            <!-- Step 1 Tab -->
            <button type="button" @click="currentStep = 1" :class="currentStep === 1 ? 'bg-amber-600 text-white font-bold' : (currentStep > 1 ? 'bg-amber-50 text-amber-900 font-semibold hover:bg-amber-100' : 'bg-slate-50 text-slate-500 hover:bg-slate-100')" class="p-3 rounded-xl text-left text-xs transition flex items-center gap-2.5">
                <span class="w-6 h-6 rounded-full flex items-center justify-center font-bold text-[11px]" :class="currentStep === 1 ? 'bg-white text-amber-700' : (currentStep > 1 ? 'bg-amber-600 text-white' : 'bg-slate-200 text-slate-700')">
                    <i x-show="currentStep > 1" class="fa-solid fa-check"></i>
                    <span x-show="currentStep <= 1">1</span>
                </span>
                <div class="truncate">
                    <span class="block text-[10px] opacity-75 uppercase">Langkah 1</span>
                    <span class="font-bold">Maklumat Pemohon</span>
                </div>
            </button>

            <!-- Step 2 Tab -->
            <button type="button" @click="currentStep = 2; $nextTick(() => { if (window.epuMap) window.epuMap.invalidateSize(); })" :class="currentStep === 2 ? 'bg-amber-600 text-white font-bold' : (currentStep > 2 ? 'bg-amber-50 text-amber-900 font-semibold hover:bg-amber-100' : 'bg-slate-50 text-slate-500 hover:bg-slate-100')" class="p-3 rounded-xl text-left text-xs transition flex items-center gap-2.5">
                <span class="w-6 h-6 rounded-full flex items-center justify-center font-bold text-[11px]" :class="currentStep === 2 ? 'bg-white text-amber-700' : (currentStep > 2 ? 'bg-amber-600 text-white' : 'bg-slate-200 text-slate-700')">
                    <i x-show="currentStep > 2" class="fa-solid fa-check"></i>
                    <span x-show="currentStep <= 2">2</span>
                </span>
                <div class="truncate">
                    <span class="block text-[10px] opacity-75 uppercase">Langkah 2</span>
                    <span class="font-bold">Maklumat Penternakan / Ladang</span>
                </div>
            </button>

            <!-- Step 3 Tab -->
            <button type="button" @click="currentStep = 3" :class="currentStep === 3 ? 'bg-amber-600 text-white font-bold' : (currentStep > 3 ? 'bg-amber-50 text-amber-900 font-semibold hover:bg-amber-100' : 'bg-slate-50 text-slate-500 hover:bg-slate-100')" class="p-3 rounded-xl text-left text-xs transition flex items-center gap-2.5">
                <span class="w-6 h-6 rounded-full flex items-center justify-center font-bold text-[11px]" :class="currentStep === 3 ? 'bg-white text-amber-700' : (currentStep > 3 ? 'bg-amber-600 text-white' : 'bg-slate-200 text-slate-700')">
                    <i x-show="currentStep > 3" class="fa-solid fa-check"></i>
                    <span x-show="currentStep <= 3">3</span>
                </span>
                <div class="truncate">
                    <span class="block text-[10px] opacity-75 uppercase">Langkah 3</span>
                    <span class="font-bold">Lampiran</span>
                </div>
            </button>

            <!-- Step 4 Tab -->
            <button type="button" @click="currentStep = 4" :class="currentStep === 4 ? 'bg-amber-600 text-white font-bold' : 'bg-slate-50 text-slate-500 hover:bg-slate-100'" class="p-3 rounded-xl text-left text-xs transition flex items-center gap-2.5">
                <span class="w-6 h-6 rounded-full flex items-center justify-center font-bold text-[11px]" :class="currentStep === 4 ? 'bg-white text-amber-700' : 'bg-slate-200 text-slate-700'">
                    4
                </span>
                <div class="truncate">
                    <span class="block text-[10px] opacity-75 uppercase">Langkah 4</span>
                    <span class="font-bold">Semakan / Pengesahan</span>
                </div>
            </button>
        </div>
    </div>

    <!-- Main Form Form Container -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs">
        <form action="{{ route('epu.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 text-xs">
            @csrf

            <!-- Hidden JSON data for coops and calculated values -->
            <input type="hidden" name="reban_data" :value="rebanDataJson">
            <input type="hidden" name="kapasiti_maksimum_unggas" :value="computedKapasiti">
            <input type="hidden" name="yuran_lesen" :value="computedFi">
            <input type="hidden" name="latitude" :value="latitude">
            <input type="hidden" name="longitude" :value="longitude">
            <input type="hidden" name="mohon_pengecualian" :value="mohonPengecualian === 'Ya' ? 1 : 0">

            @if($errors->any())
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium">
                    <div class="font-bold mb-1 flex items-center gap-1.5"><i class="fa-solid fa-triangle-exclamation"></i> Sila semak ralat berikut:</div>
                    <ul class="list-disc pl-5 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- ========================================== -->
            <!-- STEP 1: MAKLUMAT PEMOHON (Image 2)         -->
            <!-- ========================================== -->
            <div x-show="currentStep === 1" class="space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-extrabold text-slate-800 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center text-xs">1</span>
                        <span>Maklumat Pemohon</span>
                    </h3>
                    <span class="text-xs text-slate-400">Langkah 1 daripada 4</span>
                </div>

                @if($user->isStaff())
                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl">
                        <label class="block font-bold text-amber-950 uppercase mb-1">Daftar Bagi Pihak Pengguna / Usahawan</label>
                        <select name="user_id" class="w-full px-3.5 py-2.5 text-sm bg-white border border-amber-300 rounded-xl focus:ring-2 focus:ring-amber-500">
                            @foreach($usahawanList as $u)
                                <option value="{{ $u->id }}" {{ $u->id == $user->id ? 'selected' : '' }}>
                                    {{ $u->name }} (No. KP: {{ $u->ic_number }} &bull; {{ $u->jajahan }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Nama -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">
                        Nama <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nama_pemohon_atau_syarikat" x-model="namaPemohon" required placeholder="Nama penuh pemohon atau syarikat" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>

                <!-- Kad Pengenalan -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">
                        Kad Pengenalan / No. Syarikat <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="no_syarikat_atau_ssm" x-model="noKp" required placeholder="No. Kad Pengenalan atau No. Pendaftaran SSM" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none font-mono">
                </div>

                <!-- Alamat Surat Menyurat -->
                <div class="space-y-2">
                    <label class="block font-bold text-slate-700 uppercase">
                        Alamat Surat Menyurat <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="alamat_pemohon_1" x-model="alamatPemohon1" required placeholder="Baris Alamat 1 (No. Rumah, Jalan, Kampung)" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    <input type="text" name="alamat_pemohon_2" x-model="alamatPemohon2" placeholder="Baris Alamat 2 (Taman / Mukim / Poskod)" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>

                <!-- Poskod & Negeri -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">
                            Poskod <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="poskod_pemohon" x-model="poskodPemohon" required placeholder="Contoh: 15000" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">
                            Negeri <span class="text-rose-500">*</span>
                        </label>
                        <select name="negeri_pemohon" x-model="negeriPemohon" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            <option value="Kelantan">Kelantan</option>
                            <option value="Terengganu">Terengganu</option>
                            <option value="Pahang">Pahang</option>
                            <option value="Perak">Perak</option>
                            <option value="Kedah">Kedah</option>
                            <option value="Pulau Pinang">Pulau Pinang</option>
                            <option value="Perlis">Perlis</option>
                            <option value="Selangor">Selangor</option>
                            <option value="Negeri Sembilan">Negeri Sembilan</option>
                            <option value="Melaka">Melaka</option>
                            <option value="Johor">Johor</option>
                            <option value="Sabah">Sabah</option>
                            <option value="Sarawak">Sarawak</option>
                            <option value="W.P. Kuala Lumpur">W.P. Kuala Lumpur</option>
                            <option value="W.P. Putrajaya">W.P. Putrajaya</option>
                            <option value="W.P. Labuan">W.P. Labuan</option>
                        </select>
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">
                        Email <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" name="email_pemohon" x-model="emailPemohon" required placeholder="nama@email.com" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>

                <!-- No Telefon & No Telefon (Fax) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">
                            No Telefon <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="phone_pemohon" x-model="phonePemohon" required placeholder="Contoh: 019-9887766" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">
                            No Telefon (Fax)
                        </label>
                        <input type="text" name="fax_pemohon" x-model="faxPemohon" placeholder="Contoh: 09-7441234" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none font-mono">
                    </div>
                </div>

                <!-- Navigation Step 1 -->
                <div class="pt-4 flex justify-end">
                    <button type="button" @click="currentStep = 2; $nextTick(() => { if (window.epuMap) window.epuMap.invalidateSize(); })" class="px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-amber-600/30 transition flex items-center gap-2">
                        <span>Seterusnya</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- ======================================================= -->
            <!-- STEP 2: MAKLUMAT PENTERNAKAN / LADANG (Image 3)         -->
            <!-- ======================================================= -->
            <div x-show="currentStep === 2" class="space-y-6">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-extrabold text-slate-800 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center text-xs">2</span>
                        <span>Maklumat Penternakan / Ladang</span>
                    </h3>
                    <span class="text-xs text-slate-400">Langkah 2 daripada 4</span>
                </div>

                <!-- Butir-butir Penternakan -->
                <div class="bg-amber-50/50 p-5 rounded-2xl border border-amber-200/60 space-y-4">
                    <h4 class="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-layer-group text-amber-600"></i>
                        <span>Butir-butir Penternakan</span>
                    </h4>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Jenis Unggas -->
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">
                                Jenis Unggas <span class="text-rose-500">*</span>
                            </label>
                            <select name="jenis_unggas" x-model="jenisUnggas" required class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 font-bold">
                                @foreach($jenisUnggasList as $u)
                                    <option value="{{ $u }}">{{ $u }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Jurusan dan Aktiviti -->
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">
                                Jurusan dan Aktiviti <span class="text-rose-500">*</span>
                            </label>
                            <select name="jurusan_aktiviti" x-model="jurusanAktiviti" required class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 font-bold">
                                @foreach($jurusanAktivitiList as $a)
                                    <option value="{{ $a }}">{{ $a }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Butir-butir Ladang / Loji & OpenStreetMap Leaflet -->
                <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200 space-y-4">
                    <h4 class="font-extrabold text-slate-800 text-sm flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-map-location-dot text-amber-600"></i>
                            <span>Butir-butir Ladang / Loji</span>
                        </span>
                        <span class="text-[11px] text-slate-500 font-normal">Klik pada peta atau seret penanda pin untuk menetapkan koordinat lokasi</span>
                    </h4>

                    <!-- Peta Interaktif Leaflet -->
                    <div wire:ignore class="rounded-2xl overflow-hidden border border-slate-300 shadow-inner">
                        <div id="map"></div>
                    </div>

                    <!-- Koordinat Lokasi -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">
                                Latitude <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" x-model="latitude" readonly class="w-full px-3.5 py-2 text-sm bg-white border border-slate-200 rounded-xl font-mono text-slate-700 font-bold">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">
                                Longitude <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" x-model="longitude" readonly class="w-full px-3.5 py-2 text-sm bg-white border border-slate-200 rounded-xl font-mono text-slate-700 font-bold">
                        </div>
                    </div>

                    <!-- Alamat Premis / Ladang -->
                    <div class="space-y-2">
                        <label class="block font-bold text-slate-700 uppercase">
                            Alamat Premis / Ladang <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="alamat_ladang" x-model="alamatLadang1" required placeholder="Baris Alamat 1 Lokasi Tapak Ladang" class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500">
                        <input type="text" x-model="alamatLadang2" placeholder="Baris Alamat 2 Lokasi Tapak Ladang (Pilihan)" class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500">
                    </div>

                    <!-- Poskod & Negeri -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">
                                Poskod <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="poskod" x-model="poskodLadang" required placeholder="Contoh: 15000" class="w-full px-3.5 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 font-mono">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">
                                Negeri <span class="text-rose-500">*</span>
                            </label>
                            <select name="negeri" x-model="negeriLadang" required class="w-full px-3.5 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500">
                                <option value="Kelantan">Kelantan</option>
                                <option value="Terengganu">Terengganu</option>
                                <option value="Pahang">Pahang</option>
                            </select>
                        </div>
                    </div>

                    <!-- Jajahan, Daerah, ID Premis, Luas Kawasan -->
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">
                                Jajahan <span class="text-rose-500">*</span>
                            </label>
                            <select name="jajahan" x-model="jajahanLadang" required class="w-full px-3.5 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500">
                                @foreach($jajahanList as $j)
                                    <option value="{{ $j }}">{{ $j }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">
                                Daerah (Mukim) <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="daerah" x-model="daerahLadang" required placeholder="Contoh: Kemumin" class="w-full px-3.5 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">
                                ID Premis <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="id_premis" x-model="idPremis" required placeholder="Contoh: PRM-KB-001" class="w-full px-3.5 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 font-mono">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">
                                Luas Kawasan (ft²) <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" name="luas_kawasan_sqft" x-model="luasKawasan" required placeholder="Contoh: 10000" class="w-full px-3.5 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 font-mono">
                        </div>
                    </div>
                </div>

                <!-- Keluasan Ladang & Pengiraan Reban Dinamik (Image 3) -->
                <div class="bg-amber-50/40 p-5 rounded-2xl border border-amber-200/80 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <h4 class="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                                <i class="fa-solid fa-calculator text-amber-600"></i>
                                <span>Keluasan Ladang (ft²) & Pengiraan Reban</span>
                            </h4>
                            <span class="text-[11px] text-slate-500 font-medium block mt-0.5" x-text="formulaLabel"></span>
                        </div>
                        <span class="text-xs text-amber-900 font-bold bg-amber-100 px-3 py-1 rounded-lg border border-amber-200 self-start sm:self-auto" x-text="'Ternakan: ' + jenisUnggas"></span>
                    </div>

                    <!-- BAHAGIAN A: PENGIRAAN REBAN UNTUK UNGGAS AYAM -->
                    <div x-show="jenisUnggas === 'Ayam'" class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-700 text-xs">Pengiraan reban untuk unggas ayam:</span>
                            <button type="button" @click="tambahRebanAyam()" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-lg text-xs transition flex items-center gap-1.5 shadow-xs">
                                <i class="fa-solid fa-plus"></i>
                                <span>Tambah Reban</span>
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border border-slate-200 rounded-xl overflow-hidden bg-white text-xs">
                                <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200">
                                    <tr>
                                        <th class="p-2.5">Reban Ayam</th>
                                        <th class="p-2.5">Lebar (ft)</th>
                                        <th class="p-2.5">Panjang (ft)</th>
                                        <th class="p-2.5">Tingkat</th>
                                        <th class="p-2.5">Keluasan (ft²)</th>
                                        <th class="p-2.5 text-center">Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <template x-for="(r, index) in rebanAyam" :key="r.id">
                                        <tr class="hover:bg-amber-50/30">
                                            <td class="p-2.5 font-bold text-slate-800" x-text="'Reban Ayam ' + (index + 1)"></td>
                                            <td class="p-2">
                                                <input type="number" x-model="r.lebar" min="1" class="w-24 px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs font-mono">
                                            </td>
                                            <td class="p-2">
                                                <input type="number" x-model="r.panjang" min="1" class="w-24 px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs font-mono">
                                            </td>
                                            <td class="p-2">
                                                <input type="number" x-model="r.tingkat" min="1" max="5" class="w-16 px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs font-mono">
                                            </td>
                                            <td class="p-2.5 font-mono font-bold text-slate-700" x-text="((r.lebar || 0) * (r.panjang || 0) * (r.tingkat || 1)).toLocaleString() + ' ft²'"></td>
                                            <td class="p-2.5 text-center">
                                                <button type="button" @click="padamRebanAyam(index)" class="text-rose-600 hover:text-rose-800 p-1 font-bold" title="Padam Reban" :disabled="rebanAyam.length === 1">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- BAHAGIAN B: PENGIRAAN REBAN UNTUK UNGGAS ITIK DAN PUYUH -->
                    <div x-show="jenisUnggas !== 'Ayam'" class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-700 text-xs" x-text="'Pengiraan reban untuk unggas ' + jenisUnggas.toLowerCase() + ':'"></span>
                            <button type="button" @click="tambahRebanLain()" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-lg text-xs transition flex items-center gap-1.5 shadow-xs">
                                <i class="fa-solid fa-plus"></i>
                                <span>Tambah Reban</span>
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border border-slate-200 rounded-xl overflow-hidden bg-white text-xs">
                                <thead class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200">
                                    <tr>
                                        <th class="p-2.5" x-text="'Reban ' + jenisUnggas"></th>
                                        <th class="p-2.5">Lebar (ft)</th>
                                        <th class="p-2.5">Panjang (ft)</th>
                                        <th class="p-2.5">Keluasan (ft²)</th>
                                        <th class="p-2.5 text-center">Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <template x-for="(r, index) in rebanLain" :key="r.id">
                                        <tr class="hover:bg-amber-50/30">
                                            <td class="p-2.5 font-bold text-slate-800" x-text="'Reban ' + (index + 1)"></td>
                                            <td class="p-2">
                                                <input type="number" x-model="r.lebar" min="1" class="w-24 px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs font-mono">
                                            </td>
                                            <td class="p-2">
                                                <input type="number" x-model="r.panjang" min="1" class="w-24 px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs font-mono">
                                            </td>
                                            <td class="p-2.5 font-mono font-bold text-slate-700" x-text="((r.lebar || 0) * (r.panjang || 0)).toLocaleString() + ' ft²'"></td>
                                            <td class="p-2.5 text-center">
                                                <button type="button" @click="padamRebanLain(index)" class="text-rose-600 hover:text-rose-800 p-1 font-bold" title="Padam Reban" :disabled="rebanLain.length === 1">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Dynamic Summary Badge Box (Image 3) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-3 border-t border-amber-200/80">
                        <div class="p-4 bg-white rounded-2xl border border-amber-300 shadow-xs flex items-center justify-between">
                            <div>
                                <span class="block text-[11px] font-bold text-slate-500 uppercase">Kapasiti Ladang:</span>
                                <span class="text-xs text-slate-400 font-normal">Kapasiti maksimum reban</span>
                            </div>
                            <div class="text-right">
                                <span class="text-xl font-black text-amber-900 font-mono" x-text="computedKapasiti.toLocaleString() + ' ekor'">0 ekor</span>
                            </div>
                        </div>

                        <div class="p-4 bg-white rounded-2xl border border-emerald-300 shadow-xs flex items-center justify-between">
                            <div>
                                <span class="block text-[11px] font-bold text-slate-500 uppercase">Jumlah fi yang perlu dibayar:</span>
                                <span class="text-xs text-slate-400 font-normal" x-text="freeTierLabel">Kadar Fi Lesen Statutori</span>
                            </div>
                            <div class="text-right">
                                <span class="text-xl font-black text-emerald-700 font-mono" x-text="computedFi === 0 ? 'PERCUMA (RM 0.00)' : 'RM ' + computedFi.toFixed(2)">RM 0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aktiviti Berkaitan -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200">
                    <label class="block font-bold text-slate-700 uppercase mb-1">
                        Bilangan Ternakan Semasa (Ekor) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="bilangan_semasa_unggas" x-model="bilanganSemasa" required min="0" placeholder="Bilangan ternakan sedia ada di ladang" class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 font-mono">
                </div>

                <!-- Alamat Premis Perniagaan (jika ada) -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="font-bold text-slate-800 text-xs">Alamat Premis Perniagaan (jika ada)</label>
                        <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-600 font-medium">
                            <input type="checkbox" x-model="adaPremisPerniagaan" class="rounded text-amber-600 focus:ring-amber-500">
                            <span>Ada premis perniagaan berasingan</span>
                        </label>
                    </div>

                    <div x-show="adaPremisPerniagaan" class="space-y-3 pt-2">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">Alamat</label>
                            <input type="text" name="alamat_premis_perniagaan" x-model="alamatPerniagaan" placeholder="Alamat penuh pejabat / premis perniagaan" class="w-full px-3.5 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-slate-700 uppercase mb-1">Poskod</label>
                                <input type="text" name="poskod_premis_perniagaan" x-model="poskodPerniagaan" placeholder="Poskod" class="w-full px-3.5 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 font-mono">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 uppercase mb-1">Negeri</label>
                                <select name="negeri_premis_perniagaan" x-model="negeriPerniagaan" class="w-full px-3.5 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500">
                                    <option value="Kelantan">Kelantan</option>
                                    <option value="Terengganu">Terengganu</option>
                                    <option value="Pahang">Pahang</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pengecualian Lesen (Image 3) -->
                <div class="p-5 bg-rose-50/40 rounded-2xl border border-rose-200 space-y-4">
                    <h4 class="font-extrabold text-slate-800 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-file-shield text-rose-600"></i>
                        <span>Pengecualian Lesen</span>
                    </h4>

                    <!-- Pink Warning Box (Image 3) -->
                    <div class="p-3 bg-rose-100/70 border border-rose-200 rounded-xl flex items-center gap-2 text-rose-900 text-xs font-semibold">
                        <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                        <span>Permohonan pengecualian lesen tertakluk kepada syarat statutori Enakmen Perladangan Unggas 2005.</span>
                    </div>

                    <!-- Radio Ya / Tidak -->
                    <div>
                        <label class="block font-bold text-slate-700 mb-2">
                            Adakah anda ingin membuat permohonan pengecualian lesen?
                        </label>
                        <div class="flex items-center gap-6">
                            <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-800">
                                <input type="radio" name="radio_pengecualian" value="Ya" x-model="mohonPengecualian" class="text-amber-600 focus:ring-amber-500">
                                <span>Ya</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-800">
                                <input type="radio" name="radio_pengecualian" value="Tidak" x-model="mohonPengecualian" class="text-amber-600 focus:ring-amber-500">
                                <span>Tidak</span>
                            </label>
                        </div>
                    </div>

                    <!-- Pengecualian Fields if Ya -->
                    <div x-show="mohonPengecualian === 'Ya'" class="space-y-3 pt-3 border-t border-rose-200">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">
                                Sebab-sebab pengecualian lesen <span class="text-rose-500">*</span>
                            </label>
                            <select name="sebab_pengecualian" x-model="sebabPengecualian" class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500">
                                <option value="Penternakan Tradisional / Skala Kecil Isi Rumah">Penternakan Tradisional / Skala Kecil Isi Rumah</option>
                                <option value="Penyelidikan dan Pendidikan Institusi Awam">Penyelidikan dan Pendidikan Institusi Awam</option>
                                <option value="Pusat Kuarantin Kerajaan">Pusat Kuarantin Kerajaan</option>
                                <option value="Lain-Lain">Lain-Lain</option>
                            </select>
                        </div>

                        <div x-show="sebabPengecualian === 'Lain-Lain'">
                            <label class="block font-bold text-slate-700 uppercase mb-1">
                                Lain-Lain Sebab Pengecualian
                            </label>
                            <input type="text" name="sebab_pengecualian_lain" x-model="sebabLain" placeholder="Nyatakan sebab pengecualian" class="w-full px-3.5 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">
                                Lampiran sebab pengecualian <span class="text-rose-500">*</span>
                            </label>
                            <input type="file" name="lampiran_pengecualian" accept=".pdf,.png,.jpg,.jpeg" class="w-full px-3.5 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500">
                            <span class="text-[11px] text-slate-400 mt-1 block">Format fail: PDF, PNG, JPG, JPEG (Maksimum 10MB)</span>
                        </div>
                    </div>
                </div>

                <!-- Navigation Step 2 -->
                <div class="pt-4 flex items-center justify-between">
                    <button type="button" @click="currentStep = 1" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-xl transition flex items-center gap-2">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Sebelum</span>
                    </button>
                    <button type="button" @click="currentStep = 3" class="px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-amber-600/30 transition flex items-center gap-2">
                        <span>Seterusnya</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- STEP 3: LAMPIRAN                           -->
            <!-- ========================================== -->
            <div x-show="currentStep === 3" class="space-y-6">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-extrabold text-slate-800 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center text-xs">3</span>
                        <span>Muat Naik Dokumen & Lampiran</span>
                    </h3>
                    <span class="text-xs text-slate-400">Langkah 3 daripada 4</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Pelan Susunatur Ladang -->
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-2">
                        <label class="block font-bold text-slate-800 uppercase text-xs">
                            Pelan Susunatur & Lokasi Tapak Ladang <span class="text-rose-500">*</span>
                        </label>
                        <p class="text-[11px] text-slate-500">Pelan lakaran tapak ladang, kedudukan reban, dan punca air.</p>
                        <input type="file" name="dokumen_pelan" accept=".pdf,.png,.jpg,.jpeg" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl">
                    </div>

                    <!-- Geran Tanah / Perjanjian Sewa -->
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-2">
                        <label class="block font-bold text-slate-800 uppercase text-xs">
                            Salinan Geran Tanah / Perjanjian Sewa Pajakan <span class="text-rose-500">*</span>
                        </label>
                        <p class="text-[11px] text-slate-500">Bukti hak milik tanah atau perjanjian penyewaan premis tapak.</p>
                        <input type="file" name="dokumen_tanah" accept=".pdf,.png,.jpg,.jpeg" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl">
                    </div>

                    <!-- Kebenaran Merancang / PBT -->
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-2">
                        <label class="block font-bold text-slate-800 uppercase text-xs">
                            Surat Kebenaran Merancang / Kelulusan PBT
                        </label>
                        <p class="text-[11px] text-slate-500">Surat kebenaran daripada Majlis Daerah / PBT berkaitan (jika ada).</p>
                        <input type="file" name="dokumen_pbt" accept=".pdf,.png,.jpg,.jpeg" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl">
                    </div>

                    <!-- Sijil Pendaftaran Perniagaan SSM -->
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-2">
                        <label class="block font-bold text-slate-800 uppercase text-xs">
                            Sijil Pendaftaran Perniagaan (SSM / Koperasi)
                        </label>
                        <p class="text-[11px] text-slate-500">Salinan pendaftaran SSM bagi permohonan atas nama syarikat.</p>
                        <input type="file" name="dokumen_ssm" accept=".pdf,.png,.jpg,.jpeg" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl">
                    </div>
                </div>

                <!-- Navigation Step 3 -->
                <div class="pt-4 flex items-center justify-between">
                    <button type="button" @click="currentStep = 2" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-xl transition flex items-center gap-2">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Sebelum</span>
                    </button>
                    <button type="button" @click="currentStep = 4" class="px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-amber-600/30 transition flex items-center gap-2">
                        <span>Seterusnya (Semakan)</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- STEP 4: SEMAKAN / PENGESAHAN               -->
            <!-- ========================================== -->
            <div x-show="currentStep === 4" class="space-y-6">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-extrabold text-slate-800 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs">4</span>
                        <span>Semakan Maklumat & Perakuan Pemohon</span>
                    </h3>
                    <span class="text-xs text-slate-400">Langkah 4 daripada 4</span>
                </div>

                <!-- Ringkasan Permohonan Card -->
                <div class="bg-slate-50 rounded-2xl border border-slate-200 p-5 space-y-4">
                    <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider text-amber-800 border-b border-slate-200 pb-2 flex items-center gap-2">
                        <i class="fa-solid fa-clipboard-check"></i>
                        <span>Ringkasan Maklumat Permohonan Lesen EPU</span>
                    </h4>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div class="space-y-2">
                            <div>
                                <span class="text-slate-500 font-medium block">Nama Pemohon:</span>
                                <span class="font-bold text-slate-900" x-text="namaPemohon"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 font-medium block">No. Kad Pengenalan / SSM:</span>
                                <span class="font-bold font-mono text-slate-900" x-text="noKp"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 font-medium block">No. Telefon & Emel:</span>
                                <span class="font-bold text-slate-900" x-text="phonePemohon + ' • ' + emailPemohon"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 font-medium block">Alamat Surat Menyurat:</span>
                                <span class="font-bold text-slate-900" x-text="alamatPemohon1 + (alamatPemohon2 ? ', ' + alamatPemohon2 : '') + ', ' + poskodPemohon + ', ' + negeriPemohon"></span>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div>
                                <span class="text-slate-500 font-medium block">Jenis Unggas & Jurusan Aktiviti:</span>
                                <span class="font-bold text-amber-900 uppercase" x-text="jenisUnggas + ' (' + jurusanAktiviti + ')'"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 font-medium block">Lokasi & Jajahan Ladang:</span>
                                <span class="font-bold text-slate-900" x-text="jajahanLadang + ' (Daerah: ' + daerahLadang + ')'"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 font-medium block">Koordinat GPS Lokasi:</span>
                                <span class="font-bold font-mono text-slate-900" x-text="'Lat: ' + latitude + ', Lng: ' + longitude"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 font-medium block">ID Premis & Bilangan Semasa:</span>
                                <span class="font-bold text-slate-900" x-text="idPremis + ' • ' + bilanganSemasa + ' ekor'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Highlight Kapasiti & Fi -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-3 border-t border-slate-200">
                        <div class="p-3 bg-amber-100/60 rounded-xl border border-amber-200">
                            <span class="text-[11px] text-amber-900 font-bold block">Kapasiti Ladang Dikira:</span>
                            <span class="text-lg font-black text-amber-950 font-mono" x-text="computedKapasiti.toLocaleString() + ' ekor'"></span>
                        </div>
                        <div class="p-3 bg-emerald-100/60 rounded-xl border border-emerald-200">
                            <span class="text-[11px] text-emerald-900 font-bold block">Fi Permohonan Lesen:</span>
                            <span class="text-lg font-black text-emerald-950 font-mono" x-text="computedFi === 0 ? 'PERCUMA (RM 0.00)' : 'RM ' + computedFi.toFixed(2)"></span>
                        </div>
                    </div>
                </div>

                <!-- Perakuan Pemohon -->
                <div class="p-5 bg-amber-50/60 rounded-2xl border border-amber-300 space-y-3">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="perakuan" x-model="perakuan" required class="mt-1 rounded text-amber-600 focus:ring-amber-500 w-4 h-4">
                        <div class="text-xs text-slate-800">
                            <span class="font-bold block text-slate-900 mb-0.5">Perakuan Pemohon (Enakmen Perladangan Unggas Kelantan 2005)</span>
                            <span>Saya dengan ini memperakui bahawa segala maklumat yang dinyatakan di dalam borang permohonan ini dan lampiran yang disertakan adalah benar, lengkap dan tepat. Saya bersedia untuk mematuhi segala peruntukan undang-undang, syarat-syarat lesen dan arahan pemeriksaan veterinar.</span>
                        </div>
                    </label>
                </div>

                <!-- Navigation Step 4 & Submit -->
                <div class="pt-4 flex items-center justify-between">
                    <button type="button" @click="currentStep = 3" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-xl transition flex items-center gap-2">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Sebelum</span>
                    </button>
                    <button type="submit" :disabled="!perakuan" :class="perakuan ? 'bg-amber-600 hover:bg-amber-700 shadow-amber-600/30 cursor-pointer' : 'bg-slate-300 text-slate-500 cursor-not-allowed'" class="px-8 py-3.5 text-white font-bold text-sm rounded-xl shadow-lg transition flex items-center gap-2">
                        <i class="fa-solid fa-check"></i>
                        <span>Hantar Permohonan Lesen EPU</span>
                    </button>
                </div>
            </div>

        </form>
    </div>

</div>
@endsection

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@endpush
