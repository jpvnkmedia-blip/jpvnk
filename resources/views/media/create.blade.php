@extends('layouts.app')

@section('title', 'Borang Permohonan Tempahan Unit Media')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Action & Navigation Bar -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('media.index') }}" class="w-10 h-10 rounded-2xl bg-white border border-slate-200 text-slate-700 flex items-center justify-center hover:bg-slate-50 transition shadow-xs">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-camera-retro text-amber-500"></i>
                    <span>Borang Permohonan Tempahan Unit Media</span>
                </h1>
                <p class="text-xs text-slate-500">Sila lengkapkan 6 seksyen permohonan di bawah untuk semakan dan pengesahan Unit Media JPVNK.</p>
            </div>
        </div>

        <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
            <i class="fa-solid fa-building-columns text-indigo-500"></i>
            <span>JPVNK Media</span>
        </span>
    </div>

    <!-- Main Application Form -->
    <form action="{{ route('media.store') }}" method="POST" enctype="multipart/form-data"
          x-data="{
              jenis: [],
              hasFoto: false,
              hasPoster: false,
              hasVideo: false,
              hasLain: false,
              keutamaan: 'Biasa',
              updateSelection() {
                  this.hasFoto = this.jenis.includes('Liputan Fotografi') || this.jenis.includes('Rakaman Temu Bual');
                  this.hasPoster = this.jenis.includes('Reka Bentuk Poster') || this.jenis.includes('Reka Bentuk Banner / Backdrop');
                  this.hasVideo = this.jenis.includes('Liputan Videografi') || this.jenis.includes('Video Promosi / Montaj') || this.jenis.includes('Siaran Langsung');
                  this.hasLain = this.jenis.includes('Hebahan Facebook') || this.jenis.includes('Hebahan Instagram') || this.jenis.includes('Hebahan TikTok') || this.jenis.includes('Lain-lain');
              }
          }"
          class="space-y-6">
        @csrf

        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-xs space-y-1 shadow-xs">
                <div class="font-bold flex items-center gap-1.5 text-sm">
                    <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
                    <span>Terdapat ralat pada borang permohonan:</span>
                </div>
                <ul class="list-disc list-inside pl-2 space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- ================= 1. MAKLUMAT PEMOHON ================= -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xs space-y-5">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                    1
                </div>
                <div>
                    <h2 class="text-sm sm:text-base font-black text-slate-900">👤 1. Maklumat Pemohon</h2>
                    <p class="text-xs text-slate-500">Perincian kakitangan yang membuat permohonan perkhidmatan media.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Pemohon <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_pemohon" value="{{ old('nama_pemohon', $user->name) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden text-xs font-semibold text-slate-800">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jawatan <span class="text-rose-500">*</span></label>
                    <input type="text" name="jawatan" value="{{ old('jawatan', $user->jawatan ?? 'Pegawai Veterinar') }}" required placeholder="Cth: Penolong Pegawai Veterinar GV29" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden text-xs">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Bahagian / Unit / Pejabat Perkhidmatan Veterinar Jajahan <span class="text-rose-500">*</span></label>
                    <input type="text" name="bahagian_unit_jajahan" value="{{ old('bahagian_unit_jajahan', $user->jajahan ? 'PPVJ ' . $user->jajahan : 'Ibu Pejabat JPVNK') }}" required placeholder="Cth: Bahagian Pembangunan Komoditi / PPVJ Machang" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden text-xs">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">No. Telefon <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_telefon" value="{{ old('no_telefon', $user->phone) }}" required placeholder="Cth: 019-9998877" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden text-xs font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Emel Rasmi</label>
                        <input type="email" name="emel" value="{{ old('emel', $user->email) }}" placeholder="Cth: pemohon@dvs.gov.my" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden text-xs">
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= 2. MAKLUMAT PROGRAM ================= -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xs space-y-5">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                    2
                </div>
                <div>
                    <h2 class="text-sm sm:text-base font-black text-slate-900">📅 2. Maklumat Program</h2>
                    <p class="text-xs text-slate-500">Maklumat aktiviti, masa, lokasi dan penganjur program.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Nama Program / Aktiviti <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_program" value="{{ old('nama_program') }}" required placeholder="Cth: Majlis Perasmian Hari Ladang Penternak Ruminan Negeri Kelantan 2026" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden text-xs font-semibold">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tarikh Program <span class="text-rose-500">*</span></label>
                    <input type="date" name="tarikh_program" value="{{ old('tarikh_program', $tarikhPilihan) }}" min="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden text-xs font-mono font-bold">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tarikh Tamat Program (Jika Lebih 1 Hari)</label>
                    <input type="date" name="tarikh_tamat" value="{{ old('tarikh_tamat') }}" min="{{ date('Y-m-d') }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden text-xs font-mono">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Masa Mula <span class="text-rose-500">*</span></label>
                        <input type="time" name="masa_mula" value="{{ old('masa_mula', '08:30') }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden text-xs font-mono font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Masa Tamat <span class="text-rose-500">*</span></label>
                        <input type="time" name="masa_tamat" value="{{ old('masa_tamat', '16:30') }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden text-xs font-mono font-bold">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Lokasi Program <span class="text-rose-500">*</span></label>
                    <input type="text" name="lokasi" value="{{ old('lokasi') }}" required placeholder="Cth: Dewan Utama DVS Kota Bharu / Pusat Pembiakan Telaga Papan" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden text-xs">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Penganjur <span class="text-rose-500">*</span></label>
                    <input type="text" name="penganjur" value="{{ old('penganjur', 'Jabatan Perkhidmatan Veterinar Negeri Kelantan') }}" required placeholder="Cth: JPVNK &amp; Pejabat Veterinar Jajahan Pasir Mas" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden text-xs">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Pegawai Bertanggungjawab / PIC <span class="text-rose-500">*</span></label>
                    <input type="text" name="pegawai_bertanggungjawab" value="{{ old('pegawai_bertanggungjawab', $user->name) }}" required placeholder="Nama pegawai penyelaras program" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden text-xs">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Anggaran Jumlah Peserta / Hadirin</label>
                    <input type="number" name="anggaran_peserta" value="{{ old('anggaran_peserta', '50') }}" min="1" placeholder="Cth: 100" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden text-xs font-mono">
                </div>
            </div>
        </div>

        <!-- ================= 3. JENIS PERMOHONAN ================= -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xs space-y-5">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                    3
                </div>
                <div>
                    <h2 class="text-sm sm:text-base font-black text-slate-900">🎥 3. Jenis Permohonan Media</h2>
                    <p class="text-xs text-slate-500">Pilih satu atau lebih perkhidmatan media yang diperlukan bagi program ini.</p>
                </div>
            </div>

            @php
                $jenisList = [
                    ['id' => 'Liputan Fotografi', 'icon' => 'fa-camera', 'label' => 'Liputan Fotografi', 'desc' => 'Jurufoto rasmi majlis, aktiviti & foto kumpulan.'],
                    ['id' => 'Liputan Videografi', 'icon' => 'fa-video', 'label' => 'Liputan Videografi', 'desc' => 'Rakaman video berkualiti tinggi semasa majlis.'],
                    ['id' => 'Reka Bentuk Poster', 'icon' => 'fa-palette', 'label' => 'Reka Bentuk Poster', 'desc' => 'Grafik promosi digital atau cetakan.'],
                    ['id' => 'Reka Bentuk Banner / Backdrop', 'icon' => 'fa-panorama', 'label' => 'Banner / Backdrop', 'desc' => 'Reka bentuk tirai latar pentas / kain rentang.'],
                    ['id' => 'Hebahan Facebook', 'icon' => 'fa-facebook', 'label' => 'Hebahan Facebook', 'desc' => 'Pengeposan di Facebook Page Rasmi JPVNK.'],
                    ['id' => 'Hebahan Instagram', 'icon' => 'fa-instagram', 'label' => 'Hebahan Instagram', 'desc' => 'Kandungan feed & reels rasmi Instagram JPVNK.'],
                    ['id' => 'Hebahan TikTok', 'icon' => 'fa-tiktok', 'label' => 'Hebahan TikTok', 'desc' => 'Video pendek dinamik TikTok JPVNK.'],
                    ['id' => 'Video Promosi / Montaj', 'icon' => 'fa-film', 'label' => 'Video Promosi / Montaj', 'desc' => 'Penyuntingan video montaj perasmian/teaser.'],
                    ['id' => 'Rakaman Temu Bual', 'icon' => 'fa-microphone-lines', 'label' => 'Rakaman Temu Bual', 'desc' => 'Temu bual VIP, penternak atau tetamu khas.'],
                    ['id' => 'Siaran Langsung', 'icon' => 'fa-tower-broadcast', 'label' => 'Siaran Langsung (FB Live)', 'desc' => 'Live streaming majlis ke media sosial rasmi.'],
                    ['id' => 'Lain-lain', 'icon' => 'fa-ellipsis', 'label' => 'Lain-lain Perkhidmatan', 'desc' => 'Khidmat reka bentuk atau media khusus.'],
                ];
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                @foreach($jenisList as $item)
                    <label class="flex items-start gap-3 p-3.5 rounded-2xl border transition cursor-pointer select-none"
                           :class="jenis.includes('{{ $item['id'] }}') ? 'bg-indigo-50/80 border-indigo-400 ring-2 ring-indigo-400/30' : 'bg-slate-50/50 border-slate-200 hover:bg-slate-50'">
                        <input type="checkbox" name="jenis_permohonan[]" value="{{ $item['id'] }}"
                               x-model="jenis" @change="updateSelection()"
                               {{ in_array($item['id'], old('jenis_permohonan', [])) ? 'checked' : '' }}
                               class="mt-1 rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                        <div class="space-y-0.5">
                            <div class="font-bold text-xs text-slate-900 flex items-center gap-1.5">
                                <i class="fa-solid {{ $item['icon'] }} text-indigo-600 text-xs"></i>
                                <span>{{ $item['label'] }}</span>
                            </div>
                            <p class="text-[10px] text-slate-500">{{ $item['desc'] }}</p>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- ================= 4. BUTIRAN KEPERLUAN KHUSUS ================= -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xs space-y-6">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                    4
                </div>
                <div>
                    <h2 class="text-sm sm:text-base font-black text-slate-900">📸 4. Butiran Keperluan Khusus</h2>
                    <p class="text-xs text-slate-500">Lengkapkan butiran terperinci mengikut jenis permohonan yang dipilih di atas.</p>
                </div>
            </div>

            <!-- Sub-seksyen Fotografi -->
            <div x-show="hasFoto" x-transition class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3 text-xs">
                <div class="font-bold text-slate-900 flex items-center gap-2 text-sm">
                    <i class="fa-solid fa-camera text-indigo-600"></i>
                    <span>Keperluan Fotografi / Rakaman Visual:</span>
                </div>
                <p class="text-slate-500 text-[11px]">Sila tandakan fokus penting yang perlu dirakam oleh jurufoto:</p>
                
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                    @foreach(['Kehadiran VIP / Pengarah', 'Gimik Perasmian', 'Penyampaian Bantuan / Sijil', 'Aktiviti Peserta / Ladang', 'Sesi Foto Berkumpulan', 'Pameran & Booth'] as $fokus)
                        <label class="flex items-center gap-2 bg-white p-2.5 rounded-xl border border-slate-200 cursor-pointer">
                            <input type="checkbox" name="butiran_fotografi[fokus][]" value="{{ $fokus }}" class="rounded text-indigo-600 focus:ring-indigo-500">
                            <span class="text-xs text-slate-800 font-medium">{{ $fokus }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="mt-2">
                    <label class="block font-bold text-slate-700 mb-1">Catatan Tambahan Fotografi</label>
                    <input type="text" name="butiran_fotografi[catatan]" placeholder="Cth: Mohon fokus gambar interaksi Pengarah bersama penternak" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white">
                </div>
            </div>

            <!-- Sub-seksyen Poster / Banner -->
            <div x-show="hasPoster" x-transition class="p-5 rounded-2xl bg-amber-50/50 border border-amber-200 space-y-3 text-xs">
                <div class="font-bold text-amber-950 flex items-center gap-2 text-sm">
                    <i class="fa-solid fa-palette text-amber-600"></i>
                    <span>Keperluan Reka Bentuk Poster / Banner / Backdrop:</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tajuk / Tema Poster</label>
                        <input type="text" name="butiran_poster[tajuk]" placeholder="Cth: Kursus Penternakan Unggas Komersial 2026" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Saiz Reka Bentuk</label>
                        <input type="text" name="butiran_poster[saiz]" placeholder="Cth: A4, A3, Banner 10x4 kaki, Backdrop 20x10 kaki" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kandungan / Teks Utama yang Perlu Dimuatkan</label>
                    <textarea name="butiran_poster[teks]" rows="2" placeholder="Senaraikan teks, tarikh, yuran, penceramah atau maklumat wajib dalam poster..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Logo yang Diperlukan</label>
                        <input type="text" name="butiran_poster[logo]" placeholder="Cth: Logo JPVNK, Kerajaan Kelantan, MAFI" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tarikh Bahan Poster Diperlukan</label>
                        <input type="date" name="butiran_poster[tarikh_siap]" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white font-mono">
                    </div>
                </div>
            </div>

            <!-- Sub-seksyen Video -->
            <div x-show="hasVideo" x-transition class="p-5 rounded-2xl bg-indigo-50/50 border border-indigo-200 space-y-3 text-xs">
                <div class="font-bold text-indigo-950 flex items-center gap-2 text-sm">
                    <i class="fa-solid fa-video text-indigo-600"></i>
                    <span>Keperluan Videografi &amp; Montaj:</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tujuan / Konsep Video</label>
                        <input type="text" name="butiran_video[tujuan]" placeholder="Cth: Video montaj perasmian / Video rangkuman majlis" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Anggaran Durasi Video</label>
                        <input type="text" name="butiran_video[durasi]" placeholder="Cth: 1-2 Minit / 3-5 Minit" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Format &amp; Platform Penggunaan</label>
                        <input type="text" name="butiran_video[platform]" placeholder="Cth: Reels 9:16 (TikTok/IG) &amp; Horizontal 16:9 (FB/Youtube)" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tarikh Video Diperlukan</label>
                        <input type="date" name="butiran_video[tarikh_siap]" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white font-mono">
                    </div>
                </div>
            </div>

            <!-- Catatan Tambahan / Lain-lain -->
            <div>
                <label class="block font-bold text-slate-700 mb-1 text-xs">Catatan &amp; Keperluan Tambahan</label>
                <textarea name="butiran_lain" rows="2" placeholder="Nyatakan sebarang keperluan khas lain sekiranya ada..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-xs"></textarea>
            </div>
        </div>

        <!-- ================= 5. LAMPIRAN ================= -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xs space-y-5">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                    5
                </div>
                <div>
                    <h2 class="text-sm sm:text-base font-black text-slate-900">📎 5. Lampiran Fail &amp; Rujukan</h2>
                    <p class="text-xs text-slate-500">Muat naik surat jemputan, atur cara program, tentatif, logo, atau dokumen sokongan.</p>
                </div>
            </div>

            <div class="p-6 border-2 border-dashed border-slate-300 rounded-2xl bg-slate-50/50 text-center space-y-2">
                <i class="fa-solid fa-cloud-arrow-up text-3xl text-indigo-500"></i>
                <div class="text-xs font-bold text-slate-700">Pilih atau Seret Fail ke Sini</div>
                <p class="text-[11px] text-slate-500">Boleh pilih lebih daripada satu fail (PDF, DOCX, PNG, JPG - Maksimum 10MB setiap fail).</p>
                <input type="file" name="lampiran_files[]" multiple class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer pt-2">
            </div>
        </div>

        <!-- ================= 6. PENGESAHAN ================= -->
        <div class="bg-gradient-to-br from-indigo-50/70 to-slate-50 rounded-3xl p-6 sm:p-7 border border-indigo-200/80 shadow-xs space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-indigo-200/60">
                <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-sm shadow-xs">
                    6
                </div>
                <div>
                    <h2 class="text-sm sm:text-base font-black text-slate-900">✅ 6. Pengesahan Pemohon</h2>
                    <p class="text-xs text-slate-600">Perakuan ketepatan maklumat sebelum permohonan dihantar.</p>
                </div>
            </div>

            <label class="flex items-start gap-3 p-4 rounded-2xl bg-white border border-indigo-200 cursor-pointer shadow-xs">
                <input type="checkbox" name="pengesahan_pemohon" value="1" required class="mt-1 rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                <span class="text-xs leading-relaxed text-slate-700">
                    Saya mengesahkan bahawa maklumat yang diberikan di atas adalah <b>benar dan lengkap</b> serta bersetuju untuk pihak Unit Media JPVNK menghubungi saya sekiranya terdapat sebarang maklumat tambahan atau penyelarasan teknikal yang diperlukan.
                </span>
            </label>

            <div class="pt-3 flex flex-col sm:flex-row items-center justify-between gap-3">
                <a href="{{ route('media.index') }}" class="w-full sm:w-auto px-5 py-3 rounded-2xl bg-white border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-100 transition text-center">
                    Batal &amp; Kembali
                </a>

                <button type="submit" class="w-full sm:w-auto px-8 py-3 rounded-2xl bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-bold text-xs sm:text-sm shadow-lg shadow-indigo-600/30 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Hantar Permohonan Tempahan</span>
                </button>
            </div>
        </div>

    </form>

</div>
@endsection
