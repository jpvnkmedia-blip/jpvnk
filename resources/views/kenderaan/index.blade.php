@extends('layouts.app')

@section('title', 'Permohonan Kenderaan & Logistik Jabatan')
@section('page_title', 'Pengurusan Logistik & Tempahan Kenderaan Jabatan')

@section('content')
<div class="space-y-6" x-data="{ modalLulus: false, modalTolak: false, selectedId: null, selectedNo: '', selectedDest: '', selectedKenderaanId: '' }">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">Tempahan Kenderaan & Fleet Jabatan</h2>
            <p class="text-xs text-slate-500 mt-0.5">Pengurusan kenderaan operasi 4x4, lori angkut ternakan, van penguatkuasaan dan kereta rasmi jabatan</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if(Auth::user()->canManageKenderaanFleet())
                <a href="{{ route('kenderaan.fleet') }}" class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-2xs flex items-center gap-1.5">
                    <i class="fa-solid fa-truck-pickup text-teal-600"></i>
                    <span>Pengurusan Fleet ({{ $totalKenderaan ?? 0 }})</span>
                </a>
                <a href="{{ route('kenderaan.pemandu.index') }}" class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-2xs flex items-center gap-1.5">
                    <i class="fa-solid fa-id-card-clip text-teal-600"></i>
                    <span>Maklumat Pemandu ({{ $totalPemandu ?? 0 }})</span>
                </a>
            @endif
            @if(Auth::user()->canBookVehicle())
                <a href="{{ route('kenderaan.create') }}" class="px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-lg shadow-teal-700/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-calendar-plus"></i>
                    <span>Permohonan Tempahan</span>
                </a>
            @endif
        </div>
    </div>

    @if(Auth::user()->canManageKenderaanFleet())
    <!-- Vehicles Fleet Header Bar -->
    <div class="flex items-center justify-between pt-1">
        <div class="flex items-center gap-2">
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Status Fleet Kenderaan Jabatan</h3>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-50 text-teal-800 border border-teal-200">{{ $totalSedia ?? 0 }} Sedia</span>
        </div>
        <a href="{{ route('kenderaan.fleet') }}" class="text-xs font-bold text-teal-700 hover:text-teal-900 flex items-center gap-1">
            <span>Urus & Tambah Kenderaan</span>
            <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>

    <!-- Vehicles Fleet Status Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach(($fleet ?? $kenderaanList ?? []) as $k)
            <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs space-y-2">
                <div class="flex items-center justify-between">
                    <span class="font-mono font-black text-sm text-teal-900">{{ $k->no_pendaftaran }}</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $k->status === 'Sedia' ? 'bg-emerald-100 text-emerald-800' : ($k->status === 'Sedang Digunakan' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                        {{ $k->status }}
                    </span>
                </div>
                <div>
                    <div class="font-bold text-slate-900 text-sm">{{ $k->model }}</div>
                    <div class="text-[11px] text-slate-500">{{ $k->jenis_kenderaan ?? $k->jenis ?? 'Kenderaan Jabatan' }} &bull; Kapasiti {{ $k->kapasiti_penumpang }} Orang</div>
                </div>
                <div class="pt-2 border-t border-slate-100 flex justify-between text-[11px] text-slate-500">
                    <span>Odometer: {{ number_format($k->odometer_semasa_km ?? 0) }} km</span>
                    <span class="font-semibold text-slate-700">{{ $k->jajahan_penempatan }}</span>
                </div>
            </div>
        @endforeach
    </div>
    @endif

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs flex flex-col md:flex-row md:items-center justify-between gap-3">
        <!-- Status Filter Pills -->
        <div class="flex flex-wrap items-center gap-1.5 text-xs font-bold">
            <a href="{{ route('kenderaan.index') }}" class="px-3 py-1.5 rounded-xl transition {{ !request()->filled('status') ? 'bg-teal-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua
            </a>
            <a href="{{ route('kenderaan.index', ['status' => 'Menunggu']) }}" class="px-3 py-1.5 rounded-xl transition flex items-center gap-1.5 {{ request('status') === 'Menunggu' ? 'bg-amber-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <span>Menunggu Kelulusan</span>
                @if(($totalTempahanMenunggu ?? 0) > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ request('status') === 'Menunggu' ? 'bg-amber-800 text-white' : 'bg-amber-200 text-amber-900' }}">{{ $totalTempahanMenunggu }}</span>
                @endif
            </a>
            <a href="{{ route('kenderaan.index', ['status' => 'Diluluskan']) }}" class="px-3 py-1.5 rounded-xl transition flex items-center gap-1.5 {{ request('status') === 'Diluluskan' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <span>Diluluskan</span>
                @if(($totalTempahanDiluluskan ?? 0) > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ request('status') === 'Diluluskan' ? 'bg-emerald-800 text-white' : 'bg-emerald-200 text-emerald-900' }}">{{ $totalTempahanDiluluskan }}</span>
                @endif
            </a>
            <a href="{{ route('kenderaan.index', ['status' => 'Ditolak']) }}" class="px-3 py-1.5 rounded-xl transition flex items-center gap-1.5 {{ request('status') === 'Ditolak' ? 'bg-rose-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <span>Ditolak</span>
                @if(($totalTempahanDitolak ?? 0) > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ request('status') === 'Ditolak' ? 'bg-rose-800 text-white' : 'bg-rose-200 text-rose-900' }}">{{ $totalTempahanDitolak }}</span>
                @endif
            </a>
            <a href="{{ route('kenderaan.index', ['status' => 'Selesai']) }}" class="px-3 py-1.5 rounded-xl transition flex items-center gap-1.5 {{ request('status') === 'Selesai' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <span>Selesai</span>
                @if(($totalTempahanSelesai ?? 0) > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ request('status') === 'Selesai' ? 'bg-blue-800 text-white' : 'bg-blue-200 text-blue-900' }}">{{ $totalTempahanSelesai }}</span>
                @endif
            </a>
        </div>

        <!-- Search input -->
        <form action="{{ route('kenderaan.index') }}" method="GET" class="flex items-center gap-2">
            @if(request()->filled('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="relative">
                <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pemohon / destinasi / no..." class="pl-8 pr-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none w-56">
            </div>
            <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition">Cari</button>
        </form>
    </div>

    <!-- Bookings Table -->
    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Senarai Permohonan & Rekod Tempahan Kenderaan</h3>
            <span class="text-xs text-slate-500">Jumlah: <b>{{ $tempahanList->total() }}</b> permohonan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-bold text-[11px]">
                        <th class="px-5 py-3.5">No. Permohonan</th>
                        <th class="px-4 py-3.5">Pemohon</th>
                        <th class="px-4 py-3.5">Kenderaan & Pemandu</th>
                        <th class="px-4 py-3.5">Destinasi & Tujuan</th>
                        <th class="px-4 py-3.5">Tarikh Perjalanan</th>
                        <th class="px-4 py-3.5">Status & Pelulus</th>
                        <th class="px-5 py-3.5 text-right">Tindakan Pegawai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tempahanList as $t)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 font-mono font-bold text-teal-900">
                                <a href="{{ route('kenderaan.show', $t->id) }}" class="hover:underline">
                                    {{ $t->no_tempahan ?? $t->no_permohonan ?? 'KND-'.$t->id }}
                                </a>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-slate-900">{{ $t->pemohon->name ?? '-' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $t->pemohon->role_label ?? '-' }} &bull; {{ $t->bilangan_penumpang }} Penumpang</div>
                            </td>
                            <td class="px-4 py-3.5">
                                @if($t->kenderaan)
                                    <div class="font-mono font-bold text-slate-900">{{ $t->kenderaan->no_pendaftaran }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $t->kenderaan->model }}</div>
                                @else
                                    <span class="text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded text-[10px] font-bold">
                                        Belum Ditugaskan
                                    </span>
                                @endif
                                @if($t->pemandu_nama)
                                    <div class="text-[10px] text-teal-700 mt-0.5"><i class="fa-solid fa-id-badge mr-1"></i>{{ $t->pemandu_nama }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-slate-800">{{ $t->destinasi }}</div>
                                <div class="text-[11px] text-slate-500 line-clamp-1">{{ $t->tujuan_perjalanan ?? $t->tujuan ?? 'Urusan Rasmi' }}</div>
                            </td>
                            <td class="px-4 py-3.5 font-medium">
                                <div>{{ $t->tarikh_mula ? \Carbon\Carbon::parse($t->tarikh_mula)->format('d/m/Y') : ($t->tarikh_keluar ? \Carbon\Carbon::parse($t->tarikh_keluar)->format('d/m/Y') : '-') }}</div>
                                <div class="text-[10px] text-slate-400">hingga {{ $t->tarikh_tamat ? \Carbon\Carbon::parse($t->tarikh_tamat)->format('d/m/Y') : ($t->tarikh_kembali ? \Carbon\Carbon::parse($t->tarikh_kembali)->format('d/m/Y') : '-') }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $t->status === 'Diluluskan' ? 'bg-emerald-100 text-emerald-800' : ($t->status === 'Selesai' ? 'bg-blue-100 text-blue-800' : ($t->status === 'Ditolak' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800')) }}">
                                    {{ $t->status }}
                                </span>
                                @if($t->pelulus)
                                    <div class="text-[10px] text-slate-500 mt-0.5">Oleh: <span class="font-semibold">{{ $t->pelulus->name }}</span></div>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1 whitespace-nowrap">
                                @if(in_array($t->status, ['Menunggu', 'Menunggu Kelulusan']) && Auth::user()->canManageKenderaanFleet())
                                    <!-- Butang Luluskan Pantas -->
                                    <button type="button" @click="selectedId = {{ $t->id }}; selectedNo = '{{ $t->no_tempahan ?? 'KND-'.$t->id }}'; selectedDest = '{{ addslashes($t->destinasi) }}'; selectedKenderaanId = '{{ $t->kenderaan_id ?? '' }}'; modalLulus = true;" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs transition" title="Luluskan Tempahan Ini">
                                        <i class="fa-solid fa-circle-check"></i> Lulus
                                    </button>
                                    
                                    <!-- Butang Tolak Pantas -->
                                    <button type="button" @click="selectedId = {{ $t->id }}; selectedNo = '{{ $t->no_tempahan ?? 'KND-'.$t->id }}'; selectedDest = '{{ addslashes($t->destinasi) }}'; modalTolak = true;" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs transition" title="Tolak Permohonan Ini">
                                        <i class="fa-solid fa-circle-xmark"></i> Tolak
                                    </button>
                                @endif

                                <a href="{{ route('kenderaan.show', $t->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-teal-100 text-slate-700 hover:text-teal-800 font-bold transition">
                                    <i class="fa-solid fa-eye"></i> Butiran
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                Tiada rekod tempahan kenderaan ditemui.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tempahanList->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $tempahanList->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL: Kelulusan Permohonan Kenderaan -->
    <div x-show="modalLulus" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl text-xs space-y-4" @click.outside="modalLulus = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Kelulusan Permohonan Kenderaan</h3>
                        <p class="text-[11px] text-slate-500 font-mono" x-text="selectedNo"></p>
                    </div>
                </div>
                <button @click="modalLulus = false" class="text-slate-400 hover:text-slate-600 text-base">&times;</button>
            </div>

            <form :action="'{{ url('/kenderaan/tempahan') }}/' + selectedId + '/kelulusan'" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="status" value="Diluluskan">

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Pilih Kenderaan Ditugaskan <span class="text-rose-500">*</span></label>
                    <select name="kenderaan_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="">-- Pilih Kenderaan Tersedia --</option>
                        @foreach(($availableKenderaan ?? $fleet ?? []) as $v)
                            <option value="{{ $v->id }}">
                                {{ $v->no_pendaftaran }} - {{ $v->model }} ({{ $v->jenis_kenderaan ?? $v->jenis ?? 'Kenderaan' }}) &bull; {{ $v->status }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Pilih Pemandu Ditugaskan <span class="text-rose-500">*</span></label>
                    <select name="pemandu_nama" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="Pemandu Khas Jabatan">-- Pemandu Khas Jabatan --</option>
                        @foreach(($pemanduList ?? []) as $d)
                            <option value="{{ $d->nama }} ({{ $d->no_telefon }}) - Lesen {{ $d->kelas_lesen }}">
                                {{ $d->nama }} &bull; {{ $d->no_telefon }} (Lesen: {{ $d->kelas_lesen }}) - {{ $d->status }}
                            </option>
                        @endforeach
                        <option value="Pemandu Sendiri (Pemohon)">Pemandu Sendiri (Pemohon)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Catatan Kelulusan / Arahan Perjalanan</label>
                    <textarea name="catatan_kelulusan" rows="2" placeholder="Catatan atau syarat perjalanan rasmi..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none font-medium">Permohonan tempahan diluluskan untuk urusan rasmi jabatan.</textarea>
                </div>

                <div class="p-3 bg-emerald-50 rounded-2xl border border-emerald-200 text-emerald-900 text-[11px] flex items-center gap-2">
                    <i class="fa-solid fa-user-shield text-base text-emerald-600"></i>
                    <div>Pegawai Pelulus: <b>{{ Auth::user()->name }}</b> ({{ Auth::user()->role_label }})</div>
                </div>

                <div class="pt-3 flex justify-end gap-2">
                    <button type="button" @click="modalLulus = false" class="px-4 py-2 rounded-xl bg-slate-100 font-bold text-slate-600 hover:bg-slate-200 transition">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-md transition flex items-center gap-1.5">
                        <i class="fa-solid fa-check-double"></i> Sahkan & Luluskan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: Penolakan Permohonan Kenderaan -->
    <div x-show="modalTolak" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl text-xs space-y-4" @click.outside="modalTolak = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Penolakan Permohonan Kenderaan</h3>
                        <p class="text-[11px] text-slate-500 font-mono" x-text="selectedNo"></p>
                    </div>
                </div>
                <button @click="modalTolak = false" class="text-slate-400 hover:text-slate-600 text-base">&times;</button>
            </div>

            <form :action="'{{ url('/kenderaan/tempahan') }}/' + selectedId + '/tolak'" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Sebab / Alasan Penolakan <span class="text-rose-500">*</span></label>
                    <textarea name="sebab_tolak" rows="3" required placeholder="Nyatakan sebab permohonan tidak diluluskan (cth: Tiada kenderaan tersedia pada tarikh tersebut / Bertindih dengan operasi lain)..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500 focus:outline-none font-medium"></textarea>
                </div>

                <div class="p-3 bg-rose-50 rounded-2xl border border-rose-200 text-rose-900 text-[11px] flex items-center gap-2">
                    <i class="fa-solid fa-user-xmark text-base text-rose-600"></i>
                    <div>Pegawai Menolak: <b>{{ Auth::user()->name }}</b> ({{ Auth::user()->role_label }})</div>
                </div>

                <div class="pt-3 flex justify-end gap-2">
                    <button type="button" @click="modalTolak = false" class="px-4 py-2 rounded-xl bg-slate-100 font-bold text-slate-600 hover:bg-slate-200 transition">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold shadow-md transition flex items-center gap-1.5">
                        <i class="fa-solid fa-ban"></i> Sahkan Penolakan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

