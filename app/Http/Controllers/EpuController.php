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
                    if ($user->role === 'admin_pejabat') {
                        return redirect()->route('inventori.index')->with('error', 'Akses Ditolak: Peranan Admin Pejabat dikhaskan untuk Pengurusan Pejabat (Inventori & Kenderaan Rasmi) sahaja.');
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

        // Admin Pejabat hanya dibenarkan menguruskan Pejabat (Inventori & Kenderaan Rasmi)
        if ($user->role === 'admin_pejabat') {
            return redirect()->route('inventori.index')->with('error', 'Akses Ditolak: Peranan Admin Pejabat dikhaskan untuk Pengurusan Pejabat (Inventori & Kenderaan Rasmi) sahaja.');
        }

        $query = EpuLadang::with('pemilik', 'permohonanList', 'pemeriksaanList');

        if (!$user->isStaff()) {
            $query->where('user_id', $user->id);
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
        $totalLadang = EpuLadang::count();
        $totalLesenAktif = EpuPermohonan::where('status', 'Diluluskan')->count();
        $totalRebanTertutup = EpuLadang::where('sistem_reban', 'Tertutup')->count();
        $totalPemeriksaan = EpuPemeriksaan::count();

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
            'no_resit_bayaran' => 'RES-EPU-' . date('Y') . '-' . rand(100, 999),
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

        return redirect()->route('epu.show', $ladang->id)->with('success', 'Permohonan Pendaftaran & Lesen Ladang Unggas (EPU Borang A) berjaya dihantar!');
    }

    // Maklumat Ladang & Senarai Lesen
    public function show($id)
    {
        $ladang = EpuLadang::with('pemilik', 'permohonanList.pelulus', 'pemeriksaanList.pegawai')->findOrFail($id);
        return view('epu.show', compact('ladang'));
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
        [$ladang, $permohonan] = $this->resolveLadangDanPermohonan($id);
        return view('epu.cetak-borang-a', compact('ladang', 'permohonan'));
    }

    // Cetak Borang B: Permohonan Pengecualian Lesen Perladangan Unggas
    public function cetakBorangBPengecualian($id)
    {
        [$ladang, $permohonan] = $this->resolveLadangDanPermohonan($id);
        return view('epu.cetak-borang-b-pengecualian', compact('ladang', 'permohonan'));
    }

    // Cetak Borang C: Sijil Pengecualian Lesen Perladangan Unggas
    public function cetakSijilPengecualianC($id)
    {
        [$ladang, $permohonan] = $this->resolveLadangDanPermohonan($id);
        return view('epu.cetak-sijil-pengecualian-c', compact('ladang', 'permohonan'));
    }

    // Cetak Borang A: Permohonan Salinan Pendua Lesen
    public function cetakSalinanPendua($id)
    {
        [$ladang, $permohonan] = $this->resolveLadangDanPermohonan($id);
        return view('epu.cetak-salinan-pendua', compact('ladang', 'permohonan'));
    }

    // EPU Borang B: Cetak Lesen Ladang Unggas
    public function cetakLesen($permohonanId)
    {
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

        return redirect()->route('epu.show', $ladang->id)->with('success', 'Laporan Pemeriksaan Tapak & Penguatkuasaan (EPU Borang D) telah direkodkan.');
    }

    private function uploadFileSafely($file, $folder)
    {
        if (!$file || !$file->isValid()) {
            return null;
        }

        $realPath = $file->getRealPath();
        if (empty($realPath) || !file_exists($realPath)) {
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
