<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('action_lists', function (Blueprint $table) {
            $table->id();
            $table->string('kod_dokumen')->default('PK-RK-61');
            $table->string('no_bil')->nullable(); // No. Siri / BIL
            $table->string('jajahan')->default('Pasir Puteh');
            $table->date('tarikh')->nullable();
            
            // A. Maklumat Pelanggan
            $table->string('masa_pendaftaran')->nullable();
            $table->string('kategori_pelanggan')->default('Individu'); // Individu / Syarikat
            $table->string('nama_pelanggan')->nullable();
            $table->string('no_kp')->nullable();
            $table->text('alamat')->nullable();
            $table->string('mukim')->nullable();
            $table->string('poskod')->nullable();
            $table->string('daerah')->nullable();
            $table->string('telefon')->nullable();
            $table->string('no_rujukan')->nullable(); // 20. Rujukan
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // B. Butir-butir Perkhidmatan
            $table->text('catatan_perkhidmatan_dipohon')->nullable(); // 7. Catatan ringkas dipohon

            // C. Maklumat Temujanji
            $table->string('nama_pegawai')->nullable(); // 8. Nama Pegawai
            $table->string('masa_pegawai')->nullable();
            $table->string('masa_temujanji_mula')->nullable(); // 9. Mula
            $table->string('masa_temujanji_hingga')->nullable(); // 9. Hingga
            $table->text('maklumat_pelanggan_berlainan')->nullable(); // 10. Maklumat jika berlainan
            $table->text('maklumat_tambahan')->nullable(); // 11. Peta lokasi / catatan
            $table->string('lampiran_peta')->nullable();

            // D. Maklumat Perkhidmatan Yang Diberi
            $table->json('perkhidmatan_diberi')->nullable(); // 12. rawatan lapangan, klinik, pembedahan, dsb.
            $table->string('keterangan_pembedahan')->nullable();
            $table->string('keterangan_projek')->nullable();
            $table->string('keterangan_lain')->nullable();
            
            // 13. Catatan Ringkas
            $table->json('jenis_ternakan')->nullable(); // Lembu, Kerbau, Kambing, etc.
            $table->string('jenis_ternakan_lain')->nullable();
            $table->integer('bil_ternakan')->nullable();
            $table->integer('bil_yang_ada')->nullable();
            
            // 17. Laporan & 21. Penggunaan Ubat
            $table->longText('laporan')->nullable();
            $table->longText('penggunaan_ubat')->nullable();

            // E. Pengakuan Pelanggan & Pengesahan Pegawai
            $table->string('tandatangan_pelanggan_nama')->nullable(); // 14. Nama Penandatangan
            $table->date('tandatangan_pelanggan_tarikh')->nullable();
            $table->string('tandatangan_pelanggan_masa')->nullable();
            $table->string('kepuasan_pelanggan')->nullable(); // 15. Puashati / Tidak puashati / Boleh dipertimbangkan
            $table->text('cadangan_pelanggan')->nullable(); // 16. Cadangan
            $table->decimal('bayaran', 10, 2)->default(0.00); // 18. Bayaran
            $table->string('no_resit')->nullable(); // No. Resit
            $table->text('pengesahan_ulasan_pegawai')->nullable(); // 19. Pengesahan dan Ulasan Pegawai
            
            // Kawalan Sistem & Hubungan
            $table->string('status')->default('Selesai'); // Deraf, Selesai, Disahkan
            $table->foreignId('pegawai_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('temujanji_id')->nullable()->constrained('klinik_temujanji')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_lists');
    }
};
