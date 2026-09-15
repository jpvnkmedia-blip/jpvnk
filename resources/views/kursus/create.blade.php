@extends('layouts.app')

@section('title', 'Terbitkan Kursus Baharu')
@section('page_title', 'Pusat Latihan Veterinar: Terbitkan Kursus Baharu')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs">
    
    <div class="border-b border-slate-100 pb-4 mb-6">
        <h3 class="text-base font-bold text-slate-900">Borang Pendaftaran Kursus & Bengkel Ternakan</h3>
        <p class="text-xs text-slate-500 mt-0.5">Sila masukkan butiran kursus, lokasi dan had kuota peserta.</p>
    </div>

    <form action="{{ route('kursus.store') }}" method="POST" class="space-y-4 text-xs">
        @csrf

        <div>
            <label class="block font-bold text-slate-700 uppercase mb-1">Tajuk Kursus / Bengkel</label>
            <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Kursus Inseminasi Buatan (AI) & Pembiakan Lembu Hibrid" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none font-medium">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Kategori Kursus</label>
                <select name="category" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none font-bold">
                    <option value="Ruminan">Ruminan (Lembu, Kambing, Kerbau)</option>
                    <option value="Unggas">Unggas & Reban Moden EPU</option>
                    <option value="Pemakanan Ternakan">Pemakanan Ternakan & Silaj</option>
                    <option value="Kesihatan Haiwan">Kesihatan & Rawatan Asas Haiwan</option>
                    <option value="Keusahawanan">Keusahawanan Agromakanan</option>
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Nama Penceramah / Tenaga Pengajar</label>
                <input type="text" name="trainer_name" value="{{ old('trainer_name', 'Pegawai Kanan Veterinar JPVNK') }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
            </div>
        </div>

        <div>
            <label class="block font-bold text-slate-700 uppercase mb-1">Sinopsis / Penerangan Kursus</label>
            <textarea name="description" rows="3" required placeholder="Penerangan modul pembelajaran dan objektif latihan" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">{{ old('description') }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Mula</label>
                <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Tamat</label>
                <input type="date" name="end_date" value="{{ old('end_date', date('Y-m-d')) }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Masa / Sesi</label>
                <input type="text" name="time" value="{{ old('time', '8:30 Pagi - 4:30 Petang') }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <label class="block font-bold text-slate-700 uppercase mb-1">Lokasi / Tempat Kursus</label>
                <input type="text" name="location" value="{{ old('location', 'Pusat Latihan Veterinar Kelantan, Bachok') }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Jajahan</label>
                <select name="jajahan" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    @foreach(['Bachok', 'Kota Bharu', 'Pasir Mas', 'Tumpat', 'Pasir Puteh', 'Machang', 'Tanah Merah', 'Jeli', 'Kuala Krai', 'Gua Musang'] as $j)
                        <option value="{{ $j }}">{{ $j }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Had Kapasiti Peserta (Orang)</label>
                <input type="number" name="capacity" value="{{ old('capacity', 40) }}" min="5" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none font-bold">
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Yuran Kursus (RM) - 0 jika percuma</label>
                <input type="number" name="fee" value="{{ old('fee', 0) }}" min="0" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
            </div>
        </div>

        <div class="pt-4 flex items-center justify-end gap-3">
            <a href="{{ route('kursus.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 font-bold text-slate-700 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-bold shadow-lg shadow-cyan-700/30 transition flex items-center gap-2">
                <i class="fa-solid fa-check"></i>
                <span>Terbitkan Kursus</span>
            </button>
        </div>
    </form>
</div>
@endsection
