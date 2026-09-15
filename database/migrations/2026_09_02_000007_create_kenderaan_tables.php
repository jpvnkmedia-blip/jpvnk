<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Senarai Kenderaan Jabatan
        Schema::create('kenderaan', function (Blueprint $table) {
            $table->id();
            $table->string('no_pendaftaran', 20)->unique();
            $table->string('jenis_kenderaan', 50); // Pacuan 4 Roda (4x4), Van, Kereta Sedan, Lori Sederhana, Motosikal
            $table->string('model', 50); // Toyota Hilux, Isuzu D-Max, Toyota Hiace, Proton Persona, dll
            $table->integer('tahun_buatan')->nullable();
            $table->integer('kapasiti_penumpang')->default(4);
            $table->string('jajahan_penempatan', 50)->default('Ibu Pejabat Kota Bharu');
            $table->string('status', 30)->default('Sedia'); // Sedia, Sedang Digunakan, Dalam Servis, Rosak
            $table->string('lokasi_kunci')->nullable();
            $table->integer('odometer_semasa_km')->default(0);
            $table->date('tarikh_tamat_cukai_jalan')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 2. Tempahan Kenderaan Rasmi Jabatan
        Schema::create('kenderaan_tempahan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Pemohon
            $table->foreignId('kenderaan_id')->nullable()->constrained('kenderaan')->nullOnDelete();
            $table->string('no_tempahan', 50)->unique();
            $table->text('tujuan_perjalanan');
            $table->string('destinasi');
            $table->date('tarikh_mula');
            $table->time('masa_mula');
            $table->date('tarikh_tamat');
            $table->time('masa_tamat');
            $table->integer('bilangan_penumpang')->default(1);
            $table->text('senarai_nama_penumpang')->nullable();
            $table->string('pemandu_nama')->nullable();
            $table->integer('odometer_keluar')->nullable();
            $table->integer('odometer_masuk')->nullable();
            $table->string('status', 30)->default('Menunggu'); // Menunggu, Diluluskan, Ditolak, Sedang Digunakan, Selesai, Batal
            $table->text('catatan_kelulusan')->nullable();
            $table->foreignId('diluluskan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kenderaan_tempahan');
        Schema::dropIfExists('kenderaan');
    }
};
