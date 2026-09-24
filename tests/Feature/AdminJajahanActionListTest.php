<?php

namespace Tests\Feature;

use App\Models\ActionList;
use App\Models\KlinikTemujanji;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminJajahanActionListTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $adminJajahanPasirPuteh;
    protected User $adminJajahanKotaBharu;
    protected User $penternak;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->superAdmin = User::where('role', 'super_admin')->first();

        $this->adminJajahanPasirPuteh = User::where('jajahan', 'Pasir Puteh')
            ->whereIn('role', ['admin_jajahan', 'admin_eptr_jajahan'])
            ->first() ?: User::factory()->create([
                'name' => 'Pegawai Veterinar Pasir Puteh',
                'email' => 'admin.pp.test@veterinar.kelantan.gov.my',
                'ic_number' => '850505039911',
                'role' => 'admin_jajahan',
                'jajahan' => 'Pasir Puteh',
            ]);

        $this->adminJajahanKotaBharu = User::where('jajahan', 'Kota Bharu')
            ->whereIn('role', ['admin_jajahan', 'admin_eptr_jajahan'])
            ->first() ?: User::factory()->create([
                'name' => 'Pegawai Veterinar Kota Bharu',
                'email' => 'admin.kb.test@veterinar.kelantan.gov.my',
                'ic_number' => '860606039922',
                'role' => 'admin_jajahan',
                'jajahan' => 'Kota Bharu',
            ]);

        $this->penternak = User::where('role', 'penternak')->first() ?: User::factory()->create([
            'name' => 'Mohd Azman bin Salleh',
            'email' => 'azman.penternak.test@gmail.com',
            'ic_number' => '900729039933',
            'phone' => '019-9887766',
            'address' => 'Kampung Gong Chapa, 16800 Pasir Puteh',
            'poskod' => '16800',
            'jajahan' => 'Pasir Puteh',
            'role' => 'penternak',
        ]);
    }

    public function test_admin_jajahan_can_access_action_list_index(): void
    {
        $response = $this->actingAs($this->adminJajahanPasirPuteh)->get(route('action-list.index'));
        $response->assertStatus(200);
        $response->assertSee('Action List Pejabat Perkhidmatan Veterinar Jajahan');
        $response->assertSee('PK-RK-61');
    }

    public function test_unauthorized_regular_user_cannot_access_action_list(): void
    {
        $response = $this->actingAs($this->penternak)->get(route('action-list.index'));
        $response->assertStatus(403);
    }

    public function test_admin_jajahan_can_create_and_store_action_list_form_pk_rk_61(): void
    {
        $formData = [
            'kod_dokumen' => 'PK-RK-61',
            'no_bil' => 'AL/PP/2026/0001',
            'jajahan' => 'Pasir Puteh',
            'tarikh' => '2026-09-24',
            'masa_pendaftaran' => '09:30 AM',
            'kategori_pelanggan' => 'Individu',
            'nama_pelanggan' => 'Mohd Azman bin Salleh',
            'no_kp' => $this->penternak->ic_number,
            'telefon' => '019-9887766',
            'alamat' => 'Kampung Gong Chapa, Pasir Puteh',
            'mukim' => 'Padang Pak Amat',
            'poskod' => '16800',
            'daerah' => 'Jajahan Pasir Puteh, Kelantan',
            'no_rujukan' => 'JPVNK/PP/RAW/2026/001',
            'user_id' => $this->penternak->id,

            // B. Butir-butir Perkhidmatan
            'catatan_perkhidmatan_dipohon' => 'Rawatan lembu sakit demam dan permohonan suntikan vaksin.',

            // C. Maklumat Temujanji
            'nama_pegawai' => 'Dr. Nik Farhan',
            'masa_pegawai' => '08:30 AM',
            'masa_temujanji_mula' => '09:00 AM',
            'masa_temujanji_hingga' => '11:00 AM',
            'maklumat_pelanggan_berlainan' => 'Pemilik sendiri hadir di kandang',
            'maklumat_tambahan' => 'Kandang belakang masjid Gong Chapa',

            // D. Perkhidmatan Diberi
            'rawatan_lapangan' => '1',
            'pemantauan_pawah' => '1',
            'jenis_ternakan' => ['Lembu'],
            'bil_ternakan' => 3,
            'bil_yang_ada' => 15,
            'laporan' => 'Pemeriksaan fizikal mendapati 3 ekor lembu mengalami demam ringan. Suhu 39.5C.',
            'penggunaan_ubat' => 'Oxytetracycline 20% LA 15ml, Vitamin B-Complex 10ml, Flunixin 5ml',

            // E. Pengakuan Pelanggan & Pengesahan
            'tandatangan_pelanggan_nama' => 'Mohd Azman bin Salleh',
            'tandatangan_pelanggan_tarikh' => '2026-09-24',
            'tandatangan_pelanggan_masa' => '10:45 AM',
            'kepuasan_pelanggan' => 'Puashati',
            'cadangan_pelanggan' => 'Perkhidmatan pantas dan terbaik.',
            'bayaran' => '45.00',
            'no_resit' => 'R-2026-0988',
            'pengesahan_ulasan_pegawai' => 'Rawatan selesai. Keadaan lembu stabil.',
            'status' => 'Selesai',
        ];

        $response = $this->actingAs($this->adminJajahanPasirPuteh)->post(route('action-list.store'), $formData);
        
        $actionList = ActionList::where('no_bil', 'AL/PP/2026/0001')->first();
        $this->assertNotNull($actionList);
        $response->assertRedirect(route('action-list.show', $actionList->id));

        $this->assertEquals('Mohd Azman bin Salleh', $actionList->nama_pelanggan);
        $this->assertEquals('Pasir Puteh', $actionList->jajahan);
        $this->assertEquals(45.00, $actionList->bayaran);
        $this->assertEquals('Puashati', $actionList->kepuasan_pelanggan);
        $this->assertTrue($actionList->perkhidmatan_diberi['rawatan_lapangan']);
        $this->assertTrue($actionList->perkhidmatan_diberi['pemantauan_pawah']);
        $this->assertContains('Lembu', $actionList->jenis_ternakan);
    }

    public function test_action_list_prefill_from_klinik_temujanji(): void
    {
        $temujanji = KlinikTemujanji::create([
            'user_id' => $this->penternak->id,
            'no_temujanji' => 'TMJ-2026-9901',
            'jenis_haiwan' => 'Kambing',
            'nama_haiwan' => 'Billy',
            'baka' => 'Boer',
            'jantina_haiwan' => 'Jantan',
            'umur_haiwan' => '2 Tahun',
            'simptom_atau_tujuan' => 'Pemeriksaan luka dan vaksinasi tahunan.',
            'tarikh_temujanji' => now()->toDateString(),
            'sesi' => 'Pagi (8:30 AM - 12:30 PM)',
            'klinik_jajahan' => 'Pusat Veterinar Pasir Puteh',
            'status' => 'Disahkan',
        ]);

        $response = $this->actingAs($this->adminJajahanPasirPuteh)->get(route('action-list.create', ['temujanji_id' => $temujanji->id]));
        $response->assertStatus(200);
        $response->assertSee($this->penternak->name);
        $response->assertSee($this->penternak->ic_number);
        $response->assertSee('Pemeriksaan luka dan vaksinasi tahunan.');
    }

    public function test_admin_jajahan_can_view_and_print_pk_rk_61(): void
    {
        $actionList = ActionList::create([
            'kod_dokumen' => 'PK-RK-61',
            'no_bil' => 'AL/KB/2026/0005',
            'jajahan' => 'Kota Bharu',
            'tarikh' => now()->toDateString(),
            'kategori_pelanggan' => 'Individu',
            'nama_pelanggan' => 'Nik Siti Nurhaliza',
            'no_kp' => '950101035999',
            'telefon' => '011-22334455',
            'alamat' => 'Kubang Kerian, Kota Bharu',
            'mukim' => 'Kubang Kerian',
            'poskod' => '16150',
            'daerah' => 'Jajahan Kota Bharu, Kelantan',
            'catatan_perkhidmatan_dipohon' => 'Rawatan kucing sakit',
            'perkhidmatan_diberi' => ['rawatan_klinik' => true],
            'jenis_ternakan' => ['Kucing'],
            'bil_ternakan' => 1,
            'laporan' => 'Kucing diberikan ubat cacing dan antibiotik.',
            'bayaran' => 30.00,
            'kepuasan_pelanggan' => 'Puashati',
            'status' => 'Selesai',
        ]);

        // Show page
        $showResp = $this->actingAs($this->adminJajahanKotaBharu)->get(route('action-list.show', $actionList->id));
        $showResp->assertStatus(200);
        $showResp->assertSee('AL/KB/2026/0005');
        $showResp->assertSee('Nik Siti Nurhaliza');

        // Cetak PK-RK-61 official printable page
        $cetakResp = $this->actingAs($this->adminJajahanKotaBharu)->get(route('action-list.cetak', $actionList->id));
        $cetakResp->assertStatus(200);
        $cetakResp->assertSee('ACTION LIST');
        $cetakResp->assertSee('PK-RK-61');
        $cetakResp->assertSee('JAJAHAN');
        $cetakResp->assertSee('KOTA BHARU');
        $cetakResp->assertSee('Nik Siti Nurhaliza');
        $cetakResp->assertSee('A. MAKLUMAT PELANGGAN');
        $cetakResp->assertSee('B. BUTIR-BUTIR PERKHIDMATAN');
        $cetakResp->assertSee('C. MAKLUMAT TEMUJANJI');
        $cetakResp->assertSee('D. MAKLUMAT PERKHIDMATAN YANG DIBERI');
        $cetakResp->assertSee('E. PENGAKUAN PELANGGAN');
    }

    public function test_admin_jajahan_can_update_action_list(): void
    {
        $actionList = ActionList::create([
            'kod_dokumen' => 'PK-RK-61',
            'no_bil' => 'AL/PP/2026/0010',
            'jajahan' => 'Pasir Puteh',
            'tarikh' => now()->toDateString(),
            'kategori_pelanggan' => 'Individu',
            'nama_pelanggan' => 'Pak Seman Penternak',
            'no_kp' => '700101035777',
            'bayaran' => 0.00,
            'status' => 'Deraf',
        ]);

        $updateData = [
            'no_bil' => 'AL/PP/2026/0010',
            'jajahan' => 'Pasir Puteh',
            'tarikh' => now()->toDateString(),
            'kategori_pelanggan' => 'Individu',
            'nama_pelanggan' => 'Pak Seman Penternak Kemaskini',
            'no_kp' => '700101035777',
            'bayaran' => '50.00',
            'no_resit' => 'R-8899',
            'status' => 'Selesai',
        ];

        $response = $this->actingAs($this->adminJajahanPasirPuteh)->put(route('action-list.update', $actionList->id), $updateData);
        $response->assertRedirect(route('action-list.show', $actionList->id));

        $actionList->refresh();
        $this->assertEquals('Pak Seman Penternak Kemaskini', $actionList->nama_pelanggan);
        $this->assertEquals(50.00, $actionList->bayaran);
        $this->assertEquals('Selesai', $actionList->status);
    }

    public function test_super_admin_and_admin_jajahan_can_delete_action_list(): void
    {
        $actionList = ActionList::create([
            'kod_dokumen' => 'PK-RK-61',
            'no_bil' => 'AL/PP/2026/0099',
            'jajahan' => 'Pasir Puteh',
            'tarikh' => now()->toDateString(),
            'kategori_pelanggan' => 'Individu',
            'nama_pelanggan' => 'Penternak Untuk Dipadam',
        ]);

        $deleteResp = $this->actingAs($this->superAdmin)->delete(route('action-list.destroy', $actionList->id));
        $deleteResp->assertRedirect(route('action-list.index'));

        $this->assertDatabaseMissing('action_lists', [
            'id' => $actionList->id,
        ]);
    }

    public function test_api_cari_pelanggan_returns_matching_users(): void
    {
        $response = $this->actingAs($this->adminJajahanPasirPuteh)->getJson(route('action-list.api-cari-pelanggan', ['query' => substr($this->penternak->name, 0, 5)]));
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => $this->penternak->name,
            'ic_number' => $this->penternak->ic_number,
        ]);
    }

    public function test_api_semak_pelanggan_lengkap_returns_cross_module_data(): void
    {
        // 1. Create/Update Pemunya & EPTR Ternakan
        $pemunya = \App\Models\Pemunya::updateOrCreate(
            ['no_kp' => $this->penternak->ic_number],
            [
                'user_id' => $this->penternak->id,
                'nama' => $this->penternak->name,
                'no_telefon' => '019-9887766',
                'alamat' => 'Kampung Gong Chapa',
                'jajahan' => 'Pasir Puteh',
                'daerah' => 'Jajahan Pasir Puteh',
                'mukim' => 'Padang Pak Amat',
                'poskod' => '16800',
            ]
        );

        $ternakan = \App\Models\Ternakan::create([
            'pemunya_id' => $pemunya->id,
            'no_tag' => 'MY-KEL-2026-9988',
            'jenis_ternakan' => 'Lembu',
            'baka' => 'Brakmas',
            'jantina' => 'Betina',
            'jajahan' => 'Pasir Puteh',
            'status' => 'Aktif',
            'status_kelulusan' => 'Diluluskan',
            'lokasi_kandang' => 'Kandang Gong Chapa',
        ]);

        // 2. Create EPU Ladang
        $ladang = \App\Models\EpuLadang::create([
            'user_id' => $this->penternak->id,
            'nama_pemohon_atau_syarikat' => $this->penternak->name,
            'nama_ladang' => 'Ladang Ayam Chapa',
            'id_premis' => 'EPU-PP-001',
            'jajahan' => 'Pasir Puteh',
            'alamat_ladang' => 'Lot 123 Kampung Gong Chapa',
            'latitude' => 5.839212,
            'longitude' => 102.394123,
            'kapasiti_maksimum_unggas' => 5000,
            'status_ladang' => 'Aktif',
        ]);

        // 3. Create Pawah Perjanjian
        $pawah = \App\Models\PawahPerjanjian::create([
            'user_id' => $this->penternak->id,
            'no_perjanjian' => 'PW-PP-2026-0099',
            'nama_program' => 'Program Pawah Lembu Baka Kelantan',
            'jenis_pawah' => 'Pawah Lembu',
            'jajahan' => 'Pasir Puteh',
            'status' => 'Aktif',
            'tarikh_mula' => now()->toDateString(),
            'tarikh_tamat' => now()->addYears(3)->toDateString(),
            'bilangan_induk' => 2,
        ]);

        // 4. Test API Semak Pelanggan Lengkap
        $response = $this->actingAs($this->adminJajahanPasirPuteh)->getJson(route('action-list.api-semak-pelanggan-lengkap', ['ic_number' => $this->penternak->ic_number]));
        $response->assertStatus(200);
        $response->assertJson([
            'found' => true,
            'pelanggan' => [
                'nama' => $this->penternak->name,
                'no_kp' => $this->penternak->ic_number,
            ],
            'suggested_gps' => '5.839212, 102.394123',
        ]);

        $response->assertJsonFragment([
            'no_tag' => 'MY-KEL-2026-9988',
            'jenis_ternakan' => 'Lembu',
        ]);

        $response->assertJsonFragment([
            'no_perjanjian' => 'PW-PP-2026-0099',
        ]);

        $response->assertJsonFragment([
            'nama_ladang' => 'Ladang Ayam Chapa',
        ]);
    }

    public function test_admin_jajahan_can_store_action_list_with_smart_fields(): void
    {
        $formData = [
            'kod_dokumen' => 'PK-RK-61',
            'no_bil' => 'AL/PP/2026/0888',
            'jajahan' => 'Pasir Puteh',
            'tarikh' => '2026-09-24',
            'kategori_pelanggan' => 'Individu',
            'nama_pelanggan' => 'Mohd Azman bin Salleh',
            'no_kp' => $this->penternak->ic_number,
            'gps_koordinat' => '5.839212, 102.394123',
            'rawatan_lapangan' => '1',
            'pemantauan_pawah' => '1',
            'jenis_ternakan' => ['Lembu'],
            'ternakan_terlibat_ids' => [1, 2],
            'bil_ternakan' => 2,
            'status' => 'Selesai',
        ];

        $response = $this->actingAs($this->adminJajahanPasirPuteh)->post(route('action-list.store'), $formData);
        
        $actionList = ActionList::where('no_bil', 'AL/PP/2026/0888')->first();
        $this->assertNotNull($actionList);
        $this->assertEquals('5.839212, 102.394123', $actionList->gps_koordinat);
        $this->assertEquals([1, 2], $actionList->ternakan_terlibat_ids);
    }
}
