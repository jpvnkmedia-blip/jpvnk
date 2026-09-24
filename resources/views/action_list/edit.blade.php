@extends('layouts.app')

@section('title', 'Kemaskini Borang Action List (PK-RK-61) - JPVNK')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gradient-to-r from-emerald-900 via-teal-900 to-slate-900 p-6 rounded-3xl text-white shadow-xl">
        <div class="space-y-1">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold border border-amber-500/30">
                <i class="fa-solid fa-pen-to-square"></i> KEMASKINI BORANG ACTION LIST (PK-RK-61)
            </div>
            <h1 class="text-2xl font-black">{{ $actionList->no_bil ?: ('BIL-' . $actionList->id) }}</h1>
            <p class="text-xs text-slate-300">
                Pejabat Perkhidmatan Veterinar Jajahan {{ $actionList->jajahan }} | Tarikh: {{ $actionList->tarikh ? $actionList->tarikh->format('d/m/Y') : '-' }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('action-list.show', $actionList->id) }}" class="px-4 py-2 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition flex items-center gap-1.5 border border-white/20">
                <i class="fa-solid fa-eye"></i> Lihat Rekod
            </a>
            <a href="{{ route('action-list.index') }}" class="px-4 py-2 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition flex items-center gap-1.5 border border-white/20">
                <i class="fa-solid fa-arrow-left"></i> Senarai
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

    <form action="{{ route('action-list.update', $actionList->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- HEADER DOKUMEN BORANG -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="font-black text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-stamp text-emerald-600"></i> MAKLUMAT DOKUMEN & PEJABAT JAJAHAN
                </div>
                <div class="font-mono text-xs font-black text-slate-400">{{ $actionList->kod_dokumen ?? 'PK-RK-61' }}</div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Kod Dokumen</label>
                    <input type="text" name="kod_dokumen" value="{{ $actionList->kod_dokumen ?? 'PK-RK-61' }}" readonly class="w-full py-2 px-3 rounded-xl bg-slate-100 border border-slate-200 font-mono font-bold text-slate-600">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">No. Siri / BIL <span class="text-rose-500">*</span></label>
                    <input type="text" name="no_bil" value="{{ old('no_bil', $actionList->no_bil) }}" required class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 font-mono font-bold text-emerald-700">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Pejabat Jajahan <span class="text-rose-500">*</span></label>
                    <select name="jajahan" required class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 font-bold text-slate-800">
                        @foreach($jajahanList as $jjh)
                            <option value="{{ $jjh }}" {{ old('jajahan', $actionList->jajahan) === $jjh ? 'selected' : '' }}>Jajahan {{ $jjh }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Borang <span class="text-rose-500">*</span></label>
                    <input type="date" name="tarikh" value="{{ old('tarikh', $actionList->tarikh ? $actionList->tarikh->format('Y-m-d') : date('Y-m-d')) }}" required class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 font-semibold text-slate-800">
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
                        <input type="radio" name="kategori_pelanggan" value="Individu" {{ old('kategori_pelanggan', $actionList->kategori_pelanggan) === 'Individu' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                        <span class="font-bold text-slate-700">INDIVIDU</span>
                    </label>
                    <label class="inline-flex items-center gap-1.5 cursor-pointer">
                        <input type="radio" name="kategori_pelanggan" value="Syarikat" {{ old('kategori_pelanggan', $actionList->kategori_pelanggan) === 'Syarikat' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                        <span class="font-bold text-slate-700">SYARIKAT</span>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
                <div class="lg:col-span-2">
                    <label class="block font-bold text-slate-700 uppercase mb-1">1. Nama Pelanggan / Syarikat <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_pelanggan" value="{{ old('nama_pelanggan', $actionList->nama_pelanggan) }}" required class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 font-bold text-slate-800">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Masa Pendaftaran</label>
                    <input type="text" name="masa_pendaftaran" value="{{ old('masa_pendaftaran', $actionList->masa_pendaftaran) }}" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">2. No. Kad Pengenalan (Baru) / SSM</label>
                    <input type="text" name="no_kp" value="{{ old('no_kp', $actionList->no_kp) }}" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 font-mono font-semibold text-slate-800">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">6. No. Telefon</label>
                    <input type="text" name="telefon" value="{{ old('telefon', $actionList->telefon) }}" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">20. No. Rujukan Fail</label>
                    <input type="text" name="no_rujukan" value="{{ old('no_rujukan', $actionList->no_rujukan) }}" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 font-mono text-slate-700">
                </div>

                <div class="lg:col-span-3">
                    <label class="block font-bold text-slate-700 uppercase mb-1">3. Alamat Lengkap</label>
                    <textarea name="alamat" rows="2" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">{{ old('alamat', $actionList->alamat) }}</textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">4. Mukim</label>
                    <input type="text" name="mukim" value="{{ old('mukim', $actionList->mukim) }}" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Poskod</label>
                    <input type="text" name="poskod" value="{{ old('poskod', $actionList->poskod) }}" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 font-mono text-slate-700">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">5. Daerah / Jajahan</label>
                    <input type="text" name="daerah" value="{{ old('daerah', $actionList->daerah) }}" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
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
                <textarea name="catatan_perkhidmatan_dipohon" rows="2" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">{{ old('catatan_perkhidmatan_dipohon', $actionList->catatan_perkhidmatan_dipohon) }}</textarea>
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
                    <input type="text" name="nama_pegawai" value="{{ old('nama_pegawai', $actionList->nama_pegawai) }}" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 font-semibold text-slate-800">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Masa Pegawai</label>
                    <input type="text" name="masa_pegawai" value="{{ old('masa_pegawai', $actionList->masa_pegawai) }}" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">9. Masa Temujanji (Mula)</label>
                    <input type="text" name="masa_temujanji_mula" value="{{ old('masa_temujanji_mula', $actionList->masa_temujanji_mula) }}" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Masa Temujanji (Hingga)</label>
                    <input type="text" name="masa_temujanji_hingga" value="{{ old('masa_temujanji_hingga', $actionList->masa_temujanji_hingga) }}" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>

                <div class="lg:col-span-2">
                    <label class="block font-bold text-slate-700 uppercase mb-1">10. Maklumat Pelanggan (jika berlainan / wakil)</label>
                    <input type="text" name="maklumat_pelanggan_berlainan" value="{{ old('maklumat_pelanggan_berlainan', $actionList->maklumat_pelanggan_berlainan) }}" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>

                <div class="lg:col-span-2">
                    <label class="block font-bold text-slate-700 uppercase mb-1">11. Maklumat Tambahan / Catatan Lokasi</label>
                    <input type="text" name="maklumat_tambahan" value="{{ old('maklumat_tambahan', $actionList->maklumat_tambahan) }}" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>

                <!-- GPS Koordinat Lokasi -->
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
                        <input type="text" name="gps_koordinat" id="gpsKoordinatInput" value="{{ old('gps_koordinat', $actionList->gps_koordinat) }}" placeholder="cth: 5.8392, 102.3941" class="w-full py-1.5 px-3 rounded-xl border border-slate-300 bg-white font-mono text-slate-800">
                        <button type="button" onclick="bukaGoogleMaps()" class="px-3 py-1.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs whitespace-nowrap flex items-center gap-1">
                            <i class="fa-solid fa-map-location-dot"></i> Peta
                        </button>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <label class="block font-bold text-slate-700 uppercase mb-1">Lampiran Lakaran Peta</label>
                    @if($actionList->lampiran_peta)
                        <div class="mb-2 flex items-center gap-3">
                            <a href="{{ asset('storage/' . $actionList->lampiran_peta) }}" target="_blank" class="px-3 py-1 bg-slate-100 border border-slate-300 text-slate-700 font-bold rounded-lg text-xs hover:bg-slate-200 inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-map"></i> Lihat Lampiran Semasa
                            </a>
                            <span class="text-[11px] text-slate-400">Muat naik fail baharu di bawah jika mahu menggantikan lampiran sedia ada.</span>
                        </div>
                    @endif
                    <input type="file" name="lampiran_peta" accept="image/*" class="w-full py-1.5 px-3 rounded-xl border border-slate-300 text-xs text-slate-600 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                </div>
            </div>
        </div>

        <!-- D. MAKLUMAT PERKHIDMATAN YANG DIBERI -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-5">
            <div class="font-black text-slate-900 text-sm flex items-center gap-2 border-b border-slate-100 pb-3">
                <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">D</span>
                MAKLUMAT PERKHIDMATAN YANG DIBERI
            </div>

            @php
                $srv = is_array($actionList->perkhidmatan_diberi) ? $actionList->perkhidmatan_diberi : [];
            @endphp

            <!-- 12. Pilihan Perkhidmatan -->
            <div class="space-y-2">
                <label class="block font-bold text-slate-700 text-xs uppercase">12. Jenis Perkhidmatan Yang Diberi</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                    <label class="p-3 rounded-2xl border border-slate-200 hover:border-emerald-400 bg-slate-50/50 cursor-pointer flex items-start gap-2.5 transition">
                        <input type="checkbox" name="rawatan_lapangan" value="1" {{ old('rawatan_lapangan', !empty($srv['rawatan_lapangan'])) ? 'checked' : '' }} class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500">
                        <span class="font-bold text-slate-800">Rawatan Di Lapangan</span>
                    </label>

                    <label class="p-3 rounded-2xl border border-slate-200 hover:border-emerald-400 bg-slate-50/50 cursor-pointer flex items-start gap-2.5 transition">
                        <input type="checkbox" name="rawatan_klinik" value="1" {{ old('rawatan_klinik', !empty($srv['rawatan_klinik'])) ? 'checked' : '' }} class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500">
                        <span class="font-bold text-slate-800">Rawatan Di Klinik</span>
                    </label>

                    <div class="p-3 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-1.5">
                        <label class="cursor-pointer flex items-start gap-2.5">
                            <input type="checkbox" name="pembedahan" value="1" {{ old('pembedahan', !empty($srv['pembedahan'])) ? 'checked' : '' }} class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500">
                            <span class="font-bold text-slate-800">Pembedahan</span>
                        </label>
                        <input type="text" name="keterangan_pembedahan" value="{{ old('keterangan_pembedahan', $actionList->keterangan_pembedahan ?? ($srv['keterangan_pembedahan'] ?? '')) }}" placeholder="Nyatakan pembedahan..." class="w-full py-1 px-2 text-[11px] rounded-lg border border-slate-200">
                    </div>

                    <label class="p-3 rounded-2xl border border-slate-200 hover:border-emerald-400 bg-slate-50/50 cursor-pointer flex items-start gap-2.5 transition">
                        <input type="checkbox" name="pemantauan_pawah" value="1" {{ old('pemantauan_pawah', !empty($srv['pemantauan_pawah'])) ? 'checked' : '' }} class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500">
                        <span class="font-bold text-slate-800">Pemantauan Pawah Negeri</span>
                    </label>

                    <div class="p-3 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-1.5">
                        <label class="cursor-pointer flex items-start gap-2.5">
                            <input type="checkbox" name="pemantauan_projek" value="1" {{ old('pemantauan_projek', !empty($srv['pemantauan_projek'])) ? 'checked' : '' }} class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500">
                            <span class="font-bold text-slate-800">Pemantauan Projek</span>
                        </label>
                        <input type="text" name="keterangan_projek" value="{{ old('keterangan_projek', $actionList->keterangan_projek ?? ($srv['keterangan_projek'] ?? '')) }}" placeholder="Nama projek / skim..." class="w-full py-1 px-2 text-[11px] rounded-lg border border-slate-200">
                    </div>

                    <label class="p-3 rounded-2xl border border-slate-200 hover:border-emerald-400 bg-slate-50/50 cursor-pointer flex items-start gap-2.5 transition">
                        <input type="checkbox" name="pemantauan_trust" value="1" {{ old('pemantauan_trust', !empty($srv['pemantauan_trust'])) ? 'checked' : '' }} class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500">
                        <span class="font-bold text-slate-800">Pemantauan TRUST</span>
                    </label>

                    <label class="p-3 rounded-2xl border border-slate-200 hover:border-emerald-400 bg-slate-50/50 cursor-pointer flex items-start gap-2.5 transition">
                        <input type="checkbox" name="lawatan_terancang" value="1" {{ old('lawatan_terancang', !empty($srv['lawatan_terancang'])) ? 'checked' : '' }} class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500">
                        <span class="font-bold text-slate-800">Lawatan Terancang & berJadual</span>
                    </label>

                    <div class="p-3 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-1.5">
                        <label class="cursor-pointer flex items-start gap-2.5">
                            <input type="checkbox" name="perkhidmatan_lain" value="1" {{ old('perkhidmatan_lain', !empty($srv['perkhidmatan_lain'])) ? 'checked' : '' }} class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500">
                            <span class="font-bold text-slate-800">Lain-lain</span>
                        </label>
                        <input type="text" name="keterangan_lain" value="{{ old('keterangan_lain', $actionList->keterangan_lain ?? ($srv['keterangan_lain'] ?? '')) }}" placeholder="Catatan lain-lain..." class="w-full py-1 px-2 text-[11px] rounded-lg border border-slate-200">
                    </div>
                </div>
            </div>

            <!-- 13. Catatan Ringkas: Jenis Ternakan & Bilangan -->
            <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-200 space-y-3 text-xs">
                <div class="font-bold text-slate-800 uppercase">13. Catatan Ringkas : 1. Jenis Ternakan</div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2">
                    @php
                        $ternakanOptions = ['Lembu', 'Kerbau', 'Kambing', 'Biri-biri', 'Kuda', 'Ayam Pedaging', 'Kucing', 'Anjing', 'Arnab', 'Lain-lain'];
                        $currentJenis = is_array($actionList->jenis_ternakan) ? $actionList->jenis_ternakan : [];
                        $oldJenis = old('jenis_ternakan', $currentJenis);
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
                        <input type="text" name="jenis_ternakan_lain" value="{{ old('jenis_ternakan_lain', $actionList->jenis_ternakan_lain) }}" class="w-full py-1.5 px-3 rounded-xl border border-slate-300 bg-white">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-600 mb-1">2. Bil. Ternakan Dirawat (ekor):</label>
                        <input type="number" name="bil_ternakan" value="{{ old('bil_ternakan', $actionList->bil_ternakan) }}" min="0" class="w-full py-1.5 px-3 rounded-xl border border-slate-300 bg-white font-mono font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-600 mb-1">3. Bil. Yang Ada di Ladang/Premis (ekor):</label>
                        <input type="number" name="bil_yang_ada" value="{{ old('bil_yang_ada', $actionList->bil_yang_ada) }}" min="0" class="w-full py-1.5 px-3 rounded-xl border border-slate-300 bg-white font-mono font-bold">
                    </div>
                </div>
            </div>

            <!-- 17. Laporan & 21. Penggunaan Ubat -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">17. Laporan Lengkap Tindakan & Rawatan</label>
                    <textarea name="laporan" rows="4" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700 leading-relaxed">{{ old('laporan', $actionList->laporan) }}</textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">21. Penggunaan Ubat & Vaksin</label>
                    <textarea name="penggunaan_ubat" id="ubatTextarea" rows="4" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700 leading-relaxed">{{ old('penggunaan_ubat', $actionList->penggunaan_ubat) }}</textarea>
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
                            <input type="radio" name="kepuasan_pelanggan" value="Puashati" {{ old('kepuasan_pelanggan', $actionList->kepuasan_pelanggan) === 'Puashati' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                            <span class="text-emerald-800">Puashati</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer font-bold text-slate-800">
                            <input type="radio" name="kepuasan_pelanggan" value="Tidak puashati" {{ old('kepuasan_pelanggan', $actionList->kepuasan_pelanggan) === 'Tidak puashati' ? 'checked' : '' }} class="text-rose-600 focus:ring-rose-500">
                            <span class="text-rose-800">Tidak puashati</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer font-bold text-slate-800">
                            <input type="radio" name="kepuasan_pelanggan" value="Boleh dipertimbangkan" {{ old('kepuasan_pelanggan', $actionList->kepuasan_pelanggan) === 'Boleh dipertimbangkan' ? 'checked' : '' }} class="text-amber-600 focus:ring-amber-500">
                            <span class="text-amber-800">Boleh dipertimbangkan</span>
                        </label>
                    </div>
                </div>

                <!-- 16. Cadangan Pelanggan -->
                <div class="lg:col-span-3">
                    <label class="block font-bold text-slate-700 uppercase mb-1">16. Cadangan Pelanggan</label>
                    <input type="text" name="cadangan_pelanggan" value="{{ old('cadangan_pelanggan', $actionList->cadangan_pelanggan) }}" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>

                <!-- 14. Tandatangan Pelanggan -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">14. Nama Penandatangan (Pelanggan)</label>
                    <input type="text" name="tandatangan_pelanggan_nama" value="{{ old('tandatangan_pelanggan_nama', $actionList->tandatangan_pelanggan_nama) }}" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Tandatangan</label>
                    <input type="date" name="tandatangan_pelanggan_tarikh" value="{{ old('tandatangan_pelanggan_tarikh', $actionList->tandatangan_pelanggan_tarikh ? $actionList->tandatangan_pelanggan_tarikh->format('Y-m-d') : '') }}" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Masa Tandatangan</label>
                    <input type="text" name="tandatangan_pelanggan_masa" value="{{ old('tandatangan_pelanggan_masa', $actionList->tandatangan_pelanggan_masa) }}" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                </div>

                <!-- 18. Bayaran & Resit -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">18. Bayaran (RM)</label>
                    <input type="number" step="0.01" name="bayaran" value="{{ old('bayaran', $actionList->bayaran) }}" min="0" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 font-mono font-bold text-slate-800">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">No. Resit Pembayaran</label>
                    <input type="text" name="no_resit" value="{{ old('no_resit', $actionList->no_resit) }}" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 font-mono text-slate-700">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Status Borang</label>
                    <select name="status" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 font-bold text-slate-800">
                        <option value="Selesai" {{ old('status', $actionList->status) === 'Selesai' ? 'selected' : '' }}>Selesai (Telah Ditandatangani)</option>
                        <option value="Deraf" {{ old('status', $actionList->status) === 'Deraf' ? 'selected' : '' }}>Deraf</option>
                        <option value="Disahkan" {{ old('status', $actionList->status) === 'Disahkan' ? 'selected' : '' }}>Disahkan Pegawai Projek</option>
                    </select>
                </div>

                <!-- 19. Pengesahan dan Ulasan -->
                <div class="lg:col-span-3">
                    <label class="block font-bold text-slate-700 uppercase mb-1">19. Pengesahan dan Ulasan Pegawai Projek</label>
                    <textarea name="pengesahan_ulasan_pegawai" rows="2" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">{{ old('pengesahan_ulasan_pegawai', $actionList->pengesahan_ulasan_pegawai) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Butang Simpan Kemaskini -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('action-list.show', $actionList->id) }}" class="px-5 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                Batal
            </a>
            <button type="submit" class="px-7 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-sm shadow-lg transition flex items-center gap-2">
                <i class="fa-solid fa-save"></i> Simpan Perubahan Borang (PK-RK-61)
            </button>
        </div>
    </form>
</div>

<script>
    function dapatkanGpsSemasa() {
        if (!navigator.geolocation) {
            alert('Pelayar anda tidak menyokong geolokasi GPS.');
            return;
        }
        navigator.geolocation.getCurrentPosition(pos => {
            const lat = pos.coords.latitude.toFixed(6);
            const lng = pos.coords.longitude.toFixed(6);
            document.getElementById('gpsKoordinatInput').value = `${lat}, ${lng}`;
        }, err => {
            alert('Gagal mendapatkan lokasi GPS. Sila pastikan kebenaran lokasi diaktifkan.');
        });
    }

    function bukaGoogleMaps() {
        const coords = document.getElementById('gpsKoordinatInput').value.trim();
        if (!coords) {
            alert('Sila masukkan koordinat GPS terlebih dahulu.');
            return;
        }
        window.open(`https://www.google.com/maps?q=${encodeURIComponent(coords)}`, '_blank');
    }
</script>
@endsection
