<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Kenderaan;
use App\Models\KenderaanTempahan;
use App\Models\InventoriItem;
use App\Models\InventoriPermohonan;
use App\Models\Pemandu;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StorPejabatKenderaanSeparationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_pejabat_can_manage_stor_pejabat_but_blocked_from_kenderaan_fleet_and_approvals()
    {
        $adminPejabat = User::where('role', 'admin_pejabat')->first();
        $this->assertNotNull($adminPejabat);

        // 1. Admin Stor Pejabat boleh akses stor peralatan pejabat
        $this->actingAs($adminPejabat)->get(route('inventori.pejabat.index'))->assertStatus(200);
        $this->actingAs($adminPejabat)->get(route('inventori.pejabat.create'))->assertStatus(200);
        $this->actingAs($adminPejabat)->get(route('inventori.pejabat.permohonan'))->assertStatus(200);

        // 2. Helper check: Admin Stor Pejabat disekat daripada modul Kenderaan Rasmi
        $this->assertFalse($adminPejabat->canAccessKenderaan());
        $this->assertFalse($adminPejabat->canBookVehicle());
        $this->assertFalse($adminPejabat->canManageKenderaanFleet());

        // 3. Admin Stor Pejabat DISEKAT daripada kesemua laluan modul Kenderaan Rasmi (403 Forbidden)
        $this->actingAs($adminPejabat)->get(route('kenderaan.index'))->assertStatus(403);
        $this->actingAs($adminPejabat)->get(route('kenderaan.create'))->assertStatus(403);
        $this->actingAs($adminPejabat)->get(route('kenderaan.fleet'))->assertStatus(403);
        $this->actingAs($adminPejabat)->get(route('kenderaan.pemandu.index'))->assertStatus(403);

        // 4. Admin Stor Pejabat disekat daripada membuat permohonan tempahan kenderaan (403 Forbidden)
        $this->actingAs($adminPejabat)->post(route('kenderaan.store'), [
            'destinasi' => 'Machang',
            'tujuan_perjalanan' => 'Urusan rasmi',
            'tarikh_mula' => now()->toDateString(),
            'masa_mula' => '08:00',
            'tarikh_tamat' => now()->toDateString(),
            'masa_tamat' => '17:00',
            'bilangan_penumpang' => 2,
        ])->assertStatus(403);
    }

    public function test_admin_kenderaan_can_manage_fleet_and_approve_bookings_but_blocked_from_stor_pejabat()
    {
        $adminKenderaan = User::where('role', 'admin_kenderaan')->first();
        $this->assertNotNull($adminKenderaan);

        // 1. Admin Kenderaan boleh akses pengurusan fleet dan pemandu
        $this->actingAs($adminKenderaan)->get(route('kenderaan.fleet'))->assertStatus(200);
        $this->actingAs($adminKenderaan)->get(route('kenderaan.pemandu.index'))->assertStatus(200);
        $this->actingAs($adminKenderaan)->get(route('kenderaan.pemandu.create'))->assertStatus(302);

        // 2. Admin Kenderaan boleh meluluskan tempahan kenderaan
        $kenderaan = Kenderaan::where('status', 'Sedia')->first();
        $staf = User::where('role', 'staf')->first();
        $tempahan = KenderaanTempahan::create([
            'user_id' => $staf->id,
            'no_tempahan' => 'KND-TEST-002',
            'kenderaan_id' => $kenderaan->id,
            'destinasi' => 'Pasir Mas',
            'tujuan_perjalanan' => 'Pemeriksaan Lapangan',
            'tarikh_mula' => now()->toDateString(),
            'masa_mula' => '09:00',
            'tarikh_tamat' => now()->toDateString(),
            'masa_tamat' => '16:00',
            'bilangan_penumpang' => 3,
            'status' => 'Menunggu',
        ]);

        $response = $this->actingAs($adminKenderaan)->post(route('kenderaan.approve', $tempahan->id), [
            'status' => 'Diluluskan',
            'kenderaan_id' => $kenderaan->id,
            'pemandu_nama' => 'En. Razak Pemandu',
            'catatan_kelulusan' => 'Diluluskan oleh Admin Kenderaan',
        ]);
        $response->assertRedirect();
        $tempahan->refresh();
        $this->assertEquals('Diluluskan', $tempahan->status);
        $this->assertEquals($adminKenderaan->id, $tempahan->diluluskan_oleh);

        // 3. Admin Kenderaan DISEKAT daripada mentadbir Stor Peralatan Pejabat
        $this->actingAs($adminKenderaan)->get(route('inventori.pejabat.index'))->assertStatus(403);
        $this->actingAs($adminKenderaan)->get(route('inventori.pejabat.create'))->assertStatus(403);
        $this->actingAs($adminKenderaan)->get(route('inventori.pejabat.permohonan'))->assertStatus(403);

        // 4. Admin Kenderaan disekat daripada mendaftar barang stor pejabat
        $this->actingAs($adminKenderaan)->post(route('inventori.pejabat.store'), [
            'kod_item' => 'TEST-BLOCKED-01',
            'nama_item' => 'Item Blok',
            'kategori' => 'Alat Tulis & Pejabat',
            'unit' => 'Unit',
            'kuantiti_semasa' => 10,
            'kuantiti_minimum' => 2,
        ])->assertStatus(403);

        // 5. Admin Kenderaan DISEKAT daripada modul Program NAIMbif dan Permohonan Stor Staf
        $this->assertFalse($adminKenderaan->canAccessNaimbif());
        $this->assertFalse($adminKenderaan->canRequestInventori());
        $this->assertFalse($adminKenderaan->canRequestAlatanPejabat());

        // Laluan pentadbiran NAIMbif disekat (403)
        $this->actingAs($adminKenderaan)->get(route('naimbif.admin.index'))->assertStatus(403);
        $this->actingAs($adminKenderaan)->get(route('naimbif.admin.export'))->assertStatus(403);

        // Laluan Permohonan Stor Staf disekat (403)
        $this->actingAs($adminKenderaan)->get(route('inventori.permohonan.saya'))->assertStatus(403);
        $this->actingAs($adminKenderaan)->get(route('inventori.permohonan.pejabat.mohon'))->assertStatus(403);
        $this->actingAs($adminKenderaan)->get(route('inventori.permohonan.ubat.mohon'))->assertStatus(403);

        // Semak sidebar & dashboard tidak memaparkan Program NAIMbif dan Permohonan Stor Staf
        $dashboardResp = $this->actingAs($adminKenderaan)->get(route('dashboard'));
        $dashboardResp->assertStatus(200);
        $dashboardResp->assertDontSee('Program NAIMbif', false);
        $dashboardResp->assertDontSee('Permohonan Stor Staf', false);
        $dashboardResp->assertSee('Admin Kenderaan &amp; Fleet', false);
        $dashboardResp->assertSee('Kelulusan Tempahan Kenderaan', false);
        $dashboardResp->assertSee('Pengurusan Fleet Kenderaan', false);
    }

    public function test_super_admin_has_full_access_to_both_stor_pejabat_and_kenderaan()
    {
        $superAdmin = User::where('role', 'super_admin')->first();
        $this->assertNotNull($superAdmin);

        // Super Admin boleh akses Stor Pejabat
        $this->actingAs($superAdmin)->get(route('inventori.pejabat.index'))->assertStatus(200);
        $this->actingAs($superAdmin)->get(route('inventori.pejabat.create'))->assertStatus(200);
        $this->actingAs($superAdmin)->get(route('inventori.pejabat.permohonan'))->assertStatus(200);

        // Super Admin boleh akses Kenderaan & Fleet
        $this->actingAs($superAdmin)->get(route('kenderaan.fleet'))->assertStatus(200);
        $this->actingAs($superAdmin)->get(route('kenderaan.pemandu.index'))->assertStatus(200);
        $this->actingAs($superAdmin)->get(route('kenderaan.pemandu.create'))->assertStatus(302);
    }

    public function test_admin_klinik_can_manage_klinik_but_blocked_from_stor_staf_and_kenderaan_rasmi()
    {
        $adminKlinik = User::where('role', 'admin_klinik')->first();
        if (!$adminKlinik) {
            $adminKlinik = User::factory()->create(['role' => 'admin_klinik', 'name' => 'Dr. Aminah Veterinar']);
        }
        $this->assertNotNull($adminKlinik);

        // 1. Admin Klinik boleh akses modul Klinik Haiwan
        $this->assertTrue($adminKlinik->canAccessKlinik());
        $this->actingAs($adminKlinik)->get(route('klinik.index'))->assertStatus(200);
        $this->actingAs($adminKlinik)->get(route('klinik.create'))->assertStatus(200);

        // 2. Admin Klinik DISEKAT daripada Kenderaan Rasmi
        $this->assertFalse($adminKlinik->canAccessKenderaan());
        $this->assertFalse($adminKlinik->canBookVehicle());
        $this->actingAs($adminKlinik)->get(route('kenderaan.index'))->assertStatus(403);
        $this->actingAs($adminKlinik)->get(route('kenderaan.create'))->assertStatus(403);
        $this->actingAs($adminKlinik)->get(route('kenderaan.fleet'))->assertStatus(403);
        $this->actingAs($adminKlinik)->get(route('kenderaan.pemandu.index'))->assertStatus(403);

        // 3. Admin Klinik DISEKAT daripada Permohonan Stor Staf
        $this->assertFalse($adminKlinik->canRequestInventori());
        $this->assertFalse($adminKlinik->canRequestAlatanPejabat());
        $this->actingAs($adminKlinik)->get(route('inventori.permohonan.saya'))->assertStatus(403);
        $this->actingAs($adminKlinik)->get(route('inventori.permohonan.pejabat.mohon'))->assertStatus(403);
        $this->actingAs($adminKlinik)->get(route('inventori.permohonan.ubat.mohon'))->assertStatus(403);

        // 4. Semak Dashboard & Sidebar tiada menu/butang Kenderaan Rasmi dan Permohonan Stor Staf
        $dashboardResp = $this->actingAs($adminKlinik)->get(route('dashboard'));
        $dashboardResp->assertStatus(200);
        $dashboardResp->assertDontSee('Kenderaan Rasmi', false);
        $dashboardResp->assertDontSee('Permohonan Stor Staf', false);
        $dashboardResp->assertDontSee('Permohonan Stor Saya', false);
        $dashboardResp->assertSee('Admin Klinik Haiwan &amp; Rawatan', false);
        $dashboardResp->assertSee('Daftar Temujanji Rawatan', false);
        $dashboardResp->assertSee('Senarai Semua Temujanji', false);
    }
}
