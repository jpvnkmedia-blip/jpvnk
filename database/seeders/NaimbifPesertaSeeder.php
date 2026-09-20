<?php

namespace Database\Seeders;

use App\Models\NaimbifPermohonan;
use App\Models\NaimbifInventoriTernakan;
use App\Models\User;
use App\Models\Pemunya;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class NaimbifPesertaSeeder extends Seeder
{
    public function run(): void
    {
        $participants = [
            [
                'bil' => 1,
                'no_pendaftaran' => 'NB00064/BA0011',
                'id_premis' => 'BA0011',
                'jajahan' => 'Bachok',
                'nama' => 'MOHAMED BIN AWANG HAMAT',
                'alamat' => 'KG. AMAN TELONG, 16310 BACHOK.',
                'poskod' => '16310',
                'no_tel' => '017-9828974 / 016-9633709',
                'no_kp' => '640815035121',
                'keluasan' => 6.50,
                'pengalaman' => 8,
                'inventori' => [
                    ['baka' => 'CHAROLAIS', 'induk' => 10, 'dara' => 4, 'anak_b' => 2, 'pejantan' => 2, 'anak_j' => 1],
                    ['baka' => 'KEDAH KELANTAN', 'induk' => 8, 'dara' => 2, 'anak_b' => 1, 'pejantan' => 1, 'anak_j' => 2],
                ]
            ],
            [
                'bil' => 3,
                'no_pendaftaran' => 'NB00033/BA0007',
                'id_premis' => 'BA0007',
                'jajahan' => 'Bachok',
                'nama' => 'MOHAMAD BIN DOLLAH',
                'alamat' => 'KG. TERATAI, BERIS PANCHOR TAWANG, 16320 BACHOK.',
                'poskod' => '16320',
                'no_tel' => '013-9872752',
                'no_kp' => '680210035433',
                'keluasan' => 5.00,
                'pengalaman' => 10,
                'inventori' => [
                    ['baka' => 'LIMOUSIN', 'induk' => 8, 'dara' => 3, 'anak_b' => 1, 'pejantan' => 2, 'anak_j' => 2],
                    ['baka' => 'CHAROLAIS', 'induk' => 6, 'dara' => 2, 'anak_b' => 1, 'pejantan' => 1, 'anak_j' => 1],
                ]
            ],
            [
                'bil' => 4,
                'no_pendaftaran' => 'NB00087/PP0012',
                'id_premis' => 'PP0012',
                'jajahan' => 'Pasir Puteh',
                'nama' => 'CHE ABDUL AZIZ BIN CHE MUSTAPHA',
                'alamat' => 'NO. 8, KG. TELOSAN, 16800 PASIR PUTEH.',
                'poskod' => '16800',
                'no_tel' => '013-4636870',
                'no_kp' => '720512035887',
                'keluasan' => 8.00,
                'pengalaman' => 12,
                'inventori' => [
                    ['baka' => 'BELGIAN BLUE', 'induk' => 12, 'dara' => 5, 'anak_b' => 2, 'pejantan' => 2, 'anak_j' => 2],
                    ['baka' => 'KEDAH KELANTAN', 'induk' => 10, 'dara' => 3, 'anak_b' => 2, 'pejantan' => 1, 'anak_j' => 1],
                ]
            ],
            [
                'bil' => 5,
                'no_pendaftaran' => 'NB00078/KB0026',
                'id_premis' => 'KB0026',
                'jajahan' => 'Kota Bharu',
                'nama' => 'MOHAMMAD RIDZUAN BIN ABDUL HALIL',
                'alamat' => 'LOT 321, KG. BIAH, JALAN PEROL, 16010 KOTA BHARU.',
                'poskod' => '16010',
                'no_tel' => '017-7029858',
                'no_kp' => '850720035078',
                'keluasan' => 4.50,
                'pengalaman' => 6,
                'inventori' => [
                    ['baka' => "BLONDE D'AQUITAINE", 'induk' => 7, 'dara' => 3, 'anak_b' => 1, 'pejantan' => 1, 'anak_j' => 1],
                    ['baka' => 'CHAROLAIS', 'induk' => 5, 'dara' => 2, 'anak_b' => 1, 'pejantan' => 1, 'anak_j' => 1],
                ]
            ],
            [
                'bil' => 6,
                'no_pendaftaran' => 'NB00068/BA0015',
                'id_premis' => 'BA0015',
                'jajahan' => 'Bachok',
                'nama' => "MOHD ZAIN BIN PA'ADEK",
                'alamat' => 'KG. AMAN KANDIS, 16310 BACHOK.',
                'poskod' => '16310',
                'no_tel' => '011-10795694',
                'no_kp' => '650304035668',
                'keluasan' => 5.20,
                'pengalaman' => 9,
                'inventori' => [
                    ['baka' => 'CHAROLAIS', 'induk' => 9, 'dara' => 3, 'anak_b' => 2, 'pejantan' => 1, 'anak_j' => 1],
                    ['baka' => 'KEDAH KELANTAN', 'induk' => 6, 'dara' => 2, 'anak_b' => 1, 'pejantan' => 1, 'anak_j' => 1],
                ]
            ],
            [
                'bil' => 7,
                'no_pendaftaran' => 'NB00073/BA0020',
                'id_premis' => 'BA0020',
                'jajahan' => 'Bachok',
                'nama' => 'CHE NOH BIN MAT',
                'alamat' => 'KG. KUAU MELAWI, 16300 BACHOK.',
                'poskod' => '16300',
                'no_tel' => '013-9251282',
                'no_kp' => '591118035073',
                'keluasan' => 7.00,
                'pengalaman' => 15,
                'inventori' => [
                    ['baka' => 'LIMOUSIN', 'induk' => 11, 'dara' => 4, 'anak_b' => 2, 'pejantan' => 2, 'anak_j' => 2],
                    ['baka' => 'CHAROLAIS', 'induk' => 7, 'dara' => 2, 'anak_b' => 1, 'pejantan' => 1, 'anak_j' => 1],
                ]
            ],
            [
                'bil' => 9,
                'no_pendaftaran' => 'NB00075/BA0022',
                'id_premis' => 'BA0022',
                'jajahan' => 'Bachok',
                'nama' => 'ZAKARIA BIN MUSA',
                'alamat' => 'KG. GONG BATU, TAWANG, 16020 BACHOK.',
                'poskod' => '16020',
                'no_tel' => '019-9641396',
                'no_kp' => '710925035075',
                'keluasan' => 6.00,
                'pengalaman' => 11,
                'inventori' => [
                    ['baka' => 'BELGIAN BLUE', 'induk' => 8, 'dara' => 3, 'anak_b' => 1, 'pejantan' => 1, 'anak_j' => 2],
                    ['baka' => 'KEDAH KELANTAN', 'induk' => 9, 'dara' => 3, 'anak_b' => 2, 'pejantan' => 1, 'anak_j' => 1],
                ]
            ],
            [
                'bil' => 10,
                'no_pendaftaran' => 'NB00059/PM0002',
                'id_premis' => 'PM0002',
                'jajahan' => 'Pasir Mas',
                'nama' => 'SHAMUDDIN BIN MAT DAUD',
                'alamat' => "LOT 1429, KG. KEPAS TO'UBAN, 17060 PASIR MAS.",
                'poskod' => '17060',
                'no_tel' => '013-6392539',
                'no_kp' => '670414035059',
                'keluasan' => 9.50,
                'pengalaman' => 14,
                'inventori' => [
                    ['baka' => 'CHAROLAIS', 'induk' => 14, 'dara' => 5, 'anak_b' => 3, 'pejantan' => 2, 'anak_j' => 2],
                    ['baka' => 'LIMOUSIN', 'induk' => 8, 'dara' => 3, 'anak_b' => 1, 'pejantan' => 1, 'anak_j' => 1],
                ]
            ],
            [
                'bil' => 11,
                'no_pendaftaran' => 'NB00060/PM0003',
                'id_premis' => 'PM0003',
                'jajahan' => 'Pasir Mas',
                'nama' => 'MOHD HAKIMI BIN MAT JENIN',
                'alamat' => 'KG. PADANG HANGUS CHETOK, 17060 PASIR MAS.',
                'poskod' => '17060',
                'no_tel' => '018-2844252',
                'no_kp' => '880608035060',
                'keluasan' => 5.80,
                'pengalaman' => 7,
                'inventori' => [
                    ['baka' => "BLONDE D'AQUITAINE", 'induk' => 7, 'dara' => 3, 'anak_b' => 1, 'pejantan' => 1, 'anak_j' => 1],
                    ['baka' => 'KEDAH KELANTAN', 'induk' => 8, 'dara' => 2, 'anak_b' => 1, 'pejantan' => 1, 'anak_j' => 2],
                ]
            ],
            [
                'bil' => 12,
                'no_pendaftaran' => 'NB00102/TU0008',
                'id_premis' => 'TU0008',
                'jajahan' => 'Tumpat',
                'nama' => 'MOHD ROSLAN BIN MOHAMMAD NOR',
                'alamat' => 'KG. BENDANG LUAS, 16250 WAKAF BHARU.',
                'poskod' => '16250',
                'no_tel' => '019-9393760 / 017-9831657',
                'no_kp' => '740321035102',
                'keluasan' => 6.20,
                'pengalaman' => 10,
                'inventori' => [
                    ['baka' => 'CHAROLAIS', 'induk' => 10, 'dara' => 4, 'anak_b' => 2, 'pejantan' => 1, 'anak_j' => 1],
                    ['baka' => 'BELGIAN BLUE', 'induk' => 6, 'dara' => 2, 'anak_b' => 1, 'pejantan' => 1, 'anak_j' => 1],
                ]
            ],
            [
                'bil' => 14,
                'no_pendaftaran' => 'NB00094/BA0027',
                'id_premis' => 'BA0027',
                'jajahan' => 'Bachok',
                'nama' => 'MOHD SHAHIRAN BIN MOHD ASRI',
                'alamat' => 'LOT 110, KG. PANJANG, PERUPOK, 16500 BACHOK.',
                'poskod' => '16500',
                'no_tel' => '018-3944060 / 014-9663135 / 017-9022219',
                'no_kp' => '910819035094',
                'keluasan' => 4.00,
                'pengalaman' => 5,
                'inventori' => [
                    ['baka' => 'LIMOUSIN', 'induk' => 6, 'dara' => 2, 'anak_b' => 1, 'pejantan' => 1, 'anak_j' => 1],
                    ['baka' => 'CHAROLAIS', 'induk' => 5, 'dara' => 2, 'anak_b' => 1, 'pejantan' => 1, 'anak_j' => 1],
                ]
            ],
            [
                'bil' => 17,
                'no_pendaftaran' => 'NB00105/PM0006',
                'id_premis' => 'PM0006',
                'jajahan' => 'Pasir Mas',
                'nama' => 'HASSANUL AL-ANUAR BIN HUSIN',
                'alamat' => 'KG. BAYU LALANG, 17000 PASIR MAS.',
                'poskod' => '17000',
                'no_tel' => '013-9118818',
                'no_kp' => '790515035105',
                'keluasan' => 7.50,
                'pengalaman' => 8,
                'inventori' => [
                    ['baka' => 'CHAROLAIS', 'induk' => 12, 'dara' => 4, 'anak_b' => 2, 'pejantan' => 2, 'anak_j' => 1],
                    ['baka' => 'KEDAH KELANTAN', 'induk' => 8, 'dara' => 3, 'anak_b' => 1, 'pejantan' => 1, 'anak_j' => 2],
                ]
            ],
            [
                'bil' => 18,
                'no_pendaftaran' => 'NB00112/MA0010',
                'id_premis' => 'MA0010',
                'jajahan' => 'Machang',
                'nama' => 'ZOL BIN ABDULLAH',
                'alamat' => 'KG. CHERANG HANGUS, 18500 MACHANG.',
                'poskod' => '18500',
                'no_tel' => '012-3743927',
                'no_kp' => '630228035112',
                'keluasan' => 8.20,
                'pengalaman' => 16,
                'inventori' => [
                    ['baka' => 'BELGIAN BLUE', 'induk' => 10, 'dara' => 4, 'anak_b' => 2, 'pejantan' => 2, 'anak_j' => 1],
                    ['baka' => 'LIMOUSIN', 'induk' => 8, 'dara' => 3, 'anak_b' => 1, 'pejantan' => 1, 'anak_j' => 1],
                ]
            ],
            [
                'bil' => 19,
                'no_pendaftaran' => 'NB00119/BA0028',
                'id_premis' => 'BA0028',
                'jajahan' => 'Bachok',
                'nama' => 'NURUDDIN B AWANG',
                'alamat' => 'LOT 2462, KG CHABANG 3 MELAWI, 16310 BACHOK, KELANTAN',
                'poskod' => '16310',
                'no_tel' => '019-9859030',
                'no_kp' => '761105035119',
                'keluasan' => 5.50,
                'pengalaman' => 7,
                'inventori' => [
                    ['baka' => 'CHAROLAIS', 'induk' => 8, 'dara' => 3, 'anak_b' => 1, 'pejantan' => 1, 'anak_j' => 1],
                    ['baka' => 'KEDAH KELANTAN', 'induk' => 7, 'dara' => 2, 'anak_b' => 1, 'pejantan' => 1, 'anak_j' => 1],
                ]
            ],
            [
                'bil' => 20,
                'no_pendaftaran' => 'NB00120/BA0029',
                'id_premis' => 'BA0029',
                'jajahan' => 'Bachok',
                'nama' => 'MUHAMAD LUKMAN HAKIM B SULAIMAN',
                'alamat' => 'KG KUAL BESAR, GUNUNG BACHOK KELANTAN',
                'poskod' => '16300',
                'no_tel' => '011-26038026',
                'no_kp' => '940412035120',
                'keluasan' => 4.80,
                'pengalaman' => 4,
                'inventori' => [
                    ['baka' => "BLONDE D'AQUITAINE", 'induk' => 6, 'dara' => 2, 'anak_b' => 1, 'pejantan' => 1, 'anak_j' => 1],
                    ['baka' => 'CHAROLAIS', 'induk' => 5, 'dara' => 2, 'anak_b' => 1, 'pejantan' => 1, 'anak_j' => 1],
                ]
            ],
            [
                'bil' => 21,
                'no_pendaftaran' => 'NB00121/BA0030',
                'id_premis' => 'BA0030',
                'jajahan' => 'Bachok',
                'nama' => 'MOHD FADZIL BIN MERAH',
                'alamat' => 'LOT 1065 KAMPUNG KEMAYANG, TAWANG 16020 BACHOK KELANTAN',
                'poskod' => '16020',
                'no_tel' => '014-8281230 / 018-9596460',
                'no_kp' => '820930035121',
                'keluasan' => 6.00,
                'pengalaman' => 8,
                'inventori' => [
                    ['baka' => 'LIMOUSIN', 'induk' => 9, 'dara' => 3, 'anak_b' => 2, 'pejantan' => 1, 'anak_j' => 1],
                    ['baka' => 'KEDAH KELANTAN', 'induk' => 6, 'dara' => 2, 'anak_b' => 1, 'pejantan' => 1, 'anak_j' => 1],
                ]
            ],
            [
                'bil' => 22,
                'no_pendaftaran' => 'NB00122/PP0013',
                'id_premis' => 'PP0013',
                'jajahan' => 'Pasir Puteh',
                'nama' => 'MOHD MARZUKI BIN MOHD ZIN',
                'alamat' => 'P44, KAMPUNG RAJA DAGANG SEMERAK, 16700 PASIR PUTEH KELANTAN',
                'poskod' => '16700',
                'no_tel' => '011-59131818',
                'no_kp' => '780120035122',
                'keluasan' => 5.20,
                'pengalaman' => 6,
                'inventori' => [
                    ['baka' => 'CHAROLAIS', 'induk' => 8, 'dara' => 3, 'anak_b' => 1, 'pejantan' => 1, 'anak_j' => 1],
                    ['baka' => 'BELGIAN BLUE', 'induk' => 6, 'dara' => 2, 'anak_b' => 1, 'pejantan' => 1, 'anak_j' => 1],
                ]
            ],
            [
                'bil' => 23,
                'no_pendaftaran' => 'NB00123/PP0014',
                'id_premis' => 'PP0014',
                'jajahan' => 'Pasir Puteh',
                'nama' => 'MOHD ROSSAIRIEY BIN DERIS',
                'alamat' => 'KG CHERANG RUKU SEMERAK, 16700 PASIR PUTEH, KELANTAN',
                'poskod' => '16700',
                'no_tel' => '019-8044763',
                'no_kp' => '830707035123',
                'keluasan' => 7.00,
                'pengalaman' => 9,
                'inventori' => [
                    ['baka' => 'LIMOUSIN', 'induk' => 10, 'dara' => 4, 'anak_b' => 2, 'pejantan' => 2, 'anak_j' => 1],
                    ['baka' => 'KEDAH KELANTAN', 'induk' => 8, 'dara' => 2, 'anak_b' => 1, 'pejantan' => 1, 'anak_j' => 2],
                ]
            ],
            [
                'bil' => 24,
                'no_pendaftaran' => 'NB00124/KB0027',
                'id_premis' => 'KB0027',
                'jajahan' => 'Kota Bharu',
                'nama' => 'AHMAD FARIEZ BIN MOHAMED',
                'alamat' => 'LOT 671 LORONG MASJID BAUNG, JALAN PENGKALAN CHEPA 16100 KOTA BHARU KELANTAN',
                'poskod' => '16100',
                'no_tel' => '017-9265309',
                'no_kp' => '861015035124',
                'keluasan' => 5.00,
                'pengalaman' => 5,
                'inventori' => [
                    ['baka' => 'CHAROLAIS', 'induk' => 7, 'dara' => 3, 'anak_b' => 1, 'pejantan' => 1, 'anak_j' => 1],
                    ['baka' => "BLONDE D'AQUITAINE", 'induk' => 5, 'dara' => 2, 'anak_b' => 1, 'pejantan' => 1, 'anak_j' => 1],
                ]
            ],
            [
                'bil' => 25,
                'no_pendaftaran' => 'NB00125/PM0007',
                'id_premis' => 'PM0007',
                'jajahan' => 'Pasir Mas',
                'nama' => 'PERBADANAN KEMAJUAN PERTANIAN NEGERI KELANTAN',
                'alamat' => 'LOT 3142B, MUKIM TOK UBAN, DAERAH KANGKUNG, JAJAHAN PASIR MAS, KELANTAN',
                'poskod' => '17000',
                'no_tel' => '013-6405661',
                'no_kp' => '900101035125',
                'keluasan' => 25.00,
                'pengalaman' => 20,
                'inventori' => [
                    ['baka' => 'CHAROLAIS', 'induk' => 25, 'dara' => 10, 'anak_b' => 5, 'pejantan' => 4, 'anak_j' => 4],
                    ['baka' => 'BELGIAN BLUE', 'induk' => 20, 'dara' => 8, 'anak_b' => 4, 'pejantan' => 3, 'anak_j' => 3],
                    ['baka' => 'LIMOUSIN', 'induk' => 15, 'dara' => 5, 'anak_b' => 3, 'pejantan' => 2, 'anak_j' => 2],
                ]
            ],
            [
                'bil' => 26,
                'no_pendaftaran' => 'NB00126/KK0001',
                'id_premis' => 'KK0001',
                'jajahan' => 'Kuala Krai',
                'nama' => 'MOHAMMED FAIZAL BIN AHMAD',
                'alamat' => 'KG. SUNGAI SOK, 18000 KUALA KRAI, KELANTAN',
                'poskod' => '18000',
                'no_tel' => '019-9000026',
                'no_kp' => '840506035126',
                'keluasan' => 6.00,
                'pengalaman' => 7,
                'inventori' => [
                    ['baka' => 'CHAROLAIS', 'induk' => 9, 'dara' => 3, 'anak_b' => 1, 'pejantan' => 1, 'anak_j' => 1],
                    ['baka' => 'KEDAH KELANTAN', 'induk' => 8, 'dara' => 3, 'anak_b' => 2, 'pejantan' => 1, 'anak_j' => 1],
                ]
            ],
        ];

        $defaultPassword = Hash::make('password123');

        foreach ($participants as $pData) {
            $cleanIc = $pData['no_kp'];
            $cleanPhone = explode('/', $pData['no_tel'])[0];
            $cleanPhone = trim($cleanPhone);

            // 1. Cipta atau kemaskini User (role: penternak)
            $email = 'naimbif_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $pData['id_premis'])) . '@penternak.jpvnk.gov.my';
            $user = User::updateOrCreate(
                ['ic_number' => $cleanIc],
                [
                    'name' => $pData['nama'],
                    'email' => $email,
                    'password' => $defaultPassword,
                    'phone' => $cleanPhone,
                    'address' => $pData['alamat'],
                    'poskod' => $pData['poskod'],
                    'jajahan' => $pData['jajahan'],
                    'negeri' => 'Kelantan',
                    'role' => 'penternak',
                    'roles' => ['penternak'],
                    'status' => 'Aktif',
                ]
            );

            // 2. Cipta atau kemaskini Pemunya
            $pemunya = Pemunya::updateOrCreate(
                ['no_kp' => $cleanIc],
                [
                    'user_id' => $user->id,
                    'nama' => $pData['nama'],
                    'no_telefon' => $cleanPhone,
                    'alamat' => $pData['alamat'],
                    'poskod' => $pData['poskod'],
                    'jajahan' => $pData['jajahan'],
                ]
            );

            // 3. Cipta atau kemaskini NaimbifPermohonan
            $naimbif = NaimbifPermohonan::updateOrCreate(
                ['no_rujukan' => $pData['no_pendaftaran']],
                [
                    'user_id' => $user->id,
                    'pemunya_id' => $pemunya->id,
                    'nama' => $pData['nama'],
                    'no_kp' => $cleanIc,
                    'no_telefon' => $pData['no_tel'],
                    'alamat_tetap' => $pData['alamat'],
                    'poskod' => $pData['poskod'],
                    'jajahan' => $pData['jajahan'],
                    'pengalaman_menternak' => $pData['pengalaman'],
                    'status_penternakan' => 'Sepenuh Masa',
                    'pernah_kursus' => true,
                    'nama_kursus' => 'Kursus Pembiakan & Pengurusan Lembu Pedaging NAIMbif',
                    'anjuran_kursus' => 'Jabatan Perkhidmatan Veterinar Negeri Kelantan (JPVNK)',
                    'berminat_kursus_jpvnk' => true,
                    // Maklumat Asas Ladang
                    'alamat_ladang' => $pData['alamat'],
                    'poskod_ladang' => $pData['poskod'],
                    'jajahan_ladang' => $pData['jajahan'],
                    'status_tanah' => 'Sendiri',
                    'keluasan_tanah' => $pData['keluasan'],
                    'padang_ragut' => 'Ada',
                    'bilangan_pekerja' => 2,
                    // Maklumat Asas Ternakan
                    'punca_ternakan' => 'Beli',
                    'kaedah_pembiakan' => 'Asli',
                    'pengakuan_benar' => true,
                    'tarikh_permohonan' => Carbon::now()->subMonths(3)->toDateString(),
                    // Pejabat Jajahan
                    'id_premis' => $pData['id_premis'],
                    'status_kelengkapan' => 'Lengkap',
                    'syor_permohonan' => 'Disokong',
                    'pegawai_penyiasat' => 'Pegawai Veterinar Jajahan ' . $pData['jajahan'],
                    'tarikh_siasatan' => Carbon::now()->subMonths(2)->toDateString(),
                    'catatan_jajahan' => 'Kandang dan fasiliti ternakan lembu pedaging lengkap dan menepati piawaian NAIMbif.',
                    'tarikh_semakan_jajahan' => Carbon::now()->subMonths(2),
                    // Ulasan Negeri
                    'status_negeri' => 'Lulus',
                    'no_rujukan_negeri' => $pData['no_pendaftaran'],
                    'ulasan_negeri' => 'Permohonan diperakukan dan diluluskan sebagai Peserta Ladang Bridlot NAIMbif Negeri Kelantan.',
                    'pegawai_pelulus' => 'Pengarah Perkhidmatan Veterinar Negeri Kelantan',
                    'tarikh_kelulusan_negeri' => Carbon::now()->subMonths(1),
                    'status_permohonan' => 'Lulus',
                ]
            );

            // 4. Cipta Inventori Ternakan
            // Padam inventori lama jika ada untuk elak duplikasi
            NaimbifInventoriTernakan::where('naimbif_permohonan_id', $naimbif->id)->delete();

            foreach ($pData['inventori'] as $inv) {
                $totalBaka = ($inv['induk'] ?? 0) + ($inv['dara'] ?? 0) + ($inv['anak_b'] ?? 0) + ($inv['pejantan'] ?? 0) + ($inv['anak_j'] ?? 0);
                NaimbifInventoriTernakan::create([
                    'naimbif_permohonan_id' => $naimbif->id,
                    'baka' => $inv['baka'],
                    'betina_induk' => $inv['induk'] ?? 0,
                    'betina_dara' => $inv['dara'] ?? 0,
                    'betina_anak' => $inv['anak_b'] ?? 0,
                    'jantan_pejantan' => $inv['pejantan'] ?? 0,
                    'jantan_anak' => $inv['anak_j'] ?? 0,
                    'jumlah_baka' => $totalBaka,
                ]);
            }
        }
    }
}