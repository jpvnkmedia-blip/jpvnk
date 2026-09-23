@extends('layouts.app')

@section('title', 'Profil Pengguna - ' . $targetUser->name)
@section('page_title', 'Maklumat Terperinci Pengguna')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Card -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-amber-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border border-slate-700">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-amber-500 text-slate-950 flex items-center justify-center text-2xl font-black shadow-lg">
                {{ strtoupper(substr($targetUser->name, 0, 1)) }}
            </div>
            <div>
                <div class="flex flex-wrap gap-1 mb-1.5">
                    @forelse($targetUser->roles_data as $rd)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full border text-[11px] font-bold bg-white/15 border-white/30 text-amber-300">
                            <i class="fa-solid {{ $rd['icon'] }} text-[10px]"></i>
                            <span>{{ $rd['label'] }}</span>
                        </span>
                    @empty
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-500/20 border border-amber-500/40 text-amber-300 text-xs font-bold uppercase mb-1">
                            {{ $targetUser->role_label }}
                        </span>
                    @endforelse
                </div>
                <h2 class="text-2xl font-black">{{ $targetUser->name }}</h2>
                <div class="text-xs text-slate-300 flex items-center gap-2 mt-0.5">
                    <span><i class="fa-solid fa-id-card mr-1 text-slate-400"></i>{{ $targetUser->ic_number ?? '-' }}</span>
                    <span>&bull;</span>
                    <span><i class="fa-solid fa-location-dot mr-1 text-slate-400"></i>{{ $targetUser->jajahan ?? 'Seluruh Kelantan' }}</span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('users.edit', $targetUser->id) }}" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold transition flex items-center gap-1.5 shadow">
                <i class="fa-solid fa-pen-to-square"></i> Kemaskini
            </a>
            <a href="{{ route('users.index') }}" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white text-xs font-bold transition">
                Kembali
            </a>
        </div>
    </div>

    <!-- User Information Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
        
        <!-- Personal & Account Details -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs space-y-4">
            <div class="font-bold text-slate-900 text-sm pb-2 border-b border-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-address-card text-amber-600"></i>
                <span>Maklumat Akaun &amp; Hubungan</span>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">Nama Penuh</span>
                    <span class="font-bold text-slate-900 text-right">{{ $targetUser->name }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">No. Kad Pengenalan</span>
                    <span class="font-mono font-bold text-slate-900 text-right">{{ $targetUser->ic_number ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">Alamat Emel</span>
                    <span class="font-medium text-slate-900 text-right">{{ $targetUser->email }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">No. Telefon</span>
                    <span class="font-medium text-slate-900 text-right">{{ $targetUser->phone ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">Jajahan</span>
                    <span class="font-bold text-slate-900 text-right">{{ $targetUser->jajahan ?? 'Seluruh Kelantan' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">Status Akaun</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $targetUser->status === 'Aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                        {{ $targetUser->status ?? 'Aktif' }}
                    </span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500">Tarikh Daftar</span>
                    <span class="text-slate-700 text-right">{{ $targetUser->created_at ? $targetUser->created_at->format('d/m/Y h:i A') : '-' }}</span>
                </div>
                @if($targetUser->signature)
                    <div class="flex justify-between items-center py-2 border-b border-slate-50">
                        <span class="text-slate-500">Tandatangan Digital</span>
                        <div class="bg-slate-50 border border-slate-200 p-1.5 rounded-xl shadow-xs">
                            <img src="{{ asset('storage/' . $targetUser->signature) }}" alt="Tandatangan {{ $targetUser->name }}" class="h-10 max-w-[140px] object-contain">
                        </div>
                    </div>
                @endif
            </div>

            <div class="pt-2">
                <div class="text-[11px] font-bold text-slate-500 mb-1">Alamat Kediaman / Pejabat:</div>
                <div class="p-3 bg-slate-50 rounded-xl text-slate-800 leading-relaxed">
                    {{ $targetUser->address ?? 'Tiada alamat dinyatakan' }}
                </div>
            </div>
        </div>

        <!-- Activity & Linked Modules Overview -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs space-y-4">
            <div class="font-bold text-slate-900 text-sm pb-2 border-b border-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-emerald-600"></i>
                <span>Aktiviti &amp; Rekod Terpaut</span>
            </div>

            <div class="grid grid-cols-2 gap-3">
                @if($targetUser->pemunya)
                    <div class="p-3.5 bg-emerald-50 rounded-2xl border border-emerald-200">
                        <div class="text-[10px] font-bold text-emerald-800 uppercase">Ternakan EPTR</div>
                        <div class="text-xl font-black text-emerald-950 mt-0.5">{{ $targetUser->pemunya->ternakan->count() }} Ekor</div>
                        <div class="text-[10px] text-emerald-700 mt-1">Profil Pemunya Aktif</div>
                    </div>
                @endif

                <div class="p-3.5 bg-cyan-50 rounded-2xl border border-cyan-200">
                    <div class="text-[10px] font-bold text-cyan-800 uppercase">Permohonan Kursus</div>
                    <div class="text-xl font-black text-cyan-950 mt-0.5">{{ $targetUser->permohonanKursus->count() }} Kursus</div>
                    <div class="text-[10px] text-cyan-700 mt-1">Latihan Jabatan</div>
                </div>

                <div class="p-3.5 bg-rose-50 rounded-2xl border border-rose-200">
                    <div class="text-[10px] font-bold text-rose-800 uppercase">Temujanji Klinik</div>
                    <div class="text-xl font-black text-rose-950 mt-0.5">{{ $targetUser->temujanjiKlinik->count() }} Temujanji</div>
                    <div class="text-[10px] text-rose-700 mt-1">Rawatan Haiwan</div>
                </div>

                @if($targetUser->isStaff())
                    <div class="p-3.5 bg-indigo-50 rounded-2xl border border-indigo-200">
                        <div class="text-[10px] font-bold text-indigo-800 uppercase">Permohonan Stor</div>
                        <div class="text-xl font-black text-indigo-950 mt-0.5">{{ $targetUser->permohonanInventori->count() }} Permohonan</div>
                        <div class="text-[10px] text-indigo-700 mt-1">Bekalan &amp; Alatan</div>
                    </div>

                    <div class="p-3.5 bg-teal-50 rounded-2xl border border-teal-200">
                        <div class="text-[10px] font-bold text-teal-800 uppercase">Tempahan Kenderaan</div>
                        <div class="text-xl font-black text-teal-950 mt-0.5">{{ $targetUser->tempahanKenderaan->count() }} Tempahan</div>
                        <div class="text-[10px] text-teal-700 mt-1">Kenderaan Rasmi</div>
                    </div>
                @endif
            </div>

            <!-- Role Permissions Card -->
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200">
                <div class="font-bold text-slate-800 mb-1 flex items-center gap-1.5">
                    <i class="fa-solid fa-shield text-amber-500"></i>
                    <span>Hak Akses Modul:</span>
                </div>
                <div class="flex flex-wrap gap-1.5 mt-2">
                    @if($targetUser->canAccessEptr())
                        <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold text-[10px]">EPTR Ruminan</span>
                    @endif
                    @if($targetUser->canAccessPawah())
                        <span class="px-2 py-0.5 rounded bg-teal-100 text-teal-800 font-bold text-[10px]">Program Pawah</span>
                    @endif
                    @if($targetUser->canAccessEpu())
                        <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-bold text-[10px]">EPU Unggas</span>
                    @endif
                    @if($targetUser->canAccessKursus())
                        <span class="px-2 py-0.5 rounded bg-cyan-100 text-cyan-800 font-bold text-[10px]">Kursus</span>
                    @endif
                    @if($targetUser->canAccessKlinik())
                        <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-800 font-bold text-[10px]">Klinik Haiwan</span>
                    @endif
                    @if($targetUser->canAccessStorPejabat())
                        <span class="px-2 py-0.5 rounded bg-indigo-100 text-indigo-800 font-bold text-[10px]">Stor Pejabat</span>
                    @endif
                    @if($targetUser->canAccessStorUbat())
                        <span class="px-2 py-0.5 rounded bg-pink-100 text-pink-800 font-bold text-[10px]">Stor Ubat</span>
                    @endif
                    @if($targetUser->canAccessKenderaan())
                        <span class="px-2 py-0.5 rounded bg-teal-100 text-teal-800 font-bold text-[10px]">Kenderaan</span>
                    @endif
                    @if($targetUser->canAccessMedia())
                        <span class="px-2 py-0.5 rounded bg-indigo-100 text-indigo-800 font-bold text-[10px]">Unit Media</span>
                    @endif
                    @if($targetUser->canManageMedia())
                        <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-bold text-[10px]">Admin Media (Kelulusan)</span>
                    @endif
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
