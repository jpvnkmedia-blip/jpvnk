<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\EpuLadang;
use App\Models\EpuPermohonan;
use App\Models\EpuPemeriksaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Closure;
use Carbon\Carbon;

class EpuController extends Controller implements HasMiddleware
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
                    if (!$user->canAccessEpu()) {
                        abort(403, 'Akses Ditolak: Peranan ' . ($user->role_label ?? $user->role) . ' tidak dibenarkan mengakses modul EPU (Perladangan Unggas).');
                    }
                }
                return $next($request);
            }
        ];
    }

    // Senarai Ladang & Lesen Unggas EPU
    public function index(Request $request)
    {
        $user = Auth::user();

        // Admin Pejabat & Admin Kenderaan hanya dibenarkan menguruskan Pentadbiran
        if (in_array($user->role, ['admin_pejabat', 'admin_kenderaan'])) {
            return redirect()->route($user->role === 'admin_kenderaan' ? 'kenderaan.index' : 'inventori.index')->with('error', 'Akses Ditolak: Peranan ' . $user->role_label . ' dikhaskan untuk Pengurusan Pentadbiran sahaja.');
        }

        $query = EpuLadang::with('pemilik', 'permohonanList', 'pemeriksaanList');

        if (!$user->isStaff()) {
            $query->where('user_id', $user->id);
        } elseif (in_array($user->role, ['admin_epu_jajahan', 'pegawai_verifikasi_epu']) && !empty($user->jajahan)) {
            $query->where('jajahan', $user->jajahan);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_ladang', 'like', "%{$search}%")
                  ->orWhere('nama_pemohon_atau_syarikat', 'like', "%{$search}%")
                  ->orWhere('no_syarikat_atau_ssm', 'like', "%{$search}%");
            });
        }

        if ($request->filled('jajahan')) {
            $query->where('jajahan', $request->jajahan);
        }

        if ($request->filled('sistem_reban')) {
            $query->where('sistem_reban', $request->sistem_reban);
        }

        $ladangList = $query->latest()->paginate(15);

        // KPI Metrics (Disesuaikan mengikut skop Jajahan jika Pegawai Jajahan)
        $ladangCountQuery = EpuLadang::query();
        $permohonanCountQuery = EpuPermohonan::query();
        $pemeriksaanCountQuery = EpuPemeriksaan::query();

        if (!$user->isStaff()) {
            $ladangCountQuery->where('user_id', $user->id);
            $permohonanCountQuery->whereHas('ladang', fn($q) => $q->where('user_id', $user->id));
            $pemeriksaanCountQuery->whereHas('ladang', fn($q) => $q->where('user_id', $user->id));
        } elseif (in_array($user->role, ['admin_epu_jajahan', 'pegawai_verifikasi_epu']) && !empty($user->jajahan)) {
            $ladangCountQuery->where('jajahan', $user->jajahan);
            $permohonanCountQuery->whereHas('ladang', fn($q) => $q->where('jajahan', $user->jajahan));
            $pemeriksaanCountQuery->whereHas('ladang', fn($q) => $q->where('jajahan', $user->jajahan));
        }

        $totalLadang = $ladangCountQuery->count();
        $totalLesenAktif = (clone $permohonanCountQuery)->where('status', 'Diluluskan')->count();
        $totalRebanTertutup = (clone $ladangCountQuery)->where('sistem_reban', 'Tertutup')->count();
        $totalPemeriksaan = $pemeriksaanCountQuery->count();

        return view('epu.index', compact(
            'ladangList',
            'totalLadang',
            'totalLesenAktif',
            'totalRebanTertutup',
            'totalPemeriksaan'
        ));
    }

    // EPU Borang A: Permohonan Pendaftaran & Lesen Ladang Unggas
    public function create()
    {
        $user = Auth::user();
        $usahawanList = User::whereIn('role', ['usahawan', 'penternak', 'orang_awam'])->orderBy('name')->get();
        
        $jajahanList = [
            'Kota Bharu', 'Pasir Mas', 'Tumpat', 'Bachok', 'Pasir Puteh',
            'Machang', 'Tanah Merah', 'Jeli', 'Kuala Krai', 'Gua Musang'
        ];

        $jenisUnggasList = [
            'Ayam',
            'Itik',
            'Puyuh',
            'Merpati'
        ];

        $jurusanAktivitiList = [
            'Baka',
            'Pedaging',
            'Penelur'
        ];

        return view('epu.borang-a', compact('usahawanList', 'jajahanList', 'jenisUnggasList', 'jurusanAktivitiList', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            // Step 1: Maklumat Pemohon
            'nama_pemohon_atau_syarikat' => 'required|string|max:255',
            'no_syarikat_atau_ssm' => 'nullable|string|max:50',
            'email_pemohon' => 'nullable|email|max:255',
            'phone_pemohon' => 'nullable|string|max:25',
            'fax_pemohon' => 'nullable|string|max:25',

            // Step 2: Maklumat Penternakan / Ladang
            'jenis_unggas' => 'required|string|in:Ayam,Itik,Puyuh,Merpati',
            'jurusan_aktiviti' => 'required|string|in:Baka,Pedaging,Penelur',
            'nama_ladang' => 'nullable|string|max:255',
            'id_premis' => 'nullable|string|max:50',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'alamat_ladang' => 'required|string',
            'poskod' => 'nullable|string|max:10',
            'negeri' => 'nullable|string|max:50',
            'jajahan' => 'required|string',
            'daerah' => 'nullable|string|max:50',
            'luas_kawasan_sqft' => 'nullable|numeric',
            'luas_tanah_ekar' => 'nullable|numeric',
            'status_pemilikan_tanah' => 'nullable|string',
            'sistem_reban' => 'nullable|in:Tertutup,Terbuka,Semi-Tertutup',
            'reban_data' => 'nullable',
            'kapasiti_maksimum_unggas' => 'nullable|integer',
            'bilangan_semasa_unggas' => 'nullable|integer',
            'yuran_lesen' => 'nullable|numeric',
            'alamat_premis_perniagaan' => 'nullable|string',
            'poskod_premis_perniagaan' => 'nullable|string|max:10',
            'negeri_premis_perniagaan' => 'nullable|string|max:50',
            
            // Pengecualian Lesen
            'mohon_pengecualian' => 'nullable|boolean',
            'sebab_pengecualian' => 'nullable|string',
            'sebab_pengecualian_lain' => 'nullable|string',
            'lampiran_pengecualian' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',

            // Step 3: Lampiran
            'dokumen_pelan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'dokumen_tanah' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'dokumen_pbt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'dokumen_ssm' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        // Process Reban Data JSON
        $rebanData = null;
        if ($request->filled('reban_data')) {
            $rebanData = is_string($request->reban_data) ? json_decode($request->reban_data, true) : $request->reban_data;
        }

        // File uploads handling safely
        $pathPengecualian = $this->uploadFileSafely($request->file('lampiran_pengecualian'), 'epu/pengecualian');
        $pathPelan = $this->uploadFileSafely($request->file('dokumen_pelan'), 'epu/dokumen');
        $pathTanah = $this->uploadFileSafely($request->file('dokumen_tanah'), 'epu/dokumen');
        $pathPbt = $this->uploadFileSafely($request->file('dokumen_pbt'), 'epu/dokumen');
        $pathSsm = $this->uploadFileSafely($request->file('dokumen_ssm'), 'epu/dokumen');

        $namaLadang = $validated['nama_ladang'] ?? ('Ladang ' . $validated['jenis_unggas'] . ' - ' . $validated['nama_pemohon_atau_syarikat']);
        $idPremis = $validated['id_premis'] ?? ('PRM-' . strtoupper(substr($validated['jajahan'], 0, 3)) . '-' . rand(100, 999));
        $kapasiti = (int) ($validated['kapasiti_maksimum_unggas'] ?? 1000);
        $bilanganSemasa = (int) ($validated['bilangan_semasa_unggas'] ?? 0);
        
        // Ayam & Itik: <= 500 percuma; Puyuh & Merpati: <= 1000 percuma
        $isFree = (($validated['jenis_unggas'] === 'Ayam' || $validated['jenis_unggas'] === 'Itik') && $kapasiti <= 500)
               || (($validated['jenis_unggas'] === 'Puyuh' || $validated['jenis_unggas'] === 'Merpati') && $kapasiti <= 1000);

        $yuranLesen = $request->filled('yuran_lesen')
            ? (float) $request->input('yuran_lesen')
            : ($isFree ? 0.00 : ($kapasiti > 20000 ? 500.00 : ($kapasiti > 5000 ? 200.00 : 100.00)));

        $ladang = EpuLadang::create([
            'user_id' => $user->isStaff() ? ($request->input('user_id', $user->id)) : $user->id,
            'nama_pemohon_atau_syarikat' => $validated['nama_pemohon_atau_syarikat'],
            'no_syarikat_atau_ssm' => $validated['no_syarikat_atau_ssm'] ?? null,
            'nama_ladang' => $namaLadang,
            'id_premis' => $idPremis,
            'no_geran_tanah' => $request->input('no_geran_tanah'),
            'no_lot' => $request->input('no_lot'),
            'luas_tanah_ekar' => $validated['luas_tanah_ekar'] ?? 1.0,
            'luas_kawasan_sqft' => $validated['luas_kawasan_sqft'] ?? null,
            'jajahan' => $validated['jajahan'],
            'daerah' => $validated['daerah'] ?? null,
            'mukim' => $validated['daerah'] ?? null,
            'alamat_ladang' => $validated['alamat_ladang'],
            'poskod' => $validated['poskod'] ?? null,
            'negeri' => $validated['negeri'] ?? 'Kelantan',
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'status_pemilikan_tanah' => $validated['status_pemilikan_tanah'] ?? 'Milik Sendiri',
            'sistem_reban' => $validated['sistem_reban'] ?? 'Tertutup',
            'reban_data' => $rebanData,
            'alamat_premis_perniagaan' => $validated['alamat_premis_perniagaan'] ?? null,
            'poskod_premis_perniagaan' => $validated['poskod_premis_perniagaan'] ?? null,
            'negeri_premis_perniagaan' => $validated['negeri_premis_perniagaan'] ?? null,
            'kapasiti_maksimum_unggas' => $kapasiti,
            'jarak_kediaman_terdekat_meter' => 200,
            'jarak_sungai_terdekat_meter' => 100,
            'kaedah_kawalan_lalat_bau' => 'Semburan EM & Kawalan Kebersihan Berkala',
            'kaedah_pelupusan_tinja' => 'Difermentasi menjadi baja organik',
            'kaedah_pelupusan_bangkai' => 'Lubang bangkai konkrit bertutup',
            'status_ladang' => 'Aktif',
        ]);

        $noRujukan = 'EPU/' . strtoupper(substr($validated['jajahan'], 0, 3)) . '/' . date('Y') . '/' . rand(1000, 9999);
        $noLesen = 'EPU-' . strtoupper(substr($validated['jajahan'], 0, 3)) . '-' . date('Y') . '-' . rand(1000, 9999);

        $permohonan = EpuPermohonan::create([
            'epu_ladang_id' => $ladang->id,
            'no_rujukan_permohonan' => $noRujukan,
            'jenis_permohonan' => 'Baru',
            'jenis_unggas' => $validated['jenis_unggas'],
            'jurusan_aktiviti' => $validated['jurusan_aktiviti'],
            'bilangan_semasa_unggas' => $bilanganSemasa,
            'kapasiti_ladang' => $kapasiti,
            'no_lesen_epu' => $noLesen,
            'tarikh_mula_lesen' => Carbon::now()->toDateString(),
            'tarikh_tamat_lesen' => Carbon::now()->addYear()->toDateString(),
            'yuran_lesen' => $yuranLesen,
            'no_resit_bayaran' => null,
            'status_bayaran_fi' => $isFree ? 'Dikecualikan' : 'Belum Bayar',
            'status' => $user->isStaff() ? 'Diluluskan' : 'Dihantar',
            'mohon_pengecualian' => $request->boolean('mohon_pengecualian'),
            'sebab_pengecualian' => $validated['sebab_pengecualian'] ?? null,
            'sebab_pengecualian_lain' => $validated['sebab_pengecualian_lain'] ?? null,
            'lampiran_pengecualian' => $pathPengecualian,
            'dokumen_pelan' => $pathPelan,
            'dokumen_tanah' => $pathTanah,
            'dokumen_pbt' => $pathPbt,
            'dokumen_ssm' => $pathSsm,
            'dokumen_sokongan' => $pathPelan ?? $pathTanah,
            'syarat_khas_lesen' => "1. Mematuhi Enakmen Perladangan Unggas 2005.\n2. Mengamalkan kawalan lalat dan bau secara berkala.\n3. Tiada pelepasan air basuhan ke saliran awam tanpa tapisan.",
            'diluluskan_oleh' => $user->isStaff() ? $user->id : null,
            'tarikh_kelulusan' => $user->isStaff() ? Carbon::now()->toDateString() : null,
        ]);

        if ($ladang->user_id) {
            \App\Models\UserNotification::send(
                $ladang->user_id,
                'Pendaftaran Permohonan Lesen EPU Berjaya',
                "Permohonan Lesen Unggas '{$ladang->nama_ladang}' (No. Rujukan: {$noRujukan}) berjaya dihantar ke JPVNK untuk semakan.",
                'epu',
                route('epu.show', $ladang->id),
                'fa-solid fa-feather-pointed',
                'amber'
            );
        }

        // Notifikasi kepada Pegawai Verifikasi PPVJ Jajahan, Admin EPU & Super Admin
        $this->notifyEpuOfficers(
            ['pegawai_verifikasi_epu', 'admin_epu', 'super_admin'],
            $ladang->jajahan,
            "Permohonan Lesen EPU Baharu (PPVJ {$ladang->jajahan})",
            "Permohonan lesen Borang A bagi '{$ladang->nama_ladang}' ({$ladang->nama_pemohon_atau_syarikat}) di Jajahan {$ladang->jajahan} telah dihantar dan memerlukan semakan dokumen & verifikasi tapak.",
            route('epu.show', $ladang->id),
            'fa-solid fa-feather-pointed',
            'amber',
            $user->id
        );

        return redirect()->route('epu.show', $ladang->id)->with('success', 'Permohonan Pendaftaran & Lesen Ladang Unggas (EPU Borang A) berjaya dihantar!');
    }

    // Maklumat Ladang & Senarai Lesen
    public function show($id)
    {
        $ladang = EpuLadang::with('pemilik', 'permohonanList.pelulus', 'permohonanList.pegawaiVerifikasi', 'pemeriksaanList.pegawai')->findOrFail($id);
        $permohonanUtama = $ladang->permohonanList()->latest()->first();
        return view('epu.show', compact('ladang', 'permohonanUtama'));
    }

    protected function resolveLadangDanPermohonan($id)
    {
        $permohonan = EpuPermohonan::with('ladang.pemilik', 'pelulus')->find($id);
        if ($permohonan) {
            $ladang = $permohonan->ladang;
        } else {
            $ladang = EpuLadang::with('pemilik', 'permohonanTerkini.pelulus')->findOrFail($id);
            $permohonan = $ladang->permohonanTerkini;
        }
        return [$ladang, $permohonan];
    }

    // Cetak Borang A: Permohonan Lesen Perladangan Unggas (Enakmen 2005 Format Rasmi Warta)
    public function cetakBorangA($id)
    {
        if (!Auth::user()->canCetakBorangEpu()) {
            abort(403, 'Akses Ditolak: Borang EPU hanya boleh dicetak oleh Pegawai Verifikasi EPU dan Admin EPU Negeri.');
        }
        [$ladang, $permohonan] = $this->resolveLadangDanPermohonan($id);
        return view('epu.cetak-borang-a', compact('ladang', 'permohonan'));
    }

    // Cetak Borang B: Permohonan Pengecualian Lesen Perladangan Unggas
    public function cetakBorangBPengecualian($id)
    {
        if (!Auth::user()->canCetakBorangEpu()) {
            abort(403, 'Akses Ditolak: Borang EPU hanya boleh dicetak oleh Pegawai Verifikasi EPU dan Admin EPU Negeri.');
        }
        [$ladang, $permohonan] = $this->resolveLadangDanPermohonan($id);
        return view('epu.cetak-borang-b-pengecualian', compact('ladang', 'permohonan'));
    }

    // Cetak Borang C: Sijil Pengecualian Lesen Perladangan Unggas
    public function cetakSijilPengecualianC($id)
    {
        if (!Auth::user()->canCetakBorangEpu()) {
            abort(403, 'Akses Ditolak: Borang EPU hanya boleh dicetak oleh Pegawai Verifikasi EPU dan Admin EPU Negeri.');
        }
        [$ladang, $permohonan] = $this->resolveLadangDanPermohonan($id);
        return view('epu.cetak-sijil-pengecualian-c', compact('ladang', 'permohonan'));
    }

    // Cetak Borang A: Permohonan Salinan Pendua Lesen
    public function cetakSalinanPendua($id)
    {
        if (!Auth::user()->canCetakBorangEpu()) {
            abort(403, 'Akses Ditolak: Borang EPU hanya boleh dicetak oleh Pegawai Verifikasi EPU dan Admin EPU Negeri.');
        }
        [$ladang, $permohonan] = $this->resolveLadangDanPermohonan($id);
        return view('epu.cetak-salinan-pendua', compact('ladang', 'permohonan'));
    }

    // EPU Borang B: Cetak Lesen Ladang Unggas
    public function cetakLesen($permohonanId)
    {
        if (!Auth::user()->canCetakBorangEpu()) {
            abort(403, 'Akses Ditolak: Lesen Ladang Unggas (Borang B) hanya boleh dicetak oleh Pegawai Verifikasi EPU dan Admin EPU Negeri.');
        }
        $permohonan = EpuPermohonan::with('ladang.pemilik', 'pelulus')->findOrFail($permohonanId);
        return view('epu.borang-b-lesen', compact('permohonan'));
    }

    // EPU Borang C: Pembaharuan Lesen
    public function createPembaharuan($ladangId)
    {
        $ladang = EpuLadang::with('permohonanTerkini')->findOrFail($ladangId);
        return view('epu.borang-c-pembaharuan', compact('ladang'));
    }

    public function storePembaharuan(Request $request, $ladangId)
    {
        $ladang = EpuLadang::findOrFail($ladangId);

        $validated = $request->validate([
            'bilangan_semasa_unggas' => 'required|integer',
            'perubahan_maklumat' => 'nullable|string',
        ]);

        $noRujukan = 'EPU-RENEW/' . strtoupper(substr($ladang->jajahan, 0, 3)) . '/' . date('Y') . '/' . rand(1000, 9999);
        $noLesen = 'EPU-' . strtoupper(substr($ladang->jajahan, 0, 3)) . '-' . date('Y') . '-' . rand(1000, 9999);

        EpuPermohonan::create([
            'epu_ladang_id' => $ladang->id,
            'no_rujukan_permohonan' => $noRujukan,
            'jenis_permohonan' => 'Pembaharuan',
            'jenis_unggas' => $ladang->permohonanTerkini ? $ladang->permohonanTerkini->jenis_unggas : 'Ayam Pedaging',
            'bilangan_semasa_unggas' => $validated['bilangan_semasa_unggas'],
            'no_lesen_epu' => $noLesen,
            'tarikh_mula_lesen' => Carbon::now()->toDateString(),
            'tarikh_tamat_lesen' => Carbon::now()->addYear()->toDateString(),
            'yuran_lesen' => 200.00,
            'no_resit_bayaran' => 'RES-EPU-' . date('Y') . '-' . rand(100, 999),
            'status' => Auth::user()->isStaff() ? 'Diluluskan' : 'Dihantar',
            'syarat_khas_lesen' => 'Lesen diperbaharui dengan syarat mengekalkan piawaian kebersihan dan biosekuriti.',
            'diluluskan_oleh' => Auth::user()->isStaff() ? Auth::id() : null,
            'tarikh_kelulusan' => Auth::user()->isStaff() ? Carbon::now()->toDateString() : null,
        ]);

        if ($ladang->user_id) {
            \App\Models\UserNotification::send(
                $ladang->user_id,
                'Permohonan Pembaharuan Lesen EPU Dihantar',
                "Permohonan pembaharuan lesen (Borang C) bagi '{$ladang->nama_ladang}' telah direkodkan.",
                'epu',
                route('epu.show', $ladang->id),
                'fa-solid fa-arrows-rotate',
                'blue'
            );
        }

        $this->notifyEpuOfficers(
            ['pegawai_verifikasi_epu', 'admin_epu', 'pegawai_pelesen', 'super_admin'],
            $ladang->jajahan,
            "Permohonan Pembaharuan Lesen EPU (PPVJ {$ladang->jajahan})",
            "Permohonan pembaharuan lesen Borang C bagi '{$ladang->nama_ladang}' (No. Ruj: {$noRujukan}) telah dihantar.",
            route('epu.show', $ladang->id),
            'fa-solid fa-arrows-rotate',
            'blue',
            Auth::id()
        );

        return redirect()->route('epu.show', $ladang->id)->with('success', 'Permohonan Pembaharuan Lesen Unggas (EPU Borang C) berjaya didaftarkan.');
    }

    // EPU Borang D: Laporan Pemeriksaan & Penguatkuasaan
    public function createPemeriksaan($ladangId)
    {
        $ladang = EpuLadang::with('permohonanTerkini')->findOrFail($ladangId);
        return view('epu.borang-d-pemeriksaan', compact('ladang'));
    }

    public function storePemeriksaan(Request $request, $ladangId)
    {
        $ladang = EpuLadang::findOrFail($ladangId);

        $validated = $request->validate([
            'tarikh_pemeriksaan' => 'required|date',
            'skor_kebersihan_peratus' => 'required|integer|min:0|max:100',
            'patuh_zon_penampan' => 'required|boolean',
            'kawalan_lalat_memuaskan' => 'required|boolean',
            'kawalan_bau_memuaskan' => 'required|boolean',
            'sistem_longkang_sempurna' => 'required|boolean',
            'penemuan_pemeriksaan' => 'required|string',
            'syor_dan_arahan' => 'required|string',
            'status_keputusan' => 'required|in:Lulus,Lulus Bersyarat,Gagal,Notis Dikeluarkan',
            'no_notis_pematuhan' => 'nullable|string',
            'tarikh_akhir_pematuhan' => 'nullable|date',
        ]);

        EpuPemeriksaan::create([
            'epu_ladang_id' => $ladang->id,
            'epu_permohonan_id' => $ladang->permohonanTerkini ? $ladang->permohonanTerkini->id : null,
            'pegawai_id' => Auth::id(),
            'tarikh_pemeriksaan' => $validated['tarikh_pemeriksaan'],
            'skor_kebersihan_peratus' => $validated['skor_kebersihan_peratus'],
            'patuh_zon_penampan' => $validated['patuh_zon_penampan'],
            'kawalan_lalat_memuaskan' => $validated['kawalan_lalat_memuaskan'],
            'kawalan_bau_memuaskan' => $validated['kawalan_bau_memuaskan'],
            'sistem_longkang_sempurna' => $validated['sistem_longkang_sempurna'],
            'penemuan_pemeriksaan' => $validated['penemuan_pemeriksaan'],
            'syor_dan_arahan' => $validated['syor_dan_arahan'],
            'status_keputusan' => $validated['status_keputusan'],
            'no_notis_pematuhan' => $validated['no_notis_pematuhan'] ?? null,
            'tarikh_akhir_pematuhan' => $validated['tarikh_akhir_pematuhan'] ?? null,
        ]);

        if ($ladang->user_id) {
            \App\Models\UserNotification::send(
                $ladang->user_id,
                'Laporan Pemeriksaan Ladang EPU Direkodkan',
                "Laporan pemeriksaan tapak (Borang D) bagi '{$ladang->nama_ladang}' telah direkodkan. Keputusan: {$validated['status_keputusan']} (Skor: {$validated['skor_kebersihan_peratus']}%).",
                'epu',
                route('epu.show', $ladang->id),
                'fa-solid fa-clipboard-check',
                $validated['status_keputusan'] === 'Lulus' ? 'emerald' : 'amber'
            );
        }

        $this->notifyEpuOfficers(
            ['admin_epu', 'pegawai_pelesen', 'super_admin'],
            $ladang->jajahan,
            "Laporan Pemeriksaan Ladang EPU (PPVJ {$ladang->jajahan})",
            "Laporan pemeriksaan Borang D bagi '{$ladang->nama_ladang}' telah direkodkan oleh " . Auth::user()->name . ". Keputusan: {$validated['status_keputusan']}.",
            route('epu.show', $ladang->id),
            'fa-solid fa-clipboard-check',
            'blue',
            Auth::id()
        );

        return redirect()->route('epu.show', $ladang->id)->with('success', 'Laporan Pemeriksaan Tapak & Penguatkuasaan (EPU Borang D) telah direkodkan.');
    }

    // Step 3 & 4: Verifikasi Kelengkapan & Kepatuhan Tapak oleh PPVJ
    public function verifikasiJajahan(Request $request, $id)
    {
        $user = Auth::user();
        $permohonan = EpuPermohonan::with('ladang.pemilik')->findOrFail($id);

        if ($permohonan->status === 'Diluluskan' || $permohonan->status_kelulusan_pelesen === 'Lulus') {
            abort(403, 'Akses Ditolak: Status Verifikasi Pegawai Verifikasi Jajahan tidak boleh diubah lagi kerana permohonan EPU ini telah diluluskan sepenuhnya.');
        }

        if (!$user->canPerformVerifikasi($permohonan->ladang->jajahan)) {
            abort(403, 'Akses Ditolak: Pegawai Pelesen / Pengarah DVS tidak dibenarkan mengubah status Pegawai Verifikasi Jajahan. Tindakan ini dikhaskan untuk Pegawai Verifikasi PPVJ (' . $permohonan->ladang->jajahan . ').');
        }

        $validated = $request->validate([
            'status_verifikasi' => 'required|in:Lengkap,Tidak Lengkap,Tidak Patuh,Patuh',
            'catatan_verifikasi' => 'nullable|string',
            'tindakan_penambahbaikan' => 'nullable|string',
        ]);

        $statusVerifikasi = $validated['status_verifikasi'];
        $catatan = $validated['catatan_verifikasi'] ?? null;
        $tindakan = $validated['tindakan_penambahbaikan'] ?? null;

        $statusPermohonan = match($statusVerifikasi) {
            'Lengkap' => 'Diterima PPVJ',
            'Tidak Lengkap' => 'Tidak Lengkap',
            'Tidak Patuh' => 'Tidak Patuh',
            'Patuh' => 'Patuh / Disahkan',
            default => $permohonan->status
        };

        $permohonan->update([
            'status' => $statusPermohonan,
            'status_verifikasi' => $statusVerifikasi,
            'pegawai_verifikasi_id' => $user->id,
            'tarikh_verifikasi' => Carbon::now()->toDateString(),
            'catatan_verifikasi' => $catatan,
            'tindakan_penambahbaikan' => $tindakan,
        ]);

        $ownerId = $permohonan->ladang->user_id;

        if ($statusVerifikasi === 'Tidak Lengkap') {
            if ($ownerId) {
                \App\Models\UserNotification::send(
                    $ownerId,
                    'Permohonan EPU: Dokumen / Maklumat Tidak Lengkap',
                    "Permohonan lesen unggas anda (No: {$permohonan->no_rujukan_permohonan}) memerlukan pembetulan dokumen: {$catatan}",
                    'epu',
                    route('epu.show', $permohonan->ladang->id),
                    'fa-solid fa-triangle-exclamation',
                    'rose'
                );
            }
            $this->notifyEpuOfficers(
                ['admin_epu', 'super_admin'],
                $permohonan->ladang->jajahan,
                "Verifikasi PPVJ {$permohonan->ladang->jajahan}: Dokumen Tidak Lengkap",
                "Pegawai Verifikasi {$user->name} mendapati dokumen bagi '{$permohonan->ladang->nama_ladang}' tidak lengkap.",
                route('epu.show', $permohonan->ladang->id),
                'fa-solid fa-triangle-exclamation',
                'rose',
                $user->id
            );
        } elseif ($statusVerifikasi === 'Tidak Patuh') {
            if ($ownerId) {
                \App\Models\UserNotification::send(
                    $ownerId,
                    'Makluman Ketidakpatuhan Ladang EPU & Tindakan Penambahbaikan',
                    "Pemeriksaan verifikasi mendapati premis ladang belum mematuhi syarat. Tindakan penambahbaikan diperlukan: {$tindakan}",
                    'epu',
                    route('epu.show', $permohonan->ladang->id),
                    'fa-solid fa-clipboard-question',
                    'amber'
                );
            }
            $this->notifyEpuOfficers(
                ['admin_epu', 'super_admin'],
                $permohonan->ladang->jajahan,
                "Verifikasi PPVJ {$permohonan->ladang->jajahan}: Ketidakpatuhan Ladang",
                "Pegawai Verifikasi {$user->name} mengeluarkan arahan penambahbaikan bagi '{$permohonan->ladang->nama_ladang}': {$tindakan}",
                route('epu.show', $permohonan->ladang->id),
                'fa-solid fa-clipboard-question',
                'amber',
                $user->id
            );
        } elseif ($statusVerifikasi === 'Patuh') {
            if ($ownerId) {
                \App\Models\UserNotification::send(
                    $ownerId,
                    'Verifikasi Ladang EPU: Patuh Piawaian',
                    "Verifikasi ladang ({$permohonan->ladang->nama_ladang}) telah disahkan PATUH oleh Pegawai Verifikasi PPVJ {$permohonan->ladang->jajahan}.",
                    'epu',
                    route('epu.show', $permohonan->ladang->id),
                    'fa-solid fa-circle-check',
                    'emerald'
                );
            }
            $this->notifyEpuOfficers(
                ['admin_epu', 'pegawai_pelesen', 'super_admin'],
                $permohonan->ladang->jajahan,
                "Verifikasi PPVJ {$permohonan->ladang->jajahan}: Disahkan Patuh",
                "Verifikasi tapak bagi '{$permohonan->ladang->nama_ladang}' telah disahkan PATUH oleh {$user->name}. Bersedia untuk perakuan penilaian kelulusan.",
                route('epu.show', $permohonan->ladang->id),
                'fa-solid fa-circle-check',
                'emerald',
                $user->id
            );
        }

        return redirect()->route('epu.show', $permohonan->ladang->id)->with('success', "Status verifikasi PPVJ telah dikemaskini kepada: {$statusVerifikasi}");
    }

    // Step 5: Pegawai Verifikasi Menghantar Penilaian Ladang kepada Pegawai Pelesen
    public function hantarPenilaian(Request $request, $id)
    {
        $user = Auth::user();
        $permohonan = EpuPermohonan::with('ladang.pemilik')->findOrFail($id);

        if ($permohonan->status === 'Diluluskan' || $permohonan->status_kelulusan_pelesen === 'Lulus') {
            abort(403, 'Akses Ditolak: Permohonan EPU ini telah diluluskan sepenuhnya.');
        }

        if (!$user->canPerformVerifikasi($permohonan->ladang->jajahan)) {
            abort(403, 'Akses Ditolak: Hanya Pegawai Verifikasi PPVJ dibenarkan menghantar penilaian ladang ke Pegawai Pelesen.');
        }

        $validated = $request->validate([
            'catatan_penilaian_ladang' => 'nullable|string',
        ]);

        $permohonan->update([
            'status' => 'Menunggu Kelulusan Pelesen',
            'status_penilaian_ladang' => 'Dihantar ke Pegawai Pelesen',
            'tarikh_hantar_penilaian' => Carbon::now()->toDateString(),
            'catatan_penilaian_ladang' => $validated['catatan_penilaian_ladang'] ?? 'Laporan verifikasi dan penilaian tapak diperakukan untuk kelulusan Pegawai Pelesen.',
        ]);

        if ($permohonan->ladang->user_id) {
            \App\Models\UserNotification::send(
                $permohonan->ladang->user_id,
                'Penilaian Ladang EPU Dihantar ke Pegawai Pelesen',
                "Penilaian ladang telah dihantar kepada Pegawai Pelesen / Pengarah untuk semakan kelulusan lesen.",
                'epu',
                route('epu.show', $permohonan->ladang->id),
                'fa-solid fa-paper-plane',
                'blue'
            );
        }

        // Notifikasi kepada Pegawai Pelesen / Pengarah, Admin EPU & Super Admin
        $this->notifyEpuOfficers(
            ['pegawai_pelesen', 'admin_epu', 'super_admin'],
            null,
            "Penilaian Ladang EPU Memerlukan Semakan Kelulusan",
            "Penilaian ladang '{$permohonan->ladang->nama_ladang}' (PPVJ {$permohonan->ladang->jajahan}) telah dimajukan oleh Pegawai Verifikasi {$user->name} untuk semakan keputusan kelulusan lesen.",
            route('epu.show', $permohonan->ladang->id),
            'fa-solid fa-stamp',
            'blue',
            $user->id
        );

        return redirect()->route('epu.show', $permohonan->ladang->id)->with('success', 'Penilaian ladang berjaya dimajukan kepada Pegawai Pelesen.');
    }

    // Step 6: Keputusan Pegawai Pelesen / Pengarah (Lulus / Gagal)
    public function keputusanPelesen(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->canPerformKeputusanPelesen()) {
            abort(403, 'Akses Ditolak: Pegawai Verifikasi Jajahan tidak mempunyai kebenaran untuk membuat keputusan kelulusan lesen atau pemprosesan rayuan. Tindakan ini dikhaskan untuk Pegawai Pelesen / Pengarah.');
        }

        $permohonan = EpuPermohonan::with('ladang.pemilik')->findOrFail($id);

        if ($permohonan->status === 'Diluluskan' || $permohonan->status_kelulusan_pelesen === 'Lulus') {
            abort(403, 'Akses Ditolak: Rekod Keputusan Pegawai Pelesen tidak boleh diubah lagi kerana permohonan EPU ini telah diluluskan sepenuhnya.');
        }

        $validated = $request->validate([
            'keputusan' => 'required|in:Lulus,Gagal',
            'catatan_pegawai' => 'nullable|string',
            'syarat_khas_lesen' => 'nullable|string',
        ]);

        $keputusan = $validated['keputusan'];
        $ownerId = $permohonan->ladang->user_id;

        if ($keputusan === 'Lulus') {
            $isFree = $permohonan->mohon_pengecualian || $permohonan->yuran_lesen <= 0;
            $permohonan->update([
                'status' => 'Diluluskan',
                'status_kelulusan_pelesen' => 'Lulus',
                'diluluskan_oleh' => $user->id,
                'tarikh_kelulusan' => Carbon::now()->toDateString(),
                'catatan_pegawai' => $validated['catatan_pegawai'] ?? 'Permohonan lesen diluluskan oleh Pegawai Pelesen DVS.',
                'syarat_khas_lesen' => $validated['syarat_khas_lesen'] ?? $permohonan->syarat_khas_lesen,
                'status_bayaran_fi' => $isFree ? 'Dikecualikan' : ($permohonan->status_bayaran_fi === 'Selesai Bayar' ? 'Selesai Bayar' : 'Belum Bayar'),
            ]);

            // Makluman kelulusan lesen dan pembayaran fi kepada pemohon
            if ($ownerId) {
                $pesananFi = $isFree 
                    ? "Permohonan lesen anda telah DILULUSKAN (Dikecualikan Bayaran). Anda kini boleh mencetak Lesen Borang B."
                    : "Permohonan lesen anda telah DILULUSKAN. Sila jelaskan pembayaran fi lesen sebanyak RM " . number_format($permohonan->yuran_lesen, 2) . " untuk pencetakan lesen rasmi.";

                \App\Models\UserNotification::send(
                    $ownerId,
                    'Permohonan Lesen EPU DILULUSKAN',
                    $pesananFi,
                    'epu',
                    route('epu.show', $permohonan->ladang->id),
                    'fa-solid fa-award',
                    'emerald'
                );
            }
        } else {
            // Gagal
            $permohonan->update([
                'status' => 'Ditolak',
                'status_kelulusan_pelesen' => 'Gagal',
                'diluluskan_oleh' => $user->id,
                'tarikh_kelulusan' => Carbon::now()->toDateString(),
                'catatan_pegawai' => $validated['catatan_pegawai'] ?? 'Permohonan lesen gagal memenuhi piawaian Enakmen Perladangan Unggas.',
            ]);

            // Makluman kegagalan lesen kepada pemohon & hak rayuan kepada Pengarah
            if ($ownerId) {
                \App\Models\UserNotification::send(
                    $ownerId,
                    'Makluman Kegagalan Permohonan Lesen EPU',
                    "Permohonan lesen ladang tidak diluluskan. Alasan: " . ($validated['catatan_pegawai'] ?? 'Tidak memenuhi syarat.') . " Anda boleh mengemukakan Rayuan kepada Pengarah DVS melalui sistem.",
                    'epu',
                    route('epu.show', $permohonan->ladang->id),
                    'fa-solid fa-circle-xmark',
                    'rose'
                );
            }
        }

        // Notifikasi kepada Pegawai Verifikasi PPVJ & Admin EPU berkenaan keputusan
        $this->notifyEpuOfficers(
            ['pegawai_verifikasi_epu', 'admin_epu'],
            $permohonan->ladang->jajahan,
            "Keputusan Lesen EPU: {$keputusan} (PPVJ {$permohonan->ladang->jajahan})",
            "Pegawai Pelesen ({$user->name}) telah merekodkan keputusan [{$keputusan}] bagi permohonan '{$permohonan->ladang->nama_ladang}'.",
            route('epu.show', $permohonan->ladang->id),
            $keputusan === 'Lulus' ? 'fa-solid fa-award' : 'fa-solid fa-circle-xmark',
            $keputusan === 'Lulus' ? 'emerald' : 'rose',
            $user->id
        );

        return redirect()->route('epu.show', $permohonan->ladang->id)->with('success', "Keputusan Pegawai Pelesen ({$keputusan}) telah direkodkan dan makluman dihantar.");
    }

    // Step 6 (Sub-flow): Kemukakan Rayuan kepada Pengarah oleh Pemohon
    public function hantarRayuan(Request $request, $id)
    {
        $permohonan = EpuPermohonan::with('ladang.pemilik')->findOrFail($id);
        $user = Auth::user();

        // Pastikan hanya pemilik atau staf boleh hantar rayuan
        if (!$user->isStaff() && $permohonan->ladang->user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'alasan_rayuan' => 'required|string',
            'dokumen_rayuan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $pathDokumenRayuan = $this->uploadFileSafely($request->file('dokumen_rayuan'), 'epu/rayuan');

        $permohonan->update([
            'status_rayuan' => 'Rayuan Dihantar',
            'alasan_rayuan' => $validated['alasan_rayuan'],
            'dokumen_rayuan' => $pathDokumenRayuan ?? $permohonan->dokumen_rayuan,
            'tarikh_rayuan' => Carbon::now()->toDateString(),
        ]);

        if ($permohonan->ladang->user_id) {
            \App\Models\UserNotification::send(
                $permohonan->ladang->user_id,
                'Rayuan Lesen EPU Berjaya Dihantar',
                "Rayuan anda bagi permohonan lesen '{$permohonan->ladang->nama_ladang}' telah dimajukan kepada Pengarah DVS.",
                'epu',
                route('epu.show', $permohonan->ladang->id),
                'fa-solid fa-scale-balanced',
                'amber'
            );
        }

        // Notifikasi kepada Pegawai Pelesen / Pengarah, Admin EPU & Super Admin
        $this->notifyEpuOfficers(
            ['pegawai_pelesen', 'admin_epu', 'super_admin'],
            null,
            "Rayuan Lesen EPU Diterima untuk Pertimbangan Pengarah",
            "Pemohon bagi '{$permohonan->ladang->nama_ladang}' (Jajahan {$permohonan->ladang->jajahan}) telah mengemukakan rayuan rasmi kepada Pengarah DVS.",
            route('epu.show', $permohonan->ladang->id),
            'fa-solid fa-scale-balanced',
            'purple',
            $user->id
        );

        return redirect()->route('epu.show', $permohonan->ladang->id)->with('success', 'Rayuan kepada Pengarah DVS telah berjaya dihantar.');
    }

    // Step 6 (Sub-flow): Pengarah Memproses Rayuan (Panjangkan ke Pihak Berkuasa Negeri / Lulus Rayuan / Tolak Rayuan)
    public function prosesRayuan(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->canPerformKeputusanPelesen()) {
            abort(403, 'Akses Ditolak: Hanya Pegawai Pelesen / Pengarah dibenarkan memproses keputusan rayuan.');
        }

        $permohonan = EpuPermohonan::with('ladang.pemilik')->findOrFail($id);
        $validated = $request->validate([
            'tindakan_rayuan' => 'required|in:Panjangkan ke PBN,Lulus Rayuan,Tolak Rayuan',
            'catatan_keputusan_rayuan' => 'nullable|string',
        ]);

        $tindakan = $validated['tindakan_rayuan'];
        $catatan = $validated['catatan_keputusan_rayuan'] ?? null;
        $ownerId = $permohonan->ladang->user_id;

        if ($tindakan === 'Panjangkan ke PBN') {
            $permohonan->update([
                'status_rayuan' => 'Dipanjangkan ke Pihak Berkuasa Negeri',
                'catatan_keputusan_rayuan' => $catatan ?? 'Pengarah telah memanjangkan rayuan kepada Pihak Berkuasa Negeri untuk pertimbangan.',
            ]);

            if ($ownerId) {
                \App\Models\UserNotification::send(
                    $ownerId,
                    'Rayuan EPU Dipanjangkan ke Pihak Berkuasa Negeri',
                    "Pengarah DVS telah memanjangkan rayuan lesen ladang anda kepada Pihak Berkuasa Negeri untuk keputusan rasmi.",
                    'epu',
                    route('epu.show', $permohonan->ladang->id),
                    'fa-solid fa-landmark',
                    'blue'
                );
            }
        } elseif ($tindakan === 'Lulus Rayuan') {
            $isFree = $permohonan->mohon_pengecualian || $permohonan->yuran_lesen <= 0;
            $permohonan->update([
                'status' => 'Diluluskan',
                'status_kelulusan_pelesen' => 'Lulus (Rayuan)',
                'status_rayuan' => 'Lulus Rayuan',
                'diluluskan_oleh' => $user->id,
                'tarikh_kelulusan' => Carbon::now()->toDateString(),
                'catatan_keputusan_rayuan' => $catatan ?? 'Rayuan telah diterima dan diluluskan oleh Pengarah / Pihak Berkuasa Negeri.',
                'status_bayaran_fi' => $isFree ? 'Dikecualikan' : ($permohonan->status_bayaran_fi === 'Selesai Bayar' ? 'Selesai Bayar' : 'Belum Bayar'),
            ]);

            if ($ownerId) {
                \App\Models\UserNotification::send(
                    $ownerId,
                    'Rayuan Lesen EPU DILULUSKAN',
                    "Tahniah, rayuan lesen anda telah DILULUSKAN. Sila buat pembayaran fi lesen untuk mencetak Lesen Borang B.",
                    'epu',
                    route('epu.show', $permohonan->ladang->id),
                    'fa-solid fa-circle-check',
                    'emerald'
                );
            }
        } else {
            // Tolak Rayuan
            $permohonan->update([
                'status' => 'Ditolak',
                'status_rayuan' => 'Ditolak Rayuan',
                'catatan_keputusan_rayuan' => $catatan ?? 'Rayuan permohonan lesen ditolak secara muktamad.',
            ]);

            if ($ownerId) {
                \App\Models\UserNotification::send(
                    $ownerId,
                    'Keputusan Rayuan Lesen EPU Ditolak',
                    "Rayuan permohonan lesen anda telah ditolak. Catatan: " . ($catatan ?? 'Tidak memenuhi syarat Enakmen.'),
                    'epu',
                    route('epu.show', $permohonan->ladang->id),
                    'fa-solid fa-circle-xmark',
                    'rose'
                );
            }
        }

        // Notifikasi kepada Pegawai Verifikasi PPVJ & Admin EPU
        $this->notifyEpuOfficers(
            ['pegawai_verifikasi_epu', 'admin_epu'],
            $permohonan->ladang->jajahan,
            "Keputusan Rayuan Lesen EPU: {$tindakan}",
            "Pengarah / PBN telah merekodkan keputusan rayuan [{$tindakan}] bagi ladang '{$permohonan->ladang->nama_ladang}'.",
            route('epu.show', $permohonan->ladang->id),
            'fa-solid fa-gavel',
            'purple',
            $user->id
        );

        return redirect()->route('epu.show', $permohonan->ladang->id)->with('success', "Keputusan rayuan telah direkodkan: {$tindakan}");
    }

    // Step 7: Pembayaran Fi Lesen oleh Pemohon
    public function bayarFiLesen(Request $request, $id)
    {
        $permohonan = EpuPermohonan::with('ladang.pemilik')->findOrFail($id);
        $user = Auth::user();

        // Resit hanya boleh dimuat naik oleh pemohon (pemilik ladang)
        $isOwner = ($permohonan->ladang->user_id === $user->id);
        if (!$isOwner && !$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Resit bayaran fi hanya boleh dimuat naik oleh pemohon.');
        }

        $validated = $request->validate([
            'resit_bayaran_fi' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'no_resit_bayaran' => 'nullable|string|max:50',
            'kaedah_bayaran' => 'nullable|string',
        ]);

        $pathResit = $this->uploadFileSafely($request->file('resit_bayaran_fi'), 'epu/resit_bayaran');
        if (!$pathResit) {
            return back()->with('error', 'Gagal memuat naik fail resit bayaran. Sila pastikan fail berformat JPG, PNG, atau PDF dan saiz tidak melebihi 10MB.');
        }

        $noResit = !empty($validated['no_resit_bayaran']) 
            ? $validated['no_resit_bayaran'] 
            : ($permohonan->no_resit_bayaran ?: ('RES-EPU-' . date('Y') . '-' . str_pad($permohonan->id, 4, '0', STR_PAD_LEFT)));

        $permohonan->update([
            'status_bayaran_fi' => 'Menunggu Pengesahan',
            'resit_bayaran_fi' => $pathResit,
            'no_resit_bayaran' => $noResit,
            'tarikh_bayaran_fi' => Carbon::now()->toDateString(),
        ]);

        if ($permohonan->ladang->user_id) {
            \App\Models\UserNotification::send(
                $permohonan->ladang->user_id,
                'Pembayaran Fi Lesen EPU Diterima',
                "Fail resit bayaran fi lesen telah berjaya dimuat naik dan dihantar untuk pengesahan pegawai.",
                'epu',
                route('epu.show', $permohonan->ladang->id),
                'fa-solid fa-receipt',
                'emerald'
            );
        }

        // Notifikasi kepada Admin EPU, Pegawai Verifikasi PPVJ, Pegawai Pelesen & Super Admin
        $this->notifyEpuOfficers(
            ['admin_epu', 'pegawai_verifikasi_epu', 'pegawai_pelesen', 'super_admin'],
            $permohonan->ladang->jajahan,
            "Bukti Pembayaran Fi Lesen EPU Dimuat Naik (PPVJ {$permohonan->ladang->jajahan})",
            "Pemohon '{$permohonan->ladang->nama_ladang}' telah memuat naik resit bayaran fi lesen. Sila buat semakan dan pengesahan bayaran.",
            route('epu.show', $permohonan->ladang->id),
            'fa-solid fa-money-check-dollar',
            'amber',
            $user->id
        );

        return redirect()->route('epu.show', $permohonan->ladang->id)->with('success', 'Fail resit bayaran fi berjaya dimuat naik!');
    }

    // Pengesahan Bayaran Fi oleh Pegawai
    public function sahkanBayaranFi(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isStaff()) {
            abort(403);
        }

        $permohonan = EpuPermohonan::with('ladang.pemilik')->findOrFail($id);
        $permohonan->update([
            'status_bayaran_fi' => 'Selesai Bayar',
            'tarikh_bayaran_fi' => Carbon::now()->toDateString(),
        ]);

        if ($permohonan->ladang->user_id) {
            \App\Models\UserNotification::send(
                $permohonan->ladang->user_id,
                'Pembayaran Fi Lesen EPU Disahkan',
                "Pembayaran fi lesen anda telah DISAHKAN. Lesen Ladang Unggas (Borang B) kini boleh dicetak.",
                'epu',
                route('epu.show', $permohonan->ladang->id),
                'fa-solid fa-file-circle-check',
                'emerald'
            );
        }

        // Notifikasi kepada Pegawai Pelesen, PPVJ, Admin EPU & Super Admin
        $this->notifyEpuOfficers(
            ['pegawai_pelesen', 'pegawai_verifikasi_epu', 'admin_epu', 'super_admin'],
            $permohonan->ladang->jajahan,
            "Pembayaran Fi Lesen EPU Telah Disahkan",
            "Pembayaran fi lesen bagi '{$permohonan->ladang->nama_ladang}' telah disahkan oleh {$user->name}. Lesen Borang B kini sedia dicetak.",
            route('epu.show', $permohonan->ladang->id),
            'fa-solid fa-file-circle-check',
            'emerald',
            $user->id
        );

        return redirect()->route('epu.show', $permohonan->ladang->id)->with('success', 'Pembayaran fi lesen telah disahkan.');
    }

    /**
     * Hantar notifikasi kepada pegawai EPU berkaitan (Admin EPU Negeri, Admin EPU Jajahan, Pegawai Pelesen, Pengarah, Super Admin)
     */
    private function notifyEpuOfficers(array $roles, ?string $jajahan, string $title, string $message, ?string $actionUrl = null, string $icon = 'fa-solid fa-bell', string $color = 'emerald', ?int $excludeUserId = null): void
    {
        $rolesExpanded = $roles;
        if (in_array('admin_epu', $roles)) {
            $rolesExpanded[] = 'admin_epu_negeri';
            $rolesExpanded[] = 'pegawai_pelesen';
            $rolesExpanded[] = 'pengarah';
        }
        if (in_array('pegawai_verifikasi_epu', $roles)) {
            $rolesExpanded[] = 'admin_epu_jajahan';
        }
        $rolesExpanded[] = 'super_admin';
        $rolesExpanded = array_values(array_unique($rolesExpanded));

        $query = User::where(function ($q) use ($rolesExpanded) {
            $q->whereIn('role', $rolesExpanded);
            foreach ($rolesExpanded as $r) {
                $q->orWhereJsonContains('roles', $r);
            }
        });

        if ($jajahan) {
            $query->where(function ($q) use ($jajahan) {
                $q->where(function ($sub) use ($jajahan) {
                    $sub->where(function ($roleSub) {
                        $roleSub->whereIn('role', ['pegawai_verifikasi_epu', 'admin_epu_jajahan'])
                                ->orWhereJsonContains('roles', 'pegawai_verifikasi_epu')
                                ->orWhereJsonContains('roles', 'admin_epu_jajahan');
                    })->where('jajahan', $jajahan);
                })
                ->orWhere(function ($stateSub) {
                    $stateSub->whereIn('role', ['admin_epu', 'admin_epu_negeri', 'pengarah', 'pegawai_pelesen', 'super_admin'])
                             ->orWhereJsonContains('roles', 'admin_epu')
                             ->orWhereJsonContains('roles', 'admin_epu_negeri')
                             ->orWhereJsonContains('roles', 'pengarah')
                             ->orWhereJsonContains('roles', 'pegawai_pelesen')
                             ->orWhereJsonContains('roles', 'super_admin');
                });
            });
        }

        if ($excludeUserId) {
            $query->where('id', '!=', $excludeUserId);
        }

        $officers = $query->get();

        foreach ($officers as $officer) {
            \App\Models\UserNotification::send(
                $officer->id,
                $title,
                $message,
                'epu',
                $actionUrl,
                $icon,
                $color
            );
        }
    }

    private function uploadFileSafely($file, $folder)
    {
        if (!$file || !$file->isValid()) {
            return null;
        }

        $extension = $file->getClientOriginalExtension() ?: 'pdf';
        $filename = time() . '_' . uniqid() . '.' . $extension;

        try {
            return $file->storeAs($folder, $filename, 'public');
        } catch (\Throwable $e) {
            try {
                $targetDir = storage_path('app/public/' . $folder);
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                $file->move($targetDir, $filename);
                return $folder . '/' . $filename;
            } catch (\Throwable $ex) {
                return null;
            }
        }
    }
}
