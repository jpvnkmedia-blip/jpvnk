@extends('layouts.app')

@section('title', 'Laman Utama - Program Ladang Bridlot NAIMbif JPVNK')

@section('content')
<!-- Hero Section -->
<div class="relative bg-gradient-to-br from-slate-900 via-emerald-950 to-slate-900 text-white overflow-hidden py-16 sm:py-24">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#10b981_1px,transparent_1px)] [background-size:16px_16px]"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-full bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 text-xs font-semibold">
                    <i class="fas fa-certificate text-amber-400"></i>
                    <span>Inisiatif Transformasi Ternakan Negeri Kelantan</span>
                </div>

                <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-white leading-tight">
                    Program Ladang Bridlot <br class="hidden sm:inline">
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 via-teal-300 to-amber-300">NAIMbif Kelantan</span>
                </h1>

                <p class="text-base sm:text-lg text-slate-300 max-w-2xl leading-relaxed">
                    Sistem permohonan dalam talian rasmi Jabatan Perkhidmatan Veterinar Negeri Kelantan (JPVNK) untuk penyertaan ladang pembiakan lembu baka pedaging berkualiti tinggi melalui kaedah Bridlot moden.
                </p>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-4">
                    @if(!(Auth::check() && Auth::user()->isPurePengarah()))
                    <a href="{{ route('naimbif.public.apply') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 rounded-xl text-base font-bold text-slate-900 bg-gradient-to-r from-amber-400 to-amber-300 hover:from-amber-300 hover:to-amber-200 shadow-lg shadow-amber-400/20 transition-all transform hover:-translate-y-0.5">
                        <i class="fas fa-file-signature text-lg mr-2.5"></i> Mohon Penyertaan Sekarang
                    </a>
                    @endif
                    <a href="{{ route('naimbif.public.check_status') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 rounded-xl text-base font-bold text-white bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700 transition-all">
                        <i class="fas fa-search text-emerald-400 mr-2.5"></i> Semak Status Permohonan
                    </a>
                </div>

                <!-- Info Badges -->
                <div class="grid grid-cols-3 gap-4 pt-6 border-t border-slate-800 text-center sm:text-left">
                    <div>
                        <div class="text-2xl font-bold text-white">{{ $totalLulus }}+</div>
                        <div class="text-xs text-slate-400">Ladang Diluluskan</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-emerald-400">{{ $totalTernakan }}+</div>
                        <div class="text-xs text-slate-400">Populasi Baka Direkod</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-amber-400">{{ $jajahanCount }} Jajahan</div>
                        <div class="text-xs text-slate-400">Seluruh Negeri Kelantan</div>
                    </div>
                </div>
            </div>

            <!-- Visual Card -->
            <div class="lg:col-span-5">
                <div class="relative rounded-2xl bg-gradient-to-b from-slate-800 to-slate-900 p-6 border border-slate-700/80 shadow-2xl">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-700">
                        <div class="flex items-center space-x-3">
                            <img src="{{ asset('images/naimbif-logo.png') }}" alt="Logo NAIMbif" class="h-11 w-auto object-contain bg-white/10 p-1 rounded-xl">
                            <div>
                                <h3 class="text-sm font-bold text-white">Borang Permohonan Rasmi</h3>
                                <p class="text-xs text-slate-400">Program Ladang Bridlot JPVNK</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                            Percuma
                        </span>
                    </div>

                    <div class="space-y-4 py-4 text-xs text-slate-300">
                        <div class="flex items-start space-x-3">
                            <div class="w-5 h-5 rounded-full bg-emerald-900/60 text-emerald-400 flex items-center justify-center font-bold text-[10px] mt-0.5">1</div>
                            <div>
                                <strong class="text-white">Maklumat Peserta & Pengalaman:</strong> Pengisian profil penternak dan rekod latihan/kursus penternakan.
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="w-5 h-5 rounded-full bg-emerald-900/60 text-emerald-400 flex items-center justify-center font-bold text-[10px] mt-0.5">2</div>
                            <div>
                                <strong class="text-white">Maklumat Asas Ladang:</strong> Alamat ladang, koordinat GPS, status tanah & padang ragut.
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="w-5 h-5 rounded-full bg-emerald-900/60 text-emerald-400 flex items-center justify-center font-bold text-[10px] mt-0.5">3</div>
                            <div>
                                <strong class="text-white">Matriks Stok Baka Lembu:</strong> Charolais, Belgian Blue, Limousin, Kedah Kelantan & lain-lain.
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="w-5 h-5 rounded-full bg-emerald-900/60 text-emerald-400 flex items-center justify-center font-bold text-[10px] mt-0.5">4</div>
                            <div>
                                <strong class="text-white">Pengesahan & Tandatangan Digital:</strong> Pengakuan terus secara dalam talian.
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-700">
                        <a href="{{ route('naimbif.public.apply') }}" class="w-full flex items-center justify-center px-4 py-3 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 transition-colors shadow-md">
                            Isi Borang Permohonan Sekarang <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Features & Information Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center max-w-3xl mx-auto mb-16">
        <h2 class="text-xs font-bold tracking-widest text-emerald-700 uppercase mb-2">Panduan & Syarat Permohonan</h2>
        <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
            Bagaimana Proses Permohonan Ladang Bridlot Berfungsi?
        </p>
        <p class="text-slate-600 text-sm mt-3">
            Permohonan diproses mengikut standard prosedur Jabatan Perkhidmatan Veterinar Negeri Kelantan dari peringkat Jajahan sehingga Ibu Pejabat.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Step 1 -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow relative">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl font-bold mb-4">
                <i class="fas fa-edit"></i>
            </div>
            <span class="absolute top-6 right-6 text-xs font-bold text-slate-300">LANGKAH 01</span>
            <h3 class="text-base font-bold text-slate-800 mb-2">Isi Borang Online</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                Lengkapkan butiran diri, maklumat ladang, lokasi GPS serta bilangan stok baka ternakan semasa anda.
            </p>
        </div>

        <!-- Step 2 -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow relative">
            <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-xl font-bold mb-4">
                <i class="fas fa-qrcode"></i>
            </div>
            <span class="absolute top-6 right-6 text-xs font-bold text-slate-300">LANGKAH 02</span>
            <h3 class="text-base font-bold text-slate-800 mb-2">Dapatkan No. Rujukan</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                Sistem akan menjana No. Permohonan automatik (contoh: NB-2026-XXXX) berserta slip pengesahan ber-QR kod.
            </p>
        </div>

        <!-- Step 3 -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow relative">
            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl font-bold mb-4">
                <i class="fas fa-search-location"></i>
            </div>
            <span class="absolute top-6 right-6 text-xs font-bold text-slate-300">LANGKAH 03</span>
            <h3 class="text-base font-bold text-slate-800 mb-2">Siasatan Jajahan</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                Pegawai Veterinar Jajahan akan menyemak premis ternakan, padang ragut, dan mengesahkan status kelengkapan.
            </p>
        </div>

        <!-- Step 4 -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow relative">
            <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-xl font-bold mb-4">
                <i class="fas fa-award"></i>
            </div>
            <span class="absolute top-6 right-6 text-xs font-bold text-slate-300">LANGKAH 04</span>
            <h3 class="text-base font-bold text-slate-800 mb-2">Keputusan Jabatan</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                Ibu Pejabat JPVNK mengeluarkan kelulusan rasmi untuk bimbingan pembiakan baka lembu pedaging bermutu tinggi.
            </p>
        </div>
    </div>

    <!-- Cattle Breeds Focus Section -->
    <div class="mt-20 bg-gradient-to-r from-emerald-900 to-teal-900 rounded-3xl p-8 sm:p-12 text-white shadow-xl">
        <div class="max-w-3xl">
            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-700/60 text-emerald-200 uppercase tracking-wider">
                Fokus Baka Pedaging Program NAIMbif
            </span>
            <h2 class="text-2xl sm:text-4xl font-extrabold mt-4 mb-4">
                Baka Lembu Pedaging Berkualiti Tinggi
            </h2>
            <p class="text-slate-300 text-sm leading-relaxed mb-8">
                Program NAIMbif memberi penekanan kepada peningkatan genetik lembu pedaging melalui kaedah Permanian Beradas (AI) dan baka terpilih seperti:
            </p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @php
                $breeds = [
                    ['name' => 'Charolais', 'desc' => 'Tumbesaran pantas & daging lembut'],
                    ['name' => 'Belgian Blue', 'desc' => 'Otot ganda & peratus karkas tinggi'],
                    ['name' => "Blonde D'Aquitaine", 'desc' => 'Struktur rangka tegap & mudah lahir'],
                    ['name' => 'Limousin', 'desc' => 'Kualiti daging premium'],
                    ['name' => 'Kedah Kelantan', 'desc' => 'Daya tahan tinggi persekitaran tempatan'],
                    ['name' => 'Kacukan / Lain-lain', 'desc' => 'Brahman Cross & Simmental'],
                ];
            @endphp

            @foreach($breeds as $b)
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/10 text-center hover:bg-white/20 transition-colors">
                    <div class="w-10 h-10 mx-auto rounded-full bg-emerald-400/20 text-emerald-300 flex items-center justify-center mb-2">
                        <i class="fas fa-bullhorn text-sm"></i>
                    </div>
                    <div class="text-sm font-bold text-white">{{ $b['name'] }}</div>
                    <div class="text-[11px] text-slate-300 mt-1 leading-snug">{{ $b['desc'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
