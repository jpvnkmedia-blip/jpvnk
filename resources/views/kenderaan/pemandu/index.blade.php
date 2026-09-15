@extends('layouts.app')

@section('title', 'Direktori & Pengurusan Pemandu Jabatan')
@section('page_title', 'Pengurusan Pemandu Kenderaan Rasmi JPVNK')

@section('content')
<div class="space-y-6" x-data="{ 
    modalTambah: false, 
    modalEdit: false,
    editId: null,
    editNama: '',
    editNoKp: '',
    editNoPekerja: '',
    editNoTel: '',
    editKelasLesen: '',
    editTarikhTamatLesen: '',
    editJajahan: '',
    editStatus: 'Aktif',
    editCatatan: ''
}">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('kenderaan.index') }}" class="p-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition text-xs" title="Kembali">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h2 class="text-xl font-extrabold text-slate-900">Direktori Pemandu Rasmi Jabatan</h2>
            </div>
            <p class="text-xs text-slate-500 mt-0.5 ml-7">Pengurusan profil pemandu rasmi, kelayakan kelas lesen memandu dan status penugasan kenderaan jabatan</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('kenderaan.index') }}" class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-2xs flex items-center gap-1.5">
                <i class="fa-solid fa-truck-pickup text-teal-600"></i>
                <span>Pengurusan Kenderaan</span>
            </a>
            @if(Auth::user()->isStaff())
                <button @click="modalTambah = true" class="px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-lg shadow-teal-700/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Tambah Maklumat Pemandu</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Stats Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Jumlah Pemandu</div>
                <div class="text-2xl font-black text-slate-900 mt-0.5">{{ $totalPemandu ?? 0 }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center font-bold text-base">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Pemandu Aktif</div>
                <div class="text-2xl font-black text-emerald-600 mt-0.5">{{ $totalAktif ?? 0 }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-base">
                <i class="fa-solid fa-id-card-clip"></i>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Sedang Bertugas</div>
                <div class="text-2xl font-black text-blue-600 mt-0.5">{{ $totalBertugas ?? 0 }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold text-base">
                <i class="fa-solid fa-steering-wheel fa-car"></i>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center justify-between">
            <div>
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Sedang Cuti</div>
                <div class="text-2xl font-black text-amber-600 mt-0.5">{{ $totalCuti ?? 0 }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center font-bold text-base">
                <i class="fa-solid fa-calendar-xmark"></i>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs flex flex-col md:flex-row md:items-center justify-between gap-3">
        <!-- Status Filter Pills -->
        <div class="flex flex-wrap items-center gap-1.5 text-xs font-bold">
            <a href="{{ route('kenderaan.pemandu.index') }}" class="px-3 py-1.5 rounded-xl transition {{ !request()->filled('status') ? 'bg-teal-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua
            </a>
            <a href="{{ route('kenderaan.pemandu.index', ['status' => 'Aktif']) }}" class="px-3 py-1.5 rounded-xl transition {{ request('status') === 'Aktif' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Aktif (Sedia)
            </a>
            <a href="{{ route('kenderaan.pemandu.index', ['status' => 'Bertugas']) }}" class="px-3 py-1.5 rounded-xl transition {{ request('status') === 'Bertugas' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Sedang Bertugas
            </a>
            <a href="{{ route('kenderaan.pemandu.index', ['status' => 'Cuti']) }}" class="px-3 py-1.5 rounded-xl transition {{ request('status') === 'Cuti' ? 'bg-amber-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Cuti
            </a>
        </div>

        <!-- Search input -->
        <form action="{{ route('kenderaan.pemandu.index') }}" method="GET" class="flex items-center gap-2">
            @if(request()->filled('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="relative">
                <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, no KP, telefon..." class="pl-8 pr-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none w-56">
            </div>
            <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-xs font-bold transition">Cari</button>
        </form>
    </div>

    <!-- Drivers Table & Directory -->
    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Senarai Pemandu Berdaftar Jabatan</h3>
            <span class="text-xs text-slate-500">Jumlah: <b>{{ $pemanduList->total() }}</b> orang pemandu</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-bold text-[11px]">
                        <th class="px-5 py-3.5">Nama & No. Pekerja</th>
                        <th class="px-4 py-3.5">No. Kad Pengenalan</th>
                        <th class="px-4 py-3.5">No. Telefon & Perhubungan</th>
                        <th class="px-4 py-3.5">Kelas Lesen Memandu</th>
                        <th class="px-4 py-3.5">Penempatan Jajahan</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pemanduList as $p)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-900 text-sm">{{ $p->nama }}</div>
                                <div class="font-mono text-[11px] text-teal-700">{{ $p->no_pekerja ?? 'VET-DVR-'.sprintf('%03d', $p->id) }}</div>
                            </td>
                            <td class="px-4 py-3.5 font-mono text-slate-700">
                                {{ $p->no_kp }}
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-slate-900">{{ $p->no_telefon }}</div>
                                <a href="https://wa.me/6{{ preg_replace('/[^0-9]/', '', $p->no_telefon) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-emerald-600 hover:text-emerald-800 font-semibold mt-0.5">
                                    <i class="fa-brands fa-whatsapp"></i> Hubungi WhatsApp
                                </a>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 font-mono">
                                    <i class="fa-solid fa-id-card mr-1 text-[10px]"></i> {{ $p->kelas_lesen }}
                                </span>
                                @if($p->tarikh_tamat_lesen)
                                    <div class="text-[10px] text-slate-400 mt-0.5">Luput: {{ $p->tarikh_tamat_lesen->format('d/m/Y') }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-slate-700 font-medium">
                                {{ $p->jajahan_penempatan }}
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $p->status === 'Aktif' ? 'bg-emerald-100 text-emerald-800' : ($p->status === 'Bertugas' ? 'bg-blue-100 text-blue-800' : ($p->status === 'Cuti' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700')) }}">
                                    {{ $p->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1 whitespace-nowrap">
                                @if(Auth::user()->isStaff())
                                    <!-- Butang Kemaskini -->
                                    <button type="button" @click="
                                        editId = {{ $p->id }};
                                        editNama = '{{ addslashes($p->nama) }}';
                                        editNoKp = '{{ $p->no_kp }}';
                                        editNoPekerja = '{{ $p->no_pekerja }}';
                                        editNoTel = '{{ $p->no_telefon }}';
                                        editKelasLesen = '{{ $p->kelas_lesen }}';
                                        editTarikhTamatLesen = '{{ $p->tarikh_tamat_lesen ? $p->tarikh_tamat_lesen->format('Y-m-d') : '' }}';
                                        editJajahan = '{{ addslashes($p->jajahan_penempatan) }}';
                                        editStatus = '{{ $p->status }}';
                                        editCatatan = '{{ addslashes($p->catatan ?? '') }}';
                                        modalEdit = true;
                                    " class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition text-xs" title="Kemaskini Maklumat Pemandu">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </button>

                                    <!-- Butang Padam -->
                                    <form action="{{ route('kenderaan.pemandu.destroy', $p->id) }}" method="POST" class="inline" onsubmit="return confirm('Adakah anda pasti ingin memadam rekod pemandu {{ addslashes($p->nama) }}?');">
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
                                Tiada maklumat pemandu ditemui. Sila klik butang <b>+ Tambah Maklumat Pemandu</b> di atas untuk mendaftar pemandu rasmi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pemanduList->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $pemanduList->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL: Tambah Pemandu Baharu -->
    <div x-show="modalTambah" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 max-w-lg w-full shadow-2xl text-xs space-y-4 max-h-[90vh] overflow-y-auto" @click.outside="modalTambah = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Tambah Maklumat Pemandu Baharu</h3>
                        <p class="text-[11px] text-slate-500">Daftar maklumat profil dan kelayakan lesen pemandu kenderaan jabatan</p>
                    </div>
                </div>
                <button @click="modalTambah = false" class="text-slate-400 hover:text-slate-600 text-base">&times;</button>
            </div>

            <form action="{{ route('kenderaan.pemandu.store') }}" method="POST" class="space-y-3">
                @csrf

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Nama Penuh Pemandu <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama" required placeholder="Contoh: En. Wan Kamaruddin bin Wan Noh" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none font-medium">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">No. Kad Pengenalan <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_kp" required placeholder="Contoh: 850101035511" maxlength="14" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">No. Pekerja / Gred (Pilihan)</label>
                        <input type="text" name="no_pekerja" placeholder="Contoh: VET-DVR-005 / H11" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">No. Telefon Bimbit <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_telefon" required placeholder="Contoh: 019-9876543" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none font-medium">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Kelas Lesen Memandu <span class="text-rose-500">*</span></label>
                        <select name="kelas_lesen" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none font-bold">
                            <option value="D, GDL">D, GDL (Kereta, 4x4, Van & Lori Ringan)</option>
                            <option value="D, DA">D, DA (Kereta & Van Sahaja)</option>
                            <option value="E, GDL">E, GDL (Lori Berat & Angkut Ternakan)</option>
                            <option value="D, E, GDL, PSV">D, E, GDL, PSV (Semua Kelas Kenderaan)</option>
                            <option value="B2, D">B2, D (Motosikal & Kereta)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Luput Lesen (Pilihan)</label>
                        <input type="date" name="tarikh_tamat_lesen" value="{{ date('Y-m-d', strtotime('+1 year')) }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>
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
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Status Pemandu <span class="text-rose-500">*</span></label>
                    <select name="status" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none font-bold">
                        <option value="Aktif">Aktif (Sedia Ditugaskan)</option>
                        <option value="Bertugas">Sedang Bertugas</option>
                        <option value="Cuti">Sedang Bercuti</option>
                        <option value="Tidak Aktif">Tidak Aktif</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Catatan Tambahan (Pilihan)</label>
                    <textarea name="catatan" rows="2" placeholder="Catatan kelayakan khas atau pengalaman operasi lapangan..." class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-teal-500 focus:outline-none font-medium"></textarea>
                </div>

                <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                    <button type="button" @click="modalTambah = false" class="px-4 py-2.5 rounded-xl bg-slate-100 font-bold text-slate-600 hover:bg-slate-200 transition">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold shadow-md transition flex items-center gap-1.5">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Maklumat Pemandu
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: Kemaskini Maklumat Pemandu -->
    <div x-show="modalEdit" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 max-w-lg w-full shadow-2xl text-xs space-y-4 max-h-[90vh] overflow-y-auto" @click.outside="modalEdit = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Kemaskini Maklumat Pemandu</h3>
                        <p class="text-[11px] text-slate-500 font-mono" x-text="editNama"></p>
                    </div>
                </div>
                <button @click="modalEdit = false" class="text-slate-400 hover:text-slate-600 text-base">&times;</button>
            </div>

            <form :action="'{{ url('/kenderaan/pemandu') }}/' + editId" method="POST" class="space-y-3">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Nama Penuh Pemandu <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama" x-model="editNama" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-medium">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">No. Kad Pengenalan <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_kp" x-model="editNoKp" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">No. Pekerja / Gred</label>
                        <input type="text" name="no_pekerja" x-model="editNoPekerja" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">No. Telefon Bimbit <span class="text-rose-500">*</span></label>
                        <input type="text" name="no_telefon" x-model="editNoTel" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-medium">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Kelas Lesen Memandu <span class="text-rose-500">*</span></label>
                        <input type="text" name="kelas_lesen" x-model="editKelasLesen" required placeholder="Contoh: D, GDL" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Tarikh Luput Lesen</label>
                        <input type="date" name="tarikh_tamat_lesen" x-model="editTarikhTamatLesen" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>
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
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Status Pemandu <span class="text-rose-500">*</span></label>
                    <select name="status" x-model="editStatus" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold">
                        <option value="Aktif">Aktif (Sedia Ditugaskan)</option>
                        <option value="Bertugas">Sedang Bertugas</option>
                        <option value="Cuti">Sedang Bercuti</option>
                        <option value="Tidak Aktif">Tidak Aktif</option>
                    </select>
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
