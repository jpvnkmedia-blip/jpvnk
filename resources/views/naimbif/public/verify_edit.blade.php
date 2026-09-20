@extends('layouts.app')

@section('title', 'Pengesahan Keselamatan Dwi-Faktor Pemohon - ' . $application->no_rujukan)

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
    <div class="max-w-md w-full space-y-6 bg-white p-8 sm:p-10 rounded-3xl shadow-xl border border-slate-200 text-center">
        
        <!-- Shield Icon Badge -->
        <div class="w-16 h-16 mx-auto rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-3xl border border-emerald-500/20 shadow-sm">
            <i class="fas fa-user-shield"></i>
        </div>

        <div>
            <span class="text-xs uppercase font-extrabold tracking-widest text-emerald-700 bg-emerald-100 px-3 py-1 rounded-full inline-block">
                Pengesahan Keselamatan Dwi-Faktor (2FA)
            </span>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-3">
                Kemas Kini Permohonan
            </h1>
            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                Demi melindungi kerahsiaan dan integriti data penternak daripada diubah oleh pihak ketiga, sila lengkapkan <strong class="text-slate-800">2 maklumat pengesahan</strong> di bawah:
            </p>
        </div>

        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs">
            <span class="text-slate-400 font-semibold">No. Rujukan Permohonan:</span>
            <div class="font-mono font-bold text-emerald-800 text-sm mt-0.5">{{ $application->no_rujukan }}</div>
        </div>

        @if(session('error'))
            <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold text-left flex items-start">
                <i class="fas fa-exclamation-triangle text-rose-500 mr-2 mt-0.5"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        <!-- Form 2FA Verification -->
        <form action="{{ route('naimbif.public.verify_edit', $application->no_rujukan) }}" method="POST" class="space-y-4 text-left">
            @csrf

            <!-- 1. No Kad Pengenalan -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                    1. No. Kad Pengenalan Pemohon <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                        <i class="fas fa-id-card"></i>
                    </span>
                    <input type="text" name="no_kp" value="{{ old('no_kp') }}" required autofocus placeholder="Contoh: 850712035411" maxlength="14"
                           class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm font-mono tracking-wider">
                </div>
                <span class="text-[11px] text-slate-400 block mt-0.5">12 digit No. KP tanpa tanda sengkang (-).</span>
            </div>

            <!-- 2. No Telefon / 4 Digit Terakhir -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                    2. No. Telefon / 4 Digit Terakhir No. Telefon <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                        <i class="fas fa-phone-alt"></i>
                    </span>
                    <input type="text" name="no_telefon" value="{{ old('no_telefon') }}" required placeholder="Contoh: 0199123456 atau 3456" maxlength="15"
                           class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm font-mono tracking-wider">
                </div>
                <span class="text-[11px] text-slate-400 block mt-0.5">Nombor telefon bimbit yang didaftarkan semasa permohonan.</span>
            </div>

            <button type="submit" class="w-full py-3.5 px-6 rounded-xl font-bold text-white bg-emerald-700 hover:bg-emerald-800 shadow-md shadow-emerald-700/20 transition-all flex items-center justify-center text-sm mt-2">
                <i class="fas fa-lock-open mr-2"></i> Sahkan & Buka Borang
            </button>
        </form>

        <div class="pt-2 border-t border-slate-100">
            <a href="{{ route('naimbif.public.check_status', ['carian' => $application->no_rujukan]) }}" class="text-xs font-semibold text-slate-400 hover:text-slate-700">
                <i class="fas fa-arrow-left mr-1"></i> Batal & Kembali ke Status
            </a>
        </div>

    </div>
</div>
@endsection
