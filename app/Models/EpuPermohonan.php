<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EpuPermohonan extends Model
{
    use HasFactory;

    protected $table = 'epu_permohonan';

    protected $fillable = [
        'epu_ladang_id',
        'no_rujukan_permohonan',
        'jenis_permohonan',
        'jenis_unggas',
        'jurusan_aktiviti',
        'bilangan_semasa_unggas',
        'kapasiti_ladang',
        'no_lesen_epu',
        'tarikh_mula_lesen',
        'tarikh_tamat_lesen',
        'yuran_lesen',
        'no_resit_bayaran',
        'status',
        'mohon_pengecualian',
        'sebab_pengecualian',
        'sebab_pengecualian_lain',
        'lampiran_pengecualian',
        'syarat_khas_lesen',
        'catatan_pegawai',
        'diluluskan_oleh',
        'tarikh_kelulusan',
        'dokumen_sokongan',
        'dokumen_pelan',
        'dokumen_tanah',
        'dokumen_pbt',
        'dokumen_ssm',
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
    ];

    protected function casts(): array
    {
        return [
            'tarikh_mula_lesen' => 'date',
            'tarikh_tamat_lesen' => 'date',
            'tarikh_kelulusan' => 'date',
            'tarikh_verifikasi' => 'date',
            'tarikh_hantar_penilaian' => 'date',
            'tarikh_rayuan' => 'date',
            'tarikh_bayaran_fi' => 'date',
            'yuran_lesen' => 'decimal:2',
            'mohon_pengecualian' => 'boolean',
        ];
    }

    public function ladang()
    {
        return $this->belongsTo(EpuLadang::class, 'epu_ladang_id');
    }

    public function pelulus()
    {
        return $this->belongsTo(User::class, 'diluluskan_oleh');
    }

    public function pegawaiVerifikasi()
    {
        return $this->belongsTo(User::class, 'pegawai_verifikasi_id');
    }

    public function pemeriksaanList()
    {
        return $this->hasMany(EpuPemeriksaan::class);
    }

    public function getTahapWorkflowAttribute(): int
    {
        if ($this->status === 'Diluluskan') {
            return ($this->status_bayaran_fi === 'Selesai Bayar' || $this->mohon_pengecualian || $this->yuran_lesen <= 0) ? 6 : 5;
        }
        if ($this->status === 'Ditolak' || $this->status_kelulusan_pelesen === 'Gagal') {
            return 5;
        }
        if ($this->status_penilaian_ladang === 'Dihantar ke Pegawai Pelesen') {
            return 4;
        }
        if ($this->pemeriksaanList()->exists() || in_array($this->status_verifikasi, ['Patuh', 'Tidak Patuh', 'Lengkap'])) {
            return 3;
        }
        if ($this->status_verifikasi === 'Tidak Lengkap') {
            return 2;
        }
        return 2; // Permohonan Dihantar
    }
}
