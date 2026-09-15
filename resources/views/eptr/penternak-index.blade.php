@extends('layouts.app')

@section('title', 'Senarai Penternak EPTR - Jabatan Perkhidmatan Veterinar Negeri Kelantan')
@section('page_title', 'Direktori & Senarai Penternak EPTR')

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center text-lg font-bold">
                    <i class="fa-solid fa-users"></i>
                </span>
                <span>Direktori Penternak & Pemunya Ternakan</span>
            </h2>
            <p class="text-xs text-slate-500 mt-1">Pengurusan maklumat pemunya ternakan ruminan, status pendaftaran dan inventori ternakan aktif mengikut jajahan</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('eptr.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-1.5 shadow-2xs">
                <i class="fa-solid fa-cow text-amber-600"></i>
                <span>Senarai Ternakan (EPTR)</span>
            </a>
            <a href="{{ route('eptr.borang-b.index') }}" class="px-3.5 py-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-800 border border-blue-300 text-xs font-bold transition flex items-center gap-1.5 shadow-2xs">
                <i class="fa-solid fa-arrows-rotate text-blue-600"></i>
                <span>Pindah Milik (Borang B)</span>
            </a>
            <a href="{{ route('eptr.kesihatan.index') }}" class="px-3.5 py-2 rounded-xl bg-teal-50 hover:bg-teal-100 text-teal-800 border border-teal-300 text-xs font-bold transition flex items-center gap-1.5 shadow-2xs">
                <i class="fa-solid fa-heart-pulse text-teal-600"></i>
                <span>Program Kesihatan</span>
            </a>
            @if(!Auth::user()->isStaff() || Auth::user()->isSuperAdmin())
            <a href="{{ route('eptr.create') }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-lg shadow-emerald-700/30 transition flex items-center gap-1.5">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Daftar Ternakan (Borang A)</span>
            </a>
            @endif
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
        <!-- Jumlah Penternak -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <div class="text-xl font-black text-slate-900">{{ number_format($totalPenternak) }}</div>
                <div class="text-[11px] text-slate-500 font-semibold">Jumlah Penternak</div>
            </div>
        </div>

        <!-- Penternak Aktif -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <div>
                <div class="text-xl font-black text-emerald-700">{{ number_format($totalAktif) }}</div>
                <div class="text-[11px] text-slate-500 font-semibold">Penternak Aktif</div>
            </div>
        </div>

        <!-- Memiliki Ternakan -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <div>
                <div class="text-xl font-black text-amber-800">{{ number_format($totalAdaTernakan) }}</div>
                <div class="text-[11px] text-slate-500 font-semibold">Memiliki Ternakan</div>
            </div>
        </div>

        <!-- Peserta Program Pawah -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-handshake"></i>
            </div>
            <div>
                <div class="text-xl font-black text-indigo-700">{{ number_format($totalPenternakPawah) }}</div>
                <div class="text-[11px] text-slate-500 font-semibold">Peserta Pawah / Skim</div>
            </div>
        </div>

        <!-- Jumlah Ternakan Aktif (Breakdown) -->
        <div class="p-4 rounded-2xl bg-slate-900 text-white shadow-md flex flex-col justify-between col-span-2 sm:col-span-3 lg:col-span-1">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-300">Ternakan Aktif</span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 font-bold">{{ number_format($totalTernakanAktif) }} Ekor</span>
            </div>
            <div class="grid grid-cols-4 gap-1 text-center mt-2 pt-2 border-t border-slate-800 text-[10px]">
                <div title="Lembu">
                    <span class="block text-slate-400 font-medium">🐮</span>
                    <span class="font-bold text-slate-100">{{ $totalLembu }}</span>
                </div>
                <div title="Kambing">
                    <span class="block text-slate-400 font-medium">🐐</span>
                    <span class="font-bold text-slate-100">{{ $totalKambing }}</span>
                </div>
                <div title="Kerbau">
                    <span class="block text-slate-400 font-medium">🦬</span>
                    <span class="font-bold text-slate-100">{{ $totalKerbau }}</span>
                </div>
                <div title="Biri-biri">
                    <span class="block text-slate-400 font-medium">🐑</span>
                    <span class="font-bold text-slate-100">{{ $totalBiri }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-2xs">
        <form action="{{ route('eptr.penternak.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 text-xs">
            
            <!-- Carian Kata Kunci -->
            <div class="lg:col-span-2">
                <label class="block font-bold text-slate-600 mb-1">Carian Penternak</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / No. KP / No. Tel / Daerah..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400"></i>
                </div>
            </div>

            <!-- Jajahan -->
            <div>
                <label class="block font-bold text-slate-600 mb-1">Jajahan</label>
                <select name="jajahan" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="">-- Semua Jajahan --</option>
                    @foreach(['Kota Bharu', 'Pasir Mas', 'Tumpat', 'Bachok', 'Pasir Puteh', 'Machang', 'Tanah Merah', 'Jeli', 'Kuala Krai', 'Gua Musang'] as $j)
                        <option value="{{ $j }}" {{ request('jajahan') == $j ? 'selected' : '' }}>{{ $j }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Kategori Pemilikan -->
            <div>
                <label class="block font-bold text-slate-600 mb-1">Kategori Ternakan</label>
                <select name="kategori_ternakan" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="">Semua Kategori</option>
                    <option value="ada_ternakan" {{ request('kategori_ternakan') == 'ada_ternakan' ? 'selected' : '' }}>Memiliki Ternakan</option>
                    <option value="tiada_ternakan" {{ request('kategori_ternakan') == 'tiada_ternakan' ? 'selected' : '' }}>Tiada Ternakan (0 Ekor)</option>
                    <option value="pawah" {{ request('kategori_ternakan') == 'pawah' ? 'selected' : '' }}>Peserta Pawah / Skim</option>
                </select>
            </div>

            <!-- Penapis Spesies Dimiliki -->
            <div>
                <label class="block font-bold text-slate-600 mb-1">Spesies Ternakan</label>
                <select name="spesies" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="">Semua Spesies</option>
                    <option value="Lembu" {{ request('spesies') == 'Lembu' ? 'selected' : '' }}>Lembu</option>
                    <option value="Kambing" {{ request('spesies') == 'Kambing' ? 'selected' : '' }}>Kambing</option>
                    <option value="Kerbau" {{ request('spesies') == 'Kerbau' ? 'selected' : '' }}>Kerbau</option>
                    <option value="Biri-biri" {{ request('spesies') == 'Biri-biri' ? 'selected' : '' }}>Biri-biri</option>
                </select>
            </div>

            <!-- Butang Tindakan -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl transition flex items-center justify-center gap-1.5 shadow-xs">
                    <i class="fa-solid fa-filter"></i>
                    <span>Tapis</span>
                </button>
                <a href="{{ route('eptr.penternak.index') }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition" title="Reset Penapis">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Senarai Penternak Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-slate-50/50">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Senarai Pemunya Ternakan Berdaftar</h3>
                <p class="text-xs text-slate-500">Memaparkan {{ $penternakList->firstItem() ?? 0 }} - {{ $penternakList->lastItem() ?? 0 }} daripada {{ $penternakList->total() }} rekod penternak</p>
            </div>
            @if(request()->anyFilled(['search', 'jajahan', 'status', 'kategori_ternakan', 'spesies']))
                <div class="flex items-center gap-1.5 text-xs text-amber-700 bg-amber-50 px-3 py-1.5 rounded-xl border border-amber-200">
                    <i class="fa-solid fa-filter"></i>
                    <span>Penapis aktif diaktifkan</span>
                    <a href="{{ route('eptr.penternak.index') }}" class="underline font-bold ml-1 hover:text-amber-900">Kosongkan</a>
                </div>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-bold text-[11px]">
                        <th class="px-4 py-3.5 w-12 text-center">#</th>
                        <th class="px-4 py-3.5">Nama Penternak & No. KP</th>
                        <th class="px-4 py-3.5">Maklumat Hubungan & Lokasi</th>
                        <th class="px-4 py-3.5">Jajahan & Daerah</th>
                        <th class="px-4 py-3.5">Pemilikan Ternakan</th>
                        <th class="px-4 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($penternakList as $index => $p)
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Bilangan -->
                            <td class="px-4 py-3.5 text-center text-slate-400 font-mono">
                                {{ $penternakList->firstItem() + $index }}
                            </td>

                            <!-- Nama & No. KP -->
                            <td class="px-4 py-3.5">
                                <div class="flex items-start gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-sky-100 text-sky-800 font-black flex items-center justify-center text-xs shrink-0 shadow-2xs">
                                        {{ strtoupper(substr($p->nama, 0, 2)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('eptr.penternak.show', $p->id) }}" class="font-bold text-slate-900 hover:text-sky-600 hover:underline flex items-center gap-1.5">
                                            <span>{{ $p->nama }}</span>
                                        </a>
                                        <div class="flex items-center gap-2 mt-0.5 font-mono text-[11px] text-slate-500">
                                            <i class="fa-regular fa-id-card text-slate-400"></i>
                                            <span>{{ $p->no_kp }}</span>
                                        </div>
                                        @if($p->user)
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-medium mt-1">
                                                <i class="fa-solid fa-user-check text-emerald-500"></i> Akaun Sistem ({{ $p->user->email }})
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Hubungan & Alamat -->
                            <td class="px-4 py-3.5">
                                <div class="space-y-1">
                                    @if($p->no_telefon)
                                        <div class="flex items-center gap-1.5 text-slate-700 font-medium">
                                            <i class="fa-solid fa-phone text-slate-400 w-3 text-center"></i>
                                            <a href="tel:{{ $p->no_telefon }}" class="hover:underline text-slate-800">{{ $p->no_telefon }}</a>
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic">Tiada no. tel</span>
                                    @endif

                                    <div class="text-slate-500 text-[11px] max-w-xs truncate" title="{{ $p->alamat }}">
                                        <i class="fa-solid fa-location-dot text-slate-400 w-3 text-center"></i>
                                        <span>{{ $p->alamat ?: 'Alamat belum dikemaskini' }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Jajahan & Daerah -->
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-slate-800">{{ $p->jajahan }}</div>
                                <div class="text-[11px] text-slate-500">{{ $p->daerah ?: '-' }} {{ $p->poskod ? "({$p->poskod})" : '' }}</div>
                            </td>

                            <!-- Pemilikan Ternakan & Pecahan Spesies -->
                            <td class="px-4 py-3.5">
                                <div class="space-y-1.5">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-bold {{ $p->ternakan_aktif_count > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $p->ternakan_aktif_count }} Ekor Aktif
                                        </span>
                                        @if($p->ternakan_count > $p->ternakan_aktif_count)
                                            <span class="text-[10px] text-slate-400">({{ $p->ternakan_count }} jumlah rekod)</span>
                                        @endif
                                    </div>

                                    <!-- Species Pills -->
                                    <div class="flex flex-wrap gap-1 text-[10px]">
                                        @if($p->lembu_count > 0)
                                            <span class="px-1.5 py-0.5 rounded bg-amber-50 text-amber-800 border border-amber-200 font-medium" title="Lembu">
                                                🐮 {{ $p->lembu_count }}
                                            </span>
                                        @endif
                                        @if($p->kambing_count > 0)
                                            <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200 font-medium" title="Kambing">
                                                🐐 {{ $p->kambing_count }}
                                            </span>
                                        @endif
                                        @if($p->kerbau_count > 0)
                                            <span class="px-1.5 py-0.5 rounded bg-blue-50 text-blue-800 border border-blue-200 font-medium" title="Kerbau">
                                                🦬 {{ $p->kerbau_count }}
                                            </span>
                                        @endif
                                        @if($p->biri_count > 0)
                                            <span class="px-1.5 py-0.5 rounded bg-purple-50 text-purple-800 border border-purple-200 font-medium" title="Biri-biri">
                                                🐑 {{ $p->biri_count }}
                                            </span>
                                        @endif
                                        @if($p->ternakan_count == 0)
                                            <span class="text-slate-400 italic text-[10px]">Tiada pautan ternakan</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Status Penternak -->
                            <td class="px-4 py-3.5 text-center">
                                @if($p->status === 'Aktif')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400 mr-1.5"></span>
                                        {{ $p->status ?: 'Tidak Aktif' }}
                                    </span>
                                @endif
                            </td>

                            <!-- Tindakan -->
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('eptr.penternak.show', $p->id) }}" class="px-3 py-1.5 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-700 font-bold transition flex items-center gap-1" title="Lihat Profil Lengkap Penternak">
                                        <i class="fa-solid fa-id-badge"></i>
                                        <span>Profil</span>
                                    </a>
                                    <a href="{{ route('eptr.index', ['pemunya_id' => $p->id]) }}" class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition" title="Lihat Senarai Ternakan Pemunya Ini">
                                        <i class="fa-solid fa-cow"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-2xl">
                                    <i class="fa-solid fa-user-slash"></i>
                                </div>
                                <h4 class="font-bold text-slate-700 text-sm">Tiada Rekod Penternak Ditemui</h4>
                                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Tiada penternak yang sepadan dengan kriteria carian atau penapis yang dipilih.</p>
                                @if(request()->anyFilled(['search', 'jajahan', 'status', 'kategori_ternakan', 'spesies']))
                                    <a href="{{ route('eptr.penternak.index') }}" class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 rounded-xl bg-slate-800 text-white font-bold text-xs hover:bg-slate-700 transition">
                                        <i class="fa-solid fa-rotate-left"></i>
                                        <span>Reset Semua Penapis</span>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($penternakList->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $penternakList->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
