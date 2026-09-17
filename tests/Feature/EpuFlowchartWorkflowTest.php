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

        // 7. Pegawai Sahkan Bayaran Fi
        $this->actingAs($adminEpu)->post("/epu/permohonan/{$permohonan->id}/sahkan-bayaran");
        $permohonan->refresh();
        $this->assertEquals('Selesai Bayar', $permohonan->status_bayaran_fi);

        // 8. Pemohon Mencetak Lesen Borang B
        $printResponse = $this->actingAs($penternak)->get("/epu/lesen-borang-b/{$permohonan->id}/cetak");
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
}
