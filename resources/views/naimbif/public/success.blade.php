@extends('layouts.app')

@section('title', 'Permohonan Berjaya Dihantar - NAIMbif')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden text-center">
        <!-- Top Banner -->
        <div class="bg-gradient-to-r from-emerald-700 to-teal-700 p-8 sm:p-10 text-white relative">
            <div class="w-20 h-20 mx-auto rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white border-2 border-white/30 shadow-lg mb-4">
                <i class="fas fa-check-circle text-4xl text-emerald-300"></i>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Permohonan Berjaya Dihantar!</h1>
            <p class="text-xs sm:text-sm text-emerald-100 mt-2 max-w-md mx-auto">
                Borang permohonan penyertaan Ladang Bridlot NAIMbif anda telah selamat diterima oleh Jabatan Perkhidmatan Veterinar Negeri Kelantan.
            </p>
        </div>

        <!-- Reference Code Box -->
        <div class="p-8 sm:p-10 space-y-6">
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 text-center max-w-md mx-auto">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Nombor Rujukan Permohonan Anda:</span>
                <div class="text-2xl sm:text-3xl font-black text-emerald-700 font-mono tracking-wider mt-1 select-all">
                    {{ $application->no_rujukan }}
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Sila simpan nombor rujukan ini untuk semakan status permohonan.</p>
            </div>

            <!-- Summary Table -->
            <div class="text-left border border-slate-200 rounded-2xl overflow-hidden text-xs">
                <div class="bg-slate-100 px-4 py-3 font-bold text-slate-700 border-b border-slate-200">
                    Ringkasan Maklumat Permohonan
                </div>
                <div class="divide-y divide-slate-100 p-2">
                    <div class="grid grid-cols-3 p-2">
                        <span class="text-slate-500 font-medium">Nama Pemohon:</span>
                        <span class="col-span-2 font-bold text-slate-800">{{ $application->nama }}</span>
                    </div>
                    <div class="grid grid-cols-3 p-2">
                        <span class="text-slate-500 font-medium">No. Kad Pengenalan:</span>
                        <span class="col-span-2 font-semibold text-slate-800 font-mono">{{ $application->formatted_no_kp }}</span>
                    </div>
                    <div class="grid grid-cols-3 p-2">
                        <span class="text-slate-500 font-medium">Jajahan Ladang:</span>
                        <span class="col-span-2 font-semibold text-slate-800">{{ $application->jajahan_ladang ?: $application->jajahan }}</span>
                    </div>
                    <div class="grid grid-cols-3 p-2">
                        <span class="text-slate-500 font-medium">Jumlah Ternakan Semasa:</span>
                        <span class="col-span-2 font-bold text-emerald-700">{{ $application->total_ternakan }} Ekor</span>
                    </div>
                    <div class="grid grid-cols-3 p-2">
                        <span class="text-slate-500 font-medium">Tarikh Hantar:</span>
                        <span class="col-span-2 font-semibold text-slate-800">{{ $application->created_at->format('d/m/Y, h:i A') }}</span>
                    </div>
                    <div class="grid grid-cols-3 p-2">
                        <span class="text-slate-500 font-medium">Status Semasa:</span>
                        <span class="col-span-2">{!! $application->status_badge !!}</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-4">
                <a href="{{ route('naimbif.public.edit', $application->no_rujukan) }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-3 rounded-xl text-xs font-bold text-emerald-800 bg-emerald-100 hover:bg-emerald-200 border border-emerald-300 transition-colors">
                    <i class="fas fa-edit mr-2 text-emerald-600"></i> Ubah / Kemas Kini Maklumat
                </a>
                <a href="{{ route('naimbif.public.print', $application->no_rujukan) }}" target="_blank"
                   class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-3 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 shadow-md transition-colors">
                    <i class="fas fa-print mr-2 text-amber-400"></i> Cetak Salinan Borang (PDF)
                </a>
                <a href="{{ route('naimbif.public.check_status', ['carian' => $application->no_rujukan]) }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-3 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 transition-colors">
                    <i class="fas fa-search mr-2"></i> Semak Status
                </a>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <a href="{{ route('naimbif.public.home') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Laman Utama
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
