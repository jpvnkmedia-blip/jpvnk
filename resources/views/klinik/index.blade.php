@extends('layouts.app')

@section('title', 'Klinik Haiwan & Perkhidmatan Rawatan')
@section('page_title', 'Perkhidmatan Rawatan & Klinik Haiwan Veterinar')

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">Perkhidmatan Klinik Veterinar & Temujanji</h2>
            <p class="text-xs text-slate-500 mt-0.5">Pemeriksaan kesihatan, diagnosis, rawatan penyakit, vaksinasi, pembedahan kembiri dan rawatan kecemasan</p>
        </div>
        <div class="flex items-center gap-2.5">
            @if(Auth::user()->isStaff() && Auth::user()->canAccessKlinik())
            <a href="{{ route('klinik.permohonan_ubat.index') }}" class="px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 text-rose-700 border border-rose-200 text-xs font-bold shadow-xs transition flex items-center gap-2">
                <i class="fa-solid fa-pills text-rose-600"></i>
                <span>Permohonan Ubat &amp; Farmasi</span>
            </a>
            @endif
            <a href="{{ route('klinik.create') }}" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-lg shadow-rose-700/30 transition flex items-center gap-2">
                <i class="fa-solid fa-calendar-plus"></i>
                <span>Daftar Temujanji Rawatan</span>
            </a>
        </div>
    </div>

    <!-- KPI Summary Pills -->
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase">Jumlah Temujanji</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $totalTemujanji }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Keseluruhan rekod</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase">Rawatan Selesai</div>
            <div class="text-2xl font-black text-emerald-800 mt-1">{{ $totalSelesai }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Pesakit telah dirawat</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs col-span-2 sm:col-span-1">
            <div class="text-[11px] font-bold text-slate-500 uppercase">Rekod Kad Rawatan</div>
            <div class="text-2xl font-black text-rose-800 mt-1">{{ $totalRawatan }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Kad perubatan tersimpan</div>
        </div>
    </div>

    <!-- Table of Clinic Appointments with In-Column Search Filters -->
    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xs">
        <form action="{{ route('klinik.index') }}" method="GET" class="overflow-x-auto m-0 p-0">
            <table class="w-full text-left text-xs">
                <thead>
                    <!-- Header Titles -->
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-bold text-[11px]">
                        <th class="px-4 py-3.5 min-w-[150px]">No. Temujanji</th>
                        <th class="px-4 py-3.5 min-w-[190px]">Pemilik / No. KP</th>
                        <th class="px-4 py-3.5 min-w-[160px]">Jenis Haiwan & Nama</th>
                        <th class="px-4 py-3.5 min-w-[200px]">Tujuan / Simptom</th>
                        <th class="px-4 py-3.5 min-w-[170px]">Tarikh & Klinik Jajahan</th>
                        <th class="px-4 py-3.5 min-w-[130px]">Status</th>
                        <th class="px-4 py-3.5 text-right min-w-[90px]">Tindakan</th>
                    </tr>
                    <!-- In-Column Filter Row -->
                    <tr class="bg-slate-100/80 border-b border-slate-200">
                        <!-- Col 1: No. Temujanji -->
                        <th class="p-2">
                            <input type="text" name="no_temujanji" value="{{ request('no_temujanji') }}" placeholder="Cari No. TJ..." class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-rose-500 focus:outline-none font-mono font-normal">
                        </th>
                        <!-- Col 2: Pemilik / No KP -->
                        <th class="p-2">
                            @if(Auth::user()->isStaff())
                                <input type="text" name="no_kp" value="{{ request('no_kp') }}" placeholder="Cari Nama / No KP / Tel..." class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-rose-500 focus:outline-none font-normal">
                            @else
                                <span class="text-[10px] text-slate-400 font-normal px-2 block">Rekod Anda</span>
                            @endif
                        </th>
                        <!-- Col 3: Jenis Haiwan -->
                        <th class="p-2">
                            <select name="jenis_haiwan" onchange="this.form.submit()" class="w-full px-2 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-rose-500 focus:outline-none font-normal">
                                <option value="">Semua Haiwan</option>
                                @foreach($jenisHaiwanList ?? ['Kucing', 'Anjing', 'Lembu', 'Kambing', 'Biri-biri', 'Kuda', 'Unggas / Burung', 'Arnab', 'Lain-lain'] as $jh)
                                    <option value="{{ $jh }}" {{ request('jenis_haiwan') == $jh ? 'selected' : '' }}>{{ $jh }}</option>
                                @endforeach
                            </select>
                        </th>
                        <!-- Col 4: Simptom / Tujuan -->
                        <th class="p-2">
                            <input type="text" name="simptom" value="{{ request('simptom') }}" placeholder="Cari Simptom / Tujuan..." class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-rose-500 focus:outline-none font-normal">
                        </th>
                        <!-- Col 5: Tarikh & Klinik -->
                        <th class="p-2">
                            @if(Auth::user()->isStaff())
                                <select name="klinik_jajahan" onchange="this.form.submit()" class="w-full px-2 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-rose-500 focus:outline-none font-normal">
                                    <option value="">Semua Klinik</option>
                                    @foreach($klinikList ?? [] as $k)
                                        <option value="{{ $k }}" {{ request('klinik_jajahan') == $k ? 'selected' : '' }}>{{ str_replace(['Pusat Veterinar Jajahan ', 'Klinik Haiwan Ibu Pejabat JPVNK '], ['', 'HQ '], $k) }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="date" name="tarikh" value="{{ request('tarikh') }}" onchange="this.form.submit()" class="w-full px-2 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-rose-500 focus:outline-none font-normal">
                            @endif
                        </th>
                        <!-- Col 6: Status -->
                        <th class="p-2">
                            <select name="status" onchange="this.form.submit()" class="w-full px-2 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-rose-500 focus:outline-none font-normal">
                                <option value="">Semua Status</option>
                                <option value="Menunggu" {{ request('status') == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                                <option value="Disahkan" {{ request('status') == 'Disahkan' ? 'selected' : '' }}>Disahkan</option>
                                <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="Dibatalkan" {{ request('status') == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </th>
                        <!-- Col 7: Action Buttons -->
                        <th class="p-2 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs transition" title="Tapis Rekod">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                                @if(request()->hasAny(['no_temujanji', 'no_kp', 'search', 'jenis_haiwan', 'simptom', 'klinik_jajahan', 'tarikh', 'status']))
                                    <a href="{{ route('klinik.index') }}" class="px-2 py-1.5 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs transition" title="Set Semula Penapis">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </a>
                                @endif
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($temujanjiList as $tj)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 font-mono font-bold text-rose-900">
                                <a href="{{ route('klinik.show', $tj->id) }}" class="hover:underline flex items-center gap-1.5">
                                    <i class="fa-solid fa-notes-medical text-rose-600"></i>
                                    <span>{{ $tj->no_temujanji }}</span>
                                </a>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-slate-900">{{ $tj->pemilik->name ?? '-' }}</div>
                                <div class="text-[11px] text-slate-500 font-mono flex items-center gap-1 mt-0.5">
                                    @if(!empty($tj->pemilik->ic_number))
                                        <span class="text-rose-900 font-semibold bg-rose-50 px-1.5 py-0.5 rounded border border-rose-100">{{ $tj->pemilik->ic_number }}</span>
                                    @endif
                                    <span>{{ $tj->pemilik->phone ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-slate-800">{{ $tj->jenis_haiwan }} @if($tj->nama_haiwan) ({{ $tj->nama_haiwan }}) @endif</div>
                                <div class="text-[11px] text-slate-500">{{ $tj->baka ?? 'Baka Tempatan' }} &bull; {{ $tj->jantina_haiwan }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-medium text-slate-800 line-clamp-1">{{ $tj->simptom_atau_tujuan }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-slate-900">{{ $tj->tarikh_temujanji ? $tj->tarikh_temujanji->format('d/m/Y') : '-' }}</div>
                                <div class="text-[11px] text-slate-500 truncate">{{ $tj->klinik_jajahan }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $tj->status === 'Selesai' ? 'bg-emerald-100 text-emerald-800' : ($tj->status === 'Disahkan' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ $tj->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1">
                                <a href="{{ route('klinik.show', $tj->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                                    <i class="fa-solid fa-eye"></i> Butiran
                                </a>
                                @if(Auth::user()->isStaff() && $tj->status !== 'Selesai')
                                    <a href="{{ route('klinik.rawatan.create', $tj->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-rose-100 hover:bg-rose-200 text-rose-900 font-bold transition">
                                        <i class="fa-solid fa-stethoscope"></i> Rawat
                                    </a>
                                @endif
                                @if($tj->rawatan)
                                    <a href="{{ route('klinik.cetak-kad-rawatan', $tj->id) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-amber-100 hover:bg-amber-200 text-amber-900 font-bold transition" title="Cetak Kad Rawatan">
                                        <i class="fa-solid fa-print"></i> Kad
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                Tiada rekod temujanji klinik haiwan dijumpai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </form>

        @if($temujanjiList->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $temujanjiList->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
