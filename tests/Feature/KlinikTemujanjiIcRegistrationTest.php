<?php

namespace Tests\Feature;

use App\Models\KlinikHaiwan;
use App\Models\KlinikTemujanji;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KlinikTemujanjiIcRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminKlinik;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminKlinik = User::factory()->create([
            'role' => 'admin_klinik',
            'email' => 'adminklinik@example.com',
            'ic_number' => '800101031111',
        ]);
    }

    public function test_semak_pemilik_returns_found_when_user_exists(): void
    {
        $existingUser = User::factory()->create([
            'name' => 'Ahmad Bin Ali',
            'ic_number' => '900101035555',
            'email' => 'ahmad@example.com',
            'phone' => '0123456789',
            'jajahan' => 'Kota Bharu',
            'address' => 'No 123 Kampung Sireh',
            'role' => 'orang_awam',
        ]);

        $response = $this->actingAs($this->adminKlinik)
            ->getJson(route('klinik.semak_pemilik', ['no_kp' => '900101-03-5555']));

        $response->assertOk();
        $response->assertJson([
            'found' => true,
            'user_id' => $existingUser->id,
            'nama' => 'Ahmad Bin Ali',
            'no_kp' => '900101035555',
            'no_telefon' => '0123456789',
            'emel' => 'ahmad@example.com',
            'jajahan' => 'Kota Bharu',
            'alamat' => 'No 123 Kampung Sireh',
        ]);
    }

    public function test_semak_pemilik_returns_not_found_when_user_does_not_exist(): void
    {
        $response = $this->actingAs($this->adminKlinik)
            ->getJson(route('klinik.semak_pemilik', ['no_kp' => '999999039999']));

        $response->assertOk();
        $response->assertJson([
            'found' => false,
        ]);
    }

    public function test_store_temujanji_with_existing_ic_links_existing_user(): void
    {
        $existingUser = User::factory()->create([
            'name' => 'Siti Aminah',
            'ic_number' => '920202036666',
            'email' => 'siti@example.com',
            'phone' => '01122334455',
            'jajahan' => 'Pasir Mas',
            'role' => 'orang_awam',
        ]);

        $postData = [
            'klinik_jajahan' => 'Pusat Veterinar Jajahan Pasir Mas',
            'jenis_haiwan' => 'Kucing',
            'nama_haiwan' => 'Comel',
            'baka' => 'Domestic Short Hair',
            'jantina_haiwan' => 'Betina',
            'umur_haiwan' => '1 tahun',
            'simptom_atau_tujuan' => 'Vaksinasi tahunan pertama',
            'tarikh_temujanji' => now()->addDays(2)->format('Y-m-d'),
            'sesi' => 'Pagi (8:30 AM - 12:30 PM)',
            'no_kp_pemilik' => '920202-03-6666',
            'nama_pemilik' => 'Siti Aminah',
            'no_telefon_pemilik' => '01122334455',
        ];

        $response = $this->actingAs($this->adminKlinik)
            ->post(route('klinik.store'), $postData);

        $temujanji = KlinikTemujanji::first();
        $this->assertNotNull($temujanji);
        $response->assertRedirect(route('klinik.show', $temujanji->id));

        $this->assertDatabaseHas('klinik_temujanji', [
            'user_id' => $existingUser->id,
            'nama_haiwan' => 'Comel',
            'jenis_haiwan' => 'Kucing',
        ]);
    }

    public function test_store_temujanji_with_new_ic_creates_new_user_and_links_temujanji(): void
    {
        $newNoKp = '950505037777';

        $postData = [
            'klinik_jajahan' => 'Pusat Veterinar Jajahan Tumpat',
            'jenis_haiwan' => 'Anjing',
            'nama_haiwan' => 'Lucky',
            'baka' => 'Mix',
            'jantina_haiwan' => 'Jantan',
            'umur_haiwan' => '2 tahun',
            'simptom_atau_tujuan' => 'Demam dan kurang selera makan',
            'tarikh_temujanji' => now()->addDays(3)->format('Y-m-d'),
            'sesi' => 'Petang (2:00 PM - 4:30 PM)',
            'no_kp_pemilik' => '950505-03-7777',
            'nama_pemilik' => 'Mohd Faizal',
            'no_telefon_pemilik' => '0199998888',
            'emel_pemilik' => 'faizal@example.com',
            'alamat_pemilik' => 'Lot 456 Kampung Laut, Tumpat',
            'jajahan_pemilik' => 'Tumpat',
        ];

        $response = $this->actingAs($this->adminKlinik)
            ->post(route('klinik.store'), $postData);

        $temujanji = KlinikTemujanji::first();
        $this->assertNotNull($temujanji);
        $response->assertRedirect(route('klinik.show', $temujanji->id));

        // Check user is created
        $this->assertDatabaseHas('users', [
            'ic_number' => $newNoKp,
            'name' => 'Mohd Faizal',
            'email' => 'faizal@example.com',
            'phone' => '0199998888',
            'address' => 'Lot 456 Kampung Laut, Tumpat',
            'jajahan' => 'Tumpat',
            'role' => 'orang_awam',
        ]);

        $createdUser = User::where('ic_number', $newNoKp)->first();
        $this->assertNotNull($createdUser);

        // Check appointment is created with new user id
        $this->assertDatabaseHas('klinik_temujanji', [
            'user_id' => $createdUser->id,
            'nama_haiwan' => 'Lucky',
        ]);
    }
}