@extends('layouts.app')

@section('title', 'Isi Borang Action List (PK-RK-61) - JPVNK')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gradient-to-r from-emerald-900 via-teal-900 to-slate-900 p-6 rounded-3xl text-white shadow-xl">
        <div class="space-y-1">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold border border-amber-500/30">
                <i class="fa-solid fa-file-signature"></i> BORANG ACTION LIST (PK-RK-61)
            </div>
            <h1 class="text-2xl font-black">Pejabat Perkhidmatan Veterinar Jajahan</h1>
            <p class="text-xs text-slate-300">
                Borang pintar tindakan perkhidmatan lapangan, klinik, pemantauan pawah & semakan pangkalan data bersepadu.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('action-list.index') }}" class="px-4 py-2 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition flex items-center gap-1.5 border border-white/20">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Senarai
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold space-y-1">
            <div class="flex items-center gap-2 text-sm font-black"><i class="fa-solid fa-triangle-exclamation"></i> Sila semak maklumat yang dimasukkan:</div>
            <ul class="list-disc list-inside pl-2 space-y-0.5 font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('action-list.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="actionListForm">
        @csrf

        <!-- HEADER DOKUMEN BORANG -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="font-black text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-stamp text-emerald-600"></i> MAKLUMAT DOKUMEN & PEJABAT JAJAHAN
                </div>
                <div class="font-mono text-xs font-black text-slate-400">PK-RK-61</div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Kod Dokumen</label>
                    <input type="text" name="kod_dokumen" value="PK-RK-61" readonly class="w-full py-2 px-3 rounded-xl bg-slate-100 border border-slate-200 font-mono font-bold text-slate-600">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">No. Siri / BIL <span class="text-rose-500">*</span></label>
                    <input type="text" name="no_bil" value="{{ old('no_bil', $proposedNoBil) }}" required class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 font-mono font-bold text-emerald-700">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Pejabat Jajahan <span class="text-rose-500">*</span></label>
                    <select name="jajahan" id="jajahanSelect" required class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 font-bold text-slate-800">
                        @foreach($jajahanList as $jjh)
                            <option value="{{ $jjh }}" {{ old('jajahan', $defaultJajahan) === $jjh ? 'selected' : '' }}>Jajahan {{ $jjh }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Borang <span class="text-rose-500">*</span></label>
                    <input type="date" name="tarikh" value="{{ old('tarikh', date('Y-m-d')) }}" required class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 font-semibold text-slate-800">
                </div>
            </div>
        </div>

        <!-- A. MAKLUMAT PELANGGAN -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 pb-3 gap-2">
                <div class="font-black text-slate-900 text-sm flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">A</span>
                    MAKLUMAT PELANGGAN
                </div>
                <div class="flex items-center gap-4 text-xs">
                    <label class="inline-flex items-center gap-1.5 cursor-pointer">
                        <input type="radio" name="kategori_pelanggan" id="kategoriIndividu" value="Individu" {{ old('kategori_pelanggan', 'Individu') === 'Individu' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                        <span class="font-bold text-slate-700">INDIVIDU</span>
                    </label>
                    <label class="inline-flex items-center gap-1.5 cursor-pointer">
                        <input type="radio" name="kategori_pelanggan" id="kategoriSyarikat" value="Syarikat" {{ old('kategori_pelanggan') === 'Syarikat' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                        <span class="font-bold text-slate-700">SYARIKAT</span>
                    </label>
                </div>
            </div>

            <!-- Semakan No. Kad Pengenalan Pintar -->
            <div class="p-4 rounded-2xl bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 space-y-2">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <label class="block font-black text-emerald-950 text-xs uppercase flex items-center gap-1.5">
                        <i class="fa-solid fa-id-card text-emerald-600"></i> Semak No. Kad Pengenalan Pelanggan (Autolengkap):
                    </label>
                    <span class="text-[11px] text-emerald-700 font-medium">Masukkan No. K/P tanpa sengkang untuk semakan data EPTR, EPU, Klinik & Pawah</span>
                </div>
                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="relative flex-1">
                        <input type="text" id="icLookupInput" name="no_kp" value="{{ old('no_kp', $prefillUser->ic_number ?? '') }}" placeholder="cth: 900729035413 atau 900729-03-5413" class="w-full py-2.5 pl-9 pr-3 text-xs rounded-xl border border-emerald-300 focus:outline-emerald-600 font-mono font-bold text-emerald-950 bg-white">
                        <i class="fa-solid fa-search absolute left-3 top-3.5 text-emerald-500 text-xs"></i>
                    </div>
                    <button type="button" id="btnSemakIc" onclick="semakPelangganLengkap()" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition flex items-center justify-center gap-2">
                        <span id="btnSemakText"><i class="fa-solid fa-bolt"></i> Semak & Autolengkap</span>
                        <i id="btnSemakSpinner" class="fa-solid fa-spinner fa-spin hidden"></i>
                    </button>
                </div>
                <div id="semakStatusAlert" class="hidden text-xs font-semibold p-2.5 rounded-xl"></div>
            </div>

            <input type="hidden" name="user_id" id="userIdInput" value="{{ old('user_id', $prefillUser->id ?? '') }}">
            <input type="hidden" name="temujanji_id" id="temujanjiIdInput" value="{{ $temujanji->id ?? '' }}">
            <input type="hidden" name="pawah_perjanjian_id" id="pawahPerjanjianIdInput" value="{{ old('pawah_perjanjian_id') }}">
            <div id="hiddenTernakanInputs"></div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs pt-1">
                <div class="lg:col-span-2">
                    <label class="block font-bold text-slate-700 uppercase mb-1">1. Nama Pelanggan / Syarikat <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_pelanggan" id="namaInput" value="{{ old('nama_pelanggan', $prefillUser->name ?? '') }}" required placeholder="cth: Ahmad bin Abdullah / Syarikat Ternakan Maju Sdn Bhd" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 font-bold text-slate-800">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Masa Pendaftaran</label>
                    <input type="text" name="masa_pendaftaran" value="{{ old('masa_pendaftaran', date('h:i A')) }}" placeholder="cth: 09:30 AM" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">6. No. Telefon</label>
                    <input type="text" name="telefon" id="phoneInput" value="{{ old('telefon', $prefillUser->phone ?? '') }}" placeholder="cth: 019-9876543" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">20. No. Rujukan Fail</label>
                    <input type="text" name="no_rujukan" id="noRujukanInput" value="{{ old('no_rujukan') }}" placeholder="cth: JPVNK/PP/RAW/2026/012" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 font-mono text-slate-700">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">4. Mukim</label>
                    <input type="text" name="mukim" id="mukimInput" value="{{ old('mukim') }}" placeholder="cth: Padang Pak Amat" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>

                <div class="lg:col-span-3">
                    <label class="block font-bold text-slate-700 uppercase mb-1">3. Alamat Lengkap</label>
                    <textarea name="alamat" id="addressInput" rows="2" placeholder="Alamat premis / kediaman / ladang penternak..." class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">{{ old('alamat', $prefillUser->address ?? '') }}</textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Poskod</label>
                    <input type="text" name="poskod" id="poskodInput" value="{{ old('poskod', $prefillUser->poskod ?? '16800') }}" placeholder="16800" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 font-mono text-slate-700">
                </div>

                <div class="lg:col-span-2">
                    <label class="block font-bold text-slate-700 uppercase mb-1">5. Daerah / Jajahan</label>
                    <input type="text" name="daerah" id="daerahInput" value="{{ old('daerah', 'Jajahan ' . $defaultJajahan . ', Kelantan') }}" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>
            </div>
        </div>

        <!-- PANEL MAKLUMAT BERSEPADU (EPTR, EPU, KLINIK & PAWAH) -->
        <div id="panelBersepaduContainer" class="hidden space-y-4">
            
            <!-- EPTR: Senarai Ternakan Berdaftar (Kad Kuning / Tag Telinga) -->
            <div id="panelEptr" class="hidden bg-white p-6 rounded-3xl border border-amber-200 shadow-xs space-y-3">
                <div class="flex items-center justify-between border-b border-amber-100 pb-2">
                    <div class="font-black text-amber-950 text-xs flex items-center gap-2">
                        <i class="fa-solid fa-cow text-amber-600"></i>
                        <span>EPTR: Senarai Ternakan Ruminan Berdaftar (Klik untuk pilih ternakan yang dirawat):</span>
                    </div>
                    <span id="eptrCountBadge" class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 font-mono font-bold text-[11px]">0 Ekor</span>
                </div>
                <div id="eptrListContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5 text-xs max-h-64 overflow-y-auto pr-1"></div>
            </div>

            <!-- EPU: Ladang Unggas Berdaftar -->
            <div id="panelEpu" class="hidden bg-white p-6 rounded-3xl border border-blue-200 shadow-xs space-y-3">
                <div class="flex items-center justify-between border-b border-blue-100 pb-2">
                    <div class="font-black text-blue-950 text-xs flex items-center gap-2">
                        <i class="fa-solid fa-feather text-blue-600"></i>
                        <span>EPU: Ladang Unggas Berdaftar Pelanggan:</span>
                    </div>
                    <span id="epuCountBadge" class="px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-900 font-mono font-bold text-[11px]">0 Ladang</span>
                </div>
                <div id="epuListContainer" class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs"></div>
            </div>

            <!-- PAWAH: Perjanjian Pawah Aktif -->
            <div id="panelPawah" class="hidden bg-white p-6 rounded-3xl border border-purple-200 shadow-xs space-y-3">
                <div class="flex items-center justify-between border-b border-purple-100 pb-2">
                    <div class="font-black text-purple-950 text-xs flex items-center gap-2">
                        <i class="fa-solid fa-handshake-angle text-purple-600"></i>
                        <span>PROGRAM PAWAH: Senarai Perjanjian Pawah Pelanggan:</span>
                    </div>
                    <span id="pawahCountBadge" class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-900 font-mono font-bold text-[11px]">0 Perjanjian</span>
                </div>
                <div id="pawahListContainer" class="space-y-2.5 text-xs"></div>
            </div>

            <!-- KLINIK: Sejarah Temujanji & Rawatan Lalu -->
            <div id="panelKlinik" class="hidden bg-white p-6 rounded-3xl border border-teal-200 shadow-xs space-y-3">
                <div class="flex items-center justify-between border-b border-teal-100 pb-2">
                    <div class="font-black text-teal-950 text-xs flex items-center gap-2">
                        <i class="fa-solid fa-notes-medical text-teal-600"></i>
                        <span>KLINIK: Rekod Temujanji & Rawatan Terdahulu:</span>
                    </div>
                    <span id="klinikCountBadge" class="px-2.5 py-0.5 rounded-full bg-teal-100 text-teal-900 font-mono font-bold text-[11px]">0 Rekod</span>
                </div>
                <div id="klinikListContainer" class="space-y-2 text-xs max-h-48 overflow-y-auto pr-1"></div>
            </div>
        </div>

        <!-- B. BUTIR-BUTIR PERKHIDMATAN -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
            <div class="font-black text-slate-900 text-sm flex items-center gap-2 border-b border-slate-100 pb-3">
                <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">B</span>
                BUTIR-BUTIR PERKHIDMATAN
            </div>
            <div class="text-xs">
                <label class="block font-bold text-slate-700 uppercase mb-1">7. Catatan Ringkas Perkhidmatan Yang Dipohon</label>
                <textarea name="catatan_perkhidmatan_dipohon" id="catatanPerkhidmatanInput" rows="2" placeholder="Nyatakan permohonan atau aduan penternak (cth: Lembu mengalami demam dan luka di kuku / Permohonan pemantauan pawah / Pemeriksaan kesihatan)..." class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">{{ old('catatan_perkhidmatan_dipohon', $temujanji->simptom_atau_tujuan ?? '') }}</textarea>
            </div>
        </div>

        <!-- C. MAKLUMAT TEMUJANJI & GPS -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
            <div class="font-black text-slate-900 text-sm flex items-center gap-2 border-b border-slate-100 pb-3">
                <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">C</span>
                MAKLUMAT TEMUJANJI & LOKASI
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">8. Nama Pegawai</label>
                    <input type="text" name="nama_pegawai" value="{{ old('nama_pegawai', Auth::user()->name) }}" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 font-semibold text-slate-800">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Masa Pegawai</label>
                    <input type="text" name="masa_pegawai" value="{{ old('masa_pegawai', date('h:i A')) }}" placeholder="08:30 AM" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">9. Masa Temujanji (Mula)</label>
                    <input type="text" name="masa_temujanji_mula" id="masaMulaInput" value="{{ old('masa_temujanji_mula', '09:00 AM') }}" placeholder="09:00 AM" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Masa Temujanji (Hingga)</label>
                    <input type="text" name="masa_temujanji_hingga" id="masaHinggaInput" value="{{ old('masa_temujanji_hingga', '11:00 AM') }}" placeholder="11:00 AM" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>

                <div class="lg:col-span-2">
                    <label class="block font-bold text-slate-700 uppercase mb-1">10. Maklumat Pelanggan (jika berlainan / wakil)</label>
                    <input type="text" name="maklumat_pelanggan_berlainan" value="{{ old('maklumat_pelanggan_berlainan') }}" placeholder="Nama wakil, no telefon atau hubungan dengan pemilik..." class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>

                <div class="lg:col-span-2">
                    <label class="block font-bold text-slate-700 uppercase mb-1">11. Maklumat Tambahan / Panduan Simpang</label>
                    <input type="text" name="maklumat_tambahan" value="{{ old('maklumat_tambahan') }}" placeholder="Penanda lokasi, simpang masuk ladang, pokok besar..." class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>

                <!-- GPS Koordinat Lokasi Pintar -->
                <div class="lg:col-span-2 bg-slate-50 p-3.5 rounded-2xl border border-slate-200 space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block font-bold text-slate-700 uppercase text-[11px] flex items-center gap-1.5">
                            <i class="fa-solid fa-location-dot text-rose-500"></i> GPS Koordinat Lokasi:
                        </label>
                        <button type="button" onclick="dapatkanGpsSemasa()" class="text-[11px] font-bold text-emerald-700 hover:text-emerald-800 flex items-center gap-1">
                            <i class="fa-solid fa-crosshairs"></i> Guna GPS Semasa
                        </button>
                    </div>
                    <div class="flex gap-2">
                        <input type="text" name="gps_koordinat" id="gpsKoordinatInput" value="{{ old('gps_koordinat') }}" placeholder="cth: 5.8392, 102.3941" class="w-full py-1.5 px-3 rounded-xl border border-slate-300 bg-white font-mono text-slate-800">
                        <button type="button" onclick="bukaGoogleMaps()" class="px-3 py-1.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs whitespace-nowrap flex items-center gap-1">
                            <i class="fa-solid fa-map-location-dot"></i> Peta
                        </button>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <label class="block font-bold text-slate-700 uppercase mb-1">Muat Naik Lakaran Peta / Lampiran (Pilihan)</label>
                    <input type="file" name="lampiran_peta" accept="image/*" class="w-full py-1.5 px-3 rounded-xl border border-slate-300 text-xs text-slate-600 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                    <span class="text-[11px] text-slate-400 mt-1 block">Format: JPG, PNG, GIF. Maksimum 5MB.</span>
                </div>
            </div>
        </div>

        <!-- D. MAKLUMAT PERKHIDMATAN YANG DIBERI -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-5">
            <div class="font-black text-slate-900 text-sm flex items-center gap-2 border-b border-slate-100 pb-3">
                <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">D</span>
                MAKLUMAT PERKHIDMATAN YANG DIBERI
            </div>

            <!-- 12. Pilihan Perkhidmatan -->
            <div class="space-y-2">
                <label class="block font-bold text-slate-700 text-xs uppercase">12. Jenis Perkhidmatan Yang Diberi (Tandakan yang berkenaan)</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                    <label class="p-3 rounded-2xl border border-slate-200 hover:border-emerald-400 bg-slate-50/50 cursor-pointer flex items-start gap-2.5 transition">
                        <input type="checkbox" name="rawatan_lapangan" id="chkRawatanLapangan" value="1" {{ old('rawatan_lapangan') ? 'checked' : '' }} class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500">
                        <span class="font-bold text-slate-800">Rawatan Di Lapangan</span>
                    </label>

                    <label class="p-3 rounded-2xl border border-slate-200 hover:border-emerald-400 bg-slate-50/50 cursor-pointer flex items-start gap-2.5 transition">
                        <input type="checkbox" name="rawatan_klinik" id="chkRawatanKlinik" value="1" {{ old('rawatan_klinik', $temujanji ? '1' : '') ? 'checked' : '' }} class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500">
                        <span class="font-bold text-slate-800">Rawatan Di Klinik</span>
                    </label>

                    <div class="p-3 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-1.5">
                        <label class="cursor-pointer flex items-start gap-2.5">
                            <input type="checkbox" name="pembedahan" value="1" {{ old('pembedahan') ? 'checked' : '' }} class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500">
                            <span class="font-bold text-slate-800">Pembedahan</span>
                        </label>
                        <input type="text" name="keterangan_pembedahan" value="{{ old('keterangan_pembedahan') }}" placeholder="Nyatakan pembedahan..." class="w-full py-1 px-2 text-[11px] rounded-lg border border-slate-200">
                    </div>

                    <label class="p-3 rounded-2xl border border-slate-200 hover:border-emerald-400 bg-slate-50/50 cursor-pointer flex items-start gap-2.5 transition">
                        <input type="checkbox" name="pemantauan_pawah" id="chkPemantauanPawah" value="1" {{ old('pemantauan_pawah') ? 'checked' : '' }} class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500">
                        <span class="font-bold text-slate-800">Pemantauan Pawah Negeri</span>
                    </label>

                    <div class="p-3 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-1.5">
                        <label class="cursor-pointer flex items-start gap-2.5">
                            <input type="checkbox" name="pemantauan_projek" value="1" {{ old('pemantauan_projek') ? 'checked' : '' }} class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500">
                            <span class="font-bold text-slate-800">Pemantauan Projek</span>
                        </label>
                        <input type="text" name="keterangan_projek" value="{{ old('keterangan_projek') }}" placeholder="Nama projek / skim..." class="w-full py-1 px-2 text-[11px] rounded-lg border border-slate-200">
                    </div>

                    <label class="p-3 rounded-2xl border border-slate-200 hover:border-emerald-400 bg-slate-50/50 cursor-pointer flex items-start gap-2.5 transition">
                        <input type="checkbox" name="pemantauan_trust" value="1" {{ old('pemantauan_trust') ? 'checked' : '' }} class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500">
                        <span class="font-bold text-slate-800">Pemantauan TRUST</span>
                    </label>

                    <label class="p-3 rounded-2xl border border-slate-200 hover:border-emerald-400 bg-slate-50/50 cursor-pointer flex items-start gap-2.5 transition">
                        <input type="checkbox" name="lawatan_terancang" value="1" {{ old('lawatan_terancang') ? 'checked' : '' }} class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500">
                        <span class="font-bold text-slate-800">Lawatan Terancang & berJadual</span>
                    </label>

                    <div class="p-3 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-1.5">
                        <label class="cursor-pointer flex items-start gap-2.5">
                            <input type="checkbox" name="perkhidmatan_lain" value="1" {{ old('perkhidmatan_lain') ? 'checked' : '' }} class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500">
                            <span class="font-bold text-slate-800">Lain-lain</span>
                        </label>
                        <input type="text" name="keterangan_lain" value="{{ old('keterangan_lain') }}" placeholder="Catatan lain-lain..." class="w-full py-1 px-2 text-[11px] rounded-lg border border-slate-200">
                    </div>
                </div>
            </div>

            <!-- 13. Catatan Ringkas: Jenis Ternakan & Bilangan -->
            <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-200 space-y-3 text-xs">
                <div class="font-bold text-slate-800 uppercase">13. Catatan Ringkas : 1. Jenis Ternakan</div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2">
                    @php
                        $ternakanOptions = ['Lembu', 'Kerbau', 'Kambing', 'Biri-biri', 'Kuda', 'Ayam Pedaging', 'Kucing', 'Anjing', 'Arnab', 'Lain-lain'];
                        $oldJenis = old('jenis_ternakan', $temujanji ? [$temujanji->jenis_haiwan] : []);
                    @endphp
                    @foreach($ternakanOptions as $tOpt)
                        <label class="p-2 rounded-xl bg-white border border-slate-200 hover:border-emerald-400 cursor-pointer flex items-center gap-2 transition">
                            <input type="checkbox" name="jenis_ternakan[]" id="chkJenis_{{ \Illuminate\Support\Str::slug($tOpt) }}" value="{{ $tOpt }}" {{ in_array($tOpt, (array)$oldJenis) ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500">
                            <span class="font-bold text-slate-700">{{ $tOpt }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                    <div>
                        <label class="block font-bold text-slate-600 mb-1">Jika Lain-lain, Nyatakan:</label>
                        <input type="text" name="jenis_ternakan_lain" id="jenisLainInput" value="{{ old('jenis_ternakan_lain') }}" placeholder="cth: Unggas penelur, puyuh..." class="w-full py-1.5 px-3 rounded-xl border border-slate-300 bg-white">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-600 mb-1">2. Bil. Ternakan Dirawat (ekor):</label>
                        <input type="number" name="bil_ternakan" id="bilTernakanInput" value="{{ old('bil_ternakan', 1) }}" min="0" class="w-full py-1.5 px-3 rounded-xl border border-slate-300 bg-white font-mono font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-600 mb-1">3. Bil. Yang Ada di Ladang/Premis (ekor):</label>
                        <input type="number" name="bil_yang_ada" id="bilYangAdaInput" value="{{ old('bil_yang_ada') }}" min="0" placeholder="cth: 25" class="w-full py-1.5 px-3 rounded-xl border border-slate-300 bg-white font-mono font-bold">
                    </div>
                </div>
            </div>

            <!-- 17. Laporan & 21. Penggunaan Ubat -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">17. Laporan Lengkap Tindakan & Rawatan</label>
                    <textarea name="laporan" id="laporanTextarea" rows="5" placeholder="Catatkan perincian tag ternakan yang dirawat, pemeriksaan klinikal, diagnosis, suhu, rawatan atau pemantauan pawah..." class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700 leading-relaxed">{{ old('laporan') }}</textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">21. Penggunaan Ubat & Vaksin</label>
                    <textarea name="penggunaan_ubat" id="ubatTextarea" rows="5" placeholder="Senarai ubat/vaksin dan dos yang diberikan (cth: Oxytetracycline 20% LA 10ml, Vitamin B-Complex 5ml, Ivermectin 2ml)..." class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700 leading-relaxed">{{ old('penggunaan_ubat') }}</textarea>
                    @if(!empty($senaraiUbat))
                        <div class="mt-2 flex flex-wrap gap-1">
                            <span class="text-[10px] font-bold text-slate-400 py-0.5">Pilihan Pantas:</span>
                            @foreach(array_slice($senaraiUbat, 0, 8) as $ubt)
                                <button type="button" onclick="tambahUbat('{{ addslashes($ubt) }}')" class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-emerald-100 hover:text-emerald-900 text-[10px] font-semibold text-slate-600 transition">
                                    + {{ $ubt }}
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- E. PENGAKUAN PELANGGAN & PENGESAHAN PEGAWAI -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
            <div class="font-black text-slate-900 text-sm flex items-center gap-2 border-b border-slate-100 pb-3">
                <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">E</span>
                PENGAKUAN PELANGGAN & PENGESAHAN PEGAWAI
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
                <!-- 15. Tahap Kepuasan -->
                <div class="lg:col-span-3 bg-amber-50/60 p-4 rounded-2xl border border-amber-200">
                    <label class="block font-bold text-amber-900 uppercase mb-2">15. Saya (Pelanggan / Penternak):</label>
                    <div class="flex flex-wrap gap-6">
                        <label class="inline-flex items-center gap-2 cursor-pointer font-bold text-slate-800">
                            <input type="radio" name="kepuasan_pelanggan" value="Puashati" {{ old('kepuasan_pelanggan', 'Puashati') === 'Puashati' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                            <span class="text-emerald-800">Puashati</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer font-bold text-slate-800">
                            <input type="radio" name="kepuasan_pelanggan" value="Tidak puashati" {{ old('kepuasan_pelanggan') === 'Tidak puashati' ? 'checked' : '' }} class="text-rose-600 focus:ring-rose-500">
                            <span class="text-rose-800">Tidak puashati</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer font-bold text-slate-800">
                            <input type="radio" name="kepuasan_pelanggan" value="Boleh dipertimbangkan" {{ old('kepuasan_pelanggan') === 'Boleh dipertimbangkan' ? 'checked' : '' }} class="text-amber-600 focus:ring-amber-500">
                            <span class="text-amber-800">Boleh dipertimbangkan</span>
                        </label>
                    </div>
                </div>

                <!-- 16. Cadangan Pelanggan -->
                <div class="lg:col-span-3">
                    <label class="block font-bold text-slate-700 uppercase mb-1">16. Cadangan Pelanggan</label>
                    <input type="text" name="cadangan_pelanggan" value="{{ old('cadangan_pelanggan') }}" placeholder="Sebarang cadangan penambahbaikan perkhidmatan veterinar..." class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>

                <!-- 14. Tandatangan Pelanggan -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">14. Nama Penandatangan (Pelanggan)</label>
                    <input type="text" name="tandatangan_pelanggan_nama" id="tandatanganNamaInput" value="{{ old('tandatangan_pelanggan_nama') }}" placeholder="Nama penuh pelanggan..." class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Tandatangan</label>
                    <input type="date" name="tandatangan_pelanggan_tarikh" value="{{ old('tandatangan_pelanggan_tarikh', date('Y-m-d')) }}" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Masa Tandatangan</label>
                    <input type="text" name="tandatangan_pelanggan_masa" value="{{ old('tandatangan_pelanggan_masa', date('h:i A')) }}" placeholder="10:30 AM" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>

                <!-- 18. Bayaran & Resit -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">18. Bayaran (RM)</label>
                    <input type="number" step="0.01" name="bayaran" value="{{ old('bayaran', '0.00') }}" min="0" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 font-mono font-bold text-slate-800">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">No. Resit Pembayaran</label>
                    <input type="text" name="no_resit" value="{{ old('no_resit') }}" placeholder="cth: R-2026-0881" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 font-mono text-slate-700">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Status Borang</label>
                    <select name="status" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 font-bold text-slate-800">
                        <option value="Selesai" selected>Selesai (Telah Ditandatangani)</option>
                        <option value="Deraf">Deraf</option>
                        <option value="Disahkan">Disahkan Pegawai Projek</option>
                    </select>
                </div>

                <!-- 19. Pengesahan dan Ulasan -->
                <div class="lg:col-span-3">
                    <label class="block font-bold text-slate-700 uppercase mb-1">19. Pengesahan dan Ulasan Pegawai Projek</label>
                    <textarea name="pengesahan_ulasan_pegawai" rows="2" placeholder="Ulasan rasmi pegawai bertugas mengenai status kes dan rawatan..." class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">{{ old('pengesahan_ulasan_pegawai') }}</textarea>
                </div>
            </div>

            <div class="text-[11px] text-slate-400 italic pt-2 border-t border-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-amber-500"></i>
                Setiap dokumen hendaklah disahkan oleh pegawai projek. Hanya satu borang untuk setiap kes/pelanggan.
            </div>
        </div>

        <!-- Butang Simpan -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('action-list.index') }}" class="px-5 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                Batal
            </a>
            <button type="submit" class="px-7 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-sm shadow-lg transition flex items-center gap-2">
                <i class="fa-solid fa-check"></i> Simpan Borang Action List (PK-RK-61)
            </button>
        </div>
    </form>
</div>

<script>
    let selectedTernakanTags = new Set();
    let selectedTernakanIds = new Set();

    // Semak Pelanggan Lengkap (EPTR, EPU, Klinik, Pawah)
    function semakPelangganLengkap() {
        const icInput = document.getElementById('icLookupInput');
        const rawVal = icInput ? icInput.value.trim() : '';
        const alertBox = document.getElementById('semakStatusAlert');
        const btnText = document.getElementById('btnSemakText');
        const btnSpinner = document.getElementById('btnSemakSpinner');

        if (!rawVal) {
            tunjukAlert('Sila masukkan No. Kad Pengenalan pelanggan.', 'rose');
            return;
        }

        btnText.classList.add('hidden');
        btnSpinner.classList.remove('hidden');

        fetch(`{{ route('action-list.api-semak-pelanggan-lengkap') }}?ic_number=${encodeURIComponent(rawVal)}`)
            .then(res => res.json())
            .then(data => {
                btnText.classList.remove('hidden');
                btnSpinner.classList.add('hidden');

                if (data.found && data.pelanggan) {
                    const p = data.pelanggan;
                    document.getElementById('userIdInput').value = p.user_id || '';
                    document.getElementById('namaInput').value = p.nama || '';
                    document.getElementById('phoneInput').value = p.telefon || '';
                    document.getElementById('addressInput').value = p.alamat || '';
                    document.getElementById('poskodInput').value = p.poskod || '';
                    document.getElementById('mukimInput').value = p.mukim || '';
                    document.getElementById('tandatanganNamaInput').value = p.nama || '';

                    if (p.jajahan) {
                        const jSelect = document.getElementById('jajahanSelect');
                        if (jSelect) {
                            for (let i = 0; i < jSelect.options.length; i++) {
                                if (jSelect.options[i].value.toLowerCase() === p.jajahan.toLowerCase()) {
                                    jSelect.selectedIndex = i;
                                    break;
                                }
                            }
                        }
                        document.getElementById('daerahInput').value = p.daerah ? p.daerah : `Jajahan ${p.jajahan}, Kelantan`;
                    }

                    if (p.kategori_pelanggan === 'Syarikat') {
                        document.getElementById('kategoriSyarikat').checked = true;
                    } else {
                        document.getElementById('kategoriIndividu').checked = true;
                    }

                    // Suggested GPS & Temujanji
                    if (data.suggested_gps && !document.getElementById('gpsKoordinatInput').value) {
                        document.getElementById('gpsKoordinatInput').value = data.suggested_gps;
                    }

                    if (data.suggested_temujanji) {
                        const tj = data.suggested_temujanji;
                        if (tj.id) document.getElementById('temujanjiIdInput').value = tj.id;
                        if (tj.masa) document.getElementById('masaMulaInput').value = tj.masa;
                        if (tj.tujuan && !document.getElementById('catatanPerkhidmatanInput').value) {
                            document.getElementById('catatanPerkhidmatanInput').value = tj.tujuan;
                        }
                        if (tj.jenis_haiwan) {
                            tandakanJenisTernakan(tj.jenis_haiwan);
                        }
                        document.getElementById('chkRawatanKlinik').checked = true;
                    }

                    // Papar Komponen Bersepadu
                    renderBersepadu(data);
                    tunjukAlert(`Rekod pelanggan '${p.nama}' berjaya dimuatkan dengan integrasi data bersepadu!`, 'emerald');
                } else {
                    tunjukAlert(data.message || 'Tiada rekod penternak/pengguna dijumpai untuk No. K/P ini.', 'amber');
                    document.getElementById('panelBersepaduContainer').classList.add('hidden');
                }
            })
            .catch(err => {
                btnText.classList.remove('hidden');
                btnSpinner.classList.add('hidden');
                tunjukAlert('Ralat menyemak pangkalan data. Sila cuba lagi.', 'rose');
            });
    }

    function renderBersepadu(data) {
        const container = document.getElementById('panelBersepaduContainer');
        container.classList.remove('hidden');

        // 1. EPTR (Ruminan Tags)
        const panelEptr = document.getElementById('panelEptr');
        const listEptr = document.getElementById('eptrListContainer');
        const badgeEptr = document.getElementById('eptrCountBadge');

        if (data.eptr_ternakan && data.eptr_ternakan.length > 0) {
            panelEptr.classList.remove('hidden');
            badgeEptr.innerText = `${data.eptr_ternakan.length} Ekor`;
            listEptr.innerHTML = '';

            data.eptr_ternakan.forEach(t => {
                const item = document.createElement('div');
                item.className = 'p-2.5 rounded-xl border border-amber-200 bg-amber-50/50 hover:bg-amber-100/60 cursor-pointer transition flex items-start gap-2.5';
                item.innerHTML = `
                    <input type="checkbox" id="eptr_chk_${t.id}" class="mt-1 rounded text-amber-600 focus:ring-amber-500 eptr-ternakan-chk" onchange="toggleTernakanTag('${t.id}', '${t.no_tag}', '${t.jenis_ternakan}')">
                    <div class="flex-1" onclick="document.getElementById('eptr_chk_${t.id}').click()">
                        <div class="font-bold text-slate-900 font-mono text-xs flex items-center justify-between">
                            <span>${t.no_tag}</span>
                            <span class="text-[10px] px-1.5 py-0.2 rounded font-sans font-bold bg-amber-200 text-amber-900">${t.jenis_ternakan}</span>
                        </div>
                        <div class="text-[11px] text-slate-600 mt-0.5">Baka: ${t.baka || '-'} | ${t.jantina || '-'} (${t.umur || '-'})</div>
                        <div class="text-[10px] text-slate-500 truncate mt-0.5"><i class="fa-solid fa-location-dot text-amber-600"></i> ${t.lokasi_kandang || 'Kandang Penternak'}</div>
                    </div>
                `;
                listEptr.appendChild(item);
            });
        } else {
            panelEptr.classList.add('hidden');
        }

        // 2. EPU (Ladang Unggas)
        const panelEpu = document.getElementById('panelEpu');
        const listEpu = document.getElementById('epuListContainer');
        const badgeEpu = document.getElementById('epuCountBadge');

        if (data.epu_ladang && data.epu_ladang.length > 0) {
            panelEpu.classList.remove('hidden');
            badgeEpu.innerText = `${data.epu_ladang.length} Ladang`;
            listEpu.innerHTML = '';

            data.epu_ladang.forEach(l => {
                const item = document.createElement('div');
                item.className = 'p-3 rounded-2xl border border-blue-200 bg-blue-50/50 flex flex-col justify-between gap-2';
                item.innerHTML = `
                    <div>
                        <div class="font-bold text-slate-900 text-xs">${l.nama_ladang} (${l.id_premis || 'Premis'})</div>
                        <div class="text-[11px] text-slate-600 mt-0.5">${l.alamat_ladang || ''}, ${l.mukim || ''}</div>
                        <div class="text-[10px] font-mono text-blue-700 mt-1">
                            <i class="fa-solid fa-location-dot"></i> GPS: ${l.gps_koordinat || 'Tiada'} | Kapasiti: ${l.kapasiti_maksimum_unggas ? l.kapasiti_maksimum_unggas + ' ekor' : '-'}
                        </div>
                    </div>
                    <button type="button" onclick="pilihLadangEpu('${encodeURIComponent(JSON.stringify(l))}')" class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-arrow-down"></i> Gunakan Lokasi & Unggas Ladang Ini
                    </button>
                `;
                listEpu.appendChild(item);
            });
        } else {
            panelEpu.classList.add('hidden');
        }

        // 3. Program Pawah
        const panelPawah = document.getElementById('panelPawah');
        const listPawah = document.getElementById('pawahListContainer');
        const badgePawah = document.getElementById('pawahCountBadge');

        if (data.pawah_perjanjian && data.pawah_perjanjian.length > 0) {
            panelPawah.classList.remove('hidden');
            badgePawah.innerText = `${data.pawah_perjanjian.length} Perjanjian`;
            listPawah.innerHTML = '';

            data.pawah_perjanjian.forEach(pj => {
                const tagBadges = pj.ternakans.map(t => `<span class="px-1.5 py-0.5 rounded bg-purple-200 text-purple-950 font-mono text-[10px] font-bold">${t.no_tag} (${t.jenis_ternakan})</span>`).join(' ');
                const item = document.createElement('div');
                item.className = 'p-3 rounded-2xl border border-purple-200 bg-purple-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3';
                item.innerHTML = `
                    <div class="space-y-1">
                        <div class="font-bold text-slate-900 text-xs flex items-center gap-2">
                            <span class="font-mono text-purple-800">${pj.no_perjanjian}</span>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold bg-purple-100 text-purple-800">${pj.status}</span>
                        </div>
                        <div class="text-[11px] text-slate-600">${pj.nama_program || 'Program Pawah Ternakan'} (${pj.tarikh_mula} - ${pj.tarikh_tamat})</div>
                        <div class="flex flex-wrap gap-1 pt-1">${tagBadges || '<span class="text-[10px] text-slate-400">Tiada tag induk</span>'}</div>
                    </div>
                    <button type="button" onclick="pilihPawahPerjanjian('${pj.id}', '${pj.no_perjanjian}', '${encodeURIComponent(JSON.stringify(pj.ternakans))}')" class="px-3.5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs whitespace-nowrap transition flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-check"></i> Pilih Untuk Pemantauan Pawah
                    </button>
                `;
                listPawah.appendChild(item);
            });
        } else {
            panelPawah.classList.add('hidden');
        }

        // 4. Klinik (Rekod Lalu)
        const panelKlinik = document.getElementById('panelKlinik');
        const listKlinik = document.getElementById('klinikListContainer');
        const badgeKlinik = document.getElementById('klinikCountBadge');

        if (data.klinik_rekod && data.klinik_rekod.length > 0) {
            panelKlinik.classList.remove('hidden');
            badgeKlinik.innerText = `${data.klinik_rekod.length} Rekod`;
            listKlinik.innerHTML = '';

            data.klinik_rekod.forEach(k => {
                const item = document.createElement('div');
                item.className = 'p-2.5 rounded-xl border border-teal-200 bg-teal-50/40 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs';
                item.innerHTML = `
                    <div>
                        <div class="font-bold text-slate-900">${k.tarikh} (${k.masa}) - <span class="text-teal-800">${k.jenis_haiwan || 'Haiwan'}</span> ${k.nama_haiwan ? `(${k.nama_haiwan})` : ''}</div>
                        <div class="text-[11px] text-slate-600">Aduan: ${k.tujuan || '-'} ${k.rawatan.diagnosis ? ` | Diagnosis: ${k.rawatan.diagnosis}` : ''}</div>
                    </div>
                    <button type="button" onclick="gunaRekodKlinik('${k.id}', '${k.jenis_haiwan}', '${encodeURIComponent(k.tujuan || '')}', '${encodeURIComponent(k.rawatan.ubat_diberikan || '')}')" class="px-2.5 py-1 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-[11px] font-bold transition whitespace-nowrap">
                        Guna Maklumat Kes Ini
                    </button>
                `;
                listKlinik.appendChild(item);
            });
        } else {
            panelKlinik.classList.add('hidden');
        }
    }

    function toggleTernakanTag(id, tag, jenis) {
        const chk = document.getElementById(`eptr_chk_${id}`);
        if (chk && chk.checked) {
            selectedTernakanIds.add(id);
            selectedTernakanTags.add(`${jenis} [${tag}]`);
            tandakanJenisTernakan(jenis);
        } else {
            selectedTernakanIds.delete(id);
            selectedTernakanTags.delete(`${jenis} [${tag}]`);
        }

        kemaskiniTernakanTerpilih();
    }

    function kemaskiniTernakanTerpilih() {
        const bilInput = document.getElementById('bilTernakanInput');
        if (selectedTernakanIds.size > 0) {
            bilInput.value = selectedTernakanIds.size;
        }

        // Kemaskini hidden inputs
        const hiddenDiv = document.getElementById('hiddenTernakanInputs');
        hiddenDiv.innerHTML = '';
        selectedTernakanIds.forEach(id => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'ternakan_terlibat_ids[]';
            inp.value = id;
            hiddenDiv.appendChild(inp);
        });

        // Kemaskini Laporan
        const laporanTxt = document.getElementById('laporanTextarea');
        if (selectedTernakanTags.size > 0) {
            const tagStr = `Ternakan dirawat (EPTR): ${Array.from(selectedTernakanTags).join(', ')}`;
            if (!laporanTxt.value.includes('Ternakan dirawat (EPTR)')) {
                laporanTxt.value = tagStr + (laporanTxt.value ? "\n" + laporanTxt.value : '');
            } else {
                laporanTxt.value = laporanTxt.value.replace(/Ternakan dirawat \(EPTR\):.*/, tagStr);
            }
        }
    }

    function pilihLadangEpu(rawJson) {
        const l = JSON.parse(decodeURIComponent(rawJson));
        if (l.alamat_ladang) document.getElementById('addressInput').value = l.alamat_ladang;
        if (l.mukim) document.getElementById('mukimInput').value = l.mukim;
        if (l.gps_koordinat) document.getElementById('gpsKoordinatInput').value = l.gps_koordinat;
        if (l.kapasiti_maksimum_unggas) document.getElementById('bilYangAdaInput').value = l.kapasiti_maksimum_unggas;
        
        tandakanJenisTernakan('Ayam Pedaging');
        document.getElementById('chkRawatanLapangan').checked = true;
        tunjukAlert(`Maklumat ladang '${l.nama_ladang}' dan koordinat GPS telah disalin ke borang.`, 'emerald');
    }

    function pilihPawahPerjanjian(id, noPerjanjian, rawTags) {
        document.getElementById('pawahPerjanjianIdInput').value = id;
        document.getElementById('chkPemantauanPawah').checked = true;
        
        const tags = JSON.parse(decodeURIComponent(rawTags));
        let tagStr = '';
        if (tags && tags.length > 0) {
            tagStr = tags.map(t => `${t.jenis_ternakan} [${t.no_tag}]`).join(', ');
            tandakanJenisTernakan(tags[0].jenis_ternakan);
            document.getElementById('bilTernakanInput').value = tags.length;
        }

        const catInp = document.getElementById('catatanPerkhidmatanInput');
        if (!catInp.value) {
            catInp.value = `Pemantauan Ternakan Program Pawah Negeri (No. Perjanjian: ${noPerjanjian})`;
        }

        const lapTxt = document.getElementById('laporanTextarea');
        const pawahReport = `Pemantauan Pawah: Perjanjian ${noPerjanjian}. Tag Terlibat: ${tagStr || 'Tiada'}.`;
        if (!lapTxt.value.includes('Pemantauan Pawah:')) {
            lapTxt.value = pawahReport + (lapTxt.value ? "\n" + lapTxt.value : '');
        }

        tunjukAlert(`Perjanjian Pawah '${noPerjanjian}' telah dihubungkan ke borang ini.`, 'emerald');
    }

    function gunaRekodKlinik(id, jenis, rawTujuan, rawUbat) {
        document.getElementById('temujanjiIdInput').value = id;
        document.getElementById('chkRawatanKlinik').checked = true;
        if (jenis) tandakanJenisTernakan(jenis);

        const tujuan = decodeURIComponent(rawTujuan);
        if (tujuan) document.getElementById('catatanPerkhidmatanInput').value = tujuan;

        const ubat = decodeURIComponent(rawUbat);
        if (ubat) {
            const ubatTxt = document.getElementById('ubatTextarea');
            ubatTxt.value = ubatTxt.value ? ubatTxt.value + ', ' + ubat : ubat;
        }

        tunjukAlert('Maklumat temujanji klinik telah disalin ke borang.', 'emerald');
    }

    function tandakanJenisTernakan(jenis) {
        if (!jenis) return;
        const slug = jenis.toLowerCase().replace(/[^a-z0-9]/g, '-');
        const chk = document.getElementById(`chkJenis_${slug}`);
        if (chk) {
            chk.checked = true;
        } else {
            const chkLain = document.getElementById('chkJenis_lain-lain');
            if (chkLain) chkLain.checked = true;
            const lainInp = document.getElementById('jenisLainInput');
            if (lainInp && !lainInp.value) lainInp.value = jenis;
        }
    }

    function tunjukAlert(msg, color) {
        const box = document.getElementById('semakStatusAlert');
        box.className = `text-xs font-semibold p-2.5 rounded-xl border ${color === 'emerald' ? 'bg-emerald-100 text-emerald-900 border-emerald-300' : (color === 'rose' ? 'bg-rose-100 text-rose-900 border-rose-300' : 'bg-amber-100 text-amber-900 border-amber-300')}`;
        box.innerHTML = `<i class="fa-solid ${color === 'emerald' ? 'fa-check-circle text-emerald-600' : 'fa-info-circle text-amber-600'} mr-1.5"></i> ${msg}`;
        box.classList.remove('hidden');
    }

    function dapatkanGpsSemasa() {
        if (!navigator.geolocation) {
            alert('Pelayar anda tidak menyokong geolokasi GPS.');
            return;
        }
        navigator.geolocation.getCurrentPosition(pos => {
            const lat = pos.coords.latitude.toFixed(6);
            const lng = pos.coords.longitude.toFixed(6);
            document.getElementById('gpsKoordinatInput').value = `${lat}, ${lng}`;
            tunjukAlert(`Koordinat GPS semasa diperolehi: ${lat}, ${lng}`, 'emerald');
        }, err => {
            alert('Gagal mendapatkan lokasi GPS. Sila pastikan kebenaran lokasi diaktifkan.');
        });
    }

    function bukaGoogleMaps() {
        const coords = document.getElementById('gpsKoordinatInput').value.trim();
        if (!coords) {
            alert('Sila masukkan atau dapatkan koordinat GPS terlebih dahulu.');
            return;
        }
        window.open(`https://www.google.com/maps?q=${encodeURIComponent(coords)}`, '_blank');
    }

    function tambahUbat(nama) {
        const txt = document.getElementById('ubatTextarea');
        if (!txt) return;
        if (txt.value.trim() === '') {
            txt.value = nama;
        } else {
            txt.value += ', ' + nama;
        }
    }

    // Auto semak jika no_kp telah wujud dalam input semasa halaman dibuka
    document.addEventListener('DOMContentLoaded', function() {
        const icInp = document.getElementById('icLookupInput');
        if (icInp && icInp.value.trim().length >= 8) {
            semakPelangganLengkap();
        }

        if (icInp) {
            icInp.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    semakPelangganLengkap();
                }
            });
        }
    });
</script>
@endsection
