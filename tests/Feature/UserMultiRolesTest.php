<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pemunya;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UserMultiRolesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_super_admin_can_create_user_with_multiple_roles()
    {
        $superAdmin = User::where('role', 'super_admin')->first();
        $this->assertNotNull($superAdmin);

        $response = $this->actingAs($superAdmin)->post(route('users.store'), [
            'name' => 'Pegawai Multi Peranan Pasir Puteh',
            'email' => 'multi.pp@jpvnk.test',
            'ic_number' => '890101039988',
            'phone' => '0199998888',
            'address' => 'Pejabat JPVNK Pasir Puteh',
            'jajahan' => 'Pasir Puteh',
            'jawatan' => 'Pegawai Veterinar Jajahan',
            'bahagian_unit' => 'Pejabat JPV Pasir Puteh',
            'roles' => [
                'admin_jajahan',
                'admin_ubat',
                'admin_pejabat',
            ],
            'status' => 'Aktif',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('users.index'));

        $user = User::where('email', 'multi.pp@jpvnk.test')->first();
        $this->assertNotNull($user);
        $this->assertEquals(['admin_jajahan', 'admin_ubat', 'admin_pejabat'], $user->getRolesList());
        $this->assertTrue($user->isAdmin());
        $this->assertTrue($user->isAdminEptr());
        $this->assertTrue($user->isAdminUbat());
        $this->assertTrue($user->isAdminPejabat());
        $this->assertTrue($user->canAccessEptr());
        $this->assertTrue($user->canAccessStorUbat());
        $this->assertTrue($user->canAccessStorPejabat());
        $this->assertFalse($user->canAccessKenderaan());
        $this->assertTrue($user->hasRole('admin_jajahan'));
        $this->assertTrue($user->hasRole('admin_ubat'));
        $this->assertTrue($user->hasRole('admin_pejabat'));
        $this->assertFalse($user->hasRole('admin_program'));
    }

    public function test_super_admin_can_update_user_roles()
    {
        $superAdmin = User::where('role', 'super_admin')->first();

        // Create initial user
        $user = User::create([
            'name' => 'Pegawai Asal',
            'email' => 'pegawai.asal@jpvnk.test',
            'ic_number' => '880101035566',
            'phone' => '0198887766',
            'address' => 'Kota Bharu',
            'jajahan' => 'Kota Bharu',
            'jawatan' => 'Pegawai Latihan',
            'bahagian_unit' => 'Unit Pembangunan Modal Insan',
            'role' => 'admin_kursus',
            'roles' => ['admin_kursus'],
            'status' => 'Aktif',
            'password' => Hash::make('password123'),
        ]);

        $this->assertTrue($user->isAdminKursus());
        $this->assertFalse($user->isAdminEptr());

        // Update to hold both admin_kursus and admin_program
        $updateResponse = $this->actingAs($superAdmin)->put(route('users.update', $user->id), [
            'name' => 'Pegawai Asal Dikemaskini',
            'email' => 'pegawai.asal@jpvnk.test',
            'ic_number' => '880101035566',
            'phone' => '0198887766',
            'address' => 'Kota Bharu',
            'jajahan' => 'Kota Bharu',
            'jawatan' => 'Ketua Unit Latihan',
            'bahagian_unit' => 'Unit Pembangunan Modal Insan',
            'roles' => ['admin_kursus', 'admin_program', 'admin_eptr'],
            'status' => 'Aktif',
        ]);

        $updateResponse->assertRedirect(route('users.index'));

        $user->refresh();
        $this->assertEquals(['admin_kursus', 'admin_program', 'admin_eptr'], $user->getRolesList());
        $this->assertTrue($user->isAdminKursus());
        $this->assertTrue($user->isAdminProgram());
        $this->assertTrue($user->isAdminEptr());
        $this->assertTrue($user->canAccessPawah());
        $this->assertTrue($user->canAccessKursus());
    }

    public function test_backward_compatibility_single_role_string_store()
    {
        $superAdmin = User::where('role', 'super_admin')->first();

        $response = $this->actingAs($superAdmin)->post(route('users.store'), [
            'name' => 'Pegawai Single Role String',
            'email' => 'single.role@jpvnk.test',
            'ic_number' => '870101031234',
            'phone' => '0123456789',
            'address' => 'Tumpat',
            'jajahan' => 'Tumpat',
            'jawatan' => 'Pegawai Jajahan',
            'bahagian_unit' => 'Pejabat JPV Tumpat',
            'role' => 'admin_jajahan',
            'status' => 'Aktif',
        ]);

        $response->assertRedirect(route('users.index'));
        $user = User::where('email', 'single.role@jpvnk.test')->first();
        $this->assertNotNull($user);
        $this->assertEquals('admin_jajahan', $user->role);
        $this->assertEquals(['admin_jajahan'], $user->getRolesList());
        $this->assertTrue($user->isAdminJajahan());
    }

    public function test_super_admin_cannot_strip_super_admin_from_own_account()
    {
        $superAdmin = User::where('role', 'super_admin')->first();

        $response = $this->actingAs($superAdmin)->put(route('users.update', $superAdmin->id), [
            'name' => $superAdmin->name,
            'email' => $superAdmin->email,
            'ic_number' => $superAdmin->ic_number,
            'phone' => $superAdmin->phone,
            'address' => $superAdmin->address,
            'jajahan' => $superAdmin->jajahan,
            'jawatan' => 'Super Administrator',
            'bahagian_unit' => 'Unit Teknologi Maklumat',
            'roles' => ['staf'], // Trying to remove super_admin from self
            'status' => 'Aktif',
        ]);

        $response->assertSessionHas('error');
        $superAdmin->refresh();
        $this->assertTrue($superAdmin->isSuperAdmin());
    }

    public function test_user_index_filtering_with_multi_roles()
    {
        $superAdmin = User::where('role', 'super_admin')->first();

        $multiUser = User::create([
            'name' => 'Dr. Multi Pegawai Ubat & Pejabat',
            'email' => 'multi.ubatpejabat@jpvnk.test',
            'ic_number' => '910101031122',
            'phone' => '0191112233',
            'address' => 'Kota Bharu',
            'jajahan' => 'Kota Bharu',
            'jawatan' => 'Pegawai Farmasi',
            'bahagian_unit' => 'Stor Farmasi & Pejabat',
            'role' => 'admin_ubat',
            'roles' => ['admin_ubat', 'admin_pejabat'],
            'status' => 'Aktif',
            'password' => Hash::make('password123'),
        ]);

        // Filter by admin_pejabat should find this user even though primary role is admin_ubat
        $response = $this->actingAs($superAdmin)->get(route('users.index', ['role' => 'admin_pejabat']));
        $response->assertStatus(200);
        $response->assertSee('Dr. Multi Pegawai Ubat & Pejabat');
    }

    public function test_update_user_without_providing_signature_file()
    {
        $superAdmin = User::where('role', 'super_admin')->first();
        $targetUser = User::where('role', '!=', 'super_admin')->first();

        $response = $this->actingAs($superAdmin)->put(route('users.update', $targetUser->id), [
            'name' => $targetUser->name . ' Updated',
            'email' => $targetUser->email,
            'ic_number' => $targetUser->ic_number,
            'phone' => $targetUser->phone ?? '0191234567',
            'address' => $targetUser->address ?? 'Alamat Test',
            'jajahan' => $targetUser->jajahan ?? 'Kota Bharu',
            'jawatan' => $targetUser->jawatan ?? 'Pegawai Veterinar',
            'bahagian_unit' => $targetUser->bahagian_unit ?? 'Unit Veterinar',
            'roles' => $targetUser->getRolesList(),
            'status' => 'Aktif',
        ]);

        $response->assertRedirect(route('users.index'));
        $targetUser->refresh();
        $this->assertStringContainsString('Updated', $targetUser->name);
    }

    public function test_super_admin_sidebar_contains_all_module_menus()
    {
        $superAdmin = User::where('role', 'super_admin')->first();

        $response = $this->actingAs($superAdmin)->get(route('dashboard'));
        $response->assertStatus(200);

        // Semak semua modul perkhidmatan veterinar wujud dalam sidebar Super Admin
        $response->assertSee('Perkhidmatan Veterinar');
        $response->assertSee('EPTR Ruminan');
        $response->assertSee('Program Pawah');
        $response->assertSee('Program NAIMbif');
        $response->assertSee('EPU Unggas');
        $response->assertSee('Kursus Ternakan');
        $response->assertSee('Klinik Haiwan');
        $response->assertSee('Stor Ubat &amp; Farmasi', false);
        $response->assertSee('Stor Pejabat');
        $response->assertSee('Kenderaan Rasmi');
        $response->assertSee('Pengurusan Pengguna');
    }

    public function test_super_admin_can_create_admin_media_user_and_they_can_approve_media()
    {
        $superAdmin = User::where('role', 'super_admin')->first();

        // 1. Create User with admin_media role
        $response = $this->actingAs($superAdmin)->post(route('users.store'), [
            'name' => 'Pegawai Unit Media Kelantan',
            'email' => 'admin.media@jpvnk.test',
            'ic_number' => '900101037788',
            'phone' => '0198889900',
            'address' => 'Ibu Pejabat JPVNK Kota Bharu',
            'jajahan' => 'Kota Bharu',
            'jawatan' => 'Pegawai Penerangan & Media',
            'bahagian_unit' => 'Unit Media & Komunikasi',
            'roles' => ['admin_media'],
            'status' => 'Aktif',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('users.index'));

        $mediaAdmin = User::where('email', 'admin.media@jpvnk.test')->first();
        $this->assertNotNull($mediaAdmin);
        $this->assertTrue($mediaAdmin->isAdminMedia());
        $this->assertTrue($mediaAdmin->canManageMedia());
        $this->assertTrue($mediaAdmin->canAccessMedia());
        $this->assertEquals('Admin Media Jabatan', $mediaAdmin->role_label);

        // 2. Verify admin_media can approve media booking
        $staff = User::where('role', 'staf')->first() ?? $superAdmin;
        $tempahan = \App\Models\MediaTempahan::create([
            'user_id' => $staff->id,
            'no_rujukan' => 'MEDIA/2026/09/0888',
            'nama_pemohon' => $staff->name,
            'jawatan_pemohon' => 'Pembantu Veterinar',
            'bahagian_unit' => 'Unit Latihan',
            'no_telefon' => '0123456789',
            'emel' => $staff->email,
            'nama_program' => 'Karnival Inovasi Veterinar',
            'tarikh_program' => now()->addDays(4)->toDateString(),
            'masa_mula' => '09:00',
            'masa_tamat' => '17:00',
            'lokasi' => 'Dewan Utama',
            'penganjur' => 'JPVNK',
            'pegawai_bertanggungjawab' => 'PIC Program',
            'jenis_permohonan' => ['Liputan Fotografi', 'Reka Bentuk Poster'],
            'status' => 'Menunggu Kelulusan',
            'perakuan' => true,
        ]);

        $actionResponse = $this->actingAs($mediaAdmin)->post(route('media.tindakan', $tempahan->id), [
            'keputusan' => 'Diluluskan',
            'pegawai_media_bertugas' => 'En. Media & Pn. Krew',
            'catatan_unit_media' => 'Permohonan diluluskan dan krew telah dijadualkan.',
        ]);

        $actionResponse->assertRedirect(route('media.show', $tempahan->id));
        $tempahan->refresh();
        $this->assertEquals('Diluluskan', $tempahan->status);
        $this->assertEquals($mediaAdmin->id, $tempahan->diluluskan_oleh);
    }

    public function test_super_admin_can_multi_delete_users()
    {
        $superAdmin = User::where('role', 'super_admin')->first();

        // Create 3 dummy users
        $user1 = User::create([
            'name' => 'User Multi Del 1',
            'email' => 'del1@jpvnk.test',
            'ic_number' => '990101031111',
            'phone' => '0191111111',
            'address' => 'Kota Bharu',
            'jajahan' => 'Kota Bharu',
            'role' => 'orang_awam',
            'status' => 'Aktif',
            'password' => Hash::make('password123'),
        ]);

        $user2 = User::create([
            'name' => 'User Multi Del 2',
            'email' => 'del2@jpvnk.test',
            'ic_number' => '990101032222',
            'phone' => '0192222222',
            'address' => 'Pasir Mas',
            'jajahan' => 'Pasir Mas',
            'role' => 'penternak',
            'status' => 'Aktif',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($superAdmin)->delete(route('users.multi-destroy'), [
            'ids' => [$user1->id, $user2->id],
        ]);

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $user1->id]);
        $this->assertDatabaseMissing('users', ['id' => $user2->id]);
    }

    public function test_multi_delete_safely_skips_own_super_admin_account()
    {
        $superAdmin = User::where('role', 'super_admin')->first();

        $user1 = User::create([
            'name' => 'User Multi Del Single',
            'email' => 'single.del@jpvnk.test',
            'ic_number' => '990101033333',
            'phone' => '0193333333',
            'address' => 'Bachok',
            'jajahan' => 'Bachok',
            'role' => 'staf',
            'status' => 'Aktif',
            'password' => Hash::make('password123'),
        ]);

        // Attempt to delete both user1 and superAdmin himself
        $response = $this->actingAs($superAdmin)->delete(route('users.multi-destroy'), [
            'ids' => [$superAdmin->id, $user1->id],
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseMissing('users', ['id' => $user1->id]);
        $this->assertDatabaseHas('users', ['id' => $superAdmin->id]);
    }

    public function test_non_super_admin_cannot_multi_delete_users()
    {
        $staff = User::where('role', 'staf')->first() ?? User::where('role', 'penternak')->first();

        $user1 = User::create([
            'name' => 'User Test Protect',
            'email' => 'protect@jpvnk.test',
            'ic_number' => '990101034444',
            'phone' => '0194444444',
            'address' => 'Machang',
            'jajahan' => 'Machang',
            'role' => 'orang_awam',
            'status' => 'Aktif',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($staff)->delete(route('users.multi-destroy'), [
            'ids' => [$user1->id],
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('users', ['id' => $user1->id]);
    }

    public function test_super_admin_can_delete_across_all_ten_modules()
    {
        $superAdmin = User::where('role', 'super_admin')->first();
        $penternak = User::where('role', 'penternak')->first();
        $staff = User::where('role', 'staf')->first() ?? $superAdmin;

        // 1. Senarai Ternakan Ruminan Berdaftar (EPTR)
        $pemunya = Pemunya::first() ?? Pemunya::create([
            'user_id' => $penternak->id,
            'nama' => 'Pemunya Test',
            'no_kp' => '800101035511',
            'no_telefon' => '0191234567',
            'alamat' => 'Kota Bharu',
            'jajahan' => 'Kota Bharu',
        ]);
        $ternakan = \App\Models\Ternakan::create([
            'pemunya_id' => $pemunya->id,
            'no_tag' => 'TAG-DEL-001',
            'jenis_ternakan' => 'Lembu',
            'baka' => 'Brahman',
            'jantina' => 'Jantan',
            'status' => 'Aktif',
            'status_kelulusan' => 'Diluluskan',
            'jajahan' => 'Kota Bharu',
        ]);
        $delResp1 = $this->actingAs($superAdmin)->delete(route('eptr.ternakan.destroy', $ternakan->id));
        $delResp1->assertRedirect(route('eptr.index'));
        $this->assertDatabaseMissing('ternakan', ['id' => $ternakan->id]);

        // 2. Program Pawah - Senarai Surat Perjanjian
        $pawah = \App\Models\PawahPerjanjian::create([
            'user_id' => $penternak->id,
            'no_perjanjian' => 'PAWAH/DEL/001',
            'nama_program' => 'Program Pawah Lembu',
            'jajahan' => 'Kota Bharu',
            'bilangan_induk' => 5,
            'tarikh_mula' => now()->toDateString(),
            'tarikh_tamat' => now()->addYears(2)->toDateString(),
            'tempoh_tahun' => 2,
            'status' => 'Aktif',
        ]);
        $delResp2 = $this->actingAs($superAdmin)->delete(route('pawah.destroy', $pawah->id));
        $delResp2->assertRedirect(route('pawah.index'));
        $this->assertDatabaseMissing('pawah_perjanjian', ['id' => $pawah->id]);

        // 3. Program NAIMbif - Senarai Permohonan Ladang
        $naimbif = \App\Models\NaimbifPermohonan::create([
            'user_id' => $penternak->id,
            'no_rujukan' => 'NAIMBIF/DEL/001',
            'nama' => 'Pemohon NAIMbif',
            'no_kp' => '850101035522',
            'no_telefon' => '0199998877',
            'alamat_tetap' => 'Kg Kota Bharu',
            'poskod' => '15000',
            'jajahan' => 'Kota Bharu',
            'keluasan_tanah' => 5.0,
            'status_tanah' => 'Sendiri',
            'status_permohonan' => 'Dihantar',
        ]);
        $delResp3 = $this->actingAs($superAdmin)->delete(route('naimbif.admin.destroy', $naimbif->id));
        $delResp3->assertRedirect(route('naimbif.admin.index'));
        $this->assertSoftDeleted('naimbif_permohonan', ['id' => $naimbif->id]);

        // 4. Senarai Ladang Unggas (EPU)
        $ladang = \App\Models\EpuLadang::create([
            'user_id' => $penternak->id,
            'nama_pemohon_atau_syarikat' => 'Syarikat Unggas Del',
            'nama_ladang' => 'Ladang Unggas Del',
            'alamat_ladang' => 'Kota Bharu',
            'jajahan' => 'Kota Bharu',
            'status_ladang' => 'Aktif',
        ]);
        $delResp4 = $this->actingAs($superAdmin)->delete(route('epu.destroy', $ladang->id));
        $delResp4->assertRedirect(route('epu.index'));
        $this->assertDatabaseMissing('epu_ladang', ['id' => $ladang->id]);

        // 5. Kursus Ternakan - Pengurusan Pemohon
        $course = \App\Models\Course::first() ?? \App\Models\Course::create([
            'title' => 'Kursus Del Test',
            'code' => 'CRS-DEL-01',
            'category' => 'Ruminan',
            'description' => 'Test kursus',
            'trainer_name' => 'Jurulatih',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'time' => '09:00 AM',
            'location' => 'Pusat Latihan',
            'jajahan' => 'Kota Bharu',
            'capacity' => 30,
            'registered_count' => 1,
            'fee' => 0,
            'status' => 'Buka',
            'created_by' => $superAdmin->id,
        ]);
        $courseApp = \App\Models\CourseApplication::create([
            'course_id' => $course->id,
            'user_id' => $penternak->id,
            'registration_number' => 'REG-DEL-001',
            'status' => 'Menunggu',
        ]);
        $delResp5 = $this->actingAs($superAdmin)->delete(route('kursus.pemohon.destroy', $courseApp->id));
        $delResp5->assertRedirect();
        $this->assertDatabaseMissing('course_applications', ['id' => $courseApp->id]);

        // 6. Senarai Temujanji (Klinik Haiwan)
        $temujanji = \App\Models\KlinikTemujanji::create([
            'user_id' => $penternak->id,
            'no_temujanji' => 'TJ-DEL-001',
            'jenis_haiwan' => 'Kucing',
            'simptom_atau_tujuan' => 'Vaksinasi',
            'tarikh_temujanji' => now()->addDays(2)->toDateString(),
            'sesi' => 'Pagi (8:30 AM - 12:30 PM)',
            'klinik_jajahan' => 'Klinik Haiwan Ibu Pejabat JPVNK Kota Bharu',
            'status' => 'Disahkan',
        ]);
        $delResp6 = $this->actingAs($superAdmin)->delete(route('klinik.destroy', $temujanji->id));
        $delResp6->assertRedirect(route('klinik.index'));
        $this->assertDatabaseMissing('klinik_temujanji', ['id' => $temujanji->id]);

        // 7. Katalog Stor Pejabat
        $itemPejabat = \App\Models\InventoriItem::create([
            'kod_item' => 'ITM-PJB-DEL-01',
            'nama_item' => 'Item Pejabat Del',
            'jenis_stor' => 'pejabat',
            'kategori' => 'Alat Tulis & Pejabat',
            'unit' => 'Unit',
            'kuantiti_semasa' => 10,
            'kuantiti_minimum' => 2,
            'status' => 'Mencukupi',
        ]);
        $delResp7 = $this->actingAs($superAdmin)->delete(route('inventori.destroy', $itemPejabat->id));
        $delResp7->assertRedirect(route('inventori.pejabat.index'));
        $this->assertDatabaseMissing('inventori_items', ['id' => $itemPejabat->id]);

        // 8. Katalog Ubat & Vaksin
        $itemUbat = \App\Models\InventoriItem::create([
            'kod_item' => 'ITM-UBT-DEL-01',
            'nama_item' => 'Item Ubat Del',
            'jenis_stor' => 'ubat',
            'kategori' => 'Vaksin',
            'unit' => 'Botol',
            'kuantiti_semasa' => 20,
            'kuantiti_minimum' => 5,
            'status' => 'Mencukupi',
        ]);
        $delResp8 = $this->actingAs($superAdmin)->delete(route('inventori.destroy', $itemUbat->id));
        $delResp8->assertRedirect(route('inventori.ubat.index'));
        $this->assertDatabaseMissing('inventori_items', ['id' => $itemUbat->id]);

        // 9. Senarai Permohonan & Rekod Tempahan Kenderaan
        $tempahanKenderaan = \App\Models\KenderaanTempahan::create([
            'user_id' => $staff->id,
            'no_tempahan' => 'KND-DEL-001',
            'destinasi' => 'Machang',
            'tujuan_perjalanan' => 'Lawatan Ladang',
            'tarikh_mula' => now()->toDateString(),
            'masa_mula' => '08:30',
            'tarikh_tamat' => now()->toDateString(),
            'masa_tamat' => '17:00',
            'bilangan_penumpang' => 2,
            'status' => 'Menunggu',
        ]);
        $delResp9 = $this->actingAs($superAdmin)->delete(route('kenderaan.tempahan.destroy', $tempahanKenderaan->id));
        $delResp9->assertRedirect(route('kenderaan.index'));
        $this->assertDatabaseMissing('kenderaan_tempahan', ['id' => $tempahanKenderaan->id]);

        // 10. Senarai Permohonan Tempahan Media
        $tempahanMedia = \App\Models\MediaTempahan::create([
            'user_id' => $staff->id,
            'no_rujukan' => 'MEDIA/DEL/001',
            'nama_pemohon' => $staff->name,
            'jawatan' => 'Pegawai Tadbir',
            'bahagian_unit_jajahan' => 'Unit Pentadbiran',
            'no_telefon' => '0123456789',
            'emel' => $staff->email,
            'nama_program' => 'Program Media Del',
            'tarikh_program' => now()->addDays(3)->toDateString(),
            'masa_mula' => '09:00',
            'masa_tamat' => '17:00',
            'lokasi' => 'Dewan JPVNK',
            'penganjur' => 'JPVNK',
            'pegawai_bertanggungjawab' => 'PIC',
            'jenis_permohonan' => ['Liputan Fotografi'],
            'status' => 'Menunggu Kelulusan',
            'pengesahan_pemohon' => true,
        ]);
        $delResp10 = $this->actingAs($superAdmin)->delete(route('media.destroy', $tempahanMedia->id));
        $delResp10->assertRedirect(route('media.index'));
        $this->assertDatabaseMissing('media_tempahan', ['id' => $tempahanMedia->id]);
    }
}
