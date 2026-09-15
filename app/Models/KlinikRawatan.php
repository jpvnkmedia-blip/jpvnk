<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KlinikRawatan extends Model
{
    use HasFactory;

    protected $table = 'klinik_rawatan';

    protected $fillable = [
        'klinik_temujanji_id',
        'user_id',
        'no_rekod_rawatan',
        'tarikh_rawatan',
        'pegawai_veterinar',
        'berat_badan_kg',
        'suhu_celsius',
        'diagnosis',
        'rawatan_diberikan',
        'ubat_diberikan',
        'vaksinasi',
        'tarikh_temujanji_susulan',
        'kos_rawatan',
        'status_bayaran',
        'nasihat_veterinar',
    ];

    protected function casts(): array
    {
        return [
            'tarikh_rawatan' => 'date',
            'tarikh_temujanji_susulan' => 'date',
            'berat_badan_kg' => 'decimal:2',
            'suhu_celsius' => 'decimal:1',
            'kos_rawatan' => 'decimal:2',
        ];
    }

    public function temujanji()
    {
        return $this->belongsTo(KlinikTemujanji::class, 'klinik_temujanji_id');
    }

    public function pemilik()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
