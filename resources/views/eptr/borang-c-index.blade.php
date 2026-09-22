@extends('layouts.app')

@section('title', 'EPTR Borang C - Notis Pembatalan / Kematian')
@section('page_title', 'EPTR Borang C: Notis Pembatalan / Kematian / Pindah Keluar')

@section('content')
<div class="space-y-6" x-data="{
    selectedIds: [],
    allIds: {{ json_encode($pembatalanList->pluck('id')) }},
    toggleAll() {
        if (this.selectedIds.length === this.allIds.length) {
            this.selectedIds = [];
        } else {
            this.selectedIds = [...this.allIds];
        }
    }
}">

    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">Senarai Notis Pembatalan & Kematian (Borang C)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Rekod pembatalan pendaftaran akibat kematian, pindah keluar negeri, kecurian atau pelupusan</p>
        </div>
        <div class="flex items-center gap-2">
            @if(Auth::user()->isStaff())
                <button type="button" 
                        @click="if(selectedIds.length > 0) { $refs.bulkPrintFormC.submit(); } else { alert('Sila pilih sekurang-kurangnya satu rekod Borang C.'); }"
                        :class="selectedIds.length > 0 ? 'bg-rose-700 hover:bg-rose-800 text-white shadow-lg shadow-rose-700/30' : 'bg-slate-100 text-slate-400 cursor-not-allowed'"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2">
                    <i class="fa-solid fa-print"></i>
                    <span>Cetak Pukal Borang C</span>
                    <span x-show="selectedIds.length > 0" class="bg-white/20 px-1.5 py-0.5 rounded-md text-[10px]" x-text="selectedIds.length"></span>
                </button>
            @endif
            @if(!Auth::user()->isPurePengarah())
            <a href="{{ route('eptr.borang-c.create') }}" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-lg shadow-rose-700/30 transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                <span>Hantar Notis Borang C</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Hidden Bulk Print Form for Borang C -->
    <form x-ref="bulkPrintFormC" action="{{ route('eptr.borang-c.cetak-pukal') }}" method="POST" target="_blank" class="hidden">
        @csrf
        <template x-for="id in selectedIds" :key="id">
            <input type="hidden" name="ids[]" :value="id">
        </template>
    </form>

    <!-- Filter & Search Section -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('eptr.borang-c.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-3 items-end text-xs">
            <div class="col-span-1 sm:col-span-2 md:col-span-3">
                <label class="block font-bold text-slate-700 mb-1">Carian Kata Kunci</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="No Tag / Nama / Laporan Polis..." class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none transition">
                    <i class="fa-solid fa-search absolute left-2.5 top-2.5 text-slate-400"></i>
                </div>
            </div>

            @if(Auth::user()->isStaff())
                <div class="col-span-1 sm:col-span-2 md:col-span-3">
                    <label class="block font-bold text-slate-700 mb-1">Pilih Pemilik / Penternak</label>
                    <select name="pemunya_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none transition">
                        <option value="">-- Semua Pemilik --</option>
                        @foreach($pemunyaList as $pem)
                            <option value="{{ $pem->id }}" {{ request('pemunya_id') == $pem->id ? 'selected' : '' }}>
                                {{ $pem->nama }} ({{ $pem->no_kp }})
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="col-span-1 md:col-span-2">
                <label class="block font-bold text-slate-700 mb-1">No Kad Pengenalan</label>
                <input type="text" name="no_kp" value="{{ request('no_kp') }}" placeholder="Contoh: 850101035544" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none transition font-mono">
            </div>

            <div class="col-span-1 md:col-span-2">
                <label class="block font-bold text-slate-700 mb-1">Jenis Pembatalan</label>
                <select name="jenis_batal" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none transition">
                    <option value="">-- Semua Jenis --</option>
                    <option value="Mati" {{ request('jenis_batal') == 'Mati' ? 'selected' : '' }}>Mati</option>
                    <option value="Pindah Keluar" {{ request('jenis_batal') == 'Pindah Keluar' ? 'selected' : '' }}>Pindah Keluar</option>
                    <option value="Kecurian" {{ request('jenis_batal') == 'Kecurian' ? 'selected' : '' }}>Kecurian</option>
                    <option value="Pelupusan" {{ request('jenis_batal') == 'Pelupusan' ? 'selected' : '' }}>Pelupusan</option>
                    <option value="Lain-lain" {{ request('jenis_batal') == 'Lain-lain' ? 'selected' : '' }}>Lain-lain</option>
                </select>
            </div>

            <div class="col-span-1 md:col-span-2 flex gap-1.5">
                <button type="submit" class="flex-1 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-filter"></i>
                    <span>Tapis</span>
                </button>
                <a href="{{ route('eptr.borang-c.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition flex items-center justify-center" title="Set Semula Penapis">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xs">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-bold text-[11px]">
                    @if(Auth::user()->isStaff())
                        <th class="px-4 py-3.5 w-10 text-center">
                            <input type="checkbox" 
                                   @change="toggleAll()" 
                                   :checked="selectedIds.length === allIds.length && allIds.length > 0"
                                   class="w-4 h-4 rounded text-rose-600 border-slate-300 focus:ring-rose-500 cursor-pointer">
                        </th>
                    @endif
                    <th class="px-4 py-3.5">No Tag Ternakan</th>
                    <th class="px-4 py-3.5">Pemunya & No. KP</th>
                    <th class="px-4 py-3.5">Jenis Pembatalan</th>
                    <th class="px-4 py-3.5">Tarikh Peristiwa</th>
                    <th class="px-4 py-3.5">Sebab / Laporan</th>
                    <th class="px-4 py-3.5">Status Pengesahan</th>
                    <th class="px-4 py-3.5 text-right">Tindakan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($pembatalanList as $p)
                    <tr class="hover:bg-slate-50/80 transition" :class="selectedIds.includes({{ $p->id }}) ? 'bg-rose-50/40' : ''">
                        @if(Auth::user()->isStaff())
                            <td class="px-4 py-3.5 text-center">
                                <input type="checkbox" 
                                       value="{{ $p->id }}" 
                                       x-model="selectedIds"
                                       class="w-4 h-4 rounded text-rose-600 border-slate-300 focus:ring-rose-500 cursor-pointer">
                            </td>
                        @endif
                        <td class="px-4 py-3.5 font-mono font-bold text-slate-900">
                            {{ $p->ternakan->no_tag ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="font-bold text-slate-900">{{ $p->ternakan->pemunya->nama ?? '-' }}</div>
                            <div class="text-[11px] text-slate-500 font-mono">KP: {{ $p->ternakan->pemunya->no_kp ?? '-' }} &bull; {{ $p->ternakan->jajahan ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $p->jenis_batal === 'Mati' ? 'bg-slate-200 text-slate-800' : ($p->jenis_batal === 'Kecurian' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                {{ $p->jenis_batal }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            {{ $p->tarikh_peristiwa ? $p->tarikh_peristiwa->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="font-medium text-slate-800">{{ $p->sebab }}</div>
                            @if($p->no_laporan_polis)
                                <div class="text-[10px] text-slate-500 font-mono">Polis: {{ $p->no_laporan_polis }}</div>
                            @endif
                            @if($p->destinasi_pindah_keluar)
                                <div class="text-[10px] text-slate-500">Destinasi: {{ $p->destinasi_pindah_keluar }}</div>
                            @endif
                            @if($p->dokumen_sokongan)
                                <div class="mt-1">
                                    <a href="{{ Storage::url($p->dokumen_sokongan) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 px-2 py-0.5 rounded-md border border-rose-200 transition" title="Lihat Dokumen Berkaitan">
                                        <i class="fa-solid fa-paperclip"></i>
                                        <span>Dokumen Berkaitan</span>
                                    </a>
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $p->status_kelulusan === 'Disahkan' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : ($p->status_kelulusan === 'Ditolak' ? 'bg-rose-100 text-rose-800 border border-rose-300' : 'bg-amber-100 text-amber-800 border border-amber-300') }}">
                                <i class="fa-solid {{ $p->status_kelulusan === 'Disahkan' ? 'fa-check mr-1 text-emerald-600' : ($p->status_kelulusan === 'Ditolak' ? 'fa-xmark mr-1 text-rose-600' : 'fa-clock mr-1 text-amber-600') }}"></i>
                                {{ $p->status_kelulusan }}
                            </span>
                            @if($p->pengesah)
                                <div class="text-[10px] text-slate-400 mt-0.5">{{ $p->pengesah->nama }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-right whitespace-nowrap space-x-1">
                            @if(Auth::user()->isStaff())
                                @if($p->status_kelulusan === 'Menunggu')
                                    <!-- Butang Sahkan / Luluskan Notis Borang C -->
                                    <form action="{{ route('eptr.borang-c.lulus', $p->id) }}" method="POST" class="inline" onsubmit="return confirm('Sahkan pembatalan ternakan ini (Status ternakan akan dikemaskini kepada {{ $p->jenis_batal === 'Mati' ? 'Mati' : ($p->jenis_batal === 'Kecurian' ? 'Batal' : 'Pindah') }})?');">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition" title="Sahkan Notis Borang C">
                                            <i class="fa-solid fa-circle-check"></i>
                                            <span>Sahkan</span>
                                        </button>
                                    </form>

                                    <!-- Butang Tolak Notis Borang C -->
                                    <form action="{{ route('eptr.borang-c.tolak', $p->id) }}" method="POST" class="inline" onsubmit="return confirm('Tolak notis pembatalan ini?');">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200 transition" title="Tolak Notis">
                                            <i class="fa-solid fa-xmark"></i>
                                            <span>Tolak</span>
                                        </button>
                                    </form>
                                @endif

                                <!-- Butang Cetak Borang C -->
                                <a href="{{ route('eptr.borang-c.cetak', $p->id) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold border border-slate-300 transition" title="Cetak Borang C Asal">
                                    <i class="fa-solid fa-print"></i>
                                    <span>Cetak</span>
                                </a>
                            @else
                                <span class="text-slate-400 text-xs italic">{{ $p->status_kelulusan === 'Disahkan' ? 'Telah Disahkan Pegawai' : ($p->status_kelulusan === 'Ditolak' ? 'Permohonan Ditolak' : 'Menunggu Tindakan Pegawai') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ Auth::user()->isStaff() ? 8 : 7 }}" class="px-6 py-12 text-center text-slate-400">
                            Tiada rekod notis pembatalan Borang C ditemui berdasarkan penapis yang dipilih.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pembatalanList->hasPages())
        <div class="pt-2">
            {{ $pembatalanList->links() }}
        </div>
    @endif

    <!-- Floating Bulk Print Toolbar for Borang C -->
    <div x-show="selectedIds.length > 0" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-8"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-8"
         class="fixed bottom-6 inset-x-0 mx-auto max-w-lg bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-2xl flex items-center justify-between border border-slate-700 z-50">
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse"></span>
            <span class="text-xs font-bold"><span x-text="selectedIds.length"></span> Notis Borang C Dipilih</span>
        </div>
        <div class="flex items-center gap-2">
            <button @click="$refs.bulkPrintFormC.submit()" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-md">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Pukal Borang C</span>
            </button>
            <button @click="selectedIds = []" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-bold transition">
                Batal
            </button>
        </div>
    </div>

</div>
@endsection
