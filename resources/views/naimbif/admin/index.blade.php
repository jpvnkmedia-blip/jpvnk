@extends('layouts.app')

@section('title', Auth::user()->role === 'pegawai_jajahan' ? 'Permohonan JPV ' . Auth::user()->jajahan : 'Senarai Permohonan')
@section('page-title', Auth::user()->role === 'pegawai_jajahan' ? 'Pengurusan Permohonan Ladang Bridlot (Pejabat JPV Jajahan ' . Auth::user()->jajahan . ')' : 'Pengurusan Permohonan Ladang Bridlot')

@section('content')
<div class="space-y-6">

    <!-- Filters & Actions Header -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
        <form action="{{ route('naimbif.admin.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Search Text -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Carian</label>
                <div class="relative">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Nama / No KP / No Rujukan..."
                           class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-emerald-500">
                    <i class="fas fa-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                </div>
            </div>

            <!-- Jajahan Filter -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Jajahan</label>
                @if(Auth::user()->role === 'pegawai_jajahan')
                    <input type="text" value="Jajahan {{ Auth::user()->jajahan }}" readonly
                           class="w-full px-3 py-2 text-xs rounded-xl border border-emerald-300 bg-emerald-50 font-bold text-emerald-800 cursor-not-allowed">
                @else
                    <select name="jajahan" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-emerald-500">
                        <option value="">-- Semua Jajahan --</option>
                        @foreach($jajahans as $j)
                            <option value="{{ $j }}" {{ request('jajahan') == $j ? 'selected' : '' }}>{{ $j }}</option>
                        @endforeach
                    </select>
                @endif
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Status Permohonan</label>
                <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-emerald-500">
                    <option value="">-- Semua Status --</option>
                    <option value="Dalam Semakan" {{ request('status') == 'Dalam Semakan' ? 'selected' : '' }}>Dalam Semakan (Jajahan)</option>
                    <option value="Disokong" {{ request('status') == 'Disokong' ? 'selected' : '' }}>Disokong (Menunggu Jabatan)</option>
                    <option value="Tidak Disokong" {{ request('status') == 'Tidak Disokong' ? 'selected' : '' }}>Tidak Disokong Jajahan</option>
                    <option value="Lulus" {{ request('status') == 'Lulus' ? 'selected' : '' }}>Lulus Jabatan</option>
                    <option value="Gagal" {{ request('status') == 'Gagal' ? 'selected' : '' }}>Ditolak Jabatan</option>
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition-colors flex items-center justify-center">
                    <i class="fas fa-filter mr-1.5"></i> Tapis Rekod
                </button>
                <a href="{{ route('naimbif.admin.index') }}" class="px-3 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200">
                    Reset
                </a>
                <a href="{{ route('naimbif.admin.export', request()->query()) }}" class="px-3 py-2 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-slate-800" title="Eksport ke CSV">
                    <i class="fas fa-file-csv"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Applications Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-xs">
                <thead class="bg-slate-50 text-slate-600 font-bold uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-6 text-left">No. Rujukan / Tarikh</th>
                        <th class="py-3.5 px-4 text-left">Pemohon (Nama & No. KP)</th>
                        <th class="py-3.5 px-4 text-left">Jajahan / Ladang</th>
                        <th class="py-3.5 px-4 text-left">ID Premis</th>
                        <th class="py-3.5 px-4 text-center">Stok Ternakan</th>
                        <th class="py-3.5 px-4 text-left">Status Permohonan</th>
                        <th class="py-3.5 px-6 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($applications as $app)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- No Rujukan & Tarikh -->
                            <td class="py-4 px-6 whitespace-nowrap">
                                <div class="font-mono font-bold text-emerald-800 text-sm">{{ $app->no_rujukan }}</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $app->tarikh_permohonan ? $app->tarikh_permohonan->format('d/m/Y') : $app->created_at->format('d/m/Y') }}</div>
                            </td>

                            <!-- Pemohon -->
                            <td class="py-4 px-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $app->nama }}</div>
                                <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                                    {{ $app->formatted_no_kp }} • <span class="text-slate-600">{{ $app->no_telefon }}</span>
                                </div>
                            </td>

                            <!-- Jajahan / Ladang -->
                            <td class="py-4 px-4">
                                <div class="font-bold text-slate-800">{{ $app->jajahan_ladang ?: $app->jajahan }}</div>
                                <div class="text-[11px] text-slate-500">{{ $app->keluasan_tanah }} Ekar ({{ $app->status_tanah }})</div>
                            </td>

                            <!-- ID Premis -->
                            <td class="py-4 px-4">
                                @if($app->id_premis)
                                    <span class="font-mono font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">{{ $app->id_premis }}</span>
                                @else
                                    <span class="text-slate-400 italic">Belum Dijana</span>
                                @endif
                            </td>

                            <!-- Stok Ternakan -->
                            <td class="py-4 px-4 text-center">
                                <span class="px-2.5 py-1 bg-slate-100 rounded-lg text-emerald-700 font-bold font-mono text-xs">
                                    {{ $app->total_ternakan }} ekor
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="py-4 px-4">
                                {!! $app->status_badge !!}
                            </td>

                            <!-- Tindakan -->
                            <td class="py-4 px-6 text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('naimbif.admin.show', $app->id) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 shadow-sm" title="Semak & Ulas">
                                    <i class="fas fa-edit mr-1"></i> Semak
                                </a>
                                <a href="{{ route('naimbif.public.print', $app->no_rujukan) }}" target="_blank" class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300" title="Cetak Borang A4">
                                    <i class="fas fa-print"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i class="fas fa-folder-open text-3xl mb-2 text-slate-300 block"></i>
                                Tiada permohonan dijumpai dengan kriteria tapisan semasa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($applications->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $applications->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
