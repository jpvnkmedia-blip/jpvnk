<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EptrController;
use App\Http\Controllers\PawahController;
use App\Http\Controllers\EpuController;
use App\Http\Controllers\KursusController;
use App\Http\Controllers\KlinikController;
use App\Http\Controllers\InventoriController;
use App\Http\Controllers\KenderaanController;
use App\Http\Controllers\PemanduController;
use App\Http\Controllers\PemindahanTernakanController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PetaTaburanController;
use App\Http\Controllers\NaimbifPublicController;
use App\Http\Controllers\NaimbifAdminController;

// Laman Utama -> Redirect ke Dashboard atau Login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Google OAuth
    Route::get('/auth/google', [AuthController::class, 'googleLogin'])->name('auth.google');
    Route::post('/auth/google/callback', [AuthController::class, 'googleCallback'])->name('auth.google.callback');

    // MyDigital ID
    Route::get('/auth/mydigitalid', [AuthController::class, 'myDigitalIdLogin'])->name('auth.mydigitalid');
    Route::post('/auth/mydigitalid/verify', [AuthController::class, 'myDigitalIdVerify'])->name('auth.mydigitalid.verify');
});

// Role Switcher (Akses pantas demo & pengujian)
Route::post('/auth/switch-role', [AuthController::class, 'switchRole'])->name('auth.switch-role');

