@extends('layouts.app')

@section('title', 'Maklumat Kursus - ' . $course->title)
@section('page_title', 'Maklumat Kursus & Senarai Peserta')

@section('content')
<div x-data="{
    rejectModalOpen: false,
    rejectAppId: null,
    rejectApplicantName: '',
    rejectReason: '',
    openRejectModal(id, name) {
        this.rejectAppId = id;
        this.rejectApplicantName = name;
        this.rejectReason = 'Kapasiti kursus telah penuh';
        this.rejectModalOpen = true;
    }
}" class="space-y-6">

    <!-- Top Action Toolbar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <a href="{{ route('kursus.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 bg-white px-3.5 py-2 rounded-xl border border-slate-200 transition">
                &larr; Senarai Kursus
            </a>
            @if(Auth::user()->canPublishCourse())
                <a href="{{ route('kursus.pemohon.index', ['course_id' => $course->id]) }}" class="text-xs font-bold text-cyan-800 hover:text-cyan-950 bg-cyan-50 px-3.5 py-2 rounded-xl border border-cyan-200 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-users-gear"></i>
                    <span>Hab Pemohon Kursus Ini</span>
                </a>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if(Auth::user()->canPublishCourse())
                <!-- Admin Edit & Actions -->
                <a href="{{ route('kursus.edit', $course->id) }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-800 font-bold text-xs shadow-2xs transition flex items-center gap-1.5">
                    <i class="fa-solid fa-pen-to-square text-cyan-700"></i>
                    <span>Kemaskini Kursus</span>
                </a>

                @if($course->status === 'Diarkibkan')
                    <span class="px-3.5 py-2 rounded-xl bg-purple-100 text-purple-900 border border-purple-200 font-bold text-xs flex items-center gap-1.5 shadow-2xs" title="Kursus telah diarkibkan. Rekod pemohon & sijil digital kekal terpelihara.">
                        <i class="fa-solid fa-box-archive text-purple-700"></i>
                        <span>Diarkibkan (Sijil &amp; Rekod Terpelihara)</span>
                    </span>
                @elseif($course->applications->count() > 0 || $course->status === 'Selesai')
                    <!-- Butang Arkibkan Kursus bagi Kursus yang ada peserta / Selesai -->
                    <form action="{{ route('kursus.arkib', $course->id) }}" method="POST" onsubmit="return confirm('Adakah anda pasti mahu mengarkibkan kursus ini? Maklumat kursus, senarai peserta dan sijil digital pemohon akan kekal terpelihara sepenuhnya dalam sistem.')">
                        @csrf
                        <button type="submit" class="px-3.5 py-2 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-800 font-bold text-xs border border-purple-200 transition flex items-center gap-1.5 shadow-2xs" title="Arkibkan kursus supaya sijil pemohon kekal terpelihara">
                            <i class="fa-solid fa-box-archive text-purple-700"></i>
                            <span>Arkibkan Kursus</span>
                        </button>
                    </form>
                @else
                    <!-- Padam Kursus jika tiada pemohon berdaftar -->
                    <form action="{{ route('kursus.destroy', $course->id) }}" method="POST" onsubmit="return confirm('Adakah anda pasti mahu memadam kursus ini daripada sistem?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition flex items-center gap-1.5">
                            <i class="fa-solid fa-trash-can"></i>
                            <span>Padam</span>
                        </button>
                    </form>
                @endif
            @endif

            @if(!$isApplied)
                @if($course->status === 'Buka')
                    <form action="{{ route('kursus.apply', $course->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-5 py-2 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-bold text-xs shadow-lg shadow-cyan-700/30 transition flex items-center gap-2">
                            <i class="fa-solid fa-user-plus"></i>
                            <span>Daftar Menyertai Kursus Ini</span>
                        </button>
                    </form>
                @else
                    <span class="px-3.5 py-2 rounded-xl bg-slate-100 text-slate-500 text-xs font-bold">
                        Pendaftaran Ditutup ({{ $course->status }})
                    </span>
                @endif
            @else
                <div class="flex items-center gap-2">
                    @if($isApplied->status === 'Menunggu')
                        <span class="px-3.5 py-2 rounded-xl bg-amber-100 text-amber-900 text-xs font-bold flex items-center gap-1.5">
                            <i class="fa-solid fa-clock-rotate-left"></i> Permohonan Anda Sedang Disemak
                        </span>
                    @elseif($isApplied->status === 'Disahkan')
                        <span class="px-3.5 py-2 rounded-xl bg-blue-100 text-blue-900 text-xs font-bold flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-check"></i> Permohonan Diluluskan (Tempat Disahkan)
                        </span>
                    @elseif($isApplied->status === 'Hadir' || $isApplied->status === 'Selesai')
                        <span class="px-3.5 py-2 rounded-xl bg-emerald-100 text-emerald-900 text-xs font-bold flex items-center gap-1.5">
                            <i class="fa-solid fa-award"></i> Kehadiran Sah (Tamat Kursus)
                        </span>
                        @if($isApplied->certificate_number)
                            <a href="{{ route('kursus.sijil', $isApplied->id) }}" target="_blank" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs shadow-md transition flex items-center gap-1.5">
                                <i class="fa-solid fa-print"></i> Cetak Sijil Digital
                            </a>
                        @endif
                    @elseif($isApplied->status === 'Ditolak')
                        <span class="px-3.5 py-2 rounded-xl bg-rose-100 text-rose-900 text-xs font-bold flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-xmark"></i> Permohonan Ditolak
                        </span>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Course Info Master Card -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 mb-6 border-b border-slate-100 gap-4">
            <div>
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-cyan-50 text-cyan-800 border border-cyan-200">
                    {{ $course->category }} &bull; {{ $course->code }}
                </span>
                <h2 class="text-2xl font-black text-slate-900 mt-2">{{ $course->title }}</h2>
                <p class="text-xs text-slate-500 mt-1">Tenaga Pengajar: <b>{{ $course->trainer_name }}</b></p>
            </div>
            <div class="text-right text-xs">
                <div class="text-slate-400 font-medium">Kapasiti Pendaftaran:</div>
                <div class="font-black text-slate-900 text-xl">{{ $course->registered_count }} / {{ $course->capacity }} Orang</div>
                <div class="mt-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $course->status === 'Buka' ? 'bg-emerald-100 text-emerald-800' : ($course->status === 'Sedang Berlangsung' ? 'bg-blue-100 text-blue-800' : ($course->status === 'Diarkibkan' ? 'bg-purple-100 text-purple-900 border border-purple-200' : ($course->status === 'Selesai' ? 'bg-teal-100 text-teal-800' : 'bg-slate-100 text-slate-800'))) }}">
                        <i class="fa-solid {{ $course->status === 'Diarkibkan' ? 'fa-box-archive mr-1' : '' }}"></i>
                        Status: {{ $course->status }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs">
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">JADUAL &amp; TARIKH</span>
                <div>Tarikh Mula: <b>{{ $course->start_date ? $course->start_date->format('d/m/Y') : '-' }}</b></div>
                <div>Tarikh Tamat: <b>{{ $course->end_date ? $course->end_date->format('d/m/Y') : '-' }}</b></div>
                <div>Masa: <b>{{ $course->time }}</b></div>
            </div>

            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">LOKASI &amp; YURAN</span>
                <div>Tempat: <b>{{ $course->location }}</b></div>
                <div>Jajahan: <b>{{ $course->jajahan }}</b></div>
                <div>Yuran: <b class="text-emerald-700">{{ $course->fee > 0 ? 'RM ' . number_format($course->fee, 2) : 'Percuma (Tajaan JPVNK)' }}</b></div>
            </div>

            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">SIJIL &amp; PERAKUAN</span>
                <div>Sijil Digital PDF dijana secara automatik dengan No. Sijil bersiri rasmi selepas permohonan disahkan dan kehadiran direkodkan.</div>
            </div>
        </div>

        <div class="mt-6 pt-4 border-t border-slate-100 text-xs">
            <span class="font-bold text-slate-800 uppercase block mb-1">Sinopsis &amp; Sukatan Kursus:</span>
            <p class="text-slate-600 leading-relaxed whitespace-pre-line">{{ $course->description }}</p>
        </div>
    </div>

    <!-- Participant List & Realtime Approval Management (Admin View) -->
    @if(Auth::user()->canPublishCourse())
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Senarai Peserta Berdaftar Bagi Kursus Ini ({{ $course->applications->count() }} Orang)</h3>
                    <p class="text-xs text-slate-500">Urus kelulusan pemohon, sahkan kehadiran peserta dan cetak sijil penyertaan digital</p>
                </div>
                <a href="{{ route('kursus.pemohon.index', ['course_id' => $course->id]) }}" class="text-xs font-bold text-cyan-700 hover:text-cyan-900">
                    Buka Hab Pengurusan Penuh &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-bold text-[11px]">
                            <th class="px-4 py-3">No. Daftar</th>
                            <th class="px-4 py-3">Nama Peserta</th>
                            <th class="px-4 py-3">No. Kad Pengenalan</th>
                            <th class="px-4 py-3">No. Telefon</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">No. Sijil Digital</th>
                            <th class="px-4 py-3 text-right">Tindakan Kelulusan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($course->applications as $app)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 font-mono font-bold text-cyan-800">{{ $app->registration_number }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-between gap-2">
                                        <a href="{{ route('kursus.pemohon.show', $app->id) }}" class="font-bold text-slate-900 hover:text-cyan-700 hover:underline flex items-center gap-1" title="Lihat Profil & Maklumat Pemohon">
                                            <i class="fa-solid fa-user-graduate text-cyan-600 text-xs"></i>
                                            <span>{{ $app->user->name ?? '-' }}</span>
                                        </a>
                                        <a href="{{ route('kursus.pemohon.show', $app->id) }}" class="p-1 rounded-lg bg-cyan-50 hover:bg-cyan-100 text-cyan-700 text-[10px] font-bold transition shrink-0" title="Lihat Maklumat Pemohon">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">{{ $app->user->email ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3 font-mono">{{ $app->user->ic_number ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $app->user->phone ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @if($app->status === 'Menunggu')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                            Menunggu
                                        </span>
                                    @elseif($app->status === 'Disahkan')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                            Diluluskan
                                        </span>
                                    @elseif($app->status === 'Hadir' || $app->status === 'Selesai')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            Hadir
                                        </span>
                                    @elseif($app->status === 'Ditolak')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700">
                                            {{ $app->status }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-mono font-semibold text-amber-900">
                                    {{ $app->certificate_number ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-right space-x-1 whitespace-nowrap">
                                    @if($app->status === 'Menunggu')
                                        <form action="{{ route('kursus.pemohon.lulus', $app->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-2xs transition" title="Luluskan">
                                                <i class="fa-solid fa-check mr-1"></i> Lulus
                                            </button>
                                        </form>
                                        <button type="button" @click="openRejectModal({{ $app->id }}, '{{ addslashes($app->user->name ?? 'Peserta') }}')" class="px-2.5 py-1 rounded-lg bg-rose-100 hover:bg-rose-200 text-rose-800 text-xs font-bold transition" title="Tolak">
                                            <i class="fa-solid fa-xmark mr-1"></i> Tolak
                                        </button>
                                    @elseif($app->status === 'Disahkan')
                                        <form action="{{ route('kursus.pemohon.hadir', $app->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-cyan-700 hover:bg-cyan-800 text-white text-xs font-bold shadow-2xs transition" title="Sahkan Kehadiran & Jana Sijil">
                                                <i class="fa-solid fa-award mr-1"></i> Sahkan Kehadiran
                                            </button>
                                        </form>
                                    @elseif($app->status === 'Hadir' || $app->status === 'Selesai')
                                        <a href="{{ route('kursus.sijil', $app->id) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs shadow-2xs transition inline-flex items-center gap-1" title="Cetak Sijil">
                                            <i class="fa-solid fa-award"></i> Sijil
                                        </a>
                                    @elseif($app->status === 'Ditolak')
                                        <form action="{{ route('kursus.pemohon.lulus', $app->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-emerald-100 text-slate-700 hover:text-emerald-800 text-xs font-bold transition" title="Luluskan Semula">
                                                <i class="fa-solid fa-rotate-left mr-0.5"></i> Pulihkan
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-slate-400">Belum ada peserta mendaftar bagi kursus ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

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
                Anda sedang menolak permohonan pendaftaran daripada <b class="text-slate-900" x-text="rejectApplicantName"></b>:
            </p>

            <form :action="'{{ url('/kursus/pemohon') }}/' + rejectAppId + '/tolak'" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Catatan Sebab Penolakan</label>
                    <textarea name="rejection_reason" x-model="rejectReason" rows="3" required class="w-full rounded-xl border border-slate-200 p-3 focus:ring-2 focus:ring-rose-500 focus:outline-none placeholder:text-slate-400"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="rejectModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold shadow-md transition flex items-center gap-1.5">
                        <i class="fa-solid fa-ban"></i>
                        <span>Sahkan Tolak</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

