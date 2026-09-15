@extends('layouts.app')

@section('title', 'Direktori & Pengurusan Fleet Kenderaan Jabatan')
@section('page_title', 'Pengurusan Fleet & Aset Kenderaan Rasmi JPVNK')

@section('content')
<div class="space-y-6" x-data="{ 
    modalTambah: false, 
    modalEdit: false,
    editId: null,
    editNoPendaftaran: '',
    editJenis: 'Pacuan 4 Roda (4x4)',
    editModel: '',
    editTahun: '{{ date('Y') }}',
    editKapasiti: 4,
    editJajahan: 'Ibu Pejabat Kota Bharu',
    editStatus: 'Sedia',
    editLokasiKunci: '',
    editOdometer: 0,
    editTarikhCukai: '',
    editCatatan: ''
}">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('kenderaan.index') }}" class="p-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition text-xs" title="Kembali">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h2 class="text-xl font-extrabold text-slate-900">Pengurusan Fleet Kenderaan Rasmi</h2>
            </div>
            <p class="text-xs text-slate-500 mt-0.5 ml-7">Pengurusan pendaftaran kenderaan operasi 4x4, van, lori angkut ternakan, status cukai jalan dan rekod odometer</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('kenderaan.index') }}" class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-2xs flex items-center gap-1.5">
                <i class="fa-solid fa-calendar-check text-teal-600"></i>
                <span>Tempahan Kenderaan</span>
            </a>
            @if(Auth::user()->isStaff())
                <button @click="modalTambah = true" class="px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-lg shadow-teal-700/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-plus-circle"></i>
                    <span>Tambah Kenderaan Baharu</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Stats Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Jumlah Kenderaan</div>
                <div class="text-2xl font-black text-slate-900 mt-0.5">{{ $totalKenderaan ?? 0 }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center font-bold text-base">
                <i class="fa-solid fa-truck-pickup"></i>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Sedia Digunakan</div>
                <div class="text-2xl font-black text-emerald-600 mt-0.5">{{ $totalSedia ?? 0 }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-base">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Sedang Beroperasi</div>
                <div class="text-2xl font-black text-blue-600 mt-0.5">{{ $totalDigunakan ?? 0 }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold text-base">
                <i class="fa-solid fa-route"></i>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Dalam Servis / Rosak</div>
                <div class="text-2xl font-black text-amber-600 mt-0.5">{{ $totalServis ?? 0 }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center font-bold text-base">
                <i class="fa-solid fa-wrench"></i>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs flex flex-col md:flex-row md:items-center justify-between gap-3">
        <!-- Status Filter Pills -->
        <div class="flex flex-wrap items-center gap-1.5 text-xs font-bold">
            <a href="{{ route('kenderaan.fleet') }}" class="px-3 py-1.5 rounded-xl transition {{ !request()->filled('status') ? 'bg-teal-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua
            </a>
            <a href="{{ route('kenderaan.fleet', ['status' => 'Sedia']) }}" class="px-3 py-1.5 rounded-xl transition {{ request('status') === 'Sedia' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Sedia
            </a>
            <a href="{{ route('kenderaan.fleet', ['status' => 'Sedang Digunakan']) }}" class="px-3 py-1.5 rounded-xl transition {{ request('status') === 'Sedang Digunakan' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Sedang Digunakan
            </a>
            <a href="{{ route('kenderaan.fleet', ['status' => 'Dalam Servis']) }}" class="px-3 py-1.5 rounded-xl transition {{ request('status') === 'Dalam Servis' ? 'bg-amber-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Dalam Servis
            </a>
            <a href="{{ route('kenderaan.fleet', ['status' => 'Rosak']) }}" class="px-3 py-1.5 rounded-xl transition {{ request('status') === 'Rosak' ? 'bg-rose-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Rosak
            </a>
        </div>

        <!-- Search input -->
        <form action="{{ route('kenderaan.fleet') }}" method="GET" class="flex items-center gap-2">
            @if(request()->filled('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="relative">
                <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no plat, model..." class="pl-8 pr-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none w-56">
            </div>
            <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition">Cari</button>
        </form>
    </div>

    <!-- Fleet Vehicles Grid & Table -->
    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Senarai Kenderaan Rasmi Jabatan</h3>
            <span class="text-xs text-slate-500">Jumlah: <b>{{ $kenderaanList->total() }}</b> unit kenderaan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-bold text-[11px]">
                        <th class="px-5 py-3.5">No. Pendaftaran</th>
                        <th class="px-4 py-3.5">Model & Jenis Kenderaan</th>
                        <th class="px-4 py-3.5">Kapasiti & Tahun</th>
                        <th class="px-4 py-3.5">Penempatan & Lokasi Kunci</th>
                        <th class="px-4 py-3.5">Odometer & Cukai Jalan</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($kenderaanList as $k)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5">
                                <div class="font-mono font-black text-sm text-teal-900">{{ $k->no_pendaftaran }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-slate-900 text-sm">{{ $k->model }}</div>
                                <div class="text-[11px] text-slate-500">{{ $k->jenis_kenderaan ?? $k->jenis ?? 'Kenderaan Jabatan' }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-slate-800">{{ $k->kapasiti_penumpang }} Orang Penumpang</div>
                                <div class="text-[11px] text-slate-500">Tahun: {{ $k->tahun_buatan ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-slate-800">{{ $k->jajahan_penempatan }}</div>
                                <div class="text-[10px] text-slate-500">{{ $k->lokasi_kunci ?? 'Papan Kunci Pentadbiran' }}</div>
                            </td>
                            <td class="px-4 py-3.5 font-mono">
                                <div class="font-bold text-slate-800">{{ number_format($k->odometer_semasa_km ?? 0) }} km</div>
                                @if($k->tarikh_tamat_cukai_jalan)
                                    <div class="text-[10px] text-slate-500">Cukai: {{ $k->tarikh_tamat_cukai_jalan->format('d/m/Y') }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $k->status === 'Sedia' ? 'bg-emerald-100 text-emerald-800' : ($k->status === 'Sedang Digunakan' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ $k->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1 whitespace-nowrap">
                                @if(Auth::user()->isStaff())
                                    <!-- Butang Kemaskini -->
                                    <button type="button" @click="
                                        editId = {{ $k->id }};
                                        editNoPendaftaran = '{{ $k->no_pendaftaran }}';
                                        editJenis = '{{ addslashes($k->jenis_kenderaan ?? 'Pacuan 4 Roda (4x4)') }}';
                                        editModel = '{{ addslashes($k->model) }}';
                                        editTahun = '{{ $k->tahun_buatan ?? date('Y') }}';
                                        editKapasiti = '{{ $k->kapasiti_penumpang ?? 4 }}';
                                        editJajahan = '{{ addslashes($k->jajahan_penempatan ?? 'Ibu Pejabat Kota Bharu') }}';
                                        editStatus = '{{ $k->status ?? 'Sedia' }}';
                                        editLokasiKunci = '{{ addslashes($k->lokasi_kunci ?? '') }}';
                                        editOdometer = '{{ $k->odometer_semasa_km ?? 0 }}';
                                        editTarikhCukai = '{{ $k->tarikh_tamat_cukai_jalan ? $k->tarikh_tamat_cukai_jalan->format('Y-m-d') : '' }}';
                                        editCatatan = '{{ addslashes($k->catatan ?? '') }}';
                                        modalEdit = true;
                                    " class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition text-xs" title="Kemaskini Maklumat Kenderaan">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </button>

                                    <!-- Butang Padam -->
                                    <form action="{{ route('kenderaan.destroyKenderaan', $k->id) }}" method="POST" class="inline" onsubmit="return confirm('Adakah anda pasti ingin memadam rekod kenderaan {{ addslashes($k->no_pendaftaran) }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-xl bg-slate-100 hover:bg-rose-100 text-slate-500 hover:text-rose-700 transition text-xs" title="Padam Rekod">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                Tiada rekod kenderaan ditemui. Sila klik butang <b>+ Tambah Kenderaan Baharu</b> untuk mendaftar kenderaan jabatan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($kenderaanList->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $kenderaanList->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL: Tambah Kenderaan Baharu -->
    <div x-show="modalTambah" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 max-w-lg w-full shadow-2xl text-xs space-y-4 max-h-[90vh] overflow-y-auto" @click.outside="modalTambah = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-plus-circle"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Tambah Maklumat Kenderaan Baharu</h3>
                        <p class="text-[11px] text-slate-500">Daftar rekod kenderaan operasi dan rasmi jabatan</p>
                    </div>
                </div>
                <button @click="modalTambah = false" class="text-slate-400 hover:text-slate-600 text-base">&times;</button>
            </div>

            <form action="{{ route('kenderaan.storeKenderaan') }}" method="POST" class="space-y-3">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">No. Pendaftaran (Plat) <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_pendaftaran" required placeholder="Contoh: DDX 8812" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none font-mono font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Jenis Kenderaan <span class="text-rose-500">*</span></label>
                        <select name="jenis_kenderaan" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none font-bold">
                            <option value="Pacuan 4 Roda (4x4)">Pacuan 4 Roda (4x4)</option>
                            <option value="Van">Van / Window Van</option>
                            <option value="Lori Sederhana">Lori Sederhana (Angkut Ternakan)</option>
                            <option value="Kereta Sedan">Kereta Sedan / MPV</option>
                            <option value="Motosikal">Motosikal Operasi</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Model & Varian <span class="text-rose-500">*</span></label>
                    <input type="text" name="model" required placeholder="Contoh: Toyota Hilux 2.4 Double Cab Automatic" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none font-medium">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Tahun Buatan</label>
                        <input type="number" name="tahun_buatan" value="{{ date('Y') }}" min="1990" max="{{ date('Y') + 1 }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Kapasiti (Orang) <span class="text-rose-500">*</span></label>
                        <input type="number" name="kapasiti_penumpang" value="4" min="1" max="50" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Status Kenderaan <span class="text-rose-500">*</span></label>
                        <select name="status" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none font-bold">
                            <option value="Sedia">Sedia</option>
                            <option value="Sedang Digunakan">Sedang Digunakan</option>
                            <option value="Dalam Servis">Dalam Servis</option>
                            <option value="Rosak">Rosak</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Jajahan Penempatan <span class="text-rose-500">*</span></label>
                        <select name="jajahan_penempatan" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none">
                            <option value="Ibu Pejabat Kota Bharu">Ibu Pejabat Kota Bharu</option>
                            <option value="Kota Bharu">Kota Bharu</option>
                            <option value="Pasir Mas">Pasir Mas</option>
                            <option value="Tumpat">Tumpat</option>
                            <option value="Bachok">Bachok</option>
                            <option value="Pasir Puteh">Pasir Puteh</option>
                            <option value="Machang">Machang</option>
                            <option value="Tanah Merah">Tanah Merah</option>
                            <option value="Jeli">Jeli</option>
                            <option value="Kuala Krai">Kuala Krai</option>
                            <option value="Gua Musang">Gua Musang</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Lokasi Simpanan Kunci</label>
                        <input type="text" name="lokasi_kunci" placeholder="Papan Kunci Pejabat Pentadbiran" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Odometer Semasa (km)</label>
                        <input type="number" name="odometer_semasa_km" value="0" min="0" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Luput Cukai Jalan</label>
                        <input type="date" name="tarikh_tamat_cukai_jalan" value="{{ date('Y-m-d', strtotime('+1 year')) }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Catatan Tambahan (Pilihan)</label>
                    <textarea name="catatan" rows="2" placeholder="Catatan kegunaan khas kenderaan..." class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none font-medium"></textarea>
                </div>

                <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                    <button type="button" @click="modalTambah = false" class="px-4 py-2.5 rounded-xl bg-slate-100 font-bold text-slate-600 hover:bg-slate-200 transition">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold shadow-md transition flex items-center gap-1.5">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Kenderaan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: Kemaskini Maklumat Kenderaan -->
    <div x-show="modalEdit" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 max-w-lg w-full shadow-2xl text-xs space-y-4 max-h-[90vh] overflow-y-auto" @click.outside="modalEdit = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Kemaskini Maklumat Kenderaan</h3>
                        <p class="text-[11px] text-slate-500 font-mono" x-text="editNoPendaftaran"></p>
                    </div>
                </div>
                <button @click="modalEdit = false" class="text-slate-400 hover:text-slate-600 text-base">&times;</button>
            </div>

            <form :action="'{{ url('/kenderaan/kenderaan') }}/' + editId" method="POST" class="space-y-3">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">No. Pendaftaran <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_pendaftaran" x-model="editNoPendaftaran" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-mono font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Jenis Kenderaan <span class="text-rose-500">*</span></label>
                        <select name="jenis_kenderaan" x-model="editJenis" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                            <option value="Pacuan 4 Roda (4x4)">Pacuan 4 Roda (4x4)</option>
                            <option value="Van">Van / Window Van</option>
                            <option value="Lori Sederhana">Lori Sederhana (Angkut Ternakan)</option>
                            <option value="Kereta Sedan">Kereta Sedan / MPV</option>
                            <option value="Motosikal">Motosikal Operasi</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Model & Varian <span class="text-rose-500">*</span></label>
                    <input type="text" name="model" x-model="editModel" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-medium">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Tahun Buatan</label>
                        <input type="number" name="tahun_buatan" x-model="editTahun" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Kapasiti (Orang) <span class="text-rose-500">*</span></label>
                        <input type="number" name="kapasiti_penumpang" x-model="editKapasiti" min="1" max="50" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Status Kenderaan <span class="text-rose-500">*</span></label>
                        <select name="status" x-model="editStatus" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                            <option value="Sedia">Sedia</option>
                            <option value="Sedang Digunakan">Sedang Digunakan</option>
                            <option value="Dalam Servis">Dalam Servis</option>
                            <option value="Rosak">Rosak</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Jajahan Penempatan <span class="text-rose-500">*</span></label>
                        <select name="jajahan_penempatan" x-model="editJajahan" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                            <option value="Ibu Pejabat Kota Bharu">Ibu Pejabat Kota Bharu</option>
                            <option value="Kota Bharu">Kota Bharu</option>
                            <option value="Pasir Mas">Pasir Mas</option>
                            <option value="Tumpat">Tumpat</option>
                            <option value="Bachok">Bachok</option>
                            <option value="Pasir Puteh">Pasir Puteh</option>
                            <option value="Machang">Machang</option>
                            <option value="Tanah Merah">Tanah Merah</option>
                            <option value="Jeli">Jeli</option>
                            <option value="Kuala Krai">Kuala Krai</option>
                            <option value="Gua Musang">Gua Musang</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Lokasi Simpanan Kunci</label>
                        <input type="text" name="lokasi_kunci" x-model="editLokasiKunci" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Odometer Semasa (km)</label>
                        <input type="number" name="odometer_semasa_km" x-model="editOdometer" min="0" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Luput Cukai Jalan</label>
                        <input type="date" name="tarikh_tamat_cukai_jalan" x-model="editTarikhCukai" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Catatan Tambahan</label>
                    <textarea name="catatan" x-model="editCatatan" rows="2" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium"></textarea>
                </div>

                <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                    <button type="button" @click="modalEdit = false" class="px-4 py-2.5 rounded-xl bg-slate-100 font-bold text-slate-600 hover:bg-slate-200 transition">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold shadow-md transition flex items-center gap-1.5">
                        <i class="fa-solid fa-check"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
