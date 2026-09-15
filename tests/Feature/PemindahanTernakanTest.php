<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pemunya;
use App\Models\Ternakan;
use App\Models\PemindahanTernakan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PemindahanTernakanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_pemindahan_index_page_is_accessible()
    {
        $admin = User::where('role', 'admin_eptr')->first();
        $response = $this->actingAs($admin)->get(route('eptr.pemindahan.index'));
        $response->assertStatus(200);
        $response->assertSee('Permohonan Pemindahan Ternakan');
    }

    public function test_pemindahan_create_page_is_accessible()
    {
        $admin = User::where('role', 'admin_eptr')->first();
        $response = $this->actingAs($admin)->get(route('eptr.pemindahan.create'));
        $response->assertStatus(200);
        $response->assertSee('Borang Permohonan Pemindahan Ternakan');
        $response->assertSee('Senarai Pengenalan Ternakan (50 Baris No. Tag)');
        $response->assertSee('Pilih Ternakan Dari Rekod EPTR Pemohon');
    }

    public function test_orang_awam_create_page_has_locked_reference_and_eptr_defaults()
    {
        $user = User::where('role', 'penternak')->first();
        $pemunya = Pemunya::where('user_id', $user->id)->first();
        if (!$pemunya) {
            $pemunya = Pemunya::create([
                'user_id' => $user->id,
                'nama' => $user->name,
                'no_kp' => $user->ic_number ?? '920101035555',
                'no_telefon' => $user->phone ?? '0198887777',
                'alamat' => 'Kampung Melor, Kota Bharu',
                'jajahan' => 'Kota Bharu',
            ]);
        }

        // Add a registered animal to EPTR
        Ternakan::create([
            'pemunya_id' => $pemunya->id,
            'no_tag' => 'KB9901',
            'jenis_ternakan' => 'Lembu',
            'baka' => 'Brahman',
            'jantina' => 'Jantan',
            'jajahan' => 'Kota Bharu',
            'status' => 'Aktif',
            'status_kelulusan' => 'Diluluskan',
        ]);

        $response = $this->actingAs($user)->get(route('eptr.pemindahan.create'));
        $response->assertStatus(200);
        $response->assertSee('Dijana secara automatik oleh sistem');
        $response->assertSee($pemunya->nama);
        $response->assertSee('KB9901');
    }

    public function test_recipient_defaults_to_applicant_when_empty()
    {
        $user = User::where('role', 'penternak')->first();
        $pemunya = Pemunya::where('user_id', $user->id)->first() ?? Pemunya::first();

        $payload = [
            'pemunya_id' => $pemunya->id,
            'jajahan_asal' => 'Pasir Mas',
            'pemohon_nama' => 'Wan Ismail Bin Wan Daud',
            'pemohon_ic' => '750303035555',
            'pemohon_tel' => '0191239999',
            'pemohon_alamat' => 'Kampung Meranti, Pasir Mas, Kelantan',
            'jenis_ternakan' => 'LEMBU',
            'bilangan_jantan' => 1,
            'bilangan_betina' => 0,
            'tujuan_pemindahan' => 'PEMELIHARAAN',
            'penerima_nama' => '', // Empty, should default to pemohon
            'penerima_ic' => '',
            'penerima_tel' => '',
            'penerima_alamat' => '',
            'tarikh_jangka_pindah' => now()->addDays(7)->format('Y-m-d'),
            'no_kenderaan' => 'DCA 4321',
            'tags' => [
                1 => ['no_tag' => 'PM0101', 'jantina' => 'Jantan'],
            ],
        ];

        $response = $this->actingAs($user)->post(route('eptr.pemindahan.store'), $payload);
        $response->assertRedirect();

        $pemindahan = PemindahanTernakan::where('no_kenderaan', 'DCA 4321')->first();
        $this->assertNotNull($pemindahan);
        $this->assertEquals('WAN ISMAIL BIN WAN DAUD', $pemindahan->pemohon_nama);
        $this->assertEquals('WAN ISMAIL BIN WAN DAUD', $pemindahan->penerima_nama);
        $this->assertEquals('750303035555', $pemindahan->penerima_ic);
        $this->assertEquals('0191239999', $pemindahan->penerima_tel);
        $this->assertEquals('KAMPUNG MERANTI, PASIR MAS, KELANTAN', $pemindahan->penerima_alamat);
        $this->assertStringContainsString('JPVPM', $pemindahan->no_rujukan);
    }

    public function test_tag_gender_is_strictly_derived_from_registered_ternakan()
    {
        $admin = User::where('role', 'admin_eptr')->first();
        $pemunya = Pemunya::first();

        // Create a registered animal with Betina gender
        Ternakan::create([
            'pemunya_id' => $pemunya->id,
            'no_tag' => 'TAG-BETINA-01',
            'jenis_ternakan' => 'Lembu',
            'baka' => 'Kedah-Kelantan',
            'jantina' => 'Betina',
            'jajahan' => 'Bachok',
            'status' => 'Aktif',
            'status_kelulusan' => 'Diluluskan',
        ]);

        // Submit form attempting to set jantina to Jantan
        $payload = [
            'pemunya_id' => $pemunya->id,
            'jajahan_asal' => 'Bachok',
            'pemohon_nama' => 'Haji Hassan',
            'pemohon_ic' => '880101035555',
            'pemohon_tel' => '0199998888',
            'pemohon_alamat' => 'Bachok',
            'jenis_ternakan' => 'LEMBU',
            'tujuan_pemindahan' => 'PEMELIHARAAN',
            'tarikh_jangka_pindah' => now()->addDays(5)->format('Y-m-d'),
            'tags' => [
                1 => ['no_tag' => 'TAG-BETINA-01', 'jantina' => 'Jantan'], // Submitted as Jantan
            ],
        ];

        $response = $this->actingAs($admin)->post(route('eptr.pemindahan.store'), $payload);
        $response->assertRedirect();

        $pemindahan = PemindahanTernakan::where('pemohon_nama', 'HAJI HASSAN')->first();
        $this->assertNotNull($pemindahan);
        // Gender must strictly be 'Betina' as per EPTR registered livestock
        $this->assertEquals('Betina', $pemindahan->senarai_tag[0]['jantina']);
        $this->assertEquals(0, $pemindahan->bilangan_jantan);
        $this->assertEquals(1, $pemindahan->bilangan_betina);
    }

    public function test_pemindahan_can_be_created_and_stored()
    {
        $admin = User::where('role', 'admin_eptr')->first();
        $pemunya = Pemunya::first();

        $tags = [
            1 => ['no_tag' => 'MY03010001', 'jantina' => 'Jantan'],
            2 => ['no_tag' => 'MY03010002', 'jantina' => 'Jantan'],
            3 => ['no_tag' => 'MY03010003', 'jantina' => 'Betina'],
        ];

        $payload = [
            'pemunya_id' => $pemunya ? $pemunya->id : null,
            'jajahan_asal' => 'Bachok',
            'pemohon_nama' => 'Haji Hassan Bin Ismail',
            'pemohon_ic' => '880101035555',
            'pemohon_tel' => '0199998888',
            'pemohon_alamat' => 'Kampung Jelawat, 16370 Bachok, Kelantan',
            'jenis_ternakan' => 'LEMBU',
            'bilangan_jantan' => 2,
            'bilangan_betina' => 1,
            'tujuan_pemindahan' => 'PEMELIHARAAN',
            'penerima_nama' => 'Ahmad Penerima',
            'penerima_ic' => '900202025555',
            'penerima_tel' => '0123456789',
            'penerima_alamat' => 'No 12, Jalan Jaya, Bukit Mertajam, Pulau Pinang',
            'penerima_jajahan' => 'Bukit Mertajam',
            'penerima_negeri' => 'PULAU PINANG',
            'tarikh_jangka_pindah' => now()->addDays(5)->format('Y-m-d'),
            'no_kenderaan' => 'DAB 1234',
            'tarikh_fmd_p1' => '2026-01-10',
            'tarikh_fmd_p2' => '2026-02-10',
            'tarikh_fmd_booster' => null,
            'tarikh_lsd' => '2026-03-01',
            'nama_penyuntik_1' => 'Ali Bin Abu',
            'nama_penyuntik_2' => 'Bakar Bin Omar',
            'tags' => $tags,
        ];

        $response = $this->actingAs($admin)->post(route('eptr.pemindahan.store'), $payload);
        $response->assertRedirect();

        $this->assertDatabaseHas('pemindahan_ternakans', [
            'jenis_ternakan' => 'LEMBU',
            'bilangan_jantan' => 2,
            'bilangan_betina' => 1,
            'penerima_nama' => 'AHMAD PENERIMA',
            'no_kenderaan' => 'DAB 1234',
            'jajahan_asal' => 'Bachok',
        ]);

        $pemindahan = PemindahanTernakan::where('penerima_nama', 'AHMAD PENERIMA')->first();
        $this->assertNotNull($pemindahan);
        $this->assertCount(3, $pemindahan->senarai_tag);
    }

    public function test_pemindahan_show_and_approval_workflow()
    {
        $admin = User::where('role', 'admin_eptr')->first();
        $pemunya = Pemunya::first();

        $pemindahan = PemindahanTernakan::create([
            'no_rujukan' => 'JPVB 600/8/13-2026/01',
            'pemunya_id' => $pemunya ? $pemunya->id : null,
            'user_id' => $admin->id,
            'pemohon_nama' => 'Ahmad Pemohon',
            'pemohon_ic' => '880101035555',
            'pemohon_tel' => '0199998888',
            'pemohon_alamat' => 'Kampung Jelawat',
            'jajahan_asal' => 'Bachok',
            'jenis_ternakan' => 'LEMBU',
            'bilangan_jantan' => 5,
            'bilangan_betina' => 5,
            'tujuan_pemindahan' => 'PEMELIHARAAN',
            'penerima_nama' => 'Penerima Test',
            'penerima_ic' => '900101011111',
            'penerima_tel' => '01122334455',
            'penerima_alamat' => 'Perak',
            'tarikh_jangka_pindah' => now()->addDays(3)->format('Y-m-d'),
            'no_kenderaan' => 'DD 9999',
            'status' => 'Menunggu Kelulusan',
            'senarai_tag' => [
                ['bil' => 1, 'no_tag' => 'TAG001', 'jantina' => 'Jantan'],
                ['bil' => 2, 'no_tag' => 'TAG002', 'jantina' => 'Betina'],
            ],
        ]);

        $showResponse = $this->actingAs($admin)->get(route('eptr.pemindahan.show', $pemindahan->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee($pemindahan->no_rujukan);
        $showResponse->assertSee('DD 9999');

        // Test Approval (POST)
        $lulusResponse = $this->actingAs($admin)->post(route('eptr.pemindahan.lulus', $pemindahan->id), [
            'catatan_kelulusan' => 'Permohonan diluluskan mengikut prosedur.',
        ]);
        $lulusResponse->assertRedirect();
        $this->assertEquals('Diluluskan', $pemindahan->fresh()->status);
    }

    public function test_pemindahan_all_print_views_render_successfully()
    {
        $admin = User::where('role', 'admin_eptr')->first();
        $pemunya = Pemunya::first();

        $pemindahan = PemindahanTernakan::create([
            'no_rujukan' => 'JPVB 600/8/13-2026/02',
            'pemunya_id' => $pemunya ? $pemunya->id : null,
            'user_id' => $admin->id,
            'pemohon_nama' => 'HAJI HASSAN',
            'pemohon_ic' => '700101035001',
            'pemohon_tel' => '0191234567',
            'pemohon_alamat' => 'KAMPUNG TANGOK, 16300 BACHOK, KELANTAN',
            'jajahan_asal' => 'Bachok',
            'jenis_ternakan' => 'LEMBU',
            'bilangan_jantan' => 4,
            'bilangan_betina' => 6,
            'tujuan_pemindahan' => 'SEMBELIHAN',
            'penerima_nama' => 'DAGING SEGAR SDN BHD',
            'penerima_ic' => '123456-X',
            'penerima_tel' => '0355554444',
            'penerima_alamat' => 'SEKSYEN 15, SHAH ALAM, SELANGOR',
            'tarikh_jangka_pindah' => '2026-09-15',
            'no_kenderaan' => 'WXY 8899',
            'tarikh_fmd_p1' => '2026-06-01',
            'tarikh_fmd_p2' => '2026-07-01',
            'tarikh_fmd_booster' => '2026-08-01',
            'tarikh_lsd' => '2026-06-15',
            'nama_penyuntik_1' => 'ENCIK RAZAK',
            'nama_penyuntik_2' => 'ENCIK KAMAL',
            'status' => 'Diluluskan',
            'senarai_tag' => [
                ['bil' => 1, 'no_tag' => 'MY0301001', 'jantina' => 'Jantan'],
                ['bil' => 2, 'no_tag' => 'MY0301002', 'jantina' => 'Betina'],
            ],
        ]);

        // 1. Surat Pengesahan Tarikh Suntikan FMD (Kn. 156)
        $r1 = $this->actingAs($admin)->get(route('eptr.pemindahan.cetak-surat-fmd', $pemindahan->id));
        $r1->assertStatus(200);
        $r1->assertSee('PENGESAHAN TARIKH SUNTIKAN FMD BAGI PEMINDAHAN TERNAKAN');
        $r1->assertSee('Kn. 156');
        $r1->assertSee('JPVB 600/8/13-2026/02');

        // 2. Borang Permohonan Pemindahan Ternakan / Produk
        $r2 = $this->actingAs($admin)->get(route('eptr.pemindahan.cetak-borang', $pemindahan->id));
        $r2->assertStatus(200);
        $r2->assertSee('PERMOHONAN PEMINDAHAN TERNAKAN / PRODUK');
        $r2->assertSee('HAJI HASSAN');
        $r2->assertSee('DAGING SEGAR SDN BHD');

        // 3. Deklarasi Status Haiwan Ruminan
        $r3 = $this->actingAs($admin)->get(route('eptr.pemindahan.cetak-deklarasi', $pemindahan->id));
        $r3->assertStatus(200);
        $r3->assertSee('DEKLARASI STATUS HAIWAN (RUMINAN)');
        $r3->assertSee('DVS/DSHR/0117/9/2021');

        // 4. Lampiran Senarai Pengenalan Ternakan (50 tags)
        $r4 = $this->actingAs($admin)->get(route('eptr.pemindahan.cetak-lampiran-tag', $pemindahan->id));
        $r4->assertStatus(200);
        $r4->assertSee('SENARAI PENGENALAN TERNAKAN');
        $r4->assertSee('MY0301001');

        // 5. Cetak Set Lengkap (4 Halaman)
        $r5 = $this->actingAs($admin)->get(route('eptr.pemindahan.cetak-set-lengkap', $pemindahan->id));
        $r5->assertStatus(200);
        $r5->assertSee('PENGESAHAN TARIKH SUNTIKAN FMD BAGI PEMINDAHAN TERNAKAN');
        $r5->assertSee('PERMOHONAN PEMINDAHAN TERNAKAN / PRODUK');
        $r5->assertSee('DEKLARASI STATUS HAIWAN (RUMINAN)');
        $r5->assertSee('SENARAI PENGENALAN TERNAKAN');
    }

    public function test_orang_awam_cannot_print_pemindahan_documents()
    {
        $user = User::where('role', 'penternak')->first();
        $pemindahan = PemindahanTernakan::create([
            'no_rujukan' => 'JPVB 600/8/13-2026/03',
            'user_id' => $user->id,
            'pemohon_nama' => 'PENTERNAK AWAM',
            'jajahan_asal' => 'Bachok',
            'jenis_ternakan' => 'LEMBU',
            'bilangan_jantan' => 1,
            'bilangan_betina' => 0,
            'tujuan_pemindahan' => 'PEMELIHARAAN',
            'penerima_nama' => 'PENERIMA AWAM',
            'penerima_alamat' => 'Kota Bharu',
            'status' => 'Diluluskan',
        ]);

        $r1 = $this->actingAs($user)->get(route('eptr.pemindahan.cetak-surat-fmd', $pemindahan->id));
        $r1->assertStatus(403);

        $r2 = $this->actingAs($user)->get(route('eptr.pemindahan.cetak-borang', $pemindahan->id));
        $r2->assertStatus(403);

        $r3 = $this->actingAs($user)->get(route('eptr.pemindahan.cetak-deklarasi', $pemindahan->id));
        $r3->assertStatus(403);

        $r4 = $this->actingAs($user)->get(route('eptr.pemindahan.cetak-lampiran-tag', $pemindahan->id));
        $r4->assertStatus(403);

        $r5 = $this->actingAs($user)->get(route('eptr.pemindahan.cetak-set-lengkap', $pemindahan->id));
        $r5->assertStatus(403);
    }

    public function test_staff_cannot_print_unapproved_pemindahan()
    {
        $admin = User::where('role', 'admin_eptr')->first();
        $pemindahan = PemindahanTernakan::create([
            'no_rujukan' => 'JPVB 600/8/13-2026/04',
            'user_id' => $admin->id,
            'pemohon_nama' => 'PEMOHON TEST',
            'jajahan_asal' => 'Kota Bharu',
            'jenis_ternakan' => 'LEMBU',
            'bilangan_jantan' => 2,
            'bilangan_betina' => 0,
            'tujuan_pemindahan' => 'PEMELIHARAAN',
            'penerima_nama' => 'PENERIMA TEST',
            'penerima_alamat' => 'Pasir Mas',
            'status' => 'Menunggu Kelulusan',
        ]);

        $r1 = $this->actingAs($admin)->get(route('eptr.pemindahan.cetak-surat-fmd', $pemindahan->id));
        $r1->assertRedirect();
        $r1->assertSessionHas('error');

        $r5 = $this->actingAs($admin)->get(route('eptr.pemindahan.cetak-set-lengkap', $pemindahan->id));
        $r5->assertRedirect();
        $r5->assertSessionHas('error');
    }

    public function test_letterhead_changes_according_to_approving_officer_jajahan()
    {
        $admin = User::where('role', 'admin_eptr')->first();

        // 1. Create a permit with jajahan_asal Pasir Mas
        $pemindahan = PemindahanTernakan::create([
            'no_rujukan' => 'JPVPM 600/8/13-2026/99',
            'user_id' => $admin->id,
            'pemohon_nama' => 'PENTERNAK PASIR MAS',
            'jajahan_asal' => 'Pasir Mas',
            'jenis_ternakan' => 'LEMBU',
            'bilangan_jantan' => 3,
            'bilangan_betina' => 2,
            'tujuan_pemindahan' => 'PEMELIHARAAN',
            'penerima_nama' => 'PENERIMA KOTA BHARU',
            'penerima_alamat' => 'Kota Bharu',
            'status' => 'Menunggu Kelulusan',
        ]);

        // 2. Approve specifying officer's jajahan as Pasir Mas
        $this->actingAs($admin)->post(route('eptr.pemindahan.lulus', $pemindahan->id), [
            'pegawai_jajahan' => 'Pasir Mas',
            'pegawai_nama' => 'DR. NIK FARHAN (PASIR MAS)',
        ]);

        $pemindahan->refresh();
        $this->assertEquals('Diluluskan', $pemindahan->status);
        $this->assertEquals('Pasir Mas', $pemindahan->pegawai_jajahan);
        $this->assertEquals('DR. NIK FARHAN (PASIR MAS)', $pemindahan->pegawai_nama);

        // 3. Check that Letterhead on Cetak 1 Set Lengkap dynamically reflects Pasir Mas office details
        $res = $this->actingAs($admin)->get(route('eptr.pemindahan.cetak-set-lengkap', $pemindahan->id));
        $res->assertStatus(200);
        $res->assertSee('JAJAHAN PASIR MAS');
        $res->assertSee('JALAN TASEK, 17000 PASIR MAS');
        $res->assertSee('09-7909242'); // Tel Pasir Mas
        $res->assertSee('09-7900675'); // Faks Pasir Mas
        $res->assertSee('ڤاسير مس'); // Jawi Pasir Mas
        $res->assertSee('DR. NIK FARHAN (PASIR MAS)');

        // 4. Now test approving with Kota Bharu officer on another permit
        $pemindahanKb = PemindahanTernakan::create([
            'no_rujukan' => 'JPVKB 600/8/13-2026/88',
            'user_id' => $admin->id,
            'pemohon_nama' => 'PENTERNAK KOTA BHARU',
            'jajahan_asal' => 'Kota Bharu',
            'jenis_ternakan' => 'LEMBU',
            'bilangan_jantan' => 1,
            'bilangan_betina' => 0,
            'tujuan_pemindahan' => 'PEMELIHARAAN',
            'penerima_nama' => 'PENERIMA PASIR PUTEH',
            'penerima_alamat' => 'Pasir Puteh',
            'status' => 'Menunggu Kelulusan',
        ]);

        $this->actingAs($admin)->post(route('eptr.pemindahan.lulus', $pemindahanKb->id), [
            'pegawai_jajahan' => 'Kota Bharu',
            'pegawai_nama' => 'DR. FAUZI BIN ABDULLAH',
        ]);

        $resKb = $this->actingAs($admin)->get(route('eptr.pemindahan.cetak-set-lengkap', $pemindahanKb->id));
        $resKb->assertStatus(200);
        $resKb->assertSee('JAJAHAN KOTA BHARU');
        $resKb->assertSee('JALAN KUBANG KACHANG, 15200 KOTA BHARU');
        $resKb->assertSee('09-7445566'); // Tel Kota Bharu
        $resKb->assertSee('09-7445577'); // Faks Kota Bharu
        $resKb->assertSee('كوتا بهارو'); // Jawi Kota Bharu
        $resKb->assertSee('DR. FAUZI BIN ABDULLAH');
    }

    public function test_destination_outside_kelantan_sets_luar_kelantan_jajahan()
    {
        $user = User::where('role', 'penternak')->first();
        $pemunya = Pemunya::where('user_id', $user->id)->first() ?? Pemunya::first();

        $payload = [
            'pemunya_id' => $pemunya->id,
            'jajahan_asal' => 'Pasir Mas',
            'pemohon_nama' => 'Wan Ismail Bin Wan Daud',
            'pemohon_ic' => '750303035555',
            'pemohon_tel' => '0191239999',
            'pemohon_alamat' => 'Pasir Mas, Kelantan',
            'jenis_ternakan' => 'LEMBU',
            'bilangan_jantan' => 1,
            'bilangan_betina' => 0,
            'tujuan_pemindahan' => 'PEMELIHARAAN',
            'penerima_nama' => 'Penerima Terengganu',
            'penerima_negeri' => 'TERENGGANU',
            'penerima_jajahan' => 'Luar Kelantan',
            'penerima_alamat' => 'Besut, Terengganu',
            'tarikh_jangka_pindah' => now()->addDays(7)->format('Y-m-d'),
            'no_kenderaan' => 'TRG 1234',
            'tags' => [
                1 => ['no_tag' => 'PM0101', 'jantina' => 'Jantan'],
            ],
        ];

        $res = $this->actingAs($user)->post(route('eptr.pemindahan.store'), $payload);

        $record = PemindahanTernakan::where('penerima_negeri', 'TERENGGANU')->latest()->first();
        $this->assertNotNull($record);
        $res->assertRedirect(route('eptr.pemindahan.show', $record->id));
        $this->assertEquals('TERENGGANU', $record->penerima_negeri);
        $this->assertEquals('Luar Kelantan', $record->penerima_jajahan);
    }
}
