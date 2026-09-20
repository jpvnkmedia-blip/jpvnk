<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\EpuLadang;
use App\Models\EpuPermohonan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class EpuRoleWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_epu_jajahan_and_negeri_helpers()
    {
        $adminNegeri = User::where('role', 'admin_epu')->first();
        $adminJajahan = User::where('role', 'admin_epu_jajahan')->first();
        $pegawaiVerifikasi = User::where('role', 'pegawai_verifikasi_epu')->first();
        $pegawaiPelesen = User::where('role', 'pegawai_pelesen')->first();
        $penternak = User::where('role', 'penternak')->first();

        $this->assertTrue($adminNegeri->isAdminEpuNegeri());
        $this->assertFalse($adminNegeri->isAdminEpuJajahan());
        $this->assertTrue($adminNegeri->isAdminEpu());

        $this->assertTrue($adminJajahan->isAdminEpuJajahan());
        $this->assertFalse($adminJajahan->isAdminEpuNegeri());
        $this->assertTrue($adminJajahan->isAdminEpu());

        $this->assertTrue($pegawaiVerifikasi->isAdminEpuJajahan());
        $this->assertTrue($pegawaiPelesen->isAdminEpuNegeri());

        $this->assertFalse($penternak->isAdminEpu());
        $this->assertFalse($penternak->isAdminEpuNegeri());
        $this->assertFalse($penternak->isAdminEpuJajahan());
    }

    public function test_admin_epu_jajahan_scoping_on_farm_list()
    {
        $penternak = User::where('role', 'penternak')->first();
        $adminJajahanPP = User::where('role', 'admin_epu_jajahan')->where('jajahan', 'Pasir Puteh')->first();
        $adminNegeri = User::where('role', 'admin_epu')->first();

        // Create farms in different districts
        $ladangPP = EpuLadang::create([
            'user_id' => $penternak->id,
            'nama_pemohon_atau_syarikat' => 'Syarikat Unggas Pasir Puteh',
            'nama_ladang' => 'Ladang Pasir Puteh Makmur',
            'alamat_ladang' => 'Lot 100, Pasir Puteh',
            'jajahan' => 'Pasir Puteh',
            'kategori_unggas' => 'Ayam Pedaging',
            'kapasiti_ternakan' => 5000,
            'status_ladang' => 'Aktif',
            'status' => 'Aktif',
        ]);

        $ladangKB = EpuLadang::create([
            'user_id' => $penternak->id,
            'nama_pemohon_atau_syarikat' => 'Syarikat Unggas KB',
            'nama_ladang' => 'Ladang KB Sentosa',
            'alamat_ladang' => 'Lot 200, Kota Bharu',
            'jajahan' => 'Kota Bharu',
            'kategori_unggas' => 'Ayam Penelur',
            'kapasiti_ternakan' => 7000,
            'status_ladang' => 'Aktif',
            'status' => 'Aktif',
        ]);

        // Jajahan admin (Pasir Puteh) sees only Pasir Puteh farm
        $responseJajahan = $this->actingAs($adminJajahanPP)->get('/epu');
        $responseJajahan->assertStatus(200);
        $responseJajahan->assertSee('Ladang Pasir Puteh Makmur');
        $responseJajahan->assertDontSee('Ladang KB Sentosa');
        $responseJajahan->assertSee('Jajahan Pasir Puteh');

        // State admin sees both farms
        $responseNegeri = $this->actingAs($adminNegeri)->get('/epu');
        $responseNegeri->assertStatus(200);
        $responseNegeri->assertSee('Ladang Pasir Puteh Makmur');
        $responseNegeri->assertSee('Ladang KB Sentosa');
    }

    public function test_admin_epu_jajahan_verification_and_state_approval_flow()
    {
        Storage::fake('public');
        $penternak = User::where('role', 'penternak')->first();
        $adminJajahanPP = User::where('role', 'admin_epu_jajahan')->where('jajahan', 'Pasir Puteh')->first();
        $adminNegeri = User::where('role', 'admin_epu')->first();

        // 1. Pemohon mohon di Pasir Puteh
        $this->actingAs($penternak)->post('/epu/daftar-borang-a', [
            'nama_pemohon_atau_syarikat' => 'Penternak Unggas Tok Bali',
            'nama_ladang' => 'Ladang Tok Bali Organik',
            'jenis_unggas' => 'Ayam',
            'jurusan_aktiviti' => 'Pedaging',
            'kapasiti_maksimum_unggas' => 4000,
            'alamat_ladang' => 'Tok Bali, Pasir Puteh',
            'jajahan' => 'Pasir Puteh',
        ]);

        $ladang = EpuLadang::where('nama_ladang', 'Ladang Tok Bali Organik')->first();
        $this->assertNotNull($ladang);
        $permohonan = $ladang->permohonanTerkini;
        $this->assertNotNull($permohonan);

        // 2. Admin Jajahan Verifikasi
        $verifResp = $this->actingAs($adminJajahanPP)->post("/epu/permohonan/{$permohonan->id}/verifikasi", [
            'status_verifikasi' => 'Patuh',
            'catatan_verifikasi' => 'Premis disahkan patuh piawaian reban dan biosekuriti.',
        ]);
        $verifResp->assertRedirect();
        $permohonan->refresh();
        $this->assertEquals('Patuh', $permohonan->status_verifikasi);

        // 3. Admin Jajahan Hantar Penilaian Ladang
        $hantarResp = $this->actingAs($adminJajahanPP)->post("/epu/permohonan/{$permohonan->id}/hantar-penilaian", [
            'catatan_penilaian_ladang' => 'Disyorkan untuk kelulusan lesen penternakan unggas.',
        ]);
        $hantarResp->assertRedirect();
        $permohonan->refresh();
        $this->assertEquals('Dihantar ke Pegawai Pelesen', $permohonan->status_penilaian_ladang);

        // 4. Admin Jajahan TIDAK BOLEH meluluskan lesen secara terus (Strict separation)
        $jajahanApprovalResp = $this->actingAs($adminJajahanPP)->post("/epu/permohonan/{$permohonan->id}/keputusan-pelesen", [
            'keputusan' => 'Lulus',
            'catatan_pegawai' => 'Cuba luluskan',
        ]);
        $jajahanApprovalResp->assertStatus(403);

        // 5. Admin EPU Negeri meluluskan lesen
        $negeriApprovalResp = $this->actingAs($adminNegeri)->post("/epu/permohonan/{$permohonan->id}/keputusan-pelesen", [
            'keputusan' => 'Lulus',
            'catatan_pegawai' => 'Diluluskan oleh Pentadbiran EPU Negeri JPVNK.',
        ]);
        $negeriApprovalResp->assertRedirect();
        $permohonan->refresh();
        $this->assertEquals('Diluluskan', $permohonan->status);
        $this->assertEquals('Lulus', $permohonan->status_kelulusan_pelesen);

        // 6. Cetakan lesen dibenarkan untuk kedua-dua Admin Jajahan dan Negeri
        $this->actingAs($adminJajahanPP)->get("/epu/lesen-borang-b/{$permohonan->id}/cetak")->assertStatus(200);
        $this->actingAs($adminNegeri)->get("/epu/lesen-borang-b/{$permohonan->id}/cetak")->assertStatus(200);
    }
}
