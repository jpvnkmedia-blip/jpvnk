<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KlinikTemujanji extends Model
{
    use HasFactory;

    protected $table = 'klinik_temujanji';

    protected $fillable = [
        'user_id',
        'no_temujanji',
        'jenis_haiwan',
        'nama_haiwan',
        'baka',
        'jantina_haiwan',
        'umur_haiwan',
        'simptom_atau_tujuan',
        'tarikh_temujanji',
        'sesi',
        'klinik_jajahan',
        'status',
        'catatan_pegawai',
    ];

    protected function casts(): array
    {
        return [
            'tarikh_temujanji' => 'date',
        ];
    }

    public function pemilik()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rawatan()
    {
        return $this->hasOne(KlinikRawatan::class);
    }
}
