<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pemunya;
use App\Models\Ternakan;
use App\Models\PawahPerjanjian;
use App\Models\PawahTernakan;
use App\Models\PawahRekodKelahiran;
use App\Models\PawahRekodKesihatan;
use App\Models\PawahPenyelesaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Closure;
use Carbon\Carbon;

class PawahController extends Controller implements HasMiddleware
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
                    if (!$user->canAccessPawah()) {
                        abort(403, 'Akses Ditolak: Peranan ' . ($user->role_label ?? $user->role) . ' tidak dibenarkan mengakses modul Program Pawah.');
                    }
                }
                return $next($request);
            }
        ];
    }

    // Senarai Perjanjian Pawah
    public function index(Request $request)
    {
        $user = Auth::user();

        // Admin Pejabat & Admin Kenderaan hanya dibenarkan menguruskan Pentadbiran
        if (in_array($user->role, ['admin_pejabat', 'admin_kenderaan'])) {
            return redirect()->route($user->role === 'admin_kenderaan' ? 'kenderaan.index' : 'inventori.index')->with('error', 'Akses Ditolak: Peranan ' . $user->role_label . ' dikhaskan untuk Pengurusan Pentadbiran sahaja.');
        }

        $query = PawahPerjanjian::with('peserta', 'ternakanList', 'rekodKelahiran', 'penyelesaian');

        if (!$user->isStaff()) {
            $query->where('user_id', $user->id);
        }

        // Carian No. Perjanjian
        if ($request->filled('no_perjanjian')) {
            $query->where('no_perjanjian', 'like', '%' . trim($request->no_perjanjian) . '%');
        }

        // Carian Kata Kunci (No. Perjanjian / No. Kad Pengenalan / Nama Peserta / Nama Program / Jenis Pawah)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $searchClean = str_replace(['-', ' '], '', $search);
            $query->where(function ($q) use ($search, $searchClean) {
                $q->where('no_perjanjian', 'like', "%{$search}%")
                  ->orWhere('nama_program', 'like', "%{$search}%")
                  ->orWhere('jenis_pawah', 'like', "%{$search}%")
                  ->orWhereHas('peserta', function ($qp) use ($search, $searchClean) {
                      $qp->where('name', 'like', "%{$search}%")
                         ->orWhere('ic_number', 'like', "%{$search}%")
                         ->orWhere('ic_number', 'like', "%{$searchClean}%");
                  });
            });
        }

        // Carian khusus mengikut No. Kad Pengenalan
        if ($request->filled('no_kp')) {
            $noKp = trim($request->no_kp);
            $noKpClean = str_replace(['-', ' '], '', $noKp);
            $query->whereHas('peserta', function ($qp) use ($noKp, $noKpClean) {
                $qp->where('ic_number', 'like', "%{$noKp}%")
                   ->orWhere('ic_number', 'like', "%{$noKpClean}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jajahan')) {
            $query->where('jajahan', $request->jajahan);
        }

        if ($request->filled('jenis_pawah')) {
            $query->where('jenis_pawah', $request->jenis_pawah);
        }

        $perjanjianList = $query->latest()->paginate(15)->withQueryString();
        $totalActive = PawahPerjanjian::where('status', 'Aktif')->count();
        $totalPending = PawahPerjanjian::where('status', 'Menunggu Kelulusan')->count();
        $totalCompleted = PawahPerjanjian::where('status', 'Selesai')->count();
        $totalCalvesBorn = PawahRekodKelahiran::count();
        $totalCalvesReturned = PawahRekodKelahiran::where('status_anak', 'Telah Dipulangkan')->count();

        $jajahanList = [
            'Kota Bharu', 'Pasir Mas', 'Tumpat', 'Bachok', 'Pasir Puteh',
            'Machang', 'Tanah Merah', 'Jeli', 'Kuala Krai', 'Gua Musang'
        ];

        $jenisPawahList = [
            'Lembu Hibrid',
            'Kambing Tenusu',
            'Rusa',
        ];

        return view('pawah.index', compact(
            'perjanjianList',
            'totalActive',
            'totalPending',
            'totalCompleted',
            'totalCalvesBorn',
            'totalCalvesReturned',
            'jajahanList',
            'jenisPawahList'
        ));
    }

    // Borang Permohonan (Orang Awam) / Pendaftaran Kontrak (Pegawai)
    public function create()
    {
        $user = Auth::user();
        if ($user && $user->isPurePengarah()) {
            abort(403, 'Akses Ditolak: Pengarah Perkhidmatan Veterinar Negeri tidak dibenarkan membuat Pendaftaran Surat Perjanjian Lembu Pawah.');
        }

        $pesertaList = User::whereIn('role', ['penternak', 'usahawan', 'orang_awam'])->orderBy('name')->get();
        
        // Pilihan Ternakan Lembu daripada EPTR yang didaftarkan (Ternakan aktif baka betina/induk)
        $availableTernakan = Ternakan::where('status', 'Aktif')
            ->where('jantina', 'Betina')
            ->with('pemunya')
            ->get();

        $jajahanList = [
            'Kota Bharu', 'Pasir Mas', 'Tumpat', 'Bachok', 'Pasir Puteh',
            'Machang', 'Tanah Merah', 'Jeli', 'Kuala Krai', 'Gua Musang'
        ];

        $jenisPawahList = [
            'Lembu Hibrid',
            'Kambing Tenusu',
            'Rusa',
        ];

        // Semak rekod pendaftaran EPTR pemohon (sama ada mempunyai profil Pemunya atau rekod Ternakan)
        $pemunyaUser = Pemunya::where('user_id', $user->id)
            ->orWhere(function ($q) use ($user) {
                if (!empty($user->ic_number)) {
                    $q->where('no_kp', $user->ic_number);
                }
            })
            ->first();

        $ternakanEptrList = collect();
        $ternakanEptrCount = 0;
        $jenisTernakanEptrList = [];

        if ($pemunyaUser) {
            $ternakanEptrList = Ternakan::where('pemunya_id', $pemunyaUser->id)->get();
            $ternakanEptrCount = $ternakanEptrList->count();
            $jenisTernakanEptrList = $ternakanEptrList->pluck('jenis_ternakan')->unique()->filter()->values()->all();
            if (empty($jenisTernakanEptrList) && $ternakanEptrCount > 0) {
                $jenisTernakanEptrList = ['Lembu'];
            }
        }

        $isEptrRegistered = ($pemunyaUser !== null) || ($ternakanEptrCount > 0);

        return view('pawah.create', compact(
            'pesertaList',
            'availableTernakan',
            'jajahanList',
            'jenisPawahList',
            'user',
            'isEptrRegistered',
            'ternakanEptrCount',
            'jenisTernakanEptrList',
            'ternakanEptrList'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->isPurePengarah()) {
            abort(403, 'Akses Ditolak: Pengarah Perkhidmatan Veterinar Negeri tidak dibenarkan membuat Pendaftaran Surat Perjanjian Lembu Pawah.');
        }

        // 1. Permohonan Program Pawah oleh Orang Awam / Penternak / Usahawan
        if (!$user->isStaff()) {
            $validated = $request->validate([
                'jenis_pawah' => 'required|in:Lembu Hibrid,Kambing Tenusu,Rusa',
                'ada_pengalaman' => 'nullable|string|in:ya,tidak',
                'pengalaman_menternak' => 'nullable|string',
                'jenis_ternakan_sedia_ada' => 'nullable|string|max:100',
                'bilangan_ternakan_sedia_ada' => 'nullable|integer|min:0',
                'jajahan' => 'required|string',
                'keluasan_padang_ragut' => 'nullable|string',
                'jenis_kandang' => 'nullable|string',
                'sumber_makanan' => 'nullable|string',
                'catatan' => 'nullable|string',
            ]);

            $pemunyaUser = Pemunya::where('user_id', $user->id)
                ->orWhere(function ($q) use ($user) {
                    if (!empty($user->ic_number)) {
                        $q->where('no_kp', $user->ic_number);
                    }
                })
                ->first();

            $ternakanEptrCount = $pemunyaUser ? Ternakan::where('pemunya_id', $pemunyaUser->id)->count() : 0;
            $jenisTernakanEptr = $pemunyaUser ? Ternakan::where('pemunya_id', $pemunyaUser->id)->pluck('jenis_ternakan')->unique()->filter()->values()->all() : [];
            $isEptrRegistered = ($pemunyaUser !== null) || ($ternakanEptrCount > 0);

            $adaPengalaman = $request->input('ada_pengalaman');
            if ($adaPengalaman === null) {
                if ($isEptrRegistered) {
                    $adaPengalaman = 'ya';
                } else {
                    $adaPengalaman = (!empty($validated['pengalaman_menternak']) && $validated['pengalaman_menternak'] !== 'Belum Pernah (Penternak Baru)') ? 'ya' : 'tidak';
                }
            }
            $pengalaman = ($adaPengalaman === 'ya') 
                ? ($validated['pengalaman_menternak'] ?? ($isEptrRegistered ? '3 - 5 Tahun' : '1 - 3 Tahun')) 
                : 'Belum Pernah (Penternak Baru)';
            $jenisKandang = ($adaPengalaman === 'ya') 
                ? ($validated['jenis_kandang'] ?? 'Kandang Berbumbung Penuh') 
                : 'Tiada / Sedang Dalam Pembinaan';

            $bilanganTernakan = ($adaPengalaman === 'ya') 
                ? ($validated['bilangan_ternakan_sedia_ada'] ?? ($ternakanEptrCount > 0 ? $ternakanEptrCount : 0)) 
                : 0;

            $defaultSpecies = !empty($jenisTernakanEptr) ? implode(', ', $jenisTernakanEptr) : ($ternakanEptrCount > 0 ? 'Lembu' : null);

            $jenisTernakan = ($adaPengalaman === 'ya')
                ? ($validated['jenis_ternakan_sedia_ada'] ?? $defaultSpecies)
                : null;

            $tarikhMula = Carbon::now();
            $tarikhTamat = (clone $tarikhMula)->addYears(3);
            $noPerjanjian = 'PW-MOHON-' . date('Ymd') . '-' . rand(100, 999);

            $catatanPenuh = "Jenis Pawah Dimohon: " . $validated['jenis_pawah']
                . (!empty($jenisTernakan) ? " | Jenis Ternakan Sekarang: " . $jenisTernakan : "")
                . " | Ternakan Sedia Ada: " . $bilanganTernakan . " Ekor"
                . " | Pengalaman Menternak: " . $pengalaman
                . ($isEptrRegistered ? " (Berdaftar EPTR)" : "")
                . " | Keluasan Padang Ragut: " . ($validated['keluasan_padang_ragut'] ?? 'Tiada / Berpagar')
                . " | Jenis Kandang: " . $jenisKandang
                . " | Sumber Makanan: " . ($validated['sumber_makanan'] ?? 'Rumput / Dedak')
                . (!empty($validated['catatan']) ? " | Catatan Tambahan: " . $validated['catatan'] : "");

            $perjanjian = PawahPerjanjian::create([
                'user_id' => $user->id,
                'no_perjanjian' => $noPerjanjian,
                'nama_program' => 'Program Pawah ' . $validated['jenis_pawah'] . ' Negeri Kelantan (Permohonan Awam)',
                'jenis_pawah' => $validated['jenis_pawah'],
                'jenis_ternakan_sedia_ada' => $jenisTernakan,
                'bilangan_ternakan_sedia_ada' => $bilanganTernakan,
                'tarikh_mula' => $tarikhMula->toDateString(),
                'tarikh_tamat' => $tarikhTamat->toDateString(),
                'tempoh_tahun' => 3,
                'bilangan_induk' => 1,
                'syarat_pemulangan' => 'Memulangkan 1 (satu) ekor anak betina pertama berumur sekurang-kurangnya 12 bulan kepada Jabatan Perkhidmatan Veterinar Negeri Kelantan.',
                'jajahan' => $validated['jajahan'],
                'status' => 'Menunggu Kelulusan',
                'pegawai_penyelia' => 'Pegawai Pawah Jajahan ' . $validated['jajahan'],
                'catatan' => $catatanPenuh,
            ]);

            return redirect()->route('pawah.show', $perjanjian->id)->with('success', 'Permohonan Program Pawah berjaya dihantar! Permohonan anda kini sedang dalam semakan Pegawai JPVNK.');
        }

        // 2. Pendaftaran Perjanjian Lembu Pawah oleh Pegawai Pawah / Admin
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'nama_program' => 'required|string|max:255',
            'jenis_pawah' => 'nullable|string',
            'tarikh_mula' => 'required|date',
            'tempoh_tahun' => 'required|integer|min:1|max:10',
            'jajahan' => 'required|string',
            'syarat_pemulangan' => 'required|string',
            'ternakan_ids' => 'required|array|min:1',
            'ternakan_ids.*' => 'exists:ternakan,id',
            'pegawai_penyelia' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $tarikhMula = Carbon::parse($validated['tarikh_mula']);
        $tarikhTamat = (clone $tarikhMula)->addYears((int)$validated['tempoh_tahun']);
        $noPerjanjian = 'JPVNK/PAWAH/' . strtoupper(substr($validated['jajahan'], 0, 2)) . '/' . date('Y') . '/' . rand(100, 999);

        $perjanjian = PawahPerjanjian::create([
            'user_id' => $validated['user_id'],
            'no_perjanjian' => $noPerjanjian,
            'nama_program' => $validated['nama_program'],
            'jenis_pawah' => $validated['jenis_pawah'] ?? 'Lembu Hibrid',
            'bilangan_ternakan_sedia_ada' => 0,
            'tarikh_mula' => $tarikhMula->toDateString(),
            'tarikh_tamat' => $tarikhTamat->toDateString(),
            'tempoh_tahun' => $validated['tempoh_tahun'],
            'bilangan_induk' => count($validated['ternakan_ids']),
            'syarat_pemulangan' => $validated['syarat_pemulangan'],
            'jajahan' => $validated['jajahan'],
            'status' => 'Aktif',
            'pegawai_penyelia' => $validated['pegawai_penyelia'] ?? Auth::user()->name,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        $pemunyaJpvnk = $this->getJpvnkPemunya();

        // Pautkan lembu EPTR ke dalam Program Pawah dan kemaskini status EPTR
        foreach ($validated['ternakan_ids'] as $ternakanId) {
            PawahTernakan::create([
                'pawah_perjanjian_id' => $perjanjian->id,
                'ternakan_id' => $ternakanId,
                'status_induk' => 'Aktif',
                'tarikh_serahan' => $tarikhMula->toDateString(),
                'catatan' => 'Induk diserahkan di bawah perjanjian pawah ' . $noPerjanjian,
            ]);

            $ternakan = Ternakan::find($ternakanId);
            if ($ternakan) {
                $ternakan->pemunya_id = $pemunyaJpvnk->id;
                $ternakan->program = $validated['nama_program'];
                $ternakan->status = 'Pawah';
                $ternakan->save();
            }
        }

        return redirect()->route('pawah.show', $perjanjian->id)->with('success', 'Surat Perjanjian Lembu Pawah berjaya didaftarkan dan dipautkan dengan lembu EPTR!');
    }

    public function show($id)
    {
        $perjanjian = PawahPerjanjian::with([
            'peserta',
            'ternakanList.pemunya',
            'rekodKelahiran.induk',
            'rekodKesihatan.ternakan',
            'penyelesaian'
        ])->findOrFail($id);

        $ternakanPemohonEptr = $perjanjian->ternakan_pemohon_eptr;

        $availableTernakan = [];
        if (Auth::user()->isStaff()) {
            $availableTernakan = Ternakan::where('status', 'Aktif')
                ->where('jantina', 'Betina')
                ->with('pemunya')
                ->get();
        }

        return view('pawah.show', compact('perjanjian', 'availableTernakan', 'ternakanPemohonEptr'));
    }

    // Pautkan Ternakan EPTR ke dalam Surat Perjanjian Pawah (Pegawai)
    public function pautkanTernakan(Request $request, $id)
    {
        if (!Auth::user()->isStaff()) {
            abort(403, 'Akses Ditolak: Hanya Pegawai Veterinar / Admin Pawah dibenarkan memautkan ternakan.');
        }

        $perjanjian = PawahPerjanjian::findOrFail($id);

        $validated = $request->validate([
            'ternakan_ids' => 'required|array|min:1',
            'ternakan_ids.*' => 'exists:ternakan,id',
        ]);

        $pemunyaJpvnk = $this->getJpvnkPemunya();

        foreach ($validated['ternakan_ids'] as $ternakanId) {
            $exists = PawahTernakan::where('pawah_perjanjian_id', $perjanjian->id)
                ->where('ternakan_id', $ternakanId)
                ->exists();

            if (!$exists) {
                PawahTernakan::create([
                    'pawah_perjanjian_id' => $perjanjian->id,
                    'ternakan_id' => $ternakanId,
                    'status_induk' => 'Aktif',
                    'tarikh_serahan' => now()->toDateString(),
                    'catatan' => 'Induk dipautkan ke perjanjian pawah ' . $perjanjian->no_perjanjian,
                ]);

                $ternakan = Ternakan::find($ternakanId);
                if ($ternakan) {
                    $ternakan->pemunya_id = $pemunyaJpvnk->id;
                    $ternakan->program = $perjanjian->nama_program;
                    $ternakan->status = 'Pawah';
                    $ternakan->save();
                }
            }
        }

        $perjanjian->bilangan_induk = $perjanjian->ternakanList()->count();
        $perjanjian->save();

        return redirect()->route('pawah.show', $perjanjian->id)->with('success', 'Lembu induk EPTR berjaya dipautkan ke dalam surat perjanjian pawah!');
    }

    // Batalkan Pautan Ternakan EPTR dari Perjanjian
    public function padamPautanTernakan(Request $request, $id, $ternakanId)
    {
        if (!Auth::user()->isStaff()) {
            abort(403, 'Akses Ditolak: Hanya Pegawai Veterinar / Admin Pawah dibenarkan membatalkan pautan ternakan.');
        }

        $perjanjian = PawahPerjanjian::findOrFail($id);

        PawahTernakan::where('pawah_perjanjian_id', $perjanjian->id)
            ->where('ternakan_id', $ternakanId)
            ->delete();

        $ternakan = Ternakan::find($ternakanId);
        if ($ternakan) {
            $ternakan->status = 'Aktif';
            $ternakan->program = null;
            if ($perjanjian->peserta) {
                $pemunyaPeserta = Pemunya::where('user_id', $perjanjian->peserta->id)
                    ->orWhere('no_kp', $perjanjian->peserta->ic_number)
                    ->first();
                if ($pemunyaPeserta) {
                    $ternakan->pemunya_id = $pemunyaPeserta->id;
                }
            }
            $ternakan->save();
        }

        $perjanjian->bilangan_induk = $perjanjian->ternakanList()->count();
        $perjanjian->save();

        return redirect()->route('pawah.show', $perjanjian->id)->with('success', 'Pautan lembu induk telah dikeluarkan daripada surat perjanjian.');
    }

    // Kelulusan Permohonan Pawah oleh Pegawai
    public function luluskanPermohonan(Request $request, $id)
    {
        if (!Auth::user()->isStaff()) {
            abort(403, 'Akses Ditolak: Hanya Pegawai Veterinar / Admin Pawah dibenarkan meluluskan permohonan.');
        }

        $perjanjian = PawahPerjanjian::findOrFail($id);

        $validated = $request->validate([
            'ternakan_ids' => 'required|array|min:1',
            'ternakan_ids.*' => 'exists:ternakan,id',
            'tarikh_mula' => 'required|date',
            'tempoh_tahun' => 'required|integer|min:1|max:10',
            'syarat_pemulangan' => 'nullable|string',
            'pegawai_penyelia' => 'nullable|string',
            'catatan_kelulusan' => 'nullable|string',
        ]);

        $tarikhMula = Carbon::parse($validated['tarikh_mula']);
        $tarikhTamat = (clone $tarikhMula)->addYears((int)$validated['tempoh_tahun']);
        $noPerjanjian = 'JPVNK/PAWAH/' . strtoupper(substr($perjanjian->jajahan, 0, 2)) . '/' . date('Y') . '/' . rand(100, 999);

        $perjanjian->no_perjanjian = $noPerjanjian;
        $perjanjian->tarikh_mula = $tarikhMula->toDateString();
        $perjanjian->tarikh_tamat = $tarikhTamat->toDateString();
        $perjanjian->tempoh_tahun = $validated['tempoh_tahun'];
        $perjanjian->bilangan_induk = count($validated['ternakan_ids']);
        if (!empty($validated['syarat_pemulangan'])) {
            $perjanjian->syarat_pemulangan = $validated['syarat_pemulangan'];
        }
        $perjanjian->pegawai_penyelia = $validated['pegawai_penyelia'] ?? Auth::user()->name;
        $perjanjian->status = 'Aktif';
        if (!empty($validated['catatan_kelulusan'])) {
            $perjanjian->catatan = ($perjanjian->catatan ? $perjanjian->catatan . " | " : "") . "Kelulusan: " . $validated['catatan_kelulusan'];
        }
        $perjanjian->save();

        $pemunyaJpvnk = $this->getJpvnkPemunya();

        foreach ($validated['ternakan_ids'] as $ternakanId) {
            PawahTernakan::create([
                'pawah_perjanjian_id' => $perjanjian->id,
                'ternakan_id' => $ternakanId,
                'status_induk' => 'Aktif',
                'tarikh_serahan' => $tarikhMula->toDateString(),
                'catatan' => 'Induk diserahkan di bawah perjanjian pawah ' . $noPerjanjian,
            ]);

            $ternakan = Ternakan::find($ternakanId);
            if ($ternakan) {
                $ternakan->pemunya_id = $pemunyaJpvnk->id;
                $ternakan->program = $perjanjian->nama_program;
                $ternakan->status = 'Pawah';
                $ternakan->save();
            }
        }

        return redirect()->route('pawah.show', $perjanjian->id)->with('success', 'Permohonan Pawah telah diluluskan dan lembu induk EPTR berjaya dipautkan!');
    }

    // Penolakan Permohonan Pawah
    public function tolakPermohonan(Request $request, $id)
    {
        if (!Auth::user()->isStaff()) {
            abort(403, 'Akses Ditolak: Hanya Pegawai Veterinar / Admin Pawah dibenarkan menolak permohonan.');
        }

        $perjanjian = PawahPerjanjian::findOrFail($id);

        $validated = $request->validate([
            'sebab_tolak' => 'required|string',
        ]);

        $perjanjian->status = 'Ditolak';
        $perjanjian->catatan = ($perjanjian->catatan ? $perjanjian->catatan . " | " : "") . "Sebab Penolakan: " . $validated['sebab_tolak'] . " (Pegawai: " . Auth::user()->name . " pada " . date('d/m/Y') . ")";
        $perjanjian->save();

        return redirect()->route('pawah.show', $perjanjian->id)->with('success', 'Permohonan Program Pawah telah ditolak.');
    }

    // Cetak Surat Perjanjian Lembu Pawah
    public function cetakPerjanjian($id)
    {
        $perjanjian = PawahPerjanjian::with([
            'peserta',
            'ternakanList.pemunya',
            'rekodKelahiran',
            'rekodKesihatan'
        ])->findOrFail($id);

        return view('pawah.cetak-perjanjian', compact('perjanjian'));
    }

    // Tambah Rekod Kelahiran Anak Pawah
    public function storeKelahiran(Request $request, $id)
    {
        $perjanjian = PawahPerjanjian::findOrFail($id);

        $validated = $request->validate([
            'ternakan_induk_id' => 'required|exists:ternakan,id',
            'no_tag_anak' => 'nullable|string',
            'jantina_anak' => 'required|in:Jantan,Betina',
            'tarikh_kelahiran' => 'required|date',
            'berat_lahir_kg' => 'nullable|numeric',
            'baka_bapa' => 'nullable|string',
            'warna' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $noTagAnak = !empty($validated['no_tag_anak']) ? $validated['no_tag_anak'] : ('ANAK-PW-' . date('Ymd') . '-' . rand(10, 99));

        PawahRekodKelahiran::create([
            'pawah_perjanjian_id' => $perjanjian->id,
            'ternakan_induk_id' => $validated['ternakan_induk_id'],
            'no_tag_anak' => $noTagAnak,
            'jantina_anak' => $validated['jantina_anak'],
            'tarikh_kelahiran' => $validated['tarikh_kelahiran'],
            'berat_lahir_kg' => $validated['berat_lahir_kg'] ?? null,
            'baka_bapa' => $validated['baka_bapa'] ?? null,
            'warna' => $validated['warna'] ?? null,
            'status_anak' => 'Dalam Peliharaan',
            'catatan' => $validated['catatan'] ?? null,
        ]);

        return back()->with('success', 'Rekod kelahiran anak pawah berjaya didaftarkan.');
    }

    // Tambah Rekod Kesihatan & Pemantauan
    public function storeKesihatan(Request $request, $id)
    {
        $perjanjian = PawahPerjanjian::findOrFail($id);

        $validated = $request->validate([
            'ternakan_id' => 'required|exists:ternakan,id',
            'tarikh_lawatan' => 'required|date',
            'status_fizikal' => 'required|string',
            'status_bunting' => 'required|boolean',
            'diagnosis' => 'nullable|string',
            'rawatan_diberikan' => 'nullable|string',
            'syor_tindakan' => 'nullable|string',
        ]);

        PawahRekodKesihatan::create([
            'pawah_perjanjian_id' => $perjanjian->id,
            'ternakan_id' => $validated['ternakan_id'],
            'tarikh_lawatan' => $validated['tarikh_lawatan'],
            'status_fizikal' => $validated['status_fizikal'],
            'status_bunting' => $validated['status_bunting'],
            'diagnosis' => $validated['diagnosis'] ?? null,
            'rawatan_diberikan' => $validated['rawatan_diberikan'] ?? null,
            'pegawai_pemeriksa' => Auth::user()->name,
            'syor_tindakan' => $validated['syor_tindakan'] ?? null,
        ]);

        return back()->with('success', 'Laporan pemantauan kesihatan veterinar berjaya disimpan.');
    }

    // Penyelesaian Program Pawah
    public function storePenyelesaian(Request $request, $id)
    {
        $perjanjian = PawahPerjanjian::findOrFail($id);

        $validated = $request->validate([
            'tarikh_penyelesaian' => 'required|date',
            'bilangan_anak_dipulangkan' => 'required|integer|min:0',
            'status_penyelesaian' => 'required|string',
            'jumlah_bayaran_tebus_guna' => 'nullable|numeric',
            'perakuan' => 'required|string',
            'resit_pembayaran' => 'required|file|mimes:jpeg,jpg,png,pdf|max:5120',
        ], [
            'resit_pembayaran.required' => 'Pengesahan penyelesaian kontrak pawah wajib disertakan dengan salinan resit bayaran.',
            'resit_pembayaran.file' => 'Fail resit bayaran mestilah fail yang sah.',
            'resit_pembayaran.mimes' => 'Resit bayaran mestilah dalam format JPEG, JPG, PNG, atau PDF.',
            'resit_pembayaran.max' => 'Saiz fail resit bayaran tidak boleh melebihi 5MB.',
        ]);

        $resitPath = null;
        if ($request->hasFile('resit_pembayaran')) {
            $resitPath = $this->uploadFileSafely($request->file('resit_pembayaran'), 'resit_pawah_penyelesaian');
        }

        PawahPenyelesaian::create([
            'pawah_perjanjian_id' => $perjanjian->id,
            'tarikh_penyelesaian' => $validated['tarikh_penyelesaian'],
            'bilangan_anak_dipulangkan' => $validated['bilangan_anak_dipulangkan'],
            'status_penyelesaian' => $validated['status_penyelesaian'],
            'jumlah_bayaran_tebus_guna' => $validated['jumlah_bayaran_tebus_guna'] ?? 0,
            'resit_pembayaran' => $resitPath,
            'pegawai_pengesah' => Auth::user()->name,
            'perakuan' => $validated['perakuan'],
        ]);

        $perjanjian->status = 'Selesai';
        $perjanjian->save();

        // Kemaskini status ternakan EPTR kepada Aktif / Selesai Program dan serahkan hak milik mutlak kepada peserta
        $pesertaUser = $perjanjian->peserta;
        $pemunyaPeserta = null;
        if ($pesertaUser) {
            $pemunyaPeserta = Pemunya::firstOrCreate(
                ['user_id' => $pesertaUser->id],
                [
                    'nama' => $pesertaUser->name,
                    'no_kp' => $pesertaUser->ic_number ?? 'TIADA',
                    'no_telefon' => $pesertaUser->phone ?? 'TIADA',
                    'alamat' => $pesertaUser->address ?? 'Negeri Kelantan',
                    'jajahan' => $perjanjian->jajahan,
                    'daerah' => 'Bandar',
                    'poskod' => '15000',
                    'status' => 'Aktif',
                ]
            );
        }

        foreach ($perjanjian->ternakanList as $ternakan) {
            if ($pemunyaPeserta) {
                $ternakan->pemunya_id = $pemunyaPeserta->id;
            }
            $ternakan->status = 'Aktif';
            $ternakan->catatan = ($ternakan->catatan ? $ternakan->catatan . ' | ' : '') . 'Selesai Program Pawah pada ' . $validated['tarikh_penyelesaian'] . ($pesertaUser ? ' (Hak Milik Mutlak Peserta: ' . $pesertaUser->name . ')' : '');
            $ternakan->save();
        }

        return back()->with('success', 'Program Pawah telah disahkan selesai dengan jayanya.');
    }

    /**
     * Helper muat naik fail secara selamat merentasi pelayan dan persekitaran Windows/Linux
     */
    protected function uploadFileSafely($file, $folder = 'resit_pawah_penyelesaian')
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

    /**
     * Dapatkan atau cipta rekod Pemunya bagi Jabatan Perkhidmatan Veterinar Negeri Kelantan
     */
    protected function getJpvnkPemunya(): Pemunya
    {
        return Pemunya::firstOrCreate(
            ['nama' => 'Jabatan Perkhidmatan Veterinar Negeri Kelantan'],
            [
                'user_id' => Auth::id() ?? 1,
                'no_kp' => 'GOV-JPVNK-01',
                'no_telefon' => '09-7445566',
                'alamat' => 'Ibu Pejabat Perkhidmatan Veterinar Negeri Kelantan, Jalan Kubang Kachang',
                'jajahan' => 'Kota Bharu',
                'daerah' => 'Bandar',
                'poskod' => '15100',
                'status' => 'Aktif',
            ]
        );
    }

    /**
     * Padam Surat Perjanjian Pawah (Super Admin Sahaja)
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Super Admin dibenarkan memadam surat perjanjian pawah.');
        }

        $perjanjian = PawahPerjanjian::findOrFail($id);
        $noPerjanjian = $perjanjian->no_perjanjian ?? ('ID: ' . $perjanjian->id);

        // Delete associated records
        PawahTernakan::where('perjanjian_id', $perjanjian->id)->delete();
        PawahRekodKelahiran::where('perjanjian_id', $perjanjian->id)->delete();
        PawahRekodKesihatan::where('perjanjian_id', $perjanjian->id)->delete();
        PawahPenyelesaian::where('perjanjian_id', $perjanjian->id)->delete();
        $perjanjian->delete();

        return redirect()->route('pawah.index')->with('success', "Surat Perjanjian Pawah {$noPerjanjian} berjaya dipadam dari sistem.");
    }
}
