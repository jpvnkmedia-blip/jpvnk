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
        $this->assertTrue($user->canAccessKenderaan());
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
}
