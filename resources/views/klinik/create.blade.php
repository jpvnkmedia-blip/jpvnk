@extends('layouts.app')

@section('title', 'Daftar Temujanji Rawatan Klinik')
@section('page_title', 'Klinik Veterinar: Tempahan Temujanji Rawatan')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs">
    
    <div class="border-b border-slate-100 pb-4 mb-6">
        <h3 class="text-base font-bold text-slate-900">Borang Tempahan Temujanji Klinik Veterinar</h3>
        <p class="text-xs text-slate-500 mt-0.5">Sila masukkan butiran haiwan kesayangan / ternakan dan pilih klinik jajahan berhampiran.</p>
    </div>

    <form action="{{ route('klinik.store') }}" method="POST" class="space-y-4 text-xs">
        @csrf

        @if(Auth::user()->isStaff() && count($registeredClients) > 0)
        <div class="p-4 rounded-2xl bg-rose-50/70 border border-rose-200">
            <label class="block font-bold text-rose-900 uppercase mb-1 flex items-center gap-1.5">
                <i class="fa-solid fa-user-pen text-rose-600"></i>
                <span>Daftar Bagi Pihak Pemilik / Orang Awam (Pilihan Admin Klinik)</span>
            </label>
            <select name="user_id" class="w-full px-3.5 py-2.5 text-sm bg-white border border-rose-300 rounded-xl focus:ring-2 focus:ring-rose-500 focus:outline-none">
                <option value="">-- Diri Sendiri / Akaun Semasa ({{ Auth::user()->name }}) --</option>
                @foreach($registeredClients as $client)
                    <option value="{{ $client->id }}" {{ old('user_id') == $client->id ? 'selected' : '' }}>
                        {{ $client->name }} (No. KP: {{ $client->ic_number }} - Tel: {{ $client->phone }}) [{{ $client->role_label }}]
                    </option>
                @endforeach
            </select>
            <p class="text-[11px] text-rose-700 mt-1">Admin Klinik boleh mendaftarkan temujanji rawatan bagi pihak pelanggan / orang awam yang hadir terus ke kaunter atau melalui panggilan.</p>
        </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Jenis Haiwan</label>
                <select name="jenis_haiwan" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none font-bold">
                    <option value="Kucing">Kucing</option>
                    <option value="Lembu">Lembu</option>
                    <option value="Kambing">Kambing</option>
                    <option value="Anjing">Anjing</option>
                    <option value="Kuda">Kuda</option>
                    <option value="Unggas / Burung">Unggas / Burung</option>
                    <option value="Lain-lain">Lain-lain</option>
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Nama / Panggilan Haiwan (Jika Ada)</label>
                <input type="text" name="nama_haiwan" value="{{ old('nama_haiwan') }}" placeholder="Contoh: Comel / Si Tompok" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Baka Haiwan</label>
                <input type="text" name="baka" value="{{ old('baka') }}" placeholder="Contoh: DSH, Persian, KK" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Jantina</label>
                <select name="jantina_haiwan" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
                    <option value="Jantan">Jantan</option>
                    <option value="Betina">Betina</option>
                    <option value="Tidak Diketahui">Tidak Diketahui</option>
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Umur Haiwan</label>
                <input type="text" name="umur_haiwan" placeholder="Contoh: 1 Tahun 2 Bulan" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
            </div>
        </div>

        <div>
            <label class="block font-bold text-slate-700 uppercase mb-1">Tujuan Temujanji / Simptom Penyakit</label>
            <textarea name="simptom_atau_tujuan" rows="3" required placeholder="Nyatakan sebab rawatan (Contoh: Pemeriksaan berkala, suntikan vaksin, hilang selera makan, luka kecederaan)" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none">{{ old('simptom_atau_tujuan') }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Cadangan Temujanji</label>
                <input type="date" name="tarikh_temujanji" value="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Sesi Masa</label>
                <select name="sesi" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none font-bold">
                    <option value="Pagi (8:30 AM - 12:30 PM)">Pagi (8:30 AM - 12:30 PM)</option>
                    <option value="Petang (2:00 PM - 4:30 PM)">Petang (2:00 PM - 4:30 PM)</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block font-bold text-slate-700 uppercase mb-1">Pilih Klinik / Pusat Veterinar Jajahan</label>
            <select name="klinik_jajahan" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
                @foreach($klinikList as $k)
                    <option value="{{ $k }}">{{ $k }}</option>
                @endforeach
            </select>
        </div>

        <div class="pt-4 flex items-center justify-end gap-3">
            <a href="{{ route('klinik.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 font-bold text-slate-700 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold shadow-lg shadow-rose-700/30 transition flex items-center gap-2">
                <i class="fa-solid fa-calendar-check"></i>
                <span>Sahkan Tempahan Temujanji</span>
            </button>
        </div>
    </form>
</div>
@endsection
