@extends('layouts.app')

@section('title', 'EPTR - Tambah Rekod Program Kesihatan Ternakan')
@section('page_title', 'EPTR: Tambah Rekod Program Kesihatan & Rawatan Ternakan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    ternakanList: {{ json_encode($ternakanList) }},
    selectedTernakanId: '{{ old('ternakan_id', $selectedTernakan->id ?? '') }}',
    jenisProgram: '{{ old('jenis_program', 'Vaksinasi / Imunisasi') }}',
    
    get selectedTernakan() {
        return this.ternakanList.find(t => t.id == this.selectedTernakanId) || null;
    }
}">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-teal-900 via-slate-900 to-emerald-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex items-center justify-between border border-teal-800/40">
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-teal-500/20 border border-teal-500/40 text-teal-300 text-xs font-bold uppercase tracking-wider mb-2">
                <i class="fa-solid fa-notes-medical"></i> Rekod Kesihatan Veterinar
            </span>
            <h2 class="text-2xl font-black">Borang Rekod Program Kesihatan Ternakan</h2>
            <p class="text-xs text-teal-100/80 mt-1">Daftarkan aktiviti vaksinasi, penyahcacingan, rawatan klinikal, atau surveilans penyakit ternakan</p>
        </div>
        <div class="hidden sm:block text-right">
            <a href="{{ route('eptr.kesihatan.index') }}" class="text-xs font-bold bg-white/10 hover:bg-white/20 text-teal-300 px-3.5 py-2 rounded-xl border border-white/20 transition inline-flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left"></i> Senarai Kesihatan
            </a>
        </div>
    </div>

    <!-- Main Health Record Form -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs">
        <form action="{{ route('eptr.kesihatan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 text-xs">
            @csrf

            <!-- Section 1: Pemilihan Ternakan -->
            <div>
                <div class="flex items-center gap-2 pb-3 mb-4 border-b border-slate-100 text-slate-800 font-bold text-sm">
                    <span class="w-6 h-6 rounded-full bg-teal-100 text-teal-800 flex items-center justify-center text-xs font-black">1</span>
                    <span>Bahagian A: Pemilihan Ternakan Ruminan</span>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Pilih Ternakan Berdaftar (Tag Telinga) <span class="text-rose-500">*</span></label>
                    <select name="ternakan_id" x-model="selectedTernakanId" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none font-bold">
                        <option value="">-- Pilih Ternakan Mengikut No. Tag --</option>
                        @foreach($ternakanList as $t)
                            <option value="{{ $t->id }}" {{ (old('ternakan_id', $selectedTernakan->id ?? '') == $t->id) ? 'selected' : '' }}>
                                Tag: {{ $t->no_tag ?? 'ID-'.$t->id }} &bull; {{ $t->jenis_ternakan }} ({{ $t->baka }}) &bull; Pemunya: {{ $t->pemunya->nama ?? 'N/A' }} &bull; {{ $t->jajahan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Ternakan Preview Box -->
                <template x-if="selectedTernakan">
                    <div class="mt-4 p-4 rounded-2xl bg-teal-50/50 border border-teal-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div>
                            <div class="text-[10px] font-bold text-teal-800 uppercase">Maklumat Ternakan Terpilih:</div>
                            <div class="font-bold text-slate-900 text-sm mt-0.5">
                                No. Tag: <span class="font-mono text-emerald-800" x-text="selectedTernakan.no_tag || 'Tiada Tag'"></span> &bull; 
                                <span class="capitalize" x-text="selectedTernakan.jenis_ternakan"></span> (<span class="capitalize" x-text="selectedTernakan.baka"></span>) &bull; 
                                Jantina: <span x-text="selectedTernakan.jantina"></span>
                            </div>
                            <div class="text-[11px] text-slate-600 mt-0.5">
                                Pemunya: <b x-text="selectedTernakan.pemunya ? selectedTernakan.pemunya.nama : '-'"></b> &bull; 
                                Lokasi: <span x-text="selectedTernakan.jajahan + ' (' + (selectedTernakan.daerah || '-') + ')'"></span>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-teal-100 text-teal-900 border border-teal-300">
                            Status: <span x-text="selectedTernakan.status"></span>
                        </span>
                    </div>
                </template>
            </div>

            <!-- Section 2: Butiran Program Kesihatan & Rawatan -->
            <div class="pt-4 border-t border-slate-100">
                <div class="flex items-center gap-2 pb-3 mb-4 border-b border-slate-100 text-slate-800 font-bold text-sm">
                    <span class="w-6 h-6 rounded-full bg-teal-100 text-teal-800 flex items-center justify-center text-xs font-black">2</span>
                    <span>Bahagian B: Butiran Program Kesihatan & Rawatan</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Jenis Program Kesihatan <span class="text-rose-500">*</span></label>
                        <select name="jenis_program" x-model="jenisProgram" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none font-bold">
                            <option value="Vaksinasi / Imunisasi">Vaksinasi / Imunisasi (FMD/HS/PPR)</option>
                            <option value="Penyahcacingan (Deworming)">Penyahcacingan (Deworming)</option>
                            <option value="Rawatan Penyakit / Klinikal">Rawatan Penyakit / Klinikal</option>
                            <option value="Ujian Saringan Penyakit & Surveilans">Ujian Saringan & Surveilans</option>
                            <option value="Pemberian Vitamin & Suplemen">Pemberian Vitamin & Suplemen</option>
                            <option value="Kawalan Ektoparasit / Semburan">Kawalan Ektoparasit / Kutu / Lalat</option>
                            <option value="Pemeriksaan Kesihatan Berkala">Pemeriksaan Kesihatan Berkala</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Nama Vaksin / Ubat / Rawatan <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_vaksin_atau_ubat" value="{{ old('nama_vaksin_atau_ubat', 'Vaksin FMD (Aftovax)') }}" required placeholder="Contoh: Aftovax, Ivermectin 1%, B-Complex" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none font-medium">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Rawatan / Vaksinasi <span class="text-rose-500">*</span></label>
                        <input type="date" name="tarikh_rawatan" value="{{ old('tarikh_rawatan', date('Y-m-d')) }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none font-bold">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Dos Ulangan / Booster (Pilihan)</label>
                        <input type="date" name="tarikh_ulangan_dos" value="{{ old('tarikh_ulangan_dos') }}" min="{{ date('Y-m-d') }}" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none font-mono">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Dos Diberikan</label>
                        <input type="text" name="dos_diberikan" value="{{ old('dos_diberikan', '2.0 ml Subkutan (SC)') }}" placeholder="Contoh: 2ml SC / 10ml Oral" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none font-mono">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Status Kesihatan Ternakan <span class="text-rose-500">*</span></label>
                        <select name="status_kesihatan" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none font-bold">
                            <option value="Sihat & Cergas">Sihat & Cergas</option>
                            <option value="Stabil">Stabil</option>
                            <option value="Dalam Pemantauan">Dalam Pemantauan / Kuarantin</option>
                            <option value="Sembuh">Sembuh</option>
                            <option value="Kritikal">Kritikal</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Berat Semasa (KG)</label>
                        <input type="number" step="0.1" name="berat_semasa_kg" value="{{ old('berat_semasa_kg', '320.0') }}" placeholder="Contoh: 350.0" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Suhu Badan (&deg;C)</label>
                        <input type="number" step="0.1" name="suhu_badan_celsius" value="{{ old('suhu_badan_celsius', '38.5') }}" placeholder="Normal: 38.0 - 39.0 &deg;C" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Diagnosis / Gejala / Tujuan Program</label>
                        <textarea name="diagnosis_atau_tujuan" rows="2" placeholder="Contoh: Program vaksinasi pencegahan FMD tahunan zon Kota Bharu" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">{{ old('diagnosis_atau_tujuan', 'Program Imunisasi Pencegahan FMD Tahunan') }}</textarea>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Tindakan / Prosedur Rawatan Diberikan</label>
                        <textarea name="tindakan_rawatan" rows="2" placeholder="Contoh: Suntikan vaksin FMD 2ml pada bahagian leher kiri" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">{{ old('tindakan_rawatan', 'Suntikan vaksinasi pencegahan dos primer') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Section 3: Pegawai Pemeriksa & Lampiran -->
            <div class="pt-4 border-t border-slate-100">
                <div class="flex items-center gap-2 pb-3 mb-4 border-b border-slate-100 text-slate-800 font-bold text-sm">
                    <span class="w-6 h-6 rounded-full bg-teal-100 text-teal-800 flex items-center justify-center text-xs font-black">3</span>
                    <span>Bahagian C: Pegawai Pemeriksa & Lokasi</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Nama Pegawai Veterinar / Pemeriksa <span class="text-rose-500">*</span></label>
                        <input type="text" name="pegawai_pemeriksa" value="{{ old('pegawai_pemeriksa', $user->name) }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Lokasi Pemeriksaan / Kandang</label>
                        <input type="text" name="lokasi_pemeriksaan" value="{{ old('lokasi_pemeriksaan', 'Kandang Utama Pemunya') }}" placeholder="Contoh: Kandang Kg Padang Kala" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Nasihat & Syor Veterinar</label>
                        <input type="text" name="catatan_dan_syor" value="{{ old('catatan_dan_syor') }}" placeholder="Contoh: Rehatkan ternakan 24 jam, sediakan air bersih mencukupi" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Muat Naik Lampiran Laporan / Gambar (Pilihan)</label>
                        <input type="file" name="dokumen_lampiran" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white">
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('eptr.kesihatan.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-7 py-2.5 bg-teal-700 hover:bg-teal-800 text-white font-black text-sm rounded-xl shadow-lg shadow-teal-700/20 transition flex items-center gap-2">
                    <i class="fa-solid fa-save"></i>
                    <span>Simpan Rekod Program Kesihatan</span>
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
