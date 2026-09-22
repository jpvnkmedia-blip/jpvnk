@extends('layouts.app')

@section('title', 'Perincian Item: ' . $item->nama_item . ' (' . $item->kod_item . ')')
@section('page_title', ($item->isStorUbat() ? 'Stor Ubat & Farmasi: ' : 'Stor Peralatan Pejabat: ') . $item->nama_item)

@section('content')
<div class="space-y-6" x-data="{ modalMasuk: false, modalKeluar: false, modalPinjam: false }">

    <!-- Top Action Toolbar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ $item->isStorUbat() ? route('inventori.ubat.index') : route('inventori.pejabat.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 bg-white px-3.5 py-2 rounded-xl border border-slate-200 transition">
            &larr; Kembali ke {{ $item->isStorUbat() ? 'Stor Ubat & Farmasi' : 'Stor Peralatan Pejabat' }}
        </a>

        @php
            $canManageItem = $item->isStorUbat() ? Auth::user()->canAccessStorUbat() : Auth::user()->canInputStorPejabat();
        @endphp

        @if($canManageItem)
        <div class="flex flex-wrap items-center gap-2">
            <!-- Butang Rekod Stok Masuk -->
            <button @click="modalMasuk = true" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-down"></i>
                <span>Rekod Stok Masuk</span>
            </button>

            <!-- Butang Rekod Stok Keluar -->
            <button @click="modalKeluar = true" class="px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-up"></i>
                <span>Rekod Stok Keluar / Agihan</span>
            </button>

            <!-- Butang Pinjaman -->
            <button @click="modalPinjam = true" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                <i class="fa-solid fa-hand-holding-hand text-indigo-400"></i>
                <span>Pinjam Peralatan</span>
            </button>

            <!-- Padam Item -->
            <form action="{{ route('inventori.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Adakah anda pasti mahu memadam item ini daripada pangkalan data stor?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition" title="Padam Item">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </form>
        </div>
        @else
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs border border-slate-200">
                <i class="fa-solid fa-eye mr-1"></i> Paparan Semakan Pegawai
            </span>
        </div>
        @endif
    </div>

    <!-- Item Overview Card -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs space-y-6 text-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-100 gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $item->isStorUbat() ? 'bg-rose-50 text-rose-800 border border-rose-200' : 'bg-indigo-50 text-indigo-800 border border-indigo-200' }}">
                        <i class="fa-solid {{ $item->isStorUbat() ? 'fa-pills' : 'fa-boxes-stacked' }} mr-1"></i>
                        {{ $item->isStorUbat() ? 'Stor Ubat & Farmasi' : 'Stor Peralatan Pejabat' }} &bull; {{ $item->kategori }}
                    </span>
                    <span class="font-mono text-xs font-bold text-slate-500">{{ $item->kod_item }}</span>
                </div>
                <h2 class="text-2xl font-black text-slate-900 mt-2">{{ $item->nama_item }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Lokasi: <b>{{ $item->lokasi_rak ?? '-' }}</b> &bull; {{ $item->jajahan }}
                </p>
            </div>

            <div class="text-right text-xs">
                <div class="text-slate-400 font-medium">Baki Stok Semasa:</div>
                <div class="font-black text-2xl {{ $item->kuantiti_semasa <= $item->kuantiti_minimum ? 'text-amber-600' : 'text-emerald-700' }}">
                    {{ number_format($item->kuantiti_semasa) }} {{ $item->unit }}
                </div>
                <div class="mt-1 flex items-center justify-end gap-2">
                    <span class="text-[11px] text-slate-500">Paras Minima: <b>{{ $item->kuantiti_minimum }} {{ $item->unit }}</b></span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $item->status === 'Mencukupi' ? 'bg-emerald-100 text-emerald-800' : ($item->status === 'Stok Rendah' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                        {{ $item->status }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Specifications Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 space-y-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">HARGA &amp; UNIT</span>
                <div class="text-sm font-bold text-slate-800">RM {{ number_format($item->harga_seunit, 2) }}</div>
                <div class="text-[11px] text-slate-500">Unit Ukuran: <b>{{ $item->unit }}</b></div>
            </div>

            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 space-y-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">PEMBEKAL UTAMA</span>
                <div class="font-bold text-slate-800 truncate">{{ $item->pembekal_utama ?? 'Pembekal Am Jabatan' }}</div>
                <div class="text-[11px] text-slate-500">Pusat Pembekalan</div>
            </div>

            @if($item->isStorUbat())
            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 space-y-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">NO. BATCH &amp; LUPUT</span>
                <div class="font-mono font-bold text-slate-800">{{ $item->no_batch ?? 'N/A' }}</div>
                <div class="text-[11px] {{ $item->isExpired() ? 'text-rose-600 font-bold' : ($item->isExpiringSoon() ? 'text-amber-600 font-bold' : 'text-slate-600') }}">
                    Luput: <b>{{ $item->tarikh_luput ? $item->tarikh_luput->format('d/m/Y') : 'Tiada Tarikh' }}</b>
                </div>
            </div>

            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 space-y-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">KAWALAN SUHU</span>
                <div class="font-bold text-slate-800 truncate">{{ $item->suhu_simpanan ?? 'Suhu Bilik (< 25°C)' }}</div>
                <div class="text-[11px] text-slate-500">Syarat Penyimpanan</div>
            </div>
            @else
            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 space-y-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">LOKASI RAK</span>
                <div class="font-bold text-slate-800">{{ $item->lokasi_rak ?? 'Stor Pejabat' }}</div>
                <div class="text-[11px] text-slate-500">Ibu Pejabat Kota Bharu</div>
            </div>

            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 space-y-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">NILAI KESELURUHAN BAKI</span>
                <div class="font-bold text-indigo-900 text-sm">RM {{ number_format($item->kuantiti_semasa * $item->harga_seunit, 2) }}</div>
                <div class="text-[11px] text-slate-500">Nilai atas stok semasa</div>
            </div>
            @endif
        </div>

        @if($item->deskripsi)
            <div class="text-xs text-slate-600 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="font-bold text-slate-800 block mb-1">Catatan / Indikasi / Spesifikasi:</span>
                <p class="leading-relaxed">{{ $item->deskripsi }}</p>
            </div>
        @endif
    </div>

    <!-- Movement Transactions & Loans -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Left: Transaksi Keluar Masuk Stok -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs space-y-4 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-indigo-600 text-base"></i>
                    <h3 class="text-sm font-bold text-slate-900">Sejarah Transaksi Stok Masuk &amp; Keluar</h3>
                </div>
                <span class="text-xs font-bold text-indigo-800">{{ $item->transaksi->count() }} Transaksi</span>
            </div>

            <div class="space-y-3 max-h-[500px] overflow-y-auto pr-1">
                @forelse($item->transaksi->sortByDesc('id') as $tr)
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1.5 hover:bg-slate-100/70 transition">
                        <div class="flex items-center justify-between">
                            <span class="font-bold px-2 py-0.5 rounded text-[11px] {{ $tr->jenis_transaksi === 'Stok Masuk' ? 'bg-emerald-100 text-emerald-800' : ($tr->jenis_transaksi === 'Pelupusan' ? 'bg-slate-200 text-slate-800' : 'bg-rose-100 text-rose-800') }}">
                                <i class="fa-solid {{ $tr->jenis_transaksi === 'Stok Masuk' ? 'fa-arrow-down' : ($tr->jenis_transaksi === 'Pelupusan' ? 'fa-trash' : 'fa-arrow-up') }} mr-1"></i>
                                {{ $tr->jenis_transaksi }}: {{ number_format($tr->kuantiti) }} {{ $item->unit }}
                            </span>
                            <span class="font-mono text-slate-400 text-[10px]">{{ $tr->created_at ? $tr->created_at->format('d/m/Y H:i') : '-' }}</span>
                        </div>
                        <div class="text-slate-800">
                            Penerima / Pembekal: <b>{{ $tr->penerima_atau_pembekal }}</b>
                        </div>
                        <div class="text-[11px] text-slate-500 flex items-center justify-between border-t border-slate-200/60 pt-1">
                            <span>Rujukan: <b class="font-mono">{{ $tr->rujukan_dokumen ?? '-' }}</b></span>
                            <span>Baki Selepas: <b class="text-slate-900">{{ number_format($tr->baki_selepas) }} {{ $item->unit }}</b></span>
                        </div>
                        @if($tr->catatan)
                            <div class="text-[10px] text-slate-500 italic bg-white p-1.5 rounded-lg border border-slate-100">
                                "{{ $tr->catatan }}"
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400">
                        <i class="fa-solid fa-clock-rotate-left text-2xl mb-1 text-slate-300"></i>
                        <p>Belum ada rekod transaksi keluar/masuk.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right: Rekod Pinjaman Peralatan -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs space-y-4 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-hand-holding text-amber-600 text-base"></i>
                    <h3 class="text-sm font-bold text-slate-900">Rekod Pinjaman &amp; Penggunaan Lapangan</h3>
                </div>
                <span class="text-xs font-bold text-amber-800">{{ $item->pinjaman->count() }} Pinjaman</span>
            </div>

            <div class="space-y-3 max-h-[500px] overflow-y-auto pr-1">
                @forelse($item->pinjaman->sortByDesc('id') as $pj)
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-900">{{ $pj->peminjam->name ?? '-' }} ({{ $pj->kuantiti }} Unit)</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $pj->status === 'Dipinjam' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                {{ $pj->status }}
                            </span>
                        </div>
                        <div class="text-slate-700">Tujuan: {{ $pj->tujuan_pinjaman }}</div>
                        <div class="text-[11px] text-slate-500 flex items-center justify-between">
                            <span>Pinjam: {{ $pj->tarikh_pinjam ? $pj->tarikh_pinjam->format('d/m/Y') : '-' }}</span>
                            <span>Jangka Pulang: {{ $pj->tarikh_jangka_pulang ? $pj->tarikh_jangka_pulang->format('d/m/Y') : '-' }}</span>
                        </div>
                        @if($pj->status === 'Dipinjam' && $canManageItem)
                            <form action="{{ route('inventori.pinjaman.pulang', $pj->id) }}" method="POST" class="pt-1.5 border-t border-slate-200/60 flex justify-end">
                                @csrf
                                <button type="submit" class="px-3 py-1 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] shadow-xs transition flex items-center gap-1">
                                    <i class="fa-solid fa-check"></i> Sahkan Pemulangan
                                </button>
                            </form>
                        @endif
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400">
                        <i class="fa-solid fa-hand-holding text-2xl mb-1 text-slate-300"></i>
                        <p>Tiada rekod pinjaman peralatan aktif.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- MODAL: Rekod Stok Masuk -->
    <div x-show="modalMasuk" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl text-xs space-y-4" @click.outside="modalMasuk = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-arrow-down"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Rekod Penerimaan Stok Masuk</h3>
                        <p class="text-[11px] text-slate-500 font-mono">{{ $item->kod_item }} &bull; {{ $item->nama_item }}</p>
                    </div>
                </div>
                <button @click="modalMasuk = false" class="text-slate-400 hover:text-slate-600 text-base">&times;</button>
            </div>

            <form action="{{ route('inventori.transaksi.store', $item->id) }}" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="jenis_transaksi" value="Stok Masuk">

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Kuantiti Masuk ({{ $item->unit }}) <span class="text-rose-500">*</span></label>
                    <input type="number" name="kuantiti" min="1" value="10" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <span class="text-[10px] text-slate-400 mt-0.5 block">Baki semasa: {{ $item->kuantiti_semasa }} {{ $item->unit }}</span>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Nama Pembekal / Punca Bekalan <span class="text-rose-500">*</span></label>
                    <input type="text" name="penerima_atau_pembekal" value="{{ $item->pembekal_utama ?? 'Pembekal Rasmi' }}" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">No. Rujukan Dokumen (DO / Invois / PO)</label>
                    <input type="text" name="rujukan_dokumen" placeholder="cth: DO-2026-9912 / INV-8821" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-mono focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Catatan Penerimaan</label>
                    <textarea name="catatan" rows="2" placeholder="Catatan perolehan stok, keadaan bungkusan atau batch..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none font-medium"></textarea>
                </div>

                <div class="pt-3 flex justify-end gap-2">
                    <button type="button" @click="modalMasuk = false" class="px-4 py-2 rounded-xl bg-slate-100 font-bold text-slate-600 hover:bg-slate-200 transition">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-md transition flex items-center gap-1.5">
                        <i class="fa-solid fa-plus-circle"></i> Sahkan Stok Masuk
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: Rekod Stok Keluar / Agihan -->
    <div x-show="modalKeluar" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl text-xs space-y-4" @click.outside="modalKeluar = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-arrow-up"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Rekod Pengeluaran &amp; Agihan Stok</h3>
                        <p class="text-[11px] text-slate-500 font-mono">{{ $item->kod_item }} &bull; {{ $item->nama_item }}</p>
                    </div>
                </div>
                <button @click="modalKeluar = false" class="text-slate-400 hover:text-slate-600 text-base">&times;</button>
            </div>

            <form action="{{ route('inventori.transaksi.store', $item->id) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Jenis Transaksi Keluar <span class="text-rose-500">*</span></label>
                    <select name="jenis_transaksi" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold focus:ring-2 focus:ring-rose-500 focus:outline-none">
                        <option value="Stok Keluar">Stok Keluar (Agihan Klinik / Bahagian)</option>
                        <option value="Pelupusan">Pelupusan (Rosak / Luput / Pecah)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Kuantiti Keluar ({{ $item->unit }}) <span class="text-rose-500">*</span></label>
                    <input type="number" name="kuantiti" min="1" max="{{ $item->kuantiti_semasa }}" value="1" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold focus:ring-2 focus:ring-rose-500 focus:outline-none">
                    <span class="text-[10px] text-slate-400 mt-0.5 block">Baki stok sedia ada: <b>{{ $item->kuantiti_semasa }} {{ $item->unit }}</b></span>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Penerima / Lokasi Agihan <span class="text-rose-500">*</span></label>
                    <input type="text" name="penerima_atau_pembekal" placeholder="cth: Klinik Haiwan Bachok / Bahagian Pentadbiran / Pegawai Rawatan" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium focus:ring-2 focus:ring-rose-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">No. Baucar / Borang Pengeluaran</label>
                    <input type="text" name="rujukan_dokumen" placeholder="cth: AGIHAN-2026-0044 / KELUAR-12" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-mono focus:ring-2 focus:ring-rose-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Tujuan / Catatan Pengeluaran</label>
                    <textarea name="catatan" rows="2" placeholder="Tujuan pengeluaran bagi operasi rawatan / kegunaan harian staf..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500 focus:outline-none font-medium"></textarea>
                </div>

                <div class="pt-3 flex justify-end gap-2">
                    <button type="button" @click="modalKeluar = false" class="px-4 py-2 rounded-xl bg-slate-100 font-bold text-slate-600 hover:bg-slate-200 transition">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold shadow-md transition flex items-center gap-1.5">
                        <i class="fa-solid fa-minus-circle"></i> Sahkan Stok Keluar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: Pinjam Peralatan -->
    <div x-show="modalPinjam" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl text-xs space-y-4" @click.outside="modalPinjam = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-hand-holding-hand"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Pinjaman Peralatan / Aset</h3>
                        <p class="text-[11px] text-slate-500 font-mono">{{ $item->kod_item }} &bull; {{ $item->nama_item }}</p>
                    </div>
                </div>
                <button @click="modalPinjam = false" class="text-slate-400 hover:text-slate-600 text-base">&times;</button>
            </div>

            <form action="{{ route('inventori.pinjaman.store', $item->id) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Pilih Pegawai / Staf Peminjam <span class="text-rose-500">*</span></label>
                    <select name="user_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-semibold focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        @foreach(\App\Models\User::whereIn('role', ['super_admin', 'admin_pejabat', 'admin_ubat', 'admin_eptr', 'admin_program', 'admin_epu', 'admin_kursus', 'admin_jajahan'])->get() as $u)
                            <option value="{{ $u->id }}" {{ Auth::id() === $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ $u->role_label }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Kuantiti Pinjaman ({{ $item->unit }}) <span class="text-rose-500">*</span></label>
                    <input type="number" name="kuantiti" value="1" min="1" max="{{ $item->kuantiti_semasa }}" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-bold focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <span class="text-[10px] text-slate-400 mt-0.5 block">Baki sedia ada: {{ $item->kuantiti_semasa }} {{ $item->unit }}</span>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Tujuan Pinjaman / Operasi <span class="text-rose-500">*</span></label>
                    <textarea name="tujuan_pinjaman" rows="2" required placeholder="Contoh: Operasi tag telinga di jajahan Bachok / Mesyuarat pengurusan" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Pinjam</label>
                        <input type="date" name="tarikh_pinjam" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Jangka Pulang</label>
                        <input type="date" name="tarikh_jangka_pulang" value="{{ date('Y-m-d', strtotime('+3 days')) }}" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>
                </div>

                <div class="pt-3 flex justify-end gap-2">
                    <button type="button" @click="modalPinjam = false" class="px-4 py-2 rounded-xl bg-slate-100 font-bold text-slate-600">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-md">Hantar Permohonan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
