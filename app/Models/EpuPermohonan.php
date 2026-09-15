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
    ];

    protected function casts(): array
    {
        return [
            'tarikh_mula_lesen' => 'date',
            'tarikh_tamat_lesen' => 'date',
            'tarikh_kelulusan' => 'date',
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

    public function pemeriksaanList()
    {
        return $this->hasMany(EpuPemeriksaan::class);
    }
}
