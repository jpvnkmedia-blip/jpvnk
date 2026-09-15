@extends('layouts.app')

@section('title', 'Pusat Notifikasi & Aktiviti Pengguna')
@section('page_title', 'Pusat Notifikasi & Aktiviti Pengguna')

@section('content')
<div class="space-y-6">

    <!-- Header Card -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border border-slate-700">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs font-bold uppercase tracking-wider flex items-center gap-1.5">
                    <i class="fa-solid fa-bell"></i> Log Aktiviti &amp; Makluman Terkini
                </span>
                @if($unreadCount > 0)
                    <span class="px-2.5 py-0.5 rounded-full bg-rose-500/30 border border-rose-500/50 text-rose-300 text-xs font-extrabold animate-pulse">
                        {{ $unreadCount }} Belum Dibaca
                    </span>
                @endif
            </div>
            <h2 class="text-2xl sm:text-3xl font-black">Pusat Notifikasi Pengguna</h2>
            <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl">
                Pantau aktiviti harian, status permohonan ternakan, lesen, kursus, klinik, bekalan pejabat dan keselamatan akaun anda secara berpusat.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if($unreadCount > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-lg shadow-emerald-900/30 transition flex items-center gap-2">
                        <i class="fa-solid fa-check-double"></i>
                        <span>Tanda Semua Dibaca</span>
                    </button>
                </form>
            @endif

            @if($totalCount > 0)
                <form action="{{ route('notifications.clear-read') }}" method="POST" onsubmit="return confirm('Adakah anda pasti untuk memadam semua notifikasi yang telah dibaca?')">
                    @csrf
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white text-xs font-bold transition flex items-center gap-2">
                        <i class="fa-solid fa-trash-can"></i>
                        <span>Bersihkan Dibaca</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Metric Stat Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <!-- 1. Semua -->
        <a href="{{ route('notifications.index') }}" class="p-4 rounded-2xl border transition {{ empty($type) && $status === 'all' ? 'bg-slate-900 text-white border-slate-800 shadow-md' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Semua</span>
                <i class="fa-solid fa-layer-group text-sm text-slate-400"></i>
            </div>
            <div class="mt-2 text-xl font-black">{{ $totalCount }}</div>
        </a>

        <!-- 2. Belum Dibaca -->
        <a href="{{ route('notifications.index', ['status' => 'unread']) }}" class="p-4 rounded-2xl border transition {{ $status === 'unread' ? 'bg-rose-900 text-white border-rose-800 shadow-md' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider {{ $status === 'unread' ? 'text-rose-200' : 'text-rose-600' }}">Belum Baca</span>
                <i class="fa-solid fa-envelope text-sm text-rose-500"></i>
            </div>
            <div class="mt-2 text-xl font-black text-rose-600 {{ $status === 'unread' ? '!text-white' : '' }}">{{ $unreadCount }}</div>
        </a>

        <!-- 3. EPTR & Pawah -->
        <a href="{{ route('notifications.index', ['type' => 'eptr_pawah']) }}" class="p-4 rounded-2xl border transition {{ $type === 'eptr_pawah' ? 'bg-emerald-900 text-white border-emerald-800 shadow-md' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">EPTR &amp; Pawah</span>
                <i class="fa-solid fa-cow text-sm text-emerald-600"></i>
            </div>
            <div class="mt-2 text-xl font-black">{{ $eptrCount }}</div>
        </a>

        <!-- 4. EPU Unggas -->
        <a href="{{ route('notifications.index', ['type' => 'epu']) }}" class="p-4 rounded-2xl border transition {{ $type === 'epu' ? 'bg-amber-900 text-white border-amber-800 shadow-md' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">EPU Unggas</span>
                <i class="fa-solid fa-feather-pointed text-sm text-amber-600"></i>
            </div>
            <div class="mt-2 text-xl font-black">{{ $epuCount }}</div>
        </a>

        <!-- 5. Kursus & Klinik -->
        <a href="{{ route('notifications.index', ['type' => 'kursus']) }}" class="p-4 rounded-2xl border transition {{ in_array($type, ['kursus', 'klinik']) ? 'bg-cyan-900 text-white border-cyan-800 shadow-md' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Kursus/Klinik</span>
                <i class="fa-solid fa-graduation-cap text-sm text-cyan-600"></i>
            </div>
            <div class="mt-2 text-xl font-black">{{ $kursusCount + $klinikCount }}</div>
        </a>

        <!-- 6. Keselamatan & Akaun -->
        <a href="{{ route('notifications.index', ['type' => 'keselamatan']) }}" class="p-4 rounded-2xl border transition {{ $type === 'keselamatan' ? 'bg-indigo-900 text-white border-indigo-800 shadow-md' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Akaun/Sekuriti</span>
                <i class="fa-solid fa-shield-halved text-sm text-indigo-600"></i>
            </div>
            <div class="mt-2 text-xl font-black">{{ $securityCount }}</div>
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-3xl border border-slate-200 p-4 sm:p-5 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Category Pills -->
        <div class="flex flex-wrap items-center gap-1.5 w-full md:w-auto">
            <a href="{{ route('notifications.index', array_merge(request()->except('type', 'page'), ['type' => 'all'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ empty($type) || $type === 'all' ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua
            </a>
            <a href="{{ route('notifications.index', array_merge(request()->except('type', 'page'), ['type' => 'eptr_pawah'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $type === 'eptr_pawah' ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                EPTR &amp; Pawah
            </a>
            <a href="{{ route('notifications.index', array_merge(request()->except('type', 'page'), ['type' => 'epu'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $type === 'epu' ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                EPU Unggas
            </a>
            <a href="{{ route('notifications.index', array_merge(request()->except('type', 'page'), ['type' => 'kursus'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $type === 'kursus' ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Kursus
            </a>
            <a href="{{ route('notifications.index', array_merge(request()->except('type', 'page'), ['type' => 'klinik'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $type === 'klinik' ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Klinik
            </a>
            <a href="{{ route('notifications.index', array_merge(request()->except('type', 'page'), ['type' => 'stor_kenderaan'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $type === 'stor_kenderaan' ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Stor &amp; Kenderaan
            </a>
            <a href="{{ route('notifications.index', array_merge(request()->except('type', 'page'), ['type' => 'keselamatan'])) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $type === 'keselamatan' ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Akaun
            </a>
        </div>

        <!-- Search Form -->
        <form action="{{ route('notifications.index') }}" method="GET" class="flex items-center gap-2 w-full md:w-auto">
            @if(request('type'))
                <input type="hidden" name="type" value="{{ request('type') }}">
            @endif
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="relative w-full md:w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari notifikasi / aktiviti..." class="w-full pl-9 pr-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
            </div>
            @if(request('search'))
                <a href="{{ route('notifications.index', request()->except('search', 'page')) }}" class="p-2 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 text-xs font-bold">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- Notification List Section -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        @if($notifications->count() > 0)
            <div class="divide-y divide-slate-100">
                @foreach($notifications as $item)
                    @php
                        $colorClass = match($item->color) {
                            'emerald' => 'bg-emerald-100 text-emerald-700 border-emerald-300',
                            'amber' => 'bg-amber-100 text-amber-700 border-amber-300',
                            'blue' => 'bg-blue-100 text-blue-700 border-blue-300',
                            'rose' => 'bg-rose-100 text-rose-700 border-rose-300',
                            'indigo' => 'bg-indigo-100 text-indigo-700 border-indigo-300',
                            'teal' => 'bg-teal-100 text-teal-700 border-teal-300',
                            'purple' => 'bg-purple-100 text-purple-700 border-purple-300',
                            default => 'bg-slate-100 text-slate-700 border-slate-300',
                        };
                        $typeLabel = match($item->type) {
                            'eptr' => 'EPTR Ruminan',
                            'pawah' => 'Skim Pawah',
                            'epu' => 'EPU Ladang Unggas',
                            'kursus' => 'Kursus Ternakan',
                            'klinik' => 'Klinik Haiwan',
                            'inventori' => 'Stor & Inventori',
                            'kenderaan' => 'Kenderaan Rasmi',
                            'profil' => 'Profil & Keselamatan',
                            'auth' => 'Log Masuk & Pendaftaran',
                            default => 'Sistem Veterinar',
                        };
                    @endphp
                    <div class="p-4 sm:p-5 transition flex flex-col sm:flex-row items-start justify-between gap-4 {{ !$item->isRead() ? 'bg-emerald-50/30' : 'hover:bg-slate-50/60' }}">
                        <div class="flex items-start gap-4 flex-1">
                            <!-- Icon Badge -->
                            <div class="w-10 h-10 rounded-2xl border flex items-center justify-center shrink-0 shadow-xs text-sm {{ $colorClass }}">
                                <i class="{{ $item->icon }}"></i>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0 space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-sm font-bold text-slate-900">
                                        {{ $item->title }}
                                    </h3>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        {{ $typeLabel }}
                                    </span>
                                    @if(!$item->isRead())
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                            Baharu
                                        </span>
                                    @endif
                                </div>

                                <p class="text-xs text-slate-600 leading-relaxed">
                                    {{ $item->message }}
                                </p>

                                <div class="flex items-center gap-3 text-[11px] text-slate-400 pt-1">
                                    <span class="flex items-center gap-1">
                                        <i class="fa-regular fa-clock"></i>
                                        {{ $item->created_at->diffForHumans() }}
                                    </span>
                                    <span>&bull;</span>
                                    <span>{{ $item->created_at->format('d M Y, h:i A') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                            @if(!empty($item->action_url))
                                <form action="{{ route('notifications.mark-read', ['id' => $item->id, 'redirect' => 1]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-xs">
                                        <span>Buka Halaman</span>
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                    </button>
                                </form>
                            @endif

                            @if(!$item->isRead())
                                <form action="{{ route('notifications.mark-read', $item->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" title="Tanda Telah Dibaca" class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-emerald-50 text-slate-600 hover:text-emerald-700 border border-slate-200 text-xs font-bold transition">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </form>
                            @endif

                            <form action="{{ route('notifications.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Padam notifikasi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Padam Notifikasi" class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-400 hover:text-rose-600 border border-slate-200 text-xs font-bold transition">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="p-4 bg-slate-50 border-t border-slate-100">
                {{ $notifications->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mx-auto mb-3">
                    <i class="fa-regular fa-bell"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800">Tiada Notifikasi Ditemui</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Tiada sebarang notifikasi aktiviti yang sepadan dengan tapisan semasa anda.
                </p>
                <div class="mt-4">
                    <a href="{{ route('notifications.index') }}" class="px-4 py-2 rounded-xl bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 transition inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Set Semula Tapisan</span>
                    </a>
                </div>
            </div>
        @endif
    </div>

</div>
@endsection