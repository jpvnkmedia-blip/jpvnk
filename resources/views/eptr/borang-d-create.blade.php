@extends('layouts.app')

@section('title', 'Permohonan Permit Sembelihan & SKV Sembelih - EPTR Borang D')
@section('page_title', 'EPTR Borang D: Permohonan Permit & SKV Sembelihan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    tujuanSembelih: '{{ old('tujuan_sembelih', 'Jualan') }}',
    hariKorbanPercuma: '{{ old('hari_korban_percuma', '') }}',
    aidiladhaMap: {
        '2024-06-17': { name: 'Hari Raya Pertama', hijri: '10 Zulhijjah', eligiblePercuma: true },
        '2024-06-18': { name: 'Hari Raya Kedua', hijri: '11 Zulhijjah', eligiblePercuma: true },
        '2024-06-19': { name: 'Hari Raya Ketiga', hijri: '12 Zulhijjah', eligiblePercuma: false },
        '2024-06-20': { name: 'Hari Raya Keempat', hijri: '13 Zulhijjah', eligiblePercuma: false },
        '2025-06-06': { name: 'Hari Raya Pertama', hijri: '10 Zulhijjah', eligiblePercuma: true },
        '2025-06-07': { name: 'Hari Raya Kedua', hijri: '11 Zulhijjah', eligiblePercuma: true },
        '2025-06-08': { name: 'Hari Raya Ketiga', hijri: '12 Zulhijjah', eligiblePercuma: false },
        '2025-06-09': { name: 'Hari Raya Keempat', hijri: '13 Zulhijjah', eligiblePercuma: false },
        '2026-05-27': { name: 'Hari Raya Pertama', hijri: '10 Zulhijjah', eligiblePercuma: true },
        '2026-05-28': { name: 'Hari Raya Kedua', hijri: '11 Zulhijjah', eligiblePercuma: true },
        '2026-05-29': { name: 'Hari Raya Ketiga', hijri: '12 Zulhijjah', eligiblePercuma: false },
        '2026-05-30': { name: 'Hari Raya Keempat', hijri: '13 Zulhijjah', eligiblePercuma: false },
        '2027-05-16': { name: 'Hari Raya Pertama', hijri: '10 Zulhijjah', eligiblePercuma: true },
        '2027-05-17': { name: 'Hari Raya Kedua', hijri: '11 Zulhijjah', eligiblePercuma: true },
        '2027-05-18': { name: 'Hari Raya Ketiga', hijri: '12 Zulhijjah', eligiblePercuma: false },
        '2027-05-19': { name: 'Hari Raya Keempat', hijri: '13 Zulhijjah', eligiblePercuma: false },
        '2028-05-05': { name: 'Hari Raya Pertama', hijri: '10 Zulhijjah', eligiblePercuma: true },
        '2028-05-06': { name: 'Hari Raya Kedua', hijri: '11 Zulhijjah', eligiblePercuma: true },
        '2028-05-07': { name: 'Hari Raya Ketiga', hijri: '12 Zulhijjah', eligiblePercuma: false },
        '2028-05-08': { name: 'Hari Raya Keempat', hijri: '13 Zulhijjah', eligiblePercuma: false }
    },
    get primaryTarikh() {
        let firstWithDate = this.items.find(i => (i.tarikh_sembelihan || '').trim() !== '');
        return firstWithDate ? firstWithDate.tarikh_sembelihan : '{{ date('Y-m-d') }}';
    },
    get detectedHariInfo() {
        // Semak tarikh sembelihan daripada jadual ternakan
        for (let it of this.items) {
            if (it.tarikh_sembelihan && this.aidiladhaMap[it.tarikh_sembelihan]) {
                return this.aidiladhaMap[it.tarikh_sembelihan];
            }
        }
        return null;
    },
    get isMusimKorban() {
        if (this.detectedHariInfo) return true;
        if ((this.tujuanSembelih || '').toLowerCase() === 'ibadah korban') return true;
        return false;
    },
    get isEligiblePercuma() {
        // Layak percuma jika ada tarikh hari pertama/kedua atau tujuan korban
        for (let it of this.items) {
            if (it.tarikh_sembelihan && this.aidiladhaMap[it.tarikh_sembelihan]?.eligiblePercuma) {
                return true;
            }
        }
        if ((this.tujuanSembelih || '').toLowerCase() === 'ibadah korban') {
            // Jika ada tarikh tetapi semua hari 3/4, tidak layak
            let hasOnlyDay3Or4 = this.items.some(i => i.tarikh_sembelihan && this.aidiladhaMap[i.tarikh_sembelihan] && !this.aidiladhaMap[i.tarikh_sembelihan].eligiblePercuma);
            let hasDay1Or2 = this.items.some(i => i.tarikh_sembelihan && this.aidiladhaMap[i.tarikh_sembelihan]?.eligiblePercuma);
            if (hasOnlyDay3Or4 && !hasDay1Or2) return false;
            return true;
        }
        return false;
    },
    get detectedHariKorban() {
        if (this.detectedHariInfo) return this.detectedHariInfo.name;
        return this.isMusimKorban ? 'Hari Raya Pertama' : null;
    },
    get maxRows() {
        return this.isMusimKorban ? 10 : 7;
    },
    onRowDateChange(index) {
        let it = this.items[index];
        if (it && it.tarikh_sembelihan) {
            let info = this.aidiladhaMap[it.tarikh_sembelihan];
            if (info) {
                it.hari_sembelihan_korban = info.name;
            }
        }
        this.updateHariKorbanPercumaState();
    },
    updateHariKorbanPercumaState() {
        if (this.isMusimKorban && this.isEligiblePercuma) {
            if (!this.hariKorbanPercuma) {
                let firstEligible = this.items.find(i => i.tarikh_sembelihan && this.aidiladhaMap[i.tarikh_sembelihan]?.eligiblePercuma);
                if (firstEligible) {
                    this.hariKorbanPercuma = this.aidiladhaMap[firstEligible.tarikh_sembelihan].name;
                } else {
                    this.hariKorbanPercuma = 'Hari Raya Pertama';
                }
            }
        } else if (!this.isEligiblePercuma) {
            this.hariKorbanPercuma = '';
        }
    },
    receiptFileName: '',
    receiptFileSize: '',
    onFileSelected(event) {
        const file = event.target.files[0];
        if (file) {
            this.receiptFileName = file.name;
            const sizeInKb = (file.size / 1024).toFixed(1);
            this.receiptFileSize = sizeInKb > 1024 ? (sizeInKb / 1024).toFixed(2) + ' MB' : sizeInKb + ' KB';
        } else {
            this.receiptFileName = '';
            this.receiptFileSize = '';
        }
    },
    init() {
        this.updateHariKorbanPercumaState();
    },
    items: [
        @if(old('items'))
            @foreach(old('items') as $idx => $item)
                {
                    ternakan_id: '{{ $item['ternakan_id'] ?? '' }}',
                    jantina: '{{ $item['jantina'] ?? 'J' }}',
                    no_id_ternakan: '{{ $item['no_id_ternakan'] ?? '' }}',
                    no_siri_kad_pendaftaran: '{{ $item['no_siri_kad_pendaftaran'] ?? '' }}',
                    tarikh_sembelihan: '{{ $item['tarikh_sembelihan'] ?? date('Y-m-d') }}',
                    hari_sembelihan_korban: '{{ $item['hari_sembelihan_korban'] ?? old('hari_korban_percuma', 'Hari Raya Pertama') }}',
                    tempat_sembelihan: '{{ $item['tempat_sembelihan'] ?? '' }}',
                    no_kn_haiwan_16: '{{ $item['no_kn_haiwan_16'] ?? '' }}',
                    kuantiti_karkas: '{{ $item['kuantiti_karkas'] ?? '1 Ekor' }}'
                },
            @endforeach
        @elseif(isset($selectedTernakan))
            {
                ternakan_id: '{{ $selectedTernakan->id }}',
                jantina: '{{ $selectedTernakan->jantina === 'Jantan' ? 'J' : 'B' }}',
                no_id_ternakan: '{{ $selectedTernakan->no_tag }}',
                no_siri_kad_pendaftaran: '{{ $selectedTernakan->no_siri_kad_kuning ?? '-' }}',
                tarikh_sembelihan: '{{ date('Y-m-d') }}',
                hari_sembelihan_korban: 'Hari Raya Pertama',
                tempat_sembelihan: 'Rumah Sembelihan {{ $selectedTernakan->jajahan }}',
                no_kn_haiwan_16: '',
                kuantiti_karkas: '1 Ekor'
            }
        @else
            {
                ternakan_id: '',
                jantina: 'J',
                no_id_ternakan: '',
                no_siri_kad_pendaftaran: '',
                tarikh_sembelihan: '{{ date('Y-m-d') }}',
                hari_sembelihan_korban: 'Hari Raya Pertama',
                tempat_sembelihan: '',
                no_kn_haiwan_16: '',
                kuantiti_karkas: '1 Ekor'
            }
        @endif
    ],
    availableTernakan: {{ json_encode($ternakanList->map(fn($t) => [
        'id' => $t->id,
        'pemunya_id' => $t->pemunya_id,
        'no_tag' => $t->no_tag,
        'jenis' => $t->jenis_ternakan,
        'baka' => $t->baka,
        'jantina' => $t->jantina === 'Jantan' ? 'J' : 'B',
        'no_siri_kad_kuning' => $t->no_siri_kad_kuning ?? '-',
        'lokasi_kandang' => $t->lokasi_kandang ?? $t->jajahan,
        'label' => $t->no_tag . ' (' . $t->baka . ' - ' . $t->jantina . ')'
    ])) }},
    selectedPemunyaId: '{{ old('pemunya_id', (isset($selectedTernakan) ? $selectedTernakan->pemunya_id : ($user->pemunya ? $user->pemunya->id : ''))) }}',
    selectedJenisTernakan: '{{ old('jenis_ternakan', (isset($selectedTernakan) ? $selectedTernakan->jenis_ternakan : 'Lembu')) }}',
    fiSkvTetap: 10.00,
    get countBesar() {
        return this.items.filter(i => {
            if (!(i.no_id_ternakan || '').trim()) return false;
            let found = this.availableTernakan.find(t => t.id == i.ternakan_id);
            if (found && found.jenis) {
                let j = found.jenis.toLowerCase();
                return j.includes('lembu') || j.includes('kerbau');
            }
            let sel = (this.selectedJenisTernakan || '').toLowerCase();
            return sel.includes('lembu') || sel.includes('kerbau') || (!sel.includes('kambing') && !sel.includes('biri'));
        }).length;
    },
    get countBesarPercuma() {
        if (!this.isMusimKorban) return 0;
        return this.items.filter(i => {
            if (!(i.no_id_ternakan || '').trim()) return false;
            if (i.hari_sembelihan_korban !== this.hariKorbanPercuma) return false;
            let found = this.availableTernakan.find(t => t.id == i.ternakan_id);
            if (found && found.jenis) {
                let j = found.jenis.toLowerCase();
                return j.includes('lembu') || j.includes('kerbau');
            }
            let sel = (this.selectedJenisTernakan || '').toLowerCase();
            return sel.includes('lembu') || sel.includes('kerbau') || (!sel.includes('kambing') && !sel.includes('biri'));
        }).length;
    },
    get countBesarBerbayar() {
        return Math.max(0, this.countBesar - this.countBesarPercuma);
    },
    get countKecil() {
        return this.items.filter(i => {
            if (!(i.no_id_ternakan || '').trim()) return false;
            let found = this.availableTernakan.find(t => t.id == i.ternakan_id);
            if (found && found.jenis) {
                let j = found.jenis.toLowerCase();
                return j.includes('kambing') || j.includes('biri');
            }
            let sel = (this.selectedJenisTernakan || '').toLowerCase();
            return sel.includes('kambing') || sel.includes('biri');
        }).length;
    },
    get countKecilPercuma() {
        if (!this.isMusimKorban) return 0;
        return this.items.filter(i => {
            if (!(i.no_id_ternakan || '').trim()) return false;
            if (i.hari_sembelihan_korban !== this.hariKorbanPercuma) return false;
            let found = this.availableTernakan.find(t => t.id == i.ternakan_id);
            if (found && found.jenis) {
                let j = found.jenis.toLowerCase();
                return j.includes('kambing') || j.includes('biri');
            }
            let sel = (this.selectedJenisTernakan || '').toLowerCase();
            return sel.includes('kambing') || sel.includes('biri');
        }).length;
    },
    get countKecilBerbayar() {
        return Math.max(0, this.countKecil - this.countKecilPercuma);
    },
    get totalEkor() {
        let count = this.items.filter(i => (i.no_id_ternakan || '').trim() !== '').length;
        return count > 0 ? count : (this.items.length || 1);
    },
    get fiPembatalanBesar() {
        return this.countBesarBerbayar * 10.00;
    },
    get fiPembatalanKecil() {
        return this.countKecilBerbayar * 5.00;
    },
    get totalFiPembatalan() {
        return this.fiPembatalanBesar + this.fiPembatalanKecil;
    },
    get totalFi() {
        return this.fiSkvTetap + this.totalFiPembatalan;
    },
    get filteredTernakan() {
        if (!this.selectedPemunyaId) {
            return this.availableTernakan;
        }
        return this.availableTernakan.filter(t => t.pemunya_id == this.selectedPemunyaId);
    },
    getAvailableTernakanForRow(currentIndex) {
        let selectedIds = this.items
            .map((item, idx) => idx !== currentIndex ? String(item.ternakan_id || '') : null)
            .filter(id => id && id.trim() !== '');

        return this.filteredTernakan.filter(t => !selectedIds.includes(String(t.id)));
    },
    addRow() {
        if (this.items.length < this.maxRows) {
            let lastItem = this.items.length > 0 ? this.items[this.items.length - 1] : null;
            let defaultDate = (lastItem && lastItem.tarikh_sembelihan) ? lastItem.tarikh_sembelihan : '{{ date('Y-m-d') }}';
            let defaultTempat = (lastItem && lastItem.tempat_sembelihan) ? lastItem.tempat_sembelihan : (document.getElementById('lokasi_sembelih_main') ? document.getElementById('lokasi_sembelih_main').value : '');
            let defaultHari = (lastItem && lastItem.hari_sembelihan_korban) ? lastItem.hari_sembelihan_korban : (this.hariKorbanPercuma || 'Hari Raya Pertama');
            
            this.items.push({
                ternakan_id: '',
                jantina: 'J',
                no_id_ternakan: '',
                no_siri_kad_pendaftaran: '',
                tarikh_sembelihan: defaultDate,
                hari_sembelihan_korban: defaultHari,
                tempat_sembelihan: defaultTempat,
                no_kn_haiwan_16: '',
                kuantiti_karkas: '1 Ekor'
            });
            this.updateHariKorbanPercumaState();
        }
    },
    removeRow(index) {
        if (this.items.length > 1) {
            this.items.splice(index, 1);
        }
    },
    onTernakanSelect(index, tId) {
        if (!tId) {
            this.items[index].ternakan_id = '';
            this.items[index].no_id_ternakan = '';
            this.items[index].jantina = 'J';
            this.items[index].no_siri_kad_pendaftaran = '';
            return;
        }
        let found = this.availableTernakan.find(t => t.id == tId);
        if (found) {
            this.items[index].ternakan_id = found.id;
            this.items[index].no_id_ternakan = found.no_tag;
            this.items[index].jantina = found.jantina; // Jantina mengikut rekod pendaftaran ternakan
            this.items[index].no_siri_kad_pendaftaran = found.no_siri_kad_kuning;
            if (!this.items[index].tempat_sembelihan) {
                let defaultTempat = document.getElementById('lokasi_sembelih_main') ? document.getElementById('lokasi_sembelih_main').value : '';
                this.items[index].tempat_sembelihan = defaultTempat || ('Rumah Sembelih ' + (found.lokasi_kandang || 'Jajahan'));
            }
        }
    }
}">
    
    <!-- Info Banner: Had Baris, Tempoh Sah Laku 7 Hari, Bayaran RM10 -->
    <div class="p-4 sm:p-5 rounded-3xl bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-emerald-500/10 border border-amber-200 shadow-2xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-lg font-bold shrink-0 shadow-md">
                    <i class="fa-solid fa-file-invoice"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-900 text-sm">Borang D & Sijil Kesihatan Veterinar (SKV) Sembelih</h3>
                    <p class="text-xs text-slate-600 mt-0.5">Enakmen Pendaftaran Ternakan Ruminan 2024 &bull; Perintah Menteri Besar Kn. P.U. 16 (2005)</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <div class="px-3 py-1.5 rounded-xl bg-white border border-amber-300 text-amber-900 text-xs font-bold shadow-2xs">
                    <i class="fa-solid fa-money-bill-wave text-amber-600 mr-1"></i> Kadar Fi: <strong>1 Sijil SKV (RM 10.00) + Pembatalan: RM 10.00 (Besar) / RM 5.00 (Kecil)</strong>
                </div>
                <div class="px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs font-bold shadow-2xs">
                    <i class="fa-solid fa-clock-rotate-left text-emerald-600 mr-1"></i> Sah Laku: <strong>7 Hari</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Form Card -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs">
        <form action="{{ route('eptr.borang-d.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 text-xs">
            @csrf

            <!-- Section 1: Maklumat Pemunya & Pilihan Musim -->
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-slate-900 text-white flex items-center justify-center text-xs">1</span>
                        <span>Maklumat Pemunya & Spesies Sembelihan</span>
                    </h4>
                    
                    <!-- Status Dikesan: Musim Korban vs Hari Biasa (Auto-Kira Berdasarkan Tarikh Sembelihan) -->
                    <div class="flex items-center gap-2">
                        <template x-if="isMusimKorban">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-red-100 text-red-800 font-bold text-xs border border-red-200 shadow-2xs">
                                <i class="fa-solid fa-moon text-red-600"></i>
                                <span>Musim Hari Raya Korban (Auto-Dikesan &bull; Had 10 Baris)</span>
                            </span>
                        </template>
                        <template x-if="!isMusimKorban">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs border border-slate-200">
                                <i class="fa-solid fa-calendar-day text-slate-500"></i>
                                <span>Hari Biasa (Had 7 Baris)</span>
                            </span>
                        </template>
                        <input type="hidden" name="is_musim_korban" :value="isMusimKorban ? '1' : '0'">
                    </div>
                </div>

                <!-- Pilihan Hari Sembelihan Percuma Korban (Pengecualian Bayaran Pembatalan) -->
                <div x-show="isMusimKorban" x-transition class="p-4 bg-gradient-to-r from-red-500/10 via-amber-500/10 to-emerald-500/10 border border-red-200 rounded-2xl space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-lg bg-red-600 text-white font-black text-[10px] font-mono">MUSIM RAYA KORBAN</span>
                            <span class="font-extrabold text-slate-900 text-xs">
                                <span x-show="isEligiblePercuma">Pilihan Hari Sembelihan Percuma Bayaran Pembatalan Pendaftaran</span>
                                <span x-show="!isEligiblePercuma">Ketetapan Fi Sembelihan Hari Tasyrik (Wajib Berbayar)</span>
                            </span>
                        </div>
                        <span x-show="isEligiblePercuma" class="text-[11px] font-bold text-emerald-800 bg-emerald-100 px-2.5 py-0.5 rounded-lg border border-emerald-300">
                            <i class="fa-solid fa-gift mr-1"></i> 1 Hari Percuma
                        </span>
                        <span x-show="!isEligiblePercuma" class="text-[11px] font-bold text-amber-900 bg-amber-100 px-2.5 py-0.5 rounded-lg border border-amber-300">
                            <i class="fa-solid fa-receipt mr-1"></i> Wajib Berbayar (Tiada Percuma)
                        </span>
                    </div>
                    
                    <div class="p-2.5 rounded-xl bg-white border border-amber-200 text-xs text-slate-700 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-circle-info text-amber-600 text-sm"></i>
                            <div>
                                <span class="text-[11px]">Tarikh Sembelihan Utama: <strong class="font-mono text-slate-900" x-text="primaryTarikh"></strong></span>
                                <template x-if="detectedHariInfo">
                                    <span class="text-[10px] font-bold block" :class="detectedHariInfo.eligiblePercuma ? 'text-emerald-700' : 'text-amber-800'">
                                        <i class="fa-solid" :class="detectedHariInfo.eligiblePercuma ? 'fa-calendar-check text-emerald-600' : 'fa-calendar-day text-amber-600'"></i> 
                                        Autotetap: Dikesan jatuh pada <span x-text="detectedHariInfo.name"></span> (<span x-text="detectedHariInfo.hijri"></span>)
                                    </span>
                                </template>
                            </div>
                        </div>
                        <span class="text-[10px] font-semibold text-slate-500">Auto Dihitung dari Jadual</span>
                    </div>

                    <!-- Jika Hari Raya Pertama atau Kedua: Paparkan Pilihan Radio Percuma -->
                    <template x-if="isEligiblePercuma">
                        <div class="space-y-3">
                            <p class="text-[11px] text-slate-600">
                                Pemohon diberi <strong>pengecualian percuma (RM 0.00)</strong> untuk Bayaran Pembatalan Pendaftaran Ternakan Sembelihan bagi <strong>salah satu hari pilihan (Hari Raya Pertama ATAU Hari Raya Kedua)</strong> untuk ruminan besar dan kecil. Sembelihan pada hari selain pilihan anda adalah berbayar.
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                                <label class="flex items-start gap-3 p-3 bg-white rounded-xl border cursor-pointer transition shadow-2xs" :class="hariKorbanPercuma === 'Hari Raya Pertama' ? 'border-emerald-500 ring-2 ring-emerald-500 bg-emerald-50/60' : 'border-slate-200 hover:border-slate-300'">
                                    <input type="radio" name="hari_korban_percuma" value="Hari Raya Pertama" x-model="hariKorbanPercuma" class="mt-0.5 text-emerald-600 focus:ring-emerald-500">
                                    <div>
                                        <strong class="text-slate-900 text-xs flex items-center gap-1.5">
                                            <span>Hari Raya Pertama (10 Zulhijjah)</span>
                                            <span class="px-1.5 py-0.2 rounded bg-emerald-600 text-white text-[9px] font-black">PERCUMA</span>
                                        </strong>
                                        <span class="text-[10px] text-slate-500 block mt-0.5">Sembelihan Hari Raya Pertama: <strong>RM 0.00</strong> &bull; Hari Raya Ke-2: <strong>Berbayar</strong></span>
                                    </div>
                                </label>
                                <label class="flex items-start gap-3 p-3 bg-white rounded-xl border cursor-pointer transition shadow-2xs" :class="hariKorbanPercuma === 'Hari Raya Kedua' ? 'border-emerald-500 ring-2 ring-emerald-500 bg-emerald-50/60' : 'border-slate-200 hover:border-slate-300'">
                                    <input type="radio" name="hari_korban_percuma" value="Hari Raya Kedua" x-model="hariKorbanPercuma" class="mt-0.5 text-emerald-600 focus:ring-emerald-500">
                                    <div>
                                        <strong class="text-slate-900 text-xs flex items-center gap-1.5">
                                            <span>Hari Raya Kedua (11 Zulhijjah)</span>
                                            <span class="px-1.5 py-0.2 rounded bg-emerald-600 text-white text-[9px] font-black">PERCUMA</span>
                                        </strong>
                                        <span class="text-[10px] text-slate-500 block mt-0.5">Sembelihan Hari Raya Ke-2: <strong>RM 0.00</strong> &bull; Hari Raya Pertama: <strong>Berbayar</strong></span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </template>

                    <!-- Jika Hari Raya Ketiga atau Keempat: Tidak Keluar Pilihan Percuma (Notis Berbayar Penuh) -->
                    <template x-if="!isEligiblePercuma">
                        <div class="p-3 bg-amber-50 border border-amber-300 rounded-xl text-amber-900 space-y-1.5">
                            <div class="flex items-center gap-2 font-bold text-xs text-amber-900">
                                <i class="fa-solid fa-triangle-exclamation text-amber-600"></i>
                                <span>Tiada Pengecualian Percuma untuk Hari Raya Ketiga &amp; Keempat (Hari Tasyrik)</span>
                            </div>
                            <p class="text-[11px] text-slate-700">
                                Pengecualian percuma bayaran pembatalan hanya terhad kepada sembelihan pada <strong>Hari Raya Pertama (10 Zulhijjah) ATAU Hari Raya Kedua (11 Zulhijjah)</strong>. Sembelihan pada <strong>Hari Raya Ketiga (12 Zulhijjah) dan Hari Raya Keempat (13 Zulhijjah)</strong> adalah <strong>WAJIB DIBAYAR</strong> mengikut kadar statutori (RM 10.00 / ekor bagi Ruminan Besar dan RM 5.00 / ekor bagi Ruminan Kecil).
                            </p>
                            <input type="hidden" name="hari_korban_percuma" value="">
                        </div>
                    </template>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Pilih Pemunya -->
                    <div class="sm:col-span-2 lg:col-span-1">
                        <label class="block font-bold text-slate-700 uppercase mb-1">Pemunya / Penternak <span class="text-rose-500">*</span></label>
                        <select name="pemunya_id" x-model="selectedPemunyaId" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            <option value="">-- Pilih Pemunya --</option>
                            @foreach($pemunyaList as $p)
                                <option value="{{ $p->id }}" {{ (old('pemunya_id') == $p->id || (isset($selectedTernakan) && $selectedTernakan->pemunya_id == $p->id)) ? 'selected' : '' }}>
                                    {{ $p->nama }} ({{ $p->jajahan }}) - {{ $p->no_kp }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Jenis Ternakan -->
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Jenis / Spesies <span class="text-rose-500">*</span></label>
                        <select name="jenis_ternakan" x-model="selectedJenisTernakan" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none font-bold">
                            <option value="Lembu" {{ old('jenis_ternakan') == 'Lembu' ? 'selected' : '' }}>Lembu</option>
                            <option value="Kerbau" {{ old('jenis_ternakan') == 'Kerbau' ? 'selected' : '' }}>Kerbau</option>
                            <option value="Kambing" {{ old('jenis_ternakan') == 'Kambing' ? 'selected' : '' }}>Kambing</option>
                            <option value="Biri-biri" {{ old('jenis_ternakan') == 'Biri-biri' ? 'selected' : '' }}>Biri-biri</option>
                        </select>
                    </div>

                    <!-- Tujuan Sembelih -->
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Tujuan Sembelih <span class="text-rose-500">*</span></label>
                        <select name="tujuan_sembelih" x-model="tujuanSembelih" @change="onTujuanChange()" required class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none font-bold">
                            <option value="Jualan" {{ old('tujuan_sembelih') == 'Jualan' ? 'selected' : '' }}>Jualan (Pasar / Runcit / Kedai)</option>
                            <option value="Ibadah Korban" {{ old('tujuan_sembelih') == 'Ibadah Korban' ? 'selected' : '' }}>Ibadah Korban</option>
                            <option value="Aqiqah" {{ old('tujuan_sembelih') == 'Aqiqah' ? 'selected' : '' }}>Aqiqah</option>
                            <option value="Kenduri / Jamuan" {{ old('tujuan_sembelih') == 'Kenduri / Jamuan' ? 'selected' : '' }}>Kenduri / Jamuan</option>
                            <option value="Persendirian" {{ old('tujuan_sembelih') == 'Persendirian' ? 'selected' : '' }}>Kegunaan Sendiri</option>
                        </select>
                    </div>

                    <!-- Tempat Sembelihan Induk -->
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Premis / Lokasi Sembelih</label>
                        <input type="text" id="lokasi_sembelih_main" name="lokasi_sembelih" value="{{ old('lokasi_sembelih') }}" placeholder="Rumah Sembelih / Premis" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Section 2: Jadual Senarai Ternakan SKV Sembelih (Maksimum 7 atau 10 baris) -->
            <div class="space-y-3 pt-2">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-slate-900 text-white flex items-center justify-center text-xs">2</span>
                            <span>Jadual Ternakan SKV Sembelih (<span x-text="items.length"></span> / <span x-text="maxRows"></span> Baris)</span>
                        </h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            Had pengisian: <strong class="text-slate-800" x-text="maxRows + ' baris sahaja'"></strong>.
                            <span x-show="!isMusimKorban" class="text-amber-700 font-semibold">(Hari biasa: Maksimum 7 baris)</span>
                            <span x-show="isMusimKorban" class="text-red-700 font-semibold">(Musim Hari Raya Korban: Maksimum 10 baris)</span>
                        </p>
                    </div>

                    <button type="button" @click="addRow()" :disabled="items.length >= maxRows" :class="items.length >= maxRows ? 'opacity-40 cursor-not-allowed bg-slate-200 text-slate-500' : 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs'" class="px-3.5 py-2 rounded-xl font-bold text-xs transition flex items-center gap-1.5">
                        <i class="fa-solid fa-plus-circle"></i>
                        <span>Tambah Baris</span>
                    </button>
                </div>

                <div class="overflow-x-auto border border-slate-200 rounded-2xl">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold uppercase text-[10px]">
                            <tr>
                                <th class="p-2.5 w-10 text-center">BIL</th>
                                <th class="p-2.5 min-w-[190px]">Pilih / No. ID Ternakan</th>
                                <th class="p-2.5 w-24">Jantina (J/B)</th>
                                <th class="p-2.5 min-w-[150px]">No Siri Kad Kuning</th>
                                <th class="p-2.5 min-w-[170px]">Tarikh Sembelihan</th>
                                <th class="p-2.5 min-w-[150px]">Tempat Sembelih</th>
                                <th class="p-2.5 min-w-[120px]">Kuantiti Karkas</th>
                                <th class="p-2.5 w-12 text-center">Hapus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="(item, index) in items" :key="index">
                                <tr class="hover:bg-slate-50/80 transition">
                                    <!-- Bil -->
                                    <td class="p-2 text-center font-bold text-slate-500" x-text="index + 1"></td>

                                    <!-- Pilih / No ID Ternakan -->
                                    <td class="p-2">
                                        <div class="space-y-1">
                                            <select x-model="item.ternakan_id" @change="onTernakanSelect(index, $event.target.value)" class="w-full p-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-1 focus:ring-amber-500">
                                                <option value="">-- Pilih Ternakan Berdaftar (Auto-Isi) --</option>
                                                <template x-for="t in getAvailableTernakanForRow(index)" :key="t.id">
                                                    <option :value="t.id" x-text="t.label" :selected="t.id == item.ternakan_id"></option>
                                                </template>
                                            </select>
                                            <input type="text" :name="'items[' + index + '][no_id_ternakan]'" x-model="item.no_id_ternakan" placeholder="No Tag / No ID..." required class="w-full p-1.5 text-xs bg-white border border-slate-200 rounded-lg font-mono font-bold text-emerald-800">
                                            <input type="hidden" :name="'items[' + index + '][ternakan_id]'" x-model="item.ternakan_id">
                                        </div>
                                    </td>

                                    <!-- Jantina (J/B) - Berdasarkan jantina pendaftaran ternakan -->
                                    <td class="p-2">
                                        <div class="relative">
                                            <select :name="'items[' + index + '][jantina]'" x-model="item.jantina" :disabled="!!item.ternakan_id" class="w-full p-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg font-bold disabled:bg-slate-100 disabled:text-slate-700 disabled:cursor-not-allowed">
                                                <option value="J">J (Jantan)</option>
                                                <option value="B">B (Betina)</option>
                                            </select>
                                            <template x-if="item.ternakan_id">
                                                <input type="hidden" :name="'items[' + index + '][jantina]'" :value="item.jantina">
                                            </template>
                                        </div>
                                    </td>

                                    <!-- No Siri Kad Kuning - Tak boleh diubah (Readonly) -->
                                    <td class="p-2">
                                        <input type="text" :name="'items[' + index + '][no_siri_kad_pendaftaran]'" x-model="item.no_siri_kad_pendaftaran" placeholder="Auto dari Kad Kuning..." readonly class="w-full p-1.5 text-xs bg-slate-100 border border-slate-200 rounded-lg font-mono font-medium text-slate-700 cursor-not-allowed select-none" title="No Siri Kad Kuning diambil secara automatik daripada rekod pendaftaran ternakan dan tidak boleh diubah">
                                    </td>

                                    <!-- Tarikh Sembelihan -->
                                    <td class="p-2">
                                        <div class="space-y-1">
                                            <input type="date" :name="'items[' + index + '][tarikh_sembelihan]'" x-model="item.tarikh_sembelihan" @change="onRowDateChange(index)" required class="w-full p-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg font-bold">
                                            
                                            <!-- Status Musim Korban Badge per Baris -->
                                            <template x-if="isMusimKorban">
                                                <div class="pt-0.5">
                                                    <input type="hidden" :name="'items[' + index + '][hari_sembelihan_korban]'" :value="item.hari_sembelihan_korban || (aidiladhaMap[item.tarikh_sembelihan] ? aidiladhaMap[item.tarikh_sembelihan].name : 'Hari Raya Pertama')">
                                                    <template x-if="aidiladhaMap[item.tarikh_sembelihan]">
                                                        <div>
                                                            <span class="text-[9.5px] font-bold block text-slate-700" x-text="aidiladhaMap[item.tarikh_sembelihan].name + ' (' + aidiladhaMap[item.tarikh_sembelihan].hijri + ')'"></span>
                                                            <span x-show="aidiladhaMap[item.tarikh_sembelihan].name === hariKorbanPercuma" class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800 font-bold text-[9px]">
                                                                <i class="fa-solid fa-gift"></i> PERCUMA (RM 0)
                                                            </span>
                                                            <span x-show="aidiladhaMap[item.tarikh_sembelihan].name !== hariKorbanPercuma" class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded bg-amber-100 text-amber-900 font-bold text-[9px]">
                                                                <i class="fa-solid fa-receipt"></i> BERBAYAR
                                                            </span>
                                                        </div>
                                                    </template>
                                                    <template x-if="!aidiladhaMap[item.tarikh_sembelihan]">
                                                        <div>
                                                            <span x-show="(item.hari_sembelihan_korban || 'Hari Raya Pertama') === hariKorbanPercuma" class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800 font-bold text-[9px]">
                                                                <i class="fa-solid fa-gift"></i> PERCUMA (RM 0)
                                                            </span>
                                                            <span x-show="(item.hari_sembelihan_korban || 'Hari Raya Pertama') !== hariKorbanPercuma" class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded bg-amber-100 text-amber-900 font-bold text-[9px]">
                                                                <i class="fa-solid fa-receipt"></i> BERBAYAR
                                                            </span>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </td>

                                    <!-- Tempat Sembelihan -->
                                    <td class="p-2">
                                        <input type="text" :name="'items[' + index + '][tempat_sembelihan]'" x-model="item.tempat_sembelihan" placeholder="Rumah Sembelih..." class="w-full p-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg">
                                    </td>

                                    <!-- Kuantiti Karkas -->
                                    <td class="p-2">
                                        <input type="text" :name="'items[' + index + '][kuantiti_karkas]'" x-model="item.kuantiti_karkas" placeholder="1 Ekor" class="w-full p-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg">
                                    </td>

                                    <!-- Button Delete Row -->
                                    <td class="p-2 text-center">
                                        <button type="button" @click="removeRow(index)" :disabled="items.length === 1" class="p-1.5 text-slate-400 hover:text-rose-600 disabled:opacity-20 disabled:hover:text-slate-400 transition" title="Padam Baris">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Section 3: Maklumat Agihan / Pemindahan Karkas (Alamat 1, 2, 3) -->
            <div class="space-y-4 pt-2">
                <div class="border-b border-slate-100 pb-2">
                    <h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-slate-900 text-white flex items-center justify-center text-xs">3</span>
                        <span>Maklumat Agihan & Pemindahan Karkas (Pilihan)</span>
                    </h4>
                    <p class="text-[11px] text-slate-500 mt-0.5">Untuk tujuan Sijil SKV Pemindahan Karkas (Halaman 2)</p>
                </div>

                <div class="space-y-3 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                    <!-- Alamat 1 -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                        <div class="sm:col-span-8">
                            <label class="block font-bold text-slate-700 mb-1">A. Alamat Agihan 1</label>
                            <input type="text" name="alamat_1" value="{{ old('alamat_1') }}" placeholder="Contoh: Pasar Besar Siti Khadijah, Kota Bharu" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl">
                        </div>
                        <div class="sm:col-span-4">
                            <label class="block font-bold text-slate-700 mb-1">Bahagian / Kuantiti Karkas 1</label>
                            <input type="text" name="kuantiti_karkas_1" value="{{ old('kuantiti_karkas_1') }}" placeholder="Contoh: 2 Paha / 50 kg" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl">
                        </div>
                    </div>

                    <!-- Alamat 2 -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                        <div class="sm:col-span-8">
                            <label class="block font-bold text-slate-700 mb-1">B. Alamat Agihan 2</label>
                            <input type="text" name="alamat_2" value="{{ old('alamat_2') }}" placeholder="Contoh: Kedai Daging Segar Kubang Kerian" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl">
                        </div>
                        <div class="sm:col-span-4">
                            <label class="block font-bold text-slate-700 mb-1">Bahagian / Kuantiti Karkas 2</label>
                            <input type="text" name="kuantiti_karkas_2" value="{{ old('kuantiti_karkas_2') }}" placeholder="Contoh: 1 Bangkai Penuh" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl">
                        </div>
                    </div>

                    <!-- Alamat 3 -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                        <div class="sm:col-span-8">
                            <label class="block font-bold text-slate-700 mb-1">C. Alamat Agihan 3</label>
                            <input type="text" name="alamat_3" value="{{ old('alamat_3') }}" placeholder="Alamat agihan tambahan" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl">
                        </div>
                        <div class="sm:col-span-4">
                            <label class="block font-bold text-slate-700 mb-1">Bahagian / Kuantiti Karkas 3</label>
                            <input type="text" name="kuantiti_karkas_3" value="{{ old('kuantiti_karkas_3') }}" placeholder="Kuantiti karkas" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Catatan & Submit Button -->
            <div class="space-y-3 pt-2">
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Catatan Tambahan (Jika Ada)</label>
                    <textarea name="catatan" rows="2" placeholder="Catatan atau maklumat sokongan" class="w-full px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">{{ old('catatan') }}</textarea>
                </div>

                <!-- Pengiraan Fi Statutori Borang D & SKV Sembelih (Perenggan 11(1)(b)) -->
                <div class="p-5 rounded-3xl bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-slate-100 border border-amber-200/80 space-y-4 shadow-xs">
                    <div class="flex items-center justify-between border-b border-amber-200/60 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-lg bg-amber-200 text-amber-900 font-black text-[10px] font-mono tracking-wider">FI STATUTORI</span>
                            <div>
                                <span class="font-extrabold text-slate-900 text-xs">Kadar Fi Sembelihan &amp; SKV (Perenggan 11(1)(b))</span>
                                <span class="text-[10px] text-slate-500 block">Kos 1 Sijil SKV Sembelih (RM 10.00) + Kadar Pembatalan Sembelihan Mengikut Spesies</span>
                            </div>
                        </div>
                        <a href="{{ route('eptr.jadual-fi') }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-white border border-amber-200 text-[11px] font-bold text-amber-900 hover:bg-amber-100 transition flex items-center gap-1.5 shadow-2xs">
                            <i class="fa-solid fa-receipt text-amber-600"></i>
                            <span>Jadual Fi Rasmi</span>
                        </a>
                    </div>

                    <!-- Pecahan 3 Kad: 1. Fi SKV Sembelih Tetap RM10, 2. Pembatalan Besar RM10/ekor, 3. Pembatalan Kecil RM5/ekor -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                        <!-- 1. Kos Sijil SKV Sembelih (1 Sijil) -->
                        <div class="p-3.5 bg-white rounded-2xl border border-blue-200/70 space-y-1.5 shadow-2xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-800 text-[11px] flex items-center gap-1.5">
                                    <i class="fa-solid fa-file-shield text-blue-600"></i>
                                    <span>Kadar 1 Sijil SKV Sembelih</span>
                                </span>
                                <span class="px-2 py-0.5 rounded-md bg-blue-100 text-blue-900 font-mono font-bold text-[10px]">1 Sijil</span>
                            </div>
                            <div class="text-[11px] text-slate-600 flex justify-between items-center pt-1.5 border-t border-slate-100">
                                <span>Sijil SKV &amp; Karkas:</span>
                                <strong class="text-blue-900 font-mono font-bold text-xs">RM 10.00</strong>
                            </div>
                        </div>

                        <!-- 2. Pembatalan Sembelihan Ruminan Besar -->
                        <div class="p-3.5 bg-white rounded-2xl border border-amber-200/70 space-y-1.5 shadow-2xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-800 text-[11px] flex items-center gap-1.5">
                                    <i class="fa-solid fa-cow text-amber-600"></i>
                                    <span>Pembatalan Sembelih (Besar)</span>
                                </span>
                                <span class="px-2 py-0.5 rounded-md bg-amber-100 text-amber-900 font-mono font-bold text-[10px]">RM 10.00 / ekor</span>
                            </div>
                            <div class="text-[11px] text-slate-600 flex justify-between items-center pt-1.5 border-t border-slate-100">
                                <div>
                                    <span>Lembu / Kerbau (<span class="font-bold font-mono" x-text="countBesar"></span> ekor):</span>
                                    <template x-if="isMusimKorban && countBesarPercuma > 0">
                                        <span class="text-[9.5px] text-emerald-700 block font-semibold">
                                            <i class="fa-solid fa-gift"></i> <span x-text="countBesarPercuma"></span> ekor Percuma (<span x-text="hariKorbanPercuma"></span>)
                                        </span>
                                    </template>
                                </div>
                                <div class="text-right">
                                    <strong class="text-amber-900 font-mono font-bold text-xs">RM <span x-text="fiPembatalanBesar.toFixed(2)"></span></strong>
                                    <span x-show="isMusimKorban && countBesarBerbayar > 0" class="text-[9.5px] text-slate-400 block">(<span x-text="countBesarBerbayar"></span> &times; RM10)</span>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Pembatalan Sembelihan Ruminan Kecil -->
                        <div class="p-3.5 bg-white rounded-2xl border border-emerald-200/70 space-y-1.5 shadow-2xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-800 text-[11px] flex items-center gap-1.5">
                                    <i class="fa-solid fa-hippo text-emerald-600"></i>
                                    <span>Pembatalan Sembelih (Kecil)</span>
                                </span>
                                <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-900 font-mono font-bold text-[10px]">RM 5.00 / ekor</span>
                            </div>
                            <div class="text-[11px] text-slate-600 flex justify-between items-center pt-1.5 border-t border-slate-100">
                                <div>
                                    <span>Kambing / Bebiri (<span class="font-bold font-mono" x-text="countKecil"></span> ekor):</span>
                                    <template x-if="isMusimKorban && countKecilPercuma > 0">
                                        <span class="text-[9.5px] text-emerald-700 block font-semibold">
                                            <i class="fa-solid fa-gift"></i> <span x-text="countKecilPercuma"></span> ekor Percuma (<span x-text="hariKorbanPercuma"></span>)
                                        </span>
                                    </template>
                                </div>
                                <div class="text-right">
                                    <strong class="text-emerald-900 font-mono font-bold text-xs">RM <span x-text="fiPembatalanKecil.toFixed(2)"></span></strong>
                                    <span x-show="isMusimKorban && countKecilBerbayar > 0" class="text-[9.5px] text-slate-400 block">(<span x-text="countKecilBerbayar"></span> &times; RM5)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Jumlah Besar Card -->
                    <div class="p-4 bg-gradient-to-r from-slate-950 via-slate-900 to-amber-950 text-white rounded-2xl shadow-md flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div class="space-y-1">
                            <div class="text-xs text-slate-300 flex items-center gap-2">
                                <span>Jumlah Bilangan Ternakan:</span>
                                <span class="px-2.5 py-0.5 rounded-md bg-amber-500/20 text-amber-300 font-bold font-mono text-xs" x-text="totalEkor + ' Ekor'"></span>
                            </div>
                            <div class="text-[10px] text-slate-400">
                                <span class="block">
                                    <i class="fa-solid fa-circle-check text-emerald-400 mr-1"></i> 1 Sijil SKV Sembelih (RM 10.00) + Pembatalan Sembelihan (<span x-text="'RM ' + totalFiPembatalan.toFixed(2)"></span>)
                                </span>
                                <template x-if="isMusimKorban && hariKorbanPercuma">
                                    <span class="text-emerald-300 block font-medium mt-0.5">
                                        <i class="fa-solid fa-gift text-emerald-400 mr-1"></i> Termasuk Pengecualian Percuma bagi sembelihan <strong x-text="hariKorbanPercuma"></strong>
                                    </span>
                                </template>
                                <template x-if="isMusimKorban && !hariKorbanPercuma">
                                    <span class="text-amber-300 block font-medium mt-0.5">
                                        <i class="fa-solid fa-receipt text-amber-400 mr-1"></i> Sembelihan Hari Tasyrik (Semua sembelihan berbayar)
                                    </span>
                                </template>
                            </div>
                        </div>
                        <div class="text-left sm:text-right border-t sm:border-t-0 border-white/10 pt-2 sm:pt-0 w-full sm:w-auto">
                            <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider block">Jumlah Kadar Fi Sembelihan dan SKV</span>
                            <span class="text-xl sm:text-2xl font-black text-amber-300 font-mono tracking-tight" x-text="'RM ' + totalFi.toFixed(2)"></span>
                        </div>
                    </div>
                </div>

                <!-- Muat Naik Salinan Resit Pembayaran Fi Statutori (Wajib bagi Penternak/Pemohon) -->
                <div class="space-y-2 pt-1">
                    <label class="block font-bold text-slate-800 uppercase text-xs">
                        Salinan Resit Pembayaran Fi Sembelihan &amp; SKV
                        @if(!Auth::user()->isStaff())
                            <span class="text-rose-500 font-black">* (Wajib Dimuat Naik)</span>
                        @else
                            <span class="text-slate-400 font-normal">(Pilihan bagi Staf)</span>
                        @endif
                    </label>

                    <div class="p-5 bg-slate-50 rounded-2xl border-2 border-dashed {{ $errors->has('resit_pembayaran') ? 'border-rose-400 bg-rose-50/20' : 'border-slate-300 hover:border-emerald-500' }} transition text-center relative">
                        <input type="file" name="resit_pembayaran" id="resit_pembayaran_borang_d" @change="onFileSelected($event)" accept="image/jpeg,image/png,image/jpg,application/pdf" {{ !Auth::user()->isStaff() ? 'required' : '' }} class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        
                        <div class="space-y-2 pointer-events-none">
                            <div class="w-12 h-12 mx-auto rounded-full {{ $errors->has('resit_pembayaran') ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }} flex items-center justify-center text-xl shadow-xs">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </div>
                            <div class="font-bold text-slate-800 text-sm">
                                <span x-show="!receiptFileName">Klik atau Seret Resit Pembayaran di Sini {{ !Auth::user()->isStaff() ? '(Wajib)' : '' }}</span>
                                <span x-show="receiptFileName" class="text-emerald-700 font-mono" x-text="receiptFileName"></span>
                            </div>
                            <p class="text-[11px] text-slate-500" x-show="!receiptFileName">
                                Format yang disokong: <b>PDF, JPG, PNG</b> (Maksimum 2MB)
                            </p>
                            <p class="text-[11px] text-emerald-600 font-semibold" x-show="receiptFileSize">
                                Saiz Fail: <span x-text="receiptFileSize"></span>
                            </p>
                        </div>
                    </div>
                    @error('resit_pembayaran')
                        <p class="text-xs font-bold text-rose-600 mt-2 flex items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-3 border-t border-slate-100">
                    <div class="text-[11px] text-slate-500">
                        <i class="fa-solid fa-circle-check text-emerald-600 mr-1"></i> Termasuk 1 set lengkap (Borang D + SKV Sembelih &amp; Karkas).
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('eptr.borang-d.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 font-bold text-slate-700 transition">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold shadow-lg shadow-amber-700/30 transition flex items-center gap-2">
                            <i class="fa-solid fa-file-invoice"></i>
                            <span>Hantar Permohonan SKV Sembelih</span>
                        </button>
                    </div>
                </div>
            </div>

        </form>
    </div>

</div>
@endsection
