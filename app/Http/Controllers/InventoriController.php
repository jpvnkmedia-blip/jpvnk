<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\InventoriItem;
use App\Models\InventoriTransaksi;
use App\Models\InventoriPinjaman;
use App\Models\InventoriPermohonan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Closure;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InventoriController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            function (Request $request, Closure $next) {
                if (Auth::check()) {
                    $user = Auth::user();
                    if (!$user->isStaff() || in_array($user->role, ['admin_program', 'admin_eptr', 'admin_epu', 'pegawai_pelesen', 'pegawai_verifikasi_epu'])) {
                        abort(403, 'Akses Ditolak: Peranan ' . ($user->role_label ?? $user->role) . ' tidak dibenarkan mengakses modul Inventori.');
                    }
                }
                return $next($request);
            }
        ];
    }

    // 1. Laluan Utama Inventori (Peralihan mengikut peranan pengguna)
    public function index()
    {
        $user = Auth::user();
        if ($user->canAccessStorPejabat() && !$user->canAccessStorUbat()) {
            return redirect()->route('inventori.pejabat.index');
        } elseif ($user->canAccessStorUbat() && !$user->canAccessStorPejabat()) {
            return redirect()->route('inventori.ubat.index');
        } elseif ($user->isStaff() && !$user->canAccessStorPejabat() && !$user->canAccessStorUbat()) {
            return redirect()->route('inventori.permohonan.saya');
        }

        // Jika Super Admin, lalai ke Stor Pejabat
        return redirect()->route('inventori.pejabat.index');
    }

    // ==========================================
    // 2. MODUL STOR PERALATAN PEJABAT
    // ==========================================
    public function indexPejabat(Request $request)
    {
        if (!Auth::user()->canAccessStorPejabat()) {
            abort(403, 'Akses Ditolak: Hanya Admin Pejabat dan Super Admin dibenarkan mengakses Stor Peralatan Pejabat.');
        }

        $query = InventoriItem::where('jenis_stor', 'pejabat');

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_item', 'like', "%{$search}%")
                  ->orWhere('kod_item', 'like', "%{$search}%")
                  ->orWhere('pembekal_utama', 'like', "%{$search}%")
                  ->orWhere('lokasi_rak', 'like', "%{$search}%");
            });
        }

        $items = $query->latest()->paginate(15);
        $totalItems = InventoriItem::where('jenis_stor', 'pejabat')->count();
        $totalNilaiStok = InventoriItem::where('jenis_stor', 'pejabat')->sum(DB::raw('kuantiti_semasa * harga_seunit'));
        $lowStockCount = InventoriItem::where('jenis_stor', 'pejabat')->where('status', 'Stok Rendah')->count();
        $outOfStockCount = InventoriItem::where('jenis_stor', 'pejabat')->where('status', 'Habis Stok')->count();
        $totalPinjamanAktif = InventoriPinjaman::whereHas('item', function ($q) {
            $q->where('jenis_stor', 'pejabat');
        })->where('status', 'Dipinjam')->count();
        $totalPermohonanMenunggu = InventoriPermohonan::where('jenis_stor', 'pejabat')->where('status', 'Menunggu Kelulusan')->count();

        $kategoriList = [
            'Alat Tulis & Pejabat',
            'Peralatan Pejabat & IT',
            'Perabot Pejabat',
            'Aset & Perkakasan',
            'Pembersihan & Sanitasi Pejabat',
        ];

        return view('inventori.pejabat.index', compact(
            'items',
            'totalItems',
            'totalNilaiStok',
            'lowStockCount',
            'outOfStockCount',
            'totalPinjamanAktif',
            'totalPermohonanMenunggu',
            'kategoriList'
        ));
    }

    public function createPejabat()
    {
        if (!Auth::user()->canAccessStorPejabat()) {
            abort(403, 'Akses Ditolak: Hanya Admin Pejabat dan Super Admin dibenarkan mendaftar barangan Stor Pejabat.');
        }

        $kategoriList = [
            'Alat Tulis & Pejabat',
            'Peralatan Pejabat & IT',
            'Perabot Pejabat',
            'Aset & Perkakasan',
            'Pembersihan & Sanitasi Pejabat',
        ];

        return view('inventori.pejabat.create', compact('kategoriList'));
    }

    public function storePejabat(Request $request)
    {
        if (!Auth::user()->canAccessStorPejabat()) {
            abort(403, 'Akses Ditolak: Hanya Admin Pejabat dan Super Admin dibenarkan mendaftar barangan Stor Pejabat.');
        }

        $validated = $request->validate([
            'kod_item' => 'required|string|max:50|unique:inventori_items,kod_item',
            'nama_item' => 'required|string|max:255',
            'kategori' => 'required|string',
            'unit' => 'required|string',
            'kuantiti_semasa' => 'required|integer|min:0',
            'kuantiti_minimum' => 'required|integer|min:1',
            'harga_seunit' => 'nullable|numeric|min:0',
            'pembekal_utama' => 'nullable|string|max:255',
            'lokasi_rak' => 'nullable|string|max:100',
            'jajahan' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
        ]);

        $item = InventoriItem::create([
            'kod_item' => $validated['kod_item'],
            'nama_item' => $validated['nama_item'],
            'jenis_stor' => 'pejabat',
            'kategori' => $validated['kategori'],
            'unit' => $validated['unit'],
            'kuantiti_semasa' => $validated['kuantiti_semasa'],
            'kuantiti_minimum' => $validated['kuantiti_minimum'],
            'harga_seunit' => $validated['harga_seunit'] ?? 0,
            'pembekal_utama' => $validated['pembekal_utama'] ?? null,
            'lokasi_rak' => $validated['lokasi_rak'] ?? 'Stor Pejabat JPVNK',
            'jajahan' => $validated['jajahan'] ?? 'Ibu Pejabat Kota Bharu',
            'status' => 'Mencukupi',
            'deskripsi' => $validated['deskripsi'],
        ]);

        $item->updateStatusStock();

        // Rekod transaksi masuk permulaan
        if ($validated['kuantiti_semasa'] > 0) {
            InventoriTransaksi::create([
                'inventori_item_id' => $item->id,
                'jenis_transaksi' => 'Stok Masuk',
                'kuantiti' => $validated['kuantiti_semasa'],
                'penerima_atau_pembekal' => $validated['pembekal_utama'] ?? 'Baki Permulaan Daftar Item',
                'rujukan_dokumen' => 'DAFTAR-PJB-' . date('Ymd'),
                'baki_selepas' => $validated['kuantiti_semasa'],
                'dikendalikan_oleh' => Auth::id(),
                'catatan' => 'Pendaftaran barangan baharu Stor Peralatan Pejabat.',
            ]);
        }

        return redirect()->route('inventori.pejabat.index')->with('success', "Barangan pejabat '{$item->nama_item}' ({$item->kod_item}) berjaya didaftarkan.");
    }

    // ==========================================
    // 3. MODUL STOR UBAT & VAKSIN VETERINAR
    // ==========================================
    public function indexUbat(Request $request)
    {
        if (!Auth::user()->canAccessStorUbat()) {
            abort(403, 'Akses Ditolak: Hanya Admin Stor Ubat dan Super Admin dibenarkan mengakses Stor Ubat & Vaksin Veterinar.');
        }

        $query = InventoriItem::where('jenis_stor', 'ubat');

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_item', 'like', "%{$search}%")
                  ->orWhere('kod_item', 'like', "%{$search}%")
                  ->orWhere('no_batch', 'like', "%{$search}%")
                  ->orWhere('pembekal_utama', 'like', "%{$search}%")
                  ->orWhere('lokasi_rak', 'like', "%{$search}%");
            });
        }

        $items = $query->latest()->paginate(15);
        $totalItems = InventoriItem::where('jenis_stor', 'ubat')->count();
        $totalNilaiStok = InventoriItem::where('jenis_stor', 'ubat')->sum(DB::raw('kuantiti_semasa * harga_seunit'));
        $lowStockCount = InventoriItem::where('jenis_stor', 'ubat')->where('status', 'Stok Rendah')->count();
        $outOfStockCount = InventoriItem::where('jenis_stor', 'ubat')->where('status', 'Habis Stok')->count();
        $expiredCount = InventoriItem::where('jenis_stor', 'ubat')->where(function($q) {
            $q->where('status', 'Luput')->orWhere('tarikh_luput', '<', now());
        })->count();
        $totalPermohonanMenunggu = InventoriPermohonan::where('jenis_stor', 'ubat')->where('status', 'Menunggu Kelulusan')->count();

        $kategoriList = [
            'Ubat-ubatan',
            'Vaksin',
            'Tag Telinga EPTR',
            'Aplikator & Peralatan Tagging',
            'Peralatan Surgeri & Klinik',
            'Antibiotik & Antiseptik',
            'Vitamin & Suplemen',
            'Reagen & Ujian Diagnostik',
        ];

        return view('inventori.ubat.index', compact(
            'items',
            'totalItems',
            'totalNilaiStok',
            'lowStockCount',
            'outOfStockCount',
            'expiredCount',
            'totalPermohonanMenunggu',
            'kategoriList'
        ));
    }

    public function createUbat()
    {
        if (!Auth::user()->canAccessStorUbat()) {
            abort(403, 'Akses Ditolak: Hanya Admin Stor Ubat dan Super Admin dibenarkan mendaftar ubat/vaksin.');
        }

        $kategoriList = [
            'Ubat-ubatan',
            'Vaksin',
            'Tag Telinga EPTR',
            'Aplikator & Peralatan Tagging',
            'Peralatan Surgeri & Klinik',
            'Antibiotik & Antiseptik',
            'Vitamin & Suplemen',
            'Reagen & Ujian Diagnostik',
        ];

        return view('inventori.ubat.create', compact('kategoriList'));
    }

    public function storeUbat(Request $request)
    {
        if (!Auth::user()->canAccessStorUbat()) {
            abort(403, 'Akses Ditolak: Hanya Admin Stor Ubat dan Super Admin dibenarkan mendaftar ubat/vaksin.');
        }

        $validated = $request->validate([
            'kod_item' => 'required|string|max:50|unique:inventori_items,kod_item',
            'nama_item' => 'required|string|max:255',
            'kategori' => 'required|string',
            'unit' => 'required|string',
            'kuantiti_semasa' => 'required|integer|min:0',
            'kuantiti_minimum' => 'required|integer|min:1',
            'harga_seunit' => 'nullable|numeric|min:0',
            'no_batch' => 'nullable|string|max:60',
            'tarikh_luput' => 'nullable|date',
            'suhu_simpanan' => 'nullable|string|max:100',
            'pembekal_utama' => 'nullable|string|max:255',
            'lokasi_rak' => 'nullable|string|max:100',
            'jajahan' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
        ]);

        $item = InventoriItem::create([
            'kod_item' => $validated['kod_item'],
            'nama_item' => $validated['nama_item'],
            'jenis_stor' => 'ubat',
            'kategori' => $validated['kategori'],
            'unit' => $validated['unit'],
            'kuantiti_semasa' => $validated['kuantiti_semasa'],
            'kuantiti_minimum' => $validated['kuantiti_minimum'],
            'harga_seunit' => $validated['harga_seunit'] ?? 0,
            'no_batch' => $validated['no_batch'] ?? null,
            'tarikh_luput' => $validated['tarikh_luput'] ?? null,
            'suhu_simpanan' => $validated['suhu_simpanan'] ?? 'Suhu Bilik Kering (< 25°C)',
            'pembekal_utama' => $validated['pembekal_utama'] ?? null,
            'lokasi_rak' => $validated['lokasi_rak'] ?? 'Bilik Farmasi & Stor Ubat JPVNK',
            'jajahan' => $validated['jajahan'] ?? 'Ibu Pejabat Kota Bharu',
            'status' => 'Mencukupi',
            'deskripsi' => $validated['deskripsi'],
        ]);

        $item->updateStatusStock();

        // Rekod transaksi masuk permulaan
        if ($validated['kuantiti_semasa'] > 0) {
            InventoriTransaksi::create([
                'inventori_item_id' => $item->id,
                'jenis_transaksi' => 'Stok Masuk',
                'kuantiti' => $validated['kuantiti_semasa'],
                'penerima_atau_pembekal' => $validated['pembekal_utama'] ?? 'Baki Permulaan Daftar Ubat',
                'rujukan_dokumen' => 'DAFTAR-UBT-' . date('Ymd'),
                'baki_selepas' => $validated['kuantiti_semasa'],
                'dikendalikan_oleh' => Auth::id(),
                'catatan' => 'Pendaftaran item farmaseutikal / ubat / vaksin baharu (Batch: ' . ($validated['no_batch'] ?? 'N/A') . ').',
            ]);
        }

        return redirect()->route('inventori.ubat.index')->with('success', "Item ubat/vaksin '{$item->nama_item}' ({$item->kod_item}) berjaya didaftarkan.");
    }

    // ==========================================
    // 4. PERMOHONAN STAF JABATAN (ALATAN PEJABAT & UBAT/VAKSIN)
    // ==========================================
    public function permohonanSaya(Request $request)
    {
        $user = Auth::user();

        if (!$user || (!$user->canRequestInventori() && !$user->canAccessStorPejabat())) {
            abort(403, 'Akses Ditolak: Modul Permohonan Stor Staf tidak dibenarkan bagi peranan anda.');
        }

        $query = InventoriPermohonan::where('user_id', $user->id)->with(['item', 'pelulus']);

        if ($request->filled('jenis_stor')) {
            $query->where('jenis_stor', $request->jenis_stor);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $permohonans = $query->latest()->paginate(15);
        $totalPermohonan = InventoriPermohonan::where('user_id', $user->id)->count();
        $menungguCount = InventoriPermohonan::where('user_id', $user->id)->where('status', 'Menunggu Kelulusan')->count();
        $lulusCount = InventoriPermohonan::where('user_id', $user->id)->where('status', 'Diluluskan')->count();
        $selesaiCount = InventoriPermohonan::where('user_id', $user->id)->where('status', 'Telah Diambil / Diserahkan')->count();

        return view('inventori.permohonan.index_saya', compact(
            'permohonans',
            'totalPermohonan',
            'menungguCount',
            'lulusCount',
            'selesaiCount'
        ));
    }

    public function createPermohonanPejabat()
    {
        if (!Auth::user()->canRequestAlatanPejabat()) {
            abort(403, 'Akses Ditolak: Permohonan alatan pejabat tidak dibenarkan bagi peranan anda.');
        }

        $items = InventoriItem::where('jenis_stor', 'pejabat')->where('status', '!=', 'Habis Stok')->orderBy('nama_item')->get();
        return view('inventori.permohonan.create_pejabat', compact('items'));
    }

    public function storePermohonanPejabat(Request $request)
    {
        if (!Auth::user()->canRequestAlatanPejabat()) {
            abort(403, 'Akses Ditolak: Permohonan alatan pejabat tidak dibenarkan bagi peranan anda.');
        }

        $validated = $request->validate([
            'inventori_item_id' => 'required|exists:inventori_items,id',
            'kuantiti_dimohon' => 'required|integer|min:1',
            'unit_bahagian' => 'required|string|max:150',
            'tujuan_permohonan' => 'required|string',
            'tarikh_diperlukan' => 'nullable|date',
            'catatan_pemohon' => 'nullable|string',
        ]);

        $item = InventoriItem::findOrFail($validated['inventori_item_id']);
        if (!$item->isStorPejabat()) {
            return back()->with('error', 'Item yang dipilih bukan daripada Stor Peralatan Pejabat.');
        }

        $noPermohonan = 'REQ-PJB-' . date('Ymd') . '-' . rand(1000, 9999);

        InventoriPermohonan::create([
            'no_permohonan' => $noPermohonan,
            'user_id' => Auth::id(),
            'inventori_item_id' => $item->id,
            'jenis_stor' => 'pejabat',
            'kuantiti_dimohon' => $validated['kuantiti_dimohon'],
            'unit_bahagian' => $validated['unit_bahagian'],
            'tujuan_permohonan' => $validated['tujuan_permohonan'],
            'tarikh_diperlukan' => $validated['tarikh_diperlukan'] ?? now()->toDateString(),
            'catatan_pemohon' => $validated['catatan_pemohon'] ?? null,
            'status' => 'Menunggu Kelulusan',
        ]);

        \App\Models\UserNotification::send(
            Auth::id(),
            'Permohonan Stor Pejabat Dihantar',
            "Permohonan alatan pejabat '{$item->nama_item}' (Kuantiti: {$validated['kuantiti_dimohon']}) telah dihantar dan sedang menunggu kelulusan.",
            'inventori',
            route('inventori.permohonan.saya'),
            'fa-solid fa-boxes-stacked',
            'indigo'
        );

        return redirect()->route('inventori.permohonan.saya')->with('success', "Permohonan alatan pejabat '{$item->nama_item}' ({$noPermohonan}) telah berjaya dihantar kepada Admin Pejabat.");
    }

    public function createPermohonanUbat()
    {
        if (!Auth::user()->canRequestUbat()) {
            abort(403, 'Akses Ditolak: Permohonan bekalan ubat dan vaksin hanya dibenarkan untuk Pegawai Veterinar Jajahan (Admin Jajahan).');
        }

        $items = InventoriItem::where('jenis_stor', 'ubat')
            ->where('status', '!=', 'Habis Stok')
            ->where('status', '!=', 'Luput')
            ->orderBy('nama_item')
            ->get();

        return view('inventori.permohonan.create_ubat', compact('items'));
    }

    public function storePermohonanUbat(Request $request)
    {
        if (!Auth::user()->canRequestUbat()) {
            abort(403, 'Akses Ditolak: Permohonan bekalan ubat dan vaksin hanya dibenarkan untuk Pegawai Veterinar Jajahan (Admin Jajahan).');
        }

        $validated = $request->validate([
            'inventori_item_id' => 'required|exists:inventori_items,id',
            'kuantiti_dimohon' => 'required|integer|min:1',
            'unit_bahagian' => 'required|string|max:150',
            'tujuan_permohonan' => 'required|string',
            'tarikh_diperlukan' => 'nullable|date',
            'catatan_pemohon' => 'nullable|string',
        ]);

        $item = InventoriItem::findOrFail($validated['inventori_item_id']);
        if (!$item->isStorUbat()) {
            return back()->with('error', 'Item yang dipilih bukan daripada Stor Ubat & Vaksin Veterinar.');
        }

        $noPermohonan = 'REQ-UBT-' . date('Ymd') . '-' . rand(1000, 9999);

        InventoriPermohonan::create([
            'no_permohonan' => $noPermohonan,
            'user_id' => Auth::id(),
            'inventori_item_id' => $item->id,
            'jenis_stor' => 'ubat',
            'kuantiti_dimohon' => $validated['kuantiti_dimohon'],
            'unit_bahagian' => $validated['unit_bahagian'],
            'tujuan_permohonan' => $validated['tujuan_permohonan'],
            'tarikh_diperlukan' => $validated['tarikh_diperlukan'] ?? now()->toDateString(),
            'catatan_pemohon' => $validated['catatan_pemohon'] ?? null,
            'status' => 'Menunggu Kelulusan',
        ]);

        \App\Models\UserNotification::send(
            Auth::id(),
            'Permohonan Bekalan Ubat Dihantar',
            "Permohonan bekalan ubat/vaksin '{$item->nama_item}' (Kuantiti: {$validated['kuantiti_dimohon']}) telah dihantar ke Stor Farmasi Pusat.",
            'inventori',
            route('inventori.permohonan.saya'),
            'fa-solid fa-pills',
            'rose'
        );

        return redirect()->route('inventori.permohonan.saya')->with('success', "Permohonan ubat/vaksin '{$item->nama_item}' ({$noPermohonan}) telah berjaya dihantar kepada Admin Stor Ubat.");
    }

    public function senaraiPermohonanPejabat(Request $request)
    {
        if (!Auth::user()->canManagePermohonanPejabat()) {
            abort(403, 'Akses Ditolak: Hanya Admin Pejabat dan Super Admin dibenarkan menguruskan permohonan Stor Pejabat.');
        }

        $query = InventoriPermohonan::where('jenis_stor', 'pejabat')->with(['pemohon', 'item', 'pelulus']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_permohonan', 'like', "%{$search}%")
                  ->orWhere('unit_bahagian', 'like', "%{$search}%")
                  ->orWhereHas('pemohon', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('item', function ($i) use ($search) {
                      $i->where('nama_item', 'like', "%{$search}%")->orWhere('kod_item', 'like', "%{$search}%");
                  });
            });
        }

        $permohonans = $query->latest()->paginate(15);
        $totalPermohonan = InventoriPermohonan::where('jenis_stor', 'pejabat')->count();
        $menungguCount = InventoriPermohonan::where('jenis_stor', 'pejabat')->where('status', 'Menunggu Kelulusan')->count();
        $lulusCount = InventoriPermohonan::where('jenis_stor', 'pejabat')->where('status', 'Diluluskan')->count();
        $selesaiCount = InventoriPermohonan::where('jenis_stor', 'pejabat')->where('status', 'Telah Diambil / Diserahkan')->count();

        return view('inventori.pejabat.permohonan', compact(
            'permohonans',
            'totalPermohonan',
            'menungguCount',
            'lulusCount',
            'selesaiCount'
        ));
    }

    public function senaraiPermohonanUbat(Request $request)
    {
        if (!Auth::user()->canManagePermohonanUbat()) {
            abort(403, 'Akses Ditolak: Hanya Admin Stor Ubat dan Super Admin dibenarkan menguruskan permohonan Stor Ubat Veterinar.');
        }

        $query = InventoriPermohonan::where('jenis_stor', 'ubat')->with(['pemohon', 'item', 'pelulus']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_permohonan', 'like', "%{$search}%")
                  ->orWhere('unit_bahagian', 'like', "%{$search}%")
                  ->orWhereHas('pemohon', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('item', function ($i) use ($search) {
                      $i->where('nama_item', 'like', "%{$search}%")->orWhere('kod_item', 'like', "%{$search}%");
                  });
            });
        }

        $permohonans = $query->latest()->paginate(15);
        $totalPermohonan = InventoriPermohonan::where('jenis_stor', 'ubat')->count();
        $menungguCount = InventoriPermohonan::where('jenis_stor', 'ubat')->where('status', 'Menunggu Kelulusan')->count();
        $lulusCount = InventoriPermohonan::where('jenis_stor', 'ubat')->where('status', 'Diluluskan')->count();
        $selesaiCount = InventoriPermohonan::where('jenis_stor', 'ubat')->where('status', 'Telah Diambil / Diserahkan')->count();

        return view('inventori.ubat.permohonan', compact(
            'permohonans',
            'totalPermohonan',
            'menungguCount',
            'lulusCount',
            'selesaiCount'
        ));
    }

    // Kelulusan / Penolakan / Penyerahan Stok Permohonan
    public function updateStatusPermohonan(Request $request, $id)
    {
        $permohonan = InventoriPermohonan::with(['item', 'pemohon'])->findOrFail($id);

        if ($permohonan->isStorPejabat() && !Auth::user()->canManagePermohonanPejabat()) {
            abort(403, 'Akses Ditolak: Anda tidak dibenarkan menguruskan permohonan Stor Pejabat.');
        }

        if ($permohonan->isStorUbat() && !Auth::user()->canManagePermohonanUbat()) {
            abort(403, 'Akses Ditolak: Anda tidak dibenarkan menguruskan permohonan Stor Ubat.');
        }

        $validated = $request->validate([
            'tindakan' => 'required|in:lulus,tolak,serah',
            'kuantiti_diluluskan' => 'nullable|integer|min:1',
            'catatan_pegawai' => 'nullable|string',
        ]);

        $item = $permohonan->item;

        if ($validated['tindakan'] === 'lulus') {
            $kuantitiDiluluskan = $validated['kuantiti_diluluskan'] ?? $permohonan->kuantiti_dimohon;
            $permohonan->update([
                'status' => 'Diluluskan',
                'kuantiti_diluluskan' => $kuantitiDiluluskan,
                'catatan_pegawai' => $validated['catatan_pegawai'] ?? 'Permohonan diluluskan. Sedia untuk serahan stok.',
                'disahkan_oleh' => Auth::id(),
                'tarikh_kelulusan' => now(),
            ]);

            return back()->with('success', "Permohonan {$permohonan->no_permohonan} berjaya DILULUSKAN ({$kuantitiDiluluskan} {$item->unit}).");
        } elseif ($validated['tindakan'] === 'tolak') {
            $permohonan->update([
                'status' => 'Ditolak',
                'catatan_pegawai' => $validated['catatan_pegawai'] ?? 'Permohonan tidak dapat diluluskan atas faktor kekangan stok/keperluan.',
                'disahkan_oleh' => Auth::id(),
                'tarikh_kelulusan' => now(),
            ]);

            return back()->with('success', "Permohonan {$permohonan->no_permohonan} telah DITOLAK.");
        } elseif ($validated['tindakan'] === 'serah') {
            $kuantitiSerah = $validated['kuantiti_diluluskan'] ?? ($permohonan->kuantiti_diluluskan ?? $permohonan->kuantiti_dimohon);

            if ($item->kuantiti_semasa < $kuantitiSerah) {
                return back()->with('error', "Gagal serah: Baki stok semasa ({$item->kuantiti_semasa} {$item->unit}) tidak mencukupi untuk penyerahan {$kuantitiSerah} {$item->unit}.");
            }

            // Tolak baki stok inventori
            $item->kuantiti_semasa -= $kuantitiSerah;
            $item->updateStatusStock();

            // Cipta rekod Stok Keluar dalam lejar
            InventoriTransaksi::create([
                'inventori_item_id' => $item->id,
                'jenis_transaksi' => 'Stok Keluar',
                'kuantiti' => $kuantitiSerah,
                'penerima_atau_pembekal' => $permohonan->pemohon->name . ' (' . ($permohonan->unit_bahagian ?? 'Staf JPVNK') . ')',
                'rujukan_dokumen' => $permohonan->no_permohonan,
                'baki_selepas' => $item->kuantiti_semasa,
                'dikendalikan_oleh' => Auth::id(),
                'catatan' => 'Serahan bekalan permohonan staf: ' . $permohonan->tujuan_permohonan,
            ]);

            $permohonan->update([
                'status' => 'Telah Diambil / Diserahkan',
                'kuantiti_diluluskan' => $kuantitiSerah,
                'catatan_pegawai' => $validated['catatan_pegawai'] ?? 'Stok telah diserahkan sepenuhnya kepada pemohon.',
                'disahkan_oleh' => Auth::id(),
                'tarikh_kelulusan' => now(),
            ]);

            return back()->with('success', "Stok sebanyak {$kuantitiSerah} {$item->unit} telah diserahkan kepada {$permohonan->pemohon->name}. Baki inventori dikemas kini automatik ({$item->kuantiti_semasa} {$item->unit}).");
        }

        return back();
    }

    public function batalPermohonan($id)
    {
        $permohonan = InventoriPermohonan::findOrFail($id);

        if ($permohonan->user_id !== Auth::id() && !Auth::user()->isSuperAdmin()) {
            abort(403, 'Akses Ditolak.');
        }

        if ($permohonan->status !== 'Menunggu Kelulusan') {
            return back()->with('error', 'Hanya permohonan berstatus Menunggu Kelulusan yang boleh dibatalkan.');
        }

        $no = $permohonan->no_permohonan;
        $permohonan->delete();

        return back()->with('success', "Permohonan {$no} telah berjaya dibatalkan.");
    }

    // ==========================================
    // 5. MAKLUMAT ITEM & TRANSAKSI KELUAR MASUK
    // ==========================================
    public function show($id)
    {
        $item = InventoriItem::with(['transaksi.pengendali', 'pinjaman.peminjam', 'permohonan.pemohon'])->findOrFail($id);

        if ($item->isStorPejabat() && !Auth::user()->canAccessStorPejabat()) {
            abort(403, 'Akses Ditolak: Anda tidak dibenarkan melihat rekod Stor Peralatan Pejabat.');
        }

        if ($item->isStorUbat() && !Auth::user()->canAccessStorUbat()) {
            abort(403, 'Akses Ditolak: Anda tidak dibenarkan melihat rekod Stor Ubat & Vaksin Veterinar.');
        }

        return view('inventori.show', compact('item'));
    }

    // Transaksi Keluar Masuk Stok
    public function storeTransaksi(Request $request, $id)
    {
        $item = InventoriItem::findOrFail($id);

        if ($item->isStorPejabat() && !Auth::user()->canAccessStorPejabat()) {
            abort(403, 'Akses Ditolak: Anda tidak dibenarkan menguruskan transaksi Stor Peralatan Pejabat.');
        }

        if ($item->isStorUbat() && !Auth::user()->canAccessStorUbat()) {
            abort(403, 'Akses Ditolak: Anda tidak dibenarkan menguruskan transaksi Stor Ubat Veterinar.');
        }

        $validated = $request->validate([
            'jenis_transaksi' => 'required|in:Stok Masuk,Stok Keluar,Pelupusan,Penyelarasan Kiraan',
            'kuantiti' => 'required|integer|min:1',
            'penerima_atau_pembekal' => 'required|string|max:255',
            'rujukan_dokumen' => 'nullable|string|max:100',
            'catatan' => 'nullable|string',
        ]);

        if (in_array($validated['jenis_transaksi'], ['Stok Keluar', 'Pelupusan']) && $item->kuantiti_semasa < $validated['kuantiti']) {
            return back()->with('error', "Gagal: Kuantiti stok semasa ({$item->kuantiti_semasa} {$item->unit}) tidak mencukupi untuk membuat pengeluaran {$validated['kuantiti']} {$item->unit}.");
        }

        if ($validated['jenis_transaksi'] === 'Stok Masuk') {
            $item->kuantiti_semasa += $validated['kuantiti'];
        } elseif ($validated['jenis_transaksi'] === 'Penyelarasan Kiraan') {
            $item->kuantiti_semasa = $validated['kuantiti'];
        } else {
            $item->kuantiti_semasa -= $validated['kuantiti'];
        }

        $item->updateStatusStock();

        InventoriTransaksi::create([
            'inventori_item_id' => $item->id,
            'jenis_transaksi' => $validated['jenis_transaksi'],
            'kuantiti' => $validated['kuantiti'],
            'penerima_atau_pembekal' => $validated['penerima_atau_pembekal'],
            'rujukan_dokumen' => $validated['rujukan_dokumen'] ?? null,
            'baki_selepas' => $item->kuantiti_semasa,
            'dikendalikan_oleh' => Auth::id(),
            'catatan' => $validated['catatan'] ?? null,
        ]);

        return redirect()->route('inventori.show', $item->id)->with('success', "Transaksi '{$validated['jenis_transaksi']}' sebanyak {$validated['kuantiti']} {$item->unit} berjaya direkodkan. Baki semasa: {$item->kuantiti_semasa} {$item->unit}.");
    }

    // Pinjaman Peralatan Pejabat / Klinik
    public function storePinjaman(Request $request, $id)
    {
        $item = InventoriItem::findOrFail($id);

        if ($item->isStorPejabat() && !Auth::user()->canAccessStorPejabat()) {
            abort(403, 'Akses Ditolak: Anda tidak dibenarkan menguruskan pinjaman Stor Peralatan Pejabat.');
        }

        if ($item->isStorUbat() && !Auth::user()->canAccessStorUbat()) {
            abort(403, 'Akses Ditolak: Anda tidak dibenarkan menguruskan pinjaman Stor Ubat Veterinar.');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'kuantiti' => 'required|integer|min:1',
            'tujuan_pinjaman' => 'required|string',
            'tarikh_pinjam' => 'required|date',
            'tarikh_jangka_pulang' => 'required|date|after_or_equal:tarikh_pinjam',
        ]);

        if ($item->kuantiti_semasa < $validated['kuantiti']) {
            return back()->with('error', "Gagal: Kuantiti item tidak mencukupi untuk dipinjam (Baki: {$item->kuantiti_semasa} {$item->unit}).");
        }

        $item->kuantiti_semasa -= $validated['kuantiti'];
        $item->updateStatusStock();

        InventoriPinjaman::create([
            'inventori_item_id' => $item->id,
            'user_id' => $validated['user_id'],
            'kuantiti' => $validated['kuantiti'],
            'tujuan_pinjaman' => $validated['tujuan_pinjaman'],
            'tarikh_pinjam' => $validated['tarikh_pinjam'],
            'tarikh_jangka_pulang' => $validated['tarikh_jangka_pulang'],
            'keadaan_semasa_pinjam' => 'Baik / Berfungsi',
            'status' => 'Dipinjam',
            'disahkan_oleh' => Auth::id(),
        ]);

        return redirect()->route('inventori.show', $item->id)->with('success', 'Rekod pinjaman peralatan berjaya direkodkan.');
    }

    // Pemulangan Pinjaman
    public function pulangPinjaman(Request $request, $id)
    {
        $pinjaman = InventoriPinjaman::with('item')->findOrFail($id);
        $item = $pinjaman->item;

        if ($item->isStorPejabat() && !Auth::user()->canAccessStorPejabat()) {
            abort(403, 'Akses Ditolak.');
        }

        if ($item->isStorUbat() && !Auth::user()->canAccessStorUbat()) {
            abort(403, 'Akses Ditolak.');
        }

        $item->kuantiti_semasa += $pinjaman->kuantiti;
        $item->updateStatusStock();

        $pinjaman->update([
            'status' => 'Telah Dipulangkan',
            'tarikh_pulang_sebenar' => now()->toDateString(),
            'keadaan_semasa_pulang' => $request->input('keadaan_semasa_pulang', 'Baik / Lengkap'),
            'catatan' => $request->input('catatan', 'Telah dipulangkan kepada pengurus stor.'),
        ]);

        return back()->with('success', "Peralatan '{$item->nama_item}' telah berjaya disahkan pemulangan. Stok telah dikembalikan.");
    }

    public function destroy($id)
    {
        $item = InventoriItem::findOrFail($id);

        if ($item->isStorPejabat() && !Auth::user()->canAccessStorPejabat()) {
            abort(403, 'Akses Ditolak.');
        }

        if ($item->isStorUbat() && !Auth::user()->canAccessStorUbat()) {
            abort(403, 'Akses Ditolak.');
        }

        $itemTitle = $item->nama_item;
        $storType = $item->jenis_stor;
        $item->delete();

        $route = $storType === 'ubat' ? 'inventori.ubat.index' : 'inventori.pejabat.index';
        return redirect()->route($route)->with('success', "Item '{$itemTitle}' berjaya dipadam daripada pangkalan data stor.");
    }
}

