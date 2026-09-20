<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pemunya;
use App\Models\NaimbifPermohonan;
use App\Models\NaimbifInventoriTernakan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NaimbifIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_naimbif_public_home_renders_successfully()
    {
        $response = $this->get('/naimbif');
        $response->assertStatus(200);
        $response->assertSee('Program Ladang Bridlot');
        $response->assertSee('NAIMbif Kelantan');
        $response->assertSee('Mohon Penyertaan Sekarang');
    }

    public function test_naimbif_public_apply_page_renders()
    {
        $response = $this->get('/naimbif/permohonan');
        $response->assertStatus(200);
        $response->assertSee('Borang Permohonan Ladang Bridlot NAIMbif');
        $response->assertSee('MAKLUMAT PESERTA');
        $response->assertSee('MAKLUMAT ASAS TERNAKAN');
    }

    public function test_naimbif_api_check_existing_ic_returns_data()
    {
        $pemunya = Pemunya::first();
        if ($pemunya) {
            $response = $this->getJson('/naimbif/api/semak-kp?no_kp=' . $pemunya->no_kp);
            $response->assertStatus(200);
            $response->assertJson([
                'found' => true,
                'data' => [
                    'nama' => $pemunya->nama,
                ],
                'autofill' => [
                    'nama' => $pemunya->nama,
                ],
            ]);
            $response->assertJsonStructure([
                'found',
                'source',
                'source_label',
                'autofill_keys',
                'autofill' => [
                    'nama',
                    'no_telefon',
                    'alamat_tetap',
                    'jajahan',
                ],
            ]);
        } else {
            $this->assertTrue(true);
        }
    }

    public function test_naimbif_public_application_submission_creates_records_and_links_pemunya()
    {
        $payload = [
            'nama' => 'Ahmad Faris Bin Ismail',
            'no_kp' => '850101035555',
            'no_telefon' => '0198887777',
            'alamat_tetap' => 'Kampung Padang Temusu, 16800 Pasir Puteh',
            'poskod' => '16800',
            'jajahan' => 'Pasir Puteh',
            'pengalaman_menternak' => 8,
            'status_penternakan' => 'Sepenuh Masa',
            'pernah_kursus' => '0',
            'berminat_kursus_jpvnk' => '1',
            'alamat_ladang' => 'Lot 458, Mukim Bukit Awang, Pasir Puteh',
            'poskod_ladang' => '16800',
            'jajahan_ladang' => 'Pasir Puteh',
            'keluasan_tanah' => 15.5,
            'status_tanah' => 'Sendiri',
            'padang_ragut' => 'Ada',
            'bilangan_pekerja' => 2,
            'punca_ternakan' => 'Beli',
            'kaedah_pembiakan' => 'Permanian Beradas',
            'gps_latitud' => '5.8341',
            'gps_longitud' => '102.4012',
            'tarikh_permohonan' => '2026-09-20',
            'pengakuan_benar' => '1',
            'stok' => [
                'CHAROLAIS' => [
                    'betina_anak' => 4,
                    'betina_dara' => 4,
                    'betina_induk' => 10,
                    'jantan_anak' => 3,
                    'jantan_pejantan' => 2,
                ],
                'BELGIAN BLUE' => [
                    'betina_anak' => 2,
                    'betina_dara' => 1,
                    'betina_induk' => 5,
                    'jantan_anak' => 0,
                    'jantan_pejantan' => 1,
                ],
            ],
        ];

        $response = $this->post('/naimbif/permohonan', $payload);
        $response->assertRedirect();

        $application = NaimbifPermohonan::where('no_kp', '850101035555')->first();
        $this->assertNotNull($application);
        $this->assertEquals('Dalam Semakan', $application->status_kelengkapan);
        $this->assertEquals('Menunggu Kelulusan', $application->status_negeri);

        // Check inventory rows created
        $inventories = NaimbifInventoriTernakan::where('naimbif_permohonan_id', $application->id)->get();
        $this->assertCount(2, $inventories);

        // Check total ternakan calculation
        $this->assertEquals(32, $application->total_ternakan);

        // Check Pemunya linkage
        $pemunya = Pemunya::where('no_kp', '850101035555')->first();
        $this->assertNotNull($pemunya);
        $this->assertEquals($pemunya->id, $application->pemunya_id);
    }

    public function test_naimbif_check_status_lookup_works()
    {
        $application = NaimbifPermohonan::create([
            'no_rujukan' => 'NB-2026-9999',
            'nama' => 'Mohd Azhar Bin Salleh',
            'no_kp' => '900202036666',
            'no_telefon' => '0139998888',
            'alamat_tetap' => 'Kg Kok Lanas, Kota Bharu',
            'poskod' => '16450',
            'jajahan' => 'Kota Bharu',
            'pengalaman_menternak' => 5,
            'status_penternakan' => 'Sepenuh Masa',
            'pernah_kursus' => false,
            'alamat_ladang' => 'Lot 102 Kok Lanas',
            'poskod_ladang' => '16450',
            'jajahan_ladang' => 'Kota Bharu',
            'keluasan_tanah' => 10,
            'status_tanah' => 'Sendiri',
            'padang_ragut' => 'Ada',
            'bilangan_pekerja' => 1,
            'punca_ternakan' => 'Beli',
            'kaedah_pembiakan' => 'Asli',
            'syor_permohonan' => 'Disokong',
            'status_negeri' => 'Dalam Semakan',
            'pengakuan_benar' => true,
            'tarikh_permohonan' => '2026-09-20',
        ]);

        $response = $this->get('/naimbif/semakan?keyword=900202036666');
        $response->assertStatus(200);
        $response->assertSee('NB-2026-9999');
        $response->assertSee('Mohd Azhar Bin Salleh');
        $response->assertSee('Disokong Jajahan');
    }

    public function test_naimbif_admin_officer_can_view_listing_and_verify_premise()
    {
        $officer = User::where('role', 'super_admin')->first();

        $application = NaimbifPermohonan::create([
            'no_rujukan' => 'NB-2026-8888',
            'nama' => 'Razak Bin Daud',
            'no_kp' => '780303037777',
            'no_telefon' => '0129997777',
            'alamat_tetap' => 'Machang, Kelantan',
            'poskod' => '18500',
            'jajahan' => 'Machang',
            'pengalaman_menternak' => 10,
            'status_penternakan' => 'Sepenuh Masa',
            'pernah_kursus' => false,
            'alamat_ladang' => 'Mukim Ulu Sat, Machang',
            'poskod_ladang' => '18500',
            'jajahan_ladang' => 'Machang',
            'keluasan_tanah' => 12,
            'status_tanah' => 'Sendiri',
            'padang_ragut' => 'Ada',
            'bilangan_pekerja' => 2,
            'punca_ternakan' => 'Beli',
            'kaedah_pembiakan' => 'Asli',
            'status_negeri' => 'Dalam Semakan',
            'pengakuan_benar' => true,
            'tarikh_permohonan' => '2026-09-20',
        ]);

        // 1. Officer views list
        $resList = $this->actingAs($officer)->get('/naimbif/urus');
        $resList->assertStatus(200);
        $resList->assertSee('NB-2026-8888');

        // 2. Officer views detail
        $resShow = $this->actingAs($officer)->get('/naimbif/urus/' . $application->id);
        $resShow->assertStatus(200);
        $resShow->assertSee('Razak Bin Daud');

        // 3. Jajahan officer verifies premise
        $resVerify = $this->actingAs($officer)->put('/naimbif/urus/' . $application->id . '/jajahan', [
            'id_premis' => 'PRM-MCG-001',
            'syor_permohonan' => 'Disokong',
            'status_kelengkapan' => 'Lengkap',
            'catatan_jajahan' => 'Kawasan kandang dan padang ragut sangat memuaskan.',
        ]);
        $resVerify->assertRedirect();

        $application->refresh();
        $this->assertEquals('Disokong', $application->syor_permohonan);
        $this->assertEquals('PRM-MCG-001', $application->id_premis);

        // 4. State officer approves application
        $resApprove = $this->actingAs($officer)->put('/naimbif/urus/' . $application->id . '/negeri', [
            'status_negeri' => 'Lulus',
            'ulasan_negeri' => 'Diluluskan untuk bekalan baka fasa 1.',
        ]);
        $resApprove->assertRedirect();

        $application->refresh();
        $this->assertEquals('Lulus', $application->status_negeri);
        $this->assertNotNull($application->no_rujukan_negeri);
        $this->assertStringStartsWith('JPVNK/BL/', $application->no_rujukan_negeri);
    }

    public function test_naimbif_print_official_form_renders()
    {
        $application = NaimbifPermohonan::create([
            'no_rujukan' => 'NB-2026-7777',
            'nama' => 'Kamal Bin Hashim',
            'no_kp' => '820404038888',
            'no_telefon' => '0149996666',
            'alamat_tetap' => 'Tumpat, Kelantan',
            'poskod' => '16200',
            'jajahan' => 'Tumpat',
            'pengalaman_menternak' => 4,
            'status_penternakan' => 'Sepenuh Masa',
            'pernah_kursus' => false,
            'alamat_ladang' => 'Mukim Jal, Tumpat',
            'poskod_ladang' => '16200',
            'jajahan_ladang' => 'Tumpat',
            'keluasan_tanah' => 8,
            'status_tanah' => 'Sendiri',
            'padang_ragut' => 'Ada',
            'bilangan_pekerja' => 1,
            'punca_ternakan' => 'Beli',
            'kaedah_pembiakan' => 'Asli',
            'syor_permohonan' => 'Disokong',
            'status_negeri' => 'Lulus',
            'no_rujukan_negeri' => 'JPVNK/BL/2026/0001',
            'pengakuan_benar' => true,
            'tarikh_permohonan' => '2026-09-20',
        ]);

        $response = $this->get('/naimbif/cetak/' . $application->no_rujukan);
        $response->assertStatus(200);
        $response->assertSee('BORANG PERMOHONAN PENYERTAAN LADANG BRIDLOT NAIMbif');
        $response->assertSee('NB-2026-7777');
        $response->assertSee('Kamal Bin Hashim');
    }

    public function test_gis_map_includes_naimbif_approved_farms()
    {
        $officer = User::where('role', 'super_admin')->first();

        NaimbifPermohonan::create([
            'no_rujukan' => 'NB-2026-6666',
            'nama' => 'Latif Bin Yusof',
            'no_kp' => '880505039999',
            'no_telefon' => '0159995555',
            'alamat_tetap' => 'Tanah Merah',
            'poskod' => '17500',
            'jajahan' => 'Tanah Merah',
            'pengalaman_menternak' => 6,
            'status_penternakan' => 'Sepenuh Masa',
            'pernah_kursus' => false,
            'alamat_ladang' => 'Mukim Kusial, Tanah Merah',
            'poskod_ladang' => '17500',
            'jajahan_ladang' => 'Tanah Merah',
            'keluasan_tanah' => 14,
            'status_tanah' => 'Sendiri',
            'padang_ragut' => 'Ada',
            'bilangan_pekerja' => 2,
            'punca_ternakan' => 'Beli',
            'kaedah_pembiakan' => 'Asli',
            'gps_latitud' => '5.805',
            'gps_longitud' => '102.145',
            'syor_permohonan' => 'Disokong',
            'status_negeri' => 'Lulus',
            'pengakuan_benar' => true,
            'tarikh_permohonan' => '2026-09-20',
        ]);

        $response = $this->actingAs($officer)->get('/peta-taburan');
        $response->assertStatus(200);
        $response->assertSee('Latif Bin Yusof');
        $response->assertSee('NAIMbif (Ladang Bridlot)');
    }
}