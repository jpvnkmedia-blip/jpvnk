<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Senarai Item / Aset / Bekalan Inventori (Stor Pejabat & Stor Ubat Veterinar)
        Schema::create('inventori_items', function (Blueprint $table) {
            $table->id();
            $table->string('kod_item', 50)->unique();
            $table->string('nama_item');
            $table->string('jenis_stor', 30)->default('pejabat'); // 'pejabat' atau 'ubat'
            $table->string('kategori', 80); // Alat Tulis & Pejabat, Aset IT, Ubat-ubatan, Vaksin, Tag Telinga EPTR, dsb.
            $table->string('unit', 30)->default('Unit'); // Botol, Kotak, Unit, Keping, Pek, Set, Vial, Tiub
            $table->integer('kuantiti_semasa')->default(0);
            $table->integer('kuantiti_minimum')->default(10);
            $table->decimal('harga_seunit', 10, 2)->default(0.00);
            $table->string('no_batch', 60)->nullable(); // Khusus Ubat / Vaksin
            $table->date('tarikh_luput')->nullable(); // Khusus Ubat / Vaksin / Reagen
            $table->string('suhu_simpanan', 100)->nullable(); // Chiller 2°C - 8°C, Suhu Bilik < 25°C, dsb.
            $table->string('pembekal_utama')->nullable();
            $table->string('lokasi_rak')->nullable();
            $table->string('jajahan', 50)->default('Ibu Pejabat Kota Bharu');
            $table->string('status', 30)->default('Mencukupi'); // Mencukupi, Stok Rendah, Habis Stok, Dilupuskan, Luput
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // 2. Transaksi Keluar Masuk Inventori (Stok Masuk / Stok Keluar)
        Schema::create('inventori_transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventori_item_id')->constrained('inventori_items')->cascadeOnDelete();
            $table->enum('jenis_transaksi', ['Stok Masuk', 'Stok Keluar', 'Pelupusan', 'Penyelarasan Kiraan']);
            $table->integer('kuantiti');
            $table->string('penerima_atau_pembekal'); // Pembekal / Nama Pegawai Jajahan Penerima
            $table->string('rujukan_dokumen', 100)->nullable(); // No DO / No Baucar / No Pesanan
            $table->integer('baki_selepas');
            $table->foreignId('dikendalikan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 3. Rekod Pinjaman Peralatan Inventori
        Schema::create('inventori_pinjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventori_item_id')->constrained('inventori_items')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Peminjam
            $table->integer('kuantiti')->default(1);
            $table->text('tujuan_pinjaman');
            $table->date('tarikh_pinjam');
            $table->date('tarikh_jangka_pulang');
            $table->date('tarikh_pulang_sebenar')->nullable();
            $table->string('keadaan_semasa_pinjam')->default('Baik / Berfungsi');
            $table->string('keadaan_semasa_pulang')->nullable();
            $table->string('status', 30)->default('Dipinjam'); // Dipinjam, Telah Dipulangkan, Lewat Pulang, Rosak / Hilang
            $table->foreignId('disahkan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 4. Permohonan Alatan Tulis & Ubat oleh Staf Jabatan
        Schema::create('inventori_permohonan', function (Blueprint $table) {
            $table->id();
            $table->string('no_permohonan', 50)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Staf Pemohon
            $table->foreignId('inventori_item_id')->constrained('inventori_items')->cascadeOnDelete(); // Item Dimohon
            $table->string('jenis_stor', 30); // 'pejabat' atau 'ubat'
            $table->integer('kuantiti_dimohon');
            $table->integer('kuantiti_diluluskan')->nullable();
            $table->string('unit_bahagian')->nullable(); // Bahagian / Unit / Jajahan Staf
            $table->text('tujuan_permohonan');
            $table->date('tarikh_diperlukan')->nullable();
            $table->string('status', 40)->default('Menunggu Kelulusan'); // Menunggu Kelulusan, Diluluskan, Ditolak, Telah Diambil / Diserahkan
            $table->text('catatan_pemohon')->nullable();
            $table->text('catatan_pegawai')->nullable();
            $table->foreignId('disahkan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('tarikh_kelulusan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventori_permohonan');
        Schema::dropIfExists('inventori_pinjaman');
        Schema::dropIfExists('inventori_transaksi');
        Schema::dropIfExists('inventori_items');
    }
};
