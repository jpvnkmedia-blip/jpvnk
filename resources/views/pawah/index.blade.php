@extends('layouts.app')

@section('title', 'Pengurusan Program Pawah Ternakan')
@section('page_title', 'Pengurusan Program Pawah Ternakan Negeri Kelantan')

@section('content')
<div class="space-y-6">

    <!-- Header Actions & Info -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">Surat Perjanjian &amp; Pemantauan Program Pawah</h2>
            <p class="text-xs text-slate-500 mt-0.5">Pengurusan skim pawah ternakan berhubung terus dengan pendaftaran lembu EPTR</p>
        </div>
        <div>
            @if(Auth::user()->isStaff())
                <a href="{{ route('pawah.create') }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-lg shadow-emerald-700/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-file-contract"></i>
                    <span>Daftar Perjanjian Pawah Baru</span>
                </a>
            @else
                <a href="{{ route('pawah.create') }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-lg shadow-emerald-700/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-handshake-angle"></i>
                    <span>Mohon Program Pawah Ternakan</span>
                </a>
            @endif
        </div>
    </div>

    <!-- KPI Metrics -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase">Perjanjian Aktif</div>
            <div class="text-2xl font-black text-emerald-800 mt-1">{{ $totalActive }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Sedang dipantau</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase">Menunggu Kelulusan</div>
            <div class="text-2xl font-black text-amber-700 mt-1">{{ $totalPending }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Permohonan baharu</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase">Kelahiran Anak Pawah</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $totalCalvesBorn }} Ekor</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Direkodkan di kandang</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase">Anak Dipulangkan</div>
            <div class="text-2xl font-black text-blue-800 mt-1">{{ $totalCalvesReturned }} Ekor</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Diagih ke pawah baru</div>
        </div>
    </div>

    <!-- Pawah Agreement Table with In-Column Search Filters -->
    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xs">
        <form action="{{ route('pawah.index') }}" method="GET" class="overflow-x-auto m-0 p-0">
            <table class="w-full text-left text-xs">
                <thead>
                    <!-- Header Titles -->
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-bold text-[11px]">
                        <th class="px-4 py-3.5 min-w-[160px]">No. Perjanjian Pawah</th>
                        <th class="px-4 py-3.5 min-w-[180px]">Nama Peserta / No. KP</th>
                        <th class="px-4 py-3.5 min-w-[180px]">Nama Skim & Jenis Pawah</th>
                        <th class="px-4 py-3.5 min-w-[140px]">Bil. Induk EPTR</th>
                        <th class="px-4 py-3.5 min-w-[150px]">Tempoh Kontrak</th>
                        <th class="px-4 py-3.5 min-w-[130px]">Status</th>
                        <th class="px-4 py-3.5 text-right min-w-[90px]">Tindakan</th>
                    </tr>
                    <!-- In-Column Filter Row -->
                    <tr class="bg-slate-100/80 border-b border-slate-200">
                        <!-- Col 1: No. Perjanjian & Jajahan -->
                        <th class="p-2">
                            <input type="text" name="no_perjanjian" value="{{ request('no_perjanjian') }}" placeholder="No. Perjanjian..." class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none font-mono font-normal">
                        </th>
                        <!-- Col 2: Peserta / No. KP -->
                        <th class="p-2">
                            @if(Auth::user()->isStaff())
                                <input type="text" name="no_kp" value="{{ request('no_kp') }}" placeholder="Nama / No KP / Tel..." class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none font-normal">
                            @else
                                <span class="text-[10px] text-slate-400 font-normal px-2 block">Rekod Anda</span>
                            @endif
                        </th>
                        <!-- Col 3: Jenis Pawah -->
                        <th class="p-2">
                            <select name="jenis_pawah" onchange="this.form.submit()" class="w-full px-2 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none font-normal">
                                <option value="">Semua Jenis Pawah</option>
                                @foreach($jenisPawahList ?? ['Lembu Hibrid', 'Kambing Tenusu', 'Rusa'] as $jp)
                                    <option value="{{ $jp }}" {{ request('jenis_pawah') == $jp ? 'selected' : '' }}>{{ $jp }}</option>
                                @endforeach
                            </select>
                        </th>
                        <!-- Col 4: Induk EPTR / Jajahan -->
                        <th class="p-2">
                            @if(Auth::user()->isStaff())
                                <select name="jajahan" onchange="this.form.submit()" class="w-full px-2 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none font-normal">
                                    <option value="">Semua Jajahan</option>
                                    @foreach($jajahanList ?? [] as $j)
                                        <option value="{{ $j }}" {{ request('jajahan') == $j ? 'selected' : '' }}>{{ $j }}</option>
                                    @endforeach
                                </select>
                            @else
                                <span class="text-[10px] text-slate-400 font-normal px-2 block">-</span>
                            @endif
                        </th>
                        <!-- Col 5: Tempoh Kontrak -->
                        <th class="p-2">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Kata Kunci..." class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none font-normal">
                        </th>
                        <!-- Col 6: Status -->
                        <th class="p-2">
                            <select name="status" onchange="this.form.submit()" class="w-full px-2 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none font-normal">
                                <option value="">Semua Status</option>
                                <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Menunggu Kelulusan" {{ request('status') == 'Menunggu Kelulusan' ? 'selected' : '' }}>Menunggu Kelulusan</option>
                                <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </th>
                        <!-- Col 7: Action Buttons -->
                        <th class="p-2 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs transition" title="Tapis Rekod">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                                @if(request()->hasAny(['no_perjanjian', 'no_kp', 'jenis_pawah', 'jajahan', 'search', 'status']))
                                    <a href="{{ route('pawah.index') }}" class="px-2 py-1.5 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs transition" title="Set Semula Penapis">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </a>
                                @endif
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($perjanjianList as $p)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 font-mono font-bold text-emerald-800">
                                <a href="{{ route('pawah.show', $p->id) }}" class="hover:underline flex items-center gap-1.5">
                                    <i class="fa-solid fa-file-contract text-emerald-600"></i>
                                    <span>{{ $p->no_perjanjian }}</span>
                                </a>
                                <div class="text-[10px] text-slate-400 font-sans">Jajahan: {{ $p->jajahan }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-slate-900">{{ $p->peserta->name ?? 'N/A' }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">{{ $p->peserta->ic_number ?? '-' }} &bull; {{ $p->peserta->phone ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-medium text-slate-800">{{ $p->nama_program }}</div>
                                <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-200">
                                        {{ $p->jenis_pawah ?? 'Lembu Hibrid' }}
                                    </span>
                                    @if($p->bilangan_ternakan_sedia_ada > 0 || $p->jenis_ternakan_sedia_ada)
                                    <span class="text-[10px] text-slate-500 font-medium">
                                        (Sedia Ada: {{ $p->bilangan_ternakan_sedia_ada ?? 0 }} ekor {{ $p->jenis_ternakan_sedia_ada ? $p->jenis_ternakan_sedia_ada : '' }})
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                @if($p->status === 'Menunggu Kelulusan')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                        {{ $p->bilangan_induk }} Ekor Dimohon
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300">
                                        {{ $p->ternakanList->count() }} Ekor Lembu EPTR
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-medium text-slate-800">{{ $p->tarikh_mula ? $p->tarikh_mula->format('d/m/Y') : '-' }} &rarr; {{ $p->tarikh_tamat ? $p->tarikh_tamat->format('d/m/Y') : '-' }}</div>
                                <div class="text-[10px] text-slate-400">Tempoh: {{ $p->tempoh_tahun }} Tahun</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $p->status === 'Aktif' ? 'bg-emerald-100 text-emerald-800' : ($p->status === 'Menunggu Kelulusan' ? 'bg-amber-100 text-amber-900 border border-amber-300' : ($p->status === 'Ditolak' ? 'bg-rose-100 text-rose-800' : ($p->status === 'Selesai' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-800'))) }}">
                                    {{ $p->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1">
                                <a href="{{ route('pawah.show', $p->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-emerald-100 text-slate-700 hover:text-emerald-800 font-bold transition" title="Lihat Perjanjian & Rekod">
                                    <i class="fa-solid fa-eye"></i> Semak
                                </a>
                                @if($p->status === 'Aktif' || $p->status === 'Selesai')
                                <a href="{{ route('pawah.cetak-perjanjian', $p->id) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-amber-100 hover:bg-amber-200 text-amber-900 font-bold transition" title="Cetak Surat Perjanjian Lembu Pawah">
                                    <i class="fa-solid fa-print"></i> Perjanjian
                                </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                Tiada rekod perjanjian program pawah dijumpai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </form>

        @if($perjanjianList->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $perjanjianList->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
