<!DOCTYPE html>
<html lang="ms" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Baru - Sistem Pengurusan Lesen Penternakan Unggas (e-Unggas) JPVNK</title>
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
</head>
<body class="h-full font-sans antialiased text-slate-800 flex flex-col justify-center py-10 sm:px-6 lg:px-8 bg-gradient-to-br from-slate-950 via-slate-900 to-amber-950">

    <div class="sm:mx-auto sm:w-full sm:max-w-2xl text-center px-4">
        <div class="inline-flex items-center justify-center mb-3 bg-white/95 p-2 rounded-2xl shadow-xl border border-slate-700/40">
            <img src="{{ asset('images/logo-veterinar.png') }}" alt="Logo JPVNK" class="w-14 h-14 object-contain">
        </div>
        <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight">
            Sistem Pengurusan Lesen Penternakan Unggas (e-Unggas)
        </h1>
        <p class="text-sm font-bold text-amber-400 mt-1 uppercase tracking-wider">
            PENDAFTARAN BARU
        </p>
    </div>

    <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-2xl px-4" x-data="{
        icNumber: '{{ old('ic_number', '') }}',
        showPass: false,
        showConfirmPass: false,
        captchaCode: '{{ $captcha ?? rand(10000, 99999) }}',
        refreshCaptcha() {
            this.captchaCode = Math.floor(10000 + Math.random() * 90000).toString();
        },
        get defaultPasswordPreview() {
            const clean = (this.icNumber || '').replace(/[^0-9]/g, '');
            const last4 = clean.length >= 4 ? clean.slice(-4) : (clean.length > 0 ? clean.padStart(4, '0') : 'XXXX');
            return 'super@DVS' + last4;
        }
    }">
        <div class="bg-white py-8 px-6 shadow-2xl rounded-3xl sm:px-10 border border-slate-100">
            
            @if($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium">
                    <div class="font-bold mb-1 flex items-center gap-1.5"><i class="fa-solid fa-triangle-exclamation"></i> Sila semak maklumat yang dimasukkan:</div>
                    <ul class="list-disc pl-5 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Syarikat Notice Box (Kuning) -->
            <div class="mb-6 p-3.5 bg-amber-50 border border-amber-200/80 rounded-2xl flex items-center gap-3 text-amber-900 text-xs">
                <i class="fa-solid fa-circle-info text-amber-600 text-base shrink-0"></i>
                <span>Sila isi <b>Nama Syarikat</b> dan <b>Nombor Pendaftaran Syarikat</b> jika permohonan melalui syarikat.</span>
            </div>

            <form action="{{ route('register') }}" method="POST" class="space-y-4 text-xs">
                @csrf

                <!-- Nama -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">
                        Nama <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Nama penuh pemohon" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>

                <!-- Kad Pengenalan -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1 flex items-center justify-between">
                        <span>Kad Pengenalan <span class="text-rose-500">*</span></span>
                        <span class="text-[11px] font-normal text-slate-400 lowercase">(contoh : 880102031234)</span>
                    </label>
                    <input type="text" name="ic_number" x-model="icNumber" value="{{ old('ic_number') }}" required placeholder="No. Kad Pengenalan tanpa tanda sempang" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none font-mono">
                </div>

                <!-- Syarikat Section -->
                <div class="p-4 bg-slate-50/80 border border-slate-200/80 rounded-2xl space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">Nama Syarikat</label>
                            <input type="text" name="nama_syarikat" value="{{ old('nama_syarikat') }}" placeholder="Contoh: Ladang Unggas Sdn Bhd" class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 uppercase mb-1">No Pendaftaran Syarikat</label>
                            <input type="text" name="no_ssm" value="{{ old('no_ssm') }}" placeholder="Contoh: 202301012345 (123456-X)" class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Bentuk Perniagaan</label>
                        <select name="bentuk_perniagaan" class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            <option value="">- Sila Pilih Bentuk Perniagaan -</option>
                            <option value="Milikan Tunggal" {{ old('bentuk_perniagaan') == 'Milikan Tunggal' ? 'selected' : '' }}>Milikan Tunggal (Sole Proprietorship)</option>
                            <option value="Perkongsian" {{ old('bentuk_perniagaan') == 'Perkongsian' ? 'selected' : '' }}>Perkongsian (Partnership)</option>
                            <option value="Sendirian Berhad" {{ old('bentuk_perniagaan') == 'Sendirian Berhad' ? 'selected' : '' }}>Sendirian Berhad (Sdn. Bhd.)</option>
                            <option value="Awam Berhad" {{ old('bentuk_perniagaan') == 'Awam Berhad' ? 'selected' : '' }}>Awam Berhad (Bhd.)</option>
                            <option value="Koperasi" {{ old('bentuk_perniagaan') == 'Koperasi' ? 'selected' : '' }}>Koperasi</option>
                            <option value="Perkongsian Liabiliti Terhad" {{ old('bentuk_perniagaan') == 'Perkongsian Liabiliti Terhad' ? 'selected' : '' }}>Perkongsian Liabiliti Terhad (PLT)</option>
                            <option value="Lain-Lain" {{ old('bentuk_perniagaan') == 'Lain-Lain' ? 'selected' : '' }}>Lain-Lain</option>
                        </select>
                    </div>
                </div>

                <!-- Alamat -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">
                        Alamat <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="address" rows="2" required placeholder="Alamat penuh surat-menyurat / kediaman" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">{{ old('address') }}</textarea>
                </div>

                <!-- Poskod & Negeri -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">
                            Poskod <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="poskod" value="{{ old('poskod', '15000') }}" required placeholder="Contoh: 15000" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">
                            Negeri <span class="text-rose-500">*</span>
                        </label>
                        <select name="negeri" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                            <option value="Kelantan" {{ old('negeri', 'Kelantan') == 'Kelantan' ? 'selected' : '' }}>Kelantan</option>
                            <option value="Terengganu" {{ old('negeri') == 'Terengganu' ? 'selected' : '' }}>Terengganu</option>
                            <option value="Pahang" {{ old('negeri') == 'Pahang' ? 'selected' : '' }}>Pahang</option>
                            <option value="Perak" {{ old('negeri') == 'Perak' ? 'selected' : '' }}>Perak</option>
                            <option value="Kedah" {{ old('negeri') == 'Kedah' ? 'selected' : '' }}>Kedah</option>
                            <option value="Pulau Pinang" {{ old('negeri') == 'Pulau Pinang' ? 'selected' : '' }}>Pulau Pinang</option>
                            <option value="Perlis" {{ old('negeri') == 'Perlis' ? 'selected' : '' }}>Perlis</option>
                            <option value="Selangor" {{ old('negeri') == 'Selangor' ? 'selected' : '' }}>Selangor</option>
                            <option value="Negeri Sembilan" {{ old('negeri') == 'Negeri Sembilan' ? 'selected' : '' }}>Negeri Sembilan</option>
                            <option value="Melaka" {{ old('negeri') == 'Melaka' ? 'selected' : '' }}>Melaka</option>
                            <option value="Johor" {{ old('negeri') == 'Johor' ? 'selected' : '' }}>Johor</option>
                            <option value="Sabah" {{ old('negeri') == 'Sabah' ? 'selected' : '' }}>Sabah</option>
                            <option value="Sarawak" {{ old('negeri') == 'Sarawak' ? 'selected' : '' }}>Sarawak</option>
                            <option value="W.P. Kuala Lumpur" {{ old('negeri') == 'W.P. Kuala Lumpur' ? 'selected' : '' }}>W.P. Kuala Lumpur</option>
                            <option value="W.P. Putrajaya" {{ old('negeri') == 'W.P. Putrajaya' ? 'selected' : '' }}>W.P. Putrajaya</option>
                            <option value="W.P. Labuan" {{ old('negeri') == 'W.P. Labuan' ? 'selected' : '' }}>W.P. Labuan</option>
                        </select>
                    </div>
                </div>

                <!-- No Telefon & No Fax -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">
                            No Telefon <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="Contoh: 019-9887766" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">
                            No Fax
                        </label>
                        <input type="text" name="fax" value="{{ old('fax') }}" placeholder="Contoh: 09-7441234" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none font-mono">
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">
                        Email <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="nama@email.com" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none">
                </div>

                <!-- Password Rules Yellow Box -->
                <div class="p-3.5 bg-amber-50 border border-amber-200/80 rounded-2xl flex items-start gap-3 text-amber-900 text-xs">
                    <i class="fa-solid fa-lock text-amber-600 text-base shrink-0 mt-0.5"></i>
                    <div>
                        <p class="font-medium">Kata laluan mestilah sekurang-kurangnya 12 aksara panjang dan mengandungi sekurang-kurangnya satu huruf besar, satu huruf kecil, satu nombor dan satu simbol.</p>
                        <div class="mt-2 pt-2 border-t border-amber-200/70 text-[11px] text-amber-950 flex flex-wrap items-center gap-1.5">
                            <span class="font-bold">Pilihan Kata Laluan Lalai:</span>
                            <span class="font-mono bg-white px-2 py-0.5 rounded border border-amber-300 font-bold" x-text="defaultPasswordPreview">super@DVSXXXX</span>
                            <span class="text-slate-500">(Boleh biarkan kosong untuk guna kata laluan lalai ini)</span>
                        </div>
                    </div>
                </div>

                <!-- Kata Laluan & Pengesahan Kata Laluan with [Lihat] Buttons -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">
                            Kata Laluan <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative flex items-center">
                            <input :type="showPass ? 'text' : 'password'" name="password" placeholder="Masukkan kata laluan" class="w-full pl-3.5 pr-20 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none font-mono">
                            <button type="button" @click="showPass = !showPass" class="absolute right-2 px-2.5 py-1 text-xs font-bold rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 transition">
                                <span x-text="showPass ? 'Sembunyi' : 'Lihat'">Lihat</span>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">
                            Pengesahan Kata Laluan <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative flex items-center">
                            <input :type="showConfirmPass ? 'text' : 'password'" name="password_confirmation" placeholder="Ulang kata laluan" class="w-full pl-3.5 pr-20 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-amber-500 focus:outline-none font-mono">
                            <button type="button" @click="showConfirmPass = !showConfirmPass" class="absolute right-2 px-2.5 py-1 text-xs font-bold rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 transition">
                                <span x-text="showConfirmPass ? 'Sembunyi' : 'Lihat'">Lihat</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Captcha Verification Box -->
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl">
                    <label class="block font-bold text-slate-700 uppercase mb-2">
                        Pengesahan Keselamatan (Captcha) <span class="text-rose-500">*</span>
                    </label>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                        <div class="flex items-center gap-2">
                            <!-- Captcha Visual Badge -->
                            <div class="px-5 py-2.5 bg-gradient-to-r from-slate-800 to-slate-900 text-white font-mono text-xl tracking-[0.35em] font-extrabold rounded-xl select-none border border-slate-700 shadow-inner flex items-center justify-center min-w-[130px] italic">
                                <span x-text="captchaCode">46255</span>
                            </div>
                            <button type="button" @click="refreshCaptcha()" title="Muat semula kod captcha" class="p-2.5 bg-white border border-slate-300 hover:bg-slate-100 rounded-xl text-slate-600 transition">
                                <i class="fa-solid fa-rotate"></i>
                            </button>
                        </div>
                        <div class="flex-1">
                            <input type="text" name="captcha" placeholder="Masukkan 5 angka di sebelah" class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:outline-none font-mono uppercase">
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-3">
                    <button type="submit" class="w-full py-3.5 px-4 bg-amber-600 hover:bg-amber-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-amber-600/30 transition duration-150 flex items-center justify-center gap-2 uppercase tracking-wide">
                        <i class="fa-solid fa-user-plus"></i>
                        <span>Daftar</span>
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-4 border-t border-slate-100 text-center text-xs text-slate-600">
                <span>Sudah mempunyai akaun?</span>
                <a href="{{ route('login') }}" class="font-bold text-amber-700 hover:underline ml-1">
                    Log Masuk di Sini &rarr;
                </a>
            </div>

        </div>
    </div>

</body>
</html>
