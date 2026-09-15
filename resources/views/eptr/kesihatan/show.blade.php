@extends('layouts.app')

@section('title', 'EPTR - Butiran Rekod Kesihatan ' . $rekod->no_rujukan_kesihatan)
@section('page_title', 'Butiran Rekod Program Kesihatan Ternakan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Action Bar -->
    <div class="flex items-center justify-between">
        <a href="{{ route('eptr.kesihatan.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-slate-900 bg-white px-3.5 py-2 rounded-xl border border-slate-200 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Senarai Kesihatan</span>
        </a>

        <div class="flex items-center gap-2">
            <a href="{{ route('eptr.show', $rekod->ternakan_id) }}" class="px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-md transition flex items-center gap-2">
                <i class="fa-solid fa-cow"></i>
                <span>Lihat Profil Ternakan (Tag: {{ $rekod->ternakan->no_tag ?? 'ID-'.$rekod->ternakan_id }})</span>
            </a>
            <button onclick="window.print()" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-md transition flex items-center gap-1.5">
                <i class="fa-solid fa-print text-teal-400"></i>
                <span>Cetak Rekod</span>
            </button>
        </div>
    </div>

    <!-- Official Health Card View -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xl space-y-6">
        
        <!-- Header -->
        <div class="border-b border-slate-200 pb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-teal-700 text-white flex items-center justify-center text-3xl shadow-md">
                    <i class="fa-solid fa-notes-medical"></i>
                </div>
                <div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-teal-100 text-teal-900 border border-teal-300">
                        {{ $rekod->jenis_program }}
                    </span>
                    <h2 class="text-xl font-black text-slate-900 mt-1">{{ $rekod->nama_vaksin_atau_ubat }}</h2>
                    <p class="text-xs text-slate-500 font-mono">No. Rujukan: <b>{{ $rekod->no_rujukan_kesihatan }}</b></p>
                </div>
            </div>

            <div class="text-right">
                <div class="text-xs text-slate-500">Tarikh Rawatan:</div>
                <div class="font-bold text-slate-900 text-sm">{{ $rekod->tarikh_rawatan ? $rekod->tarikh_rawatan->format('d/m/Y') : '-' }}</div>
                @if($rekod->tarikh_ulangan_dos)
                    <div class="text-[11px] text-amber-800 font-mono font-bold mt-1">
                        <i class="fa-solid fa-bell mr-1"></i> Dos Booster: {{ $rekod->tarikh_ulangan_dos->format('d/m/Y') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Left: Maklumat Ternakan & Pemunya -->
            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200 space-y-3 text-xs">
                <div class="font-black uppercase text-teal-900 text-xs flex items-center gap-2 pb-2 border-b border-slate-200">
                    <i class="fa-solid fa-cow text-teal-700"></i>
                    <span>Maklumat Ternakan & Pemunya</span>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <div class="text-[11px] text-slate-500">No. Tag Telinga:</div>
                        <div class="font-mono font-bold text-emerald-800 text-sm">{{ $rekod->ternakan->no_tag ?? 'ID-'.$rekod->ternakan_id }}</div>
                    </div>
                    <div>
                        <div class="text-[11px] text-slate-500">Spesies & Baka:</div>
                        <div class="font-bold text-slate-900 capitalize">{{ $rekod->ternakan->jenis_ternakan }} &bull; {{ $rekod->ternakan->baka }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-200">
                    <div>
                        <div class="text-[11px] text-slate-500">Nama Pemunya:</div>
                        <div class="font-bold text-slate-900">{{ $rekod->ternakan->pemunya->nama ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-[11px] text-slate-500">No. Telefon:</div>
                        <div class="font-mono text-slate-800">{{ $rekod->ternakan->pemunya->no_telefon ?? '-' }}</div>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-200">
                    <div class="text-[11px] text-slate-500">Lokasi / Jajahan:</div>
                    <div class="font-medium text-slate-800">{{ $rekod->lokasi_pemeriksaan ?? '-' }} (Jajahan: {{ $rekod->jajahan }})</div>
                </div>
            </div>

            <!-- Right: Butiran Klinikal & Rawatan -->
            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200 space-y-3 text-xs">
                <div class="font-black uppercase text-teal-900 text-xs flex items-center gap-2 pb-2 border-b border-slate-200">
                    <i class="fa-solid fa-stethoscope text-teal-700"></i>
                    <span>Pemeriksaan Klinikal & Dos</span>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <div class="text-[11px] text-slate-500">Status Kesihatan:</div>
                        <div class="font-bold text-emerald-800">{{ $rekod->status_kesihatan }}</div>
                    </div>
                    <div>
                        <div class="text-[11px] text-slate-500">Dos Diberikan:</div>
                        <div class="font-mono font-bold text-slate-900">{{ $rekod->dos_diberikan ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-[11px] text-slate-500">Berat Semasa:</div>
                        <div class="font-mono font-bold text-slate-900">{{ $rekod->berat_semasa_kg ? $rekod->berat_semasa_kg . ' KG' : '-' }}</div>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-200">
                    <div class="text-[11px] text-slate-500">Suhu Badan (&deg;C):</div>
                    <div class="font-mono font-bold text-slate-900">{{ $rekod->suhu_badan_celsius ? $rekod->suhu_badan_celsius . ' °C' : 'Normal' }}</div>
                </div>

                <div class="pt-2 border-t border-slate-200">
                    <div class="text-[11px] text-slate-500">Pegawai Veterinar Pemeriksa:</div>
                    <div class="font-bold text-slate-900">{{ $rekod->pegawai_pemeriksa }}</div>
                </div>
            </div>

        </div>

        <!-- Diagnosis & Prosedur Rawatan -->
        <div class="p-5 bg-teal-50/40 rounded-2xl border border-teal-200 space-y-3 text-xs">
            <div>
                <div class="font-bold text-teal-950 uppercase text-[11px] mb-1">Diagnosis & Tujuan Rawatan:</div>
                <p class="text-slate-800 bg-white p-3 rounded-xl border border-teal-200">
                    {{ $rekod->diagnosis_atau_tujuan ?? 'Tiada maklumat diagnosis khusus direkodkan.' }}
                </p>
            </div>

            <div>
                <div class="font-bold text-teal-950 uppercase text-[11px] mb-1">Tindakan & Prosedur Rawatan Diberikan:</div>
                <p class="text-slate-800 bg-white p-3 rounded-xl border border-teal-200">
                    {{ $rekod->tindakan_rawatan ?? 'Suntikan dos vaksin / ubat pencegahan mengikut jadual piawai veterinar.' }}
                </p>
            </div>

            @if($rekod->catatan_dan_syor)
                <div>
                    <div class="font-bold text-teal-950 uppercase text-[11px] mb-1">Nasihat & Syor Penjagaan Veterinar:</div>
                    <p class="text-slate-800 bg-white p-3 rounded-xl border border-teal-200">
                        {{ $rekod->catatan_dan_syor }}
                    </p>
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
