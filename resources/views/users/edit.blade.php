@extends('layouts.app')

@section('title', 'Kemaskini Pengguna - Super Admin')
@section('page_title', 'Kemaskini Maklumat Pengguna')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    selectedRole: '{{ old('role', $targetUser->role) }}',
    selectedJajahan: '{{ old('jajahan', $targetUser->jajahan ?? 'Kota Bharu') }}',
    status: '{{ old('status', $targetUser->status ?? 'Aktif') }}',
}">

    <!-- Header Card -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-amber-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex items-center justify-between border border-slate-700">
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/20 border border-amber-500/40 text-amber-300 text-xs font-bold uppercase tracking-wider mb-2">
                <i class="fa-solid fa-pen-to-square text-amber-400"></i> Modul Super Admin
            </span>
            <h2 class="text-2xl font-black">Kemaskini: {{ $targetUser->name }}</h2>
            <p class="text-xs text-slate-300 mt-1">Ubah peranan, maklumat peribadi, jajahan bertugas, atau tetapkan semula kata laluan.</p>
        </div>
        <div class="hidden sm:block text-right">
            <a href="{{ route('users.index') }}" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white text-xs font-bold transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Senarai
            </a>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-xs">
        <form action="{{ route('users.update', $targetUser->id) }}" method="POST" class="space-y-6 text-xs">
            @csrf
            @method('PUT')

            <!-- Section 1: Maklumat Profil & Log Masuk -->
            <div>
                <div class="flex items-center gap-2 pb-3 mb-4 border-b border-slate-100 text-slate-800 font-bold text-sm">
                    <span class="w-6 h-6 rounded-full bg-amber-100 text-amber-900 flex items-center justify-center text-xs font-black">1</span>
                    <span>Maklumat Peribadi &amp; Akaun</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Nama Penuh <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $targetUser->name) }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Alamat Emel <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $targetUser->email) }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">No. Kad Pengenalan <span class="text-rose-500">*</span></label>
                        <input type="text" name="ic_number" value="{{ old('ic_number', $targetUser->ic_number) }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">No. Telefon <span class="text-rose-500">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone', $targetUser->phone) }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
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
                    <textarea name="address" rows="2" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">{{ old('address', $targetUser->address) }}</textarea>
                </div>
            </div>

            <!-- Section 2: Pemilihan Peranan (Role) -->
            <div class="pt-4 border-t border-slate-100">
                <div class="flex items-center gap-2 pb-3 mb-4 border-b border-slate-100 text-slate-800 font-bold text-sm">
                    <span class="w-6 h-6 rounded-full bg-amber-100 text-amber-900 flex items-center justify-center text-xs font-black">2</span>
                    <span>Peranan (Role) &amp; Akses Sistem</span>
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
                                    <label class="relative flex items-start p-3.5 rounded-2xl border cursor-pointer transition"
                                           :class="selectedRole === '{{ $rKey }}' ? 'bg-amber-50/70 border-amber-400 ring-2 ring-amber-400/50 shadow-xs' : 'bg-slate-50/50 border-slate-200 hover:bg-slate-50'">
                                        <input type="radio" name="role" value="{{ $rKey }}" x-model="selectedRole" class="mt-0.5 text-amber-600 focus:ring-amber-500">
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
            </div>

            <!-- Section 3: Reset Kata Laluan (Pilihan) & Status Akaun -->
            <div class="pt-4 border-t border-slate-100">
                <div class="flex items-center gap-2 pb-3 mb-4 border-b border-slate-100 text-slate-800 font-bold text-sm">
                    <span class="w-6 h-6 rounded-full bg-amber-100 text-amber-900 flex items-center justify-center text-xs font-black">3</span>
                    <span>Tukar Kata Laluan &amp; Status Akaun</span>
                </div>

                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 mb-4 text-slate-600 text-xs">
                    <i class="fa-solid fa-circle-info text-amber-600 mr-1"></i> Biarkan ruangan kata laluan kosong sekiranya anda tidak mahu menukar kata laluan pengguna ini.
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Kata Laluan Baharu</label>
                        <input type="password" name="password" placeholder="Biarkan kosong jika tiada perubahan" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Sahkan Kata Laluan Baharu</label>
                        <input type="password" name="password_confirmation" placeholder="Ulang kata laluan baharu" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Status Akaun <span class="text-rose-500">*</span></label>
                        <select name="status" x-model="status" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none font-bold">
                            <option value="Aktif">Aktif (Boleh Log Masuk)</option>
                            <option value="Tidak Aktif">Tidak Aktif (Digantung)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 border-t border-slate-200 flex items-center justify-between">
                <a href="{{ route('users.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-50 font-bold text-slate-700 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black shadow-lg shadow-amber-900/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-save text-sm"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
