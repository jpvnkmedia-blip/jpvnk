<?php

namespace App\Http\Controllers;

use App\Models\NaimbifPermohonan;
use App\Models\NaimbifInventoriTernakan;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NaimbifAdminController extends Controller
{
    /**
     * Senarai Semua Permohonan NAIMbif dengan Tapisan
     */
    public function index(Request $request)
    {
        $query = NaimbifPermohonan::forUser()->with(['inventoriTernakan', 'disemakOleh', 'diluluskanOleh', 'user', 'pemunya']);

        // Filter: Carian teks
        if ($request->filled('q')) {
            $search = $request->q;
            $cleanIc = preg_replace('/[^0-9]/', '', $search);
            $query->where(function ($q) use ($search, $cleanIc) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_rujukan', 'like', "%{$search}%")
                  ->orWhere('no_kp', 'like', "%{$cleanIc}%")
                  ->orWhere('no_telefon', 'like', "%{$search}%")
                  ->orWhere('id_premis', 'like', "%{$search}%");
            });
        }

        // Filter: Jajahan
        if ($request->filled('jajahan')) {
            $query->where(function ($q) use ($request) {
                $q->where('jajahan_ladang', $request->jajahan)
                  ->orWhere('jajahan', $request->jajahan);
            });
        }

        // Filter: Status Kelengkapan / Negeri
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'Lulus') {
                $query->where('status_negeri', 'Lulus');
            } elseif ($status === 'Gagal') {
                $query->where('status_negeri', 'Gagal');
            } elseif ($status === 'Disokong') {
                $query->where('syor_permohonan', 'Disokong')->where('status_negeri', 'Menunggu Kelulusan');
            } elseif ($status === 'Tidak Disokong') {
                $query->where('syor_permohonan', 'Tidak Disokong');
            } elseif ($status === 'Dalam Semakan') {
                $query->where('status_kelengkapan', 'Dalam Semakan');
            }
        }

        // Sorting
        $sort = $request->input('sort', 'latest');
        if ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $applications = $query->paginate(15)->withQueryString();
        $jajahans = NaimbifPermohonan::JAJAHAN_LIST;

        // Metrik Ringkas
        $totalApps = NaimbifPermohonan::forUser()->count();
        $totalLulus = NaimbifPermohonan::forUser()->where('status_negeri', 'Lulus')->count();
        $totalMenungguJajahan = NaimbifPermohonan::forUser()->where('syor_permohonan', 'Belum Disemak')->count();
        $totalMenungguNegeri = NaimbifPermohonan::forUser()->where('syor_permohonan', 'Disokong')->where('status_negeri', 'Menunggu Kelulusan')->count();

        return view('naimbif.admin.index', compact('applications', 'jajahans', 'totalApps', 'totalLulus', 'totalMenungguJajahan', 'totalMenungguNegeri'));
    }

    /**
     * Paparan Perincian & Borang Tindakan Pegawai Jajahan / Negeri
     */
    public function show($id)
    {
        $application = NaimbifPermohonan::with(['inventoriTernakan', 'disemakOleh', 'diluluskanOleh', 'user', 'pemunya'])
            ->findOrFail($id);

        if (!$application->canBeAccessedBy(Auth::user())) {
            return redirect()->route('naimbif.admin.index')
                ->with('error', 'Akses Disekat: Anda tidak mempunyai kebenaran untuk mengakses maklumat pemohon ini.');
        }

        $inventories = [];
        foreach ($application->inventoriTernakan as $inv) {
            $inventories[$inv->baka] = $inv;
        }

        return view('naimbif.admin.show', compact('application', 'inventories'));
    }

    /**
     * Tindakan Siasatan Premis & Syor Pejabat Jajahan
     */
    public function updateJajahan(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isStaff()) {
            return redirect()->back()->with('error', 'Akses Disekat: Hanya pegawai jabatan dibenarkan membuat semakan jajahan.');
        }

        $application = NaimbifPermohonan::findOrFail($id);

        if (!$application->canBeAccessedBy($user)) {
            return redirect()->route('naimbif.admin.index')
                ->with('error', 'Akses Disekat: Anda hanya dibenarkan mengurus pemohon dalam jajahan bertugas anda.');
        }

        $request->validate([
            'id_premis' => 'nullable|string|max:100',
            'status_kelengkapan' => 'nullable|in:Lengkap,Tidak Lengkap,Dalam Semakan',
            'syor_permohonan' => 'required|in:Disokong,Tidak disokong,Belum Disemak',
            'pegawai_penyiasat' => 'nullable|string|max:255',
            'tarikh_siasatan' => 'nullable|date',
            'catatan_jajahan' => 'nullable|string',
        ]);

        $pegawaiPenyiasat = $request->filled('pegawai_penyiasat') ? $request->pegawai_penyiasat : ($application->pegawai_penyiasat ?: $user->name);
        $statusKelengkapan = $request->filled('status_kelengkapan') ? $request->status_kelengkapan : ($application->status_kelengkapan ?: 'Lengkap');

        $application->update([
            'id_premis' => $request->id_premis ?: $application->id_premis,
            'status_kelengkapan' => $statusKelengkapan,
            'syor_permohonan' => $request->syor_permohonan,
            'pegawai_penyiasat' => $pegawaiPenyiasat,
            'tarikh_siasatan' => $request->tarikh_siasatan ?: now()->toDateString(),
            'catatan_jajahan' => $request->catatan_jajahan,
            'tarikh_semakan_jajahan' => now(),
            'disemak_oleh_user_id' => $user->id,
            'status_permohonan' => $request->syor_permohonan === 'Disokong' ? 'Disemak Jajahan' : 'Dalam Semakan',
        ]);

        // Notifikasi kepada pemohon jika ada akaun berdaftar
        if ($application->user_id) {
            UserNotification::send(
                $application->user_id,
                'Status Siasatan NAIMbif Dikemaskini',
                'Permohonan NAIMbif (' . $application->no_rujukan . ') anda telah disiasat oleh Pejabat JPVNK Jajahan dengan syor: ' . $application->syor_permohonan . '.',
                'naimbif',
                route('naimbif.public.check_status', ['no_rujukan' => $application->no_rujukan]),
                'fa-solid fa-clipboard-check',
                'blue'
            );
        }

        return redirect()->route('naimbif.admin.show', $application->id)
            ->with('success', 'Ulasan dan status tindakan Pejabat Jajahan telah berjaya disimpan.');
    }

    /**
     * Tindakan Keputusan & Kelulusan Pejabat Negeri (Ibu Pejabat JPVNK)
     */
    public function updateNegeri(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isPengarah() && !in_array($user->role, ['admin_eptr', 'admin_program'])) {
            return redirect()->back()->with('error', 'Akses Disekat: Ruangan Keputusan Negeri hanya boleh dikemaskini oleh Pegawai Ibu Pejabat / Pengarah.');
        }

        $application = NaimbifPermohonan::findOrFail($id);

        $request->validate([
            'status_negeri' => 'required|in:Lulus,Gagal,Menunggu Kelulusan',
            'no_rujukan_negeri' => 'nullable|string|max:100',
            'ulasan_negeri' => 'nullable|string',
            'pegawai_pelulus' => 'nullable|string|max:255',
        ]);

        $pegawaiPelulus = $request->filled('pegawai_pelulus') ? $request->pegawai_pelulus : ($application->pegawai_pelulus ?: $user->name);
        $noRujukanNegeri = $request->filled('no_rujukan_negeri') 
            ? $request->no_rujukan_negeri 
            : ($application->no_rujukan_negeri ?: ($request->status_negeri === 'Lulus' ? sprintf('JPVNK/BL/%s/%04d', date('Y'), $application->id) : null));

        $application->update([
            'status_negeri' => $request->status_negeri,
            'no_rujukan_negeri' => $noRujukanNegeri,
            'ulasan_negeri' => $request->ulasan_negeri,
            'pegawai_pelulus' => $pegawaiPelulus,
            'tarikh_kelulusan_negeri' => now(),
            'diluluskan_oleh_user_id' => $user->id,
            'status_permohonan' => $request->status_negeri === 'Lulus' ? 'Lulus' : ($request->status_negeri === 'Gagal' ? 'Ditolak' : 'Disemak Jajahan'),
        ]);

        // Notifikasi kepada pemohon jika ada akaun berdaftar
        if ($application->user_id) {
            $isLulus = $request->status_negeri === 'Lulus';
            UserNotification::send(
                $application->user_id,
                $isLulus ? 'Permohonan NAIMbif DILULUSKAN' : 'Keputusan Permohonan NAIMbif',
                'Keputusan rasmi bagi permohonan NAIMbif (' . $application->no_rujukan . ') adalah: ' . strtoupper($application->status_negeri) . '.',
                'naimbif',
                route('naimbif.public.check_status', ['no_rujukan' => $application->no_rujukan]),
                $isLulus ? 'fa-solid fa-circle-check' : 'fa-solid fa-circle-xmark',
                $isLulus ? 'emerald' : 'rose'
            );
        }

        return redirect()->route('naimbif.admin.show', $application->id)
            ->with('success', 'Keputusan dan ulasan Pejabat Negeri telah berjaya disimpan.');
    }

    /**
     * Eksport Data Permohonan ke CSV
     */
    public function exportCsv(Request $request)
    {
        $query = NaimbifPermohonan::forUser()->with(['inventoriTernakan', 'disemakOleh', 'diluluskanOleh']);

        if ($request->filled('jajahan')) {
            $query->where(function ($q) use ($request) {
                $q->where('jajahan_ladang', $request->jajahan)
                  ->orWhere('jajahan', $request->jajahan);
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'Lulus') {
                $query->where('status_negeri', 'Lulus');
            } elseif ($request->status === 'Gagal') {
                $query->where('status_negeri', 'Gagal');
            } elseif ($request->status === 'Disokong') {
                $query->where('syor_permohonan', 'Disokong');
            }
        }

        $applications = $query->latest()->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="senarai-permohonan-naimbif-' . date('Ymd-His') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return new StreamedResponse(function () use ($applications) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            fputcsv($output, [
                'No Rujukan',
                'Nama Pemohon',
                'No KP',
                'No Telefon',
                'Jajahan Peserta',
                'Jajahan Ladang',
                'Status Penternakan',
                'Keluasan Tanah (Ekar)',
                'Jumlah Lembu',
                'ID Premis',
                'Syor Jajahan',
                'Pegawai Penyiasat Jajahan',
                'Status Negeri',
                'No Rujukan Kelulusan Negeri',
                'Pegawai Pelulus',
                'Tarikh Permohonan',
            ]);

            foreach ($applications as $app) {
                fputcsv($output, [
                    $app->no_rujukan,
                    $app->nama,
                    $app->formatted_no_kp,
                    $app->no_telefon,
                    $app->jajahan,
                    $app->jajahan_ladang ?: $app->jajahan,
                    $app->status_penternakan,
                    $app->keluasan_tanah,
                    $app->total_ternakan,
                    $app->id_premis ?: '-',
                    $app->syor_permohonan ?: '-',
                    $app->pegawai_penyiasat ?: '-',
                    $app->status_negeri ?: '-',
                    $app->no_rujukan_negeri ?: '-',
                    $app->pegawai_pelulus ?: '-',
                    $app->tarikh_permohonan ? $app->tarikh_permohonan->format('d/m/Y') : $app->created_at->format('d/m/Y'),
                ]);
            }

            fclose($output);
        }, 200, $headers);
    }

    /**
     * Padam Permohonan (Admin Sahaja)
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isPengarah()) {
            return redirect()->back()->with('error', 'Akses Disekat: Hanya Pentadbir Utama dibenarkan memadam permohonan.');
        }

        $application = NaimbifPermohonan::findOrFail($id);
        $application->delete();

        return redirect()->route('naimbif.admin.index')
            ->with('success', 'Rekod permohonan ' . $application->no_rujukan . ' telah berjaya dipadamkan.');
    }
}
