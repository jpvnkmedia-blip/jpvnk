@extends('layouts.app')

@section('title', 'EPTR Borang D & SKV Sembelih - Permit Sembelihan Ternakan')
@section('page_title', 'EPTR Borang D: Permit & Sijil SKV Sembelihan')

@section('content')
<div class="space-y-6" x-data="{
    selectedIds: [],
    allIds: {{ json_encode($permitList->pluck('id')) }},
    toggleAll() {
        if (this.selectedIds.length === this.allIds.length) {
            this.selectedIds = [];
        } else {
            this.selectedIds = [...this.allIds];
        }
    },
    submitBulk(actionUrl) {
        if (this.selectedIds.length === 0) {
            alert('Sila pilih sekurang-kurangnya satu Permit Sembelihan.');
            return;
        }
        let form = this.$refs.bulkPrintForm;
        form.action = actionUrl;
        form.submit();
    }
}">

    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">Senarai Permit Sembelihan & SKV Sembelih (Borang D)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Pengeluaran permit sembelihan sah (Borang D) dan Sijil Kesihatan Veterinar (SKV) Sembelih & Pemindahan Karkas (Sah laku 7 hari &bull; Bayaran RM 10.00)</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if(Auth::user()->isStaff())
                <button type="button" 
                        @click="submitBulk('{{ route('eptr.borang-d.cetak-pukal-skv') }}')"
                        :class="selectedIds.length > 0 ? 'bg-emerald-700 hover:bg-emerald-800 text-white shadow-lg shadow-emerald-700/30' : 'bg-slate-100 text-slate-400 cursor-not-allowed'"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-2xs">
                    <i class="fa-solid fa-file-shield text-emerald-300"></i>
                    <span>Cetak Pukal SKV Sembelih</span>
                    <span x-show="selectedIds.length > 0" class="bg-white/20 px-1.5 py-0.5 rounded-md text-[10px]" x-text="selectedIds.length"></span>
                </button>
                <button type="button" 
                        @click="submitBulk('{{ route('eptr.borang-d.cetak-pukal') }}')"
                        :class="selectedIds.length > 0 ? 'bg-slate-900 hover:bg-slate-800 text-white shadow-lg' : 'bg-slate-100 text-slate-400 cursor-not-allowed'"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-2xs">
                    <i class="fa-solid fa-print text-amber-400"></i>
                    <span>Cetak Pukal Borang D</span>
                    <span x-show="selectedIds.length > 0" class="bg-white/20 px-1.5 py-0.5 rounded-md text-[10px]" x-text="selectedIds.length"></span>
                </button>
            @endif
            @if(!Auth::user()->isPurePengarah())
            <a href="{{ route('eptr.borang-d.create') }}" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-lg shadow-amber-700/30 transition flex items-center gap-2">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Mohon Permit & SKV Sembelih</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Hidden Bulk Print Form -->
    <form x-ref="bulkPrintForm" action="" method="POST" target="_blank" class="hidden">
        @csrf
        <template x-for="id in selectedIds" :key="id">
            <input type="hidden" name="ids[]" :value="id">
        </template>
    </form>

    <!-- Filter & Search Section -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('eptr.borang-d.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-3 items-end text-xs">
            <div class="col-span-1 sm:col-span-2 md:col-span-3">
                <label class="block font-bold text-slate-700 mb-1">Carian Kata Kunci</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="No. Permit / No. Tag / Rujukan SKV..." class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none transition">
                    <i class="fa-solid fa-search absolute left-2.5 top-2.5 text-slate-400"></i>
                </div>
            </div>

            @if(Auth::user()->isStaff())
                <div class="col-span-1 sm:col-span-2 md:col-span-3">
                    <label class="block font-bold text-slate-700 mb-1">Pilih Pemilik / Penternak</label>
                    <select name="pemunya_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none transition">
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
                <input type="text" name="no_kp" value="{{ request('no_kp') }}" placeholder="Contoh: 850101035544" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none transition font-mono">
            </div>

            <div class="col-span-1 md:col-span-2">
                <label class="block font-bold text-slate-700 mb-1">Status Kelulusan</label>
                <select name="status_kelulusan" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none transition">
                    <option value="">-- Semua Status --</option>
                    <option value="Menunggu" {{ request('status_kelulusan') == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="Diluluskan" {{ request('status_kelulusan') == 'Diluluskan' ? 'selected' : '' }}>Diluluskan</option>
                    <option value="Ditolak" {{ request('status_kelulusan') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div class="col-span-1 md:col-span-2 flex gap-1.5">
                <button type="submit" class="flex-1 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-filter"></i>
                    <span>Tapis</span>
                </button>
                <a href="{{ route('eptr.borang-d.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition flex items-center justify-center" title="Set Semula Penapis">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-bold text-[11px]">
                        @if(Auth::user()->isStaff())
                            <th class="px-4 py-3.5 w-10 text-center">
                                <input type="checkbox" 
                                       @change="toggleAll()" 
                                       :checked="selectedIds.length === allIds.length && allIds.length > 0"
                                       class="w-4 h-4 rounded text-amber-600 border-slate-300 focus:ring-amber-500 cursor-pointer">
                            </th>
                        @endif
                        <th class="px-5 py-3.5">No. Permit & Rujukan SKV</th>
                        <th class="px-4 py-3.5">Pemunya & No. KP</th>
                        <th class="px-4 py-3.5">Bil. Ternakan & Spesies</th>
                        <th class="px-4 py-3.5">Tujuan & Lokasi Sembelih</th>
                        <th class="px-4 py-3.5">Tarikh Sembelih & Sah Laku</th>
                        <th class="px-4 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($permitList as $p)
                        @php
                            $items = $p->senarai_ternakan_list;
                            $countAnimals = count($items);
                            $isExpired = $p->isExpired();
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition" :class="selectedIds.includes({{ $p->id }}) ? 'bg-amber-50/40' : ''">
                            @if(Auth::user()->isStaff())
                                <td class="px-4 py-3.5 text-center">
                                    <input type="checkbox" 
                                           value="{{ $p->id }}" 
                                           x-model="selectedIds"
                                           class="w-4 h-4 rounded text-amber-600 border-slate-300 focus:ring-amber-500 cursor-pointer">
                                </td>
                            @endif
                            <td class="px-5 py-3.5 font-mono">
                                <a href="{{ route('eptr.borang-d.show', $p->id) }}" class="font-bold text-amber-900 hover:underline">
                                    {{ $p->no_permit }}
                                </a>
                                @if($p->no_rujukan_skv)
                                    <div class="text-[10px] text-slate-500">{{ $p->no_rujukan_skv }}</div>
                                @endif
                                <div class="text-[10px] font-sans text-emerald-700 font-bold">Bayaran: RM 10.00</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-slate-900">{{ $p->pemunya->nama ?? '-' }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">KP: {{ $p->pemunya->no_kp ?? '-' }} &bull; {{ $p->pemunya->jajahan ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded-full font-bold bg-amber-50 text-amber-800 border border-amber-200 text-[10px]">
                                        {{ $countAnimals }} Ekor ({{ $p->jenis_ternakan ?? 'Lembu' }})
                                    </span>
                                    @if($p->is_musim_korban)
                                        <span class="px-1.5 py-0.5 rounded bg-red-50 text-red-700 text-[9px] font-bold">Korban</span>
                                    @endif
                                </div>
                                <div class="text-[10px] text-slate-500 font-mono mt-0.5 truncate max-w-[160px]">
                                    @if($items->isNotEmpty())
                                        {{ $items->pluck('no_id_ternakan')->take(2)->implode(', ') }}{{ $countAnimals > 2 ? ' +' . ($countAnimals - 2) : '' }}
                                    @else
                                        {{ $p->ternakan->no_tag ?? '-' }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-slate-800">{{ $p->tujuan_sembelih }}</div>
                                <div class="text-[11px] text-slate-500 truncate max-w-[150px]">{{ $p->lokasi_sembelih ?: ($p->alamat_premis_sembelih ?? '-') }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-medium text-slate-800">{{ $p->tarikh_sembelih ? $p->tarikh_sembelih->format('d/m/Y') : '-' }}</div>
                                <div class="text-[10px] font-mono {{ $isExpired ? 'text-rose-600 font-bold' : 'text-emerald-700' }}">
                                    Sah: {{ $p->tarikh_tamat ? $p->tarikh_tamat->format('d/m/Y') : \Carbon\Carbon::parse($p->tarikh_sembelih)->addDays(6)->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $p->status_kelulusan === 'Diluluskan' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($p->status_kelulusan === 'Ditolak' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-amber-50 text-amber-700 border border-amber-200') }}">
                                    <i class="fa-solid {{ $p->status_kelulusan === 'Diluluskan' ? 'fa-check mr-1 text-emerald-500' : ($p->status_kelulusan === 'Ditolak' ? 'fa-xmark mr-1 text-rose-500' : 'fa-clock mr-1 text-amber-500') }}"></i>
                                    {{ $p->status_kelulusan }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('eptr.borang-d.show', $p->id) }}" class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition" title="Lihat Butiran">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    @if($p->resit_pembayaran)
                                        <a href="{{ asset('storage/' . $p->resit_pembayaran) }}" target="_blank" class="px-2.5 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold transition flex items-center gap-1 text-xs border border-indigo-200" title="Buka Gambar / Fail Resit Asal (Tab Baharu)">
                                            <i class="fa-solid fa-file-invoice-dollar"></i>
                                            <span>Resit</span>
                                        </a>
                                    @endif
                                    @if(Auth::user()->isStaff() && $p->status_kelulusan === 'Diluluskan')
                                        <a href="{{ route('eptr.borang-d.cetak-skv', $p->id) }}" target="_blank" class="px-2.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold transition flex items-center gap-1" title="Cetak Sijil SKV Sembelih (2 Halaman)">
                                            <i class="fa-solid fa-file-shield"></i>
                                            <span>SKV</span>
                                        </a>
                                        <a href="{{ route('eptr.borang-d.cetak', $p->id) }}" target="_blank" class="px-2.5 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold transition" title="Cetak Borang D">
                                            <i class="fa-solid fa-print"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">
                                <i class="fa-solid fa-receipt text-2xl mb-2 block"></i>
                                <span>Tiada rekod permit sembelihan ditemui.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($permitList->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $permitList->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
