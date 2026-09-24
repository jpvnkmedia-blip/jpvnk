<?php

namespace App\Http\Controllers;

use App\Models\ActionList;
use App\Models\KlinikTemujanji;
use App\Models\KlinikUbat;
use App\Models\InventoriItem;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ActionListController extends Controller
{
    /**
     * Papar senarai Borang Action List (PK-RK-61)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->canManageActionList()) {
            abort(403, 'Akses Ditolak: Anda tidak mempunyai kebenaran untuk mengakses modul Action List Jajahan.');
        }

        $jajahanList = array_keys(config('kelantan.jajahan', []));
        if (empty($jajahanList)) {
            $jajahanList = ['Pasir Puteh', 'Kota Bharu', 'Pasir Mas', 'Tumpat', 'Bachok', 'Machang', 'Tanah Merah', 'Kuala Krai', 'Gua Musang', 'Jeli'];
        }

        $selectedJajahan = $request->input('jajahan');
        if (!$selectedJajahan && !$user->isSuperAdmin() && !$user->isPengarah() && !empty($user->jajahan)) {
            $selectedJajahan = $user->jajahan;
        }

        $query = ActionList::with(['user', 'pegawai', 'temujanji']);

        if ($selectedJajahan && $selectedJajahan !== 'Semua') {
            $query->where('jajahan', $selectedJajahan);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('no_bil', 'like', "%{$search}%")
                  ->orWhere('nama_pelanggan', 'like', "%{$search}%")
                  ->orWhere('no_kp', 'like', "%{$search}%")
                  ->orWhere('telefon', 'like', "%{$search}%")
                  ->orWhere('no_rujukan', 'like', "%{$search}%")
                  ->orWhere('no_resit', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori_pelanggan', $request->input('kategori'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('tarikh_mula')) {
            $query->whereDate('tarikh', '>=', $request->input('tarikh_mula'));
        }

        if ($request->filled('tarikh_akhir')) {
            $query->whereDate('tarikh', '<=', $request->input('tarikh_akhir'));
        }

        $actionLists = $query->latest('tarikh')->latest('id')->paginate(15)->withQueryString();

        // Statistik
        $statsQuery = ActionList::query();
        if ($selectedJajahan && $selectedJajahan !== 'Semua') {
            $statsQuery->where('jajahan', $selectedJajahan);
        }

        $totalRecords = (clone $statsQuery)->count();
        $totalKutipan = (clone $statsQuery)->sum('bayaran');
        
        $allRecords = (clone $statsQuery)->get();
        $rawatanLapanganCount = 0;
        $rawatanKlinikCount = 0;
        $pemantauanPawahCount = 0;
        $pemantauanProjekCount = 0;

        foreach ($allRecords as $rec) {
            $services = is_array($rec->perkhidmatan_diberi) ? $rec->perkhidmatan_diberi : [];
            if (!empty($services['rawatan_lapangan'])) $rawatanLapanganCount++;
            if (!empty($services['rawatan_klinik'])) $rawatanKlinikCount++;
            if (!empty($services['pemantauan_pawah'])) $pemantauanPawahCount++;
            if (!empty($services['pemantauan_projek']) || !empty($services['pemantauan_trust'])) $pemantauanProjekCount++;
        }

        return view('action_list.index', compact(
            'actionLists',
            'jajahanList',
            'selectedJajahan',
            'totalRecords',
            'totalKutipan',
            'rawatanLapanganCount',
            'rawatanKlinikCount',
            'pemantauanPawahCount',
            'pemantauanProjekCount'
        ));
    }

    /**
     * Papar borang pengisian Action List baharu (PK-RK-61)
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->canManageActionList()) {
            abort(403, 'Akses Ditolak: Anda tidak mempunyai kebenaran untuk mengisi Borang Action List.');
        }

        $defaultJajahan = $user->jajahan ?: 'Pasir Puteh';
        $jajahanList = array_keys(config('kelantan.jajahan', []));
        if (empty($jajahanList)) {
            $jajahanList = ['Pasir Puteh', 'Kota Bharu', 'Pasir Mas', 'Tumpat', 'Bachok', 'Machang', 'Tanah Merah', 'Kuala Krai', 'Gua Musang', 'Jeli'];
        }

        $proposedNoBil = ActionList::generateNoBil($defaultJajahan);

        // Pre-fill data from Temujanji Klinik if passed
        $temujanji = null;
        if ($request->filled('temujanji_id')) {
            $temujanji = KlinikTemujanji::with('user')->find($request->input('temujanji_id'));
        }

        // Pre-fill data from Penternak/User if passed
        $prefillUser = null;
        if ($request->filled('user_id')) {
            $prefillUser = User::find($request->input('user_id'));
        } elseif ($temujanji && $temujanji->user) {
            $prefillUser = $temujanji->user;
        }

        // Senarai ubat sedia ada untuk autolengkap
        $senaraiUbat = [];
        if (class_exists(KlinikUbat::class)) {
            $senaraiUbat = KlinikUbat::pluck('nama_ubat')->toArray();
        }
        if (empty($senaraiUbat) && class_exists(InventoriItem::class)) {
            $senaraiUbat = InventoriItem::where('kategori', 'like', '%ubat%')
                ->orWhere('kategori', 'like', '%vaksin%')
                ->pluck('nama_item')
                ->toArray();
        }

        return view('action_list.create', compact(
            'defaultJajahan',
            'jajahanList',
            'proposedNoBil',
            'temujanji',
            'prefillUser',
            'senaraiUbat'
        ));
    }

    /**
     * Simpan rekod Borang Action List
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->canManageActionList()) {
            abort(403, 'Akses Ditolak.');
        }

        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'jajahan' => 'required|string|max:100',
            'tarikh' => 'required|date',
            'kategori_pelanggan' => 'required|in:Individu,Syarikat',
            'no_kp' => 'nullable|string|max:50',
            'telefon' => 'nullable|string|max:30',
            'alamat' => 'nullable|string|max:500',
            'mukim' => 'nullable|string|max:100',
            'poskod' => 'nullable|string|max:10',
            'daerah' => 'nullable|string|max:100',
            'bayaran' => 'nullable|numeric|min:0',
            'lampiran_peta' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $lampiranPetaPath = null;
        if ($request->hasFile('lampiran_peta')) {
            $lampiranPetaPath = $request->file('lampiran_peta')->store('action_list_maps', 'public');
        }

        $noBil = $request->input('no_bil') ?: ActionList::generateNoBil($request->input('jajahan'));

        // Format Perkhidmatan Diberi
        $perkhidmatanDiberi = [
            'rawatan_lapangan' => $request->boolean('rawatan_lapangan'),
            'rawatan_klinik' => $request->boolean('rawatan_klinik'),
            'pembedahan' => $request->boolean('pembedahan'),
            'keterangan_pembedahan' => $request->input('keterangan_pembedahan'),
            'pemantauan_pawah' => $request->boolean('pemantauan_pawah'),
            'pemantauan_projek' => $request->boolean('pemantauan_projek'),
            'keterangan_projek' => $request->input('keterangan_projek'),
            'pemantauan_trust' => $request->boolean('pemantauan_trust'),
            'lawatan_terancang' => $request->boolean('lawatan_terancang'),
            'perkhidmatan_lain' => $request->boolean('perkhidmatan_lain'),
            'keterangan_lain' => $request->input('keterangan_lain'),
        ];

        // Format Jenis Ternakan
        $jenisTernakan = $request->input('jenis_ternakan', []);
        if (!is_array($jenisTernakan)) {
            $jenisTernakan = [$jenisTernakan];
        }

        // Cari user_id sekiranya wujud dalam pangkalan data
        $targetUserId = $request->input('user_id');
        if (!$targetUserId && $request->filled('no_kp')) {
            $cleanIc = preg_replace('/[^0-9]/', '', $request->input('no_kp'));
            $matchedUser = User::where('ic_number', $cleanIc)->orWhere('ic_number', $request->input('no_kp'))->first();
            if ($matchedUser) {
                $targetUserId = $matchedUser->id;
            }
        }

        $actionList = ActionList::create([
            'kod_dokumen' => $request->input('kod_dokumen', 'PK-RK-61'),
            'no_bil' => $noBil,
            'jajahan' => $request->input('jajahan'),
            'tarikh' => $request->input('tarikh'),
            'masa_pendaftaran' => $request->input('masa_pendaftaran'),
            'kategori_pelanggan' => $request->input('kategori_pelanggan', 'Individu'),
            'nama_pelanggan' => $request->input('nama_pelanggan'),
            'no_kp' => $request->input('no_kp'),
            'alamat' => $request->input('alamat'),
            'mukim' => $request->input('mukim'),
            'poskod' => $request->input('poskod'),
            'daerah' => $request->input('daerah'),
            'telefon' => $request->input('telefon'),
            'no_rujukan' => $request->input('no_rujukan'),
            'user_id' => $targetUserId,

            'catatan_perkhidmatan_dipohon' => $request->input('catatan_perkhidmatan_dipohon'),

            'nama_pegawai' => $request->input('nama_pegawai', $user->name),
            'masa_pegawai' => $request->input('masa_pegawai'),
            'masa_temujanji_mula' => $request->input('masa_temujanji_mula'),
            'masa_temujanji_hingga' => $request->input('masa_temujanji_hingga'),
            'maklumat_pelanggan_berlainan' => $request->input('maklumat_pelanggan_berlainan'),
            'maklumat_tambahan' => $request->input('maklumat_tambahan'),
            'lampiran_peta' => $lampiranPetaPath,

            'perkhidmatan_diberi' => $perkhidmatanDiberi,
            'keterangan_pembedahan' => $request->input('keterangan_pembedahan'),
            'keterangan_projek' => $request->input('keterangan_projek'),
            'keterangan_lain' => $request->input('keterangan_lain'),

            'jenis_ternakan' => $jenisTernakan,
            'jenis_ternakan_lain' => $request->input('jenis_ternakan_lain'),
            'bil_ternakan' => $request->input('bil_ternakan'),
            'bil_yang_ada' => $request->input('bil_yang_ada'),

            'laporan' => $request->input('laporan'),
            'penggunaan_ubat' => $request->input('penggunaan_ubat'),

            'tandatangan_pelanggan_nama' => $request->input('tandatangan_pelanggan_nama', $request->input('nama_pelanggan')),
            'tandatangan_pelanggan_tarikh' => $request->input('tandatangan_pelanggan_tarikh', $request->input('tarikh')),
            'tandatangan_pelanggan_masa' => $request->input('tandatangan_pelanggan_masa'),
            'kepuasan_pelanggan' => $request->input('kepuasan_pelanggan', 'Puashati'),
            'cadangan_pelanggan' => $request->input('cadangan_pelanggan'),
            'bayaran' => $request->input('bayaran', 0.00),
            'no_resit' => $request->input('no_resit'),
            'pengesahan_ulasan_pegawai' => $request->input('pengesahan_ulasan_pegawai'),

            'status' => $request->input('status', 'Selesai'),
            'pegawai_id' => $user->id,
            'temujanji_id' => $request->input('temujanji_id'),
            'created_by' => $user->id,
        ]);

        // Hantar notifikasi sistem & emel jika ada user_id
        if ($targetUserId) {
            UserNotification::send(
                $targetUserId,
                'Borang Action List (PK-RK-61) Dikeluarkan',
                "Rekod perkhidmatan veterinar anda di Pejabat Perkhidmatan Veterinar Jajahan {$actionList->jajahan} ({$noBil}) telah selesai direkodkan.",
                'klinik',
                route('action-list.show', $actionList->id),
                'fa-solid fa-clipboard-check',
                'emerald'
            );
        }

        return redirect()->route('action-list.show', $actionList->id)
            ->with('success', "Borang Action List '{$noBil}' bagi pelanggan '{$actionList->nama_pelanggan}' berjaya disimpan.");
    }

    /**
     * Papar maklumat terperinci Borang Action List
     */
    public function show($id)
    {
        $user = Auth::user();
        if (!$user || !$user->canManageActionList()) {
            abort(403, 'Akses Ditolak.');
        }

        $actionList = ActionList::with(['user', 'pegawai', 'temujanji', 'creator'])->findOrFail($id);

        return view('action_list.show', compact('actionList'));
    }

    /**
     * Borang kemaskini Action List
     */
    public function edit($id)
    {
        $user = Auth::user();
        if (!$user || !$user->canManageActionList()) {
            abort(403, 'Akses Ditolak.');
        }

        $actionList = ActionList::with(['user', 'pegawai', 'temujanji'])->findOrFail($id);

        $jajahanList = array_keys(config('kelantan.jajahan', []));
        if (empty($jajahanList)) {
            $jajahanList = ['Pasir Puteh', 'Kota Bharu', 'Pasir Mas', 'Tumpat', 'Bachok', 'Machang', 'Tanah Merah', 'Kuala Krai', 'Gua Musang', 'Jeli'];
        }

        $senaraiUbat = [];
        if (class_exists(KlinikUbat::class)) {
            $senaraiUbat = KlinikUbat::pluck('nama_ubat')->toArray();
        }
        if (empty($senaraiUbat) && class_exists(InventoriItem::class)) {
            $senaraiUbat = InventoriItem::where('kategori', 'like', '%ubat%')
                ->orWhere('kategori', 'like', '%vaksin%')
                ->pluck('nama_item')
                ->toArray();
        }

        return view('action_list.edit', compact('actionList', 'jajahanList', 'senaraiUbat'));
    }

    /**
     * Simpan kemaskini Action List
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || !$user->canManageActionList()) {
            abort(403, 'Akses Ditolak.');
        }

        $actionList = ActionList::findOrFail($id);

        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'jajahan' => 'required|string|max:100',
            'tarikh' => 'required|date',
            'kategori_pelanggan' => 'required|in:Individu,Syarikat',
            'bayaran' => 'nullable|numeric|min:0',
            'lampiran_peta' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $lampiranPetaPath = $actionList->lampiran_peta;
        if ($request->hasFile('lampiran_peta')) {
            if ($lampiranPetaPath && Storage::disk('public')->exists($lampiranPetaPath)) {
                Storage::disk('public')->delete($lampiranPetaPath);
            }
            $lampiranPetaPath = $request->file('lampiran_peta')->store('action_list_maps', 'public');
        }

        $perkhidmatanDiberi = [
            'rawatan_lapangan' => $request->boolean('rawatan_lapangan'),
            'rawatan_klinik' => $request->boolean('rawatan_klinik'),
            'pembedahan' => $request->boolean('pembedahan'),
            'keterangan_pembedahan' => $request->input('keterangan_pembedahan'),
            'pemantauan_pawah' => $request->boolean('pemantauan_pawah'),
            'pemantauan_projek' => $request->boolean('pemantauan_projek'),
            'keterangan_projek' => $request->input('keterangan_projek'),
            'pemantauan_trust' => $request->boolean('pemantauan_trust'),
            'lawatan_terancang' => $request->boolean('lawatan_terancang'),
            'perkhidmatan_lain' => $request->boolean('perkhidmatan_lain'),
            'keterangan_lain' => $request->input('keterangan_lain'),
        ];

        $jenisTernakan = $request->input('jenis_ternakan', []);
        if (!is_array($jenisTernakan)) {
            $jenisTernakan = [$jenisTernakan];
        }

        $actionList->update([
            'no_bil' => $request->input('no_bil', $actionList->no_bil),
            'jajahan' => $request->input('jajahan'),
            'tarikh' => $request->input('tarikh'),
            'masa_pendaftaran' => $request->input('masa_pendaftaran'),
            'kategori_pelanggan' => $request->input('kategori_pelanggan', 'Individu'),
            'nama_pelanggan' => $request->input('nama_pelanggan'),
            'no_kp' => $request->input('no_kp'),
            'alamat' => $request->input('alamat'),
            'mukim' => $request->input('mukim'),
            'poskod' => $request->input('poskod'),
            'daerah' => $request->input('daerah'),
            'telefon' => $request->input('telefon'),
            'no_rujukan' => $request->input('no_rujukan'),

            'catatan_perkhidmatan_dipohon' => $request->input('catatan_perkhidmatan_dipohon'),

            'nama_pegawai' => $request->input('nama_pegawai'),
            'masa_pegawai' => $request->input('masa_pegawai'),
            'masa_temujanji_mula' => $request->input('masa_temujanji_mula'),
            'masa_temujanji_hingga' => $request->input('masa_temujanji_hingga'),
            'maklumat_pelanggan_berlainan' => $request->input('maklumat_pelanggan_berlainan'),
            'maklumat_tambahan' => $request->input('maklumat_tambahan'),
            'lampiran_peta' => $lampiranPetaPath,

            'perkhidmatan_diberi' => $perkhidmatanDiberi,
            'keterangan_pembedahan' => $request->input('keterangan_pembedahan'),
            'keterangan_projek' => $request->input('keterangan_projek'),
            'keterangan_lain' => $request->input('keterangan_lain'),

            'jenis_ternakan' => $jenisTernakan,
            'jenis_ternakan_lain' => $request->input('jenis_ternakan_lain'),
            'bil_ternakan' => $request->input('bil_ternakan'),
            'bil_yang_ada' => $request->input('bil_yang_ada'),

            'laporan' => $request->input('laporan'),
            'penggunaan_ubat' => $request->input('penggunaan_ubat'),

            'tandatangan_pelanggan_nama' => $request->input('tandatangan_pelanggan_nama'),
            'tandatangan_pelanggan_tarikh' => $request->input('tandatangan_pelanggan_tarikh'),
            'tandatangan_pelanggan_masa' => $request->input('tandatangan_pelanggan_masa'),
            'kepuasan_pelanggan' => $request->input('kepuasan_pelanggan'),
            'cadangan_pelanggan' => $request->input('cadangan_pelanggan'),
            'bayaran' => $request->input('bayaran', 0.00),
            'no_resit' => $request->input('no_resit'),
            'pengesahan_ulasan_pegawai' => $request->input('pengesahan_ulasan_pegawai'),

            'status' => $request->input('status', 'Selesai'),
        ]);

        return redirect()->route('action-list.show', $actionList->id)
            ->with('success', "Borang Action List '{$actionList->no_bil}' berjaya dikemaskini.");
    }

    /**
     * Padam rekod Action List (Super Admin / Admin Jajahan)
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user || (!$user->isSuperAdmin() && !$user->isAdminJajahan())) {
            abort(403, 'Akses Ditolak: Hanya Super Admin dan Admin Jajahan dibenarkan memadam rekod Action List.');
        }

        $actionList = ActionList::findOrFail($id);
        $noBil = $actionList->no_bil;

        if ($actionList->lampiran_peta && Storage::disk('public')->exists($actionList->lampiran_peta)) {
            Storage::disk('public')->delete($actionList->lampiran_peta);
        }

        $actionList->delete();

        return redirect()->route('action-list.index')
            ->with('success', "Borang Action List '{$noBil}' telah berjaya dipadam daripada sistem.");
    }

    /**
     * Cetak Borang Rasmi Action List (PK-RK-61 Format)
     */
    public function cetak($id)
    {
        $actionList = ActionList::with(['user', 'pegawai', 'temujanji'])->findOrFail($id);
        return view('action_list.cetak', compact('actionList'));
    }

    /**
     * API Carian Pelanggan / Penternak untuk Autolengkap Bahagian A
     */
    public function apiCariPelanggan(Request $request)
    {
        $q = $request->input('query');
        if (empty($q) || strlen($q) < 2) {
            return response()->json([]);
        }

        $users = User::where('name', 'like', "%{$q}%")
            ->orWhere('ic_number', 'like', "%{$q}%")
            ->orWhere('phone', 'like', "%{$q}%")
            ->limit(10)
            ->get(['id', 'name', 'ic_number', 'phone', 'address', 'poskod', 'jajahan']);

        return response()->json($users);
    }
}
