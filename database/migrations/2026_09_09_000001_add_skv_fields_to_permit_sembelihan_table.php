<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permit_sembelihan', function (Blueprint $table) {
            $table->unsignedBigInteger('ternakan_id')->nullable()->change();
            $table->string('no_rujukan_skv', 100)->nullable()->after('no_permit');
            $table->string('no_rujukan_karkas', 100)->nullable()->after('no_rujukan_skv');
            $table->string('jenis_ternakan', 50)->default('Lembu')->after('pemunya_id');
            $table->boolean('is_musim_korban')->default(false)->after('tujuan_sembelih');
            $table->date('tarikh_mula')->nullable()->after('tarikh_sembelih');
            $table->date('tarikh_tamat')->nullable()->after('tarikh_mula');
            $table->string('no_kenderaan', 30)->nullable()->after('tarikh_tamat');
            $table->text('alamat_1')->nullable()->after('alamat_premis_sembelih');
            $table->string('kuantiti_karkas_1', 100)->nullable()->after('alamat_1');
            $table->text('alamat_2')->nullable()->after('kuantiti_karkas_1');
            $table->string('kuantiti_karkas_2', 100)->nullable()->after('alamat_2');
            $table->text('alamat_3')->nullable()->after('kuantiti_karkas_2');
            $table->string('kuantiti_karkas_3', 100)->nullable()->after('alamat_3');
            $table->json('senarai_ternakan')->nullable()->after('kuantiti_karkas_3');
        });
    }

    public function down(): void
    {
        Schema::table('permit_sembelihan', function (Blueprint $table) {
            $table->dropColumn([
                'no_rujukan_skv',
                'no_rujukan_karkas',
                'jenis_ternakan',
                'is_musim_korban',
                'tarikh_mula',
                'tarikh_tamat',
                'no_kenderaan',
                'alamat_1',
                'kuantiti_karkas_1',
                'alamat_2',
                'kuantiti_karkas_2',
                'alamat_3',
                'kuantiti_karkas_3',
                'senarai_ternakan',
            ]);
        });
    }
};
