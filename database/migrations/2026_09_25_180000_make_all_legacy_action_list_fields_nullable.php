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
            $table->string('nama_pelanggan')->nullable()->default(null)->change();
            $table->string('kategori_pelanggan')->nullable()->default('Individu')->change();
            $table->string('kod_dokumen')->nullable()->default('DAIRI-AKTIVITI')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
