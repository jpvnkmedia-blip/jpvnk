@extends('layouts.app')

@section('title', in_array(Auth::user()->role, ['admin_epu_jajahan', 'pegawai_verifikasi_epu']) ? 'EPU Unggas PPVJ ' . (Auth::user()->jajahan ?? 'Jajahan') : 'EPU - Enakmen Perladangan Unggas')
@section('page_title', in_array(Auth::user()->role, ['admin_epu_jajahan', 'pegawai_verifikasi_epu']) ? 'Pengurusan Ladang Unggas (PPVJ Jajahan ' . (Auth::user()->jajahan ?? 'Kelantan') . ')' : 'Enakmen Perladangan Unggas (EPU 2005 / 2024)')

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">Senarai Ladang & Lesen Perladangan Unggas</h2>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ in_array(Auth::user()->role, ['admin_epu_jajahan', 'pegawai_verifikasi_epu']) ? 'Pengurusan premis, verifikasi tapak kepatuhan & laporan pemeriksaan Borang D bagi Jajahan ' . (Auth::user()->jajahan ?? 'Kelantan') : 'Pengurusan pendaftaran premis, pengeluaran lesen EPU, pembaharuan tahunan dan kelulusan rasmi peringkat Negeri Kelantan' }}
            </p>
        </div>
        <a href="{{ route('epu.create') }}" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-lg shadow-amber-700/30 transition flex items-center gap-2">
            <i class="fa-solid fa-plus-circle"></i>
            <span>Daftar Ladang & Lesen (Borang A)</span>
        </a>
    </div>

    <!-- KPI Summary Pills -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase">Jumlah Ladang Unggas</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $totalLadang }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">
                {{ in_array(Auth::user()->role, ['admin_epu_jajahan', 'pegawai_verifikasi_epu']) ? 'Jajahan ' . (Auth::user()->jajahan ?? 'Kelantan') : 'Seluruh Kelantan' }}
            </div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase">Lesen EPU Aktif</div>
            <div class="text-2xl font-black text-emerald-800 mt-1">{{ $totalLesenAktif }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Borang B Sah Laku</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase">Reban Tertutup Moden</div>
            <div class="text-2xl font-black text-blue-800 mt-1">{{ $totalRebanTertutup }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Kawalan Biosekuriti Tinggi</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase">Laporan Pemeriksaan</div>
            <div class="text-2xl font-black text-amber-800 mt-1">{{ $totalPemeriksaan }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Borang D Lapangan</div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm">
        <form action="{{ route('epu.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Search Input -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Carian Ladang / Syarikat</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama Ladang / SSM / Pemilik..."
                           class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-amber-500">
                    <i class="fas fa-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                </div>
            </div>

            <!-- Jajahan Filter -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Jajahan</label>
                @if(in_array(Auth::user()->role, ['admin_epu_jajahan', 'pegawai_verifikasi_epu']) && Auth::user()->jajahan)
                    <div class="relative">
                        <input type="text" value="Jajahan {{ Auth::user()->jajahan }} (Terkunci)" readonly
                               class="w-full px-3 py-2 text-xs rounded-xl border border-amber-300 bg-amber-50 font-bold text-amber-800 cursor-not-allowed">
                        <i class="fas fa-lock absolute right-3 top-2.5 text-amber-600 text-xs"></i>
                    </div>
                @else
                    <select name="jajahan" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-amber-500">
                        <option value="">-- Semua Jajahan --</option>
                        @php
                            $kelantanJajahans = ['Kota Bharu', 'Pasir Mas', 'Tumpat', 'Bachok', 'Pasir Puteh', 'Machang', 'Tanah Merah', 'Jeli', 'Kuala Krai', 'Gua Musang'];
                        @endphp
                        @foreach($kelantanJajahans as $j)
                            <option value="{{ $j }}" {{ request('jajahan') == $j ? 'selected' : '' }}>{{ $j }}</option>
                        @endforeach
                    </select>
                @endif
            </div>

            <!-- Sistem Reban -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Sistem Reban</label>
                <select name="sistem_reban" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-amber-500">
                    <option value="">-- Semua Sistem --</option>
                    <option value="Tertutup" {{ request('sistem_reban') == 'Tertutup' ? 'selected' : '' }}>Tertutup (Moden)</option>
                    <option value="Terbuka" {{ request('sistem_reban') == 'Terbuka' ? 'selected' : '' }}>Terbuka (Tradisional)</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-xl text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 transition shadow-sm flex items-center justify-center">
                    <i class="fas fa-filter mr-1.5"></i> Tapis
                </button>
                <a href="{{ route('epu.index') }}" class="px-3 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Farms Table -->
    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-bold text-[11px]">
                        <th class="px-5 py-3.5">Nama Ladang & Syarikat</th>
                        <th class="px-4 py-3.5">Pemilik / Pengusaha</th>
                        <th class="px-4 py-3.5">Jajahan / Mukim</th>
                        <th class="px-4 py-3.5">Sistem Reban</th>
                        <th class="px-4 py-3.5">Kapasiti Burung</th>
                        <th class="px-4 py-3.5">Status Lesen Terkini</th>
                        <th class="px-5 py-3.5 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($ladangList as $ladang)
                        @php $latestLesen = $ladang->permohonanTerkini; @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5">
                                <a href="{{ route('epu.show', $ladang->id) }}" class="font-bold text-slate-900 text-sm hover:text-emerald-700 hover:underline flex items-center gap-1.5">
                                    <i class="fa-solid fa-feather text-amber-600"></i>
                                    <span>{{ $ladang->nama_ladang }}</span>
                                </a>
                                <div class="text-[11px] text-slate-500 mt-0.5">{{ $ladang->nama_pemohon_atau_syarikat }} ({{ $ladang->no_syarikat_atau_ssm ?? 'Individu' }})</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-slate-800">{{ $ladang->pemilik->name ?? '-' }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">{{ $ladang->pemilik->phone ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-medium text-slate-900">{{ $ladang->jajahan }}</div>
                                <div class="text-[11px] text-slate-500">Mukim {{ $ladang->mukim ?? '-' }} &bull; {{ $ladang->luas_tanah_ekar ?? '0' }} Ekar</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold {{ $ladang->sistem_reban === 'Tertutup' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $ladang->sistem_reban }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 font-bold text-slate-900">
                                {{ number_format($ladang->kapasiti_maksimum_unggas) }} Ekor
                            </td>
                            <td class="px-4 py-3.5">
                                @if($latestLesen)
                                    <div class="space-y-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold
                                            @if($latestLesen->status === 'Diluluskan') bg-emerald-100 text-emerald-800
                                            @elseif($latestLesen->status === 'Ditolak') bg-rose-100 text-rose-800
                                            @elseif($latestLesen->status_verifikasi === 'Tidak Patuh') bg-amber-100 text-amber-800
                                            @elseif($latestLesen->status_verifikasi === 'Tidak Lengkap') bg-orange-100 text-orange-800
                                            @else bg-blue-100 text-blue-800 @endif">
                                            {{ $latestLesen->status }}
                                        </span>
                                        @if($latestLesen->no_lesen_epu && $latestLesen->status === 'Diluluskan')
                                            <div class="text-[10px] font-mono font-bold text-slate-600">{{ $latestLesen->no_lesen_epu }}</div>
                                        @elseif($latestLesen->status_verifikasi)
                                            <div class="text-[10px] text-slate-500">PPVJ: {{ $latestLesen->status_verifikasi }}</div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-400 text-[11px]">Belum Berlesen</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('epu.show', $ladang->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition" title="Lihat Ladang">
                                    <i class="fa-solid fa-eye"></i> Ladang
                                </a>
                                @if($latestLesen && $latestLesen->resit_bayaran_fi)
                                    <a href="{{ asset('storage/' . $latestLesen->resit_bayaran_fi) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 font-bold transition" title="Buka Gambar / Fail Resit Asal (Tab Baharu)">
                                        <i class="fa-solid fa-file-invoice-dollar"></i> Resit
                                    </a>
                                @endif
                                @if(Auth::user()->canCetakBorangEpu())
                                    <a href="{{ route('epu.cetak-borang-a', $ladang->id) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 font-bold transition" title="Cetak Borang A Rasmi (Warta)">
                                        <i class="fa-solid fa-print"></i> Borang A
                                    </a>
                                    @if($latestLesen && $latestLesen->status === 'Diluluskan')
                                        <a href="{{ route('epu.cetak-lesen', $latestLesen->id) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-emerald-100 hover:bg-emerald-200 text-emerald-900 font-bold transition" title="Cetak Lesen EPU Borang B">
                                            <i class="fa-solid fa-certificate"></i> Lesen
                                        </a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                Tiada rekod ladang unggas dijumpai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ladangList->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $ladangList->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
