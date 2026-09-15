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
        Schema::table('permit_sembelihan', function (Blueprint $table) {
            $table->string('resit_pembayaran')->nullable()->after('no_resit_bayaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permit_sembelihan', function (Blueprint $table) {
            $table->dropColumn('resit_pembayaran');
        });
    }
};
