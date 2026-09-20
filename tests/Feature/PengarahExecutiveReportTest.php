<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\EpuLadang;
use App\Models\EpuPermohonan;
use App\Models\Ternakan;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengarahExecutiveReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $pengarah;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->pengarah = User::where('role', 'pengarah')->first();
        if (!$this->pengarah) {
            $this->pengarah = User::factory()->create([
                'name' => 'Dr. Roslan bin Abdul Wahid',
                'email' => 'pengarah@dvs.gov.my',
                'role' => 'pengarah',
                'roles' => ['pengarah', 'pegawai_pelesen'],
                'jajahan' => 'Kota Bharu',
                'status' => 'Aktif',
            ]);
        }

        $this->regularUser = User::where('role', 'penternak')->first();
        if (!$this->regularUser) {
            $this->regularUser = User::factory()->create([
                'name' => 'Ahmad Penternak',
                'email' => 'ahmad@example.com',
                'role' => 'penternak',
                'roles' => ['penternak'],
                'jajahan' => 'Pasir Mas',
                'status' => 'Aktif',
            ]);
        }
    }

    public function test_pengarah_role_definition_and_authorizations(): void
    {
        $roleDefs = UserController::getRoleDefinitions();
        $this->assertArrayHasKey('Pentadbiran & Pengurusan Sistem', $roleDefs);
        $this->assertArrayHasKey('pengarah', $roleDefs['Pentadbiran & Pengurusan Sistem']);

        $this->assertTrue($this->pengarah->isPengarah());
        $this->assertTrue($this->pengarah->isAdmin());
        $this->assertTrue($this->pengarah->isStaff());
        $this->assertTrue($this->pengarah->isPegawaiPelesen());
        $this->assertTrue($this->pengarah->canPerformKeputusanPelesen());
        $this->assertTrue($this->pengarah->canAccessPetaTaburan());
        $this->assertTrue($this->pengarah->canAccessEptr());
        $this->assertTrue($this->pengarah->canAccessPawah());
        $this->assertTrue($this->pengarah->canAccessEpu());
        $this->assertTrue($this->pengarah->canAccessNaimbif());
        $this->assertTrue($this->pengarah->canAccessKlinik());
        $this->assertTrue($this->pengarah->canAccessKursus());
        $this->assertTrue($this->pengarah->canAccessStorPejabat());
        $this->assertTrue($this->pengarah->canAccessStorUbat());
        $this->assertTrue($this->pengarah->canManageKenderaanFleet());
    }

    public function test_pengarah_can_access_executive_reports(): void
    {
        $response = $this->actingAs($this->pengarah)->get(route('pengarah.laporan'));

        $response->assertStatus(200);
        $response->assertViewIs('pengarah.laporan');
        $response->assertSee('Laporan Statistik &amp; Analitik Bersepadu', false);
        $response->assertSee('Pejabat Pengarah &amp; Pengurusan Eksekutif', false);
        $response->assertSee('EPTR Ruminan', false);
        $response->assertSee('EPU Unggas', false);
        $response->assertSee('NAIMbif Bridlot', false);
        $response->assertSee('Program Pawah', false);
        $response->assertSee('Klinik Veterinar', false);
        $response->assertSee('Kursus Penternakan', false);
        $response->assertSee('Stor &amp; Farmasi', false);
        $response->assertSee('Armada Fleet', false);
    }

    public function test_pengarah_can_approve_epu_license(): void
    {
        $ladang = EpuLadang::first();
        if (!$ladang) {
            $ladang = EpuLadang::create([
                'user_id' => $this->regularUser->id,
                'nama_ladang' => 'Ladang Ayam Berkat',
                'alamat_ladang' => 'Lot 100, Pasir Mas',
                'jajahan' => 'Pasir Mas',
                'daerah' => 'Lemal',
                'jenis_unggas' => 'Ayam',
                'jurusan_aktiviti' => 'Pedaging',
                'kapasiti_maksimum_unggas' => 10000,
                'bilangan_semasa_unggas' => 5000,
                'sistem_reban' => 'Tertutup',
                'status_pemilikan_tanah' => 'Milik Sendiri',
            ]);
        }

        $permohonan = EpuPermohonan::create([
            'epu_ladang_id' => $ladang->id,
            'no_rujukan_permohonan' => 'EPU-2026-TEST01',
            'jenis_permohonan' => 'Baru',
            'jenis_unggas' => 'Ayam Pedaging',
            'jurusan_aktiviti' => 'Pedaging',
            'kapasiti_ladang' => 8000,
            'bilangan_semasa_unggas' => 4000,
            'yuran_lesen' => 150.00,
            'status' => 'Menunggu Kelulusan Pelesen',
            'status_penilaian_ladang' => 'Dihantar ke Pegawai Pelesen',
        ]);

        $response = $this->actingAs($this->pengarah)->post(route('epu.keputusan-pelesen', $permohonan->id), [
            'keputusan' => 'Lulus',
            'syarat_khas_lesen' => 'Pematuhan biosekuriti ketat.',
            'catatan_pegawai' => 'Diluluskan oleh Pengarah.',
        ]);

        $response->assertSessionHasNoErrors();
        $permohonan->refresh();

        $this->assertEquals('Diluluskan', $permohonan->status);
        $this->assertEquals('Lulus', $permohonan->status_kelulusan_pelesen);
    }

    public function test_pengarah_can_process_epu_appeal(): void
    {
        $ladang = EpuLadang::first();

        $permohonan = EpuPermohonan::create([
            'epu_ladang_id' => $ladang->id,
            'no_rujukan_permohonan' => 'EPU-2026-TEST02',
            'jenis_permohonan' => 'Baru',
            'jenis_unggas' => 'Ayam Pedaging',
            'jurusan_aktiviti' => 'Pedaging',
            'kapasiti_ladang' => 8000,
            'bilangan_semasa_unggas' => 4000,
            'status' => 'Rayuan',
            'status_rayuan' => 'Menunggu Semakan Rayuan',
            'alasan_rayuan' => 'Penambahbaikan sistem kumbahan telah siap dilaksanakan.',
        ]);

        $response = $this->actingAs($this->pengarah)->post(route('epu.rayuan.proses', $permohonan->id), [
            'tindakan_rayuan' => 'Lulus Rayuan',
            'catatan_keputusan_rayuan' => 'Rayuan diterima selepas semakan laporan pembetulan.',
        ]);

        $response->assertSessionHasNoErrors();
        $permohonan->refresh();

        $this->assertEquals('Lulus Rayuan', $permohonan->status_rayuan);
        $this->assertEquals('Diluluskan', $permohonan->status);
    }

    public function test_non_staff_user_cannot_access_executive_reports(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('pengarah.laporan'));
        $response->assertStatus(403);
    }
}