<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PawahTernakan extends Model
{
    use HasFactory;

    protected $table = 'pawah_ternakan';

    protected $fillable = [
        'pawah_perjanjian_id',
        'ternakan_id',
        'status_induk',
        'tarikh_serahan',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tarikh_serahan' => 'date',
        ];
    }

    public function perjanjian()
    {
        return $this->belongsTo(PawahPerjanjian::class, 'pawah_perjanjian_id');
    }

    public function ternakan()
    {
        return $this->belongsTo(Ternakan::class, 'ternakan_id');
    }
}
