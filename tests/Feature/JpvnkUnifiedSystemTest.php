<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pemunya;
use App\Models\Ternakan;
use App\Models\PawahPerjanjian;
use App\Models\EpuLadang;
use App\Models\EpuPermohonan;
use App\Models\Course;
use App\Models\KlinikTemujanji;
use App\Models\InventoriItem;
use App\Models\InventoriTransaksi;
use App\Models\InventoriPinjaman;
use App\Models\Kenderaan;
use App\Models\PermitSembelihan;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class JpvnkUnifiedSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_homepage_redirects_to_login_and_login_page_renders()
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');

        $loginResponse = $this->get('/login');
        $loginResponse->assertStatus(200);
        $loginResponse->assertSee('Jabatan Perkhidmatan Veterinar Negeri Kelantan');
        $loginResponse->assertSee('No. Kad Pengenalan / Emel');
        $loginResponse->assertSee('Kata Laluan');
    }

    public function test_auth_login_and_dashboard_renders()
    {
        $user = User::where('role', 'super_admin')->first();
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Papan Pemuka');
        $response->assertSee('EPTR');
        $response->assertSee('Pawah');
        $response->assertSee('EPU');
    }

    public function test_eptr_registration_with_program_column_and_kad_kuning()
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $user = User::where('role', 'penternak')->first();
        $admin = User::where('role', 'admin_eptr')->first();
        $file = \Illuminate\Http\UploadedFile::fake()->create('resit_bayaran_eptr.pdf', 200, 'application/pdf');

        // 1. Pemohon menghantar Borang A (No tag belum dibuat)
        $response = $this->actingAs($user)->post('/eptr/daftar-borang-a', [
            'nama_pemunya' => 'Ahmad Penternak',
            'no_kp_pemunya' => '850101035544',
            'no_tel_pemunya' => '0199001122',
            'jajahan' => 'Kota Bharu',
            'daerah' => 'Peringat',
            'poskod' => '16400',
            'alamat_pemunya' => 'Kg Lembah',
            'jenis_ternakan' => 'lembu',
            'baka' => 'kedah-kelantan',
            'jantina' => 'Betina',
            'tarikh_lahir' => '2024-05-10',
            'umur' => '2 Tahun',
            'warna' => 'Coklat',
            'program' => 'Program Pawah Ternakan Negeri Kelantan',
            'tujuan_ternakan' => 'Pembiakan',
            'resit_pembayaran' => $file,
        ]);

        $response->assertRedirect();
        
        $pemunya = Pemunya::where('no_kp', '850101035544')->first();
        $this->assertNotNull($pemunya);

        $ternakan = Ternakan::where('pemunya_id', $pemunya->id)->latest('id')->first();
        $this->assertNotNull($ternakan);
        $this->assertNull($ternakan->no_tag); // Belum ada tag sebelum diluluskan
        $this->assertEquals('Menunggu', $ternakan->status_kelulusan);
        $this->assertNotNull($ternakan->resit_pembayaran);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($ternakan->resit_pembayaran);

        // 2. Admin Jajahan EPTR meluluskan permohonan -> Auto generate No Tag Telinga
        $approveResponse = $this->actingAs($admin)->post("/eptr/ternakan/{$ternakan->id}/lulus");
        $approveResponse->assertRedirect();

        $ternakan->refresh();
        $this->assertEquals('Diluluskan', $ternakan->status_kelulusan);
        $this->assertEquals('Aktif', $ternakan->status);
        $this->assertEquals('Tiada', $ternakan->program); // Penternak tidak boleh mengisi program pawah, kekal Tiada
        $this->assertNotNull($ternakan->no_tag);
        $this->assertStringStartsWith('PRG-', $ternakan->no_tag); // Singkatan Daerah Peringat (PRG)

        // 3. Semakan paparan Borang B Kad Kuning
        $showResponse = $this->actingAs($user)->get("/eptr/kad-kuning-borang-b/{$ternakan->id}");
        $showResponse->assertStatus(200);
        $showResponse->assertSee($ternakan->no_tag);
    }

    public function test_pawah_agreement_linking_eptr_cattle_and_recording_birth()
    {
        $admin = User::where('role', 'admin_program')->first();
        $peserta = User::where('role', 'penternak')->first();
        $ternakan = Ternakan::first();

        // Create Pawah agreement
        $response = $this->actingAs($admin)->post('/pawah/perjanjian-baru', [
            'user_id' => $peserta->id,
            'nama_program' => 'Program Pawah Khas Test',
            'jajahan' => 'Bachok',
            'tempoh_tahun' => 3,
            'tarikh_mula' => '2026-09-01',
            'syarat_pemulangan' => 'Pulang 1 ekor anak betina.',
            'ternakan_ids' => [$ternakan->id]
        ]);

        $response->assertRedirect();
        $perjanjian = PawahPerjanjian::where('nama_program', 'Program Pawah Khas Test')->first();
        $this->assertNotNull($perjanjian);

        // Record calf birth
        $birthResponse = $this->actingAs($admin)->post("/pawah/perjanjian/{$perjanjian->id}/kelahiran", [
            'ternakan_induk_id' => $ternakan->id,
            'jantina_anak' => 'Betina',
            'tarikh_kelahiran' => '2026-09-02',
            'berat_lahir_kg' => 24.5,
            'baka_bapa' => 'Charolais',
            'warna' => 'Coklat Muda'
        ]);

        $birthResponse->assertRedirect();
        $this->assertDatabaseHas('pawah_rekod_kelahiran', [
            'pawah_perjanjian_id' => $perjanjian->id,
            'jantina_anak' => 'Betina'
        ]);
    }

    public function test_epu_poultry_farm_registration_and_inspection()
    {
        $usahawan = User::where('role', 'usahawan')->first();
        $admin = User::where('role', 'admin_epu')->first();

        // Register EPU farm (Borang A)
        $response = $this->actingAs($usahawan)->post('/epu/daftar-borang-a', [
            'nama_ladang' => 'Ladang Ayam Bersih Bachok',
            'nama_pemohon_atau_syarikat' => 'Syarikat Unggas Maju Sdn Bhd',
            'no_syarikat_atau_ssm' => 'SSM-123456-X',
            'jajahan' => 'Bachok',
            'mukim' => 'Gunong',
            'alamat_ladang' => 'Lot 888, Kg Gunong',
            'sistem_reban' => 'Tertutup',
            'jenis_unggas' => 'Ayam',
            'jurusan_aktiviti' => 'Pedaging',
            'kapasiti_maksimum_unggas' => 20000,
            'bilangan_semasa_unggas' => 15000,
            'kaedah_kawalan_lalat_bau' => 'Semburan EM',
            'jarak_kediaman_terdekat_meter' => 250,
            'jarak_sungai_terdekat_meter' => 100,
        ]);

        $response->assertRedirect();
        $ladang = EpuLadang::where('nama_ladang', 'Ladang Ayam Bersih Bachok')->first();
        $this->assertNotNull($ladang);

        // Site inspection (Borang D)
        $inspectionResponse = $this->actingAs($admin)->post("/epu/pemeriksaan-borang-d/{$ladang->id}", [
            'tarikh_pemeriksaan' => '2026-09-02',
            'skor_kebersihan_peratus' => 92,
            'patuh_zon_penampan' => 1,
            'kawalan_lalat_memuaskan' => 1,
            'kawalan_bau_memuaskan' => 1,
            'sistem_longkang_sempurna' => 1,
            'penemuan_pemeriksaan' => 'Reban sangat bersih dan canggih.',
            'syor_dan_arahan' => 'Kekalkan biosekuriti.',
            'status_keputusan' => 'Lulus'
        ]);

        $inspectionResponse->assertRedirect();
        $this->assertDatabaseHas('epu_pemeriksaan', [
            'epu_ladang_id' => $ladang->id,
            'status_keputusan' => 'Lulus'
        ]);
    }

    public function test_course_application_and_certificate()
    {
        $user = User::where('role', 'penternak')->first();
        $course = Course::first();

        $response = $this->actingAs($user)->post("/kursus/{$course->id}/daftar");
        $response->assertRedirect();

        $this->assertDatabaseHas('course_applications', [
            'user_id' => $user->id,
            'course_id' => $course->id
        ]);
    }

    public function test_clinic_appointment_and_treatment()
    {
        $user = User::where('role', 'orang_awam')->first();
        $staff = User::where('role', 'super_admin')->first();

        $response = $this->actingAs($user)->post('/klinik/temujanji-baru', [
            'jenis_haiwan' => 'Kucing',
            'nama_haiwan' => 'Mimi',
            'baka' => 'Domestic Short Hair',
            'jantina_haiwan' => 'Betina',
            'simptom_atau_tujuan' => 'Pemeriksaan rutin dan vaksinasi',
            'tarikh_temujanji' => date('Y-m-d'),
            'sesi' => 'Pagi (8:30 AM - 12:30 PM)',
            'klinik_jajahan' => 'Klinik Haiwan Kota Bharu'
        ]);

        $response->assertRedirect();
        $tj = KlinikTemujanji::where('nama_haiwan', 'Mimi')->first();
        $this->assertNotNull($tj);

        $treatmentResponse = $this->actingAs($staff)->post("/klinik/temujanji/{$tj->id}/rekod-rawatan", [
            'berat_badan_kg' => 3.2,
            'suhu_celsius' => 38.6,
            'diagnosis' => 'Kucing sihat dan cergas',
            'rawatan_diberikan' => 'Suntikan vaksin Tricat Trio',
            'ubat_diberikan' => 'Ubat Cacing',
            'vaksinasi' => 'Tricat Trio',
            'kos_rawatan' => 45.00
        ]);

        $treatmentResponse->assertRedirect();
        $this->assertDatabaseHas('klinik_rawatan', [
            'klinik_temujanji_id' => $tj->id,
            'vaksinasi' => 'Tricat Trio'
        ]);
    }

    public function test_eptr_official_original_forms_print_rendering()
    {
        $admin = User::where('role', 'admin_jajahan')->first();
        $penternak = User::where('role', 'penternak')->first();
        $ternakan = Ternakan::whereNotNull('no_tag')->first();

        // 1. Pengesahan: Orang Awam / Penternak TIDAK dibenarkan mencetak secara terus (403)
        $unauthorizedA = $this->actingAs($penternak)->get("/eptr/daftar-borang-a/{$ternakan->id}/cetak");
        $unauthorizedA->assertStatus(403);

        $unauthorizedB = $this->actingAs($penternak)->get("/eptr/kad-kuning-borang-b/{$ternakan->id}/cetak");
        $unauthorizedB->assertStatus(403);

        // 1b. Pengesahan: Ternakan BELUM LULUS (Menunggu) TIDAK boleh dicetak oleh sesiapa
        $ternakanMenunggu = Ternakan::create([
            'pemunya_id' => $ternakan->pemunya_id,
            'jenis_ternakan' => 'lembu',
            'baka' => 'charolais',
            'jantina' => 'Jantan',
            'tujuan_ternakan' => 'Pedaging',
            'jajahan' => 'Kota Bharu',
            'daerah' => 'Peringat',
            'status' => 'Menunggu',
            'status_kelulusan' => 'Menunggu',
            'no_tag' => null,
        ]);

        $unapprovedPrintA = $this->actingAs($admin)->get("/eptr/daftar-borang-a/{$ternakanMenunggu->id}/cetak");
        $unapprovedPrintA->assertRedirect();
        $unapprovedPrintA->assertSessionHas('error');

        $unapprovedPrintB = $this->actingAs($admin)->get("/eptr/kad-kuning-borang-b/{$ternakanMenunggu->id}/cetak");
        $unapprovedPrintB->assertRedirect();
        $unapprovedPrintB->assertSessionHas('error');

        // 2. Uji Cetak Borang A Asal oleh Admin Jajahan bagi ternakan yang telah diluluskan
        $responseA = $this->actingAs($admin)->get("/eptr/daftar-borang-a/{$ternakan->id}/cetak");
        $responseA->assertStatus(200);
        $responseA->assertSee('ENAKMEN PENDAFTARAN TERNAKAN RUMINAN 2024');
        $responseA->assertSee('JADUAL PERTAMA');
        $responseA->assertSee('BORANG A');
        $responseA->assertSee('[subseksyen 5 (4)]');
        $responseA->assertSee('DAFTAR TERNAKAN RUMINAN');
        $responseA->assertSee('Kelahiran');
        $responseA->assertSee('Pindah Milik');
        $responseA->assertSee('Sembelih');

        // 3. Uji Cetak Borang B Kad Kuning Asal oleh Admin Jajahan
        $responseB = $this->actingAs($admin)->get("/eptr/kad-kuning-borang-b/{$ternakan->id}/cetak");
        $responseB->assertStatus(200);
        $responseB->assertSee('ENAKMEN PENDAFTARAN TERNAKAN RUMINAN 2024');
        $responseB->assertSee('JADUAL KEDUA');
        $responseB->assertSee('BORANG B');
        $responseB->assertSee('[subseksyen 5 (4)]');
        $responseB->assertSee('PERAKUAN DAFTAR TERNAKAN RUMINAN');
        $responseB->assertSee('PERTUKARAN MILIKAN');
        $responseB->assertSee('TANDATANGAN DAN COP PENOLONG PENDAFTAR');

        // 4. Uji Cetak Borang C Asal
        $pembatalan = \App\Models\PembatalanTernakan::first();
        if (!$pembatalan) {
            $pembatalan = \App\Models\PembatalanTernakan::create([
                'ternakan_id' => $ternakan->id,
                'jenis_batal' => 'Mati',
                'tarikh_peristiwa' => '2026-09-02',
                'sebab' => 'Sakit tua',
                'status_kelulusan' => 'Disahkan'
            ]);
        }

        // Pengesahan penternak tidak boleh cetak Borang C (403)
        $unauthorizedC = $this->actingAs($penternak)->get("/eptr/pembatalan-borang-c/{$pembatalan->id}/cetak");
        $unauthorizedC->assertStatus(403);

        $responseC = $this->actingAs($admin)->get("/eptr/pembatalan-borang-c/{$pembatalan->id}/cetak");
        $responseC->assertStatus(200);
        $responseC->assertSee('ENAKMEN PENDAFTARAN TERNAKAN RUMINAN 2024');
        $responseC->assertSee('JADUAL KETIGA');
        $responseC->assertSee('BORANG C');
        $responseC->assertSee('[subseksyen 11 (2)]');
        $responseC->assertSee('PEMBATALAN PENDAFTARAN TERNAKAN RUMINAN');
        $responseC->assertSee('Kematian');

        // 5. Uji Cetak Borang D Asal
        $permit = \App\Models\PermitSembelihan::first();
        if (!$permit) {
            $permit = \App\Models\PermitSembelihan::create([
                'no_permit' => 'PS-KB-2026-001',
                'pemunya_id' => $ternakan->pemunya_id,
                'ternakan_id' => $ternakan->id,
                'tujuan_sembelih' => 'Ibadah Korban',
                'tarikh_sembelih' => '2026-09-10',
                'nama_premis_sembelih' => 'Rumah Penyembelihan Kota Bharu',
                'alamat_premis_sembelih' => 'Peringat, Kota Bharu',
                'status_kelulusan' => 'Diluluskan'
            ]);
        }

        // Pengesahan penternak tidak boleh cetak Borang D (403)
        $unauthorizedD = $this->actingAs($penternak)->get("/eptr/permit-sembelihan-borang-d/{$permit->id}/cetak");
        $unauthorizedD->assertStatus(403);

        $responseD = $this->actingAs($admin)->get("/eptr/permit-sembelihan-borang-d/{$permit->id}/cetak");
        $responseD->assertStatus(200);
        $responseD->assertSee('ENAKMEN PENDAFTARAN TERNAKAN RUMINAN 2024');
        $responseD->assertSee('JADUAL KEEMPAT');
        $responseD->assertSee('BORANG D');
        $responseD->assertSee('[subseksyen 11(1) (b)]');
        $responseD->assertSee('PEMBATALAN PENDAFTARAN TERNAKAN RUMINAN AKIBAT SEMBELIHAN');
        $responseD->assertSee('Kaedah 17 Kaedah-Kaedah Binatang');

        // 6. Uji Cetak Pukal Borang A & Borang B bagi berbilang ternakan / pemilik terpilih
        $allIds = Ternakan::whereNotNull('no_tag')->pluck('id')->toArray();

        $bulkA = $this->actingAs($admin)->post('/eptr/cetak-pukal-borang-a', [
            'ids' => implode(',', $allIds)
        ]);
        $bulkA->assertStatus(200);
        $bulkA->assertSee('CETAK PUKAL BORANG A');
        $bulkA->assertSee('DAFTAR TERNAKAN RUMINAN');

        $bulkB = $this->actingAs($admin)->post('/eptr/cetak-pukal-borang-b', [
            'ids' => implode(',', $allIds)
        ]);
        $bulkB->assertStatus(200);
        $bulkB->assertSee('CETAK PUKAL BORANG B KAD KUNING');
        $bulkB->assertSee('PERAKUAN DAFTAR TERNAKAN RUMINAN');
    }

    public function test_eptr_offspring_registration_creates_child_livestock_and_links_to_mother()
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $user = User::where('role', 'penternak')->first();
        $induk = Ternakan::where('jantina', 'Betina')->first();

        // 1. Uji cubaan daftar anak oleh penternak tanpa resit (Gagal validasi)
        $respNoReceipt = $this->actingAs($user)->post('/eptr/daftar-anak', [
            'induk_id' => $induk->id,
            'tarikh_kelahiran' => date('Y-m-d'),
            'jantina_anak' => 'Betina',
            'baka_anak' => 'charolais',
            'berat_lahir_kg' => 29.5,
            'warna_anak' => 'Kuning Krim',
            'status_kelahiran' => 'Hidup',
            'keadaan_anak' => 'Cergas',
        ]);
        $respNoReceipt->assertSessionHasErrors(['resit_pembayaran']);

        // 2. Uji pendaftaran anak bersama lampiran fail resit yang sah (Berjaya)
        $receiptFile = \Illuminate\Http\UploadedFile::fake()->create('resit_anak_eptr.pdf', 300, 'application/pdf');

        $response = $this->actingAs($user)->post('/eptr/daftar-anak', [
            'induk_id' => $induk->id,
            'tarikh_kelahiran' => date('Y-m-d'),
            'jantina_anak' => 'Betina',
            'baka_anak' => 'charolais',
            'berat_lahir_kg' => 29.5,
            'warna_anak' => 'Kuning Krim',
            'tanda_badan_anak' => 'Tompok putih di dahi',
            'status_kelahiran' => 'Hidup',
            'keadaan_anak' => 'Cergas',
            'catatan' => 'Kelahiran anak betina baka Charolais',
            'resit_pembayaran' => $receiptFile,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('rekod_kelahiran', [
            'induk_id' => $induk->id,
            'jantina_anak' => 'Betina',
            'baka_anak' => 'charolais',
            'status_kelahiran' => 'Hidup'
        ]);

        // Semak bahawa profil ternakan baharu untuk anak telah dicipta secara automatik berserta resit
        $this->assertDatabaseHas('ternakan', [
            'no_tanda_pengenalan_induk' => $induk->no_tag ?? 'INDUK-' . $induk->id,
            'baka' => 'charolais',
            'jantina' => 'Betina',
            'umur' => 'Baru Lahir'
        ]);
    }

    public function test_livestock_health_program_recording_and_booster_alert()
    {
        $admin = User::where('role', 'admin_eptr')->first();
        $ternakan = Ternakan::first();

        // 1. Tambah rekod program kesihatan
        $response = $this->actingAs($admin)->post('/eptr/program-kesihatan/daftar', [
            'ternakan_id' => $ternakan->id,
            'jenis_program' => 'Vaksinasi / Imunisasi',
            'nama_vaksin_atau_ubat' => 'Vaksin Haemorrhagic Septicaemia (HS)',
            'tarikh_rawatan' => date('Y-m-d'),
            'tarikh_ulangan_dos' => Carbon::now()->addDays(180)->toDateString(),
            'dos_diberikan' => '3.0 ml Subkutan',
            'berat_semasa_kg' => 340.0,
            'suhu_badan_celsius' => 38.7,
            'status_kesihatan' => 'Sihat & Cergas',
            'pegawai_pemeriksa' => 'Dr. Mohd Fauzi bin Abdullah',
            'diagnosis_atau_tujuan' => 'Program Vaksinasi Hawar Berdarah Tahunan',
            'tindakan_rawatan' => 'Suntikan vaksin HS dos primer',
            'catatan_dan_syor' => 'Rehatkan ternakan dan pantau tindak balas 24 jam',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('program_kesihatan', [
            'ternakan_id' => $ternakan->id,
            'nama_vaksin_atau_ubat' => 'Vaksin Haemorrhagic Septicaemia (HS)',
            'jenis_program' => 'Vaksinasi / Imunisasi',
            'pegawai_pemeriksa' => 'Dr. Mohd Fauzi bin Abdullah'
        ]);

        // 2. Semak paparan senarai Program Kesihatan
        $indexResponse = $this->actingAs($admin)->get('/eptr/program-kesihatan');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Vaksin Haemorrhagic Septicaemia (HS)');

        // 3. Semak paparan butiran rekod kesihatan
        $rekod = \App\Models\ProgramKesihatan::where('nama_vaksin_atau_ubat', 'Vaksin Haemorrhagic Septicaemia (HS)')->first();
        $this->assertNotNull($rekod);

        $showResponse = $this->actingAs($admin)->get("/eptr/program-kesihatan/{$rekod->id}");
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Dr. Mohd Fauzi bin Abdullah');
        $showResponse->assertSee('Program Vaksinasi Hawar Berdarah Tahunan');
    }

    public function test_borang_c_and_borang_d_approval_flow_by_admin_jajahan()
    {
        $admin = User::where('role', 'admin_jajahan')->first();
        $penternak = User::where('role', 'penternak')->first();
        $ternakan1 = Ternakan::where('status', 'Aktif')->first();
        $ternakan2 = Ternakan::where('status', 'Aktif')->skip(1)->first() ?? $ternakan1;

        // 1. Uji Penternak Hantar Borang C (Status: Menunggu)
        $pembatalan = \App\Models\PembatalanTernakan::create([
            'ternakan_id' => $ternakan1->id,
            'jenis_batal' => 'Mati',
            'tarikh_peristiwa' => date('Y-m-d'),
            'sebab' => 'Penyakit semulajadi',
            'status_kelulusan' => 'Menunggu',
        ]);

        // Pengesahan bukan staf tidak boleh luluskan (403 / error)
        $this->actingAs($penternak)->post("/eptr/pembatalan-borang-c/{$pembatalan->id}/lulus");
        $this->assertEquals('Menunggu', $pembatalan->fresh()->status_kelulusan);

        // Admin Jajahan Sahkan / Luluskan Borang C
        $responseC = $this->actingAs($admin)->post("/eptr/pembatalan-borang-c/{$pembatalan->id}/lulus");
        $responseC->assertRedirect();
        $this->assertEquals('Disahkan', $pembatalan->fresh()->status_kelulusan);
        $this->assertEquals('Mati', $ternakan1->fresh()->status);

        // 2. Uji Penternak Mohon Permit Sembelihan Borang D (Status: Menunggu)
        $permit = \App\Models\PermitSembelihan::create([
            'no_permit' => 'PS-TEST-001',
            'pemunya_id' => $ternakan2->pemunya_id,
            'ternakan_id' => $ternakan2->id,
            'tujuan_sembelih' => 'Ibadah Aqiqah',
            'tarikh_sembelih' => date('Y-m-d'),
            'status_kelulusan' => 'Menunggu',
        ]);

        // Admin Jajahan Luluskan Borang D
        $responseD = $this->actingAs($admin)->post("/eptr/permit-sembelihan-borang-d/{$permit->id}/lulus");
        $responseD->assertRedirect();
        $this->assertEquals('Diluluskan', $permit->fresh()->status_kelulusan);
        $this->assertEquals('Sembelih', $ternakan2->fresh()->status);
    }

    public function test_bulk_printing_for_borang_c_and_borang_d()
    {
        $admin = User::where('role', 'admin_jajahan')->first();
        $penternak = User::where('role', 'penternak')->first();
        $ternakan1 = Ternakan::where('status', 'Aktif')->first();

        $pembatalan1 = \App\Models\PembatalanTernakan::create([
            'ternakan_id' => $ternakan1->id,
            'jenis_batal' => 'Mati',
            'tarikh_peristiwa' => date('Y-m-d'),
            'sebab' => 'Penyakit semulajadi',
            'status_kelulusan' => 'Disahkan',
            'disahkan_oleh' => $admin->id,
        ]);

        $permit1 = \App\Models\PermitSembelihan::create([
            'no_permit' => 'PS-PUKAL-001',
            'pemunya_id' => $ternakan1->pemunya_id,
            'ternakan_id' => $ternakan1->id,
            'tujuan_sembelih' => 'Kenduri Kahwin',
            'tarikh_sembelih' => date('Y-m-d'),
            'status_kelulusan' => 'Diluluskan',
            'diluluskan_oleh' => $admin->id,
        ]);

        // 1. Uji Penternak cuba cetak pukal Borang C & D (Akses ditolak 403)
        $respPublicC = $this->actingAs($penternak)->post('/eptr/pembatalan-borang-c/cetak-pukal', [
            'ids' => [$pembatalan1->id]
        ]);
        $respPublicC->assertStatus(403);

        $respPublicD = $this->actingAs($penternak)->post('/eptr/permit-sembelihan-borang-d/cetak-pukal', [
            'ids' => [$permit1->id]
        ]);
        $respPublicD->assertStatus(403);

        // 2. Uji Admin Jajahan mencetak pukal Borang C (200 OK)
        $respAdminC = $this->actingAs($admin)->post('/eptr/pembatalan-borang-c/cetak-pukal', [
            'ids' => [$pembatalan1->id]
        ]);
        $respAdminC->assertStatus(200);
        $respAdminC->assertSee('CETAK PUKAL BORANG C');
        $respAdminC->assertSee('PEMBATALAN PENDAFTARAN TERNAKAN RUMINAN');

        // 3. Uji Admin Jajahan mencetak pukal Borang D (200 OK)
        $respAdminD = $this->actingAs($admin)->post('/eptr/permit-sembelihan-borang-d/cetak-pukal', [
            'ids' => [$permit1->id]
        ]);
        $respAdminD->assertStatus(200);
        $respAdminD->assertSee('CETAK PUKAL BORANG D');
        $respAdminD->assertSee('PEMBATALAN PENDAFTARAN TERNAKAN RUMINAN AKIBAT SEMBELIHAN');
    }

    public function test_filtering_by_owner_and_ic_for_borang_c_and_borang_d()
    {
        $admin = User::where('role', 'admin_jajahan')->first();
        $pemunya = Pemunya::first();

        // 1. Uji Penapisan Borang C mengikut No Kad Pengenalan
        $respC = $this->actingAs($admin)->get('/eptr/pembatalan-borang-c?no_kp=' . $pemunya->no_kp);
        $respC->assertStatus(200);
        $respC->assertSee($pemunya->nama);

        // 2. Uji Penapisan Borang D mengikut Pemunya ID
        $respD = $this->actingAs($admin)->get('/eptr/permit-sembelihan-borang-d?pemunya_id=' . $pemunya->id);
        $respD->assertStatus(200);
        $respD->assertSee($pemunya->nama);
    }

    public function test_farmer_must_attach_payment_receipt_when_registering_livestock_borang_a()
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $penternak = User::where('role', 'penternak')->first();
        $admin = User::where('role', 'admin_jajahan')->first();

        // 1. Penternak hantar tanpa resit -> Ralat validasi (resit_pembayaran wajib)
        $respNoReceipt = $this->actingAs($penternak)->post('/eptr/daftar-borang-a', [
            'nama_pemunya' => 'Ahmad Penternak',
            'no_kp_pemunya' => '900101035511',
            'no_tel_pemunya' => '0129998888',
            'alamat_pemunya' => 'Kg Kok Lanas',
            'jenis_ternakan' => 'lembu',
            'baka' => 'charolais',
            'jantina' => 'Betina',
            'tarikh_lahir' => '2024-06-01',
            'tujuan_ternakan' => 'Pembiakan',
            'jajahan' => 'Kota Bharu',
            'daerah' => 'Peringat',
        ]);
        $respNoReceipt->assertSessionHasErrors(['resit_pembayaran']);

        // 2. Penternak hantar berserta fail resit pembayaran yang sah -> Berjaya
        $receiptFile = \Illuminate\Http\UploadedFile::fake()->create('resit_bayaran_eptr.pdf', 500, 'application/pdf');
        $respWithReceipt = $this->actingAs($penternak)->post('/eptr/daftar-borang-a', [
            'nama_pemunya' => 'Ahmad Penternak',
            'no_kp_pemunya' => '900101035511',
            'no_tel_pemunya' => '0129998888',
            'alamat_pemunya' => 'Kg Kok Lanas',
            'jenis_ternakan' => 'lembu',
            'baka' => 'charolais',
            'jantina' => 'Betina',
            'tarikh_lahir' => '2024-06-01',
            'tujuan_ternakan' => 'Pembiakan',
            'jajahan' => 'Kota Bharu',
            'daerah' => 'Peringat',
            'resit_pembayaran' => $receiptFile,
        ]);
        $respWithReceipt->assertRedirect();
        $this->assertDatabaseHas('ternakan', [
            'baka' => 'charolais',
            'status_kelulusan' => 'Menunggu'
        ]);

        // 3. Pengesahan: Admin Jajahan & Admin EPTR disekat daripada mendaftar Borang A
        $respStaff = $this->actingAs($admin)->post('/eptr/daftar-borang-a', [
            'nama_pemunya' => 'Pak Daud',
            'no_kp_pemunya' => '700101036622',
            'no_tel_pemunya' => '0191234567',
            'alamat_pemunya' => 'Kg Sireh',
            'jenis_ternakan' => 'lembu',
            'baka' => 'brahman',
            'jantina' => 'Jantan',
            'tujuan_ternakan' => 'Pedaging',
            'jajahan' => 'Kota Bharu',
            'daerah' => 'Kota',
        ]);
        $respStaff->assertRedirect(route('eptr.index'));
        $respStaff->assertSessionHas('error');
    }

    public function test_all_admins_except_super_admin_cannot_access_or_register_borang_a_and_daftar_anak()
    {
        $adminJajahan = User::where('role', 'admin_jajahan')->first();
        $adminEptr = User::where('role', 'admin_eptr')->first();
        $adminProgram = User::where('role', 'admin_program')->first();
        $superAdmin = User::where('role', 'super_admin')->first();

        // 1. Admin Jajahan cuba buka Borang A -> Disekat
        $getJajahanA = $this->actingAs($adminJajahan)->get('/eptr/daftar-borang-a');
        $getJajahanA->assertRedirect(route('eptr.index'));
        $getJajahanA->assertSessionHas('error');

        // 2. Admin Jajahan cuba buka Daftar Anak -> Disekat
        $getJajahanAnak = $this->actingAs($adminJajahan)->get('/eptr/daftar-anak');
        $getJajahanAnak->assertRedirect(route('eptr.index'));
        $getJajahanAnak->assertSessionHas('error');

        // 3. Admin EPTR cuba hantar POST Daftar Anak -> Disekat
        $postEptrAnak = $this->actingAs($adminEptr)->post('/eptr/daftar-anak', [
            'induk_id' => 1,
            'tarikh_kelahiran' => date('Y-m-d'),
            'jantina_anak' => 'Jantan',
            'baka_anak' => 'kedah-kelantan',
            'status_kelahiran' => 'Hidup',
            'keadaan_anak' => 'Cergas',
        ]);
        $postEptrAnak->assertRedirect(route('eptr.index'));
        $postEptrAnak->assertSessionHas('error');

        // 4. Admin Program cuba akses Borang A -> Disekat 403
        $getProgramA = $this->actingAs($adminProgram)->get('/eptr/daftar-borang-a');
        $getProgramA->assertStatus(403);
        $postProgramA = $this->actingAs($adminProgram)->post('/eptr/daftar-borang-a', [
            'nama_pemunya' => 'Ternakan Cubaan',
            'no_kp_pemunya' => '990101039999',
            'jenis_ternakan' => 'lembu',
            'baka' => 'kedah-kelantan',
            'jantina' => 'Jantan',
            'tujuan_ternakan' => 'Pedaging',
            'jajahan' => 'Kota Bharu',
            'daerah' => 'Peringat',
        ]);
        $postProgramA->assertStatus(403);

        // 5. Super Admin DIBENARKAN buka Borang A & Daftar Anak
        $getSuperA = $this->actingAs($superAdmin)->get('/eptr/daftar-borang-a');
        $getSuperA->assertStatus(200);

        $getSuperAnak = $this->actingAs($superAdmin)->get('/eptr/daftar-anak');
        $getSuperAnak->assertStatus(200);
    }

    public function test_admin_pejabat_can_only_access_pengurusan_pejabat_and_blocked_from_veterinary_services()
    {
        $adminPejabat = User::where('role', 'admin_pejabat')->first();

        // 1. Admin Pejabat Boleh Akses Stor Peralatan Pejabat
        $getInventori = $this->actingAs($adminPejabat)->get('/inventori/pejabat');
        $getInventori->assertStatus(200);

        // 2. Admin Pejabat DISEKAT daripada Modul Kenderaan Rasmi
        $getKenderaan = $this->actingAs($adminPejabat)->get('/kenderaan');
        $getKenderaan->assertStatus(403);

        // 3. Admin Pejabat Disekat daripada Modul EPTR Ruminan
        $getEptr = $this->actingAs($adminPejabat)->get('/eptr');
        $getEptr->assertRedirect(route('inventori.index'));
        $getEptr->assertSessionHas('error');

        // 4. Admin Pejabat Disekat daripada Modul Program Pawah
        $getPawah = $this->actingAs($adminPejabat)->get('/pawah');
        $getPawah->assertRedirect(route('inventori.index'));
        $getPawah->assertSessionHas('error');

        // 5. Admin Pejabat Disekat daripada Modul EPU Unggas
        $getEpu = $this->actingAs($adminPejabat)->get('/epu');
        $getEpu->assertRedirect(route('inventori.index'));
        $getEpu->assertSessionHas('error');

        // 6. Admin Pejabat Disekat daripada Modul Klinik Haiwan
        $getKlinik = $this->actingAs($adminPejabat)->get('/klinik');
        $getKlinik->assertRedirect(route('inventori.index'));
        $getKlinik->assertSessionHas('error');

        // 7. Dashboard memaparkan maklumat Pengurusan Stor Pejabat bagi Admin Pejabat
        $getDashboard = $this->actingAs($adminPejabat)->get('/dashboard');
        $getDashboard->assertStatus(200);
        $getDashboard->assertSee('Admin Stor Pejabat');
        $getDashboard->assertSee('Jumlah Barangan Pejabat');
        $getDashboard->assertSee('Stor Pejabat');
    }

    public function test_kenderaan_tempahan_create_store_show_flow()
    {
        $staf = User::where('role', 'staf')->first();

        // 1. Buka borang tempahan kenderaan baharu
        $getCreate = $this->actingAs($staf)->get('/kenderaan/tempahan-baru');
        $getCreate->assertStatus(200);
        $getCreate->assertSee('Borang Permohonan Penggunaan Kenderaan');

        // 2. Hantar borang tempahan kenderaan
        $postStore = $this->actingAs($staf)->post('/kenderaan/tempahan-baru', [
            'destinasi' => 'Pusat Veterinar Jajahan Pasir Mas',
            'tujuan' => 'Operasi pemantauan ladang dan logistik veterinar',
            'tarikh_keluar' => date('Y-m-d\TH:i'),
            'tarikh_kembali' => date('Y-m-d\TH:i', strtotime('+6 hours')),
            'bilangan_penumpang' => 3,
            'nama_pemandu' => 'En. Ahmad Pemandu',
        ]);
        $postStore->assertRedirect(route('kenderaan.index'));
        $postStore->assertSessionHas('success');

        $this->assertDatabaseHas('kenderaan_tempahan', [
            'destinasi' => 'Pusat Veterinar Jajahan Pasir Mas',
            'bilangan_penumpang' => 3,
            'status' => 'Menunggu',
        ]);
    }

    public function test_pegawai_kelulusan_dan_penolakan_tempahan_kenderaan()
    {
        $adminKenderaan = User::where('role', 'admin_kenderaan')->first() ?: User::factory()->create(['role' => 'admin_kenderaan', 'email' => 'adminkenderaan_test@veterinar.kelantan.gov.my']);
        $penternak = User::where('role', 'penternak')->first();
        $kenderaan = \App\Models\Kenderaan::where('status', 'Sedia')->first();

        // 1. Cipta 2 tempahan kenderaan status Menunggu
        $tempahan1 = \App\Models\KenderaanTempahan::create([
            'user_id' => $penternak->id,
            'no_tempahan' => 'KND-TEST-001',
            'tujuan_perjalanan' => 'Urusan rasmi pengangkutan sampel',
            'destinasi' => 'Makmal Veterinar Bachok',
            'tarikh_mula' => date('Y-m-d'),
            'masa_mula' => '09:00',
            'tarikh_tamat' => date('Y-m-d'),
            'masa_tamat' => '15:00',
            'bilangan_penumpang' => 2,
            'status' => 'Menunggu',
        ]);

        $tempahan2 = \App\Models\KenderaanTempahan::create([
            'user_id' => $penternak->id,
            'no_tempahan' => 'KND-TEST-002',
            'tujuan_perjalanan' => 'Urusan pemantauan ladang',
            'destinasi' => 'Ladang Jeli',
            'tarikh_mula' => date('Y-m-d'),
            'masa_mula' => '08:00',
            'tarikh_tamat' => date('Y-m-d'),
            'masa_tamat' => '17:00',
            'bilangan_penumpang' => 4,
            'status' => 'Menunggu',
        ]);

        // 2. Pegawai Kenderaan Luluskan Tempahan 1 dengan tugasan kenderaan dan pemandu
        $postLulus = $this->actingAs($adminKenderaan)->post("/kenderaan/tempahan/{$tempahan1->id}/kelulusan", [
            'status' => 'Diluluskan',
            'kenderaan_id' => $kenderaan->id,
            'pemandu_nama' => 'En. Razak Pemandu Khas',
            'catatan_kelulusan' => 'Diluluskan untuk urusan makmal rasmi.',
        ]);
        $postLulus->assertRedirect();
        $postLulus->assertSessionHas('success');

        $tempahan1->refresh();
        $this->assertEquals('Diluluskan', $tempahan1->status);
        $this->assertEquals($kenderaan->id, $tempahan1->kenderaan_id);
        $this->assertEquals('En. Razak Pemandu Khas', $tempahan1->pemandu_nama);
        $this->assertEquals($adminKenderaan->id, $tempahan1->diluluskan_oleh);

        // 3. Pegawai Kenderaan Tolak Tempahan 2 dengan sebab penolakan
        $postTolak = $this->actingAs($adminKenderaan)->post("/kenderaan/tempahan/{$tempahan2->id}/tolak", [
            'sebab_tolak' => 'Tiada kenderaan 4x4 sedia pada tarikh tersebut kerana bertindih operasi.',
        ]);
        $postTolak->assertRedirect();
        $postTolak->assertSessionHas('success');

        $tempahan2->refresh();
        $this->assertEquals('Ditolak', $tempahan2->status);
        $this->assertStringContainsString('Tiada kenderaan 4x4', $tempahan2->catatan_kelulusan);
        $this->assertEquals($adminKenderaan->id, $tempahan2->diluluskan_oleh);

        // 4. Semak paparan index mengandungi penapis dan butang tindakan
        $getIndex = $this->actingAs($adminKenderaan)->get('/kenderaan');
        $getIndex->assertStatus(200);
        $getIndex->assertSee('Menunggu Kelulusan');
        $getIndex->assertSee('Diluluskan');
        $getIndex->assertSee('Ditolak');

        // 5. Semak paparan butiran show memaparkan maklumat pegawai pelulus
        $getShow = $this->actingAs($adminKenderaan)->get("/kenderaan/tempahan/{$tempahan1->id}");
        $getShow->assertStatus(200);
        $getShow->assertSee('Permohonan Tempahan Telah Diluluskan');
        $getShow->assertSee($adminKenderaan->name);
    }

    public function test_pengurusan_maklumat_pemandu_jabatan()
    {
        $adminKenderaan = User::where('role', 'admin_kenderaan')->first() ?: User::factory()->create(['role' => 'admin_kenderaan', 'email' => 'adminkenderaan_test@veterinar.kelantan.gov.my']);

        // 1. Semak paparan senarai pemandu
        $getIndex = $this->actingAs($adminKenderaan)->get('/kenderaan/pemandu');
        $getIndex->assertStatus(200);
        $getIndex->assertSee('Direktori Pemandu Rasmi Jabatan');
        $getIndex->assertSee('En. Che Rosli bin Dollah');

        // 2. Tambah maklumat pemandu baharu
        $postStore = $this->actingAs($adminKenderaan)->post('/kenderaan/pemandu', [
            'nama' => 'En. Zulkifli bin Mat Yasin',
            'no_kp' => '870101035599',
            'no_pekerja' => 'VET-DVR-005',
            'no_telefon' => '019-9988776',
            'kelas_lesen' => 'D, E, GDL',
            'tarikh_tamat_lesen' => date('Y-m-d', strtotime('+1 year')),
            'jajahan_penempatan' => 'Kota Bharu',
            'status' => 'Aktif',
            'catatan' => 'Pemandu berpengalaman operasi penguatkuasaan.',
        ]);
        $postStore->assertRedirect(route('kenderaan.pemandu.index'));
        $postStore->assertSessionHas('success');

        $this->assertDatabaseHas('pemandu', [
            'no_kp' => '870101035599',
            'nama' => 'En. Zulkifli bin Mat Yasin',
            'status' => 'Aktif',
        ]);

        $pemandu = \App\Models\Pemandu::where('no_kp', '870101035599')->first();

        // 3. Kemaskini maklumat pemandu
        $putUpdate = $this->actingAs($adminKenderaan)->put("/kenderaan/pemandu/{$pemandu->id}", [
            'nama' => 'En. Zulkifli bin Mat Yasin (Kanan)',
            'no_kp' => '870101035599',
            'no_pekerja' => 'VET-DVR-005',
            'no_telefon' => '019-9988777',
            'kelas_lesen' => 'D, E, GDL, PSV',
            'tarikh_tamat_lesen' => date('Y-m-d', strtotime('+2 years')),
            'jajahan_penempatan' => 'Ibu Pejabat Kota Bharu',
            'status' => 'Bertugas',
            'catatan' => 'Dinaikkan gred pemandu kanan.',
        ]);
        $putUpdate->assertRedirect(route('kenderaan.pemandu.index'));
        $putUpdate->assertSessionHas('success');

        $pemandu->refresh();
        $this->assertEquals('En. Zulkifli bin Mat Yasin (Kanan)', $pemandu->nama);
        $this->assertEquals('Bertugas', $pemandu->status);
        $this->assertEquals('D, E, GDL, PSV', $pemandu->kelas_lesen);

        // 4. Padam maklumat pemandu
        $delete = $this->actingAs($adminKenderaan)->delete("/kenderaan/pemandu/{$pemandu->id}");
        $delete->assertRedirect(route('kenderaan.pemandu.index'));
        $delete->assertSessionHas('success');

        $this->assertDatabaseMissing('pemandu', [
            'id' => $pemandu->id,
        ]);
    }

    public function test_pengurusan_maklumat_fleet_kenderaan_jabatan()
    {
        $adminKenderaan = User::where('role', 'admin_kenderaan')->first() ?: User::factory()->create(['role' => 'admin_kenderaan', 'email' => 'adminkenderaan_test@veterinar.kelantan.gov.my']);

        // 1. Semak paparan senarai fleet kenderaan
        $getFleet = $this->actingAs($adminKenderaan)->get('/kenderaan/fleet');
        $getFleet->assertStatus(200);
        $getFleet->assertSee('Pengurusan Fleet Kenderaan Rasmi');
        $getFleet->assertSee('DDX 8812');

        // 2. Tambah kenderaan baharu
        $postStore = $this->actingAs($adminKenderaan)->post('/kenderaan/kenderaan-baru', [
            'no_pendaftaran' => 'DEA 9999',
            'jenis_kenderaan' => 'Pacuan 4 Roda (4x4)',
            'model' => 'Isuzu D-Max 3.0 4x4 X-Terrain',
            'tahun_buatan' => 2024,
            'kapasiti_penumpang' => 5,
            'jajahan_penempatan' => 'Gua Musang',
            'status' => 'Sedia',
            'lokasi_kunci' => 'Papan Kunci Pejabat Veterinar Gua Musang',
            'odometer_semasa_km' => 12500,
            'tarikh_tamat_cukai_jalan' => date('Y-m-d', strtotime('+1 year')),
            'catatan' => 'Kenderaan pacuan 4 roda operasi veterinar pedalaman Gua Musang.',
        ]);
        $postStore->assertRedirect();
        $postStore->assertSessionHas('success');

        $this->assertDatabaseHas('kenderaan', [
            'no_pendaftaran' => 'DEA 9999',
            'model' => 'Isuzu D-Max 3.0 4x4 X-Terrain',
            'jajahan_penempatan' => 'Gua Musang',
            'status' => 'Sedia',
        ]);

        $kenderaan = \App\Models\Kenderaan::where('no_pendaftaran', 'DEA 9999')->first();

        // 3. Kemaskini maklumat kenderaan
        $putUpdate = $this->actingAs($adminKenderaan)->put("/kenderaan/kenderaan/{$kenderaan->id}", [
            'no_pendaftaran' => 'DEA 9999',
            'jenis_kenderaan' => 'Pacuan 4 Roda (4x4)',
            'model' => 'Isuzu D-Max 3.0 4x4 X-Terrain (Kemaskini)',
            'tahun_buatan' => 2024,
            'kapasiti_penumpang' => 5,
            'jajahan_penempatan' => 'Ibu Pejabat Kota Bharu',
            'status' => 'Sedang Digunakan',
            'lokasi_kunci' => 'Papan Kunci Pejabat Pentadbiran JPVNK',
            'odometer_semasa_km' => 13200,
            'tarikh_tamat_cukai_jalan' => date('Y-m-d', strtotime('+1 year')),
            'catatan' => 'Dipindahkan sementara ke Ibu Pejabat Kota Bharu.',
        ]);
        $putUpdate->assertRedirect();
        $putUpdate->assertSessionHas('success');

        $kenderaan->refresh();
        $this->assertEquals('Isuzu D-Max 3.0 4x4 X-Terrain (Kemaskini)', $kenderaan->model);
        $this->assertEquals('Ibu Pejabat Kota Bharu', $kenderaan->jajahan_penempatan);
        $this->assertEquals('Sedang Digunakan', $kenderaan->status);
        $this->assertEquals(13200, $kenderaan->odometer_semasa_km);

        // 4. Padam kenderaan
        $delete = $this->actingAs($adminKenderaan)->delete("/kenderaan/kenderaan/{$kenderaan->id}");
        $delete->assertRedirect();
        $delete->assertSessionHas('success');

        $this->assertDatabaseMissing('kenderaan', [
            'id' => $kenderaan->id,
        ]);
    }

    public function test_sekatan_tempahan_kenderaan_mengikut_peranan()
    {
        // 1. Peranan yang DIBENARKAN membuat tempahan: super_admin, staf, admin_kenderaan
        $allowedRoles = ['super_admin', 'staf', 'admin_kenderaan'];
        foreach ($allowedRoles as $role) {
            $user = User::where('role', $role)->first();
            if ($user) {
                $getCreate = $this->actingAs($user)->get('/kenderaan/tempahan-baru');
                $getCreate->assertStatus(200);

                $postStore = $this->actingAs($user)->post('/kenderaan/tempahan-baru', [
                    'destinasi' => 'Pusat Operasi Veterinar ' . $role,
                    'tujuan' => 'Urusan rasmi jabatan oleh ' . $role,
                    'tarikh_keluar' => date('Y-m-d\TH:i'),
                    'tarikh_kembali' => date('Y-m-d\TH:i', strtotime('+4 hours')),
                    'bilangan_penumpang' => 2,
                    'nama_pemandu' => $user->name,
                ]);
                $postStore->assertRedirect(route('kenderaan.index'));
            }
        }

        // 2. Peranan yang DISEKAT daripada modul Kenderaan Rasmi (admin_pejabat, admin_epu, admin_eptr, admin_program, admin_kursus, admin_jajahan, penternak, usahawan, orang_awam):
        $blockedVehicleRoles = ['admin_pejabat', 'admin_epu', 'admin_eptr', 'admin_program', 'admin_kursus', 'admin_jajahan', 'penternak', 'usahawan', 'orang_awam'];
        foreach ($blockedVehicleRoles as $blockedRole) {
            $blockedUser = User::where('role', $blockedRole)->first();
            if ($blockedUser) {
                // Sekatan akses modul Kenderaan Rasmi (403 Forbidden)
                $this->actingAs($blockedUser)->get('/kenderaan')->assertStatus(403);
                $this->actingAs($blockedUser)->get('/kenderaan/tempahan-baru')->assertStatus(403);

                // Cuba hantar tempahan -> 403 Forbidden
                $postStore = $this->actingAs($blockedUser)->post('/kenderaan/tempahan-baru', [
                    'destinasi' => 'Destinasi Tidak Dibenarkan',
                    'tujuan' => 'Percubaan tempahan oleh ' . $blockedRole,
                    'tarikh_mula' => date('Y-m-d'),
                    'masa_mula' => '09:00',
                    'tarikh_tamat' => date('Y-m-d'),
                    'masa_tamat' => '12:00',
                    'bilangan_penumpang' => 1,
                ]);
                $postStore->assertStatus(403);
            }
        }

        // 3. Pengguna Awam / Penternak disekat daripada inventori (403 Forbidden)
        $publicRoles = ['penternak', 'usahawan', 'orang_awam'];
        foreach ($publicRoles as $pRole) {
            $publicUser = User::where('role', $pRole)->first();
            if ($publicUser) {
                $this->actingAs($publicUser)->get('/inventori')->assertStatus(403);
                $this->actingAs($publicUser)->get('/inventori/pejabat')->assertStatus(403);
                $this->actingAs($publicUser)->get('/inventori/ubat')->assertStatus(403);

                $dashboard = $this->actingAs($publicUser)->get('/dashboard');
                $dashboard->assertStatus(200);
                $dashboard->assertDontSee('Pengurusan Pejabat');
                $dashboard->assertDontSee('Kenderaan Rasmi');
            }
        }
    }

    public function test_admin_epu_hanya_boleh_lihat_tempahan_dan_mohon_baru_disekat_fleet_dan_pemandu()
    {
        $adminEpu = User::where('role', 'admin_epu')->first();
        $this->assertNotNull($adminEpu, "User with role admin_epu should exist.");

        // Admin EPU disekat daripada modul kenderaan sepenuhnya
        $this->actingAs($adminEpu)->get('/kenderaan')->assertStatus(403);
        $this->actingAs($adminEpu)->get('/kenderaan/tempahan-baru')->assertStatus(403);
        $this->actingAs($adminEpu)->get('/kenderaan/fleet')->assertStatus(403);
        $this->actingAs($adminEpu)->get('/kenderaan/pemandu')->assertStatus(403);

        // Admin Kenderaan & Super Admin BOLEH akses semua modul fleet dan pemandu
        $adminKenderaan = User::where('role', 'admin_kenderaan')->first() ?: User::factory()->create(['role' => 'admin_kenderaan', 'email' => 'adminkenderaan_test2@veterinar.kelantan.gov.my']);
        $this->actingAs($adminKenderaan)->get('/kenderaan/fleet')->assertStatus(200);
        $this->actingAs($adminKenderaan)->get('/kenderaan/pemandu')->assertStatus(200);

        // Admin Pejabat DISEKAT daripada urusan fleet dan pemandu
        $adminPejabat = User::where('role', 'admin_pejabat')->first();
        $this->actingAs($adminPejabat)->get('/kenderaan/fleet')->assertStatus(403);
        $this->actingAs($adminPejabat)->get('/kenderaan/pemandu')->assertStatus(403);

        $superAdmin = User::where('role', 'super_admin')->first();
        $this->actingAs($superAdmin)->get('/kenderaan/fleet')->assertStatus(200);
        $this->actingAs($superAdmin)->get('/kenderaan/pemandu')->assertStatus(200);
    }

    public function test_admin_eptr_disekat_daripada_epu_dan_admin_epu_disekat_daripada_eptr()
    {
        $adminEptr = User::where('role', 'admin_eptr')->first();
        $adminEpu = User::where('role', 'admin_epu')->first();
        $superAdmin = User::where('role', 'super_admin')->first();
        $adminJajahan = User::where('role', 'admin_jajahan')->first();

        $this->assertNotNull($adminEptr);
        $this->assertNotNull($adminEpu);

        // 1. Admin EPTR:
        // - BOLEH akses EPTR (200 OK)
        $this->actingAs($adminEptr)->get('/eptr')->assertStatus(200);
        $this->actingAs($adminEptr)->get('/dashboard')
            ->assertStatus(200)
            ->assertSee(route('eptr.index'))
            ->assertDontSee(route('epu.index'))
            ->assertDontSee(route('epu.create'));

        // - DISEKAT daripada modul EPU Unggas (403 Forbidden)
        $this->actingAs($adminEptr)->get('/epu')->assertStatus(403);
        $this->actingAs($adminEptr)->get('/epu/daftar-borang-a')->assertStatus(403);
        $this->actingAs($adminEptr)->post('/epu/daftar-borang-a', [
            'nama_ladang' => 'Ladang Haram',
        ])->assertStatus(403);

        // 2. Admin EPU (Unggas):
        // - BOLEH akses EPU (200 OK)
        $this->actingAs($adminEpu)->get('/epu')->assertStatus(200);
        $this->actingAs($adminEpu)->get('/dashboard')
            ->assertStatus(200)
            ->assertSee(route('epu.index'))
            ->assertDontSee(route('eptr.index'))
            ->assertDontSee(route('eptr.create'));

        // - DISEKAT daripada modul EPTR Ruminan (403 Forbidden)
        $this->actingAs($adminEpu)->get('/eptr')->assertStatus(403);
        $this->actingAs($adminEpu)->get('/eptr/daftar-borang-a')->assertStatus(403);
        $this->actingAs($adminEpu)->get('/eptr/pembatalan-borang-c')->assertStatus(403);
        $this->actingAs($adminEpu)->get('/eptr/permit-sembelihan-borang-d')->assertStatus(403);
        $this->actingAs($adminEpu)->get('/eptr/daftar-anak')->assertStatus(403);
        $this->actingAs($adminEpu)->get('/eptr/program-kesihatan')->assertStatus(403);

        // 3. Admin Program (Pawah):
        // - BOLEH akses Pawah & EPTR
        $adminProgram = User::where('role', 'admin_program')->first();
        $this->assertNotNull($adminProgram);
        $this->actingAs($adminProgram)->get('/pawah')->assertStatus(200);
        $this->actingAs($adminProgram)->get('/dashboard')
            ->assertStatus(200)
            ->assertSee(route('pawah.index'))
            ->assertDontSee(route('epu.index'))
            ->assertDontSee(route('epu.create'));

        // - DISEKAT daripada modul EPU Unggas (403 Forbidden)
        $this->actingAs($adminProgram)->get('/epu')->assertStatus(403);
        $this->actingAs($adminProgram)->get('/epu/daftar-borang-a')->assertStatus(403);
        $this->actingAs($adminProgram)->post('/epu/daftar-borang-a', [
            'nama_ladang' => 'Ladang Haram Program',
        ])->assertStatus(403);

        // 4. Super Admin & Admin Jajahan BOLEH akses kedua-duanya
        $this->actingAs($superAdmin)->get('/eptr')->assertStatus(200);
        $this->actingAs($superAdmin)->get('/epu')->assertStatus(200);

        $this->actingAs($adminJajahan)->get('/eptr')->assertStatus(200);
        $this->actingAs($adminJajahan)->get('/epu')->assertStatus(200);
    }

    public function test_hanya_admin_kursus_dan_super_admin_boleh_terbit_kursus_baharu()
    {
        $adminKursus = User::where('role', 'admin_kursus')->first();
        $superAdmin = User::where('role', 'super_admin')->first();

        $this->assertNotNull($adminKursus);
        $this->assertNotNull($superAdmin);

        // 1. Admin Kursus DIBENARKAN terbit kursus baharu
        $getCreateKursus = $this->actingAs($adminKursus)->get('/kursus/cipta');
        $getCreateKursus->assertStatus(200);

        $getIndexKursus = $this->actingAs($adminKursus)->get('/kursus');
        $getIndexKursus->assertStatus(200);
        $getIndexKursus->assertSee('Terbitkan Kursus Baharu');

        $postStoreKursus = $this->actingAs($adminKursus)->post('/kursus/cipta', [
            'title' => 'Bengkel Pengurusan Penternakan Ruminan Moden 2026',
            'category' => 'Ruminan',
            'description' => 'Kursus intensif pemakanan silaj dan pengurusan baka lembu pedaging.',
            'trainer_name' => 'Dr. Mohd Shukri (Pegawai Veterinar)',
            'start_date' => date('Y-m-d', strtotime('+7 days')),
            'end_date' => date('Y-m-d', strtotime('+8 days')),
            'time' => '08:30 Pagi - 04:30 Petang',
            'location' => 'Dewan Latihan Pusat Veterinar Machang',
            'jajahan' => 'Machang',
            'capacity' => 35,
            'fee' => 0,
        ]);
        $postStoreKursus->assertRedirect(route('kursus.index'));
        $postStoreKursus->assertSessionHas('success');

        // 2. Super Admin juga DIBENARKAN
        $this->actingAs($superAdmin)->get('/kursus/cipta')->assertStatus(200);

        // 3. SEMUA pengguna lain DISEKAT daripada menerbitkan kursus baharu (403 Forbidden)
        $blockedRoles = [
            'admin_eptr',
            'admin_program',
            'admin_epu',
            'admin_pejabat',
            'admin_jajahan',
            'penternak',
            'usahawan',
            'orang_awam'
        ];

        foreach ($blockedRoles as $blockedRole) {
            $user = User::where('role', $blockedRole)->first();
            if ($user) {
                // Cuba buka borang cipta kursus -> 403 Forbidden
                $getAttempt = $this->actingAs($user)->get('/kursus/cipta');
                $getAttempt->assertStatus(403);

                // Cuba hantar data cipta kursus -> 403 Forbidden
                $postAttempt = $this->actingAs($user)->post('/kursus/cipta', [
                    'title' => 'Kursus Tidak Sah oleh ' . $blockedRole,
                    'category' => 'Ruminan',
                    'description' => 'Percubaan terbit kursus tanpa autoriti.',
                    'trainer_name' => 'Pesalah',
                    'start_date' => date('Y-m-d', strtotime('+5 days')),
                    'end_date' => date('Y-m-d', strtotime('+5 days')),
                    'time' => '09:00 - 17:00',
                    'location' => 'Dewan',
                    'jajahan' => 'Kota Bharu',
                    'capacity' => 20,
                    'fee' => 0,
                ]);
                $postAttempt->assertStatus(403);

                // Pada direktori kursus
                $getIndex = $this->actingAs($user)->get('/kursus');
                if (in_array($blockedRole, ['admin_program', 'admin_eptr'])) {
                    $getIndex->assertStatus(403);
                } else {
                    $getIndex->assertStatus(200);
                    $getIndex->assertDontSee('Terbitkan Kursus Baharu');
                }
            }
        }
    }

    public function test_admin_kursus_hanya_boleh_akses_modul_kursus_dan_disekat_semua_modul_lain()
    {
        $adminKursus = User::where('role', 'admin_kursus')->first();
        $this->assertNotNull($adminKursus);

        // 1. DIBENARKAN akses Modul Kursus Ternakan (200 OK)
        $this->actingAs($adminKursus)->get('/kursus')->assertStatus(200);
        $this->actingAs($adminKursus)->get('/kursus/cipta')->assertStatus(200);

        // 2. Dashboard khusus Admin Kursus memaparkan kursus dan TIDAK memaparkan modul lain
        $getDashboard = $this->actingAs($adminKursus)->get('/dashboard');
        $getDashboard->assertStatus(200);
        $getDashboard->assertSee('Senarai Kursus Ternakan Diterbitkan');
        $getDashboard->assertDontSee(route('eptr.index'));
        $getDashboard->assertDontSee(route('pawah.index'));
        $getDashboard->assertDontSee(route('epu.index'));
        $getDashboard->assertDontSee(route('klinik.index'));
        $getDashboard->assertDontSee(route('inventori.pejabat.index'));
        $getDashboard->assertDontSee(route('inventori.ubat.index'));
        $getDashboard->assertDontSee(route('kenderaan.index'));

        // 3. DISEKAT daripada Modul EPTR Ruminan (403 Forbidden)
        $this->actingAs($adminKursus)->get('/eptr')->assertStatus(403);
        $this->actingAs($adminKursus)->get('/eptr/daftar-borang-a')->assertStatus(403);
        $this->actingAs($adminKursus)->get('/eptr/pembatalan-borang-c')->assertStatus(403);
        $this->actingAs($adminKursus)->get('/eptr/permit-sembelihan-borang-d')->assertStatus(403);
        $this->actingAs($adminKursus)->get('/eptr/daftar-anak')->assertStatus(403);
        $this->actingAs($adminKursus)->get('/eptr/program-kesihatan')->assertStatus(403);

        // 4. DISEKAT daripada Modul Program Pawah (403 Forbidden)
        $this->actingAs($adminKursus)->get('/pawah')->assertStatus(403);
        $this->actingAs($adminKursus)->get('/pawah/perjanjian-baru')->assertStatus(403);

        // 5. DISEKAT daripada Modul EPU Unggas (403 Forbidden)
        $this->actingAs($adminKursus)->get('/epu')->assertStatus(403);
        $this->actingAs($adminKursus)->get('/epu/daftar-borang-a')->assertStatus(403);

        // 6. DISEKAT daripada Modul Klinik Haiwan (403 Forbidden)
        $this->actingAs($adminKursus)->get('/klinik')->assertStatus(403);
        $this->actingAs($adminKursus)->get('/klinik/temujanji-baru')->assertStatus(403);

        // 7. DISEKAT daripada Pengurusan Stor Pejabat & Stor Ubat (403 Forbidden)
        $this->actingAs($adminKursus)->get('/inventori/pejabat')->assertStatus(403);
        $this->actingAs($adminKursus)->get('/inventori/ubat')->assertStatus(403);

        // 8. DISEKAT daripada Modul Kenderaan Rasmi (403 Forbidden)
        $this->actingAs($adminKursus)->get('/kenderaan')->assertStatus(403);
        $this->actingAs($adminKursus)->get('/kenderaan/tempahan-baru')->assertStatus(403);
    }

    public function test_pengurusan_kursus_dan_kelulusan_pemohon_oleh_admin_kursus()
    {
        $adminKursus = User::where('role', 'admin_kursus')->first();
        $penternak = User::where('role', 'penternak')->first();
        $usahawan = User::where('role', 'usahawan')->first();
        $adminEptr = User::where('role', 'admin_eptr')->first();

        $this->assertNotNull($adminKursus);
        $this->assertNotNull($penternak);
        $this->assertNotNull($usahawan);
        $this->assertNotNull($adminEptr);

        // 1. Admin Kursus boleh akses Hab Pengurusan Pemohon
        $getHub = $this->actingAs($adminKursus)->get('/kursus/pengurusan-pemohon');
        $getHub->assertStatus(200);
        $getHub->assertSee('Hab Pengurusan Peserta Kursus Ternakan');
        $getHub->assertSee('Menunggu Kelulusan');

        // Pengguna lain disekat daripada Hab Pengurusan Pemohon (403 Forbidden)
        $this->actingAs($penternak)->get('/kursus/pengurusan-pemohon')->assertStatus(403);
        $this->actingAs($adminEptr)->get('/kursus/pengurusan-pemohon')->assertStatus(403);

        // 2. Admin Kursus menerbitkan kursus baru
        $postCourse = $this->actingAs($adminKursus)->post('/kursus/cipta', [
            'title' => 'Bengkel Biosekuriti & Silaj Jagung Fodder 2026',
            'category' => 'Pemakanan Ternakan',
            'description' => 'Bengkel praktikal formulasi dedak dan silaj berkualiti tinggi.',
            'trainer_name' => 'Dr. Wan Ahmad Fadzil',
            'start_date' => date('Y-m-d', strtotime('+10 days')),
            'end_date' => date('Y-m-d', strtotime('+12 days')),
            'time' => '08:30 Pagi - 04:30 Petang',
            'location' => 'Pusat Inovasi Makanan Ternakan JPVNK',
            'jajahan' => 'Pasir Puteh',
            'capacity' => 30,
            'fee' => 0,
        ]);
        $postCourse->assertRedirect();
        
        $course = \App\Models\Course::where('title', 'Bengkel Biosekuriti & Silaj Jagung Fodder 2026')->first();
        $this->assertNotNull($course);

        // 3. Admin Kursus boleh kemaskini maklumat kursus (Edit & Update)
        $getEdit = $this->actingAs($adminKursus)->get("/kursus/{$course->id}/edit");
        $getEdit->assertStatus(200);
        $getEdit->assertSee('Kemaskini Kursus');

        $putUpdate = $this->actingAs($adminKursus)->put("/kursus/{$course->id}", [
            'title' => 'Bengkel Biosekuriti & Silaj Jagung Fodder 2026 (Edisi Khas)',
            'category' => 'Pemakanan Ternakan',
            'description' => 'Bengkel praktikal formulasi dedak dan silaj berkualiti tinggi (Kemaskini).',
            'trainer_name' => 'Dr. Wan Ahmad Fadzil & Pakar Pemakanan',
            'start_date' => date('Y-m-d', strtotime('+10 days')),
            'end_date' => date('Y-m-d', strtotime('+12 days')),
            'time' => '08:30 Pagi - 05:00 Petang',
            'location' => 'Pusat Inovasi Makanan Ternakan JPVNK, Cherang Ruku',
            'jajahan' => 'Pasir Puteh',
            'capacity' => 35,
            'fee' => 0,
            'status' => 'Buka',
        ]);
        $putUpdate->assertRedirect(route('kursus.show', $course->id));
        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'capacity' => 35,
            'location' => 'Pusat Inovasi Makanan Ternakan JPVNK, Cherang Ruku',
        ]);

        // 4. Penternak mendaftar kursus -> Status Permohonan: Menunggu
        $postApply = $this->actingAs($penternak)->post("/kursus/{$course->id}/daftar");
        $postApply->assertRedirect();
        $postApply->assertSessionHas('success');

        $applicationPenternak = \App\Models\CourseApplication::where('course_id', $course->id)
            ->where('user_id', $penternak->id)
            ->first();
        $this->assertNotNull($applicationPenternak);
        $this->assertEquals('Menunggu', $applicationPenternak->status);

        // Usahawan juga mendaftar kursus
        $this->actingAs($usahawan)->post("/kursus/{$course->id}/daftar");
        $applicationUsahawan = \App\Models\CourseApplication::where('course_id', $course->id)
            ->where('user_id', $usahawan->id)
            ->first();
        $this->assertNotNull($applicationUsahawan);
        $this->assertEquals('Menunggu', $applicationUsahawan->status);

        // 5. Admin Kursus MELULUSKAN permohonan Penternak
        $postApprove = $this->actingAs($adminKursus)->post("/kursus/pemohon/{$applicationPenternak->id}/lulus");
        $postApprove->assertRedirect();
        $postApprove->assertSessionHas('success');

        $applicationPenternak->refresh();
        $this->assertEquals('Disahkan', $applicationPenternak->status);

        // 6. Admin Kursus MENOLAK permohonan Usahawan dengan sebab
        $postReject = $this->actingAs($adminKursus)->post("/kursus/pemohon/{$applicationUsahawan->id}/tolak", [
            'rejection_reason' => 'Kapasiti kursus telah penuh untuk sesi ini.',
        ]);
        $postReject->assertRedirect();
        $postReject->assertSessionHas('success');

        $applicationUsahawan->refresh();
        $this->assertEquals('Ditolak', $applicationUsahawan->status);
        $this->assertEquals('Kapasiti kursus telah penuh untuk sesi ini.', $applicationUsahawan->rejection_reason);

        // 7. Admin Kursus MENGESAHKAN KEHADIRAN & MENJANA SIJIL DIGITAL
        $postAttendance = $this->actingAs($adminKursus)->post("/kursus/pemohon/{$applicationPenternak->id}/hadir");
        $postAttendance->assertRedirect();
        $postAttendance->assertSessionHas('success');

        $applicationPenternak->refresh();
        $this->assertEquals('Hadir', $applicationPenternak->status);
        $this->assertNotNull($applicationPenternak->certificate_number);
        $this->assertStringStartsWith('SIJIL-JPVNK-', $applicationPenternak->certificate_number);
        $this->assertNotNull($applicationPenternak->certificate_issued_at);

        // 8. Daftarkan rekod ternakan EPTR untuk penternak bagi menguji paparan maklumat ternakan pemohon
        $pemunyaPenternak = \App\Models\Pemunya::where('no_kp', $penternak->ic_number)->first() ?: \App\Models\Pemunya::create([
            'user_id' => $penternak->id,
            'nama' => $penternak->name,
            'no_kp' => $penternak->ic_number,
            'no_telefon' => $penternak->phone,
            'alamat' => 'Kampung Padang Pak Amat',
            'jajahan' => 'Pasir Puteh',
        ]);

        \App\Models\Ternakan::create([
            'pemunya_id' => $pemunyaPenternak->id,
            'no_tag' => 'TAG-TEST-KURSUS-01',
            'jenis_ternakan' => 'Lembu',
            'baka' => 'Brahman Cross Sado',
            'jantina' => 'Betina',
            'umur' => '2 Tahun 6 Bulan',
            'tujuan_ternakan' => 'Pembiakan',
            'jajahan' => 'Pasir Puteh',
            'status' => 'Aktif',
            'status_kelulusan' => 'Diluluskan',
        ]);

        // Admin Kursus BOLEH melihat maklumat penuh pemohon & rekod ternakan di pemohon.show
        $getApplicantShow = $this->actingAs($adminKursus)->get("/kursus/pemohon/{$applicationPenternak->id}");
        $getApplicantShow->assertStatus(200);
        $getApplicantShow->assertSee('Profil Pemohon &amp; Maklumat Ternakan Berdaftar', false);
        $getApplicantShow->assertSee('Maklumat Ternakan &amp; Premis Ladang Pemohon', false);
        $getApplicantShow->assertSee('TAG-TEST-KURSUS-01');
        $getApplicantShow->assertSee('Brahman Cross Sado');
        $getApplicantShow->assertSee($penternak->name);
        $getApplicantShow->assertSee($penternak->ic_number);
        $getApplicantShow->assertSee($penternak->phone);
        $getApplicantShow->assertSee($applicationPenternak->registration_number);
        $getApplicantShow->assertSee('Status Sijil Penyertaan Digital');

        // Pengguna lain (bukan admin kursus / super admin) disekat daripada melihat profil pemohon (403 Forbidden)
        $this->actingAs($adminEptr)->get("/kursus/pemohon/{$applicationPenternak->id}")->assertStatus(403);
        $this->actingAs($usahawan)->get("/kursus/pemohon/{$applicationPenternak->id}")->assertStatus(403);

        // 9. Sijil boleh dicetak secara digital (PDF View)
        $getCert = $this->actingAs($penternak)->get("/kursus/sijil/{$applicationPenternak->id}");
        $getCert->assertStatus(200);
        $getCert->assertSee('SIJIL PENYERTAAN');
        $getCert->assertSee($applicationPenternak->certificate_number);
        $getCert->assertSee($penternak->name);

        // 10. Ujian Perlindungan Rekod & Sijil: Padam Kursus yang mempunyai peserta -> Diarkibkan (Bukan Hard Delete)
        $deleteAttempt = $this->actingAs($adminKursus)->delete("/kursus/{$course->id}");
        $deleteAttempt->assertRedirect(route('kursus.index'));
        $deleteAttempt->assertSessionHas('success');

        // Pastikan kursus MASIH WUJUD dalam pangkalan data dan status bertukar ke 'Diarkibkan'
        $course->refresh();
        $this->assertEquals('Diarkibkan', $course->status);
        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'status' => 'Diarkibkan',
        ]);

        // Pastikan rekod permohonan dan nombor sijil pemohon TIDAK HILANG
        $this->assertDatabaseHas('course_applications', [
            'id' => $applicationPenternak->id,
            'course_id' => $course->id,
            'certificate_number' => $applicationPenternak->certificate_number,
            'status' => 'Hadir',
        ]);

        // Pemohon masih boleh mencetak dan memaparkan sijil digital kursus yang telah diarkibkan
        $getCertAfterArchive = $this->actingAs($penternak)->get("/kursus/sijil/{$applicationPenternak->id}");
        $getCertAfterArchive->assertStatus(200);
        $getCertAfterArchive->assertSee($applicationPenternak->certificate_number);

        // 11. Ujian Arkib Manual & Pemadaman Kursus Tanpa Peserta
        $emptyCourse = \App\Models\Course::create([
            'title' => 'Kursus Ujian Kosong Tanpa Peserta',
            'code' => 'KRS-TEST-EMPTY-01',
            'category' => 'Ruminan',
            'description' => 'Kursus ujian untuk dipadam kerana tiada pendaftar.',
            'trainer_name' => 'Penceramah Jemputan',
            'start_date' => date('Y-m-d', strtotime('+30 days')),
            'end_date' => date('Y-m-d', strtotime('+31 days')),
            'time' => '08:30 Pagi - 04:30 Petang',
            'location' => 'Dewan Serbaguna JPVNK',
            'jajahan' => 'Kota Bharu',
            'capacity' => 20,
            'status' => 'Buka',
            'created_by' => $adminKursus->id,
        ]);

        // Kursus tanpa peserta boleh dipadam terus
        $deleteEmpty = $this->actingAs($adminKursus)->delete("/kursus/{$emptyCourse->id}");
        $deleteEmpty->assertRedirect(route('kursus.index'));
        $this->assertDatabaseMissing('courses', ['id' => $emptyCourse->id]);
    }

    public function test_inventory_store_segregation_and_role_access_isolation()
    {
        $adminPejabat = User::where('role', 'admin_pejabat')->first();
        $adminUbat = User::where('role', 'admin_ubat')->first();
        $superAdmin = User::where('role', 'super_admin')->first();
        $adminKursus = User::where('role', 'admin_kursus')->first();
        $adminEptr = User::where('role', 'admin_eptr')->first();
        $penternak = User::where('role', 'penternak')->first();
        $orangAwam = User::where('role', 'orang_awam')->first();

        // 1. Admin Pejabat: Boleh akses Stor Pejabat, DISEKAT daripada Stor Ubat (403)
        $respPejabat1 = $this->actingAs($adminPejabat)->get('/inventori/pejabat');
        $respPejabat1->assertStatus(200);
        $respPejabat1->assertSee('Stor Peralatan &amp; Aset Pejabat', false);

        $respPejabatBlockUbat = $this->actingAs($adminPejabat)->get('/inventori/ubat');
        $respPejabatBlockUbat->assertStatus(403);

        // 2. Admin Stor Ubat: Boleh akses Stor Ubat, DISEKAT daripada Stor Pejabat & modul lain (403)
        $respUbat1 = $this->actingAs($adminUbat)->get('/inventori/ubat');
        $respUbat1->assertStatus(200);
        $respUbat1->assertSee('Stor Ubat &amp; Vaksin Veterinar', false);

        $respUbatBlockPejabat = $this->actingAs($adminUbat)->get('/inventori/pejabat');
        $respUbatBlockPejabat->assertStatus(403);

        $respUbatBlockKenderaan = $this->actingAs($adminUbat)->get('/kenderaan');
        $respUbatBlockKenderaan->assertStatus(403);

        $respUbatBlockEptr = $this->actingAs($adminUbat)->get('/eptr');
        $respUbatBlockEptr->assertStatus(403);

        // 3. Super Admin: Boleh akses kedua-dua stor
        $respSuperPejabat = $this->actingAs($superAdmin)->get('/inventori/pejabat');
        $respSuperPejabat->assertStatus(200);

        $respSuperUbat = $this->actingAs($superAdmin)->get('/inventori/ubat');
        $respSuperUbat->assertStatus(200);

        // 4. Peranan lain disekat daripada kedua-dua stor
        $this->actingAs($adminKursus)->get('/inventori/pejabat')->assertStatus(403);
        $this->actingAs($adminKursus)->get('/inventori/ubat')->assertStatus(403);
        $this->actingAs($adminEptr)->get('/inventori/pejabat')->assertStatus(403);
        $this->actingAs($adminEptr)->get('/inventori/ubat')->assertStatus(403);
        $this->actingAs($penternak)->get('/inventori/pejabat')->assertStatus(403);
        $this->actingAs($penternak)->get('/inventori/ubat')->assertStatus(403);
        $this->actingAs($orangAwam)->get('/inventori/pejabat')->assertStatus(403);
        $this->actingAs($orangAwam)->get('/inventori/ubat')->assertStatus(403);
    }

    public function test_register_office_item_and_veterinary_medicine_item()
    {
        $adminPejabat = User::where('role', 'admin_pejabat')->first();
        $adminUbat = User::where('role', 'admin_ubat')->first();

        // 1. Admin Pejabat daftar barangan Stor Pejabat
        $respDaftarPejabat = $this->actingAs($adminPejabat)->post('/inventori/pejabat/tambah', [
            'kod_item' => 'PJB-TEST-001',
            'nama_item' => 'Penebuk Lubang Kertas Heavy Duty',
            'kategori' => 'Alat Tulis & Pejabat',
            'unit' => 'Unit',
            'kuantiti_semasa' => 15,
            'kuantiti_minimum' => 3,
            'harga_seunit' => 45.00,
            'pembekal_utama' => 'Pembekal Alat Tulis KB',
            'lokasi_rak' => 'Rak A1',
            'deskripsi' => 'Penebuk kertas 2 lubang tebal',
        ]);
        $respDaftarPejabat->assertRedirect(route('inventori.pejabat.index'));

        $this->assertDatabaseHas('inventori_items', [
            'kod_item' => 'PJB-TEST-001',
            'jenis_stor' => 'pejabat',
            'nama_item' => 'Penebuk Lubang Kertas Heavy Duty',
            'kuantiti_semasa' => 15,
        ]);

        // 2. Admin Ubat daftar ubat/vaksin Stor Ubat (dengan batch & suhu)
        $respDaftarUbat = $this->actingAs($adminUbat)->post('/inventori/ubat/tambah', [
            'kod_item' => 'UBT-TEST-002',
            'nama_item' => 'Vaksin Anthrax Spore Live 50 Doses',
            'kategori' => 'Vaksin',
            'unit' => 'Botol',
            'kuantiti_semasa' => 20,
            'kuantiti_minimum' => 5,
            'harga_seunit' => 210.00,
            'no_batch' => 'ANT-2026-X9',
            'tarikh_luput' => date('Y-m-d', strtotime('+1 year')),
            'suhu_simpanan' => 'Rangkaian Sejuk / Chiller (2°C - 8°C)',
            'pembekal_utama' => 'Pharmaniaga Animal Health',
            'lokasi_rak' => 'Chiller 2',
            'deskripsi' => 'Vaksin spora anthrax ternakan ruminan',
        ]);
        $respDaftarUbat->assertRedirect(route('inventori.ubat.index'));

        $this->assertDatabaseHas('inventori_items', [
            'kod_item' => 'UBT-TEST-002',
            'jenis_stor' => 'ubat',
            'no_batch' => 'ANT-2026-X9',
            'kuantiti_semasa' => 20,
        ]);
    }

    public function test_inventory_stock_in_and_stock_out_movement_flow()
    {
        $adminUbat = User::where('role', 'admin_ubat')->first();
        $item = InventoriItem::where('jenis_stor', 'ubat')->where('kod_item', 'UBT-VET-001')->first();
        $initialQty = $item->kuantiti_semasa;

        // 1. Rekod Stok Masuk (+10)
        $respMasuk = $this->actingAs($adminUbat)->post("/inventori/item/{$item->id}/transaksi", [
            'jenis_transaksi' => 'Stok Masuk',
            'kuantiti' => 10,
            'penerima_atau_pembekal' => 'Pembekal Pharmaniaga',
            'rujukan_dokumen' => 'DO-TEST-1234',
            'catatan' => 'Penerimaan stok tambahan',
        ]);
        $respMasuk->assertRedirect(route('inventori.show', $item->id));

        $item->refresh();
        $this->assertEquals($initialQty + 10, $item->kuantiti_semasa);

        // 2. Rekod Stok Keluar (-5)
        $respKeluar = $this->actingAs($adminUbat)->post("/inventori/item/{$item->id}/transaksi", [
            'jenis_transaksi' => 'Stok Keluar',
            'kuantiti' => 5,
            'penerima_atau_pembekal' => 'Klinik Haiwan Pasir Mas',
            'rujukan_dokumen' => 'AGIHAN-TEST-567',
            'catatan' => 'Agihan rawatan ternakan',
        ]);
        $respKeluar->assertRedirect(route('inventori.show', $item->id));

        $item->refresh();
        $this->assertEquals($initialQty + 10 - 5, $item->kuantiti_semasa);

        // 3. Stok Keluar melebihi baki -> Dihalang dengan ralat
        $respFail = $this->actingAs($adminUbat)->post("/inventori/item/{$item->id}/transaksi", [
            'jenis_transaksi' => 'Stok Keluar',
            'kuantiti' => 99999,
            'penerima_atau_pembekal' => 'Klinik Haiwan',
            'catatan' => 'Cubaan melebihi stok',
        ]);
        $respFail->assertSessionHas('error');

        $item->refresh();
        $this->assertEquals($initialQty + 10 - 5, $item->kuantiti_semasa);
    }

    public function test_equipment_loan_and_return_flow()
    {
        $adminPejabat = User::where('role', 'admin_pejabat')->first();
        $adminEptr = User::where('role', 'admin_eptr')->first();
        $item = InventoriItem::where('jenis_stor', 'pejabat')->where('kod_item', 'PJB-AST-004')->first();
        $initialQty = $item->kuantiti_semasa;

        // 1. Rekod Pinjaman Peralatan (Kuantiti: 1)
        $respPinjam = $this->actingAs($adminPejabat)->post("/inventori/item/{$item->id}/pinjaman", [
            'user_id' => $adminEptr->id,
            'kuantiti' => 1,
            'tujuan_pinjaman' => 'Taklimat EPTR di Pasir Puteh',
            'tarikh_pinjam' => date('Y-m-d'),
            'tarikh_jangka_pulang' => date('Y-m-d', strtotime('+2 days')),
        ]);
        $respPinjam->assertRedirect(route('inventori.show', $item->id));

        $item->refresh();
        $this->assertEquals($initialQty - 1, $item->kuantiti_semasa);

        $pinjaman = InventoriPinjaman::where('inventori_item_id', $item->id)->where('status', 'Dipinjam')->first();
        $this->assertNotNull($pinjaman);

        // 2. Sahkan Pemulangan Pinjaman
        $respPulang = $this->actingAs($adminPejabat)->post("/inventori/pinjaman/{$pinjaman->id}/pulang", [
            'keadaan_semasa_pulang' => 'Sangat Baik / Lengkap',
            'catatan' => 'Telah dipulangkan selepas program selesai',
        ]);
        $respPulang->assertSessionHas('success');

        $item->refresh();
        $this->assertEquals($initialQty, $item->kuantiti_semasa);

        $pinjaman->refresh();
        $this->assertEquals('Telah Dipulangkan', $pinjaman->status);
    }

    public function test_staff_inventory_requisition_submission_and_role_access()
    {
        $staf = User::where('role', 'staf')->first();
        $adminEptr = User::where('role', 'admin_eptr')->first();
        $adminJajahan = User::where('role', 'admin_jajahan')->first();
        $penternak = User::where('role', 'penternak')->first();
        $orangAwam = User::where('role', 'orang_awam')->first();

        $pejabatItem = InventoriItem::where('jenis_stor', 'pejabat')->first();
        $ubatItem = InventoriItem::where('jenis_stor', 'ubat')->first();

        // 1. Staf jabatan hantar permohonan alatan pejabat
        $respMohonPejabat = $this->actingAs($staf)->post('/inventori/permohonan/pejabat/mohon', [
            'inventori_item_id' => $pejabatItem->id,
            'kuantiti_dimohon' => 3,
            'unit_bahagian' => 'Bahagian Khidmat Pengurusan',
            'tujuan_permohonan' => 'Ujian permohonan alatan pejabat staf',
            'tarikh_diperlukan' => date('Y-m-d'),
            'catatan_pemohon' => 'Ujian pemohon',
        ]);
        $respMohonPejabat->assertRedirect(route('inventori.permohonan.saya'));

        $this->assertDatabaseHas('inventori_permohonan', [
            'user_id' => $staf->id,
            'inventori_item_id' => $pejabatItem->id,
            'jenis_stor' => 'pejabat',
            'kuantiti_dimohon' => 3,
            'status' => 'Menunggu Kelulusan',
        ]);

        // 2. Admin Jajahan hantar permohonan ubat/vaksin (200 OK & Redirect)
        $respGetMohonUbatJajahan = $this->actingAs($adminJajahan)->get('/inventori/permohonan/ubat/mohon');
        $respGetMohonUbatJajahan->assertStatus(200);

        $respMohonUbat = $this->actingAs($adminJajahan)->post('/inventori/permohonan/ubat/mohon', [
            'inventori_item_id' => $ubatItem->id,
            'kuantiti_dimohon' => 2,
            'unit_bahagian' => 'Klinik Haiwan Machang',
            'tujuan_permohonan' => 'Ujian permohonan ubat veterinar klinik',
            'tarikh_diperlukan' => date('Y-m-d'),
        ]);
        $respMohonUbat->assertRedirect(route('inventori.permohonan.saya'));

        $this->assertDatabaseHas('inventori_permohonan', [
            'user_id' => $adminJajahan->id,
            'inventori_item_id' => $ubatItem->id,
            'jenis_stor' => 'ubat',
            'kuantiti_dimohon' => 2,
            'status' => 'Menunggu Kelulusan',
        ]);

        // 3. Staf khusus (cth: admin_eptr) DISEKAT daripada inventori & permohonan ubat/alatan pejabat (403 Forbidden)
        $respEptrInventori = $this->actingAs($adminEptr)->get('/inventori');
        $respEptrInventori->assertStatus(403);

        $respEptrMohonUbat = $this->actingAs($adminEptr)->get('/inventori/permohonan/ubat/mohon');
        $respEptrMohonUbat->assertStatus(403);

        $respEptrPostUbat = $this->actingAs($adminEptr)->post('/inventori/permohonan/ubat/mohon', [
            'inventori_item_id' => $ubatItem->id,
            'kuantiti_dimohon' => 2,
            'unit_bahagian' => 'Unit EPTR',
            'tujuan_permohonan' => 'Cubaan mohon ubat oleh admin eptr',
        ]);
        $respEptrPostUbat->assertStatus(403);

        // 4. Staf lihat senarai permohonan sendiri (200 OK)
        $respSaya = $this->actingAs($staf)->get('/inventori/permohonan/saya');
        $respSaya->assertStatus(200);
        $respSaya->assertSee('Senarai Permohonan Alatan Pejabat &amp; Ubat Saya', false);

        // 5. Orang Awam / Penternak disekat daripada memohon bekalan stor dalaman (403)
        $respPublicMohon = $this->actingAs($orangAwam)->get('/inventori/permohonan/pejabat/mohon');
        $respPublicMohon->assertStatus(403);

        $respFarmerMohon = $this->actingAs($penternak)->post('/inventori/permohonan/ubat/mohon', [
            'inventori_item_id' => $ubatItem->id,
            'kuantiti_dimohon' => 10,
            'unit_bahagian' => 'Penternak Luar',
            'tujuan_permohonan' => 'Cubaan mohon tidak sah',
        ]);
        $respFarmerMohon->assertStatus(403);
    }

    public function test_admin_requisition_approval_and_automatic_stock_deduction_flow()
    {
        $adminPejabat = User::where('role', 'admin_pejabat')->first();
        $adminUbat = User::where('role', 'admin_ubat')->first();
        $adminKursus = User::where('role', 'admin_kursus')->first();

        $pejabatItem = InventoriItem::where('jenis_stor', 'pejabat')->first();
        $initialStock = $pejabatItem->kuantiti_semasa;

        // Cipta permohonan alatan pejabat
        $permohonan = \App\Models\InventoriPermohonan::create([
            'no_permohonan' => 'REQ-TEST-PJB-999',
            'user_id' => $adminKursus->id,
            'inventori_item_id' => $pejabatItem->id,
            'jenis_stor' => 'pejabat',
            'kuantiti_dimohon' => 4,
            'unit_bahagian' => 'Unit Kursus',
            'tujuan_permohonan' => 'Penyediaan fail kursus ternakan',
            'status' => 'Menunggu Kelulusan',
        ]);

        // 1. Admin Stor Ubat cuba luluskan permohonan stor pejabat -> 403 Forbidden
        $respUbatCubaLulus = $this->actingAs($adminUbat)->post("/inventori/permohonan/{$permohonan->id}/status", [
            'tindakan' => 'lulus',
            'kuantiti_diluluskan' => 4,
        ]);
        $respUbatCubaLulus->assertStatus(403);

        // 2. Admin Pejabat luluskan permohonan stor pejabat
        $respLulus = $this->actingAs($adminPejabat)->post("/inventori/permohonan/{$permohonan->id}/status", [
            'tindakan' => 'lulus',
            'kuantiti_diluluskan' => 4,
            'catatan_pegawai' => 'Diluluskan penuh untuk kursus.',
        ]);
        $respLulus->assertSessionHas('success');

        $permohonan->refresh();
        $this->assertEquals('Diluluskan', $permohonan->status);
        $this->assertEquals(4, $permohonan->kuantiti_diluluskan);

        // 3. Admin Pejabat serahkan stok -> Baki inventori ditolak automatik & rekod Stok Keluar dicipta
        $respSerah = $this->actingAs($adminPejabat)->post("/inventori/permohonan/{$permohonan->id}/status", [
            'tindakan' => 'serah',
            'kuantiti_diluluskan' => 4,
            'catatan_pegawai' => 'Stok telah diserahkan di kaunter stor.',
        ]);
        $respSerah->assertSessionHas('success');

        $permohonan->refresh();
        $this->assertEquals('Telah Diambil / Diserahkan', $permohonan->status);

        $pejabatItem->refresh();
        $this->assertEquals($initialStock - 4, $pejabatItem->kuantiti_semasa);

        $this->assertDatabaseHas('inventori_transaksi', [
            'inventori_item_id' => $pejabatItem->id,
            'jenis_transaksi' => 'Stok Keluar',
            'kuantiti' => 4,
            'rujukan_dokumen' => 'REQ-TEST-PJB-999',
            'baki_selepas' => $initialStock - 4,
        ]);
    }

    public function test_general_staff_user_capabilities_and_restrictions()
    {
        $staf = User::where('role', 'staf')->first();
        $this->assertNotNull($staf);
        $this->assertTrue($staf->isStaff());
        $this->assertTrue($staf->canRequestAlatanPejabat());
        $this->assertFalse($staf->canRequestUbat());
        $this->assertFalse($staf->canPublishCourse());

        $course = \App\Models\Course::where('status', 'Buka')->first();
        $pejabatItem = InventoriItem::where('jenis_stor', 'pejabat')->first();
        $ubatItem = InventoriItem::where('jenis_stor', 'ubat')->first();

        // 1. Staf biasa boleh melihat direktori kursus dan mendaftar kursus (200 OK & Redirect)
        $respKursus = $this->actingAs($staf)->get('/kursus');
        $respKursus->assertStatus(200);

        $respDaftarKursus = $this->actingAs($staf)->post("/kursus/{$course->id}/daftar");
        $respDaftarKursus->assertRedirect();
        $this->assertDatabaseHas('course_applications', [
            'course_id' => $course->id,
            'user_id' => $staf->id,
        ]);

        // 2. Staf biasa DISEKAT daripada menerbitkan kursus (403 Forbidden)
        $this->actingAs($staf)->get('/kursus/cipta')->assertStatus(403);
        $this->actingAs($staf)->post('/kursus/cipta', [
            'title' => 'Kursus Tidak Sah Oleh Staf Biasa',
        ])->assertStatus(403);

        // 3. Staf biasa boleh memohon alatan pejabat di stor (200 OK & Redirect)
        $respMohonPejabat = $this->actingAs($staf)->post('/inventori/permohonan/pejabat/mohon', [
            'inventori_item_id' => $pejabatItem->id,
            'kuantiti_dimohon' => 1,
            'unit_bahagian' => 'Unit Pentadbiran',
            'tujuan_permohonan' => 'Ujian permohonan staf biasa',
            'tarikh_diperlukan' => date('Y-m-d'),
        ]);
        $respMohonPejabat->assertRedirect(route('inventori.permohonan.saya'));

        // 4. Staf biasa DISEKAT daripada memohon ubat/vaksin veterinar (403 Forbidden)
        $this->actingAs($staf)->get('/inventori/permohonan/ubat/mohon')->assertStatus(403);
        $this->actingAs($staf)->post('/inventori/permohonan/ubat/mohon', [
            'inventori_item_id' => $ubatItem->id,
            'kuantiti_dimohon' => 5,
            'unit_bahagian' => 'Unit Pentadbiran',
            'tujuan_permohonan' => 'Cubaan mohon ubat oleh staf biasa',
        ])->assertStatus(403);

        // 5. Staf biasa DISEKAT daripada pengurusan stor (403 Forbidden)
        $this->actingAs($staf)->get('/inventori/pejabat')->assertStatus(403);
        $this->actingAs($staf)->get('/inventori/ubat')->assertStatus(403);

        // 6. Staf biasa DISEKAT daripada modul admin EPTR, EPU & Pawah (403 Forbidden)
        $this->actingAs($staf)->get('/eptr')->assertStatus(403);
        $this->actingAs($staf)->get('/epu')->assertStatus(403);
        $this->actingAs($staf)->get('/pawah')->assertStatus(403);

        // 7. Semakan Papan Pemuka Khas Staf Biasa
        $dashboardResp = $this->actingAs($staf)->get('/dashboard');
        $dashboardResp->assertStatus(200);

        // Pastikan Taburan Ternakan EPTR/Pawah, Pecahan Modul dan Surat Perjanjian Pawah DIBUANG
        $dashboardResp->assertDontSee('Taburan Ternakan EPTR &amp; Program Pawah Mengikut Jajahan', false);
        $dashboardResp->assertDontSee('Pecahan Modul Perkhidmatan');
        $dashboardResp->assertDontSee('Surat Perjanjian Lembu Pawah Terkini');

        // Pastikan Maklumat Stor & Permohonan Staf DIPAPARKAN
        $dashboardResp->assertSee('Permohonan Alatan Pejabat Saya');
        $dashboardResp->assertSee('Barangan Stor Pejabat Sedia Ada');
        $dashboardResp->assertSee('Kursus Ternakan Terbuka Terkini');
        $dashboardResp->assertSee('Mohon Alatan Pejabat');
        $dashboardResp->assertSee('Permohonan Stor Saya');
    }

    public function test_admin_klinik_and_orang_awam_boleh_buat_tempahan_klinik_and_role_isolation()
    {
        $adminKlinik = User::where('role', 'admin_klinik')->first();
        $orangAwam = User::where('role', 'orang_awam')->first();
        $penternak = User::where('role', 'penternak')->first();

        $this->assertNotNull($adminKlinik);
        $this->assertNotNull($orangAwam);
        $this->assertTrue($adminKlinik->isStaff());
        $this->assertTrue($adminKlinik->isAdminKlinik());
        $this->assertTrue($adminKlinik->canAccessKlinik());
        $this->assertTrue($orangAwam->canAccessKlinik());

        // 1. Orang Awam membuat tempahan temujanji klinik haiwan
        $respPublicGet = $this->actingAs($orangAwam)->get('/klinik/temujanji-baru');
        $respPublicGet->assertStatus(200);

        $respPublicStore = $this->actingAs($orangAwam)->post('/klinik/temujanji-baru', [
            'jenis_haiwan' => 'Kucing',
            'nama_haiwan' => 'Mimi Awam',
            'baka' => 'Persian Mix',
            'jantina_haiwan' => 'Betina',
            'umur_haiwan' => '2 Tahun',
            'simptom_atau_tujuan' => 'Suntikan vaksin tahunan dan deworming.',
            'tarikh_temujanji' => date('Y-m-d'),
            'sesi' => 'Pagi (8:30 AM - 12:30 PM)',
            'klinik_jajahan' => 'Klinik Haiwan Ibu Pejabat JPVNK Kota Bharu',
        ]);
        $respPublicStore->assertRedirect();
        $this->assertDatabaseHas('klinik_temujanji', [
            'user_id' => $orangAwam->id,
            'nama_haiwan' => 'Mimi Awam',
            'jenis_haiwan' => 'Kucing',
            'status' => 'Disahkan',
        ]);

        // 2. Admin Klinik membuat tempahan bagi pihak pelanggan / penternak
        $respAdminGet = $this->actingAs($adminKlinik)->get('/klinik/temujanji-baru');
        $respAdminGet->assertStatus(200);
        $respAdminGet->assertSee('Daftar Bagi Pihak Pemilik / Orang Awam (Pilihan Admin Klinik)');

        $respAdminStore = $this->actingAs($adminKlinik)->post('/klinik/temujanji-baru', [
            'user_id' => $penternak->id, // Tempahan bagi pihak penternak
            'jenis_haiwan' => 'Kambing',
            'nama_haiwan' => 'Kambing Boer 01',
            'baka' => 'Boer',
            'jantina_haiwan' => 'Jantan',
            'umur_haiwan' => '8 Bulan',
            'simptom_atau_tujuan' => 'Rawatan batuk dan jangkitan pernafasan ternakan.',
            'tarikh_temujanji' => date('Y-m-d'),
            'sesi' => 'Petang (2:00 PM - 4:30 PM)',
            'klinik_jajahan' => 'Pusat Veterinar Jajahan Pasir Mas',
        ]);
        $respAdminStore->assertRedirect();

        $appointmentPenternak = \App\Models\KlinikTemujanji::where('nama_haiwan', 'Kambing Boer 01')->first();
        $this->assertNotNull($appointmentPenternak);
        $this->assertEquals($penternak->id, $appointmentPenternak->user_id);

        // 3. Admin Klinik melihat senarai temujanji dan merekodkan rawatan pesakit
        $respAdminIndex = $this->actingAs($adminKlinik)->get('/klinik');
        $respAdminIndex->assertStatus(200);
        $respAdminIndex->assertSee('Mimi Awam');
        $respAdminIndex->assertSee('Kambing Boer 01');

        $respRawatan = $this->actingAs($adminKlinik)->post("/klinik/temujanji/{$appointmentPenternak->id}/rekod-rawatan", [
            'berat_badan_kg' => 25.5,
            'suhu_celsius' => 39.2,
            'diagnosis' => 'Pneumonia peringkat awal pada kambing muda.',
            'rawatan_diberikan' => 'Suntikan antibiotik Oxytetracycline LA dan vitamin B-Complex.',
            'ubat_diberikan' => 'Oxytetracycline LA 200mg',
            'kos_rawatan' => 35.00,
            'nasihat_veterinar' => 'Pastikan kandang bersih dan peredaran udara baik.',
        ]);
        $respRawatan->assertRedirect();

        $appointmentPenternak->refresh();
        $this->assertEquals('Selesai', $appointmentPenternak->status);
        $this->assertDatabaseHas('klinik_rawatan', [
            'klinik_temujanji_id' => $appointmentPenternak->id,
            'diagnosis' => 'Pneumonia peringkat awal pada kambing muda.',
        ]);

        // 4. Pengasingan Kuasa / Role Isolation Admin Klinik:
        // - Admin Klinik disekat daripada mentadbir modul lain (403 Forbidden)
        $this->actingAs($adminKlinik)->get('/eptr')->assertStatus(403);
        $this->actingAs($adminKlinik)->get('/epu')->assertStatus(403);
        $this->actingAs($adminKlinik)->get('/pawah')->assertStatus(403);
        $this->actingAs($adminKlinik)->get('/inventori/pejabat')->assertStatus(403);
        $this->actingAs($adminKlinik)->get('/inventori/ubat')->assertStatus(403);

        // - Admin Klinik boleh mohon alatan pejabat staf tetapi disekat daripada mohon ubat veterinar (hanya admin_jajahan)
        $this->actingAs($adminKlinik)->get('/inventori/permohonan/pejabat/mohon')->assertStatus(200);
        $this->actingAs($adminKlinik)->get('/inventori/permohonan/ubat/mohon')->assertStatus(403);

        // - Semakan Papan Pemuka Admin Klinik
        $respDashboard = $this->actingAs($adminKlinik)->get('/dashboard');
        $respDashboard->assertStatus(200);
        $respDashboard->assertSee('Daftar Temujanji Rawatan');
        $respDashboard->assertSee('Senarai Temujanji Klinik Veterinar Terkini');
    }

    public function test_permohonan_program_pawah_dari_orang_awam_dan_kelulusan_pegawai()
    {
        $orangAwam = User::where('role', 'orang_awam')->first();
        $adminProgram = User::where('role', 'admin_program')->first();
        $penternak = User::where('role', 'penternak')->first();
        $lembuBetina = Ternakan::where('status', 'Aktif')->where('jantina', 'Betina')->first();

        $this->assertNotNull($orangAwam);
        $this->assertNotNull($adminProgram);
        $this->assertNotNull($lembuBetina);

        // 1. Orang Awam membuka borang permohonan pawah
        $respGet = $this->actingAs($orangAwam)->get('/pawah/perjanjian-baru');
        $respGet->assertStatus(200);
        $respGet->assertSee('Borang Permohonan Program Pawah Ternakan');
        $respGet->assertSee('PERMOHONAN AWAM');
        $respGet->assertSee($orangAwam->name);

        // 2. Orang Awam menghantar permohonan pawah (Kambing Tenusu, jenis ternakan sekarang: Kambing Boer, bilangan: 6 ekor)
        $respStore = $this->actingAs($orangAwam)->post('/pawah/perjanjian-baru', [
            'jenis_pawah' => 'Kambing Tenusu',
            'jenis_ternakan_sedia_ada' => 'Kambing Boer',
            'bilangan_ternakan_sedia_ada' => 6,
            'jajahan' => 'Tumpat',
            'pengalaman_menternak' => '1 - 3 Tahun',
            'keluasan_padang_ragut' => '2 Ekar tanah rumput ragut',
            'jenis_kandang' => 'Kandang Berbumbung Penuh',
            'sumber_makanan' => 'Rumput Napier & Dedak',
            'catatan' => 'Kandang sedia ada dan bersedia menerima ternakan.',
        ]);
        $respStore->assertRedirect();

        $mohonPawah = PawahPerjanjian::where('user_id', $orangAwam->id)
            ->where('jajahan', 'Tumpat')
            ->latest()
            ->first();

        $this->assertNotNull($mohonPawah);
        $this->assertEquals('Menunggu Kelulusan', $mohonPawah->status);
        $this->assertEquals('Kambing Tenusu', $mohonPawah->jenis_pawah);
        $this->assertEquals('Kambing Boer', $mohonPawah->jenis_ternakan_sedia_ada);
        $this->assertEquals(6, $mohonPawah->bilangan_ternakan_sedia_ada);
        $this->assertStringContainsString('Jenis Pawah Dimohon: Kambing Tenusu', $mohonPawah->catatan);
        $this->assertStringContainsString('Jenis Ternakan Sekarang: Kambing Boer', $mohonPawah->catatan);
        $this->assertStringContainsString('Ternakan Sedia Ada: 6 Ekor', $mohonPawah->catatan);
        $this->assertStringContainsString('Pengalaman Menternak: 1 - 3 Tahun', $mohonPawah->catatan);

        // 3. Orang Awam menyemak butiran permohonan
        $respShowAwam = $this->actingAs($orangAwam)->get("/pawah/perjanjian/{$mohonPawah->id}");
        $respShowAwam->assertStatus(200);
        $respShowAwam->assertSee('Menunggu Kelulusan');
        $respShowAwam->assertSee('Kambing Tenusu');
        $respShowAwam->assertSee('Kambing Boer');
        $respShowAwam->assertSee('6 Ekor');
        $respShowAwam->assertSee('Permohonan Program Pawah Sedang Disemak');

        // 4. Pengguna bukan staf DISEKAT daripada meluluskan/menolak permohonan (403 Forbidden)
        $this->actingAs($orangAwam)->post("/pawah/perjanjian/{$mohonPawah->id}/lulus", [
            'ternakan_ids' => [$lembuBetina->id],
            'tarikh_mula' => date('Y-m-d'),
            'tempoh_tahun' => 3,
        ])->assertStatus(403);

        $this->actingAs($orangAwam)->post("/pawah/perjanjian/{$mohonPawah->id}/tolak", [
            'sebab_tolak' => 'Percubaan tolak tidak sah',
        ])->assertStatus(403);

        // 5. Pegawai Admin Pawah meluluskan permohonan dan memautkan lembu induk EPTR
        $respLulus = $this->actingAs($adminProgram)->post("/pawah/perjanjian/{$mohonPawah->id}/lulus", [
            'ternakan_ids' => [$lembuBetina->id],
            'tarikh_mula' => date('Y-m-d'),
            'tempoh_tahun' => 3,
            'pegawai_penyelia' => $adminProgram->name,
            'catatan_kelulusan' => 'Kandang dan padang ragut telah diperiksa dan disahkan lulus SOP.',
        ]);
        $respLulus->assertRedirect();
        $respLulus->assertSessionHas('success');

        $mohonPawah->refresh();
        $this->assertEquals('Aktif', $mohonPawah->status);
        $this->assertStringStartsWith('JPVNK/PAWAH/TU/', $mohonPawah->no_perjanjian);
        $this->assertDatabaseHas('pawah_ternakan', [
            'pawah_perjanjian_id' => $mohonPawah->id,
            'ternakan_id' => $lembuBetina->id,
            'status_induk' => 'Aktif',
        ]);

        $lembuBetina->refresh();
        $this->assertEquals('Pawah', $lembuBetina->status);
        $this->assertEquals('Jabatan Perkhidmatan Veterinar Negeri Kelantan', $lembuBetina->pemunya->nama);

        // Ujian Penyelesaian Program Pawah: Hak milik lembu induk asal bertukar kepada peserta
        $respSelesai = $this->actingAs($adminProgram)->post("/pawah/perjanjian/{$mohonPawah->id}/penyelesaian", [
            'tarikh_penyelesaian' => date('Y-m-d'),
            'bilangan_anak_dipulangkan' => 1,
            'status_penyelesaian' => 'Selesai Sepenuhnya',
            'jumlah_bayaran_tebus_guna' => 0.00,
            'resit_pembayaran' => UploadedFile::fake()->create('resit_penyelesaian.pdf', 500, 'application/pdf'),
            'perakuan' => 'Peserta telah menyempurnakan pemulangan 1 ekor anak pawah dan hak milik induk diserahkan.',
        ]);
        $respSelesai->assertSessionHas('success');

        $lembuBetina->refresh();
        $this->assertEquals('Aktif', $lembuBetina->status);
        $this->assertEquals($orangAwam->name, $lembuBetina->pemunya->nama);
        $this->assertEquals($orangAwam->id, $lembuBetina->pemunya->user_id);

        // 6. Ujian Penolakan Permohonan Pawah
        $permohonanDitolak = PawahPerjanjian::create([
            'user_id' => $penternak->id,
            'no_perjanjian' => 'PW-MOHON-TOLAK-01',
            'nama_program' => 'Program Pawah Ternakan Negeri Kelantan (Permohonan Awam)',
            'tarikh_mula' => date('Y-m-d'),
            'tarikh_tamat' => date('Y-m-d', strtotime('+3 years')),
            'tempoh_tahun' => 3,
            'bilangan_induk' => 1,
            'jajahan' => 'Bachok',
            'status' => 'Menunggu Kelulusan',
        ]);

        $respTolak = $this->actingAs($adminProgram)->post("/pawah/perjanjian/{$permohonanDitolak->id}/tolak", [
            'sebab_tolak' => 'Keluasan padang ragut tidak menepati saiz minimum yang disyaratkan.',
        ]);
        $respTolak->assertRedirect();
        $respTolak->assertSessionHas('success');

        $permohonanDitolak->refresh();
        $this->assertEquals('Ditolak', $permohonanDitolak->status);
        $this->assertStringContainsString('Keluasan padang ragut tidak menepati', $permohonanDitolak->catatan);
    }

    public function test_carian_perjanjian_pawah_mengikut_no_kad_pengenalan()
    {
        $adminProgram = User::where('role', 'admin_program')->first();
        $penternak = User::where('role', 'penternak')->first();
        $orangAwam = User::where('role', 'orang_awam')->first();

        // 1. Carian menggunakan input no_kp khusus
        $respKp = $this->actingAs($adminProgram)->get('/pawah?no_kp=' . $penternak->ic_number);
        $respKp->assertStatus(200);
        $respKp->assertSee($penternak->name);
        $respKp->assertSee($penternak->ic_number);

        // 2. Carian menggunakan input kata kunci umum 'search' dengan nombor kad pengenalan
        $respSearch = $this->actingAs($adminProgram)->get('/pawah?search=' . $orangAwam->ic_number);
        $respSearch->assertStatus(200);
        $respSearch->assertSee($orangAwam->name);
        $respSearch->assertSee($orangAwam->ic_number);

        // 3. Carian dengan format bersempang (contoh: 780312-03-5123)
        $formattedIc = substr($penternak->ic_number, 0, 6) . '-' . substr($penternak->ic_number, 6, 2) . '-' . substr($penternak->ic_number, 8);
        $respHyphen = $this->actingAs($adminProgram)->get('/pawah?search=' . $formattedIc);
        $respHyphen->assertStatus(200);
        $respHyphen->assertSee($penternak->name);

        // 4. Semakan Paparan Bar Penapis:
        // - Staf / Admin melihat penapis No. Kad Pengenalan dan Jajahan
        $respAdminView = $this->actingAs($adminProgram)->get('/pawah');
        $respAdminView->assertStatus(200);
        $respAdminView->assertSee('name="no_kp"', false);
        $respAdminView->assertSee('Semua Jajahan');
        $respAdminView->assertSee('Semua Status');

        // - Orang Awam TIDAK melihat penapis No. Kad Pengenalan dan Jajahan
        $respAwamView = $this->actingAs($orangAwam)->get('/pawah');
        $respAwamView->assertStatus(200);
        $respAwamView->assertDontSee('name="no_kp"', false);
        $respAwamView->assertDontSee('Semua Jajahan');
        $respAwamView->assertSee('Semua Status');
    }

    public function test_carian_temujanji_klinik_mengikut_no_kad_pengenalan()
    {
        $adminKlinik = User::where('role', 'admin_klinik')->first();
        $orangAwam = User::where('role', 'orang_awam')->first();

        // 1. Carian menggunakan input no_kp khusus untuk mencari temujanji pesakit walk-in
        $respKp = $this->actingAs($adminKlinik)->get('/klinik?no_kp=' . $orangAwam->ic_number);
        $respKp->assertStatus(200);
        $respKp->assertSee($orangAwam->name);
        $respKp->assertSee($orangAwam->ic_number);

        // 2. Carian menggunakan carian kata kunci umum 'search'
        $respSearch = $this->actingAs($adminKlinik)->get('/klinik?search=' . $orangAwam->ic_number);
        $respSearch->assertStatus(200);
        $respSearch->assertSee($orangAwam->name);

        // 3. Carian dengan format bersempang (contoh: 950101-03-5999)
        $formattedIc = substr($orangAwam->ic_number, 0, 6) . '-' . substr($orangAwam->ic_number, 6, 2) . '-' . substr($orangAwam->ic_number, 8);
        $respHyphen = $this->actingAs($adminKlinik)->get('/klinik?search=' . $formattedIc);
        $respHyphen->assertStatus(200);
        $respHyphen->assertSee($orangAwam->name);

        // 4. Semakan Paparan Bar Penapis Dalam Kolum:
        // - Admin Klinik melihat input penapis No. Kad Pengenalan dan Klinik Jajahan
        $respAdminView = $this->actingAs($adminKlinik)->get('/klinik');
        $respAdminView->assertStatus(200);
        $respAdminView->assertSee('name="no_kp"', false);
        $respAdminView->assertSee('Semua Klinik');
        $respAdminView->assertSee('Semua Status');

        // - Orang Awam TIDAK melihat penapis No. Kad Pengenalan dan Klinik Jajahan
        $respAwamView = $this->actingAs($orangAwam)->get('/klinik');
        $respAwamView->assertStatus(200);
        $respAwamView->assertDontSee('name="no_kp"', false);
        $respAwamView->assertDontSee('Semua Klinik');
        $respAwamView->assertSee('Semua Status');
    }

    public function test_admin_pawah_can_only_access_pawah_module()
    {
        $adminProgram = User::where('role', 'admin_program')->first();
        $this->assertNotNull($adminProgram);

        // 1. Admin Program boleh akses modul Pawah
        $respPawahIndex = $this->actingAs($adminProgram)->get('/pawah');
        $respPawahIndex->assertStatus(200);
        $respPawahIndex->assertSee('Surat Perjanjian');
        $respPawahIndex->assertSee('Program Pawah');

        $respPawahCreate = $this->actingAs($adminProgram)->get('/pawah/perjanjian-baru');
        $respPawahCreate->assertStatus(200);
        $respPawahCreate->assertSee('Perjanjian');

        // 2. Admin Program melihat Dashboard khusus modul Pawah
        $respDashboard = $this->actingAs($adminProgram)->get('/dashboard');
        $respDashboard->assertStatus(200);
        $respDashboard->assertSee('Admin Program Pawah');
        $respDashboard->assertSee('Perjanjian Pawah Berjalan');
        $respDashboard->assertSee('Program Pawah');
        $respDashboard->assertSee(route('pawah.index'));
        $respDashboard->assertSee(route('pawah.create'));

        // Sidebar tidak memaparkan pautan modul lain untuk Admin Program
        $respDashboard->assertDontSee(route('eptr.index'));
        $respDashboard->assertDontSee(route('epu.index'));
        $respDashboard->assertDontSee(route('klinik.index'));
        $respDashboard->assertDontSee(route('inventori.index'));
        $respDashboard->assertDontSee(route('kenderaan.index'));
        $respDashboard->assertDontSee(route('kursus.index'));

        // 3. Admin Program DITOLAK (403) dari mengakses modul-modul lain
        $this->actingAs($adminProgram)->get('/eptr')->assertStatus(403);
        $this->actingAs($adminProgram)->get('/epu')->assertStatus(403);
        $this->actingAs($adminProgram)->get('/klinik')->assertStatus(403);
        $this->actingAs($adminProgram)->get('/kursus')->assertStatus(403);
        $this->actingAs($adminProgram)->get('/inventori')->assertStatus(403);
        $this->actingAs($adminProgram)->get('/kenderaan')->assertStatus(403);
    }

    public function test_admin_eptr_can_only_access_eptr_module()
    {
        $adminEptr = User::where('role', 'admin_eptr')->first();
        $this->assertNotNull($adminEptr);

        // 1. Admin EPTR boleh akses modul EPTR & sub-modulnya
        $respEptrIndex = $this->actingAs($adminEptr)->get('/eptr');
        $respEptrIndex->assertStatus(200);
        $respEptrIndex->assertSee('Enakmen Pendaftaran Ternakan Ruminan');

        $respKesihatan = $this->actingAs($adminEptr)->get('/eptr/program-kesihatan');
        $respKesihatan->assertStatus(200);

        $respBorangD = $this->actingAs($adminEptr)->get('/eptr/permit-sembelihan-borang-d');
        $respBorangD->assertStatus(200);

        // 2. Admin EPTR melihat Dashboard khusus modul EPTR
        $respDashboard = $this->actingAs($adminEptr)->get('/dashboard');
        $respDashboard->assertStatus(200);
        $respDashboard->assertSee('Admin EPTR');
        $respDashboard->assertSee('Ternakan Berdaftar EPTR');
        $respDashboard->assertSee(route('eptr.index'));

        // Sidebar tidak memaparkan pautan modul lain untuk Admin EPTR
        $respDashboard->assertDontSee(route('pawah.index'));
        $respDashboard->assertDontSee(route('epu.index'));
        $respDashboard->assertDontSee(route('klinik.index'));
        $respDashboard->assertDontSee(route('inventori.index'));
        $respDashboard->assertDontSee(route('kenderaan.index'));
        $respDashboard->assertDontSee(route('kursus.index'));

        // 3. Admin EPTR DITOLAK (403) dari mengakses modul-modul lain
        $this->actingAs($adminEptr)->get('/pawah')->assertStatus(403);
        $this->actingAs($adminEptr)->get('/epu')->assertStatus(403);
        $this->actingAs($adminEptr)->get('/klinik')->assertStatus(403);
        $this->actingAs($adminEptr)->get('/kursus')->assertStatus(403);
        $this->actingAs($adminEptr)->get('/inventori')->assertStatus(403);
        $this->actingAs($adminEptr)->get('/kenderaan')->assertStatus(403);
    }

    public function test_ternakan_dibatalkan_mati_atau_sembelih_disekat_rawatan_dan_sebarang_perubahan()
    {
        $admin = User::where('role', 'admin_eptr')->first();
        $penternak = User::where('role', 'penternak')->first();

        // 1. Dapatkan ternakan aktif
        $ternakan = Ternakan::where('status', 'Aktif')->where('jantina', 'Betina')->first();
        $this->assertNotNull($ternakan);
        $this->assertFalse($ternakan->isDibatalkanAtauMatiAtauSembelih());
        $this->assertTrue($ternakan->canPerformAction());

        // 2. Lakukan pembatalan / kematian ke atas ternakan
        $pembatalan = \App\Models\PembatalanTernakan::create([
            'ternakan_id' => $ternakan->id,
            'jenis_batal' => 'Mati',
            'tarikh_peristiwa' => date('Y-m-d'),
            'sebab' => 'Kematian akibat penyakit kembung perut',
            'status_kelulusan' => 'Disahkan',
            'disahkan_oleh' => $admin->id,
        ]);
        $ternakan->status = 'Mati';
        $ternakan->save();

        $ternakan->refresh();
        $this->assertTrue($ternakan->isDibatalkanAtauMatiAtauSembelih());
        $this->assertFalse($ternakan->canPerformAction());

        // 3. Cubaan mendaftar rawatan / program kesihatan disekat
        $respRawatan = $this->actingAs($admin)->post('/eptr/program-kesihatan/daftar', [
            'ternakan_id' => $ternakan->id,
            'jenis_program' => 'Rawatan Penyakit / Klinikal',
            'nama_vaksin_atau_ubat' => 'Antibiotik Test',
            'tarikh_rawatan' => date('Y-m-d'),
            'status_kesihatan' => 'Rawatan',
            'pegawai_pemeriksa' => 'Dr. Test',
        ]);
        $respRawatan->assertSessionHas('error');
        $this->assertDatabaseMissing('program_kesihatan', [
            'ternakan_id' => $ternakan->id,
            'nama_vaksin_atau_ubat' => 'Antibiotik Test',
        ]);

        // Cubaan buka borang kesihatan dengan ternakan_id yang mati -> redirect error
        $respGetRawatan = $this->actingAs($admin)->get("/eptr/program-kesihatan/daftar?ternakan_id={$ternakan->id}");
        $respGetRawatan->assertRedirect(route('eptr.kesihatan.index'));
        $respGetRawatan->assertSessionHas('error');

        // 4. Cubaan mendaftar kelahiran anak bagi induk yang mati disekat
        $receiptFile = \Illuminate\Http\UploadedFile::fake()->create('resit.pdf', 100, 'application/pdf');
        $respAnak = $this->actingAs($penternak)->post('/eptr/daftar-anak', [
            'induk_id' => $ternakan->id,
            'tarikh_kelahiran' => date('Y-m-d'),
            'jantina_anak' => 'Betina',
            'baka_anak' => 'brahman',
            'status_kelahiran' => 'Hidup',
            'keadaan_anak' => 'Cergas',
            'resit_pembayaran' => $receiptFile,
        ]);
        $respAnak->assertSessionHas('error');

        // 5. Cubaan memohon permit sembelihan (Borang D) bagi ternakan yang mati disekat
        $respSembelih = $this->actingAs($penternak)->post('/eptr/permit-sembelihan-borang-d/mohon', [
            'ternakan_id' => $ternakan->id,
            'tujuan_sembelih' => 'Ibadah Korban',
            'tarikh_sembelih' => date('Y-m-d', strtotime('+2 days')),
        ]);
        $respSembelih->assertSessionHas('error');

        // 6. Cubaan memohon notis pembatalan semula (Borang C) bagi ternakan yang telah mati disekat
        $respBatalSemula = $this->actingAs($penternak)->post('/eptr/pembatalan-borang-c/mohon', [
            'ternakan_id' => $ternakan->id,
            'jenis_batal' => 'Pelupusan',
            'tarikh_peristiwa' => date('Y-m-d'),
            'sebab' => 'Cubaan batal kali kedua',
        ]);
        $respBatalSemula->assertSessionHas('error');

        // 7. Semakan paparan Borang B (Kad Kuning) dan Senarai EPTR
        $respBorangB = $this->actingAs($penternak)->get("/eptr/kad-kuning-borang-b/{$ternakan->id}");
        $respBorangB->assertStatus(200);
        $respBorangB->assertSee('REKOD DITUTUP / TIDAK AKTIF');
        $respBorangB->assertDontSee('Tambah Rekod Kesihatan');
        $respBorangB->assertDontSee('Daftar Anak Baru');

        $respIndex = $this->actingAs($penternak)->get('/eptr');
        $respIndex->assertStatus(200);
        $respIndex->assertSee('Rekod Ditutup');
    }

    public function test_borang_c_document_upload_and_verification()
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $penternak = User::where('role', 'penternak')->first();
        $admin = User::where('role', 'admin_eptr')->first();

        // 1. Cipta ternakan aktif untuk penternak
        $pemunya = Pemunya::where('user_id', $penternak->id)->first() ?? Pemunya::first();
        $ternakan = Ternakan::create([
            'pemunya_id' => $pemunya->id,
            'no_tag' => 'BATAL-DOC-01',
            'jenis_ternakan' => 'Lembu',
            'baka' => 'Kedah-Kelantan',
            'jantina' => 'Betina',
            'status' => 'Aktif',
            'status_kelulusan' => 'Diluluskan',
            'jajahan' => 'Kota Bharu',
        ]);

        // 2. Penternak hantar Notis Borang C bersama muat naik dokumen sokongan
        $docFile = \Illuminate\Http\UploadedFile::fake()->create('surat_kematian_veterinar.pdf', 300, 'application/pdf');
        $resp = $this->actingAs($penternak)->post('/eptr/pembatalan-borang-c/mohon', [
            'ternakan_id' => $ternakan->id,
            'jenis_batal' => 'Mati',
            'tarikh_peristiwa' => date('Y-m-d'),
            'sebab' => 'Kematian akibat sakit kembung perut yang disahkan oleh doktor',
            'dokumen_sokongan' => $docFile,
        ]);

        $resp->assertRedirect(route('eptr.borang-c.index'));
        $resp->assertSessionHas('success');

        $pembatalan = \App\Models\PembatalanTernakan::where('ternakan_id', $ternakan->id)->latest()->first();
        $this->assertNotNull($pembatalan);
        $this->assertEquals('Mati', $pembatalan->jenis_batal);
        $this->assertNotNull($pembatalan->dokumen_sokongan);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($pembatalan->dokumen_sokongan);

        // 3. Semak paparan senarai Borang C menunjukkan pautan dokumen berkaitan
        $respIndex = $this->actingAs($admin)->get(route('eptr.borang-c.index'));
        $respIndex->assertStatus(200);
        $respIndex->assertSee('Dokumen Berkaitan');
        $respIndex->assertSee('BATAL-DOC-01');
    }

    public function test_pawah_application_by_public_with_and_without_experience()
    {
        $penternak = User::where('role', 'penternak')->first();
        $awam = User::where('role', 'orang_awam')->first();

        // 1. Permohonan awam dengan Pengalaman Menternak (mengisi 5 butiran fasiliti)
        $respWithExp = $this->actingAs($penternak)->post('/pawah/perjanjian-baru', [
            'jenis_pawah' => 'Lembu Hibrid',
            'jajahan' => 'Kota Bharu',
            'ada_pengalaman' => 'ya',
            'pengalaman_menternak' => '3 - 5 Tahun',
            'jenis_ternakan_sedia_ada' => 'Lembu Kedah-Kelantan',
            'bilangan_ternakan_sedia_ada' => 8,
            'keluasan_padang_ragut' => '2 Ekar Rumput Napier',
            'jenis_kandang' => 'Kandang Berbumbung Penuh',
            'sumber_makanan' => 'Rumput Napier & Dedak PKP',
            'catatan' => 'Permohonan penternak berpengalaman',
            'perakuan' => 1,
        ]);
        $respWithExp->assertRedirect();

        $this->assertDatabaseHas('pawah_perjanjian', [
            'user_id' => $penternak->id,
            'jenis_pawah' => 'Lembu Hibrid',
            'jenis_ternakan_sedia_ada' => 'Lembu Kedah-Kelantan',
            'bilangan_ternakan_sedia_ada' => 8,
            'status' => 'Menunggu Kelulusan',
        ]);

        // 2. Permohonan awam tanpa pengalaman (Penternak Baru)
        $respNoExp = $this->actingAs($awam)->post('/pawah/perjanjian-baru', [
            'jenis_pawah' => 'Kambing Tenusu',
            'jajahan' => 'Bachok',
            'ada_pengalaman' => 'tidak',
            'pengalaman_menternak' => 'Belum Pernah (Penternak Baru)',
            'catatan' => 'Permohonan peserta baharu',
            'perakuan' => 1,
        ]);
        $respNoExp->assertRedirect();

        $this->assertDatabaseHas('pawah_perjanjian', [
            'user_id' => $awam->id,
            'jenis_pawah' => 'Kambing Tenusu',
            'bilangan_ternakan_sedia_ada' => 0,
            'status' => 'Menunggu Kelulusan',
        ]);

        // 3. Semak paparan Borang Permohonan Pawah (GET)
        $respForm = $this->actingAs($awam)->get('/pawah/perjanjian-baru');
        $respForm->assertStatus(200);
        $respForm->assertSee('Ada Pengalaman Menternak');
        $respForm->assertSee('Keluasan Padang Ragut / Tanah Rumput');
        $respForm->assertSee('Jenis &amp; Keadaan Kandang', false);
        $respForm->assertSee('Sumber Makanan &amp; Bekalan Air', false);
    }

    public function test_pawah_agreement_displays_applicant_eptr_cattle_and_supports_linking(): void
    {
        $adminPawah = User::factory()->create([
            'role' => 'admin_program',
            'name' => 'Admin Pawah Pengesahan',
        ]);

        $penternak = User::factory()->create([
            'role' => 'penternak',
            'name' => 'Kamarul Ariffin bin Yahya',
            'ic_number' => '850515039876',
        ]);

        $pemunya = Pemunya::create([
            'user_id' => $penternak->id,
            'nama' => $penternak->name,
            'no_kp' => $penternak->ic_number,
            'no_telefon' => '0129998877',
            'alamat' => 'Kampung Padang Temusu',
            'jajahan' => 'Pasir Mas',
            'status' => 'Aktif',
        ]);

        // Ternakan sedia ada pemohon di bawah EPTR
        $lembuEptr1 = Ternakan::create([
            'pemunya_id' => $pemunya->id,
            'no_tag' => 'KMR-001',
            'jenis_ternakan' => 'Lembu',
            'baka' => 'Kedah-Kelantan',
            'jantina' => 'Betina',
            'status' => 'Aktif',
            'jajahan' => 'Pasir Mas',
            'tarikh_daftar' => date('Y-m-d'),
        ]);

        $lembuEptr2 = Ternakan::create([
            'pemunya_id' => $pemunya->id,
            'no_tag' => 'KMR-002',
            'jenis_ternakan' => 'Lembu',
            'baka' => 'Brahman',
            'jantina' => 'Jantan',
            'status' => 'Aktif',
            'jajahan' => 'Pasir Mas',
            'tarikh_daftar' => date('Y-m-d'),
        ]);

        // Permohonan Pawah pemohon (status Menunggu Kelulusan)
        $perjanjian = PawahPerjanjian::create([
            'user_id' => $penternak->id,
            'no_perjanjian' => 'PW-MOHON-2026-TEST',
            'nama_program' => 'Program Pawah Lembu Hibrid Negeri Kelantan (Permohonan Awam)',
            'jenis_pawah' => 'Lembu Hibrid',
            'tarikh_mula' => date('Y-m-d'),
            'tarikh_tamat' => date('Y-m-d', strtotime('+3 years')),
            'tempoh_tahun' => 3,
            'bilangan_induk' => 1,
            'jajahan' => 'Pasir Mas',
            'status' => 'Menunggu Kelulusan',
            'catatan' => 'Permohonan ujian',
        ]);

        // 1. Semak paparan perjanjian (GET)
        $response = $this->actingAs($penternak)->get("/pawah/perjanjian/{$perjanjian->id}");
        $response->assertStatus(200);
        $response->assertSee('Senarai Lembu Induk Skim Pawah Dipautkan Dari EPTR');
        $response->assertSee('Ternakan Sedia Ada Pemohon Di Bawah EPTR (Kad Kuning)');
        $response->assertSee('KMR-001');
        $response->assertSee('KMR-002');
        $response->assertSee('2 Ekor Didaftarkan EPTR');

        // 2. Staff memautkan ternakan EPTR ke dalam perjanjian
        $perjanjian->status = 'Aktif';
        $perjanjian->save();

        $respPaut = $this->actingAs($adminPawah)->post("/pawah/perjanjian/{$perjanjian->id}/paut-ternakan", [
            'ternakan_ids' => [$lembuEptr1->id],
        ]);
        $respPaut->assertRedirect();
        $respPaut->assertSessionHas('success');

        $this->assertDatabaseHas('pawah_ternakan', [
            'pawah_perjanjian_id' => $perjanjian->id,
            'ternakan_id' => $lembuEptr1->id,
        ]);

        // 3. Staff membatalkan pautan ternakan
        $respUnlink = $this->actingAs($adminPawah)->delete("/pawah/perjanjian/{$perjanjian->id}/paut-ternakan/{$lembuEptr1->id}");
        $respUnlink->assertRedirect();
        $respUnlink->assertSessionHas('success');

        $this->assertDatabaseMissing('pawah_ternakan', [
            'pawah_perjanjian_id' => $perjanjian->id,
            'ternakan_id' => $lembuEptr1->id,
        ]);
    }

    public function test_pawah_application_auto_ticks_experience_if_registered_in_eptr(): void
    {
        $penternak = User::factory()->create([
            'role' => 'penternak',
            'name' => 'Wan Zulkifli Wan Daud',
            'ic_number' => '790812036543',
            'jajahan' => 'Kota Bharu',
        ]);

        $pemunya = Pemunya::create([
            'user_id' => $penternak->id,
            'nama' => $penternak->name,
            'no_kp' => $penternak->ic_number,
            'no_telefon' => '0198887766',
            'alamat' => 'Peringat, Kota Bharu',
            'jajahan' => 'Kota Bharu',
            'status' => 'Aktif',
        ]);

        Ternakan::create([
            'pemunya_id' => $pemunya->id,
            'no_tag' => 'WZ-001',
            'jenis_ternakan' => 'Lembu',
            'baka' => 'Charolais Cross',
            'jantina' => 'Betina',
            'status' => 'Aktif',
            'jajahan' => 'Kota Bharu',
            'tarikh_daftar' => date('Y-m-d'),
        ]);

        Ternakan::create([
            'pemunya_id' => $pemunya->id,
            'no_tag' => 'WZ-002',
            'jenis_ternakan' => 'Lembu',
            'baka' => 'Kedah-Kelantan',
            'jantina' => 'Betina',
            'status' => 'Aktif',
            'jajahan' => 'Kota Bharu',
            'tarikh_daftar' => date('Y-m-d'),
        ]);

        // 1. GET form - semak banner dikesan dan prefill spesies ternakan
        $respForm = $this->actingAs($penternak)->get('/pawah/perjanjian-baru');
        $respForm->assertStatus(200);
        $respForm->assertSee('Rekod EPTR Dikesan: Pendaftaran Ternakan Sah Ditemui');
        $respForm->assertSee('2 ekor ternakan');
        $respForm->assertSee('value="Lembu"', false);

        // 2. POST form tanpa mengisi secara manual - auto detect pengalaman & ternakan (spesies)
        $respStore = $this->actingAs($penternak)->post('/pawah/perjanjian-baru', [
            'jenis_pawah' => 'Lembu Hibrid',
            'jajahan' => 'Kota Bharu',
            'keluasan_padang_ragut' => '3 Ekar',
            'jenis_kandang' => 'Kandang Bumbung Zink',
            'sumber_makanan' => 'Rumput Napier',
            'perakuan' => 1,
        ]);
        $respStore->assertRedirect();

        $this->assertDatabaseHas('pawah_perjanjian', [
            'user_id' => $penternak->id,
            'jenis_pawah' => 'Lembu Hibrid',
            'jenis_ternakan_sedia_ada' => 'Lembu',
            'bilangan_ternakan_sedia_ada' => 2,
            'status' => 'Menunggu Kelulusan',
        ]);
    }

    public function test_borang_b_transfer_of_ownership_lifecycle()
    {
        $admin = User::where('role', 'admin_jajahan')->first();
        $penternak = User::where('role', 'penternak')->first();
        $pemunya1 = Pemunya::where('user_id', $penternak->id)->first();

        // Cipta Pemunya kedua sebagai pembeli
        $pemunya2 = Pemunya::create([
            'nama' => 'MOHD ZAIN BIN CHE MAT',
            'no_kp' => '780101037788',
            'no_telefon' => '0139887766',
            'alamat' => 'Kg Chepa, Kubang Kerian',
            'jajahan' => 'Kota Bharu',
            'daerah' => 'Kubang Kerian',
            'poskod' => '16150',
            'status' => 'Aktif',
        ]);

        $ternakan = Ternakan::create([
            'pemunya_id' => $pemunya1->id,
            'no_tag' => 'KB-PM-001',
            'jenis_ternakan' => 'Lembu',
            'baka' => 'Brahman',
            'jantina' => 'Jantan',
            'status' => 'Aktif',
            'status_kelulusan' => 'Diluluskan',
            'jajahan' => 'Kota Bharu',
            'tarikh_daftar' => date('Y-m-d'),
        ]);

        // 1. Pegawai / Admin disekat daripada mengisi Borang B (hanya pemohon/penternak boleh buat)
        $respAdminCreate = $this->actingAs($admin)->get('/eptr/pindah-milik-borang-b/mohon?ternakan_id=' . $ternakan->id);
        $respAdminCreate->assertRedirect(route('eptr.borang-b.index'));
        $respAdminCreate->assertSessionHas('error');

        $respAdminStore = $this->actingAs($admin)->post('/eptr/pindah-milik-borang-b/mohon', [
            'ternakan_id' => $ternakan->id,
            'jenis_pemunya_baru' => 'sedia_ada',
            'pemunya_baru_id' => $pemunya2->id,
            'tarikh_pindah' => date('Y-m-d'),
            'sebab_pindah' => 'Jualan',
            'perakuan' => 1,
        ]);
        $respAdminStore->assertRedirect(route('eptr.borang-b.index'));
        $respAdminStore->assertSessionHas('error');

        // 2. Penternak isi Borang B memohon pindah milik ke Pemunya 2
        $respCreate = $this->actingAs($penternak)->get('/eptr/pindah-milik-borang-b/mohon?ternakan_id=' . $ternakan->id);
        $respCreate->assertStatus(200);
        $respCreate->assertSee('Borang Notis Pertukaran Milikan Ternakan Ruminan');

        // 2.1 Permohonan tanpa resit pembayaran oleh penternak hendaklah disekat
        $respStoreNoReceipt = $this->actingAs($penternak)->post('/eptr/pindah-milik-borang-b/mohon', [
            'ternakan_id' => $ternakan->id,
            'jenis_pemunya_baru' => 'sedia_ada',
            'pemunya_baru_id' => $pemunya2->id,
            'tarikh_pindah' => date('Y-m-d'),
            'sebab_pindah' => 'Jualan',
            'harga_jualan' => 4200.00,
            'catatan' => 'Jualan tunai antara penternak',
            'perakuan' => 1,
        ]);
        $respStoreNoReceipt->assertSessionHasErrors('resit_pembayaran');

        // 2.2 Permohonan lengkap berserta resit pembayaran (Diterima)
        $receiptBorangB = \Illuminate\Http\UploadedFile::fake()->create('resit_pindah_milik.pdf', 120, 'application/pdf');
        $respStore = $this->actingAs($penternak)->post('/eptr/pindah-milik-borang-b/mohon', [
            'ternakan_id' => $ternakan->id,
            'jenis_pemunya_baru' => 'sedia_ada',
            'pemunya_baru_id' => $pemunya2->id,
            'tarikh_pindah' => date('Y-m-d'),
            'sebab_pindah' => 'Jualan',
            'harga_jualan' => 4200.00,
            'catatan' => 'Jualan tunai antara penternak',
            'perakuan' => 1,
            'resit_pembayaran' => $receiptBorangB,
        ]);
        $respStore->assertRedirect();

        $pindahMilik = \App\Models\PindahMilik::where('ternakan_id', $ternakan->id)->first();
        $this->assertNotNull($pindahMilik);
        $this->assertNotNull($pindahMilik->resit_pembayaran);
        $this->assertEquals('Menunggu', $pindahMilik->status_kelulusan);
        $this->assertEquals($pemunya1->id, $pindahMilik->pemunya_asal_id);
        $this->assertEquals($pemunya2->id, $pindahMilik->pemunya_baru_id);
        $this->assertEquals(4200.00, (float)$pindahMilik->harga_jualan);

        // Ternakan masih milik pemunya1 sebelum kelulusan
        $this->assertEquals($pemunya1->id, $ternakan->fresh()->pemunya_id);

        // Admin Jajahan berlainan jajahan disekat daripada meluluskan
        $adminBachok = User::where('jajahan', 'Bachok')->whereIn('role', ['admin_jajahan', 'admin_eptr_jajahan'])->first();
        if ($adminBachok) {
            $respWrongJajahan = $this->actingAs($adminBachok)->post("/eptr/pindah-milik-borang-b/{$pindahMilik->id}/lulus");
            $respWrongJajahan->assertSessionHas('error');
            $this->assertEquals('Menunggu', $pindahMilik->fresh()->status_kelulusan);
        }

        // 3. Admin EPTR Negeri / Admin Jajahan yang sah semak dan luluskan Borang B
        $respLulus = $this->actingAs($admin)->post("/eptr/pindah-milik-borang-b/{$pindahMilik->id}/lulus");
        $respLulus->assertRedirect();

        $this->assertEquals('Diluluskan', $pindahMilik->fresh()->status_kelulusan);
        // Pemilikan ternakan berpindah kepada pemunya2
        $this->assertEquals($pemunya2->id, $ternakan->fresh()->pemunya_id);

        // 4. Cetakan Borang B
        $respPrint = $this->actingAs($admin)->get("/eptr/pindah-milik-borang-b/{$pindahMilik->id}/cetak");
        $respPrint->assertStatus(200);
        $respPrint->assertSee('BORANG B');
        $respPrint->assertSee('PERAKUAN DAFTAR TERNAKAN RUMINAN');
        $respPrint->assertSee('PERTUKARAN MILIKAN');
        $respPrint->assertSee('<b>Lembu (L)</b>', false);
        $respPrint->assertSee($pemunya1->nama);
        $respPrint->assertSee($pemunya2->nama);

        // 5. Cetak Pukal Borang B
        $respBulk = $this->actingAs($admin)->post('/eptr/pindah-milik-borang-b/cetak-pukal', [
            'ids' => [$pindahMilik->id]
        ]);
        $respBulk->assertStatus(200);
        $respBulk->assertSee('CETAK PUKAL BORANG B');
        $respBulk->assertSee('PERAKUAN DAFTAR TERNAKAN RUMINAN');

        // 6. Ternakan yang telah mati/sembelih/batal tidak boleh dipindah milik
        $ternakanMati = Ternakan::create([
            'pemunya_id' => $pemunya1->id,
            'no_tag' => 'KB-MATI-001',
            'jenis_ternakan' => 'Lembu',
            'baka' => 'Kedah-Kelantan',
            'jantina' => 'Betina',
            'status' => 'Mati',
            'status_kelulusan' => 'Diluluskan',
            'jajahan' => 'Kota Bharu',
            'tarikh_daftar' => date('Y-m-d'),
        ]);

        $respDead = $this->actingAs($penternak)->post('/eptr/pindah-milik-borang-b/mohon', [
            'ternakan_id' => $ternakanMati->id,
            'jenis_pemunya_baru' => 'sedia_ada',
            'pemunya_baru_id' => $pemunya2->id,
            'tarikh_pindah' => date('Y-m-d'),
            'sebab_pindah' => 'Jualan',
            'perakuan' => 1,
            'resit_pembayaran' => \Illuminate\Http\UploadedFile::fake()->create('resit.pdf', 100, 'application/pdf'),
        ]);
        $respDead->assertSessionHas('error');

        // 7. Permohonan Pindah Milik Banyak Ekor Sekali Gus (Multi-Livestock Batch Transfer)
        $ternakan3 = Ternakan::create([
            'pemunya_id' => $pemunya1->id,
            'no_tag' => 'KB-MULTI-001',
            'jenis_ternakan' => 'Lembu',
            'baka' => 'Brahman',
            'jantina' => 'Betina',
            'status' => 'Aktif',
            'status_kelulusan' => 'Diluluskan',
            'jajahan' => 'Kota Bharu',
            'tarikh_daftar' => date('Y-m-d'),
        ]);

        $ternakan4 = Ternakan::create([
            'pemunya_id' => $pemunya1->id,
            'no_tag' => 'KB-MULTI-002',
            'jenis_ternakan' => 'Lembu',
            'baka' => 'Kedah-Kelantan',
            'jantina' => 'Jantan',
            'status' => 'Aktif',
            'status_kelulusan' => 'Diluluskan',
            'jajahan' => 'Kota Bharu',
            'tarikh_daftar' => date('Y-m-d'),
        ]);

        $multiResit = \Illuminate\Http\UploadedFile::fake()->create('resit_multi.pdf', 150, 'application/pdf');
        $respMulti = $this->actingAs($penternak)->post('/eptr/pindah-milik-borang-b/mohon', [
            'ternakan_ids' => [$ternakan3->id, $ternakan4->id],
            'jenis_pemunya_baru' => 'sedia_ada',
            'pemunya_baru_id' => $pemunya2->id,
            'tarikh_pindah' => date('Y-m-d'),
            'sebab_pindah' => 'Jualan',
            'harga_jualan' => 6000.00,
            'catatan' => 'Jualan pakej 2 ekor sekali gus',
            'perakuan' => 1,
            'resit_pembayaran' => $multiResit,
        ]);
        $respMulti->assertRedirect(route('eptr.borang-b.index'));

        $pm3 = \App\Models\PindahMilik::where('ternakan_id', $ternakan3->id)->first();
        $pm4 = \App\Models\PindahMilik::where('ternakan_id', $ternakan4->id)->first();

        $this->assertNotNull($pm3);
        $this->assertNotNull($pm4);
        $this->assertEquals(3000.00, (float)$pm3->harga_jualan);
        $this->assertEquals(3000.00, (float)$pm4->harga_jualan);
        $this->assertEquals('Menunggu', $pm3->status_kelulusan);
        $this->assertEquals('Menunggu', $pm4->status_kelulusan);
        $this->assertNotNull($pm3->resit_pembayaran);
        $this->assertNotNull($pm4->resit_pembayaran);
    }

    public function test_borang_b_ic_check_and_transfer_lifecycle()
    {
        $penternak = User::where('role', 'penternak')->first();
        $pemunyaAsal = Pemunya::where('user_id', $penternak->id)->first();

        // Penternak sedia ada
        $pemunyaLama = Pemunya::create([
            'nama' => 'WAN ISMAIL BIN WAN JUSOH',
            'no_kp' => '850505035511',
            'no_telefon' => '0199998888',
            'alamat' => 'Kampung Salor, Pasir Mas',
            'jajahan' => 'Pasir Mas',
            'daerah' => 'Salor',
            'poskod' => '17000',
            'status' => 'Aktif',
        ]);

        $ternakanA = Ternakan::create([
            'pemunya_id' => $pemunyaAsal->id,
            'no_tag' => 'KB-TEST-IC-01',
            'jenis_ternakan' => 'Lembu',
            'baka' => 'Brahman',
            'jantina' => 'Jantan',
            'status' => 'Aktif',
            'status_kelulusan' => 'Diluluskan',
            'jajahan' => 'Kota Bharu',
            'tarikh_daftar' => date('Y-m-d'),
        ]);

        $ternakanB = Ternakan::create([
            'pemunya_id' => $pemunyaAsal->id,
            'no_tag' => 'KB-TEST-IC-02',
            'jenis_ternakan' => 'Lembu',
            'baka' => 'Charolais',
            'jantina' => 'Betina',
            'status' => 'Aktif',
            'status_kelulusan' => 'Diluluskan',
            'jajahan' => 'Kota Bharu',
            'tarikh_daftar' => date('Y-m-d'),
        ]);

        // 1. Semak UI Borang B mengandungi input semakan No. KP
        $respCreate = $this->actingAs($penternak)->get('/eptr/pindah-milik-borang-b/mohon?ternakan_id=' . $ternakanA->id);
        $respCreate->assertStatus(200);
        $respCreate->assertSee('Masukkan No. Kad Pengenalan (MyKad) Penerima / Pembeli');
        $respCreate->assertSee('Semak No. KP');
        $respCreate->assertSee('no_kp_semakan');

        // 2. Hantar Borang B untuk Penternak Sedia Ada melalui No. KP (auto-resolve)
        $receiptA = \Illuminate\Http\UploadedFile::fake()->create('resitA.pdf', 100, 'application/pdf');
        $respStoreLama = $this->actingAs($penternak)->post('/eptr/pindah-milik-borang-b/mohon', [
            'ternakan_id' => $ternakanA->id,
            'jenis_pemunya_baru' => 'sedia_ada',
            'no_kp_semakan' => '850505035511',
            'tarikh_pindah' => date('Y-m-d'),
            'sebab_pindah' => 'Jualan',
            'harga_jualan' => 3500.00,
            'perakuan' => 1,
            'resit_pembayaran' => $receiptA,
        ]);
        $respStoreLama->assertRedirect();

        $pmLama = \App\Models\PindahMilik::where('ternakan_id', $ternakanA->id)->first();
        $this->assertNotNull($pmLama);
        $this->assertEquals($pemunyaLama->id, $pmLama->pemunya_baru_id);
        $this->assertEquals('Menunggu', $pmLama->status_kelulusan);

        // 3. Hantar Borang B untuk Penternak Baharu (belum wujud di sistem EPTR)
        $receiptB = \Illuminate\Http\UploadedFile::fake()->create('resitB.pdf', 100, 'application/pdf');
        $respStoreBaru = $this->actingAs($penternak)->post('/eptr/pindah-milik-borang-b/mohon', [
            'ternakan_id' => $ternakanB->id,
            'jenis_pemunya_baru' => 'baru',
            'no_kp_pemunya_baru' => '950606037799',
            'nama_pemunya_baru' => 'NIK MOHD AZMAN BIN NIK PA',
            'no_tel_pemunya_baru' => '01122334455',
            'alamat_pemunya_baru' => 'Kg Kok Lanas, Kota Bharu',
            'jajahan_pemunya_baru' => 'Kota Bharu',
            'daerah_pemunya_baru' => 'Kadok',
            'poskod_pemunya_baru' => '16450',
            'lokasi_kandang_baru' => 'Kandang Kok Lanas',
            'tarikh_pindah' => date('Y-m-d'),
            'sebab_pindah' => 'Jualan',
            'harga_jualan' => 4000.00,
            'perakuan' => 1,
            'resit_pembayaran' => $receiptB,
        ]);
        $respStoreBaru->assertRedirect();

        $pemunyaBaruCreated = Pemunya::where('no_kp', '950606037799')->first();
        $this->assertNotNull($pemunyaBaruCreated);
        $this->assertEquals('NIK MOHD AZMAN BIN NIK PA', $pemunyaBaruCreated->nama);

        $pmBaru = \App\Models\PindahMilik::where('ternakan_id', $ternakanB->id)->first();
        $this->assertNotNull($pmBaru);
        $this->assertEquals($pemunyaBaruCreated->id, $pmBaru->pemunya_baru_id);
    }

    public function test_eptr_penternak_index_and_show_with_filters_and_scoping()
    {
        $admin = User::where('role', 'admin_eptr')->first();
        $penternakUser = User::create([
            'name' => 'Haji Abdullah bin Ali',
            'email' => 'abdullah.penternak@example.com',
            'ic_number' => '700101035511',
            'phone' => '0129881122',
            'role' => 'penternak',
            'status' => 'Aktif',
            'password' => bcrypt('password'),
        ]);

        // 1. Cipta Penternak Contoh
        $pemunya1 = Pemunya::create([
            'user_id' => $penternakUser->id,
            'nama' => 'Haji Abdullah bin Ali',
            'no_kp' => '700101035511',
            'no_telefon' => '0129881122',
            'alamat' => 'Kampung Padang Temusu',
            'jajahan' => 'Pasir Mas',
            'daerah' => 'Lemal',
            'poskod' => '17000',
            'status' => 'Aktif',
        ]);

        $pemunya2 = Pemunya::create([
            'user_id' => null,
            'nama' => 'Siti Aminah binti Yusof',
            'no_kp' => '850505036622',
            'no_telefon' => '0139994455',
            'alamat' => 'Kampung Chekok',
            'jajahan' => 'Kota Bharu',
            'daerah' => 'Kubang Kerian',
            'poskod' => '16150',
            'status' => 'Aktif',
        ]);

        // Cipta Ternakan untuk pemunya1
        $ternakanLembu = Ternakan::create([
            'pemunya_id' => $pemunya1->id,
            'no_tag' => 'PM-LML-0099',
            'jenis_ternakan' => 'Lembu',
            'baka' => 'Brahman Cross',
            'jantina' => 'Betina',
            'status' => 'Aktif',
            'status_kelulusan' => 'Diluluskan',
            'jajahan' => 'Pasir Mas',
            'daerah' => 'Lemal',
            'tarikh_daftar' => date('Y-m-d'),
        ]);

        $ternakanKambing = Ternakan::create([
            'pemunya_id' => $pemunya1->id,
            'no_tag' => 'PM-LML-0100',
            'jenis_ternakan' => 'Kambing',
            'baka' => 'Boer',
            'jantina' => 'Jantan',
            'status' => 'Aktif',
            'status_kelulusan' => 'Diluluskan',
            'jajahan' => 'Pasir Mas',
            'daerah' => 'Lemal',
            'tarikh_daftar' => date('Y-m-d'),
        ]);

        // 2. Akses Penternak Index sebagai Admin
        $respAdmin = $this->actingAs($admin)->get('/eptr/penternak');
        $respAdmin->assertStatus(200);
        $respAdmin->assertSee('Direktori Penternak');
        $respAdmin->assertSee('Haji Abdullah bin Ali');
        $respAdmin->assertSee('Siti Aminah binti Yusof');
        $respAdmin->assertSee('700101035511');

        // 3. Tapis mengikut Carian Nama
        $respSearch = $this->actingAs($admin)->get('/eptr/penternak?search=Abdullah');
        $respSearch->assertStatus(200);
        $respSearch->assertSee('Haji Abdullah bin Ali');
        $respSearch->assertDontSee('Siti Aminah binti Yusof');

        // 4. Tapis mengikut Jajahan
        $respJajahan = $this->actingAs($admin)->get('/eptr/penternak?jajahan=Pasir+Mas');
        $respJajahan->assertStatus(200);
        $respJajahan->assertSee('Haji Abdullah bin Ali');
        $respJajahan->assertDontSee('Siti Aminah binti Yusof');

        // 5. Tapis mengikut Kategori Pemilikan Ternakan (Ada Ternakan vs Tiada Ternakan)
        $respAdaTernakan = $this->actingAs($admin)->get('/eptr/penternak?kategori_ternakan=ada_ternakan');
        $respAdaTernakan->assertStatus(200);
        $respAdaTernakan->assertSee('Haji Abdullah bin Ali');

        // 6. Tapis mengikut Spesies Dimiliki (Kambing)
        $respKambing = $this->actingAs($admin)->get('/eptr/penternak?spesies=Kambing');
        $respKambing->assertStatus(200);
        $respKambing->assertSee('Haji Abdullah bin Ali');

        // 7. Akses Profil Penternak Show
        $respShow = $this->actingAs($admin)->get("/eptr/penternak/{$pemunya1->id}");
        $respShow->assertStatus(200);
        $respShow->assertSee('Haji Abdullah bin Ali');
        $respShow->assertSee('PM-LML-0099');
        $respShow->assertSee('PM-LML-0100');
        $respShow->assertSee('Brahman Cross');
        $respShow->assertSee('Boer');
        $respShow->assertSee('Sejarah Pindah Milik (Borang B)');

        // 8. Sekatan Penternak Biasa: Penternak hanya boleh melihat profil dirinya sendiri
        $respSelf = $this->actingAs($penternakUser)->get("/eptr/penternak/{$pemunya1->id}");
        $respSelf->assertStatus(200);

        // Penternak tidak boleh melihat profil penternak lain
        $respOther = $this->actingAs($penternakUser)->get("/eptr/penternak/{$pemunya2->id}");
        $respOther->assertStatus(403);

        // 9. Sekatan Orang Awam: Orang awam sama sekali TIDAK BOLEH melihat senarai atau profil penternak
        $orangAwamUser = User::where('role', 'orang_awam')->first();
        if (!$orangAwamUser) {
            $orangAwamUser = User::create([
                'name' => 'Orang Awam Demo',
                'email' => 'orangawam@example.com',
                'ic_number' => '950101035599',
                'phone' => '01122334455',
                'role' => 'orang_awam',
                'status' => 'Aktif',
                'password' => bcrypt('password'),
            ]);
        }

        $respAwamIndex = $this->actingAs($orangAwamUser)->get('/eptr/penternak');
        $respAwamIndex->assertStatus(403);

        $respAwamShow = $this->actingAs($orangAwamUser)->get("/eptr/penternak/{$pemunya1->id}");
        $respAwamShow->assertStatus(403);

        // 10. Filter "Pilih Pemilik / Pemunya" di /eptr hanya untuk staf dan tidak dipaparkan untuk orang awam
        $respEptrAdmin = $this->actingAs($admin)->get('/eptr');
        $respEptrAdmin->assertStatus(200);
        $respEptrAdmin->assertSee('Pilih Pemilik / Pemunya');

        $respEptrAwam = $this->actingAs($orangAwamUser)->get('/eptr');
        $respEptrAwam->assertStatus(200);
        $respEptrAwam->assertDontSee('Pilih Pemilik / Pemunya');
    }

    public function test_borang_d_and_skv_sembelih_lifecycle_with_limits_and_validity_period(): void
    {
        $admin = User::where('role', 'admin_eptr')->first();
        $penternak = User::where('role', 'penternak')->first();
        $pemunya = Pemunya::where('user_id', $penternak->id)->first() ?? Pemunya::create([
            'user_id' => $penternak->id,
            'nama' => $penternak->name,
            'no_kp' => $penternak->ic_number ?? '800101035511',
            'no_telefon' => '0199998877',
            'alamat' => 'Kampung Padang Temusu',
            'jajahan' => 'Kota Bharu',
            'status' => 'Aktif',
        ]);

        // 1. Cipta 8 ekor ternakan berdaftar untuk menguji had 7 baris
        $ternakanArr = [];
        for ($i = 1; $i <= 8; $i++) {
            $ternakanArr[] = Ternakan::create([
                'pemunya_id' => $pemunya->id,
                'no_tag' => "KB-TEST-00{$i}",
                'jenis_ternakan' => 'Lembu',
                'baka' => 'Brahman Cross',
                'jantina' => $i % 2 === 0 ? 'Betina' : 'Jantan',
                'status' => 'Aktif',
                'status_kelulusan' => 'Diluluskan',
                'jajahan' => 'Kota Bharu',
                'no_siri_kad_kuning' => "DB-KB-2026-00{$i}",
                'tarikh_daftar' => date('Y-m-d'),
            ]);
        }

        // 1.1 Semak paparan Borang D mengandungi FI STATUTORI Kadar Fi Sembelihan & SKV
        $respCreateForm = $this->actingAs($penternak)->get('/eptr/permit-sembelihan-borang-d/mohon');
        $respCreateForm->assertStatus(200);
        $respCreateForm->assertSee('Kadar Fi Sembelihan &amp; SKV (Perenggan 11(1)(b))', false);
        $respCreateForm->assertSee('Jumlah Kadar Fi Sembelihan dan SKV');

        // 1.2 Semak permohonan tanpa resit bayaran oleh penternak disekat (Wajib upload resit)
        $respNoReceipt = $this->actingAs($penternak)->post('/eptr/permit-sembelihan-borang-d/mohon', [
            'pemunya_id' => $pemunya->id,
            'jenis_ternakan' => 'Lembu',
            'tujuan_sembelih' => 'Jualan',
            'is_musim_korban' => 0,
            'tarikh_sembelih' => date('Y-m-d'),
            'no_kenderaan' => 'DBC 1234',
            'items' => [
                [
                    'ternakan_id' => $ternakanArr[0]->id,
                    'jantina' => 'J',
                    'no_id_ternakan' => $ternakanArr[0]->no_tag,
                    'no_siri_kad_pendaftaran' => $ternakanArr[0]->no_siri_kad_kuning,
                    'tarikh_sembelihan' => date('Y-m-d'),
                    'tempat_sembelihan' => 'Rumah Sembelih Kota Bharu',
                    'kuantiti_karkas' => '1 Ekor',
                ]
            ],
        ]);
        $respNoReceipt->assertSessionHasErrors('resit_pembayaran');

        // 2. Permohonan hari biasa melebihi 7 baris (8 baris) hendaklah DITOLAK
        $resitFile = \Illuminate\Http\UploadedFile::fake()->create('resit_sembelih.pdf', 150, 'application/pdf');
        $items8 = [];
        for ($i = 0; $i < 8; $i++) {
            $items8[] = [
                'ternakan_id' => $ternakanArr[$i]->id,
                'jantina' => $ternakanArr[$i]->jantina === 'Jantan' ? 'J' : 'B',
                'no_id_ternakan' => $ternakanArr[$i]->no_tag,
                'no_siri_kad_pendaftaran' => $ternakanArr[$i]->no_siri_kad_kuning,
                'tarikh_sembelihan' => date('Y-m-d'),
                'tempat_sembelihan' => 'Rumah Sembelih Kota Bharu',
                'kuantiti_karkas' => '1 Ekor',
            ];
        }

        $respOver7 = $this->actingAs($penternak)->post('/eptr/permit-sembelihan-borang-d/mohon', [
            'pemunya_id' => $pemunya->id,
            'jenis_ternakan' => 'Lembu',
            'tujuan_sembelih' => 'Jualan',
            'is_musim_korban' => 0,
            'tarikh_sembelih' => date('Y-m-d'),
            'no_kenderaan' => 'DBC 1234',
            'resit_pembayaran' => $resitFile,
            'items' => $items8,
        ]);
        $respOver7->assertSessionHasErrors('items');

        // 3. Permohonan hari biasa dengan 7 baris (Diterima)
        $items7 = array_slice($items8, 0, 7);
        $tarikhSembelih = date('Y-m-d');
        $resitFile7 = \Illuminate\Http\UploadedFile::fake()->create('resit_sembelih_7.pdf', 150, 'application/pdf');
        $respValid7 = $this->actingAs($penternak)->post('/eptr/permit-sembelihan-borang-d/mohon', [
            'pemunya_id' => $pemunya->id,
            'jenis_ternakan' => 'Lembu',
            'tujuan_sembelih' => 'Jualan',
            'is_musim_korban' => 0,
            'tarikh_sembelih' => $tarikhSembelih,
            'no_kenderaan' => 'DBC 1234',
            'alamat_1' => 'Pasar Besar Siti Khadijah',
            'kuantiti_karkas_1' => '4 Paha',
            'resit_pembayaran' => $resitFile7,
            'items' => $items7,
        ]);
        $respValid7->assertRedirect();

        $permit = PermitSembelihan::where('pemunya_id', $pemunya->id)->latest('id')->first();
        $this->assertNotNull($permit);
        $this->assertNotNull($permit->resit_pembayaran);
        $this->assertEquals(10.00, (float)$permit->kadar_bayaran); // Bayaran RM 10.00
        $this->assertEquals(7, count($permit->senarai_ternakan_list));
        $this->assertEquals('J', $permit->senarai_ternakan_list[0]['jantina']);
        $this->assertEquals($ternakanArr[0]->no_siri_kad_kuning, $permit->senarai_ternakan_list[0]['no_siri_kad_pendaftaran']);
        $this->assertEquals('B', $permit->senarai_ternakan_list[1]['jantina']);
        $this->assertEquals($ternakanArr[1]->no_siri_kad_kuning, $permit->senarai_ternakan_list[1]['no_siri_kad_pendaftaran']);

        // Sah Laku 7 Hari
        $tarikhMula = \Carbon\Carbon::parse($tarikhSembelih)->toDateString();
        $tarikhTamat = \Carbon\Carbon::parse($tarikhSembelih)->addDays(6)->toDateString();
        $this->assertEquals($tarikhMula, $permit->tarikh_mula ? $permit->tarikh_mula->toDateString() : $permit->tarikh_sembelih->toDateString());
        $this->assertEquals($tarikhTamat, $permit->tarikh_tamat->toDateString());

        // 4. Admin meluluskan permit
        $respLulus = $this->actingAs($admin)->post("/eptr/permit-sembelihan-borang-d/{$permit->id}/lulus");
        $respLulus->assertRedirect();
        $this->assertEquals('Diluluskan', $permit->fresh()->status_kelulusan);

        // 5. Cetakan Sijil SKV Sembelih (2 Halaman)
        $respPrintSkv = $this->actingAs($admin)->get("/eptr/permit-sembelihan-borang-d/{$permit->id}/cetak-skv");
        $respPrintSkv->assertStatus(200);
        $respPrintSkv->assertSee('PPVJKB.600-3');
        $respPrintSkv->assertSee('jata-kelantan.png');
        $respPrintSkv->assertSee('كوتا بهارو');
        $respPrintSkv->assertSee('KEPADA SESIAPA YANG BERKENAAN');
        $respPrintSkv->assertSee('Kebenaran bertulis penyembelihan binatang dan Sijil Kesihatan Veterinar (SKV) penyembelihan binatang');
        $respPrintSkv->assertSee('Kebenaran bertulis pemindahan karkas dan Sijil Kesihatan Veterinar (SKV) karkas setelah sembelihan');
        $respPrintSkv->assertSee('DBC 1234');

        // 6. Cetakan Set Lengkap (Borang D + SKV)
        $respPrintLengkap = $this->actingAs($admin)->get("/eptr/permit-sembelihan-borang-d/{$permit->id}/cetak-lengkap");
        $respPrintLengkap->assertStatus(200);
        $respPrintLengkap->assertSee('BORANG D');
        $respPrintLengkap->assertSee('PPVJKB.600-3');
        $respPrintLengkap->assertSee('jata-kelantan.png');
        $respPrintLengkap->assertSee('كوتا بهارو');

        // 7. Musim Hari Raya Korban membenarkan sehingga 10 baris
        $items10 = [];
        for ($k = 1; $k <= 10; $k++) {
            $items10[] = [
                'jantina' => 'J',
                'no_id_ternakan' => "KORBAN-2026-0{$k}",
                'no_siri_kad_pendaftaran' => "DB-KB-2026-K0{$k}",
                'tarikh_sembelihan' => date('Y-m-d'),
                'tempat_sembelihan' => 'Masjid Kota Bharu',
                'kuantiti_karkas' => '1 Ekor',
            ];
        }

        $resitKorban = \Illuminate\Http\UploadedFile::fake()->create('resit_korban.jpg', 150, 'image/jpeg');
        $respKorban = $this->actingAs($penternak)->post('/eptr/permit-sembelihan-borang-d/mohon', [
            'pemunya_id' => $pemunya->id,
            'jenis_ternakan' => 'Lembu',
            'tujuan_sembelih' => 'Ibadah Korban',
            'tarikh_sembelih' => '2026-05-27', // Jatuh Hari Raya Pertama Aidiladha
            'no_kenderaan' => 'DBC 9999',
            'resit_pembayaran' => $resitKorban,
            'items' => $items10,
        ]);
        $respKorban->assertRedirect();

        $permitKorban = PermitSembelihan::where('is_musim_korban', true)->latest('id')->first();
        $this->assertNotNull($permitKorban);
        $this->assertTrue((bool)$permitKorban->is_musim_korban);
        $this->assertEquals('Hari Raya Pertama', $permitKorban->hari_korban_percuma);
        $this->assertEquals(10, count($permitKorban->senarai_ternakan_list));
        $this->assertEquals(10.00, (float)$permitKorban->kadar_bayaran);

        // 8. Permohonan Musim Korban pada Hari Raya Keempat (2027-05-19) - Tiada pengecualian percuma (Wajib Berbayar Penuh)
        $itemsDay4 = [
            [
                'jantina' => 'J',
                'no_id_ternakan' => 'KORBAN-2027-D4',
                'no_siri_kad_pendaftaran' => 'DB-KB-2027-K01',
                'tarikh_sembelihan' => '2027-05-19',
                'hari_sembelihan_korban' => 'Hari Raya Keempat',
                'tempat_sembelihan' => 'Masjid Kota Bharu',
                'kuantiti_karkas' => '1 Ekor',
            ],
        ];

        $resitDay4 = \Illuminate\Http\UploadedFile::fake()->create('resit_day4.pdf', 150, 'application/pdf');
        $respKorbanDay4 = $this->actingAs($penternak)->post('/eptr/permit-sembelihan-borang-d/mohon', [
            'pemunya_id' => $pemunya->id,
            'jenis_ternakan' => 'Lembu',
            'tujuan_sembelih' => 'Ibadah Korban',
            'tarikh_sembelih' => '2027-05-19', // Hari Raya Keempat (13 Zulhijjah)
            'no_kenderaan' => 'DBC 9999',
            'resit_pembayaran' => $resitDay4,
            'items' => $itemsDay4,
        ]);
        $respKorbanDay4->assertRedirect();

        $permitDay4 = PermitSembelihan::whereDate('tarikh_sembelih', '2027-05-19')->latest('id')->first();
        $this->assertNotNull($permitDay4);
        $this->assertTrue((bool)$permitDay4->is_musim_korban);
        $this->assertNull($permitDay4->hari_korban_percuma); // Tiada hari percuma pada Hari Ke-4
        $this->assertEquals(1, count($permitDay4->senarai_ternakan_list));
        $this->assertEquals('Hari Raya Keempat', $permitDay4->senarai_ternakan_list[0]['hari_sembelihan_korban']);
    }

    public function test_dashboard_personal_data_scoping_for_public_and_staff_users()
    {
        // 1. Cipta Pengguna Penternak 1 & Datanya
        $user1 = User::factory()->create([
            'name' => 'Penternak Ahmad',
            'email' => 'ahmad_scope@example.com',
            'role' => 'penternak',
            'ic_number' => '850101038881',
            'phone' => '0198881111',
            'jajahan' => 'Kota Bharu',
        ]);

        $pemunya1 = \App\Models\Pemunya::create([
            'user_id' => $user1->id,
            'nama' => 'Penternak Ahmad',
            'no_kp' => '850101038881',
            'no_telefon' => '0198881111',
            'alamat' => 'Kampung Salor, Kota Bharu',
            'jajahan' => 'Kota Bharu',
            'daerah' => 'Salor',
        ]);

        $ternakan1 = Ternakan::create([
            'pemunya_id' => $pemunya1->id,
            'no_tag' => 'MY-KB-AHMAD-01',
            'jenis_ternakan' => 'Lembu',
            'baka' => 'Brahman',
            'jantina' => 'Betina',
            'tarikh_lahir' => '2022-01-01',
            'status' => 'Aktif',
            'status_kelulusan' => 'Diluluskan',
            'jajahan' => 'Kota Bharu',
        ]);

        $pawah1 = PawahPerjanjian::create([
            'user_id' => $user1->id,
            'no_perjanjian' => 'PAWAH/KB/AHMAD/01',
            'nama_program' => 'Skim Pawah Lembu Kelantan',
            'jajahan' => 'Kota Bharu',
            'bilangan_induk' => 2,
            'tarikh_mula' => now(),
            'tarikh_tamat' => now()->addYears(2),
            'status' => 'Aktif',
        ]);

        $farm1 = EpuLadang::create([
            'user_id' => $user1->id,
            'nama_pemohon_atau_syarikat' => 'Penternak Ahmad',
            'nama_ladang' => 'Ladang Ayam Ahmad',
            'jajahan' => 'Kota Bharu',
            'mukim' => 'Salor',
            'alamat_ladang' => 'Lot 101 Salor',
            'kapasiti_maksimum_unggas' => 5000,
            'status_ladang' => 'Aktif',
        ]);

        $course = \App\Models\Course::first();
        if (!$course) {
            $course = \App\Models\Course::create([
                'title' => 'Kursus Penternakan Ruminan Moden',
                'description' => 'Kursus pengurusan ternakan lembu dan kambing.',
                'start_date' => now()->addDays(10),
                'end_date' => now()->addDays(12),
                'location' => 'Pusat Latihan Veterinar',
                'capacity' => 30,
                'status' => 'Buka',
            ]);
        }

        $courseApp1 = \App\Models\CourseApplication::create([
            'course_id' => $course->id,
            'user_id' => $user1->id,
            'registration_number' => 'REG-AHMAD-01',
            'status' => 'Diluluskan',
        ]);

        $clinic1 = \App\Models\KlinikTemujanji::create([
            'user_id' => $user1->id,
            'no_temujanji' => 'KLN-AHMAD-01',
            'jenis_haiwan' => 'Lembu',
            'nama_haiwan' => 'Si Merah',
            'simptom_atau_tujuan' => 'Pemeriksaan Kesihatan & Vaksinasi',
            'tarikh_temujanji' => now()->addDays(3),
            'sesi' => 'Pagi (8:30 AM - 12:30 PM)',
            'klinik_jajahan' => 'Kota Bharu',
            'status' => 'Disahkan',
        ]);

        // 2. Cipta Pengguna Penternak 2 & Datanya
        $user2 = User::factory()->create([
            'name' => 'Penternak Bakar',
            'email' => 'bakar_scope@example.com',
            'role' => 'penternak',
            'ic_number' => '860202038882',
            'phone' => '0198882222',
            'jajahan' => 'Pasir Mas',
        ]);

        $pemunya2 = \App\Models\Pemunya::create([
            'user_id' => $user2->id,
            'nama' => 'Penternak Bakar',
            'no_kp' => '860202038882',
            'no_telefon' => '0198882222',
            'alamat' => 'Kampung Tendong, Pasir Mas',
            'jajahan' => 'Pasir Mas',
            'daerah' => 'Tendong',
        ]);

        $ternakan2 = Ternakan::create([
            'pemunya_id' => $pemunya2->id,
            'no_tag' => 'MY-PM-BAKAR-01',
            'jenis_ternakan' => 'Kambing',
            'baka' => 'Boer',
            'jantina' => 'Jantan',
            'tarikh_lahir' => '2023-01-01',
            'status' => 'Aktif',
            'status_kelulusan' => 'Diluluskan',
            'jajahan' => 'Pasir Mas',
        ]);

        // 3. Uji Dashboard untuk Penternak 1 (Hanya paparkan rekod milik Ahmad)
        $responseUser1 = $this->actingAs($user1)->get('/dashboard');
        $responseUser1->assertStatus(200);
        
        // Semak data view khusus untuk pengguna 1
        $this->assertEquals(1, $responseUser1->viewData('totalTernakanEptr'));
        $this->assertEquals(1, $responseUser1->viewData('totalPawahActive'));
        $this->assertEquals(1, $responseUser1->viewData('totalEpuFarms'));
        $this->assertEquals(1, $responseUser1->viewData('totalCourses'));
        $this->assertEquals(1, $responseUser1->viewData('totalClinicAppointments'));
        
        $myTernakan = $responseUser1->viewData('myTernakan');
        $this->assertCount(1, $myTernakan);
        $this->assertEquals('MY-KB-AHMAD-01', $myTernakan->first()->no_tag);

        $myPawah = $responseUser1->viewData('myPawah');
        $this->assertCount(1, $myPawah);
        $this->assertEquals('PAWAH/KB/AHMAD/01', $myPawah->first()->no_perjanjian);

        $myFarms = $responseUser1->viewData('myFarms');
        $this->assertCount(1, $myFarms);
        $this->assertEquals('Ladang Ayam Ahmad', $myFarms->first()->nama_ladang);

        $myCourses = $responseUser1->viewData('myCourses');
        $this->assertCount(1, $myCourses);
        $this->assertEquals('REG-AHMAD-01', $myCourses->first()->registration_number);

        $myClinicAppointments = $responseUser1->viewData('myClinicAppointments');
        $this->assertCount(1, $myClinicAppointments);
        $this->assertEquals('KLN-AHMAD-01', $myClinicAppointments->first()->no_temujanji);

        // 4. Uji Dashboard untuk Admin EPTR (Melihat data pentadbiran)
        $adminEptr = User::where('role', 'admin_eptr')->first();
        if ($adminEptr) {
            $responseAdmin = $this->actingAs($adminEptr)->get('/dashboard');
            $responseAdmin->assertStatus(200);
            $this->assertGreaterThanOrEqual(2, $responseAdmin->viewData('totalTernakanEptr'));
        }
    }

    public function test_login_using_ic_number_and_email()
    {
        $user = User::factory()->create([
            'name' => 'Pengguna Ujian IC',
            'email' => 'ujian.ic@example.com',
            'ic_number' => '950512035544',
            'password' => \Illuminate\Support\Facades\Hash::make('rahsia123'),
            'role' => 'penternak',
        ]);

        // 1. Log masuk guna No Kad Pengenalan tepat (tanpa sempang)
        $resp1 = $this->post('/login', [
            'ic_number' => '950512035544',
            'password' => 'rahsia123',
        ]);
        $resp1->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        \Illuminate\Support\Facades\Auth::logout();

        // 2. Log masuk guna No Kad Pengenalan dengan tanda sempang
        $resp2 = $this->post('/login', [
            'ic_number' => '950512-03-5544',
            'password' => 'rahsia123',
        ]);
        $resp2->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        \Illuminate\Support\Facades\Auth::logout();

        // 3. Log masuk guna Emel
        $resp3 = $this->post('/login', [
            'ic_number' => 'ujian.ic@example.com',
            'password' => 'rahsia123',
        ]);
        $resp3->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        \Illuminate\Support\Facades\Auth::logout();

        // 4. Log masuk gagal dengan kata laluan salah
        $respFail = $this->post('/login', [
            'ic_number' => '950512035544',
            'password' => 'salah',
        ]);
        $respFail->assertSessionHasErrors('ic_number');
        $this->assertGuest();
    }

    public function test_eptr_and_pawah_distribution_chart_visibility_restricted_to_admin_ibu_pejabat_and_admin_eptr()
    {
        $superAdmin = User::where('role', 'super_admin')->first();
        $adminEptr = User::where('role', 'admin_eptr')->first();
        $adminJajahan = User::where('role', 'admin_jajahan')->first();
        $penternak = User::where('role', 'penternak')->first();

        // 1. Super Admin (Admin Ibu Pejabat) BOLEH lihat carta taburan
        if ($superAdmin) {
            $respSuper = $this->actingAs($superAdmin)->get('/dashboard');
            $respSuper->assertStatus(200);
            $respSuper->assertSee('Taburan Ternakan EPTR & Program Pawah Mengikut Jajahan', false);
        }

        // 2. Admin EPTR Negeri BOLEH lihat carta taburan
        if ($adminEptr) {
            $respEptr = $this->actingAs($adminEptr)->get('/dashboard');
            $respEptr->assertStatus(200);
            $respEptr->assertSee('Taburan Ternakan EPTR & Program Pawah Mengikut Jajahan', false);
        }

        // 3. Admin Jajahan TIDAK BOLEH lihat carta taburan negeri ini
        if ($adminJajahan) {
            $respJajahan = $this->actingAs($adminJajahan)->get('/dashboard');
            $respJajahan->assertStatus(200);
            $respJajahan->assertDontSee('Taburan Ternakan EPTR & Program Pawah Mengikut Jajahan', false);
        }

        // 4. Penternak (Pengguna Awam) TIDAK BOLEH lihat carta taburan negeri ini
        if ($penternak) {
            $respPenternak = $this->actingAs($penternak)->get('/dashboard');
            $respPenternak->assertStatus(200);
            $respPenternak->assertDontSee('Taburan Ternakan EPTR & Program Pawah Mengikut Jajahan', false);
        }
    }

    public function test_pawah_contract_settlement_requires_receipt_upload_and_stores_correctly()
    {
        Storage::fake('public');

        $adminProgram = User::where('role', 'admin_program')->first();
        $penternak = User::where('role', 'penternak')->first();

        $perjanjian = PawahPerjanjian::create([
            'user_id' => $penternak->id,
            'no_perjanjian' => 'PW-TEST-RESIT-001',
            'nama_program' => 'Program Pawah Lembu Baka Kelantan',
            'tarikh_mula' => date('Y-m-d', strtotime('-3 years')),
            'tarikh_tamat' => date('Y-m-d'),
            'tempoh_tahun' => 3,
            'bilangan_induk' => 1,
            'jajahan' => 'Pasir Puteh',
            'status' => 'Sedang Berjalan',
        ]);

        // 1. Cuba hantar penyelesaian tanpa resit pembayaran -> Mesti gagal validasi
        $responseFail = $this->actingAs($adminProgram)->post("/pawah/perjanjian/{$perjanjian->id}/penyelesaian", [
            'tarikh_penyelesaian' => date('Y-m-d'),
            'bilangan_anak_dipulangkan' => 1,
            'status_penyelesaian' => 'Selesai Penuh',
            'jumlah_bayaran_tebus_guna' => 0.00,
            'perakuan' => 'Peserta telah pulangkan anak ternakan.',
        ]);
        $responseFail->assertSessionHasErrors('resit_pembayaran');

        // 2. Hantar penyelesaian dengan fail resit sah
        $file = UploadedFile::fake()->create('resit_penyelesaian_pawah.pdf', 300, 'application/pdf');
        $responseSuccess = $this->actingAs($adminProgram)->post("/pawah/perjanjian/{$perjanjian->id}/penyelesaian", [
            'tarikh_penyelesaian' => date('Y-m-d'),
            'bilangan_anak_dipulangkan' => 1,
            'status_penyelesaian' => 'Selesai Penuh',
            'jumlah_bayaran_tebus_guna' => 0.00,
            'perakuan' => 'Peserta telah pulangkan anak ternakan dengan jayanya.',
            'resit_pembayaran' => $file,
        ]);
        $responseSuccess->assertSessionHas('success');

        $perjanjian->refresh();
        $this->assertEquals('Selesai', $perjanjian->status);
        $this->assertNotNull($perjanjian->penyelesaian);
        $this->assertNotNull($perjanjian->penyelesaian->resit_pembayaran);

        // Semak fail disimpan dalam storage
        Storage::disk('public')->assertExists($perjanjian->penyelesaian->resit_pembayaran);

        // Semak paparan di halaman show pawah mengandungi resit bayaran
        $responseView = $this->actingAs($adminProgram)->get("/pawah/perjanjian/{$perjanjian->id}");
        $responseView->assertStatus(200);
        $responseView->assertSee('Resit Bayaran Penyelesaian Disertakan:');
        $responseView->assertSee('Lihat / Muat Turun Resit');
    }

    public function test_surat_perjanjian_lembu_pawah_print_template_renders_official_4_pages_and_all_clauses()
    {
        $adminProgram = User::where('role', 'admin_program')->first();
        $penternak = User::where('role', 'penternak')->first();

        $perjanjian = PawahPerjanjian::create([
            'user_id' => $penternak->id,
            'no_perjanjian' => 'PW-CETAK-TEST-001',
            'nama_program' => 'Program Pawah Ternakan Negeri Kelantan',
            'jenis_pawah' => 'Lembu Hibrid',
            'tarikh_mula' => '2026-01-15',
            'tarikh_tamat' => '2029-01-15',
            'tempoh_tahun' => 3,
            'bilangan_induk' => 1,
            'syarat_pemulangan' => 'Memulangkan 1 ekor anak betina pertama berumur 1 tahun ke atas.',
            'jajahan' => 'Kota Bharu',
            'status' => 'Aktif',
            'pegawai_penyelia' => 'Dr. Mohd Faizal (Pegawai Veterinar Jajahan Kota Bharu)',
        ]);

        $response = $this->actingAs($adminProgram)->get("/pawah/perjanjian/{$perjanjian->id}/cetak");
        $response->assertStatus(200);

        // Semak Tajuk Rasmi & Header
        $response->assertSee('JABATAN PERKHIDMATAN VETERINAR');
        $response->assertSee('NEGERI KELANTAN');
        $response->assertSee('SURAT PERJANJIAN LEMBU PAWAH');

        // Semak Logo Rasmi (Jata Negara & Jata Kelantan)
        $response->assertSee('jata-negara.svg');
        $response->assertSee('jata-kelantan.png');

        // Semak Maklumat Peserta & Jajahan
        $response->assertSee($penternak->name);
        $response->assertSee('Jajahan Kota Bharu');

        // Semak Fasal-Fasal Rasmi (Fasal 1 hingga Fasal 26)
        $response->assertSee('(1)');
        $response->assertSee('(5)');
        $response->assertSee('(6)');
        $response->assertSee('(11)');
        $response->assertSee('(13)');
        $response->assertSee('(14)');
        $response->assertSee('(21)');
        $response->assertSee('(22)');
        $response->assertSee('(26)');
        $response->assertSee('Sodium Arsenita');

        // Semak Ruangan Tandatangan & Saksi
        $response->assertSee('DITANDATANGANI OLEH');
        $response->assertSee('(BAGI PIHAK KERAJAAN MALAYSIA/NEGERI KELANTAN)');
        $response->assertSee('(SEBAGAI SAKSI)');
        $response->assertSee('(SEBAGAI PEMAWAH)');
    }

    public function test_kesihatan_index_and_booster_alert_is_scoped_to_current_user_only()
    {
        $penternak1 = User::factory()->create([
            'role' => 'penternak',
            'name' => 'Penternak Ahmad',
            'ic_number' => '880101031111',
        ]);
        $pemunya1 = Pemunya::create([
            'user_id' => $penternak1->id,
            'nama' => 'Penternak Ahmad',
            'no_kp' => '880101031111',
            'no_telefon' => '0191111111',
            'alamat' => 'Kg Pasir Hor',
            'jajahan' => 'Kota Bharu',
            'status' => 'Aktif',
        ]);
        $ternakan1 = Ternakan::create([
            'pemunya_id' => $pemunya1->id,
            'no_tag' => 'MY-KB-TEST-001',
            'jenis_ternakan' => 'Lembu',
            'baka' => 'Brahman',
            'jantina' => 'Betina',
            'status' => 'Aktif',
            'status_kelulusan' => 'Diluluskan',
            'jajahan' => 'Kota Bharu',
            'tarikh_lahir' => '2023-01-01',
            'tujuan_ternakan' => 'Pembiakan',
        ]);

        $penternak2 = User::factory()->create([
            'role' => 'penternak',
            'name' => 'Penternak Bakar',
            'ic_number' => '890202032222',
        ]);
        $pemunya2 = Pemunya::create([
            'user_id' => $penternak2->id,
            'nama' => 'Penternak Bakar',
            'no_kp' => '890202032222',
            'no_telefon' => '0192222222',
            'alamat' => 'Kg Tendong',
            'jajahan' => 'Pasir Mas',
            'status' => 'Aktif',
        ]);
        $ternakan2 = Ternakan::create([
            'pemunya_id' => $pemunya2->id,
            'no_tag' => 'MY-PM-TEST-002',
            'jenis_ternakan' => 'Lembu',
            'baka' => 'Charolais',
            'jantina' => 'Jantan',
            'status' => 'Aktif',
            'status_kelulusan' => 'Diluluskan',
            'jajahan' => 'Pasir Mas',
            'tarikh_lahir' => '2023-05-01',
            'tujuan_ternakan' => 'Penggemukan',
        ]);

        // Rekod Kesihatan Penternak 1
        \App\Models\ProgramKesihatan::create([
            'ternakan_id' => $ternakan1->id,
            'no_rujukan_kesihatan' => 'MED-KB-2026-0001',
            'jenis_program' => 'Vaksinasi / Imunisasi',
            'nama_vaksin_atau_ubat' => 'Vaksin FMD Penternak 1',
            'tarikh_rawatan' => date('Y-m-d'),
            'tarikh_ulangan_dos' => date('Y-m-d', strtotime('+14 days')),
            'status_kesihatan' => 'Sihat & Cergas',
            'pegawai_pemeriksa' => 'Dr. Farhan',
            'jajahan' => 'Kota Bharu',
            'didaftar_oleh' => $penternak1->id,
        ]);

        // Rekod Kesihatan Penternak 2
        \App\Models\ProgramKesihatan::create([
            'ternakan_id' => $ternakan2->id,
            'no_rujukan_kesihatan' => 'MED-PM-2026-0002',
            'jenis_program' => 'Penyahcacingan (Deworming)',
            'nama_vaksin_atau_ubat' => 'Deworming Penternak 2',
            'tarikh_rawatan' => date('Y-m-d'),
            'tarikh_ulangan_dos' => date('Y-m-d', strtotime('+20 days')),
            'status_kesihatan' => 'Sihat & Cergas',
            'pegawai_pemeriksa' => 'Dr. Suzana',
            'jajahan' => 'Pasir Mas',
            'didaftar_oleh' => $penternak2->id,
        ]);

        // 1. Penternak 1 buka halaman kesihatan -> Hanya lihat ternakan & booster miliknya
        $respP1 = $this->actingAs($penternak1)->get('/eptr/program-kesihatan');
        $respP1->assertStatus(200);
        $respP1->assertSee('MY-KB-TEST-001');
        $respP1->assertSee('Vaksin FMD Penternak 1');
        $respP1->assertDontSee('MY-PM-TEST-002');
        $respP1->assertDontSee('Deworming Penternak 2');

        // 2. Penternak 2 buka halaman kesihatan -> Hanya lihat ternakan & booster miliknya
        $respP2 = $this->actingAs($penternak2)->get('/eptr/program-kesihatan');
        $respP2->assertStatus(200);
        $respP2->assertSee('MY-PM-TEST-002');
        $respP2->assertSee('Deworming Penternak 2');
        $respP2->assertDontSee('MY-KB-TEST-001');
        $respP2->assertDontSee('Vaksin FMD Penternak 1');
    }

    public function test_eptr_index_statistics_and_listing_are_scoped_to_current_user()
    {
        $penternakA = User::factory()->create([
            'role' => 'penternak',
            'name' => 'Penternak User A',
            'ic_number' => '900101031111',
            'email' => 'penternak_a@test.com',
        ]);
        $pemunyaA = Pemunya::create([
            'user_id' => $penternakA->id,
            'nama' => 'Penternak User A',
            'no_kp' => '900101031111',
            'no_telefon' => '0111111111',
            'alamat' => 'Kampung Kota Bharu',
            'jajahan' => 'Kota Bharu',
            'status' => 'Aktif',
        ]);

        $penternakB = User::factory()->create([
            'role' => 'penternak',
            'name' => 'Penternak User B',
            'ic_number' => '900101032222',
            'email' => 'penternak_b@test.com',
        ]);
        $pemunyaB = Pemunya::create([
            'user_id' => $penternakB->id,
            'nama' => 'Penternak User B',
            'no_kp' => '900101032222',
            'no_telefon' => '0122222222',
            'alamat' => 'Kampung Pasir Mas',
            'jajahan' => 'Pasir Mas',
            'status' => 'Aktif',
        ]);

        // Ternakan A: 2 Lembu, 1 Kambing, 1 Program Pawah
        Ternakan::create([
            'pemunya_id' => $pemunyaA->id,
            'no_tag' => 'TAG-A-01',
            'jenis_ternakan' => 'Lembu',
            'baka' => 'Brahman',
            'jantina' => 'Betina',
            'program' => 'Program Pawah Ternakan',
            'status' => 'Aktif',
            'status_kelulusan' => 'Diluluskan',
            'jajahan' => 'Kota Bharu',
        ]);
        Ternakan::create([
            'pemunya_id' => $pemunyaA->id,
            'no_tag' => 'TAG-A-02',
            'jenis_ternakan' => 'Lembu',
            'baka' => 'Kedah-Kelantan',
            'jantina' => 'Jantan',
            'program' => 'Tiada',
            'status' => 'Aktif',
            'status_kelulusan' => 'Diluluskan',
            'jajahan' => 'Kota Bharu',
        ]);
        Ternakan::create([
            'pemunya_id' => $pemunyaA->id,
            'no_tag' => 'TAG-A-03',
            'jenis_ternakan' => 'Kambing',
            'baka' => 'Boer',
            'jantina' => 'Betina',
            'program' => 'Tiada',
            'status' => 'Aktif',
            'status_kelulusan' => 'Diluluskan',
            'jajahan' => 'Kota Bharu',
        ]);

        // Ternakan B: 3 Kerbau, 2 Biri-biri
        Ternakan::create([
            'pemunya_id' => $pemunyaB->id,
            'no_tag' => 'TAG-B-01',
            'jenis_ternakan' => 'Kerbau',
            'baka' => 'Kerbau Sawah',
            'jantina' => 'Betina',
            'status' => 'Aktif',
            'status_kelulusan' => 'Diluluskan',
            'jajahan' => 'Pasir Mas',
        ]);
        Ternakan::create([
            'pemunya_id' => $pemunyaB->id,
            'no_tag' => 'TAG-B-02',
            'jenis_ternakan' => 'Biri-biri',
            'baka' => 'Dorper',
            'jantina' => 'Jantan',
            'status' => 'Aktif',
            'status_kelulusan' => 'Diluluskan',
            'jajahan' => 'Pasir Mas',
        ]);

        // Penternak A buka senarai ternakan
        $respA = $this->actingAs($penternakA)->get(route('eptr.index'));
        $respA->assertStatus(200);
        $respA->assertViewHas('totalCows', 2);
        $respA->assertViewHas('totalGoats', 1);
        $respA->assertViewHas('totalBuffalo', 0);
        $respA->assertViewHas('totalSheep', 0);
        $respA->assertViewHas('totalWithProgram', 1);
        $respA->assertSee('TAG-A-01');
        $respA->assertSee('TAG-A-02');
        $respA->assertSee('TAG-A-03');
        $respA->assertDontSee('TAG-B-01');
        $respA->assertDontSee('TAG-B-02');

        // Penternak B buka senarai ternakan
        $respB = $this->actingAs($penternakB)->get(route('eptr.index'));
        $respB->assertStatus(200);
        $respB->assertViewHas('totalCows', 0);
        $respB->assertViewHas('totalGoats', 0);
        $respB->assertViewHas('totalBuffalo', 1);
        $respB->assertViewHas('totalSheep', 1);
        $respB->assertViewHas('totalWithProgram', 0);
        $respB->assertSee('TAG-B-01');
        $respB->assertSee('TAG-B-02');
        $respB->assertDontSee('TAG-A-01');
        $respB->assertDontSee('TAG-A-02');
        $respB->assertDontSee('TAG-A-03');
    }

    public function test_pawah_cattle_counted_in_livestock_type_totals_and_program_totals()
    {
        $penternak = User::factory()->create([
            'role' => 'penternak',
            'name' => 'Peserta Pawah User',
            'ic_number' => '850505035555',
            'email' => 'peserta_pawah@test.com',
        ]);
        $pemunya = Pemunya::create([
            'user_id' => $penternak->id,
            'nama' => 'Peserta Pawah User',
            'no_kp' => '850505035555',
            'no_telefon' => '0199999999',
            'alamat' => 'Kampung Pawah',
            'jajahan' => 'Kota Bharu',
            'status' => 'Aktif',
        ]);

        $pemunyaJpvnk = Pemunya::firstOrCreate(
            ['no_kp' => 'JPVNK-PAWAH-HOLDINGS'],
            [
                'nama' => 'Jabatan Perkhidmatan Veterinar Negeri Kelantan (JPVNK)',
                'no_telefon' => '09-7482344',
                'alamat' => 'Ibu Pejabat JPVNK, Kubang Kerian',
                'jajahan' => 'Kota Bharu',
                'status' => 'Aktif',
            ]
        );

        // 1. Ternakan persendirian penternak: 1 Lembu, 1 Kambing
        $lembuPersendirian = Ternakan::create([
            'pemunya_id' => $pemunya->id,
            'no_tag' => 'TAG-MY-COW',
            'jenis_ternakan' => 'Lembu',
            'baka' => 'Brahman',
            'jantina' => 'Betina',
            'program' => 'Tiada',
            'status' => 'Aktif',
            'status_kelulusan' => 'Diluluskan',
            'jajahan' => 'Kota Bharu',
        ]);
        $kambingPersendirian = Ternakan::create([
            'pemunya_id' => $pemunya->id,
            'no_tag' => 'TAG-MY-GOAT',
            'jenis_ternakan' => 'Kambing',
            'baka' => 'Boer',
            'jantina' => 'Betina',
            'program' => 'Tiada',
            'status' => 'Aktif',
            'status_kelulusan' => 'Diluluskan',
            'jajahan' => 'Kota Bharu',
        ]);

        // 2. Ternakan Pawah (dimiliki JPVNK tetapi dipautkan kepada peserta melalui PawahPerjanjian): 2 Lembu Pawah & 1 Kambing Pawah
        $lembuPawah1 = Ternakan::create([
            'pemunya_id' => $pemunyaJpvnk->id,
            'no_tag' => 'TAG-PAWAH-COW-1',
            'jenis_ternakan' => 'Lembu',
            'baka' => 'Charolais Hibrid',
            'jantina' => 'Betina',
            'program' => 'Program Pawah Ternakan Negeri Kelantan',
            'status' => 'Pawah',
            'status_kelulusan' => 'Diluluskan',
            'jajahan' => 'Kota Bharu',
        ]);
        $lembuPawah2 = Ternakan::create([
            'pemunya_id' => $pemunyaJpvnk->id,
            'no_tag' => 'TAG-PAWAH-COW-2',
            'jenis_ternakan' => 'Lembu',
            'baka' => 'Limousin Hibrid',
            'jantina' => 'Betina',
            'program' => 'Program Pawah Ternakan Negeri Kelantan',
            'status' => 'Pawah',
            'status_kelulusan' => 'Diluluskan',
            'jajahan' => 'Kota Bharu',
        ]);
        $kambingPawah = Ternakan::create([
            'pemunya_id' => $pemunyaJpvnk->id,
            'no_tag' => 'TAG-PAWAH-GOAT-1',
            'jenis_ternakan' => 'Kambing',
            'baka' => 'Boer',
            'jantina' => 'Betina',
            'program' => 'Program Pawah Ternakan Negeri Kelantan',
            'status' => 'Pawah',
            'status_kelulusan' => 'Diluluskan',
            'jajahan' => 'Kota Bharu',
        ]);

        // Cipta Perjanjian Pawah untuk peserta
        $perjanjian = \App\Models\PawahPerjanjian::create([
            'user_id' => $penternak->id,
            'no_perjanjian' => 'PW-TEST-2026-99',
            'nama_program' => 'Program Pawah Ternakan Negeri Kelantan',
            'jenis_pawah' => 'Lembu Hibrid',
            'tarikh_mula' => date('Y-m-d'),
            'tarikh_tamat' => date('Y-m-d', strtotime('+3 years')),
            'tempoh_tahun' => 3,
            'bilangan_induk' => 3,
            'syarat_pemulangan' => 'Pulang 1 ekor anak',
            'jajahan' => 'Kota Bharu',
            'status' => 'Aktif',
            'pegawai_penyelia' => 'Dr. Farhan',
        ]);

        \App\Models\PawahTernakan::create([
            'pawah_perjanjian_id' => $perjanjian->id,
            'ternakan_id' => $lembuPawah1->id,
            'status_induk' => 'Aktif',
            'tarikh_serahan' => date('Y-m-d'),
        ]);
        \App\Models\PawahTernakan::create([
            'pawah_perjanjian_id' => $perjanjian->id,
            'ternakan_id' => $lembuPawah2->id,
            'status_induk' => 'Aktif',
            'tarikh_serahan' => date('Y-m-d'),
        ]);
        \App\Models\PawahTernakan::create([
            'pawah_perjanjian_id' => $perjanjian->id,
            'ternakan_id' => $kambingPawah->id,
            'status_induk' => 'Aktif',
            'tarikh_serahan' => date('Y-m-d'),
        ]);

        // Semak di EPTR Index bagi penternak:
        // Jumlah Lembu = 1 (sendiri) + 2 (pawah) = 3
        // Jumlah Kambing = 1 (sendiri) + 1 (pawah) = 2
        // Program Pawah / Bantuan = 3
        $resp = $this->actingAs($penternak)->get(route('eptr.index'));
        $resp->assertStatus(200);
        $resp->assertViewHas('totalCows', 3);
        $resp->assertViewHas('totalGoats', 2);
        $resp->assertViewHas('totalBuffalo', 0);
        $resp->assertViewHas('totalSheep', 0);
        $resp->assertViewHas('totalWithProgram', 3);

        $resp->assertSee('TAG-MY-COW');
        $resp->assertSee('TAG-MY-GOAT');
        $resp->assertSee('TAG-PAWAH-COW-1');
        $resp->assertSee('TAG-PAWAH-COW-2');
        $resp->assertSee('TAG-PAWAH-GOAT-1');

        // Semak juga di Dashboard Utama
        $respDash = $this->actingAs($penternak)->get('/dashboard');
        $respDash->assertStatus(200);
        $respDash->assertViewHas('totalTernakanEptr', 5); // 2 sendiri + 3 pawah
        $respDash->assertViewHas('totalTernakanPawah', 3);
    }

    public function test_eptr_borang_a_induk_selection_and_auto_linking()
    {
        Storage::fake('public');
        $penternak = User::where('role', 'penternak')->first();
        $admin = User::where('role', 'admin_eptr')->first();

        // 1. Cipta ternakan Induk yang telah diluluskan
        $induk = Ternakan::create([
            'pemunya_id' => $penternak->pemunya ? $penternak->pemunya->id : Pemunya::create([
                'user_id' => $penternak->id,
                'nama' => 'Penternak Induk',
                'no_kp' => '900101035544',
                'no_telefon' => '0123456789',
                'jajahan' => 'Kota Bharu',
                'status' => 'Aktif',
            ])->id,
            'no_tag' => 'KB-INDUK-9999',
            'jenis_ternakan' => 'lembu',
            'baka' => 'kedah-kelantan',
            'jantina' => 'Betina',
            'tarikh_lahir' => '2022-01-01',
            'status' => 'Aktif',
            'status_kelulusan' => 'Diluluskan',
            'jajahan' => 'Kota Bharu',
            'daerah' => 'Peringat',
            'tujuan_ternakan' => 'Pembiakan',
        ]);

        // 2. Semak view Borang A memuatkan indukList
        $createResponse = $this->actingAs($penternak)->get(route('eptr.create'));
        $createResponse->assertStatus(200);
        $createResponse->assertViewHas('indukList');
        $createResponse->assertSee('KB-INDUK-9999');

        // 3. Daftar anak dengan memasukkan / memilih No Tag Induk
        $file = UploadedFile::fake()->create('resit_anak.pdf', 200, 'application/pdf');
        $storeResponse = $this->actingAs($penternak)->post(route('eptr.store'), [
            'nama_pemunya' => $penternak->name,
            'no_kp_pemunya' => '900101035544',
            'no_tel_pemunya' => '0123456789',
            'jajahan' => 'Kota Bharu',
            'daerah' => 'Peringat',
            'alamat_pemunya' => 'Kg Pasir Hor',
            'jenis_ternakan' => 'lembu',
            'baka' => 'kedah-kelantan',
            'baka_pejantan' => 'charolais',
            'baka_induk' => 'kedah-kelantan',
            'no_tanda_pengenalan_induk' => 'KB-INDUK-9999',
            'jantina' => 'Jantan',
            'tarikh_lahir' => Carbon::yesterday()->toDateString(),
            'tujuan_ternakan' => 'Pedaging',
            'resit_pembayaran' => $file,
        ]);

        $storeResponse->assertRedirect();
        
        $anak = Ternakan::where('no_tanda_pengenalan_induk', 'KB-INDUK-9999')->latest('id')->first();
        $this->assertNotNull($anak);
        $this->assertEquals('kedah-kelantan', $anak->baka_induk);
        $this->assertEquals('KB-INDUK-9999', $anak->no_tanda_pengenalan_induk);

        // Semak RekodKelahiran dicipta dan dipautkan secara automatik
        $rekodKelahiran = \App\Models\RekodKelahiran::where('induk_id', $induk->id)
            ->where('anak_ternakan_id', $anak->id)
            ->first();
        $this->assertNotNull($rekodKelahiran);
        $this->assertEquals('Hidup', $rekodKelahiran->status_kelahiran);

        // 4. Luluskan anak ternakan dan semak no_tag_sementara dikemaskini
        $lulusResponse = $this->actingAs($admin)->post(route('eptr.lulus', $anak->id));
        $lulusResponse->assertRedirect();

        $anak->refresh();
        $rekodKelahiran->refresh();
        $this->assertNotNull($anak->no_tag);
        $this->assertEquals($anak->no_tag, $rekodKelahiran->no_tag_sementara);
    }

    public function test_super_admin_user_management_module()
    {
        $superAdmin = User::where('role', 'super_admin')->first();
        $penternak = User::where('role', 'penternak')->first();

        // 1. Non-super admin dihalang daripada mengakses /pengguna
        $forbiddenResponse = $this->actingAs($penternak)->get(route('users.index'));
        $forbiddenResponse->assertStatus(403);

        $forbiddenCreateResponse = $this->actingAs($penternak)->get(route('users.create'));
        $forbiddenCreateResponse->assertStatus(403);

        // 2. Super Admin boleh melihat senarai pengguna & borang pendaftaran pengguna
        $indexResponse = $this->actingAs($superAdmin)->get(route('users.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Pengurusan Pengguna');

        $createResponse = $this->actingAs($superAdmin)->get(route('users.create'));
        $createResponse->assertStatus(200);
        $createResponse->assertSee('Daftar Pengguna Baharu');

        // 3. Super Admin menambah pengguna baharu (contoh: Pegawai Jajahan Pasir Mas)
        $storeResponse = $this->actingAs($superAdmin)->post(route('users.store'), [
            'name' => 'Pegawai Jajahan Pasir Mas',
            'email' => 'admin.pasirmas@jpvnk.test',
            'ic_number' => '880101037788',
            'phone' => '0198889900',
            'address' => 'Pejabat Veterinar Jajahan Pasir Mas',
            'jajahan' => 'Pasir Mas',
            'role' => 'admin_jajahan',
            'status' => 'Aktif',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $storeResponse->assertRedirect(route('users.index'));
        $newUser = User::where('email', 'admin.pasirmas@jpvnk.test')->first();
        $this->assertNotNull($newUser);
        $this->assertEquals('880101037788', $newUser->ic_number);
        $this->assertEquals('admin_jajahan', $newUser->role);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('password123', $newUser->password));

        // 4. Super Admin menambah pengguna penternak -> Profil Pemunya auto-dicipta
        $storePenternakResponse = $this->actingAs($superAdmin)->post(route('users.store'), [
            'name' => 'Penternak Baharu Ditambah Admin',
            'email' => 'penternak.admin@jpvnk.test',
            'ic_number' => '950505031122',
            'phone' => '01122334455',
            'address' => 'Kg Kok Lanas, Kota Bharu',
            'jajahan' => 'Kota Bharu',
            'role' => 'penternak',
            'status' => 'Aktif',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $storePenternakResponse->assertRedirect(route('users.index'));
        $newPenternak = User::where('email', 'penternak.admin@jpvnk.test')->first();
        $this->assertNotNull($newPenternak);
        $this->assertNotNull($newPenternak->pemunya);
        $this->assertEquals('950505031122', $newPenternak->pemunya->no_kp);

        // 5. Super Admin mengemaskini maklumat pengguna
        $updateResponse = $this->actingAs($superAdmin)->put(route('users.update', $newUser->id), [
            'name' => 'Pegawai Jajahan Pasir Mas (Terkini)',
            'email' => 'admin.pasirmas@jpvnk.test',
            'ic_number' => '880101037788',
            'phone' => '0198889911',
            'address' => 'Pejabat Veterinar Jajahan Pasir Mas Baru',
            'jajahan' => 'Pasir Mas',
            'role' => 'admin_jajahan',
            'status' => 'Aktif',
        ]);

        $updateResponse->assertRedirect(route('users.index'));
        $newUser->refresh();
        $this->assertEquals('Pegawai Jajahan Pasir Mas (Terkini)', $newUser->name);
        $this->assertEquals('0198889911', $newUser->phone);

        // 6. Super Admin menukar status pengguna (toggle status)
        $toggleResponse = $this->actingAs($superAdmin)->post(route('users.toggle-status', $newUser->id));
        $toggleResponse->assertRedirect();
        $newUser->refresh();
        $this->assertEquals('Tidak Aktif', $newUser->status);

        // 7. Super Admin memadam pengguna
        $deleteResponse = $this->actingAs($superAdmin)->delete(route('users.destroy', $newUser->id));
        $deleteResponse->assertRedirect(route('users.index'));
        $this->assertNull(User::find($newUser->id));

        // 8. Super Admin dihalang memadam atau menyahaktifkan akaun sendiri
        $selfDeleteResponse = $this->actingAs($superAdmin)->delete(route('users.destroy', $superAdmin->id));
        $selfDeleteResponse->assertSessionHas('error');
        $this->assertNotNull(User::find($superAdmin->id));
    }

    public function test_user_profile_page_and_updates()
    {
        $penternak = User::where('role', 'penternak')->first();

        // 1. Pengguna log masuk boleh membuka halaman Profil
        $profileResponse = $this->actingAs($penternak)->get(route('profile.show'));
        $profileResponse->assertStatus(200);
        $profileResponse->assertSee($penternak->name);
        $profileResponse->assertSee('Profil & Tetapan Akaun Pengguna');

        // 2. Pengguna mengemaskini maklumat profil peribadi
        $updateResponse = $this->actingAs($penternak)->put(route('profile.update'), [
            'name' => 'Ahmad Penternak Dikemaskini',
            'email' => $penternak->email,
            'phone' => '0199998888',
            'address' => 'Kampung Baru Kota Bharu',
            'jajahan' => 'Kota Bharu',
        ]);

        $updateResponse->assertRedirect();
        $penternak->refresh();
        $this->assertEquals('Ahmad Penternak Dikemaskini', $penternak->name);
        $this->assertEquals('0199998888', $penternak->phone);
        $this->assertEquals('Kampung Baru Kota Bharu', $penternak->address);

        // Semak profil Pemunya turut dikemaskini
        if ($penternak->pemunya) {
            $this->assertEquals('Ahmad Penternak Dikemaskini', $penternak->pemunya->nama);
            $this->assertEquals('0199998888', $penternak->pemunya->no_telefon);
        }

        // 3. Pengguna menukar kata laluan
        $passwordResponse = $this->actingAs($penternak)->put(route('profile.password'), [
            'current_password' => 'password',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $passwordResponse->assertRedirect();
        $penternak->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newpassword123', $penternak->password));

        // 4. Semak ralat jika kata laluan semasa salah
        $wrongPasswordResponse = $this->actingAs($penternak)->put(route('profile.password'), [
            'current_password' => 'salah_password',
            'password' => 'anotherpassword123',
            'password_confirmation' => 'anotherpassword123',
        ]);

        $wrongPasswordResponse->assertSessionHasErrors('current_password');
    }

    public function test_automatic_default_password_generation_on_registration()
    {
        // 1. Super Admin mendaftar pengguna tanpa menetapkan kata laluan
        $superAdmin = User::where('role', 'super_admin')->first();
        $responseAdminCreate = $this->actingAs($superAdmin)->post(route('users.store'), [
            'name' => 'Kakitangan Baharu JPVNK',
            'email' => 'kakitangan.baru@dvs.gov.my',
            'ic_number' => '920815-03-8899',
            'phone' => '01122334455',
            'jajahan' => 'Kota Bharu',
            'address' => 'Pejabat JPVNK Kota Bharu',
            'role' => 'staf',
            'status' => 'Aktif',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $responseAdminCreate->assertRedirect(route('users.index'));
        $newUser = User::where('email', 'kakitangan.baru@dvs.gov.my')->first();
        $this->assertNotNull($newUser);
        $this->assertEquals('920815038899', $newUser->ic_number);
        // Semak kata laluan lalai: super@DVS8899
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('super@DVS8899', $newUser->password));

        // Logout superAdmin terlebih dahulu
        $this->post(route('logout'));

        // Semak pengguna boleh log masuk menggunakan IC dan kata laluan lalai
        $loginResponse = $this->post(route('login'), [
            'ic_number' => '920815038899',
            'password' => 'super@DVS8899',
        ]);
        $loginResponse->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($newUser);

        // Logout
        $this->post(route('logout'));
        $this->assertGuest();

        // 2. Pendaftaran Pengguna Awam (/register) tanpa kata laluan
        $responsePublicRegister = $this->post(route('register'), [
            'name' => 'Penternak Baharu Awam',
            'email' => 'penternak.awam@gmail.com',
            'ic_number' => '880102-03-1234',
            'phone' => '0198765432',
            'jajahan' => 'Pasir Mas',
            'address' => 'Kg Pasir Pekan',
            'role' => 'penternak',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $responsePublicRegister->assertRedirect('/dashboard');
        $newPenternak = User::where('email', 'penternak.awam@gmail.com')->first();
        $this->assertNotNull($newPenternak);
        $this->assertEquals('880102031234', $newPenternak->ic_number);
        // Semak kata laluan lalai: super@DVS1234
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('super@DVS1234', $newPenternak->password));
    }

    public function test_bahagian_d_program_bantuan_hidden_for_public_penternak_usahawan_in_borang_a()
    {
        $penternak = User::where('role', 'penternak')->first();
        $orangAwam = User::where('role', 'orang_awam')->first();
        $usahawan = User::where('role', 'usahawan')->first();
        $superAdmin = User::where('role', 'super_admin')->first();

        // 1. Penternak buka Borang A -> Bahagian D tidak dipaparkan langsung
        $respPenternak = $this->actingAs($penternak)->get(route('eptr.create'));
        $respPenternak->assertStatus(200);
        $respPenternak->assertDontSee('Bahagian D: Kolum Program Bantuan');
        $respPenternak->assertDontSee('Penetapan Pegawai JPVNK Sahaja');

        // 2. Orang Awam buka Borang A -> Bahagian D tidak dipaparkan langsung
        $respOrangAwam = $this->actingAs($orangAwam)->get(route('eptr.create'));
        $respOrangAwam->assertStatus(200);
        $respOrangAwam->assertDontSee('Bahagian D: Kolum Program Bantuan');
        $respOrangAwam->assertDontSee('Penetapan Pegawai JPVNK Sahaja');

        // 3. Usahawan buka Borang A -> Bahagian D tidak dipaparkan langsung
        $respUsahawan = $this->actingAs($usahawan)->get(route('eptr.create'));
        $respUsahawan->assertStatus(200);
        $respUsahawan->assertDontSee('Bahagian D: Kolum Program Bantuan');
        $respUsahawan->assertDontSee('Penetapan Pegawai JPVNK Sahaja');

        // 4. Super Admin buka Borang A -> Bahagian D dipaparkan dengan pilihan Program
        $respSuperAdmin = $this->actingAs($superAdmin)->get(route('eptr.create'));
        $respSuperAdmin->assertStatus(200);
        $respSuperAdmin->assertSee('Bahagian D: Kolum Program Bantuan (Pilihan)');
        $respSuperAdmin->assertSee('Akses Pegawai JPVNK');
    }

    public function test_user_activity_notifications_system()
    {
        $penternak = User::where('role', 'penternak')->first();

        // 1. Uji penjana notifikasi apabila profil dikemaskini
        $this->actingAs($penternak)->put(route('profile.update'), [
            'name' => 'Ahmad Penternak Notif',
            'email' => $penternak->email,
            'phone' => '0191112233',
            'address' => 'Kg Notifikasi',
            'jajahan' => 'Kota Bharu',
        ]);

        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $penternak->id,
            'type' => 'profil',
            'title' => 'Profil Dikemaskini',
        ]);

        $this->assertEquals(1, $penternak->unreadNotificationsCount());

        // 2. Uji kemaskini kata laluan menjana notifikasi
        $this->actingAs($penternak)->put(route('profile.password'), [
            'current_password' => 'password',
            'password' => 'newnotifpass123',
            'password_confirmation' => 'newnotifpass123',
        ]);

        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $penternak->id,
            'type' => 'profil',
            'title' => 'Kata Laluan Ditukar',
        ]);

        $this->assertEquals(2, $penternak->unreadNotificationsCount());

        // 3. Uji endpoint API /notifikasi/feed (digunakan oleh dropdown loceng)
        $feedResp = $this->actingAs($penternak)->get(route('notifications.feed'));
        $feedResp->assertStatus(200);
        $feedResp->assertJsonPath('unread_count', 2);
        $feedData = $feedResp->json('notifications');
        $this->assertCount(2, $feedData);

        // 4. Uji paparan Pusat Notifikasi (/notifikasi)
        $indexResp = $this->actingAs($penternak)->get(route('notifications.index'));
        $indexResp->assertStatus(200);
        $indexResp->assertSee('Pusat Notifikasi Pengguna');
        $indexResp->assertSee('Profil Dikemaskini');
        $indexResp->assertSee('Kata Laluan Ditukar');

        // 5. Uji tandakan satu notifikasi sebagai telah dibaca
        $latestNotif = $penternak->userNotifications()->unread()->first();
        $this->assertNotNull($latestNotif);

        $readResp = $this->actingAs($penternak)->post(route('notifications.mark-read', $latestNotif->id));
        $readResp->assertRedirect();
        $this->assertEquals(1, $penternak->unreadNotificationsCount());

        // 6. Uji tanda semua dibaca
        $markAllResp = $this->actingAs($penternak)->post(route('notifications.mark-all-read'));
        $markAllResp->assertRedirect();
        $this->assertEquals(0, $penternak->unreadNotificationsCount());

        // 7. Uji padam satu notifikasi
        $deleteResp = $this->actingAs($penternak)->delete(route('notifications.destroy', $latestNotif->id));
        $deleteResp->assertRedirect();
        $this->assertDatabaseMissing('user_notifications', ['id' => $latestNotif->id]);

        // 8. Uji pembersihan notifikasi telah dibaca
        $cleanResp = $this->actingAs($penternak)->post(route('notifications.clear-read'));
        $cleanResp->assertRedirect();
        $this->assertEquals(0, $penternak->userNotifications()->count());
    }

    public function test_borang_a_submission_notifies_applicant_admin_jajahan_and_admin_negeri()
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $penternak = User::where('role', 'penternak')->first();
        $adminJajahan = User::where('role', 'admin_jajahan')->where('jajahan', 'Kota Bharu')->first();
        $adminEptr = User::where('role', 'admin_eptr')->first();
        $superAdmin = User::where('role', 'super_admin')->first();

        // Rekod kiraan awal notifikasi
        $adminJajahanInitialCount = $adminJajahan->unreadNotificationsCount();
        $adminEptrInitialCount = $adminEptr->unreadNotificationsCount();
        $superAdminInitialCount = $superAdmin->unreadNotificationsCount();

        $file = \Illuminate\Http\UploadedFile::fake()->create('resit_bayaran.pdf', 200, 'application/pdf');

        // Penternak mendaftar ternakan (Borang A)
        $resp = $this->actingAs($penternak)->post(route('eptr.store'), [
            'nama_pemunya' => $penternak->name,
            'no_kp_pemunya' => $penternak->ic_number,
            'no_tel_pemunya' => $penternak->phone,
            'alamat_pemunya' => $penternak->address,
            'jajahan' => 'Kota Bharu',
            'daerah' => 'Peringat',
            'jenis_ternakan' => 'lembu',
            'baka' => 'kedah-kelantan',
            'jantina' => 'Betina',
            'tarikh_lahir' => '2023-01-01',
            'tujuan_ternakan' => 'Pembiakan',
            'resit_pembayaran' => $file,
        ]);

        $resp->assertRedirect();

        // 1. Semak notifikasi untuk Penternak (Pemohon)
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $penternak->id,
            'type' => 'eptr',
            'title' => 'Permohonan Pendaftaran Ternakan Dihantar',
        ]);

        // 2. Semak notifikasi untuk Admin EPTR Jajahan Kota Bharu
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $adminJajahan->id,
            'type' => 'eptr',
            'title' => 'Permohonan Ternakan Menunggu Kelulusan (Kota Bharu)',
        ]);
        $this->assertEquals($adminJajahanInitialCount + 1, $adminJajahan->fresh()->unreadNotificationsCount());

        // 3. Semak notifikasi untuk Admin EPTR Negeri & Super Admin
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $adminEptr->id,
            'type' => 'eptr',
            'title' => 'Permohonan Ternakan Baharu (Kota Bharu)',
        ]);
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $superAdmin->id,
            'type' => 'eptr',
            'title' => 'Permohonan Ternakan Baharu (Kota Bharu)',
        ]);
    }

    public function test_daftar_anak_submission_notifies_applicant_admin_jajahan_and_admin_negeri()
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $penternak = User::where('role', 'penternak')->first();
        $adminJajahan = User::where('role', 'admin_jajahan')->where('jajahan', 'Kota Bharu')->first();
        $adminEptr = User::where('role', 'admin_eptr')->first();
        $superAdmin = User::where('role', 'super_admin')->first();

        $induk = Ternakan::where('jantina', 'Betina')->where('jajahan', 'Kota Bharu')->first();
        if (!$induk) {
            $pemunya = Pemunya::where('user_id', $penternak->id)->first();
            $induk = Ternakan::create([
                'pemunya_id' => $pemunya->id,
                'no_tag' => 'KB-INDUK-999',
                'jenis_ternakan' => 'Lembu',
                'baka' => 'Brahman',
                'jantina' => 'Betina',
                'status' => 'Aktif',
                'status_kelulusan' => 'Diluluskan',
                'jajahan' => 'Kota Bharu',
                'daerah' => 'Peringat',
                'tarikh_daftar' => date('Y-m-d'),
            ]);
        }

        $receipt = \Illuminate\Http\UploadedFile::fake()->create('resit_anak.pdf', 150, 'application/pdf');

        $resp = $this->actingAs($penternak)->post('/eptr/daftar-anak', [
            'induk_id' => $induk->id,
            'tarikh_kelahiran' => date('Y-m-d'),
            'jantina_anak' => 'Jantan',
            'baka_anak' => 'Brahman',
            'berat_lahir_kg' => 30.0,
            'warna_anak' => 'Coklat Putih',
            'status_kelahiran' => 'Hidup',
            'keadaan_anak' => 'Cergas',
            'catatan' => 'Anak jantan sihat',
            'resit_pembayaran' => $receipt,
        ]);

        $resp->assertRedirect();

        // 1. Notifikasi untuk Penternak (Pemohon)
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $penternak->id,
            'type' => 'eptr',
            'title' => 'Pendaftaran Kelahiran Anak Ternakan Dihantar',
        ]);

        // 2. Notifikasi untuk Admin EPTR Jajahan Kota Bharu
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $adminJajahan->id,
            'type' => 'eptr',
            'title' => 'Kelahiran Anak Ternakan Menunggu Kelulusan (Kota Bharu)',
        ]);

        // 3. Notifikasi untuk Admin EPTR Negeri & Super Admin
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $adminEptr->id,
            'type' => 'eptr',
            'title' => 'Pendaftaran Kelahiran Anak Baharu (Kota Bharu)',
        ]);
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $superAdmin->id,
            'type' => 'eptr',
            'title' => 'Pendaftaran Kelahiran Anak Baharu (Kota Bharu)',
        ]);
    }

    public function test_daftar_anak_late_registration_detection_and_fee_calculation()
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $penternak = User::where('role', 'penternak')->first();
        $induk = Ternakan::where('jantina', 'Betina')->first();

        // 1. Semak UI daftar anak mengandungi pengesanan lewat pendaftaran (> 14 hari)
        $respCreate = $this->actingAs($penternak)->get('/eptr/daftar-anak?induk_id=' . $induk->id);
        $respCreate->assertStatus(200);
        $respCreate->assertSee('FI STATUTORI');
        $respCreate->assertSee('Pendaftaran Lewat');
        $respCreate->assertSee('Seksyen 7 (Daftar &amp; Tagging + Denda Lewat)', false);

        // 2. Daftar anak yang lahir 25 hari lalu (> 14 hari)
        $birthDate = Carbon::today()->subDays(25)->toDateString();
        $receipt = \Illuminate\Http\UploadedFile::fake()->create('resit_lewat.pdf', 150, 'application/pdf');

        $resp = $this->actingAs($penternak)->post('/eptr/daftar-anak', [
            'induk_id' => $induk->id,
            'tarikh_kelahiran' => $birthDate,
            'jantina_anak' => 'Betina',
            'baka_anak' => 'Brahman',
            'kategori_daftar' => 'lewat',
            'status_kelahiran' => 'Hidup',
            'keadaan_anak' => 'Cergas',
            'resit_pembayaran' => $receipt,
        ]);

        $resp->assertRedirect();

        $anak = Ternakan::where('no_tanda_pengenalan_induk', $induk->no_tag)->latest()->first();
        $this->assertNotNull($anak);
        $this->assertEquals('25 Hari', $anak->umur);
        $this->assertStringContainsString('[Pendaftaran Lewat Seksyen 7 (> 14 Hari)]', $anak->catatan);
    }

    public function test_epu_new_registration_and_wizard_application_flow()
    {
        // 1. Test Registration page (Image 1)
        $respRegisterPage = $this->get('/register');
        $respRegisterPage->assertStatus(200);
        $respRegisterPage->assertSee('PENDAFTARAN BARU');
        $respRegisterPage->assertSee('Bentuk Perniagaan');
        $respRegisterPage->assertSee('Nama Syarikat');
        $respRegisterPage->assertSee('No Fax');
        $respRegisterPage->assertSee('Pengesahan Keselamatan (Captcha)');

        // Submit new registration
        $captcha = session('register_captcha');
        $ic = '910515037788';
        $respRegister = $this->post('/register', [
            'name' => 'Ahmad Razak bin Razali',
            'nama_syarikat' => 'Razak Unggas Enterprise',
            'no_ssm' => '202301099887',
            'bentuk_perniagaan' => 'Sendirian Berhad',
            'ic_number' => $ic,
            'phone' => '019-9112233',
            'fax' => '09-7449988',
            'email' => 'razak.unggas@gmail.com',
            'address' => 'Kampung Padang Kala, Peringat',
            'poskod' => '16400',
            'negeri' => 'Kelantan',
            'captcha' => $captcha,
        ]);

        $respRegister->assertRedirect(route('dashboard'));
        $newUser = User::where('ic_number', $ic)->first();
        $this->assertNotNull($newUser);
        $this->assertEquals('Razak Unggas Enterprise', $newUser->nama_syarikat);
        $this->assertEquals('Sendirian Berhad', $newUser->bentuk_perniagaan);

        // 2. Test EPU 4-Step Wizard Application Form (Image 2 & Image 3)
        $respBorangA = $this->actingAs($newUser)->get('/epu/daftar-borang-a');
        $respBorangA->assertStatus(200);
        $respBorangA->assertSee('Maklumat Pemohon');
        $respBorangA->assertSee('Maklumat Penternakan / Ladang');
        $respBorangA->assertSee('Pengiraan Reban');
        $respBorangA->assertSee('Pengecualian Lesen');

        // 3. Submit EPU application with coops & GPS coordinates
        $rebanData = [
            'jenis' => 'Ayam',
            'reban_ayam' => [
                ['id' => 1, 'lebar' => 40, 'panjang' => 300, 'tingkat' => 2]
            ],
            'total_keluasan' => 24000,
            'kapasiti' => 20000,
            'fi' => 200.00
        ];

        $respSubmit = $this->actingAs($newUser)->post('/epu/daftar-borang-a', [
            'nama_pemohon_atau_syarikat' => 'Razak Unggas Enterprise',
            'no_syarikat_atau_ssm' => '202301099887',
            'email_pemohon' => 'razak.unggas@gmail.com',
            'phone_pemohon' => '019-9112233',
            'jenis_unggas' => 'Ayam',
            'jurusan_aktiviti' => 'Pedaging',
            'nama_ladang' => 'Ladang Ayam Pedaging Razak Peringat',
            'id_premis' => 'PRM-KB-099',
            'latitude' => 6.0523,
            'longitude' => 102.2981,
            'alamat_ladang' => 'Lot 2024, Mukim Padang Kala, Peringat',
            'poskod' => '16400',
            'negeri' => 'Kelantan',
            'jajahan' => 'Kota Bharu',
            'daerah' => 'Peringat',
            'luas_kawasan_sqft' => 30000,
            'reban_data' => json_encode($rebanData),
            'kapasiti_maksimum_unggas' => 20000,
            'bilangan_semasa_unggas' => 18000,
            'yuran_lesen' => 200.00,
            'mohon_pengecualian' => 0,
        ]);

        $respSubmit->assertRedirect();
        $ladang = EpuLadang::where('nama_ladang', 'Ladang Ayam Pedaging Razak Peringat')->first();
        $this->assertNotNull($ladang);
        $this->assertEquals('PRM-KB-099', $ladang->id_premis);
        $this->assertEquals('Kota Bharu', $ladang->jajahan);
        $this->assertEquals(20000, $ladang->kapasiti_maksimum_unggas);

        $permohonan = EpuPermohonan::where('epu_ladang_id', $ladang->id)->first();
        $this->assertNotNull($permohonan);
        $this->assertEquals('Ayam', $permohonan->jenis_unggas);
        $this->assertEquals('Pedaging', $permohonan->jurusan_aktiviti);
        $this->assertEquals(200.00, (float)$permohonan->yuran_lesen);

        // 4. Test Small Farm (Ayam <= 500 = Free / RM 0.00)
        $respAyamSmall = $this->actingAs($newUser)->post('/epu/daftar-borang-a', [
            'nama_pemohon_atau_syarikat' => 'Razak Unggas Enterprise',
            'no_syarikat_atau_ssm' => '202301099887',
            'jenis_unggas' => 'Ayam',
            'jurusan_aktiviti' => 'Pedaging',
            'nama_ladang' => 'Reban Ayam Kampung Kecil',
            'alamat_ladang' => 'Belakang Rumah, Kg Padang Kala',
            'jajahan' => 'Kota Bharu',
            'kapasiti_maksimum_unggas' => 450,
            'bilangan_semasa_unggas' => 300,
        ]);

        $respAyamSmall->assertRedirect();
        $ayamSmallLadang = EpuLadang::where('nama_ladang', 'Reban Ayam Kampung Kecil')->first();
        $this->assertNotNull($ayamSmallLadang);
        $ayamSmallPermohonan = EpuPermohonan::where('epu_ladang_id', $ayamSmallLadang->id)->first();
        $this->assertNotNull($ayamSmallPermohonan);
        $this->assertEquals(0.00, (float)$ayamSmallPermohonan->yuran_lesen);

        // 5. Test Small Farm (Puyuh / Merpati <= 1000 = Free / RM 0.00)
        $respPuyuhSmall = $this->actingAs($newUser)->post('/epu/daftar-borang-a', [
            'nama_pemohon_atau_syarikat' => 'Razak Unggas Enterprise',
            'no_syarikat_atau_ssm' => '202301099887',
            'jenis_unggas' => 'Puyuh',
            'jurusan_aktiviti' => 'Penelur',
            'nama_ladang' => 'Reban Puyuh 800 Ekor',
            'alamat_ladang' => 'Belakang Rumah, Kg Padang Kala',
            'jajahan' => 'Kota Bharu',
            'kapasiti_maksimum_unggas' => 800,
            'bilangan_semasa_unggas' => 600,
        ]);

        $respPuyuhSmall->assertRedirect();
        $puyuhSmallLadang = EpuLadang::where('nama_ladang', 'Reban Puyuh 800 Ekor')->first();
        $this->assertNotNull($puyuhSmallLadang);
        $puyuhSmallPermohonan = EpuPermohonan::where('epu_ladang_id', $puyuhSmallLadang->id)->first();
        $this->assertNotNull($puyuhSmallPermohonan);
        $this->assertEquals(0.00, (float)$puyuhSmallPermohonan->yuran_lesen);
    }

    public function test_epu_official_gazetted_forms_rendering()
    {
        $user = User::where('role', 'super_admin')->first();
        $ladang = EpuLadang::first();
        if (!$ladang) {
            $ladang = EpuLadang::create([
                'user_id' => $user->id,
                'nama_pemohon_atau_syarikat' => 'Syarikat Ternakan Unggas Maju Sdn Bhd',
                'no_syarikat_atau_ssm' => '202301099887',
                'nama_ladang' => 'Ladang Ayam Maju',
                'alamat_ladang' => 'Lot 102, Jalan Tok Bali',
                'poskod' => '16800',
                'jajahan' => 'Pasir Puteh',
                'negeri' => 'Kelantan',
                'sistem_reban' => 'Tertutup',
                'kapasiti_maksimum_unggas' => 15000,
                'luas_kawasan_sqft' => 20000,
                'latitude' => '5.8500',
                'longitude' => '102.4000',
            ]);
            EpuPermohonan::create([
                'epu_ladang_id' => $ladang->id,
                'no_rujukan_permohonan' => 'EPU/PPT/2026/001',
                'jenis_permohonan' => 'Baru',
                'jenis_unggas' => 'Ayam Pedaging',
                'bilangan_semasa_unggas' => 10000,
                'no_lesen_epu' => 'EPU-PPT-2026-001',
                'tarikh_mula_lesen' => date('Y-m-d'),
                'tarikh_tamat_lesen' => date('Y-m-d', strtotime('+1 year')),
                'yuran_lesen' => 200.00,
                'no_resit_bayaran' => 'RES-9988',
                'status' => 'Diluluskan',
                'diluluskan_oleh' => $user->id,
                'tarikh_kelulusan' => date('Y-m-d'),
            ]);
        }

        // 1. Borang A: Permohonan Lesen (Format Rasmi Warta)
        $respBorangA = $this->actingAs($user)->get("/epu/cetak-borang-a/{$ladang->id}");
        $respBorangA->assertStatus(200);
        $respBorangA->assertSee('PERMOHONAN LESEN PERLADANGAN UNGGAS DAN');
        $respBorangA->assertSee('JADUAL PERTAMA');
        $respBorangA->assertSee('UNTUK KEGUNAAN RASMI PEJABAT');

        // 2. Borang B: Permohonan Pengecualian Lesen
        $respBorangB = $this->actingAs($user)->get("/epu/cetak-borang-b-pengecualian/{$ladang->id}");
        $respBorangB->assertStatus(200);
        $respBorangB->assertSee('BORANG B');
        $respBorangB->assertSee('BORANG PERMOHONAN PENGECUALIAN LESEN PERLADANGAN UNGGAS');
        $respBorangB->assertSee('Projek', false);
        $respBorangB->assertSee('Amanah Ikhtiar Malaysia');

        // 3. Borang C: Sijil Pengecualian Lesen
        $respBorangC = $this->actingAs($user)->get("/epu/cetak-sijil-pengecualian-c/{$ladang->id}");
        $respBorangC->assertStatus(200);
        $respBorangC->assertSee('BORANG C');
        $respBorangC->assertSee('SIJIL PENGECUALIAN LESEN PERLADANGAN UNGGAS');
        $respBorangC->assertSee('SYARAT-SYARAT KHAS');
        $respBorangC->assertSee('Tandatangan Pengarah dan Cop Jabatan');

        // 4. Borang A: Permohonan Salinan Pendua Lesen
        $respPendua = $this->actingAs($user)->get("/epu/cetak-salinan-pendua/{$ladang->id}");
        $respPendua->assertStatus(200);
        $respPendua->assertSee('BORANG A');
        $respPendua->assertSee('BORANG PERMOHONAN SALINAN PENDUA LESEN');
        $respPendua->assertSee('Hilang');
        $respPendua->assertSee('Musnah');
    }

    public function test_epu_store_handles_file_uploads_safely()
    {
        $user = User::where('role', 'super_admin')->first();
        \Illuminate\Support\Facades\Storage::fake('public');

        $file = \Illuminate\Http\UploadedFile::fake()->create('dokumen_pelan.pdf', 100);

        $response = $this->actingAs($user)->post('/epu/daftar-borang-a', [
            'nama_pemohon_atau_syarikat' => 'Syarikat Pelan Unggas',
            'no_syarikat_atau_ssm' => '202301099887',
            'jenis_unggas' => 'Ayam',
            'jurusan_aktiviti' => 'Pedaging',
            'nama_ladang' => 'Ladang Pelan Selamat',
            'alamat_ladang' => 'Lot 50, Pasir Puteh',
            'jajahan' => 'Pasir Puteh',
            'kapasiti_maksimum_unggas' => 2000,
            'bilangan_semasa_unggas' => 1500,
            'dokumen_pelan' => $file,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('epu_ladang', [
            'nama_ladang' => 'Ladang Pelan Selamat',
        ]);
    }
}




