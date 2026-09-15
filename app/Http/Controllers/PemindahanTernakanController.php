<?php

namespace App\Http\Controllers;

use App\Models\PemindahanTernakan;
use App\Models\Pemunya;
use App\Models\Ternakan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PemindahanTernakanController extends Controller
{
    /**
     * Display a listing of livestock movement permits.
     */
    public function index(Request $request)
    {
        $query = PemindahanTernakan::with(['pemunya', 'user'])->latest();

        $user = Auth::user();
        if ($user && $user->role === 'orang_awam') {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('pemohon_ic', $user->no_ic ?? $user->ic_no ?? '')
                  ->orWhere('pemohon_nama', 'like', '%' . $user->name . '%');
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_rujukan', 'like', "%{$search}%")
                  ->orWhere('pemohon_nama', 'like', "%{$search}%")
                  ->orWhere('pemohon_ic', 'like', "%{$search}%")
                  ->orWhere('penerima_nama', 'like', "%{$search}%")
                  ->orWhere('no_kenderaan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jajahan')) {
            $query->where('jajahan_asal', $request->jajahan);
        }

        if ($request->filled('tujuan')) {
            $query->where('tujuan_pemindahan', $request->tujuan);
        }

        $pemindahanList = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => PemindahanTernakan::count(),
            'menunggu' => PemindahanTernakan::where('status', 'Menunggu Kelulusan')->count(),
            'diluluskan' => PemindahanTernakan::where('status', 'Diluluskan')->count(),
            'ditolak' => PemindahanTernakan::where('status', 'Ditolak')->count(),
        ];

        return view('eptr.pemindahan.index', compact('pemindahanList', 'stats'));
    }

    /**
     * Show the form for creating a new livestock movement permit.
     */
    public function create()
    {
        $user = Auth::user();
        $isStaff = $user ? $user->isStaff() : false;

        // Dapatkan profil Pemunya EPTR bagi pengguna semasa jika ada
        $currentPemunya = null;
        if ($user) {
            $currentPemunya = $user->pemunya;
            if (!$currentPemunya && $user->ic_number) {
                $currentPemunya = Pemunya::where('no_kp', $user->ic_number)->first();
            }
            if (!$currentPemunya && $user->id) {
                $currentPemunya = Pemunya::where('user_id', $user->id)->first();
            }
        }

        // Senarai ternakan milik pemohon (untuk dipilih ke dalam 50 baris tag)
        $userTernakanList = collect();
        if ($currentPemunya) {
            $userTernakanList = Ternakan::where('pemunya_id', $currentPemunya->id)
                ->whereIn('status', ['Aktif', 'Pawah'])
                ->orderBy('no_tag')
                ->get();
        }

        // Senarai semua pemunya (untuk kegunaan kakitangan/admin)
        $pemunyaList = Pemunya::with(['ternakan' => function ($q) {
            $q->whereIn('status', ['Aktif', 'Pawah'])->orderBy('no_tag');
        }])->orderBy('nama')->get();

        $ternakanList = Ternakan::where('status_pendaftaran', 'Aktif')
            ->orWhere('status_kelulusan', 'Diluluskan')
            ->orWhere('status_kelulusan', 'Lulus')
            ->orderBy('no_tag')
            ->get();

        $jajahanList = [
            'Bachok', 'Kota Bharu', 'Pasir Mas', 'Tumpat', 'Pasir Puteh',
            'Machang', 'Tanah Merah', 'Jeli', 'Kuala Krai', 'Gua Musang'
        ];

        $defaultJajahan = $currentPemunya->jajahan ?? ($user->jajahan ?? 'Bachok');
        $suggestedNoRujukan = PemindahanTernakan::generateNoRujukan($defaultJajahan);

        return view('eptr.pemindahan.create', compact(
            'user',
            'isStaff',
            'currentPemunya',
            'userTernakanList',
            'pemunyaList',
            'ternakanList',
            'jajahanList',
            'suggestedNoRujukan'
        ));
    }

    /**
     * Store a newly created livestock movement permit in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $isStaff = $user ? $user->isStaff() : false;

        $request->validate([
            'pemohon_nama' => 'required|string|max:255',
            'jajahan_asal' => 'required|string',
            'tujuan_pemindahan' => 'required|string',
            'jenis_ternakan' => 'required|string',
            'tarikh_jangka_pindah' => 'nullable|date',
        ]);

        // Dapatkan Pemunya ID
        $pemunyaId = $request->pemunya_id;
        if (!$isStaff && $user) {
            $currentPemunya = $user->pemunya ?? Pemunya::where('user_id', $user->id)->orWhere('no_kp', $user->ic_number)->first();
            if ($currentPemunya) {
                $pemunyaId = $currentPemunya->id;
            }
        }

        // Penerima: Jika kosong, gunakan maklumat pemohon
        $penerimaNama = $request->filled('penerima_nama') ? $request->penerima_nama : $request->pemohon_nama;
        $penerimaIc = $request->filled('penerima_ic') ? $request->penerima_ic : $request->pemohon_ic;
        $penerimaTel = $request->filled('penerima_tel') ? $request->penerima_tel : $request->pemohon_tel;
        $penerimaAlamat = $request->filled('penerima_alamat') ? $request->penerima_alamat : $request->pemohon_alamat;
        $penerimaIdPremis = $request->filled('penerima_id_premis') ? $request->penerima_id_premis : $request->pemohon_id_premis;
        $penerimaNegeri = strtoupper($request->penerima_negeri ?? 'KELANTAN');
        $penerimaJajahan = ($penerimaNegeri !== 'KELANTAN') 
            ? ($request->filled('penerima_jajahan') && $request->penerima_jajahan !== '-' ? $request->penerima_jajahan : 'Luar Kelantan')
            : ($request->filled('penerima_jajahan') ? $request->penerima_jajahan : $request->jajahan_asal);

        // Process tag list (up to 50 tags)
        $tags = [];
        $jantanCount = (int) $request->input('bilangan_jantan', 0);
        $betinaCount = (int) $request->input('bilangan_betina', 0);

        if ($request->has('tags') && is_array($request->tags)) {
            $calculatedJ = 0;
            $calculatedB = 0;

            // Dapatkan peta ternakan berdaftar untuk mengesahkan jantina daripada rekod EPTR
            $inputTags = array_filter(array_map(fn($t) => strtoupper(trim($t['no_tag'] ?? '')), $request->tags));
            $registeredMap = Ternakan::whereIn('no_tag', $inputTags)
                ->get()
                ->keyBy(fn($item) => strtoupper(trim($item->no_tag)));

            foreach ($request->tags as $idx => $tagData) {
                if (!empty($tagData['no_tag'])) {
                    $tagNo = trim(strtoupper($tagData['no_tag']));
                    $ternakanObj = $registeredMap->get($tagNo);

                    // Jantina wajib diambil berdasarkan pendaftaran ternakan jika wujud
                    $gender = 'Betina';
                    if ($ternakanObj) {
                        $gender = in_array(strtoupper($ternakanObj->jantina ?? ''), ['J', 'JANTAN']) ? 'Jantan' : 'Betina';
                    } elseif (!empty($tagData['jantina'])) {
                        $gender = in_array(strtoupper($tagData['jantina']), ['J', 'JANTAN']) ? 'Jantan' : 'Betina';
                    }

                    $tags[] = [
                        'bil' => count($tags) + 1,
                        'no_tag' => $tagNo,
                        'jantina' => $gender,
                        'ternakan_id' => $ternakanObj ? $ternakanObj->id : ($tagData['ternakan_id'] ?? null),
                    ];
                    if (strtoupper($gender) === 'JANTAN') {
                        $calculatedJ++;
                    } else {
                        $calculatedB++;
                    }
                }
            }
            if ($calculatedJ > 0 || $calculatedB > 0) {
                $jantanCount = $calculatedJ;
                $betinaCount = $calculatedB;
            }
        }

        // Bagi orang awam, No. Rujukan permit dijana secara automatik oleh sistem
        if (!$isStaff || empty($request->no_rujukan)) {
            $noRujukan = PemindahanTernakan::generateNoRujukan($request->jajahan_asal);
        } else {
            $noRujukan = $request->no_rujukan;
        }

        $pemindahan = PemindahanTernakan::create([
            'no_rujukan' => $noRujukan,
            'pemunya_id' => $pemunyaId,
            'user_id' => Auth::id(),
            'jajahan_asal' => $request->jajahan_asal,
            'tarikh_permohonan' => $request->tarikh_permohonan ?: date('Y-m-d'),
            'tarikh_jangka_pindah' => $request->tarikh_jangka_pindah,
            'tujuan_pemindahan' => strtoupper($request->tujuan_pemindahan),
            'jenis_ternakan' => strtoupper($request->jenis_ternakan),
            'bilangan_jantan' => $jantanCount,
            'bilangan_betina' => $betinaCount,
            'no_kenderaan' => strtoupper($request->no_kenderaan ?? ''),

            // Pemohon
            'pemohon_nama' => strtoupper($request->pemohon_nama),
            'pemohon_ic' => $request->pemohon_ic,
            'pemohon_tel' => $request->pemohon_tel,
            'pemohon_id_premis' => strtoupper($request->pemohon_id_premis ?? ''),
            'pemohon_alamat' => strtoupper($request->pemohon_alamat ?? ''),

            // Penerima (Default ke maklumat pemohon jika tidak diubah)
            'penerima_nama' => strtoupper($penerimaNama),
            'penerima_ic' => $penerimaIc,
            'penerima_tel' => $penerimaTel,
            'penerima_id_premis' => strtoupper($penerimaIdPremis ?? ''),
            'penerima_alamat' => strtoupper($penerimaAlamat ?? ''),
            'penerima_jajahan' => $penerimaJajahan,
            'penerima_negeri' => strtoupper($request->penerima_negeri ?? 'KELANTAN'),

            // Suntikan & Vaksinasi
            'tarikh_fmd_p1' => $request->tarikh_fmd_p1,
            'tarikh_fmd_p2' => $request->tarikh_fmd_p2,
            'tarikh_fmd_booster' => $request->tarikh_fmd_booster,
            'tarikh_lsd' => $request->tarikh_lsd,
            'nama_penyuntik_1' => strtoupper($request->nama_penyuntik_1 ?? 'NABIL RABANI BIN AHMAD'),
            'nama_penyuntik_2' => strtoupper($request->nama_penyuntik_2 ?? 'NUR FARAHTUL NAJWA BT SHAHRUL AZMI'),

            // Doktor Swasta / Ladang
            'nama_doktor_veterinar' => strtoupper($request->nama_doktor_veterinar ?? ''),
            'ic_doktor_veterinar' => $request->ic_doktor_veterinar,
            'tel_doktor_veterinar' => $request->tel_doktor_veterinar,
            'no_pendaftaran_doktor' => $request->no_pendaftaran_doktor,
            'no_mygap' => $request->no_mygap,

            // Status Kesihatan & Ubatan
            'status_penyakit_ruminan_besar' => $request->has('status_penyakit_ruminan_besar'),
            'status_penyakit_ruminan_kecil' => $request->has('status_penyakit_ruminan_kecil'),
            'lain_vaksin_nama' => $request->lain_vaksin_nama,
            'lain_vaksin_tarikh' => $request->lain_vaksin_tarikh,
            'tarikh_rawatan_terakhir' => $request->tarikh_rawatan_terakhir,
            'nama_ubat_terakhir' => $request->nama_ubat_terakhir,
            'cara_rawatan_terakhir' => $request->cara_rawatan_terakhir,

            // Sembelihan
            'nama_rumah_sembelih' => strtoupper($request->nama_rumah_sembelih ?? ''),
            'alamat_rumah_sembelih' => strtoupper($request->alamat_rumah_sembelih ?? ''),
            'tarikh_keluar_ladang' => $request->tarikh_keluar_ladang,
            'tarikh_sembelih' => $request->tarikh_sembelih,

            // Tags
            'senarai_tag' => $tags,

            // Status
            'status' => 'Menunggu Kelulusan',
        ]);

        return redirect()->route('eptr.pemindahan.show', $pemindahan->id)
            ->with('success', "Permohonan Pemindahan Ternakan berjaya didaftarkan dengan No. Rujukan: {$pemindahan->no_rujukan}");
    }

    /**
     * Display the specified permit details.
     */
    public function show($id)
    {
        $pemindahan = PemindahanTernakan::with(['pemunya', 'user'])->findOrFail($id);

        return view('eptr.pemindahan.show', compact('pemindahan'));
    }

    /**
     * Approve the permit.
     */
    public function lulus(Request $request, $id)
    {
        $pemindahan = PemindahanTernakan::findOrFail($id);
        $user = Auth::user();

        // Tentukan jajahan pegawai yang meluluskan
        $pegawaiJajahan = $request->filled('pegawai_jajahan')
            ? $request->pegawai_jajahan
            : ($user->jajahan ?: $pemindahan->jajahan_asal);

        $pegawaiJawatan = $request->filled('pegawai_jawatan')
            ? $request->pegawai_jawatan
            : "Pegawai Perkhidmatan Veterinar Jajahan {$pegawaiJajahan}";

        $pemindahan->update([
            'status' => 'Diluluskan',
            'pegawai_nama' => $request->pegawai_nama ?: $user->name,
            'pegawai_jawatan' => $pegawaiJawatan,
            'pegawai_jajahan' => $pegawaiJajahan,
            'tarikh_kelulusan' => now(),
            'catatan_kelulusan' => $request->catatan_kelulusan,
        ]);

        return redirect()->route('eptr.pemindahan.show', $pemindahan->id)
            ->with('success', "Permohonan Pemindahan Ternakan {$pemindahan->no_rujukan} telah DILULUSKAN oleh Pegawai ({$pegawaiJajahan}).");
    }

    /**
     * Reject the permit.
     */
    public function tolak(Request $request, $id)
    {
        $pemindahan = PemindahanTernakan::findOrFail($id);

        $pemindahan->update([
            'status' => 'Ditolak',
            'pegawai_nama' => Auth::user()->name,
            'tarikh_kelulusan' => now(),
            'catatan_kelulusan' => $request->catatan_kelulusan ?? 'Permohonan ditolak.',
        ]);

        return redirect()->route('eptr.pemindahan.show', $pemindahan->id)
            ->with('error', "Permohonan Pemindahan Ternakan {$pemindahan->no_rujukan} telah DITOLAK.");
    }

    /**
     * Page 1: Cetak Surat Pengesahan Tarikh Suntikan FMD (Kn. 156)
     */
    public function cetakSuratFmd($id)
    {
        $user = Auth::user();
        if (!$user || !$user->isStaff()) {
            abort(403, 'Akses Ditolak: Hanya Pegawai Veterinar / Pentadbir yang dibenarkan mencetak dokumen permit pemindahan ternakan.');
        }

        $pemindahan = PemindahanTernakan::with('pemunya')->findOrFail($id);

        if ($pemindahan->status !== 'Diluluskan') {
            return back()->with('error', 'Akses Ditolak: Dokumen permit pemindahan hanya boleh dicetak selepas permohonan diluluskan oleh Pegawai.');
        }

        return view('eptr.pemindahan.cetak-surat-fmd', compact('pemindahan'));
    }

    /**
     * Page 2: Cetak Borang Permohonan Pemindahan Ternakan / Produk
     */
    public function cetakBorangPermohonan($id)
    {
        $user = Auth::user();
        if (!$user || !$user->isStaff()) {
            abort(403, 'Akses Ditolak: Hanya Pegawai Veterinar / Pentadbir yang dibenarkan mencetak dokumen permit pemindahan ternakan.');
        }

        $pemindahan = PemindahanTernakan::with('pemunya')->findOrFail($id);

        if ($pemindahan->status !== 'Diluluskan') {
            return back()->with('error', 'Akses Ditolak: Dokumen permit pemindahan hanya boleh dicetak selepas permohonan diluluskan oleh Pegawai.');
        }

        return view('eptr.pemindahan.cetak-borang-pemindahan', compact('pemindahan'));
    }

    /**
     * Page 3: Cetak Deklarasi Status Haiwan Ruminan (DVS/DSHR/0117/9/2021)
     */
    public function cetakDeklarasi($id)
    {
        $user = Auth::user();
        if (!$user || !$user->isStaff()) {
            abort(403, 'Akses Ditolak: Hanya Pegawai Veterinar / Pentadbir yang dibenarkan mencetak dokumen permit pemindahan ternakan.');
        }

        $pemindahan = PemindahanTernakan::with('pemunya')->findOrFail($id);

        if ($pemindahan->status !== 'Diluluskan') {
            return back()->with('error', 'Akses Ditolak: Dokumen permit pemindahan hanya boleh dicetak selepas permohonan diluluskan oleh Pegawai.');
        }

        return view('eptr.pemindahan.cetak-deklarasi', compact('pemindahan'));
    }

    /**
     * Page 4: Cetak Lampiran Senarai Pengenalan Ternakan (50 No Tag)
     */
    public function cetakLampiranTag($id)
    {
        $user = Auth::user();
        if (!$user || !$user->isStaff()) {
            abort(403, 'Akses Ditolak: Hanya Pegawai Veterinar / Pentadbir yang dibenarkan mencetak dokumen permit pemindahan ternakan.');
        }

        $pemindahan = PemindahanTernakan::with('pemunya')->findOrFail($id);

        if ($pemindahan->status !== 'Diluluskan') {
            return back()->with('error', 'Akses Ditolak: Dokumen permit pemindahan hanya boleh dicetak selepas permohonan diluluskan oleh Pegawai.');
        }

        return view('eptr.pemindahan.cetak-lampiran-tag', compact('pemindahan'));
    }

    /**
     * Cetak 1 Set Lengkap (4 Halaman Penuh)
     */
    public function cetakSetLengkap($id)
    {
        $user = Auth::user();
        if (!$user || !$user->isStaff()) {
            abort(403, 'Akses Ditolak: Hanya Pegawai Veterinar / Pentadbir yang dibenarkan mencetak dokumen permit pemindahan ternakan.');
        }

        $pemindahan = PemindahanTernakan::with('pemunya')->findOrFail($id);

        if ($pemindahan->status !== 'Diluluskan') {
            return back()->with('error', 'Akses Ditolak: Dokumen permit pemindahan hanya boleh dicetak selepas permohonan diluluskan oleh Pegawai.');
        }

        return view('eptr.pemindahan.cetak-set-lengkap', compact('pemindahan'));
    }
}
