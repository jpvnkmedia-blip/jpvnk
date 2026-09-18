@extends('layouts.app')

@section('title', 'EPTR Borang B - Senarai Pertukaran / Pemindahan Milikan Ternakan')
@section('page_title', 'EPTR Borang B: Pertukaran / Pemindahan Milikan Ternakan Ruminan')

@section('content')
<div class="space-y-6" x-data="{
    selectedIds: [],
    allIds: {{ json_encode($pindahMilikList->pluck('id')) }},
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
            <h2 class="text-xl font-extrabold text-slate-900">Senarai Pertukaran Milikan Ternakan (Borang B)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Rekod pemindahan dan pertukaran hak milik ternakan ruminan berdaftar di bawah EPTR 2024</p>
        </div>
        <div class="flex items-center gap-2">
            @if(Auth::user()->isStaff())
                <button type="button" 
                        @click="if(selectedIds.length > 0) { $refs.bulkPrintFormB.submit(); } else { alert('Sila pilih sekurang-kurangnya satu rekod Borang B.'); }"
                        :class="selectedIds.length > 0 ? 'bg-blue-700 hover:bg-blue-800 text-white shadow-lg shadow-blue-700/30' : 'bg-slate-100 text-slate-400 cursor-not-allowed'"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2">
                    <i class="fa-solid fa-print"></i>
                    <span>Cetak Pukal Borang B</span>
                    <span x-show="selectedIds.length > 0" class="bg-white/20 px-1.5 py-0.5 rounded-md text-[10px]" x-text="selectedIds.length"></span>
                </button>
            @endif
            @if(!Auth::user()->isStaff())
            <a href="{{ route('eptr.borang-b.create') }}" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-lg shadow-blue-700/30 transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                <span>Borang Pindah Milik Baru</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Hidden Bulk Print Form for Borang B -->
    <form x-ref="bulkPrintFormB" action="{{ route('eptr.borang-b.cetak-pukal') }}" method="POST" target="_blank" class="hidden">
        @csrf
        <template x-for="id in selectedIds" :key="id">
            <input type="hidden" name="ids[]" :value="id">
        </template>
    </form>

    <!-- Filter & Search Section -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('eptr.borang-b.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-3 items-end text-xs">
            <div class="col-span-1 sm:col-span-2 md:col-span-4">
                <label class="block font-bold text-slate-700 mb-1">Carian Kata Kunci</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="No Tag / Nama Pemilik / No KP / Sebab..." class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                    <i class="fa-solid fa-search absolute left-2.5 top-2.5 text-slate-400"></i>
                </div>
            </div>

            <div class="col-span-1 sm:col-span-2 md:col-span-3">
                <label class="block font-bold text-slate-700 mb-1">Sebab Pindah Milik</label>
                <select name="sebab_pindah" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                    <option value="">-- Semua Sebab --</option>
                    <option value="Jualan" {{ request('sebab_pindah') == 'Jualan' ? 'selected' : '' }}>Jualan</option>
                    <option value="Hibah / Hadiah" {{ request('sebab_pindah') == 'Hibah / Hadiah' ? 'selected' : '' }}>Hibah / Hadiah</option>
                    <option value="Pewarisan / Pusaka" {{ request('sebab_pindah') == 'Pewarisan / Pusaka' ? 'selected' : '' }}>Pewarisan / Pusaka</option>
                    <option value="Pajakan / Kongsi" {{ request('sebab_pindah') == 'Pajakan / Kongsi' ? 'selected' : '' }}>Pajakan / Kongsi</option>
                    <option value="Lain-lain" {{ request('sebab_pindah') == 'Lain-lain' ? 'selected' : '' }}>Lain-lain</option>
                </select>
            </div>

            <div class="col-span-1 md:col-span-3">
                <label class="block font-bold text-slate-700 mb-1">Status Kelulusan</label>
                <select name="status_kelulusan" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                    <option value="">-- Semua Status --</option>
                    <option value="Menunggu" {{ request('status_kelulusan') == 'Menunggu' ? 'selected' : '' }}>Menunggu Kelulusan</option>
                    <option value="Diluluskan" {{ request('status_kelulusan') == 'Diluluskan' ? 'selected' : '' }}>Diluluskan</option>
                    <option value="Ditolak" {{ request('status_kelulusan') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div class="col-span-1 md:col-span-2 flex gap-1.5">
                <button type="submit" class="flex-1 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-filter"></i>
                    <span>Tapis</span>
                </button>
                <a href="{{ route('eptr.borang-b.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition flex items-center justify-center" title="Set Semula Penapis">
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
                                   class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500 cursor-pointer">
                        </th>
                    @endif
                    <th class="px-4 py-3.5">No Tag & Ternakan</th>
                    <th class="px-4 py-3.5">Pemunya Asal (Penjual)</th>
                    <th class="px-4 py-3.5">Pemunya Baru (Pembeli)</th>
                    <th class="px-4 py-3.5">Tarikh & Sebab</th>
                    <th class="px-4 py-3.5">Harga Jualan (RM)</th>
                    <th class="px-4 py-3.5">Status Kelulusan</th>
                    <th class="px-4 py-3.5 text-right">Tindakan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($pindahMilikList as $pm)
                    <tr class="hover:bg-slate-50/80 transition" :class="selectedIds.includes({{ $pm->id }}) ? 'bg-blue-50/40' : ''">
                        @if(Auth::user()->isStaff())
                            <td class="px-4 py-3.5 text-center">
                                <input type="checkbox" 
                                       value="{{ $pm->id }}" 
                                       x-model="selectedIds"
                                       class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500 cursor-pointer">
                            </td>
                        @endif
                        <td class="px-4 py-3.5">
                            <div class="font-mono font-bold text-slate-900 text-sm">
                                <a href="{{ route('eptr.show', $pm->ternakan_id) }}" class="hover:underline text-emerald-800">
                                    {{ $pm->ternakan->no_tag ?? 'Tag N/A' }}
                                </a>
                            </div>
                            <div class="text-[11px] text-slate-500 capitalize">{{ $pm->ternakan->baka ?? '-' }} &bull; {{ $pm->ternakan->jantina ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="font-bold text-slate-900">{{ $pm->pemunyaAsal->nama ?? '-' }}</div>
                            <div class="text-[11px] text-slate-500 font-mono">KP: {{ $pm->pemunyaAsal->no_kp ?? '-' }} &bull; {{ $pm->pemunyaAsal->jajahan ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="font-bold text-emerald-800">{{ $pm->pemunyaBaru->nama ?? '-' }}</div>
                            <div class="text-[11px] text-slate-500 font-mono">KP: {{ $pm->pemunyaBaru->no_kp ?? '-' }} &bull; {{ $pm->pemunyaBaru->jajahan ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="font-semibold text-slate-800">{{ $pm->tarikh_pindah ? $pm->tarikh_pindah->format('d/m/Y') : '-' }}</div>
                            <div class="text-[11px] text-slate-500">{{ $pm->sebab_pindah }}</div>
                        </td>
                        <td class="px-4 py-3.5 font-mono font-semibold text-slate-800">
                            {{ $pm->harga_jualan ? 'RM ' . number_format($pm->harga_jualan, 2) : '-' }}
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $pm->status_kelulusan === 'Diluluskan' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : ($pm->status_kelulusan === 'Ditolak' ? 'bg-rose-100 text-rose-800 border border-rose-300' : 'bg-amber-100 text-amber-800 border border-amber-300') }}">
                                <i class="fa-solid {{ $pm->status_kelulusan === 'Diluluskan' ? 'fa-check mr-1 text-emerald-600' : ($pm->status_kelulusan === 'Ditolak' ? 'fa-xmark mr-1 text-rose-600' : 'fa-clock mr-1 text-amber-600') }}"></i>
                                {{ $pm->status_kelulusan }}
                            </span>
                            @if($pm->pelulus)
                                <div class="text-[10px] text-slate-400 mt-0.5">{{ $pm->pelulus->nama }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-right whitespace-nowrap space-x-1">
                            <a href="{{ route('eptr.borang-b.show', $pm->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold border border-blue-200 transition" title="Lihat Butiran Borang B">
                                <i class="fa-solid fa-eye"></i>
                                <span>Butiran</span>
                            </a>

                            @if($pm->resit_pembayaran)
                                <a href="{{ asset('storage/' . $pm->resit_pembayaran) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold border border-indigo-200 transition" title="Buka Gambar / Fail Resit Asal (Tab Baharu)">
                                    <i class="fa-solid fa-file-invoice-dollar"></i>
                                    <span>Resit</span>
                                </a>
                            @endif

                            @if(Auth::user()->isStaff())
                                @if($pm->status_kelulusan === 'Menunggu')
                                    <form action="{{ route('eptr.borang-b.lulus', $pm->id) }}" method="POST" class="inline" onsubmit="return confirm('Luluskan pindah milik ini dan tukar hak milik ternakan secara rasmi?');">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition" title="Luluskan Pindah Milik">
                                            <i class="fa-solid fa-circle-check"></i>
                                            <span>Lulus</span>
                                        </button>
                                    </form>

                                    <form action="{{ route('eptr.borang-b.tolak', $pm->id) }}" method="POST" class="inline" onsubmit="return confirm('Tolak permohonan pindah milik ini?');">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200 transition" title="Tolak Permohonan">
                                            <i class="fa-solid fa-xmark"></i>
                                            <span>Tolak</span>
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('eptr.borang-b.cetak', $pm->id) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold border border-slate-300 transition" title="Cetak Borang B Asal">
                                    <i class="fa-solid fa-print"></i>
                                    <span>Cetak</span>
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ Auth::user()->isStaff() ? 8 : 7 }}" class="px-6 py-12 text-center text-slate-400">
                            Tiada rekod pindah milik Borang B ditemui berdasarkan penapis yang dipilih.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pindahMilikList->hasPages())
        <div class="pt-2">
            {{ $pindahMilikList->links() }}
        </div>
    @endif

    <!-- Floating Bulk Print Toolbar for Borang B -->
    <div x-show="selectedIds.length > 0" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-8"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-8"
         class="fixed bottom-6 inset-x-0 mx-auto max-w-lg bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-2xl flex items-center justify-between border border-slate-700 z-50">
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse"></span>
            <span class="text-xs font-bold"><span x-text="selectedIds.length"></span> Rekod Borang B Dipilih</span>
        </div>
        <div class="flex items-center gap-2">
            <button @click="$refs.bulkPrintFormB.submit()" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-md">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Pukal Borang B</span>
            </button>
            <button @click="selectedIds = []" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-bold transition">
                Batal
            </button>
        </div>
    </div>

</div>
@endsection
