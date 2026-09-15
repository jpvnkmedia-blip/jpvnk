@extends('layouts.app')

@section('title', 'Program Kesihatan Ternakan - JPVNK')
@section('page_title', 'Program Kesihatan & Imunisasi Ternakan Ruminan')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-teal-900 via-slate-900 to-emerald-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border border-teal-800/40">
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-teal-500/20 border border-teal-500/40 text-teal-300 text-xs font-bold uppercase tracking-wider mb-2">
                <i class="fa-solid fa-heart-pulse"></i> Program Veterinar & Kawalan Penyakit
            </span>
            <h2 class="text-2xl sm:text-3xl font-black">Program Kesihatan & Vaksinasi Ternakan</h2>
            <p class="text-xs sm:text-sm text-teal-100/80 mt-1 max-w-2xl">
                Pengurusan dan pemantauan menyeluruh aktiviti vaksinasi (FMD, Hawar Berdarah), kawalan parasit (Deworming), rawatan klinikal, dan surveilans penyakit ternakan Negeri Kelantan.
            </p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('eptr.kesihatan.create') }}" class="px-5 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-black text-xs shadow-lg transition flex items-center gap-2">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Rekod Rawatan / Vaksinasi Baru</span>
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 sm:gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 text-xs mb-1">
                <span>Jumlah Rawatan</span>
                <i class="fa-solid fa-notes-medical text-teal-600"></i>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ $totalRawatan }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Keseluruhan rekod kesihatan</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 text-xs mb-1">
                <span>Vaksinasi / Imunisasi</span>
                <i class="fa-solid fa-syringe text-emerald-600"></i>
            </div>
            <div class="text-2xl font-black text-emerald-800">{{ $totalVaksinasi }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">FMD, HS, Anthrax, PPR</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 text-xs mb-1">
                <span>Penyahcacingan</span>
                <i class="fa-solid fa-pills text-blue-600"></i>
            </div>
            <div class="text-2xl font-black text-blue-800">{{ $totalDeworming }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Kawalan parasit & cacing</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 text-xs mb-1">
                <span>Rawatan Klinikal</span>
                <i class="fa-solid fa-stethoscope text-amber-600"></i>
            </div>
            <div class="text-2xl font-black text-amber-800">{{ $totalRawatanKlinikal }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Penyakit, luka & kecederaan</div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between text-slate-500 text-xs mb-1">
                <span>Surveilans Penyakit</span>
                <i class="fa-solid fa-vial-virus text-purple-600"></i>
            </div>
            <div class="text-2xl font-black text-purple-800">{{ $totalSurveilans }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Saringan Brucella & TB</div>
        </div>
    </div>

    <!-- Upcoming Booster Alert List (If Any) -->
    @if($boosterAkanDatang->count() > 0)
        <div class="p-4 bg-amber-50 rounded-2xl border border-amber-200 text-amber-900 space-y-2">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 font-bold text-xs sm:text-sm">
                    <i class="fa-solid fa-bell text-amber-600"></i>
                    <span>Peringatan Temujanji Dos Ulangan / Booster Akan Datang (30 Hari)</span>
                </div>
                <span class="px-2 py-0.5 bg-amber-200 text-amber-900 rounded-full text-[10px] font-black">
                    {{ $boosterAkanDatang->count() }} Temujanji
                </span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-1 text-xs">
                @foreach($boosterAkanDatang as $b)
                    <div class="bg-white p-2.5 rounded-xl border border-amber-200 flex items-center justify-between">
                        <div>
                            <div class="font-bold text-slate-900">Tag: {{ $b->ternakan->no_tag ?? 'ID-'.$b->ternakan_id }}</div>
                            <div class="text-[11px] text-slate-500">{{ $b->nama_vaksin_atau_ubat }}</div>
                        </div>
                        <div class="text-right font-mono font-bold text-amber-800 text-[11px]">
                            {{ $b->tarikh_ulangan_dos->format('d/m/Y') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Search & Filter Bar -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-xs space-y-4">
        <form method="GET" action="{{ route('eptr.kesihatan.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div>
                <label class="block font-bold text-slate-600 mb-1">Carian Rekod / Tag</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="No Tag / No Rujukan / Vaksin" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none">
            </div>

            <div>
                <label class="block font-bold text-slate-600 mb-1">Jenis Program</label>
                <select name="jenis_program" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none">
                    <option value="">-- Semua Program --</option>
                    <option value="Vaksinasi / Imunisasi" {{ request('jenis_program') == 'Vaksinasi / Imunisasi' ? 'selected' : '' }}>Vaksinasi / Imunisasi</option>
                    <option value="Penyahcacingan (Deworming)" {{ request('jenis_program') == 'Penyahcacingan (Deworming)' ? 'selected' : '' }}>Penyahcacingan (Deworming)</option>
                    <option value="Rawatan Penyakit / Klinikal" {{ request('jenis_program') == 'Rawatan Penyakit / Klinikal' ? 'selected' : '' }}>Rawatan Penyakit / Klinikal</option>
                    <option value="Ujian Saringan Penyakit & Surveilans" {{ request('jenis_program') == 'Ujian Saringan Penyakit & Surveilans' ? 'selected' : '' }}>Ujian Saringan & Surveilans</option>
                    <option value="Pemberian Vitamin & Suplemen" {{ request('jenis_program') == 'Pemberian Vitamin & Suplemen' ? 'selected' : '' }}>Pemberian Vitamin & Suplemen</option>
                    <option value="Pemeriksaan Kesihatan Berkala" {{ request('jenis_program') == 'Pemeriksaan Kesihatan Berkala' ? 'selected' : '' }}>Pemeriksaan Berkala</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-600 mb-1">Jajahan</label>
                <select name="jajahan" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none">
                    <option value="">-- Semua Jajahan --</option>
                    @foreach($jajahanList as $j)
                        <option value="{{ $j }}" {{ request('jajahan') == $j ? 'selected' : '' }}>{{ $j }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl transition">
                    <i class="fa-solid fa-filter mr-1"></i> Tapis
                </button>
                <a href="{{ route('eptr.kesihatan.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
                    Reset
                </a>
            </div>
        </form>

        <!-- Records Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-bold text-[11px]">
                        <th class="px-4 py-3.5">No Rujukan</th>
                        <th class="px-4 py-3.5">Ternakan & Pemunya</th>
                        <th class="px-4 py-3.5">Jenis Program</th>
                        <th class="px-4 py-3.5">Ubat / Vaksin & Dos</th>
                        <th class="px-4 py-3.5">Tarikh Rawatan</th>
                        <th class="px-4 py-3.5">Dos Ulangan</th>
                        <th class="px-4 py-3.5">Status Kesihatan</th>
                        <th class="px-4 py-3.5 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rekodList as $r)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3.5 font-mono font-bold text-teal-800">
                                <a href="{{ route('eptr.kesihatan.show', $r->id) }}" class="hover:underline flex items-center gap-1.5">
                                    <i class="fa-solid fa-file-medical text-teal-600"></i>
                                    <span>{{ $r->no_rujukan_kesihatan }}</span>
                                </a>
                            </td>
                            <td class="px-4 py-3.5">
                                <a href="{{ route('eptr.show', $r->ternakan_id) }}" class="font-bold text-slate-900 hover:text-emerald-700 font-mono">
                                    Tag: {{ $r->ternakan->no_tag ?? 'ID-'.$r->ternakan_id }}
                                </a>
                                <div class="text-[11px] text-slate-500">{{ $r->ternakan->pemunya->nama ?? '-' }} ({{ $r->jajahan }})</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold {{ str_contains($r->jenis_program, 'Vaksin') ? 'bg-emerald-100 text-emerald-800' : (str_contains($r->jenis_program, 'Penyahcacing') ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-800') }}">
                                    {{ $r->jenis_program }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-slate-900">{{ $r->nama_vaksin_atau_ubat }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">{{ $r->dos_diberikan ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3.5 font-medium text-slate-900">
                                {{ $r->tarikh_rawatan ? $r->tarikh_rawatan->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-4 py-3.5 font-mono text-[11px] text-amber-800 font-bold">
                                {{ $r->tarikh_ulangan_dos ? $r->tarikh_ulangan_dos->format('d/m/Y') : 'Tiada' }}
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $r->status_kesihatan === 'Sihat & Cergas' || $r->status_kesihatan === 'Cemerlang / Sihat' || $r->status_kesihatan === 'Sembuh' ? 'bg-emerald-100 text-emerald-800' : ($r->status_kesihatan === 'Dalam Pemantauan' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700') }}">
                                    {{ $r->status_kesihatan }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <a href="{{ route('eptr.kesihatan.show', $r->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold transition">
                                    <i class="fa-solid fa-eye text-teal-700"></i> Butiran
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-heart-pulse text-4xl text-slate-300 mb-2 block"></i>
                                Tiada rekod program kesihatan dijumpai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rekodList->hasPages())
            <div class="pt-4 border-t border-slate-100">
                {{ $rekodList->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
