<!DOCTYPE html>
<html lang="ms" class="min-h-screen bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
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
    <style>
        html, body {
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
        }
    </style>
</head>
<body class="min-h-screen font-sans antialiased text-slate-800 flex flex-col justify-start py-8 sm:py-12 px-3.5 sm:px-6 lg:px-8 bg-gradient-to-br from-slate-950 via-slate-900 to-emerald-950">

    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center px-2">
        <div class="inline-flex items-center justify-center mb-3">
            <img src="{{ asset('images/logo-veterinar.png') }}" alt="Logo Jabatan Perkhidmatan Veterinar" class="w-16 h-16 sm:w-20 sm:h-20 object-contain drop-shadow-2xl bg-white/95 p-2 rounded-2xl border border-slate-700/40">
        </div>
        <h2 class="text-xl sm:text-3xl font-extrabold text-white tracking-tight">
            Sistem Veterinar Bersepadu
        </h2>
        <p class="mt-1 sm:mt-2 text-xs sm:text-sm text-emerald-300/90 font-medium">
            Jabatan Perkhidmatan Veterinar Negeri Kelantan
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-xl px-4">
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

            <!-- Login Form -->
            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="ic_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">No. Kad Pengenalan / Emel</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <i class="fa-solid fa-id-card text-sm"></i>
                        </span>
                        <input id="ic_number" name="ic_number" type="text" autocomplete="username" required value="{{ old('ic_number') }}" placeholder="Contoh: 900729035413 atau emel" class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none transition font-mono">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Masukkan 12 digit tanpa tanda sempang (-) atau alamat emel berdaftar.</p>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kata Laluan</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </span>
                        <input id="password" name="password" type="password" required placeholder="Masukkan kata laluan anda" class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none transition">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                        <span class="ml-2">Ingat saya pada peranti ini</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-emerald-700/30 transition duration-150 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>Log Masuk ke Sistem</span>
                </button>
            </form>

            <!-- Single Registration Link -->
            <div class="mt-6 pt-6 border-t border-slate-100 text-center text-xs text-slate-600">
                <span>Belum mempunyai akaun?</span>
                <a href="{{ route('register') }}" class="font-bold text-emerald-700 hover:underline ml-1">
                    Daftar Akaun Sekali Sahaja (Percuma) &rarr;
                </a>
            </div>

        </div>
    </div>

</body>
</html>
