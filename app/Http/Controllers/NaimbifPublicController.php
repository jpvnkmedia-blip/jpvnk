<?php

namespace App\Http\Controllers;

use App\Models\NaimbifPermohonan;
use App\Models\NaimbifInventoriTernakan;
use App\Models\Pemunya;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NaimbifPublicController extends Controller
{
    /**
     * Laman Utama Portal NAIMbif JPVNK
     */
    public function index()
    {
        $totalLulus = NaimbifPermohonan::where('status_negeri', 'Lulus')->count();
        $totalTernakan = NaimbifInventoriTernakan::sum('jumlah_baka');
        $totalPermohonan = NaimbifPermohonan::count();
        $jajahanCount = count(NaimbifPermohonan::JAJAHAN_LIST);

        return view('naimbif.public.home', compact('totalLulus', 'totalTernakan', 'totalPermohonan', 'jajahanCount'));
    }

    /**
     * Papar Borang Permohonan Baru
     */
    public function create()
    {
        $user = Auth::user();
        if ($user && $user->isPurePengarah()) {
            abort(403, 'Akses Ditolak: Pengarah Perkhidmatan Veterinar Negeri tidak dibenarkan membuat permohonan penyertaan Ladang Bridlot NAIMbif.');
        }

        $jajahans = NaimbifPermohonan::JAJAHAN_LIST;
        $bakas = NaimbifPermohonan::BAKA_LIST;

        $pemunya = null;
        if ($user) {
            $pemunya = Pemunya::where('user_id', $user->id)
                ->orWhere('no_kp', $user->ic_number)
                ->first();
        }

        return view('naimbif.public.apply', compact('jajahans', 'bakas', 'user', 'pemunya'));
    }

    /**
     * API Semakan No. Kad Pengenalan untuk Auto-fill Data Penternak JPVNK
     */
    public function checkExistingIc(Request $request)
    {
        $ic = preg_replace('/[^0-9]/', '', $request->get('no_kp', ''));
        if (strlen($ic) < 6) {
            return response()->json(['found' => false]);
        }

        // 1. Semak jika ada permohonan aktif
        $activeApp = NaimbifPermohonan::where('no_kp', $ic)
            ->where(function ($q) {
                $q->whereNull('status_negeri')
                  ->orWhere('status_negeri', '!=', 'Gagal');
            })
            ->latest()
            ->first();

        // 2. Semak rekod Pemunya, User & Rekod Terdahulu
        $pemunya = Pemunya::with('ternakan')->where('no_kp', $ic)->first();
        $user = User::where('ic_number', $ic)
            ->orWhere('id', $pemunya?->user_id)
            ->first();

        $pastNaimbif = NaimbifPermohonan::with('inventoriTernakan')
            ->where('no_kp', $ic)
            ->latest()
            ->first();

        $courseApp = null;
        if ($user) {
            $courseApp = \App\Models\CourseApplication::with('course')
                ->where('user_id', $user->id)
                ->whereIn('status', ['approved', 'completed', 'attended', 'sijil_dikeluarkan'])
                ->latest()
                ->first();
        }

        $epuLadang = null;
        if ($user || $pemunya) {
            $epuLadang = \App\Models\EpuLadang::where('user_id', $user?->id)
                ->orWhere('pemunya_id', $pemunya?->id)
                ->latest()
                ->first();
        }

        $hasPawah = false;
        if ($pemunya || $user) {
            $hasPawah = \App\Models\PawahPerjanjian::where('pemunya_id', $pemunya?->id)
                ->orWhere('user_id', $user?->id)
                ->exists();
        }

        $found = (bool) ($activeApp || $pemunya || $user || $pastNaimbif);

        if (!$found) {
            return response()->json([
                'found' => false,
                'has_active_application' => false,
            ]);
        }

        // Bina Objek Data Auto-fill
        $nama = $pemunya?->nama ?: ($user?->name ?: ($pastNaimbif?->nama ?: ''));
        $noTelefon = $pemunya?->no_telefon ?: ($user?->phone ?: ($pastNaimbif?->no_telefon ?: ''));
        $alamatTetap = $pemunya?->alamat ?: ($user?->address ?: ($pastNaimbif?->alamat_tetap ?: ''));
        $poskod = $pemunya?->poskod ?: ($user?->poskod ?: ($pastNaimbif?->poskod ?: ''));
        $jajahan = $pemunya?->jajahan ?: ($user?->jajahan ?: ($pastNaimbif?->jajahan ?: ''));

        $pengalamanMenternak = $pastNaimbif?->pengalaman_menternak ?? ($epuLadang ? 5 : null);
        $statusPenternakan = $pastNaimbif?->status_penternakan ?? 'Sepenuh Masa';

        $pernahKursus = $courseApp ? '1' : ($pastNaimbif ? ($pastNaimbif->pernah_kursus ? '1' : '0') : null);
        $namaKursus = $courseApp ? ($courseApp->course?->tajuk ?: 'Kursus Asas Penternakan Ruminan JPVNK') : ($pastNaimbif?->nama_kursus ?: '');
        $anjuranKursus = $courseApp ? 'Jabatan Perkhidmatan Veterinar Negeri Kelantan (JPVNK)' : ($pastNaimbif?->anjuran_kursus ?: '');
        $berminatKursus = $pastNaimbif ? ($pastNaimbif->berminat_kursus_jpvnk ? '1' : '0') : '1';

        // Maklumat Ladang
        $alamatLadang = $pastNaimbif?->alamat_ladang ?: ($epuLadang?->alamat_ladang ?: ($pemunya?->ternakan?->first()?->lokasi_kandang ?: $alamatTetap));
        $poskodLadang = $pastNaimbif?->poskod_ladang ?: ($epuLadang?->poskod ?: ($pemunya?->ternakan?->first()?->poskod ?: $poskod));
        $jajahanLadang = $pastNaimbif?->jajahan_ladang ?: ($epuLadang?->jajahan ?: ($pemunya?->ternakan?->first()?->jajahan ?: $jajahan));
        $gpsLat = $pastNaimbif?->gps_latitud ?: ($epuLadang?->latitude ?: '');
        $gpsLng = $pastNaimbif?->gps_longitud ?: ($epuLadang?->longitude ?: '');

        $statusTanah = $pastNaimbif?->status_tanah ?: ($epuLadang?->status_pemilikan ?: 'Sendiri');
        $statusTanahLain = $pastNaimbif?->status_tanah_lain ?: '';
        $keluasanTanah = $pastNaimbif?->keluasan_tanah ?: ($epuLadang?->keluasan_hektar ? round($epuLadang->keluasan_hektar * 2.47105, 2) : '');
        $padangRagut = $pastNaimbif?->padang_ragut ?: 'Ada';
        $bilanganPekerja = $pastNaimbif?->bilangan_pekerja ?? ($epuLadang?->bilangan_pekerja ?? 1);

        // Maklumat Ternakan
        $puncaTernakan = $hasPawah ? 'Pawah' : ($pastNaimbif?->punca_ternakan ?: 'Beli');
        $puncaTernakanLain = $pastNaimbif?->punca_ternakan_lain ?: '';
        $kaedahPembiakan = $pastNaimbif?->kaedah_pembiakan ?: 'Permanian Beradas';

        // Stok Baka Sedia Ada
        $stokBaka = [
            'charolais' => ['betina_anak' => 0, 'betina_dara' => 0, 'betina_induk' => 0, 'jantan_anak' => 0, 'jantan_pejantan' => 0],
            'belgian_blue' => ['betina_anak' => 0, 'betina_dara' => 0, 'betina_induk' => 0, 'jantan_anak' => 0, 'jantan_pejantan' => 0],
            'blonde_daquitaine' => ['betina_anak' => 0, 'betina_dara' => 0, 'betina_induk' => 0, 'jantan_anak' => 0, 'jantan_pejantan' => 0],
            'limousin' => ['betina_anak' => 0, 'betina_dara' => 0, 'betina_induk' => 0, 'jantan_anak' => 0, 'jantan_pejantan' => 0],
            'kedah_kelantan' => ['betina_anak' => 0, 'betina_dara' => 0, 'betina_induk' => 0, 'jantan_anak' => 0, 'jantan_pejantan' => 0],
            'lain_lain' => ['betina_anak' => 0, 'betina_dara' => 0, 'betina_induk' => 0, 'jantan_anak' => 0, 'jantan_pejantan' => 0, 'nama_baka_lain' => ''],
        ];

        // Isikan stok baka daripada rekod Ternakan EPTR
        if ($pemunya && $pemunya->ternakan && $pemunya->ternakan->count() > 0) {
            foreach ($pemunya->ternakan as $t) {
                $bakaUpper = strtoupper(trim($t->baka ?: ''));
                $key = 'lain_lain';
                if (str_contains($bakaUpper, 'CHAROLAIS')) $key = 'charolais';
                elseif (str_contains($bakaUpper, 'BELGIAN') || str_contains($bakaUpper, 'BLUE')) $key = 'belgian_blue';
                elseif (str_contains($bakaUpper, 'BLONDE') || str_contains($bakaUpper, 'AQUITAINE')) $key = 'blonde_daquitaine';
                elseif (str_contains($bakaUpper, 'LIMOUSIN')) $key = 'limousin';
                elseif (str_contains($bakaUpper, 'KELANTAN') || str_contains($bakaUpper, 'KK')) $key = 'kedah_kelantan';

                $jantina = strtolower($t->jantina ?: '');
                $umur = (float) ($t->umur ?: 1);

                if (str_contains($jantina, 'betina')) {
                    if ($umur < 1) $stokBaka[$key]['betina_anak']++;
                    elseif ($umur <= 2) $stokBaka[$key]['betina_dara']++;
                    else $stokBaka[$key]['betina_induk']++;
                } else {
                    if ($umur < 1.5) $stokBaka[$key]['jantan_anak']++;
                    else $stokBaka[$key]['jantan_pejantan']++;
                }
            }
        } elseif ($pastNaimbif && $pastNaimbif->inventoriTernakan && $pastNaimbif->inventoriTernakan->count() > 0) {
            foreach ($pastNaimbif->inventoriTernakan as $inv) {
                $slug = \Illuminate\Support\Str::slug($inv->baka, '_');
                if (isset($stokBaka[$slug])) {
                    $stokBaka[$slug]['betina_anak'] = (int) $inv->betina_anak;
                    $stokBaka[$slug]['betina_dara'] = (int) $inv->betina_dara;
                    $stokBaka[$slug]['betina_induk'] = (int) $inv->betina_induk;
                    $stokBaka[$slug]['jantan_anak'] = (int) $inv->jantan_anak;
                    $stokBaka[$slug]['jantan_pejantan'] = (int) $inv->jantan_pejantan;
                    if ($slug === 'lain_lain' && $inv->nama_baka_lain) {
                        $stokBaka[$slug]['nama_baka_lain'] = $inv->nama_baka_lain;
                    }
                }
            }
        }

        // Tentukan punca sumber data untuk paparan mesra pengguna
        $sources = [];
        if ($pemunya) $sources[] = 'Pangkalan Data Penternak (Pemunya)';
        if ($user) $sources[] = 'Akaun Pengguna JPVNK';
        if ($pemunya?->ternakan?->count() > 0) $sources[] = 'Daftar Ternakan EPTR (' . $pemunya->ternakan->count() . ' Ekor)';
        if ($courseApp) $sources[] = 'Rekod Kursus Veterinar';
        if ($pastNaimbif) $sources[] = 'Rekod Ladang NAIMbif';

        $sourceLabel = implode(' • ', $sources) ?: 'Pangkalan Data Sepunya JPVNK';

        $autofillData = [
            'nama' => $nama,
            'no_telefon' => $noTelefon,
            'alamat_tetap' => $alamatTetap,
            'poskod' => $poskod,
            'jajahan' => $jajahan,
            'pengalaman_menternak' => $pengalamanMenternak,
            'status_penternakan' => $statusPenternakan,
            'pernah_kursus' => $pernahKursus,
            'nama_kursus' => $namaKursus,
            'anjuran_kursus' => $anjuranKursus,
            'berminat_kursus_jpvnk' => $berminatKursus,
            'alamat_ladang' => $alamatLadang,
            'poskod_ladang' => $poskodLadang,
            'jajahan_ladang' => $jajahanLadang,
            'gps_latitud' => $gpsLat,
            'gps_longitud' => $gpsLng,
            'status_tanah' => $statusTanah,
            'status_tanah_lain' => $statusTanahLain,
            'keluasan_tanah' => $keluasanTanah,
            'padang_ragut' => $padangRagut,
            'bilangan_pekerja' => $bilanganPekerja,
            'punca_ternakan' => $puncaTernakan,
            'punca_ternakan_lain' => $puncaTernakanLain,
            'kaedah_pembiakan' => $kaedahPembiakan,
            'stok' => $stokBaka,
        ];

        // Senarai field yang berjaya diisi
        $autofillKeys = [];
        foreach ($autofillData as $k => $v) {
            if ($k !== 'stok' && !empty($v)) {
                $autofillKeys[] = $k;
            }
        }

        $responsePayload = [
            'found' => true,
            'has_active_application' => (bool) $activeApp,
            'source' => $pemunya ? 'pemunya' : ($user ? 'user' : 'naimbif'),
            'source_label' => $sourceLabel,
            'autofill_keys' => $autofillKeys,
            'data' => [
                'nama' => $nama,
                'no_telefon' => $noTelefon,
                'alamat_tetap' => $alamatTetap,
                'poskod' => $poskod,
                'jajahan' => $jajahan,
                'daerah' => $pemunya?->daerah ?: $jajahan,
            ],
            'autofill' => $autofillData,
        ];

        if ($activeApp) {
            $responsePayload['application'] = [
                'no_rujukan' => $activeApp->no_rujukan,
                'nama' => $activeApp->nama,
                'status' => $activeApp->status_negeri === 'Lulus' ? 'Lulus' : ($activeApp->syor_permohonan === 'Disokong' ? 'Disokong Jajahan' : $activeApp->status_kelengkapan),
                'tarikh' => $activeApp->tarikh_permohonan ? $activeApp->tarikh_permohonan->format('d/m/Y') : $activeApp->created_at->format('d/m/Y'),
                'check_url' => route('naimbif.public.check_status', ['carian' => $activeApp->no_rujukan]),
                'edit_url' => route('naimbif.public.edit', $activeApp->no_rujukan),
            ];
        }

        return response()->json($responsePayload);
    }

    /**
     * Simpan Permohonan Baru
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->isPurePengarah()) {
            abort(403, 'Akses Ditolak: Pengarah Perkhidmatan Veterinar Negeri tidak dibenarkan membuat permohonan penyertaan Ladang Bridlot NAIMbif.');
        }

        $rules = [
            // Maklumat Peserta
            'nama' => 'required|string|max:255',
            'no_kp' => 'required|string|max:20',
            'no_telefon' => 'required|string|max:30',
            'alamat_tetap' => 'required|string',
            'poskod' => 'required|string|max:10',
            'jajahan' => 'required|string|in:' . implode(',', NaimbifPermohonan::JAJAHAN_LIST),
            'pengalaman_menternak' => 'required|integer|min:0|max:80',
            'status_penternakan' => 'required|string|in:Sepenuh Masa,Sampingan',
            'pernah_kursus' => 'required|in:1,0',
            'nama_kursus' => 'nullable|required_if:pernah_kursus,1|string|max:255',
            'anjuran_kursus' => 'nullable|required_if:pernah_kursus,1|string|max:255',
            'berminat_kursus_jpvnk' => 'nullable|in:1,0',

            // Maklumat Ladang
            'alamat_ladang' => 'nullable|string',
            'poskod_ladang' => 'nullable|string|max:10',
            'jajahan_ladang' => 'nullable|string|in:' . implode(',', NaimbifPermohonan::JAJAHAN_LIST),
            'gps_longitud' => 'nullable|string|max:50',
            'gps_latitud' => 'nullable|string|max:50',
            'status_tanah' => 'required|string|in:Sendiri,Sewa,Kerajaan,Lain-lain',
            'status_tanah_lain' => 'nullable|required_if:status_tanah,Lain-lain|string|max:255',
            'keluasan_tanah' => 'required|numeric|min:0.1',
            'padang_ragut' => 'required|string|in:Ada,Tiada',
            'bilangan_pekerja' => 'required|integer|min:0',

            // Maklumat Ternakan
            'punca_ternakan' => 'required|string|in:Beli,Pawah,Lain-lain',
            'punca_ternakan_lain' => 'nullable|required_if:punca_ternakan,Lain-lain|string|max:255',
            'kaedah_pembiakan' => 'required|string|in:Asli,Permanian Beradas',

            // Pengakuan
            'pengakuan_benar' => 'accepted',
            'tandatangan' => 'nullable|string',
            'tarikh_permohonan' => 'required|date',

            // Stok Ternakan (Array)
            'stok' => 'required|array',
        ];

        $messages = [
            'nama.required' => 'Sila masukkan Nama Pemohon.',
            'no_kp.required' => 'Sila masukkan No. Kad Pengenalan.',
            'no_telefon.required' => 'Sila masukkan No. Telefon.',
            'alamat_tetap.required' => 'Sila masukkan Alamat Tetap.',
            'poskod.required' => 'Sila masukkan Poskod.',
            'jajahan.required' => 'Sila pilih Jajahan.',
            'keluasan_tanah.required' => 'Sila masukkan Keluasan Tanah (Ekar).',
            'pengakuan_benar.accepted' => 'Sila tandakan pengakuan bahawa butiran di atas adalah benar.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $cleanNoKp = preg_replace('/[^0-9]/', '', $request->no_kp);

        // Semakan Sekatan: 1 Permohonan Aktif Sahaja Bagi Setiap No. KP
        $existingActiveApp = NaimbifPermohonan::where('no_kp', $cleanNoKp)
            ->where(function ($query) {
                $query->whereNull('status_negeri')
                      ->orWhere('status_negeri', '!=', 'Gagal');
            })
            ->latest()
            ->first();

        if ($existingActiveApp) {
            return redirect()->back()
                ->withInput()
                ->with('duplicate_error', [
                    'no_kp' => $request->no_kp,
                    'no_rujukan' => $existingActiveApp->no_rujukan,
                    'nama' => $existingActiveApp->nama,
                    'status' => $existingActiveApp->status_permohonan ?: ($existingActiveApp->status_negeri ?: $existingActiveApp->syor_permohonan),
                    'tarikh' => $existingActiveApp->tarikh_permohonan ? $existingActiveApp->tarikh_permohonan->format('d/m/Y') : $existingActiveApp->created_at->format('d/m/Y'),
                ]);
        }

        // Hubungkan ke User atau Pemunya jika wujud, atau daftar Pemunya baru secara automatik
        $linkedUser = Auth::user() ?: User::where('ic_number', $cleanNoKp)->first();
        $linkedPemunya = Pemunya::where('no_kp', $cleanNoKp)->first();

        if (!$linkedPemunya) {
            $linkedPemunya = Pemunya::create([
                'user_id' => $linkedUser?->id,
                'nama' => strtoupper(trim($request->nama)),
                'no_kp' => $cleanNoKp,
                'no_telefon' => trim($request->no_telefon),
                'alamat' => trim($request->alamat_tetap),
                'poskod' => trim($request->poskod),
                'jajahan' => $request->jajahan,
                'daerah' => $request->jajahan,
            ]);
        }

        try {
            DB::beginTransaction();

            $application = NaimbifPermohonan::create([
                'user_id' => $linkedUser?->id,
                'pemunya_id' => $linkedPemunya?->id,
                'nama' => strtoupper(trim($request->nama)),
                'no_kp' => $cleanNoKp,
                'no_telefon' => trim($request->no_telefon),
                'alamat_tetap' => trim($request->alamat_tetap),
                'poskod' => trim($request->poskod),
                'jajahan' => $request->jajahan,
                'pengalaman_menternak' => (int) $request->pengalaman_menternak,
                'status_penternakan' => $request->status_penternakan,
                'pernah_kursus' => (bool) $request->pernah_kursus,
                'nama_kursus' => $request->pernah_kursus ? $request->nama_kursus : null,
                'anjuran_kursus' => $request->pernah_kursus ? $request->anjuran_kursus : null,
                'berminat_kursus_jpvnk' => !$request->pernah_kursus ? (bool) $request->berminat_kursus_jpvnk : null,

                'alamat_ladang' => $request->alamat_ladang ?: $request->alamat_tetap,
                'poskod_ladang' => $request->poskod_ladang ?: $request->poskod,
                'jajahan_ladang' => $request->jajahan_ladang ?: $request->jajahan,
                'gps_longitud' => $request->gps_longitud,
                'gps_latitud' => $request->gps_latitud,
                'status_tanah' => $request->status_tanah,
                'status_tanah_lain' => $request->status_tanah === 'Lain-lain' ? $request->status_tanah_lain : null,
                'keluasan_tanah' => $request->keluasan_tanah,
                'padang_ragut' => $request->padang_ragut,
                'bilangan_pekerja' => (int) $request->bilangan_pekerja,

                'punca_ternakan' => $request->punca_ternakan,
                'punca_ternakan_lain' => $request->punca_ternakan === 'Lain-lain' ? $request->punca_ternakan_lain : null,
                'kaedah_pembiakan' => $request->kaedah_pembiakan,

                'pengakuan_benar' => true,
                'tandatangan' => $request->tandatangan,
                'tarikh_permohonan' => $request->tarikh_permohonan,

                'status_kelengkapan' => 'Dalam Semakan',
                'syor_permohonan' => 'Belum Disemak',
                'status_negeri' => 'Menunggu Kelulusan',
                'status_permohonan' => 'Dihantar',
            ]);

            // Simpan Pecahan Baka Ternakan
            if (is_array($request->stok)) {
                foreach ($request->stok as $baka => $data) {
                    $bAnak = (int) ($data['betina_anak'] ?? 0);
                    $bDara = (int) ($data['betina_dara'] ?? 0);
                    $bInduk = (int) ($data['betina_induk'] ?? 0);
                    $jAnak = (int) ($data['jantan_anak'] ?? 0);
                    $jPejantan = (int) ($data['jantan_pejantan'] ?? 0);
                    $jumlah = $bAnak + $bDara + $bInduk + $jAnak + $jPejantan;

                    if ($jumlah > 0 || !empty($data['nama_baka_lain'])) {
                        NaimbifInventoriTernakan::create([
                            'naimbif_permohonan_id' => $application->id,
                            'baka' => $baka,
                            'nama_baka_lain' => $baka === 'LAIN-LAIN' ? ($data['nama_baka_lain'] ?? null) : null,
                            'betina_anak' => $bAnak,
                            'betina_dara' => $bDara,
                            'betina_induk' => $bInduk,
                            'jantan_anak' => $jAnak,
                            'jantan_pejantan' => $jPejantan,
                            'jumlah_baka' => $jumlah,
                        ]);
                    }
                }
            }

            // Hantar Notifikasi kepada Pentadbir / Pegawai
            if ($linkedUser) {
                UserNotification::send(
                    $linkedUser->id,
                    'Permohonan NAIMbif Dihantar',
                    'Permohonan Program NAIMbif (' . $application->no_rujukan . ') anda telah berjaya dihantar untuk semakan Pejabat JPVNK Jajahan ' . $application->jajahan . '.',
                    'naimbif',
                    route('naimbif.public.check_status', ['no_rujukan' => $application->no_rujukan]),
                    'fa-solid fa-cow',
                    'emerald'
                );
            }

            DB::commit();

            return redirect()->route('naimbif.public.success', ['no_rujukan' => $application->no_rujukan]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ralat sistem ketika memproses permohonan: ' . $e->getMessage());
        }
    }

    /**
     * Paparan Berjaya Hantar Permohonan
     */
    public function success($no_rujukan)
    {
        $application = NaimbifPermohonan::with('inventoriTernakan')
            ->where('no_rujukan', $no_rujukan)
            ->firstOrFail();

        return view('naimbif.public.success', compact('application'));
    }

    /**
     * Semakan Status Permohonan Online
     */
    public function checkStatus(Request $request)
    {
        $application = null;
        $applications = collect();
        $carianDibuat = false;
        $input = trim($request->input('carian', $request->input('keyword', $request->input('query', $request->input('no_rujukan', $request->input('no_kp', ''))))));

        if ($request->isMethod('post') || !empty($input)) {
            $cleanInput = preg_replace('/[^0-9]/', '', $input);
            $carianDibuat = true;

            if (!empty($input)) {
                $queryBuilder = NaimbifPermohonan::with('inventoriTernakan')
                    ->where(function ($q) use ($input, $cleanInput) {
                        $q->where('no_rujukan', strtoupper($input))
                          ->orWhere('no_rujukan', 'like', "%{$input}%");
                        if (!empty($cleanInput)) {
                            $q->orWhere('no_kp', $cleanInput);
                        }
                    });

                $applications = $queryBuilder->latest()->get();
                $application = $applications->first();
            }
        }

        $query = $input;
        $searched = $carianDibuat;

        return view('naimbif.public.check_status', compact('application', 'applications', 'carianDibuat', 'query', 'searched'));
    }

    /**
     * Cetak Borang Permohonan Rasmi NAIMbif JPVNK
     */
    public function printForm($no_rujukan)
    {
        $application = NaimbifPermohonan::with(['inventoriTernakan', 'disemakOleh', 'diluluskanOleh'])
            ->where('no_rujukan', $no_rujukan)
            ->firstOrFail();

        $inventories = [];
        foreach ($application->inventoriTernakan as $inv) {
            $inventories[$inv->baka] = $inv;
        }

        return view('naimbif.public.print', compact('application', 'inventories'));
    }

    /**
     * Kemaskini Permohonan (Sebelum Disahkan Jajahan)
     */
    public function edit($no_rujukan)
    {
        $application = NaimbifPermohonan::with('inventoriTernakan')
            ->where('no_rujukan', $no_rujukan)
            ->firstOrFail();

        if ($application->isDisemakJajahan() || in_array($application->status_negeri, ['Lulus', 'Gagal'])) {
            return redirect()->route('naimbif.public.check_status', ['no_rujukan' => $no_rujukan])
                ->with('error', 'Permohonan ini telah disemak oleh pegawai dan tidak boleh dikemaskini secara terbuka.');
        }

        $jajahans = NaimbifPermohonan::JAJAHAN_LIST;
        $bakas = NaimbifPermohonan::BAKA_LIST;

        $inventories = [];
        foreach ($application->inventoriTernakan as $inv) {
            $inventories[$inv->baka] = $inv;
        }

        return view('naimbif.public.edit', compact('application', 'jajahans', 'bakas', 'inventories'));
    }

    /**
     * Simpan Kemas Kini Permohonan
     */
    public function update(Request $request, $no_rujukan)
    {
        $application = NaimbifPermohonan::where('no_rujukan', $no_rujukan)->firstOrFail();

        if ($application->isDisemakJajahan() || in_array($application->status_negeri, ['Lulus', 'Gagal'])) {
            return redirect()->route('naimbif.public.check_status', ['no_rujukan' => $no_rujukan])
                ->with('error', 'Permohonan ini tidak boleh dikemaskini kerana sedang atau telah diproses oleh pihak jabatan.');
        }

        $request->validate([
            'no_telefon' => 'required|string|max:30',
            'alamat_tetap' => 'required|string',
            'poskod' => 'required|string|max:10',
            'jajahan' => 'required|string|in:' . implode(',', NaimbifPermohonan::JAJAHAN_LIST),
            'pengalaman_menternak' => 'required|integer|min:0|max:80',
            'status_penternakan' => 'required|string|in:Sepenuh Masa,Sampingan',
            'keluasan_tanah' => 'required|numeric|min:0.1',
            'stok' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            $application->update([
                'no_telefon' => trim($request->no_telefon),
                'alamat_tetap' => trim($request->alamat_tetap),
                'poskod' => trim($request->poskod),
                'jajahan' => $request->jajahan,
                'pengalaman_menternak' => (int) $request->pengalaman_menternak,
                'status_penternakan' => $request->status_penternakan,
                'pernah_kursus' => (bool) $request->pernah_kursus,
                'nama_kursus' => $request->pernah_kursus ? $request->nama_kursus : null,
                'anjuran_kursus' => $request->pernah_kursus ? $request->anjuran_kursus : null,
                'berminat_kursus_jpvnk' => !$request->pernah_kursus ? (bool) $request->berminat_kursus_jpvnk : null,
                'alamat_ladang' => $request->alamat_ladang ?: $request->alamat_tetap,
                'poskod_ladang' => $request->poskod_ladang ?: $request->poskod,
                'jajahan_ladang' => $request->jajahan_ladang ?: $request->jajahan,
                'gps_longitud' => $request->gps_longitud,
                'gps_latitud' => $request->gps_latitud,
                'status_tanah' => $request->status_tanah,
                'status_tanah_lain' => $request->status_tanah === 'Lain-lain' ? $request->status_tanah_lain : null,
                'keluasan_tanah' => $request->keluasan_tanah,
                'padang_ragut' => $request->padang_ragut,
                'bilangan_pekerja' => (int) $request->bilangan_pekerja,
                'punca_ternakan' => $request->punca_ternakan,
                'punca_ternakan_lain' => $request->punca_ternakan === 'Lain-lain' ? $request->punca_ternakan_lain : null,
                'kaedah_pembiakan' => $request->kaedah_pembiakan,
            ]);

            // Kemaskini Inventori
            $application->inventoriTernakan()->delete();
            if (is_array($request->stok)) {
                foreach ($request->stok as $baka => $data) {
                    $bAnak = (int) ($data['betina_anak'] ?? 0);
                    $bDara = (int) ($data['betina_dara'] ?? 0);
                    $bInduk = (int) ($data['betina_induk'] ?? 0);
                    $jAnak = (int) ($data['jantan_anak'] ?? 0);
                    $jPejantan = (int) ($data['jantan_pejantan'] ?? 0);
                    $jumlah = $bAnak + $bDara + $bInduk + $jAnak + $jPejantan;

                    if ($jumlah > 0 || !empty($data['nama_baka_lain'])) {
                        NaimbifInventoriTernakan::create([
                            'naimbif_permohonan_id' => $application->id,
                            'baka' => $baka,
                            'nama_baka_lain' => $baka === 'LAIN-LAIN' ? ($data['nama_baka_lain'] ?? null) : null,
                            'betina_anak' => $bAnak,
                            'betina_dara' => $bDara,
                            'betina_induk' => $bInduk,
                            'jantan_anak' => $jAnak,
                            'jantan_pejantan' => $jPejantan,
                            'jumlah_baka' => $jumlah,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('naimbif.public.check_status', ['no_rujukan' => $application->no_rujukan])
                ->with('success', 'Maklumat permohonan telah berjaya dikemaskini.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ralat semasa mengemaskini maklumat: ' . $e->getMessage());
        }
    }
}
