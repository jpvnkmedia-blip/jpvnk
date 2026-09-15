@extends('layouts.app')

@section('title', 'Profil Pengguna - ' . $user->name)
@section('page_title', 'Profil & Tetapan Akaun Pengguna')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Top Profile Banner Card -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-6 border border-slate-700 relative overflow-hidden">
        <div class="flex items-center gap-5 relative z-10">
            <!-- Large User Initial Avatar -->
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-3xl bg-emerald-500/20 border-2 border-emerald-400/40 text-emerald-300 flex items-center justify-center text-3xl font-black shadow-inner shrink-0">
                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                    <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs font-bold uppercase">
                        <i class="fa-solid fa-id-badge"></i> {{ $user->role_label }}
                    </span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $user->status === 'Aktif' ? 'bg-emerald-950/80 text-emerald-300 border border-emerald-700/50' : 'bg-rose-950/80 text-rose-300 border border-rose-700/50' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $user->status === 'Aktif' ? 'bg-emerald-400' : 'bg-rose-400' }}"></span>
                        {{ $user->status ?? 'Aktif' }}
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">{{ $user->name }}</h1>
                <div class="text-xs text-slate-300 flex flex-wrap items-center gap-x-4 gap-y-1 mt-1 font-medium">
                    <span><i class="fa-solid fa-id-card text-emerald-400 mr-1.5"></i>No. KP: <b class="font-mono text-white">{{ $user->ic_number ?? '-' }}</b></span>
                    <span>&bull;</span>
                    <span><i class="fa-solid fa-envelope text-emerald-400 mr-1.5"></i>{{ $user->email }}</span>
                    <span>&bull;</span>
                    <span><i class="fa-solid fa-location-dot text-emerald-400 mr-1.5"></i>{{ $user->jajahan ?? 'Kelantan' }}</span>
                </div>
            </div>
        </div>

        <!-- Quick Summary Box -->
        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/15 text-xs shrink-0 self-stretch md:self-auto flex flex-col justify-center space-y-1.5 relative z-10">
            <div class="flex justify-between items-center gap-4 text-slate-300">
                <span>Kaedah Log Masuk:</span>
                <span class="font-bold text-white capitalize flex items-center gap-1">
                    @if($user->auth_provider === 'google')
                        <i class="fa-brands fa-google text-red-400"></i> Google
                    @elseif($user->auth_provider === 'mydigital_id')
                        <i class="fa-solid fa-id-card text-blue-400"></i> MyDigital ID
                    @else
                        <i class="fa-solid fa-key text-amber-400"></i> Kata Laluan Manual
                    @endif
                </span>
            </div>
            <div class="flex justify-between items-center gap-4 text-slate-300">
                <span>Tarikh Mendaftar:</span>
                <span class="font-bold text-white">{{ $user->created_at ? $user->created_at->format('d/m/Y') : '-' }}</span>
            </div>
        </div>
    </div>

    <!-- Main Content Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column: Forms (Update Profile & Change Password) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Card 1: Kemaskini Maklumat Peribadi -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-xs">
                <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100">
                    <div class="flex items-center gap-2.5 text-slate-900 font-bold text-base">
                        <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm">
                            <i class="fa-solid fa-user-pen"></i>
                        </div>
                        <span>Kemaskini Maklumat Peribadi</span>
                    </div>
                </div>

                <form action="{{ route('profile.update') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">Nama Penuh <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">Alamat Emel <span class="text-rose-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">No. Telefon / WhatsApp <span class="text-rose-500">*</span></label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required placeholder="Contoh: 0199887766" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">Jajahan <span class="text-rose-500">*</span></label>
                            <select name="jajahan" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none font-bold">
                                @foreach($jajahanList as $j)
                                    <option value="{{ $j }}" {{ old('jajahan', $user->jajahan) === $j ? 'selected' : '' }}>{{ $j }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">No. Kad Pengenalan (Kekal / Sistem)</label>
                        <input type="text" value="{{ $user->ic_number ?? '-' }}" disabled class="w-full px-3.5 py-2.5 text-sm bg-slate-100 border border-slate-200 rounded-xl text-slate-500 font-mono cursor-not-allowed">
                        <p class="text-[10px] text-slate-400 mt-1">No. Kad Pengenalan adalah pengenal unik rasmi akaun anda.</p>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Alamat Penuh <span class="text-rose-500">*</span></label>
                        <textarea name="address" rows="3" required placeholder="No. Rumah, Jalan, Kampung, Poskod" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">{{ old('address', $user->address) }}</textarea>
                    </div>

                    <div class="pt-3 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold shadow-md shadow-emerald-900/20 transition flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Simpan Maklumat Profil</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Card 2: Tukar Kata Laluan -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-7 shadow-xs">
                <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100">
                    <div class="flex items-center gap-2.5 text-slate-900 font-bold text-base">
                        <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-sm">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <span>Keselamatan &amp; Tukar Kata Laluan</span>
                    </div>
                </div>

                <form action="{{ route('profile.password') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Kata Laluan Semasa <span class="text-rose-500">*</span></label>
                        <input type="password" name="current_password" required placeholder="Masukkan kata laluan semasa anda" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">Kata Laluan Baharu <span class="text-rose-500">*</span></label>
                            <input type="password" name="password" required placeholder="Minimum 6 aksara" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">Sahkan Kata Laluan Baharu <span class="text-rose-500">*</span></label>
                            <input type="password" name="password_confirmation" required placeholder="Ulang kata laluan baharu" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                        </div>
                    </div>

                    <div class="pt-3 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold shadow-md transition flex items-center gap-2">
                            <i class="fa-solid fa-key"></i>
                            <span>Kemaskini Kata Laluan</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <!-- Right Column: Account Stats & Module Access Badges -->
        <div class="space-y-6">

            <!-- Card: Maklumat Ringkasan Aktiviti Akaun -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-xs space-y-4">
                <div class="font-bold text-slate-900 text-sm pb-2 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-emerald-600"></i>
                    <span>Ringkasan Aktiviti Anda</span>
                </div>

                <div class="space-y-3 text-xs">
                    @if($user->pemunya)
                        <div class="p-3.5 bg-emerald-50 rounded-2xl border border-emerald-200 flex items-center justify-between">
                            <div>
                                <div class="font-bold text-emerald-950">Ternakan Ruminan (EPTR)</div>
                                <div class="text-[11px] text-emerald-700">Ternakan Berdaftar</div>
                            </div>
                            <span class="text-xl font-black text-emerald-900 font-mono">{{ $user->pemunya->ternakan->count() }}</span>
                        </div>
                    @endif

                    @if($user->pawahPerjanjian->count() > 0)
                        <div class="p-3.5 bg-teal-50 rounded-2xl border border-teal-200 flex items-center justify-between">
                            <div>
                                <div class="font-bold text-teal-950">Program Pawah</div>
                                <div class="text-[11px] text-teal-700">Surat Perjanjian</div>
                            </div>
                            <span class="text-xl font-black text-teal-900 font-mono">{{ $user->pawahPerjanjian->count() }}</span>
                        </div>
                    @endif

                    @if($user->ladangUnggas->count() > 0)
                        <div class="p-3.5 bg-amber-50 rounded-2xl border border-amber-200 flex items-center justify-between">
                            <div>
                                <div class="font-bold text-amber-950">Ladang Unggas (EPU)</div>
                                <div class="text-[11px] text-amber-700">Premis Berlesen</div>
                            </div>
                            <span class="text-xl font-black text-amber-900 font-mono">{{ $user->ladangUnggas->count() }}</span>
                        </div>
                    @endif

                    <div class="p-3.5 bg-cyan-50 rounded-2xl border border-cyan-200 flex items-center justify-between">
                        <div>
                            <div class="font-bold text-cyan-950">Kursus Ternakan</div>
                            <div class="text-[11px] text-cyan-700">Permohonan Latihan</div>
                        </div>
                        <span class="text-xl font-black text-cyan-900 font-mono">{{ $user->permohonanKursus->count() }}</span>
                    </div>

                    <div class="p-3.5 bg-rose-50 rounded-2xl border border-rose-200 flex items-center justify-between">
                        <div>
                            <div class="font-bold text-rose-950">Klinik Haiwan</div>
                            <div class="text-[11px] text-rose-700">Rekod Temujanji</div>
                        </div>
                        <span class="text-xl font-black text-rose-900 font-mono">{{ $user->temujanjiKlinik->count() }}</span>
                    </div>

                    @if($user->isStaff())
                        <div class="p-3.5 bg-indigo-50 rounded-2xl border border-indigo-200 flex items-center justify-between">
                            <div>
                                <div class="font-bold text-indigo-950">Permohonan Stor</div>
                                <div class="text-[11px] text-indigo-700">Alatan &amp; Bekalan Pejabat</div>
                            </div>
                            <span class="text-xl font-black text-indigo-900 font-mono">{{ $user->permohonanInventori->count() }}</span>
                        </div>

                        <div class="p-3.5 bg-teal-50 rounded-2xl border border-teal-200 flex items-center justify-between">
                            <div>
                                <div class="font-bold text-teal-950">Tempahan Kenderaan</div>
                                <div class="text-[11px] text-teal-700">Kenderaan Rasmi Jabatan</div>
                            </div>
                            <span class="text-xl font-black text-teal-900 font-mono">{{ $user->tempahanKenderaan->count() }}</span>
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
