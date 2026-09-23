@extends('layouts.app')

@section('title', 'Butiran Tempahan Media - ' . $tempahan->no_rujukan)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    @php
        $badge = $tempahan->status_badge;
        $keutamaan = $tempahan->keutamaan_badge;
    @endphp

    <!-- Top Navigation & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('media.index') }}" class="w-10 h-10 rounded-2xl bg-white border border-slate-200 text-slate-700 flex items-center justify-center hover:bg-slate-50 transition shadow-xs">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-mono font-bold text-xs text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-lg border border-indigo-200">
                        {{ $tempahan->no_rujukan }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold border flex items-center gap-1.5 {{ $badge['bg'] }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot'] }}"></span>
                        <span>{{ $badge['label'] }}</span>
                    </span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 mt-1">
                    {{ $tempahan->nama_program }}
                </h1>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if($tempahan->status === 'Diluluskan')
                <a href="{{ route('media.cetak', $tempahan->id) }}" target="_blank" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2">
                    <i class="fa-solid fa-print text-amber-400"></i>
                    <span>Cetak Slip Pengesahan</span>
                </a>
            @endif

            @if(in_array($tempahan->status, ['Perlu Pembetulan', 'Menunggu Kelulusan']) && ($tempahan->user_id === Auth::id() || Auth::user()->canManageMedia()))
                <a href="{{ route('media.edit', $tempahan->id) }}" class="px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <span>Kemas Kini Permohonan</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Alert Status Box (e.g. Perlu Pembetulan / Diluluskan / Ditolak) -->
    @if($tempahan->status === 'Perlu Pembetulan')
        <div class="bg-sky-50 border border-sky-300 rounded-3xl p-5 sm:p-6 text-sky-950 space-y-2 shadow-xs">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 font-black text-sm text-sky-900">
                    <i class="fa-solid fa-circle-exclamation text-sky-600 text-base"></i>
                    <span>Tindakan Diperlukan: Arahan Pembetulan Daripada Unit Media</span>
                </div>
                <span class="text-xs font-mono font-bold text-sky-700 bg-white/80 px-2 py-0.5 rounded-lg border border-sky-200">Perlu Pembetulan</span>
            </div>
            <p class="text-xs leading-relaxed bg-white p-3.5 rounded-2xl border border-sky-200 font-medium">
                {{ $tempahan->catatan_unit_media ?? 'Sila semak semula butiran program atau lampiran yang diperlukan.' }}
            </p>
            @if($tempahan->user_id === Auth::id() || Auth::user()->canManageMedia())
                <div class="pt-2">
                    <a href="{{ route('media.edit', $tempahan->id) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs shadow-xs transition">
                        <i class="fa-solid fa-pen"></i>
                        <span>Kemas Kini Sekarang &amp; Hantar Semula</span>
                    </a>
                </div>
            @endif
        </div>
    @elseif($tempahan->status === 'Diluluskan')
        <div class="bg-emerald-50 border border-emerald-300 rounded-3xl p-5 sm:p-6 text-emerald-950 space-y-2 shadow-xs">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 font-black text-sm text-emerald-900">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                    <span>Tempahan Disahkan &amp; Diluluskan</span>
                </div>
                <span class="text-xs font-mono font-bold text-emerald-800 bg-white/80 px-2.5 py-0.5 rounded-lg border border-emerald-200">
                    Tarikh Kelulusan: {{ $tempahan->tarikh_kelulusan ? $tempahan->tarikh_kelulusan->format('d/m/Y H:i') : '-' }}
                </span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs pt-1">
                <div class="bg-white p-3 rounded-2xl border border-emerald-200">
                    <span class="text-slate-500 block text-[11px]">Pegawai / Krew Media Bertugas:</span>
                    <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $tempahan->pegawai_media_bertugas ?? 'Pasukan Unit Media JPVNK' }}</span>
                </div>
                <div class="bg-white p-3 rounded-2xl border border-emerald-200">
                    <span class="text-slate-500 block text-[11px]">Catatan Unit Media:</span>
                    <span class="font-medium text-slate-800 mt-0.5 block">{{ $tempahan->catatan_unit_media ?? 'Liputan disahkan mengikut atur cara yang ditetapkan.' }}</span>
                </div>
            </div>
        </div>
    @elseif($tempahan->status === 'Ditolak')
        <div class="bg-rose-50 border border-rose-300 rounded-3xl p-5 sm:p-6 text-rose-950 space-y-2 shadow-xs">
            <div class="flex items-center gap-2 font-black text-sm text-rose-900">
                <i class="fa-solid fa-circle-xmark text-rose-600 text-base"></i>
                <span>Permohonan Tempahan Tidak Diluluskan</span>
            </div>
            <p class="text-xs bg-white p-3.5 rounded-2xl border border-rose-200 text-rose-800">
                <b>Sebab Penolakan:</b> {{ $tempahan->catatan_unit_media ?? 'Tiada krew media tersedia pada tarikh tersebut / pertindihan jadual program utama.' }}
            </p>
        </div>
    @endif

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Detailed Application Breakdown -->
        <div class="lg:col-span-2 space-y-6">

            <!-- 1. Maklumat Pemohon & Program -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="font-black text-slate-900 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-calendar-days text-indigo-600"></i>
                        <span>Maklumat Program &amp; Pemohon</span>
                    </h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $keutamaan['bg'] }}">
                        Keutamaan: {{ $keutamaan['label'] }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-500 block text-[11px]">Nama Program:</span>
                        <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $tempahan->nama_program }}</span>
                    </div>

                    <div>
                        <span class="text-slate-500 block text-[11px]">Tarikh &amp; Masa Program:</span>
                        <span class="font-mono font-bold text-slate-900 mt-0.5 block">
                            {{ $tempahan->tarikh_program->format('d/m/Y') }} 
                            @if($tempahan->tarikh_tamat && $tempahan->tarikh_tamat->ne($tempahan->tarikh_program))
                                hingga {{ $tempahan->tarikh_tamat->format('d/m/Y') }}
                            @endif
                            ({{ $tempahan->masa_mula }} - {{ $tempahan->masa_tamat }})
                        </span>
                    </div>

                    <div>
                        <span class="text-slate-500 block text-[11px]">Lokasi Majlis / Program:</span>
                        <span class="font-semibold text-slate-900 mt-0.5 block">{{ $tempahan->lokasi }}</span>
                    </div>

                    <div>
                        <span class="text-slate-500 block text-[11px]">Penganjur / Bahagian:</span>
                        <span class="font-semibold text-slate-900 mt-0.5 block">{{ $tempahan->penganjur }}</span>
                    </div>

                    <div>
                        <span class="text-slate-500 block text-[11px]">Pegawai Penyelaras / PIC:</span>
                        <span class="font-semibold text-slate-900 mt-0.5 block">{{ $tempahan->pegawai_bertanggungjawab }}</span>
                    </div>

                    <div>
                        <span class="text-slate-500 block text-[11px]">Anggaran Peserta:</span>
                        <span class="font-semibold text-slate-900 mt-0.5 block">{{ $tempahan->anggaran_peserta ? number_format($tempahan->anggaran_peserta) . ' orang' : '-' }}</span>
                    </div>
                </div>

                <div class="pt-4 mt-2 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs bg-slate-50/70 p-3.5 rounded-2xl">
                    <div>
                        <span class="text-slate-500 block text-[11px]">Pemohon:</span>
                        <span class="font-bold text-slate-900">{{ $tempahan->nama_pemohon }} ({{ $tempahan->jawatan }})</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-[11px]">Bahagian / Pejabat:</span>
                        <span class="font-medium text-slate-800">{{ $tempahan->bahagian_unit_jajahan }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-[11px]">No. Telefon:</span>
                        <span class="font-mono font-bold text-indigo-700">{{ $tempahan->no_telefon }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block text-[11px]">Emel:</span>
                        <span class="font-mono text-slate-700">{{ $tempahan->emel ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- 2. Jenis Perkhidmatan Media & Keperluan Khusus -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xs space-y-5">
                <div class="pb-3 border-b border-slate-100">
                    <h3 class="font-black text-slate-900 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-film text-indigo-600"></i>
                        <span>Jenis Perkhidmatan Media Yang Dimohon</span>
                    </h3>
                </div>

                <!-- Checkbox tags selected -->
                <div class="flex flex-wrap gap-2">
                    @if($tempahan->jenis_permohonan && is_array($tempahan->jenis_permohonan))
                        @foreach($tempahan->jenis_permohonan as $jenis)
                            <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-indigo-50 text-indigo-800 border border-indigo-200 flex items-center gap-1.5 shadow-2xs">
                                <i class="fa-solid fa-check text-indigo-600 text-[10px]"></i>
                                <span>{{ $jenis }}</span>
                            </span>
                        @endforeach
                    @endif
                </div>

                <!-- Perincian Fotografi -->
                @if(!empty($tempahan->butiran_fotografi))
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-2">
                        <div class="font-bold text-slate-900 flex items-center gap-2">
                            <i class="fa-solid fa-camera text-indigo-600"></i>
                            <span>Fokus Liputan Fotografi:</span>
                        </div>
                        @if(!empty($tempahan->butiran_fotografi['fokus']) && is_array($tempahan->butiran_fotografi['fokus']))
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($tempahan->butiran_fotografi['fokus'] as $f)
                                    <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 font-medium text-slate-700">
                                        &bull; {{ $f }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                        @if(!empty($tempahan->butiran_fotografi['catatan']))
                            <div class="text-slate-600 mt-1">
                                <b>Catatan:</b> {{ $tempahan->butiran_fotografi['catatan'] }}
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Perincian Poster / Banner -->
                @if(!empty($tempahan->butiran_poster))
                    <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200 text-xs space-y-2">
                        <div class="font-bold text-amber-950 flex items-center gap-2">
                            <i class="fa-solid fa-palette text-amber-600"></i>
                            <span>Keperluan Reka Bentuk Poster / Banner:</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-slate-700">
                            <div><b>Tajuk:</b> {{ $tempahan->butiran_poster['tajuk'] ?? '-' }}</div>
                            <div><b>Saiz:</b> {{ $tempahan->butiran_poster['saiz'] ?? '-' }}</div>
                            <div class="sm:col-span-2"><b>Teks Utama:</b> {{ $tempahan->butiran_poster['teks'] ?? '-' }}</div>
                            <div><b>Logo:</b> {{ $tempahan->butiran_poster['logo'] ?? '-' }}</div>
                            <div><b>Tarikh Siap:</b> {{ $tempahan->butiran_poster['tarikh_siap'] ?? '-' }}</div>
                        </div>
                    </div>
                @endif

                <!-- Perincian Video -->
                @if(!empty($tempahan->butiran_video))
                    <div class="p-4 rounded-2xl bg-indigo-50/60 border border-indigo-200 text-xs space-y-2">
                        <div class="font-bold text-indigo-950 flex items-center gap-2">
                            <i class="fa-solid fa-video text-indigo-600"></i>
                            <span>Keperluan Videografi &amp; Montaj:</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-slate-700">
                            <div><b>Tujuan:</b> {{ $tempahan->butiran_video['tujuan'] ?? '-' }}</div>
                            <div><b>Durasi:</b> {{ $tempahan->butiran_video['durasi'] ?? '-' }}</div>
                            <div><b>Platform:</b> {{ $tempahan->butiran_video['platform'] ?? '-' }}</div>
                            <div><b>Tarikh Siap:</b> {{ $tempahan->butiran_video['tarikh_siap'] ?? '-' }}</div>
                        </div>
                    </div>
                @endif

                @if(!empty($tempahan->butiran_lain))
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
                        <span class="font-bold text-slate-700 block mb-1">Catatan Tambahan:</span>
                        <p class="text-slate-600 whitespace-pre-line">{{ $tempahan->butiran_lain }}</p>
                    </div>
                @endif
            </div>

            <!-- 3. Lampiran Fail Sokongan -->
            <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xs space-y-3">
                <h3 class="font-black text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-paperclip text-indigo-600"></i>
                    <span>Lampiran &amp; Dokumen Sokongan</span>
                </h3>

                @if(!empty($tempahan->lampiran) && count($tempahan->lampiran) > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        @foreach($tempahan->lampiran as $item)
                            <a href="{{ asset('storage/' . $item['fail']) }}" target="_blank" class="p-3 rounded-2xl bg-slate-50 hover:bg-slate-100 border border-slate-200 flex items-center justify-between transition group">
                                <div class="flex items-center gap-2.5 truncate">
                                    <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold shrink-0">
                                        <i class="fa-solid fa-file-lines"></i>
                                    </div>
                                    <div class="truncate">
                                        <div class="font-bold text-xs text-slate-800 group-hover:text-indigo-600 truncate">{{ $item['nama_asal'] }}</div>
                                        <div class="text-[10px] text-slate-400 uppercase font-mono">{{ $item['format'] ?? 'FAIL' }}</div>
                                    </div>
                                </div>
                                <i class="fa-solid fa-arrow-down text-xs text-slate-400 group-hover:text-indigo-600 ml-2"></i>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-xs text-slate-400 italic py-3 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                        Tiada dokumen lampiran dimuat naik.
                    </div>
                @endif
            </div>

        </div>

        <!-- Right Col: Timeline & Action Control Panel -->
        <div class="space-y-6">

            <!-- Panel Kelulusan Admin Unit Media -->
            @if(Auth::user()->canManageMedia())
                <div class="bg-gradient-to-br from-slate-900 to-indigo-950 text-white rounded-3xl p-6 shadow-xl border border-slate-800 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
                            <h3 class="font-black text-sm text-white">Panel Tindakan Unit Media</h3>
                        </div>
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-indigo-500/20 text-indigo-300 font-bold border border-indigo-500/30">ADMIN</span>
                    </div>

                    <form action="{{ route('media.tindakan', $tempahan->id) }}" method="POST" class="space-y-4 text-xs">
                        @csrf

                        <div>
                            <label class="block font-bold text-slate-300 mb-1.5">Keputusan Permohonan <span class="text-rose-400">*</span></label>
                            <select name="keputusan" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-700 bg-slate-800/90 text-white font-bold focus:ring-2 focus:ring-indigo-500 focus:outline-hidden">
                                <option value="Diluluskan" {{ $tempahan->status === 'Diluluskan' ? 'selected' : '' }}>✅ Luluskan Permohonan</option>
                                <option value="Perlu Pembetulan" {{ $tempahan->status === 'Perlu Pembetulan' ? 'selected' : '' }}>⚠️ Minta Pembetulan Maklumat</option>
                                <option value="Ditolak" {{ $tempahan->status === 'Ditolak' ? 'selected' : '' }}>❌ Tolak Permohonan</option>
                                <option value="Selesai" {{ $tempahan->status === 'Selesai' ? 'selected' : '' }}>🏁 Tandakan Selesai Liputan</option>
                                <option value="Dibatalkan" {{ $tempahan->status === 'Dibatalkan' ? 'selected' : '' }}>🚫 Batalkan Tempahan</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-300 mb-1.5">Pegawai / Krew Media Bertugas</label>
                            <input type="text" name="pegawai_media_bertugas" value="{{ old('pegawai_media_bertugas', $tempahan->pegawai_media_bertugas) }}" placeholder="Cth: En. Ahmad Jurufoto &amp; Pn. Siti Videografi" class="w-full px-3.5 py-2 rounded-xl border border-slate-700 bg-slate-800/90 text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-300 mb-1.5">Catatan / Ulasan Unit Media</label>
                            <textarea name="catatan_unit_media" rows="3" placeholder="Sila masukkan nota penugasan, arahan pembetulan atau sebab penolakan..." class="w-full px-3.5 py-2 rounded-xl border border-slate-700 bg-slate-800/90 text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 text-xs">{{ old('catatan_unit_media', $tempahan->catatan_unit_media) }}</textarea>
                        </div>

                        <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-black text-xs transition shadow-lg shadow-amber-500/20 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-check-double"></i>
                            <span>Simpan Keputusan Tindakan</span>
                        </button>
                    </form>
                </div>
            @endif

            <!-- Status Tracking & Timeline -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs space-y-4">
                <h3 class="font-black text-slate-900 text-sm pb-2 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-timeline text-indigo-600"></i>
                    <span>Jejak Status Permohonan</span>
                </h3>

                <div class="space-y-4 text-xs">
                    <!-- Step 1: Dihantar -->
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div>
                            <div class="font-bold text-slate-900">Permohonan Dihantar</div>
                            <div class="text-[11px] text-slate-500">{{ $tempahan->tarikh_hantar ? $tempahan->tarikh_hantar->format('d/m/Y H:i') : $tempahan->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>

                    <!-- Step 2: Semakan Unit Media -->
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full {{ in_array($tempahan->status, ['Diluluskan', 'Perlu Pembetulan', 'Ditolak', 'Selesai']) ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700 animate-pulse' }} flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5">
                            <i class="fa-solid {{ in_array($tempahan->status, ['Diluluskan', 'Perlu Pembetulan', 'Ditolak', 'Selesai']) ? 'fa-check' : 'fa-clock' }}"></i>
                        </div>
                        <div>
                            <div class="font-bold text-slate-900">Semakan Unit Media</div>
                            <div class="text-[11px] text-slate-500">{{ $tempahan->tarikh_kelulusan ? $tempahan->tarikh_kelulusan->format('d/m/Y H:i') : 'Dalam proses semakan krew media' }}</div>
                        </div>
                    </div>

                    <!-- Step 3: Keputusan Akhir -->
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 rounded-full {{ $tempahan->status === 'Diluluskan' || $tempahan->status === 'Selesai' ? 'bg-emerald-100 text-emerald-700' : ($tempahan->status === 'Ditolak' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-400') }} flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5">
                            <i class="fa-solid {{ $tempahan->status === 'Diluluskan' || $tempahan->status === 'Selesai' ? 'fa-check' : ($tempahan->status === 'Ditolak' ? 'fa-xmark' : 'fa-circle') }}"></i>
                        </div>
                        <div>
                            <div class="font-bold text-slate-900">Pengesahan Tempahan</div>
                            <div class="text-[11px] text-slate-500">
                                @if($tempahan->status === 'Diluluskan')
                                    Disahkan oleh: {{ $tempahan->pelulus->name ?? 'Admin Unit Media' }}
                                @elseif($tempahan->status === 'Ditolak')
                                    Permohonan ditolak
                                @elseif($tempahan->status === 'Perlu Pembetulan')
                                    Menunggu pembetulan pemohon
                                @else
                                    Menunggu keputusan kelulusan
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
