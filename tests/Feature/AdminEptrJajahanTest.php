<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\PemindahanTernakan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminEptrJajahanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_all_10_jajahan_admin_eptr_accounts_are_seeded()
    {
        $jajahanList = [
            'Kota Bharu',
            'Pasir Mas',
            'Bachok',
            'Tumpat',
            'Pasir Puteh',
            'Machang',
            'Tanah Merah',
            'Jeli',
            'Kuala Krai',
            'Gua Musang',
        ];

        foreach ($jajahanList as $jajahan) {
            $adminJajahan = User::where('jajahan', $jajahan)
                ->whereIn('role', ['admin_jajahan', 'admin_eptr_jajahan'])
                ->first();

            $this->assertNotNull($adminJajahan, "Admin EPTR Jajahan bagi {$jajahan} mesti wujud dalam pangkalan data.");
            $this->assertTrue($adminJajahan->isAdminEptr());
            $this->assertTrue($adminJajahan->isAdminJajahan());
            $this->assertTrue($adminJajahan->isAdminEptrJajahan());
            $this->assertTrue($adminJajahan->isStaff());
            $this->assertTrue($adminJajahan->canAccessEptr());
            $this->assertStringContainsString($jajahan, $adminJajahan->role_label);
        }
    }

    public function test_admin_eptr_jajahan_can_access_eptr_and_dashboard()
    {
        $adminPasirMas = User::where('jajahan', 'Pasir Mas')
            ->whereIn('role', ['admin_jajahan', 'admin_eptr_jajahan'])
            ->first();

        $response = $this->actingAs($adminPasirMas)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Admin EPTR Jajahan Pasir Mas');
        $response->assertSee('Permit Pemindahan Ternakan');

        $responseEptr = $this->actingAs($adminPasirMas)->get(route('eptr.index'));
        $responseEptr->assertStatus(200);
    }

    public function test_switch_role_to_specific_admin_jajahan()
    {
        $adminBachok = User::where('jajahan', 'Bachok')
            ->whereIn('role', ['admin_jajahan', 'admin_eptr_jajahan'])
            ->first();

        $response = $this->post(route('auth.switch-role'), [
            'user_id' => $adminBachok->id,
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($adminBachok);
    }

    public function test_admin_eptr_jajahan_can_approve_pemindahan_with_their_jajahan_letterhead()
    {
        $adminTumpat = User::where('jajahan', 'Tumpat')
            ->whereIn('role', ['admin_jajahan', 'admin_eptr_jajahan'])
            ->first();

        $pemindahan = PemindahanTernakan::create([
            'no_rujukan' => 'PT-TPT-2026-0001',
            'jajahan_asal' => 'Tumpat',
            'tarikh_permohonan' => now()->toDateString(),
            'tarikh_jangka_pindah' => now()->addDays(2)->toDateString(),
            'tujuan_pemindahan' => 'Ternak',
            'jenis_ternakan' => 'Lembu',
            'bilangan_jantan' => 2,
            'bilangan_betina' => 0,
            'pemohon_nama' => 'Penternak Tumpat',
            'pemohon_ic' => '850101035544',
            'pemohon_tel' => '0199998888',
            'pemohon_alamat' => 'Kampung Wakaf Bharu, Tumpat',
            'penerima_nama' => 'Penternak Kota Bharu',
            'penerima_ic' => '860101035544',
            'penerima_tel' => '0199997777',
            'penerima_alamat' => 'Kampung Peringat, Kota Bharu',
            'penerima_jajahan' => 'Kota Bharu',
            'penerima_negeri' => 'Kelantan',
            'status' => 'Menunggu Kelulusan',
        ]);

        $response = $this->actingAs($adminTumpat)->post(route('eptr.pemindahan.lulus', $pemindahan->id), [
            'status' => 'Diluluskan',
            'nama_pegawai_kelulusan' => $adminTumpat->name,
            'jawatan_pegawai' => 'Pegawai Perkhidmatan Veterinar Jajahan Tumpat',
            'jajahan_pegawai' => 'Tumpat',
            'tarikh_kelulusan' => now()->toDateString(),
        ]);

        $response->assertRedirect(route('eptr.pemindahan.show', $pemindahan->id));
        $pemindahan->refresh();
        $this->assertEquals('Diluluskan', $pemindahan->status);
        $this->assertEquals('Tumpat', $pemindahan->pegawai_jajahan);

        // Check print set lengkap
        $printResponse = $this->actingAs($adminTumpat)->get(route('eptr.pemindahan.cetak-set-lengkap', $pemindahan->id));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('JAJAHAN TUMPAT');
        $printResponse->assertSee('09-7257242');
    }

    public function test_jadual_fi_page_renders_with_enactment_rates()
    {
        $user = User::where('role', 'penternak')->first();

        $response = $this->actingAs($user)->get(route('eptr.jadual-fi'));
        $response->assertStatus(200);
        $response->assertSee('Jadual Fi Bayaran Pendaftaran Ternakan Ruminan');
        $response->assertSee('Seksyen 5');
        $response->assertSee('Seksyen 6');
        $response->assertSee('Seksyen 7');
        $response->assertSee('Seksyen 8');
        $response->assertSee('Seksyen 10');
        $response->assertSee('Perenggan 11(1)(b)');
        $response->assertSee('Perenggan 11(1)(c)');
        $response->assertSee('Subseksyen 44(3)');
        $response->assertSee('Kira Fi Bayaran Statutori');
    }

    public function test_borang_a_requires_tarikh_lahir_and_auto_calculates_age()
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $penternak = User::where('role', 'penternak')->first();
        $receipt = \Illuminate\Http\UploadedFile::fake()->create('resit.pdf', 100, 'application/pdf');

        // 1. Gagal jika tarikh lahir tiada
        $responseMissing = $this->actingAs($penternak)->post(route('eptr.store'), [
            'nama_pemunya' => 'Siti Penternak',
            'no_kp_pemunya' => '950505035522',
            'no_tel_pemunya' => '0198887766',
            'alamat_pemunya' => 'Kampung Pauh, Bachok',
            'jenis_ternakan' => 'lembu',
            'baka' => 'kedah-kelantan',
            'jantina' => 'Betina',
            'tujuan_ternakan' => 'Pembiakan',
            'jajahan' => 'Bachok',
            'daerah' => 'Bekelam',
            'resit_pembayaran' => $receipt,
        ]);
        $responseMissing->assertSessionHasErrors(['tarikh_lahir']);

        // 2. Berjaya jika tarikh lahir disertakan dan auto kira umur
        $birthDate = now()->subMonths(18)->toDateString();
        $receipt2 = \Illuminate\Http\UploadedFile::fake()->create('resit2.pdf', 100, 'application/pdf');
        $responseSuccess = $this->actingAs($penternak)->post(route('eptr.store'), [
            'nama_pemunya' => 'Siti Penternak',
            'no_kp_pemunya' => '950505035522',
            'no_tel_pemunya' => '0198887766',
            'alamat_pemunya' => 'Kampung Pauh, Bachok',
            'jenis_ternakan' => 'lembu',
            'baka' => 'kedah-kelantan',
            'jantina' => 'Betina',
            'tarikh_lahir' => $birthDate,
            'tujuan_ternakan' => 'Pembiakan',
            'jajahan' => 'Bachok',
            'daerah' => 'Bekelam',
            'resit_pembayaran' => $receipt2,
        ]);
        $responseSuccess->assertSessionHasNoErrors();
        $responseSuccess->assertRedirect();
        
        $ternakan = \App\Models\Ternakan::latest('id')->first();
        $this->assertNotNull($ternakan);
        $this->assertEquals($birthDate, $ternakan->tarikh_lahir->toDateString());
        $this->assertStringContainsString('Tahun', $ternakan->umur);
    }

    public function test_borang_b_pindah_milik_only_created_by_penternak_and_approved_by_admin_jajahan()
    {
        $penternakUser = User::create([
            'name' => 'Penternak Pasir Mas Baru',
            'email' => 'penternak.pm.baru@example.com',
            'password' => bcrypt('password'),
            'ic_number' => '800101035544',
            'phone' => '0191112222',
            'role' => 'penternak',
            'status' => 'Aktif',
        ]);
        $adminPasirMas = User::where('jajahan', 'Pasir Mas')->whereIn('role', ['admin_jajahan', 'admin_eptr_jajahan'])->first();
        $adminBachok = User::where('jajahan', 'Bachok')->whereIn('role', ['admin_jajahan', 'admin_eptr_jajahan'])->first();

        $pemunya1 = \App\Models\Pemunya::create([
            'nama' => 'Penternak Asal Pasir Mas',
            'no_kp' => '800101035544',
            'no_telefon' => '0191112222',
            'alamat' => 'Kampung Lemal, Pasir Mas',
            'jajahan' => 'Pasir Mas',
            'user_id' => $penternakUser->id,
            'status' => 'Aktif',
        ]);

        $pemunya2 = \App\Models\Pemunya::create([
            'nama' => 'Penternak Pembeli Pasir Mas',
            'no_kp' => '820202035544',
            'no_telefon' => '0192223333',
            'alamat' => 'Kampung Meranti, Pasir Mas',
            'jajahan' => 'Pasir Mas',
            'status' => 'Aktif',
        ]);

        $ternakan = \App\Models\Ternakan::create([
            'pemunya_id' => $pemunya1->id,
            'no_tag' => 'PM-LEMBU-001',
            'jenis_ternakan' => 'Lembu',
            'baka' => 'Brahman',
            'jantina' => 'Jantan',
            'status' => 'Aktif',
            'status_kelulusan' => 'Diluluskan',
            'jajahan' => 'Pasir Mas',
            'tarikh_daftar' => now()->toDateString(),
        ]);

        // Admin Pasir Mas tidak boleh isi borang B (disekat)
        $respAdminCreate = $this->actingAs($adminPasirMas)->get(route('eptr.borang-b.create'));
        $respAdminCreate->assertRedirect(route('eptr.borang-b.index'));
        $respAdminCreate->assertSessionHas('error');

        // Penternak isi Borang B memohon pindah milik
        $resitFile = \Illuminate\Http\UploadedFile::fake()->create('resit_pindah.pdf', 100, 'application/pdf');
        $respMohon = $this->actingAs($penternakUser)->post(route('eptr.borang-b.store'), [
            'ternakan_id' => $ternakan->id,
            'jenis_pemunya_baru' => 'sedia_ada',
            'pemunya_baru_id' => $pemunya2->id,
            'tarikh_pindah' => now()->toDateString(),
            'sebab_pindah' => 'Jual Beli',
            'harga_jualan' => 3500.00,
            'perakuan' => 1,
            'resit_pembayaran' => $resitFile,
        ]);
        $respMohon->assertRedirect();

        $pindahMilik = \App\Models\PindahMilik::where('ternakan_id', $ternakan->id)->first();
        $this->assertNotNull($pindahMilik);
        $this->assertEquals('Menunggu', $pindahMilik->status_kelulusan);

        // Admin jajahan lain (Bachok) disekat daripada meluluskan ternakan Pasir Mas
        $respBachokLulus = $this->actingAs($adminBachok)->post(route('eptr.borang-b.lulus', $pindahMilik->id));
        $respBachokLulus->assertSessionHas('error');
        $this->assertEquals('Menunggu', $pindahMilik->fresh()->status_kelulusan);

        // Admin Pasir Mas meluluskan pindah milik
        $respPasirMasLulus = $this->actingAs($adminPasirMas)->post(route('eptr.borang-b.lulus', $pindahMilik->id));
        $respPasirMasLulus->assertRedirect();

        $this->assertEquals('Diluluskan', $pindahMilik->fresh()->status_kelulusan);
        $this->assertEquals($adminPasirMas->id, $pindahMilik->fresh()->diluluskan_oleh);
        $this->assertEquals($pemunya2->id, $ternakan->fresh()->pemunya_id);
    }
}
