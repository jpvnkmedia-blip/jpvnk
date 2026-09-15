<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add fields to users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nama_syarikat')) {
                $table->string('nama_syarikat')->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'no_ssm')) {
                $table->string('no_ssm', 50)->nullable()->after('nama_syarikat');
            }
            if (!Schema::hasColumn('users', 'bentuk_perniagaan')) {
                $table->string('bentuk_perniagaan', 50)->nullable()->after('no_ssm');
            }
            if (!Schema::hasColumn('users', 'poskod')) {
                $table->string('poskod', 10)->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'negeri')) {
                $table->string('negeri', 50)->default('Kelantan')->after('poskod');
            }
            if (!Schema::hasColumn('users', 'fax')) {
                $table->string('fax', 25)->nullable()->after('phone');
            }
        });

        // 2. Add fields to epu_ladang
        Schema::table('epu_ladang', function (Blueprint $table) {
            if (!Schema::hasColumn('epu_ladang', 'id_premis')) {
                $table->string('id_premis', 50)->nullable()->after('nama_ladang');
            }
            if (!Schema::hasColumn('epu_ladang', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('alamat_ladang');
            }
            if (!Schema::hasColumn('epu_ladang', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('epu_ladang', 'poskod')) {
                $table->string('poskod', 10)->nullable()->after('alamat_ladang');
            }
            if (!Schema::hasColumn('epu_ladang', 'negeri')) {
                $table->string('negeri', 50)->default('Kelantan')->after('poskod');
            }
            if (!Schema::hasColumn('epu_ladang', 'daerah')) {
                $table->string('daerah', 50)->nullable()->after('jajahan');
            }
            if (!Schema::hasColumn('epu_ladang', 'luas_kawasan_sqft')) {
                $table->decimal('luas_kawasan_sqft', 12, 2)->nullable()->after('luas_tanah_ekar');
            }
            if (!Schema::hasColumn('epu_ladang', 'reban_data')) {
                $table->json('reban_data')->nullable()->after('sistem_reban');
            }
            if (!Schema::hasColumn('epu_ladang', 'alamat_premis_perniagaan')) {
                $table->text('alamat_premis_perniagaan')->nullable()->after('reban_data');
            }
            if (!Schema::hasColumn('epu_ladang', 'poskod_premis_perniagaan')) {
                $table->string('poskod_premis_perniagaan', 10)->nullable()->after('alamat_premis_perniagaan');
            }
            if (!Schema::hasColumn('epu_ladang', 'negeri_premis_perniagaan')) {
                $table->string('negeri_premis_perniagaan', 50)->nullable()->after('poskod_premis_perniagaan');
            }
        });

        // 3. Add fields to epu_permohonan
        Schema::table('epu_permohonan', function (Blueprint $table) {
            if (!Schema::hasColumn('epu_permohonan', 'jurusan_aktiviti')) {
                $table->string('jurusan_aktiviti', 50)->nullable()->after('jenis_unggas');
            }
            if (!Schema::hasColumn('epu_permohonan', 'kapasiti_ladang')) {
                $table->integer('kapasiti_ladang')->nullable()->after('bilangan_semasa_unggas');
            }
            if (!Schema::hasColumn('epu_permohonan', 'mohon_pengecualian')) {
                $table->boolean('mohon_pengecualian')->default(false)->after('status');
            }
            if (!Schema::hasColumn('epu_permohonan', 'sebab_pengecualian')) {
                $table->string('sebab_pengecualian')->nullable()->after('mohon_pengecualian');
            }
            if (!Schema::hasColumn('epu_permohonan', 'sebab_pengecualian_lain')) {
                $table->string('sebab_pengecualian_lain')->nullable()->after('sebab_pengecualian');
            }
            if (!Schema::hasColumn('epu_permohonan', 'lampiran_pengecualian')) {
                $table->string('lampiran_pengecualian')->nullable()->after('sebab_pengecualian_lain');
            }
            if (!Schema::hasColumn('epu_permohonan', 'dokumen_pelan')) {
                $table->string('dokumen_pelan')->nullable()->after('dokumen_sokongan');
            }
            if (!Schema::hasColumn('epu_permohonan', 'dokumen_tanah')) {
                $table->string('dokumen_tanah')->nullable()->after('dokumen_pelan');
            }
            if (!Schema::hasColumn('epu_permohonan', 'dokumen_pbt')) {
                $table->string('dokumen_pbt')->nullable()->after('dokumen_tanah');
            }
            if (!Schema::hasColumn('epu_permohonan', 'dokumen_ssm')) {
                $table->string('dokumen_ssm')->nullable()->after('dokumen_pbt');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nama_syarikat', 'no_ssm', 'bentuk_perniagaan', 'poskod', 'negeri', 'fax']);
        });

        Schema::table('epu_ladang', function (Blueprint $table) {
            $table->dropColumn([
                'id_premis', 'latitude', 'longitude', 'poskod', 'negeri', 'daerah',
                'luas_kawasan_sqft', 'reban_data', 'alamat_premis_perniagaan',
                'poskod_premis_perniagaan', 'negeri_premis_perniagaan'
            ]);
        });

        Schema::table('epu_permohonan', function (Blueprint $table) {
            $table->dropColumn([
                'jurusan_aktiviti', 'kapasiti_ladang', 'mohon_pengecualian',
                'sebab_pengecualian', 'sebab_pengecualian_lain', 'lampiran_pengecualian',
                'dokumen_pelan', 'dokumen_tanah', 'dokumen_pbt', 'dokumen_ssm'
            ]);
        });
    }
};
