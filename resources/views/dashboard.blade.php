@extends('layouts.app')

@section('title', 'Papan Pemuka Utama')
@section('page_title', 'Papan Pemuka Sistem Bersepadu')

@section('content')
<div class="space-y-6">

    <!-- Top Welcome Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 p-6 sm:p-8 text-white shadow-xl">
        <div class="relative z-10 max-w-3xl">
            <div class="flex items-center gap-3 mb-4">
                <img src="{{ asset('images/logo-veterinar.png') }}" alt="Logo Jabatan Perkhidmatan Veterinar" class="w-14 h-14 object-contain bg-white/95 p-1.5 rounded-2xl shadow-xl border border-white/20">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-0.5 rounded-full bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs font-semibold">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Sistem Veterinar Bersepadu Kelantan</span>
                    </div>
                    <div class="text-xs text-slate-300 mt-0.5 font-medium">Jabatan Perkhidmatan Veterinar Negeri Kelantan</div>
                </div>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                Selamat Datang, {{ $user->name }}!
            </h1>
            <p class="mt-2 text-sm text-slate-300 leading-relaxed">
                @if($user->role === 'pengarah')
                    Anda sedang mengakses sistem sebagai <span class="text-emerald-400 font-bold">Pengarah Perkhidmatan Veterinar Negeri Kelantan</span>. Anda memegang autoriti eksekutif meluluskan permohonan Lesen Enakmen Penternakan Unggas (EPU Borang B), menyemak rayuan lesen, dan memantau prestasi, statistik serta analitik bersepadu bagi kesemua 9 modul JPVNK.
                @elseif($user->role === 'admin_pejabat')
                    Anda sedang mengakses sistem sebagai <span class="text-indigo-400 font-bold">Admin Stor Pejabat</span>. Anda bertanggungjawab menguruskan <span class="text-white font-semibold">Inventori &amp; Stor Peralatan Pejabat</span> serta kelulusan permohonan bekalan staf JPVNK.
                @elseif($user->role === 'admin_kenderaan')
                    Anda sedang mengakses sistem sebagai <span class="text-blue-400 font-bold">Admin Kenderaan &amp; Fleet</span>. Anda bertanggungjawab menguruskan <span class="text-white font-semibold">Armada Kenderaan Rasmi</span>, jadual pemandu serta kelulusan tempahan kenderaan JPVNK.
                @elseif($user->role === 'admin_epu')
                    Anda sedang mengakses sistem sebagai <span class="text-amber-400 font-bold">Admin EPU Negeri</span>. Anda bertanggungjawab menguruskan <span class="text-white font-semibold">Enakmen Penternakan Unggas (EPU)</span>, pendaftaran ladang ternakan unggas, semakan permohonan lesen, pengesahan bayaran fi, dan cetakan borang rasmi peringkat Negeri Kelantan.
                @elseif($user->role === 'pegawai_verifikasi_epu')
                    Anda sedang mengakses sistem sebagai <span class="text-amber-400 font-bold">Pegawai Verifikasi EPU Jajahan {{ $user->jajahan ?? '' }}</span>. Anda bertanggungjawab menyemak kelengkapan dokumen, melaksanakan lawatan verifikasi tapak kepatuhan ladang unggas, dan mencetak Borang A serta Lesen Borang B bagi Jajahan <span class="text-amber-300 font-bold">{{ $user->jajahan ?? 'Kelantan' }}</span>.
                @elseif($user->role === 'pegawai_pelesen')
                    Anda sedang mengakses sistem sebagai <span class="text-amber-400 font-bold">Pegawai Pelesen / Pengarah DVS</span>. Anda bertanggungjawab menyemak penilaian tapak ladang, meluluskan permohonan lesen Borang B, surat sokongan teknikal, dan memproses rayuan lesen EPU peringkat HQ JPVNK.
                @elseif($user->role === 'admin_eptr')
                    Anda sedang mengakses sistem sebagai <span class="text-emerald-400 font-bold">Admin EPTR Negeri</span>. Anda bertanggungjawab menguruskan <span class="text-white font-semibold">Pendaftaran Ternakan Ruminan (EPTR)</span>, kelulusan tag telinga, pembatalan/kematian ternakan, permit sembelihan dan permit pemindahan ternakan peringkat Negeri Kelantan.
                @elseif($user->role === 'admin_jajahan' || $user->role === 'admin_eptr_jajahan')
                    Anda sedang mengakses sistem sebagai <span class="text-emerald-400 font-bold">Admin EPTR Jajahan {{ $user->jajahan ?? '' }}</span>. Anda bertanggungjawab menguruskan <span class="text-white font-semibold">Pendaftaran Ternakan Ruminan (EPTR)</span>, verifikasi &amp; kelulusan tag telinga, permit sembelihan, kelulusan permit pemindahan ternakan dan pengurusan kesihatan bagi Jajahan <span class="text-emerald-300 font-bold">{{ $user->jajahan ?? 'Kelantan' }}</span>.
                @elseif($user->role === 'admin_program')
                    Anda sedang mengakses sistem sebagai <span class="text-emerald-400 font-bold">Admin Program Pawah</span>. Anda bertanggungjawab menguruskan <span class="text-white font-semibold">Skim Bantuan Pawah Ternakan</span>, pendaftaran perjanjian pawah, kelulusan permohonan awam, pemantauan kelahiran anak, dan proses penyelesaian pawah JPVNK.
                @elseif($user->role === 'admin_naimbif_negeri' || $user->role === 'admin_naimbif')
                    Anda sedang mengakses sistem sebagai <span class="text-emerald-400 font-bold">Admin NAIMbif Negeri</span>. Anda bertanggungjawab menguruskan permohonan, semakan penilaian, dan kelulusan geran/bantuan <span class="text-white font-semibold">Program Ladang Bridlot Pedaging NAIMbif</span> peringkat Negeri Kelantan.
                @elseif($user->role === 'admin_naimbif_jajahan')
                    Anda sedang mengakses sistem sebagai <span class="text-emerald-400 font-bold">Admin NAIMbif Jajahan {{ $user->jajahan ?? '' }}</span>. Anda bertanggungjawab melaksanakan siasatan premis ternakan, verifikasi kandang &amp; padang ragut, penetapan ID premis, dan perakuan syor permohonan Ladang Bridlot bagi Jajahan <span class="text-emerald-300 font-bold">{{ $user->jajahan ?? 'Kelantan' }}</span>.
                @elseif($user->role === 'admin_klinik')
                    Anda sedang mengakses sistem sebagai <span class="text-rose-400 font-bold">Admin Klinik Haiwan &amp; Rawatan</span>. Anda bertanggungjawab menguruskan <span class="text-white font-semibold">Temujanji Rawatan Haiwan</span>, rekod rawatan klinikal, surgeri/pembedahan serta pengeluaran kad rawatan pesakit veterinar.
                @elseif($user->role === 'admin_ubat')
                    Anda sedang mengakses sistem sebagai <span class="text-rose-400 font-bold">Admin Stor Ubat &amp; Farmasi</span>. Anda bertanggungjawab menguruskan <span class="text-white font-semibold">Katalog Ubat &amp; Vaksin Veterinar</span> serta kelulusan permohonan bekalan farmasi veterinar JPVNK.
                @else
                    Anda sedang mengakses sistem sebagai <span class="text-emerald-400 font-bold">{{ $user->role_label }}</span>. Sistem ini membolehkan pengurusan bersepadu bagi semua perkhidmatan veterinar di bawah satu akaun berpusat.
                @endif
            </p>

            <!-- Quick Action Buttons -->
            <div class="mt-5 flex flex-wrap gap-2.5">
                @if($user->role === 'pengarah')
                    <a href="{{ route('pengarah.laporan') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-lg shadow-emerald-900/40 transition hover:scale-102">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span>Laporan &amp; Analitik Eksekutif</span>
                    </a>
                    <a href="{{ route('epu.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-black shadow-lg shadow-amber-900/40 transition hover:scale-102">
                        <i class="fa-solid fa-stamp"></i>
                        <span>Kelulusan Lesen EPU ({{ $totalEpuPendingPelesen }})</span>
                    </a>
                @endif
                @if($user->canAccessPetaTaburan())
                    <a href="{{ route('peta.taburan') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-lg shadow-emerald-900/40 transition hover:scale-102">
                        <i class="fa-solid fa-map-location-dot"></i>
                        <span>Peta Taburan Penternak (GIS)</span>
                    </a>
                @endif
                @if($user->isSuperAdmin())
                    <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-black shadow-lg shadow-amber-900/40 transition hover:scale-102">
                        <i class="fa-solid fa-user-plus"></i>
                        <span>Tambah Pengguna Baharu</span>
                    </a>
                @endif
                @if($user->role === 'admin_naimbif_negeri' || $user->role === 'admin_naimbif')
                    <a href="{{ route('naimbif.admin.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-lg shadow-emerald-900/40 transition">
                        <i class="fa-solid fa-cow"></i>
                        <span>Senarai Permohonan NAIMbif ({{ $totalNaimbifApps }})</span>
                    </a>
                    <a href="{{ route('naimbif.admin.index', ['status' => 'Disokong']) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-lg shadow-blue-900/40 transition">
                        <i class="fa-solid fa-stamp"></i>
                        <span>Menunggu Kelulusan Negeri ({{ $totalNaimbifMenungguNegeri }})</span>
                    </a>
                    <a href="{{ route('naimbif.admin.index', ['status' => 'Lulus']) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold shadow-lg shadow-teal-900/40 transition">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Permohonan Lulus ({{ $totalNaimbifLulus }})</span>
                    </a>
                @elseif($user->role === 'admin_naimbif_jajahan')
                    <a href="{{ route('naimbif.admin.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-lg shadow-emerald-900/40 transition">
                        <i class="fa-solid fa-cow"></i>
                        <span>Permohonan Jajahan {{ $user->jajahan }} ({{ $totalNaimbifApps }})</span>
                    </a>
                    <a href="{{ route('naimbif.admin.index', ['status' => 'Dalam Semakan']) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-black shadow-lg shadow-amber-900/40 transition">
                        <i class="fa-solid fa-clipboard-question"></i>
                        <span>Perlu Siasatan Premis ({{ $totalNaimbifMenungguJajahan }})</span>
                    </a>
                    <a href="{{ route('naimbif.public.apply') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold shadow-lg shadow-teal-900/40 transition">
                        <i class="fa-solid fa-file-signature"></i>
                        <span>Borang Permohonan Awam</span>
                    </a>
                @elseif($user->role === 'admin_eptr' || $user->role === 'admin_jajahan' || $user->role === 'admin_eptr_jajahan')
                    <a href="{{ route('eptr.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-lg shadow-emerald-900/40 transition">
                        <i class="fa-solid fa-cow"></i>
                        <span>Senarai Ternakan (EPTR)</span>
                    </a>
                    <a href="{{ route('eptr.index', ['status_kelulusan' => 'Menunggu']) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-black shadow-lg shadow-amber-900/40 transition">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span>Kelulusan Pendaftaran ({{ $totalPendingTernakan }})</span>
                    </a>
                    <a href="{{ route('eptr.kesihatan.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold shadow-lg shadow-teal-900/40 transition">
                        <i class="fa-solid fa-heart-pulse"></i>
                        <span>Program Kesihatan &amp; Vaksin</span>
                    </a>
                    <a href="{{ route('eptr.borang-d.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-lg shadow-indigo-900/40 transition">
                        <i class="fa-solid fa-file-invoice"></i>
                        <span>Permit Sembelihan (Borang D)</span>
                    </a>
                    <a href="{{ route('eptr.pemindahan.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold shadow-lg shadow-blue-900/40 transition">
                        <i class="fa-solid fa-truck-moving"></i>
                        <span>Permit Pemindahan Ternakan</span>
                    </a>
                    @if($user->role === 'admin_jajahan' || $user->role === 'admin_eptr_jajahan')
                    <a href="{{ route('inventori.permohonan.ubat.mohon') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold shadow-lg shadow-rose-900/40 transition">
                        <i class="fa-solid fa-pills"></i>
                        <span>Mohon Bekalan Ubat / Vaksin</span>
                    </a>
                    @endif
                @elseif($user->role === 'admin_program')
                    <a href="{{ route('pawah.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-lg shadow-emerald-900/40 transition">
                        <i class="fa-solid fa-plus-circle"></i>
                        <span>Daftar Perjanjian Pawah Baharu</span>
                    </a>
                    <a href="{{ route('pawah.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-black shadow-lg shadow-amber-900/40 transition">
                        <i class="fa-solid fa-handshake-angle"></i>
                        <span>Senarai Surat Perjanjian</span>
                    </a>
                @elseif(in_array($user->role, ['admin_pejabat', 'admin_stor_pejabat', 'pegawai_pengesah_pejabat', 'admin_pelulus_pejabat']))
                    <a href="{{ route('inventori.pejabat.permohonan') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-black shadow-lg shadow-amber-900/40 transition">
                        <i class="fa-solid fa-clipboard-check"></i>
                        <span>{{ $user->canApprovePermohonanPejabat() && !$user->canInputStorPejabat() ? 'Pengesahan Permohonan Pejabat' : 'Permohonan & Serahan Stok' }}</span>
                    </a>
                    @if($user->canInputStorPejabat())
                    <a href="{{ route('inventori.pejabat.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-lg shadow-indigo-900/40 transition">
                        <i class="fa-solid fa-plus-circle"></i>
                        <span>Daftar Barangan Pejabat</span>
                    </a>
                    @endif
                    <a href="{{ route('inventori.pejabat.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-white text-xs font-bold shadow-lg transition">
                        <i class="fa-solid fa-boxes-stacked"></i>
                        <span>Katalog Stor Pejabat</span>
                    </a>
                @elseif($user->role === 'admin_kenderaan')
                    <a href="{{ route('kenderaan.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-black shadow-lg shadow-amber-900/40 transition">
                        <i class="fa-solid fa-clipboard-check"></i>
                        <span>Kelulusan Tempahan Kenderaan</span>
                    </a>
                    <a href="{{ route('kenderaan.fleet') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold shadow-lg shadow-teal-900/40 transition">
                        <i class="fa-solid fa-truck-pickup"></i>
                        <span>Pengurusan Fleet Kenderaan</span>
                    </a>
                    <a href="{{ route('kenderaan.pemandu.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-lg shadow-indigo-900/40 transition">
                        <i class="fa-solid fa-id-card"></i>
                        <span>Maklumat Pemandu Rasmi</span>
                    </a>
                    <a href="{{ route('kenderaan.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-white text-xs font-bold shadow-lg transition">
                        <i class="fa-solid fa-plus-circle"></i>
                        <span>Tempahan Kenderaan Baharu</span>
                    </a>
                @elseif($user->role === 'admin_ubat')
                    <a href="{{ route('inventori.ubat.permohonan') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-black shadow-lg shadow-amber-900/40 transition">
                        <i class="fa-solid fa-clipboard-check"></i>
                        <span>Kelulusan Permohonan Ubat/Vaksin</span>
                    </a>
                    <a href="{{ route('inventori.ubat.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold shadow-lg shadow-rose-900/40 transition">
                        <i class="fa-solid fa-plus-circle"></i>
                        <span>Daftar Ubat / Vaksin Baharu</span>
                    </a>
                    <a href="{{ route('inventori.ubat.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-white text-xs font-bold shadow-lg transition">
                        <i class="fa-solid fa-pills"></i>
                        <span>Pengurusan Stor Ubat &amp; Vaksin</span>
                    </a>
                @elseif($user->role === 'admin_kursus')
                    <a href="{{ route('kursus.pemohon.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-black shadow-lg shadow-amber-900/40 transition">
                        <i class="fa-solid fa-users-gear"></i>
                        <span>Pengurusan &amp; Kelulusan Pemohon</span>
                    </a>
                    <a href="{{ route('kursus.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-cyan-700 hover:bg-cyan-600 text-white text-xs font-bold shadow-lg shadow-cyan-900/40 transition">
                        <i class="fa-solid fa-plus-circle"></i>
                        <span>Terbitkan Kursus Baharu</span>
                    </a>
                    <a href="{{ route('inventori.permohonan.saya') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold shadow-lg shadow-slate-900/40 transition">
                        <i class="fa-solid fa-clipboard-list"></i>
                        <span>Permohonan Stor Saya</span>
                    </a>
                @elseif($user->role === 'admin_klinik')
                    <a href="{{ route('klinik.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold shadow-lg shadow-rose-900/40 transition">
                        <i class="fa-solid fa-calendar-plus"></i>
                        <span>Daftar Temujanji Rawatan</span>
                    </a>
                    <a href="{{ route('klinik.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-black shadow-lg shadow-amber-900/40 transition">
                        <i class="fa-solid fa-notes-medical"></i>
                        <span>Senarai Semua Temujanji</span>
                    </a>
                @elseif($user->role === 'staf')
                    <a href="{{ route('inventori.permohonan.pejabat.mohon') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-lg shadow-indigo-900/40 transition">
                        <i class="fa-solid fa-boxes-stacked"></i>
                        <span>Mohon Alatan Pejabat</span>
                    </a>
                    <a href="{{ route('inventori.permohonan.saya') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold shadow-lg shadow-slate-900/40 transition">
                        <i class="fa-solid fa-clipboard-list"></i>
                        <span>Permohonan Stor Saya</span>
                    </a>
                    <a href="{{ route('kursus.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-cyan-700 hover:bg-cyan-600 text-white text-xs font-bold shadow-lg shadow-cyan-900/40 transition">
                        <i class="fa-solid fa-graduation-cap"></i>
                        <span>Daftar Kursus Ternakan</span>
                    </a>
                @elseif(in_array($user->role, ['admin_epu', 'pegawai_verifikasi_epu', 'pegawai_pelesen']))
                    <a href="{{ route('epu.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold shadow-lg shadow-amber-900/40 transition">
                        <i class="fa-solid fa-feather"></i>
                        <span>Senarai Permohonan EPU</span>
                    </a>
                    <a href="{{ route('epu.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-lg shadow-emerald-900/40 transition">
                        <i class="fa-solid fa-warehouse"></i>
                        <span>Direktori Ladang Unggas</span>
                    </a>
                    @if($user->role === 'pegawai_verifikasi_epu' || $user->role === 'admin_epu' || $user->isSuperAdmin())
                    <a href="{{ route('epu.index', ['status' => 'Menunggu Semakan Dokumen']) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-lg shadow-indigo-900/40 transition">
                        <i class="fa-solid fa-clipboard-check"></i>
                        <span>Verifikasi Dokumen &amp; Tapak ({{ $totalEpuPendingVerifikasi }})</span>
                    </a>
                    @endif
                    @if($user->role === 'pegawai_pelesen' || $user->isSuperAdmin())
                    <a href="{{ route('epu.index', ['status' => 'Menunggu Kelulusan Pelesen']) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold shadow-lg shadow-rose-900/40 transition">
                        <i class="fa-solid fa-stamp"></i>
                        <span>Kelulusan Pelesen / Pengarah ({{ $totalEpuPendingPelesen }})</span>
                    </a>
                    @endif
                    @if($user->role === 'admin_epu' || $user->isSuperAdmin())
                    <a href="{{ route('epu.index', ['status' => 'Diluluskan']) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold shadow-lg shadow-teal-900/40 transition">
                        <i class="fa-solid fa-receipt"></i>
                        <span>Pengesahan Bayaran Fi ({{ $totalEpuPendingBayaran }})</span>
                    </a>
                    @endif
                @else
                    @if(Auth::user()->canAccessEptr())
                        @if(!$user->isStaff() || $user->isSuperAdmin())
                        <a href="{{ route('eptr.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-lg shadow-emerald-900/40 transition">
                            <i class="fa-solid fa-plus-circle"></i>
                            <span>Daftar Ternakan (Borang A)</span>
                        </a>
                        <a href="{{ route('eptr.daftar-anak') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-pink-700 hover:bg-pink-600 text-white text-xs font-bold shadow-lg shadow-pink-900/40 transition">
                            <i class="fa-solid fa-baby"></i>
                            <span>Daftar Anak Ternakan</span>
                        </a>
                        @endif
                        <a href="{{ route('eptr.kesihatan.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-teal-700 hover:bg-teal-600 text-white text-xs font-bold shadow-lg shadow-teal-900/40 transition">
                            <i class="fa-solid fa-heart-pulse"></i>
                            <span>Program Kesihatan & Vaksin</span>
                        </a>
                    @endif
                    @if(Auth::user()->canAccessEpu())
                        <a href="{{ route('epu.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold shadow-lg shadow-amber-900/40 transition">
                            <i class="fa-solid fa-feather"></i>
                            <span>Mohon Lesen EPU Unggas</span>
                        </a>
                    @endif
                    @if(Auth::user()->canRequestInventori())
                        <a href="{{ route('inventori.permohonan.saya') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-700 hover:bg-indigo-600 text-white text-xs font-bold shadow-lg shadow-indigo-900/40 transition">
                            <i class="fa-solid fa-clipboard-list"></i>
                            <span>Permohonan Bekalan Stor</span>
                        </a>
                    @endif
                    <a href="{{ route('kursus.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-cyan-700 hover:bg-cyan-600 text-white text-xs font-bold shadow-lg shadow-cyan-900/40 transition">
                        <i class="fa-solid fa-graduation-cap"></i>
                        <span>Daftar Kursus Ternakan</span>
                    </a>
                    @if(Auth::user()->canAccessKlinik())
                    <a href="{{ route('klinik.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-rose-700 hover:bg-rose-600 text-white text-xs font-bold shadow-lg shadow-rose-900/40 transition">
                        <i class="fa-solid fa-calendar-check"></i>
                        <span>Temujanji Klinik Haiwan</span>
                    </a>
                    @endif
                    @if(Auth::user()->canAccessPawah() && (!Auth::user()->isStaff() || Auth::user()->isSuperAdmin()))
                    <a href="{{ route('pawah.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-600 text-white text-xs font-bold shadow-lg shadow-emerald-900/40 transition">
                        <i class="fa-solid fa-handshake-angle"></i>
                        <span>Mohon Program Pawah</span>
                    </a>
                    @endif
                @endif
            </div>
        </div>

        <div class="absolute right-0 bottom-0 opacity-10 translate-x-10 translate-y-10 pointer-events-none hidden lg:block">
            <i class="fa-solid {{ in_array($user->role, ['admin_pejabat', 'admin_stor_pejabat', 'pegawai_pengesah_pejabat', 'admin_pelulus_pejabat']) ? 'fa-boxes-stacked' : ($user->role === 'admin_kenderaan' ? 'fa-truck-pickup' : ($user->role === 'admin_program' ? 'fa-handshake-angle' : 'fa-cow')) }} text-[280px]"></i>
        </div>
    </div>

    @if(in_array($user->role, ['admin_pejabat', 'admin_stor_pejabat', 'pegawai_pengesah_pejabat', 'admin_pelulus_pejabat']))
        <!-- Admin Stor Pejabat KPI Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Jumlah Item Inventori Pejabat -->
            <a href="{{ route('inventori.pejabat.index') }}" class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-indigo-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-400 uppercase">Stor Pejabat</span>
                </div>
                <div>
                    <div class="text-3xl font-black text-slate-900">{{ $totalPejabatItems }}</div>
                    <div class="text-sm font-semibold text-slate-700">Jumlah Barangan Pejabat</div>
                    <div class="text-xs text-slate-500 mt-1">Katalog Peralatan Pejabat</div>
                </div>
            </a>

            <!-- 2. Stok Rendah -->
            <a href="{{ route('inventori.pejabat.index', ['status' => 'Stok Rendah']) }}" class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-amber-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <span class="text-xs font-bold text-amber-600 uppercase">Amaran Stok</span>
                </div>
                <div>
                    <div class="text-3xl font-black {{ $lowStockPejabatCount > 0 ? 'text-amber-600' : 'text-slate-900' }}">{{ $lowStockPejabatCount }}</div>
                    <div class="text-sm font-semibold text-slate-700">Item Stok Rendah / Habis</div>
                    <div class="text-xs text-slate-500 mt-1">Perlu Pembekalan Semula</div>
                </div>
            </a>

            <!-- 3. Permohonan Menunggu Kelulusan -->
            <a href="{{ route('inventori.pejabat.permohonan') }}" class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-blue-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <span class="text-xs font-bold text-blue-600 uppercase">Permohonan</span>
                </div>
                <div>
                    <div class="text-3xl font-black {{ $pendingPejabatRequests > 0 ? 'text-blue-600' : 'text-slate-900' }}">{{ $pendingPejabatRequests }}</div>
                    <div class="text-sm font-semibold text-slate-700">Permohonan Alatan Menunggu</div>
                    <div class="text-xs text-slate-500 mt-1">Kelulusan Stor Pejabat</div>
                </div>
            </a>

            <!-- 4. Pinjaman Alatan Aktif -->
            <a href="{{ route('inventori.pejabat.index') }}" class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-emerald-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-hand-holding-box"></i>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 uppercase">Pinjaman</span>
                </div>
                <div>
                    <div class="text-3xl font-black text-slate-900">{{ $totalPejabatPinjaman }}</div>
                    <div class="text-sm font-semibold text-slate-700">Pinjaman Peralatan Aktif</div>
                    <div class="text-xs text-slate-500 mt-1">Dalam Pegangan Staf Jabatan</div>
                </div>
            </a>
        </div>

        <!-- Admin Stor Pejabat Lower Activity Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left: Inventori Pejabat Terkini -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Senarai Inventori & Stok Terkini</h3>
                    </div>
                    <a href="{{ route('inventori.pejabat.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-bold">
                        Urus Stor Pejabat &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 uppercase font-semibold">
                                <th class="py-2.5">Kod & Nama Item</th>
                                <th class="py-2.5">Kategori</th>
                                <th class="py-2.5">Baki Stok</th>
                                <th class="py-2.5">Status</th>
                                <th class="py-2.5 text-right">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($recentInventory as $item)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3">
                                        <div class="font-bold text-slate-900">{{ $item->nama_item }}</div>
                                        <div class="font-mono text-[11px] text-slate-500">{{ $item->kod_item }}</div>
                                    </td>
                                    <td class="py-3 text-slate-600">{{ $item->kategori }}</td>
                                    <td class="py-3 font-bold text-slate-900">{{ $item->kuantiti }} {{ $item->unit }}</td>
                                    <td class="py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $item->status === 'Dalam Stok' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-right">
                                        <a href="{{ route('inventori.show', $item->id) }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-indigo-100 text-slate-600 hover:text-indigo-700 transition" title="Lihat Butiran Item">
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-400">
                                        Tiada rekod inventori dijumpai.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right: Permohonan Stor Pejabat Terkini -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-clipboard-list"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Permohonan Alatan Pejabat Terkini</h3>
                    </div>
                    <a href="{{ route('inventori.pejabat.permohonan') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-bold">
                        Semua Permohonan &rarr;
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($recentPejabatPermohonan as $p)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 hover:bg-indigo-50/30 transition flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs font-bold text-slate-900">{{ $p->no_permohonan ?? 'REQ-'.$p->id }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $p->status === 'Diluluskan' ? 'bg-emerald-100 text-emerald-800' : ($p->status === 'Menunggu Kelulusan' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-800') }}">
                                        {{ $p->status }}
                                    </span>
                                </div>
                                <div class="text-xs font-medium text-slate-700 mt-1">
                                    {{ $p->item->nama_item ?? 'Item Stor' }} ({{ $p->kuantiti_dimohon }} {{ $p->item->unit ?? 'unit' }})
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    Pemohon: <span class="font-semibold text-slate-800">{{ $p->user->name ?? 'Staf' }}</span> &bull; Tarikh: {{ \Carbon\Carbon::parse($p->created_at)->format('d/m/Y') }}
                                </div>
                            </div>
                            <a href="{{ route('inventori.pejabat.permohonan') }}" class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:border-indigo-500 text-xs font-bold text-slate-700 hover:text-indigo-700 shadow-2xs transition">
                                Urus &rarr;
                            </a>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400 text-xs">
                            Tiada permohonan alatan pejabat aktif.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @elseif($user->role === 'admin_kenderaan')
        <!-- Admin Kenderaan KPI Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Kenderaan Sedia Digunakan -->
            <a href="{{ route('kenderaan.fleet') }}" class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-teal-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-truck-pickup"></i>
                    </div>
                    <span class="text-xs font-bold text-teal-600 uppercase">Armada Kenderaan</span>
                </div>
                <div>
                    <div class="text-3xl font-black text-slate-900">{{ $availableVehicles }} / {{ $totalVehicles }}</div>
                    <div class="text-sm font-semibold text-slate-700">Kenderaan Sedia Digunakan</div>
                    <div class="text-xs text-slate-500 mt-1">{{ $totalVehicles }} Jumlah Kenderaan Jabatan</div>
                </div>
            </a>

            <!-- 2. Tempahan Menunggu Kelulusan -->
            <a href="{{ route('kenderaan.index', ['status' => 'Menunggu']) }}" class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-amber-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <span class="text-xs font-bold text-amber-600 uppercase">Menunggu Tindakan</span>
                </div>
                <div>
                    <div class="text-3xl font-black {{ $pendingVehicleBookings > 0 ? 'text-amber-600' : 'text-slate-900' }}">{{ $pendingVehicleBookings }}</div>
                    <div class="text-sm font-semibold text-slate-700">Tempahan Menunggu Kelulusan</div>
                    <div class="text-xs text-slate-500 mt-1">Perlu Semakan Admin Kenderaan</div>
                </div>
            </a>

            <!-- 3. Kenderaan Sedang Digunakan -->
            <a href="{{ route('kenderaan.fleet', ['status' => 'Sedang Digunakan']) }}" class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-blue-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-route"></i>
                    </div>
                    <span class="text-xs font-bold text-blue-600 uppercase">Dalam Perjalanan</span>
                </div>
                <div>
                    <div class="text-3xl font-black text-slate-900">{{ $inUseVehicles }}</div>
                    <div class="text-sm font-semibold text-slate-700">Kenderaan Sedang Digunakan</div>
                    <div class="text-xs text-slate-500 mt-1">Tugasan Luar / Rasmi</div>
                </div>
            </a>

            <!-- 4. Pemandu Jabatan -->
            <a href="{{ route('kenderaan.pemandu.index') }}" class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-emerald-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-id-card-clip"></i>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 uppercase">Pemandu Jabatan</span>
                </div>
                <div>
                    <div class="text-3xl font-black text-slate-900">{{ $activePemandu }} / {{ $totalPemandu }}</div>
                    <div class="text-sm font-semibold text-slate-700">Pemandu Aktif & Bertugas</div>
                    <div class="text-xs text-slate-500 mt-1">{{ $totalPemandu }} Pemandu Berdaftar</div>
                </div>
            </a>
        </div>

        <!-- Admin Kenderaan Lower Activity Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left: Permohonan Kenderaan Terkini -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Tempahan Kenderaan Rasmi Terkini</h3>
                    </div>
                    <a href="{{ route('kenderaan.index') }}" class="text-xs text-teal-600 hover:text-teal-800 font-bold">
                        Semua Tempahan &rarr;
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($recentTempahanKenderaan as $tk)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 hover:bg-teal-50/30 transition flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs font-bold text-slate-900">{{ $tk->no_tempahan ?? 'TK-'.$tk->id }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $tk->status === 'Diluluskan' ? 'bg-emerald-100 text-emerald-800' : ($tk->status === 'Menunggu' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-800') }}">
                                        {{ $tk->status }}
                                    </span>
                                </div>
                                <div class="text-xs font-medium text-slate-700 mt-1">
                                    {{ $tk->kenderaan->model ?? 'Kenderaan Jabatan' }} ({{ $tk->kenderaan->no_pendaftaran ?? 'N/A' }}) &bull; Destinasi: <span class="font-bold text-slate-800">{{ $tk->destinasi }}</span>
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    Pemohon: <span class="font-semibold text-slate-800">{{ $tk->pemohon->name ?? 'Pegawai' }}</span> &bull; Tarikh: {{ \Carbon\Carbon::parse($tk->tarikh_mula)->format('d/m/Y') }}
                                </div>
                            </div>
                            <a href="{{ route('kenderaan.show', $tk->id) }}" class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:border-teal-500 text-xs font-bold text-slate-700 hover:text-teal-700 shadow-2xs transition">
                                Butiran &rarr;
                            </a>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400 text-xs">
                            Tiada tempahan kenderaan aktif.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Right: Status Armada Kenderaan Rasmi -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-truck-pickup"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Armada Kenderaan Jabatan</h3>
                    </div>
                    <a href="{{ route('kenderaan.fleet') }}" class="text-xs text-teal-600 hover:text-teal-800 font-bold">
                        Urus Armada &rarr;
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($recentFleet as $vk)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 hover:bg-teal-50/30 transition flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-xs text-slate-900">{{ $vk->no_pendaftaran }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $vk->status === 'Sedia' ? 'bg-emerald-100 text-emerald-800' : ($vk->status === 'Sedang Digunakan' ? 'bg-blue-100 text-blue-800' : 'bg-rose-100 text-rose-800') }}">
                                        {{ $vk->status }}
                                    </span>
                                </div>
                                <div class="text-xs font-medium text-slate-700 mt-1">
                                    {{ $vk->model }} ({{ $vk->jenis_kenderaan }}) &bull; Penempatan: {{ $vk->jajahan_penempatan }}
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    Kapasiti: {{ $vk->kapasiti_penumpang }} Orang &bull; Odometer: {{ number_format($vk->odometer_semasa_km) }} KM
                                </div>
                            </div>
                            <a href="{{ route('kenderaan.fleet') }}" class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:border-teal-500 text-xs font-bold text-slate-700 hover:text-teal-700 shadow-2xs transition">
                                Lihat &rarr;
                            </a>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400 text-xs">
                            Tiada rekod kenderaan dijumpai.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @elseif($user->role === 'admin_program')
        <!-- Admin Program Pawah KPI Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Perjanjian Pawah Aktif -->
            <a href="{{ route('pawah.index', ['status' => 'Aktif']) }}" class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-emerald-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-handshake-angle"></i>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 uppercase">Perjanjian Aktif</span>
                </div>
                <div>
                    <div class="text-3xl font-black text-slate-900">{{ $totalPawahActive }}</div>
                    <div class="text-sm font-semibold text-slate-700">Perjanjian Pawah Berjalan</div>
                    <div class="text-xs text-slate-500 mt-1">Kontrak Dalam Tempoh 3 Tahun</div>
                </div>
            </a>

            <!-- 2. Permohonan Menunggu Kelulusan -->
            <a href="{{ route('pawah.index', ['status' => 'Menunggu Kelulusan']) }}" class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-amber-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <span class="text-xs font-bold text-amber-600 uppercase">Menunggu Kelulusan</span>
                </div>
                <div>
                    <div class="text-3xl font-black {{ $totalPawahMenunggu > 0 ? 'text-amber-600' : 'text-slate-900' }}">{{ $totalPawahMenunggu }}</div>
                    <div class="text-sm font-semibold text-slate-700">Permohonan Awam Baharu</div>
                    <div class="text-xs text-slate-500 mt-1">Semak &amp; Luluskan &rarr;</div>
                </div>
            </a>

            <!-- 3. Jumlah Induk Dipawah -->
            <a href="{{ route('pawah.index') }}" class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-emerald-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-cow"></i>
                    </div>
                    <span class="text-xs font-bold text-teal-600 uppercase">Ternakan Induk</span>
                </div>
                <div>
                    <div class="text-3xl font-black text-slate-900">{{ $totalPawahTernakanInduk }}</div>
                    <div class="text-sm font-semibold text-slate-700">Induk Lembu / Kambing / Rusa</div>
                    <div class="text-xs text-teal-600 font-semibold mt-1">Hak Milik JPVNK Tersedia</div>
                </div>
            </a>

            <!-- 4. Rekod Kelahiran & Rawatan -->
            <div class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-baby"></i>
                    </div>
                    <span class="text-xs font-bold text-purple-600 uppercase">Kelahiran Anak</span>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900">{{ $totalPawahKelahiran }} Kelahiran</div>
                    <div class="text-sm font-semibold text-slate-700">{{ $totalPawahKesihatan }} Rekod Pemantauan</div>
                    <div class="text-xs text-purple-600 font-medium mt-1">Status Pemulangan Anak</div>
                </div>
            </div>
        </div>

        <!-- Admin Program Main Content Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Senarai Surat Perjanjian & Permohonan Terkini -->
            <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200 p-6 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-handshake-angle"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Senarai Perjanjian &amp; Permohonan Pawah Terkini</h3>
                            <p class="text-[11px] text-slate-500">Pantau status surat perjanjian pawah ternakan seluruh Kelantan</p>
                        </div>
                    </div>
                    <a href="{{ route('pawah.index') }}" class="text-xs text-emerald-600 hover:text-emerald-800 font-bold">
                        Lihat Semua Perjanjian &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 uppercase font-semibold">
                                <th class="py-2.5">No. Perjanjian</th>
                                <th class="py-2.5">Peserta / Pemohon</th>
                                <th class="py-2.5">Jenis Pawah</th>
                                <th class="py-2.5">Jajahan</th>
                                <th class="py-2.5">Status</th>
                                <th class="py-2.5 text-right">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($recentPawah as $p)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3 font-mono font-bold text-emerald-800">
                                        {{ $p->no_perjanjian }}
                                    </td>
                                    <td class="py-3">
                                        <div class="font-bold text-slate-900">{{ $p->peserta->name ?? 'Pemohon Awam' }}</div>
                                        <div class="text-[11px] text-slate-500 font-mono">{{ $p->peserta->ic_number ?? '-' }}</div>
                                    </td>
                                    <td class="py-3">
                                        <div class="font-semibold text-slate-800">{{ $p->jenis_pawah ?? 'Lembu Hibrid' }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $p->bilangan_induk }} Ekor Induk</div>
                                    </td>
                                    <td class="py-3 text-slate-600 font-medium">
                                        {{ $p->jajahan ?? '-' }}
                                    </td>
                                    <td class="py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $p->status === 'Aktif' ? 'bg-emerald-100 text-emerald-800' : ($p->status === 'Menunggu Kelulusan' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-800') }}">
                                            {{ $p->status }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-right space-x-1">
                                        <a href="{{ route('pawah.show', $p->id) }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-emerald-100 text-slate-600 hover:text-emerald-700 transition" title="Buka Butiran Perjanjian">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('pawah.cetak-perjanjian', $p->id) }}" target="_blank" class="p-1.5 rounded-lg bg-amber-100 hover:bg-amber-200 text-amber-900 transition" title="Cetak Surat Perjanjian PDF">
                                            <i class="fa-solid fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-400">
                                        Tiada rekod perjanjian pawah dijumpai.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right: Tindakan & Panduan Skim Pawah -->
            <div class="space-y-6">
                <!-- Box 1: Tindakan Pantas -->
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs">
                    <div class="flex items-center gap-2.5 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Tindakan Pantas</h3>
                    </div>
                    <div class="space-y-2.5">
                        <a href="{{ route('pawah.create') }}" class="w-full flex items-center justify-between p-3.5 rounded-2xl bg-emerald-50/80 hover:bg-emerald-100/80 border border-emerald-200/80 text-emerald-900 transition group">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-file-signature text-emerald-700"></i>
                                <span class="text-xs font-bold">Daftar Perjanjian Baharu</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-xs text-emerald-600 group-hover:translate-x-0.5 transition-transform"></i>
                        </a>
                        <a href="{{ route('pawah.index', ['status' => 'Menunggu Kelulusan']) }}" class="w-full flex items-center justify-between p-3.5 rounded-2xl bg-amber-50/80 hover:bg-amber-100/80 border border-amber-200/80 text-amber-900 transition group">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-clipboard-check text-amber-700"></i>
                                <span class="text-xs font-bold">Kelulusan Permohonan Awam ({{ $totalPawahMenunggu }})</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-xs text-amber-600 group-hover:translate-x-0.5 transition-transform"></i>
                        </a>
                    </div>
                </div>

                <!-- Box 2: Panduan Skim Pawah Ternakan -->
                <div class="bg-gradient-to-br from-slate-900 to-emerald-950 rounded-3xl p-6 text-white shadow-xs">
                    <div class="flex items-center gap-2.5 mb-3">
                        <i class="fa-solid fa-circle-info text-emerald-400"></i>
                        <h4 class="text-sm font-bold text-white">Syarat Skim Pawah JPVNK</h4>
                    </div>
                    <ul class="text-xs text-slate-300 space-y-2 leading-relaxed list-disc list-inside">
                        <li>Semua ternakan induk didaftarkan atas nama <b class="text-white">Jabatan Perkhidmatan Veterinar Negeri Kelantan</b>.</li>
                        <li>Peserta wajib memulangkan <b class="text-emerald-300">1 ekor anak betina pertama (umur &ge; 12 bulan)</b> kepada Jabatan.</li>
                        <li>Induk pawah menjadi hak milik kekal penternak setelah syarat pemulangan anak disempurnakan.</li>
                    </ul>
                </div>
            </div>
        </div>
    @elseif($user->role === 'admin_eptr')
        <!-- Admin EPTR KPI Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Jumlah Ternakan EPTR -->
            <a href="{{ route('eptr.index') }}" class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-emerald-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-cow"></i>
                    </div>
                    <span class="text-xs font-bold text-amber-600 uppercase">Ternakan Ruminan</span>
                </div>
                <div>
                    <div class="text-3xl font-black text-slate-900">{{ $totalTernakanEptr }}</div>
                    <div class="text-sm font-semibold text-slate-700">Ternakan Berdaftar EPTR</div>
                    <div class="text-xs text-slate-500 mt-1">Lembu, Kerbau, Kambing &amp; Biri-Biri</div>
                </div>
            </a>

            <!-- 2. Pendaftaran Menunggu Kelulusan -->
            <a href="{{ route('eptr.index', ['status_kelulusan' => 'Menunggu']) }}" class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-amber-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <span class="text-xs font-bold text-amber-600 uppercase">Menunggu Kelulusan</span>
                </div>
                <div>
                    <div class="text-3xl font-black {{ $totalPendingTernakan > 0 ? 'text-amber-600' : 'text-slate-900' }}">{{ $totalPendingTernakan }}</div>
                    <div class="text-sm font-semibold text-slate-700">Permohonan Tag Telinga</div>
                    <div class="text-xs text-slate-500 mt-1">Semak &amp; Jana No. Tag &rarr;</div>
                </div>
            </a>

            <!-- 3. Program Kesihatan & Vaksinasi -->
            <a href="{{ route('eptr.kesihatan.index') }}" class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-teal-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-heart-pulse"></i>
                    </div>
                    <span class="text-xs font-bold text-teal-600 uppercase">Kesihatan &amp; Vaksin</span>
                </div>
                <div>
                    <div class="text-3xl font-black text-slate-900">{{ $totalKesihatan }}</div>
                    <div class="text-sm font-semibold text-slate-700">Rekod Rawatan &amp; Vaksin</div>
                    <div class="text-xs text-teal-600 font-semibold mt-1">Imunisasi &amp; Penyahcacingan</div>
                </div>
            </a>

            <!-- 4. Permit Sembelihan -->
            <a href="{{ route('eptr.borang-d.index') }}" class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-indigo-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-file-invoice"></i>
                    </div>
                    <span class="text-xs font-bold text-indigo-600 uppercase">Permit Sembelih</span>
                </div>
                <div>
                    <div class="text-3xl font-black text-slate-900">{{ $totalPermitSembelihan }}</div>
                    <div class="text-sm font-semibold text-slate-700">Borang D Dikeluarkan</div>
                    <div class="text-xs text-indigo-600 font-medium mt-1">Urus Permit &rarr;</div>
                </div>
            </a>
        </div>

        <!-- Charts & Analytics Section (Admin EPTR Negeri) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Chart 1: EPTR & Pawah Distribution -->
            <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Taburan Ternakan EPTR & Program Pawah Mengikut Jajahan</h3>
                        <p class="text-xs text-slate-500">Statistik masa nyata pendaftaran ruminan Negeri Kelantan 2026</p>
                    </div>
                    <span class="text-xs bg-emerald-50 text-emerald-700 font-bold px-3 py-1 rounded-full border border-emerald-200">
                        Live Data
                    </span>
                </div>
                <div id="eptrChart" class="h-64"></div>
            </div>

            <!-- Chart 2: Modul Engagement Pie -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900 mb-1">Pecahan Modul Perkhidmatan</h3>
                    <p class="text-xs text-slate-500 mb-4">Aktiviti penggunaan pangkalan data sepunya</p>
                    <div id="servicePieChart" class="h-56"></div>
                </div>
                <div class="pt-4 border-t border-slate-100 text-xs text-slate-500 flex justify-between">
                    <span>Pengguna Berdaftar: <b>{{ $totalUsers }}</b></span>
                    <span class="text-emerald-600 font-semibold">10 Peranan Aktif</span>
                </div>
            </div>
        </div>

        <!-- Admin EPTR Main Content Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Senarai Pendaftaran Ternakan Terkini -->
            <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200 p-6 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-cow"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Senarai Pendaftaran Ternakan EPTR Terkini</h3>
                            <p class="text-[11px] text-slate-500">Pangkalan data pendaftaran ruminan &amp; Kad Kuning Borang B</p>
                        </div>
                    </div>
                    <a href="{{ route('eptr.index') }}" class="text-xs text-amber-600 hover:text-amber-800 font-bold">
                        Lihat Semua Ternakan &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 uppercase font-semibold">
                                <th class="py-2.5">No Tag</th>
                                <th class="py-2.5">Pemunya / Penternak</th>
                                <th class="py-2.5">Baka &amp; Jantina</th>
                                <th class="py-2.5">Jajahan</th>
                                <th class="py-2.5">Status</th>
                                <th class="py-2.5 text-right">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($recentTernakan as $t)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3 font-mono font-bold text-emerald-800">
                                        {{ $t->no_tag ?? 'BELUM JANA' }}
                                    </td>
                                    <td class="py-3">
                                        <div class="font-bold text-slate-900">{{ $t->pemunya->nama ?? '-' }}</div>
                                        <div class="text-[11px] text-slate-500 font-mono">{{ $t->pemunya->no_kp ?? '-' }}</div>
                                    </td>
                                    <td class="py-3">
                                        <div class="font-semibold text-slate-900">{{ ucfirst($t->baka ?? $t->jenis_ternakan) }}</div>
                                        <div class="text-[11px] text-slate-500">{{ ucfirst($t->jenis_ternakan) }} &bull; {{ $t->jantina }}</div>
                                    </td>
                                    <td class="py-3 text-slate-600 font-medium">
                                        {{ $t->jajahan }}
                                    </td>
                                    <td class="py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $t->status_kelulusan === 'Diluluskan' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ $t->status_kelulusan ?? $t->status }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-right space-x-1">
                                        <a href="{{ route('eptr.show', $t->id) }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-amber-100 text-slate-600 hover:text-amber-700 transition" title="Buka Kad Kuning Borang B">
                                            <i class="fa-solid fa-id-card"></i>
                                        </a>
                                        <a href="{{ route('eptr.cetak-kad-kuning', $t->id) }}" target="_blank" class="p-1.5 rounded-lg bg-amber-100 hover:bg-amber-200 text-amber-900 transition" title="Cetak Kad Kuning PDF">
                                            <i class="fa-solid fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-400">
                                        Tiada rekod pendaftaran ternakan dijumpai.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right: Tindakan Pantas & Panduan Enakmen EPTR -->
            <div class="space-y-6">
                <!-- Box 1: Tindakan Pantas -->
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs">
                    <div class="flex items-center gap-2.5 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Tindakan Pantas EPTR</h3>
                    </div>
                    <div class="space-y-2.5">
                        <a href="{{ route('eptr.index', ['status_kelulusan' => 'Menunggu']) }}" class="w-full flex items-center justify-between p-3.5 rounded-2xl bg-amber-50/80 hover:bg-amber-100/80 border border-amber-200/80 text-amber-900 transition group">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-tag text-amber-700"></i>
                                <span class="text-xs font-bold">Kelulusan No. Tag ({{ $totalPendingTernakan }})</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-xs text-amber-600 group-hover:translate-x-0.5 transition-transform"></i>
                        </a>
                        <a href="{{ route('eptr.kesihatan.index') }}" class="w-full flex items-center justify-between p-3.5 rounded-2xl bg-teal-50/80 hover:bg-teal-100/80 border border-teal-200/80 text-teal-900 transition group">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-heart-pulse text-teal-700"></i>
                                <span class="text-xs font-bold">Rekod Vaksinasi &amp; Rawatan</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-xs text-teal-600 group-hover:translate-x-0.5 transition-transform"></i>
                        </a>
                        <a href="{{ route('eptr.borang-d.index') }}" class="w-full flex items-center justify-between p-3.5 rounded-2xl bg-indigo-50/80 hover:bg-indigo-100/80 border border-indigo-200/80 text-indigo-900 transition group">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-file-invoice text-indigo-700"></i>
                                <span class="text-xs font-bold">Permit Sembelihan (Borang D)</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-xs text-indigo-600 group-hover:translate-x-0.5 transition-transform"></i>
                        </a>
                        <a href="{{ route('eptr.pemindahan.index') }}" class="w-full flex items-center justify-between p-3.5 rounded-2xl bg-blue-50/80 hover:bg-blue-100/80 border border-blue-200/80 text-blue-900 transition group">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-truck-moving text-blue-700"></i>
                                <span class="text-xs font-bold">Permit Pemindahan Ternakan</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-xs text-blue-600 group-hover:translate-x-0.5 transition-transform"></i>
                        </a>
                        <a href="{{ route('eptr.borang-c.index') }}" class="w-full flex items-center justify-between p-3.5 rounded-2xl bg-rose-50/80 hover:bg-rose-100/80 border border-rose-200/80 text-rose-900 transition group">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-circle-xmark text-rose-700"></i>
                                <span class="text-xs font-bold">Pembatalan / Kematian (Borang C)</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-xs text-rose-600 group-hover:translate-x-0.5 transition-transform"></i>
                        </a>
                    </div>
                </div>

                <!-- Box 2: Panduan Enakmen EPTR -->
                <div class="bg-gradient-to-br from-slate-900 to-amber-950 rounded-3xl p-6 text-white shadow-xs">
                    <div class="flex items-center gap-2.5 mb-3">
                        <i class="fa-solid fa-shield-halved text-amber-400"></i>
                        <h4 class="text-sm font-bold text-white">Enakmen Pendaftaran Ternakan Ruminan 2024</h4>
                    </div>
                    <ul class="text-xs text-slate-300 space-y-2 leading-relaxed list-disc list-inside">
                        <li>Semua ternakan ruminan wajib dipasang <b class="text-amber-300">Tag Telinga Rasmi JPVNK</b>.</li>
                        <li>Setiap pendaftaran menjana <b class="text-white">Kad Kuning (Borang B)</b> beserta Kod QR unik.</li>
                        <li>Penyembelihan memerlukan pengeluaran <b class="text-white">Permit Sembelihan (Borang D)</b> yang sah.</li>
                    </ul>
                </div>
            </div>
        </div>
    @elseif($user->role === 'admin_kursus')
        <!-- Admin Kursus KPI Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Jumlah Kursus Diterbitkan -->
            <a href="{{ route('kursus.index') }}" class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-cyan-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <span class="text-xs font-bold text-cyan-600 uppercase">Kursus Aktif</span>
                </div>
                <div>
                    <div class="text-3xl font-black text-slate-900">{{ $totalCourses }}</div>
                    <div class="text-sm font-semibold text-slate-700">Kursus & Bengkel Latihan</div>
                    <div class="text-xs text-slate-500 mt-1">Diterbitkan untuk Penternak</div>
                </div>
            </a>

            <!-- 2. Jumlah Permohonan Peserta -->
            <a href="{{ route('kursus.pemohon.index') }}" class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-emerald-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 uppercase">Hab Pemohon</span>
                </div>
                <div>
                    <div class="text-3xl font-black text-slate-900">{{ $totalCourseApplications }}</div>
                    <div class="text-sm font-semibold text-slate-700">Jumlah Permohonan Peserta</div>
                    <div class="text-xs text-emerald-600 font-semibold mt-1">Urus Kelulusan &amp; Kehadiran &rarr;</div>
                </div>
            </a>

            <!-- 3. Kategori Kursus -->
            <div class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-400 uppercase">Bidang Kursus</span>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900">Ruminan & Unggas</div>
                    <div class="text-sm font-semibold text-slate-700">Silaj & Biosekuriti</div>
                    <div class="text-xs text-slate-500 mt-1">Silabus Komprehensif</div>
                </div>
            </div>

            <!-- 4. Sijil Digital PDF -->
            <div class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-certificate"></i>
                    </div>
                    <span class="text-xs font-bold text-purple-600 uppercase">Pensijilan</span>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900">Sijil Digital</div>
                    <div class="text-sm font-semibold text-slate-700">Penjanaan 1-Klik</div>
                    <div class="text-xs text-purple-600 font-medium mt-1">Diperakui Pengarah JPVNK</div>
                </div>
            </div>
        </div>

        <!-- Admin Kursus Content Section -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-cyan-100 text-cyan-700 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">
                        Senarai Kursus Ternakan Diterbitkan
                    </h3>
                </div>
                <a href="{{ route('kursus.index') }}" class="text-xs text-cyan-700 hover:text-cyan-900 font-bold">
                    Lihat Direktori Kursus &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 uppercase font-semibold">
                            <th class="py-2.5">Tajuk Kursus</th>
                            <th class="py-2.5">Kategori</th>
                            <th class="py-2.5">Tarikh & Masa</th>
                            <th class="py-2.5">Lokasi & Jajahan</th>
                            <th class="py-2.5">Penyertaan / Kapasiti</th>
                            <th class="py-2.5">Status</th>
                            <th class="py-2.5 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentCourses as $c)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3">
                                    <div class="font-bold text-slate-900">{{ $c->title }}</div>
                                    <div class="text-[11px] text-slate-500 font-mono">{{ $c->code }} &bull; Tenaga Pengajar: {{ $c->trainer_name }}</div>
                                </td>
                                <td class="py-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-cyan-50 text-cyan-800 border border-cyan-200">
                                        {{ $c->category }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <div class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($c->start_date)->format('d/m/Y') }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $c->time }}</div>
                                </td>
                                <td class="py-3">
                                    <div class="font-semibold text-slate-800">{{ $c->location }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $c->jajahan }}</div>
                                </td>
                                <td class="py-3">
                                    <span class="font-bold text-slate-900">{{ $c->applications_count ?? 0 }}</span> / {{ $c->capacity }} Orang
                                </td>
                                <td class="py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                        {{ $c->status }}
                                    </span>
                                </td>
                                <td class="py-3 text-right">
                                    <a href="{{ route('kursus.show', $c->id) }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-cyan-100 text-slate-600 hover:text-cyan-700 transition" title="Lihat Butiran & Peserta">
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-6 text-center text-slate-400">
                                    Tiada kursus diterbitkan setakat ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @elseif($user->role === 'admin_klinik')
        <!-- Admin Klinik KPI Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Jumlah Temujanji -->
            <a href="{{ route('klinik.index') }}" class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-rose-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <span class="text-xs font-bold text-rose-600 uppercase">Temujanji</span>
                </div>
                <div>
                    <div class="text-3xl font-black text-slate-900">{{ $totalClinicAppointments }}</div>
                    <div class="text-sm font-semibold text-slate-700">Jumlah Temujanji Rawatan</div>
                    <div class="text-xs text-slate-500 mt-1">Keseluruhan Klinik Jajahan</div>
                </div>
            </a>

            <!-- 2. Rawatan Selesai -->
            <a href="{{ route('klinik.index', ['status' => 'Selesai']) }}" class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-emerald-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 uppercase">Selesai</span>
                </div>
                <div>
                    <div class="text-3xl font-black text-emerald-700">{{ \App\Models\KlinikTemujanji::where('status', 'Selesai')->count() }}</div>
                    <div class="text-sm font-semibold text-slate-700">Pesakit Selesai Rawatan</div>
                    <div class="text-xs text-emerald-600 font-medium mt-1">Rekod Perubatan Lengkap</div>
                </div>
            </a>

            <!-- 3. Rekod Rawatan Veterinar -->
            <div class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-notes-medical"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-400 uppercase">Perubatan</span>
                </div>
                <div>
                    <div class="text-3xl font-black text-slate-900">{{ \App\Models\KlinikRawatan::count() }}</div>
                    <div class="text-sm font-semibold text-slate-700">Kad Rawatan Dikeluarkan</div>
                    <div class="text-xs text-slate-500 mt-1">Diagnosis &amp; Preskripsi Ubat</div>
                </div>
            </div>

            <!-- 4. Daftar Temujanji Baru -->
            <a href="{{ route('klinik.create') }}" class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-indigo-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-calendar-plus"></i>
                    </div>
                    <span class="text-xs font-bold text-indigo-600 uppercase">Daftar Pantas</span>
                </div>
                <div>
                    <div class="text-lg font-black text-slate-900">Temujanji Kaunter</div>
                    <div class="text-sm font-semibold text-slate-700">Pendaftaran Walk-In</div>
                    <div class="text-xs text-indigo-600 font-medium mt-1">Buka Borang &rarr;</div>
                </div>
            </a>
        </div>

        <!-- Admin Klinik Main Content Section -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-stethoscope"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Senarai Temujanji Klinik Veterinar Terkini</h3>
                        <p class="text-[11px] text-slate-500">Semak status kehadiran pesakit dan masukkan laporan rawatan</p>
                    </div>
                </div>
                <a href="{{ route('klinik.index') }}" class="text-xs text-rose-600 hover:text-rose-800 font-bold">
                    Lihat Semua Temujanji &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 uppercase font-semibold">
                            <th class="py-2.5">No. Temujanji</th>
                            <th class="py-2.5">Pemilik / Pemohon</th>
                            <th class="py-2.5">Haiwan &amp; Baka</th>
                            <th class="py-2.5">Tujuan / Simptom</th>
                            <th class="py-2.5">Tarikh &amp; Klinik</th>
                            <th class="py-2.5">Status</th>
                            <th class="py-2.5 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentTemujanji as $tj)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 font-mono font-bold text-rose-900">
                                    {{ $tj->no_temujanji }}
                                </td>
                                <td class="py-3">
                                    <div class="font-bold text-slate-900">{{ $tj->pemilik->name ?? 'Pelanggan' }}</div>
                                    <div class="text-[11px] text-slate-500 font-mono">{{ $tj->pemilik->phone ?? '-' }}</div>
                                </td>
                                <td class="py-3">
                                    <div class="font-bold text-slate-800">{{ $tj->jenis_haiwan }} @if($tj->nama_haiwan) ({{ $tj->nama_haiwan }}) @endif</div>
                                    <div class="text-[11px] text-slate-500">{{ $tj->baka ?? 'Baka Tempatan' }} &bull; {{ $tj->jantina_haiwan }}</div>
                                </td>
                                <td class="py-3">
                                    <div class="font-medium text-slate-800 line-clamp-1">{{ $tj->simptom_atau_tujuan }}</div>
                                </td>
                                <td class="py-3">
                                    <div class="font-semibold text-slate-900">{{ $tj->tarikh_temujanji ? $tj->tarikh_temujanji->format('d/m/Y') : '-' }}</div>
                                    <div class="text-[11px] text-slate-500 truncate">{{ $tj->klinik_jajahan }}</div>
                                </td>
                                <td class="py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $tj->status === 'Selesai' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $tj->status }}
                                    </span>
                                </td>
                                <td class="py-3 text-right space-x-1">
                                    <a href="{{ route('klinik.show', $tj->id) }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-rose-100 text-slate-600 hover:text-rose-700 transition" title="Lihat Butiran">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    @if($tj->status !== 'Selesai')
                                        <a href="{{ route('klinik.rawatan.create', $tj->id) }}" class="p-1.5 rounded-lg bg-rose-100 hover:bg-rose-200 text-rose-800 transition" title="Rekod Rawatan">
                                            <i class="fa-solid fa-stethoscope"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">
                                    Tiada rekod temujanji klinik haiwan terkini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @elseif($user->role === 'staf')
        <!-- Kakitangan Biasa KPI Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Permohonan Stor Saya -->
            <a href="{{ route('inventori.permohonan.saya') }}" class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-indigo-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-clipboard-list"></i>
                    </div>
                    <span class="text-xs font-bold text-indigo-600 uppercase">Stor Saya</span>
                </div>
                <div>
                    <div class="text-3xl font-black text-slate-900">{{ $myInventoryRequests->count() }}</div>
                    <div class="text-sm font-semibold text-slate-700">Permohonan Alatan Pejabat</div>
                    <div class="text-xs text-slate-500 mt-1">
                        <span class="font-bold text-amber-600">{{ $myInventoryPendingCount }} Menunggu</span> &bull; 
                        <span class="font-bold text-emerald-600">{{ $myInventoryApprovedCount }} Selesai</span>
                    </div>
                </div>
            </a>

            <!-- 2. Katalog Stor Pejabat -->
            <a href="{{ route('inventori.permohonan.pejabat.mohon') }}" class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-indigo-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-400 uppercase">Katalog Pejabat</span>
                </div>
                <div>
                    <div class="text-3xl font-black text-slate-900">{{ $totalPejabatItems }}</div>
                    <div class="text-sm font-semibold text-slate-700">Barangan Stor Pejabat</div>
                    <div class="text-xs text-indigo-600 font-semibold mt-1">Kertas, Toner & Alat Tulis &rarr;</div>
                </div>
            </a>

            <!-- 3. Kursus Ternakan Dibuka -->
            <a href="{{ route('kursus.index') }}" class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-cyan-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <span class="text-xs font-bold text-cyan-600 uppercase">Latihan &amp; Kursus</span>
                </div>
                <div>
                    <div class="text-3xl font-black text-slate-900">{{ $totalCourses }}</div>
                    <div class="text-sm font-semibold text-slate-700">Kursus Dibuka Penyertaan</div>
                    <div class="text-xs text-slate-500 mt-1">Ruminan, Unggas & Biosekuriti</div>
                </div>
            </a>

            <!-- 4. Kursus Disertai Staf -->
            <a href="{{ route('kursus.index') }}" class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-emerald-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-certificate"></i>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 uppercase">Penyertaan Saya</span>
                </div>
                <div>
                    <div class="text-3xl font-black text-slate-900">{{ $myCourses->count() }}</div>
                    <div class="text-sm font-semibold text-slate-700">Kursus Didaftarkan</div>
                    <div class="text-xs text-emerald-600 font-semibold mt-1">Sijil Digital PDF &rarr;</div>
                </div>
            </a>
        </div>

        <!-- Kakitangan Biasa Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left: Permohonan Stor Alatan Pejabat Saya -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-clipboard-list"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Permohonan Alatan Pejabat Saya</h3>
                                <p class="text-[11px] text-slate-500">Status semakan &amp; kelulusan oleh Admin Pejabat</p>
                            </div>
                        </div>
                        <a href="{{ route('inventori.permohonan.saya') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-bold">
                            Lihat Semua &rarr;
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-400 uppercase font-semibold">
                                    <th class="py-2.5">No. Permohonan & Item</th>
                                    <th class="py-2.5">Kuantiti</th>
                                    <th class="py-2.5">Status</th>
                                    <th class="py-2.5 text-right">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($myInventoryRequests as $req)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-3">
                                            <div class="font-bold text-slate-900">{{ $req->item->nama_item ?? 'Item Stor' }}</div>
                                            <div class="text-[11px] text-slate-500 font-mono">{{ $req->no_permohonan }} &bull; {{ $req->unit_bahagian }}</div>
                                        </td>
                                        <td class="py-3">
                                            <span class="font-bold text-slate-900">{{ $req->kuantiti_dimohon }}</span> {{ $req->item->unit ?? 'Unit' }}
                                        </td>
                                        <td class="py-3">
                                            @if($req->status === 'Diluluskan')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                                    Diluluskan
                                                </span>
                                            @elseif($req->status === 'Telah Diambil / Diserahkan')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-100 text-indigo-800">
                                                    Telah Diserah
                                                </span>
                                            @elseif($req->status === 'Ditolak')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-100 text-rose-800">
                                                    Ditolak
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-800">
                                                    Menunggu
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-right">
                                            <a href="{{ route('inventori.permohonan.saya') }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-indigo-100 text-slate-600 hover:text-indigo-700 transition" title="Lihat Status">
                                                <i class="fa-solid fa-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-8 text-center text-slate-400">
                                            <i class="fa-solid fa-clipboard-check text-2xl text-slate-300 mb-2 block"></i>
                                            Anda belum membuat sebarang permohonan alatan pejabat.
                                            <div class="mt-3">
                                                <a href="{{ route('inventori.permohonan.pejabat.mohon') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition">
                                                    <i class="fa-solid fa-plus"></i> Mohon Sekarang
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-500 font-medium">Perlukan alat tulis atau kertas tambahan?</span>
                    <a href="{{ route('inventori.permohonan.pejabat.mohon') }}" class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 hover:text-indigo-800">
                        <i class="fa-solid fa-plus-circle"></i> Mohon Alatan Pejabat
                    </a>
                </div>
            </div>

            <!-- Right: Katalog Stor Pejabat & Kursus Terbuka -->
            <div class="space-y-6">
                <!-- Box 1: Katalog Stor Pejabat Sedia Ada -->
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-boxes-stacked"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Barangan Stor Pejabat Sedia Ada</h3>
                                <p class="text-[11px] text-slate-500">Pilihan item yang boleh dimohon daripada Stor Pejabat</p>
                            </div>
                        </div>
                        <a href="{{ route('inventori.permohonan.pejabat.mohon') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-bold">
                            Borang Permohonan &rarr;
                        </a>
                    </div>

                    <div class="space-y-2.5">
                        @forelse($officeStoreItems as $item)
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80 hover:bg-indigo-50/30 transition flex items-center justify-between">
                                <div>
                                    <div class="font-bold text-slate-900 text-xs">{{ $item->nama_item }}</div>
                                    <div class="text-[11px] text-slate-500 font-mono">
                                        {{ $item->kod_item }} &bull; {{ $item->kategori }} &bull; Baki: <span class="font-bold text-emerald-700">{{ $item->kuantiti_semasa }} {{ $item->unit }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('inventori.permohonan.pejabat.mohon') }}" class="px-3 py-1 rounded-xl bg-white border border-slate-200 hover:border-indigo-500 text-xs font-bold text-indigo-700 shadow-2xs transition">
                                    Mohon &rarr;
                                </a>
                            </div>
                        @empty
                            <div class="py-4 text-center text-slate-400 text-xs">
                                Tiada barangan stor pejabat tersedia buat masa ini.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Box 2: Kursus & Bengkel Latihan Terbuka -->
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-cyan-100 text-cyan-700 flex items-center justify-center font-bold text-sm">
                                <i class="fa-solid fa-graduation-cap"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Kursus Ternakan Terbuka Terkini</h3>
                                <p class="text-[11px] text-slate-500">Peluang peningkatan kemahiran &amp; persijilan digital</p>
                            </div>
                        </div>
                        <a href="{{ route('kursus.index') }}" class="text-xs text-cyan-700 hover:text-cyan-900 font-bold">
                            Direktori Kursus &rarr;
                        </a>
                    </div>

                    <div class="space-y-2.5">
                        @forelse($recentCourses as $c)
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80 hover:bg-cyan-50/30 transition flex items-center justify-between">
                                <div class="pr-2">
                                    <div class="font-bold text-slate-900 text-xs">{{ $c->title }}</div>
                                    <div class="text-[11px] text-slate-500">
                                        <i class="fa-regular fa-calendar mr-1"></i> {{ \Carbon\Carbon::parse($c->start_date)->format('d/m/Y') }} &bull; {{ $c->location }} ({{ $c->jajahan }})
                                    </div>
                                </div>
                                <a href="{{ route('kursus.show', $c->id) }}" class="shrink-0 px-3 py-1 rounded-xl bg-white border border-slate-200 hover:border-cyan-500 text-xs font-bold text-cyan-700 shadow-2xs transition">
                                    Daftar &rarr;
                                </a>
                            </div>
                        @empty
                            <div class="py-4 text-center text-slate-400 text-xs">
                                Tiada kursus terbuka pada masa ini.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @elseif(in_array($user->role, ['admin_epu', 'pegawai_verifikasi_epu', 'pegawai_pelesen']))
        <!-- EPU KPI Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Ladang Unggas Berdaftar -->
            <a href="{{ route('epu.index') }}" class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-amber-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-warehouse"></i>
                    </div>
                    <span class="text-xs font-bold text-amber-600 uppercase">Ladang Unggas</span>
                </div>
                <div>
                    <div class="text-3xl font-black text-slate-900">{{ $totalEpuFarms }}</div>
                    <div class="text-sm font-semibold text-slate-700">Jumlah Premis / Ladang</div>
                    <div class="text-xs text-slate-500 mt-1">Ayam, Itik, Puyuh &amp; Burung Unta</div>
                </div>
            </a>

            <!-- 2. Menunggu Verifikasi Dokumen & Tapak -->
            <a href="{{ route('epu.index', ['status' => 'Menunggu Semakan Dokumen']) }}" class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-indigo-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-clipboard-check"></i>
                    </div>
                    <span class="text-xs font-bold text-indigo-600 uppercase">Verifikasi Jajahan</span>
                </div>
                <div>
                    <div class="text-3xl font-black {{ $totalEpuPendingVerifikasi > 0 ? 'text-indigo-600' : 'text-slate-900' }}">{{ $totalEpuPendingVerifikasi }}</div>
                    <div class="text-sm font-semibold text-slate-700">Menunggu Semakan &amp; Tapak</div>
                    <div class="text-xs text-indigo-600 font-semibold mt-1">Tindakan Pegawai PPVJ &rarr;</div>
                </div>
            </a>

            <!-- 3. Menunggu Kelulusan Pelesen / Pengarah -->
            <a href="{{ route('epu.index', ['status' => 'Menunggu Kelulusan Pelesen']) }}" class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-rose-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-stamp"></i>
                    </div>
                    <span class="text-xs font-bold text-rose-600 uppercase">Kelulusan Lesen</span>
                </div>
                <div>
                    <div class="text-3xl font-black {{ $totalEpuPendingPelesen > 0 ? 'text-rose-600' : 'text-slate-900' }}">{{ $totalEpuPendingPelesen }}</div>
                    <div class="text-sm font-semibold text-slate-700">Menunggu Kelulusan Pelesen</div>
                    <div class="text-xs text-rose-600 font-semibold mt-1">Tindakan Pengarah / HQ &rarr;</div>
                </div>
            </a>

            <!-- 4. Lesen Diluluskan & Fi -->
            <a href="{{ route('epu.index', ['status' => 'Diluluskan']) }}" class="p-5 rounded-3xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-emerald-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-certificate"></i>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 uppercase">Lesen Sah</span>
                </div>
                <div>
                    <div class="text-3xl font-black text-emerald-700">{{ $totalEpuLicenses }}</div>
                    <div class="text-sm font-semibold text-slate-700">Lesen Borang B Diluluskan</div>
                    <div class="text-xs text-slate-500 mt-1">
                        <span class="font-bold text-amber-600">{{ $totalEpuPendingBayaran }} Menunggu Fi</span>
                    </div>
                </div>
            </a>
        </div>

        <!-- EPU Main Content Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Senarai Permohonan EPU Terkini -->
            <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200 p-6 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-feather"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Senarai Permohonan Lesen EPU Terkini</h3>
                            <p class="text-[11px] text-slate-500">Aliran kerja semakan PPVJ Jajahan, kelulusan Pegawai Pelesen &amp; bayaran fi</p>
                        </div>
                    </div>
                    <a href="{{ route('epu.index') }}" class="text-xs text-amber-600 hover:text-amber-800 font-bold">
                        Lihat Semua Permohonan &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 uppercase font-semibold">
                                <th class="py-2.5">No. Rujukan</th>
                                <th class="py-2.5">Pemohon &amp; Ladang</th>
                                <th class="py-2.5">Jenis Ternakan</th>
                                <th class="py-2.5">Jajahan</th>
                                <th class="py-2.5">Status Aliran</th>
                                <th class="py-2.5 text-right">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($recentEpu as $epu)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3 font-mono font-bold text-amber-900">
                                        {{ $epu->no_rujukan }}
                                        <div class="text-[10px] text-slate-400 font-normal">{{ $epu->created_at ? $epu->created_at->format('d/m/Y') : '-' }}</div>
                                    </td>
                                    <td class="py-3">
                                        <div class="font-bold text-slate-900">{{ $epu->ladang->pemilik->name ?? 'Pemohon' }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $epu->ladang->nama_ladang ?? '-' }}</div>
                                    </td>
                                    <td class="py-3">
                                        <div class="font-semibold text-slate-800">{{ $epu->ladang->kategori_unggas ?? 'Unggas' }}</div>
                                        <div class="text-[11px] text-slate-500">{{ number_format($epu->ladang->kapasiti_ternakan ?? 0) }} Ekor</div>
                                    </td>
                                    <td class="py-3 text-slate-600 font-medium">
                                        {{ $epu->ladang->jajahan ?? '-' }}
                                    </td>
                                    <td class="py-3">
                                        @if($epu->status === 'Diluluskan')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                <i class="fa-solid fa-circle-check mr-1"></i> Diluluskan
                                            </span>
                                        @elseif($epu->status === 'Menunggu Kelulusan Pelesen')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                                <i class="fa-solid fa-stamp mr-1"></i> Kelulusan Pelesen
                                            </span>
                                        @elseif($epu->status === 'Ditolak')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-800">
                                                <i class="fa-solid fa-circle-xmark mr-1"></i> Ditolak
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                                <i class="fa-solid fa-clock mr-1"></i> {{ $epu->status }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-right space-x-1">
                                        <a href="{{ route('epu.show', $epu->id) }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-amber-100 text-slate-600 hover:text-amber-700 transition" title="Buka Butiran Permohonan">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        @if(Auth::user()->canCetakBorangEpu())
                                            <a href="{{ route('epu.cetak-borang-a', $epu->id) }}" target="_blank" class="p-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 transition" title="Cetak Borang A">
                                                <i class="fa-solid fa-print"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-400">
                                        Tiada rekod permohonan lesen EPU dijumpai.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right: Tindakan Pantas & Panduan Enakmen Unggas -->
            <div class="space-y-6">
                <!-- Box 1: Tindakan Pantas Mengikut Peranan -->
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs">
                    <div class="flex items-center gap-2.5 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Tindakan Pantas EPU</h3>
                    </div>
                    <div class="space-y-2.5">
                        @if($user->role === 'pegawai_verifikasi_epu' || $user->role === 'admin_epu' || $user->isSuperAdmin())
                        <a href="{{ route('epu.index', ['status' => 'Menunggu Semakan Dokumen']) }}" class="w-full flex items-center justify-between p-3.5 rounded-2xl bg-indigo-50/80 hover:bg-indigo-100/80 border border-indigo-200/80 text-indigo-900 transition group">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-clipboard-check text-indigo-700"></i>
                                <span class="text-xs font-bold">Verifikasi Dokumen / Tapak ({{ $totalEpuPendingVerifikasi }})</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-xs text-indigo-600 group-hover:translate-x-0.5 transition-transform"></i>
                        </a>
                        @endif

                        @if($user->role === 'pegawai_pelesen' || $user->isSuperAdmin())
                        <a href="{{ route('epu.index', ['status' => 'Menunggu Kelulusan Pelesen']) }}" class="w-full flex items-center justify-between p-3.5 rounded-2xl bg-rose-50/80 hover:bg-rose-100/80 border border-rose-200/80 text-rose-900 transition group">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-stamp text-rose-700"></i>
                                <span class="text-xs font-bold">Kelulusan Pelesen ({{ $totalEpuPendingPelesen }})</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-xs text-rose-600 group-hover:translate-x-0.5 transition-transform"></i>
                        </a>
                        @endif

                        <a href="{{ route('epu.index') }}" class="w-full flex items-center justify-between p-3.5 rounded-2xl bg-amber-50/80 hover:bg-amber-100/80 border border-amber-200/80 text-amber-900 transition group">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-warehouse text-amber-700"></i>
                                <span class="text-xs font-bold">Pengurusan Ladang Unggas</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-xs text-amber-600 group-hover:translate-x-0.5 transition-transform"></i>
                        </a>

                        @if($user->role === 'admin_epu' || $user->isSuperAdmin())
                        <a href="{{ route('epu.index', ['status' => 'Diluluskan']) }}" class="w-full flex items-center justify-between p-3.5 rounded-2xl bg-emerald-50/80 hover:bg-emerald-100/80 border border-emerald-200/80 text-emerald-900 transition group">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-receipt text-emerald-700"></i>
                                <span class="text-xs font-bold">Semakan Bayaran Fi ({{ $totalEpuPendingBayaran }})</span>
                            </div>
                            <i class="fa-solid fa-chevron-right text-xs text-emerald-600 group-hover:translate-x-0.5 transition-transform"></i>
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Box 2: Panduan & Pengasingan Kuasa Enakmen Unggas -->
                <div class="bg-gradient-to-br from-slate-900 to-amber-950 rounded-3xl p-6 text-white shadow-xs">
                    <div class="flex items-center gap-2.5 mb-3">
                        <i class="fa-solid fa-shield-halved text-amber-400"></i>
                        <h4 class="text-sm font-bold text-white">Enakmen Penternakan Unggas 2007</h4>
                    </div>
                    <ul class="text-xs text-slate-300 space-y-2 leading-relaxed list-disc list-inside">
                        <li><b class="text-amber-300">Pegawai Verifikasi Jajahan (PPVJ)</b>: Verifikasi kelengkapan dokumen permohonan &amp; pemeriksaan tapak di peringkat jajahan.</li>
                        <li><b class="text-rose-300">Pegawai Pelesen / Pengarah DVS</b>: Semakan penilaian tapak, kelulusan Lesen Borang B &amp; rayuan HQ.</li>
                        <li><b class="text-emerald-300">Admin EPU Negeri</b>: Pengesahan resit fi pelesenan, kawalan modul &amp; cetakan borang rasmi.</li>
                        <li><b class="text-white">Kawalan Cetakan</b>: Borang A dan Lesen Borang B hanya boleh dicetak oleh Pegawai Verifikasi dan Admin Negeri.</li>
                    </ul>
                </div>
            </div>
        </div>
    @else
        <!-- 7 Services KPI Cards Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-3.5">
            
            @if(Auth::user()->canAccessEptr())
            <!-- 1. EPTR Ruminan -->
            <a href="{{ route('eptr.index') }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-emerald-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-cow"></i>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase">EPTR</span>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900">{{ $totalTernakanEptr }}</div>
                    <div class="text-xs font-semibold text-slate-600 truncate">{{ $user->isStaff() ? 'Ternakan Ruminan' : 'Ternakan Saya (EPTR)' }}</div>
                    <div class="text-[10px] text-emerald-600 font-medium mt-0.5">
                        @if($user->isStaff())
                            {{ $totalTernakanPawah }} di bawah program
                        @else
                            {{ $totalPendingTernakan }} Pendaftaran Menunggu
                        @endif
                    </div>
                </div>
            </a>
            @endif

            @if(Auth::user()->canAccessPawah())
            <!-- 2. Program Pawah -->
            <a href="{{ route('pawah.index') }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-emerald-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-handshake-angle"></i>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Pawah</span>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900">{{ $totalPawahActive }}</div>
                    <div class="text-xs font-semibold text-slate-600 truncate">{{ $user->isStaff() ? 'Perjanjian Pawah' : 'Pawah Saya' }}</div>
                    <div class="text-[10px] text-emerald-600 font-medium mt-0.5">
                        @if($user->isStaff())
                            Berhubung EPTR
                        @else
                            {{ $totalPawahMenunggu }} Permohonan Menunggu
                        @endif
                    </div>
                </div>
            </a>
            @endif

            @if(Auth::user()->canAccessEpu())
            <!-- 3. EPU Unggas -->
            <a href="{{ route('epu.index') }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-emerald-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-feather-pointed"></i>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase">EPU</span>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900">{{ $totalEpuFarms }}</div>
                    <div class="text-xs font-semibold text-slate-600 truncate">{{ $user->isStaff() ? 'Ladang Unggas' : 'Ladang Unggas Saya' }}</div>
                    <div class="text-[10px] text-emerald-600 font-medium mt-0.5">
                        @if($user->isStaff())
                            {{ $totalEpuLicenses }} Lesen Aktif
                        @else
                            {{ $totalEpuLicenses }} Lesen Diluluskan
                        @endif
                    </div>
                </div>
            </a>
            @endif

            <!-- 4. Kursus Ternakan -->
            <a href="{{ route('kursus.index') }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-emerald-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Kursus</span>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900">{{ $totalCourses }}</div>
                    <div class="text-xs font-semibold text-slate-600 truncate">{{ $user->isStaff() ? 'Kursus Dibuka' : 'Permohonan Kursus Saya' }}</div>
                    <div class="text-[10px] text-cyan-600 font-medium mt-0.5">
                        @if($user->isStaff())
                            Sijil Digital PDF
                        @else
                            {{ $totalCourseApplications }} Permohonan Diluluskan
                        @endif
                    </div>
                </div>
            </a>

            @if(Auth::user()->canAccessKlinik())
            <!-- 5. Klinik Haiwan -->
            <a href="{{ route('klinik.index') }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-emerald-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-stethoscope"></i>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Klinik</span>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900">{{ $totalClinicAppointments }}</div>
                    <div class="text-xs font-semibold text-slate-600 truncate">{{ $user->isStaff() ? 'Temujanji Rawatan' : 'Temujanji Rawatan Saya' }}</div>
                    <div class="text-[10px] text-rose-600 font-medium mt-0.5">
                        @if($user->isStaff())
                            10 Jajahan Klinik
                        @else
                            Rekod Temujanji
                        @endif
                    </div>
                </div>
            </a>
            @endif

            @if(Auth::user()->canAccessInventori())
            <!-- 6. Inventori Pejabat -->
            <a href="{{ route('inventori.index') }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-emerald-400 transition group flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Inventori</span>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900">{{ $totalInventoryItems }}</div>
                    <div class="text-xs font-semibold text-slate-600 truncate">Item & Stok Ubat</div>
                    <div class="text-[10px] {{ $lowStockInventoryCount > 0 ? 'text-amber-600 font-bold' : 'text-slate-500' }} mt-0.5">
                        {{ $lowStockInventoryCount }} Stok Rendah
                    </div>
                </div>
            </a>
            @endif

            @if(Auth::user()->canAccessKenderaan())
            <!-- 7. Permohonan Kenderaan -->
            <a href="{{ route('kenderaan.index') }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md hover:border-emerald-400 transition group flex flex-col justify-between col-span-2 sm:col-span-1">
                <div class="flex items-center justify-between mb-2">
                    <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-truck-pickup"></i>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Kenderaan</span>
                </div>
                <div>
                    <div class="text-2xl font-black text-slate-900">{{ $availableVehicles }} / {{ $totalVehicles }}</div>
                    <div class="text-xs font-semibold text-slate-600 truncate">Kenderaan Sedia</div>
                    <div class="text-[10px] {{ $pendingVehicleBookings > 0 ? 'text-amber-600 font-bold' : 'text-slate-500' }} mt-0.5">
                        {{ $pendingVehicleBookings }} Tempahan Menunggu
                    </div>
                </div>
            </a>
            @endif
        </div>

        @if(in_array($user->role, ['super_admin', 'admin_eptr']))
        <!-- Charts & Analytics Section (Admin Ibu Pejabat / Admin EPTR Negeri Sahaja) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Chart 1: EPTR & Pawah Distribution -->
            <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Taburan Ternakan EPTR & Program Pawah Mengikut Jajahan</h3>
                        <p class="text-xs text-slate-500">Statistik masa nyata pendaftaran ruminan Negeri Kelantan 2026</p>
                    </div>
                    <span class="text-xs bg-emerald-50 text-emerald-700 font-bold px-3 py-1 rounded-full border border-emerald-200">
                        Live Data
                    </span>
                </div>
                <div id="eptrChart" class="h-64"></div>
            </div>

            <!-- Chart 2: Modul Engagement Pie -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs flex flex-col justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900 mb-1">Pecahan Modul Perkhidmatan</h3>
                    <p class="text-xs text-slate-500 mb-4">Aktiviti penggunaan pangkalan data sepunya</p>
                    <div id="servicePieChart" class="h-56"></div>
                </div>
                <div class="pt-4 border-t border-slate-100 text-xs text-slate-500 flex justify-between">
                    <span>Pengguna Berdaftar: <b>{{ $totalUsers }}</b></span>
                    <span class="text-emerald-600 font-semibold">10 Peranan Aktif</span>
                </div>
            </div>
        </div>
        @endif

        @if($user->isStaff())
        <!-- Dynamic Section Based On Role (Staff) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Left Column: EPTR or EPU Activity -->
            @if(Auth::user()->canAccessEptr())
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-cow"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">
                            Ternakan EPTR Terkini Didaftarkan
                        </h3>
                    </div>
                    <a href="{{ route('eptr.index') }}" class="text-xs text-emerald-600 hover:text-emerald-800 font-bold">
                        Lihat Semua &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 uppercase font-semibold">
                                <th class="py-2.5">No Tag</th>
                                <th class="py-2.5">Baka & Jantina</th>
                                <th class="py-2.5">Program</th>
                                <th class="py-2.5">Status</th>
                                <th class="py-2.5 text-right">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($recentTernakan as $t)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3 font-mono font-bold text-emerald-800">
                                        {{ $t->no_tag }}
                                    </td>
                                    <td class="py-3">
                                        <div class="font-semibold text-slate-900">{{ $t->baka }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $t->jenis_ternakan }} • {{ $t->jantina }}</div>
                                    </td>
                                    <td class="py-3">
                                        @if($t->program && $t->program !== 'Tiada')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                <i class="fa-solid fa-handshake mr-1"></i> {{ Str::limit($t->program, 18) }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-[11px]">Tiada</span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $t->status === 'Aktif' ? 'bg-emerald-100 text-emerald-800' : ($t->status === 'Pawah' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-800') }}">
                                            {{ $t->status }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-right">
                                        <a href="{{ route('eptr.show', $t->id) }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-emerald-100 text-slate-600 hover:text-emerald-700 transition" title="Buka Kad Kuning Borang B">
                                            <i class="fa-solid fa-id-card"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-400">
                                        Tiada rekod ternakan dijumpai.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @elseif(Auth::user()->canAccessEpu())
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-feather-pointed"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">
                            Permohonan Lesen Unggas EPU Terkini
                        </h3>
                    </div>
                    <a href="{{ route('epu.index') }}" class="text-xs text-amber-600 hover:text-amber-800 font-bold">
                        Lihat Semua &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 uppercase font-semibold">
                                <th class="py-2.5">Nama Ladang & Pemohon</th>
                                <th class="py-2.5">Kategori / Kapasiti</th>
                                <th class="py-2.5">Jajahan</th>
                                <th class="py-2.5">Status Lesen</th>
                                <th class="py-2.5 text-right">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($recentEpu as $epu)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3">
                                        <div class="font-bold text-slate-900">{{ $epu->ladang->nama_ladang ?? 'Ladang Unggas' }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $epu->ladang->nama_pemohon_atau_syarikat ?? '-' }}</div>
                                    </td>
                                    <td class="py-3">
                                        <div class="font-semibold text-slate-700">{{ $epu->ladang->kategori_unggas ?? 'Ayam Daging' }}</div>
                                        <div class="text-[11px] text-slate-500">{{ number_format($epu->ladang->kapasiti_ternakan ?? 0) }} Ekor</div>
                                    </td>
                                    <td class="py-3 text-slate-600 font-medium">{{ $epu->ladang->jajahan ?? '-' }}</td>
                                    <td class="py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $epu->status === 'Diluluskan' ? 'bg-emerald-100 text-emerald-800' : ($epu->status === 'Menunggu' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-800') }}">
                                            {{ $epu->status }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-right">
                                        <a href="{{ route('epu.show', $epu->ladang_id ?? 1) }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-amber-100 text-slate-600 hover:text-amber-700 transition" title="Buka Maklumat Ladang">
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-400">
                                        Tiada rekod permohonan lesen unggas dijumpai.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Right Column: Pawah Activity (Staff) -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-handshake-angle"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">
                            Surat Perjanjian Lembu Pawah Terkini
                        </h3>
                    </div>
                    <a href="{{ route('pawah.index') }}" class="text-xs text-emerald-600 hover:text-emerald-800 font-bold">
                        Lihat Semua &rarr;
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($recentPawah as $p)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 hover:bg-emerald-50/30 transition flex items-center justify-between">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs font-bold text-slate-900">{{ $p->no_perjanjian }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">{{ $p->status }}</span>
                                </div>
                                <div class="text-xs font-medium text-slate-700 mt-1">{{ $p->nama_program }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    Peserta: <span class="font-semibold text-slate-800">{{ $p->peserta->name ?? 'N/A' }}</span> &bull; {{ $p->bilangan_induk }} Ekor Induk EPTR
                                </div>
                            </div>
                            <a href="{{ route('pawah.show', $p->id) }}" class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:border-emerald-500 text-xs font-bold text-slate-700 hover:text-emerald-700 shadow-2xs transition">
                                Butiran &rarr;
                            </a>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400 text-xs">
                            Tiada surat perjanjian pawah aktif.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
        @else
        <!-- User Personal Dashboard Sections (Milik Pengguna Sahaja) -->
        <div class="space-y-6">
            
            <!-- Grid 1: EPTR & Pawah Saya -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- 1. Ternakan Ruminan Saya (EPTR) -->
                @if(Auth::user()->canAccessEptr())
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-base">
                                <i class="fa-solid fa-cow"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Ternakan Ruminan Saya (EPTR)</h3>
                                <p class="text-[11px] text-slate-500">Pendaftaran & rekod ternakan ruminan milik anda</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('eptr.create') }}" class="px-2.5 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-bold transition">
                                <i class="fa-solid fa-plus mr-1"></i> Daftar
                            </a>
                            <a href="{{ route('eptr.index') }}" class="text-xs text-slate-500 hover:text-slate-800 font-bold">
                                Semua &rarr;
                            </a>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-400 uppercase font-semibold">
                                    <th class="py-2.5">No Tag</th>
                                    <th class="py-2.5">Baka & Jantina</th>
                                    <th class="py-2.5">Program</th>
                                    <th class="py-2.5">Status</th>
                                    <th class="py-2.5 text-right">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($myTernakan as $t)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-3 font-mono font-bold text-emerald-800">
                                            {{ $t->no_tag }}
                                        </td>
                                        <td class="py-3">
                                            <div class="font-semibold text-slate-900">{{ $t->baka }}</div>
                                            <div class="text-[11px] text-slate-500">{{ $t->jenis_ternakan }} • {{ $t->jantina }}</div>
                                        </td>
                                        <td class="py-3">
                                            @if($t->program && $t->program !== 'Tiada')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                    <i class="fa-solid fa-handshake mr-1"></i> {{ Str::limit($t->program, 18) }}
                                                </span>
                                            @else
                                                <span class="text-slate-400 text-[11px]">Tiada</span>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $t->status === 'Aktif' ? 'bg-emerald-100 text-emerald-800' : ($t->status === 'Pawah' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-800') }}">
                                                {{ $t->status }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-right">
                                            <a href="{{ route('eptr.show', $t->id) }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-emerald-100 text-slate-600 hover:text-emerald-700 transition" title="Buka Kad Kuning Borang B">
                                                <i class="fa-solid fa-id-card"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center">
                                            <div class="text-slate-400 text-xs mb-2">Tiada rekod ternakan berdaftar untuk akaun anda.</div>
                                            <a href="{{ route('eptr.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-500 transition">
                                                <i class="fa-solid fa-plus"></i> Daftar Ternakan Baharu (Borang A)
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- 2. Perjanjian Pawah Saya -->
                @if(Auth::user()->canAccessPawah())
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-base">
                                <i class="fa-solid fa-handshake-angle"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Perjanjian Program Pawah Saya</h3>
                                <p class="text-[11px] text-slate-500">Skim pawah dan ternakan pawah di bawah seliaan anda</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('pawah.create') }}" class="px-2.5 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-bold transition">
                                <i class="fa-solid fa-plus mr-1"></i> Mohon
                            </a>
                            <a href="{{ route('pawah.index') }}" class="text-xs text-slate-500 hover:text-slate-800 font-bold">
                                Semua &rarr;
                            </a>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @forelse($myPawah as $p)
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 hover:bg-emerald-50/30 transition flex items-center justify-between">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-xs font-bold text-slate-900">{{ $p->no_perjanjian }}</span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $p->status === 'Aktif' ? 'bg-emerald-100 text-emerald-800' : ($p->status === 'Menunggu Kelulusan' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-800') }}">{{ $p->status }}</span>
                                    </div>
                                    <div class="text-xs font-medium text-slate-700 mt-1">{{ $p->nama_program }}</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        {{ $p->bilangan_induk }} Ekor Induk &bull; {{ $p->ternakanList->count() }} Ternakan Terpaut
                                    </div>
                                </div>
                                <a href="{{ route('pawah.show', $p->id) }}" class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:border-emerald-500 text-xs font-bold text-slate-700 hover:text-emerald-700 shadow-2xs transition">
                                    Butiran &rarr;
                                </a>
                            </div>
                        @empty
                            <div class="py-8 text-center">
                                <div class="text-slate-400 text-xs mb-2">Tiada permohonan atau perjanjian program pawah aktif.</div>
                                <a href="{{ route('pawah.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-500 transition">
                                    <i class="fa-solid fa-plus"></i> Mohon Skim Bantuan Pawah
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
                @endif
            </div>

            <!-- Grid 2: EPU, Kursus, Klinik Saya -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- 3. Ladang Unggas Saya (EPU) -->
                @if(Auth::user()->canAccessEpu())
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center font-bold text-base">
                                    <i class="fa-solid fa-feather-pointed"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-900">Ladang Unggas Saya (EPU)</h3>
                                    <p class="text-[11px] text-slate-500">Pendaftaran ladang & lesen unggas</p>
                                </div>
                            </div>
                            <a href="{{ route('epu.index') }}" class="text-xs text-slate-500 hover:text-slate-800 font-bold">
                                Semua &rarr;
                            </a>
                        </div>

                        <div class="space-y-3">
                            @forelse($myFarms as $farm)
                                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 hover:bg-amber-50/30 transition flex items-center justify-between">
                                    <div>
                                        <div class="font-bold text-xs text-slate-900">{{ $farm->nama_ladang }}</div>
                                        <div class="text-[11px] text-slate-500 mt-0.5">{{ $farm->jajahan }} &bull; Kapasiti: {{ number_format($farm->kapasiti_maksimum_unggas ?? 0) }} Ekor</div>
                                        <div class="mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $farm->status_ladang === 'Aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-800' }}">
                                                {{ $farm->status_ladang ?? 'Aktif' }}
                                            </span>
                                        </div>
                                    </div>
                                    <a href="{{ route('epu.show', $farm->id) }}" class="px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-amber-500 text-xs font-bold text-slate-700 hover:text-amber-700 transition">
                                        Lihat
                                    </a>
                                </div>
                            @empty
                                <div class="py-8 text-center">
                                    <div class="text-slate-400 text-xs mb-2">Tiada ladang unggas didaftarkan.</div>
                                    <a href="{{ route('epu.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-600 text-white text-xs font-bold hover:bg-amber-500 transition">
                                        <i class="fa-solid fa-plus"></i> Daftar Ladang (Borang A)
                                    </a>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                @endif

                <!-- 4. Permohonan Kursus Ternakan Saya -->
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-cyan-100 text-cyan-600 flex items-center justify-center font-bold text-base">
                                    <i class="fa-solid fa-graduation-cap"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-900">Kursus Ternakan Saya</h3>
                                    <p class="text-[11px] text-slate-500">Permohonan kursus & sijil digital</p>
                                </div>
                            </div>
                            <a href="{{ route('kursus.index') }}" class="text-xs text-slate-500 hover:text-slate-800 font-bold">
                                Semua &rarr;
                            </a>
                        </div>

                        <div class="space-y-3">
                            @forelse($myCourses as $mc)
                                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 hover:bg-cyan-50/30 transition flex items-center justify-between">
                                    <div>
                                        <div class="font-bold text-xs text-slate-900">{{ Str::limit($mc->course->title ?? 'Kursus Veterinar', 24) }}</div>
                                        <div class="text-[11px] text-slate-500 mt-0.5">
                                            @if($mc->course && $mc->course->start_date)
                                                {{ \Carbon\Carbon::parse($mc->course->start_date)->format('d/m/Y') }}
                                            @endif
                                        </div>
                                        <div class="mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $mc->status === 'Diluluskan' ? 'bg-emerald-100 text-emerald-800' : ($mc->status === 'Menunggu' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-800') }}">
                                                {{ $mc->status }}
                                            </span>
                                        </div>
                                    </div>
                                    @if($mc->course_id)
                                    <a href="{{ route('kursus.show', $mc->course_id) }}" class="px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-cyan-500 text-xs font-bold text-slate-700 hover:text-cyan-700 transition">
                                        Lihat
                                    </a>
                                    @endif
                                </div>
                            @empty
                                <div class="py-8 text-center">
                                    <div class="text-slate-400 text-xs mb-2">Tiada permohonan kursus buat masa ini.</div>
                                    <a href="{{ route('kursus.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-cyan-600 text-white text-xs font-bold hover:bg-cyan-500 transition">
                                        <i class="fa-solid fa-search"></i> Terokai Kursus Dibuka
                                    </a>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- 5. Temujanji Klinik Haiwan Saya -->
                @if(Auth::user()->canAccessKlinik())
                <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-base">
                                    <i class="fa-solid fa-stethoscope"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-900">Klinik Haiwan Saya</h3>
                                    <p class="text-[11px] text-slate-500">Temujanji & rawatan kesihatan</p>
                                </div>
                            </div>
                            <a href="{{ route('klinik.index') }}" class="text-xs text-slate-500 hover:text-slate-800 font-bold">
                                Semua &rarr;
                            </a>
                        </div>

                        <div class="space-y-3">
                            @forelse($myClinicAppointments as $apt)
                                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 hover:bg-rose-50/30 transition flex items-center justify-between">
                                    <div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-mono font-bold text-xs text-slate-900">{{ $apt->no_temujanji }}</span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $apt->status === 'Selesai' ? 'bg-emerald-100 text-emerald-800' : ($apt->status === 'Disahkan' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">{{ $apt->status }}</span>
                                        </div>
                                        <div class="text-[11px] text-slate-700 font-medium mt-0.5">{{ $apt->jenis_haiwan }} ({{ $apt->nama_haiwan ?? '-' }})</div>
                                        <div class="text-[10px] text-slate-500">
                                            {{ $apt->tarikh_temujanji ? $apt->tarikh_temujanji->format('d/m/Y') : '-' }} &bull; {{ $apt->klinik_jajahan }}
                                        </div>
                                    </div>
                                    <a href="{{ route('klinik.show', $apt->id) }}" class="px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 hover:border-rose-500 text-xs font-bold text-slate-700 hover:text-rose-700 transition">
                                        Lihat
                                    </a>
                                </div>
                            @empty
                                <div class="py-8 text-center">
                                    <div class="text-slate-400 text-xs mb-2">Tiada rekod temujanji rawatan.</div>
                                    <a href="{{ route('klinik.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-600 text-white text-xs font-bold hover:bg-rose-500 transition">
                                        <i class="fa-solid fa-calendar-plus"></i> Tempah Temujanji Rawatan
                                    </a>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                @endif

            </div>

        </div>
        @endif
    @endif

</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // EPTR & Pawah Jajahan Bar Chart
        var optionsEptr = {
            chart: {
                type: 'bar',
                height: 250,
                toolbar: { show: false },
                fontFamily: 'Plus Jakarta Sans, sans-serif',
            },
            series: [{
                name: 'Ternakan EPTR',
                data: {!! json_encode($chartEptrData ?? []) !!}
            }, {
                name: 'Lembu Pawah',
                data: {!! json_encode($chartPawahData ?? []) !!}
            }],
            xaxis: {
                categories: {!! json_encode($jajahanList ?? ['Kota Bharu', 'Pasir Mas', 'Tumpat', 'Bachok', 'Pasir Puteh', 'Machang', 'Tanah Merah', 'Jeli', 'Kuala Krai', 'Gua Musang']) !!},
                labels: { style: { fontSize: '10px' } }
            },
            yaxis: {
                labels: {
                    formatter: function(val) {
                        return Math.floor(val);
                    }
                }
            },
            colors: ['#10b981', '#f59e0b'],
            plotOptions: {
                bar: {
                    horizontal: false,
                    borderRadius: 4,
                    columnWidth: '55%',
                }
            },
            legend: { position: 'top', fontSize: '11px' },
            dataLabels: { enabled: false },
            grid: { borderColor: '#f1f5f9' },
            noData: {
                text: 'Tiada rekod ternakan ditemui',
                align: 'center',
                verticalAlign: 'middle',
                style: {
                    color: '#94a3b8',
                    fontSize: '12px'
                }
            }
        };

        document.querySelectorAll("#eptrChart").forEach(function (el) {
            var chartEptr = new ApexCharts(el, optionsEptr);
            chartEptr.render();
        });

        // Service Distribution Donut Chart
        var totalServiceEngagement = {{ (int) ($totalTernakanEptr + $totalTernakanPawah + $totalEpuFarms + $totalCourses + $totalClinicAppointments) }};
        var optionsPie = {
            chart: {
                type: 'donut',
                height: 230,
                fontFamily: 'Plus Jakarta Sans, sans-serif',
            },
            series: totalServiceEngagement > 0 
                ? [{{ (int)$totalTernakanEptr }}, {{ (int)$totalTernakanPawah }}, {{ (int)$totalEpuFarms }}, {{ (int)$totalCourses }}, {{ (int)$totalClinicAppointments }}] 
                : [],
            labels: ['EPTR Ruminan', 'Program Pawah', 'EPU Unggas', 'Kursus Ternakan', 'Klinik Haiwan'],
            colors: ['#10b981', '#f59e0b', '#8b5cf6', '#06b6d4', '#f43f5e'],
            legend: { position: 'bottom', fontSize: '10px' },
            dataLabels: { enabled: false },
            noData: {
                text: 'Tiada data perkhidmatan aktif',
                align: 'center',
                verticalAlign: 'middle',
                style: {
                    color: '#94a3b8',
                    fontSize: '11px'
                }
            },
            responsive: [{
                breakpoint: 480,
                options: { chart: { width: 200 }, legend: { position: 'bottom' } }
            }]
        };

        document.querySelectorAll("#servicePieChart").forEach(function (el) {
            var chartPie = new ApexCharts(el, optionsPie);
            chartPie.render();
        });
    });
</script>
@endpush
@endsection
