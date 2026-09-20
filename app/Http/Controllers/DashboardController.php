<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pemunya;
use App\Models\Ternakan;
use App\Models\PindahMilik;
use App\Models\PembatalanTernakan;
use App\Models\PermitSembelihan;
use App\Models\ProgramKesihatan;
use App\Models\PawahPerjanjian;
use App\Models\PawahTernakan;
use App\Models\PawahRekodKelahiran;
use App\Models\PawahRekodKesihatan;
use App\Models\PawahPenyelesaian;
use App\Models\EpuLadang;
use App\Models\EpuPermohonan;
use App\Models\Course;
use App\Models\CourseApplication;
use App\Models\KlinikTemujanji;
use App\Models\KlinikRawatan;
use App\Models\InventoriItem;
use App\Models\InventoriPermohonan;
use App\Models\Kenderaan;
use App\Models\KenderaanTempahan;
use App\Models\Pemandu;
use App\Models\NaimbifPermohonan;
use App\Models\NaimbifInventoriTernakan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isStaff = $user->isStaff();

        // Dapatkan profil Pemunya EPTR bagi pengguna
        $currentPemunya = $user->pemunya;
        if (!$currentPemunya && $user->ic_number) {
            $currentPemunya = \App\Models\Pemunya::where('no_kp', $user->ic_number)->first();
        }
        if (!$currentPemunya && $user->id) {
            $currentPemunya = \App\Models\Pemunya::where('user_id', $user->id)->first();
        }

        // 1. Data Khusus Pengguna Semasa (Milik Pengguna Sahaja & Termasuk Ternakan Pawah)
        $userPawahTernakanIds = \App\Models\PawahTernakan::whereHas('perjanjian', fn($q) => $q->where('user_id', $user->id))->pluck('ternakan_id');

        $userTernakanQuery = Ternakan::where(function ($q) use ($currentPemunya, $userPawahTernakanIds) {
            if ($currentPemunya) {
                $q->where('pemunya_id', $currentPemunya->id);
            }
            if ($userPawahTernakanIds->isNotEmpty()) {
                $q->orWhereIn('id', $userPawahTernakanIds);
            }
            if (!$currentPemunya && $userPawahTernakanIds->isEmpty()) {
                $q->whereRaw('1 = 0');
            }
        });

        $myTernakan = (clone $userTernakanQuery)->latest()->take(6)->get();
        $myPawah = PawahPerjanjian::where('user_id', $user->id)->with('ternakanList')->latest()->get();
        $myFarms = EpuLadang::where('user_id', $user->id)->with('permohonanList')->latest()->get();
        $myCourses = CourseApplication::where('user_id', $user->id)->with('course')->latest()->take(6)->get();
        $myClinicAppointments = KlinikTemujanji::where('user_id', $user->id)->latest()->take(6)->get();
        $myVehicleBookings = KenderaanTempahan::where('user_id', $user->id)->with('kenderaan')->latest()->take(5)->get();
        $myInventoryRequests = \App\Models\InventoriPermohonan::where('user_id', $user->id)->with(['item', 'pelulus'])->latest()->take(6)->get();
        $myInventoryPendingCount = \App\Models\InventoriPermohonan::where('user_id', $user->id)->where('status', 'Menunggu Kelulusan')->count();
        $myInventoryApprovedCount = \App\Models\InventoriPermohonan::where('user_id', $user->id)->whereIn('status', ['Diluluskan', 'Telah Diambil / Diserahkan'])->count();
        $officeStoreItems = InventoriItem::where('jenis_stor', 'pejabat')->where('status', '!=', 'Habis Stok')->latest()->take(6)->get();

        if ($isStaff) {
            // STATISTIK KESELURUHAN & PENTADBIRAN (UNTUK KAKITANGAN / ADMIN)
            if (in_array($user->role, ['admin_jajahan', 'admin_eptr_jajahan']) && $user->jajahan) {
                $totalTernakanEptr = Ternakan::where('jajahan', $user->jajahan)->count();
                $totalPendingTernakan = Ternakan::where('jajahan', $user->jajahan)->where('status_kelulusan', 'Menunggu')->count();
                $totalKesihatan = \App\Models\ProgramKesihatan::where('jajahan', $user->jajahan)->count();
                $totalPermitSembelihan = PermitSembelihan::whereHas('pemunya', function ($q) use ($user) {
                    $q->where('jajahan', $user->jajahan);
                })->count();
                $recentTernakan = Ternakan::with('pemunya')->where('jajahan', $user->jajahan)->latest()->take(6)->get();
            } else {
                $totalTernakanEptr = Ternakan::count();
                $totalPendingTernakan = Ternakan::where('status_kelulusan', 'Menunggu')->count();
                $totalKesihatan = \App\Models\ProgramKesihatan::count();
                $totalPermitSembelihan = PermitSembelihan::count();
                $recentTernakan = Ternakan::with('pemunya')->latest()->take(6)->get();
            }

            $totalTernakanPawah = Ternakan::whereNotNull('program')->where('program', '!=', 'Tiada')->count();
            $totalPawahActive = PawahPerjanjian::where('status', 'Aktif')->count();
            $totalPawahMenunggu = PawahPerjanjian::where('status', 'Menunggu Kelulusan')->count();
            $totalPawahSelesai = PawahPerjanjian::where('status', 'Selesai')->count();
            $totalPawahKelahiran = PawahRekodKelahiran::count();
            $totalPawahKesihatan = PawahRekodKesihatan::count();
            $totalPawahTernakanInduk = PawahTernakan::count();
            $totalEpuFarms = EpuLadang::count();
            $totalEpuLicenses = EpuPermohonan::where('status', 'Diluluskan')->count();
            
            $epuQuery = EpuPermohonan::query();
            if ($user->role === 'pegawai_verifikasi_epu' && $user->jajahan) {
                $epuQuery->whereHas('ladang', fn($q) => $q->where('jajahan', $user->jajahan));
            }
            $totalEpuPendingVerifikasi = (clone $epuQuery)->where(function ($q) {
                $q->whereNull('status_verifikasi')
                  ->orWhereIn('status_verifikasi', ['Belum Disemak', 'Tidak Lengkap', 'Tidak Patuh']);
            })->where('status', '!=', 'Diluluskan')->count();

            $totalEpuPendingPelesen = EpuPermohonan::where('status_penilaian_ladang', 'Dihantar ke Pegawai Pelesen')
                ->where('status', 'Menunggu Kelulusan Pelesen')->count();

            $totalEpuPendingBayaran = EpuPermohonan::where('status', 'Diluluskan')
                ->where('status_bayaran_fi', 'Menunggu Pengesahan')->count();

            $totalCourses = Course::where('status', 'Buka')->count();
            $totalCourseApplications = CourseApplication::count();
            $totalClinicAppointments = KlinikTemujanji::count();
            
            $totalPejabatItems = InventoriItem::where('jenis_stor', 'pejabat')->count();
            $lowStockPejabatCount = InventoriItem::where('jenis_stor', 'pejabat')->where(function ($q) {
                $q->where('status', 'Stok Rendah')->orWhere('status', 'Habis Stok');
            })->count();
            $pendingPejabatRequests = \App\Models\InventoriPermohonan::where('jenis_stor', 'pejabat')->where('status', 'Menunggu Kelulusan')->count();
            $totalPejabatPinjaman = \App\Models\InventoriPinjaman::whereHas('item', fn($q) => $q->where('jenis_stor', 'pejabat'))->where('status', 'Dipinjam')->count();
            $recentPejabatPermohonan = \App\Models\InventoriPermohonan::where('jenis_stor', 'pejabat')->with(['user', 'item'])->latest()->take(6)->get();

            $totalUbatItems = InventoriItem::where('jenis_stor', 'ubat')->count();
            $lowStockUbatCount = InventoriItem::where('jenis_stor', 'ubat')->where(function ($q) {
                $q->where('status', 'Stok Rendah')->orWhere('status', 'Habis Stok');
            })->count();

            if ($user->role === 'admin_pejabat') {
                $totalInventoryItems = $totalPejabatItems;
                $lowStockInventoryCount = $lowStockPejabatCount;
                $recentInventory = InventoriItem::where('jenis_stor', 'pejabat')->latest()->take(6)->get();
            } elseif ($user->role === 'admin_ubat') {
                $totalInventoryItems = $totalUbatItems;
                $lowStockInventoryCount = $lowStockUbatCount;
                $recentInventory = InventoriItem::where('jenis_stor', 'ubat')->latest()->take(6)->get();
            } else {
                $totalInventoryItems = InventoriItem::count();
                $lowStockInventoryCount = InventoriItem::where('status', 'Stok Rendah')->orWhere('status', 'Habis Stok')->count();
                $recentInventory = InventoriItem::latest()->take(6)->get();
            }

            $totalVehicles = Kenderaan::count();
            $availableVehicles = Kenderaan::where('status', 'Sedia')->count();
            $inUseVehicles = Kenderaan::where('status', 'Sedang Digunakan')->count();
            $inServiceVehicles = Kenderaan::whereIn('status', ['Dalam Servis', 'Rosak'])->count();
            $pendingVehicleBookings = KenderaanTempahan::whereIn('status', ['Menunggu', 'Menunggu Kelulusan'])->count();
            $totalPemandu = \App\Models\Pemandu::count();
            $activePemandu = \App\Models\Pemandu::whereIn('status', ['Aktif', 'Bertugas'])->count();
            $recentFleet = Kenderaan::latest()->take(6)->get();
            $totalUsers = User::count();

            // NAIMbif Program Metrics (Staff / Admin)
            $totalNaimbifApps = \App\Models\NaimbifPermohonan::forUser()->count();
            $totalNaimbifLulus = \App\Models\NaimbifPermohonan::forUser()->where('status_negeri', 'Lulus')->count();
            $totalNaimbifMenungguJajahan = \App\Models\NaimbifPermohonan::forUser()->where('syor_permohonan', 'Belum Disemak')->count();
            $totalNaimbifMenungguNegeri = \App\Models\NaimbifPermohonan::forUser()->where('syor_permohonan', 'Disokong')->where('status_negeri', 'Menunggu Kelulusan')->count();
            $recentNaimbif = \App\Models\NaimbifPermohonan::forUser()->with('inventoriTernakan')->latest()->take(6)->get();

            $recentPawah = PawahPerjanjian::with('peserta', 'ternakanList')->latest()->take(5)->get();
            $recentEpu = (clone $epuQuery)->with('ladang.pemilik', 'pegawaiVerifikasi')->latest()->take(8)->get();
            $recentTemujanji = KlinikTemujanji::with('pemilik')->latest()->take(5)->get();
            $recentTempahanKenderaan = KenderaanTempahan::with('pemohon', 'kenderaan')->latest()->take(5)->get();
            $recentSembelehan = PermitSembelihan::with('pemunya', 'ternakan')->latest()->take(5)->get();
            $recentCourses = Course::withCount('applications')->latest()->take(6)->get();

            $jajahanList = ['Kota Bharu', 'Pasir Mas', 'Tumpat', 'Bachok', 'Pasir Puteh', 'Machang', 'Tanah Merah', 'Jeli', 'Kuala Krai', 'Gua Musang'];
            $eptrByJajahan = Ternakan::whereNotNull('jajahan')
                ->groupBy('jajahan')
                ->select('jajahan', DB::raw('count(*) as total_count'))
                ->pluck('total_count', 'jajahan')
                ->toArray();

            $pawahByJajahan = Ternakan::where(function ($q) {
                    $q->whereNotNull('program')->where('program', '!=', 'Tiada')
                      ->orWhere('status', 'Pawah');
                })
                ->whereNotNull('jajahan')
                ->groupBy('jajahan')
                ->select('jajahan', DB::raw('count(*) as total_count'))
                ->pluck('total_count', 'jajahan')
                ->toArray();

            $pawahPerjanjianByJajahan = PawahPerjanjian::whereNotNull('jajahan')
                ->groupBy('jajahan')
                ->select('jajahan', DB::raw('count(*) as total_count'))
                ->pluck('total_count', 'jajahan')
                ->toArray();

            $chartEptrData = [];
            $chartPawahData = [];
            foreach ($jajahanList as $j) {
                $chartEptrData[] = (int) ($eptrByJajahan[$j] ?? 0);
                $pCount = (int) ($pawahByJajahan[$j] ?? 0);
                if ($pCount === 0 && isset($pawahPerjanjianByJajahan[$j])) {
                    $pCount = (int) $pawahPerjanjianByJajahan[$j];
                }
                $chartPawahData[] = $pCount;
            }
        } else {
            $jajahanList = ['Kota Bharu', 'Pasir Mas', 'Tumpat', 'Bachok', 'Pasir Puteh', 'Machang', 'Tanah Merah', 'Jeli', 'Kuala Krai', 'Gua Musang'];
            $totalEpuPendingVerifikasi = 0;
            $totalEpuPendingPelesen = 0;
            $totalEpuPendingBayaran = 0;
            // MAKLUMAT UNTUK PENGGUNA TERSEBUT SAHAJA (PENTERNAK / USAHAWAN / ORANG AWAM)
            $totalTernakanEptr = (clone $userTernakanQuery)->whereIn('status', ['Aktif', 'Pawah'])->count();
            $totalPendingTernakan = (clone $userTernakanQuery)->where('status_kelulusan', 'Menunggu')->count();
            $totalTernakanPawah = (clone $userTernakanQuery)->where(function ($q) {
                $q->where(function ($sq) {
                    $sq->whereNotNull('program')->where('program', '!=', 'Tiada');
                })->orWhere('status', 'Pawah');
            })->count();
            $totalKesihatan = $currentPemunya ? \App\Models\ProgramKesihatan::whereHas('ternakan', fn($q) => $q->where('pemunya_id', $currentPemunya->id))->count() : 0;
            $totalPermitSembelihan = $currentPemunya ? PermitSembelihan::where('pemunya_id', $currentPemunya->id)->count() : 0;

            $chartEptrData = array_fill(0, count($jajahanList), 0);
            $chartPawahData = array_fill(0, count($jajahanList), 0);
            if ($user->jajahan && in_array($user->jajahan, $jajahanList)) {
                $idx = array_search($user->jajahan, $jajahanList);
                $chartEptrData[$idx] = $totalTernakanEptr;
                $chartPawahData[$idx] = $totalTernakanPawah;
            }

            $totalPawahActive = PawahPerjanjian::where('user_id', $user->id)->where('status', 'Aktif')->count();
            $totalPawahMenunggu = PawahPerjanjian::where('user_id', $user->id)->where('status', 'Menunggu Kelulusan')->count();
            $totalPawahSelesai = PawahPerjanjian::where('user_id', $user->id)->where('status', 'Selesai')->count();
            $totalPawahKelahiran = PawahRekodKelahiran::whereHas('perjanjian', fn($q) => $q->where('user_id', $user->id))->count();
            $totalPawahKesihatan = PawahRekodKesihatan::whereHas('perjanjian', fn($q) => $q->where('user_id', $user->id))->count();
            $totalPawahTernakanInduk = PawahTernakan::whereHas('perjanjian', fn($q) => $q->where('user_id', $user->id))->count();

            $totalEpuFarms = EpuLadang::where('user_id', $user->id)->count();
            $totalEpuLicenses = EpuPermohonan::whereHas('ladang', fn($q) => $q->where('user_id', $user->id))->where('status', 'Diluluskan')->count();

            $totalCourses = CourseApplication::where('user_id', $user->id)->count();
            $totalCourseApplications = CourseApplication::where('user_id', $user->id)->where('status', 'Diluluskan')->count();

            $totalClinicAppointments = KlinikTemujanji::where('user_id', $user->id)->count();

            $totalInventoryItems = 0;
            $lowStockInventoryCount = 0;
            $totalPejabatItems = 0;
            $lowStockPejabatCount = 0;
            $pendingPejabatRequests = 0;
            $totalPejabatPinjaman = 0;
            $recentPejabatPermohonan = collect();
            $totalUbatItems = 0;
            $lowStockUbatCount = 0;
            $totalVehicles = 0;
            $availableVehicles = 0;
            $inUseVehicles = 0;
            $inServiceVehicles = 0;
            $pendingVehicleBookings = 0;
            $totalPemandu = 0;
            $activePemandu = 0;
            $recentFleet = collect();
            $totalUsers = 1;

            $totalNaimbifApps = \App\Models\NaimbifPermohonan::forUser()->count();
            $totalNaimbifLulus = \App\Models\NaimbifPermohonan::forUser()->where('status_negeri', 'Lulus')->count();
            $totalNaimbifMenungguJajahan = \App\Models\NaimbifPermohonan::forUser()->where('syor_permohonan', 'Belum Disemak')->count();
            $totalNaimbifMenungguNegeri = \App\Models\NaimbifPermohonan::forUser()->where('syor_permohonan', 'Disokong')->where('status_negeri', 'Menunggu Kelulusan')->count();
            $recentNaimbif = \App\Models\NaimbifPermohonan::forUser()->with('inventoriTernakan')->latest()->take(6)->get();

            $recentTernakan = $myTernakan;
            $recentPawah = $myPawah;
            $recentEpu = EpuPermohonan::whereHas('ladang', fn($q) => $q->where('user_id', $user->id))->with('ladang')->latest()->take(5)->get();
            $recentTemujanji = $myClinicAppointments;
            $recentTempahanKenderaan = $myVehicleBookings;
            $recentSembelehan = $currentPemunya ? PermitSembelihan::where('pemunya_id', $currentPemunya->id)->with('ternakan')->latest()->take(5)->get() : collect();
            $recentInventory = collect();
            $recentCourses = Course::where('status', 'Buka')->latest()->take(4)->get();
        }

        return view('dashboard', compact(
            'user',
            'totalTernakanEptr',
            'totalPendingTernakan',
            'totalKesihatan',
            'totalPermitSembelihan',
            'totalTernakanPawah',
            'totalPawahActive',
            'totalPawahMenunggu',
            'totalPawahSelesai',
            'totalPawahKelahiran',
            'totalPawahKesihatan',
            'totalPawahTernakanInduk',
            'totalEpuFarms',
            'totalEpuLicenses',
            'totalEpuPendingVerifikasi',
            'totalEpuPendingPelesen',
            'totalEpuPendingBayaran',
            'totalCourses',
            'totalCourseApplications',
            'totalClinicAppointments',
            'totalInventoryItems',
            'lowStockInventoryCount',
            'totalPejabatItems',
            'lowStockPejabatCount',
            'pendingPejabatRequests',
            'totalPejabatPinjaman',
            'recentPejabatPermohonan',
            'totalUbatItems',
            'lowStockUbatCount',
            'totalVehicles',
            'availableVehicles',
            'inUseVehicles',
            'inServiceVehicles',
            'pendingVehicleBookings',
            'totalPemandu',
            'activePemandu',
            'recentFleet',
            'totalUsers',
            'totalNaimbifApps',
            'totalNaimbifLulus',
            'totalNaimbifMenungguJajahan',
            'totalNaimbifMenungguNegeri',
            'recentNaimbif',
            'myTernakan',
            'myPawah',
            'myFarms',
            'myCourses',
            'myClinicAppointments',
            'myVehicleBookings',
            'myInventoryRequests',
            'myInventoryPendingCount',
            'myInventoryApprovedCount',
            'officeStoreItems',
            'recentTernakan',
            'recentPawah',
            'recentEpu',
            'recentTemujanji',
            'recentTempahanKenderaan',
            'recentSembelehan',
            'recentInventory',
            'recentCourses',
            'jajahanList',
            'chartEptrData',
            'chartPawahData'
        ));
    }

    public function laporanPengarah()
    {
        $user = Auth::user();
        if (!$user->isPengarah() && !$user->isSuperAdmin() && !$user->isStaff()) {
            abort(403, 'Akses Ditolak: Hanya Pengarah dan Pentadbir Eksekutif yang dibenarkan melihat modul ini.');
        }

        $jajahanList = ['Kota Bharu', 'Pasir Mas', 'Tumpat', 'Bachok', 'Pasir Puteh', 'Machang', 'Tanah Merah', 'Jeli', 'Kuala Krai', 'Gua Musang'];

        // 1. EPTR Ruminan
        $totalTernakanEptr = Ternakan::count();
        $totalTernakanAktif = Ternakan::where('status', 'Aktif')->count();
        $totalTernakanPawah = Ternakan::where(function ($q) {
            $q->whereNotNull('program')->where('program', '!=', 'Tiada')
              ->orWhere('status', 'Pawah');
        })->count();
        $totalTernakanTagged = Ternakan::whereNotNull('no_tag')->where('no_tag', '!=', '')->count();
        $totalTernakanUntagged = max(0, $totalTernakanEptr - $totalTernakanTagged);
        $totalPendingTernakan = Ternakan::where('status_kelulusan', 'Menunggu')->count();
        $totalPermitSembelihan = PermitSembelihan::count();
        $totalPembatalanKematian = PembatalanTernakan::count();
        $totalPindahMilik = PindahMilik::count();
        $totalProgramKesihatan = ProgramKesihatan::count();

        $eptrSpeciesRaw = Ternakan::select('jenis_ternakan', DB::raw('count(*) as total'))
            ->groupBy('jenis_ternakan')->pluck('total', 'jenis_ternakan')->toArray();
        $eptrSpeciesData = [
            'Lembu' => $eptrSpeciesRaw['Lembu'] ?? 0,
            'Kerbau' => $eptrSpeciesRaw['Kerbau'] ?? 0,
            'Kambing' => $eptrSpeciesRaw['Kambing'] ?? 0,
            'Biri-biri' => ($eptrSpeciesRaw['Biri-biri'] ?? 0) + ($eptrSpeciesRaw['Biri-Biri'] ?? 0) + ($eptrSpeciesRaw['Biri - Biri'] ?? 0),
            'Rusa' => $eptrSpeciesRaw['Rusa'] ?? 0,
            'Lain-lain' => 0
        ];
        foreach ($eptrSpeciesRaw as $sp => $cnt) {
            if (!in_array($sp, ['Lembu', 'Kerbau', 'Kambing', 'Biri-biri', 'Biri-Biri', 'Biri - Biri', 'Rusa'])) {
                $eptrSpeciesData['Lain-lain'] += $cnt;
            }
        }

        $eptrJajahanRaw = Ternakan::whereNotNull('jajahan')
            ->select('jajahan', DB::raw('count(*) as total'))
            ->groupBy('jajahan')->pluck('total', 'jajahan')->toArray();
        $eptrByJajahan = [];
        foreach ($jajahanList as $j) {
            $eptrByJajahan[$j] = (int) ($eptrJajahanRaw[$j] ?? 0);
        }

        // 2. Program Pawah
        $totalPawahPerjanjian = PawahPerjanjian::count();
        $totalPawahAktif = PawahPerjanjian::where('status', 'Aktif')->count();
        $totalPawahMenunggu = PawahPerjanjian::where('status', 'Menunggu Kelulusan')->count();
        $totalPawahSelesai = PawahPerjanjian::where('status', 'Selesai')->count();
        $totalPawahInduk = PawahTernakan::count();
        $totalPawahKelahiran = PawahRekodKelahiran::count();
        $totalPawahKelahiranHidup = PawahRekodKelahiran::where('status_anak', 'Hidup')->count();
        $totalPawahKesihatan = PawahRekodKesihatan::count();

        $pawahJajahanRaw = PawahPerjanjian::whereNotNull('jajahan')
            ->select('jajahan', DB::raw('count(*) as total'))
            ->groupBy('jajahan')->pluck('total', 'jajahan')->toArray();
        $pawahByJajahan = [];
        foreach ($jajahanList as $j) {
            $pawahByJajahan[$j] = (int) ($pawahJajahanRaw[$j] ?? 0);
        }

        // 3. EPU (Enakmen Penternakan Unggas)
        $totalEpuLadang = EpuLadang::count();
        $totalEpuPermohonan = EpuPermohonan::count();
        $totalEpuLulus = EpuPermohonan::where('status', 'Diluluskan')->count();
        $totalEpuPendingVerifikasi = EpuPermohonan::where(function ($q) {
            $q->whereNull('status_verifikasi')->orWhereIn('status_verifikasi', ['Belum Disemak', 'Tidak Lengkap', 'Tidak Patuh']);
        })->where('status', '!=', 'Diluluskan')->count();
        $totalEpuPendingPelesen = EpuPermohonan::where('status_penilaian_ladang', 'Dihantar ke Pegawai Pelesen')
            ->where('status', 'Menunggu Kelulusan Pelesen')->count();
        $totalEpuRayuan = EpuPermohonan::where('status_rayuan', 'Menunggu Semakan Rayuan')
            ->orWhere('status', 'Rayuan')->count();
        $totalEpuDitolak = EpuPermohonan::where('status', 'Ditolak')->count();
        $totalEpuKapasiti = (int) EpuPermohonan::sum('kapasiti_ladang');
        $totalEpuSemasaUnggas = (int) EpuPermohonan::sum('bilangan_semasa_unggas');
        $totalEpuFiKutipan = (float) EpuPermohonan::where('status', 'Diluluskan')->sum('yuran_lesen');

        $epuSpeciesRaw = EpuPermohonan::select('jenis_unggas', DB::raw('sum(bilangan_semasa_unggas) as total'))
            ->groupBy('jenis_unggas')->pluck('total', 'jenis_unggas')->toArray();
        $epuJajahanRaw = EpuLadang::whereNotNull('jajahan')
            ->select('jajahan', DB::raw('count(*) as total'))
            ->groupBy('jajahan')->pluck('total', 'jajahan')->toArray();
        $epuByJajahan = [];
        foreach ($jajahanList as $j) {
            $epuByJajahan[$j] = (int) ($epuJajahanRaw[$j] ?? 0);
        }

        $pendingEpuForDirector = EpuPermohonan::with(['ladang.pemilik', 'pegawaiVerifikasi'])
            ->where(function ($q) {
                $q->where(function ($sq) {
                    $sq->where('status_penilaian_ladang', 'Dihantar ke Pegawai Pelesen')
                       ->where('status', 'Menunggu Kelulusan Pelesen');
                })->orWhere('status_rayuan', 'Menunggu Semakan Rayuan')
                  ->orWhere('status', 'Rayuan');
            })
            ->latest()->take(10)->get();

        // 4. Program NAIMbif (Ladang Bridlot)
        $totalNaimbifApps = NaimbifPermohonan::count();
        $totalNaimbifLulus = NaimbifPermohonan::where('status_negeri', 'Lulus')->count();
        $totalNaimbifMenungguNegeri = NaimbifPermohonan::where('syor_permohonan', 'Disokong')->where('status_negeri', 'Menunggu Kelulusan')->count();
        $totalNaimbifMenungguJajahan = NaimbifPermohonan::where('syor_permohonan', 'Belum Disemak')->count();
        $totalNaimbifTolak = NaimbifPermohonan::where('status_negeri', 'Tolak')->orWhere('syor_permohonan', 'Tidak Disokong')->count();
        $totalNaimbifPopulasi = (int) NaimbifInventoriTernakan::sum('jumlah_baka');

        $naimbifBakaRaw = NaimbifInventoriTernakan::select('baka', DB::raw('sum(jumlah_baka) as total'))
            ->groupBy('baka')->pluck('total', 'baka')->toArray();
        
        $naimbifJajahanRaw = NaimbifPermohonan::select(DB::raw('COALESCE(jajahan_ladang, jajahan) as jajahan_nama'), DB::raw('count(*) as total'))
            ->groupBy('jajahan_nama')->pluck('total', 'jajahan_nama')->toArray();
        $naimbifByJajahan = [];
        foreach ($jajahanList as $j) {
            $naimbifByJajahan[$j] = (int) ($naimbifJajahanRaw[$j] ?? 0);
        }

        // 5. Klinik Haiwan & Rawatan
        $totalKlinikTemujanji = KlinikTemujanji::count();
        $totalKlinikSelesai = KlinikTemujanji::where('status', 'Selesai')->count();
        $totalKlinikDijadualkan = KlinikTemujanji::whereIn('status', ['Menunggu', 'Disahkan'])->count();
        $totalKlinikRawatan = KlinikRawatan::count();

        $klinikJajahanRaw = KlinikTemujanji::whereNotNull('klinik_jajahan')
            ->select('klinik_jajahan', DB::raw('count(*) as total'))
            ->groupBy('klinik_jajahan')->pluck('total', 'klinik_jajahan')->toArray();
        $klinikByJajahan = [];
        foreach ($jajahanList as $j) {
            $klinikByJajahan[$j] = (int) ($klinikJajahanRaw[$j] ?? 0);
        }

        $klinikSpeciesRaw = KlinikTemujanji::whereNotNull('jenis_haiwan')
            ->select('jenis_haiwan', DB::raw('count(*) as total'))
            ->groupBy('jenis_haiwan')->pluck('total', 'jenis_haiwan')->toArray();

        // 6. Kursus Penternakan
        $totalCourses = Course::count();
        $totalCoursesActive = Course::where('status', 'Buka')->count();
        $totalCourseApplications = CourseApplication::count();
        $totalCourseApproved = CourseApplication::whereIn('status', ['Lulus', 'Diluluskan'])->count();
        $totalCourseGraduated = CourseApplication::whereNotNull('certificate_number')->orWhereIn('status', ['Lulus', 'Hadir', 'Selesai'])->count();

        // 7. Stor & Inventori
        $totalPejabatItems = InventoriItem::where('jenis_stor', 'pejabat')->count();
        $totalUbatItems = InventoriItem::where('jenis_stor', 'ubat')->count();
        $lowStockPejabat = InventoriItem::where('jenis_stor', 'pejabat')->whereIn('status', ['Stok Rendah', 'Habis Stok'])->count();
        $lowStockUbat = InventoriItem::where('jenis_stor', 'ubat')->whereIn('status', ['Stok Rendah', 'Habis Stok'])->count();
        $totalPermohonanPejabat = InventoriPermohonan::where('jenis_stor', 'pejabat')->count();
        $totalPermohonanUbat = InventoriPermohonan::where('jenis_stor', 'ubat')->count();

        // 8. Armada Kenderaan Rasmi
        $totalVehicles = Kenderaan::count();
        $availableVehicles = Kenderaan::where('status', 'Sedia')->count();
        $inUseVehicles = Kenderaan::where('status', 'Sedang Digunakan')->count();
        $inServiceVehicles = Kenderaan::whereIn('status', ['Dalam Servis', 'Rosak'])->count();
        $totalVehicleBookings = KenderaanTempahan::count();
        $totalPemandu = Pemandu::count();
        $activePemandu = Pemandu::whereIn('status', ['Aktif', 'Bertugas'])->count();

        // 9. GIS Data & Totals
        $totalPremisGIS = $totalTernakanEptr + $totalEpuLadang + $totalNaimbifApps;

        return view('pengarah.laporan', compact(
            'user',
            'jajahanList',
            'totalTernakanEptr',
            'totalTernakanAktif',
            'totalTernakanPawah',
            'totalTernakanTagged',
            'totalTernakanUntagged',
            'totalPendingTernakan',
            'totalPermitSembelihan',
            'totalPembatalanKematian',
            'totalPindahMilik',
            'totalProgramKesihatan',
            'eptrSpeciesData',
            'eptrByJajahan',
            'totalPawahPerjanjian',
            'totalPawahAktif',
            'totalPawahMenunggu',
            'totalPawahSelesai',
            'totalPawahInduk',
            'totalPawahKelahiran',
            'totalPawahKelahiranHidup',
            'totalPawahKesihatan',
            'pawahByJajahan',
            'totalEpuLadang',
            'totalEpuPermohonan',
            'totalEpuLulus',
            'totalEpuPendingVerifikasi',
            'totalEpuPendingPelesen',
            'totalEpuRayuan',
            'totalEpuDitolak',
            'totalEpuKapasiti',
            'totalEpuSemasaUnggas',
            'totalEpuFiKutipan',
            'epuSpeciesRaw',
            'epuByJajahan',
            'pendingEpuForDirector',
            'totalNaimbifApps',
            'totalNaimbifLulus',
            'totalNaimbifMenungguNegeri',
            'totalNaimbifMenungguJajahan',
            'totalNaimbifTolak',
            'totalNaimbifPopulasi',
            'naimbifBakaRaw',
            'naimbifByJajahan',
            'totalKlinikTemujanji',
            'totalKlinikSelesai',
            'totalKlinikDijadualkan',
            'totalKlinikRawatan',
            'klinikByJajahan',
            'klinikSpeciesRaw',
            'totalCourses',
            'totalCoursesActive',
            'totalCourseApplications',
            'totalCourseApproved',
            'totalCourseGraduated',
            'totalPejabatItems',
            'totalUbatItems',
            'lowStockPejabat',
            'lowStockUbat',
            'totalPermohonanPejabat',
            'totalPermohonanUbat',
            'totalVehicles',
            'availableVehicles',
            'inUseVehicles',
            'inServiceVehicles',
            'totalVehicleBookings',
            'totalPemandu',
            'activePemandu',
            'totalPremisGIS'
        ));
    }
}
