<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Pegawai Pelesen / Pengarah (EPU)
        User::firstOrCreate(
            ['ic_number' => '780410035123'],
            [
                'name' => 'Dr. Roslan bin Abdul Wahid',
                'email' => 'pelesen.epu@dvs.gov.my',
                'phone' => '019-9112233',
                'address' => 'Pejabat Pengarah / Pegawai Pelesen EPU, Ibu Pejabat JPVNK Kota Bharu',
                'jajahan' => 'Kota Bharu',
                'role' => 'pegawai_pelesen',
                'auth_provider' => 'manual',
                'status' => 'Aktif',
                'password' => Hash::make('super@DVS5123'),
            ]
        );

        // 2. Pegawai Verifikasi EPU (PPVJ Kota Bharu)
        User::firstOrCreate(
            ['ic_number' => '850312035521'],
            [
                'name' => 'Dr. Nor Azman bin Yusof',
                'email' => 'verifikasi.kb@dvs.gov.my',
                'phone' => '019-9223344',
                'address' => 'Pejabat Perkhidmatan Veterinar Jajahan Kota Bharu (PPVJ)',
                'jajahan' => 'Kota Bharu',
                'role' => 'pegawai_verifikasi_epu',
                'auth_provider' => 'manual',
                'status' => 'Aktif',
                'password' => Hash::make('super@DVS5521'),
            ]
        );

        // 3. Pegawai Verifikasi EPU (PPVJ Pasir Mas)
        User::firstOrCreate(
            ['ic_number' => '870815035541'],
            [
                'name' => 'En. Zulkifli bin Daud',
                'email' => 'verifikasi.pm@dvs.gov.my',
                'phone' => '019-9334455',
                'address' => 'Pejabat Perkhidmatan Veterinar Jajahan Pasir Mas (PPVJ)',
                'jajahan' => 'Pasir Mas',
                'role' => 'pegawai_verifikasi_epu',
                'auth_provider' => 'manual',
                'status' => 'Aktif',
                'password' => Hash::make('super@DVS5541'),
            ]
        );
    }

    public function down(): void
    {
        User::whereIn('ic_number', ['780410035123', '850312035521', '870815035541'])->delete();
    }
};
