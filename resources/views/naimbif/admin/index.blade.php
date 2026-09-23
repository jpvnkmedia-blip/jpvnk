@extends('layouts.app')

@section('title', in_array(Auth::user()->role, ['admin_naimbif_jajahan', 'admin_jajahan', 'admin_eptr_jajahan', 'pegawai_jajahan']) ? 'Permohonan Ladang JPV ' . (Auth::user()->jajahan ?? 'Jajahan') : 'Pengurusan Program NAIMbif Negeri')
@section('page-title', in_array(Auth::user()->role, ['admin_naimbif_jajahan', 'admin_jajahan', 'admin_eptr_jajahan', 'pegawai_jajahan']) ? 'Pengurusan Permohonan Ladang Bridlot (Pejabat JPV Jajahan ' . (Auth::user()->jajahan ?? 'Kelantan') . ')' : 'Pengurusan Program NAIMbif (Ladang Bridlot Pedaging)')

@section('content')
<div class="space-y-6">

    <!-- Top Role Banner & Quick Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Jumlah Permohonan -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Jumlah Permohonan</span>
                <span class="text-2xl font-black text-slate-900 mt-1 block font-mono">{{ $totalApps }}</span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">
                    {{ in_array(Auth::user()->role, ['admin_naimbif_jajahan', 'admin_jajahan', 'admin_eptr_jajahan', 'pegawai_jajahan']) ? 'Jajahan ' . (Auth::user()->jajahan ?? 'Kelantan') : 'Seluruh Negeri Kelantan' }}
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-cow"></i>
            </div>
        </div>

        <!-- Metric 2: Menunggu Siasatan Jajahan -->
        <a href="{{ route('naimbif.admin.index', ['status' => 'Dalam Semakan']) }}" class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between hover:border-amber-400 hover:shadow-md transition group">
            <div>
                <span class="text-xs font-bold text-amber-700 uppercase tracking-wider block">Menunggu Siasatan Jajahan</span>
                <span class="text-2xl font-black text-amber-600 mt-1 block font-mono">{{ $totalMenungguJajahan }}</span>
                <span class="text-[11px] text-slate-400 mt-0.5 block group-hover:text-amber-600 transition">Perlu siasatan premis &amp; syor</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-inner group-hover:scale-105 transition">
                <i class="fa-solid fa-clipboard-question"></i>
            </div>
        </a>

        <!-- Metric 3: Menunggu Kelulusan Negeri -->
        <a href="{{ route('naimbif.admin.index', ['status' => 'Disokong']) }}" class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between hover:border-blue-400 hover:shadow-md transition group">
            <div>
                <span class="text-xs font-bold text-blue-700 uppercase tracking-wider block">Disokong (Menunggu HQ)</span>
                <span class="text-2xl font-black text-blue-600 mt-1 block font-mono">{{ $totalMenungguNegeri }}</span>
                <span class="text-[11px] text-slate-400 mt-0.5 block group-hover:text-blue-600 transition">Tindakan kelulusan Negeri</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-inner group-hover:scale-105 transition">
                <i class="fa-solid fa-stamp"></i>
            </div>
        </a>

        <!-- Metric 4: Permohonan Diluluskan -->
        <a href="{{ route('naimbif.admin.index', ['status' => 'Lulus']) }}" class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between hover:border-emerald-400 hover:shadow-md transition group">
            <div>
                <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider block">Permohonan Lulus</span>
                <span class="text-2xl font-black text-emerald-600 mt-1 block font-mono">{{ $totalLulus }}</span>
                <span class="text-[11px] text-slate-400 mt-0.5 block group-hover:text-emerald-600 transition">Peserta rasmi ladang bridlot</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-inner group-hover:scale-105 transition">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </a>
    </div>

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
                @if(in_array(Auth::user()->role, ['admin_naimbif_jajahan', 'admin_jajahan', 'admin_eptr_jajahan', 'pegawai_jajahan']) && Auth::user()->jajahan)
                    <div class="relative">
                        <input type="text" value="Jajahan {{ Auth::user()->jajahan }} (Terkunci)" readonly
                               class="w-full px-3 py-2 text-xs rounded-xl border border-emerald-300 bg-emerald-50 font-bold text-emerald-800 cursor-not-allowed">
                        <i class="fas fa-lock absolute right-3 top-2.5 text-emerald-600 text-xs"></i>
                    </div>
                @else
                    <select name="jajahan" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-emerald-500">
                        <option value="">-- Semua Jajahan (Negeri) --</option>
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
                    <option value="Disokong" {{ request('status') == 'Disokong' ? 'selected' : '' }}>Disokong (Menunggu HQ Negeri)</option>
                    <option value="Tidak Disokong" {{ request('status') == 'Tidak Disokong' ? 'selected' : '' }}>Tidak Disokong Jajahan</option>
                    <option value="Lulus" {{ request('status') == 'Lulus' ? 'selected' : '' }}>Lulus Jabatan (Negeri)</option>
                    <option value="Gagal" {{ request('status') == 'Gagal' ? 'selected' : '' }}>Ditolak Jabatan</option>
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition-colors flex items-center justify-center shadow-sm">
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
                                @if(Auth::user()->isSuperAdmin())
                                <form action="{{ route('naimbif.admin.destroy', $app->id) }}" method="POST" class="inline" onsubmit="return confirm('PERINGATAN SUPER ADMIN: Adakah anda pasti ingin memadam permohonan NAIMbif {{ $app->no_rujukan }} secara kekal? Tindakan ini tidak boleh diundur!');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200" title="Padam Permohonan (Super Admin Sahaja)">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @endif
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
