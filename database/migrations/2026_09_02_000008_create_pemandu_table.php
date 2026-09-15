<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemandu', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('no_kp', 20)->unique();
            $table->string('no_pekerja', 30)->nullable();
            $table->string('no_telefon', 20);
            $table->string('kelas_lesen', 50)->default('D, GDL'); // D, DA, E, GDL, PSV
            $table->date('tarikh_tamat_lesen')->nullable();
            $table->string('jajahan_penempatan', 50)->default('Ibu Pejabat Kota Bharu');
            $table->string('status', 30)->default('Aktif'); // Aktif, Bertugas, Cuti, Tidak Aktif
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemandu');
    }
};
