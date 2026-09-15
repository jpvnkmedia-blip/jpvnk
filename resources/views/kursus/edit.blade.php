@extends('layouts.app')

@section('title', 'Kemaskini Kursus - ' . $course->title)
@section('page_title', 'Kemaskini Maklumat Kursus Ternakan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-cyan-100 text-cyan-800">
                Penyelenggaraan Kursus &bull; {{ $course->code }}
            </span>
            <h2 class="text-xl font-extrabold text-slate-900 mt-1">Kemaskini Kursus: {{ $course->title }}</h2>
            <p class="text-xs text-slate-500 mt-0.5">Ubah jadual waktu, lokasi latihan, penceramah, had kapasiti peserta atau status kursus</p>
        </div>
        <a href="{{ route('kursus.show', $course->id) }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 bg-white px-3.5 py-2 rounded-xl border border-slate-200 transition">
            &larr; Kembali
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs">
        <form action="{{ route('kursus.update', $course->id) }}" method="POST" class="space-y-6 text-xs">
            @csrf
            @method('PUT')

            <!-- Section 1: Maklumat Utama Kursus -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-slate-900 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-graduation-cap text-cyan-600"></i>
                    <span>1. Maklumat Asas &amp; Kategori Kursus</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Tajuk Kursus / Bengkel <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $course->title) }}" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Kategori Kursus <span class="text-rose-500">*</span></label>
                        <select name="category" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                            @foreach(['Ruminan', 'Unggas', 'Pemakanan Ternakan', 'Kesihatan Haiwan', 'Keusahawanan Agromakanan', 'Biosekuriti Ladang'] as $cat)
                                <option value="{{ $cat }}" {{ old('category', $course->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Nama Penceramah / Pegawai Penyelaras <span class="text-rose-500">*</span></label>
                        <input type="text" name="trainer_name" value="{{ old('trainer_name', $course->trainer_name) }}" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Sinopsis &amp; Sukatan Latihan <span class="text-rose-500">*</span></label>
                        <textarea name="description" rows="4" required class="w-full rounded-xl border border-slate-200 p-3.5 focus:ring-2 focus:ring-cyan-500 focus:outline-none leading-relaxed">{{ old('description', $course->description) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Section 2: Tarikh, Masa & Lokasi -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <h3 class="text-sm font-bold text-slate-900 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-calendar-days text-cyan-600"></i>
                    <span>2. Tarikh, Waktu &amp; Lokasi Latihan</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Tarikh Mula <span class="text-rose-500">*</span></label>
                        <input type="date" name="start_date" value="{{ old('start_date', $course->start_date ? $course->start_date->format('Y-m-d') : '') }}" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Tarikh Tamat <span class="text-rose-500">*</span></label>
                        <input type="date" name="end_date" value="{{ old('end_date', $course->end_date ? $course->end_date->format('Y-m-d') : '') }}" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Masa / Waktu <span class="text-rose-500">*</span></label>
                        <input type="text" name="time" value="{{ old('time', $course->time) }}" required placeholder="Contoh: 8:30 Pagi - 4:30 Petang" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Nama Tempat / Dewan Latihan <span class="text-rose-500">*</span></label>
                        <input type="text" name="location" value="{{ old('location', $course->location) }}" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Jajahan <span class="text-rose-500">*</span></label>
                        <select name="jajahan" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                            @foreach(['Kota Bharu', 'Pasir Mas', 'Tumpat', 'Bachok', 'Pasir Puteh', 'Machang', 'Tanah Merah', 'Jeli', 'Kuala Krai', 'Gua Musang'] as $j)
                                <option value="{{ $j }}" {{ old('jajahan', $course->jajahan) === $j ? 'selected' : '' }}>{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 3: Kapasiti, Yuran & Status Kursus -->
            <div class="space-y-4 pt-4 border-t border-slate-100">
                <h3 class="text-sm font-bold text-slate-900 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-cyan-600"></i>
                    <span>3. Had Kapasiti, Yuran &amp; Status Semasa</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Had Kapasiti Peserta <span class="text-rose-500">*</span></label>
                        <input type="number" name="capacity" min="5" value="{{ old('capacity', $course->capacity) }}" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                        <span class="text-[10px] text-slate-400 mt-0.5 block">Jumlah Berdaftar Semasa: <b>{{ $course->registered_count }}</b></span>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Yuran Pendaftaran (RM)</label>
                        <input type="number" step="0.01" name="fee" value="{{ old('fee', $course->fee) }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                        <span class="text-[10px] text-slate-400 mt-0.5 block">Masukkan 0.00 jika percuma/tajaan</span>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Status Kursus <span class="text-rose-500">*</span></label>
                        <select name="status" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-cyan-500 focus:outline-none font-semibold">
                            <option value="Buka" {{ old('status', $course->status) === 'Buka' ? 'selected' : '' }}>🟢 Buka (Pendaftaran Dibuka)</option>
                            <option value="Tutup" {{ old('status', $course->status) === 'Tutup' ? 'selected' : '' }}>🟡 Tutup (Pendaftaran Ditutup)</option>
                            <option value="Sedang Berlangsung" {{ old('status', $course->status) === 'Sedang Berlangsung' ? 'selected' : '' }}>🔵 Sedang Berlangsung</option>
                            <option value="Selesai" {{ old('status', $course->status) === 'Selesai' ? 'selected' : '' }}>🟣 Selesai (Kursus Telah Tamat)</option>
                            <option value="Diarkibkan" {{ old('status', $course->status) === 'Diarkibkan' ? 'selected' : '' }}>📦 Diarkibkan (Arkib Rekod Selesai &amp; Pelihara Sijil)</option>
                            <option value="Batal" {{ old('status', $course->status) === 'Batal' ? 'selected' : '' }}>🔴 Batal (Kursus Dibatalkan)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('kursus.show', $course->id) }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                    Batal
                </a>

                <button type="submit" class="px-6 py-2.5 rounded-xl bg-cyan-700 hover:bg-cyan-800 text-white font-bold shadow-lg shadow-cyan-700/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-save"></i>
                    <span>Simpan Perubahan Kursus</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
