<!DOCTYPE html>
<html lang="ms" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Masuk dengan Google - JPVNK</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-veterinar.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="h-full flex items-center justify-center p-4 bg-slate-900">
    <div class="max-w-md w-full bg-white rounded-3xl p-8 shadow-2xl text-center border border-slate-100">
        <div class="flex items-center justify-center gap-3 mb-4">
            <img src="{{ asset('images/logo-veterinar.png') }}" alt="Logo Veterinar" class="w-12 h-12 object-contain bg-slate-50 p-1 rounded-xl border border-slate-200">
            <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 border border-red-100 flex items-center justify-center text-2xl">
                <i class="fa-brands fa-google"></i>
            </div>
        </div>
        <h2 class="text-xl font-bold text-slate-800">Log Masuk Google SSO</h2>
        <p class="text-xs text-slate-500 mt-1 mb-6">Pilih profil akaun Google untuk menyambung ke Sistem Veterinar JPVNK</p>

        <form action="{{ route('auth.google.callback') }}" method="POST" class="space-y-4 text-left">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Penuh</label>
                <input type="text" name="name" value="Penternak Google User" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-red-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Alamat Emel Google</label>
                <input type="email" name="email" value="penternak.google@gmail.com" required class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-red-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Peranan</label>
                <select name="role" class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-red-500 focus:outline-none">
                    <option value="penternak">Penternak Ruminan</option>
                    <option value="usahawan">Usahawan Unggas EPU</option>
                    <option value="orang_awam">Orang Awam</option>
                </select>
            </div>

            <button type="submit" class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-red-600/30 transition flex items-center justify-center gap-2">
                <i class="fa-brands fa-google"></i>
                <span>Sahkan & Sambung Akaun Google</span>
            </button>
        </form>

        <div class="mt-6">
            <a href="{{ route('login') }}" class="text-xs text-slate-500 hover:text-slate-800">&larr; Kembali ke halaman log masuk</a>
        </div>
    </div>
</body>
</html>
