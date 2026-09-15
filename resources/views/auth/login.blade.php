<!DOCTYPE html>
<html lang="ms" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Masuk - Sistem Veterinar Bersepadu JPVNK</title>
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
<body class="h-full font-sans antialiased text-slate-800 flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gradient-to-br from-slate-950 via-slate-900 to-emerald-950">

    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <div class="inline-flex items-center justify-center mb-4">
            <img src="{{ asset('images/logo-veterinar.png') }}" alt="Logo Jabatan Perkhidmatan Veterinar" class="w-20 h-20 object-contain drop-shadow-2xl bg-white/95 p-2 rounded-2xl border border-slate-700/40">
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
            Sistem Veterinar Bersepadu
        </h2>
        <p class="mt-2 text-sm text-emerald-300/90 font-medium">
            Jabatan Perkhidmatan Veterinar Negeri Kelantan
        </p>
        <p class="text-xs text-slate-400 mt-1">
            Pangkalan Data Sepunya • EPTR • Pawah • EPU • Kursus • Klinik • Inventori • Kenderaan
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-xl px-4" x-data="{ tab: 'manual' }">
        <div class="bg-white py-8 px-6 shadow-2xl rounded-3xl sm:px-10 border border-slate-100">
            
            @if(session('success'))
                <div class="mb-5 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium">
                    <div class="font-bold mb-1 flex items-center gap-1.5"><i class="fa-solid fa-triangle-exclamation"></i> Ralat Log Masuk:</div>
                    <ul class="list-disc pl-5 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Auth Method Selector Tabs -->
            <div class="flex rounded-2xl bg-slate-100 p-1.5 mb-6 text-xs font-semibold">
                <button @click="tab = 'manual'" :class="tab === 'manual' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900'" class="flex-1 py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-id-card text-emerald-600"></i>
                    <span>No. Kad Pengenalan</span>
                </button>
                <button @click="tab = 'sso'" :class="tab === 'sso' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900'" class="flex-1 py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-fingerprint text-blue-600"></i>
                    <span>MyDigital ID / Google</span>
                </button>
            </div>

            <!-- TAB 1: Manual Login Form -->
            <div x-show="tab === 'manual'" class="space-y-4">
                <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="ic_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">No. Kad Pengenalan</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                                <i class="fa-solid fa-id-card text-sm"></i>
                            </span>
                            <input id="ic_number" name="ic_number" type="text" autocomplete="username" required value="{{ old('ic_number', '800515035511') }}" placeholder="800515035511" class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none transition">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Masukkan 12 digit tanpa tanda sempang (-) atau alamat emel berdaftar.</p>
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kata Laluan</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                                <i class="fa-solid fa-lock text-sm"></i>
                            </span>
                            <input id="password" name="password" type="password" required value="password" class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none transition">
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <label class="flex items-center text-slate-600 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                            <span class="ml-2">Ingat saya pada peranti ini</span>
                        </label>
                        <a href="#" class="text-emerald-700 font-semibold hover:underline">Lupa kata laluan?</a>
                    </div>

                    <button type="submit" class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-emerald-700/30 transition duration-150 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <span>Log Masuk ke Sistem</span>
                    </button>
                </form>
            </div>

            <!-- TAB 2: SSO (Google & MyDigital ID) -->
            <div x-show="tab === 'sso'" x-cloak class="space-y-4">
                
                <!-- MyDigital ID Option -->
                <a href="{{ route('auth.mydigitalid') }}" class="w-full p-4 rounded-2xl border-2 border-blue-600 bg-blue-50/60 hover:bg-blue-100/70 transition flex items-center justify-between group">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center text-2xl shadow-md shadow-blue-500/30">
                            <i class="fa-solid fa-id-card"></i>
                        </div>
                        <div class="text-left">
                            <div class="text-sm font-bold text-blue-950 flex items-center gap-1.5">
                                MyDigital ID Nasional
                                <span class="bg-blue-600 text-white text-[10px] px-1.5 py-0.5 rounded font-bold uppercase">Rasmi</span>
                            </div>
                            <div class="text-xs text-blue-700">Log masuk selamat menggunakan Identiti Digital Malaysia</div>
                        </div>
                    </div>
                    <i class="fa-solid fa-arrow-right text-blue-600 group-hover:translate-x-1 transition-transform"></i>
                </a>

                <!-- Google Account Option -->
                <a href="{{ route('auth.google') }}" class="w-full p-4 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 transition flex items-center justify-between group shadow-xs">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 border border-red-100 flex items-center justify-center text-2xl">
                            <i class="fa-brands fa-google"></i>
                        </div>
                        <div class="text-left">
                            <div class="text-sm font-bold text-slate-900">Google Account</div>
                            <div class="text-xs text-slate-500">Log masuk menggunakan akaun Gmail / Google Workspace</div>
                        </div>
                    </div>
                    <i class="fa-solid fa-arrow-right text-slate-400 group-hover:text-red-600 group-hover:translate-x-1 transition"></i>
                </a>
            </div>

            <!-- Single Registration Link -->
            <div class="mt-6 pt-6 border-t border-slate-100 text-center text-xs text-slate-600">
                <span>Belum mempunyai akaun?</span>
                <a href="{{ route('register') }}" class="font-bold text-emerald-700 hover:underline ml-1">
                    Daftar Akaun Sekali Sahaja (Percuma) &rarr;
                </a>
            </div>

            <!-- Quick Demo Login Buttons -->
            <div class="mt-8 pt-6 border-t border-slate-200">
                <div class="text-center mb-3">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider bg-slate-100 px-3 py-1 rounded-full">
                        <i class="fa-solid fa-bolt text-amber-500 mr-1"></i> Log Masuk Pantas Ujian
                    </span>
                </div>
                
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-[11px]">
                    @foreach($demoUsers as $demoUser)
                        <form action="{{ route('auth.switch-role') }}" method="POST">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $demoUser->id }}">
                            <button type="submit" class="w-full text-left p-2 rounded-xl border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50/50 transition flex flex-col justify-between h-full bg-slate-50/70">
                                <span class="font-bold text-slate-800 truncate">{{ $demoUser->role_label }}</span>
                                <span class="text-[10px] text-slate-500 truncate">{{ $demoUser->name }}</span>
                                <span class="text-[9px] font-mono text-emerald-700 truncate"><i class="fa-solid fa-id-badge mr-0.5"></i> {{ $demoUser->ic_number }}</span>
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

</body>
</html>
