<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kenderaan;
use App\Models\KenderaanTempahan;
use App\Models\Pemandu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Closure;
use Carbon\Carbon;

class KenderaanController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            function (Request $request, Closure $next) {
                if (Auth::check()) {
                    $user = Auth::user();
                    if (!$user->canAccessKenderaan()) {
                        abort(403, 'Akses Ditolak: Peranan ' . ($user->role_label ?? $user->role) . ' tidak dibenarkan mengakses modul Kenderaan Rasmi.');
                    }
                }
                return $next($request);
            }
        ];
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $kenderaanList = Kenderaan::all();
        $availableKenderaan = Kenderaan::where('status', 'Sedia')->get();
        $pemanduList = Pemandu::where('status', 'Aktif')->get();
        
        $query = KenderaanTempahan::with('pemohon', 'kenderaan', 'pelulus');

        if (!$user->isStaff()) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'Menunggu') {
                $query->whereIn('status', ['Menunggu', 'Menunggu Kelulusan']);
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_tempahan', 'like', "%{$search}%")
                  ->orWhere('destinasi', 'like', "%{$search}%")
                  ->orWhere('tujuan_perjalanan', 'like', "%{$search}%")
                  ->orWhereHas('pemohon', function ($qp) use ($search) {
                      $qp->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $tempahanList = $query->latest()->paginate(15);
        $totalKenderaan = Kenderaan::count();
        $totalSedia = Kenderaan::where('status', 'Sedia')->count();
        $totalTempahanMenunggu = KenderaanTempahan::whereIn('status', ['Menunggu', 'Menunggu Kelulusan'])->count();
        $totalTempahanDiluluskan = KenderaanTempahan::where('status', 'Diluluskan')->count();
        $totalTempahanDitolak = KenderaanTempahan::where('status', 'Ditolak')->count();
        $totalTempahanSelesai = KenderaanTempahan::where('status', 'Selesai')->count();
        $totalPemandu = Pemandu::count();

        return view('kenderaan.index', compact(
            'kenderaanList',
            'availableKenderaan',
            'pemanduList',
            'tempahanList',
            'totalKenderaan',
            'totalSedia',
            'totalTempahanMenunggu',
            'totalTempahanDiluluskan',
            'totalTempahanDitolak',
            'totalTempahanSelesai',
            'totalPemandu'
        ))->with('fleet', $kenderaanList);
    }

    public function create()
    {
        if (!Auth::user()->canBookVehicle()) {
            abort(403, 'Akses Ditolak: Peranan ' . Auth::user()->role_label . ' tidak dibenarkan membuat permohonan tempahan kenderaan rasmi jabatan.');
        }

        $availableKenderaan = Kenderaan::where('status', 'Sedia')->get();
        $pemanduList = Pemandu::where('status', 'Aktif')->get();
        $fleet = Kenderaan::all();
        $kenderaanList = $fleet;
        return view('kenderaan.create', compact('availableKenderaan', 'pemanduList', 'fleet', 'kenderaanList'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->canBookVehicle()) {
            abort(403, 'Akses Ditolak: Peranan ' . Auth::user()->role_label . ' tidak dibenarkan membuat permohonan tempahan kenderaan rasmi jabatan.');
        }

        $validated = $request->validate([
            'kenderaan_id' => 'nullable|exists:kenderaan,id',
            'destinasi' => 'required|string',
            'tujuan' => 'nullable|string',
            'tujuan_perjalanan' => 'nullable|string',
            'tarikh_keluar' => 'nullable',
            'tarikh_kembali' => 'nullable',
            'tarikh_mula' => 'nullable|date',
            'masa_mula' => 'nullable',
            'tarikh_tamat' => 'nullable|date',
            'masa_tamat' => 'nullable',
            'bilangan_penumpang' => 'required|integer|min:1',
            'nama_pemandu' => 'nullable|string',
            'pemandu_nama' => 'nullable|string',
            'senarai_nama_penumpang' => 'nullable|string',
        ]);

        $tujuan = $request->input('tujuan') ?: $request->input('tujuan_perjalanan', 'Urusan Rasmi Jabatan');

        $tarikhMula = $request->input('tarikh_mula');
        $masaMula = $request->input('masa_mula');
        if ($request->filled('tarikh_keluar')) {
            $dtKeluar = Carbon::parse($request->input('tarikh_keluar'));
            $tarikhMula = $dtKeluar->format('Y-m-d');
            $masaMula = $dtKeluar->format('H:i');
        }

        $tarikhTamat = $request->input('tarikh_tamat');
        $masaTamat = $request->input('masa_tamat');
        if ($request->filled('tarikh_kembali')) {
            $dtKembali = Carbon::parse($request->input('tarikh_kembali'));
            $tarikhTamat = $dtKembali->format('Y-m-d');
            $masaTamat = $dtKembali->format('H:i');
        }

        do {
            $noTempahan = 'KND-' . date('Ymd') . '-' . rand(1000, 9999) . rand(10, 99);
        } while (KenderaanTempahan::where('no_tempahan', $noTempahan)->exists());

        $tempahan = KenderaanTempahan::create([
            'user_id' => Auth::id(),
            'kenderaan_id' => $validated['kenderaan_id'] ?? null,
            'no_tempahan' => $noTempahan,
            'tujuan_perjalanan' => $tujuan,
            'destinasi' => $validated['destinasi'],
            'tarikh_mula' => $tarikhMula ?? date('Y-m-d'),
            'masa_mula' => $masaMula ?? '08:30',
            'tarikh_tamat' => $tarikhTamat ?? date('Y-m-d'),
            'masa_tamat' => $masaTamat ?? '17:00',
            'bilangan_penumpang' => $validated['bilangan_penumpang'],
            'pemandu_nama' => $request->input('nama_pemandu') ?: $request->input('pemandu_nama'),
            'senarai_nama_penumpang' => $validated['senarai_nama_penumpang'] ?? null,
            'status' => 'Menunggu',
        ]);

        \App\Models\UserNotification::send(
            Auth::id(),
            'Tempahan Kenderaan Dihantar',
            "Tempahan kenderaan rasmi ({$noTempahan}) ke {$validated['destinasi']} telah dihantar untuk kelulusan.",
            'kenderaan',
            route('kenderaan.show', $tempahan->id),
            'fa-solid fa-truck-pickup',
            'teal'
        );

        return redirect()->route('kenderaan.index')->with('success', 'Permohonan tempahan kenderaan rasmi berjaya dihantar untuk kelulusan admin pejabat.');
    }

    public function show($id)
    {
        $tempahan = KenderaanTempahan::with('pemohon', 'kenderaan', 'pelulus')->findOrFail($id);
        $availableKenderaan = Kenderaan::where('status', 'Sedia')->get();
        $availableFleet = $availableKenderaan;
        $fleet = Kenderaan::all();
        $pemanduList = Pemandu::where('status', 'Aktif')->get();

        return view('kenderaan.show', compact('tempahan', 'availableKenderaan', 'availableFleet', 'fleet', 'pemanduList'));
    }

    // Kelulusan & Penugasan Pemandu (Admin Kenderaan / Super Admin)
    public function approve(Request $request, $id)
    {
        if (!Auth::user()->canManageKenderaanFleet()) {
            abort(403, 'Akses Ditolak: Hanya Admin Kenderaan dan Super Admin dibenarkan membuat kelulusan tempahan kenderaan.');
        }

        $tempahan = KenderaanTempahan::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:Diluluskan,Ditolak',
            'kenderaan_id' => 'required_if:status,Diluluskan|nullable|exists:kenderaan,id',
            'pemandu_nama' => 'nullable|string',
            'nama_pemandu' => 'nullable|string',
            'catatan_kelulusan' => 'nullable|string',
        ]);

        $tempahan->status = $validated['status'];
        if ($validated['status'] === 'Diluluskan') {
            $tempahan->kenderaan_id = $validated['kenderaan_id'];
            $tempahan->pemandu_nama = $request->input('nama_pemandu') ?: $request->input('pemandu_nama');
            
            // Kemaskini odometer keluar kenderaan
            $kenderaan = Kenderaan::find($validated['kenderaan_id']);
            if ($kenderaan) {
                $tempahan->odometer_keluar = $kenderaan->odometer_semasa_km;
                $kenderaan->status = 'Sedang Digunakan';
                $kenderaan->save();
            }
        }
        $tempahan->catatan_kelulusan = $validated['catatan_kelulusan'] ?? null;
        $tempahan->diluluskan_oleh = Auth::id();
        $tempahan->save();

        if ($tempahan->user_id) {
            $title = $validated['status'] === 'Diluluskan' ? 'Tempahan Kenderaan Diluluskan' : 'Tempahan Kenderaan Ditolak';
            $msg = $validated['status'] === 'Diluluskan' 
                ? "Permohonan tempahan kenderaan rasmi ({$tempahan->no_tempahan}) ke {$tempahan->destinasi} telah DILULUSKAN." 
                : "Permohonan tempahan kenderaan ({$tempahan->no_tempahan}) telah DITOLAK.";
            $icon = $validated['status'] === 'Diluluskan' ? 'fa-solid fa-circle-check' : 'fa-solid fa-circle-xmark';
            $color = $validated['status'] === 'Diluluskan' ? 'emerald' : 'rose';

            \App\Models\UserNotification::send(
                $tempahan->user_id,
                $title,
                $msg,
                'kenderaan',
                route('kenderaan.show', $tempahan->id),
                $icon,
                $color
            );
        }

        // Catat ke Action List
        \App\Models\ActionList::catatAktiviti([
            'tajuk_aktiviti' => "Kelulusan Tempahan Kenderaan Jabatan (#{$tempahan->no_tempahan}) - {$validated['status']}",
            'kategori_aktiviti' => 'Tempahan Kenderaan & Media',
            'maklumat_aktiviti' => "Tindakan kelulusan tempahan kenderaan rasmi (#{$tempahan->no_tempahan}) ke {$tempahan->destinasi} bagi tujuan '{$tempahan->tujuan_perjalanan}'. Status: {$validated['status']}.",
            'jajahan' => 'Kota Bharu',
            'lokasi' => $tempahan->destinasi,
            'status' => 'Selesai',
        ]);

        return redirect()->back()->with('success', 'Status permohonan kenderaan berjaya dikemaskini.');
    }

    // Penolakan Permohonan Kenderaan
    public function tolak(Request $request, $id)
    {
        if (!Auth::user()->canManageKenderaanFleet()) {
            abort(403, 'Akses Ditolak: Hanya Admin Kenderaan dan Super Admin dibenarkan menolak tempahan kenderaan.');
        }

        $tempahan = KenderaanTempahan::findOrFail($id);

        $catatan = $request->input('catatan_kelulusan') ?: $request->input('sebab_tolak', 'Permohonan ditolak oleh pegawai penguasa pentadbiran.');

        $tempahan->status = 'Ditolak';
        $tempahan->catatan_kelulusan = $catatan;
        $tempahan->diluluskan_oleh = Auth::id();
        $tempahan->save();

        // Catat ke Action List
        \App\Models\ActionList::catatAktiviti([
            'tajuk_aktiviti' => "Penolakan Tempahan Kenderaan Jabatan (#{$tempahan->no_tempahan})",
            'kategori_aktiviti' => 'Tempahan Kenderaan & Media',
            'maklumat_aktiviti' => "Menolak tempahan kenderaan (#{$tempahan->no_tempahan}) ke {$tempahan->destinasi}. Sebab: {$catatan}.",
            'jajahan' => 'Kota Bharu',
            'lokasi' => $tempahan->destinasi,
            'status' => 'Selesai',
        ]);

        return redirect()->back()->with('success', 'Permohonan tempahan kenderaan telah ditolak.');
    }

    // Pemulangan Kenderaan & Log Odometer
    public function complete(Request $request, $id)
    {
        $tempahan = KenderaanTempahan::findOrFail($id);

        $odometerMasuk = $request->input('odometer_tamat') ?: $request->input('odometer_masuk');

        $validated = $request->validate([
            'odometer_masuk' => 'nullable|integer',
            'odometer_tamat' => 'nullable|integer',
            'kos_minyak' => 'nullable|numeric',
            'laporan_keadaan_kenderaan' => 'nullable|string',
        ]);

        $finalOdometer = $odometerMasuk ?: (($tempahan->odometer_keluar ?? 45000) + 50);

        $tempahan->odometer_masuk = $finalOdometer;
        $tempahan->status = 'Selesai';
        $tempahan->save();

        if ($tempahan->kenderaan) {
            $tempahan->kenderaan->odometer_semasa_km = $finalOdometer;
            $tempahan->kenderaan->status = 'Sedia';
            $tempahan->save();
        }

        return redirect()->route('kenderaan.show', $tempahan->id)->with('success', 'Perjalanan telah selesai dan bacaan odometer berjaya dikemaskini.');
    }

    // Direktori & Pengurusan Fleet Kenderaan
    public function fleetIndex(Request $request)
    {
        if (!Auth::user()->canManageKenderaanFleet()) {
            abort(403, 'Akses Ditolak: Hanya Admin Kenderaan dan Super Admin dibenarkan melihat dan menguruskan Fleet Kenderaan.');
        }

        $query = Kenderaan::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis_kenderaan', $request->jenis);
        }

        if ($request->filled('jajahan')) {
            $query->where('jajahan_penempatan', $request->jajahan);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_pendaftaran', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('jenis_kenderaan', 'like', "%{$search}%");
            });
        }

        $kenderaanList = $query->orderBy('no_pendaftaran')->paginate(12);

        $totalKenderaan = Kenderaan::count();
        $totalSedia = Kenderaan::where('status', 'Sedia')->count();
        $totalDigunakan = Kenderaan::where('status', 'Sedang Digunakan')->count();
        $totalServis = Kenderaan::whereIn('status', ['Dalam Servis', 'Rosak'])->count();

        return view('kenderaan.fleet.index', compact(
            'kenderaanList',
            'totalKenderaan',
            'totalSedia',
            'totalDigunakan',
            'totalServis'
        ));
    }

    // Tambah Maklumat Kenderaan Baharu
    public function storeKenderaan(Request $request)
    {
        if (!Auth::user()->canManageKenderaanFleet()) {
            abort(403, 'Akses Ditolak: Hanya Admin Kenderaan dan Super Admin dibenarkan mendaftar kenderaan.');
        }

        $validated = $request->validate([
            'no_pendaftaran' => 'required|string|max:20|unique:kenderaan,no_pendaftaran',
            'jenis_kenderaan' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'tahun_buatan' => 'nullable|integer|min:1990|max:' . (date('Y') + 1),
            'kapasiti_penumpang' => 'required|integer|min:1|max:50',
            'jajahan_penempatan' => 'required|string|max:50',
            'status' => 'required|in:Sedia,Sedang Digunakan,Dalam Servis,Rosak',
            'lokasi_kunci' => 'nullable|string|max:100',
            'odometer_semasa_km' => 'nullable|integer|min:0',
            'tarikh_tamat_cukai_jalan' => 'nullable|date',
            'catatan' => 'nullable|string',
        ]);

        Kenderaan::create($validated);

        return redirect()->back()->with('success', 'Maklumat kenderaan baharu berjaya didaftarkan.');
    }

    // Kemaskini Maklumat Kenderaan
    public function updateKenderaan(Request $request, $id)
    {
        if (!Auth::user()->canManageKenderaanFleet()) {
            abort(403, 'Akses Ditolak: Hanya Admin Kenderaan dan Super Admin dibenarkan mengemaskini kenderaan.');
        }

        $kenderaan = Kenderaan::findOrFail($id);

        $validated = $request->validate([
            'no_pendaftaran' => 'required|string|max:20|unique:kenderaan,no_pendaftaran,' . $kenderaan->id,
            'jenis_kenderaan' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'tahun_buatan' => 'nullable|integer|min:1990|max:' . (date('Y') + 1),
            'kapasiti_penumpang' => 'required|integer|min:1|max:50',
            'jajahan_penempatan' => 'required|string|max:50',
            'status' => 'required|in:Sedia,Sedang Digunakan,Dalam Servis,Rosak',
            'lokasi_kunci' => 'nullable|string|max:100',
            'odometer_semasa_km' => 'nullable|integer|min:0',
            'tarikh_tamat_cukai_jalan' => 'nullable|date',
            'catatan' => 'nullable|string',
        ]);

        $kenderaan->update($validated);

        return redirect()->back()->with('success', 'Maklumat kenderaan berjaya dikemaskini.');
    }

    // Padam Maklumat Kenderaan
    public function destroyKenderaan($id)
    {
        if (!Auth::user()->canManageKenderaanFleet()) {
            abort(403, 'Akses Ditolak: Hanya Admin Kenderaan dan Super Admin dibenarkan memadam kenderaan.');
        }

        $kenderaan = Kenderaan::findOrFail($id);
        $kenderaan->delete();

        return redirect()->back()->with('success', 'Rekod kenderaan telah dipadam.');
    }

    // Padam Permohonan Tempahan Kenderaan (Super Admin Sahaja)
    public function destroyTempahan($id)
    {
        $user = Auth::user();
        if (!$user || !$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Super Admin dibenarkan memadam permohonan tempahan kenderaan.');
        }

        $tempahan = KenderaanTempahan::findOrFail($id);
        $noTempahan = $tempahan->no_tempahan;

        // Reset status kenderaan jika tempahan sedang berjalan/diluluskan
        if ($tempahan->kenderaan && $tempahan->status === 'Diluluskan') {
            $tempahan->kenderaan->update(['status' => 'Sedia']);
        }

        $tempahan->delete();

        return redirect()->route('kenderaan.index')->with('success', "Rekod tempahan kenderaan '{$noTempahan}' berjaya dipadam daripada sistem.");
    }
}
