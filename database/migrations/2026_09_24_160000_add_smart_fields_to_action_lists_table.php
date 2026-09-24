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
            $table->string('gps_koordinat')->nullable()->after('lampiran_peta');
            $table->foreignId('pawah_perjanjian_id')->nullable()->after('temujanji_id')->constrained('pawah_perjanjian')->nullOnDelete();
            $table->json('ternakan_terlibat_ids')->nullable()->after('jenis_ternakan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('action_lists', function (Blueprint $table) {
            $table->dropForeign(['pawah_perjanjian_id']);
            $table->dropColumn(['gps_koordinat', 'pawah_perjanjian_id', 'ternakan_terlibat_ids']);
        });
    }
};
