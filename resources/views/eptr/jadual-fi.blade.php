@extends('layouts.app')

@section('title', 'Jadual Fi Bayaran EPTR - Enakmen Pendaftaran Ternakan Ruminan')
@section('page_title', 'Jadual Fi Bayaran Enakmen Pendaftaran Ternakan Ruminan')

@section('content')
<div class="space-y-6" x-data="{
    calcCategory: 'seksyen_5_6',
    calcJenis: 'besar',
    calcKuantiti: 1,
    calcKonsainan: 1,
    
    // Senarai kadar
    rates: {
        seksyen_5: { nama: 'Pendaftaran Pertama (Seksyen 5)', besar: 2.00, kecil: 2.00, unit: 'ekor' },
        seksyen_6: { nama: 'Penandaan Ternakan / Tagging (Seksyen 6)', besar: 8.00, kecil: 5.00, unit: 'ekor' },
        seksyen_5_6: { nama: 'Pendaftaran Pertama + Penandaan (Seksyen 5 & 6)', besar: 10.00, kecil: 7.00, unit: 'ekor' },
        seksyen_7: { nama: 'Pendaftaran Lewat Ternakan (Seksyen 7)', besar: 12.00, kecil: 10.00, unit: 'ekor' },
        seksyen_8: { nama: 'Pindah Milik Ternakan (Seksyen 8 - Borang B)', besar: 2.00, kecil: 2.00, unit: 'ekor' },
        seksyen_10_daftar: { nama: 'Pendaftaran Pindah Masuk Dalam Negeri / Antara Jajahan (Seksyen 10)', besar: 2.00, kecil: 2.00, unit: 'ekor' },
        seksyen_10_konsainan: { nama: 'Bayaran Pindah Masuk Dalam Negeri / Antara Jajahan (Seksyen 10 - Konsainan)', besar: 10.00, kecil: 8.00, unit: 'konsainan' },
        seksyen_10_lengkap: { nama: 'Pindah Masuk Antara Jajahan Lengkap (Daftar + Konsainan)', besar: 12.00, kecil: 10.00, unit: 'pakej' },
        perenggan_11_1_b: { nama: 'Pembatalan Akibat Sembelihan (Perenggan 11(1)(b) - Borang D)', besar: 10.00, kecil: 5.00, unit: 'ekor' },
        skv_sembelih_lengkap: { nama: 'Permit & SKV Sembelih Lengkap (SKV RM10 + Pembatalan)', besar: 20.00, kecil: 15.00, unit: 'pakej' },
        perenggan_11_1_c: { nama: 'Pembatalan Akibat Pindah Keluar Negeri (Perenggan 11(1)(c))', besar: 20.00, kecil: 10.00, unit: 'ekor' },
        subseksyen_44_3: { nama: 'Salinan Pendua Borang B (Subseksyen 44(3))', besar: 10.00, kecil: 10.00, unit: 'salinan' }
    },
    
    get selectedRate() {
        return this.rates[this.calcCategory] || this.rates.seksyen_5_6;
    },
    
    get ratePerUnit() {
        let r = this.selectedRate;
        return this.calcJenis === 'besar' ? r.besar : r.kecil;
    },
    
    get totalFi() {
        if (this.calcCategory === 'seksyen_10_lengkap') {
            const daftarFee = (this.calcKuantiti || 0) * 2.00;
            const konsainanRate = this.calcJenis === 'besar' ? 10.00 : 8.00;
            const konsainanFee = (this.calcKonsainan || 1) * konsainanRate;
            return daftarFee + konsainanFee;
        } else if (this.calcCategory === 'skv_sembelih_lengkap') {
            const pembatalanRate = this.calcJenis === 'besar' ? 10.00 : 5.00;
            return 10.00 + ((this.calcKuantiti || 1) * pembatalanRate);
        } else {
            return (this.calcKuantiti || 0) * this.ratePerUnit;
        }
    }
}">

    <!-- Top Banner Header -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-950 via-slate-900 to-emerald-950 p-6 sm:p-8 text-white shadow-xl border border-slate-800">
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs font-bold uppercase tracking-wider">
                    <i class="fa-solid fa-scale-balanced"></i>
                    <span>Jadual Statutori Rasmi &bull; Enakmen EPTR</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                    Jadual Fi Bayaran Pendaftaran Ternakan Ruminan
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                    Kadar bayaran fi berkanun mengikut <strong>Enakmen Pendaftaran Ternakan Ruminan Negeri Kelantan</strong> bagi Ruminan Besar (Lembu, Kerbau) dan Ruminan Kecil (Kambing, Bebiri).
                </p>
            </div>

            <!-- Quick Stats / Badges -->
            <div class="flex flex-wrap gap-2 sm:gap-3 bg-white/5 backdrop-blur-md p-4 rounded-2xl border border-white/10">
                <div class="text-center px-3 py-1 border-r border-white/10">
                    <span class="block text-[10px] text-slate-400 uppercase font-semibold">Ruminan Besar</span>
                    <span class="text-sm font-bold text-amber-300">Lembu & Kerbau</span>
                </div>
                <div class="text-center px-3 py-1">
                    <span class="block text-[10px] text-slate-400 uppercase font-semibold">Ruminan Kecil</span>
                    <span class="text-sm font-bold text-emerald-300">Kambing & Bebiri</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Banner Makluman Khas: Tempoh Pemutihan EPTR -->
    <div class="p-5 rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 text-white shadow-lg flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center text-2xl shrink-0">
                <i class="fa-solid fa-gift"></i>
            </div>
            <div>
                <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-white/20 text-[10px] font-black uppercase tracking-wider mb-1">
                    <span>Program Khas Pemutihan EPTR 2026</span>
                </div>
                <h3 class="font-extrabold text-sm sm:text-base">Pengecualian Denda Lewat Pendaftaran 100% (RM 0.00)</h3>
                <p class="text-xs text-emerald-100 mt-0.5">
                    Bermula <strong>20 September sehingga 31 Disember 2026</strong>, semua pendaftaran ternakan yang lewat melebihi 14 hari tidak dikenakan caj denda Seksyen 7. Penternak hanya membayar fi pendaftaran dan penandaan biasa sahaja.
                </p>
            </div>
        </div>
        <div class="shrink-0">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white text-emerald-900 font-extrabold text-xs shadow-md">
                <i class="fa-solid fa-clock"></i>
                <span>20 Sept &ndash; 31 Dis 2026</span>
            </span>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Jadual Fi Rasmi (8 Cols) -->
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xs">
                
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="font-bold text-slate-900 text-base flex items-center gap-2">
                            <i class="fa-solid fa-receipt text-emerald-600"></i>
                            <span>Senarai Fi Mengikut Peruntukan Enakmen</span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Rujukan kadar bayaran fi statutori bagi setiap kategori perkhidmatan EPTR</p>
                    </div>

                    <button onclick="window.print()" class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5 no-print">
                        <i class="fa-solid fa-print"></i>
                        <span>Cetak Jadual</span>
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-700 font-bold uppercase text-[11px] border-b border-slate-200">
                            <tr>
                                <th class="py-3.5 px-4 w-36">Peruntukan Enakmen</th>
                                <th class="py-3.5 px-4 min-w-[220px]">Keterangan Transaksi</th>
                                <th class="py-3.5 px-4 text-center bg-amber-50/50 text-amber-900 border-x border-slate-200">
                                    <div>Ruminan Besar</div>
                                    <div class="text-[10px] font-normal text-slate-500">(Lembu, Kerbau)</div>
                                </th>
                                <th class="py-3.5 px-4 text-center bg-emerald-50/50 text-emerald-900">
                                    <div>Ruminan Kecil</div>
                                    <div class="text-[10px] font-normal text-slate-500">(Kambing, Bebiri)</div>
                                </th>
                                <th class="py-3.5 px-4 text-center">Borang / Modul</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @foreach($fiList as $item)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <!-- Peruntukan Enakmen -->
                                    <td class="py-3.5 px-4 font-bold text-slate-900">
                                        <span class="inline-block px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 font-mono text-[11px] border border-slate-200">
                                            {{ $item['peruntukan'] }}
                                        </span>
                                    </td>

                                    <!-- Keterangan -->
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-800 text-sm">{{ $item['keterangan'] }}</div>
                                    </td>

                                    <!-- Ruminan Besar -->
                                    <td class="py-3.5 px-4 text-center bg-amber-50/30 border-x border-slate-100">
                                        <span class="font-mono font-extrabold text-amber-800 text-sm">
                                            RM {{ number_format($item['kadar_ruminan_besar'], 2) }}
                                        </span>
                                        <span class="text-[10px] text-slate-500 block">{{ $item['unit_ruminan_besar'] }}</span>
                                    </td>

                                    <!-- Ruminan Kecil -->
                                    <td class="py-3.5 px-4 text-center bg-emerald-50/30">
                                        <span class="font-mono font-extrabold text-emerald-800 text-sm">
                                            RM {{ number_format($item['kadar_ruminan_kecil'], 2) }}
                                        </span>
                                        <span class="text-[10px] text-slate-500 block">{{ $item['unit_ruminan_kecil'] }}</span>
                                    </td>

                                    <!-- Borang / Modul -->
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $item['borang'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Nota Panduan Statutori -->
                <div class="p-5 bg-slate-50 border-t border-slate-200 space-y-2 text-xs text-slate-600">
                    <div class="font-bold text-slate-800 flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-info text-blue-600"></i>
                        <span>Nota Pelaksanaan Fi EPTR:</span>
                    </div>
                    <ul class="list-disc pl-5 space-y-1 text-[11px] text-slate-600 leading-relaxed">
                        <li><strong>Pendaftaran Kali Pertama:</strong> Fi Seksyen 5 (RM2.00) dikenakan bersama fi Penandaan Seksyen 6 (RM8.00 untuk ruminan besar, RM5.00 untuk ruminan kecil) bagi pengeluaran Kad Kuning & pemasangan tag telinga rasmi.</li>
                        <li><strong>Pendaftaran Lewat:</strong> Pendaftaran melebihi tempoh statutori yang ditetapkan tertakluk kepada fi Seksyen 7 (RM12.00 bagi ruminan besar, RM10.00 bagi ruminan kecil).</li>
                        <li><strong>Permit Sembelihan & SKV Sembelih:</strong> Pembatalan pendaftaran akibat sembelihan tertakluk kepada fi Perenggan 11(1)(b) (RM10.00 bagi lembu/kerbau, RM5.00 bagi kambing/bebiri).</li>
                        <li><strong>Pemindahan Masuk Konsainan (Antara Jajahan Dalam Negeri):</strong> Pemindahan ternakan dalam negeri (iaitu pergerakan antara jajahan) yang dipindah masuk tertakluk kepada fi pendaftaran RM2.00/ekor serta fi konsainan RM10.00 (besar) / RM8.00 (kecil) mengikut Seksyen 10.</li>
                    </ul>
                </div>

            </div>
        </div>

        <!-- Interactive Fee Calculator (4 Cols) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Calculator Card -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs space-y-5">
                <div class="border-b border-slate-100 pb-4">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[10px] font-bold uppercase mb-2">
                        <i class="fa-solid fa-calculator"></i>
                        <span>Kalkulator Interaktif</span>
                    </div>
                    <h3 class="font-black text-slate-900 text-base">Kira Fi Bayaran Statutori</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Kira anggaran jumlah fi yang perlu dibayar secara pantas mengikut kategori transaksi.</p>
                </div>

                <div class="space-y-4 text-xs">
                    <!-- 1. Kategori Transaksi -->
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1.5">Jenis Transaksi / Perkhidmatan</label>
                        <select x-model="calcCategory" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none text-xs font-semibold">
                            <option value="seksyen_5_6">Pendaftaran Pertama + Tagging (Seksyen 5 &amp; 6)</option>
                            <option value="seksyen_5">Pendaftaran Sahaja (Seksyen 5 - RM2.00)</option>
                            <option value="seksyen_6">Penandaan / Tagging Sahaja (Seksyen 6)</option>
                            <option value="seksyen_7">Pendaftaran Lewat (Seksyen 7)</option>
                            <option value="seksyen_8">Pindah Milik Ternakan (Seksyen 8 - Borang B)</option>
                            <option value="seksyen_10_lengkap">Pindah Masuk Antara Jajahan - Lengkap (Daftar + Konsainan)</option>
                            <option value="seksyen_10_daftar">Pindah Masuk Antara Jajahan - Pendaftaran Sahaja (Seksyen 10)</option>
                            <option value="seksyen_10_konsainan">Pindah Masuk Antara Jajahan - Fi Konsainan Sahaja (Seksyen 10)</option>
                            <option value="perenggan_11_1_b">Pembatalan Sembelihan Sahaja (Perenggan 11(1)(b))</option>
                            <option value="skv_sembelih_lengkap">Permit &amp; SKV Sembelih Lengkap (SKV RM10 + Pembatalan)</option>
                            <option value="perenggan_11_1_c">Pembatalan Pindah Keluar Negeri (Perenggan 11(1)(c))</option>
                            <option value="subseksyen_44_3">Salinan Pendua Borang B (Subseksyen 44(3))</option>
                        </select>
                    </div>

                    <!-- 2. Jenis Ruminan -->
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1.5">Kategori Ruminan</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" @click="calcJenis = 'besar'" :class="calcJenis === 'besar' ? 'bg-amber-500 text-slate-950 font-black border-amber-600 shadow-xs' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border-slate-200 font-bold'" class="py-2.5 px-3 rounded-xl border text-xs transition flex flex-col items-center">
                                <span class="flex items-center gap-1.5"><i class="fa-solid fa-cow"></i> Ruminan Besar</span>
                                <span class="text-[10px] font-normal opacity-80">Lembu &amp; Kerbau</span>
                            </button>
                            <button type="button" @click="calcJenis = 'kecil'" :class="calcJenis === 'kecil' ? 'bg-emerald-600 text-white font-black border-emerald-700 shadow-xs' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border-slate-200 font-bold'" class="py-2.5 px-3 rounded-xl border text-xs transition flex flex-col items-center">
                                <span class="flex items-center gap-1.5"><i class="fa-solid fa-paw"></i> Ruminan Kecil</span>
                                <span class="text-[10px] font-normal opacity-80">Kambing &amp; Bebiri</span>
                            </button>
                        </div>
                    </div>

                    <!-- 3. Bilangan Ekor / Kuantiti -->
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1.5">
                            <span x-text="selectedRate.unit === 'konsainan' ? 'Bilangan Konsainan' : (selectedRate.unit === 'salinan' ? 'Bilangan Salinan' : 'Bilangan Ekor Ternakan')"></span>
                        </label>
                        <div class="relative">
                            <input type="number" min="1" max="1000" x-model.number="calcKuantiti" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none font-bold text-slate-900 font-mono">
                            <span class="absolute right-3.5 top-2.5 text-xs text-slate-400 font-semibold" x-text="selectedRate.unit === 'konsainan' ? 'Konsainan' : (selectedRate.unit === 'salinan' ? 'Salinan' : 'Ekor')"></span>
                        </div>
                    </div>

                    <!-- Extra: Bilangan Konsainan jika Pindah Masuk Lengkap -->
                    <div x-show="calcCategory === 'seksyen_10_lengkap'" x-cloak>
                        <label class="block font-bold text-slate-700 uppercase mb-1.5">Bilangan Konsainan</label>
                        <input type="number" min="1" max="100" x-model.number="calcKonsainan" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none font-bold text-slate-900 font-mono">
                    </div>

                    <!-- Calculation Output Box -->
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-slate-900 to-emerald-950 text-white shadow-md space-y-3 mt-4">
                        <div class="flex items-center justify-between text-xs text-slate-300 border-b border-white/10 pb-2">
                            <span>Kadar Seunit:</span>
                            <span class="font-mono font-bold text-emerald-300">
                                RM <span x-text="ratePerUnit.toFixed(2)"></span> / <span x-text="selectedRate.unit"></span>
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-300">Jumlah Fi Perlu Dibayar:</span>
                            <div class="text-right">
                                <span class="text-2xl font-black text-amber-300 font-mono">
                                    RM <span x-text="totalFi.toFixed(2)"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Quick Action Links -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs space-y-3">
                <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Tindakan Pantas EPTR</h4>
                
                <div class="space-y-2 text-xs font-bold">
                    <a href="{{ route('eptr.create') }}" class="w-full p-3 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 transition flex items-center justify-between group">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-plus-circle text-emerald-600"></i>
                            <span>Daftar Ternakan (Borang A)</span>
                        </div>
                        <i class="fa-solid fa-arrow-right text-emerald-600 group-hover:translate-x-1 transition-transform"></i>
                    </a>

                    <a href="{{ route('eptr.borang-b.create') }}" class="w-full p-3 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-800 transition flex items-center justify-between group">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-arrow-right-arrow-left text-blue-600"></i>
                            <span>Pindah Milik (Borang B)</span>
                        </div>
                        <i class="fa-solid fa-arrow-right text-blue-600 group-hover:translate-x-1 transition-transform"></i>
                    </a>

                    <a href="{{ route('eptr.borang-c.create') }}" class="w-full p-3 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-800 transition flex items-center justify-between group">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-ban text-rose-600"></i>
                            <span>Pembatalan / Kematian (Borang C)</span>
                        </div>
                        <i class="fa-solid fa-arrow-right text-rose-600 group-hover:translate-x-1 transition-transform"></i>
                    </a>

                    <a href="{{ route('eptr.borang-d.create') }}" class="w-full p-3 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 transition flex items-center justify-between group">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-file-invoice text-amber-600"></i>
                            <span>Permit Sembelihan (Borang D)</span>
                        </div>
                        <i class="fa-solid fa-arrow-right text-amber-600 group-hover:translate-x-1 transition-transform"></i>
                    </a>

                    <a href="{{ route('eptr.pemindahan.create') }}" class="w-full p-3 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-800 transition flex items-center justify-between group">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-truck-moving text-purple-600"></i>
                            <span>Permit Pemindahan Ternakan</span>
                        </div>
                        <i class="fa-solid fa-arrow-right text-purple-600 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
