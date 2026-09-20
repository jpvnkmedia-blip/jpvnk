<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\KlinikTemujanji;
use App\Models\KlinikRawatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        if (Auth::user()->isStaff()) {
            $registeredClients = User::whereIn('role', ['orang_awam', 'penternak', 'usahawan'])
                ->orderBy('name')
                ->get();
        }

        return view('klinik.create', compact('klinikList', 'registeredClients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'jenis_haiwan' => 'required|string',
            'nama_haiwan' => 'nullable|string|max:100',
            'baka' => 'nullable|string|max:100',
            'jantina_haiwan' => 'required|in:Jantan,Betina,Tidak Diketahui',
            'umur_haiwan' => 'nullable|string|max:50',
            'simptom_atau_tujuan' => 'required|string',
            'tarikh_temujanji' => 'required|date|after_or_equal:today',
            'sesi' => 'required|string',
            'klinik_jajahan' => 'required|string',
        ]);

        $userId = Auth::id();
        if (Auth::user()->isStaff() && !empty($validated['user_id'])) {
            $userId = $validated['user_id'];
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
            'klinik_jajahan' => $validated['klinik_jajahan'],
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

        return redirect()->route('klinik.show', $temujanji->id)->with('success', 'Rekod rawatan pesakit veterinar berjaya disimpan.');
    }

    public function cetakKadRawatan($id)
    {
        $temujanji = KlinikTemujanji::with('pemilik', 'rawatan')->findOrFail($id);
        return view('klinik.cetak-kad-rawatan', compact('temujanji'));
    }
}
