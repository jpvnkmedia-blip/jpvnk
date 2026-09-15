@extends('layouts.app')

@section('title', 'Kursus Ternakan & Latihan')
@section('page_title', 'Pusat Latihan & Kursus Ternakan Veterinar')

@section('content')
<div class="space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">Direktori Kursus & Bengkel Ternakan</h2>
            <p class="text-xs text-slate-500 mt-0.5">Tingkatkan kemahiran pengurusan ternakan ruminan, reban unggas moden, biosekuriti dan keusahawanan</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-2">
            @if(Auth::user()->canPublishCourse())
                <a href="{{ route('kursus.pemohon.index') }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-800 text-xs font-bold shadow-2xs transition flex items-center gap-2">
                    <i class="fa-solid fa-users-gear text-cyan-700"></i>
                    <span>Pengurusan Pemohon</span>
                    @if($pendingApplicationsCount > 0)
                        <span class="px-2 py-0.5 rounded-full bg-amber-500 text-slate-950 font-black text-[10px] animate-pulse">
                            {{ $pendingApplicationsCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('kursus.create') }}" class="px-4 py-2 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white text-xs font-bold shadow-lg shadow-cyan-700/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-plus-circle"></i>
                    <span>Terbitkan Kursus Baharu</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-3xl border border-slate-200 p-4 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Filter Categories & Status -->
        <div class="flex flex-wrap gap-2 text-xs">
            <a href="{{ route('kursus.index') }}" class="px-3.5 py-1.5 rounded-full font-bold transition {{ !request('category') && !request('status') ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua Kursus
            </a>
            <a href="{{ route('kursus.index', ['category' => 'Ruminan']) }}" class="px-3.5 py-1.5 rounded-full font-bold transition {{ request('category') === 'Ruminan' ? 'bg-amber-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <i class="fa-solid fa-cow mr-1"></i> Ruminan
            </a>
            <a href="{{ route('kursus.index', ['category' => 'Unggas']) }}" class="px-3.5 py-1.5 rounded-full font-bold transition {{ request('category') === 'Unggas' ? 'bg-amber-500 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <i class="fa-solid fa-feather-pointed mr-1"></i> Unggas
            </a>
            <a href="{{ route('kursus.index', ['category' => 'Pemakanan Ternakan']) }}" class="px-3.5 py-1.5 rounded-full font-bold transition {{ request('category') === 'Pemakanan Ternakan' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <i class="fa-solid fa-wheat-awn mr-1"></i> Pemakanan &amp; Silaj
            </a>
            <a href="{{ route('kursus.index', ['category' => 'Biosekuriti Ladang']) }}" class="px-3.5 py-1.5 rounded-full font-bold transition {{ request('category') === 'Biosekuriti Ladang' ? 'bg-cyan-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                <i class="fa-solid fa-shield-virus mr-1"></i> Biosekuriti
            </a>
            <a href="{{ route('kursus.index', ['status' => 'Diarkibkan']) }}" class="px-3.5 py-1.5 rounded-full font-bold transition {{ request('status') === 'Diarkibkan' ? 'bg-purple-800 text-white shadow-xs' : 'bg-purple-50 text-purple-800 hover:bg-purple-100 border border-purple-200' }}">
                <i class="fa-solid fa-box-archive mr-1"></i> Arkib Kursus Selesai
            </a>
        </div>

        <!-- Search Input -->
        <form action="{{ route('kursus.index') }}" method="GET" class="flex items-center gap-2">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari tajuk / penceramah..." class="rounded-xl border border-slate-200 pl-8 pr-3 py-1.5 text-xs bg-slate-50 focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none w-48 sm:w-64">
                <i class="fa-solid fa-magnifying-glass text-slate-400 absolute left-2.5 top-2.5 text-xs"></i>
            </div>
            <button type="submit" class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition">
                Cari
            </button>
        </form>
    </div>

    <!-- Courses Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($courses as $course)
            @php 
                $applied = in_array($course->id, $myApplications); 
                $appRecord = $applied && isset($myApplicationsList[$course->id]) ? $myApplicationsList[$course->id] : null;
            @endphp
            <div class="bg-white rounded-3xl border {{ $course->status === 'Diarkibkan' ? 'border-purple-200 bg-purple-50/10' : 'border-slate-200' }} overflow-hidden shadow-xs hover:shadow-md transition flex flex-col justify-between">
                <div class="p-6 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $course->status === 'Diarkibkan' ? 'bg-purple-100 text-purple-900 border border-purple-200' : 'bg-cyan-50 text-cyan-800 border border-cyan-200' }}">
                            {{ $course->category }}
                        </span>
                        <div class="flex items-center gap-1.5">
                            @if($course->status === 'Diarkibkan')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-900 border border-purple-200">
                                    <i class="fa-solid fa-box-archive mr-0.5"></i> Arkib
                                </span>
                            @elseif($course->status === 'Selesai')
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-100 text-teal-800 border border-teal-200">
                                    Selesai
                                </span>
                            @endif
                            <span class="text-xs font-mono font-bold text-slate-400">{{ $course->code }}</span>
                        </div>
                    </div>

                    <h3 class="text-base font-extrabold text-slate-900 leading-snug">
                        <a href="{{ route('kursus.show', $course->id) }}" class="hover:text-cyan-700 hover:underline">
                            {{ $course->title }}
                        </a>
                    </h3>

                    <p class="text-xs text-slate-600 line-clamp-2">
                        {{ $course->description }}
                    </p>

                    <div class="pt-3 border-t border-slate-100 text-xs space-y-1.5 text-slate-600">
                        <div class="flex items-center gap-2">
                            <i class="fa-regular fa-calendar text-cyan-600 w-4 text-center"></i>
                            <span>{{ $course->start_date ? $course->start_date->format('d/m/Y') : '-' }} hingga {{ $course->end_date ? $course->end_date->format('d/m/Y') : '-' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-location-dot text-rose-500 w-4 text-center"></i>
                            <span class="truncate">{{ $course->location }} ({{ $course->jajahan }})</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-user-tie text-slate-400 w-4 text-center"></i>
                            <span class="truncate">Penceramah: {{ $course->trainer_name }}</span>
                        </div>
                    </div>
                </div>

                <!-- Footer & Action -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs">
                    <div>
                        <span class="font-bold text-slate-900">{{ $course->registered_count }} / {{ $course->capacity }}</span>
                        <span class="text-slate-400 text-[11px]"> Peserta</span>
                    </div>

                    <div>
                        @if($applied && $appRecord)
                            <div class="flex items-center gap-1.5">
                                @if($appRecord->status === 'Menunggu')
                                    <span class="px-2.5 py-1 rounded-xl bg-amber-100 text-amber-800 font-bold text-[11px]">
                                        <i class="fa-solid fa-clock-rotate-left"></i> Menunggu
                                    </span>
                                @elseif($appRecord->status === 'Disahkan')
                                    <span class="px-2.5 py-1 rounded-xl bg-blue-100 text-blue-800 font-bold text-[11px]">
                                        <i class="fa-solid fa-circle-check"></i> Diluluskan
                                    </span>
                                @elseif($appRecord->status === 'Hadir' || $appRecord->status === 'Selesai')
                                    <span class="px-2.5 py-1 rounded-xl bg-emerald-100 text-emerald-800 font-bold text-[11px]">
                                        <i class="fa-solid fa-award"></i> Hadir
                                    </span>
                                    @if($appRecord->certificate_number)
                                        <a href="{{ route('kursus.sijil', $appRecord->id) }}" target="_blank" class="p-1.5 rounded-xl bg-amber-100 hover:bg-amber-200 text-amber-900 font-bold" title="Cetak Sijil Digital">
                                            <i class="fa-solid fa-print"></i>
                                        </a>
                                    @endif
                                @elseif($appRecord->status === 'Ditolak')
                                    <span class="px-2.5 py-1 rounded-xl bg-rose-100 text-rose-800 font-bold text-[11px]">
                                        <i class="fa-solid fa-xmark"></i> Ditolak
                                    </span>
                                @endif
                            </div>
                        @else
                            <a href="{{ route('kursus.show', $course->id) }}" class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 font-bold transition">
                                Maklumat & Daftar &rarr;
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 bg-white rounded-3xl border border-slate-200 p-12 text-center text-slate-400">
                <i class="fa-solid fa-graduation-cap text-4xl mb-3 text-slate-300"></i>
                <div class="font-bold text-slate-700">Tiada kursus dijumpai</div>
                <div class="text-xs mt-1">Sila cuba kategori lain atau semak semula kemudian.</div>
            </div>
        @endforelse
    </div>

    @if($courses->hasPages())
        <div class="p-4">
            {{ $courses->links() }}
        </div>
    @endif

</div>
@endsection
