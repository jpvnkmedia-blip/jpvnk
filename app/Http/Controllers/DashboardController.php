<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ternakan;
use App\Models\PawahPerjanjian;
use App\Models\PawahTernakan;
use App\Models\PawahRekodKelahiran;
use App\Models\PawahRekodKesihatan;
use App\Models\EpuLadang;
use App\Models\EpuPermohonan;
use App\Models\Course;
use App\Models\CourseApplication;
use App\Models\KlinikTemujanji;
use App\Models\InventoriItem;
use App\Models\Kenderaan;
use App\Models\KenderaanTempahan;
use App\Models\PermitSembelihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            $totalInventoryItems = InventoriItem::count();
            $lowStockInventoryCount = InventoriItem::where('status', 'Stok Rendah')->orWhere('status', 'Habis Stok')->count();
            $totalVehicles = Kenderaan::count();
            $availableVehicles = Kenderaan::where('status', 'Sedia')->count();
            $pendingVehicleBookings = KenderaanTempahan::where('status', 'Menunggu')->count();
            $totalUsers = User::count();

            $recentPawah = PawahPerjanjian::with('peserta', 'ternakanList')->latest()->take(5)->get();
            $recentEpu = (clone $epuQuery)->with('ladang.pemilik', 'pegawaiVerifikasi')->latest()->take(8)->get();
            $recentTemujanji = KlinikTemujanji::with('pemilik')->latest()->take(5)->get();
            $recentTempahanKenderaan = KenderaanTempahan::with('pemohon', 'kenderaan')->latest()->take(5)->get();
            $recentSembelehan = PermitSembelihan::with('pemunya', 'ternakan')->latest()->take(5)->get();
            $recentInventory = InventoriItem::latest()->take(6)->get();
            $recentCourses = Course::withCount('applications')->latest()->take(6)->get();
        } else {
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
            $totalVehicles = 0;
            $availableVehicles = 0;
            $pendingVehicleBookings = 0;
            $totalUsers = 1;

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
            'totalVehicles',
            'availableVehicles',
            'pendingVehicleBookings',
            'totalUsers',
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
            'recentCourses'
        ));
    }
}
