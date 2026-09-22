<?php

namespace Tests\Feature;

use App\Models\InventoriItem;
use App\Models\InventoriPermohonan;
use App\Models\InventoriTransaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorPejabatSeparationOfDutiesTest extends TestCase
{
    use RefreshDatabase;

    private User $adminPejabat;
    private User $pegawaiPengesah;
    private User $superAdmin;
    private User $staf;
    private InventoriItem $pejabatItem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminPejabat = User::factory()->create([
            'name' => 'Pegawai Stor Pejabat',
            'email' => 'stor.pejabat@veterinar.kelantan.gov.my',
            'role' => 'admin_pejabat',
            'roles' => ['admin_pejabat'],
        ]);

        $this->pegawaiPengesah = User::factory()->create([
            'name' => 'Pegawai Pengesah Stor',
            'email' => 'pengesah.pejabat@veterinar.kelantan.gov.my',
            'role' => 'pegawai_pengesah_pejabat',
            'roles' => ['pegawai_pengesah_pejabat'],
        ]);

        $this->superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@veterinar.kelantan.gov.my',
            'role' => 'super_admin',
            'roles' => ['super_admin'],
        ]);

        $this->staf = User::factory()->create([
            'name' => 'Kakitangan JPVNK',
            'email' => 'staf@veterinar.kelantan.gov.my',
            'role' => 'staf',
            'roles' => ['staf'],
        ]);

        $this->pejabatItem = InventoriItem::create([
            'kod_item' => 'PJB-TEST-01',
            'nama_item' => 'Kertas A4 80gsm (Test)',
            'jenis_stor' => 'pejabat',
            'kategori' => 'Alat Tulis & Pejabat',
            'unit' => 'Rim',
            'kuantiti_semasa' => 50,
            'kuantiti_minimum' => 10,
            'harga_seunit' => 14.50,
            'status' => 'Mencukupi',
        ]);
    }

    public function test_admin_pejabat_can_view_create_form_and_store_new_item()
    {
        $response = $this->actingAs($this->adminPejabat)->get(route('inventori.pejabat.create'));
        $response->assertStatus(200);

        $postResponse = $this->actingAs($this->adminPejabat)->post(route('inventori.pejabat.store'), [
            'kod_item' => 'PJB-TEST-02',
            'nama_item' => 'Pen Mata Bulat Biru',
            'kategori' => 'Alat Tulis & Pejabat',
            'unit' => 'Kotak',
            'kuantiti_semasa' => 20,
            'kuantiti_minimum' => 5,
            'harga_seunit' => 12.00,
        ]);

        $postResponse->assertRedirect(route('inventori.pejabat.index'));
        $this->assertDatabaseHas('inventori_items', ['kod_item' => 'PJB-TEST-02']);
    }

    public function test_pegawai_pengesah_cannot_create_or_store_new_item()
    {
        $response = $this->actingAs($this->pegawaiPengesah)->get(route('inventori.pejabat.create'));
        $response->assertStatus(403);

        $postResponse = $this->actingAs($this->pegawaiPengesah)->post(route('inventori.pejabat.store'), [
            'kod_item' => 'PJB-TEST-03',
            'nama_item' => 'Fail Poket',
            'kategori' => 'Alat Tulis & Pejabat',
            'unit' => 'Keping',
            'kuantiti_semasa' => 30,
            'kuantiti_minimum' => 10,
        ]);

        $postResponse->assertStatus(403);
        $this->assertDatabaseMissing('inventori_items', ['kod_item' => 'PJB-TEST-03']);
    }

    public function test_admin_pejabat_can_record_stock_transaction_and_loan()
    {
        $transResponse = $this->actingAs($this->adminPejabat)->post(route('inventori.transaksi.store', $this->pejabatItem->id), [
            'jenis_transaksi' => 'Stok Masuk',
            'kuantiti' => 10,
            'penerima_atau_pembekal' => 'Pembekal Jaya Sdn Bhd',
        ]);
        $transResponse->assertRedirect(route('inventori.show', $this->pejabatItem->id));
        $this->assertEquals(60, $this->pejabatItem->fresh()->kuantiti_semasa);

        $loanResponse = $this->actingAs($this->adminPejabat)->post(route('inventori.pinjaman.store', $this->pejabatItem->id), [
            'user_id' => $this->staf->id,
            'kuantiti' => 2,
            'tujuan_pinjaman' => 'Mesyuarat Bahagian',
            'tarikh_pinjam' => now()->toDateString(),
            'tarikh_jangka_pulang' => now()->addDays(2)->toDateString(),
        ]);
        $loanResponse->assertRedirect(route('inventori.show', $this->pejabatItem->id));
        $this->assertEquals(58, $this->pejabatItem->fresh()->kuantiti_semasa);
    }

    public function test_pegawai_pengesah_cannot_record_stock_transaction_or_loan()
    {
        $transResponse = $this->actingAs($this->pegawaiPengesah)->post(route('inventori.transaksi.store', $this->pejabatItem->id), [
            'jenis_transaksi' => 'Stok Masuk',
            'kuantiti' => 10,
            'penerima_atau_pembekal' => 'Pembekal',
        ]);
        $transResponse->assertStatus(403);

        $loanResponse = $this->actingAs($this->pegawaiPengesah)->post(route('inventori.pinjaman.store', $this->pejabatItem->id), [
            'user_id' => $this->staf->id,
            'kuantiti' => 2,
            'tujuan_pinjaman' => 'Mesyuarat Bahagian',
            'tarikh_pinjam' => now()->toDateString(),
            'tarikh_jangka_pulang' => now()->addDays(2)->toDateString(),
        ]);
        $loanResponse->assertStatus(403);
    }

    public function test_pegawai_pengesah_can_approve_and_reject_office_store_applications()
    {
        $permohonan = InventoriPermohonan::create([
            'no_permohonan' => 'REQ-TEST-001',
            'user_id' => $this->staf->id,
            'inventori_item_id' => $this->pejabatItem->id,
            'jenis_stor' => 'pejabat',
            'kuantiti_dimohon' => 5,
            'unit_bahagian' => 'Unit Pentadbiran',
            'tujuan_permohonan' => 'Cetakan Laporan Bulanan',
            'status' => 'Menunggu Kelulusan',
        ]);

        $approveResponse = $this->actingAs($this->pegawaiPengesah)->post(
            route('inventori.permohonan.status', $permohonan->id),
            [
                'tindakan' => 'lulus',
                'kuantiti_diluluskan' => 5,
                'catatan_pegawai' => 'Diluluskan mengikut keperluan.',
            ]
        );

        $approveResponse->assertSessionHas('success');
        $this->assertEquals('Diluluskan', $permohonan->fresh()->status);
        $this->assertEquals(5, $permohonan->fresh()->kuantiti_diluluskan);
    }

    public function test_admin_pejabat_cannot_approve_or_reject_office_store_applications()
    {
        $permohonan = InventoriPermohonan::create([
            'no_permohonan' => 'REQ-TEST-002',
            'user_id' => $this->staf->id,
            'inventori_item_id' => $this->pejabatItem->id,
            'jenis_stor' => 'pejabat',
            'kuantiti_dimohon' => 3,
            'unit_bahagian' => 'Unit Kewangan',
            'tujuan_permohonan' => 'Kerja Harian',
            'status' => 'Menunggu Kelulusan',
        ]);

        $approveResponse = $this->actingAs($this->adminPejabat)->post(
            route('inventori.permohonan.status', $permohonan->id),
            [
                'tindakan' => 'lulus',
                'kuantiti_diluluskan' => 3,
            ]
        );
        $approveResponse->assertStatus(403);

        $rejectResponse = $this->actingAs($this->adminPejabat)->post(
            route('inventori.permohonan.status', $permohonan->id),
            [
                'tindakan' => 'tolak',
                'catatan_pegawai' => 'Tidak boleh lulus',
            ]
        );
        $rejectResponse->assertStatus(403);

        $this->assertEquals('Menunggu Kelulusan', $permohonan->fresh()->status);
    }

    public function test_admin_pejabat_can_handover_approved_stock_and_deduct_inventory()
    {
        $permohonan = InventoriPermohonan::create([
            'no_permohonan' => 'REQ-TEST-003',
            'user_id' => $this->staf->id,
            'inventori_item_id' => $this->pejabatItem->id,
            'jenis_stor' => 'pejabat',
            'kuantiti_dimohon' => 10,
            'kuantiti_diluluskan' => 10,
            'unit_bahagian' => 'Unit Pentadbiran',
            'tujuan_permohonan' => 'Bekalan Pejabat',
            'status' => 'Diluluskan',
        ]);

        $initialStock = $this->pejabatItem->kuantiti_semasa; // 50

        $serahResponse = $this->actingAs($this->adminPejabat)->post(
            route('inventori.permohonan.status', $permohonan->id),
            [
                'tindakan' => 'serah',
                'kuantiti_diluluskan' => 10,
                'catatan_pegawai' => 'Stok telah diserahkan di kaunter stor.',
            ]
        );

        $serahResponse->assertSessionHas('success');
        $this->assertEquals('Telah Diambil / Diserahkan', $permohonan->fresh()->status);
        $this->assertEquals($initialStock - 10, $this->pejabatItem->fresh()->kuantiti_semasa);

        $this->assertDatabaseHas('inventori_transaksi', [
            'inventori_item_id' => $this->pejabatItem->id,
            'jenis_transaksi' => 'Stok Keluar',
            'kuantiti' => 10,
            'rujukan_dokumen' => $permohonan->no_permohonan,
        ]);
    }

    public function test_pegawai_pengesah_cannot_handover_stock()
    {
        $permohonan = InventoriPermohonan::create([
            'no_permohonan' => 'REQ-TEST-004',
            'user_id' => $this->staf->id,
            'inventori_item_id' => $this->pejabatItem->id,
            'jenis_stor' => 'pejabat',
            'kuantiti_dimohon' => 5,
            'kuantiti_diluluskan' => 5,
            'unit_bahagian' => 'Unit IT',
            'tujuan_permohonan' => 'Keperluan Mesyuarat',
            'status' => 'Diluluskan',
        ]);

        $serahResponse = $this->actingAs($this->pegawaiPengesah)->post(
            route('inventori.permohonan.status', $permohonan->id),
            [
                'tindakan' => 'serah',
                'kuantiti_diluluskan' => 5,
            ]
        );

        $serahResponse->assertStatus(403);
        $this->assertEquals('Diluluskan', $permohonan->fresh()->status);
        $this->assertEquals(50, $this->pejabatItem->fresh()->kuantiti_semasa);
    }
}
