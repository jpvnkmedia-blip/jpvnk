<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PindahMilik extends Model
{
    use HasFactory;

    protected $table = 'pindah_milik';

    protected $fillable = [
        'ternakan_id',
        'pemunya_asal_id',
        'pemunya_baru_id',
        'tarikh_pindah',
        'sebab_pindah',
        'harga_jualan',
        'status_kelulusan',
        'diluluskan_oleh',
        'catatan',
        'resit_pembayaran',
    ];

    protected function casts(): array
    {
        return [
            'tarikh_pindah' => 'date',
            'harga_jualan' => 'decimal:2',
        ];
    }

    public function ternakan()
    {
        return $this->belongsTo(Ternakan::class);
    }

    public function pemunyaAsal()
    {
        return $this->belongsTo(Pemunya::class, 'pemunya_asal_id');
    }

    public function pemunyaBaru()
    {
        return $this->belongsTo(Pemunya::class, 'pemunya_baru_id');
    }

    public function pelulus()
    {
        return $this->belongsTo(User::class, 'diluluskan_oleh');
    }
}
