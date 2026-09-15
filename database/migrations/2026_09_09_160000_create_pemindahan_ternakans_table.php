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
        Schema::create('pemindahan_ternakans', function (Blueprint $table) {
            $table->id();
            $table->string('no_rujukan')->unique();
            $table->foreignId('pemunya_id')->nullable()->constrained('pemunya')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Maklumat Asas & Jajahan
            $table->string('jajahan_asal')->default('Bachok');
            $table->date('tarikh_permohonan')->nullable();
            $table->date('tarikh_jangka_pindah')->nullable();
            $table->string('tujuan_pemindahan')->default('PEMELIHARAAN'); // PEMELIHARAAN, SEMBELIHAN, KORBAN, BIAK, JUALAN
            $table->string('jenis_ternakan')->default('LEMBU'); // LEMBU, KERBAU, KAMBING, BIRI-BIRI
            $table->integer('bilangan_jantan')->default(0);
            $table->integer('bilangan_betina')->default(0);
            $table->string('no_kenderaan')->nullable(); // e.g. CBE2227

            // Maklumat Pemohon / Penghantar
            $table->string('pemohon_nama');
            $table->string('pemohon_ic')->nullable();
            $table->string('pemohon_tel')->nullable();
            $table->string('pemohon_id_premis')->nullable(); // e.g. D100038
            $table->text('pemohon_alamat')->nullable();

            // Maklumat Penerima / Destinasi
            $table->string('penerima_nama');
            $table->string('penerima_ic')->nullable();
            $table->string('penerima_tel')->nullable();
            $table->string('penerima_id_premis')->nullable(); // e.g. D07191
            $table->text('penerima_alamat')->nullable();
            $table->string('penerima_jajahan')->nullable();
            $table->string('penerima_negeri')->default('KELANTAN');

            // Maklumat Suntikan & Vaksinasi (FMD & LSD)
            $table->date('tarikh_fmd_p1')->nullable();
            $table->date('tarikh_fmd_p2')->nullable();
            $table->date('tarikh_fmd_booster')->nullable();
            $table->date('tarikh_lsd')->nullable();
            $table->string('nama_penyuntik_1')->nullable();
            $table->string('nama_penyuntik_2')->nullable();

            // Maklumat Doktor Veterinar Swasta / Ladang (DVS/DSHR/0117/9/2021)
            $table->string('nama_doktor_veterinar')->nullable();
            $table->string('ic_doktor_veterinar')->nullable();
            $table->string('tel_doktor_veterinar')->nullable();
            $table->string('no_pendaftaran_doktor')->nullable();
            $table->string('no_mygap')->nullable();

            // Status Penyakit & Ubatan Veterinar (Bahagian A & B)
            $table->boolean('status_penyakit_ruminan_besar')->default(true);
            $table->boolean('status_penyakit_ruminan_kecil')->default(false);
            $table->string('lain_vaksin_nama')->nullable();
            $table->date('lain_vaksin_tarikh')->nullable();
            $table->date('tarikh_rawatan_terakhir')->nullable();
            $table->string('nama_ubat_terakhir')->nullable();
            $table->string('cara_rawatan_terakhir')->nullable(); // Suntikan / Minuman / Makanan / Lain-lain

            // Seksyen Khusus Untuk Kegunaan Sembelihan
            $table->string('nama_rumah_sembelih')->nullable();
            $table->text('alamat_rumah_sembelih')->nullable();
            $table->dateTime('tarikh_keluar_ladang')->nullable();
            $table->date('tarikh_sembelih')->nullable();

            // Senarai No Tag Ternakan (JSON Array up to 50 items)
            $table->json('senarai_tag')->nullable();

            // Status Permohonan & Kelulusan Pegawai
            $table->string('status')->default('Menunggu Kelulusan'); // Menunggu Kelulusan, Diluluskan, Ditolak
            $table->string('pegawai_nama')->nullable();
            $table->string('pegawai_jawatan')->nullable();
            $table->string('pegawai_jajahan')->nullable();
            $table->dateTime('tarikh_kelulusan')->nullable();
            $table->text('catatan_kelulusan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemindahan_ternakans');
    }
};
