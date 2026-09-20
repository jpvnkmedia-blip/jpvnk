@extends('layouts.app')

@section('title', 'Semakan Status Permohonan - NAIMbif JPVNK')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold uppercase tracking-wider mb-2">
            <i class="fas fa-search"></i> Portal Semakan Permohonan
        </div>
        <h1 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight">
            Semakan Status Permohonan Ladang Bridlot
        </h1>
        <p class="text-slate-500 text-xs sm:text-sm mt-2 max-w-lg mx-auto">
            Sila masukkan No. Kad Pengenalan (12 digit) atau No. Rujukan Permohonan untuk melihat perkembangan status anda.
        </p>
    </div>

    <!-- Search Input Form -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6 sm:p-8 mb-8">
        <form action="{{ route('naimbif.public.check_status') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <i class="fas fa-search text-base"></i>
                </div>
                <input type="text" name="carian" value="{{ $query }}" required
                       placeholder="Masukkan No. KP (contoh: 850712035411) atau No. Rujukan (contoh: NB-2026-0001)"
                       class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm font-medium">
            </div>
            <button type="submit" class="px-8 py-3.5 rounded-2xl text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition-all flex items-center justify-center">
                <span>Cari Status</span>
                <i class="fas fa-arrow-right ml-2"></i>
            </button>
        </form>

        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center text-xs text-slate-500">
            <i class="fas fa-info-circle text-emerald-600 mr-2"></i>
            <span><strong>Petunjuk Pemohon:</strong> Anda boleh menyemak perkembangan semasa atau <strong>mengemas kini maklumat</strong> permohonan dengan membuat carian No. KP atau No. Rujukan di atas.</span>
        </div>
    </div>

    <!-- Result Display -->
    @if($searched)
        @if($application)
            <div class="bg-white rounded-3xl shadow-md border border-slate-200 overflow-hidden mb-8">
                <!-- Result Header -->
                <div class="p-6 sm:p-8 bg-slate-900 text-white flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div>
                        <div class="text-xs text-amber-400 font-bold uppercase tracking-wider font-mono">No. Rujukan: {{ $application->no_rujukan }}</div>
                        <h2 class="text-xl sm:text-2xl font-black mt-1">{{ $application->nama }}</h2>
                        <div class="text-xs text-slate-400 mt-0.5">
                            No. KP: <span class="font-mono text-slate-300 font-bold">{{ $application->formatted_no_kp }}</span> | Jajahan: <span class="text-slate-200 font-semibold">{{ $application->jajahan_ladang ?: $application->jajahan }}</span>
                        </div>
                    </div>
                    
                    <!-- Action Toolbar in Header -->
                    <div class="flex flex-wrap items-center gap-2.5">
                        {!! $application->status_badge !!}
                        
                        <a href="{{ route('naimbif.public.edit', $application->no_rujukan) }}"
                           class="inline-flex items-center px-4 py-2.5 rounded-xl text-xs font-bold text-emerald-900 bg-emerald-400 hover:bg-emerald-300 shadow-md transition-all">
                            <i class="fas fa-edit mr-1.5 text-emerald-950"></i> Ubah / Kemas Kini Maklumat
                        </a>

                        <a href="{{ route('naimbif.public.print', $application->no_rujukan) }}" target="_blank"
                           class="inline-flex items-center px-4 py-2.5 rounded-xl text-xs font-bold text-slate-200 bg-slate-800 hover:bg-slate-700 hover:text-white border border-slate-700 transition-all">
                            <i class="fas fa-print mr-1.5 text-amber-400"></i> Cetak PDF
                        </a>
                    </div>
                </div>

                <!-- Prominent Action Bar for Applicant -->
                <div class="p-4 sm:p-5 bg-gradient-to-r from-emerald-50 via-teal-50 to-emerald-50/50 border-b border-emerald-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="flex items-center space-x-3 text-left">
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-lg flex-shrink-0 shadow-sm">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-emerald-950">Perlu mengemas kini butiran permohonan anda?</div>
                            <div class="text-[11px] text-emerald-800/80">Anda boleh meminda maklumat penternak, lokasi tapak ladang, atau matriks bilangan lembu pada bila-bila masa.</div>
                        </div>
                    </div>
                    <a href="{{ route('naimbif.public.edit', $application->no_rujukan) }}"
                       class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 shadow-md shadow-emerald-700/20 transition-all flex-shrink-0">
                        <i class="fas fa-edit mr-2 text-emerald-200"></i> Ubah / Kemas Kini Sekarang
                    </a>
                </div>

                <!-- Status Progress Stepper -->
                <div class="p-6 sm:p-8 border-b border-slate-200 bg-slate-50/50">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-6">Garis Masa Permohonan</h3>
                    
                    @php
                        // Determine step states
                        $step1 = true; // Permohonan Dihantar
                        $step2 = $application->status_kelengkapan === 'Lengkap' || $application->syor_permohonan === 'Disokong' || $application->status_negeri === 'Lulus';
                        $step3 = $application->status_negeri === 'Lulus' || $application->status_negeri === 'Gagal';
                    @endphp

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 relative">
                        <!-- Step 1 -->
                        <div class="flex sm:flex-col items-center sm:text-center space-x-3 sm:space-x-0 space-y-0 sm:space-y-2">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm bg-emerald-600 text-white shadow-md">
                                <i class="fas fa-check"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-800">1. Permohonan Dihantar</div>
                                <div class="text-[11px] text-slate-500">{{ $application->tarikh_permohonan ? $application->tarikh_permohonan->format('d/m/Y') : '-' }}</div>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="flex sm:flex-col items-center sm:text-center space-x-3 sm:space-x-0 space-y-0 sm:space-y-2">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm {{ $step2 ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-200 text-slate-400' }}">
                                @if($step2) <i class="fas fa-check"></i> @else 2 @endif
                            </div>
                            <div>
                                <div class="text-xs font-bold {{ $step2 ? 'text-slate-800' : 'text-slate-400' }}">2. Semakan Pejabat Jajahan</div>
                                <div class="text-[11px] text-slate-500">
                                    @if($application->syor_permohonan === 'Disokong')
                                        <span class="text-emerald-600 font-semibold">Disokong</span> ({{ $application->tarikh_semakan_jajahan ? $application->tarikh_semakan_jajahan->format('d/m/Y') : '' }})
                                    @elseif($application->status_kelengkapan === 'Lengkap')
                                        <span class="text-indigo-600 font-semibold">Dokumen Lengkap</span>
                                    @else
                                        Menunggu Siasatan Premis
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="flex sm:flex-col items-center sm:text-center space-x-3 sm:space-x-0 space-y-0 sm:space-y-2">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm {{ $application->status_negeri === 'Lulus' ? 'bg-emerald-600 text-white shadow-md' : ($application->status_negeri === 'Gagal' ? 'bg-rose-600 text-white' : 'bg-slate-200 text-slate-400') }}">
                                @if($application->status_negeri === 'Lulus') <i class="fas fa-check"></i>
                                @elseif($application->status_negeri === 'Gagal') <i class="fas fa-times"></i>
                                @else 3 @endif
                            </div>
                            <div>
                                <div class="text-xs font-bold {{ $step3 ? 'text-slate-800' : 'text-slate-400' }}">3. Keputusan Jabatan (JPVNK)</div>
                                <div class="text-[11px] text-slate-500">
                                    @if($application->status_negeri === 'Lulus')
                                        <span class="text-emerald-600 font-bold">LULUS</span> ({{ $application->tarikh_kelulusan_negeri ? $application->tarikh_kelulusan_negeri->format('d/m/Y') : '' }})
                                    @elseif($application->status_negeri === 'Gagal')
                                        <span class="text-rose-600 font-bold">DITOLAK</span>
                                    @else
                                        Menunggu Keputusan Mesyuarat
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Content -->
                <div class="p-6 sm:p-8 space-y-6">
                    <!-- Office Review Status Box -->
                    @if($application->id_premis || $application->no_rujukan_negeri || $application->catatan_jajahan || $application->ulasan_negeri)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Jajahan Details -->
                            <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-200 text-xs">
                                <div class="font-bold text-emerald-900 mb-2 flex items-center">
                                    <i class="fas fa-building mr-1.5"></i> Tindakan Pejabat Jajahan ({{ $application->jajahan }})
                                </div>
                                <div class="space-y-1 text-slate-700">
                                    <div><strong>ID Premis:</strong> <span class="font-mono text-emerald-800">{{ $application->id_premis ?? 'Sedang dijana' }}</span></div>
                                    <div><strong>Status Kelengkapan:</strong> {{ $application->status_kelengkapan }}</div>
                                    <div><strong>Syor Permohonan:</strong> {{ $application->syor_permohonan }}</div>
                                    <div><strong>Pegawai Penyiasat:</strong> {{ $application->pegawai_penyiasat ?? '-' }}</div>
                                    @if($application->catatan_jajahan)
                                        <div class="mt-2 pt-2 border-t border-emerald-200/60 text-slate-600 italic">
                                            "{{ $application->catatan_jajahan }}"
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Jabatan Details -->
                            <div class="p-4 rounded-2xl bg-blue-50/60 border border-blue-200 text-xs">
                                <div class="font-bold text-blue-900 mb-2 flex items-center">
                                    <i class="fas fa-landmark mr-1.5"></i> Keputusan Jabatan (JPVNK)
                                </div>
                                <div class="space-y-1 text-slate-700">
                                    <div><strong>Status Kelulusan:</strong> <span class="font-bold {{ $application->status_negeri === 'Lulus' ? 'text-emerald-700' : '' }}">{{ $application->status_negeri }}</span></div>
                                    <div><strong>No. Rujukan Rasmi:</strong> <span class="font-mono text-blue-800">{{ $application->no_rujukan_negeri ?? '-' }}</span></div>
                                    <div><strong>Pegawai Pelulus:</strong> {{ $application->pegawai_pelulus ?? '-' }}</div>
                                    @if($application->ulasan_negeri)
                                        <div class="mt-2 pt-2 border-t border-blue-200/60 text-slate-600 italic">
                                            "{{ $application->ulasan_negeri }}"
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Farm & Livestock Summary -->
                    <div class="border border-slate-200 rounded-2xl p-4 bg-slate-50 text-xs grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div>
                            <span class="text-slate-500 font-medium">Keluasan Ladang:</span>
                            <div class="font-bold text-slate-800 text-sm mt-0.5">{{ $application->keluasan_tanah }} Ekar</div>
                        </div>
                        <div>
                            <span class="text-slate-500 font-medium">Padang Ragut:</span>
                            <div class="font-bold text-slate-800 text-sm mt-0.5">{{ $application->padang_ragut }}</div>
                        </div>
                        <div>
                            <span class="text-slate-500 font-medium">Kaedah Pembiakan:</span>
                            <div class="font-bold text-slate-800 text-sm mt-0.5">{{ $application->kaedah_pembiakan }}</div>
                        </div>
                        <div>
                            <span class="text-slate-500 font-medium">Jumlah Lembu Semasa:</span>
                            <div class="font-black text-emerald-700 text-sm mt-0.5">{{ $application->total_ternakan }} Ekor</div>
                        </div>
                    </div>

                    <!-- Actions (Edit & Print) -->
                    <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-slate-100">
                        <div>
                            <a href="{{ route('naimbif.public.edit', $application->no_rujukan) }}"
                               class="inline-flex items-center px-5 py-3 rounded-xl text-xs font-bold text-emerald-800 bg-emerald-100 hover:bg-emerald-200 border border-emerald-300 transition-colors shadow-sm">
                                <i class="fas fa-edit mr-2 text-emerald-600"></i> Kemas Kini / Ubah Maklumat Permohonan
                            </a>
                        </div>

                        <a href="{{ route('naimbif.public.print', $application->no_rujukan) }}" target="_blank"
                           class="inline-flex items-center px-6 py-3 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 transition-colors shadow-md">
                            <i class="fas fa-print mr-2 text-amber-400"></i> Cetak / Salinan Borang Rasmi PDF (A4)
                        </a>
                    </div>
                </div>
            </div>
        @else
            <!-- Not Found State -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 sm:p-12 text-center">
                <div class="w-16 h-16 mx-auto rounded-full bg-rose-50 text-rose-500 flex items-center justify-center text-2xl mb-4 border border-rose-200">
                    <i class="fas fa-search-minus"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800">Tiada Rekod Permohonan Dijumpai</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Permohonan dengan carian <span class="font-mono font-bold text-slate-700">"{{ $query }}"</span> tidak ditemui dalam sistem. Sila pastikan No. KP atau No. Rujukan dimasukkan dengan betul.
                </p>
                <div class="mt-6">
                    <a href="{{ route('naimbif.public.apply') }}" class="inline-flex items-center px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700">
                        <i class="fas fa-plus-circle mr-1.5"></i> Hantar Permohonan Baru
                    </a>
                </div>
            </div>
        @endif
    @endif
</div>
@endsection
