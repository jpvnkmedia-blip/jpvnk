<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PawahRekodKesihatan extends Model
{
    use HasFactory;

    protected $table = 'pawah_rekod_kesihatan';

    protected $fillable = [
        'pawah_perjanjian_id',
        'ternakan_id',
        'tarikh_lawatan',
        'status_fizikal',
        'status_bunting',
        'diagnosis',
        'rawatan_diberikan',
        'pegawai_pemeriksa',
        'syor_tindakan',
    ];

    protected function casts(): array
    {
        return [
            'tarikh_lawatan' => 'date',
            'status_bunting' => 'boolean',
        ];
    }

    public function perjanjian()
    {
        return $this->belongsTo(PawahPerjanjian::class, 'pawah_perjanjian_id');
    }

    public function ternakan()
    {
        return $this->belongsTo(Ternakan::class);
    }
}
