<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EpuLadang extends Model
{
    use HasFactory;

    protected $table = 'epu_ladang';

    protected $fillable = [
        'user_id',
        'nama_pemohon_atau_syarikat',
        'no_syarikat_atau_ssm',
        'nama_ladang',
        'id_premis',
        'no_geran_tanah',
        'no_lot',
        'luas_tanah_ekar',
        'luas_kawasan_sqft',
        'jajahan',
        'daerah',
        'mukim',
        'alamat_ladang',
        'poskod',
        'negeri',
        'latitude',
        'longitude',
        'status_pemilikan_tanah',
        'sistem_reban',
        'reban_data',
        'alamat_premis_perniagaan',
        'poskod_premis_perniagaan',
        'negeri_premis_perniagaan',
        'kapasiti_maksimum_unggas',
        'jarak_kediaman_terdekat_meter',
        'jarak_sungai_terdekat_meter',
        'kaedah_kawalan_lalat_bau',
        'kaedah_pelupusan_tinja',
        'kaedah_pelupusan_bangkai',
        'status_ladang',
    ];

    protected function casts(): array
    {
        return [
            'reban_data' => 'array',
            'latitude' => 'float',
            'longitude' => 'float',
            'luas_kawasan_sqft' => 'decimal:2',
            'luas_tanah_ekar' => 'decimal:2',
        ];
    }

    public function pemilik()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function permohonanList()
    {
        return $this->hasMany(EpuPermohonan::class);
    }

    public function permohonanTerkini()
    {
        return $this->hasOne(EpuPermohonan::class)->latestOfMany();
    }

    public function pemeriksaanList()
    {
        return $this->hasMany(EpuPemeriksaan::class);
    }
}
