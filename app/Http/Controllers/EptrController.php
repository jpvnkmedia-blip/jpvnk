<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pemunya;
use App\Models\Ternakan;
use App\Models\PindahMilik;
use App\Models\PembatalanTernakan;
use App\Models\PermitSembelihan;
use App\Models\RekodKelahiran;
use App\Models\ProgramKesihatan;
use App\Models\PawahTernakan;
use App\Models\PawahPerjanjian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Closure;
use Carbon\Carbon;

class EptrController extends Controller implements HasMiddleware
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
                    if (!$user->canAccessEptr()) {
                        abort(403, 'Akses Ditolak: Peranan ' . ($user->role_label ?? $user->role) . ' tidak dibenarkan mengakses modul EPTR (Ternakan Ruminan).');
                    }
                }
                return $next($request);
            }
        ];
    }

    // Senarai Ternakan EPTR
    public function index(Request $request)
    {
        $user = Auth::user();

        // Admin Pejabat & Admin Kenderaan hanya dibenarkan menguruskan Pentadbiran
        if (in_array($user->role, ['admin_pejabat', 'admin_kenderaan'])) {
            return redirect()->route($user->role === 'admin_kenderaan' ? 'kenderaan.index' : 'inventori.index')->with('error', 'Akses Ditolak: Peranan ' . $user->role_label . ' dikhaskan untuk Pengurusan Pentadbiran sahaja.');
        }

        $query = Ternakan::with('pemunya', 'pawahTernakan');

        // Dapatkan profil Pemunya EPTR bagi pengguna
        $currentPemunya = $user->pemunya;
        if (!$currentPemunya && $user->ic_number) {
            $currentPemunya = Pemunya::where('no_kp', $user->ic_number)->first();
        }
        if (!$currentPemunya && $user->id) {
            $currentPemunya = Pemunya::where('user_id', $user->id)->first();
        }

        $baseStatsQuery = Ternakan::query();

        // Jika bukan staf admin, hanya paparkan ternakan sendiri (termasuk ternakan program pawah yang dipautkan kepada pengguna)
        if (!$user->isStaff()) {
            $userPawahTernakanIds = PawahTernakan::whereHas('perjanjian', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->pluck('ternakan_id');

            if ($currentPemunya || $userPawahTernakanIds->isNotEmpty()) {
                $query->where(function ($q) use ($currentPemunya, $userPawahTernakanIds) {
                    if ($currentPemunya) {
                        $q->where('pemunya_id', $currentPemunya->id);
                    }
                    if ($userPawahTernakanIds->isNotEmpty()) {
                        $q->orWhereIn('id', $userPawahTernakanIds);
                    }
                });
                $baseStatsQuery->where(function ($q) use ($currentPemunya, $userPawahTernakanIds) {
                    if ($currentPemunya) {
                        $q->where('pemunya_id', $currentPemunya->id);
                    }
                    if ($userPawahTernakanIds->isNotEmpty()) {
                        $q->orWhereIn('id', $userPawahTernakanIds);
                    }
                });
            } else {
                $query->whereRaw('1 = 0');
                $baseStatsQuery->whereRaw('1 = 0');
            }
        } else {
            // Filter jajahan jika admin jajahan
            if (in_array($user->role, ['admin_jajahan', 'admin_eptr_jajahan']) && $user->jajahan) {
                $query->where('jajahan', $user->jajahan);
                $baseStatsQuery->where('jajahan', $user->jajahan);
            }
        }

        // Carian & Penapis
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_tag', 'like', "%{$search}%")
                  ->orWhere('baka', 'like', "%{$search}%")
                  ->orWhereHas('pemunya', function ($qp) use ($search) {
                      $qp->where('nama', 'like', "%{$search}%")
                         ->orWhere('no_kp', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('jenis_ternakan')) {
            $query->whereRaw('LOWER(jenis_ternakan) = ?', [strtolower($request->jenis_ternakan)]);
        }

        if ($request->filled('jajahan')) {
            $query->where('jajahan', $request->jajahan);
        }

        if ($request->filled('program')) {
            if ($request->program === 'Ada Program') {
                $query->where(function ($q) {
                    $q->where(function ($sq) {
                        $sq->whereNotNull('program')->where('program', '!=', 'Tiada');
                    })->orWhere('status', 'Pawah');
                });
            } elseif ($request->program === 'Tiada Program') {
                $query->where(function ($q) {
                    $q->whereNull('program')->orWhere('program', 'Tiada');
                })->where('status', '!=', 'Pawah');
            } else {
                $query->where(function ($q) use ($request) {
                    $q->where('program', 'like', "%{$request->program}%")
                      ->orWhere('status', 'like', "%{$request->program}%");
                });
            }
        }

        if ($request->filled('pemunya_id')) {
            $query->where('pemunya_id', $request->pemunya_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $ternakanList = $query->latest()->paginate(15);
        $totalCows = (clone $baseStatsQuery)->whereRaw('LOWER(jenis_ternakan) = ?', ['lembu'])->count();
        $totalGoats = (clone $baseStatsQuery)->whereRaw('LOWER(jenis_ternakan) = ?', ['kambing'])->count();
        $totalBuffalo = (clone $baseStatsQuery)->whereRaw('LOWER(jenis_ternakan) = ?', ['kerbau'])->count();
        $totalSheep = (clone $baseStatsQuery)->where(function ($q) {
            $q->whereRaw('LOWER(jenis_ternakan) = ?', ['biri-biri'])
              ->orWhereRaw('LOWER(jenis_ternakan) = ?', ['biri biri'])
              ->orWhereRaw('LOWER(jenis_ternakan) = ?', ['biribiri'])
              ->orWhereRaw('LOWER(jenis_ternakan) = ?', ['domba']);
        })->count();
        $totalWithProgram = (clone $baseStatsQuery)->where(function ($q) {
            $q->where(function ($sq) {
                $sq->whereNotNull('program')->where('program', '!=', 'Tiada');
            })->orWhere('status', 'Pawah');
        })->count();

        $pemunyaList = $user->isStaff() ? Pemunya::orderBy('nama')->get() : ($currentPemunya ? collect([$currentPemunya]) : collect());

        return view('eptr.index', compact(
            'ternakanList',
            'totalCows',
            'totalGoats',
            'totalBuffalo',
            'totalSheep',
            'totalWithProgram',
            'pemunyaList'
        ));
    }

    // EPTR Borang A: Borang Pendaftaran Ternakan
    public function create()
    {
        $user = Auth::user();

        // Semua admin kecuali Super Admin tidak dibenarkan mendaftar ternakan (Borang A)
        if ($user->isStaff() && !$user->isSuperAdmin()) {
            return redirect()->route('eptr.index')->with('error', 'Akses Ditolak: Pegawai pentadbir tidak dibenarkan mengisi Borang A. Pendaftaran ternakan hanya boleh diisi oleh penternak / pemunya.');
        }

        $pemunyaList = $user->isStaff() ? Pemunya::orderBy('nama')->get() : ($user->pemunya ? collect([$user->pemunya]) : collect());
        
        $kelantanData = config('kelantan.jajahan', []);
        $jajahanList = array_keys($kelantanData);
        $bakaData = config('baka', []);

        // Senarai Induk Berdaftar (Ternakan Betina Aktif & Diluluskan)
        $indukQuery = Ternakan::with('pemunya')
            ->where('jantina', 'Betina')
            ->whereNotNull('no_tag')
            ->whereIn('status', ['Aktif', 'Pawah'])
            ->where('status_kelulusan', 'Diluluskan')
            ->whereDoesntHave('pembatalanTernakan', function ($q) {
                $q->where('status_kelulusan', 'Disahkan');
            })
            ->whereDoesntHave('permitSembelihan', function ($q) {
                $q->where('status_kelulusan', 'Diluluskan');
            });

        $indukList = $indukQuery->orderBy('no_tag')->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'no_tag' => $item->no_tag,
                'baka' => $item->baka,
                'jenis_ternakan' => strtolower($item->jenis_ternakan),
                'warna' => $item->warna ?? '',
                'jajahan' => $item->jajahan ?? '',
                'pemunya' => $item->pemunya ? $item->pemunya->nama : 'N/A',
                'pemunya_kp' => $item->pemunya ? $item->pemunya->no_kp : '',
                'pemunya_id' => $item->pemunya_id,
            ];
        });

        $programList = [
            'Tiada',
            'Program Pawah Ternakan Negeri Kelantan',
            'Program Pawah Dun',
            'Skim Bantuan Baka Induk Pedaging',
            'Program Usahawan Belia Ternakan',
            'Program Pembiakan Baka Hibrid (Sado/Charolais)'
        ];

        return view('eptr.borang-a', compact('pemunyaList', 'jajahanList', 'kelantanData', 'bakaData', 'programList', 'user', 'indukList'));
    }

    /**
     * Jana No. Tag Telinga Rasmi secara automatik berasaskan kod singkatan Daerah dan nombor turutan
     */
    public static function generateNoTag($jajahan, $daerah)
    {
        $kodMap = config("kelantan.jajahan.{$jajahan}.kod", []);
        $kod = $kodMap[$daerah] ?? null;

        if (!$kod) {
            // Singkatan gantian 3 konsonan/huruf jika tidak ditemui
            $clean = strtoupper(preg_replace('/[^A-Za-z]/', '', $daerah ?? $jajahan));
            $kod = substr($clean, 0, 3) ?: 'KLT';
        }

        // Dapatkan nombor turutan tertinggi bagi kod daerah ini
        $lastTernakan = Ternakan::where('no_tag', 'like', "{$kod}-%")
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastTernakan && preg_match('/-(\d+)$/', $lastTernakan->no_tag, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $count = Ternakan::where('no_tag', 'like', "{$kod}-%")->count();
            $nextNumber = $count + 1;
        }

        // Pastikan tiada pertembungan nombor tag yang unik
        do {
            $noTag = sprintf('%s-%04d', $kod, $nextNumber);
            $exists = Ternakan::where('no_tag', $noTag)->exists();
            if ($exists) {
                $nextNumber++;
            }
        } while ($exists);

        return $noTag;
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Semua admin kecuali Super Admin tidak dibenarkan mendaftar ternakan (Borang A)
        if ($user->isStaff() && !$user->isSuperAdmin()) {
            return redirect()->route('eptr.index')->with('error', 'Akses Ditolak: Pegawai pentadbir tidak dibenarkan mendaftar ternakan (Borang A). Tugas pegawai adalah menyemak dan meluluskan permohonan.');
        }

        $isStaff = $user->isStaff();

        $validated = $request->validate([
            'nama_pemunya' => 'required_without:pemunya_id|string|max:255',
            'no_kp_pemunya' => 'required_without:pemunya_id|string|max:20',
            'no_tel_pemunya' => 'required_without:pemunya_id|string|max:25',
            'alamat_pemunya' => 'required_without:pemunya_id|string',
            'pemunya_id' => 'nullable|exists:pemunya,id',
            'jenis_ternakan' => 'required|string',
            'baka' => 'required|string',
            'baka_pejantan' => 'nullable|string',
            'baka_induk' => 'nullable|string',
            'no_tanda_pengenalan_induk' => 'nullable|string',
            'jantina' => 'required|in:Jantan,Betina',
            'umur' => 'nullable|string',
            'tarikh_lahir' => 'required|date|before_or_equal:today',
            'warna' => 'nullable|string',
            'tanda_badan' => 'nullable|string',
            'tujuan_ternakan' => 'required|string',
            'lokasi_kandang' => 'nullable|string',
            'jajahan' => 'required|string',
            'daerah' => 'nullable|string',
            'poskod' => 'nullable|string',
            'program' => 'nullable|string', // Kolum pilihan PROGRAM
            'resit_pembayaran' => $isStaff ? 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048' : 'required|file|mimes:jpeg,png,jpg,pdf|max:2048', // Wajib bagi penternak (Maksimum 2MB)
            'catatan' => 'nullable|string',
        ], [
            'tarikh_lahir.required' => 'Tarikh lahir ternakan wajib diisi untuk menentukan umur dan pengiraan fi pendaftaran.',
            'tarikh_lahir.before_or_equal' => 'Tarikh lahir ternakan tidak boleh melebihi tarikh hari ini.',
            'resit_pembayaran.required' => 'Semasa mendaftar ternakan (Borang A), penternak wajib melampirkan salinan resit pembayaran.',
            'resit_pembayaran.mimes' => 'Fail resit pembayaran mestilah dalam format JPG, PNG, atau PDF.',
            'resit_pembayaran.max' => 'Saiz fail resit pembayaran tidak boleh melebihi 2MB.',
        ]);

        // Dapatkan atau cipta profil Pemunya
        if ($request->filled('pemunya_id')) {
            $pemunya = Pemunya::findOrFail($request->pemunya_id);
            if ($request->filled('daerah')) {
                $pemunya->daerah = $request->daerah;
            }
            if ($request->filled('poskod')) {
                $pemunya->poskod = $request->poskod;
            }
            $pemunya->save();
        } else {
            $pemunya = Pemunya::firstOrCreate(
                ['no_kp' => $request->no_kp_pemunya],
                [
                    'user_id' => $user->isStaff() ? null : $user->id,
                    'nama' => $request->nama_pemunya,
                    'no_telefon' => $request->no_tel_pemunya,
                    'alamat' => $request->alamat_pemunya,
                    'jajahan' => $request->jajahan,
                    'daerah' => $request->daerah,
                    'poskod' => $request->poskod,
                    'status' => 'Aktif',
                ]
            );
        }

        // Muat naik fail resit pembayaran jika disertakan
        $resitPath = null;
        if ($request->hasFile('resit_pembayaran')) {
            $resitPath = $this->uploadFileSafely($request->file('resit_pembayaran'), 'resit_eptr');
        }

        // Auto-kira umur dari tarikh lahir jika medan umur kosong
        $computedUmur = $validated['umur'] ?? null;
        if (empty($computedUmur) && !empty($validated['tarikh_lahir'])) {
            $birth = Carbon::parse($validated['tarikh_lahir']);
            $now = Carbon::now();
            $years = $birth->diffInYears($now);
            $months = $birth->copy()->addYears($years)->diffInMonths($now);
            $days = $birth->copy()->addYears($years)->addMonths($months)->diffInDays($now);

            if ($years > 0) {
                $computedUmur = $years . ' Tahun' . ($months > 0 ? (' ' . $months . ' Bulan') : '');
            } elseif ($months > 0) {
                $computedUmur = $months . ' Bulan' . ($days > 0 ? (' ' . $days . ' Hari') : '');
            } else {
                $computedUmur = $days . ' Hari';
            }
        }

        $statusKelulusan = 'Menunggu';
        $statusTernakan = 'Menunggu';
        $noTag = null;
        $noSiriKadKuning = null;
        $qrCode = null;
        $tarikhDaftar = null;
        $diluluskanOleh = null;
        $tarikhKelulusan = null;

        $isStaff = $user->isStaff();
        $programValue = $isStaff ? ($validated['program'] ?? 'Tiada') : 'Tiada';
        $catatanValue = $validated['catatan'] ?? null;

        // Semak dan pautkan No. Tag Induk jika wujud dalam sistem
        $tagInduk = trim($validated['no_tanda_pengenalan_induk'] ?? '');
        $indukTernakan = null;
        if (!empty($tagInduk) && strtoupper($tagInduk) !== 'TIADA') {
            $indukTernakan = Ternakan::where('no_tag', $tagInduk)->first();
            if (!$indukTernakan) {
                $indukTernakan = Ternakan::whereRaw('UPPER(no_tag) = ?', [strtoupper($tagInduk)])->first();
            }
        }

        $bakaInduk = $validated['baka_induk'] ?? null;
        if (empty($bakaInduk) && $indukTernakan) {
            $bakaInduk = $indukTernakan->baka;
        }
        if (empty($bakaInduk)) {
            $bakaInduk = $validated['baka'] ?? 'KACUKAN';
        }

        $noTagIndukClean = !empty($tagInduk) ? ($indukTernakan ? $indukTernakan->no_tag : $tagInduk) : 'TIADA';

        $ternakan = Ternakan::create([
            'pemunya_id' => $pemunya->id,
            'no_tag' => $noTag,
            'jenis_ternakan' => $validated['jenis_ternakan'],
            'baka' => $validated['baka'],
            'baka_pejantan' => $validated['baka_pejantan'] ?? ($validated['baka'] ?? 'KACUKAN'),
            'baka_induk' => $bakaInduk,
            'no_tanda_pengenalan_induk' => $noTagIndukClean,
            'jantina' => $validated['jantina'],
            'umur' => $computedUmur ?? 'Tidak Dinyatakan',
            'tarikh_lahir' => $validated['tarikh_lahir'],
            'warna' => $validated['warna'] ?? null,
            'tanda_badan' => $validated['tanda_badan'] ?? null,
            'tujuan_ternakan' => $validated['tujuan_ternakan'],
            'lokasi_kandang' => $validated['lokasi_kandang'] ?? null,
            'jajahan' => $validated['jajahan'],
            'daerah' => $validated['daerah'] ?? null,
            'poskod' => $validated['poskod'] ?? null,
            'program' => $programValue,
            'status' => $statusTernakan,
            'status_kelulusan' => $statusKelulusan,
            'tarikh_daftar' => $tarikhDaftar,
            'tarikh_kelulusan' => $tarikhKelulusan,
            'diluluskan_oleh' => $diluluskanOleh,
            'no_siri_kad_kuning' => $noSiriKadKuning,
            'qr_code' => $qrCode,
            'resit_pembayaran' => $resitPath,
            'catatan' => $catatanValue,
            'didaftar_oleh' => $user->id,
        ]);

        // Pautkan secara automatik rekod kelahiran dengan Induk berdaftar
        if ($indukTernakan) {
            RekodKelahiran::firstOrCreate(
                [
                    'induk_id' => $indukTernakan->id,
                    'anak_ternakan_id' => $ternakan->id,
                ],
                [
                    'pemunya_id' => $ternakan->pemunya_id,
                    'no_tag_sementara' => $ternakan->no_tag ?? ('MENUNGGU-' . $ternakan->id),
                    'jantina_anak' => $ternakan->jantina,
                    'tarikh_kelahiran' => $ternakan->tarikh_lahir,
                    'baka_anak' => $ternakan->baka,
                    'warna_anak' => $ternakan->warna,
                    'tanda_badan_anak' => $ternakan->tanda_badan,
                    'status_kelahiran' => 'Hidup',
                    'keadaan_anak' => 'Cergas',
                    'catatan' => 'Dipautkan secara automatik dari pendaftaran EPTR (Borang A)',
                    'didaftar_oleh' => $user->id,
                ]
            );
        }

        // 1. Notifikasi kepada pemohon (Penternak / Orang Awam)
        \App\Models\UserNotification::send(
            $user->id,
            'Permohonan Pendaftaran Ternakan Dihantar',
            "Borang A pendaftaran " . ucfirst($ternakan->jenis_ternakan) . " ({$ternakan->baka}) telah dihantar dan sedang menunggu kelulusan Pegawai JPVNK.",
            'eptr',
            route('eptr.show', $ternakan->id),
            'fa-solid fa-file-signature',
            'amber'
        );

        // 2. Notifikasi kepada Admin EPTR Jajahan berkenaan
        $adminJajahanList = User::whereIn('role', ['admin_jajahan', 'admin_eptr_jajahan'])
            ->where(function ($q) use ($ternakan) {
                $q->where('jajahan', $ternakan->jajahan)
                  ->orWhereNull('jajahan')
                  ->orWhere('jajahan', '');
            })
            ->where('id', '!=', $user->id)
            ->get();

        if ($adminJajahanList->isEmpty()) {
            $adminJajahanList = User::whereIn('role', ['admin_jajahan', 'admin_eptr_jajahan'])
                ->where('id', '!=', $user->id)
                ->get();
        }

        foreach ($adminJajahanList as $aj) {
            \App\Models\UserNotification::send(
                $aj->id,
                "Permohonan Ternakan Menunggu Kelulusan ({$ternakan->jajahan})",
                "Permohonan pendaftaran Borang A bagi ternakan " . ucfirst($ternakan->jenis_ternakan) . " ({$ternakan->baka}) oleh {$pemunya->nama} di Jajahan {$ternakan->jajahan} memerlukan semakan & kelulusan anda.",
                'eptr',
                route('eptr.show', $ternakan->id),
                'fa-solid fa-clipboard-check',
                'amber'
            );
        }

        // 3. Notifikasi kepada Admin EPTR Negeri & Super Admin
        $adminNegeriList = User::whereIn('role', ['admin_eptr', 'super_admin'])
            ->where('id', '!=', $user->id)
            ->get();

        foreach ($adminNegeriList as $an) {
            \App\Models\UserNotification::send(
                $an->id,
                "Permohonan Ternakan Baharu ({$ternakan->jajahan})",
                "Penternak {$pemunya->nama} telah menghantar permohonan pendaftaran ternakan " . ucfirst($ternakan->jenis_ternakan) . " ({$ternakan->baka}) di Jajahan {$ternakan->jajahan} (Menunggu Kelulusan).",
                'eptr',
                route('eptr.show', $ternakan->id),
                'fa-solid fa-cow',
                'blue'
            );
        }

        return redirect()->route('eptr.show', $ternakan->id)->with('success', 'Permohonan Pendaftaran Ternakan EPTR (Borang A) telah dihantar! No. Tag Telinga Rasmi akan dijana secara automatik selepas permohonan diluluskan oleh Admin Jajahan.');
    }

    /**
     * Kelulusan Permohonan Pendaftaran EPTR oleh Admin Jajahan
     */
    public function luluskanPendaftaran(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isStaff()) {
            return back()->with('error', 'Hanya Admin Jajahan / Pegawai EPTR yang dibenarkan meluluskan pendaftaran ternakan.');
        }

        $ternakan = Ternakan::findOrFail($id);

        if ($ternakan->status_kelulusan === 'Diluluskan' && !empty($ternakan->no_tag)) {
            return back()->with('info', "Ternakan ini telah pun diluluskan dengan No. Tag: {$ternakan->no_tag}.");
        }

        // Auto-generate No. Tag Telinga mengikut short form daerah dan no turutan
        $noTag = self::generateNoTag($ternakan->jajahan, $ternakan->daerah);
        $noSiriKadKuning = 'DB-' . strtoupper(substr($ternakan->jajahan, 0, 2)) . '-' . date('Y') . '-' . rand(10000, 99999);
        $qrCode = 'QR-EPTR-' . $noTag;

        $ternakan->no_tag = $noTag;
        $ternakan->status_kelulusan = 'Diluluskan';
        $ternakan->status = (!empty($ternakan->program) && $ternakan->program !== 'Tiada' && str_contains(strtolower($ternakan->program), 'pawah')) ? 'Pawah' : 'Aktif';
        $ternakan->tarikh_daftar = Carbon::now()->toDateString();
        $ternakan->tarikh_kelulusan = Carbon::now();
        $ternakan->diluluskan_oleh = $user->id;
        $ternakan->no_siri_kad_kuning = $noSiriKadKuning;
        $ternakan->qr_code = $qrCode;
        $ternakan->save();

        // Kemaskini rekod kelahiran anak jika ada
        if ($ternakan->rekodKelahiranSendiri) {
            $ternakan->rekodKelahiranSendiri->update([
                'no_tag_sementara' => $noTag,
            ]);
        }

        // Notifikasi kepada pemunya jika pengguna berdaftar
        if ($ternakan->pemunya && $ternakan->pemunya->user_id) {
            \App\Models\UserNotification::send(
                $ternakan->pemunya->user_id,
                'Pendaftaran Ternakan Diluluskan',
                "Permohonan pendaftaran ternakan " . ucfirst($ternakan->jenis_ternakan) . " ({$ternakan->baka}) telah DILULUSKAN dengan No. Tag Rasmi: {$noTag}.",
                'eptr',
                route('eptr.show', $ternakan->id),
                'fa-solid fa-circle-check',
                'emerald'
            );
        }

        // Catat ke Action List (Dairi Aktiviti Admin)
        \App\Models\ActionList::catatAktiviti([
            'tajuk_aktiviti' => "Kelulusan Pendaftaran Ternakan EPTR (Tag: {$noTag})",
            'kategori_aktiviti' => 'Pendaftaran Ternakan (EPTR)',
            'maklumat_aktiviti' => "Meluluskan pendaftaran ternakan {$ternakan->jenis_ternakan} ({$ternakan->baka}) bagi pemunya " . ($ternakan->pemunya->nama ?? 'Penternak') . " dengan No. Tag Rasmi {$noTag} dan No Siri Kad Kuning {$noSiriKadKuning}.",
            'jajahan' => $ternakan->jajahan ?: ($user->jajahan ?: 'Pasir Puteh'),
            'lokasi' => 'Pejabat JPV Jajahan ' . ($ternakan->jajahan ?: 'Pasir Puteh'),
            'status' => 'Selesai',
        ]);

        return redirect()->route('eptr.show', $ternakan->id)->with('success', "Permohonan pendaftaran ternakan berjaya DILULUSKAN! No. Tag Telinga Rasmi: {$noTag} telah dijana secara automatik mengikut Daerah {$ternakan->daerah}.");
    }

    /**
     * Penolakan Permohonan Pendaftaran EPTR oleh Admin Jajahan
     */
    public function tolakPendaftaran(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isStaff()) {
            return back()->with('error', 'Hanya Admin Jajahan / Pegawai EPTR yang dibenarkan menolak pendaftaran.');
        }

        $ternakan = Ternakan::findOrFail($id);
        $ternakan->status_kelulusan = 'Ditolak';
        $ternakan->status = 'Batal';
        $ternakan->catatan = ($ternakan->catatan ? $ternakan->catatan . " | " : "") . "Ditolak oleh Admin Jajahan pada " . Carbon::now()->format('d/m/Y');
        $ternakan->save();

        // Notifikasi penolakan kepada pemunya
        if ($ternakan->pemunya && $ternakan->pemunya->user_id) {
            \App\Models\UserNotification::send(
                $ternakan->pemunya->user_id,
                'Pendaftaran Ternakan Ditolak',
                "Permohonan pendaftaran ternakan " . ucfirst($ternakan->jenis_ternakan) . " ({$ternakan->baka}) tidak diluluskan. Sila semak maklumat atau hubungi pejabat.",
                'eptr',
                route('eptr.index'),
                'fa-solid fa-circle-xmark',
                'rose'
            );
        }

        // Catat ke Action List
        \App\Models\ActionList::catatAktiviti([
            'tajuk_aktiviti' => "Penolakan Pendaftaran Ternakan EPTR (ID: #{$ternakan->id})",
            'kategori_aktiviti' => 'Pendaftaran Ternakan (EPTR)',
            'maklumat_aktiviti' => "Menolak permohonan pendaftaran ternakan {$ternakan->jenis_ternakan} ({$ternakan->baka}) bagi pemunya " . ($ternakan->pemunya->nama ?? 'Penternak') . ".",
            'jajahan' => $ternakan->jajahan ?: ($user->jajahan ?: 'Pasir Puteh'),
            'lokasi' => 'Pejabat JPV Jajahan ' . ($ternakan->jajahan ?: 'Pasir Puteh'),
            'status' => 'Selesai',
        ]);

        return redirect()->route('eptr.index')->with('info', "Permohonan pendaftaran ternakan telah DITOLAK.");
    }

    /**
     * Muat Naik / Kemaskini Salinan Resit Pembayaran Ternakan
     */
    public function muatNaikResit(Request $request, $id)
    {
        $user = Auth::user();
        $ternakan = Ternakan::findOrFail($id);

        $isOwner = ($ternakan->pemunya && $ternakan->pemunya->user_id === $user->id) || ($ternakan->didaftar_oleh === $user->id);
        if (!$isOwner && !$user->isStaff()) {
            return back()->with('error', 'Akses Ditolak: Anda tidak mempunyai kebenaran untuk memuat naik resit bagi ternakan ini.');
        }

        $request->validate([
            'resit_pembayaran' => 'required|file|mimes:jpeg,png,jpg,pdf|max:10240',
        ], [
            'resit_pembayaran.required' => 'Sila pilih fail resit atau gambar slip bayaran.',
            'resit_pembayaran.mimes' => 'Fail resit mestilah dalam format JPG, PNG, atau PDF.',
            'resit_pembayaran.max' => 'Saiz fail resit tidak boleh melebihi 10MB.',
        ]);

        $resitPath = $this->uploadFileSafely($request->file('resit_pembayaran'), 'resit_eptr');

        if (!$resitPath) {
            return back()->with('error', 'Gagal memuat naik fail resit. Sila cuba lagi.');
        }

        $ternakan->resit_pembayaran = $resitPath;
        $ternakan->save();

        return back()->with('success', 'Gambar / fail resit pembayaran telah berjaya dimuat naik.');
    }

    // Cetak Borang A Daftar (Format Asal) - Hanya Admin Jajahan / Staf yang telah Diluluskan
    public function cetakBorangA($id)
    {
        $user = Auth::user();
        if (!$user || !$user->isStaff()) {
            abort(403, 'Akses Ditolak: Hanya Admin Jajahan / Pegawai EPTR yang dibenarkan mencetak perakuan dan dokumen rasmi EPTR.');
        }

        $ternakan = Ternakan::with('pemunya', 'pindahMilik.pemunyaBaru', 'pembatalanTernakan', 'permitSembelihan', 'kelahiranAnak.anakTernakan')->findOrFail($id);

        if ($ternakan->status_kelulusan !== 'Diluluskan' || empty($ternakan->no_tag)) {
            return back()->with('error', 'Akses Ditolak: Borang A tidak boleh dicetak selagi permohonan pendaftaran ternakan belum diluluskan oleh Admin Jajahan.');
        }

        return view('eptr.cetak-borang-a', compact('ternakan'));
    }

    // Cetak Pukal Borang A (Banyak Ternakan / Pemilik Terpilih)
    public function cetakPukalBorangA(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isStaff()) {
            abort(403, 'Akses Ditolak: Hanya Admin Jajahan / Pegawai EPTR yang dibenarkan mencetak perakuan dan dokumen rasmi EPTR.');
        }

        $ids = $request->input('ids', []);
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }

        if (empty($ids)) {
            return back()->with('error', 'Sila pilih sekurang-kurangnya satu ekor ternakan untuk dicetak.');
        }

        $ternakanList = Ternakan::with('pemunya', 'pindahMilik.pemunyaBaru', 'pembatalanTernakan', 'permitSembelihan', 'kelahiranAnak.anakTernakan')
            ->whereIn('id', $ids)
            ->where('status_kelulusan', 'Diluluskan')
            ->whereNotNull('no_tag')
            ->orderBy('pemunya_id')
            ->orderBy('no_tag')
            ->get();

        if ($ternakanList->isEmpty()) {
            return back()->with('error', 'Borang A tidak boleh dicetak bagi ternakan yang belum diluluskan oleh Admin Jajahan.');
        }

        return view('eptr.cetak-pukal-borang-a', compact('ternakanList'));
    }

    // EPTR Kad Kuning / Pasport Perakuan Pendaftaran
    public function show($id)
    {
        $ternakan = Ternakan::with('pemunya', 'pawahTernakan.perjanjian', 'permitSembelihan', 'pindahMilik.pemunyaBaru', 'kelahiranAnak.anakTernakan', 'programKesihatan')->findOrFail($id);
        return view('eptr.kad-kuning', compact('ternakan'));
    }

    // Cetak Kad Kuning (Format Asal) - Hanya Admin Jajahan / Staf yang telah Diluluskan
    public function cetakKadKuning($id)
    {
        $user = Auth::user();
        if (!$user || !$user->isStaff()) {
            abort(403, 'Akses Ditolak: Hanya Admin Jajahan / Pegawai EPTR yang dibenarkan mencetak Kad Kuning rasmi EPTR.');
        }

        $ternakan = Ternakan::with('pemunya', 'pawahTernakan.perjanjian', 'pindahMilik.pemunyaBaru')->findOrFail($id);

        if ($ternakan->status_kelulusan !== 'Diluluskan' || empty($ternakan->no_tag)) {
            return back()->with('error', 'Akses Ditolak: Kad Kuning tidak boleh dicetak selagi permohonan pendaftaran ternakan belum diluluskan oleh Admin Jajahan.');
        }

        return view('eptr.cetak-kad-kuning', compact('ternakan'));
    }

    // Cetak Pukal Kad Kuning (Banyak Ternakan / Pemilik Terpilih)
    public function cetakPukalKadKuning(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isStaff()) {
            abort(403, 'Akses Ditolak: Hanya Admin Jajahan / Pegawai EPTR yang dibenarkan mencetak Kad Kuning rasmi EPTR.');
        }

        $ids = $request->input('ids', []);
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }

        if (empty($ids)) {
            return back()->with('error', 'Sila pilih sekurang-kurangnya satu ekor ternakan untuk dicetak.');
        }

        $ternakanList = Ternakan::with('pemunya', 'pawahTernakan.perjanjian', 'pindahMilik.pemunyaBaru')
            ->whereIn('id', $ids)
            ->where('status_kelulusan', 'Diluluskan')
            ->whereNotNull('no_tag')
            ->orderBy('pemunya_id')
            ->orderBy('no_tag')
            ->get();

        if ($ternakanList->isEmpty()) {
            return back()->with('error', 'Kad Kuning tidak boleh dicetak bagi ternakan yang belum diluluskan oleh Admin Jajahan.');
        }

        return view('eptr.cetak-pukal-kad-kuning', compact('ternakanList'));
    }

    // ==========================================
    // PENGURUSAN SENARAI PENTERNAK (PEMUNYA)
    // ==========================================

    public function penternakIndex(Request $request)
    {
        $user = Auth::user();

        // Sekatan peranan: Orang awam tidak dibenarkan melihat senarai penternak
        if ($user->role === 'orang_awam') {
            abort(403, 'Akses Ditolak: Pengguna peranan Orang Awam tidak dibenarkan melihat senarai penternak.');
        }

        $query = Pemunya::withCount([
            'ternakan',
            'ternakan as ternakan_aktif_count' => function ($q) {
                $q->whereIn('status', ['Aktif', 'Pawah'])->where('status_kelulusan', 'Diluluskan');
            },
            'ternakan as lembu_count' => function ($q) {
                $q->whereRaw('LOWER(jenis_ternakan) = ?', ['lembu'])->whereIn('status', ['Aktif', 'Pawah']);
            },
            'ternakan as kambing_count' => function ($q) {
                $q->whereRaw('LOWER(jenis_ternakan) = ?', ['kambing'])->whereIn('status', ['Aktif', 'Pawah']);
            },
            'ternakan as kerbau_count' => function ($q) {
                $q->whereRaw('LOWER(jenis_ternakan) = ?', ['kerbau'])->whereIn('status', ['Aktif', 'Pawah']);
            },
            'ternakan as biri_count' => function ($q) {
                $q->where(function ($sq) {
                    $sq->whereRaw('LOWER(jenis_ternakan) = ?', ['biri-biri'])
                       ->orWhereRaw('LOWER(jenis_ternakan) = ?', ['biri biri'])
                       ->orWhereRaw('LOWER(jenis_ternakan) = ?', ['biribiri'])
                       ->orWhereRaw('LOWER(jenis_ternakan) = ?', ['domba']);
                })->whereIn('status', ['Aktif', 'Pawah']);
            },
        ])->with(['user', 'ternakan' => function ($q) {
            $q->select('id', 'pemunya_id', 'jenis_ternakan', 'baka', 'no_tag', 'status', 'status_kelulusan');
        }]);

        // Sekatan peranan: Penternak biasa hanya boleh melihat rekod dirinya
        if (!$user->isStaff()) {
            if ($user->pemunya) {
                $query->where('id', $user->pemunya->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif ($user->role === 'admin_jajahan' && $user->jajahan) {
            $query->where('jajahan', $user->jajahan);
        }

        // 1. Penapisan Carian Kata Kunci
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_kp', 'like', "%{$search}%")
                  ->orWhere('no_telefon', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhereHas('ternakan', function ($qt) use ($search) {
                      $qt->where('no_tag', 'like', "%{$search}%")
                         ->orWhere('baka', 'like', "%{$search}%");
                  });
            });
        }

        // 2. Penapisan Jajahan
        if ($request->filled('jajahan')) {
            $query->where('jajahan', $request->jajahan);
        }

        // 3. Penapisan Status Keaktifan Penternak
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 4. Penapisan Jenis Spesies Ternakan
        if ($request->filled('spesies')) {
            $spesies = strtolower($request->spesies);
            $query->whereHas('ternakan', function ($q) use ($spesies) {
                $q->whereRaw('LOWER(jenis_ternakan) = ?', [$spesies]);
            });
        }

        // 5. Penapisan Status Program Bantuan / Pawah
        if ($request->filled('program')) {
            if ($request->program === 'Ada Program Pawah') {
                $query->whereHas('ternakan', function ($q) {
                    $q->where('status', 'Pawah')->orWhere('program', 'like', '%pawah%');
                });
            } elseif ($request->program === 'Tiada Program (Biasa)') {
                $query->whereDoesntHave('ternakan', function ($q) {
                    $q->where('status', 'Pawah')->orWhere('program', 'like', '%pawah%');
                });
            }
        }

        // Statistik KPI (berdasarkan skop kuasa pengguna)
        $kpiBaseQuery = Pemunya::query();
        if (!$user->isStaff() && $user->pemunya) {
            $kpiBaseQuery->where('id', $user->pemunya->id);
        } elseif ($user->role === 'admin_jajahan' && $user->jajahan) {
            $kpiBaseQuery->where('jajahan', $user->jajahan);
        }

        $totalPenternak = (clone $kpiBaseQuery)->count();
        $totalAktif = (clone $kpiBaseQuery)->where('status', 'Aktif')->count();
        $totalAdaTernakan = (clone $kpiBaseQuery)->has('ternakan')->count();
        $totalPenternakPawah = (clone $kpiBaseQuery)->whereHas('ternakan', function ($q) {
            $q->where('status', 'Pawah')->orWhere('program', 'like', '%pawah%');
        })->count();

        // Jumlah ternakan dalam skop
        $ownerIds = (clone $kpiBaseQuery)->pluck('id');
        $totalTernakanAktif = Ternakan::whereIn('pemunya_id', $ownerIds)->whereIn('status', ['Aktif', 'Pawah'])->count();
        $totalLembu = Ternakan::whereIn('pemunya_id', $ownerIds)->whereRaw('LOWER(jenis_ternakan) = ?', ['lembu'])->whereIn('status', ['Aktif', 'Pawah'])->count();
        $totalKambing = Ternakan::whereIn('pemunya_id', $ownerIds)->whereRaw('LOWER(jenis_ternakan) = ?', ['kambing'])->whereIn('status', ['Aktif', 'Pawah'])->count();
        $totalKerbau = Ternakan::whereIn('pemunya_id', $ownerIds)->whereRaw('LOWER(jenis_ternakan) = ?', ['kerbau'])->whereIn('status', ['Aktif', 'Pawah'])->count();
        $totalBiri = Ternakan::whereIn('pemunya_id', $ownerIds)->where(function ($q) {
            $q->whereRaw('LOWER(jenis_ternakan) = ?', ['biri-biri'])
              ->orWhereRaw('LOWER(jenis_ternakan) = ?', ['biri biri'])
              ->orWhereRaw('LOWER(jenis_ternakan) = ?', ['biribiri'])
              ->orWhereRaw('LOWER(jenis_ternakan) = ?', ['domba']);
        })->whereIn('status', ['Aktif', 'Pawah'])->count();

        $penternakList = $query->orderBy('nama')->paginate(15)->withQueryString();
        $kelantanData = config('kelantan.jajahan', []);

        return view('eptr.penternak-index', compact(
            'penternakList',
            'totalPenternak',
            'totalAktif',
            'totalAdaTernakan',
            'totalPenternakPawah',
            'totalTernakanAktif',
            'totalLembu',
            'totalKambing',
            'totalKerbau',
            'totalBiri',
            'kelantanData'
        ));
    }

    public function penternakShow($id)
    {
        $user = Auth::user();

        // Sekatan akses: Orang awam tidak dibenarkan melihat maklumat penternak
        if ($user->role === 'orang_awam') {
            abort(403, 'Akses Ditolak: Pengguna peranan Orang Awam tidak dibenarkan melihat maklumat penternak.');
        }

        // Sekatan akses: Penternak biasa hanya boleh melihat profil sendiri
        if (!$user->isStaff() && (!$user->pemunya || $user->pemunya->id != $id)) {
            abort(403, 'Akses Ditolak: Anda hanya dibenarkan melihat maklumat penternak milik anda sendiri.');
        }

        $pemunya = Pemunya::withCount([
            'ternakan',
            'ternakan as ternakan_aktif_count' => function ($q) {
                $q->whereIn('status', ['Aktif', 'Pawah'])->where('status_kelulusan', 'Diluluskan');
            },
            'ternakan as lembu_count' => function ($q) {
                $q->whereRaw('LOWER(jenis_ternakan) = ?', ['lembu'])->whereIn('status', ['Aktif', 'Pawah']);
            },
            'ternakan as kambing_count' => function ($q) {
                $q->whereRaw('LOWER(jenis_ternakan) = ?', ['kambing'])->whereIn('status', ['Aktif', 'Pawah']);
            },
            'ternakan as kerbau_count' => function ($q) {
                $q->whereRaw('LOWER(jenis_ternakan) = ?', ['kerbau'])->whereIn('status', ['Aktif', 'Pawah']);
            },
            'ternakan as biri_count' => function ($q) {
                $q->where(function ($sq) {
                    $sq->whereRaw('LOWER(jenis_ternakan) = ?', ['biri-biri'])
                       ->orWhereRaw('LOWER(jenis_ternakan) = ?', ['biri biri'])
                       ->orWhereRaw('LOWER(jenis_ternakan) = ?', ['biribiri'])
                       ->orWhereRaw('LOWER(jenis_ternakan) = ?', ['domba']);
                })->whereIn('status', ['Aktif', 'Pawah']);
            },
        ])->with([
            'ternakan' => function ($q) {
                $q->latest();
            },
            'user',
            'permitSembelihan' => function ($q) {
                $q->latest();
            }
        ])->findOrFail($id);

        // Rekod Pindah Milik melibatkan penternak ini (sama ada sebagai penjual atau pembeli)
        $pindahMilikList = PindahMilik::with(['ternakan', 'pemunyaAsal', 'pemunyaBaru'])
            ->where('pemunya_asal_id', $pemunya->id)
            ->orWhere('pemunya_baru_id', $pemunya->id)
            ->latest('tarikh_pindah')
            ->get();

        return view('eptr.penternak-show', compact('pemunya', 'pindahMilikList'));
    }

    // ==========================================
    // EPTR BORANG B: NOTIS PERTUKARAN MILIKAN
    // ==========================================

    public function borangBIndex(Request $request)
    {
        $user = Auth::user();
        $query = PindahMilik::with(['ternakan.pemunya', 'pemunyaAsal', 'pemunyaBaru', 'pelulus']);

        if (!$user->isStaff() && $user->pemunya) {
            $pemId = $user->pemunya->id;
            $query->where(function ($q) use ($pemId) {
                $q->where('pemunya_asal_id', $pemId)
                  ->orWhere('pemunya_baru_id', $pemId);
            });
        } elseif ($user->role === 'admin_jajahan' && $user->jajahan) {
            $query->whereHas('ternakan', function ($q) use ($user) {
                $q->where('jajahan', $user->jajahan);
            });
        }

        // Penapisan Carian Kata Kunci
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sebab_pindah', 'like', "%{$search}%")
                  ->orWhere('catatan', 'like', "%{$search}%")
                  ->orWhereHas('ternakan', function ($tq) use ($search) {
                      $tq->where('no_tag', 'like', "%{$search}%");
                  })
                  ->orWhereHas('pemunyaAsal', function ($pq) use ($search) {
                      $pq->where('nama', 'like', "%{$search}%")
                         ->orWhere('no_kp', 'like', "%{$search}%");
                  })
                  ->orWhereHas('pemunyaBaru', function ($pq) use ($search) {
                      $pq->where('nama', 'like', "%{$search}%")
                         ->orWhere('no_kp', 'like', "%{$search}%");
                  });
            });
        }

        // Penapisan mengikut Status Kelulusan
        if ($request->filled('status_kelulusan')) {
            $query->where('status_kelulusan', $request->status_kelulusan);
        }

        // Penapisan mengikut Sebab Pindah
        if ($request->filled('sebab_pindah')) {
            $query->where('sebab_pindah', $request->sebab_pindah);
        }

        $pindahMilikList = $query->latest()->paginate(15)->withQueryString();
        $pemunyaList = $user->isStaff() ? Pemunya::orderBy('nama')->get() : ($user->pemunya ? collect([$user->pemunya]) : collect());

        return view('eptr.borang-b-index', compact('pindahMilikList', 'pemunyaList'));
    }

    public function createBorangB(Request $request)
    {
        $user = Auth::user();
        if ($user->isStaff()) {
            return redirect()->route('eptr.borang-b.index')->with('error', 'Akses Ditolak: Permohonan Borang B (Pindah Milik Ternakan) hanya boleh dibuat oleh pemohon / penternak. Pegawai pentadbir hanya bertindak menyemak dan meluluskan permohonan.');
        }

        $query = Ternakan::with('pemunya')
            ->whereIn('status', ['Aktif', 'Pawah'])
            ->where('status_kelulusan', 'Diluluskan')
            ->whereDoesntHave('pembatalanTernakan', function ($q) {
                $q->where('status_kelulusan', 'Disahkan');
            })
            ->whereDoesntHave('permitSembelihan', function ($q) {
                $q->where('status_kelulusan', 'Diluluskan');
            });

        if ($user->pemunya) {
            $query->where('pemunya_id', $user->pemunya->id);
        }
        $ternakanList = $query->orderBy('no_tag')->get();

        $selectedTernakan = null;
        if ($request->filled('ternakan_id')) {
            $selectedTernakan = Ternakan::with('pemunya')->find($request->ternakan_id);
            if ($selectedTernakan && $selectedTernakan->isDibatalkanAtauMatiAtauSembelih()) {
                return redirect()->route('eptr.borang-b.index')->with('error', "Akses Ditolak: Ternakan No. Tag {$selectedTernakan->no_tag} telah dibatalkan / mati / disembelih dan tidak boleh dipindah milik.");
            }
        }

        $pemunyaList = Pemunya::orderBy('nama')->get();
        $kelantanData = config('kelantan.jajahan', []);

        return view('eptr.borang-b-create', compact('ternakanList', 'selectedTernakan', 'pemunyaList', 'kelantanData', 'user'));
    }

    public function storeBorangB(Request $request)
    {
        $user = Auth::user();
        if ($user->isStaff()) {
            return redirect()->route('eptr.borang-b.index')->with('error', 'Akses Ditolak: Permohonan Borang B (Pindah Milik Ternakan) hanya boleh dibuat oleh pemohon / penternak.');
        }

        $rules = [
            'ternakan_id' => 'nullable|exists:ternakan,id',
            'ternakan_ids' => 'required_without:ternakan_id|array|min:1',
            'ternakan_ids.*' => 'exists:ternakan,id',
            'jenis_pemunya_baru' => 'required|in:sedia_ada,baru',
            'tarikh_pindah' => 'required|date|before_or_equal:today',
            'sebab_pindah' => 'required|string|max:100',
            'harga_jualan' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
            'perakuan' => 'required|accepted',
            'resit_pembayaran' => $user->isStaff() ? 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048' : 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ];

        if ($request->jenis_pemunya_baru === 'sedia_ada') {
            if (!$request->filled('pemunya_baru_id') && ($request->filled('no_kp_pemunya_baru') || $request->filled('no_kp_semakan'))) {
                $rawKp = $request->input('no_kp_pemunya_baru') ?: $request->input('no_kp_semakan');
                $cleanKp = preg_replace('/[^0-9]/', '', $rawKp);
                $found = Pemunya::where('no_kp', $rawKp)
                    ->orWhere('no_kp', $cleanKp)
                    ->orWhereRaw("REPLACE(REPLACE(no_kp, '-', ''), ' ', '') = ?", [$cleanKp])
                    ->first();
                if ($found) {
                    $request->merge(['pemunya_baru_id' => $found->id]);
                }
            }
            $rules['pemunya_baru_id'] = 'required|exists:pemunya,id';
        } else {
            $rules['nama_pemunya_baru'] = 'required|string|max:255';
            $rules['no_kp_pemunya_baru'] = 'required|string|max:20';
            $rules['no_tel_pemunya_baru'] = 'required|string|max:20';
            $rules['alamat_pemunya_baru'] = 'required|string|max:500';
            $rules['jajahan_pemunya_baru'] = 'required|string|max:50';
            $rules['daerah_pemunya_baru'] = 'nullable|string|max:50';
            $rules['poskod_pemunya_baru'] = 'nullable|string|max:10';
            $rules['lokasi_kandang_baru'] = 'nullable|string|max:255';
        }

        $validated = $request->validate($rules, [
            'perakuan.accepted' => 'Sila tandakan perakuan kebenaran dan kesahihan maklumat pindah milik.',
            'ternakan_ids.required_without' => 'Sila pilih sekurang-kurangnya seekor ternakan untuk dipindah milik.',
            'pemunya_baru_id.required' => 'Sila semak atau pilih penternak penerima / pembeli sedia ada.',
            'nama_pemunya_baru.required' => 'Sila masukkan nama penternak penerima / pemilik baharu.',
            'no_kp_pemunya_baru.required' => 'Sila masukkan No. Kad Pengenalan penternak penerima baharu.',
            'no_tel_pemunya_baru.required' => 'Sila masukkan No. Telefon penternak penerima baharu.',
            'resit_pembayaran.required' => 'Bagi permohonan pertukaran / pemindahan milikan (Borang B), pemohon wajib memuat naik salinan resit pembayaran.',
            'resit_pembayaran.mimes' => 'Fail resit pembayaran mestilah dalam format JPG, PNG, atau PDF.',
            'resit_pembayaran.max' => 'Saiz fail resit pembayaran tidak boleh melebihi 2MB.',
        ]);

        $ternakanIds = $request->input('ternakan_ids', []);
        if (empty($ternakanIds) && $request->filled('ternakan_id')) {
            $ternakanIds = [$request->ternakan_id];
        }
        $ternakanIds = array_values(array_unique(array_filter($ternakanIds)));

        if (empty($ternakanIds)) {
            return back()->with('error', 'Sila pilih sekurang-kurangnya seekor ternakan untuk dipindah milik.')->withInput();
        }

        $ternakanCollection = Ternakan::with('pemunya')->whereIn('id', $ternakanIds)->get();

        foreach ($ternakanCollection as $ternakan) {
            if ($ternakan->isDibatalkanAtauMatiAtauSembelih()) {
                return back()->with('error', "Tindakan Ditolak: Ternakan No. Tag {$ternakan->no_tag} telah dibatalkan / mati / disembelih dan tidak boleh dipindah milik.")->withInput();
            }

            // Pastikan penternak biasa hanya memindahkan ternakan miliknya
            if ($user->pemunya && $ternakan->pemunya_id !== $user->pemunya->id) {
                return back()->with('error', "Akses Ditolak: Anda hanya dibenarkan memohon pindah milik bagi ternakan berdaftar milik anda sendiri (No. Tag {$ternakan->no_tag}).")->withInput();
            }
        }

        // Tentukan pemunya baru
        if ($validated['jenis_pemunya_baru'] === 'sedia_ada') {
            $pemunyaBaruId = $validated['pemunya_baru_id'];
            $pemunyaBaru = Pemunya::findOrFail($pemunyaBaruId);
        } else {
            $pemunyaBaru = Pemunya::firstOrCreate(
                ['no_kp' => $validated['no_kp_pemunya_baru']],
                [
                    'nama' => $validated['nama_pemunya_baru'],
                    'no_telefon' => $validated['no_tel_pemunya_baru'],
                    'alamat' => $validated['alamat_pemunya_baru'],
                    'jajahan' => $validated['jajahan_pemunya_baru'],
                    'daerah' => $validated['daerah_pemunya_baru'] ?? null,
                    'poskod' => $validated['poskod_pemunya_baru'] ?? null,
                    'lokasi_kandang' => $validated['lokasi_kandang_baru'] ?? null,
                    'status' => 'Aktif',
                ]
            );
            $pemunyaBaruId = $pemunyaBaru->id;
        }

        foreach ($ternakanCollection as $ternakan) {
            if ($pemunyaBaruId == $ternakan->pemunya_id) {
                return back()->with('error', "Pemunya baru tidak boleh sama dengan pemunya asal bagi ternakan No. Tag {$ternakan->no_tag}.")->withInput();
            }
        }

        $resitPath = null;
        if ($request->hasFile('resit_pembayaran')) {
            $resitPath = $this->uploadFileSafely($request->file('resit_pembayaran'), 'resit_eptr');
        }

        $createdPindahMilik = [];
        $totalTernakan = count($ternakanCollection);
        $hargaPerEkor = (isset($validated['harga_jualan']) && is_numeric($validated['harga_jualan']) && $totalTernakan > 0)
            ? round((float)$validated['harga_jualan'] / $totalTernakan, 2)
            : null;

        foreach ($ternakanCollection as $ternakan) {
            $pm = PindahMilik::create([
                'ternakan_id' => $ternakan->id,
                'pemunya_asal_id' => $ternakan->pemunya_id,
                'pemunya_baru_id' => $pemunyaBaruId,
                'tarikh_pindah' => $validated['tarikh_pindah'],
                'sebab_pindah' => $validated['sebab_pindah'],
                'harga_jualan' => $hargaPerEkor ?? ($validated['harga_jualan'] ?? null),
                'status_kelulusan' => 'Menunggu',
                'diluluskan_oleh' => null,
                'catatan' => $validated['catatan'] ?? null,
                'resit_pembayaran' => $resitPath,
            ]);
            $createdPindahMilik[] = $pm;
        }

        // Notifikasi kepada pemohon & admin
        \App\Models\UserNotification::send(
            $user->id,
            'Permohonan Pindah Milik Dihantar',
            "Permohonan pertukaran milikan ternakan (Borang B) bagi {$totalTernakan} ekor ternakan telah dihantar dan sedang menunggu kelulusan.",
            'eptr',
            route('eptr.borang-b.index'),
            'fa-solid fa-arrow-right-arrow-left',
            'amber'
        );

        $jajahanTarget = $ternakanCollection[0]->jajahan ?? $user->jajahan;
        $adminJajahanList = User::whereIn('role', ['admin_jajahan', 'admin_eptr_jajahan'])
            ->where(function ($q) use ($jajahanTarget) {
                $q->where('jajahan', $jajahanTarget)
                  ->orWhereNull('jajahan')
                  ->orWhere('jajahan', '');
            })
            ->where('id', '!=', $user->id)
            ->get();

        foreach ($adminJajahanList as $aj) {
            \App\Models\UserNotification::send(
                $aj->id,
                "Permohonan Pindah Milik Menunggu Kelulusan ({$jajahanTarget})",
                "Permohonan Borang B pindah milik {$totalTernakan} ekor ternakan oleh {$user->name} di Jajahan {$jajahanTarget} memerlukan semakan dan kelulusan anda.",
                'eptr',
                route('eptr.borang-b.index'),
                'fa-solid fa-clipboard-check',
                'amber'
            );
        }

        if ($totalTernakan === 1) {
            return redirect()->route('eptr.borang-b.show', $createdPindahMilik[0]->id)->with('success', "Permohonan Pindah Milik Ternakan EPTR (Borang B) bagi No. Tag {$ternakanCollection[0]->no_tag} telah berjaya dihantar dan sedang menunggu kelulusan Admin Jajahan.");
        }

        return redirect()->route('eptr.borang-b.index')->with('success', "Permohonan Pindah Milik Ternakan EPTR (Borang B) bagi {$totalTernakan} ekor ternakan telah berjaya dihantar dan sedang menunggu kelulusan Admin Jajahan.");
    }

    public function showBorangB($id)
    {
        $pindahMilik = PindahMilik::with(['ternakan.pemunya', 'pemunyaAsal', 'pemunyaBaru', 'pelulus'])->findOrFail($id);
        return view('eptr.borang-b-show', compact('pindahMilik'));
    }

    public function luluskanBorangB(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isStaff()) {
            return back()->with('error', 'Hanya Admin Jajahan / Pegawai EPTR yang dibenarkan meluluskan pindah milik ternakan.');
        }

        $pindahMilik = PindahMilik::with(['ternakan', 'pemunyaBaru'])->findOrFail($id);

        if ($pindahMilik->status_kelulusan === 'Diluluskan') {
            return back()->with('info', 'Permohonan pindah milik ini telah pun diluluskan sebelum ini.');
        }

        // Sekatan jajahan bagi Admin Jajahan EPTR
        if ($user->role === 'admin_jajahan' && $user->jajahan && $pindahMilik->ternakan && $pindahMilik->ternakan->jajahan !== $user->jajahan) {
            return back()->with('error', "Akses Ditolak: Anda hanya dibenarkan meluluskan permohonan pindah milik bagi ternakan di jajahan {$user->jajahan}.");
        }

        $pindahMilik->status_kelulusan = 'Diluluskan';
        $pindahMilik->diluluskan_oleh = $user->id;
        $pindahMilik->save();

        // Kemaskini pemilikan pada rekod ternakan
        $ternakan = $pindahMilik->ternakan;
        if ($ternakan) {
            $ternakan->pemunya_id = $pindahMilik->pemunya_baru_id;
            $ternakan->jajahan = $pindahMilik->pemunyaBaru->jajahan;
            if (!empty($pindahMilik->pemunyaBaru->daerah)) {
                $ternakan->daerah = $pindahMilik->pemunyaBaru->daerah;
            }
            if (!empty($pindahMilik->pemunyaBaru->lokasi_kandang)) {
                $ternakan->lokasi_kandang = $pindahMilik->pemunyaBaru->lokasi_kandang;
            }
            $ternakan->catatan = ($ternakan->catatan ? $ternakan->catatan . " | " : "") . "Pindah milik diluluskan kepada {$pindahMilik->pemunyaBaru->nama} pada " . Carbon::now()->format('d/m/Y');
            $ternakan->save();
        }

        // Catat ke Action List
        \App\Models\ActionList::catatAktiviti([
            'tajuk_aktiviti' => "Kelulusan Pindah Milik Ternakan (Tag: " . ($ternakan->no_tag ?? '-') . ")",
            'kategori_aktiviti' => 'Pendaftaran Ternakan (EPTR)',
            'maklumat_aktiviti' => "Meluluskan Borang B (Pindah Milik Ternakan) bagi No. Tag " . ($ternakan->no_tag ?? '-') . " daripada pemunya asal kepada {$pindahMilik->pemunyaBaru->nama}.",
            'jajahan' => $pindahMilik->pemunyaBaru->jajahan ?: ($user->jajahan ?: 'Pasir Puteh'),
            'lokasi' => 'Pejabat JPV Jajahan ' . ($pindahMilik->pemunyaBaru->jajahan ?: 'Pasir Puteh'),
            'status' => 'Selesai',
        ]);

        return redirect()->route('eptr.borang-b.show', $pindahMilik->id)->with('success', "Permohonan Borang B (Pindah Milik Ternakan) telah BERJAYA DILULUSKAN. Hak milik No. Tag {$ternakan->no_tag} telah dipindahkan kepada {$pindahMilik->pemunyaBaru->nama}.");
    }

    public function tolakBorangB(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isStaff()) {
            return back()->with('error', 'Hanya Admin Jajahan / Pegawai EPTR yang dibenarkan menolak permohonan pindah milik.');
        }

        $pindahMilik = PindahMilik::with('ternakan')->findOrFail($id);

        if ($user->role === 'admin_jajahan' && $user->jajahan && $pindahMilik->ternakan && $pindahMilik->ternakan->jajahan !== $user->jajahan) {
            return back()->with('error', "Akses Ditolak: Anda hanya dibenarkan menolak permohonan pindah milik bagi ternakan di jajahan {$user->jajahan}.");
        }

        $pindahMilik->status_kelulusan = 'Ditolak';
        $pindahMilik->diluluskan_oleh = $user->id;
        $pindahMilik->catatan = ($pindahMilik->catatan ? $pindahMilik->catatan . " | " : "") . "Ditolak oleh {$user->nama} pada " . Carbon::now()->format('d/m/Y');
        $pindahMilik->save();

        // Catat ke Action List
        \App\Models\ActionList::catatAktiviti([
            'tajuk_aktiviti' => "Penolakan Pindah Milik Ternakan (ID: #{$pindahMilik->id})",
            'kategori_aktiviti' => 'Pendaftaran Ternakan (EPTR)',
            'maklumat_aktiviti' => "Menolak permohonan Borang B pindah milik bagi ternakan " . ($pindahMilik->ternakan->no_tag ?? "ID #{$pindahMilik->ternakan_id}") . ".",
            'jajahan' => $user->jajahan ?: 'Pasir Puteh',
            'lokasi' => 'Pejabat JPV Jajahan ' . ($user->jajahan ?: 'Pasir Puteh'),
            'status' => 'Selesai',
        ]);

        return redirect()->route('eptr.borang-b.show', $pindahMilik->id)->with('info', 'Permohonan Pindah Milik Ternakan (Borang B) telah DITOLAK.');
    }

    public function cetakBorangB($id)
    {
        $user = Auth::user();
        if (!$user || !$user->isStaff()) {
            abort(403, 'Akses Ditolak: Hanya Admin Jajahan / Pegawai EPTR yang dibenarkan mencetak Notis Rasmi Borang B.');
        }

        $pindahMilik = PindahMilik::with(['ternakan.pemunya', 'pemunyaAsal', 'pemunyaBaru', 'pelulus'])->findOrFail($id);
        return view('eptr.cetak-borang-b', compact('pindahMilik'));
    }

    public function cetakPukalBorangB(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isStaff()) {
            abort(403, 'Akses Ditolak: Hanya Admin Jajahan / Pegawai EPTR yang dibenarkan mencetak Notis Rasmi Borang B.');
        }

        $ids = $request->input('ids', []);
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }

        if (empty($ids)) {
            return back()->with('error', 'Sila pilih sekurang-kurangnya satu rekod Borang B untuk dicetak.');
        }

        $pindahMilikList = PindahMilik::with(['ternakan.pemunya', 'pemunyaAsal', 'pemunyaBaru', 'pelulus'])
            ->whereIn('id', $ids)
            ->latest()
            ->get();

        return view('eptr.cetak-pukal-borang-b', compact('pindahMilikList'));
    }

    // EPTR Borang C: Notis Pembatalan / Kematian / Pindah Keluar
    public function borangCIndex(Request $request)
    {
        $user = Auth::user();
        $query = PembatalanTernakan::with('ternakan.pemunya', 'pengesah');

        if (!$user->isStaff() && $user->pemunya) {
            $query->whereHas('ternakan', function ($q) use ($user) {
                $q->where('pemunya_id', $user->pemunya->id);
            });
        }

        // Penapisan Carian Kata Kunci
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sebab', 'like', "%{$search}%")
                  ->orWhere('no_laporan_polis', 'like', "%{$search}%")
                  ->orWhereHas('ternakan', function ($tq) use ($search) {
                      $tq->where('no_tag', 'like', "%{$search}%")
                         ->orWhereHas('pemunya', function ($pq) use ($search) {
                             $pq->where('nama', 'like', "%{$search}%")
                                ->orWhere('no_kp', 'like', "%{$search}%");
                         });
                  });
            });
        }

        // Penapisan mengikut Pemunya ID
        if ($request->filled('pemunya_id')) {
            $query->whereHas('ternakan', function ($q) use ($request) {
                $q->where('pemunya_id', $request->pemunya_id);
            });
        }

        // Penapisan mengikut No Kad Pengenalan
        if ($request->filled('no_kp')) {
            $noKp = $request->no_kp;
            $query->whereHas('ternakan.pemunya', function ($q) use ($noKp) {
                $q->where('no_kp', 'like', "%{$noKp}%");
            });
        }

        // Penapisan mengikut Jenis Pembatalan
        if ($request->filled('jenis_batal')) {
            $query->where('jenis_batal', $request->jenis_batal);
        }

        // Penapisan mengikut Status Kelulusan
        if ($request->filled('status_kelulusan')) {
            $query->where('status_kelulusan', $request->status_kelulusan);
        }

        $pembatalanList = $query->latest()->paginate(15)->withQueryString();
        $pemunyaList = $user->isStaff() ? Pemunya::orderBy('nama')->get() : ($user->pemunya ? collect([$user->pemunya]) : collect());

        return view('eptr.borang-c-index', compact('pembatalanList', 'pemunyaList'));
    }

    public function createBorangC(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->isPurePengarah()) {
            abort(403, 'Akses Ditolak: Pengarah Perkhidmatan Veterinar Negeri tidak dibenarkan membuat permohonan Pembatalan / Kematian (Borang C).');
        }

        $query = Ternakan::with('pemunya')
            ->whereIn('status', ['Aktif', 'Pawah'])
            ->where('status_kelulusan', 'Diluluskan')
            ->whereDoesntHave('pembatalanTernakan', function ($q) {
                $q->where('status_kelulusan', 'Disahkan');
            })
            ->whereDoesntHave('permitSembelihan', function ($q) {
                $q->where('status_kelulusan', 'Diluluskan');
            });

        if (!$user->isStaff() && $user->pemunya) {
            $query->where('pemunya_id', $user->pemunya->id);
        }
        $ternakanList = $query->orderBy('no_tag')->get();
        
        $selectedTernakan = null;
        if ($request->filled('ternakan_id')) {
            $selectedTernakan = Ternakan::find($request->ternakan_id);
            if ($selectedTernakan && $selectedTernakan->isDibatalkanAtauMatiAtauSembelih()) {
                return redirect()->route('eptr.borang-c.index')->with('error', "Akses Ditolak: Ternakan No. Tag {$selectedTernakan->no_tag} telah pun direkodkan dibatalkan / mati / disembelih.");
            }
        }

        return view('eptr.borang-c-create', compact('ternakanList', 'selectedTernakan'));
    }

    public function storeBorangC(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->isPurePengarah()) {
            abort(403, 'Akses Ditolak: Pengarah Perkhidmatan Veterinar Negeri tidak dibenarkan membuat permohonan Pembatalan / Kematian (Borang C).');
        }
        $validated = $request->validate([
            'ternakan_id' => 'required|exists:ternakan,id',
            'jenis_batal' => 'required|in:Mati,Pindah Keluar,Kecurian,Pelupusan,Sembelihan,Lain-lain',
            'tarikh_peristiwa' => 'required|date',
            'sebab' => 'required|string',
            'no_laporan_polis' => 'nullable|string',
            'destinasi_pindah_keluar' => 'nullable|string',
            'dokumen_sokongan' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'catatan' => 'nullable|string',
        ]);

        $ternakan = Ternakan::findOrFail($validated['ternakan_id']);
        if ($ternakan->isDibatalkanAtauMatiAtauSembelih()) {
            return back()->with('error', "Tindakan Ditolak: Ternakan No. Tag {$ternakan->no_tag} telah pun dibatalkan / mati / disembelih dan tidak boleh dihantar notis pembatalan lagi.")->withInput();
        }

        // Muat naik fail dokumen sokongan / resit jika ada
        $dokumenPath = null;
        if ($request->hasFile('dokumen_sokongan')) {
            $dokumenPath = $this->uploadFileSafely($request->file('dokumen_sokongan'), 'dokumen_borang_c');
        }

        $pembatalan = PembatalanTernakan::create([
            'ternakan_id' => $validated['ternakan_id'],
            'jenis_batal' => $validated['jenis_batal'],
            'tarikh_peristiwa' => $validated['tarikh_peristiwa'],
            'sebab' => $validated['sebab'],
            'no_laporan_polis' => $validated['no_laporan_polis'] ?? null,
            'destinasi_pindah_keluar' => $validated['destinasi_pindah_keluar'] ?? null,
            'dokumen_sokongan' => $dokumenPath,
            'status_kelulusan' => Auth::user()->isStaff() ? 'Disahkan' : 'Menunggu',
            'disahkan_oleh' => Auth::user()->isStaff() ? Auth::id() : null,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        // Kemaskini status ternakan
        if (Auth::user()->isStaff()) {
            $ternakan->status = $validated['jenis_batal'] === 'Mati' ? 'Mati' : ($validated['jenis_batal'] === 'Kecurian' ? 'Batal' : 'Pindah');
            $ternakan->save();
        } else {
            \App\Models\UserNotification::send(
                $user->id,
                'Notis Pembatalan Dihantar',
                "Borang C bagi ternakan No. Tag {$ternakan->no_tag} ({$validated['jenis_batal']}) telah dihantar untuk pengesahan admin.",
                'eptr',
                route('eptr.borang-c.index'),
                'fa-solid fa-file-excel',
                'rose'
            );

            $adminJajahanList = User::whereIn('role', ['admin_jajahan', 'admin_eptr_jajahan'])
                ->where(function ($q) use ($ternakan) {
                    $q->where('jajahan', $ternakan->jajahan)
                      ->orWhereNull('jajahan')
                      ->orWhere('jajahan', '');
                })
                ->where('id', '!=', $user->id)
                ->get();

            foreach ($adminJajahanList as $aj) {
                \App\Models\UserNotification::send(
                    $aj->id,
                    "Notis Pembatalan Ternakan ({$ternakan->jajahan})",
                    "Notis Borang C ({$validated['jenis_batal']}) bagi ternakan No. Tag {$ternakan->no_tag} telah dihantar oleh {$user->name} untuk pengesahan anda.",
                    'eptr',
                    route('eptr.borang-c.index'),
                    'fa-solid fa-clipboard-check',
                    'rose'
                );
            }
        }

        return redirect()->route('eptr.borang-c.index')->with('success', 'Permohonan Notis Pembatalan EPTR (Borang C) telah berjaya dihantar.');
    }

    /**
     * Pengesahan / Kelulusan Notis Borang C oleh Admin Jajahan
     */
    public function luluskanBorangC(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isStaff()) {
            return back()->with('error', 'Hanya Admin Jajahan / Pegawai EPTR yang dibenarkan mengesahkan Notis Pembatalan EPTR.');
        }

        $pembatalan = PembatalanTernakan::findOrFail($id);
        $pembatalan->status_kelulusan = 'Disahkan';
        $pembatalan->disahkan_oleh = $user->id;
        $pembatalan->catatan = ($pembatalan->catatan ? $pembatalan->catatan . " | " : "") . "Disahkan oleh {$user->nama} pada " . Carbon::now()->format('d/m/Y H:i');
        $pembatalan->save();

        // Kemaskini status ternakan berkaitan
        $ternakan = Ternakan::find($pembatalan->ternakan_id);
        if ($ternakan) {
            $ternakan->status = $pembatalan->jenis_batal === 'Mati' ? 'Mati' : ($pembatalan->jenis_batal === 'Kecurian' ? 'Batal' : 'Pindah');
            $ternakan->save();
        }

        // Catat ke Action List
        \App\Models\ActionList::catatAktiviti([
            'tajuk_aktiviti' => "Pengesahan Notis Pembatalan Ternakan (Tag: " . ($ternakan->no_tag ?? '-') . ")",
            'kategori_aktiviti' => 'Pendaftaran Ternakan (EPTR)',
            'maklumat_aktiviti' => "Mengesahkan Notis Borang C (Pembatalan/Kematian/Kecurian) bagi No. Tag " . ($ternakan->no_tag ?? '-') . " (Sebab: {$pembatalan->jenis_batal}).",
            'jajahan' => $pembatalan->jajahan ?: ($user->jajahan ?: 'Pasir Puteh'),
            'lokasi' => 'Pejabat JPV Jajahan ' . ($pembatalan->jajahan ?: ($user->jajahan ?: 'Pasir Puteh')),
            'status' => 'Selesai',
        ]);

        return redirect()->route('eptr.borang-c.index')->with('success', "Notis Pembatalan EPTR (Borang C) untuk Ternakan No. Tag " . ($ternakan->no_tag ?? '') . " telah berjaya DISAHKAN.");
    }

    /**
     * Penolakan Notis Borang C oleh Admin Jajahan
     */
    public function tolakBorangC(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isStaff()) {
            return back()->with('error', 'Hanya Admin Jajahan / Pegawai EPTR yang dibenarkan menolak Notis Pembatalan EPTR.');
        }

        $pembatalan = PembatalanTernakan::findOrFail($id);
        $pembatalan->status_kelulusan = 'Ditolak';
        $pembatalan->disahkan_oleh = $user->id;
        $pembatalan->catatan = ($pembatalan->catatan ? $pembatalan->catatan . " | " : "") . "Ditolak oleh {$user->nama} pada " . Carbon::now()->format('d/m/Y H:i');
        $pembatalan->save();

        // Catat ke Action List
        \App\Models\ActionList::catatAktiviti([
            'tajuk_aktiviti' => "Penolakan Notis Pembatalan Ternakan (ID: #{$pembatalan->id})",
            'kategori_aktiviti' => 'Pendaftaran Ternakan (EPTR)',
            'maklumat_aktiviti' => "Menolak Notis Pembatalan Ternakan Borang C (ID: #{$pembatalan->id}).",
            'jajahan' => $pembatalan->jajahan ?: ($user->jajahan ?: 'Pasir Puteh'),
            'lokasi' => 'Pejabat JPV Jajahan ' . ($pembatalan->jajahan ?: ($user->jajahan ?: 'Pasir Puteh')),
            'status' => 'Selesai',
        ]);

        return redirect()->route('eptr.borang-c.index')->with('info', "Notis Pembatalan EPTR (Borang C) telah DITOLAK.");
    }

    // Cetak Borang C Pembatalan (Format Asal) - Hanya Admin Jajahan / Staf
    public function cetakBorangC($id)
    {
        $user = Auth::user();
        if (!$user || !$user->isStaff()) {
            abort(403, 'Akses Ditolak: Hanya Admin Jajahan / Pegawai EPTR yang dibenarkan mencetak Notis Pembatalan EPTR.');
        }

        $pembatalan = PembatalanTernakan::with('ternakan.pemunya', 'pengesah')->findOrFail($id);
        return view('eptr.cetak-borang-c', compact('pembatalan'));
    }

    // Cetak Pukal Borang C Pembatalan - Hanya Admin Jajahan / Staf
    public function cetakPukalBorangC(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isStaff()) {
            abort(403, 'Akses Ditolak: Hanya Admin Jajahan / Pegawai EPTR yang dibenarkan mencetak Notis Pembatalan EPTR.');
        }

        $ids = $request->input('ids', []);
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }

        if (empty($ids)) {
            return back()->with('error', 'Sila pilih sekurang-kurangnya satu rekod Notis Borang C untuk dicetak.');
        }

        $pembatalanList = PembatalanTernakan::with('ternakan.pemunya', 'pengesah')
            ->whereIn('id', $ids)
            ->latest()
            ->get();

        return view('eptr.cetak-pukal-borang-c', compact('pembatalanList'));
    }

    // EPTR Borang D: Permit Sembelihan
    public function borangDIndex(Request $request)
    {
        $user = Auth::user();
        $query = PermitSembelihan::with('pemunya', 'ternakan', 'pelulus');

        if (!$user->isStaff() && $user->pemunya) {
            $query->where('pemunya_id', $user->pemunya->id);
        }

        // Penapisan Carian Kata Kunci
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_permit', 'like', "%{$search}%")
                  ->orWhere('tujuan_sembelih', 'like', "%{$search}%")
                  ->orWhere('lokasi_sembelih', 'like', "%{$search}%")
                  ->orWhereHas('pemunya', function ($pq) use ($search) {
                      $pq->where('nama', 'like', "%{$search}%")
                         ->orWhere('no_kp', 'like', "%{$search}%");
                  })
                  ->orWhereHas('ternakan', function ($tq) use ($search) {
                      $tq->where('no_tag', 'like', "%{$search}%");
                  });
            });
        }

        // Penapisan mengikut Pemunya ID
        if ($request->filled('pemunya_id')) {
            $query->where('pemunya_id', $request->pemunya_id);
        }

        // Penapisan mengikut No Kad Pengenalan
        if ($request->filled('no_kp')) {
            $noKp = $request->no_kp;
            $query->whereHas('pemunya', function ($q) use ($noKp) {
                $q->where('no_kp', 'like', "%{$noKp}%");
            });
        }

        // Penapisan mengikut Status Kelulusan
        if ($request->filled('status_kelulusan')) {
            $query->where('status_kelulusan', $request->status_kelulusan);
        }

        $permitList = $query->latest()->paginate(15)->withQueryString();
        $pemunyaList = $user->isStaff() ? Pemunya::orderBy('nama')->get() : ($user->pemunya ? collect([$user->pemunya]) : collect());

        return view('eptr.borang-d-index', compact('permitList', 'pemunyaList'));
    }

    public function createBorangD(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->isPurePengarah()) {
            abort(403, 'Akses Ditolak: Pengarah Perkhidmatan Veterinar Negeri tidak dibenarkan membuat permohonan Permit & SKV Sembelihan (Borang D).');
        }

        // Senarai pemunya (untuk staf)
        $pemunyaList = $user->isStaff() ? Pemunya::orderBy('nama')->get() : ($user->pemunya ? collect([$user->pemunya]) : collect());

        // Ternakan aktif yang layak untuk permit sembelihan
        $query = Ternakan::with('pemunya')
            ->whereIn('status', ['Aktif', 'Pawah'])
            ->where('status_kelulusan', 'Diluluskan')
            ->whereDoesntHave('pembatalanTernakan', function ($q) {
                $q->where('status_kelulusan', 'Disahkan');
            })
            ->whereDoesntHave('permitSembelihan', function ($q) {
                $q->where('status_kelulusan', 'Diluluskan');
            });

        if (!$user->isStaff() && $user->pemunya) {
            $query->where('pemunya_id', $user->pemunya->id);
        }
        $ternakanList = $query->orderBy('no_tag')->get();
        
        $selectedTernakan = null;
        if ($request->filled('ternakan_id')) {
            $selectedTernakan = Ternakan::with('pemunya')->find($request->ternakan_id);
            if ($selectedTernakan && $selectedTernakan->isDibatalkanAtauMatiAtauSembelih()) {
                return redirect()->route('eptr.borang-d.index')->with('error', "Akses Ditolak: Ternakan No. Tag {$selectedTernakan->no_tag} telah dibatalkan / mati / disembelih dan tidak boleh memohon permit sembelihan.");
            }
        }

        $kelantanData = config('kelantan.jajahan', []);

        return view('eptr.borang-d-create', compact('ternakanList', 'selectedTernakan', 'pemunyaList', 'kelantanData', 'user'));
    }

    public function storeBorangD(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->isPurePengarah()) {
            abort(403, 'Akses Ditolak: Pengarah Perkhidmatan Veterinar Negeri tidak dibenarkan membuat permohonan Permit & SKV Sembelihan (Borang D).');
        }

        // Semak ternakan_id jika dihantar secara terus (single ternakan / backwards compatibility)
        if ($request->filled('ternakan_id')) {
            $tObj = Ternakan::find($request->ternakan_id);
            if ($tObj) {
                if ($tObj->isDibatalkanAtauMatiAtauSembelih()) {
                    return back()->with('error', "Tindakan Ditolak: Ternakan No. Tag {$tObj->no_tag} telah dibatalkan / mati / disembelih dan tidak boleh memohon permit sembelihan.")->withInput();
                }
                if (!$request->filled('pemunya_id')) {
                    $request->merge(['pemunya_id' => $tObj->pemunya_id]);
                }
                if (!$request->filled('jenis_ternakan')) {
                    $request->merge(['jenis_ternakan' => $tObj->jenis_ternakan ?? 'Lembu']);
                }
            }
        }

        $tarikhSembelih = $request->input('tarikh_sembelih', date('Y-m-d'));
        $tujuanSembelih = $request->input('tujuan_sembelih', '');

        $aidiladhaDates = [
            '2024-06-17' => ['name' => 'Hari Raya Pertama', 'hijri' => '10 Zulhijjah', 'percuma' => true],
            '2024-06-18' => ['name' => 'Hari Raya Kedua', 'hijri' => '11 Zulhijjah', 'percuma' => true],
            '2024-06-19' => ['name' => 'Hari Raya Ketiga', 'hijri' => '12 Zulhijjah', 'percuma' => false],
            '2024-06-20' => ['name' => 'Hari Raya Keempat', 'hijri' => '13 Zulhijjah', 'percuma' => false],
            '2025-06-06' => ['name' => 'Hari Raya Pertama', 'hijri' => '10 Zulhijjah', 'percuma' => true],
            '2025-06-07' => ['name' => 'Hari Raya Kedua', 'hijri' => '11 Zulhijjah', 'percuma' => true],
            '2025-06-08' => ['name' => 'Hari Raya Ketiga', 'hijri' => '12 Zulhijjah', 'percuma' => false],
            '2025-06-09' => ['name' => 'Hari Raya Keempat', 'hijri' => '13 Zulhijjah', 'percuma' => false],
            '2026-05-27' => ['name' => 'Hari Raya Pertama', 'hijri' => '10 Zulhijjah', 'percuma' => true],
            '2026-05-28' => ['name' => 'Hari Raya Kedua', 'hijri' => '11 Zulhijjah', 'percuma' => true],
            '2026-05-29' => ['name' => 'Hari Raya Ketiga', 'hijri' => '12 Zulhijjah', 'percuma' => false],
            '2026-05-30' => ['name' => 'Hari Raya Keempat', 'hijri' => '13 Zulhijjah', 'percuma' => false],
            '2027-05-16' => ['name' => 'Hari Raya Pertama', 'hijri' => '10 Zulhijjah', 'percuma' => true],
            '2027-05-17' => ['name' => 'Hari Raya Kedua', 'hijri' => '11 Zulhijjah', 'percuma' => true],
            '2027-05-18' => ['name' => 'Hari Raya Ketiga', 'hijri' => '12 Zulhijjah', 'percuma' => false],
            '2027-05-19' => ['name' => 'Hari Raya Keempat', 'hijri' => '13 Zulhijjah', 'percuma' => false],
            '2028-05-05' => ['name' => 'Hari Raya Pertama', 'hijri' => '10 Zulhijjah', 'percuma' => true],
            '2028-05-06' => ['name' => 'Hari Raya Kedua', 'hijri' => '11 Zulhijjah', 'percuma' => true],
            '2028-05-07' => ['name' => 'Hari Raya Ketiga', 'hijri' => '12 Zulhijjah', 'percuma' => false],
            '2028-05-08' => ['name' => 'Hari Raya Keempat', 'hijri' => '13 Zulhijjah', 'percuma' => false],
        ];

        $tujuanSembelih = $request->input('tujuan_sembelih', '');
        $rawItems = $request->input('items', []);
        
        // Cari tarikh paling awal daripada baris jadual ternakan
        $allRowDates = [];
        if (is_array($rawItems)) {
            foreach ($rawItems as $it) {
                if (!empty($it['tarikh_sembelihan'])) {
                    $allRowDates[] = $it['tarikh_sembelihan'];
                }
            }
        }
        sort($allRowDates);
        $tarikhSembelih = !empty($allRowDates[0]) ? $allRowDates[0] : $request->input('tarikh_sembelih', date('Y-m-d'));

        $detectedInfo = $aidiladhaDates[$tarikhSembelih] ?? null;
        $detectedHariKorban = $detectedInfo['name'] ?? null;

        // Semak jika mana-mana baris jatuh pada Aidiladha
        $hasAnyAidiladhaRow = false;
        $hasEligibleRow = false;
        foreach ($allRowDates as $d) {
            if (isset($aidiladhaDates[$d])) {
                $hasAnyAidiladhaRow = true;
                if ($aidiladhaDates[$d]['percuma']) {
                    $hasEligibleRow = true;
                }
            }
        }

        $isMusimKorban = $request->boolean('is_musim_korban') 
            || !empty($detectedHariKorban) 
            || $hasAnyAidiladhaRow
            || strtolower($tujuanSembelih) === 'ibadah korban'
            || !empty($request->input('hari_korban_percuma'));

        $isEligiblePercuma = false;
        if ($hasEligibleRow || ($detectedInfo && $detectedInfo['percuma'] === true)) {
            $isEligiblePercuma = true;
        } elseif (!$hasAnyAidiladhaRow && strtolower($tujuanSembelih) === 'ibadah korban') {
            $isEligiblePercuma = true;
        }

        $maxRows = $isMusimKorban ? 10 : 7;
        $isStaff = $user->isStaff();

        $validated = $request->validate([
            'pemunya_id' => 'required|exists:pemunya,id',
            'jenis_ternakan' => 'required|string',
            'tujuan_sembelih' => 'required|string|max:100',
            'is_musim_korban' => 'nullable|boolean',
            'hari_korban_percuma' => 'nullable|string|max:50',
            'tarikh_sembelih' => 'nullable|date|after_or_equal:today',
            'resit_pembayaran' => $isStaff ? 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048' : 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'no_kenderaan' => 'nullable|string|max:50',
            'lokasi_sembelih' => 'nullable|string|max:255',
            'nama_premis_sembelih' => 'nullable|string|max:255',
            'alamat_premis_sembelih' => 'nullable|string|max:255',
            'alamat_1' => 'nullable|string|max:255',
            'kuantiti_karkas_1' => 'nullable|string|max:100',
            'alamat_2' => 'nullable|string|max:255',
            'kuantiti_karkas_2' => 'nullable|string|max:100',
            'alamat_3' => 'nullable|string|max:255',
            'kuantiti_karkas_3' => 'nullable|string|max:100',
            'catatan' => 'nullable|string',
            'items' => 'nullable|array|max:' . $maxRows,
            'items.*.jantina' => 'nullable|string',
            'items.*.no_id_ternakan' => 'nullable|string',
            'items.*.no_siri_kad_pendaftaran' => 'nullable|string',
            'items.*.tarikh_sembelihan' => 'nullable|date|after_or_equal:today',
            'items.*.hari_sembelihan_korban' => 'nullable|string|max:50',
            'items.*.tempat_sembelihan' => 'nullable|string',
            'items.*.no_kn_haiwan_16' => 'nullable|string',
            'items.*.kuantiti_karkas' => 'nullable|string',
            'items.*.ternakan_id' => 'nullable|exists:ternakan,id',
            'ternakan_id' => 'nullable|exists:ternakan,id',
        ], [
            'items.max' => "Borang SKV Sembelih hanya membenarkan maksimum {$maxRows} baris ternakan sahaja" . ($isMusimKorban ? ' bagi musim Hari Raya Korban.' : ' bagi hari biasa.'),
            'tarikh_sembelih.after_or_equal' => 'Tarikh sembelihan mestilah tarikh hari ini atau tarikh akan datang.',
            'items.*.tarikh_sembelihan.after_or_equal' => 'Tarikh sembelihan pada jadual ternakan mestilah tarikh hari ini atau tarikh akan datang.',
            'pemunya_id.required' => 'Sila pilih pemunya / penternak ternakan.',
            'resit_pembayaran.required' => 'Bagi permohonan yang ada pembayaran fi, pemohon wajib memuat naik salinan resit pembayaran.',
            'resit_pembayaran.mimes' => 'Fail resit pembayaran mestilah dalam format JPG, PNG, atau PDF.',
            'resit_pembayaran.max' => 'Saiz fail resit pembayaran tidak boleh melebihi 2MB.',
        ]);

        $pemunya = Pemunya::findOrFail($validated['pemunya_id']);

        // Pastikan penternak biasa hanya memohon untuk dirinya sendiri
        if (!$user->isStaff()) {
            $isOwner = ($pemunya->user_id === $user->id) 
                || ($user->pemunya && $user->pemunya->id === $pemunya->id) 
                || (!empty($user->ic_number) && $pemunya->no_kp === $user->ic_number);
            if (!$isOwner) {
                return back()->with('error', 'Akses Ditolak: Anda hanya dibenarkan memohon permit sembelihan bagi diri anda sendiri.')->withInput();
            }
        }

        // Proses Senarai Ternakan SKV (Maksimum 7 baris biasa / 10 baris musim korban)
        $itemsProcessed = [];
        $firstTernakanId = $request->ternakan_id;
        $allTernakanIds = [];

        // Hari Percuma hanya diberikan jika isEligiblePercuma
        $hariKorbanPercuma = null;
        if ($isMusimKorban && $isEligiblePercuma) {
            $hariKorbanPercuma = $validated['hari_korban_percuma'] ?? ($detectedHariKorban ?: 'Hari Raya Pertama');
            if (!in_array($hariKorbanPercuma, ['Hari Raya Pertama', 'Hari Raya Kedua'])) {
                $hariKorbanPercuma = 'Hari Raya Pertama';
            }
        }

        if (!empty($validated['items']) && is_array($validated['items'])) {
            $filteredItems = array_filter($validated['items'], function ($item) {
                return !empty($item['no_id_ternakan']) || !empty($item['ternakan_id']);
            });

            if (count($filteredItems) > $maxRows) {
                return back()->with('error', "Bilangan ternakan melebihi had yang dibenarkan (Maksimum {$maxRows} baris). Sila kurangkan bilangan baris.")->withInput();
            }

            $counter = 1;
            foreach ($filteredItems as $item) {
                $tId = $item['ternakan_id'] ?? null;
                $tObj = $tId ? Ternakan::find($tId) : null;

                if ($tObj && $tObj->isDibatalkanAtauMatiAtauSembelih()) {
                    return back()->with('error', "Tindakan Ditolak: Ternakan No. Tag {$tObj->no_tag} telah dibatalkan / mati / disembelih.")->withInput();
                }

                if ($tId) {
                    $allTernakanIds[] = $tId;
                    if (!$firstTernakanId) {
                        $firstTernakanId = $tId;
                    }
                }

                $rowDate = !empty($item['tarikh_sembelihan']) ? $item['tarikh_sembelihan'] : $tarikhSembelih;
                $detectedRowHari = $aidiladhaDates[$rowDate]['name'] ?? null;

                $itemsProcessed[] = [
                    'bil' => $counter++,
                    'ternakan_id' => $tId,
                    'jantina' => $tObj ? ($tObj->jantina === 'Jantan' ? 'J' : 'B') : (!empty($item['jantina']) ? (in_array(strtoupper($item['jantina']), ['J', 'JANTAN']) ? 'J' : 'B') : 'J'),
                    'no_id_ternakan' => (!empty($item['no_id_ternakan'])) ? $item['no_id_ternakan'] : ($tObj ? ($tObj->no_tag ?? 'ID-' . $tObj->id) : '-'),
                    'no_siri_kad_pendaftaran' => $tObj ? ($tObj->no_siri_kad_kuning ?? '-') : (!empty($item['no_siri_kad_pendaftaran']) ? $item['no_siri_kad_pendaftaran'] : '-'),
                    'tarikh_sembelihan' => $rowDate,
                    'hari_sembelihan_korban' => !empty($item['hari_sembelihan_korban']) ? $item['hari_sembelihan_korban'] : ($detectedRowHari ?: ($isMusimKorban ? ($hariKorbanPercuma ?? 'Hari Raya Pertama') : null)),
                    'tempat_sembelihan' => (!empty($item['tempat_sembelihan'])) ? $item['tempat_sembelihan'] : ($validated['lokasi_sembelih'] ?? ($validated['nama_premis_sembelih'] ?? 'Rumah Sembelih ' . ($pemunya->jajahan ?? 'Kota Bharu'))),
                    'no_kn_haiwan_16' => (!empty($item['no_kn_haiwan_16'])) ? $item['no_kn_haiwan_16'] : ('16/' . date('Y') . '/' . rand(100, 999)),
                    'kuantiti_karkas' => (!empty($item['kuantiti_karkas'])) ? $item['kuantiti_karkas'] : '1 Ekor',
                ];
            }
        }

        // Jika tiada item grid tetapi ada ternakan_id tunggal
        if (empty($itemsProcessed) && $firstTernakanId) {
            $tObj = Ternakan::findOrFail($firstTernakanId);
            if ($tObj->isDibatalkanAtauMatiAtauSembelih()) {
                return back()->with('error', "Tindakan Ditolak: Ternakan No. Tag {$tObj->no_tag} telah dibatalkan / mati / disembelih.")->withInput();
            }
            $allTernakanIds[] = $tObj->id;
            $itemsProcessed[] = [
                'bil' => 1,
                'ternakan_id' => $tObj->id,
                'jantina' => $tObj->jantina === 'Jantan' ? 'J' : 'B',
                'no_id_ternakan' => $tObj->no_tag ?? 'ID-' . $tObj->id,
                'no_siri_kad_pendaftaran' => $tObj->no_siri_kad_kuning ?? '-',
                'tarikh_sembelihan' => $tarikhSembelih,
                'hari_sembelihan_korban' => $isMusimKorban ? ($hariKorbanPercuma ?? 'Hari Raya Pertama') : null,
                'tempat_sembelihan' => $validated['lokasi_sembelih'] ?? ($validated['nama_premis_sembelih'] ?? 'Rumah Sembelih ' . ($pemunya->jajahan ?? 'Kota Bharu')),
                'no_kn_haiwan_16' => '16/' . date('Y') . '/' . rand(100, 999),
                'kuantiti_karkas' => '1 Ekor',
            ];
        }

        if (empty($itemsProcessed)) {
            return back()->with('error', 'Sila isi sekurang-kurangnya satu baris maklumat ternakan untuk disembelih.')->withInput();
        }

        // Pengiraan tempoh sah laku tepat 7 hari
        $effectiveTarikhSembelih = $validated['tarikh_sembelih'] ?? $tarikhSembelih ?? date('Y-m-d');
        $tarikhMula = Carbon::parse($effectiveTarikhSembelih)->startOfDay();
        $tarikhTamat = (clone $tarikhMula)->addDays(6)->endOfDay(); // 7 hari sah laku termasuk hari permulaan

        // Kod singkatan Jajahan
        $jajahanRaw = $pemunya->jajahan ?? 'Kota Bharu';
        $kodJajahan = match (strtolower(trim($jajahanRaw))) {
            'kota bharu' => 'KB',
            'pasir mas' => 'PM',
            'tumpat' => 'T',
            'bachok' => 'B',
            'pasir puteh' => 'PP',
            'machang' => 'M',
            'tanah merah' => 'TM',
            'jeli' => 'J',
            'kuala krai' => 'KK',
            'gua musang' => 'GM',
            default => strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $jajahanRaw) ?: 'KB', 0, 2)),
        };

        $randomNum = rand(1000, 9999);
        $noPermit = 'PS-' . $kodJajahan . '-' . date('Y') . '-' . $randomNum;
        $noRujukanSkv = 'PPVJ' . $kodJajahan . '.600-3(' . rand(10, 99) . ')';
        $noRujukanKarkas = 'PPVJ' . $kodJajahan . '.600-3/1/1/H(' . rand(10, 99) . ')';

        $isStaff = $user->isStaff();
        $statusKelulusan = $isStaff ? 'Diluluskan' : 'Menunggu';
        $diluluskanOleh = $isStaff ? $user->id : null;

        // Muat naik fail resit pembayaran jika disertakan / wajib bagi pemohon
        $resitPath = null;
        if ($request->hasFile('resit_pembayaran')) {
            $resitPath = $this->uploadFileSafely($request->file('resit_pembayaran'), 'resit_eptr');
        }

        $permit = PermitSembelihan::create([
            'no_permit' => $noPermit,
            'no_rujukan_skv' => $noRujukanSkv,
            'no_rujukan_karkas' => $noRujukanKarkas,
            'pemunya_id' => $pemunya->id,
            'jenis_ternakan' => $validated['jenis_ternakan'],
            'ternakan_id' => $firstTernakanId,
            'tujuan_sembelih' => $validated['tujuan_sembelih'],
            'is_musim_korban' => $isMusimKorban,
            'hari_korban_percuma' => $isMusimKorban ? $hariKorbanPercuma : null,
            'tarikh_sembelih' => $effectiveTarikhSembelih,
            'tarikh_mula' => $tarikhMula->toDateString(),
            'tarikh_tamat' => $tarikhTamat->toDateString(),
            'no_kenderaan' => $validated['no_kenderaan'] ?? null,
            'lokasi_sembelih' => $validated['lokasi_sembelih'] ?? ($validated['alamat_premis_sembelih'] ?? 'Rumah Sembelih ' . ($pemunya->jajahan ?? 'Kota Bharu')),
            'nama_premis_sembelih' => $validated['nama_premis_sembelih'] ?? 'Rumah Penyembelihan Berlesen',
            'alamat_premis_sembelih' => $validated['alamat_premis_sembelih'] ?? ($validated['lokasi_sembelih'] ?? 'Jajahan ' . ($pemunya->jajahan ?? 'Kota Bharu')),
            'alamat_1' => $validated['alamat_1'] ?? null,
            'kuantiti_karkas_1' => $validated['kuantiti_karkas_1'] ?? null,
            'alamat_2' => $validated['alamat_2'] ?? null,
            'kuantiti_karkas_2' => $validated['kuantiti_karkas_2'] ?? null,
            'alamat_3' => $validated['alamat_3'] ?? null,
            'kuantiti_karkas_3' => $validated['kuantiti_karkas_3'] ?? null,
            'senarai_ternakan' => $itemsProcessed,
            'no_resit_bayaran' => 'RES-SKV-' . date('Y') . '-' . rand(1000, 9999),
            'resit_pembayaran' => $resitPath,
            'kadar_bayaran' => 10.00, // Bayaran tetap RM 10.00 untuk 1 set SKV Sembelih
            'status_kelulusan' => $statusKelulusan,
            'diluluskan_oleh' => $diluluskanOleh,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        // Jika didaftar oleh staf / diluluskan terus, kemaskini status semua ternakan terlibat
        if ($isStaff) {
            foreach ($allTernakanIds as $tid) {
                $tObj = Ternakan::find($tid);
                if ($tObj) {
                    $tObj->status = 'Sembelih';
                    $tObj->save();
                }
            }
        } else {
            \App\Models\UserNotification::send(
                $user->id,
                'Permohonan Permit Sembelih Dihantar',
                "Permohonan Permit Sembelihan & SKV (Borang D) bagi No. Permit {$permit->no_permit} telah dihantar untuk kelulusan admin.",
                'eptr',
                route('eptr.borang-d.show', $permit->id),
                'fa-solid fa-file-lines',
                'amber'
            );

            $adminJajahanList = User::whereIn('role', ['admin_jajahan', 'admin_eptr_jajahan'])
                ->where(function ($q) use ($pemunya) {
                    $q->where('jajahan', $pemunya->jajahan)
                      ->orWhereNull('jajahan')
                      ->orWhere('jajahan', '');
                })
                ->where('id', '!=', $user->id)
                ->get();

            foreach ($adminJajahanList as $aj) {
                \App\Models\UserNotification::send(
                    $aj->id,
                    "Permohonan Permit Sembelih ({$pemunya->jajahan})",
                    "Permohonan Permit Sembelihan & SKV ({$permit->no_permit}) telah dihantar oleh {$pemunya->nama} untuk kelulusan anda.",
                    'eptr',
                    route('eptr.borang-d.show', $permit->id),
                    'fa-solid fa-clipboard-check',
                    'amber'
                );
            }
        }

        return redirect()->route('eptr.borang-d.show', $permit->id)->with('success', "Permohonan Borang D dan 1 Set SKV Sembelih (Bayaran RM 10.00, Sah Laku 7 Hari) telah berjaya dijana dengan No. Permit: {$permit->no_permit}.");
    }

    /**
     * Kelulusan Permit Sembelihan & SKV Sembelih (Borang D) oleh Admin Jajahan
     */
    public function luluskanBorangD(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isStaff()) {
            return back()->with('error', 'Hanya Admin Jajahan / Pegawai EPTR yang dibenarkan meluluskan Permit Sembelihan EPTR.');
        }

        $permit = PermitSembelihan::findOrFail($id);
        $permit->status_kelulusan = 'Diluluskan';
        $permit->diluluskan_oleh = $user->id;
        $permit->catatan = ($permit->catatan ? $permit->catatan . " | " : "") . "Diluluskan oleh {$user->nama} pada " . Carbon::now()->format('d/m/Y H:i');
        $permit->save();

        // Kemaskini status semua ternakan terlibat
        $ternakanIds = collect($permit->senarai_ternakan_list)->pluck('ternakan_id')->filter()->unique();
        if ($permit->ternakan_id && !$ternakanIds->contains($permit->ternakan_id)) {
            $ternakanIds->push($permit->ternakan_id);
        }

        foreach ($ternakanIds as $tid) {
            $ternakan = Ternakan::find($tid);
            if ($ternakan) {
                $ternakan->status = 'Sembelih';
                $ternakan->save();
            }
        }

        // Catat ke Action List
        \App\Models\ActionList::catatAktiviti([
            'tajuk_aktiviti' => "Kelulusan Permit Sembelihan (No: {$permit->no_permit})",
            'kategori_aktiviti' => 'Pendaftaran Ternakan (EPTR)',
            'maklumat_aktiviti' => "Meluluskan Borang D (Permit Sembelihan Luar / Rumah Sembelih) No. {$permit->no_permit} bagi pemunya " . ($permit->pemunya->nama ?? 'Penternak') . " melibatkan " . count($ternakanIds) . " ekor ternakan.",
            'jajahan' => $permit->jajahan ?: ($user->jajahan ?: 'Pasir Puteh'),
            'lokasi' => $permit->lokasi_sembelih ?: ('Pejabat JPV Jajahan ' . ($permit->jajahan ?: 'Pasir Puteh')),
            'status' => 'Selesai',
        ]);

        return redirect()->route('eptr.borang-d.show', $permit->id)->with('success', "Permit Sembelihan No. {$permit->no_permit} dan SKV Sembelih telah BERJAYA DILULUSKAN.");
    }

    /**
     * Penolakan Permit Sembelihan (Borang D) oleh Admin Jajahan
     */
    public function tolakBorangD(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isStaff()) {
            return back()->with('error', 'Hanya Admin Jajahan / Pegawai EPTR yang dibenarkan menolak Permit Sembelihan EPTR.');
        }

        $permit = PermitSembelihan::findOrFail($id);
        $permit->status_kelulusan = 'Ditolak';
        $permit->diluluskan_oleh = $user->id;
        $permit->catatan = ($permit->catatan ? $permit->catatan . " | " : "") . "Ditolak oleh {$user->nama} pada " . Carbon::now()->format('d/m/Y H:i');
        $permit->save();

        // Catat ke Action List
        \App\Models\ActionList::catatAktiviti([
            'tajuk_aktiviti' => "Penolakan Permit Sembelihan (No: {$permit->no_permit})",
            'kategori_aktiviti' => 'Pendaftaran Ternakan (EPTR)',
            'maklumat_aktiviti' => "Menolak permohonan Permit Sembelihan Borang D No. {$permit->no_permit}.",
            'jajahan' => $permit->jajahan ?: ($user->jajahan ?: 'Pasir Puteh'),
            'lokasi' => 'Pejabat JPV Jajahan ' . ($permit->jajahan ?: 'Pasir Puteh'),
            'status' => 'Selesai',
        ]);

        return redirect()->route('eptr.borang-d.show', $permit->id)->with('info', "Permit Sembelihan No. {$permit->no_permit} telah DITOLAK.");
    }

    public function showBorangD($id)
    {
        $permit = PermitSembelihan::with('pemunya', 'ternakan', 'pelulus')->findOrFail($id);
        return view('eptr.borang-d-show', compact('permit'));
    }

    // Cetak Borang D Sahaja (Jadual Keempat) - Hanya Admin Jajahan / Staf
    public function cetakBorangD($id)
    {
        $user = Auth::user();
        if (!$user || !$user->isStaff()) {
            abort(403, 'Akses Ditolak: Hanya Admin Jajahan / Pegawai EPTR yang dibenarkan mencetak Permit Sembelihan EPTR.');
        }

        $permit = PermitSembelihan::with('pemunya', 'ternakan', 'pelulus')->findOrFail($id);
        return view('eptr.cetak-borang-d', compact('permit'));
    }

    // Cetak Sijil Kesihatan Veterinar (SKV) Sembelih (2 Halaman - Sembelih & Karkas)
    public function cetakSkvSembelih($id)
    {
        $user = Auth::user();
        if (!$user || !$user->isStaff()) {
            abort(403, 'Akses Ditolak: Hanya Admin Jajahan / Pegawai EPTR yang dibenarkan mencetak Sijil SKV Sembelih.');
        }

        $permit = PermitSembelihan::with('pemunya', 'ternakan', 'pelulus')->findOrFail($id);
        return view('eptr.cetak-skv-sembelih', compact('permit'));
    }

    // Cetak 1 Set Lengkap (Borang D + SKV Sembelih 2 Halaman)
    public function cetakSetLengkapBorangD($id)
    {
        $user = Auth::user();
        if (!$user || !$user->isStaff()) {
            abort(403, 'Akses Ditolak: Hanya Admin Jajahan / Pegawai EPTR yang dibenarkan mencetak set lengkap Borang D.');
        }

        $permit = PermitSembelihan::with('pemunya', 'ternakan', 'pelulus')->findOrFail($id);
        return view('eptr.cetak-set-lengkap-borang-d', compact('permit'));
    }

    // Cetak Pukal Borang D Permit Sembelih - Hanya Admin Jajahan / Staf
    public function cetakPukalBorangD(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isStaff()) {
            abort(403, 'Akses Ditolak: Hanya Admin Jajahan / Pegawai EPTR yang dibenarkan mencetak Permit Sembelihan EPTR.');
        }

        $ids = $request->input('ids', []);
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }

        if (empty($ids)) {
            return back()->with('error', 'Sila pilih sekurang-kurangnya satu permit sembelihan untuk dicetak.');
        }

        $permitList = PermitSembelihan::with('pemunya', 'ternakan', 'pelulus')
            ->whereIn('id', $ids)
            ->latest()
            ->get();

        return view('eptr.cetak-pukal-borang-d', compact('permitList'));
    }

    // Cetak Pukal SKV Sembelih - Hanya Admin Jajahan / Staf
    public function cetakPukalSkvSembelih(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isStaff()) {
            abort(403, 'Akses Ditolak: Hanya Admin Jajahan / Pegawai EPTR yang dibenarkan mencetak SKV Sembelih.');
        }

        $ids = $request->input('ids', []);
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }

        if (empty($ids)) {
            return back()->with('error', 'Sila pilih sekurang-kurangnya satu permit sembelihan untuk dicetak.');
        }

        $permitList = PermitSembelihan::with('pemunya', 'ternakan', 'pelulus')
            ->whereIn('id', $ids)
            ->latest()
            ->get();

        return view('eptr.cetak-pukal-skv-sembelih', compact('permitList'));
    }

    // ==========================================
    // PROSES DAFTAR ANAK TERNAKAN (KELAHIRAN)
    // ==========================================

    public function createAnak(Request $request)
    {
        $user = Auth::user();

        // Semua admin kecuali Super Admin tidak dibenarkan mendaftar anak ternakan
        if ($user->isStaff() && !$user->isSuperAdmin()) {
            return redirect()->route('eptr.index')->with('error', 'Akses Ditolak: Pegawai pentadbir tidak dibenarkan mendaftar anak ternakan. Pendaftaran anak ternakan hanya boleh dilakukan oleh penternak / pemunya induk.');
        }

        // Senarai Induk (Ternakan Betina Aktif yang tidak dibatalkan/mati/sembelih)
        $indukQuery = Ternakan::with('pemunya')
            ->where('jantina', 'Betina')
            ->whereIn('status', ['Aktif', 'Pawah'])
            ->where('status_kelulusan', 'Diluluskan')
            ->whereDoesntHave('pembatalanTernakan', function ($q) {
                $q->where('status_kelulusan', 'Disahkan');
            })
            ->whereDoesntHave('permitSembelihan', function ($q) {
                $q->where('status_kelulusan', 'Diluluskan');
            });

        if (!$user->isStaff() && $user->pemunya) {
            $indukQuery->where('pemunya_id', $user->pemunya->id);
        }
        $indukList = $indukQuery->orderBy('no_tag')->get();

        // Senarai Pejantan (Ternakan Jantan Aktif yang tidak dibatalkan/mati/sembelih)
        $pejantanQuery = Ternakan::with('pemunya')
            ->where('jantina', 'Jantan')
            ->whereIn('status', ['Aktif', 'Pawah'])
            ->where('status_kelulusan', 'Diluluskan')
            ->whereDoesntHave('pembatalanTernakan', function ($q) {
                $q->where('status_kelulusan', 'Disahkan');
            })
            ->whereDoesntHave('permitSembelihan', function ($q) {
                $q->where('status_kelulusan', 'Diluluskan');
            });
        $pejantanList = $pejantanQuery->orderBy('no_tag')->get();

        $selectedInduk = null;
        if ($request->filled('induk_id')) {
            $selectedInduk = Ternakan::with('pemunya')->find($request->induk_id);
            if ($selectedInduk && $selectedInduk->isDibatalkanAtauMatiAtauSembelih()) {
                return redirect()->route('eptr.index')->with('error', "Akses Ditolak: Induk No. Tag {$selectedInduk->no_tag} telah dibatalkan / mati / disembelih dan tidak boleh didaftarkan kelahiran anak.");
            }
        }

        $bakaData = config('baka', []);
        $kelantanData = config('kelantan.jajahan', []);

        return view('eptr.daftar-anak', compact('indukList', 'pejantanList', 'selectedInduk', 'bakaData', 'kelantanData', 'user'));
    }

    public function storeAnak(Request $request)
    {
        $user = Auth::user();

        // Semua admin kecuali Super Admin tidak dibenarkan mendaftar anak ternakan
        if ($user->isStaff() && !$user->isSuperAdmin()) {
            return redirect()->route('eptr.index')->with('error', 'Akses Ditolak: Pegawai pentadbir tidak dibenarkan mendaftar anak ternakan. Pendaftaran anak ternakan hanya boleh dilakukan oleh penternak / pemunya induk.');
        }

        $isStaff = $user->isStaff();

        $validated = $request->validate([
            'induk_id' => 'required|exists:ternakan,id',
            'pejantan_id' => 'nullable|exists:ternakan,id',
            'tarikh_kelahiran' => 'required|date|before_or_equal:today',
            'jantina_anak' => 'required|in:Jantan,Betina',
            'baka_anak' => 'required|string',
            'berat_lahir_kg' => 'nullable|numeric|min:0.5|max:100',
            'warna_anak' => 'nullable|string|max:100',
            'tanda_badan_anak' => 'nullable|string|max:255',
            'status_kelahiran' => 'required|in:Hidup,Mati Semasa Lahir,Gugur',
            'keadaan_anak' => 'required|in:Cergas,Sederhana,Lemah',
            'catatan' => 'nullable|string',
            'gambar_anak' => 'nullable|image|max:5120',
            'resit_pembayaran' => $isStaff ? 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048' : 'required|file|mimes:jpeg,png,jpg,pdf|max:2048', // Wajib bagi penternak (Maksimum 2MB)
        ], [
            'resit_pembayaran.required' => 'Semasa mendaftar anak ternakan, penternak wajib melampirkan salinan resit pembayaran.',
            'resit_pembayaran.mimes' => 'Fail resit pembayaran mestilah dalam format JPG, PNG, atau PDF.',
            'resit_pembayaran.max' => 'Saiz fail resit pembayaran tidak boleh melebihi 2MB.',
        ]);

        $induk = Ternakan::with('pemunya')->findOrFail($validated['induk_id']);
        if ($induk->isDibatalkanAtauMatiAtauSembelih()) {
            return back()->with('error', "Tindakan Ditolak: Induk No. Tag {$induk->no_tag} telah dibatalkan / mati / disembelih dan tidak dibenarkan mendaftar kelahiran anak.")->withInput();
        }

        $pejantan = $request->filled('pejantan_id') ? Ternakan::find($request->pejantan_id) : null;
        if ($pejantan && $pejantan->isDibatalkanAtauMatiAtauSembelih()) {
            return back()->with('error', "Tindakan Ditolak: Pejantan No. Tag {$pejantan->no_tag} telah dibatalkan / mati / disembelih.")->withInput();
        }

        // Muat naik gambar jika ada
        $gambarPath = null;
        if ($request->hasFile('gambar_anak')) {
            $gambarPath = $this->uploadFileSafely($request->file('gambar_anak'), 'anak_ternakan');
        }

        // Muat naik resit pembayaran jika disertakan
        $resitPath = null;
        if ($request->hasFile('resit_pembayaran')) {
            $resitPath = $this->uploadFileSafely($request->file('resit_pembayaran'), 'resit_eptr');
        }

        $anakTernakan = null;
        $noTagAnak = null;

        // Jika anak hidup, daftarkan rekod ternakan baharu secara automatik
        if ($validated['status_kelahiran'] === 'Hidup') {
            $statusKelulusan = 'Menunggu';
            $statusAnak = 'Menunggu';
            $noTagAnak = null;
            $noSiriKadKuning = null;
            $qrCode = null;
            $tarikhDaftar = null;
            $diluluskanOleh = null;
            $tarikhKelulusan = null;

            $anakTernakan = Ternakan::create([
                'pemunya_id' => $induk->pemunya_id,
                'no_tag' => $noTagAnak,
                'jenis_ternakan' => $induk->jenis_ternakan,
                'baka' => $validated['baka_anak'],
                'baka_pejantan' => $pejantan ? $pejantan->baka : ($validated['baka_anak'] ?? 'KACUKAN'),
                'baka_induk' => $induk->baka,
                'no_tanda_pengenalan_induk' => $induk->no_tag ?? 'INDUK-' . $induk->id,
                'jantina' => $validated['jantina_anak'],
                'umur' => (function() use ($validated) {
                    $birth = Carbon::parse($validated['tarikh_kelahiran'])->startOfDay();
                    $today = Carbon::today();
                    $diffDays = $birth->diffInDays($today, false);
                    if ($diffDays <= 0) return 'Baru Lahir';
                    $years = (int)$birth->diffInYears($today);
                    $months = (int)$birth->copy()->addYears($years)->diffInMonths($today);
                    $days = (int)$birth->copy()->addYears($years)->addMonths($months)->diffInDays($today);
                    if ($years > 0) return $years . ' Tahun' . ($months > 0 ? (' ' . $months . ' Bulan') : '');
                    if ($months > 0) return $months . ' Bulan' . ($days > 0 ? (' ' . $days . ' Hari') : '');
                    return $days . ' Hari';
                })(),
                'tarikh_lahir' => $validated['tarikh_kelahiran'],
                'warna' => $validated['warna_anak'] ?? $induk->warna,
                'tanda_badan' => $validated['tanda_badan_anak'] ?? null,
                'tujuan_ternakan' => $induk->tujuan_ternakan,
                'lokasi_kandang' => $induk->lokasi_kandang,
                'jajahan' => $induk->jajahan,
                'daerah' => $induk->daerah,
                'poskod' => $induk->poskod,
                'program' => $induk->program ?? 'Tiada',
                'status' => $statusAnak,
                'status_kelulusan' => $statusKelulusan,
                'tarikh_daftar' => $tarikhDaftar,
                'tarikh_kelulusan' => $tarikhKelulusan,
                'diluluskan_oleh' => $diluluskanOleh,
                'no_siri_kad_kuning' => $noSiriKadKuning,
                'qr_code' => $qrCode,
                'gambar_ternakan' => $gambarPath,
                'resit_pembayaran' => $resitPath,
                'catatan' => (Carbon::parse($validated['tarikh_kelahiran'])->startOfDay()->diffInDays(Carbon::today()) > 14 ? "[Pendaftaran Lewat Seksyen 7 (> 14 Hari)] " : "") . "Anak kelahiran dari Induk Tag: " . ($induk->no_tag ?? $induk->id) . (!empty($validated['catatan']) ? " | " . $validated['catatan'] : ""),
                'didaftar_oleh' => $user->id,
            ]);
        }

        // Cipta Rekod Kelahiran
        $rekodKelahiran = RekodKelahiran::create([
            'induk_id' => $induk->id,
            'pejantan_id' => $pejantan ? $pejantan->id : null,
            'anak_ternakan_id' => $anakTernakan ? $anakTernakan->id : null,
            'pemunya_id' => $induk->pemunya_id,
            'no_tag_sementara' => $noTagAnak,
            'jantina_anak' => $validated['jantina_anak'],
            'tarikh_kelahiran' => $validated['tarikh_kelahiran'],
            'berat_lahir_kg' => $validated['berat_lahir_kg'] ?? null,
            'baka_anak' => $validated['baka_anak'],
            'warna_anak' => $validated['warna_anak'] ?? null,
            'tanda_badan_anak' => $validated['tanda_badan_anak'] ?? null,
            'status_kelahiran' => $validated['status_kelahiran'],
            'keadaan_anak' => $validated['keadaan_anak'],
            'gambar_anak' => $gambarPath,
            'catatan' => $validated['catatan'] ?? null,
            'didaftar_oleh' => $user->id,
        ]);

        // Notifikasi aktiviti pendaftaran kelahiran anak ternakan
        $namaPemunya = $induk->pemunya ? $induk->pemunya->nama : $user->name;
        $targetUrl = route('eptr.show', $anakTernakan ? $anakTernakan->id : $induk->id);
        $jajahanKelahiran = $induk->jajahan;

        // 1. Notifikasi kepada pemohon (Penternak)
        \App\Models\UserNotification::send(
            $user->id,
            'Pendaftaran Kelahiran Anak Ternakan Dihantar',
            "Pendaftaran kelahiran anak ternakan bagi Induk " . ($induk->no_tag ?? $induk->id) . " ({$validated['jantina_anak']} - {$validated['baka_anak']}) telah dihantar dan sedang menunggu semakan kelulusan Pegawai JPVNK.",
            'eptr',
            $targetUrl,
            'fa-solid fa-baby',
            'amber'
        );

        // 2. Notifikasi kepada Admin EPTR Jajahan berkenaan
        $adminJajahanList = User::whereIn('role', ['admin_jajahan', 'admin_eptr_jajahan'])
            ->where(function ($q) use ($jajahanKelahiran) {
                $q->where('jajahan', $jajahanKelahiran)
                  ->orWhereNull('jajahan')
                  ->orWhere('jajahan', '');
            })
            ->where('id', '!=', $user->id)
            ->get();

        if ($adminJajahanList->isEmpty()) {
            $adminJajahanList = User::whereIn('role', ['admin_jajahan', 'admin_eptr_jajahan'])
                ->where('id', '!=', $user->id)
                ->get();
        }

        foreach ($adminJajahanList as $aj) {
            \App\Models\UserNotification::send(
                $aj->id,
                "Kelahiran Anak Ternakan Menunggu Kelulusan ({$jajahanKelahiran})",
                "Pendaftaran kelahiran anak ternakan baharu bagi Induk " . ($induk->no_tag ?? $induk->id) . " oleh {$namaPemunya} di Jajahan {$jajahanKelahiran} memerlukan semakan & kelulusan anda.",
                'eptr',
                $targetUrl,
                'fa-solid fa-clipboard-check',
                'amber'
            );
        }

        // 3. Notifikasi kepada Admin EPTR Negeri & Super Admin
        $adminNegeriList = User::whereIn('role', ['admin_eptr', 'super_admin'])
            ->where('id', '!=', $user->id)
            ->get();

        foreach ($adminNegeriList as $an) {
            \App\Models\UserNotification::send(
                $an->id,
                "Pendaftaran Kelahiran Anak Baharu ({$jajahanKelahiran})",
                "Pendaftaran kelahiran anak ternakan baharu bagi Induk " . ($induk->no_tag ?? $induk->id) . " oleh {$namaPemunya} (Jajahan {$jajahanKelahiran}).",
                'eptr',
                $targetUrl,
                'fa-solid fa-cow',
                'blue'
            );
        }

        $mesej = "Pendaftaran kelahiran anak ternakan bagi Induk " . ($induk->no_tag ?? $induk->id) . " berjaya dihantar dan sedang menunggu kelulusan Pegawai JPVNK.";

        return redirect()->route('eptr.show', $anakTernakan ? $anakTernakan->id : $induk->id)->with('success', $mesej);
    }

    // ==========================================
    // PROGRAM KESIHATAN TERNAKAN
    // ==========================================

    public function kesihatanIndex(Request $request)
    {
        $user = Auth::user();
        $query = ProgramKesihatan::with('ternakan.pemunya', 'pendaftar');
        $boosterQuery = ProgramKesihatan::with('ternakan.pemunya')
            ->whereNotNull('tarikh_ulangan_dos')
            ->where('tarikh_ulangan_dos', '>=', Carbon::now()->toDateString())
            ->where('tarikh_ulangan_dos', '<=', Carbon::now()->addDays(30)->toDateString());
        $statsQuery = ProgramKesihatan::query();

        if (!$user->isStaff()) {
            $pemunyaIds = Pemunya::where('user_id', $user->id)
                ->orWhere(function ($q) use ($user) {
                    if (!empty($user->ic_number)) {
                        $q->where('no_kp', $user->ic_number);
                    }
                })
                ->pluck('id');

            $query->whereHas('ternakan', function ($q) use ($pemunyaIds) {
                $q->whereIn('pemunya_id', $pemunyaIds);
            });
            $boosterQuery->whereHas('ternakan', function ($q) use ($pemunyaIds) {
                $q->whereIn('pemunya_id', $pemunyaIds);
            });
            $statsQuery->whereHas('ternakan', function ($q) use ($pemunyaIds) {
                $q->whereIn('pemunya_id', $pemunyaIds);
            });
        } elseif ($user->role === 'admin_jajahan' && !empty($user->jajahan)) {
            $query->where('jajahan', $user->jajahan);
            $boosterQuery->where('jajahan', $user->jajahan);
            $statsQuery->where('jajahan', $user->jajahan);
        }

        // Penapis Jajahan
        if ($request->filled('jajahan')) {
            $query->where('jajahan', $request->jajahan);
            $boosterQuery->where('jajahan', $request->jajahan);
            $statsQuery->where('jajahan', $request->jajahan);
        }

        // Penapis Jenis Program
        if ($request->filled('jenis_program')) {
            $query->where('jenis_program', $request->jenis_program);
        }

        // Carian No Tag / Nama Vaksin
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_rujukan_kesihatan', 'like', "%{$search}%")
                  ->orWhere('nama_vaksin_atau_ubat', 'like', "%{$search}%")
                  ->orWhere('diagnosis_atau_tujuan', 'like', "%{$search}%")
                  ->orWhereHas('ternakan', function ($qt) use ($search) {
                      $qt->where('no_tag', 'like', "%{$search}%")
                         ->orWhere('baka', 'like', "%{$search}%");
                  });
            });
        }

        $rekodList = $query->orderBy('tarikh_rawatan', 'desc')->paginate(12);

        // Statistik Program Kesihatan
        $totalRawatan = (clone $statsQuery)->count();
        $totalVaksinasi = (clone $statsQuery)->where('jenis_program', 'like', '%Vaksin%')->count();
        $totalDeworming = (clone $statsQuery)->where('jenis_program', 'like', '%Penyahcacing%')->count();
        $totalRawatanKlinikal = (clone $statsQuery)->where('jenis_program', 'like', '%Rawatan%')->count();
        $totalSurveilans = (clone $statsQuery)->where(function ($q) {
            $q->where('jenis_program', 'like', '%Surveilans%')->orWhere('jenis_program', 'like', '%Saringan%');
        })->count();

        // Temujanji Ulangan / Booster akan datang dalam masa 30 hari
        $boosterAkanDatang = $boosterQuery->orderBy('tarikh_ulangan_dos', 'asc')
            ->take(5)
            ->get();

        $jajahanList = array_keys(config('kelantan.jajahan', []));

        return view('eptr.kesihatan.index', compact(
            'rekodList',
            'totalRawatan',
            'totalVaksinasi',
            'totalDeworming',
            'totalRawatanKlinikal',
            'totalSurveilans',
            'boosterAkanDatang',
            'jajahanList',
            'user'
        ));
    }

    public function createKesihatan(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->isPurePengarah()) {
            abort(403, 'Akses Ditolak: Pengarah Perkhidmatan Veterinar Negeri tidak dibenarkan membuat pendaftaran Rekod Program Kesihatan Ternakan.');
        }
        
        $query = Ternakan::with('pemunya')
            ->whereIn('status', ['Aktif', 'Pawah'])
            ->where('status_kelulusan', 'Diluluskan')
            ->whereDoesntHave('pembatalanTernakan', function ($q) {
                $q->where('status_kelulusan', 'Disahkan');
            })
            ->whereDoesntHave('permitSembelihan', function ($q) {
                $q->where('status_kelulusan', 'Diluluskan');
            });

        if (!$user->isStaff()) {
            $pemunyaIds = Pemunya::where('user_id', $user->id)
                ->orWhere(function ($q) use ($user) {
                    if (!empty($user->ic_number)) {
                        $q->where('no_kp', $user->ic_number);
                    }
                })
                ->pluck('id');
            $query->whereIn('pemunya_id', $pemunyaIds);
        } elseif ($user->role === 'admin_jajahan' && !empty($user->jajahan)) {
            $query->where('jajahan', $user->jajahan);
        }
        $ternakanList = $query->orderBy('no_tag')->get();

        $selectedTernakan = null;
        if ($request->filled('ternakan_id')) {
            $selectedTernakan = Ternakan::with('pemunya')->find($request->ternakan_id);
            if ($selectedTernakan && $selectedTernakan->isDibatalkanAtauMatiAtauSembelih()) {
                return redirect()->route('eptr.kesihatan.index')->with('error', "Akses Ditolak: Ternakan No. Tag {$selectedTernakan->no_tag} telah dibatalkan / mati / disembelih dan tidak boleh menerima sebarang rawatan atau perubahan rekod kesihatan.");
            }
        }

        $jajahanList = array_keys(config('kelantan.jajahan', []));

        return view('eptr.kesihatan.create', compact('ternakanList', 'selectedTernakan', 'jajahanList', 'user'));
    }

    public function storeKesihatan(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->isPurePengarah()) {
            abort(403, 'Akses Ditolak: Pengarah Perkhidmatan Veterinar Negeri tidak dibenarkan membuat pendaftaran Rekod Program Kesihatan Ternakan.');
        }

        $validated = $request->validate([
            'ternakan_id' => 'required|exists:ternakan,id',
            'jenis_program' => 'required|string',
            'nama_vaksin_atau_ubat' => 'required|string|max:200',
            'tarikh_rawatan' => 'required|date',
            'tarikh_ulangan_dos' => 'nullable|date|after_or_equal:tarikh_rawatan',
            'berat_semasa_kg' => 'nullable|numeric|min:1|max:2000',
            'suhu_badan_celsius' => 'nullable|numeric|min:30|max:45',
            'status_kesihatan' => 'required|string',
            'dos_diberikan' => 'nullable|string|max:100',
            'tindakan_rawatan' => 'nullable|string',
            'diagnosis_atau_tujuan' => 'nullable|string',
            'pegawai_pemeriksa' => 'required|string|max:150',
            'lokasi_pemeriksaan' => 'nullable|string|max:200',
            'catatan_dan_syor' => 'nullable|string',
            'dokumen_lampiran' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:10240',
        ]);

        $ternakan = Ternakan::findOrFail($validated['ternakan_id']);
        if ($ternakan->isDibatalkanAtauMatiAtauSembelih()) {
            return back()->with('error', "Tindakan Ditolak: Ternakan No. Tag {$ternakan->no_tag} telah dibatalkan / mati / disembelih dan tidak dibenarkan membuat sebarang rawatan atau perubahan rekod kesihatan.")->withInput();
        }

        $lampiranPath = null;
        if ($request->hasFile('dokumen_lampiran')) {
            $lampiranPath = $this->uploadFileSafely($request->file('dokumen_lampiran'), 'kesihatan_ternakan');
        }

        $noRujukan = 'MED-' . strtoupper(substr($ternakan->jajahan, 0, 2)) . '-' . date('Y') . '-' . rand(10000, 99999);

        $rekod = ProgramKesihatan::create([
            'ternakan_id' => $ternakan->id,
            'no_rujukan_kesihatan' => $noRujukan,
            'jenis_program' => $validated['jenis_program'],
            'nama_vaksin_atau_ubat' => $validated['nama_vaksin_atau_ubat'],
            'tarikh_rawatan' => $validated['tarikh_rawatan'],
            'tarikh_ulangan_dos' => $validated['tarikh_ulangan_dos'] ?? null,
            'berat_semasa_kg' => $validated['berat_semasa_kg'] ?? null,
            'suhu_badan_celsius' => $validated['suhu_badan_celsius'] ?? null,
            'status_kesihatan' => $validated['status_kesihatan'],
            'diagnosis_atau_tujuan' => $validated['diagnosis_atau_tujuan'] ?? null,
            'tindakan_rawatan' => $validated['tindakan_rawatan'] ?? null,
            'dos_diberikan' => $validated['dos_diberikan'] ?? null,
            'pegawai_pemeriksa' => $validated['pegawai_pemeriksa'],
            'jajahan' => $ternakan->jajahan,
            'lokasi_pemeriksaan' => $validated['lokasi_pemeriksaan'] ?? ($ternakan->lokasi_kandang ?? 'Kandang Penternak'),
            'catatan_dan_syor' => $validated['catatan_dan_syor'] ?? null,
            'dokumen_lampiran' => $lampiranPath,
            'didaftar_oleh' => $user->id,
        ]);

        // Catat ke Action List
        \App\Models\ActionList::catatAktiviti([
            'tajuk_aktiviti' => "Program Kesihatan Ternakan ({$validated['jenis_program']} - Tag: {$ternakan->no_tag})",
            'kategori_aktiviti' => 'Khidmat Rawatan & Klinikal',
            'maklumat_aktiviti' => "Pemberian rawatan/vaksinasi ({$validated['nama_vaksin_atau_ubat']}) bagi ternakan No. Tag {$ternakan->no_tag}. Status: {$validated['status_kesihatan']}. Pemeriksa: {$validated['pegawai_pemeriksa']}.",
            'jajahan' => $ternakan->jajahan ?: ($user->jajahan ?: 'Pasir Puteh'),
            'lokasi' => $validated['lokasi_pemeriksaan'] ?? ($ternakan->lokasi_kandang ?? ('Kandang Penternak ' . $ternakan->jajahan)),
            'status' => 'Selesai',
        ]);

        return redirect()->route('eptr.kesihatan.show', $rekod->id)->with('success', "Rekod Program Kesihatan ({$validated['jenis_program']}) berjaya disimpan bagi No. Tag: {$ternakan->no_tag}.");
    }

    public function showKesihatan($id)
    {
        $rekod = ProgramKesihatan::with('ternakan.pemunya', 'pendaftar')->findOrFail($id);
        return view('eptr.kesihatan.show', compact('rekod'));
    }

    /**
     * Jadual Fi Bayaran Statutori EPTR mengikut Enakmen
     */
    public function jadualFi()
    {
        $fiList = config('eptr_fi.fi', []);
        return view('eptr.jadual-fi', compact('fiList'));
    }

    /**
     * Padam Rekod Ternakan Ruminan (Super Admin Sahaja)
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Super Admin dibenarkan memadam rekod ternakan ruminan.');
        }

        $ternakan = Ternakan::findOrFail($id);
        $noTag = $ternakan->no_tag ?? ('ID: ' . $ternakan->id);
        $ternakan->delete();

        return redirect()->route('eptr.index')->with('success', "Rekod ternakan {$noTag} berjaya dipadam dari sistem.");
    }

    /**
     * Helper muat naik fail secara selamat merentasi pelayan dan persekitaran Windows/Linux
     */
    protected function uploadFileSafely($file, $folder = 'resit_eptr')
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
