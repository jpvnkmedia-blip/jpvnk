@extends('layouts.app')

@section('title', 'Tambah Pengguna Baharu - Super Admin')
@section('page_title', 'Pendaftaran Pengguna Baharu')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    selectedRoles: {{ json_encode(old('roles', old('role') ? [old('role')] : [])) }},
    selectedJajahan: '{{ old('jajahan', 'Kota Bharu') }}',
    status: '{{ old('status', 'Aktif') }}',
    icNumber: '{{ old('ic_number', '') }}',
    toggleRole(role) {
        if (this.selectedRoles.includes(role)) {
            this.selectedRoles = this.selectedRoles.filter(r => r !== role);
        } else {
            this.selectedRoles.push(role);
        }
    },
    hasRole(role) {
        return this.selectedRoles.includes(role);
    },
    resetRoles() {
        this.selectedRoles = [];
    },
    get defaultPasswordPreview() {
        const clean = (this.icNumber || '').replace(/[^0-9]/g, '');
        const last4 = clean.length >= 4 ? clean.slice(-4) : (clean.length > 0 ? clean.padStart(4, '0') : 'XXXX');
        return 'super@DVS' + last4;
    }
}">

    <!-- Header Card -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-amber-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex items-center justify-between border border-slate-700">
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/20 border border-amber-500/40 text-amber-300 text-xs font-bold uppercase tracking-wider mb-2">
                <i class="fa-solid fa-crown text-amber-400"></i> Akses Super Admin
            </span>
            <h2 class="text-2xl font-black">Daftar Pengguna Baharu</h2>
            <p class="text-xs text-slate-300 mt-1">Cipta akaun untuk kakitangan pentadbir, pegawai jajahan, penternak, usahawan, atau orang awam.</p>
        </div>
        <div class="hidden sm:block text-right">
            <a href="{{ route('users.index') }}" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white text-xs font-bold transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Senarai
            </a>
        </div>
    </div>

    <!-- Registration Form -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs">
        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 text-xs">
            @csrf

            <!-- Section 1: Maklumat Profil & Log Masuk -->
            <div>
                <div class="flex items-center gap-2 pb-3 mb-4 border-b border-slate-100 text-slate-800 font-bold text-sm">
                    <span class="w-6 h-6 rounded-full bg-amber-100 text-amber-900 flex items-center justify-center text-xs font-black">1</span>
                    <span>Maklumat Peribadi &amp; Akaun</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Nama Penuh <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Nik Ahmad bin Nik Daud" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Alamat Emel <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="Contoh: pegawai@veterinar.kelantan.gov.my" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Jawatan <span class="text-rose-500">*</span></label>
                        <input type="text" name="jawatan" value="{{ old('jawatan') }}" required placeholder="Contoh: Pegawai Veterinar / Penolong Pegawai" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Bahagian / Unit / Pejabat JPV Jajahan <span class="text-rose-500">*</span></label>
                        <input type="text" name="bahagian_unit" value="{{ old('bahagian_unit') }}" required placeholder="Contoh: Bahagian Pembangunan Ternakan / JPV Kota Bharu" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">No. Kad Pengenalan <span class="text-rose-500">*</span></label>
                        <input type="text" name="ic_number" x-model="icNumber" value="{{ old('ic_number') }}" required placeholder="Contoh: 850101035544" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none font-mono">
                        <p class="text-[10px] text-slate-400 mt-1">Tanpa sengkang (-) &bull; Digunakan untuk log masuk &amp; jana kata laluan default.</p>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">No. Telefon <span class="text-rose-500">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="Contoh: 0199887766" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Jajahan Bertugas / Kediaman <span class="text-rose-500">*</span></label>
                        <select name="jajahan" x-model="selectedJajahan" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none font-bold">
                            @foreach($jajahanList as $j)
                                <option value="{{ $j }}">{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block font-bold text-slate-700 uppercase mb-1">Alamat Penuh <span class="text-rose-500">*</span></label>
                    <textarea name="address" rows="2" required placeholder="No. Rumah, Jalan, Kampung / Alamat Pejabat JPVNK Jajahan" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">{{ old('address') }}</textarea>
                </div>
            </div>

            <!-- Section 2: Pemilihan Peranan (Role) & Kebenaran Akses -->
            <div class="pt-4 border-t border-slate-100">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 mb-4 border-b border-slate-100 gap-2">
                    <div class="flex items-center gap-2 text-slate-800 font-bold text-sm">
                        <span class="w-6 h-6 rounded-full bg-amber-100 text-amber-900 flex items-center justify-center text-xs font-black">2</span>
                        <span>Pemilihan Peranan (Role) &amp; Akses Sistem</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-amber-100 text-amber-900 border border-amber-300">
                            <span x-text="selectedRoles.length">0</span> Peranan Dipilih
                        </span>
                        <button type="button" @click="resetRoles()" class="text-[10px] text-slate-500 hover:text-slate-800 underline">
                            Set Semula
                        </button>
                    </div>
                </div>

                <div class="mb-3 p-3 bg-amber-50/60 rounded-2xl border border-amber-200/80 text-amber-900 text-xs flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-users-gear text-amber-600 text-sm"></i>
                        <span><b>Sokongan Berbilang Peranan:</b> Anda boleh memilih <b>lebih daripada 1 peranan</b> untuk membolehkan pengguna/pegawai menguruskan beberapa modul sistem serentak.</span>
                    </div>
                </div>

                <div class="space-y-4">
                    @foreach($roleDefinitions as $groupName => $roles)
                        <div>
                            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2 flex items-center gap-1.5">
                                <i class="fa-solid fa-folder-tree text-amber-500"></i>
                                <span>{{ $groupName }}</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($roles as $rKey => $rMeta)
                                    <label class="relative flex items-start p-3.5 rounded-2xl border cursor-pointer transition select-none"
                                           :class="hasRole('{{ $rKey }}') ? 'bg-amber-50/80 border-amber-400 ring-2 ring-amber-400/50 shadow-xs' : 'bg-slate-50/50 border-slate-200 hover:bg-slate-50'">
                                        <input type="checkbox" name="roles[]" value="{{ $rKey }}" :checked="hasRole('{{ $rKey }}')" @change="toggleRole('{{ $rKey }}')" class="mt-0.5 rounded text-amber-600 focus:ring-amber-500">
                                        <div class="ml-3 min-w-0 flex-1">
                                            <div class="flex items-center gap-1.5">
                                                <i class="fa-solid {{ $rMeta['icon'] }} text-xs"></i>
                                                <span class="font-bold text-slate-900 text-xs">{{ $rMeta['label'] }}</span>
                                            </div>
                                            <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">{{ $rMeta['desc'] }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Info Box for Penternak Role -->
                <div x-show="hasRole('penternak')" x-transition class="mt-4 p-3 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-3 text-emerald-900 text-xs">
                    <div class="w-8 h-8 rounded-xl bg-emerald-700 text-white flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-cow text-xs"></i>
                    </div>
                    <div>
                        <b>Automasi Profil Pemunya:</b> Sistem akan mencipta profil <b>Pemunya Ternakan (EPTR)</b> secara automatik bagi pengguna ini untuk urusan pendaftaran ternakan dan kad kuning.
                    </div>
                </div>
            </div>

            <!-- Section 3: Kata Laluan & Status Akaun -->
            <div class="pt-4 border-t border-slate-100">
                <div class="flex items-center gap-2 pb-3 mb-4 border-b border-slate-100 text-slate-800 font-bold text-sm">
                    <span class="w-6 h-6 rounded-full bg-amber-100 text-amber-900 flex items-center justify-center text-xs font-black">3</span>
                    <span>Keselamatan &amp; Status Akaun</span>
                </div>

                <!-- Default Password Info Banner -->
                <div class="mb-4 p-4 bg-amber-50/90 rounded-2xl border border-amber-300 flex items-start gap-3 shadow-xs">
                    <div class="w-8 h-8 rounded-xl bg-amber-600 text-white flex items-center justify-center shrink-0 mt-0.5 shadow-xs">
                        <i class="fa-solid fa-key text-xs"></i>
                    </div>
                    <div class="text-xs">
                        <div class="font-bold text-amber-950 flex flex-wrap items-center gap-2">
                            <span>Kata Laluan Lalai Automatik Sistem:</span>
                            <span class="font-mono bg-white px-2.5 py-0.5 rounded-lg border border-amber-300 text-amber-900 font-black text-xs tracking-wider" x-text="defaultPasswordPreview">super@DVSXXXX</span>
                        </div>
                        <p class="text-amber-800 text-[11px] mt-1 leading-relaxed">
                            Format automatik: <b>super@DVS[4 angka belakang No. KP]</b>. Biarkan ruangan kata laluan di bawah kosong untuk menggunakan kata laluan lalai ini secara automatik.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">
                            <span>Kata Laluan Khusus</span>
                            <span class="text-[10px] text-slate-400 font-normal">(Pilihan)</span>
                        </label>
                        <input type="password" name="password" placeholder="Kosongkan untuk guna default" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">
                            <span>Sahkan Kata Laluan</span>
                            <span class="text-[10px] text-slate-400 font-normal">(Pilihan)</span>
                        </label>
                        <input type="password" name="password_confirmation" placeholder="Ulang kata laluan jika diisi" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Status Akaun <span class="text-rose-500">*</span></label>
                        <select name="status" x-model="status" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none font-bold">
                            <option value="Aktif">Aktif (Boleh Log Masuk)</option>
                            <option value="Tidak Aktif">Tidak Aktif (Digantung)</option>
                        </select>
                    </div>
                </div>

                <!-- Digital Signature Upload -->
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <label class="block font-bold text-slate-700 uppercase mb-1">
                        <span>Muat Naik Tandatangan Digital</span>
                        <span class="text-[10px] text-slate-400 font-normal">(Pilihan - Khusus untuk Pegawai Pelesen / Pengarah / Pegawai Pengesah)</span>
                    </label>
                    <div class="flex items-center gap-4">
                        <input type="file" name="signature" accept="image/png,image/jpeg,image/jpg,image/webp" class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-100 file:text-amber-800 hover:file:bg-amber-200 cursor-pointer">
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1">Format fail: PNG, JPG, JPEG, atau WEBP (Maksimum 2MB). Tandatangan ini akan dicetak secara automatik pada Borang A EPU di ruangan Pegawai Pelesen dan dokumen rasmi lain.</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 border-t border-slate-200 flex items-center justify-between">
                <a href="{{ route('users.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 font-bold text-slate-700 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black shadow-lg shadow-amber-900/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-user-check text-sm"></i>
                    <span>Daftar Pengguna Sekarang</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
