@extends('layouts.app')

@section('title', 'Permohonan Pemindahan Ternakan Baharu')
@section('page_title', 'Borang Permohonan Pemindahan Ternakan')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Back Button & Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('eptr.pemindahan.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-indigo-600 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Senarai Pemindahan</span>
        </a>
        <div class="text-xs font-semibold text-slate-500">
            Status Pengguna: <span class="px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700 font-bold">{{ $user->role_label ?? 'Orang Awam' }}</span>
        </div>
    </div>

    @php
        $defaultNama = old('pemohon_nama', $currentPemunya->nama ?? ($user->name ?? ''));
        $defaultIc = old('pemohon_ic', $currentPemunya->no_kp ?? ($user->ic_number ?? ''));
        $defaultTel = old('pemohon_tel', $currentPemunya->no_telefon ?? ($user->phone ?? ''));
        $defaultAlamat = old('pemohon_alamat', $currentPemunya->alamat ?? ($user->address ?? ''));
        $defaultIdPremis = old('pemohon_id_premis', $currentPemunya->id_premis ?? ($currentPemunya ? 'D' . str_pad($currentPemunya->id, 5, '0', STR_PAD_LEFT) : ''));
        $defaultJajahan = old('jajahan_asal', $currentPemunya->jajahan ?? ($user->jajahan ?? 'Bachok'));
        
        $defaultPenerimaNama = old('penerima_nama', $defaultNama);
        $defaultPenerimaIc = old('penerima_ic', $defaultIc);
        $defaultPenerimaTel = old('penerima_tel', $defaultTel);
        $defaultPenerimaAlamat = old('penerima_alamat', $defaultAlamat);
        $defaultPenerimaIdPremis = old('penerima_id_premis', $defaultIdPremis);
        $defaultPenerimaJajahan = old('penerima_jajahan', $defaultJajahan);
    @endphp

    <form action="{{ route('eptr.pemindahan.store') }}" method="POST" id="pemindahanForm" class="space-y-6">
        @csrf

        <!-- Card 1: Maklumat Rujukan & Jajahan Asal -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 sm:p-8 space-y-6">
            <div class="flex items-center gap-3 border-b border-slate-200 dark:border-slate-700 pb-4">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">
                    1
                </div>
                <div>
                    <h2 class="text-lg font-black text-slate-800 dark:text-white">Maklumat Asas Permohonan &amp; Rujukan</h2>
                    <p class="text-xs text-slate-500">Tetapan jajahan asal, no rujukan rasmi, dan tujuan permohonan</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">
                        No. Rujukan Permit <span class="text-rose-500">*</span>
                    </label>
                    @if($isStaff)
                        <input type="text" name="no_rujukan" id="no_rujukan" value="{{ old('no_rujukan', $suggestedNoRujukan) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white font-mono font-bold text-sm focus:ring-2 focus:ring-indigo-500">
                        <p class="text-[11px] text-slate-400 mt-1">Pegawai boleh menyunting no rujukan ini jika perlu.</p>
                    @else
                        <div class="relative">
                            <input type="text" value="[ Dijana secara automatik oleh sistem ]" disabled class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-100 text-slate-500 font-mono font-bold text-xs cursor-not-allowed">
                            <div class="absolute right-3 top-2.5 text-slate-400">
                                <i class="fa-solid fa-lock text-xs"></i>
                            </div>
                        </div>
                        <input type="hidden" name="no_rujukan" value="">
                        <p class="text-[11px] text-emerald-600 font-medium mt-1">
                            <i class="fa-solid fa-circle-info"></i> No. rujukan permit akan dijana secara automatik mengikut Jajahan anda.
                        </p>
                    @endif
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">Jajahan Asal (Pejabat JPV) <span class="text-rose-500">*</span></label>
                    <select name="jajahan_asal" id="jajahan_asal" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-indigo-500">
                        @foreach($jajahanList as $j)
                            <option value="{{ $j }}" {{ $defaultJajahan === $j ? 'selected' : '' }}>{{ $j }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">Tarikh Permohonan <span class="text-rose-500">*</span></label>
                    <input type="date" name="tarikh_permohonan" value="{{ old('tarikh_permohonan', date('Y-m-d')) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">Tujuan Pemindahan <span class="text-rose-500">*</span></label>
                    <select name="tujuan_pemindahan" id="tujuan_pemindahan" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-indigo-500">
                        <option value="PEMELIHARAAN" {{ old('tujuan_pemindahan') === 'PEMELIHARAAN' ? 'selected' : '' }}>PEMELIHARAAN</option>
                        <option value="SEMBELIHAN" {{ old('tujuan_pemindahan') === 'SEMBELIHAN' ? 'selected' : '' }}>SEMBELIHAN</option>
                        <option value="KORBAN" {{ old('tujuan_pemindahan') === 'KORBAN' ? 'selected' : '' }}>KORBAN</option>
                        <option value="BIAK" {{ old('tujuan_pemindahan') === 'BIAK' ? 'selected' : '' }}>BIAK</option>
                        <option value="JUALAN" {{ old('tujuan_pemindahan') === 'JUALAN' ? 'selected' : '' }}>JUALAN</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">Spesies / Jenis Ternakan <span class="text-rose-500">*</span></label>
                    <select name="jenis_ternakan" id="jenis_ternakan" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-indigo-500">
                        <option value="LEMBU" {{ old('jenis_ternakan') === 'LEMBU' ? 'selected' : '' }}>LEMBU</option>
                        <option value="KERBAU" {{ old('jenis_ternakan') === 'KERBAU' ? 'selected' : '' }}>KERBAU</option>
                        <option value="KAMBING" {{ old('jenis_ternakan') === 'KAMBING' ? 'selected' : '' }}>KAMBING</option>
                        <option value="BIRI-BIRI" {{ old('jenis_ternakan') === 'BIRI-BIRI' ? 'selected' : '' }}>BIRI-BIRI</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">Tarikh Jangka Bertolak / Pindah <span class="text-rose-500">*</span></label>
                    <input type="date" name="tarikh_jangka_pindah" value="{{ old('tarikh_jangka_pindah', date('Y-m-d', strtotime('+7 days'))) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500">
                    <p class="text-[11px] text-slate-400 mt-1">*Sekurang-kurangnya 7 hari sebelum tarikh pemindahan</p>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">No. Pendaftaran Kenderaan / Lori (No. Plat) <span class="text-rose-500">*</span></label>
                <input type="text" name="no_kenderaan" value="{{ old('no_kenderaan') }}" placeholder="Cth: DAB 1234 / CBE 2227" required class="w-full md:w-1/3 px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white uppercase font-mono font-bold text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        <!-- Card 2: Maklumat Pemohon (Penghantar) - Diambil Dari EPTR -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 sm:p-8 space-y-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-700 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                        2
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-800 dark:text-white">Maklumat Pemohon / Penghantar</h2>
                        <p class="text-xs text-slate-500">Maklumat dimuatkan secara automatik dari rekod EPTR</p>
                    </div>
                </div>

                @if($isStaff)
                <div class="w-full md:w-72">
                    <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Pilih Penternak Dari EPTR</label>
                    <select id="select_pemunya" class="w-full px-3 py-2 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-900 text-xs font-bold">
                        <option value="">-- Pilih Pemunya Berdaftar --</option>
                        @foreach($pemunyaList as $p)
                            <option value="{{ $p->id }}" 
                                    data-nama="{{ $p->nama }}" 
                                    data-ic="{{ $p->no_kp }}" 
                                    data-tel="{{ $p->no_telefon }}" 
                                    data-alamat="{{ $p->alamat }}"
                                    data-jajahan="{{ $p->jajahan }}"
                                    data-idpremis="{{ $p->id_premis ?? 'D' . str_pad($p->id, 5, '0', STR_PAD_LEFT) }}"
                                    data-ternakans="{{ json_encode($p->ternakan) }}"
                                    {{ ($currentPemunya && $currentPemunya->id === $p->id) ? 'selected' : '' }}>
                                {{ $p->nama }} ({{ $p->no_kp }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @else
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>Profil EPTR Berdaftar Dikenalpasti</span>
                </div>
                @endif
            </div>

            <input type="hidden" name="pemunya_id" id="pemunya_id" value="{{ old('pemunya_id', $currentPemunya->id ?? '') }}">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">Nama Penuh Pemohon / @Syarikat <span class="text-rose-500">*</span></label>
                    <input type="text" name="pemohon_nama" id="pemohon_nama" value="{{ $defaultNama }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white uppercase font-bold text-sm focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">ID Premis / Ladang Pemohon</label>
                    <input type="text" name="pemohon_id_premis" id="pemohon_id_premis" value="{{ $defaultIdPremis }}" placeholder="Cth: D100038" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white uppercase font-mono font-bold text-sm focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">No. Kad Pengenalan / No. Syarikat <span class="text-rose-500">*</span></label>
                    <input type="text" name="pemohon_ic" id="pemohon_ic" value="{{ $defaultIc }}" placeholder="Cth: 880101035555" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white font-mono text-sm focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">No. Telefon <span class="text-rose-500">*</span></label>
                    <input type="text" name="pemohon_tel" id="pemohon_tel" value="{{ $defaultTel }}" placeholder="Cth: 0199998888" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">Alamat Penuh Lokasi Ternakan / Ladang <span class="text-rose-500">*</span></label>
                <textarea name="pemohon_alamat" id="pemohon_alamat" rows="2" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white uppercase text-sm focus:ring-2 focus:ring-emerald-500">{{ $defaultAlamat }}</textarea>
            </div>
        </div>

        <!-- Card 3: Maklumat Penerima (Destinasi) - Default Maklumat Pemohon -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 sm:p-8 space-y-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 dark:border-slate-700 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-cyan-50 dark:bg-cyan-900/40 text-cyan-600 dark:text-cyan-400 flex items-center justify-center font-bold">
                        3
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-800 dark:text-white">Maklumat Penerima / Destinasi Pemindahan</h2>
                        <p class="text-xs text-slate-500">Secara default disalin dari maklumat pemohon atau disesuaikan mengikut destinasi</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <label class="inline-flex items-center gap-2 text-xs font-bold text-cyan-800 dark:text-cyan-300 cursor-pointer bg-cyan-50 dark:bg-cyan-950/60 px-3 py-1.5 rounded-xl border border-cyan-200 dark:border-cyan-800">
                        <input type="checkbox" id="sync_penerima_checkbox" checked class="rounded border-cyan-400 text-cyan-600 focus:ring-cyan-500">
                        <span>Sama Seperti Maklumat Pemohon</span>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">Nama Penuh Penerima / Pejabat / Premis <span class="text-rose-500">*</span></label>
                    <input type="text" name="penerima_nama" id="penerima_nama" value="{{ $defaultPenerimaNama }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white uppercase font-bold text-sm focus:ring-2 focus:ring-cyan-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">ID Premis Destinasi</label>
                    <input type="text" name="penerima_id_premis" id="penerima_id_premis" value="{{ $defaultPenerimaIdPremis }}" placeholder="Cth: D07191" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white uppercase font-mono font-bold text-sm focus:ring-2 focus:ring-cyan-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">No. IC / Syarikat Penerima</label>
                    <input type="text" name="penerima_ic" id="penerima_ic" value="{{ $defaultPenerimaIc }}" placeholder="Cth: 880227-03-5266" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white font-mono text-sm focus:ring-2 focus:ring-cyan-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">No. Telefon Penerima</label>
                    <input type="text" name="penerima_tel" id="penerima_tel" value="{{ $defaultPenerimaTel }}" placeholder="Cth: 018-2024219" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm focus:ring-2 focus:ring-cyan-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">Negeri Destinasi <span class="text-rose-500">*</span></label>
                    <select name="penerima_negeri" id="penerima_negeri" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-cyan-500">
                        @foreach(['KELANTAN', 'TERENGGANU', 'PAHANG', 'PERAK', 'KEDAH', 'PULAU PINANG', 'PERLIS', 'SELANGOR', 'NEGERI SEMBILAN', 'MELAKA', 'JOHOR', 'KUALA LUMPUR', 'SABAH', 'SARAWAK'] as $neg)
                            <option value="{{ $neg }}" {{ old('penerima_negeri', 'KELANTAN') === $neg ? 'selected' : '' }}>{{ $neg }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5 flex items-center justify-between">
                        <span>Jajahan Destinasi</span>
                        <span id="label_jajahan_dest_badge" class="text-[10px] text-cyan-600 dark:text-cyan-400 font-bold hidden">(Luar Kelantan)</span>
                    </label>
                    <select name="penerima_jajahan" id="penerima_jajahan" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-sm font-semibold focus:ring-2 focus:ring-cyan-500 transition">
                        <option value="Luar Kelantan" id="opt_luar_kelantan" class="hidden">-- Luar Kelantan --</option>
                        @foreach($jajahanList as $j)
                            <option value="{{ $j }}" {{ $defaultPenerimaJajahan === $j ? 'selected' : '' }}>{{ $j }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">Alamat Penuh Lokasi Destinasi <span class="text-rose-500">*</span></label>
                    <textarea name="penerima_alamat" id="penerima_alamat" rows="2" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white uppercase text-sm focus:ring-2 focus:ring-cyan-500">{{ $defaultPenerimaAlamat }}</textarea>
                </div>
            </div>
        </div>

        <!-- Card 4: Maklumat Suntikan & Vaksinasi (FMD & LSD) -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 sm:p-8 space-y-6">
            <div class="flex items-center gap-3 border-b border-slate-200 dark:border-slate-700 pb-4">
                <div class="w-10 h-10 rounded-xl bg-teal-50 dark:bg-teal-900/40 text-teal-600 dark:text-teal-400 flex items-center justify-center font-bold">
                    4
                </div>
                <div>
                    <h2 class="text-lg font-black text-slate-800 dark:text-white">Maklumat Suntikan FMD &amp; Kesihatan Ternakan</h2>
                    <p class="text-xs text-slate-500">Pengesahan suntikan bagi Surat Akuan FMD (Kn. 156) &amp; Deklarasi Kesihatan</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">Suntikan FMD (P1) <span class="text-rose-500">*</span></label>
                    <input type="date" name="tarikh_fmd_p1" value="{{ old('tarikh_fmd_p1', date('Y-m-d', strtotime('-60 days'))) }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-xs font-medium focus:ring-2 focus:ring-teal-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">Suntikan FMD (P2) <span class="text-rose-500">*</span></label>
                    <input type="date" name="tarikh_fmd_p2" value="{{ old('tarikh_fmd_p2', date('Y-m-d', strtotime('-30 days'))) }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-xs font-medium focus:ring-2 focus:ring-teal-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">FMD Booster</label>
                    <input type="date" name="tarikh_fmd_booster" value="{{ old('tarikh_fmd_booster') }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-xs font-medium focus:ring-2 focus:ring-teal-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">Suntikan LSD</label>
                    <input type="date" name="tarikh_lsd" value="{{ old('tarikh_lsd', date('Y-m-d', strtotime('-45 days'))) }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white text-xs font-medium focus:ring-2 focus:ring-teal-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">Nama Penyuntik 1 <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_penyuntik_1" value="{{ old('nama_penyuntik_1', 'NABIL RABANI BIN AHMAD') }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white uppercase font-medium text-sm focus:ring-2 focus:ring-teal-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">Nama Penyuntik 2</label>
                    <input type="text" name="nama_penyuntik_2" value="{{ old('nama_penyuntik_2', 'NUR FARAHTUL NAJWA BT SHAHRUL AZMI') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-slate-700 dark:text-white uppercase font-medium text-sm focus:ring-2 focus:ring-teal-500">
                </div>
            </div>
        </div>

        <!-- Card 5: Senarai 50 Baris Nombor Tag Ternakan (Boleh Pilih dari EPTR) -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 sm:p-8 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200 dark:border-slate-700 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold">
                        5
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-800 dark:text-white">Senarai Pengenalan Ternakan (50 Baris No. Tag)</h2>
                        <p class="text-xs text-slate-500">Pilih dari rekod ternakan EPTR pemohon atau masukkan secara manual</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-500">Kiraan:</span>
                    <span id="badge_jantan" class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-bold text-xs">0 Jantan</span>
                    <span id="badge_betina" class="px-2.5 py-1 rounded-lg bg-pink-50 text-pink-700 font-bold text-xs">0 Betina</span>
                    <span id="badge_total" class="px-2.5 py-1 rounded-lg bg-slate-900 text-white font-bold text-xs">Jumlah: 0 / 50</span>
                </div>
            </div>

            <!-- Panel Pemilihan Ternakan Berdaftar EPTR -->
            <div class="rounded-2xl border-2 border-dashed border-indigo-200 bg-indigo-50/40 p-4 sm:p-5 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-cow text-indigo-600"></i>
                        <span class="text-xs font-extrabold uppercase text-indigo-900 tracking-wide">
                            Pilih Ternakan Dari Rekod EPTR Pemohon
                        </span>
                        <span id="eptr_count_badge" class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-indigo-200 text-indigo-800">
                            {{ count($userTernakanList) }} Ekor Tersedia
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" id="btn_select_all_eptr" class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold transition">
                            <i class="fa-solid fa-check-double mr-1"></i> Pilih Semua
                        </button>
                        <button type="button" id="btn_clear_all_eptr" class="px-3 py-1 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg text-xs font-bold transition">
                            <i class="fa-solid fa-rotate-left mr-1"></i> Kosongkan
                        </button>
                    </div>
                </div>

                <!-- Live Tag Selector Grid -->
                <div id="eptr_livestock_container" class="max-h-48 overflow-y-auto pr-1 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2 pt-1">
                    @forelse($userTernakanList as $t)
                        <label class="eptr-tag-item flex items-center justify-between p-2 rounded-xl border border-indigo-200 bg-white hover:bg-indigo-100/60 cursor-pointer text-xs transition shadow-2xs select-none" data-tag="{{ $t->no_tag }}" data-jantina="{{ $t->jantina ?? 'Betina' }}" data-species="{{ strtoupper($t->jenis_ternakan ?? 'LEMBU') }}">
                            <div class="flex items-center gap-2 min-w-0">
                                <input type="checkbox" class="eptr-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" value="{{ $t->no_tag }}" data-jantina="{{ $t->jantina ?? 'Betina' }}">
                                <div class="min-w-0">
                                    <div class="font-mono font-bold text-slate-900 truncate">{{ $t->no_tag }}</div>
                                    <div class="text-[10px] text-slate-500 truncate">{{ $t->baka ?? $t->jenis_ternakan }}</div>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ strtolower($t->jantina ?? '') === 'jantan' ? 'bg-indigo-100 text-indigo-800' : 'bg-pink-100 text-pink-800' }}">
                                {{ substr($t->jantina ?? 'B', 0, 1) }}
                            </span>
                        </label>
                    @empty
                        <div id="no_livestock_msg" class="col-span-full py-4 text-center text-xs text-slate-500 italic">
                            Tiada ternakan aktif dijumpai untuk pemohon ini. Anda juga boleh memasukkan No Tag secara terus di bawah.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- 50 Rows Tag Table (2 Columns x 25 Rows) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kolum 1: Tag 1 - 25 -->
                <div class="space-y-2 border border-slate-200 dark:border-slate-700 rounded-2xl p-4 bg-slate-50/50 dark:bg-slate-900/20">
                    <h3 class="text-xs font-black uppercase text-indigo-600 tracking-wider mb-2 border-b pb-1 flex items-center justify-between">
                        <span>Kolum 1: Bil 1 - 25</span>
                        <span class="text-[10px] text-slate-400 font-normal">Jantina (J/B) Mengikut Rekod</span>
                    </h3>
                    <div class="space-y-1.5 max-h-96 overflow-y-auto pr-1">
                        @for($i = 1; $i <= 25; $i++)
                        <div class="flex items-center gap-2 tag-row-item" data-row="{{ $i }}">
                            <span class="w-7 text-xs font-bold text-slate-400 text-right">{{ $i }}.</span>
                            <input type="text" 
                                   name="tags[{{ $i }}][no_tag]" 
                                   id="tag_input_{{ $i }}"
                                   value="{{ old("tags.$i.no_tag", '') }}" 
                                   placeholder="No Tag" 
                                   class="tag-input flex-1 px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-xs font-mono font-bold uppercase focus:ring-2 focus:ring-indigo-500">
                            
                            <!-- Jantina dikunci mengikut rekod ternakan -->
                            <select id="tag_gender_display_{{ $i }}" disabled class="tag-gender-display w-24 px-2 py-1.5 rounded-lg border border-slate-200 bg-slate-100 text-slate-700 text-xs font-bold cursor-not-allowed">
                                <option value="Betina" {{ old("tags.$i.jantina") === 'Betina' ? 'selected' : '' }}>Betina (B)</option>
                                <option value="Jantan" {{ old("tags.$i.jantina") === 'Jantan' ? 'selected' : '' }}>Jantan (J)</option>
                            </select>
                            <input type="hidden" name="tags[{{ $i }}][jantina]" id="tag_gender_hidden_{{ $i }}" value="{{ old("tags.$i.jantina", 'Betina') }}" class="tag-gender">
                        </div>
                        @endfor
                    </div>
                </div>

                <!-- Kolum 2: Tag 26 - 50 -->
                <div class="space-y-2 border border-slate-200 dark:border-slate-700 rounded-2xl p-4 bg-slate-50/50 dark:bg-slate-900/20">
                    <h3 class="text-xs font-black uppercase text-indigo-600 tracking-wider mb-2 border-b pb-1 flex items-center justify-between">
                        <span>Kolum 2: Bil 26 - 50</span>
                        <span class="text-[10px] text-slate-400 font-normal">Jantina (J/B) Mengikut Rekod</span>
                    </h3>
                    <div class="space-y-1.5 max-h-96 overflow-y-auto pr-1">
                        @for($i = 26; $i <= 50; $i++)
                        <div class="flex items-center gap-2 tag-row-item" data-row="{{ $i }}">
                            <span class="w-7 text-xs font-bold text-slate-400 text-right">{{ $i }}.</span>
                            <input type="text" 
                                   name="tags[{{ $i }}][no_tag]" 
                                   id="tag_input_{{ $i }}"
                                   value="{{ old("tags.$i.no_tag", '') }}" 
                                   placeholder="No Tag" 
                                   class="tag-input flex-1 px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-700 text-xs font-mono font-bold uppercase focus:ring-2 focus:ring-indigo-500">
                            
                            <!-- Jantina dikunci mengikut rekod ternakan -->
                            <select id="tag_gender_display_{{ $i }}" disabled class="tag-gender-display w-24 px-2 py-1.5 rounded-lg border border-slate-200 bg-slate-100 text-slate-700 text-xs font-bold cursor-not-allowed">
                                <option value="Betina" {{ old("tags.$i.jantina") === 'Betina' ? 'selected' : '' }}>Betina (B)</option>
                                <option value="Jantan" {{ old("tags.$i.jantina") === 'Jantan' ? 'selected' : '' }}>Jantan (J)</option>
                            </select>
                            <input type="hidden" name="tags[{{ $i }}][jantina]" id="tag_gender_hidden_{{ $i }}" value="{{ old("tags.$i.jantina", 'Betina') }}" class="tag-gender">
                        </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 6: Pengiraan Fi Statutori Pemindahan Ternakan -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 sm:p-8 space-y-6">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-violet-50 dark:bg-violet-900/40 text-violet-600 dark:text-violet-400 flex items-center justify-center font-bold">
                        6
                    </div>
                    <div>
                        <h2 class="text-lg font-black text-slate-800 dark:text-white">Pengiraan Fi Statutori Pemindahan Ternakan</h2>
                        <p class="text-xs text-slate-500">Kadar bayaran rasmi Enakmen Pendaftaran Ternakan Ruminan (EPTR)</p>
                    </div>
                </div>
                <a href="{{ route('eptr.jadual-fi') }}" target="_blank" class="text-xs font-bold text-violet-600 dark:text-violet-400 hover:text-violet-700 flex items-center gap-1.5 hover:underline">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    <span>Jadual Fi EPTR</span>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5 flex items-center justify-between">
                        <span>Jenis / Aliran Pemindahan</span>
                        <span class="text-[10px] text-amber-600 dark:text-amber-400 font-bold flex items-center gap-1">
                            <i class="fa-solid fa-lock text-[10px]"></i> Autotetap
                        </span>
                    </label>
                    <select id="kategori_aliran_fi" disabled class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-sm font-bold cursor-not-allowed pointer-events-none">
                        <option value="KELUAR">Pindah Keluar Kelantan (Perenggan 11(1)(c))</option>
                        <option value="MASUK">Pindah Masuk Antara Jajahan (Seksyen 10)</option>
                        <option value="DALAM">Antara Jajahan (Dalam Kelantan)</option>
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1">Dikunci secara automatik mengikut Negeri &amp; Jajahan Destinasi.</p>
                </div>

                <div class="md:col-span-2">
                    <div class="h-full rounded-2xl bg-gradient-to-r from-slate-900 via-indigo-950 to-violet-950 p-4 sm:p-5 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-lg shadow-indigo-950/20">
                        <div class="space-y-1">
                            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-violet-500/20 text-violet-300 text-[10px] font-mono font-bold uppercase tracking-wide border border-violet-500/30" id="fi_peruntukan_badge">
                                Seksyen 8 / Piawai
                            </div>
                            <div class="text-xs text-slate-300 font-medium" id="fi_keterangan_text">
                                Pemindahan Tempatan Antara Jajahan Negeri Kelantan
                            </div>
                            <div class="text-[11px] text-slate-400 font-mono" id="fi_formula_text">
                                0 ekor dipindahkan (Tiada fi statutori tambahan)
                            </div>
                        </div>

                        <div class="text-left sm:text-right border-t sm:border-t-0 pt-2 sm:pt-0 border-white/10">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-violet-300">Jumlah Fi Statutori</div>
                            <div class="text-2xl sm:text-3xl font-black text-amber-300 font-mono tracking-tight" id="fi_grand_total">
                                RM 0.00
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ringkasan Perincian Fi -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs pt-1">
                <div class="p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40">
                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Kategori Ruminan</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200" id="fi_kategori_spesies">Ruminan Besar (LEMBU)</span>
                </div>
                <div class="p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40">
                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Kadar Berkuatkuasa</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200 font-mono" id="fi_kadar_unit">RM 0.00 / ekor</span>
                </div>
                <div class="p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40">
                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Jumlah Ternakan Dipilih</span>
                    <span class="font-bold text-indigo-600 dark:text-indigo-400 font-mono" id="fi_jumlah_ekor">0 Ekor</span>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end gap-3 pb-8">
            <a href="{{ route('eptr.pemindahan.index') }}" class="px-6 py-3 rounded-2xl border border-slate-300 hover:bg-slate-100 text-slate-700 font-bold text-sm transition">
                Batal
            </a>
            <button type="submit" class="px-8 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm shadow-xl shadow-indigo-900/40 transition flex items-center gap-2">
                <i class="fa-solid fa-check"></i>
                <span>Daftar Permohonan Pemindahan</span>
            </button>
        </div>

    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectPemunya = document.getElementById('select_pemunya');
    const syncCheckbox = document.getElementById('sync_penerima_checkbox');

    // Peta ternakan berdaftar untuk mengunci jantina mengikut rekod EPTR
    window.registeredTernakanMap = {};

    @foreach($userTernakanList as $t)
        window.registeredTernakanMap["{{ strtoupper(trim($t->no_tag)) }}"] = {
            no_tag: "{{ $t->no_tag }}",
            jantina: "{{ in_array(strtoupper($t->jantina ?? ''), ['J', 'JANTAN']) ? 'Jantan' : 'Betina' }}",
            baka: "{{ $t->baka ?? $t->jenis_ternakan }}"
        };
    @endforeach

    @foreach($ternakanList as $t)
        if (!window.registeredTernakanMap["{{ strtoupper(trim($t->no_tag)) }}"]) {
            window.registeredTernakanMap["{{ strtoupper(trim($t->no_tag)) }}"] = {
                no_tag: "{{ $t->no_tag }}",
                jantina: "{{ in_array(strtoupper($t->jantina ?? ''), ['J', 'JANTAN']) ? 'Jantan' : 'Betina' }}",
                baka: "{{ $t->baka ?? $t->jenis_ternakan }}"
            };
        }
    @endforeach

    // 1. Sync Penerima from Pemohon
    function syncPenerimaFromPemohon() {
        if (syncCheckbox && syncCheckbox.checked) {
            document.getElementById('penerima_nama').value = document.getElementById('pemohon_nama').value;
            document.getElementById('penerima_ic').value = document.getElementById('pemohon_ic').value;
            document.getElementById('penerima_tel').value = document.getElementById('pemohon_tel').value;
            document.getElementById('penerima_alamat').value = document.getElementById('pemohon_alamat').value;
            document.getElementById('penerima_id_premis').value = document.getElementById('pemohon_id_premis').value;
            
            const asalJajahan = document.getElementById('jajahan_asal').value;
            const penerimaJajahan = document.getElementById('penerima_jajahan');
            for (let i = 0; i < penerimaJajahan.options.length; i++) {
                if (penerimaJajahan.options[i].value.toLowerCase() === asalJajahan.toLowerCase()) {
                    penerimaJajahan.selectedIndex = i;
                    break;
                }
            }

            const penerimaNegeri = document.getElementById('penerima_negeri');
            if (penerimaNegeri) {
                penerimaNegeri.value = 'KELANTAN';
            }

            autoDetectAliranFi();
            updateCounts();
        }
    }

    if (syncCheckbox) {
        syncCheckbox.addEventListener('change', function() {
            if (this.checked) syncPenerimaFromPemohon();
        });
    }

    ['pemohon_nama', 'pemohon_ic', 'pemohon_tel', 'pemohon_alamat', 'pemohon_id_premis'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', function() {
            if (syncCheckbox && syncCheckbox.checked) syncPenerimaFromPemohon();
        });
    });

    const jajahanAsalEl = document.getElementById('jajahan_asal');
    if (jajahanAsalEl) {
        jajahanAsalEl.addEventListener('change', function() {
            if (syncCheckbox && syncCheckbox.checked) syncPenerimaFromPemohon();
        });
    }

    // 2. Pemunya Selection (For Staff)
    if (selectPemunya) {
        selectPemunya.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            if (opt.value) {
                document.getElementById('pemunya_id').value = opt.value;
                document.getElementById('pemohon_nama').value = opt.getAttribute('data-nama') || '';
                document.getElementById('pemohon_ic').value = opt.getAttribute('data-ic') || '';
                document.getElementById('pemohon_tel').value = opt.getAttribute('data-tel') || '';
                document.getElementById('pemohon_alamat').value = opt.getAttribute('data-alamat') || '';
                document.getElementById('pemohon_id_premis').value = opt.getAttribute('data-idpremis') || '';
                
                const jajahan = opt.getAttribute('data-jajahan');
                if (jajahan) {
                    const jajahanSelect = document.getElementById('jajahan_asal');
                    for (let i = 0; i < jajahanSelect.options.length; i++) {
                        if (jajahanSelect.options[i].value.toLowerCase() === jajahan.toLowerCase()) {
                            jajahanSelect.selectedIndex = i;
                            break;
                        }
                    }
                }

                // Sync Penerima
                syncPenerimaFromPemohon();

                // Reload EPTR Livestock container
                try {
                    const rawTernakan = opt.getAttribute('data-ternakans');
                    const ternakanArr = JSON.parse(rawTernakan || '[]');
                    renderEptrLivestockList(ternakanArr);
                } catch (e) {
                    console.error("Error parsing ternakan data", e);
                }
            }
        });
    }

    // 3. Render EPTR Livestock Checkboxes
    function renderEptrLivestockList(ternakanArr) {
        const container = document.getElementById('eptr_livestock_container');
        const badge = document.getElementById('eptr_count_badge');
        if (!container) return;

        container.innerHTML = '';
        if (badge) badge.innerText = (ternakanArr ? ternakanArr.length : 0) + ' Ekor Tersedia';

        if (!ternakanArr || ternakanArr.length === 0) {
            container.innerHTML = '<div class="col-span-full py-4 text-center text-xs text-slate-500 italic">Tiada ternakan berdaftar dijumpai bagi pemohon ini.</div>';
            return;
        }

        ternakanArr.forEach(t => {
            const rawJ = t.jantina || 'Betina';
            const jantina = (rawJ.toUpperCase() === 'J' || rawJ.toUpperCase() === 'JANTAN') ? 'Jantan' : 'Betina';
            const isJantan = jantina === 'Jantan';
            
            // Register into map
            window.registeredTernakanMap[t.no_tag.trim().toUpperCase()] = {
                no_tag: t.no_tag,
                jantina: jantina,
                baka: t.baka || t.jenis_ternakan
            };

            const label = document.createElement('label');
            label.className = 'eptr-tag-item flex items-center justify-between p-2 rounded-xl border border-indigo-200 bg-white hover:bg-indigo-100/60 cursor-pointer text-xs transition shadow-2xs select-none';
            label.setAttribute('data-tag', t.no_tag);
            label.setAttribute('data-jantina', jantina);
            label.innerHTML = `
                <div class="flex items-center gap-2 min-w-0">
                    <input type="checkbox" class="eptr-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" value="${t.no_tag}" data-jantina="${jantina}">
                    <div class="min-w-0">
                        <div class="font-mono font-bold text-slate-900 truncate">${t.no_tag}</div>
                        <div class="text-[10px] text-slate-500 truncate">${t.baka || t.jenis_ternakan || 'Ternakan'}</div>
                    </div>
                </div>
                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded ${isJantan ? 'bg-indigo-100 text-indigo-800' : 'bg-pink-100 text-pink-800'}">
                    ${jantina.substr(0, 1).toUpperCase()}
                </span>
            `;
            container.appendChild(label);
        });

        attachEptrCheckboxEvents();
    }

    // 4. Attach Checkbox Events
    function attachEptrCheckboxEvents() {
        document.querySelectorAll('.eptr-checkbox').forEach(cb => {
            cb.addEventListener('change', function() {
                const tagVal = this.value;
                const genderVal = this.getAttribute('data-jantina') || 'Betina';

                if (this.checked) {
                    // Insert into first empty row (1 to 50)
                    let inserted = false;
                    for (let i = 1; i <= 50; i++) {
                        const input = document.getElementById('tag_input_' + i);
                        if (input && input.value.trim() === '') {
                            input.value = tagVal;
                            setRowGender(i, genderVal);
                            inserted = true;
                            break;
                        }
                    }
                    if (!inserted) {
                        alert("Maksimum 50 baris tag telah dipenuhi.");
                        this.checked = false;
                    }
                } else {
                    // Remove from row
                    for (let i = 1; i <= 50; i++) {
                        const input = document.getElementById('tag_input_' + i);
                        if (input && input.value.trim().toUpperCase() === tagVal.trim().toUpperCase()) {
                            input.value = '';
                            setRowGender(i, 'Betina');
                            break;
                        }
                    }
                }
                updateCounts();
            });
        });
    }

    function setRowGender(rowIdx, gender) {
        const normalized = (gender.toUpperCase() === 'J' || gender.toUpperCase() === 'JANTAN') ? 'Jantan' : 'Betina';
        const display = document.getElementById('tag_gender_display_' + rowIdx);
        const hidden = document.getElementById('tag_gender_hidden_' + rowIdx);
        if (display) display.value = normalized;
        if (hidden) hidden.value = normalized;
    }

    attachEptrCheckboxEvents();

    // 5. Select All & Clear All Buttons
    const btnSelectAll = document.getElementById('btn_select_all_eptr');
    if (btnSelectAll) {
        btnSelectAll.addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('.eptr-checkbox');
            let count = 0;
            checkboxes.forEach((cb, idx) => {
                if (count < 50) {
                    cb.checked = true;
                    const rowIdx = count + 1;
                    const input = document.getElementById('tag_input_' + rowIdx);
                    if (input) {
                        input.value = cb.value;
                        setRowGender(rowIdx, cb.getAttribute('data-jantina') || 'Betina');
                    }
                    count++;
                }
            });
            updateCounts();
        });
    }

    const btnClearAll = document.getElementById('btn_clear_all_eptr');
    if (btnClearAll) {
        btnClearAll.addEventListener('click', function() {
            document.querySelectorAll('.eptr-checkbox').forEach(cb => cb.checked = false);
            for (let i = 1; i <= 50; i++) {
                const input = document.getElementById('tag_input_' + i);
                if (input) input.value = '';
                setRowGender(i, 'Betina');
            }
            updateCounts();
        });
    }

    // 6. Real-time Counter Update & Auto Gender Lookup
    function updateCounts() {
        let j = 0;
        let b = 0;
        let total = 0;
        const currentFilledTags = [];

        for (let i = 1; i <= 50; i++) {
            const input = document.getElementById('tag_input_' + i);
            if (input) {
                const val = input.value.trim().toUpperCase();
                if (val !== '') {
                    total++;
                    currentFilledTags.push(val);

                    // If tag is in registered livestock map, force gender from record
                    if (window.registeredTernakanMap[val]) {
                        const rec = window.registeredTernakanMap[val];
                        setRowGender(i, rec.jantina);
                    }

                    const hidden = document.getElementById('tag_gender_hidden_' + i);
                    const gender = hidden ? hidden.value : 'Betina';
                    if (gender.toLowerCase() === 'jantan') {
                        j++;
                    } else {
                        b++;
                    }
                }
            }
        }

        // Sync checkbox checked state with grid inputs
        document.querySelectorAll('.eptr-checkbox').forEach(cb => {
            cb.checked = currentFilledTags.includes(cb.value.trim().toUpperCase());
        });

        document.getElementById('badge_jantan').innerText = j + ' Jantan';
        document.getElementById('badge_betina').innerText = b + ' Betina';
        document.getElementById('badge_total').innerText = 'Jumlah: ' + total + ' / 50';

        // Update statutory fee calculation
        updateFeeCalculation(total);
    }

    let previousKelantanJajahan = "{{ $defaultPenerimaJajahan }}";

    // 7. Pengesanan Automatik Aliran Fi Mengikut Destinasi & Jajahan
    function autoDetectAliranFi() {
        const negeriEl = document.getElementById('penerima_negeri');
        const jajahanDestEl = document.getElementById('penerima_jajahan');
        const optLuarKelantan = document.getElementById('opt_luar_kelantan');
        const badgeLuarKelantan = document.getElementById('label_jajahan_dest_badge');
        const jajahanAsalEl = document.getElementById('jajahan_asal');
        const aliranEl = document.getElementById('kategori_aliran_fi');
        if (!aliranEl) return;

        const negeri = (negeriEl ? negeriEl.value : 'KELANTAN').trim().toUpperCase();
        const asalJajahan = (jajahanAsalEl ? jajahanAsalEl.value : '').trim().toLowerCase();

        if (negeri !== 'KELANTAN') {
            if (jajahanDestEl) {
                if (jajahanDestEl.value !== 'Luar Kelantan') {
                    previousKelantanJajahan = jajahanDestEl.value;
                }
                if (optLuarKelantan) optLuarKelantan.classList.remove('hidden');
                jajahanDestEl.value = 'Luar Kelantan';
                jajahanDestEl.disabled = true;
                jajahanDestEl.classList.add('bg-slate-100', 'dark:bg-slate-800', 'text-slate-400', 'cursor-not-allowed');
            }
            if (badgeLuarKelantan) badgeLuarKelantan.classList.remove('hidden');
            aliranEl.value = 'KELUAR';
        } else {
            if (jajahanDestEl) {
                jajahanDestEl.disabled = false;
                jajahanDestEl.classList.remove('bg-slate-100', 'dark:bg-slate-800', 'text-slate-400', 'cursor-not-allowed');
                if (optLuarKelantan) optLuarKelantan.classList.add('hidden');
                if (jajahanDestEl.value === 'Luar Kelantan' || !jajahanDestEl.value) {
                    jajahanDestEl.value = previousKelantanJajahan || (jajahanAsalEl ? jajahanAsalEl.value : 'Bachok');
                }
            }
            if (badgeLuarKelantan) badgeLuarKelantan.classList.add('hidden');

            const destJajahan = (jajahanDestEl ? jajahanDestEl.value : '').trim().toLowerCase();
            if (destJajahan && asalJajahan && destJajahan !== asalJajahan) {
                aliranEl.value = 'MASUK';
            } else if (destJajahan && asalJajahan && destJajahan === asalJajahan) {
                aliranEl.value = 'DALAM';
            } else {
                aliranEl.value = 'MASUK';
            }
        }
    }

    // 8. Pengiraan Fi Statutori Pemindahan Ternakan (Jadual Fi EPTR)
    function updateFeeCalculation(totalCount) {
        const jenisEl = document.getElementById('jenis_ternakan');
        const jenisTernakan = (jenisEl ? jenisEl.value : 'LEMBU').toUpperCase();
        const isBesar = (jenisTernakan === 'LEMBU' || jenisTernakan === 'KERBAU');
        
        const negeriEl = document.getElementById('penerima_negeri');
        const penerimaNegeri = (negeriEl ? negeriEl.value : 'KELANTAN').toUpperCase();

        const jajahanDestEl = document.getElementById('penerima_jajahan');
        const destJajahan = (jajahanDestEl ? jajahanDestEl.value : '').trim().toLowerCase();
        const jajahanAsalEl = document.getElementById('jajahan_asal');
        const asalJajahan = (jajahanAsalEl ? jajahanAsalEl.value : '').trim().toLowerCase();
        
        const aliranEl = document.getElementById('kategori_aliran_fi');
        const kategoriAliran = (aliranEl ? aliranEl.value : 'AUTO');

        let mode = 'DALAM';
        if (kategoriAliran === 'AUTO') {
            if (penerimaNegeri !== 'KELANTAN') {
                mode = 'KELUAR';
            } else if (destJajahan && asalJajahan && destJajahan !== asalJajahan) {
                mode = 'MASUK';
            } else {
                mode = 'DALAM';
            }
        } else {
            mode = kategoriAliran;
        }

        let peruntukan = 'Seksyen 8 / Piawai';
        let keterangan = 'Pemindahan Tempatan Antara Jajahan Negeri Kelantan';
        let kadarText = 'RM 0.00 / ekor';
        let formulaText = totalCount + ' ekor dipindahkan (Tiada fi pembatalan keluar negeri)';
        let totalFi = 0.00;

        if (mode === 'KELUAR') {
            peruntukan = 'Perenggan 11(1)(c)';
            keterangan = 'Bayaran Pembatalan Akibat Pindah Keluar Negeri Kelantan';
            const rate = isBesar ? 20.00 : 10.00;
            kadarText = 'RM ' + rate.toFixed(2) + ' / ekor';
            totalFi = totalCount * rate;
            formulaText = totalCount + ' ekor × RM ' + rate.toFixed(2) + ' = RM ' + totalFi.toFixed(2);
        } else if (mode === 'MASUK') {
            peruntukan = 'Seksyen 10';
            keterangan = 'Pendaftaran & Konsainan Pindah Masuk (Antara Jajahan Dalam Negeri)';
            const konsainanRate = isBesar ? 10.00 : 8.00;
            kadarText = 'Daftar RM 2.00/ekor + Konsainan RM ' + konsainanRate.toFixed(2);
            totalFi = totalCount > 0 ? ((totalCount * 2.00) + konsainanRate) : 0.00;
            formulaText = totalCount > 0 
                ? ('(' + totalCount + ' ekor × RM 2.00) + Konsainan RM ' + konsainanRate.toFixed(2) + ' = RM ' + totalFi.toFixed(2))
                : '0 ekor dipilih = RM 0.00';
        } else {
            peruntukan = 'Seksyen 8 / Piawai';
            keterangan = 'Pemindahan Tempatan Antara Jajahan (Dalam Negeri Kelantan)';
            kadarText = 'RM 0.00 / ekor';
            totalFi = 0.00;
            formulaText = totalCount + ' ekor dipindahkan (Tiada fi statutori tambahan)';
        }

        const badgeEl = document.getElementById('fi_peruntukan_badge');
        const ketEl = document.getElementById('fi_keterangan_text');
        const formEl = document.getElementById('fi_formula_text');
        const grandEl = document.getElementById('fi_grand_total');
        const katEl = document.getElementById('fi_kategori_spesies');
        const unitEl = document.getElementById('fi_kadar_unit');
        const countEl = document.getElementById('fi_jumlah_ekor');

        if (badgeEl) badgeEl.innerText = peruntukan;
        if (ketEl) ketEl.innerText = keterangan;
        if (formEl) formEl.innerText = formulaText;
        if (grandEl) grandEl.innerText = 'RM ' + totalFi.toFixed(2);
        if (katEl) katEl.innerText = (isBesar ? 'Ruminan Besar (' : 'Ruminan Kecil (') + jenisTernakan + ')';
        if (unitEl) unitEl.innerText = kadarText;
        if (countEl) countEl.innerText = totalCount + ' Ekor';
    }

    for (let i = 1; i <= 50; i++) {
        const input = document.getElementById('tag_input_' + i);
        if (input) {
            input.addEventListener('input', updateCounts);
            input.addEventListener('change', updateCounts);
        }
    }

    ['jenis_ternakan', 'kategori_aliran_fi'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('change', updateCounts);
        }
    });

    ['penerima_negeri', 'penerima_jajahan', 'jajahan_asal'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('change', function() {
                autoDetectAliranFi();
                updateCounts();
            });
        }
    });

    const pemindahanForm = document.getElementById('pemindahanForm');
    if (pemindahanForm) {
        pemindahanForm.addEventListener('submit', function() {
            const jajahanDestEl = document.getElementById('penerima_jajahan');
            if (jajahanDestEl) jajahanDestEl.disabled = false;
            const aliranEl = document.getElementById('kategori_aliran_fi');
            if (aliranEl) aliranEl.disabled = false;
        });
    }
    
    // Initial auto detection & calculation
    autoDetectAliranFi();
    updateCounts();
});
</script>
@endsection
