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
        Schema::table('action_lists', function (Blueprint $table) {
            if (!Schema::hasColumn('action_lists', 'tajuk_aktiviti')) {
                $table->string('tajuk_aktiviti')->nullable()->after('tarikh');
            }
            if (!Schema::hasColumn('action_lists', 'kategori_aktiviti')) {
                $table->string('kategori_aktiviti')->default('Lawatan Lapangan')->after('tajuk_aktiviti');
            }
            if (!Schema::hasColumn('action_lists', 'maklumat_aktiviti')) {
                $table->longText('maklumat_aktiviti')->nullable()->after('kategori_aktiviti');
            }
            if (!Schema::hasColumn('action_lists', 'masa_mula')) {
                $table->string('masa_mula')->nullable()->after('tarikh');
            }
            if (!Schema::hasColumn('action_lists', 'masa_selesai')) {
                $table->string('masa_selesai')->nullable()->after('masa_mula');
            }
            if (!Schema::hasColumn('action_lists', 'lokasi')) {
                $table->string('lokasi')->nullable()->after('maklumat_aktiviti');
            }
            if (!Schema::hasColumn('action_lists', 'keutamaan')) {
                $table->string('keutamaan')->default('Biasa')->after('status');
            }
            if (!Schema::hasColumn('action_lists', 'tindakan_susulan')) {
                $table->text('tindakan_susulan')->nullable()->after('keutamaan');
            }
            if (!Schema::hasColumn('action_lists', 'lampiran')) {
                $table->string('lampiran')->nullable()->after('tindakan_susulan');
            }
            if (!Schema::hasColumn('action_lists', 'nama_pegawai')) {
                $table->string('nama_pegawai')->nullable()->after('lokasi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('action_lists', function (Blueprint $table) {
            $table->dropColumn([
                'tajuk_aktiviti',
                'kategori_aktiviti',
                'maklumat_aktiviti',
                'masa_mula',
                'masa_selesai',
                'lokasi',
                'keutamaan',
                'tindakan_susulan',
                'lampiran'
            ]);
        });
    }
};
