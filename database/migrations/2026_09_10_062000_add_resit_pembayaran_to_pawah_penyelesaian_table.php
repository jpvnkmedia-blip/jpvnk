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
        if (Schema::hasTable('pawah_penyelesaian') && !Schema::hasColumn('pawah_penyelesaian', 'resit_pembayaran')) {
            Schema::table('pawah_penyelesaian', function (Blueprint $table) {
                $table->string('resit_pembayaran')->nullable()->after('jumlah_bayaran_tebus_guna');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pawah_penyelesaian') && Schema::hasColumn('pawah_penyelesaian', 'resit_pembayaran')) {
            Schema::table('pawah_penyelesaian', function (Blueprint $table) {
                $table->dropColumn('resit_pembayaran');
            });
        }
    }
};