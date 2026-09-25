@extends('layouts.app')

@section('title', 'Butiran Aktiviti - ' . ($actionList->tajuk_aktiviti ?: $actionList->no_bil))

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gradient-to-r from-emerald-900 via-teal-900 to-slate-900 p-6 rounded-3xl text-white shadow-xl">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="px-3 py-0.5 rounded-full text-xs font-bold bg-white/20 border border-white/30">
                    No. Rujukan: #{{ $actionList->no_bil }}
                </span>
                <span class="px-3 py-0.5 rounded-full text-xs font-bold {{ $actionList->status_badge_class }}">
                    {{ $actionList->status }}
                </span>
            </div>
            <h1 class="text-2xl font-black">{{ $actionList->tajuk_aktiviti ?: 'Catatan Aktiviti' }}</h1>
            <p class="text-xs text-slate-300 flex items-center gap-4">
                <span><i class="fa-regular fa-calendar mr-1"></i> {{ $actionList->tarikh_formatted }}</span>
                @if($actionList->masa_mula || $actionList->masa_selesai)
                    <span><i class="fa-regular fa-clock mr-1"></i> {{ $actionList->masa_mula }} - {{ $actionList->masa_selesai }}</span>
                @endif
                <span><i class="fa-solid fa-map-pin mr-1"></i> Jajahan {{ $actionList->jajahan }}</span>
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('action-list.cetak', $actionList->id) }}" target="_blank" class="px-3.5 py-2 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition flex items-center gap-1.5 border border-white/20">
                <i class="fa-solid fa-print"></i> Cetak
            </a>
            <a href="{{ route('action-list.edit', $actionList->id) }}" class="px-3.5 py-2 rounded-2xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs transition flex items-center gap-1.5 shadow-md">
                <i class="fa-solid fa-pen-to-square"></i> Kemaskini
            </a>
            <a href="{{ route('action-list.index') }}" class="px-3.5 py-2 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition flex items-center gap-1.5 border border-white/20">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Main Content (2 cols) -->
        <div class="md:col-span-2 space-y-6">
            <!-- Maklumat Aktiviti -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
                <h2 class="font-black text-slate-900 text-sm uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
                    <i class="fa-solid fa-align-left text-emerald-600"></i> Perincian & Catatan Aktiviti
                </h2>
                <div class="text-slate-800 text-sm leading-relaxed whitespace-pre-line bg-slate-50/50 p-4 rounded-2xl border border-slate-100">
                    {{ $actionList->maklumat_aktiviti }}
                </div>
            </div>

            <!-- Tindakan Susulan (Jika ada) -->
            @if($actionList->tindakan_susulan)
                <div class="bg-amber-50/60 p-6 rounded-3xl border border-amber-200 shadow-xs space-y-3">
                    <h2 class="font-black text-amber-900 text-sm uppercase tracking-wider flex items-center gap-2 border-b border-amber-200/60 pb-3">
                        <i class="fa-solid fa-list-check text-amber-600"></i> Tindakan Susulan / Catatan Tambahan
                    </h2>
                    <div class="text-amber-950 text-sm leading-relaxed whitespace-pre-line">
                        {{ $actionList->tindakan_susulan }}
                    </div>
                </div>
            @endif

            <!-- Lampiran (Jika ada) -->
            @if($actionList->lampiran)
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4">
                    <h2 class="font-black text-slate-900 text-sm uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
                        <i class="fa-solid fa-paperclip text-emerald-600"></i> Dokumen / Gambar Lampiran
                    </h2>
                    @php
                        $ext = pathinfo($actionList->lampiran, PATHINFO_EXTENSION);
                        $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                    @endphp

                    @if($isImage)
                        <div class="space-y-2">
                            <a href="{{ Storage::url($actionList->lampiran) }}" target="_blank" class="block overflow-hidden rounded-2xl border border-slate-200 max-h-96 group">
                                <img src="{{ Storage::url($actionList->lampiran) }}" alt="Lampiran Aktiviti" class="w-full object-contain group-hover:scale-105 transition duration-300">
                            </a>
                            <a href="{{ Storage::url($actionList->lampiran) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 hover:text-emerald-900">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Gambar Penuh
                            </a>
                        </div>
                    @else
                        <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg">
                                    <i class="fa-solid fa-file-lines"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800 text-xs uppercase">Fail Lampiran (.{{ strtoupper($ext) }})</p>
                                    <p class="text-[11px] text-slate-500">Klik butang di sebelah untuk memuat turun atau membaca.</p>
                                </div>
                            </div>
                            <a href="{{ Storage::url($actionList->lampiran) }}" target="_blank" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition flex items-center gap-1.5">
                                <i class="fa-solid fa-download"></i> Muat Turun
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Sidebar Meta (1 col) -->
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs space-y-4 text-xs">
                <h2 class="font-black text-slate-900 text-sm uppercase tracking-wider border-b border-slate-100 pb-3">
                    Ringkasan Maklumat
                </h2>

                <div class="space-y-3.5">
                    <div>
                        <span class="text-slate-400 font-bold uppercase block text-[10px]">Kategori Aktiviti</span>
                        <span class="font-bold text-slate-800 text-sm">{{ $actionList->kategori_label }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 font-bold uppercase block text-[10px]">Pejabat Jajahan</span>
                        <span class="font-bold text-slate-800 text-sm">Jajahan {{ $actionList->jajahan }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 font-bold uppercase block text-[10px]">Lokasi / Premis</span>
                        <span class="font-semibold text-slate-700">{{ $actionList->lokasi ?: '-' }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 font-bold uppercase block text-[10px]">Pegawai Bertugas / Staf</span>
                        <span class="font-semibold text-slate-700">{{ $actionList->nama_pegawai ?: '-' }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 font-bold uppercase block text-[10px]">Tahap Keutamaan</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full font-bold text-[11px] {{ $actionList->keutamaan_badge_class }}">
                            {{ $actionList->keutamaan }}
                        </span>
                    </div>

                    <div>
                        <span class="text-slate-400 font-bold uppercase block text-[10px]">Direkodkan Oleh</span>
                        <span class="font-semibold text-slate-700">{{ $actionList->creator->name ?? 'Sistem' }}</span>
                        <span class="text-slate-400 text-[10px] block">{{ $actionList->created_at->format('d/m/Y H:i A') }}</span>
                    </div>

                    @if($actionList->updated_at->ne($actionList->created_at))
                        <div>
                            <span class="text-slate-400 font-bold uppercase block text-[10px]">Kemaskini Terakhir</span>
                            <span class="text-slate-600 text-[11px]">{{ $actionList->updated_at->format('d/m/Y H:i A') }}</span>
                        </div>
                    @endif
                </div>

                @if(Auth::user()->isSuperAdmin() || Auth::user()->isAdminJajahan())
                    <div class="pt-4 border-t border-slate-100">
                        <form action="{{ route('action-list.destroy', $actionList->id) }}" method="POST" onsubmit="return confirm('Adakah anda pasti ingin memadam rekod aktiviti ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full py-2 px-3 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition border border-rose-200 flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-trash-can"></i> Padam Rekod Aktiviti
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
