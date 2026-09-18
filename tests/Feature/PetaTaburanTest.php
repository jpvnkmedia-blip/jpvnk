<?php

namespace Tests\Feature;

use App\Models\EpuLadang;
use App\Models\EpuPermohonan;
use App\Models\Pemunya;
use App\Models\Ternakan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetaTaburanTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_peta_taburan(): void
    {
        $response = $this->get(route('peta.taburan'));
        $response->assertRedirect(route('login'));

        $responseAlias = $this->get(route('pengarah.peta'));
        $responseAlias->assertRedirect(route('login'));
    }

    public function test_pengarah_or_pegawai_pelesen_can_view_peta_taburan(): void
    {
        $pengarah = User::factory()->create([
            'role' => 'pegawai_pelesen',
            'email' => 'pengarah@dvs.gov.my',
        ]);

        $response = $this->actingAs($pengarah)->get(route('peta.taburan'));
        $response->assertStatus(200);
        $response->assertSee('Peta Taburan Penternak', false);
        $response->assertSee('gisMap', false);
        $response->assertSee('Skrin Penuh', false);
        $response->assertSee('Penapis Taburan Penternak', false);
        $response->assertViewHas(['allMarkers', 'availableLivestockTypes', 'jajahanList', 'stats', 'jajahanStats']);
    }

    public function test_super_admin_and_staff_can_view_peta_taburan(): void
    {
        $admin = User::factory()->create([
            'role' => 'super_admin',
        ]);

        $response = $this->actingAs($admin)->get(route('pengarah.peta'));
        $response->assertStatus(200);
        $response->assertSee('Peta Taburan Penternak', false);
    }

    public function test_peta_taburan_aggregates_epu_and_eptr_data_correctly(): void
    {
        $user = User::factory()->create(['role' => 'super_admin']);

        // 1. Cipta Penternak EPU
        $penternakEpuUser = User::factory()->create(['role' => 'usahawan', 'name' => 'Ahmad Penternak Ayam']);
        $ladang = EpuLadang::create([
            'user_id' => $penternakEpuUser->id,
            'nama_pemohon_atau_syarikat' => 'Syarikat Ayam Segar Sdn Bhd',
            'nama_ladang' => 'Ladang Unggas Kota Bharu',
            'alamat_ladang' => 'Lot 123, Jalan Kemumin, 16100 Kota Bharu',
            'jajahan' => 'Kota Bharu',
            'daerah' => 'Kemumin',
            'latitude' => 6.1254,
            'longitude' => 102.2381,
            'kapasiti_maksimum_unggas' => 15000,
            'status_ladang' => 'Aktif',
        ]);

        EpuPermohonan::create([
            'epu_ladang_id' => $ladang->id,
            'no_rujukan_permohonan' => 'EPU-KB-2026-001',
            'jenis_permohonan' => 'Baru',
            'jenis_unggas' => 'Ayam Pedaging',
            'bilangan_semasa_unggas' => 12000,
            'status' => 'Diluluskan',
        ]);

        // 2. Cipta Penternak EPTR
        $penternakEptrUser = User::factory()->create(['role' => 'penternak', 'name' => 'Razak Penternak Lembu']);
        $pemunya = Pemunya::create([
            'user_id' => $penternakEptrUser->id,
            'nama' => 'Mohd Razak bin Ismail',
            'no_kp' => '850101035555',
            'no_telefon' => '0199998888',
            'alamat' => 'Kampung Gunong, 16300 Bachok',
            'jajahan' => 'Bachok',
            'daerah' => 'Gunong',
            'status' => 'Aktif',
        ]);

        Ternakan::create([
            'pemunya_id' => $pemunya->id,
            'no_tag' => 'BCK-0001',
            'jenis_ternakan' => 'Lembu',
            'baka' => 'Kedah-Kelantan',
            'jantina' => 'Betina',
            'umur' => '2 Tahun',
            'jajahan' => 'Bachok',
            'status' => 'Aktif',
            'status_kelulusan' => 'Diluluskan',
        ]);

        Ternakan::create([
            'pemunya_id' => $pemunya->id,
            'no_tag' => 'BCK-0002',
            'jenis_ternakan' => 'Kambing',
            'baka' => 'Boer',
            'jantina' => 'Jantan',
            'umur' => '1 Tahun',
            'jajahan' => 'Bachok',
            'status' => 'Aktif',
            'status_kelulusan' => 'Diluluskan',
        ]);

        $response = $this->actingAs($user)->get(route('peta.taburan'));
        $response->assertStatus(200);

        $markers = $response->viewData('allMarkers');
        $this->assertCount(2, $markers);

        // Semak data EPU
        $epuMarker = collect($markers)->firstWhere('modul', 'EPU');
        $this->assertNotNull($epuMarker);
        $this->assertEquals('Syarikat Ayam Segar Sdn Bhd', $epuMarker['nama_penternak']);
        $this->assertEquals('Kota Bharu', $epuMarker['jajahan']);
        $this->assertEquals(12000, $epuMarker['jumlah_ternakan']);
        $this->assertContains('Ayam Pedaging', $epuMarker['jenis_ternakan_list']);

        // Semak data EPTR
        $eptrMarker = collect($markers)->firstWhere('modul', 'EPTR');
        $this->assertNotNull($eptrMarker);
        $this->assertEquals('Mohd Razak bin Ismail', $eptrMarker['nama_penternak']);
        $this->assertEquals('Bachok', $eptrMarker['jajahan']);
        $this->assertEquals(2, $eptrMarker['jumlah_ternakan']);
        $this->assertContains('Lembu', $eptrMarker['jenis_ternakan_list']);
        $this->assertContains('Kambing', $eptrMarker['jenis_ternakan_list']);

        // Semak KPI stats
        $stats = $response->viewData('stats');
        $this->assertEquals(2, $stats['total_penternak']);
        $this->assertEquals(1, $stats['total_epu']);
        $this->assertEquals(1, $stats['total_eptr']);
        $this->assertEquals(12000, $stats['total_unggas']);
        $this->assertEquals(2, $stats['total_ruminan']);
    }
}
