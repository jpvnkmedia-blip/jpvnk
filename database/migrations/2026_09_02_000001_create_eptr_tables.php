<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Pemunya Ternakan
        Schema::create('pemunya', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama');
            $table->string('no_kp', 20)->unique();
            $table->string('no_telefon', 25);
            $table->text('alamat');
            $table->string('jajahan', 50);
            $table->string('daerah', 50)->nullable();
            $table->string('poskod', 10)->nullable();
            $table->string('status', 20)->default('Aktif');
            $table->timestamps();
        });

        // 2. Ternakan (EPTR Borang A & Borang B)
        Schema::create('ternakan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemunya_id')->constrained('pemunya')->cascadeOnDelete();
            $table->string('no_tag', 50)->nullable()->unique(); // Dijana automatik semasa kelulusan admin jajahan
            $table->string('jenis_ternakan', 30); // Lembu, Kerbau, Kambing, Biri-biri
            $table->string('baka', 100); // mengikut senarai baka
            $table->string('baka_pejantan', 100)->nullable();
            $table->string('baka_induk', 100)->nullable();
            $table->string('no_tanda_pengenalan_induk', 50)->nullable();
            $table->enum('jantina', ['Jantan', 'Betina']);
            $table->string('umur', 30)->nullable(); // contoh: 2 Tahun 4 Bulan
            $table->date('tarikh_lahir')->nullable();
            $table->string('warna', 50)->nullable();
            $table->text('tanda_badan')->nullable(); // contoh: Tompok putih di dahi
            $table->string('tujuan_ternakan', 50)->default('Pedaging'); // Pedaging, Tenusu, Pembiakan
            $table->string('lokasi_kandang')->nullable();
            $table->string('jajahan', 50);
            $table->string('daerah', 50)->nullable();
            $table->string('poskod', 10)->nullable();
            
            // Kolum PROGRAM (Pilihan - mengaitkan lembu dengan bantuan / Pawah / dsb)
            $table->string('program', 100)->nullable(); // Contoh: Tiada, Program Pawah Dun, Skim Bantuan Baka Induk, Program Usahawan Muda
            
            $table->string('status', 30)->default('Menunggu'); // Menunggu, Aktif, Batal, Mati, Sembelih, Pindah, Pawah
            $table->string('status_kelulusan', 30)->default('Menunggu'); // Menunggu, Diluluskan, Ditolak
            $table->date('tarikh_daftar')->nullable();
            $table->timestamp('tarikh_kelulusan')->nullable();
            $table->foreignId('diluluskan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->string('no_siri_kad_kuning', 50)->nullable();
            $table->string('qr_code')->nullable();
            $table->string('gambar_ternakan')->nullable();
            $table->string('resit_pembayaran')->nullable(); // Lampiran fail resit pembayaran
            $table->text('catatan')->nullable();
            $table->foreignId('didaftar_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 3. Pindah Milik Ternakan
        Schema::create('pindah_milik', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ternakan_id')->constrained('ternakan')->cascadeOnDelete();
            $table->foreignId('pemunya_asal_id')->constrained('pemunya')->cascadeOnDelete();
            $table->foreignId('pemunya_baru_id')->constrained('pemunya')->cascadeOnDelete();
            $table->date('tarikh_pindah');
            $table->text('sebab_pindah')->nullable();
            $table->decimal('harga_jualan', 10, 2)->nullable();
            $table->string('status_kelulusan', 30)->default('Menunggu'); // Menunggu, Diluluskan, Ditolak
            $table->foreignId('diluluskan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 4. Pembatalan / Kematian / Pindah Keluar (EPTR Borang C)
        Schema::create('pembatalan_ternakan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ternakan_id')->constrained('ternakan')->cascadeOnDelete();
            $table->enum('jenis_batal', ['Mati', 'Pindah Keluar', 'Kecurian', 'Pelupusan', 'Lain-lain']);
            $table->date('tarikh_peristiwa');
            $table->text('sebab');
            $table->string('no_laporan_polis', 50)->nullable();
            $table->string('dokumen_sokongan')->nullable();
            $table->string('destinasi_pindah_keluar')->nullable(); // Jika pindah keluar negeri
            $table->string('status_kelulusan', 30)->default('Menunggu'); // Menunggu, Disahkan, Ditolak
            $table->foreignId('disahkan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 5. Permit Sembelihan Ternakan Ruminan (EPTR Borang D)
        Schema::create('permit_sembelihan', function (Blueprint $table) {
            $table->id();
            $table->string('no_permit', 50)->unique();
            $table->foreignId('pemunya_id')->constrained('pemunya')->cascadeOnDelete();
            $table->foreignId('ternakan_id')->constrained('ternakan')->cascadeOnDelete();
            $table->string('tujuan_sembelih', 100); // Korban, Aqiqah, Kenduri, Jualan Pasar, Sembelihan Kecemasan
            $table->date('tarikh_sembelih');
            $table->string('lokasi_sembelih')->nullable(); // Rumah Sembelihan / Alamat Premis
            $table->string('nama_premis_sembelih', 150)->nullable();
            $table->text('alamat_premis_sembelih')->nullable();
            $table->string('no_resit_bayaran', 50)->nullable();
            $table->decimal('kadar_bayaran', 8, 2)->default(10.00);
            $table->string('status_kelulusan', 30)->default('Menunggu'); // Menunggu, Diluluskan, Ditolak, Selesai
            $table->foreignId('diluluskan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 6. Rekod Kelahiran Anak Ternakan (Proses Daftar Anak Ternakan)
        Schema::create('rekod_kelahiran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('induk_id')->constrained('ternakan')->cascadeOnDelete();
            $table->foreignId('pejantan_id')->nullable()->constrained('ternakan')->nullOnDelete();
            $table->foreignId('anak_ternakan_id')->nullable()->constrained('ternakan')->nullOnDelete();
            $table->foreignId('pemunya_id')->constrained('pemunya')->cascadeOnDelete();
            $table->string('no_tag_sementara', 50)->nullable();
            $table->enum('jantina_anak', ['Jantan', 'Betina']);
            $table->date('tarikh_kelahiran');
            $table->decimal('berat_lahir_kg', 5, 2)->nullable();
            $table->string('baka_anak', 100);
            $table->string('warna_anak', 100)->nullable();
            $table->text('tanda_badan_anak')->nullable();
            $table->enum('status_kelahiran', ['Hidup', 'Mati Semasa Lahir', 'Gugur'])->default('Hidup');
            $table->enum('keadaan_anak', ['Cergas', 'Sederhana', 'Lemah'])->default('Cergas');
            $table->string('gambar_anak')->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('didaftar_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 7. Program Kesihatan Ternakan (Vaksinasi, Penyahcacingan, Rawatan, Surveilans, Pemeriksaan)
        Schema::create('program_kesihatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ternakan_id')->constrained('ternakan')->cascadeOnDelete();
            $table->string('no_rujukan_kesihatan', 50)->unique();
            $table->string('jenis_program', 100); // Vaksinasi, Penyahcacingan, Rawatan Penyakit, Surveilans Penyakit, Pemberian Vitamin, Kawalan Parasit, Pemeriksaan Rutin
            $table->string('nama_vaksin_atau_ubat', 150); // Contoh: Vaksin FMD (Aftovax), Ivermectin, Oxytetracycline LA, Vitamin B-Complex
            $table->date('tarikh_rawatan');
            $table->date('tarikh_ulangan_dos')->nullable(); // Tarikh booster / next deworming
            $table->decimal('berat_semasa_kg', 6, 2)->nullable();
            $table->decimal('suhu_badan_celsius', 4, 1)->nullable();
            $table->string('status_kesihatan', 50)->default('Sihat & Cergas'); // Sihat & Cergas, Stabil, Dalam Pemantauan, Kritikal, Sembuh
            $table->text('diagnosis_atau_tujuan')->nullable();
            $table->text('tindakan_rawatan')->nullable();
            $table->string('dos_diberikan', 100)->nullable(); // contoh: 2ml Subkutan, 10ml Oral
            $table->string('pegawai_pemeriksa', 150);
            $table->string('jajahan', 50);
            $table->string('lokasi_pemeriksaan')->nullable(); // Premis Penternak / Klinik Jajahan
            $table->text('catatan_dan_syor')->nullable();
            $table->string('dokumen_lampiran')->nullable();
            $table->foreignId('didaftar_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_kesihatan');
        Schema::dropIfExists('rekod_kelahiran');
        Schema::dropIfExists('permit_sembelihan');
        Schema::dropIfExists('pembatalan_ternakan');
        Schema::dropIfExists('pindah_milik');
        Schema::dropIfExists('ternakan');
        Schema::dropIfExists('pemunya');
    }
};
