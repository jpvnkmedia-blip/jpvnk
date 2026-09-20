@extends('layouts.app')

@section('title', 'Kemaskini Permohonan Ladang Bridlot - ' . $application->no_rujukan)

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
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="applicationEditForm()">

    <!-- Form Header Banner -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-emerald-800 via-emerald-700 to-teal-800 p-6 sm:p-8 text-white relative">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 rounded-2xl bg-white p-1 flex items-center justify-center shadow-md">
                        <img src="{{ asset('images/naimbif-logo.png') }}" alt="Logo NAIMbif" class="h-14 w-auto object-contain">
                    </div>
                    <div>
                        <div class="text-xs uppercase font-extrabold tracking-widest text-amber-300">Kemaskini Maklumat Permohonan</div>
                        <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white mt-1">
                            NO. PERMOHONAN: <span class="text-amber-300 font-mono">{{ $application->no_rujukan }}</span>
                        </h1>
                        <p class="text-xs text-emerald-100 mt-1">Sila kemas kini maklumat yang perlu dipinda dan klik butang simpan di bawah.</p>
                    </div>
                </div>

                <div class="text-right sm:text-right">
                    <a href="{{ route('naimbif.public.check_status', ['carian' => $application->no_rujukan]) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-semibold backdrop-blur-sm border border-white/20 transition-colors">
                        <i class="fas fa-arrow-left mr-1.5"></i> Kembali ke Status
                    </a>
                </div>
            </div>
        </div>

        @if($application->status_negeri === 'Lulus')
            <div class="p-4 bg-emerald-50 border-b border-emerald-200 text-emerald-900 text-xs flex items-center space-x-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-200 text-emerald-800 flex items-center justify-center text-sm flex-shrink-0">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <div class="font-bold">Permohonan Ini Telah Diluluskan Rasmi (No. Rujukan Kelulusan: <span class="font-mono text-emerald-950 font-extrabold">{{ $application->no_rujukan_negeri ?: $application->no_rujukan }}</span>)</div>
                    <div class="text-[11px] text-emerald-700 mt-0.5">Anda dibenarkan mengemas kini data penternak, ladang, atau matriks bilangan lembu semasa. Status kelulusan rasmi akan kekal terpelihara.</div>
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
    <form action="{{ route('naimbif.public.update', $application->no_rujukan) }}" method="POST" id="permohonanForm" @submit="prepareSubmission">
        @csrf
        @method('PUT')

        <!-- ========================================== -->
        <!-- SEKSYEN 1: MAKLUMAT PESERTA                -->
        <!-- ========================================== -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8 mb-8">
            <div class="flex items-center space-x-3 pb-4 mb-6 border-b border-slate-200">
                <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 font-extrabold flex items-center justify-center text-sm">1</span>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">MAKLUMAT PESERTA</h2>
                    <p class="text-xs text-slate-500">Butiran peribadi penternak / pemohon</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- 1. Nama -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        1. NAMA PENUH <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nama" value="{{ old('nama', $application->nama) }}" required placeholder="Contoh: WAN MUHAMMAD AZLAN BIN WAN HASSAN"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm uppercase">
                    @error('nama') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- 2. No Kad Pengenalan -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        2. NO. KAD PENGENALAN <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="no_kp" value="{{ old('no_kp', $application->no_kp) }}" required placeholder="Contoh: 850712035411" maxlength="14"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm font-mono">
                    @error('no_kp') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- 3. No Telefon -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        3. NO. TELEFON (BIMBIT / RUMAH) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="no_telefon" value="{{ old('no_telefon', $application->no_telefon) }}" required placeholder="Contoh: 019-9123456"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm font-mono">
                    @error('no_telefon') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- 4. Alamat Tetap -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        4. ALAMAT TETAP <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="alamat_tetap" rows="2" required placeholder="Alamat surat-menyurat kediaman pemohon..."
                              class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">{{ old('alamat_tetap', $application->alamat_tetap) }}</textarea>
                    @error('alamat_tetap') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Poskod & Jajahan -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        POSKOD <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="poskod" value="{{ old('poskod', $application->poskod) }}" required placeholder="Contoh: 15150" maxlength="5"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm font-mono">
                    @error('poskod') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        JAJAHAN PEMOHON <span class="text-rose-500">*</span>
                    </label>
                    <select name="jajahan" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm bg-white">
                        <option value="">-- Pilih Jajahan --</option>
                        @foreach($jajahans as $jajahan)
                            <option value="{{ $jajahan }}" {{ old('jajahan', $application->jajahan) == $jajahan ? 'selected' : '' }}>
                                {{ $jajahan }}
                            </option>
                        @endforeach
                    </select>
                    @error('jajahan') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- 5. Pengalaman Menternak -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        5. PENGALAMAN MENTERNAK (TAHUN) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" name="pengalaman_menternak" value="{{ old('pengalaman_menternak', $application->pengalaman_menternak) }}" min="0" max="80" required placeholder="0"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm font-mono pr-14">
                        <span class="absolute right-4 top-2.5 text-xs text-slate-400 font-semibold">Tahun</span>
                    </div>
                    @error('pengalaman_menternak') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- 6. Status Penternakan -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        6. STATUS PENTERNAKAN <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3 pt-1">
                        <label class="flex items-center p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition-colors"
                               :class="statusPenternakan === 'Sepenuh Masa' ? 'border-emerald-500 bg-emerald-50/50' : ''">
                            <input type="radio" name="status_penternakan" value="Sepenuh Masa" x-model="statusPenternakan"
                                   class="text-emerald-600 focus:ring-emerald-500">
                            <span class="ml-2 text-xs font-bold text-slate-800">Sepenuh Masa</span>
                        </label>
                        <label class="flex items-center p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition-colors"
                               :class="statusPenternakan === 'Sampingan' ? 'border-emerald-500 bg-emerald-50/50' : ''">
                            <input type="radio" name="status_penternakan" value="Sampingan" x-model="statusPenternakan"
                                   class="text-emerald-600 focus:ring-emerald-500">
                            <span class="ml-2 text-xs font-bold text-slate-800">Sampingan</span>
                        </label>
                    </div>
                </div>

                <!-- 7. Pernah Hadir Kursus -->
                <div class="md:col-span-2 pt-2 border-t border-slate-100">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        7. PERNAH HADIR KURSUS PENTERNAKAN LEMBU PEDAGING? <span class="text-rose-500">*</span>
                    </label>
                    <div class="flex items-center space-x-6">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="pernah_kursus" value="1" x-model="pernahKursus" class="text-emerald-600 focus:ring-emerald-500">
                            <span class="ml-2 text-sm font-semibold text-slate-800">Ya, Pernah</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="pernah_kursus" value="0" x-model="pernahKursus" class="text-emerald-600 focus:ring-emerald-500">
                            <span class="ml-2 text-sm font-semibold text-slate-800">Tidak Pernah</span>
                        </label>
                    </div>

                    <!-- Jika Ya -->
                    <div x-show="pernahKursus == '1'" x-transition class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4 p-4 rounded-xl bg-emerald-50/50 border border-emerald-200">
                        <div>
                            <label class="block text-xs font-bold uppercase text-emerald-900 mb-1">Nama Kursus</label>
                            <input type="text" name="nama_kursus" value="{{ old('nama_kursus', $application->nama_kursus) }}" placeholder="Contoh: Kursus Pembiakan & Pengurusan Bridlot"
                                   class="w-full px-3 py-2 rounded-lg border border-emerald-300 focus:ring-emerald-500 text-xs bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-emerald-900 mb-1">Anjuran Agensi / Jabatan</label>
                            <input type="text" name="anjuran_kursus" value="{{ old('anjuran_kursus', $application->anjuran_kursus) }}" placeholder="Contoh: JPV Kelantan / MARDI"
                                   class="w-full px-3 py-2 rounded-lg border border-emerald-300 focus:ring-emerald-500 text-xs bg-white">
                        </div>
                    </div>

                    <!-- Jika Tidak -->
                    <div x-show="pernahKursus == '0'" x-transition class="mt-4 p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="berminat_kursus_jpvnk" value="1" {{ old('berminat_kursus_jpvnk', $application->berminat_kursus_jpvnk) ? 'checked' : '' }}
                                   class="rounded text-emerald-600 focus:ring-emerald-500">
                            <span class="ml-2 text-xs font-semibold text-slate-700">Saya berminat menyertai kursus bimbingan anjuran JPVNK sekiranya ditawarkan.</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SEKSYEN 2: MAKLUMAT PROJEK / LADANG        -->
        <!-- ========================================== -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8 mb-8">
            <div class="flex items-center space-x-3 pb-4 mb-6 border-b border-slate-200">
                <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 font-extrabold flex items-center justify-center text-sm">2</span>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">MAKLUMAT PROJEK & PREMIS LADANG</h2>
                    <p class="text-xs text-slate-500">Lokasi fizikal kandang, tanah ragut dan koordinat GPS</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Alamat Ladang -->
                <div class="md:col-span-2">
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                            1. ALAMAT LOKASI KANDANG / LADANG
                        </label>
                        <button type="button" @click="copyAlamatTetap" class="text-xs text-emerald-700 hover:text-emerald-800 font-semibold flex items-center">
                            <i class="fas fa-copy mr-1"></i> Sama seperti alamat kediaman
                        </button>
                    </div>
                    <textarea name="alamat_ladang" x-model="alamatLadang" rows="2" placeholder="Alamat fizikal tapak projek..."
                              class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm"></textarea>
                </div>

                <!-- Poskod & Jajahan Ladang -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        POSKOD LADANG
                    </label>
                    <input type="text" name="poskod_ladang" x-model="poskodLadang" placeholder="Contoh: 15150" maxlength="5"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        JAJAHAN LOKASI PROJEK <span class="text-rose-500">*</span>
                    </label>
                    <select name="jajahan_ladang" x-model="jajahanLadang" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm bg-white">
                        <option value="">-- Pilih Jajahan --</option>
                        @foreach($jajahans as $jajahan)
                            <option value="{{ $jajahan }}">{{ $jajahan }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- GPS Coordinates & Leaflet Map Picker -->
                <div class="md:col-span-2 pt-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        2. LOKASI GPS KOORDINAT PREMIS (PETA INTERAKTIF)
                    </label>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <div class="lg:col-span-2 rounded-xl overflow-hidden border border-slate-300 shadow-inner">
                            <div id="map"></div>
                            <div class="p-2 bg-slate-50 border-t border-slate-200 text-[11px] text-slate-500 flex items-center justify-between">
                                <span><i class="fas fa-hand-pointer mr-1 text-emerald-600"></i> Klik atau heret penanda pada peta untuk mengubah lokasi.</span>
                                <button type="button" @click="getCurrentGpsLocation" class="text-emerald-700 font-bold hover:underline">
                                    <i class="fas fa-crosshairs mr-1"></i> Lokasi Semasa Saya
                                </button>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-0.5">Latitude (Lat)</label>
                                <input type="text" name="gps_latitud" x-model="gpsLat" readonly
                                       class="w-full px-3 py-2 rounded-lg border border-slate-300 bg-slate-50 font-mono text-xs text-slate-700">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-0.5">Longitude (Long)</label>
                                <input type="text" name="gps_longitud" x-model="gpsLong" readonly
                                       class="w-full px-3 py-2 rounded-lg border border-slate-300 bg-slate-50 font-mono text-xs text-slate-700">
                            </div>
                            <p class="text-[11px] text-slate-400 italic">
                                Koordinat GPS ini akan digunakan oleh Pegawai Veterinar Jajahan semasa lawatan siasatan premis.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 3. Status Tanah -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        3. STATUS TANAH <span class="text-rose-500">*</span>
                    </label>
                    <select name="status_tanah" x-model="statusTanah" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-sm bg-white">
                        <option value="Sendiri">Hak Milik Sendiri</option>
                        <option value="Sewa">Sewa / Pajakan</option>
                        <option value="Kerajaan">Rizab / Tanah Kerajaan</option>
                        <option value="Lain-lain">Lain-lain Status</option>
                    </select>

                    <div x-show="statusTanah === 'Lain-lain'" class="mt-2">
                        <input type="text" name="status_tanah_lain" value="{{ old('status_tanah_lain', $application->status_tanah_lain) }}" placeholder="Nyatakan status tanah..."
                               class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300">
                    </div>
                </div>

                <!-- 4. Keluasan Tanah -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        4. KELUASAN TANAH (EKAR) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" step="0.01" name="keluasan_tanah" value="{{ old('keluasan_tanah', $application->keluasan_tanah) }}" min="0.1" required placeholder="Contoh: 3.5"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm font-mono pr-14">
                        <span class="absolute right-4 top-2.5 text-xs text-slate-400 font-semibold">Ekar</span>
                    </div>
                </div>

                <!-- 5. Padang Ragut -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        5. PADANG RAGUT / KAWASAN RAGUTAN <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3 pt-1">
                        <label class="flex items-center p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50">
                            <input type="radio" name="padang_ragut" value="Ada" {{ old('padang_ragut', $application->padang_ragut) === 'Ada' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                            <span class="ml-2 text-xs font-bold text-slate-800">Ada Padang Ragut</span>
                        </label>
                        <label class="flex items-center p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50">
                            <input type="radio" name="padang_ragut" value="Tiada" {{ old('padang_ragut', $application->padang_ragut) === 'Tiada' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                            <span class="ml-2 text-xs font-bold text-slate-800">Tiada (Feedlot Penuh)</span>
                        </label>
                    </div>
                </div>

                <!-- 6. Bilangan Pekerja -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        6. BILANGAN PEKERJA LADANG <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" name="bilangan_pekerja" value="{{ old('bilangan_pekerja', $application->bilangan_pekerja) }}" min="0" required placeholder="0"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm font-mono pr-14">
                        <span class="absolute right-4 top-2.5 text-xs text-slate-400 font-semibold">Orang</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SEKSYEN 3: MAKLUMAT TERNAKAN & BAKA        -->
        <!-- ========================================== -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8 mb-8">
            <div class="flex items-center space-x-3 pb-4 mb-6 border-b border-slate-200">
                <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 font-extrabold flex items-center justify-center text-sm">3</span>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">MAKLUMAT TERNAKAN SEMASA</h2>
                    <p class="text-xs text-slate-500">Punca bekalan, kaedah pembiakan dan inventori baka lembu</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- 1. Punca Ternakan -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        1. PUNCA TERNAKAN <span class="text-rose-500">*</span>
                    </label>
                    <select name="punca_ternakan" x-model="puncaTernakan" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 text-sm bg-white">
                        <option value="Beli">Beli Sendiri</option>
                        <option value="Pawah">Pawah / Bantuan Agensi</option>
                        <option value="Lain-lain">Lain-lain Punca</option>
                    </select>

                    <div x-show="puncaTernakan === 'Lain-lain'" class="mt-2">
                        <input type="text" name="punca_ternakan_lain" value="{{ old('punca_ternakan_lain', $application->punca_ternakan_lain) }}" placeholder="Nyatakan punca ternakan..."
                               class="w-full px-3 py-2 text-xs rounded-lg border border-slate-300">
                    </div>
                </div>

                <!-- 2. Kaedah Pembiakan -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        2. KAEDAH PEMBIAKAN DIAPLIKASIKAN <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3 pt-1">
                        <label class="flex items-center p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50">
                            <input type="radio" name="kaedah_pembiakan" value="Asli" {{ old('kaedah_pembiakan', $application->kaedah_pembiakan) === 'Asli' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                            <span class="ml-2 text-xs font-bold text-slate-800">Semulajadi (Asli)</span>
                        </label>
                        <label class="flex items-center p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50">
                            <input type="radio" name="kaedah_pembiakan" value="Permanian Beradas" {{ old('kaedah_pembiakan', $application->kaedah_pembiakan) === 'Permanian Beradas' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                            <span class="ml-2 text-xs font-bold text-slate-800">Permanian Beradas (AI)</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Matriks Stok Ternakan -->
            <div class="pt-4 border-t border-slate-100">
                <div class="flex items-center justify-between mb-3">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        3. BILANGAN TERNAKAN MENGIKUT BAKA & JANTINA
                    </label>
                    <span class="text-xs text-emerald-700 font-bold bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                        Jumlah Keseluruhan: <span x-text="grandTotal" class="font-mono">0</span> Ekor
                    </span>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-xs">
                        <thead>
                            <tr class="bg-slate-800 text-white text-center font-bold">
                                <th rowspan="2" class="p-3 text-left uppercase border-r border-slate-700 w-44">BAKA</th>
                                <th colspan="3" class="p-2 uppercase bg-pink-900/60 border-r border-slate-700">BETINA</th>
                                <th colspan="2" class="p-2 uppercase bg-blue-900/60 border-r border-slate-700">JANTAN</th>
                                <th rowspan="2" class="p-3 uppercase bg-emerald-900/70 w-24">JUMLAH (EKOR)</th>
                            </tr>
                            <tr class="bg-slate-700 text-slate-200 text-center font-semibold text-[11px]">
                                <th class="p-2 border-r border-slate-600">ANAK</th>
                                <th class="p-2 border-r border-slate-600">DARA</th>
                                <th class="p-2 border-r border-slate-600">INDUK</th>
                                <th class="p-2 border-r border-slate-600">ANAK JANTAN</th>
                                <th class="p-2 border-r border-slate-600">PEJANTAN</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach($bakas as $bIndex => $baka)
                                @php $bKey = \Illuminate\Support\Str::slug($baka, '_'); @endphp
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="p-2.5 font-bold text-slate-900 border-r border-slate-200 bg-slate-50/50">
                                        {{ $baka }}
                                        @if($baka === 'LAIN-LAIN')
                                            <input type="text" name="stok[{{ $baka }}][nama_baka_lain]"
                                                   x-model="stok.{{ $bKey }}.nama_baka_lain"
                                                   placeholder="Nyatakan baka lain..."
                                                   class="w-full mt-1.5 px-2 py-1 text-[11px] rounded border border-slate-300">
                                        @endif
                                    </td>
                                    <td class="p-2 border-r border-slate-200">
                                        <input type="number" min="0" name="stok[{{ $baka }}][betina_anak]"
                                               x-model.number="stok.{{ $bKey }}.betina_anak"
                                               @input="calculateTotals"
                                               class="table-input">
                                    </td>
                                    <td class="p-2 border-r border-slate-200">
                                        <input type="number" min="0" name="stok[{{ $baka }}][betina_dara]"
                                               x-model.number="stok.{{ $bKey }}.betina_dara"
                                               @input="calculateTotals"
                                               class="table-input">
                                    </td>
                                    <td class="p-2 border-r border-slate-200">
                                        <input type="number" min="0" name="stok[{{ $baka }}][betina_induk]"
                                               x-model.number="stok.{{ $bKey }}.betina_induk"
                                               @input="calculateTotals"
                                               class="table-input">
                                    </td>
                                    <td class="p-2 border-r border-slate-200">
                                        <input type="number" min="0" name="stok[{{ $baka }}][jantan_anak]"
                                               x-model.number="stok.{{ $bKey }}.jantan_anak"
                                               @input="calculateTotals"
                                               class="table-input">
                                    </td>
                                    <td class="p-2 border-r border-slate-200">
                                        <input type="number" min="0" name="stok[{{ $baka }}][jantan_pejantan]"
                                               x-model.number="stok.{{ $bKey }}.jantan_pejantan"
                                               @input="calculateTotals"
                                               class="table-input">
                                    </td>
                                    <td class="p-2.5 text-center font-bold text-slate-800 bg-emerald-50/50 font-mono text-sm">
                                        <span x-text="rowTotals.{{ $bKey }} || 0">0</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-900 text-white font-black text-center text-xs">
                                <td class="p-3 text-left uppercase border-r border-slate-800">JUMLAH KESELURUHAN</td>
                                <td class="p-2 border-r border-slate-800 text-pink-300 font-mono" x-text="colTotals.betina_anak">0</td>
                                <td class="p-2 border-r border-slate-800 text-pink-300 font-mono" x-text="colTotals.betina_dara">0</td>
                                <td class="p-2 border-r border-slate-800 text-pink-300 font-mono" x-text="colTotals.betina_induk">0</td>
                                <td class="p-2 border-r border-slate-800 text-blue-300 font-mono" x-text="colTotals.jantan_anak">0</td>
                                <td class="p-2 border-r border-slate-800 text-blue-300 font-mono" x-text="colTotals.jantan_pejantan">0</td>
                                <td class="p-3 bg-amber-500 text-slate-950 font-black font-mono text-sm" x-text="grandTotal">0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SEKSYEN 4: TANDATANGAN & PENGESAHAN        -->
        <!-- ========================================== -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8 mb-8">
            <div class="flex items-center space-x-3 pb-4 mb-6 border-b border-slate-200">
                <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 font-extrabold flex items-center justify-center text-sm">4</span>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">PENGESAHAN & TANDATANGAN</h2>
                    <p class="text-xs text-slate-500">Perakuan pemohon bagi permohonan yang dikemas kini</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        TANDATANGAN PEMOHON
                    </label>

                    @if($application->tandatangan)
                        <div class="mb-3 p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between" x-show="!showNewSignature">
                            <div>
                                <span class="text-[11px] font-bold text-slate-600 block mb-1">Tandatangan Sedia Ada:</span>
                                <img src="{{ $application->tandatangan }}" alt="Tandatangan Sedia Ada" class="h-12 border border-slate-300 bg-white p-1 rounded">
                            </div>
                            <button type="button" @click="showNewSignature = true; $nextTick(() => initSignaturePad())" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700">
                                <i class="fas fa-pen mr-1"></i> Tandatangan Semula
                            </button>
                        </div>
                    @endif

                    <div x-show="showNewSignature || !'{{ $application->tandatangan }}'" class="border-2 border-dashed border-slate-300 rounded-xl p-3 bg-slate-50">
                        <canvas id="signature-pad" class="w-full h-32 bg-white rounded-lg border border-slate-200 cursor-crosshair touch-none"></canvas>
                        <div class="flex items-center justify-between mt-2 text-xs">
                            <span class="text-slate-400 text-[11px]"><i class="fas fa-pen mr-1"></i> Tandatangan menggunakan tetikus / jari di atas kotak.</span>
                            <button type="button" @click="clearSignature" class="px-2.5 py-1 text-slate-600 bg-slate-200 hover:bg-slate-300 rounded font-semibold text-[11px]">
                                <i class="fas fa-undo mr-1"></i> Padam
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="tandatangan" id="tandatanganInput" x-model="signatureData">
                </div>

                <div class="flex flex-col justify-between">
                    <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900">
                        <div class="font-bold flex items-center mb-1">
                            <i class="fas fa-info-circle mr-1.5"></i> Maklumat Tambahan:
                        </div>
                        <p class="leading-relaxed">
                            Pindaan maklumat ini akan dikemas kini serta-merta dalam sistem pangkalan data dan boleh disemak semula oleh Pegawai Veterinar Jajahan.
                        </p>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-4 px-6 rounded-xl font-extrabold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 shadow-lg shadow-emerald-600/30 transition-all transform hover:-translate-y-0.5 flex items-center justify-center text-base">
                            <i class="fas fa-save mr-2 text-lg"></i> SIMPAN PERUBAHAN PERMOHONAN
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- Signature Pad JS -->
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

<script>
function applicationEditForm() {
    return {
        statusPenternakan: '{{ old('status_penternakan', $application->status_penternakan) }}',
        pernahKursus: '{{ old('pernah_kursus', $application->pernah_kursus ? '1' : '0') }}',
        statusTanah: '{{ old('status_tanah', $application->status_tanah) }}',
        puncaTernakan: '{{ old('punca_ternakan', $application->punca_ternakan) }}',
        
        alamatTetap: @json(old('alamat_tetap', $application->alamat_tetap)),
        poskodTetap: @json(old('poskod', $application->poskod)),
        jajahanTetap: @json(old('jajahan', $application->jajahan)),
        alamatLadang: @json(old('alamat_ladang', $application->alamat_ladang)),
        poskodLadang: @json(old('poskod_ladang', $application->poskod_ladang)),
        jajahanLadang: @json(old('jajahan_ladang', $application->jajahan_ladang)),

        gpsLat: '{{ old('gps_latitud', $application->gps_latitud ?: '6.1254') }}',
        gpsLong: '{{ old('gps_longitud', $application->gps_longitud ?: '102.2381') }}',

        map: null,
        marker: null,
        signaturePad: null,
        signatureData: '',
        showNewSignature: {{ empty($application->tandatangan) ? 'true' : 'false' }},

        stok: {
            @foreach($bakas as $baka)
                @php $bKey = \Illuminate\Support\Str::slug($baka, '_'); @endphp
                '{{ $bKey }}': {
                    betina_anak: {{ old("stok.{$baka}.betina_anak", isset($inventories[$baka]) ? $inventories[$baka]->betina_anak : 0) }},
                    betina_dara: {{ old("stok.{$baka}.betina_dara", isset($inventories[$baka]) ? $inventories[$baka]->betina_dara : 0) }},
                    betina_induk: {{ old("stok.{$baka}.betina_induk", isset($inventories[$baka]) ? $inventories[$baka]->betina_induk : 0) }},
                    jantan_anak: {{ old("stok.{$baka}.jantan_anak", isset($inventories[$baka]) ? $inventories[$baka]->jantan_anak : 0) }},
                    jantan_pejantan: {{ old("stok.{$baka}.jantan_pejantan", isset($inventories[$baka]) ? $inventories[$baka]->jantan_pejantan : 0) }},
                    nama_baka_lain: @json(old("stok.{$baka}.nama_baka_lain", isset($inventories[$baka]) ? $inventories[$baka]->nama_baka_lain : ''))
                },
            @endforeach
        },

        rowTotals: {},
        colTotals: { betina_anak: 0, betina_dara: 0, betina_induk: 0, jantan_anak: 0, jantan_pejantan: 0 },
        grandTotal: 0,

        init() {
            this.initMap();
            this.calculateTotals();
            if (this.showNewSignature) {
                this.$nextTick(() => {
                    this.initSignaturePad();
                });
            }
        },

        initMap() {
            const lat = parseFloat(this.gpsLat) || 6.1254;
            const lng = parseFloat(this.gpsLong) || 102.2381;

            this.map = L.map('map').setView([lat, lng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(this.map);

            this.marker = L.marker([lat, lng], { draggable: true }).addTo(this.map);

            this.marker.on('dragend', (e) => {
                const pos = e.target.getLatLng();
                this.gpsLat = pos.lat.toFixed(6);
                this.gpsLong = pos.lng.toFixed(6);
            });

            this.map.on('click', (e) => {
                this.marker.setLatLng(e.latlng);
                this.gpsLat = e.latlng.lat.toFixed(6);
                this.gpsLong = e.latlng.lng.toFixed(6);
            });
        },

        getCurrentGpsLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        const lat = pos.coords.latitude;
                        const lng = pos.coords.longitude;
                        this.gpsLat = lat.toFixed(6);
                        this.gpsLong = lng.toFixed(6);
                        this.map.setView([lat, lng], 15);
                        this.marker.setLatLng([lat, lng]);
                    },
                    (err) => {
                        alert('Gagal mendapatkan lokasi GPS automatik: ' + err.message + '. Sila klik pada peta secara manual.');
                    }
                );
            }
        },

        copyAlamatTetap() {
            this.alamatLadang = this.alamatTetap;
            this.poskodLadang = this.poskodTetap;
            this.jajahanLadang = this.jajahanTetap;
        },

        initSignaturePad() {
            const canvas = document.getElementById('signature-pad');
            if (canvas) {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                this.signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgb(255, 255, 255)',
                    penColor: 'rgb(15, 23, 42)'
                });
            }
        },

        clearSignature() {
            if (this.signaturePad) {
                this.signaturePad.clear();
                this.signatureData = '';
            }
        },

        calculateTotals() {
            this.colTotals = { betina_anak: 0, betina_dara: 0, betina_induk: 0, jantan_anak: 0, jantan_pejantan: 0 };
            let totalAll = 0;

            for (const baka in this.stok) {
                const item = this.stok[baka];
                const bAnak = parseInt(item.betina_anak) || 0;
                const bDara = parseInt(item.betina_dara) || 0;
                const bInduk = parseInt(item.betina_induk) || 0;
                const jAnak = parseInt(item.jantan_anak) || 0;
                const jPej = parseInt(item.jantan_pejantan) || 0;

                const rowSum = bAnak + bDara + bInduk + jAnak + jPej;
                this.rowTotals[baka] = rowSum;

                this.colTotals.betina_anak += bAnak;
                this.colTotals.betina_dara += bDara;
                this.colTotals.betina_induk += bInduk;
                this.colTotals.jantan_anak += jAnak;
                this.colTotals.jantan_pejantan += jPej;

                totalAll += rowSum;
            }

            this.grandTotal = totalAll;
        },

        prepareSubmission(e) {
            if (this.showNewSignature && this.signaturePad && !this.signaturePad.isEmpty()) {
                this.signatureData = this.signaturePad.toDataURL();
            }
        }
    }
}
</script>
@endpush
