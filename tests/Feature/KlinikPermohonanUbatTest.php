<?php

namespace Tests\Feature;

use App\Models\InventoriItem;
use App\Models\InventoriPermohonan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KlinikPermohonanUbatTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminKlinik;
    protected User $adminUbat;
    protected User $orangAwam;
    protected InventoriItem $ubatItem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminKlinik = User::factory()->create([
            'name' => 'Dr. Aina (Veterinar)',
            'role' => 'admin_klinik',
            'email' => 'dr.aina@jpvnk.gov.my',
            'jajahan' => 'Kota Bharu',
            'phone' => '019-8765432',
        ]);

        $this->adminUbat = User::factory()->create([
            'name' => 'Pegawai Stor Farmasi',
            'role' => 'admin_ubat',
            'email' => 'farmasi@jpvnk.gov.my',
        ]);

        $this->orangAwam = User::factory()->create([
            'role' => 'orang_awam',
            'email' => 'awam@example.com',
        ]);

        $this->ubatItem = InventoriItem::create([
            'kod_item' => 'UBT-VAK-001',
            'nama_item' => 'Vaksin Rabies & Tricat Trio',
            'jenis_stor' => 'ubat',
            'kategori' => 'Vaksinasi Haiwan',
            'unit' => 'vial',
            'kuantiti_semasa' => 50,
            'kuantiti_minimum' => 10,
            'harga_seunit' => 25.00,
            'pembekal_utama' => 'PharmaVet Malaysia',
            'lokasi_rak' => 'Peti Sejuk A-02',
            'jajahan' => 'Ibu Pejabat Kota Bharu',
            'status' => 'Mencukupi',
        ]);
    }

    public function test_admin_klinik_can_access_permohonan_ubat_index(): void
    {
        $response = $this->actingAs($this->adminKlinik)
            ->get(route('klinik.permohonan_ubat.index'));

        $response->assertOk();
        $response->assertSee('Permohonan Ubat &amp; Vaksin ke Stor Farmasi', false);
        $response->assertSee('Mohon Ubat / Vaksin Baru');
    }

    public function test_admin_klinik_can_access_permohonan_ubat_create(): void
    {
        $response = $this->actingAs($this->adminKlinik)
            ->get(route('klinik.permohonan_ubat.create'));

        $response->assertOk();
        $response->assertSee('Vaksin Rabies &amp; Tricat Trio', false);
        $response->assertSee('UBT-VAK-001');
    }

    public function test_admin_klinik_can_submit_medicine_request_to_pharmacy_store(): void
    {
        $postData = [
            'inventori_item_id' => $this->ubatItem->id,
            'kuantiti_dimohon' => 15,
            'klinik_jajahan' => 'Klinik Haiwan Ibu Pejabat JPVNK Kota Bharu',
            'tujuan_permohonan' => 'Keperluan vaksinasi rutin pesakit kucing & anjing di klinik',
            'tarikh_diperlukan' => now()->addDays(2)->format('Y-m-d'),
            'catatan_pemohon' => 'Simpanan rantaian sejuk (cold-chain 2-8°C)',
        ];

        $response = $this->actingAs($this->adminKlinik)
            ->post(route('klinik.permohonan_ubat.store'), $postData);

        $response->assertRedirect(route('klinik.permohonan_ubat.index'));

        $this->assertDatabaseHas('inventori_permohonan', [
            'user_id' => $this->adminKlinik->id,
            'inventori_item_id' => $this->ubatItem->id,
            'jenis_stor' => 'ubat',
            'kuantiti_dimohon' => 15,
            'unit_bahagian' => 'Klinik Haiwan Ibu Pejabat JPVNK Kota Bharu',
            'status' => 'Menunggu Kelulusan',
        ]);
    }

    public function test_admin_klinik_can_cancel_pending_medicine_request(): void
    {
        $permohonan = InventoriPermohonan::create([
            'no_permohonan' => 'REQ-KLN-UBT-20260920-9999',
            'user_id' => $this->adminKlinik->id,
            'inventori_item_id' => $this->ubatItem->id,
            'jenis_stor' => 'ubat',
            'kuantiti_dimohon' => 5,
            'unit_bahagian' => 'Klinik Haiwan Kota Bharu',
            'tujuan_permohonan' => 'Ujian stok tambahan',
            'tarikh_diperlukan' => now()->toDateString(),
            'status' => 'Menunggu Kelulusan',
        ]);

        $response = $this->actingAs($this->adminKlinik)
            ->post(route('klinik.permohonan_ubat.batal', $permohonan->id));

        $response->assertRedirect();

        $this->assertDatabaseHas('inventori_permohonan', [
            'id' => $permohonan->id,
            'status' => 'Dibatalkan',
        ]);
    }

    public function test_admin_ubat_can_approve_and_issue_clinic_medicine_request(): void
    {
        $permohonan = InventoriPermohonan::create([
            'no_permohonan' => 'REQ-KLN-UBT-20260920-8888',
            'user_id' => $this->adminKlinik->id,
            'inventori_item_id' => $this->ubatItem->id,
            'jenis_stor' => 'ubat',
            'kuantiti_dimohon' => 10,
            'unit_bahagian' => 'Klinik Haiwan Ibu Pejabat Kota Bharu',
            'tujuan_permohonan' => 'Vaksinasi berkala klinik',
            'status' => 'Menunggu Kelulusan',
        ]);

        // 1. Luluskan
        $lulusResponse = $this->actingAs($this->adminUbat)
            ->post(route('inventori.permohonan.status', $permohonan->id), [
                'tindakan' => 'lulus',
                'kuantiti_diluluskan' => 10,
                'catatan_pegawai' => 'Stok sedia untuk diserahkan.',
            ]);

        $lulusResponse->assertRedirect();
        $this->assertEquals('Diluluskan', $permohonan->fresh()->status);

        // 2. Serahkan stok
        $serahResponse = $this->actingAs($this->adminUbat)
            ->post(route('inventori.permohonan.status', $permohonan->id), [
                'tindakan' => 'serah',
                'kuantiti_diluluskan' => 10,
                'catatan_pegawai' => 'Stok telah diserahkan kepada staf klinik.',
            ]);

        $serahResponse->assertRedirect();
        $this->assertEquals('Telah Diambil / Diserahkan', $permohonan->fresh()->status);

        // Semak baki stok ditolak (50 - 10 = 40)
        $this->assertEquals(40, $this->ubatItem->fresh()->kuantiti_semasa);

        // Semak rekod lejar transaksi stok keluar
        $this->assertDatabaseHas('inventori_transaksi', [
            'inventori_item_id' => $this->ubatItem->id,
            'jenis_transaksi' => 'Stok Keluar',
            'kuantiti' => 10,
            'baki_selepas' => 40,
        ]);
    }

    public function test_public_user_cannot_access_clinic_medicine_request_module(): void
    {
        $response = $this->actingAs($this->orangAwam)
            ->get(route('klinik.permohonan_ubat.index'));

        $response->assertForbidden();
    }
}