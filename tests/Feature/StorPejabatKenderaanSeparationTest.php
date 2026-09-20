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

        // 2. Admin Stor Pejabat boleh buat tempahan kenderaan untuk urusan sendiri, tetapi DISEKAT daripada urusan pengurusan fleet & pemandu
        $this->actingAs($adminPejabat)->get(route('kenderaan.fleet'))->assertStatus(403);
        $this->actingAs($adminPejabat)->get(route('kenderaan.pemandu.index'))->assertStatus(403);
        $this->actingAs($adminPejabat)->get(route('kenderaan.pemandu.create'))->assertStatus(403);

        // 3. Admin Stor Pejabat disekat daripada meluluskan atau menolak tempahan kenderaan
        $kenderaan = Kenderaan::first();
        $tempahan = KenderaanTempahan::create([
            'user_id' => $adminPejabat->id,
            'no_tempahan' => 'KND-TEST-001',
            'kenderaan_id' => $kenderaan->id,
            'destinasi' => 'Machang',
            'tujuan_perjalanan' => 'Urusan rasmi',
            'tarikh_mula' => now()->toDateString(),
            'masa_mula' => '08:00',
            'tarikh_tamat' => now()->toDateString(),
            'masa_tamat' => '17:00',
            'bilangan_penumpang' => 2,
            'status' => 'Menunggu',
        ]);

        $this->actingAs($adminPejabat)->post(route('kenderaan.approve', $tempahan->id), [
            'status' => 'Diluluskan',
            'kenderaan_id' => $kenderaan->id,
        ])->assertStatus(403);

        $this->actingAs($adminPejabat)->post(route('kenderaan.tolak', $tempahan->id), [
            'sebab_tolak' => 'Ujian tolak',
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
}
