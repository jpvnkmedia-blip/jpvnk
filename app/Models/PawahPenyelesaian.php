<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PawahPenyelesaian extends Model
{
    use HasFactory;

    protected $table = 'pawah_penyelesaian';

    protected $fillable = [
        'pawah_perjanjian_id',
        'tarikh_penyelesaian',
        'bilangan_anak_dipulangkan',
        'status_penyelesaian',
        'jumlah_bayaran_tebus_guna',
        'resit_pembayaran',
        'pegawai_pengesah',
        'perakuan',
        'sijil_penyelesaian',
    ];

    protected function casts(): array
    {
        return [
            'tarikh_penyelesaian' => 'date',
            'jumlah_bayaran_tebus_guna' => 'decimal:2',
        ];
    }

    public function perjanjian()
    {
        return $this->belongsTo(PawahPerjanjian::class, 'pawah_perjanjian_id');
    }
}
