<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('epu_permohonan', function (Blueprint $table) {
            if (!Schema::hasColumn('epu_permohonan', 'status_verifikasi')) {
                $table->string('status_verifikasi', 50)->default('Menunggu Verifikasi')->after('status');
            }
            if (!Schema::hasColumn('epu_permohonan', 'pegawai_verifikasi_id')) {
                $table->foreignId('pegawai_verifikasi_id')->nullable()->after('status_verifikasi')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('epu_permohonan', 'tarikh_verifikasi')) {
                $table->date('tarikh_verifikasi')->nullable()->after('pegawai_verifikasi_id');
            }
            if (!Schema::hasColumn('epu_permohonan', 'catatan_verifikasi')) {
                $table->text('catatan_verifikasi')->nullable()->after('tarikh_verifikasi');
            }
            if (!Schema::hasColumn('epu_permohonan', 'tindakan_penambahbaikan')) {
                $table->text('tindakan_penambahbaikan')->nullable()->after('catatan_verifikasi');
            }
            if (!Schema::hasColumn('epu_permohonan', 'status_penilaian_ladang')) {
                $table->string('status_penilaian_ladang', 50)->default('Belum Dihantar')->after('tindakan_penambahbaikan');
            }
            if (!Schema::hasColumn('epu_permohonan', 'tarikh_hantar_penilaian')) {
                $table->date('tarikh_hantar_penilaian')->nullable()->after('status_penilaian_ladang');
            }
            if (!Schema::hasColumn('epu_permohonan', 'catatan_penilaian_ladang')) {
                $table->text('catatan_penilaian_ladang')->nullable()->after('tarikh_hantar_penilaian');
            }
            if (!Schema::hasColumn('epu_permohonan', 'status_kelulusan_pelesen')) {
                $table->string('status_kelulusan_pelesen', 50)->nullable()->after('catatan_penilaian_ladang');
            }
            if (!Schema::hasColumn('epu_permohonan', 'status_rayuan')) {
                $table->string('status_rayuan', 50)->default('Tiada')->after('status_kelulusan_pelesen');
            }
            if (!Schema::hasColumn('epu_permohonan', 'alasan_rayuan')) {
                $table->text('alasan_rayuan')->nullable()->after('status_rayuan');
            }
            if (!Schema::hasColumn('epu_permohonan', 'dokumen_rayuan')) {
                $table->string('dokumen_rayuan')->nullable()->after('alasan_rayuan');
            }
            if (!Schema::hasColumn('epu_permohonan', 'tarikh_rayuan')) {
                $table->date('tarikh_rayuan')->nullable()->after('dokumen_rayuan');
            }
            if (!Schema::hasColumn('epu_permohonan', 'catatan_keputusan_rayuan')) {
                $table->text('catatan_keputusan_rayuan')->nullable()->after('tarikh_rayuan');
            }
            if (!Schema::hasColumn('epu_permohonan', 'status_bayaran_fi')) {
                $table->string('status_bayaran_fi', 50)->default('Belum Bayar')->after('catatan_keputusan_rayuan');
            }
            if (!Schema::hasColumn('epu_permohonan', 'resit_bayaran_fi')) {
                $table->string('resit_bayaran_fi')->nullable()->after('status_bayaran_fi');
            }
            if (!Schema::hasColumn('epu_permohonan', 'tarikh_bayaran_fi')) {
                $table->date('tarikh_bayaran_fi')->nullable()->after('resit_bayaran_fi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('epu_permohonan', function (Blueprint $table) {
            $table->dropForeign(['pegawai_verifikasi_id']);
            $table->dropColumn([
                'status_verifikasi',
                'pegawai_verifikasi_id',
                'tarikh_verifikasi',
                'catatan_verifikasi',
                'tindakan_penambahbaikan',
                'status_penilaian_ladang',
                'tarikh_hantar_penilaian',
                'catatan_penilaian_ladang',
                'status_kelulusan_pelesen',
                'status_rayuan',
                'alasan_rayuan',
                'dokumen_rayuan',
                'tarikh_rayuan',
                'catatan_keputusan_rayuan',
                'status_bayaran_fi',
                'resit_bayaran_fi',
                'tarikh_bayaran_fi',
            ]);
        });
    }
};
