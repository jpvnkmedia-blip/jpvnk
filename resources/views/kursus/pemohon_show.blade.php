@extends('layouts.app')

@section('title', 'Maklumat Pemohon & Ternakan - ' . ($application->user->name ?? 'Peserta'))
@section('page_title', 'Profil Pemohon & Maklumat Ternakan Peserta')

@section('content')
<div x-data="{
    rejectModalOpen: false,
    rejectReason: 'Kapasiti kursus telah penuh'
}" class="space-y-6">

    <!-- Header Navigation & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('kursus.pemohon.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition">
                    &larr; Hab Pengurusan Pemohon
                </a>
                <span class="text-xs text-slate-300">•</span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-cyan-100 text-cyan-800">
                    No. Daftar: {{ $application->registration_number }}
                </span>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 mt-1">Profil Pemohon &amp; Maklumat Ternakan Berdaftar</h2>
            <p class="text-xs text-slate-500 mt-0.5">Semak profil peribadi pemohon, aset ternakan ruminan/unggas dalam pangkalan data sepunya, dan status kursus</p>
        </div>

        <!-- Quick Status & Actions -->
        <div class="flex flex-wrap items-center gap-2">
            @if($application->status === 'Menunggu')
                <form action="{{ route('kursus.pemohon.lulus', $application->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-lg shadow-emerald-700/30 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-check"></i>
                        <span>Luluskan Permohonan</span>
                    </button>
                </form>

                <button type="button" @click="rejectModalOpen = true" class="px-4 py-2 rounded-xl bg-rose-100 hover:bg-rose-200 text-rose-800 font-bold text-xs transition flex items-center gap-1.5">
                    <i class="fa-solid fa-xmark"></i>
                    <span>Tolak Permohonan</span>
                </button>
            @elseif($application->status === 'Disahkan')
                <form action="{{ route('kursus.pemohon.hadir', $application->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-bold text-xs shadow-lg shadow-cyan-700/30 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-award"></i>
                        <span>Sahkan Kehadiran &amp; Jana Sijil Digital</span>
                    </button>
                </form>

                <button type="button" @click="rejectModalOpen = true" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-rose-100 text-slate-600 hover:text-rose-700 font-bold text-xs transition">
                    <i class="fa-solid fa-ban mr-1"></i> Batal / Tolak
                </button>
            @elseif($application->status === 'Hadir' || $application->status === 'Selesai')
                <a href="{{ route('kursus.sijil', $application->id) }}" target="_blank" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs shadow-md transition flex items-center gap-1.5">
                    <i class="fa-solid fa-print"></i>
                    <span>Cetak Sijil Digital Rasmi</span>
                </a>
            @elseif($application->status === 'Ditolak')
                <form action="{{ route('kursus.pemohon.lulus', $application->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-emerald-100 text-slate-700 hover:text-emerald-800 font-bold text-xs transition flex items-center gap-1.5">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Pulihkan &amp; Luluskan Semula</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Status Overview Banner Card -->
    <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-cyan-100 text-cyan-800 flex items-center justify-center text-2xl font-black shrink-0">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-lg font-black text-slate-900">{{ $application->user->name ?? 'N/A' }}</h3>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                        {{ $application->user->role_label ?? $application->user->role }}
                    </span>
                </div>
                <div class="text-xs text-slate-500 flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 font-mono">
                    <span><i class="fa-solid fa-id-card text-slate-400 mr-1"></i> {{ $application->user->ic_number ?? '-' }}</span>
                    <span><i class="fa-solid fa-envelope text-slate-400 mr-1"></i> {{ $application->user->email ?? '-' }}</span>
                    <span><i class="fa-solid fa-phone text-slate-400 mr-1"></i> {{ $application->user->phone ?? '-' }}</span>
                </div>
            </div>
        </div>

        <div class="text-left md:text-right">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status Permohonan Kursus:</div>
            <div class="mt-1">
                @if($application->status === 'Menunggu')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-200">
                        <i class="fa-solid fa-clock-rotate-left mr-1.5"></i> Menunggu Kelulusan
                    </span>
                @elseif($application->status === 'Disahkan')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-900 border border-blue-200">
                        <i class="fa-solid fa-circle-check mr-1.5"></i> Diluluskan (Tempat Sah)
                    </span>
                @elseif($application->status === 'Hadir' || $application->status === 'Selesai')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-900 border border-emerald-200">
                        <i class="fa-solid fa-award mr-1.5"></i> Hadir &amp; Selesai
                    </span>
                @elseif($application->status === 'Ditolak')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-900 border border-rose-200">
                        <i class="fa-solid fa-circle-xmark mr-1.5"></i> Ditolak
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                        {{ $application->status }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Applicant Farming & Livestock KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Card 1: Ternakan EPTR Ruminan -->
        <div class="p-5 rounded-3xl bg-white border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wider block">EPTR Ruminan</span>
                <div class="text-2xl font-black text-slate-900 mt-0.5">{{ $ternakanList->count() }} Ekor</div>
                <div class="text-xs text-slate-500 mt-0.5">Ternakan Berdaftar (Borang A/B)</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-cow"></i>
            </div>
        </div>

        <!-- Card 2: Skim Lembu Pawah -->
        <div class="p-5 rounded-3xl bg-white border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">Program Pawah</span>
                <div class="text-2xl font-black text-slate-900 mt-0.5">{{ $pawahList->count() }} Perjanjian</div>
                <div class="text-xs text-slate-500 mt-0.5">Peserta Skim Pawah Ternakan</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-handshake-angle"></i>
            </div>
        </div>

        <!-- Card 3: EPU Ladang Unggas -->
        <div class="p-5 rounded-3xl bg-white border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-amber-700 uppercase tracking-wider block">EPU Unggas</span>
                <div class="text-2xl font-black text-slate-900 mt-0.5">{{ $ladangUnggasList->count() }} Premis</div>
                <div class="text-xs text-slate-500 mt-0.5">Ladang Unggas Komersil</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-feather-pointed"></i>
            </div>
        </div>
    </div>

    <!-- Main 2-Column Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 text-xs">
        
        <!-- Column 1: Profil Peribadi & Hubungan Pemohon -->
        <div class="space-y-6">
            
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center font-bold">
                            <i class="fa-solid fa-address-card"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">1. Maklumat Peribadi &amp; Profil Pemohon</h3>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Pangkalan Data Sepunya</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nama Penuh</span>
                        <span class="text-sm font-bold text-slate-900 mt-0.5 block">{{ $application->user->name ?? '-' }}</span>
                    </div>

                    <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">No. Kad Pengenalan</span>
                        <span class="text-sm font-mono font-bold text-slate-900 mt-0.5 block">{{ $application->user->ic_number ?? 'Tiada Rekod' }}</span>
                    </div>

                    <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nombor Telefon / WhatsApp</span>
                        <span class="text-sm font-bold text-slate-900 mt-0.5 block">{{ $application->user->phone ?? '-' }}</span>
                    </div>

                    <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Alamat Emel</span>
                        <span class="text-sm font-bold text-slate-900 mt-0.5 block truncate">{{ $application->user->email ?? '-' }}</span>
                    </div>

                    <div class="sm:col-span-2 bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Alamat Kediaman / Premis</span>
                        <span class="text-xs font-semibold text-slate-800 mt-0.5 block">{{ $application->user->address ?: 'Alamat tidak dinyatakan secara terperinci.' }}</span>
                    </div>

                    <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Jajahan / Cawangan</span>
                        <span class="text-xs font-bold text-slate-900 mt-0.5 block">{{ $application->user->jajahan ?: ($application->course->jajahan ?? 'Kelantan') }}</span>
                    </div>

                    <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Kaedah Log Masuk / Identiti</span>
                        <span class="text-xs font-bold text-slate-900 mt-0.5 block flex items-center gap-1.5">
                            @if(($application->user->auth_provider ?? '') === 'google')
                                <i class="fa-brands fa-google text-red-500"></i> Google SSO
                            @elseif(($application->user->auth_provider ?? '') === 'mydigital_id')
                                <i class="fa-solid fa-id-card text-blue-600"></i> MyDigital ID
                            @else
                                <i class="fa-solid fa-envelope text-slate-500"></i> Manual ID
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Kad Pensijilan Digital -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-700 flex items-center justify-center font-bold">
                            <i class="fa-solid fa-certificate"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">2. Status Sijil Penyertaan Digital</h3>
                    </div>
                    <span class="text-[10px] font-bold text-purple-600 uppercase">Diperakui JPVNK</span>
                </div>

                <div class="space-y-3">
                    @if($application->certificate_number)
                        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-bold text-amber-800 uppercase tracking-wider block">Nombor Siri Sijil Digital</span>
                                <span class="text-base font-mono font-black text-amber-950 mt-0.5 block">{{ $application->certificate_number }}</span>
                                <span class="text-[10px] text-amber-700 mt-1 block">Tarikh Dikeluarkan: <b>{{ $application->certificate_issued_at ? $application->certificate_issued_at->format('d/m/Y') : '-' }}</b></span>
                            </div>
                            <a href="{{ route('kursus.sijil', $application->id) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs shadow-xs transition flex items-center gap-1.5">
                                <i class="fa-solid fa-print"></i>
                                <span>Cetak Sijil</span>
                            </a>
                        </div>
                    @else
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-slate-500">
                            <div class="flex items-center gap-2 font-semibold">
                                <i class="fa-solid fa-circle-info text-cyan-600"></i>
                                <span>Sijil Digital Belum Dijana</span>
                            </div>
                            <p class="text-[11px] mt-1 text-slate-400">
                                Sijil digital akan dijana secara automatik dengan nombor siri bersiri apabila Pegawai Kursus menekan butang <b>"Sahkan Kehadiran &amp; Jana Sijil Digital"</b>.
                            </p>
                        </div>
                    @endif

                    @if($application->rejection_reason)
                        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900">
                            <span class="text-[10px] font-bold text-rose-700 uppercase tracking-wider block">Catatan Sebab Penolakan Rasmi:</span>
                            <p class="text-xs font-semibold mt-1">{{ $application->rejection_reason }}</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- Column 2: Maklumat Kursus & Sejarah Kursus Lain -->
        <div class="space-y-6">
            
            <!-- Maklumat Kursus Semasa -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-cyan-50 text-cyan-700 flex items-center justify-center font-bold">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">3. Kursus Yang Dipohon</h3>
                    </div>
                    <a href="{{ route('kursus.show', $application->course_id) }}" class="text-xs font-bold text-cyan-700 hover:text-cyan-900">
                        Lihat Kursus &rarr;
                    </a>
                </div>

                <div class="space-y-3">
                    <div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-cyan-50 text-cyan-800 border border-cyan-200 uppercase">
                            {{ $application->course->category ?? '-' }} &bull; {{ $application->course->code ?? '-' }}
                        </span>
                        <h4 class="text-base font-extrabold text-slate-900 mt-1.5">{{ $application->course->title ?? '-' }}</h4>
                        <p class="text-slate-500 text-[11px] mt-0.5">Penceramah: <b>{{ $application->course->trainer_name ?? '-' }}</b></p>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-100">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Tarikh Kursus</span>
                            <span class="font-semibold text-slate-800">{{ $application->course->start_date ? $application->course->start_date->format('d/m/Y') : '-' }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Masa</span>
                            <span class="font-semibold text-slate-800">{{ $application->course->time ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Lokasi &amp; Jajahan</span>
                            <span class="font-semibold text-slate-800 truncate block">{{ $application->course->location }} ({{ $application->course->jajahan }})</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Yuran Pendaftaran</span>
                            <span class="font-semibold text-emerald-700">{{ ($application->course->fee ?? 0) > 0 ? 'RM ' . number_format($application->course->fee, 2) : 'Percuma (Tajaan JPVNK)' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sejarah Kursus Lain Yang Pernah Disertai -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">4. Sejarah Permohonan Kursus Lain</h3>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400">Rekod Pengguna</span>
                </div>

                <div class="space-y-2.5">
                    @php
                        $otherApplications = $application->user ? $application->user->permohonanKursus->where('id', '!=', $application->id) : collect();
                    @endphp

                    @forelse($otherApplications as $other)
                        <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between gap-3">
                            <div>
                                <a href="{{ route('kursus.pemohon.show', $other->id) }}" class="font-bold text-slate-900 hover:text-cyan-700 transition block">
                                    {{ $other->course->title ?? 'Kursus' }}
                                </a>
                                <div class="text-[10px] text-slate-500 font-mono mt-0.5">
                                    {{ $other->registration_number }} &bull; {{ $other->course->start_date ? $other->course->start_date->format('d/m/Y') : '-' }}
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                @if($other->status === 'Disahkan')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                        Disahkan
                                    </span>
                                @elseif($other->status === 'Hadir' || $other->status === 'Selesai')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        Hadir
                                    </span>
                                @elseif($other->status === 'Menunggu')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                        Menunggu
                                    </span>
                                @elseif($other->status === 'Ditolak')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                        Ditolak
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-slate-400 text-xs">
                            Tiada rekod kursus lain bagi pengguna ini. Ini adalah permohonan pertama beliau.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    <!-- Section: Maklumat Terperinci Ternakan & Premis Pemohon (EPTR, Pawah, EPU) -->
    <div class="space-y-6 pt-4 border-t border-slate-200">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800">
                        Pangkalan Data Sepunya JPVNK
                    </span>
                    <span class="text-xs text-slate-400">•</span>
                    <span class="text-xs font-semibold text-slate-500">Profil Ternakan Pemohon</span>
                </div>
                <h3 class="text-base font-extrabold text-slate-900 mt-1">Maklumat Ternakan &amp; Premis Ladang Pemohon</h3>
                <p class="text-xs text-slate-500">Rekod pendaftaran lembu/kambing EPTR, perjanjian skim pawah dan lesen reban unggas EPU bagi pemohon ini</p>
            </div>
        </div>

        <!-- 1. Jadual Ternakan Ruminan EPTR -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-cow"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">Senarai Ternakan Ruminan EPTR ({{ $ternakanList->count() }} Ekor)</h4>
                        <p class="text-[11px] text-slate-400">Pendaftaran Enakmen Penternakan Ruminan (EPTR 2024)</p>
                    </div>
                </div>
                @if($pemunya)
                    <span class="text-[11px] font-mono font-semibold text-slate-500 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100">
                        ID Pemunya: #{{ $pemunya->id }} &bull; {{ $pemunya->jajahan }}
                    </span>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-bold text-[11px]">
                            <th class="px-4 py-3">No. Tag / Kad Kuning</th>
                            <th class="px-4 py-3">Jenis &amp; Baka</th>
                            <th class="px-4 py-3">Jantina &amp; Umur</th>
                            <th class="px-4 py-3">Tujuan Ternakan</th>
                            <th class="px-4 py-3">Lokasi Kandang</th>
                            <th class="px-4 py-3">Program Bantuan</th>
                            <th class="px-4 py-3 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($ternakanList as $t)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3">
                                    <div class="font-mono font-bold text-emerald-800">{{ $t->no_tag ?: 'Belum Tag' }}</div>
                                    @if($t->no_siri_kad_kuning)
                                        <div class="text-[10px] text-amber-700 font-mono">Kad: {{ $t->no_siri_kad_kuning }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-900">{{ $t->jenis_ternakan }}</div>
                                    <div class="text-[10px] text-slate-500">{{ $t->baka }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-semibold text-slate-800">{{ $t->jantina }}</span>
                                    <div class="text-[10px] text-slate-400">{{ $t->umur ?: ($t->tarikh_lahir ? $t->tarikh_lahir->format('d/m/Y') : '-') }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">
                                        {{ $t->tujuan_ternakan }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-slate-800 font-medium">{{ $t->lokasi_kandang ?: 'Kandang Utama' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $t->jajahan }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-slate-600 font-medium">{{ $t->program ?: 'Persendirian' }}</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $t->status === 'Aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $t->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-6 text-center text-slate-400 text-xs">
                                    Tiada rekod ternakan ruminan (EPTR) didaftarkan di bawah nama pemohon ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- 2. Jadual Skim Program Pawah -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-handshake-angle"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Perjanjian Skim Pawah ({{ $pawahList->count() }})</h4>
                            <p class="text-[11px] text-slate-400">Skim Pembiakan Ternakan JPVNK</p>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-bold text-[10px]">
                                <th class="px-3 py-2.5">No. Perjanjian</th>
                                <th class="px-3 py-2.5">Bilangan Lembu</th>
                                <th class="px-3 py-2.5">Tarikh Perjanjian</th>
                                <th class="px-3 py-2.5 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($pawahList as $p)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-3 py-2.5 font-mono font-bold text-emerald-800">
                                        {{ $p->no_perjanjian }}
                                    </td>
                                    <td class="px-3 py-2.5 font-bold text-slate-900">
                                        {{ $p->ternakanList ? $p->ternakanList->count() : ($p->bilangan_induk ?? 1) }} Ekor
                                    </td>
                                    <td class="px-3 py-2.5 text-slate-600">
                                        {{ $p->tarikh_mula ? $p->tarikh_mula->format('d/m/Y') : ($p->tarikh_perjanjian ? $p->tarikh_perjanjian->format('d/m/Y') : '-') }}
                                    </td>
                                    <td class="px-3 py-2.5 text-right">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            {{ $p->status ?? 'Aktif' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-5 text-center text-slate-400 text-xs">
                                        Pemohon bukan peserta aktif skim pawah.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. Jadual Ladang Unggas EPU -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-feather-pointed"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Ladang Unggas EPU ({{ $ladangUnggasList->count() }})</h4>
                            <p class="text-[11px] text-slate-400">Enakmen Perladangan Unggas 2005</p>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-bold text-[10px]">
                                <th class="px-3 py-2.5">Nama Ladang &amp; No. Lesen</th>
                                <th class="px-3 py-2.5">Kategori / Spesies</th>
                                <th class="px-3 py-2.5">Kapasiti Reban</th>
                                <th class="px-3 py-2.5 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($ladangUnggasList as $ladang)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-3 py-2.5">
                                        <div class="font-bold text-slate-900">{{ $ladang->nama_ladang }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">{{ $ladang->no_lesen ?: ($ladang->no_pendaftaran ?? 'Permohonan Baru') }}</div>
                                    </td>
                                    <td class="px-3 py-2.5">
                                        <div class="font-semibold text-slate-800">{{ $ladang->jenis_unggas ?: 'Ayam Daging' }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $ladang->sistem_reban ?: 'Tertutup' }}</div>
                                    </td>
                                    <td class="px-3 py-2.5 font-bold text-slate-900">
                                        {{ number_format($ladang->kapasiti_ternakan ?? $ladang->kapasiti ?? 0) }} Ekor
                                    </td>
                                    <td class="px-3 py-2.5 text-right">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                            {{ $ladang->status_lesen ?? $ladang->status ?? 'Aktif' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-5 text-center text-slate-400 text-xs">
                                        Tiada rekod ladang unggas komersil (EPU) berdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Penolakan Permohonan -->
    <div x-show="rejectModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="rejectModalOpen = false" class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-base">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Tolak Permohonan Kursus</h3>
                </div>
                <button type="button" @click="rejectModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <p class="text-xs text-slate-600">
                Anda sedang menolak permohonan pendaftaran daripada <b class="text-slate-900">{{ $application->user->name ?? 'Peserta' }}</b>:
            </p>

            <form action="{{ route('kursus.pemohon.tolak', $application->id) }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Pilihan Cepat Sebab Penolakan</label>
                    <select @change="rejectReason = $event.target.value" class="w-full rounded-xl border border-slate-200 px-3 py-2 bg-slate-50 text-xs focus:ring-2 focus:ring-rose-500 focus:outline-none mb-2">
                        <option value="Kapasiti kursus telah penuh">Kapasiti kursus telah penuh</option>
                        <option value="Maklumat pengenalan diri tidak lengkap">Maklumat pengenalan diri tidak lengkap</option>
                        <option value="Syarat kelayakan kursus tidak dipenuhi">Syarat kelayakan kursus tidak dipenuhi</option>
                        <option value="Kursus telah dibatalkan atau ditunda">Kursus telah dibatalkan atau ditunda</option>
                        <option value="">-- Nyatakan Sebab Lain --</option>
                    </select>

                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Catatan Sebab Penolakan</label>
                    <textarea name="rejection_reason" x-model="rejectReason" rows="3" required class="w-full rounded-xl border border-slate-200 p-3 text-xs focus:ring-2 focus:ring-rose-500 focus:outline-none placeholder:text-slate-400" placeholder="Nyatakan sebab penolakan secara rasmi..."></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="rejectModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold shadow-md shadow-rose-600/30 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-ban"></i>
                        <span>Sahkan Tolak</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

