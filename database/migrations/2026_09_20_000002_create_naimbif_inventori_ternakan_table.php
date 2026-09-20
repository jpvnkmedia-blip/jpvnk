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
        Schema::create('naimbif_inventori_ternakan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('naimbif_permohonan_id')->constrained('naimbif_permohonan')->cascadeOnDelete();
            $table->string('baka'); // CHAROLAIS, BELGIAN BLUE, BLONDE D'AQUITAINE, LIMOUSIN, KEDAH KELANTAN, LAIN-LAIN
            $table->string('nama_baka_lain')->nullable();
            
            // BETINA
            $table->unsignedInteger('betina_anak')->default(0);
            $table->unsignedInteger('betina_dara')->default(0);
            $table->unsignedInteger('betina_induk')->default(0);

            // JANTAN
            $table->unsignedInteger('jantan_anak')->default(0);
            $table->unsignedInteger('jantan_pejantan')->default(0);

            // JUMLAH
            $table->unsignedInteger('jumlah_baka')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('naimbif_inventori_ternakan');
    }
};
