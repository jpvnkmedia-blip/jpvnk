<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EpuPemeriksaan extends Model
{
    use HasFactory;

    protected $table = 'epu_pemeriksaan';

    protected $fillable = [
        'epu_ladang_id',
        'epu_permohonan_id',
        'pegawai_id',
        'tarikh_pemeriksaan',
        'skor_kebersihan_peratus',
        'patuh_zon_penampan',
        'kawalan_lalat_memuaskan',
        'kawalan_bau_memuaskan',
        'sistem_longkang_sempurna',
        'penemuan_pemeriksaan',
        'syor_dan_arahan',
        'status_keputusan',
        'no_notis_pematuhan',
        'tarikh_akhir_pematuhan',
        'gambar_pemeriksaan',
    ];

    protected function casts(): array
    {
        return [
            'tarikh_pemeriksaan' => 'date',
            'tarikh_akhir_pematuhan' => 'date',
            'patuh_zon_penampan' => 'boolean',
            'kawalan_lalat_memuaskan' => 'boolean',
            'kawalan_bau_memuaskan' => 'boolean',
            'sistem_longkang_sempurna' => 'boolean',
        ];
    }

    public function ladang()
    {
        return $this->belongsTo(EpuLadang::class, 'epu_ladang_id');
    }

    public function permohonan()
    {
        return $this->belongsTo(EpuPermohonan::class, 'epu_permohonan_id');
    }

    public function pegawai()
    {
        return $this->belongsTo(User::class, 'pegawai_id');
    }
}
