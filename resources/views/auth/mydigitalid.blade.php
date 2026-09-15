<!DOCTYPE html>
<html lang="ms" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyDigital ID Pengesahan - JPVNK</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-veterinar.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="h-full flex items-center justify-center p-4 bg-gradient-to-br from-slate-950 via-blue-950 to-slate-900">
    <div class="max-w-md w-full bg-white rounded-3xl p-8 shadow-2xl text-center border border-blue-100">
        
        <!-- Header Logos -->
        <div class="flex items-center justify-center gap-3 mb-4">
            <img src="{{ asset('images/logo-veterinar.png') }}" alt="Logo Veterinar" class="w-12 h-12 object-contain bg-slate-50 p-1 rounded-xl border border-slate-200">
            <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center text-2xl shadow-md shadow-blue-500/30">
                <i class="fa-solid fa-id-card"></i>
            </div>
        </div>
        <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-bold uppercase tracking-wider mb-2">
            <i class="fa-solid fa-circle-check text-blue-600"></i> MyDigital ID Nasional
        </div>
        <h2 class="text-xl font-extrabold text-slate-900">Pengesahan Identiti Digital</h2>
        <p class="text-xs text-slate-500 mt-1 mb-6">Imbas kod QR menggunakan aplikasi MyDigital ID atau masukkan No. Kad Pengenalan anda.</p>

        <!-- Simulated QR Code Card -->
        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 mb-6 flex flex-col items-center">
            <div class="w-36 h-36 bg-white border-2 border-dashed border-blue-400 rounded-xl flex items-center justify-center relative overflow-hidden shadow-inner p-2">
                <i class="fa-solid fa-qrcode text-8xl text-slate-800"></i>
                <div class="absolute inset-x-0 h-0.5 bg-blue-500 animate-pulse"></div>
            </div>
            <p class="text-[11px] text-blue-600 font-semibold mt-2.5 flex items-center gap-1">
                <i class="fa-solid fa-mobile-screen"></i> Buka Aplikasi MyDigital ID di telefon & imbas
            </p>
        </div>

        <form action="{{ route('auth.mydigitalid.verify') }}" method="POST" class="space-y-4 text-left">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">No. Kad Pengenalan (MyKad)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                        <i class="fa-solid fa-id-badge text-sm"></i>
                    </span>
                    <input type="text" name="ic_number" value="900729035411" required placeholder="Contoh: 900729035411" class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none font-mono font-bold">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Pemegang MyDigital ID</label>
                <input type="text" name="name" value="Mohd Rizal bin Kamaruddin" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Peranan Sistem</label>
                <select name="role" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="penternak">Penternak Ruminan</option>
                    <option value="usahawan">Usahawan Unggas EPU</option>
                    <option value="orang_awam">Orang Awam</option>
                </select>
            </div>

            <button type="submit" class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-blue-600/30 transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-shield-halved"></i>
                <span>Sahkan Pengenalan & Log Masuk</span>
            </button>
        </form>

        <div class="mt-6">
            <a href="{{ route('login') }}" class="text-xs text-slate-500 hover:text-slate-800">&larr; Kembali ke halaman log masuk</a>
        </div>
    </div>
</body>
</html>
