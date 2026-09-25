<?php

namespace App\Http\Controllers;

use App\Models\ActionList;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ActionListController extends Controller
{
    /**
     * Papar senarai log & dairi aktiviti admin (Action List)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->canManageActionList()) {
            abort(403, 'Akses Ditolak: Anda tidak mempunyai kebenaran untuk mengakses Action List.');
        }

        $jajahanList = array_keys(config('kelantan.jajahan', []));
        if (empty($jajahanList)) {
            $jajahanList = ['Pasir Puteh', 'Kota Bharu', 'Pasir Mas', 'Tumpat', 'Bachok', 'Machang', 'Tanah Merah', 'Kuala Krai', 'Gua Musang', 'Jeli'];
        }

        $selectedJajahan = $request->input('jajahan', 'Semua');

        $query = ActionList::with(['pegawai', 'creator']);

        if ($selectedJajahan && $selectedJajahan !== 'Semua') {
            $query->where('jajahan', $selectedJajahan);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('tajuk_aktiviti', 'like', "%{$search}%")
                  ->orWhere('maklumat_aktiviti', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%")
                  ->orWhere('nama_pegawai', 'like', "%{$search}%")
                  ->orWhere('no_bil', 'like', "%{$search}%")
                  ->orWhere('tindakan_susulan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori_aktiviti', $request->input('kategori'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('keutamaan')) {
            $query->where('keutamaan', $request->input('keutamaan'));
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

        $totalAktiviti = (clone $statsQuery)->count();
        $selesaiCount = (clone $statsQuery)->where('status', 'Selesai')->count();
        $dalamTindakanCount = (clone $statsQuery)->where('status', 'Dalam Tindakan')->count();
        $perancanganCount = (clone $statsQuery)->where('status', 'Perancangan')->count();
        $bulanIniCount = (clone $statsQuery)->whereMonth('tarikh', Carbon::now()->month)->whereYear('tarikh', Carbon::now()->year)->count();

        $kategoriList = ActionList::KATEGORI_LIST;
        $statusList = ActionList::STATUS_LIST;

        return view('action_list.index', compact(
            'actionLists',
            'jajahanList',
            'selectedJajahan',
            'totalAktiviti',
            'selesaiCount',
            'dalamTindakanCount',
            'perancanganCount',
            'bulanIniCount',
            'kategoriList',
            'statusList'
        ));
    }

    /**
     * Borang rekod aktiviti baharu
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->canManageActionList()) {
            abort(403, 'Akses Ditolak.');
        }

        $defaultJajahan = $user->jajahan ?: 'Pasir Puteh';
        $jajahanList = array_keys(config('kelantan.jajahan', []));
        if (empty($jajahanList)) {
            $jajahanList = ['Pasir Puteh', 'Kota Bharu', 'Pasir Mas', 'Tumpat', 'Bachok', 'Machang', 'Tanah Merah', 'Kuala Krai', 'Gua Musang', 'Jeli'];
        }

        $kategoriList = ActionList::KATEGORI_LIST;
        $statusList = ActionList::STATUS_LIST;
        $keutamaanList = ActionList::KEUTAMAAN_LIST;

        return view('action_list.create', compact(
            'defaultJajahan',
            'jajahanList',
            'kategoriList',
            'statusList',
            'keutamaanList'
        ));
    }

    /**
     * Simpan rekod aktiviti baharu
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->canManageActionList()) {
            abort(403, 'Akses Ditolak.');
        }

        $request->validate([
            'tajuk_aktiviti' => 'required|string|max:255',
            'tarikh' => 'required|date',
            'kategori_aktiviti' => 'required|string|max:100',
            'jajahan' => 'required|string|max:100',
            'maklumat_aktiviti' => 'required|string',
            'masa_mula' => 'nullable|string|max:30',
            'masa_selesai' => 'nullable|string|max:30',
            'lokasi' => 'nullable|string|max:255',
            'nama_pegawai' => 'nullable|string|max:255',
            'status' => 'required|in:Selesai,Dalam Tindakan,Perancangan,Ditangguhkan,Dibatalkan',
            'keutamaan' => 'nullable|in:Biasa,Tinggi,Segera',
            'tindakan_susulan' => 'nullable|string',
            'lampiran' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx',
        ]);

        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('action_list_lampiran', 'public');
        }

        $noBil = ActionList::generateNoBil($request->input('jajahan'));

        $actionList = ActionList::create([
            'no_bil' => $noBil,
            'tajuk_aktiviti' => $request->input('tajuk_aktiviti'),
            'tarikh' => $request->input('tarikh'),
            'kategori_aktiviti' => $request->input('kategori_aktiviti'),
            'jajahan' => $request->input('jajahan'),
            'maklumat_aktiviti' => $request->input('maklumat_aktiviti'),
            'masa_mula' => $request->input('masa_mula'),
            'masa_selesai' => $request->input('masa_selesai'),
            'lokasi' => $request->input('lokasi'),
            'nama_pegawai' => $request->input('nama_pegawai', $user->name),
            'status' => $request->input('status', 'Selesai'),
            'keutamaan' => $request->input('keutamaan', 'Biasa'),
            'tindakan_susulan' => $request->input('tindakan_susulan'),
            'lampiran' => $lampiranPath,
            'pegawai_id' => $user->id,
            'created_by' => $user->id,
        ]);

        return redirect()->route('action-list.index')
            ->with('success', "Aktiviti '{$actionList->tajuk_aktiviti}' berjaya direkodkan ke dalam Action List.");
    }

    /**
     * Papar butiran rekod aktiviti
     */
    public function show($id)
    {
        $user = Auth::user();
        if (!$user || !$user->canManageActionList()) {
            abort(403, 'Akses Ditolak.');
        }

        $actionList = ActionList::with(['pegawai', 'creator'])->findOrFail($id);

        return view('action_list.show', compact('actionList'));
    }

    /**
     * Borang kemaskini rekod aktiviti
     */
    public function edit($id)
    {
        $user = Auth::user();
        if (!$user || !$user->canManageActionList()) {
            abort(403, 'Akses Ditolak.');
        }

        $actionList = ActionList::with(['pegawai'])->findOrFail($id);

        $jajahanList = array_keys(config('kelantan.jajahan', []));
        if (empty($jajahanList)) {
            $jajahanList = ['Pasir Puteh', 'Kota Bharu', 'Pasir Mas', 'Tumpat', 'Bachok', 'Machang', 'Tanah Merah', 'Kuala Krai', 'Gua Musang', 'Jeli'];
        }

        $kategoriList = ActionList::KATEGORI_LIST;
        $statusList = ActionList::STATUS_LIST;
        $keutamaanList = ActionList::KEUTAMAAN_LIST;

        return view('action_list.edit', compact(
            'actionList',
            'jajahanList',
            'kategoriList',
            'statusList',
            'keutamaanList'
        ));
    }

    /**
     * Simpan kemaskini rekod aktiviti
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || !$user->canManageActionList()) {
            abort(403, 'Akses Ditolak.');
        }

        $actionList = ActionList::findOrFail($id);

        $request->validate([
            'tajuk_aktiviti' => 'required|string|max:255',
            'tarikh' => 'required|date',
            'kategori_aktiviti' => 'required|string|max:100',
            'jajahan' => 'required|string|max:100',
            'maklumat_aktiviti' => 'required|string',
            'masa_mula' => 'nullable|string|max:30',
            'masa_selesai' => 'nullable|string|max:30',
            'lokasi' => 'nullable|string|max:255',
            'nama_pegawai' => 'nullable|string|max:255',
            'status' => 'required|in:Selesai,Dalam Tindakan,Perancangan,Ditangguhkan,Dibatalkan',
            'keutamaan' => 'nullable|in:Biasa,Tinggi,Segera',
            'tindakan_susulan' => 'nullable|string',
            'lampiran' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx',
        ]);

        $lampiranPath = $actionList->lampiran;
        if ($request->hasFile('lampiran')) {
            if ($lampiranPath && Storage::disk('public')->exists($lampiranPath)) {
                Storage::disk('public')->delete($lampiranPath);
            }
            $lampiranPath = $request->file('lampiran')->store('action_list_lampiran', 'public');
        }

        $actionList->update([
            'tajuk_aktiviti' => $request->input('tajuk_aktiviti'),
            'tarikh' => $request->input('tarikh'),
            'kategori_aktiviti' => $request->input('kategori_aktiviti'),
            'jajahan' => $request->input('jajahan'),
            'maklumat_aktiviti' => $request->input('maklumat_aktiviti'),
            'masa_mula' => $request->input('masa_mula'),
            'masa_selesai' => $request->input('masa_selesai'),
            'lokasi' => $request->input('lokasi'),
            'nama_pegawai' => $request->input('nama_pegawai', $actionList->nama_pegawai),
            'status' => $request->input('status', $actionList->status),
            'keutamaan' => $request->input('keutamaan', $actionList->keutamaan),
            'tindakan_susulan' => $request->input('tindakan_susulan'),
            'lampiran' => $lampiranPath,
        ]);

        return redirect()->route('action-list.show', $actionList->id)
            ->with('success', "Rekod aktiviti '{$actionList->tajuk_aktiviti}' berjaya dikemaskini.");
    }

    /**
     * Padam rekod aktiviti
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user || (!$user->isSuperAdmin() && !$user->isAdminJajahan())) {
            abort(403, 'Akses Ditolak: Hanya Super Admin dan Admin Jajahan dibenarkan memadam rekod Action List.');
        }

        $actionList = ActionList::findOrFail($id);
        $tajuk = $actionList->tajuk_aktiviti ?: $actionList->no_bil;

        if ($actionList->lampiran && Storage::disk('public')->exists($actionList->lampiran)) {
            Storage::disk('public')->delete($actionList->lampiran);
        }

        $actionList->delete();

        return redirect()->route('action-list.index')
            ->with('success', "Aktiviti '{$tajuk}' telah berjaya dipadam.");
    }

    /**
     * Cetak Laporan / Dairi Aktiviti Pegawai
     */
    public function cetak(Request $request, $id = null)
    {
        $user = Auth::user();
        if (!$user || !$user->canManageActionList()) {
            abort(403, 'Akses Ditolak.');
        }

        if ($id) {
            $actionList = ActionList::with(['pegawai', 'creator'])->findOrFail($id);
            return view('action_list.cetak_tunggal', compact('actionList'));
        }

        // Cetak Senarai Dairi Mengikut Penapis
        $query = ActionList::with(['pegawai']);

        if ($request->filled('jajahan') && $request->input('jajahan') !== 'Semua') {
            $query->where('jajahan', $request->input('jajahan'));
        }

        if ($request->filled('kategori')) {
            $query->where('kategori_aktiviti', $request->input('kategori'));
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

        $actionLists = $query->latest('tarikh')->latest('id')->get();
        $selectedJajahan = $request->input('jajahan', 'Semua');
        $tarikhMula = $request->input('tarikh_mula');
        $tarikhAkhir = $request->input('tarikh_akhir');

        return view('action_list.cetak', compact('actionLists', 'selectedJajahan', 'tarikhMula', 'tarikhAkhir'));
    }
}
