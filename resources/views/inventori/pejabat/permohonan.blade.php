@extends('layouts.app')

@section('title', 'Pengurusan Permohonan Alatan Stor Pejabat')
@section('page_title', 'Pengurusan Permohonan Alatan & Aset Stor Pejabat')

@section('content')
<div class="space-y-6" x-data="{ modalAction: false, selectedPermohonan: null, tindakan: 'lulus', kuantitiDiluluskan: 1, maxStok: 1, unit: '', namaItem: '', namaPemohon: '', noPermohonan: '', permohonanId: null }">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-lg bg-indigo-100 text-indigo-900 font-black text-xs uppercase tracking-wider">
                    <i class="fa-solid fa-boxes-stacked mr-1"></i> Admin Stor Pejabat
                </span>
                <span class="text-xs text-slate-500 font-semibold">Semakan &amp; Kelulusan Permohonan Staf Jabatan</span>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 mt-1">Pengurusan Permohonan Barangan Pejabat</h2>
            <p class="text-xs text-slate-500 mt-0.5">Semak permohonan alatan tulis, kertas, toner dan perabot pejabat daripada pegawai &amp; unit jabatan untuk kelulusan dan penyerahan stok.</p>
        </div>

        <a href="{{ route('inventori.pejabat.index') }}" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-md transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Stor Pejabat</span>
        </a>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase">Jumlah Permohonan Pejabat</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $totalPermohonan }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Semua rekod staf</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase">Menunggu Tindakan</div>
            <div class="text-2xl font-black text-amber-600 mt-1">{{ $menungguCount }}</div>
            <div class="text-[10px] text-amber-700 mt-0.5 font-semibold">Perlu semakan segera</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase">Telah Diluluskan</div>
            <div class="text-2xl font-black text-blue-600 mt-1">{{ $lulusCount }}</div>
            <div class="text-[10px] text-blue-700 mt-0.5 font-semibold">Menunggu serahan stok</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase">Selesai Diserahkan</div>
            <div class="text-2xl font-black text-emerald-700 mt-1">{{ $selesaiCount }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Stok telah ditolak</div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-3xl border border-slate-200 p-4 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex flex-wrap gap-2 text-xs">
            <a href="{{ route('inventori.pejabat.permohonan') }}" class="px-3.5 py-1.5 rounded-full font-bold transition {{ !request('status') ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua
            </a>
            <a href="{{ route('inventori.pejabat.permohonan', ['status' => 'Menunggu Kelulusan']) }}" class="px-3.5 py-1.5 rounded-full font-bold transition {{ request('status') === 'Menunggu Kelulusan' ? 'bg-amber-600 text-white shadow-xs' : 'bg-amber-50 text-amber-800 hover:bg-amber-100 border border-amber-200' }}">
                ⚠️ Menunggu ({{ $menungguCount }})
            </a>
            <a href="{{ route('inventori.pejabat.permohonan', ['status' => 'Diluluskan']) }}" class="px-3.5 py-1.5 rounded-full font-bold transition {{ request('status') === 'Diluluskan' ? 'bg-blue-600 text-white shadow-xs' : 'bg-blue-50 text-blue-800 hover:bg-blue-100 border border-blue-200' }}">
                Diluluskan ({{ $lulusCount }})
            </a>
            <a href="{{ route('inventori.pejabat.permohonan', ['status' => 'Telah Diambil / Diserahkan']) }}" class="px-3.5 py-1.5 rounded-full font-bold transition {{ request('status') === 'Telah Diambil / Diserahkan' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Selesai ({{ $selesaiCount }})
            </a>
            <a href="{{ route('inventori.pejabat.permohonan', ['status' => 'Ditolak']) }}" class="px-3.5 py-1.5 rounded-full font-bold transition {{ request('status') === 'Ditolak' ? 'bg-rose-600 text-white shadow-xs' : 'bg-rose-50 text-rose-800 hover:bg-rose-100 border border-rose-200' }}">
                Ditolak
            </a>
        </div>

        <form action="{{ route('inventori.pejabat.permohonan') }}" method="GET" class="flex items-center gap-2">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama staf / no. permohonan / item..." class="rounded-xl border border-slate-200 pl-8 pr-3 py-1.5 text-xs bg-slate-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none w-56 sm:w-72">
                <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-2.5 top-2.5 text-xs"></i>
            </div>
            <button type="submit" class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">
                Cari
            </button>
        </form>
    </div>

    <!-- Requisitions Table -->
    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Senarai Permohonan Alatan &amp; Bekalan Stor Pejabat</h3>
            <span class="text-xs text-slate-500">Jumlah: <b>{{ $permohonans->total() }}</b> rekod</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-bold text-[11px]">
                        <th class="px-5 py-3.5">No. Permohonan</th>
                        <th class="px-4 py-3.5">Pegawai / Unit Pemohon</th>
                        <th class="px-4 py-3.5">Item Dimohon &amp; Kategori</th>
                        <th class="px-4 py-3.5">Kuantiti (Mohon / Baki Stor)</th>
                        <th class="px-4 py-3.5">Tujuan Permohonan</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Tindakan Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($permohonans as $p)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 font-mono">
                                <span class="font-bold text-indigo-900 block">{{ $p->no_permohonan }}</span>
                                <span class="text-[10px] text-slate-400">{{ $p->created_at ? $p->created_at->format('d/m/Y H:i') : '-' }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-slate-900">{{ $p->pemohon->name ?? 'Staf Jabatan' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $p->unit_bahagian }} &bull; {{ $p->pemohon->role_label ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-slate-900">{{ $p->item->nama_item ?? '-' }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">{{ $p->item->kod_item ?? '-' }} &bull; {{ $p->item->kategori ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-slate-900 text-sm">
                                    {{ number_format($p->kuantiti_dimohon) }} {{ $p->item->unit ?? 'Unit' }}
                                </div>
                                <div class="text-[10px] text-slate-500 mt-0.5">
                                    Baki Stor: <b class="{{ ($p->item->kuantiti_semasa ?? 0) < $p->kuantiti_dimohon ? 'text-rose-600' : 'text-emerald-700' }}">{{ $p->item->kuantiti_semasa ?? 0 }} {{ $p->item->unit ?? 'Unit' }}</b>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 max-w-xs">
                                <div class="text-slate-800 line-clamp-2">{{ $p->tujuan_permohonan }}</div>
                                <div class="text-[10px] text-slate-500 mt-1">
                                    Diperlukan: <b>{{ $p->tarikh_diperlukan ? $p->tarikh_diperlukan->format('d/m/Y') : 'Segera' }}</b>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $p->status_badge }}">
                                    {{ $p->status }}
                                </span>
                                @if($p->kuantiti_diluluskan !== null)
                                    <div class="text-[10px] text-slate-600 mt-1">
                                        Dilulus: <b>{{ $p->kuantiti_diluluskan }} {{ $p->item->unit ?? 'Unit' }}</b>
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap space-x-1">
                                @if($p->isPending())
                                    <!-- Lulus Button -->
                                    <button @click="modalAction = true; permohonanId = {{ $p->id }}; noPermohonan = '{{ $p->no_permohonan }}'; namaPemohon = '{{ addslashes($p->pemohon->name ?? '') }}'; namaItem = '{{ addslashes($p->item->nama_item ?? '') }}'; unit = '{{ $p->item->unit ?? 'Unit' }}'; kuantitiDiluluskan = {{ $p->kuantiti_dimohon }}; maxStok = {{ $p->item->kuantiti_semasa ?? 1 }}; tindakan = 'lulus'" class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition inline-flex items-center gap-1">
                                        <i class="fa-solid fa-check"></i> Lulus
                                    </button>

                                    <!-- Tolak Button -->
                                    <button @click="modalAction = true; permohonanId = {{ $p->id }}; noPermohonan = '{{ $p->no_permohonan }}'; namaPemohon = '{{ addslashes($p->pemohon->name ?? '') }}'; namaItem = '{{ addslashes($p->item->nama_item ?? '') }}'; unit = '{{ $p->item->unit ?? 'Unit' }}'; kuantitiDiluluskan = {{ $p->kuantiti_dimohon }}; maxStok = {{ $p->item->kuantiti_semasa ?? 1 }}; tindakan = 'tolak'" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition inline-flex items-center gap-1">
                                        <i class="fa-solid fa-xmark"></i> Tolak
                                    </button>
                                @elseif($p->isApproved())
                                    <!-- Serah Stok Button -->
                                    <button @click="modalAction = true; permohonanId = {{ $p->id }}; noPermohonan = '{{ $p->no_permohonan }}'; namaPemohon = '{{ addslashes($p->pemohon->name ?? '') }}'; namaItem = '{{ addslashes($p->item->nama_item ?? '') }}'; unit = '{{ $p->item->unit ?? 'Unit' }}'; kuantitiDiluluskan = {{ $p->kuantiti_diluluskan ?? $p->kuantiti_dimohon }}; maxStok = {{ $p->item->kuantiti_semasa ?? 1 }}; tindakan = 'serah'" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition inline-flex items-center gap-1">
                                        <i class="fa-solid fa-hand-holding-box"></i> Serah Stok
                                    </button>
                                @else
                                    <span class="text-slate-400 text-xs font-semibold">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-inbox text-3xl mb-2 text-slate-300"></i>
                                <div class="font-bold text-slate-600">Tiada permohonan alatan pejabat dijumpai</div>
                                <div class="text-xs text-slate-400 mt-0.5">Permohonan baharu daripada staf jabatan akan dipaparkan di sini.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($permohonans->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $permohonans->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL TINDAKAN: Kelulusan / Tolak / Serah Stok -->
    <div x-show="modalAction" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl text-xs space-y-4" @click.outside="modalAction = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center font-bold" :class="tindakan === 'lulus' ? 'bg-blue-100 text-blue-700' : (tindakan === 'serah' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700')">
                        <i class="fa-solid" :class="tindakan === 'lulus' ? 'fa-check' : (tindakan === 'serah' ? 'fa-box-open' : 'fa-xmark')"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900" x-text="tindakan === 'lulus' ? 'Kelulusan Permohonan Pejabat' : (tindakan === 'serah' ? 'Penyerahan Stok Alatan Pejabat' : 'Penolakan Permohonan')"></h3>
                        <p class="text-[11px] text-slate-500 font-mono" x-text="noPermohonan"></p>
                    </div>
                </div>
                <button @click="modalAction = false" class="text-slate-400 hover:text-slate-600 text-base">&times;</button>
            </div>

            <!-- Detail Banner -->
            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 space-y-1">
                <div>Pemohon: <b class="text-slate-900" x-text="namaPemohon"></b></div>
                <div>Item: <b class="text-indigo-900" x-text="namaItem"></b></div>
                <div class="text-[11px] text-slate-500">Baki Stok Sedia Ada: <b class="text-slate-800" x-text="maxStok + ' ' + unit"></b></div>
            </div>

            <form :action="'{{ url('/inventori/permohonan') }}/' + permohonanId + '/status'" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="tindakan" :value="tindakan">

                <template x-if="tindakan === 'lulus' || tindakan === 'serah'">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Kuantiti Diluluskan / Diserahkan (<span x-text="unit"></span>) <span class="text-rose-500">*</span></label>
                        <input type="number" name="kuantiti_diluluskan" x-model="kuantitiDiluluskan" min="1" :max="maxStok" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                </template>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1" x-text="tindakan === 'tolak' ? 'Sebab Penolakan Permohonan' : 'Catatan Pegawai Pentadbiran (Pilihan)'"></label>
                    <textarea name="catatan_pegawai" rows="2.5" :required="tindakan === 'tolak'" :placeholder="tindakan === 'tolak' ? 'Nyatakan sebab penolakan (cth: Kekangan stok / Perlu justifikasi lanjut)...' : 'Catatan kepada pemohon semasa pengambilan alatan...'" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                </div>

                <template x-if="tindakan === 'serah'">
                    <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200 text-emerald-800 text-[11px]">
                        <i class="fa-solid fa-circle-info mr-1"></i> Penyerahan ini akan <b>menolak baki stok inventori secara automatik</b> dan merekodkan catatan <b>Stok Keluar</b> dalam lejar.
                    </div>
                </template>

                <div class="pt-3 flex justify-end gap-2">
                    <button type="button" @click="modalAction = false" class="px-4 py-2 rounded-xl bg-slate-100 font-bold text-slate-600 hover:bg-slate-200 transition">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-white font-bold shadow-md transition flex items-center gap-1.5" :class="tindakan === 'lulus' ? 'bg-blue-600 hover:bg-blue-700' : (tindakan === 'serah' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700')">
                        <i class="fa-solid" :class="tindakan === 'lulus' ? 'fa-check' : (tindakan === 'serah' ? 'fa-check-double' : 'fa-xmark')"></i>
                        <span x-text="tindakan === 'lulus' ? 'Sahkan Kelulusan' : (tindakan === 'serah' ? 'Sahkan Penyerahan Stok' : 'Sahkan Penolakan')"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
