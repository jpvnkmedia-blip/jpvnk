<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\EpuLadang;
use App\Models\EpuPermohonan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class EpuFlowchartWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_epu_full_flowchart_workflow_success_path()
    {
        Storage::fake('public');
        $penternak = User::where('role', 'penternak')->first();
        $adminEpu = User::where('role', 'admin_epu')->first();
        $superAdmin = User::where('role', 'super_admin')->first();

        // 1. Pemohon membuat permohonan lesen (Borang A)
        $response = $this->actingAs($penternak)->post('/epu/daftar-borang-a', [
            'nama_pemohon_atau_syarikat' => 'Syarikat Ternak Unggas Maju Sdn Bhd',
            'no_syarikat_atau_ssm' => '202601009988',
            'nama_ladang' => 'Ladang Ayam Pedaging Kota Bharu',
            'jenis_unggas' => 'Ayam',
            'jurusan_aktiviti' => 'Pedaging',
            'kapasiti_maksimum_unggas' => 10000,
            'bilangan_semasa_unggas' => 8000,
            'alamat_ladang' => 'Lot 889, Mukim Peringat',
            'jajahan' => 'Kota Bharu',
            'daerah' => 'Peringat',
            'sistem_reban' => 'Tertutup',
            'status_pemilikan_tanah' => 'Milik Sendiri',
        ]);

        $response->assertRedirect();
        $ladang = EpuLadang::where('nama_ladang', 'Ladang Ayam Pedaging Kota Bharu')->first();
        $this->assertNotNull($ladang);
        $permohonan = $ladang->permohonanTerkini;
        $this->assertNotNull($permohonan);
        $this->assertEquals('Dihantar', $permohonan->status);

        // 2. PPVJ Verifikasi: Permohonan disahkan Lengkap
        $this->actingAs($adminEpu)->post("/epu/permohonan/{$permohonan->id}/verifikasi", [
            'status_verifikasi' => 'Lengkap',
            'catatan_verifikasi' => 'Semua dokumen dan pelan tapak lengkap.',
        ]);
        $permohonan->refresh();
        $this->assertEquals('Lengkap', $permohonan->status_verifikasi);
        $this->assertEquals('Diterima PPVJ', $permohonan->status);

        // 3. PPVJ Verifikasi Tapak: Disahkan Patuh Piawaian
        $this->actingAs($adminEpu)->post("/epu/permohonan/{$permohonan->id}/verifikasi", [
            'status_verifikasi' => 'Patuh',
            'catatan_verifikasi' => 'Reban tertutup mematuhi zon penampan dan tiada isu lalat/bau.',
        ]);
        $permohonan->refresh();
        $this->assertEquals('Patuh', $permohonan->status_verifikasi);

        // 4. PPVJ Hantar Penilaian ke Pegawai Pelesen
        $this->actingAs($adminEpu)->post("/epu/permohonan/{$permohonan->id}/hantar-penilaian", [
            'catatan_penilaian_ladang' => 'Disyorkan untuk kelulusan lesen.',
        ]);
        $permohonan->refresh();
        $this->assertEquals('Dihantar ke Pegawai Pelesen', $permohonan->status_penilaian_ladang);
        $this->assertEquals('Menunggu Kelulusan Pelesen', $permohonan->status);

        // 5. Pegawai Pelesen / Pengarah Meluluskan Permohonan
        $this->actingAs($superAdmin)->post("/epu/permohonan/{$permohonan->id}/keputusan-pelesen", [
            'keputusan' => 'Lulus',
            'syarat_khas_lesen' => 'Kekalkan sistem kawalan lalat dan biosekuriti.',
            'catatan_pegawai' => 'Permohonan memenuhi semua kehendak Enakmen.',
        ]);
        $permohonan->refresh();
        $this->assertEquals('Diluluskan', $permohonan->status);
        $this->assertEquals('Lulus', $permohonan->status_kelulusan_pelesen);

        // 6. Pemohon Muat Naik Resit Bayaran Fi
        $resit = UploadedFile::fake()->create('resit_fi_epu.pdf', 150, 'application/pdf');
        $this->actingAs($penternak)->post("/epu/permohonan/{$permohonan->id}/bayar-fi", [
            'no_resit_bayaran' => 'RES-ONLINE-8877',
            'resit_bayaran_fi' => $resit,
        ]);
        $permohonan->refresh();
        $this->assertEquals('RES-ONLINE-8877', $permohonan->no_resit_bayaran);
        $this->assertNotNull($permohonan->resit_bayaran_fi);

        // 7. Pegawai Sahkan Bayaran Fi
        $this->actingAs($adminEpu)->post("/epu/permohonan/{$permohonan->id}/sahkan-bayaran");
        $permohonan->refresh();
        $this->assertEquals('Selesai Bayar', $permohonan->status_bayaran_fi);

        // 8. Pemohon Tidak Boleh Cetak (Hanya PPVJ / Admin Negeri / Super Admin)
        $penternakPrintResponse = $this->actingAs($penternak)->get("/epu/lesen-borang-b/{$permohonan->id}/cetak");
        $penternakPrintResponse->assertStatus(403);

        // 9. Pegawai Verifikasi / Admin EPU Negeri Boleh Mencetak Lesen Borang B
        $printResponse = $this->actingAs($adminEpu)->get("/epu/lesen-borang-b/{$permohonan->id}/cetak");
        $printResponse->assertStatus(200);
        $printResponse->assertSee('BORANG B');
        $printResponse->assertSee('LESEN PERLADANGAN UNGGAS');
        $printResponse->assertSee('Syarikat Ternak Unggas Maju Sdn Bhd');
    }

    public function test_epu_rejection_appeal_and_approval_flow()
    {
        Storage::fake('public');
        $penternak = User::where('role', 'penternak')->first();
        $adminEpu = User::where('role', 'admin_epu')->first();
        $superAdmin = User::where('role', 'super_admin')->first();

        // 1. Daftar ladang
        $this->actingAs($penternak)->post('/epu/daftar-borang-a', [
            'nama_pemohon_atau_syarikat' => 'Penternak Itik Pasir Mas',
            'nama_ladang' => 'Ladang Itik Telur Pasir Mas',
            'jenis_unggas' => 'Itik',
            'jurusan_aktiviti' => 'Penelur',
            'kapasiti_maksimum_unggas' => 3000,
            'alamat_ladang' => 'Lot 102, Mukim Bunut Susu',
            'jajahan' => 'Pasir Mas',
        ]);

        $ladang = EpuLadang::where('nama_ladang', 'Ladang Itik Telur Pasir Mas')->first();
        $permohonan = $ladang->permohonanTerkini;

        // 2. PPVJ Verifikasi: Tidak Patuh (Keluarkan Notis Ketidakpatuhan)
        $this->actingAs($adminEpu)->post("/epu/permohonan/{$permohonan->id}/verifikasi", [
            'status_verifikasi' => 'Tidak Patuh',
            'catatan_verifikasi' => 'Kawasan kolam kumbahan tidak diselenggara.',
            'tindakan_penambahbaikan' => 'Sila bersihkan kolam kumbahan dan pasang jaring biosekuriti.',
        ]);
        $permohonan->refresh();
        $this->assertEquals('Tidak Patuh', $permohonan->status_verifikasi);

        // 3. Pegawai Pelesen Menolak Permohonan (Gagal)
        $this->actingAs($superAdmin)->post("/epu/permohonan/{$permohonan->id}/keputusan-pelesen", [
            'keputusan' => 'Gagal',
            'catatan_pegawai' => 'Premis gagal memenuhi syarat kebersihan dan biosekuriti.',
        ]);
        $permohonan->refresh();
        $this->assertEquals('Ditolak', $permohonan->status);
        $this->assertEquals('Gagal', $permohonan->status_kelulusan_pelesen);

        // 4. Pemohon Menghantar Rayuan kepada Pengarah
        $lampiranRayuan = UploadedFile::fake()->create('surat_rayuan_dan_bukti_pembaikan.pdf', 200, 'application/pdf');
        $this->actingAs($penternak)->post("/epu/permohonan/{$permohonan->id}/rayuan", [
            'alasan_rayuan' => 'Pembersihan kolam dan pemasangan jaring biosekuriti telah diselesaikan sepenuhnya.',
            'dokumen_rayuan' => $lampiranRayuan,
        ]);
        $permohonan->refresh();
        $this->assertEquals('Rayuan Dihantar', $permohonan->status_rayuan);

        // 5. Pengarah Memproses Rayuan (Panjangkan ke PBN / Lulus Rayuan)
        $this->actingAs($superAdmin)->post("/epu/permohonan/{$permohonan->id}/proses-rayuan", [
            'tindakan_rayuan' => 'Lulus Rayuan',
            'catatan_keputusan_rayuan' => 'Rayuan dipertimbangkan dan diluluskan setelah semakan gambar pembaikan tapak.',
        ]);
        $permohonan->refresh();
        $this->assertEquals('Lulus Rayuan', $permohonan->status_rayuan);
        $this->assertEquals('Diluluskan', $permohonan->status);
    }

    public function test_pegawai_verifikasi_and_pegawai_pelesen_role_specific_actions()
    {
        Storage::fake('public');
        $penternak = User::where('role', 'penternak')->first();
        $pegawaiVerifikasi = User::where('role', 'pegawai_verifikasi_epu')->where('jajahan', 'Kota Bharu')->first();
        $pegawaiPelesen = User::where('role', 'pegawai_pelesen')->first();

        $this->assertNotNull($pegawaiVerifikasi);
        $this->assertNotNull($pegawaiPelesen);
        $this->assertTrue($pegawaiVerifikasi->isPegawaiVerifikasiEpu());
        $this->assertTrue($pegawaiPelesen->isPegawaiPelesen());

        // 1. Pemohon mohon
        $this->actingAs($penternak)->post('/epu/daftar-borang-a', [
            'nama_pemohon_atau_syarikat' => 'Reban Ayam Kampung KB',
            'nama_ladang' => 'Reban Unggas Organik KB',
            'jenis_unggas' => 'Ayam',
            'jurusan_aktiviti' => 'Pedaging',
            'kapasiti_maksimum_unggas' => 4000,
            'alamat_ladang' => 'Kg Pasir Hor',
            'jajahan' => 'Kota Bharu',
        ]);

        $ladang = EpuLadang::where('nama_ladang', 'Reban Unggas Organik KB')->first();
        $permohonan = $ladang->permohonanTerkini;

        // 2. Pegawai Verifikasi PPVJ verifikasi patuh & hantar penilaian
        $this->actingAs($pegawaiVerifikasi)->post("/epu/permohonan/{$permohonan->id}/verifikasi", [
            'status_verifikasi' => 'Patuh',
            'catatan_verifikasi' => 'Pemeriksaan PPVJ Kota Bharu mendapati premis bersih dan patuh.',
        ]);
        $permohonan->refresh();
        $this->assertEquals('Patuh', $permohonan->status_verifikasi);
        $this->assertEquals($pegawaiVerifikasi->id, $permohonan->pegawai_verifikasi_id);

        $this->actingAs($pegawaiVerifikasi)->post("/epu/permohonan/{$permohonan->id}/hantar-penilaian", [
            'catatan_penilaian_ladang' => 'Diperakukan untuk pertimbangan Pegawai Pelesen DVS.',
        ]);
        $permohonan->refresh();
        $this->assertEquals('Dihantar ke Pegawai Pelesen', $permohonan->status_penilaian_ladang);

        // 3. Pegawai Pelesen menyemak penilaian dan meluluskan lesen
        $this->actingAs($pegawaiPelesen)->post("/epu/permohonan/{$permohonan->id}/keputusan-pelesen", [
            'keputusan' => 'Lulus',
            'catatan_pegawai' => 'Disemak dan diluluskan oleh Pegawai Pelesen EPU JPVNK.',
        ]);
        $permohonan->refresh();
        $this->assertEquals('Diluluskan', $permohonan->status);
        $this->assertEquals($pegawaiPelesen->id, $permohonan->diluluskan_oleh);
    }

    public function test_strict_separation_of_powers_between_ppvj_and_pegawai_pelesen()
    {
        $penternak = User::where('role', 'penternak')->first();
        $pegawaiVerifikasi = User::where('role', 'pegawai_verifikasi_epu')->where('jajahan', 'Kota Bharu')->first();
        $pegawaiPelesen = User::where('role', 'pegawai_pelesen')->first();

        // Pemohon cipta permohonan
        $this->actingAs($penternak)->post('/epu/daftar-borang-a', [
            'nama_pemohon_atau_syarikat' => 'Ladang Ayam Pasir Mas',
            'nama_ladang' => 'Ladang Reban PM',
            'jenis_unggas' => 'Ayam',
            'jurusan_aktiviti' => 'Pedaging',
            'kapasiti_maksimum_unggas' => 2000,
            'alamat_ladang' => 'Lot 55',
            'jajahan' => 'Pasir Mas',
        ]);
        $ladang = EpuLadang::where('nama_ladang', 'Ladang Reban PM')->first();
        $permohonan = $ladang->permohonanTerkini;

        // 1. Pegawai Pelesen CUBA buat verifikasi PPVJ -> MESTI DITOLAK (403 Forbidden)
        $pelesenVerifResponse = $this->actingAs($pegawaiPelesen)->post("/epu/permohonan/{$permohonan->id}/verifikasi", [
            'status_verifikasi' => 'Patuh',
        ]);
        $pelesenVerifResponse->assertStatus(403);

        // 2. Pegawai Pelesen CUBA hantar penilaian PPVJ -> MESTI DITOLAK (403 Forbidden)
        $pelesenHantarResponse = $this->actingAs($pegawaiPelesen)->post("/epu/permohonan/{$permohonan->id}/hantar-penilaian", [
            'catatan_penilaian_ladang' => 'Test',
        ]);
        $pelesenHantarResponse->assertStatus(403);

        // 3. Pegawai Verifikasi CUBA buat keputusan pelesenan lesen -> MESTI DITOLAK (403 Forbidden)
        $verifKeputusanResponse = $this->actingAs($pegawaiVerifikasi)->post("/epu/permohonan/{$permohonan->id}/keputusan-pelesen", [
            'keputusan' => 'Lulus',
        ]);
        $verifKeputusanResponse->assertStatus(403);

        // 4. Pegawai Verifikasi CUBA proses rayuan Pengarah -> MESTI DITOLAK (403 Forbidden)
        $verifRayuanResponse = $this->actingAs($pegawaiVerifikasi)->post("/epu/permohonan/{$permohonan->id}/proses-rayuan", [
            'tindakan_rayuan' => 'Lulus Rayuan',
        ]);
        $verifRayuanResponse->assertStatus(403);
    }

    public function test_epu_notifications_broadcasted_to_all_relevant_officers()
    {
        $penternak = User::where('role', 'penternak')->first();
        $adminEpu = User::where('role', 'admin_epu')->first();
        $pegawaiVerifikasi = User::where('role', 'pegawai_verifikasi_epu')->where('jajahan', 'Kota Bharu')->first();
        $pegawaiPelesen = User::where('role', 'pegawai_pelesen')->first();

        // 1. Pemohon hantar Borang A
        $this->actingAs($penternak)->post('/epu/daftar-borang-a', [
            'nama_pemohon_atau_syarikat' => 'Ladang Notifikasi KB',
            'nama_ladang' => 'Ladang Ayam Notifikasi',
            'jenis_unggas' => 'Ayam',
            'jurusan_aktiviti' => 'Pedaging',
            'kapasiti_maksimum_unggas' => 3000,
            'alamat_ladang' => 'Lot 999',
            'jajahan' => 'Kota Bharu',
        ]);

        $ladang = EpuLadang::where('nama_ladang', 'Ladang Ayam Notifikasi')->first();
        $permohonan = $ladang->permohonanTerkini;

        // Semak notifikasi sampai kepada Admin EPU & PPVJ Kota Bharu
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $adminEpu->id,
            'type' => 'epu',
        ]);
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $pegawaiVerifikasi->id,
            'type' => 'epu',
        ]);

        // 2. PPVJ hantar penilaian ladang
        $this->actingAs($pegawaiVerifikasi)->post("/epu/permohonan/{$permohonan->id}/verifikasi", [
            'status_verifikasi' => 'Patuh',
        ]);
        $this->actingAs($pegawaiVerifikasi)->post("/epu/permohonan/{$permohonan->id}/hantar-penilaian", [
            'catatan_penilaian_ladang' => 'Penilaian lengkap.',
        ]);

        // Semak notifikasi sampai kepada Pegawai Pelesen
        $this->assertDatabaseHas('user_notifications', [
            'user_id' => $pegawaiPelesen->id,
            'type' => 'epu',
            'title' => 'Penilaian Ladang EPU Memerlukan Semakan Kelulusan',
        ]);
    }

    public function test_epu_print_restricted_only_to_pegawai_verifikasi_and_admin_epu()
    {
        $penternak = User::where('role', 'penternak')->first();
        $adminEpu = User::where('role', 'admin_epu')->first();
        $pegawaiVerifikasi = User::where('role', 'pegawai_verifikasi_epu')->first();
        $pegawaiPelesen = User::where('role', 'pegawai_pelesen')->first();
        $staf = User::where('role', 'staf')->first();

        $this->assertTrue($adminEpu->canCetakBorangEpu());
        $this->assertTrue($pegawaiVerifikasi->canCetakBorangEpu());
        $this->assertFalse($penternak->canCetakBorangEpu());
        $this->assertFalse($pegawaiPelesen->canCetakBorangEpu());
        $this->assertFalse($staf->canCetakBorangEpu());

        // Cipta permohonan EPU yang diluluskan
        $ladang = EpuLadang::create([
            'user_id' => $penternak->id,
            'nama_pemohon_atau_syarikat' => 'Syarikat Penternak Test',
            'nama_ladang' => 'Ladang Cetak Test',
            'jajahan' => 'Kota Bharu',
            'kategori_unggas' => 'Ayam Pedaging',
            'kapasiti_ternakan' => 5000,
            'alamat_ladang' => 'Lot 123',
            'status' => 'Aktif',
        ]);

        $permohonan = EpuPermohonan::create([
            'epu_ladang_id' => $ladang->id,
            'no_rujukan_permohonan' => 'EPU-TEST-PRINT-001',
            'jenis_permohonan' => 'Baharu',
            'jenis_unggas' => 'Ayam',
            'jurusan_aktiviti' => 'Pedaging',
            'kapasiti_ladang' => 5000,
            'bilangan_semasa_unggas' => 4000,
            'tarikh_mula_lesen' => now(),
            'status' => 'Diluluskan',
            'status_verifikasi' => 'Patuh',
            'status_penilaian_ladang' => 'Dihantar ke Pegawai Pelesen',
            'status_kelulusan_pelesen' => 'Lulus',
            'status_bayaran_fi' => 'Selesai Bayar',
            'no_resit_bayaran' => 'RES-9988',
            'no_lesen_epu' => 'DVS/EPU/2026/001',
            'tarikh_tamat_lesen' => now()->addYear(),
        ]);

        // 1. Admin EPU dan Pegawai Verifikasi BOLEH cetak
        $this->actingAs($adminEpu)->get("/epu/cetak-borang-a/{$permohonan->id}")->assertStatus(200);
        $this->actingAs($adminEpu)->get("/epu/lesen-borang-b/{$permohonan->id}/cetak")->assertStatus(200);
        $this->actingAs($pegawaiVerifikasi)->get("/epu/cetak-borang-a/{$permohonan->id}")->assertStatus(200);
        $this->actingAs($pegawaiVerifikasi)->get("/epu/lesen-borang-b/{$permohonan->id}/cetak")->assertStatus(200);

        // 2. Penternak, Pegawai Pelesen, Staf TIDAK BOLEH cetak (403 Forbidden)
        $this->actingAs($penternak)->get("/epu/cetak-borang-a/{$permohonan->id}")->assertStatus(403);
        $this->actingAs($penternak)->get("/epu/lesen-borang-b/{$permohonan->id}/cetak")->assertStatus(403);
        $this->actingAs($pegawaiPelesen)->get("/epu/cetak-borang-a/{$permohonan->id}")->assertStatus(403);
        $this->actingAs($pegawaiPelesen)->get("/epu/lesen-borang-b/{$permohonan->id}/cetak")->assertStatus(403);
        $this->actingAs($staf)->get("/epu/cetak-borang-a/{$permohonan->id}")->assertStatus(403);
        $this->actingAs($staf)->get("/epu/lesen-borang-b/{$permohonan->id}/cetak")->assertStatus(403);
    }

    public function test_admin_epu_module_isolation_cannot_access_other_modules()
    {
        $adminEpu = User::where('role', 'admin_epu')->first();
        $this->assertNotNull($adminEpu);

        // Helper checks
        $this->assertTrue($adminEpu->canAccessEpu());
        $this->assertFalse($adminEpu->canAccessEptr());
        $this->assertFalse($adminEpu->canAccessPawah());
        $this->assertFalse($adminEpu->canAccessKursus());
        $this->assertFalse($adminEpu->canAccessKlinik());
        $this->assertFalse($adminEpu->canAccessKenderaan());
        $this->assertFalse($adminEpu->canRequestInventori());
        $this->assertFalse($adminEpu->canRequestAlatanPejabat());

        // HTTP Routes isolation checks
        $this->actingAs($adminEpu)->get('/epu')->assertStatus(200);
        $this->actingAs($adminEpu)->get('/eptr')->assertStatus(403);
        $this->actingAs($adminEpu)->get('/pawah')->assertStatus(403);
        $this->actingAs($adminEpu)->get('/klinik')->assertStatus(403);
        $this->actingAs($adminEpu)->get('/kenderaan')->assertStatus(403);
        $this->actingAs($adminEpu)->get('/inventori/permohonan/saya')->assertStatus(403);
    }

    public function test_receipt_upload_restricted_to_applicant_and_admin_can_view()
    {
        Storage::fake('public');
        $penternak = User::where('role', 'penternak')->first();
        $adminEpu = User::where('role', 'admin_epu')->first();
        $otherUser = User::where('role', 'penternak')->where('id', '!=', $penternak->id)->first();

        // 1. Cipta ladang dan permohonan untuk pemohon $penternak
        $ladang = EpuLadang::create([
            'user_id' => $penternak->id,
            'nama_pemohon_atau_syarikat' => 'Penternak Ayam Segar',
            'nama_ladang' => 'Ladang Ayam Segar Kelantan',
            'id_premis' => 'PRM-KB-999',
            'jajahan' => 'Kota Bharu',
            'alamat_ladang' => 'Lot 100, Kota Bharu',
            'status_ladang' => 'Aktif',
        ]);

        $permohonan = EpuPermohonan::create([
            'epu_ladang_id' => $ladang->id,
            'no_rujukan_permohonan' => 'EPU/KB/2026/9991',
            'jenis_permohonan' => 'Baru',
            'jenis_unggas' => 'Ayam',
            'jurusan_aktiviti' => 'Pedaging',
            'bilangan_semasa_unggas' => 1000,
            'kapasiti_ladang' => 5000,
            'no_lesen_epu' => 'EPU-KB-2026-9991',
            'yuran_lesen' => 100.00,
            'status' => 'Diluluskan',
            'status_bayaran_fi' => 'Belum Bayar',
        ]);

        $resit = UploadedFile::fake()->create('resit_bayaran.pdf', 200, 'application/pdf');

        // 2. Admin EPU / Pegawai cuba muat naik resit -> Ditolak (403 Forbidden)
        $adminUploadResponse = $this->actingAs($adminEpu)->post("/epu/permohonan/{$permohonan->id}/bayar-fi", [
            'no_resit_bayaran' => 'RES-ADMIN-001',
            'resit_bayaran_fi' => $resit,
        ]);
        $adminUploadResponse->assertStatus(403);

        // 3. Pengguna lain yang bukan pemilik cuba muat naik -> Ditolak (403 Forbidden)
        if ($otherUser) {
            $otherUploadResponse = $this->actingAs($otherUser)->post("/epu/permohonan/{$permohonan->id}/bayar-fi", [
                'no_resit_bayaran' => 'RES-OTHER-001',
                'resit_bayaran_fi' => $resit,
            ]);
            $otherUploadResponse->assertStatus(403);
        }

        // 4. Pemohon (pemilik ladang) muat naik resit -> Berjaya
        $applicantUploadResponse = $this->actingAs($penternak)->post("/epu/permohonan/{$permohonan->id}/bayar-fi", [
            'no_resit_bayaran' => 'RES-PENTERNAK-8899',
            'resit_bayaran_fi' => $resit,
        ]);
        $applicantUploadResponse->assertRedirect();
        $permohonan->refresh();
        $this->assertEquals('Menunggu Pengesahan', $permohonan->status_bayaran_fi);
        $this->assertEquals('RES-PENTERNAK-8899', $permohonan->no_resit_bayaran);
        $this->assertNotNull($permohonan->resit_bayaran_fi);

        // 5. Admin EPU boleh melihat butiran permohonan dan resit yang dimuat naik
        $viewResponse = $this->actingAs($adminEpu)->get("/epu/ladang/{$ladang->id}");
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('RES-PENTERNAK-8899');
        $viewResponse->assertSee('Buka Gambar / Fail Resit');
        $viewResponse->assertDontSee('Bayar &amp; Muat Naik Resit', false);

        // 6. Admin EPU boleh mengesahkan bayaran
        $sahkanResponse = $this->actingAs($adminEpu)->post("/epu/permohonan/{$permohonan->id}/sahkan-bayaran");
        $sahkanResponse->assertRedirect();
        $permohonan->refresh();
        $this->assertEquals('Selesai Bayar', $permohonan->status_bayaran_fi);
    }

    public function test_applicant_cannot_upload_receipt_before_application_is_approved_by_pegawai_pelesen_or_pengarah(): void
    {
        $penternak = User::where('role', 'penternak')->first();

        $ladang = EpuLadang::create([
            'user_id' => $penternak->id,
            'nama_pemohon_atau_syarikat' => 'Ladang Ayam - Jais Test',
            'nama_ladang' => 'Ladang Ayam - Jais Test',
            'alamat_ladang' => 'Kampung Padang Temusu',
            'jajahan' => 'Kota Bharu',
            'kategori_unggas' => 'Ayam Pedaging',
            'kapasiti_ternakan' => 5000,
            'status_ladang' => 'Aktif',
            'status' => 'Aktif',
        ]);

        $permohonan = EpuPermohonan::create([
            'epu_ladang_id' => $ladang->id,
            'no_rujukan_permohonan' => 'EPU/2026/09/JAIS01',
            'jenis_permohonan' => 'Lesen Baharu',
            'jenis_unggas' => 'Ayam',
            'jurusan_aktiviti' => 'Pedaging',
            'bilangan_semasa_unggas' => 4000,
            'kapasiti_ladang' => 5000,
            'yuran_lesen' => 100.00,
            'status' => 'Dihantar', // Belum diluluskan
            'status_penilaian_ladang' => 'Dihantar ke Pegawai Pelesen',
            'status_bayaran_fi' => 'Belum Bayar',
        ]);

        // 1. Pemohon tengok halaman - borang upload disekat, mesej amaran dipaparkan
        $resp = $this->actingAs($penternak)->get("/epu/ladang/{$ladang->id}");
        $resp->assertStatus(200);
        $resp->assertSee('Belum Boleh Dimuat Naik');
        $resp->assertSee('Gambar / Fail resit bayaran fi belum boleh dimuat naik oleh pemohon selagi permohonan belum diluluskan oleh Pegawai Pelesen / Pengarah DVS');

        // 2. Pemohon cuba hantar fail resit bayaran sebelum diluluskan -> Disekat dengan ralat
        $resit = UploadedFile::fake()->create('resit_bayaran.pdf', 200, 'application/pdf');
        $uploadResp = $this->actingAs($penternak)->post("/epu/permohonan/{$permohonan->id}/bayar-fi", [
            'no_resit_bayaran' => 'RES-JAIS-001',
            'resit_bayaran_fi' => $resit,
        ]);
        $uploadResp->assertRedirect();
        $uploadResp->assertSessionHas('error');
        $this->assertNull($permohonan->fresh()->resit_bayaran_fi);
    }
}
