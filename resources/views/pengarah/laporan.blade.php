@extends('layouts.app')

@section('title', 'Laporan & Analitik Eksekutif - JPVNK')
@section('page_title', 'Laporan & Analitik Eksekutif Pengarah')

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'eptr' }">

    <!-- Header & Executive Summary Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 p-6 sm:p-8 text-white shadow-xl">
        <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
            <div class="max-w-3xl">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-emerald-400 font-extrabold text-xl shadow-inner">
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-0.5 rounded-full bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs font-semibold">
                            <i class="fa-solid fa-shield-halved"></i>
                            <span>Pejabat Pengarah &amp; Pengurusan Eksekutif</span>
                        </div>
                        <div class="text-xs text-slate-300 mt-0.5">Jabatan Perkhidmatan Veterinar Negeri Kelantan</div>
                    </div>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                    Laporan Statistik &amp; Analitik Bersepadu
                </h1>
                <p class="mt-2 text-sm text-slate-300 leading-relaxed">
                    Ringkasan data masa nyata merangkumi 9 modul utama: Pendaftaran Ruminan (EPTR), Skim Bantuan Pawah, Pelesenan Ladang Unggas (EPU), Program Ladang Bridlot Pedaging NAIMbif, Temujanji &amp; Rawatan Klinik, Kursus Penternakan, Stor &amp; Farmasi, Armada Kenderaan serta Taburan GIS 10 Jajahan Negeri Kelantan.
                </p>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex flex-wrap lg:flex-col gap-2.5 w-full lg:w-auto shrink-0 print:hidden">
                <button onclick="window.print()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 text-xs font-bold shadow-lg transition hover:scale-102">
                    <i class="fa-solid fa-print text-emerald-400"></i>
                    <span>Cetak Laporan / PDF</span>
                </button>
                <a href="{{ route('peta.taburan') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-lg shadow-emerald-900/40 transition hover:scale-102">
                    <i class="fa-solid fa-map-location-dot"></i>
                    <span>Peta Taburan (GIS)</span>
                </a>
                <a href="{{ route('epu.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-black shadow-lg shadow-amber-900/40 transition hover:scale-102">
                    <i class="fa-solid fa-feather-pointed"></i>
                    <span>Portal Pelesenan EPU</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Alert / Tindakan Segera Pengarah (EPU Pending Approval & Rayuan) -->
    @if($totalEpuPendingPelesen > 0 || $totalEpuRayuan > 0)
    <div class="rounded-2xl bg-amber-500/10 border-2 border-amber-500/30 p-5 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-600 flex items-center justify-center text-lg shrink-0 mt-0.5">
                    <i class="fa-solid fa-bell-exclamation"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-amber-900">Perhatian Pengarah: Permohonan Lesen EPU Menunggu Kelulusan &amp; Rayuan</h3>
                    <p class="text-xs text-amber-800/90 mt-0.5">
                        Terdapat <strong class="underline">{{ $totalEpuPendingPelesen }} permohonan lesen</strong> yang telah selesai verifikasi tapak dan sedia untuk ditandatangani/diluluskan, serta <strong class="underline">{{ $totalEpuRayuan }} permohonan rayuan</strong> menunggu keputusan pelesenan.
                    </p>
                </div>
            </div>
            <a href="#senarai-tindakan-pengarah" class="shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold shadow-md transition">
                <span>Semak Permohonan</span>
                <i class="fa-solid fa-arrow-down"></i>
            </a>
        </div>
    </div>
    @endif

    <!-- 8 Executive KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- KPI 1: EPTR Ruminan -->
        <div class="rounded-2xl bg-white p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">1. EPTR Ruminan</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-base">
                    <i class="fa-solid fa-cow"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-slate-900">{{ number_format($totalTernakanEptr) }}</div>
                <div class="text-xs text-slate-500 mt-1 flex items-center justify-between">
                    <span>Ber-Tag: <strong class="text-emerald-600">{{ number_format($totalTernakanTagged) }}</strong></span>
                    <span>Aktif: <strong class="text-slate-700">{{ number_format($totalTernakanAktif) }}</strong></span>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Permit Sembelih: <strong>{{ $totalPermitSembelihan }}</strong></span>
                <span>Kesihatan: <strong>{{ $totalProgramKesihatan }}</strong></span>
            </div>
        </div>

        <!-- KPI 2: EPU Unggas -->
        <div class="rounded-2xl bg-white p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">2. EPU Unggas</span>
                <div class="w-9 h-9 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center text-base">
                    <i class="fa-solid fa-feather-pointed"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-slate-900">{{ number_format($totalEpuSemasaUnggas) }} <span class="text-xs font-normal text-slate-500">ekor</span></div>
                <div class="text-xs text-slate-500 mt-1 flex items-center justify-between">
                    <span>Ladang: <strong class="text-slate-700">{{ $totalEpuLadang }}</strong></span>
                    <span>Lesen Lulus: <strong class="text-emerald-600">{{ $totalEpuLulus }}</strong></span>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Kapasiti: <strong>{{ number_format($totalEpuKapasiti) }}</strong></span>
                <span>Kutipan: <strong class="text-emerald-600">RM {{ number_format($totalEpuFiKutipan, 2) }}</strong></span>
            </div>
        </div>

        <!-- KPI 3: Program NAIMbif -->
        <div class="rounded-2xl bg-white p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">3. NAIMbif Bridlot</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base">
                    <i class="fa-solid fa-wheat-awn"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-slate-900">{{ number_format($totalNaimbifPopulasi) }} <span class="text-xs font-normal text-slate-500">ekor</span></div>
                <div class="text-xs text-slate-500 mt-1 flex items-center justify-between">
                    <span>Permohonan: <strong class="text-slate-700">{{ $totalNaimbifApps }}</strong></span>
                    <span>Lulus Negeri: <strong class="text-emerald-600">{{ $totalNaimbifLulus }}</strong></span>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Siasatan Jajahan: <strong>{{ $totalNaimbifMenungguJajahan }}</strong></span>
                <span>Menunggu HQ: <strong>{{ $totalNaimbifMenungguNegeri }}</strong></span>
            </div>
        </div>

        <!-- KPI 4: Skim Bantuan Pawah -->
        <div class="rounded-2xl bg-white p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">4. Program Pawah</span>
                <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-base">
                    <i class="fa-solid fa-handshake-angle"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-slate-900">{{ number_format($totalPawahAktif) }} <span class="text-xs font-normal text-slate-500">perjanjian</span></div>
                <div class="text-xs text-slate-500 mt-1 flex items-center justify-between">
                    <span>Induk Diagih: <strong class="text-slate-700">{{ $totalPawahInduk }}</strong></span>
                    <span>Kelahiran: <strong class="text-emerald-600">{{ $totalPawahKelahiran }}</strong></span>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Anak Hidup: <strong>{{ $totalPawahKelahiranHidup }}</strong></span>
                <span>Selesai: <strong>{{ $totalPawahSelesai }}</strong></span>
            </div>
        </div>

        <!-- KPI 5: Klinik Haiwan -->
        <div class="rounded-2xl bg-white p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">5. Klinik Veterinar</span>
                <div class="w-9 h-9 rounded-xl bg-pink-50 text-pink-600 flex items-center justify-center text-base">
                    <i class="fa-solid fa-stethoscope"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-slate-900">{{ number_format($totalKlinikTemujanji) }} <span class="text-xs font-normal text-slate-500">kes</span></div>
                <div class="text-xs text-slate-500 mt-1 flex items-center justify-between">
                    <span>Rawatan Selesai: <strong class="text-emerald-600">{{ $totalKlinikSelesai }}</strong></span>
                    <span>Dijadualkan: <strong class="text-slate-700">{{ $totalKlinikDijadualkan }}</strong></span>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Rekod Rawatan: <strong>{{ $totalKlinikRawatan }}</strong></span>
                <span>10 Jajahan Beroperasi</span>
            </div>
        </div>

        <!-- KPI 6: Kursus & Latihan -->
        <div class="rounded-2xl bg-white p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">6. Kursus Penternakan</span>
                <div class="w-9 h-9 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-base">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-slate-900">{{ number_format($totalCourses) }} <span class="text-xs font-normal text-slate-500">kursus</span></div>
                <div class="text-xs text-slate-500 mt-1 flex items-center justify-between">
                    <span>Permohonan: <strong class="text-slate-700">{{ $totalCourseApplications }}</strong></span>
                    <span>Diluluskan: <strong class="text-emerald-600">{{ $totalCourseApproved }}</strong></span>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Kursus Buka: <strong>{{ $totalCoursesActive }}</strong></span>
                <span>Peserta Hadir: <strong>{{ $totalCourseGraduated }}</strong></span>
            </div>
        </div>

        <!-- KPI 7: Stor Ubat & Pejabat -->
        <div class="rounded-2xl bg-white p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">7. Stor &amp; Farmasi</span>
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-base">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-slate-900">{{ number_format($totalUbatItems + $totalPejabatItems) }} <span class="text-xs font-normal text-slate-500">SKU</span></div>
                <div class="text-xs text-slate-500 mt-1 flex items-center justify-between">
                    <span>Ubat/Vaksin: <strong class="text-slate-700">{{ $totalUbatItems }}</strong></span>
                    <span>Alat Pejabat: <strong class="text-slate-700">{{ $totalPejabatItems }}</strong></span>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Stok Rendah Ubat: <strong class="{{ $lowStockUbat > 0 ? 'text-rose-600 font-bold' : 'text-slate-700' }}">{{ $lowStockUbat }}</strong></span>
                <span>Permohonan: <strong>{{ $totalPermohonanUbat + $totalPermohonanPejabat }}</strong></span>
            </div>
        </div>

        <!-- KPI 8: Armada Kenderaan Rasmi -->
        <div class="rounded-2xl bg-white p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">8. Armada Fleet</span>
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base">
                    <i class="fa-solid fa-truck-pickup"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-slate-900">{{ number_format($totalVehicles) }} <span class="text-xs font-normal text-slate-500">unit</span></div>
                <div class="text-xs text-slate-500 mt-1 flex items-center justify-between">
                    <span>Sedia: <strong class="text-emerald-600">{{ $availableVehicles }}</strong></span>
                    <span>Bergerak: <strong class="text-blue-600">{{ $inUseVehicles }}</strong></span>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Dalam Servis: <strong>{{ $inServiceVehicles }}</strong></span>
                <span>Pemandu Bertugas: <strong>{{ $activePemandu }}</strong></span>
            </div>
        </div>
    </div>

    <!-- Section: 6 Interactive Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Chart 1: EPTR & Pawah by Jajahan -->
        <div class="rounded-2xl bg-white p-6 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Taburan Populasi Ruminan (EPTR &amp; Pawah) Mengikut 10 Jajahan</h3>
                    <p class="text-xs text-slate-500">Bilangan ternakan ruminan berdaftar di setiap daerah negeri Kelantan</p>
                </div>
                <span class="p-2 rounded-xl bg-amber-50 text-amber-600 text-sm">
                    <i class="fa-solid fa-chart-column"></i>
                </span>
            </div>
            <div id="chart-eptr-jajahan" class="w-full min-h-[300px]"></div>
        </div>

        <!-- Chart 2: EPU Poultry Population by Jajahan -->
        <div class="rounded-2xl bg-white p-6 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Taburan Ladang &amp; Populasi Unggas (EPU) Mengikut Jajahan</h3>
                    <p class="text-xs text-slate-500">Bilangan ladang berdaftar di bawah Enakmen Penternakan Unggas</p>
                </div>
                <span class="p-2 rounded-xl bg-orange-50 text-orange-600 text-sm">
                    <i class="fa-solid fa-chart-bar"></i>
                </span>
            </div>
            <div id="chart-epu-jajahan" class="w-full min-h-[300px]"></div>
        </div>

        <!-- Chart 3: Ruminant Species Donut -->
        <div class="rounded-2xl bg-white p-6 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Pecahan Spesis Ternakan Ruminan (EPTR)</h3>
                    <p class="text-xs text-slate-500">Peratusan populasi mengikut jenis spesis ternakan</p>
                </div>
                <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600 text-sm">
                    <i class="fa-solid fa-chart-pie"></i>
                </span>
            </div>
            <div id="chart-eptr-spesis" class="w-full min-h-[280px]"></div>
        </div>

        <!-- Chart 4: NAIMbif Breeds Donut -->
        <div class="rounded-2xl bg-white p-6 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Populasi Lembu Bridlot NAIMbif Mengikut Baka</h3>
                    <p class="text-xs text-slate-500">Pecahan baka lembu pedaging kacukan berkualiti tinggi</p>
                </div>
                <span class="p-2 rounded-xl bg-teal-50 text-teal-600 text-sm">
                    <i class="fa-solid fa-chart-pie"></i>
                </span>
            </div>
            <div id="chart-naimbif-baka" class="w-full min-h-[280px]"></div>
        </div>

        <!-- Chart 5: Clinic Appointments by Jajahan -->
        <div class="rounded-2xl bg-white p-6 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Beban Temujanji &amp; Kes Rawatan Klinik Haiwan</h3>
                    <p class="text-xs text-slate-500">Agihan perkhidmatan rawatan veterinar mengikut jajahan</p>
                </div>
                <span class="p-2 rounded-xl bg-pink-50 text-pink-600 text-sm">
                    <i class="fa-solid fa-hospital-user"></i>
                </span>
            </div>
            <div id="chart-klinik-jajahan" class="w-full min-h-[280px]"></div>
        </div>

        <!-- Chart 6: EPU Licensing Pipeline Status -->
        <div class="rounded-2xl bg-white p-6 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Status Aliran Kerja Pelesenan EPU</h3>
                    <p class="text-xs text-slate-500">Perincian status permohonan lesen ladang unggas</p>
                </div>
                <span class="p-2 rounded-xl bg-indigo-50 text-indigo-600 text-sm">
                    <i class="fa-solid fa-bars-progress"></i>
                </span>
            </div>
            <div id="chart-epu-status" class="w-full min-h-[280px]"></div>
        </div>

    </div>

    <!-- Section: Permohonan EPU Menunggu Tindakan Pengarah (Anchor link target) -->
    <div id="senarai-tindakan-pengarah" class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/50">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-xs font-bold mb-1">
                    <i class="fa-solid fa-gavel"></i>
                    <span>Tindakan Eksekutif Pengarah</span>
                </div>
                <h3 class="text-base font-bold text-slate-900">Permohonan Lesen EPU Menunggu Kelulusan / Rayuan Pengarah</h3>
                <p class="text-xs text-slate-500">Senarai permohonan yang telah selesai lawatan verifikasi tapak atau memfailkan rayuan rasmi.</p>
            </div>
            <a href="{{ route('epu.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 hover:text-emerald-700 transition">
                <span>Lihat Semua Di Modul EPU</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase tracking-wider font-semibold border-b border-slate-200/80 text-[11px]">
                    <tr>
                        <th class="px-5 py-3.5">No. Rujukan / Ladang</th>
                        <th class="px-5 py-3.5">Pemohon / Pengusaha</th>
                        <th class="px-5 py-3.5">Jajahan &amp; Lokasi</th>
                        <th class="px-5 py-3.5">Jenis &amp; Kapasiti</th>
                        <th class="px-5 py-3.5">Status Penilaian Tapak</th>
                        <th class="px-5 py-3.5 text-right">Tindakan Pengarah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pendingEpuForDirector as $permohonan)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="px-5 py-4 font-medium text-slate-900">
                            <div class="font-bold text-slate-900">{{ $permohonan->no_rujukan_permohonan ?? 'EPU-' . str_pad($permohonan->id, 5, '0', STR_PAD_LEFT) }}</div>
                            <div class="text-[11px] text-slate-500">{{ $permohonan->ladang->nama_ladang ?? 'Ladang Unggas' }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-semibold text-slate-800">{{ $permohonan->ladang->pemilik->name ?? 'Pengusaha' }}</div>
                            <div class="text-[11px] text-slate-500">{{ $permohonan->ladang->pemilik->phone ?? '-' }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                {{ $permohonan->ladang->jajahan ?? 'Kelantan' }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-medium text-slate-800">{{ $permohonan->jenis_unggas ?? 'Ayam Pedaging' }}</div>
                            <div class="text-[11px] text-slate-500">Kapasiti: {{ number_format($permohonan->kapasiti_ladang ?? 0) }} / Semasa: {{ number_format($permohonan->bilangan_semasa_unggas ?? 0) }}</div>
                        </td>
                        <td class="px-5 py-4">
                            @if($permohonan->status_rayuan === 'Menunggu Semakan Rayuan' || $permohonan->status === 'Rayuan')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                    <i class="fa-solid fa-rotate-left mr-1"></i> Rayuan Diterima
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                    <i class="fa-solid fa-clock mr-1"></i> Sedia Untuk Kelulusan
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('epu.show', $permohonan->epu_ladang_id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-xs transition">
                                <i class="fa-solid fa-stamp"></i>
                                <span>Buat Keputusan Lesen</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-slate-400">
                            <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-2 text-slate-400 text-lg">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>
                            <p class="font-medium text-slate-600">Tiada permohonan lesen atau rayuan EPU yang menunggu kelulusan pada masa ini.</p>
                            <p class="text-xs text-slate-400 mt-0.5">Semua permohonan telah selesai diproses oleh Pegawai Pelesen.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section: Tabbed Module Breakdown Tables -->
    <div class="rounded-2xl bg-white border border-slate-200/80 shadow-xs overflow-hidden">
        <!-- Navigation Tabs -->
        <div class="flex flex-wrap border-b border-slate-200 bg-slate-50/80 px-4 pt-3 gap-2">
            <button @click="activeTab = 'eptr'" :class="activeTab === 'eptr' ? 'bg-white text-emerald-600 border-b-2 border-emerald-600 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 font-medium'" class="px-4 py-2.5 rounded-t-xl text-xs transition flex items-center gap-2">
                <i class="fa-solid fa-cow text-amber-500"></i>
                <span>1. EPTR Ruminan &amp; Sembelihan</span>
            </button>
            <button @click="activeTab = 'pawah'" :class="activeTab === 'pawah' ? 'bg-white text-emerald-600 border-b-2 border-emerald-600 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 font-medium'" class="px-4 py-2.5 rounded-t-xl text-xs transition flex items-center gap-2">
                <i class="fa-solid fa-handshake-angle text-teal-500"></i>
                <span>2. Skim Bantuan Pawah</span>
            </button>
            <button @click="activeTab = 'epu'" :class="activeTab === 'epu' ? 'bg-white text-emerald-600 border-b-2 border-emerald-600 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 font-medium'" class="px-4 py-2.5 rounded-t-xl text-xs transition flex items-center gap-2">
                <i class="fa-solid fa-feather-pointed text-orange-500"></i>
                <span>3. Enakmen Unggas (EPU)</span>
            </button>
            <button @click="activeTab = 'naimbif'" :class="activeTab === 'naimbif' ? 'bg-white text-emerald-600 border-b-2 border-emerald-600 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 font-medium'" class="px-4 py-2.5 rounded-t-xl text-xs transition flex items-center gap-2">
                <i class="fa-solid fa-wheat-awn text-emerald-500"></i>
                <span>4. NAIMbif Bridlot</span>
            </button>
            <button @click="activeTab = 'sokongan'" :class="activeTab === 'sokongan' ? 'bg-white text-emerald-600 border-b-2 border-emerald-600 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 font-medium'" class="px-4 py-2.5 rounded-t-xl text-xs transition flex items-center gap-2">
                <i class="fa-solid fa-cubes text-blue-500"></i>
                <span>5. Klinik, Kursus, Stor &amp; Fleet</span>
            </button>
        </div>

        <!-- Tab 1: EPTR Breakdown -->
        <div x-show="activeTab === 'eptr'" class="p-6 space-y-6">
            <div>
                <h4 class="text-sm font-bold text-slate-900 mb-1">Perincian Taburan Ruminan (EPTR) Mengikut 10 Jajahan</h4>
                <p class="text-xs text-slate-500">Data pendaftaran ternakan, pematuhan tag telinga keselamatan dan permit rasmi</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 border border-slate-200/60 rounded-xl overflow-hidden">
                    <thead class="bg-slate-50 text-slate-700 font-bold text-[11px] uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3">Jajahan / Daerah</th>
                            <th class="px-4 py-3 text-right">Jumlah Ternakan</th>
                            <th class="px-4 py-3 text-right">Peratusan (%)</th>
                            <th class="px-4 py-3 text-center">Status Operasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($jajahanList as $jajahan)
                        @php
                            $cnt = $eptrByJajahan[$jajahan] ?? 0;
                            $pct = $totalTernakanEptr > 0 ? round(($cnt / $totalTernakanEptr) * 100, 1) : 0;
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-semibold text-slate-800">
                                <i class="fa-solid fa-location-dot text-emerald-600 mr-1.5"></i>
                                {{ $jajahan }}
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-slate-900">{{ number_format($cnt) }} ekor</td>
                            <td class="px-4 py-3 text-right">
                                <span class="inline-block w-12 text-right font-medium">{{ $pct }}%</span>
                                <div class="inline-block w-24 bg-slate-100 rounded-full h-1.5 ml-2 align-middle overflow-hidden">
                                    <div class="bg-emerald-600 h-1.5 rounded-full" style="width: {{ min(100, $pct) }}%"></div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Aktif PPVJ
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 font-bold text-slate-900 border-t border-slate-200">
                        <tr>
                            <td class="px-4 py-3">Jumlah Keseluruhan Negeri</td>
                            <td class="px-4 py-3 text-right text-emerald-700 font-extrabold">{{ number_format($totalTernakanEptr) }} ekor</td>
                            <td class="px-4 py-3 text-right">100.0%</td>
                            <td class="px-4 py-3 text-center">-</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Tab 2: Pawah Breakdown -->
        <div x-show="activeTab === 'pawah'" class="p-6 space-y-6" x-cloak>
            <div>
                <h4 class="text-sm font-bold text-slate-900 mb-1">Perincian Skim Bantuan Pawah Ternakan</h4>
                <p class="text-xs text-slate-500">Pemantauan induk pembiakan, kadar kelahiran anak dan taburan peserta mengikut daerah</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-4 rounded-xl bg-teal-50/60 border border-teal-100">
                    <div class="text-xs text-teal-700 font-semibold">Jumlah Perjanjian Pawah</div>
                    <div class="text-xl font-extrabold text-teal-900 mt-1">{{ number_format($totalPawahPerjanjian) }}</div>
                    <div class="text-[11px] text-teal-600 mt-0.5">Aktif: {{ $totalPawahAktif }} | Selesai: {{ $totalPawahSelesai }}</div>
                </div>
                <div class="p-4 rounded-xl bg-emerald-50/60 border border-emerald-100">
                    <div class="text-xs text-emerald-700 font-semibold">Induk Ternakan Diagih</div>
                    <div class="text-xl font-extrabold text-emerald-900 mt-1">{{ number_format($totalPawahInduk) }} ekor</div>
                    <div class="text-[11px] text-emerald-600 mt-0.5">Lembu baka pawah berkualiti</div>
                </div>
                <div class="p-4 rounded-xl bg-blue-50/60 border border-blue-100">
                    <div class="text-xs text-blue-700 font-semibold">Kelahiran Anak Pawah</div>
                    <div class="text-xl font-extrabold text-blue-900 mt-1">{{ number_format($totalPawahKelahiran) }} ekor</div>
                    <div class="text-[11px] text-blue-600 mt-0.5">Hidup: {{ $totalPawahKelahiranHidup }} ({{ $totalPawahKelahiran > 0 ? round(($totalPawahKelahiranHidup/$totalPawahKelahiran)*100, 1) : 0 }}%)</div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 border border-slate-200/60 rounded-xl overflow-hidden">
                    <thead class="bg-slate-50 text-slate-700 font-bold text-[11px] uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3">Jajahan</th>
                            <th class="px-4 py-3 text-right">Perjanjian Aktif</th>
                            <th class="px-4 py-3 text-right">Peratusan (%)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($jajahanList as $jajahan)
                        @php
                            $pCount = $pawahByJajahan[$jajahan] ?? 0;
                            $pPct = $totalPawahPerjanjian > 0 ? round(($pCount / $totalPawahPerjanjian) * 100, 1) : 0;
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-semibold text-slate-800">{{ $jajahan }}</td>
                            <td class="px-4 py-3 text-right font-bold text-slate-900">{{ number_format($pCount) }}</td>
                            <td class="px-4 py-3 text-right">{{ $pPct }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab 3: EPU Breakdown -->
        <div x-show="activeTab === 'epu'" class="p-6 space-y-6" x-cloak>
            <div>
                <h4 class="text-sm font-bold text-slate-900 mb-1">Perincian Pelesenan Enakmen Penternakan Unggas (EPU)</h4>
                <p class="text-xs text-slate-500">Statistik kapasiti kandang, spesis unggas komersial dan hasil kutipan fi lesen negeri</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="p-4 rounded-xl bg-orange-50/60 border border-orange-100">
                    <div class="text-xs text-orange-700 font-semibold">Jumlah Ladang Berdaftar</div>
                    <div class="text-xl font-extrabold text-orange-900 mt-1">{{ number_format($totalEpuLadang) }}</div>
                </div>
                <div class="p-4 rounded-xl bg-emerald-50/60 border border-emerald-100">
                    <div class="text-xs text-emerald-700 font-semibold">Lesen Borang B Diluluskan</div>
                    <div class="text-xl font-extrabold text-emerald-900 mt-1">{{ number_format($totalEpuLulus) }}</div>
                </div>
                <div class="p-4 rounded-xl bg-amber-50/60 border border-amber-100">
                    <div class="text-xs text-amber-700 font-semibold">Menunggu Kelulusan Pengarah</div>
                    <div class="text-xl font-extrabold text-amber-900 mt-1">{{ number_format($totalEpuPendingPelesen) }}</div>
                </div>
                <div class="p-4 rounded-xl bg-purple-50/60 border border-purple-100">
                    <div class="text-xs text-purple-700 font-semibold">Jumlah Fi Lesen Dikutip</div>
                    <div class="text-xl font-extrabold text-purple-900 mt-1">RM {{ number_format($totalEpuFiKutipan, 2) }}</div>
                </div>
            </div>
        </div>

        <!-- Tab 4: NAIMbif Breakdown -->
        <div x-show="activeTab === 'naimbif'" class="p-6 space-y-6" x-cloak>
            <div>
                <h4 class="text-sm font-bold text-slate-900 mb-1">Perincian Program Ladang Bridlot Pedaging NAIMbif</h4>
                <p class="text-xs text-slate-500">Taburan baka lembu pedaging premium dan status penilaian premis kandang / padang ragut</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach($naimbifBakaRaw as $baka => $count)
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                    <div class="text-xs text-slate-500 font-bold uppercase">{{ $baka }}</div>
                    <div class="text-xl font-black text-slate-900 mt-1">{{ number_format($count) }} <span class="text-xs font-normal text-slate-500">ekor</span></div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Tab 5: Sokongan Breakdown -->
        <div x-show="activeTab === 'sokongan'" class="p-6 space-y-6" x-cloak>
            <div>
                <h4 class="text-sm font-bold text-slate-900 mb-1">Perkhidmatan Sokongan, Latihan &amp; Logistik Jabatan</h4>
                <p class="text-xs text-slate-500">Prestasi klinik veterinar jajahan, kursus penternakan, stor farmasi &amp; pergerakan kenderaan rasmi</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-4 rounded-xl bg-pink-50 border border-pink-100">
                    <div class="text-xs font-bold text-pink-800">Klinik Haiwan</div>
                    <div class="text-lg font-black text-pink-900 mt-1">{{ $totalKlinikTemujanji }} Temujanji</div>
                    <div class="text-xs text-pink-700 mt-1">Selesai Dirawat: {{ $totalKlinikSelesai }}</div>
                </div>
                <div class="p-4 rounded-xl bg-cyan-50 border border-cyan-100">
                    <div class="text-xs font-bold text-cyan-800">Kursus Penternakan</div>
                    <div class="text-lg font-black text-cyan-900 mt-1">{{ $totalCourses }} Modul Ditawar</div>
                    <div class="text-xs text-cyan-700 mt-1">Peserta Hadir: {{ $totalCourseGraduated }}</div>
                </div>
                <div class="p-4 rounded-xl bg-indigo-50 border border-indigo-100">
                    <div class="text-xs font-bold text-indigo-800">Stor Ubat &amp; Pejabat</div>
                    <div class="text-lg font-black text-indigo-900 mt-1">{{ $totalUbatItems }} Ubat | {{ $totalPejabatItems }} Pejabat</div>
                    <div class="text-xs text-indigo-700 mt-1">Stok Rendah: {{ $lowStockUbat + $lowStockPejabat }}</div>
                </div>
                <div class="p-4 rounded-xl bg-blue-50 border border-blue-100">
                    <div class="text-xs font-bold text-blue-800">Armada Kenderaan</div>
                    <div class="text-lg font-black text-blue-900 mt-1">{{ $totalVehicles }} Unit Kenderaan</div>
                    <div class="text-xs text-blue-700 mt-1">Sedia: {{ $availableVehicles }} | Digunakan: {{ $inUseVehicles }}</div>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- ApexCharts Script Rendering -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const jajahanLabels = @json($jajahanList);
    const eptrData = @json(array_values($eptrByJajahan));
    const pawahData = @json(array_values($pawahByJajahan));
    const epuData = @json(array_values($epuByJajahan));
    const klinikData = @json(array_values($klinikByJajahan));

    // 1. Chart EPTR & Pawah by Jajahan
    new ApexCharts(document.querySelector('#chart-eptr-jajahan'), {
        series: [
            { name: 'Ternakan Ruminan (EPTR)', data: eptrData },
            { name: 'Perjanjian Pawah', data: pawahData }
        ],
        chart: { type: 'bar', height: 300, toolbar: { show: false } },
        colors: ['#d97706', '#0d9488'],
        plotOptions: { bar: { horizontal: false, columnWidth: '55%', borderRadius: 4 } },
        dataLabels: { enabled: false },
        xaxis: { categories: jajahanLabels, labels: { rotate: -45, style: { fontSize: '11px' } } },
        yaxis: { title: { text: 'Bilangan Ternakan / Perjanjian' } },
        legend: { position: 'top' },
        fill: { opacity: 1 }
    }).render();

    // 2. Chart EPU by Jajahan
    new ApexCharts(document.querySelector('#chart-epu-jajahan'), {
        series: [{ name: 'Bilangan Ladang Unggas', data: epuData }],
        chart: { type: 'bar', height: 300, toolbar: { show: false } },
        colors: ['#f97316'],
        plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '60%' } },
        dataLabels: { enabled: true, style: { fontSize: '11px' } },
        xaxis: { categories: jajahanLabels },
        yaxis: { title: { text: 'Jajahan' } }
    }).render();

    // 3. Chart EPTR Species Donut
    const eptrSpecies = @json($eptrSpeciesData);
    new ApexCharts(document.querySelector('#chart-eptr-spesis'), {
        series: Object.values(eptrSpecies),
        labels: Object.keys(eptrSpecies),
        chart: { type: 'donut', height: 280 },
        colors: ['#d97706', '#475569', '#10b981', '#06b6d4', '#8b5cf6', '#94a3b8'],
        legend: { position: 'bottom' },
        responsive: [{ breakpoint: 480, options: { chart: { width: 260 }, legend: { position: 'bottom' } } }]
    }).render();

    // 4. Chart NAIMbif Breeds Donut
    const naimbifBaka = @json($naimbifBakaRaw);
    new ApexCharts(document.querySelector('#chart-naimbif-baka'), {
        series: Object.values(naimbifBaka).length ? Object.values(naimbifBaka) : [1],
        labels: Object.keys(naimbifBaka).length ? Object.keys(naimbifBaka) : ['Tiada Data'],
        chart: { type: 'donut', height: 280 },
        colors: ['#059669', '#2563eb', '#ea580c', '#db2777', '#7c3aed', '#64748b'],
        legend: { position: 'bottom' }
    }).render();

    // 5. Chart Clinic Appointments by Jajahan
    new ApexCharts(document.querySelector('#chart-klinik-jajahan'), {
        series: [{ name: 'Jumlah Kes Rawatan', data: klinikData }],
        chart: { type: 'bar', height: 280, toolbar: { show: false } },
        colors: ['#ec4899'],
        plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
        dataLabels: { enabled: false },
        xaxis: { categories: jajahanLabels, labels: { rotate: -45, style: { fontSize: '11px' } } }
    }).render();

    // 6. Chart EPU Status Pipeline
    new ApexCharts(document.querySelector('#chart-epu-status'), {
        series: [
            {{ $totalEpuLulus }},
            {{ $totalEpuPendingPelesen }},
            {{ $totalEpuPendingVerifikasi }},
            {{ $totalEpuRayuan }},
            {{ $totalEpuDitolak }}
        ],
        labels: ['Diluluskan / Berlesen', 'Menunggu Kelulusan Pengarah', 'Menunggu Verifikasi Tapak', 'Dalam Rayuan', 'Ditolak'],
        chart: { type: 'donut', height: 280 },
        colors: ['#16a34a', '#d97706', '#3b82f6', '#9333ea', '#e11d48'],
        legend: { position: 'bottom' }
    }).render();
});
</script>
@endsection