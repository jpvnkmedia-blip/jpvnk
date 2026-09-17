<!DOCTYPE html>
<html lang="ms" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Veterinar Bersepadu') - JPVNK</title>
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
                    },
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                            950: '#052e16',
                        },
                        jpvnk: {
                            gold: '#d97706',
                            maroon: '#991b1b',
                            navy: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
        [x-cloak] { display: none !important; }
        
        @media print {
            .no-print {
                display: none !important;
            }
            .print-only {
                display: block !important;
            }
            body {
                background: white !important;
                color: black !important;
                font-size: 12pt !important;
            }
            .card-print {
                box-shadow: none !important;
                border: 1px solid #000 !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="h-full font-sans antialiased text-slate-800" x-data="{ sidebarOpen: false, roleMenuOpen: false }">
    <div class="min-h-full flex">
        
        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden no-print" @click="sidebarOpen = false"></div>

        <!-- Sidebar Navigation -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-white transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 flex flex-col no-print shadow-xl">
            
            <!-- App Logo / Header -->
            <div class="h-20 flex items-center px-6 bg-slate-950 border-b border-slate-800">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                    <img src="{{ asset('images/logo-veterinar.png') }}" alt="Logo Jabatan Perkhidmatan Veterinar" class="w-11 h-11 object-contain bg-white/95 p-1 rounded-xl shadow-lg border border-slate-700/50 group-hover:scale-105 transition-transform">
                    <div>
                        <h1 class="text-base font-bold tracking-tight text-white flex items-center gap-1.5">
                            JPVNK <span class="text-xs bg-emerald-500/20 text-emerald-400 px-1.5 py-0.5 rounded font-mono">v2.0</span>
                        </h1>
                        <p class="text-xs text-slate-400">Sistem Veterinar Bersepadu</p>
                    </div>
                </a>
            </div>

            <!-- Current User Badge (Clickable to Profile) -->
            <a href="{{ route('profile.show') }}" class="block p-4 mx-3 my-3 rounded-xl bg-slate-800/80 hover:bg-slate-800 border border-slate-700/60 hover:border-emerald-500/40 transition group">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-emerald-400 font-bold group-hover:scale-105 transition-transform">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white truncate group-hover:text-emerald-300 transition-colors">{{ Auth::user()->name ?? 'Pengguna' }}</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-900/60 text-emerald-300 border border-emerald-700/50">
                            {{ Auth::user()->role_label ?? 'Awam' }}
                        </span>
                    </div>
                    <i class="fa-solid fa-chevron-right text-slate-500 group-hover:text-emerald-400 text-xs transition-colors"></i>
                </div>
            </a>

            <!-- Navigation Links -->
            <nav class="flex-1 overflow-y-auto px-3 py-2 space-y-1.5 text-sm">
                
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium transition {{ request()->routeIs('dashboard') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-900/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-gauge-high w-5 text-center text-base"></i>
                    <span>Papan Pemuka</span>
                </a>

                <!-- Profil Pengguna -->
                <a href="{{ route('profile.show') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium transition {{ request()->routeIs('profile.*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-900/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-circle-user w-5 text-center text-base text-emerald-400"></i>
                    <span>Profil Saya</span>
                </a>

                <!-- Pusat Notifikasi Aktiviti -->
                <a href="{{ route('notifications.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium transition {{ request()->routeIs('notifications.*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-900/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-bell w-5 text-center text-base text-amber-400"></i>
                        <span>Notifikasi Aktiviti</span>
                    </div>
                    @php
                        $sidebarUnreadCount = Auth::user()->unreadNotificationsCount();
                    @endphp
                    @if($sidebarUnreadCount > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-600 text-white shadow-xs">
                            {{ $sidebarUnreadCount > 99 ? '99+' : $sidebarUnreadCount }}
                        </span>
                    @endif
                </a>

                @if(Auth::user()->role !== 'admin_pejabat')
                <div class="pt-3 pb-1 px-3.5 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">
                    Perkhidmatan Veterinar
                </div>

                @if(Auth::user()->canAccessEptr())
                <!-- 1. EPTR Ruminan -->
                <div x-data="{ open: {{ request()->routeIs('eptr.*') ? 'true' : 'false' }} }" class="space-y-1">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium transition {{ request()->routeIs('eptr.*') ? 'bg-slate-800 text-emerald-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-cow w-5 text-center text-base text-amber-400"></i>
                            <span>EPTR Ruminan</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-cloak class="pl-9 pr-2 py-1 space-y-1 text-xs">
                        <a href="{{ route('eptr.index') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('eptr.index') ? 'text-emerald-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            Senarai Ternakan (EPTR)
                        </a>
                        @if(Auth::user()->role !== 'orang_awam')
                        <a href="{{ route('eptr.penternak.index') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('eptr.penternak.*') ? 'text-emerald-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            <i class="fa-solid fa-users mr-1 text-sky-400"></i> Senarai Penternak
                        </a>
                        @endif
                        @if(!Auth::user()->isStaff() || Auth::user()->isSuperAdmin())
                        <a href="{{ route('eptr.create') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('eptr.create') ? 'text-emerald-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            Daftar Ternakan (Borang A)
                        </a>
                        @endif
                        <a href="{{ route('eptr.borang-b.index') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('eptr.borang-b.*') ? 'text-emerald-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            Pindah Milik (Borang B)
                        </a>
                        <a href="{{ route('eptr.borang-c.index') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('eptr.borang-c.*') ? 'text-emerald-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            Pembatalan / Kematian (Borang C)
                        </a>
                        <a href="{{ route('eptr.borang-d.index') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('eptr.borang-d.*') ? 'text-emerald-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            Permit Sembelih (Borang D)
                        </a>
                        <a href="{{ route('eptr.pemindahan.index') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('eptr.pemindahan.*') ? 'text-emerald-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            <i class="fa-solid fa-truck-moving mr-1 text-blue-400"></i> Pemindahan Ternakan
                        </a>
                        @if(!Auth::user()->isStaff() || Auth::user()->isSuperAdmin())
                        <a href="{{ route('eptr.daftar-anak') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('eptr.daftar-anak') ? 'text-emerald-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            <i class="fa-solid fa-baby mr-1 text-pink-400"></i> Daftar Anak Ternakan
                        </a>
                        @endif
                        <a href="{{ route('eptr.kesihatan.index') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('eptr.kesihatan.*') ? 'text-emerald-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            <i class="fa-solid fa-heart-pulse mr-1 text-teal-400"></i> Program Kesihatan Ternakan
                        </a>
                        <a href="{{ route('eptr.jadual-fi') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('eptr.jadual-fi') ? 'text-amber-400 bg-slate-800/60 font-semibold' : 'text-amber-300/80 hover:text-amber-300 hover:bg-slate-800/30' }}">
                            <i class="fa-solid fa-scale-balanced mr-1 text-amber-400"></i> Jadual Fi Bayaran (EPTR)
                        </a>
                    </div>
                </div>
                @endif

                @if(Auth::user()->canAccessPawah())
                <!-- 2. Program Pawah -->
                <div x-data="{ open: {{ request()->routeIs('pawah.*') ? 'true' : 'false' }} }" class="space-y-1">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium transition {{ request()->routeIs('pawah.*') ? 'bg-slate-800 text-emerald-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-handshake-angle w-5 text-center text-base text-emerald-400"></i>
                            <span>Program Pawah</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-cloak class="pl-9 pr-2 py-1 space-y-1 text-xs">
                        <a href="{{ route('pawah.index') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('pawah.index') ? 'text-emerald-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            Senarai Surat Perjanjian
                        </a>
                        @if(Auth::user()->isStaff())
                        <a href="{{ route('pawah.create') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('pawah.create') ? 'text-emerald-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            Daftar Perjanjian Lembu Pawah
                        </a>
                        @else
                        <a href="{{ route('pawah.create') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('pawah.create') ? 'text-emerald-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            Mohon Program Pawah
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                @if(Auth::user()->canAccessEpu())
                <!-- 3. EPU Ladang Unggas -->
                <div x-data="{ open: {{ request()->routeIs('epu.*') ? 'true' : 'false' }} }" class="space-y-1">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium transition {{ request()->routeIs('epu.*') ? 'bg-slate-800 text-emerald-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-feather-pointed w-5 text-center text-base text-amber-500"></i>
                            <span>EPU Unggas</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-cloak class="pl-9 pr-2 py-1 space-y-1 text-xs">
                        <a href="{{ route('epu.index') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('epu.index') ? 'text-emerald-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            Senarai Ladang Unggas
                        </a>
                        <a href="{{ route('epu.create') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('epu.create') ? 'text-emerald-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            Mohon Lesen (Borang A)
                        </a>
                    </div>
                </div>
                @endif

                <!-- 4. Kursus Ternakan -->
                @if(Auth::user()->canPublishCourse())
                <div x-data="{ open: {{ request()->routeIs('kursus.*') ? 'true' : 'false' }} }" class="space-y-1">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium transition {{ request()->routeIs('kursus.*') ? 'bg-slate-800 text-emerald-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-graduation-cap w-5 text-center text-base text-cyan-400"></i>
                            <span>Kursus Ternakan</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-cloak class="pl-9 pr-2 py-1 space-y-1 text-xs">
                        <a href="{{ route('kursus.index') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('kursus.index') ? 'text-emerald-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            Direktori Kursus
                        </a>
                        <a href="{{ route('kursus.pemohon.index') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('kursus.pemohon.*') ? 'text-emerald-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            Pengurusan Pemohon
                        </a>
                        <a href="{{ route('kursus.create') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('kursus.create') ? 'text-emerald-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            Terbitkan Kursus Baharu
                        </a>
                    </div>
                </div>
                @elseif(Auth::user()->canAccessKursus())
                <a href="{{ route('kursus.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium transition {{ request()->routeIs('kursus.*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-900/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-graduation-cap w-5 text-center text-base text-cyan-400"></i>
                    <span>Kursus Ternakan</span>
                </a>
                @endif

                @if(Auth::user()->canAccessKlinik())
                <!-- 5. Klinik Haiwan -->
                <div x-data="{ open: {{ request()->routeIs('klinik.*') ? 'true' : 'false' }} }" class="space-y-1">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium transition {{ request()->routeIs('klinik.*') ? 'bg-slate-800 text-rose-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-stethoscope w-5 text-center text-base text-rose-400"></i>
                            <span>Klinik Haiwan</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-cloak class="pl-9 pr-2 py-1 space-y-1 text-xs">
                        <a href="{{ route('klinik.index') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('klinik.index') ? 'text-rose-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            Senarai Temujanji
                        </a>
                        <a href="{{ route('klinik.create') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('klinik.create') ? 'text-rose-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            Tempah Temujanji
                        </a>
                    </div>
                </div>
                @endif

                @if(Auth::user()->canAccessStorUbat())
                <!-- 5b. Stor Ubat & Vaksin Veterinar (Admin Stor Ubat & Super Admin) -->
                <div x-data="{ open: {{ request()->routeIs('inventori.ubat.*') ? 'true' : 'false' }} }" class="space-y-1">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium transition {{ request()->routeIs('inventori.ubat.*') ? 'bg-slate-800 text-rose-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-pills w-5 text-center text-base text-rose-400"></i>
                            <span>Stor Ubat &amp; Farmasi</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-cloak class="pl-9 pr-2 py-1 space-y-1 text-xs">
                        <a href="{{ route('inventori.ubat.index') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('inventori.ubat.index') ? 'text-rose-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            Katalog Ubat &amp; Vaksin
                        </a>
                        <a href="{{ route('inventori.ubat.permohonan') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('inventori.ubat.permohonan') ? 'text-rose-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            Kelulusan Permohonan
                        </a>
                        <a href="{{ route('inventori.ubat.create') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('inventori.ubat.create') ? 'text-rose-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            Daftar Ubat Baharu
                        </a>
                    </div>
                </div>
                @endif
                @endif

                @if(Auth::user()->isStaff() && !in_array(Auth::user()->role, ['admin_program', 'admin_eptr', 'admin_epu', 'pegawai_pelesen', 'pegawai_verifikasi_epu']))
                <div class="pt-3 pb-1 px-3.5 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">
                    Pengurusan Pejabat &amp; Bekalan
                </div>

                <!-- Modul Permohonan Bekalan Staf -->
                <div x-data="{ open: {{ request()->routeIs('inventori.permohonan.*') ? 'true' : 'false' }} }" class="space-y-1">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium transition {{ request()->routeIs('inventori.permohonan.*') ? 'bg-slate-800 text-emerald-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-clipboard-list w-5 text-center text-base text-emerald-400"></i>
                            <span>Permohonan Stor Staf</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-cloak class="pl-9 pr-2 py-1 space-y-1 text-xs">
                        <a href="{{ route('inventori.permohonan.saya') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('inventori.permohonan.saya') ? 'text-emerald-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            Permohonan Saya
                        </a>
                        <a href="{{ route('inventori.permohonan.pejabat.mohon') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('inventori.permohonan.pejabat.mohon') ? 'text-emerald-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            Mohon Alatan Pejabat
                        </a>
                        @if(Auth::user()->canRequestUbat())
                        <a href="{{ route('inventori.permohonan.ubat.mohon') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('inventori.permohonan.ubat.mohon') ? 'text-rose-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            <i class="fa-solid fa-pills mr-1 text-rose-400"></i> Mohon Ubat / Vaksin (Jajahan)
                        </a>
                        @endif
                    </div>
                </div>

                @if(Auth::user()->canAccessStorPejabat())
                <!-- 6. Stor Peralatan Pejabat (Admin Pejabat & Super Admin) -->
                <div x-data="{ open: {{ request()->routeIs('inventori.pejabat.*') ? 'true' : 'false' }} }" class="space-y-1">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium transition {{ request()->routeIs('inventori.pejabat.*') ? 'bg-slate-800 text-indigo-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-boxes-stacked w-5 text-center text-base text-indigo-400"></i>
                            <span>Stor Pejabat</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-cloak class="pl-9 pr-2 py-1 space-y-1 text-xs">
                        <a href="{{ route('inventori.pejabat.index') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('inventori.pejabat.index') ? 'text-indigo-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            Katalog Stor Pejabat
                        </a>
                        <a href="{{ route('inventori.pejabat.permohonan') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('inventori.pejabat.permohonan') ? 'text-indigo-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            Kelulusan Permohonan
                        </a>
                        <a href="{{ route('inventori.pejabat.create') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('inventori.pejabat.create') ? 'text-indigo-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            Daftar Barangan Baharu
                        </a>
                    </div>
                </div>
                @endif

                @if(Auth::user()->canAccessKenderaan())
                <!-- 7. Permohonan Kenderaan & Pemandu -->
                <div x-data="{ open: {{ request()->routeIs('kenderaan.*') ? 'true' : 'false' }} }" class="space-y-1">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium transition {{ request()->routeIs('kenderaan.*') ? 'bg-slate-800 text-teal-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-truck-pickup w-5 text-center text-base text-teal-400"></i>
                            <span>Kenderaan Rasmi</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-cloak class="pl-9 pr-2 py-1 space-y-1 text-xs">
                        <a href="{{ route('kenderaan.index') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('kenderaan.index') || request()->routeIs('kenderaan.show') ? 'text-teal-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            Tempahan Kenderaan
                        </a>
                        @if(Auth::user()->canManageKenderaanFleet())
                        <a href="{{ route('kenderaan.fleet') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('kenderaan.fleet') ? 'text-teal-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            Fleet Kenderaan
                        </a>
                        <a href="{{ route('kenderaan.pemandu.index') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('kenderaan.pemandu.*') ? 'text-teal-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            Maklumat Pemandu
                        </a>
                        @endif
                        @if(Auth::user()->canBookVehicle())
                        <a href="{{ route('kenderaan.create') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('kenderaan.create') ? 'text-teal-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            Permohonan Baru
                        </a>
                        @endif
                    </div>
                </div>
                @endif
                @endif

                @if(Auth::user()->isSuperAdmin())
                <div class="pt-3 pb-1 px-3.5 text-[11px] font-semibold tracking-wider text-amber-400 uppercase flex items-center gap-1.5">
                    <i class="fa-solid fa-crown text-amber-400"></i> Pentadbiran Sistem
                </div>

                <!-- 8. Pengurusan Pengguna (Super Admin) -->
                <div x-data="{ open: {{ request()->routeIs('users.*') ? 'true' : 'false' }} }" class="space-y-1">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium transition {{ request()->routeIs('users.*') ? 'bg-slate-800 text-amber-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-users-gear w-5 text-center text-base text-amber-400"></i>
                            <span>Pengurusan Pengguna</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-cloak class="pl-9 pr-2 py-1 space-y-1 text-xs">
                        <a href="{{ route('users.index') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('users.index') ? 'text-amber-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            Senarai Pengguna
                        </a>
                        <a href="{{ route('users.create') }}" class="block px-3 py-2 rounded-lg {{ request()->routeIs('users.create') ? 'text-amber-400 bg-slate-800/60 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/30' }}">
                            <i class="fa-solid fa-user-plus mr-1 text-emerald-400"></i> Tambah Pengguna Baru
                        </a>
                    </div>
                </div>
                @endif
            </nav>

            <!-- Role Switcher Shortcut Footer (Demo / Testing Helper) -->
            <div class="p-3 bg-slate-950 border-t border-slate-800">
                <form action="{{ route('auth.switch-role') }}" method="POST" class="space-y-1.5">
                    @csrf
                    <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider flex items-center justify-between">
                        <span><i class="fa-solid fa-shuffle mr-1 text-emerald-400"></i> Tukar Peranan Demo</span>
                    </label>
                    <select name="user_id" onchange="this.form.submit()" class="w-full bg-slate-900 border border-slate-700 text-white text-xs rounded-lg px-2.5 py-2 focus:ring-2 focus:ring-emerald-500 focus:outline-none cursor-pointer">
                        @php
                            $allDemoUsers = \App\Models\User::orderBy('id')->get();
                        @endphp
                        @foreach($allDemoUsers as $u)
                            <option value="{{ $u->id }}" {{ Auth::id() === $u->id ? 'selected' : '' }}>
                                @if($u->role === 'super_admin') 👑
                                @elseif($u->role === 'admin_eptr') 🐄
                                @elseif($u->role === 'admin_jajahan' || $u->role === 'admin_eptr_jajahan') 🏛️
                                @elseif($u->role === 'admin_program') 🤝
                                @elseif($u->role === 'admin_epu') 🐔
                                @elseif($u->role === 'admin_kursus') 🎓
                                @elseif($u->role === 'admin_klinik') 🩺
                                @elseif($u->role === 'admin_pejabat') 🏢
                                @elseif($u->role === 'admin_ubat') 💊
                                @elseif($u->role === 'staf') 🧑‍💼
                                @elseif($u->role === 'penternak') 🌾
                                @elseif($u->role === 'usahawan') 💼
                                @else 👤
                                @endif
                                {{ $u->role_label }} - {{ $u->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-100/70">
            
            <!-- Topbar Navigation -->
            <header class="h-20 bg-white border-b border-slate-200 px-4 sm:px-6 lg:px-8 flex items-center justify-between sticky top-0 z-30 shadow-xs no-print">
                <div class="flex items-center space-x-4">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                    <div>
                        <h2 class="text-lg sm:text-xl font-bold text-slate-800">@yield('page_title', 'Jabatan Perkhidmatan Veterinar Negeri Kelantan')</h2>
                        <p class="text-xs text-slate-500 hidden sm:block">Sistem Pengurusan Sepunya: EPTR • Pawah • EPU • Kursus • Klinik • Inventori • Kenderaan</p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <!-- Provider Badge -->
                    <span class="hidden md:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ Auth::user()->auth_provider === 'google' ? 'bg-red-50 text-red-700 border border-red-200' : (Auth::user()->auth_provider === 'mydigital_id' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-700 border border-slate-200') }}">
                        @if(Auth::user()->auth_provider === 'google')
                            <i class="fa-brands fa-google text-red-500"></i> Google Account
                        @elseif(Auth::user()->auth_provider === 'mydigital_id')
                            <i class="fa-solid fa-id-card text-blue-600"></i> MyDigital ID
                        @else
                            <i class="fa-solid fa-envelope text-slate-500"></i> Manual ID
                        @endif
                    </span>

                    <!-- Notification Bell Dropdown -->
                    <div x-data="{
                        notifOpen: false,
                        unreadCount: {{ Auth::user()->unreadNotificationsCount() }},
                        notifications: [],
                        loading: false,
                        async fetchNotifications() {
                            this.loading = true;
                            try {
                                const res = await fetch('{{ route('notifications.feed') }}');
                                const data = await res.json();
                                this.unreadCount = data.unread_count;
                                this.notifications = data.notifications;
                            } catch(e) {}
                            this.loading = false;
                        },
                        async markAllRead() {
                            try {
                                const res = await fetch('{{ route('notifications.mark-all-read') }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json',
                                    }
                                });
                                const data = await res.json();
                                if (data.success) {
                                    this.unreadCount = 0;
                                    this.notifications.forEach(n => n.is_read = true);
                                }
                            } catch(e) {}
                        },
                        async markRead(id, url) {
                            try {
                                await fetch(`/notifikasi/${id}/baca`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json',
                                    }
                                });
                                this.unreadCount = Math.max(0, this.unreadCount - 1);
                                const item = this.notifications.find(n => n.id === id);
                                if (item) item.is_read = true;
                                if (url) window.location.href = url;
                            } catch(e) {
                                if (url) window.location.href = url;
                            }
                        }
                    }" @click.away="notifOpen = false" class="relative">
                        <!-- Bell Button -->
                        <button @click="notifOpen = !notifOpen; if(notifOpen) fetchNotifications();"
                                title="Notifikasi Aktiviti"
                                class="relative p-2.5 rounded-xl border border-slate-200 text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 transition flex items-center justify-center">
                            <i class="fa-solid fa-bell text-base"></i>
                            <template x-if="unreadCount > 0">
                                <span class="absolute -top-1 -right-1 min-w-[20px] h-5 px-1 bg-rose-600 text-white font-extrabold text-[10px] rounded-full flex items-center justify-center shadow-xs animate-pulse"
                                      x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
                            </template>
                        </button>

                        <!-- Dropdown Panel -->
                        <div x-show="notifOpen" x-cloak x-transition
                             class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-200 py-2 z-50 overflow-hidden">
                            <!-- Header -->
                            <div class="px-4 py-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-bell text-emerald-600 text-sm"></i>
                                    <span class="font-bold text-xs text-slate-800 uppercase tracking-wider">Notifikasi Aktiviti</span>
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold" x-text="unreadCount + ' Baru'"></span>
                                </div>
                                <template x-if="unreadCount > 0">
                                    <button @click="markAllRead()" class="text-[11px] text-emerald-700 hover:text-emerald-900 font-bold hover:underline">
                                        Tanda Semua Dibaca
                                    </button>
                                </template>
                            </div>

                            <!-- Notification List -->
                            <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
                                <template x-if="loading">
                                    <div class="p-6 text-center text-xs text-slate-400">
                                        <i class="fa-solid fa-circle-notch fa-spin text-emerald-600 text-lg mb-1 block"></i>
                                        Memuatkan notifikasi...
                                    </div>
                                </template>

                                <template x-if="!loading && notifications.length === 0">
                                    <div class="p-6 text-center text-xs text-slate-400">
                                        <i class="fa-regular fa-bell-slash text-2xl text-slate-300 mb-2 block"></i>
                                        Tiada notifikasi aktiviti terkini.
                                    </div>
                                </template>

                                <template x-for="item in notifications" :key="item.id">
                                    <div @click="markRead(item.id, item.action_url)"
                                         :class="!item.is_read ? 'bg-emerald-50/40 hover:bg-emerald-50/70 font-medium' : 'bg-white hover:bg-slate-50'"
                                         class="p-3.5 transition cursor-pointer flex items-start gap-3">
                                        <div :class="{
                                            'bg-emerald-100 text-emerald-700 border-emerald-200': item.color === 'emerald',
                                            'bg-amber-100 text-amber-700 border-amber-200': item.color === 'amber',
                                            'bg-blue-100 text-blue-700 border-blue-200': item.color === 'blue',
                                            'bg-rose-100 text-rose-700 border-rose-200': item.color === 'rose',
                                            'bg-indigo-100 text-indigo-700 border-indigo-200': item.color === 'indigo',
                                            'bg-teal-100 text-teal-700 border-teal-200': item.color === 'teal',
                                            'bg-purple-100 text-purple-700 border-purple-200': item.color === 'purple'
                                        }" class="w-8 h-8 rounded-xl border flex items-center justify-center shrink-0 mt-0.5 text-xs">
                                            <i :class="item.icon"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-1">
                                                <h4 class="text-xs font-bold text-slate-900 truncate" x-text="item.title"></h4>
                                                <span class="text-[10px] text-slate-400 whitespace-nowrap" x-text="item.time_ago"></span>
                                            </div>
                                            <p class="text-[11px] text-slate-600 line-clamp-2 mt-0.5 leading-snug" x-text="item.message"></p>
                                        </div>
                                        <template x-if="!item.is_read">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0 mt-2"></span>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            <!-- Footer -->
                            <div class="p-2.5 bg-slate-50 border-t border-slate-100 text-center">
                                <a href="{{ route('notifications.index') }}" class="block py-1.5 text-xs font-bold text-emerald-700 hover:text-emerald-800 transition">
                                    <span>Lihat Semua di Pusat Notifikasi</span> &rarr;
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Button -->
                    <a href="{{ route('profile.show') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold {{ request()->routeIs('profile.*') ? 'bg-emerald-50 text-emerald-800 border border-emerald-300' : 'text-slate-700 hover:text-emerald-700 hover:bg-emerald-50 border border-slate-200' }} transition">
                        <i class="fa-solid fa-circle-user text-sm text-emerald-600"></i>
                        <span class="hidden sm:inline">Profil Saya</span>
                    </a>

                    <!-- Logout Button -->
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:text-rose-600 hover:bg-rose-50 border border-slate-200 transition">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            <span class="hidden sm:inline">Log Keluar</span>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Alerts / Flash Messages -->
            <div class="px-4 sm:px-6 lg:px-8 pt-4 no-print">
                @if(session('success'))
                    <div class="p-4 mb-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3 shadow-xs">
                        <i class="fa-solid fa-circle-check text-emerald-600 mt-0.5 text-lg"></i>
                        <div class="flex-1 text-sm font-medium">
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="p-4 mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3 shadow-xs">
                        <i class="fa-solid fa-circle-exclamation text-rose-600 mt-0.5 text-lg"></i>
                        <div class="flex-1 text-sm font-medium">
                            {{ session('error') }}
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-4 mb-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 shadow-xs">
                        <div class="flex items-center gap-2 font-semibold text-sm mb-1">
                            <i class="fa-solid fa-triangle-exclamation text-amber-600"></i> Sila semak ralat berikut:
                        </div>
                        <ul class="list-disc pl-6 text-xs space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <!-- Page Body -->
            <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-slate-200 px-6 py-4 text-center text-xs text-slate-500 no-print">
                <p>&copy; 2026 Jabatan Perkhidmatan Veterinar Negeri Kelantan (JPVNK). Hak Cipta Terpelihara.</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Sistem Veterinar Bersepadu • Enakmen EPTR 2024 &bull; Enakmen EPU 2005 &bull; Skim Pawah &bull; Pangkalan Data Sepunya</p>
            </footer>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
