<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemandu extends Model
{
    use HasFactory;

    protected $table = 'pemandu';

    protected $fillable = [
        'nama',
        'no_kp',
        'no_pekerja',
        'no_telefon',
        'kelas_lesen',
        'tarikh_tamat_lesen',
        'jajahan_penempatan',
        'status',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tarikh_tamat_lesen' => 'date',
        ];
    }
}
