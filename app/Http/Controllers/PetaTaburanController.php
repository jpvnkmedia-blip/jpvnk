<?php

namespace App\Http\Controllers;

use App\Models\EpuLadang;
use App\Models\EpuPermohonan;
use App\Models\Pemunya;
use App\Models\Ternakan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetaTaburanController extends Controller
{
    /**
     * Koordinat rujukan pusat Jajahan di Negeri Kelantan
     */
    protected array $jajahanCoordinates = [
        'Kota Bharu'   => ['lat' => 6.1254, 'lng' => 102.2381],
        'Pasir Mas'    => ['lat' => 6.0431, 'lng' => 102.1415],
        'Tumpat'       => ['lat' => 6.1978, 'lng' => 102.1710],
        'Bachok'       => ['lat' => 6.0644, 'lng' => 102.4000],
        'Pasir Puteh'  => ['lat' => 5.8360, 'lng' => 102.4042],
        'Machang'      => ['lat' => 5.7667, 'lng' => 102.2167],
        'Tanah Merah'  => ['lat' => 5.8086, 'lng' => 102.1481],
        'Jeli'         => ['lat' => 5.6961, 'lng' => 101.8437],
        'Kuala Krai'   => ['lat' => 5.5317, 'lng' => 102.2008],
        'Gua Musang'   => ['lat' => 4.8789, 'lng' => 101.9686],
        'Lojing'       => ['lat' => 4.6500, 'lng' => 101.4500],
        'Kecil Lojing' => ['lat' => 4.6500, 'lng' => 101.4500],
    ];

    /**
     * Paparan Peta Taburan Penternak (EPU & EPTR)
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. Dapatkan Data Penternak & Ladang EPU (Unggas)
        $epuQuery = EpuLadang::with(['pemilik', 'permohonanList', 'permohonanTerkini']);
        $ladangEpuList = $epuQuery->get();

        $epuMarkers = [];
        $totalUnggas = 0;

        foreach ($ladangEpuList as $index => $ladang) {
            $jajahan = trim($ladang->jajahan ?: 'Kota Bharu');
            $coords = $this->resolveCoordinates($ladang->latitude, $ladang->longitude, $jajahan, 'epu', $ladang->id);

            // Kumpul maklumat jenis ternakan dan jumlah
            $jenisList = [];
            $jumlahEkor = 0;
            $statusLesen = 'Tiada Permohonan';

            if ($ladang->permohonanList && $ladang->permohonanList->count() > 0) {
                foreach ($ladang->permohonanList as $perm) {
                    $jenisName = trim($perm->jenis_unggas ?: 'Ayam Pedaging');
                    $bilangan = (int)($perm->bilangan_semasa_unggas ?: ($ladang->kapasiti_maksimum_unggas ?: 0));
                    $jenisList[$jenisName] = ($jenisList[$jenisName] ?? 0) + $bilangan;
                    $jumlahEkor += $bilangan;
                }
                $terkini = $ladang->permohonanTerkini ?: $ladang->permohonanList->first();
                $statusLesen = $terkini->status ?? 'Dalam Proses';
            } else {
                $jenisName = 'Ayam / Unggas';
                $bilangan = (int)($ladang->kapasiti_maksimum_unggas ?: 0);
                $jenisList[$jenisName] = $bilangan;
                $jumlahEkor = $bilangan;
            }

            if ($jumlahEkor === 0 && !empty($ladang->kapasiti_maksimum_unggas)) {
                $jumlahEkor = (int)$ladang->kapasiti_maksimum_unggas;
            }

            $totalUnggas += $jumlahEkor;

            $epuMarkers[] = [
                'id' => 'epu_' . $ladang->id,
                'raw_id' => $ladang->id,
                'modul' => 'EPU',
                'modul_label' => 'EPU (Unggas)',
                'kategori' => 'unggas',
                'nama_penternak' => $ladang->nama_pemohon_atau_syarikat ?: ($ladang->pemilik->name ?? 'Penternak Unggas'),
                'nama_premis' => $ladang->nama_ladang ?: 'Ladang Unggas #' . $ladang->id,
                'no_syarikat_ssm' => $ladang->no_syarikat_atau_ssm ?? '-',
                'no_telefon' => $ladang->pemilik->no_telefon ?? '-',
                'jajahan' => $jajahan,
                'daerah' => $ladang->daerah ?: '-',
                'mukim' => $ladang->mukim ?: '-',
                'alamat' => $ladang->alamat_ladang ?: ($jajahan . ', Kelantan'),
                'lat' => $coords['lat'],
                'lng' => $coords['lng'],
                'jenis_ternakan_list' => array_keys($jenisList),
                'pecahan_ternakan' => $jenisList,
                'jumlah_ternakan' => $jumlahEkor,
                'kapasiti_ladang' => (int)($ladang->kapasiti_maksimum_unggas ?: $jumlahEkor),
                'status' => $statusLesen,
                'sistem_reban' => $ladang->sistem_reban ?: 'Tertutup',
                'url' => route('epu.show', $ladang->id),
            ];
        }

        // 2. Dapatkan Data Penternak & Ternakan EPTR (Ruminan)
        $pemunyaQuery = Pemunya::with(['ternakan', 'user']);
        $pemunyaList = $pemunyaQuery->get();

        $eptrMarkers = [];
        $totalRuminan = 0;

        foreach ($pemunyaList as $index => $pemunya) {
            $jajahan = trim($pemunya->jajahan ?: 'Kota Bharu');
            $coords = $this->resolveCoordinates(null, null, $jajahan, 'eptr', $pemunya->id);

            $ternakanGroup = $pemunya->ternakan ?: collect();
            $jumlahEkor = $ternakanGroup->count();
            $totalRuminan += $jumlahEkor;

            // Kumpul pecahan mengikut jenis ternakan (lembu, kambing, biri-biri, kerbau, dll)
            $jenisList = [];
            $bakaList = [];
            $hasPawah = false;

            foreach ($ternakanGroup as $t) {
                $jName = ucfirst(strtolower(trim($t->jenis_ternakan ?: 'Lembu')));
                $jenisList[$jName] = ($jenisList[$jName] ?? 0) + 1;

                if (!empty($t->baka)) {
                    $bName = ucfirst(strtolower(trim($t->baka)));
                    $bakaList[$bName] = ($bakaList[$bName] ?? 0) + 1;
                }

                if ($t->status === 'Pawah' || (!empty($t->program) && str_contains(strtolower($t->program), 'pawah'))) {
                    $hasPawah = true;
                }
            }

            if (empty($jenisList)) {
                $jenisList['Ruminan'] = 0;
            }

            $firstKandang = $ternakanGroup->first(fn($t) => !empty($t->lokasi_kandang));
            $namaPremis = $firstKandang ? $firstKandang->lokasi_kandang : ('Kandang Ternakan ' . ($pemunya->nama ?: '#' . $pemunya->id));

            $eptrMarkers[] = [
                'id' => 'eptr_' . $pemunya->id,
                'raw_id' => $pemunya->id,
                'modul' => 'EPTR',
                'modul_label' => $hasPawah ? 'EPTR (Pawah & Ruminan)' : 'EPTR (Ruminan)',
                'kategori' => 'ruminan',
                'has_pawah' => $hasPawah,
                'nama_penternak' => $pemunya->nama ?: ($pemunya->user->name ?? 'Penternak Ruminan'),
                'nama_premis' => $namaPremis,
                'no_kp' => $pemunya->no_kp ?? '-',
                'no_telefon' => $pemunya->no_telefon ?? ($pemunya->user->no_telefon ?? '-'),
                'jajahan' => $jajahan,
                'daerah' => $pemunya->daerah ?: ($firstKandang->daerah ?? '-'),
                'mukim' => '-',
                'alamat' => $pemunya->alamat ?: ($jajahan . ', Kelantan'),
                'lat' => $coords['lat'],
                'lng' => $coords['lng'],
                'jenis_ternakan_list' => array_keys($jenisList),
                'pecahan_ternakan' => $jenisList,
                'pecahan_baka' => $bakaList,
                'jumlah_ternakan' => $jumlahEkor,
                'kapasiti_ladang' => $jumlahEkor,
                'status' => $pemunya->status ?: 'Aktif',
                'url' => route('eptr.penternak.show', $pemunya->id),
            ];
        }

        // 3. Dapatkan Data Penternak & Ladang NAIMbif (Bridlot Pedaging)
        $naimbifQuery = \App\Models\NaimbifPermohonan::with(['inventoriTernakan', 'user', 'pemunya']);
        $naimbifList = $naimbifQuery->get();

        $naimbifMarkers = [];
        $totalNaimbifLembu = 0;

        foreach ($naimbifList as $naimbif) {
            $jajahan = trim($naimbif->jajahan_ladang ?: ($naimbif->jajahan ?: 'Kota Bharu'));
            $lat = is_numeric($naimbif->gps_latitud) ? (float)$naimbif->gps_latitud : null;
            $lng = is_numeric($naimbif->gps_longitud) ? (float)$naimbif->gps_longitud : null;
            $coords = $this->resolveCoordinates($lat, $lng, $jajahan, 'naimbif', $naimbif->id);

            $inventories = $naimbif->inventoriTernakan ?: collect();
            $jumlahEkor = $inventories->sum('jumlah_baka');
            $totalNaimbifLembu += $jumlahEkor;

            $bakaList = [];
            $jenisList = ['Lembu Pedaging' => $jumlahEkor];

            foreach ($inventories as $inv) {
                $bName = ucfirst(strtolower(trim($inv->display_name ?: $inv->baka)));
                $bCount = (int)$inv->jumlah_baka;
                $bakaList[$bName] = ($bakaList[$bName] ?? 0) + $bCount;
            }

            $naimbifMarkers[] = [
                'id' => 'naimbif_' . $naimbif->id,
                'raw_id' => $naimbif->id,
                'modul' => 'NAIMbif',
                'modul_label' => 'NAIMbif (Ladang Bridlot)',
                'kategori' => 'naimbif',
                'nama_penternak' => $naimbif->nama,
                'nama_premis' => 'Ladang NAIMbif (' . ($naimbif->id_premis ?: $naimbif->no_rujukan) . ')',
                'no_kp' => $naimbif->no_kp ?? '-',
                'no_telefon' => $naimbif->no_telefon ?? '-',
                'jajahan' => $jajahan,
                'daerah' => '-',
                'mukim' => '-',
                'alamat' => $naimbif->alamat_ladang ?: ($naimbif->alamat_tetap ?: ($jajahan . ', Kelantan')),
                'lat' => $coords['lat'],
                'lng' => $coords['lng'],
                'jenis_ternakan_list' => ['Lembu Pedaging', 'Lembu'],
                'pecahan_ternakan' => $jenisList,
                'pecahan_baka' => $bakaList,
                'jumlah_ternakan' => $jumlahEkor,
                'kapasiti_ladang' => $jumlahEkor,
                'status' => $naimbif->status_negeri ?: $naimbif->status_permohonan,
                'url' => route('naimbif.admin.show', $naimbif->id),
            ];
        }

        // Gabungkan semua penanda lokasi
        $allMarkers = array_merge($epuMarkers, $eptrMarkers, $naimbifMarkers);

        // Kumpul senarai unik jenis ternakan untuk dropdown filter
        $availableLivestockTypes = [
            'Ruminan' => [
                'Lembu',
                'Kerbau',
                'Kambing',
                'Biri-biri',
                'Rusa',
                'Kuda',
                'Babi',
            ],
            'Unggas' => [
                'Ayam Pedaging',
                'Ayam Penelur',
                'Ayam Kampung',
                'Ayam',
                'Itik',
                'Burung Puyuh',
                'Angsa',
                'Burung Unta',
                'Unggas',
            ]
        ];

        $jajahanList = array_keys($this->jajahanCoordinates);

        // Statistik Jajahan Teramai
        $jajahanStats = collect($allMarkers)->groupBy('jajahan')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total_ternakan' => $group->sum('jumlah_ternakan'),
            ];
        })->sortByDesc('count');

        $topJajahan = $jajahanStats->keys()->first() ?? 'Kota Bharu';

        $stats = [
            'total_penternak' => count($allMarkers),
            'total_epu' => count($epuMarkers),
            'total_eptr' => count($eptrMarkers),
            'total_unggas' => $totalUnggas,
            'total_ruminan' => $totalRuminan,
            'top_jajahan' => $topJajahan,
        ];

        return view('peta.index', compact(
            'allMarkers',
            'availableLivestockTypes',
            'jajahanList',
            'stats',
            'jajahanStats'
        ));
    }

    /**
     * Selesaikan koordinat GPS yang sah dengan penyesuaian sedikit jika bertindih
     */
    protected function resolveCoordinates(?float $lat, ?float $lng, string $jajahan, string $type, int $id): array
    {
        // Jika koordinat sah dalam jajaran Malaysia/Kelantan (Lat ~ 4.0 - 7.0, Lng ~ 100.0 - 104.0)
        if ($lat && $lng && $lat >= 4.0 && $lat <= 7.5 && $lng >= 100.0 && $lng <= 104.5) {
            return [
                'lat' => round((float)$lat, 6),
                'lng' => round((float)$lng, 6),
            ];
        }

        // Cari pusat Jajahan
        $base = $this->jajahanCoordinates[$jajahan] ?? $this->jajahanCoordinates['Kota Bharu'];

        // Hasilkan sedikit variasi koordinat pseudo-rawak berdasarkan ID supaya tidak bertindih 100%
        $angle = ($id * 137.5) * (M_PI / 180); // golden ratio angle
        $radius = 0.006 + (($id % 15) * 0.0035); // jarak ~ 500m hingga 4km dari pusat

        $offsetLat = $radius * cos($angle);
        $offsetLng = $radius * sin($angle);

        // Jika EPU vs EPTR, beri anjakan sedikit
        if ($type === 'epu') {
            $offsetLat += 0.002;
            $offsetLng += 0.002;
        } else {
            $offsetLat -= 0.002;
            $offsetLng -= 0.002;
        }

        return [
            'lat' => round($base['lat'] + $offsetLat, 6),
            'lng' => round($base['lng'] + $offsetLng, 6),
        ];
    }
}
