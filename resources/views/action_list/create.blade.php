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
                Borang rasmi tindakan perkhidmatan lapangan, klinik, pemantauan projek pawah dan rawatan haiwan.
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

    <form action="{{ route('action-list.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
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
                    <select name="jajahan" required class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 font-bold text-slate-800">
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
                        <input type="radio" name="kategori_pelanggan" value="Individu" {{ old('kategori_pelanggan', 'Individu') === 'Individu' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                        <span class="font-bold text-slate-700">INDIVIDU</span>
                    </label>
                    <label class="inline-flex items-center gap-1.5 cursor-pointer">
                        <input type="radio" name="kategori_pelanggan" value="Syarikat" {{ old('kategori_pelanggan') === 'Syarikat' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                        <span class="font-bold text-slate-700">SYARIKAT</span>
                    </label>
                </div>
            </div>

            <!-- Carian Autolengkap Pantas -->
            <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200 flex flex-col sm:flex-row sm:items-center gap-3 text-xs">
                <div class="font-bold text-slate-600 flex items-center gap-1.5 whitespace-nowrap">
                    <i class="fa-solid fa-bolt text-amber-500"></i> Carian Pantas Penternak:
                </div>
                <div class="relative flex-1">
                    <input type="text" id="carianPelangganInput" placeholder="Taip No. Kad Pengenalan / Nama Penternak / No Telefon untuk autolengkap..." class="w-full py-1.5 pl-8 pr-3 text-xs rounded-xl border border-slate-300 focus:outline-emerald-500">
                    <i class="fa-solid fa-search absolute left-2.5 top-2.5 text-slate-400 text-xs"></i>
                    <div id="carianResultBox" class="absolute z-20 left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg hidden max-h-48 overflow-y-auto divide-y divide-slate-100"></div>
                </div>
            </div>

            <input type="hidden" name="user_id" id="userIdInput" value="{{ old('user_id', $prefillUser->id ?? '') }}">
            <input type="hidden" name="temujanji_id" value="{{ $temujanji->id ?? '' }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
                <div class="lg:col-span-2">
                    <label class="block font-bold text-slate-700 uppercase mb-1">1. Nama Pelanggan / Syarikat <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_pelanggan" id="namaInput" value="{{ old('nama_pelanggan', $prefillUser->name ?? '') }}" required placeholder="cth: Ahmad bin Abdullah / Syarikat Ternakan Maju Sdn Bhd" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 font-bold text-slate-800">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Masa Pendaftaran</label>
                    <input type="text" name="masa_pendaftaran" value="{{ old('masa_pendaftaran', date('h:i A')) }}" placeholder="cth: 09:30 AM" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">2. No. Kad Pengenalan (Baru) / SSM</label>
                    <input type="text" name="no_kp" id="icInput" value="{{ old('no_kp', $prefillUser->ic_number ?? '') }}" placeholder="cth: 880101035555" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 font-mono font-semibold text-slate-800">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">6. No. Telefon</label>
                    <input type="text" name="telefon" id="phoneInput" value="{{ old('telefon', $prefillUser->phone ?? '') }}" placeholder="cth: 019-9876543" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">20. No. Rujukan Fail</label>
                    <input type="text" name="no_rujukan" value="{{ old('no_rujukan') }}" placeholder="cth: JPVNK/PP/RAW/2026/012" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 font-mono text-slate-700">
                </div>

                <div class="lg:col-span-3">
                    <label class="block font-bold text-slate-700 uppercase mb-1">3. Alamat Lengkap</label>
                    <textarea name="alamat" id="addressInput" rows="2" placeholder="Alamat premis / ladang penternak..." class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">{{ old('alamat', $prefillUser->address ?? '') }}</textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">4. Mukim</label>
                    <input type="text" name="mukim" id="mukimInput" value="{{ old('mukim') }}" placeholder="cth: Padang Pak Amat" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Poskod</label>
                    <input type="text" name="poskod" id="poskodInput" value="{{ old('poskod', $prefillUser->poskod ?? '16800') }}" placeholder="16800" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 font-mono text-slate-700">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">5. Daerah / Jajahan</label>
                    <input type="text" name="daerah" id="daerahInput" value="{{ old('daerah', 'Jajahan ' . $defaultJajahan . ', Kelantan') }}" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>
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
                <textarea name="catatan_perkhidmatan_dipohon" rows="2" placeholder="Nyatakan permohonan atau aduan penternak (cth: Lembu mengalami demam dan hilang selera makan / Permohonan pemeriksaan kesihatan ruminan)..." class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">{{ old('catatan_perkhidmatan_dipohon', $temujanji->simptom_atau_tujuan ?? '') }}</textarea>
            </div>
        </div>

        <!-- C. MAKLUMAT TEMUJANJI -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
            <div class="font-black text-slate-900 text-sm flex items-center gap-2 border-b border-slate-100 pb-3">
                <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">C</span>
                MAKLUMAT TEMUJANJI
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
                    <input type="text" name="masa_temujanji_mula" value="{{ old('masa_temujanji_mula', '09:00 AM') }}" placeholder="09:00 AM" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Masa Temujanji (Hingga)</label>
                    <input type="text" name="masa_temujanji_hingga" value="{{ old('masa_temujanji_hingga', '11:00 AM') }}" placeholder="11:00 AM" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>

                <div class="lg:col-span-2">
                    <label class="block font-bold text-slate-700 uppercase mb-1">10. Maklumat Pelanggan (jika berlainan / wakil)</label>
                    <input type="text" name="maklumat_pelanggan_berlainan" value="{{ old('maklumat_pelanggan_berlainan') }}" placeholder="Nama wakil, no telefon atau hubungan dengan pemilik..." class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>

                <div class="lg:col-span-2">
                    <label class="block font-bold text-slate-700 uppercase mb-1">11. Maklumat Tambahan / Catatan Lokasi</label>
                    <input type="text" name="maklumat_tambahan" value="{{ old('maklumat_tambahan') }}" placeholder="Penanda lokasi, koordinat GPS, panduan simpang..." class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>

                <div class="lg:col-span-4">
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
                        <input type="checkbox" name="rawatan_lapangan" value="1" {{ old('rawatan_lapangan') ? 'checked' : '' }} class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500">
                        <span class="font-bold text-slate-800">Rawatan Di Lapangan</span>
                    </label>

                    <label class="p-3 rounded-2xl border border-slate-200 hover:border-emerald-400 bg-slate-50/50 cursor-pointer flex items-start gap-2.5 transition">
                        <input type="checkbox" name="rawatan_klinik" value="1" {{ old('rawatan_klinik', $temujanji ? '1' : '') ? 'checked' : '' }} class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500">
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
                        <input type="checkbox" name="pemantauan_pawah" value="1" {{ old('pemantauan_pawah') ? 'checked' : '' }} class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500">
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
                            <input type="checkbox" name="jenis_ternakan[]" value="{{ $tOpt }}" {{ in_array($tOpt, (array)$oldJenis) ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500">
                            <span class="font-bold text-slate-700">{{ $tOpt }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                    <div>
                        <label class="block font-bold text-slate-600 mb-1">Jika Lain-lain, Nyatakan:</label>
                        <input type="text" name="jenis_ternakan_lain" value="{{ old('jenis_ternakan_lain') }}" placeholder="cth: Unggas penelur, puyuh..." class="w-full py-1.5 px-3 rounded-xl border border-slate-300 bg-white">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-600 mb-1">2. Bil. Ternakan Dirawat (ekor):</label>
                        <input type="number" name="bil_ternakan" value="{{ old('bil_ternakan', 1) }}" min="0" class="w-full py-1.5 px-3 rounded-xl border border-slate-300 bg-white font-mono font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-600 mb-1">3. Bil. Yang Ada di Ladang/Premis (ekor):</label>
                        <input type="number" name="bil_yang_ada" value="{{ old('bil_yang_ada') }}" min="0" placeholder="cth: 25" class="w-full py-1.5 px-3 rounded-xl border border-slate-300 bg-white font-mono font-bold">
                    </div>
                </div>
            </div>

            <!-- 17. Laporan & 21. Penggunaan Ubat -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">17. Laporan Lengkap Tindakan & Rawatan</label>
                    <textarea name="laporan" rows="4" placeholder="Catatkan perincian pemeriksaan klinikal, diagnosis, suhu, berat, prosedur pembedahan atau tindakan pemantauan..." class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700 leading-relaxed">{{ old('laporan') }}</textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">21. Penggunaan Ubat & Vaksin</label>
                    <textarea name="penggunaan_ubat" id="ubatTextarea" rows="4" placeholder="Senarai ubat/vaksin dan dos yang diberikan (cth: Oxytetracycline 20% LA 10ml, Vitamin B-Complex 5ml, Ivermectin 2ml)..." class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700 leading-relaxed">{{ old('penggunaan_ubat') }}</textarea>
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
    // Autocomplete Carian Pelanggan
    const searchInput = document.getElementById('carianPelangganInput');
    const resultBox = document.getElementById('carianResultBox');

    if (searchInput) {
        let timeout = null;
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            const val = this.value.trim();
            if (val.length < 2) {
                resultBox.classList.add('hidden');
                return;
            }

            timeout = setTimeout(() => {
                fetch(`{{ route('action-list.api-cari-pelanggan') }}?query=${encodeURIComponent(val)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            resultBox.innerHTML = '';
                            data.forEach(item => {
                                const row = document.createElement('div');
                                row.className = 'p-2.5 hover:bg-emerald-50 cursor-pointer transition';
                                row.innerHTML = `
                                    <div class="font-bold text-slate-900">${item.name}</div>
                                    <div class="text-[11px] text-slate-500">K/P: ${item.ic_number || '-'} | Tel: ${item.phone || '-'} | ${item.jajahan || ''}</div>
                                `;
                                row.addEventListener('click', () => {
                                    document.getElementById('userIdInput').value = item.id;
                                    document.getElementById('namaInput').value = item.name;
                                    document.getElementById('icInput').value = item.ic_number || '';
                                    document.getElementById('phoneInput').value = item.phone || '';
                                    document.getElementById('addressInput').value = item.address || '';
                                    document.getElementById('poskodInput').value = item.poskod || '';
                                    if (item.jajahan) {
                                        document.getElementById('daerahInput').value = `Jajahan ${item.jajahan}, Kelantan`;
                                    }
                                    document.getElementById('tandatanganNamaInput').value = item.name;
                                    resultBox.classList.add('hidden');
                                    searchInput.value = '';
                                });
                                resultBox.appendChild(row);
                            });
                            resultBox.classList.remove('hidden');
                        } else {
                            resultBox.innerHTML = '<div class="p-3 text-center text-slate-400 text-xs">Tiada penternak/pengguna dijumpai.</div>';
                            resultBox.classList.remove('hidden');
                        }
                    });
            }, 300);
        });

        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !resultBox.contains(e.target)) {
                resultBox.classList.add('hidden');
            }
        });
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
</script>
@endsection
