<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pemunya;
use App\Models\Ternakan;
use App\Models\PindahMilik;
use App\Models\PembatalanTernakan;
use App\Models\PermitSembelihan;
use App\Models\RekodKelahiran;
use App\Models\ProgramKesihatan;
use App\Models\PawahPerjanjian;
use App\Models\PawahTernakan;
use App\Models\PawahRekodKelahiran;
use App\Models\PawahRekodKesihatan;
use App\Models\PawahPenyelesaian;
use App\Models\EpuLadang;
use App\Models\EpuPermohonan;
use App\Models\EpuPemeriksaan;
use App\Models\Course;
use App\Models\CourseApplication;
use App\Models\KlinikTemujanji;
use App\Models\KlinikRawatan;
use App\Models\InventoriItem;
use App\Models\InventoriTransaksi;
use App\Models\InventoriPinjaman;
use App\Models\InventoriPermohonan;
use App\Models\Kenderaan;
use App\Models\KenderaanTempahan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. PENGGUNA (10 ROLES)
        $password = Hash::make('password');

        $superAdmin = User::updateOrCreate(
            ['role' => 'super_admin'],
            [
                'name' => 'Mohd Hanif bin Ismail',
                'email' => 'hanif@dvs.gov.my',
                'ic_number' => '900729035413',
                'phone' => '019-9112233',
                'address' => 'Ibu Pejabat JPVNK, Jalan Kubang Kachang, 15200 Kota Bharu, Kelantan',
                'jajahan' => 'Kota Bharu',
                'auth_provider' => 'manual',
                'status' => 'Aktif',
                'password' => Hash::make('super@DVS5413'),
            ]
        );

        $adminPejabat = User::create([
            'name' => 'Pn. Noraini binti Che Mat',
            'email' => 'adminpejabat@veterinar.kelantan.gov.my',
            'ic_number' => '850320036622',
            'phone' => '019-9223344',
            'address' => 'Bahagian Pengurusan & Stor Peralatan Pejabat, Ibu Pejabat JPVNK Kota Bharu',
            'jajahan' => 'Kota Bharu',
            'role' => 'admin_pejabat',
            'auth_provider' => 'manual',
            'status' => 'Aktif',
            'password' => $password,
        ]);

        $adminUbat = User::create([
            'name' => 'Dr. Faridah binti Mat Zin',
            'email' => 'adminubat@veterinar.kelantan.gov.my',
            'ic_number' => '880404035510',
            'phone' => '019-9112233',
            'address' => 'Unit Farmasi & Stor Ubat Veterinar, Ibu Pejabat JPVNK Kota Bharu',
            'jajahan' => 'Kota Bharu',
            'role' => 'admin_ubat',
            'auth_provider' => 'manual',
            'status' => 'Aktif',
            'password' => $password,
        ]);

        $adminEptr = User::create([
            'name' => 'En. Wan Kamaruddin bin Wan Noh',
            'email' => 'admineptr@veterinar.kelantan.gov.my',
            'ic_number' => '831112035533',
            'phone' => '019-9334455',
            'address' => 'Unit Regulatori & EPTR, Pejabat Veterinar Negeri Kelantan',
            'jajahan' => 'Kota Bharu',
            'role' => 'admin_eptr',
            'auth_provider' => 'manual',
            'status' => 'Aktif',
            'password' => $password,
        ]);

        $adminProgram = User::create([
            'name' => 'Dr. Zulkifli bin Ismail',
            'email' => 'adminprogram@veterinar.kelantan.gov.my',
            'ic_number' => '790808035544',
            'phone' => '019-9445566',
            'address' => 'Bahagian Pembangunan Industri Ternakan & Skim Pawah, JPVNK',
            'jajahan' => 'Kota Bharu',
            'role' => 'admin_program',
            'auth_provider' => 'manual',
            'status' => 'Aktif',
            'password' => $password,
        ]);

        $adminEpu = User::create([
            'name' => 'En. Azman bin Mohd Noor',
            'email' => 'adminepu@veterinar.kelantan.gov.my',
            'ic_number' => '810614035555',
            'phone' => '019-9556677',
            'address' => 'Bahagian Regulatori Perladangan Unggas (EPU), JPVNK',
            'jajahan' => 'Kota Bharu',
            'role' => 'admin_epu',
            'auth_provider' => 'manual',
            'status' => 'Aktif',
            'password' => $password,
        ]);

        $adminKursus = User::create([
            'name' => 'Pn. Roslina binti Daud',
            'email' => 'adminkursus@veterinar.kelantan.gov.my',
            'ic_number' => '870425035566',
            'phone' => '019-9667788',
            'address' => 'Pusat Latihan Veterinar Kelantan, Bachok',
            'jajahan' => 'Bachok',
            'role' => 'admin_kursus',
            'auth_provider' => 'manual',
            'status' => 'Aktif',
            'password' => $password,
        ]);

        // Admin EPTR Jajahan (10 Jajahan di Kelantan)
        $adminJajahan = User::create([
            'name' => 'Dr. Nik Farhan bin Nik Hassan',
            'email' => 'adminjajahan@veterinar.kelantan.gov.my',
            'ic_number' => '840919035577',
            'phone' => '019-9778899',
            'address' => 'Pejabat Perkhidmatan Veterinar Jajahan Kota Bharu, Jalan Kubang Kachang, 15200 Kota Bharu',
            'jajahan' => 'Kota Bharu',
            'role' => 'admin_jajahan',
            'auth_provider' => 'manual',
            'status' => 'Aktif',
            'password' => $password,
        ]);

        User::create([
            'name' => 'Dr. Ahmad Zaki bin Ismail',
            'email' => 'admineptr.pasirmas@veterinar.kelantan.gov.my',
            'ic_number' => '850818035501',
            'phone' => '09-7909242',
            'address' => 'Pejabat Perkhidmatan Veterinar Jajahan Pasir Mas, Jalan Tasek, 17000 Pasir Mas',
            'jajahan' => 'Pasir Mas',
            'role' => 'admin_jajahan',
            'auth_provider' => 'manual',
            'status' => 'Aktif',
            'password' => $password,
        ]);

        User::create([
            'name' => 'Dr. Rosli bin Daud',
            'email' => 'admineptr.bachok@veterinar.kelantan.gov.my',
            'ic_number' => '820412035502',
            'phone' => '09-7788242',
            'address' => 'Pejabat Perkhidmatan Veterinar Jajahan Bachok, Jalan Kampung Nipah, 16300 Bachok',
            'jajahan' => 'Bachok',
            'role' => 'admin_jajahan',
            'auth_provider' => 'manual',
            'status' => 'Aktif',
            'password' => $password,
        ]);

        User::create([
            'name' => 'Dr. Wan Azman bin Wan Sulaiman',
            'email' => 'admineptr.tumpat@veterinar.kelantan.gov.my',
            'ic_number' => '810923035503',
            'phone' => '09-7257242',
            'address' => 'Pejabat Perkhidmatan Veterinar Jajahan Tumpat, Jalan Dato Bikam, 16200 Tumpat',
            'jajahan' => 'Tumpat',
            'role' => 'admin_jajahan',
            'auth_provider' => 'manual',
            'status' => 'Aktif',
            'password' => $password,
        ]);

        User::create([
            'name' => 'Dr. Mohd Yusof bin Othman',
            'email' => 'admineptr.pasirputeh@veterinar.kelantan.gov.my',
            'ic_number' => '830115035504',
            'phone' => '09-7866242',
            'address' => 'Pejabat Perkhidmatan Veterinar Jajahan Pasir Puteh, Jalan Nik Mat Saman, 16800 Pasir Puteh',
            'jajahan' => 'Pasir Puteh',
            'role' => 'admin_jajahan',
            'auth_provider' => 'manual',
            'status' => 'Aktif',
            'password' => $password,
        ]);

        User::create([
            'name' => 'Dr. Siti Aminah binti Razali',
            'email' => 'admineptr.machang@veterinar.kelantan.gov.my',
            'ic_number' => '870606035505',
            'phone' => '09-9751242',
            'address' => 'Pejabat Perkhidmatan Veterinar Jajahan Machang, Jalan Pejabat Pos, 18500 Machang',
            'jajahan' => 'Machang',
            'role' => 'admin_jajahan',
            'auth_provider' => 'manual',
            'status' => 'Aktif',
            'password' => $password,
        ]);

        User::create([
            'name' => 'Dr. Abdul Halim bin Hashim',
            'email' => 'admineptr.tanahmerah@veterinar.kelantan.gov.my',
            'ic_number' => '841120035506',
            'phone' => '09-9556242',
            'address' => 'Pejabat Perkhidmatan Veterinar Jajahan Tanah Merah, Jalan Kelantan, 17500 Tanah Merah',
            'jajahan' => 'Tanah Merah',
            'role' => 'admin_jajahan',
            'auth_provider' => 'manual',
            'status' => 'Aktif',
            'password' => $password,
        ]);

        User::create([
            'name' => 'Dr. Mohd Khairi bin Mansor',
            'email' => 'admineptr.jeli@veterinar.kelantan.gov.my',
            'ic_number' => '880714035507',
            'phone' => '09-9440242',
            'address' => 'Pejabat Perkhidmatan Veterinar Jajahan Jeli, Jalan Hospital, 17600 Jeli',
            'jajahan' => 'Jeli',
            'role' => 'admin_jajahan',
            'auth_provider' => 'manual',
            'status' => 'Aktif',
            'password' => $password,
        ]);

        User::create([
            'name' => 'Dr. Nurul Huda binti Salleh',
            'email' => 'admineptr.kualakrai@veterinar.kelantan.gov.my',
            'ic_number' => '860303035508',
            'phone' => '09-9666242',
            'address' => 'Pejabat Perkhidmatan Veterinar Jajahan Kuala Krai, Jalan Sultan Yahya Petra, 18000 Kuala Krai',
            'jajahan' => 'Kuala Krai',
            'role' => 'admin_jajahan',
            'auth_provider' => 'manual',
            'status' => 'Aktif',
            'password' => $password,
        ]);

        User::create([
            'name' => 'Dr. Muhammad Hafiz bin Kamaruddin',
            'email' => 'admineptr.guamusang@veterinar.kelantan.gov.my',
            'ic_number' => '891219035509',
            'phone' => '09-9121242',
            'address' => 'Pejabat Perkhidmatan Veterinar Jajahan Gua Musang, Tingkat 1, Wisma Persekutuan, 18300 Gua Musang',
            'jajahan' => 'Gua Musang',
            'role' => 'admin_jajahan',
            'auth_provider' => 'manual',
            'status' => 'Aktif',
            'password' => $password,
        ]);

        $adminKlinik = User::create([
            'name' => 'Dr. Sarah binti Mohd Zaki',
            'email' => 'adminklinik@veterinar.kelantan.gov.my',
            'ic_number' => '890812035588',
            'phone' => '019-9887766',
            'address' => 'Klinik Haiwan Kesayangan Ibu Pejabat JPVNK Kota Bharu',
            'jajahan' => 'Kota Bharu',
            'role' => 'admin_klinik',
            'auth_provider' => 'manual',
            'status' => 'Aktif',
            'password' => $password,
        ]);

        $kakitangan = User::create([
            'name' => 'En. Mohd Haziq bin Abdullah',
            'email' => 'staf@veterinar.kelantan.gov.my',
            'ic_number' => '950515035512',
            'phone' => '013-9876543',
            'address' => 'Bahagian Khidmat Pengurusan & Pentadbiran, JPVNK Kota Bharu',
            'jajahan' => 'Kota Bharu',
            'role' => 'staf',
            'auth_provider' => 'manual',
            'status' => 'Aktif',
            'password' => $password,
        ]);

        $penternak = User::create([
            'name' => 'Ahmad bin Ibrahim',
            'email' => 'penternak@gmail.com',
            'ic_number' => '780312035123',
            'phone' => '013-9201122',
            'address' => 'Kampung Padang Kala, Peringat, 16400 Kota Bharu, Kelantan',
            'jajahan' => 'Kota Bharu',
            'role' => 'penternak',
            'auth_provider' => 'manual',
            'status' => 'Aktif',
            'password' => $password,
        ]);

        $penternak2 = User::create([
            'name' => 'Hassan bin Abdullah',
            'email' => 'hassan.ternak@gmail.com',
            'ic_number' => '821005035443',
            'phone' => '014-8899112',
            'address' => 'Kampung Temangan, 18500 Machang, Kelantan',
            'jajahan' => 'Machang',
            'role' => 'penternak',
            'auth_provider' => 'manual',
            'status' => 'Aktif',
            'password' => $password,
        ]);

        $usahawan = User::create([
            'name' => 'Siti Hajar binti Yusoff',
            'email' => 'usahawan@gmail.com',
            'ic_number' => '890214035678',
            'phone' => '011-12345678',
            'address' => 'Lot 4522, Mukim Gunong, 16090 Bachok, Kelantan',
            'jajahan' => 'Bachok',
            'role' => 'usahawan',
            'auth_provider' => 'google',
            'status' => 'Aktif',
            'password' => $password,
        ]);

        $orangAwam = User::create([
            'name' => 'Mohd Razak bin Ismail',
            'email' => 'awam@gmail.com',
            'ic_number' => '950101035999',
            'phone' => '017-9876543',
            'address' => 'No 12, Taman Desa Kemumin, Pengkalan Chepa, 16100 Kota Bharu, Kelantan',
            'jajahan' => 'Kota Bharu',
            'role' => 'orang_awam',
            'auth_provider' => 'mydigital_id',
            'status' => 'Aktif',
            'password' => $password,
        ]);

        // 2. PEMUNYA & EPTR TERNAKAN
        $pemunya1 = Pemunya::create([
            'user_id' => $penternak->id,
            'nama' => $penternak->name,
            'no_kp' => $penternak->ic_number,
            'no_telefon' => $penternak->phone,
            'alamat' => $penternak->address,
            'jajahan' => 'Kota Bharu',
            'daerah' => 'Peringat',
            'poskod' => '16400',
            'status' => 'Aktif',
        ]);

        $pemunya2 = Pemunya::create([
            'user_id' => $penternak2->id,
            'nama' => $penternak2->name,
            'no_kp' => $penternak2->ic_number,
            'no_telefon' => $penternak2->phone,
            'alamat' => $penternak2->address,
            'jajahan' => 'Machang',
            'daerah' => 'Temangan',
            'poskod' => '18500',
            'status' => 'Aktif',
        ]);

        $pemunyaJpvnk = Pemunya::create([
            'user_id' => $superAdmin->id,
            'nama' => 'Jabatan Perkhidmatan Veterinar Negeri Kelantan',
            'no_kp' => 'GOV-JPVNK-01',
            'no_telefon' => '09-7445566',
            'alamat' => 'Ibu Pejabat Perkhidmatan Veterinar Negeri Kelantan, Jalan Kubang Kachang',
            'jajahan' => 'Kota Bharu',
            'daerah' => 'Bandar',
            'poskod' => '15100',
            'status' => 'Aktif',
        ]);

        // Lembu EPTR dengan kolum PROGRAM
        $lembu1 = Ternakan::create([
            'pemunya_id' => $pemunyaJpvnk->id, // Hak milik Jabatan bila dipawah!
            'no_tag' => 'PRG-0001',
            'jenis_ternakan' => 'lembu',
            'baka' => 'brahman',
            'baka_pejantan' => 'brahman',
            'baka_induk' => 'kedah-kelantan',
            'no_tanda_pengenalan_induk' => 'TIADA',
            'jantina' => 'Betina',
            'umur' => '3 Tahun',
            'tarikh_lahir' => '2023-03-15',
            'warna' => 'Kelabu Keputihan',
            'tanda_badan' => 'Kuping telinga kanan berlubang tanda vaksin',
            'tujuan_ternakan' => 'Pembiakan',
            'lokasi_kandang' => 'Kandang A, Padang Kala Peringat',
            'jajahan' => 'Kota Bharu',
            'daerah' => 'Peringat',
            'poskod' => '16400',
            'program' => 'Program Pawah Ternakan Negeri Kelantan', // KOLUM PROGRAM
            'status' => 'Pawah',
            'status_kelulusan' => 'Diluluskan',
            'tarikh_daftar' => '2024-01-10',
            'no_siri_kad_kuning' => 'DB-KB-2024-00101',
            'qr_code' => 'QR-EPTR-PRG-0001',
            'catatan' => 'Induk lembu pawah sihat dan subur',
            'didaftar_oleh' => $adminEptr->id,
            'diluluskan_oleh' => $adminEptr->id,
        ]);

        $lembu2 = Ternakan::create([
            'pemunya_id' => $pemunya1->id,
            'no_tag' => 'PRG-0002',
            'jenis_ternakan' => 'lembu',
            'baka' => 'kedah-kelantan',
            'baka_pejantan' => 'kedah-kelantan',
            'baka_induk' => 'kedah-kelantan',
            'no_tanda_pengenalan_induk' => 'TIADA',
            'jantina' => 'Jantan',
            'umur' => '2 Tahun 6 Bulan',
            'tarikh_lahir' => '2023-09-20',
            'warna' => 'Coklat Gelap',
            'tanda_badan' => 'Tompok putih pada kaki belakang',
            'tujuan_ternakan' => 'Pedaging',
            'lokasi_kandang' => 'Kandang B, Padang Kala Peringat',
            'jajahan' => 'Kota Bharu',
            'daerah' => 'Peringat',
            'poskod' => '16400',
            'program' => 'Tiada', // Tiada program bantuan
            'status' => 'Aktif',
            'status_kelulusan' => 'Diluluskan',
            'tarikh_daftar' => '2024-04-12',
            'no_siri_kad_kuning' => 'DB-KB-2024-00102',
            'qr_code' => 'QR-EPTR-PRG-0002',
            'catatan' => 'Pendaftaran persendirian penternak',
            'didaftar_oleh' => $adminEptr->id,
            'diluluskan_oleh' => $adminEptr->id,
        ]);

        $lembu3 = Ternakan::create([
            'pemunya_id' => $pemunya2->id,
            'no_tag' => 'TMG-0001',
            'jenis_ternakan' => 'lembu',
            'baka' => 'charolais',
            'baka_pejantan' => 'charolais',
            'baka_induk' => 'kedah-kelantan',
            'no_tanda_pengenalan_induk' => 'TIADA',
            'jantina' => 'Betina',
            'umur' => '2 Tahun',
            'tarikh_lahir' => '2024-02-10',
            'warna' => 'Kuning Krim',
            'tanda_badan' => 'Tanduk melengkung sedikit ke atas',
            'tujuan_ternakan' => 'Pembiakan',
            'lokasi_kandang' => 'Kandang Temangan Machang',
            'jajahan' => 'Machang',
            'daerah' => 'Temangan',
            'poskod' => '18500',
            'program' => 'Skim Bantuan Baka Induk Pedaging', // Program Bantuan
            'status' => 'Aktif',
            'status_kelulusan' => 'Diluluskan',
            'tarikh_daftar' => '2024-06-01',
            'no_siri_kad_kuning' => 'DB-MC-2024-00201',
            'qr_code' => 'QR-EPTR-TMG-0001',
            'catatan' => 'Baka hibrid berkualiti tinggi',
            'didaftar_oleh' => $adminJajahan->id,
            'diluluskan_oleh' => $adminJajahan->id,
        ]);

        $kambing1 = Ternakan::create([
            'pemunya_id' => $pemunya1->id,
            'no_tag' => 'PRG-0003',
            'jenis_ternakan' => 'kambing',
            'baka' => 'boer,cap',
            'baka_pejantan' => 'boer,cap',
            'baka_induk' => 'katjang',
            'no_tanda_pengenalan_induk' => 'TIADA',
            'jantina' => 'Jantan',
            'umur' => '1 Tahun 8 Bulan',
            'tarikh_lahir' => '2024-07-05',
            'warna' => 'Kepala Coklat Badan Putih',
            'tanda_badan' => 'Tiada tanda khas',
            'tujuan_ternakan' => 'Pembiakan',
            'lokasi_kandang' => 'Kandang Kambing Padang Kala',
            'jajahan' => 'Kota Bharu',
            'daerah' => 'Peringat',
            'poskod' => '16400',
            'program' => 'Tiada',
            'status' => 'Aktif',
            'status_kelulusan' => 'Diluluskan',
            'tarikh_daftar' => '2025-01-15',
            'no_siri_kad_kuning' => 'DB-KB-2025-00103',
            'qr_code' => 'QR-EPTR-PRG-0003',
            'catatan' => 'Baka pejantan aktif',
            'didaftar_oleh' => $adminEptr->id,
            'diluluskan_oleh' => $adminEptr->id,
        ]);

        // Ternakan Permohonan Baru (Menunggu Kelulusan Admin Jajahan untuk Jana No. Tag)
        $lembuPending = Ternakan::create([
            'pemunya_id' => $pemunya1->id,
            'no_tag' => null, // Belum dibuat sehingga diluluskan
            'jenis_ternakan' => 'lembu',
            'baka' => 'angus',
            'baka_pejantan' => 'angus',
            'baka_induk' => 'kedah-kelantan',
            'no_tanda_pengenalan_induk' => 'TIADA',
            'jantina' => 'Jantan',
            'umur' => '1 Tahun 2 Bulan',
            'tarikh_lahir' => '2025-07-10',
            'warna' => 'Hitam Pekat',
            'tanda_badan' => 'Tiada tanda khas',
            'tujuan_ternakan' => 'Pedaging',
            'lokasi_kandang' => 'Kandang Padang Kala Peringat',
            'jajahan' => 'Kota Bharu',
            'daerah' => 'Peringat',
            'poskod' => '16400',
            'program' => 'Tiada',
            'status' => 'Menunggu',
            'status_kelulusan' => 'Menunggu',
            'tarikh_daftar' => null,
            'no_siri_kad_kuning' => null,
            'qr_code' => null,
            'catatan' => 'Permohonan pendaftaran baru oleh penternak, menunggu kelulusan admin jajahan.',
            'didaftar_oleh' => $penternak->id,
        ]);

        // Rekod Kelahiran Anak Ternakan
        RekodKelahiran::create([
            'induk_id' => $lembu1->id,
            'pejantan_id' => null,
            'anak_ternakan_id' => $lembu2->id,
            'pemunya_id' => $pemunya1->id,
            'no_tag_sementara' => $lembu2->no_tag,
            'jantina_anak' => 'Jantan',
            'tarikh_kelahiran' => '2023-09-20',
            'berat_lahir_kg' => 28.5,
            'baka_anak' => 'kedah-kelantan',
            'warna_anak' => 'Coklat Gelap',
            'tanda_badan_anak' => 'Tompok putih pada kaki belakang',
            'status_kelahiran' => 'Hidup',
            'keadaan_anak' => 'Cergas',
            'catatan' => 'Kelahiran anak sulung baka KK dalam keadaan normal dan sihat.',
            'didaftar_oleh' => $adminEptr->id,
        ]);

        // Rekod Program Kesihatan Ternakan
        ProgramKesihatan::create([
            'ternakan_id' => $lembu1->id,
            'no_rujukan_kesihatan' => 'MED-KB-2026-00101',
            'jenis_program' => 'Vaksinasi / Imunisasi',
            'nama_vaksin_atau_ubat' => 'Vaksin FMD (Aftovax Bivalent)',
            'tarikh_rawatan' => '2026-08-10',
            'tarikh_ulangan_dos' => Carbon::now()->addDays(20)->toDateString(),
            'berat_semasa_kg' => 380.0,
            'suhu_badan_celsius' => 38.6,
            'status_kesihatan' => 'Sihat & Cergas',
            'dos_diberikan' => '2.0 ml Subkutan (SC)',
            'diagnosis_atau_tujuan' => 'Program Imunisasi Pencegahan Penyakit Kuku dan Mulut (FMD) Zon Kota Bharu',
            'tindakan_rawatan' => 'Suntikan vaksinasi pencegahan FMD pada bahagian tengkuk kiri',
            'pegawai_pemeriksa' => 'Dr. Mohd Fauzi bin Abdullah',
            'jajahan' => 'Kota Bharu',
            'lokasi_pemeriksaan' => 'Kandang A, Padang Kala Peringat',
            'catatan_dan_syor' => 'Ternakan bertindak balas baik, tiada alahan dikesan. Dos ulangan dijadualkan bulan hadapan.',
            'didaftar_oleh' => $adminEptr->id,
        ]);

        ProgramKesihatan::create([
            'ternakan_id' => $lembu2->id,
            'no_rujukan_kesihatan' => 'MED-KB-2026-00102',
            'jenis_program' => 'Penyahcacingan (Deworming)',
            'nama_vaksin_atau_ubat' => 'Ivermectin 1% Injectable',
            'tarikh_rawatan' => '2026-08-15',
            'tarikh_ulangan_dos' => Carbon::now()->addDays(75)->toDateString(),
            'berat_semasa_kg' => 295.0,
            'suhu_badan_celsius' => 38.4,
            'status_kesihatan' => 'Sihat & Cergas',
            'dos_diberikan' => '6.0 ml Subkutan (SC)',
            'diagnosis_atau_tujuan' => 'Kawalan parasit dalaman (cacing usus) dan ektoparasit rutin',
            'tindakan_rawatan' => 'Suntikan Ivermectin mengikut nisbah berat badan',
            'pegawai_pemeriksa' => 'En. Wan Kamaruddin bin Wan Noh',
            'jajahan' => 'Kota Bharu',
            'lokasi_pemeriksaan' => 'Kandang B, Padang Kala Peringat',
            'catatan_dan_syor' => 'Selera makan baik, badan berisi dan aktif.',
            'didaftar_oleh' => $adminEptr->id,
        ]);

        ProgramKesihatan::create([
            'ternakan_id' => $lembu3->id,
            'no_rujukan_kesihatan' => 'MED-MC-2026-00201',
            'jenis_program' => 'Rawatan Penyakit / Klinikal',
            'nama_vaksin_atau_ubat' => 'Oxytetracycline LA 20% & Flunixin Meglumine',
            'tarikh_rawatan' => '2026-08-20',
            'tarikh_ulangan_dos' => null,
            'berat_semasa_kg' => 410.0,
            'suhu_badan_celsius' => 39.2,
            'status_kesihatan' => 'Sembuh',
            'dos_diberikan' => '20.0 ml Intramuskular (IM)',
            'diagnosis_atau_tujuan' => 'Luka kecederaan ringan pada bahagian kaki hadapan kanan akibat geseran pagar',
            'tindakan_rawatan' => 'Pembersihan luka dengan antiseptik povidone iodine, semburan antibakteria dan suntikan antibiotik',
            'pegawai_pemeriksa' => 'Dr. Zulkifli bin Ismail',
            'jajahan' => 'Machang',
            'lokasi_pemeriksaan' => 'Kandang Temangan Machang',
            'catatan_dan_syor' => 'Luka telah kering sepenuhnya, ternakan pulih dan boleh bergerak normal.',
            'didaftar_oleh' => $adminJajahan->id,
        ]);

        // EPTR Borang D: Permit Sembelihan
        PermitSembelihan::create([
            'no_permit' => 'PS-KB-2026-0001',
            'pemunya_id' => $pemunya1->id,
            'ternakan_id' => $lembu2->id,
            'tujuan_sembelih' => 'Kenduri Kahwin & Jamuan Keluarga',
            'tarikh_sembelih' => Carbon::now()->addDays(5)->toDateString(),
            'lokasi_sembelih' => 'Rumah Sembelihan Berlesen Kota Bharu',
            'no_resit_bayaran' => 'RES-2026-8812',
            'kadar_bayaran' => 10.00,
            'status_kelulusan' => 'Diluluskan',
            'diluluskan_oleh' => $adminEptr->id,
            'catatan' => 'Pemeriksaan fizikal sebelum sembelih mendapati ternakan sihat',
        ]);

        // 3. PROGRAM PAWAH (Mengaitkan Lembu EPTR)
        $perjanjianPawah = PawahPerjanjian::create([
            'user_id' => $penternak->id,
            'no_perjanjian' => 'JPVNK/PAWAH/KB/2024/005',
            'nama_program' => 'Program Bantuan Pawah Lembu Hibrid Dun Kadok',
            'jenis_pawah' => 'Lembu Hibrid',
            'jenis_ternakan_sedia_ada' => 'Lembu Pedaging',
            'bilangan_ternakan_sedia_ada' => 12,
            'tarikh_mula' => '2024-01-15',
            'tarikh_tamat' => '2027-01-14',
            'tempoh_tahun' => 3,
            'bilangan_induk' => 1,
            'syarat_pemulangan' => 'Peserta wajib memulangkan 1 ekor anak betina pertama berumur sekurang-kurangnya 12 bulan kepada Jabatan untuk diagihkan kepada peserta lain.',
            'jajahan' => 'Kota Bharu',
            'status' => 'Aktif',
            'pegawai_penyelia' => 'Dr. Zulkifli bin Ismail',
            'catatan' => 'Peserta berpengalaman dan memiliki kandang mengikut spesifikasi jabatan.',
        ]);

        PawahTernakan::create([
            'pawah_perjanjian_id' => $perjanjianPawah->id,
            'ternakan_id' => $lembu1->id, // Rujukan kepada lembu EPTR!
            'status_induk' => 'Telah Melahirkan',
            'tarikh_serahan' => '2024-01-20',
            'catatan' => 'Induk dalam keadaan sihat semasa diserahkan.',
        ]);

        // Rekod Kelahiran Anak Pawah
        PawahRekodKelahiran::create([
            'pawah_perjanjian_id' => $perjanjianPawah->id,
            'ternakan_induk_id' => $lembu1->id,
            'no_tag_anak' => 'KB-2026-PAWAH-01',
            'jantina_anak' => 'Betina',
            'tarikh_kelahiran' => '2025-02-14',
            'berat_lahir_kg' => 26.50,
            'baka_bapa' => 'Charolais',
            'warna' => 'Coklat Putih',
            'status_anak' => 'Dalam Peliharaan',
            'catatan' => 'Anak betina pertama membesar dengan baik dan sihat.',
        ]);

        // Rekod Pemantauan Kesihatan Pawah
        PawahRekodKesihatan::create([
            'pawah_perjanjian_id' => $perjanjianPawah->id,
            'ternakan_id' => $lembu1->id,
            'tarikh_lawatan' => '2025-08-10',
            'status_fizikal' => 'Baik',
            'status_bunting' => false,
            'diagnosis' => 'Pemeriksaan rutin selepas kelahiran. Tiada tanda jangkitan.',
            'rawatan_diberikan' => 'Suntikan vitamin dan ubat cacing (Albendazole).',
            'pegawai_pemeriksa' => 'Dr. Zulkifli bin Ismail',
            'syor_tindakan' => 'Kekalkan rumput napier dan dedak mencukupi.',
        ]);

        // Permohonan Pawah dari Orang Awam (Menunggu Kelulusan)
        $perjanjianPawahAwam = PawahPerjanjian::create([
            'user_id' => $orangAwam->id,
            'no_perjanjian' => 'PW-MOHON-20260906-088',
            'nama_program' => 'Program Pawah Kambing Tenusu Negeri Kelantan (Permohonan Awam)',
            'jenis_pawah' => 'Kambing Tenusu',
            'jenis_ternakan_sedia_ada' => 'Kambing Kampung',
            'bilangan_ternakan_sedia_ada' => 5,
            'tarikh_mula' => Carbon::now()->toDateString(),
            'tarikh_tamat' => Carbon::now()->addYears(3)->toDateString(),
            'tempoh_tahun' => 3,
            'bilangan_induk' => 1,
            'syarat_pemulangan' => 'Memulangkan 1 (satu) ekor anak betina pertama berumur sekurang-kurangnya 12 bulan kepada Jabatan Perkhidmatan Veterinar Negeri Kelantan.',
            'jajahan' => 'Pasir Mas',
            'status' => 'Menunggu Kelulusan',
            'pegawai_penyelia' => 'Pegawai Pawah Jajahan Pasir Mas',
            'catatan' => 'Jenis Pawah Dimohon: Kambing Tenusu | Jenis Ternakan Sekarang: Kambing Kampung | Ternakan Sedia Ada: 5 Ekor | Pengalaman Menternak: 1 - 3 Tahun | Keluasan Padang Ragut: 1.5 Ekar | Jenis Kandang: Kandang Separa Tertutup (Semi-intensive) | Sumber Makanan: Rumput Napier & Dedak | Catatan Tambahan: Pemohon mempunyai kandang siap dan bersedia menerima ternakan.',
        ]);

        // 4. EPU (ENAKMEN PERLADANGAN UNGGAS)
        $ladangEpu = EpuLadang::create([
            'user_id' => $usahawan->id,
            'nama_pemohon_atau_syarikat' => 'Siti Hajar Unggas Enterprise',
            'no_syarikat_atau_ssm' => '202303124589 (KT0458921-X)',
            'nama_ladang' => 'Ladang Ayam Bersih Gunong',
            'no_geran_tanah' => 'GRN 88412',
            'no_lot' => 'Lot 1420',
            'luas_tanah_ekar' => 3.50,
            'jajahan' => 'Bachok',
            'mukim' => 'Gunong',
            'alamat_ladang' => 'Lot 1420, Jalan Jelawat-Gunong, 16090 Bachok, Kelantan',
            'status_pemilikan_tanah' => 'Milik Sendiri',
            'sistem_reban' => 'Tertutup',
            'kapasiti_maksimum_unggas' => 25000,
            'jarak_kediaman_terdekat_meter' => 250,
            'jarak_sungai_terdekat_meter' => 400,
            'kaedah_kawalan_lalat_bau' => 'Penggunaan mikrob efektif (EM) dan semburan larvisid berkala setiap minggu',
            'kaedah_pelupusan_tinja' => 'Difermentasikan menjadi baja organik di bangsal bertutup',
            'kaedah_pelupusan_bangkai' => 'Lubang bangkai berpenutup simen mengikut SOP veterinar',
            'status_ladang' => 'Aktif',
        ]);

        $epuPermohonan = EpuPermohonan::create([
            'epu_ladang_id' => $ladangEpu->id,
            'no_rujukan_permohonan' => 'EPU/BCK/2025/001',
            'jenis_permohonan' => 'Baru',
            'jenis_unggas' => 'Ayam Pedaging (Broiler)',
            'bilangan_semasa_unggas' => 20000,
            'no_lesen_epu' => 'EPU-BCK-2025-0042',
            'tarikh_mula_lesen' => '2025-01-01',
            'tarikh_tamat_lesen' => '2025-12-31',
            'yuran_lesen' => 250.00,
            'no_resit_bayaran' => 'RES-EPU-2025-019',
            'status' => 'Diluluskan',
            'syarat_khas_lesen' => '1. Memastikan zon penampan sentiasa diselenggara.\n2. Tiada pelepasan air basuhan ke parit awam tanpa rawatan.',
            'catatan_pegawai' => 'Ladang mematuhi semua kriteria Enakmen Perladangan Unggas 2005.',
            'diluluskan_oleh' => $adminEpu->id,
            'tarikh_kelulusan' => '2025-01-05',
        ]);

        // EPU Borang D: Pemeriksaan Lapangan
        EpuPemeriksaan::create([
            'epu_ladang_id' => $ladangEpu->id,
            'epu_permohonan_id' => $epuPermohonan->id,
            'pegawai_id' => $adminEpu->id,
            'tarikh_pemeriksaan' => '2025-07-15',
            'skor_kebersihan_peratus' => 92,
            'patuh_zon_penampan' => true,
            'kawalan_lalat_memuaskan' => true,
            'kawalan_bau_memuaskan' => true,
            'sistem_longkang_sempurna' => true,
            'penemuan_pemeriksaan' => 'Keadaan reban tertutup berada dalam keadaan amat memuaskan. Kipas pengudaraan berfungsi optimum dan tiada bau menyengat.',
            'syor_dan_arahan' => 'Teruskan amalan pengurusan biosekuriti yang cemerlang.',
            'status_keputusan' => 'Lulus',
        ]);

        // 5. KURSUS TERNAKAN
        $kursus1 = Course::create([
            'title' => 'Kursus Pengurusan & Pembiakan Lembu Pedaging Moden (EPTR & Biosekuriti)',
            'code' => 'KURSUS-RUM-2026-01',
            'category' => 'Ruminan',
            'description' => 'Kursus komprehensif merangkumi pengurusan fidlot, pemakanan formulasi rumput silaj, rekod EPTR dan suntikan vaksin asas.',
            'trainer_name' => 'Dr. Mohd Fauzi & Pegawai Kanan JPVNK',
            'start_date' => Carbon::now()->addDays(10)->toDateString(),
            'end_date' => Carbon::now()->addDays(11)->toDateString(),
            'time' => '8:30 Pagi - 4:30 Petang',
            'location' => 'Dewan Syarahan Pusat Latihan Veterinar, Bachok',
            'jajahan' => 'Bachok',
            'capacity' => 40,
            'registered_count' => 15,
            'fee' => 0.00,
            'status' => 'Buka',
            'created_by' => $adminKursus->id,
        ]);

        $kursus2 = Course::create([
            'title' => 'Bengkel Pengurusan Reban Unggas Tertutup & Pematuhan EPU 2024',
            'code' => 'KURSUS-UNG-2026-02',
            'category' => 'Unggas',
            'description' => 'Panduan teknikal reka bentuk reban tertutup, kawalan suhu pintar, pengurusan sisa tinja dan pematuhan lesen EPU.',
            'trainer_name' => 'En. Azman & Pakar Industri Unggas',
            'start_date' => Carbon::now()->addDays(20)->toDateString(),
            'end_date' => Carbon::now()->addDays(20)->toDateString(),
            'time' => '9:00 Pagi - 5:00 Petang',
            'location' => 'Auditorium Ibu Pejabat JPVNK Kota Bharu',
            'jajahan' => 'Kota Bharu',
            'capacity' => 50,
            'registered_count' => 28,
            'fee' => 0.00,
            'status' => 'Buka',
            'created_by' => $adminKursus->id,
        ]);

        // Permohonan Kursus
        CourseApplication::create([
            'course_id' => $kursus1->id,
            'user_id' => $penternak->id,
            'registration_number' => 'REG-2026-0014',
            'status' => 'Disahkan',
            'certificate_number' => 'SIJIL-JPVNK-2026-0014',
            'certificate_issued_at' => Carbon::now()->toDateString(),
        ]);

        CourseApplication::create([
            'course_id' => $kursus2->id,
            'user_id' => $kakitangan->id,
            'registration_number' => 'REG-2026-0025',
            'status' => 'Disahkan',
            'certificate_number' => 'SIJIL-JPVNK-2026-0025',
            'certificate_issued_at' => Carbon::now()->toDateString(),
        ]);

        // 6. KLINIK HAIWAN
        $temujanji1 = KlinikTemujanji::create([
            'user_id' => $orangAwam->id,
            'no_temujanji' => 'TJ-KB-2026-0088',
            'jenis_haiwan' => 'Kucing',
            'nama_haiwan' => 'Comel',
            'baka' => 'Domestic Short Hair (DSH)',
            'jantina_haiwan' => 'Betina',
            'umur_haiwan' => '1 Tahun 2 Bulan',
            'simptom_atau_tujuan' => 'Pemeriksaan kesihatan tahunan, vaksinasi core (FVRCP) dan permohonan pembedahan kembiri.',
            'tarikh_temujanji' => Carbon::now()->addDays(2)->toDateString(),
            'sesi' => 'Pagi (8:30 AM - 12:30 PM)',
            'klinik_jajahan' => 'Klinik Haiwan Kesayangan Ibu Pejabat JPVNK Kota Bharu',
            'status' => 'Disahkan',
            'catatan_pegawai' => 'Sila bawa buku rekod kesihatan haiwan terdahulu.',
        ]);

        KlinikRawatan::create([
            'klinik_temujanji_id' => $temujanji1->id,
            'user_id' => $orangAwam->id,
            'no_rekod_rawatan' => 'RAW-2026-0045',
            'tarikh_rawatan' => Carbon::now()->toDateString(),
            'pegawai_veterinar' => 'Dr. Nik Farhan bin Nik Hassan',
            'berat_badan_kg' => 3.40,
            'suhu_celsius' => 38.5,
            'diagnosis' => 'Keadaan kucing sihat, tiada parasit luar, suhu normal.',
            'rawatan_diberikan' => 'Vaksinasi 3-in-1 (Felocell 3) dan ubat cacing spot-on.',
            'ubat_diberikan' => 'Revolution Plus Spot-On',
            'vaksinasi' => 'Felocell 3 Booster',
            'tarikh_temujanji_susulan' => Carbon::now()->addMonths(1)->toDateString(),
            'kos_rawatan' => 45.00,
            'status_bayaran' => 'Selesai Bayar',
            'nasihat_veterinar' => 'Kucing perlu dipantau 24 jam selepas vaksinasi untuk sebarang alahan.',
        ]);

        // 7A. STOR PERALATAN PEJABAT (Diuruskan oleh Admin Pejabat)
        $pejabatItem1 = InventoriItem::create([
            'kod_item' => 'PJB-KTS-001',
            'nama_item' => 'Kertas Fotostat A4 IK Yellow 70gsm (1 Kotak / 5 Rim)',
            'jenis_stor' => 'pejabat',
            'kategori' => 'Alat Tulis & Pejabat',
            'unit' => 'Kotak',
            'kuantiti_semasa' => 85,
            'kuantiti_minimum' => 20,
            'harga_seunit' => 62.50,
            'pembekal_utama' => 'Percetakan & Alat Tulis Kelantan Sdn Bhd',
            'lokasi_rak' => 'Stor Pejabat Tingkat 1 - Rak A1',
            'jajahan' => 'Ibu Pejabat Kota Bharu',
            'status' => 'Mencukupi',
            'deskripsi' => 'Kertas kegunaan harian pejabat, cetakan borang rasmi dan surat-menyurat.',
        ]);

        $pejabatItem2 = InventoriItem::create([
            'kod_item' => 'PJB-TNR-002',
            'nama_item' => 'Katrij Toner HP LaserJet Pro Original 76A (CF276A)',
            'jenis_stor' => 'pejabat',
            'kategori' => 'Peralatan Pejabat & IT',
            'unit' => 'Unit',
            'kuantiti_semasa' => 12,
            'kuantiti_minimum' => 5,
            'harga_seunit' => 385.00,
            'pembekal_utama' => 'Mega IT Solutions Kota Bharu',
            'lokasi_rak' => 'Stor Pejabat Tingkat 1 - Rak B2',
            'jajahan' => 'Ibu Pejabat Kota Bharu',
            'status' => 'Mencukupi',
            'deskripsi' => 'Toner pencetak laser monokrom untuk Bahagian Pentadbiran dan Kewangan.',
        ]);

        $pejabatItem3 = InventoriItem::create([
            'kod_item' => 'PJB-AST-003',
            'nama_item' => 'Komputer Meja HP ProDesk 400 G7 i5 / 16GB RAM / 512GB SSD',
            'jenis_stor' => 'pejabat',
            'kategori' => 'Aset & Perkakasan',
            'unit' => 'Set',
            'kuantiti_semasa' => 6,
            'kuantiti_minimum' => 2,
            'harga_seunit' => 3250.00,
            'pembekal_utama' => 'Infotech Komputer Kelantan',
            'lokasi_rak' => 'Bilik Server & Aset IT JPVNK',
            'jajahan' => 'Ibu Pejabat Kota Bharu',
            'status' => 'Mencukupi',
            'deskripsi' => 'Set PC stesen kerja pegawai veterinar dan pangkalan data sepunya.',
        ]);

        $pejabatItem4 = InventoriItem::create([
            'kod_item' => 'PJB-AST-004',
            'nama_item' => 'Proyektor Mudah Alih Epson EB-X51 3800 Lumens',
            'jenis_stor' => 'pejabat',
            'kategori' => 'Aset & Perkakasan',
            'unit' => 'Unit',
            'kuantiti_semasa' => 3,
            'kuantiti_minimum' => 1,
            'harga_seunit' => 2190.00,
            'pembekal_utama' => 'Infotech Komputer Kelantan',
            'lokasi_rak' => 'Almari Aset Pejabat Pentadbiran',
            'jajahan' => 'Ibu Pejabat Kota Bharu',
            'status' => 'Mencukupi',
            'deskripsi' => 'Proyektor kegunaan mesyuarat pengurusan dan taklimat jabatan.',
        ]);

        // Transaksi & Pinjaman Stor Pejabat
        InventoriTransaksi::create([
            'inventori_item_id' => $pejabatItem1->id,
            'jenis_transaksi' => 'Stok Masuk',
            'kuantiti' => 50,
            'penerima_atau_pembekal' => 'Percetakan & Alat Tulis Kelantan Sdn Bhd',
            'rujukan_dokumen' => 'DO-KTS-2026-0881',
            'baki_selepas' => 85,
            'dikendalikan_oleh' => $adminPejabat->id,
            'catatan' => 'Penerimaan bekalan kertas suku tahun pertama 2026.',
        ]);

        InventoriPinjaman::create([
            'inventori_item_id' => $pejabatItem4->id,
            'user_id' => $adminProgram->id,
            'kuantiti' => 1,
            'tujuan_pinjaman' => 'Sesi Taklimat Agihan Lembu Skim Pawah Bersama Penternak Ruminan',
            'tarikh_pinjam' => Carbon::now()->subDay()->toDateString(),
            'tarikh_jangka_pulang' => Carbon::now()->addDays(2)->toDateString(),
            'keadaan_semasa_pinjam' => 'Baik / Berfungsi',
            'status' => 'Dipinjam',
            'disahkan_oleh' => $adminPejabat->id,
        ]);

        // 7B. STOR UBAT, VAKSIN & FARMASEUTIKAL VETERINAR (Diuruskan oleh Admin Stor Ubat)
        $ubatItem1 = InventoriItem::create([
            'kod_item' => 'UBT-VET-001',
            'nama_item' => 'Ivomec Super Injection 500ml (Ivermectin + Clorsulon)',
            'jenis_stor' => 'ubat',
            'kategori' => 'Ubat-ubatan',
            'unit' => 'Botol',
            'kuantiti_semasa' => 45,
            'kuantiti_minimum' => 10,
            'harga_seunit' => 180.00,
            'no_batch' => 'IVM-2025-089',
            'tarikh_luput' => Carbon::now()->addMonths(18)->toDateString(),
            'suhu_simpanan' => 'Suhu Bilik Kering (< 25°C)',
            'pembekal_utama' => 'Boehringer Ingelheim Animal Health / Pharmaniaga',
            'lokasi_rak' => 'Bilik Farmasi - Rak A2 (Cecair Suntikan)',
            'jajahan' => 'Ibu Pejabat Kota Bharu',
            'status' => 'Mencukupi',
            'deskripsi' => 'Ubat anti-parasit cacing dan kutu spektrum luas bagi ternakan ruminan besar.',
        ]);

        $ubatItem2 = InventoriItem::create([
            'kod_item' => 'VAK-FMD-001',
            'nama_item' => 'Vaksin Penyakit Kuku & Mulut (FMD O/A/Asia1) 100 Doses',
            'jenis_stor' => 'ubat',
            'kategori' => 'Vaksin',
            'unit' => 'Botol',
            'kuantiti_semasa' => 8,
            'kuantiti_minimum' => 15,
            'harga_seunit' => 320.00,
            'no_batch' => 'FMD-MY-2026-04',
            'tarikh_luput' => Carbon::now()->addMonths(6)->toDateString(),
            'suhu_simpanan' => 'Rangkaian Sejuk / Chiller (2°C - 8°C)',
            'pembekal_utama' => 'Veterinary Biologics Malaysia Sdn Bhd',
            'lokasi_rak' => 'Peti Sejuk Vaksin No. 1 (2-8°C)',
            'jajahan' => 'Ibu Pejabat Kota Bharu',
            'status' => 'Stok Rendah',
            'deskripsi' => 'Vaksin pencegahan wabak Penyakit Kuku dan Mulut (FMD) ternakan ruminan.',
        ]);

        $ubatItem3 = InventoriItem::create([
            'kod_item' => 'UBT-ANT-003',
            'nama_item' => 'Terramycin LA Long Acting Injectable Solution 100ml',
            'jenis_stor' => 'ubat',
            'kategori' => 'Ubat-ubatan',
            'unit' => 'Botol',
            'kuantiti_semasa' => 60,
            'kuantiti_minimum' => 12,
            'harga_seunit' => 58.00,
            'no_batch' => 'TRM-2026-112',
            'tarikh_luput' => Carbon::now()->addMonths(24)->toDateString(),
            'suhu_simpanan' => 'Suhu Bilik Kering (< 25°C)',
            'pembekal_utama' => 'Zoetis Animal Health Malaysia',
            'lokasi_rak' => 'Bilik Farmasi - Rak B1 (Antibiotik)',
            'jajahan' => 'Ibu Pejabat Kota Bharu',
            'status' => 'Mencukupi',
            'deskripsi' => 'Antibiotik oksitetrasiklin bertindak panjang bagi rawatan jangkitan bakteria ternakan.',
        ]);

        $ubatItem4 = InventoriItem::create([
            'kod_item' => 'TAG-EPTR-001',
            'nama_item' => 'Tag Telinga Ruminan EPTR Rasmi JPVNK (Kuning Bersiri RFID/Barcode)',
            'jenis_stor' => 'ubat',
            'kategori' => 'Tag Telinga EPTR',
            'unit' => 'Keping',
            'kuantiti_semasa' => 1200,
            'kuantiti_minimum' => 200,
            'harga_seunit' => 4.50,
            'no_batch' => 'TAG-EPTR-2026-B1',
            'pembekal_utama' => 'Allflex Livestock Intelligence / Tagging Tech',
            'lokasi_rak' => 'Stor Bahan Biosekuriti & Tagging - Rak C1',
            'jajahan' => 'Ibu Pejabat Kota Bharu',
            'status' => 'Mencukupi',
            'deskripsi' => 'Tag telinga laser barcode khas mengikut enakmen EPTR Kelantan 2024.',
        ]);

        $ubatItem5 = InventoriItem::create([
            'kod_item' => 'ALT-TAG-002',
            'nama_item' => 'Universal Ear Tag Applicator Pliers Heavy Duty',
            'jenis_stor' => 'ubat',
            'kategori' => 'Aplikator & Peralatan Tagging',
            'unit' => 'Unit',
            'kuantiti_semasa' => 25,
            'kuantiti_minimum' => 5,
            'harga_seunit' => 85.00,
            'pembekal_utama' => 'Tagging Tech Sdn Bhd',
            'lokasi_rak' => 'Stor Bahan Biosekuriti & Tagging - Rak C3',
            'jajahan' => 'Ibu Pejabat Kota Bharu',
            'status' => 'Mencukupi',
            'deskripsi' => 'Playar aplikator khas pemasangan tag telinga ternakan lembu & kerbau.',
        ]);

        $ubatItem6 = InventoriItem::create([
            'kod_item' => 'KLN-PIC-006',
            'nama_item' => 'Automatic Continuous Syringe 5ml with Bottle Mount (Jarum Suntikan Automatik)',
            'jenis_stor' => 'ubat',
            'kategori' => 'Peralatan Surgeri & Klinik',
            'unit' => 'Set',
            'kuantiti_semasa' => 18,
            'kuantiti_minimum' => 4,
            'harga_seunit' => 145.00,
            'pembekal_utama' => 'Syarikat Alat Surgeri Veterinar',
            'lokasi_rak' => 'Bilik Peralatan Klinikal - Rak D1',
            'jajahan' => 'Ibu Pejabat Kota Bharu',
            'status' => 'Mencukupi',
            'deskripsi' => 'Picagari berterusan suntikan vaksin & ubat cacing kumpulan ternakan di lapangan.',
        ]);

        // Transaksi Stok Masuk & Stok Keluar Stor Ubat
        InventoriTransaksi::create([
            'inventori_item_id' => $ubatItem4->id,
            'jenis_transaksi' => 'Stok Masuk',
            'kuantiti' => 1000,
            'penerima_atau_pembekal' => 'Pembekal Tagging Tech Sdn Bhd',
            'rujukan_dokumen' => 'DO-TAG-2026-9901',
            'baki_selepas' => 1200,
            'dikendalikan_oleh' => $adminUbat->id,
            'catatan' => 'Penerimaan bekalan tag telinga rasmi EPTR peruntukan 2026.',
        ]);

        InventoriTransaksi::create([
            'inventori_item_id' => $ubatItem1->id,
            'jenis_transaksi' => 'Stok Keluar',
            'kuantiti' => 5,
            'penerima_atau_pembekal' => 'Klinik Haiwan Veterinar Jajahan Pasir Puteh',
            'rujukan_dokumen' => 'AGIHAN-UBT-2026-012',
            'baki_selepas' => 45,
            'dikendalikan_oleh' => $adminUbat->id,
            'catatan' => 'Agihan bulanan ubat nyahcacing bagi rawatan ternakan penternak jajahan.',
        ]);

        InventoriPinjaman::create([
            'inventori_item_id' => $ubatItem5->id,
            'user_id' => $adminEptr->id,
            'kuantiti' => 2,
            'tujuan_pinjaman' => 'Operasi Pemasangan Tag Telinga EPTR di Jajahan Bachok',
            'tarikh_pinjam' => Carbon::now()->subDays(2)->toDateString(),
            'tarikh_jangka_pulang' => Carbon::now()->addDays(3)->toDateString(),
            'keadaan_semasa_pinjam' => 'Baik / Berfungsi',
            'status' => 'Dipinjam',
            'disahkan_oleh' => $adminUbat->id,
        ]);

        // 7c. Permohonan Stor Pejabat & Ubat oleh Staf Jabatan
        InventoriPermohonan::create([
            'no_permohonan' => 'REQ-PJB-2026-001',
            'user_id' => $adminEptr->id,
            'inventori_item_id' => $pejabatItem1->id, // Kertas A4
            'jenis_stor' => 'pejabat',
            'kuantiti_dimohon' => 5,
            'kuantiti_diluluskan' => 5,
            'unit_bahagian' => 'Unit Pendaftaran EPTR Ruminan',
            'tujuan_permohonan' => 'Cetakan borang pendaftaran Borang A penternak ruminan dan kad kuning ternakan.',
            'tarikh_diperlukan' => Carbon::now()->addDays(2)->toDateString(),
            'status' => 'Diluluskan',
            'catatan_pemohon' => 'Kertas A4 diperlukan segera sebelum operasi lapangan di Bachok.',
            'catatan_pegawai' => 'Permohonan diluluskan. Sila ambil di Stor Pejabat.',
            'disahkan_oleh' => $adminPejabat->id,
            'tarikh_kelulusan' => Carbon::now(),
        ]);

        InventoriPermohonan::create([
            'no_permohonan' => 'REQ-PJB-2026-002',
            'user_id' => $adminKursus->id,
            'inventori_item_id' => $pejabatItem3->id, // Toner HP LaserJet
            'jenis_stor' => 'pejabat',
            'kuantiti_dimohon' => 1,
            'unit_bahagian' => 'Unit Latihan & Kursus Ternakan',
            'tujuan_permohonan' => 'Cetakan modul dan sijil penyertaan kursus pembiakan lembu pedaging.',
            'tarikh_diperlukan' => Carbon::now()->addDays(5)->toDateString(),
            'status' => 'Menunggu Kelulusan',
            'catatan_pemohon' => 'Toner pencetak bilik urus setia kursus kehabisan dakwat.',
        ]);

        InventoriPermohonan::create([
            'no_permohonan' => 'REQ-PJB-2026-003',
            'user_id' => $kakitangan->id,
            'inventori_item_id' => $pejabatItem1->id, // Kertas A4
            'jenis_stor' => 'pejabat',
            'kuantiti_dimohon' => 2,
            'unit_bahagian' => 'Unit Pentadbiran & Sumber Manusia',
            'tujuan_permohonan' => 'Dokumentasi fail rekod perkhidmatan dan memo dalaman jabatan.',
            'tarikh_diperlukan' => Carbon::now()->addDay()->toDateString(),
            'status' => 'Menunggu Kelulusan',
            'catatan_pemohon' => 'Bekalan kertas A4 di meja kerja pentadbiran habis.',
        ]);

        InventoriPermohonan::create([
            'no_permohonan' => 'REQ-UBT-2026-001',
            'user_id' => $adminJajahan->id,
            'inventori_item_id' => $ubatItem1->id, // Ivermectin
            'jenis_stor' => 'ubat',
            'kuantiti_dimohon' => 4,
            'unit_bahagian' => 'Pusat Veterinar Jajahan Pasir Mas',
            'tujuan_permohonan' => 'Rawatan kes kurap dan parasit ternakan lembu penternak kampung.',
            'tarikh_diperlukan' => Carbon::now()->addDays(1)->toDateString(),
            'status' => 'Menunggu Kelulusan',
            'catatan_pemohon' => 'Bekalan ubat cacing di klinik jajahan hampir habis.',
        ]);

        InventoriPermohonan::create([
            'no_permohonan' => 'REQ-UBT-2026-002',
            'user_id' => $adminEptr->id,
            'inventori_item_id' => $ubatItem4->id, // Tag Telinga EPTR
            'jenis_stor' => 'ubat',
            'kuantiti_dimohon' => 50,
            'kuantiti_diluluskan' => 50,
            'unit_bahagian' => 'Unit Tagging EPTR',
            'tujuan_permohonan' => 'Operasi penandaan tag telinga lembu penternak jajahan Pasir Puteh.',
            'tarikh_diperlukan' => Carbon::now()->subDay()->toDateString(),
            'status' => 'Telah Diambil / Diserahkan',
            'catatan_pemohon' => 'Tag siri 2026 untuk batch penternak baharu.',
            'catatan_pegawai' => 'Stok 50 unit tag telinga diserahkan kepada En. Wan Kamaruddin.',
            'disahkan_oleh' => $adminUbat->id,
            'tarikh_kelulusan' => Carbon::now()->subDay(),
        ]);

        // 8. KENDERAAN JABATAN
        $van = Kenderaan::create([
            'no_pendaftaran' => 'DDX 8812',
            'jenis_kenderaan' => 'Pacuan 4 Roda (4x4)',
            'model' => 'Toyota Hilux 2.4 Double Cab',
            'tahun_buatan' => 2022,
            'kapasiti_penumpang' => 5,
            'jajahan_penempatan' => 'Ibu Pejabat Kota Bharu',
            'status' => 'Sedia',
            'lokasi_kunci' => 'Papan Kunci Pejabat Pentadbiran JPVNK',
            'odometer_semasa_km' => 45200,
            'tarikh_tamat_cukai_jalan' => '2026-11-30',
            'catatan' => 'Sesuai untuk operasi lapangan & lawatan kandang/ladang.',
        ]);

        $lori = Kenderaan::create([
            'no_pendaftaran' => 'DEA 5521',
            'jenis_kenderaan' => 'Van',
            'model' => 'Toyota Hiace Window Van 2.5',
            'tahun_buatan' => 2021,
            'kapasiti_penumpang' => 11,
            'jajahan_penempatan' => 'Ibu Pejabat Kota Bharu',
            'status' => 'Sedia',
            'lokasi_kunci' => 'Papan Kunci Pejabat Pentadbiran JPVNK',
            'odometer_semasa_km' => 62100,
            'tarikh_tamat_cukai_jalan' => '2026-10-15',
            'catatan' => 'Pengangkutan staf menghadiri kursus & mesyuarat luar.',
        ]);

        $sedan = Kenderaan::create([
            'no_pendaftaran' => 'DCM 3144',
            'jenis_kenderaan' => 'Kereta Sedan',
            'model' => 'Proton Persona 1.6 Premium',
            'tahun_buatan' => 2023,
            'kapasiti_penumpang' => 4,
            'jajahan_penempatan' => 'Ibu Pejabat Kota Bharu',
            'status' => 'Sedia',
            'lokasi_kunci' => 'Papan Kunci Pejabat Pentadbiran JPVNK',
            'odometer_semasa_km' => 28400,
            'tarikh_tamat_cukai_jalan' => '2027-01-20',
            'catatan' => 'Kenderaan kegunaan rasmi urusan pentadbiran & bank.',
        ]);

        KenderaanTempahan::create([
            'user_id' => $adminEptr->id,
            'kenderaan_id' => $van->id,
            'no_tempahan' => 'KND-2026-0032',
            'tujuan_perjalanan' => 'Operasi Verifikasi & Pemasangan Tag Telinga EPTR Penternak Ruminan',
            'destinasi' => 'Jajahan Bachok & Pasir Puteh',
            'tarikh_mula' => Carbon::now()->addDays(1)->toDateString(),
            'masa_mula' => '08:30:00',
            'tarikh_tamat' => Carbon::now()->addDays(1)->toDateString(),
            'masa_tamat' => '17:00:00',
            'bilangan_penumpang' => 3,
            'senarai_nama_penumpang' => 'En. Wan Kamaruddin, En. Mohd Saufi, En. Hafizul',
            'pemandu_nama' => 'En. Che Rosli (Pemandu Kenderaan JPVNK)',
            'odometer_keluar' => 45200,
            'status' => 'Diluluskan',
            'catatan_kelulusan' => 'Permohonan diluluskan. Sila ambil kad minyak dan kunci daripada penyelia.',
            'diluluskan_oleh' => $adminPejabat->id,
        ]);

        // 8. PEMANDU KENDERAAN JABATAN
        \App\Models\Pemandu::create([
            'nama' => 'En. Che Rosli bin Dollah',
            'no_kp' => '780415035581',
            'no_pekerja' => 'VET-DVR-001',
            'no_telefon' => '019-9812345',
            'kelas_lesen' => 'D, E, GDL',
            'tarikh_tamat_lesen' => '2027-04-15',
            'jajahan_penempatan' => 'Ibu Pejabat Kota Bharu',
            'status' => 'Aktif',
            'catatan' => 'Pemandu Kanan operasi lori angkut ternakan dan van jabatan.',
        ]);

        \App\Models\Pemandu::create([
            'nama' => 'En. Ahmad Razak bin Zakaria',
            'no_kp' => '820921035582',
            'no_pekerja' => 'VET-DVR-002',
            'no_telefon' => '013-9123456',
            'kelas_lesen' => 'D, GDL',
            'tarikh_tamat_lesen' => '2027-09-21',
            'jajahan_penempatan' => 'Ibu Pejabat Kota Bharu',
            'status' => 'Aktif',
            'catatan' => 'Pemandu Hilux 4x4 operasi lapangan dan tag telinga.',
        ]);

        \App\Models\Pemandu::create([
            'nama' => 'En. Mohd Khairul bin Ramli',
            'no_kp' => '861105035583',
            'no_pekerja' => 'VET-DVR-003',
            'no_telefon' => '011-23456789',
            'kelas_lesen' => 'D, DA',
            'tarikh_tamat_lesen' => '2026-11-05',
            'jajahan_penempatan' => 'Ibu Pejabat Kota Bharu',
            'status' => 'Aktif',
            'catatan' => 'Pemandu kereta rasmi dan urusan pentadbiran.',
        ]);

        \App\Models\Pemandu::create([
            'nama' => 'En. Wan Mohd Hafiz bin Wan Daud',
            'no_kp' => '900112035584',
            'no_pekerja' => 'VET-DVR-004',
            'no_telefon' => '017-9876543',
            'kelas_lesen' => 'D, E, GDL',
            'tarikh_tamat_lesen' => '2027-01-12',
            'jajahan_penempatan' => 'Pasir Mas',
            'status' => 'Bertugas',
            'catatan' => 'Pemandu operasi Pusat Veterinar Jajahan Pasir Mas.',
        ]);
    }
}
