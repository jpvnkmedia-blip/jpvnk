@extends('layouts.app')

@section('title', 'Action List (Dairi & Log Aktiviti Admin) - JPVNK')

@section('content')
<div class="space-y-6">
    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gradient-to-r from-emerald-900 via-teal-900 to-slate-900 p-6 sm:p-8 rounded-3xl text-white shadow-xl">
        <div class="space-y-1.5">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-bold border border-emerald-500/30">
                <i class="fa-solid fa-book-journal-whills"></i> DAIRI & LOG AKTIVITI PEGAWAI
            </div>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Action List Pentadbiran & Lapangan</h1>
            <p class="text-xs sm:text-sm text-slate-300 max-w-2xl">
                Rekod log harian aktiviti pegawai veterinar, lawatan tapak, mesyuarat, pemeriksaan premis, dan pemantauan projek jajahan.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('action-list.cetak', request()->query()) }}" target="_blank" class="px-4 py-2.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition flex items-center gap-2 border border-white/20">
                <i class="fa-solid fa-print"></i> Cetak Laporan / Dairi
            </a>
            <a href="{{ route('action-list.create') }}" class="px-5 py-2.5 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs shadow-lg shadow-emerald-950/40 transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Rekod Aktiviti Baharu
            </a>
        </div>
    </div>

    <!-- Statistik Ringkasan Dairi -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl flex-shrink-0">
                <i class="fa-solid fa-clipboard-list"></i>
            </div>
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Jumlah Rekod</div>
                <div class="text-xl font-black text-slate-900 mt-0.5">{{ number_format($totalAktiviti) }}</div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl flex-shrink-0">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Bulan Ini</div>
                <div class="text-xl font-black text-teal-700 mt-0.5">{{ number_format($bulanIniCount) }}</div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center text-xl flex-shrink-0">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Aktiviti Selesai</div>
                <div class="text-xl font-black text-green-700 mt-0.5">{{ number_format($selesaiCount) }}</div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl flex-shrink-0">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Dalam Tindakan</div>
                <div class="text-xl font-black text-amber-700 mt-0.5">{{ number_format($dalamTindakanCount) }}</div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center gap-4 col-span-2 lg:col-span-1">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl flex-shrink-0">
                <i class="fa-solid fa-calendar-plus"></i>
            </div>
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Perancangan</div>
                <div class="text-xl font-black text-blue-700 mt-0.5">{{ number_format($perancanganCount) }}</div>
            </div>
        </div>
    </div>

    <!-- Filter & Carian -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
        <form action="{{ route('action-list.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                <!-- Carian Kata Kunci -->
                <div class="sm:col-span-2 lg:col-span-1">
                    <label class="block font-bold text-slate-700 uppercase mb-1">Carian Aktiviti</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tajuk, lokasi, pegawai, catatan..." class="w-full py-2 pl-8 pr-3 text-xs rounded-xl border border-slate-300 focus:outline-emerald-500">
                        <i class="fa-solid fa-search absolute left-2.5 top-2.5 text-slate-400"></i>
                    </div>
                </div>

                <!-- Jajahan -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Pejabat Jajahan</label>
                    <select name="jajahan" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-800 font-semibold">
                        <option value="Semua">Semua Jajahan</option>
                        @foreach($jajahanList as $jjh)
                            <option value="{{ $jjh }}" {{ (request('jajahan', $selectedJajahan) === $jjh) ? 'selected' : '' }}>Jajahan {{ $jjh }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Kategori Aktiviti -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Kategori Aktiviti</label>
                    <select name="kategori" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-800 font-semibold">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoriList as $kKey => $kVal)
                            <option value="{{ $kKey }}" {{ request('kategori') === $kKey ? 'selected' : '' }}>{{ $kVal }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Status Aktiviti</label>
                    <select name="status" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-800 font-semibold">
                        <option value="">Semua Status</option>
                        @foreach($statusList as $sKey => $sVal)
                            <option value="{{ $sKey }}" {{ request('status') === $sKey ? 'selected' : '' }}>{{ $sVal }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tarikh Mula -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Mula</label>
                    <input type="date" name="tarikh_mula" value="{{ request('tarikh_mula') }}" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-800">
                </div>

                <!-- Tarikh Akhir -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Akhir</label>
                    <input type="date" name="tarikh_akhir" value="{{ request('tarikh_akhir') }}" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-800">
                </div>

                <!-- Keutamaan -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Keutamaan</label>
                    <select name="keutamaan" class="w-full py-2 px-3 rounded-xl border border-slate-300 focus:outline-emerald-500 text-slate-800 font-semibold">
                        <option value="">Semua Keutamaan</option>
                        <option value="Biasa" {{ request('keutamaan') === 'Biasa' ? 'selected' : '' }}>Biasa</option>
                        <option value="Tinggi" {{ request('keutamaan') === 'Tinggi' ? 'selected' : '' }}>Tinggi</option>
                        <option value="Segera" {{ request('keutamaan') === 'Segera' ? 'selected' : '' }}>Segera</option>
                    </select>
                </div>

                <!-- Butang Tindakan -->
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition flex items-center justify-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-filter"></i> Tapis Rekod
                    </button>
                    <a href="{{ route('action-list.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition" title="Set Semula">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Senarai Rekod Aktiviti / Action List -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-black text-slate-900 text-base flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-emerald-600"></i> Senarai Log & Dairi Aktiviti Pegawai
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Memaparkan rekod aktiviti terkini mengikut tarikh pelaksanaan.</p>
            </div>
            <div class="text-xs font-semibold text-slate-500">
                Menunjukkan {{ $actionLists->firstItem() ?? 0 }} - {{ $actionLists->lastItem() ?? 0 }} daripada {{ $actionLists->total() }} rekod
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-600 uppercase font-black tracking-wider text-[11px] border-b border-slate-200">
                        <th class="py-3.5 px-4 w-12 text-center">No.</th>
                        <th class="py-3.5 px-4">Tarikh & Masa</th>
                        <th class="py-3.5 px-4">Aktiviti & Kategori</th>
                        <th class="py-3.5 px-4">Maklumat Aktiviti</th>
                        <th class="py-3.5 px-4">Lokasi & Jajahan</th>
                        <th class="py-3.5 px-4">Pegawai Bertugas</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center w-28">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-800">
                    @forelse($actionLists as $index => $item)
                        <tr class="hover:bg-slate-50/60 transition group">
                            <td class="py-3.5 px-4 text-center font-mono text-slate-400">
                                {{ $actionLists->firstItem() + $index }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-black text-slate-900 flex items-center gap-1.5">
                                    <i class="fa-regular fa-calendar text-emerald-600"></i>
                                    {{ $item->tarikh ? $item->tarikh->format('d/m/Y') : '-' }}
                                </div>
                                <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                                    @if($item->masa_mula)
                                        {{ $item->masa_mula }} {{ $item->masa_selesai ? '- ' . $item->masa_selesai : '' }}
                                    @else
                                        {{ $item->masa_pendaftaran ?: '-' }}
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-black text-slate-900 text-sm group-hover:text-emerald-700 transition">
                                    {{ $item->tajuk_aktiviti ?: ($item->nama_pelanggan ? ('Tindakan Pelanggan: ' . $item->nama_pelanggan) : 'Aktiviti Pentadbiran') }}
                                </div>
                                <div class="inline-flex items-center gap-1.5 mt-1 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-bold border border-slate-200">
                                    <i class="{{ $item->kategori_icon }}"></i>
                                    <span>{{ $item->kategori_aktiviti ?: 'Tugas Pentadbiran' }}</span>
                                </div>
                                @if($item->keutamaan && $item->keutamaan !== 'Biasa')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $item->keutamaan_badge_class }} ml-1">
                                        {{ $item->keutamaan }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 max-w-xs">
                                <div class="line-clamp-2 text-slate-600 text-xs leading-relaxed">
                                    {{ $item->maklumat_aktiviti ?: ($item->catatan_perkhidmatan_dipohon ?: ($item->laporan ?: 'Tiada maklumat aktiviti tambahan.')) }}
                                </div>
                                @if($item->tindakan_susulan)
                                    <div class="text-[10px] text-amber-700 font-semibold mt-1 flex items-center gap-1">
                                        <i class="fa-solid fa-triangle-exclamation"></i> Tindakan: {{ \Illuminate\Support\Str::limit($item->tindakan_susulan, 40) }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-bold text-slate-800">
                                    {{ $item->lokasi ?: ($item->alamat ? \Illuminate\Support\Str::limit($item->alamat, 25) : 'Pejabat Jajahan') }}
                                </div>
                                <div class="text-[11px] text-emerald-700 font-semibold mt-0.5">
                                    <i class="fa-solid fa-location-dot"></i> Jajahan {{ $item->jajahan }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-bold text-slate-900">
                                    {{ $item->nama_pegawai ?: ($item->pegawai->name ?? '-') }}
                                </div>
                                <div class="text-[10px] font-mono text-slate-400">
                                    {{ $item->no_bil ?: ('ID-' . $item->id) }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ $item->status_badge_class }}">
                                    {{ $item->status ?? 'Selesai' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('action-list.show', $item->id) }}" class="p-2 rounded-xl bg-slate-100 hover:bg-emerald-100 text-slate-700 hover:text-emerald-900 transition" title="Lihat Butiran">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('action-list.edit', $item->id) }}" class="p-2 rounded-xl bg-slate-100 hover:bg-amber-100 text-slate-700 hover:text-amber-900 transition" title="Kemaskini">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    @if(Auth::user()->isSuperAdmin() || Auth::user()->isAdminJajahan())
                                        <form action="{{ route('action-list.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Adakah anda pasti mahu memadam rekod aktiviti ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-xl bg-slate-100 hover:bg-rose-100 text-slate-700 hover:text-rose-900 transition" title="Padam">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mx-auto mb-3">
                                    <i class="fa-solid fa-clipboard"></i>
                                </div>
                                <div class="font-bold text-slate-700 text-sm">Tiada Rekod Aktiviti Dijumpai</div>
                                <p class="text-xs text-slate-400 mt-1">Sila gunakan butang 'Rekod Aktiviti Baharu' untuk mula mencatat dairi pegawai.</p>
                                <a href="{{ route('action-list.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 mt-4 rounded-xl bg-emerald-600 text-white font-bold text-xs hover:bg-emerald-700 transition">
                                    <i class="fa-solid fa-plus"></i> Rekod Aktiviti Baharu
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($actionLists->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $actionLists->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
