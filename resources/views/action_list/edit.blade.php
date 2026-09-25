@extends('layouts.app')

@section('title', 'Kemaskini Aktiviti - Action List JPVNK')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gradient-to-r from-emerald-900 via-teal-900 to-slate-900 p-6 rounded-3xl text-white shadow-xl">
        <div class="space-y-1">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold border border-amber-500/30">
                <i class="fa-solid fa-pen-to-square"></i> KEMASKINI REKOD AKTIVITI #{{ $actionList->no_bil }}
            </div>
            <h1 class="text-2xl font-black">{{ $actionList->tajuk_aktiviti ?: 'Kemaskini Catatan Aktiviti' }}</h1>
            <p class="text-xs text-slate-300">
                Kemaskini maklumat, status pelaksanaan, atau tindakan susulan aktiviti.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('action-list.show', $actionList->id) }}" class="px-4 py-2 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition flex items-center gap-1.5 border border-white/20">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Butiran
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

        <!-- 1. MAKLUMAT UTAMA AKTIVITI -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-5">
            <div class="font-black text-slate-900 text-sm flex items-center gap-2 border-b border-slate-100 pb-3">
                <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">1</span>
                MAKLUMAT UTAMA AKTIVITI
            </div>

            <div class="space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Tajuk Aktiviti / Perkara <span class="text-rose-500">*</span></label>
                    <input type="text" name="tajuk_aktiviti" value="{{ old('tajuk_aktiviti', $actionList->tajuk_aktiviti) }}" required placeholder="cth: Lawatan Pemantauan Projek Ruminan Kg. Gong Chapa / Mesyuarat Penyelarasan Bulanan..." class="w-full py-2.5 px-3.5 rounded-xl border border-slate-300 focus:outline-emerald-500 font-bold text-slate-900 text-sm">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Kategori Aktiviti <span class="text-rose-500">*</span></label>
                        <select name="kategori_aktiviti" required class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 font-bold text-slate-800">
                            @foreach($kategoriList as $kKey => $kVal)
                                <option value="{{ $kKey }}" {{ old('kategori_aktiviti', $actionList->kategori_aktiviti) === $kKey ? 'selected' : '' }}>{{ $kVal }}</option>
                            @endforeach
                        </select>
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
                        <label class="block font-bold text-slate-700 uppercase mb-1">Status Aktiviti <span class="text-rose-500">*</span></label>
                        <select name="status" required class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 font-bold text-slate-800">
                            @foreach($statusList as $sKey => $sVal)
                                <option value="{{ $sKey }}" {{ old('status', $actionList->status) === $sKey ? 'selected' : '' }}>{{ $sVal }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Aktiviti <span class="text-rose-500">*</span></label>
                        <input type="date" name="tarikh" value="{{ old('tarikh', $actionList->tarikh ? \Carbon\Carbon::parse($actionList->tarikh)->format('Y-m-d') : date('Y-m-d')) }}" required class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 font-semibold text-slate-800">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Masa Mula</label>
                        <input type="text" name="masa_mula" value="{{ old('masa_mula', $actionList->masa_mula) }}" placeholder="cth: 09:00 AM" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Masa Selesai</label>
                        <input type="text" name="masa_selesai" value="{{ old('masa_selesai', $actionList->masa_selesai) }}" placeholder="cth: 12:30 PM" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Lokasi / Premis Aktiviti</label>
                        <input type="text" name="lokasi" value="{{ old('lokasi', $actionList->lokasi) }}" placeholder="cth: Ladang Ternakan Lembu Jaya, Kg. Gong Chapa..." class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Nama Pegawai & Staf Terlibat</label>
                        <input type="text" name="nama_pegawai" value="{{ old('nama_pegawai', $actionList->nama_pegawai) }}" placeholder="cth: Dr. Nik Farhan, En. Yusof..." class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 font-semibold text-slate-800">
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. MAKLUMAT & PERINCIAN AKTIVITI -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
            <div class="font-black text-slate-900 text-sm flex items-center gap-2 border-b border-slate-100 pb-3">
                <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold">2</span>
                MAKLUMAT & PERINCIAN AKTIVITI
            </div>

            <div class="space-y-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Maklumat & Catatan Lengkap Aktiviti <span class="text-rose-500">*</span></label>
                    <textarea name="maklumat_aktiviti" rows="6" required placeholder="Catatkan perincian aktiviti yang dilaksanakan, perbincangan, penemuan di lapangan, statistik/bilangan ternakan, atau keputusan yang dicapai..." class="w-full py-2.5 px-3.5 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-800 leading-relaxed">{{ old('maklumat_aktiviti', $actionList->maklumat_aktiviti) }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Tahap Keutamaan</label>
                        <select name="keutamaan" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-800 font-semibold">
                            @foreach($keutamaanList as $kKeutamaan)
                                <option value="{{ $kKeutamaan }}" {{ old('keutamaan', $actionList->keutamaan) === $kKeutamaan ? 'selected' : '' }}>{{ $kKeutamaan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Muat Naik Lampiran Baharu (Pilihan)</label>
                        <input type="file" name="lampiran" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx" class="w-full py-1.5 px-3 rounded-xl border border-slate-300 text-xs text-slate-600 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                        @if($actionList->lampiran)
                            <div class="mt-2 text-xs text-emerald-700 flex items-center gap-1.5 font-bold">
                                <i class="fa-solid fa-paperclip"></i> Fail sedia ada: 
                                <a href="{{ Storage::url($actionList->lampiran) }}" target="_blank" class="underline hover:text-emerald-900">Lihat Lampiran</a>
                            </div>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Tindakan Susulan (Jika ada)</label>
                    <textarea name="tindakan_susulan" rows="3" placeholder="Nyatakan sebarang tindakan susulan yang perlu diselesaikan..." class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-700">{{ old('tindakan_susulan', $actionList->tindakan_susulan) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Butang Simpan -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('action-list.show', $actionList->id) }}" class="px-5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-700 font-bold text-xs hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md hover:shadow-lg transition flex items-center gap-2">
                <i class="fa-solid fa-check"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
