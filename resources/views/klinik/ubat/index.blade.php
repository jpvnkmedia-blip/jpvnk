@extends('layouts.app')

@section('title', 'Permohonan Ubat & Farmasi Klinik Haiwan')
@section('page_title', 'Permohonan Ubat & Farmasi dari Klinik Haiwan')

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-lg bg-rose-100 text-rose-900 font-black text-xs uppercase tracking-wider">
                    <i class="fa-solid fa-stethoscope mr-1"></i> Klinik Veterinar &amp; Rawatan
                </span>
                <span class="text-xs text-slate-500 font-semibold">&bull; Permohonan Bekalan Farmasi</span>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 mt-1">Permohonan Ubat &amp; Vaksin ke Stor Farmasi</h2>
            <p class="text-xs text-slate-500 mt-0.5">Pesanan bekalan ubat, vaksin, antibiotik, ubat bius dan kelengkapan klinikal veterinar dari Stor Ubat &amp; Farmasi Pusat.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('klinik.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Klinik</span>
            </a>
            <a href="{{ route('klinik.permohonan_ubat.create') }}" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-lg shadow-rose-700/30 transition flex items-center gap-2">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Mohon Ubat / Vaksin Baru</span>
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase">Jumlah Permohonan</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $totalPermohonan }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Keseluruhan pesanan klinik</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase">Menunggu Kelulusan</div>
            <div class="text-2xl font-black text-amber-600 mt-1">{{ $menungguCount }}</div>
            <div class="text-[10px] text-amber-700 mt-0.5 font-semibold">Sedang diproses Stor Farmasi</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase">Telah Diluluskan</div>
            <div class="text-2xl font-black text-blue-600 mt-1">{{ $lulusCount }}</div>
            <div class="text-[10px] text-blue-700 mt-0.5 font-semibold">Sedia untuk pengambilan stok</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase">Selesai Diserahkan</div>
            <div class="text-2xl font-black text-emerald-700 mt-1">{{ $selesaiCount }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Stok telah diterima di klinik</div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-3xl border border-slate-200 p-4 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex flex-wrap gap-2 text-xs">
            <a href="{{ route('klinik.permohonan_ubat.index') }}" class="px-3.5 py-1.5 rounded-full font-bold transition {{ !request('status') ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua
            </a>
            <a href="{{ route('klinik.permohonan_ubat.index', ['status' => 'Menunggu Kelulusan']) }}" class="px-3.5 py-1.5 rounded-full font-bold transition {{ request('status') === 'Menunggu Kelulusan' ? 'bg-amber-600 text-white shadow-xs' : 'bg-amber-50 text-amber-800 hover:bg-amber-100 border border-amber-200' }}">
                ⚠️ Menunggu ({{ $menungguCount }})
            </a>
            <a href="{{ route('klinik.permohonan_ubat.index', ['status' => 'Diluluskan']) }}" class="px-3.5 py-1.5 rounded-full font-bold transition {{ request('status') === 'Diluluskan' ? 'bg-blue-600 text-white shadow-xs' : 'bg-blue-50 text-blue-800 hover:bg-blue-100 border border-blue-200' }}">
                Diluluskan ({{ $lulusCount }})
            </a>
            <a href="{{ route('klinik.permohonan_ubat.index', ['status' => 'Telah Diambil / Diserahkan']) }}" class="px-3.5 py-1.5 rounded-full font-bold transition {{ request('status') === 'Telah Diambil / Diserahkan' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Selesai ({{ $selesaiCount }})
            </a>
            <a href="{{ route('klinik.permohonan_ubat.index', ['status' => 'Ditolak']) }}" class="px-3.5 py-1.5 rounded-full font-bold transition {{ request('status') === 'Ditolak' ? 'bg-rose-600 text-white shadow-xs' : 'bg-rose-50 text-rose-800 hover:bg-rose-100 border border-rose-200' }}">
                Ditolak ({{ $ditolakCount }})
            </a>
        </div>

        <form action="{{ route('klinik.permohonan_ubat.index') }}" method="GET" class="flex items-center gap-2">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no. permohonan / ubat / tujuan..." class="rounded-xl border border-slate-200 pl-8 pr-3 py-1.5 text-xs bg-slate-50 focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none w-56 sm:w-72">
                <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-2.5 top-2.5 text-xs"></i>
            </div>
            <button type="submit" class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">
                Cari
            </button>
        </form>
    </div>

    <!-- Table of Clinic Medicine Requests -->
    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Senarai Rekod Permohonan Ubat &amp; Vaksin Klinik</h3>
            <span class="text-xs text-slate-500">Jumlah: <b>{{ $permohonans->total() }}</b> rekod</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-bold text-[11px]">
                        <th class="px-5 py-3.5">No. Permohonan</th>
                        <th class="px-4 py-3.5">Ubat / Vaksin Dimohon</th>
                        <th class="px-4 py-3.5">Kuantiti (Mohon / Lulus)</th>
                        <th class="px-4 py-3.5">Klinik / Unit &amp; Tujuan Rawatan</th>
                        <th class="px-4 py-3.5">Tarikh &amp; Pemohon</th>
                        <th class="px-4 py-3.5">Status &amp; Kelulusan</th>
                        <th class="px-5 py-3.5 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($permohonans as $p)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="px-5 py-4 font-mono font-bold text-slate-900">
                            {{ $p->no_permohonan }}
                            <div class="text-[10px] text-slate-400 font-sans font-normal mt-0.5">Stor Farmasi Pusat</div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                                <i class="fa-solid fa-pills text-rose-500"></i>
                                <span>{{ $p->item->nama_item ?? 'Item Dipadam' }}</span>
                            </div>
                            <div class="text-[10px] text-slate-500 mt-0.5">
                                <span class="font-mono">[{{ $p->item->kod_item ?? '-' }}]</span> &bull; {{ $p->item->kategori ?? 'Ubat Veterinar' }}
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="font-black text-slate-900 text-xs">
                                {{ $p->kuantiti_dimohon }} {{ $p->item->unit ?? 'unit' }}
                            </div>
                            @if($p->kuantiti_diluluskan)
                                <div class="text-[10px] font-bold text-emerald-600 mt-0.5">
                                    Diluluskan: {{ $p->kuantiti_diluluskan }} {{ $p->item->unit ?? 'unit' }}
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-4 max-w-xs">
                            <div class="font-semibold text-slate-800 text-[11px] truncate">
                                {{ $p->unit_bahagian ?? 'Klinik Haiwan' }}
                            </div>
                            <div class="text-[10px] text-slate-500 mt-0.5 line-clamp-2">
                                {{ $p->tujuan_permohonan }}
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-[11px] text-slate-700 font-medium">
                                Dimohon: {{ $p->created_at->format('d/m/Y') }}
                            </div>
                            <div class="text-[10px] text-slate-400">
                                Diperlukan: {{ $p->tarikh_diperlukan ? $p->tarikh_diperlukan->format('d/m/Y') : '-' }}
                            </div>
                            <div class="text-[10px] text-slate-500 font-medium mt-0.5">
                                Oleh: {{ $p->pemohon->name ?? '-' }}
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $p->status_badge }}">
                                {{ $p->status }}
                            </span>
                            @if($p->catatan_pegawai)
                                <div class="text-[10px] text-slate-500 mt-1 max-w-xs bg-slate-50 p-1.5 rounded-lg border border-slate-100">
                                    <span class="font-bold text-slate-700">Nota Pegawai Farmasi:</span> {{ $p->catatan_pegawai }}
                                </div>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            @if($p->isPending() && ($p->user_id === Auth::id() || Auth::user()->isSuperAdmin() || Auth::user()->isAdminKlinik()))
                                <form action="{{ route('klinik.permohonan_ubat.batal', $p->id) }}" method="POST" onsubmit="return confirm('Adakah anda pasti ingin membatalkan permohonan bekalan ubat {{ $p->no_permohonan }}?')" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-[11px] font-bold transition">
                                        <i class="fa-solid fa-ban mr-1"></i> Batal
                                    </button>
                                </form>
                            @else
                                <span class="text-[11px] text-slate-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                <i class="fa-solid fa-prescription-bottle-medical text-lg"></i>
                            </div>
                            <div class="font-bold text-slate-700">Tiada Rekod Permohonan Ubat Ditemui</div>
                            <div class="text-xs text-slate-400 mt-1">Klik butang "Mohon Ubat / Vaksin Baru" di atas untuk membuat pesanan bekalan ke Stor Farmasi.</div>
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

</div>
@endsection