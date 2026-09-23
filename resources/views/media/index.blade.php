@extends('layouts.app')

@section('title', 'Sistem Tempahan Unit Media')

@section('content')
<div class="space-y-6">

    <!-- Header & Hero Section -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden border border-slate-800">
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-20 top-0 w-56 h-56 bg-amber-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-xs font-bold tracking-wide uppercase">
                    <i class="fa-solid fa-camera-retro text-amber-400"></i>
                    <span>Unit Media &amp; Komunikasi Korporat JPVNK</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white flex items-center gap-3">
                    <span>Sistem Tempahan Unit Media</span>
                </h1>
                <p class="text-slate-300 text-sm max-w-2xl leading-relaxed">
                    Semak ketersediaan slot tarikh liputan program melalui kalendar interaktif, buat permohonan perkhidmatan fotografi, videografi, poster, hebahan media sosial, dan semak status kelulusan secara bersepadu.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('media.create') }}" class="px-5 py-3 rounded-2xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-bold text-sm shadow-lg shadow-amber-500/20 transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
                    <i class="fa-solid fa-circle-plus text-base"></i>
                    <span>Buat Permohonan Baharu</span>
                </a>
            </div>
        </div>

        <!-- Quick Summary Metrics -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 mt-6 pt-6 border-t border-slate-800/80">
            <div class="bg-slate-800/60 backdrop-blur-xs rounded-2xl p-3.5 border border-slate-700/50">
                <div class="text-slate-400 text-xs font-medium">Jumlah Tempahan</div>
                <div class="text-2xl font-black text-white mt-1">{{ number_format($statTotal) }}</div>
            </div>
            <div class="bg-slate-800/60 backdrop-blur-xs rounded-2xl p-3.5 border border-slate-700/50">
                <div class="text-amber-400 text-xs font-medium">Menunggu Kelulusan</div>
                <div class="text-2xl font-black text-amber-400 mt-1">{{ number_format($statPending) }}</div>
            </div>
            <div class="bg-slate-800/60 backdrop-blur-xs rounded-2xl p-3.5 border border-slate-700/50">
                <div class="text-emerald-400 text-xs font-medium">Diluluskan / Aktif</div>
                <div class="text-2xl font-black text-emerald-400 mt-1">{{ number_format($statApproved) }}</div>
            </div>
            <div class="bg-slate-800/60 backdrop-blur-xs rounded-2xl p-3.5 border border-slate-700/50">
                <div class="text-purple-400 text-xs font-medium">Selesai Liputan</div>
                <div class="text-2xl font-black text-purple-400 mt-1">{{ number_format($statCompleted) }}</div>
            </div>
        </div>
    </div>

    <!-- Main Navigation Tabs: Kalendar vs Senarai Permohonan -->
    <div x-data="{ tab: '{{ $activeTab }}', selectedDate: null, selectedBookings: [], isPastDate: false }" class="space-y-6">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-3">
            <div class="flex items-center gap-2 p-1 bg-slate-100 rounded-2xl border border-slate-200">
                <button @click="tab = 'kalendar'" :class="tab === 'kalendar' ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'" class="px-4 py-2 rounded-xl text-xs sm:text-sm transition flex items-center gap-2">
                    <i class="fa-solid fa-calendar-days text-indigo-600"></i>
                    <span>Kalendar Ketersediaan Media</span>
                </button>
                <button @click="tab = 'senarai'" :class="tab === 'senarai' ? 'bg-white text-slate-900 shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'" class="px-4 py-2 rounded-xl text-xs sm:text-sm transition flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-indigo-600"></i>
                    <span>Senarai &amp; Status Permohonan</span>
                    @if($statPending > 0)
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-slate-950">{{ $statPending }}</span>
                    @endif
                </button>
            </div>

            <!-- Petunjuk Warna Kalendar (Legend) -->
            <div class="flex flex-wrap items-center gap-4 text-xs font-medium text-slate-600">
                <span class="font-bold text-slate-700">Petunjuk Slot:</span>
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 shadow-xs"></span>
                    <span>🟢 Tarikh Kosong</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-amber-500 shadow-xs"></span>
                    <span>🟡 Sebahagian Ditempah (1-2 Slot)</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-rose-500 shadow-xs"></span>
                    <span>🔴 Tarikh Penuh (3+ Slot)</span>
                </div>
            </div>
        </div>

        <!-- ================= TAB 1: KALENDAR INTERAKTIF ================= -->
        <div x-show="tab === 'kalendar'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
            
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
                <!-- Month & Year Selector Header -->
                <div class="p-5 sm:p-6 bg-slate-50/70 border-b border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-200 text-indigo-600 flex items-center justify-center font-bold text-lg shadow-xs">
                            <i class="fa-solid fa-calendar-week"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-slate-900 uppercase tracking-wide">
                                {{ $currentDate->locale('ms')->translatedFormat('F Y') }}
                            </h2>
                            <p class="text-xs text-slate-500">Klik mana-mana tarikh untuk melihat perincian program atau membuat permohonan segera.</p>
                        </div>
                    </div>

                    <!-- Month Navigation Buttons -->
                    @php
                        $prevMonth = $currentDate->copy()->subMonth();
                        $nextMonth = $currentDate->copy()->addMonth();
                    @endphp
                    <div class="flex items-center gap-2">
                        <a href="{{ route('media.index', ['month' => $prevMonth->month, 'year' => $prevMonth->year, 'tab' => 'kalendar']) }}" class="px-3.5 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs border border-slate-200 shadow-xs transition flex items-center gap-1.5">
                            <i class="fa-solid fa-chevron-left text-slate-400"></i>
                            <span>{{ $prevMonth->locale('ms')->translatedFormat('M') }}</span>
                        </a>

                        <a href="{{ route('media.index', ['month' => now()->month, 'year' => now()->year, 'tab' => 'kalendar']) }}" class="px-3 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs border border-indigo-200 transition">
                            Bulan Ini
                        </a>

                        <a href="{{ route('media.index', ['month' => $nextMonth->month, 'year' => $nextMonth->year, 'tab' => 'kalendar']) }}" class="px-3.5 py-2 rounded-xl bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs border border-slate-200 shadow-xs transition flex items-center gap-1.5">
                            <span>{{ $nextMonth->locale('ms')->translatedFormat('M') }}</span>
                            <i class="fa-solid fa-chevron-right text-slate-400"></i>
                        </a>
                    </div>
                </div>

                <!-- Calendar Grid View -->
                <div class="p-4 sm:p-6">
                    <!-- Day Names Header -->
                    <div class="grid grid-cols-7 gap-2 mb-2 text-center text-xs font-bold uppercase tracking-wider text-slate-500">
                        <div class="py-2 text-rose-500">Ahad</div>
                        <div class="py-2">Isnin</div>
                        <div class="py-2">Selasa</div>
                        <div class="py-2">Rabu</div>
                        <div class="py-2">Khamis</div>
                        <div class="py-2">Jumaat</div>
                        <div class="py-2 text-indigo-500">Sabtu</div>
                    </div>

                    <!-- Days Grid -->
                    <div class="grid grid-cols-7 gap-2 sm:gap-3">
                        <!-- Empty slots for days before start of month -->
                        @for($i = 0; $i < $startDayOfWeek; $i++)
                            <div class="min-h-[90px] sm:min-h-[110px] rounded-2xl bg-slate-50/40 border border-dashed border-slate-200/60 p-2 opacity-40"></div>
                        @endfor

                        <!-- Actual Days in Month -->
                        @foreach($calendarDays as $day)
                            @php
                                $isToday = $day['date'] === date('Y-m-d');
                                $isPast = $day['date'] < date('Y-m-d');
                            @endphp
                            <div @click="selectedDate = '{{ $day['date'] }}'; selectedBookings = {{ json_encode($day['bookings']) }}; isPastDate = {{ $isPast ? 'true' : 'false' }}"
                                 class="min-h-[90px] sm:min-h-[110px] rounded-2xl border p-2 sm:p-2.5 transition-all flex flex-col justify-between group relative overflow-hidden {{ $isPast ? 'bg-slate-100/60 border-slate-200/80 opacity-60 hover:opacity-100 hover:bg-slate-100 cursor-pointer' : ($isToday ? 'ring-2 ring-indigo-500 bg-indigo-50/30 border-indigo-300 shadow-xs cursor-pointer' : 'bg-white hover:bg-slate-50 border-slate-200 hover:border-indigo-300 hover:shadow-md cursor-pointer') }}">
                                
                                <!-- Top Bar: Day Number & Status Dot -->
                                <div class="flex items-center justify-between">
                                    <span class="font-black text-sm {{ $isPast ? 'text-slate-400' : ($isToday ? 'text-indigo-700 bg-indigo-100 px-2 py-0.5 rounded-lg' : 'text-slate-800') }}">
                                        {{ $day['day'] }}
                                    </span>

                                    <!-- Status Indicator Dot -->
                                    <div class="flex items-center gap-1">
                                        @if($isPast)
                                            <span class="text-[9px] font-extrabold uppercase text-slate-400 bg-slate-200/80 px-1.5 py-0.5 rounded">Tamat</span>
                                        @else
                                            <span class="w-2.5 h-2.5 rounded-full {{ $day['status'] === 'kosong' ? 'bg-emerald-500' : ($day['status'] === 'sebahagian' ? 'bg-amber-500 animate-pulse' : 'bg-rose-500 animate-bounce') }}"></span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Booking Preview Badges inside Cell -->
                                <div class="my-1.5 space-y-1">
                                    @if($day['count'] > 0)
                                        @foreach($day['bookings']->take(2) as $booking)
                                            <div class="truncate text-[10px] font-bold px-1.5 py-0.5 rounded-md {{ $isPast ? 'bg-slate-200 text-slate-600' : ($booking->status === 'Diluluskan' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200') }}">
                                                {{ $booking->nama_program }}
                                            </div>
                                        @endforeach
                                        @if($day['count'] > 2)
                                            <div class="text-[9px] font-black text-slate-500 text-center">
                                                +{{ $day['count'] - 2 }} lagi
                                            </div>
                                        @endif
                                    @else
                                        @if($isPast)
                                            <div class="text-[10px] text-slate-400 italic">
                                                Tiada rekod
                                            </div>
                                        @else
                                            <div class="text-[10px] text-emerald-600 font-bold italic opacity-75 sm:opacity-100 group-hover:opacity-100 transition">
                                                🟢 Kosong
                                            </div>
                                        @endif
                                    @endif
                                </div>

                                <!-- Bottom Status Badge -->
                                <div class="pt-1 border-t border-slate-100 flex items-center justify-between text-[10px]">
                                    @if($isPast)
                                        <span class="font-bold text-slate-400">
                                            {{ $day['count'] > 0 ? $day['count'] . ' Program' : 'Tarikh Lepas' }}
                                        </span>
                                    @else
                                        <span class="font-extrabold {{ $day['status'] === 'kosong' ? 'text-emerald-700' : ($day['status'] === 'sebahagian' ? 'text-amber-700' : 'text-rose-700') }}">
                                            {{ $day['badge'] }}
                                        </span>
                                    @endif
                                    <i class="fa-solid fa-arrow-right text-[9px] text-slate-300 group-hover:text-indigo-600 group-hover:translate-x-0.5 transition-all"></i>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Date Details Modal / Drawer (Alpine.js) -->
            <div x-show="selectedDate" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
                <div @click.away="selectedDate = null" class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-200 space-y-5 animate-in fade-in zoom-in duration-150">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-base">
                                <i class="fa-solid fa-calendar-day"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-black text-slate-900">Perincian Slot Tarikh</h3>
                                <p class="text-xs text-slate-500 font-mono" x-text="selectedDate"></p>
                            </div>
                        </div>
                        <button @click="selectedDate = null" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    <!-- Past Date Warning Notice -->
                    <template x-if="isPastDate">
                        <div class="p-3.5 bg-amber-50 rounded-2xl border border-amber-200 text-amber-900 text-xs flex items-center gap-2.5">
                            <i class="fa-solid fa-triangle-exclamation text-amber-600 text-base shrink-0"></i>
                            <div>
                                <span class="font-bold block">Tarikh ini telah berlalu</span>
                                <span class="text-[11px] text-amber-800">Tempahan slot media tidak boleh dibuat untuk tarikh sebelum hari ini.</span>
                            </div>
                        </div>
                    </template>

                    <!-- Booking list on that date -->
                    <div class="space-y-3 max-h-72 overflow-y-auto pr-1">
                        <template x-if="selectedBookings.length === 0">
                            <div class="text-center py-6 bg-emerald-50/50 rounded-2xl border border-dashed border-emerald-200 p-4">
                                <i class="fa-solid fa-calendar-check text-emerald-500 text-2xl mb-2 block"></i>
                                <div class="text-sm font-bold text-emerald-900">Tiada Tempahan Pada Tarikh Ini</div>
                                <p class="text-xs text-emerald-700 mt-0.5">Semua slot jurugambar dan krew media adalah kosong dan sedia untuk ditempah.</p>
                            </div>
                        </template>

                        <template x-for="b in selectedBookings" :key="b.id">
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="font-mono font-bold text-[11px] text-indigo-600" x-text="b.no_rujukan"></span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                                          :class="b.status === 'Diluluskan' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'"
                                          x-text="b.status"></span>
                                </div>
                                <div class="font-bold text-sm text-slate-900" x-text="b.nama_program"></div>
                                <div class="grid grid-cols-2 gap-2 text-slate-600">
                                    <div><i class="fa-solid fa-clock text-indigo-500 mr-1"></i> <span x-text="b.masa_mula + ' - ' + b.masa_tamat"></span></div>
                                    <div><i class="fa-solid fa-location-dot text-rose-500 mr-1"></i> <span x-text="b.lokasi"></span></div>
                                </div>
                                <div class="text-slate-500 pt-1 border-t border-slate-200 flex items-center justify-between">
                                    <span>Pemohon: <b class="text-slate-700" x-text="b.nama_pemohon"></b></span>
                                    <a :href="'/media/' + b.id" class="text-indigo-600 font-bold hover:underline">Lihat &rarr;</a>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Action buttons -->
                    <div class="pt-3 border-t border-slate-200 flex gap-2">
                        <template x-if="!isPastDate">
                            <a :href="'/media/tempah?tarikh=' + selectedDate" class="flex-1 py-3 px-4 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs sm:text-sm text-center shadow-lg shadow-indigo-600/20 transition flex items-center justify-center gap-2">
                                <i class="fa-solid fa-circle-plus"></i>
                                <span>Tempah Pada Tarikh Ini</span>
                            </a>
                        </template>
                        <template x-if="isPastDate">
                            <div class="flex-1 py-3 px-4 rounded-2xl bg-slate-100 text-slate-400 font-bold text-xs sm:text-sm text-center flex items-center justify-center gap-2 cursor-not-allowed border border-slate-200 select-none">
                                <i class="fa-solid fa-ban text-slate-400"></i>
                                <span>Tarikh Telah Berlalu (Tidak Boleh Ditempah)</span>
                            </div>
                        </template>
                        <button @click="selectedDate = null" class="px-4 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs sm:text-sm transition">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <!-- ================= TAB 2: SENARAI PERMOHONAN ================= -->
        <div x-show="tab === 'senarai'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
            
            <!-- Filters & Search Bar -->
            <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-xs">
                <form method="GET" action="{{ route('media.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 items-end">
                    <input type="hidden" name="tab" value="senarai">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Carian Program / No. Rujukan / Pemohon</label>
                        <div class="relative">
                            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="text" name="carian" value="{{ request('carian') }}" placeholder="Cth: Hari Penternak, MEDIA/2026..." class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status Permohonan</label>
                        <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden bg-white">
                            <option value="">Semua Status</option>
                            <option value="Menunggu Kelulusan" {{ request('status') === 'Menunggu Kelulusan' ? 'selected' : '' }}>Menunggu Kelulusan</option>
                            <option value="Perlu Pembetulan" {{ request('status') === 'Perlu Pembetulan' ? 'selected' : '' }}>Perlu Pembetulan</option>
                            <option value="Diluluskan" {{ request('status') === 'Diluluskan' ? 'selected' : '' }}>Diluluskan</option>
                            <option value="Ditolak" {{ request('status') === 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                            <option value="Selesai" {{ request('status') === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tarikh Program</label>
                        <input type="date" name="tarikh_pilih" value="{{ request('tarikh_pilih') }}" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-hidden">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition flex items-center justify-center gap-1.5 shadow-xs">
                            <i class="fa-solid fa-filter"></i>
                            <span>Tapis</span>
                        </button>
                        <a href="{{ route('media.index', ['tab' => 'senarai']) }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center justify-center" title="Reset Tapis">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Booking Table List -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="font-black text-slate-900 text-sm">Senarai Permohonan Tempahan Media</h3>
                        <span class="text-xs text-slate-500">Jumlah rekod: <b>{{ $tempahanList->total() }}</b></span>
                    </div>
                    @if(Auth::user()->canManageMedia() || Auth::user()->isPengarah())
                        <a href="{{ route('media.export', request()->query()) }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition">
                            <i class="fa-solid fa-file-excel"></i>
                            <span>Eksport Data (CSV / Excel)</span>
                        </a>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3.5">No. Rujukan &amp; Tarikh</th>
                                <th class="px-4 py-3.5">Maklumat Program &amp; Lokasi</th>
                                <th class="px-4 py-3.5">Pemohon &amp; Bahagian</th>
                                <th class="px-4 py-3.5">Jenis Keperluan Media</th>
                                <th class="px-4 py-3.5">Status</th>
                                <th class="px-4 py-3.5 text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($tempahanList as $t)
                                @php
                                    $badge = $t->status_badge;
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        <div class="font-mono font-bold text-indigo-700">{{ $t->no_rujukan }}</div>
                                        <div class="text-[11px] text-slate-500 mt-0.5">
                                            <i class="fa-solid fa-calendar text-slate-400 mr-1"></i>
                                            {{ $t->tarikh_program->format('d/m/Y') }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <div class="font-bold text-slate-900 text-xs">{{ $t->nama_program }}</div>
                                        <div class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                            <i class="fa-solid fa-location-dot text-rose-500"></i>
                                            <span>{{ $t->lokasi }} ({{ $t->masa_mula }} - {{ $t->masa_tamat }})</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <div class="font-semibold text-slate-900">{{ $t->nama_pemohon }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $t->bahagian_unit_jajahan }}</div>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <div class="flex flex-wrap gap-1 max-w-xs">
                                            @if($t->jenis_permohonan && is_array($t->jenis_permohonan))
                                                @foreach(array_slice($t->jenis_permohonan, 0, 2) as $jenis)
                                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                        {{ $jenis }}
                                                    </span>
                                                @endforeach
                                                @if(count($t->jenis_permohonan) > 2)
                                                    <span class="px-1.5 py-0.5 rounded-md text-[9px] font-bold bg-slate-100 text-slate-600">
                                                        +{{ count($t->jenis_permohonan) - 2 }}
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border flex items-center gap-1.5 w-fit {{ $badge['bg'] }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                                            <span>{{ $badge['label'] }}</span>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <a href="{{ route('media.show', $t->id) }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition" title="Lihat Butiran">
                                                <i class="fa-solid fa-eye text-xs"></i>
                                            </a>
                                            @if($t->status === 'Diluluskan')
                                                <a href="{{ $t->google_calendar_url }}" target="_blank" class="p-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold transition" title="Tambah ke Google Calendar (jpvnkmedia@gmail.com)">
                                                    <i class="fa-solid fa-calendar-plus text-xs"></i>
                                                </a>
                                                <a href="{{ route('media.cetak', $t->id) }}" target="_blank" class="p-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold transition" title="Cetak Slip Tempahan">
                                                    <i class="fa-solid fa-print text-xs"></i>
                                                </a>
                                            @endif
                                            @if(in_array($t->status, ['Perlu Pembetulan', 'Menunggu Kelulusan']) && ($t->user_id === Auth::id() || Auth::user()->canManageMedia()))
                                                <a href="{{ route('media.edit', $t->id) }}" class="p-2 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-800 font-bold transition" title="Kemas Kini">
                                                    <i class="fa-solid fa-pen text-xs"></i>
                                                </a>
                                            @endif
                                            @if(Auth::user()->isSuperAdmin())
                                                <form action="{{ route('media.destroy', $t->id) }}" method="POST" class="inline" onsubmit="return confirm('PERINGATAN SUPER ADMIN: Adakah anda pasti ingin memadam permohonan tempahan media {{ $t->no_rujukan }} secara kekal? Tindakan ini tidak boleh diundur!');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold transition" title="Padam Tempahan Media (Super Admin Sahaja)">
                                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-10 text-slate-400">
                                        <i class="fa-solid fa-calendar-xmark text-3xl mb-2 block"></i>
                                        <p class="font-medium text-sm">Tiada rekod tempahan media dijumpai.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($tempahanList->hasPages())
                    <div class="p-4 border-t border-slate-200">
                        {{ $tempahanList->links() }}
                    </div>
                @endif
            </div>

        </div>

    </div>

</div>
@endsection
