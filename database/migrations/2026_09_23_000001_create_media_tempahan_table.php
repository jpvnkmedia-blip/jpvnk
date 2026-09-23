<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_tempahan', function (Blueprint $table) {
            $table->id();
            $table->string('no_rujukan')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // 1. Maklumat Pemohon
            $table->string('nama_pemohon');
            $table->string('jawatan')->nullable();
            $table->string('bahagian_unit_jajahan')->nullable();
            $table->string('no_telefon');
            $table->string('emel')->nullable();

            // 2. Maklumat Program
            $table->string('nama_program');
            $table->date('tarikh_program');
            $table->date('tarikh_tamat')->nullable();
            $table->string('masa_mula')->nullable();
            $table->string('masa_tamat')->nullable();
            $table->string('lokasi');
            $table->string('penganjur')->nullable();
            $table->string('pegawai_bertanggungjawab')->nullable();
            $table->integer('anggaran_peserta')->nullable();

            // 3. Jenis Permohonan (JSON array)
            $table->json('jenis_permohonan')->nullable();

            // 4. Butiran Keperluan (JSON / Text)
            $table->json('butiran_fotografi')->nullable();
            $table->json('butiran_poster')->nullable();
            $table->json('butiran_video')->nullable();
            $table->text('butiran_lain')->nullable();

            // 5. Keutamaan & Tarikh Diperlukan
            $table->date('tarikh_diperlukan')->nullable();
            $table->string('tahap_keutamaan')->default('Biasa'); // Biasa, Segera, Sangat Segera
            $table->text('sebab_segera')->nullable();

            // 6. Lampiran (JSON array)
            $table->json('lampiran')->nullable();

            // 7. Pengesahan
            $table->boolean('pengesahan_pemohon')->default(true);
            $table->timestamp('tarikh_hantar')->nullable();

            // 8. Aliran & Keputusan Unit Media
            $table->string('status')->default('Menunggu Kelulusan'); // Menunggu Kelulusan, Perlu Pembetulan, Diluluskan, Ditolak, Selesai, Dibatalkan
            $table->text('catatan_unit_media')->nullable();
            $table->string('pegawai_media_bertugas')->nullable();
            $table->text('peralatan_disediakan')->nullable();
            $table->foreignId('diluluskan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tarikh_kelulusan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_tempahan');
    }
};
