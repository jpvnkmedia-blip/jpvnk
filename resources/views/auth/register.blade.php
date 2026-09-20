<!DOCTYPE html>
<html lang="ms" class="min-h-screen bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Pendaftaran Pengguna Baharu - Sistem Veterinar Bersepadu JPVNK</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-veterinar.png') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        html, body {
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
        }
    </style>
</head>
<body class="min-h-screen font-sans antialiased text-slate-800 flex flex-col justify-start py-6 sm:py-12 px-3.5 sm:px-6 lg:px-8 bg-gradient-to-br from-slate-950 via-slate-900 to-emerald-950">

    <div class="sm:mx-auto sm:w-full sm:max-w-xl text-center px-2">
        <div class="inline-flex items-center justify-center mb-2.5">
            <img src="{{ asset('images/logo-veterinar.png') }}" alt="Logo JPVNK" class="w-14 h-14 sm:w-16 sm:h-16 object-contain drop-shadow-2xl bg-white/95 p-2 rounded-2xl border border-slate-700/40">
        </div>
        <h1 class="text-lg sm:text-2xl font-black text-white tracking-tight">
            Sistem Veterinar Bersepadu
        </h1>
        <p class="text-xs sm:text-sm text-emerald-300 font-semibold mt-0.5">
            Jabatan Perkhidmatan Veterinar Negeri Kelantan
        </p>
        <div class="inline-block mt-2 px-3 py-0.5 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-[11px] sm:text-xs font-bold uppercase tracking-wider">
            PENDAFTARAN BARU PENGGUNA (KALI PERTAMA)
        </div>
    </div>

    <div class="mt-4 sm:mt-6 sm:mx-auto sm:w-full sm:max-w-xl px-1 sm:px-4 pb-12" x-data="{
        icNumber: '{{ old('ic_number', '') }}',
        showPass: false,
        showConfirmPass: false,
        hasSyarikat: {{ old('nama_syarikat') || old('no_ssm') ? 'true' : 'false' }}
    }">
        <div class="bg-white py-6 px-4 shadow-2xl rounded-2xl sm:rounded-3xl sm:py-8 sm:px-10 border border-slate-100">
            
            @if($errors->any())
                <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium">
                    <div class="font-bold mb-1 flex items-center gap-1.5"><i class="fa-solid fa-triangle-exclamation"></i> Sila semak maklumat yang dimasukkan:</div>
                    <ul class="list-disc pl-5 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="space-y-4 text-xs">
                @csrf

                <!-- Nama Penuh -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">
                        Nama Penuh <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Ahmad bin Ismail" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>

                <!-- No. Kad Pengenalan -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1 flex items-center justify-between">
                        <span>No. Kad Pengenalan (MyKad) <span class="text-rose-500">*</span></span>
                        <span class="text-[11px] font-normal text-slate-400 lowercase">(tanpa tanda '-')</span>
                    </label>
                    <input type="text" name="ic_number" x-model="icNumber" value="{{ old('ic_number') }}" required placeholder="Contoh: 880102031234" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none font-mono">
                </div>

                <!-- Emel & No Telefon -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">
                            Alamat Emel <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="nama@email.com" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">
                            No. Telefon Bimbit <span class="text-rose-500">*</span>
                        </label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="Contoh: 019-1234567" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none font-mono">
                    </div>
                </div>

                <!-- Alamat Kediaman -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">
                        Alamat Kediaman / Surat-Menyurat <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="address" rows="2" required placeholder="No. rumah, nama jalan, kampung atau taman" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">{{ old('address') }}</textarea>
                </div>

                <!-- Jajahan, Poskod & Negeri -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">
                            Jajahan <span class="text-rose-500">*</span>
                        </label>
                        <select name="jajahan" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            @foreach(['Kota Bharu', 'Pasir Mas', 'Tumpat', 'Bachok', 'Pasir Puteh', 'Machang', 'Tanah Merah', 'Jeli', 'Kuala Krai', 'Gua Musang'] as $j)
                                <option value="{{ $j }}" {{ old('jajahan') === $j ? 'selected' : '' }}>{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Poskod</label>
                        <input type="text" name="poskod" value="{{ old('poskod', '15000') }}" placeholder="Poskod" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Negeri</label>
                        <input type="text" name="negeri" value="{{ old('negeri', 'Kelantan') }}" readonly class="w-full px-3.5 py-2.5 text-sm bg-slate-100 border border-slate-200 rounded-xl text-slate-600 focus:outline-none">
                    </div>
                </div>

                <!-- Kategori Pengguna -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">
                        Kategori Pengguna <span class="text-rose-500">*</span>
                    </label>
                    <select name="role" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none font-medium">
                        <option value="orang_awam" {{ old('role', 'orang_awam') === 'orang_awam' ? 'selected' : '' }}>Orang Awam</option>
                        <option value="penternak" {{ old('role') === 'penternak' ? 'selected' : '' }}>Penternak</option>
                        <option value="usahawan" {{ old('role') === 'usahawan' ? 'selected' : '' }}>Usahawan</option>
                    </select>
                </div>

                <!-- Optional: Maklumat Syarikat & Bentuk Perniagaan Toggle -->
                <div class="pt-1">
                    <button type="button" @click="hasSyarikat = !hasSyarikat" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 flex items-center gap-1.5 focus:outline-none">
                        <i class="fa-solid" :class="hasSyarikat ? 'fa-square-minus text-rose-500' : 'fa-square-plus text-emerald-600'"></i>
                        <span x-text="hasSyarikat ? 'Tutup Maklumat Syarikat & Perniagaan' : '+ Tambah Maklumat Syarikat / Perniagaan (Jika Ada)'"></span>
                    </button>

                    <div x-show="hasSyarikat" x-cloak class="mt-3 p-4 bg-slate-50 border border-slate-200 rounded-2xl space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-slate-700 uppercase text-[11px] mb-1">Nama Syarikat</label>
                                <input type="text" name="nama_syarikat" value="{{ old('nama_syarikat') }}" placeholder="Contoh: Maju Ternak Sdn Bhd" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 uppercase text-[11px] mb-1">No. Pendaftaran Syarikat / SSM</label>
                                <input type="text" name="no_ssm" value="{{ old('no_ssm') }}" placeholder="Contoh: 202301012345" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none font-mono">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold text-slate-700 uppercase text-[11px] mb-1">Bentuk Perniagaan</label>
                                <select name="bentuk_perniagaan" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                                    <option value="">Pilih Bentuk Perniagaan</option>
                                    <option value="Milikan Tunggal" {{ old('bentuk_perniagaan') === 'Milikan Tunggal' ? 'selected' : '' }}>Milikan Tunggal</option>
                                    <option value="Perkongsian" {{ old('bentuk_perniagaan') === 'Perkongsian' ? 'selected' : '' }}>Perkongsian</option>
                                    <option value="Sendirian Berhad" {{ old('bentuk_perniagaan') === 'Sendirian Berhad' ? 'selected' : '' }}>Sendirian Berhad</option>
                                    <option value="Koperasi" {{ old('bentuk_perniagaan') === 'Koperasi' ? 'selected' : '' }}>Koperasi</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 uppercase text-[11px] mb-1">No Fax (Pilihan)</label>
                                <input type="tel" name="fax" value="{{ old('fax') }}" placeholder="Contoh: 09-7412345" class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:outline-none font-mono">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kata Laluan -->
                <div class="pt-2 border-t border-slate-100">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">
                                Kata Laluan
                            </label>
                            <div class="relative">
                                <input :type="showPass ? 'text' : 'password'" name="password" placeholder="Masukkan kata laluan" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none pr-10">
                                <button type="button" @click="showPass = !showPass" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600 focus:outline-none">
                                    <i class="fa-solid" :class="showPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">
                                Sahkan Kata Laluan
                            </label>
                            <div class="relative">
                                <input :type="showConfirmPass ? 'text' : 'password'" name="password_confirmation" placeholder="Ulang kata laluan" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none pr-10">
                                <button type="button" @click="showConfirmPass = !showConfirmPass" class="absolute right-3 top-3 text-slate-400 hover:text-slate-600 focus:outline-none">
                                    <i class="fa-solid" :class="showConfirmPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pengesahan Keselamatan (Captcha) -->
                <div class="pt-2 border-t border-slate-100">
                    <label class="block font-bold text-slate-700 uppercase mb-1">
                        Pengesahan Keselamatan (Captcha)
                    </label>
                    <div class="flex items-center gap-3">
                        <div class="bg-slate-900 text-amber-400 font-mono font-black text-base px-4 py-2 rounded-xl tracking-widest select-none border border-slate-700">
                            {{ session('register_captcha', '89241') }}
                        </div>
                        <input type="text" name="captcha" value="{{ session('register_captcha', '89241') }}" placeholder="Masukkan kod" class="w-36 px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none font-mono">
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit" class="w-full py-3.5 px-4 rounded-xl text-white font-bold text-sm bg-emerald-600 hover:bg-emerald-700 shadow-lg shadow-emerald-600/30 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-user-plus"></i>
                        <span>Daftar Akaun Pengguna Baharu</span>
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center text-xs text-slate-500 pt-4 border-t border-slate-100">
                Sudah mempunyai akaun berdaftar?
                <a href="{{ route('login') }}" class="font-bold text-emerald-700 hover:text-emerald-800 hover:underline ml-1">
                    Log Masuk di Sini &rarr;
                </a>
            </div>

        </div>
    </div>

</body>
</html>
