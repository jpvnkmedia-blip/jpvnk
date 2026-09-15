@extends('layouts.app')

@section('title', 'Rekod Permohonan Stor Saya')
@section('page_title', 'Status & Rekod Permohonan Stor Staf')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-900 font-black text-xs uppercase tracking-wider">
                    <i class="fa-solid fa-clipboard-list mr-1"></i> Permohonan Staf
                </span>
                <span class="text-xs text-slate-500 font-semibold">{{ Auth::user()->name }} ({{ Auth::user()->role_label }})</span>
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 mt-1">Senarai Permohonan Alatan Pejabat &amp; Ubat Saya</h2>
            <p class="text-xs text-slate-500 mt-0.5">Pantau status kelulusan dan serahan bekalan alat tulis, kertas, toner, vaksin dan ubat-ubatan veterinar yang telah dimohon.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('inventori.permohonan.pejabat.mohon') }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-lg shadow-indigo-700/30 transition flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked"></i>
                <span>Mohon Alatan Pejabat</span>
            </a>

            @if(Auth::user()->canRequestUbat())
            <a href="{{ route('inventori.permohonan.ubat.mohon') }}" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-lg shadow-rose-700/30 transition flex items-center gap-2">
                <i class="fa-solid fa-pills"></i>
                <span>Mohon Ubat / Vaksin (Klinik Jajahan)</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Summary KPI Pills -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase">Jumlah Permohonan</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $totalPermohonan }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Keseluruhan rekod</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase">Menunggu Semakan</div>
            <div class="text-2xl font-black text-amber-600 mt-1">{{ $menungguCount }}</div>
            <div class="text-[10px] text-amber-700 mt-0.5 font-semibold">Tindakan admin stor</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase">Telah Diluluskan</div>
            <div class="text-2xl font-black text-blue-600 mt-1">{{ $lulusCount }}</div>
            <div class="text-[10px] text-blue-700 mt-0.5 font-semibold">Sedia untuk serahan</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase">Selesai / Diserahkan</div>
            <div class="text-2xl font-black text-emerald-700 mt-1">{{ $selesaiCount }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Stok telah diterima</div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex flex-wrap gap-2 text-xs">
        <a href="{{ route('inventori.permohonan.saya') }}" class="px-3.5 py-1.5 rounded-full font-bold transition {{ !request('jenis_stor') && !request('status') ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            Semua Permohonan
        </a>
        <a href="{{ route('inventori.permohonan.saya', ['jenis_stor' => 'pejabat']) }}" class="px-3.5 py-1.5 rounded-full font-bold transition {{ request('jenis_stor') === 'pejabat' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            <i class="fa-solid fa-boxes-stacked mr-1"></i> Stor Pejabat
        </a>
        <a href="{{ route('inventori.permohonan.saya', ['jenis_stor' => 'ubat']) }}" class="px-3.5 py-1.5 rounded-full font-bold transition {{ request('jenis_stor') === 'ubat' ? 'bg-rose-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            <i class="fa-solid fa-pills mr-1"></i> Stor Ubat &amp; Farmasi
        </a>
        <a href="{{ route('inventori.permohonan.saya', ['status' => 'Menunggu Kelulusan']) }}" class="px-3.5 py-1.5 rounded-full font-bold transition {{ request('status') === 'Menunggu Kelulusan' ? 'bg-amber-600 text-white shadow-xs' : 'bg-amber-50 text-amber-800 hover:bg-amber-100 border border-amber-200' }}">
            Menunggu
        </a>
        <a href="{{ route('inventori.permohonan.saya', ['status' => 'Diluluskan']) }}" class="px-3.5 py-1.5 rounded-full font-bold transition {{ request('status') === 'Diluluskan' ? 'bg-blue-600 text-white shadow-xs' : 'bg-blue-50 text-blue-800 hover:bg-blue-100 border border-blue-200' }}">
            Diluluskan
        </a>
    </div>

    <!-- Requisitions Table -->
    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-bold text-[11px]">
                        <th class="px-5 py-3.5">No. Permohonan</th>
                        <th class="px-4 py-3.5">Jenis Stor &amp; Item Dimohon</th>
                        <th class="px-4 py-3.5">Kuantiti (Mohon / Lulus)</th>
                        <th class="px-4 py-3.5">Tujuan &amp; Tarikh Diperlukan</th>
                        <th class="px-4 py-3.5">Status Kelulusan</th>
                        <th class="px-5 py-3.5 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($permohonans as $p)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5 font-mono">
                                <span class="font-bold text-slate-900 block">{{ $p->no_permohonan }}</span>
                                <span class="text-[10px] text-slate-400">{{ $p->created_at ? $p->created_at->format('d/m/Y H:i') : '-' }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold mb-1 {{ $p->isStorUbat() ? 'bg-rose-50 text-rose-800 border border-rose-200' : 'bg-indigo-50 text-indigo-800 border border-indigo-200' }}">
                                    <i class="fa-solid {{ $p->isStorUbat() ? 'fa-pills' : 'fa-boxes-stacked' }} mr-1"></i>
                                    {{ $p->isStorUbat() ? 'Stor Ubat' : 'Stor Pejabat' }}
                                </span>
                                <div class="font-bold text-slate-900">{{ $p->item->nama_item ?? 'Item Stor' }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">{{ $p->item->kod_item ?? '-' }} &bull; {{ $p->item->kategori ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-slate-900 text-sm">
                                    {{ number_format($p->kuantiti_dimohon) }} {{ $p->item->unit ?? 'Unit' }}
                                </div>
                                @if($p->kuantiti_diluluskan !== null)
                                    <div class="text-[11px] text-emerald-700 font-bold">
                                        Diluluskan: {{ number_format($p->kuantiti_diluluskan) }} {{ $p->item->unit ?? 'Unit' }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 max-w-xs">
                                <div class="text-slate-800 line-clamp-2">{{ $p->tujuan_permohonan }}</div>
                                <div class="text-[10px] text-slate-500 mt-1">
                                    <i class="fa-regular fa-calendar mr-1"></i>Diperlukan: <b>{{ $p->tarikh_diperlukan ? $p->tarikh_diperlukan->format('d/m/Y') : 'Segera' }}</b> &bull; {{ $p->unit_bahagian }}
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $p->status_badge }}">
                                    {{ $p->status }}
                                </span>
                                @if($p->catatan_pegawai)
                                    <div class="text-[10px] text-slate-500 mt-1 italic">
                                        "{{ $p->catatan_pegawai }}"
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                @if($p->isPending())
                                    <form action="{{ route('inventori.permohonan.batal', $p->id) }}" method="POST" onsubmit="return confirm('Adakah anda pasti mahu membatalkan permohonan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition">
                                            Batal Permohonan
                                        </button>
                                    </form>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-clipboard-question text-3xl mb-2 text-slate-300"></i>
                                <div class="font-bold text-slate-600">Tiada rekod permohonan stor dijumpai</div>
                                <div class="text-xs text-slate-400 mt-0.5">Sila gunakan butang di atas untuk memohon alatan pejabat atau ubat/vaksin veterinar.</div>
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
