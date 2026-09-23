<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\EpuLadang;
use App\Models\EpuPermohonan;
use App\Models\UserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class EpuEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_epu_creation_sends_notifications_to_admin_epu_and_ppvj()
    {
        Storage::fake('public');
        $penternak = User::where('role', 'penternak')->first();
        $adminEpu = User::where('role', 'admin_epu')->first();
        $ppvjKotaBharu = User::where('role', 'admin_epu_jajahan')->where('jajahan', 'Kota Bharu')->first();

        // Clear existing notifications
        UserNotification::truncate();

        $response = $this->actingAs($penternak)->post('/epu/daftar-borang-a', [
            'nama_pemohon_atau_syarikat' => 'Ternakan Unggas Berkat',
            'no_syarikat_atau_ssm' => '202602001122',
            'nama_ladang' => 'Ladang Ayam Berkat KB',
            'jenis_unggas' => 'Ayam',
            'jurusan_aktiviti' => 'Pedaging',
            'kapasiti_maksimum_unggas' => 12000,
            'bilangan_semasa_unggas' => 10000,
            'alamat_ladang' => 'Lot 554, Mukim Pendek',
            'jajahan' => 'Kota Bharu',
            'sistem_reban' => 'Tertutup',
            'status_pemilikan_tanah' => 'Milik Sendiri',
        ]);

        $response->assertRedirect();

        // Admin EPU should have received notification
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $adminEpu->id,
            'type' => 'epu',
        ]);

        // PPVJ Kota Bharu should have received notification
        if ($ppvjKotaBharu) {
            $this->assertDatabaseHas('user_notifications', [
                'user_id' => $ppvjKotaBharu->id,
                'type' => 'epu',
            ]);
        }
    }

    public function test_pegawai_pelesen_and_pengarah_cannot_alter_ppvj_verification_status()
    {
        Storage::fake('public');
        $penternak = User::where('role', 'penternak')->first();
        $pegawaiPelesen = User::where('role', 'pegawai_pelesen')->first();
        $pengarah = User::where('role', 'pengarah')->first();

        // Register farm
        $ladang = EpuLadang::create([
            'user_id' => $penternak->id,
            'nama_pemohon_atau_syarikat' => 'Unggas Jaya Sdn Bhd',
            'nama_ladang' => 'Ladang Unggas Jaya',
            'alamat_ladang' => 'Lot 123, Machang',
            'jajahan' => 'Machang',
            'kategori_unggas' => 'Ayam Pedaging',
            'kapasiti_ternakan' => 5000,
            'status_ladang' => 'Aktif',
            'status' => 'Aktif',
        ]);

        $permohonan = EpuPermohonan::create([
            'epu_ladang_id' => $ladang->id,
            'no_rujukan_permohonan' => 'EPU/2026/09/TEST01',
            'jenis_permohonan' => 'Lesen Baharu',
            'jenis_unggas' => 'Ayam',
            'jurusan_aktiviti' => 'Pedaging',
            'bilangan_semasa_unggas' => 4000,
            'kapasiti_ladang' => 5000,
            'status' => 'Dihantar',
            'status_verifikasi' => 'Menunggu Verifikasi',
        ]);

        // Pegawai Pelesen attempts to alter PPVJ verification status -> 403 Forbidden
        $responsePelesen = $this->actingAs($pegawaiPelesen)->post("/epu/permohonan/{$permohonan->id}/verifikasi", [
            'status_verifikasi' => 'Patuh',
            'catatan_verifikasi' => 'Cuba ubah oleh Pegawai Pelesen',
        ]);
        $responsePelesen->assertStatus(403);

        // Pengarah attempts to alter PPVJ verification status -> 403 Forbidden
        if ($pengarah) {
            $responsePengarah = $this->actingAs($pengarah)->post("/epu/permohonan/{$permohonan->id}/verifikasi", [
                'status_verifikasi' => 'Patuh',
                'catatan_verifikasi' => 'Cuba ubah oleh Pengarah',
            ]);
            $responsePengarah->assertStatus(403);
        }
    }

    public function test_verification_and_pelesen_decision_cannot_be_altered_after_full_approval()
    {
        Storage::fake('public');
        $superAdmin = User::where('role', 'super_admin')->first();
        $adminEpu = User::where('role', 'admin_epu')->first();
        $penternak = User::where('role', 'penternak')->first();

        // Create an already fully approved permohonan
        $ladang = EpuLadang::create([
            'user_id' => $penternak->id,
            'nama_pemohon_atau_syarikat' => 'Unggas Selesai',
            'nama_ladang' => 'Ladang Unggas Selesai',
            'alamat_ladang' => 'Lot 999, Kota Bharu',
            'jajahan' => 'Kota Bharu',
            'kategori_unggas' => 'Ayam Pedaging',
            'kapasiti_ternakan' => 5000,
            'status_ladang' => 'Aktif',
            'status' => 'Aktif',
        ]);

        $permohonan = EpuPermohonan::create([
            'epu_ladang_id' => $ladang->id,
            'no_rujukan_permohonan' => 'EPU/2026/09/APPROVED01',
            'jenis_permohonan' => 'Lesen Baharu',
            'jenis_unggas' => 'Ayam',
            'jurusan_aktiviti' => 'Pedaging',
            'bilangan_semasa_unggas' => 5000,
            'kapasiti_ladang' => 5000,
            'status' => 'Diluluskan',
            'status_verifikasi' => 'Patuh',
            'status_kelulusan_pelesen' => 'Lulus',
            'tarikh_kelulusan' => now(),
            'diluluskan_oleh' => $superAdmin->id,
        ]);

        // Attempt to alter PPVJ verification on approved application -> 403
        $respVerif = $this->actingAs($adminEpu)->post("/epu/permohonan/{$permohonan->id}/verifikasi", [
            'status_verifikasi' => 'Tidak Patuh',
            'catatan_verifikasi' => 'Cuba ubah selepas kelulusan',
        ]);
        $respVerif->assertStatus(403);

        // Attempt to hantar penilaian on approved application -> 403
        $respHantar = $this->actingAs($adminEpu)->post("/epu/permohonan/{$permohonan->id}/hantar-penilaian", [
            'catatan_penilaian_ladang' => 'Cuba hantar semula',
        ]);
        $respHantar->assertStatus(403);

        // Attempt to alter pelesen decision on approved application -> 403
        $respDecision = $this->actingAs($superAdmin)->post("/epu/permohonan/{$permohonan->id}/keputusan-pelesen", [
            'keputusan' => 'Gagal',
            'catatan_pegawai' => 'Cuba batalkan kelulusan',
        ]);
        $respDecision->assertStatus(403);
    }

    public function test_user_signature_upload_in_user_management_and_borang_a_print()
    {
        Storage::fake('public');
        $superAdmin = User::where('role', 'super_admin')->first();

        // 1. Create a Pegawai Pelesen with signature image
        $signatureFile = UploadedFile::fake()->image('tandatangan_pelesen.png', 200, 80);

        $responseCreate = $this->actingAs($superAdmin)->post(route('users.store'), [
            'name' => 'Dato Dr Pelesen Negeri',
            'email' => 'pelesen.negeri@dvs.gov.my',
            'ic_number' => '750101035511',
            'phone' => '019-9998811',
            'address' => 'Ibu Pejabat JPVNK Kota Bharu',
            'jajahan' => 'Kota Bharu',
            'jawatan' => 'Pegawai Veterinar Negeri',
            'bahagian_unit' => 'Bahagian Perladangan Unggas',
            'roles' => ['pegawai_pelesen'],
            'status' => 'Aktif',
            'signature' => $signatureFile,
        ]);

        $responseCreate->assertRedirect(route('users.index'));
        $newPelesen = User::where('email', 'pelesen.negeri@dvs.gov.my')->first();
        $this->assertNotNull($newPelesen);
        $this->assertNotNull($newPelesen->signature);
        Storage::disk('public')->assertExists($newPelesen->signature);

        // 2. View user profile to verify signature display
        $showResp = $this->actingAs($superAdmin)->get(route('users.show', $newPelesen->id));
        $showResp->assertStatus(200);
        $showResp->assertSee(asset('storage/' . $newPelesen->signature));

        // 3. Create approved EPU application approved by this pelesen
        $ladang = EpuLadang::create([
            'user_id' => $superAdmin->id,
            'nama_pemohon_atau_syarikat' => 'Ladang Mega Unggas',
            'nama_ladang' => 'Mega Unggas KB',
            'alamat_ladang' => 'Lot 77, Kota Bharu',
            'jajahan' => 'Kota Bharu',
            'kategori_unggas' => 'Ayam Pedaging',
            'kapasiti_ternakan' => 20000,
            'status_ladang' => 'Aktif',
            'status' => 'Aktif',
        ]);

        $permohonan = EpuPermohonan::create([
            'epu_ladang_id' => $ladang->id,
            'no_rujukan_permohonan' => 'EPU/2026/09/MEGA01',
            'jenis_permohonan' => 'Lesen Baharu',
            'jenis_unggas' => 'Ayam',
            'jurusan_aktiviti' => 'Pedaging',
            'bilangan_semasa_unggas' => 15000,
            'kapasiti_ladang' => 20000,
            'status' => 'Diluluskan',
            'status_verifikasi' => 'Patuh',
            'status_kelulusan_pelesen' => 'Lulus',
            'tarikh_kelulusan' => now(),
            'diluluskan_oleh' => $newPelesen->id,
        ]);

        // 4. Admin EPU prints Borang A
        $adminEpu = User::where('role', 'admin_epu')->first();
        $printResp = $this->actingAs($adminEpu)->get(route('epu.cetak-borang-a', $ladang->id));
        $printResp->assertStatus(200);

        // Assert signature is rendered in Borang A
        $printResp->assertSee(asset('storage/' . $newPelesen->signature));
        $printResp->assertSee('Dato Dr Pelesen Negeri');

        // Assert '61' in header is removed
        $printResp->assertDontSee('<span class="text-sm">61</span>', false);

        // 5. Test updating signature in User Management
        $newSignatureFile = UploadedFile::fake()->image('tandatangan_baru.png', 220, 90);
        $updateResp = $this->actingAs($superAdmin)->put(route('users.update', $newPelesen->id), [
            'name' => 'Dato Dr Pelesen Negeri Dikemaskini',
            'email' => 'pelesen.negeri@dvs.gov.my',
            'ic_number' => '750101035511',
            'phone' => '019-9998811',
            'address' => 'Ibu Pejabat JPVNK Kota Bharu',
            'jajahan' => 'Kota Bharu',
            'jawatan' => 'Pengarah Kanan Veterinar',
            'bahagian_unit' => 'Ibu Pejabat JPVNK',
            'roles' => ['pegawai_pelesen'],
            'status' => 'Aktif',
            'signature' => $newSignatureFile,
        ]);
        $updateResp->assertRedirect(route('users.index'));
        $newPelesen->refresh();
        $this->assertEquals('Dato Dr Pelesen Negeri Dikemaskini', $newPelesen->name);
        Storage::disk('public')->assertExists($newPelesen->signature);

        // 6. Test Cetak Lesen EPU (Borang B) includes digital signature
        $lesenResp = $this->actingAs($adminEpu)->get(route('epu.cetak-lesen', $permohonan->id));
        $lesenResp->assertStatus(200);
        $lesenResp->assertSee(asset('storage/' . $newPelesen->signature));
        $lesenResp->assertSee('Dato Dr Pelesen Negeri Dikemaskini');
    }

    public function test_borang_a_does_not_render_digital_signature_if_application_is_not_approved()
    {
        Storage::fake('public');
        $superAdmin = User::where('role', 'super_admin')->first();
        $adminEpu = User::where('role', 'admin_epu')->first();
        $penternak = User::where('role', 'penternak')->first();

        // Create a pelesen with signature
        $pelesen = User::create([
            'name' => 'Dr Pegawai Pelesen DVS',
            'email' => 'pelesen.test@dvs.gov.my',
            'ic_number' => '800101035544',
            'phone' => '019-9998844',
            'address' => 'Kota Bharu',
            'role' => 'pegawai_pelesen',
            'roles' => ['pegawai_pelesen'],
            'status' => 'Aktif',
            'signature' => 'signatures/pelesen_test.png',
        ]);

        $ladang = EpuLadang::create([
            'user_id' => $penternak->id,
            'nama_pemohon_atau_syarikat' => 'Ladang Belum Lulus',
            'nama_ladang' => 'Ladang Ayam Segar',
            'alamat_ladang' => 'Lot 101, Tumpat',
            'jajahan' => 'Tumpat',
            'kategori_unggas' => 'Ayam Pedaging',
            'kapasiti_ternakan' => 5000,
            'status_ladang' => 'Aktif',
            'status' => 'Aktif',
        ]);

        $permohonan = EpuPermohonan::create([
            'epu_ladang_id' => $ladang->id,
            'no_rujukan_permohonan' => 'EPU/2026/09/PENDING01',
            'jenis_permohonan' => 'Lesen Baharu',
            'jenis_unggas' => 'Ayam',
            'jurusan_aktiviti' => 'Pedaging',
            'bilangan_semasa_unggas' => 3000,
            'kapasiti_ladang' => 5000,
            'status' => 'Dihantar', // Belum lulus
            'status_verifikasi' => 'Menunggu Verifikasi',
        ]);

        $response = $this->actingAs($adminEpu)->get(route('epu.cetak-borang-a', $ladang->id));
        $response->assertStatus(200);

        // Signature and name must NOT be rendered when status is not Diluluskan
        $response->assertDontSee(asset('storage/' . $pelesen->signature));
        $response->assertDontSee('Dr Pegawai Pelesen DVS');
    }

    public function test_borang_a_can_only_be_viewed_by_admin_epu_jajahan_admin_epu_negeri_and_super_admin()
    {
        Storage::fake('public');
        $superAdmin = User::where('role', 'super_admin')->first();
        $adminEpuNegeri = User::where('role', 'admin_epu')->first();
        $adminEpuJajahan = User::where('role', 'admin_epu_jajahan')->first();
        $penternak = User::where('role', 'penternak')->first();
        $pegawaiPelesen = User::where('role', 'pegawai_pelesen')->first();

        $ladang = EpuLadang::create([
            'user_id' => $penternak->id,
            'nama_pemohon_atau_syarikat' => 'Ladang Hak Akses',
            'nama_ladang' => 'Ladang Akses KB',
            'alamat_ladang' => 'Lot 88, Kota Bharu',
            'jajahan' => 'Kota Bharu',
            'kategori_unggas' => 'Ayam Pedaging',
            'kapasiti_ternakan' => 5000,
            'status_ladang' => 'Aktif',
            'status' => 'Aktif',
        ]);

        EpuPermohonan::create([
            'epu_ladang_id' => $ladang->id,
            'no_rujukan_permohonan' => 'EPU/2026/09/AKSES01',
            'jenis_permohonan' => 'Lesen Baharu',
            'jenis_unggas' => 'Ayam',
            'jurusan_aktiviti' => 'Pedaging',
            'bilangan_semasa_unggas' => 5000,
            'kapasiti_ladang' => 5000,
            'status' => 'Diluluskan',
            'status_verifikasi' => 'Patuh',
            'status_kelulusan_pelesen' => 'Lulus',
        ]);

        // 1. Super Admin can view -> 200
        $this->actingAs($superAdmin)->get(route('epu.cetak-borang-a', $ladang->id))->assertStatus(200);

        // 2. Admin EPU Negeri can view -> 200
        $this->actingAs($adminEpuNegeri)->get(route('epu.cetak-borang-a', $ladang->id))->assertStatus(200);

        // 3. Admin EPU Jajahan can view -> 200
        if ($adminEpuJajahan) {
            $this->actingAs($adminEpuJajahan)->get(route('epu.cetak-borang-a', $ladang->id))->assertStatus(200);
        }

        // 4. Penternak cannot view -> 403 Forbidden
        $this->actingAs($penternak)->get(route('epu.cetak-borang-a', $ladang->id))->assertStatus(403);

        // 5. Pegawai Pelesen cannot view -> 403 Forbidden
        if ($pegawaiPelesen) {
            $this->actingAs($pegawaiPelesen)->get(route('epu.cetak-borang-a', $ladang->id))->assertStatus(403);
        }
    }
}
