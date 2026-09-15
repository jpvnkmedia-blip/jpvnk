<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ladang / Premis Unggas
        Schema::create('epu_ladang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama_pemohon_atau_syarikat');
            $table->string('no_syarikat_atau_ssm')->nullable();
            $table->string('nama_ladang');
            $table->string('no_geran_tanah')->nullable();
            $table->string('no_lot')->nullable();
            $table->decimal('luas_tanah_ekar', 8, 2)->nullable();
            $table->string('jajahan', 50);
            $table->string('mukim', 50)->nullable();
            $table->text('alamat_ladang');
            $table->string('status_pemilikan_tanah', 50)->default('Milik Sendiri'); // Milik Sendiri, Sewa, Pajakan, Tanah Rizab
            $table->enum('sistem_reban', ['Tertutup', 'Terbuka', 'Semi-Tertutup'])->default('Tertutup');
            $table->integer('kapasiti_maksimum_unggas')->default(5000);
            $table->integer('jarak_kediaman_terdekat_meter')->nullable();
            $table->integer('jarak_sungai_terdekat_meter')->nullable();
            $table->string('kaedah_kawalan_lalat_bau')->nullable();
            $table->string('kaedah_pelupusan_tinja')->nullable();
            $table->string('kaedah_pelupusan_bangkai')->nullable();
            $table->string('status_ladang', 30)->default('Aktif');
            $table->timestamps();
        });

        // 2. Permohonan & Lesen EPU (Borang A - Pendaftaran Baru, Borang B - Lesen, Borang C - Pembaharuan)
        Schema::create('epu_permohonan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('epu_ladang_id')->constrained('epu_ladang')->cascadeOnDelete();
            $table->string('no_rujukan_permohonan', 50)->unique();
            $table->enum('jenis_permohonan', ['Baru', 'Pembaharuan', 'Pindaan Syarat'])->default('Baru');
            $table->string('jenis_unggas', 50); // Ayam Pedaging (Broiler), Ayam Penelur (Layer), Ayam Kampung, Itik, Puyuh, Burung Unta
            $table->integer('bilangan_semasa_unggas')->default(1000);
            $table->string('no_lesen_epu', 50)->nullable()->unique();
            $table->date('tarikh_mula_lesen')->nullable();
            $table->date('tarikh_tamat_lesen')->nullable();
            $table->decimal('yuran_lesen', 8, 2)->default(100.00);
            $table->string('no_resit_bayaran', 50)->nullable();
            $table->string('status', 30)->default('Dihantar'); // Draf, Dihantar, Lawatan_Tapak, Diluluskan, Ditolak, Tamat_Tempoh
            $table->text('syarat_khas_lesen')->nullable();
            $table->text('catatan_pegawai')->nullable();
            $table->foreignId('diluluskan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tarikh_kelulusan')->nullable();
            $table->string('dokumen_sokongan')->nullable();
            $table->timestamps();
        });

        // 3. Laporan Pemeriksaan Tapak & Penguatkuasaan EPU (Borang D)
        Schema::create('epu_pemeriksaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('epu_ladang_id')->constrained('epu_ladang')->cascadeOnDelete();
            $table->foreignId('epu_permohonan_id')->nullable()->constrained('epu_permohonan')->nullOnDelete();
            $table->foreignId('pegawai_id')->constrained('users')->cascadeOnDelete();
            $table->date('tarikh_pemeriksaan');
            $table->integer('skor_kebersihan_peratus')->default(80);
            $table->boolean('patuh_zon_penampan')->default(true);
            $table->boolean('kawalan_lalat_memuaskan')->default(true);
            $table->boolean('kawalan_bau_memuaskan')->default(true);
            $table->boolean('sistem_longkang_sempurna')->default(true);
            $table->text('penemuan_pemeriksaan');
            $table->text('syor_dan_arahan');
            $table->enum('status_keputusan', ['Lulus', 'Lulus Bersyarat', 'Gagal', 'Notis Dikeluarkan'])->default('Lulus');
            $table->string('no_notis_pematuhan', 50)->nullable();
            $table->date('tarikh_akhir_pematuhan')->nullable();
            $table->string('gambar_pemeriksaan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('epu_pemeriksaan');
        Schema::dropIfExists('epu_permohonan');
        Schema::dropIfExists('epu_ladang');
    }
};
