<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pemunya;
use App\Models\KlinikTemujanji;
use App\Models\KlinikRawatan;
use App\Models\InventoriItem;
use App\Models\InventoriPermohonan;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Closure;
use Carbon\Carbon;

class KlinikController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            function (Request $request, Closure $next) {
                if (Auth::check()) {
                    $user = Auth::user();
                    if (in_array($user->role, ['admin_pejabat', 'admin_kenderaan'])) {
                        return redirect()->route($user->role === 'admin_kenderaan' ? 'kenderaan.index' : 'inventori.index')->with('error', 'Akses Ditolak: Peranan ' . $user->role_label . ' dikhaskan untuk Pengurusan Pentadbiran sahaja.');
                    }
                    if (!$user->canAccessKlinik()) {
                        abort(403, 'Akses Ditolak: Peranan ' . ($user->role_label ?? $user->role) . ' tidak dibenarkan mengakses modul Klinik Haiwan.');
                    }
                }
                return $next($request);
            }
        ];
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        // Admin Pejabat & Admin Kenderaan hanya dibenarkan menguruskan Pentadbiran
        if (in_array($user->role, ['admin_pejabat', 'admin_kenderaan'])) {
            return redirect()->route($user->role === 'admin_kenderaan' ? 'kenderaan.index' : 'inventori.index')->with('error', 'Akses Ditolak: Peranan ' . $user->role_label . ' dikhaskan untuk Pengurusan Pentadbiran sahaja.');
        }

        $query = KlinikTemujanji::with('pemilik', 'rawatan');

        if (!$user->isStaff()) {
            $query->where('user_id', $user->id);
        }

        // 1. Carian No. Temujanji
        if ($request->filled('no_temujanji')) {
            $query->where('no_temujanji', 'like', '%' . trim($request->no_temujanji) . '%');
        }

        // 2. Carian Kata Kunci Am (No. Temujanji, Nama Pemilik, No. KP, No. Telefon, Nama Haiwan, Baka, Simptom/Tujuan)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $searchClean = str_replace(['-', ' '], '', $search);
            $query->where(function ($q) use ($search, $searchClean) {
                $q->where('no_temujanji', 'like', "%{$search}%")
                  ->orWhere('simptom_atau_tujuan', 'like', "%{$search}%")
                  ->orWhere('nama_haiwan', 'like', "%{$search}%")
                  ->orWhere('baka', 'like', "%{$search}%")
                  ->orWhere('jenis_haiwan', 'like', "%{$search}%")
                  ->orWhere('klinik_jajahan', 'like', "%{$search}%")
                  ->orWhereHas('pemilik', function ($qp) use ($search, $searchClean) {
                      $qp->where('name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%")
                         ->orWhere('ic_number', 'like', "%{$search}%")
                         ->orWhere('ic_number', 'like', "%{$searchClean}%");
                  });
            });
        }

        // 3. Carian khusus mengikut No. Kad Pengenalan / Nama Pemohon
        if ($request->filled('no_kp')) {
            $noKp = trim($request->no_kp);
            $noKpClean = str_replace(['-', ' '], '', $noKp);
            $query->whereHas('pemilik', function ($qp) use ($noKp, $noKpClean) {
                $qp->where('ic_number', 'like', "%{$noKp}%")
                   ->orWhere('ic_number', 'like', "%{$noKpClean}%")
                   ->orWhere('name', 'like', "%{$noKp}%")
                   ->orWhere('phone', 'like', "%{$noKp}%");
            });
        }

        // 4. Jenis Haiwan
        if ($request->filled('jenis_haiwan')) {
            $query->where('jenis_haiwan', $request->jenis_haiwan);
        }

        // 5. Simptom / Tujuan Rawatan
        if ($request->filled('simptom')) {
            $query->where('simptom_atau_tujuan', 'like', '%' . trim($request->simptom) . '%');
        }

        // 6. Klinik Jajahan
        if ($request->filled('klinik_jajahan')) {
            $query->where('klinik_jajahan', $request->klinik_jajahan);
        }

        // 7. Tarikh Temujanji
        if ($request->filled('tarikh')) {
            $query->whereDate('tarikh_temujanji', $request->tarikh);
        }

        // 8. Status Rawatan
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $temujanjiList = $query->latest('tarikh_temujanji')->latest('id')->paginate(15)->withQueryString();
        $totalTemujanji = KlinikTemujanji::count();
        $totalSelesai = KlinikTemujanji::where('status', 'Selesai')->count();
        $totalRawatan = KlinikRawatan::count();

        $klinikList = [
            'Klinik Haiwan Ibu Pejabat JPVNK Kota Bharu',
            'Pusat Veterinar Jajahan Pasir Mas',
            'Pusat Veterinar Jajahan Bachok',
            'Pusat Veterinar Jajahan Machang',
            'Pusat Veterinar Jajahan Tanah Merah',
            'Pusat Veterinar Jajahan Pasir Puteh',
            'Pusat Veterinar Jajahan Tumpat',
            'Pusat Veterinar Jajahan Kuala Krai',
            'Pusat Veterinar Jajahan Gua Musang',
            'Pusat Veterinar Jajahan Jeli'
        ];

        $jenisHaiwanList = [
            'Kucing', 'Anjing', 'Lembu', 'Kambing', 'Biri-biri', 'Kuda', 'Unggas / Burung', 'Arnab', 'Lain-lain'
        ];

        return view('klinik.index', compact('temujanjiList', 'totalTemujanji', 'totalSelesai', 'totalRawatan', 'klinikList', 'jenisHaiwanList'));
    }

    public function create()
    {
        $klinikList = [
            'Klinik Haiwan Ibu Pejabat JPVNK Kota Bharu',
            'Pusat Veterinar Jajahan Pasir Mas',
            'Pusat Veterinar Jajahan Bachok',
            'Pusat Veterinar Jajahan Machang',
            'Pusat Veterinar Jajahan Tanah Merah',
            'Pusat Veterinar Jajahan Pasir Puteh',
            'Pusat Veterinar Jajahan Tumpat',
            'Pusat Veterinar Jajahan Kuala Krai',
            'Pusat Veterinar Jajahan Gua Musang',
            'Pusat Veterinar Jajahan Jeli'
        ];

        $registeredClients = [];
        $adminKlinikNama = null;
        if (Auth::user()->isStaff()) {
            $registeredClients = User::whereIn('role', ['orang_awam', 'penternak', 'usahawan'])
                ->orderBy('name')
                ->get();

            $adminJajahan = Auth::user()->jajahan ?: 'Kota Bharu';
            $adminKlinikNama = (strcasecmp($adminJajahan, 'Kota Bharu') === 0 || empty(Auth::user()->jajahan))
                ? 'Klinik Haiwan Ibu Pejabat JPVNK Kota Bharu'
                : 'Pusat Veterinar Jajahan ' . $adminJajahan;
        }

        return view('klinik.create', compact('klinikList', 'registeredClients', 'adminKlinikNama'));
    }

    /**
     * API Semakan No. Kad Pengenalan Pemilik untuk Auto-fill
     */
    public function semakPemilik(Request $request)
    {
        $ic = preg_replace('/[^0-9]/', '', $request->get('no_kp', $request->get('ic', '')));
        if (strlen($ic) < 6) {
            return response()->json(['found' => false]);
        }

        // 1. Cari dalam User
        $user = User::where('ic_number', $ic)
            ->orWhereRaw("REPLACE(REPLACE(ic_number, '-', ''), ' ', '') = ?", [$ic])
            ->first();

        // 2. Jika tiada, cari dalam Pemunya
        $pemunya = null;
        if (!$user) {
            $pemunya = Pemunya::where('no_kp', $ic)
                ->orWhereRaw("REPLACE(REPLACE(no_kp, '-', ''), ' ', '') = ?", [$ic])
                ->first();
            if ($pemunya && $pemunya->user_id) {
                $user = User::find($pemunya->user_id);
            }
        }

        // 3. Jika masih tiada, cari dalam rekod klinik temujanji terdahulu
        if (!$user && !$pemunya) {
            $pastTemujanji = KlinikTemujanji::with('pemilik')
                ->whereHas('pemilik', function($q) use ($ic) {
                    $q->where('ic_number', $ic)
                      ->orWhereRaw("REPLACE(REPLACE(ic_number, '-', ''), ' ', '') = ?", [$ic]);
                })
                ->latest()
                ->first();
            if ($pastTemujanji && $pastTemujanji->pemilik) {
                $user = $pastTemujanji->pemilik;
            }
        }

        if ($user || $pemunya) {
            return response()->json([
                'found' => true,
                'user_id' => $user?->id,
                'nama' => $user?->name ?? $pemunya?->nama,
                'no_kp' => $user?->ic_number ?? $pemunya?->no_kp ?? $ic,
                'no_telefon' => $user?->phone ?? $user?->no_telefon ?? $pemunya?->no_telefon ?? '',
                'emel' => $user?->email ?? '',
                'alamat' => $user?->address ?? $user?->alamat ?? $pemunya?->alamat ?? '',
                'jajahan' => $user?->jajahan ?? $pemunya?->jajahan ?? '',
                'role_label' => $user?->role_label ?? 'Pemilik / Penternak',
            ]);
        }

        return response()->json([
            'found' => false,
            'no_kp' => $ic,
        ]);
    }

    public function store(Request $request)
    {
        $isStaff = Auth::user()->isStaff();

        $rules = [
            'jenis_haiwan' => 'required|string',
            'nama_haiwan' => 'nullable|string|max:100',
            'baka' => 'nullable|string|max:100',
            'jantina_haiwan' => 'required|in:Jantan,Betina,Tidak Diketahui',
            'umur_haiwan' => 'nullable|string|max:50',
            'simptom_atau_tujuan' => 'required|string',
            'tarikh_temujanji' => 'required|date|after_or_equal:today',
            'sesi' => 'required|string',
            'klinik_jajahan' => $isStaff ? 'nullable|string' : 'required|string',
        ];

        if ($isStaff) {
            $rules['user_id'] = 'nullable|exists:users,id';
            $rules['no_kp_pemilik'] = 'nullable|string|max:30';
            $rules['nama_pemilik'] = 'nullable|string|max:255';
            $rules['no_telefon_pemilik'] = 'nullable|string|max:30';
            $rules['emel_pemilik'] = 'nullable|string|max:255';
            $rules['alamat_pemilik'] = 'nullable|string|max:500';
            $rules['jajahan_pemilik'] = 'nullable|string|max:100';
        }

        $validated = $request->validate($rules);

        // Tentukan klinik jajahan: jika staf/walk-in, gunakan klinik bertugas staf jika tidak dinyatakan
        $adminJajahan = Auth::user()->jajahan ?: 'Kota Bharu';
        $defaultKlinik = (strcasecmp($adminJajahan, 'Kota Bharu') === 0 || empty(Auth::user()->jajahan))
            ? 'Klinik Haiwan Ibu Pejabat JPVNK Kota Bharu'
            : 'Pusat Veterinar Jajahan ' . $adminJajahan;

        $klinikJajahan = !empty($validated['klinik_jajahan']) ? $validated['klinik_jajahan'] : $defaultKlinik;

        $userId = Auth::id();

        if (Auth::user()->isStaff()) {
            // 1. Jika admin pilih pengguna berdaftar sedia ada dari dropdown / user_id
            if (!empty($validated['user_id'])) {
                $userId = $validated['user_id'];
            }
            // 2. Jika admin masukkan No. Kad Pengenalan Pemilik
            elseif (!empty($validated['no_kp_pemilik'])) {
                $cleanIc = preg_replace('/[^0-9]/', '', $validated['no_kp_pemilik']);

                // Cari pengguna sedia ada mengikut IC
                $existingUser = User::where('ic_number', $cleanIc)
                    ->orWhereRaw("REPLACE(REPLACE(ic_number, '-', ''), ' ', '') = ?", [$cleanIc])
                    ->first();

                if (!$existingUser) {
                    $pemunya = Pemunya::where('no_kp', $cleanIc)
                        ->orWhereRaw("REPLACE(REPLACE(no_kp, '-', ''), ' ', '') = ?", [$cleanIc])
                        ->first();
                    if ($pemunya && $pemunya->user_id) {
                        $existingUser = User::find($pemunya->user_id);
                    }
                }

                if (!$existingUser && !empty($validated['emel_pemilik'])) {
                    $existingUser = User::where('email', $validated['emel_pemilik'])->first();
                }

                if ($existingUser) {
                    $userId = $existingUser->id;
                    // Kemaskini no telefon / alamat jika belum ada
                    $updateData = [];
                    if (empty($existingUser->phone) && !empty($validated['no_telefon_pemilik'])) {
                        $updateData['phone'] = $validated['no_telefon_pemilik'];
                    }
                    if (empty($existingUser->address) && !empty($validated['alamat_pemilik'])) {
                        $updateData['address'] = $validated['alamat_pemilik'];
                    }
                    if (!empty($updateData)) {
                        $existingUser->update($updateData);
                    }
                } else {
                    // Pendaftaran Pengguna Baharu Secara Automatik
                    $namaPemilik = !empty($validated['nama_pemilik']) ? $validated['nama_pemilik'] : ('Pemilik ' . ($cleanIc ?: 'Awam'));
                    $email = !empty($validated['emel_pemilik']) 
                        ? $validated['emel_pemilik'] 
                        : ('awam_' . ($cleanIc ?: rand(100000, 999999)) . '@awam.jpvnk.gov.my');

                    if (User::where('email', $email)->exists()) {
                        $email = 'awam_' . ($cleanIc ?: rand(100000, 999999)) . '_' . time() . '@awam.jpvnk.gov.my';
                    }

                    $newUser = User::create([
                        'name' => $namaPemilik,
                        'ic_number' => $cleanIc ?: null,
                        'email' => $email,
                        'password' => Hash::make(User::generateDefaultPassword($cleanIc)),
                        'phone' => $validated['no_telefon_pemilik'] ?? null,
                        'role' => 'orang_awam',
                        'jajahan' => $validated['jajahan_pemilik'] ?? $validated['klinik_jajahan'] ?? 'Kota Bharu',
                        'address' => $validated['alamat_pemilik'] ?? null,
                        'status' => 'Aktif',
                    ]);

                    $userId = $newUser->id;
                }
            }
        }

        $noTemujanji = 'TJ-' . strtoupper(substr($validated['jenis_haiwan'], 0, 3)) . '-' . date('Ymd') . '-' . rand(100, 999);

        $temujanji = KlinikTemujanji::create([
            'user_id' => $userId,
            'no_temujanji' => $noTemujanji,
            'jenis_haiwan' => $validated['jenis_haiwan'],
            'nama_haiwan' => $validated['nama_haiwan'] ?? null,
            'baka' => $validated['baka'] ?? null,
            'jantina_haiwan' => $validated['jantina_haiwan'] ?? 'Tidak Diketahui',
            'umur_haiwan' => $validated['umur_haiwan'] ?? null,
            'simptom_atau_tujuan' => $validated['simptom_atau_tujuan'],
            'tarikh_temujanji' => $validated['tarikh_temujanji'],
            'sesi' => $validated['sesi'],
            'klinik_jajahan' => $klinikJajahan,
            'status' => 'Disahkan',
            'catatan_pegawai' => 'Temujanji disahkan secara automatik. Sila hadir 15 minit awal.',
        ]);

        if ($userId) {
            \App\Models\UserNotification::send(
                $userId,
                'Temujanji Klinik Disahkan',
                "Temujanji bagi haiwan {$temujanji->jenis_haiwan} ({$temujanji->no_temujanji}) pada {$temujanji->tarikh_temujanji} ({$temujanji->sesi}) di {$temujanji->klinik_jajahan} telah disahkan.",
                'klinik',
                route('klinik.show', $temujanji->id),
                'fa-solid fa-stethoscope',
                'rose'
            );
        }

        // Catat ke Action List jika didaftarkan oleh Admin / Staf
        if (Auth::user()->isStaff()) {
            \App\Models\ActionList::catatAktiviti([
                'tajuk_aktiviti' => "Pendaftaran Temujanji Klinik Veterinar (#{$temujanji->no_temujanji})",
                'kategori_aktiviti' => 'Khidmat Rawatan & Klinikal',
                'maklumat_aktiviti' => "Mendaftar temujanji klinikal bagi haiwan {$temujanji->jenis_haiwan} (" . ($temujanji->pemilik->name ?? 'Pemilik') . ") untuk tujuan: {$temujanji->simptom_atau_tujuan}.",
                'jajahan' => Auth::user()->jajahan ?: 'Kota Bharu',
                'lokasi' => $temujanji->klinik_jajahan,
                'status' => 'Selesai',
            ]);
        }

        return redirect()->route('klinik.show', $temujanji->id)->with('success', 'Temujanji Klinik Haiwan berjaya didaftarkan.');
    }

    public function show($id)
    {
        $temujanji = KlinikTemujanji::with('pemilik', 'rawatan')->findOrFail($id);
        return view('klinik.show', compact('temujanji'));
    }

    // Rekod Rawatan Veterinar (Bagi Pegawai Veterinar / Admin)
    public function createRawatan($temujanjiId)
    {
        $temujanji = KlinikTemujanji::with('pemilik')->findOrFail($temujanjiId);
        return view('klinik.create-rawatan', compact('temujanji'));
    }

    public function storeRawatan(Request $request, $temujanjiId)
    {
        $temujanji = KlinikTemujanji::findOrFail($temujanjiId);

        $validated = $request->validate([
            'berat_badan_kg' => 'nullable|numeric',
            'suhu_celsius' => 'nullable|numeric',
            'diagnosis' => 'required|string',
            'rawatan_diberikan' => 'required|string',
            'ubat_diberikan' => 'nullable|string',
            'vaksinasi' => 'nullable|string',
            'tarikh_temujanji_susulan' => 'nullable|date',
            'kos_rawatan' => 'nullable|numeric',
            'nasihat_veterinar' => 'nullable|string',
        ]);

        $noRawatan = 'RAW-' . date('Y') . '-' . rand(1000, 9999);

        KlinikRawatan::create([
            'klinik_temujanji_id' => $temujanji->id,
            'user_id' => $temujanji->user_id,
            'no_rekod_rawatan' => $noRawatan,
            'tarikh_rawatan' => Carbon::now()->toDateString(),
            'pegawai_veterinar' => Auth::user()->name,
            'berat_badan_kg' => $validated['berat_badan_kg'] ?? null,
            'suhu_celsius' => $validated['suhu_celsius'] ?? null,
            'diagnosis' => $validated['diagnosis'],
            'rawatan_diberikan' => $validated['rawatan_diberikan'],
            'ubat_diberikan' => $validated['ubat_diberikan'] ?? null,
            'vaksinasi' => $validated['vaksinasi'] ?? null,
            'tarikh_temujanji_susulan' => $validated['tarikh_temujanji_susulan'] ?? null,
            'kos_rawatan' => $validated['kos_rawatan'] ?? 0.00,
            'status_bayaran' => 'Selesai Bayar',
            'nasihat_veterinar' => $validated['nasihat_veterinar'] ?? null,
        ]);

        $temujanji->status = 'Selesai';
        $temujanji->save();

        // Catat ke Action List
        \App\Models\ActionList::catatAktiviti([
            'tajuk_aktiviti' => "Penyelesaian Rawatan Klinikal Veterinar (#{$noRawatan})",
            'kategori_aktiviti' => 'Khidmat Rawatan & Klinikal',
            'maklumat_aktiviti' => "Memberikan rawatan pesakit veterinar bagi {$temujanji->jenis_haiwan} ({$temujanji->nama_haiwan}). Diagnosis: {$validated['diagnosis']}. Rawatan: {$validated['rawatan_diberikan']}. Ubat: " . ($validated['ubat_diberikan'] ?? 'Tiada') . ".",
            'jajahan' => Auth::user()->jajahan ?: 'Kota Bharu',
            'lokasi' => $temujanji->klinik_jajahan,
            'status' => 'Selesai',
        ]);

        return redirect()->route('klinik.show', $temujanji->id)->with('success', 'Rekod rawatan pesakit veterinar berjaya disimpan.');
    }

    public function cetakKadRawatan($id)
    {
        $temujanji = KlinikTemujanji::with('pemilik', 'rawatan')->findOrFail($id);
        return view('klinik.cetak-kad-rawatan', compact('temujanji'));
    }

    /**
     * Senarai Permohonan Ubat & Farmasi dari Klinik Haiwan
     */
    public function permohonanUbatIndex(Request $request)
    {
        $user = Auth::user();
        if (!$user->isStaff() || (!$user->canRequestUbat() && !$user->canAccessKlinik())) {
            abort(403, 'Akses Ditolak: Hanya staf klinik dan pegawai veterinar dibenarkan mengakses pengurusan permohonan ubat klinik.');
        }

        $query = InventoriPermohonan::where('jenis_stor', 'ubat')
            ->with(['pemohon', 'item', 'pelulus']);

        // Jika bukan super_admin atau admin_ubat, paparkan permohonan dibuat oleh staf klinik/pengguna semasa
        if (!$user->isSuperAdmin() && !$user->isAdminUbat()) {
            $query->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('unit_bahagian', 'like', '%Klinik%')
                  ->orWhere('unit_bahagian', 'like', '%' . ($user->jajahan ?? 'Kota Bharu') . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('no_permohonan', 'like', "%{$search}%")
                  ->orWhere('unit_bahagian', 'like', "%{$search}%")
                  ->orWhere('tujuan_permohonan', 'like', "%{$search}%")
                  ->orWhereHas('pemohon', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('item', function ($i) use ($search) {
                      $i->where('nama_item', 'like', "%{$search}%")->orWhere('kod_item', 'like', "%{$search}%");
                  });
            });
        }

        $permohonans = $query->latest()->paginate(15)->withQueryString();

        // Base query for counts
        $baseCountQuery = InventoriPermohonan::where('jenis_stor', 'ubat');
        if (!$user->isSuperAdmin() && !$user->isAdminUbat()) {
            $baseCountQuery->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('unit_bahagian', 'like', '%Klinik%')
                  ->orWhere('unit_bahagian', 'like', '%' . ($user->jajahan ?? 'Kota Bharu') . '%');
            });
        }

        $totalPermohonan = (clone $baseCountQuery)->count();
        $menungguCount = (clone $baseCountQuery)->where('status', 'Menunggu Kelulusan')->count();
        $lulusCount = (clone $baseCountQuery)->where('status', 'Diluluskan')->count();
        $selesaiCount = (clone $baseCountQuery)->where('status', 'Telah Diambil / Diserahkan')->count();
        $ditolakCount = (clone $baseCountQuery)->where('status', 'Ditolak')->count();

        return view('klinik.ubat.index', compact(
            'permohonans',
            'totalPermohonan',
            'menungguCount',
            'lulusCount',
            'selesaiCount',
            'ditolakCount'
        ));
    }

    /**
     * Borang Permohonan Ubat Baharu dari Klinik Haiwan ke Stor Farmasi
     */
    public function permohonanUbatCreate()
    {
        $user = Auth::user();
        if (!$user->isStaff() || (!$user->canRequestUbat() && !$user->canAccessKlinik())) {
            abort(403, 'Akses Ditolak: Hanya staf klinik dan pegawai veterinar dibenarkan membuat permohonan bekalan ubat.');
        }

        $items = InventoriItem::where('jenis_stor', 'ubat')
            ->where('status', '!=', 'Habis Stok')
            ->where('status', '!=', 'Luput')
            ->orderBy('nama_item')
            ->get();

        $userJajahan = $user->jajahan ?: 'Kota Bharu';
        $klinikNama = (strcasecmp($userJajahan, 'Kota Bharu') === 0 || empty($user->jajahan))
            ? 'Klinik Haiwan Ibu Pejabat JPVNK Kota Bharu'
            : 'Pusat Veterinar Jajahan ' . $userJajahan;

        // Senarai ubat yang telah ada / diterima di klinik haiwan jajahan tersebut
        $stokKlinikJajahan = InventoriPermohonan::where('jenis_stor', 'ubat')
            ->where(function($q) use ($user, $userJajahan, $klinikNama) {
                $q->where('unit_bahagian', 'like', "%{$userJajahan}%")
                  ->orWhere('unit_bahagian', $klinikNama)
                  ->orWhere('user_id', $user->id);
            })
            ->whereIn('status', ['Telah Diambil / Diserahkan', 'Diluluskan'])
            ->with(['item', 'pelulus'])
            ->latest()
            ->get();

        // Ringkaskan kuantiti & maklumat ubat mengikut item
        $ringkasanUbatKlinik = [];
        foreach ($stokKlinikJajahan as $permohonan) {
            $itemId = $permohonan->inventori_item_id;
            if (!$permohonan->item) continue;

            if (!isset($ringkasanUbatKlinik[$itemId])) {
                $ringkasanUbatKlinik[$itemId] = [
                    'item' => $permohonan->item,
                    'jumlah_diterima' => 0,
                    'tarikh_terakhir' => $permohonan->updated_at ?? $permohonan->created_at,
                    'status_terkini' => $permohonan->status,
                    'tujuan_terakhir' => $permohonan->tujuan_permohonan,
                    'bilangan_pesanan' => 0,
                ];
            }
            $kuantiti = $permohonan->kuantiti_diluluskan ?: $permohonan->kuantiti_dimohon;
            $ringkasanUbatKlinik[$itemId]['jumlah_diterima'] += $kuantiti;
            $ringkasanUbatKlinik[$itemId]['bilangan_pesanan']++;
        }

        return view('klinik.ubat.create', compact('items', 'klinikNama', 'userJajahan', 'ringkasanUbatKlinik'));
    }

    /**
     * Simpan Permohonan Ubat dari Klinik Haiwan
     */
    public function permohonanUbatStore(Request $request)
    {
        $user = Auth::user();
        if (!$user->isStaff() || (!$user->canRequestUbat() && !$user->canAccessKlinik())) {
            abort(403, 'Akses Ditolak: Hanya staf klinik dan pegawai veterinar dibenarkan membuat permohonan bekalan ubat.');
        }

        $userJajahan = $user->jajahan ?: 'Kota Bharu';
        $defaultKlinik = (strcasecmp($userJajahan, 'Kota Bharu') === 0 || empty($user->jajahan))
            ? 'Klinik Haiwan Ibu Pejabat JPVNK Kota Bharu'
            : 'Pusat Veterinar Jajahan ' . $userJajahan;

        $validated = $request->validate([
            'inventori_item_id' => 'required|exists:inventori_items,id',
            'kuantiti_dimohon' => 'required|integer|min:1',
            'klinik_jajahan' => 'nullable|string|max:255',
            'tujuan_permohonan' => 'required|string|max:1000',
            'tarikh_diperlukan' => 'nullable|date',
            'catatan_pemohon' => 'nullable|string|max:500',
        ]);

        $klinikJajahan = !empty($validated['klinik_jajahan']) ? $validated['klinik_jajahan'] : $defaultKlinik;

        $item = InventoriItem::findOrFail($validated['inventori_item_id']);
        if (!$item->isStorUbat()) {
            return back()->with('error', 'Item yang dipilih bukan daripada Stor Ubat & Farmasi Veterinar.');
        }

        $noPermohonan = 'REQ-KLN-UBT-' . date('Ymd') . '-' . rand(1000, 9999);

        $permohonan = InventoriPermohonan::create([
            'no_permohonan' => $noPermohonan,
            'user_id' => $user->id,
            'inventori_item_id' => $item->id,
            'jenis_stor' => 'ubat',
            'kuantiti_dimohon' => $validated['kuantiti_dimohon'],
            'unit_bahagian' => $klinikJajahan,
            'tujuan_permohonan' => $validated['tujuan_permohonan'],
            'tarikh_diperlukan' => $validated['tarikh_diperlukan'] ?? now()->toDateString(),
            'catatan_pemohon' => $validated['catatan_pemohon'] ?? null,
            'status' => 'Menunggu Kelulusan',
        ]);

        // Notifikasi kepada pemohon
        UserNotification::send(
            $user->id,
            'Permohonan Ubat Klinik Dihantar',
            "Permohonan bekalan ubat '{$item->nama_item}' ({$validated['kuantiti_dimohon']} {$item->unit}) bagi {$validated['klinik_jajahan']} telah berjaya dihantar ke Stor Ubat & Farmasi.",
            'klinik',
            route('klinik.permohonan_ubat.index'),
            'fa-solid fa-pills',
            'rose'
        );

        // Notifikasi kepada Admin Stor Ubat
        $adminUbatList = User::where(function($q) {
            $q->where('role', 'admin_ubat')
              ->orWhere('role', 'super_admin')
              ->orWhereJsonContains('roles', 'admin_ubat');
        })->get();

        foreach ($adminUbatList as $adminUbat) {
            UserNotification::send(
                $adminUbat->id,
                'Permohonan Ubat Baharu Dari Klinik Haiwan',
                "Permohonan ubat '{$item->nama_item}' ({$validated['kuantiti_dimohon']} {$item->unit}) diterima daripada {$user->name} ({$validated['klinik_jajahan']}).",
                'inventori',
                route('inventori.ubat.permohonan'),
                'fa-solid fa-pills',
                'rose'
            );
        }

        return redirect()->route('klinik.permohonan_ubat.index')->with('success', "Permohonan ubat '{$item->nama_item}' ({$noPermohonan}) telah berjaya dihantar kepada Stor Ubat & Farmasi.");
    }

    /**
     * Batal Permohonan Ubat dari Klinik
     */
    public function permohonanUbatBatal($id)
    {
        $permohonan = InventoriPermohonan::where('jenis_stor', 'ubat')->findOrFail($id);

        if ($permohonan->user_id !== Auth::id() && !Auth::user()->isSuperAdmin() && !Auth::user()->isAdminKlinik()) {
            abort(403, 'Akses Ditolak: Anda tidak dibenarkan membatalkan permohonan ini.');
        }

        if ($permohonan->status !== 'Menunggu Kelulusan') {
            return back()->with('error', 'Hanya permohonan yang berstatus Menunggu Kelulusan sahaja boleh dibatalkan.');
        }

        $permohonan->update([
            'status' => 'Dibatalkan',
            'catatan_pegawai' => 'Permohonan dibatalkan oleh pemohon klinik pada ' . now()->format('d/m/Y H:i'),
        ]);

        return back()->with('success', "Permohonan {$permohonan->no_permohonan} telah berjaya dibatalkan.");
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user || !$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Super Admin dibenarkan memadam rekod temujanji klinik haiwan.');
        }

        $temujanji = KlinikTemujanji::findOrFail($id);
        $noTemujanji = $temujanji->no_temujanji;

        // Delete child rawatan records
        $temujanji->rawatan()->delete();
        $temujanji->delete();

        return redirect()->route('klinik.index')->with('success', "Rekod temujanji '{$noTemujanji}' berjaya dipadam daripada sistem.");
    }
}
