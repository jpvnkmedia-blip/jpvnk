@extends('layouts.app')

@section('title', 'Maklumat Temujanji & Kad Rawatan - ' . $temujanji->no_temujanji)
@section('page_title', 'Temujanji Klinik Veterinar: ' . $temujanji->no_temujanji)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('klinik.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 bg-white px-3.5 py-2 rounded-xl border border-slate-200 transition">
            &larr; Kembali ke Senarai Temujanji
        </a>
            @if(Auth::user()->canManageActionList())
                <a href="{{ route('action-list.create', ['temujanji_id' => $temujanji->id]) }}" class="px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5" title="Jana Borang Action List PK-RK-61">
                    <i class="fa-solid fa-clipboard-list"></i> Jana Action List (PK-RK-61)
                </a>
            @endif
            @if(Auth::user()->isStaff() && $temujanji->status !== 'Selesai')
                <a href="{{ route('klinik.rawatan.create', $temujanji->id) }}" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                    <i class="fa-solid fa-stethoscope"></i> Rekod Rawatan Veterinar
                </a>
            @endif
            @if($temujanji->rawatan)
                <a href="{{ route('klinik.cetak-kad-rawatan', $temujanji->id) }}" target="_blank" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs shadow-md transition flex items-center gap-1.5">
                    <i class="fa-solid fa-print"></i> Cetak Kad Rawatan Pesakit
                </a>
            @endif
        </div>
    </div>

    <!-- Appointment Overview Card -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs space-y-6 text-xs">
        
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <span class="font-mono text-base font-black text-rose-900">{{ $temujanji->no_temujanji }}</span>
                <div class="text-[11px] text-slate-500 mt-0.5">{{ $temujanji->klinik_jajahan }}</div>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $temujanji->status === 'Selesai' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                Status: {{ $temujanji->status }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">MAKLUMAT PEMILIK</span>
                <div>Nama: <b>{{ $temujanji->pemilik->name ?? '-' }}</b></div>
                <div>No KP: <span class="font-mono font-bold">{{ $temujanji->pemilik->ic_number ?? '-' }}</span></div>
                <div>No Tel: <b>{{ $temujanji->pemilik->phone ?? '-' }}</b></div>
                <div>Alamat: {{ $temujanji->pemilik->address ?? '-' }}</div>
            </div>

            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">BUTIRAN HAIWAN</span>
                <div>Jenis: <b class="text-rose-900">{{ $temujanji->jenis_haiwan }}</b> @if($temujanji->nama_haiwan) ({{ $temujanji->nama_haiwan }}) @endif</div>
                <div>Baka: <b>{{ $temujanji->baka ?? 'Baka Tempatan' }}</b></div>
                <div>Jantina & Umur: <b>{{ $temujanji->jantina_haiwan }} &bull; {{ $temujanji->umur_haiwan ?? '-' }}</b></div>
                <div>Tarikh Temujanji: <b>{{ $temujanji->tarikh_temujanji ? $temujanji->tarikh_temujanji->format('d/m/Y') : '-' }} ({{ $temujanji->sesi }})</b></div>
            </div>
        </div>

        <div>
            <span class="font-bold text-slate-700 uppercase block mb-1">Tujuan Rawatan / Simptom Dilaporkan:</span>
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-slate-700">
                {{ $temujanji->simptom_atau_tujuan }}
            </div>
        </div>

        <!-- Medical Treatment Record If Exists -->
        @if($temujanji->rawatan)
            <div class="pt-6 border-t-2 border-slate-100 space-y-4">
                <div class="flex items-center gap-2 text-rose-900 font-bold text-sm">
                    <i class="fa-solid fa-notes-medical text-lg"></i>
                    <span>Rekod Rawatan Klinikal Veterinar ({{ $temujanji->rawatan->no_rekod_rawatan }})</span>
                </div>

                <div class="grid grid-cols-3 gap-3 bg-rose-50/50 p-4 rounded-2xl border border-rose-200/80">
                    <div>
                        <span class="text-[10px] text-slate-500 font-bold uppercase block">Pegawai Veterinar:</span>
                        <span class="font-bold text-slate-900">{{ $temujanji->rawatan->pegawai_veterinar }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-500 font-bold uppercase block">Berat Badan / Suhu:</span>
                        <span class="font-bold text-slate-900">{{ $temujanji->rawatan->berat_badan_kg ?? '-' }} kg &bull; {{ $temujanji->rawatan->suhu_celsius ?? '-' }} °C</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-500 font-bold uppercase block">Kos Rawatan:</span>
                        <span class="font-black text-emerald-800">RM {{ number_format($temujanji->rawatan->kos_rawatan, 2) }}</span>
                    </div>
                </div>

                <div class="space-y-2">
                    <div>
                        <span class="font-bold text-slate-800 uppercase block text-[11px]">Diagnosis Klinikal:</span>
                        <p class="bg-slate-50 p-3 rounded-xl border border-slate-200 text-slate-700">{{ $temujanji->rawatan->diagnosis }}</p>
                    </div>

                    <div>
                        <span class="font-bold text-slate-800 uppercase block text-[11px]">Rawatan Diberikan:</span>
                        <p class="bg-slate-50 p-3 rounded-xl border border-slate-200 text-slate-700">{{ $temujanji->rawatan->rawatan_diberikan }}</p>
                    </div>

                    @if($temujanji->rawatan->ubat_diberikan)
                        <div>
                            <span class="font-bold text-slate-800 uppercase block text-[11px]">Preskripsi Ubat & Vaksin:</span>
                            <p class="bg-slate-50 p-3 rounded-xl border border-slate-200 font-mono text-emerald-900">{{ $temujanji->rawatan->ubat_diberikan }} @if($temujanji->rawatan->vaksinasi) &bull; Vaksin: {{ $temujanji->rawatan->vaksinasi }} @endif</p>
                        </div>
                    @endif

                    @if($temujanji->rawatan->nasihat_veterinar)
                        <div>
                            <span class="font-bold text-slate-800 uppercase block text-[11px]">Nasihat & Penjagaan Rumah:</span>
                            <p class="bg-slate-50 p-3 rounded-xl border border-slate-200 text-slate-600 italic">{{ $temujanji->rawatan->nasihat_veterinar }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

    </div>

</div>
@endsection
