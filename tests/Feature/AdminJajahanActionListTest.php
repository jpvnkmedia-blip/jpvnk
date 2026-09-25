<?php

namespace Tests\Feature;

use App\Models\ActionList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminJajahanActionListTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $adminJajahanPasirPuteh;
    protected User $adminJajahanKotaBharu;
    protected User $penternak;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->superAdmin = User::where('role', 'super_admin')->first();

        $this->adminJajahanPasirPuteh = User::where('jajahan', 'Pasir Puteh')
            ->whereIn('role', ['admin_jajahan', 'admin_eptr_jajahan'])
            ->first() ?: User::factory()->create([
                'name' => 'Pegawai Veterinar Pasir Puteh',
                'email' => 'admin.pp.test@veterinar.kelantan.gov.my',
                'ic_number' => '850505039911',
                'role' => 'admin_jajahan',
                'jajahan' => 'Pasir Puteh',
            ]);

        $this->adminJajahanKotaBharu = User::where('jajahan', 'Kota Bharu')
            ->whereIn('role', ['admin_jajahan', 'admin_eptr_jajahan'])
            ->first() ?: User::factory()->create([
                'name' => 'Pegawai Veterinar Kota Bharu',
                'email' => 'admin.kb.test@veterinar.kelantan.gov.my',
                'ic_number' => '860606039922',
                'role' => 'admin_jajahan',
                'jajahan' => 'Kota Bharu',
            ]);

        $this->penternak = User::where('role', 'penternak')->first() ?: User::factory()->create([
            'name' => 'Mohd Azman bin Salleh',
            'email' => 'azman.penternak.test@gmail.com',
            'ic_number' => '900729039933',
            'phone' => '019-9887766',
            'address' => 'Kampung Gong Chapa, 16800 Pasir Puteh',
            'poskod' => '16800',
            'jajahan' => 'Pasir Puteh',
            'role' => 'penternak',
        ]);
    }

    public function test_admin_jajahan_can_access_action_list_index(): void
    {
        $response = $this->actingAs($this->adminJajahanPasirPuteh)->get(route('action-list.index'));
        $response->assertStatus(200);
        $response->assertSee('Action List');
        $response->assertSee('Dairi &amp; Log Aktiviti', false);
    }

    public function test_unauthorized_regular_user_cannot_access_action_list(): void
    {
        $response = $this->actingAs($this->penternak)->get(route('action-list.index'));
        $response->assertStatus(403);
    }

    public function test_admin_jajahan_can_create_and_store_activity_diary(): void
    {
        Storage::fake('public');

        $formData = [
            'tajuk_aktiviti' => 'Lawatan Pemantauan Projek Ruminan Gong Chapa',
            'tarikh' => '2026-09-25',
            'masa_mula' => '09:00 AM',
            'masa_selesai' => '12:30 PM',
            'kategori_aktiviti' => 'Lawatan / Pemeriksaan Lapangan',
            'jajahan' => 'Pasir Puteh',
            'lokasi' => 'Ladang Ternakan Lembu Jaya, Gong Chapa',
            'nama_pegawai' => 'Dr. Nik Farhan & En. Yusof',
            'status' => 'Selesai',
            'keutamaan' => 'Tinggi',
            'maklumat_aktiviti' => 'Pemeriksaan kesihatan ternakan dan pemberian vaksin pencegahan penyakit hawar berdarah (HS).',
            'tindakan_susulan' => 'Jadualkan pemantauan ulangan dalam tempoh 1 bulan.',
            'lampiran' => UploadedFile::fake()->create('laporan_lawatan.pdf', 500, 'application/pdf'),
        ];

        $response = $this->actingAs($this->adminJajahanPasirPuteh)->post(route('action-list.store'), $formData);
        
        $response->assertRedirect(route('action-list.index'));

        $actionList = ActionList::where('tajuk_aktiviti', 'Lawatan Pemantauan Projek Ruminan Gong Chapa')->first();
        $this->assertNotNull($actionList);
        $this->assertEquals('Pasir Puteh', $actionList->jajahan);
        $this->assertEquals('Selesai', $actionList->status);
        $this->assertEquals('Tinggi', $actionList->keutamaan);
        $this->assertNotNull($actionList->lampiran);
        Storage::disk('public')->assertExists($actionList->lampiran);
    }

    public function test_admin_can_view_activity_details(): void
    {
        $activity = ActionList::create([
            'no_bil' => 'AL/KB/2026/0001',
            'tajuk_aktiviti' => 'Mesyuarat Penyelarasan Veterinar Jajahan',
            'tarikh' => '2026-09-25',
            'kategori_aktiviti' => 'Mesyuarat / Perbincangan',
            'jajahan' => 'Kota Bharu',
            'maklumat_aktiviti' => 'Membincangkan pelan pemantauan ladang ternakan.',
            'status' => 'Selesai',
            'created_by' => $this->adminJajahanKotaBharu->id,
        ]);

        $response = $this->actingAs($this->adminJajahanKotaBharu)->get(route('action-list.show', $activity->id));
        $response->assertStatus(200);
        $response->assertSee('Mesyuarat Penyelarasan Veterinar Jajahan');
        $response->assertSee('AL/KB/2026/0001');
    }

    public function test_admin_can_update_activity(): void
    {
        $activity = ActionList::create([
            'no_bil' => 'AL/PP/2026/0002',
            'tajuk_aktiviti' => 'Operasi Vaksinasi Lapangan',
            'tarikh' => '2026-09-25',
            'kategori_aktiviti' => 'Rawatan & Survelan Penyakit',
            'jajahan' => 'Pasir Puteh',
            'maklumat_aktiviti' => 'Vaksinasi 20 ekor lembu.',
            'status' => 'Dalam Tindakan',
            'created_by' => $this->adminJajahanPasirPuteh->id,
        ]);

        $updateData = [
            'tajuk_aktiviti' => 'Operasi Vaksinasi Lapangan - SELESAI',
            'tarikh' => '2026-09-25',
            'kategori_aktiviti' => 'Rawatan & Survelan Penyakit',
            'jajahan' => 'Pasir Puteh',
            'maklumat_aktiviti' => 'Vaksinasi 20 ekor lembu telah selesai dijalankan dengan lancar.',
            'status' => 'Selesai',
            'keutamaan' => 'Biasa',
        ];

        $response = $this->actingAs($this->adminJajahanPasirPuteh)->put(route('action-list.update', $activity->id), $updateData);
        $response->assertRedirect(route('action-list.show', $activity->id));

        $activity->refresh();
        $this->assertEquals('Operasi Vaksinasi Lapangan - SELESAI', $activity->tajuk_aktiviti);
        $this->assertEquals('Selesai', $activity->status);
    }

    public function test_super_admin_can_delete_activity(): void
    {
        $activity = ActionList::create([
            'no_bil' => 'AL/PP/2026/0003',
            'tajuk_aktiviti' => 'Aktiviti untuk dipadam',
            'tarikh' => '2026-09-25',
            'kategori_aktiviti' => 'Lain-lain',
            'jajahan' => 'Pasir Puteh',
            'maklumat_aktiviti' => 'Ujian padam aktiviti.',
            'status' => 'Dibatalkan',
            'created_by' => $this->adminJajahanPasirPuteh->id,
        ]);

        $response = $this->actingAs($this->superAdmin)->delete(route('action-list.destroy', $activity->id));
        $response->assertRedirect(route('action-list.index'));

        $this->assertDatabaseMissing('action_lists', ['id' => $activity->id]);
    }

    public function test_admin_can_print_activity_diary_list(): void
    {
        ActionList::create([
            'no_bil' => 'AL/PP/2026/0004',
            'tajuk_aktiviti' => 'Pemeriksaan Premis Sembelihan',
            'tarikh' => '2026-09-25',
            'kategori_aktiviti' => 'Pemeriksaan Premis / Kebajikan Haiwan',
            'jajahan' => 'Pasir Puteh',
            'maklumat_aktiviti' => 'Pemeriksaan premis rumah sembelih.',
            'status' => 'Selesai',
            'created_by' => $this->adminJajahanPasirPuteh->id,
        ]);

        $response = $this->actingAs($this->adminJajahanPasirPuteh)->get(route('action-list.cetak', ['jajahan' => 'Pasir Puteh']));
        $response->assertStatus(200);
        $response->assertSee('LOG & DAIRI AKTIVITI ADMIN / PEGAWAI', false);
        $response->assertSee('Pemeriksaan Premis Sembelihan');
    }

    public function test_admin_can_print_single_activity_report(): void
    {
        $activity = ActionList::create([
            'no_bil' => 'AL/PP/2026/0005',
            'tajuk_aktiviti' => 'Audit Bio-sekuriti Reban Ayam',
            'tarikh' => '2026-09-25',
            'kategori_aktiviti' => 'Lawatan Lapangan',
            'jajahan' => 'Pasir Puteh',
            'maklumat_aktiviti' => 'Laporan audit biosekuriti reban tertutup.',
            'status' => 'Selesai',
            'created_by' => $this->adminJajahanPasirPuteh->id,
        ]);

        $response = $this->actingAs($this->adminJajahanPasirPuteh)->get(route('action-list.cetak', $activity->id));
        $response->assertStatus(200);
        $response->assertSee('Audit Bio-sekuriti Reban Ayam');
        $response->assertSee('AL/PP/2026/0005');
    }

    public function test_eptr_admin_approval_automatically_logs_to_action_list(): void
    {
        $pemunya = \App\Models\Pemunya::firstOrCreate(
            ['no_kp' => '900101035544'],
            [
                'user_id' => $this->penternak->id,
                'nama' => 'Ahmad Razak',
                'no_telefon' => '012-3456789',
                'alamat' => 'Kampung Padang Pak Amat',
                'jajahan' => 'Pasir Puteh',
                'status' => 'Aktif',
            ]
        );

        $ternakan = \App\Models\Ternakan::create([
            'pemunya_id' => $pemunya->id,
            'jenis_ternakan' => 'Lembu',
            'baka' => 'Kedah-Kelantan (KK)',
            'jantina' => 'Jantan',
            'tarikh_lahir' => '2024-01-01',
            'status_kelulusan' => 'Menunggu Kelulusan',
            'status' => 'Menunggu Kelulusan',
            'jajahan' => 'Pasir Puteh',
            'daerah' => 'Padang Pak Amat',
        ]);

        $response = $this->actingAs($this->adminJajahanPasirPuteh)
            ->post(route('eptr.lulus', $ternakan->id));

        $response->assertRedirect(route('eptr.show', $ternakan->id));

        // Semak rekod automatik dalam action_lists
        $this->assertDatabaseHas('action_lists', [
            'kategori_aktiviti' => 'Pendaftaran Ternakan (EPTR)',
            'jajahan' => 'Pasir Puteh',
            'status' => 'Selesai',
        ]);

        $loggedActivity = ActionList::where('kategori_aktiviti', 'Pendaftaran Ternakan (EPTR)')->first();
        $this->assertNotNull($loggedActivity);
        $this->assertStringContainsString('Kelulusan Pendaftaran Ternakan EPTR', $loggedActivity->tajuk_aktiviti);
    }

    public function test_all_admin_roles_can_access_action_list(): void
    {
        $adminRoles = [
            'admin_eptr',
            'admin_epu_negeri',
            'admin_epu_jajahan',
            'admin_kursus',
            'admin_ubat',
            'admin_klinik',
            'admin_kenderaan',
            'admin_pejabat',
        ];

        foreach ($adminRoles as $role) {
            $adminUser = User::factory()->create([
                'role' => $role,
                'jajahan' => 'Pasir Puteh',
            ]);

            $response = $this->actingAs($adminUser)->get(route('action-list.index'));
            $response->assertStatus(200);
        }
    }
}
