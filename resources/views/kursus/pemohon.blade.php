@extends('layouts.app')

@section('title', 'Pengurusan Permohonan Peserta Kursus')
@section('page_title', 'Pengurusan Permohonan & Kelulusan Peserta Kursus')

@section('content')
<div x-data="{ 
    selectedApplicants: [], 
    selectAll: false,
    rejectModalOpen: false,
    rejectAppId: null,
    rejectApplicantName: '',
    rejectReason: '',
    toggleSelectAll() {
        if (this.selectAll) {
            this.selectedApplicants = Array.from(document.querySelectorAll('.applicant-checkbox')).map(el => el.value);
        } else {
            this.selectedApplicants = [];
        }
    },
    openRejectModal(id, name) {
        this.rejectAppId = id;
        this.rejectApplicantName = name;
        this.rejectReason = 'Kapasiti kursus telah penuh';
        this.rejectModalOpen = true;
    }
}" class="space-y-6">

    <!-- Header Navigation & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-cyan-100 text-cyan-800">
                    Modul Pentadbiran Kursus
                </span>
                <span class="text-xs text-slate-400 font-mono">•</span>
                <span class="text-xs text-slate-500 font-semibold">Kelulusan & Pengesahan Kehadiran</span>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 mt-1">Hab Pengurusan Peserta Kursus Ternakan</h2>
            <p class="text-xs text-slate-500 mt-0.5">Semak permohonan pendaftaran peserta, buat kelulusan, rekod kehadiran fizikal dan jana sijil digital PDF</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('kursus.index') }}" class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-2xs transition flex items-center gap-1.5">
                <i class="fa-solid fa-list-ul text-cyan-600"></i>
                <span>Direktori Kursus</span>
            </a>
            <a href="{{ route('kursus.create') }}" class="px-4 py-2 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white text-xs font-bold shadow-lg shadow-cyan-700/30 transition flex items-center gap-2">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Terbitkan Kursus</span>
            </a>
        </div>
    </div>

    <!-- 5 KPI Status Counter Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
        <!-- 1. Jumlah Permohonan -->
        <a href="{{ route('kursus.pemohon.index') }}" class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:shadow-md transition flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-base">
                    <i class="fa-solid fa-users"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-400 uppercase">Semua</span>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-900">{{ $totalApplications }}</div>
                <div class="text-xs font-semibold text-slate-600">Jumlah Permohonan</div>
            </div>
        </a>

        <!-- 2. Menunggu Kelulusan -->
        <a href="{{ route('kursus.pemohon.index', ['status' => 'Menunggu']) }}" class="p-4 rounded-2xl bg-white border {{ request('status') === 'Menunggu' ? 'border-amber-500 ring-2 ring-amber-200' : 'border-slate-200/80' }} shadow-xs hover:shadow-md transition flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-base">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <span class="text-[10px] font-bold text-amber-600 uppercase">Tindakan</span>
            </div>
            <div>
                <div class="text-2xl font-black text-amber-600">{{ $totalPending }}</div>
                <div class="text-xs font-semibold text-slate-700">Menunggu Kelulusan</div>
            </div>
        </a>

        <!-- 3. Disahkan / Layak -->
        <a href="{{ route('kursus.pemohon.index', ['status' => 'Disahkan']) }}" class="p-4 rounded-2xl bg-white border {{ request('status') === 'Disahkan' ? 'border-blue-500 ring-2 ring-blue-200' : 'border-slate-200/80' }} shadow-xs hover:shadow-md transition flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <span class="text-[10px] font-bold text-blue-600 uppercase">Disahkan</span>
            </div>
            <div>
                <div class="text-2xl font-black text-blue-700">{{ $totalApproved }}</div>
                <div class="text-xs font-semibold text-slate-700">Diluluskan (Tempat Sah)</div>
            </div>
        </a>

        <!-- 4. Hadir & Sijil Dijana -->
        <a href="{{ route('kursus.pemohon.index', ['status' => 'Hadir']) }}" class="p-4 rounded-2xl bg-white border {{ request('status') === 'Hadir' ? 'border-emerald-500 ring-2 ring-emerald-200' : 'border-slate-200/80' }} shadow-xs hover:shadow-md transition flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base">
                    <i class="fa-solid fa-award"></i>
                </div>
                <span class="text-[10px] font-bold text-emerald-600 uppercase">Sijil Sedia</span>
            </div>
            <div>
                <div class="text-2xl font-black text-emerald-700">{{ $totalAttended }}</div>
                <div class="text-xs font-semibold text-slate-700">Hadir &amp; Pensijilan</div>
            </div>
        </a>

        <!-- 5. Ditolak -->
        <a href="{{ route('kursus.pemohon.index', ['status' => 'Ditolak']) }}" class="p-4 rounded-2xl bg-white border {{ request('status') === 'Ditolak' ? 'border-rose-500 ring-2 ring-rose-200' : 'border-slate-200/80' }} shadow-xs hover:shadow-md transition flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-base">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
                <span class="text-[10px] font-bold text-rose-600 uppercase">Ditolak</span>
            </div>
            <div>
                <div class="text-2xl font-black text-rose-600">{{ $totalRejected }}</div>
                <div class="text-xs font-semibold text-slate-700">Permohonan Ditolak</div>
            </div>
        </a>
    </div>

    <!-- Search & Filter Card -->
    <div class="bg-white rounded-3xl border border-slate-200 p-5 shadow-xs">
        <form action="{{ route('kursus.pemohon.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
            <!-- Filter Kursus -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Pilih Kursus</label>
                <select name="course_id" class="w-full rounded-xl border border-slate-200 px-3 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    <option value="">-- Semua Kursus --</option>
                    @foreach($coursesList as $c)
                        <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>
                            {{ Str::limit($c->title, 35) }} ({{ $c->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Status -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Status Permohonan</label>
                <select name="status" class="w-full rounded-xl border border-slate-200 px-3 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    <option value="">-- Semua Status --</option>
                    <option value="Menunggu" {{ request('status') === 'Menunggu' ? 'selected' : '' }}>Menunggu Kelulusan</option>
                    <option value="Disahkan" {{ request('status') === 'Disahkan' ? 'selected' : '' }}>Diluluskan / Disahkan</option>
                    <option value="Hadir" {{ request('status') === 'Hadir' ? 'selected' : '' }}>Hadir (Sijil Dijana)</option>
                    <option value="Ditolak" {{ request('status') === 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <!-- Filter Jajahan -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Jajahan Lokasi</label>
                <select name="jajahan" class="w-full rounded-xl border border-slate-200 px-3 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    <option value="">-- Semua Jajahan --</option>
                    @foreach(['Kota Bharu', 'Pasir Mas', 'Tumpat', 'Bachok', 'Pasir Puteh', 'Machang', 'Tanah Merah', 'Jeli', 'Kuala Krai', 'Gua Musang'] as $j)
                        <option value="{{ $j }}" {{ request('jajahan') === $j ? 'selected' : '' }}>{{ $j }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Carian Kata Kunci & Butang -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Carian Peserta / Sijil</label>
                <div class="flex items-center gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / No. KP / No. Daftar" class="w-full rounded-xl border border-slate-200 px-3 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none text-xs">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold transition">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                    @if(request()->hasAny(['course_id', 'status', 'jajahan', 'search']))
                        <a href="{{ route('kursus.pemohon.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold transition" title="Reset Penapis">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Bulk Action Toolbar (When checkboxes are selected) -->
    <div x-show="selectedApplicants.length > 0" x-cloak class="p-4 rounded-2xl bg-cyan-900 text-white flex flex-col sm:flex-row items-center justify-between gap-3 shadow-lg transition">
        <div class="flex items-center gap-3">
            <span class="w-7 h-7 rounded-lg bg-cyan-700 text-white flex items-center justify-center font-bold text-xs">
                <span x-text="selectedApplicants.length"></span>
            </span>
            <span class="text-xs font-semibold">Peserta Dipilih untuk Tindakan Pukal</span>
        </div>
        <form action="{{ route('kursus.pemohon.lulus-pukal') }}" method="POST" class="flex items-center gap-2">
            @csrf
            <template x-for="id in selectedApplicants" :key="id">
                <input type="hidden" name="application_ids[]" :value="id">
            </template>
            <button type="submit" onclick="return confirm('Adakah anda pasti mahu meluluskan semua peserta yang dipilih?')" class="px-4 py-1.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold text-xs shadow-xs transition flex items-center gap-1.5">
                <i class="fa-solid fa-check-double"></i>
                <span>Luluskan Semua Terpilih</span>
            </button>
        </form>
    </div>

    <!-- Applications Master Data Table -->
    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-bold text-[11px]">
                        <th class="px-4 py-3.5 w-10 text-center">
                            <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                        </th>
                        <th class="px-4 py-3.5">No. Pendaftaran &amp; Tarikh</th>
                        <th class="px-4 py-3.5">Maklumat Peserta</th>
                        <th class="px-4 py-3.5">Kursus &amp; Lokasi</th>
                        <th class="px-4 py-3.5">Status Permohonan</th>
                        <th class="px-4 py-3.5">No. Sijil Digital</th>
                        <th class="px-4 py-3.5 text-right">Tindakan Kelulusan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($applications as $app)
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Checkbox -->
                            <td class="px-4 py-3.5 text-center">
                                <input type="checkbox" value="{{ $app->id }}" x-model="selectedApplicants" class="applicant-checkbox rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                            </td>

                            <!-- Registration No & Date -->
                            <td class="px-4 py-3.5">
                                <div class="font-mono font-bold text-cyan-800">{{ $app->registration_number }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    {{ $app->created_at ? $app->created_at->format('d/m/Y H:i') : '-' }}
                                </div>
                            </td>

                            <!-- Participant Info -->
                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-between gap-2">
                                    <a href="{{ route('kursus.pemohon.show', $app->id) }}" class="font-bold text-slate-900 text-sm hover:text-cyan-700 hover:underline flex items-center gap-1.5" title="Lihat Profil & Maklumat Penuh Pemohon">
                                        <i class="fa-solid fa-user-graduate text-cyan-600 text-xs"></i>
                                        <span>{{ $app->user->name ?? 'N/A' }}</span>
                                    </a>
                                    <a href="{{ route('kursus.pemohon.show', $app->id) }}" class="p-1 rounded-lg bg-cyan-50 hover:bg-cyan-100 text-cyan-700 text-[10px] font-bold transition shrink-0" title="Lihat Maklumat Pemohon">
                                        <i class="fa-solid fa-eye"></i> Profil
                                    </a>
                                </div>
                                <div class="text-[11px] text-slate-500 flex flex-wrap gap-2 mt-1">
                                    <span class="font-mono"><i class="fa-solid fa-id-card text-slate-400 mr-0.5"></i> {{ $app->user->ic_number ?? '-' }}</span>
                                    <span><i class="fa-solid fa-phone text-slate-400 mr-0.5"></i> {{ $app->user->phone ?? '-' }}</span>
                                </div>
                                <div class="mt-1 flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">
                                        {{ $app->user->role_label ?? $app->user->role }}
                                    </span>
                                    @if($app->user->jajahan)
                                        <span class="text-[10px] text-slate-400 font-medium">
                                            <i class="fa-solid fa-location-dot mr-0.5 text-slate-300"></i> {{ $app->user->jajahan }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Course & Location -->
                            <td class="px-4 py-3.5">
                                <a href="{{ route('kursus.show', $app->course_id) }}" class="font-bold text-slate-900 hover:text-cyan-700 transition">
                                    {{ $app->course->title ?? '-' }}
                                </a>
                                <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                                    {{ $app->course->code ?? '-' }} &bull; {{ $app->course->jajahan ?? '-' }}
                                </div>
                                <div class="text-[11px] text-slate-600 mt-0.5">
                                    <i class="fa-solid fa-calendar-day text-slate-400 mr-1"></i>
                                    {{ $app->course->start_date ? $app->course->start_date->format('d/m/Y') : '-' }}
                                </div>
                            </td>

                            <!-- Status Badge -->
                            <td class="px-4 py-3.5">
                                @if($app->status === 'Menunggu')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        <i class="fa-solid fa-clock-rotate-left mr-1"></i> Menunggu Kelulusan
                                    </span>
                                @elseif($app->status === 'Disahkan')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                        <i class="fa-solid fa-check mr-1"></i> Diluluskan
                                    </span>
                                @elseif($app->status === 'Hadir' || $app->status === 'Selesai')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <i class="fa-solid fa-award mr-1"></i> Hadir &amp; Selesai
                                    </span>
                                @elseif($app->status === 'Ditolak')
                                    <div>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                            <i class="fa-solid fa-xmark mr-1"></i> Ditolak
                                        </span>
                                        @if($app->rejection_reason)
                                            <div class="text-[10px] text-rose-600 mt-1 italic max-w-xs">
                                                Sebab: {{ $app->rejection_reason }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700">
                                        {{ $app->status }}
                                    </span>
                                @endif
                            </td>

                            <!-- Certificate No -->
                            <td class="px-4 py-3.5">
                                @if($app->certificate_number)
                                    <div class="font-mono font-bold text-amber-900 text-xs">{{ $app->certificate_number }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">Dikeluarkan: {{ $app->certificate_issued_at ? $app->certificate_issued_at->format('d/m/Y') : '-' }}</div>
                                @else
                                    <span class="text-slate-400 text-[11px] italic">Belum Dijana</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-3.5 text-right space-x-1 whitespace-nowrap">
                                @if($app->status === 'Menunggu')
                                    <!-- Approve Button -->
                                    <form action="{{ route('kursus.pemohon.lulus', $app->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-2xs transition" title="Luluskan Permohonan">
                                            <i class="fa-solid fa-check mr-1"></i> Lulus
                                        </button>
                                    </form>

                                    <!-- Reject Button (Modal Trigger) -->
                                    <button type="button" @click="openRejectModal({{ $app->id }}, '{{ addslashes($app->user->name ?? 'Peserta') }}')" class="px-2.5 py-1.5 rounded-lg bg-rose-100 hover:bg-rose-200 text-rose-800 text-xs font-bold transition" title="Tolak Permohonan">
                                        <i class="fa-solid fa-xmark mr-1"></i> Tolak
                                    </button>
                                @elseif($app->status === 'Disahkan')
                                    <!-- Mark Attendance & Issue Certificate -->
                                    <form action="{{ route('kursus.pemohon.hadir', $app->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-cyan-700 hover:bg-cyan-800 text-white text-xs font-bold shadow-2xs transition" title="Sahkan Kehadiran & Jana Sijil Digital">
                                            <i class="fa-solid fa-award mr-1"></i> Sahkan Kehadiran
                                        </button>
                                    </form>

                                    <!-- Reject Option -->
                                    <button type="button" @click="openRejectModal({{ $app->id }}, '{{ addslashes($app->user->name ?? 'Peserta') }}')" class="p-1.5 rounded-lg bg-slate-100 hover:bg-rose-100 text-slate-500 hover:text-rose-700 transition" title="Tukar ke Ditolak">
                                        <i class="fa-solid fa-ban"></i>
                                    </button>
                                @elseif($app->status === 'Hadir' || $app->status === 'Selesai')
                                    <!-- Print Digital Certificate -->
                                    <a href="{{ route('kursus.sijil', $app->id) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold shadow-2xs transition inline-flex items-center gap-1">
                                        <i class="fa-solid fa-print"></i>
                                        <span>Cetak Sijil</span>
                                    </a>
                                @elseif($app->status === 'Ditolak')
                                    <!-- Re-approve option -->
                                    <form action="{{ route('kursus.pemohon.lulus', $app->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-emerald-100 text-slate-700 hover:text-emerald-800 text-xs font-bold transition" title="Luluskan Semula">
                                            <i class="fa-solid fa-rotate-left mr-1"></i> Pulihkan &amp; Lulus
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 text-xs">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-xl mx-auto mb-2">
                                    <i class="fa-solid fa-user-slash"></i>
                                </div>
                                Tiada rekod permohonan peserta kursus dijumpai bagi penapis ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($applications->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $applications->links() }}
            </div>
        @endif
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
                Anda sedang menolak permohonan pendaftaran daripada <b class="text-slate-900" x-text="rejectApplicantName"></b>. Sila nyatakan sebab penolakan:
            </p>

            <form :action="'{{ url('/kursus/pemohon') }}/' + rejectAppId + '/tolak'" method="POST" class="space-y-3">
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
                    <button type="button" @click="rejectModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-md shadow-rose-600/30 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-ban"></i>
                        <span>Sahkan Tolak</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
