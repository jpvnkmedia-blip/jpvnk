<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\MediaTempahan;
use App\Models\UserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaTempahanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Storage::fake('public');
    }

    private function getStaffUser(): User
    {
        $staff = User::where('role', 'staf')->first();
        if (!$staff) {
            $staff = User::create([
                'name' => 'Staf JPVNK Demo',
                'email' => 'stafdemo@jpvnk.gov.my',
                'role' => 'staf',
                'password' => bcrypt('password'),
            ]);
        }
        return $staff;
    }

    private function getAdminUser(): User
    {
        $admin = User::where('role', 'super_admin')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Super Admin JPVNK',
                'email' => 'superadmin@jpvnk.gov.my',
                'role' => 'super_admin',
                'password' => bcrypt('password'),
            ]);
        }
        return $admin;
    }

    public function test_staff_can_view_media_calendar_and_booking_list()
    {
        $staff = $this->getStaffUser();

        // Buat tempahan contoh
        MediaTempahan::create([
            'user_id' => $staff->id,
            'no_rujukan' => 'MEDIA/2026/09/0001',
            'nama_pemohon' => $staff->name,
            'jawatan_pemohon' => 'Pembantu Veterinar Gred G19',
            'bahagian_unit' => 'Bahagian Pembangunan Industri Ternakan',
            'no_telefon' => '0123456789',
            'emel' => $staff->email,
            'nama_program' => 'Hari Bertemu Pelanggan Penternak 2026',
            'tarikh_program' => now()->addDays(3)->toDateString(),
            'masa_mula' => '08:30',
            'masa_tamat' => '17:00',
            'lokasi' => 'Dewan Serbaguna JPVNK',
            'penganjur' => 'Unit Latihan & Pengembangan',
            'pegawai_bertanggungjawab' => 'Encik Ahmad Bin Ali',
            'anggaran_peserta' => 150,
            'jenis_permohonan' => ['Liputan Fotografi', 'Reka Bentuk Poster'],
            'keperluan_fotografi' => ['Kehadiran VIP', 'Perasmian', 'Foto berkumpulan'],
            'keperluan_poster' => ['tajuk' => 'Poster Karnival Ternakan', 'saiz' => 'A3', 'konsep' => 'Moden dan korporat'],
            'keutamaan' => 'Biasa',
            'status' => 'Menunggu Kelulusan',
            'perakuan' => true,
        ]);

        $response = $this->actingAs($staff)->get(route('media.index'));
        $response->assertStatus(200);
        $response->assertSee('Sistem Tempahan Unit Media');
        $response->assertSee('Hari Bertemu Pelanggan Penternak 2026');
        $response->assertSee('MEDIA/2026/09/0001');
    }

    public function test_staff_can_submit_full_6_section_media_booking_with_attachments()
    {
        $staff = $this->getStaffUser();

        $file1 = UploadedFile::fake()->create('surat_jemputan.pdf', 500, 'application/pdf');
        $file2 = UploadedFile::fake()->image('logo_program.png');

        $tarikhProgram = now()->addDays(5)->toDateString();

        $formData = [
            // 1. Maklumat Pemohon
            'nama_pemohon' => 'Siti Aminah Binti Kassim',
            'jawatan_pemohon' => 'Pegawai Veterinar Gred GV41',
            'bahagian_unit' => 'Seksyen Kesihatan Awam Veterinar',
            'no_telefon' => '0198765432',
            'emel' => 'siti@jpvnk.gov.my',

            // 2. Maklumat Program
            'nama_program' => 'Kempen Kesedaran Keselamatan Makanan Haiwan',
            'tarikh_program' => $tarikhProgram,
            'masa_mula' => '09:00',
            'masa_tamat' => '13:00',
            'lokasi' => 'Auditorium Kompleks Veterinar Kota Bharu',
            'penganjur' => 'Seksyen Kesihatan Awam',
            'pegawai_bertanggungjawab' => 'Dr. Siti Aminah',
            'anggaran_peserta' => 80,

            // 3. Jenis Permohonan
            'jenis_permohonan' => [
                'Liputan Fotografi',
                'Reka Bentuk Banner / Backdrop',
                'Hebahan Facebook',
                'Video Promosi / Montaj',
            ],

            // 4. Butiran Keperluan Khusus
            'keperluan_fotografi' => ['Kehadiran VIP', 'Perasmian', 'Penyampaian bantuan'],
            'poster_tajuk' => 'Banner Pentas Utama Kempen Kesedaran',
            'poster_saiz' => '20ft x 10ft Backdrop',
            'poster_konsep' => 'Warna rasmi JPVNK, visual produk veterinar selamat',
            'video_format' => '16:9 Landscape Video',
            'video_durasi' => 'Montaj 3 Minit',
            'catatan_keperluan' => 'Perlukan jurufoto standby 30 minit sebelum VIP tiba',

            // 5. Lampiran
            'lampiran' => [$file1, $file2],

            // 6. Pengesahan
            'perakuan' => '1',
        ];

        $response = $this->actingAs($staff)->post(route('media.store'), $formData);

        $response->assertRedirect();

        // Pastikan rekod tersimpan
        $tempahan = MediaTempahan::where('nama_program', 'Kempen Kesedaran Keselamatan Makanan Haiwan')->first();
        $this->assertNotNull($tempahan);
        $this->assertEquals('Menunggu Kelulusan', $tempahan->status);
        $this->assertStringStartsWith('MEDIA/', $tempahan->no_rujukan);
        $this->assertCount(4, $tempahan->jenis_permohonan);
        $this->assertCount(2, $tempahan->lampiran);
        $this->assertEquals('Banner Pentas Utama Kempen Kesedaran', $tempahan->keperluan_poster['tajuk']);

        // Pastikan notifikasi dicipta untuk Admin Media / Super Admin
        $adminUsers = User::whereIn('role', ['admin_media', 'super_admin'])->pluck('id');
        if ($adminUsers->isNotEmpty()) {
            $notification = UserNotification::where('user_id', $adminUsers->first())
                ->where('type', 'media')
                ->first();
            $this->assertNotNull($notification);
        }
    }

    public function test_non_staff_public_user_is_forbidden_from_media_system()
    {
        $publicUser = User::where('role', 'orang_awam')->first();
        if (!$publicUser) {
            $publicUser = User::create([
                'name' => 'Orang Awam',
                'email' => 'awam@gmail.com',
                'role' => 'orang_awam',
                'password' => bcrypt('password'),
            ]);
        }

        $this->assertFalse($publicUser->isStaff());
        $this->assertFalse($publicUser->canAccessMedia());

        // Sekat akses ke paparan senarai, borang cipta, dan simpan (403)
        $this->actingAs($publicUser)->get(route('media.index'))->assertStatus(403);
        $this->actingAs($publicUser)->get(route('media.create'))->assertStatus(403);
        $this->actingAs($publicUser)->post(route('media.store'), [
            'nama_pemohon' => 'Awam',
        ])->assertStatus(403);
    }

    public function test_admin_media_can_approve_booking_and_assign_crew()
    {
        $superAdmin = $this->getAdminUser();
        $staff = $this->getStaffUser();

        $tempahan = MediaTempahan::create([
            'user_id' => $staff->id,
            'no_rujukan' => 'MEDIA/2026/09/0002',
            'nama_pemohon' => $staff->name,
            'jawatan_pemohon' => 'Pegawai Veterinar',
            'bahagian_unit' => 'Unit Komunikasi',
            'no_telefon' => '0112233445',
            'emel' => $staff->email,
            'nama_program' => 'Perasmian Klinik Bergerak JPVNK',
            'tarikh_program' => now()->addDays(7)->toDateString(),
            'masa_mula' => '10:00',
            'masa_tamat' => '13:00',
            'lokasi' => 'Pekan Pasir Mas',
            'penganjur' => 'PPVJ Pasir Mas',
            'pegawai_bertanggungjawab' => 'En. Razak',
            'anggaran_peserta' => 50,
            'jenis_permohonan' => ['Liputan Fotografi', 'Liputan Videografi'],
            'keutamaan' => 'Biasa',
            'status' => 'Menunggu Kelulusan',
            'perakuan' => true,
        ]);

        $response = $this->actingAs($superAdmin)->post(route('media.tindakan', $tempahan->id), [
            'status' => 'Diluluskan',
            'pegawai_media_bertugas' => 'Krew A - Jurufoto & Juruvideo (Mohd Hafiz & Nurul)',
            'peralatan_disediakan' => 'Sony A7IV, Gimbal Ronin, Dron DJI Mini 4 Pro, Mic Wireless',
            'catatan_admin' => 'Diluluskan. Sila hadir 30 minit sebelum acara.',
        ]);

        $response->assertRedirect();

        $tempahan->refresh();
        $this->assertEquals('Diluluskan', $tempahan->status);
        $this->assertEquals('Krew A - Jurufoto & Juruvideo (Mohd Hafiz & Nurul)', $tempahan->pegawai_media_bertugas);
        $this->assertEquals($superAdmin->id, $tempahan->diluluskan_oleh);
        $this->assertNotNull($tempahan->tarikh_kelulusan);

        // Notifikasi dihantar kepada pemohon
        $notification = UserNotification::where('user_id', $staff->id)
            ->where('type', 'media')
            ->where('title', 'like', '%Diluluskan%')
            ->first();
        $this->assertNotNull($notification);
    }

    public function test_admin_can_request_correction_and_applicant_can_update_resubmit()
    {
        $superAdmin = $this->getAdminUser();
        $staff = $this->getStaffUser();

        $tempahan = MediaTempahan::create([
            'user_id' => $staff->id,
            'no_rujukan' => 'MEDIA/2026/09/0003',
            'nama_pemohon' => $staff->name,
            'jawatan_pemohon' => 'Pegawai Veterinar',
            'bahagian_unit' => 'Unit Latihan',
            'no_telefon' => '0112233445',
            'emel' => $staff->email,
            'nama_program' => 'Bengkel Penulisan Berita Korporat',
            'tarikh_program' => now()->addDays(10)->toDateString(),
            'masa_mula' => '09:00',
            'masa_tamat' => '16:00',
            'lokasi' => 'Bilik Mesyuarat Utama',
            'penganjur' => 'Unit Latihan',
            'pegawai_bertanggungjawab' => 'En. Zulkifli',
            'anggaran_peserta' => 30,
            'jenis_permohonan' => ['Reka Bentuk Poster'],
            'keutamaan' => 'Biasa',
            'status' => 'Menunggu Kelulusan',
            'perakuan' => true,
        ]);

        // 1. Admin minta pembetulan
        $this->actingAs($superAdmin)->post(route('media.tindakan', $tempahan->id), [
            'status' => 'Perlu Pembetulan',
            'catatan_admin' => 'Sila lampirkan draf teks dan tentatif program yang lengkap.',
        ]);

        $tempahan->refresh();
        $this->assertEquals('Perlu Pembetulan', $tempahan->status);
        $this->assertEquals('Sila lampirkan draf teks dan tentatif program yang lengkap.', $tempahan->catatan_admin);

        // 2. Pemohon buka borang kemaskini
        $this->actingAs($staff)->get(route('media.edit', $tempahan->id))->assertStatus(200);

        // 3. Pemohon kemaskini & hantar semula
        $response = $this->actingAs($staff)->put(route('media.update', $tempahan->id), [
            'nama_pemohon' => $staff->name,
            'jawatan_pemohon' => 'Pegawai Veterinar Gred GV41',
            'bahagian_unit' => 'Unit Latihan JPVNK',
            'no_telefon' => '0112233445',
            'emel' => $staff->email,
            'nama_program' => 'Bengkel Penulisan Berita Korporat 2026 (Dikemaskini)',
            'tarikh_program' => now()->addDays(10)->toDateString(),
            'masa_mula' => '09:00',
            'masa_tamat' => '16:00',
            'lokasi' => 'Bilik Mesyuarat Utama JPVNK',
            'penganjur' => 'Unit Latihan',
            'pegawai_bertanggungjawab' => 'En. Zulkifli',
            'anggaran_peserta' => 35,
            'jenis_permohonan' => ['Reka Bentuk Poster', 'Hebahan Facebook'],
            'poster_tajuk' => 'Bengkel Penulisan Korporat',
            'poster_saiz' => 'A4 & Media Sosial',
            'catatan_keperluan' => 'Tentatif lengkap telah dimasukkan.',
            'keutamaan' => 'Biasa',
            'perakuan' => '1',
        ]);

        $response->assertRedirect(route('media.show', $tempahan->id));

        $tempahan->refresh();
        $this->assertEquals('Menunggu Kelulusan', $tempahan->status);
        $this->assertEquals('Bengkel Penulisan Berita Korporat 2026 (Dikemaskini)', $tempahan->nama_program);
    }

    public function test_admin_can_reject_booking()
    {
        $superAdmin = $this->getAdminUser();
        $staff = $this->getStaffUser();

        $tempahan = MediaTempahan::create([
            'user_id' => $staff->id,
            'no_rujukan' => 'MEDIA/2026/09/0004',
            'nama_pemohon' => $staff->name,
            'jawatan_pemohon' => 'Pegawai',
            'bahagian_unit' => 'Unit Audit',
            'no_telefon' => '0112233445',
            'emel' => $staff->email,
            'nama_program' => 'Mesyuarat Dalaman Tertutup',
            'tarikh_program' => now()->addDays(2)->toDateString(),
            'masa_mula' => '09:00',
            'masa_tamat' => '11:00',
            'lokasi' => 'Bilik Audit',
            'penganjur' => 'Unit Audit',
            'pegawai_bertanggungjawab' => 'Audit',
            'anggaran_peserta' => 5,
            'jenis_permohonan' => ['Liputan Fotografi'],
            'keutamaan' => 'Biasa',
            'status' => 'Menunggu Kelulusan',
            'perakuan' => true,
        ]);

        $this->actingAs($superAdmin)->post(route('media.tindakan', $tempahan->id), [
            'status' => 'Ditolak',
            'catatan_admin' => 'Maaf, semua krew media telah ditugaskan bagi program luar daerah pada tarikh tersebut.',
        ]);

        $tempahan->refresh();
        $this->assertEquals('Ditolak', $tempahan->status);
        $this->assertStringContainsString('krew media telah ditugaskan', $tempahan->catatan_admin);
    }

    public function test_staff_and_admin_can_view_and_print_confirmation_slip()
    {
        $staff = $this->getStaffUser();

        $tempahan = MediaTempahan::create([
            'user_id' => $staff->id,
            'no_rujukan' => 'MEDIA/2026/09/0005',
            'nama_pemohon' => $staff->name,
            'jawatan_pemohon' => 'Pegawai Veterinar',
            'bahagian_unit' => 'Unit Biosekuriti',
            'no_telefon' => '0112233445',
            'emel' => $staff->email,
            'nama_program' => 'Persidangan Kesihatan Haiwan Kebangsaan',
            'tarikh_program' => now()->addDays(15)->toDateString(),
            'masa_mula' => '08:00',
            'masa_tamat' => '17:00',
            'lokasi' => 'Grand Riverview Hotel, Kota Bharu',
            'penganjur' => 'Bahagian Biosekuriti',
            'pegawai_bertanggungjawab' => 'Dr. Kamaruddin',
            'anggaran_peserta' => 200,
            'jenis_permohonan' => ['Liputan Fotografi', 'Liputan Videografi', 'Siaran Langsung'],
            'keutamaan' => 'Sangat Segera',
            'status' => 'Diluluskan',
            'pegawai_media_bertugas' => 'Krew Utama Media JPVNK',
            'perakuan' => true,
        ]);

        $response = $this->actingAs($staff)->get(route('media.cetak', $tempahan->id));
        $response->assertStatus(200);
        $response->assertSee('SLIP PENGESAHAN TEMPAHAN');
        $response->assertSee('MEDIA/2026/09/0005');
        $response->assertSee('Persidangan Kesihatan Haiwan Kebangsaan');
        $response->assertSee('DILULUSKAN');
    }

    public function test_cannot_book_media_on_past_date()
    {
        $staff = $this->getStaffUser();

        // 1. Check create view defaults to tomorrow if given past date in query string
        $pastDate = now()->subDays(3)->toDateString();
        $createResponse = $this->actingAs($staff)->get(route('media.create', ['tarikh' => $pastDate]));
        $createResponse->assertStatus(200);
        $createResponse->assertSee('value="' . now()->addDay()->toDateString() . '"', false);

        // 2. Check store rejects past date with validation error
        $formData = [
            'nama_pemohon' => 'Siti Aminah Binti Kassim',
            'jawatan_pemohon' => 'Pegawai Veterinar',
            'bahagian_unit' => 'Unit Media',
            'no_telefon' => '0198887766',
            'emel' => 'aminah@jpvnk.gov.my',
            'nama_program' => 'Program Tarikh Lepas',
            'tarikh_program' => $pastDate,
            'masa_mula' => '08:30',
            'masa_tamat' => '17:00',
            'lokasi' => 'Dewan JPVNK',
            'penganjur' => 'JPVNK',
            'pegawai_bertanggungjawab' => 'Aminah',
            'jenis_permohonan' => ['Liputan Fotografi'],
            'perakuan' => '1',
        ];

        $response = $this->actingAs($staff)->post(route('media.store'), $formData);
        $response->assertSessionHasErrors(['tarikh_program']);
        $this->assertEquals(0, MediaTempahan::where('nama_program', 'Program Tarikh Lepas')->count());
    }

    public function test_admin_media_can_update_deliverables_link_and_whatsapp_is_present()
    {
        $staff = $this->getStaffUser();
        $admin = $this->getAdminUser();

        $tempahan = MediaTempahan::create([
            'user_id' => $staff->id,
            'no_rujukan' => 'MEDIA/2026/09/0099',
            'nama_pemohon' => 'En. Bakar',
            'jawatan_pemohon' => 'Pegawai Tadbir',
            'bahagian_unit' => 'Unit Media',
            'no_telefon' => '0123456789',
            'emel' => 'bakar@jpvnk.gov.my',
            'nama_program' => 'Program Hari Penternak',
            'tarikh_program' => now()->addDays(2)->toDateString(),
            'masa_mula' => '08:30',
            'masa_tamat' => '17:00',
            'lokasi' => 'Dewan JPVNK',
            'penganjur' => 'JPVNK',
            'pegawai_bertanggungjawab' => 'Bakar',
            'jenis_permohonan' => ['Liputan Fotografi'],
            'status' => 'Diluluskan',
            'perakuan' => true,
        ]);

        // Admin updates deliverables link
        $driveUrl = 'https://drive.google.com/drive/folders/samplefolder123';
        $updateResponse = $this->actingAs($admin)->post(route('media.tindakan', $tempahan->id), [
            'keputusan' => 'Selesai',
            'pautan_hasil_media' => $driveUrl,
            'catatan_unit_media' => 'Semua gambar telah dimuat naik ke folder Google Drive.',
        ]);

        $updateResponse->assertRedirect(route('media.show', $tempahan->id));
        $tempahan->refresh();
        $this->assertEquals('Selesai', $tempahan->status);
        $this->assertEquals($driveUrl, $tempahan->pautan_hasil_media);

        // Staff views detail page with drive link and WhatsApp button
        $viewResponse = $this->actingAs($staff)->get(route('media.show', $tempahan->id));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee($driveUrl);
        $viewResponse->assertSee('https://wa.me/60123456789', false);
    }

    public function test_admin_media_can_export_csv_report()
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get(route('media.export'));
        $response->assertStatus(200);
        $this->assertStringContainsString('attachment; filename="Laporan_Tempahan_Unit_Media_', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('.csv"', $response->headers->get('Content-Disposition'));
    }

    public function test_admin_media_only_has_access_to_media_module_and_not_other_modules()
    {
        $mediaUser = User::create([
            'name' => 'Admin Media Sahaja',
            'email' => 'media.only@jpvnk.test',
            'ic_number' => '920101031122',
            'phone' => '0197778899',
            'role' => 'admin_media',
            'roles' => ['admin_media'],
            'status' => 'Aktif',
            'password' => bcrypt('password'),
        ]);

        // Has media access
        $this->assertTrue($mediaUser->canAccessMedia());
        $this->assertTrue($mediaUser->canManageMedia());

        // Blocked from other administrative modules
        $this->assertFalse($mediaUser->canAccessEptr());
        $this->assertFalse($mediaUser->canAccessPawah());
        $this->assertFalse($mediaUser->canAccessNaimbif());
        $this->assertFalse($mediaUser->canAccessEpu());
        $this->assertFalse($mediaUser->canAccessKursus());
        $this->assertFalse($mediaUser->canAccessKlinik());
        $this->assertFalse($mediaUser->canAccessStorPejabat());
        $this->assertFalse($mediaUser->canAccessStorUbat());
        $this->assertFalse($mediaUser->canAccessKenderaan());
        $this->assertFalse($mediaUser->canRequestInventori());
    }

    public function test_admin_media_can_whatsapp_other_media_admins_to_take_up_task()
    {
        $mediaAdmin1 = User::create([
            'name' => 'Admin Media Utama',
            'email' => 'media1@jpvnk.test',
            'ic_number' => '920101031133',
            'phone' => '0191112233',
            'role' => 'admin_media',
            'roles' => ['admin_media'],
            'status' => 'Aktif',
            'password' => bcrypt('password'),
        ]);

        $mediaAdmin2 = User::create([
            'name' => 'En. Jurufoto Rakan Media',
            'email' => 'media2@jpvnk.test',
            'ic_number' => '920101031144',
            'phone' => '0194445566',
            'role' => 'admin_media',
            'roles' => ['admin_media'],
            'status' => 'Aktif',
            'password' => bcrypt('password'),
        ]);

        $tempahan = MediaTempahan::create([
            'user_id' => $mediaAdmin1->id,
            'no_rujukan' => 'MEDIA/2026/09/0777',
            'nama_pemohon' => 'Pegawai JPVNK',
            'jawatan_pemohon' => 'Penolong Pegawai',
            'bahagian_unit' => 'Unit Media',
            'no_telefon' => '0123456789',
            'emel' => 'pemohon@jpvnk.gov.my',
            'nama_program' => 'Majlis Anugerah Khidmat Cemerlang',
            'tarikh_program' => now()->addDays(3)->toDateString(),
            'masa_mula' => '08:30',
            'masa_tamat' => '13:00',
            'lokasi' => 'Dewan Besar JPVNK',
            'penganjur' => 'JPVNK',
            'pegawai_bertanggungjawab' => 'Urusetia',
            'jenis_permohonan' => ['Liputan Fotografi', 'Liputan Videografi'],
            'status' => 'Menunggu Kelulusan',
            'perakuan' => true,
        ]);

        $response = $this->actingAs($mediaAdmin1)->get(route('media.show', $tempahan->id));
        $response->assertStatus(200);
        $response->assertSee('WhatsApp Rakan Admin Media');
        $response->assertSee('En. Jurufoto Rakan Media');
        $response->assertSee('https://wa.me/60194445566', false);
        $response->assertSee('Majlis+Anugerah+Khidmat+Cemerlang');
    }
}
