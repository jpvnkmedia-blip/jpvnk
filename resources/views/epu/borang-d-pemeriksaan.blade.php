@extends('layouts.app')

@section('title', 'EPU Borang D - Laporan Pemeriksaan Tapak')
@section('page_title', 'EPU Borang D: Laporan Pemeriksaan Tapak & Penguatkuasaan')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs">
    
    <div class="border-b border-slate-100 pb-4 mb-6">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-100 text-blue-900 text-xs font-bold uppercase mb-2">
            <i class="fa-solid fa-clipboard-check"></i> EPU Borang D
        </div>
        <h3 class="text-base font-bold text-slate-900">Laporan Pemeriksaan Tapak & Penguatkuasaan Ladang Unggas</h3>
        <p class="text-xs text-slate-500 mt-0.5">Ladang: <b>{{ $ladang->nama_ladang }}</b> &bull; Pemilik: {{ $ladang->nama_pemohon_atau_syarikat }}</p>
    </div>

    <form action="{{ route('epu.borang-d.store', $ladang->id) }}" method="POST" class="space-y-4 text-xs">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Lawatan Pemeriksaan</label>
                <input type="date" name="tarikh_pemeriksaan" value="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Skor Kebersihan & Biosekuriti (%)</label>
                <input type="number" name="skor_kebersihan_peratus" min="0" max="100" value="85" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none font-bold">
            </div>
        </div>

        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
            <span class="text-[11px] font-bold text-slate-700 uppercase block">Senarai Semak Pematuhan Enakmen EPU:</span>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="patuh_zon_penampan" value="0">
                    <input type="checkbox" name="patuh_zon_penampan" value="1" checked class="w-4 h-4 rounded text-blue-600">
                    <span>Pematuhan Zon Penampan (Buffer Zone)</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="kawalan_lalat_memuaskan" value="0">
                    <input type="checkbox" name="kawalan_lalat_memuaskan" value="1" checked class="w-4 h-4 rounded text-blue-600">
                    <span>Kawalan Indeks Lalat Memuaskan</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="kawalan_bau_memuaskan" value="0">
                    <input type="checkbox" name="kawalan_bau_memuaskan" value="1" checked class="w-4 h-4 rounded text-blue-600">
                    <span>Kawalan Bau & Pengudaraan Reban Baik</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="sistem_longkang_sempurna" value="0">
                    <input type="checkbox" name="sistem_longkang_sempurna" value="1" checked class="w-4 h-4 rounded text-blue-600">
                    <span>Sistem Longkang & Rawatan Sisa Berfungsi</span>
                </label>
            </div>
        </div>

        <div>
            <label class="block font-bold text-slate-700 uppercase mb-1">Penemuan Pemeriksaan Lapangan</label>
            <textarea name="penemuan_pemeriksaan" rows="3" required placeholder="Catatan terperinci keadaan reban, kipas, pengurusan tinja dan biosekuriti pintu masuk" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">Pemeriksaan fizikal mendapati keadaan reban tertutup berada dalam keadaan bersih dan terurus. Tiada pembiakan lalat dikesan pada bangsal tinja.</textarea>
        </div>

        <div>
            <label class="block font-bold text-slate-700 uppercase mb-1">Syor & Arahan Pegawai Pemeriksa</label>
            <textarea name="syor_dan_arahan" rows="2" required placeholder="Syor penambahbaikan atau arahan pematuhan" class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">Teruskan program semburan EM berjadual dan pastikan pagar biosekuriti sentiasa berkunci.</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">Keputusan Pemeriksaan</label>
                <select name="status_keputusan" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none font-bold">
                    <option value="Lulus">Lulus (Pematuhan Penuh)</option>
                    <option value="Lulus Bersyarat">Lulus Bersyarat</option>
                    <option value="Notis Dikeluarkan">Notis Pematuhan Dikeluarkan</option>
                    <option value="Gagal">Gagal / Gantung Lesen</option>
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 uppercase mb-1">No. Notis Pematuhan (Jika Berkaitan)</label>
                <input type="text" name="no_notis_pematuhan" placeholder="Contoh: NOTIS/EPU/2026/012" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl">
            </div>
        </div>

        <div class="pt-4 flex items-center justify-end gap-3">
            <a href="{{ route('epu.show', $ladang->id) }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 font-bold text-slate-700 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-lg shadow-blue-700/30 transition flex items-center gap-2">
                <i class="fa-solid fa-clipboard-check"></i>
                <span>Simpan Laporan Pemeriksaan (Borang D)</span>
            </button>
        </div>
    </form>
</div>
@endsection
