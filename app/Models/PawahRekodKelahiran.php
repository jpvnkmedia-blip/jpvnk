<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PawahRekodKelahiran extends Model
{
    use HasFactory;

    protected $table = 'pawah_rekod_kelahiran';

    protected $fillable = [
        'pawah_perjanjian_id',
        'ternakan_induk_id',
        'no_tag_anak',
        'jantina_anak',
        'tarikh_kelahiran',
        'berat_lahir_kg',
        'baka_bapa',
        'warna',
        'status_anak',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tarikh_kelahiran' => 'date',
            'berat_lahir_kg' => 'decimal:2',
        ];
    }

    public function perjanjian()
    {
        return $this->belongsTo(PawahPerjanjian::class, 'pawah_perjanjian_id');
    }

    public function induk()
    {
        return $this->belongsTo(Ternakan::class, 'ternakan_induk_id');
    }
}
