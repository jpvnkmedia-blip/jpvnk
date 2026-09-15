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
        Schema::table('pemindahan_ternakans', function (Blueprint $table) {
            if (!Schema::hasColumn('pemindahan_ternakans', 'pegawai_jajahan')) {
                $table->string('pegawai_jajahan')->nullable()->after('pegawai_jawatan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemindahan_ternakans', function (Blueprint $table) {
            if (Schema::hasColumn('pemindahan_ternakans', 'pegawai_jajahan')) {
                $table->dropColumn('pegawai_jajahan');
            }
        });
    }
};
