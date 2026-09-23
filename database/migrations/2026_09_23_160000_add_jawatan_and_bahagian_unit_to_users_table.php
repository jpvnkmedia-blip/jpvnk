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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'jawatan')) {
                $table->string('jawatan', 150)->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'bahagian_unit')) {
                $table->string('bahagian_unit', 200)->nullable()->after('jawatan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'jawatan')) {
                $table->dropColumn('jawatan');
            }
            if (Schema::hasColumn('users', 'bahagian_unit')) {
                $table->dropColumn('bahagian_unit');
            }
        });
    }
};
