<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

return new class extends Migration
{
    public function up(): void
    {
        // Bersihkan sebarang rekod ujian dengan email atau IC yang sama
        User::where(function ($q) {
            $q->where('email', 'hanif@dvs.gov.my')
              ->orWhere('ic_number', '900729035413');
        })->where('role', '!=', 'super_admin')->delete();

        // Kemaskini akaun Super Admin
        $superAdmin = User::where('role', 'super_admin')->first();

        if ($superAdmin) {
            $superAdmin->update([
                'name' => 'Mohd Hanif bin Ismail',
                'email' => 'hanif@dvs.gov.my',
                'ic_number' => '900729035413',
                'password' => Hash::make('super@DVS5413'),
                'status' => 'Aktif',
            ]);
        } else {
            User::firstOrCreate(
                ['ic_number' => '900729035413'],
                [
                    'name' => 'Mohd Hanif bin Ismail',
                    'email' => 'hanif@dvs.gov.my',
                    'phone' => '019-9112233',
                    'address' => 'Ibu Pejabat JPVNK, Jalan Kubang Kachang, 15200 Kota Bharu, Kelantan',
                    'jajahan' => 'Kota Bharu',
                    'role' => 'super_admin',
                    'auth_provider' => 'manual',
                    'status' => 'Aktif',
                    'password' => Hash::make('super@DVS5413'),
                ]
            );
        }
    }

    public function down(): void
    {
        // No-op
    }
};
