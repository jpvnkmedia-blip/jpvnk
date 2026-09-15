@extends('layouts.app')

@section('title', Auth::user()->isStaff() ? 'Daftar Perjanjian Lembu Pawah' : 'Permohonan Program Pawah Ternakan')
@section('page_title', Auth::user()->isStaff() ? 'Surat Perjanjian Lembu Pawah (Pendaftaran Baru)' : 'Permohonan Program Pawah Ternakan Negeri Kelantan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    @if(!Auth::user()->isStaff())
    <!-- Public Applicant Banner -->
    <div class="bg-gradient-to-r from-emerald-900 via-teal-900 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex items-center justify-between">
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs font-bold uppercase mb-2">
                <i class="fa-solid fa-handshake-angle"></i> Bantuan Skim Pawah Ternakan JPVNK
            </span>
            <h2 class="text-2xl font-black">Borang Permohonan Program Pawah Ternakan</h2>
            <p class="text-xs text-emerald-100/80 mt-1">Skim pembiakan lembu pawah bagi penternak dan orang awam Negeri Kelantan</p>
        </div>
        <div class="hidden sm:block text-right">
            <span class="text-xs font-mono bg-emerald-500/30 text-emerald-200 px-3 py-1.5 rounded-xl border border-emerald-400/40">PERMOHONAN AWAM</span>
        </div>
    </div>

    <!-- Public Application Form -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs">
        <form action="{{ route('pawah.store') }}" method="POST" class="space-y-6 text-xs">
            @csrf

            <!-- Bahagian 1: Profil Pemohon -->
            <div>
                <div class="flex items-center gap-2 pb-3 mb-4 border-b border-slate-100 text-slate-800 font-bold text-sm">
                    <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs">1</span>
                    <span>Bahagian 1: Maklumat Pemohon / Penternak</span>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <span class="text-slate-400 block text-[11px]">Nama Penuh Pemohon:</span>
                        <span class="font-bold text-slate-900 text-sm">{{ Auth::user()->name }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">No. Kad Pengenalan:</span>
                        <span class="font-mono font-bold text-slate-900">{{ Auth::user()->ic_number ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">No. Telefon Bimbit:</span>
                        <span class="font-medium text-slate-800">{{ Auth::user()->phone ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">Emel Berdaftar:</span>
                        <span class="font-medium text-slate-800">{{ Auth::user()->email }}</span>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="text-slate-400 block text-[11px]">Alamat Kediaman / Premis:</span>
                        <span class="font-medium text-slate-800">{{ Auth::user()->address ?? 'Alamat belum dikemaskini' }}</span>
                    </div>
                </div>
            </div>

            @php
                $defaultAdaPengalaman = old('ada_pengalaman', $isEptrRegistered ? 'ya' : ((old('pengalaman_menternak') && old('pengalaman_menternak') !== 'Belum Pernah (Penternak Baru)') ? 'ya' : 'tidak'));
                $defaultBilanganTernakan = old('bilangan_ternakan_sedia_ada', $ternakanEptrCount > 0 ? $ternakanEptrCount : 0);
                $defaultJenisTernakan = old('jenis_ternakan_sedia_ada', !empty($jenisTernakanEptrList) ? implode(', ', $jenisTernakanEptrList) : ($ternakanEptrCount > 0 ? 'Lembu' : ''));
            @endphp

            <!-- Bahagian 2: Maklumat Permohonan & Keadaan Kandang -->
            <div class="pt-4 border-t border-slate-100" x-data="{
                adaPengalaman: '{{ $defaultAdaPengalaman }}',
                tempohPengalaman: '{{ old('pengalaman_menternak', $isEptrRegistered ? '3 - 5 Tahun' : '1 - 3 Tahun') }}'
            }">
                <div class="flex items-center gap-2 pb-3 mb-4 border-b border-slate-100 text-slate-800 font-bold text-sm">
                    <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs">2</span>
                    <span>Bahagian 2: Butiran Permohonan &amp; Maklumat Pengalaman Menternak</span>
                </div>

                @if($isEptrRegistered)
                <div class="mb-4 p-4 rounded-2xl bg-emerald-50 border-2 border-emerald-300 text-emerald-950 flex items-start gap-3 shadow-xs">
                    <div class="w-9 h-9 rounded-xl bg-emerald-200 text-emerald-800 flex items-center justify-center text-base shrink-0 mt-0.5">
                        <i class="fa-solid fa-id-card-clip"></i>
                    </div>
                    <div class="space-y-1">
                        <div class="font-bold text-emerald-900 text-sm flex items-center gap-2">
                            <span>Rekod EPTR Dikesan: Pendaftaran Ternakan Sah Ditemui</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-700 text-white">EPTR Aktif</span>
                        </div>
                        <p class="text-xs text-emerald-800 leading-relaxed">
                            Maklumat No. Kad Pengenalan anda dikesan mempunyai <b>{{ $ternakanEptrCount }} ekor ternakan</b> berdaftar di bawah sistem EPTR (Kad Kuning). Pilihan <b>Ada Pengalaman Menternak</b> telah dipilih secara automatik berserta butiran ternakan sedia ada anda.
                        </p>
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Jenis Pawah Dimohon</label>
                        <select name="jenis_pawah" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none font-bold text-emerald-950">
                            <option value="Lembu Hibrid" {{ old('jenis_pawah') == 'Lembu Hibrid' ? 'selected' : '' }}>🐄 Lembu Hibrid</option>
                            <option value="Kambing Tenusu" {{ old('jenis_pawah') == 'Kambing Tenusu' ? 'selected' : '' }}>🐐 Kambing Tenusu</option>
                            <option value="Rusa" {{ old('jenis_pawah') == 'Rusa' ? 'selected' : '' }}>🦌 Rusa</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Pilih Jajahan Penternakan</label>
                        <select name="jajahan" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none font-bold">
                            @foreach($jajahanList as $j)
                                <option value="{{ $j }}" {{ (old('jajahan') == $j || Auth::user()->jajahan == $j) ? 'selected' : '' }}>{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Pilihan Pengalaman Menternak -->
                <div class="mt-4 p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                    <label class="block font-bold text-slate-800 uppercase text-xs">Pengalaman Menternak</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 p-3.5 rounded-xl border cursor-pointer transition" :class="adaPengalaman === 'ya' ? 'bg-emerald-50/80 border-emerald-500 ring-2 ring-emerald-500/20 shadow-xs' : 'bg-white border-slate-200 hover:bg-slate-100'">
                            <input type="radio" name="ada_pengalaman" value="ya" x-model="adaPengalaman" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 border-slate-300">
                            <div>
                                <span class="font-bold text-slate-900 block flex items-center gap-1.5">
                                    <i class="fa-solid fa-user-check text-emerald-600"></i> Ada Pengalaman Menternak
                                </span>
                                <span class="text-[11px] text-slate-500">Mempunyai ternakan sedia ada / rekod pengalaman menternak</span>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3.5 rounded-xl border cursor-pointer transition" :class="adaPengalaman === 'tidak' ? 'bg-emerald-50/80 border-emerald-500 ring-2 ring-emerald-500/20 shadow-xs' : 'bg-white border-slate-200 hover:bg-slate-100'">
                            <input type="radio" name="ada_pengalaman" value="tidak" x-model="adaPengalaman" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 border-slate-300">
                            <div>
                                <span class="font-bold text-slate-900 block flex items-center gap-1.5">
                                    <i class="fa-solid fa-user-plus text-slate-500"></i> Tiada Pengalaman (Penternak Baru)
                                </span>
                                <span class="text-[11px] text-slate-500">Baru berminat menceburi industri ternakan</span>
                            </div>
                        </label>
                    </div>

                    <!-- Tempoh Pengalaman jika Ada -->
                    <div x-show="adaPengalaman === 'ya'" x-cloak class="pt-2">
                        <label class="block font-bold text-slate-700 uppercase mb-1 text-[11px]">Tempoh Pengalaman Menternak</label>
                        <select name="pengalaman_menternak" x-model="tempohPengalaman" class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none font-medium">
                            <option value="1 - 3 Tahun">1 - 3 Tahun</option>
                            <option value="3 - 5 Tahun">3 - 5 Tahun</option>
                            <option value="Lebih 5 Tahun (Berpengalaman)">Lebih 5 Tahun (Berpengalaman)</option>
                        </select>
                    </div>

                    <input type="hidden" name="pengalaman_menternak" value="Belum Pernah (Penternak Baru)" x-show="adaPengalaman === 'tidak'" :disabled="adaPengalaman === 'ya'">
                </div>

                <!-- Bahagian Textbox Butiran Fasiliti & Ternakan Sedia Ada (Keluar hanya bila klik Ada Pengalaman) -->
                <div x-show="adaPengalaman === 'ya'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="mt-4 p-5 rounded-2xl bg-emerald-50/40 border border-emerald-200 space-y-4">
                    <div class="flex items-center gap-2 text-emerald-900 font-bold text-xs pb-2 border-b border-emerald-200/60">
                        <i class="fa-solid fa-clipboard-list text-emerald-600"></i>
                        <span>Butiran Ternakan Sedia Ada &amp; Fasiliti Tapak Ternakan:</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">Jenis Ternakan Sekarang (Jika Ada)</label>
                            <input type="text" name="jenis_ternakan_sedia_ada" value="{{ $defaultJenisTernakan }}" placeholder="Contoh: Lembu, Kambing, Biri-biri" class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none font-medium">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">Bilangan Ternakan Sedia Ada (Ekor)</label>
                            <input type="number" name="bilangan_ternakan_sedia_ada" min="0" value="{{ $defaultBilanganTernakan }}" placeholder="Contoh: 5" class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none font-bold">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">Keluasan Padang Ragut / Tanah Rumput</label>
                            <input type="text" name="keluasan_padang_ragut" value="{{ old('keluasan_padang_ragut') }}" placeholder="Contoh: 1 Ekar / 0.5 Hektar / Beli rumput potong" class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">Jenis &amp; Keadaan Kandang</label>
                            <input type="text" name="jenis_kandang" value="{{ old('jenis_kandang', 'Kandang Berbumbung Penuh') }}" placeholder="Contoh: Kandang Berbumbung Penuh / Separa Tertutup / Ragutan Berpagar" class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none font-medium">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Sumber Makanan &amp; Bekalan Air</label>
                        <input type="text" name="sumber_makanan" value="{{ old('sumber_makanan') }}" placeholder="Contoh: Rumput Napier, Silaj, Dedak &amp; Air Paip / Graviti" class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block font-bold text-slate-700 uppercase mb-1">Catatan / Justifikasi Permohonan</label>
                    <textarea name="catatan" rows="3" placeholder="Nyatakan sebarang maklumat tambahan mengenai lokasi kandang, matlamat pembiakan atau sokongan lain." class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">{{ old('catatan') }}</textarea>
                </div>
            </div>

            <!-- Bahagian 3: Syarat Program & Perakuan -->
            <div class="pt-4 border-t border-slate-100">
                <div class="flex items-center gap-2 pb-3 mb-4 border-b border-slate-100 text-slate-800 font-bold text-sm">
                    <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs">3</span>
                    <span>Bahagian 3: Terma Program &amp; Perakuan Pemohon</span>
                </div>

                <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 space-y-2 text-emerald-950">
                    <div class="font-bold flex items-center gap-2 text-emerald-900">
                        <i class="fa-solid fa-circle-info text-emerald-600"></i>
                        <span>Syarat Pemulangan Anak di bawah Program Pawah:</span>
                    </div>
                    <p class="text-[11px] leading-relaxed text-slate-700">
                        Peserta yang diluluskan akan dibekalkan lembu induk betina baka terpilih daripada pangkalan data EPTR. Peserta wajib memulangkan <b>1 (satu) ekor anak betina pertama berumur sekurang-kurangnya 12 bulan</b> kepada Jabatan untuk diagihkan kepada peserta pawah seterusnya. Selepas anak diserahkan, induk pawah akan menjadi hak milik mutlak peserta.
                    </p>
                </div>

                <div class="mt-4">
                    <label class="flex items-start gap-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-200 cursor-pointer hover:bg-slate-100 transition">
                        <input type="checkbox" required name="perakuan" value="1" class="mt-0.5 w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                        <span class="text-slate-700 leading-normal">
                            Saya mengaku dan memperakui bahawa segala maklumat yang diberikan adalah benar dan sah. Saya bersedia untuk mematuhi semua peraturan, syarat pemeliharaan, rawatan kesihatan dan pemantauan oleh Pegawai Veterinar JPVNK.
                        </span>
                    </label>
                </div>
            </div>

            <!-- Submit -->
            <div class="pt-4 flex items-center justify-end gap-3">
                <a href="{{ route('pawah.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 font-bold text-slate-700 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-lg shadow-emerald-700/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Hantar Permohonan Program Pawah</span>
                </button>
            </div>
        </form>
    </div>

    @else
    <!-- Staff / Admin Agreement Form -->
    <div class="bg-gradient-to-r from-emerald-900 via-teal-900 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex items-center justify-between">
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs font-bold uppercase mb-2">
                <i class="fa-solid fa-file-contract"></i> Program Pawah JPVNK
            </span>
            <h2 class="text-2xl font-black">Pendaftaran Surat Perjanjian Lembu Pawah</h2>
            <p class="text-xs text-emerald-100/80 mt-1">Mengambil dan memautkan ternakan induk berdaftar di bawah sistem EPTR</p>
        </div>
        <div class="hidden sm:block text-right">
            <span class="text-xs font-mono bg-white/10 px-3 py-1.5 rounded-xl border border-white/20">DOKUMEN KONTRAK</span>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs">
        <form action="{{ route('pawah.store') }}" method="POST" class="space-y-6 text-xs">
            @csrf

            <!-- Peserta Pawah -->
            <div>
                <div class="flex items-center gap-2 pb-3 mb-4 border-b border-slate-100 text-slate-800 font-bold text-sm">
                    <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs">1</span>
                    <span>Bahagian 1: Maklumat Peserta Penerima Pawah</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Pilih Peserta (Pengguna Berdaftar)</label>
                        <select name="user_id" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            <option value="">-- Pilih Peserta --</option>
                            @foreach($pesertaList as $p)
                                <option value="{{ $p->id }}" {{ old('user_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }} (No KP: {{ $p->ic_number }} &bull; {{ $p->jajahan }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Jajahan Projek Pawah</label>
                        <select name="jajahan" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            @foreach($jajahanList as $j)
                                <option value="{{ $j }}" {{ old('jajahan') == $j ? 'selected' : '' }}>{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Nama Skim / Program Pawah</label>
                        <input type="text" name="nama_program" value="{{ old('nama_program', 'Program Pawah Ternakan Negeri Kelantan') }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none font-medium">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Pegawai Penyelia Bertugas</label>
                        <input type="text" name="pegawai_penyelia" value="{{ old('pegawai_penyelia', Auth::user()->name) }}" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Lembu Induk EPTR -->
            <div class="pt-4 border-t border-slate-100">
                <div class="flex items-center gap-2 pb-3 mb-4 border-b border-slate-100 text-slate-800 font-bold text-sm">
                    <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs">2</span>
                    <span>Bahagian 2: Pemilihan Lembu Induk Dari Pangkalan Data EPTR</span>
                </div>
                <p class="text-[11px] text-slate-500 mb-3">Pilih satu atau lebih ternakan betina berdaftar di bawah sistem EPTR untuk dijadikan induk di bawah perjanjian ini:</p>

                <div class="max-h-60 overflow-y-auto border border-slate-200 rounded-2xl divide-y divide-slate-100 bg-slate-50/50 p-2">
                    @forelse($availableTernakan as $ternakan)
                        <label class="p-3 rounded-xl hover:bg-emerald-50 transition flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="ternakan_ids[]" value="{{ $ternakan->id }}" class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                            <div class="flex-1">
                                <div class="font-bold text-slate-900 font-mono text-sm flex items-center gap-2">
                                    <span>{{ $ternakan->no_tag }}</span>
                                    <span class="text-xs font-sans font-semibold px-2 py-0.5 rounded bg-amber-100 text-amber-800 border border-amber-200">{{ $ternakan->baka }}</span>
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    Pemunya Asal: {{ $ternakan->pemunya->nama ?? '-' }} &bull; Umur: {{ $ternakan->umur ?? '-' }} &bull; Lokasi: {{ $ternakan->jajahan }}
                                </div>
                            </div>
                        </label>
                    @empty
                        <div class="p-6 text-center text-slate-400 text-xs">
                            Tiada ternakan lembu betina aktif dijumpai dalam sistem EPTR. Sila daftar ternakan di modul EPTR terlebih dahulu.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Terma & Tempoh Perjanjian -->
            <div class="pt-4 border-t border-slate-100">
                <div class="flex items-center gap-2 pb-3 mb-4 border-b border-slate-100 text-slate-800 font-bold text-sm">
                    <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs">3</span>
                    <span>Bahagian 3: Tempoh Kontrak &amp; Terma Pemulangan Anak Pawah</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Mula Kontrak Perjanjian</label>
                        <input type="date" name="tarikh_mula" value="{{ old('tarikh_mula', date('Y-m-d')) }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Tempoh Perjanjian (Tahun)</label>
                        <input type="number" name="tempoh_tahun" min="1" max="10" value="{{ old('tempoh_tahun', 3) }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none font-bold">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block font-bold text-slate-700 uppercase mb-1">Syarat Pemulangan Anak Pawah / Terma Kontrak</label>
                    <textarea name="syarat_pemulangan" rows="3" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">{{ old('syarat_pemulangan', 'Peserta wajib memulangkan 1 (satu) ekor anak betina pertama berumur sekurang-kurangnya 12 bulan kepada Jabatan untuk diagihkan kepada peserta pawah seterusnya. Selepas anak diserahkan, induk pawah akan menjadi hak milik penuh peserta.') }}</textarea>
                </div>

                <div class="mt-4">
                    <label class="block font-bold text-slate-700 uppercase mb-1">Catatan Tambahan</label>
                    <input type="text" name="catatan" value="{{ old('catatan') }}" placeholder="Catatan syarat kandang / pemeriksaan" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
            </div>

            <!-- Submit -->
            <div class="pt-4 flex items-center justify-end gap-3">
                <a href="{{ route('pawah.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 font-bold text-slate-700 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-lg shadow-emerald-700/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-file-contract"></i>
                    <span>Daftar Surat Perjanjian Pawah</span>
                </button>
            </div>
        </form>
    </div>
    @endif

</div>
@endsection
