@extends('layouts.app')

@section('title', 'Kemas Kini Permohonan Media - ' . $tempahan->no_rujukan)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Action & Navigation Bar -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('media.show', $tempahan->id) }}" class="w-10 h-10 rounded-2xl bg-white border border-slate-200 text-slate-700 flex items-center justify-center hover:bg-slate-50 transition shadow-xs">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-mono font-bold text-xs text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-lg border border-indigo-200">
                        {{ $tempahan->no_rujukan }}
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 text-sky-800 border border-sky-200">
                        Kemas Kini Permohonan
                    </span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 mt-1">
                    Kemas Kini Butiran Tempahan Unit Media
                </h1>
            </div>
        </div>
    </div>

    @if($tempahan->catatan_unit_media)
        <div class="bg-sky-50 border border-sky-300 rounded-3xl p-5 sm:p-6 text-sky-950 space-y-2 shadow-xs">
            <div class="font-bold flex items-center gap-2 text-sm text-sky-900">
                <i class="fa-solid fa-circle-exclamation text-sky-600"></i>
                <span>Nota / Arahan Pembetulan Daripada Unit Media:</span>
            </div>
            <p class="text-xs leading-relaxed bg-white p-3.5 rounded-2xl border border-sky-200 font-medium">
                {{ $tempahan->catatan_unit_media }}
            </p>
        </div>
    @endif

    <!-- Main Edit Form -->
    <form action="{{ route('media.update', $tempahan->id) }}" method="POST" enctype="multipart/form-data"
          x-data="{
              jenis: {{ json_encode(old('jenis_permohonan', $tempahan->jenis_permohonan ?? [])) }},
              hasFoto: false,
              hasPoster: false,
              hasVideo: false,
              hasLain: false,
              init() {
                  this.updateSelection();
              },
              updateSelection() {
                  this.hasFoto = this.jenis.includes('Liputan Fotografi') || this.jenis.includes('Rakaman Temu Bual');
                  this.hasPoster = this.jenis.includes('Reka Bentuk Poster') || this.jenis.includes('Reka Bentuk Banner / Backdrop');
                  this.hasVideo = this.jenis.includes('Liputan Videografi') || this.jenis.includes('Video Promosi / Montaj') || this.jenis.includes('Siaran Langsung');
                  this.hasLain = this.jenis.includes('Hebahan Facebook') || this.jenis.includes('Hebahan Instagram') || this.jenis.includes('Hebahan TikTok') || this.jenis.includes('Lain-lain');
              }
          }"
          class="space-y-6">
        @csrf
        @method('PUT')

        <!-- 1. Maklumat Pemohon -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xs space-y-5">
            <h2 class="text-sm sm:text-base font-black text-slate-900 pb-2 border-b border-slate-100">
                👤 1. Maklumat Pemohon
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Pemohon <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_pemohon" value="{{ old('nama_pemohon', $tempahan->nama_pemohon) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs font-semibold">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jawatan <span class="text-rose-500">*</span></label>
                    <input type="text" name="jawatan" value="{{ old('jawatan', $tempahan->jawatan) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Bahagian / Unit / Jajahan <span class="text-rose-500">*</span></label>
                    <input type="text" name="bahagian_unit_jajahan" value="{{ old('bahagian_unit_jajahan', $tempahan->bahagian_unit_jajahan) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">No. Telefon <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_telefon" value="{{ old('no_telefon', $tempahan->no_telefon) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Emel Rasmi</label>
                        <input type="email" name="emel" value="{{ old('emel', $tempahan->emel) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs">
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Maklumat Program -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xs space-y-5">
            <h2 class="text-sm sm:text-base font-black text-slate-900 pb-2 border-b border-slate-100">
                📅 2. Maklumat Program
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Nama Program / Aktiviti <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_program" value="{{ old('nama_program', $tempahan->nama_program) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs font-semibold">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tarikh Program <span class="text-rose-500">*</span></label>
                    <input type="date" name="tarikh_program" value="{{ old('tarikh_program', $tempahan->tarikh_program->format('Y-m-d')) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs font-mono font-bold">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tarikh Tamat Program</label>
                    <input type="date" name="tarikh_tamat" value="{{ old('tarikh_tamat', $tempahan->tarikh_tamat ? $tempahan->tarikh_tamat->format('Y-m-d') : '') }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs font-mono">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Masa Mula <span class="text-rose-500">*</span></label>
                        <input type="time" name="masa_mula" value="{{ old('masa_mula', $tempahan->masa_mula) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Masa Tamat <span class="text-rose-500">*</span></label>
                        <input type="time" name="masa_tamat" value="{{ old('masa_tamat', $tempahan->masa_tamat) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs font-mono">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Lokasi Program <span class="text-rose-500">*</span></label>
                    <input type="text" name="lokasi" value="{{ old('lokasi', $tempahan->lokasi) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Penganjur <span class="text-rose-500">*</span></label>
                    <input type="text" name="penganjur" value="{{ old('penganjur', $tempahan->penganjur) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Pegawai Penyelaras / PIC <span class="text-rose-500">*</span></label>
                    <input type="text" name="pegawai_bertanggungjawab" value="{{ old('pegawai_bertanggungjawab', $tempahan->pegawai_bertanggungjawab) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Anggaran Jumlah Peserta</label>
                    <input type="number" name="anggaran_peserta" value="{{ old('anggaran_peserta', $tempahan->anggaran_peserta) }}" min="1" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs font-mono">
                </div>
            </div>
        </div>

        <!-- 3. Jenis Permohonan -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xs space-y-5">
            <h2 class="text-sm sm:text-base font-black text-slate-900 pb-2 border-b border-slate-100">
                🎥 3. Jenis Permohonan Media
            </h2>

            @php
                $jenisList = [
                    ['id' => 'Liputan Fotografi', 'label' => 'Liputan Fotografi'],
                    ['id' => 'Liputan Videografi', 'label' => 'Liputan Videografi'],
                    ['id' => 'Reka Bentuk Poster', 'label' => 'Reka Bentuk Poster'],
                    ['id' => 'Reka Bentuk Banner / Backdrop', 'label' => 'Banner / Backdrop'],
                    ['id' => 'Hebahan Facebook', 'label' => 'Hebahan Facebook'],
                    ['id' => 'Hebahan Instagram', 'label' => 'Hebahan Instagram'],
                    ['id' => 'Hebahan TikTok', 'label' => 'Hebahan TikTok'],
                    ['id' => 'Video Promosi / Montaj', 'label' => 'Video Promosi / Montaj'],
                    ['id' => 'Rakaman Temu Bual', 'label' => 'Rakaman Temu Bual'],
                    ['id' => 'Siaran Langsung', 'label' => 'Siaran Langsung (Live)'],
                    ['id' => 'Lain-lain', 'label' => 'Lain-lain'],
                ];
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach($jenisList as $item)
                    <label class="flex items-center gap-2 p-3 rounded-xl border cursor-pointer select-none"
                           :class="jenis.includes('{{ $item['id'] }}') ? 'bg-indigo-50 border-indigo-300 font-bold text-indigo-900' : 'bg-slate-50 border-slate-200 text-slate-700'">
                        <input type="checkbox" name="jenis_permohonan[]" value="{{ $item['id'] }}"
                               x-model="jenis" @change="updateSelection()"
                               class="rounded text-indigo-600 focus:ring-indigo-500">
                        <span class="text-xs">{{ $item['label'] }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- 5. Lampiran Tambahan -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xs space-y-4">
            <h2 class="text-sm sm:text-base font-black text-slate-900 pb-2 border-b border-slate-100">
                📎 5. Muat Naik Lampiran Tambahan
            </h2>

            <input type="file" name="lampiran_files[]" multiple class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer">
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between gap-3 pt-2">
            <a href="{{ route('media.show', $tempahan->id) }}" class="px-5 py-3 rounded-2xl bg-white border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-100 transition">
                Batal
            </a>

            <button type="submit" class="px-8 py-3 rounded-2xl bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-bold text-xs sm:text-sm shadow-lg shadow-indigo-600/30 transition flex items-center gap-2">
                <i class="fa-solid fa-paper-plane"></i>
                <span>Simpan &amp; Hantar Semula Permohonan</span>
            </button>
        </div>

    </form>

</div>
@endsection
