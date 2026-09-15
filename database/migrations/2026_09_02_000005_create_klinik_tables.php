<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Temujanji Klinik Haiwan
        Schema::create('klinik_temujanji', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('no_temujanji', 50)->unique();
            $table->string('jenis_haiwan', 50); // Lembu, Kambing, Kucing, Anjing, Kuda, Unggas, Lain-lain
            $table->string('nama_haiwan')->nullable();
            $table->string('baka', 50)->nullable();
            $table->enum('jantina_haiwan', ['Jantan', 'Betina', 'Tidak Diketahui'])->default('Tidak Diketahui');
            $table->string('umur_haiwan', 30)->nullable();
            $table->text('simptom_atau_tujuan'); // Pemeriksaan Kesihatan, Vaksinasi, Rawatan Sakit, Pembedahan/Kembiri, Surgeri Kecemasan
            $table->date('tarikh_temujanji');
            $table->enum('sesi', ['Pagi (8:30 AM - 12:30 PM)', 'Petang (2:00 PM - 4:30 PM)'])->default('Pagi (8:30 AM - 12:30 PM)');
            $table->string('klinik_jajahan', 50); // Pusat Veterinar Kota Bharu, Machang, Pasir Mas, dll
            $table->string('status', 30)->default('Menunggu'); // Menunggu, Disahkan, Sedang Rawatan, Selesai, Batal
            $table->text('catatan_pegawai')->nullable();
            $table->timestamps();
        });

        // 2. Rekod Rawatan & Kad Kesihatan Haiwan
        Schema::create('klinik_rawatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('klinik_temujanji_id')->nullable()->constrained('klinik_temujanji')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Pemilik
            $table->string('no_rekod_rawatan', 50)->unique();
            $table->date('tarikh_rawatan');
            $table->string('pegawai_veterinar');
            $table->decimal('berat_badan_kg', 6, 2)->nullable();
            $table->decimal('suhu_celsius', 4, 1)->nullable();
            $table->text('diagnosis');
            $table->text('rawatan_diberikan');
            $table->text('ubat_diberikan')->nullable();
            $table->string('vaksinasi')->nullable();
            $table->date('tarikh_temujanji_susulan')->nullable();
            $table->decimal('kos_rawatan', 8, 2)->default(0.00);
            $table->string('status_bayaran', 30)->default('Selesai Bayar'); // Belum Bayar, Selesai Bayar, Dikecualikan
            $table->text('nasihat_veterinar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('klinik_rawatan');
        Schema::dropIfExists('klinik_temujanji');
    }
};
