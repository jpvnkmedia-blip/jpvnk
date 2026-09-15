<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kenderaan extends Model
{
    use HasFactory;

    protected $table = 'kenderaan';

    protected $fillable = [
        'no_pendaftaran',
        'jenis_kenderaan',
        'model',
        'tahun_buatan',
        'kapasiti_penumpang',
        'jajahan_penempatan',
        'status',
        'lokasi_kunci',
        'odometer_semasa_km',
        'tarikh_tamat_cukai_jalan',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tarikh_tamat_cukai_jalan' => 'date',
        ];
    }

    public function tempahan()
    {
        return $this->hasMany(KenderaanTempahan::class);
    }
}
