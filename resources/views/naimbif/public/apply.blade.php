@extends('layouts.app')

@section('title', 'Borang Permohonan Ladang Bridlot NAIMbif - JPVNK')

@push('styles')
<!-- Leaflet Map CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 260px; width: 100%; border-radius: 0.75rem; z-index: 10; }
    .table-input {
        width: 100%;
        text-align: center;
        padding: 0.4rem 0.25rem;
        font-size: 0.875rem;
        border: 1px solid #cbd5e1;
        border-radius: 0.375rem;
        transition: all 0.15s ease-in-out;
    }
    .table-input:focus {
        border-color: #10b981;
        outline: none;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
    }
    @keyframes pulseSoft {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.95; transform: scale(1.005); }
    }
    .autofill-highlight {
        border-color: #10b981 !important;
        background-color: #f0fdf4 !important;
    }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="applicationForm()">

    <!-- Form Official Header Banner -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-emerald-800 via-emerald-700 to-teal-800 p-6 sm:p-8 text-white relative">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 rounded-2xl bg-white p-1 flex items-center justify-center shadow-md">
                        <img src="{{ asset('images/naimbif-logo.png') }}" alt="Logo NAIMbif" class="h-14 w-auto object-contain">
                    </div>
                    <div>
                        <div class="text-xs uppercase font-extrabold tracking-widest text-amber-300">Jabatan Perkhidmatan Veterinar Negeri Kelantan</div>
                        <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white mt-1">
                            BORANG PERMOHONAN PENYERTAAN LADANG BRIDLOT NAIMbif
                        </h1>
                        <p class="text-xs text-emerald-100 mt-1">Sila masukkan No. Kad Pengenalan untuk auto-fill data pendaftaran sedia ada dalam sistem.</p>
                    </div>
                </div>

                <div class="bg-black/20 backdrop-blur-sm rounded-xl p-3 text-xs text-emerald-100 border border-white/10 max-w-xs text-left">
                    <div class="font-semibold text-white mb-1"><i class="fas fa-magic text-amber-300 mr-1"></i> Auto-Fill Pintar:</div>
                    <p class="text-[11px] leading-snug">
                        Sistem akan mengesan profil penternak anda secara automatik. Anda hanya perlu melengkapkan ruangan yang masih belum diisi.
                    </p>
                </div>
            </div>
        </div>

        @if (session('duplicate_error'))
            @php $dup = session('duplicate_error'); @endphp
            <div class="p-6 bg-amber-50 border-b-2 border-amber-300 text-amber-950">
                <div class="flex items-start space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-200 text-amber-900 flex items-center justify-center text-lg flex-shrink-0 mt-0.5">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="space-y-2 flex-1 text-xs">
                        <h3 class="text-sm font-bold text-amber-900">
                            Pendaftaran Bertindih Dikesan: Permohonan Sedia Ada Ditemui
                        </h3>
                        <p class="leading-relaxed">
                            No. Kad Pengenalan <strong class="font-mono bg-amber-200/80 px-1.5 py-0.5 rounded">{{ $dup['no_kp'] }}</strong> telah pun berdaftar dalam sistem dengan No. Rujukan <strong class="font-mono font-bold text-emerald-800">{{ $dup['no_rujukan'] }}</strong> (Pemohon: <strong>{{ $dup['nama'] }}</strong>, Status: <span class="font-bold text-amber-800">{{ $dup['status'] }}</span>).
                        </p>
                        <p class="text-[11px] text-amber-800">
                            Setiap penternak hanya dibenarkan mempunyai <strong>satu (1) permohonan aktif</strong> pada satu-satu masa. Anda tidak perlu mendaftar semula. Sila semak status atau kemas kini permohonan anda melalui butang di bawah:
                        </p>
                        <div class="pt-2 flex flex-wrap gap-2">
                            <a href="{{ route('naimbif.public.check_status') }}?carian={{ $dup['no_rujukan'] }}" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 shadow-sm transition-all">
                                <i class="fas fa-search mr-1.5"></i> Semak Status ({{ $dup['no_rujukan'] }})
                            </a>
                            <a href="{{ route('naimbif.public.edit', $dup['no_rujukan']) }}" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold text-slate-800 bg-amber-200 hover:bg-amber-300 transition-all">
                                <i class="fas fa-edit mr-1.5"></i> Kemas Kini Maklumat (2FA)
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 bg-rose-50 border-b border-rose-200 text-rose-800 text-xs">
                <div class="font-bold flex items-center mb-1">
                    <i class="fas fa-exclamation-circle mr-1.5 text-sm"></i> Terdapat maklumat yang tidak lengkap atau memerlukan pembetulan:
                </div>
                <ul class="list-disc list-inside space-y-0.5 ml-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Application Form -->
    <form action="{{ route('naimbif.public.store') }}" method="POST" id="permohonanForm" @submit="prepareSubmission">
        @csrf

        <!-- ========================================== -->
        <!-- SEKSYEN 1: MAKLUMAT PESERTA                -->
        <!-- ========================================== -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 mb-6 border-b border-slate-200 gap-2">
                <div class="flex items-center space-x-3">
                    <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 font-extrabold flex items-center justify-center text-sm">1</span>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">MAKLUMAT PESERTA</h2>
                        <p class="text-xs text-slate-500">Butiran peribadi penternak / pemohon</p>
                    </div>
                </div>

                <!-- Auto-fill Status Chip in Header -->
                <template x-if="autofillActive">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-300 text-xs font-bold animate-pulse">
                        <i class="fas fa-circle-check text-emerald-600"></i>
                        <span>Rekod Ditemui & Diisi Automatik</span>
                    </div>
                </template>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- 2. No Kad Pengenalan (Utama untuk Carian Auto-Fill) -->
                <div class="md:col-span-2 bg-gradient-to-br from-emerald-50/50 via-teal-50/30 to-slate-50 p-4 sm:p-5 rounded-2xl border-2 border-emerald-200/80 shadow-xs">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-black uppercase tracking-wider text-emerald-950 flex items-center gap-1.5">
                            <i class="fas fa-id-card text-emerald-700 text-sm"></i>
                            NO. KAD PENGENALAN <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[11px] font-bold text-emerald-800 bg-emerald-100/80 px-2 py-0.5 rounded-md">
                            <i class="fas fa-bolt text-amber-500 mr-1"></i> Auto-Semakan Data
                        </span>
                    </div>

                    <div class="relative">
                        <input type="text" name="no_kp" x-model="noKp" @input.debounce.400ms="checkIc" @blur="checkIc" value="{{ old('no_kp', $pemunya->no_kp ?? ($user->ic_number ?? '')) }}" required maxlength="14" placeholder="Contoh: 850712035411 atau 850712-03-5411"
                               class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-base font-mono font-bold tracking-wider"
                               :class="{'border-emerald-500 bg-white ring-2 ring-emerald-400/20': autofillActive, 'border-amber-400 focus:ring-amber-400 bg-amber-50/40': duplicateInfo.exists}">
                        <div x-show="isCheckingIc" x-cloak class="absolute right-4 top-3.5 text-emerald-600 text-sm flex items-center gap-2">
                            <i class="fas fa-circle-notch fa-spin text-base"></i>
                            <span class="text-xs font-semibold">Menyemak rekod...</span>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1">Masukkan 12 digit No. KP anda. Sistem akan mencari dan mengisi maklumat berdaftar anda secara automatik.</p>
                    @error('no_kp') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror

                    <!-- Dynamic Auto-fill Notification Card -->
                    <template x-if="autofillActive && !duplicateInfo.exists">
                        <div class="mt-3 p-4 bg-emerald-50 border border-emerald-300 rounded-xl text-xs text-emerald-950 space-y-2 shadow-xs">
                            <div class="flex items-center justify-between">
                                <div class="font-bold flex items-center text-emerald-900 text-sm">
                                    <i class="fas fa-magic text-emerald-600 mr-2 text-base"></i> Data Penternak Ditemui dalam Pangkalan Data JPVNK!
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full bg-emerald-200/80 text-emerald-900 font-extrabold text-[11px]" x-text="autofillKeys.length + ' Ruangan Diisikan'"></span>
                            </div>
                            <p class="text-[11px] text-emerald-800 leading-relaxed">
                                Rekod anda telah dikesan daripada: <strong class="font-semibold text-emerald-950" x-text="autofillSource"></strong>. Maklumat yang telah berdaftar telah diisikan ke dalam borang. Sila <strong>semak dan hanya lengkapkan baki ruangan yang masih kosong</strong>.
                            </p>
                        </div>
                    </template>

                    <!-- Real-Time Duplicate Warning Card -->
                    <div x-show="duplicateInfo.exists" x-cloak class="mt-3 p-4 bg-amber-50 border border-amber-300 rounded-xl text-xs text-amber-950 space-y-2">
                        <div class="font-bold flex items-center text-amber-900">
                            <i class="fas fa-exclamation-circle text-amber-600 mr-1.5 text-sm"></i> Rekod Permohonan Aktif Ditemui
                        </div>
                        <p class="text-[11px] leading-relaxed text-amber-800">
                            No. KP ini telah mempunyai permohonan aktif: <strong class="font-mono text-emerald-800" x-text="duplicateInfo.no_rujukan"></strong> (<span x-text="duplicateInfo.nama"></span> - Status: <span class="font-semibold" x-text="duplicateInfo.status"></span>).
                        </p>
                        <div class="flex flex-wrap gap-2 pt-1">
                            <a :href="duplicateInfo.check_url" class="px-3 py-1.5 rounded-lg bg-emerald-700 text-white font-bold text-[11px] hover:bg-emerald-800 inline-flex items-center">
                                <i class="fas fa-search mr-1"></i> Semak Status
                            </a>
                            <a :href="duplicateInfo.edit_url" class="px-3 py-1.5 rounded-lg bg-amber-200 text-amber-900 font-bold text-[11px] hover:bg-amber-300 inline-flex items-center">
                                <i class="fas fa-edit mr-1"></i> Kemas Kini (2FA)
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 1. Nama Penuh -->
                <div class="md:col-span-2">
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            1. NAMA PENUH <span class="text-rose-500">*</span>
                        </label>
                        <template x-if="isAutofilled('nama')">
                            <span class="text-[10px] font-bold px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md">
                                <i class="fas fa-check text-emerald-600 mr-1"></i> Auto-fill JPVNK
                            </span>
                        </template>
                    </div>
                    <input type="text" name="nama" x-model="formData.nama" value="{{ old('nama') }}" required placeholder="Contoh: WAN MUHAMMAD AZLAN BIN WAN HASSAN"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm uppercase transition"
                           :class="{'autofill-highlight': isAutofilled('nama')}">
                    @error('nama') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- 3. No Telefon -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            3. NO. TELEFON <span class="text-rose-500">*</span>
                        </label>
                        <template x-if="isAutofilled('no_telefon')">
                            <span class="text-[10px] font-bold px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md">
                                <i class="fas fa-check text-emerald-600 mr-1"></i> Auto-fill JPVNK
                            </span>
                        </template>
                    </div>
                    <input type="tel" name="no_telefon" x-model="formData.no_telefon" value="{{ old('no_telefon') }}" required placeholder="Contoh: 019-9112233"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition"
                           :class="{'autofill-highlight': isAutofilled('no_telefon')}">
                    @error('no_telefon') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Jajahan Tetap -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            JAJAHAN <span class="text-rose-500">*</span>
                        </label>
                        <template x-if="isAutofilled('jajahan')">
                            <span class="text-[10px] font-bold px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md">
                                <i class="fas fa-check text-emerald-600 mr-1"></i> Auto-fill JPVNK
                            </span>
                        </template>
                    </div>
                    <select name="jajahan" x-model="formData.jajahan" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition"
                            :class="{'autofill-highlight': isAutofilled('jajahan')}">
                        <option value="">-- Pilih Jajahan --</option>
                        @foreach($jajahans as $j)
                            <option value="{{ $j }}" {{ old('jajahan') == $j ? 'selected' : '' }}>{{ $j }}</option>
                        @endforeach
                    </select>
                    @error('jajahan') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- 4. Alamat Tetap -->
                <div class="md:col-span-2">
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            4. ALAMAT TETAP <span class="text-rose-500">*</span>
                        </label>
                        <template x-if="isAutofilled('alamat_tetap')">
                            <span class="text-[10px] font-bold px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md">
                                <i class="fas fa-check text-emerald-600 mr-1"></i> Auto-fill JPVNK
                            </span>
                        </template>
                    </div>
                    <textarea name="alamat_tetap" x-model="formData.alamat_tetap" rows="2" required placeholder="Alamat kediaman tetap anda..."
                              class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition"
                              :class="{'autofill-highlight': isAutofilled('alamat_tetap')}">{{ old('alamat_tetap') }}</textarea>
                    @error('alamat_tetap') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Poskod Tetap -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            POSKOD <span class="text-rose-500">*</span>
                        </label>
                        <template x-if="isAutofilled('poskod')">
                            <span class="text-[10px] font-bold px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md">
                                <i class="fas fa-check text-emerald-600 mr-1"></i> Auto-fill JPVNK
                            </span>
                        </template>
                    </div>
                    <input type="text" name="poskod" x-model="formData.poskod" value="{{ old('poskod') }}" required maxlength="5" placeholder="Contoh: 15050"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition"
                           :class="{'autofill-highlight': isAutofilled('poskod')}">
                    @error('poskod') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- 5. Pengalaman Menternak -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            5. PENGALAMAN MENTERNAK (TAHUN) <span class="text-rose-500">*</span>
                        </label>
                        <template x-if="isAutofilled('pengalaman_menternak')">
                            <span class="text-[10px] font-bold px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md">
                                <i class="fas fa-check text-emerald-600 mr-1"></i> Rekod Sedia Ada
                            </span>
                        </template>
                    </div>
                    <div class="relative">
                        <input type="number" name="pengalaman_menternak" x-model.number="formData.pengalaman_menternak" value="{{ old('pengalaman_menternak', 0) }}" min="0" max="80" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm pr-16 transition"
                               :class="{'autofill-highlight': isAutofilled('pengalaman_menternak')}">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-xs font-semibold text-slate-400">Tahun</span>
                    </div>
                    @error('pengalaman_menternak') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- 6. Status Penternakan -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        6. STATUS PENTERNAKAN <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3 mt-1.5">
                        <label class="flex items-center p-3 border rounded-xl cursor-pointer hover:bg-slate-50 transition-colors"
                               :class="formData.status_penternakan === 'Sepenuh Masa' ? 'border-emerald-500 bg-emerald-50/50 font-bold' : 'border-slate-300'">
                            <input type="radio" name="status_penternakan" value="Sepenuh Masa" x-model="formData.status_penternakan" class="text-emerald-600 focus:ring-emerald-500">
                            <span class="ml-2 text-xs text-slate-800">Sepenuh Masa</span>
                        </label>
                        <label class="flex items-center p-3 border rounded-xl cursor-pointer hover:bg-slate-50 transition-colors"
                               :class="formData.status_penternakan === 'Sampingan' ? 'border-emerald-500 bg-emerald-50/50 font-bold' : 'border-slate-300'">
                            <input type="radio" name="status_penternakan" value="Sampingan" x-model="formData.status_penternakan" class="text-emerald-600 focus:ring-emerald-500">
                            <span class="ml-2 text-xs text-slate-800">Sampingan</span>
                        </label>
                    </div>
                </div>

                <!-- 7. Kursus Berkaitan -->
                <div class="md:col-span-2 bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            7. PERNAH MENGIKUTI SEBARANG KURSUS BERKAITAN? <span class="text-rose-500">*</span>
                        </label>
                        <template x-if="isAutofilled('pernah_kursus')">
                            <span class="text-[10px] font-bold px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md">
                                <i class="fas fa-graduation-cap text-emerald-600 mr-1"></i> Rekod Kursus JPVNK
                            </span>
                        </template>
                    </div>
                    <div class="flex items-center space-x-6 mb-3">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="pernah_kursus" value="1" x-model="formData.pernah_kursus" class="text-emerald-600 focus:ring-emerald-500">
                            <span class="ml-2 text-xs font-semibold text-slate-800">YA</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="pernah_kursus" value="0" x-model="formData.pernah_kursus" class="text-emerald-600 focus:ring-emerald-500">
                            <span class="ml-2 text-xs font-semibold text-slate-800">TIDAK</span>
                        </label>
                    </div>

                    <!-- Jika Ya: Butiran Kursus -->
                    <div x-show="formData.pernah_kursus === '1' || formData.pernah_kursus === 1 || formData.pernah_kursus === true" class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3 pt-3 border-t border-slate-200">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">NAMA KURSUS:</label>
                            <input type="text" name="nama_kursus" x-model="formData.nama_kursus" value="{{ old('nama_kursus') }}" placeholder="Contoh: Kursus Pengurusan Ternakan Bridlot"
                                   class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs focus:ring-emerald-500 focus:border-emerald-500 transition"
                                   :class="{'autofill-highlight': isAutofilled('nama_kursus')}">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">ANJURAN:</label>
                            <input type="text" name="anjuran_kursus" x-model="formData.anjuran_kursus" value="{{ old('anjuran_kursus') }}" placeholder="Contoh: JPV Negeri Kelantan / MARDI"
                                   class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs focus:ring-emerald-500 focus:border-emerald-500 transition"
                                   :class="{'autofill-highlight': isAutofilled('anjuran_kursus')}">
                        </div>
                    </div>

                    <!-- Jika Tidak: Minat Sertai Kursus JPVNK -->
                    <div x-show="formData.pernah_kursus === '0' || formData.pernah_kursus === 0 || formData.pernah_kursus === false" class="mt-3 pt-3 border-t border-slate-200">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            JIKA TIDAK, ADAKAH ANDA BERMINAT UNTUK MENYERTAI KURSUS YANG DIANJURKAN JPVNK?
                        </label>
                        <div class="flex items-center space-x-6 mt-1">
                            <label class="inline-flex items-center">
                                <input type="radio" name="berminat_kursus_jpvnk" value="1" x-model="formData.berminat_kursus_jpvnk" class="text-emerald-600">
                                <span class="ml-2 text-xs font-medium text-slate-700">YA, BERMINAT</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="berminat_kursus_jpvnk" value="0" x-model="formData.berminat_kursus_jpvnk" class="text-emerald-600">
                                <span class="ml-2 text-xs font-medium text-slate-700">TIDAK</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SEKSYEN 2: MAKLUMAT ASAS LADANG            -->
        <!-- ========================================== -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8 mb-8">
            <div class="flex items-center space-x-3 pb-4 mb-6 border-b border-slate-200">
                <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 font-extrabold flex items-center justify-center text-sm">2</span>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">MAKLUMAT ASAS LADANG</h2>
                    <p class="text-xs text-slate-500">Lokasi, tanah, koordinat GPS dan fasiliti ladang</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- 8. Alamat Ladang -->
                <div class="md:col-span-2">
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            8. ALAMAT LADANG <span class="text-xs font-normal text-slate-400">(Jika berlainan dari alamat di atas)</span>
                        </label>
                        <button type="button" @click="samaAlamat = !samaAlamat" class="text-xs text-emerald-600 hover:text-emerald-800 font-semibold underline">
                            <span x-text="samaAlamat ? 'Klik jika berlainan dari alamat tetap' : 'Guna alamat tetap'"></span>
                        </button>
                    </div>
                    
                    <div x-show="!samaAlamat" class="space-y-3 mt-2">
                        <textarea name="alamat_ladang" x-model="formData.alamat_ladang" rows="2" placeholder="Alamat tapak ladang / kandang..."
                                  class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm transition"
                                  :class="{'autofill-highlight': isAutofilled('alamat_ladang')}">{{ old('alamat_ladang') }}</textarea>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">POSKOD LADANG</label>
                                <input type="text" name="poskod_ladang" x-model="formData.poskod_ladang" value="{{ old('poskod_ladang') }}" maxlength="5" placeholder="Poskod"
                                       class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs transition"
                                       :class="{'autofill-highlight': isAutofilled('poskod_ladang')}">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">JAJAHAN LADANG</label>
                                <select name="jajahan_ladang" x-model="formData.jajahan_ladang" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs transition"
                                        :class="{'autofill-highlight': isAutofilled('jajahan_ladang')}">
                                    <option value="">-- Pilih Jajahan --</option>
                                    @foreach($jajahans as $j)
                                        <option value="{{ $j }}" {{ old('jajahan_ladang') == $j ? 'selected' : '' }}>{{ $j }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div x-show="samaAlamat" class="p-3 bg-slate-50 border border-dashed border-slate-300 rounded-xl text-xs text-slate-500">
                        <i class="fas fa-check-circle text-emerald-500 mr-1"></i> Alamat ladang sama seperti Alamat Tetap di atas.
                    </div>
                </div>

                <!-- 9. Lokasi GPS -->
                <div class="md:col-span-2 bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                        <div class="flex items-center gap-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                9. LOKASI GPS LADANG
                            </label>
                            <template x-if="isAutofilled('gps_latitud')">
                                <span class="text-[10px] font-bold px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md">
                                    <i class="fas fa-map-marker-alt text-emerald-600 mr-1"></i> Koordinat Rekod JPVNK
                                </span>
                            </template>
                        </div>
                        <button type="button" @click="detectLocation()" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-600 text-white hover:bg-emerald-700 transition-colors shadow-sm">
                            <i class="fas fa-crosshairs mr-1.5"></i> Kesan Lokasi Semasa Saya
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">LONGITUD [E]</label>
                            <input type="text" name="gps_longitud" x-model="gpsLng" placeholder="Contoh: 102.291240"
                                   class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs focus:ring-emerald-500 font-mono"
                                   :class="{'autofill-highlight': isAutofilled('gps_longitud')}">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">LATITUD [N]</label>
                            <input type="text" name="gps_latitud" x-model="gpsLat" placeholder="Contoh: 6.158420"
                                   class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs focus:ring-emerald-500 font-mono"
                                   :class="{'autofill-highlight': isAutofilled('gps_latitud')}">
                        </div>
                    </div>

                    <!-- Interactive Map Container -->
                    <div class="relative">
                        <div id="map"></div>
                        <div class="text-[10px] text-slate-500 mt-1.5 flex items-center justify-between">
                            <span><i class="fas fa-info-circle text-emerald-600 mr-1"></i> Klik pada peta untuk menetapkan pin lokasi ladang anda secara tepat.</span>
                        </div>
                    </div>
                </div>

                <!-- 10. Status Tanah -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        10. STATUS TANAH <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach(['Sendiri', 'Sewa', 'Kerajaan', 'Lain-lain'] as $st)
                            <label class="flex items-center p-3 border rounded-xl cursor-pointer hover:bg-slate-50 transition-colors" :class="formData.status_tanah === '{{ $st }}' ? 'border-emerald-500 bg-emerald-50/50 font-bold' : 'border-slate-300'">
                                <input type="radio" name="status_tanah" value="{{ $st }}" x-model="formData.status_tanah" class="text-emerald-600 focus:ring-emerald-500">
                                <span class="ml-2 text-xs text-slate-800">{{ $st }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div x-show="formData.status_tanah === 'Lain-lain'" class="mt-3">
                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">JIKA LAIN-LAIN, NYATAKAN:</label>
                        <input type="text" name="status_tanah_lain" x-model="formData.status_tanah_lain" value="{{ old('status_tanah_lain') }}" placeholder="Nyatakan status tanah..."
                               class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs">
                    </div>
                </div>

                <!-- 11. Keluasan Tanah -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            11. KELUASAN TANAH <span class="text-rose-500">*</span>
                        </label>
                        <template x-if="isAutofilled('keluasan_tanah')">
                            <span class="text-[10px] font-bold px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md">
                                <i class="fas fa-check text-emerald-600 mr-1"></i> Auto-fill JPVNK
                            </span>
                        </template>
                    </div>
                    <div class="relative">
                        <input type="number" step="0.1" min="0.1" name="keluasan_tanah" x-model="formData.keluasan_tanah" value="{{ old('keluasan_tanah') }}" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm pr-16 transition"
                               :class="{'autofill-highlight': isAutofilled('keluasan_tanah')}">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-xs font-semibold text-slate-400">Ekar</span>
                    </div>
                    @error('keluasan_tanah') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- 12. Padang Ragut -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        12. PADANG RAGUT <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3 mt-1">
                        <label class="flex items-center p-3 border rounded-xl cursor-pointer hover:bg-slate-50 transition-colors"
                               :class="formData.padang_ragut === 'Ada' ? 'border-emerald-500 bg-emerald-50/50 font-bold' : 'border-slate-300'">
                            <input type="radio" name="padang_ragut" value="Ada" x-model="formData.padang_ragut" class="text-emerald-600">
                            <span class="ml-2 text-xs text-slate-800">ADA</span>
                        </label>
                        <label class="flex items-center p-3 border rounded-xl cursor-pointer hover:bg-slate-50 transition-colors"
                               :class="formData.padang_ragut === 'Tiada' ? 'border-emerald-500 bg-emerald-50/50 font-bold' : 'border-slate-300'">
                            <input type="radio" name="padang_ragut" value="Tiada" x-model="formData.padang_ragut" class="text-emerald-600">
                            <span class="ml-2 text-xs text-slate-800">TIADA</span>
                        </label>
                    </div>
                </div>

                <!-- 13. Bilangan Pekerja -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            13. BILANGAN PEKERJA <span class="text-rose-500">*</span>
                        </label>
                        <template x-if="isAutofilled('bilangan_pekerja')">
                            <span class="text-[10px] font-bold px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md">
                                <i class="fas fa-check text-emerald-600 mr-1"></i> Auto-fill
                            </span>
                        </template>
                    </div>
                    <div class="relative">
                        <input type="number" min="0" name="bilangan_pekerja" x-model.number="formData.bilangan_pekerja" value="{{ old('bilangan_pekerja', 1) }}" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm pr-16 transition"
                               :class="{'autofill-highlight': isAutofilled('bilangan_pekerja')}">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-xs font-semibold text-slate-400">Orang</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SEKSYEN 3: MAKLUMAT ASAS TERNAKAN & MATRIKS-->
        <!-- ========================================== -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8 mb-8">
            <div class="flex items-center space-x-3 pb-4 mb-6 border-b border-slate-200">
                <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 font-extrabold flex items-center justify-center text-sm">3</span>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">MAKLUMAT ASAS TERNAKAN</h2>
                    <p class="text-xs text-slate-500">Punca ternakan, kaedah pembiakan dan stok baka lembu semasa</p>
                </div>
            </div>

            <!-- 14. Punca Ternakan & 15. Kaedah Pembiakan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 pb-6 border-b border-slate-200">
                <!-- 14. Punca Ternakan -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        14. PUNCA TERNAKAN <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="flex items-center p-2.5 border rounded-xl cursor-pointer hover:bg-slate-50 transition-colors" :class="formData.punca_ternakan === 'Beli' ? 'border-emerald-500 bg-emerald-50/50 font-bold' : 'border-slate-300'">
                            <input type="radio" name="punca_ternakan" value="Beli" x-model="formData.punca_ternakan" class="text-emerald-600">
                            <span class="ml-1.5 text-xs text-slate-800">BELI</span>
                        </label>
                        <label class="flex items-center p-2.5 border rounded-xl cursor-pointer hover:bg-slate-50 transition-colors" :class="formData.punca_ternakan === 'Pawah' ? 'border-emerald-500 bg-emerald-50/50 font-bold' : 'border-slate-300'">
                            <input type="radio" name="punca_ternakan" value="Pawah" x-model="formData.punca_ternakan" class="text-emerald-600">
                            <span class="ml-1.5 text-xs text-slate-800">PAWAH</span>
                        </label>
                        <label class="flex items-center p-2.5 border rounded-xl cursor-pointer hover:bg-slate-50 transition-colors" :class="formData.punca_ternakan === 'Lain-lain' ? 'border-emerald-500 bg-emerald-50/50 font-bold' : 'border-slate-300'">
                            <input type="radio" name="punca_ternakan" value="Lain-lain" x-model="formData.punca_ternakan" class="text-emerald-600">
                            <span class="ml-1.5 text-xs text-slate-800">LAIN-LAIN</span>
                        </label>
                    </div>

                    <div x-show="formData.punca_ternakan === 'Lain-lain'" class="mt-2">
                        <input type="text" name="punca_ternakan_lain" x-model="formData.punca_ternakan_lain" value="{{ old('punca_ternakan_lain') }}" placeholder="Nyatakan punca ternakan..."
                               class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs">
                    </div>
                </div>

                <!-- 15. Kaedah Pembiakan -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        15. KAEDAH PEMBIAKAN <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center p-2.5 border rounded-xl cursor-pointer hover:bg-slate-50 transition-colors" :class="formData.kaedah_pembiakan === 'Asli' ? 'border-emerald-500 bg-emerald-50/50 font-bold' : 'border-slate-300'">
                            <input type="radio" name="kaedah_pembiakan" value="Asli" x-model="formData.kaedah_pembiakan" class="text-emerald-600">
                            <span class="ml-2 text-xs text-slate-800">ASLI</span>
                        </label>
                        <label class="flex items-center p-2.5 border rounded-xl cursor-pointer hover:bg-slate-50 transition-colors" :class="formData.kaedah_pembiakan === 'Permanian Beradas' ? 'border-emerald-500 bg-emerald-50/50 font-bold' : 'border-slate-300'">
                            <input type="radio" name="kaedah_pembiakan" value="Permanian Beradas" x-model="formData.kaedah_pembiakan" class="text-emerald-600">
                            <span class="ml-2 text-xs text-slate-800">PERMANIAN BERADAS (AI)</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- 16. Matriks Stok Ternakan -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800">
                        16. STOK TERNAKAN (BAKA & PECAHAN JANTINA)
                    </label>
                    <span class="text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                        <i class="fas fa-calculator mr-1"></i> Pengiraan Automatik
                    </span>
                </div>

                <div class="overflow-x-auto border border-slate-300 rounded-xl shadow-inner">
                    <table class="min-w-full text-xs text-center border-collapse">
                        <thead>
                            <tr class="bg-slate-800 text-white font-bold">
                                <th rowspan="2" class="p-3 border-r border-slate-700 w-1/4 text-left">STOK TERNAKAN (BAKA)</th>
                                <th colspan="3" class="p-2 border-r border-slate-700 bg-emerald-900/90 text-emerald-200 uppercase tracking-wider">BETINA</th>
                                <th colspan="2" class="p-2 border-r border-slate-700 bg-blue-900/90 text-blue-200 uppercase tracking-wider">JANTAN</th>
                                <th rowspan="2" class="p-3 bg-slate-900 w-24">JUMLAH</th>
                            </tr>
                            <tr class="bg-slate-700 text-slate-200 font-semibold text-[11px]">
                                <th class="p-2 border-r border-slate-600 bg-emerald-950/60">ANAK</th>
                                <th class="p-2 border-r border-slate-600 bg-emerald-950/60">DARA</th>
                                <th class="p-2 border-r border-slate-600 bg-emerald-950/60">INDUK</th>
                                <th class="p-2 border-r border-slate-600 bg-blue-950/60">ANAK JANTAN</th>
                                <th class="p-2 border-r border-slate-600 bg-blue-950/60">PEJANTAN</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach($bakas as $b)
                                @php $bKey = \Illuminate\Support\Str::slug($b, '_'); @endphp
                                <tr class="hover:bg-slate-50/80 transition-colors font-medium">
                                    <td class="p-2.5 text-left border-r border-slate-200 font-bold text-slate-800">
                                        {{ $b }}
                                        @if($b === 'LAIN-LAIN')
                                            <div class="mt-1">
                                                <input type="text" name="stok[{{ $b }}][nama_baka_lain]" x-model="stok.lain_lain.nama_baka_lain" placeholder="Nyatakan baka lain..."
                                                       class="w-full px-2 py-1 text-[11px] rounded border border-slate-300 font-normal">
                                            </div>
                                        @endif
                                    </td>
                                    
                                    <!-- Betina Anak -->
                                    <td class="p-1.5 border-r border-slate-200 bg-emerald-50/30">
                                        <input type="number" min="0" name="stok[{{ $b }}][betina_anak]"
                                               x-model.number="stok.{{ $bKey }}.betina_anak"
                                               class="table-input">
                                    </td>
                                    <!-- Betina Dara -->
                                    <td class="p-1.5 border-r border-slate-200 bg-emerald-50/30">
                                        <input type="number" min="0" name="stok[{{ $b }}][betina_dara]"
                                               x-model.number="stok.{{ $bKey }}.betina_dara"
                                               class="table-input">
                                    </td>
                                    <!-- Betina Induk -->
                                    <td class="p-1.5 border-r border-slate-200 bg-emerald-50/30">
                                        <input type="number" min="0" name="stok[{{ $b }}][betina_induk]"
                                               x-model.number="stok.{{ $bKey }}.betina_induk"
                                               class="table-input">
                                    </td>

                                    <!-- Jantan Anak -->
                                    <td class="p-1.5 border-r border-slate-200 bg-blue-50/30">
                                        <input type="number" min="0" name="stok[{{ $b }}][jantan_anak]"
                                               x-model.number="stok.{{ $bKey }}.jantan_anak"
                                               class="table-input">
                                    </td>
                                    <!-- Jantan Pejantan -->
                                    <td class="p-1.5 border-r border-slate-200 bg-blue-50/30">
                                        <input type="number" min="0" name="stok[{{ $b }}][jantan_pejantan]"
                                               x-model.number="stok.{{ $bKey }}.jantan_pejantan"
                                               class="table-input">
                                    </td>

                                    <!-- Row Total -->
                                    <td class="p-2 font-bold text-slate-900 bg-slate-100 text-sm">
                                        <span x-text="getRowTotal('{{ $bKey }}')">0</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-900 text-white font-extrabold text-xs">
                                <td class="p-3 text-left border-r border-slate-800 tracking-wider uppercase">JUMLAH KESELURUHAN</td>
                                <td class="p-2 border-r border-slate-800 text-emerald-300 font-mono text-sm" x-text="getColTotal('betina_anak')">0</td>
                                <td class="p-2 border-r border-slate-800 text-emerald-300 font-mono text-sm" x-text="getColTotal('betina_dara')">0</td>
                                <td class="p-2 border-r border-slate-800 text-emerald-300 font-mono text-sm" x-text="getColTotal('betina_induk')">0</td>
                                <td class="p-2 border-r border-slate-800 text-blue-300 font-mono text-sm" x-text="getColTotal('jantan_anak')">0</td>
                                <td class="p-2 border-r border-slate-800 text-blue-300 font-mono text-sm" x-text="getColTotal('jantan_pejantan')">0</td>
                                <td class="p-3 bg-amber-500 text-slate-950 font-black font-mono text-base" x-text="getGrandTotal()">0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SEKSYEN 4: PENGAKUAN & TANDATANGAN         -->
        <!-- ========================================== -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8 mb-8">
            <div class="flex items-center space-x-3 pb-4 mb-6 border-b border-slate-200">
                <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 font-extrabold flex items-center justify-center text-sm">4</span>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">PENGAKUAN PEMOHON</h2>
                    <p class="text-xs text-slate-500">Perakuan kebenaran maklumat dan tandatangan</p>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Pengakuan Benar Checkbox -->
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" name="pengakuan_benar" value="1" required {{ old('pengakuan_benar') ? 'checked' : '' }}
                               class="mt-1 h-5 w-5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        <span class="ml-3 text-xs leading-relaxed font-semibold text-slate-800">
                            SAYA MENGAKUI BAHAWA SEMUA BUTIRAN DAN MAKLUMAT YANG DIBERIKAN DI ATAS ADALAH BENAR DAN TEPAT. SEKIRANYA TERDAPAT SEBARANG MAKLUMAT PALSU, PIHAK JABATAN BERHAK MEMBATALKAN PERMOHONAN SAYA SERTA-MERTA.
                        </span>
                    </label>
                    @error('pengakuan_benar') <p class="text-rose-600 text-xs mt-2 ml-8">{{ $message }}</p> @enderror
                </div>

                <!-- Signature Pad Container -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                TANDATANGAN DIGITAL PEMOHON
                            </label>
                            <button type="button" @click="clearSignature()" class="text-xs text-rose-600 hover:text-rose-800 font-semibold underline">
                                <i class="fas fa-undo mr-1"></i> Padam Semula
                            </button>
                        </div>
                        <div class="border-2 border-dashed border-slate-300 rounded-2xl bg-white p-1 relative shadow-inner">
                            <canvas id="signatureCanvas" class="w-full h-44 rounded-xl cursor-crosshair"></canvas>
                            <div class="absolute bottom-2 left-3 text-[10px] text-slate-400 pointer-events-none">
                                <i class="fas fa-pencil-alt mr-1"></i> Tandatangan menggunakan tetikus / skrin sentuh
                            </div>
                        </div>
                        <input type="hidden" name="tandatangan" id="signatureData">
                    </div>

                    <!-- Tarikh Permohonan -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            TARIKH PERMOHONAN
                        </label>
                        <input type="date" name="tarikh_permohonan" value="{{ date('Y-m-d') }}" readonly
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 bg-slate-100 text-sm font-semibold text-slate-700">
                        
                        <div class="mt-6 p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-500 space-y-1">
                            <div class="font-bold text-slate-700"><i class="fas fa-shield-alt text-emerald-600 mr-1"></i> Pengesahan Rasmi</div>
                            <p>Setelah dihantar, permohonan anda akan terus direkodkan ke dalam pangkalan data Pejabat Perkhidmatan Veterinar Negeri Kelantan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-end gap-4">
            <a href="{{ route('naimbif.public.home') }}" class="w-full sm:w-auto px-6 py-3.5 rounded-xl text-sm font-bold text-slate-600 bg-slate-200 hover:bg-slate-300 text-center transition-colors">
                Batal
            </a>
            <template x-if="duplicateInfo.exists">
                <a :href="duplicateInfo.check_url" class="w-full sm:w-auto px-8 py-4 rounded-xl text-base font-bold text-white bg-amber-600 hover:bg-amber-700 shadow-lg shadow-amber-600/30 text-center transition-all flex items-center justify-center">
                    <i class="fas fa-search mr-2"></i> LIHAT PERMOHONAN SEDIA ADA (<span x-text="duplicateInfo.no_rujukan"></span>)
                </a>
            </template>
            <button x-show="!duplicateInfo.exists" type="submit" class="w-full sm:w-auto px-10 py-4 rounded-xl text-base font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 shadow-xl shadow-emerald-600/30 transition-all transform hover:-translate-y-0.5 flex items-center justify-center">
                <i class="fas fa-paper-plane mr-2.5 text-lg"></i> HANTAR PERMOHONAN SEKARANG
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<!-- Leaflet Map JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- Signature Pad JS -->
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

<script>
    let map, marker, signaturePad;

    function applicationForm() {
        return {
            noKp: '{{ old("no_kp", $pemunya->no_kp ?? ($user->ic_number ?? "")) }}',
            isCheckingIc: false,
            autofillActive: false,
            autofillSource: '',
            autofillKeys: [],
            duplicateInfo: { exists: false },
            samaAlamat: true,
            gpsLat: '{{ old("gps_latitud", "6.1254") }}',
            gpsLng: '{{ old("gps_longitud", "102.2381") }}',
            
            formData: {
                nama: '{{ old("nama", $pemunya->nama ?? ($user->name ?? "")) }}',
                no_telefon: '{{ old("no_telefon", $pemunya->no_telefon ?? ($user->phone ?? "")) }}',
                alamat_tetap: '{{ old("alamat_tetap", $pemunya->alamat ?? ($user->address ?? "")) }}',
                poskod: '{{ old("poskod", $pemunya->poskod ?? ($user->poskod ?? "")) }}',
                jajahan: '{{ old("jajahan", $pemunya->jajahan ?? ($user->jajahan ?? "")) }}',
                pengalaman_menternak: {{ old("pengalaman_menternak", 0) }},
                status_penternakan: '{{ old("status_penternakan", "Sepenuh Masa") }}',
                pernah_kursus: '{{ old("pernah_kursus", "0") }}',
                nama_kursus: '{{ old("nama_kursus", "") }}',
                anjuran_kursus: '{{ old("anjuran_kursus", "") }}',
                berminat_kursus_jpvnk: '{{ old("berminat_kursus_jpvnk", "1") }}',
                alamat_ladang: '{{ old("alamat_ladang", "") }}',
                poskod_ladang: '{{ old("poskod_ladang", "") }}',
                jajahan_ladang: '{{ old("jajahan_ladang", "") }}',
                status_tanah: '{{ old("status_tanah", "Sendiri") }}',
                status_tanah_lain: '{{ old("status_tanah_lain", "") }}',
                keluasan_tanah: '{{ old("keluasan_tanah", "") }}',
                padang_ragut: '{{ old("padang_ragut", "Ada") }}',
                bilangan_pekerja: {{ old("bilangan_pekerja", 1) }},
                punca_ternakan: '{{ old("punca_ternakan", "Beli") }}',
                punca_ternakan_lain: '{{ old("punca_ternakan_lain", "") }}',
                kaedah_pembiakan: '{{ old("kaedah_pembiakan", "Permanian Beradas") }}',
            },

            stok: {
                @foreach($bakas as $b)
                '{{ \Illuminate\Support\Str::slug($b, '_') }}': {
                    betina_anak: 0,
                    betina_dara: 0,
                    betina_induk: 0,
                    jantan_anak: 0,
                    jantan_pejantan: 0,
                    @if($b === 'LAIN-LAIN')
                    nama_baka_lain: ''
                    @endif
                },
                @endforeach
            },

            isAutofilled(key) {
                return this.autofillKeys.includes(key);
            },

            init() {
                this.$nextTick(() => {
                    this.initMap();
                    this.initSignature();
                    if (this.noKp && this.noKp.replace(/[^0-9]/g, '').length >= 6) {
                        this.checkIc();
                    }
                });
            },

            async checkIc() {
                const clean = (this.noKp || '').replace(/[^0-9]/g, '');
                if (clean.length < 6) {
                    this.duplicateInfo = { exists: false };
                    this.autofillActive = false;
                    this.autofillKeys = [];
                    return;
                }

                this.isCheckingIc = true;
                try {
                    const res = await fetch('{{ route("naimbif.public.check_ic") }}?no_kp=' + encodeURIComponent(clean));
                    const data = await res.json();
                    
                    if (data.has_active_application) {
                        this.duplicateInfo = data.application || { exists: true };
                        this.duplicateInfo.exists = true;
                    } else {
                        this.duplicateInfo = { exists: false };
                    }

                    if (data.found && data.autofill) {
                        this.autofillActive = true;
                        this.autofillSource = data.source_label || 'Pangkalan Data JPVNK';
                        this.autofillKeys = data.autofill_keys || [];

                        // 1. Isikan Data Peserta & Ladang
                        for (const key in data.autofill) {
                            if (key === 'stok') continue;
                            const val = data.autofill[key];
                            if (val !== null && val !== undefined && val !== '') {
                                this.formData[key] = val;
                            }
                        }

                        // 2. Set GPS Koordinat & Reposisi Peta
                        if (data.autofill.gps_latitud && data.autofill.gps_longitud) {
                            this.gpsLat = data.autofill.gps_latitud;
                            this.gpsLng = data.autofill.gps_longitud;
                            const lat = parseFloat(this.gpsLat);
                            const lng = parseFloat(this.gpsLng);
                            if (!isNaN(lat) && !isNaN(lng) && map && marker) {
                                map.setView([lat, lng], 14);
                                marker.setLatLng([lat, lng]);
                            }
                        }

                        // 3. Set Alamat Ladang status jika berbeza
                        if (data.autofill.alamat_ladang && data.autofill.alamat_ladang !== data.autofill.alamat_tetap) {
                            this.samaAlamat = false;
                        }

                        // 4. Set Stok Ternakan
                        if (data.autofill.stok && typeof data.autofill.stok === 'object') {
                            for (const bKey in data.autofill.stok) {
                                if (this.stok[bKey]) {
                                    this.stok[bKey] = Object.assign({}, this.stok[bKey], data.autofill.stok[bKey]);
                                }
                            }
                        }
                    } else if (!data.found) {
                        this.autofillActive = false;
                        this.autofillKeys = [];
                    }
                } catch (e) {
                    console.error('Ralat semasa semakan No. KP:', e);
                } finally {
                    this.isCheckingIc = false;
                }
            },

            initMap() {
                const defaultLat = parseFloat(this.gpsLat) || 6.1254; // Kota Bharu center
                const defaultLng = parseFloat(this.gpsLng) || 102.2381;

                map = L.map('map').setView([defaultLat, defaultLng], 12);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap JPVNK'
                }).addTo(map);

                marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

                marker.on('dragend', (e) => {
                    const pos = e.target.getLatLng();
                    this.updateGps(pos.lat, pos.lng);
                });

                map.on('click', (e) => {
                    marker.setLatLng(e.latlng);
                    this.updateGps(e.latlng.lat, e.latlng.lng);
                });
            },

            updateGps(lat, lng) {
                this.gpsLat = lat.toFixed(6);
                this.gpsLng = lng.toFixed(6);
                this.formData.gps_latitud = this.gpsLat;
                this.formData.gps_longitud = this.gpsLng;
            },

            detectLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            this.updateGps(lat, lng);
                            map.setView([lat, lng], 15);
                            marker.setLatLng([lat, lng]);
                        },
                        (error) => {
                            alert("Tidak dapat mengesan lokasi semasa. Sila tandakan pin secara manual pada peta.");
                        }
                    );
                } else {
                    alert("Pelayar anda tidak menyokong pengesanan geolokasi.");
                }
            },

            initSignature() {
                const canvas = document.getElementById('signatureCanvas');
                if (!canvas) return;

                // Adjust resolution for Retina / High-DPI screens
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);

                signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgb(255, 255, 255)',
                    penColor: 'rgb(15, 23, 42)'
                });
            },

            clearSignature() {
                if (signaturePad) {
                    signaturePad.clear();
                }
            },

            getRowTotal(baka) {
                const row = this.stok[baka];
                if (!row) return 0;
                return (Number(row.betina_anak) || 0) +
                       (Number(row.betina_dara) || 0) +
                       (Number(row.betina_induk) || 0) +
                       (Number(row.jantan_anak) || 0) +
                       (Number(row.jantan_pejantan) || 0);
            },

            getColTotal(col) {
                let total = 0;
                for (const baka in this.stok) {
                    total += (Number(this.stok[baka][col]) || 0);
                }
                return total;
            },

            getGrandTotal() {
                let total = 0;
                for (const baka in this.stok) {
                    total += this.getRowTotal(baka);
                }
                return total;
            },

            prepareSubmission(e) {
                if (signaturePad && !signaturePad.isEmpty()) {
                    document.getElementById('signatureData').value = signaturePad.toDataURL();
                }
            }
        };
    }
</script>
@endpush