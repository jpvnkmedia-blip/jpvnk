<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KenderaanTempahan extends Model
{
    use HasFactory;

    protected $table = 'kenderaan_tempahan';

    protected $fillable = [
        'user_id',
        'kenderaan_id',
        'no_tempahan',
        'tujuan_perjalanan',
        'destinasi',
        'tarikh_mula',
        'masa_mula',
        'tarikh_tamat',
        'masa_tamat',
        'bilangan_penumpang',
        'senarai_nama_penumpang',
        'pemandu_nama',
        'odometer_keluar',
        'odometer_masuk',
        'status',
        'catatan_kelulusan',
        'diluluskan_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tarikh_mula' => 'date',
            'tarikh_tamat' => 'date',
        ];
    }

    public function pemohon()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kenderaan()
    {
        return $this->belongsTo(Kenderaan::class, 'kenderaan_id');
    }

    public function pelulus()
    {
        return $this->belongsTo(User::class, 'diluluskan_oleh');
    }
}