// Authenticated System Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Unified Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Peta Taburan Penternak GIS (Modul Pengarah & Eksekutif)
    Route::get('/peta-taburan', [PetaTaburanController::class, 'index'])->name('peta.taburan');
    Route::get('/pengarah/peta-taburan', [PetaTaburanController::class, 'index'])->name('pengarah.peta');

    // Pusat Notifikasi Aktiviti Pengguna
    Route::prefix('notifikasi')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/feed', [NotificationController::class, 'feed'])->name('feed');
        Route::post('/{id}/baca', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/baca-semua', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::post('/bersihkan-dibaca', [NotificationController::class, 'clearRead'])->name('clear-read');
    });

    // Profil Pengguna (Semua Pengguna Berdaftar)
    Route::prefix('profil')->name('profile.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ProfileController::class, 'show'])->name('show');
        Route::put('/', [\App\Http\Controllers\ProfileController::class, 'update'])->name('update');
        Route::put('/kata-laluan', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('password');
    });

    // 1. MODUL EPTR (Enakmen Pendaftaran Ternakan Ruminan)
    Route::prefix('eptr')->name('eptr.')->group(function () {
        Route::get('/', [EptrController::class, 'index'])->name('index');
        Route::get('/jadual-fi', [EptrController::class, 'jadualFi'])->name('jadual-fi');
        Route::get('/daftar-borang-a', [EptrController::class, 'create'])->name('create');
        Route::post('/daftar-borang-a', [EptrController::class, 'store'])->name('store');
        Route::get('/daftar-borang-a/{id}/cetak', [EptrController::class, 'cetakBorangA'])->name('cetak-borang-a');
        Route::match(['get', 'post'], '/cetak-pukal-borang-a', [EptrController::class, 'cetakPukalBorangA'])->name('cetak-pukal-borang-a');
        Route::get('/kad-kuning-borang-b/{id}', [EptrController::class, 'show'])->name('show');
        Route::get('/kad-kuning-borang-b/{id}/cetak', [EptrController::class, 'cetakKadKuning'])->name('cetak-kad-kuning');
        Route::match(['get', 'post'], '/cetak-pukal-borang-b', [EptrController::class, 'cetakPukalKadKuning'])->name('cetak-pukal-borang-b');
        Route::post('/ternakan/{id}/lulus', [EptrController::class, 'luluskanPendaftaran'])->name('lulus');
        Route::post('/ternakan/{id}/tolak', [EptrController::class, 'tolakPendaftaran'])->name('tolak');
        Route::post('/ternakan/{id}/muat-naik-resit', [EptrController::class, 'muatNaikResit'])->name('muat-naik-resit');

        // Pengurusan Senarai Penternak / Pemunya Ternakan
        Route::get('/penternak', [EptrController::class, 'penternakIndex'])->name('penternak.index');
        Route::get('/penternak/{id}', [EptrController::class, 'penternakShow'])->name('penternak.show');

        // EPTR Borang B (Pertukaran / Pemindahan Milikan Ternakan)
        Route::get('/pindah-milik-borang-b', [EptrController::class, 'borangBIndex'])->name('borang-b.index');
        Route::get('/pindah-milik-borang-b/mohon', [EptrController::class, 'createBorangB'])->name('borang-b.create');
        Route::post('/pindah-milik-borang-b/mohon', [EptrController::class, 'storeBorangB'])->name('borang-b.store');
        Route::get('/pindah-milik-borang-b/{id}', [EptrController::class, 'showBorangB'])->name('borang-b.show');
        Route::post('/pindah-milik-borang-b/{id}/lulus', [EptrController::class, 'luluskanBorangB'])->name('borang-b.lulus');
        Route::post('/pindah-milik-borang-b/{id}/tolak', [EptrController::class, 'tolakBorangB'])->name('borang-b.tolak');
        Route::get('/pindah-milik-borang-b/{id}/cetak', [EptrController::class, 'cetakBorangB'])->name('borang-b.cetak');
        Route::match(['get', 'post'], '/pindah-milik-borang-b/cetak-pukal', [EptrController::class, 'cetakPukalBorangB'])->name('borang-b.cetak-pukal');

        // Proses Daftar Anak Ternakan (Kelahiran Baru)
        Route::get('/daftar-anak', [EptrController::class, 'createAnak'])->name('daftar-anak');
        Route::post('/daftar-anak', [EptrController::class, 'storeAnak'])->name('store-anak');

        // Program Kesihatan Ternakan (Vaksinasi, Penyahcacingan, Rawatan, Surveilans)
        Route::get('/program-kesihatan', [EptrController::class, 'kesihatanIndex'])->name('kesihatan.index');
        Route::get('/program-kesihatan/daftar', [EptrController::class, 'createKesihatan'])->name('kesihatan.create');
        Route::post('/program-kesihatan/daftar', [EptrController::class, 'storeKesihatan'])->name('kesihatan.store');
        Route::get('/program-kesihatan/{id}', [EptrController::class, 'showKesihatan'])->name('kesihatan.show');

        // EPTR Borang C (Pembatalan / Kematian / Pindah Keluar)
        Route::get('/pembatalan-borang-c', [EptrController::class, 'borangCIndex'])->name('borang-c.index');
        Route::get('/pembatalan-borang-c/mohon', [EptrController::class, 'createBorangC'])->name('borang-c.create');
        Route::post('/pembatalan-borang-c/mohon', [EptrController::class, 'storeBorangC'])->name('borang-c.store');
        Route::post('/pembatalan-borang-c/{id}/lulus', [EptrController::class, 'luluskanBorangC'])->name('borang-c.lulus');
        Route::post('/pembatalan-borang-c/{id}/tolak', [EptrController::class, 'tolakBorangC'])->name('borang-c.tolak');
        Route::get('/pembatalan-borang-c/{id}/cetak', [EptrController::class, 'cetakBorangC'])->name('borang-c.cetak');
        Route::match(['get', 'post'], '/pembatalan-borang-c/cetak-pukal', [EptrController::class, 'cetakPukalBorangC'])->name('borang-c.cetak-pukal');

        // EPTR Borang D & SKV Sembelih (Permit Sembelihan & Sijil Kesihatan Veterinar Sembelih)
        Route::get('/permit-sembelihan-borang-d', [EptrController::class, 'borangDIndex'])->name('borang-d.index');
        Route::get('/permit-sembelihan-borang-d/mohon', [EptrController::class, 'createBorangD'])->name('borang-d.create');
        Route::post('/permit-sembelihan-borang-d/mohon', [EptrController::class, 'storeBorangD'])->name('borang-d.store');
        Route::post('/permit-sembelihan-borang-d/{id}/lulus', [EptrController::class, 'luluskanBorangD'])->name('borang-d.lulus');
        Route::post('/permit-sembelihan-borang-d/{id}/tolak', [EptrController::class, 'tolakBorangD'])->name('borang-d.tolak');
        Route::get('/permit-sembelihan-borang-d/{id}', [EptrController::class, 'showBorangD'])->name('borang-d.show');
        Route::get('/permit-sembelihan-borang-d/{id}/cetak', [EptrController::class, 'cetakBorangD'])->name('borang-d.cetak');
        Route::get('/permit-sembelihan-borang-d/{id}/cetak-skv', [EptrController::class, 'cetakSkvSembelih'])->name('borang-d.cetak-skv');
        Route::get('/permit-sembelihan-borang-d/{id}/cetak-lengkap', [EptrController::class, 'cetakSetLengkapBorangD'])->name('borang-d.cetak-lengkap');
        Route::match(['get', 'post'], '/permit-sembelihan-borang-d/cetak-pukal', [EptrController::class, 'cetakPukalBorangD'])->name('borang-d.cetak-pukal');
        Route::match(['get', 'post'], '/permit-sembelihan-borang-d/cetak-pukal-skv', [EptrController::class, 'cetakPukalSkvSembelih'])->name('borang-d.cetak-pukal-skv');

        // Modul Pemindahan Ternakan (Surat FMD, Borang Pemindahan, Deklarasi & Lampiran Tag)
        Route::prefix('pemindahan-ternakan')->name('pemindahan.')->group(function () {
            Route::get('/', [PemindahanTernakanController::class, 'index'])->name('index');
            Route::get('/mohon', [PemindahanTernakanController::class, 'create'])->name('create');
            Route::post('/mohon', [PemindahanTernakanController::class, 'store'])->name('store');
            Route::get('/{id}', [PemindahanTernakanController::class, 'show'])->name('show');
            Route::post('/{id}/lulus', [PemindahanTernakanController::class, 'lulus'])->name('lulus');
            Route::post('/{id}/tolak', [PemindahanTernakanController::class, 'tolak'])->name('tolak');
            Route::get('/{id}/cetak-surat-fmd', [PemindahanTernakanController::class, 'cetakSuratFmd'])->name('cetak-surat-fmd');
            Route::get('/{id}/cetak-borang', [PemindahanTernakanController::class, 'cetakBorangPermohonan'])->name('cetak-borang');
            Route::get('/{id}/cetak-deklarasi', [PemindahanTernakanController::class, 'cetakDeklarasi'])->name('cetak-deklarasi');
            Route::get('/{id}/cetak-lampiran-tag', [PemindahanTernakanController::class, 'cetakLampiranTag'])->name('cetak-lampiran-tag');
            Route::get('/{id}/cetak-set-lengkap', [PemindahanTernakanController::class, 'cetakSetLengkap'])->name('cetak-set-lengkap');
        });
    });

    // 2. MODUL PENGURUSAN PROGRAM PAWAH (Berhubung dengan EPTR)
    Route::prefix('pawah')->name('pawah.')->group(function () {
        Route::get('/', [PawahController::class, 'index'])->name('index');
        Route::get('/perjanjian-baru', [PawahController::class, 'create'])->name('create');
        Route::post('/perjanjian-baru', [PawahController::class, 'store'])->name('store');
        Route::get('/perjanjian/{id}', [PawahController::class, 'show'])->name('show');
        Route::get('/perjanjian/{id}/cetak', [PawahController::class, 'cetakPerjanjian'])->name('cetak-perjanjian');

        // Kelulusan & Penolakan Permohonan Pawah (Pegawai Pawah)
        Route::post('/perjanjian/{id}/lulus', [PawahController::class, 'luluskanPermohonan'])->name('lulus');
        Route::post('/perjanjian/{id}/tolak', [PawahController::class, 'tolakPermohonan'])->name('tolak');
        Route::post('/perjanjian/{id}/paut-ternakan', [PawahController::class, 'pautkanTernakan'])->name('paut-ternakan');
        Route::delete('/perjanjian/{id}/paut-ternakan/{ternakanId}', [PawahController::class, 'padamPautanTernakan'])->name('padam-pautan-ternakan');

        // Rekod Kelahiran & Kesihatan & Penyelesaian
        Route::post('/perjanjian/{id}/kelahiran', [PawahController::class, 'storeKelahiran'])->name('kelahiran.store');
        Route::post('/perjanjian/{id}/kesihatan', [PawahController::class, 'storeKesihatan'])->name('kesihatan.store');
        Route::post('/perjanjian/{id}/penyelesaian', [PawahController::class, 'storePenyelesaian'])->name('penyelesaian.store');
    });

    // 3. MODUL EPU (Enakmen Perladangan Unggas)
    Route::prefix('epu')->name('epu.')->group(function () {
        Route::get('/', [EpuController::class, 'index'])->name('index');
        Route::get('/daftar-borang-a', [EpuController::class, 'create'])->name('create');
        Route::post('/daftar-borang-a', [EpuController::class, 'store'])->name('store');
        Route::get('/ladang/{id}', [EpuController::class, 'show'])->name('show');
        Route::get('/lesen-borang-b/{permohonanId}/cetak', [EpuController::class, 'cetakLesen'])->name('cetak-lesen');
        Route::get('/pembaharuan-borang-c/{ladangId}', [EpuController::class, 'createPembaharuan'])->name('borang-c.create');
        Route::post('/pembaharuan-borang-c/{ladangId}', [EpuController::class, 'storePembaharuan'])->name('borang-c.store');
        Route::get('/pemeriksaan-borang-d/{ladangId}', [EpuController::class, 'createPemeriksaan'])->name('borang-d.create');
        Route::post('/pemeriksaan-borang-d/{ladangId}', [EpuController::class, 'storePemeriksaan'])->name('borang-d.store');

        // Cetak Borang Rasmi Diwartakan EPU
        Route::get('/cetak-borang-a/{id}', [EpuController::class, 'cetakBorangA'])->name('cetak-borang-a');
        Route::get('/cetak-borang-b-pengecualian/{id}', [EpuController::class, 'cetakBorangBPengecualian'])->name('cetak-borang-b-pengecualian');
        Route::get('/cetak-sijil-pengecualian-c/{id}', [EpuController::class, 'cetakSijilPengecualianC'])->name('cetak-sijil-pengecualian-c');
        Route::get('/cetak-salinan-pendua/{id}', [EpuController::class, 'cetakSalinanPendua'])->name('cetak-salinan-pendua');

        // Aliran Kerja & Tindakan Berperingkat EPU
        Route::post('/permohonan/{id}/verifikasi', [EpuController::class, 'verifikasiJajahan'])->name('verifikasi');
        Route::post('/permohonan/{id}/hantar-penilaian', [EpuController::class, 'hantarPenilaian'])->name('hantar-penilaian');
        Route::post('/permohonan/{id}/keputusan-pelesen', [EpuController::class, 'keputusanPelesen'])->name('keputusan-pelesen');
        Route::post('/permohonan/{id}/rayuan', [EpuController::class, 'hantarRayuan'])->name('rayuan.store');
        Route::post('/permohonan/{id}/proses-rayuan', [EpuController::class, 'prosesRayuan'])->name('rayuan.proses');
        Route::post('/permohonan/{id}/bayar-fi', [EpuController::class, 'bayarFiLesen'])->name('bayar-fi');
        Route::post('/permohonan/{id}/sahkan-bayaran', [EpuController::class, 'sahkanBayaranFi'])->name('sahkan-bayaran');
    });

    // 4. MODUL KURSUS TERNAKAN
    Route::prefix('kursus')->name('kursus.')->group(function () {
        Route::get('/', [KursusController::class, 'index'])->name('index');
        Route::get('/cipta', [KursusController::class, 'create'])->name('create');
        Route::post('/cipta', [KursusController::class, 'store'])->name('store');
        Route::get('/pengurusan-pemohon', [KursusController::class, 'pengurusanPemohon'])->name('pemohon.index');
        Route::get('/{id}', [KursusController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [KursusController::class, 'edit'])->name('edit');
        Route::put('/{id}', [KursusController::class, 'update'])->name('update');
        Route::delete('/{id}', [KursusController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/arkib', [KursusController::class, 'arkib'])->name('arkib');
        Route::post('/{id}/daftar', [KursusController::class, 'apply'])->name('apply');
        Route::post('/{id}/tukar-status', [KursusController::class, 'updateStatus'])->name('update-status');

        // Pengurusan & Kelulusan Pemohon
        Route::get('/pemohon/{id}', [KursusController::class, 'showPemohon'])->name('pemohon.show');
        Route::post('/pemohon/{id}/lulus', [KursusController::class, 'luluskanPemohon'])->name('pemohon.lulus');
        Route::post('/pemohon/{id}/tolak', [KursusController::class, 'tolakPemohon'])->name('pemohon.tolak');
        Route::post('/pemohon/{id}/hadir', [KursusController::class, 'sahkanKehadiran'])->name('pemohon.hadir');
        Route::post('/pemohon/lulus-pukal', [KursusController::class, 'lulusPukal'])->name('pemohon.lulus-pukal');

        // Cetak Sijil
        Route::get('/sijil/{applicationId}', [KursusController::class, 'cetakSijil'])->name('sijil');
    });

    // 5. MODUL KLINIK HAIWAN
    Route::prefix('klinik')->name('klinik.')->group(function () {
        Route::get('/', [KlinikController::class, 'index'])->name('index');
        Route::get('/temujanji-baru', [KlinikController::class, 'create'])->name('create');
        Route::post('/temujanji-baru', [KlinikController::class, 'store'])->name('store');
        Route::get('/api/semak-pemilik', [KlinikController::class, 'semakPemilik'])->name('semak_pemilik');
        Route::get('/temujanji/{id}', [KlinikController::class, 'show'])->name('show');
        Route::get('/temujanji/{id}/rekod-rawatan', [KlinikController::class, 'createRawatan'])->name('rawatan.create');
        Route::post('/temujanji/{id}/rekod-rawatan', [KlinikController::class, 'storeRawatan'])->name('rawatan.store');
        Route::get('/temujanji/{id}/kad-rawatan', [KlinikController::class, 'cetakKadRawatan'])->name('cetak-kad-rawatan');
    });

    // 6. MODUL INVENTORI (STOR PERALATAN PEJABAT & STOR UBAT VETERINAR)
    Route::prefix('inventori')->name('inventori.')->group(function () {
        Route::get('/', [InventoriController::class, 'index'])->name('index');
        
        // Permohonan Bekalan & Alatan oleh Staf Jabatan
        Route::prefix('permohonan')->name('permohonan.')->group(function () {
            Route::get('/saya', [InventoriController::class, 'permohonanSaya'])->name('saya');
            Route::get('/pejabat/mohon', [InventoriController::class, 'createPermohonanPejabat'])->name('pejabat.mohon');
            Route::post('/pejabat/mohon', [InventoriController::class, 'storePermohonanPejabat'])->name('pejabat.store');
            Route::get('/ubat/mohon', [InventoriController::class, 'createPermohonanUbat'])->name('ubat.mohon');
            Route::post('/ubat/mohon', [InventoriController::class, 'storePermohonanUbat'])->name('ubat.store');
            Route::post('/{id}/status', [InventoriController::class, 'updateStatusPermohonan'])->name('status');
            Route::delete('/{id}', [InventoriController::class, 'batalPermohonan'])->name('batal');
        });

        // Stor Peralatan Pejabat (Admin Pejabat & Super Admin)
        Route::prefix('pejabat')->name('pejabat.')->group(function () {
            Route::get('/', [InventoriController::class, 'indexPejabat'])->name('index');
            Route::get('/tambah', [InventoriController::class, 'createPejabat'])->name('create');
            Route::post('/tambah', [InventoriController::class, 'storePejabat'])->name('store');
            Route::get('/permohonan', [InventoriController::class, 'senaraiPermohonanPejabat'])->name('permohonan');
        });

        // Stor Ubat, Vaksin & Peralatan Veterinar (Admin Stor Ubat & Super Admin)
        Route::prefix('ubat')->name('ubat.')->group(function () {
            Route::get('/', [InventoriController::class, 'indexUbat'])->name('index');
            Route::get('/tambah', [InventoriController::class, 'createUbat'])->name('create');
            Route::post('/tambah', [InventoriController::class, 'storeUbat'])->name('store');
            Route::get('/permohonan', [InventoriController::class, 'senaraiPermohonanUbat'])->name('permohonan');
        });

        // Maklumat Item, Transaksi Keluar Masuk & Pinjaman
        Route::get('/item/{id}', [InventoriController::class, 'show'])->name('show');
        Route::post('/item/{id}/transaksi', [InventoriController::class, 'storeTransaksi'])->name('transaksi.store');
        Route::post('/item/{id}/pinjaman', [InventoriController::class, 'storePinjaman'])->name('pinjaman.store');
        Route::post('/pinjaman/{id}/pulang', [InventoriController::class, 'pulangPinjaman'])->name('pinjaman.pulang');
        Route::delete('/item/{id}', [InventoriController::class, 'destroy'])->name('destroy');
    });

    // 7. MODUL PERMOHONAN KENDERAAN & PEMANDU
    Route::prefix('kenderaan')->name('kenderaan.')->group(function () {
        Route::get('/', [KenderaanController::class, 'index'])->name('index');
        Route::get('/tempahan-baru', [KenderaanController::class, 'create'])->name('create');
        Route::post('/tempahan-baru', [KenderaanController::class, 'store'])->name('store');
        Route::get('/tempahan/{id}', [KenderaanController::class, 'show'])->name('show');
        Route::post('/tempahan/{id}/kelulusan', [KenderaanController::class, 'approve'])->name('approve');
        Route::post('/tempahan/{id}/lulus', [KenderaanController::class, 'approve'])->name('lulus');
        Route::post('/tempahan/{id}/tolak', [KenderaanController::class, 'tolak'])->name('tolak');
        Route::post('/tempahan/{id}/selesai', [KenderaanController::class, 'complete'])->name('complete');
        Route::post('/tempahan/{id}/tamat', [KenderaanController::class, 'complete'])->name('selesai');

        // Pengurusan Maklumat & Fleet Kenderaan
        Route::get('/fleet', [KenderaanController::class, 'fleetIndex'])->name('fleet');
        Route::post('/kenderaan-baru', [KenderaanController::class, 'storeKenderaan'])->name('storeKenderaan');
        Route::put('/kenderaan/{id}', [KenderaanController::class, 'updateKenderaan'])->name('updateKenderaan');
        Route::delete('/kenderaan/{id}', [KenderaanController::class, 'destroyKenderaan'])->name('destroyKenderaan');

        // Pengurusan Maklumat Pemandu Jabatan
        Route::prefix('pemandu')->name('pemandu.')->group(function () {
            Route::get('/', [PemanduController::class, 'index'])->name('index');
            Route::get('/daftar', [PemanduController::class, 'create'])->name('create');
            Route::post('/', [PemanduController::class, 'store'])->name('store');
            Route::get('/{id}', [PemanduController::class, 'show'])->name('show');
            Route::get('/{id}/kemaskini', [PemanduController::class, 'edit'])->name('edit');
            Route::put('/{id}', [PemanduController::class, 'update'])->name('update');
            Route::delete('/{id}', [PemanduController::class, 'destroy'])->name('destroy');
        });
    });

    // 8. MODUL PENGURUSAN PENGGUNA (SUPER ADMIN)
    Route::prefix('pengguna')->name('users.')->group(function () {
        Route::get('/', [\App\Http\Controllers\UserController::class, 'index'])->name('index');
        Route::get('/tambah', [\App\Http\Controllers\UserController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\UserController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\UserController::class, 'show'])->name('show');
        Route::get('/{id}/kemaskini', [\App\Http\Controllers\UserController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\UserController::class, 'update'])->name('update');
        Route::post('/{id}/toggle-status', [\App\Http\Controllers\UserController::class, 'toggleStatus'])->name('toggle-status');
        Route::delete('/{id}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('destroy');
    });

    // 9. MODUL PENGURUSAN PROGRAM NAIMbif (LADANG BRIDLOT)
    Route::prefix('naimbif/urus')->name('naimbif.admin.')->group(function () {
        Route::get('/', [NaimbifAdminController::class, 'index'])->name('index');
        Route::get('/eksport', [NaimbifAdminController::class, 'exportCsv'])->name('export');
        Route::get('/{id}', [NaimbifAdminController::class, 'show'])->name('show');
        Route::post('/{id}/jajahan', [NaimbifAdminController::class, 'updateJajahan'])->name('update_jajahan');
        Route::put('/{id}/jajahan', [NaimbifAdminController::class, 'updateJajahan'])->name('update_jajahan_put');
        Route::post('/{id}/negeri', [NaimbifAdminController::class, 'updateNegeri'])->name('update_negeri');
        Route::put('/{id}/negeri', [NaimbifAdminController::class, 'updateNegeri'])->name('update_negeri_put');
        Route::delete('/{id}', [NaimbifAdminController::class, 'destroy'])->name('destroy');
    });
});

// ==========================================
// PORTAL PROGRAM NAIMbif (LADANG BRIDLOT PEDAGING)
// ==========================================
Route::prefix('naimbif')->name('naimbif.public.')->group(function () {
    Route::get('/', [NaimbifPublicController::class, 'index'])->name('home');
    Route::get('/permohonan', [NaimbifPublicController::class, 'create'])->name('apply');
    Route::post('/permohonan', [NaimbifPublicController::class, 'store'])->name('store');
    Route::match(['get', 'post'], '/semakan', [NaimbifPublicController::class, 'checkStatus'])->name('check_status');
    Route::get('/berjaya/{no_rujukan}', [NaimbifPublicController::class, 'success'])->name('success');
    Route::get('/cetak/{no_rujukan}', [NaimbifPublicController::class, 'printForm'])->name('print');
    Route::get('/kemaskini/{no_rujukan}', [NaimbifPublicController::class, 'edit'])->name('edit');
    Route::match(['post', 'put'], '/kemaskini/{no_rujukan}', [NaimbifPublicController::class, 'update'])->name('update');
    Route::match(['get', 'post'], '/sahkan-kemaskini/{no_rujukan}', [NaimbifPublicController::class, 'verifyEdit'])->name('verify_edit');
    Route::get('/api/semak-kp', [NaimbifPublicController::class, 'checkExistingIc'])->name('check_ic');
});

