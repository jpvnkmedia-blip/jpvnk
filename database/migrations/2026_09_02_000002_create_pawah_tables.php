<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Surat Perjanjian Pawah
        Schema::create('pawah_perjanjian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Peserta Pawah
            $table->string('no_perjanjian', 50)->unique();
            $table->string('nama_program', 100)->default('Program Pawah Ternakan Negeri Kelantan');
            $table->string('jenis_pawah', 50)->default('Lembu Hibrid'); // Lembu Hibrid, Kambing Tenusu, Rusa
            $table->string('jenis_ternakan_sedia_ada', 100)->nullable(); // Jenis ternakan sekarang jika ada (Lembu, Kambing, dll)
            $table->integer('bilangan_ternakan_sedia_ada')->default(0)->nullable();
            $table->date('tarikh_mula');
            $table->date('tarikh_tamat');
            $table->integer('tempoh_tahun')->default(3);
            $table->integer('bilangan_induk')->default(1);
            $table->text('syarat_pemulangan')->nullable(); // contoh: Memulangkan 1 ekor anak betina pertama berumur 1 tahun ke atas
            $table->string('jajahan', 50);
            $table->string('status', 30)->default('Aktif'); // Draf, Aktif, Selesai, Dibatalkan, Pelanggaran Kontrak, Menunggu Kelulusan, Ditolak
            $table->string('pegawai_penyelia')->nullable();
            $table->text('catatan')->nullable();
            $table->string('dokumen_perjanjian')->nullable();
            $table->timestamps();
        });

        // 2. Lembu/Ternakan di bawah Pawah (Mengambil daripada pendaftaran EPTR)
        Schema::create('pawah_ternakan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pawah_perjanjian_id')->constrained('pawah_perjanjian')->cascadeOnDelete();
            $table->foreignId('ternakan_id')->constrained('ternakan')->cascadeOnDelete(); // Lembu EPTR
            $table->string('status_induk', 30)->default('Aktif'); // Aktif, Bunting, Telah Melahirkan, Sakit, Mati, Dipindahkan
            $table->date('tarikh_serahan')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 3. Rekod Kelahiran Anak Pawah
        Schema::create('pawah_rekod_kelahiran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pawah_perjanjian_id')->constrained('pawah_perjanjian')->cascadeOnDelete();
            $table->foreignId('ternakan_induk_id')->constrained('ternakan')->cascadeOnDelete(); // Induk EPTR
            $table->string('no_tag_anak', 50)->nullable();
            $table->enum('jantina_anak', ['Jantan', 'Betina']);
            $table->date('tarikh_kelahiran');
            $table->decimal('berat_lahir_kg', 5, 2)->nullable();
            $table->string('baka_bapa', 50)->nullable();
            $table->string('warna', 50)->nullable();
            $table->string('status_anak', 40)->default('Dalam Peliharaan'); // Dalam Peliharaan, Telah Dipulangkan, Dijual, Mati
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 4. Rekod Kesihatan & Pemantauan Pawah
        Schema::create('pawah_rekod_kesihatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pawah_perjanjian_id')->constrained('pawah_perjanjian')->cascadeOnDelete();
            $table->foreignId('ternakan_id')->constrained('ternakan')->cascadeOnDelete();
            $table->date('tarikh_lawatan');
            $table->string('status_fizikal', 50)->default('Baik'); // Baik, Sederhana, Kurus, Sakit
            $table->boolean('status_bunting')->default(false);
            $table->string('diagnosis')->nullable();
            $table->text('rawatan_diberikan')->nullable();
            $table->string('pegawai_pemeriksa');
            $table->text('syor_tindakan')->nullable();
            $table->timestamps();
        });

        // 5. Rekod Penyelesaian Program Pawah
        Schema::create('pawah_penyelesaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pawah_perjanjian_id')->constrained('pawah_perjanjian')->cascadeOnDelete();
            $table->date('tarikh_penyelesaian');
            $table->integer('bilangan_anak_dipulangkan')->default(1);
            $table->string('status_penyelesaian', 40)->default('Selesai Penuh'); // Selesai Penuh, Tebus Guna Tunai, Ganti Rugi, Dibatalkan
            $table->decimal('jumlah_bayaran_tebus_guna', 10, 2)->nullable();
            $table->string('resit_pembayaran')->nullable();
            $table->string('pegawai_pengesah');
            $table->text('perakuan')->nullable();
            $table->string('sijil_penyelesaian')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pawah_penyelesaian');
        Schema::dropIfExists('pawah_rekod_kesihatan');
        Schema::dropIfExists('pawah_rekod_kelahiran');
        Schema::dropIfExists('pawah_ternakan');
        Schema::dropIfExists('pawah_perjanjian');
    }
};
